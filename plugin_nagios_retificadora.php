#!/usr/bin/php
<?php	
//Plugin para verificar retificado Huawe ETP48xx com SMU
// Por Giovana M. Sato 
// algum dia de 2021 ou 2022
// versão 0.99999999

//variavel que recebe o ip
$host = $argv[1];

//variavel que recebe a communit
$communit = $argv[2];

//variavel que recebe o que vai ser consultado
$parametros = $argv[3];

$amperagemBateria = $argv[4];
//$amperagemBateria = 1000;


$aviso = $argv[5];
$critico = $argv[6];

//variavel que recebe a "vers�o", se for igual a 2 ele consulta MIB diferente de Load e Tens�o do Barramento
//se for nulo ou diferente de 2 ele consulta as MIB padr�o
$versao = $argv[7];


$ac = snmp2_get("$host", "$communit", "1.3.6.1.4.1.2011.6.164.1.3.2.2.1.6.28672");
$AC = str_replace("Gauge32: ", "", $ac);

if ($versao == 2){
	$loaddc = snmp2_get("$host", "$communit", ".1.3.6.1.4.1.2011.6.164.1.6.2.1.1.5.3980");
	$loadDC = str_replace("INTEGER: ", "", $loaddc);

}else{
	$loaddc = snmp2_get("$host", "$communit", ".1.3.6.1.4.1.2011.6.164.1.6.1.4.0");
	$loadDC = str_replace("INTEGER: ", "", $loaddc);
}


$tensaof1 = snmp2_get("$host", "$communit", "1.3.6.1.4.1.2011.6.164.1.3.2.2.1.3.28672");
$tensaoF1 = str_replace("Gauge32: ", "", $tensaof1);


$tensaof2 = snmp2_get("$host", "$communit", "1.3.6.1.4.1.2011.6.164.1.3.2.2.1.3.28673");
$tensaoF2 = str_replace("Gauge32: ", "", $tensaof2);


$statusf1 = snmp2_get("$host", "$communit", "1.3.6.1.4.1.2011.6.164.1.3.2.2.1.99.28672");
$statusF1 = str_replace("INTEGER: ", "", $statusf1);

if ($versao == 2){
	$tensaoBarramento = ($tensaoF1 + $tensaoF2)/2;
}else{
	$tensaobarramento = snmp2_get("$host", "$communit", ".1.3.6.1.4.1.2011.6.164.1.6.1.3.0");	
	$tensaoBarramento = str_replace("Gauge32: ", "", $tensaobarramento);
}


$statusf2 = snmp2_get("$host", "$communit", "1.3.6.1.4.1.2011.6.164.1.3.2.2.1.99.28673");
$statusF2 = str_replace("INTEGER: ", "", $statusf2);


$cargaWatts = $loadDC * $tensaoBarramento;



$bateriaWatts = $tensaoBarramento * $amperagemBateria;


$autonomia = $bateriaWatts / $cargaWatts;


$horas = explode('.', $autonomia);

switch($parametros) {
	
	case("AC"):
		if($AC == "2147483647"){
			echo "Sem tensão de entrada - Em bateria";
			exit(2);
		}elseif($AC > $aviso && $AC < $critico){
			echo "Ok - " . $AC . "V ";
			exit(0);
		}elseif($AC >= $critico or $AC <= $aviso){
			echo "Aviso - " . $AC . "V ";
			exit(1);
		}echo "AC Retorno desconhecido - Return: $AC ";
		exit(3);
		break; 

	case("loadDC"):	
		if($loadDC > $aviso && $loadDC <= $critico){
			echo "Ok - " . $loadDC . "A ";
			exit(0);
		}elseif($loadDC >= $critico or $loadDC <= $aviso){
			echo "Critico - " . $loadDC . "A ";
			exit(2);
		}echo "Retorno desconhecido - Return: $loadDC ";
		exit(3);
		break;
		
	case("tensaoF1"):
		if($tensaoF1 == "2147483647" && $statusF1 == "2"){
			echo "Houve uma falha na F1 ";
			exit(2);
		}elseif($tensaoF1 == "2147483647" && $statusF1 == "4"){
			echo "Falha na comunicação F1 ";
			exit(2);
		}elseif($tensaoF1 == "2147483647" && $statusF1 == "5"){
			echo "Fonte desligada ";
			exit(2);
		}elseif($tensaoF1 > $aviso && $tensaoF1 < $critico){
			echo "Ok - Tudo certo " . $tensaoF1 . "V ";
			exit(0);
		}elseif($tensaoF1 >= $critico or $tensaoF1 <= $aviso){
			echo "Critico - " . $tensaoF1 . "V ";
			exit(2);
		}echo "Retorno desconhecido - Return: $tensaoF1";
		exit(3);
		break;
		
			case("tensaoF2"):
		if($tensaoF2 == "2147483647" && $statusF2 == "2"){
			echo "Houve uma falha na F2 ";
			exit(2);
		}elseif($tensaoF2 == "2147483647" && $statusF2 == "4"){
			echo "Falha na comunicação F2 ";
			exit(2);
		}elseif($tensaoF2 == "2147483647" && $statusF2 == "5"){
			echo "Fonte desligada F2 ";
			exit(2);
		}elseif($tensaoF2 > $aviso && $tensaoF2 < $critico){
			echo "Ok - Tudo certo " . $tensaoF2 . "V ";
			exit(0);
		}elseif($tensaoF2 >= $critico or $tensaoF2 <= $aviso){
			echo "Critico - " . $tensaoF2 . "V ";
			exit(2);
		}echo "Retorno desconhecido - Return: $tensaoF2 ";
		exit(3);
		break;
		
		case("horas"):
			if($horas[0] == 0){
				echo "CRITICO - " . $horas[0] . "h de autonomia ";
				exit(2);
			}elseif($horas[0] <= $critico){
				echo "CRITICO - " . $horas[0] . "h de autonomia ";
				exit(2);
			}elseif($horas[0] <= $aviso){
				echo "AVISO - " . $horas[0] . "h de autonomia ";
				exit(1);
			}else{
				echo "Ok - " . $horas[0] . "h de autonomia ";
				exit(0);
			}echo "Retorno desconhecido - Return: $horas[0] ";
		exit(3);
		break;
		
		
		default:
			echo "Parametro desconhecido ";
			exit(3);
}
