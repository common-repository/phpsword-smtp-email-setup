<?php
/**
* Plugin Name: PhpSword SMTP Email Setup
* Description: PhpSword SMTP Email Setup WordPress plugin lets you configure WordPress to use SMTP email address to sent all the emails from your WordPress website.
* Version: 1.0
* Author: Pradnyankur Nikam
* License: GPLv3
*/

class PhpswSES {

public $PhpswSESOptions;

public function PhpswSES(){
	$this->PhpswSESOptions = get_option('PhpswSESOptions');
	if(is_admin()){ $this->register_settings_and_field(); }
	if($this->PhpswSESOptions['PhpswSESMailer']=='smtpMail'){
	add_action('phpmailer_init', array($this, 'PhpswSESPhpmailerInit'));	
	add_filter('wp_mail_from', array($this, 'PhpswSESMailFrom'));
	add_filter('wp_mail_from_name', array($this, 'PhpswSESMailFromName'));	
	}
}

// Adds a menu on left column inside WP admin panel.
public function PhpswSESNewAdminMenu(){
global $wp_version;
version_compare($wp_version, '3.9', '>=') ? $icon_url = 'dashicons-email-alt' : $icon_url = plugins_url('images/phpswses.png', __FILE__);
add_menu_page('PhpSword SMTP Email Setup', 'PhpSw SMTP Email Setup', 'administrator', 'phpsword-smtp-email-setup', array('PhpswSES', 'PhpswSESPluginPage'), $icon_url);
}

// Register group, section and fields
public function register_settings_and_field()
{
register_setting('PhpswSESOptions', 'PhpswSESOptions', array($this, 'PhpswSESValidateSettings'));
add_settings_section('PhpswSESSection', 'SMTP Email Setting', array($this, 'PhpswSESSectionCB'), __FILE__);

add_settings_field('PhpswSESMailer', 'Send Email Via: ', array($this, 'PhpswSESMailerSetting'), __FILE__, 'PhpswSESSection');
add_settings_field('PhpswSESHost', 'SMTP Host Name: ', array($this, 'PhpswSESHostSetting'), __FILE__, 'PhpswSESSection');
add_settings_field('PhpswSESPort', 'SMTP Port No: ', array($this, 'PhpswSESPortSetting'), __FILE__, 'PhpswSESSection');
add_settings_field('PhpswSESUser', 'SMTP Username/Email: ', array($this, 'PhpswSESUserSetting'), __FILE__, 'PhpswSESSection');
add_settings_field('PhpswSESPass', 'SMTP Password: ', array($this, 'PhpswSESPassSetting'), __FILE__, 'PhpswSESSection');

add_settings_field('PhpswSESAuthentication', 'SMTP Authentication Required? : ', array($this, 'PhpswSESAuthenticationSetting'), __FILE__, 'PhpswSESSection');
add_settings_field('PhpswSESEncryption', 'Encryption Type: ', array($this, 'PhpswSESEncryptionSetting'), __FILE__, 'PhpswSESSection');

add_settings_field('PhpswSESFromEmail', 'From Email Address: ', array($this, 'PhpswSESFromEmailSetting'), __FILE__, 'PhpswSESSection');
add_settings_field('PhpswSESFromName', 'From Name: ', array($this, 'PhpswSESFromNameSetting'), __FILE__, 'PhpswSESSection');
}

// PhpswSESSection callback function
public function PhpswSESSectionCB() { }

// Validate submitted settings and options
public function PhpswSESValidateSettings($PhpswSESOptions)
{

if(!isset($_POST['PhpswAdminSESFormSubmit']) && !$_POST['PhpswAdminSESFormSubmit'] == 'yep'){
exit;
} // End: PhpswAdminSESFormSubmit

if(!check_admin_referer('PhpswSESNonceSubmitted', 'PhpswSESNonceField')){ exit; }

$PhpswSESOptions['PhpswSESVersion'] = $this->PhpswSESOptions['PhpswSESVersion'];
$PhpswSESOptions['PhpswSESVersionType'] = $this->PhpswSESOptions['PhpswSESVersionType'];

if(isset($_POST['PhpswSESOptions']['PhpswSESMailer'])){
$PhpswSESOptions['PhpswSESMailer'] = esc_html($_POST['PhpswSESOptions']['PhpswSESMailer']);
} else { $PhpswSESOptions['PhpswSESMailer'] =  $this->PhpswSESOptions['PhpswSESMailer']; }

if(!empty($_POST['PhpswSESOptions']['PhpswSESHost'])){
$PhpswSESOptions['PhpswSESHost'] = esc_html($_POST['PhpswSESOptions']['PhpswSESHost']);
} else { $PhpswSESOptions['PhpswSESHost'] =  $this->PhpswSESOptions['PhpswSESHost']; }

if(isset($_POST['PhpswSESOptions']['PhpswSESPort'])){
$PhpswSESOptions['PhpswSESPort'] = esc_html($_POST['PhpswSESOptions']['PhpswSESPort']);
} else { $PhpswSESOptions['PhpswSESPort'] =  $this->PhpswSESOptions['PhpswSESPort']; }

if(!empty($_POST['PhpswSESOptions']['PhpswSESUser'])){
$PhpswSESOptions['PhpswSESUser'] = esc_html($_POST['PhpswSESOptions']['PhpswSESUser']);
} else { $PhpswSESOptions['PhpswSESUser'] =  $this->PhpswSESOptions['PhpswSESUser']; }

if(!empty($_POST['PhpswSESOptions']['PhpswSESPass'])){
$PhpswSESOptions['PhpswSESPass'] = esc_html($_POST['PhpswSESOptions']['PhpswSESPass']);
} else { $PhpswSESOptions['PhpswSESPass'] =  $this->PhpswSESOptions['PhpswSESPass']; }

if(isset($_POST['PhpswSESOptions']['PhpswSESAuthentication'])){
$PhpswSESOptions['PhpswSESAuthentication'] = esc_html($_POST['PhpswSESOptions']['PhpswSESAuthentication']);
} else { $PhpswSESOptions['PhpswSESAuthentication'] =  $this->PhpswSESOptions['PhpswSESAuthentication']; }

if(isset($_POST['PhpswSESOptions']['PhpswSESEncryption'])){
$PhpswSESOptions['PhpswSESEncryption'] = esc_html($_POST['PhpswSESOptions']['PhpswSESEncryption']);
} else { $PhpswSESOptions['PhpswSESEncryption'] =  $this->PhpswSESOptions['PhpswSESEncryption']; }

if(!empty($_POST['PhpswSESOptions']['PhpswSESFromEmail'])){
$PhpswSESOptions['PhpswSESFromEmail'] = esc_html($_POST['PhpswSESOptions']['PhpswSESFromEmail']);
} else { $PhpswSESOptions['PhpswSESFromEmail'] =  $this->PhpswSESOptions['PhpswSESFromEmail']; }

if(!empty($_POST['PhpswSESOptions']['PhpswSESFromName'])){
$PhpswSESOptions['PhpswSESFromName'] = esc_html($_POST['PhpswSESOptions']['PhpswSESFromName']);
} else { $PhpswSESOptions['PhpswSESFromName'] =  $this->PhpswSESOptions['PhpswSESFromName']; }

return $PhpswSESOptions;

}

// Send Email via field
public function PhpswSESMailerSetting(){
echo '<input type="radio" name="PhpswSESOptions[PhpswSESMailer]" value="smtpMail"';
if(isset($this->PhpswSESOptions['PhpswSESMailer']) && $this->PhpswSESOptions['PhpswSESMailer'] == 'smtpMail')
{ echo ' checked '; }
echo '/>&nbsp; SMTP Email';
echo '&nbsp; &nbsp;';
echo '<input type="radio" name="PhpswSESOptions[PhpswSESMailer]" value="phpMail"';
if(isset($this->PhpswSESOptions['PhpswSESMailer']) && $this->PhpswSESOptions['PhpswSESMailer'] == 'phpMail')
{ echo ' checked '; }
echo '/>&nbsp; PHP Mail Function';
}

// SMTP host name field
public function PhpswSESHostSetting(){
echo '<input type="text" name="PhpswSESOptions[PhpswSESHost]" id="PhpswSESOptions[PhpswSESHost]" class="regular-text" value="';
if(!empty($this->PhpswSESOptions['PhpswSESHost'])){ echo esc_html($this->PhpswSESOptions['PhpswSESHost']); }
echo '" placeholder="smtpout.secureserver.net" />';
}

// SMTP port no field
public function PhpswSESPortSetting(){
echo '<select name="PhpswSESOptions[PhpswSESPort]">';
echo '<option value=""';
if(!empty($this->PhpswSESOptions['PhpswSESPort']) && $this->PhpswSESOptions['PhpswSESPort']==''){ echo ' selected '; }
echo '>Select SMTP Port No</option>';
echo '<option value="25"';
if(!empty($this->PhpswSESOptions['PhpswSESPort']) && $this->PhpswSESOptions['PhpswSESPort']=='25'){ echo ' selected '; }
echo '>25</option>';
echo '<option value="26"';
if(!empty($this->PhpswSESOptions['PhpswSESPort']) && $this->PhpswSESOptions['PhpswSESPort']=='26'){ echo ' selected '; }
echo '>26</option>';
echo '<option value="80"';
if(!empty($this->PhpswSESOptions['PhpswSESPort']) && $this->PhpswSESOptions['PhpswSESPort']=='80'){ echo ' selected '; }
echo '>80</option>';
echo '<option value="465"';
if(!empty($this->PhpswSESOptions['PhpswSESPort']) && $this->PhpswSESOptions['PhpswSESPort']=='465'){ echo ' selected '; }
echo '>465</option>';
echo '<option value="587"';
if(!empty($this->PhpswSESOptions['PhpswSESPort']) && $this->PhpswSESOptions['PhpswSESPort']=='587'){ echo ' selected '; }
echo '>587</option>';
echo '</select>';
}

// SMTP username field
public function PhpswSESUserSetting(){
echo '<input type="text" name="PhpswSESOptions[PhpswSESUser]" id="PhpswSESOptions[PhpswSESUser]" class="regular-text" value="';
if(!empty($this->PhpswSESOptions['PhpswSESUser'])){ echo esc_html($this->PhpswSESOptions['PhpswSESUser']); }
echo '" placeholder="login@example.com" />';
}

// SMTP password field
public function PhpswSESPassSetting(){
echo '<input type="text" name="PhpswSESOptions[PhpswSESPass]" id="PhpswSESOptions[PhpswSESPass]" class="regular-text" value="';
if(!empty($this->PhpswSESOptions['PhpswSESPass'])){ echo esc_html($this->PhpswSESOptions['PhpswSESPass']); }
echo '" placeholder="password" />';
}

// SMTP authentication field
public function PhpswSESAuthenticationSetting(){
echo '<input type="radio" name="PhpswSESOptions[PhpswSESAuthentication]" value="yes"';
if(isset($this->PhpswSESOptions['PhpswSESAuthentication']) && $this->PhpswSESOptions['PhpswSESAuthentication'] == 'yes')
{ echo ' checked '; }
echo '/>&nbsp; Yes';
echo '&nbsp; &nbsp;';
echo '<input type="radio" name="PhpswSESOptions[PhpswSESAuthentication]" value="no"';
if(isset($this->PhpswSESOptions['PhpswSESAuthentication']) && $this->PhpswSESOptions['PhpswSESAuthentication'] == 'no')
{ echo ' checked '; }
echo '/>&nbsp; No';
}

// SMTP encryption field
public function PhpswSESEncryptionSetting(){
echo '<input type="radio" name="PhpswSESOptions[PhpswSESEncryption]" value="ssl"';
if(isset($this->PhpswSESOptions['PhpswSESEncryption']) && $this->PhpswSESOptions['PhpswSESEncryption'] == 'ssl')
{ echo ' checked '; }
echo '/>&nbsp; Use SSL Encryption';
echo '&nbsp; &nbsp;';
echo '<input type="radio" name="PhpswSESOptions[PhpswSESEncryption]" value="tls"';
if(isset($this->PhpswSESOptions['PhpswSESEncryption']) && $this->PhpswSESOptions['PhpswSESEncryption'] == 'tls')
{ echo ' checked '; }
echo '/>&nbsp; Use TLS encryption';
}

// SMTP email from field
public function PhpswSESFromEmailSetting(){
echo '<input type="text" name="PhpswSESOptions[PhpswSESFromEmail]" id="PhpswSESOptions[PhpswSESFromEmail]" class="regular-text" value="';
if(!empty($this->PhpswSESOptions['PhpswSESFromEmail'])){ echo esc_html($this->PhpswSESOptions['PhpswSESFromEmail']); }
echo '" placeholder="youremail@example.com" />';
}

// SMTP email from name field
public function PhpswSESFromNameSetting(){
echo '<input type="text" name="PhpswSESOptions[PhpswSESFromName]" id="PhpswSESOptions[PhpswSESFromName]" class="regular-text" value="';
if(!empty($this->PhpswSESOptions['PhpswSESFromName'])){ echo esc_html($this->PhpswSESOptions['PhpswSESFromName']); }
echo '" placeholder="From Email Name" />';
}

// Display plugin inside admin panel
public function PhpswSESPluginPage(){
$PhpswSESOptions = get_option('PhpswSESOptions');
?>
<div id="wrap">
<h2>PhpSword SMTP Email Setup version <?php echo $PhpswSESOptions['PhpswSESVersion']; ?></h2>
<form action="options.php" method="post" id="PhpswSESForm">
<?php
PhpswSES::PhpswUpdateMessage();
settings_fields('PhpswSESOptions');
do_settings_sections(__FILE__);
wp_nonce_field( 'PhpswSESNonceSubmitted','PhpswSESNonceField' );
?>
	<p><input type="hidden" name="PhpswAdminSESFormSubmit" value="yep" /></p>
	<p><input type="submit" name="submit" class="button-primary" value="Save Changes" /></p>
</form>
<br /><hr />
<p><strong>Thank you for using PhpSword SMTP Email Setup WordPress plugin.</strong></p>
<p>Share your experience by rating the plugin. Provide your valuable feedback and suggestions to improve the quality of this plugin.</p>
<p>Browse and install more Free WordPress Plugins for your website.</p>
</div> <!-- End: wrap -->
<?php
}

public function PhpswSESPhpmailerInit($phpmailer) {
	$phpmailer->isSMTP();
	if(!empty($this->PhpswSESOptions['PhpswSESHost']) && !empty($this->PhpswSESOptions['PhpswSESPort']) && !empty($this->PhpswSESOptions['PhpswSESEncryption'])){
	
		$phpmailer->Host 		= $this->PhpswSESOptions['PhpswSESHost'];    
		$phpmailer->Port 		= $this->PhpswSESOptions['PhpswSESPort'];
		$phpmailer->SMTPSecure 	= $this->PhpswSESOptions['PhpswSESEncryption'];
		
		if($this->PhpswSESOptions['PhpswSESAuthentication']=='no'){
		$phpmailer->SMTPAuth 	= false;
		} else {
		$phpmailer->SMTPAuth 	= true; // require username and password to authenticate
		$phpmailer->Username 	= $this->PhpswSESOptions['PhpswSESUser'];
		$phpmailer->Password 	= $this->PhpswSESOptions['PhpswSESPass'];
		
		}	
	}
}

public function PhpswSESMailFrom(){
return $this->PhpswSESOptions['PhpswSESFromEmail'];
}

public function PhpswSESMailFromName(){
return $this->PhpswSESOptions['PhpswSESFromName'];
}

// Update message
public function PhpswUpdateMessage(){
	if($_GET['page'] == 'phpsword-smtp-email-setup' && ($_GET['updated'] == 'true' || $_GET['settings-updated'] == 'true')){
	?>
	<div id="setting-error-settings_updated" class="updated settings-error">
		<p><strong>Settings saved.</strong></p>
	</div>
	<?php
	}
}

} // End: class PhpswSES


// Save default values on plugin activation
function PhpswSESActivation(){
	$admin_email = get_option('admin_email');
	$sitename = get_bloginfo('name');
update_option('PhpswSESOptions', array('PhpswSESVersion' => '1.0', 'PhpswSESVersionType' => 'free', 'PhpswSESMailer' => 'phpMail', 'PhpswSESHost' => '', 'PhpswSESPort' => '25', 'PhpswSESUser' => '', 'PhpswSESPass' => '', 'PhpswSESAuthentication' => 'yes', 'PhpswSESEncryption' => 'tls', 'PhpswSESFromEmail' => $admin_email, 'PhpswSESFromName' => $sitename));
}
register_activation_hook(__FILE__, 'PhpswSESActivation');

// Initialize the class
function PhpswSESInit(){ new PhpswSES(); }

if(is_admin()){
// Call function to initialize class at back-end
add_action('admin_init', 'PhpswSESInit');
// Call function to add new plugin menu
add_action('admin_menu', array('PhpswSES', 'PhpswSESNewAdminMenu'));
} else {
// Call function to initialize class at front-end
add_action('init', 'PhpswSESInit');
}

?>