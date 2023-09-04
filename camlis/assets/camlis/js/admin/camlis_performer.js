$(function() {
	var tb_performer = $("#tb_performer").DataTable({
		"info"       : false,
		"processing" : true,
		"serverSide" : true,
		/*"scrollY"    : "200px",
		"scrollCollapse": true,*/
		"language"   : dataTableOption.language,
		"ajax"       : {
			"url"    : base_url+'performer/view_lab_performer',
			"type"   : 'POST'
		},
		"columns"    : [
			{ "data" : "number" },
			{ "data" : "performer_name" },
			{ "data" : "gender" },
			{ "data" : "action" }
		],
		"columnDefs": [
            { "targets": -1, "orderable": false, "searchable": false, "className" : "text-middle no-wrap", "width" : "60px" },
            { "targets": '_all', "className" : "text-middle" },
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

	//show modal New Performer
	$("#addNew").on("click", function(evt) {
		evt.preventDefault();

		$("#modal_performer").removeData("performer_id");
		$("#modal_performer").modal({backdrop:"static"});
	});

	//clear modal on close 
	$("#modal_performer").on("hidden.bs.modal", function() {
		$("#modal_performer #performer_name").val("");
		$("label[for=performer_name]").removeAttr("data-hint");
		$("#modal_performer input[name=gender][value=1]").iCheck("check");
		$(this).removeData("performer_id");
	});

	//Add/Update Performer
	$("#modal_performer").on("click", "#btnSave", function(evt) {
		evt.preventDefault();
		var _data = {
			performer_name : $("#modal_performer #performer_name").val().trim(),
			gender : $("#modal_performer input[name=gender]:checked").attr("value")
		};

		if (_data.performer_name.length == 0) {
			myDialog.showDialog('show', {text:msg_required_data, style : 'warning'});
			return false;
		}

		var url  = base_url + "performer/save";
		var performer_id = $("#modal_performer").data("performer_id");
		if (performer_id != undefined && Number(performer_id) > 0) {
			_data.performer_id = performer_id;
			url  = base_url + "performer/update";
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
					tb_performer.ajax.reload();
					$("#modal_performer").modal("hide");
				}
			},
			error : function () {
				myDialog.showDialog('show', {text: performer_id > 0 ? msg_update_fail : msg_save_fail, style : 'warning'});
			}
		})
	});

	// show edit modal
	$("#tb_performer").on("click", "a.edit", function(evt) {
		evt.preventDefault();
		$(this).blur();

		var data = tb_performer.row($(this).parents("tr")).data();
		$("#modal_performer").find("#performer_name").val(data.performer_name);
		$("#modal_performer").find("input[name=gender][value=" + data.gender_code + "]").iCheck("check");
		$("#modal_performer").data("performer_id", data.performer_id);
		$("#modal_performer").modal({ backdrop : "static" });
	});

	// remove performer
	$("#tb_performer").on("click", "a.remove", function(evt) {
		evt.preventDefault();
		$(this).blur();

		var data = tb_performer.row($(this).parents("tr")).data();

		if (!data.performer_id || data.performer_id <= 0) {
			myDialog.showDialog('show', {text: msg_delete_fail, style : 'warning'});
			return false;
		}

		if (confirm(q_delete_performer)) {
			$.ajax({
				url      : base_url + "performer/delete",
				type     : 'POST',
				data     : { performer_id : data.performer_id },
				dataType : 'json',
				success  : function(resText) {
					if (resText.status == true) {
						myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
						if (resText.status > 0) {
							tb_performer.ajax.reload();
							$("#modal_performer").modal("hide");
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