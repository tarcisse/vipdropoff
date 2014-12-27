$(document).ready(function(){
    
  /*******relative à la photo********/
   /*****************************************************************************************/
               $('#type-support').change(function(){
        $('.type-suport').hide();
        $("#"+$('#type-support').val()).show();
        suport=$('#type-support').val();
      });
      
      
      $('#annonce_video').keyup(function(){
        $('#feed-video').html("<img src='web/images/load.gif'>");
        $.post('/apercu-video',{mignature:$('#annonce_video').val()},function(data){
            $('#feed-video').html(data);
        });
      });
          
          
          /***********************************/
      $('.fich_img').click(function(){
	// alert('1');
	    a=this.alt;
	    $('#file'+a).trigger('click');
	});
	
	 $('.fich_cach').change(function(){
	    a=this.alt;
	    $('#frame-feed'+a).html("<img src='web/images/load.gif'>");
	    $('#submit'+a).trigger('click');
            // alert('2');
	 });
//========================================================================================//


$('#bt_enr').click(function(e){
   e.preventDefault();
   $('#feed_enr').html("<img src='web/images/load.gif'>");
   $.post('/enrvoiture',{marque:$('#marque').val(),
          matricule:$('#matricule').val(),
          place:$('#nplace').val(),
          climatisation:$('#climatisation').val(),
          musique:$('#musique').val(),
          autorisation:$('#autorisation').val()},function(data){
    
    $('#feed_enr').html(data);
   });
});

$('.inventory').click(function(e){
    
e.preventDefault();
});

$('.supvoit').click(function(){
    a=this.id;
   t=$('#'+a).data('idvoiture');
 $('#feed1').fadeIn();
  $('#feed2').fadeIn();
  $('#feed2').html('<div id="message_trajet" title="Informations du Trajet"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>Voulez vous vraiment supprimer la voiture ? <a href="?sup='+t+'">oui</a> ou <a href="/espace">non</a>.</p></div>');
  $( "#message_trajet" ).dialog();
 
});

});