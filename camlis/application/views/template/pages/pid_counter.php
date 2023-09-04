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
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/bundles/izitoast/css/iziToast.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/bundles/prism/prism.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/bundles/datatables/datatables.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css'); ?>">  
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
.modal.fade {
  z-index: 1060 !important;
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
          <div class="col-12 col-sm-10 offset-sm-1 col-md-10 offset-md-1 col-lg-10 offset-lg-1 col-xl-10 offset-xl-1">
              <div class="card">                
                <div class="card-body">
                  <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="home-tab2" data-toggle="tab" href="#counter" role="tab"
                        aria-controls="home" aria-selected="false">PID COUNTER</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="profile-tab2" data-toggle="tab" href="#user" role="tab"
                        aria-controls="profile" aria-selected="true">USER</a>
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
                  <div class="invalid-feedback" id="message">End date must be greater or equal with start date</div>
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
                        <table class="table table-bordered table-md" id="tbl_user" name="tbl_user">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Name</th>
                              <th>Username</th>
                              <th>Location</th>
                              <th>Province</th>
                              <th>Approval</th>
                              <th>Options</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $n = 1;
                              $rejectBtnEnable  = '';
                              $approveBtnEnable = '';
                              $editBtnEnable    = '';
                              if(count($users) == 0){
                                echo '<tr><td colspan="7"> No data</td></tr>';
                              }else{
                                foreach($users as $user){
                                  echo '<tr data-row="'.$user['ID'].'">';
                                  echo '<td>'.$n.'</td>';
                                  echo '<td>'.$user["fullname"].'</td>';
                                  echo '<td>'.$user["username"].'</td>';
                                  echo '<td>'.$user["location"].'</td>';
                                  echo '<td>'.$user["province_name"].'</td>';
                                  if($user["approval"] == 0){
                                    echo '<td><div class="badge badge-warning">Pending</div></td>';
                                  }else if($user["approval"] == 1){
                                    echo '<td><div class="badge badge-danger">Reject</div></td>';
                                  }else if($user["approval"] == 2){
                                    echo '<td><div class="badge badge-danger">Active</div></td>';
                                  }
                                  if($user["approval"] == 0){
                                    // show both buttom
                                    $rejectBtnEnable = '<button type="button" class="btn btn-icon btn-sm btn-danger" data-id='.$user['ID'].' data-approve = "1" name="reject"><i class="fas fa-times"></i></button>';
                                    $approveBtnEnable = '<button type="button" class="btn btn-icon btn-sm btn-info" data-id='.$user['ID'].' data-approve = "2" name="approve"><i class="fas fa-check"></i></button>';

                                  }else if($user["approval"] == 1){
                                    // enable only active button
                                    $rejectBtnEnable = '';                                    
                                    $approveBtnEnable = '<button type="button" class="btn btn-icon btn-sm btn-info" data-id='.$user['ID'].' data-approve = "2" name="approve"><i class="fas fa-check"></i></button>';
                                  } else if($user["approval"] == 2){
                                    //enable only reject button
                                    $rejectBtnEnable = '<button type="button" class="btn btn-icon btn-sm btn-danger" data-id='.$user['ID'].' data-approve = "1" name="reject"><i class="fas fa-times"></i></button>';
                                    $approveBtnEnable = '';
                                    $editBtnEnable    = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" name="edit" data-id="'.$user['ID'].'"><i class="fas fa-edit"></i></button>';
                                  }
                                  echo '<td><div class="btn-group  btn-group-sm" role="group" aria-label="Basic example">
                                  '.$rejectBtnEnable.'  '.$approveBtnEnable.' '.$editBtnEnable.'
                                  </div></td>';
                                  echo '</tr>';
                                  $n++;
                                }
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

        <div class="text-muted text-center">
          <a href="<?php echo base_url('generate/logout')?>">Logut</a>
        </div>

        
      </div>
    </section>
  <!-- Editable Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="formModal"
          aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="formModal">User Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
              <form method="POST" action="<?php echo base_url();?>generatedev/doUpdate" id="theForm" class="needs-validation" novalidate="">
                  <div class="row">
                    <div class="form-group col-4">
                      <label for="frist_name">Full name *</label>
                      <input id="full_name" type="text" class="form-control" name="full_name" autofocus>
                      <div class="text-warning" id="full_name-message"></div>
                    </div>
                    <div class="form-group col-4">
                      <label for="username">Username *</label>
                      <input id="username" type="text" class="form-control" name="username" onkeypress="checkspace(event)">
                      <div class="text-warning" id="username-message"></div>
                    </div>
                    <div class="form-group col-4">
                      <label for="location">Location *</label>
                      <input id="location" type="text" class="form-control" name="location">
                      <div class="text-warning" id="location-message"></div>
                    </div>
                  </div>
                  <div class="row">
                  <div class="form-group col-4">
                      <label for="province">Province / City *</label>
                      <select class="form-control form-control-sm" id="province" name="province">
                        <option value="">ជ្រើសរើសខេត្តក្រុង</option>
                      <?php foreach($provinces as $pro){
                          echo "<option value='".$pro->code."'>".$pro->name_kh."</option>";
                      }?>
                        </select>
                      <div class="text-warning" id="province-message"></div>
                    </div>
                    <div class="form-group col-4">
                      <label for="email">Phone number </label>
                      <input id="phone" type="number" class="form-control" name="phone">
                      <div class="text-warning" id="phone-message"></div>
                    </div>
                    <div class="form-group col-4">
                      <label for="email">Email </label>
                      <input id="email" type="email" class="form-control" name="email" disabled>
                      <div class="text-warning" id="email-message"></div>
                    </div>                    
                  </div>
                  
                  <div class="row">
                    <div class="form-group col-6">
                      <label for="password" class="d-block">Password *</label>
                      <input id="password" type="password" class="form-control pwstrength" name="password">
                      <div class="text-warning" id="password-message"></div>
                    </div>                    
                  </div>                  
                  <div class="form-group">
                    <button type="button" class="btn btn-primary btn-lg btn-block" id="btnSave">
                      Save
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- End Editable Modal -->
  </div>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/app.min.js'); ?>"></script>
  <script src="<?php echo site_url('assets/godata/dashboard/bundles/izitoast/js/iziToast.min.js'); ?>"></script>
  <script src="<?php echo site_url('assets/godata/dashboard/bundles/bootstrap-daterangepicker/daterangepicker.js'); ?>"></script>
  <script src="<?php echo site_url('assets/godata/dashboard/bundles/datatables/datatables.min.js'); ?>"></script>
  <script src="<?php echo site_url('assets/godata/dashboard/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js'); ?>"></script> 
  <script src="<?php echo site_url('assets/godata/dashboard/bundles/jquery-ui/jquery-ui.min.js');?>"></script>
  <script src="<?php echo site_url('assets/godata/dashboard/bundles/prism/prism.js');?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/scripts.js'); ?>"></script>

  <script>
    var base_url = '<?php echo base_url().$app_lang;?>';
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
    });
    $("button[name=reject]").click(function(){
      var id    = $(this).attr("data-id");
      var appr  = $(this).attr("data-approve");
      $(this).addClass('disabled btn-progress'); //prevent multiple click
      console.log(id+" "+appr);
      $.ajax({
            url: base_url + "/generate/updateUser",
            type: "POST",
            data: { id: id , approve: appr },
            dataType: 'json',
            success: function (resText) {
              console.log(resText);
              if(resText.status){
                var htmlStr = '';
                var users = resText.data.users;
                var n = 1;
                $("table[name=tbl_user] tbody").html('<tr><td colspan="7" align="center">Loading...</td></tr>');
                if(users.length == 0){
                    htmlStr += '<tr><td colspan="7" align="center">No data</td></tr>';
                }else{
                  for (var k in users){
                    htmlStr += '<tr>';
                    htmlStr += '<td>'+n+'</td>';
                    htmlStr += '<td>'+users[k].fullname+'</td>';
                    htmlStr += '<td>'+users[k].username+'</td>';
                    htmlStr += '<td>'+users[k].location+'</td>';
                    htmlStr += '<td>'+users[k].province_name+'</td>';
                    if(users[k].approval == 0){
                      htmlStr += '<td><div class="badge badge-warning">Pending</div></td>';
                    }else if(users[k].approval == 1){
                      htmlStr += '<td><div class="badge badge-warning">Reject</div></td>';
                    }else if(users[k].approval == 2){
                      htmlStr += '<td><div class="badge badge-info">Active</div></td>';
                    }
                    htmlStr += '<td><div class="btn-group  btn-group-sm" role="group" aria-label="Basic example"><button type="button" class="btn btn-icon btn-sm btn-danger" data-id="'+users[k].ID+'" data-approve = "1" name="reject"><i class="fas fa-times"></i></button><button type="button" class="btn btn-icon btn-sm btn-info" data-id="'+users[k].ID+'" data-approve = "2" name="approve"><i class="fas fa-check"></i></button></div></td>';
                    htmlStr += '</tr>';
                    n++;
                  }
                }
                setTimeout(function(){
                  $("table[name=tbl_user] tbody").html(htmlStr);

                  iziToast.success({
                    title: 'Success',
                    message: resText.msg,
                    position: 'topCenter'
                  });
                  window.location.reload();
                }, 1000);
              }else{
                iziToast.warning({
                  title: 'Warning',
                  message: resText.msg,
                  position: 'topCenter'
                });
              }
            },
            error: function(xhr, status, error) {
              var err = eval("(" + xhr.responseText + ")");
              console.log(err.Message);
              console.log(xhr.responseText);
            }
        });
    });
    $("button[name=approve]").click(function(){
      var id    = $(this).attr("data-id");
      var appr  = $(this).attr("data-approve");
      $(this).addClass('disabled btn-progress'); //prevent multiple click
      
      $.ajax({
            url: base_url + "/generate/updateUser",
            type: "POST",
            data: { id: id , approve: appr },
            dataType: 'json',
            success: function (resText) {
              console.log(resText);
              if(resText.status){
                var htmlStr = '';
                var users = resText.data.users;
                var n = 1;
                $("table[name=tbl_user] tbody").html('<tr><td colspan="7" align="center">Loading...</td></tr>');
                if(users.length == 0){
                    htmlStr += '<tr><td colspan="7" align="center">No data</td></tr>';
                }else{
                  for (var k in users){
                    htmlStr += '<tr>';
                    htmlStr += '<td>'+n+'</td>';
                    htmlStr += '<td>'+users[k].fullname+'</td>';
                    htmlStr += '<td>'+users[k].username+'</td>';
                    htmlStr += '<td>'+users[k].location+'</td>';
                    htmlStr += '<td>'+users[k].province_name+'</td>';
                    if(users[k].approval == 0){
                      htmlStr += '<td><div class="badge badge-warning">Pending</div></td>';
                    }else if(users[k].approval == 1){
                      htmlStr += '<td><div class="badge badge-warning">Reject</div></td>';
                    }else if(users[k].approval == 2){
                      htmlStr += '<td><div class="badge badge-info">Active</div></td>';
                    }
                    htmlStr += '<td><div class="btn-group  btn-group-sm" role="group" aria-label="Basic example"><button type="button" class="btn btn-icon btn-sm btn-danger" data-id="'+users[k].ID+'" data-approve = "1" name="reject"><i class="fas fa-times"></i></button><button type="button" class="btn btn-icon btn-sm btn-info" data-id="'+users[k].ID+'" data-approve = "2" name="approve"><i class="fas fa-check"></i></button></div></td>';
                    htmlStr += '</tr>';
                    n++;
                  }
                }
                setTimeout(function(){
                  $("table[name=tbl_user] tbody").html(htmlStr);
                  iziToast.success({
                    title: 'Success',
                    message: resText.msg,
                    position: 'topCenter'
                  });
                  window.location.reload();
                }, 1000);
              }else{
                iziToast.warning({
                  title: 'Warning',
                  message: resText.msg,
                  position: 'topCenter'
                });
              }
            },
            error: function(xhr, status, error) {
              var err = eval("(" + xhr.responseText + ")");
              console.log(err.Message);
              console.log(xhr.responseText);
            }
        });
    });
    
    $("#tbl_user").dataTable({
      "columnDefs": [
        { "sortable": false, "targets": [2, 3] }
      ]
    });
    
    $('button[name=edit]').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget)
      var recipient = button.data('whatever') // Extract info from data-* attributes
      var id = button.data('id') // Extract info from data-* attributes
      console.log(id);
      var modal = $(this)
      modal.find('.modal-title').text('New message to ' + recipient)
      modal.find('.modal-body input').val(recipient)
    })


  </script>
  </body>
</html>
