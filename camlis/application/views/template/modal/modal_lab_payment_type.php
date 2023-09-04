<!-- Modal -->
<div class="modal fade modal-primary" id="modal-payment-type">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _t('manage.choose_payment_type'); ?></h4>
            </div>
            <div class="modal-body" style="height: 430px; overflow: auto;">
                <?php foreach ($payment_types as $payment_type) { ?>
                <div class="row form-group">
                    <div class="col-sm-12">
                        <label class="control-label pointer">
                            <input type="checkbox" name="payment_type" id="payment-type-<?php echo $payment_type['id']; ?>" value="<?php echo $payment_type['id']; ?>">&nbsp;
                            <?php echo $payment_type['name']; ?>
                        </label>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnSave"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<span class="save"><?php echo _t('global.save'); ?></span><span class="update" style="display: none;"><?php echo _t('global.update'); ?></span></button>
                <button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
            </div>
        </div>
    </div>
</div>