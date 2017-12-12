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
    * Classe de Regra da Função FN_ORCAMENTO_CONSOLIDADO_ELEM_DESPESA
    * Data de Criação   : 05/10/2004

    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Regra

    $Revision: 30824 $
    $Name$
    $Autor:$
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.23
*/

/*
$Log$
Revision 1.15  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO     );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"             );
include_once ( CAM_GF_ORC_MAPEAMENTO."FOrcamentoConsolidadoElemDesp.class.php"   );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"              );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"          );

/**
    * Classe de Regra da Função FN_ORCAMENTO_CONSOLIDADO_ELEM_DESP
    * @author Desenvolvedor: Vandré Miguel Ramos
*/
class ROrcamentoRelatorioConsolidadoElemDesp extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFBalanceteDespesa;
/**
    * @var Object
    * @access Private
*/
var $obREntidade;
/**
    * @var Object
    * @access Private
*/
var $obRConfiguracaoOrcamento;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $stCodOrgaoInicial;
/**
    * @var String
    * @access Private
*/
var $stCodOrgaoFinal;
/**
    * @var String
    * @access Private
*/
var $stCodUnidadeInicial;
/**
    * @var String
    * @access Private
*/
var $stCodUnidadeFinal;
/**
    * @var Integer
    * @access Private
*/
var $inCodFuncao;
/**
    * @var Integer
    * @access Private
*/
var $inCodSubFuncao;
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
var $stDataInicial;
/**
    * @var String
    * @access Private
*/
var $stDataFinal;
var $stDestinacaoRecurso;
var $inCodDetalhamento;

/**
     * @access Public
     * @param Object $valor
*/
function setFConsolidadoElemDesp($valor) { $this->obFConsolidadoElemDesp        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setREntidade($valor) { $this->obREntidade              = $valor; }
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
function setCodOrgaoInicial($valor) { $this->stCodOrgaoInicial      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodOrgaoFinal($valor) { $this->stCodOrgaoFinal      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodUnidadeInicial($valor) { $this->stCodUnidadeInicial      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodUnidadeFinal($valor) { $this->stCodUnidadeFinal      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodFuncao($valor) { $this->inCodFuncao = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodSubFuncao($valor) { $this->inCodSubFuncao = $valor; }
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
     * @param Object $valor
*/
function setDataInicial($valor) { $this->stDataInicial              = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinal($valor) { $this->stDataFinal               = $valor; }

function setDestinacaoRecurso($valor) { $this->stDestinacaoRecurso = $valor; }
function setCodDetalhamento($valor) { $this->inCodDetalhamento = $valor; }

/**
     * @access Public
     * @return Object
*/
function getFConsolidadoElemDesp() { return $this->obFConsolidadoElemDesp;            }
/**
     * @access Public
     * @param Object $valor
*/
function getREntidade() { return $this->obREntidade;                  }
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
     * @return Object
*/
function getCodOrgaoInicial() { return $this->stCodOrgaoInicial;                }
/**
     * @access Public
     * @return Object
*/
function getCodOrgaoFinal() { return $this->stCodOrgaoFinal;                }
/**
     * @access Public
     * @return Object
*/
function getCodUnidadeInicial() { return $this->stCodUnidadeInicial;                }
/**
     * @access Public
     * @return Object
*/
function getCodUnidadeFinal() { return $this->stCodUnidadeFinal;                }
/**
     * @access Public
     * @return Integer
*/
function getCodFuncao() { return $this->inCodFuncao; }
/**
     * @access Public
     * @return Integer
*/
function getCodSubFuncao() { return $this->inCodSubFuncao; }
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
     * @param Object $valor
*/
function getDataInicial() { return $this->stDataInicial;              }
/**
     * @access Public
     * @param Object $valor
*/
function getDataFinal() { return $this->stDataFinal;                }
/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioConsolidadoElemDesp()
{
    $this->setFConsolidadoElemDesp       ( new FOrcamentoConsolidadoElemDesp );
    $this->setREntidade                  ( new ROrcamentoEntidade       );
    $this->obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm')              );
    $this->setRConfiguracaoOrcamento     ( new ROrcamentoConfiguracao  );
    $this->obROrcamentoUnidadeOrcamentaria       = new ROrcamentoUnidadeOrcamentaria;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSetRelatorio(&$rsRecordSet , $stOrder = "", $inTipo)
{
    $stFiltro = "";
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

    $this->obFConsolidadoElemDesp->setDado("exercicio",$this->getExercicio());
    $this->obFConsolidadoElemDesp->setDado("stFiltro",$this->getFiltro());
    $this->obFConsolidadoElemDesp->setDado("stEntidade",$this->getCodEntidade());
    $this->obFConsolidadoElemDesp->setDado("stCodOrgaoInicial",$this->getCodOrgaoInicial());
    $this->obFConsolidadoElemDesp->setDado("stCodOrgaoFinal",$this->getCodOrgaoFinal());
    $this->obFConsolidadoElemDesp->setDado("stCodUnidadeInicial",$this->getCodUnidadeInicial());
    $this->obFConsolidadoElemDesp->setDado("stCodUnidadeFinal",$this->getCodUnidadeFinal());
    $this->obFConsolidadoElemDesp->setDado("inCodFuncao", $this->getCodFuncao() ? $this->getCodFuncao() : 0);
    $this->obFConsolidadoElemDesp->setDado("inCodSubFuncao",$this->getCodSubFuncao() ? $this->getCodSubFuncao() : 0);
    $this->obFConsolidadoElemDesp->setDado("stDataInicial",$this->getDataInicial());
    $this->obFConsolidadoElemDesp->setDado("stDataFinal",$this->getDataFinal());
    $this->obFConsolidadoElemDesp->setDado("stDestinacaoRecurso",$this->stDestinacaoRecurso);
    $this->obFConsolidadoElemDesp->setDado("inCodDetalhamento",$this->inCodDetalhamento );
    $stOrder = "";
    if ($this->getCodOrgaoInicial() || $this->getCodOrgaoFinal()) {
        $stOrder .= "num_orgao";
        if ($this->getCodUnidadeInicial() || $this->getCodUnidadeFinal()) {
            $stOrder .= ", num_unidade";
        }
        $stOrder .= ", classificacao";
    } else {
        $stOrder .= "classificacao";
    }
    if($inTipo==1)
        $obErro = $this->obFConsolidadoElemDesp->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );
    else
        $obErro = $this->obFConsolidadoElemDesp->recuperaTodosSinteticos( $rsRecordSet, $stFiltro, $stOrder );

    $inCount            = 0;
    $inTotalOrgao       = 0;
    $inTotalUnidade     = 0;
    $inTotalGeral       = 0;
    $arRecord           = array();
    $orgaoAtual         = "";
    $mostra             = false;
    $mostraUnidade      = false;
    $boSoma             = true;

    while ( !$rsRecordSet->eof() ) {
        if ($rsRecordSet->getCampo('saldo_inicial')>0 OR $rsRecordSet->getCampo('empenhado_ano')>0 OR $rsRecordSet->getCampo( 'suplementacoes' ) > 0 OR $rsRecordSet->getCampo('reducoes')) {
        if ($inCount==0) {
            $ultimo = $rsRecordSet->getCampo('cod_reduzido');
            $tam = strlen($ultimo);
        } else {
            if (substr($rsRecordSet->getCampo('cod_reduzido'),0,$tam) == $ultimo) {
                $boSoma = false;
            } else {
                $boSoma = true;
                $ultimo = $rsRecordSet->getCampo('cod_reduzido');
                $tam = strlen($ultimo);
            }
        }

        if ( (($rsRecordSet->getCampo('saldo_inicial')<>"0,00") AND ($rsRecordSet->getCampo('tipo_conta')=="M" or $rsRecordSet->getCampo('tipo_conta')=="D")) OR ($rsRecordSet->getCampo('empenhado_ano')<>"0,00") OR ($rsRecordSet->getCampo('suplementacoes')<>"0,00") OR ($rsRecordSet->getCampo('reducoes')<>"0,00") ) {
        $orgao      = $rsRecordSet->getCampo('num_orgao');
        $unidade    = $rsRecordSet->getCampo('num_unidade');

        if ($orgao > 0 and $inCount==0) {
            //MONTA LINHA DO ORGÃO
            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = "ORGÃO";
            $arRecord[$inCount]['descricao_despesa'] = $rsRecordSet->getCampo('num_orgao') . " - " . $rsRecordSet->getCampo('nom_orgao');
            $arRecord[$inCount]['coluna3']     = '';
            $arRecord[$inCount]['coluna4']     = '';
            $arRecord[$inCount]['coluna5']     = '';
            $arRecord[$inCount]['coluna6']     = '';
            $arRecord[$inCount]['coluna7']     = '';

            if($inCount == 0)
                $orgaoAtual = $orgao;

            $inCount++;

            if ($unidade > 0) {
                //MONTA LINHA DA UNIDADE
                $arRecord[$inCount]['nivel']             = 1;
                $arRecord[$inCount]['classificacao']     = "UNIDADE";
                $arRecord[$inCount]['descricao_despesa'] = $rsRecordSet->getCampo('num_unidade') . " - " . $rsRecordSet->getCampo('nom_unidade');
                $arRecord[$inCount]['coluna3']     = '';
                $arRecord[$inCount]['coluna4']     = '';
                $arRecord[$inCount]['coluna5']     = '';
                $arRecord[$inCount]['coluna6']     = '';
                $arRecord[$inCount]['coluna7']     = '';

                $unidadeAtual = $unidade;

                $inCount++;
            }

            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = "";
            $arRecord[$inCount]['descricao_despesa'] = "";
            $arRecord[$inCount]['coluna3']           = '';
            $arRecord[$inCount]['coluna4']           = '';
            $arRecord[$inCount]['coluna5']           = '';
            $arRecord[$inCount]['coluna6']           = '';
            $arRecord[$inCount]['coluna7']           = '';
            $inCount++;
        }

        if ($orgao > 0) {
            if ($unidade > 0) {
                if ($unidadeAtual <> $unidade or $orgaoAtual <> $orgao) {
                    $unidadeAtual = $unidade;

                    //MONTA TOTALIZADOR GERAL
                    $arRecord[$inCount]['nivel']             = 1;
                    $arRecord[$inCount]['classificacao']     = "";
                    $arRecord[$inCount]['descricao_despesa'] = "";
                    $arRecord[$inCount]['coluna3']           = "";
                    $arRecord[$inCount]['coluna4']           = "";
                    $arRecord[$inCount]['coluna5']           = "";
                    $arRecord[$inCount]['coluna6']           = "";
                    $arRecord[$inCount]['coluna7']           = "";
                    $inCount++;

                    $arRecord[$inCount]['nivel']             = 1;
                    $arRecord[$inCount]['classificacao']     = "TOTAL UNIDADE";
                    $arRecord[$inCount]['descricao_despesa'] = "";
                    $arRecord[$inCount]['coluna3']           = number_format( $inUnidadeSaldoInicial, 2, ',', '.' );
                    $arRecord[$inCount]['coluna4']           = number_format( $inUnidadeSuplementacoes, 2, ',', '.' );
                    $arRecord[$inCount]['coluna5']           = number_format( $inUnidadeReducoes, 2, ',', '.' );
                    $arRecord[$inCount]['coluna6']           = number_format( $inUnidadeTotalCredito, 2, ',', '.' );
                    $arRecord[$inCount]['coluna7']           = number_format( $inUnidadeSaldoDisponivel, 2, ',', '.' );
                    $inCount++;

                    $arRecord[$inCount]['nivel']             = 1;
                    $arRecord[$inCount]['classificacao']     = "";
                    $arRecord[$inCount]['descricao_despesa'] = "";
                    $arRecord[$inCount]['coluna3']           = number_format( $inUnidadeEmpenhadoMes, 2, ',', '.' );
                    $arRecord[$inCount]['coluna4']           = number_format( $inUnidadeAnuladoMes, 2, ',', '.' );
                    $arRecord[$inCount]['coluna5']           = number_format( $inUnidadeLiquidadoMes, 2, ',', '.' );
                    $arRecord[$inCount]['coluna6']           = number_format( $inUnidadePagoMes, 2, ',', '.' );
                    $arRecord[$inCount]['coluna7']           = number_format( $inUnidadeLiquidar, 2, ',', '.' );
                    $inCount++;

                    $arRecord[$inCount]['nivel']             = 1;
                    $arRecord[$inCount]['classificacao']     = "";
                    $arRecord[$inCount]['descricao_despesa'] = "";
                    $arRecord[$inCount]['coluna3']           = number_format( $inUnidadeEmpenhadoAno, 2, ',', '.' );
                    $arRecord[$inCount]['coluna4']           = number_format( $inUnidadeAnuladoAno, 2, ',', '.' );
                    $arRecord[$inCount]['coluna5']           = number_format( $inUnidadeLiquidadoAno, 2, ',', '.' );
                    $arRecord[$inCount]['coluna6']           = number_format( $inUnidadePagoAno, 2, ',', '.' );
                    $arRecord[$inCount]['coluna7']           = number_format( $inUnidadePagarLiquidado, 2, ',', '.' );
                    $inCount++;

                    $inUnidadeSaldoInicial      = 0;
                    $inUnidadeSaldoInicial      = 0;
                    $inUnidadeSuplementacoes    = 0;
                    $inUnidadeReducoes          = 0;
                    $inUnidadeTotalCredito      = 0;
                    $inUnidadeSaldoDisponivel   = 0;
                    $inUnidadeEmpenhadoMes      = 0;
                    $inUnidadeAnuladoMes        = 0;
                    $inUnidadeLiquidadoMes      = 0;
                    $inUnidadePagoMes           = 0;
                    $inUnidadeLiquidar          = 0;
                    $inUnidadeEmpenhadoAno      = 0;
                    $inUnidadeAnuladoAno        = 0;
                    $inUnidadeLiquidadoAno      = 0;
                    $inUnidadePagoAno           = 0;
                    $inUnidadePagarLiquidado    = 0;

                    $mostraUnidade = true;
                }
            }

            if ($orgaoAtual <> $orgao) {
                $orgaoAtual = $orgao;

                //MONTA TOTALIZADOR GERAL
                $arRecord[$inCount]['nivel']             = 1;
                $arRecord[$inCount]['classificacao']     = "";
                $arRecord[$inCount]['descricao_despesa'] = "";
                $arRecord[$inCount]['coluna3']           = "";
                $arRecord[$inCount]['coluna4']           = "";
                $arRecord[$inCount]['coluna5']           = "";
                $arRecord[$inCount]['coluna6']           = "";
                $arRecord[$inCount]['coluna7']           = "";
                $inCount++;

                $arRecord[$inCount]['nivel']             = 1;
                $arRecord[$inCount]['classificacao']     = "TOTAL ORGÃO";
                $arRecord[$inCount]['descricao_despesa'] = "";
                $arRecord[$inCount]['coluna3']           = number_format( $inOrgaoSaldoInicial, 2, ',', '.' );
                $arRecord[$inCount]['coluna4']           = number_format( $inOrgaoSuplementacoes, 2, ',', '.' );
                $arRecord[$inCount]['coluna5']           = number_format( $inOrgaoReducoes, 2, ',', '.' );
                $arRecord[$inCount]['coluna6']           = number_format( $inOrgaoTotalCredito, 2, ',', '.' );
                $arRecord[$inCount]['coluna7']           = number_format( $inOrgaoSaldoDisponivel, 2, ',', '.' );
                $inCount++;

                $arRecord[$inCount]['nivel']             = 1;
                $arRecord[$inCount]['classificacao']     = "";
                $arRecord[$inCount]['descricao_despesa'] = "";
                $arRecord[$inCount]['coluna3']           = number_format( $inOrgaoEmpenhadoMes, 2, ',', '.' );
                $arRecord[$inCount]['coluna4']           = number_format( $inOrgaoAnuladoMes, 2, ',', '.' );
                $arRecord[$inCount]['coluna5']           = number_format( $inOrgaoLiquidadoMes, 2, ',', '.' );
                $arRecord[$inCount]['coluna6']           = number_format( $inOrgaoPagoMes, 2, ',', '.' );
                $arRecord[$inCount]['coluna7']           = number_format( $inOrgaoLiquidar, 2, ',', '.' );
                $inCount++;

                $arRecord[$inCount]['nivel']             = 1;
                $arRecord[$inCount]['classificacao']     = "";
                $arRecord[$inCount]['descricao_despesa'] = "";
                $arRecord[$inCount]['coluna3']           = number_format( $inOrgaoEmpenhadoAno, 2, ',', '.' );
                $arRecord[$inCount]['coluna4']           = number_format( $inOrgaoAnuladoAno, 2, ',', '.' );
                $arRecord[$inCount]['coluna5']           = number_format( $inOrgaoLiquidadoAno, 2, ',', '.' );
                $arRecord[$inCount]['coluna6']           = number_format( $inOrgaoPagoAno, 2, ',', '.' );
                $arRecord[$inCount]['coluna7']           = number_format( $inOrgaoPagarLiquidado, 2, ',', '.' );
                $inCount++;

                $inOrgaoSaldoInicial      = 0;
                $inOrgaoSaldoInicial      = 0;
                $inOrgaoSuplementacoes    = 0;
                $inOrgaoReducoes          = 0;
                $inOrgaoTotalCredito      = 0;
                $inOrgaoSaldoDisponivel   = 0;
                $inOrgaoEmpenhadoMes      = 0;
                $inOrgaoAnuladoMes        = 0;
                $inOrgaoLiquidadoMes      = 0;
                $inOrgaoPagoMes           = 0;
                $inOrgaoLiquidar          = 0;
                $inOrgaoEmpenhadoAno      = 0;
                $inOrgaoAnuladoAno        = 0;
                $inOrgaoLiquidadoAno      = 0;
                $inOrgaoPagoAno           = 0;
                $inOrgaoPagarLiquidado    = 0;

                $mostra = true;
                $unidadeAtual = "";
            }

            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = "";
            $arRecord[$inCount]['descricao_despesa'] = "";
            $arRecord[$inCount]['coluna3']           = '';
            $arRecord[$inCount]['coluna4']           = '';
            $arRecord[$inCount]['coluna5']           = '';
            $arRecord[$inCount]['coluna6']           = '';
            $arRecord[$inCount]['coluna7']           = '';

            $inCount++;

            if ($mostra) {
                //MONTA LINHA DO ORGÃO
                $arRecord[$inCount]['nivel']             = 1;
                $arRecord[$inCount]['classificacao']     = "ORGÃO";
                $arRecord[$inCount]['descricao_despesa'] = $rsRecordSet->getCampo('num_orgao'). " - " . $rsRecordSet->getCampo('nom_orgao');
                $arRecord[$inCount]['coluna3']     = '';
                $arRecord[$inCount]['coluna4']     = '';
                $arRecord[$inCount]['coluna5']     = '';
                $arRecord[$inCount]['coluna6']     = '';
                $arRecord[$inCount]['coluna7']     = '';
                $orgaoAtual = $orgao;
                $inCount++;
            }

            if ($unidade > 0) {
                if ($mostraUnidade) {
                    //MONTA LINHA DA UNIDADE
                    $arRecord[$inCount]['nivel']             = 1;
                    $arRecord[$inCount]['classificacao']     = "UNIDADE";
                    $arRecord[$inCount]['descricao_despesa'] = $rsRecordSet->getCampo('num_unidade') . " - " . $rsRecordSet->getCampo('nom_unidade');
                    $arRecord[$inCount]['coluna3']     = '';
                    $arRecord[$inCount]['coluna4']     = '';
                    $arRecord[$inCount]['coluna5']     = '';
                    $arRecord[$inCount]['coluna6']     = '';
                    $arRecord[$inCount]['coluna7']     = '';
                    $unidadeAtual = $unidade;
                    $inCount++;
                }
            }

            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = "";
            $arRecord[$inCount]['descricao_despesa'] = "";
            $arRecord[$inCount]['coluna3']           = '';
            $arRecord[$inCount]['coluna4']           = '';
            $arRecord[$inCount]['coluna5']           = '';
            $arRecord[$inCount]['coluna6']           = '';
            $arRecord[$inCount]['coluna7']           = '';

            $inCount++;
        }

        $stClassificacao = ucwords( strtolower( $rsRecordSet->getCampo('descricao' ) ) );

        //MONTA LINHA DA DESPESA SEM VALORES
        $arRecord[$inCount]['nivel']             = 1;
        $arRecord[$inCount]['classificacao']     = $rsRecordSet->getCampo('classificacao');
        $arRecord[$inCount]['descricao_despesa'] = $stClassificacao;
        $arRecord[$inCount]['coluna3']     = '';
        $arRecord[$inCount]['coluna4']     = '';
        $arRecord[$inCount]['coluna5']     = '';
        $arRecord[$inCount]['coluna6']     = '';
        $arRecord[$inCount]['coluna7']     = '';
        $inCount++;

        //MONTA LINHA DA DESPESA SALDO_INICIAL, SUPLEMENTAÇÕES, REDUÇÕES, TOTAL_CREDITOS, SALDO_DISPONÍVEL

/*        if (strlen($rsRecordSet->getCampo('cod_reduzido')) == "3") {
            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = '';
            $arRecord[$inCount]['descricao_despesa'] = '';
            $arRecord[$inCount]['coluna3']           = number_format($rsRecordSet->getCampo('saldo_inicial'), 2, ',', '.');
            $arRecord[$inCount]['coluna4']           = number_format($rsRecordSet->getCampo('suplementacoes'), 2, ',', '.');
            $arRecord[$inCount]['coluna5']           = number_format($rsRecordSet->getCampo('reducoes'), 2, ',', '.');
            if ( $rsRecordSet->getCampo('saldo_inicial') == '0.00' ) {
                $arRecord[$inCount]['coluna6']       = '0.00';
                $arRecord[$inCount]['coluna7']       = '0.00';
            } else {
                $totalCredito = $rsRecordSet->getCampo('saldo_inicial') + $rsRecordSet->getCampo('suplementacoes') - $rsRecordSet->getCampo('reducoes');
                $arRecord[$inCount]['coluna6']       = number_format($totalCredito, 2, ',', '.');
//              $arRecord[$inCount]['coluna7']       = number_format($totalCredito - $rsRecordSet->getCampo('empenhado_mes') + $rsRecordSet->getCampo('anulado_mes'), 2, ',', '.');
                $arRecord[$inCount]['coluna7']       = number_format($totalCredito - $rsRecordSet->getCampo('empenhado_ano') + $rsRecordSet->getCampo('anulado_ano'), 2, ',', '.');
            }
          } else*/if ($rsRecordSet->getCampo('tipo_conta')=="M" or $rsRecordSet->getCampo('tipo_conta')=="D") {
            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = '';
            $arRecord[$inCount]['descricao_despesa'] = '';
            $arRecord[$inCount]['coluna3']           = number_format($rsRecordSet->getCampo('saldo_inicial'), 2, ',', '.');
            $arRecord[$inCount]['coluna4']           = number_format($rsRecordSet->getCampo('suplementacoes'), 2, ',', '.');
            $arRecord[$inCount]['coluna5']           = number_format($rsRecordSet->getCampo('reducoes'), 2, ',', '.');
/*            if ( $rsRecordSet->getCampo('saldo_inicial') == '0.00' ) {
                $arRecord[$inCount]['coluna6']       = '0.00';
                $arRecord[$inCount]['coluna7']       = '0.00';
            } else {*/
            $totalCredito = $rsRecordSet->getCampo('saldo_inicial') + $rsRecordSet->getCampo('suplementacoes') - $rsRecordSet->getCampo('reducoes');
            $arRecord[$inCount]['coluna6']       = number_format($totalCredito, 2, ',', '.');
//          $arRecord[$inCount]['coluna7']       = number_format($totalCredito - $rsRecordSet->getCampo('empenhado_mes') + $rsRecordSet->getCampo('anulado_mes'), 2, ',', '.');
            $arRecord[$inCount]['coluna7']       = number_format($totalCredito - $rsRecordSet->getCampo('empenhado_ano') + $rsRecordSet->getCampo('anulado_ano'), 2, ',', '.');
//          }

            $inUnidadeSaldoInicial      += $rsRecordSet->getCampo('saldo_inicial');
            $inUnidadeSuplementacoes    += $rsRecordSet->getCampo('suplementacoes');
            $inUnidadeReducoes          += $rsRecordSet->getCampo('reducoes');
            $inUnidadeTotalCredito      += $totalCredito;
            $inUnidadeSaldoDisponivel   += $totalCredito - $rsRecordSet->getCampo('empenhado_ano') + $rsRecordSet->getCampo('anulado_ano');
//          $inUnidadeSaldoDisponivel   += $totalCredito - $rsRecordSet->getCampo('empenhado_mes') + $rsRecordSet->getCampo('anulado_mes');

            $inOrgaoSaldoInicial      += $rsRecordSet->getCampo('saldo_inicial');
            $inOrgaoSuplementacoes    += $rsRecordSet->getCampo('suplementacoes');
            $inOrgaoReducoes          += $rsRecordSet->getCampo('reducoes');
            $inOrgaoTotalCredito      += $totalCredito;
            $inOrgaoSaldoDisponivel   += $totalCredito - $rsRecordSet->getCampo('empenhado_ano') + $rsRecordSet->getCampo('anulado_ano');
//          $inOrgaoSaldoDisponivel   += $totalCredito - $rsRecordSet->getCampo('empenhado_mes') + $rsRecordSet->getCampo('anulado_mes');

            $inTotalSaldoInicial       = bcadd( $inTotalSaldoInicial   , $rsRecordSet->getCampo('saldo_inicial') , 4 );
            $inTotalSuplementacoes     = bcadd( $inTotalSuplementacoes , $rsRecordSet->getCampo('suplementacoes'), 4 );
            $inTotalReducoes           = bcadd( $inTotalReducoes       , $rsRecordSet->getCampo('reducoes')      , 4 );
            $inTotalTotalCredito       = bcadd( $inTotalTotalCredito   , $totalCredito                           , 4 );
            $inTotalSaldoDisponivel   += $totalCredito - $rsRecordSet->getCampo('empenhado_ano') + $rsRecordSet->getCampo('anulado_ano');
//          $inTotalSaldoDisponivel   += $totalCredito - $rsRecordSet->getCampo('empenhado_mes') + $rsRecordSet->getCampo('anulado_mes');
        } else {
            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = '';
            $arRecord[$inCount]['descricao_despesa'] = '';

            if ($rsRecordSet->getCampo('nivel') < 4) {
                $arRecord[$inCount]['coluna3']       = number_format($rsRecordSet->getCampo('saldo_inicial'), 2, ',', '.');
                $arRecord[$inCount]['coluna4']       = number_format($rsRecordSet->getCampo('suplementacoes'), 2, ',', '.');
                $arRecord[$inCount]['coluna5']       = number_format($rsRecordSet->getCampo('reducoes'), 2, ',', '.');
                $totalCredito = $rsRecordSet->getCampo('saldo_inicial') + $rsRecordSet->getCampo('suplementacoes') - $rsRecordSet->getCampo('reducoes');
                $arRecord[$inCount]['coluna6']       = number_format($totalCredito, 2, ',', '.');
                $arRecord[$inCount]['coluna7']       = number_format($totalCredito - $rsRecordSet->getCampo('empenhado_ano') + $rsRecordSet->getCampo('anulado_ano'), 2, ',', '.');
            } else {
                $arRecord[$inCount]['coluna3']       = '0,00';
                $arRecord[$inCount]['coluna4']       = '0,00';
                $arRecord[$inCount]['coluna5']       = '0,00';
                $arRecord[$inCount]['coluna6']       = '0,00';
                $arRecord[$inCount]['coluna7']       = '0,00';
            }
        }

        $inCount++;

        //MONTA LINHA DA DESPESA EMPENHADO_MES,ANULADO_MES,LIQUIDADO_MES,PAGO_MES,A LIQUIDAR
        $arRecord[$inCount]['nivel']             = 1;
        $arRecord[$inCount]['classificacao']     = '';
        $arRecord[$inCount]['descricao_despesa'] = '';
        $arRecord[$inCount]['coluna3']           = number_format($rsRecordSet->getCampo('empenhado_mes'), 2, ',', '.');
        $arRecord[$inCount]['coluna4']           = number_format($rsRecordSet->getCampo('anulado_mes'), 2, ',', '.');
        $arRecord[$inCount]['coluna5']           = number_format($rsRecordSet->getCampo('liquidado_mes'), 2, ',', '.');
        $arRecord[$inCount]['coluna6']           = number_format($rsRecordSet->getCampo('pago_mes'), 2, ',', '.');
        $arRecord[$inCount]['coluna7']           = number_format($rsRecordSet->getCampo('empenhado_ano') - $rsRecordSet->getCampo('anulado_ano') - $rsRecordSet->getCampo('liquidado_ano') , 2, ',', '.');
//      $arRecord[$inCount]['coluna7']           = number_format($rsRecordSet->getCampo('empenhado_mes') - $rsRecordSet->getCampo('anulado_mes') - $rsRecordSet->getCampo('liquidado_mes') , 2, ',', '.');

//        if ($rsRecordSet->getCampo('tipo_conta')=="D" or $rsRecordSet->getCampo('tipo_conta')=="F") {
        if ($boSoma) {
            $inUnidadeEmpenhadoMes      += $rsRecordSet->getCampo('empenhado_mes');
            $inUnidadeAnuladoMes        += $rsRecordSet->getCampo('anulado_mes');
            $inUnidadeLiquidadoMes      += $rsRecordSet->getCampo('liquidado_mes');
            $inUnidadePagoMes           += $rsRecordSet->getCampo('pago_mes');
            $inUnidadeLiquidar          += $rsRecordSet->getCampo('empenhado_ano') - $rsRecordSet->getCampo('anulado_ano') - $rsRecordSet->getCampo('liquidado_ano');
//          $inUnidadeLiquidar          += $rsRecordSet->getCampo('empenhado_mes') - $rsRecordSet->getCampo('anulado_mes') - $rsRecordSet->getCampo('liquidado_mes');

            $inOrgaoEmpenhadoMes      += $rsRecordSet->getCampo('empenhado_mes');
            $inOrgaoAnuladoMes        += $rsRecordSet->getCampo('anulado_mes');
            $inOrgaoLiquidadoMes      += $rsRecordSet->getCampo('liquidado_mes');
            $inOrgaoPagoMes           += $rsRecordSet->getCampo('pago_mes');
            $inOrgaoLiquidar          += $rsRecordSet->getCampo('empenhado_ano') - $rsRecordSet->getCampo('anulado_ano') - $rsRecordSet->getCampo('liquidado_ano');
//          $inOrgaoLiquidar          += $rsRecordSet->getCampo('empenhado_mes') - $rsRecordSet->getCampo('anulado_mes') - $rsRecordSet->getCampo('liquidado_mes');

            $inTotalEmpenhadoMes      += $rsRecordSet->getCampo('empenhado_mes');
            $inTotalAnuladoMes        += $rsRecordSet->getCampo('anulado_mes');
            $inTotalLiquidadoMes      += $rsRecordSet->getCampo('liquidado_mes');
            $inTotalPagoMes           += $rsRecordSet->getCampo('pago_mes');
//          $inTotalLiquidar          += $rsRecordSet->getCampo('empenhado_mes') - $rsRecordSet->getCampo('anulado_mes') - $rsRecordSet->getCampo('liquidado_mes');
            $inTotalLiquidar          += $rsRecordSet->getCampo('empenhado_ano') - $rsRecordSet->getCampo('anulado_ano') - $rsRecordSet->getCampo('liquidado_ano');
        }

        $inCount++;

        //MONTA LINHA DA DESPESA EMPENHADO_ANO,ANULADO_ANO,LIQUIDADO_ANO,PAGO_ANO,A PAGAR LIQUIDADO
        $arRecord[$inCount]['nivel']             = 1;
        $arRecord[$inCount]['classificacao']     = '';
        $arRecord[$inCount]['descricao_despesa'] = '';
        $arRecord[$inCount]['coluna3']           = number_format($rsRecordSet->getCampo('empenhado_ano')  , 2, ',', '.' );
        $arRecord[$inCount]['coluna4']           = number_format($rsRecordSet->getCampo('anulado_ano'), 2, ',', '.');
        $arRecord[$inCount]['coluna5']           = number_format($rsRecordSet->getCampo('liquidado_ano'), 2, ',', '.');
        $arRecord[$inCount]['coluna6']           = number_format($rsRecordSet->getCampo('pago_ano'), 2, ',', '.');
//      $arRecord[$inCount]['coluna7']           = number_format($rsRecordSet->getCampo('liquidado_mes') - $rsRecordSet->getCampo('pago_mes') , 2, ',', '.');
        $arRecord[$inCount]['coluna7']           = number_format($rsRecordSet->getCampo('liquidado_ano') - $rsRecordSet->getCampo('pago_ano') , 2, ',', '.');

//        if ($rsRecordSet->getCampo('tipo_conta')=="D" or $rsRecordSet->getCampo('tipo_conta')=="F") {
//        if ($rsRecordSet->getCampo('tipo_soma')=="P") {
        if ($boSoma) {
            $inUnidadeEmpenhadoAno      += $rsRecordSet->getCampo('empenhado_ano');
            $inUnidadeAnuladoAno        += $rsRecordSet->getCampo('anulado_ano');
            $inUnidadeLiquidadoAno      += $rsRecordSet->getCampo('liquidado_ano');
            $inUnidadePagoAno           += $rsRecordSet->getCampo('pago_ano');
//          $inUnidadePagarLiquidado    += $rsRecordSet->getCampo('liquidado_mes') - $rsRecordSet->getCampo('pago_mes');
            $inUnidadePagarLiquidado    += $rsRecordSet->getCampo('liquidado_ano') - $rsRecordSet->getCampo('pago_ano');

            $inOrgaoEmpenhadoAno      += $rsRecordSet->getCampo('empenhado_ano');
            $inOrgaoAnuladoAno        += $rsRecordSet->getCampo('anulado_ano');
            $inOrgaoLiquidadoAno      += $rsRecordSet->getCampo('liquidado_ano');
            $inOrgaoPagoAno           += $rsRecordSet->getCampo('pago_ano');
//          $inOrgaoPagarLiquidado    += $rsRecordSet->getCampo('liquidado_mes') - $rsRecordSet->getCampo('pago_mes');
            $inOrgaoPagarLiquidado    += $rsRecordSet->getCampo('liquidado_ano') - $rsRecordSet->getCampo('pago_ano');

            $inTotalEmpenhadoAno      += $rsRecordSet->getCampo('empenhado_ano');
            $inTotalAnuladoAno        += $rsRecordSet->getCampo('anulado_ano');
            $inTotalLiquidadoAno      += $rsRecordSet->getCampo('liquidado_ano');
            $inTotalPagoAno           += $rsRecordSet->getCampo('pago_ano');
//          $inTotalPagarLiquidado    += $rsRecordSet->getCampo('liquidado_mes') - $rsRecordSet->getCampo('pago_mes');
            $inTotalPagarLiquidado    += $rsRecordSet->getCampo('liquidado_ano') - $rsRecordSet->getCampo('pago_ano');
        }

        $inCount++;

        $mostra = false;
        $mostraUnidade = false;
        }
        }
        $rsRecordSet->proximo();
    }

    if ($inCount) {
        if ($orgao > 0) {
            if ($unidade > 0) {
                //MONTA TOTALIZADOR GERAL
                $arRecord[$inCount]['nivel']             = 1;
                $arRecord[$inCount]['classificacao']     = "";
                $arRecord[$inCount]['descricao_despesa'] = "";
                $arRecord[$inCount]['coluna3']           = "";
                $arRecord[$inCount]['coluna4']           = "";
                $arRecord[$inCount]['coluna5']           = "";
                $arRecord[$inCount]['coluna6']           = "";
                $arRecord[$inCount]['coluna7']           = "";
                $inCount++;

                $arRecord[$inCount]['nivel']             = 1;
                $arRecord[$inCount]['classificacao']     = "TOTAL UNIDADE";
                $arRecord[$inCount]['descricao_despesa'] = "";
                $arRecord[$inCount]['coluna3']           = number_format( $inUnidadeSaldoInicial, 2, ',', '.' );
                $arRecord[$inCount]['coluna4']           = number_format( $inUnidadeSuplementacoes, 2, ',', '.' );
                $arRecord[$inCount]['coluna5']           = number_format( $inUnidadeReducoes, 2, ',', '.' );
                $arRecord[$inCount]['coluna6']           = number_format( $inUnidadeTotalCredito, 2, ',', '.' );
                $arRecord[$inCount]['coluna7']           = number_format( $inUnidadeSaldoDisponivel, 2, ',', '.' );
                $inCount++;

                $arRecord[$inCount]['nivel']             = 1;
                $arRecord[$inCount]['classificacao']     = "";
                $arRecord[$inCount]['descricao_despesa'] = "";
                $arRecord[$inCount]['coluna3']           = number_format( $inUnidadeEmpenhadoMes, 2, ',', '.' );
                $arRecord[$inCount]['coluna4']           = number_format( $inUnidadeAnuladoMes, 2, ',', '.' );
                $arRecord[$inCount]['coluna5']           = number_format( $inUnidadeLiquidadoMes, 2, ',', '.' );
                $arRecord[$inCount]['coluna6']           = number_format( $inUnidadePagoMes, 2, ',', '.' );
                $arRecord[$inCount]['coluna7']           = number_format( $inUnidadeLiquidar, 2, ',', '.' );
                $inCount++;

                $arRecord[$inCount]['nivel']             = 1;
                $arRecord[$inCount]['classificacao']     = "";
                $arRecord[$inCount]['descricao_despesa'] = "";
                $arRecord[$inCount]['coluna3']           = number_format( $inUnidadeEmpenhadoAno, 2, ',', '.' );
                $arRecord[$inCount]['coluna4']           = number_format( $inUnidadeAnuladoAno, 2, ',', '.' );
                $arRecord[$inCount]['coluna5']           = number_format( $inUnidadeLiquidadoAno, 2, ',', '.' );
                $arRecord[$inCount]['coluna6']           = number_format( $inUnidadePagoAno, 2, ',', '.' );
                $arRecord[$inCount]['coluna7']           = number_format( $inUnidadePagarLiquidado, 2, ',', '.' );
                $inCount++;
            }

            //MONTA TOTALIZADOR GERAL
            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = "";
            $arRecord[$inCount]['descricao_despesa'] = "";
            $arRecord[$inCount]['coluna3']           = "";
            $arRecord[$inCount]['coluna4']           = "";
            $arRecord[$inCount]['coluna5']           = "";
            $arRecord[$inCount]['coluna6']           = "";
            $arRecord[$inCount]['coluna7']           = "";
            $inCount++;

            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = "TOTAL ORGÃO";
            $arRecord[$inCount]['descricao_despesa'] = "";
            $arRecord[$inCount]['coluna3']           = number_format( $inOrgaoSaldoInicial, 2, ',', '.' );
            $arRecord[$inCount]['coluna4']           = number_format( $inOrgaoSuplementacoes, 2, ',', '.' );
            $arRecord[$inCount]['coluna5']           = number_format( $inOrgaoReducoes, 2, ',', '.' );
            $arRecord[$inCount]['coluna6']           = number_format( $inOrgaoTotalCredito, 2, ',', '.' );
            $arRecord[$inCount]['coluna7']           = number_format( $inOrgaoSaldoDisponivel, 2, ',', '.' );
            $inCount++;

            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = "";
            $arRecord[$inCount]['descricao_despesa'] = "";
            $arRecord[$inCount]['coluna3']           = number_format( $inOrgaoEmpenhadoMes, 2, ',', '.' );
            $arRecord[$inCount]['coluna4']           = number_format( $inOrgaoAnuladoMes, 2, ',', '.' );
            $arRecord[$inCount]['coluna5']           = number_format( $inOrgaoLiquidadoMes, 2, ',', '.' );
            $arRecord[$inCount]['coluna6']           = number_format( $inOrgaoPagoMes, 2, ',', '.' );
            $arRecord[$inCount]['coluna7']           = number_format( $inOrgaoLiquidar, 2, ',', '.' );
            $inCount++;

            $arRecord[$inCount]['nivel']             = 1;
            $arRecord[$inCount]['classificacao']     = "";
            $arRecord[$inCount]['descricao_despesa'] = "";
            $arRecord[$inCount]['coluna3']           = number_format( $inOrgaoEmpenhadoAno, 2, ',', '.' );
            $arRecord[$inCount]['coluna4']           = number_format( $inOrgaoAnuladoAno, 2, ',', '.' );
            $arRecord[$inCount]['coluna5']           = number_format( $inOrgaoLiquidadoAno, 2, ',', '.' );
            $arRecord[$inCount]['coluna6']           = number_format( $inOrgaoPagoAno, 2, ',', '.' );
            $arRecord[$inCount]['coluna7']           = number_format( $inOrgaoPagarLiquidado, 2, ',', '.' );
            $inCount++;
        }

        //MONTA TOTALIZADOR GERAL
        $arRecord[$inCount]['nivel']             = 1;
        $arRecord[$inCount]['classificacao']     = "";
        $arRecord[$inCount]['descricao_despesa'] = "";
        $arRecord[$inCount]['coluna3']           = "";
        $arRecord[$inCount]['coluna4']           = "";
        $arRecord[$inCount]['coluna5']           = "";
        $arRecord[$inCount]['coluna6']           = "";
        $arRecord[$inCount]['coluna7']           = "";
        $inCount++;

        $arRecord[$inCount]['nivel']             = 1;
        $arRecord[$inCount]['classificacao']     = "TOTAL GERAL";
        $arRecord[$inCount]['descricao_despesa'] = "";
        $arRecord[$inCount]['coluna3']           = number_format( $inTotalSaldoInicial, 2, ',', '.' );
        $arRecord[$inCount]['coluna4']           = number_format( $inTotalSuplementacoes, 2, ',', '.' );
        $arRecord[$inCount]['coluna5']           = number_format( $inTotalReducoes, 2, ',', '.' );
        $arRecord[$inCount]['coluna6']           = number_format( $inTotalTotalCredito, 2, ',', '.' );
        $arRecord[$inCount]['coluna7']           = number_format( $inTotalSaldoDisponivel, 2, ',', '.' );
        $inCount++;

        $arRecord[$inCount]['nivel']             = 1;
        $arRecord[$inCount]['classificacao']     = "";
        $arRecord[$inCount]['descricao_despesa'] = "";
        $arRecord[$inCount]['coluna3']           = number_format( $inTotalEmpenhadoMes, 2, ',', '.' );
        $arRecord[$inCount]['coluna4']           = number_format( $inTotalAnuladoMes, 2, ',', '.' );
        $arRecord[$inCount]['coluna5']           = number_format( $inTotalLiquidadoMes, 2, ',', '.' );
        $arRecord[$inCount]['coluna6']           = number_format( $inTotalPagoMes, 2, ',', '.' );
        $arRecord[$inCount]['coluna7']           = number_format( $inTotalLiquidar, 2, ',', '.' );
        $inCount++;

        $arRecord[$inCount]['nivel']             = 1;
        $arRecord[$inCount]['classificacao']     = "";
        $arRecord[$inCount]['descricao_despesa'] = "";
        $arRecord[$inCount]['coluna3']           = number_format( $inTotalEmpenhadoAno, 2, ',', '.' );
        $arRecord[$inCount]['coluna4']           = number_format( $inTotalAnuladoAno, 2, ',', '.' );
        $arRecord[$inCount]['coluna5']           = number_format( $inTotalLiquidadoAno, 2, ',', '.' );
        $arRecord[$inCount]['coluna6']           = number_format( $inTotalPagoAno, 2, ',', '.' );
        $arRecord[$inCount]['coluna7']           = number_format( $inTotalPagarLiquidado, 2, ',', '.' );
        $inCount++;
    }

    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}
