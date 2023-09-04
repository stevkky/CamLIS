$(function() {
    var $result_template = $("div.result-template");

    //Init iCheck
    $("input:checkbox").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });

    $("#btnSaveResultTemplate").on("click", function (evt) {
        evt.preventDefault();

        var data = [
            { data_key : 'left-result-footer',   value : $result_template.find("input[name=left-footer-text]").val(),   status : $result_template.find("input[name=left-footer-status]:checked").val()   || 0 },
            { data_key : 'middle-result-footer', value : $result_template.find("input[name=middle-footer-text]").val(), status : $result_template.find("input[name=middle-footer-status]:checked").val() || 0 },
            { data_key : 'right-result-footer',  value : $result_template.find("input[name=right-footer-text]").val(),  status : $result_template.find("input[name=right-footer-status]:checked").val()  || 0 }
        ];

        myDialog.showProgress("show");
        $.ajax({
            url      : base_url + "laboratory/set_variables",
            type     : 'POST',
            data     : { varialbes : data },
            dataType : 'json',
            success  : function (resText) {
                myDialog.showProgress("hide");
                myDialog.showDialog("show", { text : resText.msg, style : resText.status ? 'success' : 'warning' } );
            },
            error    : function () {
                myDialog.showProgress("hide");
                myDialog.showDialog("show", { text : msg_save_fail, style : 'warning' });
            }
        });
    });
});