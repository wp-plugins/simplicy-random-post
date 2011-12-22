<?php
/*
Plugin Name: Simplicy Random Post
Plugin URI: http://www.naxialis.com
Description: Afficher des articles al&eacute;atoire dans votre sidebar.
Version: 1.0
Author: naxialis
Author URI: http://www.naxialis.com
*/

load_plugin_textdomain('SP-Random-Post', false, basename(dirname(__FILE__)) . '/lang');
wp_enqueue_style('simplicy-random-post', '/wp-content/plugins/sp-random-post/css/simplicy-random-post.css');
require_once(dirname(__FILE__).'/func/function.php');

?>