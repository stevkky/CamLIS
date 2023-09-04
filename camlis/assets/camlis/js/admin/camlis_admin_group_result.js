$(function() {
    /**
     * Enable/Disable Edit Group Result
     */
    $("#modal-test #group-result-list").on("change", function () {
        var val = $(this).val();
        if (val > 0) {
            $("#modal-test #btn-edit-group-result").prop("disabled", false);
        } else {
            $("#modal-test #btn-edit-group-result").prop("disabled", true);
        }
    });

    /**
     * Show Input for Entry New Group Result
     */
    $("#modal-test #btn-new-group-result").on("click", function (evt) {
        evt.preventDefault();
        $("#modal-test #select-group-result-wrapper").hide();
        $("#modal-test #group-result-wrapper").show();
        $("#modal-test #group-result-wrapper").find("#group-result-name").val('');
        $("#modal-test #group-result-wrapper").find("#group-result-name").focus();
        $("#modal-test #group-result-wrapper").find("#group-result-name").removeData("group_result_id");
        $("#modal-test #group-result-list").val(null).trigger("change");
        $("#modal-test #btn-delete-group-result").prop("disabled", true);
        $("#modal-test #btnSave").prop("disabled", true);
    });

    /**
     * Show Input for Edit Group Result
     */
    $("#modal-test #btn-edit-group-result").on("click", function (evt) {
        evt.preventDefault();
        var group_result_list = $("#modal-test #group-result-list");
        var group_result_id = Number(group_result_list.val()); //Current Selected Test

        if (!isNaN(group_result_id) && group_result_id > 0) {
            $("#modal-test #select-group-result-wrapper").hide();
            $("#modal-test #group-result-wrapper").show();

            $("#modal-test #group-result-wrapper").find("#group-result-name").val(group_result_list.find("option:selected").text());
            $("#modal-test #group-result-wrapper").find("#group-result-name").focus();
            $("#modal-test #btn-delete-group-result").prop("disabled", false);
            $("#modal-test #btnSave").prop("disabled", true);
            $("#modal-test #group-result-list").val(null).trigger("change");

            //set group result id
            $("#modal-test #group-result-wrapper").find("#group-result-name").removeData("group_result_id");
            $("#modal-test #group-result-wrapper").find("#group-result-name").data("group_result_id", group_result_id);
        }
    });

    /**
     * Show Group Result List (Hide Test Entry Form)
     */
    $("#modal-test #btn-cancel-group-result").on("click", function (evt) {
        evt.preventDefault();
        var group_result_id = $("#modal-test #group-result-wrapper").find("#group-result-name").data("group_result_id");
        if (group_result_id != undefined && Number(group_result_id) > 0) {
            $("#modal-test #group-result-list").val(group_result_id).trigger("change");
        } else {
            $("#modal-test #group-result-list").val(null).trigger("change");
        }

        $("#modal-test #select-group-result-wrapper").show();
        $("#modal-test #group-result-wrapper").hide();
        $("#modal-test #group-result-wrapper").find("#group-result").removeData("group_result_id");
        $("#modal-test #btnSave").prop("disabled", false);
    });


    /**
     * Save/Update Group Result
     */
    $("#modal-test #btn-save-group-result").on("click", function (evt) {
        evt.preventDefault();
        var group_name = $("#modal-test #group-result-wrapper #group-result-name").val().trim();
        var group_result_id = Number($("#modal-test #group-result-wrapper").find("#group-result-name").data("group_result_id"));
        var data = {group_name: group_name};
        var url = base_url + "test/add_std_group_result";

        if (group_name.length == 0) {
            myDialog.showDialog('show', {text: msg_fill_required_data, style: 'warning'});
            return false;
        }

        if (!isNaN(group_result_id) && group_result_id > 0) {
            url = base_url + "test/update_std_group_result";
            data.group_result_id = group_result_id;
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (resText) {
                myDialog.showDialog('show', {text: resText.msg, style: resText.status > 0 ? 'success' : 'warning'});
                //Add new Group Result to test list
                if (isNaN(group_result_id) && resText.status > 0 && resText.data && resText.data.group_result_id > 0) {
                    $("#modal-test #btn-cancel-group-result").trigger("click");
                    $("#modal-test #select-group-result-wrapper #group-result-list").append("<option value='" + resText.data.group_result_id + "'>" + resText.data.group_name + "</option>");
                    $("#modal-test #select-group-result-wrapper #group-result-list").val(resText.data.group_result_id).trigger("change");
                } else if (group_result_id > 0 && resText.status > 0) {
                    $("#modal-test #btn-cancel-group-result").trigger("click");
                    $("#modal-test #select-group-result-wrapper #group-result-list").find("option[value=" + group_result_id + "]").text(data.group_name);
                    $("#modal-test #select-group-result-wrapper #group-result-list").select2();
                    $("#modal-test #select-group-result-wrapper #group-result-list").val(group_result_id).trigger("change");
                }
            },
            error: function () {
                myDialog.showDialog('show', {text: group_result_id > 0 ? msg_update_fail : msg_save_fail, style: 'warning'});
            }
        });
    });

    /**
     * Delete Group Result
     */
    $("#modal-test #btn-delete-group-result").on("click", function (evt) {
        evt.preventDefault();
        var group_result_id = Number($("#modal-test #group-result-wrapper").find("#group-result-name").data("group_result_id"));

        if (isNaN(group_result_id) || group_result_id <= 0) return false;

        if (confirm(msg_q_delete_test)) {
            $.ajax({
                url: base_url + 'test/delete_std_group_result',
                type: 'POST',
                data: {group_result_id: group_result_id},
                dataType: 'json',
                success: function (resText) {
                    myDialog.showDialog('show', {text: resText.msg, style: resText.status > 0 ? 'success' : 'warning'});
                    if (resText.status > 0) {
                        $("#modal-test #btn-cancel-group-result").trigger("click");
                        $("#modal-test #select-group-result-wrapper #group-result-list").find("option[value=" + group_result_id + "]").remove();
                        $("#modal-test #select-group-result-wrapper #group-result-list").val(null).trigger("change");
                    }
                },
                error: function () {
                    myDialog.showDialog('show', {text: msg_delete_fail, style: 'warning'});
                }
            });
        }
    });
});