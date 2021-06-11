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
                                    <p class="login-card-description">Sign into your account</p>
                                    <form method="POST" action="<?php echo $this->core->conf['conf']['path'] . "/startregistration/saveaccess/" . $this->core->item; ?> ">
                                        <div class="form-group">
                                            <label for="email_phone_number" class="sr-only">Email/Phone number</label>
                                            <input type="text" name="email_phone_number" id="email_phone_number" class="form-control"
                                                placeholder="Email  or Phone number">
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="password" class="sr-only">Password</label>
                                            <input type="password" name="password" id="password" class="form-control"
                                                placeholder="***********">
                                        </div>
                                        <input name="login" id="login" class="btn btn-block login-btn mb-4"
                                            type="submit" value="Login">
                                    </form>
                                    <a href="#!" class="forgot-password-link">Forgot password?</a>
                                    <p class="login-card-footer-text">Don't have an account? <?php echo '<a class="text-reset"  href="' .$this->core->conf['conf']['path'] .'/startregistration/create/"; >'; ?>Register here</a></p>
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
            <!-- added Links by Nicholas -->
  <link rel="stylesheet" href="<?php echo $this->core->conf['conf']['path']; ?>/templates/loanpro/css/newUserLogin.css">
    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- JAVASCRIPT -->
            
 
        
        
            
        
        </div>
    </div>
</section>