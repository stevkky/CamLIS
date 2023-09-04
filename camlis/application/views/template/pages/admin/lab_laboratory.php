<script>
	var msg_update_fail = '<?php echo _t('global.msg.update_fail'); ?>';
	var msg_required_data = '<?php echo _t('global.msg.fill_required_data'); ?>';
</script>
<div class="wrapper col-sm-9">
	<h4 class="sub-header"><i class="fa fa-building-o"></i>&nbsp;&nbsp;<?php echo _t('global.lab_info'); ?></h4>
	<div class="well well-md">
		<form id="frm-laboratory">
		<div class="row">
			<div class="form-group">
				<div class="col-md-3 col-md-push-9 text-center">
					<?php
						$path = "assets/camlis/images/laboratory/";
						$icon = "";
						if ($laboratoryInfo->photo && file_exists("./".$path.$laboratoryInfo->photo)) {
							$icon = site_url($path.$laboratoryInfo->photo);
						} else {
							$icon = site_url($path."no-icon.png");
						}
					?>
					<img default-src="<?php echo site_url($path."no-icon.png"); ?>" src="<?php echo $icon; ?>" alt="Photo" class="img-responsive img-thumbnail" id="lab-icon-view">
					<div class="custom-filestyle">
						<input type="file" name="lab_icon" id="lab-icon">
						<button type="button" class="btn btn-primary"><i class="fa fa-folder-open"></i> <?php echo _t('manage.choose_photo'); ?></button>
					</div>
					<button type="button" class="btn btn-danger" id="btn-remove-icon" style="width: 100%"><i class="fa fa-trash"></i> <?php echo _t('manage.remove_photo'); ?></button>
				</div>
			</div>
			<div class="col-md-9 col-md-pull-3">
				<div class="form-vertical">
					<div class="row form-group">
						<div class="col-sm-12">
							<label for="lab_nameEN" class="control-label hint--right hint--error hint--always"><?php echo _t('manage.lab_name_en'); ?> *</label>
							<div>
								<input type="text" class="form-control" name="name_en" id="lab-name-en" value="<?php echo $laboratoryInfo->name_en; ?>">
							</div>
						</div>
					</div>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <label for="lab_nameKH" class="control-label hint--right hint--error hint--always"><?php echo _t('manage.lab_name_kh'); ?> *</label>
                            <div>
                                <input type="text" class="form-control" name="name_kh" id="lab-name-kh" value="<?php echo $laboratoryInfo->name_kh; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <label for="lab-code" class="control-label hint--right hint--error hint--always"><?php echo _t('manage.lab_short_name'); ?> *</label>
                            <div>
                                <input type="text" class="form-control" value="<?php echo $laboratoryInfo->lab_code; ?>" disabled>
                            </div>
                        </div>
                    </div>
					<div class="row form-group">
						<div class="col-sm-12">
							<label for="address-en" class="control-label"><?php echo _t('manage.address_en'); ?></label>
							<input type="text" class="form-control" name="address_en" id="address-en" value="<?php echo $laboratoryInfo->address_en; ?>" >
						</div>
					</div>
					<div class="row form-group">
						<div class="col-sm-12">
							<label for="address-kh" class="control-label"><?php echo _t('manage.address_kh'); ?></label>
							<input type="text" class="form-control" name="address_kh" id="address-kh" value="<?php echo $laboratoryInfo->address_kh; ?>" >
						</div>
					</div>
					<input type="hidden" name="laboratory_id" id="laboratory-id" value="<?php echo $laboratoryInfo->labID; ?>">
					<input type="hidden" name="lab_code" id="lab-code" value="<?php echo $laboratoryInfo->lab_code; ?>">
					<input type="hidden" name="sample_number" id="sample-number-type" value="<?php echo $laboratoryInfo->sample_number; ?>">
				</div>
			</div>
		</div>
		</form>
		<div class="row adm_lab_btnWrapper">
			<div class="col-sm-12">
				<button type="button" class="btn btn-primary pull-right" id="btnEditLaboratory"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo _t('global.update'); ?></button>
			</div>
		</div>
	</div>
</div>