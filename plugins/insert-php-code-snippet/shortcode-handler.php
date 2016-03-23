<?php 
global $wpdb;

add_shortcode('xyz-ips','xyz_ips_display_content');		

function xyz_ips_display_content($xyz_snippet_name){
	global $wpdb;

	if(is_array($xyz_snippet_name)){
		$snippet_name = $xyz_snippet_name['snippet'];
		
		$query = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."xyz_ips_short_code WHERE title=%s" ,$snippet_name));
		
		if(count($query)>0){
			
			foreach ($query as $sippetdetails){
				if($sippetdetails->status==1){
					
					if(is_numeric(ini_get('output_buffering'))){
						$tmp=ob_get_contents();
						ob_clean();
						ob_start();
						$content_to_eval=$sippetdetails->content;
						if(substr($content_to_eval, 0,5)=='<?php')
							$content_to_eval='?>'.$content_to_eval;
						eval($content_to_eval);
						$xyz_em_content = ob_get_contents();
						ob_clean();
						echo $tmp;
						return $xyz_em_content;
					}else{
						eval($sippetdetails->content);
					}
					
					
					
				}else{ 
					return '';
				}
			break;
			}
			
		}else{

			return '';
/*			return "<div style='padding:20px; font-size:16px; color:#FA5A6A; width:93%;text-align:center;background:lightyellow;border:1px solid #3FAFE3; margin:20px 0 20px 0'>
			
			Please use a valid short code to call snippet.
			
			
			</div>";
*/			
		}
		
	}
}


add_filter('widget_text', 'do_shortcode'); // to run shortcodes in text widgets
