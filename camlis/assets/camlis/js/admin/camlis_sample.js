$(function() {
	var tb_sample = $("#tb_sample").DataTable({
		"info"		: false,
		"processing": true,
		"serverSide": true,
		"ajax"		: {
			"url"	: base_url+'sample/view_lab_sample',
			"type"	: 'POST'
		},
		"language"	: dataTableOption.language,
		"order"		: [[1, 'asc'], [2, 'asc']]
	});

	//Get Standard Sample in all Department
	var lab_sample = null;
	$.ajax({
		url		: base_url + "sample/get_lab_sample",
		type	: "POST",
		dataType: 'json',
		success	: function(resText) {
			lab_sample = resText;
		}
	});

	$("#addNew").on("click", function() {
		$.ajax({
			url		: base_url + 'sample/get_lab_department_sample',
			type	: 'POST',
			dataType: 'json',
			success	: function(resText) {
				//current sample from datatable
				/*var rows    = tb_sample.rows().data();
				var rowData = [];
				$.each(rows, function(i, row) {
					rowData.push(row.DT_RowData.lab_department_id + "_" + row.DT_RowData.sample_id);
				});*/

				var rowData	= [];
				for (var i in lab_sample) {
					rowData.push(lab_sample[i].lab_department_id + "_" + lab_sample[i].sample_id);
				}


				var content = $("#std_sample").find(".modal-body");
				content.css("padding-left", "0");

				var list = "";
				for (var i in resText) {
					var style = list.length > 0 ? "style='margin-top:30px;'" : "";
					var group = "<div class='form-group'> \
<div class='col-sm-12 group-header' "+style+"><b>" + resText[i].department_name + "</b></div>";

					if (resText[i].sample != undefined && resText[i].sample.length > 0) {
						var samples				= resText[i].sample;
						var lab_department_id	= resText[i].lab_department_id;

						for (var i in samples) {
							var checked = "";
							if (rowData.indexOf(lab_department_id + "_" + samples[i].sample_id) > -1) {
								checked = "checked";
							}

							group += "<div class='col-sm-3'> \
<label class='control-label' style='cursor:pointer;'><input type='checkbox' parent-value='" + lab_department_id + "' value='" + samples[i].sample_id + "'" + checked + ">&nbsp;&nbsp;" + samples[i].sample_name + "</label> \
</div>"; 
						}
					} else {
						group += "<div class='col-sm-12'>No Sample</div>";
					}
					group += "</div><div style='clear:both;'></div>";

					list += group;
				}

				content.html(list != "" ? list : "<center><h4>No Data! Please select one or more department!</h4></center>")
					.find('input').iCheck({
					checkboxClass: 'icheckbox_minimal',
					radioClass: 'iradio_minimal'
				});

				$("#std_sample").modal({backdrop : 'static'});
			}
		});    
	});

	//Add Department
	$("#btnAddSample").on("click", function(evt) {
		var content = $("#std_sample").find(".modal-body");
		var selected = [];
		content.find("input:checked").each(function() {
			selected.push({
				'lab_department_id' : $(this).attr('parent-value'),
				'sample_id'         : $(this).val()
			});            
		});

		$.ajax({
			url      : base_url + 'sample/add_lab_sample',
			type     : 'POST',
			data     : { samples : selected },
			dataType : 'json',
			success  : function(resText) {
				$("#std_sample").modal("hide");
				tb_sample.ajax.reload();
			}
		}); 
	});

	//Add Comment
	var tb_cmt_list = null;
	$("#tb_sample").on("click", '.add_cmt', function(evt) {
		evt.preventDefault();
		$(this).blur();

		var id      = $(this).attr("data-id");
		tb_cmt_list = $("#tb_cmt_list").DataTable({
			"destroy"    : true,
			"autoWidth"  : false,
			"info"       : false,
			"processing" : true,
			"serverSide" : true,
			"ajax"       : {
				"url"    : base_url+'sample/view_lab_sample_comment',
				"type"   : 'POST',
				"data"   : function(d) {
					d.lab_sample_id = id;
					d.view_type     = 'edit';
				}
			},
			"columns"    : [
				{ "data" : "number" },
				{ "data" : "comment" },
				{ "data" : "action" }
			],
			"order": [[0, 'asc']],
			"language"   : dataTableOption.language
		});

		$("#comment").val('');
		$("#comment").removeData('cmt_id');
		$("#modal_comment").removeData('lab_sample_id');
		$("#modal_comment").data('lab_sample_id', id);
		$("#modal_comment").modal({backdrop:'static'});
	});

	//save
	$("#btnAddComment").on("click", function(evt) {
		evt.preventDefault();

		var comment = $("#comment").val().trim();

		if (comment.length == 0) {
			$("label[for=comment]").attr("data-hint", require_comment);
			return false;
		} else {
			$("label[for=comment]").removeAttr("data-hint");
		}

		$.ajax({
			url      : base_url + 'sample/add_comment',
			type     : 'POST',
			data     : { comment : comment, cmt_id : $("#comment").data('cmt_id'), lab_sample_id : $("#modal_comment").data('lab_sample_id') },
			dataType : 'json',
			success  : function(resText) {
				var style = resText.status == true ? 'success' : 'warning';
				myDialog.showDialog('show', { text : resText.msg, status : '', style : style });
				tb_cmt_list.ajax.reload();

				$("#comment").removeData('cmt_id');
				$("#comment").val('');
			}
		});
	});

	$("#tb_cmt_list tbody").on("click", '.edit', function(evt) {
		evt.preventDefault();
		$(this).blur();
		var data = tb_cmt_list.row($(this).parents("tr")).data().DT_RowData;

		$("#comment").data('cmt_id', data.ID);
		$("#comment").val(data.comment);
	});

	$("#tb_cmt_list tbody").on("click", '.remove', function(evt) {
		evt.preventDefault();
		$(this).blur();
		var data = tb_cmt_list.row($(this).parents("tr")).data().DT_RowData;

		if (confirm('Are you sure you want to delete this comment?')) {
			$.ajax({
				url      : base_url + 'sample/delete_comment',
				type     : 'POST',
				data     : { cmt_id : data.ID },
				dataType : 'json',
				success  : function(resText) {
					tb_cmt_list.ajax.reload();
				}
			});
		}
	});
});