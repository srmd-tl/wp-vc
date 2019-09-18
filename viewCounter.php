<?php
/**
 * Post View Counter 
 *
 *
 * @package           viewCounter
 * @author            Sarmad Sohail
 * @copyright         2019 Sarmad Sohail
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:  Post View Counter
 * Plugin URI:        https://localhost.com
 * Description:       Get the post views on custom time intervals.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sarmad Sohail
 * Author URI:        https://example.com
 * Text Domain:       plugin-slug
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

//Add On sidebar
add_action( 'wp_loaded', 'vc_plugin_top_menu' );
//

//Add Script on every post
add_filter( 'the_content', 'my_the_content_filter' );
//

 function my_the_content_filter($content)
{

    $url=plugins_url()."/viewCounter/ajax/StoreCounter.php";

    $current_page = sanitize_post( $GLOBALS['wp_the_query']->get_queried_object() );
    // Get the page slug
    $slug = $current_page->ID??null;
    //Get Visitied Post Parameters
    $params=$slug??null;
    //For how long user should remain on the page
    $time=get_user_meta( 1,"vc_timer",true)*1000??12000;
    // $time=$_ENV["COUNT_TIME"];

    if($params)
    {
            $content.=
         "<script>
            function addViewToPost(){
                jQuery.post( '$url',{post:'$params'})
                  .done(function( data ) {
                  
                  });
            }

              setTimeout(
            function() {
                addViewToPost();
            }, $time);
            </script>";
                return $content;
    }
 
}

 function viewCounter_active()
{
	add_action( 'admin_menu', 'vc_plugin_top_menu');
    // add_action( 'admin_menu', 'wporg_options_page' );
    databse_create();
}
//Html for plugin
function vc_options_page_html() {
    // check user capabilities
    if ( ! current_user_can( 'read' ) ) {

        return;
    }
   
    include_once("resources/views/posts.php");

}
//
//Html for Plugin Configurations
function vc_config_page_html()
{
   // check user capabilities
    if ( ! current_user_can( 'read' ) ) {

        return;
    }
   
    include_once("resources/views/configs.php");
}
//End Html for Plugin Configurations

 function vc_plugin_top_menu(){ 

 add_menu_page(
        'VC',//$page_title
        'ViewCounter',//$menu_title
        'read',//$capability
        'vc',//menu_slug
        'vc_options_page_html',//function
        plugin_dir_url(__FILE__) . 'images/icon_wporg.png',//icon_url
        20//position
    );
    add_action('admin_menu','addConfigSubmenuInSettings');
    // add_action('admin_menu','addSubmenusForAuthors');

 }

//Add Submenus
  function addSubmenusForAuthors()
 {
    $authors = get_users( [ 'role__in' => [ 'author','administrator' ] ] );   
       foreach ($authors as $author) { 
        add_submenu_page(
        'vc', //Parent
       'custom', //Page Title
        $author->display_name, //Menu Title
        'read',//capability
          'custom_'.$author->ID, 
         'vc_options_page_html');
    }
 }
// End Add Submenus


//Add Configuration Submenus
 function addConfigSubmenuInSettings()
 {
      add_submenu_page(
        'options-general.php', //Parent
       'config', //Page Title
        'Add Timer Config', //Menu Title
        'manage_options',//capability
          'vc_config', 
         'vc_config_page_html');

 }
//End Add Configuration Submenus

//Database Migration

global $vc_db_version;
$vc_db_version = '1.0';
function databse_create() {
    global $wpdb;
    global $vc_db_version;

    $table_name = $wpdb->prefix . 'ips';
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        created_at date  NOT NULL,
        ip tinytext NOT NULL,
        post_id mediumint(9) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( 'vc_db_version', $vc_db_version );
}
register_activation_hook( __FILE__, 'viewCounter_active' );


?>