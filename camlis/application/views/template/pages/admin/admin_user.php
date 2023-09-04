<script>
    var msg_required_data       = '<?php echo _t('global.msg.fill_required_data'); ?>';
	var msg_save_fail           = '<?php echo _t('global.msg.save_fail'); ?>';
	var msg_delete_fail         = '<?php echo _t('global.msg.delete_fail'); ?>';
	var msg_password_not_match  = "<?php echo _t('admin.msg.password_not_match'); ?>";
	var msg_password_criteria   = '<?php echo _t('user.msg.password_criteria'); ?>';
	var q_delete_user           = '<?php echo _t('user.q.delete_user'); ?>';
</script>

<div class="wrapper col-sm-9">
	<h4 class="sub-header">
        <i class="fa fa-users"></i>&nbsp;
        <span><?php echo _t('global.user'); ?></span>&nbsp;
        <span class="hint--right hint--info" data-hint='<?php echo _t('user.new-user'); ?>' id="btn-new-user"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span>
    </h4>
	<table class="table table-bordered table-striped" id="tbl-user" data-laboratory="<?php echo isset($current_laboratory) && $current_laboratory > 0 ? $current_laboratory : ''; ?>">
		<thead>
			<th><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('user.fullname'); ?></th>
			<th><?php echo _t('user.username'); ?></th>
			<th><?php echo _t('user.email'); ?></th>
			<th><?php echo _t('user.phone'); ?></th>
			<th></th>
		</thead>
		<tbody></tbody>
	</table>
</div>