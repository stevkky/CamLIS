<script>
	var msg_save_fail = '<?php echo _t('global.msg.save_fail'); ?>';
	var msg_update_fail = '<?php echo _t('global.msg.update_fail'); ?>';
	var msg_delete_fail = '<?php echo _t('global.msg.delete_fail'); ?>';
	var msg_required_data = '<?php echo _t('global.msg.fill_required_data'); ?>';
	var q_delete_sample_source = '<?php echo _t('manage.msg.q_delete_sample_source'); ?>';
</script>
<div class="wrapper col-sm-9">
	<h4 class="sub-header"><?php echo _t('global.sample_source'); ?>&nbsp;&nbsp;<span class="hint--right hint--info" data-hint='<?php echo _t('manage.new_sample_source'); ?>' id="addNew"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4>
	<table class="table table-bordered table-striped" id="tbl-sample-source">
		<thead>
			<th style="width:40px;"><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('manage.sample_source_name'); ?></th>
			<th style="width: 50px;"></th>
		</thead>
		<tbody></tbody>
	</table>
</div>