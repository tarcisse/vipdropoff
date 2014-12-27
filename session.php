<?php

 $session=array();
   if($app['session']->get('connexion') && $app['session']->get('connexion')=='true' )
    {
	$session['etat']='true';
	$session['user']=$app['session']->get('nom');
	$session['id_session']=$app['session']->getId();
    }
    else
    {
	$session['etat']='false';
	$session['user']='';
	$session['id_session']='';
    }

?>