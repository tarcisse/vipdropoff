$(document).ready(function(){
   $('#bt_insc').click(function(e){
      
      if ($('#nom').val()=='' ||
          $('#mail').val()=='' || 
          $('#password').val()==''  ||
           $('#codepostal').val()==''  ||
            $('#ville').val()==''  ||
             $('#mpays').val()==''  ||
              $('#adresse').val()==''  ||
               $('#mobile').val()=='')
      
   {
      $('#feed_insc').html("<div class='avertissement'>Veuillez remplir les champs obligatoires</div>");
      }
      else
      {
          $('#feed_insc').html("Chargement...<img src='web/images/load.gif'>");
         $.post('/inscription',{
                                  civilite:$('#civilite').val(),
                                  nom:$('#nom').val(),
                                  prenom:$('#prenom').val(),
                                    mail:$('#mail').val(),
                                    password:$('#password').val(),
                                    societe:$('#societe').val(),
                                   codepostal:$('#codepostal').val(),
                                   ville:$('#ville').val(),
                                    pays:$('#pays').val(),
                                   adresse:$('#adresse').val(),
                                    mobile:$('#mobile').val(),
                                    fixe:$('#fixe').val(),
                                    faxe:$('#faxe').val(),
                                    type:$('#type').val()
            },function(data){
            $('#feed_insc').html(data);
         });
      }
   });
});