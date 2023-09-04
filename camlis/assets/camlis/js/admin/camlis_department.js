$(function() {
    var tb_department = $("#tb_department").DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "ajax"       : {
            "url"    : base_url+'department/view_lab_department',
            "type"   : 'POST'
        },
        "language"   : dataTableOption.language
    });
    
    //show department list             
    $("#addNew").on("click", function() {
        $.ajax({
            url      : base_url + 'department/get_std_department',
            type     : 'POST',
            dataType : 'json',
            success  : function(resText) {
                //current department from datatable
                var rows    = tb_department.rows().data();
                var rowData = [];
                $.each(rows, function(i, row) {
                   rowData.push(row.DT_RowData);    
                });
                
                var content = $("#std_department").find(".modal-body .tree");
                var list  = "<ul>";
                for(var i in resText) {    
                    var checked = "";
                    if (rowData.indexOf(resText[i].ID) > -1) {
                        checked = "checked";
                    }
                    
                    list += "<li> \
                                <label class='control-label' style='cursor:pointer;'><input type='checkbox' name='departments' value='" + resText[i].ID + "' " + checked + " >&nbsp;&nbsp;" + resText[i].name + "</label> \
                             </li>";
                }
                
                list += '</ul>';
                
                content.html(list)
                       .find('input').iCheck({
                            checkboxClass: 'icheckbox_minimal',
                            radioClass: 'iradio_minimal'
                        });
                
                $("#std_department").modal({backdrop : 'static'});
            }
        });    
    });
    
    //Add Department
    $("#btnAddDepartment").on("click", function(evt) {
        var content = $("#std_department").find(".modal-body .tree");
        var selected = [];
        content.find("input:checked").each(function() {
            selected.push($(this).val());            
        });
        
        $.ajax({
            url      : base_url + 'department/add_lab_department',
            type     : 'POST',
            data     : { departments : selected },
            dataType : 'json',
            success  : function(resText) {
                $("#std_department").modal("hide");
                tb_department.ajax.reload();
            }
        }); 
    });
});