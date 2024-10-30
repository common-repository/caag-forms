<?php
/**
 * @package caag-forms
 * @version 2.0.4
 */
/*
Plugin Name: Caag Forms
Description: Use this plugin to easily add your Caag Software Forms into your Wordpress Website
Author: Caag Software
Text Domain: caag-forms
Version: 2.0.4
Author URI: https://www.caagsoftware.com/
*/

/*
 * Global Attributtes
 */
define('CAAG_FORMS_VERSION','2.0.4');

/*
 * Plugin Options
 */
define('CAAG_FORMS_TENANT_TOKEN','caag_forms_tenant_token');
define('CAAG_FORMS_USER_TOKEN','caag_forms_user_token');
define('CAAG_FORMS_USER_API_BASE_URL', 'caag_forms_user_api_base_url');

/*
 * Require Plugin Files
 */
require_once('modules/init.php');

/*
 * Install Plugin
 */
function caag_forms_install()
{
    add_caag_forms_setting_options();
}
register_activation_hook(__FILE__,'caag_forms_install');

/*
 * Deactivation Function
 */
function caag_forms_deactivate()
{
	//Nothing To Do
}
register_deactivation_hook(__FILE__,'caag_forms_deactivate');