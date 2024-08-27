$(document).ready( function () {

    let url = window.location.origin + localStorage.getItem('_path');

    $( '#loan-record-table' ).DataTable();
    //Filter by status
    $( "#loanFilter" ).on('click' , () => {
        if( $( '#status').val() === null ){
            $( '#filter_alert' ).show(300)
            return;
        }
       window.location = url +  '/hrm/loans/filter/status/' + $( '#status' ).val() ;
    });

    $('#closePopBtn').on('click' , () => {

        $('#search_filter_popup').val('');
        $('#loan-record-popup').html('');
        $( "#loan-detail-popup" ).hide();
        $('#loan_added').hide();
        $('#employee_name').html("");
        $( "#employee_id" ).val('');
        
    })       

    $('#search_filter_popup').keypress(function(event){
        if (event.keyCode==13){
        event.preventDefault();
        $('#reason').focus();
        }
    });

    $('#reason').keypress(function(event){
        if (event.keyCode==13){
        event.preventDefault();
        $('#amount').focus();
        }
    });

    $('#amount').keypress(function(event){
        if (event.keyCode==13){
        event.preventDefault();
        $('#return').focus();
        }
    });
    
    $('#return').keypress(function(event){
        if (event.keyCode==13){
        event.preventDefault();
        $('#loanSaveBtn').focus();
        }
    });
    // find loan Employee
    const findEmployeeForLoan = () => {

        if( $( "#search_filter_popup" ).val() == ''  ){
            $( "#alert" ).show(200)
            return ;
        }
            $.ajax({
                type: "GET",
                url: url + '/hrm/employees/filter/'+ $( "#search_filter_popup" ).val(),
                success: function( data ) {
                    if( data.status ){
                        $( "#alert_no_record" ).show(200)
                        return
                    }

                    $( "#alert" ).hide(200);
                    $( "#alert_no_record" ).hide(200);
                    
                    if( data.id ){
                        $( "#employee_id" ).val( data.id )
                        $('#employee_name').html(
                            ` <span> Submit loan approvel request for : <span><b><h4 class="dataname"> ${ data.employee_name } </h4> <b> </span> </span>`
                        )
                        $( "#loan-detail-popup" ).slideDown(200)
                        return
                    }

                    $( "#loan-table-popup" ).slideDown(200)
                    $( "#loan-detail-popup" ).slideDown(200)
                    $( "#loan-record-popup" ).html("")

                    $( "#employee_id" ).val(data[0].employee_id)
                  
                    $('#employee_name').html(
                        ` <span> Submit loan approvel request for : <span><b><h4 class="dataname"> ${  data[0].name }  </h4> <b> </span> </span>`
                    )

                    data.forEach( ( record , index ) => {
                            

                        $("#loan-record-popup").append(`
                            <tr>
                                <td> ${ index + 1 } </td>
                                <td> GCH-EM-${ record.employee_id } </td>
                                <td>${ record.name } </td>
                                <td>${ record.t_amount }</td>
                                <td><div class="badge badge-${ record.status == "Approved" ? "success" : record.status == "Pending" ? "warning" : "danger" }">${ record.status }</div></td>
                            </tr>
                        `).hide().show('slow');
                        
                    })
                }
            });
    }
    //Input Field Enter Key Press
    $( "#search_filter_popup" ).on('keydown' , (e)=> {
        let key = e.which;
        if(key == 13){
            findEmployeeForLoan();
        }
    });

    //return type on change
    $( '#return' ).on('change' , ()=>{
        let value = $( '#return' ).val()
        if(value == 'Monthly'){
            $( '#installment_div').show(300);
            $( '#return_date_div').hide(300);
        }
        else if(value == 'OneTime'){
            $( '#installment_div').hide(300);
            $( '#return_date_div').show(300);
        }
    });

    //Reset Button
    $( '#reset' ).on('click', ()=> {
        window.location = url +'/hrm/loans' ;
    })

    //Find Button
    $( '#search_filter_popup_btn' ).on('click' , ()=> {
        findEmployeeForLoan();
    }) 
    //search_filter_popup_btn END

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
                    $( "#search_filter_popup" ).val(response.results.cnic)

                    findEmployeeForLoan();
                    $('#employee_name').html(
                        ` <span> Submit loan approvel request for : <span><b><h4 class="dataname"> ${ response.results.name }, Designation : ${ response.results.designation.name }</h4> <b> </span> </span>`
                    )
                    $( "#loan-detail-popup" ).slideDown(200)
                    return
                }
                else{
                    $("#attendance-alert").show(300);
                    $("#attendance-form").hide(100);
                }
            }
        })
    
    })
        
    //Submit for approvel button
        $( "#loanSaveBtn" ).on('click', ()=>{
          
            if( $( "#reason" ).val() == '' || $( "#amount" ).val() == '' ){
                $( "#loan_form_alert" ).show(200)
                return
            }

            if( $('#return').val() == 'Monthly' && $('#installment').val() == ''){
                $( "#loan_form_alert" ).show(200)
                return;
            }

            if( $('#type').val() == 'Loan' && $('#return').val() == 'OneTime' && $('#return_date').val() == ''){
                $( "#loan_form_alert" ).show(200)
                return;
            }

            $.ajax({
                type: "GET",
                url: url + '/hrm/loans/add',
                data : {
                    employee_id : $( '#employee_id' ).val(),
                    reason      : $( '#reason' ).val(),
                    amount      : $( '#amount' ).val(),
                    return      : $( '#return' ).val(),
                    type        : $( '#type' ).val(),
                    installment : ( $( '#installment' ).val() ==  '' ) ? 0 : $( '#installment' ).val(),
                    return_date : ( $('#return_date').val() == '' ) ? '0000-00-00' : $('#return_date').val() 
                },
                success: function( data ) {
                   
                   
                    $('#loan_added').show(200);
                    $( "#loan_form_alert" ).hide(400);
                    $( "#loan-detail-popup" ).slideDown(200);
                    $( "#loan-record-popup" ).html("");
                    
                    data.forEach( ( record , index ) => {
                        $("#loan-record-popup").append(`
                            <tr>
                                <td> ${ index + 1 } </td>
                                <td> GCH-EM-${ record.employee_id } </td>
                                <td>${ record.name } </td>
                                <td>${ record.t_amount }</td>
                                <td><div class="badge badge-${ record.status == "Approved" ? "success" : record.status == "Pending" ? "warning" : "danger" }">${ record.status }</div></td>
                            </tr>
                        `).hide().show('slow');
                        
                    })

                    $( "#reason" ).val('');   
                    $( "#amount" ).val('');
                    $( "#installment" ).val(''); 
                }
            });
        });
    
    //*********Add transaction loan section

    $("body").delegate( ".loan_trans_info", 'click' , function(){
        //Hide all error on load
        
        $( "#loan_trans_amount" ).val('');
        $( "#trans_alert" ).hide();
        $( "#trans_warning" ).hide();
        $( "#trans_form" ).hide();
        $( '#loan_trans_data' ).html('');

        let loan_id = $(this).attr("lid");   
        $( '#loan_id' ).val( loan_id );

        let approvel_date = $(this).attr("appoveddate");
        $( '#approvel_date' ).html( approvel_date );

        $.ajax({
            type: "GET",
            url: url +'/hrm/loans/transactions/records/' + loan_id ,
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

    //Delete loan Request
    $(".delete_loan_request").on('click' , function(){
        let loan_id = $(this).attr("lid");   
        $( '#delete_loan_id' ).val(loan_id);
    });

    $( "#delete_loan_request" ).on('click', ()=> {
        let loan_id = $( '#delete_loan_id' ).val()
        
        $.ajax({
        type: "GET",
        url: url +'/hrm/loans/delete/' + loan_id,
        data : {
            amount  : $( '#loan_trans_amount' ).val(),
            loan_id : $( '#loan_id' ).val()
        },
        success: function( data ) {
            if(data.message){
                window.location.reload();
            }
         }
    });

    })

const loanPayment= () => {

    if( $( "#loan_trans_amount" ).val() == '' ){
        $( "#trans_form" ).hide(200)
        $( "#trans_alert" ).show(200)
        return
    }

    $.ajax({
        type: "GET",
        url: url +'/hrm/loans/transactions/add',
        data : {
            amount  : $( '#loan_trans_amount' ).val(),
            loan_id : $( '#loan_id' ).val()
        },
        success: function( data ) { 
            //if status rejected or pending
            if( data.status ){
                $( "#trans_form" ).hide(200)
                $( "#trans_warning" ).show(200);
                $( "#trans-text" ).html( data.status );
                return
            }

            $( "#trans_warning" ).hide(400);
            $( "#trans_alert" ).hide(200);
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
                
                 total += parseInt( record.amount ) ;
            })
            
            $( "#loan_trans_amount" ).val('');
            $( "#trans_form" ).show(200)
            $( "#trans_total" ).html( total )
        }
    });


}

    $( '#add_loan_payment' ).on('click', () => {
       loanPayment();
    });//#add_loan_payment

    $( "#loan_trans_amount" ).on('keydown' , (e)=> {
        let key = e.which;
        if(key == 13){
            loanPayment();
           }
    });


    $( "#loan_trans_detail_print_btn" ).on('click', ()=> {
 
        window.open( url + '/hrm/loans/transactions/records/loan/pdf/'+ $( '#loan_id' ).val() , "_blank" );
    
    });

    //On Advance select disable return type
    $( '#type' ).on('change' , ()=>{
        let value = $('#type').val()
        if( value == 'Advance' ) {
            $( '#return' ).html();
            $( '#return' ).html(`
                <option value="OneTime" >One Time</option>
            `);
            $( '#return_date_div' ).hide(300)
        }
        else{
            $( '#return' ).html();
            $( '#return' ).html(`
                <option value="OneTime" >One Time</option>
                <option value="Monthly" >Monthly</option>
            `);
        }
    });

})//Document Ready closed
