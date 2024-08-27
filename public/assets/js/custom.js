/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 * 
 */

"use strict";

$(document).ready( function () {
   
    let url = window.location.origin + localStorage.getItem('_path') ;
  
    if(localStorage.getItem('_token') === null ){
        $.ajax({
            type: "GET",
            url: url +'/generate_token',
            success: function( data ) {
                localStorage.setItem('_token', data.response.access_token )
            }
        });
    }
    $('.modal').appendTo('body');   
})