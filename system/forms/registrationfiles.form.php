<section class="content">


    <div class="container-fluid">

        <div style="padding: 30px;" class="card">

            <div style="margin: 10px;" class="text-center">
                <?php echo '<a class="btn btn-default btn-rounded mb-4"  href="' .$this->core->conf['conf']['path'] .'/startregistration/summery/"; >'; ?>
                    View Summery</a>

                <h1>
                    <b>Attachments
                    </b>
                </h1>

            </div>

            <h2>
                <b> Attach the following documents: </b> 
                <b>To attach a document click on browse and it will show you available files from your computer and select the file you wish to upload and then click on upload
                to complete the process. The uploaded file will be shown below. Please make sure your attachments are in the following formats (*.jpg,.jpeg,.png), attachments exceeding 4MB will fail to upload</b>
            </h2>
<br>
            <h2 style="color: Red;">
                Make sure your file name starts with your name and type (EG: firstname-lastname-filetype.)
            </h2>

            <br>

            <form style="padding: 50px;"  id="studentUser" class="form-horizontal" method="POST" enctype="multipart/form-data" action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/savefiles/" . $this->core->item; ?> ">

            <div class="form-group">
                    <label for="depositslip">Application Deposit slip:*</label>
                    <input type="file" class="form-control" name="depositslip" id="depositslip">

                </div>
            
            <div class="form-group">
                    <label for="grade_12_certificate">Copy of grade 12 certificate:*</label>
                    <input type="file" class="form-control" name="grade_12_certificate" id="grade_12_certificate">

                </div>

                <div class="form-group">
                    <label for="passport_nrc">Copy of passport or NRC:*</label>
                    <input type="file" class="form-control" name="passport_nrc" id="passport_nrc">
                </div>

                <div class="form-group">
                    <label for="qualification">Proof of Academic / Professional Qualification:</label>
                    <input type="file" class="form-control" name="qualification" id="qualification">

                </div>

                <div class="form-group">
                    <label for="qualification1">Proof of Academic / Professional Qualification:</label>
                    <input type="file" class="form-control" name="qualification1" id="qualification1">

                </div>

                <div class="form-group">
                    <label for="reference">Reference (Postgraduate only):</label>
                    <input type="file" class="form-control" name="reference" id="reference">

                </div>

                <br>

                <!-- <a class="btn btn-primary" href="${contextPath}/${user.username}/application/2">Previous</a> -->
                <button type="submit" name="save" class="btn btn-primary">Next</button>

            </form>

        </div>
    </div>
</section>

<!-- javascript function to check is passwords are the same -->

<script type="text/javascript">
    function checkPass() {

        //Store the password field objects into variables ...
        var password = document.getElementById('password');
        var confirm = document.getElementById('password_confirm');
        //Store the Confirmation Message Object ...
        var message = document.getElementById('confirm-message2');
        //Set the colors we will be using ...
        var good_color = "#66cc66";
        var bad_color = "#ff6666";
        //Compare the values in the password field 
        //and the confirmation field
        if (password.value == confirm.value) {
            //The passwords match. 
            //Set the color to the good color and inform
            //the user that they have entered the correct password 
            confirm.style.backgroundColor = good_color;
            message.style.color = good_color;
            message.innerHTML = '<p>  Passwords Match</p>';
        } else {
            //The passwords do not match.
            //Set the color to the bad color and
            //notify the user.
            confirm.style.backgroundColor = bad_color;
            message.style.color = bad_color;
            message.innerHTML = '<p>  Passwords do not Match</p>';
        }
    }
</script>



<script>
    function showEntries(str) {
        if (str == "") {
            document.getElementById("txtHint").innerHTML = "";
            return;
        } else {
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtHint").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "<?php echo $this->core->conf['conf']['path']; ?>/api/payrollitems/" + str, true);
            xmlhttp.send();
        }
    }

    function showBank(str) {
        if (str == "") {
            document.getElementById("txtHintBank").innerHTML = "";
            return;
        } else {
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtHintBank").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "<?php echo $this->core->conf['conf']['path']; ?>/api/payrollbank/" + str, true);
            xmlhttp.send();
        }
    }
</script>

