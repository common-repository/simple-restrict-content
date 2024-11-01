<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wp_roles, $post;

if ( !isset( $wp_roles ) ) {
    $wp_roles = new WP_Roles();
}
	$roles = $wp_roles->get_names();
    $availableRoles = get_post_meta($post->ID, '_src_available_for', true);
	foreach ($roles as $role_value => $role_name) {
        $checked = '';
        if($availableRoles && in_array($role_value, $availableRoles)) {
            $checked = 'checked="checked"';
        }
		echo '<p><input type="checkbox" name="_src_available_for[]" value="' . $role_value . '" '.$checked.'>'.translate_user_role($role_name).'</p>';
  	}


?>