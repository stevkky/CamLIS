$(function () {
    /**
     * Save/Update User Profile
     */
    $("#btnSaveProfile").on("click", function (evt) {
        evt.preventDefault();
        var data = {
            fullname     : $("#fullname").val().trim(),
            username     : $("#username").val().trim(),
            email        : $("#email").val().trim(),
            phone        : $("#phone").val().trim(),
            old_password : $("#old-password").val().trim(),
            password     : $("#new-password").val().trim(), //New Password
            confirm_password : $("#confirm-password").val().trim() //New Confirm Password
        };

        $("div.hint--error").attr("data-hint", "");

        var isValid = true;
        if (data.fullname.length == 0) {
            $("div.fullname").attr("data-hint", msg_required_fullname);
            isValid = false;
        }

        if (data.username.length == 0) {
            $("div.username").attr("data-hint", msg_required_username);
            isValid = false;
        }

        if (data.old_password.length > 0 && data.password.length == 0) {
            $("div.new-password").attr("data-hint", msg_required_new_pass);
            isValid = false;
        }

        if (data.password.length > 0 && (data.password.length < 5 || data.password.length > 20)) {
            $("div.new-password").attr("data-hint", msg_password_criteria);
            isValid = false;
        }

        if ((data.password.length > 0 || data.confirm_password.length > 0) && data.password != data.confirm_password) {
            $("div.new-confirm-password").attr("data-hint", msg_wrong_confirm_pass);
            isValid = false;
        }

        if ((data.password.length > 0 || data.confirm_password.length > 0) && data.old_password.length == 0) {
            $("div.old-password").attr("data-hint", msg_required_old_pass);
            isValid = false;
        }

        if (!isValid) return false;

        if (data.old_password.length > 0) {
            myDialog.showProgress("show");
            $.ajax({
                url: base_url + "user/verify_password",
                type: "POST",
                data: {password: data.old_password},
                dataType: 'json',
                success: function (resText) {
                    myDialog.showProgress("hide");

                    if (resText.status) {
                        $("div.old-password").attr("data-hint", "");
                        updateProfile(data);
                    }
                    else $("div.old-password").attr("data-hint", msg_wrong_password);
                },
                error: function () {
                    myDialog.showProgress("hide");
                    $("div.old-password").attr("data-hint", msg_wrong_password);
                }
            });
        } else {
            updateProfile(data);
        }
    });
});

/**
 * Update Profile
 */
function updateProfile(data) {
    myDialog.showProgress("show");
    $.ajax({
        url      : base_url + "user/updateProfile",
        type     : "POST",
        data     : data,
        dataType : 'json',
        success  : function (resText) {
            myDialog.showProgress("hide");
            myDialog.showDialog('show', {text : resText.msg, style : resText.status ? 'success' : 'warning', onHidden : function () {
                if (resText.status) {
                    location.reload();
                }
            } });
            $("#old-password").val('');
            $("#new-password").val('');
            $("#confirm-password").val('');
        },
        error    : function () {
            myDialog.showProgress("hide");
            myDialog.showDialog('show', {text : msg_update_fail, style : 'warning'});
        }
    });
}
