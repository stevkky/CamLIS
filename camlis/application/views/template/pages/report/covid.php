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
<div class="d-none" style="display: none;">
<?php 
	
	$currentLabo = $this->session->userdata('laboratory');
	$userLabo	=  $this->session->userdata('user_laboratories');
	
?>
</div>
<div class="wrapper col-sm-9"> 
		<div class="form-vertical"> 
			 
			<div class="row">
				<div class="col-sm-4">
					<label for="lab_nameEN" class="control-label hint--right hint--error hint--always"><?php echo _t('report.laboratory'); ?></label>
					<select id="laboratory" class="form-control input-sm" style="width: 135px !important;" name="laboratory[value][]" multiple>
						<?php
							foreach($laboratories as $laboratory) {
								$app_lang	= empty($app_lang) ? 'en' : $app_lang;
								$name = 'name_'.$app_lang;
								if($laboratory->labID == $currentLabo->labID){
									echo "<option value='".$laboratory->labID."' selected>".$laboratory->$name."</option>";
								}else{
									echo "<option value='".$laboratory->labID."'>".$laboratory->$name."</option>";
								}
								
							}
						?>
					</select>
				</div>	
				<!--						
				<div class="col-sm-4">
					<label for="lab_nameEN" class="control-label hint--right hint--error hint--always"><?php echo _t('report.laboratory'); ?></label>
					<div>
						<select class="form-control" name="labo_name" id="labo_name">    
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
				-->				
				

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
			</div>
			
			<div class="row"> 
				<div class="col-sm-3">
					<label for="ct_end" class="control-label hint--right hint--error hint--always"><?php echo _t('report.reason_for_testing'); ?></label>
                    <div class="input-group">
						<?php
							$app_lang	= empty($app_lang) ? 'en' : $app_lang;
							if($app_lang == 'en'){
								$FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY);
							}else{
								$FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
							}							
						?>
						<select class="form-control" name="for_research" id="for_reasearch">
						<option value="all"​ selected>បង្ហាញទាំំងអស់</option>
						<?php 
							for($i = 1 ; $i < count($FOR_RESEARCH_ARR) ; $i++){
								echo '<option value="'.$i.'">'.$FOR_RESEARCH_ARR[$i].'</option>';
							}
						?>
						</select>
                    </div>
				</div> 	
				<!-- Added 27-04-2021-->
				<div class="col-sm-3">
					<label for="sample_source" class="control-label hint--right hint--error hint--always"><?php echo _t('report.sample_source'); ?></label>
					<select class="form-control" name="sample_source" id="sample_source" multiple="multiple">							
						<?php
							foreach($sample_source as $sc) {
								$selected = "";
								if ($sc->source_id == $patient_sample['sample_source_id']) $selected = "selected";
								echo "<option value='$sc->source_id' $selected>$sc->source_name</option>";
							}
						?>
					</select>
				</div>
				<?php //if($_SESSION['roleid'] == 1){	?>
				<div class="col-sm-3">
					<label for="test_name" class="control-label hint--right hint--error hint--always"><?php echo _t('report.test_name'); ?></label>
					<select class="form-control" name="test_name" id="test_name" multiple="multiple">							
						<?php
							$SARSCOV2_DD = unserialize(SARSCOV2_DD);
							foreach($SARSCOV2_DD as $key => $test){
								echo '<option value="'.$key.'">'.$test.'</option>';
							}								
						?>
						
					</select>
				</div>
				<?php //} ?>

				<?php //if($_SESSION['roleid'] == 1){	?>
				<div class="col-sm-3">
					<label for="test_name" class="control-label hint--right hint--error hint--always"><?php echo _t('sample.test_result'); ?></label>
					<select class="form-control" name="test_result" id="test_result" >
						<option value="0">All</option>
						<option value="1">Positive</option>
						<option value="2">Negative</option>
						<option value="3">Invalid</option>
					</select>
				</div>
				<?php //} ?>
			</div>

			<div class="row">
				<div class="col-sm-3">
					<label for="test_start" class="control-label hint--right hint--error hint--always">ថ្ងៃធ្វើតេស្ត</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="test_start_date" name="test_start_date"/>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
					<div class="valid-feedback"​ id="test_start_date_msg"></div>
				</div>
				<div class="col-sm-2">
					<label for="ct_end" class="control-label hint--right hint--error hint--always">&nbsp;</label>
                    <div class="input-group">
                        <input type="text" class="form-control"  id="test_end_date" name="test_end_date"/>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>                        
                    </div>
					<div class="valid-feedback"​ id="test_end_date_msg"></div>
				</div>
				<div class="col-sm-3">
					<label for="test_start" class="control-label hint--right hint--error hint--always">ចន្លោះលេខរៀងសំណាក</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="start_sample_number" name="start_sample_number"/>                     
                    </div>
				</div>
				<div class="col-sm-2">
					<label for="ct_end" class="control-label hint--right hint--error hint--always">&nbsp;</label>
                    <div class="input-group">
                        <input type="text" class="form-control"  id="end_sample_number" name="end_sample_number"/>                                
                    </div>
				</div>
				<div class="col-md-2">
					<label class="control-label"><?php echo _t('sample.number_of_sample'); ?></label>
					<?php
						$NUMBER_OF_SAMPLE_DD = unserialize(NUMBER_OF_SAMPLE_DD);
						echo form_dropdown('number_of_sample', $NUMBER_OF_SAMPLE_DD,'', 'class="form-control"');
					?>
				</div>
			</div>

			<div class="row">
				<input type="hidden" value="<?php echo $app_lang;?>" name="lang" id="lang" />
				<div class="col-sm-12">
					<label for="lab_nameKH" class="control-label hint--right hint--error hint--always">&nbsp;</label>
					<div>
						<button type="button" id="btnSearchCovid" class="btn btn-primary"><?php echo _t('report.filter'); ?>&nbsp;&nbsp;
						<i class="fa fa-search"></i></button>					
						<button type="button" id="btnPrint" disabled class="btn btn-primary"><?php echo _t('report.print'); ?>&nbsp;&nbsp;
						<i class="fa fa-print"></i></button>
						<?php echo form_open(base_url()."report/export_covid_report", 'id="theForm" style="float:right"'); ?>
							<input type="hidden" name="start_date" value="" id="start_date" />
							<input type="hidden" name="end_date" value="" id="end_date" />
							<button type="button" id="btnExportExcel" disabled  class="btn btn-primary">Excel &nbsp;&nbsp;<i class="fa fa-print"></i></button>
							
							
						</form>

					</div>
				</div>
			</div>  
		</div>  
        
    	<div class="row adm_lab_btnWrapper">
            <div class="col-sm-12">
             
                <!-- header logo -->  
                <div id="header" class="printable" style="height:110px; display:none"> 
                    <img style="position:absolute;" src="<?php echo $laboratory_logo_url; ?>" width="77" align="left"/> 
                    <div style="text-align:center;position:relative;"> 
                        <h3 style="color:#558fd5;"><?php echo $laboratoryInfo->name_kh?></h3> 
                        <h4 style="font-size:22px;color:#558fd5;"><?php echo $laboratoryInfo->name_en?></h4> 
                    </div>		 

                </div><!-- end --> 
                  
                  <div class="printable" style="display:none; text-align:center; margin-top:0px; font-size:18px; "> លទ្ធផលមន្ទីរពិសោធន៍អ្នកសង្ស័យ ជំងឺកូវីដ-១៩​
					<h4 style="color:#558fd5;"><?php echo _t('report.start_date');?><span id="spstart"></span>---<?php echo _t('report.end_date');?> <span id="spend"></span> </h4></div>
						
                  <div class="printable" style="overflow-x: auto;">
                    <table class="table table-bordered table-striped table-responsive" id="tbl-result"> 
					<thead>
						<tr>
							<th>#</th>
							<th><?php echo _t('patient.patient_id'); ?></th>
							<th><?php echo _t('patient.patient_name'); ?></th>
							<th><?php echo _t('patient.age'); ?></th>
							<th><?php echo _t('patient.sex'); ?></th>
							<th><?php echo _t('patient.nationality');?></th>
							<th><?php echo _t('patient.passport_no');?></th>
							<th><?php echo _t('patient.flight_number');?></th>
							<th><?php echo _t('patient.date_of_arrival');?></th>
							<th><?php echo _t('sample.sample_source'); ?></th>
							<th><?php echo _t('sample.reason_for_testing'); ?></th>
							<th><?php echo _t('sample.clinical_history'); ?></th>
							<th><?php echo _t('sample.collect_dt'); ?></th>
							<th><?php echo _t('sample.receive_dt'); ?></th>
							<th><?php echo _t('sample.test_date'); ?></th>
							<th><?php echo _t('sample.test_result'); ?></th>
							<th><?php echo _t('sample.sample_number'); ?></th>
							<th><?php echo _t('sample.number_of_sample'); ?></th>
						</tr>
					</thead>					
					<tbody>
						<tr>
						<td colspan="17"></td>						
						</tr>
					</tbody>
                    </table> 
                </div>
            </div>
        </div>
</div>
<script type="text/javascript">
 	window.onload=function(){
		jQuery.fn.extend({
			printElem: function() {
				var cloned = this.clone();
				var printSection = $('#printSection');
				if (printSection.length == 0) {
					printSection = $('<div id="printSection"></div>')
					$('body').append(printSection);
				}
				printSection.append(cloned);
				var toggleBody = $('body *:visible');
				toggleBody.hide();
				$('#printSection, #printSection *').show();
				window.print();
				printSection.remove();
				toggleBody.show();
			}
		});
	//
	$(document).ready(function(){
		$(document).on('click', '#btnPrint', function(){
			//
			$('#spstart').text($('#criteria_start').val());
			$('#spend').text($('#criteria_end').val());
			// 
			$('.printable').printElem();
	  });
	//  $("#tbl-result").DataTable();
	});
}
  
  
	// When the document is ready
	$(document).ready(function () {
		$("select[name=sample_source], select[name=test_name], select[name=labo_name], select[name=labo_names]").select2();
		var dtPickerOption = {
			widgetPositioning : {
				horizontal	: 'left',
				vertical	: 'bottom'
			},
			showClear		: true,
			format			: 'DD/MM/YYYY',
			useCurrent		: false, 
			locale			: app_lang == 'kh' ? 'km' : 'en'
		};
		$("#criteria_start").datetimepicker(dtPickerOption).on("dp.change", function(e) {
			var dob = $(this).data("DateTimePicker").date(); 
			//console.log(dob);
			//console.log(e.target.value);
		//	var start_time = $("#start_time").val();
		//	var start_time = moment($("#start_time").val(), "HH:mm");
        //    var end_time   = moment($("#end_time").val(), "HH:mm");
		//	var start_time = start_time.format("HH:mm");
		//var end_time   = end_time.format("HH:mm");

			//console.log(start_time+" "+end_time);
			$("#start_date").val(e.target.value);
		}); 
		
		$("#criteria_end").datetimepicker(dtPickerOption).on("dp.change", function(e) {
			var dob = $(this).data("DateTimePicker").date(); 
			$("#end_date").val(e.target.value);
		});

        $("#start_time, #end_time").timepicker({minuteStep: 1, showMeridian: false});
		//added 31-03-2021
		$("#test_start_date").datetimepicker(dtPickerOption).on("dp.change", function(e) {
		}); 
		$("#test_end_date").datetimepicker(dtPickerOption).on("dp.change", function(e) {			
		}); 

	}); // interface
	

</script>
    