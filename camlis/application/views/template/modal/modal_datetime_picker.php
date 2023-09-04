<!-- Modal -->
<div class="modal fade modal-primary" id="modal-datepicker">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title new">Datetime</h4>
			</div>
			<div class="modal-body">
				<div>
					<div class="form-vertical">
						<div class="row form-group">
							<div class="col-sm-12">
								<label for="department_name" class="control-label">Datetime picker</label>
								<input class="form-control" id="date_picker_comment" placeholder="">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btn-choose-datetime">
					<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<span class="save">Choose</span>
				</button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>