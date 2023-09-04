$(function() {
	var tb_stest_organism = $("#tb_stest_organism").DataTable({
		"info"			: false,
		"processing"	: true,
		"serverSide"	: true,
		"ajax"			: {
			"url"	: base_url+'organism/view_stest_organism',
			"type"	: 'POST'
		},
		"language"		: dataTableOption.language,
		"columns"		: [
			{
				"class"			: "details-control",
				"orderable"		: false,
				"data"			: null,
				"defaultContent": ""
			},
			{ "data" : "number" },
			{ "data" : "department_name" },
			{ "data" : "sample_name" },
			{ "data" : "test_name" },
			{ "data" : "organism_count" },
			{ "data" : "antibiotic_count" },
			{ "data" : "stest_id" }
		],
		"order": [[1, 'asc']],
        "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
        "pageLength": 20
	});
	
	// Add event listener for opening and closing details
	$("#tb_stest_organism").on('click', 'td.details-control', function () {
		var tr = $(this).closest('tr');
		var row = tb_stest_organism.row( tr );
 
		if ( row.child.isShown() ) {
			// This row is already open - close it
			row.child.hide();
			tr.removeClass('shown');
		}
		else {
			// Open this row
			//row.child( detailsFormat(row.data()) ).show();
			detailsFormat(row);
			tr.addClass('shown');
		}
	});
	
	$("#addNew").on("click", function(evt) {
		evt.preventDefault();
		
		$("#modal_assign_organism").modal({backdrop : "static"});
	});
	
	//Get Sample base on Department
	$("#department").on("change", function(evt, lab_sampleID) {
		evt.preventDefault();
		
		$.ajax({
			url      : base_url + 'sample/get_lab_sample',
			type     : 'POST',
			data     : { department : $(this).val() },
			dataType : 'json',
			success  : function(resText) {
				$sampleType = $("#modal_assign_organism").find("select#sample");
				$sampleType.find("option").not("option[value=-1]").remove();
				
				for (var i in resText) {
					var selected = "";
					if (lab_sampleID != undefined && lab_sampleID == resText[i].lab_sample_id) {
						selected = "selected";    
					}
					
					$opt = $("<option value='" + resText[i].lab_sample_id + "'"+selected+">" + resText[i].sample_name + "</option>");
					$sampleType.append($opt);
				}
			},
			error : function(resText) {
				
			}
		});
	});
	
	//Get Test by Sample
	$("#sample").on("change", function(evt, lab_sample_id, lab_stest_id) {
		evt.preventDefault();
		
		$.ajax({
			url      : base_url + 'test/get_all_stest',
			type     : 'POST',
			data     : { sample_id : lab_sample_id == undefined ? $(this).val() : lab_sample_id, field_type : [1, 2] },
			dataType : 'json',
			success  : function(resText) {
				$test = $("#modal_assign_organism").find("select#test");
				$test.find("option").not("option[value=-1]").remove();
				
				for (var i in resText) {
					var selected = "";
					if (lab_stest_id != undefined && lab_stest_id == resText[i].lab_stest_id) {
						selected = "selected";    
					}
					
					$opt = $("<option value='" + resText[i].lab_stest_id + "'"+selected+">" + resText[i].testName + "</option>");
					$test.append($opt);
				}
			},
			error : function(resText) {
				
			}
		});
	});
	
	//Get Organism
	$.ajax({
		url      : base_url + 'organism/get_std_organism',
		type     : 'POST',
		dataType : 'json',
		success  : function(resText) {
			$("#tb_organism").data("std_organism", resText);
		}
	});
	
	//Get Antibiotic
	$.ajax({
		url      : base_url + 'antibiotic/get_std_antibiotic',
		type     : 'POST',
		dataType : 'json',
		success  : function(resText) {
			$("#tb_antibiotic").data("std_antibiotic", resText);
		}
	});
	
	//New Organism row
	$("#new_organism").on("click", function(evt, data) {
		evt.preventDefault();    
		
		//All Organism
		var organism        = $("#tb_organism").data("std_organism");
		var organism_option = "";
		for(var i in organism) {
			var selected = "";
			if (data != undefined) {
				if (data.organism_id == organism[i].ID) {
					selected = "selected";
				}    
			}
			
			organism_option += "<option value='"+organism[i].ID+"'"+selected+">"+organism[i].organism_name+"</option>";
		}
		
		var numRow = $("#tb_organism tbody tr").length;
		$tr = $("<tr> \
					<td style='vertical-align:middle;'>" + (numRow + 1) + "</td> \
					<td> \
						<select name='organism' class='organism form-control'> \
							<option value='-1'></option>"+organism_option+" \
						</select> \
					</td> \
					<td style='width:50px; vertical-align:middle;' class='text-center'><a href='#' onclick='return removeRow(this);'><i class='fa fa-trash-o text-red'></i></a></td> \
				 </tr>");
		
		//Set DB data
		if (data != undefined) {
			$tr.data("stest_organism_id", data.stest_organism_id);
			
			var tmp = [];
			for (var i in data.antibiotic) {
				tmp.push(data.antibiotic[i].antibiotic_id);    
			}
			$tr.data("antibiotic", tmp);
		}
		
		//Add Assgin antibiotic to data attr.
		if ($("#tb_organism").data("prev_tr") != undefined) {
			var antibiotic = getAssignAntibiotic();
			$("#tb_organism").data("prev_tr").data("antibiotic", antibiotic);
			$("#tb_organism").removeData("prev_tr");
			$("#tb_antibiotic tbody").empty();
		}
		
		$("#tb_organism").data("prev_tr", $tr);
		$tr.css("cursor", "pointer");

		$tr.on("click", function(evt) {
			var prev_tr = $("#tb_organism").data("prev_tr"); 
			
			var target = evt.target.tagName.toLowerCase();
			if (target == "select" || target == "option") {
				$(this).addClass("selected");
				$(this).siblings("tr").removeClass("selected"); 
				return false;
			}

			//set selected background
			$(this).toggleClass("selected");
			$(this).siblings("tr").removeClass("selected"); 
			
			if (prev_tr != undefined) {
				var antibiotic = getAssignAntibiotic();
				prev_tr.data("antibiotic", antibiotic);
				$("#tb_organism").removeData("prev_tr");
				prev_tr = undefined;
				$("#tb_antibiotic tbody").empty();
			}

			//get antibiotic
			if ($(this).hasClass("selected")) {
				var antibiotic = $(this).data("antibiotic"); //last selected antibiotic
				$("#tb_organism").data("prev_tr", $(this));
				$("#tb_antibiotic tbody").empty();
				for(var i in antibiotic) {
					$("#new_antibiotic").trigger("click", [ antibiotic[i] ]);
				}

			} else {
				$("#tb_antibiotic tbody").empty();
			}
		});
		$("#tb_organism tbody").append($tr);
		$tr.addClass("selected");
		$tr.siblings("tr").removeClass("selected");   
	});
	
	//New antibiotic row
	$("#new_antibiotic").on("click", function(evt, prev_antibiotic) {
		evt.preventDefault();    
		if ($("#tb_organism tbody tr.selected").length == 0) {
			alert("Please select any organism first!");
			return false;
		}
		
		var antibiotic        = $("#tb_antibiotic").data("std_antibiotic");
		var antibiotic_option = "";
		for(var i in antibiotic) {
			antibiotic_option += "<option value='"+antibiotic[i].ID+"'>"+antibiotic[i].antibiotic_name+"</option>";
		}
		
		var numRow = $("#tb_antibiotic tbody tr").length;
		$tr = $("<tr> \
					<td style='ertical-align:middle;'>" + (numRow + 1) + "</td> \
					<td> \
						<select name='antibiotic' class='antibiotic form-control'> \
							<option value='-1'></option>"+antibiotic_option+" \
						</select> \
					</td> \
					<td style='width:50px; vertical-align:middle;' class='text-center'><a href='#' onclick='return removeRow(this);'><i class='fa fa-trash-o text-red'></i></a></td> \
				 </tr>");
		$tr.find("select.antibiotic").val(prev_antibiotic);
		$("#tb_antibiotic tbody").append($tr);
	});
	
	//Clear All Form
	$("#modal_assign_organism").on("hidden.bs.modal", function(evt) {
		$(this).find("#department").val(-1);
		$(this).find("select#sample option, select#test option").not("option[value=-1]").remove();  
		$("#tb_organism tbody").empty();
		$("#tb_antibiotic tbody").empty();
		tb_stest_organism.ajax.reload();
	});
	
	//Save Organism/Antibiotic
	$("#btnSave").on("click", function(evt) {
		evt.preventDefault();
		
		//Validation
		var hasError = 0;
		if ($("#department").val() <= 0) {
			hasError += 1;
			$("#label_department").attr("data-hint", "Please choose Department!");
		} else {
			$("#label_department").removeAttr("data-hint");
		}
		
		if ($("#sample").val() <= 0) {
			hasError += 1;
			$("#label_sample").attr("data-hint", "Please choose Sample!");
		} else {
			$("#label_sample").removeAttr("data-hint");
		}
		
		if ($("#test").val() <= 0) {
			hasError += 1;
			$("#label_test").attr("data-hint", "Please choose Test!");
		} else {
			$("#label_test").removeAttr("data-hint");
		}
		
		if (hasError > 0) {
			myDialog.showDialog('show', {text:'Please fill all required data!', status : 'Warning! ', style : 'warning'});
			return false;
		}
		
		if ($("#tb_organism tbody").find("tr").length == 0) {
			myDialog.showDialog('show', {text:'Please add at least one Organism!', status : 'Warning! ', style : 'warning'});
			return false;
		}
		
		//Assgin current antibiotic to selected organism
		var antibiotic = getAssignAntibiotic();
		if ($("#tb_organism").data("prev_tr") != undefined)
			$("#tb_organism").data("prev_tr").data("antibiotic", antibiotic);
		
		//Get Test 
		var stest_id    = $("#test").val();
		var organism    = [];
		$("#tb_organism tbody").find("tr").each(function() {    
			var organism_id = $(this).find("select.organism").val(); 
			if (organism_id > 0) {
				organism.push({
					stest_organism_id   : $(this).data("stest_organism_id"),
					organism_id         : organism_id,
					antibiotic          : $(this).data("antibiotic")
				});    
			}
		});
		
		//save data
		$.ajax({
			url      : base_url + "organism/save_stest_organism",
			type     : 'POST',
			data     : { stest_id : stest_id, organism : organism},
			dataType : 'json',
			success  : function(resText) {
				myDialog.showDialog('show', {
					text    : 'Data has been saved!', 
					status  : 'Success! ', 
					style   : 'success',
					onHidden  : function() {
						 $("#modal_assign_organism").modal("hide");
					}
				});            
			}
		});
	});
	
	//Edit Sample
	$("#tb_stest_organism").on("click", ".edit_stest_organism", function(evt) {
		evt.preventDefault();
		$(this).blur();
		
		var data = tb_stest_organism.row($(this).parents("tr")).data().DT_RowData;
		$("#department").val(data.lab_department_id);
		$("#department").trigger("change", [data.lab_sample_id]);
		$("#sample").trigger("change", [data.lab_sample_id, data.lab_stest_id]);
		
		$.ajax({
			url      : base_url + "organism/get_organism_antibiotic",
			type     : 'POST',
			data     : { stest_id : $(this).attr("value") },
			dataType : 'json',
			success  : function(resText) {
				for(var i in resText) {
					$("#new_organism").trigger("click", [ resText[i] ]);
					
					for(var j in resText[i].antibiotic) {
						$("#new_antibiotic").trigger("click", [ resText[i].antibiotic[j].antibiotic_id ]);
					}
				}            
			}
		});
		
		$("#modal_assign_organism").modal({backdrop : "static"});
	});
});

//Get Assign Antibiotic
function getAssignAntibiotic() {
	$list           = $("#tb_antibiotic tbody");
	var selected    = [];
	
	$list.find("tr").each(function() {
		var antibiotic_id  = $(this).find("select.antibiotic").val();
		selected.push(antibiotic_id);
	});
	
	return selected;
}

function removeRow(THIS) {
	var tbody =  $(THIS).parents("tbody");
	$(THIS).parents("tr").remove(); 
	
	tbody.find("tr").each(function(index) {
		$(this).find("td:eq(0)").html(index + 1);    
	});
	
	if ($(THIS).parents("tr").hasClass('selected'))
		$("#tb_antibiotic tbody").empty();
	
	return false;
}

function detailsFormat(row) {
	row.child("<center><i class='fa fa-refresh fa-spin' style='color:dodgerblue;'></i></center>").show();
	$.ajax({
		url      : base_url + "organism/get_organism_antibiotic",
		type     : 'POST',
		data     : { stest_id : row.data().DT_RowData.lab_stest_id },
		dataType : 'json',
		success  : function(resText) {     
			var list = "<div class='tree'><ul>";
			for(var i in resText) {
				list += "<li><label class='control-label'>" + resText[i].organism_name + "</label>";
				
				if (resText[i].antibiotic.length > 0) {
					list += "<ul>";
					for (var j in resText[i].antibiotic) {
						list += "<li><label class='control-label'>"+resText[i].antibiotic[j].antibiotic_name+"</label></li>";        
					}
					list += "</ul>";
				}
				
				list += "</li>";
			}
			
			list += "</ul></div>";
			
			var items = $(list);
			items.treeview({animated : true});
			
			row.child(items).show();
		}
	});
}