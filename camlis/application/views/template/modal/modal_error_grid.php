<!--Modal Read patient from excel -->
<div class="modal fade modal-danger" id="modal_error_line_list" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg" style="width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;
				<b>តារាងប្រាប់ពីបញ្ហាទិន្នន័យ</b>
			</div>
			<div class="modal-body">
                <table class="table table-sm text-center" id="tblErrorLineList" name="tblErrorLineList">
                    <thead>
                        <tr>
                            <th>លេខសំគាល់អ្នកជំងឺ</th>
                            <th>ឈ្មោះ</th>
							<th>អាយុ</th>
							<th>ភេទ</th>
							<th>ខេត្ត</th>
							<th>ស្រុក</th>
							<th>ឃុំ</th>
							<th>ភូមិ</th>
							<th>ថ្ងៃយកសំណាក</th>
							<th>សំណាកលើកទី</th>
                        </tr>
                    </thead>
                    <tbody align="center">
                    </tbody>
                </table>
			</div>
			<div class="modal-footer">						
				<button type="button" class="btn btn-default" data-dismiss='modal'>
					<?php echo _t('global.cancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>