<?php
    $report_generation_perm = $this->aauth->is_allowed('generate_individual_report') ||
                              $this->aauth->is_allowed('generate_bacteriology_report') ||
                              $this->aauth->is_allowed('generate_aggregate_report') ||
                              $this->aauth->is_allowed('generate_ward_report') ||
                              $this->aauth->is_allowed('generate_amr_report') ||
                              $this->aauth->is_allowed('generate_tat_report');
?>

<nav id="main-menu">
    <?php if ($this->aauth->is_loggedin()): ?>
        <ul>
            <!-- home -->
            <li class="<?php echo $cur_main_page == 'laboratory' ? 'selected' : ''; ?>">
                <a href="<?php echo $this->app_language->site_url('laboratory'); ?>"><?php echo _t('nav.home'); ?></a>
            </li>
            <!-- choose laboratory -->
            <?php if (!empty($laboratoryInfo->labID)): ?>
                <!-- patient -->
                <li class="<?php echo $cur_main_page == 'patient' ? 'selected' : ''; ?>">
                    <a href="<?php echo $this->app_language->site_url('patient'); ?>"><?php echo _t('nav.patient'); ?></a>
                </li>
                <!-- Request sample -->
                <?php if ($this->aauth->is_allowed('request_sample') || $this->aauth->is_allowed('view_request')): ?>
                    <li class="dropdown-list <?php echo $cur_main_page == 'request' ? 'selected' : ''; ?>">
                        <a href="javascript:void(0)"><?php echo _t('nav.request'); ?> &nbsp;<i class='fa fa-chevron-down nav-indicator'></i></a>
                        <ul>
                            <!-- add new request sample -->
                            <?php if ($this->aauth->is_allowed('request_sample')): ?>
                                <li><a href="<?php echo $this->app_language->site_url('request/add'); ?>"><?php echo _t('nav.new_request'); ?></a></li>
                            <?php endif ?>
                            <!-- view request sample -->
                            <?php if ($this->aauth->is_allowed('view_request')): ?>
                                <li><a href="<?php echo $this->app_language->site_url('request'); ?>"><?php echo _t('nav.view_request'); ?></a></li>
                            <?php endif ?>
                        </ul>
                    </li>
                <?php endif ?>
                <!-- sample -->
                <li class="dropdown-list <?php echo $cur_main_page == 'patient_sample' ? 'selected' : ''; ?>">
                    <a href="javascript:void(0)"><?php echo _t('nav.sample'); ?> &nbsp;<i class='fa fa-chevron-down nav-indicator'></i></a>
                    <ul>
                        <!-- add sample -->
                        <?php if ($this->aauth->is_allowed('add_psample')): ?>
                            <li><a href="<?php echo $this->app_language->site_url('sample/new'); ?>"><?php echo _t('nav.new_sample'); ?></a></li>
                        <?php endif ?>
                        <!-- view sample -->
                        <li><a href="<?php echo $this->app_language->site_url('sample/view'); ?>"><?php echo _t('nav.view_sample'); ?></a></li>
                    </ul>
                </li>
                <!-- analysis -->
                <?php if ($report_generation_perm || $this->aauth->is_allowed('generate_graph') || $this->aauth->is_allowed('generate_map') || $this->aauth->is_allowed('generate_query_extractor')): ?>
                    <li class="dropdown-list <?php echo $cur_main_page == 'report' ? 'selected' : ''; ?>">
                        <a href="javascript:void(0)"><?php echo _t('nav.analysis'); ?> &nbsp;<i class='fa fa-chevron-down nav-indicator'></i></a>
                        <ul>
                            <!-- report generation -->
                            <?php if ($report_generation_perm): ?>
                                <li><a href="<?php echo $this->app_language->site_url('report'); ?>"><?php echo _t('nav.report_generation'); ?></a></li>
                            <?php endif ?>
                            <!-- data visualizer -->
                            <?php if ($this->aauth->is_allowed('generate_graph')): ?>
                                <li><a href="<?php echo $this->app_language->site_url('report/graph'); ?>"><?php echo _t('nav.graph'); ?></a></li>
                            <?php endif ?>
                            <!-- map generation -->
                            <?php if ($this->aauth->is_allowed('generate_map')): ?>
                                <li><a href="<?php echo $this->app_language->site_url('report/map_generation'); ?>"><?php echo _t('nav.map_generation'); ?></a></li>
                            <?php endif ?>
                            <!-- query extractor -->
                            <?php if ($this->aauth->is_allowed('generate_query_extractor')): ?>
                                <li><a href="<?php echo $this->app_language->site_url('report/query_extractor'); ?>"><?php echo _t('nav.query_extractor'); ?></a></li>
                            <?php endif ?>
                            <!-- financial report -->
                            <?php if ($this->aauth->is_allowed('financial_report')): ?>
                                <li><a href="<?php echo $this->app_language->site_url('report/financial'); ?>"><?php echo _t('nav.financial_report'); ?></a></li>
                            <?php endif ?>
                            <!-- audit user -->
                            <?php if ($this->aauth->is_allowed('audit_user')): ?>
                                <li><a href="<?php echo $this->app_language->site_url('report/audit'); ?>"><?php echo _t('nav.audit_user_report'); ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <!-- manage laboratory -->
                    <?php if ($this->aauth->is_allowed('access_camlis_lab_admin_page')): ?>
                        <li class="<?php echo $cur_main_page == 'lab_admin' ? 'selected' : ''; ?>">
                            <a href="<?php echo $this->app_language->site_url('manage/laboratory'); ?>"><?php echo _t('nav.manage_laboratory'); ?></a>
                        </li>
                    <?php endif ?>
                <?php endif ?>
            <?php endif ?>
            <!-- manage camlis -->
            <?php if ($this->aauth->is_allowed('access_camlis_admin_page')): ?>
                <li class="<?php echo $cur_main_page == 'camlis_admin' ? 'selected' : ''; ?>">
                    <a href="<?php echo $this->app_language->site_url('admin/laboratory'); ?>"><?php echo _t('nav.manage_camlis'); ?></a>
                </li>
            <?php endif ?>
            <!-- about -->
            <li class="dropdown-list <?php echo $cur_main_page == 'about_camlis' ? 'selected' : ''; ?>">
                <a href="javascript:void(0)" id="users_seen_update">
                    <?php echo _t('nav.about_camlis'); ?> &nbsp;
                    <i class='fa fa-chevron-down nav-indicator'></i>
                    <?php if (isset($system_update)): ?>
                        <?php if ($system_update > 0): ?>
                            <span class="notification badge"><?php echo _t('nav.new'); ?></span>
                        <?php endif ?>
                    <?php endif ?>
                </a>
                <ul>
                    <?php if ($this->aauth->is_admin()): ?>
                        <li><a href="javascript:void(0)" id="there_are_update">Update</a></li>
                    <?php endif ?>
                    <li><a href="<?php echo $this->app_language->site_url('about/sop'); ?>"><?php echo _t('nav.sop'); ?></a></li>
                    <li><a href="<?php echo $this->app_language->site_url('about/update'); ?>"><?php echo _t('nav.update'); ?></a></li>
                    <li><a href="<?php echo $this->app_language->site_url('about/help'); ?>"><?php echo _t('nav.help'); ?></a></li>
                    <li><a href="http://arcg.is/biXPa"><?php echo _t('nav.about'); ?></a></li>
                </ul>
            </li>
        </ul>
    <?php endif ?>
</nav>