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
	<h4 class="sub-header">&nbsp;&nbsp;<?php echo _t('global.listmachine'); ?> &nbsp;&nbsp;<span class="hint--right hint--info" data-hint='Assign machine test' id="machine-test">
			<i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i>
		</span>
	</h4>
	<table class="table table-bordered table-striped" id="tbl-machine">
		<thead>
			<th><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('global.machine'); ?></th>
			<th><?php echo _t('global.test'); ?></th>
			<th class="action"></th>
		</thead>
		<tbody>
			<?php $i = 1; ?>
			<?php foreach ($list_machine_tests as $machine_test): ?>
				<tr>
					<td><center><?php echo $i; ?></center></td>
					<td><?php echo $machine_test->machine_name; ?></td>
					<td><?php echo $machine_test->department_name."=>".$machine_test->sample_name."=>".$machine_test->test_name; ?></td>
					<td>
						<center>
							<a href='#' class='text-blue edit hint--left hint--info' data-hint="<?php echo _t('global.edit'); ?>" id="<?php echo $machine_test->id; ?>">
								<i class='fa fa-pencil-square-o'></i>
							</a>&nbsp;|&nbsp;
							<a href='#' class='text-red remove hint--left hint--error' data-hint="<?php echo _t('global.remove'); ?>" id="<?php echo $machine_test->test_id; ?>">
								<i class='fa fa-trash'></i>
							</a>
						</center>
					</td>
				</tr>
				<?php $i++; ?>
			<?php endforeach ?>
		</tbody>
	</table>
</div>