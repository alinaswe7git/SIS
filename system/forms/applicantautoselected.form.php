<p style=" text-align:center;">
    <a class="btn btn-primary" data-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">View Accepted Bachelor's Applicants</a>
    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#multiCollapseExample2" aria-expanded="false" aria-controls="multiCollapseExample2">View Rejected Bachelor's Applicants</button>
</p>
<div class="row">
    <div class="col">
        <div class="collapse multi-collapse" id="multiCollapseExample1">
            <div class="card card-body">

                <h1 style="background-color:DodgerBlue; text-align:center;">
                    AUTO QUALIFIED BACHELOR APPLICANTS
                </h1>

                <table id="dtMaterialDesignExample" class="table" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="th-sm">#
                            </th>
                            <th class="th-sm">First Name
                            </th>
                            <th class="th-sm">Middle Name
                            </th>
                            <th class="th-sm">Last Name
                            </th>
                            <th class="th-sm">Level
                            </th>
                            <th class="th-sm">Mode of study
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
                                    <td><?php echo $row['middlename']; ?></td>
                                    <td><?php echo $row['lastname']; ?></td>
                                    <td><?php echo $row['level']; ?></td>
                                    <td><?php echo $row['modeofstudy']; ?></td>
                                    <td><?php echo $row['program']; ?></td>

                                </tr>

                        <?php
                            }
                        }

                        ?>

                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="th-sm">#
                            </th>
                            <th class="th-sm">First Name
                            </th>
                            <th class="th-sm">Middle Name
                            </th>
                            <th class="th-sm">Last Name
                            </th>
                            <th class="th-sm">Level
                            </th>
                            <th class="th-sm">Mode of study
                            </th>
                            <th class="th-sm">Program
                            </th>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>
    <br><br>
    <div class="col">
        <div class="collapse multi-collapse" id="multiCollapseExample2">
            <div class="card card-body">

                <h1 style="background-color:red; text-align:center;">
                    BACHELOR APPLICANTS THAT NEED REVIEW
                </h1>
                <table id="rejectedtable" class="table" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="th-sm">#
                            </th>
                            <th class="th-sm">First Name
                            </th>
                            <th class="th-sm">Middle Name
                            </th>
                            <th class="th-sm">Last Name
                            </th>
                            <th class="th-sm">Level
                            </th>
                            <th class="th-sm">Mode of study
                            </th>
                            <th class="th-sm">Program
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        if ($rejected->num_rows > 0) {
                            while ($row = $rejected->fetch_assoc()) {

                        ?>
                                <tr>
                                    <td>
                                        <a href="http://192.168.0.33/sis/admissions/profile/<?php echo $row['applicantno']; ?>">
                                            <?php echo $row['applicantno']; ?>
                                        </a>
                                    </td>
                                    <td><?php echo $row['firstname']; ?></td>
                                    <td><?php echo $row['middlename']; ?></td>
                                    <td><?php echo $row['lastname']; ?></td>
                                    <td><?php echo $row['level']; ?></td>
                                    <td><?php echo $row['modeofstudy']; ?></td>
                                    <td><?php echo $row['program']; ?></td>

                                </tr>

                        <?php
                            }
                        }

                        ?>

                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="th-sm">#
                            </th>
                            <th class="th-sm">First Name
                            </th>
                            <th class="th-sm">Middle Name
                            </th>
                            <th class="th-sm">Last Name
                            </th>
                            <th class="th-sm">Level
                            </th>
                            <th class="th-sm">Mode of study
                            </th>
                            <th class="th-sm">Program
                            </th>
                        </tr>
                    </tfoot>
                </table>



            </div>
        </div>
    </div>
</div>

<p style=" text-align:center;">
    <a class="btn btn-primary" data-toggle="collapse" href="#diplomaAccepted" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">View Accepted Diploma Applicants</a>
    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#diplomaRejected" aria-expanded="false" aria-controls="multiCollapseExample2">View Rejected Diploma Applicants</button>
</p>

<div class="row">
    <div class="col">
        <div class="collapse multi-collapse" id="diplomaAccepted">
            <div class="card card-body">

                <h1 style="background-color:DodgerBlue; text-align:center;">
                    AUTO QUALIFIED DIPLOMA APPLICANTS
                </h1>

                <table id="diplomaAcceptedTable" class="table" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="th-sm">#
                            </th>
                            <th class="th-sm">First Name
                            </th>
                            <th class="th-sm">Middle Name
                            </th>
                            <th class="th-sm">Last Name
                            </th>
                            <th class="th-sm">Level
                            </th>
                            <th class="th-sm">Mode of study
                            </th>
                            <th class="th-sm">Program
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        if ($diplomaAccepted->num_rows > 0) {
                            while ($row = $diplomaAccepted->fetch_assoc()) {

                        ?>
                                <tr>
                                    <td>
                                        <a href="http://192.168.0.33/sis/admissions/profile/<?php echo $row['applicantno']; ?>">
                                            <?php echo $row['applicantno']; ?>
                                        </a>
                                    </td>
                                    <td><?php echo $row['firstname']; ?></td>
                                    <td><?php echo $row['middlename']; ?></td>
                                    <td><?php echo $row['lastname']; ?></td>
                                    <td><?php echo $row['level']; ?></td>
                                    <td><?php echo $row['modeofstudy']; ?></td>
                                    <td><?php echo $row['program']; ?></td>

                                </tr>

                        <?php
                            }
                        }

                        ?>

                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="th-sm">#
                            </th>
                            <th class="th-sm">First Name
                            </th>
                            <th class="th-sm">Middle Name
                            </th>
                            <th class="th-sm">Last Name
                            </th>
                            <th class="th-sm">Level
                            </th>
                            <th class="th-sm">Mode of study
                            </th>
                            <th class="th-sm">Program
                            </th>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>
    <br><br>
    <div class="col">
        <div class="collapse multi-collapse" id="diplomaRejected">
            <div class="card card-body">

                <h1 style="background-color:red; text-align:center;">
                    DIPLOMA APPLICANTS THAT NEED REVIEW
                </h1>
                <table id="diplomaRejectedTable" class="table" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="th-sm">#
                            </th>
                            <th class="th-sm">First Name
                            </th>
                            <th class="th-sm">Middle Name
                            </th>
                            <th class="th-sm">Last Name
                            </th>
                            <th class="th-sm">Level
                            </th>
                            <th class="th-sm">Mode of study
                            </th>
                            <th class="th-sm">Program
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        if ($diplomaRejected->num_rows > 0) {
                            while ($row = $diplomaRejected->fetch_assoc()) {

                        ?>
                                <tr>
                                    <td>
                                        <a href="http://192.168.0.33/sis/admissions/profile/<?php echo $row['applicantno']; ?>">
                                            <?php echo $row['applicantno']; ?>
                                        </a>
                                    </td>
                                    <td><?php echo $row['firstname']; ?></td>
                                    <td><?php echo $row['middlename']; ?></td>
                                    <td><?php echo $row['lastname']; ?></td>
                                    <td><?php echo $row['level']; ?></td>
                                    <td><?php echo $row['modeofstudy']; ?></td>
                                    <td><?php echo $row['program']; ?></td>

                                </tr>

                        <?php
                            }
                        }

                        ?>

                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="th-sm">#
                            </th>
                            <th class="th-sm">First Name
                            </th>
                            <th class="th-sm">Middle Name
                            </th>
                            <th class="th-sm">Last Name
                            </th>
                            <th class="th-sm">Level
                            </th>
                            <th class="th-sm">Mode of study
                            </th>
                            <th class="th-sm">Program
                            </th>
                        </tr>
                    </tfoot>
                </table>



            </div>
        </div>
    </div>
</div>




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
        $('#diplomaRejectedTable').DataTable();
        $('#rejectedtable').DataTable();
        $('#diplomaAcceptedTable').DataTable();

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
