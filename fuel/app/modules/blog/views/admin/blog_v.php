<h1><?php echo \Lang::get('blog'); ?></h1>

<div class="row cmds">
	<div class="col-sm-6">
		<?php if (\Model_AccountLevelPermission::checkAdminPermission('blog_perm', 'blog_write_perm')) {echo \Html::anchor('blog/admin/index/add', \Lang::get('admin.admin_add'), array('class' => 'btn btn-default'));} ?> 
		| <?php printf(\Lang::get('admin.admin_total', array('total' => (isset($list_items['total']) ? $list_items['total'] : '0')))); ?>
	</div>
</div>

<?php echo \Form::open(array('action' => 'blog/admin/index/multiple', 'class' => 'form-horizontal', 'role' => 'form')); ?> 
	<div class="form-status-placeholder">
		<?php if (isset($form_status) && isset($form_status_message)) { ?> 
		<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
		<?php } ?> 
	</div>
	<?php echo \Extension\NoCsrf::generate(); ?> 

	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
					<th><?php echo \Lang::get('blog_post_name'); ?></th>
					<th><?php echo \Lang::get('blog_total_comment'); ?></th>
					<th><?php echo \Lang::get('blog_post_date'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
					<th><?php echo \Lang::get('blog_post_name'); ?></th>
					<th><?php echo \Lang::get('blog_total_comment'); ?></th>
					<th><?php echo \Lang::get('blog_post_date'); ?></th>
					<th></th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (isset($list_items['items']) && is_array($list_items['items']) && !empty($list_items['items'])) { ?> 
				<?php foreach ($list_items['items'] as $row) { ?> 
				<tr>
					<td class="check-column"><?php echo \Extension\Form::checkbox('id[]', $row->post_id); ?></td>
					<td><?php echo $row->post_name; ?></td>
					<td></td>
					<td><?php echo \Extension\Date::gmtDate('', $row->post_date); ?></td>
					<td>
						<?php if (\Model_AccountLevelPermission::checkAdminPermission('blog_perm', 'blog_write_perm')) { ?><?php echo \Extension\Html::anchor('blog/admin/index/edit/' . $row->post_id, '<span class="glyphicon glyphicon-pencil"></span> ' . \Lang::get('admin.admin_edit'), array('class' => 'btn btn-default btn-xs')); ?><?php } ?> 
					</td>
				</tr>
				<?php } // endforeach; ?> 
				<?php } else { ?> 
				<tr>
					<td colspan="5"><?php echo \Lang::get('fslang.fslang_no_data'); ?></td>
				</tr>
				<?php } // endfi; ?> 
			</tbody>
		</table>
	</div>

	<div class="row cmds">
		<div class="col-sm-6">
			 
			<select name="act" class="form-control select-inline chosen-select">
				<option value="" selected="selected"></option>
				<?php if (\Model_AccountLevelPermission::checkAdminPermission('blog_perm', 'blog_manage_perm')) { ?><option value="del"><?php echo \Lang::get('admin.admin_delete'); ?></option><?php } ?> 
			</select>
			<button type="submit" class="bb-button btn btn-warning"><?php echo \Lang::get('admin.admin_submit'); ?></button>
			<?php echo \Extension\Html::anchor('admin', \Lang::get('admin.admin_cancel'), array('class' => 'btn btn-default')); ?> 
		</div>
		<div class="col-sm-6">
			<?php if (isset($pagination)) {echo $pagination->render();} ?> 
		</div>
	</div>
<?php echo \Form::close(); ?>