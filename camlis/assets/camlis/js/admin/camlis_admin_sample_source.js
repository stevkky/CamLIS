$(function() {
    //Initialize DataTable
    var tbl_sample_source = $("#tbl-sample-source").DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "ajax"       : {
            "url"    : base_url+'sample_source/view_std_sample_source',
            "type"   : 'POST'
        },
        "language"   : dataTableOption.language,
        "columns"    : [
            { "data" : "number" },
            { "data" : "source_name" },
            { "data" : "action" }
        ],
        "columnDefs": [
            { "targets": -1, "orderable": false, "searchable": false }
        ],
        "order": [[1, 'asc']]
    });

    //Add New Department
    $("#addNew").on("click", function(evt) {
        evt.preventDefault();

        $("#modal-sample-source #btnSave span.save").show();
        $("#modal-sample-source #btnSave span.update").hide();
        $("#modal-sample-source").find('.modal-title.new').show();
        $("#modal-sample-source").find('.modal-title.edit').hide();
        $("#modal-sample-source #sample-source-name").val('');
        $("#modal-sample-source").removeData("source_id");
        $("#modal-sample-source").modal({ backdrop : 'static' });
    });
    
    $("#btnSave").on("click", function(evt) {
        evt.preventDefault();

        var source_name  = $("#modal-sample-source #sample-source-name").val();
        var url     = base_url + "sample_source/add_std_sample_source";

        if (source_name.trim().length == 0) {
            myDialog.showDialog('show', {text:msg_required_data, style : 'warning'});
            return false;
        }

        var ID = $("#modal-sample-source").data("source_id");

        if (ID > 0) url = base_url + "sample_source/update_std_sample_source";

        $.ajax({
            url      : url,
            type     : 'POST',
            data     : { source_name : source_name, source_id : ID },
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                if (resText.status > 0) {
                    tbl_sample_source.ajax.reload();
                    $("#modal-sample-source").modal("hide");
                }
            },
            error : function () {
                myDialog.showDialog('show', {text: ID > 0 ? msg_update_fail : msg_save_fail, style : 'warning'});
            }
        });
    });
    
    $("#tbl-sample-source").on("click", ".edit", function(evt) {
        evt.preventDefault();
        $(this).blur();

        //label
        $("#modal-sample-source #btnSave span.save").hide();
        $("#modal-sample-source #btnSave span.update").show();
        $("#modal-sample-source").find('.modal-title.new').hide();
        $("#modal-sample-source").find('.modal-title.edit').show();

        var data = tbl_sample_source.row($(this).parents("tr")).data();
        $("#modal-sample-source #sample-source-name").val('');
        $("#modal-sample-source #sample-source-name").val(data.source_name);

        $("#modal-sample-source").removeData("source_id");
        $("#modal-sample-source").data("source_id", data.source_id);
        $("#modal-sample-source").modal({ backdrop : 'static' });
    });
    
    $("#tbl-sample-source").on("click", ".remove", function(evt) {
        evt.preventDefault();
        
        var data = tbl_sample_source.row($(this).parents("tr")).data();
        
        if (confirm(q_delete_sample_source)) {
            $.ajax({
                url      : base_url + 'sample_source/delete_std_sample_source',
                type     : 'POST',
                data     : { source_id : data.source_id },
                dataType : 'json',
                success  : function(resText) {
                    myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                    tbl_sample_source.ajax.reload();
                },
                error : function () {
                    myDialog.showDialog('show', {text: msg_delete_fail, style : 'warning'});
                }
            });
        }
    });
});