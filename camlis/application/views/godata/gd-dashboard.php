<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>GODATA - DASHBOARD</title>
  <!-- General CSS Files -->
  
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/app.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/bundles/izitoast/css/iziToast.min.css'); ?>">
  
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/bundles/bootstrap-daterangepicker/daterangepicker.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/style.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/components.css'); ?>">
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="<?php echo site_url('assets/godata/dashboard/css/custom.css'); ?>">
  <link rel='shortcut icon' type='image/x-icon' href='<?php echo site_url('assets/godata/dashboard/img/gd_ico.ico'); ?>' />
  <style>
  .none-cursor {cursor: none;}
  </style>
</head>
<?php
$CLASSIFICATION_KEY = unserialize (CLASSIFICATION_KEY);
  $CLASSVALUE = array(
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_AIRPLANE_PASSENGER'    =>'Airplane',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_LAND_CROSSING'         =>'Land crossing',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_SUSPECT'               =>'Suspect',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_ILI_SARI'              =>'ILI/SARI',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_FOLLOWUP'              =>'Followup',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_CONTACT'               =>'Contact',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_CERTIFICATE'           =>'Cerficate',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_PENUMONIA'             =>'Pneumonia',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_HOTSPOT_SURVEILLANCE'  =>'Hotspot',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_HEALTH_CARE_WORKER'    =>'Health Care Worker',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED'  =>'Case Discarded',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_SCHOOL'                =>'School',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_FACTORY_WORKERS'       =>'Factory Workers',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_CONFIRMED'             =>'Confirmed',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_EXPORTED_CASES'        =>'Exported Cases',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_PROBABLE'              =>'Probable',
    'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_GENERAL_SCREENING'     =>'General Screening'
  );
  $LAB_NAME = array(
    '0' => '--- Select Lab name ---',
    'LNG_REFERENCE_DATA_CATEGORY_LAB_NAME_IPC'                      => 'IPC',
    'LNG_REFERENCE_DATA_CATEGORY_LAB_NAME_NIPH'                     => 'NIPH',
    'LNG_REFERENCE_DATA_CATEGORY_LAB_NAME_NAMRU_2'                  => 'NAMRU 2',
    'LNG_REFERENCE_DATA_CATEGORY_LAB_NAME_SPECIAL_HOSPITAL'         => 'Military Region Special Hospital',
    'LNG_REFERENCE_DATA_CATEGORY_LAB_NAME_PREAH_KETMEALEA_HOSPITAL' => 'Preah Ketmealea Hospital',
    'LNG_REFERENCE_DATA_CATEGORY_LAB_NAME_R_4'                      => 'R4',
    'LNG_REFERENCE_DATA_CATEGORY_LAB_NAME_R_5'                      => 'R5',
    'LNG_REFERENCE_DATA_CATEGORY_LAB_NAME_REAM'                     => 'Ream',
    'LNG_REFERENCE_DATA_CATEGORY_LAB_NAME_SIEM_REAP'                => 'Siem Reap',
    'LNG_REFERENCE_DATA_CATEGORY_LAB_NAME_UHS'                      => 'UHS',
  );
  $CLASSIFICATION_DISPLAY_FOR_COVID19 = array('LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_CONFIRMED','LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_EXPORTED_CASES','LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_PROBABLE');
  function timeago($date) {
    $timestamp = strtotime($date);	
    
    $strTime = array("second", "minute", "hour", "day", "month", "year");
    $length = array("60","60","24","30","12","10");

    $currentTime = time();
    if($currentTime >= $timestamp) {
     $diff     = time()- $timestamp;
     for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
     $diff = $diff / $length[$i];
     }

     $diff = round($diff);
     return $diff . " " . $strTime[$i] . "(s) ago ";
    }
 }
?>
<body>
  <div class="loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar sticky">
        <div class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg
									collapse-btn"> <i data-feather="align-justify"></i></a></li>
            <li><a href="#" class="nav-link nav-link-lg fullscreen-btn">
                <i data-feather="maximize"></i>
              </a></li>
          </ul>
        </div>
        <ul class="navbar-nav navbar-right">          
          <li class="dropdown"><a href="#" data-toggle="dropdown"
              class="nav-link dropdown-toggle nav-link-lg nav-link-user"> <img alt="image" src="<?php echo site_url('assets/godata/dashboard/img/user.png'); ?>"
                class="user-img-radious-style"> <span class="d-sm-none d-lg-inline-block"></span></a>

            <div class="dropdown-menu dropdown-menu-right pullDown">
              <div class="dropdown-title">Welcome To GoData</div>              
              <div class="dropdown-divider"></div>
              <a href="signout" class="dropdown-item has-icon text-danger"> <i class="fas fa-sign-out-alt"></i>
                Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="dashboard"> <img alt="image" src="<?php echo site_url('assets/godata/dashboard/img/gd-logo.png'); ?>" class="header-logo" /> <span
                class="logo-name">GODATA</span>
            </a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header">Main</li>
            <li class="dropdown active">
              <a href="<?php echo $this->app_language->site_url('godata/dashboard'); ?>" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a>
            </li>                        
          </ul>
        </aside>
      </div>
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-body">          
            <div class="row">
              <!-- Table Covid 19 Outbreak -->
              <div class="col-12 col-9 col-lg-9" >
                  <div class="card">
                    <div class="card-header">
                      <h4>
                      
                      <form class="form-inline">
                        <div class="input-group" >
                          <input type="text" placeholder="From Date" class="form-control datepicker" name="startDate">
                        </div>
                        <div class="input-group">
                          <input type="text" placeholder="To Date" class="form-control datepicker" name="endDate">
                        </div>
                        <button type="btn" class="btn btn-primary" name="covid19BtnFilter">Filter</button> &nbsp;
                      </form>

                      </h4>
                      <div class="card-header-action">
                        <button type="button" class="btn btn-icon btn-primary" id="btnClassificationCovid19"><i class="fas fa-download"></i></button>                        
                      </div>
                    </div>
                    <div class="card-body">

                    <?php if(!empty($classification[1])){ ?>
                      <div class="section-title mt-0">Update: <span id="updateTimeCovid19"> <?php echo timeago($classification[1][0]->lastUpdate); ?></span></div>
                    <?php } ?>
                      <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center" name="tblResult_COVID_19">
                          <thead>
                            <tr>
                              <td width="30" rowspan="2"> #</td>
                              <td width="151" rowspan="2">Classification</td>
                              <td width="89" rowspan="2">Male</td>
                              <td width="89" rowspan="2">Female</td>
                              <td width="177" colspan="2">Discharged</td>
                              <td width="88" rowspan="2">Total</td>
                            </tr>
                            <tr>
                              <td width="89">Male</td>
                              <td width="89">Female</td>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <?php $covid19 = array(); ?>
                              <?php if(empty($classification[1])){ ?>
                                <td colspan="7">No data found</td>
                              <?php } else { 
                                // covid19
                                $covid19 = $classification[1];
                                $n = 1;
                                foreach($covid19 as $item){
                                  if (in_array($item->classKey, $CLASSIFICATION_DISPLAY_FOR_COVID19)){
                                  echo "<tr>";
                                  echo "<td>".$n."</td>";
                                  echo "<td>".$CLASSVALUE[$item->classKey]."</td>";
                                  echo "<td>".number_format($item->maleCount)."</td>";
                                  echo "<td>".number_format($item->femaleCount)."</td>";
                                  echo "<td>".number_format($item->dischargedMaleCount)."</td>";
                                  echo "<td>".number_format($item->dischargedFemaleCount)."</td>";
                                  echo "<td>".number_format($item->total)."</td>";                                  
                                  echo "<tr>";      
                                  $n++;
                                  }
                                }
                              ?> 
                              <?php }?>
                            </tr>
                            </tbody>
                        </table>
                      </div>
                    </div>               
                  </div>
              </div>
              <!-- End Table covid outbreak-->

              <div class="col-12 col-3 col-lg-3">                                
                <!-- Color-->                    
                    <div class="row">
                    <?php 
                      $LIST_COLOR = array(
                        'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_CONFIRMED'       => 'danger',
                        'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_EXPORTED_CASES'  => 'secondary',
                        'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_PROBABLE'        => 'primary'
                      );
                      foreach($covid19 as $item){
                        if (in_array($item->classKey, $CLASSIFICATION_DISPLAY_FOR_COVID19)){
                      ?>
                        <div class="col-12 col-md-12 col-lg-12">
                          <div class="card card-<?php echo $LIST_COLOR[$item->classKey];?>" style="margin-bottom:10px;">
                            <div class="card-header" style="padding: 4px;">
                              <h4><span class="badge badge-<?php echo $LIST_COLOR[$item->classKey];?>" id="covid19_<?php echo $CLASSVALUE[$item->classKey]?>_count"><?php echo number_format($item->total)?></span><span id="covid19_<?php echo $CLASSVALUE[$item->classKey]?>"></span> <?php echo $CLASSVALUE[$item->classKey]?></h4>
                            </div>
                          </div>
                        </div>
                      <?php
                        }
                      }
                      ?>                                                                  
              </div>
            <!-- End Color by clasification-->
              </div>
            </div>
            <div class="row">
              <div class="col-12 col-md-6 col-lg-6">
                  <div class="card">
                    <div class="card-header">
                      <h4>Cases by geographic location</h4>
                      <div class="card-header-action">
                        <button type="button" class="btn btn-icon btn-primary" id="btnGetLocationCovid19"><i class="fas fa-download"></i></button>
                      </div>
                    </div>
                    <div class="card-body">
                      <?php 
                            if(!empty($location[1])){ ?>
                              <div class="section-title mt-0">Update: <span id="tblCovid19_location_update"> <?php echo timeago($location[1][0]->lastUpdate); ?></span></div>
                      <?php } ?>
                      <table class="table table-sm" name="tblCovid19_location">
                        <thead>
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">Province</th>
                            <th scope="col">M</th>
                            <th scope="col">F</th>
                            <th scope="col">Total</th>
                          </tr>
                        </thead>
                        <tbody class="geolocation">
                          <?php 
                            if(empty($location[1])){
                              echo "<tr><td colspan='5' class='text-center'> No data</td></tr>";
                            }else{
                              $province = $location[1];
                              $n = 1;
                              foreach($province as $pro){
                                echo "<tr>";
                                echo "<th>".$n."</th>";
                                echo "<td>".$pro->name."</td>";
                                echo "<td>".number_format($pro->maleCount)."</td>";
                                echo "<td>".number_format($pro->femaleCount)."</td>";
                                echo "<td>".number_format($pro->casesCount)."</td>";
                                echo "</tr>";
                                $n++;
                              }
                            }
                          ?>
                        </tbody>
                      </table>                    
                    </div>
                  </div>
             
              </div>

              <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                  <div class="card-header">
                    <h4>Nationality</h4>       
                    <div class="card-header-action">                        
                        <button type="button" class="btn btn-icon btn-primary" id="btnGetNationalityCovid19"><i class="fas fa-download"></i></button>
                    </div>
                  </div>
                  <div class="card-body">
                      <?php 
                            if(!empty($nationality[1])){ ?>
                            <div class="section-title mt-0">Update: <span id="tblCovid19_nationality_update"> <?php echo timeago($nationality[1][0]->lastUpdate); ?></span></div>                              
                      <?php } ?>                    
                    <table class="table table-sm table-bordered" name="tblCovid19_nationality">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Nationality</th>
                          <th scope="col">M</th>
                          <th scope="col">F</th>
                          <th scope="col">Total</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          if(empty($nationality[1])){
                            echo "<tr><td colspan='5' class='text-center'> No data</td></tr>";
                          }else{
                            $nat = $nationality[1];
                            $n = 1;
                            foreach($nat as $item){
                              $total = $item->maleCount + $item->femaleCount;
                              echo "<tr>";
                              echo "<th>".$n."</th>";
                              echo "<td>".$item->name."</td>";
                              echo "<td>".number_format($item->maleCount)."</td>";
                              echo "<td>".number_format($item->femaleCount)."</td>";
                              echo "<td>".$total."</td>";
                              echo "</tr>";
                              $n++;
                            }
                          }
                        ?>                      
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="card mt-4">
                  <div class="card-header">
                    <h4>Contact High Risk</h4>
                    <div class="card-header-action">
                        <button type="button" class="btn btn-icon btn-primary" id="btnGetHighRiskCovid19"><i class="fas fa-download"></i></button>
                      </div>
                  </div>
                  <div class="card-body">
                    <?php 
                          if(!empty($highRiskContact[1])){ ?>
                            <div class="section-title mt-0">Update: <span id="updateTimehighrisk"> <?php echo timeago($highRiskContact[1][0]->lastUpdate); ?></span></div>
                    <?php } ?>                    
                    <table class="table table-bordered table-sm text-center" name="tblCovid19_highrisk">
                      <thead>
                        <tr>
                          <th scope="col" colspan="2">High Risk Level</th>
                          <th scope="col" colspan="2">Under Follow Up</th>
                        </tr>
                        <tr>
                          <th>F</th>
                          <th>M</th>
                          <th>F</th>
                          <th>M</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          if(empty($highRiskContact[1])){
                            echo "<tr><td colspan='4' class='text-center'> No data</td></tr>";
                          }else{
                            $nat = $highRiskContact[1];       
                            $total = 0;
                            $totalActive = 0;                     
                            foreach($nat as $item){
                              echo "<tr>";
                              echo "<td>".number_format($item->maleCount)."</td>";
                              echo "<td>".number_format($item->femaleCount)."</td>";
                              echo "<td>".number_format($item->activeMaleCount)."</td>";
                              echo "<td>".number_format($item->activeFemaleCount)."</td>";
                              echo "</tr>";

                              $total += $item->maleCount;
                              $total += $item->femaleCount;
                              $totalActive += $item->activeMaleCount;
                              $totalActive += $item->activeFemaleCount;
                            }
                          }
                        ?> 
                      </tbody>
                      <?php 
                          if(!empty($highRiskContact[1])){
                      ?>
                      <tfoot>
                        <tr>
                          <td colspan="2"><?php echo number_format($total);?> </td>
                          <td colspan="2"><?php echo number_format($totalActive);?></td>
                        </tr>
                      </tfoot>
                      <?php
                          }
                      ?>  
                    </table>                    
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12 col-md-8 col-lg-8">
                <div class="card">
                  <div class="card-header">                                      
                      <h4>
                      <form class="card-header-form">
                      <div class="form-row">
                        <div class="col">
                          <input type="text" placeholder="From Date" class="form-control datepicker" name="startDate">
                        </div>
                        <div class="col">
                          <input type="text" class="form-control datepicker" placeholder="To Date" name="endDate">
                        </div>
                        <div class="col">
                          <button type="button" class="btn btn-primary btn-sm btnFilter" name="covid19NatBtnFilter">Filter</button>
                        </div>
                      </div>
                      </form>
                      </h4>
                      <div class="card-header-action">
                        <button type="button" class="btn btn-icon btn-primary" id="btnClassificationCovid19Nat"><i class="fas fa-download"></i></button>
                      </div>
                    
                  </div>
                  <div class="card-body">
                  <?php $outbreak19National = $classification[2];?>
                         
                  <div class="section-title mt-0">
                  <table class="table">
                    <tr>
                      <td>
                      <?php if(!empty($outbreak19National[0])){ ?>
                          Update: <span id="updateTimeCovid19Nat"> <?php echo timeago($outbreak19National[0]->lastUpdate); ?></span>
                      <?php } ?>
                      </td>
                      <td>
                        <div class="pull-right">
                        <?php echo form_dropdown('lab_name', $LAB_NAME, '','class="form-control-sm"');?>                        
                          <a href="#" class="btn disabled btn-primary btn-sm btn-progress d-none" id="loading">Progress</a>
                        </div>
                      </td>
                    </tr>
                  </table>

                  </div>
                   
                    <table class="table table-sm table-bordered" name="tblResult_COVID_19_nat">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Classification</th>
                          <th scope="col">Male</th>
                          <th scope="col">Female</th>
                          <th scope="col" class="text-right">Total</th>                          
                        </tr>
                      </thead>
                      <tbody>
                            <tr>
                              <?php if(empty($outbreak19National[2])){ ?>
                                <td colspan="5">No data</td>
                              <?php } else {                                 
                                $n = 1;
                                foreach($outbreak19National as $item){
                                  echo "<tr>";
                                  echo "<th>".$n."</th>";
                                  $v = empty($CLASSVALUE[$item->classKey]) ? $item->classKey : $CLASSVALUE[$item->classKey];
                                  echo "<td>".$v."</td>";
                                  echo "<td class='text-right'>".number_format($item->maleCount)."</td>";
                                  echo "<td class='text-right'>".number_format($item->femaleCount)."</td>";                                  
                                  echo "<td class='text-right'>".number_format($item->total)."</td>";                                  
                                  echo "<tr>";      
                                  $n++;                           
                                }
                              ?> 
                              <?php }?>
                            </tr>
                            </tbody>                        
                    </table>                    
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4 col-lg-4">
                <div class="card">
                  <div class="card-body" style="padding: 0 5px;">
                  <?php 
                    $LIST_COLOR_OUTBREAK_NATIONAL = array(
                      'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_AIRPLANE_PASSENGER'    =>'primary',
                      'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_SUSPECT'               =>'danger',
                      'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_ILI_SARI'              =>'dark',
                      'LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_FACTORY_WORKERS'       =>'info',
                    );
                  ?>
                    <div class="buttons" id="covid19Nat_class">
                      <?php 
                        
                         $NationalClass = $classification[2];                         
                         foreach($NationalClass as $item){

                            if (array_key_exists($item->classKey, $LIST_COLOR_OUTBREAK_NATIONAL)){
                              $color = $LIST_COLOR_OUTBREAK_NATIONAL[$item->classKey];
                            }else{
                              $color = "light";
                            }
                            $v = empty($CLASSVALUE[$item->classKey]) ? $item->classKey : $CLASSVALUE[$item->classKey];
                        ?>
                            <button type="button" class="btn btn-sm none-cursor">
                            <span class="badge badge-<?php echo $color;?>"> <?php echo number_format($item->total); ?> </span> <?php echo $v; ?> 
                          </button>
                        <?php
                         }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12 col-md-6 col-lg-6">
                    <div class="card">
                      <div class="card-header">
                        <h4>Cases by geographic location</h4>
                        <div class="card-header-action">
                          <button type="button" class="btn btn-icon btn-primary" id="btnGetLocationCovid19Nat"><i class="fas fa-download"></i></button>
                        </div>
                      </div>
                      <div class="card-body">
                        <?php 
                              if(!empty($location[2])){ ?>
                                <div class="section-title mt-0">Update: <span id="tblCovid19Nat_location_update"> <?php echo timeago($location[2][0]->lastUpdate); ?></span></div>                              
                        <?php } ?>
                        <table class="table table-sm" name="tblCovid19Nat_location">
                          <thead>
                            <tr>
                              <th scope="col">#</th>
                              <th scope="col">Province</th>
                              <th scope="col">M</th>
                              <th scope="col">F</th>
                              <th scope="col">Total</th>
                            </tr>
                          </thead>
                          <tbody class="geolocation">
                            <?php 
                              if(empty($location[2])){
                                echo "<tr><td colspan='5' class='text-center'> No data</td></tr>";
                              }else{
                                $province = $location[2];
                                $n = 1;
                                foreach($province as $pro){
                                  echo "<tr>";
                                  echo "<th>".$n."</th>";
                                  echo "<td>".$pro->name."</td>";
                                  echo "<td>".number_format($pro->maleCount)."</td>";
                                  echo "<td>".number_format($pro->femaleCount)."</td>";
                                  echo "<td>".number_format($pro->casesCount)."</td>";
                                  echo "</tr>";
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
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Number of people tested by provinces</h4>
                    <div class="card-header-action">
                          <button type="button" class="btn btn-icon btn-primary" id="btnGetLocationAndClassCovid19Nat"><i class="fas fa-download"></i></button>
                        </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-sm table-bordered text-center" id="tbl-tested-by-province">
                        <thead>
                          <tr>
                            <th width="61" rowspan="3">Province</th>
                            <th width="803" colspan="26">Classification</th>
                            </tr>
                            <tr>
                              <th width="61" colspan="2">Airplane</th>
                              <th width="59" colspan="2">Land crossing</th>
                              <th width="57" colspan="2">Suspect</th>
                              <th width="57" colspan="2">ILI/SARI</th>
                              <th width="65" colspan="2">Followup</th>
                              <th width="57" colspan="2">Contact</th>
                              <th width="64" colspan="2">Cerficate</th>
                              <th width="77" colspan="2">Pneumonia</th>
                              <th width="59" colspan="2">Hotspot</th>
                              <th width="53" colspan="2">Health Care Worker</th>
                              <th width="48" colspan="2">School</th>
                              <th width="82" colspan="2">Screening</th>
                              <th width="63" colspan="2">General Screening</th>
                            </tr>
                            <tr>
                            <th width="34">M</th>
                            <th width="27">F</th>
                            <th width="33">M</th>
                            <th width="27">F</th>
                            <th width="32">M</th>
                            <th width="26">F</th>
                            <th width="32">M</th>
                            <th width="26">F</th>
                            <th width="36">M</th>
                            <th width="29">F</th>
                            <th width="32">M</th>
                            <th width="26">F</th>
                            <th width="35">M</th>
                            <th width="29">F</th>
                            <th width="42">M</th>
                            <th width="34">F</th>
                            <th width="32">M</th>
                            <th width="26">F</th>
                            <th width="29">M</th>
                            <th width="23">F</th>
                            <th width="27">M</th>
                            <th width="21">F</th>
                            <th width="62">M</th>
                            <th width="20">F</th>
                            <th width="35">M</th>
                            <th width="28">F</th>
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td colspan="27"> No data</td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <input type="hidden" value="<?php echo $listOutbreak[0]->outbreakID; ?>" id="outbreak_id">
        <input type="hidden" value="<?php echo $listOutbreak[0]->id; ?>" id="ob_id_covid19">
        <input type="hidden" value="<?php echo $listOutbreak[1]->outbreakID; ?>" id="outbreak_id_national">
        <input type="hidden" value="<?php echo $listOutbreak[1]->id; ?>" id="ob_id_covid19_national">
        <input type="hidden" value="<?php echo base_url(); ?>" id="base_url">
      </div>
      <!-- End Main Content-->
      <footer class="main-footer">
        <div class="footer-left">
          <a href="camlis.net">CamLis</a></a>
        </div>
        <div class="footer-right">
        </div>
      </footer>
    </div>
  </div>

  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/app.min.js'); ?>"></script>
  <script src="<?php echo site_url('assets/godata/dashboard/bundles/izitoast/js/iziToast.min.js'); ?>"></script>
  <script src="<?php echo site_url('assets/godata/dashboard/bundles/bootstrap-daterangepicker/daterangepicker.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/scripts.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/godata/dashboard/js/custom.js'); ?>"></script>

</body>
</html>