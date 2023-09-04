<div class="modal fade modal-primary" id="modal_upload_excel" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;
				<b>ជ្រើសរើសឯងសារសំរាប់ទាញទិន្នន័យ</b>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" id="theUploadForm">
					<div class="row form-group">
						<label for="theExcelFile">សូមជ្រើសរើសឯកសារ​* (only xls and xlsx allowed)</label>
						<input type="file" class="form-control" id="theExcelFile" name="theExcelFile">
					</div>
					<div class="invalid-feedback" id="theExcelFile_message"></div>
					<button type="submit" class="btn btn-primary" id="btnSubmitExcelFile" disabled>Submit</button>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss='modal'>
					<?php echo _t('global.cancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>