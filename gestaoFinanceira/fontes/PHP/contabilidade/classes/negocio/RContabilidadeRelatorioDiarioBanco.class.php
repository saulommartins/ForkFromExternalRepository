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
  * Página de
  * Data de criação : 05/07/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Relatorio

    * $Id: RContabilidadeRelatorioDiarioBanco.class.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-02.02.24
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                       );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                              );
include_once( CAM_GF_CONT_MAPEAMENTO."FContabilidadeDiarioBanco.class.php"            );

class RContabilidadeRelatorioDiarioBanco extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFContabilidadeDiarioBanco;
/**
    * @var String
    * @access Private
*/
var $stFiltro;
/**
    * @var String
    * @access Private
*/
var $stDtInicial;
/**
    * @var String
    * @access Private
*/
var $stDtFinal;
/**
    * @var Integer
    * @access Private
*/
var $inExercicio;

/**
     * @access Public
     * @param Object $valor
*/
function setFContabilidadeDiarioBanco($valor) { $this->obFContabilidadeDiarioBanco  = $valor;}
/**
     * @access Public
     * @param String $valor
*/
function setFiltro($valor) { $this->stFiltro                              = $valor;}
/**
     * @access Public
     * @param String $valor
*/
function setDtInicial($valor) { $this->stDtInicial                           = $valor;}
/**
     * @access Public
     * @param String $valor
*/
function setDtFinal($valor) { $this->stDtFinal                             = $valor;}
/**
     * @access Public
     * @param Integer $valor
*/
function setExercicio($valor) { $this->inExercicio                           = $valor;}
/**
     * @access Public
     * @param String $valor
*/
function setNomeContaFinal($valor) { $this->stContaFinal                             = $valor;}
/**
     * @access Public
     * @param Integer $valor
*/
function setNomeContaInicial($valor) { $this->stContaInicial                           = $valor;}
/**
     * @access Public
     * @param String $valor
*/
function setCodContaFinal($valor) { $this->inCodContaFinal                             = $valor;}
/**
     * @access Public
     * @param Integer $valor
*/
function setCodContaInicial($valor) { $this->inCodContaInicial                           = $valor;}
/**
     * @access Public
     * @param Object $valor
*/
function getFContabilidadeDiarioBanco() { return $this->obFContabilidadeDiarioBanco; }
/**
     * @access Public
     * @param String $valor
*/
function getFiltro() { return $this->stFiltro                    ; }
/**
     * @access Public
     * @param String $valor
*/
function getDtInicial() { return $this->stDtInicial                 ; }
/**
     * @access Public
     * @param String $valor
*/
function getDtFinal() { return $this->stDtFinal                   ; }
/**
     * @access Public
     * @param String $valor
*/
function getExercicio() { return $this->inExercicio                 ; }
/**
     * @access Public
     * @param String $valor
*/
function getNomeContaFinal() { return $this->stContaFinal                   ; }
/**
     * @access Public
     * @param String $valor
*/
function getNomeContaInicial() { return $this->stContaInicial                 ; }
/**
     * @access Public
     * @param String $valor
*/
function getCodContaFinal() { return $this->inCodContaFinal                   ; }
/**
     * @access Public
     * @param String $valor
*/
function getCodContaInicial() { return $this->inCodContaInicial                 ; }

/**
    * Método Construtor
    * @access Private
*/
function RContabilidadeRelatorioDiarioBanco()
{
    $this->obFContabilidadeDiarioBanco = new FContabilidadeDiarioBanco;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet, $stOrder = "")
{
   $arRecordSet = array();

   $this->obFContabilidadeDiarioBanco->setDado ("exercicio"     , $this->inExercicio);
   $this->obFContabilidadeDiarioBanco->setDado ("stFiltro"      , $this->stFiltro);
   $this->obFContabilidadeDiarioBanco->setDado ("stDtInicial"   , $this->stDtInicial);
   $this->obFContabilidadeDiarioBanco->setDado ("stDtFinal"     , $this->stDtFinal);
   $this->obFContabilidadeDiarioBanco->setDado ("stContaInicial", $this->stContaInicial);
   $this->obFContabilidadeDiarioBanco->setDado ("stContaFinal"  , $this->stContaFinal);
   $this->obFContabilidadeDiarioBanco->setDado ("inCodContaInicial" , $this->inCodContaInicial);
   $this->obFContabilidadeDiarioBanco->setDado ("inCodContaFinal" , $this->inCodContaFinal);

   $obErro = $this->obFContabilidadeDiarioBanco->recuperaTodos( $rsRecordSet, "", "" );
   $inCount = 0;
//----------------------------------------------------------------------------------------------------------------------//
   while ( !$rsRecordSet->eof() ) {
     if (str_replace(".","",substr( $rsRecordSet->getCampo("cod_estrutural"),0,9)) >= 11111) {
        $arRecordSet[$inCount]  ["cod_plano"]           = $rsRecordSet->getCampo("cod_plano");
        $arRecordSet[$inCount]  ["cod_estrutural"]      = $rsRecordSet->getCampo("cod_estrutural");
        $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = $rsRecordSet->getCampo("vl_saldo_anterior");
        $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = $rsRecordSet->getCampo("vl_saldo_debitos");
        $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = $rsRecordSet->getCampo("vl_saldo_creditos");
        $arRecordSet[$inCount]["vl_saldo_atual"]      = $rsRecordSet->getCampo("vl_saldo_atual");

        $stConta = str_replace( chr(10) , "", $rsRecordSet->getCampo("nom_conta") );
        $stConta = wordwrap( $stConta , 35, chr(13) );
        $arConta = explode( chr(13), $stConta );
        foreach ($arConta as $stConta) {
            $arRecordSet[$inCount]["nom_conta"]   = $stConta;
            $inCount++;
        }

        $nuSaldoAnterior =  bcadd($nuSaldoAnterior,$rsRecordSet->getCampo("vl_saldo_anterior"),4);
        $nuSaldoDebitos  =  bcadd($nuSaldoDebitos,$rsRecordSet->getCampo("vl_saldo_debitos"),4);
        $nuSaldoCreditos =  bcadd($nuSaldoCreditos,$rsRecordSet->getCampo("vl_saldo_creditos"),4);
        $nuSaldoAtual    =  bcadd($nuSaldoAtual,$rsRecordSet->getCampo("vl_saldo_atual"),4);

        if (str_replace(".","",substr( $rsRecordSet->getCampo("cod_estrutural"),0,24)) <> 111110000000000 ||
            str_replace(".","",substr( $rsRecordSet->getCampo("cod_estrutural"),0,24)) <> 111120000000000 ||
            str_replace(".","",substr( $rsRecordSet->getCampo("cod_estrutural"),0,24)) <> 111130000000000  ) {

            $totalSaldoAnterior = bcadd($totalSaldoAnterior,$rsRecordSet->getCampo("vl_saldo_anterior"),4);
            $totalSaldoDebitos  = bcadd($totalSaldoDebitos,$rsRecordSet->getCampo("vl_saldo_debitos"),4);
            $totalSaldoCreditos = bcadd($totalSaldoCreditos,$rsRecordSet->getCampo("vl_saldo_creditos"),4);
            $totalSaldoAtual    = bcadd($totalSaldoAtual,$rsRecordSet->getCampo("vl_saldo_atual"),4);
        }

        if (str_replace(".","",substr( $rsRecordSet->getCampo("cod_estrutural"),0,24)) == 111110000000000 ||
        str_replace(".","",substr( $rsRecordSet->getCampo("cod_estrutural"),0,24)) == 111120000000000 ||
        str_replace(".","",substr( $rsRecordSet->getCampo("cod_estrutural"),0,24)) == 111130000000000  ) {

            $totalSaldoAnterior = $rsRecordSet->getCampo("vl_saldo_anterior");
            $totalSaldoDebitos  = $rsRecordSet->getCampo("vl_saldo_debitos");
            $totalSaldoCreditos = $rsRecordSet->getCampo("vl_saldo_creditos");
            $totalSaldoAtual    = $rsRecordSet->getCampo("vl_saldo_atual");

            $inCount--;
            $arRecordSet[$inCount]  ["cod_plano"]           = $rsRecordSet->getCampo("cod_plano");
            $arRecordSet[$inCount]  ["nom_conta"]           = "";
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = "";
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = "";
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = "";
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = "";

            $stConta = str_replace( chr(10) , "", $rsRecordSet->getCampo("nom_conta") );
            $stConta = wordwrap( $stConta , 35, chr(13) );
            $arConta = explode( chr(13), $stConta );
            foreach ($arConta as $stConta) {
                $arRecordSet[$inCount]["cod_estrutural"]   = $stConta;
                $inCount++;
            }
            $arRecordSet[$inCount]  ["nom_conta"]           = "";
            $inCount++;
        }
        $inCodEstruturalOld = str_replace(".","",substr( $rsRecordSet->getCampo("cod_estrutural"),0,9));
        $rsRecordSet->proximo();
        $inCodEstrutural = str_replace(".","",substr( $rsRecordSet->getCampo("cod_estrutural"),0,9));
        if ($inCodEstrutural <> $inCodEstruturalOld) {
            $arRecordSet[$inCount]  ["nom_conta"] = "";
            $inCount++;
            $arRecordSet[$inCount]  ["nom_conta"] = "T O T A L .......";
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = $totalSaldoAnterior;
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = $totalSaldoDebitos;
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = $totalSaldoCreditos;
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = $totalSaldoAtual;
            $inCount++;
            $totalSaldoAnterior = 0;
            $totalSaldoDebitos  = 0;
            $totalSaldoCreditos = 0;
            $totalSaldoAtual    = 0;
        }
     } else {
        $rsRecordSet->proximo();
     }
   }

  $arRecordSet[$inCount]["nom_conta"] = "T O T A L  B A N C O S .....";
  $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = $bancoSaldoAnterior;
  $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = $bancoSaldoDebitos;
  $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = $bancoSaldoCreditos;
  $arRecordSet[$inCount]  ["vl_saldo_atual"]      = $bancoSaldoAtual;

  $inCount++;

//---------------------------------------------------------------------------------------------------------------------//
    if ( is_array($arRecordSet) ) {
        //Espaço
        $arRecordSet[$inCount]  ["cod_plano"]      = '';
        $arRecordSet[$inCount]  ["cod_estrutural"]      = '';
        $arRecordSet[$inCount]  ["nom_conta"]           = '';
        $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
        $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
        $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
        $arRecordSet[++$inCount]["vl_saldo_atual"]      = '';

        //Total Geral
        $arRecordSet[$inCount]  ["cod_plano"]           = '';
        $arRecordSet[$inCount]  ["cod_estrutural"]      = '';
        $arRecordSet[$inCount]  ["nom_conta"]           = 'T O T A L  G E R A L .......';
        $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = $nuSaldoAnterior;
        $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = $nuSaldoDebitos;
        $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = $nuSaldoCreditos;
        $arRecordSet[$inCount]  ["vl_saldo_atual"]      = $nuSaldoAtual;
    }
    $rsRecordSetNovo = new RecordSet;
    $rsRecordSetNovo->preenche( $arRecordSet );
    $rsRecordSet = $rsRecordSetNovo;

    $rsRecordSet->addFormatacao("vl_saldo_anterior","CONTABIL");
    $rsRecordSet->addFormatacao("vl_saldo_debitos", "CONTABIL");
    $rsRecordSet->addFormatacao("vl_saldo_creditos","CONTABIL");
    $rsRecordSet->addFormatacao("vl_saldo_atual",   "CONTABIL");

    return $obErro;
}
}
