"use strict";
var input_group = {
    fullname    : new Input("full_name"),
    username    : new Input("username"),
	location    : new Input("location"),
    province    : new Input("province"),    
	password    : new Input("password"),
    password2   : new Input("password2"),
}
var emailObj       = new Input("email");
var phoneObj       = new Input("phone");

$( document ).ready(function() {
	$("#" + emailObj.id).blur(function() {
		if(emailObj.getValue() !== ""){
			if (!IsEmail(emailObj.getValue())) {
				emailObj.msg("Email address is not valid");
				emailObj.valid = false;
			} else {
				$.ajax({
					url		 : base_url + '/generate/emailExist',
					type	 : 'POST',
					data	 : { email : emailObj.getValue() },
					dataType : 'json',
					success	 : function (resText) {
					   if(resText.status){
							emailObj.msg("Email exists, please try again ....");
							emailObj.valid = false;
					   }else{
							emailObj.msg("&nbsp;");
							emailObj.valid = true;
					   }
					}
				});
				emailObj.msg("&nbsp;");
				emailObj.valid = true;
			}
		}else{
			emailObj.msg("&nbsp;");
		}
	})
	$.each(input_group, function(key, inputObj) {
		$("#" + inputObj.id).blur(function() {			
			if (inputObj.getValue() == "") {
				inputObj.msg("Field required");
				inputObj.valid = false;
			} else if (key == 'username') {
                var username = inputObj.getValue();
                var usernameObj = input_group.username;
				$.ajax({
                    url		 : base_url + '/generate/usernameExist',
                    type	 : 'POST',
                    data	 : { username : username },
                    dataType : 'json',
                    success	 : function (resText) {
                       if(resText.status){
                            usernameObj.msg("Username exists, please try again ....");
					        usernameObj.valid = false;
                       }else{
                            usernameObj.msg("&nbsp;");
                            usernameObj.valid = true;
                       }
                    }
                });
			}else if (key == 'password2') {
				var passwordObj  = input_group.password;
				if(passwordObj.getValue() !== inputObj.getValue()){
					inputObj.msg("Confirmation password does not match ....");
					$("#" + inputObj.id).focus();
					inputObj.valid = false;					
				}else{
					inputObj.msg("&nbsp;");
					inputObj.valid = true;
				}
			}else {
				inputObj.msg("&nbsp;");
				inputObj.valid = true;
			}
		})
	});
    $("#btnRegister").on("click", function() {
		var isValid = false;
		$(this).html('Please wait ...').attr('disabled', 'disabled'); // prevent multiple click
		$.each(input_group, function(key, inputObj) {
			if (inputObj.valid == false) {
				$("#" + inputObj.id).focus();
				isValid = false;
				$("#btnRegister").html('Register').removeAttr('disabled'); // prevent multiple click
				return false;
			}
			isValid = true;
		})
		
		var passwordObj  = input_group.password;
		var password2Obj = input_group.password2;
		if(passwordObj.getValue() !== password2Obj.getValue()){
			password2Obj.msg("Confirmation password does not match ....");
			$("#" + password2Obj.id).focus();
			password2Obj.valid = false;
			isValid = false;
		}else{
			password2Obj.valid = true;
			isValid = true;
		}
		if(emailObj.getValue() !== ""){
			if (!IsEmail(emailObj.getValue())) {
				emailObj.msg("Email address is not valid");
				emailObj.valid = false;
				isValid = false;
			} else {
				$.ajax({
					url		 : base_url + '/generate/emailExist',
					type	 : 'POST',
					data	 : { email : emailObj.getValue() },
					dataType : 'json',
					success	 : function (resText) {
					   if(resText.status){
							emailObj.msg("Email exists, please try again ....");
							emailObj.valid = false;
							isValid = false;
					   }else{
							emailObj.msg("&nbsp;");
							emailObj.valid = true;
							isValid = true;
					   }
					}
				});
			}
		}
		if (!isValid) {
			$("#btnRegister").html('Register').removeAttr('disabled'); // prevent multiple click
			return false;
		}
		$.ajax({
			url		 : base_url + '/generate/doRegister',
			type	 : 'POST',
			data	 : {fullname: input_group.fullname.getValue() , email : emailObj.getValue(), phone: phoneObj.getValue(), password: input_group.password.getValue(), province: input_group.province.getValue(), username: input_group.username.getValue(), location: input_group.location.getValue() },
			dataType : 'json',
			success	 : function (resText) {
				console.log(resText);
			   if(resText.status){
					// close the registration section 
					setTimeout(function(){
						$("#register_section").addClass('d-none');
						$("#btnRegister").html('Register').removeAttr('disabled');
					}, 1000);
					setTimeout(function(){
						$("#result_section").removeClass('d-none');
						$("#btnRegister").html('Register').removeAttr('disabled');
					}, 500);
					
			   }else{
					$("#btnRegister").html('Register').removeAttr('disabled'); // prevent multiple click
					alert("Warning "+resText.msg);	
			   }
			}
		});
	});			
});