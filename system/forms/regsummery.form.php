
<section class="content">
    <div class="container-fluid">
        <div style="padding: 20px;" class="card">

            <form action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/logout/" . $this->core->item; ?> " method="post">
                <input type="submit" style="width: 100px;" class="btn-primary" value="Logout">
            </form>
            <br>
            <div id="start_application" style="display:flex; justify-content:flex-end; width:100%; padding:0;">
                <?php echo '<a href="' .$this->core->conf['conf']['path'] .'/startregistration/program/"; >'; ?>
                    <input id="start_application" type="button" value="Start Application" />
                </a>
            </div>
            <br>

            <style>
                .formsum {
                    padding: 15px;
                    border: 1px solid #666;
                    background: #fff;
                    display: none;
                }

                #formButton1 {
                    display: block;
                    margin-right: auto;
                    margin-left: auto;
                }
            </style>
            <p style="color: red;">Make sure to enter all the required infrormation before submitting the application </p>
            <!-- <fieldset style="padding: 10px;">
                <legend>View Summery:</legend>

                <div style="display:flex">
                    <div style="margin: 10px;" class="text-center">
                        <a href="" class="btn btn-default btn-rounded mb-4" data-toggle="modal" data-target="#personalForm">
                            Personal Information</a>
                    </div>

                    <div style="margin: 10px;" class="text-center">
                        <a href="" class="btn btn-default btn-rounded mb-4" data-toggle="modal" data-target="#programForm">
                            Program Information</a>
                    </div>

                    <div style="margin: 10px;" class="text-center">
                        <a href="" class="btn btn-default btn-rounded mb-4" data-toggle="modal" data-target="#kinForm">
                            Employment,kin & Sponsor Information</a>
                    </div>

                </div>

                <div style="display: flex;">

                    <div style="margin: 10px;" class="text-center">
                        <a href="" class="btn btn-default btn-rounded mb-4" data-toggle="modal" data-target="#eduHistoryForm">
                            Education hisotry</a>
                    </div>

                    <div style="margin: 10px;" class="text-center">
                        <a href="" class="btn btn-default btn-rounded mb-4" data-toggle="modal" data-target="#olevelForm">
                            O-Level Information</a>
                    </div>

                    <div style="margin: 10px;" class="text-center">
                        <a href="" class="btn btn-default btn-rounded mb-4" data-toggle="modal" data-target="#tertiaryForm">
                            Tertiary Education</a>
                    </div>


                    <div style="margin: 10px;" class="text-center">
                        <a href="" class="btn btn-default btn-rounded mb-4" data-toggle="modal" data-target="#uploadForm">
                            Uploads </a>
                    </div>

                </div>

                <input type="button" value="Submit" >
            </fieldset> -->


            <!-- ======= Featured Services Section ======= -->
            <section id="featured-services" class="featured-services">
                <div class="container" data-aos="fade-up">

                    <div class="row">
                        <div data-toggle="modal" data-target="#programForm" class="col-md-6 col-lg-2 d-flex align-items-stretch mb-5 mb-lg-0">
                            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
                                <div class="icon"><i class="bx bxl-dribbble"></i></div>
                                <h4 class="title"><a href="">PROGRAM DETAILS</a></h4>
                                <p class="description">Review Personal Details</p>
                            </div>
                        </div>

                        <div data-toggle="modal" data-target="#personalForm" class="col-md-6 col-lg-2 d-flex align-items-stretch mb-5 mb-lg-0">
                            <div class="icon-box" data-aos="fade-up" data-aos-delay="200">
                                <div class="icon"><i class="bx bx-file"></i></div>
                                <h4 class="title"><a href="">PERSONAL DETAILS</a></h4>
                                <p class="description">Review Personal Details</p>
                            </div>
                        </div>

                        <div data-toggle="modal" data-target="#kinForm" class="col-md-6 col-lg-2 d-flex align-items-stretch mb-5 mb-lg-0">
                            <div class="icon-box" data-aos="fade-up" data-aos-delay="300">
                                <div class="icon"><i class="bx bx-tachometer"></i></div>
                                <h4 class="title"><a href="">SPONSOR DETAILS</a></h4>
                                <p class="description">Review Sponsor, Employment & Kin</p>
                            </div>
                        </div>

                        <div data-toggle="modal" data-target="#eduHistoryForm" class="col-md-6 col-lg-2 d-flex align-items-stretch mb-5 mb-lg-0">
                            <div class="icon-box" data-aos="fade-up" data-aos-delay="400">
                                <div class="icon"><i class="bx bx-world"></i></div>
                                <h4 class="title"><a href="">EDUCATION HISTORY</a></h4>
                                <p class="description">Review Education hisotry</p>
                            </div>
                        </div>

                    </div>
                    <br>
                    <div class="row">
                        <div data-toggle="modal" data-target="#olevelForm" class="col-md-6 col-lg-2 d-flex align-items-stretch mb-5 mb-lg-0">
                            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
                                <div class="icon"><i class="bx bxl-dribbble"></i></div>
                                <h4 class="title"><a href="">O-LEVEL DETAILS</a></h4>
                                <p class="description">Review 0-level GCE Details</p>
                            </div>
                        </div>

                        <div data-toggle="modal" data-target="#tertiaryForm" class="col-md-6 col-lg-2 d-flex align-items-stretch mb-5 mb-lg-0">
                            <div class="icon-box" data-aos="fade-up" data-aos-delay="200">
                                <div class="icon"><i class="bx bx-file"></i></div>
                                <h4 class="title"><a href="">TERTIARY DETAILS</a></h4>
                                <p class="description">Review Tertiary Education Details</p>
                            </div>
                        </div>

                        <div data-toggle="modal" data-target="#uploadForm" class="col-md-6 col-lg-2 d-flex align-items-stretch mb-5 mb-lg-0">
                            <div class="icon-box" data-aos="fade-up" data-aos-delay="300">
                                <div class="icon"><i class="bx bx-tachometer"></i></div>
                                <h4 class="title"><a href="">UPLOAD DETAILS</a></h4>
                                <p class="description">Review Uloaded Details</p>
                            </div>
                        </div>



                    </div>

                    <br>
                    <div class="row">
                        <div id="submitdiv" data-toggle="modal" data-target="#submitForm" class="col-md-6 col-lg-2 d-flex align-items-stretch mb-5 mb-lg-0">
                            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
                                <div class="icon"><i class="bx bxl-dribbble"></i></div>
                                <h4 class="title"><a href="">Submit Application</a></h4>
                            </div>
                        </div>


                    </div>

                </div>
            </section><!-- End Featured Services Section -->

            <style>
                /* #Featured Services--------------------------------------------------------------*/

                .featured-services .icon-box {
                    padding: 10px;
                    position: relative;
                    overflow: hidden;
                    background: #fff;
                    box-shadow: 0 0 29px 0 rgba(68, 88, 144, 0.12);
                    transition: all 0.3s ease-in-out;
                    border-radius: 8px;
                    z-index: 1;
                }

                .featured-services .icon-box::before {
                    content: '';
                    position: absolute;
                    background: #cbe0fb;
                    right: 0;
                    left: 0;
                    bottom: 0;
                    top: 100%;
                    transition: all 0.3s;
                    z-index: -1;
                }

                .featured-services .icon-box:hover::before {
                    background: #106eea;
                    top: 0;
                    border-radius: 0px;
                }

                .featured-services .icon {
                    margin-bottom: 15px;
                }

                .featured-services .icon i {
                    font-size: 48px;
                    line-height: 1;
                    color: #106eea;
                    transition: all 0.3s ease-in-out;
                }

                .featured-services .title {
                    font-weight: 700;
                    margin-bottom: 15px;
                    font-size: 18px;
                }

                .featured-services .title a {
                    color: #111;
                }

                .featured-services .description {
                    font-size: 13px;
                    line-height: 28px;
                    margin-bottom: 0;
                }

                .featured-services .icon-box:hover .title a,
                .featured-services .icon-box:hover .description {
                    color: #fff;
                }

                .featured-services .icon-box:hover .icon i {
                    color: #fff;
                }
            </style>


            <script>
                !(function($) {
                        "use strict";

                        // Preloader
                        $(window).on('load', function() {
                            if ($('#preloader').length) {
                                $('#preloader').delay(100).fadeOut('slow', function() {
                                    $(this).remove();
                                });
                            }
                        });

                        // Smooth scroll for the navigation menu and links with .scrollto classes
                        var scrolltoOffset = $('#header').outerHeight() - 21;
                        if (window.matchMedia("(max-width: 991px)").matches) {
                            scrolltoOffset += 20;
                        }
                        $(document).on('click', '.nav-menu a, .mobile-nav a, .scrollto', function(e) {
                            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                                var target = $(this.hash);
                                if (target.length) {
                                    e.preventDefault();

                                    var scrollto = target.offset().top - scrolltoOffset;

                                    if ($(this).attr("href") == '#header') {
                                        scrollto = 0;
                                    }

                                    $('html, body').animate({
                                        scrollTop: scrollto
                                    }, 1500, 'easeInOutExpo');

                                    if ($(this).parents('.nav-menu, .mobile-nav').length) {
                                        $('.nav-menu .active, .mobile-nav .active').removeClass('active');
                                        $(this).closest('li').addClass('active');
                                    }

                                    if ($('body').hasClass('mobile-nav-active')) {
                                        $('body').removeClass('mobile-nav-active');
                                        $('.mobile-nav-toggle i').toggleClass('icofont-navigation-menu icofont-close');
                                        $('.mobile-nav-overly').fadeOut();
                                    }
                                    return false;
                                }
                            }
                        });

                        // Activate smooth scroll on page load with hash links in the url
                        $(document).ready(function() {
                            if (window.location.hash) {
                                var initial_nav = window.location.hash;
                                if ($(initial_nav).length) {
                                    var scrollto = $(initial_nav).offset().top - scrolltoOffset;
                                    $('html, body').animate({
                                        scrollTop: scrollto
                                    }, 1500, 'easeInOutExpo');
                                }
                            }
                        });

                        // Navigation active state on scroll
                        var nav_sections = $('section');
                        var main_nav = $('.nav-menu, .mobile-nav');

                        $(window).on('scroll', function() {
                            var cur_pos = $(this).scrollTop() + 200;

                            nav_sections.each(function() {
                                var top = $(this).offset().top,
                                    bottom = top + $(this).outerHeight();

                                if (cur_pos >= top && cur_pos <= bottom) {
                                    if (cur_pos <= bottom) {
                                        main_nav.find('li').removeClass('active');
                                    }
                                    main_nav.find('a[href="#' + $(this).attr('id') + '"]').parent('li').addClass('active');
                                }
                                if (cur_pos < 300) {
                                    $(".nav-menu ul:first li:first, .mobile-menu ul:first li:first").addClass('active');
                                }
                            });
                        });

                        // Mobile Navigation
                        if ($('.nav-menu').length) {
                            var $mobile_nav = $('.nav-menu').clone().prop({
                                class: 'mobile-nav d-lg-none'
                            });
                            $('body').append($mobile_nav);
                            $('body').prepend('<button type="button" class="mobile-nav-toggle d-lg-none"><i class="icofont-navigation-menu"></i></button>');
                            $('body').append('<div class="mobile-nav-overly"></div>');

                            $(document).on('click', '.mobile-nav-toggle', function(e) {
                                $('body').toggleClass('mobile-nav-active');
                                $('.mobile-nav-toggle i').toggleClass('icofont-navigation-menu icofont-close');
                                $('.mobile-nav-overly').toggle();
                            });

                            $(document).on('click', '.mobile-nav .drop-down > a', function(e) {
                                e.preventDefault();
                                $(this).next().slideToggle(300);
                                $(this).parent().toggleClass('active');
                            });

                            $(document).click(function(e) {
                                var container = $(".mobile-nav, .mobile-nav-toggle");
                                if (!container.is(e.target) && container.has(e.target).length === 0) {
                                    if ($('body').hasClass('mobile-nav-active')) {
                                        $('body').removeClass('mobile-nav-active');
                                        $('.mobile-nav-toggle i').toggleClass('icofont-navigation-menu icofont-close');
                                        $('.mobile-nav-overly').fadeOut();
                                    }
                                }
                            });
                        } else if ($(".mobile-nav, .mobile-nav-toggle").length) {
                            $(".mobile-nav, .mobile-nav-toggle").hide();
                        }
            </script>


            <!-- /////////////////////////////////////////////////// -->

            <!-- personal information -->
            <div class="modal fade" id="personalForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div style="max-width: 700px;" class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h4 class="modal-title w-200 font-weight-bold">Personal details
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <!-- personal information -->
                            <form method="POST" action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/savepersonal/" . $this->core->item; ?> ">

                                <div style="padding: 20px;" class="">

                                    <div style="display: flex;">
                                        <div class="form-line col-sm-4 ">
                                            <label for="first_name">First Name </label>
                                            <input readonly readonly name="first_name" Class="form-control" value="<?php echo $personal['firstname']; ?>" required="required" />
                                        </div>

                                        <div class="form-line col-sm-4 ">
                                            <label for="middle_name">Middle Name:</label>
                                            <input readonly readonly readonly name="middle_name" value="<?php echo $personal['middlename']; ?>" Class="form-control" />
                                        </div>

                                        <div class="form-line col-sm-4 ">
                                            <label for="last_name">Last Name</label>
                                            <input readonly readonly name="last_name" value="<?php echo $personal['lastname']; ?>" Class="form-control" required="required" />
                                        </div>


                                    </div>

                                    <br> <br>

                                    <div style="display: flex;">

                                        <div class="form-line col-sm-4">
                                            <label for="nrc">NRC </label>
                                            <input readonly readonly name="nrc" id="nrc" value="<?php echo $personal['NRCnumber']; ?>" placeholder="******/**/**" Class="form-control" required />
                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="title">Title </label>
                                            <input readonly readonly name="title" id="title" value="<?php echo $personal['title']; ?>" Class="form-control" />
                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="dob">Date of Birth </label>
                                            <input readonly readonly value="<?php echo $personal['dateofbirth']; ?>" type="date" name="dob" Class="form-control" />
                                        </div>

                                    </div>

                                    <br> <br>

                                    <div style="display: flex;">

                                        <div class="form-line col-sm-4">
                                            <label for="gender">Gender </label>
                                            <input readonly readonly value="<?php echo $personal['gender']; ?>" type="text" name="gender" Class="form-control" />

                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="maritalstatus">Marital Status </label>
                                            <input readonly readonly value="<?php echo $personal['maritalstatus']; ?>" type="text" name="maritalstatus" Class="form-control" />

                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="nationality">Nationality </label>
                                            <input readonly readonly value="<?php echo $personal['nationality']; ?>" type="text" name="nationality" Class="form-control" />

                                        </div>

                                    </div>

                                    <br> <br>

                                    <div style="display: flex;">

                                        <div class="form-line col-sm-4">
                                            <label for="country_of_residence">Country of Residence </label>
                                            <input readonly readonly value="<?php echo $personal['countryofresidence']; ?>" type="text" name="country_of_residence" Class="form-control" />

                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="place_of_birth">Place of Birth </label>
                                            <input readonly readonly value="<?php echo $personal['placeofbirth']; ?>" type="text" name="place_of_birth" Class="form-control" required />
                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="residencial_address">Residencial Address </label>
                                            <input readonly readonly value="<?php echo $personal['residentialaddress']; ?>" type="text" name="residencial_address" Class="form-control" />
                                        </div>

                                    </div>

                                    <br><br>

                                    <div style="display: flex;">

                                        <div class="form-line col-sm-4">
                                            <label for="mobile_number">Mobile Number </label>
                                            <input readonly readonly value="<?php echo $personal['mobilephone']; ?>" type="text" name="mobile_number" required Class="form-control" />
                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="telephone">Telephone </label>
                                            <input readonly readonly value="<?php echo $personal['telephone']; ?>" type="text" name="telephone" Class="form-control" />
                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="fax">Fax </label>
                                            <input readonly readonly value="<?php echo $personal['fax']; ?>" type="text" name="fax" Class="form-control" />
                                        </div>

                                    </div>

                                    <br><br>

                                    <div style="display: flex;">

                                        <div class="form-line col-sm-4">
                                            <label for="email">Email </label>
                                            <input readonly readonly value="<?php echo $personal['email']; ?>" type="text" name="email" Class="form-control" required />
                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="disability">Disability </label>
                                            <input readonly readonly value="<?php echo $personal['disability']; ?>" type="text" name="disability" Class="form-control" />
                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="datecreated">Date </label>
                                            <input readonly readonly value="<?php echo $personal['dateofcreation']; ?>" type="date" name="datecreated" Class="form-control" />
                                        </div>



                                    </div>



                                </div>

                                <script>
                                    $(document).ready(function() {
                                        $("#formButton").click(function() {
                                            $("#form1").toggle();
                                        });
                                    });
                                </script>
                            </form>

                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button id="go_back_personal" class="btn btn-default"><?php echo '<a href="' .$this->core->conf['conf']['path'] .'/startregistration/personal/"; >'; ?>Add personal
                                    information</a></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- program summery -->
            <div class="modal fade" id="programForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div style="max-width: 700px;" class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h4 class="modal-title w-200 font-weight-bold">Program Details
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <!-- program summery -->
                            <form method="POST" action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/savepersonal/" . $this->core->item; ?> ">

                                <div style="padding: 20px;" class="">

                                    <div style="display: flex;">
                                        <div class="form-line col-sm-4 ">
                                            <label for="first_name">Level :</label>
                                            <input readonly readonly name="first_name" Class="form-control" value="<?php echo $program['level']; ?>" required="required" />
                                        </div>

                                        <div class="form-line col-sm-4 ">
                                            <label for="middle_name">Mode of Study</label>
                                            <input readonly readonly readonly name="middle_name" value="<?php echo $program['modeofstudy']; ?>" Class="form-control" />
                                        </div>

                                        <div class="form-line col-sm-4 ">
                                            <label for="last_name">Campus </label>
                                            <input readonly readonly name="last_name" value="<?php echo $program['campus']; ?>" Class="form-control" required="required" />
                                        </div>


                                    </div>

                                    <br> <br>

                                    <div style="display: flex;">

                                        <div class="form-line col-sm-4">
                                            <label for="nrc">How you heard of Nipa </label>
                                            <input readonly readonly name="nrc" id="nrc" value="<?php echo $program['knowhow']; ?>"  Class="form-control" required />
                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="title">Program </label>
                                            <input readonly readonly name="title" id="title" value="<?php echo $program['program']; ?>" Class="form-control" />
                                        </div>


                                    </div>

                                </div>

                                <script>
                                    $(document).ready(function() {
                                        $("#formButton1").click(function() {
                                            $("#form1").toggle();
                                        });
                                    });
                                    $(document).ready(function() {
                                        $("#formButton2").click(function() {
                                            $("#form2").toggle();
                                        });
                                    });
                                </script>
                            </form>

                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button id="back_to_program" class="btn btn-default"><?php echo '<a href="' .$this->core->conf['conf']['path'] .'/startregistration/program/"; >'; ?>Add program
                                    information</a></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- employment,next of kin and sponsor summery -->
            <div class="modal fade" id="kinForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div style="max-width: 700px;" class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h4 class="modal-title w-200 font-weight-bold">Employment,Next of kin and Sponsor details
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <!-- kin summery -->
                            <form method="POST" action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/savepersonal/" . $this->core->item; ?> ">

                                <h2>Employment</h2>

                                <div style="padding: 20px;" class="">

                                    <div style="display: flex;">
                                        <div class="form-line col-sm-4 ">
                                            <label for="first_name">Employer :</label>
                                            <input readonly readonly name="first_name" Class="form-control" value="<?php echo $employment['employer']; ?>" required="required" />
                                        </div>

                                        <div class="form-line col-sm-4 ">
                                            <label for="middle_name">Job Title</label>
                                            <input readonly readonly readonly name="middle_name" value="<?php echo $employment['jobtitle']; ?>" Class="form-control" />
                                        </div>

                                        <div class="form-line col-sm-4 ">
                                            <label for="last_name">Postal Address </label>
                                            <input readonly readonly name="last_name" value="<?php echo $employment['postaladdress']; ?>" Class="form-control" required="required" />
                                        </div>


                                    </div>

                                    <br> <br>

                                    <div style="display: flex;">

                                        <div class="form-line col-sm-4">
                                            <label for="nrc">TelePhone</label>
                                            <input readonly readonly name="nrc" id="nrc" value="<?php echo $employment['telephone']; ?>" placeholder="******/**/**" Class="form-control" required />
                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="title">Date of Appointment </label>
                                            <input readonly readonly name="title" id="title" value="<?php echo $employment['dateofappointment']; ?>" Class="form-control" />
                                        </div>

                                        <!-- <div class="form-line col-sm-4">
                                            <label for="dob">Date of Birth </label>
                                            <input readonly readonly value="<?php echo $personal['dateofbirth']; ?>"
                                                type="date" name="dob" Class="form-control" />
                                        </div> -->

                                    </div>

                                </div>

                                <h2>Next of kin</h2>

                                <div style="padding: 20px;" class="">

                                    <div style="display: flex;">
                                        <div class="form-line col-sm-4 ">
                                            <label for="first_name">Full Name :</label>
                                            <input readonly readonly name="first_name" Class="form-control" value="<?php echo $nextofkin['fullname']; ?>" required="required" />
                                        </div>

                                        <div class="form-line col-sm-4 ">
                                            <label for="middle_name">Relationship</label>
                                            <input readonly readonly readonly name="middle_name" value="<?php echo $nextofkin['relationship']; ?>" Class="form-control" />
                                        </div>

                                        <div class="form-line col-sm-4 ">
                                            <label for="last_name">Postal Address </label>
                                            <input readonly readonly name="last_name" value="<?php echo $nextofkin['postaladdress']; ?>" Class="form-control" required="required" />
                                        </div>


                                    </div>

                                    <br> <br>

                                    <div style="display: flex;">

                                        <div class="form-line col-sm-4">
                                            <label for="nrc">Telephone</label>
                                            <input readonly readonly name="nrc" id="nrc" value="<?php echo $nextofkin['telephone']; ?>" placeholder="******/**/**" Class="form-control" required />
                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="title">Email </label>
                                            <input readonly readonly name="title" id="title" value="<?php echo $nextofkin['email']; ?>" Class="form-control" />
                                        </div>



                                    </div>

                                </div>

                                <h2>Sponsor</h2>

                                <div style="padding: 20px;" class="">

                                    <div style="display: flex;">
                                        <div class="form-line col-sm-4 ">
                                            <label for="first_name">Sponsor Name :</label>
                                            <input readonly readonly name="first_name" Class="form-control" value="<?php echo $sponsor['sponsorname']; ?>" required="required" />
                                        </div>

                                        <div class="form-line col-sm-4 ">
                                            <label for="middle_name">Relationship</label>
                                            <input readonly readonly readonly name="middle_name" value="<?php echo $sponsor['relationship']; ?>" Class="form-control" />
                                        </div>

                                        <div class="form-line col-sm-4 ">
                                            <label for="last_name">Postal Address </label>
                                            <input readonly readonly name="last_name" value="<?php echo $sponsor['postaladdress']; ?>" Class="form-control" required="required" />
                                        </div>


                                    </div>

                                    <br> <br>

                                    <div style="display: flex;">

                                        <div class="form-line col-sm-4">
                                            <label for="nrc">Telephone</label>
                                            <input readonly readonly name="nrc" id="nrc" value="<?php echo $sponsor['telephone']; ?>" placeholder="******/**/**" Class="form-control" required />
                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="title">Email </label>
                                            <input readonly readonly name="title" id="title" value="<?php echo $sponsor['email']; ?>" Class="form-control" />
                                        </div>



                                    </div>

                                </div>


                                <script>
                                    $(document).ready(function() {
                                        $("#formButton1").click(function() {
                                            $("#form1").toggle();
                                        });
                                    });
                                    $(document).ready(function() {
                                        $("#formButton2").click(function() {
                                            $("#form2").toggle();
                                        });
                                    });
                                </script>
                            </form>

                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button id="back_to_kin" class="btn btn-default"><?php echo '<a href="' .$this->core->conf['conf']['path'] .'/startregistration/kin/"; >'; ?>Add
                                    Employment,kin </a></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Education hisotry and grade 12 infromation -->
            <div class="modal fade" id="eduHistoryForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div style="max-width: 700px;" class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h4 class="modal-title w-200 font-weight-bold">Education History
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <!-- Education hisotry and grade 12 infromation -->
                            <form action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/savepersonal/" . $this->core->item; ?> ">

                                <div style="padding: 20px;" class="">


                                    <div style="display: flex;">
                                        <div class="form-line col-sm-4 ">
                                            <label for="first_name">Examination Number </label>
                                            <input readonly readonly name="first_name" Class="form-control" value="<?php echo $grade12['examno']; ?>" required="required" />
                                        </div>

                                        <div class="form-line col-sm-4 ">
                                            <label for="middle_name">Examination Body :</label>
                                            <input readonly readonly readonly name="middle_name" value="<?php echo $grade12['exambody']; ?>" Class="form-control" />
                                        </div>

                                        <div class="form-line col-sm-4 ">
                                            <label for="last_name">Examination year </label>
                                            <input readonly readonly name="last_name" value="<?php echo $grade12['examyear']; ?>" Class="form-control" required="required" />
                                        </div>


                                    </div>

                                    <br> <br>

                                    <h2>Education History</h2>
                                    <br><br>

                                                                        <?php
                                    if ($educationhistory->num_rows > 0) {
                                        while ($row = $educationhistory->fetch_assoc()) {
                                    ?>

                                    <div style="display: flex;">

                                        <div class="form-line col-sm-4">
                                            <label for="nrc">School :</label>
                                            <input readonly readonly name="nrc" id="nrc" value="<?php echo $row['school']; ?>" placeholder="******/**/**" Class="form-control" required />
                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="title"> From </label>
                                            <input readonly readonly name="title" id="title" value="<?php echo $row['yearfrom']; ?>" Class="form-control" />
                                        </div>

                                        <div class="form-line col-sm-4">
                                            <label for="dob">To </label>
                                            <input readonly readonly value="<?php echo $row['yearto']; ?>" type="date" name="dob" Class="form-control" />
                                        </div>

                                    </div>
                                    <br> <br>
                                    <div style="display: flex;">
                                        <div class="form-line col-sm-4 ">
                                            <label for="first_name">Level of attainment </label>
                                            <input readonly readonly name="first_name" Class="form-control" value="<?php echo $row['level']; ?>" required="required" />
                                        </div>


                                    </div>

                                    <br> <br>
                                    <?php
                                        }
                                    }
                                    ?>

                                </div>

                                <script>
                                    $(document).ready(function() {
                                        $("#formButton4").click(function() {
                                            $("#form4").toggle();
                                        });
                                    });
                                </script>
                            </form>

                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button id="back_to_education_history" class="btn btn-default"><?php echo '<a href="' .$this->core->conf['conf']['path'] .'/startregistration/previous/"; >'; ?>
                                    Education
                                    information</a></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Olevel Education infromation -->
            <div class="modal fade" id="olevelForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div style="max-width: 700px;" class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h4 class="modal-title w-200 font-weight-bold">Education History
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <!-- Olevel Education infromation -->
                            <form action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/savepersonal/" . $this->core->item; ?> ">

                                <h2>O level Information</h2>
                                <div style="padding: 20px;">

                                    <?php
                                    if ($olevel->num_rows > 0) {
                                        while ($row = $olevel->fetch_assoc()) {
                                    ?>
                                            <div style="display: flex;">
                                                <div class="form-line col-sm-4 ">
                                                    <label for="first_name">Subject :</label>
                                                    <input readonly readonly name="first_name" Class="form-control" value="<?php echo $row['subject']; ?>" required="required" />
                                                </div>

                                                <div class="form-line col-sm-4 ">
                                                    <label for="middle_name">Grade :</label>
                                                    <input readonly readonly readonly name="middle_name" value="<?php echo $row['level']; ?>" Class="form-control" />
                                                </div>

                                                <div class="form-line col-sm-4 ">
                                                    <label for="last_name">Level </label>
                                                    <input readonly readonly name="last_name" value="<?php echo $row['grade']; ?>" Class="form-control" required="required" />
                                                </div>


                                            </div>
                                    <?php
                                        }
                                    }
                                    ?>


                                </div>

                                <script>
                                    $(document).ready(function() {
                                        $("#formButton5").click(function() {
                                            $("#form5").toggle();
                                        });
                                    });
                                </script>
                            </form>

                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button id="back_to_education_history_olevel" class="btn btn-default"><?php echo '<a href="' .$this->core->conf['conf']['path'] .'/startregistration/subjects/"; >'; ?>Add O level
                                    information</a></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tertiary Education infromation -->
            <div class="modal fade" id="tertiaryForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div style="max-width: 700px;" class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h4 class="modal-title w-200 font-weight-bold">Tertiary Education
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <!-- Tertiary Education infromation -->
                            <form method="POST" id="form6" action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/savepersonal/" . $this->core->item; ?> ">

                                <div style="padding: 20px;">


                                    <?php
                                    if ($tertiaryedu->num_rows > 0) {
                                        while ($row = $tertiaryedu->fetch_assoc()) {
                                    ?>
                                            <div style="display: flex;">
                                                <div class="form-line col-sm-5 ">
                                                    <label for="first_name">Institution :</label>
                                                    <input readonly readonly name="first_name" Class="form-control" value="<?php echo $row['institution']; ?>" required="required" />
                                                </div>

                                                <div class="form-line col-sm-4 ">
                                                    <label for="middle_name">Specialisation :</label>
                                                    <input readonly readonly readonly name="middle_name" value="<?php echo $row['specialisation']; ?>" Class="form-control" />
                                                </div>

                                                <div class="form-line col-sm-4 ">
                                                    <label for="last_name">Qualification </label>
                                                    <input readonly readonly name="last_name" value="<?php echo $row['qualification']; ?>" Class="form-control" required="required" />
                                                </div>
                                            </div>
                                            <br>
                                            <div style="display: flex;">
                                                <div class="form-line col-sm-4 ">
                                                    <label for="last_name">Date obtained </label>
                                                    <input readonly readonly name="last_name" value="<?php echo $row['dateobtained']; ?>" Class="form-control" required="required" />
                                                </div>
                                            </div>
                                            <br><br><br>


                                    <?php
                                        }
                                    }
                                    ?>


                                </div>

                                <script>
                                    $(document).ready(function() {
                                        $("#formButton6").click(function() {
                                            $("#form6").toggle();
                                        });
                                    });
                                </script>
                            </form>

                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button id="back_tertiary_education" class="btn btn-default"><?php echo '<a href="' .$this->core->conf['conf']['path'] .'/startregistration/program/"; >'; ?>suAdd Tertiary
                                    Education</a></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload infromation -->
            <div class="modal fade" id="uploadForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div style="max-width: 700px;" class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h4 class="modal-title w-200 font-weight-bold">Uploads
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <!-- Upload infromation -->
                            <form action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/savepersonal/" . $this->core->item; ?> ">

                                <div style="padding: 20px;" class="">


                                    <div style="display: flex;">
                                        <div class="form-line col-sm-6 ">
                                            <label for="first_name">Deposit Slip </label>
                                            <input readonly readonly name="first_name" Class="form-control" value="<?php echo $upload['depositslip']; ?>" required="required" />
                                        </div>

                                        <div class="form-line col-sm-6 ">
                                            <label for="middle_name">Passport or NRC </label>
                                            <input readonly readonly readonly name="middle_name" value="<?php echo $upload['passportornrc']; ?>" Class="form-control" />
                                        </div>

                                    </div>
                                    <br>

                                    <div style="display: flex;">
                                        <div class="form-line col-sm-6 ">
                                            <label for="last_name">Grade 12 Certificate </label>
                                            <input readonly readonly name="last_name" value="<?php echo $upload['grade12certificate']; ?>" Class="form-control" required="required" />
                                        </div>

                                        <div class="form-line col-sm-6 ">
                                            <label for="last_name">Proof of Academic Qualification </label>
                                            <input readonly readonly name="last_name" value="<?php echo $upload['academic_professional_qualification']; ?>" Class="form-control" required="required" />
                                        </div>
                                    </div>



                                    <br>
                                    <div style="display: flex;">

                                        <div class="form-line col-sm-6">
                                            <label for="nrc">Proof of Academic Qualification 2 </label>
                                            <input readonly readonly name="nrc" id="nrc" value="<?php echo $upload['academic_professional_qualification_2']; ?>" Class="form-control" required />
                                        </div>

                                        <div class="form-line col-sm-6">
                                            <label for="title"> Reference (Postgraduate only) </label>
                                            <input readonly readonly name="title" id="title" value="<?php echo $upload['reference']; ?>" Class="form-control" />
                                        </div>

                                    </div>
                                    <br>

                                    <div style="display: flex;">
                                        <div class="form-line col-sm-6">
                                            <label for="title"> Reference (Postgraduate only) </label>
                                            <input readonly readonly name="title" id="title" value="<?php echo $upload['reference']; ?>" Class="form-control" />
                                        </div>
                                    </div>

                                    <br>

                                    <div style="display: flex;">

                                    </div>

                                </div>

                                <script>
                                    $(document).ready(function() {
                                        $("#formButton4").click(function() {
                                            $("#form4").toggle();
                                        });
                                    });
                                </script>
                            </form>

                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button id="back_to_upload" class="btn btn-default"><?php echo '<a href="' .$this->core->conf['conf']['path'] .'/startregistration/upload/"; >'; ?>
                                    Upload
                                    information</a></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Final Step  -->
            <div class="modal fade" id="submitForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div style="max-width: 700px;" class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h4 class="modal-title w-200 font-weight-bold">Submit Information
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body mx-3">
                            <!-- Olevel Education infromation -->
                            <form action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/submit/" . $this->core->item; ?> ">



                                <style>
                                    li {
                                        font-size: 15px;
                                    }

                                    p {
                                        font-size: 15px;
                                    }
                                </style>

                                <h2>Notice Before Submission</h2>
                                <p>Before you submit your application make sure the relevant information has been truthfully and accurately entered. This will increase your chances of being accepted</p>
                                <ul>
                                    <li>Program information</li>
                                    <li>Personal information</li>
                                    <li>Next of kin information</li>
                                    <li>Sponsor information</li>
                                    <li>Employment information</li>
                                    <li>O-level information</li>
                                    <li>Tertiary information</li>
                                    <li>Upload information</li>
                                    <li>Deposit Slip information</li>

                                </ul>
                                <br>
                                <p>Make sure the above information has be correctly entered where applicable.</p>
                                <script>
                                    $(document).ready(function() {
                                        $("#formButton5").click(function() {
                                            $("#form5").toggle();
                                        });
                                    });
                                </script>


                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <input type="submit" value="submit">
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                //alert("aaaa");
                $(".alert-dismissible").fadeTo(2000, 500).slideUp(500, function() {
                    $(".alert-dismissible").alert('close');
                });
            </script>


        </div>
</section>

<style> 

.container-fluid{
    
}

</style>