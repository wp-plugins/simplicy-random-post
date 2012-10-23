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
		$_thumb_w=$_GET['thumb_w'];
		$_thumb_h=$_GET['thumb_h'];
		$_thumbsaffiche=$_GET['thumbsaffiche'];
		$_dateaffiche=$_GET['dateaffiche'];
		$_excerpt=$_GET['excerpt'];
		$_length=$_GET['length'];
		$_auto=$_GET['auto'];
		$_time=$_GET['time'];
		echo WARP_sp1_Random_posts("number=$_num&cat=$_num_cat&year=$_num_year&month=$_num_month&thumbsaffiche=$_thumbsaffiche&dateaffiche=$_dateaffiche&thumb_w=$_thumb_w&thumb_h=$_thumb_h&excerpt=$_excerpt&length=$_length&auto=$_auto&time=$_time");
		die();
	}
}
add_action('init', 'WARP__init');


	
function WARP_sp1_Random_posts($args=''){
	$defargs=array('number' => 8,'cmtcount' => 0,'month' => 0,'year' => 0,'thumbsaffiche' => 0,'dateaffiche' => 0, 'excerpt' => 0, 'length' => 100, 'auto' => 0, 'time' => 60);
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
			
			if (has_post_thumbnail()) {
			$thumb = get_post_thumbnail_id(); $img_url = wp_get_attachment_url( $thumb,'full' ); $image = sp_post_ramdom_resize( $img_url, $args['thumb_w'], $args['thumb_h'], true ); 
  			$output.='<div class="SP-random-post-clear-top"></div>';	
			// image fin
			if($args['thumbsaffiche']!=0)$output.='<a href="'.get_permalink().'" title="'.get_the_title().'"><img  class="simplicy-random-post-img" width="'.$args['thumb_w'].'" height="'.$args['thumb_h'].'" src="'.$image.'" alt="" /></a>'; }
			$output.='<li id="random-post-'.get_the_ID().'" class="simplicy-random-post"><div class="random-post-title"><a title="'.get_the_title().'" href="'.get_permalink().'">'.get_the_title().'</a>';
			
			if($args['dateaffiche']!=0)$output.='<dd class="simplicy-date_random-post">'.get_the_date().'</dd>'; 
		 
			$output.='</div>';
			 
			if($args['excerpt']!=0)$output.='<div class="random-post-excerpt">'.simplicy_random_content((int)$args['length']).'</div>';
			$output.='</li><div class="SP-random-post"></div>';
			
			$output.='<div class="SP-random-post-clear"></div>';
			
		endwhile;
		wp_reset_query();
		$output.='<li id="random-post-more" class="simplicy-random-post" style="text-align:center"><div><a style="width:100%;height:100%" href="javascript:;" onclick="WARP_.get_random_posts(\'number='.$args['number'].'&month='.$args['month'].'&year='.$args['year'].'&cat='.$args['cat'].'&thumbsaffiche='.$args['thumbsaffiche'].'&dateaffiche='.$args['dateaffiche'].'&excerpt='.$args['excerpt'].'&length='.$args['length'].'&thumb_w='.$args['thumb_w'].'&thumb_h='.$args['thumb_h'].'&auto='.$args['auto'].'&time='.$args['time'].'\')"><div class="icon-reload"></div></a>'.($args['auto']?'<span id="refreshTime">'.$args['time'].'</span>':'').'</div></li>';
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
		'dateaffiche'=>false,
		'excerpt'=>false,
		'length'=>35,
		'auto'=>false,
		'time'=>60));
echo '<p><label for="'.$this->get_field_name('title').'">'.__('Titre <small>(Title)</small>', 'SP-Random-Post').'<input style="width:100%;" name="'.$this->get_field_name('title').'" type="text" value="'.htmlspecialchars($instance['title']).'" /></label></p>';

echo'<fieldset>
  	 <legend>Articles <small>(Posts)</small></legend>';
echo '<p><label for="'.$this->get_field_name('number').'">'.__('Nombre d&acute;article al&eacute;atoire', 'SP-Random-Post').'<input style="width:40px;float: right;" name="'.$this->get_field_name('number').'" type="text" value="'.htmlspecialchars($instance['number']).'" /></label></br><small>(Random post number)</small></p>';
		
// Mois de publication
echo '<p><label for="'.$this->get_field_name('month').'">'.__('Ici vous pouvez indiquer le mois de publication', 'SP-Random-Post').'<input style="width:40px;float: right;" name="'.$this->get_field_name('month').'" type="text" value="'.htmlspecialchars($instance['month']).'" /></label></br><small>(Here you can specify the month of publication)</small></p>';
//fin

// Année de publication
echo '<p><label for="'.$this->get_field_name('year').'">'.__('Ici vous pouvez indiquer l&acute;ann&eacute;e de publication', 'SP-Random-Post').'<input style="width:40px;float: right;" name="'.$this->get_field_name('year').'" type="text" value="'.htmlspecialchars($instance['year']).'" /></label></br><small>(Here you can enter the year of publication)</small></p>';
//fin
echo '<p><input style="" name="'.$this->get_field_name('excerpt').'" type="checkbox" value="checkbox" ';if($instance['excerpt'])echo 'checked="checked"';echo '/><label for="'.$this->get_field_name('excerpt').'">'.__('Cochez cette case pour afficher un extrait', 'SP-Random-Post').'</label></br><small>(Check this box to display an excerpt)</small></p>';

echo '<p><label for="'.$this->get_field_name('length').'">'.__('Longueur de l&acute;extrait :', 'SP-Random-Post').'<input style="width:40px;float: right;" name="'.$this->get_field_name('length').'" type="text" value="'.htmlspecialchars($instance['length']).'" /></label></br><small>(Length of excerpt)</small></p>';

echo '<p><input style="" name="'.$this->get_field_name('dateaffiche').'" type="checkbox" value="checkbox" ';if($instance['dateaffiche'])echo 'checked="checked"';echo '/><label for="'.$this->get_field_name('dateaffiche').'">'.__('Cochez cette case pour afficher la date', 'SP-Random-Post').'</label></br><small>(Check this box to display the date)</small></p>';
?>
<p>			
<label><?php _e( 'Categorie' ); ?>:</label>
<?php wp_dropdown_categories( array( 'show_option_all' => __('ALL'),'hide_empty'  => 0,'name' => $this->get_field_name("cat"), 'selected' => $instance["cat"] ) ); ?>
</br><small>(Category)</small>
</p>
<?php
echo'</fieldset>';
// image
echo'<fieldset>
  	 <legend>Vignette <small>(Thumbnail)</small></legend>';
echo '<p><input style="" name="'.$this->get_field_name('thumbsaffiche').'" type="checkbox" value="checkbox" ';if($instance['thumbsaffiche'])echo 'checked="checked"';echo '/><label for="'.$this->get_field_name('thumbsaffiche').'">'.__('Cochez cette case pour afficher une vignette </br><small>(Check this box to display a thumbnail)</small>', 'SP-Random-Post').'</label></p>';
	
echo '<p><label for="'.$this->get_field_name('thumb_w').'">'.__('Largeur de la Vignette ', 'SP-Random-Post').'<input style="width:40px; float:right;margin-right:60px;" name="'.$this->get_field_name('thumb_w').'" type="text" value="'.htmlspecialchars($instance['thumb_w']).'" /></label></br><small>(Thumbnail width)</small></p>';

echo '<p><label for="'.$this->get_field_name('thumb_h').'">'.__('Hauteur de la Vignette ', 'SP-Random-Post').'<input style="width:40px; float:right;margin-right:60px;" name="'.$this->get_field_name('thumb_h').'" type="text" value="'.htmlspecialchars($instance['thumb_h']).'" /></label></br><small>(Thumbnail height)</small></p>';
echo'</fieldset>';
// image fin


echo'</br>';
echo'<fieldset>
  	 <legend>Auto actualisation <small>(Auto update)</small></legend>';
echo '<p><input style="" name="'.$this->get_field_name('auto').'" type="checkbox" value="checkbox" ';if($instance['auto'])echo 'checked="checked"';echo '/><label for="'.$this->get_field_name('auto').'">'.__('Cochez cette case pour l&acute;actualisation automatique', 'SP-Random-Post').'</label></br><small>(Select this checkbox to refresh automatically)</small></p>';

echo '<p><label for="'.$this->get_field_name('time').'">'.__('Intervalle en seconde de actualisation automatique', 'SP-Random-Post').'<input style="width:40px;float: right;" name="'.$this->get_field_name('time').'" type="text" value="'.htmlspecialchars($instance['time']).'" /></label></br><small>(Second interval auto-refresh)</small></p>';

echo'</fieldset>';
	}
	function update($new_instance,$old_instance){
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['number'] = (int)$new_instance['number'];
		$instance['month'] = (int)$new_instance['month'];
		$instance['cat'] = (int)$new_instance['cat'];
		$instance['year'] = (int)$new_instance['year'];
		$instance['thumb_w'] = (int)$new_instance['thumb_w'];
		$instance['thumb_h'] = (int)$new_instance['thumb_h'];
		$instance['thumbsaffiche'] = (bool)$new_instance['thumbsaffiche'];
		$instance['dateaffiche'] = (bool)$new_instance['dateaffiche'];
		$instance['excerpt'] = (bool)$new_instance['excerpt'];
		$instance['length'] = (int)$new_instance['length'];
		$instance['auto'] = (bool)$new_instance['auto'];
		$instance['time'] = (int)$new_instance['time'];
		return $instance;
	}
	function widget($args,$instance){
		extract($args);
		$myargs='number='.$instance['number'].'&thumb_w='.((int)$instance['thumb_w']).'&thumb_h='.((int)$instance['thumb_h']).'&thumbsaffiche='.((int)$instance['thumbsaffiche']).'&dateaffiche='.((int)$instance['dateaffiche']).'&excerpt='.((int)$instance['excerpt']).'&length='.(int)$instance['length'].			        '&month='.(int)$instance['month'].'&year='.(int)$instance['year'].'&cat='.(int)$instance['cat'].'&auto='.((int)$instance['auto']).'&time='.(int)$instance['time'];
		$title = apply_filters('widget_title',empty($instance['title']) ? __('Random Posts', 'SP-Random-Post') : $instance['title']);
		echo '<li id="random-post-widget" class="widget"><h3 class="widget-title" >'.$title.'</h3>';
		SP_load_Random_Posts($myargs);
		echo '</li>';
	}
}

// ******************************************************** fonction image ***************************************************************
function sp_post_ramdom_resize( $url, $width, $height = null, $crop = null, $single = true ) {
	
	//validate inputs
	if(!$url OR !$width ) return false;
	
	//define upload path & dir
	$upload_info = wp_upload_dir();
	$upload_dir = $upload_info['basedir'];
	$upload_url = $upload_info['baseurl'];
	
	//check if $img_url is local
	if(strpos( $url, $upload_url ) === false) return false;
	
	//define path of image
	$rel_path = str_replace( $upload_url, '', $url);
	$img_path = $upload_dir . $rel_path;
	
	//check if img path exists, and is an image indeed
	if( !file_exists($img_path) OR !getimagesize($img_path) ) return false;
	
	//get image info
	$info = pathinfo($img_path);
	$ext = $info['extension'];
	list($orig_w,$orig_h) = getimagesize($img_path);
	
	//get image size after cropping
	$dims = image_resize_dimensions($orig_w, $orig_h, $width, $height, $crop);
	$dst_w = $dims[4];
	$dst_h = $dims[5];
	
	//use this to check if cropped image already exists, so we can return that instead
	$suffix = "{$dst_w}x{$dst_h}";
	$dst_rel_path = str_replace( '.'.$ext, '', $rel_path);
	$destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";
	
	//if orig size is smaller
	if($width >= $orig_w) {
		
		if(!$dst_h) :
			//can't resize, so return original url
			$img_url = $url;
			$dst_w = $orig_w;
			$dst_h = $orig_h;
			
		else :
			//else check if cache exists
			if(file_exists($destfilename) && getimagesize($destfilename)) {
				$img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
			} 
			//else resize and return the new resized image url
			else {
				$resized_img_path = image_resize( $img_path, $width, $height, $crop );
				$resized_rel_path = str_replace( $upload_dir, '', $resized_img_path);
				$img_url = $upload_url . $resized_rel_path;
			}
			
		endif;
		
	}
	//else check if cache exists
	elseif(file_exists($destfilename) && getimagesize($destfilename)) {
		$img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
	} 
	//else, we resize the image and return the new resized image url
	else {
		$resized_img_path = image_resize( $img_path, $width, $height, $crop );
		$resized_rel_path = str_replace( $upload_dir, '', $resized_img_path);
		$img_url = $upload_url . $resized_rel_path;
	}
	
	//return the output
	if($single) {
		//str return
		$image = $img_url;
	} else {
		//array return
		$image = array (
			0 => $img_url,
			1 => $dst_w,
			2 => $dst_h
		);
	}
	
	return $image;
}

// Fin fonction image

function WARP__widget_init(){
	register_widget('WARP__widget');
}
add_action('widgets_init','WARP__widget_init');
?>