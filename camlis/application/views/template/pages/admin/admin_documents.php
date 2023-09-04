<script>
    var msg_required_data       = '<?php echo _t('global.msg.fill_required_data'); ?>';
	var msg_save_fail           = '<?php echo _t('global.msg.save_fail'); ?>';
</script>
<?php
    $left_result_footer   = isset($laboratory_variables['left-result-footer']['value']) ? $laboratory_variables['left-result-footer']['value'] : '';
    $left_result_status   = isset($laboratory_variables['left-result-footer']['status']) && $laboratory_variables['left-result-footer']['status'] == 1 ? 'checked' : '';
    $middle_result_footer = isset($laboratory_variables['middle-result-footer']['value']) ? $laboratory_variables['middle-result-footer']['value'] : '';
    $middle_result_status = isset($laboratory_variables['middle-result-footer']['status']) && $laboratory_variables['middle-result-footer']['status'] == 1 ? 'checked' : '';
    $right_result_footer  = isset($laboratory_variables['right-result-footer']['value']) ? $laboratory_variables['right-result-footer']['value'] : '';
    $right_result_status  = isset($laboratory_variables['right-result-footer']['status']) && $laboratory_variables['right-result-footer']['status'] == 1 ? 'checked' : '';
?>
<div class="wrapper col-sm-9">
	<h4 class="sub-header">
        <i class="fa fa-file-text"></i>&nbsp;
        <span><?php echo _t('manage.document_template'); ?></span>
    </h4><br>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab1"><?php echo _t('manage.result_template'); ?></a></li>
        </ul>
        <div class="tab-content">
            <div id="tab1" class="tab-pane fade in active">
                <div class="row">
                    <div class="col-sm-10">
                        <div class="result-template">
                            <table>
                                <tr>
                                    <td class='text-left'>Last Date Test : <?php echo date('d-M-Y H:i'); ?></td>
                                    <td></td>
                                    <td class='text-right'>Report Date : <?php echo date('d-M-Y H:i'); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding-right: 30px;">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <input type="checkbox" name="left-footer-status" value="1" <?php echo $left_result_status; ?>>
                                            </div>
                                            <input type="text" class="form-control" name="left-footer-text" value="<?php echo $left_result_footer; ?>">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <input type="checkbox" name="middle-footer-status" value="1" <?php echo $middle_result_status; ?>>
                                            </div>
                                            <input type="text" class="form-control" name="middle-footer-text" value="<?php echo $middle_result_footer; ?>">
                                        </div>
                                    </td>
                                    <td class="text-right" style="padding-left: 30px;">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <input type="checkbox" name="right-footer-status" value="1" <?php echo $right_result_status; ?>>
                                            </div>
                                            <input type="text" class="form-control" name="right-footer-text" value="<?php echo $right_result_footer; ?>">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="footer text-center">
                                <?php echo $laboratoryInfo->address_kh; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-primary" id="btnSaveResultTemplate" style="width: 100%"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<?php echo _t('global.save'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>