<script>
	var msg_save_fail = '<?php echo _t('global.msg.save_fail'); ?>';
	var msg_update_fail = '<?php echo _t('global.msg.update_fail'); ?>';
	var msg_delete_fail = '<?php echo _t('global.msg.delete_fail'); ?>';
	var msg_required_data = '<?php echo _t('global.msg.fill_required_data'); ?>';
	var q_delete_organism = '<?php echo _t('admin.msg.q_delete_organism'); ?>';
	var q_delete_antibiotic = '<?php echo _t('admin.msg.q_delete_antibiotic'); ?>';
	var q_delete_quantity = '<?php echo _t('admin.msg.q_delete_quantity'); ?>';
</script>
<div class="wrapper col-sm-9">
    <h4 class="sub-header"><?php echo _t('global.organism').'/'._t('global.qty').'/'._t('global.antibiotic'); ?></h4>
    <br>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab1" data-tab-name="organism"><?php echo _t('global.organism'); ?></a></li>
            <li><a data-toggle="tab" href="#tab2" data-tab-name="quantity"><?php echo _t('admin.organism_quantity'); ?></a></li>
            <li><a data-toggle="tab" href="#tab3" data-tab-name="antibiotic"><?php echo _t('global.antibiotic'); ?></a></li>
        </ul>
        <div class="tab-content">
            <div id="tab1" class="tab-pane fade in active">
                <br>
                <button type="button" class="btn btn-primary btn-flat" id="new-organism"><i class="fa fa-plus-square"></i>&nbsp;<?php echo _t('admin.add_new_organism'); ?></button>
                <br><br>
                <table class="table table-bordered table-striped" id="tbl-organism" style="width: 100%">
                    <thead>
                        <th><?php echo _t('global.no.'); ?></th>
                        <th><?php echo _t('admin.order'); ?></th>
                        <th><?php echo _t('admin.organism_name'); ?></th>
                        <th><?php echo _t('global.value'); ?></th>
                        <th></th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div id="tab2" class="tab-pane fade in">
                <br>
                <button type="button" class="btn btn-primary btn-flat" id="new-quantity"><i class="fa fa-plus-square"></i>&nbsp;<?php echo _t('admin.add_new_quantity'); ?></button>
                <br><br>
                <table class="table table-bordered table-striped" id="tbl-quantity" style="width: 100%">
                    <thead>
                        <th><?php echo _t('global.no.'); ?></th>
                        <th><?php echo _t('admin.quantity'); ?></th>
                        <th></th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div id="tab3" class="tab-pane fade in">
                <br>
                <button type="button" class="btn btn-primary btn-flat" id="new-antibiotic"><i class="fa fa-plus-square"></i>&nbsp;<?php echo _t('admin.add_new_antibiotic'); ?></button>
                <br><br>
                <table class="table table-bordered table-striped" id="tbl-antibiotic" style="width: 100%">
                    <thead>
                        <th><?php echo _t('global.no.'); ?></th>
                        <th><?php echo _t('admin.order'); ?></th>
                        <th><?php echo _t('admin.antibiotic_name'); ?></th>
                        <th></th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>