$(document).ready(function(){
   
   $('#bt_connexion').click(function(e){
   e.preventDefault();
   $('#feed_connexion').html("Chargement...<img src='web/images/load.gif'>");
    if ($('#mail').val()=='' ||  $('#password').val()=='') {
       if ($('#mail').val()=='')
           {
        $('#feed_connexion').html("<div class='erreur'>Veuillez remplir votre mail svp</div>");
           }
           else
           {
         $('#feed_connexion').html("<div class='erreur'>Veuillez mentionnez votre mot de passe svp</div>");
           }
    }
    else
    {
    $.post('/connexion',{mail:$('#mail').val(),password:$('#password').val()},function(data){
        $('#feed_connexion').html(data);
    });
    }
   });
   
   $('#motdp').click(function(e){
    
    e.preventDefault();
    $('#mdpo').toggle('show');
   });
   
   $('#bt_mdpo').click(function(){
    
    $('#feed_mdpo').html("Chargement...<img src='web/images/load.gif'>");
    $.post('/mdpo',{email:$('#email').val()},function(data){
        $('#feed_mdpo').html(data);
    });
   });
});