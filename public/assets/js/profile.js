$(document).ready( function () {

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

    $("#attendanceFilterButton").on("click",function(){
        
        let today = new Date();
        let from  = $("#from").val() == ""?"1971-01-01":$("#from").val();
        let to  = $("#to").val() == ""? today.getFullYear() + "-" + (today.getMonth()+1) + "-" + today.getDate() :$("#to").val();
        $('#attendanceRecordTable').DataTable().clear().destroy();
        $.ajax({
            type: 'get',
            url: 'attendance/filter/' + from + "/" + to,
            success: function(response) {

                response.forEach((record, i) => {

                    $("#attendanceRecordTableBody").append(`
                        <tr>
                            <td> ${ i+1 } </td>
                            <td> ${ record.date } </td>
                            <td> ${ record.check_in } </td>
                            <td> ${ record.check_out??"" } </td>
                            <td> ${ record.total_time } </td>
                        </tr>
                    `)

                    setTimeout(() => {
                        $("#attendanceRecordTable").DataTable();
                    }, 500);

                })
            }
        })
    })

    $("#printSalarySlip").on("click",function(){

        let month = $( '#month' ).val();

        if( month == ''){
            $('#month_error').show(200)
            setTimeout(() => {
                $('#month_error').hide(200)
            }, 5000);
            return;
        }
        
        window.open( 'salary-slip/' + month , "_blank" );

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
    $( "#employeePopUpLoanBtn" ).on('click', ()=>{
          
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
        
        
        $('#loan-table').DataTable().clear().destroy();

        $.ajax({
            type: "GET",
            url: 'loan/add',
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
                
                data.forEach( ( record , index ) => {

                    $("#loan-table tbody").append(`
                        <tr>
                            <td> ${ index + 1 } </td>
                            <th>${ record.reason }</th>
                            <th>${ record.t_amount }</th>
                            <th>${ record.created_at }</th>
                            <th>${ record.return_type }</th>
                            <th>${ record.status }</th>
                            <a href="#loan-transactions" class="btn btn-outline-dark fa fa-search view-transactions" appovedDate=${ record.approved_date ?? 'Not-Available' } did="${ record.id }"></a>
                        </tr>
                    `).hide().show('slow');
                    
                })

                setTimeout(() => {
                    $("#loan-table").DataTable();
                }, 500);
            }
        });
    });
    
    //*********Add transaction loan section

    $("body").delegate( ".view-transactions", 'click' , function(){
        
        $( '#loan_trans_data' ).html('');

        let loan_id = $(this).attr("did");   
        $( '#loan_id' ).val( loan_id );

        let approvel_date = $(this).attr("appoveddate");
        $( '#approvel_date' ).html( approvel_date );
        $("#loan_trans_detail_print_btn").attr("did",loan_id)

        $.ajax({
            type: "GET",
            url: 'transactions/'+loan_id,
            success: function( data ) { 
                $( '#loan_trans_data' ).html('');

                var total = 0;
                data.forEach( ( record , index ) => {
                    $("#loan_trans_data").append(`
                        <tr>
                            <td> ${ index + 1 } </td>
                            <td> ${ record.amount } </td>
                            <td> ${ new Date( record.created_at ) } </td>
                        </tr>
                    `).hide().show('slow');

                     total += parseInt(record.amount) ;
                })
                
                $( "#trans_total" ).html( total )
            }
        });
    });

    $( "#loan_trans_detail_print_btn" ).on('click', ()=> {
        let loan_id = $("#loan_trans_detail_print_btn").attr("did")
 
        window.open( 'loan/transactions/pdf/'+ loan_id , "_blank" );
    
    });

});