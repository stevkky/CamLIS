<div class="col-sm-12">
    <div class='login-wrapper'>
        <img src="<?php echo site_url('assets/camlis/images/moh_logo.png') ?>" alt="MOH Logo" id="moh-logo">
        <form action="user/login" method="post">
            <div class="input-wrapper">
                <h3><i class="fa fa-lock"></i>&nbsp;<?php echo strtoupper(_t("login.login")); ?></h3>
                <?php if (isset($login_errors) && count($login_errors) > 0) { ?>
                    <div id="errBox">
                        <?php
                        foreach ($login_errors as $error) {
                            echo "<i class='ion-alert-circled'></i>&nbsp;".$error."<br/>";
                        }
                        ?>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <div class="input-group">
                        <label class="input-group-addon"><i class="fa fa-user"></i></label>
                        <input type="text" class="form-control" name="username" id="username" placeholder="ឈ្មោះអ្នកប្រើប្រាស់" required autofocus >
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <label class="input-group-addon"><i class="fa fa-key"></i></label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="លេខសម្ងាត់" maxlength="30" required >
                    </div>
                </div>
            </div>
            <div class="btn-wrapper">
                <button type="submit" class="btn btn-flat btn-primary" id="btnLogin"><?php echo _t('login.login'); ?>&nbsp;&nbsp;<i class="fa fa-sign-in"></i></button>
            </div>
        </form>
    </div>
</div>
