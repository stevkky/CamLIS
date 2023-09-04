<!--Modal Reject Sample/Test -->
<div class="modal fade modal-danger" id="modal-rejection">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;
				<b><?php echo _t('sample.msg.provide_reject_cmt'); ?></b>
			</div>
			<div class="modal-body">
				<div class="well">
					<div class="row">
						<div class="col-sm-12">
							<b class="hint--right hint--error hint--always" id="sample_test_error"><i class="fa fa-info-circle"></i>&nbsp;<?php echo _t('sample.msg.choose_reject_sample_test'); ?></b>
							<hr style="margin:10px 0;">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<label class="control-label text-blue" style="cursor:pointer;">
								<input type="checkbox" name="reject_sample" id="reject_sample" value="1">&nbsp;
								<?php echo _t('sample.reject_sample'); ?>
							</label>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-sm-12">
							<h5><i class="fa fa-hand-o-right"></i>&nbsp;<b><?php echo _t('sample.selected_reject_test'); ?></b></h5>
						</div>
					</div>
					<div class="test-list" style="padding-left: 23px;"></div>
				</div>
				<!-- 29/08/2018 add class hide and class extarea_comment for create text area comment on each dapartment -->
				<div class="row hide">
					<div class="col-sm-12 textarea_comment">
						<div class="form-vertical">
                            <div class="row" style="margin-bottom: 5px;">
                                <div class="col-sm-12">
                                    <label for="reject_comment" class="control-label hint--right hint--error hint--always" data-hint=''>
                                        <i class="fa fa-comments-o"></i>&nbsp;<?php echo _t('global.comment'); ?> *
                                    </label>
                                    <button class="btn btn-primary btn-sm pull-right show-reject-comment-modal"><i class="fa fa-plus"></i>&nbsp;<?php echo _t('global.choose'). ' '. _t('global.comment'); ?></button>
                                </div>
                            </div>
							<div class="row">
								<div class="col-sm-12">
									<textarea name="reject_comment" id="reject_comment" cols="30" rows="2" class="form-control" style="resize:none;"></textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
                <button type="button" class="btn btn-success pull-left print-reject-test"><i class="fa fa-print"></i>&nbsp;<?php echo _t('global.print'); ?></button>
				<button type="button" class="btn btn-primary" id="btnAddRejection"><i class="fa fa-floppy-o"></i>&nbsp;
					<?php echo _t('global.save'); ?>
				</button>
				<button type="button" class="btn btn-default" data-dismiss='modal'>
					<?php echo _t('global.cancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>