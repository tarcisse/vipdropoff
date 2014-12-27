<?php
include('fonction.php');
include('sendmail.php');


//$env_mail->envoyerp('tarcisseeddy@gmail.com','va te faire voir','mo cul','reservations/pdf/14.pdf');
// On charge le framework Silex
require_once 'vendor/autoload.php';

// On définit des noms utiles
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

// On crée l'application et on la configure en mode debug
$app = new Application();
$app['debug'] = true;
$app->register(new Silex\Provider\SessionServiceProvider());
if(isset($_GET['lang']))
{
    $app['session']->set('lang',$_GET['lang']);
    
}

if(! $app['session']->get('lang'))
{
   $app['session']->set('lang','fr');
}


/*
 *inclusion de module
 */

 

////----------------twig-----------------------------------------------------
$app->register(new Silex\Provider\TwigServiceProvider(), 
	       array('twig.path' => '.',));
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array($app['session']->get('lang')),
));
////----------------translation-----------------------------------------------------
use Symfony\Component\Translation\Loader\YamlFileLoader;

$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', __DIR__.'/locales/en.yml', 'en');
    $translator->addResource('yaml', __DIR__.'/locales/do.yml', 'do');
    $translator->addResource('yaml', __DIR__.'/locales/fr.yml', 'fr');

    return $translator;
}));
////----------------------------bdd------------------
 try {
$dns = 'mysql:host=127.0.0.1;dbname=vipdropoff';
$utilisateur = 'root';
$motDePasse = '';// tu remplace juste le mot de passe par vide
$db = new PDO( $dns, $utilisateur, $motDePasse );

}
catch ( Exception $e ) {
  die ($e->getMessage());
  
}
///*---------------------------VARIABLE GLOBAL--------
   $photos_=array("JANVIER",
		  "FEVRIER",
		  "MARS",
		  "AVRIL",
		  "MAI",
		  "JUIN",
		  "JUILLET",
		  "AOUT",
		  "SEPTEMBRE",
		  "OCTOBRE",
		  "NOVEMBRE",
		  "DECEMBRE");
   $site="128.79.202.88";
   $livraison='tarcisseeddy@gmail.com';//'contact@vipdropoff.com';
///----------------------------------mail

/*
 *fin inclusion module
 */


 
 /*--------------------------------------------------------------------------------------------------------------------------------------*/
 //                                                                                                                                      //
 //                                               DEBUT DES ROUTES                                                                       //
 //                                                                                                                                      //
 /*--------------------------------------------------------------------------------------------------------------------------------------*/
// On définit une route pour l'url /


$app->get('/', function(Application $app, Request $req) {
    
   include ('session.php');
 //$app['session']->clear();
    return $app['twig']->render('web/index.html', array('title' => 'Accueil',
							'description'=>'desc',
							'key' => 'chauffeur privé',
							'session' => $session,
							));
  });
// On définit une route pour reservation
$app->get('/reservation', function(Application $app, Request $req) {
    
   include ('session.php');
   $to   = $req->query->get("to");
    return $app['twig']->render('web/reservation.html', array('title' => 'reservation',
							'description'=>'desc',
							'key' => 'chauffeur privé',
							'session' => $session,
							'to'=>$to
							));
  });

// On définit une route pour avantages
$app->get('/avantages', function(Application $app, Request $req) {
     include ('session.php');
  
    return $app['twig']->render('web/avantages.html', array('title' => 'Avantages',
							'description'=>'desc',
							'key' => 'chauffeur privée',
							'session' => $session,
							));
  });

// On définit une route pour avantages
$app->get('/devis', function(Application $app, Request $req) {
     include ('session.php');
  
    return $app['twig']->render('web/devis.html', array('title' => 'devis',
							'description'=>'desc',
							'key' => 'chauffeur privée',
							'session' => $session,
							));
  });
// On définit une route pour partenariat
$app->get('/partenariat', function(Application $app, Request $req) {
     include ('session.php');
      $page='web/mettre.html';
      $titre='mettre sa voiture a dispostion';
      $text=' Vous &ecirc;tes un taxi ou un chauffeur ind&eacute;pendant (VTC) et souhaitez accro&icirc;tre votre activit&eacute; ?';
  if($req->query->get('type') && $req->query->get('type')=='devenir')
  {
    $page='web/devenir.html';
    $titre='devenir partenaire';
    $text='';
  }
    return $app['twig']->render('web/partenariat.html', array('title' => $titre,
							'description'=>'desc',
							'key' => 'chauffeur privée',
							'session' => $session,
							'page'=>$page,
							'text'=>$text
							));
  });
// On définit une route pour contact
$app->get('/contact', function(Application $app, Request $req) {
     include ('session.php');
  
    return $app['twig']->render('web/pcontact.html', array('title' => 'contact',
							'description'=>'desc',
							'key' => 'chauffeur privée'
							));
  });

  // On définit une route pour contact
$app->get('/connexion', function(Application $app, Request $req) {
    
    if($app['session']->get('connexion') && $app['session']->get('connexion')=='true' )
    {
	return $app->redirect('/');
    }
  
    return $app['twig']->render('web/pconection.html', array('title' => 'connexion',
							'description'=>'desc',
							'key' => 'chauffeur privée'
							));
  });
 // On définit une route pour contact
$app->get('/inscription', function(Application $app, Request $req) {
    
    include ('session.php');
    return $app['twig']->render('web/pinscription.html', array('title' => 'inscription',
							'description'=>'desc',
							'key' => 'chauffeur privée',
							'session'=>$session
							));
  });
$app->post('/inscription', function(Application $app, Request $req) {
   global $db;
   global $site;
   if(trim($req->request->get('mail'))=='' ||
      trim($req->request->get('password'))=='')
{
    return"<div class='erreur'>Veuillez svp remplir les champs vides</div>";
}
if(! filter_var($req->request->get('mail'), FILTER_VALIDATE_EMAIL)){
    return"<div class='avertissement'>Le format de l' adresse mail est incorrect , veuillez verifier l' ecriture de celle ci</div>";
}

                                
				 $civilite=$db->quote($req->request->get('civilite'));
				 $nom=$db->quote($req->request->get('nom'));
				    $prenom=$db->quote($req->request->get('prenom'));
                                    $mail=$db->quote($req->request->get('mail'));
                                    $password=$db->quote(sha1($req->request->get('password')));
                                    $societe=$db->quote($req->request->get('societe'));
                                   $codepostal=$db->quote($req->request->get('codepostal'));
                                   $ville=$db->quote($req->request->get('ville'));
                                    $pays=$db->quote($req->request->get('pays'));
                                   $adresse=$db->quote($req->request->get('adresse'));
                                    $mobile=$db->quote($req->request->get('mobile'));
                                    $fixe=$db->quote($req->request->get('fixe'));
                                    $faxe=$db->quote($req->request->get('faxe'));
				    $type=$db->quote($req->request->get('type'));
				    $date=$db->quote(time());
				    $token=sha1(time()+41);
    $r2=$db->query("SELECT * FROM `client` WHERE `MAIL`= $mail limit 1");
    $r2=$r2->fetch();
    if(!empty($r2))
    {
	return"<div class='erreur'>
	Il semblerait que cette adresse mail existe deja dans notre base des donn&eacute;es si vous pens&eacute; que ceci est une erreur veuillez nous contactez en cliquant <a href='/contact' target='blanck'>ici</a>
	</div>";
    }
    
    $db->query("INSERT INTO `client`(`ID_CLIENT`, `CIVILITE`, `NOM`, `PRENOM`, `SOCIETE`, `CODEPOSTAL`, `VILLE`, `PAYS`, `ADRESSE`, `TELEPHONE1`, `TELEPHONE2`, `TYPE_CLIENT`, `FAXE`, `MAIL`, `PASSWORD`, `DATE_INSCRIPTION`, `token_compte`, `typedecontrat`)
	       VALUES ('',$civilite,$nom,$prenom,$societe,$codepostal,$ville,$pays,$adresse,$mobile,$fixe,$type,$faxe,$mail,$password,$date,".$db->quote($token).",1)") or die($db->errorInfo()[2]);
     //$c=$req->request->get('mail');
    // $c2="send";//message de confirmation en xa d' envoi
     $url=$site."/active_vip?token=".$token;
     $message=$app['twig']->render('web/mail/activeco.html', array('nom'=>$req->request->get('nom')." ".$req->request->get('prenom'),
							    'object'=>'Activation compte',
							    'url'=>$url,
							    'site'=>$site));
   
   /*$sujet='Activation compte';
include('mail.php');*/
   global $env_mail;
   $env_mail->envoyer($req->request->get('mail'),'Activation du compte VIPDROPOFF',$message);
    return"<div class='succes'>&Agrave; lire attentivement: un mail vous a &eacute;t&eacute; envoyez sur votre adresse mail pour confirmer votre inscription et activez votre compte. si vous ne recevez pas de mail de confirmation au bout de 5 minutes, veuillez nous contactez sur ce lien pour une activation manuelle</div>";
   
  });




 $app->post('/inscription2', function(Application $app, Request $req) {
   global $db;
   global $site;
   if(trim($req->request->get('mail'))=='' ||
      trim($req->request->get('password'))=='')
{
    return"<div class='erreur'>Veuillez svp remplir les champs vides</div>";
}
if(! filter_var($req->request->get('mail'), FILTER_VALIDATE_EMAIL)){
    return"<div class='avertissement'>Le format de l' adresse mail est incorrect , veuillez verifier l' ecriture de celle ci</div>";
}

                                
				 $civilite=$db->quote($req->request->get('civilite'));
				 $nom=$db->quote($req->request->get('nom'));
				    $prenom=$db->quote($req->request->get('prenom'));
                                    $mail=$db->quote($req->request->get('mail'));
                                    $password=$db->quote(sha1($req->request->get('password')));
                                    $societe=$db->quote($req->request->get('societe'));
                                   $codepostal=$db->quote($req->request->get('codepostal'));
                                   $ville=$db->quote($req->request->get('ville'));
                                    $pays=$db->quote($req->request->get('pays'));
                                   $adresse=$db->quote($req->request->get('adresse'));
                                    $mobile=$db->quote($req->request->get('mobile'));
                                    $fixe=$db->quote($req->request->get('fixe'));
                                    $faxe=$db->quote($req->request->get('faxe'));
				    $type=$db->quote($req->request->get('type'));
				    $date=$db->quote(time());
				    $token=sha1(time()+41);
    $r2=$db->query("SELECT * FROM `client` WHERE `MAIL`= $mail limit 1");
    $r2=$r2->fetch();
    if(!empty($r2))
    {
	return"<div class='erreur'>
	Il semblerait que cette adresse mail existe deja dans notre base des donn&eacute;es si vous pens&eacute; que ceci est une erreur veuillez nous contactez en cliquant <a href='/contact' target='blanck'>ici</a>
	</div>";
    }
    if(!empty($r2))
    {
	return"<div class='erreur'>
	Il semblerait que cette adresse mail existe deja dans notre base des donn&eacute;es si vous pens&eacute; que ceci est une erreur veuillez nous contactez en cliquant <a href='/contact' target='blanck'>ici</a>
	</div>";
    }
    $db->query("INSERT INTO `client`(`ID_CLIENT`, `CIVILITE`, `NOM`, `PRENOM`, `SOCIETE`, `CODEPOSTAL`, `VILLE`, `PAYS`, `ADRESSE`, `TELEPHONE1`, `TELEPHONE2`, `TYPE_CLIENT`, `FAXE`, `MAIL`, `PASSWORD`, `DATE_INSCRIPTION`, `token_compte`, `typedecontrat`)
	       VALUES ('',$civilite,$nom,$prenom,$societe,$codepostal,$ville,$pays,$adresse,$mobile,$fixe,$type,$faxe,$mail,$password,$date,".$db->quote($token).",2)") or die($db->errorInfo()[2]);
     $c=$req->request->get('mail');
     $c2="send";//message de confirmation en xa d' envoi
     $url=$site."/active_vip?token=".$token;
     
     $message=$app['twig']->render('web/mail/partenariat1.html', array('nom'=>$req->request->get('nom')." ".$req->request->get('prenom'),
							    'object'=>'Partenariat pour une mise à disposition',
							    'url'=>$url,
							    'site'=>$site));
   /*$sujet='Activation compte';
include('mail.php');*/
  
   global $env_mail;
   $env_mail->envoyer($req->request->get('mail'),'Activation du compte VIPDROPOFF',$message);
   
    return"<div class='succes'>&Agrave; lire attentivement: un mail vous a &eacute;t&eacute; envoyez sur votre adresse mail pour confirmer votre inscription et activez votre compte. si vous ne recevez pas de mail de confirmation au bout de 5 minutes, veuillez nous contactez sur ce lien pour une activation manuelle</div>";
   
  });
 
 
 
  $app->get('/active_vip', function(Application $app, Request $req) {
global $db;
$token=$db->quote($req->query->get('token'));
$r=$db->query("SELECT * FROM `client` WHERE `token_compte`=$token ");
$r=$r->fetch();
if(empty($r))
   {
    return"<div class='erreur'>Il semblerait que ce lien &agrave; d&eacute;j&agrave; &eacute;t&eacute; utilis&eacute; ou est invalide. Si vous pensez que ceci est une
    erreur de notre par veuillez nous contactez en cliquant  <a href='/contact' target='blank'>ici</a></div>";
   }
$db->query("UPDATE `client` SET `token_compte`='active' where ID_CLIENT=".$r['ID_CLIENT'])or die("erreur");
$app['session']->set('connexion','true');
$app['session']->set('nom',$r['NOM']);
$app['session']->set('prenom',$r['PRENOM']);
$app['session']->set('id',$r['ID_CLIENT']);
$app['session']->set('mail',$r['MAIL']);
return $app->redirect('/connexion?compte=true');
  });
  
 //On définit une route pour la connexion d' un client==================================================================
$app->post('/connexion', function(Application $app, Request $req) {

global $db;

  $loc = null;
  if(!empty($req->request->get('loc')))
       $loc = $req->request->get('loc');

  $mail=$req->request->get('mail');
  $password=$req->request->get('password');
  if(empty($mail) || empty($password))
  {
      return"<div class='erreur'>Veuillez svp remplir les champs vides</div>";
  }
  if(! filter_var($mail, FILTER_VALIDATE_EMAIL)){
      return"<div class='avertissement'>Le format de l' adresse mail est incorrect , veuillez verifier l' ecriture de celle ci</div>";
  }
  $password=$db->quote(sha1($password));
  $mail=$db->quote($mail);
  $r=$db->query("SELECT * FROM `client` WHERE `PASSWORD`=$password  AND `MAIL`=$mail limit 1 ")or die('erreur1');
  $r=$r->fetch();
  if(empty($r))
  {
      return "<div class='erreur'>Aucune donn&#233; ne correspond &#224; vos iddentifiant, Mail ou Mot de passe incorrect</div>";
  }
  $app['session']->set('connexion','true');
  $app['session']->set('nom',$r['NOM']);
  $app['session']->set('prenom',$r['PRENOM']);
  $app['session']->set('id',$r['ID_CLIENT']);
  $app['session']->set('mail',$r['MAIL']);

  if ($loc)
      return "<script type='text/javascript'>$('#al').html('<div class='succes'>Connexion réussi! Validez maintenant votre réservation</div>');</script>";

  return "
  <div class='succes'>Connexion reussie</div>
  <script>
  window.location.reload();
  </script>
  ";
     
    });
  //On définit une route pour la connexion d' un client==================================================================
  $app->get('/deconnexion', function(Application $app, Request $req) {
 /* $id=$req->request->get('id');
  if($id==$app['session']->getId())  {*/
      $app['session']->clear();
      return $app->redirect('/');
 /* }  else  {
      return $app->redirect('/');
  }*/
   
  });
//On définit une route pour la connexion d' un client==================================================================
$app->get('/deconnexion', function(Application $app, Request $req) {
$id=$req->request->get('id');
/*if($id==$app['session']->getId())
{*/
    $app['session']->clear();
    return $app->redirect('/');
/*}
else
{
    return $app->redirect('/');
}*/
});



//On définit une route pour la connexion d' un client==================================================================
$app->post('/mdpo', function(Application $app, Request $req) {
    global $db;
    global $site;
    $mail=$db->quote($req->request->get('email'));
    $r=$db->query("SELECT * FROM `client` WHERE `MAIL`= $mail");
    $r=$r->fetch();
    if(empty($r))
    {
	return "<div class='avertissement'>Desol&#233; nous n' avons trouvez aucun compte correspondant &#224;  votre adresse.<br>
	veuillez verifier l' ecriture ou vous inscrire. si vous pensez que ceci est une erreur de notre part veuillez nous contactez<br>
	en cliquant  <a href='/contact' target='blanck'>ici</a></div>";
    }
    
   $token=sha1(time()+41);
   $r2=$db->query("UPDATE client set token_mpd=".$db->quote($token)." where mail=$mail");
   $url=$site."/mdp/active/".$token;
   $c=$req->request->get('email');
//return $r['NOM'];
   $c2="send";//message de confirmation en xa d' envoi
   $message=$app['twig']->render('web/mail/mdp.html', array('nom'=>$r['NOM']." ".$r['PRENOM'],
							    'object'=>'Reinisialisation mot de passe',
							    'url'=>$url,
							    'url2'=>'url2'));
   $sujet='Reinisialisation mot de passe';
include('mail.php');
return "<div class='succes'>Un mail de reinitialisation vous a  &#233;t&#233; envoyez sur votre compte ".$req->request->get('email')." cliquez sur nouveaupour specifier un nouveau mot de passe</div>";
});

//On définit une route pour la connexion d' un client==================================================================
$app->get('/mdp/active/{token}', function(Application $app, Request $req, $token) {

 return $app->redirect("/reinis?token=$token");
  
});
$app->get('/reinis', function(Application $app, Request $req) {
     include ('session.php');
  
    return $app['twig']->render('web/preinis.html', array('title' => 'Mot de passe perdu',
							'description'=>'desc',
							'key' => 'chauffeur privée',
							'token'=>$req->query->get('token'),
							'session'=>$session
							));
});
$app->post('/reinitialiser', function(Application $app, Request $req) {
     global $db;
   $mdp= $db->quote(sha1($req->request->get('mdp')));
   $token=$db->quote($req->request->get('token'));
   $r=$db->query("SELECT `ID_CLIENT` FROM `client` WHERE `token_mpd`=$token");
   $r=$r->fetch();
   $c=$req->request->get('token');
   
   if(empty($r) || empty($c))
   {
    return"<div class='erreur'>Il semblerait que ce lien &agrave; d&eacute;j&agrave; &eacute;t&eacute; utilis&eacute; ou est invalide. Si vous pensez que ceci est une
    erreur de notre par veuillez nous contactez en cliquant  <a href='/contact' target='blank'>ici</a></div>";
   }
   $db->query("UPDATE `client` SET `PASSWORD`=$mdp,`token_mpd`='' where ID_CLIENT=".$r['ID_CLIENT'])or die("erreur");
   return "<div class='succes'>Votre mot de passe &agrave; &eacute;t&eacute; correctement modifier</div>";
 
});
//=======================Espace de gestion======================================
$app->get('/espace', function(Application $app, Request $req) {
     global $db;
     
    
     
     
     if($app['session']->get('id'))
    {
	$id=$app['session']->get('id');
    }
    else
    {
	
	return $app->redirect('/');
    }
    
     
      if($req->query->get('sup')){
         $idv=$req->query->get("sup");
	$r=$db->query("SELECT `ID_CLIENT` FROM `voiture` WHERE `ID_VOITURE`=$idv");
	$r=$r->fetchAll();
	
	if(empty($r))
	{
	    return $app->redirect('/espace');
	}
	
	$db->query("DELETE FROM `voiture` WHERE `ID_VOITURE`=$idv");
     }
     
     
     
     
     $token=md5(rand(1,100)*rand(200,700)*date('y')*date('m')*date('H')*date('m')*date('s'));
  $app['session']->set('autorisation',"$token");
     include ('session.php');
     $action='listev';
     if($req->query->get('action'))
     {
	if($req->query->get('action')=='ajouter')
	{
	    $action='ajouterv';
	}
	
	if($req->query->get('action')=='course')
	{
	    $action='coursev';
	}
     }
     
     $r=$db->query("SELECT * FROM `voiture` WHERE ID_CLIENT=$id order by ID_VOITURE DESC");
     $r=$r->fetchAll();
     
    return $app['twig']->render('web/espace.html', array('title' => 'espace membre',
							'description'=>'desc',
							'key' => 'chauffeur privée',
							'session' => $session,
							'action'=>$action,
							'autorisation'=>$token,
							'voiture'=>$r
							));
});



//*/*--/upload  proprement dite fichier -------------------------------------------------------------------------------------//-/------/--/---/-/-/-/-/-/
$app->post('/uploader',function(Application $app , Request $req){
   
  $token=$app['session']->get('autorisation');
 $tof=$_FILES['tof']['name'];
    $tof_tmp=$_FILES['tof']['tmp_name'];
    $adr=7777;
    $id='';
  
 $nom='web/tmp/'.$req->query->get('nm');//$_GET['nm'];
 
    
  
        
    
    
    if(!empty($tof_tmp))
    {
        $image=explode('.',$tof);
        $image_ext=end($image);
        
        if(in_array(strtolower($image_ext),array('png','gif','jpeg','jpg'))==false)
        {
             $code=$_GET['code'];
            return"
             <script src='../../script/jquery.js'></script>
           <script>
           $('document').ready(function(){
            $('#frame-feed$code', window.parent.document).html('le fichier charg&#233 ne pas une image');
           });
           </script>
           ";
        }
        else
        {
            
            //$image_src=imagecreatefromjpeg($tof_tmp);
           // imagejpeg($image_src,"rep/$adr/2013-07-28-190713.jpg");
            
            
            move_uploaded_file($tof_tmp,"$nom.jpg");
            $code=$_GET['code'];
	 return"<div style='width:100px; height:100px;overflow: hidden;'><img src='$nom.jpg' width=100px style='float:left'></div>
           <a href='/supfichier/$token/$code'>supprim&#233</a>
          <script src='//code.jquery.com/jquery-1.10.2.js'></script>
           <script>
           $('document').ready(function(){
            $('#frame-feed$code', window.parent.document).html('');
           });
           </script>
           ";
           
        }
    }
});

/**//*////*-*-*-**-*-*-*-*-*--*****-*-**-**-**-*supprimer fichier ///***-*-*--*--*********************************/
$app->get('/supfichier/{fichier}/{code}',function(Application $app, Request $req, $fichier, $code){
  
  

 
 $nom="web/tmp/$fichier.$code.jpg";

  
supprimer("$nom");
    return'';
});

$app->post('/enrvoiture',function(Application $app, Request $req){
global $db;
  if( $req->request->get('marque')=='' ||
     $req->request->get('matricule')==''  ||
     is_numeric($req->request->get('place'))==false ||
     is_numeric($req->request->get('climatisation'))==false ||
     is_numeric($req->request->get('musique'))==false )
  {
     return"<div class='erreur'>Verifier que les differents champs sont correctement remplis</div>";
  }
        
          $marque=$db->quote($req->request->get('marque'));
          $matricule=$db->quote($req->request->get('matricule'));
          $place=$db->quote($req->request->get('place'));
          $climatisation=$db->quote($req->request->get('climatisation'));
          $musique=$db->quote($req->request->get('musique'));
	  $autorisation=$req->request->get('autorisation');
	  
	   $r2=$db->query("SELECT * FROM `voiture` WHERE matricule= $matricule limit 1");
    $r2=$r2->fetch();
    if(!empty($r2))
    {
	return"<div class='erreur'>
	Il semblerait que ce num&eacute;ros matricule existe deja dans notre base des donn&eacute;es si vous pens&eacute; que ceci est une erreur veuillez nous contactez en cliquant <a href='/contact' target='blanck'>ici</a>
	</div>";
    }
    
    if($app['session']->get('id'))
    {
	$id=$app['session']->get('id');
    }
    else
    {
	return "<div class='erreur'>operation impossible, car aucune session n' est en cour</div>";
    }
    
    echo "web/tmp/$autorisation.5jpg";
    $db->query("INSERT INTO `voiture`(`ID_VOITURE`, `ID_CLIENT`, `MARQUE`, `NBPLACE`, `CLIMATISATION`, `SURPLUS`, `MUSIC`, `MATRICULE`) VALUES
	       ('',$id,$marque,$place,$climatisation,'',$musique,$matricule)") or die("<div class='erreur'>erreur</div>");
   $idd=$db->lastInsertId();
   
     deplacer("web/tmp/{$autorisation}5.jpg","web/images/voiture/$idd.jpg");
       
    return "<div class='succes'>votre voiture &agrave; &eacute;t&eacute; correctement enregistr&eacute;e</div>";
    
	
});


// On lance l'application
$app->get('/tarcisse',function(Application $app, Request $req){
    
    $t=array();
    
    
    if($flux = simplexml_load_file('http://www.visitparisregion.com/evenements-paris/rss-evenements-41069.html&idPage=20003'))
{
   $donnee = $flux->channel;

   //Lecture des données
$i=0;
   foreach($donnee->item as $valeur)
   {
      //Affichages des données

     $t[$i]['date']=date("d/m/Y",strtotime($valeur->pubDate));
     $t[$i]['titre']=utf8_decode($valeur->title);
     $t[$i]['description']=utf8_decode($valeur->description);
   }
  
   return $app['twig']->render('web/box3-2.html', array('evenement'=>$t
							));
}else echo 'Erreur de lecture du flux RSS';
    
    
    
});









//=======================================================
$app->post('contact',function(Application $app, Request $req){
    global $env_mail;
   $nom=$req->request->get('nom');
   $mail=$req->request->get('mail');
   $telephone=$req->request->get('telephone');
   $mes=$req->request->get('message');
   
   if(trim($nom)=='' || trim($mes)=='')
   {
    
   return"<div class='erreur'>Veuillez svp remplir les champs vides</div>";
}
if(! filter_var($req->request->get('mail'), FILTER_VALIDATE_EMAIL)){
    return"<div class='avertissement'>Le format de l' adresse mail est incorrect , veuillez verifier l' ecriture de celle ci</div>";
}
   
   $message="Nom:$nom<br>
   Mail :$mail<br>
   Telephone :$telephone<br>
   <p>
   message
   <p>";
   
  global $livraison; 
$env_mail->envoyer($livraison,'contact vipdropoff',$message);


return "<div class='succes'>
Votre message &agrave; &eacute;t&eacute; correctement transm&icirc;t, nous vous r&eacute;pondrons dans le plus bref d&eacute;lai. merci pour la confiance que vous accordez &agrave; vip dropoff
</div>";
   
});









$app->get('/pdf',function(Application $app, Request $req){
    
   $contentnue=$app['twig']->render('reservations/pdf.html',array('nom'=>$nom." ".$prenom,
							       'adresse'=>$adresse,
							       'telephone'=>$tmobile,
							       'email'=>$email,
							       'service'=>$service,
							       'nbpersonne'=>$nbpersonne,
							       'chvdo'=>$chvdo,
							       'date'=>$date_reserv,
							       'depart'=>$depart,
							       'arrivee'=>$arrivee,
							       'ar'=>$aller_retour,
							       'distance'=>$distance,
							       'duree'=>$duree,
							       'prix'=>$prix,
							       'id_reservation'=>$id_reservation)); 
return gpdf($contentnue,$id_reservation,'reservations/pdf');
//''  
return gpdf($contentnue,'14','reservations/pdf');

 

});










//-------------------------------------- TRAITEMENT DES DONNÉES D'UNE RÉSERVATION ----------------------------------------------------------------------------------
$app->post('/reservation', function(Application $app, Request $req) {
    global $db;
    $p = "";
    
         
     // si non connecté 
     if(!($app['session']->get('connexion') && $app['session']->get('connexion')=='true')){
      // vérifier la non-vacuité des infos obligatoires
      $mes = "";
      $p  = trim($req->request->get('passwd'));
      $pc = trim($req->request->get('passwd_conf'));
       
      if( $p =='' || $pc ==''  ){
          $mes = "Veuillez remplir les champs vides";
      } 
      else if (strcmp($p, $pc) != 0)
               $mes = "Les mots de passe ne sont pas identiques !";
      if (!empty($mes)) return "<div class='erreur'>".$mes."</div><script type='text/javascript'>$('#precedent4').show();</script>";
     }

     // vérifier la non-vacuité des infos obligatoires
     if( trim($req->request->get('nom'))=='' || trim($req->request->get('tmobile'))=='' || trim($req->request->get('email'))=='' || trim($req->request->get('adresse'))=='' 
        || trim($req->request->get('service'))=='' || trim($req->request->get('nbpersonne'))=='' ){
        return"<div class='erreur'>Veuillez remplir les champs vides !</div> <script type='text/javascript'>$('#precedent4').show();</script>";
     } 
     // Formatage des infos
     if(! filter_var($req->request->get('email'), FILTER_VALIDATE_EMAIL)){
           return"<div class='avertissement'>Le format de l' adresse mail est incorrect ! Veuillez le corriger.</div><script type='text/javascript'>$('#precedent4').show();</script>";
     }
      
     $type_cli  = $db->quote($req->request->get('type_cli'));   
     $civilite  = $db->quote($req->request->get('civilite'));
     $nom       = $db->quote($req->request->get('nom'));
     $prenom    = $db->quote($req->request->get('prenom'));
     $email     = $db->quote($req->request->get('email'));
     $service   = $db->quote($req->request->get('service'));
     $societe   = $db->quote($req->request->get('societe'));
     $codepostal= $db->quote($req->request->get('codepostal'));
     $ville     = $db->quote($req->request->get('ville'));
     $pays      = $db->quote($req->request->get('pays'));
     $adresse   = $db->quote($req->request->get('adresse'));
     $tmobile   = $db->quote($req->request->get('tmobile'));
     $tfixe     = $db->quote($req->request->get('tfixe'));
     $faxe      = $db->quote($req->request->get('faxe'));
     $nbpersonne= $db->quote($req->request->get('nbpersonne'));
     $date_retour = "25/09/2014";
     $commentaire = $db->quote($req->request->get('commentaire'));
     $etat = "0";
     $prix = "0";
     $date_reserv = $db->quote(date("d-m-Y"));
     $agee  = $db->quote($req->request->get('agee'));   
     $chvdo  = $db->quote($req->request->get('chvdo'));

      // Si le mot de passe n'est pas vide, l'internaute est un nouveau client : on l'inscrit automatiquement
     if(strcmp($p, "") != 0){
         $date_ins = $db->quote(date("d-m-Y"));
         $p = $db->quote($p);
         $db->query("INSERT INTO `client`(`ID_CLIENT`, `CIVILITE`, `NOM`, `PRENOM`, `SOCIETE`, `CODEPOSTAL`, `VILLE`, `PAYS`, `ADRESSE`, `TELEPHONE1`, `TELEPHONE2`, `TYPE_CLIENT`, `FAXE`, `MAIL`, `PASSWORD`, `DATE_INSCRIPTION`, `AGEE`)
                                        VALUES ('',$civilite,$nom,$prenom,$societe,$codepostal,$ville,$pays,$adresse,$tmobile,$tfixe,$type_cli,$faxe,$email,$p,$date_ins, $agee) ") or die($db->errorInfo()[2]);
         $id_cli = $db->lastInsertId();
     } else{
         $id_cli = $app['session']->get('id');
     }
   
     $db->query("INSERT INTO `reservation`(`ID_CLIENT`, `CIVILITE`, `NOM`, `PRENOM`, `SOCIETE`, `CODEPOSTAL`, `VILLE`, `PAYS`, `ADRESSE`, `TELEPHONE1`, `TELEPHONE2`, `TYPE_CLIENT`, `FAXE`, `MAIL`, `DATE_RESERVATION`, `SERVICE`, `NBPERSONNE`, `DATE_RETOUR`, `COMMENTAIRE`, `ETAT`, `PRIX`, `AGEE`,`CHVDO`)
          VALUES ($id_cli, $civilite, $nom, $prenom, $societe, $codepostal, $ville, $pays, $adresse, $tmobile, $tfixe, $type_cli, $faxe, $email, $date_reserv, $service, $nbpersonne, $date_retour, $commentaire, $etat, $prix, $agee, $chvdo)") or die($db->errorInfo()[2]);
     
     $id_reservation = $db->lastInsertId();


    // vérifier la non-vacuité des infos obligatoires du trajet
     if( trim($req->request->get('aller_retour'))=='' || trim($req->request->get('depart'))=='' || trim($req->request->get('depart'))==''|| trim($req->request->get('date_trajet'))=='' ){
        return "<div class='erreur'>Veuillez remplir les champs vides !</div><script type='text/javascript'>$('#precedent4').show();</script>";
     } 
     
      
     $aller_retour  = $db->quote($req->request->get('aller_retour'));
     $depart        = $db->quote($req->request->get('depart'));
     $arrivee       = $db->quote($req->request->get('arrivee'));
     $date_trajet   = $db->quote($req->request->get('date_trajet'));   
     $distance      = $db->quote($req->request->get('distance')); 
     $duree         = $db->quote($req->request->get('duree')); 

   
     $db->query("INSERT INTO `trajet`(`ID_TRAJET`,`ALLERRETOUR`, `DEPART`, `ARRIVEE`, `DATE_TRAJET`, `DISTANCE`, `DUREE`)
                 VALUES ('',$aller_retour, $depart, $arrivee, $date_trajet, $distance, $duree)") or die($db->errorInfo()[2]); 

     $id_trajet = $db->lastInsertId();

     $db->query("INSERT INTO `affectertrajet`(`ID_RESERVATION`, `ID_TRAJET`) VALUES ($id_reservation, $id_trajet)") or die ($db->errorInfo()[2]); 

     // prix à renvoyer
     $dist = floatval( str_replace(",",".", $req->request->get('distance')) ); //floatval($distance);
     $nbpersonne = $req->request->get('nbpersonne');
     $prix = $aller_retour == 0 ? calculerPrix($dist, $nbpersonne) : 2*calculerPrix($dist, $nbpersonne);
     //$prix .="€"; 
     
     // To do : tenir compte du lieu de départ saisi dans le formulaire de réservation
     // 
      $to = $req->request->get('to');
      if ($to){
      switch ($to) {
        case "degaulle":
             if(1<=$nbpersonne && $nbpersonne <=4)
               $prix = 50;
             else if( $nbpersonne == 5)
               $prix = 65;
             else if (6<=$nbpersonne && $nbpersonne <=8)
               $prix = 85;
          break;
          case "orly":
               if(1<=$nbpersonne && $nbpersonne <=4)
               $prix = 45;
             else if( $nbpersonne == 5)
               $prix = 55;
             else if (6<=$nbpersonne && $nbpersonne <=8)
               $prix = 70;
             break;
          case "beauvais":
               if(1<=$nbpersonne && $nbpersonne <=4)
               $prix = 90;
             else if( $nbpersonne == 5)
               $prix = 115;
             else if (6<=$nbpersonne && $nbpersonne <=8)
               $prix = 150; 
               break; 
       // default: $prix = calculerPrix($dist, $nbpersonne);
      }
     }

     
     
     $dt=date('d / m/ Y');
 //================generer pdf
$contentnue=$app['twig']->render('reservations/pdf.html',array('nom'=>$req->request->get('nom')." ".$req->request->get('prenom'),
							       'adresse'=>$req->request->get('adresse'),
							       'telephone'=>$req->request->get('tmobile'),
							       'email'=>$req->request->get('email'),
							       'service'=>$req->request->get('service'),
							       'nbpersonne'=>$req->request->get('nbpersonne'),
							       'chvdo'=>$req->request->get('chvdo'),
							       'date'=>$req->request->get('date_reserv'),
							       'depart'=>$req->request->get('depart'),
							       'arrivee'=>$req->request->get('arrivee'),
							       'ar'=>$req->request->get('aller_retour'),
							       'distance'=>$req->request->get('distance'),
							       'duree'=>$req->request->get('duree'),
							       'prix'=>$prix,
							       'id_reservation'=>$id_reservation,
							       'date'=>$dt
							       )); 
 gpdf($contentnue,$id_reservation,'reservations/pdf');

//============finpdf
//========envoi de mail
global $env_mail;
global $site;
global $livraison;
  $message=$app['twig']->render('web/mail/reservation.html', array('nom'=>$req->request->get('nom')." ".$req->request->get('prenom'),
							    'object'=>'Information Reservation',
							    'id_reservation'=>$id_reservation,
							    'site'=>$site));
 $env_mail->envoyerp($req->request->get('email'),'FICHE DE ROUTE',$message,"reservations/pdf/$id_reservation.pdf");
 $env_mail->envoyerp($livraison,'NOUVELLE RESERVATION',$message,"reservations/pdf/$id_reservation.pdf");
 //=========fin envoi de mail
     

     return "<script type='text/javascript'>$('#bt_reserv').hide(); $('.alert').hide(); $('#remail').show();$('#fiche_reserv').trigger('fiche_reserv'); sessionStorage.setItem('prix',$prix);</script>
            <div class='succes'>Félicitaions !! Votre réservation a bien été enregistrée. Un mail de confirmation vient de vous être envoyé.</div>"; 
  });
// END RESERVATION -----------------------------------------------------------------------------------------------------------------------------------------

  
 //On définit une route pour la connexion d' un client==================================================================
$app->get('/checkmail', function(Application $app, Request $req) {

  global $db;
  $mail = $req->query->get('mail');
  if( empty($mail) ) {
      return "<div class='erreur'>Votre email n'est pas saisi !</div>";
  }
  if(! filter_var($mail, FILTER_VALIDATE_EMAIL)){
      return"<div class='avertissement'>Le format de votre adresse mail est incorrect , veuillez le corriger</div>";
  }
  $mail = $db->quote($mail);
  $r = $db->query("SELECT * FROM `client` WHERE  `MAIL`=$mail ")or die($db->errorInfo()[2]);
  $r = $r->fetch();
  
  if(empty($r)){
      return "0";
  }
  return "1";
     
});

// DEBUT Pré-remplir les champs de l'identité pour la réservation d'un client
$app->get('/pre_saisir', function(Application $app, Request $req){
   global $db;

   $r = "non_connect&eacute;";
   if ( $app['session']->get('id') ){
      $id = $app['session']->get('id');
      $r = $db->query("SELECT * FROM `client` WHERE `ID_CLIENT`=$id ")or die($db->errorInfo()[2]);
      $r = $r->fetch(PDO::FETCH_ASSOC);
      return $app->json($r);
   } 
   return $app->json(array('erreur'=>$r));

});
// [FIN pre_saisir]  ------------------

$app->get('/orly', function(Application $app, Request $req) { 
   include ('session.php');
   return $app['twig']->render('web/serviceo.html', array('title'      => 'ORLY',
                                                        'description'=> 'desc',
                                                        'key'        => 'orly',
                                                        'session'    =>  $session,
                                                        )); 
});

// 
$app->get('/degaulle', function(Application $app, Request $req) { 
    include ('session.php');
    return $app['twig']->render('web/serviced.html', array('title'      => 'Charles De Gaulle',
                                                        'description'=> 'desc',
                                                        'key'        => 'charles_de_gaulle',
                                                        'session'    =>  $session,
                                                        )); 
   //return "DE GAULLE";
});

$app->get('/aeoroports', function(Application $app, Request $req) { 
    include ('session.php');
    return $app['twig']->render('web/serviced.html', array('title'      => 'Aeroports',
                                                        'description'=> 'desc',
                                                        'key'        => 'charles_de_gaulle',
                                                        'session'    =>  $session,
                                                        )); 
   //return "DE GAULLE";
});

$app->get('/beauvais', function(Application $app, Request $req) { 
   include ('session.php');
   return $app['twig']->render('web/serviceb.html', array('title'    => 'LE BEAUVAIS',
                                                        'description'=> 'desc',
                                                        'key'        => 'beauvais',
                                                        'session'    =>  $session,
                                                        ));
});
$app->get('/evenement', function(Application $app, Request $req) { 
    include ('session.php');

    $page  = 'web/autres_events.html';
    $titre = 'Autres evenements';

    if($req->query->get('type') && $req->query->get('type')=='nightclub')
    {
      $page='web/night_club.html';
      $titre='Night Club';
      $text='';
    }
    if($req->query->get('type') && $req->query->get('type')=='excursion')
    {
      $page='web/excursion.html';
      $titre='Excursion';
      $text='';
    }
    return $app['twig']->render('web/service_events.html', array( 'title'      => $titre,
                                                                  'description'=> 'desc',
                                                                  'key'        => 'chauffeur privé',
                                                                  'session'    => $session,
                                                                  'page'       => $page,
                                                                  'text'       => $text
                                                                  ));
});


$app->get('/affaire', function(Application $app, Request $req) { 
   include ('session.php');
   return $app['twig']->render('web/service_aux_entreprises.html', array('title'       => 'Personne Agée',
                                                                'description'=> 'desc',
                                                                'key'        => 'agee',
                                                                'session'    =>  $session,
                                                                )); 
});









$app->run();

?>
