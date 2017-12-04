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
    * Classe de regra para Anexo2CategoriaEconomica
    * Data de Criação   : 29/09/2004

    * @author Desenvolvedor: Anderson Buzo
    * @author Desenvolvedor: Diego Victoria
    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Gustavo Tourinho

    * @ignore

    $Revision: 30824 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.11
*/

/*
$Log$
Revision 1.3  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO       );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco.class.php" );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioDespesaBalanco.class.php"          );
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoDespesa.class.php"                                        );

class ROrcamentoRelatorioAnexo2CategoriaEconomicaBalanco
{
/**
    * @var Object
    * @access Private
*/
var $obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco;
var $obFOrcamentoSomatorioDespesaBalanco;
var $stFiltro;
var $inOrgao;
var $inUnidade;
var $inExercicio;
var $stSituacao;
var $stEntidades;
var $stDataInicial;
var $stDataFinal;

/**
     * @access Public
     * @param Object $valor
*/
function setFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco($valor) { $this->obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco  = $valor; }
function setFOrcamentoSomatorioDespesaBalanco($valor) { $this->obFOrcamentoSomatorioDespesaBalanco  = $valor; }
function setFiltro($valor) { $this->stFiltro                             = $valor; }
function setOrgao($valor) { $this->inOrgao                              = $valor; }
function setUnidade($valor) { $this->inUnidade                            = $valor; }
function setExercicio($valor) { $this->inExercicio                          = $valor; }
function setSituacao($valor) { $this->stSituacao                           = $valor; }
function setEntidades($valor) { $this->stEntidades                          = $valor; }
function setDataInicial($valor) { $this->stDataInicial                        = $valor; }
function setDataFinal($valor) { $this->stDataFinal                          = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco() { return $this->obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco; }
function getFiltro() { return $this->stFiltro                    ; }
function getOrgao() { return $this->inOrgao                     ; }
function getUnidade() { return $this->inUnidade                   ; }
function getExercicio() { return $this->inExercicio                 ; }
function getSituacao() { return $this->stSituacao                  ; }
function getEntidades() { return $this->stEntidades                 ; }
function getDataInicial() { return $this->stDataInicial               ; }
function getDataFinal() { return $this->stDataFinal                 ; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioAnexo2CategoriaEconomicaBalanco()
{
    $this->setFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco ( new FOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco );
    $this->setFOrcamentoSomatorioDespesaBalanco                          ( new FOrcamentoSomatorioDespesaBalanco                          );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet, $inCategoriaEconomica)
{
    if ($inCategoriaEconomica == 9) {
        $this->obFOrcamentoSomatorioDespesaBalanco->setDado ("exercicio"     , $this->inExercicio);
        $this->obFOrcamentoSomatorioDespesaBalanco->setDado ("stFiltro"      , $this->stFiltro   );
        $this->obFOrcamentoSomatorioDespesaBalanco->setDado ("stDataInicial" , $this->stDataInicial );
        $this->obFOrcamentoSomatorioDespesaBalanco->setDado ("stDataFinal"   , $this->stDataFinal );
        $this->obFOrcamentoSomatorioDespesaBalanco->setDado ("stEntidades"   , $this->getEntidades());
        $this->obFOrcamentoSomatorioDespesaBalanco->setDado ("stSituacao"    , $this->stSituacao );
        $stFiltro  = " WHERE valor <> 0 ";
        $stFiltro .= " AND substr(classificacao,1,1)= '9' and nivel = fnorcamentoanexo2despesaconta() ";

        return $obErro = $this->obFOrcamentoSomatorioDespesaBalanco->recuperaTodos( $rsRecordSet, $stFiltro, "" );
    }
    // DEFINE NUMERO MAXIMO DE COLUNAS QUE UM BLOCO TERÁ
    $inMaxColunas = 4;
    // ===============================================

    $obTOrcamentoDespesa = new TOrcamentoDespesa;
    $obTOrcamentoDespesa->setDado ( "stFiltro"            , $this->stFiltro    );
    $obTOrcamentoDespesa->setDado ( "categoria_economica" , $inCategoriaEconomica );
    $obTOrcamentoDespesa->setDado ( "exercicio"           , $this->inExercicio );

    // Busca todos nomes de funcoes
    $obErro = $obTOrcamentoDespesa->buscaGrupos ( $rsDespesaNomesGrupos );

    $inNumGrupos = $rsDespesaNomesGrupos->getNumLinhas ();

    $stFuncoes = "";
    $inCount = 0;
    while ( !$rsDespesaNomesGrupos->eof() ) {
        $stFuncoes .= " g_".$rsDespesaNomesGrupos->getCampo("cod_grupo")." numeric(14,2), ";
        $arCabecalhoGrupos[$inCount]["descricao"]  = $rsDespesaNomesGrupos->getCampo("descricao");
        $arCabecalhoGrupos[$inCount]["nom_grupo"] = 'g_'.$rsDespesaNomesGrupos->getCampo("cod_grupo");
        $inCount++;
        $rsDespesaNomesGrupos->proximo();
    }

    if ( !$obErro->ocorreu () ) {
        $this->obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco->setDado ("categoria_economica", $inCategoriaEconomica );
        $this->obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco->setDado ("exercicio"           , $this->inExercicio);
        $this->obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco->setDado ("stFiltro"            , $this->stFiltro);
        $this->obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco->setDado ("grupos"              , $stFuncoes);
        $this->obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco->setDado ("stDataInicial" , $this->getDataInicial());
        $this->obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco->setDado ("stDataFinal"   , $this->getDataFinal());
        $this->obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco->setDado ("stEntidades"   , $this->getEntidades());
        $this->obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco->setDado ("stSituacao"    , $this->getSituacao());
        $this->obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco->setDado ("inOrgao"       , $this->getOrgao());
        $this->obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco->setDado ("inUnidade"     , $this->getUnidade());

        $obErro = $this->obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco->recuperaTodos( $rsRecordSet, "", "" );
        //$this->obFOrcamentoSomatorioDespesaUnidadeCategoriaEconomicaBalanco->debug();
    }

    // Gera nome dos campos para indice do array
    $arCampos = array ();
    $arCampos[0] = "descricao";

    $arCampoRecordSet = array ();
    $arCampoRecordSet[0] = "nom_unidade";
    for ($inCount = 1, $inCount2 = 0; $inCount2 < $inNumGrupos; $inCount++, $inCount2++) {
        if ($arCabecalhoGrupos[$inCount2]["descricao"] == $stDescAnt) {
            $inDesc++;
            $arCabecalhoGrupos[$inCount2]["descricao"] = $arCabecalhoGrupos[$inCount2]["descricao"].str_pad(' ',$inDesc);
        }
        $arCampos[$inCount] = $arCabecalhoGrupos[$inCount2]["descricao"];
        $arCampoRecordSet[$inCount] = $arCabecalhoGrupos[$inCount2]["nom_grupo"];
        $stDescAnt = $arCabecalhoGrupos[$inCount2]["descricao"];
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

    $arTotais = array ();

    $inNumeroBloco = 0;
    $arBloco = array ();  // cria o array que vai conter o bloco com as colunas
    $inTotalBlocos = (int) (($inNumGrupos/$inMaxColunas) + 1);
    for ($inCountColunas = 0, $inCampoCount = 1; $inCountColunas < $inTotalBlocos; $inCountColunas++, $inNumeroBloco++, $inCampoCount = $inCampo) {
        for ( $inLinhasBloco = 0, $rsRecordSet->setPrimeiroElemento(); $inLinhasBloco < $rsRecordSet->getNumLinhas(); $inLinhasBloco++, $rsRecordSet->proximo() ) {
            $arBloco[$inNumeroBloco][$inLinhasBloco]["codigo"]    = $rsRecordSet->getCampo("num_orgao");
            $arBloco[$inNumeroBloco][$inLinhasBloco]["descricao"] = $rsRecordSet->getCampo("nom_unidade");
            $arTotais[0]["descricao"] = "T O T A L ........";
            for ($inColunasBloco = 0, $inCampo = $inCampoCount; $inColunasBloco < $inMaxColunas ; $inColunasBloco++, $inCampo++) {
                $arBloco[$inNumeroBloco][$inLinhasBloco][$arCampos[$inCampo]] = $arRelatorio[$inLinhasBloco][$arCampos[$inCampo]];
                $arTotais[0][$arCampos[$inCampo]] = $arTotais[0][$arCampos[$inCampo]] + $arRelatorio[$inLinhasBloco][$arCampos[$inCampo]];
                $arTotais[0][$arCampos[$inCampo]] = number_format($arTotais[0][$arCampos[$inCampo]], 2,',','');
            }
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
