<?php


/*
* Hook Function to Create Setting Submenu
*/
add_action('admin_menu', 'caag_forms_setting_menu');
function caag_forms_setting_menu()
{
	add_options_page(
		'Caag Forms Settings',
		'Caag Forms',
		'manage_options',
		'caag',
		'caag_forms_menu_setting_html'
	);
}
/*
 * Html for Setting Screen
 * @return void
 */
function caag_forms_menu_setting_html()
{
	caag_forms_scripts();
	$settings = caag_forms_get_caag_user_settings();
	$current_endpoint = get_option(CAAG_FORMS_USER_API_BASE_URL);
    define('CAAG_FORMS_NONCE', plugin_basename(__FILE__));
	?>
	<?php if(isset($success)): ?>
		<div class="message updated"><p><?php echo $success; ?></p></div>
	<?php endif; ?>
	<div class="wrap">
		<div id="wrap">
			<h1>Caag Software Authentication Access</h1>
			<div class="caag-notice-wp notice caag-notice">
				<p>Don't have an account yet? Create a new account by clicking on this link</p>
				<a href="https://caagsoftware.com/" class="caag-button caag-button-primary caag-button-external-link" target="_blank">Register Now</a>
			</div>
			<form action="" method="post">
				<table class="form-table">
					<tbody>
					<tr>
						<th><label class="wp-heading-inline" id="title" for="title">Tenant Token</label></th>
						<td> <input type="text" name="<?php echo CAAG_FORMS_TENANT_TOKEN; ?>" size="70" value="<?php echo $settings[CAAG_FORMS_TENANT_TOKEN]; ?>" id="title" spellcheck="true" autocomplete="off"></td>
					</tr>
					<tr>
						<th><label class="wp-heading-inline" id="title-prompt-text" for="title">User Token</label></th>
						<td><input type="text" name="<?php echo CAAG_FORMS_USER_TOKEN; ?>" size="70" value="<?php echo $settings[CAAG_FORMS_USER_TOKEN]; ?>" id="title" spellcheck="true" autocomplete="off"></td>
					</tr>
                    <tr>
                        <th><label class="wp-heading-inline" id="title-prompt-text" for="title">Select Api Region</label></th>
                        <td>
                            <select name="<?php echo CAAG_FORMS_USER_API_BASE_URL; ?>">
                                <option value="https://api.caagcrm.com/api/" <?php echo ($current_endpoint == 'https://api.caagcrm.com/api/') ? 'selected="selected"' : ''; ?>>America</option>
                                <option value="https://api-europe.caagcrm.com/api-europe/" <?php echo ($current_endpoint == 'https://api-europe.caagcrm.com/api-europe/') ? 'selected="selected"' : ''; ?>>Europe</option>
                                <option value="https://api-asia.caagcrm.com/api-asia/" <?php echo ($current_endpoint == 'https://api-asia.caagcrm.com/api-asia/') ? 'selected="selected"' : ''; ?>>Asia</option>
                            </select>
                        </td>
                    </tr>
					</tbody>
				</table>
				<?php wp_nonce_field( CAAG_FORMS_NONCE, 'caag_nonce' ); ?>
				<input type="submit" name="publish" id="publish" class="button button-primary button-large" value="Save">
			</form>
		</div>
	</div>
	<?php
	if(!empty($_POST) and wp_verify_nonce($_POST['caag_nonce'], CAAG_FORMS_NONCE)){
		caag_forms_save_settings(($_POST));
		$success = __('Settings were successfully saved!');
	}
	?>
	<?php if(isset($success)): ?>
		<div class="message updated"><p><?php echo $success; ?></p>
		</div>
		<script>
			document.getElementById("wrap").remove();
		</script>
	<?php endif; ?>
	<?php

}

/*
 * Create Settings Options on Plugin Install
 */
function add_caag_forms_setting_options()
{
    if( ! get_option(CAAG_FORMS_TENANT_TOKEN, false) ){
        add_option(CAAG_FORMS_TENANT_TOKEN,'');
    }
    if( ! get_option(CAAG_FORMS_USER_TOKEN, false) ){
        add_option(CAAG_FORMS_USER_TOKEN,'');
    }
    if( ! get_option(CAAG_FORMS_USER_API_BASE_URL, false) ){
        add_option(CAAG_FORMS_USER_API_BASE_URL,'');
    }
}

/*
 * Add Meta Data columns to Post Table: Link
 * Only Header and Footer
 */
add_filter('manage_'.CAAG_FORMS_CUSTOM_POST_TYPE.'_posts_columns', 'caag_forms_add_meta_columns');
function caag_forms_add_meta_columns($columns)
{
    return array(
        CAAG_FORMS_ID_COLUMN_NAME               =>  CAAG_FORMS_ID_COLUMN_NAME,
        CAAG_FORMS_TITLE_COLUMN_NAME            =>  CAAG_FORMS_TITLE_COLUMN_NAME,
        CAAG_FORMS_CATEGORY_COLUMN_NAME         =>  CAAG_FORMS_CATEGORY_COLUMN_NAME,
        CAAG_FORMS_LINK_COLUMN_NAME             =>  CAAG_FORMS_LINK_COLUMN_NAME,
        CAAG_FORMS_SHORTCODE_COLUMN_NAME        =>  CAAG_FORMS_SHORTCODE_COLUMN_NAME
    );
}

/*
 * Displaying Actual Meta Data Values
 * return @void
 */
add_action( 'manage_posts_custom_column' , 'caag_forms_fill_meta_column_link', 10, 2 );
function caag_forms_fill_meta_column_link($column_name, $post_id) {
    if ($column_name == CAAG_FORMS_ID_COLUMN_NAME) {
        if(!empty(get_post_meta($post_id, CAAG_FORMS_CAAG_ID, true))){
            echo get_post_meta($post_id, CAAG_FORMS_CAAG_ID, true);
        }else{
            echo '';
        }
    }
    if ($column_name == CAAG_FORMS_TITLE_COLUMN_NAME) {
        $post = get_post($post_id);
        echo $post->post_title;
    }
    if ($column_name == CAAG_FORMS_CATEGORY_COLUMN_NAME) {
        if(!empty(get_post_meta($post_id, CAAG_FORMS_CATEGORY, true))){
            echo get_post_meta($post_id, CAAG_FORMS_CATEGORY, true);
        }else{
            echo '';
        }
    }
    if ($column_name == CAAG_FORMS_LINK_COLUMN_NAME) {
        if(!empty(get_post_meta($post_id, CAAG_FORMS_LINK, true))){
            echo get_post_meta($post_id, CAAG_FORMS_LINK, true);
        }else{
            echo '';
        }
    }
    if ($column_name == CAAG_FORMS_SHORTCODE_COLUMN_NAME) {
        if(!empty(get_post_meta($post_id, CAAG_FORMS_SHORTCODE, true))){
            echo get_post_meta($post_id, CAAG_FORMS_SHORTCODE, true);
        }else{
            echo '';
        }
    }
}





/*
 * Make Id and Title Table Header sortable
 * @param array
 * @return array
 */
add_filter( 'manage_edit-'.CAAG_FORMS_CUSTOM_POST_TYPE.'_sortable_columns', 'caag_forms_all_sortable_columns' );
function caag_forms_all_sortable_columns( $columns )
{
    $columns[CAAG_FORMS_ID_COLUMN_NAME] = 'Identifier';
    $columns[CAAG_FORMS_TITLE_COLUMN_NAME] = 'Title';
    return $columns;
}


