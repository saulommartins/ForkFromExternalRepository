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
    * Classe de Regra da Função FN_ORCAMENTO_RELACAO_DESPESA
    * Data de Criação   : 05/10/2004

    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Anderson Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.18
*/

/*
$Log$
Revision 1.9  2006/11/20 21:37:42  gelson
Bug #7444#
Parte 1

Revision 1.8  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO      );
include_once ( CAM_GF_ORC_MAPEAMENTO."FOrcamentoRelacaoDespesa.class.php"   );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"              );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"              );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"          );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"                     );
include_once ( CAM_FW_PDF."RRelatorio.class.php"           );

/**
    * Classe de Regra da Função FN_ORCAMENTO_RELACAO_DESPESA
    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Anderson Buzo
*/
class ROrcamentoRelatorioRelacaoDespesa extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFRelacaoDespesa;
/**
    * @var Object
    * @access Private
*/
var $obRRelatorio;
/**
    * @var Object
    * @access Private
*/
var $obREntidade;
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoUnidade;
/**
    * @var Object
    * @access Private
*/
var $obRConfiguracaoOrcamento;
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoRecurso;
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
    * @var String
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
function setFRelacaoDespesa($valor) { $this->obFRelacaoDespesa        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRRelatorio($valor) { $this->obRRelatorio              = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setREntidade($valor) { $this->obREntidade              = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoUnidade($valor) { $this->obROrcamentoUnidade              = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoRecurso($valor) { $this->obROrcamentoRecurso              = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRConfiguracaoOrcamento($valor) { $this->obRConfiguracaoOrcamento = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade            = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setExercicio($valor) { $this->inExercicio              = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setFiltro($valor) { $this->stFiltro                 = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setTipoOrdenacao($valor) { $this->stTipoOrdenacao    = $valor; }

/**
     * @access Public
     * @return Object
*/
function getFRelacaoDespesa() { return $this->obFRelacaoDespesa;            }
/**
     * @access Public
     * @param Object $valor
*/
function getRRelatorio() { return $this->obRRelatorio;                  }
/**
     * @access Public
     * @param Object $valor
*/
function getREntidade() { return $this->obREntidade;                  }
/**
     * @access Public
     * @param Object $valor
*/
function getROrcamentoUnidade() { return $this->obROrcamentoUnidade;                  }
/**
     * @access Public
     * @param Object $valor
*/
function getROrcamentoRecurso() { return $this->obROrcamentoRecurso;                  }
/**
     * @access Public
     * @param Object $valor
*/
function getRConfiguracaoOrcamento() { return $this->obRConfiguracaoOrcamento;     }
/**
     * @access Public
     * @return Object
*/
function getCodEntidade() { return $this->inCodEntidade;                }
/**
     * @access Public
     * @param Object $valor
*/
function getExercicio() { return $this->inExercicio;                  }
/**
     * @access Public
     * @param Object $valor
*/
function getFiltro() { return $this->stFiltro;                     }
/**
     * @access Public
     * @param String $valor
*/
function getTipoOrdenacao() { return $this->stTipoOrdenacao; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioRelacaoDespesa()
{
    $this->setFRelacaoDespesa            ( new FOrcamentoRelacaoDespesa );
    $this->setRRelatorio                 ( new RRelatorio               );
    $this->setREntidade                  ( new ROrcamentoEntidade       );
    $this->setROrcamentoUnidade          ( new ROrcamentoUnidadeOrcamentaria );
    $this->setROrcamentoRecurso          ( new ROrcamentoRecurso        );
    $this->setRConfiguracaoOrcamento     ( new ROrcamentoConfiguracao   );
    $this->obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm')              );
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
    $this->obFRelacaoDespesa->setDado("exercicio", $this->getExercicio() );
    $this->obFRelacaoDespesa->setDado("inNumOrgao", $this->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );
    $this->obFRelacaoDespesa->setDado("inNumUnidade", $this->obROrcamentoUnidade->getNumeroUnidade());
    $this->obFRelacaoDespesa->setDado("cod_recurso", $this->obROrcamentoRecurso->getCodRecurso());
    $this->obFRelacaoDespesa->setDado("stDestinacaoRecurso", $this->obROrcamentoRecurso->getDestinacaoRecurso());
    $this->obFRelacaoDespesa->setDado("cod_detalhamento", $this->obROrcamentoRecurso->getCodDetalhamento());
    $this->obFRelacaoDespesa->setDado("stFiltro" , $this->getFiltro() );

    $stOrder = "num_orgao,num_unidade,cod_funcao,cod_subfuncao,cod_programa,num_pao";

    if($this->getTipoOrdenacao() == "reduzido")
        $stOrder.=",cod_despesa,classificacao";
    else
        $stOrder.=",classificacao,cod_despesa";

    $obErro = $this->obFRelacaoDespesa->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $inCount                 = 0;
    $arRecord                = array();
    $stDotacaoPrincipal      = "";
    $stDotacaoPrincipalAtiva = "";
    $inOrgao                 = "";
    $inOrgaoAtivo            = "";
    $stOrgaoUnidade          = "";
    $stOrgaoUnidadeAtivo     = "";
    $stPAO                   = "";
    $stPAOAtivo              = "";
    $inTotalOrgao            = 0;
    $inTotalUnidade          = 0;
    $inTotalGeral            = 0;
    $inTotalOrgaoDisponivel  = 0;
    $inTotalUnidadeDisponivel = 0;
    $inTotalGeralDisponivel   = 0;

    //monta mascara de ORGAO, UNIDADE, FUNCAO, SUB-FUNCAO e PROGRAMA
    $this->obRConfiguracaoOrcamento->consultarConfiguracao();
    $stMascaraDespesa = $this->obRConfiguracaoOrcamento->getMascDespesa();
    $arMarcaraDespesa = preg_split( "/[^a-zA-Z0-9]/", $stMascaraDespesa);
    $stMascOrgao      = $arMarcaraDespesa[0];
    $stMascUnidade    = $arMarcaraDespesa[0].".".$arMarcaraDespesa[1];
    $stMascFuncao     = $arMarcaraDespesa[0].".".$arMarcaraDespesa[1].".".$arMarcaraDespesa[2];
    $stMascSubFuncao  = $arMarcaraDespesa[0].".".$arMarcaraDespesa[1].".".$arMarcaraDespesa[2].".".$arMarcaraDespesa[3];
    $stMascPrograma   = $arMarcaraDespesa[0].".".$arMarcaraDespesa[1].".".$arMarcaraDespesa[2].".".$arMarcaraDespesa[3].".".$arMarcaraDespesa[4];
    $stMascPAO        = $arMarcaraDespesa[0].".".$arMarcaraDespesa[1].".".$arMarcaraDespesa[2].".".$arMarcaraDespesa[3].".".$arMarcaraDespesa[4].".".$arMarcaraDespesa[5];

    while ( !$rsRecordSet->eof() ) {
        $stDotacaoPrincipal  = $rsRecordSet->getCampo('num_orgao').".".$rsRecordSet->getCampo('num_unidade').".";
        $stDotacaoPrincipal .= $rsRecordSet->getCampo('cod_funcao').".".$rsRecordSet->getCampo('cod_subfuncao').".";
        $stDotacaoPrincipal .= $rsRecordSet->getCampo('cod_programa');

        $inOrgao        = $rsRecordSet->getCampo('num_orgao');
        $stOrgaoUnidade = $rsRecordSet->getCampo('num_orgao').".".$rsRecordSet->getCampo('num_unidade');
        $stPAO          = $rsRecordSet->getCampo('num_pao');

        $stClassPAO = $rsRecordSet->getCampo('num_orgao' ).".".$rsRecordSet->getCampo('num_unidade' ).".".$rsRecordSet->getCampo('cod_funcao' ).".".$rsRecordSet->getCampo('cod_subfuncao' ).".".$rsRecordSet->getCampo('num_programa' ).".".$rsRecordSet->getCampo('num_acao' );
        $stClassPAO = Mascara::validaMascaraDinamica( $stMascPAO       , $stClassPAO );

        if ($inCount == 0) {
            $inOrgaoAtivo        = $inOrgao;
            $stOrgaoUnidadeAtivo = $stOrgaoUnidade;
        }

        //MONTA TOTALIZADOR DE UNIDADE
        if ($stOrgaoUnidade != $stOrgaoUnidadeAtivo) {
            $arRecord[$inCount]['pagina']            = 0;
            $arRecord[$inCount]['nivel']             = 0;
            $arRecord[$inCount]['classificacao']     = "TOTAL UNIDADE";
            $arRecord[$inCount]['descricao_despesa'] = "";
            $arRecord[$inCount]['cod_recurso']       = "";
            $arRecord[$inCount]['nom_recurso']       = "";
            $arRecord[$inCount]['cod_despesa']       = "";
            $arRecord[$inCount]['valor_previsto']    = number_format( $inTotalUnidade, 2 ,',' ,'.' );
            $arRecord[$inCount]['saldo_disponivel']  = "";
            $stOrgaoUnidadeAtivo = $stOrgaoUnidade;
            $inTotalUnidade = 0;
            $inCount++;
        }

        //MONTA TOTALIZADOR DE ORGAO
        if ($inOrgao != $inOrgaoAtivo) {
            $arRecord[$inCount]['pagina']            = 1;
            $arRecord[$inCount]['nivel']             = 0;
            $arRecord[$inCount]['classificacao']     = utf8_decode("TOTAL ORGÃO");
            $arRecord[$inCount]['descricao_despesa'] = "";
            $arRecord[$inCount]['cod_recurso']       = "";
            $arRecord[$inCount]['nom_recurso']       = "";
            $arRecord[$inCount]['cod_despesa']       = "";
            $arRecord[$inCount]['valor_previsto']    = number_format( $inTotalOrgao, 2, ',', '.' );
            $arRecord[$inCount]['saldo_disponivel']  = "";
            $inOrgaoAtivo = $inOrgao;
            $inTotalOrgao = 0;
            $inCount++;
        }

        //MONTA LINHA DE ORGAO, UNIDADE, FUNCAO, SUBFUNCAO, PROGRAMA
        if ( $inCount == 0 OR ( $stDotacaoPrincipalAtiva != $stDotacaoPrincipal )) {

            $stPAOAtivo = "";

            $stClassOrgao     = $rsRecordSet->getCampo('num_orgao' );
            $stClassUnidade   = $rsRecordSet->getCampo('num_orgao' ).".".$rsRecordSet->getCampo('num_unidade' );
            $stClassFuncao    = $rsRecordSet->getCampo('num_orgao' ).".".$rsRecordSet->getCampo('num_unidade' ).".".$rsRecordSet->getCampo('cod_funcao' );
            $stClassSubFuncao = $rsRecordSet->getCampo('num_orgao' ).".".$rsRecordSet->getCampo('num_unidade' ).".".$rsRecordSet->getCampo('cod_funcao' ).".".$rsRecordSet->getCampo('cod_subfuncao' );
            $stClassPrograma  = $rsRecordSet->getCampo('num_orgao' ).".".$rsRecordSet->getCampo('num_unidade' ).".".$rsRecordSet->getCampo('cod_funcao' ).".".$rsRecordSet->getCampo('cod_subfuncao' ).".".$rsRecordSet->getCampo('num_programa' );

            $stClassOrgao     = Mascara::validaMascaraDinamica( $stMascOrgao     , $stClassOrgao );
            $stClassUnidade   = Mascara::validaMascaraDinamica( $stMascUnidade   , $stClassUnidade );
            $stClassFuncao    = Mascara::validaMascaraDinamica( $stMascFuncao    , $stClassFuncao );
            $stClassSubFuncao = Mascara::validaMascaraDinamica( $stMascSubFuncao , $stClassSubFuncao );
            $stClassPrograma  = Mascara::validaMascaraDinamica( $stMascPrograma  , $stClassPrograma );

            $stDotacaoPrincipalAtiva = $stDotacaoPrincipal;
            $arRecord[$inCount]['pagina']            = 0;
            $arRecord[$inCount]['nivel']             = 0;
            $arRecord[$inCount]['classificacao']     = $stClassOrgao[1];
            $arRecord[$inCount]['descricao_despesa'] = utf8_decode($rsRecordSet->getCampo('nom_orgao' ));
            $arRecord[$inCount]['cod_recurso']       = "";
            $arRecord[$inCount]['nom_recurso']       = "";
            $arRecord[$inCount]['cod_despesa']       = "";
            $arRecord[$inCount]['valor_previsto']    = "";
            $arRecord[$inCount]['saldo_disponivel']  = "";
            $inCount++;
            $arRecord[$inCount]['pagina']            = 0;
            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = $stClassUnidade[1];
            $arRecord[$inCount]['descricao_despesa'] = utf8_decode($rsRecordSet->getCampo('nom_unidade' ));
            $arRecord[$inCount]['cod_recurso']       = "";
            $arRecord[$inCount]['nom_recurso']       = "";
            $arRecord[$inCount]['cod_despesa']       = "";
            $arRecord[$inCount]['valor_previsto']    = "";
            $arRecord[$inCount]['saldo_disponivel']  = "";
            $inCount++;
            $arRecord[$inCount]['pagina']            = 0;
            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = $stClassFuncao[1];
            $arRecord[$inCount]['descricao_despesa'] = utf8_decode($rsRecordSet->getCampo('nom_funcao' ));
            $arRecord[$inCount]['cod_recurso']       = "";
            $arRecord[$inCount]['nom_recurso']       = "";
            $arRecord[$inCount]['cod_despesa']       = "";
            $arRecord[$inCount]['valor_previsto']    = "";
            $arRecord[$inCount]['saldo_disponivel']  = "";
            $inCount++;
            $arRecord[$inCount]['pagina']            = 0;
            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = $stClassSubFuncao[1];
            $arRecord[$inCount]['descricao_despesa'] = utf8_decode($rsRecordSet->getCampo('nom_subfuncao' ));
            $arRecord[$inCount]['cod_recurso']       = "";
            $arRecord[$inCount]['nom_recurso']       = "";
            $arRecord[$inCount]['cod_despesa']       = "";
            $arRecord[$inCount]['valor_previsto']    = "";
            $arRecord[$inCount]['saldo_disponivel']  = "";
            $inCount++;
            $arRecord[$inCount]['pagina']            = 0;
            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = $stClassPrograma[1];
            $arRecord[$inCount]['descricao_despesa'] = utf8_decode($rsRecordSet->getCampo('nom_programa' ));
            $arRecord[$inCount]['cod_recurso']       = "";
            $arRecord[$inCount]['nom_recurso']       = "";
            $arRecord[$inCount]['cod_despesa']       = "";
            $arRecord[$inCount]['valor_previsto']    = "";
            $arRecord[$inCount]['saldo_disponivel']  = "";
            $inCount++;
        }

        //MONTA LINHA DO PAO
        if ( $inCount == 5 OR ( $stPAO != $stPAOAtivo )) {
            if ($inCount == 5) {
                $inNivel = 1;
            } else {
                $inNivel = 0;
            }
            $stPAOAtivo = $stPAO;
            $arRecord[$inCount]['pagina']            = 0;
            $arRecord[$inCount]['nivel']             = $inNivel;
            $arRecord[$inCount]['classificacao']     = $stClassPAO[1];
            $arRecord[$inCount]['descricao_despesa'] = utf8_decode($rsRecordSet->getCampo('nom_pao' ));
            $arRecord[$inCount]['cod_recurso']       = "";
            $arRecord[$inCount]['nom_recurso']       = "";
            $arRecord[$inCount]['cod_despesa']       = "";
            $arRecord[$inCount]['valor_previsto']    = "";
            $arRecord[$inCount]['saldo_disponivel']  = "";
            $inCount++;
        }

        $stClassificacao = ucwords( strtolower( $rsRecordSet->getCampo('descricao' ) ) );

        //MONTA LINHA DA DESPESA
        $arRecord[$inCount]['pagina']            = 0;
        $arRecord[$inCount]['nivel']             = 2;
        $arRecord[$inCount]['classificacao']     = $rsRecordSet->getCampo('classificacao'   );
        $arRecord[$inCount]['descricao_despesa'] = utf8_decode($stClassificacao);
        $arRecord[$inCount]['cod_recurso']       = $rsRecordSet->getCampo('num_recurso' );
        $arRecord[$inCount]['nom_recurso']       = utf8_decode($rsRecordSet->getCampo('nom_recurso' ));
        $arRecord[$inCount]['cod_despesa']       = $rsRecordSet->getCampo('cod_despesa' );
        $arRecord[$inCount]['valor_previsto']    = number_format($rsRecordSet->getCampo('vl_original')  , 2, ',', '.' );
        $arRecord[$inCount]['saldo_disponivel']  = number_format($rsRecordSet->getCampo('saldo_dotacao'), 2, ',', '.' );

        $inTotalOrgao   = $inTotalOrgao   + $rsRecordSet->getCampo('vl_original');
        $inTotalUnidade = $inTotalUnidade + $rsRecordSet->getCampo('vl_original');
        $inTotalGeral   = $inTotalGeral   + $rsRecordSet->getCampo('vl_original');
        $nuTotalOrgaoDisponivel   = $nuTotalOrgaoDisponivel   + $rsRecordSet->getCampo('saldo_dotacao');
        $nuTotalUnidadeDisponivel = $nuTotalUnidadeDisponivel + $rsRecordSet->getCampo('saldo_dotacao');
        $nuTotalGeralDisponivel   = $nuTotalGeralDisponivel   + $rsRecordSet->getCampo('saldo_dotacao');
        $inCount++;

        $rsRecordSet->proximo();
    }

    //MONTA TOTALIZADOR DE UNIDADE
    $arRecord[$inCount]['pagina']            = 0;
    $arRecord[$inCount]['nivel']             = 0;
    $arRecord[$inCount]['classificacao']     = "TOTAL UNIDADE";
    $arRecord[$inCount]['descricao_despesa'] = "";
    $arRecord[$inCount]['cod_recurso']       = "";
    $arRecord[$inCount]['nom_recurso']       = "";
    $arRecord[$inCount]['cod_despesa']       = "";
    $arRecord[$inCount]['valor_previsto']    = number_format( $inTotalUnidade,          2 ,',','.');
    $arRecord[$inCount]['saldo_disponivel']  = number_format( $nuTotalUnidadeDisponivel,2 ,',','.');
    $stOrgaoUnidadeAtivo = $stOrgaoUnidade;
    $inTotalUnidade = 0;
    $nuTotalUnidadeDisponivel = 0;
    $inCount++;

    //MONTA TOTALIZADOR DE ORGAO
    $arRecord[$inCount]['pagina']            = 0;
    $arRecord[$inCount]['nivel']             = 0;
    $arRecord[$inCount]['classificacao']     = utf8_decode("TOTAL ORGÃO");
    $arRecord[$inCount]['descricao_despesa'] = "";
    $arRecord[$inCount]['cod_recurso']       = "";
    $arRecord[$inCount]['nom_recurso']       = "";
    $arRecord[$inCount]['cod_despesa']       = "";
    $arRecord[$inCount]['valor_previsto']    = number_format( $inTotalOrgao          , 2, ',', '.' );
    $arRecord[$inCount]['saldo_disponivel']  = number_format( $nuTotalOrgaoDisponivel, 2, ',', '.' );
    $inOrgaoAtivo = $inOrgao;
    $inTotalOrgao = 0;
    $nuTotalOrgaoDisponivel = 0;
    $inCount++;

    //MONTA TOTALIZADOR GERAL
    $arRecord[$inCount]['pagina']            = 0;
    $arRecord[$inCount]['nivel']             = 0;
    $arRecord[$inCount]['classificacao']     = "TOTAL GERAL";
    $arRecord[$inCount]['descricao_despesa'] = "";
    $arRecord[$inCount]['cod_recurso']       = "";
    $arRecord[$inCount]['nom_recurso']       = "";
    $arRecord[$inCount]['cod_despesa']       = "";
    $arRecord[$inCount]['valor_previsto']    = number_format( $inTotalGeral          , 2, ',', '.' );
    $arRecord[$inCount]['saldo_disponivel']  = number_format( $nuTotalGeralDisponivel, 2, ',', '.' );

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}
