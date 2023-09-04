<!-- Modal -->
<div class="modal fade modal-primary" id="modal-test-payment">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _t('manage.test_payment'); ?></h4>
            </div>
            <div class="modal-body form-vertical">
                <div class="row form-group">
                    <div class="col-sm-12">
                        <label class="control-label"><?php echo _t('manage.group_result'); ?> *</label>
                        <select name="group_result" id="group-result" class="form-control">
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <h5 style="margin-top: 10px;"><b><?php echo _t('manage.test_payment'); ?></b></h5>
                        <table class="table table-bordered" id="tbl-payments">
                            <thead>
                                <tr>
                                    <th><?php echo _t('global.payment_type'); ?></th>
                                    <th style="width: 230px;"><?php echo _t('manage.price'); ?></th>
                                    <td style="width: 48px;"></td>
                                    <td style="width: 48px;"></td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnSave"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<span class="save"><?php echo _t('global.save'); ?></span><span class="update" style="display: none;"><?php echo _t('global.update'); ?></span></button>
                <button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
            </div>
        </div>
    </div>
</div>