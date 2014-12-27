<?php
$image = file_get_contents('https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d1314.4363686678755!2d2.1303633999999994!3d48.78431979999998!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e67dd6b770b3f7%3A0xdd2ee2b174235ece!2s18B+Rue+du+Haras%2C+78530+Buc!5e0!3m2!1sfr!2sfr!4v1418161682654'); 
$fp  = fopen('ae.png', 'w+'); 

fputs($fp, $image); 
fclose($fp); 
unset($image);
?>