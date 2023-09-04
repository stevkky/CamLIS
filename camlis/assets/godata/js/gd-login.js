var loadingScreen   = new Loading();
var input_group = {
	email: new Input("email"),
	password: new Input("password"),
}
$( document ).ready(function() {
	$.each(input_group, function(key, inputObj) {
		$("#" + inputObj.id).blur(function() {
			if (inputObj.getValue() == "") {
				inputObj.msg("Field required");
				inputObj.valid = false;
			} else if (key == 'email') {
				if (!IsEmail(inputObj.getValue())) {
					inputObj.msg("Email address is not valid");
					inputObj.valid = false;
				} else {
					inputObj.msg("&nbsp;");
					inputObj.valid = true;
				}
			} else {
				inputObj.msg("&nbsp;");
				inputObj.valid = true;
			}
		})
	});

	$("#btnSubmit").on("click", function() {
		doSubmit(theForm);
	})	
	$('#theform').keypress(function (e) {
		if (e.which == 13) {
			doSubmit(theForm);
			return false;
		}
	});	
	function doSubmit(){
		$("#btnSubmit").on("click", function() {
			$(this).html('Please wait ...').attr('disabled', 'disabled'); // prevent multiple click
			$.each(input_group, function(key, inputObj) {
				if (inputObj.valid == false) {
					$("#" + inputObj.id).focus();
					isValid = false;
					return false;
				}
				isValid = true;
			})

			if (!isValid) {
				$("#btnSubmit").html('Submit').removeAttr('disabled'); // prevent multiple click
				return false;
			}
			loadingScreen.show();
			setTimeout(function(){ 					
				loadingScreen.hide();
				$("#btnSubmit").html('Submit').removeAttr('disabled');
				$("#theForm").submit(); 
			}, 1000);					
		});
	}
});


