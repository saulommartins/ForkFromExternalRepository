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
    * Classe de Regra da Função FN_ORCAMENTO_BALANCETE_DESPESA
    * Data de Criação   : 05/10/2004

    * @author Desenvolvedor: Vandre Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    * $Id: ROrcamentoRelatorioBalanceteDespesa.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE_RELATORIO;
include_once CAM_GF_ORC_MAPEAMENTO."FOrcamentoBalanceteDespesa.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php";

/**
    * Classe de Regra da Função FN_ORCAMENTO_RELACAO_DESPESA
    * @author Desenvolvedor: Vandré Miguel Ramos
*/
class ROrcamentoRelatorioBalanceteDespesa extends PersistenteRelatorio
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
var $obROrcamentoUnidade;

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

/**
    * @var String
    * @access Private
*/
var $stDemonstrarDesdobramentos;

/**
    * @var String
    * @access Private
*/
var $rsResumoRecurso;

/**
     * @access Public
     * @param Object $valor
*/
function setFBalanceteDespesa($valor) { $this->obFBalanceteDespesa      = $valor; }
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
function setCodEstruturalInicial($valor) { $this->stCodEstruturalInicial   = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEstruturalFinal($valor) { $this->stCodEstruturalFinal     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodReduzidoInicial($valor) { $this->stCodReduzidoInicial     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodReduzidoFinal($valor) { $this->stCodReduzidoFinal       = $valor; }
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
function setDataInicial($valor) { $this->stDataInicial            = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinal($valor) { $this->stDataFinal              = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setControleDetalhado($valor) { $this->stControleDetalhado      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setDemonstrarDesdobramentos($valor) { $this->stDemonstrarDesdobramentos = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setRsResumoRecurso($rsValor)
{
    $this->rsResumoRecurso = $rsValor;
}

/**
     * @access Public
     * @return Object
*/
function getFBalanceteDespesa() { return $this->obFBalanceteDespesa;            }
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
     * @access Public
     * @param Object $valor
*/
function getControleDetalhado() { return $this->stControleDetalhado;        }

/**
     * @access Public
     * @param Object $valor
*/
function getDemonstrarDesdobramentos() { return $this->stDemonstrarDesdobramentos;        }

/**
     * @access Public
     * @param Object $valor
*/
function getRsResumoRecurso()
{
    return $this->rsResumoRecurso;
}
/**
    * Método Construtor
    * @access Private
*/

function ROrcamentoRelatorioBalanceteDespesa()
{
    $this->setFBalanceteDespesa             ( new FOrcamentoBalanceteDespesa    );
    $this->setREntidade                     ( new ROrcamentoEntidade            );
    $this->setROrcamentoUnidade             ( new ROrcamentoUnidadeOrcamentaria );
    $this->setRConfiguracaoOrcamento        ( new ROrcamentoConfiguracao        );
    $this->obREntidade->obRCGM->setNumCGM   ( Sessao::read('numCgm')                   );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$arRegistros, &$arCabecalho, &$rsTotalFinal, $stOrder = "")
{
    $stFiltro = "";
    $this->obFBalanceteDespesa->setDado("exercicio",$this->getExercicio());
    $this->obFBalanceteDespesa->setDado("stFiltro",$this->getFiltro());
    $this->obFBalanceteDespesa->setDado("stEntidade",$this->getCodEntidade());
    $this->obFBalanceteDespesa->setDado("stCodEstruturalInicial",$this->getCodEstruturalInicial());
    $this->obFBalanceteDespesa->setDado("stCodEstruturalFinal",$this->getCodEstruturalFinal());
    $this->obFBalanceteDespesa->setDado("stCodReduzidoInicial",$this->getCodReduzidoInicial());
    $this->obFBalanceteDespesa->setDado("stCodReduzidoFinal",$this->getCodReduzidoFinal());
    $this->obFBalanceteDespesa->setDado("stDataInicial",$this->getDataInicial());
    $this->obFBalanceteDespesa->setDado("stDataFinal",$this->getDataFinal());
    $this->obFBalanceteDespesa->setDado("stControleDetalhado",$this->getControleDetalhado());
    $this->obFBalanceteDespesa->setDado("inNumOrgao", $this->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );
    $this->obFBalanceteDespesa->setDado("inNumUnidade", $this->obROrcamentoUnidade->getNumeroUnidade());
    //$stOrder = "num_orgao,num_unidade,cod_funcao,cod_subfuncao,cod_programa,num_pao,classificacao,cod_despesa";
    $stOrder = "";

    $obErro = $this->obFBalanceteDespesa->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    $inCount                 = 0;
    $arRecord                = array();
    $arTotalFinal            = array();
    $stDotacaoPrincipal      = "";
    $stDotacaoPrincipalAtiva = "";
    $inOrgao                 = "";
    $inOrgaoAtivo            = "";
    $stOrgaoUnidade          = "";
    $stOrgaoUnidadeAtivo     = "";
    $stPAO                   = "";
    $stPAOAtivo              = "";

    $inTotalOrgao               = 0;
    $inTotalUnidade             = 0;
    $inTotalGeral               = 0;
    $inTotalOrgaoDisponivel     = 0;
    $inTotalUnidadeDisponivel   = 0;
    $inTotalGeralDisponivel     = 0;

    $inSomatorioSaldoInicial           = 0;
    $inSomatorioTotalEmpenhadoPer      = 0;
    $inSomatorioTotalEmpenhadoAno      = 0;
    $inSomatorioTotalSuplementacoes    = 0;
    $inSomatorioTotalAnuladoPer        = 0;
    $inSomatorioTotalAnuladoAno        = 0;
    $inSomatorioTotalReducoes          = 0;
    $inSomatorioTotalLiquidadoPer      = 0;
    $inSomatorioTotalLiquidadoAno      = 0;
    $inSomatorioTotalCreditos          = 0;
    $inSomatorioTotalPagoPer           = 0;
    $inSomatorioTotalPagoAno           = 0;
    $inSomatorioTotalSaldoDisponivel   = 0;
    $inSomatorioTotalALiquidar         = 0;
    $inSomatorioTotalAPagarLiquidado   = 0;

    $inTotalSaldoInicial    = 0;
    $inTotalEmpenhadoPer    = 0;
    $inTotalEmpenhadoAno    = 0;
    $inTotalSuplementacoes  = 0;
    $inTotalAnuladoPer      = 0;
    $inTotalAnuladoAno      = 0;
    $inTotalReducoes        = 0;
    $inTotalLiquidadoPer    = 0;
    $inTotalLiquidadoAno    = 0;
    $inTotalCreditos        = 0;
    $inTotalPagoPer         = 0;
    $inTotalPagoAno         = 0;
    $inTotalSaldoDisponivel = 0;
    $inTotalALiquidar       = 0;
    $inTotalAPagarLiquidado = 0;

    $inSaldoDisponivel      = 0;
    $saldo_inicial          = 0;
    $aLiquidar              = 0;
    $aPagarLiquidado        = 0;
    $inAPagarLiquidado      = 0;
    $UnidadeALiquidar       = 0;
    $UnidadeAPagarLiquidado = 0;
    $OrgaoALiquidar         = 0;
    $OrgaoAPagarLiquidado   = 0;

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

    $ControlaTotalizador        = 1;
    $codOrgaoCabecalhoUltimo    = '';
    $codUnidadeCabecalhoUltimo  = '';
    $rsRecordSetOrgao           = clone $rsRecordSet;
    $countOrgao                 = 0;

    $arTotalRecurso = array();

    while (!$rsRecordSetOrgao->eof()) {
        $codOrgaoCabecalho    = $rsRecordSetOrgao->getCampo('num_orgao');
        $desOrgaoCabecalho    = $rsRecordSetOrgao->getCampo('nom_orgao');

        $codUnidadeCabecalho    = $rsRecordSetOrgao->getCampo('num_unidade');
        $desUnidadeCabecalho    = $rsRecordSetOrgao->getCampo('nom_unidade');

        $codEntidade            = $rsRecordSetOrgao->getCampo('cod_entidade');

        if (($codOrgaoCabecalho != $codOrgaoCabecalhoUltimo) || ($codUnidadeCabecalho != $codUnidadeCabecalhoUltimo)) {
            $codOrgaoCabecalhoTemp      = str_pad($codOrgaoCabecalho,2,"0",STR_PAD_LEFT);
            $codUnidadeCabecalhoTemp    = str_pad($codUnidadeCabecalho,2,"0",STR_PAD_LEFT);

            $arCabec = array ( array( "classificacao"=> $codOrgaoCabecalhoTemp, "descricao" => $desOrgaoCabecalho),
                               array( "classificacao"=> $codUnidadeCabecalhoTemp, "descricao" => $desUnidadeCabecalho));
            $obRSTmp = new RecordSet();
            $obRSTmp->preenche( $arCabec );
            $arCabecalho[] = $obRSTmp;
            $countOrgao++;

            //monta valores para orgãos diferentes
            $rsRecordSet->setPrimeiroElemento();
            $inReduzido = 0;

            while (!$rsRecordSet->eof()) {

                if ($codOrgaoCabecalho  == $rsRecordSet->getCampo('num_orgao') && ($codUnidadeCabecalho == $rsRecordSet->getCampo('num_unidade')) && ($codEntidade == $rsRecordSet->getCampo('cod_entidade') ) ) {
                    $stDotacaoPrincipal  = $rsRecordSet->getCampo('num_orgao').".".$rsRecordSet->getCampo('num_unidade').".";
                    $stDotacaoPrincipal .= $rsRecordSet->getCampo('cod_funcao').".".$rsRecordSet->getCampo('cod_subfuncao').".";
                    $stDotacaoPrincipal .= $rsRecordSet->getCampo('cod_programa');

                    $inOrgao        = $rsRecordSet->getCampo('num_orgao');
                    $stOrgaoUnidade = $rsRecordSet->getCampo('num_orgao').".".$rsRecordSet->getCampo('num_unidade');
                    $stPAO          = $rsRecordSet->getCampo('num_pao');
                    $inCodOrgao     = $rsRecordSet->getCampo('num_orgao');
                    $inCodUnidade   = $rsRecordSet->getCampo('num_unidade');

                    $stClassPAO = $rsRecordSet->getCampo('num_orgao' ).".".$rsRecordSet->getCampo('num_unidade' ).".".$rsRecordSet->getCampo('cod_funcao' ).".".$rsRecordSet->getCampo('cod_subfuncao' ).".".$rsRecordSet->getCampo('num_programa' ).".".$rsRecordSet->getCampo('num_acao' );
                    $stClassPAO = Mascara::validaMascaraDinamica( $stMascPAO       , $stClassPAO );

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
                        $arRecord[$inCount]['descricao_despesa'] = $rsRecordSet->getCampo('nom_orgao' );
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = "";
                        $arRecord[$inCount]['coluna4']           = "";
                        $arRecord[$inCount]['coluna5']           = "";
                        $arRecord[$inCount]['coluna6']           = "";
                        $arRecord[$inCount]['coluna7']           = '';
                        $inCount++;

                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 1;
                        $arRecord[$inCount]['classificacao']     = $stClassUnidade[1];
                        $arRecord[$inCount]['descricao_despesa'] = $rsRecordSet->getCampo('nom_unidade' );
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = "";
                        $arRecord[$inCount]['coluna4']           = "";
                        $arRecord[$inCount]['coluna5']           = "";
                        $arRecord[$inCount]['coluna6']           = "";
                        $arRecord[$inCount]['coluna7']           = '';
                        $inCount++;

                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 1;
                        $arRecord[$inCount]['classificacao']     = $stClassFuncao[1];
                        $arRecord[$inCount]['descricao_despesa'] = $rsRecordSet->getCampo('nom_funcao' );
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = "";
                        $arRecord[$inCount]['coluna4']           = "";
                        $arRecord[$inCount]['coluna5']           = "";
                        $arRecord[$inCount]['coluna6']           = "";
                        $arRecord[$inCount]['coluna7']           = '';
                        $inCount++;

                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 1;
                        $arRecord[$inCount]['classificacao']     = $stClassSubFuncao[1];
                        $arRecord[$inCount]['descricao_despesa'] = $rsRecordSet->getCampo('nom_subfuncao' );
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = "";
                        $arRecord[$inCount]['coluna4']           = "";
                        $arRecord[$inCount]['coluna5']           = "";
                        $arRecord[$inCount]['coluna6']           = "";
                        $arRecord[$inCount]['coluna7']           = '';
                        $inCount++;

                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 1;
                        $arRecord[$inCount]['classificacao']     = $stClassPrograma[1];
                        $arRecord[$inCount]['descricao_despesa'] = $rsRecordSet->getCampo('nom_programa' );
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = "";
                        $arRecord[$inCount]['coluna4']           = "";
                        $arRecord[$inCount]['coluna5']           = "";
                        $arRecord[$inCount]['coluna6']           = "";
                        $arRecord[$inCount]['coluna7']           = '';
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
                        $arRecord[$inCount]['descricao_despesa'] = $rsRecordSet->getCampo('nom_pao' );
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = "";
                        $arRecord[$inCount]['coluna4']           = "";
                        $arRecord[$inCount]['coluna5']           = "";
                        $arRecord[$inCount]['coluna6']           = "";
                        $arRecord[$inCount]['coluna7']           = '';
                        $inCount++;
                    }
                    $stClassificacao = ucwords( strtolower( $rsRecordSet->getCampo('descricao' ) ) );

                // Demonstrar desdobramentos ou não
                if( ( ($this->getDemonstrarDesdobramentos() == "N") && ( $rsRecordSet->getCampo('tipo_conta') == "M") )
                   or ($this->getDemonstrarDesdobramentos() == "S")  )
                {
                    //MONTA LINHA DA DESPESA SEM VALORES
                    $arRecord[$inCount]['pagina']            = 0;
                    $arRecord[$inCount]['nivel']             = 2;
                    $arRecord[$inCount]['classificacao']     = $rsRecordSet->getCampo('classificacao'   );
                    $arRecord[$inCount]['descricao_despesa'] = $stClassificacao;
                    $arRecord[$inCount]['cod_recurso']       = $rsRecordSet->getCampo('cod_recurso' );
                    $arRecord[$inCount]['nom_recurso']       = $rsRecordSet->getCampo('nom_recurso' );
                    $arRecord[$inCount]['cod_despesa']       = $rsRecordSet->getCampo('cod_despesa' );
                    $arRecord[$inCount]['coluna3']     = '';
                    $arRecord[$inCount]['coluna4']     = '';
                    $arRecord[$inCount]['coluna5']     = '';
                    $arRecord[$inCount]['coluna6']     = '';
                    $arRecord[$inCount]['coluna7']     = '';
                    $inCount++;

                    //MONTA LINHA DA DESPESA SALDO_INICIAL, SUPLEMENTAÇÕES, REDUÇÕES, TOTAL_CREDITOS, SALDO_DISPONÍVEL

                    $stCodigoReduzido  = 'Cód.Red -  '.$rsRecordSet->getCampo('cod_despesa');
                    $stRecurso         = $rsRecordSet->getCampo('num_recurso')." - ".$rsRecordSet->getCampo('nom_recurso');
                    $arRecord[$inCount]['pagina']            = 0;
                    $arRecord[$inCount]['nivel']             = 2;

                    if ($inReduzido != $rsRecordSet->getCampo('cod_despesa')) {
                       $arRecord[$inCount]['classificacao']     = $stCodigoReduzido;
                       $arRecord[$inCount]['descricao_despesa'] = $stRecurso;
                    } else {
                       $arRecord[$inCount]['classificacao']     = '';
                       $arRecord[$inCount]['descricao_despesa']     = '';
                    }
                    $stCodClassificacao = $arRecord[$inCount]['classificacao'];
                    $arRecord[$inCount]['cod_recurso']       = '';
                    $arRecord[$inCount]['nom_recurso']       = '';
                    $arRecord[$inCount]['cod_despesa']       = '';

                    if ($inReduzido != $rsRecordSet->getCampo('cod_despesa')) {

                        $arRecord[$inCount]['coluna3']   = number_format($rsRecordSet->getCampo('saldo_inicial'), 2, ',', '.');
                        $arRecord[$inCount]['coluna4']   = number_format($rsRecordSet->getCampo('suplementacoes'), 2, ',', '.');
                        $arRecord[$inCount]['coluna5']   = number_format($rsRecordSet->getCampo('reducoes'), 2, ',', '.');
                        $arRecord[$inCount]['coluna6']   = number_format($rsRecordSet->getCampo('total_creditos'), 2, ',', '.');

                        $rsRecordContaMae = new RecordSet;
                        $rsRecordContaMae = clone $rsRecordSet;
                        $inContaEmpenhadoAno    = 0;
                        $inContaAnuladoAno      = 0;
//                        $inContaEmpenhadoPeriodo = 0;
//                        $inContaAnuladoPeriodo   = 0;

                        while ($rsRecordContaMae->getCampo('cod_despesa') == $rsRecordSet->getCampo('cod_despesa')) {
                            $inContaEmpenhadoAno    = $inContaEmpenhadoAno + $rsRecordContaMae->getCampo('empenhado_ano');
                            $inContaAnuladoAno      = $inContaAnuladoAno   + $rsRecordContaMae->getCampo('anulado_ano');
//                            $inContaEmpenhadoPeriodo = $inContaEmpenhadoPeriodo + $rsRecordContaMae->getCampo('empenhado_per');
//                            $inContaAnuladoPeriodo   = $inContaAnuladoPeriodo   + $rsRecordContaMae->getCampo('anulado_per');
                            $rsRecordContaMae->proximo();
                        }

                        $inAuxTotalCreditos              = bcsub(bcadd($rsRecordSet->getCampo('saldo_inicial'),$rsRecordSet->getCampo('suplementacoes'),4),$rsRecordSet->getCampo('reducoes'),4);
                        $inSaldoDisponivel               = bcadd(bcsub($inAuxTotalCreditos,$inContaEmpenhadoAno,4),$inContaAnuladoAno,4);
//                      $inSaldoDisponivel               = bcadd(bcsub($inAuxTotalCreditos,$inContaEmpenhadoPeriodo,4),$inContaAnuladoPeriodo,4);
                        $arRecord[$inCount]['coluna7']   = number_format($inSaldoDisponivel, 2,',', '.');
                        if ($stCodClassificacao != '') {
                            $arTotalRecurso[$stRecurso][0]['coluna3'] += $rsRecordSet->getCampo('saldo_inicial');
                            $arTotalRecurso[$stRecurso][0]['coluna4'] += $rsRecordSet->getCampo('suplementacoes');
                            $arTotalRecurso[$stRecurso][0]['coluna5'] += $rsRecordSet->getCampo('reducoes');
                            $arTotalRecurso[$stRecurso][0]['coluna6'] += $rsRecordSet->getCampo('total_creditos');
                            $arTotalRecurso[$stRecurso][0]['coluna7'] += $inSaldoDisponivel;
                        }
                    } else {
                        $arRecord[$inCount]['coluna3']   = '0,00';
                        $arRecord[$inCount]['coluna4']   = '0,00';
                        $arRecord[$inCount]['coluna5']   = '0,00';
                        $arRecord[$inCount]['coluna6']   = '0,00';
                     // $inAuxTotalCreditos              = bcsub(bcadd($rsRecordSet->getCampo('saldo_inicial'),$rsRecordSet->getCampo('suplementacoes'),4),$rsRecordSet->getCampo('reducoes'),4);
                     // $inSaldoDisponivel               = bcadd(bcsub($inAuxTotalCreditos,$rsRecordSet->getCampo('empenhado_ano'),4),$rsRecordSet->getCampo('anulado_ano'),4);
                        $arRecord[$inCount]['coluna7']   = '0,00';
                    }

                    $inCount++;

                    //MONTA LINHA DA DESPESA EMPENHADO_PER,ANULADO_PER,LIQUIDADO_PER,PAGO_PER,A LIQUIDAR
                    $arRecord[$inCount]['pagina']            = 0;
                    $arRecord[$inCount]['nivel']             = 2;
                    $arRecord[$inCount]['classificacao']     = '';
                    $arRecord[$inCount]['descricao_despesa'] = '';
                    $arRecord[$inCount]['cod_recurso']       = '';
                    $arRecord[$inCount]['nom_recurso']       = '';
                    $arRecord[$inCount]['cod_despesa']       = '';

                    if ($inReduzido != $rsRecordSet->getCampo('cod_despesa')) {
                       $rsRecordContaMae = new RecordSet;
                       $rsRecordContaMae = clone $rsRecordSet;
                       $inEmpenhadoPer = 0;
                       $inAnuladoPer   = 0;
                       $inLiquidadoPer = 0;
                       $inPagoPer      = 0;
                       $inALiquidar    = 0;

                       while ($rsRecordContaMae->getCampo('cod_despesa') == $rsRecordSet->getCampo('cod_despesa')) {
                             $inEmpenhadoPer = $inEmpenhadoPer + $rsRecordContaMae->getCampo('empenhado_per');
                             $inAnuladoPer   = $inAnuladoPer   + $rsRecordContaMae->getCampo('anulado_per');
                             $inLiquidadoPer = $inLiquidadoPer + $rsRecordContaMae->getCampo('liquidado_per');
                             $inPagoPer      = $inPagoPer      + $rsRecordContaMae->getCampo('pago_per');
                             $inALiquidar    = $inALiquidar    +((($rsRecordContaMae->getCampo('empenhado_ano')-$rsRecordContaMae->getCampo('anulado_ano'))-$rsRecordContaMae->getCampo('liquidado_ano')));
//                           $inALiquidar    = $inALiquidar    +((($rsRecordContaMae->getCampo('empenhado_per')-$rsRecordContaMae->getCampo('anulado_per'))-$rsRecordContaMae->getCampo('liquidado_per')));
                             $rsRecordContaMae->proximo();
                            }
                       $arRecord[$inCount]['coluna3']           = number_format($inEmpenhadoPer, 2, ',', '.');
                       $arRecord[$inCount]['coluna4']           = number_format($inAnuladoPer, 2, ',', '.');
                       $arRecord[$inCount]['coluna5']           = number_format($inLiquidadoPer, 2, ',', '.');
                       $arRecord[$inCount]['coluna6']           = number_format($inPagoPer, 2, ',', '.');
                       $arRecord[$inCount]['coluna7']           = number_format($inALiquidar, 2, ',', '.');
                       if ($stCodClassificacao != '') {
                            $arTotalRecurso[$stRecurso][1]['coluna3'] += $inEmpenhadoPer;
                            $arTotalRecurso[$stRecurso][1]['coluna4'] += $inAnuladoPer;
                            $arTotalRecurso[$stRecurso][1]['coluna5'] += $inLiquidadoPer;
                            $arTotalRecurso[$stRecurso][1]['coluna6'] += $inPagoPer;
                            $arTotalRecurso[$stRecurso][1]['coluna7'] += $inALiquidar;
                        }
                    } else {
                       $arRecord[$inCount]['coluna3']           = number_format($rsRecordSet->getCampo('empenhado_per'), 2, ',', '.');
                       $arRecord[$inCount]['coluna4']           = number_format($rsRecordSet->getCampo('anulado_per'), 2, ',', '.');
                       $arRecord[$inCount]['coluna5']           = number_format($rsRecordSet->getCampo('liquidado_per'), 2, ',', '.');
                       $arRecord[$inCount]['coluna6']           = number_format($rsRecordSet->getCampo('pago_per'), 2, ',', '.');
                       $aLiquidar                               = bcsub(bcsub($rsRecordSet->getCampo('empenhado_ano'),$rsRecordSet->getCampo('anulado_ano'),4),$rsRecordSet->getCampo('liquidado_ano'),4);
                       $arRecord[$inCount]['coluna7']           = number_format($aLiquidar, 2, ',', '.');
                       if ($stCodClassificacao != '') {
                            $arTotalRecurso[$stRecurso][1]['coluna3'] += $rsRecordSet->getCampo('empenhado_per');
                            $arTotalRecurso[$stRecurso][1]['coluna4'] += $rsRecordSet->getCampo('anulado_per');
                            $arTotalRecurso[$stRecurso][1]['coluna5'] += $rsRecordSet->getCampo('liquidado_per');
                            $arTotalRecurso[$stRecurso][1]['coluna6'] += $rsRecordSet->getCampo('pago_per');
                            $arTotalRecurso[$stRecurso][1]['coluna7'] += $aLiquidar;
                        }
                    }
                    $inCount++;

                    //MONTA LINHA DA DESPESA EMPENHADO_ANO,ANULADO_ANO,LIQUIDADO_ANO,PAGO_ANO,A PAGAR LIQUIDADO
                    $arRecord[$inCount]['pagina']            = 0;
                    $arRecord[$inCount]['nivel']             = 2;
                    $arRecord[$inCount]['classificacao']     = '';
                    $arRecord[$inCount]['descricao_despesa'] = '';
                    $arRecord[$inCount]['cod_recurso']       = '';
                    $arRecord[$inCount]['nom_recurso']       = '';
                    $arRecord[$inCount]['cod_despesa']       = '';
                    if ($inReduzido != $rsRecordSet->getCampo('cod_despesa')) {
                        $rsRecordContaMae = new RecordSet;
                        $rsRecordContaMae = clone $rsRecordSet;
                        $inEmpenhadoAno    = 0;
                        $inAnuladoAno      = 0;
                        $inLiquidadoAno    = 0;
                        $inPagoAno         = 0;
                        $inAPagarLiquidado = 0;
                        while ($rsRecordContaMae->getCampo('cod_despesa') == $rsRecordSet->getCampo('cod_despesa')) {
                              $inEmpenhadoAno    = $inEmpenhadoAno + $rsRecordContaMae->getCampo('empenhado_ano');
                              $inAnuladoAno      = $inAnuladoAno   + $rsRecordContaMae->getCampo('anulado_ano');
                              $inLiquidadoAno    = $inLiquidadoAno + $rsRecordContaMae->getCampo('liquidado_ano');
                              $inPagoAno         = $inPagoAno      + $rsRecordContaMae->getCampo('pago_ano');
                              $inAPagarLiquidado = $inAPagarLiquidado + ( $rsRecordContaMae->getCampo('liquidado_ano')-$rsRecordContaMae->getCampo('pago_ano'));
//                            $inAPagarLiquidado = $inAPagarLiquidado + ( $rsRecordContaMae->getCampo('liquidado_per')-$rsRecordContaMae->getCampo('pago_per'));
                              $rsRecordContaMae->proximo();
                        }
                        $arRecord[$inCount]['coluna3']           = number_format($inEmpenhadoAno, 2, ',', '.');
                        $arRecord[$inCount]['coluna4']           = number_format($inAnuladoAno, 2, ',', '.');
                        $arRecord[$inCount]['coluna5']           = number_format($inLiquidadoAno, 2, ',', '.');
                        $arRecord[$inCount]['coluna6']           = number_format($inPagoAno, 2, ',', '.');
                        $arRecord[$inCount]['coluna7']           = number_format($inAPagarLiquidado, 2, ',', '.');

                        $UnidadeAPagarLiquidado = 0.00;
                        $UnidadeALiquidar       = 0.00;

                        $OrgaoAPagarLiquidado   = 0.00;
                        $OrgaoALiquidar         = 0.00;

                        $UnidadeAPagarLiquidado                  = $inAPagarLiquidado + bcsub($rsRecordSet->getCampo('liquidado_ano'),$rsRecordSet->getCampo('pago_ano'),4);
                        $OrgaoAPagarLiquidado                    = $inAPagarLiquidado + bcsub($rsRecordSet->getCampo('liquidado_ano'),$rsRecordSet->getCampo('pago_ano'),4);

                        $PAOALiquidar                            = bcsub(bcsub($rsRecordSet->getCampo('empenhado_ano'),$rsRecordSet->getCampo('anulado_ano'),4),$rsRecordSet->getCampo('liquidado_ano'),4);
                        $UnidadeALiquidar                        = bcsub(bcsub($rsRecordSet->getCampo('empenhado_ano'),$rsRecordSet->getCampo('anulado_ano'),4),$rsRecordSet->getCampo('liquidado_ano'),4);
                        $OrgaoALiquidar                          = bcsub(bcsub($rsRecordSet->getCampo('empenhado_ano'),$rsRecordSet->getCampo('anulado_ano'),4),$rsRecordSet->getCampo('liquidado_ano'),4);

                        $inTotalAPagarLiquidado = bcadd($inTotalAPagarLiquidado ,$inAPagarLiquidado,4);
                        $inTotalALiquidar       = bcadd($inTotalALiquidar       ,$PAOALiquidar,4);

                        $inOrgaoAPagarLiquidado = bcadd($inOrgaoAPagarLiquidado ,$OrgaoAPagarLiquidado,4);
                        $inOrgaoALiquidar       = bcadd($inOrgaoALiquidar       ,$OrgaoALiquidar,4);

                        $inUnidadeAPagarLiquidado = bcadd($inUnidadeAPagarLiquidado ,$UnidadeAPagarLiquidado,4);
                        $inUnidadeALiquidar       = bcadd($inUnidadeALiquidar       ,$UnidadeALiquidar,4);

                        if ($stCodClassificacao != '') {
                            $arTotalRecurso[$stRecurso][2]['coluna3'] += $inEmpenhadoAno;
                            $arTotalRecurso[$stRecurso][2]['coluna4'] += $inAnuladoAno;
                            $arTotalRecurso[$stRecurso][2]['coluna5'] += $inLiquidadoAno;
                            $arTotalRecurso[$stRecurso][2]['coluna6'] += $inPagoAno;
                            $arTotalRecurso[$stRecurso][2]['coluna7'] += $inAPagarLiquidado;
                        }
                    } else {
                        $arRecord[$inCount]['coluna3']           = number_format($rsRecordSet->getCampo('empenhado_ano')  , 2, ',', '.' );
                        $arRecord[$inCount]['coluna4']           = number_format($rsRecordSet->getCampo('anulado_ano'), 2, ',', '.');
                        $arRecord[$inCount]['coluna5']           = number_format($rsRecordSet->getCampo('liquidado_ano'), 2, ',', '.');

/*                      $UnidadeAPagarLiquidado = 0.00;
                        $UnidadeALiquidar       = 0.00;

                        $OrgaoAPagarLiquidado    = 0.00;
                        $OrgaoALiquidar         = 0.00;
  */
                        $aPagarLiquidado                         = bcsub($rsRecordSet->getCampo('liquidado_ano'),$rsRecordSet->getCampo('pago_ano'),4);
/*                      $UnidadeAPagarLiquidado                  = bcsub($rsRecordSet->getCampo('liquidado_ano'),$rsRecordSet->getCampo('pago_ano'),4);
                        $OrgaoAPagarLiquidado                    = bcsub($rsRecordSet->getCampo('liquidado_ano'),$rsRecordSet->getCampo('pago_ano'),4);

                        $UnidadeALiquidar                        = bcsub(bcsub($rsRecordSet->getCampo('empenhado_ano'),$rsRecordSet->getCampo('anulado_ano'),4),$rsRecordSet->getCampo('liquidado_ano'),4);
                        $OrgaoALiquidar                          = bcsub(bcsub($rsRecordSet->getCampo('empenhado_ano'),$rsRecordSet->getCampo('anulado_ano'),4),$rsRecordSet->getCampo('liquidado_ano'),4);
*/
                        $arRecord[$inCount]['coluna6']           = number_format($rsRecordSet->getCampo('pago_ano'), 2, ',', '.');
                        $arRecord[$inCount]['coluna7']           = number_format($aPagarLiquidado, 2, ',', '.');

/*                      $inTotalAPagarLiquidado = bcadd($inTotalAPagarLiquidado ,$aPagarLiquidado,4);
                        $inTotalALiquidar       = bcadd($inTotalALiquidar       ,$aLiquidar,4);

                        $inOrgaoAPagarLiquidado = bcadd($inOrgaoAPagarLiquidado ,$OrgaoAPagarLiquidado,4);
                        $inOrgaoALiquidar       = bcadd($inOrgaoALiquidar       ,$OrgaoALiquidar,4);

                        $inUnidadeAPagarLiquidado = bcadd($inUnidadeAPagarLiquidado ,$UnidadeAPagarLiquidado,4);
                        $inUnidadeALiquidar       = bcadd($inUnidadeALiquidar       ,$UnidadeALiquidar,4);*/
                        if ($stCodClassificacao != '') {
                            $arTotalRecurso[$stRecurso][2]['coluna3'] += $rsRecordSet->getCampo('empenhado_ano');
                            $arTotalRecurso[$stRecurso][2]['coluna4'] += $rsRecordSet->getCampo('anulado_ano');
                            $arTotalRecurso[$stRecurso][2]['coluna5'] += $rsRecordSet->getCampo('liquidado_ano');
                            $arTotalRecurso[$stRecurso][2]['coluna6'] += $rsRecordSet->getCampo('pago_ano');
                            $arTotalRecurso[$stRecurso][2]['coluna7'] += $aPagarLiquidado;
                        }
                    }
                    $inCount++;

                } // Fim Demonstrar Desdobramentos ou não

                    //VALORES UTILIZADOS NA TOTALIZAÇÃO POR PAO
                    $inTotalEmpenhadoPer  = bcadd($inTotalEmpenhadoPer,$rsRecordSet->getCampo('empenhado_per'),4);
                    $inTotalEmpenhadoAno  = bcadd($inTotalEmpenhadoAno,$rsRecordSet->getCampo('empenhado_ano'),4);

                    $inTotalAnuladoPer     = bcadd($inTotalAnuladoPer , $rsRecordSet->getCampo('anulado_per'),4);
                    $inTotalAnuladoAno     = bcadd($inTotalAnuladoAno , $rsRecordSet->getCampo('anulado_ano'),4);

                    $inTotalLiquidadoPer   = bcadd($inTotalLiquidadoPer, $rsRecordSet->getCampo('liquidado_per'),4);
                    $inTotalLiquidadoAno   = bcadd($inTotalLiquidadoAno , $rsRecordSet->getCampo('liquidado_ano'),4);

                    $inTotalPagoPer        = bcadd($inTotalPagoPer , $rsRecordSet->getCampo('pago_per'),4);
                    $inTotalPagoAno        = bcadd($inTotalPagoAno , $rsRecordSet->getCampo('pago_ano'),4);

                    //VALORES UTILIZADOS NA TOTALIZACAO POR ORGAO
                    $inOrgaoEmpenhadoPer   = bcadd($inOrgaoEmpenhadoPer,$rsRecordSet->getCampo('empenhado_per'),4);
                    $inOrgaoEmpenhadoAno   = bcadd($inOrgaoEmpenhadoAno,$rsRecordSet->getCampo('empenhado_ano'),4);

                    $inOrgaoAnuladoPer     = bcadd($inOrgaoAnuladoPer , $rsRecordSet->getCampo('anulado_per'),4);
                    $inOrgaoAnuladoAno     = bcadd($inOrgaoAnuladoAno , $rsRecordSet->getCampo('anulado_ano'),4);

                    $inOrgaoLiquidadoPer   = bcadd($inOrgaoLiquidadoPer, $rsRecordSet->getCampo('liquidado_per'),4);
                    $inOrgaoLiquidadoAno   = bcadd($inOrgaoLiquidadoAno , $rsRecordSet->getCampo('liquidado_ano'),4);

                    $inOrgaoPagoPer        = bcadd($inOrgaoPagoPer , $rsRecordSet->getCampo('pago_per'),4);
                    $inOrgaoPagoAno        = bcadd($inOrgaoPagoAno , $rsRecordSet->getCampo('pago_ano'),4);

                    //VALORES UTILIZADOS NA TOTALIZACAO POR UNIDADE
                    $inUnidadeEmpenhadoPer   = bcadd($inUnidadeEmpenhadoPer,$rsRecordSet->getCampo('empenhado_per'),4);
                    $inUnidadeEmpenhadoAno   = bcadd($inUnidadeEmpenhadoAno,$rsRecordSet->getCampo('empenhado_ano'),4);

                    $inUnidadeAnuladoPer     = bcadd($inUnidadeAnuladoPer , $rsRecordSet->getCampo('anulado_per'),4);
                    $inUnidadeAnuladoAno     = bcadd($inUnidadeAnuladoAno , $rsRecordSet->getCampo('anulado_ano'),4);

                    $inUnidadeLiquidadoPer   = bcadd($inUnidadeLiquidadoPer, $rsRecordSet->getCampo('liquidado_per'),4);
                    $inUnidadeLiquidadoAno   = bcadd($inUnidadeLiquidadoAno , $rsRecordSet->getCampo('liquidado_ano'),4);

                    $inUnidadePagoPer        = bcadd($inUnidadePagoPer , $rsRecordSet->getCampo('pago_per'),4);
                    $inUnidadePagoAno        = bcadd($inUnidadePagoAno , $rsRecordSet->getCampo('pago_ano'),4);

                    if ($inReduzido != $rsRecordSet->getCampo('cod_despesa')) {
                        //VALORES UTILIZADOS NA TOTALIZACAO POR PAO
                        $inTotalSaldoInicial    = bcadd($inTotalSaldoInicial,$rsRecordSet->getCampo('saldo_inicial'),4);
                        $inTotalReducoes        = bcadd($inTotalReducoes    , $rsRecordSet->getCampo('reducoes'),4);
                        $inTotalSuplementacoes  = bcadd($inTotalSuplementacoes , $rsRecordSet->getCampo('suplementacoes'),4);
                        $inTotalCreditos        = ($inTotalSaldoInicial + $inTotalSuplementacoes) - $inTotalReducoes;
                        $inTotalSaldoDisponivel = ($inTotalCreditos - $inTotalEmpenhadoAno) + $inTotalAnuladoAno;

                        //VALORES UTILIZADOS NA TOTALIZACAO POR ORGAO
                        $inOrgaoSaldoInicial    = bcadd($inOrgaoSaldoInicial,$rsRecordSet->getCampo('saldo_inicial'),4);
                        $inOrgaoReducoes        = bcadd($inOrgaoReducoes    , $rsRecordSet->getCampo('reducoes'),4);
                        $inOrgaoSuplementacoes  = bcadd($inOrgaoSuplementacoes , $rsRecordSet->getCampo('suplementacoes'),4);
                        $inOrgaoCreditos        = ($inOrgaoSaldoInicial + $inOrgaoSuplementacoes) - $inOrgaoReducoes;
                        $inOrgaoSaldoDisponivel = ($inOrgaoCreditos - $inOrgaoEmpenhadoAno) + $inOrgaoAnuladoAno;

                        //VALORES UTILIZADOS NA TOTALIZACAO POR UNIDADE
                        $inUnidadeSaldoInicial    = bcadd($inUnidadeSaldoInicial,$rsRecordSet->getCampo('saldo_inicial'),4);
                        $inUnidadeReducoes        = bcadd($inUnidadeReducoes    , $rsRecordSet->getCampo('reducoes'),4);
                        $inUnidadeSuplementacoes  = bcadd($inUnidadeSuplementacoes , $rsRecordSet->getCampo('suplementacoes'),4);
                        $inUnidadeCreditos        = ($inUnidadeSaldoInicial + $inUnidadeSuplementacoes) - $inUnidadeReducoes;
                        $inUnidadeSaldoDisponivel = ($inUnidadeCreditos - $inUnidadeEmpenhadoAno) + $inUnidadeAnuladoAno;
                    }
                    $inReduzido = $rsRecordSet->getCampo('cod_despesa');

                    // TOTALIZAÇÃO POR PAO
                    $rsRecordSet->proximo();
                    if ($stPAO != $rsRecordSet->getCampo('num_pao')) {
                        $inTotalSaldoDisponivel = bcadd(bcsub($inTotalCreditos,$inTotalEmpenhadoAno,4),$inTotalAnuladoAno,4);
//                      $inTotalSaldoDisponivel = bcadd(bcsub($inTotalCreditos,$inTotalEmpenhadoPer,4),$inTotalAnuladoPer,4);
                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 2;
                        $arRecord[$inCount]['classificacao']     = "";
                        $arRecord[$inCount]['descricao_despesa'] = "";
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = "";
                        $arRecord[$inCount]['coluna4']           = "";
                        $arRecord[$inCount]['coluna5']           = "";
                        $arRecord[$inCount]['coluna6']           = "";
                        $arRecord[$inCount]['coluna7']           = "";
                        $inCount++;

                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 2;
                        $arRecord[$inCount]['classificacao']     = "";
                        $arRecord[$inCount]['descricao_despesa'] = "TOTAL PROJETO ATIVIDADE";
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = number_format($inTotalSaldoInicial, 2, ',', '.' );
                        $arRecord[$inCount]['coluna4']           = number_format($inTotalSuplementacoes, 2, ',', '.' );
                        $arRecord[$inCount]['coluna5']           = number_format($inTotalReducoes, 2, ',', '.' );
                        $arRecord[$inCount]['coluna6']           = number_format($inTotalCreditos , 2, ',', '.' );
                        $arRecord[$inCount]['coluna7']           = number_format($inTotalSaldoDisponivel , 2, ',', '.' );
                        $inCount++;

                        $inTotalALiquidar = bcsub(bcsub($inTotalEmpenhadoAno,$inTotalAnuladoAno,4),$inTotalLiquidadoAno,4);

                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 2;
                        $arRecord[$inCount]['classificacao']     = "";
                        $arRecord[$inCount]['descricao_despesa'] = "";
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = number_format($inTotalEmpenhadoPer , 2, ',', '.' );
                        $arRecord[$inCount]['coluna4']           = number_format($inTotalAnuladoPer, 2, ',', '.' );
                        $arRecord[$inCount]['coluna5']           = number_format($inTotalLiquidadoPer , 2, ',', '.' );
                        $arRecord[$inCount]['coluna6']           = number_format($inTotalPagoPer, 2, ',', '.' );
                        $arRecord[$inCount]['coluna7']           = number_format($inTotalALiquidar, 2, ',', '.' );
                        $inCount++;

                        $inTotalAPagarLiquidado = bcsub($inTotalLiquidadoAno,$inTotalPagoAno,4);

                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 2;
                        $arRecord[$inCount]['classificacao']     = "";
                        $arRecord[$inCount]['descricao_despesa'] = "";
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = number_format($inTotalEmpenhadoAno, 2, ',', '.' );
                        $arRecord[$inCount]['coluna4']           = number_format($inTotalAnuladoAno, 2, ',', '.' );
                        $arRecord[$inCount]['coluna5']           = number_format($inTotalLiquidadoAno , 2, ',', '.' );
                        $arRecord[$inCount]['coluna6']           = number_format($inTotalPagoAno, 2, ',', '.' );
                        $arRecord[$inCount]['coluna7']           = number_format($inTotalAPagarLiquidado , 2, ',', '.' );
                        $inCount++;

                        // TOTALIZA GERAL
                        $inSomatorioTotalSaldoInicial      = bcadd($inSomatorioTotalSaldoInicial   , $inTotalSaldoInicial,4);
                        $inSomatorioTotalEmpenhadoPer      = bcadd($inSomatorioTotalEmpenhadoPer   , $inTotalEmpenhadoPer,4);
                        $inSomatorioTotalEmpenhadoAno      = bcadd($inSomatorioTotalEmpenhadoAno   , $inTotalEmpenhadoAno,4);

                        $inSomatorioTotalSuplementacoes    = bcadd($inSomatorioTotalSuplementacoes , $inTotalSuplementacoes,4);
                        $inSomatorioTotalAnuladoPer        = bcadd($inSomatorioTotalAnuladoPer     , $inTotalAnuladoPer,4) ;
                        $inSomatorioTotalAnuladoAno        = bcadd($inSomatorioTotalAnuladoAno     , $inTotalAnuladoAno,4);

                        $inSomatorioTotalReducoes          = bcadd($inSomatorioTotalReducoes       , $inTotalReducoes,4) ;
                        $inSomatorioTotalLiquidadoPer      = bcadd($inSomatorioTotalLiquidadoPer   , $inTotalLiquidadoPer,4);
                        $inSomatorioTotalLiquidadoAno      = bcadd($inSomatorioTotalLiquidadoAno   , $inTotalLiquidadoAno,4);

                        $inSomatorioTotalCreditos          = bcadd($inSomatorioTotalCreditos       , $inTotalCreditos,4) ;
                        $inSomatorioTotalPagoPer           = bcadd($inSomatorioTotalPagoPer        , $inTotalPagoPer,4) ;
                        $inSomatorioTotalPagoAno           = bcadd($inSomatorioTotalPagoAno        , $inTotalPagoAno,4);

                        $inTotalSaldoInicial    = 0;
                        $inTotalEmpenhadoPer    = 0;
                        $inTotalEmpenhadoAno    = 0;
                        $inTotalSuplementacoes  = 0;
                        $inTotalAnuladoPer      = 0;
                        $inTotalAnuladoAno      = 0;
                        $inTotalReducoes        = 0;
                        $inTotalLiquidadoPer    = 0;
                        $inTotalLiquidadoAno    = 0;
                        $inTotalCreditos        = 0;
                        $inTotalPagoPer         = 0;
                        $inTotalPagoAno         = 0;
                        $inTotalSaldoDisponivel = 0;
                        $inTotalALiquidar       = 0;
                        $inTotalAPagarLiquidado = 0;
                        $inSaldoDisponivel      = 0;
                        $saldo_inicial          = 0;
                        $aLiquidar              = 0;
                        $aPagarLiquidado        = 0;
                    }
                    $rsRecordSet->anterior();

                    // TOTALIZAÇÃO POR UNIDADE
                    $rsRecordSet->proximo();
                    if ( ($inCodOrgao != $rsRecordSet->getCampo('num_orgao')) || ($inCodUnidade!= $rsRecordSet->getCampo('num_unidade')) ) {

                        $inUnidadeSaldoDisponivel = bcadd(bcsub($inUnidadeCreditos,$inUnidadeEmpenhadoAno,4),$inUnidadeAnuladoAno,4);
//                      $inUnidadeSaldoDisponivel = bcadd(bcsub($inUnidadeCreditos,$inUnidadeEmpenhadoPer,4),$inUnidadeAnuladoPer,4);
                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 2;
                        $arRecord[$inCount]['classificacao']     = "";
                        $arRecord[$inCount]['descricao_despesa'] = "";
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = "";
                        $arRecord[$inCount]['coluna4']           = "";
                        $arRecord[$inCount]['coluna5']           = "";
                        $arRecord[$inCount]['coluna6']           = "";
                        $arRecord[$inCount]['coluna7']           = "";
                        $inCount++;

                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 2;
                        $arRecord[$inCount]['classificacao']     = "";
                        $arRecord[$inCount]['descricao_despesa'] = "TOTAL UNIDADE";
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = number_format($inUnidadeSaldoInicial, 2, ',', '.' );
                        $arRecord[$inCount]['coluna4']           = number_format($inUnidadeSuplementacoes, 2, ',', '.' );
                        $arRecord[$inCount]['coluna5']           = number_format($inUnidadeReducoes, 2, ',', '.' );
                        $arRecord[$inCount]['coluna6']           = number_format($inUnidadeCreditos , 2, ',', '.' );
                        $arRecord[$inCount]['coluna7']           = number_format($inUnidadeSaldoDisponivel , 2, ',', '.' );
                        $inCount++;

                        $inUnidadeALiquidar = bcsub(bcsub($inUnidadeEmpenhadoAno,$inUnidadeAnuladoAno,4),$inUnidadeLiquidadoAno,4);

                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 2;
                        $arRecord[$inCount]['classificacao']     = "";
                        $arRecord[$inCount]['descricao_despesa'] = "";
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = number_format($inUnidadeEmpenhadoPer , 2, ',', '.' );
                        $arRecord[$inCount]['coluna4']           = number_format($inUnidadeAnuladoPer, 2, ',', '.' );
                        $arRecord[$inCount]['coluna5']           = number_format($inUnidadeLiquidadoPer , 2, ',', '.' );
                        $arRecord[$inCount]['coluna6']           = number_format($inUnidadePagoPer, 2, ',', '.' );
                        $arRecord[$inCount]['coluna7']           = number_format($inUnidadeALiquidar, 2, ',', '.' );
                        $inCount++;

                        $inUnidadeAPagarLiquidado = bcsub($inUnidadeLiquidadoAno,$inUnidadePagoAno,4);

                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 2;
                        $arRecord[$inCount]['classificacao']     = "";
                        $arRecord[$inCount]['descricao_despesa'] = "";
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = number_format($inUnidadeEmpenhadoAno, 2, ',', '.' );
                        $arRecord[$inCount]['coluna4']           = number_format($inUnidadeAnuladoAno, 2, ',', '.' );
                        $arRecord[$inCount]['coluna5']           = number_format($inUnidadeLiquidadoAno , 2, ',', '.' );
                        $arRecord[$inCount]['coluna6']           = number_format($inUnidadePagoAno, 2, ',', '.' );
                        $arRecord[$inCount]['coluna7']           = number_format($inUnidadeAPagarLiquidado , 2, ',', '.' );
                        $inCount++;

                        // TOTALIZA GERAL
                        $inSomatorioUnidadeSaldoInicial      = bcadd($inSomatorioUnidadeSaldoInicial   , $inUnidadeInicial,4);
                        $inSomatorioUnidadeEmpenhadoPer      = bcadd($inSomatorioUnidadeEmpenhadoPer   , $inUnidadeEmpenhadoPer,4);
                        $inSomatorioUnidadeEmpenhadoAno      = bcadd($inSomatorioUnidadeEmpenhadoAno   , $inUnidadeEmpenhadoAno,4);

                        $inSomatorioUnidadeSuplementacoes    = bcadd($inSomatorioUnidadeSuplementacoes , $inUnidadeSuplementacoes,4);
                        $inSomatorioUnidadeAnuladoPer        = bcadd($inSomatorioUnidadeAnuladoPer     , $inUnidadeAnuladoPer,4) ;
                        $inSomatorioUnidadeAnuladoAno        = bcadd($inSomatorioUnidadeAnuladoAno     , $inUnidadeAnuladoAno,4);

                        $inSomatorioUnidadeReducoes          = bcadd($inSomatorioUnidadeReducoes       , $inUnidadeReducoes,4) ;
                        $inSomatorioUnidadeLiquidadoPer      = bcadd($inSomatorioUnidadeLiquidadoPer   , $inUnidadeLiquidadoPer,4);
                        $inSomatorioUnidadeLiquidadoAno      = bcadd($inSomatorioUnidadeLiquidadoAno   , $inUnidadeLiquidadoAno,4);

                        $inSomatorioUnidadeCreditos          = bcadd($inSomatorioUnidadeCreditos       , $inUnidadeCreditos,4) ;
                        $inSomatorioUnidadePagoPer           = bcadd($inSomatorioUnidadePagoPer        , $inUnidadePagoPer,4) ;
                        $inSomatorioUnidadePagoAno           = bcadd($inSomatorioUnidadePagoAno        , $inUnidadePagoAno,4);

                        $inUnidadeSaldoInicial    = 0;
                        $inUnidadeEmpenhadoPer    = 0;
                        $inUnidadeEmpenhadoAno    = 0;
                        $inUnidadeSuplementacoes  = 0;
                        $inUnidadeAnuladoPer      = 0;
                        $inUnidadeAnuladoAno      = 0;
                        $inUnidadeReducoes        = 0;
                        $inUnidadeLiquidadoPer    = 0;
                        $inUnidadeLiquidadoAno    = 0;
                        $inUnidadeCreditos        = 0;
                        $inUnidadePagoPer         = 0;
                        $inUnidadePagoAno         = 0;
                        $inUnidadeSaldoDisponivel = 0;
                        $inUnidadeALiquidar       = 0;
                        $inUnidadeAPagarLiquidado = 0;
                    }
                    $rsRecordSet->anterior();

                    // TOTALIZAÇÃO POR Orgao
                    $rsRecordSet->proximo();
                    if ( $inCodOrgao != $rsRecordSet->getCampo('num_orgao') ) {

                        $inOrgaoSaldoDisponivel = bcadd(bcsub($inOrgaoCreditos,$inOrgaoEmpenhadoAno,4),$inOrgaoAnuladoAno,4);
//                      $inOrgaoSaldoDisponivel = bcadd(bcsub($inOrgaoCreditos,$inOrgaoEmpenhadoPer,4),$inOrgaoAnuladoPer,4);

                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 2;
                        $arRecord[$inCount]['classificacao']     = "";
                        $arRecord[$inCount]['descricao_despesa'] = "";
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = "";
                        $arRecord[$inCount]['coluna4']           = "";
                        $arRecord[$inCount]['coluna5']           = "";
                        $arRecord[$inCount]['coluna6']           = "";
                        $arRecord[$inCount]['coluna7']           = "";
                        $inCount++;

                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 2;
                        $arRecord[$inCount]['classificacao']     = "";
                        $arRecord[$inCount]['descricao_despesa'] = "TOTAL ORGÃO";
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = number_format($inOrgaoSaldoInicial, 2, ',', '.' );
                        $arRecord[$inCount]['coluna4']           = number_format($inOrgaoSuplementacoes, 2, ',', '.' );
                        $arRecord[$inCount]['coluna5']           = number_format($inOrgaoReducoes, 2, ',', '.' );
                        $arRecord[$inCount]['coluna6']           = number_format($inOrgaoCreditos , 2, ',', '.' );
                        $arRecord[$inCount]['coluna7']           = number_format($inOrgaoSaldoDisponivel , 2, ',', '.' );
                        $inCount++;

                        $inOrgaoALiquidar = bcsub(bcsub($inOrgaoEmpenhadoAno,$inOrgaoAnuladoAno,4),$inOrgaoLiquidadoAno,4);

                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 2;
                        $arRecord[$inCount]['classificacao']     = "";
                        $arRecord[$inCount]['descricao_despesa'] = "";
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = number_format($inOrgaoEmpenhadoPer , 2, ',', '.' );
                        $arRecord[$inCount]['coluna4']           = number_format($inOrgaoAnuladoPer, 2, ',', '.' );
                        $arRecord[$inCount]['coluna5']           = number_format($inOrgaoLiquidadoPer , 2, ',', '.' );
                        $arRecord[$inCount]['coluna6']           = number_format($inOrgaoPagoPer, 2, ',', '.' );
                        $arRecord[$inCount]['coluna7']           = number_format($inOrgaoALiquidar, 2, ',', '.' );
                        $inCount++;

                        $inOrgaoAPagarLiquidado = bcsub($inOrgaoLiquidadoAno,$inOrgaoPagoAno,4);

                        $arRecord[$inCount]['pagina']            = 0;
                        $arRecord[$inCount]['nivel']             = 2;
                        $arRecord[$inCount]['classificacao']     = "";
                        $arRecord[$inCount]['descricao_despesa'] = "";
                        $arRecord[$inCount]['cod_recurso']       = "";
                        $arRecord[$inCount]['nom_recurso']       = "";
                        $arRecord[$inCount]['cod_despesa']       = "";
                        $arRecord[$inCount]['coluna3']           = number_format($inOrgaoEmpenhadoAno, 2, ',', '.' );
                        $arRecord[$inCount]['coluna4']           = number_format($inOrgaoAnuladoAno, 2, ',', '.' );
                        $arRecord[$inCount]['coluna5']           = number_format($inOrgaoLiquidadoAno , 2, ',', '.' );
                        $arRecord[$inCount]['coluna6']           = number_format($inOrgaoPagoAno, 2, ',', '.' );
                        $arRecord[$inCount]['coluna7']           = number_format($inOrgaoAPagarLiquidado , 2, ',', '.' );
                        $inCount++;

                        // TOTALIZA GERAL
                        $inSomatorioOrgaoSaldoInicial      = bcadd($inSomatorioOrgaoSaldoInicial   , $inOrgaoInicial,4);
                        $inSomatorioOrgaoEmpenhadoPer      = bcadd($inSomatorioOrgaoEmpenhadoPer   , $inOrgaoEmpenhadoPer,4);
                        $inSomatorioOrgaoEmpenhadoAno      = bcadd($inSomatorioOrgaoEmpenhadoAno   , $inOrgaoEmpenhadoAno,4);

                        $inSomatorioOrgaoSuplementacoes    = bcadd($inSomatorioOrgaoSuplementacoes , $inOrgaoSuplementacoes,4);
                        $inSomatorioOrgaoAnuladoPer        = bcadd($inSomatorioOrgaoAnuladoPer     , $inOrgaoAnuladoPer,4) ;
                        $inSomatorioOrgaoAnuladoAno        = bcadd($inSomatorioOrgaoAnuladoAno     , $inOrgaoAnuladoAno,4);

                        $inSomatorioOrgaoReducoes          = bcadd($inSomatorioOrgaoReducoes       , $inOrgaoReducoes,4) ;
                        $inSomatorioOrgaoLiquidadoPer      = bcadd($inSomatorioOrgaoLiquidadoPer   , $inOrgaoLiquidadoPer,4);
                        $inSomatorioOrgaoLiquidadoAno      = bcadd($inSomatorioOrgaoLiquidadoAno   , $inOrgaoLiquidadoAno,4);

                        $inSomatorioOrgaoCreditos          = bcadd($inSomatorioOrgaoCreditos       , $inOrgaoCreditos,4) ;
                        $inSomatorioOrgaoPagoPer           = bcadd($inSomatorioOrgaoPagoPer        , $inOrgaoPagoPer,4) ;
                        $inSomatorioOrgaoPagoAno           = bcadd($inSomatorioOrgaoPagoAno        , $inOrgaoPagoAno,4);

                        $inOrgaoSaldoInicial    = 0;
                        $inOrgaoEmpenhadoPer    = 0;
                        $inOrgaoEmpenhadoAno    = 0;
                        $inOrgaoSuplementacoes  = 0;
                        $inOrgaoAnuladoPer      = 0;
                        $inOrgaoAnuladoAno      = 0;
                        $inOrgaoReducoes        = 0;
                        $inOrgaoLiquidadoPer    = 0;
                        $inOrgaoLiquidadoAno    = 0;
                        $inOrgaoCreditos        = 0;
                        $inOrgaoPagoPer         = 0;
                        $inOrgaoPagoAno         = 0;
                        $inOrgaoSaldoDisponivel = 0;
                        $inOrgaoALiquidar       = 0;
                        $inOrgaoAPagarLiquidado = 0;
                    } // fim totalizacao por orgao
                    $rsRecordSet->anterior();

                }  // fim if $codOrgaoCabecalho
                $rsRecordSet->proximo();
                $inContador++;
            }

            $rsTemp = new RecordSet;
            $rsTemp->preenche($arRecord);
            unset($arRecord);
            $arRegistros[$countOrgao-1] = $rsTemp;
            $inCount = 0;
        }
        $codOrgaoCabecalhoUltimo = $rsRecordSetOrgao->getCampo('num_orgao');
        $codUnidadeCabecalhoUltimo = $rsRecordSetOrgao->getCampo('num_unidade');
        $rsRecordSetOrgao->proximo();
    }

    // RESUMO DE RECURSOS
    $inCount = 0;
    $arTotalResumoRecurso[$inCount]['pagina'] = 0;
    $arTotalResumoRecurso[$inCount]['nivel'] = 2;
    $arTotalResumoRecurso[$inCount]['classificacao'] = 'RESUMO POR RECURSO';
    $arTotalResumoRecurso[$inCount]['descricao_despesa'] = '';
    $arTotalResumoRecurso[$inCount]['cod_recurso'] = '';
    $arTotalResumoRecurso[$inCount]['nom_recurso'] = '';
    $arTotalResumoRecurso[$inCount]['cod_despesa'] = '';
    $arTotalResumoRecurso[$inCount]['coluna3']  = '';
    $arTotalResumoRecurso[$inCount]['coluna4']  = '';
    $arTotalResumoRecurso[$inCount]['coluna5']  = '';
    $arTotalResumoRecurso[$inCount]['coluna6']  = '';
    $arTotalResumoRecurso[$inCount]['coluna7']  = '';
    $inCount++;

    // Ordena as chaves do array de forma crescente
    ksort($arTotalRecurso);
    //Cria o array com os dados, o $arDadosRecurso vai retornar uma array com 3 posicoes e cada posicao tem mais um array com 5,
    //com os valores totais para cada linha do resumo
    foreach ($arTotalRecurso as $stRecurso => $arDadosRecurso) {
        $arTotalResumoRecurso[$inCount]['pagina'] = 0;
        $arTotalResumoRecurso[$inCount]['nivel'] = 2;
        $arTotalResumoRecurso[$inCount]['classificacao'] = '';
        $arTotalResumoRecurso[$inCount]['descricao_despesa'] = $stRecurso;
        $arTotalResumoRecurso[$inCount]['cod_recurso']  = '';
        $arTotalResumoRecurso[$inCount]['nom_recurso']  = '';
        $arTotalResumoRecurso[$inCount]['cod_despesa']  = '';
        foreach ($arDadosRecurso as $inChave => $arTotaisRecurso) {
            $arTotalResumoRecurso[$inCount]['coluna3'] = number_format($arTotaisRecurso['coluna3'], 2, ',', '.' );
            $arTotalResumoRecurso[$inCount]['coluna4'] = number_format($arTotaisRecurso['coluna4'], 2, ',', '.' );
            $arTotalResumoRecurso[$inCount]['coluna5'] = number_format($arTotaisRecurso['coluna5'], 2, ',', '.' );
            $arTotalResumoRecurso[$inCount]['coluna6'] = number_format($arTotaisRecurso['coluna6'], 2, ',', '.' );
            $arTotalResumoRecurso[$inCount]['coluna7'] = number_format($arTotaisRecurso['coluna7'], 2, ',', '.' );
            $arTotalColuna3[$inChave] += $arTotaisRecurso['coluna3'];
            $arTotalColuna4[$inChave] += $arTotaisRecurso['coluna4'];
            $arTotalColuna5[$inChave] += $arTotaisRecurso['coluna5'];
            $arTotalColuna6[$inChave] += $arTotaisRecurso['coluna6'];
            $arTotalColuna7[$inChave] += $arTotaisRecurso['coluna7'];
            $inCount++;
            $arTotalResumoRecurso[$inCount]['pagina'] = 0;
            $arTotalResumoRecurso[$inCount]['nivel'] = 2;
            $arTotalResumoRecurso[$inCount]['classificacao'] = '';
            $arTotalResumoRecurso[$inCount]['descricao_despesa'] = '';
            $arTotalResumoRecurso[$inCount]['cod_recurso']  = '';
            $arTotalResumoRecurso[$inCount]['nom_recurso']  = '';
            $arTotalResumoRecurso[$inCount]['cod_despesa']  = '';
        }
        $arTotalResumoRecurso[$inCount]['coluna3'] = '';
        $arTotalResumoRecurso[$inCount]['coluna4'] = '';
        $arTotalResumoRecurso[$inCount]['coluna5'] = '';
        $arTotalResumoRecurso[$inCount]['coluna6'] = '';
        $arTotalResumoRecurso[$inCount]['coluna7'] = '';
        $inCount++;
    }

    $arTotalResumoRecurso[$inCount]['pagina'] = 0;
    $arTotalResumoRecurso[$inCount]['nivel'] = 2;
    $arTotalResumoRecurso[$inCount]['classificacao'] = '';
    $arTotalResumoRecurso[$inCount]['descricao_despesa'] = 'TOTAL FINAL POR RECURSO';
    $arTotalResumoRecurso[$inCount]['cod_recurso'] = '';
    $arTotalResumoRecurso[$inCount]['nom_recurso'] = '';
    $arTotalResumoRecurso[$inCount]['cod_despesa'] = '';
    $arTotalResumoRecurso[$inCount]['coluna3']  = '';
    $arTotalResumoRecurso[$inCount]['coluna4']  = '';
    $arTotalResumoRecurso[$inCount]['coluna5']  = '';
    $arTotalResumoRecurso[$inCount]['coluna6']  = '';
    $arTotalResumoRecurso[$inCount]['coluna7']  = '';
    $inCount++;

    $arTotalResumoRecurso[$inCount]['pagina'] = 0;
    $arTotalResumoRecurso[$inCount]['nivel'] = 2;
    $arTotalResumoRecurso[$inCount]['classificacao'] = '';
    $arTotalResumoRecurso[$inCount]['descricao_despesa'] = '';
    $arTotalResumoRecurso[$inCount]['cod_recurso'] = '';
    $arTotalResumoRecurso[$inCount]['nom_recurso'] = '';
    $arTotalResumoRecurso[$inCount]['cod_despesa'] = '';
    $arTotalResumoRecurso[$inCount]['coluna3']  = number_format($arTotalColuna3[0], 2, ',', '.' );
    $arTotalResumoRecurso[$inCount]['coluna4']  = number_format($arTotalColuna4[0], 2, ',', '.' );
    $arTotalResumoRecurso[$inCount]['coluna5']  = number_format($arTotalColuna5[0], 2, ',', '.' );
    $arTotalResumoRecurso[$inCount]['coluna6']  = number_format($arTotalColuna6[0], 2, ',', '.' );
    $arTotalResumoRecurso[$inCount]['coluna7']  = number_format($arTotalColuna7[0], 2, ',', '.' );
    $inCount++;

    $arTotalResumoRecurso[$inCount]['pagina'] = 0;
    $arTotalResumoRecurso[$inCount]['nivel'] = 2;
    $arTotalResumoRecurso[$inCount]['classificacao'] = '';
    $arTotalResumoRecurso[$inCount]['descricao_despesa'] = '';
    $arTotalResumoRecurso[$inCount]['cod_recurso'] = '';
    $arTotalResumoRecurso[$inCount]['nom_recurso'] = '';
    $arTotalResumoRecurso[$inCount]['cod_despesa'] = '';
    $arTotalResumoRecurso[$inCount]['coluna3']  = number_format($arTotalColuna3[1], 2, ',', '.' );
    $arTotalResumoRecurso[$inCount]['coluna4']  = number_format($arTotalColuna4[1], 2, ',', '.' );
    $arTotalResumoRecurso[$inCount]['coluna5']  = number_format($arTotalColuna5[1], 2, ',', '.' );
    $arTotalResumoRecurso[$inCount]['coluna6']  = number_format($arTotalColuna6[1], 2, ',', '.' );
    $arTotalResumoRecurso[$inCount]['coluna7']  = number_format($arTotalColuna7[1], 2, ',', '.' );
    $inCount++;

    $arTotalResumoRecurso[$inCount]['pagina'] = 0;
    $arTotalResumoRecurso[$inCount]['nivel'] = 2;
    $arTotalResumoRecurso[$inCount]['classificacao'] = '';
    $arTotalResumoRecurso[$inCount]['descricao_despesa'] = '';
    $arTotalResumoRecurso[$inCount]['cod_recurso'] = '';
    $arTotalResumoRecurso[$inCount]['nom_recurso'] = '';
    $arTotalResumoRecurso[$inCount]['cod_despesa'] = '';
    $arTotalResumoRecurso[$inCount]['coluna3']  = number_format($arTotalColuna3[2], 2, ',', '.' );
    $arTotalResumoRecurso[$inCount]['coluna4']  = number_format($arTotalColuna4[2], 2, ',', '.' );
    $arTotalResumoRecurso[$inCount]['coluna5']  = number_format($arTotalColuna5[2], 2, ',', '.' );
    $arTotalResumoRecurso[$inCount]['coluna6']  = number_format($arTotalColuna6[2], 2, ',', '.' );
    $arTotalResumoRecurso[$inCount]['coluna7']  = number_format($arTotalColuna7[2], 2, ',', '.' );
    $inCount++;

    $rsResumoRecurso = new RecordSet;
    $rsResumoRecurso->preenche($arTotalResumoRecurso);
    $this->setRsResumoRecurso($rsResumoRecurso);

    //TOTAL GERAL PROJETO ATIVIDADE
    $inCount = 0;
    $arTotalFinal[$inCount]['pagina']            = 0;
    $arTotalFinal[$inCount]['nivel']             = 2;
    $arTotalFinal[$inCount]['classificacao']     = "";
    $arTotalFinal[$inCount]['descricao_despesa'] = "TOTAL GERAL PROJETO ATIVIDADE";
    $arTotalFinal[$inCount]['cod_recurso']       = "";
    $arTotalFinal[$inCount]['nom_recurso']       = "";
    $arTotalFinal[$inCount]['cod_despesa']       = "";

    $inSomatorioTotalSaldoDisponivel = bcadd(bcsub($inSomatorioTotalCreditos,$inSomatorioTotalEmpenhadoAno,4),$inSomatorioTotalAnuladoAno,4);
//  $inSomatorioTotalSaldoDisponivel = bcadd(bcsub($inSomatorioTotalCreditos,$inSomatorioTotalEmpenhadoPer,4),$inSomatorioTotalAnuladoPer,4);
    $inSomatorioTotalAPagarLiquidado = bcsub($inSomatorioTotalLiquidadoAno,$inSomatorioTotalPagoAno,4);
//  $inSomatorioTotalAPagarLiquidado = bcsub($inSomatorioTotalLiquidadoPer,$inSomatorioTotalPagoPer,4);
    $inSomatorioTotalALiquidar       = bcsub(bcsub($inSomatorioTotalEmpenhadoAno, $inSomatorioTotalAnuladoAno,4),$inSomatorioTotalLiquidadoAno,4);
//  $inSomatorioTotalALiquidar       = bcsub(bcsub($inSomatorioTotalEmpenhadoPer, $inSomatorioTotalAnuladoPer,4),$inSomatorioTotalLiquidadoPer,4);

    $arTotalFinal[$inCount]['coluna3']           = number_format($inSomatorioTotalSaldoInicial, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna4']           = number_format($inSomatorioTotalSuplementacoes, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna5']           = number_format($inSomatorioTotalReducoes, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna6']           = number_format($inSomatorioTotalCreditos , 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna7']           = number_format($inSomatorioTotalSaldoDisponivel , 2, ',', '.' );
    $inCount++;

    $arTotalFinal[$inCount]['pagina']            = 0;
    $arTotalFinal[$inCount]['nivel']             = 2;
    $arTotalFinal[$inCount]['classificacao']     = "";
    $arTotalFinal[$inCount]['descricao_despesa'] = "";
    $arTotalFinal[$inCount]['cod_recurso']       = "";
    $arTotalFinal[$inCount]['nom_recurso']       = "";
    $arTotalFinal[$inCount]['cod_despesa']       = "";
    $arTotalFinal[$inCount]['coluna3']           = number_format($inSomatorioTotalEmpenhadoPer , 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna4']           = number_format($inSomatorioTotalAnuladoPer, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna5']           = number_format($inSomatorioTotalLiquidadoPer , 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna6']           = number_format($inSomatorioTotalPagoPer, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna7']           = number_format($inSomatorioTotalALiquidar, 2, ',', '.' );
    $inCount++;

    $arTotalFinal[$inCount]['pagina']            = 0;
    $arTotalFinal[$inCount]['nivel']             = 2;
    $arTotalFinal[$inCount]['classificacao']     = "";
    $arTotalFinal[$inCount]['descricao_despesa'] = "";
    $arTotalFinal[$inCount]['cod_recurso']       = "";
    $arTotalFinal[$inCount]['nom_recurso']       = "";
    $arTotalFinal[$inCount]['cod_despesa']       = "";
    $arTotalFinal[$inCount]['coluna3']           = number_format($inSomatorioTotalEmpenhadoAno, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna4']           = number_format($inSomatorioTotalAnuladoAno, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna5']           = number_format($inSomatorioTotalLiquidadoAno , 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna6']           = number_format($inSomatorioTotalPagoAno, 2, ',', '.' );

    $arTotalFinal[$inCount]['coluna7']           = number_format($inSomatorioTotalAPagarLiquidado , 2, ',', '.' );
    $inCount++;

    $arTotalFinal[$inCount]['pagina']            = 0;
    $arTotalFinal[$inCount]['nivel']             = 2;
    $arTotalFinal[$inCount]['classificacao']     = "";
    $arTotalFinal[$inCount]['descricao_despesa'] = "";
    $arTotalFinal[$inCount]['cod_recurso']       = "";
    $arTotalFinal[$inCount]['nom_recurso']       = "";
    $arTotalFinal[$inCount]['cod_despesa']       = "";
    $arTotalFinal[$inCount]['coluna3']           = "";
    $arTotalFinal[$inCount]['coluna4']           = "";
    $arTotalFinal[$inCount]['coluna5']           = "";
    $arTotalFinal[$inCount]['coluna6']           = "";
    $arTotalFinal[$inCount]['coluna7']           = "";
    $inCount++;
/*
     //TOTAL GERAL ORGAO

    $arTotalFinal[$inCount]['pagina']            = 0;
    $arTotalFinal[$inCount]['nivel']             = 2;
    $arTotalFinal[$inCount]['classificacao']     = "";
    $arTotalFinal[$inCount]['descricao_despesa'] = "TOTAL GERAL ORGAO";
    $arTotalFinal[$inCount]['cod_recurso']       = "";
    $arTotalFinal[$inCount]['nom_recurso']       = "";
    $arTotalFinal[$inCount]['cod_despesa']       = "";

    $inSomatorioOrgaoSaldoDisponivel = bcadd(bcsub($inSomatorioOrgaoCreditos,$inSomatorioOrgaoEmpenhadoAno,4),$inSomatorioOrgaoAnuladoAno,4);
    $inSomatorioOrgaoAPagarLiquidado = bcsub($inSomatorioOrgaoLiquidadoAno,$inSomatorioOrgaoPagoAno,4);
    $inSomatorioOrgaoALiquidar       = bcsub(bcsub($inSomatorioOrgaoEmpenhadoAno, $inSomatorioOrgaoAnuladoAno,4),$inSomatorioOrgaoLiquidadoAno,4);

    $arTotalFinal[$inCount]['coluna3']           = number_format($inSomatorioOrgaoSaldoInicial, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna4']           = number_format($inSomatorioOrgaoSuplementacoes, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna5']           = number_format($inSomatorioOrgaoReducoes, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna6']           = number_format($inSomatorioOrgaoCreditos , 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna7']           = number_format($inSomatorioOrgaoSaldoDisponivel , 2, ',', '.' );
    $inCount++;

    $arTotalFinal[$inCount]['pagina']            = 0;
    $arTotalFinal[$inCount]['nivel']             = 2;
    $arTotalFinal[$inCount]['classificacao']     = "";
    $arTotalFinal[$inCount]['descricao_despesa'] = "";
    $arTotalFinal[$inCount]['cod_recurso']       = "";
    $arTotalFinal[$inCount]['nom_recurso']       = "";
    $arTotalFinal[$inCount]['cod_despesa']       = "";
    $arTotalFinal[$inCount]['coluna3']           = number_format($inSomatorioOrgaoEmpenhadoPer , 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna4']           = number_format($inSomatorioOrgaoAnuladoPer, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna5']           = number_format($inSomatorioOrgaoLiquidadoPer , 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna6']           = number_format($inSomatorioOrgaoPagoPer, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna7']           = number_format($inSomatorioOrgaoALiquidar, 2, ',', '.' );
    $inCount++;

    $arTotalFinal[$inCount]['pagina']            = 0;
    $arTotalFinal[$inCount]['nivel']             = 2;
    $arTotalFinal[$inCount]['classificacao']     = "";
    $arTotalFinal[$inCount]['descricao_despesa'] = "";
    $arTotalFinal[$inCount]['cod_recurso']       = "";
    $arTotalFinal[$inCount]['nom_recurso']       = "";
    $arTotalFinal[$inCount]['cod_despesa']       = "";
    $arTotalFinal[$inCount]['coluna3']           = number_format($inSomatorioOrgaoEmpenhadoAno, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna4']           = number_format($inSomatorioOrgaoAnuladoAno, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna5']           = number_format($inSomatorioOrgaoLiquidadoAno , 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna6']           = number_format($inSomatorioOrgaoPagoAno, 2, ',', '.' );

    $arTotalFinal[$inCount]['coluna7']           = number_format($inSomatorioOrgaoAPagarLiquidado , 2, ',', '.' );
    $inCount++;

    $arTotalFinal[$inCount]['pagina']            = 0;
    $arTotalFinal[$inCount]['nivel']             = 2;
    $arTotalFinal[$inCount]['classificacao']     = "";
    $arTotalFinal[$inCount]['descricao_despesa'] = "";
    $arTotalFinal[$inCount]['cod_recurso']       = "";
    $arTotalFinal[$inCount]['nom_recurso']       = "";
    $arTotalFinal[$inCount]['cod_despesa']       = "";
    $arTotalFinal[$inCount]['coluna3']           = "";
    $arTotalFinal[$inCount]['coluna4']           = "";
    $arTotalFinal[$inCount]['coluna5']           = "";
    $arTotalFinal[$inCount]['coluna6']           = "";
    $arTotalFinal[$inCount]['coluna7']           = "";

    //TOTAL GERAL UNIDADE
    $inCount++;

    $arTotalFinal[$inCount]['pagina']            = 0;
    $arTotalFinal[$inCount]['nivel']             = 2;
    $arTotalFinal[$inCount]['classificacao']     = "";
    $arTotalFinal[$inCount]['descricao_despesa'] = "TOTAL GERAL UNIDADE";
    $arTotalFinal[$inCount]['cod_recurso']       = "";
    $arTotalFinal[$inCount]['nom_recurso']       = "";
    $arTotalFinal[$inCount]['cod_despesa']       = "";

    $inSomatorioSaldoDisponivel = bcadd(bcsub($inSomatorioUnidadeCreditos,$inSomatorioUnidadeEmpenhadoAno,4),$inSomatorioUnidadeAnuladoAno,4);
    $inSomatorioUnidadeAPagarLiquidado = bcsub($inSomatorioUnidadeLiquidadoAno,$inSomatorioUnidadePagoAno,4);
    $inSomatorioUnidadeALiquidar       = bcsub(bcsub($inSomatorioUnidadeEmpenhadoAno, $inSomatorioUnidadeAnuladoAno,4),$inSomatorioUnidadeLiquidadoAno,4);

    $arTotalFinal[$inCount]['coluna3']           = number_format($inSomatorioUnidadeSaldoInicial, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna4']           = number_format($inSomatorioUnidadeSuplementacoes, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna5']           = number_format($inSomatorioUnidadeReducoes, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna6']           = number_format($inSomatorioUnidadeCreditos , 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna7']           = number_format($inSomatorioUnidadeSaldoDisponivel , 2, ',', '.' );
    $inCount++;

    $arTotalFinal[$inCount]['pagina']            = 0;
    $arTotalFinal[$inCount]['nivel']             = 2;
    $arTotalFinal[$inCount]['classificacao']     = "";
    $arTotalFinal[$inCount]['descricao_despesa'] = "";
    $arTotalFinal[$inCount]['cod_recurso']       = "";
    $arTotalFinal[$inCount]['nom_recurso']       = "";
    $arTotalFinal[$inCount]['cod_despesa']       = "";
    $arTotalFinal[$inCount]['coluna3']           = number_format($inSomatorioUnidadeEmpenhadoPer , 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna4']           = number_format($inSomatorioUnidadeAnuladoPer, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna5']           = number_format($inSomatorioUnidadeLiquidadoPer , 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna6']           = number_format($inSomatorioUnidadePagoPer, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna7']           = number_format($inSomatorioUnidadeALiquidar, 2, ',', '.' );
    $inCount++;

    $arTotalFinal[$inCount]['pagina']            = 0;
    $arTotalFinal[$inCount]['nivel']             = 2;
    $arTotalFinal[$inCount]['classificacao']     = "";
    $arTotalFinal[$inCount]['descricao_despesa'] = "";
    $arTotalFinal[$inCount]['cod_recurso']       = "";
    $arTotalFinal[$inCount]['nom_recurso']       = "";
    $arTotalFinal[$inCount]['cod_despesa']       = "";
    $arTotalFinal[$inCount]['coluna3']           = number_format($inSomatorioUnidadeEmpenhadoAno, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna4']           = number_format($inSomatorioUnidadeAnuladoAno, 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna5']           = number_format($inSomatorioUnidadeLiquidadoAno , 2, ',', '.' );
    $arTotalFinal[$inCount]['coluna6']           = number_format($inSomatorioUnidadePagoAno, 2, ',', '.' );

    $arTotalFinal[$inCount]['coluna7']           = number_format($inSomatorioUnidadeAPagarLiquidado , 2, ',', '.' );
    $inCount++;

    $arTotalFinal[$inCount]['pagina']            = 0;
    $arTotalFinal[$inCount]['nivel']             = 2;
    $arTotalFinal[$inCount]['classificacao']     = "";
    $arTotalFinal[$inCount]['descricao_despesa'] = "";
    $arTotalFinal[$inCount]['cod_recurso']       = "";
    $arTotalFinal[$inCount]['nom_recurso']       = "";
    $arTotalFinal[$inCount]['cod_despesa']       = "";
    $arTotalFinal[$inCount]['coluna3']           = "";
    $arTotalFinal[$inCount]['coluna4']           = "";
    $arTotalFinal[$inCount]['coluna5']           = "";
    $arTotalFinal[$inCount]['coluna6']           = "";
    $arTotalFinal[$inCount]['coluna7']           = "";
*/
    $rsTotalFinal = new RecordSet;
    $rsTotalFinal->preenche($arTotalFinal);

    return $obErro;
}

}
