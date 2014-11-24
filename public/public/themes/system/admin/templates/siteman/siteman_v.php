<h1><?php echo \Lang::get('siteman_multisite_manager'); ?></h1>

<div class="row cmds">
    <div class="col-sm-6">
        <ul class="actions-inline">
            <li><?php printf(\Lang::get('admin_total', array('total' => (isset($list_sites['total']) ? $list_sites['total'] : '0')))); ?></li>
            <?php if (\Model_AccountLevelPermission::checkAdminPermission('siteman_perm', 'siteman_add_perm')) { ?><li><?php echo \Html::anchor('admin/siteman/add', \Lang::get('admin_add'), array('class' => 'btn btn-default')); ?></li><?php } ?> 
        </ul>
    </div>
    <div class="col-sm-6">
        <form method="get" class="pull-right form-search-items">
            <?php echo \Html::anchor('admin/siteman', __('admin_view_all'), array('class' => 'btn btn-default')); ?> 
        </form>
        <div class="clearfix"></div>
    </div>
</div>

<?php echo \Form::open(array('action' => 'admin/siteman/multiple', 'class' => 'form-horizontal', 'role' => 'form', 'onsubmit' => 'return verifySelectedAction();')); ?> 
    <div class="form-status-placeholder">
        <?php if (isset($form_status) && isset($form_status_message)) { ?> 
        <div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
        <?php } ?> 
    </div>
    <?php echo \Extension\NoCsrf::generate(); ?> 

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <?php 
            // except querystring to generate
            $except_querystring[] = 'page';
            ?> 
            <thead>
                <tr>
                    <th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'site_id', 'sort' => $next_sort), $except_querystring, null, \Lang::get('siteman_site_id')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'site_name', 'sort' => $next_sort), $except_querystring, null, \Lang::get('siteman_site_name')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'site_domain', 'sort' => $next_sort), $except_querystring, null, \Lang::get('siteman_site_domain')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'site_status', 'sort' => $next_sort), $except_querystring, null, \Lang::get('siteman_site_status')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'site_create', 'sort' => $next_sort), $except_querystring, null, \Lang::get('siteman_site_create')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'site_update', 'sort' => $next_sort), $except_querystring, null, \Lang::get('siteman_site_update')); ?></th>
                    <th></th>
                </tr>
                <tr class="row-filter-form">
                    <th class="check-column">--</th>
                    <th><?php echo \Extension\Form::number('filter_site_id', (isset($filter_site_id) ? $filter_site_id : null), array('id' => 'filter_site_id', 'class' => 'form-control input-no-spinner input-id', 'onkeypress' => 'return noEnter(event);')); ?></th>
                    <th><?php echo \Form::input('filter_site_name', (isset($filter_site_name) ? $filter_site_name : null), array('id' => 'filter_site_name', 'class' => 'form-control', 'onkeypress' => 'return noEnter(event);')); ?></th>
                    <th><?php echo \Form::input('filter_site_domain', (isset($filter_site_domain) ? $filter_site_domain : null), array('id' => 'filter_site_domain', 'class' => 'form-control', 'onkeypress' => 'return noEnter(event);')); ?></th>
                    <th>
                    <?php 
                    echo \Form::select(
                        'filter_site_status', 
                        (isset($filter_site_status) ? $filter_site_status : null), 
                        array(
                            '' => '-',
                            '0' => __('admin_disable'),
                            '1' => __('admin_enable'),
                        ), 
                        array(
                            'id' => 'filter_site_status',
                            'class' => 'form-control chosen-select',
                        )
                    );
                    ?> 
                    </th>
                    <th>--</th>
                    <th>--</th>
                    <th><button class="btn btn-default btn-xs btn-filter" onclick="addFilterSearch();" type="button"><span class="glyphicon glyphicon-filter"></span> <?php echo __('admin_filter'); ?></button></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'site_id', 'sort' => $next_sort), $except_querystring, null, \Lang::get('siteman_site_id')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'site_name', 'sort' => $next_sort), $except_querystring, null, \Lang::get('siteman_site_name')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'site_domain', 'sort' => $next_sort), $except_querystring, null, \Lang::get('siteman_site_domain')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'site_status', 'sort' => $next_sort), $except_querystring, null, \Lang::get('siteman_site_status')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'site_create', 'sort' => $next_sort), $except_querystring, null, \Lang::get('siteman_site_create')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'site_update', 'sort' => $next_sort), $except_querystring, null, \Lang::get('siteman_site_update')); ?></th>
                    <th></th>
                </tr>
            </tfoot>
            <tbody>
                <?php if (isset($list_sites['items']) && is_array($list_sites['items']) && !empty($list_sites['items'])) { ?> 
                <?php foreach ($list_sites['items'] as $row) { ?> 
                <tr>
                    <td class="check-column"><?php echo \Extension\Form::checkbox('id[]', $row->site_id, array(($row->site_id == '1' ? 'disabled' : null))); ?></td>
                    <td><?php echo $row->site_id; ?></td>
                    <td><?php echo $row->site_name; ?></td>
                    <td><?php echo $row->site_domain; ?></td>
                    <td><span class="glyphicon glyphicon-<?php echo ($row->site_status == '1' ? 'ok' : 'remove'); ?>"></span></td>
                    <td><?php echo \Extension\Date::gmtDate('', $row->site_create); ?></td>
                    <td><?php echo \Extension\Date::gmtDate('', $row->site_update); ?></td>
                    <td>
                        <ul class="actions-inline">
                            <?php if (\Model_AccountLevelPermission::checkAdminPermission('siteman_perm', 'siteman_edit_perm')) { ?> <li><?php echo \Extension\Html::anchor('admin/siteman/edit/' . $row->site_id, '<span class="glyphicon glyphicon-pencil"></span> ' . \Lang::get('admin_edit'), array('class' => 'btn btn-default btn-xs')); ?></li><?php } ?> 
                        </ul>
                    </td>
                </tr>
                <?php } // endofreach; ?> 
                <?php } else { ?> 
                <tr>
                    <td colspan="8"><?php echo \Lang::get('fslang_no_data'); ?></td>
                </tr>
                <?php } // endif; ?> 
            </tbody>
        </table>
    </div>

    <div class="row cmds">
        <div class="col-sm-6">
             
            <select name="act" class="form-control select-inline chosen-select select-action">
                <option value="" selected="selected"></option>
                <?php if (\Model_AccountLevelPermission::checkAdminPermission('siteman_perm', 'siteman_edit_perm')) { ?><option value="enable"><?php echo \Lang::get('admin_enable'); ?></option><?php } ?> 
                <?php if (\Model_AccountLevelPermission::checkAdminPermission('siteman_perm', 'siteman_edit_perm')) { ?><option value="disable"><?php echo \Lang::get('admin_disable'); ?></option><?php } ?> 
                <?php if (\Model_AccountLevelPermission::checkAdminPermission('siteman_perm', 'siteman_delete_perm')) { ?><option value="del"><?php echo \Lang::get('admin_delete'); ?></option><?php } ?> 
            </select>
            <button type="submit" class="bb-button btn btn-warning"><?php echo \Lang::get('admin_submit'); ?></button>
            <?php echo \Extension\Html::anchor('admin', \Lang::get('admin_cancel'), array('class' => 'btn btn-default')); ?> 
        </div>
        <div class="col-sm-6">
            <?php if (isset($pagination)) {echo $pagination->render();} ?> 
        </div>
    </div>
<?php echo \Form::close(); ?> 


<script>
    function addFilterSearch() {
        // collect all filter value
        var filter_site_id = $('#filter_site_id').val();
        var filter_site_name = $('#filter_site_name').val();
        var filter_site_domain = $('#filter_site_domain').val();
        var filter_site_status = $('#filter_site_status').val();
        
        // create input hidden
        var search_new_input = '<div class="filter-inputs">';
        search_new_input += '<input type="hidden" name="filter_site_id" value="'+filter_site_id+'">';
        search_new_input += '<input type="hidden" name="filter_site_name" value="'+filter_site_name+'">';
        search_new_input += '<input type="hidden" name="filter_site_domain" value="'+filter_site_domain+'">';
        search_new_input += '<input type="hidden" name="filter_site_status" value="'+filter_site_status+'">';
        search_new_input += '</div>';
        
        // clear old filter inputs
        $('.form-search-items .filter-inputs').remove();
        $('.form-search-items input[name=filter_site_id]').remove();
        $('.form-search-items input[name=filter_site_name]').remove();
        $('.form-search-items input[name=filter_site_domain]').remove();
        $('.form-search-items input[name=filter_site_status]').remove();
        
        // add new input filter to search form
        $('.form-search-items').prepend(search_new_input);
        
        // submit the search form
        $('.form-search-items').submit();
    }// addFilterSearch
    
    
    function verifySelectedAction() {
        if ($('.select-action').val() === 'del') {
            confirm_msg = confirm('<?php echo __('admin_are_you_sure_to_delete_selected_items'); ?>');
            
            if (confirm_msg === false) {
                // admin cancel confirm dialog. stop form from submitting.
                return false;
            }
        }
    }// verifySelectedAction
</script>