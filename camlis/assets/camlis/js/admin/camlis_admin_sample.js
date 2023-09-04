$(function() {
    var $tbl_sample   = $("#tbl-sample");
    var $modal_sample = $("#modal-sample");
    var tbl_sample    = $tbl_sample.DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "ajax"       : {
            "url"    : base_url + 'sample/view_all_std_sample',
            "type"   : 'POST'
        },
        "language"   : dataTableOption.language,
        "columns"    : [
            { "data" : "number", "width": "30px" },
            { "data" : "sample_name", "className": "text-nowrap text-middle" },
            { "data" : "department_name" },
            { "data" : "sample_description" },
            { "data" : "action", "width": "40px" }
        ],
        "columnDefs": [
            { "targets": -1, "orderable": false, "searchable": false, className: "text-middle text-center no-wrap" },
            { "targets": "_all", "className" : "text-middle" },
            { "targets": [2, 3], "orderable": false, "searchable": false }
        ],
        "order": [[1, 'asc']],
        "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
        "pageLength": 20
    });

    //Init Select2
    $("#department").select2();

    //Init Tags Input
    $('input#sample-description').tagsinput({
        tagClass  : 'label label-primary tag-label',
        trimValue : true,
        allowDuplicates: false
    });

    $("#addNew").on("click", function(evt) {
        evt.preventDefault();

        //Label
        $modal_sample.find(".modal-title.save").show();
        $modal_sample.find(".modal-title.update").hide();
        $modal_sample.find("#btnSave span.save").show();
        $modal_sample.find("#btnSave span.update").hide();

        $modal_sample.find("#sample_name").val('');
        $modal_sample.find("#department").val(null).trigger("change");
        $modal_sample.find("#sample-description").tagsinput('removeAll');

        $modal_sample.find("label").removeAttr("data-hint");
        $modal_sample.removeData("sample_id");
        $modal_sample.modal({ backdrop : 'static' });
    });

    $("#btnSave").on("click", function(evt) {
        evt.preventDefault();

        //Get Required Value
        var department  = $modal_sample.find("#department").val();
        var name        = $modal_sample.find("#sample_name").val().trim();
        var description = $modal_sample.find("#sample-description").tagsinput("items");

        //Validation
        var hasErr = 0;
        if (department && department.length == 0) {
            //myDialog.showDialog('show', { text : msg_must_choose_dep, status : '', style : 'warning'});
            $modal_sample.find("label[for=department]").attr("data-hint", msg_must_choose_dep);
            hasErr += 1;
        } else {
            $modal_sample.find("label[for=department]").removeAttr("data-hint");
        }

        if (name.length == 0) {
            $modal_sample.find("label[for=sample_name]").attr("data-hint", msg_must_fill_sname);
            hasErr += 1;
        } else {
            $modal_sample.find("label[for=sample_name]").removeAttr("data-hint");
        }

        if (hasErr > 0)
            return false;
        
        var sample_id = $modal_sample.data("sample_id");
        var url   = base_url + "sample/add_std_sample";
        if (sample_id > 0) {
            url = base_url + "sample/update_std_sample";
        }

        $.ajax({
            url      : url,
            type     : 'POST',
            data     : { sample_name : name, departments : department, descriptions : description, sample_id : sample_id },
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text : resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                if (resText.status > 0) {
                    tbl_sample.ajax.reload();
                    $modal_sample.modal("hide");
                }
            },
            error   : function () {
                myDialog.showDialog('show', {text : sample_id > 0 ? msg_update_fail : msg_save_fail, style : 'warning'});
                $modal_sample.modal("hide");
            }
        });
    });

    $tbl_sample.on("click", ".edit", function(evt) {
        evt.preventDefault();
        $(this).blur();

        //Label
        $modal_sample.find(".modal-title.save").hide();
        $modal_sample.find(".modal-title.update").show();
        $modal_sample.find("#btnSave span.save").hide();
        $modal_sample.find("#btnSave span.update").show();

        $modal_sample.find("#sample_name").val('');
        $modal_sample.find("#department").val(null).trigger("change");
        $modal_sample.find("#sample-description").tagsinput('removeAll');

        var data = tbl_sample.row($(this).parents("tr")).data();

        $modal_sample.find("#sample_name").val(data.sample_name);
        var descriptions = data.DT_RowData.descriptions;
        if (data.DT_RowData.descriptions && Array.isArray(descriptions)) {
            for(var i in descriptions) {
                $modal_sample.find("#sample-description").tagsinput('add', descriptions[i]);
            }
        }

        $modal_sample.removeData("sample_id");
        //$modal_sample.data("sample_id", data.DT_RowData.ID);
        $modal_sample.data("sample_id", data.DT_RowData.sample_id);
        console.log("Sample id "+ JSON.stringify(data.DT_RowData));
        $.ajax({
            url      : base_url + "sample/get_std_department_sample",
            type     : 'POST',
            //data     : { sample_id : data.DT_RowData.ID },
            data     : { sample_id : data.DT_RowData.sample_id }, // added 15 Dec 2020
            dataType : 'json',
            success  : function(resText) {
                console.log(JSON.stringify(resText));
                var departments = [];
                for(var i in resText) {
                    departments.push(resText[i].department_id);
                }
                console.log(departments);
                $modal_sample.find("#department").val(departments).trigger("change");
                $modal_sample.modal({ backdrop : 'static' });
            }
        });
    });

    $tbl_sample.on("click", ".remove", function(evt) {
        evt.preventDefault();
        $(this).blur();
        var data = tbl_sample.row($(this).parents("tr")).data();

        if (confirm(msg_q_delete_sample_type)) {
            $.ajax({
                url      : base_url + 'sample/delete_std_sample',
                type     : 'POST',
                //data     : { sample_id : data.DT_RowData.ID },
                data     : { sample_id : data.DT_RowData.sample_id },
                dataType : 'json',
                success  : function(resText) {
                    //if (resText.status) {
                        myDialog.showDialog('show', {text:'Sample has been deleted!', status : 'Success! ', style : 'success'});
                        tbl_sample.ajax.reload();
                    //}
                },
                error : function () {
                    
                }
            });
        }
    });
});