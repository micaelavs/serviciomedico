<?php
/** @var array $data */
/** @var string $nombre */
/** @var array $titulos */
/** @var null | string $separador */

header('Content-Encoding: UTF-8');
header("Content-type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=".$nombre.".csv");
header("Pragma: no-cache");
header("Expires: 0");

$separador = (isset($separador) && !is_null($separador))? $separador:';';
$csv  = "\xEF\xBB\xBF"; //Byte Order Mark (BOM)
$csv .= implode($separador,$titulos);
$csv .= "\r\n";
foreach($data as $row) {
   $csv .= implode($separador,$row);
   $csv .= "\r\n";
}
$csv .= "\r\n";
$csv .= "total registros: ".count($data);

echo $csv;
exit;
