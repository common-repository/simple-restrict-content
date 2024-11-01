<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if(!class_exists('SRestrictContent_CORE')) {
    class SRestrictContent_CORE {
        function __construct(){
            add_action('add_meta_boxes', array($this, 'meta_box_roles_posts'));
            add_action('save_post', array($this, 'save_post'));
            add_action( 'the_post', array($this, 'the_post') );
        }
        function the_post($post){
            if(!is_admin()) {
                $restrict = get_post_meta($post->ID, '_src_available_for', true);
                if($restrict) {
                    if(is_user_logged_in()) {
                        $user = wp_get_current_user();
                        $roles = ( array ) $user->roles;
                        $difference = array_diff($restrict, $roles);
                        $count = false;
                        foreach($restrict as $rol) {
                            if(in_array($rol, $roles)) {
                                $count = true;
                                break;
                            }
                        }
                        if (!$count) {
                            status_header( 404 );
                            nocache_headers();
                            wp_safe_redirect(home_url('404'));
                            die();
                        }
                    }else{
                        
                    }
                }
            }
        }

        function save_post($postID){
            if(isset($_POST['_src_available_for'])) {

                // Sanitize POST values
                if(isset($_POST['_src_available_for']) and is_array($_POST['_src_available_for'])) {
                    // Sanitize roles
                    $allowRolesSRC = array_map('sanitize_text_field', $_POST['_src_available_for']);
                    update_post_meta($postID, '_src_available_for', $allowRolesSRC );
                }
            }else{
                // Update roles this post => false
                update_post_meta($postID, '_src_available_for', false);
            }
        }

        function meta_box_roles_posts(){
            add_meta_box( 'roles_post_metabox', __('Visible for','simple-restrict-content'), array($this, 'show_metabox_select_roles'),
                null, 'normal', 'high',
                array(
                    '__block_editor_compatible_meta_box' => true,
                    '__back_compat_meta_box' => false
                ));
        }

        function show_metabox_select_roles() {
            require_once  SRestrictContent_PATH . 'views/roles.php';
        }
    }

    $destinosRolesPosts = new SRestrictContent_CORE();
}