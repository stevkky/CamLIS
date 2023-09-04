<script type="text/javascript">
    const TEST_PAYMENTS         = <?php echo json_encode($test_payments); ?>;
    const PATIENT_SAMPLE        = <?php echo json_encode($patient_sample); ?>;
    var patient                 = <?php echo json_encode($patient); ?>;
    var sample_descriptions     = <?php echo json_encode($sample_descriptions); ?>;
    var page_action             = "<?php echo $page_action ?>";
    var msg_required_data       = "<?php echo _t('global.msg.fill_required_data');          ?>";
    var msg_must_select_test    = "<?php echo _t('request.msg.must_select_test');            ?>";
    var msg_dob_not_after_now   = "<?php echo _t('request.msg.dob_not_after_now');           ?>";
    var label_sample            = "<?php echo _t('request.sample_type');                     ?>";
    var label_sample_desription = "<?php echo _t('request.sample_desc');                     ?>";
    var label_weight1           = "<?php echo _t('request.weight1');                         ?>";
    var label_weight2           = "<?php echo _t('request.weight2');                         ?>";
    var msg_loading             = "<?php echo _t('global.plz_wait');                        ?>";
    var msg_save_fail           = "<?php echo _t('global.msg.save_fail');                   ?>";
    var q_delete_patient_sample = "<?php echo _t('request.q.delete_patient_sample');        ?>";
    var msg_delete_fail         = "<?php echo _t('global.msg.delete_fail');                 ?>";
</script>

<?php if (!isset($patient_sample) || !$patient_sample || !isset($patient) || !$patient): ?>
    <div class="col-sm-12">
        <div class="well text-center text-red" id="no-result"><b><?php echo _t('global.no_result'); ?></b></div>
    </div>
<?php else: ?>
    <div class="col-sm-12">
        <!--===============Display patient information============-->
        <div class="row" id="display_patient_information">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-2">
                        <label class="control-label">
                            <?php echo _t('patient.patient_id'); ?>
                        </label>
                    </div>
                    <div class="col-sm-1">:</div>
                    <div class="col-sm-9 patient-code">
                        <?php echo $patient['patient_code']; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <label class="control-label">
                            <?php echo _t('patient.patient_name'); ?>
                        </label>
                    </div>
                    <div class="col-sm-1">:</div>
                    <div class="col-sm-9 patient-name">
                        <?php echo $patient['name']; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <label class="control-label">
                            <?php echo _t('patient.sex'); ?>
                        </label>
                    </div>
                    <div class="col-sm-1">:</div>
                    <div class="col-sm-9 patient-gender">
                        <?php echo $patient['sex'] == 'F' ? _t('global.female') : _t('global.male'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <label class="control-label">
                            <?php echo _t('patient.age'); ?>
                        </label>
                    </div>
                    <div class="col-sm-1">:</div>
                    <div class="col-sm-9 patient-age">
                        <?php $age = calculateAge($patient['dob']); ?>
                        <span class="patient-age-year"><?php echo $age->y; ?></span> <?php echo _t('global.year') ?> &nbsp;
                        <span class="patient-age-month"><?php echo $age->m; ?></span> <?php echo _t('global.month') ?> &nbsp;
                        <span class="patient-age-day"><?php echo ($age->days > 0) ? $age->d : '1'; ?></span> <?php echo _t('global.day') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <label class="control-label">
                            <?php echo _t('patient.phone'); ?>
                        </label>
                    </div>
                    <div class="col-sm-1">:</div>
                    <div class="col-sm-9 patient-phone">
                        <?php echo $patient['phone']; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <label class="control-label">
                            <?php echo _t('patient.address'); ?>
                        </label>
                    </div>
                    <div class="col-sm-1">:</div>
                    <div class="col-sm-9 patient-address">
                        <?php 
                            $village_name  = 'village_'.$app_lang;
                            $commune_name  = 'commune_'.$app_lang;
                            $district_name = 'district_'.$app_lang;
                            $province_name = 'province_'.$app_lang;
                        ?>
                        <span class="patient-address-village"><?php echo $patient[$village_name]; ?></span> <?php echo _t('Village').' -'; ?>&nbsp;
                        <span class="patient-address-commune"><?php echo $patient[$commune_name]; ?></span> <?php echo _t('Commune').' -'; ?>&nbsp;
                        <span class="patient-address-district"><?php echo $patient[$district_name]; ?></span> <?php echo _t('District').' -'; ?>
                        <span class="patient-address-province"><?php echo $patient[$province_name]; ?></span> <?php echo _t('Province') ?>
                    </div>
                </div>
            </div>
        </div>
        <!--=============Patient entry form===============-->
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
                <input type="hidden" id="patient_id" data-value ="<?php echo $patient_sample['patient_id']; ?>">
                <input type="hidden" id="patient_age" data-value ="<?php echo getAge($patient['dob']); ?>">
                <input type="hidden" id="patient_sex" data-value ="<?php echo $patient['sex']; ?>">
            </form>
        </div>
    </div>
    <!--=================Patient sample entry form==============-->
    <div class="col-sm-12">
        <div class="row" id="patient_sample_entry_form_wrapper">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12">
                        <h4 class="content-header"><?php echo _t('request.edit_sample'); ?></h4>
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div id="patient_sample_form">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="panel panel-default sample-form edit">
                                        <form method="post" class="form-vertical form-sample-entry">
                                            <div class="panel-heading">
                                                <div class="header">
                                                    <?php echo _t('global.sample'); ?>
                                                    <span class="sample-order">1</span>
                                                </div>
                                                <div class="sample-title">
                                                    <i class="fa fa-hand-o-right" aria-hidden="true"></i>&nbsp;
                                                    <div style="margin-left: 0;" class="sample-number-title">
                                                        <b><?php echo _t('request.sample_number')." : "; ?></b>
                                                        <b class="value text-blue"><?php echo $patient_sample['sample_number']; ?></b>
                                                    </div>&nbsp;&nbsp;
                                                    <div class="received-date-title" style="display: none;">
                                                        <b><?php echo _t('request.receive_dt')." : "; ?></b>
                                                        <b class="value text-blue"></b>
                                                    </div>
                                                </div>
                                                <a href="#" class="button-minimized pull-right">
                                                    <i class="fa fa-minus"></i>
                                                </a>
                                            </div>
                                            <div class="panel-body">
                                                <div class="col-sm-10">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label class="control-label">
                                                                    <?php echo _t('request.sample_source'); ?>
                                                                    <sup class="fa fa-asterisk" style="font-size:8px"></sup>
                                                                </label>
                                                                <select name="sample_source" class="form-control">
                                                                    <option value="-1" style="color:#d8d5d5;">
                                                                        <?php echo _t('global.choose'); ?>
                                                                    </option>
                                                                    <?php foreach ($sample_source as $sc): ?>
                                                                        <option value="<?php echo $sc->source_id; ?>" <?php echo ($sc->source_id == $patient_sample['sample_source_id']) ? 'selected' : ''; ?>>
                                                                            <?php echo $sc->source_name; ?>
                                                                        </option>
                                                                    <?php endforeach ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label class="control-label">
                                                                    <?php echo _t('request.requester'); ?>
                                                                    <sup class="fa fa-asterisk" style="font-size:8px"></sup>
                                                                </label>
                                                                <select name="requester" class="form-control">
                                                                    <option value="-1"><?php echo _t('global.choose'); ?></option>
                                                                    <?php foreach ($requesters as $requester): ?>
                                                                        <option value="<?php echo $requester->requester_id; ?>" <?php echo ($requester->requester_id == $patient_sample['requester_id']) ? 'selected': ''; ?>>
                                                                            <?php echo $requester->requester_name; ?>
                                                                        </option>
                                                                    <?php endforeach ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label class="control-label">
                                                                    <?php echo _t('request.payment_type'); ?>
                                                                    <sup class="fa fa-asterisk" style="font-size:8px"></sup>
                                                                </label>
                                                                <select name="payment_type" class="form-control">
                                                                    <option value="-1">
                                                                        <?php echo _t('global.choose'); ?>
                                                                    </option>
                                                                    <?php foreach ($payment_types as $payment_type): ?>
                                                                        <option value="<?php echo $payment_type['id']; ?>" <?php echo ($payment_type['id'] == $patient_sample['payment_type_id']) ? 'selected' : ''; ?>>
                                                                            <?php echo $payment_type['name']; ?>
                                                                        </option>
                                                                    <?php endforeach ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label class="control-label">
                                                                <?php echo _t('request.admission_date'); ?>
                                                            </label>
                                                            <div class="input-group">
                                                                <?php
                                                                    $admission_date = DateTime::createFromFormat('Y-m-d H:i:s', $patient_sample['admission_date']);
                                                                    $admission_time = $admission_date ? $admission_date->format('H:i') : "";
                                                                    $admission_date = $admission_date ? $admission_date->format('d/m/Y') : "";
                                                                ?>
                                                                <input type="text" class="form-control admission-date" value="<?php echo $admission_date; ?>" name="admission_date">
                                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                <input type="text" class="form-control admission-time narrow-padding" name="admission_time" value="<?php echo $admission_time; ?>" style="width: 60px;">
                                                                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label class="control-label">
                                                                    <?php echo _t('request.clinical_history'); ?>
                                                                </label>
                                                                <textarea name="clinical_history" cols="30" rows="1" class="form-control" style="resize:none;"><?php echo $patient_sample['clinical_history']; ?></textarea>
                                                            </div>
                                                        </div>
                                                        <!-- sample status -->
                                                        <div class="col-sm-4">
                                                            <label class="control-label">
                                                                <?php echo _t('request.reason_for_testing'); ?>
                                                            </label>
                                                            <div class="form-group">
                                                                <div style="margin-top:3px;">
                                                                    <div class="checkbox-wrapper" style="margin-bottom: 3px;">
                                                                        <label class='checkbox-inline'>
                                                                            <input type="checkbox" value="1" <?php echo ($patient_sample['is_urgent'] == 1) ? 'checked' : ''; ?> name="is_urgent" >
                                                                            <?php echo _t('request.urgent'); ?>
                                                                        </label>
                                                                    </div>
                                                                    <div class="checkbox-wrapper" style="margin-bottom: 3px; padding-right: 0px">
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

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2 btn-wrapper">
                                                    <div class="form-group">
                                                        <?php if ($this->aauth->is_allowed('add_psample') || $this->aauth->is_allowed('edit_psample')): ?>
                                                            <button type="button" class="btn btn-primary btn-show-test-modal margin-top-20" disabled>
                                                                <i class="fa fa-list-alt"></i>&nbsp;
                                                                <?php echo _t('request.assign_test'); ?>
                                                            </button>
                                                            <button type="button" class="btn btn-primary btn-save-sample margintop10" disabled>
                                                                <i class="fa fa-floppy-o"></i>&nbsp;
                                                                <?php echo _t('global.save'); ?>
                                                            </button>
                                                        <?php endif ?>
                                                        <?php if ($this->aauth->is_allowed('delete_psample')): ?>
                                                            <button type="button" class="btn btn-danger btn-remove margintop10">
                                                                <i class="fa fa-trash"></i>&nbsp;
                                                                <?php echo _t('global.remove'); ?>
                                                            </button>
                                                        <?php endif ?>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 sample-user-info">
                                                    <div class="well">
                                                        <b><i class="fa fa-user"></i>&nbsp;<?php echo _t('request.sample_entry_by'); ?> : </b>
                                                        <div class="sample-entry-user-list">
                                                            <span class="sample-entry-user label label-primary template hide"></span>
                                                            <?php
                                                                $sample_entry_users = isset($patient_sample_user['sample_entry_user']) ? explode(',', $patient_sample_user['sample_entry_user']) : [];
                                                            ?>
                                                            <?php if (isset($patient_sample_user['sample_entry_user']) && !empty($patient_sample_user['sample_entry_user']) && count($sample_entry_users) > 0): ?>
                                                                <?php foreach ($sample_entry_users as $s_username): ?>
                                                                    <span class="sample-entry-user label label-primary user">
                                                                        <?php echo $s_username; ?>
                                                                    </span>
                                                                <?php endforeach ?>
                                                            <?php else: ?>
                                                                <span class='no-result'>N/A</span>
                                                            <?php endif ?>
                                                        </div>
                                                        <b><?php echo _t('request.entry_date'); ?> : </b>
                                                        <div class="sample-entry-user-list">
                                                            <span class="sample-entry-user label label-primary template hide"></span>
                                                            <?php if (isset($patient_sample_user['entryDate']) && !empty($patient_sample_user['entryDate'])): ?>
                                                                <span class="result-entry-user label label-primary user">
                                                                    <?php echo $patient_sample_user["entryDate"]; ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class='no-result'>N/A</span>
                                                            <?php endif ?>
                                                        </div>
                                                        <!-- modified by -->
                                                        <b><i class="fa fa-user"></i>&nbsp;<?php echo _t('request.modified_by'); ?> : </b>
                                                        <div class="result-entry-user-list">
                                                            <span class="result-entry-user label label-primary template hide"></span>
                                                            <?php if (isset($patient_sample_user['modifiedBy']) && !empty($patient_sample_user['modifiedBy'])): ?>
                                                                <span class="result-entry-user label label-primary user">
                                                                    <?php echo $patient_sample_user["modifiedBy"]; ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class='no-result'>N/A</span>
                                                            <?php endif ?>
                                                        </div>
                                                        <!-- modified date -->
                                                        <b><?php echo _t('request.modified_date'); ?> : </b>
                                                        <div class="result-entry-user-list">
                                                            <span class="result-entry-user label label-primary template hide"></span>
                                                            <?php if (isset($patient_sample_user['modifiedDate']) && !empty($patient_sample_user['modifiedDate'])): ?>
                                                                <span class="result-entry-user label label-primary user">
                                                                    <?php echo $patient_sample_user["modifiedDate"]; ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class='no-result'>N/A</span>
                                                            <?php endif ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="patient_sample_id" data-value="<?php echo $patient_sample['patient_sample_id']; ?>">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="button_add_more_sample" class="btn btn-flat btn-primary col-sm-12">
                            <i class="fa fa-plus"></i>
                            <?php echo _t('request.add_sample'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>