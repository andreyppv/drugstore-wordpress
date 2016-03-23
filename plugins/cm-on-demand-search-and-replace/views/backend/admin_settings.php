<?php if ( !empty( $messages ) ): ?>
	<div class="updated" style="clear:both"><p><?php echo $messages; ?></p></div>
<?php endif; ?>

<br/>

<br/>

<div class="cminds_settings_description">
	<?php
	echo do_shortcode( '[cminds_free_registration]' );
	?>

    <form method="post">
        <div>
            <div class="cmodsar_field_help_container">Warning! This option will completely erase all of the data stored by the CM On Demand Search And Replace in the database! <br/> It cannot be reverted.</div>
            <input onclick="return confirm( 'All database items of CM On Demand Search And Replace (terms, options etc.) will be erased. This cannot be reverted.' )" type="submit" name="cmodsar_pluginCleanup" value="Cleanup database" class="button cmodsar-cleanup-button"/>
            <span style="display: inline-block;position: relative;"></span>
        </div>
    </form>

	<?php
// check permalink settings
	if ( get_option( 'permalink_structure' ) == '' ) {
		echo '<span style="color:red">Your WordPress Permalinks needs to be set to allow plugin to work correctly. Please Go to <a href="' . admin_url() . 'options-permalink.php" target="new">Settings->Permalinks</a> to set Permalinks to Post Name.</span><br><br>';
	}
	?>

</div>

<?php
//include plugin_dir_path(__FILE__) . '/call_to_action.phtml';
?>

<br/>
<div class="clear"></div>

<form method="post">
	<?php wp_nonce_field( 'update-options' ); ?>
    <input type="hidden" name="action" value="update" />


    <div id="cmodsar_tabs" class="customSettingsTabs">
        <div class="custom_loading"></div>

		<?php
		CMODSAR_Base::renderSettingsTabsControls();

		CMODSAR_Base::renderSettingsTabs();
		?>

        <!-- Start Server information Module -->
        <div id="tabs-99">
            <div class='block'>
                <h3>Server Information</h3>
				<?php
				$safe_mode			 = ini_get( 'safe_mode' ) ? ini_get( 'safe_mode' ) : 'Off';
				$upload_max			 = ini_get( 'upload_max_filesize' ) ? ini_get( 'upload_max_filesize' ) : 'N/A';
				$post_max			 = ini_get( 'post_max_size' ) ? ini_get( 'post_max_size' ) : 'N/A';
				$memory_limit		 = ini_get( 'memory_limit' ) ? ini_get( 'memory_limit' ) : 'N/A';
				$allow_url_fopen	 = ini_get( 'allow_url_fopen' ) ? ini_get( 'allow_url_fopen' ) : 'N/A';
				$max_execution_time	 = ini_get( 'max_execution_time' ) !== FALSE ? ini_get( 'max_execution_time' ) : 'N/A';
				$cURL				 = function_exists( 'curl_version' ) ? 'On' : 'Off';
				$mb_support			 = function_exists( 'mb_strtolower' ) ? 'On' : 'Off';
				$intl_support		 = extension_loaded( 'intl' ) ? 'On' : 'Off';

				$php_info = cminds_parse_php_info();
				?>
                <span class="description" style="">
                    The plugin is a mix of  JavaScript application and a parsing engine.
                    This information is useful to check if plugin might have some incompabilities with you server
                </span>
                <table class="form-table server-info-table">
                    <tr>
                        <td>PHP Version</td>
                        <td><?php echo phpversion(); ?></td>
                        <td><?php if ( version_compare( phpversion(), '5.3.0', '<' ) ): ?><strong>Recommended 5.3 or higher</strong><?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>mbstring support</td>
                        <td><?php echo $mb_support; ?></td>
                        <td><?php if ( $mb_support == 'Off' ): ?>
								<strong>"mbstring" library is required for plugin to work.</strong>
							<?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>intl support</td>
                        <td><?php echo $intl_support; ?></td>
                        <td><?php if ( $intl_support == 'Off' ): ?>
								<strong>"intl" library is required for proper sorting of accented characters on Custom Index page.</strong>
							<?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>PHP Memory Limit</td>
                        <td><?php echo $memory_limit; ?></td>
                        <td><?php if ( cminds_units2bytes( $memory_limit ) < 1024 * 1024 * 128 ): ?>
								<strong>This value can be too low for a site with big custom.</strong>
							<?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>PHP Max Upload Size (Pro, Pro, Ecommerce)</td>
                        <td><?php echo $upload_max; ?></td>
                        <td><?php if ( cminds_units2bytes( $upload_max ) < 1024 * 1024 * 5 ): ?>
								<strong>This value can be too low to import large files.</strong>
							<?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>PHP Max Post Size (Pro, Pro, Ecommerce)</td>
                        <td><?php echo $post_max; ?></td>
                        <td><?php if ( cminds_units2bytes( $post_max ) < 1024 * 1024 * 5 ): ?>
								<strong>This value can be too low to import large files.</strong>
							<?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>PHP Max Execution Time </td>
                        <td><?php echo $max_execution_time; ?></td>
                        <td><?php if ( $max_execution_time != 0 && $max_execution_time < 300 ): ?>
								<strong>This value can be too low for lengthy operations. We strongly suggest setting this value to at least 300 or 0 which is no limit.</strong>
							<?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>PHP cURL (Pro, Ecommerce)</td>
                        <td><?php echo $cURL; ?></td>
                        <td><?php if ( $cURL == 'Off' ): ?>
								<strong>cURL library is required to check if remote audio file exists.</strong>
							<?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>PHP allow_url_fopen (Pro, Ecommerce)</td>
                        <td><?php echo $allow_url_fopen; ?></td>
                        <td><?php if ( $allow_url_fopen == '0' ): ?>
								<strong>allow_url_fopen is required to connect to the Merriam-Webster and Wikipedia API.</strong>
							<?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>

					<?php
					if ( isset( $php_info[ 'gd' ] ) && is_array( $php_info[ 'gd' ] ) ) {
						foreach ( $php_info[ 'gd' ] as $key => $val ) {
							if ( !preg_match( '/(WBMP|XBM|Freetype|T1Lib)/i', $key ) && $key != 'Directive' && $key != 'gd.jpeg_ignore_warning' ) {
								echo '<tr>';
								echo '<td>' . $key . '</td>';
								if ( stripos( $key, 'support' ) === false ) {
									echo '<td>' . $val . '</td>';
								} else {
									echo '<td>enabled</td>';
								}
								echo '</tr>';
							}
						}
					}
					?>
                </table>
            </div>
        </div>
    </div>
    <p class="submit" style="clear:left">
        <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" name="cmodsar_customSave" />
    </p>
</form>