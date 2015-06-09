<h1>Test module plugin</h1>

<div class="row">
    <div class="col-sm-6">
        <h3>Account hook</h3>
        <ul>
            <li><?php echo \Html::anchor('testmod/admin/index/account', 'Test account apis'); ?></li>
            <li><?php echo \Html::anchor('testmod/admin/index/accountMultisite', 'Test delete account on multisite table'); ?></li>
        </ul>
    </div>
    <div class="col-sm-6">
        <h3>Multisite hook</h3>
        <p>Try to <?php echo \Html::anchor('testmod/admin/index/accountMultisite', '<strong>create table</strong>'); ?> in this link first, then try to <strong>create new site</strong> and <strong>reload data</strong> in that link to see if table created. (<em>X_testmultisiteaccount</em> X is numbers).</p>
    </div>
</div>