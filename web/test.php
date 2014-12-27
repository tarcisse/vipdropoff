<?php


///----------------------------bdd------------------
 try {
    $dns = 'mysql:host=127.0.0.1;dbname=analyse-quantitative';
    $utilisateur = 'root';
    $motDePasse = '';// tu remplace juste le mot de passe par vide
    $db = new PDO( $dns, $utilisateur, $motDePasse );
}
catch ( Exception $e ) {
  die ($e->getMessage()); 
}




function ex2() {
    
   $n= count( func_get_args() );
   $par=func_get_args();
   
    if($n==2)
       return pow($par[0],2)*(1+pow($par[1],2));
    elseif($n==5)
    {
        $ex=ex($par[0],$par[1],$par[2],$par[3]);
        $cv=$par[4];
        return pow($ex,2)*(1+pow($cv,2));
    }
    else
        return"La fonction ne recoit que 2 ou 6 arguments";
}

//echo ex2(1500,1920000,8,'s',4);

/**
 *bande passante equivalente
 *
 */

function roeq() {
    //1er ro
    //2eme lamda
    //3eme ex() =>ex(x1,x2) || =>ex(x1,x2,x3,x4,x5,x6)
    //4E(s3)
    
    
   $n= count( func_get_args() );//on recupere le nombre des fonctions
   $par=func_get_args();//on recupere les argument dans un tableau
   
   if($n==4)
       return  $par[0]+ (($par[1]*$par[2])/2*$par[3]);
    elseif($n==5)
       return  $par[0]+ (($par[1]*ex2($par[3],$par[4]))/2*$par[5]); 
    elseif($n==5)
       return  $par[0]+ (($par[1]*ex2($par[3],$par[4],$par[5],$par[6],$par[7],$par[8]))/2*$par[9]);
    else
       return"La fonction ne recoit que 2 ou 6 arguments";
    
}


// Calcul de E[X]
   function ex($l, $v, $octet, $unite){ // $octet = 1 ou 8, $unite = "s" ou "ms"   	 
   	  	$res = $l*$octet/$v;
   	  	return $unite =="ms" ? $res*1000:$res;       
   }

   

 
 function recpEx($session)
 {
    global $db;
    $ex=array();
    $r=$db->query("SELECT * FROM `classe` WHERE `session`= '$session'");
   $r=$r->fetchAll();

    foreach($r as $session)
 {
    array_push($ex,ex($session['el_moy'],64000,8,'s',$session['cv'] ));
    //print_r($session);
    //echo"<br>";
 }
    return $ex;
 }

/*$t=recpEx('Exo1');
foreach($t as $session)
 {
    print_r($session);
    echo "<br>";
   
 }*/

 
 function recpEx2($session)
 {
    global $db;
    $ex2=array();
    $ex=recpEx($session);
    //print_r($ex);
    $r=$db->query("SELECT * FROM `classe` WHERE `session`= '$session'");
   $r=$r->fetchAll();
$i=0;
    foreach($r as $session)
 {
    array_push($ex2,ex2($ex[$i],$session['cv'] ));
    //echo $ex[$i]." ".$session['cv']."<br>";
    $i++;
 }
    return $ex2;
 }
 
 /*$t=recpEx2('exo1');
 echo "<br>";
 foreach($t as $session)
 {
    print_r($session);
    echo "<br>";
   
 }*/
 
 
 function recpL($session)
 {
    global $db;
    $ex=recpEx($session);
    $L=0;
    $r=$db->query("SELECT * FROM `classe` WHERE `session`= '$session'");
   $r=$r->fetchAll();
$i=0;
    foreach($r as $session)
 {
    $L+=$session['lambda']*$ex[$i];
    //echo $ex[$i]." ".$session['cv']."<br>";
    $i++;
 }
    return $L;
 }
 
 
 //echo recpL('exo1');
 
 
 
 function lambdaI($session)
 {
    global $db;
    $LambdaI=array();
    $r=$db->query("SELECT * FROM `classe` WHERE `session`= '$session'");
   $r=$r->fetchAll();
   $i=0;
     foreach($r as $session)
 {
    array_push($LambdaI ,$session['lambda']);
    //echo $ex[$i]." ".$session['cv']."<br>";
    $i++;
 }
   return $LambdaI;
 }
 
 
 
 function Ew($session){
    
    $ex2=recpEx2($session);
    $L=recpL($session);
    global $db;
    $r=$db->query("SELECT * FROM `classe` WHERE `session`= '$session'");
   $r=$r->fetchAll();
   $i=0;
   $somme=0;
       foreach($r as $session)
 {
    $somme+=$session['lambda']*$ex2[$i];
 }
 $denominateur=2*(1-$L);
 $Ew=$somme/$denominateur;
 return $Ew;
 }
 
 //echo Ew('exo1');
 
?>