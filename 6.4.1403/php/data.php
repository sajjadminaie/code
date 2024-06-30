<?php

//  $mainFolder              =>    ./Final Export
//  $sub_mainFolder          =>     1-HeyatVaDRR || 2-KholasatolHesab || 3-Oghlidos
//  $second_sub_mainFolder   =>     HeyatVaDRR 1.mp4
//  $mainFolderlength        =>     ex 3
//  &sub_mainFolderlength    =>     ex 180
$mainFolder = "../Sort/Library/Majles";
$sub_mainFolder = scandir($mainFolder);
$mainFolderlength = count($sub_mainFolder);

for ($i = 0; $i < $mainFolderlength; $i++) {

  if ($sub_mainFolder[$i] == "..") {
    array_splice($sub_mainFolder, $i, 1);
    $mainFolderlength--;
  }
}
$mainFolderlength = count($sub_mainFolder);
for ($i = 0; $i < $mainFolderlength; $i++) {

  if ($sub_mainFolder[$i] == ".") {
    array_splice($sub_mainFolder, $i, 1);
    $mainFolderlength--;
  }
}
$mainFolderlength = count($sub_mainFolder);

$second_sub_mainFolder = [];
$imageDimensions = [];

for ($i = 0; $i < $mainFolderlength; $i++) {
  if (!is_file($mainFolder . '/' . $sub_mainFolder[$i])) {
    $second_sub_mainFolder[] = scandir("$mainFolder/$sub_mainFolder[$i]");
    array_splice($second_sub_mainFolder[$i], 0, 2);
    sort($second_sub_mainFolder[$i], SORT_NATURAL);
      
    // Get image dimensions
    $folderDimensions = [];
    foreach ($second_sub_mainFolder[$i] as $file) {
      $filePath = $mainFolder . '/' . $sub_mainFolder[$i] . '/' . $file;
      // echo $filePath; 
      list($width, $height) = getimagesize($filePath);
      $folderDimensions[] = ['width' => $width, 'height' => $height];
    }
    $imageDimensions[] = $folderDimensions;
  }
  $sub_mainFolderlength[] = count($second_sub_mainFolder[$i]);
}


if ($_GET['req'] == "sub_mainFolder") {
  echo json_encode($sub_mainFolder, JSON_UNESCAPED_UNICODE);
} else if ($_GET['req'] == "mainFolderlength") {
  echo $mainFolderlength;
} else if ($_GET['req'] == "second_sub_mainFolder") {
  echo json_encode($second_sub_mainFolder);
} else if ($_GET['req'] == "imagesize") {
  echo json_encode($imageDimensions);
}










