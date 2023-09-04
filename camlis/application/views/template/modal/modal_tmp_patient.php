<!--Modal Read patient from excel -->
<div class="modal fade modal-primary" id="modal_patient_tmp" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg" style="width: 90% !important; ">
		<div class="modal-content" style="height: auto; min-height: 100%; border-radius: 0;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;
				<b>បញ្ជីឈ្មោះអ្នកជំងឺពីRRT</b>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4">
						<label class="control-label">
							កាលបរិច្ឆេទប្រមូលសំណាក <sup class="fa fa-asterisk" style="font-size:8px"></sup></label>
						<div class="input-group">
							<input type="text" class="form-control dtpicker" name="collected_date" tabindex="6">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>							
						</div>
					</div>
					<div class="col-md-4">
						<label class="control-label">ជ្រើសរើសRRT</label>
						<select name="rrt_dd" class="form-control" tabindex="8">
							<option value="0"><?php echo _t('global.choose'); ?></option>
							<?php
								foreach ($rrt_user as $user) {
									echo "<option value='".$user['id']."'>".$user['username']."</option>";
								}
							?>
						</select>
					</div>
					<div class="col-md-4">
						<label class="control-label">​&nbsp; &nbsp;</label><br />
						<button type="button" class="btn btn-default btn-primary"​ id="btnPullData">
						បង្ហាញ
						</button>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table table-sm" id="tblListPatient" name="tblListPatient" align="center">
						<thead>
							<tr>
								<th width="30">លរ</th>
								<th width="100">លេខសំគាល់អ្នកជំងឺ</th>
								<th width="100">ឈ្មោះ</th>
								<th width="20">អាយុ</th>
								<th width="25">ភេទ</th>
								<th width="70">លេខទូរសព្ទ័</th>
								<th width="100">កន្លែងស្នាក់នៅ</th>
								<th width="80">ខេត្ត</th>
								<th width="80">ស្រុក</th>
								<th width="80">ឃុំ</th>
								<th width="80">ភូមិ</th>
								<th width="100">មកពីប្រទេស</th>
								<th width="90">សញ្ញាតិ</th>
								<th width="100">ថ្ងៃខែមកដល់</th>
								<th width="100">លិខិតឆ្លងដែន</th>
								<th width="120">លេខជើងហោះហើរ</th>
								<th width="80">លេខកៅអី</th>
								<th width="120">ទីកន្លែងយកសំណាក</th>
								<th width="100">ថ្ងៃយកសំណាក</th>
								<th width="80">សំណាកលើកទី</th>
								<th width="120">ឈ្មោះអ្នកប្រមូល</th>
								<th width="100">លេខទូរសព្ទ័</th>
								<th width="130">យកទៅមន្ទីរពិសោធន៏</th>								
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
                
			</div>
			<div class="modal-footer">	
				<button type="button" class="btn btn-default btn-primary disabled" id="btnImportPatient">
					ទាញចូល
				</button>
				<button type="button" class="btn btn-default" data-dismiss='modal'>
					<?php echo _t('global.close'); ?>
				</button>
			</div>
		</div>
	</div>
</div>