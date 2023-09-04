<script>
    var confirm_delete_patient = "<?php echo _t('patient.confirm_delete_patient'); ?>";
    var msg_delete_fail        = "<?php echo _t('global.msg.delete_fail'); ?>";
    var confirm_merge_patient  = "<?php echo _t('patient.confirm_merge_patient'); ?>";
</script>
<div class="col-sm-12">
    <h4 class="no-marginTop content-header"><?php echo _t('patient.patient_list'); ?></h4>
    <?php if ($this->aauth->is_allowed('merge_patient')) { ?>
    <button class="btn btn-success btn-sm pull-right" id="merge-patient"><i class="fa fa-chain"></i> <?php echo _t('patient.merge_patient'); ?></button>
    <?php } ?>
    <hr style="margin-top: 0;">
	<!-- Filter -->
	<div class="form-vertical" id="patient-search-wrapper">
		<div class="col-sm-3">
			<label class="control-label"><?php echo _t('patient.search_patient'); ?></label>
			<input type="text" name="search_patient_info" id="search-patient-info" class="form-control" placeholder="<?php echo _t('patient.patient_information'); ?>" onfocus="this.select()">
		</div>
		<div class="col-sm-3" id="btn-wrapper">
			<button type="button" id="btnSearch" class="btn btn-primary"><?php echo _t('global.search'); ?>&nbsp;&nbsp;<i class="fa fa-search"></i></button>
		</div>
	</div>

	<!-- List of Patient -->
	<input type="hidden" name="hf_code" id="hf_code" value="<?php echo $laboratoryInfo->hf_code; ?>">
	<table class="table table-bordered table-striped dataTable nowrap" id="tb_Patient">
		<thead>
			<th class="text-center" style="width:30px;"><?php echo _t('global.no.'); ?></th>
            <th><?php echo _t('patient.patient_id'); ?></th>
            <th style="width:200px;"><?php echo _t('patient.patient_id'); ?></th>
			<th><?php echo _t('patient.patient_name'); ?></th>
			<th class="text-center" style="width:50px;"><?php echo _t('patient.sex'); ?></th>
			
			<th><?php echo _t('patient.phone'); ?></th>
			<th><?php echo _t('patient.passport_no'); ?></th>
	
			<th style="width:70px;">
				<i class="fa fa-check-circle text-green"></i> : <?php echo _t('patient.have_sample'); ?>
			</th>
			<th style="width:50px;"></th>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>