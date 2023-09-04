<!--Modal Read patient from excel -->
<div class="modal fade modal-success" id="modal_result_line_list_new" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg" style="width: 85%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fa fa-check-circle" aria-hidden="true" style="color:green;"></i>&nbsp;
				<b><?php echo _t('sample.result'); ?></b>
			</div>
			<div class="modal-body">
                <table class="table table-sm" id="tblResultLineListNew" name="tblResultLineListNew">
                    <thead>
                        <tr>
							<th><?php echo _t('sample.order_number'); ?></th>
							<th><?php echo _t('patient.patient_id'); ?></th>
                            <th><?php echo _t('patient.name'); ?></th>
                            <th><?php echo _t('sample.result'); ?></th>
                            <th><?php echo _t('sample.sample_number'); ?></th>
                            <th><?php echo _t('sample.result'); ?></th>
							<th><?php echo _t('sample.test'); ?></th>
							<th><?php echo _t('sample.test_result'); ?></th>
							<th><?php echo _t('sample.option'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
			</div>
			<div class="modal-footer">	
				<button type="button" class="btn btn-success btnPrintCovidFormV1" data-psample_id="" id="printAll">
					<i class="fa fa-print"></i> <?php echo _t('sample.preview_request'); ?>
				</button>
				<!--
					<button type="button" class="btnQrCode btn btn-sm btn-primary" data-patient_code="-1"><i class="fa fa-qrcode">​</i><?php echo _t('sample.print_all_qr_code');?> </button>				
				-->
				<button type="button" class="btn btn-default" data-dismiss='modal'>
					<?php echo _t('global.close'); ?>
				</button>
			</div>
		</div>
	</div>
</div>