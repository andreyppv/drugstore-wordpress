<section id="" class="cm">
	<div class="left box padding">
		<div  class="postbox">
			<h3><span>CreativeMinds Support </span></h3>
			<div class="inside">
				<p>Please click on the button to visit the forum and to submit your support questions. </p>
				<p><a href="<?php echo $this->getOption( 'plugin-support-url' ); ?>"  target="_blank" class="buttonblue">Open WordPress Support Forum</a>  </p>

	<hr>
	<h4>Share your Appreciation</h4>
	<p>Please consider sharing your experience by leaving a review. It helps us to continue our efforts in promoting this plugin.</p>
        <a target="_blank" href="<?php echo $this->getOption( 'plugin-review-url' ); ?>">
            <div class="btn button">
                <div class="dashicons dashicons-share-alt2"></div><span>Submit a review</span>
            </div>
        </a>
 
			</div>
		</div>

		<div  class="postbox">
			<h3><span>About CreativeMinds</span></h3>
			<div class="inside">
				<p><a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/about/'); ?>" target="_blank">The CreativeMinds team</a> specializes in creating cutting-edge WordPress Plugins and Magento® & Ecommerce Extensions, aimed to satisfy the growing needs of website administrators, designers and developers worldwide.</p>
				<p>CreativeMinds offers <a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/hire-us/'); ?>"  target="_blank">Custom WordPress Plugins</a> to suit your specific requirements and make your WordPress website stand out above the rest! Our team of expert developers can add <a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/hire-us/'); ?>"  target="_blank">custom features</a> to modify our existing plugins in a way that best suits your needs, or create a totally unique plugin from scratch! <a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/contact/'); ?>"  target="_blank">Contact us</a> to hear more</p>
 				<hr/>
        <h4>Stay Up-to-Date</h4>
        Twitter: <a href="https://twitter.com/CMPLUGINS" class="twitter-follow-button" data-show-count="false" data-size="large" data-dnt="true">Follow @CMPLUGINS</a>
        <script>!function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                if (!d.getElementById(id)) {
                    js = d.createElement(s);
                    js.id = id;
                    js.src = p + '://platform.twitter.com/widgets.js';
                    fjs.parentNode.insertBefore(js, fjs);
                }
            }(document, 'script', 'twitter-wjs');
        </script>
		<br />
        Google: <div class="g-follow" data-annotation="none" data-height="24" data-href="https://plus.google.com/108513627228464018583" data-rel="publisher"></div>

        <script type="text/javascript">
            (function () {
                var po = document.createElement('script');
                po.type = 'text/javascript';
                po.async = true;
                po.src = 'https://apis.google.com/js/platform.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(po, s);
            })();
        </script>

       <div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=459655384109264";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>

		Facebook: <div class="fb-follow" data-href="https://www.facebook.com/cmplugins" data-layout="standard" data-show-faces="false"></div>
        <hr>

        <!-- Begin MailChimp Signup Form -->
        <div id="mc_embed_signup">
            <form action="//cminds.us3.list-manage.com/subscribe/post?u=f48254f757fafba2669ae5918&amp;id=142732cbf9" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                <div id="mc_embed_signup_scroll">
                    <h4 for="mce-EMAIL">CM Newsletter - coupons, deals, news</h4>
                    <input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
                    <input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="btn button">
                    <span style="display:inline-block; position: relative"><div class="cmf_field_help" title="We only send newsletters a couple of times a year. They include great deals, promo codes and information about our new plugins!"></div></span>
                    <!-- real people should not fill this in and expect good things - do not remove this or risk fsorm bot signups-->
                    <div style="position: absolute; left: -5000px;"><input type="text" name="b_f48254f757fafba2669ae5918_142732cbf9" tabindex="-1" value=""></div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
        <!--End mc_embed_signup-->
		<br />
 			<h4><span>Affiliate Program</span></h4>
				<p>Earn money by referring your site visitor to CreativeMinds plugins store</p>
       			<p>
				<a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/referral-program/'); ?>"  target="_blank" class="buttonblue">Affiliate Program</a>
			</p>		


			</div>
		</div>

		<div  class="postbox">
			<h3><span>System Information</span></h3>
			<div class="inside">
				<?php echo $this->displayServerInformationTab(); ?>
			</div>
		</div>
	</div>

	<div class="right box padding">
		<div id="pages" class="pages postbox">
			<h3><span>Plugin Documentation</span><strong class="label-title-link"> <a class="label-title-link-class"  target="_blank" href="<?php echo $this->getUserguideUrl(); ?>">View Plugin Documentation >></a></strong></h3>

			<div class="inside">
				<p>For more detailed explanations please visit the plugin <a href="<?php echo $this->addAffiliateCode($this->getUserguideUrl()); ?>"  target="_blank">online documentation</a>. We also have a <a href="<?php echo $this->addAffiliateCode($this->getOption('plugin-store-url')); ?>"  target="_blank">detailed product page</a> for this plugin which includes demos and <a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/cm-plugins-video-library/'); ?>"  target="_blank">video tutorials</a>.</p>
				<hr/>
				<div class="label-video">
					<iframe src="https://player.vimeo.com/video/134692135" width="500" height="280" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				</div>
			</div>
		</div>

		<div id="buy" class="buy postbox">
			<h3> <span>Buy CreativeMidns bundle of all CM WordPress plugins</span></h3>
			<div class="plugins">
				<div class="list">
					<div class="plugins-table">
						<div class="plugins-img item">
							<a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/cm-wordpress-plugins-yearly-membership/'); ?>" target="_blank">
								<img class="img" src="https://plugins.cminds.com/wp-content/uploads/2013/08/WPmembership.png">
							</a>
						</div>

						<div class="plugins-price item">
							<span>$159</span>
						</div>

						<div class="plugins-body item">
							<p><strong>BEST VALUE:</strong> Get all CM products for a great discount! Offer includes unlimited updates and expert support.</p>
						</div>

						<div class="plugins-action item">
							<a class="button-success" href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/cm-wordpress-plugins-yearly-membership/'); ?>" target="_blank" >Buy</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="buy" class="buy postbox">
			<h3> <span>Get individual CM plugins</span></h3>
			<div class="plugins">

				<div class="list">


					<hr/>

					<div class="plugins-table">
						<div class="plugins-img item">
							<a href="https://wordpress.org/plugins/enhanced-tooltipglossary/" target="_blank">
								<img class="img" src="https://www.cminds.com/wp-content/uploads/edd/180x180_Tooltip4.png">
							</a>
						</div>

						<div class="plugins-price item">
							<span>FREE</span>
						</div>

						<div class="plugins-body item">
							<p><strong>CM Tooltip Glossary</strong> - The best glossary managment tool for WordPress. Free Edition</p>
						</div>

						<div class="plugins-action item">
							<a class="button-download" href="https://wordpress.org/plugins/enhanced-tooltipglossary/" target="_blank" >Download</a>
						</div>
					</div>

					<!-- CM Tooltip Plugin -->
					<div class="plugins-table">
						<div class="plugins-img item">
							<a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/tooltipglossary'); ?>" target="_blank">
								<img class="img" width="80" src="https://www.cminds.com/wp-content/uploads/edd/180x180_Tooltip4.png">
							</a>
						</div>

						<div class="plugins-price item">
							<span>From $29</span>
						</div>

						<div class="plugins-body item">
							<p><strong>CM Tooltip Glossary</strong> - The best glossary managment tool for WordPress</p>
						</div>

						<div class="plugins-action item">
							<a class="button-success" href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/tooltipglossary'); ?>" target="_blank" >Buy</a>
						</div>
					</div>

					<!-- CM Answers Plugin -->
					<div class="plugins-table">
						<div class="plugins-img item">
							<a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/answers'); ?>" target="_blank">
								<img class="img" src="https://www.cminds.com/wp-content/uploads/edd/iconanswers1.png">
							</a>
						</div>

						<div class="plugins-price item">
							<span>$29</span>
						</div>

						<div class="plugins-body item">
							<p><strong>CM Answers</strong> - Questions and Answers discussion forum.</p>
						</div>

						<div class="plugins-action item">
							<a class="button-success" href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/answers'); ?>" target="_blank" >Buy</a>
						</div>
					</div>

					<!-- Download Manager Plugin -->
					<div class="plugins-table">
						<div class="plugins-img item">
							<a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/downloadsmanager'); ?>" target="_blank">
								<img class="img" src="https://www.cminds.com/wp-content/uploads/edd/180x180_Dawnload5.png">
							</a>
						</div>

						<div class="plugins-price item">
							<span>$29</span>
						</div>

						<div class="plugins-body item">
							<p><strong>CM Download Manager</strong> - The ultimate tool for managing uploads and downloads.</p>
						</div>

						<div class="plugins-action item">
							<a class="button-success" href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/downloadsmanager'); ?>" target="_blank" >Buy</a>
						</div>
					</div>

					<!--  Pop Up Manager Plugin -->
					<div class="plugins-table">
						<div class="plugins-img item">
							<a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/cm-pop-up-banners-plugin-for-wordpress/'); ?>" target="_blank">
								<img class="img" src="https://www.cminds.com/wp-content/uploads/edd/icon-pop-up_5.png">
							</a>
						</div>

						<div class="plugins-price item">
							<span>$29</span>
						</div>

						<div class="plugins-body item">
							<p><strong>CM Pop Up Manager</strong> - Easily publish your  events and products using PopUp Banners.</p>
						</div>

						<div class="plugins-action item">
							<a class="button-success" href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/cm-pop-up-banners-plugin-for-wordpress/'); ?>" target="_blank" >Buy</a>
						</div>
					</div>

					<!--  Business Directory  Plugin -->
					<div class="plugins-table">
						<div class="plugins-img item">
							<a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/purchase-cm-business-directory-plugin-for-wordpress/'); ?>" target="_blank">
								<img class="img" src="https://www.cminds.com/wp-content/uploads/edd/businessdirectory2.png">
							</a>
						</div>

						<div class="plugins-price item">
							<span>$29</span>
						</div>

						<div class="plugins-body item">
							<p><strong>CM Business Directory</strong> - Supports the management of a business listing.</p>
						</div>

						<div class="plugins-action item">
							<a class="button-success" href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/purchase-cm-business-directory-plugin-for-wordpress/'); ?>" target="_blank" >Buy</a>
						</div>
					</div>

					<!--  Video Lessons  Plugin -->
					<div class="plugins-table">
						<div class="plugins-img item">
							<a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/purchase-cm-video-lessons-manager-plugin-for-wordpress/'); ?>" target="_blank">
								<img class="img" src="https://www.cminds.com/wp-content/uploads/cm-video-training-2.png">
							</a>
						</div>

						<div class="plugins-price item">
							<span>$29</span>
						</div>

						<div class="plugins-body item">
							<p><strong>CM Video Manager</strong> - Manage video lessons and allow users and admin to track progress.</p>
						</div>

						<div class="plugins-action item">
							<a class="button-success" href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/purchase-cm-video-lessons-manager-plugin-for-wordpress/'); ?>" target="_blank" >Buy</a>
						</div>
					</div>


					<!--  FAQ  Plugin -->
					<div class="plugins-table">
						<div class="plugins-img item">
							<a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/faq-plugin-for-wordpress-by-creativeminds'); ?>" target="_blank">
								<img class="img" src="https://www.cminds.com/wp-content/uploads/edd/cm_faq.png">
							</a>
						</div>

						<div class="plugins-price item">
							<span>$29</span>
						</div>

						<div class="plugins-body item">
							<p><strong>CM FAQ</strong> - Build powerful frequently answered question (FAQ) knowledge base.</p>
						</div>

						<div class="plugins-action item">
							<a class="button-success" href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/faq-plugin-for-wordpress-by-creativeminds'); ?>" target="_blank" >Buy</a>
						</div>
					</div>

					<!--  Search and Replace  Plugin -->
					<div class="plugins-table">
						<div class="plugins-img item">
							<a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/purchase-cm-on-demand-search-and-replace-plugin-for-wordpress/'); ?>" target="_blank">
								<img class="img" src="https://www.cminds.com/wp-content/uploads/edd/OnDemandSearchReplace.png">
							</a>
						</div>

						<div class="plugins-price item">
							<span>$29</span>
						</div>

						<div class="plugins-body item">
							<p><strong>CM Search and Replace</strong> - On demand search and replace tool allows you to easily replace texts & html.</p>
						</div>

						<div class="plugins-action item">
							<a class="button-success" href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/purchase-cm-on-demand-search-and-replace-plugin-for-wordpress/'); ?>" target="_blank" >Buy</a>
						</div>
					</div>

					<!--  Cm Map Location Plugin -->
					<div class="plugins-table">
						<div class="plugins-img item">
							<a href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/maps-routes-manager-plugin-for-wordpress-by-creativeminds/'); ?>" target="_blank">
								<img class="img" src="https://www.cminds.com/wp-content/uploads/edd/map_routes_manager.png">
							</a>
						</div>

						<div class="plugins-price item">
							<span>$49</span>
						</div>

						<div class="plugins-body item">
							<p><strong>CM Map Route Manager</strong> - Generate a catalog of map routes and trails.</p>
						</div>

						<div class="plugins-action item">
							<a class="button-success" href="<?php echo $this->addAffiliateCode('https://www.cminds.com/store/maps-routes-manager-plugin-for-wordpress-by-creativeminds/'); ?>" target="_blank" >Buy</a>
						</div>
					</div>

					<hr/>

					<a href="<?php echo $this->getStoreUrl(); ?>"  target="_blank" class="buttonorange">View All CM Plugins</a>
					<a href="<?php echo $this->getStoreUrl( array( 'category' => 'Bundle' )); ?>"  target="_blank" class="buttonblue">View Bundles</a>
					<a href="<?php echo $this->getStoreUrl( array( 'category' => 'Add-Ons' )); ?>"  target="_blank" class="buttonblue">View AddOns</a>
					<a href="<?php echo $this->getStoreUrl( array( 'category' => 'Service' )); ?>" target="_blank" class="buttonblue">View Services</a>
				</div>
			</div>
		</div>
	</div>
</section>