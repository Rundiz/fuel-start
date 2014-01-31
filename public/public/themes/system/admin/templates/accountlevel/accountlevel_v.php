<h1><?php echo \Lang::get('accountlv.accountlv_role'); ?></h1>

<div class="row cmds">
	<div class="col-sm-6">
		<?php if (\Model_AccountLevelPermission::checkAdminPermission('accountlv.accountlv_perm', 'accountlv.accountlv_add_perm')) {echo \Html::anchor('admin/account-level/add', \Lang::get('admin.admin_add'), array('class' => 'btn btn-default'));} ?> 
		| <?php printf(\Lang::get('admin.admin_total', array('total' => (isset($list_levels['total']) ? $list_levels['total'] : '0')))); ?>
	</div>
</div>

<?php echo \Form::open(array('action' => 'admin/account-level/multiple', 'class' => 'form-horizontal', 'role' => 'form')); ?> 
	<div class="form-status-placeholder">
		<?php if (isset($form_status) && isset($form_status_message)) { ?> 
		<div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
		<?php } ?> 
	</div>
	<?php echo \Extension\NoCsrf::generate(); ?> 

	<div class="table-responsive">
		<table class="table table-striped table-hover list-logins-table table-sortable">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
					<th style="width: 24px;"></th>
					<th><?php echo \Lang::get('accountlv.accountlv_level_priority'); ?> <span class="glyphicon glyphicon-question-sign bootstrap-tooltip" data-toggle="tooltip" data-original-title="<?php echo \Lang::get('accountlv.accountlv_higher_priority_will_come_first'); ?>"></span></th>
					<th><?php echo \Lang::get('accountlv.accountlv_role'); ?></th>
					<th><?php echo \Lang::get('accountlv.accountlv_description'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
					<th></th>
					<th><?php echo \Lang::get('accountlv.accountlv_level_priority'); ?> <span class="glyphicon glyphicon-question-sign bootstrap-tooltip" data-toggle="tooltip" data-original-title="<?php echo \Lang::get('accountlv.accountlv_higher_priority_will_come_first'); ?>"></span></th>
					<th><?php echo \Lang::get('accountlv.accountlv_role'); ?></th>
					<th><?php echo \Lang::get('accountlv.accountlv_description'); ?></th>
					<th></th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (isset($list_levels['items']) && is_array($list_levels['items']) && !empty($list_levels['items'])) { ?> 
				<?php foreach ($list_levels['items'] as $row) { ?> 
				<tr class="state-default<?php if (in_array($row->level_group_id, $disallowed_edit_delete)) { ?> ui-state-disabled<?php } ?>" id="listItem_<?php echo $row->level_group_id; ?>">
					<td class="check-column"><?php echo \Extension\Form::checkbox('id[]', $row->level_group_id, array((in_array($row->level_group_id, $disallowed_edit_delete) ? 'disabled' : null), 'title' => $row->level_group_id)); ?></td>
					<td><span class="fa fa-sort cursor-n-resize sortable-cell<?php if (in_array($row->level_group_id, $disallowed_edit_delete)) { ?> text-muted<?php } ?>"></span></td>
					<td><?php echo $row->level_priority; ?></td>
					<td><?php echo $row->level_name; ?></td>
					<td><?php echo $row->level_description; ?></td>
					<td>
						<?php if (\Model_AccountLevelPermission::checkAdminPermission('accountlv.accountlv_perm', 'accountlv.accountlv_edit_perm')) { ?> 
						<?php echo \Extension\Html::anchor('admin/account-level/edit/' . $row->level_group_id, '<span class="glyphicon glyphicon-pencil"></span> ' . \Lang::get('admin.admin_edit'), array('class' => 'btn btn-default btn-xs' . (in_array($row->level_group_id, $disallowed_edit_delete) ? ' disabled' : null))); ?> 
						<?php } ?> 
					</td>
				</tr>
				<?php } // endforeach; ?> 
				<?php } else { ?> 
				<tr>
					<td colspan="6"><?php echo \Lang::get('fslang.fslang_no_data'); ?></td>
				</tr>
				<?php } // endif; ?> 
			</tbody>
		</table>
	</div>

	<div class="row cmds">
		<div class="col-sm-6">
			<select name="act" class="form-control select-inline chosen-select">
				<option value="" selected="selected"></option>
				<?php if (\Model_AccountLevelPermission::checkAdminPermission('accountlv.accountlv_perm', 'accountlv.accountlv_delete_perm')) { ?><option value="del"><?php echo \Lang::get('admin.admin_delete'); ?></option><?php } ?> 
			</select>
			<button type="submit" class="bb-button btn btn-warning"><?php echo \Lang::get('admin.admin_submit'); ?></button>
			<?php echo \Extension\Html::anchor('admin', \Lang::get('admin.admin_cancel'), array('class' => 'btn btn-default')); ?> 
		</div>
		<div class="col-sm-6">
			
		</div>
	</div>
<?php echo \Form::close(); ?> 


<script type="text/javascript">
	$(function() {
		// Return a helper with preserved width of cells
		var fixHelper = function(e, ui) {
		    ui.children().each(function() {
			  $(this).width($(this).width());
		    });
		    return ui;
		};
		
		$('.table-sortable tbody').sortable({
			handle: '.sortable-cell',
			helper: fixHelper,
			stop: function(event, ui) {$('.form-status-placeholder').html('');},
			start: function(event, ui) {ui.placeholder.html("<td colspan='6'>&nbsp;</td>"); $('.form-status-placeholder').html('<span class="fa fa-spinner fa-spin"></span>');},
			placeholder: "ui-state-highlight",
			items: "tr:not(.ui-state-disabled)",
			update : function () {
				var orders = $('.table-sortable tbody').sortable('serialize');
				$.ajax({
					url: base_url+'admin/account-level/ajaxsort',
					type: 'POST',
					data: csrf_name+'='+nocsrf_val+'&'+orders,
					dataType: 'json',
					success: function(data) {
						if (data.result === true) {
							clearinfo();
						} else {
							$('.form-status-placeholder').html('');
						}
					},
					error: function (data, status, e) {
						clearinfo();
					}
				});
			}
		});
		$('.table-sortable tbody').disableSelection();
	});
	
	
	function clearinfo() {
		$('.form-status-placeholder').html('');
		location.reload();
	}
</script>