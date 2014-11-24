<h1>jQuery UI Dialog</h1>
<p>Warning! The dialog does not work perfectly because it was conflicted with Bootstrap's js.</p>


<div class="dialog">Hi, Hello!</div>


<script>
    $(function() {
        $('.dialog').dialog({
            title: 'Hello dialog.'
        });
    });
</script>