<script>
	var _sample_entry_by= '<?php echo _t('sample.sample_entry_by'); ?>';
	var _sample_modified_by= '<?php echo _t('sample.modified_by'); ?>';
	var _sample_entry_date= '<?php echo _t('sample.entry_date'); ?>';
</script>
<!--Test Result Modal-->
<div class="modal fade modal-wide modal-primary fixed-footer" id="result_modal">

	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header with-border">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-pencil"></i>&nbsp;<?php echo _t('sample.add_result'); ?></h4>
			</div>
			<div class="modal-body">
				<div class="form-vertical well" style="padding: 10px;">

                    <input type="hidden" id="patient_sample_id" />
                    <input type="hidden" id="hidden_sample_id" />
					<div class="row" style="margin-bottom: 0;">
						<div class="col-md-3">
							<label class="control-label">
								<?php echo _t('patient.patient_id'); ?>
							</label>
							<div class="text-blue patient-id" style="font-weight: bold;"></div>
						</div>
                        <div class="col-md-3">
                            <label class="control-label">
                                <?php echo _t('patient.patient_name'); ?>
                            </label>
                            <div class="text-blue patient-name" style="font-weight: bold;"></div>
                        </div>
						<div class="col-md-3">
							<label class="control-label">
								<?php echo _t('sample.sample_number'); ?>
							</label>
							<div class="text-blue sample-number" style="font-weight: bold;"></div>
						</div>
						<div class="col-md-3">
							<label class="control-label">
								<?php echo _t('sample.sample_source'); ?>
							</label>
							<div class="text-blue sample-source" style="font-weight: bold;"></div>
						</div>
					</div>
				</div>
				<div id="test_list_wrapper">
					<div class="row">
						<div class="col-sm-12">
							<table class="table table-striped" id="tb_test_result" style="border:2px solid #e5e5e5;">
								<thead>
									<tr style="background: #2A83D0; color: white; border: 2px solid #2A83D0; text-shadow: 0 0 1px black;">
										<!--<th><?php echo _t('global.no.'); ?> </th>-->
										<th><?php echo _t('global.test'); ?></th>
										<th><?php echo _t('sample.result'); ?></th>
										<th>&nbsp;</th>
										<th><?php echo _t('sample.unit_sign'); ?></th>
										<th><?php echo _t('sample.ref_range'); ?></th>
										<th width="120"><?php echo _t('sample.test_date'); ?></th>
										<th>&nbsp;<?php //echo _t('sample.test_time'); ?></th>
										<th width="150"><?php echo _t('sample.performed_by'); ?></th>
                                        <th></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
							<div id="performer-list" class="hide">
								<?php
								if ($performers && is_array($performers)) {
									foreach ($performers as $performer) {
										echo "<option value='".$performer['ID']."'>".$performer['performer_name']."</option>";
									}
								}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

    <div class="modal-footer">
        <div class="row" style="margin-bottom: 0;">
            <div class="col-sm-12">
                <button class="btn btn-primary assign-test pull-left"><i class="fa fa-list-alt"></i> <?php echo _t('sample.edit_test'); ?></button>
                <button type="button" class="btn btn-primary btnSaveResult">
                    <i class="fa fa-floppy-o"></i>&nbsp;<?php echo _t('global.save'); ?>
                </button>
                <button type="button" class="btn btn-success save-preview-result">
                    <i class="fa fa-floppy-o"></i>&nbsp;<?php echo _t('global.save'); ?> & <i class="fa fa-eye" aria-hidden="true"></i>&nbsp;<?php echo _t('sample.preview_result'); ?>
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _t('global.cancel'); ?>
                </button>
            </div>
        </div>
    </div>
</div>