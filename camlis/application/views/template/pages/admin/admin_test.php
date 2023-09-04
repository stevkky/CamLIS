<script>
	var msg_require_test_name = '<?php echo _t('test.msg.test_name_require'); ?>';
	var msg_fill_required_data = '<?php echo _t('global.msg.fill_required_data'); ?>';
	var msg_save_fail = '<?php echo _t('global.msg.save_fail'); ?>';
	var msg_update_fail = '<?php echo _t('global.msg.update_fail'); ?>';
	var msg_q_delete_test = '<?php echo _t('admin.msg.q_delete_test'); ?>';
	var msg_delete_fail = '<?php echo _t('global.msg.delete_fail'); ?>';
	var msg_copy_fail = '<?php echo _t('admin.msg.copy_fail'); ?>';
	var msg_diff_copy = '<?php echo _t('admin.msg.diff_copy'); ?>';
	var msg_loading	= "<?php echo _t('global.plz_wait'); ?>";
	var label_choose = "<?php echo _t('global.choose'); ?>";
</script>

<div class="wrapper col-sm-9">
	<h4 class="sub-header"><?php echo _t('global.test'); ?>&nbsp;&nbsp;
		<span class="hint--right hint--info" data-hint='<?php echo _t('admin.new_test') ?>' id="addNew"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span>&nbsp;&nbsp;|&nbsp;
		<span class="hint--right hint--info" data-hint='<?php echo _t('admin.copy_org_anti') ?>' id="copy-data"><i class="fa fa-clipboard" style='color:dodgerblue; cursor:pointer;'></i></span>
	</h4>
	<div class="filter-box">
		<div class="row">
			<div class="col-sm-4">
				<label class="control-label"><?php echo _t('global.department'); ?></label>
				<select name="department" id="filter-department" class="form-control">
					<option value="-1"><?php echo _t('global.choose'); ?></option>
					<?php
					foreach ($departments as $dep) {
						echo "<option value='".$dep->department_id."'>".$dep->department_name."</option>";
					}
					?>
				</select>
			</div>
			<div class="col-sm-4">
				<label class="control-label"><?php echo _t('admin.sample_type'); ?></label>
				<select name="sample-type" id="filter-sample-type" class="form-control">
					<option value="-1"><?php echo _t('global.choose'); ?></option>
				</select>
			</div>
		</div>
	</div>
	<table class="table table-bordered table-striped" id="tbl-test">
		<thead>
			<th><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('admin.order'); ?></th>
			<th><?php echo _t('admin.department_name'); ?></th>
			<th><?php echo _t('admin.sample_type'); ?></th>
			<th><?php echo _t('admin.test_name'); ?></th>
			<th><?php echo _t('admin.unit_sign'); ?></th>
			<th><?php echo _t('admin.field_type'); ?></th>
			<th><?php echo _t('admin.is_heading'); ?></th>
			<th><?php echo _t('admin.header'); ?></th>
			<th class="action"></th>
		</thead>
		<tbody></tbody>
	</table>
</div>