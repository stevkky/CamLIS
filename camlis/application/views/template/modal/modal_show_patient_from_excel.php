<!--Modal Read patient from excel -->
<div class="modal fade modal-primary modal-wide fixed-footer" id="modal-read-patient-from-excel" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg" style="width:98%;">
		<div class="modal-content" style="height: 90%;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;
				<b>ទំរង់បញ្ជូលទិន្នន័យអ្នកជំងឺជាបន្ទាត់ត្រង់</b>
			</div>
			<div class="modal-body" style=" max-height: calc(100% - 60px); overflow-y: scroll;">
				<p>(*): ពត៍មានចាំបាច់ ត្រូវតែបំពេញ | <span class="text-warning bg-dark" style="background-color: yellow;">*អាចបញ្ចូលម្តងបានច្រើនបំផុតតែ 100ជួរប៉ុណ្ណោះ សំរាប់បច្ចុប្បន្ន</span></p>
				<div class="col-md-12 mt-3" style="margin-top:10px; position:relative; max-width:99%; overflow:auto;">
					<div id="spreadsheet1"></div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="row form-horizontal">
					<div class="col-sm-5 text-left">
					&nbsp;
					<?php 
					if($_SESSION['roleid'] == 1){
					?>
						<button type="button" class="btn btn-success" id="btnOpenModalUpload___"><i class="fa fa-upload"></i>&nbsp;<?php echo _t('global.upload'); ?></button>
						<button type="button" class="btn btn-primary" id="btnOpenRrtFormModal"><i class="fa fa-upload"></i>&nbsp;
							ទាញទិន្នន័យពីRRT
						</button>
						<button type="button" class="btn btn-primary" id="btnSaveListTesting"><i class="fa fa-floppy-o"></i>&nbsp;
							សាកល្បងរក្សាទុក
						</button>
						<?php
					}?>
					</div>
					<div class="col-sm-7 text-right">
						<button type="button" class="btn btn-primary" id="btnSaveList"><i class="fa fa-floppy-o"></i>&nbsp;
							<?php echo _t('global.save'); ?>
						</button>
						<button type="button" class="btn btn-default" data-dismiss='modal'>
							<?php echo _t('global.cancel'); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>