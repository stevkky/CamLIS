<?php
    $laboratoryInfo = (array)$laboratoryInfo;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test</title>
    <link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/A4_print_tmp.css?_='.time()) ?>">
    <link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/print/patient_sample_test.css?_='.time()) ?>">
</head>
<body>
    <page size="A4" orientation="landscape">
        <section>
            <div class="header">
                <table>
                    <tr>
                        <td class="logo">
                            <?php
                            $logo = site_url('assets/camlis/images/moh_logo.png');
                            if (isset($laboratoryInfo['photo']) && !empty($laboratoryInfo['photo'])  && file_exists('./assets/camlis/images/laboratory/'.$laboratoryInfo['photo'])) {
                                $logo = site_url('assets/camlis/images/laboratory/'.$laboratoryInfo['photo']);
                            }
                            ?>
                            <img src="<?php echo $logo; ?>" alt="Logo" style="width: 50px;">
                        </td>
                        <td class="title">
                            <b class="laboratory-name"><?php echo $laboratoryInfo['name_'.$app_lang]; ?></b>
                            <b class="report-title"><?php echo _t('sample.request_form'); ?></b>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="patient-sample-info">
                <div class="patient-info">
                    <table>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('patient.patient_id'); ?> :</td>
                            <td><?php echo $patient['patient_code']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('patient.patient_name'); ?> :</td>
                            <td><?php echo $patient['name']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('patient.sex'); ?> : </td>
                            <td>
                                <?php
                                    $sex = "";
                                    if ($patient['sex'] == 1 || $patient['sex'] == 'M') $sex = 'global.male';
                                    else if ($patient['sex'] == 2 || $patient['sex'] == 'F') $sex = 'global.female';
                                    echo _t($sex);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('patient.age'); ?> : </td>
                            <td>
                                <?php
                                    $age = calculateAge($patient['dob'], $patient_sample['collected_date']);
                                ?>
                                <span class="age-year"></span> <?php echo $age->y.' '._t('global.year') ?> &nbsp;
                                <span class="age-month"></span> <?php echo $age->m.' '._t('global.month') ?> &nbsp;
                                <span class="age-day"></span> <?php echo ($age->days > 0 ? $age->d : 1).' '._t('global.day') ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="sample-info">
                    <table>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('sample.sample_number'); ?> :</td>
                            <td><?php echo $patient_sample['sample_number']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('sample.sample_source'); ?> :</td>
                            <td><?php echo $patient_sample['sample_source_name']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('sample.requested_by'); ?> :</td>
                            <td><?php echo $patient_sample['requester_name']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('sample.requested_date'); ?> :</td>
                            <td>
                                <?php
                                $date = DateTime::createFromFormat('Y-m-d H:i:s', $patient_sample['collected_date'].' '.$patient_sample['collected_time']);
                                echo $date ? $date->format('d/m/Y H:i') : '';
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="test-list">
                <ul>
                    <?php
                    foreach($patient_sample_test_groups as $patient_sample_test_group) {
                        $header = "";
                        $tests  = [];
                        foreach($patient_sample_test_group as $patient_sample_test) {
                            $header = $patient_sample_test['department_name']."-".$patient_sample_test['sample_name'];
                            //$tests .= "<li>".$patient_sample_test['group_result']."</li>";
                            $tests[] = $patient_sample_test['group_result'];
                        }

                        echo "<li class='header'>";
                        echo "<b>".$header."</b>";
                        echo "<ul><li>";
                        echo implode(',&nbsp;&nbsp;&nbsp;', $tests);
                        echo "</li></ul>";
                        echo "</li>";
                    }
                    ?>
                </ul>
            </div>
            <div class="footer" style="padding-right: .5cm">
                <div class="info">
                    <div>
                        <?php
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', $patient_sample['received_date'].' '.$patient_sample['received_time']);
                            $date = $date ? $date->format('d/m/Y H:i') : str_repeat('.', 25);
                            echo _t('receive_date').' : '.$date;
                        ?>
                    </div>
                    <div class="text-right"><?php echo _t('receive_by').' :'.str_repeat('.', 25); ?></div>
                </div>
                <div class="address">
                    <?php echo $laboratoryInfo['address_'.$app_lang]; ?>
                </div>
            </div>
        </section>

        <section>
            <div class="header">
                <table>
                    <tr>
                        <td class="logo">
                            <?php
                            $logo = site_url('assets/camlis/images/moh_logo.png');
                            if (isset($laboratoryInfo['photo']) && !empty($laboratoryInfo['photo'])  && file_exists('./assets/camlis/images/laboratory/'.$laboratoryInfo['photo'])) {
                                $logo = site_url('assets/camlis/images/laboratory/'.$laboratoryInfo['photo']);
                            }
                            ?>
                            <img src="<?php echo $logo; ?>" alt="Logo" style="width: 50px;">
                        </td>
                        <td class="title">
                            <b class="laboratory-name"><?php echo $laboratoryInfo['name_'.$app_lang]; ?></b>
                            <b class="report-title"><?php echo _t('sample.request_form'); ?></b>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="patient-sample-info">
                <div class="patient-info">
                    <table>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('patient.patient_id'); ?> :</td>
                            <td><?php echo $patient['patient_code']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('patient.patient_name'); ?> :</td>
                            <td><?php echo $patient['name']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('patient.sex'); ?> : </td>
                            <td>
                                <?php
                                    $sex = "";
                                    if ($patient['sex'] == 1 || $patient['sex'] == 'M') $sex = 'global.male';
                                    else if ($patient['sex'] == 2 || $patient['sex'] == 'F') $sex = 'global.female';
                                    echo _t($sex);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('patient.age'); ?> : </td>
                            <td>
                                <?php
                                    $age = calculateAge($patient['dob'], $patient_sample['collected_date']);
                                ?>
                                <span class="age-year"></span> <?php echo $age->y.' '._t('global.year') ?> &nbsp;
                                <span class="age-month"></span> <?php echo $age->m.' '._t('global.month') ?> &nbsp;
                                <span class="age-day"></span> <?php echo ($age->days > 0 ? $age->d : 1).' '._t('global.day') ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="sample-info">
                    <table>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('sample.sample_number'); ?> :</td>
                            <td><?php echo $patient_sample['sample_number']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('sample.sample_source'); ?> :</td>
                            <td><?php echo $patient_sample['sample_source_name']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('sample.requested_by'); ?> :</td>
                            <td><?php echo $patient_sample['requester_name']; ?></td>
                        </tr>
                        <tr>
                            <td class="text-right info-label"><?php echo _t('sample.requested_date'); ?> :</td>
                            <td>
                                <?php
                                    $date = DateTime::createFromFormat('Y-m-d H:i:s', $patient_sample['collected_date'].' '.$patient_sample['collected_time']);
                                    echo $date ? $date->format('d/m/Y H:i') : '';
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="test-list">
                <ul>
                    <?php
                    foreach($patient_sample_test_groups as $patient_sample_test_group) {
                        $header = "";
                        $tests  = [];
                        foreach($patient_sample_test_group as $patient_sample_test) {
                            $header = $patient_sample_test['department_name']."-".$patient_sample_test['sample_name'];
                            //$tests .= "<li>".$patient_sample_test['group_result']."</li>";
                            $tests[] = $patient_sample_test['group_result'];
                        }

                        echo "<li class='header'>";
                        echo "<b>".$header."</b>";
                        echo "<ul><li>";
                        echo implode(',&nbsp;&nbsp;&nbsp;', $tests);
                        echo "</li></ul>";
                        echo "</li>";
                    }
                    ?>
                </ul>
            </div>
            <div class="footer" style="padding-left: .5cm">
                <div class="info">
                    <div>
                        <?php
                        $date = DateTime::createFromFormat('Y-m-d H:i:s', $patient_sample['received_date'].' '.$patient_sample['received_time']);
                        $date = $date ? $date->format('d/m/Y H:i') : str_repeat('.', 25);
                        echo _t('receive_date').' : '.$date;
                        ?>
                    </div>
                    <div class="text-right"><?php echo _t('receive_by').' :'.str_repeat('.', 25); ?></div>
                </div>
                <div class="address">
                    <?php echo $laboratoryInfo['address_'.$app_lang]; ?>
                </div>
            </div>
        </section>
    </page>

    <?php if (isset($action) && $action == 'print') { ?>
        <script>
            window.print();
        </script>
    <?php } ?>
</body>
</html>