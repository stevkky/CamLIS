<!-- Modal -->
<div class="modal fade modal-primary" id="modal-merge-patient">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-chain"></i> <?php echo _t('patient.merge_patient'); ?></h4>
			</div>
			<div class="modal-body">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label class="control-label"><?php echo _t('patient.search_by_patient_code'); ?></label>
                            <input type="text" class="form-control patient-code source">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?php echo _t('patient.search_by_patient_name'); ?></label>
                            <input type="text" class="form-control patient-name source">
                        </div>
                        <br>
                        <label class="control-label"><?php echo _t('patient.patient_information'); ?></label>
                        <div class="border-box" id="patient-source-list">
                            <table class="table table-bordered patient-list"></table>
                        </div>
                    </div>
                    <div class="col-sm-2 text-center" style="padding-top: 280px;">
                        <button class="btn btn-default swap-patient" style="width: 100%;"><i class="fa fa-exchange"></i></button>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label class="control-label"><?php echo _t('patient.search_by_patient_code'); ?></label>
                            <input type="text" class="form-control patient-code destination">
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?php echo _t('patient.search_by_patient_name'); ?></label>
                            <input type="text" class="form-control patient-name destination">
                        </div>
                        <br>
                        <label class="control-label"><?php echo _t('patient.patient_information'); ?></label>
                        <div class="border-box" id="patient-destination-list">
                            <table class="table table-bordered patient-list"></table>
                        </div>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btn-merge-patient"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<span class="save"><?php echo _t('patient.merge'); ?></span></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>