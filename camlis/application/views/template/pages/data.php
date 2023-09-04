<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>    
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-4">
          <label for="">camlis_patient_sample: <?php echo count($camlis_patient_sample)?> rows</label>
          <div style="max-height: 500px; overflow-x:auto;">
            <?php 
              echo "<pre>";
              print_r($camlis_patient_sample);
              echo "</pre>";
            ?>
          </div>
        </div>
        <div class="col-sm-4">
          <label for="">camlis_pmrs_patien: <?php echo count($camlis_pmrs_patient)?> rows</label>
          <div style="max-height: 500px; overflow-x:auto;">
            <pre>
            <?php print_r($camlis_pmrs_patient)?>
            </pre>
          </div>
          
        </div>
        <div class="col-sm-4">
          <label for="">camlis_outside_patient: <?php echo count($camlis_outside_patient)?> rows</label>
          <div style="max-height: 500px; overflow-x:auto;">
          <pre>
            <?php print_r($camlis_outside_patient)?>
          </pre>
          </div>
        </div>

        <div class="col-sm-4">
          <label for="">camlis_patient_sample_tests: <?php echo count($camlis_patient_sample_tests)?> rows</label>
          <div style="max-height: 500px; overflow-x:auto;">
            <pre>
            <?php print_r($camlis_patient_sample_tests)?>
            </pre>
          </div>
        </div>

        <div class="col-sm-4">
          <label for="">camlis_ptest_result: <?php echo count($camlis_ptest_result)?> rows</label>
          <div style="max-height: 500px; overflow-x:auto;">
            <pre>
            <?php print_r($camlis_ptest_result)?>
            </pre>
          </div>
        </div>

        <div class="col-sm-4">
          <label for="">camlis_ptest_result_antibiotic: <?php echo count($camlis_ptest_result_antibiotic)?> rows</label>
          <div style="max-height: 500px; overflow-x:auto;">
            <pre>
            <?php print_r($camlis_ptest_result_antibiotic)?>
            </pre>
          </div>
        </div>

        <div class="col-sm-4">
          <label for="">camlis_patient_sample_detail: <?php echo count($camlis_patient_sample_detail)?> rows</label>
          <div style="max-height: 500px; overflow-x:auto;">
            <pre>
            <?php print_r($camlis_patient_sample_detail)?>
            </pre>
          </div>
        </div>
      </div>
    </div>
    <form method="post" action="<?php echo base_url()."data/uploading"?>">
    <button type="submit" class="btn btn-primary btn-block">Upload</button>
    </form>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>    
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
  </body>
</html>