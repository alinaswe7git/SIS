<?php
class admissions
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
                // echo '<style>
                // 	.bodycon {
                // 		background-color: #1C00ff00;
                // 	}
                // 	.contentwrapper {
                // 		padding: 20px;
                // 	}
                // </style>';
        }


        //displays the sponsor and next of kin page of the online registration................................
        public function applicantAdmissions($item)
        {
                // Always start thils first
                include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

                $select = new optionBuilder($this->core);
                $applicant = $select->showApplicant();

                include $this->core->conf['conf']['formPath'] . "applicantlist.form.php";
        }

        //displays the sponsor and next of kin page of the online registration................................
        public function admittedAdmissions($item)
        {
                // Always start thils first
                include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

                $select = new optionBuilder($this->core);
                $applicant = $select->showAdmitted();

                include $this->core->conf['conf']['formPath'] . "acceptedapplicant.form.php";
        }


        //displays the sponsor and next of kin page of the online registration................................
        public function rejectedAdmissions($item)
        {
                // Always start thils first
                include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

                $select = new optionBuilder($this->core);
                $applicant = $select->showRejected();

                include $this->core->conf['conf']['formPath'] . "rejectedapplicant.form.php";
        }


        //displays the applicants that are automatically eligable for admission................................
        public function autoselectedAdmissions($item)
        {
                // Always start thils first
                include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

                $select = new optionBuilder($this->core);
                $applicant = $select->showAutoselected();
                $rejected = $select->showAutoRejected();

                $diplomaAccepted = $select->showAutoselectedDiploma();
                $diplomaRejected = $select->showAutoRejectedDiploma();


                include $this->core->conf['conf']['formPath'] . "applicantautoselected.form.php";
        }

        //displays the applicants that are automatically eligable for admission................................
        public function profileAdmissions($item)
        {
                $applicantsid = $_GET['id'];
                $applicantno = substr($applicantsid, 19);

                // Always start thils first
                include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

                $select = new optionBuilder($this->core);
                $personal = $select->showPersonal($applicantno);
                $olevel  = $select->showOlevel($applicantno);
                $program  = $select->showProgram($applicantno);

                $nextofkin  = $select->showKins($applicantno);
                $sponsor  = $select->showSponsor($applicantno);
                $employment  = $select->showEmployment($applicantno);

                include $this->core->conf['conf']['formPath'] . "applicantprofile.form.php";
        }
        
                public function savestatusAdmissions($item)
        {
                $applicantnumber = $this->core->cleanPost['applicantno'];
                echo $applicantnumber;
                $admin = $this->core->cleanPost['currentuser'];
                $intakeyear = $this->core->cleanPost['intakeyear'];
                $accept = $_POST['accept'];
                $reject = $_POST['reject'];


                //sets the time zone to central african time 
                date_default_timezone_set("Africa/Harare");
                $dateTimeLogged = date("Y-m-d h:i:sa");
                $number = 1;
                $studentID = 0;
                if ($accept) {

                        include $this->core->conf['conf']['classPath']."showoptions.inc.php";
                         
                        $select = new optionBuilder($this->core);
                        $lastid = $select->showLastID();
                        echo  $lastStudentno;
                        $lastStudentno = $lastid['StudentID'];

                        if ($lastStudentno != "" || $lastStudentno != null){

                                $studentID = (int)$lastStudentno + 1;
                        }else{
                                //create the student number
                                $numberpaded = str_pad($number, 5, "0", STR_PAD_LEFT);
                                $studentID = $intakeyear . $numberpaded;

                        }

                        $number++;
                        $acceptstatus = "accepted";

                        // 1. first of all we add the status of the student in the appl_status table
                        $sql = "INSERT INTO appl_status (`user`,`userdate`,`applicantno`,`status`,`student_number`) VALUES ('$admin', '$dateTimeLogged', '$applicantnumber', '$acceptstatus','$studentID');";
                        
                        if ($this->core->database->doInsertQuery($sql)) {

                                // // runs in order for the quary to work
                                // include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
                        
                                
                                // $select = new optionBuilder($this->core);
                                $personaldetails = $select->showPersonal($applicantnumber);
                                $programdd = $select->showallprograminfo($applicantnumber);
                                $kin = $select->showAllKin($applicantnumber);
                                //$lastid = $select->showLastID();

                                //increamate the student number
                                
                                //$lastStudentno = $lastid['StudentID'];

                                // if ($lastStudentno != "" || $lastStudentno != null){

                                //         $studentID = (int)$lastStudentno + 1;

                                // }else{
                                //         $numberpaded = str_pad($number, 5, "0", STR_PAD_LEFT);
                                //         $studentID = $intakeyear . $numberpaded;
                                // }
                                

                                //next of kin information
                                $fullname = $kin['fullname'];
                                $relationship = $kin['relationship'];
                                $postaladdress = $kin['postaladdress'];
                                $telephone = $kin['telephone'];
                                $townkin = $kin['town'];


                                //personal information
                                $firstname = $personaldetails['firstname'];
                                $middlename = $personaldetails['middlename'];
                                $lastname = $personaldetails['lastname'];
                                $sex = $personaldetails['gender'];
                                $studentnumber = $studentID;
                                $governmentid = $personaldetails['NRCnumber'];
                                $dateofbirth = $personaldetails['dateofbirth'];
                                $placeofbirth = $personaldetails['placeofbirth'];
                                $nationality = $personaldetails['nationality'];
                                $streetname = " ";
                                $postalcode = $personaldetails['postaladdress'];
                                $town = "kabwe";
                                $country = $personaldetails['countryofresidence'];
                                $homephone = $personaldetails['telephone'];
                                $mobilenumber = $personaldetails['mobilephone'];
                                $disability = $personaldetails['disability'];
                                $disability_type = $personaldetails['disability'];
                                $email = $personaldetails['email'];
                                $maritalstatus = $personaldetails['maritalstatus'];
                                $studytype = $programdd['modeofstudy'];
                                $studyID = $programdd['program'];
                                $status = "new";

                                //inserting the student id in the student study link table 
                                $sql = " INSERT INTO `student-study-link` (StudentID,StudyID,Status) VALUES
                                ('$studentnumber','$studyID','$intakeyear');";
                                
                                if ($this->core->database->doInsertQuery($sql)) {
                                        echo '<script>alert("Success: student link ")</script>';
                                }else{
                                        echo '<script>alert("Error: student link ")</script>';
                                        echo $sql;
                                }

                                //inserting infromation from perosnal to basic information
                                $sql = " INSERT INTO aviplat.`basic-information` (FirstName,MiddleName,Surname,Sex,ID,GovernmentID,DateOfBirth,PlaceOfBirth,Nationality,StreetName,PostalCode,Town,Country,HomePhone,MobilePhone,Disability,DissabilityType,PrivateEmail,MaritalStatus,StudyType,Status) VALUES
	                        ('$firstname','$middlename','$lastname','$sex','$studentnumber','$governmentid','$dateofbirth','$placeofbirth ','$nationality ','$streetname','$postalcode','$town','$country','$homephone','$mobilenumber','$disability','$disability_type','$email','$maritalstatus','$studytype','$status');";


                                if ($this->core->database->doInsertQuery($sql)) {

                                        $sql = " INSERT INTO aviplat.`emergency-contact` (StudentID,FullName,Relationship,PhoneNumber,Street,Town,Postalcode) VALUES ('$studentnumber','$fullname','$relationship','$telephone','$townkin','$townkin','$postaladdress');";
                                        
                                        if ($this->core->database->doInsertQuery($sql)) {
                                                echo '<script>alert("Success: Applicant has been successfully admitted")</script>';
                                                echo "<script> location.href='http://192.168.0.33/sis/admissions/applicant/'; </script>";
                                        }

                                } else {
                                        echo '<script>alert("ERROR: Applicant failed to be registered: basic information")</script>';
                                }
                        } else {
                                echo '<script>alert("ERROR: Applicant could not be admitted\n Because the applicant already exists\n Contact the Information Technology Department")</script>';
                                echo "<script> location.href='http://192.168.0.33/sis/admissions/applicant/'; </script>";
                        }
                } else if ($reject) {
                       $rejectstatus = "rejected";
                        $sql = "INSERT INTO appl_status_submit (`datesubmitted`,`applicantno`,`status`) VALUES ('$dateTimeLogged', '$applicantnumber', '$rejectstatus');";
                        if ($this->core->database->doInsertQuery($sql)) {
                                echo '<script>alert("Success")</script>';
                        }else{
                                //echo "rejected";
                                echo "<script> location.href='http://192.168.0.33/sis/admissions/applicant/'; </script>";
                        }
                }
        }

       
}
