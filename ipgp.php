<?php
/*
Plugin Name: Ipgp ip address lookup
Plugin URI: http://www.ipgp.net
Description: Find information about ip address.
Author: Lucian Apostol
Version: 1.1.1
Author URI: http://www.ipgp.net
*/

add_action('admin_menu', 'create_ipgp_menu');
add_action('admin_init', 'ipgp_actions');

function ipgpFunction() 
{
	$ipgpip = '';
	if(isset($_POST['ipgpvalue']) && $_POST['ipgpvalue']) { 
		
		if(filter_var($_POST['ipgpvalue'], FILTER_VALIDATE_IP)) {
			
			$ipgpip = sanitize_text_field($_POST['ipgpvalue']);
		
		
	
	
			if(get_option('ipgp_api_key')) $api_key = get_option('ipgp_api_key');
			else $api_key = 'wordpressplugin';
	
			$file = "http://www.ipgp.net/api/xml/". $ipgpip ."/".$api_key."";
	
			$iplookup = simplexml_load_file($file);
	
	
	// The returned data is an object, you can type print_r($iplookup) at the end of the code to see the object content. 
		}
		else $ipgpip = '';
	}
	  $return = '
	
	
		<form id="ipgpform" method="post">
			Enter an IP Address to look up:<br />
			<input type="text" name="ipgpvalue" id="ipgpvalue" size="12" value="'. $ipgpip .'" />
			<input type="submit" name="submit" id="submit" value="Lookup" />
			<div id="ipgpresults"> ';
			
			 if($ipgpip) { 
			 
			 $return .= '<div id="ipgpcountry">Country: '. $iplookup->Country .'</div>
			<div id="ipgpcity">City '. $iplookup->City .'</div>
				<div id="ipgpregion">State: '. $iplookup->Region .'</div>
				<div id="ipgpisp">Isp: '. $iplookup->Isp .'</div>';
			} 
			$return .= '
			</div>
		</form>
	
	  ';
	  
	  return $return;
}

function ipgpWidget($args) {

	 extract($args);
	 echo $before_widget;
	  echo $before_title;?>Ip address lookup<?php echo $after_title;
	 echo ipgpFunction();
	 echo $after_widget;
}

function iplookup_shortcode( $atts ) {
	
	return ipgpFunction();

}

function create_ipgp_menu() {

			add_options_page(__('IPGP IP Lookup', 'ipgp_admin_page'), __('IPGP IP Lookup', 'ipgp_admin_page')	, 'manage_options', basename(__FILE__), 'ipgp_admin' );

}

function ipgp_admin() {
$content = '<br /><br /><div style="margin: 25px 0 0 25px;"><h1>IPGP Lookup Plugin</h1><br>To provide your visitors the ability to lookup IP Addresses, you must <a href="https://www.ipgp.net/get-api-key/">obtain an API key</a> first.<br /><br />
<form name="ipgp_admin_menu_form" action="" method="post">
Insert you API Key here : <input type="text" name="ipgp_api" value="'.get_option('ipgp_api_key').'" /><br /><br />
<input type="submit" name="Submit" value="Submit" />
</form>
</div>

<br />
<br />
After you add your API key in the form above, you can use \'[iplookup]\' shortcode to display an ip lookup box in your website. You can add this shorcode in your post content, sidebar, etc. 
';
echo $content;
}

function ipgp_actions() {

	if(!current_user_can( 'manage_options' )) return false;
	
	if(isset($_POST['ipgp_api']) && $_POST['ipgp_api']) 
		update_option( 'ipgp_api_key', ''. sanitize_text_field($_POST['ipgp_api']) .'', 'yes' );

}

function ipgpLookupInit()
{
  wp_register_sidebar_widget('ipgp_lookup_widget' ,__('Ip lookup', 'ipgp_admin_page'), 'ipgpWidget');  
  add_shortcode( 'iplookup', 'iplookup_shortcode' );
}


add_action("plugins_loaded", "ipgpLookupInit");
?>