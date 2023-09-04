<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">    
	
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
	<link rel="stylesheet" href="<?php echo base_url('assets/godata/css/gd-style.css')?>">
	<title>Godata Login</title>
  </head>
  <body> 
  <?php $app_lang	= empty($app_lang) ? 'en' : $app_lang;?>
	<div class="container pd-1">
        <div class="row">
            <div class="col-sm-12">
				<h4 class="mb-1 text-center">GoData Login</h4>
				<hr class="mb-2">
        <form action="<?php echo base_url().$app_lang;?>/godata/doLogin" method="post" id="theForm">
				<div class="row">
					<div class="col-12">
						<input type="text" value="" class="form-control" placeholder="Email" id="email" name="email" autofocus />
						<div id="email-message" class="text-warning text-left">&nbsp;</div>
					</div>
					<div class="col-12">
						<input type="password" value="" class="form-control" placeholder="password" id="password" name="password" />
						<div id="password-message" class="text-warning text-left">&nbsp;</div>
					</div>
				</div>	
				<hr class="mb-2" />
                <div class="row font-size-text">
                    <div class="col-12 mb-1">                        
                        <button type="button" class="btn btn-flat btn-primary" id="btnSubmit"><?php echo _t('login.login'); ?>&nbsp;&nbsp;<i class="fa fa-sign-in"></i></button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

	<script src="<?php echo base_url('assets/godata/js/gd-common.js') ?>"></script>
	<script src="<?php echo base_url('assets/godata/js/gd-login.js') ?>"></script>	
  </body>
</html>
