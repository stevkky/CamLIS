<?php if (!isset($patient_sample) || !$patient_sample || !isset($patient) || !$patient): ?>
    <div class="col-sm-12">
        <div class="well text-center text-red" id="no-result"><b><?php echo _t('global.no_result'); ?></b></div>
    </div>
<?php else: ?>
    <div class="col-sm-12">
        <div class="patient-info">
            <div class="col-sm-12">
                <div class="row">
                    <!-- =================================Patient information=============================-->
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="control-label">
                                    <?php echo _t('patient.patient_id'); ?>
                                </label>
                            </div>
                            <div class="col-sm-1">
                                <label class="control-label">:</label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $patient['patient_code']; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="control-label">
                                    <?php echo _t('patient.patient_name'); ?>
                                </label>
                            </div>
                            <div class="col-sm-1 info-label">
                                <label class="control-label">:</label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $patient['name']; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="control-label">
                                    <?php echo _t('patient.sex'); ?>
                                </label>
                            </div>
                            <div class="col-sm-1 info-label">
                                <label class="control-label">:</label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $patient['sex'] == 'F' ? _t('global.female') : _t('global.male'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="control-label">
                                    <?php echo _t('patient.age'); ?>
                                </label>
                            </div>
                            <div class="col-sm-1 info-label">
                                <label class="control-label">:</label>
                            </div>
                            <div class="col-sm-8">
                                <?php $age = calculateAge($patient['dob']); ?>
                                <span class="age-year"></span> <?php echo $age->y.' '._t('global.year') ?> &nbsp;
                                <span class="age-month"></span> <?php echo $age->m.' '._t('global.month') ?> &nbsp;
                                <span class="age-day"></span> <?php echo ($age->days > 0 ? $age->d : 1).' '._t('global.day') ?>
                            </div>
                        </div>
                    </div>
                    <!-- =================Sample information=================================== -->
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-3 info-label">
                                <label class="control-label">
                                    <?php echo _t('request.sample_number'); ?>
                                </label>
                            </div>
                            <div class="col-sm-1 info-label">
                                <label class="control-label">:</label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $patient_sample['sample_number']; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 info-label">
                                <label class="control-label">
                                    <?php echo _t('request.sample_source'); ?>
                                </label>
                            </div>
                            <div class="col-sm-1 info-label">
                                <label class="control-label">:</label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $patient_sample['sample_source_name']; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 info-label">
                                <label class="control-label">
                                    <?php echo _t('request.requested_by'); ?>
                                </label>
                            </div>
                            <div class="col-sm-1 info-label">
                                <label class="control-label">:</label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $patient_sample['requester_name']; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 info-label">
                                <label class="control-label">
                                    <?php echo _t('request.requested_date'); ?>
                                </label>
                            </div>
                            <div class="col-sm-1 info-label">
                                <label class="control-label">:</label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $patient_sample['test_date']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--=====================Sample list collection================== -->
    <div class="col-sm-12">
        <div class="sample-form-wrapper">
            <h4 class="content-header">
                <?php echo _t('request.collect_sample'); ?>
            </h4>
            <hr>
            <input type="hidden" id="patient_sample_id" value="<?php echo $patient_sample['patient_sample_id']; ?>">
            <!-- <span class="pull-right"><input type="text" name="collected_date"></span> -->
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-4">
                        <label class="control-label">
                            <?php echo _t('request.collect_dt'); ?>
                            <sup class="fa fa-asterisk" style="font-size:8px"></sup>
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control dtpicker" name="collected_date">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" class="form-control coltimepicker narrow-padding" name="collected_time" style="width: 100px;">
                            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <label class="control-label">&nbsp;</label>
                        <div class="input-group">
                            <button type="submit" id="collect" class="btn btn-flat btn-primary col-sm-12">
                                <?php echo _t('request.collect_sample'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="test-list" style="padding-top: 15px;">
                            <ul>
                                <?php foreach ($patient_sample_test_groups as $patient_sample_test_group): ?>
                                    <?php $header = ""; $tests  = []; ?>
                                    <?php foreach ($patient_sample_test_group as $patient_sample_test): ?>
                                        <?php
                                            $header = $patient_sample_test['department_name']."-".$patient_sample_test['sample_name'];
                                            $tube   = $patient_sample_test['tube']; 
                                            $tests[] = $patient_sample_test['group_result'];
                                        ?>
                                    <?php endforeach ?>
                                    <li class="header">
                                        <b><?php echo $header; ?></b>
                                        <ul>
                                            <li>
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <?php echo implode(',.........', $tests); ?>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <img src="<?php echo base_url(); ?>uploads/tubes/<?php echo $tube; ?>">
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>