<!--Modal Read patient from excel -->
<style>
th{
	text-align: center;
}
</style>
<div class="modal fade modal-danger" id="modal_error_add_result" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg" style="width: 85%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fa fa-exclamation-circle" aria-hidden="true" style="color:red;"></i>&nbsp;
				<b><?php echo _t('global.table_of_incorrect_data'); ?></b>
			</div>
			<div class="modal-body" style="overflow: auto;">
                <table class="table table-sm table-bordered" id="tblErrorLineList" name="tblErrorLineList">
                    <thead align="center">						
						<tr>
							<th>N</th>
							<th><?php echo _t('sample.sample_number'); ?></th>
							<th><?php echo _t('patient.patient_id'); ?></th>
                            <th><?php echo _t('patient.name'); ?></th>
							<th><?php echo _t('sample.test_name'); ?></th>
							<th><?php echo _t('sample.machine_name');?></th>
							<th><?php echo _t('sample.result'); ?></th>
							<th><?php echo _t('sample.test_date'); ?></th>
							<th><?php echo _t('sample.performed_by'); ?></th>							
						</tr>
                    </thead>
                    <tbody align="center">
                    </tbody>
                </table>
			</div>
			<div class="modal-footer">						
				<button type="button" class="btn btn-default" data-dismiss='modal'>
					<?php echo _t('global.close'); ?>
				</button>
			</div>
		</div>
	</div>
</div>