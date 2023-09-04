<!-- Modal​Organism -->
<div class="modal fade modal-primary" id="modal-organism">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title new"><?php echo _t('admin.add_new_organism'); ?></h4>
				<h4 class="modal-title edit" style="display: none;"><?php echo _t('admin.edit_organism'); ?></h4>
			</div>
			<div class="modal-body">
				<div>
					<div class="form-vertical">
						<div class="row form-group">
							<div class="col-sm-12">
								<label for="organism_name" class="control-label"><?php echo _t('admin.organism_name'); ?> <sup class="fa fa-asterisk" style="font-size: 8px;"></sup></label>
								<input type="text" class="form-control" name="organism_name" id="organism-name">
							</div>
						</div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label"><?php echo _t('admin.order'); ?></label>
                                    <input type="text" class="form-control" id="organism-order">
                                </div>
                            </div>
                        </div>
						<div class="row">
							<div class="col-sm-12">
								<label class="control-label"><?php echo _t('global.value'); ?></label>
							</div>
							<div class="form-group">
								<div class="col-sm-3">
									<label class="control-label pointer"><input class="organism-value" type="checkbox" name="value_positive" value="1"> &nbsp;<?php echo _t('global.positive'); ?></label>
								</div>
								<div class="col-sm-3">
									<label class="control-label pointer"><input class="organism-value" type="checkbox" name="value_negative" value="2"> &nbsp;<?php echo _t('global.negative'); ?></label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnSaveOrganism"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<span class="save"><?php echo _t('global.save'); ?></span><span class="update" style="display: none;"><?php echo _t('global.update'); ?></span></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>