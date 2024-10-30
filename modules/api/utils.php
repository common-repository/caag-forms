<?php

/*
 * Retrieves all caag_forms posts
 * @return Array / WP_POST
 */
function caag_forms_get_all_forms_posts()
{
	$args = array(
		'post_per_page'     =>  -1,
		'post_type'         =>  CAAG_FORMS_CUSTOM_POST_TYPE
	);
	$query = new WP_Query( $args );
	return $query->posts;
}

/*
 * Retrieves caag_form by Id
 * @param int id
 * @return Array / WP_POST
 */
function caag_forms_get_form_by_post_id($id)
{
	return get_post($id);
}


/*
 * Retrieves a caag_forms count
 *
 * @return stdClass
 */
function get_caag_forms_count()
{
	return wp_count_posts(CAAG_FORMS_CUSTOM_POST_TYPE)->publish;
}

/*
 *Retrieves post id for a meta Id
 * @param int | meta Id
 * @return Array
 */
function caag_forms_get_form_by_caag_id($caag_id)
{
    $args = array(
        'post_type' =>  CAAG_FORMS_CUSTOM_POST_TYPE,
        'meta_query'    =>  array(
            array(
                'key'       =>  CAAG_FORMS_CAAG_ID,
                'value'     =>  $caag_id,
                'compare'   =>  '='
            )
        )
    );
    $query = new WP_Query( $args );
    $post = $query->posts[0];
    $form = new stdClass();
    $form->caag_id = get_post_meta( $post->ID, CAAG_FORMS_CAAG_ID, true );
    $form->post_id = $post->ID;
    $form->link = get_post_meta( $post->ID, CAAG_FORMS_LINK, true);
    $form->category = get_post_meta( $post->ID, CAAG_FORMS_CATEGORY, true );
    $form->title = get_post_meta( $post->ID, CAAG_FORMS_TITLE, true );
    $form->shortcode = get_post_meta( $post->ID, CAAG_FORMS_SHORTCODE, true );
    return $form;
}

/*
 * Save Plugin Settings
 * return void
 */
function caag_forms_save_settings($settings)
{
	update_option(CAAG_FORMS_TENANT_TOKEN,$settings[CAAG_FORMS_TENANT_TOKEN]);
	update_option(CAAG_FORMS_USER_TOKEN,$settings[CAAG_FORMS_USER_TOKEN]);
    update_option(CAAG_FORMS_USER_API_BASE_URL,$settings[CAAG_FORMS_USER_API_BASE_URL]);
}


/*
 * Retrieves the Tenant Token from Settings
 * @return string
 */
function caag_form_get_tenant_token()
{
	return get_option(CAAG_FORMS_TENANT_TOKEN, '');
}

/*
 * Checks if the Caag Forms Exists
 * @param int | caag id
 * @return boolean
 */
function caag_forms_form_exists($caag_id)
{
	return !empty(caag_forms_get_form_by_caag_id($caag_id));
}

/*
 * Option Introduce by the User
 * 
 */
function caag_forms_get_caag_user_settings()
{
	$settings = array(
		CAAG_FORMS_USER_TOKEN    => get_option(CAAG_FORMS_USER_TOKEN),
		CAAG_FORMS_TENANT_TOKEN  => get_option(CAAG_FORMS_TENANT_TOKEN)
	);
	return $settings;
}

/*
 * Update Caag Form Via API
 * @param WpQuery
 * @returns void
 */
//add_action('pre_get_posts', 'caag_forms_update_forms');
add_action( 'load-edit.php', 'caag_forms_update_forms' );
function caag_forms_update_forms()
{
    $get_data = $_GET;
    if(!empty($get_data['post_type']) and $get_data['post_type'] == CAAG_FORMS_CUSTOM_POST_TYPE){
        $results = caag_forms_update_form_from_api();
        if(! $results['success'] ){
            $output = '
                    <div class="notice notice-error"> 
                        <p style="text-transform: Capitalize">Error: '.$results['message'].'</p> 
                    </div>
                    <div class="notice notice-error"> 
                        <p>Please. Check Caag Authentication Settings</p> 
                    </div>   
                    ';
            echo $output;
        }
    }
}


function caag_forms_get_api_basic_header()
{
    $args = array(
        'headers'   =>  array(
            'Authorization' =>  'Basic ' . base64_encode( get_option(CAAG_FORMS_TENANT_TOKEN) . ':' . get_option(CAAG_FORMS_USER_TOKEN))
        )
    );
    return $args;
}

function caag_forms_exists($caag_id)
{
    $args = array(
        'post_type' =>  CAAG_FORMS_CUSTOM_POST_TYPE,
        'meta_query'        =>      array(
            array(
                'key'       =>  CAAG_FORMS_CAAG_ID,
                'value'     =>  $caag_id,
                'compare'   =>  '='

            )
        )
    );
    $query = new WP_Query( $args );
    return ! empty( $query->posts );
}

/*
 * Update Routine
 */
function caag_forms_update_form_from_api()
{
    $response = wp_remote_get( get_option(CAAG_FORMS_USER_API_BASE_URL) . CAAG_FORMS_API_ROUTE , caag_forms_get_api_basic_header());
    if(! is_wp_error($response) ){
        $caag_forms = json_decode($response['body']);
        if(empty($caag_forms->success)){
            if( !empty( $caag_forms->data ) ){
                foreach ($caag_forms->data as $form){
                    if( ! caag_forms_exists($form->id) ){
                        $args = array(
                            'post_title' => $form->label,
                            'post_status' => 'publish',
                            'post_type' => CAAG_FORMS_CUSTOM_POST_TYPE
                        );
                        $post_id = wp_insert_post($args);
                        update_post_meta($post_id, CAAG_FORMS_CAAG_ID, $form->id);
                        update_post_meta($post_id, CAAG_FORMS_LINK, $form->public_permanent_link_url);
                        update_post_meta($post_id, CAAG_FORMS_SHORTCODE, '[caag_form id="'.$form->id.'"]');
                        if(empty($form->sheet_category)){
                            update_post_meta($post_id, CAAG_FORMS_CATEGORY, 'General');
                        }else{
                            update_post_meta($post_id, CAAG_FORMS_CATEGORY, $form->sheet_category);
                        }
                    }else{
                        $post = caag_forms_get_form_by_caag_id($form->id);
                        update_post_meta($post->ID, CAAG_FORMS_CAAG_ID, $form->id);
                        update_post_meta($post->ID, CAAG_FORMS_LINK, $form->public_permanent_link_url);
                        update_post_meta($post->ID, CAAG_FORMS_SHORTCODE, '[caag_form id="'.$form->id.'"]');
                        if(empty($form->sheet_category)){
                            update_post_meta($post->ID, CAAG_FORMS_CATEGORY, 'General');
                        }else{
                            update_post_meta($post->ID, CAAG_FORMS_CATEGORY, $form->sheet_category);
                        }
                    }
                }
            }
            return array(
                'success'   =>  true
            );
        }else{
            return array(
                'success'   =>  false,
                'message'   =>  $caag_forms->message
            );
        }
    }else{
        return array(
            'success'   =>  false,
            'message'   =>  'There was an Error Updating the Information... Please Try Again'
        );
    }
}
