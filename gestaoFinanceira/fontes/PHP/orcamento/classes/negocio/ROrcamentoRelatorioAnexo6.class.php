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
    * Classe de regra para Anexo 6
    * Data de Criação   : 27/09/2004

    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.13
*/

/*
$Log$
Revision 1.10  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                                                                 );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioDotacaoPao.class.php"                           );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioDotacaoPaoBalanco.class.php"                    );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"                              );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoOrgaoOrcamentario.class.php"                                );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                                         );

class ROrcamentoRelatorioAnexo6 extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFOrcamentoSomatorioDotacaoPao;
var $stFiltro;
var $inExercicio;
var $stDataInicial;
var $stDataFinal;
var $stSituacao;
var $stTipoRelatorio;
var $inOrgao;
var $inUnidade;
var $obREntidade;
var $stEntidades;
/**

/**
     * @access Public
     * @param Object $valor
*/
function setFOrcamentoSomatorioDotacaoPao($valor) { $this->obFOrcamentoSomatorioDotacaoPao = $valor; }
function setFiltro($valor) { $this->stFiltro                        = $valor; }
function setExercicio($valor) { $this->inExercicio                     = $valor; }
function setDataInicial($valor) { $this->stDataInicial                   = $valor; }
function setDataFinal($valor) { $this->stDataFinal                     = $valor; }
function setSituacao($valor) { $this->stSituacao                      = $valor; }
function setTipoRelatorio($valor) { $this->stTipoRelatorio                 = $valor; }
function setOrgao($valor) { $this->inOrgao                         = $valor; }
function setUnidade($valor) { $this->inUnidade                       = $valor; }
function setREntidade($valor) { $this->obREntidade                     = $valor; }
function setEntidades($valor) { $this->stEntidades                     = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getFOrcamentoSomatorioDotacaoPao() { return $this->obFOrcamentoSomatorioDotacaoPao; }
function getFiltro() { return $this->stFiltro                       ; }
function getExercicio() { return $this->inExercicio                    ; }
function getDataInicial() { return $this->stDataInicial                  ; }
function getDataFinal() { return $this->stDataFinal                    ; }
function getSituacao() { return $this->stSituacao                     ; }
function getTipoRelatorio() { return $this->stTipoRelatorio                ; }
function getOrgao() { return $this->inOrgao                        ; }
function getUnidade() { return $this->inUnidade                      ; }
function getREntidade() { return $this->obREntidade                    ; }
function getEntidades() { return $this->stEntidades                    ; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioAnexo6()
{
    $this->obREntidade                  = new ROrcamentoEntidade;
    $this->obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm')         );
}

function geraRecordSet(&$arRecordSet, &$arCabecalho, &$rsTotal , &$arEntidade, $stOrder = "")
{
    $arFiltro = Sessao::read('filtroRelatorio');
    if ($arFiltro["stAgrupamento"] == "orgao_unidade") {
        $orErro = $this->geraRecordSetOrgaoUnidade( $arRecordSet, $arCabecalho, $rsTotal , $arEntidade, $stOrder );
    } elseif ($arFiltro["stAgrupamento"] == "orgao") {
        $orErro = $this->geraRecordSetOrgao( $arRecordSet, $arCabecalho, $rsTotal , $arEntidade, $stOrder );
    }

    return $obErro;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSetOrgaoUnidade(&$arRecordSet, &$arCabecalho, &$rsTotal , &$arEntidade, $stOrder = "")
{
    if ($this->stTipoRelatorio=="orcamento") {
        $this->obFOrcamentoSomatorioDotacaoPao =new FOrcamentoSomatorioDotacaoPao;;
    } else {
        $this->obFOrcamentoSomatorioDotacaoPao = new FOrcamentoSomatorioDotacaoPaoBalanco;
    }
    if ($this->stTipoRelatorio=="orcamento") {
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("exercicio", $this->inExercicio       );
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("stFiltro", $this->stFiltro           );
    } else {
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("exercicio", $this->inExercicio       );
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("stFiltro", $this->stFiltro           );
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("dataInicial", $this->stDataInicial   );
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("dataFinal", $this->stDataFinal       );
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("stEntidades", $this->stEntidades     );
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("stSituacao", $this->stSituacao       );
    }

    $obErro = $this->obFOrcamentoSomatorioDotacaoPao->recuperaTodos( $rsRecordSet, $stFiltro, "" );
 //           $this->obFOrcamentoSomatorioDotacaoPao->debug();
    $arLista = array();
    $arCabecalho = array();
    $arEntidade  = array();
    $stOrgaoInicial   = "";
    $stUnidadeInicial = "";
    $arRecordSet = array();

    $inIndice=1;
    $arTmpEntidade[$inIndice]["descricao"]     = "";
    $inIndice++;
    $arTmpEntidade[$inIndice]["descricao"]     = "ENTIDADES RELACIONADAS";

    $stEntidade = substr(trim($this->stFiltro),strpos($this->stFiltro,"("));
    $stEntidade = substr(trim($stEntidade),0,strpos($stEntidade,")"));

    $inEntidades = str_replace("(","",$stEntidade);
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

    //CRIA UM NOVO RECORDSET A CADA UNIDADE
    $boFlagLoop = true;
    while ($boFlagLoop) {
        if ( $rsRecordSet->eof() ) {
            $boFlagLoop = false;
        }
        $arConta = array();
        $stDotacao = $rsRecordSet->getCampo('dotacao');
        $arDotacao = preg_split( "/\./", $stDotacao, 3 );
        //SETA O PRIMEIRO VALOR DAS VARIAVEIS $stOrgaoInicial E $stUnidadeInicial
        if ( empty( $stOrgaoInicial ) ) {
            $stOrgaoInicial = $arDotacao[0];
            $stCodigoOrgao = $stOrgaoInicial;
        }
        if ( empty( $stUnidadeInicial ) and $arDotacao[1] ) {
            $stUnidadeInicial = $arDotacao[1];
            $stCodigoUnidade  = $stUnidadeInicial;
        }
        if ( $rsRecordSet->getCampo( "nivel" ) == 1 ) {
             $stNomeOrgao   = $rsRecordSet->getCampo( "descricao" );
        }
        if ( $rsRecordSet->getCampo( "nivel" ) == 2 ) {
             $stNomeUnidade = $rsRecordSet->getCampo( "descricao" );
             $arCabec = array ( array( "classificacao" => $arDotacao[0] , "descricao" => $stNomeOrgao ),
                                array( "classificacao" => $arDotacao[1] , "descricao" => $stNomeUnidade) );
             $obRSTmp = new RecordSet();
             $obRSTmp->preenche( $arCabec );
             $arCabecalho[] = $obRSTmp;
        }
        //CRIA UM NOVO RECORD SET A CADA TROCA DE UNIDADE
        if ( ( $stOrgaoInicial != $arDotacao[0] ) or ( $stUnidadeInicial and $stUnidadeInicial != $arDotacao[1] ) ) {
             $obRSTmp = new RecordSet();
             $obRSTmp->preenche( $arLista);
             $arRecordSet[] = $obRSTmp;

             $obRSTmp = new RecordSet();
             $arTotalOrgao["descricao"] = " TOTAL .........";
             $obRSTmp->preenche( array( $arTotalOrgao) );
             $arRecordSet[] = $obRSTmp;

             $arTotalOrgao['vl_projeto']   = 0;
             $arTotalOrgao['vl_atividade'] = 0;
             $arTotalOrgao['vl_operacao']  = 0;
             $arTotalOrgao['vl_total']     = 0;
             $arTotalOrgao['vl_geral']     = 0;
             $arLista = array();
             if ( ( $stOrgaoInicial != $arDotacao[0] ) ) {
                  $stOrgaoInicial = "";
             }
             $stUnidadeInicial = "";
        }
        if ( $rsRecordSet->getCampo('nivel') == 1 ) {
            $arTotal['vl_projeto']   += $rsRecordSet->getCampo('vl_projeto');
            $arTotal['vl_atividade'] += $rsRecordSet->getCampo('vl_atividade');
            $arTotal['vl_operacao']  += $rsRecordSet->getCampo('vl_operacao');
            $arTotal['vl_total']     += $rsRecordSet->getCampo('vl_total');
            $arTotal['vl_geral']     += $rsRecordSet->getCampo('vl_total');
        } elseif ( $rsRecordSet->getCampo('nivel') == 2 ) {
            $arTotalOrgao['vl_projeto']   += $rsRecordSet->getCampo('vl_projeto');
            $arTotalOrgao['vl_atividade'] += $rsRecordSet->getCampo('vl_atividade');
            $arTotalOrgao['vl_operacao']  += $rsRecordSet->getCampo('vl_operacao');
            $arTotalOrgao['vl_total']     += $rsRecordSet->getCampo('vl_total');
            $arTotalOrgao['vl_geral']     += $rsRecordSet->getCampo('vl_total');
        } elseif ( $rsRecordSet->getCampo('nivel') > 2 ) {
            $arConta["dot"]          = $rsRecordSet->getCampo('dotacao');
            $arConta["orgao"]        = $arDotacao[0];
            $arConta["unidade"]      = $arDotacao[1];
            $arConta["dotacao"]      = $arDotacao[2];
            if ( $rsRecordSet->getCampo('nivel') == 3 ) {
                $arConta["quebra"] = true;
            } else {
                $arConta["quebra"] = false;
            }
            $arConta["cod_despesa"]  = $rsRecordSet->getCampo( "cod_despesa"  );
            $arConta["descricao"]    = $rsRecordSet->getCampo( "descricao"    );
            $arConta["nivel"]        = $rsRecordSet->getCampo( "nivel"        );
            $arConta["vl_projeto"]   = $rsRecordSet->getCampo( "vl_projeto"   );
            $arConta["vl_atividade"] = $rsRecordSet->getCampo( "vl_atividade" );
            $arConta["vl_operacao"]  = $rsRecordSet->getCampo( "vl_operacao"  );
            $arConta["vl_total"]     = $rsRecordSet->getCampo( "vl_total"     );
            $arLista[] = $arConta;
            $arFiltro = Sessao::read('filtro');
            if ($arFiltro["stDetPao"] == "sim") {
                if ( $rsRecordSet->getCampo( "detalhamento" ) ) {
                     $arConta = array();
                     $stDetalhamento = str_replace( chr(10) , "", $rsRecordSet->getCampo( "detalhamento" ) );
                     $stDetalhamento = wordwrap( $stDetalhamento , 60, chr(13) );
                     $arDetalhamento = explode( chr(13), $stDetalhamento );
                     foreach ($arDetalhamento as $stDetalhamento) {
                         $arConta["descricao"]    = $stDetalhamento;
                         $arLista[] = $arConta;
                     }
                     $arLista[] = array("descricao"=> "");
                }
             }
        }
        $rsRecordSet->proximo();
    }
    $rsTotal = new RecordSet();
    $arTotal["descricao"] = " TOTAL GERAL.........";

    $rsTotal->preenche( array( $arTotal ) );

    return $obErro;
}

function geraRecordSetOrgao(&$arRecordSet, &$arCabecalho, &$rsTotal , &$arEntidade, $stOrder = "")
{
    if ($this->stTipoRelatorio=="orcamento") {
        $this->obFOrcamentoSomatorioDotacaoPao =new FOrcamentoSomatorioDotacaoPao;;
    } else {
        $this->obFOrcamentoSomatorioDotacaoPao = new FOrcamentoSomatorioDotacaoPaoBalanco;
    }
    if ($this->stTipoRelatorio=="orcamento") {
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("exercicio", $this->inExercicio       );
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("stFiltro", $this->stFiltro           );
    } else {
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("exercicio", $this->inExercicio       );
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("stFiltro", $this->stFiltro           );
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("dataInicial", $this->stDataInicial   );
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("dataFinal", $this->stDataFinal       );
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("stEntidades", $this->stEntidades     );
        $this->obFOrcamentoSomatorioDotacaoPao->setDado ("stSituacao", $this->stSituacao       );
    }

    $obErro = $this->obFOrcamentoSomatorioDotacaoPao->recuperaTodos( $rsRecordSet, $stFiltro, "" );
    $arLista = array();
    $arCabecalho = array();
    $arEntidade  = array();
    $stOrgaoInicial   = "";
    $stUnidadeInicial = "";
    $arRecordSet = array();

    $inIndice=1;
    $arTmpEntidade[$inIndice]["descricao"]     = "";
    $inIndice++;
    $arTmpEntidade[$inIndice]["descricao"]     = "ENTIDADES RELACIONADAS";

    $stEntidade = substr(trim($this->stFiltro),strpos($this->stFiltro,"("));
    $stEntidade = substr(trim($stEntidade),0,strpos($stEntidade,")"));

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

    //CRIA UM NOVO RECORDSET A CADA UNIDADE
    $boFlagLoop = true;
    while ($boFlagLoop) {
        if ( $rsRecordSet->eof() ) {
            $boFlagLoop = false;
        }
        $arConta = array();
        $stDotacao = $rsRecordSet->getCampo('dotacao');
        $arDotacao = preg_split( "/\./", $stDotacao, 3 );
        //SETA O PRIMEIRO VALOR DAS VARIAVEIS $stOrgaoInicial E $stUnidadeInicial
        if ( empty( $stOrgaoInicial ) ) {
            $stOrgaoInicial = $arDotacao[0];
            $stCodigoOrgao = $stOrgaoInicial;
        }
        if ( $rsRecordSet->getCampo( "nivel" ) == 1 ) {
             $stNomeOrgao   = $rsRecordSet->getCampo( "descricao" );
             $arCabec = array ( array( "classificacao" => $arDotacao[0] , "descricao" => $stNomeOrgao ) );
             $obRSTmp = new RecordSet();
             $obRSTmp->preenche( $arCabec );
             $arCabecalho[] = $obRSTmp;
        }
        //CRIA UM NOVO RECORD SET A CADA TROCA DE UNIDADE
        if ($stOrgaoInicial != $arDotacao[0]) {
             $obRSTmp = new RecordSet();
             $obRSTmp->preenche( $arLista);
             $arRecordSet[] = $obRSTmp;

             $obRSTmp = new RecordSet();
             $arTotal["descricao"] = " TOTAL .........";
             $obRSTmp->preenche( array( $arTotal) );
             $arRecordSet[] = $obRSTmp;
             $arTotal['vl_projeto']   = 0;
             $arTotal['vl_atividade'] = 0;
             $arTotal['vl_operacao']  = 0;
             $arTotal['vl_total']     = 0;
             $arTotal['vl_geral']     = 0;
             $arLista = array();
             if ( ( $stOrgaoInicial != $arDotacao[0] ) ) {
                  $stOrgaoInicial = "";
             }
             $stUnidadeInicial = "";
        }
        if ( $rsRecordSet->getCampo('nivel') == 1 ) {
            $arTotal['vl_projeto']   += $rsRecordSet->getCampo('vl_projeto');
            $arTotal['vl_atividade'] += $rsRecordSet->getCampo('vl_atividade');
            $arTotal['vl_operacao']  += $rsRecordSet->getCampo('vl_operacao');
            $arTotal['vl_total']     += $rsRecordSet->getCampo('vl_total');
            $arTotal['vl_geral']     += $rsRecordSet->getCampo('vl_total');

            $arTotalG['vl_projeto']   += $rsRecordSet->getCampo('vl_projeto');
            $arTotalG['vl_atividade'] += $rsRecordSet->getCampo('vl_atividade');
            $arTotalG['vl_operacao']  += $rsRecordSet->getCampo('vl_operacao');
            $arTotalG['vl_total']     += $rsRecordSet->getCampo('vl_total');
            $arTotalG['vl_geral']     += $rsRecordSet->getCampo('vl_total');
        } elseif ( $rsRecordSet->getCampo('nivel') > 2 ) {
            $arConta["dot"]          = $rsRecordSet->getCampo('dotacao');
            $arConta["orgao"]        = $arDotacao[0];
            $arConta["unidade"]      = $arDotacao[1];
            $arConta["dotacao"]      = $arDotacao[2];
            if ( $rsRecordSet->getCampo('nivel') == 3 ) {
                $arConta["quebra"] = true;
            } else {
                $arConta["quebra"] = false;
            }
            $arConta["cod_despesa"]  = $rsRecordSet->getCampo( "cod_despesa"  );
            $arConta["descricao"]    = $rsRecordSet->getCampo( "descricao"    );
            $arConta["nivel"]        = $rsRecordSet->getCampo( "nivel"        );
            $arConta["vl_projeto"]   = $rsRecordSet->getCampo( "vl_projeto"   );
            $arConta["vl_atividade"] = $rsRecordSet->getCampo( "vl_atividade" );
            $arConta["vl_operacao"]  = $rsRecordSet->getCampo( "vl_operacao"  );
            $arConta["vl_total"]     = $rsRecordSet->getCampo( "vl_total"     );
            $arLista[] = $arConta;
            $arFiltro = Sessao::read('filtro');
            if ($arFiltroiltro["stDetPao"] == "sim") {
                if ( $rsRecordSet->getCampo( "detalhamento" ) ) {
                     $arConta = array();
                     $stDetalhamento = str_replace( chr(10) , "", $rsRecordSet->getCampo( "detalhamento" ) );
                     $stDetalhamento = wordwrap( $stDetalhamento , 60, chr(13) );
                     $arDetalhamento = explode( chr(13), $stDetalhamento );
                     foreach ($arDetalhamento as $stDetalhamento) {
                         $arConta["descricao"]    = $stDetalhamento;
                         $arLista[] = $arConta;
                     }
                     $arLista[] = array("descricao"=> "");
                }
            }
        }
        $rsRecordSet->proximo();
    }
    $rsTotal = new RecordSet();
    $arTotal["descricao"] = " TOTAL GERAL.........";
    $rsTotal->preenche( array( $arTotalG ) );

    return $obErro;
}
}
