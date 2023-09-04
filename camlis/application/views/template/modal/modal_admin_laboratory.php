<!-- Modal -->
<div class="modal fade modal-primary" id="modal-laboratory">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title new"><?php echo _t('admin.new_laboratory'); ?></h4>
				<h4 class="modal-title edit" style="display: none;"><?php echo _t('admin.edit_laboratory'); ?></h4>
			</div>
			<div class="modal-body">
				<div>
					<div class="form-vertical">
						<div class="row form-group">
							<div class="col-sm-12">
								<label for="lab-name-en" class="control-label"><?php echo _t('admin.lab_name_en'); ?>​&nbsp;<sup class="fa fa-asterisk" style="font-size: 8px;"></sup></label>
								<input type="text" class="form-control" name="lab_name_en" id="lab-name-en">
							</div>
						</div>
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <label for="lab-name-kh" class="control-label"><?php echo _t('admin.lab_name_kh'); ?>​&nbsp;<sup class="fa fa-asterisk" style="font-size: 8px;"></sup></label>
                                <input type="text" class="form-control" name="lab_name_kh" id="lab-name-kh">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <label for="lab-code" class="control-label"><?php echo _t('admin.lab_short_name'); ?>​&nbsp;<sup class="fa fa-asterisk" style="font-size: 8px;"></sup></label>
                                <input type="text" class="form-control" name="lab_code" id="lab-code" maxlength="3">
                            </div>
                        </div>
                        <div class="row form-group">
							<div class="col-sm-12">
								<label for="address-en" class="control-label"><?php echo _t('admin.address_en'); ?></label>
								<input type="text" class="form-control" name="address_en" id="address-en">
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-12">
								<label for="address-kh" class="control-label"><?php echo _t('admin.address_kh'); ?></label>
								<input type="text" class="form-control" name="address_kh" id="address-kh">
							</div>
						</div>
                        <!-- add -->
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <label for="sample-number-type" class="control-label"><?php echo _t('admin.sample_number'); ?></label>
                                <select class="form-control" name="sample_number_type" id="sample-number-type">
                                    <option value="1">Auto</option>
                                    <option value="2">Manual</option>
                                </select>
                            </div>
                        </div>

					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnSave"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<span class="save"><?php echo _t('global.save'); ?></span><span class="update" style="display: none"><?php echo _t('global.update'); ?></span></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>