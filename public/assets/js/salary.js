$(document).ready( function () {

let url = window.location.origin + localStorage.getItem('_path');

$( '#location' ).on('change' , ()=> {
    $.ajax({
        type: "GET",
        url: url +'/hrm/department/'+$( "#location" ).val(),
        success: function( data ) {
            $( '#department' ).html("");
            $( "#department" ).append(`<option value="" disabled selected>Select from the following</option>`);
            for (let i = 0; i < data.length; i++) {
                $( "#department" ).append(`<option value='${ data[i].id }'>${ data[i].name }</option>`);
            }
        }
    });
});

$( '#fetchBtn' ).on('click', ()=> {
                      
    if( $("#department").val() === null  || $("#location").val() === null || $("#month").val() === undefined ){
        $('#select_alert').show(200)
        return
    }

    $('#select_alert').hide(200)
  
        $( '#data' ).html("");
        
        $.ajax({
            type: "GET",
            url: 'salary/employees/' + $("#location").val() + '/'+$("#department").val() + '/' + $("#month").val(),
            success: function(data) {

                if( data.warning ){
                    $('#warning').show(200)
                    $('#warning-text').html( data.warning )
                    setTimeout(() => {
                        $('#warning').hide(200)
                    }, 5000);
                    $("#employees_salary").hide(200);
                    $( '#generateBtn' ).hide( 300 )
                    return 
                }

                    $('#warning').hide(200)

                    $("#employees_salary").slideDown(200)
                    var month = $("#month").val().slice( $("#month").val().indexOf('-')+1, $("#month").val().length );
                    var year = $("#month").val().slice( 0,$("#month").val().indexOf('-') );            
                
                    var daysInMonth = new Date(year, month, 0).getDate();
                    data.forEach( ( record , index ) => {

                        const installment  = record.loans[0] ? record.loans[0].installment : 0
                        const advance = record.advance[0] ? record.advance[0].t_amount : 0

                        let deduction = parseFloat(installment) + parseFloat(advance)

                        $("#data").append(`
                            <tr>
                                <td> ${ index + 1 } </td>
                                <td>
                                    <input type="hidden" name="employee[]" value="${ record.id }">
                                    <input type="hidden" name="advance_id[]" value="${ record.advance[0] ? record.advance[0].id : 0 }">
                                    <input type="hidden" name="loan_id[]" value="${ record.loans[0] ? record.loans[0].id : 0 }">
                                    <input type="hidden" name="salary_type[]" value="${ record.salary_type }">
                                    ${ record.name }
                                </td>
                                <td> ${ record.designation }</td>
                                <td> ${ record.salary }</td>
                                <td><input type="number" min="0" pattern="\\d*" value="${ daysInMonth }" name="days[]" min="0" max="31" class="form-control" required/></td>
                                <td><input type="number" min="0" pattern="\\d*" name="incentive[]" value="0" class="form-control" required/></td>
                                <td><input type="number" min="0" pattern="\\d*" name="tax[]" value="0" class="form-control tax" required/></td>
                                <td><input type="number" readonly min="0" pattern="\\d*" name="advanced[]" value="${ record.advance[0] ? record.advance[0].t_amount : 0 }" class="form-control advance" required/></td>
                                <td><input type="number" readonly min="0" pattern="\\d*" name="installment[]" value="${ record.loans[0] ? record.loans[0].installment : 0 }" class="form-control installment" required/></td>
                                <td><input type="number" min="0" pattern="\\d*" name="deducted[]" value="${ deduction }" class="form-control deduction" required/></td>
                    
                            </tr>
                        `).hide().show('slow');
                        
                    })

                    $( '#generateBtn' ).slideDown( 300 )
            }
        });
});

//On tax change 
$("body").delegate(".tax" , "keyup" , function(){
    
    if($(this).val() != ''){
        let row = $(this).parent().parent()
        let tax = parseFloat($(this).val());
        let advance = parseFloat(row.find('.advance').val());
        let installment = parseFloat(row.find('.installment').val());
    
        let total = tax + advance + installment;
        let deduction = row.find('.deduction').val( total );
    }

})

//---------------- Record Page For Salary

$( '#fetchRecordBtn' ).on('click', ()=> {
   
    if( $("#month").val() === "" || $("#location").val() === null ){
        $('#select_alert').show(200);
        return
    }

    if( $("#department").val() === null ) {
        var department = 0;
      
    }
    else{
        var department = $("#department").val();
    }
    var location   = $("#location").val();

    $('#select_alert').hide(200);

    $.ajax({
        type: "GET",
        url: 'record/'+ location +'/'+ department + '/' + $("#month").val(),
        success: function(data) {

            if(data.warning){
                $('#warning').show(200)
                $('#warning-text').html( data.warning )
                $("#record").hide()
                setTimeout(() => {
                    $('#warning').hide(200)
                }, 4000);
                return 
            }

            $('#warning').hide(400)
            $("#employees_salary_record").slideDown( 200 )

            $("#record").html("");
            data.forEach( ( record , index ) => {
                $("#record").append(`
                <tr>
                    <td>${ index + 1 }</td>
                    <td>${ record.employee.name }</td>
                    <td>${ record.employee.salary }</td>
                    <td> <input value="${ record.working_days }" type="number" readonly class="form-control" required /> </td>
                    <td> <input value="${ record.tax ?? 0 }" type="number" readonly class="form-control" required/> </td>
                    <td> <input value="${ record.incentive ?? 0 }" type="number" readonly class="form-control" required/> </td>
                    <td>
                        <input value="${ record.deducted_amount ?? 0 }" type="number" readonly class="form-control" required/> 
                        <input value="${ record.employee.id }" type="hidden" readonly class="form-control" /> 
                        <input value="${ record.id }" type="hidden" readonly class="form-control" /> 
                    </td>
                    <td class="py-2" width="120px">
                        <button class="btn btn-warning edit_salary_now" id="edit_salary_record"><i class="far fa-edit"></i> </button>
                        <button class="btn btn-success update_salary_record" style="display:none;" type="button"><i class="fas fa-check"></i> </button> 
                    </td>
                </tr>
                `).hide().show( 'slow' );
            });
            // -- Show Button
            $( '#printBtn' ).slideDown(300)    
        }
    });
})


// Edit salary record code Start

const editSalaryDetail = () =>{
    $.ajax({
        type: "GET",
        url: url +'/hrm/salary/employee-salary-record/'+$("#employee").val(),
        success: function( data ) {
            
            data.forEach( response => {
                $("#edit_salary_table").html("");
                $("#edit_salary_table").append(`
                    <tr>   
                        <td>${ index + 1 }</td>
                        <td>${ record.employee.name }</td>
                        <td>${ record.employee.salary }</td>
                        <td> <input value="${ record.working_days }" type="number" readonly class="form-control" required/> </td>
                        <td> <input value="${ record.tax }" type="number" readonly class="form-control" required/> </td>
                        <td> <input value="${ record.incentive }" type="number" readonly class="form-control" required/> </td>
                        <td>
                            <input value="${ record.deducted_amount }" type="number" readonly class="form-control" required/> 
                            <input value="${ record.employee.id }" type="hidden" readonly class="form-control" /> 
                            <input value="${ record.id }" type="hidden" readonly class="form-control" /> 
                        </td>
                        <td class="py-2" width="120px">
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
});

$("body").delegate(".update_salary_record" , "click" , function(){
    
    var mainButton   = $(this);
    var row          = $(this).parent().parent();
    var inputs       = row.find('input');

    if(inputs[0].value == '' || inputs[1].value == '' || inputs[2].value == '' || inputs[3].value == ''){
        $('#warning').show(200)
        $('#warning-text').html( 'Please fill all the required fields' )
        setTimeout(() => {
            $('#warning').hide(200)
        }, 5000);
        return;
    }

    $.ajax({
        type: "GET",
        url: url +'/hrm/salary/employee-salary-record/',
        data:{ 
            days             :inputs[0].value,
            tax              :inputs[1].value,
            incentive        :inputs[2].value,
            deducted         :inputs[3].value,
            employee_id      :inputs[4].value,
            id               :inputs[5].value,
        },
        success: function( data ) {
            $("#edit_salary_added").hide().show()
            row.find('input').attr("readonly",function(_, attr){ return !attr})
            mainButton.hide(300);
            editSalaryDetail();
            setTimeout(() => {
                $("#edit_salary_added").hide(300)
            }, 3000);
        }
    });
})

// Edit salary record code End

//Record page print btn
$( '#printBtn' ).on('click',()=>{  

    if( $("#month").val() === "" || $("#location").val() === null ){
        $('#select_alert').show(200);
        return
    }
    
    let department;
    let location;

    ( $("#department").val() === null ) ? department = 0 : department = $("#department").val();
        
    if( $("#location").val() != null )  location   = $("#location").val();

    window.open( 'record/print/'+ location +'/'+ department + '/' + $("#month").val() , "_blank" );
})

});