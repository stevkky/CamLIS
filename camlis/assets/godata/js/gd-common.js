function Loading(){
    this.show = function(){
        $("body").append('<div class="loading" id="loadingScreen">Loading&#8230;</div>');
    }
    this.hide = function(){
        setTimeout( function(){
            $("#loadingScreen").remove();
        }, 500);
    }
}
function Input(id) {
    this.id = id;
    this.value = ($('#' + id).val()).trim();
    this.valid = false;
    this.getValue = function() {
        this.value = ($('#' + id).val()).trim();
        return this.value;
    }
    this.msg = function(msg) {
        $("#" + id + "-message").html(msg);
    };
}
function IsEmail(email) {
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!regex.test(email)) {
        return false;
    } else {
        return true;
    }
}
