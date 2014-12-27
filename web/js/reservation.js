$(document).ready(function(){ 
// déclaration des variables 
  var map;
  var panel;
  var autocomplete;
  var direction;
  var result;
  ///formulaire

  var r;

  var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
  };
  var champs_adresse = {
        street_number: '',
        route: '',
        locality: '',
        administrative_area_level_1: '',
        country: '',
        postal_code: ''
  };
  var champs_trajet = {
        distance : '',
        duree    : ''
  }
// déclaration des fonctions
  var cacherIdentite;
  var cacherReserv;
  var cacherTrajet;
  var sendToServer;
  var afficherReservation;

  var checkAddress;
  var calculate;
  var geolocate;
  var initialize;
  var checkFieldset;

  
// ------------------ DÉFINITION DES FONCTIONS --------------------

// [ DEBUT cacherIdentite() ]

/* 
 Vérifie si le fieldset des informations d'identité est conformément rempli,
 le cache si oui et affiche le fieldset des infos de réservation. 
*/
cacherIdentite =  function () {
      if ( $('#civilite').val()    ==''  
          || $('#nom').val()       =='' 
          || $('#email').val()     =='' 
          || $('#codepostal').val()=='' 
          || $('#ville').val()     ==''  
          || $('#pays').val()      =='' 
          || $('#adresse').val()   ==''  
          || $('#tmobile').val()   =='') {
          $('#result').html("<div class='avertissement'>Veuillez remplir les champs obligatoires</div>").show().delay(2000).hide('slow');     
      }
      else {
            $("#info_identite").hide();            
            return true;          
      }
      return false;
}
// FIN cacherIdentite()--------------------------------------f
  

 // DEBUT cacherReserv() ------------------------------------d  
   /**
     Vérifie si le fieldset des informations de réservation est conformément rempli,
     le cache si oui et affiche le fieldset des infos du trajet. 
   */
cacherReserv = function() { //alert(5);
      if ( $('#service').val()      ==''  
           || $('#nbpersonne').val()=='' ) {
          $('#result').html("<div class='avertissement'>Veuillez remplir les champs obligatoires</div>").show().delay(2000).hide('slow');
      }
      else {
          $("#info_reserv").hide();
          return true;
      }
      return false;
}
// FIN cacherReserv() -------------------------------------------------f


cacherTrajet = function() { //alert(5);
      if ( $('#aller_retour').val() ==''  
           || $('#depart').val()    =='' 
           || $('#arrivee').val()    ==''
           || $('#date_trajet').val()==''  ) {
          $('#result').html("<div class='avertissement'>Veuillez remplir les champs obligatoires</div>").show().delay(2000).hide('slow');
          //$('#result').show("slow");
      }
      else {
          $("#info_trajet").hide();
          return true;
      }
      return false;
}   

// DEBUT sendToServer() -----------------------------------------------d   
/*
  Vérifie la non vacuité des champs obligatoires puis envoie les données au serveur
*/
sendToServer = function() { // console.log('ok1');
      // vérifier la vacuité des données obligatoires     
      if ( $('#civilite').val()     =='' 
           || $('#service').val()   =='' 
           || $('#nom').val()       =='' 
           || $('#email').val()     =='' 
           || $('#nbpersonne').val()==''  
           || $('#adresse').val()   ==''  
           || $('#tmobile').val()   ==''  ) {
           $('#result').html("<div class='avertissement'>Veuillez remplir les champs obligatoires</div>");
      }
      else {
          // afficher le loader à l'attente de réponse du serveur ...
          $('#result').html("<img src='web/images/load.gif'>");
          // récupérer les données du formulaire
           var infos = {
                        type_cli: $('#type_cli').val(),
                        civilite: $('#civilite').val(),
                        nom:      $('#nom').val(),
                        prenom:   $('#prenom').val(),
                        agee  :   $("#agee").val(),  
                        email:    $('#email').val(),
                        nbpersonne: $('#nbpersonne').val(),
                        chvdo     : $('#chvdo').val(),
                        societe:    $('#societe').val(),
                        codepostal:  champs_adresse['postal_code'],
                        ville:       champs_adresse['locality'],
                        pays:        champs_adresse['country'],
                        adresse:  $('#adresse').val(),
                        tmobile:  $('#tmobile').val(),
                        tfixe:    $('#tfixe').val(),
                        faxe:     $('#faxe').val(),
                        email:    $('#email').val(),
                        service:  $('#service').val(),
                        commentaire:  $('#commentaire').val(),
                        aller_retour: $('#aller_retour').val(),
                        depart:       $('#depart').val(),
                        arrivee:      $('#arrivee').val(),
                        date_trajet:  $('#date_trajet').val(),
                        distance:     champs_trajet['distance'],
                        duree:        champs_trajet['duree'],
                        passwd:       $('#passwd').val(),
                        passwd_conf : $('#passwd_conf').val(),
                        to          : $('#to').val()
          };
          
          // Poster les données de réservation et afficher le message de retour du serveur dans le div #result
          $.post('/reservation', infos, function(data){ $('#result').html(data).show();});

          sessionStorage.setItem("nom",infos.nom);
          sessionStorage.setItem("prenom", infos.prenom);
          sessionStorage.setItem("email", infos.email);
          sessionStorage.setItem("nbpersonne", infos.nbpersonne);
          sessionStorage.setItem("societe", infos.societe);
          sessionStorage.setItem("adresse", infos.adresse);
          sessionStorage.setItem("tmobile", infos.tmobile);
          sessionStorage.setItem("codepostal", infos.codepostal);
          sessionStorage.setItem("service", infos.service);
          sessionStorage.setItem("date_trajet", infos.date_trajet);
          sessionStorage.setItem("depart", infos.depart);
          sessionStorage.setItem("arrivee", infos.arrivee);
          sessionStorage.setItem("aller_retour", infos.aller_retour);
          sessionStorage.setItem("distance", infos.distance);
          sessionStorage.setItem("duree", infos.duree); 
          sessionStorage.setItem("agee",infos.agee);  
          sessionStorage.setItem("chvdo",infos.chvdo); 
      }
      //return infos;
}
//FIN sendToServer() -------------------------------- f

// 
autocomplete = function(id_input){
    autocomplete = new google.maps.places.Autocomplete(
        /** @type {HTMLInputElement} */(document.getElementById(id_input)), { types: ['geocode'] });
    return autocomplete;
  }

// Fonction de géolocalisation de l'utilisateur pour faciliter l'autocomplétion
geolocate = function(id_input){
    var autocomp = autocomplete(id_input); 
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var geolocation = new google.maps.LatLng( position.coords.latitude, position.coords.longitude);
        autocomp.setBounds(new google.maps.LatLngBounds(geolocation, geolocation));
      });
    }
};

// paramètre le map, le charge, configure l'autocomplétion des adresses et gère la complétude de l'adresse
initialize = function(){
    var latLng = new google.maps.LatLng(48.853352, 2.515397); // Correspond au coordonnées de Neuilly-Plaisance
    var mapOptions = {
      zoom      : 14, // Zoom par défaut
      center    : latLng, // Coordonnées de départ de la carte de type latLng 
      mapTypeId : google.maps.MapTypeId.ROADMAP, // Type de carte, différentes valeurs possible HYBRID, ROADMAP, SATELLITE, TERRAIN
      //maxZoom   : 20
    };
    
    map   = new google.maps.Map(document.getElementById('google-map-trajet'), mapOptions);
    //panel = document.getElementById('panel');
   
    // autocomplétion du départ
    geolocate('depart');

    // autocomplétion de l'arrivée
    var autocomp = new google.maps.places.Autocomplete(
        /** @type {HTMLInputElement} */(document.getElementById('arrivee')), { types: ['geocode'] });
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var geolocation = new google.maps.LatLng( position.coords.latitude, position.coords.longitude);
        autocomp.setBounds(new google.maps.LatLngBounds(geolocation, geolocation));
      });
    }
    
    // autocomplétion de l'addresse
    auto = new google.maps.places.Autocomplete(
        /** @type {HTMLInputElement} */(document.getElementById('adresse')), { types: ['geocode'] });
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var geolocation = new google.maps.LatLng( position.coords.latitude, position.coords.longitude);
        auto.setBounds(new google.maps.LatLngBounds(geolocation, geolocation));
      });
    }

    // Gestionnaire de l'addresse
    google.maps.event.addListener(auto, 'place_changed', function() { 
        if (!checkAddress()){
            $("#message_adresse").dialog({
                                modal: true,
                                buttons: {
                                  Ok: function() {
                                    $( this ).dialog( "close" );                                    
                                    $('body').animate({scrollTop:0},500);
                                  }
                                }
                              });  
            $('#info_identite').show(); 
            $('#info_reserv').hide(); 
            $('#result').html("<div class='avertissement'>Veuillez préciser davantage votre adresse. Elle semble  incomplête</div>");
            $('#result').show(); 
        }         //alert('okk'); */
    });

    // Encapsulation du rendu dans un objet DirectionsRenderer
    direction = new google.maps.DirectionsRenderer({
      map   : map     // affichage de l'itinéraire sur le map
      //panel : panel // affichage des instructions de l'itinéraire
    });

};
// [FIN initialize()] -----

// [DEBUT checkAddress()]
checkAddress = function() {

    var place = auto.getPlace();

    for (var component in componentForm) {
      document.getElementById(component).value = '';
      document.getElementById(component).disabled = false;
    }

    for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0];
      if (componentForm[addressType]) {
        //var val = place.address_components[i][componentForm[addressType]];
        champs_adresse[addressType] = place.address_components[i][componentForm[addressType]]
      }
      else {
        //$('#result').html("<div class='avertissement'>L'adresse n'est pas complête</div>");
        //$('#result').show();
      }
    }
    var is_correct = champs_adresse["administrative_area_level_1"] != "" && champs_adresse["IDF"] != "" && champs_adresse["country"] != ""&& champs_adresse["locality"] != ""&& champs_adresse["postal_code"]!="" && champs_adresse["route"]!=""&& champs_adresse["street_number"]!="";
    return is_correct; 
    console.log(champs_adresse);
  }
  // [FIN checkAddress]

/*
*/
calculate = function(){
      origin      = $('#depart').val(); // Le point départ
      destination = $('#arrivee').val(); // Le point d'arrivé
     
      if(origin && destination){
          // préparation de la requête
          var request = {
              origin      : origin,
              destination : destination,
              travelMode  : google.maps.DirectionsTravelMode.DRIVING // Mode de conduite
          }
          // Service de calcul d'itinéraire
          var directionsService = new google.maps.DirectionsService();
          // Envoie de la requête pour calculer le parcours 
          directionsService.route(request, function(response, status){ 
              if(status == google.maps.DirectionsStatus.OK){
                  direction.setDirections(response); // Trace l'itinéraire sur la carte et les différentes étapes du parcours
                  
                  //result = response;
                  //panel.innerHTML = "Distance (km) : "+response.routes[0].legs[0].distance.text + " Durée du trajet : "+response.routes[0].legs[0].duration.text;
                  champs_trajet['distance'] = response.routes[0].legs[0].distance.text;
                  champs_trajet['duree'] = response.routes[0].legs[0].duration.text;
                  //console.log(direction);
                  //console.log() // les valeurs => .value (en m et timestamp)
              }
          });
          //alert (origin+"   "+destination);
      }
};
// FIN initialize()

afficherReservation = function(){
  
    $('dd').eq(0).html( sessionStorage.getItem("nom"));
    $('dd').eq(1).html( sessionStorage.getItem("adresse"));
    $('dd').eq(2).html( sessionStorage.getItem("tmobile"));
    $('dd').eq(3).html( sessionStorage.getItem("email"));
    var service = "";
    switch(sessionStorage.getItem("service")){
      case "1": service = "Navette aéroprtuaire"; break;
      case "2": service = "Night Club"; break;
      case "3": service = "Excursion"; break;
      case "4": service = "Night Club"; break;
      case "5": service = "Totale disposition"; break;
      case "6": service = "Disposition partielle"; break;
      case "7": service = "Affaire"; break;
      default : service = "Autre"; break;
    }
    $('dd').eq(5).html( service );
    $('dd').eq(6).html( sessionStorage.getItem("nbpersonne"));
    $('dd').eq(7).html( sessionStorage.getItem("chvdo")=="0" ? "NON": "OUI");
    $('dd').eq(9).html( sessionStorage.getItem("date_trajet"));
    $('dd').eq(10).html( sessionStorage.getItem("depart"));
    $('dd').eq(11).html( sessionStorage.getItem("arrivee"));
    $('dd').eq(12).html( sessionStorage.getItem("aller_retour")=="0" ? "NON": "OUI" );
    $('dd').eq(13).html( sessionStorage.getItem("distance"));
    $('dd').eq(14).html( sessionStorage.getItem("duree"));
    $('dd').eq(15).html( sessionStorage.getItem('prix') + "€");
}
//afficherReservation();
//------------- FIN DÉFINITION DES FONCTIONS -----------------------



// DEBUT EFFECTIF : Mise en oeuvre des fonctionnalités 

   initialize();
   $("#etapes_reservation").css({
       "display" : "inline",
       backgroundColor : 'wight',
       //borderColor: "#C22A19",
       height : "27px",
       'margin-bottom' : '20px',
       'align-self' : 'center'
   });
   $("#etapes_reservation li").css({
       "display"      : "inline",
       paddingRight   : '30px', // marge intérieure de 30px
       paddingLeft    : '30px',
       backgroundColor : '#CAD6E8',
       color : '#807B77',
       'margin-left'  : '5px', // marge extérieure de 10px
       'margin-right' : '5px',
       'align-self' : 'center',
       //'cursor' : 'progress'
   });

   li0 = $('#etapes_reservation li').eq(0);     
   li0.css({
     backgroundColor : '#c7081b',
     color : 'black',
     'font-weight': 'bold'
   });
   // ...

   // Cacher les fieldsets des infos de réservation, du trajet et le bouton de validation
   $("#info_reserv").hide();
   $("#info_trajet").hide();   
   $("#validation").hide();
   $("#bt_reserv").hide();
   $('#precedent4').hide()
   $('#remail').hide()
   // Cacher les messages popup du trajet, d'identité, de la réservation et d'adresse incomplète
   $("#message_trajet").hide();
   $("#message_infos").hide();
   $("#message_reserv").hide();
   $("#message_adresse").hide();
   
// FIN EFFECTIF 




// Click pour passer aux infos de réservation
$("#suivant1").on("click", function(){
   if (cacherIdentite()){
      $('body').animate({scrollTop:0},500);      
      $("#info_reserv").show();
      
      li = $('#etapes_reservation li').eq(1);
      //$(li).not(this).removeClass( ' active ' );
      li.css({
        backgroundColor:'#c7081b',
        color : 'black',
        'font-weight': 'bold'
      });
       
       li0 = $('#etapes_reservation li').eq(0);     
       li0.css({
         backgroundColor : '#CAD6E8',
         color : '#807B77',
       }); 
     // console.log(li);    
   }
   // mettre en exergue réservation le titre Réservation et grésiller les autres titres
   //console.log("suivant1");
})
$("#precedent1").on("click", function(){
     
     console.log("precedent1");
})

// Click pour passer au trajet ...
$("#suivant2").on("click", function(){
   if (cacherReserv()){
          $('body').animate({scrollTop:330},500);
          $("#info_trajet").show();
   }
   li = $('#etapes_reservation li').eq(2);
   //$(li).not(this).removeClass( ' active ' );
   li.css({
    backgroundColor:'#c7081b',
    color : 'black',
    'font-weight': 'bold'
   });

   li1 = $('#etapes_reservation li').eq(1);     
   li1.css({
     backgroundColor : '#CAD6E8',
     color : '#807B77',
   });

   //console.log("suivant2");
})
// retour a identite
$("#precedent2").on("click", function(){
     $("#info_trajet").hide();
     $("#info_reserv").hide();
     $("#info_identite").show();
     //console.log("precedent2");
     li = $('#etapes_reservation li').eq(0);
       //$(li).not(this).removeClass( ' active ' );
       li.css({
        backgroundColor:'#c7081b',
        color : 'black',
        'font-weight': 'bold'
       });

       li1 = $('#etapes_reservation li').eq(1);     
       li1.css({
         backgroundColor : '#CAD6E8',
         color : '#807B77',
       });
})

// Click pour passer à la dernière étape, celle de la validation
$("#suivant3").on("click", function(){
   if (cacherTrajet()){
       calculate();
       $("#bt_reserv").show();
       $('body').animate({scrollTop:330},500);
       li = $('#etapes_reservation li').eq(3);
       //$(li).not(this).removeClass( ' active ' );
       li.css({
        backgroundColor:'#c7081b',
        color : 'black',
        'font-weight': 'bold'
       });

       li2 = $('#etapes_reservation li').eq(2);     
       li2.css({
         backgroundColor : '#CAD6E8',
         color : '#807B77',
       });
       //$("#precedent4").show();
       $.ajax({
              type: "GET",
              url: "/checkmail",
              data : "mail="+$('#email').val(),
              dataType: "text",
              success: function(data){
                      //alert(data);
                      if(data=="1"){
                        $("#validation").show();  
                        $('#al').html("<div class='avertissement'>Vous avez dèjà un compte VIPDROPOFF! Veuillez entrer votre mot de passe</div>");
                        $("#passwd_conf").hide();
                        //alert(data);
                        pass = "";
                        $("#passwd").on("blur", function(){
                           pass = $("#passwd").val(); //alert("h");
                           $.ajax({
                                  type: "POST",
                                  url: "/connexion",
                                  data : "mail="+$('#email').val()+"&loc=reserv"+"&password="+pass,
                                  dataType: "text",
                                  success: function(data){
                                          //alert(data);
                                          if(data=="1"){
                                            //$("#validation").show();  
                                            //$('#al').html("<div class='avertissement'>Vous avez dèjà un compte VIPDROPOFF! Veuillez entrer votre mot de passe</div>");
                                            $("#passwd_conf").val(pass);
                                            $("#passwd_conf").hide();
                                            $("#passwd").hide();
                                            //alert(data);
                                            
                                          }        
                                  },
                                  error: function(e){
                                      console.log(e.message);
                                  }
                              })
                        });  
                      }else{
                        $("#validation").show(); 
                      }        
              },
              error: function(e){
                  console.log(e.message);
              }
          })
        //.bind("control_passwd", passwordCorrect);
   }
  // console.log("suivant3");
})

// retour a reservation
$("#precedent3").on("click", function(){
     $("#info_trajet").hide();
     $("#bt_reserv").hide();
     $("#validation").hide();
     $("#precedent4").hide();
     $("#info_reserv").show();
     //console.log("precedent3");
     li = $('#etapes_reservation li').eq(1);
       //$(li).not(this).removeClass( ' active ' );
       li.css({
        backgroundColor:'#c7081b',
        color : 'black',
        'font-weight': 'bold'
       });

       li1 = $('#etapes_reservation li').eq(2);     
       li1.css({
         backgroundColor : '#CAD6E8',
         color : '#807B77',
       });
})

// retour a trajet
$("#precedent4").on("click", function(){
     $("#remail").hide();
     $("#bt_reserv").hide();
     $("#result").hide();
     $("#validation").hide();
     $("#info_trajet").show();
     $("#precedent4").hide();

     li = $('#etapes_reservation li').eq(2);
       //$(li).not(this).removeClass( ' active ' );
       li.css({
        backgroundColor:'#c7081b',
        color : 'black',
        'font-weight': 'bold'
       });

       li3 = $('#etapes_reservation li').eq(3);     
       li3.css({
         backgroundColor : '#CAD6E8',
         color : '#807B77',
       });
})

// Envoyer les données au serveur dès que l'on clicke sur le bouton de validation du formulaire de réservation
   $('#bt_reserv').bind('click', sendToServer );
   $('#fiche_reserv').bind('fiche_reserv', afficherReservation);
  

/**
 *                                                      GESTION DES DATES                                                      
 */

        var myControl =  {
        create: function(tp_inst, obj, unit, val, min, max, step){
            $('<input class="ui-timepicker-input" value="'+val+'" style="width:50%">')
                .appendTo(obj)
                .spinner({
                    min: min,
                    max: max,
                    step: step,
                    change: function(e,ui){ // key events
                            // don't call if api was used and not key press
                            if(e.originalEvent !== undefined)
                                tp_inst._onTimeChange();
                            tp_inst._onSelectHandler();
                        },
                    spin: function(e,ui){ // spin events
                            tp_inst.control.value(tp_inst, obj, unit, ui.value);
                            tp_inst._onTimeChange();
                            tp_inst._onSelectHandler();
                        }
                });
            return obj;
        },
        options: function(tp_inst, obj, unit, opts, val){
            if(typeof(opts) == 'string' && val !== undefined)
                return obj.find('.ui-timepicker-input').spinner(opts, val);
            return obj.find('.ui-timepicker-input').spinner(opts);
        },
        value: function(tp_inst, obj, unit, val){
            if(val !== undefined)
                return obj.find('.ui-timepicker-input').spinner('value', val);
            return obj.find('.ui-timepicker-input').spinner('value');
        }
    };

     // fonction d'affichage du calendrier datetimepicker
     function afficherCalendrierComplet(id){
         $(id).datetimepicker({controlType: myControl}); 
         $(id).timepicker($.timepicker.regional['fr'])
     } 

     // DateTimePicker
    afficherCalendrierComplet('#date_trajet');
   
});
// FIN --F
