<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">    

  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>PID Generator Login</title>
    
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/app.min.css'); ?>">
  
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/style.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/components.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/custom.css'); ?>">
    
  <link rel='shortcut icon' type='image/x-icon' href='<?php echo site_url('assets/godata/dashboard/img/gd_ico.ico'); ?>' />

	<title>Login</title>
  </head>
  <body> 
  <?php $app_lang	= empty($app_lang) ? 'en' : $app_lang;?>
	
  <div class="loader"></div>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="card card-primary">
              <div class="card-header">
                <h4>PID Generator Login</h4>
              </div>
              <div class="card-body">
              <?php 
                if($this->session->flashdata('error')){
                  echo $this->session->flashdata('error');
                }
              ?> 
                <form method="POST" action="<?php echo base_url();?>generatedev/doLogin" id="theForm" class="needs-validation" novalidate="">
                  <div class="form-group">
                    <label for="email">Username</label>
                    <input id="username" type="text" class="form-control" name="username" tabindex="1" required autofocus onkeypress="checkspace(event)">
                    <div class="invalid-feedback">Please fill in your username</div>
                  </div>
                  <div class="form-group">
                    <div class="d-block">
                      <label for="password" class="control-label">Password</label>                      
                    </div>
                    <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                    <div class="invalid-feedback">please fill in your password</div>
                  </div>
                  
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      Login
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <div class="text-muted text-center">
              <a href="<?php echo base_url('generatedev/register');?>">Register</a> | <a href="http://www.camlis.net">Camlis</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/app.min.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/scripts.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/custom.js'); ?>"></script>
  <script>
    function checkspace(event)
    {
      if(event.which ==32)
      {
          event.preventDefault();
          return false;
      }
    }
  </script>
  </body>
</html>
