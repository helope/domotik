<?php
$ipASTERISK = "10.102.76.24";
//addresse ip srv astersik
$ipIPX = "10.102.76.42";
//addresse ip IPX
$ipWEB ="10.102.76.49.";

$ipAST = "root@".$ipASTERISK;
//l'addresse ip asterisk n'est utilisee que pour des connexions ssh
$ipIPX = $ipIPX."/api";
//l'addresse ip IPX n'est utilisee que pour des requetes http
$ipWEB = $ipWEB.":1880";

exec('scp '.$ipAST.':/etc/asterisk/extensions.conf /web/extensions1.conf');
$texte = file_get_contents('/web/extensions1.conf');
//on recupere le fichier extensions.conf du serveur asterisk et on place son contenu dans une variable

$dir = '/web';
$files = scandir($dir);
//on liste tout les fichiers presents dans /web
$line = count($files);
//on compte le nombre de fichier dans /web
$regexAST = '#root@([0-9]{1,3}.){3}.([0-9]{1,3})#';
//regex pour trouver les adresses ip d'asterisk presentes dans les fichiers
$regexIPX = '#([0-9]{1,3}.){3}.([0-9]{1,3})/api#';
//regex pour trouver les adresse de l'IPX presentes dans les fichiers
$regexWEB = '#([0-9]{1,3}.){3}.([0-9]{1,3}):1880#';
//regex pour trouver les adresse de l'IPX presentes dans les fichiers


for ($i=2; $i < $line; $i++) {
//les deux premiers fichiers de /web sont /. et /.. on ne les prend pas en compte
//On parcours tout les fichiers du repertoire /web
	if ($files[$i] == 'addIPX.php' || strstr($files[$i], 'git')) {
		continue;
	}
	echo($files[$i]);
//on ne modifie pas cd fichier
	$texte = file_get_contents($files[$i]);
	$modif = preg_replace($regexAST,$ipAST,$texte);
	file_put_contents($files[$i], $modif);
//remplace tout les adresses d'asterisk presentes dans les fichiers
	
	$texte = file_get_contents($files[$i]);
	$modif = preg_replace($regexIPX,$ipIPX,$texte);
	file_put_contents($files[$i], $modif);
//remplace tout les adresses de l'IPX presentes dans les fichiers
	$texte = file_get_contents($files[$i]);
    $modif = preg_replace($regexWEB,$ipWEB,$texte);
    file_put_contents($files[$i], $modif);
//remplace tout les adresses du serveur Web presentes dans les fichiers
//

}

exec('scp /web/extensions1.conf '.$ipAST.':/etc/asterisk/extensions.conf');
//on pousse le fichier modifie sur le fichier extensions.conf du serveur asterisk
exec('ssh '.$ipAST.' asterisk -rx reload');

//reload le serveur asterisk
unlink('/web/extensions1.conf');
//suppression du fichier de conf local
?>

