$(function() {
	var tbl_requester = $("#tbl-requester").DataTable({
		"info"       : false,
		"processing" : true,
		"serverSide" : true,
		/*"scrollY"    : "200px",
		"scrollCollapse": true,*/
		"language"   : dataTableOption.language,
		"ajax"       : {
			"url"    : base_url + 'requester/view_lab_requester',
			"type"   : 'POST'
		},
		"columns"    : [
			{ "data" : "number" },
			{ "data" : "requester_name" },
			{ "data" : "gender" },
			{ "data" : "sample_source_text" },
			{ "data" : "action" }
		],
		"columnDefs": [
            { "targets": -1, "orderable": false, "searchable": false, "className" : "text-middle no-wrap", "width" : "60px" },
            { "targets": '_all', "className" : "text-middle" },
			{ "targets": -2, "orderable": false, "searchable": false }
		],
		"order": [[0, 'asc']],
        "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
        "pageLength": 20
	});

	//Set iCheck Style
	$('input').iCheck({
		checkboxClass: 'icheckbox_minimal',
		radioClass: 'iradio_minimal'
	});

	//init select2
	$("#modal-requester #sample-source").select2();

	//show modal New Requester
	$("#addNew").on("click", function(evt) {
		evt.preventDefault();

		$("#modal-requester").removeData("requester_id");
		$("#modal-requester").modal({backdrop:"static"});
	});

	//clear modal on close 
	$("#modal-requester").on("hidden.bs.modal", function() {
		$("#modal-requester #requester-name").val("");
		$("#modal-requester input[name=gender][value=1]").iCheck("check");
		$("#modal-requester #sample-source").val(null).trigger("change");
		$(this).removeData("requester_id");
	});

	//Add/Update Requester
	$("#modal-requester").on("click", "#btnSave", function(evt) {
		evt.preventDefault();
		var _data = {
			requester_name : $("#modal-requester #requester-name").val().trim(),
			gender : $("#modal-requester input[name=gender]:checked").attr("value"),
			sample_source : $("#modal-requester #sample-source").val()
		};

		if (_data.requester_name.length == 0) {
			myDialog.showDialog('show', {text:msg_required_data, style : 'warning'});
			return false;
		}

		var url  = base_url + "requester/save";
		var requester_id = $("#modal-requester").data("requester_id");
		if (requester_id != undefined && Number(requester_id) > 0) {
			_data.requester_id = requester_id;
			url  = base_url + "requester/update";
		}
		console.log(_data);
		
		$.ajax({
			url      : url,
			type     : 'POST',
			data     : _data,
			dataType : 'json',
			success  : function(resText) {
				myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
				if (resText.status > 0) {
					tbl_requester.ajax.reload();
					$("#modal-requester").modal("hide");
				}
			},
			error : function () {
				myDialog.showDialog('show', {text: requester_id > 0 ? msg_update_fail : msg_save_fail, style : 'warning'});
			}
		})
	});

	// show edit modal
	$("#tbl-requester").on("click", "a.edit", function(evt) {
		evt.preventDefault();
		$(this).blur();

		var data = tbl_requester.row($(this).parents("tr")).data();
		$("#modal-requester").find("#requester-name").val(data.requester_name);
		$("#modal-requester").find("input[name=gender][value=" + data.gender_code + "]").iCheck("check");
		$("#modal-requester").find("select#sample-source").val(data.sample_sources).trigger("change");
		$("#modal-requester").data("requester_id", data.requester_id);
		$("#modal-requester").modal({ backdrop : "static" });
	});

	// remove requester
	$("#tbl-requester").on("click", "a.remove", function(evt) {
		evt.preventDefault();
		$(this).blur();

		var data = tbl_requester.row($(this).parents("tr")).data();

		if (!data.requester_id || data.requester_id <= 0) {
			myDialog.showDialog('show', {text: msg_delete_fail, style : 'warning'});
			return false;
		}

		if (confirm(q_delete_requester)) {
			$.ajax({
				url      : base_url + "requester/delete",
				type     : 'POST',
				data     : { requester_id : data.requester_id },
				dataType : 'json',
				success  : function(resText) {
					if (resText.status == true) {
						myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
						if (resText.status > 0) {
							tbl_requester.ajax.reload();
							$("#modal-requester").modal("hide");
						}
					}
				},
				error : function () {
					myDialog.showDialog('show', {text: msg_delete_fail, style : 'warning'});
				}
			});
		}
	});
});