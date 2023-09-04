<script>
	var msg_update_fail = '<?php echo _t('global.msg.update_fail'); ?>';
	var msg_loading	= "<?php echo _t('global.plz_wait'); ?>";
</script>

<div class="wrapper col-sm-9">
	<h4 class="sub-header"><?php echo _t('manage.ref_range'); ?></h4>
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
	<table class="table table-bordered table-striped" id="tbl-sample-test">
		<thead>
			<th><?php echo _t('global.no.'); ?></th>
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