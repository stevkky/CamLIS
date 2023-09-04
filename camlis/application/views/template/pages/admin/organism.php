<div class="wrapper col-sm-9">
	<h4 class="sub-header"><?php echo _t('global.organism/antibiotic'); ?> &nbsp;&nbsp;<span class="hint--right hint--info" data-hint='Assign Organism/Antibiotic' id="addNew"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4>
	<table class="table table-bordered table-striped" id="tb_stest_organism">
		<thead>
			<th style="width:30px;"></th>
			<th style="width:40px;"><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('global.department'); ?></th>
			<th><?php echo _t('global.sample'); ?></th>
			<th><?php echo _t('global.test'); ?></th>
			<th style="width:100px"># Organism</th>
			<th style="width:100px"># Antibiotic</th>
			<th></th>
		</thead>
		<tbody></tbody>
	</table>
</div>

<!--Modal-->
<div class="modal fade" id="modal_assign_organism">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 id="header">New</h4>
			</div>
			<div class="modal-body">
				<div class="form-vertical">
					<div class="row">
						<div class="col-sm-4">
							<label for="department" class="control-label hint--right hint--error hint--always" data-hint='' id="label_department"><?php echo _t('global.department'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
							<select name="department" id="department" class="form-control">
								<option value="-1"></option>
								<?php
									foreach ($departments as $dep) {
										echo "<option value='".$dep->lab_department_id."'>".$dep->department_name."</option>";    
									}
								?>
							</select>
						</div>
						<div class="col-sm-4">
							<label for="sample" class="control-label hint--right hint--error hint--always" data-hint='' id="label_sample"><?php echo _t('global.sample'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
							<select name="sample" id="sample" class="form-control">
								<option value="-1"></option>
							</select>
						</div>
						<div class="col-sm-4">
							<label for="test" class="control-label hint--right hint--error hint--always" data-hint='' id="label_test"><?php echo _t('global.test'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
							<select name="test" id="test" class="form-control">
								<option value="-1"></option>
							</select>
						</div>
					</div>
				</div>
				<div class="row form-group" style="margin-top:20px;">
					<div class="col-sm-6">
						<h4 class="content-header" style="font-size:12pt"><?php echo _t('global.organism'); ?> &nbsp;&nbsp;<span class="hint--right hint--info" data-hint='<?php echo _t('manage.new_organism'); ?>' id="new_organism"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4><hr>
						<table class="table table-bordered table-striped" id="tb_organism">
							<thead>
								<th style='width:40px;'><?php echo _t('global.no.'); ?></th>
								<th><?php echo _t('global.organism'); ?></th>
								<th></th>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					<div class="col-sm-6">
						<h4 class="content-header" style="font-size:12pt;"><?php echo _t('global.antibiotic'); ?> &nbsp;&nbsp;<span class="hint--right hint--info" data-hint='<?php echo _t('manage.new_antibiotic'); ?>' id="new_antibiotic"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4><hr>
						<table class="table table-bordered table-striped" id="tb_antibiotic">
							<thead>
								<th style='width:40px;'><?php echo _t('global.no.'); ?></th>
								<th><?php echo _t('global.antibiotic'); ?></th>
								<th></th>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnSave"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<?php echo _t('global.save'); ?></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button> 
			</div>
		</div>
	</div>
</div>