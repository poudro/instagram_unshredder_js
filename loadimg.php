<?php

session_start();


// make sure everything is ok
if (!isset($_GET['url']) || !preg_match('%^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?.*\.(png|jpg|gif)$%i', rawurldecode($_GET['url'])) || !isset($_GET['tok']) || !isset($_SESSION['priv']) || !isset($_GET['pub']) || isset($_GET['tok']) && isset($_SESSION['priv']) && isset($_GET['pub']) && $_GET['pub'] != md5($_GET['tok'] . 'SALT_1' .  $_SESSION['priv'] . 'SALT_2'))
  die();
  
$url = preg_replace('/ /','%20',rawurldecode($_GET['url']));


$handle = fopen($url, "r");
if (!$handle)
  die($handle);
$contents = "";
while (!feof($handle)) {
  $contents .= fread($handle, 8192);
}
fclose($handle);

$im = imagecreatefromstring($contents);

$oW = imagesx($im);
$oH = imagesy($im);

if ($oW > 640)
{
  $nW = 640;
  $nH = round($oH * $nW / $oW);
  
  $newim = imagecreatetruecolor($nW, $nH);
  imagecopyresized($newim, $im, 0,0,0,0, $nW,$nH,$oW,$oH);
  imagedestroy($im);
  $im = $newim;
  $oW = $nW;
  $oH = $nH;
}

$numShreds = (isset($_GET['nshreds'])&&intval($_GET['nshreds'])!=0)?intval($_GET['nshreds']):20;
if ($oW/$numShreds<6)
  $numShreds = floor($oW/6);

if ($oW%$numShreds != 0)
{
  $nW = floor($oW/$numShreds)*$numShreds;
  $nH = round($oH * $nW / $oW);
  
  $newim = imagecreatetruecolor($nW, $nH);
  imagecopyresized($newim, $im, 0,0,0,0, $nW,$nH,$oW,$oH);
  imagedestroy($im);
  $im = $newim;
  $oW = $nW;
  $oH = $nH;
}


$shredWidth = intval($oW/$numShreds);
$outim = imagecreatetruecolor($oW, $oH);

  $shOrder = array();
  for ($i=0; $i<$numShreds; ++$i)
    $shOrder[] = $i;
if (!isset($_GET['as']) || isset($_GET['as']) && $_GET['as'] != "t")
{
  shuffle($shOrder);
}

for ($i=0; $i<$numShreds; ++$i)
{
  imagecopy($outim, $im, $i*$shredWidth, 0, $shOrder[$i]*$shredWidth, 0, $shredWidth, $oH);  
}





header('Content-Type: image/png');
imagepng($outim);
imagedestroy($im);
imagedestroy($outim);
