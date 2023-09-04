<!-- Modal -->
<div class="modal fade modal-primary" id="modal-sample-comment">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title new"><?php echo _t('admin.new_comment'); ?></h4>
				<h4 class="modal-title edit" style="display: none;"><?php echo _t('admin.edit_comment'); ?></h4>
			</div>
			<div class="modal-body">
				<div>
					<div class="form-vertical">
						<div class="row form-group">
							<div class="col-sm-12">
								<label for="department" class="control-label"><?php echo _t('admin.choose_department'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
								<select name="department" id="department" class="form-control">
									<option value="-1"><?php echo _t('global.choose'); ?></option>
									<?php
									foreach ($departments as $dep) {
										echo "<option value='".$dep->department_id."'>".$dep->department_name."</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-12">
								<label class="control-label"><?php echo _t('admin.choose_sample_type'); ?></label>
								<select name="sample" id="sample-type" class="form-control" multiple>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-12">
								<label class="control-label"><i class="fa fa-comments"></i> <?php echo _t('global.comment'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
								<textarea class="form-control" name="sample_comment" id="sample-comment" rows="1" style="resize: none;"></textarea>
							</div>
						</div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label pointer">
                                    <input type="checkbox" value="1" id="is-reject-comment">&nbsp;&nbsp;<?php echo _t('admin.is_reject_comment'); ?>
                                </label>
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