<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!-- /container fluid-->
<div class="container">
    <div class="col-sm-8">

        <div data-spy="scroll" class="tabbable-panel">
            <div class="tabbable-line">
                <ul class="nav nav-tabs ">
                    <li class="active">
                        <a href="#tab_default_1" data-toggle="tab">
                            Personal Information </a>
                    </li>
                    <li>
                        <a href="#tab_default_2" data-toggle="tab">
                            Results</a>
                    </li>
                    <li>
                        <a href="#tab_default_3" data-toggle="tab">
                            Program applied</a>
                    </li>
                    <li>
                        <a href="#tab_default_4" data-toggle="tab">
                            Next of Kin information</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_default_1">

                        <p>
                            Personal information
                        </p>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>First Name:</label>
                                    <p> <?php echo $personal['firstname']; ?> </p>
                                </div>
                                <div class="form-group">
                                    <label>Middle Name:</label>
                                    <p> <?php echo $personal['middlename']; ?> </p>
                                </div>
                                <div class="form-group">
                                    <label>Mobile Number:</label>
                                    <p> <?php echo $personal['mobilephone']; ?> </p>
                                </div>
                                <div class="form-group">
                                    <label>Email:</label>
                                    <p> <?php echo $personal['email']; ?> </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Last Name:</label>
                                    <p> <?php echo $personal['lastname']; ?> </p>
                                </div>
                                <div class="form-group">
                                    <label>Nation Registration Card:</label>
                                    <p> <?php echo $personal['NRCnumber']; ?> </p>
                                </div>
                                <div class="form-group">
                                    <label>Gender:</label>
                                    <p> <?php echo $personal['gender']; ?> </p>
                                </div>
                                <div class="form-group">
                                    <label>Date Of Birth:</label>
                                    <p> <?php echo $personal['dateofbirth']; ?> </p>
                                </div>

                            </div>


                        </div>

                    </div>
                    <div class="tab-pane" id="tab_default_2">

                        <?php
                        if ($olevel->num_rows > 0) {
                            while ($row = $olevel->fetch_assoc()) {
                        ?>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Subject:</label>
                                            <p> <?php echo $row['subject']; ?></p>
                                        </div>
                                        <div class="form-group">
                                            <label>Level:</label>
                                            <p> <?php echo $row['level']; ?></p>
                                        </div>

                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Grade:</label>
                                            <p> <?php echo $row['grade']; ?></p>
                                        </div>

                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>


                    </div>
                    <div class="tab-pane" id="tab_default_3">
                        <p>
                            Program Details
                        </p>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Level :</label>
                                    <p> <?php echo $program['level']; ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Campus :</label>
                                    <p> <?php echo $program['campus']; ?></p>
                                </div>
                                <div class="form-group">
                                    <label>How you heard of Nipa :</label>
                                    <p> <?php echo $program['knowhow']; ?></p>
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Program:</label>
                                    <p> <?php echo $program['program']; ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Mode of Study :</label>
                                    <p> <?php echo $program['modeofstudy']; ?></p>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_default_4">
                        <p>
                            Employment, Next of Kin and Sponsor information

                        </p>

                        <h3>Employment</h3>
                        <hr>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Employer :</label>
                                    <p> <?php echo $employment['employer']; ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Job Title:</label>
                                    <p> <?php echo $employment['jobtitle']; ?></p>
                                </div>

                                <div class="form-group">
                                    <label>Date of Appointment:</label>
                                    <p> pune, maharashtra</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Postal Address:</label>
                                    <p> <?php echo $employment['postaladdress']; ?></p>
                                </div>
                                <div class="form-group">
                                    <label>TelePhone:</label>
                                    <p> <?php echo $employment['telephone']; ?></p>
                                </div>
                            </div>
                        </div>

                        <h3>Next of Kin</h3>
                        <hr>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Full Name :</label>
                                    <p> <?php echo $nextofkin['fullname']; ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Relationship:</label>
                                    <p> <?php echo $nextofkin['relationship']; ?></p>
                                </div>

                                <div class="form-group">
                                    <label>Postal Address:</label>
                                    <p> <?php echo $nextofkin['postaladdress']; ?></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Telephone:</label>
                                    <p> <?php echo $nextofkin['telephone']; ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Email:</label>
                                    <p> <?php echo $nextofkin['email']; ?></p>
                                </div>
                            </div>
                        </div>

                        <h3>Sponsor</h3>
                        <hr>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Sponsor Name :</label>
                                    <p> <?php echo $sponsor['sponsorname']; ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Relationship:</label>
                                    <p> <?php echo $sponsor['relationship']; ?></p>
                                </div>

                                <div class="form-group">
                                    <label>Postal Address:</label>
                                    <p> <?php echo $sponsor['postaladdress']; ?></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Telephone:</label>
                                    <p> <?php echo $sponsor['telephone']; ?></p>
                                </div>
                                <div class="form-group">
                                    <label>Email:</label>
                                    <p> <?php echo $sponsor['email']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <br>
            <form  class="center" action="<?php echo $this->core->conf['conf']['path'] . "/admissions/savestatus/" . $this->core->item; ?> " method="post">
            <label for="intakeyear">SELECT INTAKE</label>
            <select required class="form-control" name="intakeyear" id="ddlYears"></select>
            <br>
            <input type="hidden" name="applicantno" value="<?php echo $applicantno; ?>" >
                <input type="hidden" name="currentuser" value="admin" >
                <input type="submit" name="reject" value="Reject">
                <input type="submit" name="accept" value="Accept">
            </form>
        </div>



    </div>
</div>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript">
    $(function () {
        //Reference the DropDownList.
        var ddlYears = $("#ddlYears");
 
        //Determine the Current Year.
        var currentYear = (new Date()).getFullYear();
 
        //Loop and add the Year values to DropDownList.
        for (var i = 2021; i <= 2030; i++) {
            var option = $("<option />");
            option.html(i);
            option.val(i);
            ddlYears.append(option);
        }
    });
</script>

<style>
    body {
        font-family: 'Open Sans', sans-serif;
    }



    /***
Bootstrap Line Tabs by @keenthemes
A component of Metronic Theme - #1 Selling Bootstrap 3 Admin Theme in Themeforest: http://j.mp/metronictheme
Licensed under MIT
***/

    /* Tabs panel */
    .tabbable-panel {
        border: 1px solid #eee;
        padding: 10px;
    }

    /* Default mode */
    .tabbable-line>.nav-tabs {
        border: none;
        margin: 0px;
    }

    .tabbable-line>.nav-tabs>li {
        margin-right: 2px;
    }

    .tabbable-line>.nav-tabs>li>a {
        border: 0;
        margin-right: 0;
        color: #737373;
    }

    .tabbable-line>.nav-tabs>li>a>i {
        color: #a6a6a6;
    }

    .tabbable-line>.nav-tabs>li.open,
    .tabbable-line>.nav-tabs>li:hover {
        border-bottom: 4px solid #fbcdcf;
    }

    .tabbable-line>.nav-tabs>li.open>a,
    .tabbable-line>.nav-tabs>li:hover>a {
        border: 0;
        background: none !important;
        color: #333333;
    }

    .tabbable-line>.nav-tabs>li.open>a>i,
    .tabbable-line>.nav-tabs>li:hover>a>i {
        color: #a6a6a6;
    }

    .tabbable-line>.nav-tabs>li.open .dropdown-menu,
    .tabbable-line>.nav-tabs>li:hover .dropdown-menu {
        margin-top: 0px;
    }

    .tabbable-line>.nav-tabs>li.active {
        border-bottom: 4px solid #f3565d;
        position: relative;
    }

    .tabbable-line>.nav-tabs>li.active>a {
        border: 0 !important;
        color: #333333;
    }

    .tabbable-line>.nav-tabs>li.active>a>i {
        color: #404040;
    }

    .tabbable-line>.tab-content {
        margin-top: -3px;
        background-color: #fff;
        border: 0;
        border-top: 1px solid #eee;
        padding: 15px 0;
    }

    .portlet .tabbable-line>.tab-content {
        padding-bottom: 0;
    }

    /* Below tabs mode */

    .tabbable-line.tabs-below>.nav-tabs>li {
        border-top: 4px solid transparent;
    }

    .tabbable-line.tabs-below>.nav-tabs>li>a {
        margin-top: 0;
    }

    .tabbable-line.tabs-below>.nav-tabs>li:hover {
        border-bottom: 0;
        border-top: 4px solid #fbcdcf;
    }

    .tabbable-line.tabs-below>.nav-tabs>li.active {
        margin-bottom: -2px;
        border-bottom: 0;
        border-top: 4px solid #f3565d;
    }

    .tabbable-line.tabs-below>.tab-content {
        margin-top: -10px;
        border-top: 0;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    }

    .menu_title {
        padding: 15px 10px;
        border-bottom: 1px solid #eee;
        margin: 0 5px;
    }


    @media (max-width:768px) {

        .fb-profile-text>h1 {
            font-weight: 700;
            font-size: 16px;
        }

        .fb-image-profile {
            margin: -45px 10px 0px 25px;
            z-index: 9;
            width: 20%;
        }
    }
</style>
