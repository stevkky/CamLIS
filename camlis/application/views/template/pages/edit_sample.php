<script>
    const TEST_PAYMENTS         = <?php echo json_encode($test_payments); ?>;
    const PATIENT_SAMPLE        = <?php echo json_encode($patient_sample); ?>;
    var patient_info            = <?php echo json_encode($patient); ?>;
    var sample_descriptions     = <?php echo json_encode($sample_descriptions); ?>;
    var page_action             = "<?php echo $page_action ?>";
	var msg_choose_testDt		= "<?php echo _t('sample.msg.choose_testDt');				?>";
	var msg_required_data		= "<?php echo _t('global.msg.fill_required_data');			?>";
	var msg_must_save_sample	= "<?php echo _t('sample.msg.must_save_sample');			?>";
	var msg_must_assign_test	= "<?php echo _t('sample.msg.must_assign_test');			?>";
	var msg_must_select_test	= "<?php echo _t('sample.msg.must_select_test');			?>";
	var msg_must_select_sample	= "<?php echo _t('sample.msg.choose_sample');				?>";
	var msg_col_rec_dt_error	= "<?php echo _t('sample.msg.col_rec_dt_error');			?>";
	var msg_reject_cmt_err		= "<?php echo _t('global.msg.fill_required_data');			?>";
	var msg_dob_not_after_now	= "<?php echo _t('sample.msg.dob_not_after_now');			?>";
	var msg_test_rejected		= "<?php echo _t('sample.msg.test_is_reject');				?>";
	var msg_choose_sample_test	= "<?php echo _t('sample.msg.must_choose_sample_test');		?>";
	var msg_col_dt_vs_now		= "<?php echo _t('sample.msg.col_dt_vs_now');				?>";
	var msg_rec_dt_vs_now		= "<?php echo _t('sample.msg.rec_dt_vs_now');				?>";
	var label_print_result		= "<?php echo _t('sample.preview_result');					?>";
	var label_department		= "<?php echo _t('global.department');						?>";
	var label_sample			= "<?php echo _t('sample.sample_type');						?>";
	var label_no_data			= "<?php echo _t('global.no_data');							?>";
	var label_choose_performer	= "<?php echo _t('sample.choose_performer');				?>";
    var label_sample_desription	= "<?php echo _t('sample.sample_desc');				        ?>";
    var label_weight1       	= "<?php echo _t('sample.weight1');				            ?>";
    var label_weight2	        = "<?php echo _t('sample.weight2');				            ?>";
	var msg_loading				= "<?php echo _t('global.plz_wait');						?>";
	var msg_saving				= "<?php echo _t('global.saving');							?>";
	var msg_save_fail			= "<?php echo _t('global.msg.save_fail');					?>";
	var q_delete_patient_sample	= "<?php echo _t('sample.q.delete_patient_sample');			?>";
	var msg_delete_fail			= "<?php echo _t('global.msg.delete_fail');					?>";
	var label_year              = "<?php echo _t('global.year'); ?>";
	var label_month             = "<?php echo _t('global.month'); ?>";
	var label_day               = "<?php echo _t('global.day'); ?>";
</script>
<?php if (!isset($patient_sample) || !$patient_sample || !isset($patient) || !$patient) { ?>
	<div class="col-sm-12">
		<div class="well text-center text-red" id="no-result"><b><?php echo _t('global.no_result'); ?></b></div>
	</div>
<?php } else { ?>
<div class="hidden">
<?php 
print_r($patient_sample);
?>
</div>

<div class="col-sm-12">
	<div id="patient-info-wrapper">
		<!-- View Patient's Info -->
		<div id="patient-info-view">
            <div class="row">
                <div class="col-sm-2">
                    <label class="control-label"><?php echo _t('patient.patient_id'); ?> :</label>
                </div>
                <div class="col-sm-10 patient-code">
                    <?php echo $patient['patient_code']; ?>
                </div>
            </div>
			<div class="row">
				<div class="col-sm-2">
					<label class="control-label"><?php echo _t('patient.patient_name'); ?> :</label>
				</div>
				<div class="col-sm-10 patient-name">
					<?php echo $patient['name']; ?>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<label class="control-label"><?php echo _t('patient.sex'); ?> :</label>
				</div>
				<div class="col-sm-10 gender" data-gender="<?php echo $patient['sex']?>">
					<?php echo (trim($patient['sex']) == 'F' || trim($patient['sex']) == 2) ? _t('global.female') : _t('global.male'); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<label class="control-label"><?php echo _t('patient.age'); ?> :</label>
				</div>
				<div class="col-sm-10 patient-age">
                    <?php
						$current_date = date('Y-m-d');
                        $age = calculateAge($patient['dob']);
						$current_date = date('Y-m-d');// $current_date ? DateTime::createFromFormat('Y-m-d', $current_date) : new DateTime();
                    ?>
                    <span class="age-year" data-currentdate='<?php echo @$current_date; ?>'></span> <?php echo @$age->y.' '._t('global.year') ?> &nbsp;
                    <span class="age-month"></span> <?php echo $age->m.' '._t('global.month') ?> &nbsp;
                    <span class="age-day"></span> <?php echo ($age->days > 0 ? $age->d : 1).' '._t('global.day') ?>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<label class="control-label"><?php echo _t('patient.phone'); ?> :</label>
				</div>
				<div class="col-sm-10 phone">
					<?php echo $patient['phone']; ?>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<label class="control-label"><?php echo _t('patient.address'); ?> :</label>
				</div>
				<div class="col-sm-10">
					<?php 
                        $village_name  = 'village_'.$app_lang;
                        $commune_name  = 'commune_'.$app_lang;
                        $district_name = 'district_'.$app_lang;
                        $province_name = 'province_'.$app_lang;
					?>
                    <span class="address-village"><?php echo $patient[$village_name]; ?></span> Village -
                    <span class="address-commune"><?php echo $patient[$commune_name]; ?></span> Commune -
                    <span class="address-district"><?php echo $patient[$district_name]; ?></span> District -
                    <span class="address-province"><?php echo $patient[$province_name]; ?></span> Province
				</div>
            </div>
            <!-- ADDED 03 Dec 2020 -->
			<div class="hidden">
				isPMRS = <?php echo $isPMRSPatientID;?>
			</div>
			<div class="hidden">
            <div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.country'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="country"><?php echo empty($patient["country_name_en"])?"": $patient["country_name_en"]; ?></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.nationality'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="nationality"><?php echo empty($patient["nationality_en"])?"": $patient["nationality_en"]; ?></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.date_of_arrival'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="date_arrival"><?php echo empty($patient["date_arrival"])?"": $patient["date_arrival"]; ?></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.passport_no'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="passport_number"><?php echo empty($patient["passport_number"])?"": $patient["passport_number"]; ?></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.flight_number'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="flight_number"><?php echo empty($patient["flight_number"])?"": $patient["flight_number"]; ?></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.seat_no'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="seat_number"><?php echo empty($patient["seat_number"])?"": $patient["seat_number"]; ?></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.history_of_covid19_history'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="is_positive_covid">
                        <?php 
							$is_positive_covid = empty($patient["is_positive_covid"])? false: $patient["is_positive_covid"];
                            if($is_positive_covid == false){
                                echo _t('patient.no');
                            }else{
                                echo _t('patient.yes');
                                echo '<span class="test_date">'._t('patient.test_date').": ".empty($patient["test_date"])? "": $patient["test_date"].'</span>';
                            }
                        ?>
                    </span>

					<span class="test_date"><?php echo empty($patient["test_date"])? "": $patient["test_date"]; ?></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.if_contact'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="is_contacted"><?php echo empty($patient["is_contacted"])? "": $patient["is_contacted"]; ?></span>
					<span class="contact_with"><?php echo empty($patient["contact_with"])? "": $patient["contact_with"]; ?></span>
					<span class="relationship_with_case"><?php echo empty($patient["relationship_with_case"])? "": $patient["relationship_with_case"]; ?></span>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-2">
					<span><?php echo _t('patient.describe_in_past_30_days'); ?></span>
				</div>
				<div class="col-sm-10">
					<span class="travel_in_past_30_days"><?php echo empty($patient["travel_in_past_30_days"])? "": $patient["travel_in_past_30_days"]; ?></span>
				</div>
			</div>
			</div>
            <!-- End -->

		</div>
		<!-- Patient's Form Entry -->
        <form id="patient-info-form" class="well form-vertical" style="display: none;">
            <div class="row">
                <div class="col-sm-2">
                    <label class="control-label">
                        <?php echo _t('patient.patient_id'); ?>
                    </label>
                    <input type="text" name="patient_manual_code" id="patient-manual-code" class="form-control" disabled readonly>
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
					<button type="button" class="btn btn-success" data-toggle="collapse" data-target="#covidQuestionaireInfo"><?php echo _t('sample.covid_questionaire'); ?></button>					
				</div>
			</div>
			<div class="collapse" id="covidQuestionaireInfo">
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
								<?php //echo _t('patient.choose_country'); ?>
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
					<div class="col-sm-3">
						<label class="control-label">
							<?php echo _t('patient.date_of_arrival'); ?></label>
						<div class="input-group">
							<input type="text" class="form-control dtpicker" name="date_arrival" tabindex="6" id="date_arrival">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
					</div>

					<div class="col-md-3">
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
							$app_lang	= empty($app_lang) ? 'en' : $app_lang;
							if($app_lang == 'en'){
								$VACCINATION_STATUS_DD = unserialize(VACCINATION_STATUS_DD_EN);
							}else{
								$VACCINATION_STATUS_DD = unserialize(VACCINATION_STATUS_DD_KH);
							}
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
					<!-- 03092021-->
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
							<input type="text" class="form-control" name="occupation" tabindex="6" id="occupation" />
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
						<!-- END-->			
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
			</div>
			<!-- End Collapse-->
			<!-- End -->
            <div class="row" style="margin-top:30px;">
                <div class="col-sm-12" style="text-align: right;">
                    <button type="button" class="btn btn-primary" id="btnSavePatient"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo _t('global.save'); ?></button>
                    <button type="button" class="btn btn-default" id="btnCancelPatient"><i class="fa fa-remove"></i>&nbsp;<?php echo _t('global.cancel'); ?></button>
                </div>
            </div>
        </form>
		<input type="hidden" id="patient-id" data-value="<?php echo $patient_sample['patient_id']; ?>">
		<input type="hidden" id="patient-age" data-value="<?php echo getAge($patient['dob']); ?>">
		<input type="hidden" id="patient-sex" data-value="<?php echo $patient['sex']; ?>">
	</div>

	<!-- Sample Forms -->
    <?php
    $collected_date = !empty($patient_sample['collected_date']) ? DateTime::createFromFormat('Y-m-d', $patient_sample['collected_date']) : false;
    $collected_date = $collected_date ? $collected_date->format('d/m/Y') : "";
    $collected_time = !empty($patient_sample['collected_time']) ? DateTime::createFromFormat('H:i:s', $patient_sample['collected_time']) : false;
    $collected_time = $collected_time ? $collected_time->format('H:i') : "";
    $received_date  = !empty($patient_sample['received_date']) ? DateTime::createFromFormat('Y-m-d', $patient_sample['received_date']) : false;
    $received_date  = $received_date ? $received_date->format('d/m/Y') : "";
    $received_time  = !empty($patient_sample['received_time']) ? DateTime::createFromFormat('H:i:s', $patient_sample['received_time']) : false;
    $received_time  = $received_time ? $received_time->format('H:i') : "";
    $admission_date = !empty($patient_sample['admission_date']) ? DateTime::createFromFormat('Y-m-d H:i:s', $patient_sample['admission_date']) : false;
    $admission_time = $admission_date ? $admission_date->format('H:i') : "";
    $admission_date = $admission_date ? $admission_date->format('d/m/Y') : "";
    ?>
	<div class="sample-form-wrapper" style="margin-top: 40px;">
		<h4 class="content-header"><?php echo _t('sample.edit_sample'); ?></h4>
		<hr>
		<div id="sample-forms">
			<div class="panel panel-default sample-form edit">
				<form action="" method="post" class="form-vertical frm-sample-entry">
					<div class="panel-heading">
						<div class="header"><?php echo _t('global.sample'); ?> <span class="sample-order">1</span></div>
                        <div class='sample-title'>
                            <i class="fa fa-hand-o-right" aria-hidden="true"></i>&nbsp;
                            <div style="margin-left: 0;" class="sample-number-title">
                                <b><?php echo _t('sample.sample_number')." : "; ?></b>
                                <b class="value text-blue"><?php echo $patient_sample['sample_number']; ?></b>
                            </div>&nbsp;&nbsp;
                            <div class="collected-date-title">
                                <b><?php echo _t('sample.collect_dt')." : "; ?></b>
                                <b class="value text-blue"><?php echo $collected_date.' '.$collected_time; ?></b>
                            </div>&nbsp;&nbsp;
                            <div class="received-date-title">
                                <b><?php echo _t('sample.receive_dt')." : "; ?></b>
                                <b class="value text-blue"><?php echo $received_date.' '.$received_time; ?></b>
                            </div>
                        </div>
						<a href="#" class="btnMinimized pull-right"><i class="fa fa-minus"></i></a>
					</div>
					<div class="panel-body">
						<div class='col-lg-10'>
							<div class="row">
								<div class="col-md-4">
									<label class="control-label">
										<?php echo _t('sample.sample_number'); ?></label>
									<input type="text" class="form-control" name="sample_number" tabindex="1" value="<?php echo $patient_sample['sample_number']; ?>" disabled>
								</div>
								<div class="col-md-4">
									<label class="control-label">
										<?php echo _t('sample.sample_source'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
									<select name="sample_source" class="form-control" tabindex="2">
										<option value="-1" style="color:#d8d5d5;">
											<?php echo _t('global.choose'); ?>
										</option>
										<?php
										foreach($sample_source as $sc) {
											$selected = "";
											if ($sc->source_id == $patient_sample['sample_source_id']) $selected = "selected";
											echo "<option value='$sc->source_id' $selected>$sc->source_name</option>";
										}
										?>
									</select>
								</div>
								<!-- Requester -->
								<div class="col-md-4">
									<label class="control-label"><?php echo _t('sample.requester'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
									<select name="requester" class="form-control" tabindex="3">
										<option value="-1"><?php echo _t('global.choose'); ?></option>
										<?php
										foreach($requesters as $requester) {
											$selected = "";
											if ($requester->requester_id == $patient_sample['requester_id']) $selected = "selected";
											echo "<option value='$requester->requester_id' $selected>$requester->requester_name</option>";
										}
										?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<label class="control-label"><?php echo _t('sample.collect_dt'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
									<div class="input-group">
										<input type="text" class="form-control dtpicker" name="collected_date" tabindex="4" value="<?php echo $collected_date; ?>">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input type="text" class="form-control coltimepicker narrow-padding" name="collected_time" style="width: 100px;" tabindex="5" value="<?php echo $collected_time; ?>">
										<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
									</div>
								</div>
								<div class="col-md-4">
									<label class="control-label">
										<?php echo _t('sample.receive_dt'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
									<div class="input-group" data-received_date = "<?php echo $patient_sample['received_date']; ?>">
										<input type="text" class="form-control dtpicker" name="received_date" tabindex="6" value="<?php echo $received_date; ?>">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input type="text" class="form-control rectimepicker narrow-padding" name="received_time" style="width: 100px;" tabindex="7" value="<?php echo $received_time; ?>">
										<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
									</div>
								</div>
                                <div class="col-md-4">
                                    <label class="control-label"><?php echo _t('sample.payment_type'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
                                    <select name="payment_type" class="form-control" tabindex="8">
                                        <option value="-1"><?php echo _t('global.choose'); ?></option>
                                        <?php
                                        foreach ($payment_types as $payment_type) {
                                            $selected = $payment_type['id'] == $patient_sample['payment_type_id'] ? "selected" : "";
                                            echo "<option value='".$payment_type['id']."' ".$selected.">".$payment_type['name']."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
							</div>
							<div class="row">
                                <div class="col-md-4">
                                    <label class="control-label"><?php echo _t('sample.admission_date'); ?></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control admission-date" name="admission_date" value="<?php echo $admission_date; ?>">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control admission-time narrow-padding" name="admission_time" style="width: 100px;" value="<?php echo $admission_time; ?>">
                                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                    </div>
                                </div>
								<div class="col-md-2">
									<label class="control-label">
										<?php echo _t('sample.clinical_history'); ?>
									</label>
									<textarea name="clinical_history" cols="30" rows="1" class="form-control" tabindex="10" style="resize:none;"><?php echo $patient_sample['clinical_history']; ?></textarea>
								</div>
                                <div class="col-md-4">
                                    <label class="control-label">
                                        <?php echo _t('sample.reason_for_testing'); ?> 
                                    </label>
                                    <div>
                                        <div class="checkbox-wrapper" style="margin-bottom: 3px; padding-right: 0px !important;" data-research = "<?php echo $patient_sample['for_research'];?>">
                                            <label class='checkbox-inline'>
                                                <input type="checkbox" value="1" name="is_urgent" tabindex="11" <?php echo $patient_sample['is_urgent'] == 1 ? 'checked' : ''; ?> >
                                                <?php echo _t('sample.urgent'); ?>
                                            </label>
                                        </div>
                                        <div class="checkbox-wrapper" style="padding-right: 0px;">                                            
											<?php 
                                            $app_lang	= empty($app_lang) ? 'en' : $app_lang;
                                            if($app_lang == 'en'){
                                                $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY);
                                            }else{
                                                $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
                                            }
                                            echo form_dropdown('for_research', $FOR_RESEARCH_ARR,$patient_sample['for_research'], 'class="form-control"');
                                    	?>
                                        </div>
                                    </div>
                                </div>
								<div class="col-md-2">
									<label class="control-label"><?php echo _t('sample.number_of_sample'); ?></label>
									<?php
										$number_of_sample = ($patient_sample['number_of_sample'] == "" || $patient_sample['number_of_sample'] == NULL) ? 0 : $patient_sample['number_of_sample'];
										$NUMBER_OF_SAMPLE_DD = unserialize(NUMBER_OF_SAMPLE_DD);
										echo form_dropdown('number_of_sample', $NUMBER_OF_SAMPLE_DD, $number_of_sample , 'class="form-control"');	
									?>
								</div> 
                            </div>
                            <!-- 
                                Covid Form
                                ADDED: 03 DEC 2020
                                -->
                            <div class="row" data-ns="<?php echo $number_of_sample." - ".$patient_sample['number_of_sample'];?>">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary btnShowQuestionaire margintop10" tabindex="13" data-toggle="collapse" data-target="#collapseCovidForm" aria-expanded="false" aria-controls="collapseCovidForm"><i class="fa fa-list-alt"></i>&nbsp;                                
                                        <?php echo _t('sample.covid_questionaire'); ?>
                                    </button>
                                </div>
                            </div>
                            <div class="collapse" id="collapseCovidForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="control-label">
                                            <?php echo _t('sample.completed_by'); ?>
                                        </label>
                                        <input type="text" name="completed_by" class="form-control" value="<?php echo $patient_sample['completed_by'];?>" >
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">
                                            <?php echo _t('sample.telephone'); ?>
                                        </label>
                                        <input type="text" name="phone_number" class="form-control" value="<?php echo $patient_sample['phone_number'];?>">
                                    </div>                        
                                    <div class="col-md-3">
                                        <label class="control-label">
                                            <?php echo _t('sample.sample_collector'); ?>
                                        </label>
                                        <input type="text" name="sample_collector" class="form-control" value="<?php echo $patient_sample['sample_collector'];?>">
									</div>
									<div class="col-md-3">
										<label class="control-label">
											<?php echo _t('sample.telephone'); ?>
										</label>
										<input type="text" name="phone_number_sample_collector" class="form-control">
									</div>
                                </div>
								<div class="row">
									<div class="col-md-8">
                                        <label class="control-label">
                                            <?php echo _t('sample.clinical_symptom'); ?>
                                        </label>                            
                                        <select name="clinical_symptom" class="form-control" multiple="multiple">
                                        <option><?php echo _t('global.choose'); ?></option>
                                        <?php 
                                             $app_lang	= empty($app_lang) ? 'en' : $app_lang;
                                             if($app_lang == 'en'){ 
                                                foreach($clinical_symptoms as $item){
                                                    echo "<option value=".$item->ID.">".$item->name_en."</option>";
                                                 }
                                             }else{
                                                foreach($clinical_symptoms as $item){
                                                    echo "<option value=".$item->ID." selected>".$item->name_kh."</option>";
                                                 }
                                             }
                                        ?>
                                        </select>
                                    </div>
									<div class="col-md-4">
										<label class="control-label">
											<?php echo _t('sample.health_facility'); ?>
										</label>
										<input type="text" name="health_facility" class="form-control" value="<?php echo !empty($patient_sample['health_facility']) ? $patient_sample['health_facility'] : "";?>">
									</div>
								</div>
                            </div>
                            <!-- End-->
						</div>
						<div class="col-lg-2 btn-wrapper">
							<?php
								$is_assigned_test = isset($patient_sample['is_assigned_test']) ? $patient_sample['is_assigned_test'] : false;
								$disabled = $is_assigned_test ? '' : 'disabled';
							?>
							<div class="form-group">
                                <?php if ($this->aauth->is_allowed('add_psample') || $this->aauth->is_allowed('edit_psample')) { ?>
								<button type="button" class="btn btn-primary btnShowTestModal margintop10" tabindex="13" disabled><i class="fa fa-list-alt"></i>&nbsp;
									<?php echo _t('sample.assign_test'); ?>
								</button>
								<button type="button" class="btn btn-primary btnSaveSample margintop10" tabindex="14" action-type='save' disabled><i class="fa fa-floppy-o"></i>&nbsp;
									<?php echo _t('global.save'); ?>
								</button>
                                <?php } ?>
								<?php if ($this->aauth->is_allowed('reject_sample')) { ?>
                                <button type="button" class="btn btn-danger btnRejectSample margintop10" tabindex="15" action-type='save-reject' data-enabled="<?php echo $is_assigned_test; ?>" <?php echo $disabled; ?> >
                                    <span class="glyphicon glyphicon-minus-sign"></span>&nbsp;</i><?php echo _t('sample.rejected'); ?>
                                </button>
								<?php } if ($this->aauth->is_allowed('add_psample_result')) { ?>
                                <button type="button" class="btn btn-primary btnAddResult margintop10" tabindex="16" data-enabled="<?php echo $is_assigned_test; ?>" <?php echo $disabled; ?> >
                                    <i class="fa fa-pencil"></i>&nbsp;<?php echo _t('sample.add_result'); ?>
                                </button>
								<?php } ?>
                                <?php if ($this->aauth->is_allowed('print_psample_result')) { ?>
								<button type="button" class="btn btn-success btnPreview margintop10" tabindex="17" data-enabled="<?php echo $is_assigned_test; ?>" <?php echo $disabled; ?> >
									<i class="fa fa-eye" aria-hidden="true"></i>&nbsp;<?php echo _t('sample.preview_result'); ?>
								</button>
                                <?php } ?>
                                <?php if ($this->aauth->is_allowed('delete_psample')) { ?>
								<button type="button" class="btn btn-danger btnRemove margintop10" tabindex="18"><i class="fa fa-trash"></i>&nbsp;
									<?php echo _t('global.remove'); ?>
								</button>
                                <?php } ?>

								<!-- ADDED 10 Jan 2021-->
								<button type="button" class="btn btn-success btnPreviewCovidForm margintop10" tabindex="18"><i class="fa fa-eye"></i>&nbsp;									
									<?php echo _t('sample.preview_request'); ?>
								</button>
								<!-- End-->
							</div>
						</div>

                        <div class="col-sm-12 sample-user-info">
                            <div class="well">

                            	<!-- entry user -->
                                <b><i class="fa fa-user"></i>&nbsp;<?php echo _t('sample.sample_entry_by'); ?> : </b>
                                <div class="sample-entry-user-list">
                                    <span class="sample-entry-user label label-primary template hide"></span>
                                    <?php
                                        $sample_entry_users = isset($patient_sample_user['sample_entry_user']) ? explode(',', $patient_sample_user['sample_entry_user']) : [];
										$sample_entry_date = isset($patient_sample_user['entry_date']) ?$patient_sample_user['entry_date']: [];

                                        $result_entry_users = isset($patient_sample_user['result_entry_user']) ? explode(',', $patient_sample_user['result_entry_user']) : [];

                                        if (isset($patient_sample_user['sample_entry_user']) && !empty($patient_sample_user['sample_entry_user']) && count($sample_entry_users) > 0) {
                                            foreach ($sample_entry_users as $s_username) {
                                                echo "<span class='sample-entry-user label label-primary user'>$s_username</span>";
                                            }
                                        } else {
                                            echo "<span class='no-result'>N/A</span>";
                                        }
                                    ?>
                                </div>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <b><?php echo _t('sample.entry_date'); ?> : </b>
                                <div class="result-entry-user-list">
                                    <span class="result-entry-user label label-primary template hide"></span>
                                    <?php
                                        if (isset($patient_sample_user['entryDate']) && !empty($patient_sample_user['entryDate'])) {
                                                echo "<span class='result-entry-user label label-primary user'>".$patient_sample_user["entryDate"]."</span>";
                                        } else {
                                            echo "<span class='no-result'>N/A</span>";
                                        }
                                    ?>
                                </div>

                                <!-- modified by  -->
								 &nbsp;&nbsp;&nbsp;&nbsp;
                                <b><i class="fa fa-user"></i>&nbsp;<?php echo _t('sample.modified_by'); ?> : </b>
                                <div class="result-entry-user-list">
                                    <span class="result-entry-user label label-primary template hide"></span>
                                    <?php
                                        if (isset($patient_sample_user['modifiedBy']) && !empty($patient_sample_user['modifiedBy'])) {
                                                echo "<span class='result-entry-user label label-primary user'>".$patient_sample_user["modifiedBy"]."</span>";
                                        } else {
                                            echo "<span class='no-result'>N/A</span>";
                                        }
                                    ?>
                                </div>
                                 &nbsp;&nbsp;&nbsp;&nbsp;
                                <b><?php echo _t('sample.modified_date'); ?> : </b>
                                <div class="result-entry-user-list">
                                    <span class="result-entry-user label label-primary template hide"></span>

                                    <?php
                                        if (isset($patient_sample_user['modifiedDate']) && !empty($patient_sample_user['modifiedDate'])) {
                                                echo "<span class='result-entry-user label label-primary user'>".$patient_sample_user["modifiedDate"]."</span>";
                                        } else {
                                            echo "<span class='no-result'>N/A</span>";
                                        }
                                    ?>
                                </div>  <!-- modified -->
                            </div>
                        </div>
					</div>
					<input type="hidden" name="patient_sample_id" data-value="<?php echo $patient_sample['patient_sample_id']; ?>">
					<input type="hidden" id="department_result_view_optional" />
					<input type="hidden" id="sample_result_view_optional" />
                    
				</form>
			</div>
            <?php $hide = $this->aauth->is_allowed('add_psample') ? '' : 'hide'; ?>
			<button type="button" id="btnMore" class="btn btn-flat btn-primary col-sm-12 <?php echo $hide; ?>"><i class="fa fa-plus"></i>&nbsp;<?php echo _t('sample.add_sample'); ?></button>
        </div>
	</div>
</div>
<?php } ?>
