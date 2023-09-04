<div id="sample_form_template" style="display: none;">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default sample-form">
                    <form method="post" class="form-vertical form-sample-entry">
                        <div class="panel-heading">
                            <div class="header">
                                <?php echo _t('global.sample'); ?>
                                <span class="sample-order"></span>
                            </div>
                            <div class="sample-title" style="display: none;">
                                <i class="fa fa-hand-o-right" aria-hidden="true"></i>&nbsp;
                                <div style="margin-left: 0; display: none;" class="sample-number-title">
                                    <b><?php echo _t('request.sample_number')." : "; ?></b>
                                    <b class="value text-blue"></b>
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
                                            <select name="sample_source" class="form-control" tabindex="1">
                                                <option value="-1" style="color:#d8d5d5;">
                                                    <?php echo _t('global.choose'); ?>
                                                </option>
                                                <?php foreach ($sample_source as $sc): ?>
                                                    <option value="<?php echo $sc->source_id; ?>">
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
                                            <select name="requester" class="form-control" tabindex="2">
                                                <option value="-1">
                                                    <?php echo _t('global.choose'); ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                <?php echo _t('request.payment_type'); ?>
                                                <sup class="fa fa-asterisk" style="font-size:8px"></sup>
                                            </label>
                                            <select name="payment_type" class="form-control" tabindex="3">
                                                <option value="-1">
                                                    <?php echo _t('global.choose'); ?>
                                                </option>
                                                <?php foreach ($payment_types as $payment_type): ?>
                                                    <option value="<?php echo $payment_type['id']; ?>">
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
                                            <input type="text" class="form-control admission-date" name="admission_date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control admission-time narrow-padding" name="admission_time" style="width: 60px;">
                                            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                <?php echo _t('request.clinical_history'); ?>
                                            </label>
                                            <textarea name="clinical_history" cols="30" rows="1" tabindex="10" class="form-control" style="resize:none;"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="control-label">
                                            <?php echo _t('request.reason_for_testing'); ?>                                            
                                        </label>
                                        <div class="form-group">
                                            <div style="margin-top:3px;">
                                                <div class="checkbox-wrapper" style="margin-bottom: 3px;">
                                                    <label class='checkbox-inline'>
                                                        <input type="checkbox" value="1" name="is_urgent" tabindex="11">
                                                        <?php echo _t('request.urgent'); ?>
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
                                        <span class='no-result'>N/A</span>
                                    </div>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <b><i class="fa fa-user"></i>&nbsp;<?php echo _t('request.result_entry_by'); ?> : </b>
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
        </div>
    </div>
</div>