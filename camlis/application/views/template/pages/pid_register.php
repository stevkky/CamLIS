<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">    

  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Register - PID</title>
    
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/app.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/style.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/components.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/custom.css'); ?>">
  <link rel='shortcut icon' type='image/x-icon' href='<?php echo site_url('assets/godata/dashboard/img/gd_ico.ico'); ?>' />

	<title>Registration</title>
  <style>
.result{
  font-size: 18px;
  color: #34395e;
}
.center-hv{
  text-align: center;
  vertical-align: middle !important;
}
.size64{
  width: 64px;
}
</style>
  </head>
  <body> 
  <?php $app_lang	= empty($app_lang) ? 'en' : $app_lang;?>
  <div class="loader"></div>
  <div id="app">
    <section class="section" id="register_section">
      <div class="container mt-5">
        <div class="row">
        
        <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
            <div class="card card-primary">
              <div class="card-header">
                <h4>Register</h4>
              </div>
              <div class="card-body">
                <form method="POST" action="<?php echo base_url();?>generatedev/doRegister" id="theForm" class="needs-validation" novalidate="">
                  <div class="row">
                    <div class="form-group col-4">
                      <label for="frist_name">Full name *</label>
                      <input id="full_name" type="text" class="form-control" name="full_name" autofocus>
                      <div class="text-warning" id="full_name-message"></div>
                    </div>
                    <div class="form-group col-4">
                      <label for="username">Username *</label>
                      <input id="username" type="text" class="form-control" name="username" onkeypress="checkspace(event)">
                      <div class="text-warning" id="username-message"></div>
                    </div>
                    <div class="form-group col-4">
                      <label for="location">Location *</label>
                      <input id="location" type="text" class="form-control" name="location">
                      <div class="text-warning" id="location-message"></div>
                    </div>
                  </div>
                  <div class="row">
                  <div class="form-group col-4">
                      <label for="province">Province / City *</label>
                      <select class="form-control form-control-sm" id="province" name="province">
                        <option value="">ជ្រើសរើសខេត្តក្រុង</option>
                      <?php foreach($provinces as $pro){
                          echo "<option value='".$pro->code."'>".$pro->name_kh."</option>";
                      }?>
                        </select>
                      <div class="text-warning" id="province-message"></div>
                    </div>
                    <div class="form-group col-4">
                      <label for="email">Phone number </label>
                      <input id="phone" type="number" class="form-control" name="phone">
                      <div class="text-warning" id="phone-message"></div>
                    </div>
                    <div class="form-group col-4">
                      <label for="email">Email </label>
                      <input id="email" type="email" class="form-control" name="email">
                      <div class="text-warning" id="email-message"></div>
                    </div>                    
                  </div>
                  
                  <div class="row">
                    <div class="form-group col-6">
                      <label for="password" class="d-block">Password *</label>
                      <input id="password" type="password" class="form-control pwstrength" name="password">
                      <div class="text-warning" id="password-message"></div>
                    </div>
                    <div class="form-group col-6">
                      <label for="password2" class="d-block">Password Confirmation *</label>
                      <input id="password2" type="password" class="form-control" name="password-confirm">
                      <div class="text-warning" id="password2-message"></div>
                    </div>
                  </div>                  
                  <div class="form-group">
                    <button type="button" class="btn btn-primary btn-lg btn-block" id="btnRegister">
                      Register
                    </button>
                  </div>
                </form>
              </div>
              <div class="mb-4 text-muted text-center">
                Already Registered? <a href="<?php echo base_url('generate/login')?>">Login</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section d-none" id="result_section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">            
            <div class="card card-primary">
              <div class="card-header">
                <h4>Thanks for registration</h4>
              </div>
              <div class="card-body">
                <div class="empty-state" data-height="300">
                  <div class="empty-state-icon">
                    <i class="fas fa-check-circle"></i>
                  </div>
                  <h2>Your account is pending</h2>
                  <p class="lead">
                    Please contact Mr. Vanra to approve the account. <br />
                  </p>
                  <a href="<?php echo base_url('generate/login')?>" class="btn btn-primary mt-4">Login</a>
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </section>

  </div>
  <script>
    var base_url = '<?php echo base_url().$app_lang;?>';
  </script>
  <script src="<?php echo site_url('assets/godata/dashboard/js/app.min.js'); ?>" type="text/javascript"></script>  
  <script src="<?php echo site_url('assets/godata/dashboard/bundles/jquery-pwstrength/jquery.pwstrength.min.js'); ?>"></script>
  <script src="<?php echo site_url('assets/godata/dashboard/bundles/jquery-selectric/jquery.selectric.min.js'); ?>"></script>  
  <script src="<?php echo site_url('assets/godata/js/gd-common.js'); ?>"></script>
  <script src="<?php echo site_url('assets/godata/js/pid-register.js'); ?>"></script>
  <script src="<?php echo site_url('assets/godata/dashboard/js/scripts.js'); ?>" type="text/javascript"></script>
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
