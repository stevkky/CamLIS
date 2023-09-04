$(function() {
  var $preview_modal = $("#print_preview_modal");
  
  //DateTimePicker options
  var datePickerConfig = {
      widgetPositioning : {
          horizontal	: 'left',
          vertical	: 'bottom'
      },
      format			: 'DD/MM/YYYY',
      useCurrent		: false,
      maxDate			: new Date(),
      locale			: app_lang == 'kh' ? 'km' : 'en'
  };

    var specimen_color = [
      { 
        id: 6,
        color:'#ea756c'
      }, // blood culture
      { 
        id: 8,
        color:'#a3a500'
      }, // csf
      { 
        id: 10,
        color:'#5ec0c5'
      }, // Sputum
      { 
        id: 11,
        color:'#59b1f6'
      }, // Stool
      { 
        id: 13,
        color:'#57b705'
      }, // Genital swap
      { 
        id: 14,
        color:'#9590ff'
      }, //Throat swap
      { 
        id: 15,
        color:'#e76bf3'
      }, // Tissue
      { 
        id: 16,
        color:'#f4c90a'
      }, // Urine
      { 
        id: 917,
        color:'#d1b087'
      }, // Urine
    ];
    var organism_color = [
      { name: "Acinetobacter sp.", color: "#ea756c"},
      { name: "Aeromonas sp.", color: "#eb7e4c"},
      { name: "Bacillus sp.", color: "#4dc199"},
      { name: "Burkholderia cepacia", color: "#e78622"},
      { name: "Burkholderia pseudomallei", color: "#db8e1c"},
      { name: "Candida, not albicans", color: "#ce9514"},
      { name: "Coagulase Negative Staphylococcus", color: ""},
      { name: "Corynebacterium sp.", color: "#9dc992"},
      { name: "Cryptococcus sp.", color: "#aea201"},
      { name: "Enterobacter aerogenes", color: "#64b200"},
      { name: "Enterobacter cloacae", color: "#57b705"},
      { name: "Enterobacter sp.", color: "#82ad00"},
      { name: "Enterococcus sp.", color: "#aea201"},
      { name: "Escherichia coli", color: "#64b200"},
      { name: "Klebsiella pneumoniae", color: "#5bbe5d"},
      { name: "Listeria monocytogenes", color: "#5dbbdf"},
      { name: "No significant growth found", color: ""},
      { name: "Non-fermenting gram negative rods", color: "#5bb6ed"},
      { name: "Proteus mirabilis", color: "#59affa"},
      { name: "Pseudomonas aeruginosa", color: "#619cff"},
      { name: "Staphylococcus aureus", color: "#b385ff"},
      { name: "Staphylococcus aureus 1", color: "#b385ff"},
      { name: "Staphylococcus aureus 2", color: "#b385ff"},
      { name: "Stenotrophomonas maltophilia", color: "#cd79ff"},
      { name: "Streptococcus anginosus group", color: "#ea66eb"},
      { name: "Streptococcus pneumoniae", color: "#ea66eb"},
      { name: "Streptococcus pyogenes", color: "#ea60da"},
      { name: "Streptococcus sp.", color: "#6dc792"},
      { name: "Streptococcus viridans, alpha-hem.", color: "#6dc192"},
      { name: "Streptococcus, beta-haem. Group B", color: "#e9669f"},
      { name: "Gram negative bacteria", color: "#5dc492" },
      { name: "Gram negative bacilli 1", color: "#5dc192"},
      { name: "Gram negative bacilli 2", color: "#5dc192"},
      { name: "Gram negative bacilli (rods)", color: "#5dc192"},
      { name: "Gram negative bacilli", color: "#5dc192"},
      { name: "Streptococcus suis", color: "#e95fc9"},
      { name: "Salmonella sp.", color: "#9191ff"},
      { name: "Pseudomonas sp.", color: "#55a7ff"},
      { name: "Gram positive bacilli", color: "#5fc1bc"},
      { name: "Elizabethkingia sp.", color: "#9aa801"},
      { name: "Candida albicans", color: "#bf9c06"}
    ]
    function get_specimen_color(id){
      var color = "#"+Math.floor(Math.random()*16777215).toString(16);
      for( var i in specimen_color){
        if(parseInt(specimen_color[i].id) == parseInt(id)) {
          color = specimen_color[i].color;
          break;
        }
      }
      return color;
    }
    function get_organism_color(name){
      var color = "#"+Math.floor(Math.random()*16777215).toString(16);
      for( var i in organism_color){
        if(organism_color[i].name == name) {
          color = organism_color[i].color;
          break;
        }
      }
      return color;
    }    

    var $start_date = $("#start-date");
    var $end_date   = $("#end-date");
    var $start_time = $("#start-time");
    var $end_time   = $("#end-time");

    $("select:not([multiple])").select2();
    $("select#laboratories").multiselect({
        'buttonWidth': '100%',
        'buttonClass': 'form-control text-left custom-multiselect',
        'includeSelectAllOption': true,
        'enableFiltering': true,
        'filterPlaceholder': '',
        'selectAllText': 'All',
        'nonSelectedText': 'Choose',
        'nSelectedText': 'laboratories',
        'allSelectedText': 'All',
        'numberDisplayed': 1,
        'selectAllNumber': false,
        'templates': {
            ul: '<ul class="multiselect-container dropdown-menu custom-multiselect-container"></ul>',
            filter: '<li class="multiselect-item filter"><input class="form-control input-sm multiselect-search" type="text"></li>',
        }
    });

    $start_date.datetimepicker(dateTimePickerOption);
    $end_date.datetimepicker(dateTimePickerOption);
    $start_time.timepicker({minuteStep: 1, showMeridian: false});
    $end_time.timepicker({minuteStep: 1, showMeridian: false});
    
    $("#btnGenerate").on("click", function(evt) {
      evt.preventDefault();
      var start_date = $start_date.data("DateTimePicker").date();
      var end_date   = $end_date.data("DateTimePicker").date();
      var start_time = moment($start_time.val(), 'HH:mm');
      var end_time   = moment($end_time.val(), 'HH:mm');

      if (!start_date || !end_date || !start_time.isValid() || !end_time.isValid()) {
          myDialog.showDialog("show", { text: msg_required_data, style: 'warning'});
          return false;
      }
      // Set header
      var data = {
        start_date:start_date.format("YYYY-MM-DD"),
        start_time: start_time.format("HH:mm"),
        end_date: end_date.format("YYYY-MM-DD"),
        end_time: end_time.format("HH:mm"),
        laboratories: $('#laboratories').val()
      }
      $(".lab_name").html($( "#laboratories option:selected" ).text()); // Set lab name
      $(".date_frame").html(start_date.format("DD/MMM/YY")+" - "+end_date.format("DD/MMM/YY"));

      myDialog.showProgress("show");

      // Generate Header of the table
      var columns = [];
      columns.push({ title : "Pathogens", data : "Pathogens" });
      var dates = dateRange(start_date.format("YYYY-MM-DD"), end_date.format("YYYY-MM-DD"));
      for (var i in dates){
        var check = moment(dates[i], 'YYYY-MM-DD');
        var month = check.format('MMM');        
        columns.push({ title : month, data : month });
      }
      columns.push({ title : "Total", data : "Total" });     
      var index = columns.length - 1;
      // Generate auto header
      var bloodstream_pathogens_tbl = $('#bloodstream_pathogens_tbl').DataTable({
        "searching" : false,
        "paging"    : false,
        "info"      : false,
        "columns"   : columns,
        "order":[[index,'desc']]
      });
      var pathogens_list_tbl =  $('#pathogens_list_tbl').DataTable({
        "searching" : false,
        "paging"    : false,
        "info"      : false,
        "columns"   : columns,
        "order":[[index,'desc']]
      });      
     
     var Klebsiella_pneumoniae_organism_tbl = $('#Klebsiella_pneumoniae_organism_tbl').DataTable({
          "searching": false,
          "paging":   false,          
          "info":     false,
          "order": [[4, "desc"]]
      });
      var escherichia_coli_organism_tbl = $('#escherichia_coli_organism_tbl').DataTable({
        "searching": false,
        "paging":   false,        
        "info":     false,
        "order": [[4, "desc"]]
     });
     var acinetobacter_organism_tbl = $('#acinetobacter_organism_tbl').DataTable({
        "searching": false,
        "paging":   false,        
        "info":     false,
        "order": [[4, "desc"]]
    });
    var staphylococcus_organism_tbl = $('#staphylococcus_organism_tbl').DataTable({
        "searching": false,
        "paging":   false,
        "info":     false,
        "order": [[4, "desc"]]
    });
    var streptococcus_pneumoniae_organism_tbl = $('#streptococcus_pneumoniae_organism_tbl').DataTable({
      "searching": false,
      "paging":   false,
      "info":     false,
      "order": [[4, "desc"]]
  });
    var antibiotic_bps_tbl = $('#antibiotic_bps_tbl').DataTable({
      "searching": false,
      "paging":   false,    
      "info":     false,
      "order": [[4, "desc"]]
    });
    var true_pathogen_tbl = $('#true_pathogen_tbl').DataTable({
        "searching": false,
        "paging":   false,
        "info":     false,
        "order": [[1, "desc"]]
    });

    var streptococcus_suis_organism_tbl = $('#streptococcus_suis_organism_tbl').DataTable({
      "searching": false,
      "paging":   false,
      "info":     false,
      "order": [[4, "desc"]]
    });

    var streptococcus_pneumoniae_organism_csf_tbl = $('#streptococcus_pneumoniae_organism_csf_tbl').DataTable({
      "searching": false,
      "paging":   false,
      "info":     false,
      "order": [[4, "desc"]]
    });
    var streptococcus_organism_tbl = $('#streptococcus_organism_tbl').DataTable({
      "searching": false,
      "paging":   false,
      "info":     false,
      "order": [[4, "desc"]]
    });
    var neisseria_meningitidis_organism_tbl = $('#neisseria_meningitidis_organism_tbl').DataTable({
      "searching": false,
      "paging":   false,
      "info":     false,
      "order": [[4, "desc"]]
    });
    
    
    // Store table id for saving as word
    var table_id = [
      "bloodstream_pathogens_tbl",
      "pathogens_list_tbl",      
      "Klebsiella_pneumoniae_organism_tbl",
      "escherichia_coli_organism_tbl",
      "acinetobacter_organism_tbl",
      "staphylococcus_organism_tbl",
      "antibiotic_bps_tbl",
      "true_pathogen_tbl",
      "streptococcus_pneumoniae_organism_tbl",
      "streptococcus_suis_organism_tbl",
      "streptococcus_pneumoniae_organism_csf_tbl",
      "streptococcus_organism_tbl",
      "neisseria_meningitidis_organism_tbl",
      "rejection_tbl"
    ];
    
    var print_data = []; //  Save result for printing data as MSWord
      $("#result_page").css("display","block");
      $.when(
        
        $.ajax({
            url: base_url + "report/get_patients_gender",
            type: "POST",
            dataType: "json",
            data: data,
            success: function(resText) {
              console.log("1) Distribution by Gender: SUCCESS");
              var total = 0;
              console.log(resText.data);
              var data = resText.data;
              if(data.length > 0){
                for( var i in resText.data){
                  total += parseInt(resText.data[i].count);
                }
              }else{
                data = [
                  {"gender": "male", "count": 0 , "color": "#ffffff"},
                  {"gender": "female", "count": 0 , "color": "#000000"}
                ]
              }
              var chart = make_gender_chart(data , total);
              print_data.push({order:1 , type: 2 , obj: chart , destination: "gender_chart"});
              
            },
            error: function(err1) {
              console.log("1) Distribution by Gender:");
              console.log("====> Err: ");
              console.log(err1.responseText)
            }
        }),
        $.ajax({
          url: base_url + "report/get_patients_age",
          type: "POST",
          dataType: "json",
          data: data,
          success: function(resText) {
            console.log("2) Distribution by Age Group: SUCCESS");
            //console.log(resText.data)
            chart = build_age_group_chart(resText.data);
            //export_img(chart,"age_group_chart_img");            
            print_data.push({order:2 , type: 2 , obj: chart , destination: "age_group_chart"});
          },
          error: function(err1) {
            console.log("2) Distribution by Age Group");
            console.log("====> Err: ");
            console.log(err1.responseText)
          }
        }),
        $.ajax({
          url: base_url + "report/get_micro_specimen",
          type: "POST",
          dataType: "json",
          data: data,
          success: function(resText) {
            console.log("3) Microbiology specimen: SUCCESS");            
            datas = resText.data;
            month_data = resText.month_data;
            console.log(datas);
            console.log(resText.month_data);
            console.log(resText.group_by);
            
            var data_group_by_month = resText.ready_data;
            
            for(var i in std_sample){ 
              var sample_id = std_sample[i].ID;
              data = datas[std_sample[i].ID];
              if(![9,17].includes(parseInt(sample_id))){                
                sample_name = std_sample[i].sample_name;
                sample_chart_id = sample_name.replace(" ","_")+"_chart";
                //Check if specimen zero
                check = 0; // zero data
                for(var j in data){
                  if(parseInt(data[j].count) > 0){
                    check = 1;
                    break;
                  }
                }
                if(check == 0){
                  $("#"+sample_chart_id+"_parent").remove();
                  console.log("#parent_"+sample_chart_id+"_image");
                  $("#parent_"+sample_chart_id+"_image").remove(); // remove from report
                }else{                  
                  specimen_chart = make_micro_specimen_chart(sample_chart_id,datas[std_sample[i].ID],sample_name,get_specimen_color(std_sample[i].ID));                  
                  print_data.push({order:3 , type: 2 , obj: specimen_chart , destination: sample_chart_id});
                }
              }
            }
            //Merge pus
            var pus_chart = make_micro_specimen_chart("pus_chart",datas[917],"Pus", get_specimen_color(917));            
            print_data.push({order:3 , type: 2 , obj: pus_chart , destination: "pus_chart"});
            // Display specimen by month
            parent = $("#specimen_by_month");
            parent_report = $("#specimen_by_month_wrapper"); // for report
            for(var k in data_group_by_month){
              id_chart = k;
              data = data_group_by_month[k];
              title = data[0].month+"-"+data[0].year;              
              parent.append('<div class="col-sm-6"><div id="'+id_chart+'" style="width: 100%; height: 350px;"></div></div>');
              chart = make_specimen_chart_by_month(id_chart, data, title);              
              print_data.push({order:3 , type: 2 , obj: chart , destination: id_chart});
              // Create div ele for report
              parent_report.append('<div id="'+id_chart+'_image" style="width: 50%; float: left;"></div>');
            }
          },
          error: function(err1) {
            console.log("3) Microbiology specimen");
            console.log("====> Err: ");
            console.log(err1.responseText)
          }
        }),        
        $.ajax({
          url: base_url + "report/get_isolated_pathogens",
          type: "POST",
          dataType: "json",
          data: data,
          success: function(resText) {
            console.log("4) Isolated Pathogens among Blood and CSF :SUCCESS");            
            var esc_coli_group = {
              organism_name : 'Escherichia coli',              blood: 0,              csf:0,              color:'#ea756c'
            };            
            var csf_data          = resText.csf;
            var blood_data        = resText.blood;            
            var ready_csf_data    = [];
            var ready_blood_data  = [];
            var is_escoli_found = false;
            for(var j in csf_data){
              if(parseInt(csf_data[j].csf) > 0){
                csf_data[j].color = get_organism_color(csf_data[j].organism_name);
                // Group Escherichia coli together
                if(csf_data[j].organism_name == 'Escherichia coli' || csf_data[j].organism_name == 'Escherichia coli 1' || csf_data[j].organism_name == 'Escherichia coli 2'){
                  esc_coli_group.csf += parseInt(csf_data[j].csf);
                  is_escoli_found = true;
                }else{
                  ready_csf_data.push(csf_data[j]);
                }
              }
            }
            // push group esc coli into array
            if(is_escoli_found == true){
              ready_csf_data.unshift(esc_coli_group);
              is_escoli_found = false;
            } 
            
            for(var k in blood_data){
              if(parseInt(blood_data[k].blood) > 0){
                blood_data[k].color = get_organism_color(blood_data[k].organism_name);
                // Group Escherichia coli together
                if(blood_data[k].organism_name == 'Escherichia coli' || blood_data[k].organism_name == 'Escherichia coli 1' || blood_data[k].organism_name == 'Escherichia coli 2'){
                  esc_coli_group.blood += parseInt(blood_data[k].blood);
                  is_escoli_found = true;
                }else{
                  ready_blood_data.push(blood_data[k]);
                }
              }
            }
            // push group esc coli into array
            if(is_escoli_found == true){
              ready_blood_data.unshift(esc_coli_group);
              is_escoli_found = false;
            } 
            //console.log(ready_blood_data);
            csf = build_isolated_pathogen_in_csf(ready_csf_data);
            var blood = make_blood_culture_pathogen_chart(ready_blood_data);
            print_data.push({order:4 , type: 2 , obj:  csf, destination: "csf_pathogent_chart"});
            print_data.push({order:4 , type: 2 , obj: blood , destination: "blood_culture_pathogent_chart"});
            // Group pathogen "Escherichia coli" into one
          },
          error: function(err1) {
            console.log("4) Isolated Pathogens among Blood and CSF");
            console.log("    Error:");
            console.log(err1.responseText);
          }
        }),
        $.ajax({
          url: base_url + "report/get_bloodstream_pathogens_isolated",
          type: "POST",
          dataType: "json",
          data: data,
          success: function(resText) {
            console.log("5) Bloodstream pathogens isolated: SUCCESS");            
            var bodyString    = resText.body;
            console.log(bodyString);
            var esc_coli_group = {
              organism_name : 'Escherichia coli',              adult: 0,              pediatric:0,              color:'#ea756c'
            };
            var is_escoli_found = false;
            bloodstream_pathogens_tbl.rows.add($(bodyString)).draw();
            // Show graph
            var adult_data      = [];
            var pediatric_data  = [];
            var data            = resText.graph_data;
            console.log(data);
            for(var j in data){
              if(parseInt(data[j].adult) > 0){
                data[j].color = get_organism_color(data[j].organism_name);                
                // Group Escherichia coli together
                if(data[j].organism_name == 'Escherichia coli' || data[j].organism_name == 'Escherichia coli 1' || data[j].organism_name == 'Escherichia coli 2'){
                  esc_coli_group.adult += parseInt(data[j].adult);
                  is_escoli_found = true;
                }else{
                  adult_data.push(data[j]);
                }
              }
            }
            // if Esc-coli found
            if(is_escoli_found == true){
              adult_data.unshift(esc_coli_group);
              is_escoli_found = false;
            }

            for(var k in data){
              if(parseInt(data[k].pediatric) > 0){
                data[k].color = get_organism_color(data[k].organism_name);
                // Group Escherichia coli together
                if(data[k].organism_name == 'Escherichia coli' || data[k].organism_name == 'Escherichia coli 1' || data[k].organism_name == 'Escherichia coli 2'){
                  esc_coli_group.pediatric += parseInt(data[k].pediatric);
                  is_escoli_found = true;
                }else{
                  pediatric_data.push(data[k]);
                }
              }
            }
            // if Esc-coli found
            if(is_escoli_found == true){
              pediatric_data.unshift(esc_coli_group);
              is_escoli_found = false;
            }

            adult = make_bloodstream_pathogen_chart("bloodstream_pathogens_chart_adult",adult_data, "Adult (>=14 years old)" , "adult");
            pediatric = make_bloodstream_pathogen_chart("bloodstream_pathogens_chart_pediatric",pediatric_data, "Pediatric ( < 14 years old)", "pediatric");            
            print_data.push({order:5 , type: 2 , obj: adult , destination: "bloodstream_pathogens_chart_adult"});
            print_data.push({order:5 , type: 2 , obj: pediatric , destination: "bloodstream_pathogens_chart_pediatric"});
          },
          error: function(err1) {
            console.log("5) Bloodstream pathogen isolate");
            console.log("    Error:");
            console.log(err1.responseText);
          }
        }),
        $.ajax({
          url: base_url + "report/get_true_pathogen_by_wards",
          type: "POST",
          dataType: "json",
          data: data,
          success: function(resText) {
            console.log("6) Blood culture true pathogen rate and contamination rate by ward: SUCCESS");            
            bodyString = "";
            data = resText.data;
            request_patient = resText.request_patient;
            true_pathogen   = resText.true_pathogen;
            contaminant     = resText.contaminant;

            console.log(request_patient);
            console.log(true_pathogen);
            console.log(contaminant);
            
            if(request_patient.length > 0){
              for(var i in request_patient){
                number_of_patient = request_patient[i].patient_request;
                number_of_bottle  = request_patient[i].number_of_bottle;
                sample_source_id  = request_patient[i].ID;
                // get number of pathogen
                number_pathogen = 0;
                
                for(var j in true_pathogen){
                  if (true_pathogen[j].sample_source_id == sample_source_id) {
                    number_pathogen = true_pathogen[j].number_pathogen;
                    break;
                  }
                }
                
                number_contaminant = 0;
                for(var k in contaminant){
                  if (contaminant[k].ID == sample_source_id) {
                    number_contaminant = contaminant[k].fake_organism;
                    break;
                  }
                }
                true_pathogen_rate = Math.floor((parseInt(number_pathogen) / parseInt(number_of_patient)) * 100);
                contamination_rate = (number_contaminant > 0 && number_of_bottle > 0) ? Math.floor((parseInt(number_contaminant) / parseInt(number_of_bottle)) * 100) : 0;
                bodyString += "<tr>";
                bodyString += "<td>"+request_patient[i].source_name+"</td>";
                bodyString += "<td>"+number_of_patient+"</td>";
                bodyString += "<td>"+number_pathogen+" ("+true_pathogen_rate+"%)"+"</td>";
                bodyString += "<td>"+number_of_bottle+"</td>";
                bodyString += "<td> "+number_contaminant+" ("+contamination_rate+"%)"+"</td>";
                bodyString += "</tr>";
              }
              true_pathogen_tbl.rows.add($(bodyString)).draw();
            }
            
          },
          error: function(err1) {
            console.log("6) Blood culture true pathogen rate and contamination rate by ward: Error");
            console.log(err1.responseText);
          }
        }),           
        $.ajax({
          url: base_url + "report/get_volume_blood_culture",
          type: "POST",
          dataType: "json",
          data: data,
          success: function(resText) {
            console.log("7) Blood culture volume : SUCCESS");
            console.log(resText);
            blood_volumn_pediatric_28d   = build_blood_volumn_28d("blood_culture_volume_for_pediatric_28d", resText.data_pediatric_28d);
            blood_volumn_pediatric_29d1y = build_blood_volumn_29d1y("blood_culture_volume_for_pediatric_29d1y", resText.data_pediatric_29d1y);
            blood_volumn_pediatric_1y14y = build_blood_volumn_1y14y("blood_culture_volume_for_pediatric_1y14y", resText.data_pediatric_1y14y);
            blood_volumn_total           = build_total_blood_volumn("blood_culture_volume_for_total", resText.total);
            blood_volumn_adult           = build_adult_blood_volumn("blood_culture_volume_adult", resText.data_adult);
            
            print_data.push({order:7 , type: 2 , obj: blood_volumn_pediatric_28d , destination: "blood_culture_volume_for_pediatric_28d"});
            print_data.push({order:7 , type: 2 , obj: blood_volumn_pediatric_29d1y , destination: "blood_culture_volume_for_pediatric_29d1y"});
            print_data.push({order:7 , type: 2 , obj: blood_volumn_pediatric_1y14y , destination: "blood_culture_volume_for_pediatric_1y14y"});
            print_data.push({order:7 , type: 2 , obj: blood_volumn_adult , destination: "blood_culture_volume_adult"});
            print_data.push({order:7 , type: 2 , obj: blood_volumn_total , destination: "blood_culture_volume_for_total"});
          },
          error: function(err1) {
            console.log("7) Blood culture volume");
            console.log("   Err: ");
            console.log(err1.responseText);
          }
        }),
        $.ajax({
          url: base_url + "report/get_notifiable_pathogens_list",
          type: "POST",
          dataType: "json",
          data: data,
          success: function(resText) {
            console.log("8) Notifiable and other important pathogens list: SUCCESS");
            console.log(resText);
            console.log(resText.sample_ids);
            console.log(resText.patient_samples);
            //$("#pathogens_list_tbl thead").html(resText.header);
           
            var group_result  = resText.group_result;
            var bodyString    = "";
            var organism_name = "";
            for(var organism_name in group_result){
              bodyString += "<tr>";
              bodyString += "<td><i>"+organism_name+"<i></td>";
              row = group_result[organism_name];      
              total = 0;        
              for(var month in row){
                count = row[month];
                total += parseInt(count);
                bodyString += "<td>"+count+"</td>";
              }
              bodyString += "<td>"+total+"</td>";
              bodyString += "</tr>";
            }            
            pathogens_list_tbl.rows.add($(bodyString)).draw();
          },
          error: function(err1) {
            console.log("8) Notifiable and other important pathogens list");
            console.log("    Error:");
            console.log(err1);
          }
        }),        
        $.ajax({
          url: base_url + "report/get_bps_from_all_specimen",
          type: "POST",
          dataType: "json",
          data: data,
          success: function(resText) {
            console.log("9.1 Burkholderia pseudomallei (Bps): SUCCESS");
            console.log(resText.data);
            data = resText.data;
            bps_chart = make_bps_chart(resText.data);
            print_data.push(bps_chart);

            var resultString = "";
            var antibiotic_result = resText.antibiotic_result;
            console.log(antibiotic_result);

            if(antibiotic_result.length > 0){
              for(var i in antibiotic_result){
                resultString += '<tr>';
                resultString += '<td>'+antibiotic_result[i].antibiotic_name+"</td>";
                resultString += '<td>'+antibiotic_result[i].sensitive+"</td>";
                resultString += '<td>'+antibiotic_result[i].intermediate+"</td>";
                resultString += '<td>'+antibiotic_result[i].resistant+"</td>";
                resultString += '<td>'+antibiotic_result[i].total+"</td>";
                resultString += '</tr>';
              }
              antibiotic_bps_tbl.rows.add($(resultString)).draw();
            }            
          },
          error: function(err1) {
            console.log("9.1 Burkholderia pseudomallei (Bps)");
            console.log("     Error:");
              console.log(err1.responseText)
          }
        }),
        $.ajax({
          url: base_url + "report/get_salmonella_from_all_specimen",
          type: "POST",
          dataType: "json",
          data: data,
          success: function(resText) {
            console.log("9.2) Salmonella cases: SUCCESS");
            console.log(resText.data);
            console.log(resText.psample);
            salmonella_chart = make_salmonella_chart(resText.data);            
            print_data.push({order:10 , type: 2 , obj: salmonella_chart , destination: "salmonella_chart"});
          },
          error: function(err1) {
            console.log("9.2) Salmonella cases: Error");
            console.log(err1.responseText);
              setTimeout(function () {
                myDialog.showProgress('hide');
              }, 400);
          }
        }),
        $.ajax({
          url: base_url + "report/get_antibiotic_organism",
          type: "POST",
          dataType: "json",
          data: {
            start_date:start_date.format("YYYY-MM-DD"),
            start_time: start_time.format("HH:mm"),
            end_date: end_date.format("YYYY-MM-DD"),
            end_time: end_time.format("HH:mm"),
            laboratories: $('#laboratories').val(),
            organism_id: '235,236,237',
            sample_id: 6
          },
          success: function(resText) {
            console.log("9.3 Escherichia coli: SUCCESS");
            console.log(resText);
            var resultString = "";
            if(resText.length > 0){              
              for(var i in resText){
                resultString += '<tr>';
                resultString += '<td>'+resText[i].antibiotic_name+"</td>";
                resultString += '<td>'+resText[i].sensitive+"</td>";
                resultString += '<td>'+resText[i].intermediate+"</td>";
                resultString += '<td>'+resText[i].resistant+"</td>";
                resultString += '<td>'+(parseInt(resText[i].resistant) + parseInt(resText[i].sensitive) + parseInt(resText[i].intermediate))+"</td>";
                resultString += '</tr>';
              }
              escherichia_coli_organism_tbl.rows.add($(resultString)).draw();
            }
          },
          error: function(err1) {
            console.log("9.3 Escherichia coli: Error");
              console.log(err1.responseText);
          }
        }),
        $.ajax({
          url: base_url + "report/get_antibiotic_organism",
          type: "POST",
          dataType: "json",
          data: {
            start_date:start_date.format("YYYY-MM-DD"),
            start_time: start_time.format("HH:mm"),
            end_date: end_date.format("YYYY-MM-DD"),
            end_time: end_time.format("HH:mm"),
            laboratories: $('#laboratories').val(),
            organism_id: '92,93,94',
            sample_id: 6
          },
          success: function(resText) {
            console.log("9.4 Acinetobacter Sp: SUCCESS");
            //console.log(resText);
            var resultString = "";
            if(resText.length > 0){
              for(var i in resText){
                resultString += '<tr>';
                resultString += '<td>'+resText[i].antibiotic_name+"</td>";
                resultString += '<td>'+resText[i].sensitive+"</td>";
                resultString += '<td>'+resText[i].intermediate+"</td>";
                resultString += '<td>'+resText[i].resistant+"</td>";
                resultString += '<td>'+(parseInt(resText[i].resistant) + parseInt(resText[i].sensitive) + parseInt(resText[i].intermediate))+"</td>";
                resultString += '</tr>';
              }
              acinetobacter_organism_tbl.rows.add($(resultString)).draw();
            }
          },
          error: function(err1) {
            console.log("9.4 Acinetobacter Sp: Error");
            console.log(err1.responseText);
          }
        }), 
        $.ajax({
          url: base_url + "report/get_antibiotic_organism",
          type: "POST",
          dataType: "json",
          data: {
            start_date:start_date.format("YYYY-MM-DD"),
            start_time: start_time.format("HH:mm"),
            end_date: end_date.format("YYYY-MM-DD"),
            end_time: end_time.format("HH:mm"),
            laboratories: $('#laboratories').val(),
            organism_id: 301,
            sample_id: 6
          },
          success: function(resText) {
            console.log("9.5 Klebsiella pneumoniae: SUCCESS");
            //console.log(resText);
            var resultString = "";
            if(resText.length > 0){
              for(var i in resText){
                resultString += '<tr>';
                resultString += '<td>'+resText[i].antibiotic_name+"</td>";
                resultString += '<td>'+resText[i].sensitive+"</td>";
                resultString += '<td>'+resText[i].intermediate+"</td>";
                resultString += '<td>'+resText[i].resistant+"</td>";
                resultString += '<td>'+(parseInt(resText[i].resistant) + parseInt(resText[i].sensitive) + parseInt(resText[i].intermediate))+"</td>";
                resultString += '</tr>';
              }
              Klebsiella_pneumoniae_organism_tbl.rows.add($(resultString)).draw();
            }
          },
          error: function(err1) {
            console.log("9.5 Klebsiella pneumoniae: Error");
              console.log(err1.responseText)
          }
        }),
        $.ajax({
          url: base_url + "report/get_antibiotic_organism",
          type: "POST",
          dataType: "json",
          data: {
            start_date:start_date.format("YYYY-MM-DD"),
            start_time: start_time.format("HH:mm"),
            end_date: end_date.format("YYYY-MM-DD"),
            end_time: end_time.format("HH:mm"),
            laboratories: $('#laboratories').val(),
            organism_id: 430,
            sample_id: 6
          },
          success: function(resText) {
            console.log("9.6 Staphylococcus aureus : SUCCESS");
            console.log(resText);
            var resultString = "";
            if(resText.length == 0){
              resultString = '<tr><td colspan="5">no data</td></tr>';
            }else{
              for(var i in resText){
                resultString += '<tr>';
                resultString += '<td>'+resText[i].antibiotic_name+"</td>";
                resultString += '<td>'+resText[i].sensitive+"</td>";
                resultString += '<td>'+resText[i].intermediate+"</td>";
                resultString += '<td>'+resText[i].resistant+"</td>";
                resultString += '<td>'+(parseInt(resText[i].resistant) + parseInt(resText[i].sensitive) + parseInt(resText[i].intermediate))+"</td>";
                resultString += '</tr>';
              }
              staphylococcus_organism_tbl.rows.add($(resultString)).draw();
            }
          },
          error: function(err1) {
              console.log("9.6 Staphylococcus aureus")
              console.log("    ERROR: ");
              console.log(err1.responseText);
          }
        }),
        $.ajax({
          url: base_url + "report/get_antibiotic_organism",
          type: "POST",
          dataType: "json",
          data: {
            start_date:start_date.format("YYYY-MM-DD"),
            start_time: start_time.format("HH:mm"),
            end_date: end_date.format("YYYY-MM-DD"),
            end_time: end_time.format("HH:mm"),
            laboratories: $('#laboratories').val(),
            organism_id: 445,
            sample_id: 6
          },
          success: function(resText) {
            console.log("9.7 Streptococcus pneumoniae: SUCCESS");
            console.log(resText);
            var resultString = "";
            if(resText.length > 0){
              for(var i in resText){
                resultString += '<tr>';
                resultString += '<td>'+resText[i].antibiotic_name+"</td>";
                resultString += '<td>'+resText[i].sensitive+"</td>";
                resultString += '<td>'+resText[i].intermediate+"</td>";
                resultString += '<td>'+resText[i].resistant+"</td>";
                resultString += '<td>'+(parseInt(resText[i].resistant) + parseInt(resText[i].sensitive) + parseInt(resText[i].intermediate))+"</td>";
                resultString += '</tr>';
              }
              streptococcus_pneumoniae_organism_tbl.rows.add($(resultString)).draw();
            }
          },
          error: function(err1) {
            console.log("9.7 Streptococcus pneumoniae: Err");
              console.log(err1.responseText);
          }
        }),
        $.ajax({
          url: base_url + "report/get_antibiotic_organism",
          type: "POST",
          dataType: "json",
          data: {
            start_date:start_date.format("YYYY-MM-DD"),
            start_time: start_time.format("HH:mm"),
            end_date: end_date.format("YYYY-MM-DD"),
            end_time: end_time.format("HH:mm"),
            laboratories: $('#laboratories').val(),
            organism_id: 448,
            sample_id: 8
          },
          success: function(resText) {
            console.log("10.1) Streptococcus suis: SUCCESS");
            console.log(resText);
            var resultString = "";
            if(resText.length > 0){              
              for(var i in resText){
                resultString += '<tr>';
                resultString += '<td>'+resText[i].antibiotic_name+"</td>";
                resultString += '<td>'+resText[i].sensitive+"</td>";
                resultString += '<td>'+resText[i].intermediate+"</td>";
                resultString += '<td>'+resText[i].resistant+"</td>";
                resultString += '<td>'+(parseInt(resText[i].resistant) + parseInt(resText[i].sensitive) + parseInt(resText[i].intermediate))+"</td>";
                resultString += '</tr>';
              }
              streptococcus_suis_organism_tbl.rows.add($(resultString)).draw();
            }
          },
          error: function(err1) {
            console.log("10.1) Streptococcus suis: Err");
              console.log(err1.responseText);
          }
        }),
        $.ajax({
          url: base_url + "report/get_antibiotic_organism",
          type: "POST",
          dataType: "json",
          data: {
            start_date:start_date.format("YYYY-MM-DD"),
            start_time: start_time.format("HH:mm"),
            end_date: end_date.format("YYYY-MM-DD"),
            end_time: end_time.format("HH:mm"),
            laboratories: $('#laboratories').val(),
            organism_id: 445,
            sample_id: 8
          },
          success: function(resText) {
            console.log("10.2 Streptococcus pneumoniae: SUCCESS");
            console.log(resText);
            var resultString = "";
            if(resText.length > 0){              
              for(var i in resText){
                resultString += '<tr>';
                resultString += '<td>'+resText[i].antibiotic_name+"</td>";
                resultString += '<td>'+resText[i].sensitive+"</td>";
                resultString += '<td>'+resText[i].intermediate+"</td>";
                resultString += '<td>'+resText[i].resistant+"</td>";
                resultString += '<td>'+(parseInt(resText[i].resistant) + parseInt(resText[i].sensitive) + parseInt(resText[i].intermediate))+"</td>";
                resultString += '</tr>';
              }
              streptococcus_pneumoniae_organism_csf_tbl.rows.add($(resultString)).draw();
            }
          },
          error: function(err1) {
              console.log("10.2) Streptococcus pneumoniae: Err");
                console.log(err1.responseText);
          }
        }),
        $.ajax({
          url: base_url + "report/get_antibiotic_organism",
          type: "POST",
          dataType: "json",
          data: {
            start_date:start_date.format("YYYY-MM-DD"),
            start_time: start_time.format("HH:mm"),
            end_date: end_date.format("YYYY-MM-DD"),
            end_time: end_time.format("HH:mm"),
            laboratories: $('#laboratories').val(),
            organism_id: 451,
            sample_id: 8
          },
          success: function(resText) {
            console.log("10.3) Streptococcus, beta-haem. Group B: SUCCESS");
            console.log(resText);
            var resultString = "";
            if(resText.length > 0){              
              for(var i in resText){
                resultString += '<tr>';
                resultString += '<td>'+resText[i].antibiotic_name+"</td>";
                resultString += '<td>'+resText[i].sensitive+"</td>";
                resultString += '<td>'+resText[i].intermediate+"</td>";
                resultString += '<td>'+resText[i].resistant+"</td>";
                resultString += '<td>'+(parseInt(resText[i].resistant) + parseInt(resText[i].sensitive) + parseInt(resText[i].intermediate))+"</td>";
                resultString += '</tr>';
              }
              streptococcus_organism_tbl.rows.add($(resultString)).draw();
            }
          },
          error: function(err1) {
            console.log("10.3) Streptococcus, beta-haem. Group B: Err");
              console.log(err1.responseText);
          }
        }),
        $.ajax({
          url: base_url + "report/get_antibiotic_organism",
          type: "POST",
          dataType: "json",
          data: {
            start_date:start_date.format("YYYY-MM-DD"),
            start_time: start_time.format("HH:mm"),
            end_date: end_date.format("YYYY-MM-DD"),
            end_time: end_time.format("HH:mm"),
            laboratories: $('#laboratories').val(),
            organism_id: 334,
            sample_id: 8
          },
          success: function(resText) {
            console.log("10.4) Neisseria meningitidis: SUCCESS");
            console.log(resText);
            var resultString = "";
            if(resText.length > 0){              
              for(var i in resText){
                resultString += '<tr>';
                resultString += '<td>'+resText[i].antibiotic_name+"</td>";
                resultString += '<td>'+resText[i].sensitive+"</td>";
                resultString += '<td>'+resText[i].intermediate+"</td>";
                resultString += '<td>'+resText[i].resistant+"</td>";
                resultString += '<td>'+(parseInt(resText[i].resistant) + parseInt(resText[i].sensitive) + parseInt(resText[i].intermediate))+"</td>";
                resultString += '</tr>';
              }
              neisseria_meningitidis_organism_tbl.rows.add($(resultString)).draw();
            }
          },
          error: function(err1) {
            console.log("10.4) Neisseria meningitidis: Err");
              console.log(err1.responseText);
          }
        }),
        
        $.ajax({
          url: base_url + "report/get_rejected_sample",
          type: "POST",
          dataType: "json",
          data: {
            start_date:start_date.format("YYYY-MM-DD"),
            start_time: start_time.format("HH:mm"),
            end_date: end_date.format("YYYY-MM-DD"),
            end_time: end_time.format("HH:mm"),
            laboratories: $('#laboratories').val(),
          },
          success: function(resText) {
            console.log("11) Rejection: SUCCESS");
            console.log(resText.wards);
            console.log(resText.specimen);
            console.log(resText.result);
            var resultString = resText.html_string;
            if(resultString.length == 0){
              resultString = '<tbody><tr><td style="text-align:center;">No data available in table</td></tr></tbody>';
            }
            console.log(resultString);

            $("#rejection_tbl").html(resultString);
            //rejection_tbl.rows.add($(resultString)).draw();
          },
          error: function(err1) {
            console.log("11) Rejection: Err");
            console.log(err1.responseText);
          }
        })
      ).then(function(data1, data2, data3 ,data4, data5,data6, data7, data8, data9 , data10, data11, data12, data13, data14, data15 , data16, data17) {
          setTimeout(function () {
            myDialog.showProgress('hide');
            }, 400);

          for(var i in print_data){
            chart = print_data[i].obj;
            destination =print_data[i].destination;
            if (typeof chart !== 'undefined'){
              var tmp = new AmCharts.AmExport(chart);
              tmp.init();
              tmp.output({
                  output: 'datastring',
                  format: 'jpg'
              },function(blob) {
                  var image = new Image();
                  image.src = blob;
                  $( image ).insertAfter( $( "#report_wrapper_2" ) );
                  $("#"+destination+"_image").html( image );
              });
            }
          }
          // clone table for report
          for (var i in table_id){
            table_html = $("#"+table_id[i]).html();
            $("#"+table_id[i]+"_clone").html(table_html);
          }
      })
  });
  AmCharts.checkEmptyData = function(chart) {
    if (0 === chart.dataProvider.length) {
        // set min/max on the value axis
        chart.valueAxes[0].minimum = 0;
        chart.valueAxes[0].maximum = 100;

        // add dummy data point
        var dataPoint = {
            dummyValue: 0
        };
        dataPoint[chart.categoryField] = '';
        chart.dataProvider = [dataPoint];

        // add label
        chart.addLabel(0, '50%', 'The chart contains no data', 'center');

        // set opacity of the chart div
        chart.chartDiv.style.opacity = 0.5;

        // redraw it
        chart.validateNow();
    }
  };
  function make_gender_chart(data , total){
      var chart = AmCharts.makeChart( "gender_chart", {
        "hideCredits":true,
        "type": "pie",
        "theme": "none",
        "dataProvider": data,
        "valueField": "count",
        "titleField": "gender",
        "colorField": "color", 
        "gridAboveGraphs": true,
        "balloon":{
          "fixedPosition":true
        },
        "allLabels": [{
          "x": 0,
          "y": 25,
          "text": "Total = "+total
        }],        
        "export": {
          "enabled": true
        }
    });
    AmCharts.checkEmptyData(chart);
    return chart;
  }
  function build_age_group_chart(data){
    var chart = AmCharts.makeChart( "age_group_chart", {
        "hideCredits":true,
        "type": "serial",
        "theme": "none",
        "dataProvider": data,
        "valueAxes": [{
            "stackType": "regular",
            "axisAlpha": 0.3,
            "gridAlpha": 0,
            "title": "Count"
        }],
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
            "labelText": "[[value]]",
            "title": "Male",
            "type": "column",
            "color": "#FFFFFF",
            "valueField": "male",
            "fillColors": "#5ec0c5",
            "fillAlphas": 1,
        }, {
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
            "labelText": "[[value]]",            
            "title": "Female",
            "type": "column",
            "color": "#FFFFFF",
            "valueField": "female",            
            "fillColors": "#ea756c",
            "fillAlphas": 1,
        }],
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "age_group",
        "categoryAxis": {
            "gridPosition": "start",
            "axisAlpha": 0,
            "gridAlpha": 0,
            "position": "left",
            "labelRotation": -45,
            "title": "Age (year)"
        },
        "legend": {
          "useGraphSettings": true
        },
        "export": {
            "enabled": true
        }
    });
    AmCharts.checkEmptyData(chart);
    return chart;
  }
function make_bloodstream_pathogen_chart(destination, data, title, value_field){
  var chart = AmCharts.makeChart(destination, {
      "hideCredits" : true,
      "type": "serial",
      "theme": "light",      
      "dataProvider": data,
      "valueAxes": [{
          "stackType": "regular",
          "axisAlpha": 0.5,
          "gridAlpha": 0,
          "title": "Cases "+title,
          "integersOnly" : true
      }],
      "graphs": [{
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",            
          "labelText": "[[value]]",            
          "title": title,
          "type": "column",
          "color": "#FFFFFF",
          "valueField": value_field,
          "fillColorsField" : "color",
          "fillColors": "#ff0000",
          "fillAlphas": 1
      }],
      "rotate": true,
      "categoryField": "organism_name",
      "categoryAxis": {
          "gridPosition": "start",
          "axisAlpha": 0,
          "gridAlpha": 0,
          "position": "left",
          "autoGridCount" : true,
          "boldLabels" : true,
          "titleRotation" : 90,
          "titleFontSize": 8          
      },
      
      "export": {
          "enabled": true
      }
  });
  height = 200;
  len = data.length;
  if (len == 0) height == 200;
  else if (len == 1 ) height = 120;
  else height = (100 + (25*len));
  $("#"+destination).height(height); // auto height
  AmCharts.checkEmptyData(chart);
  return chart;
}
function make_micro_specimen_chart(destination, data, label, color){
  var chart = AmCharts.makeChart(destination, {
    "hideCredits":true,
    "type": "serial",
    "theme": "light",
    "dataProvider": data,
    "graphs": [{
      "fillAlphas": 1,
      "lineAlpha": 0.2,
      "type": "column",
      "valueField": "count",
      "labelText": "[[value]]",
      "title" : label,
      "fillColors" : color
    }],
    "categoryField": "month_year",
    "chartCursor": {
      "fullWidth": true,
      "cursorAlpha": 0.1
    },
    "categoryAxis": {
      "gridPosition": "start",
      "axisAlpha": 0,
      "gridAlpha": 0,
      "position": "left",
      "labelRotation": -45,
      "title": "Mon-Year"
  },
    "valueAxes": [
        {
          "title": label,
          "autoGridCount": false,
          "baseValue": 0,
          "baseCoord":0,
          "integersOnly" : true
        }
    ],
    
    "export": {
      "enabled": true
  }
  });
  AmCharts.checkEmptyData(chart);
  return chart;
}
  
function make_specimen_chart_by_month(destination, data, label){
  var chart = AmCharts.makeChart(destination, {
      "hideCredits" : true,
      "type": "serial",
      "theme": "none",
      
      "dataProvider": data,
      "graphs": [{
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",            
          "labelText": "[[value]]",            
          "title": "",
          "type": "column",
          "color": "#000000",
          "valueField": "value",
          "fillColorsField" : "color",
          "fillColors": "#ff0000",
          "fillAlphas": 1
      }],
      "rotate": true,        
      "categoryField": "sample_name",    
      "valueAxes": [
          {
              "title": label,
              "baseValue" : 0,
              "axisThickness": 1,
              "integersOnly" : true
          }
      ],  
      
      "export": {
          "enabled": true
      }
  });
  height = 200;
  len = data.length;
  if (len == 0) height == 200;
  else if (len == 1 ) height = 120;
  else height = (100 + (25*len));  
  $("#"+destination).height(height); // auto height
  AmCharts.checkEmptyData(chart);
  return chart;
}

  function build_isolated_pathogen_in_csf(data){
    var chart_id = "csf_pathogent_chart";
    var chart = AmCharts.makeChart(chart_id, {
      "hideCredits":true,
      "rotate": true,
      "type": "serial",
      "theme": "light",
      "dataProvider": data,
      "graphs": [{
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "type": "column",
        "valueField": "csf",
        "labelText": "[[value]]",
        "title" : "Blood Culture",
        "fillColorsField" : "color",
        "balloonText": "<b>[[title]]</b><br><span style='font-size:12px;'>[[category]]: <b>[[value]]</b></span>",
      }],
      "categoryField": "organism_name",
      "chartCursor": {
        "fullWidth": true,
        "cursorAlpha": 0.1          
      },
      "categoryAxis": {
        "gridPosition": "start",
        "axisAlpha": 0,
        "gridAlpha": 0,
        "position": "left",
        "labelRotation": -45
      },
      "valueAxes": [
          {
            "title": "Cases",
            "baseValue" : 0,
            "axisThickness": 3,
            "integersOnly" : true
          }
      ],
      
      "export": {
        "enabled": true
      }
    });
    height = 200;
    len = data.length;
    if (len == 0) height == 200;
    else if (len == 1 ) height = 120;
    else height = (100 + (25*len));
    $("#"+chart_id).height(height); // auto height
    AmCharts.checkEmptyData(chart);
    return chart;
  }

  function make_blood_culture_pathogen_chart(data){
    var chart_id = "blood_culture_pathogent_chart";
    var chart = AmCharts.makeChart(chart_id, {
      "hideCredits":true,
      "rotate": true,
      "type": "serial",
      "theme": "light",
      "dataProvider": data,
      "graphs": [{
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "type": "column",
        "valueField": "blood",
        "labelText": "[[value]]",
        "title" : "Blood Culture",
        "fillColorsField" : "color",
        "balloonText": "<b>[[title]]</b><br><span style='font-size:12px;'>[[category]]: <b>[[value]]</b></span>"
      }],
      "categoryField": "organism_name",
      "chartCursor": {
        "fullWidth": true,
        "cursorAlpha": 0.1          
      },
      "categoryAxis": {
        "autoGridCount": true,
        "axisAlpha": 0,
        "gridAlpha": 0,
        "position": "left",
        "labelRotation": -45
      },
      "valueAxes": [
        {
          "title": "Cases",
          "integersOnly" : true 
        }
      ],
      
      "export": {
          "enabled": true
      }
    });    
    height = 200;
    len = data.length;
    if (len == 0) height == 200;
    else if (len == 1 ) height = 120;
    else height = (100 + (25*len));
    
    $("#"+chart_id).height(height); // auto height
    AmCharts.checkEmptyData(chart);
    return chart;
  }

  function build_adult_blood_volumn(destination, data){      
    var chart = AmCharts.makeChart( destination, {
        "hideCredits":true,
        "type": "serial",
        "theme": "light",
        "dataProvider": data,
        "valueAxes": [{
            "title": "Percentage (%)",
            "unit": "%",
        }],        
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]%</b></span>",
            "labelText": "[[value]]",
            "lineAlpha": 0.3,
            "title": "Too low (<8ml)",
            "type": "column",
            "color": "#000000",
            "valueField": "low_percentage",
            "fillColors": "#f1b914",
            "fillAlphas": 1,
        }, {
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]%</b></span>",
            "labelText": "[[value]]",
            "lineAlpha": 0.3,
            "title": "Correct (8-12ml)",
            "type": "column",
            "color": "#000000",
            "valueField": "correct_percentage",
            "fillColors": "#4da318",
            "fillAlphas": 1,
        }, {
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]] %</b></span>",
            "labelText": "[[value]]",
            "lineAlpha": 0.3,
            "title": "Too high (> 12ml)",
            "type": "column",
            "color": "#000000",
            "valueField": "heigh_percentage",
            "fillColors": "#e85b57",
            "fillAlphas": 1,
        }],
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "month_year",
        "categoryAxis": {
            "gridPosition": "start",
            "axisAlpha": 0,
            "gridAlpha": 0,
            "position": "left",
            "labelRotation": -45
        },
        "legend": {
            "useGraphSettings": true
        },
        "export": {
            "enabled": true
        }
    } );
    AmCharts.checkEmptyData(chart);
    return chart;
  }

  function build_blood_volumn_28d(destination, data){      
    var chart = AmCharts.makeChart( destination, {
        "hideCredits":true,
        "type": "serial",
        "theme": "light",
        "dataProvider": data,
        "valueAxes": [{
            "title": "Total volume",
            "integersOnly" : true       
        }],
        
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
            "labelText": "[[value]]",
            "lineAlpha": 0.3,
            "title": "",
            "type": "column",
            "color": "#000000",
            "valueField": "total",
            "fillColors": "#f1b914",
            "fillAlphas": 1,
        }],
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "month_year",
        "categoryAxis": {
            "gridPosition": "start",
            "axisAlpha": 0,
            "gridAlpha": 0,
            "position": "left",
            "labelRotation": -45,
            "title" : "Mon-Year"
        },
        "legend": {
            "useGraphSettings": false
        },
        "export": {
            "enabled": true
        }
    } );
    AmCharts.checkEmptyData(chart);
    return chart;
  }

  function build_blood_volumn_29d1y(destination, data){      
    var chart = AmCharts.makeChart( destination, {
        "hideCredits":true,
        "type": "serial",
        "theme": "light",
        "dataProvider": data,
        "valueAxes": [{
            "title": "Percentage (%)",
            "unit": "%",
        }],        
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]%</b></span>",
            "labelText": "[[value]]",
            "lineAlpha": 0.3,
            "title": "Too low (<0.5ml)",
            "type": "column",
            "color": "#000000",
            "valueField": "low_percentage",
            "fillColors": "#f1b914",
            "fillAlphas": 1,
        }, {
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]%</b></span>",
            "labelText": "[[value]]",
            "lineAlpha": 0.3,
            "title": "Correct (0.5 - < 2ml)",
            "type": "column",
            "color": "#000000",
            "valueField": "correct_percentage",
            "fillColors": "#4da318",
            "fillAlphas": 1,
        }, {
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]] %</b></span>",
            "labelText": "[[value]]",
            "lineAlpha": 0.3,
            "title": "Too high (>= 1ml)",
            "type": "column",
            "color": "#000000",
            "valueField": "heigh_percentage",
            "fillColors": "#e85b57",
            "fillAlphas": 1,
        }],
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "month_year",
        "categoryAxis": {
            "gridPosition": "start",
            "axisAlpha": 0,
            "gridAlpha": 0,
            "position": "left",
            "labelRotation": -45
        },
        "legend": {
            "useGraphSettings": true
        },
        "export": {
            "enabled": true
        }
    } );
    AmCharts.checkEmptyData(chart);
    return chart;
  }

  function build_blood_volumn_1y14y(destination, data){
    var chart = AmCharts.makeChart( destination, {
      "hideCredits":true,
      "type": "serial",
      "theme": "light",
      "dataProvider": data,
      "valueAxes": [{
          "title": "Percentage (%)",
          "unit": "%",
      }],        
      "gridAboveGraphs": true,
      "startDuration": 1,
      "graphs": [{
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]%</b></span>",
          "labelText": "[[value]]",
          "lineAlpha": 0.3,
          "title": "Too low (<1ml)",
          "type": "column",
          "color": "#000000",
          "valueField": "low_percentage",
          "fillColors": "#f1b914",
          "fillAlphas": 1,
      }, {
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]%</b></span>",
          "labelText": "[[value]]",
          "lineAlpha": 0.3,
          "title": "Correct (1ml - < 2ml)",
          "type": "column",
          "color": "#000000",
          "valueField": "correct_percentage",
          "fillColors": "#4da318",
          "fillAlphas": 1,
      }, {
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]] %</b></span>",
          "labelText": "[[value]]",
          "lineAlpha": 0.3,
          "title": "Too high (>= 6ml)",
          "type": "column",
          "color": "#000000",
          "valueField": "heigh_percentage",
          "fillColors": "#e85b57",
          "fillAlphas": 1,
      }],
      "chartCursor": {
          "categoryBalloonEnabled": false,
          "cursorAlpha": 0,
          "zoomable": false
      },
      "categoryField": "month_year",
      "categoryAxis": {
          "gridPosition": "start",
          "axisAlpha": 0,
          "gridAlpha": 0,
          "position": "left",
          "labelRotation": -45
      },
      "legend": {
          "useGraphSettings": true
      },
      "export": {
          "enabled": true
      }
    } );
    AmCharts.checkEmptyData(chart);
    return chart;
  }
  
  function build_total_blood_volumn(destination, data){
    var chart = AmCharts.makeChart( destination, {
      "hideCredits":true,
      "type": "serial",
      "theme": "light",
      "dataProvider": data,
      "valueAxes": [{
          "title": "Percentage (%)",
          "unit": "%",
      }],        
      "gridAboveGraphs": true,
      "startDuration": 1,
      "graphs": [{
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]%</b></span>",
          "labelText": "[[value]]",
          "lineAlpha": 0.3,
          "title": "Too low",
          "type": "column",
          "color": "#000000",
          "valueField": "low_percentage",
          "fillColors": "#f1b914",
          "fillAlphas": 1,
      }, {
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]%</b></span>",
          "labelText": "[[value]]",
          "lineAlpha": 0.3,
          "title": "Correct",
          "type": "column",
          "color": "#000000",
          "valueField": "correct_percentage",
          "fillColors": "#4da318",
          "fillAlphas": 1,
      }, {
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]] %</b></span>",
          "labelText": "[[value]]",
          "lineAlpha": 0.3,
          "title": "Too high",
          "type": "column",
          "color": "#000000",
          "valueField": "heigh_percentage",
          "fillColors": "#e85b57",
          "fillAlphas": 1,
      }],
      "chartCursor": {
          "categoryBalloonEnabled": false,
          "cursorAlpha": 0,
          "zoomable": false
      },
      "categoryField": "age_group",
      "categoryAxis": {
          "gridPosition": "start",
          "axisAlpha": 0,
          "gridAlpha": 0,
          "position": "left",
          "labelRotation": -45
      },
      "legend": {
          "useGraphSettings": true
      },
      "export": {
          "enabled": true
      }
    } );
    AmCharts.checkEmptyData(chart);
    return chart;
  }
  function make_bps_chart(data){    
    var graph_option = [];
    var include_blood_culture = false;
    var include_body_fluid = false;
    var include_csf = false;
    var include_pus = false;
    var include_sputum = false;
    var include_swab = false;
    var include_swab_genital = false;
    var include_throat_swab = false;
    var include_tissue = false;
    var include_urine = false;
    for(var i in data){
      if(parseInt(data[i].blood_culture) > 0  && include_blood_culture == false){        
        graph_option.push({
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
          "labelText": "[[value]]",
          "title": "Blood Culture",
          "type": "column",
          "color": "#000000",
          "valueField": "blood_culture",
          "fillColors": "#ea756c",
          "fillAlphas": 1
        });
        include_blood_culture = true;
      }

      if(parseInt(data[i].body_fluid) > 0  && include_body_fluid == false){
        graph_option.push({
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
          "labelText": "[[value]]",            
          "title": "Body Fluid",
          "type": "column",
          "color": "#000000",
          "valueField": "body_fluid",   
          "fillColors": "#c87bff",
          "fillAlphas": 1         
        });
        include_body_fluid = true;
      }

      if(parseInt(data[i].csf) > 0  && include_csf == false){
        graph_option.push({
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",          
          "labelText": "[[value]]",
          "title": "CSF",
          "type": "column",
          "color": "#000000",
          "valueField": "csf",
          "fillColors": "#625209",
          "fillAlphas": 1
        });
        include_csf = true;
      }
      if(parseInt(data[i].pus) > 0  && include_pus == false){
        graph_option.push({
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",          
          "labelText": "[[value]]",
          "title": "Pus",
          "type": "column",
          "color": "#000000",
          "valueField": "pus",
          "fillColors": "#d1b087",
          "fillAlphas": 1,
        });
        include_pus = true;
      }
      if(parseInt(data[i].sputum) > 0  && include_sputum == false){
        graph_option.push({
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",          
          "labelText": "[[value]]",
          "title": "Sputum",
          "type": "column",
          "color": "#000000",
          "valueField": "sputum",
          "fillColors": "#939085",
          "fillAlphas": 1
        });
        include_sputum = true;
      }
      if(parseInt(data[i].swab) > 0  && include_swab == false){
        graph_option.push({
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",          
          "labelText": "[[value]]",
          "title": "Swab",
          "type": "column",
          "color": "#000000",
          "valueField": "swab",
          "fillColors": "#2907e5",
          "fillAlphas": 1
        });
        include_swab = true;
      }
      if(parseInt(data[i].swab_genital) > 0  && include_swab_genital == false){
        graph_option.push({
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",          
          "labelText": "[[value]]",
          "title": "Swab Genital",
          "type": "column",
          "color": "#000000",
          "valueField": "swab_genital",
          "fillColors": "#f0ff08",
          "fillAlphas": 1
        });
        include_swab_genital = true;
      }
      if(parseInt(data[i].throat_swab) > 0  && include_throat_swab == false){
        graph_option.push({
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",          
          "labelText": "[[value]]",
          "title": "Throat swab",
          "type": "column",
          "color": "#000000",
          "valueField": "throat_swab",
          "fillColors": "#518d6e",
          "fillAlphas": 1
        });
        include_throat_swab = true;
      }
      if(parseInt(data[i].tissue) > 0  && include_tissue == false){
        graph_option.push({
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",          
          "labelText": "[[value]]",
          "title": "Tissue",
          "type": "column",
          "color": "#000000",
          "valueField": "tissue",
          "fillColors": "#343c38",
          "fillAlphas": 1
        });
        include_tissue = true;
      }
      if(parseInt(data[i].urine) > 0  && include_urine == false){
        graph_option.push({
          "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",          
          "labelText": "[[value]]",
          "title": "Urine",
          "type": "column",
          "color": "#000000",
          "valueField": "urine",
          "fillColors": "#f4c90a",
          "fillAlphas": 1
        });
        include_urine = true;
      }

    }
   
    var bps_chart = AmCharts.makeChart("bps_chart", {
      "hideCredits":true,
      "type": "serial",
      "theme": "none",
      "dataProvider": data,
      "valueAxes": [{
          "stackType": "regular",
          "axisAlpha": 0.3,
          "gridAlpha": 0,
          "title": "Bps cases"
      }],
      "gridAboveGraphs": true,
      "startDuration": 1,
      "graphs": graph_option,
      "chartCursor": {
          "categoryBalloonEnabled": false,
          "cursorAlpha": 0,
          "zoomable": false
      },
      "categoryField": "month_year",
      "categoryAxis": {
          "gridPosition": "start",
          "axisAlpha": 0,
          "gridAlpha": 0,
          "position": "left",
          "labelRotation": -45,
          "title": "Month"
      },
      "legend": {
        "useGraphSettings": true
      },
      "export": {
          "enabled": true
      }
    });
    AmCharts.checkEmptyData(bps_chart);
    return bps_chart;
  }   
   function make_salmonella_chart(data){   
    var chart = AmCharts.makeChart("salmonella_chart", {
      "hideCredits":true,
        "type": "serial",
        "theme": "light",
        "dataProvider": data,
        "graphs": [{
          "fillAlphas": 0.9,
          "lineAlpha": 0.2,
          "type": "column",
          "labelText": "[[value]]",
          "valueField": "total"
        }],
        "categoryField": "month_year",
        "chartCursor": {
          "fullWidth": true,
          "cursorAlpha": 0.1          
        },
        "valueAxes": [
            {
              "title": "Salmonella cases"
            }
        ],
        "legend": {
          "useGraphSettings": true
        },
        "export": {
          "enabled": true
      }
    });
    AmCharts.checkEmptyData(chart);
    return chart;
   }
   

  function scrollTo(id_ele){
    $('html, body').animate({
        scrollTop: $("#"+id_ele).offset().top
    }, 2000);
  }

  function dateRange(startDate, endDate) {
    var start      = startDate.split('-');
    var end        = endDate.split('-');
    var startYear  = parseInt(start[0]);
    var endYear    = parseInt(end[0]);
    var dates      = [];
  
    for(var i = startYear; i <= endYear; i++) {
      var endMonth = i != endYear ? 11 : parseInt(end[1]) - 1;
      var startMon = i === startYear ? parseInt(start[1])-1 : 0;
      for(var j = startMon; j <= endMonth; j = j > 12 ? j % 12 || 11 : j+1) {
        var month = j+1;
        var displayMonth = month < 10 ? '0'+month : month;
        dates.push([i, displayMonth, '01'].join('-'));
      }
    }
    return dates;
  }  
  $preview_modal.find("#doPrinting").off("click").on("click", function (evt) {
    evt.preventDefault();
    var psample_id = $preview_modal.find(".modal-dialog .psample-result.active").data("patient_sample_id");
    printpage(base_url + "result/patient_sample_result/print/" + psample_id);
});

$("#btnSave").on("click", function(evt) {
    evt.preventDefault();
    //$preview_modal.find(".modal-dialog").html("hello");
    //$preview_modal.modal("show");
    exportHTML();



  });
});

function exportHTML(){
  var css = '<style>@page { size: 7in 9.25in; margin: 27mm 16mm 27mm 16mm; background:yellow;} table.tbl_pathogen,table.tbl_pathogen_by_month{width:100%}table.tbl_pathogen thead th,table.tbl_pathogen_by_month thead th{border-bottom:1px solid #efefef;font-weight:700;}table.tbl_pathogen thead th:nth-child(0),table.tbl_pathogen_by_month thead th:nth-child(0){text-align:left}table.tbl_pathogen tbody td:nth-child(0){text-align:left !important;}table.tbl_pathogen tbody td{border-bottom:1px solid #efefef;width:60px;height:25px;text-align:center}table.tbl_pathogen td:nth-child(2){color:#8a2be2}table.tbl_pathogen td:nth-child(4){color:"orange"}table.tbl_pathogen td:nth-child(1){width:80%;text-align:left!important}table.tbl_pathogen_by_month tbody td{;border-bottom:1px solid #efefef}table.tbl_pathogen_by_month tbody td:nth-child(1){text-align:left!important} ol.toc , ol.header{ counter-reset: section; list-style-type: none; list-style-position: outside; } ol.toc>li:before , ol.header>li:before { counter-increment: section; content: counters(section, ".") " "; } ol.toc>li>a{ display:inline-block !important; padding: 3px 5px !important; } ol.header>li>h4,ol.header>li>h5{ display:inline-block !important; padding: 3px 5px !important; }';
  css += 'div.parent{background-color: "#efefef"; width: 500px !important;}';
  css += '</style>';
  var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' "+
       "xmlns:w='urn:schemas-microsoft-com:office:word' "+
       "xmlns='http://www.w3.org/TR/REC-html40'>"+
       "<head><meta charset='utf-8'><title>Microbioloy Laboratory Report</title>"+css+"</head><body>";
  
  var footer = "</body></html>";
  var sourceHTML = header+document.getElementById("report_wrapper_2").innerHTML+footer;
  
  var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
  var fileDownload = document.createElement("a");
  document.body.appendChild(fileDownload);
  fileDownload.href = source;
  var currentdate = new Date(); 
  var datetime = currentdate.getDate() + ""          
                + ("0" + (currentdate.getMonth() + 1)).slice(-2)+
                + currentdate.getFullYear() +  
                + currentdate.getHours() +  
                + currentdate.getMinutes();
  fileDownload.download = 'microbiology_report_'+datetime+'.doc';
  fileDownload.click();
  document.body.removeChild(fileDownload);
}

function export_img(chart , destination){
  var tmp = new AmCharts.AmExport(chart);
  tmp.init();
  tmp.output({
      output: 'datastring',
      format: 'jpg'
  },function(blob) {
      var image = new Image(500,350);
      image.src = blob;      
      $("#"+destination).html(image);
  });
}