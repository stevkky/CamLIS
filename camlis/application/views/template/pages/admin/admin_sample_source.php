<script>
	var msg_save_fail = '<?php echo _t('global.msg.save_fail'); ?>';
	var msg_update_fail = '<?php echo _t('global.msg.update_fail'); ?>';
	var msg_delete_fail = '<?php echo _t('global.msg.delete_fail'); ?>';
	var msg_required_data = '<?php echo _t('global.msg.fill_required_data'); ?>';
	var q_delete_sample_source = '<?php echo _t('admin.msg.q_delete_sample_source'); ?>';
</script>
<div class="wrapper col-sm-9">
	<h4 class="sub-header"><?php echo _t('global.sample_source'); ?>&nbsp;&nbsp;<span class="hint--right hint--info" data-hint='<?php echo _t('admin.new_sample_source'); ?>' id="addNew"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4>
	<table class="table table-bordered table-striped" id="tbl-sample-source">
		<thead>
			<th style="width:40px;"><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('admin.sample_source_name'); ?></th>
			<th style="width: 50px;"></th>
		</thead>
		<tbody></tbody>
	</table>
</div>

<!-- Modal -->
<div class="modal fade modal-primary" id="modal-sample-source">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title new"><?php echo _t('admin.new_sample_source'); ?></h4>
				<h4 class="modal-title edit" style="display: none;"><?php echo _t('admin.edit_sample_source'); ?></h4>
			</div>
			<div class="modal-body">
				<div>
					<div class="form-vertical">
						<div class="row form-group">
							<div class="col-sm-12">
								<label for="sample_source" class="control-label"><?php echo _t('admin.sample_source_name'); ?> <sup class="fa fa-asterisk" style="font-size: 8px;"></sup></label>
								<input type="text" class="form-control" name="sample_source" id="sample-source-name">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnSave"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<span class="save"><?php echo _t('global.save'); ?></span><span class="update" style="display: none;"><?php echo _t('global.update'); ?></span></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>