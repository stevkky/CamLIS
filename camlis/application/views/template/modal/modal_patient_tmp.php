<!--Modal Read patient from excel -->
<div class="modal fade modal-danger" id="modal_result_line_list" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;
				<b>លទ្ធផលដែលបានទទួល</b>
			</div>
			<div class="modal-body">
                <table class="table table-sm" id="tblResultLineList" name="tblResultLineList">
                    <thead>
                        <tr>                            
                            <th>លេខសំគាល់អ្នកជំងឺ</th>
                            <th>ឈ្មោះ</th>
                            <th>លទ្ធផល</th>
                            <th>លេខសំណាក</th>
                            <th>លទ្ធផល</th>
							<th>តេស្ត</th>
							<th>មុខងារ</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
			</div>
			<div class="modal-footer">	
				<button type="button" class="btn btn-primary btnPrintCovidForm" data-psample_id="" id="printAll">
					បោះពុម្ភទាំងអស់
				</button>			
				<button type="button" class="btn btn-default" data-dismiss='modal'>
					<?php echo _t('global.cancel'); ?>
				</button>
			</div>
		</div>
	</div>
</div>