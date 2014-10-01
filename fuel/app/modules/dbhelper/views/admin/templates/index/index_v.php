<h1><?php echo __('dbhelper'); ?></h1>


<?php echo \Form::open(array('class' => 'form-horizontal')); ?> 
    <?php echo \Extension\NoCsrf::generate(); ?> 

    <div class="form-group">
        <label class="control-label col-sm-2"><?php echo __('dbhelper_generate_properties'); ?>:</label>
        <div class="col-sm-10">
            <select name="table_name" class="form-control chosen-select">
                <option value="">-</option>
                <?php 
                if (isset($list_tables) && !empty($list_tables)) {
                    foreach ($list_tables as $table) {
                        echo '<option value="' . $table . '"' . (isset($table_name) && $table_name == $table ? ' selected="selected"' : null) . '>' . $table . '</option>' . "\n";
                    }
                }
                unset($list_tables, $table);
                ?> 
            </select>
            <div class="help-block"><?php echo __('dbhelper_generate_properties_help'); ?></div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-10 col-sm-offset-2">
            <button type="submit" class="btn btn-primary"><?php echo __('admin_submit'); ?></button>
        </div>
    </div>
<?php echo \Form::close(); ?> 


<?php 
if (isset($list_columns)) { 
    $total_column = count($list_columns);
?> 
<h3><?php echo __('dbhelper_result'); ?></h3>
<p><?php echo __('dbhelper_short_style_properties'); ?></p>
<pre>
protected static $_properties = array(<?php 
$i = 0;
foreach ($list_columns as $column) {
    echo '\'' . $column['name'] . '\'';
    if ($i+1 < $total_column) {
        echo ', ';
    }
    $i++;
}
unset($column, $i);
?>);
</pre>

<p><?php echo __('dbhelper_full_style_properties'); ?></p>
<pre>
protected static $_properties = array(<?php 
$i = 0;
foreach ($list_columns as $column) {
    echo "\n";
    echo '    \'' . $column['name'] . '\' =&gt; array(' . "\n";
    echo '        \'data_type\' =&gt; \'' . $column['type'] . '\'' . "\n";
    echo '    )';
    if ($i+1 < $total_column) {
        echo ', ';
    } else {
        echo "\n";
    }
    $i++;
}
unset($column, $i, $list_columns);
?>);
</pre>
<?php }// endif; ?> 