<?php
/**
 * Plugin Name: Simple Restrict Content
 * Description: Restrict the content of all WordPress, just select the roles that can access that post, page, custom post type, product ... etc.
 * Version: 1.0.0
 * Donate link: https://paypal.me/taxarpro
 * Author: TaxarPro
 * Author URI: https://taxarpro.com
 * Text Domain: simple-restrict-content
 * Domain Path: /languages
 * WC tested up to: 2.0.0
 * Tested WP: 5.5.1
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if(!defined('SRestrictContent_PATH')) {
    define('SRestrictContent_PATH', plugin_dir_path( __FILE__ ));
}

require_once(SRestrictContent_PATH . 'classes/class.roles.post.php');

add_action( 'pre_get_posts' , 'SRestrictContent_filter_query' );
if(!function_exists('SRestrictContent_filter_query')){

    function SRestrictContent_filter_query( $query ) {
      if(!is_admin()) {
              if(is_user_logged_in()) {
                      $user = wp_get_current_user();
                      $roles = ( array ) $user->roles;
                      if( $query->is_main_query() ) {

                          $meta_query = array('relation' => 'OR');
                          foreach ($roles as $rol) {
                              $meta_query[] = array(
                                  'key'       => '_src_available_for',
                                  'value'     => $rol,
                                  'compare'   => 'LIKE',
                              );
                          }

                          $meta_query[] = array(
                              'key'       => '_src_available_for',
                              'compare'   => 'NOT EXISTS',
                          );

                          $meta_query[] = array(
                              'key'       => '_src_available_for',
                              'value'     => '',
                          );
                          $query->set('meta_query', $meta_query);
                      }
              }else{
                  if( $query->is_main_query() ) {
                      $query->set( 'meta_query', array(
                          'relation' => 'OR',
                          array(
                            'key' => '_src_available_for',
                            'compare' => 'NOT EXISTS'
                          ),
                          array(
                              'key' => '_src_available_for',
                              'value' => null
                          )
                        )
                      );
                  }
              }
      }
    }
}