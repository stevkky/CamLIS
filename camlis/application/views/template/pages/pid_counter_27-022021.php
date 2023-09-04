<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">    

  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>PID COUNTER</title>
    
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/app.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/bundles/bootstrap-daterangepicker/daterangepicker.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/style.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/components.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/custom.css'); ?>">
  <link rel='shortcut icon' type='image/x-icon' href='<?php echo site_url('assets/godata/dashboard/img/gd_ico.ico'); ?>' />

	<title>PID COUNTER</title>
  <style>
  .pidzone {
    border: 2px dashed #6777ef;
    min-height: 60px;
    text-align: center;
}
.pid-result {
    font-size: 23px;
    color: #34395e;
    margin: 1.2em 0.6em;    
}
.float-left{
  float: left;
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
                <h4>PID COUNTER</h4>
              </div>
              <div class="card-body">
                <form method="POST">    
                  <div class="form-row">
                      <div class="form-group col-md-6">
                        <label>Start date</label>
                        <input type="text" class="form-control datepicker" id="startDate">
                      </div>
                      <div class="form-group col-md-6">
                        <label>End date</label>
                        <input type="text" class="form-control datepicker" id="endDate">
                      </div>
                  </div>
                  <div class="invalid-feedback" id="message">End date must be bigger or equal with start date</div>
                  <table class="table table-striped table-sm" id="tblResult">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Province</th>
                        <th scope="col">Number</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr><td colspan="3" align="center">No data</td></tr>
                    </tbody>
                  </table>
                  <div class="form-group text-center">
                    <button type="button" class="btn btn-lg btn-round btn-primary" id="btnSubmit">
                      Submit
                    </button>
                  </div>
                </form>                
              </div>
            </div>            
          </div>
        </div>


        <div class="row">
          <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
              <div class="card">                
                <div class="card-body">
                  <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="home-tab2" data-toggle="tab" href="#counter" role="tab"
                        aria-controls="home" aria-selected="true">PID COUNTER</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="profile-tab2" data-toggle="tab" href="#user" role="tab"
                        aria-controls="profile" aria-selected="false">USER</a>
                    </li>
                    
                  </ul>
                  <div class="tab-content tab-bordered" id="myTab3Content">
                    <div class="tab-pane fade show active" id="counter" role="tabpanel" aria-labelledby="home-tab2">
                    <form method="POST">    
                  <div class="form-row">
                      <div class="form-group col-md-6">
                        <label>Start date</label>
                        <input type="text" class="form-control datepicker" id="startDate">
                      </div>
                      <div class="form-group col-md-6">
                        <label>End date</label>
                        <input type="text" class="form-control datepicker" id="endDate">
                      </div>
                  </div>
                  <div class="invalid-feedback" id="message">End date must be bigger or equal with start date</div>
                  <table class="table table-striped table-sm" id="tblResult">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Province</th>
                        <th scope="col">Number</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr><td colspan="3" align="center">No data</td></tr>
                    </tbody>
                  </table>
                  <div class="form-group text-center">
                    <button type="button" class="btn btn-lg btn-round btn-primary" id="btnSubmit">
                      Submit
                    </button>
                  </div>
                </form> 
                    </div>
                    <div class="tab-pane fade" id="user" role="tabpanel" aria-labelledby="profile-tab2">
                      <div class="table-responsive">
                        <table class="table table-bordered table-md" id="tbl_user">
                          <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Location</th>
                            <th>Province</th>
                            <th>Approval</th>
                            <th>Date</th>
                            <th>Options</th>
                          </tr>
                          <tbody>
                            <?php
                              $n = 1;
                              foreach($users as $user){
                                echo '<tr>';
                                echo '<td>'.$n.'</td>';
                                echo '<td>'.$user["fullname"].'</td>';
                                echo '<td>'.$user["username"].'</td>';
                                echo '<td>'.$user["location"].'</td>';
                                echo '<td>'.$user["province_name"].'</td>';
                                if($user["approval"] == 0){
                                  echo '<td><div class="badge badge-warning">Pending</div></td>';
                                }else if($user["approval"] == 1){
                                  echo '<td><div class="badge badge-danger">Reject</div></td>';
                                }else if($user["approval"] == 1){
                                  echo '<td><div class="badge badge-danger">Active</div></td>';
                                }
                                echo '<td>'.$user["entryDate"].'</td>';
                                echo '<td><div class="buttons"><a href="#" class="btn btn-icon btn-sm btn-danger" onclick="approve('.$user['ID'].',1)"><i class="fas fa-times"></i></a>
                                <a href="#" class="btn btn-icon btn-sm btn-success" onclick="approve('.$user['ID'].',2)"><i class="fas fa-check"></i></a></div></td>';
                                echo '</tr>';
                                $n++;
                              }
                            ?>                            
                          </tbody>
                        </table>
                      </div>
                    </div>                    
                  </div>
                </div>
              </div>
            </div>
        </div>


      </div>
    </section>

  </div>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/app.min.js'); ?>"></script>
  <script src="<?php echo site_url('assets/godata/dashboard/bundles/bootstrap-daterangepicker/daterangepicker.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/scripts.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/custom.js'); ?>"></script>	
  <script>
    $(function () {
      var todayDate     = new Date().getDate();
      var endD          = new Date(new Date().setDate(todayDate - 15));
      var currDate      = new Date();
      $("#startDate").daterangepicker({
        locale: { format: "YYYY-MM-DD" },
        autoclose: true,
        singleDatePicker: true,
        minDate: moment("01/12/2021"),
        maxDate : currDate    
      });

      $("#endDate").daterangepicker({
        locale: { format: "YYYY-MM-DD" },
        autoclose: true,
        minDate:new Date(),
        singleDatePicker: true,
        minDate: moment("01/12/2021"),
        maxDate : currDate
      });
      // Daterangepicker
    })

    $("#btnSubmit").on('click', function (evt) {
      $(this).html('Please wait ...').attr('disabled', 'disabled'); // prevent multiple click
        
        var base_url = '<?php echo base_url().$app_lang;?>';        
        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        if(startDate > endDate){          
          $("#message").css("display","block");
          $("#btnSubmit").html('Submit').removeAttr('disabled'); // prevent multiple click
          return false;
        }
        $("#message").css("display","none");
        $.ajax({
            url: base_url + "/generate/count_pid",
            type: "POST",
            data: { startDate: startDate , endDate: endDate },
            dataType: 'json',
            success: function (resText) {
              var str = "No result found";
              var n = 1;
              var total = 0;
              for (var i in resText) {                
                str += '<tr><td>'+n+'</td><td>'+resText[i].province_name+'</td><td class="text-right">'+resText[i].number+'</td></tr>';
                total += resText[i].number;
                n++;
              }
              if(total > 0){
                str +='<tr><td colspan="2" class="text-right">Total</td><td class="text-right">'+total+'</td></tr>'
              }
              setTimeout(function(){
                $("#tblResult tbody").html(str);
                $("#btnSubmit").html('Submit').removeAttr('disabled'); // prevent multiple click
              }, 1000);
            },
            error: function(xhr, status, error) {
              var err = eval("(" + xhr.responseText + ")");
              console.log(err.Message);
              console.log(xhr.responseText);
            }
        });
    } );
   function approve(id , stat){
    console.log(id+" "+stat);
   }
  </script>
  </body>
</html>
