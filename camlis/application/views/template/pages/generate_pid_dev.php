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
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/bundles/bootstrap-daterangepicker/daterangepicker.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/style.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/components.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/custom.css'); ?>">
  <link rel='shortcut icon' type='image/x-icon' href='<?php echo site_url('assets/godata/dashboard/img/gd_ico.ico'); ?>' />
  <link rel="stylesheet" href="https://printjs-4de6.kxcdn.com/print.min.css" type="text/css">
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
    .roll{
      border: 1px solid #ccc;
      width: 2.60in;
      height: 1.10in;
    }
    #paperRoll{     
      width: 50mm;
      height: 23mm;
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
                <div class="card">                  
                  <div class="card-body">
                    <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active" id="home-tab2" data-toggle="tab" href="#tab1" role="tab"
                          aria-controls="home" aria-selected="true">PID GENERATOR</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="profile-tab2" data-toggle="tab" href="#tab2" role="tab"
                          aria-controls="profile" aria-selected="false">SEARCH</a>
                      </li>                      
                    </ul>
                    <div class="tab-content tab-bordered" id="contentTab1">
                      <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="home-tab2">
                      <form method="POST" action="#" id="theForm" class="needs-validation" novalidate="">
                        <div class="form-row">                          
                          <div class="form-group col-7">
                            <label for="province">ខេត្ត ឬក្រុង</label>
                            <?php foreach($provinces as $pro){
                                if($pro->code == $province_id) {                                  
                                  echo '<input type="text" class="form-control" id="province"  value="'.$pro->name_kh.'" disabled />';
                                  echo '<input type="hidden" class="form-control" id="province_id"  value="'.$province_id.'" />';
                                  break;
                                }
                            }?>
                            
                            <div class="invalid-feedback" id="message">
                            សូមជ្រើសរើសខេត្ត ឬក្រុង</div>
                          </div>
                            <div class="form-group col-5">
                              <label>ចំនួនលេខកូដ <small><i>[1 -> 500]</i></small></label>
                              <input type="text" class="form-control" id="number" placeholder="ចំនួនចន្លោះ ០១ ទៅ ៥០០" value="1" maxlength="3" onKeydown="return isNumberKey(event)" >
                              <div class="invalid-feedback" id="numbeMessage">
                                ចំនួនត្រូវនៅចន្លោះពី ០១ ទៅ ៥០០
                              </div>
                            </div>
                        </div>
                        <div class="form-group col-12">
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="1" checked ​>
                            <label class="form-check-label" for="inlineRadio1">សំរាប់ថ្ងៃនេះ</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="2">
                            <label class="form-check-label" for="inlineRadio2">កំណត់ថ្ងៃ</label>
                          </div>
                          <div class="form-check form-check-inline">                              
                            <input type="text" class="form-control form-control-sm d-none" value="" id="generateDate" placeholder="ថ្ងៃសំរាប់លេខកូដ" readonly="readonly" />
                            <div class="invalid-feedback" id="generateDateMessage">
                                ថ្ងៃខែមិនត្រឹមត្រូវ...
                            </div>
                          </div>
                        </div>
                        <div class="form-group">
                            <div class="overflow-auto" style="max-height: 450px;">
                              <table class="table table-sm" id="tblResult">
                                <thead>
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">QR-Code</th>
                                    <th scope="col">Code</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr><td colspan="3" class="text-center">PID HERE</td></tr>
                                </tbody>
                              </table>
                            </div>
                        </div>
                        <div class="form-group"  style="display: none;" id="btnGroup" >
                          <button class="btn btn-link" id="btnExportExcel"><i class="fa fa-file-excel-o"></i> Export to Excel</button>
                          <button type="button" class="btn btn-link " id="btnPrint"><i class="fa fa-print"></i> Print</button>
                        </div>        
                        <div class="form-group">
                          <button type="button" class="btn btn-primary btn-lg btn-block" id="btnGenerate" tabindex="4">
                            Generate
                          </button>
                        </div>
                      </form>
                      </div>
                      <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="profile-tab2">
                        <form method="POST" action="<?php echo base_url().$app_lang;?>/generatedev/search" id="theForm" class="needs-validation" novalidate="">
                          
                          <div class="input-group">
                            <input id="pid_number" type="text" class="form-control" name="pid_number" placeholder="Type PID here....">
                            <div class="input-group-append">
                              <button class="btn btn-success" type="button" id="btnSearch">Search</button>
                            </div>
                          </div>
                          <div class="form-group">
                            <table class="table table-sm" id="tblSearchResult">                              
                              <tbody></tbody>
                            </table>
                          </div>
                        </form>
                      </div>                      
                    </div>
                  </div>
                </div>
              </div>

          <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">
            <div class="text-muted text-center">
              <a href="generatedev/logout">Go Back to Camlis</a>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Print Zone -->
    <section id="paperRoll" class="d-none" style="padding: 0px !important; margin: 0px !important;">
    <table border="0" id="tblCode_for_printing" cellpadding="0" cellspacing="0" style="padding: 0px; border-top: 1px solid #fff; border-left: 1px solid #fff;">
      <tbody>
      </tbody>
    </table>
    </section>
    <!-- End -->
    

  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/app.min.js'); ?>"></script>
  <script src="<?php echo site_url('assets/godata/dashboard/bundles/bootstrap-daterangepicker/daterangepicker.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/scripts.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/custom.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/plugins/table2excel/jquery.table2excel.min.js'); ?>"></script>	
  <script type="text/javascript" src='https://printjs-4de6.kxcdn.com/print.min.js'></script>
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
        var province_id     = $("#province_id").val();
        var number          = $("#number").val();
        var generate_date   = $("#generateDate").val();
        var radioValue      = $("input[name='inlineRadioOptions']:checked").val();
        console.log(generate_date);
        console.log(radioValue);
        if(radioValue == 2){
          if(radioValue == ""){
            $("#generateDateMessage").css("display","block");
            return false;
          }
        }
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
            url: base_url + "/generatedev/pid",
            type: "POST",
            data: { province_id: province_id , number: number , generate_date: generate_date},
            dataType: 'json',
            success: function (resText) {
              var resultStr = "";
              var n = 1;
              var htmlStringForPrinting = '';
              for(var i in resText){
                saveData(resText[i].pid);
                var img     = ' <img src="<?php echo site_url()."assets/plugins/qrcode/img_dev/"?>'+resText[i].qrcode+'" class="size64" >';
                resultStr += '<tr><td class="result center-hv">'+n+'</td><td class="result center-hv">'+resText[i].pid+'</td><td>'+img+'</td></tr>';

                // For printing out we need to print it two for each of pid
                htmlStringForPrinting += '<tr>';
                htmlStringForPrinting += '<td style="padding: 8px 5px 8px 5px; border-right: 1px solid #fff; border-bottom: 1px solid #fff;">'+img+'</td>';
                htmlStringForPrinting += '<td style="padding: 30px 0px 0px 0px; border-right: 1px solid #fff; border-bottom: 1px solid #fff;">'+resText[i].pid+'</td>';
                htmlStringForPrinting += '</tr>';

                htmlStringForPrinting += '<tr>';
                htmlStringForPrinting += '<td style="padding: 8px 5px 8px 5px; border-right: 1px solid #fff; border-bottom: 1px solid #fff;">'+img+'</td>';
                htmlStringForPrinting += '<td style="padding: 30px 0px 0px 0px; border-right: 1px solid #fff; border-bottom: 1px solid #fff;">'+resText[i].pid+'</td>';
                htmlStringForPrinting += '</tr>';
                
                n++;
              }
              setTimeout(function(){
                  $("#tblResult tbody").html(resultStr);
                  $("#btnGroup").css("display","block");                  
                  $("#tblCode_for_printing tbody").html(htmlStringForPrinting); // 11 March 2021
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
            url: base_url + "/generatedev/is_downloaded",
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

    // added: 25 Feb 2021
    $("#btnSearch").on('click', function (evt) {
      $(this).html('Please wait ...').attr('disabled', 'disabled'); // prevent multiple click
      var base_url = '<?php echo base_url().$app_lang;?>';
      var pid_value = $("#pid_number").val();
      if (pid_value == ""){
          $("#pid_message").css("display","block");
          setTimeout(function(){
            $("#btnSearch").html('Search').removeAttr('disabled'); // prevent multiple click
          },300)
          return false;
      }
      $.ajax({
          url: base_url + "/generatedev/search",
          type: "POST",
          data: { pid: pid_value},
          dataType: 'json',
          success: function (resText) {
            console.log(resText);
            var htmlStr = "";
            if(resText.status == true){
              var data = resText.data;
              htmlStr += '<tr>';
              htmlStr += '<td colspan="3" align="center"><img src="<?php echo base_url();?>'+data.qrcode+'"> '+data.result[0].pid+'</td>';
              htmlStr += '</tr>';
              htmlStr += '<tr>';
              htmlStr += '<td>ឈ្មោះ</td>';
              htmlStr += '<td>:</td>';
              htmlStr += '<td>'+data.user.fullname+'</td>';
              htmlStr += '</tr>'
              htmlStr += '<tr>';
              htmlStr += '<td>ខេត្ត ក្រុង</td>';
              htmlStr += '<td>:</td>';
              htmlStr += '<td>'+data.user.fullname+'</td>';
              htmlStr += '</tr>'

              htmlStr += '<td>'+data.user+'</td>';
              htmlStr += '<td>'+data.user+'</td>';
              htmlStr += '</tr>';
              
            }else{
              tmlStr += '<tr>';
              htmlStr += '<td colspan="3" align="center">No PID found...</td>';
              htmlStr += '</tr>';
            }
            setTimeout(function(){
                $("#tblSearchResult tbody").html(htmlStr);
                $("#btnSearch").html('Search').removeAttr('disabled'); // prevent multiple click
              }, 1000);
          },
          error: function(xhr, status, error) {
            var err = eval("(" + xhr.responseText + ")");
            console.log(err.Message);
            console.log(xhr.responseText);
          }
      });
    })
   
    // added: 05-03-2021
    $(function () {
      
      var todayDate     = new Date().getDate();
      var endD          = new Date(new Date().setDate(todayDate - 15));
      var currDate      = new Date();
      $("#generateDate").daterangepicker({
        locale: { format: "YYYY-MM-DD" },
        autoclose: true,
        singleDatePicker: true,
        minDate: currDate        
      });
      $('input[name="inlineRadioOptions"]').click(function(){
        var inputValue = $(this).attr("value");        
        if(inputValue == 2){
          $("#generateDate").removeClass('d-none');
        }else{
          $("#generateDate").addClass('d-none');
          $("#generateDate").val('');
        }
    });
    })
    // 11 March 2021
    $("#btnPrint").on('click', function (evt) {
      $(this).attr('disabled', 'disabled'); // prevent multiple click
      const style = '@page { margin-top: 0px } @media print { body{ margin: 0px;} h1 { color: blue } img { height: 64px; width:64px; margin: 0px;} td{font-size: 18px;} }'

      printJS({
        printable: 'tblCode_for_printing',
        type: 'html',
        style: style,
      })      
    //  $(this).html('Print').removeAttr('disabled'); // prevent multiple click
      return false;
    })
    
  </script>
  </body>
</html>
