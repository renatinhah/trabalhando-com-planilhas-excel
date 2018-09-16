<?php

ini_set('default_charset','UTF-8');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);

// require_once 'src/scripts/Util.php';
require_once 'vendor/phpoffice/phpexcel/Classes/PHPExcel.php';

class Script {
 
    $conn = oci_connect("root", "root", "localhost/innovare");
   
    private function findAnomalyRules($quiz){
        if($quiz[1] == 2){
            for($i = 2; $i <= 6; $i++){
                if($quiz[$i] !== 0){
                    echo "Questionário ".$quiz[0]." apresenta erro de fluxo nas respostas a partir da questão v1 <br>";
                }
            }
        } else if($quiz[2] == 2){
            // $partQuiz = array_slice($quiz, 3, 6);
            // var_dump($partQuiz);
            for($i = 3; $i <= 6; $i++){
                if($quiz[$i] !== 0){
                    echo "Questionário ".$quiz[0]." apresenta erro de fluxo nas respostas a partir da questão v2 <br>";
                }
            }
        } else if($quiz[4] == 2){
            if($quiz[5] !== 0){
                echo "Questionário ".$quiz[0]." apresenta erro de fluxo nas respostas a partir da questão v4 <br>";
            }
        } else if($quiz[5] == 1){
             if($quiz[6] !== 0 || $quiz[7] !== 0){
                echo "Questionário ".$quiz[0]." apresenta erro de fluxo nas respostas a partir da questão v5 <br>";
            }
        } else if($quiz[7] == 2){
            for($i = 8; $i <= 13; $i++){
                if($quiz[$i] !== 0){
                    echo "Questionário ".$quiz[0]." apresenta erro de fluxo nas respostas a partir da questão v7 <br>";
                }
            }
        } else if($quiz[8] == 2){ 
            for($i = 9; $i <= 13; $i++){
                if($quiz[$i] !== 0){
                    echo "Questionário ".$quiz[0]." apresenta erro de fluxo nas respostas a partir da questão v8 <br>";
                }
            }
        }
    }

    public function findAnomalyValues($quiz) {
        $twoOptions = [1, 2]; 
        $threeOptions = [0, 1, 2]; 
        $fourOptions = [0, 1, 2, 3]; 
        $fiveOptions = [1, 2, 3, 4, 5]; 
        $sixOptions = [0, 1, 2, 3, 4, 5]; 
        $tenOptions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]; 

        for($i = 1; $i < sizeof($quiz); $i++){
            if($i == 1){
                if(!in_array($quiz[$i], $twoOptions)){
                    echo "<li> O questionario ".$quiz[0]." apresenta valor inesperado em v2 <br>";
                }
            } else if($i==2 || $i==4 || $i==7 || $i==8 || $i==9 || $i==10 || $i==11 || $i==12 || $i==3){
                if(!in_array($quiz[$i], $threeOptions)){
                    echo "O questionario ".$quiz[0]." apresenta valor inesperado em v2, v4, v7, v8, v9, v10, v11, v12 ou v13 <br>";
                }
            } else if($i==3 || $i==5 ){
                if(!in_array($quiz[$i], $fourOptions)){
                    echo "O questionario ".$quiz[0]." apresenta valor inesperado em v3 ou v5 <br>";
                }
            } else if($i==15 || $i==16){
                if(!in_array($quiz[$i], $fiveOptions)){
                    echo "O questionario ".$quiz[0]." apresenta valor inesperado em v15 ou v16 <br>";
                }
            } else if($i==6){
                if(!in_array($quiz[$i], $sixOptions)){
                    echo "<O questionario ".$quiz[0]." apresenta valor inesperado em v6 <br>";
                }
            } else if($i==14){
                if(!in_array($quiz[$i], $tenOptions)){
                    echo "O questionario ".$quiz[0]." apresenta valor inesperado em v14 <br>";
                }
            }
        }
    }

    public function readSpreadsheet($originPath, $spreadsheets, $destinPath){
        foreach($spreadsheets as $file){
            $spreadsheetUser = PHPExcel_IOFactory::load($originPath.DIRECTORY_SEPARATOR.$file);
            foreach ($spreadsheetUser->getWorksheetIterator() as $spreadsheet) {
                $numbertRow     = $spreadsheet->getHighestRow(); 
                $numberColumn   = $spreadsheet->getHighestColumn();
                $numberColumn   = PHPExcel_Cell::columnIndexFromString($numberColumn);
                $quiz = [];
                 $prefix = "INSERT INTO quest_teste (id, v1, v2, v3, v4, v5, v6, v7, v8, v9, 
                                        v10, v11, v12, v13, v14, v15, v16) VALUES ";
                for($line=2; $line<=$numbertRow; $line++) {
                    $values = "(";
                    for($column=0; $column<$numberColumn; $column++) {
                        $cell = (int)$spreadsheetUser->getActiveSheet()->getCellByColumnAndRow($column, $line)->getValue();
                        $quiz[$column] = $cell;
                        $values .= $quiz[$column].", ";
                    }
                    $this->findAnomalyRules($quiz);
                    $this->findAnomalyValues($quiz);

                    $sql = $prefix.substr($values, 0, -2)." );";

                    $s = oci_parse($conn, $sql);
                    oci_execute($s); 
                } 
            }
        }
    }
}