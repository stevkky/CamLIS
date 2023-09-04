<!-- Modal -->
<div class="modal fade modal-primary" id="modal-patient-type">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title new"><?php echo _t('admin.new_patient_type'); ?></h4>
				<h4 class="modal-title edit" style="display: none;"><?php echo _t('admin.edit_patient_type'); ?></h4>
			</div>
			<div class="modal-body">
				<div>
					<div class="form-vertical">
						<div class="row form-group">
							<div class="col-sm-12">
								<label for="patient_type_name" class="control-label"><?php echo _t('admin.patient_type'); ?> <sup class="fa fa-asterisk" style="font-size: 8px;"></sup></label>
								<input type="text" class="form-control" name="patient_type_name" id="patient-type-name">
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-12">
								<label for="patient_type_name" class="control-label"><?php echo _t('global.gender'); ?> <sup class="fa fa-asterisk" style="font-size: 8px;"></sup></label>
								<div>
									<label class="control-label" style="cursor: pointer;"><input type="radio" name="gender" value="1"> <?php echo _t('global.male'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
									<label class="control-label" style="cursor: pointer;"><input type="radio" name="gender" value="2"> <?php echo _t('global.female'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
									<label class="control-label" style="cursor: pointer;"><input type="radio" name="gender" value="3" checked> <?php echo _t('global.male').' & '._t('global.female'); ?></label>
								</div>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-4">
								<label for="min_age" class="control-label"><?php echo _t('admin.min_age'); ?></label>
								<div class="input-group">
									<input type="text" class="form-control" maxlength="3" onkeypress="return isNumber(event);" placeholder="<?php echo _t('global.age'); ?>" name="min_age" id="min-age">
									<span class="input-group-addon">
										<select name="min_age_unit" class="form-control" id="min-age-unit">
											<option value="1"><?php echo _t('global.day'); ?></option>
											<option value="7"><?php echo _t('global.week'); ?></option>
											<option value="30"><?php echo _t('global.month'); ?></option>
											<option value="365"><?php echo _t('global.year'); ?></option>
										</select>
									</span>
								</div>
							</div>
							<div class="col-sm-4">
								<label class="control-label">&nbsp;</label>
								<div class="input-group">
									<select id="is_equal_min_age" class="form-control" disabled>
										<option value="0"> > </option>
										<option value="1" selected> >= </option>
									</select>
									<span class="input-group-addon">&nbsp;&nbsp;<?php echo _t('global.age'); ?>&nbsp;&nbsp;</span>
									<select id="is-equal-max-age" class="form-control">
										<option value="0" selected> < </option>
										<option value="1"> <= </option>
									</select>
								</div>
							</div>
							<!--</div>
							<div class="row">-->
							<div class="col-sm-4">
								<label for="max-age" class="control-label"><?php echo _t('admin.max_age'); ?></label>
								<div class="input-group">
									<input type="text" class="form-control" maxlength="3" onkeypress="return isNumber(event);" placeholder="<?php echo _t('global.age'); ?>" name="max_age" id="max-age">
									<span class="input-group-addon">
										<select name="max_age_unit" class="form-control" id="max-age-unit">
											<option value="1"><?php echo _t('global.day'); ?></option>
											<option value="7"><?php echo _t('global.week'); ?></option>
											<option value="30"><?php echo _t('global.month'); ?></option>
											<option value="365"><?php echo _t('global.year'); ?></option>
										</select>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnSave"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<span class="save"><?php echo _t('global.save'); ?></span><span class="edit" style="display: none;"><?php echo _t('global.update'); ?></span></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>