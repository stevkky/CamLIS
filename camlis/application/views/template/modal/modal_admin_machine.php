<div class="modal fade modal-primary" id="modal-machine">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Machine Test</h4>
			</div>
			<div class="modal-body">
				<div class="form-vertical">
					<div class="row form-group" style="margin-bottom: 25px;">
						<div class="col-sm-12">
							<label class="control-label">Machine name</label>
							<div id="select-test-wrapper" class="input-group">
								<select name="machine_name" class="form-control machine-name" id="machine_name" multiple>
									<?php foreach ($machines as $machine): ?>
										<option value="<?php echo $machine->id; ?>"><?php echo $machine->machine_name; ?></option>
									<?php endforeach ?>
								</select>
								<span class="input-group-addon">
									<div class="btn-group btn-group-xs">
										<button id="btn-new-machine-name" type="button" data-toggle="tooltip" data-placement="top" title="<?php echo _t('global.add_new'); ?>" class="btn btn-success"><i class="fa fa-plus"></i></button>
										<button id="btn-edit-machine-name" type="button" data-toggle="tooltip" data-placement="top" title="<?php echo _t('global.edit'); ?>" class="btn btn-primary" disabled><i class="fa fa-pencil"></i></button>
									</div>
								</span>
							</div>
							<div id="test-entry-wrapper" class="input-group" style="display: none;">
								<input type="text" class="form-control" name="machine" id="machine">
								<span class="input-group-addon">
									<div class="btn-group btn-group-xs">
										<button id="btn-save-machine-name" type="button" data-toggle="tooltip" data-placement="top" title="<?php echo _t('global.save'); ?>" class="btn btn-primary"><i class="fa fa-floppy-o"></i></button>
										<button id="btn-delete-test-name" type="button" data-toggle="tooltip" data-placement="top" title="<?php echo _t('global.remove'); ?>" class="btn btn-danger"><i class="fa fa-trash"></i></button>
										<button id="btn-cancel-machine-name" type="button" data-toggle="tooltip" data-placement="top" title="<?php echo _t('global.cancel'); ?>" class="btn btn-default"><i class="fa fa-remove"></i></button>
									</div>
								</span>
							</div>
						</div>
					</div>
					<div class="row form-group" style="margin-bottom: 25px;">
						<div class="col-sm-12">
							<label class="control-label">
								<?php echo _t('global.test'); ?>&nbsp;<sup class="fa fa-asterisk" style="font-size:8px;">
							</label>
							<select name="sample_test" class="form-control sample-test" id="sample_test" multiple></select>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnSave">
					<i class="fa fa-save"></i> &nbsp;<?php echo _t('global.save'); ?>
				</button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>