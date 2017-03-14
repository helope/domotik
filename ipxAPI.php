<?php
$json = "http://10.102.71.49/api/xdevices.json?";
$jsonRelai = $json."Get=R";
$jsonDigi = $json."Get=D";
$jsonAnalog = $json."Get=A";
//creation des requetes json pour recuperer l'etat des relais, entrees digitales et analogiques de l'ipx

$content_relai = json_decode(file_get_contents($jsonRelai, true));
$content_digi = json_decode(file_get_contents($jsonDigi, true));
$content_analog = json_decode(file_get_contents($jsonAnalog, true));
//recuperation du json, on parse celui-ci et on stocke dans des variables


exec('scp root@10.102.71.52:/etc/asterisk/extensions.conf /web/extensions.conf');
$texte = file_get_contents('/web/extensions.conf');
//on recupere le fichier extensions.conf du serveur asterisk


if($content_analog->A1 < 29000){
//si la valeur retournee par le capteur lumineux est inferieur a 28900 lumen
	$replace = file_get_contents('/web/capteur_lumOFF.txt');
//on met le texte correspondant à l'abscence de lumiere dans une variable
	$modif = preg_replace("/;A(.*);A/msU", $replace, $texte);
//on remplace le texte présent dans la balise ;A du fichier extensions.conf
}else{
	$replace = file_get_contents('/web/capteur_lumON.txt');
	$modif = preg_replace("/;A(.*);A/msU", $replace, $texte);

}
//modife le contenu du fichier extenions.conf en fonction du capteur de lumiere

if($content_digi->D1 == 1){
	$replace = file_get_contents('/web/capteur_porteON.txt');
        $modif = preg_replace("/;B(.*);B/msU", $replace, $modif);
} else {
	$replace = file_get_contents('/web/capteur_porteOFF.txt');
        $modif = preg_replace("/;B(.*);B/msU", $replace, $modif);

}
//modife le contenu du fichier extenions.conf en fonction du capteur de porte


if($content_digi->D2 == 1){
        $replace = file_get_contents('/web/capteur_presON.txt');
        $modif = preg_replace("/;C(.*);C/msU", $replace, $modif);
} else {
        $replace = file_get_contents('/web/capteur_presOFF.txt');
        $modif = preg_replace("/;C(.*);C/msU", $replace, $modif);

}
//modife le contenu du fichier extenions.conf en fonction du capteur de presence


file_put_contents('extensions.conf', $modif);
//contenu modifie pousser dans extensions.conf sur le serveur web
exec('scp /web/extensions.conf root@10.102.71.52:/etc/asterisk/extensions.conf');
//extensions.conf modifié poussé sur le serveur asterisk
exec('ssh root@10.102.71.52 asterisk -rx reload');
//reload du serveur asterisk pour la prise en compte des modifications
unlink('/web/extensions.conf');
//suppression du fichier extensions.conf sur le serveur web
