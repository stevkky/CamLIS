<!-- Print Preview Modal -->
<div class="modal fade" id="print_preview_modal" style="padding-top:40px; background: rgba(0, 0, 0, 0.54);">
	<div class="print-preview-header">
        <div class="presult-pagination">
            <input type="text" class="page-number" value="1" onfocus="this.select()"> /
            <span class="page-count">1</span>
        </div>
		<ul>
			<?php if ($this->aauth->is_allowed('verify_patient_result') || $this->aauth->is_allowed('reverify_patient_result')): ?>
				<li style="display: inline-block;" class="approve" reverify="<?php echo $this->aauth->is_allowed('reverify_patient_result'); ?>">
					<a href="javascript:void(0)" id="approve"><i class="fa fa-check"></i>&nbsp;<b> <?php echo _t('global.approve'); ?> </b></a>
				</li>
			<?php endif ?>
			<?php if ($this->aauth->is_allowed('print_patient_result')): ?>
				<li style="display: inline-block;" class="print">
					<a href="javascript:void(0)" id="doPrinting"><i class="fa fa-print"></i>&nbsp;<b><?php echo _t('global.print'); ?></b></a>
				</li>
			<?php endif ?>
		</ul>
		<span class="close-preview-return" data-dismiss='modal' onclick="document.location.href='<?php echo $this->app_language->site_url('sample/view'); ?>'" title="<?php echo _t('global.returnsample'); ?>"><i class="fa fa-arrow-circle-right"><b><?php echo _t('global.sample'); ?></i>&nbsp;</b></span>
		<span class="close-preview" data-dismiss='modal' title="<?php echo _t('global.close'); ?>"><i class="fa fa-times"></i></span>
	</div>
	<div class="modal-dialog A4-portrait">

	</div>

    <button class="previous"><i class="fa fa-chevron-left"></i></button>
    <button class="next"><i class="fa fa-chevron-right"></i></button>
</div>