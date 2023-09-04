$(function() {
    //Initialize DataTable
    var tbl_payment_type = $("#tbl-payment-type").DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "ajax"       : {
            "url"    : base_url+'payment_type/view_std_payment_type',
            "type"   : 'POST'
        },
        "language"   : dataTableOption.language,
        "columns"    : [
            { "data" : "number" },
            { "data" : "name" },
            { "data" : "action" }
        ],
        "columnDefs": [
            { "targets": -1, "orderable": false, "searchable": false, "className": "text-center" }
        ],
        "order": [[1, 'asc']]
    });

    //Add New Department
    $("#addNew").on("click", function(evt) {
        evt.preventDefault();

        $("#modal-payment-type #btnSave span.save").show();
        $("#modal-payment-type #btnSave span.update").hide();
        $("#modal-payment-type").find('.modal-title.new').show();
        $("#modal-payment-type").find('.modal-title.edit').hide();
        $("#modal-payment-type #payment-type").val('');
        $("#modal-payment-type").removeData("payment_type_id");
        $("#modal-payment-type").modal({ backdrop : 'static' });
    });
    
    $("#btnSave").on("click", function(evt) {
        evt.preventDefault();

        var name = $("#modal-payment-type #payment-type").val();
        var url  = base_url + "payment_type/add_std_payment_type";

        if (name.trim().length == 0) {
            myDialog.showDialog('show', {text: msg_required_data, style : 'warning'});
            return false;
        }

        var ID = $("#modal-payment-type").data("payment_type_id");

        if (ID > 0) url = base_url + "payment_type/update_std_payment_type";

        $.ajax({
            url      : url,
            type     : 'POST',
            data     : { name : name, id : ID },
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                if (resText.status > 0) {
                    tbl_payment_type.ajax.reload();
                    $("#modal-payment-type").modal("hide");
                }
            },
            error : function () {
                myDialog.showDialog('show', {text: ID > 0 ? globalMessage.update_fail : globalMessage.save_fail, style : 'warning'});
            }
        });
    });
    
    $("#tbl-payment-type").on("click", ".edit", function(evt) {
        evt.preventDefault();
        $(this).blur();

        //label
        $("#modal-payment-type #btnSave span.save").hide();
        $("#modal-payment-type #btnSave span.update").show();
        $("#modal-payment-type").find('.modal-title.new').hide();
        $("#modal-payment-type").find('.modal-title.edit').show();

        var data = tbl_payment_type.row($(this).parents("tr")).data();
        $("#modal-payment-type #payment-type").val('');
        $("#modal-payment-type #payment-type").val(data.name);

        $("#modal-payment-type").data("payment_type_id", data.id);
        $("#modal-payment-type").modal({ backdrop : 'static' });
    });
    
    $("#tbl-payment-type").on("click", ".remove", function(evt) {
        evt.preventDefault();
        
        var data = tbl_payment_type.row($(this).parents("tr")).data();
        
        if (confirm(q_delete_payment_type)) {
            $.ajax({
                url      : base_url + 'payment_type/delete_std_payment_type',
                type     : 'POST',
                data     : { id : data.id },
                dataType : 'json',
                success  : function(resText) {
                    myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                    tbl_payment_type.ajax.reload();
                },
                error : function () {
                    myDialog.showDialog('show', {text: globalMessage.delete_fail, style : 'warning'});
                }
            });
        }
    });
});