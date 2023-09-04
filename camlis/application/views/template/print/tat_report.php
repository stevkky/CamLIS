<!DOCTYPE html>
<html>
<head>
    <title>Test</title>
    <link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/A4_print_tmp.css?_='.time()) ?>">
    <link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/print/tat_report.css?_='.time()) ?>">
</head>
<body>
    <page size="A4" orientation="landscape">
        <table>
            <tbody>
                <tr>
                    <th style="width: 100px;">
                        <?php
                            $laboratory_logo_url = site_url('assets/camlis/images/moh_logo.png');
                            if (isset($laboratoryInfo->photo) && !empty($laboratoryInfo->photo)  && file_exists('./assets/camlis/images/laboratory/'.$laboratoryInfo->photo)) {
                                $laboratory_logo_url = site_url('assets/camlis/images/laboratory/'.$laboratoryInfo->photo);
                            }
                        ?>
                        <img src="<?php echo $laboratory_logo_url; ?>" alt="Logo" style="width: 100px;">
                    </th>
                    <th class="text-center">
                        <div class="KhmerMoulLight" style="font-size: 12.5pt; font-weight: bold;"><?php echo $laboratoryInfo->name_kh; ?></div>
                        <div class="KhmerMoulLight" style="font-size: 12.5pt; font-weight: bold;"><?php echo $laboratoryInfo->name_en; ?></div>
                        <div class="Hanuman" style="font-size: 12.5pt; font-weight: bold; margin-top: 20px;"><?php echo _t('report.tat_full'); ?></div>
                        <div class="Hanuman" style="font-size: 12.5pt; font-weight: bold; margin-top: 5px;"><?php echo $startDate.' - '.$endDate; ?></div>
                    </th>
                    <th style="width: 100px;"></th>
                </tr>
            <?php foreach($report_data as $department => $group_result) { ?>
                <tr style="page-break-after: avoid;">
                    <th colspan="3" class="text-left" style="padding-bottom: 10px; padding-top: 30px;"><b><?php echo preg_replace('/^([0-9])(#+)(.+)$/', '$3', $department); ?></b></th>
                </tr>
                <tr>
                    <td colspan="3">
                        <table class="result">
                            <tbody>
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">Test Name</th>
                                    <th rowspan="2">Total</th>
                                    <th colspan="2">Collected - Received</th>
                                    <th colspan="2">Received - Printed</th>
                                    <th colspan="2">Collected - Printed</th>
                                </tr>
                                <tr>
                                    <th>Urgent</th>
                                    <th>Routine</th>
                                    <th>Urgent</th>
                                    <th>Routine</th>
                                    <th>Urgent</th>
                                    <th>Routine</th>
                                </tr>
                                <?php
                                    $i = 1;
                                    foreach($group_result as $group_result_name => $data) { ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $group_result_name; ?></td>
                                        <td><?php echo $data->get('URGENT')->count() + $data->get('ROUTINE')->count(); ?></td>
                                        <td><?php echo floor($data->get('URGENT')->avg('diff_col_rec')).' ('.$data->get('URGENT')->min('diff_col_rec').' - '.$data->get('URGENT')->max('diff_col_rec').')';  ?></td>
                                        <td><?php echo floor($data->get('ROUTINE')->avg('diff_col_rec')).' ('.$data->get('ROUTINE')->min('diff_col_rec').' - '.$data->get('ROUTINE')->max('diff_col_rec').')';  ?></td>
                                        <td><?php echo floor($data->get('URGENT')->avg('diff_rec_print')).' ('.$data->get('URGENT')->min('diff_rec_print').' - '.$data->get('URGENT')->max('diff_rec_print').')';  ?></td>
                                        <td><?php echo floor($data->get('ROUTINE')->avg('diff_rec_print')).' ('.$data->get('ROUTINE')->min('diff_rec_print').' - '.$data->get('ROUTINE')->max('diff_rec_print').')';  ?></td>
                                        <td><?php echo floor($data->get('URGENT')->avg('diff_col_print')).' ('.$data->get('URGENT')->min('diff_col_print').' - '.$data->get('URGENT')->max('diff_col_print').')';  ?></td>
                                        <td><?php echo floor($data->get('ROUTINE')->avg('diff_col_print')).' ('.$data->get('ROUTINE')->min('diff_col_print').' - '.$data->get('ROUTINE')->max('diff_col_print').')';  ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </page>
    <script>
        <?php if (!empty($action) && $action == "print") { ?>
            window.print();
        <?php } ?>
    </script>
</body>
</html>