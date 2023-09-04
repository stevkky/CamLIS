<?php
    $laboratory_logo_url = site_url('assets/camlis/images/moh_logo.png');
    if (isset($laboratoryInfo->photo) && !empty($laboratoryInfo->photo)  && file_exists('./assets/camlis/images/laboratory/'.$laboratoryInfo->photo)) {
        $laboratory_logo_url = site_url('assets/camlis/images/laboratory/'.$laboratoryInfo->photo);
    }
?>
<script>
	var msg_required_data = '<?php echo _t('global.msg.fill_required_data'); ?>';
	var date_required_startend = '<?php echo _t('global.msg.date_required_startend'); ?>';
	var label_all              = "<?php echo _t('global.all'); ?>";
	var label_laboratories     = "<?php echo _t('report.laboratory'); ?>";
</script>  

<style>
	.ui-progressbar { height:2em; text-align: left; overflow: hidden; }
	.ui-progressbar .ui-progressbar-value {margin: -1px; height:100%; }
</style>
<div >
<?php 

	$currentLabo = $this->session->userdata('laboratory');
	$userLabo	=  $this->session->userdata('user_laboratories');
	//print_r($userLabo);
?>
</div>
<div class="wrapper col-sm-9"> 
		<div class="form-vertical"> 
			<div class="row">
				<div class="col-sm-12">
					<label for="lab_nameEN" class="control-label hint--right hint--error hint--always"><?php echo _t('report.laboratory'); ?></label>
					<div>
						<select class="form-control" name="labo_names" id="labo_names" multiple="multiple">    
                        	<?php 
								$app_lang	= empty($app_lang) ? 'en' : $app_lang;
								if($app_lang == 'en'){
									foreach($laboratories as $key => $row){
										if($row->labID == $currentLabo->labID){
											echo '<option value="'.$row->labID.'" selected>'.$row->name_en.'</option>';
										}else{
											echo '<option value="'.$row->labID.'">'.$row->name_en.'</option>';
										}
									}
								}else{
									foreach($laboratories as $key => $row){
										if($row->labID == $currentLabo->labID){
											echo '<option value="'.$row->labID.'" selected>'.$row->name_kh.'</option>';
										}else{
											echo '<option value="'.$row->labID.'">'.$row->name_kh.'</option>';
										}
									}
								}
							?>
                        </select>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-4">
					<label for="ct_start" class="control-label hint--right hint--error hint--always"><?php echo _t('report.start_receive_date');?></label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="criteria_start" name="criteria_start"/>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control narrow-padding" id="start_time" size="10" value="00:00">
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                    </div>
					<div class="valid-feedback"​ id="criteria_start_msg"></div>
				</div>
				<div class="col-sm-4">
					<label for="ct_end" class="control-label hint--right hint--error hint--always"><?php echo _t('report.end_receive_date'); ?></label>
                    <div class="input-group">
                        <input type="text" class="form-control"  id="criteria_end" name="criteria_end"/>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control narrow-padding" id="end_time" size="10" value="23:59">
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                    </div>
					<div class="valid-feedback"​ id="criteria_end_msg"></div>
				</div>

				<div class="col-sm-2">
					<label for="btn"> &nbsp; </label>
					<div class="input-group">
						<button type="button" id="btnGetData" class="btn btn-primary">ទាញទិន្នន័យ</button>						
					</div>					
				</div>
				<div class="col-sm-2">
					<label for="btn"> &nbsp; </label>
					<div class="input-group">
						<button type="button" id="btnExportExcel" class="btn btn-warning">រក្សាទុកទិន្នន័យ</button>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
				<table class="table table-bordered table-striped" id="tbl_result" style="min-width: 100%;">            	
				<thead>
            	<th>Laboratory</th>
				<th>Sample_Type</th>
				<th>Test Type</th>
				<th>Status</th>
				<th>Tested For</th>
				<th>ID</th>
				<th>Code Laboratory</th>
				<th>Health Facility</th>
				<th>Date of Completion</th>
				<th>Completed by</th>
				<th>Telephone</th>
				<th>Reasons for Testing</th>
				<th>Contact of Patient</th>
				<th>Relationship</th>
				<th>Patient Name</th>
				<th>Patient ID</th>
				<th>Passport No</th>
				<th>Sex</th>
				<th>Age</th>
				<th>Nationality</th>
				<th>Patient_Tel</th>
				<th>Occupation</th>
				<th>Occupation2</th>
				<th>Address (house, village, commune, district)</th>
				<th>Province</th>
				<th>District</th>
				<th>Commune</th>
				<th>Village</th>
				<th>Fever (Y/N)</th>
				<th>Cough (Y/N)</th>
				<th>Runny nose (Y/N)</th>
				<th>Sore Throat (Y/N)</th>
				<th>Difficulty Breathing (Y/N)</th>
				<th>No Symptoms (Y/N)</th>
				<th>Date Onset (dd/mm/yyyy)</th>
				<th>Previous Covid (Y/N)</th>
				<th>Date_Prev.Test</th>
				<th>Country/Province</th>
				<th>Date_Arrival  (dd/mm/yyyy)</th>
				<th>Flight No</th>
				<th>Seat No</th>
				<th>Place_Collection</th>
				<th>Date_Collection</th>
				<th>Visit No</th>
				<th>Sample Collector</th>
				<th>Collector_Tel</th>
				<th>Received _Date  (dd/mm/yyyy)</th>
				<th>Testing Date  (dd/mm/yyyy)</th>
				<th>Test Result (Neg/Pos)</th>
				<th>Vaccinated Status</th>
				<th>Vaccines received Vaccine [1]</th>
				<th>Vaccines received Vaccine date [1]</th>
				<th>Vaccines received Vaccine status [1]</th>
				<th>Vaccines received Vaccine [2]</th>
				<th>Vaccines received Vaccine date [2]</th>
				<th>Vaccines received Vaccine status [2]</th>
				<th>Vaccines received Vaccine [3]</th>
				<th>Vaccines received Vaccine date [3]</th>
				<th>Vaccines received Vaccine status [3]</th>
				<th>Vaccines received Vaccine [4]</th>
				<th>Vaccines received Vaccine date [4]</th>
				<th>Vaccines received Vaccine status [4]</th>
				<th>Vaccines received Vaccine [5]</th>
				<th>Vaccines received Vaccine date [5]</th>
				<th>Vaccines received Vaccine status [5]</th>
				<th>Vaccines received Vaccine [6]</th>
				<th>Vaccines received Vaccine date [6]</th>
				<th>Vaccines received Vaccine status [6]</th>
				<th>Types of Test</th>
				<th>Surveillance</th>
				<th>Patient type</th>
				<th>Date Reporting</th>
				<th>Requested by</th>
				<th>NEW Reasons for Testing</th>
			</thead>
            <tbody>				
			</tbody>
		</table>
				</div>
			</div>
		</div>  	
</div>