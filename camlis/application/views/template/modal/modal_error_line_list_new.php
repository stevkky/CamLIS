<!--Modal Read patient from excel -->
<style>
th{
	text-align: center;
}
</style>
<div class="modal fade modal-danger" id="modal_error_line_list_new" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg" style="width: 95%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fa fa-exclamation-circle" aria-hidden="true" style="color:red;"></i>&nbsp;
				<b><?php echo _t('global.table_of_incorrect_data'); ?></b>
			</div>
			<div class="modal-body" style="overflow: auto;">
                <table class="table table-sm table-bordered" id="tblErrorLineListNew" name="tblErrorLineListNew">
                    <thead align="center">						
						<tr>
							<!--
							<th>លេខសំគាល់អ្នកជំងឺ</th>
                            <th>ឈ្មោះ</th>
							<th>អាយុ</th>
							<th>ភេទ</th>
							<th>លេខទូរស័ព្ទ</th>
							<th>ខេត្ត</th>
							<th>ស្រុក</th>
							<th>ឃុំ</th>
							<th>ភូមិ</th>							
							<th>ប្រភពសំណាក</th>
							<th>អ្នកស្នើសុំ</th>
							<th>កាលបរិច្ឆេទប្រមូលសំណាក</th>
							<th>កាលបរិច្ឆេទទទួលសំណាក</th>														
							<th>តេស្ត</th>
							<th>លទ្ធផល</th>
							<th>កាលបរិច្ឆេទធ្វើពិសោធន៏</th>
							<th>វិភាគដោយ</th>
							-->
							
							<th><?php echo _t('patient.patient_id'); ?></th>
                            <th><?php echo _t('patient.name'); ?></th>
							<th><?php echo _t('global.patient_age'); ?></th>
							<th><?php echo _t('global.patient_gender'); ?></th>
							<th><?php echo _t('global.patient_phone_number'); ?></th>
							<th><?php echo _t('patient.province'); ?></th>
							<th><?php echo _t('patient.district'); ?></th>
							<th><?php echo _t('patient.commune'); ?></th>
							<th><?php echo _t('patient.village'); ?></th>							
							<th><?php echo _t('sample.sample_source'); ?></th>
							<th><?php echo _t('sample.requester'); ?></th>
							<th><?php echo _t('sample.collect_dt'); ?></th>
							<th><?php echo _t('sample.receive_dt'); ?></th>
							<th><?php echo _t('sample.test_name'); ?></th>
							<th><?php echo _t('sample.result'); ?></th>
							<th><?php echo _t('sample.test_date'); ?></th>
							<th><?php echo _t('sample.performed_by'); ?></th>
							<th><?php echo _t('sample.other_error'); ?></th>
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