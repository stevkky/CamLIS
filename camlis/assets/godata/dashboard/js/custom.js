/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 * 
 */

"use strict";
/** added 11 Feb 2021 */
function time_ago(time) {

  switch (typeof time) {
    case 'number':
      break;
    case 'string':
      time = +new Date(time);
      break;
    case 'object':
      if (time.constructor === Date) time = time.getTime();
      break;
    default:
      time = +new Date();
  }
  var time_formats = [
    [60, 'seconds', 1], // 60
    [120, '1 minute ago', '1 minute from now'], // 60*2
    [3600, 'minutes', 60], // 60*60, 60
    [7200, '1 hour ago', '1 hour from now'], // 60*60*2
    [86400, 'hours', 3600], // 60*60*24, 60*60
    [172800, 'Yesterday', 'Tomorrow'], // 60*60*24*2
    [604800, 'days', 86400], // 60*60*24*7, 60*60*24
    [1209600, 'Last week', 'Next week'], // 60*60*24*7*4*2
    [2419200, 'weeks', 604800], // 60*60*24*7*4, 60*60*24*7
    [4838400, 'Last month', 'Next month'], // 60*60*24*7*4*2
    [29030400, 'months', 2419200], // 60*60*24*7*4*12, 60*60*24*7*4
    [58060800, 'Last year', 'Next year'], // 60*60*24*7*4*12*2
    [2903040000, 'years', 29030400], // 60*60*24*7*4*12*100, 60*60*24*7*4*12
    [5806080000, 'Last century', 'Next century'], // 60*60*24*7*4*12*100*2
    [58060800000, 'centuries', 2903040000] // 60*60*24*7*4*12*100*20, 60*60*24*7*4*12*100
  ];
  var seconds = (+new Date() - time) / 1000,
    token = 'ago',
    list_choice = 1;

  if (seconds == 0) {
    return 'Just now'
  }
  if (seconds < 0) {
    seconds = Math.abs(seconds);
    token = 'from now';
    list_choice = 2;
  }
  var i = 0,
    format;
  while (format = time_formats[i++])
    if (seconds < format[0]) {
      if (typeof format[2] == 'string')
        return format[list_choice];
      else
        return Math.floor(seconds / format[2]) + ' ' + format[1] + ' ' + token;
    }
  return time;
}
/**
 * Format number in JS
 */

function format_number(nStr){
  nStr += '';
  var x = nStr.split('.');
  var x1 = x[0];
  var x2 = x.length > 1 ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {
   x1 = x1.replace(rgx, '$1' + ',' + '$2');
  }
  return x1 + x2;
 }
$(function () {
    
    var todayDate     = new Date().getDate();      
    var currDate      = new Date();
    $("input[name='startDate']").daterangepicker({
      locale: { format: "YYYY-MM-DD" },
      autoclose: true,
      singleDatePicker: true,
      maxDate : currDate    
    });

    $("input[name='endDate']").daterangepicker({
      locale: { format: "YYYY-MM-DD" },
      autoclose: true,
      singleDatePicker: true,
      maxDate : currDate
    });
    // Daterangepicker
    $('input[name="endDate"]').val('');
    $('input[name="endDate"]').attr("placeholder","To Date");
    $("input[name='startDate']").val('');
    $("input[name='startDate']").attr("placeholder","From Date");
  })
    
  var word = {
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_AIRPLANE_PASSENGER    : 'Airplane',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_LAND_CROSSING         : 'Land crossing',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_SUSPECT               : 'Suspect',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_ILI_SARI              : 'ILI/SARI',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_FOLLOWUP              : 'Followup',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_CONTACT               : 'Contact',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_CERTIFICATE           : 'Cerficate',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_PENUMONIA             : 'Pneumonia',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_HOTSPOT_SURVEILLANCE  : 'Hotspot',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_HEALTH_CARE_WORKER    : 'Health Care Worker',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_NOT_A_CASE_DISCARDED  : 'Case Discarded',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_SCHOOL                : 'School',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_FACTORY_WORKERS       : 'Factory Workers',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_CONFIRMED             : 'Confirmed',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_EXPORTED_CASES        : 'Exported Cases',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_PROBABLE              : 'Probable',
      LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_GENERAL_SCREENING     : 'General Screening'
  };
  var CLASSIFICATION_DISPLAY_FOR_COVID19 = {
    LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_CONFIRMED : 'Confirmed',
    LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_EXPORTED_CASES : 'Exported Cases',
    LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_PROBABLE : 'Probable'
  };
  var LIST_COLOR_OUTBREAK_NATIONAL = {
    LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_AIRPLANE_PASSENGER    : 'primary',
    LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_SUSPECT               : 'danger',
    LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_ILI_SARI              : 'dark',
    LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_FACTORY_WORKERS       : 'info',
  };
  var outbreakID         = $("#outbreak_id").val();
  var obId_covid19       = $("#ob_id_covid19").val();
  var obId_covid19_nat   = $("#ob_id_covid19_national").val();
  var outbreakIDNational = $("#outbreak_id_national").val();
  var base_url           = $("#base_url").val();

  //Get classification of outbreak Covid19
  $("#btnClassificationCovid19").click(function(e){
    $(this).addClass('disabled btn-progress'); //prevent multiple click    
    $.ajax({
        url: base_url + "/godata/getClassification",
        type: "POST",
        data: { outbreak_id: outbreakID , ob_id: obId_covid19},
        dataType: 'json',
        success: function (resText) {
          // update data 
          if(resText.status == true){
            if(resText.data){
              var data = resText.data;
              var htmlStr = '';
              var lastUpdate = '';
              var label ='';
              // empty data zone
              $("table[name=tblResult_COVID_19] tbody").html('tr><td colspan="7">Loading...</td></tr>');
              $("#updateTimeCovid19").html("Loading...");
              var number = 1;
              for (var k in data){
                if(CLASSIFICATION_DISPLAY_FOR_COVID19[data[k].classKey] !== undefined){
                  label = word[data[k].classKey];
                  htmlStr += '<tr>';
                  htmlStr += "<td>"+number+"</td>";
                  htmlStr += "<td>"+label+"</td>";
                  htmlStr += "<td>"+data[k].maleCount+"</td>";
                  htmlStr += "<td>"+data[k].femaleCount+"</td>";
                  htmlStr += "<td>"+data[k].dischargedMaleCount+"</td>";
                  htmlStr += "<td>"+data[k].dischargedFemaleCount+"</td>";
                  htmlStr += "<td>"+data[k].total+"</td>";
                  lastUpdate = data[k].lastUpdate;
                  htmlStr += '</tr>';
                  // update right side
                  $("#covid19_"+data[k].classKey+"_count").html(data[k].total);
                  $("#covid19_"+data[k].classKey).html(label);
                  number++;
                }
              }
            }
            setTimeout(function(){
              $("table[name=tblResult_COVID_19] tbody").html(htmlStr);
              $("#updateTimeCovid19").html(time_ago(lastUpdate));
              $("#btnClassificationCovid19").removeClass('disabled btn-progress'); // prevent multiple click
              iziToast.success({
                title: 'Success',
                message: resText.msg,
                position: 'topCenter'
              });
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
  })
  // Get location
  
  $("#btnGetLocationCovid19").click(function(e){
    $(this).addClass('disabled btn-progress'); //prevent multiple click
    $.ajax({
      url: base_url + "/godata/getGenderByLocation",
      type: "POST",
      data: { outbreak_id: outbreakID , ob_id: obId_covid19 },
      dataType: 'json',
      success: function (resText) {
        // update data 
        console.log(resText)
        if(resText.status == true){
          if(resText.data){
            var data        = resText.data;
            var htmlStr     = '';
            var lastUpdate  = '';
            var label       = '';
            // empty data zone
            $("table[name=tblCovid19_location] tbody").html('tr><td colspan="5" class="text-center">Loading...</td></tr>');
            $("#tblCovid19_location_update").html("Loading...");
            var number = 1;
            for (var k in data){
              htmlStr += '<tr>';
              htmlStr += "<td>"+number+"</td>";
              htmlStr += "<td>"+data[k].name+"</td>";
              htmlStr += "<td>"+data[k].maleCount+"</td>";
              htmlStr += "<td>"+data[k].femaleCount+"</td>";
              htmlStr += "<td>"+data[k].casesCount+"</td>";
              htmlStr += '</tr>';
              lastUpdate = data[k].lastUpdate;
              number++;
            }
            setTimeout(function(){          
              $("table[name=tblCovid19_location] tbody").html(htmlStr);
              $("#tblCovid19_location_update").html(time_ago(lastUpdate));
              $("#btnGetLocationCovid19").removeClass('disabled btn-progress'); // prevent multiple click
              iziToast.success({
                title: 'Success',
                message: resText.msg,
                position: 'topCenter'
              });
            }, 1000);
          }
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
  })

  $("#btnGetLocationCovid19Nat").click(function(e){
    $(this).addClass('disabled btn-progress'); //prevent multiple click
    $.ajax({
      url: base_url + "/godata/getGenderByLocation",
      type: "POST",
      data: { outbreak_id: outbreakIDNational , ob_id: obId_covid19_nat },
      dataType: 'json',
      success: function (resText) {
        // update data 
        console.log(resText)
        
        if(resText.status == true){
          if(resText.data){
            var data        = resText.data;
            var htmlStr     = '';
            var lastUpdate  = '';
            var label       = '';
            // empty data zone
            $("table[name=tblCovid19Nat_location] tbody").html('tr><td colspan="5" class="text-center">Loading...</td></tr>');
            $("#tblCovid19Nat_location_update").html("Loading...");
            var number = 1;
            for (var k in data){
              htmlStr += '<tr>';
              htmlStr += "<td>"+number+"</td>";
              htmlStr += "<td>"+data[k].name+"</td>";
              htmlStr += "<td>"+data[k].maleCount+"</td>";
              htmlStr += "<td>"+data[k].femaleCount+"</td>";
              htmlStr += "<td>"+data[k].casesCount+"</td>";
              htmlStr += '</tr>';
              lastUpdate = data[k].lastUpdate;
              number++;
            }
            setTimeout(function(){          
              $("table[name=tblCovid19Nat_location] tbody").html(htmlStr);
              $("#tblCovid19Nat_location_update").html(time_ago(lastUpdate));
              $("#btnGetLocationCovid19Nat").removeClass('disabled btn-progress'); // prevent multiple click
              iziToast.success({
                title: 'Success',
                message: resText.msg,
                position: 'topCenter'
              });
            }, 1000);
          }
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
  })


  // Get classification of National
  $("#btnClassificationCovid19Nat").click(function(e){
    $(this).addClass('disabled btn-progress'); //prevent multiple click    
    $.ajax({
        url: base_url + "/godata/getClassification",
        type: "POST",
        data: { outbreak_id: outbreakIDNational , ob_id: obId_covid19_nat},
        dataType: 'json',
        success: function (resText) {
          // update data 
          if(resText.status == true){
            if(resText.data){
              var data = resText.data;
              var htmlStr = '';
              var lastUpdate = '';
              var label ='';
              var classification_pill_string = '';
              var color ='';
              // empty data zone
              $("table[name=tblResult_COVID_19_nat] tbody").html('tr><td colspan="5">Loading...</td></tr>');
              $("#updateTimeCovid19Nat").html("Loading...");
              $("#covid19Nat_class").html("Loading");
              var number = 1;
              
              for (var k in data){
              //  if(CLASSIFICATION_DISPLAY_FOR_COVID19[data[k].classKey] !== undefined){
                  label = word[data[k].classKey] == undefined ? data[k].classKey : word[data[k].classKey];
                  htmlStr += '<tr>';
                  htmlStr += "<td>"+number+"</td>";
                  htmlStr += "<td>"+label+"</td>";
                  htmlStr += "<td>"+format_number(data[k].maleCount)+"</td>";
                  htmlStr += "<td>"+format_number(data[k].femaleCount)+"</td>";                  
                  htmlStr += "<td>"+format_number(data[k].total)+"</td>";
                  lastUpdate = data[k].lastUpdate;
                  htmlStr += '</tr>';

                  color = LIST_COLOR_OUTBREAK_NATIONAL[data[k].classKey] == undefined ? 'light' : LIST_COLOR_OUTBREAK_NATIONAL[data[k].classKey];
                  
                  classification_pill_string += '<button type="button" class="btn btn-sm none-cursor">';
                  classification_pill_string += '<span class="badge badge-'+color+'">'+format_number(data[k].total)+'</span> '+label;
                  classification_pill_string += '</button>'
                  
                  number++;
                }
              //}
            }
            setTimeout(function(){
              $("table[name=tblResult_COVID_19_nat] tbody").html(htmlStr);
              $("#covid19Nat_class").html(classification_pill_string);

              $("#updateTimeCovid19Nat").html(time_ago(lastUpdate));
              $("#btnClassificationCovid19Nat").removeClass('disabled btn-progress'); // prevent multiple click
              iziToast.success({
                title: 'Success',
                message: resText.msg,
                position: 'topCenter'
              });
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
  })

  // Get High Risk
  $("#btnGetHighRiskCovid19").click(function(e){
    $(this).addClass('disabled btn-progress'); //prevent multiple click    
    $.ajax({
        url: base_url + "/godata/getHigtRisk",
        type: "POST",
        data: { outbreak_id: outbreakID , ob_id: obId_covid19},
        dataType: 'json',
        success: function (resText) {
          // update data 
          if(resText.status == true){
            if(resText.data){
              var data = resText.data;
              var htmlStr = '';
              var lastUpdate = ''; 
              var footerStr = '';
              var total = 0;
              var totalFollowup = 0; 
              // empty data zone
              $("table[name=tblCovid19_highrisk] tbody").html('<tr><td colspan="4">Loading...</td></tr>');
              $("table[name=tblCovid19_highrisk] tfoot").html('<tr><td colspan="4">Loading...</td></tr>');
              $("#updateTimehighrisk").html("Loading...");              
                                        
              htmlStr += '<tr>';
              htmlStr += "<td>"+format_number(data.maleCount)+"</td>";
              htmlStr += "<td>"+format_number(data.femaleCount)+"</td>";
              htmlStr += "<td>"+format_number(data.activeMaleCount)+"</td>";
              htmlStr += "<td>"+format_number(data.activeFemaleCount)+"</td>";
              lastUpdate = data.lastUpdate;
              total = parseInt(data.maleCount) + parseInt(data.femaleCount);
              totalFollowup = parseInt(data.activeMaleCount) + parseInt(data.activeFemaleCount);
              htmlStr += '</tr>';                 
              
              //footer
              footerStr += '<tr>';
              footerStr += '<td colspan="2">'+total+'</td>';
              footerStr += '<td colspan="2">'+totalFollowup+'</td>';
              footerStr += '</tr>';
              
            }
            setTimeout(function(){
              $("table[name=tblCovid19_highrisk] tbody").html(htmlStr);
              $("table[name=tblCovid19_highrisk] tfoot").html(footerStr);
              $("#updateTimehighrisk").html(time_ago(lastUpdate));
              $("#btnGetHighRiskCovid19").removeClass('disabled btn-progress'); // prevent multiple click
              iziToast.success({
                title: 'Success',
                message: resText.msg,
                position: 'topCenter'
              });
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
  })
  
  // Select Lab name
  $("select[name=lab_name]").change(function(e){        
    $(this).prop('disabled', true);//
    $("#loading").removeClass('d-none');
    var val = $(this).val();    
    $.ajax({
        url: base_url + "/godata/getClassByLab",
        type: "POST",
        data: { outbreak_id: outbreakIDNational , lab_name: val},
        dataType: 'json',
        success: function (resText) {
          // update data 
          if(resText.status == true){
            if(resText.data){
              var data = resText.data;
              var htmlStr = '';
              var lastUpdate = '';
              var label ='';
              var classification_pill_string = '';
              var color ='';
              // empty data zone
              $("table[name=tblResult_COVID_19_nat] tbody").html('<tr><td colspan="5">Loading...</td></tr>');
              var number = 1;
              if(data.length == 0){
                htmlStr += '<tr><td colspan="5" align="center">No data found</td></tr>';
              }else{
                for (var k in data){
                  htmlStr += '<tr>';
                  htmlStr += "<td>"+number+"</td>";
                  htmlStr += "<td>"+data[k].label+"</td>";
                  htmlStr += "<td>"+data[k].maleCount+"</td>";
                  htmlStr += "<td>"+data[k].femaleCount+"</td>";
                  htmlStr += "<td>"+format_number(data[k].total)+"</td>";
                  lastUpdate = data[k].lastUpdate;
                  htmlStr += '</tr>';                  
                  
                  number++;
                }
              }

            }
            setTimeout(function(){
              $("table[name=tblResult_COVID_19_nat] tbody").html(htmlStr);            
              $("#loading").addClass('d-none');
              $("select[name=lab_name").prop('disabled', false);
              iziToast.success({
                title: 'Success',
                message: resText.msg,
                position: 'topCenter'
              });
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
  })

  // Get Nationality
  $("#btnGetNationalityCovid19").click(function(e){
    $(this).addClass('disabled btn-progress'); //prevent multiple click    
    $.ajax({
        url: base_url + "/godata/getNationality",
        type: "POST",
        data: { outbreak_id: outbreakID , ob_id: obId_covid19},
        dataType: 'json',
        success: function (resText) {
          // update data 
          console.log(resText);
          if(resText.status == true){
            if(resText.data){
              var data = resText.data;
              var htmlStr = '';
              var lastUpdate = '';               
              var total = 0;       
              var number = 1;     
              // empty data zone
              $("table[name=tblCovid19_nationality] tbody").html('<tr><td colspan="5">Loading...</td></tr>');              
              $("#tblCovid19_nationality_update").html("Loading...");              
              for (var k in data){
                total = parseInt(data[k].maleCount) + parseInt(data[k].femaleCount);
                htmlStr += '<tr>';
                htmlStr += "<td>"+number+"</td>";
                htmlStr += "<td>"+data[k].name+"</td>";
                htmlStr += "<td>"+format_number(data[k].maleCount)+"</td>";     
                htmlStr += "<td>"+format_number(data[k].femaleCount)+"</td>";      
                htmlStr += "<td>"+total+"</td>";          
                lastUpdate = data[k].lastUpdate;
                htmlStr += '</tr>';  
                number++;
              }
            }
            setTimeout(function(){
              $("table[name=tblCovid19_nationality] tbody").html(htmlStr);              
              $("#tblCovid19_nationality_update").html(time_ago(lastUpdate));
              $("#btnGetNationalityCovid19").removeClass('disabled btn-progress'); // prevent multiple click
              iziToast.success({
                title: 'Success',
                message: resText.msg,
                position: 'topCenter'
              });
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
  })

  $("button[name='covid19BtnFilter']").click(function(e) {
    $(this).html('Please wait ...').attr('disabled', 'disabled'); // prevent multiple click
    e.preventDefault(); //Prevent the normal submission action
    var value           = $(this).val();
    var form            = $(this).parents('form');
    var sDate           = $("input[name='startDate']",form).val();
    var eDate           = $("input[name='endDate']",form).val();    
    
    $.ajax({
          url: base_url + "/godata/getNumberCasesByClassification",
          type: "POST",
          data: { startDate: sDate , endDate: eDate , outbreak_id: outbreakID , ob_id: obId_covid19},
          dataType: 'json',
          success: function (resText) {              
            if(resText.status == true){
              if(resText.data){
                var data = resText.data;
                var htmlStr = '';                
                var label ='';
                // empty data zone
                $("table[name=tblResult_COVID_19] tbody").html('tr><td colspan="7">Loading...</td></tr>');                
                var number = 1;
                for (var k in data){
                  if(CLASSIFICATION_DISPLAY_FOR_COVID19[data[k].classKey] !== undefined){
                    label = word[data[k].classKey];
                    htmlStr += '<tr>';
                    htmlStr += "<td>"+number+"</td>";
                    htmlStr += "<td>"+label+"</td>";
                    htmlStr += "<td>"+data[k].maleCount+"</td>";
                    htmlStr += "<td>"+data[k].femaleCount+"</td>";
                    htmlStr += "<td>"+data[k].dischargedMaleCount+"</td>";
                    htmlStr += "<td>"+data[k].dischargedFemaleCount+"</td>";
                    htmlStr += "<td>"+data[k].total+"</td>";                    
                    htmlStr += '</tr>';                   
                    number++;
                  }
                }
              }
              setTimeout(function(){
                $("table[name=tblResult_COVID_19] tbody").html(htmlStr);                
                $("button[name='covid19BtnFilter']").html('Filter').attr('disabled', false); // prevent multiple click
                iziToast.success({
                  title: 'Success',
                  message: resText.msg,
                  position: 'topCenter'
                });
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

  $("button[name='covid19NatBtnFilter']").click(function(e) {
    $(this).html('Please wait ...').attr('disabled', 'disabled'); // prevent multiple click
    e.preventDefault(); //Prevent the normal submission action
    var value           = $(this).val();
    var form            = $(this).parents('form');
    var sDate           = $("input[name='startDate']",form).val();
    var eDate           = $("input[name='endDate']",form).val();    
    
    $.ajax({
          url: base_url + "/godata/getNumberCasesByClassification",
          type: "POST",
          data: { startDate: sDate , endDate: eDate , outbreak_id: outbreakIDNational , ob_id: obId_covid19_nat},
          dataType: 'json',
          success: function (resText) {              
            if(resText.status == true){
              if(resText.data){
                var data = resText.data;
                var htmlStr = '';                
                var label ='';               
                // empty data zone
                $("table[name=tblResult_COVID_19_nat] tbody").html('tr><td colspan="5">Loading...</td></tr>');                               
                var number = 1;
                for (var k in data){
                //  if(CLASSIFICATION_DISPLAY_FOR_COVID19[data[k].classKey] !== undefined){
                    label = word[data[k].classKey] == undefined ? data[k].classKey : word[data[k].classKey];
                    htmlStr += '<tr>';
                    htmlStr += "<td>"+number+"</td>";
                    htmlStr += "<td>"+label+"</td>";
                    htmlStr += "<td>"+format_number(data[k].maleCount)+"</td>";
                    htmlStr += "<td>"+format_number(data[k].femaleCount)+"</td>";                  
                    htmlStr += "<td>"+format_number(data[k].total)+"</td>";                    
                    htmlStr += '</tr>';                                       
                    number++;
                  }
                //}
              }
              setTimeout(function(){
                $("table[name=tblResult_COVID_19_nat] tbody").html(htmlStr);                                  
                $("button[name='covid19NatBtnFilter']").html('Filter').attr('disabled', false); // prevent multiple click
                iziToast.success({
                  title: 'Success',
                  message: resText.msg,
                  position: 'topCenter'
                });
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

  $("#btnGetLocationAndClassCovid19Nat").click(function(e){
    $(this).addClass('disabled btn-progress'); //prevent multiple click    
    $.ajax({
        url: base_url + "/godata/getNationality",
        type: "POST",
        data: { outbreak_id: outbreakID , ob_id: obId_covid19},
        dataType: 'json',
        success: function (resText) {
          // update data 
          console.log(resText);
          if(resText.status == true){
            
            setTimeout(function(){                            
              $("#btnGetLocationAndClassCovid19Nat").removeClass('disabled btn-progress'); // prevent multiple click
              iziToast.success({
                title: 'Success',
                message: resText.msg,
                position: 'topCenter'
              });
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
  })