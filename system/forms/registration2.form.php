<section class="content">


    <div class="container-fluid">

        <div style="padding: 5px;" class="card">

        <div style="margin: 10px;" class="text-center">
                        <?php echo '<a class="btn btn-default btn-rounded mb-4"  href="' .$this->core->conf['conf']['path'] .'/startregistration/summery/"; >'; ?>
                            View Summery</a>

                            <h1 >
                <b>Program Choice</b>
            </h1>
                    </div>

            

            <form style="padding: 30px;" id="studentUser" class="form-horizontal" action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/saveprogram/" . $this->core->item; ?> "  method="POST">

                <div class="form-group">
                    <label for="program_level">Select Program Level</label>
                    <select class="form-control" name="program_level" id="program_level" required>
                    <option value="">Select</option>
                        <option value="certificate">Certificate</option>
                        <option value="diploma">Diploma</option>
                        <option value="undergraduate">Undergraduate</option>
                        <option value="uostgraduate">Postgraduate</option>
                    </select>

                </div>

                <div class="form-group">
                    <label for="mode_of_study">Select mode of study</label>
                    <select class="form-control" name="mode_of_study" id="mode_of_study" required>
                    <option value="">Select</option>
                        <option value="part_time">Part-time</option>
                        <option value="full_time">Full-Time</option>
                        <option value="distance">Distance</option>
                    </select>

                </div>

                <div class="form-group">
                    <label for="campus">Select Campus</label>
                    <select class="form-control" name="campus" id="campus" required>
                    <option value="">Select</option>
                        <option value="lusaka">Lusaka</option>
                        <option value="ndola">Ndola</option>
                    </select>

                </div>

                <div class="form-group">
                    <label for="how_you_head_of_nipa"> How did you hear about NIPA: </label>
                    <select class="form-control" name="how_you_head_of_nipa" id="how_you_head_of_nipa" required>
                    <option value="">Select</option>
                        <option Newpaper_advert="january_intake"> Newpaper advert</option>
                        <option value="Radio_advertisement"> Radio advertisement</option>
                        <option value="Brochure">Brochure</option>
                        <option value="Website">Website</option>
                        <option value="Through_a_friend"> Through a friend</option>
                        <option value="Through_a_family_member"> Through a family member</option>
                        <option value="Through_NIPA_alumni">Through NIPA alumni</option>
                        <option value=" Television_commercial"> Television commercial</option>
                    </select>

                </div>

                <div class="form-group">
                    <label for="selected_program">Select Program * </label>
                    <select class="form-control" name="program" id="program" required>
                    <option value="">Select</option>
			            <?php echo $schools; ?>
                    </select>
                </div>

                <br>

                <button type="submit" class="btn btn-primary">Next</button>

            </form>

        </div>
    </div>
</section>

            <!-- javascript function to check is passwords are the same -->

<script type="text/javascript">
function checkPass()
{
    
    //Store the password field objects into variables ...
    var password = document.getElementById('password');
    var confirm  = document.getElementById('password_confirm');
    //Store the Confirmation Message Object ...
    var message = document.getElementById('confirm-message2');
    //Set the colors we will be using ...
    var good_color = "#66cc66";
    var bad_color  = "#ff6666";
    //Compare the values in the password field 
    //and the confirmation field
    if(password.value == confirm.value){
        //The passwords match. 
        //Set the color to the good color and inform
        //the user that they have entered the correct password 
        confirm.style.backgroundColor = good_color;
        message.style.color           = good_color;
        message.innerHTML             = '<p>  Passwords Match</p>';
    }else{
        //The passwords do not match.
        //Set the color to the bad color and
        //notify the user.
        confirm.style.backgroundColor = bad_color;
        message.style.color           = bad_color;
        message.innerHTML             = '<p>  Passwords do not Match</p>';
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
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtHint").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "<?php echo $this->core->conf['conf']['path'];?>/api/payrollitems/" + str, true);
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
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtHintBank").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "<?php echo $this->core->conf['conf']['path'];?>/api/payrollbank/" + str, true);
            xmlhttp.send();
        }
    }
</script>
