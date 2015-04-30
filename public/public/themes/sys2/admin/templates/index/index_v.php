<h1><?php echo __('admin_administrator_dashbord'); ?></h1>

<div class="form-status-placeholder">
    <?php if (isset($form_status) && isset($form_status_message)) { ?> 
    <div class="alert alert-<?php echo str_replace('error', 'danger', $form_status); ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
    <?php } ?> 
</div>


<!--This page is just example. You can clear it all and start with your code/design.-->

<div class="row">
    <div class="col-xs-12">
        <div class="dashboard-block dashboard-block-introduce">
            <h2>Welcome to Fuel Start admin dashboard.</h2>
            <p>You can start modify this controller and theme to build your own admin dashboard.</p>
            <p>The theme of this page is located at <?php echo __FILE__; ?></p>
            <p>The controller of this page is located at <?php echo APPPATH . 'classes' . DS . 'controller' . DS . 'admin' . DS . 'index.php'; ?></p>
        </div>
    </div><!--.col-->
</div><!--.row-->
<div class="row">
    <div class="col-sm-4">
        <div class="dashboard-block dashboard-block-account">
            <h2><?php echo \Html::anchor('admin/account', __('index_account')); ?></h2>
            <p><?php echo __('index_total_accounts', array('total_accounts' => (isset($total_accounts) ? $total_accounts : 0))); ?></p>
        </div>
    </div><!--.col-->
    <div class="col-sm-4">
        <div class="dashboard-block dashboard-block-newsfeed">
            <h2>News feed <small>(example)</small></h2>
            <ul>
                <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
                <li>Aenean eleifend libero ac velit vestibulum, nec bibendum est auctor.</li>
                <li>Morbi viverra tellus id dolor accumsan consequat.</li>
                <li>Suspendisse ut sollicitudin turpis.</li>
                <li>Morbi vitae mi mauris.</li>
                <li>Aliquam ornare lorem mauris, quis vehicula mi faucibus quis.</li>
                <li>Maecenas consectetur enim at placerat maximus.</li>
                <li>Cras ac nibh augue.</li>
            </ul>
        </div>
    </div><!--.col-->
    <div class="col-sm-4">
        <div class="dashboard-block dashboard-block-stats">
            <h2>Statistic <small>(example)</small></h2>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>IP Address</th>
                            <th>Date/time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>192.168.0.102</td>
                            <td>2014-10-22 14:31:18</td>
                        </tr>
                        <tr>
                            <td>127.0.0.1</td>
                            <td>2014-10-22 12:15:09</td>
                        </tr>
                        <tr>
                            <td>127.0.0.1</td>
                            <td>2014-10-22 12:11:09</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!--.col-->
</div><!--.row-->

