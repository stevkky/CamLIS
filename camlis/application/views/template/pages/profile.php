<script>
    var msg_required_fullname  = '<?php echo _t('user.msg.require-fullname'); ?>';
    var msg_required_username  = '<?php echo _t('user.msg.require-username'); ?>';
    var msg_required_old_pass  = '<?php echo _t('user.msg.require-old-password'); ?>';
    var msg_required_new_pass  = '<?php echo _t('user.msg.require-new-password'); ?>';
    var msg_wrong_password     = '<?php echo _t('user.msg.wrong-password'); ?>';
    var msg_wrong_confirm_pass = '<?php echo _t('user.msg.wrong-new-confirm-password'); ?>';
    var msg_password_criteria  = '<?php echo _t('user.msg.password_criteria'); ?>';
    var msg_update_fail        = '<?php echo _t('global.msg.update_fail'); ?>';
</script>
<div class="col-sm-12">
    <div class="form-horizontal">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <div class="col-sm-2">
                        <i class="fa fa-user"></i>&nbsp;<label class="control-label"><?php echo _t('user.fullname'); ?> *</label>
                    </div>
                    <div class="col-sm-5 fullname hint hint--right hint--always hint--error" data-hint="">
                        <input type="text" class="form-control" name="fullname" id="fullname" placeholder="<?php echo _t('user.fullname'); ?>" value="<?php echo isset($user->fullname) ? $user->fullname : ''; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2">
                        <i class="fa fa-user"></i>&nbsp;<label class="control-label"><?php echo _t('user.username'); ?> *</label>
                    </div>
                    <div class="col-sm-5 username hint hint--right hint--always hint--error" data-hint="">
                        <input type="text" class="form-control" name="username" id="username" placeholder="<?php echo _t('user.username'); ?>" value="<?php echo isset($user->username) ? $user->username : ''; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2">
                        <i class="fa fa-envelope"></i>&nbsp;<label class="control-label"><?php echo _t('user.email'); ?></label>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="email" id="email" placeholder="<?php echo _t('user.email'); ?>" value="<?php echo isset($user->email) ? $user->email : ''; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2">
                        <i class="fa fa-phone-square"></i>&nbsp;<label class="control-label"><?php echo _t('user.phone'); ?></label>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="phone" id="phone" placeholder="<?php echo _t('user.phone'); ?>" value="<?php echo isset($user->phone) ? $user->phone : ''; ?>">
                    </div>
                </div>
            </div>
            <div class="col-sm-12" style="margin-top: 40px;">
                <div class="form-group">
                    <div class="col-sm-12">
                        <h4 class="content-header"><?php echo _t('user.change-password'); ?></h4>
                        <hr>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2">
                        <i class="fa fa-lock"></i>&nbsp;<label class="control-label"><?php echo _t('user.password'); ?></label>
                    </div>
                    <div class="col-sm-5 old-password hint hint--right hint--always hint--error" data-hint="">
                        <input type="password" class="form-control" name="old_password" id="old-password" placeholder="<?php echo _t('user.password'); ?>" maxlength="20">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2">
                        <i class="fa fa-lock"></i>&nbsp;<label class="control-label"><?php echo _t('user.new-password'); ?></label>
                    </div>
                    <div class="col-sm-5 new-password hint hint--right hint--always hint--error" data-hint="">
                        <input type="password" class="form-control" name="new_password" id="new-password" placeholder="<?php echo _t('user.new-password'); ?>" maxlength="20">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2">
                        <i class="fa fa-lock"></i>&nbsp;<label class="control-label"><?php echo _t('user.confirm-new-password'); ?></label>
                    </div>
                    <div class="col-sm-5 new-confirm-password hint hint--right hint--always hint--error" data-hint="">
                        <input type="password" class="form-control" name="confirm_password" id="confirm-password" placeholder="<?php echo _t('user.confirm-new-password'); ?>" maxlength="20">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-5 new-confirm-password col-sm-offset-2 text-right">
                        <button type="button" class="btn btn-primary" id="btnSaveProfile"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo _t('global.save'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>