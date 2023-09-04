<?php
    $report_generation_perm = $this->aauth->is_allowed('generate_individual_report') ||
                              $this->aauth->is_allowed('generate_bacteriology_report') ||
                              $this->aauth->is_allowed('generate_aggregate_report') ||
                              $this->aauth->is_allowed('generate_ward_report') ||
                              $this->aauth->is_allowed('generate_amr_report') ||
                              $this->aauth->is_allowed('generate_tat_report');
?>

<nav id="main-menu">
    <?php if ($this->aauth->is_loggedin()) { ?>
    <ul>
		<li class="<?php echo $cur_main_page == 'laboratory' ? 'selected' : ''; ?>"><a href="<?php echo $this->app_language->site_url('laboratory'); ?>"><?php echo _t('nav.home'); ?></a></li>

        <?php if (!empty($laboratoryInfo->labID)) { ?>
        <li class="<?php echo $cur_main_page == 'patient' ? 'selected' : ''; ?>"><a href="<?php echo $this->app_language->site_url('patient'); ?>"><?php echo _t('nav.patient'); ?></a></li>
		<li class="dropdown-list <?php echo $cur_main_page == 'patient_sample' ? 'selected' : ''; ?>">
			<a href="javascript:void(0)"><?php echo _t('nav.sample'); ?> &nbsp;<i class='fa fa-chevron-down nav-indicator'></i></a>
			<ul>
				<?php if ($this->aauth->is_allowed('add_psample')) { ?>
				<li><a href="<?php echo $this->app_language->site_url('sample/new'); ?>"><?php echo _t('nav.new_sample'); ?></a></li>
				<?php } ?>
				<li><a href="<?php echo $this->app_language->site_url('sample/view'); ?>"><?php echo _t('nav.view_sample'); ?></a></li>
			</ul>
		</li>
        <?php if ($report_generation_perm || $this->aauth->is_allowed('generate_graph') || $this->aauth->is_allowed('generate_map') || $this->aauth->is_allowed('generate_query_extractor')) { ?>
		<li class="dropdown-list <?php echo $cur_main_page == 'report' ? 'selected' : ''; ?>">
			<a href="javascript:void(0)"><?php echo _t('nav.analysis'); ?> &nbsp;<i class='fa fa-chevron-down nav-indicator'></i></a>
			<ul>
                <?php if ($report_generation_perm) { ?>
				<li><a href="<?php echo $this->app_language->site_url('report'); ?>"><?php echo _t('nav.report_generation'); ?></a></li>
                <?php } ?>
                <?php if ($this->aauth->is_allowed('generate_graph')) { ?>
				<li><a href="<?php echo $this->app_language->site_url('report/graph'); ?>"><?php echo _t('nav.graph'); ?></a></li>
                <?php } ?>
                <?php if ($this->aauth->is_allowed('generate_map')) { ?>
				<li><a href="<?php echo $this->app_language->site_url('report/map_generation'); ?>"><?php echo _t('nav.map_generation'); ?></a></li>
                <?php } ?>
                <?php if ($this->aauth->is_allowed('generate_query_extractor')) { ?>
				<li><a href="<?php echo $this->app_language->site_url('report/query_extractor'); ?>"><?php echo _t('nav.query_extractor'); ?></a></li>
                <?php } ?>
                <?php if ($this->aauth->is_allowed('financial_report')) { ?>
                    <li><a href="<?php echo $this->app_language->site_url('report/financial'); ?>"><?php echo _t('nav.financial_report'); ?></a></li>
                <?php } ?>
			</ul>
		</li>
        <?php } ?>
		<?php if ($this->aauth->is_allowed('access_camlis_lab_admin_page')) { ?>
		<li class="<?php echo $cur_main_page == 'lab_admin' ? 'selected' : ''; ?>"><a href="<?php echo $this->app_language->site_url('manage/laboratory'); ?>"><?php echo _t('nav.manage_laboratory'); ?></a></li>
		<?php }} ?>

		<?php if ($this->aauth->is_allowed('access_camlis_admin_page')) { ?>
		<li class="<?php echo $cur_main_page == 'camlis_admin' ? 'selected' : ''; ?>"><a href="<?php echo $this->app_language->site_url('admin/laboratory'); ?>"><?php echo _t('nav.manage_camlis'); ?></a></li>
		<?php } ?>
	</ul>
    <?php } ?>
</nav>