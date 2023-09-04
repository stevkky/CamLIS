<script>
	var msg_save_fail = '<?php echo _t('global.msg.save_fail'); ?>';
	var msg_update_fail = '<?php echo _t('global.msg.update_fail'); ?>';
	var msg_delete_fail = '<?php echo _t('global.msg.delete_fail'); ?>';
	var msg_required_data = '<?php echo _t('global.msg.fill_required_data'); ?>';
	var msg_valid_age_range = '<?php echo _t('admin.msg.valid_age_range'); ?>';
	var q_delete_patient_type = '<?php echo _t('admin.msg.q_delete_patient_type'); ?>';
</script>
<div class="wrapper col-sm-9">
	<h4 class="sub-header"><?php echo _t('admin.patient_type'); ?>&nbsp;&nbsp;<span class="hint--right hint--info" data-hint='<?php echo _t('admin.new_patient_type'); ?>' id="addNew"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4>
	<table class="table table-bordered table-striped" id="tbl-patient-type">
		<thead>
			<th style="width:30px;"><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('admin.patient_type'); ?></th>
			<th style="width: 70px;"><?php echo _t('admin.min_age'); ?></th>
			<th style="width:70px;"></th>
			<th style="width: 70px;"><?php echo _t('admin.max_age'); ?></th>
			<th style="width: 100px;"><?php echo _t('global.gender'); ?></th>
			<th style="width: 50px;"></th>
		</thead>
		<tbody></tbody>
	</table>
</div>