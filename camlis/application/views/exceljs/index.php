<?php
	//$app_lang	= empty($app_lang) ? 'en' : $app_lang;
	$app_lang	= 'en';
	$FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
	if($app_lang == 'en'){
		$REASON_FOR_TESTING_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY);		
		$VACCINATION_STATUS_DD = unserialize(VACCINATION_STATUS_DD_EN);
	}else{
		$REASON_FOR_TESTING_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
		$VACCINATION_STATUS_DD = unserialize(VACCINATION_STATUS_DD_KH);
	}
?>
<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta charset="UTF-8">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
	<title>ExcelJs</title>	
	<link rel="stylesheet" href="<?php echo site_url('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'); ?>">	
	<link rel="stylesheet" href="<?php echo site_url('assets/plugins/jspreadsheet/css/jsuites.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/plugins/jspreadsheet/css/jexcel.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/exceljs/exceljs_style.css'); ?>">	
</head>
  <body>
	<div class="dialog-background" style="display: none;" id="loading">
		<div class="dialog-loading-wrapper">
			<span class="dialog-loading-icon">Loading....</span>
		</div>
	</div> 
  	<div class="parent">
		  <div class="child">
		  	<div id="spreadsheet"></div>
		  </div>
	</div>
	<div class="fab-container">
  <div class="fab fab-icon-holder">
    <i class="fas fa-cog"></i>
  </div>
  <ul class="fab-options">
    <li data-toggle="modal" data-target="#export_form_modal">
      <span class="fab-label">Export</span>
      <div class="fab-icon-holder">
        <i class="fas fa-download"></i>
      </div>
    </li>
    <li id="btnSave">
      <span class="fab-label">Save</span>
      <div class="fab-icon-holder">
	  <i class="fas fa-save"></i>
      </div>
    </li>
  </ul>
</div>
	<!-- Button trigger modal -->
	<!-- Modal -->
	<div class="modal fade" id="resuldModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Result</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table table-bordered table-striped table-sm" id="table_result" name="table_result">
					<thead>
						<th>N</th>
						<th>Name</th>
						<th>Status</th>
						<th>Message</th>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="export_form_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" style="width: 98%;">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Export Data</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="row" style="margin-bottom:15px;">
						<div class="col-md-12">
							<form class="form-inline">
								<div class="form-group mb-2">
									<label for="date">Date of result: </label>
									<input type="text" class="form-control start_date" id="start_date" value="" name="start_date">
								</div>
								<div class="form-group mx-sm-3 mb-2">
									<input type="text" class="form-control end_date" id="end_date" name="end_date">
								</div>
								<button type="button" class="btn btn-primary mb-2" id="btnGetData">Submit</button>
							</form>
						</div>						
					</div>
					<div class="row">
						<div class="col-md-12" style="overflow-y: auto;">
							<table class="table table-bordered table-striped table-sm" name="table_data">
								<thead>
									<th>No</th>
									<th>No. by Day</th>
									<th>CDC Case No</th>
									<th>Laboratory Code</th>
									<th>Full name</th>
									<th>Sex</th>
									<th>Age</th>
									<th>nationality</th>
									<th>Phone</th>
									<th>Date of Sampling</th>
									<th>Date of Result</th>
									<th>F20 Event</th>
									<th>Imported Country`</th>
									<th>Date of onset</th>
									<th>Symptoms (306)</th>
									<th>Positive on</th>
									<th>Reason for testing</th>
									<th>Province</th>
									<th>District</th>
									<th>Commune</th>
									<th>village</th>
									<th>Province of detection</th>
									<th>Remark</th>
									<th>Vaccination Status</th>
									<th>Fist injection date</th>
									<th>Second injection date</th>
									<th>Vaccine name</th>
									<th>Image</th>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
			</div>
		</div>
	</div>
	
  <script>
	var app_language		   = "<?php echo $app_lang; ?>";
	var base_url			   = "<?php echo base_url();?>";
	const DISTRICTS            = <?php echo json_encode($districts); ?>;
	const COMMUNES             = <?php echo json_encode($communes); ?>;
	const VILLAGES             = <?php echo json_encode($villages); ?>;
	const NATIONALITIES        = <?php echo json_encode($nationalities); ?>;
	const COUNTRIES        	   = <?php echo json_encode($countries); ?>;
	const VACCINATION_STATUS_ARR = <?php echo json_encode($VACCINATION_STATUS_DD);?>;
	const REASON_FOR_TESTING_ARR   = <?php echo json_encode($REASON_FOR_TESTING_ARR);?>;
	const VACCINE_TYPE_ARR 		= <?php echo json_encode($vaccines);?>;
	const PROVINCES            = <?php echo json_encode($provinces); ?>;
  </script>
  <script src="<?php echo site_url('assets/plugins/jQuery-2.1.4.min.js'); ?>"></script>  
  <script src="<?php echo site_url('assets/plugins/moment.js'); ?>"></script>  
  <script type="text/javascript" src="<?php echo site_url('assets/plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/plugins/jspreadsheet/js/jexcel.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/plugins/jspreadsheet/js/jsuites.js'); ?>"></script>
  <script src='https://kit.fontawesome.com/622a8b63d5.js' crossorigin='anonymous'></script>
  <script type="text/javascript" src="<?php echo site_url('assets/plugins/moment.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/exceljs/excel_script.js'); ?>"></script>
  </body>
</html>
