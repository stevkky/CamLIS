<script>
    const PAYMENT_TYPES         = <?php echo json_encode($payment_types) ?>;
    const GROUP_RESULTS         = <?php echo json_encode($group_results); ?>;
    var msg_required_data       = '<?php echo _t('global.msg.fill_required_data'); ?>';
    var q_delete_test_payment   = '<?php echo _t('manage.msg.q_delete_test_payment'); ?>';
    var label_all               = "<?php echo _t('manage.all'); ?>";
    var label_choose            = "<?php echo _t('global.choose'); ?>";
    var label_group_resutls     = "<?php echo _t('manage.group_results'); ?>";
    var label_riel              = "<?php echo _t('manage.riel'); ?>";
</script>
<div class="wrapper col-sm-9">
	<h4 class="sub-header"><?php echo _t('manage.test_payment'); ?>&nbsp;&nbsp;<span class="hint--right hint--info" data-hint='<?php echo _t('manage.new_test_payment'); ?>' id="new-test-payment"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4>
	<table class="table table-bordered table-striped" id="tbl-test-payment">
		<thead>
            <th></th>
			<th style="width:40px;"><?php echo _t('global.no.'); ?></th>
            <th><?php echo _t('manage.group_result'); ?></th>
			<th style="width: 50px;"></th>
		</thead>
		<tbody></tbody>
	</table>
</div>