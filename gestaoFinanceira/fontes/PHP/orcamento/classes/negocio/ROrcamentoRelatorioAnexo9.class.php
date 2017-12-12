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
    * Classe de regra para Anexo9
    * Data de Criação   : 28/09/2004

    * @author Desenvolvedor: Gustavo Tourinho

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.16
*/

/*
$Log$
Revision 1.11  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                   );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioDetalhamentoOrgaoFuncao.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                                        );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"              );
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoDespesa.class.php"              );

class ROrcamentoRelatorioAnexo9 extends PersistenteRelatorio
{
var $obFOrcamentoSomatorioDetalhamentoOrgaoFuncao;
var $stFiltro;
var $inExercicio;
var $stDataInicial;
var $stDataFinal;
var $stSituacao;
var $stEntidades;
var $stTipoRelatorio;
var $obREntidade;

/**
     * @access Public
     * @param Object $valor
*/
function setFiltro($valor) { $this->stFiltro                      = $valor; }
function setExercicio($valor) { $this->inExercicio                   = $valor; }
function setDataInicial($valor) { $this->stDataInicial                 = $valor; }
function setDataFinal($valor) { $this->stDataFinal                   = $valor; }
function setSituacao($valor) { $this->stSituacao                    = $valor; }
function setTipoRelatorio($valor) { $this->stTipoRelatorio               = $valor; }
function setEntidades($valor) { $this->stEntidades                   = $valor; }
function setREntidade($valor) { $this->obREntidade                   = $valor; }
function setFOrcamentoSomatorioDetalhamentoOrgaoFuncao($valor) { $this->obFOrcamentoSomatorioDetalhamentoOrgaoFuncao  = $valor; }

function getFiltro() { return $this->stFiltro                    ; }
function getExercicio() { return $this->inExercicio                 ; }
function getDataInicial() { return $this->stDataInicial               ; }
function getDataFinal() { return $this->stDataFinal                 ; }
function getSituacao() { return $this->stSituacao                  ; }
function getTipoRelatorio() { return $this->stTipoRelatorio             ; }
function getEntidades() { return $this->stEntidades                 ; }
function getREntidade() { return $this->obREntidade                 ; }
function getFOrcamentoSomatorioDetalhamentoOrgaoFuncao() { return $this->obFOrcamentoSomatorioDetalhamentoOrgaoFuncao; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioAnexo9()
{
    $this->setFOrcamentoSomatorioDetalhamentoOrgaoFuncao ( new FOrcamentoSomatorioDetalhamentoOrgaoFuncao );
    $this->obREntidade                  = new ROrcamentoEntidade;
    $this->obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm')         );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet, $stOrder = "")
{
    // DEFINE NUMERO MAXIMO DE COLUNAS QUE UM BLOCO TERÁ
    $inMaxColunas = 4;
    // ===============================================

    $obTOrcamentoDespesa = new TOrcamentoDespesa;
    $obTOrcamentoDespesa->setDado ( "stFiltro" , $this->stFiltro    );
    $obTOrcamentoDespesa->setDado ( "exercicio", $this->inExercicio );

    // Busca todos nomes de funcoes
    $obErro = $obTOrcamentoDespesa->buscaNomesFuncao ( $rsDespesaNomesFuncao );
    //$obTOrcamentoDespesa->Debug ();
    $inNumFuncoes = $rsDespesaNomesFuncao->getNumLinhas ();

    $stFuncoes = "";
    $inCount = 0;
    while ( !$rsDespesaNomesFuncao->eof() ) {
        $stFuncoes .= " ".$rsDespesaNomesFuncao->getCampo("nom_funcao")." numeric(14,2), ";
        $arCabecalhoFuncoes[$inCount]["descricao"]  = $rsDespesaNomesFuncao->getCampo("descricao");
        $arCabecalhoFuncoes[$inCount]["nom_funcao"] = $rsDespesaNomesFuncao->getCampo("nom_funcao");
        $inCount++;
        $rsDespesaNomesFuncao->proximo();
    }

    if ( !$obErro->ocorreu () ) {
        $this->obFOrcamentoSomatorioDetalhamentoOrgaoFuncao->setDado ("exercicio"       , $this->inExercicio);
        $this->obFOrcamentoSomatorioDetalhamentoOrgaoFuncao->setDado ("stFiltro"        , $this->stFiltro);
        $this->obFOrcamentoSomatorioDetalhamentoOrgaoFuncao->setDado ("stSituacao"      , $this->stSituacao);
        $this->obFOrcamentoSomatorioDetalhamentoOrgaoFuncao->setDado ("stEntidades"     , $this->stEntidades);
        $this->obFOrcamentoSomatorioDetalhamentoOrgaoFuncao->setDado ("stTipoRelatorio" , $this->stTipoRelatorio);
        $this->obFOrcamentoSomatorioDetalhamentoOrgaoFuncao->setDado ("stDataInicial"   , $this->stDataInicial);
        $this->obFOrcamentoSomatorioDetalhamentoOrgaoFuncao->setDado ("stDataFinal"     , $this->stDataFinal);
        $this->obFOrcamentoSomatorioDetalhamentoOrgaoFuncao->setDado ("funcoes"         , $stFuncoes);

        $obErro = $this->obFOrcamentoSomatorioDetalhamentoOrgaoFuncao->recuperaTodos( $rsRecordSet, "", "" );
        //$this->obFOrcamentoSomatorioDetalhamentoOrgaoFuncao->Debug();
    }

    // Gera nome dos campos para indice do array
    $arCampos = array ();
    $arCampos[0] = "codigo";
    $arCampos[1] = "descricao";

    $arCampoRecordSet = array ();
    $arCampoRecordSet[0] = "num_orgao";
    $arCampoRecordSet[1] = "nom_orgao";
    for ($inCount = 2, $inCount2 = 0; $inCount2 < $inNumFuncoes; $inCount++, $inCount2++) {
        $arCampos[$inCount] = $arCabecalhoFuncoes[$inCount2]["descricao"];
        $arCampoRecordSet[$inCount] = $arCabecalhoFuncoes[$inCount2]["nom_funcao"];
    }
    $arCampos[$inCount] = "TOTAL";
    $arCampoRecordSet[$inCount] = "vl_total";

    // Monta relatorio completo conforme impressao
    $inCount = 0;
    while ( !$rsRecordSet->eof() ) {
        for ( $inCountCampos = 0; $inCountCampos < count ($arCampos); $inCountCampos++ ) {
            $arRelatorio[$inCount][$arCampos[$inCountCampos]]  = $rsRecordSet->getCampo($arCampoRecordSet[$inCountCampos]);
        }
        $arRelatorio[$inCount]["TOTAL"]  = $rsRecordSet->getCampo("vl_total");

        $inCount++;
        $rsRecordSet->proximo();
    }

    // Monta blocos (arrays) de $inMaxColuna colunas cada !
    $arTotais = array ();

    $inNumeroBloco = 0;
    $arBloco = array ();  // cria o array que vai conter o bloco com as colunas
    $inTotalBlocos = (int) (($inNumFuncoes/$inMaxColunas) + 1);
    for ($inCountColunas = 0, $inCampoCount = 2; $inCountColunas < $inTotalBlocos; $inCountColunas++, $inNumeroBloco++, $inCampoCount = $inCampo) {
        for ( $inLinhasBloco = 0, $rsRecordSet->setPrimeiroElemento(); $inLinhasBloco < $rsRecordSet->getNumLinhas(); $inLinhasBloco++, $rsRecordSet->proximo() ) {
            $arBloco[$inNumeroBloco][$inLinhasBloco]["codigo"]    = str_pad( $rsRecordSet->getCampo("num_orgao"),2, "0", STR_PAD_LEFT);
            $arBloco[$inNumeroBloco][$inLinhasBloco]["descricao"] = $rsRecordSet->getCampo("nom_orgao");
            $arTotais[0]["descricao"] = "T O T A L ........";
            for ($inColunasBloco = 0, $inCampo = $inCampoCount; $inColunasBloco < $inMaxColunas ; $inColunasBloco++, $inCampo++) {
                $arBloco[$inNumeroBloco][$inLinhasBloco][$arCampos[$inCampo]] = $arRelatorio[$inLinhasBloco][$arCampos[$inCampo]];
                $flTotal = $arTotais[0][$arCampos[$inCampo]] + $arRelatorio[$inLinhasBloco][$arCampos[$inCampo]];
                if ($arCampos[$inCampo]) {
                    $arTotais[0][$arCampos[$inCampo]] = $flTotal;
                }
            }
        }
    }

    foreach ($arTotais[0] as $key => $value) {
        if ($value == 0) {
            $arTotais[0][$key] = "0.00";
        }
    }

    $arRetorno = array ($arCampos,
                        $arBloco,
                        $arTotais);

    $rsRecordSetNovo = new RecordSet;
    $rsRecordSetNovo->preenche( $arRetorno );
    $rsRecordSet = $rsRecordSetNovo;

    return $obErro;
}
}
