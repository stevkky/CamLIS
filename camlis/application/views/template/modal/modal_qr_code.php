<!--Modal Read patient from excel -->
<div class="modal fade modal-success" id="modal_qr_code" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fa fa-check-circle" aria-hidden="true" style="color:green;"></i>&nbsp;
			</div>
			<div class="modal-body">
				<div id="tbl_qr_code_wrapper" style="width:50mm; height: 23mm;">
					<table id="tbl_qr_code" name="tbl_qr_code" cellpadding="0" cellspacing="0" style="padding: 0px; border-top: 1px solid #fff; border-left: 1px solid #fff;">
						<tbody></tbody>
					</table>
				</div>				
			</div>
			<div class="modal-footer">						
				<button type="button" class="btn btn-default" data-dismiss='modal'>
					<?php echo _t('global.close'); ?>
				</button>
			</div>
		</div>
	</div>
</div>