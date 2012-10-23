<?php
/*
Plugin Name: Simplicy Random Post
Plugin URI: http://www.naxialis.com/simplicy-random-post
Description: Afficher des articles al&eacute;atoire dans votre sidebar.
Version: 1.7
Author: naxialis
Author URI: http://www.naxialis.com
*/
require_once(dirname(__FILE__).'/func/function.php');
/* Register our stylesheet. */
wp_enqueue_style('simplicy_random_post', '/wp-content/plugins/simplicy-random-post/css/simplicy_random_post.css');
/* Register our stylesheet Admin. */
wp_enqueue_style('admin', '/wp-content/plugins/simplicy-random-post/css/admin.css');

// Excerpt length filter
	 function simplicy_random_content($limit) {
     $excerpt = explode(' ', get_the_excerpt(), $limit);
      if (count($excerpt)>=$limit) {
        array_pop($excerpt);
        $excerpt = implode(" ",$excerpt).'';
      } else {
        $excerpt = implode(" ",$excerpt);
      } 
      $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
	  
	 
	  
      return $excerpt;
    }
?>