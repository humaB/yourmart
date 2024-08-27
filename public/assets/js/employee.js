$(document).ready( function () {

    let url = window.location.origin + localStorage.getItem('_path');
    
    $( '#location' ).on('change' , ()=> {
        $.ajax({
            type: "GET",
            url: url +'/hrm/department/'+$( "#location" ).val(),
            success: function( data ) {
                $( '#department, #department_id' ).html("");
                $( '#designation, #designation_id' ).html("");
                $( "#department,#department_id" ).append(`<option value="" disabled selected>Select from the following</option>`);
                for (let i = 0; i < data.length; i++) {
                    $( "#department,#department_id" ).append(`<option value='${ data[i].id }'>${ data[i].name }</option>`);
                }
            }
        });
    });


    $("#rfid").on("change",function(){
        $("#rfid").attr("readonly",true)
    })

    $( "#department" ).on( "change" , () =>{
        
        $.ajax({
            type: "GET",
            url: url +'/hrm/designation/'+$( "#department" ).val(),
            success: function( data ) {
                $( '#designation' ).html("");
                $( "#designation" ).append(`<option value="" disabled selected>Select from the following</option>`);
                for (let i = 0; i < data.length; i++) {
                    $( "#designation" ).append(`<option value='${ data[i].id }'>${ data[i].name }</option>`);
                }
            }
        });
    });

    $( "#depBtn" ).on("click", ()=> {

        if( $( '#department_name' ).val() == ''){
            $('#dep_danger').show(200)
            $('#dep-text').html( 'Please fill the required fields' )
            return
        }

        $.ajax({
            type: "GET",
            url: url +'/hrm/department/new/'+$( "#department_name" ).val(),
            data : {
                company : $('#company_id').val()
            },
            success: function( data ) {
                if( data.danger ){
                    $('#dep_danger').show(200)
                    $('#dep-text').html( data.danger )
                    return
                }   
               //
               $( "#department" ).html('');
                $('#dep_danger' ).hide();
                $( '#dep_added' ).show();

                data.forEach( response => {
                    $( "#department_id" ).append(`<option value='${ response.id }'>${ response.name }</option>`);
                    $( "#department" ).append(`<option value='${ response.id }'>${ response.name }</option>`);
                });

                $('#dep_added').hide(7000);
                $("#department_name").val('')
            }
        });
    })

    $( "#desBtn" ).on("click", ()=> {
 
        if($('#department_id').val() === null || $('#designation_name').val() == ''){
            $('#des_danger').show(200)
            $('#des-text').html( 'Please fill the required field first' )
            return
        }
        $.ajax({
            type: "GET",
            url: url +'/hrm/designation/new/'+ $("#department_id").val() + '/' + $("#designation_name").val(),
            success: function( data ) {
                if( data.danger){
                    $('#des_danger').show(200)
                    $('#des-text').html( data.danger )
                    return
                }   
               //
               $( "#designation" ).html('');
                $('#des_danger').hide();
                $('#des_added').show();

                data.forEach( response => {
                    $( "#designation" ).append(`<option value='${ response.id }'>${ response.name }</option>`);
                });

                $('#des_added').hide(7000);
                $("#designation_name").val('')
            }
        });
    })

    $( "#locBtn" ).on("click", ()=> {

        if($("#location_name").val() == ''){
            $('#loc_danger').show(200)
            $('#loc-text').html( 'Please add company name first')
            return
        }

        $.ajax({
            type: "GET",
            url: url +'/hrm/location/new/'+ $("#location_name").val(),
            success: function( data ) {
                if( data.danger){
                    $('#loc_danger').show(200)
                    $('#loc-text').html( data.danger )
                    return
                }   
               //
               $( "#location" ).html('');
                $('#loc_danger').hide();
                $('#loc_added').show();
 
                data.forEach( response => {
                    $( "#location" ).append(`<option value='${ response.id }'>${ response.name }</option>`);
                });

                $('#loc_added').hide(7000);
                $("#location_name").val('')
            }
        });
    })

    $( "#employeeImage" ).on("change", ()=> {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
        }
    })

   $( "#employeeCnicFront" ).on("change", ()=> {
        var output = document.getElementById('output-1');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
        }
    })

    $( "#employeeCnicBack" ).on("change", ()=> {
        var output = document.getElementById('output-2');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
        }
    })

    // Employee Popup Model

    $("body").delegate(".see_information" , "click" , function(){
        let employee_id = $(this).attr("eid");
        
        $.ajax({
            type: "GET",
            url: url +'/hrm/employees/' + employee_id ,
            success: function( data ) {

              $( '#employee' ).val( data.id )
              $( '#employee_weekly' ).val( data.id )
              $( '#c-department' ).val( data.department.id )
              $( '#c-location' ).val( data.location.id )
              

              $( '#employee_id' ).html( '<b> GCH-EM-'+ data.id +'</b>' );  
              $( '#name' ).html( data.name );
              $( '#f_name' ).html( data.f_name );
              $( '#dob' ).html( data.dob );
              $( '#cnic' ).html( data.cnic );
              $( '#contact' ).html( data.contact );
              $( "#outStandingLoan" ).html( data.RemainingLoan )
              if( data.profile == ''){
                $( '#employee_image' ).attr( 'src', url + '/assets/img/blank_image.jpg' );
              }
              else{
                $( '#employee_image' ).attr( 'src', url + '/storage/uploads/employee/profile/' + data.profile );
              }

              if( data.cnic_front == ''){
                $( '#cnic_front' ).attr( 'src', url + '/assets/img/blank_image.jpg' );
              }
              else{
                $( '#cnic_front' ).attr( 'src', url + '/storage/uploads/employee/cnic_front/' + data.cnic_front );
                $( '#cnic_front_href' ).attr( 'href', url + '/storage/uploads/employee/cnic_front/' + data.cnic_front );
              }

              if( data.cnic_back == ''){
                $( '#cnic_back' ).attr( 'src', url + '/assets/img/blank_image.jpg' );
              }
              else{
                $( '#cnic_back' ).attr( 'src', url + '/storage/uploads/employee/cnic_back/' + data.cnic_back );
                $( '#cnic_back_href' ).attr( 'href', url + '/storage/uploads/employee/cnic_back/' + data.cnic_back );

              }

              $( '#address' ).html( data.address );
              $( '#E-department' ).html( data.department.name );
              $( '#E-designation' ).html( data.designation.name );
              $( '#E-location' ).html( data.location.name );
              $( '#hiring-date' ).html( data.hiring_date );
              $( '#reference' ).html( data.reference );
              $( '#salary' ).html( data.salary );
              $( '#paid_leaves' ).html( data.paid_leaves );
              $( '#hours' ).html( data.working_hour );
              $( '#blood_group' ).html( data.blood_group );  
              $( '#statusshow').html( data.status );  
              $( '#shiftAssigned').html( data.shift.shift_name.name );  
                  
            }
        });
    });

    $( "#employeeSalaryBtn" ).on('click', () => {
   
        window.open( url + '/hrm/salary/record/employees/' + $( '#employee' ).val() , "_blank" );
    })

    $( "#employeePrintBtn" ).on('click', () => {
    
        window.open( url + '/hrm/employees/print/details/' + $( '#employee' ).val() , "_blank" );
    })

    //Salary Popup Form Submit
    $("body").delegate(".generate_salary" , "click" , function(){
        advanceLoanCalculations();
    });

    const advanceLoanCalculations = () => {
        let employeeId = $( '#employee' ).val();
       
        $.ajax({
            type: "GET",
            url: url + '/hrm/employees/salary/advance-loan/' + employeeId,
            success: function(data) {
               
                //setting values
                $( '#c-advance' ).val(  data[0].advance?.[0]?.t_amount ?? 0 );
                $( '#c-installment' ).val( data[0].loans?.[0]?.installment ?? 0 );
                $( '#c-salary-type' ).val( data[0].salary_type ?? 'Cash' );
                $( '#c-salary' ).val( data[0].salary ?? 0);
                $( '#c-loan-id' ).val( data[0].loans?.[0]?.id ?? 0);
                $( '#c-advance-id' ).val( data[0].advance?.[0]?.id ?? 0 );

                let advance     = data[0].advance[0].t_amount ?? 0
                let installment = data[0].loans[0].installment ?? 0

                let total = parseFloat( advance ) + parseFloat( installment );
                $( '#c-deductation' ).val(total);
            }
        });
    };

    //On Tax Change
    $( "#c-tax" ).on("keyup" , function(){
    
        if($(this).val() != ''){

            let tax = parseFloat($(this).val());
            let advance = parseFloat( $( '#c-advance' ).val() );
            let installment = parseFloat( $( '#c-installment' ).val() );
        
            let total = tax + advance + installment;
            $( '#c-deductation' ).val( total );
        }
        else if($(this).val() == '') $(this).val(0)

    });

    //Salary Save Button
    $( "#currentSalaryBtn" ).on('click', ()=>{

        if( $("#c-working-days").val() == ""  || $("#c-tax").val() == "" || $("#c-deductation").val() == "" || $("#c-incentive").val() == "" ){
            $('#sal_warning').hide(100).show(200)
            $('#sal-text').html( 'Please fill the required fields' )
            return
        }

        if( $( "#c-working-days" ).val() > 31 ){
            $( '#sal_warning' ).hide(100).show(200)
            $( '#sal-text' ).html( 'Add working days between 0 to 31' )
            return
        }


        $.ajax({
            type: "GET",
            url: url +'/hrm/salary/employee',
            data : {
                location    : $( '#c-location' ).val(),
                department  : $( '#c-department' ).val(),
                month       : $( '#c-month' ).val(),
                employee    : $( '#employee' ).val(),
                salary      : $( '#c-salary' ).val(),
                salary_type : $( '#c-salary-type').val(),
                days        : $( '#c-working-days' ).val(),
                incentive   : $( '#c-incentive' ).val(),
                tax         : $( '#c-tax' ).val(),
                advance     : $( '#c-advance' ).val(),
                installment : $( '#c-installment' ).val(),
                deducted    : $( '#c-deductation' ).val(),
                loan_id     : $( '#c-loan-id' ).val(),
                advance_id  : $( '#c-advance-id' ).val(),
            },
            success: function( data ) {
               
                if( data.warning){
                    $('#sal_warning').hide(100).show(200)
                    $('#sal-text').html( data.warning )
                    return
                }   
               //
                $('#sal_warning').hide();
                $('#sal_added').show();
 
                $('#sal_added').hide(10000);
                $( '#c-incentive' ).val(0)
                $( '#c-tax' ).val(0),
                $( '#c-deductation' ).val(0)
                advanceLoanCalculations();
            }
        });
    })

    $("#c-month").on("change",()=>{
        var month = $("#c-month").val().slice( $("#c-month").val().indexOf('-')+1, $("#c-month").val().length );
        var year = $("#c-month").val().slice( 0,$("#c-month").val().indexOf('-') );
        var daysInMonth = new Date(year, month, 0).getDate();
        $("#c-working-days").val(daysInMonth)
    })
    $( "#employeeRecordLoanBtn" ).on('click', ()=> {
        window.open( 'loans/transactions/records/loan/employee/pdf/'+ $( '#employee' ).val() , "_blank" );
    })

    // Edit Button
    $( "#employeeEditBtn" ).on('click', ()=> {
        window.open( url + '/hrm/employees/edit/'+ $( '#employee' ).val() , "_blank" );
    })

    //Department Wise Report
    $('#departmentWiseReport').on('click', () => {
        let department = $('#department').val() === null ? 0 : $('#department').val();
        let location = $('#location').val() === null ? 0 : $('#location').val();
        let status = $('#statusFilter').val() === null ? 0 : $('#statusFilter').val();

        if( location == 0 ){
            window.open( url + `/hrm/employees/department-wise/status/${status}` , "_blank" );
            return
        }
        window.open( url + `/hrm/employees/department-wise/location/${location}/department/${department}/status/${status}` , "_blank" );

    })

    //*********Loan Popup */

    //return type on change
    $( '#loan_return' ).on('change' , ()=>{
        let value = $( '#loan_return' ).val()
        if(value == 'Monthly'){
            $( '#installment_div').show(300);
            $( '#return_date_div').hide(300);
        }
        else if(value == 'OneTime'){
            $( '#installment_div').hide(300);
            $( '#return_date_div').show(300);
        }
    });

    $( '#employeePopUpLoanBtn' ).on('click', ()=> {

        if( $( "#loan_amount" ).val() == '' ||  $( "#loan_reason" ).val() == '' ){
            $( "#loan_warning" ).show(200);
            $( "#loan-text" ).html('Please fill the required fields first');
            return
        }

        if( $('#loan_return').val() == 'Monthly' && $('#installment').val() == ''){
            $( "#loan_warning" ).show(200);
            $( "#loan-text" ).html('Please add installment');
            return;
        }

        if( $( '#type').val() == 'Loan' && $('#loan_return').val() == 'OneTime' && $('#return_date').val() == ''){
            $( "#loan_warning" ).show(200);
            $( "#loan-text" ).html('Please add return date');
            return;
        }

        $( "#loan_warning" ).hide(400);

        $.ajax({
            type: "GET",
            url: url +'/hrm/loans/add',
            data : {
                amount         : $( '#loan_amount' ).val(),
                reason         : $( '#loan_reason' ).val(),
                type           : $( '#type' ).val(),
                return         : $( '#loan_return' ).val(),
                employee_id    : $( '#employee' ).val(),
                installment    : ( $( '#installment' ).val() ==  '' ) ? 0 : $( '#installment' ).val(),
                return_date    : ( $( '#return_date' ).val() ==  '' ) ? '0000-00-00' : $( '#return_date' ).val()
            },
            success: function( data ) {
                $( "#loan_added" ).show(200);
                $( "#loan_amount" ).val('');
                $( "#loan_reason" ).val('')
                $( "#installment" ).val('')
                $( "#loan_added" ).hide(3000);

            }
        });
    });

    //reset on click
    $("#Reset").on('click', () =>{
         window.location = url +'/hrm/employees' ;
    })

   // Filter Button
   $('#filter').on("click", ()=> {
    if ($('#location').val() === null) {
        $( '#filter_alert' ).show(300)
        return;
    }
    $( '#filter_alert' ).hide(500)
    var data = {
        location: $('#location').val(),
        departrment: $('#department').val()
    }
    filterEmployee(data); 
})

const filterEmployee = (data) => {
    window.location = url +'/hrm/employees/filter/' + data.location + '/' + data.departrment;
}
   

// Edit Single Person Salary Start

const editSalaryDetail = () =>{
    
    $.ajax({
        type: "GET",
        url: url +'/hrm/salary/employee-salary-record/'+$("#employee").val(),
        success: function( data ) {
            $("#edit_salary_table").html("");
            data.forEach( response => {
                $("#edit_salary_table").append(`
                    <tr>    
                           
                        <td class="py-2"><input value="${response.month}" class="form-control" readonly required /></td>
                        <td class="py-2"><input value="${response.working_days}" class="form-control" readonly required /></td>
                        <td class="py-2"><input value="${response.tax}" class="form-control" readonly required /></td>
                        <td class="py-2"><input value="${response.incentive??0}" class="form-control" readonly required /></td>
                        <td class="py-2"><input value="${response.deducted_amount}" class="form-control" readonly required /></td>
                        <td>
                        <input type="hidden" value="${response.employee_id}" class="form-control" readonly required />
                        <input type="hidden" value="${response.id}" class="form-control" readonly required />
                        <input value="${response.given_amount??0}" class="form-control" readonly required />
                        </td>
                        <td class="py-2" width="100px">
                            <button class="btn btn-warning edit_salary_now" id="edit_salary_record"><i class="far fa-edit"></i> </button>
                            <button class="btn btn-success update_salary_record" style="display:none;" type="button"><i class="fas fa-check"></i> </button> 
                        </td>
                    </tr>
                `) 

            });
        }
    });
};

$("body").delegate(".edit_salary_now" , "click" , function(){
    var row = $(this).parent().parent();
    $(this).next().show(300)
    $("#edit_salary_added").hide(300)
    row.find('input').attr("readonly",function(_, attr){ return !attr})
})

$("body").delegate(".update_salary_record" , "click" , function(){
    var mainButton = $(this);
    var row = $(this).parent().parent();
    var inputs = row.find('input');

    $.ajax({
        type: "GET",
        url: url +'/hrm/salary/employee-salary-record/',
        data:{ 
            month:inputs[0].value,
            days:inputs[1].value,
            tax:inputs[2].value,
            incentive:inputs[3].value,
            deducted:inputs[4].value,
            employee_id:inputs[5].value,
            id:inputs[6].value,
        },
        success: function( data ) {
            $("#edit_salary_added").hide().show()
            row.find('input').attr("readonly",function(_, attr){ return !attr})
            mainButton.hide(300);
            editSalaryDetail();
        }
    });
})


$("#editSalaryBtn").on('click',()=>{
    editSalaryDetail();
})

// Edit Single Person Salary End


    $("#employeestatusBtn").on('click' , ()=>{
        
        if( $("#statusEmployee").val() == 0 ){
             $('#select_alert').show( 300 );
             return;
        }
            $.ajax({
                type: "GET",
                data:{ 
                    id     : $('#employee' ).val(),
                    status : $('#statusEmployee option:selected' ).val(),
                },
                url : url +'/hrm/employees/status/'+$("#employee").val() + '/' + $("#statusEmployee option:selected").val() ,
                success: function( data ) {
                    $('#select_alert').hide(300);
                    $('#status-change-success').show(300);

                }
            }); 
        
        
        
    })

    //Attachments

    $("#employeeAttachmentBtnList").on('click', () => {
        $.ajax({
            type: "GET",
            url : url +'/hrm/employees/attachments/'+$("#employee").val(),
            success: function( data ) {
                $("#attachment_table").html("");
                data.forEach( response => {
                    $("#attachment_table").append(`
                        <tr>    
                               
                            <td class="py-2">${response.type}</td>
                            <td class="py-2"><a href="${url + "/storage/uploads/employee/attachments/" + response.attachment}" target="_blank">${response.attachment}</a></td>
                        </tr>
                    `) 
    
                });
            }
        }); 
    })

    //WeekOFF Days
    $("#employeeWeekDaysOFFList").on('click', () => {
        $.ajax({
            type: "GET",
            url : url +'/hrm/employees/weekly-off/'+$("#employee").val(),
            success: function( data ) {
                data.mon == 1 ? $("#mon").prop('checked', true) : '';
                data.tue == 1 ? $("#tue").prop('checked', true) : '';
                data.wed == 1 ? $("#wed").prop('checked', true) : '';
                data.thu == 1 ? $("#thu").prop('checked', true) : '';
                data.fri == 1 ? $("#fri").prop('checked', true) : '';
                data.sat == 1 ? $("#sat").prop('checked', true) : '';
                data.sun == 1 ? $("#sun").prop('checked', true) : '';
            }
        }); 
    })

    $("#employeeAttachmentBtn").on('click' , ()=>{
        
        if( $("#attachmentTypeEmployee").val() == 0 ||  $("#attachmentEmployee").val() == '' ){
             $('#attachment_select_alert').show( 300 );
             return;
        }

        const fd = new FormData();

        fd.append('employee_id', $('#employee' ).val())
        fd.append('type', $('#attachmentTypeEmployee option:selected' ).val() )
        fd.append('attachment',  $('#attachmentEmployee')[0].files[0])

        $('#attachment_select_alert').hide( 300 );

            $.ajax({
                type: "POST",
                headers : {
                    "X-CSRF-TOKEN": $('meta[name=csrf-token]').attr('content')
                },
                data: fd,
                url : url +'/hrm/employees/attachments' ,
                processData: false,
                contentType: false,
                success: function( data ) {
                    $('#attachment-change-success').show(300);
                    $('#attachmentTypeEmployee').val(0);
                    $("input[type=file]").val('');
                }
            }); 
    })
    //Assign Asset to employee
    $("#employeeAssetBtn").on('click' , ()=>{
        
        if( $("#employeeAssetName").val() == 0 ){
             $('#select_alert-assets').show( 300 );
             return;
        }
            $.ajax({
                type: "GET",
                data:{ 
                    id     : $('#employee' ).val(),
                    asset : $('#employeeAssetName' ).val(),
                },
                url : url +'/hrm/employees/assets/'+$("#employee").val() + '/' + $("#employeeAssetName").val() ,
                success: function( data ) {
                    $('#select_alert-assets').hide(300);
                    $('#status-change-success-assets').show(300);
                    $('#employeeAssetName' ).val('')
                }
            }); 
        
    })

    $("#employeeHobbieBtn").on('click' , ()=>{
    
        if( $("#employeeHobbieName").val() == '' ){
             $('#select_alert-hobby').show( 300 );
             return;
        }
            $.ajax({
                type: "GET",
                data:{ 
                    id     : $('#employee' ).val(),
                    name : $('#employeeHobbieName' ).val(),
                },
                url : url +'/hrm/employees/hobbies/'+$("#employee").val() ,
                success: function( data ) {
                    $('#select_alert-hobby').hide(300);
                    $('#status-change-success-hobby').show(300);
                    $('#employeeHobbieName' ).val('')

                }
            }); 
        
        
        
    })
    //Salary Slip For Month
    $( '#salarySlipForMonth' ).on('click', () => {

        let month = $( '#selected_month' ).val();

        if( month == ''){
            $('#month_error').show(200)
            setTimeout(() => {
                $('#month_error').hide(200)
            }, 5000);
            return;
        }
        
        window.open( 'employees/print/salary-slip/'+ $( '#employee' ).val() + '/month/' + month , "_blank" );
    });

    //On Advance select disable return type
    $( '#type' ).on('change' , ()=>{
        let value = $('#type').val()
        if( value == 'Advance' ) {
            $( '#loan_return' ).html();
            $( '#loan_return' ).html(`
                <option value="OneTime" >One Time</option>
            `);
            $( '#return_date_div' ).hide(300)
        }
        else{
            $( '#loan_return' ).html();
            $( '#loan_return' ).html(`
                <option value="OneTime" >One Time</option>
                <option value="Monthly" >Monthly</option>
            `);
        }
    });

//Salary Increment 
 $("#increment_amount").on('click', () => {
     let amount = $("#increment_amount_value").val();
     let employeeId = $( '#employee' ).val();

     if( amount == ''){
        $('#dep_danger').show(200)
        return
    }

    $.ajax({
        type: "GET",
        url: url + `/hrm/employees/${employeeId}/salary/increment/${amount}`,
        success: function( data ) {
            $('#dep_added').show(200)
        }
    });
 })

 //Assign Shift
 $("#employeeAssignShiftBtn").on('click' , ()=>{
    
    if( $("#employeeShift").val() == 0 ){
         $('#select_alert-shift').show( 300 );
         return;
    }
        $.ajax({
            type: "GET",
            data:{ 
                id     : $('#employee' ).val(),
                shift : $('#employeeShift option:selected' ).val(),
            },
            url : url +'/hrm/employees/shifts/'+$("#employee").val() + '/' + $("#employeeShift option:selected").val() ,
            success: function( data ) {
                $('#select_alert-shift').hide(300);
                $('#status-change-success-shift').show(300);
            }
        }); 
    
})

$("#leave_filter_btn").on('click', () => {
    const from = $("#leave_from_date").val();
    const to   = $("#leave_from_to").val();
    const id   = $("#employee").val();

    window.open( 'employees/print/leaves/leave-record/'+ $( '#employee' ).val() + '/from/' + from +'/to/'+ to, "_blank" );
})

}); // Document ready end


