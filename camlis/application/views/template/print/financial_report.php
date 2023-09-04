<!DOCTYPE html>
<html>
<head>
    <title>Financial Report</title>
    <link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/print/financial_report.css'); ?>">
</head>
<body>
<page size="A4" orientation="landscape">
    <h4 class="KhmerMoulLight">
        <?php
            $name = 'name_'.$app_lang;
            echo $laboratoryInfo->$name;
        ?>
    </h4>
    <h3 class="text-center"><?php echo _t('financial_report'); ?></h3>
    <h4 class="text-center">
        <?php echo $start_date ? $start_date->format('d/m/Y') : ''; ?>
        <?php echo _t('to'); ?>
        <?php echo $end_date ? $end_date->format('d/m/Y') : ''; ?>
    </h4>

    <table class="report-result">
        <thead>
            <tr>
                <th rowspan="2"><?php echo _t('department'); ?></th>
                <th rowspan="2"><?php echo _t('test_name'); ?></th>
                <?php
                foreach ($payment_types as $payment_type) {
                    echo "<th colspan='2' class='text-center'>".$payment_type['name']."</th>";
                }
                ?>
            </tr>
            <tr>
                <?php
                foreach ($payment_types as $payment_type) {
                    echo "<th class='text-nowrap'>"._t('test_count')."</th>";
                    echo "<th class='text-nowrap'>"._t('cost')."</th>";
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
                //dd($report_data);
                foreach ($report_data as $department => $group_results) {
                    echo "<tr>";
                    echo "<td rowspan='".$group_results->count()."'>".preg_replace('/^(\d#)(.*)$/', '${2}', $department)."</td>";

                    $index = 0;
                    foreach ($group_results as $group_result => $data) {
                        echo $index > 0 ? "<tr>" : "";
                        echo "<td>".$group_result."</td>";
                        foreach($data as $d) {
                            echo "<td>".$d['count']."</td>";
                            echo "<td>".number_format($d['cost'])."</td>";
                        }
                        echo $index > 0 ? "</tr>" : "";
                        $index++;
                    }
                    echo "</tr>";

                    //Total
                    echo "<tr>";
                    echo "<td colspan='2' class='text-right total'>"._t('total')."</td>";
                    foreach ($total_by_departments[$department] as $data) {
                        echo "<td class='total'>".$data['total_count']."</td>";
                        echo "<td class='total'>".number_format($data['total_cost'])."</td>";
                    }
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</page>
<?php if ($type == "print") { ?>
    <script>
        window.print();
    </script>
<?php } ?>
</body>
</html>