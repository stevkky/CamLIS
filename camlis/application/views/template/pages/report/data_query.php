<script>
    const DISTRICTS            = <?php echo json_encode($districts); ?>;
    var msg_required_condition = "<?php echo _t('data_query.msg.required_condition'); ?>";
    var msg_generating_fail    = "<?php echo _t('data_query.msg.generating_fail'); ?>";
    var label_all              = "<?php echo _t('global.all'); ?>";
    var label_choose           = "<?php echo _t('global.choose'); ?>";
    var label_departments      = "<?php echo _t('data_query.departments') ?>";
    var label_sample_types     = "<?php echo _t('data_query.sample_types'); ?>";
    var label_sample_sources   = "<?php echo _t('data_query.sample_sources'); ?>";
    var label_requesters       = "<?php echo _t('data_query.requesters'); ?>";
    var label_tests            = "<?php echo _t('data_query.tests'); ?>";
    var label_antibiotics      = "<?php echo _t('data_query.antibiotics'); ?>";
    var label_result_organisms = "<?php echo _t('data_query.result_organisms'); ?>";
    var label_provinces        = "<?php echo _t('data_query.provinces'); ?>";
    var label_districts        = "<?php echo _t('data_query.districts'); ?>";
    var label_sample_status    = "<?php echo _t('data_query.sample_status'); ?>";
    var label_laboratories     = "<?php echo _t('data_query.laboratories'); ?>";
    var label_sample_descriptions = "<?php echo _t('data_query.sample_descriptions'); ?>";

	var msg_required_primary_condition = "<?php echo _t('data_query.msg.required_primary_condition'); ?>";

</script>
<div class="row">
    <div class="col-sm-12">
        <div class="col-sm-5 col-md-4">
            <form id="frm-condition">
                <table id="tbl-condition">

					<tr class="header">
						<th></th>
						<th class="text-blue"><h4><?php echo _t('data_query.enter_primary_condition'); ?> <span class="text-danger"> *</span></h4></th>
						<th class="text-blue" width="20px"><input type="checkbox" class="show-field" id="check-all" checked></th>
					</tr>

					<!-- Collected Date -->
					<tr class="query-field">
						<td class="text-right title"><?php echo _t('data_query.collection_date'); ?></td>
						<td>
							<div class="input-group">
								<input type="text" class="form-control input-sm dtpicker" id="collection-date-min" name="collected_date[min]">
								<span class="input-group-addon"><?php echo _t('data_query.to'); ?></span>
								<input type="text" class="form-control input-sm dtpicker" id="collection-date-max" name="collected_date[max]">
							</div>
						</td>
						<td class="text-center"><input type="checkbox" value="1" class="show-field-" disabled data-name="collected_date" name="collected_date[is_show]" checked></td>
					</tr>

					<!-- Received Date -->
					<tr class="query-field">
						<td class="text-right title"><?php echo _t('data_query.received_date'); ?></td>
						<td>
							<div class="input-group">
								<input type="text" class="form-control input-sm dtpicker" id="received-date-min" name="received_date[min]">
								<span class="input-group-addon"><?php echo _t('data_query.to'); ?></span>
								<input type="text" class="form-control input-sm dtpicker" id="received-date-max" name="received_date[max]">
							</div>
						</td>
						<td class="text-center"><input type="checkbox" value="1" class="show-field-" disabled data-name="received_date" name="received_date[is_show]" checked></td>
					</tr>

					<!-- Test Date -->
					<tr class="query-field">
						<td class="text-right title"><?php echo _t('data_query.test_date'); ?></td>
						<td>
							<div class="input-group">
								<input type="text" class="form-control input-sm dtpicker" id="test-date-min" name="test_date[min]">
								<span class="input-group-addon"><?php echo _t('data_query.to'); ?></span>
								<input type="text" class="form-control input-sm dtpicker" id="test-date-max" name="test_date[max]">
							</div>
						</td>
						<td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="test_date" name="test_date[is_show]" checked></td>
					</tr>

                    <tr class="header">
                        <th></th>
                        <th class="text-blue"><h4><?php echo _t('data_query.enter_condition'); ?></h4></th>
                    </tr>



					<!-- Laboratory ID -->
                    <tr class="query-field labo-wrapper">
                        <td class="text-right title"><?php echo _t('data_query.lab_id'); ?></td>
                        <td>
							<select id="laboratory" class="form-control input-sm" style="width: 135px !important;" name="laboratory[value][]" multiple>
								<?php
									foreach($laboratories as $laboratory) {
										$app_lang	= empty($app_lang) ? 'en' : $app_lang;
										$name = 'name_'.$app_lang;
										echo "<option value='".$laboratory->labID."'>".$laboratory->$name."</option>";
									}
								?>
							</select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="laboratory_name" name="laboratory[is_show]" checked></td>
                    </tr>
                    <!-- Patient ID -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.patient_id'); ?></td>
                        <td><input type="text" class="form-control input-sm" id="patient-code" name="patient_code[value]"></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="patient_code" name="patient_code[is_show]" checked></td>
                    </tr>
                    <!-- Patient's Name -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.patient_name'); ?></td>
                        <td><input type="text" class="form-control input-sm" id="patient-name" name="patient_name[value]"></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="patient_name" name="patient_name[is_show]" checked></td>
                    </tr>
                    <!-- Patient's Age -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.age'); ?></td>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control input-sm" id="patient-age-min" name="patient_age[min]">
                                <span class="input-group-addon"><?php echo _t('data_query.to'); ?></span>
                                <input type="text" class="form-control input-sm" id="patient-age-max" name="patient_age[max]">
                            </div>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="patient_age" name="patient_age[is_show]" checked></td>
                    </tr>
                    <!-- Patient's gender -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.gender'); ?></td>
                        <td>
                            <select id="patient-gender" class="form-control input-sm" name="patient_gender[value]">
                                <option value=""></option>
                                <option value="<?php echo MALE; ?>"><?php echo _t('data_query.male'); ?></option>
                                <option value="<?php echo FEMALE; ?>"><?php echo _t('data_query.female'); ?></option>
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="patient_gender" name="patient_gender[is_show]" checked></td>
                    </tr>
                    <!-- Phone -->
					<tr class="query-field hide">
                        <td class="text-right title"><?php echo _t('data_query.phone'); ?></td>
                        <td></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="phone" name="phone[is_show]"></td>
                    </tr>
                    <!-- Nationality -->
					<tr class="query-field hide">
                        <td class="text-right title"><?php echo _t('data_query.nationality'); ?></td>
                        <td></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="nationality" name="nationality[is_show]" checked></td>
                    </tr>
                    <!-- Patient's province address -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.province'); ?></td>
                        <td>
                            <select id="patient-province" class="form-control input-sm" name="province[value][]" multiple>
                                <?php
                                    if (isset($provinces)) {
                                        $app_lang	= empty($app_lang) ? 'en' : $app_lang;
                                        $name		= 'name_'.$app_lang;

                                        foreach ($provinces as $province) {
                                            echo "<option value='".$province->code."'>".$province->$name."</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="province" name="province[is_show]" checked></td>
                    </tr>
                    <!-- Patient's district address -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.district'); ?></td>
                        <td>
                            <select id="patient-district" class="form-control input-sm" name="district[value][]" multiple>
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="district" name="district[is_show]" checked></td>
                    </tr>
                    <tr class="query-field hide">
                        <td class="text-right title"><?php echo _t('data_query.commune'); ?></td>
                        <td></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="commune" name="commune[is_show]" checked></td>
                    </tr>
                    <tr class="query-field hide">
                        <td class="text-right title"><?php echo _t('data_query.village'); ?></td>
                        <td></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="village" name="village[is_show]" checked></td>
                    </tr>

                    <!-- Department -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.department'); ?></td>
                        <td>
                            <select id="department" class="form-control input-sm" name="department[value][]" multiple>
                                <?php
                                    if (isset($departments)) {
                                        foreach ($departments as $department) {
                                            echo "<option value='".$department->department_id."'>".$department->department_name."</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="department" name="department[is_show]" checked></td>
                    </tr>
                    <!-- Sample Name -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.sample_name'); ?></td>
                        <td>
                            <select id="sample-name" class="form-control input-sm" name="sample_type[value][]" multiple>
                                <?php
                                if (isset($samples)) {
                                    foreach ($samples as $sample) {
                                        echo "<option value='".$sample->ID."'>".$sample->sample_name."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="sample_type" name="sample_type[is_show]" checked></td>
                    </tr>
                    <!-- Sample Status -->
                    <tr class="query-field">
                        <?php 
                            $app_lang	= empty($app_lang) ? 'en' : $app_lang;
                            if($app_lang == 'en'){
                                $FOR_RESEARCH = unserialize(FOR_RESEARCH_FIELD_ARRAY);
                            }else{
                                $FOR_RESEARCH = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
                            }
                            ?>
                        <td class="text-right title"><?php echo _t('data_query.sample_status'); ?></td>
                        <td>
                            <select id="sample-status" class="form-control input-sm" name="sample_status[value]">
                                <option value="rejected">Rejected Sample</option>                                
                                <option value="urgent">Urgent Sample</option>
                                <?php 
                                    for($i = 1 ; $i < count($FOR_RESEARCH); $i++){
                                        echo '<option value="'.$i.'">'.$FOR_RESEARCH[$i].'</option>';
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="sample_status" name="sample_status[is_show]" checked></td>
                    </tr>
                    <!-- 14022022 reject comment -->
					<tr class="query-field hide">
                        <td class="text-right title"><?php echo _t('data_query.reject_comment'); ?></td>
                        <td></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="reject_comment" name="reject_comment[is_show]"></td>
                    </tr>
                    <!-- End -->
                    <tr class="query-field hidden" id="for_research_value_wrapper">
                            <td>&nbsp;</td>
                            <td colspan="2">
                                <?php
                                    $app_lang	= empty($app_lang) ? 'en' : $app_lang;
                                    if($app_lang == 'en'){
                                        $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY);
                                    }else{
                                        $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
                                    }
                                    echo form_dropdown('for_research_value', $FOR_RESEARCH_ARR,'', 'class="form-control"');
                                ?>
                            </td>
                    </tr>

                    <!-- Payment type -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.payment_type'); ?></td>
                        <td>
                            <select id="payment-type" class="form-control input-sm" name="payment_type[value]">
                            <?php
                            if (isset($payment_types)) {
                                foreach ($payment_types as $payment_type) {
                                    echo "<option value='".$payment_type['id']."'>".$payment_type['name']."</option>";
                                }
                            }
                            ?>
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="payment_type" name="payment_type[is_show]" checked></td>
                    </tr>
                    <!-- Sample Description -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.sample_description'); ?></td>
                        <td>
                            <select id="sample-description" class="form-control input-sm" name="sample_description[value][]" multiple>
                                <?php
                                if (isset($sample_descriptions)) {
                                    foreach ($sample_descriptions as $sample_description) {
                                        echo "<option value='".$sample_description['ID']."'>".$sample_description['description']."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="sample_description" name="sample_description[is_show]" checked></td>
                    </tr>
                    <tr class="query-field hide">
                        <td class="text-right title"><?php echo _t('data_query.volume1'); ?></td>
                        <td></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="volume1" name="volume1[is_show]" checked></td>
                    </tr>
					<tr class="query-field hide">
                        <td class="text-right title"><?php echo _t('data_query.volume2'); ?></td>
                        <td></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="volume2" name="volume2[is_show]" checked></td>
                    </tr>
                    <!-- Sample Number -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.sample_number'); ?></td>
                        <td><input type="text" class="form-control input-sm" id="sample-number" name="sample_number[value]"></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="sample_number" name="sample_number[is_show]" checked></td>
                    </tr>

                    <!-- Diagnosis -->
					<tr class="query-field hide">
                        <td class="text-right title"><?php echo _t('data_query.diagnosis'); ?></td>
                        <td></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="diagnosis" name="diagnosis[is_show]" checked></td>
                    </tr>

                    <!-- Sample Source -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.sample_source'); ?></td>
                        <td>
                            <select id="sample-source" class="form-control input-sm" name="sample_source[value][]" multiple>
                                <?php
                                    if (isset($sample_sources)) {
                                        foreach ($sample_sources as $sample_source) {
                                            echo "<option value='".$sample_source->source_id."'>".$sample_source->source_name."</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="sample_source" name="sample_source[is_show]" checked></td>
                    </tr>
                    <!-- Requester -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.requester'); ?></td>
                        <td>
                            <select id="requester" class="form-control input-sm" name="requester[value][]" multiple>
                                <?php
                                if (isset($requesters)) {
                                    foreach ($requesters as $requester) {
                                        echo "<option value='".$requester->requester_id."'>".$requester->requester_name."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="requester" name="requester[is_show]" checked></td>
                    </tr>

                    <!-- Test Name -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.test_name'); ?></td>
                        <td>
                            <select id="test-name" class="form-control input-sm" name="test[value][]" multiple>
                                <?php
                                if (isset($tests)) {
                                    foreach ($tests as $test) {
                                        echo "<option value='".$test->test_id."'>".$test->test_name."</option>";
                                    }
                                }
                                
                                /*
                                if (isset($group_results)) {
                                    foreach ($group_results as $group_result) {
                                        echo "<option value='".$group_result['group_result']."'>".$group_result['group_result']."</option>";
                                    }
                                }
                                */
                                ?>
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="test" name="test[is_show]" checked></td>
                    </tr>
                    <!-- Organism -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.result_organism'); ?></td>
                        <td>
                            <select id="organism" class="form-control input-sm" name="result_organism[value][]" multiple>
                                <?php
                                if (isset($organisms)) {
                                    foreach ($organisms as $organism) {
                                        $value = "";
                                        if ($organism->organism_value == ORGANISM_POSITIVE) $value = " Positive";
                                        else if ($organism->organism_value == ORGANISM_NEGATIVE) $value = " Negative";
                                        echo "<option value='".$organism->ID."'>".$organism->organism_name.$value."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="result_organism" name="result_organism[is_show]" checked></td>
                    </tr>
                    <tr class="query-field hide">
                        <td class="text-right title"><?php echo _t('data_query.min'); ?></td>
                        <td></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="min_val" name="min_val[is_show]" checked></td>
                    </tr>
					<tr class="query-field hide">
                        <td class="text-right title"><?php echo _t('data_query.max'); ?></td>
                        <td></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="max_val" name="max_val[is_show]" checked></td>
                    </tr>
					<tr class="query-field hide">
                        <td class="text-right title"><?php echo _t('data_query.unit'); ?></td>
                        <td></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="unit" name="unit[is_show]" checked></td>
                    </tr>
                    <!-- Antibiotic -->
                    <tr class="query-field hide">
                        <td class="text-right title"><?php echo _t('data_query.MIC'); ?></td>
                        <td></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="MIC" name="MIC[is_show]" checked></td>
                    </tr>
                    <tr class="query-field hide">
                        <td class="text-right title"><?php echo _t('data_query.DD'); ?></td>
                        <td></td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="DD" name="DD[is_show]" checked></td>
                    </tr>
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.antibiotic'); ?></td>
                        <td>
                            <select id="antibiotic" class="form-control input-sm" name="antibiotic[value][]" multiple>
                                <?php
                                if (isset($antibiotics)) {
                                    foreach ($antibiotics as $antibiotic) {
                                        echo "<option value='".$antibiotic->ID."'>".$antibiotic->antibiotic_name."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="antibiotic" name="antibiotic[is_show]" checked></td>
                    </tr>
                    <!-- Sensitivity -->
                    <tr class="query-field">
                        <td class="text-right title"><?php echo _t('data_query.sensitivity'); ?></td>
                        <td>
                            <select id="sensitivity" class="form-control input-sm" name="sensitivity[value]">
                                <option value="<?php echo ANTIBIOTIC_SENSITIVE ?>">Sensitive</option>
                                <option value="<?php echo ANTIBIOTIC_RESISTANT ?>">Resistant</option>
                                <option value="<?php echo ANTIBIOTIC_INTERMEDIATE ?>">Intermediate</option>
                            </select>
                        </td>
                        <td class="text-center"><input type="checkbox" value="1" class="show-field" data-name="sensitivity" name="sensitivity[is_show]" checked></td>
                    </tr>                    
                </table>
            </form>
        </div>
        <div class="col-sm-7 col-md-8">
            <h4 class="text-blue"><i class="fa fa-list-alt"></i>&nbsp;&nbsp;<?php echo _t('data_query.result'); ?></h4>
            <div style="margin-top: 16px;">
                <button class="btn btn-primary btn-flat btn-sm" id="btnGenerate"><?php echo _t('data_query.generate'); ?></button>
                <button class="btn btn-success btn-flat btn-sm" id="btnExportExcel" disabled><?php echo _t('data_query.export_excel'); ?></button>
                <button class="btn btn-danger btn-flat btn-sm" id="btnReset"><?php echo _t('data_query.reset'); ?></button>
            </div>
            <hr>
            <table class="table table-bordered table-striped" id="tbl-result" style="min-width: 100%;">
            </table>
        </div>
    </div>
</div>
