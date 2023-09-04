<!-- Posssible Result -->
<div class="modal fade modal-success" id="possible_result_modal">
	<div class="modal-dialog" style="width:90%;">
		<div class="modal-content">
			<div class="modal-header with-border">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-list"></i>&nbsp;<?php echo _t('sample.possible_result').' '._t('sample.for'); ?> <span class="test-name text-blue"></span></h4>
			</div>
			<div class="modal-body clearfix" style="min-height:300px;">
				<div class="row">
					<div id="organism_wrapper" class="col-sm-7">
						<div class="row form-inline">
							<div class="col-sm-12">
								<div class="form-group">
									<label class="control-label"><?php echo _t('global.search'); ?> :</label>
									<input type="search" class="form-control input-sm" onkeyup="searchTable(this, 'organism_list');">
								</div>
							</div>
						</div>
						<table class="table table-bordered table-striped" id="organism_list">
							<thead>
							<tr>
								<th><?php echo _t('sample.possible_result'); ?></th>
								<th class="text-center"><?php echo _t('global.qty'); ?></th>
								<th><?php echo _t('global.contaminant'); ?></th>
							</tr>
							</thead>
							<tbody></tbody>
						</table>
						<div id="organism-qty-list" class="hide">
							<?php
							if (isset($organism_quantity) && is_array($organism_quantity)) {
								foreach ($organism_quantity as $qty) {
									echo "<option value='".$qty['ID']."'>".$qty['quantity']."</option>";
								}
							}
							?>
						</div>
					</div>
					<div id="antibiotic_wrapper" class="col-sm-5">
						<div class="row form-inline">
							<div class="col-sm-12">
								<div class="form-group">
									<label class="control-label"><?php echo _t('global.search'); ?> :</label>
									<input type="search" class="form-control input-sm" onkeyup="searchTable(this, 'antibiotic_list');">
								</div>
							</div>
						</div>
						<table class="table table-bordered table-striped" id="antibiotic_list">
							<thead>
								<th><?php echo _t('sample.antibiotic'); ?></th>
								<th><?php echo _t('sample.disc_diffusion'); ?></th>
								<th><?php echo _t('sample.test_zone'); ?></th>
								<th><?php echo _t('sample.sensitivity'); ?></th>
								<th><?php echo _t('sample.invisible'); ?></th>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnAddOrganism">
					<i class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp;<?php echo _t('global.assign'); ?>
				</button>
				<button type="button" class="btn btn-default" data-dismiss='modal'>
					<?php echo _t('global.cancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>