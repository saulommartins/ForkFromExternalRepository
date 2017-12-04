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
    * Classe de Regra de Negócio para geração de relatótio
    * Data de Criação   : 27/09/2004

    * @author Desenvolvedor: Cassiano Ferreira

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO     );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioDotacaoFuncionalProgramaticaRecurso.class.php" );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioDotacaoFuncionalProgramaticaRecursoBalanco.class.php");
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                                              );

/**
    * Classe de Regra de Negócio para geração de relatótio
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
*/
class ROrcamentoRelatorioAnexo8 extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFOrcamento;
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
function setFOrcamento($valor) { $this->obFOrcamento                  = $valor; }
function setFiltro($valor) { $this->stFiltro                      = $valor; }
function setExercicio($valor) { $this->inExercicio                   = $valor; }
function setDataInicial($valor) { $this->stDataInicial                 = $valor; }
function setDataFinal($valor) { $this->stDataFinal                   = $valor; }
function setSituacao($valor) { $this->stSituacao                    = $valor; }
function setEntidades($valor) { $this->stEntidades                   = $valor; }
function setTipoRelatorio($valor) { $this->stTipoRelatorio               = $valor; }
function setREntidade($valor) { $this->obREntidade                   = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getFOrcamento() { return $this->obFOrcamento                ; }
function getFiltro() { return $this->stFiltro                    ; }
function getExercicio() { return $this->inExercicio                 ; }
function getDataInicial() { return $this->stDataInicial               ; }
function getDataFinal() { return $this->stDataFinal                 ; }
function getSituacao() { return $this->stSituacao                  ; }
function getTipoRelatorio() { return $this->stTipoRelatorio             ; }
function getEntidades() { return $this->stEntidades                 ; }
function getREntidade() { return $this->obREntidade                 ; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioAnexo8()
{
    $this->obREntidade                  = new ROrcamentoEntidade;
    $this->obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm')         );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , &$rsTotal, &$arEntidade, $stOrder = "")
{
    if ($this->stTipoRelatorio=="orcamento") {
        $this->obFOrcamento = new FOrcamentoSomatorioDotacaoFuncionalProgramaticaRecurso;
    } else {
        $this->obFOrcamento = new FOrcamentoSomatorioDotacaoFuncionalProgramaticaRecursoBalanco;
    }

    if ($this->stTipoRelatorio=="orcamento") {
        $this->obFOrcamento->setDado ("exercicio", $this->inExercicio       );
        $this->obFOrcamento->setDado ("stFiltro", $this->stFiltro           );
    } else {
        $this->obFOrcamento->setDado ("exercicio", $this->inExercicio       );
        $this->obFOrcamento->setDado ("stFiltro", $this->stFiltro           );
        $this->obFOrcamento->setDado ("dataInicial", $this->stDataInicial   );
        $this->obFOrcamento->setDado ("dataFinal", $this->stDataFinal       );
        $this->obFOrcamento->setDado ("stEntidades", $this->stEntidades     );
        $this->obFOrcamento->setDado ("stSituacao", $this->stSituacao       );
    }
    $stFiltro = "";

    $obErro = $this->obFOrcamento->recuperaTodos( $rsRecordSet, $stFiltro, "" );

    $inCountOrdinario = 0;
    $inCountVinculado = 0;
    $inSomaTotal      = 0;

    $arOrcamento = array();
    while ( !$rsRecordSet->eof() ) {
        if ( $rsRecordSet->getCampo("nivel") == 1 ) {
            $inCountOrdinario += $rsRecordSet->getCampo("vl_ordinario");
            $inCountVinculado += $rsRecordSet->getCampo("vl_vinculado");
            $inSomaTotal      += $rsRecordSet->getCampo("vl_total");
        }
        $rsRecordSet->proximo();
    }
    $rsRecordSet->setPrimeiroElemento();
    $rsRecordSet->addFormatacao( "vl_ordinario", "NUMERIC_BR_NULL" );
    $rsRecordSet->addFormatacao( "vl_vinculado", "NUMERIC_BR_NULL" );
    $rsRecordSet->addFormatacao( "vl_total"    , "NUMERIC_BR_NULL" );

    $arTotal = array( array( "titulo" => "Total",
                             "vl_ordinario" => $inCountOrdinario,
                             "vl_vinculado" => $inCountVinculado,
                             "vl_total" => $inSomaTotal) );
    $rsTotal = new RecordSet;
    $rsTotal->addFormatacao( "vl_ordinario", "NUMERIC_BR_NULL" );
    $rsTotal->addFormatacao( "vl_vinculado", "NUMERIC_BR_NULL" );
    $rsTotal->addFormatacao( "vl_total"    , "NUMERIC_BR_NULL" );
    $rsTotal->preenche( $arTotal );

    $arEntidade  = array();
    $inIndice=1;
    $arTmpEntidade[$inIndice]["descricao"]     = "";
    $inIndice++;
    $arTmpEntidade[$inIndice]["descricao"]     = "ENTIDADES RELACIONADAS";

    $stEntidade = substr(trim($this->stFiltro),strpos($this->stFiltro,"("));
    $stEntidade = substr($stEntidade,0,strpos($stEntidade, ")"));

    $inEntidades = str_replace("'","",$stEntidade);
    $arEntidades = explode(",",$inEntidades );

    foreach ($arEntidades as $key => $inCodEntidade) {
        $inIndice++;
        $this->obREntidade->setCodigoEntidade( $inCodEntidade );
        $this->obREntidade->consultarNomes($rsLista);
        $arTmpEntidade[$inIndice]["descricao"]     = "- ".$rsLista->getCampo("entidade");
    }

    $inIndice++;
    $arTmpEntidade[$inIndice]["descricao"]     = "";

    $obTmpEntidade = new RecordSet();
    $obTmpEntidade->preenche( $arTmpEntidade );
    $arEntidade[] = $obTmpEntidade;

    return $obErro;
}
}
