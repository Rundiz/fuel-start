<h1><?php echo \Lang::get('blog'); ?></h1>

<?php if (isset($list_items['items']) && is_array($list_items['items']) && !empty($list_items['items'])) { ?> 
<?php foreach ($list_items['items'] as $row) { ?> 

<article class="each-blog-post">
	<h1><?php echo \Extension\Html::anchor('blog/blog/post/' . $row->post_id, $row->post_name); ?></h1>
	<time datetime="<?php echo \Extension\Date::gmtDate('', $row->post_date); ?>"><?php echo \Extension\Date::gmtDate('', $row->post_date); ?></time>
	<div class="post-content">
		<?php echo $row->post_body; ?> 
	</div>
	
</article>
<?php } // endforeach; ?> 

<?php if (isset($pagination)) {echo $pagination->render();} ?> 

<?php } else { ?> 
<p><?php echo \Lang::get('fslang.fslang_no_data'); ?></p>
<?php } // endif; ?> 