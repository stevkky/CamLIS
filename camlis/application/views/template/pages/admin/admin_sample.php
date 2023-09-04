<script>
	var msg_must_choose_dep	= '<?php echo _t('global.msg.must_choose_dep');	?>';
	var msg_must_fill_sname	= '<?php echo _t('global.msg.must_fill_sname');	?>';
	var msg_save_fail		= '<?php echo _t('global.msg.save_fail');	?>';
	var msg_update_fail		= '<?php echo _t('global.msg.update_fail');	?>';
	var msg_q_delete_sample_type		= '<?php echo _t('admin.msg.q_delete_sample_type');	?>';
</script>

<div class="wrapper col-sm-9">
	<h4 class="sub-header"><?php echo _t('admin.sample_type'); ?> &nbsp;&nbsp;<span class="hint--right hint--info" data-hint='<?php echo _t('admin.new_sample_type'); ?>' id="addNew"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4>
	<table class="table table-bordered table-striped" id="tbl-sample">
		<thead>
			<th style="width:40px;"><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('admin.sample_type_name'); ?></th>
			<th><?php echo _t('global.department'); ?></th>
            <th><?php echo _t('admin.sample_description'); ?></th>
            <th style="width:60px;"></th>
		</thead>
		<tbody></tbody>
	</table>
</div>