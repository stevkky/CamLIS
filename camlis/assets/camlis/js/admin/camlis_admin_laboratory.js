$(function() {
    var $tbl_laboratory   = $("#tbl-laboratory");
    var $modal_laboratory = $("#modal-laboratory");
    var tbl_laboratory    = $tbl_laboratory.DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "ajax"       : {
            "url"    : base_url+'laboratory/view_all_laboratory',
            "type"   : 'POST'
        },
        "language"   : dataTableOption.language,
        "columns"    : [
            { "data" : "number" },
            { "data" : "name_en" },
            { "data" : "name_kh" },
            { "data" : "address_en" },
            { "data" : "sample_number", "render" : function (data) { return data == 1 ? 'Auto' : 'Manual'; } },
            { "data" : "action" }
        ],
        "columnDefs": [
            { "targets": -1, "orderable": false, "searchable": false, "className" : "text-center text-middle no-wrap" },
            { "targets": -2, "className" : "text-center text-middle no-wrap" },
            { "targets": "_all", "className" : "text-middle" }
        ],
        "order": [[0, 'asc']],
        "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
        "pageLength": 20
    });

    //Show Lab Modal
    $("#addNew").on("click", function(evt) {
        evt.preventDefault();
        $modal_laboratory.find("input").val('');
        $modal_laboratory.find("select#sample-number-type").val(1);

        $modal_laboratory.removeData("laboratory_id");
        $modal_laboratory.modal({ backdrop : 'static' });
    });

    /**
     * Add/update Laboratory
     */
    $modal_laboratory.find("#btnSave").on("click", function(evt) {
        evt.preventDefault();

        var data        = {
            name_en         : $modal_laboratory.find("#lab-name-en").val().trim(),
            name_kh         : $modal_laboratory.find("#lab-name-kh").val().trim(),
            lab_code        : $modal_laboratory.find("#lab-code").val().trim(),
            address_en      : $modal_laboratory.find("#address-en").val().trim(),
            address_kh      : $modal_laboratory.find("#address-kh").val().trim(),
            sample_number   : $modal_laboratory.find("#sample-number-type").val()
        };

        if (data.name_en.length === 0 || data.name_kh.length === 0 || data.lab_code.length === 0 || data.sample_number < 1) {
            myDialog.showDialog('show', {text:msg_required_data, style : 'warning'});
            return false;
        }
        
        var url     = base_url + "laboratory/add_new_laboratory";
        var lab_id  = $modal_laboratory.data("laboratory_id");
        if (lab_id !== undefined && Number(lab_id) > 0) {
            url = base_url + 'laboratory/update';
            data.laboratory_id = lab_id;
        }

        $.ajax({
            url      : url,
            type     : 'POST',
            data     : data,
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                if (resText.status > 0) {
                    tbl_laboratory.ajax.reload();
                    $modal_laboratory.modal("hide");
                }
            },
            error    : function () {
                myDialog.showDialog('show', {text : lab_id > 0 ? msg_update_fail : msg_save_fail, style : 'warning'});
            }
        });
    });
    
    $tbl_laboratory.on("click", ".edit", function(evt) {
        evt.preventDefault();
        $(this).blur();
        var data = tbl_laboratory.row($(this).parents("tr")).data();

        $modal_laboratory.find("#lab-name-en").val(data.name_en);
        $modal_laboratory.find("#lab-name-kh").val(data.name_kh);
        $modal_laboratory.find("#lab-code").val(data.lab_code);
        $modal_laboratory.find("#address-en").val(data.address_en);
        $modal_laboratory.find("#address-kh").val(data.address_kh);
        $modal_laboratory.find("#sample-number-type").val(data.sample_number);

        $modal_laboratory.removeData("laboratory_id");
        $modal_laboratory.data("laboratory_id", data.labID);
        $modal_laboratory.modal({ backdrop : 'static' });
    });
    
    $tbl_laboratory.on("click", ".remove", function(evt) {
        evt.preventDefault();
        
        var data = tbl_laboratory.row($(this).parents("tr")).data();
        
        if (confirm(q_delete_laboratory)) {
            $.ajax({
                url      : base_url + 'laboratory/delete',
                type     : 'POST',
                data     : { laboratory_id : data.labID },
                dataType : 'json',
                success  : function(resText) {
                    myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                    tbl_laboratory.ajax.reload();
                    $modal_laboratory.modal("hide");
                },
                error    : function () {
                    myDialog.showDialog('show', {text : msg_delete_fail, style : 'warning'});
                }
            });
        }
    });
});