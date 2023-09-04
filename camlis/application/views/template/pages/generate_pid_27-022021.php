<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">    

  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>PID GENERATOR</title>
    
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/app.min.css'); ?>">
  <!-- Add icon to homescreen -->
	
	<link rel="apple-touch-icon" sizes="57x57" href="<?php echo site_url('assets/camlis/images/homescreen') ?>/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo site_url('assets/camlis/images/homescreen') ?>/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo site_url('assets/camlis/images/homescreen') ?>/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo site_url('assets/camlis/images/homescreen') ?>/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo site_url('assets/camlis/images/homescreen') ?>/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo site_url('assets/camlis/images/homescreen') ?>/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="<?php echo site_url('assets/camlis/images/homescreen') ?>/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo site_url('assets/camlis/images/homescreen') ?>/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo site_url('assets/camlis/images/homescreen') ?>/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo site_url('assets/camlis/images/homescreen') ?>/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo site_url('assets/camlis/images/homescreen') ?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php echo site_url('assets/camlis/images/homescreen') ?>/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo site_url('assets/camlis/images/homescreen') ?>/favicon-16x16.png">
	<link rel="manifest" href="<?php echo site_url('assets/camlis/images/homescreen') ?>/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">
	
	<!-- -->
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/style.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/components.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/custom.css'); ?>">
  <link rel='shortcut icon' type='image/x-icon' href='<?php echo site_url('assets/godata/dashboard/img/gd_ico.ico'); ?>' />

	<title>Patient ID - Generator</title>
  <style>
.result{
  font-size: 18px;
  color: #34395e;
}
.center-hv{
  text-align: center;
  vertical-align: middle !important;
}
.size64{
  width: 64px;
}
</style>
  </head>
  <body> 
  <?php $app_lang	= empty($app_lang) ? 'en' : $app_lang;?>
  <div class="loader"></div>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">            
            
      
            <div class="card card-primary">
              <div class="card-header">
                <h4>PID GENERATOR</h4>
              </div>
              <div class="card-body">
                <form method="POST" action="#" id="theForm" class="needs-validation" novalidate="">
                  <div class="form-row">
                    <div class="form-group col-7">
                      <label for="province">City / Provinces</label>
                      <select class="form-control form-control-sm" id="province">
                        <option value="">ជ្រើសរើសខេត្តក្រុង</option>
                      <?php foreach($provinces as $pro){
                          echo "<option value='".$pro->code."'>".$pro->name_kh."</option>";
                      }?>
                        </select>
                      <div class="invalid-feedback" id="message">
                      សូមជ្រើសរើសខេត្ត ឬក្រុង</div>
                    </div>
                      <div class="form-group col-5">
                        <label>Number <small><i>[1 -> 500]</i></small></label>
                        <input type="text" class="form-control" id="number" placeholder="ចំនួនចន្លោះ ០១ ទៅ ៥០០" value="1" maxlength="3" onKeydown="return isNumberKey(event)" >
                        <div class="invalid-feedback" id="numbeMessage">
                          ចំនួនត្រូវនៅចន្លោះពី ០១ ទៅ ៥០០
                        </div>
                      </div>
                  </div>
                  <div class="form-group">
                      <div class="overflow-auto" style="max-height: 450px;">
                        <table class="table table-sm" id="tblResult">
                          <thead>
                            <tr>
                              <th scope="col">#</th>
                              <th scope="col">Code</th>
                              <th scope="col">QR-Code</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr><td colspan="3" class="text-center">PID HERE</td></tr>
                          </tbody>
                        </table>
                      </div>
                  </div>
                  <div class="form-group">
                    <button class="btn btn-link pull-right" style="display: none;" id="btnExportExcel"><i class="fa fa-file-excel-o"></i> Export to Excel</button>
                  </div>        
                  <div class="form-group">
                    <button type="button" class="btn btn-primary btn-lg btn-block" id="btnGenerate" tabindex="4">
                      Generate
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <div class="mt-5 text-muted text-center">
              <a href="user/logout">Go Back to Camlis</a>
            </div>        
          </div>
        </div>
      </div>
    </section>
  </div>

  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/app.min.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/scripts.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/custom.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/plugins/table2excel/jquery.table2excel.min.js'); ?>"></script>	
  <script type="text/javascript" src="<?php echo site_url('assets/godata/js/addtohomescreen.min.js'); ?>"></script>
  <script>
    var data = [];
    function saveData(val){
        data.push(val);
    }
    function isNumberKey(evt)
		{
			var charCode = (evt.which) ? evt.which : event.keyCode
			if (charCode > 31 && (charCode < 37 || charCode > 40) && (charCode < 48 || charCode > 57)){
				return false;
			}
			return true;
    }
    
    $("#btnGenerate").on('click', function (evt) {
      $(this).html('Please wait ...').attr('disabled', 'disabled'); // prevent multiple click
      $("#btnExportExcel").css("display","none");
        var province_id = $("#province").val();
        var number      = $("#number").val();
        if (province_id == ""){
          $("#message").css("display","block");
          setTimeout(function(){
            $("#btnGenerate").html('Generate').removeAttr('disabled'); // prevent multiple click
          },300)
          return false;
        }
        $("#message").css("display","none");
        if (number <= 0 || number > 500){
          $("#numbeMessage").css("display","block");
          setTimeout(function(){
            $("#btnGenerate").html('Generate').removeAttr('disabled'); // prevent multiple click
          },300)
          return false;
        }
        $("#numbeMessage").css("display","none");
        data = [];
        var base_url = '<?php echo base_url().$app_lang;?>';
        $.ajax({
            url: base_url + "/generate/pid",
            type: "POST",
            data: { province_id: province_id , number: number},
            dataType: 'json',
            success: function (resText) {
              var resultStr = "";
              var n = 1;
              for(var i in resText){
                saveData(resText[i].pid);
                var img     = ' <img src="<?php echo site_url()."assets/plugins/qrcode/img/"?>'+resText[i].qrcode+'" class="size64" >';
                resultStr += '<tr><td class="result center-hv">'+n+'</td><td class="result center-hv">'+resText[i].pid+'</td><td>'+img+'</td></tr>';
                n++;
              }
              setTimeout(function(){
                  $("#tblResult tbody").html(resultStr);
                  $("#btnExportExcel").css("display","block");
                  $("#btnGenerate").html('Generate').removeAttr('disabled'); // prevent multiple click
              }, 1000);
            },
            error: function(xhr, status, error) {
              var err = eval("(" + xhr.responseText + ")");
              console.log(err.Message);
              console.log(xhr.responseText);
            }
        });
    } );
    $("#btnExportExcel").on("click", function(evt) {
        evt.preventDefault();
        var currDate      = new Date();
        var province_id = $("#province").val();
        var number      = $("#number").val();
        $("#tblResult").table2excel({
          // exclude CSS class
          exclude:".noExl",
          name:"Worksheet Name",
          filename:"ListOfCode_"+province_id+"_"+number+"_"+currDate,//do not include extension
          fileext:".xlsx" // file extension
        });
        // save status to database which has bean saved 
      //  console.log(data);
        
        var base_url = '<?php echo base_url().$app_lang;?>';
        $.ajax({
            url: base_url + "/generate/is_downloaded",
            type: "POST",
            data: { data: data},
            dataType: 'json',
            success: function (resText) {
              console.log(resText);
            },
            error: function(xhr, status, error) {              
            }
        });
    });
  </script>
  </body>
</html>
