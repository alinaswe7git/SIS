<section class="content">
    <div class="container-fluid">
        <div class="card">

        <div style="margin: 10px;" class="text-center">
                        <?php echo '<a class="btn btn-default btn-rounded mb-4"  href="' .$this->core->conf['conf']['path'] .'/startregistration/summery/"; >'; ?>
                            View Summery</a>
                    </div>

            <form style="padding: 20px;" method="POST" action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/saveprevious/" . $this->core->item; ?> " >

                <h3>School Details</h3>
                <div style="padding: 20px;" class="card">

                    <div style="display: flex;">
                        <div class="form-line col-sm-4">
                            <label for="examination_number">Grade 12 Examination number :*</label>
                            <input name="examination_number" Class="form-control" required/>
                        </div>

                        <div class="form-line col-sm-4">
                            <label for="examination_body">Examination Body :</label>
                            <input name="examination_body" Class="form-control" required />
                        </div>

                        <div class="form-line col-sm-4">
                            <label for="examination_year">Year :</label>
                            <input name="examination_year" Class="form-control" required />
                        </div>


                    </div>

                </div>


                <br> <br>


                <!-- <div class="input_fields_wrap">
                    <button class="add_field_button">Add More Fields</button>
                    <div><input type="text" name="mytext[]"></div>
                </div> -->

                <div style="padding: 20px;" class="input_fields_wrap card">
                    
                    <button class="add_field_button form-control btn-primary" style="width: 200px;" >Add More Fields</button>
<br>
                    
                    <div style="display: flex;">

                        <div class="form-line col-sm-6">
                            <label for="school">School :*</label>
                            <input name="school1" Class="form-control" required="required" />
                        </div>

                        <div class="form-line col-sm-6">
                            <label for="start_year">Start year </label>
                            <input type="date" name="start_year1" Class="form-control" required="" />
                        </div>

                    </div>

                    <div style="display: flex;">

                        <div class="form-line col-sm-6">
                            <label for="end_year">End year :</label>
                            <input type="date" name="end_year1" Class="form-control" required="" />
                        </div>

                        <div class="form-line col-sm-6">
                            <label for="level_of_attainment">Level of attainment</label>
                            <input name="level_of_attainment1" Class="form-control" required="" />
                        </div>
                        
                        <div><input type="hidden" class="form-control" name="count" id="count" value="1"> </div> 

                    </div>

                </div>

                    <br> <br>

                    <div style="padding: 20px;" class="">
                        <button type="submit" class="btn btn-primary" name="next">Next</button>
                    </div>

            </form>

            <script>

                $(document).ready(function () {
                    var max_fields = 10; //maximum input boxes allowed
                    var wrapper = $(".input_fields_wrap"); //Fields wrapper
                    var add_button = $(".add_field_button"); //Add button ID

                    var x = 1; //initlal text box count
                    $(add_button).click(function (e) { //on add input button click
                        e.preventDefault();
                        if (x < max_fields) { //max input box allowed

                            x++; //text box increment
                            $(wrapper).append('<br><div><div style="display: flex;"><div class="form-line col-sm-6"><label for="school">School :*</label><input name="school'+ x +'" Class="form-control" required="required" /> </div><div class="form-line col-sm-6"><label for="start_year">Start year </label> <input type="date" name="start_year'+ x +'" Class="form-control" required="" /> </div>  </div>  <a href="#" class="remove_field">Remove</a></div>'); //add input box
                            $(wrapper).append('<div><div style="display: flex;"><div class="form-line col-sm-6"><label for="school">Level of attainment :*</label><input name="level_of_attainment'+ x +'" Class="form-control" required="required" /> </div><div class="form-line col-sm-6"><label for="start_year">End year </label> <input type="date" name="end_year'+ x +'" Class="form-control" required="" /> </div>  </div>  <a href="#" class="remove_field">Remove</a></div>'); //add input box         
                       
                        }
                        $(wrapper).append('<br><div><input type="hidden" class="form-control" name="count" id="count" value="'+ x +'"> </div> ' ); //add input box
                    });
                    //alert(document.getElementsByName("school2").values)

                    $(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
                        e.preventDefault(); $(this).parent('div').remove(); x--;
                    })
                });

            </script>


        </div>
    </div>
</section>
