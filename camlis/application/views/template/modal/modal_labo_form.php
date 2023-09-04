<!-- Print Preview Modal -->
<div class="modal fade" id="print_preview_labo_form_modal" style="padding-top:40px; background: rgba(0, 0, 0, 0.54);">
	<div class="print-preview-header">
        <div class="presult-pagination">
            <input type="text" class="page-number" value="1" onfocus="this.select()"> /
            <span class="page-count">1</span>
        </div>
		<ul>			
			<li style="display: inline-block;" class="print">
				<a href="javascript:void(0)" id="doPrinting"><i class="fa fa-print"></i>&nbsp;<b><?php echo _t('global.print'); ?></b></a>
			</li>
		</ul>		
		<span class="close-preview" data-dismiss='modal' title="<?php echo _t('global.close'); ?>"><i class="fa fa-times"></i></span>
	</div>
	<div class="modal-dialog A4-portrait">
		
	</div>   
</div>