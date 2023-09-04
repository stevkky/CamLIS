<script>
	var require_comment = '<?php echo _t('manage.required_comment'); ?>';
</script>

<div class="wrapper col-sm-9">
	<h4 class="sub-header"><?php echo _t('global.sample'); ?> &nbsp;&nbsp;<span class="hint--right hint--info" data-hint='Add/Edit Sample' id="addNew"><i class="fa fa-plus-square" style='color:dodgerblue; cursor:pointer;'></i></span></h4>
	<table class="table table-striped table-bordered" id="tb_sample">
		<thead>
			<th style="width:50px">N<sup>o</sup></th>
			<th><?php echo _t('global.department'); ?></th>
			<th><?php echo _t('manage.sample_name'); ?></th>
			<th style="width:50px;"></th>
		</thead>
	</table>
</div>


<!--Modal-->
<div class="modal fade" id="std_sample">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<b><?php echo _t('manage.select_sample'); ?></b>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnAddSample"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<?php echo _t('global.save'); ?></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button> 
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="modal_comment">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<b><?php echo _t('global.comment'); ?></b>    
			</div>
			<div class="modal-body">
				<div class="form-vertical">
					<div class="row">
						<div class="col-sm-12">
							<label for="comment" class="control-label hint--right hint--error hint--always"><?php echo _t('global.comment'); ?></label>
							<input type="text" name="comment" id="comment" class="form-control">
						</div>
					</div>
					<br>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<h4 class="content-header" style="font-size:12pt;"><?php echo _t('manage.list_comment'); ?></h4>
						<hr>
						<table class="table table-bordered table-striped" id="tb_cmt_list" style="width:100%; !important">
							<thead>
								<th style="width:40px;"><?php echo _t('global.no.'); ?></th>
								<th><?php echo _t('global.comment'); ?></th>
								<th style="width:60px;"></th>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnAddComment"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<?php echo _t('global.save'); ?></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button> 
			</div>
		</div>
	</div>
</div>