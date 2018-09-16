<?php

require 'src/scripts/Util.php';
require 'src/scripts/script.php';

$path = "filesToImport";
$destinPath = "importedFiles";

foreach (glob ($dir.'*') as $folder) { 
    if($folder === "filesToImport"){ 
        $spreadsheets = [];
        if(is_dir($path)){
            $directory = dir($path);
            while(($file = $directory->read()) !== false){
                if($file != ".." && $file != "."){
                    array_push($spreadsheets, $file);
                }
            }
            $directory->close();
        } else{
            echo 'A pasta não existe.';
        } 
        $script = new Script();
        $script->readSpreadsheet($path, $spreadsheets, $destinPath);
    } 
}