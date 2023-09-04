<script>
	var msg_save_fail = '<?php echo _t('global.msg.save_fail'); ?>';
	var msg_update_fail = '<?php echo _t('global.msg.update_fail'); ?>';
	var msg_delete_fail = '<?php echo _t('global.msg.delete_fail'); ?>';
	var msg_required_data = '<?php echo _t('global.msg.fill_required_data'); ?>';
	var q_delete_department = '<?php echo _t('admin.msg.q_delete_department'); ?>';
</script>
<div class="wrapper col-sm-9">
	<h4 class="sub-header"><?php echo _t('global.department'); ?>&nbsp;&nbsp;<span class="hint--right hint--info" data-hint='<?php echo _t('admin.add_new_department'); ?>' id="addNew"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4>
	<table class="table table-bordered table-striped" id="tbl-department">
		<thead>
			<th style="width:40px;"><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('global.department'); ?></th>
			<th style="width: 50px;"></th>
		</thead>
		<tbody></tbody>
	</table>
</div>