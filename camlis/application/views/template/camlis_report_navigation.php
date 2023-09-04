<div class="col-sm-3 adm-left-menu">
	<div class="list-group">
        <?php if ($this->aauth->is_allowed('generate_individual_report')) { ?>
        <a class="list-group-item <?php echo $cur_page == 'individual' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('report/individual'); ?>"><?php echo _t('report.individual'); ?></a>
        <?php } ?>
        <?php if ($this->aauth->is_allowed('generate_bacteriology_report')) { ?>
        <a class="list-group-item <?php echo $cur_page == 'bacteriology' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('report/bacteriology'); ?>"><?php echo _t('report.bacteriology'); ?></a>
        <?php } ?>
        <?php if (true) { ?>
        <a class="list-group-item <?php echo $cur_page == 'culture' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('report/culture'); ?>"><?php echo _t('report.blood_culture_report'); ?></a>
        <?php } ?>
        <?php if ($this->aauth->is_allowed('generate_aggregate_report')) { ?>
        <a class="list-group-item <?php echo $cur_page == 'aggregated' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('report/aggregated'); ?>"><?php echo _t('report.aggregated'); ?></a>
        <?php } ?>
        <?php if ($this->aauth->is_allowed('generate_ward_report')) { ?>
        <a class="list-group-item <?php echo $cur_page == 'ward' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('report/ward'); ?>"><?php echo _t('report.ward'); ?></a>
	    <?php } ?>
        <?php if ($this->aauth->is_allowed('generate_tat_report')) { ?>
        <a class="list-group-item <?php echo $cur_page == 'tat' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('report/tat'); ?>"><?php echo _t('report.TAT'); ?></a>
        <?php } ?>
		<?php if (true) { ?>
        <a class="list-group-item <?php echo $cur_page == 'rejection' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('report/sample_rejection'); ?>"><?php echo _t('report.rejection'); ?></a>
        <?php } ?>
        <?php if ($this->aauth->is_allowed('generate_amr_report')) { ?>
        <a class="list-group-item <?php echo $cur_page == 'amr' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('report/amr'); ?>"><?php echo _t('report.AMR'); ?></a>
        <?php } ?>
        <?php if ($this->aauth->is_allowed('generate_covid_report')) { ?>
        <a class="list-group-item <?php echo $cur_page == 'covid' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('report/covid'); ?>"><?php echo _t('report.covid'); ?></a>
        <?php } ?>
        <!-- 23092021-->
        <?php if (true) { ?>
        <a class="list-group-item <?php echo $cur_page == 'godata' ? 'selected' : ''; ?> " href="<?php echo $this->app_language->site_url('report/godata'); ?>">For Godata</a>
        <?php } ?>
        <?php if ($this->aauth->is_allowed('generate_microbiology_report')) { ?>
        <a class="list-group-item <?php echo $cur_page == 'micro' ? 'selected' : ''; ?>" href="<?php echo $this->app_language->site_url('report/micro'); ?>">Micro dashboard</a>
        <?php } ?>
    </div>
</div>
