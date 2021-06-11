

<!-- MDBootstrap Datatables  -->
<link href="css/addons/datatables2.min.css" rel="stylesheet">
<!-- MDBootstrap Datatables  -->
<script type="text/javascript" src="js/addons/datatables2.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css">

<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.2.min.js"></script>

<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>



<script>
    // Material Design example
    $(document).ready(function() {
        $('#resList').DataTable();
        $('#resList_wrapper').find('label').each(function() {
            $(this).parent().append($(this).children());
        });
        $('#resList_wrapper .dataTables_filter').find('input').each(function() {
            const $this = $(this);
            $this.attr("placeholder", "Search");
            $this.removeClass('form-control-sm');
        });
        $('#resList_wrapper .dataTables_length').addClass('d-flex flex-row');
        $('#resList_wrapper .dataTables_filter').addClass('md-form');
        $('#resList_wrapper select').removeClass('custom-select custom-select-sm form-control form-control-sm');
        $('#resList_wrapper select').addClass('mdb-select');
        $('#resList_wrapper .mdb-select').materialSelect();
        $('#resList_wrapper .dataTables_filter').find('label').remove();
    });
</script>