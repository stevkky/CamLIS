<script>
    var pageMessage = {
        q_confirm_delete_patient_sample: "<?php echo _t('view_sample.q_confirm_delete_patient_sample'); ?>",
        label_search: "<?php echo _t('global.search'); ?>"
    };
    var label_sample_number		= "<?php echo _t('sample.sample_number'); ?>";
    var label_patient_id		= "<?php echo _t('patient.patient_id'); ?>";
    var label_patient_name		= "<?php echo _t('patient.name'); ?>";
    var label_test_name 		= "<?php echo _t('sample.test_name'); ?>";
    var label_performed_by		= "<?php echo _t('sample.performed_by'); ?>";
	var label_result			= "<?php echo _t('sample.result'); ?>";
    var label_test_date		    = "<?php echo _t('sample.test_date'); ?>";
    var label_machine_name      = "<?php echo _t('sample.machine_name'); ?>";
    var msg_loading				= "<?php echo _t('global.plz_wait');?>";
    const PERFORMERS            = <?php echo json_encode($performers);?>;
    var can_edit_psample        = <?php echo !empty($can_edit_psample) ? $can_edit_psample : 0;?>;  
    var label_print_lab_form	= "<?php echo _t('sample.preview_request'); ?>";
	var label_qr_code			= "<?php echo _t('sample.qr_code'); ?>";
    var label_collected_date	= "<?php echo _t('sample.collected_date'); ?>";
    var msg = {	
		"not_fill" 						: "<?php echo _t('global.not_fill'); ?>",
		"not_select" 					: "<?php echo _t('global.not_select'); ?>",
		"not_greater_than_3" 			: "<?php echo _t('global.not_greater_than_3'); ?>",
		"not_correct_format" 			: "<?php echo _t('global.not_correct_format'); ?>",
		"not_greater_than_100" 			: "<?php echo _t('global.not_greater_than_100'); ?>",
		"not_greater_than_60" 			: "<?php echo _t('global.not_greater_than_60'); ?>",
		"month_not_greater_than_12" 	: "<?php echo _t('global.month_not_greater_than_12'); ?>",
		"day_not_greater_than_31" 		: "<?php echo _t('global.day_not_greater_than_31'); ?>",
		"not_data_entry" 				: "<?php echo _t('global.not_data_entry'); ?>",
		"not_greater_than_100_row" 		: "<?php echo _t('global.not_greater_than_100_row'); ?>",
		"not_greater_than"				: "<?php echo _t('global.not_greater_than'); ?>",
		"char"							: "<?php echo _t('global.char'); ?>",
		"not_greater_than_current_date"	: "<?php echo _t('global.not_greater_than_current_date'); ?>",
		"not_correct"					: "<?php echo _t('global.not_correct'); ?>",		
		"select_test_name"				: "<?php echo _t('global.select_test_name'); ?>",
		"excel_none_data_check_again"	: "<?php echo _t('global.excel_none_data_again'); ?>",
		"data_over_max_only_500_added"  : "<?php echo _t('global.data_over_max_only_500_added'); ?>",
		"data_inserted_successful"		: "<?php echo _t('global.data_inserted_successful'); ?>",
        "no_sample_found"		        : "<?php echo _t('sample.no_sample_found'); ?>",
        "covid_had_result"		        : "<?php echo _t('sample.covid_had_result'); ?>",
        "no_test_sar_cov2_found"        : "<?php echo _t('sample.no_test_sar_cov2_found'); ?>",        
	}  
</script>
<style>
.no-sort::after { display: none!important; }
.no-sort { pointer-events: none!important; cursor: default!important; }
</style>
<div class="col-sm-12" data-permission="<?php echo $can_edit_psample;?>">
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
                    <th><?php echo _t('sample.sample_number'); ?></th>
                    <th><?php echo _t('sample.collect_dt'); ?></th>
                    <th><?php echo _t('sample.receive_dt'); ?></th>
                    <th><?php echo _t('sample.print_dt'); ?></th>
                    <th><?php echo _t('sample.sample_source'); ?></th>
                    <th></th>
                    <th><?php echo _t('global.patient_status'); ?></th>
                    <th class="text-center">&nbsp;</th>
                    <th></th>
                    </thead>
                </table>
            </div>
            <div id="tab-routine" class="tab-pane fade in">
                <div id="psample-status-wrapper" class="hide">
                    <label><?php echo _t('global.show_by'); ?> &nbsp;
                    <select class="form-control input-sm" id="routine-sample-type" style="min-width: 150px;">
                        <option selected value="-1"></option>
                        <option value=""> --- <?php echo _t('view_sample.all'); ?> --- </option>
                        <option value="<?php echo PSAMPLE_REJECTED; ?>" data-color="<?php echo PSAMPLE_REJECTED_COLOR; ?>"><?php echo _t('global.rejected'); ?></option>
                        <option value="<?php echo PSAMPLE_PENDING; ?>" data-color="<?php echo PSAMPLE_PENDING_COLOR; ?>"><?php echo _t('global.Pending'); ?></option>
                        <option value="<?php echo PSAMPLE_PROGRESSING; ?>" data-color="<?php echo PSAMPLE_PROGRESSING_COLOR; ?>"><?php echo _t('global.Progress'); ?></option>
                        <option value="<?php echo PSAMPLE_COMPLETE; ?>" data-color="<?php echo PSAMPLE_COMPLETE_COLOR; ?>"><?php echo _t('global.Complete'); ?></option>
                        <option value="<?php echo PSAMPLE_PRINTED; ?>" data-color="<?php echo PSAMPLE_PRINTED_COLOR; ?>"><?php echo _t('view_sample.printed'); ?></option>
                    </select>
                    </label>
                </div>
                <br>
                
                <table style="width: 100%" class="table table-bordered table-striped dataTable nowrap" id="tb_sample_routine">
                    <thead>
                    <th>&nbsp;
                    <?php if(!empty($can_edit_psample)){ ?>
                        <input id="selectAll" type="checkbox">
                    <?php } ?>
                    </th>
                    <th style="width:40px;"><?php echo _t('global.no.'); ?></th>
                    <th style="width:200px;"><?php echo _t('patient.patient_id'); ?></th>
                    <th><?php echo _t('patient.patient_name'); ?></th>
                    <th><?php echo _t('sample.sample_number'); ?></th>
                    <th><?php echo _t('sample.collect_dt'); ?></th>
                    <th><?php echo _t('sample.receive_dt'); ?></th>
                    <th><?php echo _t('sample.print_dt'); ?></th>
                    <th><?php echo _t('sample.sample_source'); ?></th>
                    <th></th>
                    <th><?php echo _t('global.patient_status'); ?></th>
                    <th class="text-center">&nbsp;</th>
                    <th></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
