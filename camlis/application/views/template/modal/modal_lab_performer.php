<!-- Modal -->
<div class="modal fade modal-primary" id="modal_performer">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-user"></i>&nbsp;<?php echo _t('manage.performer'); ?></h4>
			</div>
			<div class="modal-body">
				<div>
					<div class="form-vertical">
						<div class="row form-group">
							<div class="col-sm-12">
								<label for="performer_name" class="control-label hint--right hint--error hint--always"><?php echo _t('manage.performer_name'); ?> <sup class="fa fa-asterisk" style="font-size: 8px;"></sup></label>
								<input type="text" class="form-control" name="performer_name" id="performer_name">
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-12">
								<label class="control-label"><?php echo _t('manage.sex'); ?></label>
								<div class="radio" style="margin-top: 0">
									<label class="control-label" style="padding-left:0;"><input type="radio" name="gender" value="1" checked> &nbsp;<?php echo _t('global.male'); ?></label>&nbsp;&nbsp;
									<label class="control-label"><input type="radio" name="gender" value="2"> &nbsp;<?php echo _t('global.female'); ?></label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnSave"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<?php echo _t('global.save'); ?></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>