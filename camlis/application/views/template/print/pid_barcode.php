<!DOCTYPE html>
<html>
<head>
    <title>Print Barcode</title>
    <meta name="viewport" content="width=device-width, initial-scale=.5, maximum-scale=12.0, minimum-scale=.25, user-scalable=yes"/>
    <link rel="stylesheet" href="<?php echo site_url('assets/camlis/css/print/pid_barcode.css?_='.time()) ?>">
</head>
<body>    
    <page size="papperroll">       
        <table border="0" width="100%">
            
        </table>
    </page>
    <script>
        window.print();
    </script>   
</body>
</html>

