<h1><?php echo \Lang::get('fs_updater'); ?></h1>


<?php 
if (isset($result) && $result === true) {
	echo '<div class="alert alert-success">' . \Lang::get('fs_update_completed') . '</div>';
} else {
	echo '<div class="alert alert-danger">' . \Lang::get('fs_failed_to_update') . '</div>';
}
?> 