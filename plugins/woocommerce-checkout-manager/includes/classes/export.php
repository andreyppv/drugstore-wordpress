<?php
/**
 * WooCommerce Checkout Manager 
 */
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function wooccm_additional_gen( $tab, $abbr, $section, $wooname ) {
	global $woocommerce, $wpdb;
    $options = get_option( 'wccs_settings' );
	$options2 = get_option( 'wccs_settings2' ); // shipping
	$options3 = get_option( 'wccs_settings3' ); // billing
	
	$args = array(
		'post_type'		=> 'shop_order',
		'posts_per_page' 	=> -1,
        	'post_status' => array( 'wc-processing', 'wc-completed' )
			);
			
	$loop = new WP_Query( $args );
    $csv_output = '';

					if ( $wooname == 'additional' ){
						$optionname = $options['buttons'];
					} 
					elseif ( $wooname == 'shipping' ){
						$optionname = $options2['shipping_buttons'];
					}
					elseif ( $wooname == 'billing' ){
						$optionname = $options3['billing_buttons'];
					}
						
						if( !empty($abbr) && $section == 1 ) {
							if ( $tab == $wooname ) {
									while ( $loop->have_posts() ) {
										$loop->the_post();
										$order_id = $loop->post->ID;
										$order = new WC_Order($order_id);
									
										if ( get_post_meta($order_id, $abbr, true) ) {
											$csv_output .= '["'.$order->billing_first_name.' '.$order->billing_last_name.'", "'.get_post_meta($order_id, $abbr, true).'" ], ';
										}
									}	
							}elseif ($tab == 'heading' ) {		
											$csv_output .= '["Name","'.$abbr.'"]';
							}
						} elseif( empty($abbr) && $section == 2 ) {
							if ( $tab == $wooname ) {
									
									while ( $loop->have_posts() ) {
										$loop->the_post();
										$order_id = $loop->post->ID;
										$order = new WC_Order($order_id);
										foreach( $optionname as $name ) {
											if ( get_post_meta($order_id, $name['cow'], true) ) {
												$listida[] = $order_id;	
											}
										}
									}
									$csv_output = array_unique($listida);
							}elseif ($tab == 'heading' ) {
									while ( $loop->have_posts() ) {
										$loop->the_post();
										$order_id = $loop->post->ID;
										$order = new WC_Order($order_id);
										foreach( $optionname as $n) {
												if ( get_post_meta($order_id, $n['cow'], true) ) {	
														$lista[] = $n['label'];
												}
										}
									}
									$csv_output = array_unique($lista);
							}
						}
return $csv_output;
}


/**
* Converting data to CSV [ SETTINGS DATA ]
*/
function wooccm_generate_csv($tab) {

    $options = get_option( 'wccs_settings' );
	$options2 = get_option( 'wccs_settings2' );
		$options3 = get_option( 'wccs_settings3' );

    $csv_output = '';
    
    if ( $tab == 'additional' ) {
        
		if ( !empty($options['buttons']) ) {
		
		$total = count($options['buttons']) - 1;
		
			foreach( $options['buttons'] as $i => $btn) {
				if( $i != 999 && !empty($btn['cow']) ) {
					$csv_output .= '[';
					
						foreach($btn as $n => $dataw) {
								$csv_output .= '"'.$dataw.'",';
						}
					
					if ( $i != $total ) {
						$csv_output .= '], ';
					} else {
						$csv_output .= ']';   
					}
				}
			}
		}
    }elseif ($tab == 'billing' ) {

    	$total = count($options3['billing_buttons']) - 1;

		if (!empty($options3['billing_buttons']) ) {
			foreach( $options3['billing_buttons'] as $i => $btn) {
				if( $i != 999 && !empty($btn['cow']) ) {
					$csv_output .= '[';
					
						foreach($btn as $n => $dataw) {
							$csv_output .= '"'.$dataw.'",';
						}
					
					if ( $i != $total) {
						$csv_output .= '], ';
					} else {
						$csv_output .= ']';   
					}
				}
			}
        } 
    }elseif ( $tab == 'shipping') {
        $total = count($options2['shipping_buttons']) -1;
        
		if( !empty($options2['shipping_buttons']) ) {
			foreach( $options2['shipping_buttons'] as $i => $btn) {
				if( $i != 999 && !empty($btn['cow']) ) {
					$csv_output .= '[';
					
						foreach($btn as $n => $dataw) {
								$csv_output .= '"'.$dataw.'",';	
						}
					
					if ( $i != $total) {
						$csv_output .= '], ';
					} else {
						$csv_output .= ']';   
					}
				}
			}
		}
	}
	elseif ( $tab == 'general') {

		if( !empty($options['checkness']) ) {
			$csv_output .= '[';
			foreach( $options['checkness'] as $i => $btn) {
					$csv_output .= '"'.$btn.'",';					  
			}
			$csv_output .= ']'; 
		}
	}elseif ($tab == 'heading' ) {

		if (!empty($options3['billing_buttons']) ) {
				$csv_output .= '[';
				
					foreach( $options3['billing_buttons'][0] as $n => $dataw) {
						$csv_output .= '"'.$n.'",';
					}
				
					$csv_output .= ']';   
	    }
    }elseif ($tab == 'heading3' ) {

		if (!empty($options['buttons']) ) {
				$csv_output .= '[';
				
					foreach( $options['buttons'][0] as $n => $dataw) {
						$csv_output .= '"'.$n.'",';
					}
				
					$csv_output .= ']';  
        } 
    }elseif ($tab == 'heading2' ) {

    	if (!empty($options['checkness']) ) {
            $csv_output .= '[';
			foreach( $options['checkness'] as $n => $btn) {
                    $csv_output .= '"'.$n.'",';
			}
            $csv_output .= ']'; 
        } 
    }

	
return $csv_output;
}
// --------------- END SETTINGS DATA ----------------

function wooccm_csvall_heading($heading) {
	$csv_output .= '["Name", ';
	foreach($heading as $data ){
		$csv_output .= '"'.$data.'", ';
	}
	$csv_output .= ']';
	
	return $csv_output;
}

function wooccm_csvall_info($orderids, $wooname){
$options = get_option( 'wccs_settings' );
$options2 = get_option( 'wccs_settings2' );
$options3 = get_option( 'wccs_settings3' );
	
	foreach( $orderids as $order_id ) {
		$csv_output .= '["'.get_post_meta($order_id, '_billing_first_name', true).' '.get_post_meta($order_id, '_billing_last_name', true).'", ';
				
	
	if( $wooname == 'additional'){
		foreach( $options['buttons'] as $name2 ) {
			$csv_output .= '"'.get_post_meta($order_id, $name2['cow'], true).'", ';
		}
	}
	if( $wooname == 'billing'){
		foreach( $options3['billing_buttons'] as $name2 ) {
			$csv_output .= '"'.get_post_meta($order_id, $name2['cow'], true).'", ';
		}
	}
	if( $wooname == 'shipping'){
		foreach( $options2['shipping_buttons'] as $name2 ) {
			$csv_output .= '"'.get_post_meta($order_id, $name2['cow'], true).'", ';
		}
	}
		
		$csv_output .= '], ';
	}
	return $csv_output;
}


function wooccm_advance_export(){ 
$options = get_option( 'wccs_settings' );
$options2 = get_option( 'wccs_settings2' );
$options3 = get_option( 'wccs_settings3' );
					
if ( isset($_POST['single-download']) && !empty($_POST['single-download']) ) {
	
	if( $_POST['single-download'] == 'additional' ){
		$csv = wooccm_additional_gen('additional', $_POST['selectedval'], 1);
		$heading = wooccm_additional_gen('heading', $_POST['selectedval'], 1);	
	}
	elseif( $_POST['single-download'] == 'shipping' ){
		$csv = wooccm_additional_gen('shipping', $_POST['shippingselectedval'], 1, 'shipping');
		$heading = wooccm_additional_gen('heading', $_POST['shippingselectedval'], 1, 'shipping' );	
	}
	elseif( $_POST['single-download'] == 'billing' ){
		$csv = wooccm_additional_gen('billing', $_POST['billingselectedval'], 1, 'billing' );
		$heading = wooccm_additional_gen('heading', $_POST['billingselectedval'], 1, 'billing' );	
	}
?> 

<script type="text/javascript">
jQuery(document).ready(function($) {

var A = [<?php echo $heading.','.$csv; ?>];  // initialize array of rows with header row as 1st item

var csvRows = [];
for(var i=0, l=A.length; i<l; ++i){ // for each array( [..] ), join with commas for csv
for (index = 0; index < A[i].length; ++index) {
    A[i][index] = '"'+A[i][index]+'"'; // add back quotes for each string, to store special characters and commas
}
    csvRows.push( A[i] );   // put data in a java useable array
}

var csvString = csvRows.join("\n"); // make rows for each array

var a = document.createElement('a');

a.href     = 'data:attachment/csv,' + encodeURIComponent(csvString);
a.target   = '_blank';
a.download = 'only_additional_fieldname.csv';
document.body.appendChild(a);
a.click();
	                   
});
</script>

<?php } 

// ----------- ALL DOWNLOAD ---------
if ( isset($_POST['all-download']) && !empty($_POST['all-download']) ) {
	
	$abbr = '';
	if( $_POST['all-download'] == 'additional' ){
		$csv = wooccm_additional_gen('additional', $abbr, 2);
		$csv = wooccm_csvall_info($csv, 'additional' );
		$heading = wooccm_additional_gen('heading', $abbr, 2);	
		$heading = wooccm_csvall_heading($heading);
	}
	elseif( $_POST['all-download'] == 'shipping' ){
		$csv = wooccm_additional_gen('additional', $abbr, 2, 'shipping' );
		$csv = wooccm_csvall_info($csv, 'shipping' );
		$heading = wooccm_additional_gen('heading', $abbr, 2, 'shipping');	
		$heading = wooccm_csvall_heading($heading);
	}
	elseif( $_POST['all-download'] == 'billing' ){
		$csv = wooccm_additional_gen('billing', $abbr, 2, 'billing' );
		$csv = wooccm_csvall_info($csv, 'billing' );
		$heading = wooccm_additional_gen('heading', $abbr, 2, 'billing' );	
		$heading = wooccm_csvall_heading($heading);
	}
?> 

<script type="text/javascript">
jQuery(document).ready(function($) {

var A = [<?php echo $heading.','.$csv; ?>];  // initialize array of rows with header row as 1st item

var csvRows = [];
for(var i=0, l=A.length; i<l; ++i){ // for each array( [..] ), join with commas for csv
for (index = 0; index < A[i].length; ++index) {
    A[i][index] = '"'+A[i][index]+'"'; // add back quotes for each string, to store special characters and commas
}
    csvRows.push( A[i] );   // put data in a java useable array
}

var csvString = csvRows.join("\n"); // make rows for each array

var a = document.createElement('a');

a.href     = 'data:attachment/csv,' + encodeURIComponent(csvString);
a.target   = '_blank';
a.download = 'only_additional_fieldname.csv';
document.body.appendChild(a);
a.click();
	                   
});
</script>

<?php } 
// ---------- END ALL DOWNLOAD --------------

// ---------- SETTING DOWNLOAD --------------
if ( isset($_POST['setting-download']) && !empty($_POST['setting-download']) ) {
	
	if( $_POST['setting-download'] == 'additional' ){
		$csv = wooccm_generate_csv('additional');
		$heading = wooccm_generate_csv('heading3');
	}
	
	print_r( $heading );
	
	if( $_POST['setting-download'] == 'billing' ){
		$csv = wooccm_generate_csv('billing');
		$heading = wooccm_generate_csv('heading');
	}
	if( $_POST['setting-download'] == 'shipping' ){
		$csv = wooccm_generate_csv('shipping');
		$heading = wooccm_generate_csv('heading');
	}
	if( $_POST['setting-download'] == 'general' ){
		$csv = wooccm_generate_csv('general');
		$heading = wooccm_generate_csv('heading2');
	}

?> 

<script type="text/javascript">
jQuery(document).ready(function($) {

var A = [<?php echo $heading.','.$csv; ?>];  // initialize array of rows with header row as 1st item

var csvRows = [];
for(var i=0, l=A.length; i<l; ++i){ // for each array( [..] ), join with commas for csv
for (index = 0; index < A[i].length; ++index) {
    A[i][index] = '"'+A[i][index]+'"'; // add back quotes for each string, to store special characters and commas
}
    csvRows.push( A[i] );   // put data in a java useable array
}

var csvString = csvRows.join("\n"); // make rows for each array

var a = document.createElement('a');

a.href     = 'data:attachment/csv,' + encodeURIComponent(csvString);
a.target   = '_blank';
a.download = 'only_additional_fieldname.csv';
document.body.appendChild(a);
a.click();
	                   
});
</script>

<?php } 
// ---------------- END SETTING DOWNLOAD --------------
?>


<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery(function () {
						jQuery(".button.single-download.additional").click(function() {
							jQuery("input[name=single-download]").val("additional");
							jQuery("#additional_export").submit();
						});
						
						jQuery(".button.all-download.additional").click(function() {
							jQuery("input[name=all-download]").val("additional");
							jQuery("#additional_export").submit();
						});
						
						jQuery(".button.setting-download.additional").click(function() {
							jQuery("input[name=setting-download]").val("additional");
							jQuery("#additional_export").submit();
						});
						
						<!-- shipping -->
						jQuery(".button.single-download.shipping").click(function() {
							jQuery("input[name=single-download]").val("shipping");
							jQuery("#additional_export").submit();
						});
						
						jQuery(".button.all-download.shipping").click(function() {
							jQuery("input[name=all-download]").val("shipping");
							jQuery("#additional_export").submit();
						});
						jQuery(".button.setting-download.additional").click(function() {
							jQuery("input[name=setting-download]").val("shipping");
							jQuery("#additional_export").submit();
						});
						<!-- end shipping -->
						
						
						<!-- billing -->
						jQuery(".button.single-download.billing").click(function() {
							jQuery("input[name=single-download]").val("billing");
							jQuery("#additional_export").submit();
						});
						
						jQuery(".button.all-download.billing").click(function() {
							jQuery("input[name=all-download]").val("billing");
							jQuery("#additional_export").submit();
						});
						
						jQuery(".button.setting-download.additional").click(function() {
							jQuery("input[name=setting-download]").val("billing");
							jQuery("#additional_export").submit();
						});
						<!-- end billing -->
						
					});
					
				});
</script>
			

<div class="wrap">

<div id="welcome-panel" class="welcome-panel heading">
<h1 class="heading-blue"><?php _e( 'Field Data Export', 'woocommerce-checkout-manager'); ?></h1>
</div>


<div id="welcome-panel" class="welcome-panel heading">
<form name="additionalexport" method="post" action="" id="additional_export">
<input type="hidden" name="single-download" val="" />
<input type="hidden" name="all-download" val="" />
<input type="hidden" name="setting-download" val="" />

		
<div id="welcome-panel" class="welcome-panel left">

<!-- ADDITIONAL SECTION --> 
		<p class="about-description heading"><?php _e( 'Additional Fields Section', 'woocommerce-checkout-manager'); ?>
		</p>
		<hr />
        <div class="welcome-panel-content">
                <p class="about-description inner"><?php _e( 'Export All Orders with abbreviation name : ', 'woocommerce-checkout-manager'); ?>
					<select name="selectedval">
					<?php foreach( $options['buttons'] as $name ) { ?>
						<option value="<?php echo $name['cow']; ?>"><?php echo $name['cow']; ?></option>
					<?php } ?>
					</select>
				</p>
				
                
                <div class="welcome-panel-column-container">
                	<div class="welcome-panel-column">
                        <ul>
                			<a class="button button-primary button-hero single-download additional" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>
                		</ul>  
                    </div>
                </div>
				
			<div class="sheet ">
			</div>
				<p style="clear:both;" class="about-description inner"><?php _e( 'Export All Orders', 'woocommerce-checkout-manager'); ?>
				</p>
                
                <div class="welcome-panel-column-container">
                	<div class="welcome-panel-column">
                        <ul>
                			<a class="button button-primary button-hero all-download additional" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>	
                		</ul>  
                    </div>
                </div>
					
			<div class="sheet ">
			</div>
			
			
			<p style="clear:both;" class="about-description inner"><?php _e( 'Export Settings', 'woocommerce-checkout-manager'); ?>
				</p>
                
                <div class="welcome-panel-column-container">
                	<div class="welcome-panel-column">
                        <ul>
                			<a class="button button-primary button-hero setting-download additional" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>
                		</ul>  
                    </div>
                </div>
			
				
	    </div>
<!-- // END ADDITIONAL SECTION -->
</div>



<div id="welcome-panel" class="welcome-panel left billing">
<!-- BILLING SECTION -->
		<p class="about-description heading"><?php _e( 'Billing Fields Section', 'woocommerce-checkout-manager'); ?>
		</p>
		<hr />
        <div class="welcome-panel-content">
                <p class="about-description inner"><?php _e( 'Export All Orders with abbreviation name : ', 'woocommerce-checkout-manager'); ?>
					<select name="billingselectedval">
					<?php foreach( $options3['billing_buttons'] as $name ) { ?>
						<option value="<?php echo $name['cow']; ?>"><?php echo $name['cow']; ?></option>
					<?php } ?>
					</select>
				</p>
				
                
                <div class="welcome-panel-column-container">
                	<div class="welcome-panel-column">
                        <ul>
                			<a class="button button-primary button-hero single-download billing" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>
                		</ul>  
                    </div>
                </div>
				
			<div class="sheet ">
			</div>
				<p style="clear:both;" class="about-description inner"><?php _e( 'Export All Orders', 'woocommerce-checkout-manager'); ?>
				</p>
                
                <div class="welcome-panel-column-container">
                	<div class="welcome-panel-column">
                        <ul>
                			<a class="button button-primary button-hero all-download billing" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>	
                		</ul>  
                    </div>
                </div>
					
			<div class="sheet ">
			</div>
			
			
			<p style="clear:both;" class="about-description inner"><?php _e( 'Export Settings', 'woocommerce-checkout-manager'); ?>
				</p>
                
                <div class="welcome-panel-column-container">
                	<div class="welcome-panel-column">
                        <ul>
                			<a class="button button-primary button-hero setting-download billing" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>
                		</ul>  
                    </div>
                </div>
				
				
	    </div>
</div>  
<!-- END BILLING SECTION -->


<div id="welcome-panel" class="welcome-panel left shipping">
<!-- SHIPPING SECTION -->
		<p class="about-description heading"><?php _e( 'Shipping Fields Section', 'woocommerce-checkout-manager'); ?>
		</p>
		<hr />
        <div class="welcome-panel-content">
                <p class="about-description inner"><?php _e( 'Export All Orders with abbreviation name : ', 'woocommerce-checkout-manager'); ?>
					<select name="shippingselectedval">
					<?php foreach( $options2['shipping_buttons'] as $name ) { ?>
						<option value="<?php echo $name['cow']; ?>"><?php echo $name['cow']; ?></option>
					<?php } ?>
					</select>
				</p>
				
                
                <div class="welcome-panel-column-container">
                	<div class="welcome-panel-column">
                        <ul>
                			<a class="button button-primary button-hero single-download shipping" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>
                		</ul>  
                    </div>
                </div>
				
			<div class="sheet  ">
			</div>
				<p style="clear:both;" class="about-description inner"><?php _e( 'Export All Orders', 'woocommerce-checkout-manager'); ?>
				</p>
                
                <div class="welcome-panel-column-container">
                	<div class="welcome-panel-column">
                        <ul>
                			<a class="button button-primary button-hero all-download shipping" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>	
                		</ul>  
                    </div>
                </div>
					
			<div class="sheet ">
			</div>
			
				
			<p style="clear:both;" class="about-description inner"><?php _e( 'Export Settings', 'woocommerce-checkout-manager'); ?>
				</p>
                
                <div class="welcome-panel-column-container">
                	<div class="welcome-panel-column">
                        <ul>
                			<a class="button button-primary button-hero setting-download shipping" href="#"><?php _e( 'Download', 'woocommerce-checkout-manager'); ?></a>
                		</ul>  
                    </div>
                </div>
				
				
	    </div>
</div>  
<!-- END SHIPPING SECTION -->




</form>
</div>
</div>

<?php }