<section class="content">
    <div class="container-fluid">
        <div class="card">

            <main class="d-flex align-items-center min-vh-100 py-3 py-md-0">
                <div class="container">
                    <div class="card login-card">
                        <div class="row no-gutters">
                            <div class="col-md-5">
                                <img src="<?php echo $this->core->conf['conf']['path']; ?>/templates/loanpro/images/login3.jpg" alt="login" class="login-card-img">
                            </div>
                            <div class="col-md-6">
                                <div class="card-body">
                                    <div class="brand-wrapper">
                                        <img style="height: 100px; width: 100px;" src="<?php echo $this->core->conf['conf']['path']; ?>/templates/loanpro/images/logo-large.png" alt="logo" class="logo">
                                    </div>
                                    <p class="login-card-description">Create account</p>
                                    <p style="color: red;" >password: should be 8 characters or more</p>
                                    <div class="tab">
                                        <button class="tablinks" onclick="openCity(event, 'regEmail')">Register With Email</button>
                                        <button class="tablinks" onclick="openCity(event, 'regPhone')">Register with Phone</button>
                                    </div>

                                    <div id="regEmail" class="tabcontent">
                                        <form method="POST" name="addapplicant" action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/save/" . $this->core->item; ?> ">
                                            <div class="form-group">
                                                <!-- <label for="emailorphone" class="sr-only">Email</label> -->
                                                <input type="email" name="emailorphone" id="emailorphone" class="form-control" placeholder="Email" required>
                                            </div>

                                            <div class="form-group mb-4">
                                                 <label for="password" class="sr-only">Password</label>
                                                <input pattern=".{8,}" type="password" name="password" id="password" minlength="8" class="form-control" placeholder="Enter Password" required>
                                            </div>

                                            <div class="form-group mb-4">
                                                <!-- <label for="password" class="sr-only"> Confirm Password</label> -->
                                                <input pattern=".{8,}" type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Confirm Password" required>
                                                <span id='message'></span>
                                            </div>
                                            <input name="login" id="login" class="btn btn-block login-btn mb-4" type="submit" value="Create Account">
                                        </form>
                                    </div>

                                    <div id="regPhone" class="tabcontent">
                                        <form method="POST" name="addapplicant" action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/save/" . $this->core->item; ?> ">
                                            <div class="form-group">
                                                <!-- <label for="emailorphone" class="sr-only">Email</label> -->
                                                <input type="text" name="emailorphone" id="emailorphone" class="form-control" placeholder="Phone" required>
                                                <small>Format: 260-971######</small><br>
                                            </div>

                                            <div class="form-group mb-4">
                                                <!-- <label for="password" class="sr-only">Password</label> -->
                                                <input pattern=".{8,}" type="password" name="password" id="password" class="form-control" placeholder="Enter Password" required>
                                            </div>

                                            <div class="form-group mb-4">
                                                <!-- <label for="password" class="sr-only"> Confirm Password</label> -->
                                                <input pattern=".{8,}" type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Confirm Password" required>
                                                <span id='message'></span>
                                            </div>
                                            <input name="login" id="login" class="btn btn-block login-btn mb-4" type="submit" value="Create Account">
                                            </form>
                                    </div>

                                    <nav class="login-card-footer-nav">
                                        <a href="#!">Terms of use.</a>
                                        <a href="#!">Privacy policy</a>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="card login-card">
        <img src="assets/images/login.jpg" alt="login" class="login-card-img">
        <div class="card-body">
          <h2 class="login-card-title">Login</h2>
          <p class="login-card-description">Sign in to your account to continue.</p>
          <form action="#!">
            <div class="form-group">
              <label for="email" class="sr-only">Email</label>
              <input type="email" name="email" id="email" class="form-control" placeholder="Email">
            </div>
            <div class="form-group">
              <label for="password" class="sr-only">Password</label>
              <input type="password" name="password" id="password" class="form-control" placeholder="Password">
            </div>
            <div class="form-prompt-wrapper">
              <div class="custom-control custom-checkbox login-card-check-box">
                <input type="checkbox" class="custom-control-input" id="customCheck1">
                <label class="custom-control-label" for="customCheck1">Remember me</label>
              </div>              
              <a href="#!" class="text-reset">Forgot password?</a>
            </div>
            <input name="login" id="login" class="btn btn-block login-btn mb-4" type="button" value="Login">
          </form>
          <p class="login-card-footer-text">Don't have an account? <a href="#!" class="text-reset">Register here</a></p>
        </div>
      </div> -->
                </div>
            </main>

            <script>
                $('#password, #password_confirm').on('keyup', function() {
                    if ($('#password').val() == $('#password_confirm').val()) {
                        $('#message').html('Password Matching').css('color', 'green');
                    } else
                        $('#message').html('Password not Matching').css('color', 'red');
                });
            </script>

            <!-- added Links by Nicholas -->
  <link rel="stylesheet" href="<?php echo $this->core->conf['conf']['path']; ?>/templates/loanpro/css/newUserLogin.css">
    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- JAVASCRIPT -->

        </div>
    </div>
</section>



<style>
    body {
        font-family: Arial;
    }

    /* Style the tab */
    .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        display: inline-block;
        width: auto;
        background-color: #f1f1f1;
    }

    /* Style the buttons inside the tab */
    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
        font-size: 17px;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #ddd;
    }

    /* Create an active/current tablink class */
    .tab button.active {
        background-color: #ccc;
    }

    /* Style the tab content */
    .tabcontent {
        display: none;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-top: none;
    }
</style>


<script>
    function openCity(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }
</script>
