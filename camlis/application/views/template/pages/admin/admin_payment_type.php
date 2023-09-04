<script>
    var msg_required_data = '<?php echo _t('global.msg.fill_required_data'); ?>';
    var q_delete_payment_type = '<?php echo _t('admin.msg.q_delete_payment_type'); ?>';
</script>
<div class="wrapper col-sm-9">
	<h4 class="sub-header"><?php echo _t('global.payment_type'); ?>&nbsp;&nbsp;<span class="hint--right hint--info" data-hint='<?php echo _t('admin.new_payment_type'); ?>' id="addNew"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4>
	<table class="table table-bordered table-striped" id="tbl-payment-type">
		<thead>
			<th style="width:40px;"><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('global.payment_type'); ?></th>
			<th style="width: 50px;"></th>
		</thead>
		<tbody></tbody>
	</table>
</div>