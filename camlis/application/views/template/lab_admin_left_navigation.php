<div class="col-sm-3 adm-left-menu">
	<div class="list-group">
		<a class="list-group-item <?php echo $cur_page == 'laboratory' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('manage/laboratory'); ?>"><?php echo _t('global.lab_info'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'performer' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('manage/performer'); ?>"><?php echo _t('manage.performer'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'machine' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('manage/machine'); ?>"><?php echo _t('global.machine'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'sample_source' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('manage/sample_source'); ?>"><?php echo _t('global.sample_source'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'requester' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('manage/requester'); ?>"><?php echo _t('manage.requester'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'ref_range' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('manage/ref_range'); ?>"><?php echo _t('manage.ref_range'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'payment_type' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('manage/payment_type'); ?>"><?php echo _t('global.payment_type'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'test_payment' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('manage/test_payment'); ?>"><?php echo _t('manage.test_payment'); ?></a>
        <a class="list-group-item <?php echo $cur_page == 'documents' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('manage/documents'); ?>"><?php echo _t('manage.document_template'); ?></a>
        <a class="list-group-item <?php echo $cur_page == 'user' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('manage/user'); ?>"><?php echo _t('global.user'); ?></a>
	</div>
</div>