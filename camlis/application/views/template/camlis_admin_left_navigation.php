<div class="col-sm-3 adm-left-menu">
	<div class="list-group">
		<a class="list-group-item <?php echo $cur_page == 'laboratory' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('admin/laboratory'); ?>"><?php echo _t('global.Laboratory'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'department' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('admin/department'); ?>"><?php echo _t('global.Department'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'sample' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('admin/sample'); ?>"><?php echo _t('global.Sample_type'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'test' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('admin/test'); ?>"><?php echo _t('global.Test'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'organism' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('admin/organism'); ?>"><?php echo _t('global.organism/antibiotic').'/'._t('global.qty'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'sample_comment' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('admin/sample_comment'); ?>"><?php echo _t('global.comment'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'patient_type' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('admin/patient_type'); ?>"><?php echo _t('global.patient_type'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'payment_type' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('admin/payment_type'); ?>"><?php echo _t('global.payment_type'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'user' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('admin/user'); ?>"><?php echo _t('global.user'); ?></a>
		<a class="list-group-item <?php echo $cur_page == 'user_role' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('admin/user_role'); ?>"><?php echo _t('global.user_role'); ?></a>
	</div>
</div>