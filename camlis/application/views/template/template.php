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
	<link rel="stylesheet" href="<?php echo site_url('assets/plugins/select2/css/select2-bootstrap.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/plugins/select2/css/select2-bootstrap-flat.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/camlis_style.css') ?>">
	<?php echo $this->template->stylesheet; ?>

    <!--Script-->
    <script type="text/javascript" src="<?php echo site_url('assets/plugins/jQuery-2.1.4.min.js'); ?>"></script>
    <script type="text/javascript">
        const LABORATORY_SESSION = <?php echo json_encode(CamlisSession::getLabSession()) ?>;
        const APP_LANG	= '<?php echo $app_lang; ?>';
        const BASE_URL  = '<?php echo preg_replace('/\/?$/', '/', $this->app_language->site_url()); ?>';
        var base_url	= '<?php echo $this->app_language->site_url(); ?>';
            base_url    = base_url.replace(/\/?$/, '/');
        var app_lang	= '<?php echo $app_lang; ?>';
    </script>
</head>
<body>
<div id="template-wrapper">
	<div id="template-content">
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
					$labname = 'name_'.strtolower($app_lang);
					echo $laboratoryInfo && isset($laboratoryInfo->labID) && $laboratoryInfo->labID > 0 ? $this->app_language->anchor('laboratory', $laboratoryInfo->$labname, 'class="banner-title"') : '';
					?>

                    <?php if ($this->aauth->is_loggedin()) { ?>
					<div id="user_box">
						<?php echo _t('global.login_as'); ?>
                        <a href="<?php echo $this->app_language->site_url('user/profile'); ?>" style="color:white;" class="hint hint--bottom hint--info" data-hint="<?php echo _t('global.msg.update-account'); ?>"><b><?php echo $this->aauth->get_user()->fullname; ?></b></a>&nbsp;
						( <a href="<?php echo site_url('user/logout'); ?>"><b><?php echo _t('global.logout'); ?>&nbsp;<i class="fa fa-sign-out"></i></b></a> )
					</div>
                    <?php } ?>
				</div>
			</div>
			<!-- Top Navigation -->
			<?php echo $this->template->widget('TopNavigation', ['laboratoryInfo' => $laboratoryInfo, 'cur_main_page' => $cur_main_page]); ?>
		</header><!--/ End header -->
		<!-- Section -->
		<section class="content-body">
			<?php if (!empty((string)$this->template->content_title)) { ?>
			<h4 class="no-marginTop content-header"><?php echo $this->template->content_title; ?></h4>
			<hr>
			<?php } ?>
			<div class="row">
				<?php
					echo $this->template->content;
				?>
			</div>
		</section> <!--/ End Section -->
		<footer class="text-center">
			<img src="<?php echo site_url('assets/camlis/images/WHO_logo.png') ?>" alt="WHO Logo" id="WHO_logo">
			<span>Designed by World Health Organization (WHO) Cambodia. Copyright © 2013. All Right Reserved Contact: iengv@who.int</span>
		</footer><!--/ End Footer -->
	</div>
</div><!--/ End wrapper -->
<?php
	echo $this->template->modal;
?>

<!--Script-->
<script type="text/javascript" src="<?php echo site_url('assets/plugins/bootstrap/js/bootstrap.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/plugins/icheck/icheck.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/plugins/jquery.printpage.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/plugins/select2/js/select2.full.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/plugins/underscorejs/underscore-min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/plugins/sprintf-js/sprintf.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/camlis/js/camlis_constants.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/camlis/js/camlis_script.js'); ?>"></script>

<script type="text/javascript">
    var myDialog	= new CustomDialog();

    var globalMessage = {
        loading: '<?php echo _t('global.loading'); ?>',
        save_success: '<?php echo _t('global.msg.save_success'); ?>',
        save_fail: '<?php echo _t('global.msg.save_fail') ?>',
        update_success: '<?php echo _t('global.msg.update_success') ?>',
        update_fail: '<?php echo _t('global.msg.update_fail') ?>',
        delete_success: '<?php echo _t('global.msg.delete_success') ?>',
        delete_fail: '<?php echo _t('global.msg.delete_fail') ?>',
        error: '<?php echo _t('global.msg.error') ?>'
    };
</script>
<?php
    echo $this->template->plugins;
    echo $this->template->javascript;
?>
</body>
</html>
