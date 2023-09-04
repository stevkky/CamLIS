<script>
	var msg_save_fail = '<?php echo _t('global.msg.save_fail'); ?>';
	var msg_update_fail = '<?php echo _t('global.msg.update_fail'); ?>';
	var msg_delete_fail = '<?php echo _t('global.msg.delete_fail'); ?>';
	var msg_required_data = '<?php echo _t('global.msg.fill_required_data'); ?>';
	var q_delete_requester = '<?php echo _t('manage.msg.q_delete_requester'); ?>';
</script>
<div class="wrapper col-sm-9">
	<!-- Performer -->
	<h4 class="sub-header"><i class="fa fa-user"></i>&nbsp;&nbsp;<?php echo _t('manage.requester'); ?> &nbsp;&nbsp;<span class="hint--right hint--info" data-hint='<?php echo _t('manage.new_requester'); ?>' id="addNew"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4>
	<table class="table table-bordered table-striped" id="tbl-requester">
		<thead>
			<th style="width:40px"><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('manage.requester_name'); ?></th>
			<th><?php echo _t('manage.sex'); ?></th>
			<th><?php echo _t('global.sample_source'); ?></th>
			<th style="width:50px;"></th>
		</thead>
		<tbody></tbody>
	</table>
</div>