<!-- Organism/Antibiotic Modal -->
<div class="modal fade modal-primary" id="modal-copy-data">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php echo _t('admin.copy_org_anti'); ?></h4>
			</div>
			<div class="modal-body">
				<div class="form-vertical">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class='radio-inline' style="padding-left: 0;">
									<input type="radio" value="1" name="type" checked>
									<?php echo _t('global.organism'); ?>
								</label>
								<label class='radio-inline'>
									<input type="radio" value="2" name="type">
									<?php echo _t('global.antibiotic'); ?>
								</label>
							</div>
						</div>
					</div>
					<div class="well" style="padding: 8px 10px;" id="copy-from">
						<b><?php echo _t('admin.copy_from'); ?></b>
						<hr style="margin-bottom: 10px; margin-top: 10px; border-top: 1px solid #e7d6d6;">
						<div class="row">
							<div class="col-sm-8">
								<label class="control-label"><?php echo _t('global.test'); ?>&nbsp;<sup class="fa fa-asterisk" style="font-size:8px;"></label>
								<select name="sample_test" class="form-control copy-from sample-test">
									<option value="-1"><?php echo _t('global.choose'); ?></option>
								</select>
							</div>
							<div class="col-sm-4 type-antibiotic" style="display: none;">
								<label class="control-label"><?php echo _t('global.organism'); ?></label>&nbsp;<sup class="fa fa-asterisk" style="font-size:8px;"></sup>
								<select name="organism" class="form-control copy-from organism">
									<option value="-1"><?php echo _t('global.choose'); ?></option>
								</select>
							</div>
						</div>
					</div>
					<!-- Copy To -->
					<div class="well" style="padding: 8px 10px;" id="copy-from">
						<b><?php echo _t('admin.copy_to'); ?></b>
						<hr style="margin-bottom: 10px; margin-top: 10px; border-top: 1px solid #e7d6d6;">
						<div class="row">
							<div class="col-sm-8">
								<label class="control-label"><?php echo _t('global.test'); ?>&nbsp;<sup class="fa fa-asterisk" style="font-size:8px;"></label>
								<select name="sample_test" class="form-control copy-to sample-test">
									<option value="-1"><?php echo _t('global.choose'); ?></option>
								</select>
							</div>
							<div class="col-sm-4 type-antibiotic" style="display: none;">
								<label class="control-label"><?php echo _t('global.organism'); ?></label>&nbsp;<sup class="fa fa-asterisk" style="font-size:8px;"></sup>
								<select name="organism" class="form-control organism copy-to">
									<option value="-1"><?php echo _t('global.choose'); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnCopy"><i class="fa fa-clipboard"></i> &nbsp;<?php echo _t('admin.copy'); ?></button>
				<button type="button" class="btn btn-default" data-dismiss='modal'><?php echo _t('global.cancel'); ?></button>
			</div>
		</div>
	</div>
</div>

<!--​Test Modal -->
<div class="modal fade modal-primary" id="modal-test">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title new"><?php echo _t('admin.new_test'); ?></h4>
				<h4 class="modal-title edit" style="display: none;"><?php echo _t('admin.edit_test'); ?></h4>
			</div>
			<div class="modal-body">
				<div>
					<form class="form-vertical">
						<div class="row form-group" style="margin-bottom: 25px;">
							<div class="col-sm-12">
								<label for="test-list" class="control-label"><?php echo _t('admin.test_name'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
								<div id="select-test-wrapper" class="input-group">
									<select class="form-control" name="test_list" id="test-list">
										<?php
										if (count($tests) > 0) {
											foreach ($tests as $row) {
												echo "<option value='".$row->test_id."'>".$row->test_name."</option>";
											}
										}
										?>
									</select>
									<span class="input-group-addon">
										<div class="btn-group btn-group-xs">
											<button id="btn-new-test-name" type="button" data-toggle="tooltip" data-placement="top" title="<?php echo _t('global.add_new'); ?>" class="btn btn-success"><i class="fa fa-plus"></i></button>
											<button id="btn-edit-test-name" type="button" data-toggle="tooltip" data-placement="top" title="<?php echo _t('global.edit'); ?>" class="btn btn-primary" disabled><i class="fa fa-pencil"></i></button>
										</div>
									</span>
								</div>
								<div id="test-entry-wrapper" class="input-group" style="display: none;">
									<input type="text" class="form-control" name="test_name" id="test-name" placeholder="<?php echo _t('test.enter_new_test_name'); ?>">
									<span class="input-group-addon">
										<div class="btn-group btn-group-xs">
											<button id="btn-save-test-name" type="button" data-toggle="tooltip" data-placement="top" title="<?php echo _t('global.save'); ?>" class="btn btn-primary"><i class="fa fa-floppy-o"></i></button>
											<button id="btn-delete-test-name" type="button" data-toggle="tooltip" data-placement="top" title="<?php echo _t('global.remove'); ?>" class="btn btn-danger"><i class="fa fa-trash"></i></button>
											<button id="btn-cancel-test-name" type="button" data-toggle="tooltip" data-placement="top" title="<?php echo _t('global.cancel'); ?>" class="btn btn-default"><i class="fa fa-remove"></i></button>
										</div>
									</span>
								</div>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-6">
								<label for="department" class="control-label"><?php echo _t('admin.choose_department'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
								<select name="department" id="department" class="form-control">
									<option value="-1"><?php echo _t('global.choose'); ?></option>
									<?php
									foreach ($departments as $dep) {
										echo "<option value='".$dep->department_id."'>".$dep->department_name."</option>";
									}
									?>
								</select>
							</div>
							<div class="col-sm-6">
								<label for="sample" class="control-label"><?php echo _t('admin.choose_sample_type'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
								<select name="sample" id="sample-type" class="form-control">
									<option value="-1"><?php echo _t('global.choose'); ?></option>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-4">
								<label class="control-label"><?php echo _t('admin.is_heading'); ?></label>
								<div>
									<label class="control-label" style="cursor:pointer;">
										<input type="checkbox" name="is_heading" value='1' id="is-heading">&nbsp;&nbsp;Yes
									</label>
								</div>
							</div>
							<div class="col-sm-4">
								<label class="control-label"><?php echo _t('admin.default_selected'); ?></label>
								<div>
									<label class="control-label" style="cursor:pointer;">
										<input type="checkbox" name="is_default_selected" value='1' id="is-default-selected">&nbsp;&nbsp;Yes
									</label>
								</div>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-4">
								<label class="control-label"><?php echo _t('admin.group_by'); ?></label>
								<select name="group_by" id="test-group" class="form-control">
									<option value="-1"><?php echo _t('global.choose'); ?></option>
								</select>
							</div>
							<div class="col-sm-4 for-test">
								<label class="control-label"><?php echo _t('admin.unit_sign'); ?></label>
								<input type="text" class="form-control" name="unit_sign" id="unit-sign">
							</div>
							<div class="col-sm-4 for-test">
								<label for="field_type" class="control-label"><?php echo _t('admin.field_type'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
								<select name="field_type" id="field-type" class="form-control">
									<option value="-1"><?php echo _t('global.choose'); ?></option>
									<?php
									foreach ($field_types as $row) {
										echo "<option value='".$row->id."'>".$row->type."</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-4">
								<label class="control-label"><?php echo _t('admin.group_result'); ?></label>
								<input type="text" class="form-control" name="group_result_name" id="group-result-name">
							</div>
							<div class="col-sm-4">
								<label class="control-label"><?php echo _t('admin.order'); ?></label>
								<input type="text" class="form-control" name="test_order" id="test-order" onkeypress="return isNumber(event);">
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
						<div class="form-vertical for-test" style="margin-top: 20px; display: none;" id="possible-results">
							<div class="row form-group" style="margin:0 2px;">
								<div class="row">
									<div class="col-sm-6">
										<h4 class="content-header" style="font-size:12pt"><?php echo _t('global.organism'); ?></h4><hr>
										<table class="table table-bordered table-striped" id="tbl-organism-header">
											<tr>
                                                <th style="border: 0;"><?php echo _t('global.organism'); ?></th>
                                                <th class="text-right" style="border: 0;">
                                                    <label class="pointer text-blue"><input type="checkbox" id="show-selected-organism">&nbsp;<?php echo _t('admin.selected'); ?></label>
                                                </th>
                                            </tr>
										</table>
										<div id="organism-wrapper">
											<div id="organsim-filter-wrapper">
												<input type="text" class="form-control" id="organsim-filter" placeholder="<?php echo _t('global.search'); ?>">
											</div>
											<div id="organism-list">
												<table class="table table-bordered table-striped" id="tbl-organism">
													<tbody>
                                                    <?php foreach ($organisms as $organism) { ?>
                                                    <tr class="organism" id="organism-<?php echo $organism->ID ?>">
                                                        <td>
                                                            <input type="checkbox" id="org-<?php echo $organism->ID ?>" class="organism-value" value="<?php echo $organism->ID ?>">&nbsp;
                                                            <span class="organism-name"><?php echo $organism->organism_name ?></span>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<h4 class="content-header" style="font-size:12pt;"><?php echo _t('global.antibiotic'); ?></h4><hr>
										<table class="table table-bordered table-striped" id="tbl-antibiotic-header">
											<thead>
											<th style="border: 0;"><?php echo _t('global.antibiotic'); ?></th>
											<th class="text-right" style="border: 0;">
                                                <label class="pointer text-blue"><input type="checkbox" id="show-selected-antibiotic">&nbsp;<?php echo _t('admin.selected'); ?></label>
                                            </th>
											</thead>
											<tbody></tbody>
										</table>
										<div id="antibiotic-wrapper" style="display: none;">
											<div id="antibiotic-filter-wrapper">
												<input type="text" class="form-control" id="antibiotic-filter" placeholder="<?php echo _t('global.search'); ?>">
											</div>
											<ul class="list-unstyled" id="antibiotic-list">
												<?php
												foreach ($antibiotic as $row) {
													echo "<li id='antibiotic-".$row->ID."'>";
													echo "<label class='control-label' style='cursor: pointer'>
																<input type='checkbox' name='antibiotic[]' class='antibiotic' id='anti-".$row->ID."' value='".$row->ID."'>&nbsp;
																<span class='antibiotic_name'>$row->antibiotic_name</span>
														  </label>";
													echo "</li>";
												}
												?>
											</ul>
										</div>
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