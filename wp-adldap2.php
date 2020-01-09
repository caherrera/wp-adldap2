<?php
/**
 * Plugin Name: WP Adldap2
 * Plugin URI: https://github.com/caherrera/wp-adldap2
 * Description: Sync your Active Directory and your Wordpress site using adldap2 library
 * Version: 1.0
 * Author: Carlos Herrera
 * Author URI: https://www.linkedin.com/in/carlosherreracaceres/
 */
if ( ! is_dir( __DIR__ . '/vendor' ) ) {
	wp_die( 'must run composer install on ' . __DIR__ . ' folder' );

}
require_once __DIR__ . '/vendor/autoload.php';
define( 'WPADLDAP2', 'WPADLDAP2' );
define( 'WPADLDAP2_DIR', __DIR__ );
define( 'WPADLDAP2_PLUGIN', __FILE__ );


if ( is_admin() ) {
	\WpAdldap2\Admin\Init::factory();
}
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	\WpAdldap2\Command::factory()->register();
	add_filter( 'bp_xprofile_set_field_data_pre_validate', '\WpAdldap2\Filters\BpXprofileSetFieldDataPreValidate::filter', 10, 3 );
}