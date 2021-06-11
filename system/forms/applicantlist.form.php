<table id="dtMaterialDesignExample" class="table" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th class="th-sm">#
            </th>
            <th class="th-sm">First Name
            </th>
            <th class="th-sm">Last Name
            </th>
            <th class="th-sm">Gender
            </th>
            <th class="th-sm">NRC #
            </th>
            <th class="th-sm">Email
            </th>
            <th class="th-sm">Program
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($applicant->num_rows > 0) {
            while ($row = $applicant->fetch_assoc()) {
        ?>
                <tr>
                    <td>
                        <a href="http://192.168.0.33/sis/admissions/profile/<?php echo $row['applicantno']; ?>">
                        <?php echo $row['applicantno']; ?>
                    </a>
                    </td>
                    <td><?php echo $row['firstname']; ?></td>
                    <td><?php echo $row['lastname']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td><?php echo $row['NRCnumber']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['program']; ?></td>

                </tr>

        <?php
            }
        }
        ?>

    </tbody>
    <tfoot>
        <tr>
            <th>Name
            </th>
            <th>Position
            </th>
            <th>Office
            </th>
            <th>Age
            </th>
            <th>Start date
            </th>
            <th>Salary
            </th>
        </tr>
    </tfoot>
</table>

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
        $('#dtMaterialDesignExample').DataTable();
        $('#dtMaterialDesignExample_wrapper').find('label').each(function() {
            $(this).parent().append($(this).children());
        });
        $('#dtMaterialDesignExample_wrapper .dataTables_filter').find('input').each(function() {
            const $this = $(this);
            $this.attr("placeholder", "Search");
            $this.removeClass('form-control-sm');
        });
        $('#dtMaterialDesignExample_wrapper .dataTables_length').addClass('d-flex flex-row');
        $('#dtMaterialDesignExample_wrapper .dataTables_filter').addClass('md-form');
        $('#dtMaterialDesignExample_wrapper select').removeClass('custom-select custom-select-sm form-control form-control-sm');
        $('#dtMaterialDesignExample_wrapper select').addClass('mdb-select');
        $('#dtMaterialDesignExample_wrapper .mdb-select').materialSelect();
        $('#dtMaterialDesignExample_wrapper .dataTables_filter').find('label').remove();
    });
</script>
