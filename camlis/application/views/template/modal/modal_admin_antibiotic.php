<!-- Modal Antibiotic -->
<div class="modal fade modal-primary" id="modal-antibiotic">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title new"><?php echo _t('admin.add_new_antibiotic'); ?></h4>
				<h4 class="modal-title edit" style="display: none;"><?php echo _t('admin.edit_antibiotic'); ?></h4>
			</div>
			<div class="modal-body">
				<div>
					<div class="form-vertical">
						<div class="row form-group">
							<div class="col-sm-12">
								<label for="antibiotic_name" class="control-label"><?php echo _t('admin.antibiotic_name'); ?> <sup class="fa fa-asterisk" style="font-size: 8px;"></sup></label>
								<input type="text" class="form-control" name="antibiotic_name" id="antibiotic-name">
							</div>
						</div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label"><?php echo _t('admin.order'); ?></label>
                                    <input type="text" class="form-control" id="antibiotic-order">
                                </div>
                            </div>
                        </div>
                        <!--div class="row form-group">
                            <div class="col-sm-12">
                                <label for="antibiotic_name" class="control-label">Gram Type<sup class="fa fa-asterisk" style="font-size: 8px;"></sup></label>
                                <select class="form-control" name="gram_type_id" id="gram_type_id">
                                < ?php
                                    foreach($gram_type as $row){
                                        echo "<option value='".$row->id."'>".$row->gram_type."</option>";
                                    }
                                ? >
                                </select>
                            </div>
                        </div-->
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnSaveAntibiotic"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<span class="save"><?php echo _t('global.save'); ?></span><span class="update" style="display: none;"><?php echo _t('global.update'); ?></span></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>