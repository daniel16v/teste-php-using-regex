<?php
require __DIR__ . '/vendor/autoload.php';


// abrir o arquivo

$parser = new \Smalot\PdfParser\Parser();
$pdf    = $parser->parseFile('Aprovados.pdf');


// ler arquivo

$text = $pdf->getText();
$string = preg_replace('/[\x00-\x1F\x7F-\xFF]/', ' ', $text);
var_dump($string);exit;
$teste = preg_replace('regex', '',$text);
$teste = preg_match_all('regex', $text, $match);


// Realizar limpeza do arquivo
// percorrer string (texto completo)
// formatar o texto
// gerar arquivo csv ou xls

?>
