<!-- Modal -->
<div class="modal fade modal-primary" id="modal-user-role">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-key"></i> &nbsp;<?php echo _t('global.user_role'); ?></h4>
			</div>
			<div class="modal-body form-vertical">
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="control-label"><?php echo _t('admin.name'); ?> *</label>
                        <input type="text" class="form-control" name="name">
                    </div>
                    <div class="col-sm-6">
                        <label class="control-label"><?php echo _t('admin.description'); ?> *</label>
                        <input type="text" class="form-control" name="definition">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="control-label"><?php echo _t('admin.default_page'); ?> *</label>
                        <input type="text" class="form-control" name="default_page">
                    </div>
                </div>
                <br>
                <h4 class="content-header"><?php echo _t('admin.permission'); ?></h4>
                <div class='row' style="margin-top: 15px;">
				<?php
                    if (isset($permissions)) {
                        foreach ($permissions as $permission) {
                            echo "<div class='col-sm-6' style='margin-bottom: 10px;'>";
                            echo "<label class='control-label' style='cursor:pointer;'>";
                            echo "<input type='checkbox' name='permission' value='".$permission->id."' id='perm-". $permission->id ."'>&nbsp;&nbsp;";
                            echo $permission->definition;
                            echo "</label>";
                            echo "</div>";
                        }
                    }
                ?>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnSave"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<?php echo _t('global.save'); ?></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>