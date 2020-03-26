<?php
/**********************************************************************************
getmusic.php
Page looks up the Marvel character, and if it exists, gets a list of songs
Copyright Jonathan Lazar 2015
**********************************************************************************/

require_once 'includes/header.php';

$name=$_POST['name'];

if (empty($name)) {
	echo 'Please enter a character name';
} else {
	$marvel = new Marvel(MARVEL_KEY, MARVEL_SECRET);
	$char = $marvel->getCharacter($name);

	if (empty($char)) {
		echo $name .' is not a known Marvel character';
	} else {
		$spotify = new Spotify(SPOTIFY_KEY, SPOTIFY_SECRET);
		$songs = $spotify->getSongs($name);
	}

	echo $char.$songs.'<br style="clear: left;" />';;
}
