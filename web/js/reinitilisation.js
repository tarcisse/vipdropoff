$(document).ready(function(){
   
   $('#bt_res').click(function(e){
    e.preventDefault();
    if ($('#md1').val() != $('#md2').val()) {
        $('#feed_res').html("<div class='avertissement'>Les mots de passe saisis ne sont pas identiques</div>");
    }
    else if ($('#md1').val()=='' || $('#md2').val()=='') {
      $('#feed_res').html("<div class='erreur'>Les mots ne peuvent pas &ecirc;tre vides</div>");
    }
    else
    {
         $('#feed_res').html("Chargement...<img src='web/images/load.gif'>");
         $.post('/reinitialiser',{mdp:$('#md1').val(),token:$('#token').val()},function(data){
            $('#feed_res').html(data);
         });
    }
   });
});