<?php
	$app_lang	= empty($app_lang) ? 'en' : $app_lang;

	$FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
	
	if($app_lang == 'en'){
		$REASON_FOR_TESTING_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY);		
		$VACCINATION_STATUS_DD = unserialize(VACCINATION_STATUS_DD_EN);
	}else{
		$REASON_FOR_TESTING_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
		$VACCINATION_STATUS_DD = unserialize(VACCINATION_STATUS_DD_KH);
	}
	
?>
<script>
    const TEST_PAYMENTS         = <?php echo json_encode($test_payments); ?>;
	var pid						= null;
	var patient_info            = null;
	var page_action             = null;
    var sample_descriptions     = JSON.parse('<?php echo json_encode($sample_descriptions); ?>');
	var msg_choose_testDt		= "<?php echo _t('sample.msg.choose_testDt');				?>";
	var msg_required_data		= "<?php echo _t('global.msg.fill_required_data');			?>";
	var msg_must_save_sample	= "<?php echo _t('sample.msg.must_save_sample');			?>";
	var msg_must_assign_test	= "<?php echo _t('sample.msg.must_assign_test');			?>";
	var msg_must_select_test	= "<?php echo _t('sample.msg.must_select_test');			?>";
	var msg_must_select_sample	= "<?php echo _t('sample.msg.choose_sample');				?>";
	var msg_save_success		= "<?php echo _t('global.msg.save_success');				?>";
	var msg_save_fail			= "<?php echo _t('global.msg.save_fail');					?>";
	var msg_col_rec_dt_error	= "<?php echo _t('sample.msg.col_rec_dt_error');			?>";
	var msg_reject_cmt_err		= "<?php echo _t('global.msg.fill_required_data');			?>";
	var msg_dob_not_after_now	= "<?php echo _t('sample.msg.dob_not_after_now');			?>";
	var msg_test_rejected		= "<?php echo _t('sample.msg.test_is_reject');				?>";
	var msg_choose_sample_test	= "<?php echo _t('sample.msg.must_choose_sample_test');	?>";
	var msg_col_dt_vs_now		= "<?php echo _t('sample.msg.col_dt_vs_now');				?>";
	var msg_rec_dt_vs_now		= "<?php echo _t('sample.msg.rec_dt_vs_now');				?>";
	var label_print_result		= "<?php echo _t('sample.preview_result');					?>";
	var label_department		= "<?php echo _t('global.department');						?>";
	var label_sample			= "<?php echo _t('sample.sample_type');					?>";
	var label_no_data			= "<?php echo _t('global.no_data');						?>";
	var label_choose_performer	= "<?php echo _t('sample.choose_performer');				?>";
	var label_sample_desription	= "<?php echo _t('sample.sample_desc');				    ?>";
    var label_weight1       	= "<?php echo _t('sample.weight1');				        ?>";
    var label_weight2	        = "<?php echo _t('sample.weight2');				        ?>";
	var msg_loading				= "<?php echo _t('global.plz_wait');						?>";
	var msg_saving				= "<?php echo _t('global.saving');							?>";
	var q_delete_patient_sample	= "<?php echo _t('sample.q.delete_patient_sample');		?>";
	var msg_delete_fail			= "<?php echo _t('global.msg.delete_fail');				?>";
    var label_year              = "<?php echo _t('global.year'); ?>";
    var label_month             = "<?php echo _t('global.month'); ?>";
	var label_day               = "<?php echo _t('global.day'); ?>";
	
	var label_patient_id		= "<?php echo _t('patient.patient_id'); ?>";
	var label_sex				= "<?php echo _t('global.patient_gender'); ?>";
	var label_patient_name		= "<?php echo _t('patient.name'); ?>";
	var label_patient_phone_number = "<?php echo _t('global.patient_phone_number'); ?>";
	var label_patient_age		= "<?php echo _t('global.patient_age'); ?>";
	var label_test_date				= "<?php echo _t('sample.test_date'); ?>";

	var label_residence							= "<?php echo _t('patient.residence'); ?>";	
	var app_language							= "<?php echo $app_lang; ?>";	
	var label_nationality						= "<?php echo _t('patient.nationality'); ?>";
	var label_country							= "<?php echo _t('patient.country'); ?>";
	var label_nationality						= "<?php echo _t('patient.nationality'); ?>";
	var label_completed_by						= "<?php echo _t('patient.completed_by'); ?>";
	var label_clinical_symtom					= "<?php echo _t('patient.clinical_symtom'); ?>";
	var label_history_of_covid19_history		= "<?php echo _t('patient.history_of_covid19_history'); ?>";
	var label_test_covid_date					= "<?php echo _t('patient.test_date'); ?>";
	var label_travel_history					= "<?php echo _t('patient.travel_history'); ?>";
	var label_date_of_arrival					= "<?php echo _t('patient.date_of_arrival'); ?>";
	var label_passport_no						= "<?php echo _t('patient.passport_no'); ?>";
	var label_seat_no							= "<?php echo _t('patient.seat_no'); ?>";
	var label_if_contact						= "<?php echo _t('patient.if_contact'); ?>";
	var label_contact_with						= "<?php echo _t('patient.contact_with'); ?>";
	var label_sample_collector					= "<?php echo _t('patient.sample_collector'); ?>";
	var label_flight_number						= "<?php echo _t('patient.flight_number'); ?>";	
	var label_reason_for_testing				= "<?php echo _t('sample.reason_for_testing'); ?>";
	var label_sample_source						= "<?php echo _t('sample.sample_source'); ?>";
	var label_sample_number						= "<?php echo _t('sample.sample_number'); ?>";
	var label_urgent							= "<?php echo _t('sample.urgent'); ?>";
	var label_requester							= "<?php echo _t('sample.requester'); ?>";
	var label_collect_dt						= "<?php echo _t('sample.collect_dt'); ?>";
	var label_receive_dt						= "<?php echo _t('sample.receive_dt'); ?>";
	var label_payment_type						= "<?php echo _t('sample.payment_type'); ?>";
	var label_clinical_history					= "<?php echo _t('sample.clinical_history'); ?>";
	var label_phone 							= "<?php echo _t('patient.phone'); ?>";
	var label_test_name 						= "<?php echo _t('sample.test_name'); ?>";
	var label_patient_info 						= "<?php echo _t('patient.patient_information'); ?>";
	var label_sample_info 						= "<?php echo _t('sample.sample_info'); ?>";
	var label_sample							= "<?php echo _t('sample.sample'); ?>";
	var label_address 							= "<?php echo _t('patient.address'); ?>";
	var label_number_of_sample					= "<?php echo _t('sample.number_of_sample'); ?>";
	var label_performed_by						= "<?php echo _t('sample.performed_by'); ?>";
	var label_result							= "<?php echo _t('sample.result'); ?>";
	var label_province							= "<?php echo _t('patient.province'); ?>";
	var label_district							= "<?php echo _t('patient.district'); ?>";
	var label_commune							= "<?php echo _t('patient.commune'); ?>";
	var label_village							= "<?php echo _t('patient.village'); ?>";
	var label_test_result						= "<?php echo _t('sample.test_result'); ?>";
	
	var label_print								= "<?php echo _t('global.print'); ?>";
	var label_yes								= "<?php echo _t('patient.yes'); ?>";
	var label_is_direct_contact					= "<?php echo _t('patient.is_direct_contact'); ?>";
	var label_patient							= "<?php echo _t('patient.patient'); ?>";
	var label_if_contact						= "<?php echo _t('patient.if_contact'); ?>";
	var label_health_facility					= "<?php echo _t('sample.health_facility'); ?>";
	var label_machine_name						= "<?php echo _t('sample.machine_name'); ?>";
	var label_print_lab_form					= "<?php echo _t('sample.preview_request'); ?>";
	var label_qr_code							= "<?php echo _t('sample.qr_code'); ?>";	
	
	var msg = {	
		"not_fill" 						: "<?php echo _t('global.not_fill'); ?>",
		"not_select" 					: "<?php echo _t('global.not_select'); ?>",
		"not_greater_than_3" 			: "<?php echo _t('global.not_greater_than_3'); ?>",
		"not_correct_format" 			: "<?php echo _t('global.not_correct_format'); ?>",
		"not_greater_than_100" 			: "<?php echo _t('global.not_greater_than_100'); ?>",
		"not_greater_than_60" 			: "<?php echo _t('global.not_greater_than_60'); ?>",
		"month_not_greater_than_12" 	: "<?php echo _t('global.month_not_greater_than_12'); ?>",
		"day_not_greater_than_31" 		: "<?php echo _t('global.day_not_greater_than_31'); ?>",
		"not_data_entry" 				: "<?php echo _t('global.not_data_entry'); ?>",
		"not_greater_than_100_row" 		: "<?php echo _t('global.not_greater_than_100_row'); ?>",
		"not_greater_than"				: "<?php echo _t('global.not_greater_than'); ?>",
		"char"							: "<?php echo _t('global.char'); ?>",
		"not_greater_than_current_date"	: "<?php echo _t('global.not_greater_than_current_date'); ?>",
		"not_correct"					: "<?php echo _t('global.not_correct'); ?>",
		"select_sample_source_first"	: "<?php echo _t('global.select_sample_source_first'); ?>",
		"select_province_first"			: "<?php echo _t('global.select_province_first'); ?>",
		"select_district_first"			: "<?php echo _t('global.select_district_first'); ?>",
		"select_commune_first"			: "<?php echo _t('global.select_commune_first'); ?>",
		"select_test_name"				: "<?php echo _t('global.select_test_name'); ?>",
		"excel_none_data_check_again"	: "<?php echo _t('global.excel_none_data_again'); ?>",
		"data_over_max_only_500_added"  : "<?php echo _t('global.data_over_max_only_500_added'); ?>",
		"data_inserted_successful"		: "<?php echo _t('global.data_inserted_successful'); ?>",
	}
	const PROVINCES            = <?php echo json_encode($provinces); ?>;
	
	// ADDED 22-03-2021 FOR LINE LIST
	const DISTRICTS            = <?php echo json_encode($districts); ?>;
	const COMMUNES             = <?php echo json_encode($communes); ?>;
	const VILLAGES             = <?php echo json_encode($villages); ?>;
	const NATIONALITIES        = <?php echo json_encode($nationalities); ?>;
	const COUNTRIES        	   = <?php echo json_encode($countries); ?>;
	
	const REASON_FOR_TESTING   = <?php echo json_encode($FOR_RESEARCH_ARR);?>;
	const CLINICAL_SYMPTOM 	   = <?php echo json_encode($clinical_symptoms);?>;
	const SAMPLE_SOURCE        = <?php echo json_encode($sample_source);?>;
	const PAYMENT_TYPE 		   = <?php echo json_encode($payment_types);?>;
	const REQUESTER 		   = <?php echo json_encode($requester);?>;
	const is_admin			   = "<?php echo $_SESSION['roleid']; ?>";
	const REASON_FOR_TESTING_ARR   = <?php echo json_encode($REASON_FOR_TESTING_ARR);?>;
	/**13072021 */
	var label_vaccination_status	= "<?php echo _t('patient.vaccination_status'); ?>";
	var label_vaccine_type			= "<?php echo _t('patient.vaccine_type'); ?>";
	var label_occupation			= "<?php echo _t('patient.occupation'); ?>";
	var label_covid_questionaire	= "<?php echo _t('sample.covid_questionaire'); ?>";
	var label_first_injection_date	= "<?php echo _t('patient.first_injection_date'); ?>";
	var label_second_injection_date	= "<?php echo _t('patient.second_injection_date'); ?>";
	var label_third_injection_date	= "<?php echo _t('patient.third_injection_date'); ?>";
	var label_covid_questionaire	= "<?php echo _t('sample.covid_questionaire'); ?>";
	const VACCINATION_STATUS_ARR = <?php echo json_encode($VACCINATION_STATUS_DD);?>;
	const VACCINE_TYPE_ARR 		= <?php echo json_encode($vaccines);?>;	
	var label_forth_injection_date	= "<?php echo _t('patient.forth_injection_date'); ?>"; //09022022
	// end
</script>

<?php if(!empty($performers)){ ?>
	<script> const PERFORMERS = <?php echo json_encode($performers);?>; </script>
<?php } ?>

<div class="col-sm-12">
	<div class="form-vertical" style="border: 2px solid #d7d7d7; padding: 12px;">
		<div class="row">
			<div class="col-sm-4">
				<label class="control-label">
					<?php echo _t('patient.find_patient_id'); ?>
				</label>
                <form id="frm-search-patient">
                    <div class="input-group">
                        <input type="text" name="search_patient_id" id="search_patient_id" class="form-control" placeholder="<?php echo _t('patient.patient_id'); ?>" value="<?php echo $patient_id; ?>" autocomplete="off" autofocus>
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-primary" id="btnSearchPatient"><?php echo _t('global.search'); ?>&nbsp;<i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
			</div>
			<div class="col-sm-1">
				<label class="control-label"> </label>
				<div style='padding-top:10px; font-weight:bold;' class="text-center"><?php echo _t('sample.or'); ?></div>
			</div>
			<div class="col-sm-2">
				<label class="control-label">&nbsp;</label>
				<div>
					<button type="button" class="btn btn-primary" id="btnNewPatient"><i class="fa fa-user-plus"></i>&nbsp;
						<?php echo _t('patient.new_patient'); ?>
					</button>
				</div>
			</div>
			<!-- Add Line list 
			Date:19-03-2021
			-->
			<?php 
			//	if($_SESSION['roleid'] == 1){
			?>
			<div class="col-sm-2">
				<label class="control-label">&nbsp;</label>
				<div>
					<button type="button" class="btn btn-info" id="btnAddPatientsNew"><i class="fa fa-user-plus"></i>&nbsp;
						<?php echo _t('patient.excel_full_form'); ?>
					</button>
				</div>
			</div>
			<?php
			//	}
			?>
			<!-- Add Line list For Dev
			Date: 02-04-2021
			-->
			<?php 
			//	if($_SESSION['roleid'] == 1){
			?>
			<div class="col-sm-3">
				<label class="control-label">&nbsp;</label>
				<div>
					<button type="button" class="btn btn-success" id="btnExcelShortForm"><i class="fa fa-user-plus"></i>&nbsp;						
						<?php echo _t('patient.excel_short_form'); ?>
					</button>					
				</div>
			</div>
			<?php
				//}
			?>
		</div>
	</div>

	<!-- No patient result -->
	<div class="well text-center text-red" id="no-result" style="display:none;"><b><?php echo _t('global.no_result'); ?></b></div>
	<div id="patient-info-wrapper">
		<!-- Patient's Info View -->
		<div id="patient-info-view" style="display: none;">
            <div class="row">
                <div class="col-sm-2">
                    <label class="control-label"><?php echo _t('patient.patient_id'); ?> :</label>
                </div>
                <div class="col-sm-10 patient-code"></div>
            </div>
			<div class="row">
				<div class="col-sm-2">
					<label class="control-label"><?php echo _t('patient.patient_name'); ?> :</label>
				</div>
				<div class="col-sm-10 patient-name"></div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<label class="control-label"><?php echo _t('patient.sex'); ?> :</label>
				</div>
				<div class="col-sm-10 gender"></div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<label class="control-label"><?php echo _t('patient.age'); ?> :</label>
				</div>
				<div class="col-sm-10 patient-age">
					<span class="age-year"></span> <?php echo _t('global.year') ?> &nbsp;
					<span class="age-month"></span> <?php echo _t('global.month') ?> &nbsp;
					<span class="age-day"></span> <?php echo _t('global.day') ?>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<label class="control-label"><?php echo _t('patient.phone'); ?> :</label>
				</div>
				<div class="col-sm-10 phone"></div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<label class="control-label"><?php echo _t('patient.address'); ?> :</label>
				</div>
				<div class="col-sm-10 address">
					<span class="address-residence"></span> <!-- added 02 Dec 2020 -->
					<span class="address-village"></span> <?php echo _t('Village').' -'; ?> &nbsp;
					<span class="address-commune"></span> <?php echo _t('Commune').' -'; ?> &nbsp;
					<span class="address-district"></span> <?php echo _t('District').' -'; ?>
					<span class="address-province"></span> <?php echo _t('Province') ?>
				</div>
			</div>
			<!-- Added: 02 Dec 2020-->
			<div class="hidden">
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.country'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="country"></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.nationality'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="nationality"></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.date_of_arrival'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="date_arrival"></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.passport_no'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="passport_number"></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.flight_number'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="flight_number"></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.seat_no'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="seat_number"></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.history_of_covid19_history'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="is_positive_covid">, <?php echo _t('patient.test_date'); ?>: </span>
					<span class="test_date"></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.if_contact'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="is_contacted"></span>
					<span class="contact_with"></span>
					<span class="relationship_with_case"></span>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.describe_in_past_30_days'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="travel_in_past_30_days"></span>
				</div>
			</div>
		</div>
		</div>
		<!-- End -->
		<!-- Patient Form -->
		<form id="patient-info-form" class="well form-vertical" style="display: none;">
			<div class="row">
				<div class="col-sm-2">
					<label class="control-label">
						<?php echo _t('patient.patient_id'); ?>
					</label>
					<input type="text" name="patient_manual_code" id="patient-manual-code" class="form-control">
				</div>
                <div class="col-sm-2">
                    <label class="control-label">
                        <?php echo _t('patient.patient_name'); ?>
                    </label>
                    <input type="text" name="patient_name" id="patient_name" class="form-control">
                </div>

				<div class="col-sm-6">
					<label for="patient_dob" class="control-label hint--right hint--error hint--always">
						<?php echo _t('patient.dob').' / '._t('global.age'); ?>
					</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="glyphicon glyphicon-calendar"></i></span>
                        <input type="text" class="form-control" id="patient_dob" size="40">
                        <span class="input-group-addon" id="basic-addon1"><b><?php echo _t('global.year'); ?></b></span>
                        <input type="number" class="form-control" id="patient-age-year" placeholder="<?php echo _t('global.year'); ?>" onkeypress="return isNumber(event);" maxlength="2" onfocus="this.select()">
                        <span class="input-group-addon" id="basic-addon1"><?php echo _t('global.month'); ?></span>
                        <input type="number" class="form-control" id="patient-age-month" maxlength="2" placeholder="<?php echo _t('global.month'); ?>" onkeypress="return isNumber(event);" onfocus="this.select()">
                        <span class="input-group-addon" id="basic-addon1"><?php echo _t('global.day'); ?></span>
                        <input type="number" class="form-control" id="patient-age-day" maxlength="2" placeholder="<?php echo _t('global.day'); ?>" onkeypress="return isNumber(event);" onfocus="this.select()">
                    </div>
				</div>
                <div class="col-sm-2">
                    <label class="control-label">
                        <?php echo _t('patient.sex'); ?>
                    </label>
                    <div>
                        <label class="control-label" style="cursor:pointer;">
                            <input type="radio" name='patient_sex' value="1">&nbsp;
                            <?php echo _t('global.male'); ?>
                        </label>
                        &nbsp;&nbsp;
                        <label class="control-label" style="cursor:pointer;">
                            <input type="radio" name='patient_sex' value="2">&nbsp;
                            <?php echo _t('global.female'); ?>
                        </label>
                    </div>
                </div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<label class="control-label">
						<?php echo _t('patient.phone'); ?>
					</label>
					<input type="text" class="form-control" name="phone" id="phone">
				</div>
				
				<div class="col-sm-8">
					<label class="control-label">
						<?php echo _t('patient.address'); ?>
					</label>
					<div class="col-sm-12" style="padding:0;">
						<div class="col-sm-3" style="padding-left:0;">
							<select name="province" id="province" class="form-control" data-get="district">
								<option value="-1" style="color:#d8d5d5;">
									<?php echo _t('global.choose_province'); ?>
								</option>
								<?php
									foreach($provinces as $pro) {
										$app_lang	= empty($app_lang) ? 'en' : $app_lang;
										$name		= 'name_'.$app_lang;
										echo "<option value='".$pro->code."'>".$pro->$name."</option>";
									}
								?>
							</select>
						</div>
						<div class="col-sm-3" style="padding-left:0;">
							<select name="district" id="district" class="form-control" data-get="commune">
								<option value="-1" style="color:#d8d5d5;">
									<?php echo _t('global.choose_district'); ?>
								</option>
							</select>
						</div>
						<div class="col-sm-3" style="padding-left:0;">
							<select name="commune" id="commune" class="form-control" data-get="village">
								<option value="-1" style="color:#d8d5d5;">
									<?php echo _t('global.choose_commune'); ?>
								</option>
							</select>
						</div>
						<div class="col-sm-3" style="padding-left:0; padding-right:0">
							<select name="village" id="village" class="form-control">
								<option value="-1" style="color:#d8d5d5;">
									<?php echo _t('global.choose_village'); ?>
								</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<!-- Update Covid-19 Form -->
			<!-- Date: 02 Dec 2020 -->
			<div class="row">
				<div class="col-sm-12">
					<button type="button" class="btn btn-success" data-toggle="collapse" data-target="#demo"><?php echo _t('sample.covid_questionaire'); ?></button>					
				</div>
			</div>
			<div class="collapse" id="demo">
			<div class="row">
				<div class="col-sm-4">
					<label class="control-label">
						<?php echo _t('patient.residence'); ?>
					</label>
					<input type="text" class="form-control" name="residence" id="residence">
				</div>
				<div class="col-sm-4">
					<label class="control-label">
						<?php echo _t('patient.country'); ?>
					</label>
					<input type="text" class="form-control" name="country_name" id="country_name">
					<!--
					<select name="country" id="country" class="form-control" data-get="country">
						<option value="" style="color:#d8d5d5;">
							<?php echo _t('patient.choose_country'); ?>
					</option>
					<?php
					/*
						foreach($countries as $con) {
							echo "<option value='".$con->num_code."'>".$con->name_en."</option>";
						}
					*/
					?>
					</select>
					-->
				</div>
				<div class="col-sm-4">
					<label class="control-label">
						<?php echo _t('patient.nationality'); ?>
					</label>
					<select name="nationality" id="nationality" class="form-control" data-get="nationality">
						<option value="" style="color:#d8d5d5;">
							<?php echo _t('patient.choose_nationality'); ?>
						</option>
						<?php
						foreach($nationalities as $nat) {
							echo "<option value='".$nat->num_code."'>".$nat->nationality_en."</option>";
						}
					?>	
					</select>
				</div>
			</div>			

			<div class="row">
				<div class="col-md-3">
					<label class="control-label">
						<?php echo _t('patient.date_of_arrival'); ?></label>
					<div class="input-group">
						<input type="text" class="form-control dtpicker" name="date_arrival" tabindex="6" id="date_arrival">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
				<div class="col-sm-3">
					<label class="control-label">
						<?php echo _t('patient.passport_no'); ?>
					</label>
					<input type="text" class="form-control" name="passport_number" id="passport_number">
				</div>
				<div class="col-sm-3">
					<label class="control-label">
						<?php echo _t('patient.flight_number'); ?>
					</label>
					<input type="text" class="form-control" name="flight_number" id="flight_number">
				</div>
				<div class="col-sm-3">
					<label class="control-label">
						<?php echo _t('patient.seat_no'); ?>
					</label>
					<input type="text" class="form-control" name="seat_number" id="seat_number">
				</div>
			</div>
			
			<!-- 12-07-2021 -->
			<div class="row">
				<div class="col-sm-3">
					<label class="control-label">
						<?php echo _t('patient.vaccination_status'); ?>
					</label>
					<select name="vaccination_status" id="vaccination_status" class="form-control" data-get="nationality">
						<option value="-1" style="color:#d8d5d5;">
							<?php echo _t('global.choose'); ?>
						</option>
						<?php
						foreach($VACCINATION_STATUS_DD as $key => $value) {
							echo "<option value='".$key."'>".$value."</option>";
						}
						?>	
					</select>
				</div>
				<div class="col-sm-3">
					<label class="control-label">
						<?php echo _t('patient.first_injection_date'); ?></sup></label>
					<div class="input-group">
						<input type="text" class="form-control dtpicker" name="first_vaccinated_date" id="first_vaccinated_date" disabled>
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
				<div class="col-sm-3">
					<label class="control-label">
						<?php echo _t('patient.second_injection_date'); ?></sup></label>
					<div class="input-group">
						<input type="text" class="form-control dtpicker" name="second_vaccinated_date" id="second_vaccinated_date" disabled>
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
				<div class="col-md-3">
					<label class="control-label">
						<?php echo _t('patient.vaccine_type'); ?>
					</label>
					<select name="vaccine_id" id="vaccine_id" class="form-control" data-get="vaccine" disabled>
						<option value="-1" style="color:#d8d5d5;">
							<?php echo _t('global.choose'); ?>
						</option>
					<?php
						foreach($vaccines as $item) {
							echo "<option value='".$item->id."'>".$item->name."</option>";
						}
					?>
					</select>
				</div>

				<div class="col-sm-3">
					<label class="control-label">
						<?php echo _t('patient.third_injection_date'); ?></sup></label>
					<div class="input-group">
						<input type="text" class="form-control dtpicker" name="third_vaccinated_date" id="third_vaccinated_date" disabled>
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
				<div class="col-md-3">
					<label class="control-label">
						<?php echo _t('patient.vaccine_type'); ?>
					</label>
					<select name="second_vaccine_id" id="second_vaccine_id" class="form-control" data-get="vaccine" disabled>
						<option value="-1" style="color:#d8d5d5;">
							<?php echo _t('global.choose'); ?>
						</option>
					<?php
						foreach($vaccines as $item) {
							echo "<option value='".$item->id."'>".$item->name."</option>";
						}
					?>
					</select>
				</div>	
				<!-- Date: 27022022
					Updated 4 doses 
				-->
				<div class="col-sm-3">
					<label class="control-label">
						<?php echo _t('patient.forth_injection_date'); ?></sup></label>
					<div class="input-group">
						<input type="text" class="form-control dtpicker" name="forth_vaccinated_date" id="forth_vaccinated_date" disabled>
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
				<div class="col-md-3">
					<label class="control-label">
						<?php echo _t('patient.vaccine_type'); ?>
					</label>
					<select name="third_vaccine_id" id="third_vaccine_id" class="form-control" data-get="vaccine" disabled>
						<option value="-1" style="color:#d8d5d5;">
							<?php echo _t('global.choose'); ?>
						</option>
					<?php
						foreach($vaccines as $item) {
							echo "<option value='".$item->id."'>".$item->name."</option>";
						}
					?>
					</select>
				</div>
				<!-- End -->
				<div class="col-sm-12">
					<label class="control-label">
						<?php echo _t('patient.occupation'); ?></sup></label>					
						<input type="text" class="form-control" name="occupation" id="occupation" />					
				</div>
			</div>
			<!-- End -->

			<div class="row">
				<div class="col-sm-3">
					<label class="control-label"><?php echo _t('patient.history_of_covid19_history'); ?></label>
					<div>
						<label class="control-label" style="cursor:pointer;">
                            <input type="checkbox" name="is_positive_covid" id="is_positive_covid" >&nbsp;
                            <?php echo _t('patient.yes'); ?>
                        </label>
					</div>
				</div>
				<div class="col-sm-2">
					&nbsp;
					<div class="hidden test_date_wrapper">
						<label class="control-label">
							<?php echo _t('patient.test_date'); ?></sup></label>
						<div class="input-group">
							<input type="text" class="form-control dtpicker" name="test_date" tabindex="6" id="test_date">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
					</div>
				</div>

				<div class="col-sm-2">
					<label class="control-label"><?php echo _t('patient.if_contact'); ?></label>
					<div>
						<label class="control-label" style="cursor:pointer;">
                            <input type="checkbox" name="is_contacted" id="is_contacted">&nbsp;
                            <?php echo _t('patient.yes'); ?>
                        </label>
					</div>
				</div>
				<div class="col-sm-5">
					&nbsp;
                    <div class="col-sm-12 hidden contact_wrapper">
						<div class="col-sm-6">
							<label class="control-label">
								<?php echo _t('patient.contact_with'); ?>
							</label>
							<input type="text" class="form-control" name="contact_with" id="contact_with">
						</div>
						<!-- Hide it due to requirement changed-->
						<div class="col-sm-6 hidden">
							<label class="control-label">
								<?php echo _t('patient.relationship_with_case'); ?>
							</label>
							<input type="text" class="form-control" name="relationship_with_case" id="relationship_with_case">
						</div>
						<!-- End -->
						<div class="col-sm-6">
							<label class="control-label">
								<?php echo _t('patient.type_of_contact'); ?>
							</label>
							<div>
								<label class="control-label" style="cursor:pointer;">
									<input type="radio" name='is_direct_contact' value="true">&nbsp;
									<?php echo _t('patient.direct'); ?>
								</label>
								&nbsp;&nbsp;
								<label class="control-label" style="cursor:pointer;">
									<input type="radio" name='is_direct_contact' value="false">&nbsp;
									<?php echo _t('patient.indirect'); ?>
								</label>
							</div>
						</div>					
					</div>
                </div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<label class="control-label">
						<?php echo _t('patient.describe_in_past_30_days'); ?>
					</label>					
					<textarea class="form-control" rows="1" name="travel_in_past_30_days" id="travel_in_past_30_days"></textarea>
				</div>
			</div>										
			<!-- End -->
			</div> <!-- End collapse-->
			<div class="row" style="margin-top:30px;">
				<div class="col-sm-12" style="text-align: right;">
					<button type="button" class="btn btn-primary" id="btnSavePatient"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo _t('global.save'); ?></button>
					<button type="button" class="btn btn-default" id="btnCancelPatient"><i class="fa fa-remove"></i>&nbsp;<?php echo _t('global.cancel'); ?></button>
				</div>
			</div>
		</form>
		<input type="hidden" id="patient-id" data-value="">
        <input type="hidden" id="patient-age" data-value="">
        <input type="hidden" id="patient-sex" data-value="">
		<input type="hidden" id="is-camlis-patient" data-value=""> 
	</div>

	<!-- Sample Forms -->
	<div id="sample-form-wrapper" style="display:none; margin-top: 40px;">
		<h4 class="content-header"><?php echo _t('sample.new_sample'); ?></h4>
		<hr>
		<div id="sample-forms">
			<button type="button" id="btnMore" class="btn btn-flat btn-primary col-sm-12"><i class="fa fa-plus"></i>
				<?php echo _t('sample.add_sample'); ?>
			</button>
		</div>		
	</div>	
</div>