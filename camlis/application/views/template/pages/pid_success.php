<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">    

  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>PID COUNTER</title>
    
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/app.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/bundles/bootstrap-daterangepicker/daterangepicker.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/style.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/components.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/custom.css'); ?>">
  <link rel='shortcut icon' type='image/x-icon' href='<?php echo site_url('assets/godata/dashboard/img/gd_ico.ico'); ?>' />

	<title>PID COUNTER</title>
  <style>
  .pidzone {
    border: 2px dashed #6777ef;
    min-height: 60px;
    text-align: center;
}
.pid-result {
    font-size: 23px;
    color: #34395e;
    margin: 1.2em 0.6em;    
}
.float-left{
  float: left;
}
</style>
  </head>
  <body> 
  <?php $app_lang	= empty($app_lang) ? 'en' : $app_lang;?>
  <div class="loader"></div>
  <div id="app">    
  <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">
            <div class="login-brand">
              Otika
            </div>
            <div class="card card-primary">
              <div class="card-header">
                <h4>Subscribe Our Newsletters</h4>
              </div>
              <div class="card-body">
                <form method="POST">
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <i class="fas fa-envelope"></i>
                        </div>
                      </div>
                      <input id="email" type="email" class="form-control" name="email" autofocus placeholder="Email">
                    </div>
                  </div>
                  <div class="form-group text-center">
                    <button type="submit" class="btn btn-lg btn-round btn-primary">
                      Subscribe
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <div class="simple-footer">
              Copyright &copy; Otika 2018
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/app.min.js'); ?>"></script>
  <script src="<?php echo site_url('assets/godata/dashboard/bundles/bootstrap-daterangepicker/daterangepicker.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/scripts.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/custom.js'); ?>"></script>	
  
  </body>
</html>
