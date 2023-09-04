<!--Style-->
<link rel="stylesheet" href="<?php echo site_url('assets/plugins/timepicki/css/timepicki.css'); ?>">
<link rel="stylesheet" href="<?php echo site_url('assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css'); ?>">
<link rel="stylesheet" href="<?php echo site_url('assets/plugins/treeview/treeview.css'); ?>">
<link rel="stylesheet" href="<?php echo site_url('assets/plugins/datepicker/css/bootstrap-datepicker.min.css'); ?>">
<link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/camlis_result.css') ?>">
<!--Script-->
<script type="text/javascript" src="<?php echo site_url('assets/plugins/timepicki/js/timepicki.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/plugins/moment.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/plugins/treeview/treeview.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/plugins/datepicker/js/bootstrap-datepicker.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/camlis/js/camlis_add_result.js'); ?>"></script>
<script>
	var sample_dateTime = null;
	var psample_id		= "<?php echo $psample->psampleID; ?>";
	var patient_dob		= "<?php echo $patient->dob; ?>";
</script>
<h4 class="no-marginTop content-header">Sample Information</h4> 
<hr>
<div class="content-wrapper">
	<div class="patient-info-wrapper">
		<div class="form-vertical">
			<div class="row">
				<div class="col-md-3">
					<label class="control-label">Patient's Name</label>
					<div id="patient_id" class="text-blue"><?php echo $patient->name; ?></div>
				</div>
				<div class="col-md-3">
					<label class="control-label">Sample Number</label>
					<div id="sample_number" class="text-blue"><?php echo $psample->sample_number; ?></div>
				</div>
				<div class="col-md-3">
					<label class="control-label">Sample Type</label>
					<div id="sample_type" class="text-blue"><?php echo $psample->sample_name; ?></div>
				</div>
				<div class="col-md-3">
					<label class="control-label">Sample Source</label>
					<div id="sample_source" class="text-blue"><?php echo $psample->sample_source; ?></div>
				</div>
			</div>
		</div>
	</div>
	
	<h4 class="content-header">Result</h4> 
	<hr>
	<div id="test_list_wrapper">
		<div class="row">
			<div class="col-md-10">
				<table class="table table-striped" id="tb_test_result">
					<thead>
						<th>N<sup>o</sup></th>
						<th>Test Name</th>
						<th>Result</th>
						<th>Unit Sign</th>
						<th>Ref. Range</th>
						<th>Test Date</th>
						<th>Performed By</th>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div class="col-md-2" style="padding-left:0;">
				<table class="table" id="tb_result_cmt">
					<thead>
						<th>Comment</th>
					</thead>
					<tbody>
						<tr>
							<td>
								<textarea name="result_cmt" id="result_cmt" rows="15" class="form-control"></textarea>
							</td>
						</tr>
						<tr>
							<td>
								<button type="button" class="btn btn-primary" id="btn_select_cmt">Select Comment</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<button type="button" class="btn btn-primary" id="btnAddResult"><i class="fa fa-floppy-o"></i> Save Result</button>
	<button type="button" class="btn btn-success" id="btnPrint"><i class="fa fa-print"></i> Print Result</button>
	<a href="<?php echo site_url("sample/edit/".$psample->patient_id."/$psample->psampleID"); ?>" class="btn btn-primary" id="btnEditSample"><i class="fa fa-pencil-square-o"></i> Edit Sample</a>
</div>     

<!-- Posssible Result -->
<div class="modal fade" id="possible_result_modal">
	<div class="modal-dialog" style="width:80%;">
		<div class="modal-content">
			<div class="modal-header with-border">
				<h4>Result</h4>
			</div>
			<div class="modal-body clearfix">
				<div id="organism_wrapper" class="pull-left" style="width:60%;">
					<table class="table table-bordered table-striped" id="list">
						<thead>
							<th>Possible Result</th>
							<th class="text-center">Quantity</th>
						</thead>
						<tbody></tbody>
					</table>
				</div>  
				<div id="antibiotic_wrapper" class="pull-right" style="width:38%;"> 
					<table class="table table-bordered table-striped" id="antibiotic_list">
						<thead>
							<th>Antibiotic</th>
							<th>Sensitivity</th>
							<th>Test Zone</th>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnAddOrganism">Add Result</button>
				<button type="button" class="btn btn-default" data-dismiss='modal'>Cancel</button> 
			</div>
		</div>
	</div>
</div>