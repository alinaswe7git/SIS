<section class="content">
    <div class="container-fluid">
        <div class="card">

        <div style="margin: 10px;" class="text-center">
                        <?php echo '<a class="btn btn-default btn-rounded mb-4"  href="' .$this->core->conf['conf']['path'] .'/startregistration/summery/"; >'; ?>
                            View Summery</a>
                    </div>

            <form style="padding: 20px;" method="POST" action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/savesubject/" . $this->core->item; ?> " >

                <button class="add_field_button btn btn-primary">Add More Fields</button>

                <div style="padding: 20px;" class="input_fields_wrap card">
                 
                    <div style="display: flex;">

                        <div class="form-line col-sm-6">
                            <label for="school">Subject :*</label>
                            <select class="form-control" name="subject1" id="subject1">
                                <?php echo $subject; ?>
                            </select>
                        </div>

                        <div class="form-line col-sm-3">
                            <label for="start_year">Grade </label>
                            <select class="form-control" name="grade1" id="grade1">
                            <option >---select---</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>

                            <option value="A+">A+</option>
                            <option value="A">A</option>
                            <option value="B+">B+</option>
                            <option value="B">B</option>
                            <option value="1">C+</option>
                            <option value="1">C</option>
                            </select>
                            
                        </div>

                        <div class="form-line col-sm-3">
                            <label for="start_year">Level </label>
                            <select class="form-control" name="level1" id="level1">
                                <option >--select--</option>
                            <option value="o_level">O Level</option>
                            <option value="a_level">A Level</option>
                            </select>
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
                            $(wrapper).append('<br><div><div style="display: flex;"><div class="form-line col-sm-6"><label for="school">Subject :*</label><select class="form-control" name="subject'+ x +'" id="subject'+ x +'"> <?php echo $subject; ?> </select></div><div class="form-line col-sm-3"><label for="start_year">Grade </label><select class="form-control" name="grade'+ x +'" id="grade'+ x +'"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="A+">A+</option><option value="A">A</option><option value="B+">B+</option><option value="B">B</option><option value="1">C+</option><option value="1">C</option></select></div><div class="form-line col-sm-3"><label for="start_year">Level </label><select class="form-control" name="level'+ x +'" id="level'+ x +'"><option value="o_level">O Level</option><option value="a_level">A Level</option></select></div></div>  <a href="#" class="remove_field">Remove</a></div>'); //add input box
                            

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
