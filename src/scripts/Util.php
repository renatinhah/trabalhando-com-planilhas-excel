<?php

class Util {

    public function moveFolderFile($file, $newFile, $originPath, $destinPath, $importStatus){
        if($importStatus){
            $destin = $destinPath.DIRECTORY_SEPARATOR.$file;
            $origin =  $originPath.DIRECTORY_SEPARATOR.$file;
            copy($origin, $destin);
            rename($destin, $destinPath.DIRECTORY_SEPARATOR.$newFile);
            unlink($origin);
        }  else {
            if(substr($newFile, 0, 4) !== 'Form'){
                $strs = explode('-', $newFile, 30);
                $table = $strs[0];
                $domain = $strs[1];
                $mes = $strs[2];
            } else {
                $domain = $mes = null;
            }
            $this->saveLog($importStatus, $newFile, $domain, $mes, $table);
        }
    }
    
    public function saveLog($inserted, $newFile, $domain=null, $mes=null, $table = null){
        date_default_timezone_set('America/Sao_Paulo');
        $path = "importLog";
        $message = (empty($domain) && empty($mes)) ? $newFile.' in ' : $domain." - ".$mes. " - ".$table.' in ';
        $message .= (date('d/m/Y')).'*'.PHP_EOL;
        $pathToSend = $inserted === 1 ? $path.DIRECTORY_SEPARATOR.'importLog.txt' : $path.DIRECTORY_SEPARATOR.'importErrorLog.txt';
        file_put_contents($pathToSend, "\n".$message, FILE_APPEND);
    }
            
}