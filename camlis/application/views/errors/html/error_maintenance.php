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
    <link rel="stylesheet" href="<?php echo site_url('assets/plugins/select2/css/select2.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo site_url('assets/plugins/select2/css/select2-bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/camlis_style.css') ?>">
</head>
<body>
<div id="template-wrapper">
    <div id="template-content">
        <header id="main-header">
            <div id="banner-wrapper">
                <div id="banner_part1" class="banner-sgement"></div>
                <div id="banner_part2" class="banner-sgement">
                    <span class="banner-title"><?php echo _t('global.sys_title'); ?></span>
                </div>
                <div id="banner_part3" class="banner-sgement"></div>
                <div id="banner_part4" class="banner-sgement"></div>
            </div>
            <nav id="main-menu"></nav>
        </header><!--/ End header -->
        <!-- Section -->
        <section class="content-body">
            <div class="col-sm-12 text-center">
                <div style="margin-top: -25px; margin-bottom: 30px;"><img src="<?php echo site_url('assets/camlis/images/maintenance-icon.png') ?>" style="width: 200px;" alt="Maintenance mode"></div>
                <p style="font-size: 18pt;">
                    The system currently undergoing maintenance.<br> Please come back again later...
                </p>
            </div>
        </section> <!--/ End Section -->
        <footer class="text-center">
            <img src="<?php echo site_url('assets/camlis/images/WHO_logo.png') ?>" alt="WHO Logo" id="WHO_logo">
            <span>Designed by World Health Organization (WHO) Cambodia. Copyright © 2013. All Right Reserved Contact: iengv@who.int</span>
        </footer><!--/ End Footer -->
    </div>
</div><!--/ End wrapper -->
</body>
</html>