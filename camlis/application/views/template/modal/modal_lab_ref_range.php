<!--​Test Modal -->
<div class="modal fade modal-primary" id="modal-ref-range">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php echo _t('manage.edit_ref_range'); ?></h4>
			</div>
			<div class="modal-body">
				<div>
					<form class="form-vertical">
						<div class="row form-group">
							<div class="col-sm-4">
								<label for="department" class="control-label"><?php echo _t('admin.department_name'); ?></label>
								<div id="department-name"></div>
							</div>
							<div class="col-sm-4">
								<label for="sample" class="control-label"><?php echo _t('admin.sample_type'); ?></label>
								<div id="sample-name"></div>
							</div>
                            <div class="col-sm-4">
                                <label for="sample" class="control-label"><?php echo _t('admin.test_name'); ?></label>
                                <div id="test-name"></div>
                            </div>
						</div>
						<div class="form-vertical for-test" style="margin-top: 20px;" id="ref-ranges">
							<div class="row form-group" style="margin:0 2px;">
								<h4 class="content-header" style="font-size:13pt;"><?php echo _t('admin.ref_range'); ?></h4>
								<hr>
								<div class="row">
									<div class="col-sm-12">
										<table class="table table-striped table-bordered" id="tbl-ref-range">
											<thead>
											<th><?php echo _t('admin.patient_type'); ?></th>
											<th style="width:150px"><?php echo _t('admin.min_value'); ?></th>
											<th style="width:80px"></th>
											<th style="width:150px"><?php echo _t('admin.max_value'); ?></th>
											<th style="width:50px"></th>
											<th style="width:50px"></th>
											</thead>
											<tbody>
											<tr type="_template" style="display: none">
												<td>
													<select name="patient_type[]" class="form-control patient-type">
														<option value="-1"><?php echo _t('global.choose'); ?></option>
														<?php
														foreach ($patient_types as $row) {
															echo "<option value='".$row->ID."'>".$row->type."</option>";
														}
														?>
													</select>
												</td>
												<td>
													<input type="text" name="min_value[]" class="form-control min-value">
												</td>
												<td>
													<select name="range-sign" class="form-control range-sign">
														<option value="" ></option>
														<option value="-" selected> - </option>
														<option value="<" > < </option>
														<option value="≤" > &le; </option>
														<option value=">" > > </option>
														<option value="≥" > &ge; </option>
													</select>
												</td>
												<td>
													<input type="text" name="max_value[]" class="form-control max-value">
												</td>
												<td>
													<button type="button" class="btn btn-danger btn-remove-ref-range"><i class="fa fa-trash"></i></button>
												</td>
												<td>
													<button type="button" class="btn btn-success btn-new-ref-range"><i class="fa fa-plus"></i></button>
												</td>
											</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnSave"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<span class="save"><?php echo _t('global.save'); ?></span><span class="edit" style="display: none;"><?php echo _t('global.update'); ?></span></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>