<script>
    var msg_required_data = "<?php echo _t('global.msg.fill_required_data'); ?>";
    var label_all = "<?php echo _t('all'); ?>";
    var label_choose = "<?php echo _t('global.choose'); ?>";
    var label_laboratory = "<?php echo _t('laboratories'); ?>";
    var label_department_sammple = "<?php echo _t('department_sample'); ?>";
    var label_test = "<?php echo _t('test'); ?>";
    var label_possible_result = "<?php echo _t('possible_result'); ?>";

    const SAMPLE_TESTS = <?php echo json_encode($sample_tests); ?>;
    const POSSIBLE_RESULTS = <?php echo json_encode($possible_results ); ?>;
</script>
<div class="col-sm-3 condition-list">
    <div class="header">
        <i class="fa fa-cogs"></i>&nbsp;<?php echo _t('condition_for_generation'); ?>
    </div>
    <div class="content" style="height: 515px;">
        <div class="condition-wrapper">
            <div class="condition-label"><?php echo _t('type_of_report'); ?> *</div>
            <div class="condition">
                <select name="graph_type" id="graph-type" class="form-control">
                    <option value="NUMBER_PATIENT_BY_ADDRESS"><?php echo _t('graph_1'); ?></option>
                    <option value="NUMBER_TEST_BY_ADDRESS"><?php echo _t('graph_2'); ?></option>
                    <option value="NUMBER_TEST_BY_LABORATORY"><?php echo _t('graph_3'); ?></option>
                </select>
            </div>
        </div>
        <div class="condition-wrapper">
            <div class="condition-label"><?php echo _t('laboratory'); ?> *</div>
            <div class="condition">
                <select name="laboratory" id="laboratory" class="form-control" multiple style="height: 34px;">
                    <?php
                        foreach($laboratories as $laboratory) {
                            $name = 'name_'.$app_lang;
                            echo "<option value='".$laboratory->labID."'>".$laboratory->$name."</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="condition-wrapper">
            <div class="condition-label"><?php echo _t('received_date'); ?></div>
            <div class="condition">
                <div class="input-group" style="margin-bottom: 3px;">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" class="form-control start-date" id="start-date" placeholder="<?php echo _t('report.start_date'); ?>" required>
                </div>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" class="form-control end-date" id="end-date" placeholder="<?php echo _t('report.end_date'); ?>" required>
                </div>
            </div>
        </div>
        <div class="condition-wrapper department-sample" style="display: none;">
            <div class="condition-label"><?php echo _t('department_sample'); ?></div>
            <div class="condition">
                <select name="department_sample" id="department-sample" class="form-control" multiple>
                    <?php
                    foreach ($department_samples as $department_sample) {
                        echo "<option value='".$department_sample->department_sample_id."'>".$department_sample->department_name." → ".$department_sample->sample_name."</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="condition-wrapper sample-test" style="display: none;">
            <div class="condition-label"><?php echo _t('test'); ?></div>
            <div class="condition">
                <select name="sample_test" id="sample-test" class="form-control" multiple>
                    <?php
                    /*foreach ($sample_tests as $sample_test) {
                        echo "<option value='".$sample_test->sample_test_id."'>".$sample_test->test_name."</option>";
                    }*/
                    ?>
                </select>
            </div>
        </div>
        <div class="condition-wrapper sample-test-result" style="display: none;">
            <div class="condition-label"><?php echo _t('possible_result'); ?></div>
            <div class="condition">
                <select name="sample_test_result" id="sample-test-result" class="form-control" multiple>
                    <?php
                    /*foreach ($possible_results as $possible_result) {
                        echo "<option value='".$possible_result->test_organism_id."'>".$possible_result->organism_name."</option>";
                    }*/
                    ?>
                </select>
            </div>
        </div>
    </div>
    <div class="footer">
        <button class="btn btn-primary btn-flat" id="btn-generate"><?php echo _t('generate_report'); ?></button>
        <button class="btn btn-default btn-flat"><?php echo _t('reset'); ?></button>
    </div>
</div>
<div class="col-sm-9">
    <h4 class="text-blue"><i class="fa fa-bar-chart"></i>&nbsp;<?php echo _t('result'); ?></h4>
    <hr>
    <div id="mapdiv" style="width: 100%; background-color:#EEEEEE; height: 500px;"></div>
</div>