<?php
/*
 * Title   : Stripe Payment extension for Woo-Commerece
 * Author  : DenonStudio
 * Url     : http://codecanyon.net/user/DenonStudio/portfolio
 * License : http://codecanyon.net/wiki/support/legal-terms/licensing-terms/
 */

class denonstudio_stripe extends WC_Payment_Gateway
{
    protected $GATEWAY_NAME               = "Stripe";
    protected $stripeTestApiKey           = '';
    protected $stripeLiveApiKey           = '';
    protected $instructions               = '';
    protected $order                      = null;
    protected $acceptableCards            = null;
    protected $transactionId              = null;
    protected $transactionErrorMessage    = null;
    protected $usesandboxapi              = true;

    public function __construct()
    {
        $this->id              = 'Stripe';
        $this->has_fields      = true;

        $this->init_form_fields();
        $this->init_settings();
        $this->supports = array(
          'products',
          'refunds',
          'default_credit_card_form'
        );

        $this->title            = $this->settings['title'];
        $this->description      = '';
        $this->usesandboxapi    = strcmp($this->settings['debug'], 'yes') == 0;
        $this->stripeTestApiKey = $this->settings['testapikey'  ];
        $this->stripeLiveApiKey = $this->settings['liveapikey'  ];
        $this->instructions     = $this->settings['instructions'];
        $this-> view_transaction_url = "https://dashboard.stripe.com/payments/%s";

        // tell WooCommerce to save options
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('admin_notices'                                           , array($this, 'perform_ssl_check'    ));
        add_action('woocommerce_thankyou'                                    , array($this, 'thankyou_page'        ));
    }

    function perform_ssl_check()
    {
         if (!$this->usesandboxapi && get_option('woocommerce_force_ssl_checkout') == 'no' && $this->enabled == 'yes') :
            echo '<div class="error"><p>'.sprintf(__('%s sandbox testing is disabled and can performe live transactions but the <a href="%s">force SSL option</a> is disabled; your checkout is not secure! Please enable SSL and ensure your server has a valid SSL certificate.', 'woocommerce'), $this->GATEWAY_NAME, admin_url('admin.php?page=woocommerce_settings&tab=general')).'</p></div>';
         endif;
    }

    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'type'        => 'checkbox',
                'title'       => __('Enable/Disable', 'woocommerce'),
                'label'       => __('Enable Credit Card Payment', 'woocommerce'),
                'default'     => 'yes'
            ),
            'debug' => array(
                'type'        => 'checkbox',
                'title'       => __('Testing', 'woocommerce'),
                'label'       => __('Turn on testing', 'woocommerce'),
                'default'     => 'no'
            ),
            'title' => array(
                'type'        => 'text',
                'title'       => __('Title', 'woocommerce'),
                'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
                'default'     => __('Credit Card Payment', 'woocommerce')
            ),
            'instructions' => array(
                'type'        => 'textarea',
                'title'       => __('Customer Message', 'woocommerce'),
                'description' => __('This message is displayed on the buttom of the Order Recieved Page.', 'woocommerce'),
                'default'     => ''
            ),
            'testapikey' => array(
                'type'        => 'text',
                'title'       => __('Stripe API Test Secret key', 'woocommerce'),
                'default'     => __('', 'woocommerce')
            ),
            'liveapikey' => array(
                'type'        => 'text',
                'title'       => __('Stripe API Live Secret key', 'woocommerce'),
                'default'     => __('', 'woocommerce')
            )
       );
    }

    public function admin_options()
    {
        include_once('form.admin.php');
    }

    public function thankyou_page($order_id)
    {
        if ($this->instructions)
            echo wpautop(wptexturize($this->instructions));
    }

    public function validate_fields()
    {
        if (!$this->isCreditCardNumber(str_replace(' ', '', $_POST['Stripe-card-number'])))
            wc_add_notice(__('<strong>Card Number</strong> is not valid.', 'woocommerce'), 'error');

        $expire_date_parts = explode('/', str_replace(' ', '', $_POST['Stripe-card-expiry']));
        if (!$this->isCorrectExpireDate($expire_date_parts[0], $expire_date_parts[1]))
            wc_add_notice(__('<strong>Expiry Date</strong> is not valid.', 'woocommerce'), 'error');

        if (!$this->isCCVNumber($_POST['Stripe-card-cvc']))
            wc_add_notice(__('<strong>Card Verification Number</strong> is not valid.', 'woocommerce'), 'error');
    }

    public function process_payment($order_id)
    {
        $this->order        = new WC_Order($order_id);
        $gatewayRequestData = $this->getRequestData();

        if ($gatewayRequestData AND $this->gePaymentApproval($gatewayRequestData))
        {
            $this->completeOrder();

            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url( $this->order )
            );
        }
        else
        {
            $this->markAsFailedPayment();
            wc_add_notice(__('(Transaction Error) something is wrong.', 'woocommerce'), 'error');
        }
    }

    public function process_refund($order_id, $amount = NULL, $reason = '')
    {
       $order = new WC_Order($order_id);
       $request = array(
           'method'    => 'POST',
           'timeout'   => 45,
           'blocking'  => true,
           'sslverify' => $this->usesandboxapi ? false : true,
           'headers'   => array(
                'authorization' => sprintf("Bearer %s", $this->usesandboxapi ? $this->stripeTestApiKey : $this->stripeLiveApiKey)
            )
        );

       if ($amount) {
           $request['body'] = array('amount' => $amount * 100);
       }

       $api_url = sprintf("https://api.stripe.com/v1/charges/%s/refunds", $order->get_transaction_id());
       $result = wp_remote_post($api_url, $request);
       $response = $result['response'];

       if ($response['code'] == 200) {
          return true;
       } else {
          $order->add_order_note(sprintf('Refund failed with: %s', $result['body']));
          return false; 
       }
    }

    protected function markAsFailedPayment()
    {
        $this->order->add_order_note(
            sprintf(
                "%s Credit Card Payment Failed with message: '%s'",
                $this->GATEWAY_NAME,
                $this->transactionErrorMessage
            )
        );
    }

    protected function completeOrder()
    {
        global $woocommerce;

        if ($this->order->status == 'completed')
            return;

        $this->order->payment_complete($this->transactionId);
        $woocommerce->cart->empty_cart();

        $this->order->add_order_note(
            sprintf(
                "%s payment completed with Transaction Id of '%s'",
                $this->GATEWAY_NAME,
                $this->transactionId
            )
        );

        unset($_SESSION['order_awaiting_payment']);
    }

    protected function gePaymentApproval($gatewayRequestData)
    {
        $erroMessage = "";
        $api_url     = sprintf("https://%s@api.stripe.com/v1/charges", $this->usesandboxapi ? $this->stripeTestApiKey : $this->stripeLiveApiKey);
        $request     = array(
            'method'    => 'POST',
            'timeout'   => 45,
            'blocking'  => true,
            'sslverify' => $this->usesandboxapi ? false : true,
            'body'      => $this->getRequestData(),
            'headers'   => array(
                'authorization' => sprintf("Bearer %s", $this->usesandboxapi ? $this->stripeTestApiKey : $this->stripeLiveApiKey)
            )
        );

        $response = wp_remote_post($api_url, $request);

        if (!is_wp_error($response))
        {
            $parsedResponse = $this->parsetResponse($response);

            if ($parsedResponse["success"])
            {
                $this->transactionId = $parsedResponse["trxid"];
                return true;
            }
            else
            {
                $this->transactionErrorMessage = $erroMessage = $parsedResponse["error"];
            }
        }
        else
        {
            $this->transactionErrorMessage = $erroMessage = 'Something went wrong while performing your request. Please contact website administrator to report this problem.';
        }

        wc_add_notice($erroMessage, 'error');
        return false;
    }

    protected function parsetResponse($response)
    {
        $result = null;
        $body   = json_decode($response["body"]);

        if ((int)$response["response"]["code"] == 200)
        {
            return array(
                "success" => true,
                "trxid"   => $body->id
            );
        }
        else
        {
            return array(
                "success" => false,
                "error"   => $body->error->message
            );
        }

        return $result;
    }

    protected function getRequestData()
    {
        if ($this->order AND $this->order != null)
        {
            $expire_date_parts = explode('/', str_replace(' ', '', $_POST['Stripe-card-expiry']));
            return array(
                "amount"      => (float)$this->order->get_total() * 100,
                "currency"    => strtolower(get_option('woocommerce_currency')),
                "description" => sprintf("Charge for %s", $this->order->billing_email),
                "card"        => array(
                    "name"            => sprintf("%s %s", $this->order->billing_first_name, $this->order->billing_last_name),
                    "exp_month"       => $expire_date_parts[0],
                    "exp_year"        => $expire_date_parts[1],
                    "number"          => $_POST['Stripe-card-number'  ],
                    "cvc"             => $_POST['Stripe-card-cvc'     ],
                    "address_line1"   => $_POST['billing_address_1'   ],
                    "address_line2"   => $_POST['billing_address_2'   ],
                    "address_zip"     => $this->order->billing_postcode,
                    "address_state"   => $this->order->billing_state,
                    "address_country" => $this->order->billing_country
                )
            );
        }

        return false;
    }

    private function isCreditCardNumber($toCheck)
    {
        if (!is_numeric($toCheck))
            return false;

        $number = preg_replace('/[^0-9]+/', '', $toCheck);
        $strlen = strlen($number);
        $sum    = 0;

        if ($strlen < 13)
            return false;

        for ($i=0; $i < $strlen; $i++)
        {
            $digit = substr($number, $strlen - $i - 1, 1);
            if($i % 2 == 1)
            {
                $sub_total = $digit * 2;
                if($sub_total > 9)
                {
                    $sub_total = 1 + ($sub_total - 10);
                }
            }
            else
            {
                $sub_total = $digit;
            }
            $sum += $sub_total;
        }

        if ($sum > 0 AND $sum % 10 == 0)
            return true;

        return false;
    }

    private function isCCVNumber($toCheck)
    {
        $length = strlen($toCheck);
        return is_numeric($toCheck) AND $length > 2 AND $length < 5;
    }

    private function isCorrectExpireDate($month, $year)
    {
        $now        = time();
        $thisYear   = (int)date('Y', $now);
        $thisMonth  = (int)date('m', $now);

        if (is_numeric($year) && is_numeric($month))
        {
            $thisDate   = mktime(0, 0, 0, $thisMonth, 1, $thisYear);
            $expireDate = mktime(0, 0, 0, $month    , 1, $year    );

            return $thisDate <= $expireDate;
        }

        return false;
    }
}

function add_stripe_gateway($methods)
{
    array_push($methods, 'denonstudio_stripe');
    return $methods;
}

add_filter('woocommerce_payment_gateways', 'add_stripe_gateway');