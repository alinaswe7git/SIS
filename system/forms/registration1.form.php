<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div style="margin: 10px;" class="text-center">
                <?php echo '<a class="btn btn-default btn-rounded mb-4"  href="' .$this->core->conf['conf']['path'] .'/startregistration/summery/"; >'; ?>
                    View Summery</a>

            </div>


            <form method="POST" action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/savekin/" . $this->core->item; ?> ">


                <div style="padding: 20px;" class="">
                    <div class="text-center">
                        <h2>Employment details</h2>
                    </div>
                    <br>

                    <div style="display: flex;">
                        <div class="form-line col-sm-6 ">
                            <label for="employment_employer">Employer Full Name:</label>
                            <input name="employment_employer" Class="form-control" />
                        </div>

                        <div class="form-line col-sm-6 ">
                            <label for="employment_jobtitle">Job Title:</label>
                            <input name="employment_jobtitle" Class="form-control" />
                        </div>
                    </div>

                    <div style="display: flex;">

                        <div class="form-line col-sm-6 ">
                            <label for="employment_postaladdress">Postal Address:</label>
                            <input name="employment_postaladdress" Class="form-control" />
                        </div>


                        <div class="form-line col-sm-6 ">
                            <label for="employment_telephone">Telephone Number:*</label>
                            <input name="employment_telephone" Class="form-control" />
                        </div>

                    </div>

                    <div class="form-line col-sm-6 ">
                        <label for="employment_dateofappointment">Date of Appointment:</label>
                        <input type="date" name="employment_dateofappointment" Class="form-control datepicker" />
                    </div>

                </div>

                <br> <br> <br> <br>

                <div class="text-center">
                    <h2>Next of Kin details</h2>
                </div>

                <div style="padding: 20px;" class="">

                    <div style="display: flex;">
                        <div class="form-line col-sm-6">
                            <label for="nextofkin_fullname">Full Name:*</label>
                            <input name="nextofkin_fullname" Class="form-control" />
                        </div>

                        <div class="form-line col-sm-6 ">
                            <label for="nextofkin_relationship">Relationship:</label>
                            <input name="nextofkin_relationship" Class="form-control" />
                        </div>
                    </div>

                    <div style="display: flex;">
                        <div class="form-line col-sm-6 ">
                            <label for="nextofkin_postaladdress">Postal:</label>
                            <input name="nextofkin_postaladdress" Class="form-control" />
                        </div>

                        <div class="form-line col-sm-6 ">
                            <label for="nextofkin_telephone">Telephone Number:*</label>
                            <input name="nextofkin_telephone" Class="form-control" />
                        </div>
                    </div>

                    <div class="form-line col-sm-6">
                        <label for="nextofkin_email">Email:</label>
                        <input name="nextofkin_email" Class="form-control" />
                    </div>
                </div>


                <br> <br>

                <div style="padding: 20px;" class="">

                    <div class="text-center">
                        <h2>Sponsor details</h2>
                    </div>
                    <br>

                    <div style="display: flex;">
                        <div class="form-line col-sm-6 ">
                            <label for="sponsor_sponsorname">Full Name:*</label>
                            <input name="sponsor_sponsorname" Class="form-control" />
                        </div>

                        <div class="form-line col-sm-6 ">
                            <label for="sponsor_relationship">Relationship:</label>
                            <input name="sponsor_relationship" Class="form-control" required="" />
                        </div>
                    </div>

                    <div style="display: flex;">

                        <div class="form-line col-sm-6 ">
                            <label for="sponsor_telephone">Telephone Number:*</label>
                            <input name="sponsor_telephone" class="form-control" />
                        </div>

                        <div class="form-line col-sm-6 ">
                            <label for="sponsor_postaladdress">Postal Address:*</label>
                            <input name="sponsor_postaladdress" class="form-control" />
                        </div>
                    </div>

                    <div class="form-line col-sm-6 ">
                        <label for="sponsor_email">Email:</label>
                        <input name="sponsor_email" class="form-control" />
                    </div>

                </div>

                <br> <br>

                <div style="padding: 20px;" class="">
                    <button type="submit" class="btn btn-primary" name="next">Next</button>
                </div>

            </form>
        </div>
    </div>
</section>
