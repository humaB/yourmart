$(document).ready( function () {

    let url = window.location.origin + localStorage.getItem('_path');

    $("#findByName").on("change",function(e){
   
            let value = $("#findByName").val();
   
            $.ajax({
                url:url + '/hrm/attendance/find-name', 
                type:"post",
                data:{
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    value: value
                },
                success:function(response) {
                    if (response.success) {
                        $("#employee-form").hide(100).show(300);
                        $("#employee-name").html(response.results.name)
                        $("#employee-father-name").html(response.results.f_name)
                        $("#employee-designation").html(response.results.designation.name)
                        $("#employee").val(response.results.id);
                    }
                    else{
                        $("#employee-form").hide(100);
                    }
                }
            })
        
    })
});