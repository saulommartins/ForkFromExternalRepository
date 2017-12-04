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
    * Classe de Regra de Negócio Itens
    * Data de Criação   : 26/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    $Id: ROrcamentoRelatorioRelacaoReceita.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.20
*/

/*
$Log$
Revision 1.8  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO        );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoRelacaoReceita.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"             );

/**
    * Classe de Regra de Negócio Itens
    * @author Desenvolvedor: Marcelo Boezzio Paulino
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
*/
class ROrcamentoRelatorioRelacaoReceita extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFRelacaoReceita;
/**
    * @var Object
    * @access Private
*/
var $obREntidade;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inExercicio;
/**
    * @var Integer
    * @access Private
*/
var $stFiltro;
/**
    * @var String
    * @access Private
*/
var $stTipoOrdenacao;

/**
     * @access Public
     * @param Object $valor
*/
function setFRelacaoReceita($valor) { $this->obFRelacaoReceita  = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setREntidade($valor) { $this->obREntidade     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setExercicio($valor) { $this->inExercicio        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setTipoOrdenacao($valor) { $this->stTipoOrdenacao    = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setFiltro($valor) { $this->stFiltro           = $valor; }

/**
     * @access Public
     * @return Object
*/
function getFRelacaoReceita() { return $this->obFRelacaoReceita;            }
/**
     * @access Public
     * @param Object $valor
*/
function getREntidade() { return $this->obREntidade;               }
/**
     * @access Public
     * @return Object
*/
function getCodEntidade() { return $this->inCodEntidade;                }
/**
     * @access Public
     * @return Object
*/
function getExercicio() { return $this->inExercicio;                  }
/**
     * @access Public
     * @return Object
*/
function getTipoOrdenacao() { return $this->stTipoOrdenacao;              }

/**
     * @access Public
     * @return Object
*/
function getFiltro() { return $this->stFiltro;                     }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioRelacaoReceita()
{
    $this->setFRelacaoReceita( new FOrcamentoRelacaoReceita );
    $this->setREntidade                  ( new ROrcamentoEntidade     );
    $this->obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm')            );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    $stFiltro = "";
    if ( $this->getCodEntidade() ) {
        $stFiltro .= " WHERE cod_entidade IN ( ".$this->getCodEntidade()." )";
    } else {
        $this->obREntidade->listarUsuariosEntidade( $rsEntidades );
        while ( !$rsEntidades->eof() ) {
            $stFiltro .= $rsEntidades->getCampo( 'cod_entidade' ).",";
            $rsEntidades->proximo();
        }
        $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 1 );
        $stFiltro = " WHERE cod_entidade IN ( ".$stFiltro." )";
    }

    $this->obFRelacaoReceita->setDado("exercicio",$this->getExercicio());
    $this->obFRelacaoReceita->setDado("stFiltro",$this->getFiltro());

    if($this->getTipoOrdenacao() == "reduzido")
        $stOrder="cod_receita";
    else
        $stOrder="classificacao";

    $obErro = $this->obFRelacaoReceita->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $inCount        = 0;
    $inTotalInicial = 0;
    $inTotalSaldo   = 0;
    $arRecord       = array();

    while ( !$rsRecordSet->eof() ) {
        if ( mb_check_encoding($rsRecordSet->getCampo('descricao_receita')) ) {
            $stClassificacao = mb_convert_case(utf8_decode($rsRecordSet->getCampo('descricao_receita')),MB_CASE_TITLE);
        } else {
            $stClassificacao = mb_convert_case($rsRecordSet->getCampo('descricao_receita'),MB_CASE_TITLE);
        }

        $arRecord[$inCount]['nivel']             = 1;
        $arRecord[$inCount]['classificacao']     = $rsRecordSet->getCampo('classificacao'     );
        $arRecord[$inCount]['descricao_receita'] = $stClassificacao;
        $arRecord[$inCount]['cod_recurso']       = $rsRecordSet->getCampo('cod_recurso'       );
        if ( mb_check_encoding($rsRecordSet->getCampo('nom_recurso')) ) {
            $arRecord[$inCount]['nom_recurso']       = utf8_decode($rsRecordSet->getCampo('nom_recurso'       ));
        } else {
            $arRecord[$inCount]['nom_recurso']       = $rsRecordSet->getCampo('nom_recurso'       );
        }
        $arRecord[$inCount]['cod_receita']       = $rsRecordSet->getCampo('cod_receita'       );
        $arRecord[$inCount]['valor_previsto']    = number_format( $rsRecordSet->getCampo('valor_previsto'), 2, ',', '.' );
        $arRecord[$inCount]['saldo_disponivel']  = number_format($rsRecordSet->getCampo( 'vl_arrecadado' ), 2, ',', '.' );
        $inCount++;
        $inTotalInicial = bcadd( $inTotalInicial, $rsRecordSet->getCampo( 'valor_previsto' ), 4 );
        $inTotalSaldo   = bcadd( $inTotalSaldo  , $rsRecordSet->getCampo( 'vl_arrecadado' ) , 4 );
        $rsRecordSet->proximo();
    }

    //MONTA TOTALIZADOR GERAL
    $arRecord[$inCount]['nivel']             = 2;
    $arRecord[$inCount]['classificacao']     = "TOTAL";
    $arRecord[$inCount]['descricao_receita'] = "";
    $arRecord[$inCount]['cod_recurso']       = "";
    $arRecord[$inCount]['nom_recurso']       = "";
    $arRecord[$inCount]['cod_receita']       = "";
    $arRecord[$inCount]['valor_previsto']    = number_format( $inTotalInicial, 2, ',', '.' );
    $arRecord[$inCount]['saldo_disponivel']  = number_format( $inTotalSaldo  , 2, ',', '.' );

    $inCount++;
    $arRecord[$inCount]['nivel']             = 1;
    $arRecord[$inCount]['classificacao']     = "";
    $arRecord[$inCount]['descricao_receita'] = "";
    $arRecord[$inCount]['cod_recurso']       = "";
    $arRecord[$inCount]['nom_recurso']       = "";
    $arRecord[$inCount]['cod_receita']       = "";
    $arRecord[$inCount]['valor_previsto']    = "";
    $arRecord[$inCount]['saldo_disponivel']  = "";

    $inCount++;
    $arRecord[$inCount]['nivel']             = 2;
    $arRecord[$inCount]['classificacao']     = "";
    $arRecord[$inCount]['descricao_receita'] = "ENTIDADES RELACIONADAS";
    $arRecord[$inCount]['cod_recurso']       = "";
    $arRecord[$inCount]['nom_recurso']       = "";
    $arRecord[$inCount]['cod_receita']       = "";
    $arRecord[$inCount]['valor_previsto']    = "";
    $arRecord[$inCount]['saldo_disponivel']  = "";

    $stEntidade = substr(trim($this->getFiltro()),strpos($this->getFiltro(),"("));
    $stEntidade = substr($stEntidade,0,strlen($stEntidade)-1);

    $inEntidades = str_replace("'","",$stEntidade);
    $arEntidades = explode(",",$inEntidades );

    foreach ($arEntidades as $key => $inCodEntidade) {
        $inCount++;
        $this->obREntidade->setCodigoEntidade( $inCodEntidade );
        $this->obREntidade->consultarNomes($rsLista);
        $arRecord[$inCount]['nivel']             = 1;
        $arRecord[$inCount]['classificacao']     = "";
        $arRecord[$inCount]['descricao_receita'] = "- ".$rsLista->getCampo("entidade");
        $arRecord[$inCount]['cod_recurso']       = "";
        $arRecord[$inCount]['nom_recurso']       = "";
        $arRecord[$inCount]['cod_despesa']       = "";
        $arRecord[$inCount]['valor_previsto']    = "";
        $arRecord[$inCount]['saldo_disponivel']  = "";
    }

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}
