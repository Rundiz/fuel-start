<h1><?php echo __('account_accounts'); ?></h1>

<div class="row cmds">
    <div class="col-sm-6">
        <ul class="actions-inline">
            <li><?php printf(__('admin_total', array('total' => (isset($list_accounts['total']) ? $list_accounts['total'] : '0')))); ?></li>
            <?php if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_add_perm')) { ?><li><?php echo \Html::anchor('admin/account/add', __('admin_add'), array('class' => 'btn btn-default')); ?></li><?php } ?> 
        </ul>
    </div>
    <div class="col-sm-6">
        <form method="get" class="form-inline pull-right form-search-items">
            <?php 
            $querystring = \Input::server('QUERY_STRING');
            $querystring_exp = explode('&', $querystring);
            foreach ($querystring_exp as $inputs) {
                if (mb_strpos($inputs, '=') !== false) {
                    list($input_name, $input_val) = explode('=', $inputs);
                    if ($input_name != 'q' && $input_name != 'page') {
                        echo \Form::hidden(urldecode($input_name), urldecode($input_val)) . "\n";
                    }
                }
            }
            unset($input_name, $input_val, $inputs, $querystring, $querystring_exp);
            ?> 
            <div class="form-group">
                <?php echo \Form::input('q', (isset($q) ? $q : ''), array('class' => 'form-control search-input', 'maxlength' => '255')); ?> 
            </div>
            <button type="submit" class="btn btn-default"><?php echo __('admin_search'); ?></button>
            <?php echo \Html::anchor('admin/account', __('admin_view_all'), array('class' => 'btn btn-default')); ?> 
        </form>
    </div>
</div>

<?php echo \Form::open(array('action' => 'admin/account/multiple', 'class' => 'form-horizontal', 'role' => 'form', 'onsubmit' => 'return verifySelectedAction();')); ?> 
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
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_id', 'sort' => $next_sort), $except_querystring, null, __('account_id')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_username', 'sort' => $next_sort), $except_querystring, null, __('account_username')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_email', 'sort' => $next_sort), $except_querystring, null, __('account_email')); ?></th>
                    <th><?php echo __('account_role'); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_create', 'sort' => $next_sort), $except_querystring, null, __('account_register_since')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_last_login', 'sort' => $next_sort), $except_querystring, null, __('account_last_login')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_status', 'sort' => $next_sort), $except_querystring, null, __('account_status')); ?></th>
                    <th></th>
                </tr>
                <tr class="row-filter-form">
                    <th class="check-column">--</th>
                    <th><?php echo \Extension\Form::number('filter_account_id', (isset($filter_account_id) ? $filter_account_id : null), array('id' => 'filter_account_id', 'class' => 'form-control input-no-spinner input-id', 'onkeypress' => 'return noEnter(event);')); ?></th>
                    <th><?php echo \Form::input('filter_account_username', (isset($filter_account_username) ? $filter_account_username : null), array('id' => 'filter_account_username', 'class' => 'form-control', 'onkeypress' => 'return noEnter(event);')); ?></th>
                    <th><?php echo \Form::input('filter_account_email', (isset($filter_account_email) ? $filter_account_email : null), array('id' => 'filter_account_email', 'class' => 'form-control', 'onkeypress' => 'return noEnter(event);')); ?></th>
                    <th>
                    <?php
                    if (isset($account_levels) && is_array($account_levels)) {
                        \Arr::insert_assoc($account_levels, array('' => '-'), 0);
                    } else {
                        $account_levels = array('' => '-');
                    }
                    echo \Form::select(
                        'filter_level_group_id',
                        (isset($filter_level_group_id) ? $filter_level_group_id : null),
                        $account_levels,
                        array(
                            'id' => 'filter_level_group_id',
                            'class' => 'form-control chosen-select'
                        )
                    );
                    ?> 
                    </th>
                    <th><?php echo \Extension\Form::date('filter_account_create', (isset($filter_account_create) ? $filter_account_create : null), array('id' => 'filter_account_create', 'class' => 'form-control input-date', 'onkeypress' => 'return noEnter(event);', 'placeholder' => __('admin_since'))); ?></th>
                    <th><?php echo \Extension\Form::date('filter_account_last_login', (isset($filter_account_last_login) ? $filter_account_last_login : null), array('id' => 'filter_account_last_login', 'class' => 'form-control input-date', 'onkeypress' => 'return noEnter(event);', 'placeholder' => __('admin_since'))); ?></th>
                    <th>
                    <?php 
                    echo \Form::select(
                        'filter_account_status', 
                        (isset($filter_account_status) ? $filter_account_status : null), 
                        array(
                            '' => '-',
                            '0' => __('admin_disable'),
                            '1' => __('admin_enable'),
                        ), 
                        array(
                            'id' => 'filter_account_status',
                            'class' => 'form-control chosen-select',
                        )
                    );
                    ?> 
                    </th>
                    <th><button class="btn btn-default btn-xs btn-filter" onclick="addFilterSearch();" type="button"><span class="glyphicon glyphicon-filter"></span> <?php echo __('admin_filter'); ?></button></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_id', 'sort' => $next_sort), $except_querystring, null, __('account_id')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_username', 'sort' => $next_sort), $except_querystring, null, __('account_username')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_email', 'sort' => $next_sort), $except_querystring, null, __('account_email')); ?></th>
                    <th><?php echo __('account_role'); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_create', 'sort' => $next_sort), $except_querystring, null, __('account_register_since')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_last_login', 'sort' => $next_sort), $except_querystring, null, __('account_last_login')); ?></th>
                    <th><?php echo \Extension\Html::fuelStartSortableLink(array('orders' => 'account_status', 'sort' => $next_sort), $except_querystring, null, __('account_status')); ?></th>
                    <th></th>
                </tr>
            </tfoot>
            <tbody>
                <?php if (isset($list_accounts['items']) && is_array($list_accounts['items']) && !empty($list_accounts['items'])) { ?> 
                <?php foreach ($list_accounts['items'] as $row) { ?> 
                <tr>
                    <td class="check-column"><?php echo \Extension\Form::checkbox('id[]', $row->account_id, array(($row->account_id == '0' ? 'disabled' : null))); ?></td>
                    <td><?php echo $row->account_id; ?></td>
                    <td><?php echo \Security::htmlentities($row->account_username); ?></td>
                    <td><?php echo $row->account_email; ?></td>
                    <td>
                        <?php 
                        $i = 1;
                        foreach($row->account_level as $lvl) {
                            $lvg = \Model_AccountLevelGroup::find($lvl->level_group_id);
                            echo $lvg->level_name;

                            if (end($row->account_level) != $lvl) {
                                echo ', ';
                            }

                            if ($i > 5) {
                                echo '...';
                                break;
                            }

                            $i++;
                        } 
                        unset($lvg, $lvl);
                        ?>
                    </td>
                    <td><?php echo \Extension\Date::gmtDate('', $row->account_create); ?></td>
                    <td><?php if ($row->account_last_login != null) {echo \Extension\Date::gmtDate('', $row->account_last_login);} ?></td>
                    <td><span class="glyphicon glyphicon-<?php echo ($row->account_status == '1' ? 'ok' : 'remove'); ?>"></span> <?php echo $row->account_status_text; ?></td>
                    <td>
                        <?php if ($row->account_id != '0') { ?> 
                        <ul class="actions-inline">
                            <?php if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_edit_perm')) { ?> <li><?php echo \Extension\Html::anchor('admin/account/edit/' . $row->account_id, '<span class="glyphicon glyphicon-pencil"></span> ' . __('admin_edit'), array('class' => 'btn btn-default btn-xs')); ?></li><?php } ?> 
                            <?php if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_viewlogin_log_perm')) { ?> <li><?php echo \Extension\Html::anchor('admin/account/viewlogins/' . $row->account_id, '<span class="glyphicon glyphicon-list"></span> ' . __('account_view_login_history'), array('class' => 'btn btn-default btn-xs')); ?></li><?php } ?> 
                            <?php if (\Model_AccountLevelPermission::checkAdminPermission('acperm_perm', 'acperm_manage_user_perm')) { ?> <li><?php echo \Extension\Html::anchor('admin/account-permission/index/' . $row->account_id, '<span class="fa fa-key"></span> ' . __('account_set_permission'), array('class' => 'btn btn-default btn-xs')); ?></li><?php } ?> 
                        </ul>
                        <?php } ?> 
                    </td>
                </tr>
                <?php } // endofreach; ?> 
                <?php } else { ?> 
                <tr>
                    <td colspan="9"><?php echo __('fslang_no_data'); ?></td>
                </tr>
                <?php } // endif; ?> 
            </tbody>
        </table>
    </div>
    
    <div class="row cmds">
        <div class="col-sm-6">
             
            <select name="act" class="form-control select-inline chosen-select select-action">
                <option value="" selected="selected"></option>
                <?php if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_edit_perm')) { ?><option value="enable"><?php echo __('admin_enable'); ?></option><?php } ?> 
                <?php if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_edit_perm')) { ?><option value="disable"><?php echo __('admin_disable'); ?></option><?php } ?> 
                <?php if (\Model_AccountLevelPermission::checkAdminPermission('account_perm', 'account_delete_perm')) { ?><option value="del"><?php echo __('admin_delete'); ?></option><?php } ?> 
            </select>
            <button type="submit" class="bb-button btn btn-warning"><?php echo __('admin_submit'); ?></button>
            <?php echo \Extension\Html::anchor('admin', __('admin_cancel'), array('class' => 'btn btn-default')); ?> 
        </div>
        <div class="col-sm-6">
            <?php if (isset($pagination)) {echo $pagination->render();} ?> 
        </div>
    </div>
<?php echo \Form::close(); ?> 


<?php
\Theme::instance()->asset->js('bootstrap-datepicker.js', array(), 'fuelstart_footer')->render('fuelstart_footer');
?> 
<script>
    function addFilterSearch() {
        // collect all filter value
        var filter_account_id = $('#filter_account_id').val();
        var filter_account_username = $('#filter_account_username').val();
        var filter_account_email = $('#filter_account_email').val();
        var filter_level_group_id = $('#filter_level_group_id').val();
        var filter_account_create = $('#filter_account_create').val();
        var filter_account_last_login = $('#filter_account_last_login').val();
        var filter_account_status = $('#filter_account_status').val();
        
        // create input hidden
        var search_new_input = '<div class="filter-inputs">';
        search_new_input += '<input type="hidden" name="filter_account_id" value="'+filter_account_id+'">';
        search_new_input += '<input type="hidden" name="filter_account_username" value="'+filter_account_username+'">';
        search_new_input += '<input type="hidden" name="filter_account_email" value="'+filter_account_email+'">';
        search_new_input += '<input type="hidden" name="filter_level_group_id" value="'+filter_level_group_id+'">';
        search_new_input += '<input type="hidden" name="filter_account_create" value="'+filter_account_create+'">';
        search_new_input += '<input type="hidden" name="filter_account_last_login" value="'+filter_account_last_login+'">';
        search_new_input += '<input type="hidden" name="filter_account_status" value="'+filter_account_status+'">';
        search_new_input += '</div>';
        
        // clear old filter inputs
        $('.form-search-items .filter-inputs').remove();
        $('.form-search-items input[name=filter_account_id]').remove();
        $('.form-search-items input[name=filter_account_username]').remove();
        $('.form-search-items input[name=filter_account_email]').remove();
        $('.form-search-items input[name=filter_level_group_id]').remove();
        $('.form-search-items input[name=filter_account_create]').remove();
        $('.form-search-items input[name=filter_account_last_login]').remove();
        $('.form-search-items input[name=filter_account_status]').remove();
        
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
    
    
    $(function() {
        // datepicker
        if (!Modernizr.inputtypes.date) {
            // this browser support date picker.
            $('.input-date').datepicker({
                format: 'yyyy-mm-dd'
            });
        }// datepicker
    });// jquery
</script>