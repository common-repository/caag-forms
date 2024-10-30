<?php
/*
 * Register Caag Form Shortcode
 * @param Array
 * return string | html
 */

function caag_form_shortcode($atts = [])
{
    caag_forms_scripts();
	$form = caag_forms_get_form_by_caag_id($atts['id']);
    $output = '';
    $output .= '<div id="caag-form">
                    <iframe id="caag-iframe" src="' . $form->link . '">
                    </iframe>
                </div>';
    return $output;
}
add_shortcode('caag_form','caag_form_shortcode');
