<?php
/*
Plugin Name: Simplicy Random Post
Plugin URI: http://www.naxialis.com
Description: Afficher des articles al&eacute;atoire dans votre sidebar.
Version: 1.5
Author: naxialis
Author URI: http://www.naxialis.com
*/
load_plugin_textdomain('SP-Random-Post', false, basename(dirname(__FILE__)) . '/lang');
require_once(dirname(__FILE__).'/func/function.php');
/* Register our stylesheet. */
wp_enqueue_style('simplicy_random_post', '/wp-content/plugins/simplicy-random-post/css/simplicy_random_post.css');
?>