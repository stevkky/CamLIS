<div class="wrapper col-sm-9">
	<h4 class="sub-header"><?php echo _t('global.test'); ?> &nbsp;&nbsp;<span class="hint--right hint--info" data-hint='Add Test' id="addNew"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4>
	<table class="table table-bordered table-striped" id="tb_sampleTest">
		<thead>
			<th style="width:30px;'"><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('global.department'); ?></th>
			<th><?php echo _t('global.sample'); ?></th>
			<th><?php echo _t('global.test'); ?></th>
			<th><?php echo _t('global.unit_sign'); ?></th>
			<th><?php echo _t('manage.field_type'); ?></th>
			<th>Default Select</th>
			<th style="width:60px;"></th>
		</thead>
		<tbody></tbody>
	</table>
</div>

<!--Modal-->
<div class="modal fade" id="modal_new_test">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 id="header"><?php echo _t('manage.new_test'); ?></h4>
			</div>
			<div class="modal-body">
				<form method="post" action="#" class="form-vertical" id="frm_new_stest">
					<div class="row form-group">
						<div class="col-sm-4">
							<label for="department" class="control-label hint--right hint--error hint--always" data-hint=''><?php echo _t('global.department'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
							<select name="department" id="department" class="form-control">
								<option value="-1"><?php echo _t('global.choose'); ?></option>
								<?php
									foreach ($departments as $dep) {
										echo "<option value='".$dep->lab_department_id."'>".$dep->department_name."</option>";    
									}
								?>
							</select>
						</div>
						<div class="col-sm-4">
							<label for="sample" class="control-label hint--right hint--error hint--always" data-hint=''><?php echo _t('global.sample'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
							<select name="sample" id="sample" class="form-control">
								<option value="-1"><?php echo _t('global.choose'); ?></option>
							</select>
						</div>
						<div class="col-sm-4">
							<label for="test" class="control-label hint--right hint--error hint--always" data-hint=''><?php echo _t('global.test'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
							<select name="test" id="test" class="form-control">
								<option value="-1"></option>
								<?php
									foreach ($std_tests as $test) {
										echo "<option value='".$test->testID."'>".$test->testName."</option>";
									}
								?>
							</select>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-sm-4">
							<label class="control-label"><?php echo _t('manage.group_by'); ?></label>
							<select name="group_by" id="group_by" class="form-control">
								<option value="-1"><?php echo _t('global.choose'); ?></option>
							</select>
						</div>
						<div class="col-sm-4">
							<label class="control-label"><?php echo _t('global.unit_sign'); ?></label>
							<input type="text" class="form-control" name="unit_sign" id="unit_sign">
						</div>
						<div class="col-sm-4">
							<label for="field_type" class="control-label hint--right hint--error hint--always" data-hint=''><?php echo _t('manage.field_type'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
							<select name="field_type" id="field_type" class="form-control">
								<option value="-1"></option>
								<option value="1">Single</option>
								<option value="2">Multiple</option>
								<option value="3">Numeric</option>
								<option value="4">Text</option>
							</select>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-sm-4">
							<label class="control-label">Default Selected</label>
							<div>
								<label class="control-label" style="cursor:pointer;">
									<input type="checkbox" name="is_default_selected" value='1' id="is_default_selected">&nbsp;&nbsp;Yes
								</label>
							</div>
						</div>
					</div>
				</form>
				<div class="form-vertical">
					<div class="row form-group" style="margin:0 2px;">
						<h4 class="content-header" style="font-size:13pt;"><?php echo _t('manage.ref_range'); ?>&nbsp;&nbsp;<span class="hint--right hint--info" data-hint='<?php echo _t('manage.new_ref_range'); ?>' id="new_ref_range"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4>
						<hr>
						<div class="col-sm-12">
							<table class="table table-striped table-bordered" id="tb_ref_range">
								<thead>
									<th><?php echo _t('manage.patient_type'); ?></th>
									<th><?php echo _t('manage.min_val'); ?></th>
									<th><?php echo _t('manage.max_val'); ?></th>
									<th style="width:70px"></th>
								</thead>
								<tbody>

								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnNewTest"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<?php echo _t('global.save'); ?></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button> 
			</div>
		</div>
	</div>
</div>