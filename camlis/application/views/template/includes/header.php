<!DOCTYPE html>
<html>
<head>
	<title>CamLIS</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Cambodia Laboratory System">
	<meta name="keywords" content="CamLIS">
	<link rel="icon" href="<?php echo site_url('assets/camlis/images/favicon.png'); ?>">
	
	<!--Style-->
	<link rel="stylesheet" href="<?php echo site_url('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/plugins/font-awesome/css/font-awesome.min.css') ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/plugins/icheck/skins/minimal/_all.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/ball-clip-rotate.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/hint.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/plugins/select2/css/select2.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/camlis_style.css') ?>">
	
	<!--Script-->
	<!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.3.min.js"></script>-->
	<script type="text/javascript" src="<?php echo site_url('assets/plugins/jQuery-2.1.4.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('assets/plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('assets/plugins/icheck/icheck.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('assets/plugins/jquery.printpage.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('assets/plugins/select2/js/select2.full.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('assets/camlis/js/camlis_script.js'); ?>"></script>
	
	<script type="text/javascript">
		var base_url	= '<?php echo $this->app_language->site_url().'/'; ?>';
		var app_lang	= '<?php echo $app_lang; ?>';
		var myDialog	= new CustomDialog();
	</script>
</head>
<body>
	<div id="wrapper">
		<header id="main-header">
			<div id="banner-wrapper"> 
				<div id="banner_part1" class="banner-sgement"></div>
				<div id="banner_part2" class="banner-sgement">
					<span class="banner-title"><?php echo _t('global.sys_title'); ?></span>
					<div id="language">
						<ul>
							<li>
								<b><a href="<?php echo $this->app_language->switchLanguage('kh'); ?>" title="Khmer"><img src="<?php echo site_url('assets/camlis/images/kh.png'); ?>" alt="Khmer"> ខ្មែរ</a></b>
							</li>
							&nbsp;&nbsp;|&nbsp;&nbsp;
							<li>
								<b><a href="<?php echo $this->app_language->switchLanguage('en'); ?>" title="English"><img src="<?php echo site_url('assets/camlis/images/en.png'); ?>" alt="English"> English</a></b>
							</li>
						</ul>
					</div>
				</div>
				<div id="banner_part3" class="banner-sgement"></div>
				<div id="banner_part4" class="banner-sgement">
					<?php
						$labname = 'name'.strtoupper($app_lang);
						echo $laboratoryInfo && isset($laboratoryInfo->labID) && $laboratoryInfo->labID > 0 ? $this->app_language->anchor('laboratory', $laboratoryInfo->$labname, 'class="banner-title"') : '';
					?>
					<div id="user_box">
						<?php echo _t('global.login_as'); ?>
						<b id="username"><?php echo $user->fullname; ?></b>
						&nbsp;( <a href="<?php echo base_url().'logout'; ?>"><?php echo _t('global.logout'); ?></a> )
					</div>
				</div>
			</div>
			<nav id="main-menu">
				<?php echo $menu; ?>
			</nav>
		</header>
		<section>