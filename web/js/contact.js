$(document).ready(function(){
    
   $('#submit_btn').click(function(e){
    e.preventDefault();
            $('#feedcontact').html("Patientez svp ...<img src='web/images/load.gif'>");
    $.post('/contact',{nom:$('#name').val(),mail:$('#email').val(),telephone:$('#telephone').val(),message:$('#msg').val()},function(data){
        
        $('#feedcontact').html(data);
        });
    
   });
});