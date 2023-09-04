<script type="text/javascript">
	var patient = null;
	var msg_loading = "<?php echo _t('global.plz_wait'); ?>";
	var msg_save_fail = "<?php echo _t('global.msg.save_fail'); ?>";
	var label_sample_desription	= "<?php echo _t('request.sample_desc'); ?>";
	var sample_descriptions = JSON.parse('<?php echo json_encode($sample_descriptions); ?>');
	var label_weight1 = "<?php echo _t('request.weight1'); ?>";
	var label_weight2 = "<?php echo _t('request.weight2'); ?>";
	var q_delete_patient_sample	= "<?php echo _t('request.q.delete_patient_sample'); ?>";
	var msg_delete_fail = "<?php echo _t('global.msg.delete_fail');	?>";
	var msg_must_select_test	= "<?php echo _t('request.msg.must_select_test'); ?>";
	const TEST_PAYMENTS = <?php echo json_encode($test_payments); ?>;
	var msg_dob_not_after_now = "<?php echo _t('request.msg.dob_not_after_now'); ?>";
	var msg_required_data = "<?php echo _t('global.msg.fill_required_data'); ?>";
</script>

<!-- ====================Search patient wraper=================== -->
<div class="col-sm-12">
	<div class="row" id="search_patient_wrapper" style="border: 1px solid rgb(238, 238, 238); padding: 12px;">
		<div class="col-sm-4">
			<div class="col-sm-12" style="padding-left: 0px;">
				<form class="form-vertical" id="search_patient" role="form">
					<label for="search_patient_id" class="control-label">
						<?php echo _t('patient.find_patient_id'); ?>
					</label>
					<div class="input-group">
						<input type="text" class="form-control" name="search-patient-id" id="search_patient_id" placeholder="" autocomplete="off" autofocus>
						<span class="input-group-btn">
							<button type="submit" class="btn btn-primary" id="button_search_patient">
								<?php echo _t('global.search'); ?>
								&nbsp;<i class="fa fa-search"></i>
							</button>
						</span>
					</div>
				</form>
			</div>
		</div>
		<div class="col-sm-1">
			<div class="row">
				<div class="col-sm-12">
					<label class="control-label"> </label>
					<div class="input-group" style='padding-top:10px; font-weight:bold;' class="text-center">
						<?php echo _t('request.or'); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-2">
			<div class="row">
				<div class="col-sm-12">
					<label class="control-label">&nbsp;</label>
					<div class="input-group">
						<button type="button" class="btn btn-primary" id="button_new_patient">
							<i class="fa fa-user-plus"></i>
							<?php echo _t('patient.new_patient'); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- ====================Display patient information=================== -->
	<div class="row" id="display_patient_information" style="padding-left: 10px; display: none;">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-2">
					<?php echo _t('patient.patient_id'); ?>
				</div>
				<div class="col-sm-1">:</div>
				<div class="col-sm-9 patient-code"></div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<?php echo _t('patient.patient_name'); ?>
				</div>
				<div class="col-sm-1">:</div>
				<div class="col-sm-9 patient-name"></div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<?php echo _t('patient.sex'); ?>
				</div>
				<div class="col-sm-1">:</div>
				<div class="col-sm-9 patient-gender"></div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<?php echo _t('patient.age'); ?>
				</div>
				<div class="col-sm-1">:</div>
				<div class="col-sm-9 patient-age">
					<span class="patient-age-year"></span> <?php echo _t('global.year') ?> &nbsp;
					<span class="patient-age-month"></span> <?php echo _t('global.month') ?> &nbsp;
					<span class="patient-age-day"></span> <?php echo _t('global.day') ?>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<?php echo _t('patient.phone'); ?>
				</div>
				<div class="col-sm-1">:</div>
				<div class="col-sm-9 patient-phone"></div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<?php echo _t('patient.address'); ?>
				</div>
				<div class="col-sm-1">:</div>
				<div class="col-sm-9 patient-address">
					<span class="patient-address-village"></span> <?php echo _t('Village').' -'; ?>&nbsp;
					<span class="patient-address-commune"></span> <?php echo _t('Commune').' -'; ?>&nbsp;
					<span class="patient-address-district"></span> <?php echo _t('District').' -'; ?>
					<span class="patient-address-province"></span> <?php echo _t('Province') ?>
				</div>
			</div>
		</div>
	</div>
	<!-- ====================Patient entry form=================== -->
	<div class="row" id="patient_entry_form_wrapper" style="display: none;">
		<form class="form-vertical" id="patient_entry_form" role="form">
			<div class="col-sm-12 well">
				<div class="row">
					<div class="col-sm-2">
						<div class="form-group">
							<label for="patient_manual_code" class="control-label">
								<?php echo _t('patient.patient_id'); ?>
							</label>
							<input type="text" class="form-control" name="patient_manual_code" id="patient_manual_code">
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label for="patient_name" class="control-label">
								<?php echo _t('patient.patient_name'); ?>
							</label>
							<input type="text" class="form-control" name="patient_name" id="patient_name">
						</div>
					</div>
					<div class="col-sm-6">
						<label for="patient_dob" class="control-label">
							<?php echo _t('patient.dob').' / '._t('global.age'); ?>
						</label>
						<div class="input-group">
							<span class="input-group-addon" id="basic-addon1"><i class="glyphicon glyphicon-calendar"></i></span>
							<input type="text" class="form-control" name="patient_dob" id="patient_dob" size="40">
							<span class="input-group-addon" id="basic-addon1"><b><?php echo _t('global.year'); ?></b></span>
							<input type="number" class="form-control" id="patient-age-year" placeholder="<?php echo _t('global.year'); ?>" onkeypress="return isNumber(event);" maxlength="2" onfocus="this.select()">
							<span class="input-group-addon" id="basic-addon1"><?php echo _t('global.month'); ?></span>
							<input type="number" class="form-control" id="patient-age-month" maxlength="2" placeholder="<?php echo _t('global.month'); ?>" onkeypress="return isNumber(event);" onfocus="this.select()">
							<span class="input-group-addon" id="basic-addon1"><?php echo _t('global.day'); ?></span>
							<input type="number" class="form-control" id="patient-age-day" maxlength="2" placeholder="<?php echo _t('global.day'); ?>" onkeypress="return isNumber(event);" onfocus="this.select()">
						</div>
					</div>
					<div class="col-sm-2">
						<label for="patient_manual_code" class="control-label">
							<?php echo _t('patient.sex'); ?>
						</label>
						<div class="form-group">
							<div class="col-sm-6">
								<label class="control-label" style="cursor:pointer;">
									<input type="radio" name='patient_sex' value="1">&nbsp;
									<?php echo _t('global.male'); ?>
								</label>
							</div>
							<div class="col-sm-6">
								<label class="control-label" style="cursor:pointer;">
									<input type="radio" name='patient_sex' value="2">&nbsp;
									<?php echo _t('global.female'); ?>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label for="patient_phone" class="control-label">
								<?php echo _t('patient.phone'); ?>
							</label>
							<input type="text" class="form-control" name="patient_phone" id="patient_phone">
						</div>
					</div>
					<div class="col-sm-8">
						<label for="patient_manual_code" class="control-label">
							<?php echo _t('patient.address'); ?>
						</label>
						<div class="form-group">
							<div class="col-sm-3" style="padding-left:0;">
								<select name="province" id="province" class="form-control" data-get="district">
									<option value="-1" style="color:#d8d5d5;"><?php echo _t('global.choose_province'); ?></option>
									<?php $app_lang = empty($app_lang) ? 'en' : $app_lang; $name = 'name_'.$app_lang; ?>
									<?php foreach ($provinces as $province): ?>
										<option value="<?php echo $province->code; ?>"><?php echo $province->$name; ?></option>
									<?php endforeach ?>
								</select>
							</div>
							<div class="col-sm-3" style="padding-left:0;">
								<select name="district" id="district" class="form-control" data-get="commune">
									<option value="-1" style="color:#d8d5d5;"><?php echo _t('global.choose_district'); ?></option>
								</select>
							</div>
							<div class="col-sm-3" style="padding-left:0;">
								<select name="commune" id="commune" class="form-control" data-get="village">
									<option value="-1" style="color:#d8d5d5;"><?php echo _t('global.choose_commune'); ?></option>
								</select>
							</div>
							<div class="col-sm-3" style="padding-left:0;">
								<select name="village" id="village" class="form-control">
									<option value="-1" style="color:#d8d5d5;"><?php echo _t('global.choose_village'); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12" style="text-align: right; padding-right: 25px;">
						<div class="form-group">
							<button type="button" class="btn btn-primary" id="button_save_patient">
								<i class="fa fa-floppy-o"></i>&nbsp;
								<?php echo _t('global.save'); ?>
							</button>
							<button type="button" class="btn btn-default" id="button_cancel_patient">
								<i class="fa fa-remove"></i>&nbsp;
								<?php echo _t('global.cancel'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
			<!-- hidden field -->
			<input type="hidden" id="patient_id" data-value ="">
			<input type="hidden" id="patient_age" data-value ="">
			<input type="hidden" id="patient_sex" data-value ="">
		</form>
	</div>
</div>
<!-- ====================Search patient not found=================== -->
<div class="col-sm-12">
	<div class="row" id="search_patient_not_found" style="display: none;">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-12">
					<div class="well well-sm text-center text-red">
						<b><?php echo _t('global.no_result'); ?></b>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- ====================Patient sample entry form=================== -->
<div class="col-sm-12">
	<div class="row" id="patient_sample_entry_form_wrapper" style="display: none;">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-12">
					<h4 class="content-header"><?php echo _t('request.new_sample'); ?></h4>
					<hr>
				</div>
			</div>
			<div class="row">
				<div id="patient_sample_form">
					<button type="button" id="button_add_more_sample" class="btn btn-flat btn-primary col-sm-12">
						<i class="fa fa-plus"></i>
						<?php echo _t('request.add_sample'); ?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>