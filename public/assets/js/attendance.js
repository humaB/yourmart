$(document).ready( function () {

    let url = window.location.origin + localStorage.getItem('_path');

    var count = 0;
    $(document).on("keypress", function(e) {
        if (count==0) {
            $("#monitor .scan ").addClass("startScan")
            count++;
        }
        var ch = String.fromCharCode(e.which)
        $("#rfid_value").val($("#rfid_value").val() + ch)
        var rfid = $("#rfid_value").val().toString();

        if (e.keyCode == 13) {

            $.ajax({
                url:url + '/hrm/attendance/check', 
                type:"post",
                data:{
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    rfid: rfid
                },
                success:function(response) {
                    $("#rfid_value").val("")
                    setTimeout(() => {
                        if (response.status == "danger") {
                            $(".successData").hide(100)
                            $("#type").css("color","#fc544b")
                            $("#type").html(response.message)
                        }
                        else{
                            $(".successData").show(300)
                            if (response.info.image) {
                                $("#image").attr('src', response.info.image ? url + "/storage/uploads/employee/profile/"+response.info.image : "assets/img/blank_image.jpg")
                            }
                            else{
                                $("#image").attr('src',url+"/assets/img/blank_image.jpg")
                            }
                            $("#name").html(response.info.name)
                            $("#department").html(response.info.department)
                            $("#designation").html(response.info.designation)
                            $("#check_in").html(response.info.check_in)
                            $("#check_out").html(response.info.check_out)
                            $("#total_time").html(response.info.total_time)
                            $("#type").css("color",response.type=="Check Out"?"#fc544b":"#54ca68")
                            $("#type").html(response.type + " Time of " + response.info.name + " has been noted")
                        }
                        $("#messageModal").modal('hide')
                        setTimeout(() => {
                            $("#monitor .scan ").removeClass("startScan")
                            $("#messageModal").modal('show')                            
                        }, 500);
                        count = 0;
                        setTimeout(() => {
                            $("#messageModal").modal('hide')
                        }, 5000);
                    }, 500);
                    
                },
                error : function() {
                    $(".successData").hide()
                    $("#messageModal").modal('show');
                    $("#type").css("color","#fc544b")
                    $("#type").html("You have been logged out please login again")
                    setTimeout(() => {
                        window.location.reload()
                    },3000)
                   
                }
            })

        }
    })


    /**************    FILTER     ***************/
    $("body").on("click", '.details', function() {

        var details = $(this).attr("data-filter");
        $.ajax({
            type: 'get',
            url: url + '/hrm/attendance/filter/details/'+details,
            success: function(response) {

                $("#recordDate").html(details)
                $('#recordTableMain').DataTable().clear().destroy();

                response.forEach((record,i) => {
                    
                    $("#recordTable").append(`
                        <tr>
                            <td>${i+1}</td>
                            <td>${ record.name }</td>
                            <td>${ record.department }</td>
                            <td>
                                <input type="hidden" class="form-control" value="${ record.employee_id }" name='attendance_employee' readonly />
                                <input type="hidden" class="form-control" value="${ record.id }" name='attendance_date' readonly />
                                <input type="time" step="1" class="form-control" value="${ record.check_in }" name='attendance_checkin' readonly />
                            </td>
                            <td align="right">
                                <input type="time" step="1" class="form-control" value="${ record.check_out??"Not Checkout Yet" }" name='attendance_checkout' readonly />
                                <small class="badge badge-danger ml-auto my-1" style="display:none; onClick="console.log($(this).prev())">reset</small>
                            </td>
                            <td>
                                <input type="number" class="form-control" value="${ record.of_site_hours }" step="any" name='attendance_site_hours' readonly />
                            </td>
                            <td>${ record.total_time == '' ? "N/A" : record.total_time }</td>
                            <td>${ record.type??"automatic" }</td>
                            <td class=" d-flex" width="160px">
                                ${ record.role != 'DEO' ? `
                                <button class="btn btn-warning edit_attendance_now" id="edit_salary_record"><i class="far fa-edit"></i> </button>
                                <button class="btn btn-success update_attendance_record" style="display:none;" type="button"><i class="fas fa-check"></i> </button> 
                                ` : ''} 
                                
                                <button class="btn btn-primary ml-2 check_late_comer">Re Check for late comer </button>
                           
                            </td>
                        </tr>
                    `)
                    
                });

                setTimeout(() => {
                    $("#recordTableMain").DataTable();
                }, 300);

            }
        })

    })

    $("body").delegate(".edit_attendance_now" , "click" , function(){
        var row = $(this).parent().parent();
        $(this).next().show(300)
        $(this).hide(300)
        row.children().eq(4).find("small").show("fast")
        console.log(row.children().eq(4).children());
        row.find('input').attr("readonly",function(_, attr){ return !attr})
    });

    $("body").delegate(".update_attendance_record" , "click" , function(){
    
        
        var mainButton   = $(this);
        var row          = $(this).parent().parent();
        var inputs       = row.find('input');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:"get",
            url: url +'/hrm/attendance/update/',
            data:{ 
                employee_id             :inputs[0].value,
                id                      :inputs[1].value,
                check_in                :inputs[2].value,
                check_out               :inputs[3].value,
                of_site_hours           :inputs[4].value,
            },
            success: function( data ) {
                row.find('input').attr("readonly",function(_, attr){ return !attr})
                mainButton.prev().hide().show()
                mainButton.hide(300);
                row.children().eq(4).find("small").hide("fast")
                row.children()[6].innerHTML=data;
            },
            error:function( res ){
                $("#error-list").html("")
                for (const key in res.responseJSON.errors) {
                    res.responseJSON.errors[key].forEach(error => {
                        $("#error-list").append(`<li><strong>${key} :</strong>${error}</li>`)                  
                    });
                }
                $("#error-modal").modal("show");
            }
        });
    })

    //Check Late Comer Again
    $("body").delegate(".check_late_comer" , "click" , function(){
    
        var mainButton   = $(this);
        var row          = $(this).parent().parent();
        var inputs       = row.find('input');
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:"get",
            url: url +'/hrm/attendance/check-late-comers/',
            data:{ 
                employee_id             :inputs[0].value,
                id                      :inputs[1].value,
                check_in                :inputs[2].value,
            },
            success: function( data ) {
                return swal({
                    title: "Success",
                    text: "Successfully sent to for re check",
                    icon: "success",
                    timer: 3000,
            });
            },
            error:function( res ){
                $("#error-list").html("")
                for (const key in res.responseJSON.errors) {
                    res.responseJSON.errors[key].forEach(error => {
                        $("#error-list").append(`<li><strong>${key} :</strong>${error}</li>`)                  
                    });
                }
                $("#error-modal").modal("show");
            }
        });
    })
    
    
    
    $("#filterButton").on("click", function() {

        let today = new Date();
        let from  = $("#from").val() == ""?"1971-01-01":$("#from").val();
        let to  = $("#to").val() == ""? today.getFullYear() + "-" + (today.getMonth()+1) + "-" + today.getDate() :$("#to").val();
        let employees  = $("#employees").val() ==""?"0":$("#employees").val();
        
        $.ajax({
            type: 'get',
            url: url + '/hrm/attendance/filter/' + from + "/" + to +
                "/" + employees,
            success: function(response) {

                var table = $('#attendanceRecordTable').DataTable();
                $("#attendanceRecordTableBody").html("")
                table.destroy();
                $('#attendanceRecordTableBody').empty();
                
                response.forEach((record, i) => {

                    $("#attendanceRecordTableBody").append(`
                        <tr>
                            <td> ${ i+1 } </td>
                            <td> ${ record.date } </td>
                            <td> ${ record.attendies } </td>
                            <td>
                                <button 
                                    data-toggle="modal"
                                    data-target="#detailsModal"
                                    data-filter="${ record.date }"
                                    class="btn btn-info fa fa-eye details"
                                >
                                </button>
                            </td>
                        </tr>
                    `)

                })
                setTimeout(() => {
                    $("#attendanceRecordTable").DataTable({
                        "order": []
                    });
                }, 300);
            }
        })

    })

    $("#printGeneralRecord").on("click",function(){

        let today = new Date();
        let from  = $("#from").val() == ""?"1971-01-01":$("#from").val();
        let to  = $("#to").val() == ""? today.getFullYear() + "-" + (today.getMonth()+1) + "-" + today.getDate() :$("#to").val();
        let employees  = $("#employees").val() ==""?"0":$("#employees").val();

        window.open( url + '/hrm/attendance/print/' + from + "/" + to + "/" + employees, '_blank' )
        
    })
    
    $("#printDetailedRecord").on("click",function(){

        let today = new Date();
        let from  = $("#from").val();
        let to  = $("#to").val();
        let employee  = $("#employee").val() ? $("#employee").val() : 0;

        window.open( url + '/hrm/attendance/print-details/' + from + "/" + to + "/" + employee, '_blank' )
        
    })
    
    $("#find").on("keyup",function(e){

        if (e.keyCode == 13) {
            let value = $("#find").val();

            $.ajax({
                url:url + '/hrm/attendance/find', 
                type:"post",
                data:{
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    value: value
                },
                success:function(response) {
                    if (response.success) {
                        $("#attendance-alert").hide(100);
                        $("#attendance-form").hide(100).show(300);
                        $("#employee-name").html(response.results.name)
                        $("#employee-father-name").html(response.results.f_name)
                        $("#employee-designation").html(response.results.designation.name)
                        $(".employee").val(response.results.id);
                    }
                    else{
                        $("#attendance-alert").show(300);
                        $("#attendance-form").hide(100);
                    }
                }
            })

        }
        
    })

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
                        $("#attendance-alert").hide(100);
                        $("#attendance-form").hide(100).show(300);
                        $("#employee-name").html(response.results.name)
                        $("#employee-father-name").html(response.results.f_name)
                        $("#employee-designation").html(response.results.designation.name)
                        $(".employee").val(response.results.id);
                    }
                    else{
                        $("#attendance-alert").show(300);
                        $("#attendance-form").hide(100);
                    }
                }
            })
        
    })
});