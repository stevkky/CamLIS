<!-- Sample Entry Form Template -->
<div id="sample-form-template" style="display:none;">
    <div class="panel panel-default sample-form">
        <form action="" method="post" class="form-vertical frm-sample-entry">
            <div class="panel-heading">
                <div class="header"><?php echo _t('global.sample'); ?> <span class="sample-order"></span></div>
                <div class='sample-title' style="display: none;">
                    <i class="fa fa-hand-o-right" aria-hidden="true"></i>&nbsp;
                    <div style="margin-left: 0; display: none;" class="sample-number-title">
                        <b><?php echo _t('sample.sample_number')." : "; ?></b>
                        <b class="value text-blue"></b>
                    </div>&nbsp;&nbsp;
                    <div class="collected-date-title" style="display: none;">
                        <b><?php echo _t('sample.collect_dt')." : "; ?></b>
                        <b class="value text-blue"></b>
                    </div>&nbsp;&nbsp;
                    <div class="received-date-title" style="display: none;">
                        <b><?php echo _t('sample.receive_dt')." : "; ?></b>
                        <b class="value text-blue"></b>
                    </div>
                </div>
                <a href="#" class="btnMinimized pull-right"><i class="fa fa-minus"></i></a>
            </div>
            <div class="panel-body">
                <?php
                    $disabled = $this->session->userdata('laboratory')->sample_number == 2 ? "" : "disabled";
                ?>
                <div class='col-lg-10'>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="sample-number" class="control-label hint--right hint--error hint--always"><?php echo _t('sample.sample_number'); echo $disabled == '' ? ' <sup class="fa fa-asterisk" style="font-size:8px"></sup>' : ''; ?></label>
                            <!-- <input type="text" onkeypress="return isNumber(event)" class="form-control" tabindex="1" name="sample_number" maxlength="5" <?php echo $disabled; echo $disabled=='' ? ' data-required="yes"' : ' data-required="no"'; ?> > -->
                            <input type="text" class="form-control" onkeydown="return /[-0-9a-zA-Z]/i.test(event.key)" tabindex="1" name="sample_number" <?php echo $disabled; echo $disabled=='' ? ' data-required="yes"' : ' data-required="no"'; ?> >
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
                                    echo "<option value='$sc->source_id'>$sc->source_name</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <!-- Requester -->
                        <div class="col-md-4">
                            <label class="control-label"><?php echo _t('sample.requester'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
                            <select name="requester" class="form-control" tabindex="3">
                                <option value="-1"><?php echo _t('global.choose'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="control-label"><?php echo _t('sample.collect_dt'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
                            <div class="input-group">
                                <input type="text" class="form-control dtpicker" name="collected_date" tabindex="4">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" class="form-control coltimepicker narrow-padding" name="collected_time" style="width: 100px;" tabindex="5">
                                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">
                                <?php echo _t('sample.receive_dt'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
                            <div class="input-group">
                                <input type="text" class="form-control dtpicker" name="received_date" tabindex="6">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" class="form-control rectimepicker narrow-padding" name="received_time" style="width: 100px;" tabindex="7">
                                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label"><?php echo _t('sample.payment_type'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
                            <select name="payment_type" class="form-control" tabindex="8">
                                <option value="-1"><?php echo _t('global.choose'); ?></option>
                                <?php
                                    foreach ($payment_types as $payment_type) {
                                        echo "<option value='".$payment_type['id']."'>".$payment_type['name']."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="control-label"><?php echo _t('sample.admission_date'); ?></label>
                            <div class="input-group">
                                <input type="text" class="form-control admission-date" name="admission_date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" class="form-control admission-time narrow-padding" name="admission_time" style="width: 100px;">
                                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label">
                                <?php echo _t('sample.clinical_history'); ?>
                            </label>
                            <textarea name="clinical_history" cols="30" rows="1" tabindex="10" class="form-control" style="resize:none;"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">
                                <?php echo _t('sample.reason_for_testing'); ?>
                            </label>
                            <div style="margin-top:3px;">
                                <div class="checkbox-wrapper" style="margin-bottom: 3px;">
                                    <label class='checkbox-inline'>
                                        <input type="checkbox" value="1" name="is_urgent" tabindex="11">
                                        <?php echo _t('sample.urgent'); ?>
                                    </label>
                                </div>
                                <div class="checkbox-wrapper" style="margin-bottom: 3px; padding-right: 0px;">                                    
                                    <?php
                                        $app_lang	= empty($app_lang) ? 'en' : $app_lang;
                                        if($app_lang == 'en'){
                                            $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY);
                                        }else{
                                            $FOR_RESEARCH_ARR = unserialize(FOR_RESEARCH_FIELD_ARRAY_KH);
                                        }
                                        echo form_dropdown('for_research', $FOR_RESEARCH_ARR, '', 'class="form-control"');
                                    ?>
                                </div>
                            </div>
                        </div>  
                        <!-- 20 April 2021 -->
                        <div class="col-md-2">
                            <label class="control-label"><?php echo _t('sample.number_of_sample'); ?></label>
                            <?php
                                $NUMBER_OF_SAMPLE_DD = unserialize(NUMBER_OF_SAMPLE_DD);
                                echo form_dropdown('number_of_sample', $NUMBER_OF_SAMPLE_DD,'', 'class="form-control"');
                            ?>
                        </div>                     
                    </div>
                    <!-- 
                                Covid Form
                                ADDED: 02 DEC 2020
                                -->
                    <div class="row">
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
                                <input type="text" name="completed_by" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="control-label">
                                    <?php echo _t('sample.telephone'); ?>
                                </label>
                                <input type="text" name="phone_number" class="form-control">
                            </div>                        
                            <div class="col-md-3">
                                <label class="control-label">
                                    <?php echo _t('sample.sample_collector'); ?>
                                </label>
                                <input type="text" name="sample_collector" class="form-control">
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
                                            echo "<option value=".$item->ID.">".$item->name_kh."</option>";
                                        }
                                    }
                                ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">
                                    <?php echo _t('sample.health_facility'); ?>
                                </label>
                                <input type="text" name="health_facility" class="form-control" value="">
                            </div>
                        </div>
                        
                    </div>
                    <!-- End-->
                </div>
                <div class="col-lg-2 btn-wrapper">
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
                        <button type="button" class="btn btn-danger btnRejectSample margintop10" tabindex="15" action-type='save-reject' disabled>
                            <span class="glyphicon glyphicon-minus-sign"></span>&nbsp;</i><?php echo _t('sample.rejected'); ?>
                        </button>
                        <?php } if ($this->aauth->is_allowed('add_psample_result')) { ?>
                        <button type="button" class="btn btn-primary btnAddResult margintop10" tabindex="16" disabled>
                            <i class="fa fa-pencil"></i>&nbsp;<?php echo _t('sample.add_result'); ?>
                        </button>
                        <?php } ?>
                        <?php if ($this->aauth->is_allowed('print_psample_result')) { ?>
                        <button type="button" class="btn btn-success btnPreview margintop10" tabindex="17" disabled>
                            <i class="fa fa-eye" aria-hidden="true"></i>&nbsp;<?php echo _t('sample.preview_result'); ?>
                        </button>
                        <?php } ?>
                        <?php if ($this->aauth->is_allowed('delete_psample')) { ?>
                        <button type="button" class="btn btn-danger btnRemove margintop10" tabindex="18"><i class="fa fa-trash"></i>&nbsp;
                            <?php echo _t('global.remove'); ?>
                        </button>
                        <?php } ?>
                        <!-- ADDED 10 Jan 2020-->
                        <button type="button" class="btn btn-success btnPreviewCovidForm margintop10" tabindex="19" disabled><i class="fa fa-eye"></i>&nbsp;
                            <?php echo _t('sample.preview_request'); ?>
                        </button>
                        <!-- End-->
                    </div>
                </div>
                <div class="col-sm-12 sample-user-info">
                    <div class="well">
                        <b><i class="fa fa-user"></i>&nbsp;<?php echo _t('sample.sample_entry_by'); ?> : </b>
                        <div class="sample-entry-user-list">
                            <span class="sample-entry-user label label-primary template hide"></span>
                            <span class='no-result'>N/A</span>
                        </div>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <b><i class="fa fa-user"></i>&nbsp;<?php echo _t('sample.result_entry_by'); ?> : </b>
                        <div class="result-entry-user-list">
                            <span class="result-entry-user label label-primary template hide"></span>
                            <span class='no-result'>N/A</span>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="patient_sample_id" data-value="">
        </form>
    </div>
</div>