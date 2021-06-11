<?php
class startregistration
{

        public $core;
        public $view;

        public function configView()
        {
                $this->view->open = TRUE;
                $this->view->header = TRUE;
                $this->view->footer = TRUE;
                $this->view->menu = FALSE;
                $this->view->internalMenu = TRUE;
                $this->view->javascript = array();
                $this->view->css = array();

                return $this->view;
        }

        public function buildView($core)
        {
                $this->core = $core;
                //change the back ground color from defualt to transparent
                echo '<style>
			.bodycon {
				background-color: #1C00ff00;
			}
			.contentwrapper {
				padding: 20px;
			}
		</style>';
        }

        public function logoutStartregistration($item)
        {
                session_unset();
                session_destroy();

                include $this->core->conf['conf']['formPath'] . "registrationlogin.form.php";
        }

        //displays PERSONAL INFORMATION of online registration...................................
        public function personalStartregistration($item)
        {
                // Always start thils first

                if (isset($_SESSION['applicant_id'])) {
                        // Grab user data from the database using the user_id
                        // Let them access the "logged in only" pages
                        include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

                        $select = new optionBuilder($this->core);
                        $country = $select->showCountry();

                        //login id
                        $applicant_id = $_SESSION['applicant_id'];

                        //check if the application id exists
                        $sql = "SELECT * FROM `appl_personal` WHERE `appl_personal`.applicantno = '$applicant_id' ";

                        $run = $this->core->database->doSelectQuery($sql);



                        if ($fetch = $run->fetch_assoc()) {
                                echo '<script>alert("Record already exist");</script>';
                        }

                        include $this->core->conf['conf']['formPath'] . "registrationpersonal.form.php";
                        echo $_SESSION['applicant_id'];
                } else {
                        // Redirect them to the login page

                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/login/"; </script>';
                }
        }

        public function savepersonalStartregistration()
        {

                // // remove all session variables
                // session_unset();
                // session_destroy();

                $applicant_id = $_SESSION['applicant_id'];

                // Next of Kin details
                $first_name = $this->core->cleanPost['first_name'];
                $last_name = $this->core->cleanPost['last_name'];
                $middle_name = $this->core->cleanPost['middle_name'];
                $nrc = $this->core->cleanPost['nrc'];
                $title = $this->core->cleanPost['title'];

                $dob = $this->core->cleanPost['dob'];
                $gender = $this->core->cleanPost['gender'];
                $maritalstatus = $this->core->cleanPost['maritalstatus'];
                $nationality = $this->core->cleanPost['nationality'];
                $country_of_residence = $this->core->cleanPost['country_of_residence'];

                $place_of_birth = $this->core->cleanPost['place_of_birth'];
                $residencial_address = $this->core->cleanPost['residencial_address'];
                $mobile_number = $this->core->cleanPost['mobile_number'];
                $telephone = $this->core->cleanPost['telephone'];
                $fax = $this->core->cleanPost['fax'];

                $postal_address = $this->core->cleanPost['postal_address'];
                $residencial_address = $this->core->cleanPost['residencial_address'];
                $mobile_number = $this->core->cleanPost['mobile_number'];
                $telephone = $this->core->cleanPost['telephone'];
                $fax = $this->core->cleanPost['fax'];

                $email = $this->core->cleanPost['email'];
                $disability = $this->core->cleanPost['disability'];
                $datecreated = $this->core->cleanPost['datecreated'];


                //check if the application id exists
                $sql = "SELECT * FROM `appl_personal` WHERE `appl_personal`.applicantno = '$applicant_id' ";

                $run = $this->core->database->doSelectQuery($sql);



                if ($fetch = $run->fetch_assoc()) {
                        echo '<script>alert("Personal Record already exists.\n RECORD WILL BE UPDATED");</script>';

                        //updating the program records
                        $sql = "UPDATE `appl_personal` SET `applicantno` = '$applicant_id', `NRCnumber` = '$nrc', `lastname` = '$last_name', `firstname` = '$first_name',`middlename` = '$middle_name', `title` = '$title', `dateofbirth` = '$dob', `placeofbirth` = '$place_of_birth',`gender` = '$gender', `maritalstatus` = '$maritalstatus', `nationality` = '$nationality', `countryofresidence` = '$country_of_residence',`postaladdress` = '$postal_address', `residentialaddress` = '$residencial_address', `mobilephone` = '$mobile_number', `telephone` = '$telephone',`fax` = '$fax', `email` = '$email', `disability` = '$disability',`dateofcreation` = '$datecreated'
               WHERE `applicantno`= $applicant_id ;";

                        if ($this->core->database->doInsertQuery($sql)) {
                                echo '<div class="alert alert-success" role="alert"> <strong>Success</strong> Saved! </div>';
                                echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/kin/"; </script>';
                                echo '<script>alert("RECORD SUCCESSFULLY UPDATED");</script>';
                        } else {
                                //echo $sql.error;
                                echo '<div class="successpopup">Error in updating program information .<br/> CHECK THE DETAILS! or Contact Admin</div>';
                        }
                } else {


                        $sql = "INSERT INTO `appl_personal` (`applicantno`, `NRCnumber`, `lastname`, `firstname`,`middlename`, `title`, `dateofbirth`, `placeofbirth`,`gender`, `maritalstatus`, `nationality`, `countryofresidence`,`postaladdress`, `residentialaddress`, `mobilephone`, `telephone`,`fax`, `email`, `disability`,`dateofcreation`)
                                VALUES ('$applicant_id','$nrc', '$last_name', '$first_name','$middle_name','$title', '$dob', '$place_of_birth','$gender','$maritalstatus', '$nationality', '$country_of_residence','$postal_address','$residencial_address', '$mobile_number', '$telephone','$fax','$email', '$disability', '$datecreated');";

                        if ($this->core->database->doInsertQuery($sql)) {
                                echo '<div class="successpopup">The requested user account has been created.<br/> WRITE THE FOLLOWING INFORMATION DOWN OR REMEMBER IT!</div>';
                                echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/kin/"; </script>';
                        } else {
                                //used to check the error with the sql query
                                //echo $sql . error;
                                echo '<script>alert("Personal information for this applicant already exists")</script>';

                                echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/summery/"; </script>';
                        }
                }
        }


        //displays the secetion where users can upload files related to registration...................
         public function uploadStartregistration($item)
        {
                // Always start thils first

                if (isset($_SESSION['applicant_id'])) {

                        //login id
                        $applicant_id = $_SESSION['applicant_id'];

                        //check if the application id exists
                        $sql = "SELECT * FROM `appl_attachments` WHERE `appl_attachments`.applicantno = '$applicant_id' ";

                        $run = $this->core->database->doSelectQuery($sql);

                        if ($fetch = $run->fetch_assoc()) {
                                echo '<script>alert("Record already exist");</script>';
                        }

                        include $this->core->conf['conf']['formPath'] . "registrationfiles.form.php";
                        echo $_SESSION['applicant_id'];
                } else {
                        // Redirect them to the login page

                        include $this->core->conf['conf']['formPath'] . "registrationlogin.form.php";
                }
        }

        //..............................................................................................

        public function savefilesStartregistration()
        {

                //session id to show that the user is login
                $applicant_id = $_SESSION['applicant_id'];
                $txt = "image";

                //deposit file..................................................................

                // name of the uploaded file
                $depositfilename = $_FILES['depositslip']['name'];
                $grade12filename = $_FILES['grade_12_certificate']['name'];
                $passportnrcfilename = $_FILES['passport_nrc']['name'];
                $qualificationfilename = $_FILES['qualification']['name'];
                $qualification1filename = $_FILES['qualification1']['name'];
                $referencefilename = $_FILES['reference']['name'];

                // the physical file on a temporary uploads directory on the server
                $depositfile = $_FILES['depositslip']['tmp_name'];
                $depositsize = $_FILES['depositslip']['size'];

                $grade12file = $_FILES['grade_12_certificate']['tmp_name'];
                $grade12size = $_FILES['grade_12_certificate']['size'];

                $passportnrcfile = $_FILES['passport_nrc']['tmp_name'];
                $passportnrcsize = $_FILES['passport_nrc']['size'];

                $qualificationfile = $_FILES['qualification']['tmp_name'];
                $qualificationsize = $_FILES['qualification']['size'];

                $qualification1file = $_FILES['qualification1']['tmp_name'];
                $qualification1size = $_FILES['qualification1']['size'];

                $referencefile = $_FILES['reference']['tmp_name'];
                $referencesize = $_FILES['reference']['size'];

                // destination of the file on the server
                $depositdestination = 'uploads/' . $depositfilename;
                $grade12destination = 'uploads/' . $grade12filename;
                $passprtnrcdestination = 'uploads/' . $passportnrcfilename;
                $qualificationdestination = 'uploads/' . $qualificationfilename;
                $qualification1destination = 'uploads/' . $qualification1filename;
                $referencedestination = 'uploads/' . $referencefilename;

                echo "depos " . $depositfilename;
                echo "g12 " . $grade12filename;
                echo "pass " . $passportnrcfilename;
                echo "q1 " . $qualificationfilename;
                echo "q2 " . $qualification1filename;
                echo "ref " . $referencefilename;

                // get the file extension
                //$depositextension = pathinfo($depositdestination, PATHINFO_EXTENSION);
                //$grade12extension = pathinfo($grade12destination, PATHINFO_EXTENSION);
                //$passprtnrcextension = pathinfo($passprtnrcdestination, PATHINFO_EXTENSION);
                //$qualificationextension = pathinfo($qualificationdestination, PATHINFO_EXTENSION);
                //$qualification1extension = pathinfo($qualification1destination, PATHINFO_EXTENSION);
                //$referenceextension = pathinfo($referencedestination, PATHINFO_EXTENSION);



                if (!in_array($depositextension, ['zip', 'pdf', 'docx'])) {
                        echo "You file extension must be .zip, .pdf or .docx";
                } elseif ($_FILES['depositslip']['size'] > 1000000) { // file shouldn't be larger than 1Megabyte
                        echo "File too large!";
                } else {
                        // move the uploaded (temporary) file to the specified destination
                        if (move_uploaded_file($depositfile, $depositdestination)) {
                                echo "Passport or nrc File uploaded successfully";
                        } else {
                                echo "Failed to upload Passport or nrc file.";
                        }
                }


                if (!in_array($grade12extension, ['zip', 'pdf', 'docx'])) {
                        echo "You file extension must be .zip, .pdf or .docx";
                } elseif ($_FILES['depositslip']['size'] > 1000000) { // file shouldn't be larger than 1Megabyte
                        echo "File too large!";
                } else {
                        // move the uploaded (temporary) file to the specified destination
                        if (move_uploaded_file($grade12file, $grade12destination)) {
                                echo "Grade 12 File uploaded successfully";
                        } else {
                                echo "Failed to upload Grade 12 file.";
                        }
                }

                if (!in_array($passprtnrcextension, ['zip', 'pdf', 'docx'])) {
                        echo "You file extension must be .zip, .pdf or .docx";
                } elseif ($_FILES['passport_nrc']['size'] > 1000000) { // file shouldn't be larger than 1Megabyte
                        echo "File too large!";
                } else {
                        // move the uploaded (temporary) file to the specified destination
                        if (move_uploaded_file($passportnrcfile, $passprtnrcdestination)) {
                                echo "Passport or nrc File uploaded successfully";
                        } else {
                                echo "Failed to upload Passport or nrc file.";
                        }
                }

                if (!in_array($qualificationextension, ['zip', 'pdf', 'docx'])) {
                        echo "You file extension must be .zip, .pdf or .docx";
                } elseif ($_FILES['qualification']['size'] > 1000000) { // file shouldn't be larger than 1Megabyte
                        echo "File too large!";
                } else {
                        // move the uploaded (temporary) file to the specified destination
                        if (move_uploaded_file($qualificationfile, $qualification1destination)) {
                                echo "Qualification File uploaded successfully";
                        } else {
                                echo "Failed to upload Qualification file.";
                        }
                }


                if (!in_array($qualification1extension, ['zip', 'pdf', 'docx'])) {
                        echo "You file extension must be .zip, .pdf or .docx";
                } elseif ($_FILES['qualification1']['size'] > 1000000) { // file shouldn't be larger than 1Megabyte
                        echo "File too large!";
                } else {
                        // move the uploaded (temporary) file to the specified destination
                        if (move_uploaded_file($qualification1file, $qualification1destination)) {
                                echo "Qualification 1 File uploaded successfully";
                        } else {
                                echo "Failed to upload Qualification 1 file.";
                        }
                }

                if (!in_array($referenceextension, ['zip', 'pdf', 'docx'])) {
                        echo "You file extension must be .zip, .pdf or .docx";
                } elseif ($_FILES['reference']['size'] > 1000000) { // file shouldn't be larger than 1Megabyte
                        echo "File too large!";
                } else {
                        // move the uploaded (temporary) file to the specified destination
                        if (move_uploaded_file($referencefile, $referencedestination)) {
                                echo "Reference File uploaded successfully";
                        } else {
                                echo "Failed to upload Reference file.";
                        }
                }

                //check if the application id exists
                $sql = "SELECT * FROM `appl_attachments` WHERE `appl_attachments`.applicantno = '$applicant_id' ";

                $run = $this->core->database->doSelectQuery($sql);

                if ($fetch = $run->fetch_assoc()) {
                        echo '<script>alert("Record already exist.\n RECORD WILL BE UPDATED");</script>';

                        $sql = "UPDATE `appl_attachments` SET `applicantno` = '$applicant_id', `attachtype` = '$txt', `depositslip` = '$depositfilename', `grade12certificate` = '$grade12filename',`passportornrc` = '$passportnrcfilename', `academic_professional_qualification` = '$qualificationfilename', `academic_professional_qualification_2` = '$qualification1filename', `reference` = '$referencefilename'
                        WHERE `applicantno`= $applicant_id ;";

                        if ($this->core->database->doInsertQuery($sql)) {
                                echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/summery/"; </script>';
                                echo '<div class="successpopup">done loading files.<br/> WRITE THE FOLLOWING INFORMATION DOWN OR REMEMBER IT!</div>';
                                echo '<script>alert("RECORD SUCCESSFULLY UPDATED");</script>';
                        } else {
                                echo $sql . error();
                                echo '<div class="successpopup">failed to load the files .<br/> CHECK THE DETAILS!</div>';
                                echo '<script> alert("Failed to submit files"); </script>';
                                echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/upload/"; </script>';
                        }
                } else {

                        $sql = "INSERT INTO `appl_attachments` (`applicantno`, `attachtype`, `depositslip`,`grade12certificate`, `passportornrc`, `academic_professional_qualification`, `academic_professional_qualification_2`, `reference`)
                        VALUES ('$applicant_id', '$txt', '$depositfilename', '$grade12filename','$passportnrcfilename','$qualificationfilename','$qualification1filename','$referencefilename');";

                        if ($this->core->database->doInsertQuery($sql)) {
                                echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/summery/"; </script>';
                                echo '<div class="successpopup">done loading files.<br/> WRITE THE FOLLOWING INFORMATION DOWN OR REMEMBER IT!</div>';
                        } else {
                                echo $sql . error();
                                echo '<div class="successpopup">failed to load the files .<br/> CHECK THE DETAILS!</div>';
                                echo '<script> alert("Failed to submit files"); </script>';
                                echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/upload/"; </script>';
                        }
                }


                // $fileExt = explode('.', $depositfilename);
                // $fileActualExt = strtolower(end($fileExt));

                // $allowed = array('pdf', 'jpg', 'png', 'jpeg');

                // if (in_array($fileActualExt, $allowed)) {

                //         if ($depositError === 0) {

                //                 if ($depositFileSize < 500000) {
                //                         $depositfilename = uniqid('', true) . "." . $fileActualExt;
                //                         $fileDestination = 'uploads/' . $depositfilename;
                //                         move_uploaded_file($depositTmpName, $fileDestination);
                //                         echo "Successfully ";
                //                 } else {
                //                         echo "Your file is to big";
                //                 }
                //         } else {
                //         }
                // } else {
                //         echo "You cannot upload files of this type!";
                // }

                // //$depositslip = $this->core->cleanPost['depositslip'];
                // $grade_12_certificate = $this->core->cleanPost['grade_12_certificate'];
                // $passport_nrc = $this->core->cleanPost['passport_nrc'];
                // $qualification = $this->core->cleanPost['qualification'];
                // $qualification1 = $this->core->cleanPost['qualification1'];
                // $reference = $this->core->cleanPost['reference'];

                // $sql = "INSERT INTO `appl_attachments` (`applicantno`, `attachtype`, `depositslip`,`grade12certificate`, `passportornrc`, `academic_professional_qualification`, `academic_professional_qualification_2`, `reference`)
                //         VALUES ('$applicant_id', '$txt', '$depositslip', '$grade_12_certificate','$passport_nrc','$qualification','$qualification1','$reference');";

                // if ($this->core->database->doInsertQuery($sql)) {
                //         echo '<div class="successpopup">done loading files.<br/> WRITE THE FOLLOWING INFORMATION DOWN OR REMEMBER IT!</div>';
                // } else {
                //         echo $sql.error();
                //         echo '<div class="successpopup">failed to load the files .<br/> CHECK THE DETAILS!</div>';
                // }
        }


        //displays the sponsor and next of kin page of the online registration................................
        public function kinStartregistration($item)
        {
                // Always start thils first


                if (isset($_SESSION['applicant_id'])) {
                        // Grab user data from the database using the user_id
                        // Let them access the "logged in only" pages

                        $applicant_id = $_SESSION['applicant_id'];

                        include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

                        $select = new optionBuilder($this->core);
                        $applicantid = $select->showKin($applicant_id);
                        $nn = $applicantid;

                        if (print $nn == $applicant_id) {
                                //echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/program/"; </script>';
                        } elseif (print $nn == $applicant_id) {
                                # code...
                        }


                        //check if the application id exists
                        $sql = "SELECT * FROM `appl_nextofkin` WHERE `appl_nextofkin`.applicantno = '$applicant_id' ";
                        $sql1 = "SELECT * FROM `appl_sponsor` WHERE `appl_sponsor`.applicantno = '$applicant_id' ";

                        $run = $this->core->database->doSelectQuery($sql);
                        $run1 = $this->core->database->doSelectQuery($sql1);

                        if ($fetch = $run->fetch_assoc()) {
                                echo '<script>alert("Record already exist");</script>';
                        } else {
                                
                        include $this->core->conf['conf']['formPath'] . "registration1.form.php";
                        echo $_SESSION['applicant_id'];
                        }
                } else {
                        // Redirect them to the login page

                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/login/"; </script>';
                }
        }

        //this is called when the user hits the next button of the first page of the registration page
        //to enable the data to be saved to the session
        public function savekinStartregistration()
        {

                if ($_SESSION['applicant_id'] != null) {

                        $applicant_id = $_SESSION['applicant_id'];
                        // Next of Kin details
                        $nextofkin_fullname = $this->core->cleanPost['nextofkin_fullname'];
                        $nextofkin_relationship = $this->core->cleanPost['nextofkin_relationship'];
                        $nextofkin_postaladdress = $this->core->cleanPost['nextofkin_postaladdress'];
                        $nextofkin_telephone = $this->core->cleanPost['nextofkin_telephone'];
                        $nextofkin_email = $this->core->cleanPost['nextofkin_email'];
                        $nextofkin_town = $this->core->cleanPost['nextofkin_town'];

                        //check if the application id exists
                        $sql = "SELECT * FROM `appl_nextofkin` WHERE `appl_nextofkin`.applicantno = '$applicant_id' ";

                        $run = $this->core->database->doSelectQuery($sql);



                        if ($fetch = $run->fetch_assoc()) {
                                echo '<script>alert("Records already exist.\n RECORD WILL BE UPDATED");</script>';

                                //updating the program records
                                $sql = "UPDATE `appl_nextofkin` SET `applicantno` = $applicant_id, `fullname` = '$nextofkin_fullname', `relationship` = '$nextofkin_relationship', `postaladdress` = '$nextofkin_postaladdress', `telephone` = '$nextofkin_telephone', `email` = '$nextofkin_email' , `town` = '$nextofkin_town'
               WHERE `applicantno`= $applicant_id ;";

                                if ($this->core->database->doInsertQuery($sql)) {
                                        //echo '<div class="alert alert-success" role="alert"> <strong>Success</strong> Saved! </div>';
                                        // echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/personal/"; </script>';
                                        //echo '<script>alert("RECORD SUCCESSFULLY UPDATED");</script>';
                                } else {
                                        //echo $sql.error;
                                        echo '<div class="successpopup">Error in updating information .<br/> CHECK THE DETAILS! or Contact Admin</div>';
                                }
                        } else {

                                //load the data for the next of kin in the database
                                $sql = "INSERT INTO `appl_nextofkin` (`applicantno`, `fullname`, `relationship`,`postaladdress`, `telephone`, `email`, `town`)
                        VALUES ('$applicant_id', '$nextofkin_fullname', '$nextofkin_relationship', '$nextofkin_postaladdress','$nextofkin_telephone','$nextofkin_email','$nextofkin_town');";

                                if ($this->core->database->doInsertQuery($sql)) {
                                        //echo '<script> alert("test"); </script>';
                                } else {
                                        //echo $sql.error;

                                }
                        }

                        // Adding variables to session
                        $_SESSION['nextofkin_fullname'] = $nextofkin_fullname;
                        $_SESSION['nextofkin_relationship'] = $nextofkin_relationship;
                        $_SESSION['nextofkin_postaladdress'] = $nextofkin_postaladdress;
                        $_SESSION['nextofkin_telephone'] = $nextofkin_telephone;
                        $_SESSION['nextofkin_email'] = $nextofkin_email;


                        // Sponsor details
                        $sponsor_sponsorname = $this->core->cleanPost['sponsor_sponsorname'];
                        $sponsor_relationship = $this->core->cleanPost['sponsor_relationship'];
                        $sponsor_telephone = $this->core->cleanPost['sponsor_telephone'];
                        $sponsor_postaladdress = $this->core->cleanPost['sponsor_postaladdress'];
                        $sponsor_email = $this->core->cleanPost['sponsor_email'];

                        //check if the application id exists
                        $sql = "SELECT * FROM `appl_sponsor` WHERE `appl_sponsor`.applicantno = '$applicant_id' ";

                        $run = $this->core->database->doSelectQuery($sql);



                        if ($fetch = $run->fetch_assoc()) {
                                //echo '<script>alert("Record already exist.\n RECORD WILL BE UPDATED");</script>';

                                //updating the program records
                                $sql = "UPDATE `appl_sponsor` SET `applicantno` = $applicant_id, `sponsorname` = '$sponsor_sponsorname', `relationship` = '$sponsor_relationship', `postaladdress` = '$sponsor_postaladdress', `telephone` = '$sponsor_telephone', `email` = '$sponsor_email'
               WHERE `applicantno`= $applicant_id ;";

                                if ($this->core->database->doInsertQuery($sql)) {
                                        // echo '<div class="alert alert-success" role="alert"> <strong>Success</strong> Saved! </div>';
                                        // echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/personal/"; </script>';
                                        //echo '<script>alert("RECORD SUCCESSFULLY UPDATED");</script>';
                                } else {
                                        //echo $sql.error;
                                        echo '<div class="successpopup">Error in updating information .<br/> CHECK THE DETAILS! or Contact Admin</div>';
                                }
                        } else {

                                //load the data for the next of sponsor in the database
                                $sql = "INSERT INTO `appl_sponsor` (`applicantno`, `sponsorname`, `relationship`,`postaladdress`, `telephone`, `email`)
                        VALUES ('$applicant_id', '$sponsor_sponsorname', '$sponsor_relationship', '$sponsor_telephone','$sponsor_postaladdress','$sponsor_email');";

                                if ($this->core->database->doInsertQuery($sql)) {
                                        echo '<div class="successpopup">The requested sponsor account has been created.<br/> WRITE THE FOLLOWING INFORMATION DOWN OR REMEMBER IT!</div>';
                                } else {
                                        //echo $sql.error;
                                        echo '<div class="successpopup">The requested sponsor account has failed to be created .<br/> CHECK THE DETAILS!</div>';
                                }
                        }

                        // Adding variables to session
                        $_SESSION['sponsor_sponsorname'] = $sponsor_sponsorname;
                        $_SESSION['sponsor_relationship'] = $sponsor_relationship;
                        $_SESSION['sponsor_telephone'] = $sponsor_telephone;
                        $_SESSION['sponsor_postaladdress'] = $sponsor_postaladdress;
                        $_SESSION['sponsor_email'] = $sponsor_email;


                        // Employment details
                        $employment_employer = $this->core->cleanPost['employment_employer'];
                        $employment_jobtitle = $this->core->cleanPost['employment_jobtitle'];
                        $employment_postaladdress = $this->core->cleanPost['employment_postaladdress'];
                        $employment_telephone = $this->core->cleanPost['employment_telephone'];
                        $employment_dateofappointment = $this->core->cleanPost['employment_dateofappointment'];


                        //check if the application id exists
                        $sql = "SELECT * FROM `appl_employment` WHERE `appl_employment`.applicantno = '$applicant_id' ";

                        $run = $this->core->database->doSelectQuery($sql);



                        if ($fetch = $run->fetch_assoc()) {
                                echo '<script>alert("Records already exist.\n RECORD WILL BE UPDATED");</script>';

                                //updating the program records
                                $sql = "UPDATE `appl_employment` SET `applicantno` = $applicant_id, `employer` = '$employment_employer', `jobtitle` = '$employment_jobtitle', `postaladdress` = '$employment_postaladdress', `telephone` = '$employment_telephone', `dateofappointment` = '$employment_dateofappointment'
               WHERE `applicantno`= $applicant_id ;";

                                if ($this->core->database->doInsertQuery($sql)) {
                                        echo '<div class="alert alert-success" role="alert"> <strong>Success</strong> Saved! </div>';
                                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/personal/"; </script>';
                                        //echo '<script>alert("RECORD SUCCESSFULLY UPDATED");</script>';
                                } else {
                                        //echo $sql.error;
                                        echo '<div class="successpopup">Error in updating information .<br/> CHECK THE DETAILS! or Contact Admin</div>';
                                }
                        } else {

                                //load the data for the next of sponsor in the database
                                $sql = "INSERT INTO `appl_employment` (`applicantno`, `employer`, `jobtitle`,`postaladdress`, `telephone`, `dateofappointment`)
                        VALUES ('$applicant_id', '$employment_employer', '$employment_jobtitle', '$employment_postaladdress','$employment_telephone','$employment_dateofappointment');";

                                if ($this->core->database->doInsertQuery($sql)) {
                                        echo '<div class="successpopup">The requested employment account has been created.<br/> WRITE THE FOLLOWING INFORMATION DOWN OR REMEMBER IT!</div>';
                                } else {
                                        echo $sql . error;
                                        echo '<div class="successpopup">The requested employment account has failed to be created .<br/> CHECK THE DETAILS!</div>';
                                }
                        }


                        // Adding variables to session
                        $_SESSION['employment_employer'] = $employment_employer;
                        $_SESSION['employment_jobtitle'] = $employment_jobtitle;
                        $_SESSION['employment_postaladdress'] = $employment_postaladdress;
                        $_SESSION['employment_telephone'] = $employment_telephone;
                        $_SESSION['employment_dateofappointment'] = $employment_dateofappointment;

                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/previous/"; </script>';
                } else {
                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/access/"; </script>';
                }
        }

        //displays the regsitration page of online registration.........................................
        public function createStartregistration($item)
        {
        		echo "<style>
		        .menu{
		                visibility: hidden;
		        }
		        </style>";
                include $this->core->conf['conf']['formPath'] . "registrationregister.form.php";
        }

        //displays the second page of online registration
        public function subjectsStartregistration($item)
        {

                if (isset($_SESSION['applicant_id'])) {
                        // Grab user data from the database using the user_id
                        // Let them access the "logged in only" pages
                        include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

                        $select = new optionBuilder($this->core);
                        $subject = $select->showSubjects();

                        //login id
                        $applicant_id = $_SESSION['applicant_id'];

                        //check if the application id exists
                        $sql = "SELECT * FROM `appl_grades` WHERE `appl_grades`.applicantno = '$applicant_id' ";

                        $run = $this->core->database->doSelectQuery($sql);

                        if ($fetch = $run->fetch_assoc()) {
                                echo '<script>alert("Record already exist");</script>';
                        }

                        include $this->core->conf['conf']['formPath'] . "registration5.form.php";
                } else {
                        // Redirect them to the login page

                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/login/"; </script>';
                }
        }

        public function savesubjectStartregistration()
        {

                $applicant_id = $_SESSION['applicant_id'];
                $count = $this->core->cleanPost["count"];

                //check if the application id exists
                $sql = "SELECT * FROM `appl_grades` WHERE `appl_grades`.applicantno = '$applicant_id' ";

                $run = $this->core->database->doSelectQuery($sql);

                if ($fetch = $run->fetch_assoc()) {
                        echo '<script>alert("Record already exist.\n RECORD WILL BE UPDATED");</script>';

                        //delete everything and replace with the new data so as to mimic an update
                        $sql = "DELETE FROM `appl_grades` WHERE `applicantno` = '$applicant_id' ;";
                        $this->core->database->doInsertQuery($sql);


                        //updating the records
                        for ($x = 1; $x <= $count; $x++) {
                                $subject = $this->core->cleanPost["subject" . $x];
                                $grade = $this->core->cleanPost["grade" . $x];
                                $level = $this->core->cleanPost["level" . $x];

                                $sql = "INSERT INTO `appl_grades` (`applicantno`, `subject_id`, `level`, `grade`)
                                        VALUES ('$applicant_id','$subject', '$grade', '$level');";

                                if ($this->core->database->doInsertQuery($sql)) {
                                        //echo $sql . error;
                                        echo '<div class="successpopup">The new user account has been created.<br/> </div>';
                                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/institution/"; </script>';
                                        echo '<script>alert("RECORD SUCCESSFULLY UPDATED");</script>';
                                } else {
                                        //used to check the error with the sql query
                                        //echo $sql . error;
                                        echo '<div class="successpopup">The requested user account has failed Update .<br/> CHECK THE DETAILS!</div>';
                                }
                                // echo $x;
                                // echo $institution1;
                        }
                } else {

                        for ($x = 1; $x <= $count; $x++) {
                                $subject = $this->core->cleanPost["subject" . $x];
                                $grade = $this->core->cleanPost["grade" . $x];
                                $level = $this->core->cleanPost["level" . $x];

                                $sql = "INSERT INTO `appl_grades` (`applicantno`, `subject_id`, `level`, `grade`)
                                VALUES ('$applicant_id','$subject', '$grade', '$level');";

                                if ($this->core->database->doInsertQuery($sql)) {
                                        echo $sql . error;
                                        echo '<div class="successpopup">The new user account has been created.<br/> </div>';
                                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/institution/"; </script>';
                                } else {
                                        //used to check the error with the sql query
                                        echo $sql . error;
                                        echo '<div class="successpopup">The requested user account has failed to be created .<br/> CHECK THE DETAILS!</div>';
                                }
                                // echo $x;
                                // echo $institution1;
                        }
                }
        }



        //displays the page for the user to enter previous institions visted  of online registration.......................................
        public function institutionStartregistration($item)
        {
                if (isset($_SESSION['applicant_id'])) {
                        // Grab user data from the database using the user_id
                        // Let them access the "logged in only" pages


                        //login id
                        $applicant_id = $_SESSION['applicant_id'];

                        //check if the application id exists
                        $sql = "SELECT * FROM `appl_professional` WHERE `appl_professional`.applicantno = '$applicant_id' ";

                        $run = $this->core->database->doSelectQuery($sql);

                        if ($fetch = $run->fetch_assoc()) {
                                echo '<script>alert("Record already exist");</script>';
                        }

                        include $this->core->conf['conf']['formPath'] . "registration6.form.php";
                } else {
                        // Redirect them to the login page

                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/login/"; </script>';
                }
        }

        //this method is used to save the registration6 page (institution)
        public function saveprofessionStartregistration()
        {

                $applicant_id = $_SESSION['applicant_id'];
                $count = $this->core->cleanPost["count"];

                //check if the application id exists
                $sql = "SELECT * FROM `appl_professional` WHERE `appl_professional`.applicantno = '$applicant_id' ";

                $run = $this->core->database->doSelectQuery($sql);

                if ($fetch = $run->fetch_assoc()) {
                        echo '<script>alert("Record already exist.\n RECORD WILL BE UPDATED");</script>';


                        $sql = "DELETE FROM `appl_professional` WHERE `applicantno` = '$applicant_id' ;";
                        $this->core->database->doInsertQuery($sql);


                        for ($x = 1; $x <= $count; $x++) {
                                $institution = $this->core->cleanPost["institution" . $x];
                                $qualification = $this->core->cleanPost["qualification" . $x];
                                $area_of_specialisation = $this->core->cleanPost["area_of_specialisation" . $x];
                                $date_obtained = $this->core->cleanPost["date_obtained" . $x];

                                $sql = "INSERT INTO `appl_professional` (`applicantno`, `institution`, `specialisation`, `qualification`, `dateobtained`)
                                                VALUES ('$applicant_id', '$institution', '$area_of_specialisation', '$qualification','$date_obtained');";

                                if ($this->core->database->doInsertQuery($sql)) {
                                        echo '<div class="successpopup">The requested user account has been created.<br/> WRITE THE FOLLOWING INFORMATION DOWN OR REMEMBER IT!</div>';
                                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/upload/"; </script>';
                                        echo '<script>alert("RECORD SUCCESSFULLY UPDATED");</script>';
                                } else {
                                        //used to check the error with the sql query
                                        //echo $sql.error;
                                        echo '<div class="successpopup">The requested user account has failed to be created .<br/> CHECK THE DETAILS!</div>';
                                }
                        }
                } else {

                        for ($x = 1; $x <= $count; $x++) {
                                $institution = $this->core->cleanPost["institution" . $x];
                                $qualification = $this->core->cleanPost["qualification" . $x];
                                $area_of_specialisation = $this->core->cleanPost["area_of_specialisation" . $x];
                                $date_obtained = $this->core->cleanPost["date_obtained" . $x];

                                $sql = "INSERT INTO `appl_professional` (`applicantno`, `institution`, `specialisation`, `qualification`, `dateobtained`)
                                                VALUES ('$applicant_id', '$institution', '$area_of_specialisation', '$qualification','$date_obtained');";

                                if ($this->core->database->doInsertQuery($sql)) {
                                        echo '<div class="successpopup">The requested user account has been created.<br/> WRITE THE FOLLOWING INFORMATION DOWN OR REMEMBER IT!</div>';
                                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/upload/"; </script>';
                                } else {
                                        //used to check the error with the sql query
                                        //echo $sql.error;
                                        echo '<div class="successpopup">The requested user account has failed to be created .<br/> CHECK THE DETAILS!</div>';
                                }
                        }
                }
        }

        //displays the select program page of online registration............................
        public function programStartregistration($item)
        {


                if (isset($_SESSION['applicant_id'])) {
                        // Grab user data from the database using the user_id
                        // Let them access the "logged in only" pages
                        include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

                        $select = new optionBuilder($this->core);
                        $schools = $select->showStudies();

                        //login id
                        $applicant_id = $_SESSION['applicant_id'];

                        //check if the application id exists
                        $sql = "SELECT * FROM `appl_program` WHERE `appl_program`.applicantno = '$applicant_id' ";

                        $run = $this->core->database->doSelectQuery($sql);



                        if ($fetch = $run->fetch_assoc()) {
                                echo '<script>alert("Record already exist");</script>';
                        }

                        // while ($fetch = $run->fetch_assoc()) {
                        //         //users uniqu identification
                        //         $applicationNumber = $fetch['applicantno'];

                        //         echo $applicationNumber . "..........................................";
                        //         echo '<script>alert("saved data);</script>';
                        // }

                        include $this->core->conf['conf']['formPath'] . "registration2.form.php";
                } else {
                        // Redirect them to the login page

                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/login/"; </script>';
                }
        }

        public function saveprogramStartregistration()
        {
                //login id
                $applicant_id = $_SESSION['applicant_id'];

                $program_level = $this->core->cleanPost['program_level'];
                $mode_of_study = $this->core->cleanPost['mode_of_study'];
                $campus = $this->core->cleanPost['campus'];
                $how_you_head_of_nipa = $this->core->cleanPost['how_you_head_of_nipa'];
                $program = $this->core->cleanPost['program'];

                //check if the id already exists 

                //check if the application id exists
                $sql = "SELECT * FROM `appl_program` WHERE `appl_program`.applicantno = '$applicant_id' ";

                $run = $this->core->database->doSelectQuery($sql);



                if ($fetch = $run->fetch_assoc()) {
                        echo '<script>alert("Record already exist.\n RECORD WILL BE UPDATED");</script>';

                        //updating the program records
                        $sql = "UPDATE `appl_program` SET `level` = '$program_level', `modeofstudy` = '$mode_of_study', `campus` = '$campus', `knowhow` = '$how_you_head_of_nipa', `program` = '$program'
               WHERE `applicantno`= $applicant_id ;";

                        if ($this->core->database->doInsertQuery($sql)) {
                                echo '<div class="alert alert-success" role="alert"> <strong>Success</strong> Saved! </div>';
                                echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/personal/"; </script>';
                                echo '<script>alert("RECORD SUCCESSFULLY UPDATED");</script>';
                        } else {
                                //echo $sql.error;
                                echo '<div class="successpopup">Error in updating program information .<br/> CHECK THE DETAILS! or Contact Admin</div>';
                        }
                } else {

                        //load the data for the next of kin in the database
                        $sql = "INSERT INTO `appl_program` (`applicantno`, `level`, `modeofstudy`,`campus`, `knowhow`, `program`)
               VALUES ('$applicant_id', '$program_level', '$mode_of_study', '$campus','$how_you_head_of_nipa','$program');";

                        if ($this->core->database->doInsertQuery($sql)) {
                                echo '<div class="alert alert-success" role="alert"> <strong>Success</strong> Saved! </div>';
                                echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/personal/"; </script>';
                        } else {
                                //echo $sql.error;
                                echo '<div class="successpopup">The requested next of kin account has failed to be created .<br/> CHECK THE DETAILS!</div>';
                        }
                }
        }

        //displays the login page of online registration
        public function previousStartregistration($item)
        {
                if (isset($_SESSION['applicant_id'])) {
                        // Grab user data from the database using the user_id
                        // Let them access the "logged in only" pages

                        //login id
                        $applicant_id = $_SESSION['applicant_id'];

                        //check if the application id exists
                        $sql = "SELECT * FROM `appl_exam` WHERE `appl_exam`.applicantno = '$applicant_id' ";

                        $run = $this->core->database->doSelectQuery($sql);



                        if ($fetch = $run->fetch_assoc()) {
                                echo '<script>alert("Record already exist");</script>';
                        }
                        include $this->core->conf['conf']['formPath'] . "registration4.form.php";
                } else {
                        // Redirect them to the login page

                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/login/"; </script>';
                }
        }

        //this method is used to save the registration6 page (institution)
        public function savepreviousStartregistration()
        {

                $applicant_id = $_SESSION['applicant_id'];
                $count = $this->core->cleanPost["count"];

                $examination_number = $this->core->cleanPost['examination_number'];
                $examination_body = $this->core->cleanPost['examination_body'];
                $examination_year = $this->core->cleanPost['examination_year'];


                //check if the application id exists
                $sql = "SELECT * FROM `appl_exam` WHERE `appl_exam`.applicantno = '$applicant_id' ";

                $run = $this->core->database->doSelectQuery($sql);



                if ($fetch = $run->fetch_assoc()) {
                        echo '<script>alert("Record already exist.\n RECORD WILL BE UPDATED");</script>';

                        //updating the program records
                        $sql = "UPDATE `appl_exam` SET `applicantno` = '$applicant_id', `examno` = '$examination_number', `exambody` = '$examination_body', `examyear` = '$examination_year'
               WHERE `applicantno`= $applicant_id ;";

                        if ($this->core->database->doInsertQuery($sql)) {
                                //echo '<div class="alert alert-success" role="alert"> <strong>Success</strong> Saved! </div>';
                                //echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/personal/"; </script>';

                                $sql = "DELETE FROM `appl_schools` WHERE `applicantno` = '$applicant_id' ;";
                                $this->core->database->doInsertQuery($sql);

                                for ($x = 1; $x <= $count; $x++) {
                                        $school = $this->core->cleanPost["school" . $x];
                                        $start_year = $this->core->cleanPost["start_year" . $x];
                                        $end_year = $this->core->cleanPost["end_year" . $x];
                                        $level_of_attainment = $this->core->cleanPost["level_of_attainment" . $x];


                                        //updating the program records
                                        $sql = "INSERT INTO `appl_schools` (`applicantno`, `school`, `yearfrom`, `yearto`, `level`)
                                                VALUES ('$applicant_id', '$school', '$start_year', '$end_year','$level_of_attainment');";

                                        if ($this->core->database->doInsertQuery($sql)) {
                                                // echo '<script>alert("saved data);</script>';
                                                //echo '<div class="successpopup">The requested user account has been created.<br/> WRITE THE FOLLOWING INFORMATION DOWN OR REMEMBER IT!</div>';
                                                echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/subjects/"; </script>';
                                        } else {
                                                //used to check the error with the sql query
                                                //echo $sql.error;
                                                echo '<script>alert("Failed to save data);</script>';
                                                echo '<div class="successpopup">The requested user account has failed to be created .<br/> CHECK THE DETAILS!</div>';
                                        }
                                }

                                echo '<script>alert("RECORD SUCCESSFULLY UPDATED");</script>';
                        } else {
                                //echo $sql.error;
                                echo '<div class="successpopup">Error in updating program information .<br/> CHECK THE DETAILS! or Contact Admin</div>';
                        }
                } else {

                        $sql = "INSERT INTO `appl_exam` (`applicantno`, `examno`, `exambody`, `examyear`)
                        VALUES ('$applicant_id', '$examination_number', '$examination_body', '$examination_year');";

                        if ($this->core->database->doInsertQuery($sql)) {

                                for ($x = 1; $x <= $count; $x++) {
                                        $school = $this->core->cleanPost["school" . $x];
                                        $start_year = $this->core->cleanPost["start_year" . $x];
                                        $end_year = $this->core->cleanPost["end_year" . $x];
                                        $level_of_attainment = $this->core->cleanPost["level_of_attainment" . $x];


                                        $sql = "INSERT INTO `appl_schools` (`applicantno`, `school`, `yearfrom`, `yearto`, `level`)
                                                VALUES ('$applicant_id', '$school', '$start_year', '$end_year','$level_of_attainment');";

                                        if ($this->core->database->doInsertQuery($sql)) {
                                                echo '<script>alert("saved data);</script>';
                                                echo '<div class="successpopup">The requested user account has been created.<br/> WRITE THE FOLLOWING INFORMATION DOWN OR REMEMBER IT!</div>';
                                                echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/subjects/"; </script>';
                                        } else {
                                                //used to check the error with the sql query
                                                //echo $sql.error;
                                                echo '<script>alert("Failed to save data);</script>';
                                                echo '<div class="successpopup">The requested user account has failed to be created .<br/> CHECK THE DETAILS!</div>';
                                        }
                                }
                        } else {
                                //used to check the error with the sql query
                                //echo $sql.error;
                                echo '<script>alert("Failed to save data);</script>';
                                echo '<div class="successpopup">The requested user account has failed to be created .<br/> CHECK THE DETAILS!</div>';
                        }
                }
        }

        //this is called when the user hits the next button of the second page of the registration page
        //to enable the data to be saved to the session
        public function saveProgram()
        {

                // profram details
                $Program_type = $this->core->cleanPost['Program_type'];
                $mode_of_study = $this->core->cleanPost['mode_of_study'];
                $campus = $this->core->cleanPost['campus'];
                $how_you_head_of_nipa = $this->core->cleanPost['how_you_head_of_nipa'];
                $selected_program = $this->core->cleanPost['selected_program'];

                // Adding variables to session
                $_SESSION['Program_type'] = $Program_type;
                $_SESSION['mode_of_study'] = $mode_of_study;
                $_SESSION['campus'] = $campus;
                $_SESSION['how_you_head_of_nipa'] = $how_you_head_of_nipa;
                $_SESSION['selected_program'] = $selected_program;


                // User registration details
                $email = $this->core->cleanPost['email'];
                $password = $this->core->cleanPost['password'];
                $sponsor_telephone = $this->core->cleanPost['sponsor_telephone'];
                $sponsor_postaladdress = $this->core->cleanPost['sponsor_postaladdress'];
                $sponsor_email = $this->core->cleanPost['sponsor_email'];

                // Adding variables to session
                $_SESSION['sponsor_sponsorname'] = $sponsor_sponsorname;
                $_SESSION['sponsor_relationship'] = $sponsor_relationship;
                $_SESSION['sponsor_telephone'] = $sponsor_telephone;
                $_SESSION['sponsor_postaladdress'] = $sponsor_postaladdress;
                $_SESSION['sponsor_email'] = $sponsor_email;


                // Employment details
                $employment_employer = $this->core->cleanPost['employment_employer'];
                $employment_jobtitle = $this->core->cleanPost['employment_jobtitle'];
                $employment_postaladdress = $this->core->cleanPost['employment_postaladdress'];
                $employment_telephone = $this->core->cleanPost['employment_telephone'];
                $employment_dateofappointment = $this->core->cleanPost['employment_dateofappointment'];

                // Adding variables to session
                $_SESSION['employment_employer'] = $employment_employer;
                $_SESSION['employment_jobtitle'] = $employment_jobtitle;
                $_SESSION['employment_postaladdress'] = $employment_postaladdress;
                $_SESSION['employment_telephone'] = $employment_telephone;
                $_SESSION['employment_dateofappointment'] = $employment_dateofappointment;

                //load the data in the database
                $sql = "INSERT INTO `newapplicantlog` (`emailornumber`, `applicantID`, `datetimelogged`)
                        VALUES ('$emailadd', '$idd', '$dateTimeLogged');";

                $this->core->redirect("startregistration", "show2", NULL);
        }

        //displays the login page of online registration...................................................
        public function loginStartregistration($item)
        {
        
		echo "<style>
		        .menu{
		                visibility: hidden;
		        }
		        </style>";
        
                include $this->core->conf['conf']['formPath'] . "registrationlogin.form.php";
        }

        public function saveaccessStartregistration()
        {

                // Always start this first
                session_start();

                try {
                        //login information
                        $email_phone_number = $this->core->cleanPost['email_phone_number'];
                        $password = $this->core->cleanPost['password'];

                        //sets the time zone to central african time
                        date_default_timezone_set("Africa/Harare");
                        $dateTimeLogged = date("Y-m-d h:i:sa");

                        if (!filter_var($email_phone_number, FILTER_VALIDATE_EMAIL)) {
                                $phoneNumber = $email_phone_number;
                                // $hash = password_hash($password, PASSWORD_DEFAULT);
                                $hash = hash('sha512', $password . $this->core->conf['conf']['hash'] . $phoneNumber);
                                
                                //sql qury to validate the user who is trying to login
                                $sql = "SELECT * FROM `users` WHERE `users`.emailorphone = '$phoneNumber' AND `users`.password = '$hash' ";


                                $run = $this->core->database->doSelectQuery($sql);

                                $idd = '';
                                $phoneadd = '';
                                while ($fetch = $run->fetch_assoc()) {
                                        //users uniqu identification
                                        $idd = $fetch['id'];

                                        //users login email address
                                        $phoneadd = $fetch['emailorphone'];

                                        echo $idd . "..........................................";
                                        echo $phoneadd;
                                }
                                if ($idd != null) {

                                        $sql = "INSERT INTO `newapplicantlog` (`emailornumber`, `applicantID`, `datetimelogged`)
                        VALUES ('$phoneadd', '$idd', '$dateTimeLogged');";

                                        if ($this->core->database->doInsertQuery($sql)) {

                                                $_SESSION['applicant_id'] = $idd;


                                                //redirect to select program after login
                                                echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/summery/"; </script>';
                                        } else {
                                                echo $sql . error;
                                                //echo ' <script> alert(""); <script>';
                                                echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/login/"; </script>';
                                        }
                                } else {
                                        //echo $sql . error;
                                        echo '<script>alert("Failed to login make sure your email/phone number and password are correct");</script>';
                                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/login/"; </script>';
                                }
                        } else {
                                $email = $email_phone_number;
                                // $hash = password_hash($password, PASSWORD_DEFAULT);
                                $hash = hash('sha512', $password . $this->core->conf['conf']['hash'] . $email);
                                //sql qury to validate the user who is trying to login
                                $sql = "SELECT * FROM `users` WHERE `users`.emailorphone = '$email' AND `users`.password = '$hash' ";


                                $run = $this->core->database->doSelectQuery($sql);

                                $idd = '';
                                $emailadd = '';
                                while ($fetch = $run->fetch_assoc()) {
                                        //users uniqu identification
                                        $idd = $fetch['id'];
                                        //users login email address
                                        $emailadd = $fetch['emailorphone'];
                                }
                                if ($idd != null) {

                                        $sql = "INSERT INTO `newapplicantlog` (`emailornumber`, `applicantID`, `datetimelogged`)
                        VALUES ('$emailadd', '$idd', '$dateTimeLogged');";

                                        if ($this->core->database->doInsertQuery($sql)) {

                                                $_SESSION['applicant_id'] = $idd;


                                                //redirect to select program after login
                                                echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/summery/"; </script>';
                                        } else {
                                                echo $sql . error;
                                                echo '<div class="successpopup">failed to add information to log  .<br/> CHECK THE DETAILS!</div>';
                                        }
                                } else {
                                        echo '<script>alert("Failed to login make sure your email and password are correct");</script>';
                                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/login/"; </script>';
                                }
                        }



                        // }else{

                        // }

                } catch (Execption $ex) {
                        echo '<div class="successpopup">Failed to login because of error check access information.<br/> CHECK THE DETAILS!</div>';
                }
        }

        public function saveStartregistration()
        {
                // new applicant details
                $emailorphone = $this->core->cleanPost['emailorphone'];
                $password = $this->core->cleanPost['password'];

                date_default_timezone_set("Africa/Harare");
                $dateTimeCreated = date("Y-m-d h:i:sa");

                // $hash = password_hash($password, PASSWORD_DEFAULT);
                $hash = hash('sha512', $password . $this->core->conf['conf']['hash'] . $emailorphone);



                $sql = "INSERT INTO `users` (`emailorphone`, `password`, `datetimecreated`)
				VALUES ('$emailorphone', '$hash', '$dateTimeCreated');";

                if ($this->core->database->doInsertQuery($sql)) {
                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/login/"; </script>';
                } else {
                        //echo $sql.error;
                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/create/"; </script>';
                }
        }

        public function searchPrograms()
        {
                include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

                $select = new optionBuilder($this->core);

                $study = $select->showStudies(null);
                $program = $select->showPrograms(null, null, null);
                $courses = $select->showCourses(null);

                include $this->core->conf['conf']['formPath'] . "searchgrades.form.php";
        }

               //displays summery information of online registration...................................
        public function summeryStartregistration($item)
        {
                // Always start thils first


                if (isset($_SESSION['applicant_id'])) {
                        // Grab user data from the database using the user_id
                        // Let them access the "logged in only" pages

                        $applicant_id = $_SESSION['applicant_id'];
                        $applicantnub = null;

                        $sql = "SELECT * FROM `appl_status_submit` WHERE `applicantno` = $applicant_id ";
                        $run = $this->core->database->doSelectQuery($sql);
                        while ($fetch = $run->fetch_assoc()) {

                                $applicantnub = $fetch['applicantno'];
                        }

                        if ($applicantnub != null or $applicantnub != "") {
                                echo "<style> #submitdiv{
                                        display: none;
                                    }
                                    #back_to_program{ display: none; }
                                    #go_back_personal{ display: none; }
                                    #back_to_kin{ display: none; }
                                    #back_to_education_history_olevel{ display: none; }
                                    #back_to_education_history{ display: none; }
                                    #back_tertiary_education{ display: none; }
                                    #back_to_upload{ display: none; }
                                    #start_application{ display: none; }
                                    </style>";

                                echo "<script>
                                        alert('Your Application has already been submitted!');
                                    </script>";
                        }


                        // $sql = "SELECT * FROM `appl_personal` WHERE `applicantno` = $applicant_id ";
                        // $run = $this->core->database->doSelectQuery($sql);
                        // $fetch = $run->fetch_assoc();

                        include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

                        $select = new optionBuilder($this->core);
                        $personal = $select->showPersonal($applicant_id);

                        $program = $select->showProgram($applicant_id);

                        $employment = $select->showEmployment($applicant_id);

                        $sponsor = $select->showSponsor($applicant_id);

                        $nextofkin = $select->showKins($applicant_id);

                        $grade12 = $select->showGrade12($applicant_id);

                        $educationhistory = $select->showPreviousschool($applicant_id);

                        $olevel = $select->showOlevel($applicant_id);

                        $tertiaryedu = $select->showTertiaryeducation($applicant_id);

                        $upload = $select->showUploads($applicant_id);



                        include $this->core->conf['conf']['formPath'] . "regsummery.form.php";
                        //echo $_SESSION['applicant_id'];
                } else {
                        // Redirect them to the login page

                        include $this->core->conf['conf']['formPath'] . "registrationlogin.form.php";
                }
        }

        public function submitStartregistration($item)
        {

                $applicant_id = $_SESSION['applicant_id'];
                $status = "Acceptted";
                //sets the time zone to central african time
                date_default_timezone_set("Africa/Harare");
                $dateTimeLogged = date("Y-m-d h:i:sa");

                $sql = "INSERT INTO `appl_status_submit` (`datesubmitted`, `applicantno`, `status`)
                        VALUES ('$dateTimeLogged', '$applicant_id', '$status');";

                if ($this->core->database->doInsertQuery($sql)) {
                        echo "<script> alert('Application successfully submitted..'); </script>";
                        echo '<script> location.href="' .$this->core->conf['conf']['path'] .'/startregistration/summery/"; </script>';
                } else {
                        echo "<script> alert('Application Could not be submitted\n Ensure all the relevant information is entered correctly'); </script>";
                }
        }
}


//........................................................................
