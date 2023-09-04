<!--Modal Read patient from excel -->
<div class="modal fade modal-primary modal-wide fixed-footer" id="modal-excel-full-form" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg" style="width:98%;">
		<div class="modal-content" style="height: 90%;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;
				<b><?php echo _t('patient.add_patient_via_excel'); ?></b>
			</div>
			<div class="modal-body" style=" max-height: calc(100% - 60px); overflow-y: scroll;">
				<p>(*) <?php echo _t('patient.required_field'); ?> | <span class="text-warning bg-dark" style="background-color: yellow;">(*) <?php echo _t('patient.maximum_add_patient'); ?></span> | <span class="text-warning bg-dark" style="background-color: blue; color:white;">(កំណត់ចំនាំសំរាប់អាយុ) បើបញ្ចូល "21" = 21ឆ្នាំ បើ "9m"= 9ខែ បើ "23d" = 23ថ្ងៃ </span></p>
				<div class="col-md-12 mt-3" style="margin-top:10px; position:relative; max-width:99%; overflow:auto;">
					<div id="spreadsheetNew"></div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="row form-horizontal">
					<div class="col-sm-5 text-left">
					&nbsp;					
					<button type="button" class="btn btn-success" id="btnOpenUploadFileModal"><i class="fa fa-upload"></i>&nbsp;<?php echo _t('global.upload'); ?></button>
					</div>
					<div class="col-sm-7 text-right">
						<button type="button" class="btn btn-primary" id="btnSaveListNew"><i class="fa fa-floppy-o"></i>&nbsp;
							<?php echo _t('global.save'); ?>
						</button>
						<button type="button" class="btn btn-default" data-dismiss='modal'>
							<?php echo _t('global.close'); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>