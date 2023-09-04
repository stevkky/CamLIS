<!--Modal Read patient from excel -->
<div class="modal fade modal-primary fixed-footer" id="modal_excel_result" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg" style="width: 95%; ">
		<div class="modal-content" style="height: 90%;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-pencil" aria-hidden="true"></i>&nbsp; <b><?php echo _t('patient.add_result_test_covid_19_line_list'); ?></b></h4>
				
			</div>
			<div class="modal-body" style=" max-height: calc(100% - 60px); overflow-y: scroll;">
				<p>(*) <?php echo _t('patient.required_field'); ?></span></p>
				<div class="col-md-12 mt-3" style="margin-top:10px; position:relative; max-width:99%; overflow:auto;">
					<div id="spreadsheetResult"></div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="row form-horizontal">
					<div class="col-sm-5 text-left">
					&nbsp;					
					</div>
					<div class="col-sm-7 text-right">
						<button type="button" class="btn btn-primary" id="btnSaveListResult"><i class="fa fa-floppy-o"></i>&nbsp;
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