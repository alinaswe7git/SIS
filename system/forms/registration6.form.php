
<section class="content">
    <div class="container-fluid">
        <div class="card">

        <div style="margin: 5px;" class="text-center">
                        <?php echo '<a class="btn btn-default btn-rounded mb-4"  href="' .$this->core->conf['conf']['path'] .'/startregistration/summery/"; >'; ?>
                            View Summery</a>
                    </div>

            <form style="padding: 10px;" method="POST" action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/saveprofession/" . $this->core->item; ?> " >

                <button class="add_field_button btn btn-primary">Add More Fields</button>

                <div style="padding: 10px;" class="input_fields_wrap card">

                    <div style="display: flex;">

                        <div  class="form-line col-sm-3">
                            <label for="institution1">Institution :*</label>
                            <input style="resize: none;" type="text" class="form-control" name="institution1" id="institution1">
                        </div>

                        <div class="form-line col-sm-3">
                            <label for="start_year1">Qualification </label>
                            <select class="form-control" name="qualification1" id=";qualification1">
                                <option>---select---</option>
                                <option value="certificate">Certificate</option>
                                <option value="advanced certificate">Advanced Certificate</option>
                                <option value="diploma">Diploma</option>
                                <option value="advanced diploma">Advanced Diploma</option>
                                <option value="Bachelor">Bachelor</option>
                                <option value="Masters">Masters</option>
                                <option value="phd">PHD</option>
                            </select>

                        </div>

                        <div class="form-line col-sm-3">
                            <label for="area_of_specialisation1">Area of specialisation	 :*</label>
                            <input class="form-control" name="area_of_specialisation1" id="area_of_specialisation1">
                        </div>

                        <div class="form-line col-sm-3">
                            <label for="date_obtained1">Date obtained :*</label>
                            <input type="date" class="form-control" name="date_obtained1" id="date_obtained1">
                        </div>
                        <div><input type="hidden" class="form-control" name="count" id="count" value="1"> </div>
                    </div>
                    
                    <div><input type="hidden" class="form-control" name="count" id="count" value="1"> </div> 

                </div>

                <br> <br>

                <div style="padding: 20px;" class="">
                    <!-- <a class="btn btn-primary" href="${contextPath}/${user.username}/application/1">Previous</a> -->
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
                            $(wrapper).append('<br><div><div><div style="display: flex;"><div class="form-line col-sm-3"><label for="institution">Institution :*</label><input type="text" class="form-control" name="institution'+x+'" id="institution'+x+'"></div><div class="form-line col-sm-3"><label for="start_year">Grade </label><select class="form-control" name="qualification'+x+'" id="qualification'+x+'"><option>---select---</option><option value="certificate">Certificate</option><option value="advanced certificate">Advanced Certificate</option><option value="diploma">Diploma</option><option value="advanced diploma">Advanced Diploma</option><option value="Bachelor">Bachelor</option><option value="Masters">Masters</option><option value="phd">PHD</option></select></div><div class="form-line col-sm-3"><label for="area_of_specialisation">Area of specialisation	 :*</label><input class="form-control" name="area_of_specialisation'+x+'" id="area_of_specialisation'+x+'"></div><div class="form-line col-sm-3"><label for="date_obtained">Date obtained :*</label><input type="date" class="form-control" name="date_obtained'+x+'" id="date_obtained'+x+'"></div></div></div> <a href="#" class="remove_field">Remove</a></div>'); //add input box
                            //alert(document.getElementsByName("institution1aa"));
                        }
                        $(wrapper).append('<br><div><input type="hidden" class="form-control" name="count" id="count" value="'+ x +'"> </div> ' ); //add input box

                    });

                    $(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
                        //alert("before" + x);
                        e.preventDefault(); $(this).parent('div').remove(); x--;
                        //alert("after" + x);
                    })
                });

            </script>


        </div>
    </div>
</section>
