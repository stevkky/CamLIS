<!-- Test Modal -->
<div class="modal fade modal-primary modal-wide fixed-footer" id="test_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header with-border">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <div style="margin-right: 30px; float: left" class="sample-number-title">
                    <label style="font-size: 16px; font-weight: bold"><i class="fa fa-list-ul"></i>&nbsp;<?php echo _t('global.test'); ?></label>
                </div>

                <div style="margin-right: 10px; float: left" class="sample-number-title">
                    <b><?php echo _t('patient.patient_id'); ?>  : </b>
                    <b class="value text-blue"><span id="sp-header_pid"><?php echo empty($patient["pid"])?'':$patient["pid"];?></span></b>
                </div>

                <div style="margin-right: 10px; float: left" class="sample-number-title">
                    <b><?php echo _t('patient.patient_name'); ?>  : </b>
                    <b class="value text-blue"><span id="sp-header_name"><?php echo empty($patient["name"])?'':$patient["name"];?></span></b>
                </div>

                <div style="margin-right: 10px; float: left" class="sample-number-title">
                    <b><?php echo _t('sample.sample_number'); ?>  : </b>
                    <b class="value text-blue"><span id="sp-sample_number"><?php echo empty($patient_sample["sample_number"])?'':$patient_sample["sample_number"];?></span></b>
                </div>

                <div style="margin-right: 10px; float: left" class="sample-number-title">
                    <b><?php echo _t('global.sample_source'); ?>  : </b>
                    <b class="value text-blue"><span id="sp-sample_source_name"><?php echo empty($patient_sample["sample_source_name"])?'':$patient_sample["sample_source_name"];?></span></b>
                </div>

			</div>
			<div class="modal-body">
				<form id="test-form"> 
					<div class="row">
						<?php
						if (isset($departments) && count($departments) > 0) {
							foreach ($departments as $dep) {
								echo "<div class='col-lg-3 col-md-3 col-sm-4 department-test' data-value='".$dep->department_id."' id='department-".$dep->department_id."'>";
								echo "<div class='department-header'>".$dep->department_name."</div>";
                                echo "<div class='tree-list-wrapper'>";
                                echo "<div class='tree-list-filter-wrapper'><input type='text' class='form-control tree-filter' placeholder='"._t('global.search')."'></div>";
                                echo "<div class='tree-list'>";
                                if (isset($sample_tests) && isset($sample_tests[$dep->department_id])) {
                                    $samples = $sample_tests[$dep->department_id]->samples;
                                    foreach ($samples as $sample) {
                                        echo "<div class='sample-type-wrapper'>";
                                        echo "<div class='sample-type-header'><i class='fa fa-hand-o-right'></i>&nbsp;$sample->sample_name</div>";
                                        echo sample_test_hierarchy_html($sample->tests);
                                        echo "</div>"; //End Sample Type Wrapper
                                    }
                                }
                                echo "</div>"; //End TreeList
								echo "</div>"; //End TreeList Wrapper
								echo "</div>"; //End Department
							}
						}
						?>
					</div>
				</form>
			</div>

		</div>
	</div>
    <div class="modal-footer">
        <div class="row form-horizontal">
            <div class="col-sm-1 text-left">
                <button type="button" class="btn btn-success btn-print"><i class="fa fa-print"></i>&nbsp;<?php echo _t('global.print'); ?></button>
            </div>
            <label class="control-label col-sm-2"><?php echo _t('sample.test_payment'); ?></label>
            <div class="col-sm-2 text-left">
                <span class="total-test-payment" style="display: inline-block; padding: 7px 15px; background: rgba(0, 0, 0, 1); color: yellow; font-size: 15px;">0</span>&nbsp;&nbsp;&nbsp;
                <span style="display: inline-block; padding-top: 7px;"><?php echo _t('sample.riel'); ?></span>
            </div>
            <div class="col-sm-7 text-right">
                <button type="button" class="btn btn-primary btnAssignTest">
                    <i class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp;<?php echo _t('sample.assign_test'); ?>
                </button>
                <button type="button" class="btn btn-success btnAssignTest add-sample-result">
                    <i class="fa fa-pencil"></i>&nbsp;<?php echo _t('sample.assign_test') . ' & ' . _t('sample.add_result'); ?>
                </button>
                <button type="button" class="btn btn-default" data-dismiss='modal'>
                    <?php echo _t('global.cancel'); ?>
                </button>
            </div>
        </div>
    </div>
</div>