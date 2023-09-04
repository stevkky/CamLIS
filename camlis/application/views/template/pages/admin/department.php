<div class="wrapper col-sm-9">
	<h4 class="sub-header"><?php echo _t('global.department'); ?> &nbsp;&nbsp;<span class="hint--right hint--info" data-hint='Add/Edit Department' id="addNew"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4>
	<table class='table table-bordered table-striped' id="tb_department">
		<thead>
			<th style="width:60px;"><?php echo _t('global.no.'); ?></th>
			<th><?php echo _t('global.department'); ?></th>
		</thead>
		<tbody></tbody>
	</table>
</div>


<!--Modal-->
<div class="modal fade" id="std_department">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<?php echo _t('manage.select_department'); ?>    
			</div>
			<div class="modal-body">
				<div class="tree"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnAddDepartment"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<?php echo _t('global.save'); ?></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button> 
			</div>
		</div>
	</div>
</div>