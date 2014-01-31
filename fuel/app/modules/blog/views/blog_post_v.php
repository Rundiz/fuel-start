<h1><?php echo $post_name; ?></h1>
<time datetime="<?php echo \Extension\Date::gmtDate('', $post_date); ?>"><?php echo \Extension\Date::gmtDate('', $post_date); ?></time>

<div class="post-content">
	<?php echo $post_body; ?> 
</div>