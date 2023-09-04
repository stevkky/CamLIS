<script>
    var patient_info            = JSON.parse('<?php echo json_encode($patient) ?>');
    var msg_required_data		= "<?php echo _t('global.msg.fill_required_data');			?>";
    var msg_save_success		= "<?php echo _t('global.msg.save_success');				?>";
    var msg_save_fail			= "<?php echo _t('global.msg.save_fail');					?>";
    var msg_dob_not_after_now	= "<?php echo _t('sample.msg.dob_not_after_now');			?>";
    var msg_loading				= "<?php echo _t('global.plz_wait');						?>";
    var msg_saving				= "<?php echo _t('global.saving');							?>";
    var msg_delete_fail			= "<?php echo _t('global.msg.delete_fail');					?>";
</script>
<?php if (!$patient || !isset($patient['name'])) { ?>
	<div class="col-sm-12">
		<div class="well text-center text-red" id="no-result"><b><?php echo _t('global.no_result'); ?></b></div>
	</div>
<?php } else { ?>
<div class="col-sm-12">
	<div class="patient-info-view" style="margin-bottom: 30px;">
        <div class="row">
            <div class="col-sm-2">
                <label class="control-label"><?php echo _t('patient.patient_id'); ?> :</label>
            </div>
            <div class="col-sm-10">
                <?php echo $patient['patient_code']; ?>
            </div>
        </div>
		<div class="row">
			<div class="col-sm-2">
				<label class="control-label"><?php echo _t('patient.patient_name'); ?> :</label>
			</div>
			<div class="col-sm-10">
				<?php echo $patient['name']; ?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-2">
				<label class="control-label"><?php echo _t('patient.sex'); ?> :</label>
			</div>
			<div class="col-sm-10">
				<?php echo $patient['sex'] == 'F' ? _t('global.female') : _t('global.male'); ?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-2">
				<label class="control-label"><?php echo _t('patient.age'); ?> :</label>
			</div>
			<div class="col-sm-10">
				<?php
                    $age = calculateAge($patient['dob']);
				?>
                <span class="age-year"></span> <?php echo $age->y.' '._t('global.year') ?> &nbsp;
                <span class="age-month"></span> <?php echo $age->m.' '._t('global.month') ?> &nbsp;
                <span class="age-day"></span> <?php echo ($age->days > 0 ? $age->d : 1).' '._t('global.day') ?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-2">
				<label class="control-label"><?php echo _t('patient.phone'); ?> :</label>
			</div>
			<div class="col-sm-10">
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
                    echo $patient[$village_name].' Village - '.$patient[$commune_name].' Commune - '.$patient[$district_name].' District - '.$patient[$province_name].' Province';
				?>
			</div>
		</div>
	</div>

    <!-- Patient's Form Entry -->
    <form id="patient-info-form" class="well form-vertical" style="display: none">
        <div class="row">
            <div class="col-sm-4">
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
                <div style="padding-top: 4px;">
                    <label class="control-label" style="cursor:pointer;">
                        <input type="radio" name='patient_sex' value="1" checked>&nbsp;
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
        
        <!-- 12-07-2021 -->
        <!-- Update Lab Request Form -->			
        <div class="row">
            <div class="col-sm-12">
                <button type="button" class="btn btn-success" data-toggle="collapse" data-target="#covid_questionaire"><?php echo _t('sample.covid_questionaire'); ?></button>					
            </div>
        </div>
        <div class="collapse" id="covid_questionaire">
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
                <div class="col-sm-2">
					<label class="control-label">
						<?php echo _t('patient.first_injection_date'); ?></sup></label>
					<div class="input-group">
						<input type="text" class="form-control dtpicker" name="first_vaccinated_date" id="first_vaccinated_date" disabled>
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
				<div class="col-sm-2">
					<label class="control-label">
						<?php echo _t('patient.second_injection_date'); ?></sup></label>
					<div class="input-group">
						<input type="text" class="form-control dtpicker" name="second_vaccinated_date" id="second_vaccinated_date" disabled>
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
                <div class="col-sm-3">
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
                
                <div class="col-sm-2">
                    <label class="control-label"><?php echo _t('patient.occupation'); ?></label>               
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
        </div>
        <!-- End -->
        <div class="row" style="margin-top:30px;">
            <div class="col-sm-12" style="text-align: right;">
                <button type="button" class="btn btn-primary" id="btnSavePatient"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo _t('global.save'); ?></button>
                <button type="button" class="btn btn-default" id="btnCancelPatient"><i class="fa fa-remove"></i>&nbsp;<?php echo _t('global.cancel'); ?></button>
            </div>
        </div>
    </form>
	
	<!--Sample History-->
	<h4 class="content-header">
		<?php echo _t('sample.sample_list'); ?>&nbsp;&nbsp;
        <?php if ($this->aauth->is_allowed('add_psample')) { ?>
		<a href="<?php echo base_url().'sample/new/'.$patient['patient_code']; ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?php echo _t('sample.add_sample'); ?></a>
	    <?php } ?>
    </h4>
	<hr>
	<table class="table table-bordered table-hovered table-striped" id="psample_list">
		<thead>
			<tr>
				<th rowspan="2" width="40px" class="text-center"><?php echo _t('global.no.'); ?></th>
				<th rowspan="2"><?php echo _t('sample.sample_number'); ?></th>
				<th rowspan="2" class="text-center" style="width:170px;"><?php echo _t('sample.collect_dt'); ?></th>
				<th rowspan="2" class="text-center" style="width:170px;"><?php echo _t('sample.receive_dt'); ?></th>
				<th colspan="4" class="text-center"><?php echo _t('sample.sample_status'); ?></th>
				<th rowspan="2" class="text-center" style="width:70px;"><?php echo _t('sample.result'); ?></th>
				<th rowspan="2" class="text-center" style="width:70px;"></th>
			</tr>
			<tr>
				<th style="font-size:13px; width:50px;" class="text-center"><?php echo _t('sample.urgent'); ?></th>
				<th style="font-size:13px; width:50px;" class="text-center"><?php echo _t('sample.payment_need'); ?></th>
				<th style="font-size:13px; width:50px;" class="text-center"><?php echo _t('sample.research'); ?></th>
				<th style="font-size:13px; width:50px;" class="text-center"><?php echo _t('sample.rejected'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (count($samples) == 0) {
					echo "<tr><td colspan='10' class='text-center'>No Sample!</td></tr>";
				} else {
					$i = 1;
					foreach($samples as $sample) {
						echo "<tr>";
						echo "<td>$i</td>";
                        echo "<td>".$sample['sample_number']."</td>";
                        echo "<td class='text-center'>".DateTime::createFromFormat('Y-m-d H:i:s', $sample['collected_date'].' '.$sample['collected_time'])->format('d-m-Y h:i A')."</td>";
                        echo "<td class='text-center'>".DateTime::createFromFormat('Y-m-d H:i:s', $sample['received_date'].' '.$sample['received_time'])->format('d-m-Y h:i A')."</td>";
                        echo "<td class='text-center'>".($sample['is_urgent']      == 0 ? '' : '<i class=\'fa fa-check-circle text-green\'></i>')."</td>";
                        echo "<td class='text-center'>".($sample['payment_needed'] == 0 ? '' : '<i class=\'fa fa-check-circle text-green\'></i>')."</td>";
                        echo "<td class='text-center'>".($sample['for_research']   == 0 ? '' : '<i class=\'fa fa-check-circle text-green\'></i>')."</td>";
                        echo "<td class='text-center'>".($sample['is_rejected']    == 0 ? '' : '<i class=\'fa fa-check-circle text-green\'></i>')."</td>";
                        echo "<td class='text-center'></td>";
                        echo "<td class='text-center'>";
						if ($this->aauth->is_allowed('edit_psample')) echo "<a href='".$this->app_language->site_url('sample/edit/'.$sample['patient_sample_id'])."' class='hint--left hint--info' data-hint='"._t('global.edit')."'><i class='fa fa-pencil-square-o'></i></a>&nbsp;|&nbsp;";
                        if ($this->aauth->is_allowed('delete_psample')) echo "<a href='#' class='btnDeleteSample hint--left hint--error text-red' data-hint='"._t('global.remove')."' data-value='".$sample['patient_sample_id']."'><i class='fa fa-trash'></i></a>";
                        echo "</td>";
						echo "</tr>";
						$i++;
					}
				}
			?>
		</tbody>
	</table>
</div>
<?php } ?>