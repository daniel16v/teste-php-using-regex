<?php

require __DIR__ . '/vendor/autoload.php';

$response = extractPdfAndReturnPatternFound();
$response = runsPattern($response);
exportCsv($response);
  
/**
 * Extrai o texto do pdf e retorna um array com os resultados encontrados conforme o padrão utilizado
 */
function extractPdfAndReturnPatternFound() {
  $parser = new \Smalot\PdfParser\Parser();
  $pdf = $parser->parseFile("classificados.pdf");
  $text = $pdf->getText();

  $string = preg_replace('/\r?\n|\r/',' ', $text); 
  $string = preg_replace('/([\x00-\x1F\x80-\xFF])/', ' ', $string);
  $string = preg_replace('/ [0-9] /', '', $string);

  preg_match_all("/([0-9]+, +[a-zA-ZáÁãÃâÂàÀèéÉêÊíÍìóÓíÜõÕôÔäöú'Úç’Çü ]+, +[0-9]+, +[0-9.]+)|reservadas/", $string, $return);
 
  return $return;
}

/**
 * Percorre o array separando e extraindo as informações
 * além de separar se o classificado é deficiente
 */
function runsPattern($response) {
  $data['classificados'] = [];
  $classificados = 'classificados';

  foreach($response[0] as $classified) {
    if($classified == 'reservadas') {
      $classificados = 'classificados deficiente';
    } else {
      $classified = explode(',', $classified);
      $data[$classificados][] = mountData($classified);
    }
  }

  $data = orderByNota($data);

  return $data;
}

/**
 * Realiza a montagem do objeto
 */
function mountData($classified) {
  $head = ['matricula', 'nome', 'acertos', 'nota'];
  
  foreach($classified as $key => $item) {
    $data[$head[$key]] = $item;
  }

  return $data;
}

/**
 * Ordena os classificados pela nota em ordem decrescente
 */
function orderByNota($data) {
  function cmp($a, $b) {
    return $a['nota'] < $b['nota'];
  }

  usort($data['classificados'], 'cmp');
  usort($data['classificados deficiente'], 'cmp');

  return $data;
}

/**
 * Exporta o arquivo CSV
 */
function exportCsv($data) {
  // header("Content-Type: text/plain");
  $filename = "classificados". ".xls";
  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Content-Type: application/vnd.ms-excel");

  $flag = false;
  $flagClassificados = false;

  foreach($data as $key => $classificados) {

    if($key == 'classificados'){
      echo $key . "\r\n\n";
    } else {
      echo "\n". $key . "\r\n\n";
      $flag = false;
    }

    foreach($classificados as $row){

      if(!$flag) {
        echo implode("\t", array_keys($row)) . "\r\n";
        $flag = true;
      }

      echo implode("\t", array_values($row)) . "\r\n";
    }  
  }

  exit;
}