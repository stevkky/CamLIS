<script>
    var pageMessage = {
        q_confirm_delete_patient_sample: "<?php echo _t('view_sample.q_confirm_delete_patient_sample'); ?>",
        label_search: "<?php echo _t('global.search'); ?>"
    };
</script>
<div class="col-sm-12">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active" style="width: 100px;"><a data-toggle="tab" href="#tab-urgent" data-tab-name="urgent"><?php echo _t('global.urgent'); ?></a></li>
            <li style="width: 100px;"><a data-toggle="tab" href="#tab-routine" data-tab-name="routine"><?php echo _t('global.routine'); ?></a></li>
        </ul>

        <div class="tab-content">
            <div id="tab-urgent" class="tab-pane fade in active">
                <br>
                <table class="table table-bordered table-striped dataTable nowrap" id="tb_sample_urgent">
                    <thead>
                        <th style="width:40px;"><?php echo _t('global.no.'); ?></th>
                        <th style="width:200px;"><?php echo _t('patient.patient_id'); ?></th>
                        <th><?php echo _t('patient.patient_name'); ?></th>
                        <th><?php echo _t('request.sample_number'); ?></th>
                        <th><?php echo _t('request.collect_dt'); ?></th>
                        <th><?php echo _t('request.receive_dt'); ?></th>
                        <th><?php echo _t('request.sample_source'); ?></th>
    					<th></th>
                        <th><?php echo _t('global.patient_status'); ?></th>
                        <th class="text-center"></th>
                        <th></th>
                    </thead>
                </table>
            </div>
            <div id="tab-routine" class="tab-pane fade in">
                <div id="psample-status-wrapper" class="hide">
                    <label><?php echo _t('global.show_by'); ?> &nbsp;
                    <select class="form-control input-sm" id="routine-request-type" style="min-width: 150px;">
                        <option value=""> --- <?php echo _t('view_sample.all'); ?> --- </option>
                        <option value="<?php echo PSAMPLE_REQUESTED; ?>" data-color="<?php echo PSAMPLE_REQUESTED_COLOR; ?>"><?php echo _t('global.Pending'); ?></option>
                        <option value="<?php echo PSAMPLE_COLLECTED; ?>" data-color="<?php echo PSAMPLE_COLLECTED_COLOR; ?>"><?php echo _t('view_sample.collected'); ?></option>
                    </select>
                    </label>
                </div>
                <br>
                <table style="width: 100%" class="table table-bordered table-striped dataTable nowrap" id="tb_request_routine">
                    <thead>
                        <th style="width:40px;"><?php echo _t('global.no.'); ?></th>
                        <th style="width:200px;"><?php echo _t('patient.patient_id'); ?></th>
                        <th><?php echo _t('patient.patient_name'); ?></th>
                        <th><?php echo _t('request.sample_number'); ?></th>
                        <th><?php echo _t('request.collect_dt'); ?></th>
                        <th><?php echo _t('request.receive_dt'); ?></th>
                        <th><?php echo _t('request.sample_source'); ?></th>
    					<th></th>
                        <th><?php echo _t('global.patient_status'); ?></th>
                        <th class="text-center"></th>
                        <th></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>