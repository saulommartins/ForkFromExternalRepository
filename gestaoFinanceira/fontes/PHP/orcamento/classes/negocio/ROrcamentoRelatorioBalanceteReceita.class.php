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
    * Classe de Regra do Relatório de Balancete de Receita
    * Data de Criação   : 15/02/2005

    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Regra

    $Id: ROrcamentoRelatorioBalanceteReceita.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE_RELATORIO;
include_once CAM_GF_ORC_MAPEAMENTO."FOrcamentoBalanceteReceita.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";

/**
    * Classe de Regra de Negócios Balancete Receita
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class ROrcamentoRelatorioBalanceteReceita extends PersistenteRelatorio
{

/**
    * @var Object
    * @access Private
*/
var $obFBalanceteReceita;
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
var $stCodEstruturalInicial;
/**
    * @var String
    * @access Private
*/
var $stCodEstruturalFinal;
/**
    * @var String
    * @access Private
*/
var $stCodReduzidoInicial;
/**
    * @var String
    * @access Private
*/
var $stCodReduzidoFinal;
/**
    * @var String
    * @access Private
*/
var $stDataInicial;
/**
    * @var String
    * @access Private
*/
var $stDataFinal;
/**
    * @var String
    * @access Private
*/
var $inExercicio;
/**
    * @var Integer
    * @access Private
*/
var $stFiltro;
/**
     * @access Public
     * @param Object $valor
*/
var $inCodRecurso;
var $stDestinacaoRecurso;
var $inCodDetalhamento;

/**
     * @access Public
     * @param Object $valor
*/
var $rsResumoRecurso;

/**
     * @access Public
     * @param Object $valor
*/
function setFBalanceteReceita($valor) { $this->obFBalanceteReceita  = $valor; }
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
function setCodEstruturalInicial($valor) { $this->stCodEstruturalInicial      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEstruturalFinal($valor) { $this->stCodEstruturalFinal      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodReduzidoInicial($valor) { $this->stCodReduzidoInicial      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodReduzidoFinal($valor) { $this->stCodReduzidoFinal      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicial($valor) { $this->stDataInicial      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinal($valor) { $this->stDataFinal      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setExercicio($valor) { $this->inExercicio        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setFiltro($valor) { $this->stFiltro           = $valor; }
/**
     * @access Public
     * @return Object
*/
function setCodRecurso($valor) { $this->inCodRecurso       = $valor; }
function setDestinacaoRecurso($valor) { $this->stDestinacaoRecurso = $valor; }
function setCodDetalhamento($valor) { $this->inCodDetalhamento = $valor; }

/**
     * @access Public
     * @return Object
*/
function setRsResumoRecurso($rsValor)
{
    $this->rsResumoRecurso = $rsValor;
}

/**
     * @access Public
     * @return Object
*/
function getFBalanceteReceita() { return $this->obFBalanceteReceita;            }
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
function getCodEstruturalInicial() { return $this->stCodEstruturalInicial;                }
/**
     * @access Public
     * @return Object
*/
function getCodEstruturalFinal() { return $this->stCodEstruturalFinal;                }
/**
     * @access Public
     * @return Object
*/
function getCodReduzidoInicial() { return $this->stCodReduzidoInicial;                }
/**
     * @access Public
     * @return Object
*/
function getCodReduzidoFinal() { return $this->stCodReduzidoFinal;                }
/**
     * @access Public
     * @return Object
*/
function getDataInicial() { return $this->stDataInicial;                }
/**
     * @access Public
     * @return Object
*/
function getDataFinal() { return $this->stDataFinal;                }
/**
     * @access Public
     * @return Object
*/
function getExercicio() { return $this->inExercicio;                  }
/**
     * @access Public
     * @return Object
*/
function getFiltro() { return $this->stFiltro;                     }
/**
    * Método Construtor
    * @access Private
*/
function getCodRecurso() { return $this->inCodRecurso;                     }

/**
    * Método Construtor
    * @access Private
*/
function getRsResumoRecurso()
{
    return $this->rsResumoRecurso;
}

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioBalanceteReceita()
{
    $this->setFBalanceteReceita( new FOrcamentoBalanceteReceita );
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
    $stEntidade = "";
    if ( $this->getCodEntidade() ) {
        $stEntidade .= $this->getCodEntidade();
    } else {
        $this->obREntidade->listarUsuariosEntidade( $rsEntidades );
        while ( !$rsEntidades->eof() ) {
            $stEntidade .= $rsEntidades->getCampo( 'cod_entidade' ).",";
            $rsEntidades->proximo();
        }
        $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
        $stEntidade = $stEntidade;
    }

    $this->obFBalanceteReceita->setDado("exercicio",$this->getExercicio());
    $this->obFBalanceteReceita->setDado("stFiltro",$this->getFiltro());
    $this->obFBalanceteReceita->setDado("stEntidade",$this->getCodEntidade());
    $this->obFBalanceteReceita->setDado("stCodEstruturalInicial",$this->getCodEstruturalInicial());
    $this->obFBalanceteReceita->setDado("stCodEstruturalFinal",$this->getCodEstruturalFinal());
    $this->obFBalanceteReceita->setDado("stCodReduzidoInicial",$this->getCodReduzidoInicial());
    $this->obFBalanceteReceita->setDado("stCodReduzidoFinal",$this->getCodReduzidoFinal());
    $this->obFBalanceteReceita->setDado("stDataInicial",$this->getDataInicial());
    $this->obFBalanceteReceita->setDado("stDataFinal",$this->getDataFinal());
    $this->obFBalanceteReceita->setDado("inCodRecurso",$this->getCodRecurso());
    $this->obFBalanceteReceita->setDado("stDestinacaoRecurso",$this->stDestinacaoRecurso );
    $this->obFBalanceteReceita->setDado("inCodDetalhamento",$this->inCodDetalhamento );

    $stOrder = "cod_estrutural";
    $obErro = $this->obFBalanceteReceita->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $inCount            = 0;
    $inTotalPrevisto    = 0;
    $inTotalPeriodo     = 0;
    $inTotalAno         = 0;
    $inTotalDiferenca   = 0;
    $arRecord           = array();

    $arTotalRecurso = array();
    $arRecurso = array();

    while ( !$rsRecordSet->eof() ) {
        $stClassificacao = ucwords( strtolower( $rsRecordSet->getCampo('descricao' ) ) );

        $arRecord[$inCount]['nivel']             = 1;
        $arRecord[$inCount]['cod_estrutural']    = $rsRecordSet->getCampo('cod_estrutural');
        $arRecord[$inCount]['receita']           = $rsRecordSet->getCampo('receita');
        $arRecord[$inCount]['recurso']           = $rsRecordSet->getCampo('recurso');
        $arRecord[$inCount]['descricao']         = $stClassificacao;
        $arRecord[$inCount]['valor_previsto']    = number_format( $rsRecordSet->getCampo('valor_previsto'), 2, ',', '.' );
        $arRecord[$inCount]['arrecadado_periodo']= number_format( ($rsRecordSet->getCampo('arrecadado_periodo')* -1), 2, ',', '.' );
        $arRecord[$inCount]['arrecadado_ano']    = number_format( ($rsRecordSet->getCampo('arrecadado_ano')* -1), 2, ',', '.' );
        $arRecord[$inCount]['diferenca']         = number_format( $rsRecordSet->getCampo('diferenca'), 2, ',', '.' );

        if ($rsRecordSet->getCampo('recurso') != '') {
            if (!isset($arRecurso[$rsRecordSet->getCampo('recurso')])) {
                $stRecurso = SistemaLegado::pegaDado('nom_recurso', "orcamento.recurso('".$this->getExercicio()."')", " WHERE masc_recurso='".$rsRecordSet->getCampo('recurso')."'");
                $arRecurso[$rsRecordSet->getCampo('recurso')] = $stRecurso;
            }

            $stChave = $rsRecordSet->getCampo('recurso').' - '.$arRecurso[$rsRecordSet->getCampo('recurso')];
            if (isset($arTotalRecurso[$stChave]['valor_previsto'])) {
                $arTotalRecurso[$stChave]['valor_previsto'] += $rsRecordSet->getCampo('valor_previsto');
            } else {
                $arTotalRecurso[$stChave]['valor_previsto'] = $rsRecordSet->getCampo('valor_previsto');
            }

            if (isset($arTotalRecurso[$stChave]['arrecadado_periodo'])) {
                $arTotalRecurso[$stChave]['arrecadado_periodo'] += ($rsRecordSet->getCampo('arrecadado_periodo')* -1);
            } else {
                $arTotalRecurso[$stChave]['arrecadado_periodo'] = ($rsRecordSet->getCampo('arrecadado_periodo')* -1);
            }

            if (isset($arTotalRecurso[$stChave]['arrecadado_ano'])) {
                $arTotalRecurso[$stChave]['arrecadado_ano'] += ($rsRecordSet->getCampo('arrecadado_ano')* -1);
            } else {
                $arTotalRecurso[$stChave]['arrecadado_ano'] = ($rsRecordSet->getCampo('arrecadado_ano')* -1);
            }

            if (isset($arTotalRecurso[$stChave]['diferenca'])) {
                $arTotalRecurso[$stChave]['diferenca'] += $rsRecordSet->getCampo('diferenca');
            } else {
                $arTotalRecurso[$stChave]['diferenca'] = $rsRecordSet->getCampo('diferenca');
            }
        }

        $inCount++;
        if ($rsRecordSet->getCampo('receita') <> "") {
            $inTotalPrevisto    = $inTotalPrevisto + $rsRecordSet->getCampo('valor_previsto');
            $inTotalPeriodo     = $inTotalPeriodo + ($rsRecordSet->getCampo('arrecadado_periodo')* -1);
            $inTotalAno         = $inTotalAno + ($rsRecordSet->getCampo('arrecadado_ano')* -1);
        }

        $rsRecordSet->proximo();
    }
    ksort($arTotalRecurso);

    if ($inCount) {
        //MONTA TOTALIZADOR GERAL
        $arRecord[$inCount]['nivel']             = 2;
        $arRecord[$inCount]['cod_estrutural']    = "";
        $arRecord[$inCount]['receita']           = "";
        $arRecord[$inCount]['recurso']           = "";
        $arRecord[$inCount]['descricao']         = "TOTAL";
        $arRecord[$inCount]['valor_previsto']    = number_format( $inTotalPrevisto, 2, ',', '.' );
        $arRecord[$inCount]['arrecadado_periodo']= number_format( $inTotalPeriodo, 2, ',', '.' );
        $arRecord[$inCount]['arrecadado_ano']    = number_format( $inTotalAno, 2, ',', '.' );
        $inTotalDiferenca = $inTotalPrevisto - $inTotalAno;
        $arRecord[$inCount]['diferenca']         = number_format( $inTotalDiferenca, 2, ',', '.' );

        $inCount++;
    }
    $arRecord[$inCount]['nivel']             = 2;
    $arRecord[$inCount]['cod_estrutural']    = "";
    $arRecord[$inCount]['receita']           = "";
    $arRecord[$inCount]['recurso']           = "";
    $arRecord[$inCount]['descricao']         = "ENTIDADES RELACIONADAS";
    $arRecord[$inCount]['valor_previsto']    = "";
    $arRecord[$inCount]['arrecadado_periodo']= "";
    $arRecord[$inCount]['arrecadado_ano']    = "";
    $arRecord[$inCount]['diferenca']         = "";

    $this->obREntidade->setExercicio( $this->getExercicio() );
    $inEntidades = str_replace("'","",$this->getCodEntidade() );
    $arEntidades = explode(",",$inEntidades );

    foreach ($arEntidades as $key => $inCodEntidade) {
        $inCount++;
        $this->obREntidade->setCodigoEntidade( $inCodEntidade );
        $this->obREntidade->consultarNomes($rsLista);
        $arRecord[$inCount]['descricao'] = $rsLista->getCampo("entidade");
    }

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    $inCount = 0;
    $arResumoRecurso[$inCount]['nivel']             = 2;
    $arResumoRecurso[$inCount]['cod_estrutural']    = "RESUMO POR RECURSO";
    $arResumoRecurso[$inCount]['receita']           = "";
    $arResumoRecurso[$inCount]['recurso']           = "";
    $arResumoRecurso[$inCount]['descricao']         = "";
    $arResumoRecurso[$inCount]['valor_previsto']    = "";
    $arResumoRecurso[$inCount]['arrecadado_periodo']= "";
    $arResumoRecurso[$inCount]['arrecadado_ano']    = "";
    $arResumoRecurso[$inCount]['diferenca']         = "";

    foreach ($arTotalRecurso as $stChave => $arDadosRecurso) {
        $inCount++;
        $arResumoRecurso[$inCount]['nivel']             = 2;
        $arResumoRecurso[$inCount]['cod_estrutural']    = '';
        $arResumoRecurso[$inCount]['receita']           = '';
        $arResumoRecurso[$inCount]['recurso']           = '';
        $arResumoRecurso[$inCount]['descricao']         = $stChave;
        $arResumoRecurso[$inCount]['valor_previsto']    = number_format($arDadosRecurso['valor_previsto'], 2, ',', '.');
        $arResumoRecurso[$inCount]['arrecadado_periodo']= number_format($arDadosRecurso['arrecadado_periodo'], 2, ',', '.');
        $arResumoRecurso[$inCount]['arrecadado_ano']    = number_format($arDadosRecurso['arrecadado_ano'], 2, ',', '.');
        $arResumoRecurso[$inCount]['diferenca']         = number_format($arDadosRecurso['diferenca'], 2, ',', '.');
        if (isset($arTotal['valor_previsto'])) {
            $arTotal['valor_previsto'] += $arDadosRecurso['valor_previsto'];
        } else {
            $arTotal['valor_previsto'] = $arDadosRecurso['valor_previsto'];
        }

        if (isset($arTotal['arrecadado_periodo'])) {
            $arTotal['arrecadado_periodo'] += $arDadosRecurso['arrecadado_periodo'];
        } else {
            $arTotal['arrecadado_periodo'] = $arDadosRecurso['arrecadado_periodo'];
        }

        if (isset($arTotal['arrecadado_ano'])) {
            $arTotal['arrecadado_ano'] += $arDadosRecurso['arrecadado_ano'];
        } else {
            $arTotal['arrecadado_ano'] = $arDadosRecurso['arrecadado_ano'];
        }

        if (isset($arTotal['diferenca'])) {
            $arTotal['diferenca'] += $arDadosRecurso['diferenca'];
        } else {
            $arTotal['diferenca'] = $arDadosRecurso['diferenca'];
        }
    }

    $arResumoRecurso[$inCount]['nivel']             = 2;
    $arResumoRecurso[$inCount]['cod_estrutural']    = 'TOTAL FINAL POR RECURSO';
    $arResumoRecurso[$inCount]['receita']           = '';
    $arResumoRecurso[$inCount]['recurso']           = '';
    $arResumoRecurso[$inCount]['descricao']         = '';
    $arResumoRecurso[$inCount]['valor_previsto']    = number_format($arTotal['valor_previsto'], 2, ',', '.');
    $arResumoRecurso[$inCount]['arrecadado_periodo']= number_format($arTotal['arrecadado_periodo'], 2, ',', '.');
    $arResumoRecurso[$inCount]['arrecadado_ano']    = number_format($arTotal['arrecadado_ano'], 2, ',', '.');
    $arResumoRecurso[$inCount]['diferenca']         = number_format($arTotal['diferenca'], 2, ',', '.');

    $rsResumoRecurso = new RecordSet;
    $rsResumoRecurso->preenche($arResumoRecurso);
    $this->setRsResumoRecurso($rsResumoRecurso);

    return $obErro;
}

}
