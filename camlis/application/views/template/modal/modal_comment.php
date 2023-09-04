<!-- Modal -->
<div class="modal fade modal-primary" id="modal-reason-comment">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title new">Comment</h4>
			</div>
			<div class="modal-body">
				<div>
					<div class="form-vertical">
						<div class="row form-group">
							<div class="col-sm-12">
								<label for="department_name" class="control-label">Type reason update the result of the test</label>
								<input type="" class="form-control" name="reason_comment" id="reason_comment" data-patient-test-id="" />								
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnAddReasonComment">
					<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<span class="save">Insert</span>
				</button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>