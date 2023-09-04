<script>
    var msg_required_data = "<?php echo _t('global.msg.fill_required_data'); ?>";
</script>
<?php $name = 'name_'.strtolower($app_lang); ?>
<div class="col-sm-12">
    <div class="form-vertical border-box">
        <form id="audit_user" role="form">
            <div class="row">
                <div class="col-sm-3">
                    <label class="control-label"><?php echo _t('global.laboratory'); ?></label>
                    <select name="laboratories[]" id="laboratories" class="form-control" multiple>
                        <?php foreach ($laboratories as $laboratory): ?>
                            <option value="<?php echo $laboratory->labID; ?>"><?php echo $laboratory->$name; ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="control-label"><?php echo _t('report.start_receive_date'); ?> *</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="start-date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control narrow-padding" id="start-time" size="10" value="00:00">
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                    </div>
                </div>
                <div class="col-sm-3">
                    <label class="control-label"><?php echo _t('report.end_receive_date'); ?> *</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="end-date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" class="form-control narrow-padding" id="end-time" size="10" value="23:59">
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                    </div>
                </div>
                <div class="col-sm-3">
                    <label class="control-label">&nbsp;</label>
                    <div class="input-group">
                        <button class="btn btn-primary" id="btnGenerate"><i class="fa fa-search"></i> <?php echo _t('report.filter'); ?></button>
                        <button class="btn btn-success" id="btnExportExcel"><i class="fa fa-file-excel-o"></i> <?php echo _t('report.export_excel'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>