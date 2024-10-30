<?php

/*
 * Register Assets
 * return void
 */
function caag_forms_register_scripts()
{
    wp_register_style('caag-iframe-style',plugin_dir_url( __FILE__ ) . 'css/caag.css', array(), '0.0.1' );
    wp_register_script('caag-iframe-resize', plugin_dir_url( __FILE__ ) . 'js/iframeResizer.min.js');
    wp_register_script('caag-iframe-resize-windows', plugin_dir_url( __FILE__ ) . 'js/iframeResizer.contentWindow.min.js' );
    wp_register_script('caag-iframe-init', plugin_dir_url( __FILE__ ) . 'js/caagResize.js', array( 'jquery' ), '0.0.1');
}
add_action('wp_enqueue_scripts','caag_forms_register_scripts');

/*
 * Loading Scripts
 */
function caag_forms_scripts()
{
    wp_enqueue_style('caag-iframe-style');
    wp_enqueue_script('caag-iframe-resize');
    wp_enqueue_script('caag-iframe-resize-windows');
    wp_enqueue_script('caag-iframe-init');
}