$(function() {
	//Update Laboratory Info
	$("#btnEditLaboratory").on("click", function(evt) {
		evt.preventDefault();

		var form = $("#frm-laboratory").get(0);
		var formData = new FormData(form);

		var data    = {
			name_en			: $("#lab-name-en").val().trim(),
			name_kh			: $("#lab-name-kh").val().trim(),
			address_en		: $("#address-en").val().trim(),
			address_kh		: $("#address-kh").val().trim(),
			lab_code		: $("#lab-code").attr("value"),
			sample_number	: $("#sample-number-type").attr("value"),
			laboratory_id	: Number($("#laboratory-id").attr("value"))
		};

		var is_valid = !(data.name_en.length === 0 || data.name_kh.length === 0 || data.laboratory_id < 1 || data.lab_code.length === 0 || ["1", "2"].indexOf(data.sample_number) < 0);
		if (!is_valid) {
			myDialog.showDialog('show', {text:msg_required_data, style : 'warning'});
			return false;
		}
		//console.log(data);
		
		$.ajax({
			url      : base_url + "laboratory/update",
			type     : 'POST',
			data     : formData,
			dataType : 'json',
			contentType	: false,
			cache		: false,
			processData	: false,
			success  : function(resText) {
				//console.log(JSON.stringify(resText));
				myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning', onHidden: function() { 
					location.reload(); 
				}});
			},
			error : function () {
				myDialog.showDialog('show', {text: msg_update_fail, style : 'warning'});
			}
		});
	});

	$("input#lab-icon").on("change", function() {
		var input = $(this).get(0);

		if (input.files && input.files[0]) {
			$("#frm-laboratory").find("input[name=clear_photo]").remove();

			var reader = new FileReader();
			reader.onload = function (e) {
				$('#lab-icon-view').attr('src', e.target.result);
			};

			reader.readAsDataURL(input.files[0]);
		}
	});

	$("#btn-remove-icon").on("click", function (evt) {
		evt.preventDefault();
		$("#lab-icon-view").attr("src", $("#lab-icon-view").attr("default-src"));
		$("#frm-laboratory").find("input[name=clear_photo]").remove();
		$("#frm-laboratory").append("<input type='hidden' name='clear_photo' value='200'>");
	});
});