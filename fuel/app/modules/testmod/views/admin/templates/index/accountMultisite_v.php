<h1>Test delete account on multisite table</h1>

<div class="form-result-placeholder"></div>
<button type="button" class="btn btn-default" onclick="createMainSiteTable();">Create table on main site.</button>
<button type="button" class="btn btn-default" onclick="insertDemoData();">Insert demo data.</button>
<button type="button" class="btn btn-default" onclick="reloadDemoData();">Reload demo data.</button>
<button type="button" class="btn btn-default" onclick="dropTable();">Drop table.</button>

<h3>Demo data:</h3>
<div class="inserted-demo-data-placeholder"></div>


<script>
    function createMainSiteTable() {
        $.ajax({
            url: site_url+'testmod/admin/index/accountMultisite',
            type: 'POST',
            data: csrf_name+'='+nocsrf_val+'&act=createmaintable',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if (typeof(data) != 'undefined' && typeof(data.result) != 'undefined' && data.result === true) {
                    $('.form-result-placeholder').html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>Main table has been created.</div>');
                }
            }
        });
    }// createMainSiteTable


    function dropTable() {
        confirm_val = confirm('Are you sure that you want to drop demo table?');
        if (confirm_val === true) {
            $.ajax({
                url: site_url+'testmod/admin/index/accountMultisite',
                type: 'POST',
                data: csrf_name+'='+nocsrf_val+'&act=droptable',
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    if (typeof(data) != 'undefined' && typeof(data.result) != 'undefined' && data.result === true) {
                        $('.form-result-placeholder').html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>Demo tables on all site has been dropped.</div>');
                        $('.inserted-demo-data-placeholder').html('');
                    }
                }
            });
        }
    }// dropTable


    function insertDemoData() {
        confirm_val = confirm('Before you insert demo data please create main site table, create some account more than just super admin and guest, create new site.');
        if (confirm_val === true) {
            $.ajax({
                url: site_url+'testmod/admin/index/accountMultisite',
                type: 'POST',
                data: csrf_name+'='+nocsrf_val+'&act=insertdemodata',
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    if (typeof(data) != 'undefined' && typeof(data.result) != 'undefined' && data.result === true) {
                        $('.form-result-placeholder').html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>Demo data inserted. Now, please remove test account that you created and reload data to see that this works.</div>');
                    }
                    if (typeof(data.tables_data) != 'undefined') {
                        display_table = '';
                        $.each(data.tables_data, function(table, rows) {
                            display_table += '<h4>table: '+table+'</h4>';
                            display_table += '<table class="table table-hover">';
                            display_table += '<tr><th>account id</th><th>timestamp</th></tr>';
                            $.each(rows, function(field, val) {
                                display_table += '<tr>';
                                display_table += '<td><a href="'+site_url+'admin/account/edit/'+val.account_id+'" target="_blank">'+val.account_id+'</a></td>';
                                display_table += '<td>'+val.actdate+'</td>';
                                display_table += '</tr>';
                            });
                            display_table += '</table>';
                        });
                        $('.inserted-demo-data-placeholder').html(display_table);
                    }
                }
            });
        }
    }// insertDemoData


    function reloadDemoData() {
        $.ajax({
            url: site_url+'testmod/admin/index/accountMultisite',
            type: 'POST',
            data: csrf_name+'='+nocsrf_val+'&act=loaddemodata',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if (typeof(data) != 'undefined' && typeof(data.result) != 'undefined' && data.result === true) {
                    $('.form-result-placeholder').html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>Please remove test account that you created and reload data to see that this works.</div>');
                }
                if (typeof(data.tables_data) != 'undefined') {
                    display_table = '';
                    $.each(data.tables_data, function(table, rows) {
                        display_table += '<h4>table: '+table+'</h4>';
                        display_table += '<table class="table table-hover">';
                        display_table += '<tr><th>account id</th><th>timestamp</th></tr>';
                        $.each(rows, function(field, val) {
                            display_table += '<tr>';
                            display_table += '<td><a href="'+site_url+'admin/account/edit/'+val.account_id+'" target="_blank">'+val.account_id+'</a></td>';
                            display_table += '<td>'+val.actdate+'</td>';
                            display_table += '</tr>';
                        });
                        display_table += '</table>';
                    });
                    $('.inserted-demo-data-placeholder').html(display_table);
                }
            }
        });
    }// reloadDemoData
</script>