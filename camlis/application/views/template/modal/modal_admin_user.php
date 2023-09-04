<!-- Modal -->
<div class="modal fade modal-primary" id="modal-laboratory">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-hospital-o"></i> &nbsp;<?php echo _t('global.laboratory'); ?></h4>
			</div>
			<div class="modal-body">
				<?php if ($laboratories) { foreach ($laboratories as $lab) { ?>
					<div class='row' style='margin-bottom:15px;'>
						<div class='col-sm-12'>
							<?php
							$name = 'name_'. strtolower($app_lang);
							echo "<label class='control-label' style='cursor:pointer;'>";
							echo "<input type='checkbox' name='lab' value='". $lab->labID ."'>&nbsp;&nbsp;";
							echo isset($lab->$name) ? $lab->$name : 'N/A';
							echo "</label>";
							?>
						</div>
					</div>
				<?php }} ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnAssign"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<?php echo _t('global.save'); ?></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade modal-primary" id="modal-groups">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-key"></i>&nbsp;<?php echo _t('user.role'); ?></h4>
			</div>
			<div class="modal-body">
				<?php
				if ($user_groups) { foreach ($user_groups as $row) {
					?>
					<div class='row' style='margin-bottom:5px;'>
						<div class='col-sm-12'>
							<?php
							echo "<label class='control-label' style='cursor:pointer;'>";
							echo "<input type='checkbox' name='group' value='". $row->id ."'>&nbsp;&nbsp;";
							echo $row->definition;
							echo "</label>";
							?>
						</div>
					</div>
				<?php }} ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnSetGroup"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<?php echo _t('global.save'); ?></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade modal-primary" id="modal-user-form">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <i class="fa fa-user"></i>&nbsp;
                    <span><?php echo _t('user.new-user'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="form-vertical">
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <label class="control-label"><?php echo _t('user.fullname'); ?> *</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                <input type="text" name="fullname" id="fullname" class="form-control" placeholder="<?php echo _t('user.fullname'); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label"><?php echo _t('user.username'); ?> *</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                <input type="text" name="username" id="username" class="form-control" placeholder="<?php echo _t('user.username'); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Added 24-04-2021 -->
                    <?php // if($_SESSION['roleid'] == 1){ ?>
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <label class="control-label"><?php echo _t('user.location'); ?></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                                <input type="text" name="location" id="location" class="form-control" placeholder="<?php echo _t('user.location'); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label"><?php echo _t('user.province'); ?>*</label>
                            <select name="province" id="province" class="form-control">
								<option value="-1" style="color:#d8d5d5;">
									<?php echo _t('global.choose_province'); ?>
								</option>
								<?php
									foreach($provinces as $pro) {
										$app_lang	= empty($app_lang) ? 'en' : $app_lang;
										$name		= 'name_'.$app_lang;
										echo "<option value='".$pro->code."'>".$pro->$name."</option>";
									}
								?>
							</select>
                        </div>
                    </div>
                    <?php // } ?>
                    <!-- -->
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <label class="control-label"><?php echo _t('user.password'); ?> *</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                <input type="password" name="password" id="password" class="form-control" placeholder="<?php echo _t('user.password'); ?>" maxlength="20">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label"><?php echo _t('user.confirm-password'); ?> *</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                <input type="password" name="confirm_password" id="confirm-password" class="form-control" placeholder="<?php echo _t('user.confirm-password'); ?>" maxlength="20">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <label class="control-label"><?php echo _t('user.email'); ?></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                <input type="text" name="email" id="email" class="form-control" placeholder="<?php echo _t('user.email'); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label"><?php echo _t('user.phone'); ?></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-phone-square"></i></span>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="<?php echo _t('user.phone'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info" style="margin-top: 35px; border-left: 4px solid #719ce7;">
                    <b><i class="fa fa-info-circle"></i>&nbsp;<?php echo _t('global.notice')." :&nbsp;&nbsp;"._t('user.msg.password_criteria'); ?></b>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnSaveUser"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<?php echo _t('global.save'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
            </div>
        </div>
    </div>
</div>