<?php
function WARP__errorpost($ErrMsg) {
    header('HTTP/1.0 500 Internal Server Error');
	header('Content-Type: text/plain;charset=UTF-8');
    echo $ErrMsg;
    exit;
}
function WARP__init(){
	if($_GET['action'] == 'SPrandompost'){
		$jsonArr=array();
		$_num=$_GET['number'];
		$_num_year=$_GET['year'];
		$_num_cat=$_GET['cat'];
		$_num_month=$_GET['month'];
		$_thumbsrand=$_GET['thumbsrand'];
		$_thumbsaffiche=$_GET['thumbsaffiche'];
		$_excerpt=$_GET['excerpt'];
		$_length=$_GET['length'];
		$_auto=$_GET['auto'];
		$_time=$_GET['time'];
		echo WARP_sp1_Random_posts("number=$_num&cat=$_num_cat&year=$_num_year&month=$_num_month&thumbsaffiche=$_thumbsaffiche&thumbsrand=$_thumbsrand&excerpt=$_excerpt&length=$_length&auto=$_auto&time=$_time");
		die();
	}
}
add_action('init', 'WARP__init');


	
function WARP_sp1_Random_posts($args=''){
	$defargs=array('number' => 8,'cmtcount' => 0,'month' => 0,'year' => 0,'thumbsaffiche' => 0, 'excerpt' => 0, 'length' => 100, 'auto' => 0, 'time' => 60);
	$args = wp_parse_args($args, $defargs);$output='';$number=$args['number'];$month=$args['month'];$year=$args['year'];$cat=$args['cat'];
	


	query_posts( array( 
	
	 
	'posts_per_page' => $number, 
	'orderby' => 'rand',
	'monthnum'  => $month,
	'year' => $year,
	'cat' => $cat,
	
	
	
	
	
	));

	 
	if(have_posts()){
		while ( have_posts()) :the_post();
		
			// image
			
			$thumb=vp_get_thumbs_random_url($post->post_content); 
  			$output.='<div class="SP-random-post-clear-top"></div>';	
			// image fin
			if($args['thumbsaffiche']!=0)$output.='<img  class="simplicy-random-post-img" width="'.$args['thumbsrand'].'" height="'.$args['thumbsrand'].'" src="'.$thumb.'" alt="" />';
			$output.='<li id="random-post-'.get_the_ID().'" class="simplicy-random-post"><div class="random-post-title"><a title="'.get_the_title().'" href="'.get_permalink().'">'.get_the_title().'</a>';
			
			$output.='<dd class="simplicy-date_random-post">'.get_the_date().'</dd>';
		 
			$output.='</div>';
			 
			if($args['excerpt']!=0)$output.='<div class="random-post-excerpt">'.WARP_SP_Random_posts_substr(strip_tags(get_the_content()),(int)$args['length']).'</div>';;
			$output.='</li><div class="SP-random-post"></div>';
			
			$output.='<div class="SP-random-post-clear"></div>';
			
		endwhile;
		wp_reset_query();
		$output.='<li id="random-post-more" class="simplicy-random-post" style="text-align:center"><div><a style="width:100%;height:100%" href="javascript:;" onclick="WARP_.get_random_posts(\'number='.$args['number'].'&month='.$args['month'].'&year='.$args['year'].'&cat='.$args['cat'].'&thumbsaffiche='.$args['thumbsaffiche'].'&excerpt='.$args['excerpt'].'&length='.$args['length'].'&thumbsrand='.$args['thumbsrand'].'&auto='.$args['auto'].'&time='.$args['time'].'\')"><div class="icon-reload"></div></a>'.($args['auto']?'<span id="refreshTime">'.$args['time'].'</span>':'').'</div></li>';
		return $output;
	}else{
		WARP__errorpost(__('Il n&acute;ya pas d&acute;articles.','SP-random-Post'));
	}
}

function SP_load_Random_Posts($args=''){
	echo '<ul id="wp-random-posts">'.WARP_sp1_Random_posts($args).'</ul>';
	
}
function WARP_SP_Random_posts_substr($str,$length){
		$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
		preg_match_all($pa, $str, $t_str);
		if(count($t_str[0]) > $length) {
			$ellipsis = '...';
			$str = join('', array_slice($t_str[0], 0, $length)) . $ellipsis;
		}
		return $str;
}

function WARP__addScript(){
	$script = '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/simplicy-random-post/js/sp-random-post.js"></script>';
	echo $script;
}
if(get_option("SP-Random-files")!='1')add_action ('wp_head', 'WARP__addScript');
else add_action ('wp_footer', 'WARP__addScript');
class WARP__widget extends WP_Widget{
	function WARP__widget(){
		$widget_ops = array('classname' => 'SP-random-Post', 'description' => __( 'Afficher des articles aléatoire avec miniature dans votre sidebar.', 'SP-Random-Post') );		
		$control_ops = array('width' => 350, 'height' => 300);		
		$this->WP_Widget('SP-random-Post', __('Simplicy Random posts'), $widget_ops, $control_ops);
	}
	function form($instance){
		$instance = wp_parse_args((array)$instance,array(
		'title'=>__('Articles au hazard', 'Simplicy-Random-Posts'),
		'number'=>8,
		'year'=>0,
		'cat'=>0,
		'month'=>0,
		'thumbsaffiche'=>false,
		'excerpt'=>false,
		'length'=>35,
		'auto'=>false,
		'time'=>60));
		echo '<p><label for="'.$this->get_field_name('title').'">'.__('Titre du Widget', 'SP-Random-Post').'<input style="width:265px;" name="'.$this->get_field_name('title').'" type="text" value="'.htmlspecialchars($instance['title']).'" /></label></p>';
		echo '<p><label for="'.$this->get_field_name('number').'">'.__('Nombre d&acute;article al&eacute;atoire', 'SP-Random-Post').'<input style="width:40px;float: right;" name="'.$this->get_field_name('number').'" type="text" value="'.htmlspecialchars($instance['number']).'" /></label></p>';
		
// Mois de publication
echo '<p><label for="'.$this->get_field_name('month').'">'.__('Ici vous pouvez indiquer le mois de publication', 'SP-Random-Post').'<input style="width:40px;float: right;" name="'.$this->get_field_name('month').'" type="text" value="'.htmlspecialchars($instance['month']).'" /></label></p>';
//fin

// Année de publication
echo '<p><label for="'.$this->get_field_name('year').'">'.__('Ici vous pouvez indiquer l&acute;ann&eacute;e de publication', 'SP-Random-Post').'<input style="width:40px;float: right;" name="'.$this->get_field_name('year').'" type="text" value="'.htmlspecialchars($instance['year']).'" /></label></p>';
//fin

?>
<p>
			<label>
				<?php _e( 'Categorie' ); ?>:
				<?php wp_dropdown_categories( array( 'show_option_all' => __('ALL'),'hide_empty'  => 0,'name' => $this->get_field_name("cat"), 'selected' => $instance["cat"] ) ); ?>
			</label>
		</p>
<?php
// image
	echo '<p><input style="" name="'.$this->get_field_name('thumbsaffiche').'" type="checkbox" value="checkbox" ';if($instance['thumbsaffiche'])echo 'checked="checked"';echo '/><label for="'.$this->get_field_name('thumbsaffiche').'">'.__('Voulez-vous afficher une vignette ?', 'SP-Random-Post').'</label></p>';
	
echo '<p><label for="'.$this->get_field_name('thumbsrand').'">'.__('Taille de la Vignette', 'SP-Random-Post').'<input style="width:40px;float: right;" name="'.$this->get_field_name('thumbsrand').'" type="text" value="'.htmlspecialchars($instance['thumbsrand']).'" /></label></p>';
// image fin
		echo '<p><input style="" name="'.$this->get_field_name('excerpt').'" type="checkbox" value="checkbox" ';if($instance['excerpt'])echo 'checked="checked"';echo '/><label for="'.$this->get_field_name('excerpt').'">'.__('Afficher un extrait ?', 'SP-Random-Post').'</label></p>';

		echo '<p><label for="'.$this->get_field_name('length').'">'.__('Longueur de l&acute;extrait :', 'SP-Random-Post').'<input style="width:40px;float: right;" name="'.$this->get_field_name('length').'" type="text" value="'.htmlspecialchars($instance['length']).'" /></label></p>';
		echo '<p><input style="" name="'.$this->get_field_name('auto').'" type="checkbox" value="checkbox" ';if($instance['auto'])echo 'checked="checked"';echo '/><label for="'.$this->get_field_name('auto').'">'.__('actualisation automatique?', 'SP-Random-Post').'</label></p>';
		echo '<p><label for="'.$this->get_field_name('time').'">'.__('Intervalle en seconde de actualisation automatique', 'SP-Random-Post').'<input style="width:40px;" name="'.$this->get_field_name('time').'" type="text" value="'.htmlspecialchars($instance['time']).'" /></label></p>';
	}
	function update($new_instance,$old_instance){
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['number'] = (int)$new_instance['number'];
		$instance['month'] = (int)$new_instance['month'];
		$instance['cat'] = (int)$new_instance['cat'];
		$instance['year'] = (int)$new_instance['year'];
		$instance['thumbsrand'] = (int)$new_instance['thumbsrand'];
		$instance['thumbsaffiche'] = (bool)$new_instance['thumbsaffiche'];
		$instance['excerpt'] = (bool)$new_instance['excerpt'];
		$instance['length'] = (int)$new_instance['length'];
		$instance['auto'] = (bool)$new_instance['auto'];
		$instance['time'] = (int)$new_instance['time'];
		return $instance;
	}
	function widget($args,$instance){
		extract($args);
		$myargs='number='.$instance['number'].'&thumbsrand='.((int)$instance['thumbsrand']).'&thumbsaffiche='.((int)$instance['thumbsaffiche']).'&excerpt='.((int)$instance['excerpt']).'&length='.(int)$instance['length'].'&month='.(int)$instance['month'].'&year='.(int)$instance['year'].'&cat='.(int)$instance['cat'].'&auto='.((int)$instance['auto']).'&time='.(int)$instance['time'];
		$title = apply_filters('widget_title',empty($instance['title']) ? __('Random Posts', 'SP-Random-Post') : $instance['title']);
		echo '<li id="random-post-widget" class="widget"><h3 class="widget-title" >'.$title.'</h3>';
		SP_load_Random_Posts($myargs);
		echo '</li>';
	}
}

// ******************************************************** fonction image ***************************************************************
 

function vp_get_thumbs_random_url($text)
{
  global $post;
 
  $imageurl="";        
 
  // extract the thumbnail from attached imaged
  $allimages =&get_children('post_type=attachment&post_mime_type=image&post_parent=' . $post->ID );        
 
  foreach ($allimages as $img){                
     $img_src = wp_get_attachment_image_src($img->ID);
     break;                       
  }
 
  $imageurl=$img_src[0];
 
 
  // try to get any image
  if (!$imageurl)
  {
    preg_match('/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'>]*)/i' ,  $text, $matches);
    $imageurl=$matches[1];
  }
 
  // try to get youtube video thumbnail
  if (!$imageurl)
  {
    preg_match("/([a-zA-Z0-9\-\_]+\.|)youtube\.com\/watch(\?v\=|\/v\/)([a-zA-Z0-9\-\_]{11})([^<\s]*)/", $text, $matches2);
 
    $youtubeurl = $matches2[0];
    if ($youtubeurl)
     $imageurl = "http://i.ytimg.com/vi/{$matches2[3]}/1.jpg"; 
   else preg_match("/([a-zA-Z0-9\-\_]+\.|)youtube\.com\/(v\/)([a-zA-Z0-9\-\_]{11})([^<\s]*)/", $text, $matches2);
 
   $youtubeurl = $matches2[0];
   if ($youtubeurl)
     $imageurl = "http://i.ytimg.com/vi/{$matches2[3]}/1.jpg"; 
  }
 
 
return $imageurl;
}

function WARP__widget_init(){
	register_widget('WARP__widget');
}
add_action('widgets_init','WARP__widget_init');
?>