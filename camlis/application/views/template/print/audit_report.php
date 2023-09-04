<!DOCTYPE html>
<html>
<head>
    <title>Audit trail user Report</title>
    <link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/print/financial_report.css'); ?>">
</head>
<body>
<page size="A4" orientation="landscape">
    <h4 class="KhmerMoulLight">
        <?php $name = 'name_'.$app_lang; ?>
    </h4>
    <h3 class="text-center">Audit trial user report</h3>
    <h4 class="text-center">
        <?php echo $start_date ? $start_date : ''; ?>
        <?php echo _t('to'); ?>
        <?php echo $end_date ? $end_date : ''; ?>
    </h4>

    <table class="report-result">
        <thead>
            <tr>
                <th>Lab name</th>
				<th>User name</th>
                <th>User role</th>
                <th>IP address</th>
                <th>Date</th>
            </tr>
        </thead>
            <?php foreach ($audits as $audit): ?>
                <tr>
				    <td><?php echo $audit->$name; ?></td>
                    <td><?php echo $audit->fullname; ?></td>
                    <td><?php echo $audit->definition; ?></td>
                    <td><?php echo $audit->ip_address; ?></td>
                    <td><?php echo $audit->timestamp; ?></td>
                </tr>
             <?php endforeach ?>
        <tbody>
            
        </tbody>
    </table>
</page>
    <?php if ($type == "print"): ?>
        <script>
            window.print();
        </script>
    <?php endif ?>
</body>
</html>