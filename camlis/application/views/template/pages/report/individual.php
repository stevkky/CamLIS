<script>    
    var msg_required_condition = "<?php echo _t('individual.msg.required_condition'); ?>";
</script>
<div class="wrapper col-sm-9">
    <div class="form-vertical">
         <div class="row">
            <div class="col-sm-3">
                <input type="hidden" id="patient_sample_id" />
                <label for="lab_nameKH" class="control-label hint--right hint--error hint--always"><?php echo _t('report.patient_id'); ?></label>
                <div>
                    <input type="text" class="form-control"  id="patient-code" placeholder="<?php echo _t('report.patient_id'); ?>"/>
                </div>
            </div>
            <div class="col-sm-3">
                <label for="lab_nameKH" class="control-label hint--right hint--error hint--always"><?php echo _t('report.patient_name'); ?></label>
                <div>
                    <input type="text" class="form-control"  id="patient-name" placeholder="<?php echo _t('report.patient_name'); ?>"/>
                </div>
            </div>
            <div class="col-sm-3">
                <label for="lab_nameKH" class="control-label hint--right hint--error hint--always"><?php echo _t('report.sample_number'); ?></label>
                <div>
                    <input type="text" class="form-control"  id="sample-number" placeholder="<?php echo _t('report.sample_number'); ?>"/>
                </div>
            </div>
            <div class="col-sm-3">
                <label for="lab_nameKH" class="control-label hint--right hint--error hint--always">&nbsp;</label>
                <div>
                    <button type="button" id="btnGenerate" class="btn btn-primary"><?php echo _t('report.filter'); ?>&nbsp;&nbsp;
                    <i class="fa fa-search"></i></button>
                </div>

            </div>
        </div>
    </div>
</div>
 