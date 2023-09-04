<script>
    var msg_required_data = "<?php echo _t('global.msg.fill_required_data'); ?>";
</script>
<div class="wrapper col-sm-9">
    <div class="form-vertical border-box">
        <form id="tat_report" role="form">
            <div class="row">
                <div class="col-sm-4">
                    <label class="control-label"><?php echo _t('report.start_receive_date'); ?> *</label>
                    <input type="text" name="start_date" class="form-control" id="start-date">
                </div>
                <div class="col-sm-4">
                    <label class="control-label"><?php echo _t('report.end_receive_date'); ?> *</label>
                    <input type="text" name="end_date" class="form-control" id="end-date">
                </div>
                <div class="col-sm-4">
                    <label class="control-label"><?php echo _t('report.testing_count'); ?></label>
                    <select name="testing_type" id="testing-type" class="form-control">
                        <option value="ALL"><?php echo _t('global.all'); ?></option>
                        <option value="SINGLE"><?php echo _t('report.single_test'); ?></option>
                        <option value="MULTIPLE"><?php echo _t('report.multiple_test'); ?></option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <label class="control-label"><?php echo _t('report.testing_name'); ?></label>
                    <select name="group_result[]" id="group-result" class="form-control" multiple="multiple">
                        <?php foreach ($group_results as $group_result): ?>
                            <option value="<?php echo $group_result->ID; ?>">
                                <?php echo $group_result->department_name.'=>'.$group_result->group_result; ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="control-label">&nbsp;</label>
                    <button class="btn btn-primary" id="btnGenerate" style="display: block">
                        <i class="fa fa-search"></i> <?php echo _t('report.filter'); ?>
                    </button>
                </div>
                <div class="col-sm-2">
                    <label class="control-label">&nbsp;</label>
                    <button class="btn btn-primary" id="btnExportRawdata" style="display: block">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export raw data<!-- <?php echo _t('report.filter'); ?> -->
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>