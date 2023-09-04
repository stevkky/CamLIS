<!--Modal Read patient from excel -->
<div class="modal fade modal-primary modal-wide fixed-footer" id="modal-read-patient-from-excel_dev" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;
				<b><?php echo _t('patient.add_patient_via_excel'); ?> / <i>(*) <?php echo _t('patient.required_field'); ?></i></b> | <span class="text-warning bg-dark" style="background-color: yellow;">(*) <?php echo _t('patient.maximum_add_patient'); ?> </span> | <span class="text-warning bg-dark" style="background-color: blue; color:white;">(កំណត់ចំនាំសំរាប់អាយុ) បើបញ្ចូល "21" = 21ឆ្នាំ បើ "9m"= 9ខែ បើ "23d" = 23ថ្ងៃ </span>
			</div>
			<div class="modal-body" >				
				<div class="row">
					<div class="col-md-2">
						<label class="control-label">
							<?php echo _t('sample.sample_source'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
						<select name="sample_source" class="form-control" tabindex="2">
							<option value="-1" style="color:#d8d5d5;">
								<?php echo _t('global.choose'); ?>
							</option>
							<?php
							foreach($sample_source as $sc) {
								$selected = "";
								if ($sc->source_id == $patient_sample['sample_source_id']) $selected = "selected";
								echo "<option value='$sc->source_id' $selected>$sc->source_name</option>";
							}
							?>
						</select>
					</div>
					<!-- Requester -->
					<div class="col-md-2">
						<label class="control-label"><?php echo _t('sample.requester'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
						<select name="requester" class="form-control" tabindex="3">
							<option value="-1"><?php echo _t('global.choose'); ?></option>
							<?php
							foreach($requesters as $requester) {
								$selected = "";
								if ($requester->requester_id == $patient_sample['requester_id']) $selected = "selected";
								echo "<option value='$requester->requester_id' $selected>$requester->requester_name</option>";
							}
							?>
						</select>
					</div>
					<div class="col-md-3">
						<label class="control-label"><?php echo _t('sample.collect_dt'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
						<div class="input-group">
							<input type="text" class="form-control dtpicker" name="collected_date" tabindex="4">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							<input type="text" class="form-control coltimepicker narrow-padding" name="collected_time" style="width: 100px;" tabindex="5">
							<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
						</div>
					</div>
					<div class="col-md-3">
						<label class="control-label">
							<?php echo _t('sample.receive_dt'); ?> <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
						<div class="input-group">
							<input type="text" class="form-control dtpicker" name="received_date" tabindex="6">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							<input type="text" class="form-control rectimepicker narrow-padding" name="received_time" style="width: 100px;" tabindex="7">
							<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
						</div>
					</div>
					<div class="col-md-2">
						<label class="control-label"><?php echo _t('sample.test_name'); ?></label>
						<select name="test_name" class="form-control" tabindex="3">
							<option value="-1"><?php echo _t('global.choose'); ?></option>
							<option value="479">SARS-CoV-2 (Method: real time RT-PCR by GeneXpert)</option>
							<option value="497">SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)</option>
							<option value="505">SARS-CoV-2 Rapid Antigen Test</option>
							<option value="509">SARS-CoV-2 (Method: real time RT-PCR by Cobas 6800)</option>
							<option value="516">SARS-CoV-2 (BIOER Gene 9660 Real Time PCR Instruments)</option>
						</select>
					</div>
				</div>
				<div class="row">					
					<div class="col-md-2">
						<label class="control-label">
							<?php echo _t('sample.completed_by'); ?>
						</label>
						<input type="text" name="completed_by" class="form-control" value="" maxlength="50">
					</div>
					<div class="col-md-2">
						<label class="control-label">
							<?php echo _t('sample.telephone'); ?>
						</label>
						<input type="text" name="phone_number" class="form-control" value="" maxlength="15">
					</div>
					<div class="col-md-2">
						<label class="control-label">
							<?php echo _t('sample.sample_collector'); ?>
						</label>
						<input type="text" name="sample_collector" class="form-control" value="" maxlength="60">
					</div>
					<div class="col-md-2">
						<label class="control-label">
							<?php echo _t('sample.telephone'); ?>
						</label>
						<input type="text" name="phone_number_sample_collector" class="form-control" maxlength="15">
					</div>
					<div class="col-md-4">
						<div class="row">
							<div class="col-md-3">
								<label class="control-label"><?php echo _t('patient.if_contact'); ?></label>
								<div>
									<label class="control-label" style="cursor:pointer;">
										<input type="checkbox" name="is_contacted" id="is_contacted">&nbsp;
										<?php echo _t('patient.yes'); ?>
									</label>
								</div>
							</div>
							<div class="col-md-9">
								<div class="col-sm-12 hidden contact_wrapper">
									<div class="col-sm-6">
										<label class="control-label">
											<?php echo _t('patient.contact_with'); ?>
										</label>
										<input type="text" class="form-control" name="contact_with" id="contact_with">
									</div>
									
									<div class="col-sm-6">
										<label class="control-label">
											<?php echo _t('patient.type_of_contact'); ?>
										</label>
										<div>
											<label class="control-label" style="cursor:pointer;">
												<input type="radio" name='is_direct_contact' value="true">&nbsp;
												<?php echo _t('patient.direct'); ?>
											</label>
											&nbsp;&nbsp;
											<label class="control-label" style="cursor:pointer;">
												<input type="radio" name='is_direct_contact' value="false">&nbsp;
												<?php echo _t('patient.indirect'); ?>
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					
				</div>
				<div class="row">
					<div class="col-md-12 mt-3" style="margin-top:10px; position:relative; max-width:99%; overflow:auto;">
						<div id="spreadsheetdev"></div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="row form-horizontal">
					<div class="col-sm-5 text-left">
					&nbsp;
					<?php 
				if($_SESSION['roleid'] == 1){
			?>
						<button type="button" class="btn btn-success" id="btnOpenModalUpload"><i class="fa fa-upload"></i>&nbsp;<?php echo _t('global.upload'); ?></button>
						<button type="button" class="btn btn-primary" id="btnSaveListDevTest"><i class="fa fa-floppy-o"></i>&nbsp;
							សាកល្បង
						</button>
						<?php
				}
			?>
					</div>
					
					<div class="col-sm-7 text-right">
						<button type="button" class="btn btn-primary" id="btnSaveListShorForm"><i class="fa fa-floppy-o"></i>&nbsp;
							<?php echo _t('global.save'); ?>
						</button>
						<button type="button" class="btn btn-default" data-dismiss='modal'>
							<?php echo _t('global.close'); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>