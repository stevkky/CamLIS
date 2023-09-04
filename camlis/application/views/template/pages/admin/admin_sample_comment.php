<script>
	var msg_save_fail = '<?php echo _t('global.msg.save_fail'); ?>';
	var msg_update_fail = '<?php echo _t('global.msg.update_fail'); ?>';
	var msg_delete_fail = '<?php echo _t('global.msg.delete_fail'); ?>';
	var msg_required_data = '<?php echo _t('global.msg.fill_required_data'); ?>';
	var q_delete_comment = '<?php echo _t('admin.msg.q_delete_comment'); ?>';
</script>
<div class="wrapper col-sm-9">
	<h4 class="sub-header"><?php echo _t('global.comment'); ?>&nbsp;&nbsp;<span class="hint--right hint--info" data-hint='<?php echo _t('admin.new_comment'); ?>' id="addNew"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4>
    <div class="filter-box">
        <div class="row" style="margin-bottom: 0;">
            <div class="col-sm-4">
                <div>
                    <label class="control-label pointer">
                        <input type="checkbox" value="1" id="show-reject-comment">&nbsp;&nbsp;<?php echo _t('admin.show_only_reject_comment'); ?>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-bordered table-striped" id="tbl-sample-comment">
		<thead>
			<th style="width:30px;"><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('global.department'); ?></th>
			<th><?php echo _t('global.sample_type'); ?></th>
			<th><?php echo _t('global.comment'); ?></th>
            <th><?php echo _t('admin.is_reject_comment'); ?></th>
			<th style="width: 50px;"></th>
		</thead>
		<tbody></tbody>
	</table>
</div>