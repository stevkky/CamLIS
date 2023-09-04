<script>
    var msg_required_data       = '<?php echo _t('global.msg.fill_required_data'); ?>';
	var msg_save_fail           = '<?php echo _t('global.msg.save_fail'); ?>';
	var q_delete_user_role      = '<?php echo _t('user.q.delete_user_role'); ?>';
</script>

<div class="wrapper col-sm-9">
	<h4 class="sub-header">
        <i class="fa fa-key"></i>&nbsp;
        <span><?php echo _t('global.user_role'); ?></span>&nbsp;
        <span class="hint--right hint--info" data-hint='<?php echo _t('admin.new_user_role'); ?>' data-toggle="modal" data-target="#modal-user-role"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span>
    </h4>
	<table class="table table-bordered table-striped" id="tbl-user-role">
		<thead>
			<th><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('admin.name'); ?></th>
            <th><?php echo _t('admin.description'); ?></th>
            <th><?php echo _t('admin.default_page'); ?></th>
            <th></th>
		</thead>
		<tbody></tbody>
	</table>
</div>