<div class="modal fade modal-danger" id="modal-existed-patient">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <i class="fa fa-user" aria-hidden="true"></i>&nbsp;
                <b><?php echo _t('patient.patient_information'); ?></b>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table" id="tbl-existed-patient" style="width:100%; !important">
                            <thead>
                                <th><?php echo _t('patient.patient_id'); ?></th>
                                <th><?php echo _t('patient.patient_name'); ?></th>
                                <th><?php echo _t('patient.sex'); ?></th>
                                <th><?php echo _t('patient.dob'); ?></th>
                                <th><?php echo _t('patient.phone'); ?></th>
                                <th><?php echo _t('patient.address'); ?></th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary use-existed-patient">
                    <i class="fa fa-sign-in" aria-hidden="true"></i>&nbsp;<?php echo _t('global.choose'); ?>
                </button>
                <button type="button" class="btn btn-default" data-dismiss='modal'>
                    <?php echo _t('global.cancel'); ?>
                </button>
            </div>
        </div>
    </div>
</div>