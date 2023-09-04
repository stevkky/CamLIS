<script>
    var msg_required_data = "<?php echo _t('global.msg.fill_required_data'); ?>";
    var label_all = "<?php echo _t('all'); ?>";
    var label_choose = "<?php echo _t('global.choose'); ?>";
    var label_laboratory = "<?php echo _t('laboratories'); ?>";
</script>
<style>
     #chartdiv {
         width		: 100%;
         height		: 600px;
         font-size	: 11px;
     }
</style>
<div class="col-sm-3 condition-list">
    <div class="header">
        <i class="fa fa-cogs"></i>&nbsp;<?php echo _t('condition_for_generation'); ?>
    </div>
    <div class="content">
        <div class="condition-wrapper">
            <div class="condition-label"><?php echo _t('type_of_report'); ?> *</div>
            <div class="condition">
                <select name="graph_type" id="graph-type" class="form-control">
                    <option value="PATIENT_BY_AGE_GROUP"><?php echo _t('graph_1'); ?></option>
                    <option value="PATIENT_BY_SAMPLE_SOURCE"><?php echo _t('graph_2'); ?></option>
                    <option value="PATIENT_BY_SAMPLE_TYPE"><?php echo _t('graph_3'); ?></option>
                    <option value="SAMPLE_TYPE_BY_MONTH"><?php echo _t('graph_4'); ?></option>
                    <option value="PATIENT_BY_DEPARTMENT"><?php echo _t('graph_5'); ?></option>
                    <option value="PATIENT_BY_MONTH"><?php echo _t('graph_6'); ?></option>
                    <option value="TEST_BY_MONTH"><?php echo _t('graph_7'); ?></option>
                </select>
            </div>
        </div>
        <div class="condition-wrapper">
            <div class="condition-label"><?php echo _t('laboratory'); ?> *</div>
            <div class="condition">
                <select name="laboratory" id="laboratory" class="form-control" multiple>
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
            <div class="condition-label"><?php echo _t('received_date'); ?> *</div>
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
        <div class="condition-wrapper sample-type" style="display: none;">
            <div class="condition-label"><?php echo _t('sample_type'); ?></div>
            <div class="condition">
                <select name="sample_type" id="sample-type" class="form-control">
                    <?php
                    foreach ($sample_types as $sample_type) {
                        echo "<option value='".$sample_type->ID."'>".$sample_type->sample_name."</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="condition-wrapper test" style="display: none;">
            <div class="condition-label"><?php echo _t('test'); ?></div>
            <div class="condition">
                <select name="test" id="test" class="form-control">
                    <?php
                    foreach ($tests as $test) {
                        echo "<option value='".$test->test_name."'>".$test->test_name."</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
    <div class="footer">
        <button class="btn btn-primary btn-flat" id="btn-generate"><?php echo _t('generate_report'); ?></button>
        <button class="btn btn-default btn-flat" id="btn-reset"><?php echo _t('reset'); ?></button>
    </div>
</div>
<div class="col-sm-9">
    <h4 class="text-blue"><i class="fa fa-bar-chart"></i>&nbsp;<?php echo _t('result'); ?></h4>
    <hr>
    <div id="chartdiv"></div>
</div>