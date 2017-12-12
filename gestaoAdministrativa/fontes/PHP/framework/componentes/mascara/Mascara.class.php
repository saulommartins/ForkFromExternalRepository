<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

$Id: Mascara.class.php 65805 2016-06-17 17:32:03Z franver $

Casos de uso: uc-01.01.00
*/

class Mascara extends Objeto
{
var $stMascara;
var $stMascarado;
var $stDesmascarado;

//SETTERS
function setMascara($valor) { $this->stMascara        = $valor; }
function setMascarado($valor) { $this->stMascarado      = $valor; }
function setDesmascarado($valor) { $this->stDesmascarado   = $valor; }

//GETTERS
function getMascara() { return $this->stMascara;        }
function getMascarado() { return $this->stMascarado;      }
function getDesmascarado() { return $this->stDesmascarado;   }

//METODO CONSTRUTOR
function Mascara()
{
}

//METODOS DA CLASSE
function mascaraDinamica()
{
    $stMascara = $this->getMascara();
    $stValor = $this->getDesmascarado();
    $arBlocos = preg_split("/[^0-9a-zA-Z]/", $stMascara );
    $stSeparadores =  preg_replace("/[0-9a-zA-Z]/","" ,$stMascara );
    $stMascarado = "";
    $inPos = 0;
    if ( count( $arBlocos ) ) {
        foreach ($arBlocos as $indice => $valor) {
            if ( $indice + 1 < count($arBlocos) ) {
                $stMascarado .= substr($stValor, $inPos, strlen($valor)).$stSeparadores[$indice];
                $inPos += strlen($valor);
            } else {
                $stMascarado .= substr($stValor, $inPos, strlen($valor));
            }
        }
    }
    if ( strlen( $stMascarado ) != strlen($stMascara) ) {
        $stMascarado = "";
    }
    $this->setMascarado( $stMascarado );
}

function demascara()
{
    $stDesmascarado =  preg_replace("/[^0-9a-zA-Z]/","" ,$this->getMascarado() );
    $this->setDesmascarado( $stDesmascarado );
}

function desmascaraDado(&$stDado)
{
    $stDado = preg_replace("/[^0-9a-zA-Z]/","" ,$stDado );
}

function mascaraDado(&$stDado)
{
    $stDado = $this->setDesmascarado( $stDado );
    $this->mascaraObjeto();
    $stDado = $this->getMascarado();
}

function mascaraObjeto()
{
    $this->mascaraDinamica();
}

function converterParaExpressaoRegular($prm)
{
    $parametro = $prm;
    $tamanho   = strlen($parametro);
    for ($x=0;$x<$tamanho;$x++) {
      if (preg_match('/[a-z]/',$parametro[$x])) {
        $expReg .= "[A-".$parametro[$x]."]";
      } elseif (preg_match('/[0-9]/',$parametro[$x])) {
        $expReg .= "[0-".$parametro[$x]."]";
      } else {
        $expReg .= '\\'.$parametro[$x];
     }
    }

 return $expReg;
}

function preencheMascaraComZeros($stValor, $stMascara)
{
    $arBlocos = preg_split("/[^0-9a-zA-Z]/", $stMascara );
    $stSeparadores =  preg_replace("/[0-9a-zA-Z]/","" ,$stMascara );
    $stMascarado = "";
    $arValor = preg_split("/[^0-9a-zA-Z]/", $stValor );
    $stValor = "";
    $i = 0;
    foreach ($arValor as $inCodigo) {
        $arMascara = each( $arBlocos );
        $arSeparador = $stSeparadores[ $i++ ];
        $stValor.= str_pad($inCodigo, strlen( $arMascara[value] ), "0", STR_PAD_LEFT).$arSeparador[value];
    }

    return $stValor;

}

/****************************************************************
/* $valor = preencheMascaraComZeros2(  '9', '9.99.99.99999' );
/*
/* Resultado = 0.00.00.00009
*/
function preencheMascaraComZeros2($stValor, $stMascara)
{
    $stMascaraLimpa = preg_replace( "/[^0-9a-zA-Z]/", "", $stMascara );
    $stValor = str_pad( $stValor, strlen( $stMascaraLimpa ), "0", STR_PAD_LEFT );
    $this->setDesmascarado( $stValor );
    $this->setMascara( $stMascara );
    $this->mascaraDinamica();

    return $this->getMascarado();
}

/**************************************************************************/
//** Função que valida um valor através de uma máscara
/**************************************************************************/
//Criado Por: Leonardo Tremper Qua Mai  7 16:25:33 UTC 2003
//Ex.: validaMascara("999.99.9999.9","1.22.564")
//retorna 001.22.0564.0

public static function validaMascara($mascara,$digitos)
{
    $mascaraFinal = "";
    $erros = 0;

    //Explode a mascara
    $elementosMascara =  explode(".",$mascara);

    //Total de elementos  da mascara
    $totalElMascara = sizeof($elementosMascara);

    //explode o digito
    $digitosMascara = explode(".",$digitos);

    //Total de elementos  do digito
    $totalDigitosMasc = sizeof($digitosMascara);

    //Inicia laço para comparação
    for ($i = 0; $i <= $totalElMascara; $i++) {
        //qtd algarismos na mascara e digitos
        $chMasc = (int) strlen($elementosMascara[$i]);
        $chDigi = (int) strlen($digitosMascara[$i]);

        if ($chDigi > $chMasc) {
            $erros++;
        }

        $chDiff = $chMasc - $chDigi;
        $chZeros = "";

        //laço para inserção de zeros
        for ($e = 0; $e < $chDiff; $e++) {
            $chZeros .= "0";
        }

        $mascaraFinal .= $chZeros.$digitosMascara[$i].".";
    }

    $tot = strlen($mascaraFinal);
    $tot = $tot-2;
    $mascaraFinal = substr($mascaraFinal, 0, $tot);

    if ($erros == 0) {
        $aMascara[0] = 1;
        $aMascara[1] = $mascaraFinal;
    } else {
        $aMascara[0] = 0;
        $aMascara[1] = "";
    }

    return $aMascara;
}// Fim da função validaMascara

/**************************************************************************/
//** Função que valida um valor através de uma máscara dinâmica
/**************************************************************************/
//Criado Por: Cassiano Ferreira e Ricardo Lopes
//Ex.: validaMascara("999.99-9999/9","1.22-564")
//retorna 001.22-0564/0
public static function validaMascaraDinamica($mascara,$digitos)
{
    $mascaraFinal = "";
    $erros = 0;

    //Explode a mascara
    $elementosMascara =  preg_split("/[^a-zA-Z0-9]/",$mascara);

    //Pega somente os separadores da máscara
    $separadoresMascara =  preg_replace("/[a-zA-Z0-9]/","",$mascara);

    //Total de elementos  da mascara
    $totalElMascara = sizeof($elementosMascara);

    //explode o digito
    $digitosMascara = preg_split("/[^a-zA-Z0-9]/",$digitos);

    //Total de elementos  do digito
    $totalDigitosMasc = sizeof($digitosMascara);

    //Inicia laço para comparação
    for ($i = 0; $i <= $totalElMascara; $i++) {
        //qtd algarismos na mascara e digitos
        $chMasc = (int) strlen($elementosMascara[$i]);
        $chDigi = (int) strlen($digitosMascara[$i]);

        if ($chDigi > $chMasc) {
            $erros++;
        }

        $chDiff = $chMasc - $chDigi;
        $chZeros = "";

        //laço para inserção de zeros
        for ($e = 0; $e < $chDiff; $e++) {
            $chZeros .= "0";
        }

        $separador = substr($separadoresMascara,$i,1);
        $mascaraFinal .= $chZeros.$digitosMascara[$i].$separador;
    }

    $tot = strlen($mascaraFinal);
    //$tot = $tot-2;
    //$mascaraFinal = substr($mascaraFinal, 0, $tot);

    if ($erros == 0) {
        $aMascara[0] = 1;
        $aMascara[1] = $mascaraFinal;
    } else {
        $aMascara[0] = 0;
        $aMascara[1] = "";
    }

    return $aMascara;
}// Fim da função validaMascaraDinamica

/**************************************************************************/
//** Função que formata um valor informado para dentro de uma máscara
/**************************************************************************/
//Criado Por: Ricardo Lopes   08/07/2003
//Ex.: geraMascara("999.99.9999.9","0012205640")
//retorna 001.22.0564.0

public static function geraMascara($mascara,$valor)
{
$mascaraFinal = "";
$erros = 0;

//Explode a mascara
$elementosMascara =  explode(".",$mascara);

$res = ""; //resultado da formatação
$pos = 0; //Posição do cursor na string informada

//Separa os dados da string fornecida de acordo com cada parte da máscara
foreach ($elementosMascara as $val) {
    $tam = strlen($val);
    $aux = substr($valor,$pos,$tam);
    if ($pos == 0) {
        $res = $aux;
    } else {
        $res = $res.".".$aux;
    }
    $pos += $tam;
}

$aMascara = Mascara::validaMascara($mascara,$res);

if ($aMascara[0]) {
    $res = $aMascara[1];
}

return $res;

// Fim da função geraMascara
}

/**************************************************************************/
//** Função que formata um valor informado para dentro de uma máscara
/**************************************************************************/
//Criado Por: Cassiano de Vasconcellos Ferreira  12/12/2003
//Ex.: geraMascara("99.999.999/9999-99","01345456789514")
//retorna 01.345.456/7895-14
function geraMascaraDinamica($mascara, $stValor)
{
    $arBlocos = preg_split("/[^0-9a-zA-Z]/", $mascara );
    $stSeparadores =  preg_replace("/[0-9a-zA-Z]/","" ,$mascara );
    $stMascara = "";
    $iPos = 0;
    foreach ($arBlocos as $indice => $valor) {
        if ( $indice + 1 < count($arBlocos) ) {
            $stMascara .= substr($stValor, $iPos, strlen($valor)).$stSeparadores[$indice];
            $iPos += strlen($valor);
        } else {
            $stMascara .= substr($stValor, $iPos, strlen($valor));
            //$stMascara .= substr($stValor, $iPos);
        }
    }

    return $stMascara;
}

/**************************************************************************/
//** Função que formata um valor informado para dentro de uma máscara de CPF
/**************************************************************************/
//Criado Por: Cassiano de Vasconcellos Ferreira  12/12/2003
//Ex.: geraMascaraCPF("01345456789514")
//retorna 01.345.456/7895-14
function geraMascaraCPF($stValor)
{
    return geraMascaraDinamica("999.999.999-99", $stValor);
}

/****************************************************************************/
//** Função que formata um valor informado para dentro de uma máscara de CNPJ
/****************************************************************************/
//Criado Por: Cassiano de Vasconcellos Ferreira  12/12/2003
//Ex.: geraMascaraCNPJ("99.999.999/9999-99","01345456789514")
//retorna 01.345.456/7895-14
function geraMascaraCNPJ($stValor)
{
    return geraMascaraDinamica("99-999-999/9999-99", $stValor);
}

/**************************************************************************/
//** Função que valida o código superior em uma hierarquia
/**************************************************************************/
//Criado Por: Leonardo Tremper Sex Mai  9 11:30:58 UTC 2003
//Ex.: retornaCodAnterior("1.2.4.5.6.9.0.0")
//retorna 1.2.4.5.6.0.0.0
function retornaCodAnterior($cod)
{
        $variaveis = explode(".",$cod);
        $i = 0;
        $total = sizeof($variaveis);
        $variaveis_rev = array_reverse($variaveis);
        $casaFinalSZero = "";

        while ($i < $total) {

            if (((int) $variaveis_rev[$i] > 0) and ($casaFinalSZero == ""))

                $casaFinalSZero = $i;

            $i++;
        }

        $qtZero = 0;

        while (list($key,$val) = each($variaveis_rev)) {
            if ($val == 0)
            $qtZero++;
        }

        //echo $qtZero;

        if ($qtZero == 0)
        $casaFinalSZero = $casaFinalSZero-1;

        $total_rev = sizeof($variaveis_rev);

        if($casaFinalSZero != ($total_rev-1))
            $variaveis_rev[$casaFinalSZero] = 0;

        $variaveis_final = array_reverse($variaveis_rev);

        $codFinal = "";

        while (list($key,$val) = each($variaveis_final)) {

            $codFinal .= $val.".";

        }

        $tot = strlen($codFinal);
        $tot = $tot-1;
        $codFinal = substr($codFinal, 0, $tot);

        return $codFinal;

}//Fim da função

/**************************************************************************/
//** Função que verifica se um código é inicial  em uma sequencia
/**************************************************************************/
//Criado Por: Leonardo Tremper Qua Mai  7 16:25:33 UTC 2003
//Ex.: verificaSeInicial(1.0.0.0){
//retorna true
function verificaSeInicial($cod)
{
        $variaveis = explode(".",$cod);
        $i = 0;
        $total = sizeof($variaveis);
        $casaCZero = 0;

        while ($i <= $total) {

            if ($variaveis[$i] != 0)

                $casaCZero++;

            $i++;
        }

        if ($casaCZero == 1)
        return true;
        else
        return false;

}
/**************************************************************************/
//** Função que verifica se existem zeros no meio do código
/**************************************************************************/
//Criado Por: Leonardo Tremper Qua Mai  7 16:25:33 UTC 2003
//Ex.: zerosNoMeio(1.0.1.0){
//retorna false
function zerosNoMeio($cod)
{
    $aR = explode(".",$cod);

    $bTemZero = false;
    $i = 0;
    $ch = 0;

    while ($i < count($aR)) {
        if ($aR[0] > 0) {
            if ($aR[$i-1] == 0 and $i > 0 and $aR[$i] > 0) {
                $bTemZero = true;
            }
        } else {
            $bTemZero = true;
        }
        if ($bTemZero) {
        $ch = 1;
        }
        $i++;
    }
    if ($ch == 0)
    return true;
    else
    return false;
    }
/**************************************************************************/
//** Função que verifica se existem zeros no meio do código com excessão
/**************************************************************************/
//Criado Por: Leonardo Tremper Qua Mai  7 16:25:33 UTC 2003
//Ex.: zerosNoMeioExcessao(1.1.0.1,3){
//retorna true
function zerosNoMeioExcessao($cod,$exc)
{
    $exc = (int) $exc + 1;
    $aR = explode(".",$cod);
    $bNTemZero = true;
    $i = 0;
    $ch = 0;
    if ($aR[0] > 0) {
        while ($i < count($aR)) {
            if(($aR[$i-1] == 0) and
               ($aR[$i+1] > 0)  and
               ($i > 0)         and
               ($aR[$i] == 0)   and
               ($i != $exc)){
                $bNTemZero = false;
            }
            $i++;
        }
    }

    return $bNTemZero;
}

/*************************************************************************/
/** Função que mascara os códigos de processo nas listagens
/*************************************************************************/
//Criado por: Alessandro La-Rocca Silveira 13/02/2004
//Ex: mascaraProcesso($codProcesso, $anoExercicio);
function mascaraProcesso($codProcesso, $anoExercicio)
{
global $mascaraProcesso;
    $codProcessoC    = $codProcesso.$anoExercicio;
    $numCasas        = strlen($mascaraProcesso) - 1;
    $iCodProcessoS   = str_pad($codProcessoC, $numCasas, "0" ,STR_PAD_LEFT);
    $iCodProcessoS   = geraMascaraDinamica($mascaraProcesso, $iCodProcessoS);

    return $iCodProcessoS;
}

}
?>
