<!-- Modal -->
<div class="modal fade modal-primary" id="modal-sample">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title save"><?php echo _t('admin.new_sample_type'); ?></h4>
				<h4 class="modal-title update" style="display: none"><?php echo _t('admin.edit_sample_type'); ?></h4>
			</div>
			<div class="modal-body">
				<div>
					<div class="form-vertical">
						<div class="row form-group">
							<div class="col-sm-12">
								<label for="sample_name" class="control-label hint--right hint--error hint--always"><?php echo _t('admin.sample_type_name'); ?> <sup class="fa fa-asterisk" style="font-size: 8px;"></sup></label>
								<input type="text" class="form-control" name="sample_name" id="sample_name">
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-12">
								<label for="department" class="control-label hint--right hint--error hint--always"><?php echo _t('admin.choose_department'); ?> <sup class="fa fa-asterisk" style="font-size: 8px;"></sup></label>
								<select name="department" id="department" class="form-control" multiple="multiple">
									<?php
									foreach($departments as $dep) {
										echo "<option value='".$dep->department_id."'>".$dep->department_name."</option>";
									}
									?>
								</select>
							</div>
						</div>
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <label for="sample_description" class="control-label"><?php echo _t('admin.sample_description'); ?></label>
                                <input type="text" class="form-control" name="sample_description" id="sample-description">
                            </div>
                        </div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnSave"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<span class="save"><?php echo _t('global.save'); ?></span><span class="update" style="display: none;"><?php echo _t('global.update'); ?></span></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>