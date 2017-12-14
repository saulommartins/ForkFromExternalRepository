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
    * Classe de relatório para Razão da Despesa
    * Data de Criação   : 06/03/2006

    * @author Analista:
    * @author Desenvolvedor: Tonismar R. Bernardo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30824 $
    $Name$
    $Autor:$
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.32
*/

/*
$Log$
Revision 1.7  2007/10/03 19:00:21  hwalves
Ticket#10171#

Revision 1.6  2007/03/23 18:28:56  luciano
#8739#

Revision 1.5  2007/03/01 20:05:11  luciano
#8509#

Revision 1.4  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php" );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoRazaoDespesa.class.php" );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoBalanceteDespesa.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php" );
include_once( CAM_FW_PDF."RRelatorio.class.php" );

/**
    * Classe de Regra de Negócio
    * @author Desenvolvedor: Tonismar R. Bernardo
*/

class ROrcamentoRelatorioRazaoDespesa extends PersistenteRelatorio
{
    /**
      * @var Object
      * @access Private
    */
    public $obREmpenhoEmpenho;
    /**
      * @var String
      * @access Private
    */
    public $stNomDotacao;
    /**
      * @var Integer
      * @access Private
    */
    public $inCodEntidade;
    /**
      * @var Date
      * @access Private
    */
    public $dtDataInicial;
    /**
      * @var Date
      * @access Private
    */
    public $dtDataFinal;
    /**
      * @var Integer
      * @access Private
    */
    public $inCodOrgao;
    /**
      * @var Integer
      * @access Private
    */
    public $inCodUnidade;
    /**
      * @var Integer;
      * @access Private
    */
    public $inCodDotacao;
    /**
      * @var Integer
      * @access Private
    */
    public $stCodDesdobramento;
    /**
      * @var Integer
      * @access Private
    */
    public $inCodRecurso;
    /**
      * @var Boolean
      * @access Private
    */
    public $boEmpenhoAnulacao;
    /**
      * @var Boolean
      * @access Private
    */
    public $boLiquidacaoAnulacao;
    /**
      * @var Boolean
      * @access Private
    */
    public $boPagamentoEstorno;
    /**
      * @var Boolean
      * @access Private
    */
    public $boSuplementacaoReducao;
    /**
      * @access Public
      * @param String
    */
    public $stExercicio;
    /**
      * @access Public
      * @param Integer
    */
    public $inCodReduzido;
    /**
      * @access Public
      * @param Integer
    */
    public $inCodDesdobramentoFinal;

    public $stDestinacaoRecurso;
    public $inCodDetalhamento;

    /** SETTERS */

    /**
      * @access Public
      * @param String $valor
    */
    public function setNomDotacao($valor) { $this->stNomDotacao           = $valor; }
    /**
      * @access Public
      * @param Integer $valor
    */
    public function setCodEntidade($valor) { $this->inCodEntidade          = $valor; }
    /**
      * @access Public
      * @param Date $valor
    */
    public function setDataInicial($valor) { $this->dtDataInicial          = $valor; }
    /**
      * @access Public
      * @param Date $valor
    */
    public function setDataFinal($valor) { $this->dtDataFinal            = $valor; }
    /**
      * @access Public
      * @param Integer $valor
    */
    public function setCodOrgao($valor) { $this->inCodOrgao             = $valor; }
    /**
      * @access Public
      * @param Integer $valor
    */
    public function setCodUnidade($valor) { $this->inCodUnidade           = $valor; }
    /**
      * @access Public
      * @param Integer $valor
    */
    public function setCodDotacao($valor) { $this->inCodDotacao           = $valor; }
    /**
      * @access Public
      * @param Integer $valor
    */
    public function setCodDesdobramento($valor) { $this->stCodDesdobramento     = $valor; }
    /**
      * @access Public
      * @param Integer $valor
    */
    public function setCodRecurso($valor) { $this->inCodRecurso           = $valor; }
    public function setDestinacaoRecurso($valor) { $this->stDestinacaoRecurso = $valor; }
    public function setCodDetalhamento($valor) { $this->inCodDetalhamento = $valor; }

    /**
      * @access Public
      * @param Boolean $valr
    */
    public function setEmpenhoAnulacao($valor) { $this->boEmpenhoAnulacao      = $valor; }
    /**
      * @access Public
      * @param Boolean $valr
    */
    public function setLiquidacaoAnulacao($valor) { $this->boLiquidacaoAnulacao   = $valor; }
    /**
      * @access Public
      * @param Boolean $valr
    */
    public function setPagamentoEstorno($valor) { $this->boPagamentoEstorno     = $valor; }
    /**
      * @access Public
      * @param Boolean $valr
    */
    public function setSuplementacaoReducao($valor) { $this->boSuplementacaoReducao = $valor; }
    /*
       * @access Public
       * @param Object
    */
    public function setFOrcamentoRazaoDespesa($valor) { $this->obFOrcamentoRazaoDespesa = $valor; }
    /**
      * @access Public
      * @param Object
    */
    public function setFOrcamentoBalanceteDespesa($valor) { $this->obFOrcamentoBalanceteDespesa = $valor; }
    /*
       * @access Public
       * @param String
    */
    public function setExercicio($valor) { $this->stExercicio = $valor; }
    /**
         * @access Public
         * @param Object $valor
    */
    public function setTContabilidadeRazaoDespesa($valor) { $this->obTContabilidadeValorLancamento        = $valor; }
    /**
        * @access Public
        * @param Integer
    */
    public function setCodReduzido($valor) { $this->inCodReduzido = $valor; }
    /**
        * @access Public
        * @param Integer
    */
    public function setCodDesdobramentoFinal($valor) { $this->inCodDesdobramentoFinal = $valor; }

    /** GETTERS */

    public function getNomDotacao() { return $this->stNomDotacao;           }
    /**
      * @access Public
      * @return Integer
    */
    public function getCodEntidade() { return $this->inCodEntidade;          }
    /**
      * @access Public
      * @return Date
    */
    public function getDataInicial() { return $this->dtDataInicial;          }
    /**
      * @access Public
      * @return Date
    */
    public function getDataFinal() { return $this->dtDataFinal;            }
     /**
      * @access Public
      * @return Integer
    */
    public function getCodOrgao() { return $this->inCodOrgao;             }
    /**
      * @access Public
      * @return Integer
    */
    public function getCodUnidade() { return $this->inCodUnidade;           }
    /**
      * @access Public
      * @return Integer
    */
    public function getCodDotacao() { return $this->inCodDotacao;           }
     /**
      * @access Public
      * @return Integer
    */
    public function getCodDesdobramento() { return $this->stCodDesdobramento;     }
    /**
      * @access Public
      * @return Integer
    */
    public function getCodRecurso() { return $this->inCodRecurso;           }
    /**
      * @access Public
      * @return Boolean
    */
    public function getEmpenhoAnulacao() { return $this->boEmpenhoAnulacao;      }
     /**
      * @access Public
      * @return Boolean
    */
    public function getLiquidacaoAnulacao() { return $this->boLiquidacaoAnulacao;   }
    /**
      * @access Public
      * @return Boolean
    */
    public function getPagamentoEstorno() { return $this->boPagamentoEstorno;     }
    /**
      * @access Public
      * @return Boolean
    */
    public function getSuplementacaoReducao() { return $this->boSuplementacaoReducao; }
    /**
      * @access Public
      * @return Object
    */
    public function getFOrcamentoRazaoDespesa() { return $this->obFOrcamentoRazaoDespesa; }
    /**
      * @access Public
      * @return String
    */
    public function getExercicio() { return $this->stExericio; }
    /**
        * @access Public
        * @return String
    */
    public function getCodReduzido() { return $this->inCodReduzido; }
    /**
        * @access Public
        * @return Integer
    */
    public function getCodDesdobramentoFinal() { return $this->inCodDesdobramentoFinal; }

    /**
      * Método Construtor
      * @access Public
    */
    public function ROrcamentoRelatorioRazaoDespesa()
    {
        $this->setFOrcamentoBalanceteDespesa ( new FOrcamentoBalanceteDespesa );
        $this->setFOrcamentoRazaoDespesa ( new FOrcamentoRazaoDespesa );
        $this->obREmpenhoEmpenho = new REmpenhoEmpenho;
        $this->obRRelatorio = new RRelatorio;
    }

function geraRecordSet(&$arRecordSet, &$arRecordSet1, $stOrder = "")
{
    $stFiltro = '';

    $inCount            = 0;
    $arRecord           = array();
    $arRecord1          = array();
    $arRecordLinha      = array();

    $this->obFOrcamentoBalanceteDespesa->setDado("exercicio", Sessao::getExercicio() );

    if ( $this->getCodEntidade() ) {
        $this->obFOrcamentoBalanceteDespesa->setDado("stFiltro"," and od.cod_entidade = ".$this->getCodEntidade());
    }

    if ( $this->getCodRecurso() ) {
        $this->obFOrcamentoBalanceteDespesa->setDado("stFiltro",$this->obFOrcamentoBalanceteDespesa->getDado("stFiltro")." and od.cod_recurso = ".$this->getCodRecurso());
    }

    if ($this->stDestinacaoRecurso) {
        $this->obFOrcamentoBalanceteDespesa->setDado("stFiltro",$this->obFOrcamentoBalanceteDespesa->getDado("stFiltro")." AND oru.masc_recurso_red like \'".$this->stDestinacaoRecurso."%\' " );
    }

    if ($this->inCodDetalhamento) {
        $this->obFOrcamentoBalanceteDespesa->setDado("stFiltro",$this->obFOrcamentoBalanceteDespesa->getDado("stFiltro")." AND oru.cod_detalhamento = ".$this->inCodDetalhamento );
    }

    $this->obFOrcamentoBalanceteDespesa->setDado("stCodEstruturalInicial",$this->getCodDesdobramento());
    $this->obFOrcamentoBalanceteDespesa->setDado("stCodEstruturalFinal", $this->getCodDesdobramentoFinal());
    $this->obFOrcamentoBalanceteDespesa->setDado("stDataInicial",$this->getDataInicial());
    $this->obFOrcamentoBalanceteDespesa->setDado("stDataFinal",$this->getDataFinal());
    $this->obFOrcamentoBalanceteDespesa->setDado("inNumOrgao", $this->getCodOrgao() );
    $this->obFOrcamentoBalanceteDespesa->setDado("inNumUnidade", $this->getCodUnidade());
    $this->obFOrcamentoBalanceteDespesa->setDado("stCodReduzidoInicial",$this->getCodReduzido());
    $this->obFOrcamentoBalanceteDespesa->setDado("stCodReduzidoFinal",$this->getCodReduzido());

    $inTmp = 0;
    $stOrder = "";

    /* CHAMADA DA FUNÇÃO BALANCETE DA DESPESA */
    $obErro = $this->obFOrcamentoBalanceteDespesa->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

    if ( (!$obErro->ocorreu()) && ($rsRecordSet->getNumLinhas() > 0)) {
        while (!$rsRecordSet->eof()) {
            $totalEmpenhadoAno += $rsRecordSet->getCampo( "empenhado_ano" );
            $totalAnuladoAno   += $rsRecordSet->getCampo( "anulado_ano"   );
            $totalPagoAno      += $rsRecordSet->getCampo( "pago_ano" );
            $totalLiquidadoAno += $rsRecordSet->getCampo( "liquidado_ano" );

            $totalEmpenhadoPeriodo += $rsRecordSet->getCampo( "empenhado_per" );
            $totalAnuladoPeriodo   += $rsRecordSet->getCampo( "anulado_per"   );
            $totalPagoPeriodo      += $rsRecordSet->getCampo( "pago_per" );
            $totalLiquidadoPeriodo += $rsRecordSet->getCampo( "liquidado_per" );

            $rsRecordSet->proximo();
        }

        $rsRecordSet->setPrimeiroElemento();

        while (!$rsRecordSet->eof()) {
            $this->obFOrcamentoRazaoDespesa->setDado("stExercicio",Sessao::getExercicio());
            $this->obFOrcamentoRazaoDespesa->setDado("inCodEntidade",$this->getCodEntidade());
            $this->obFOrcamentoRazaoDespesa->setDado("stDataInicio",$this->getDataInicial());
            $this->obFOrcamentoRazaoDespesa->setDado("stDataFim",$this->getDataFinal());
            $this->obFOrcamentoRazaoDespesa->setDado("inCodDotacao",$rsRecordSet->getCampo('cod_despesa'));
            $this->obFOrcamentoRazaoDespesa->setDado("boEmpenho",$this->getEmpenhoAnulacao());
            $this->obFOrcamentoRazaoDespesa->setDado("boLiquidacao",$this->getLiquidacaoAnulacao());
            $this->obFOrcamentoRazaoDespesa->setDado("boPagamento",$this->getPagamentoEstorno());
            $this->obFOrcamentoRazaoDespesa->setDado("boSuplementacao",$this->getSuplementacaoReducao());
            $this->obFOrcamentoRazaoDespesa->setDado("inCodConta",$rsRecordSet->getCampo('cod_conta'));
            $this->obFOrcamentoRazaoDespesa->setDado("stDestinacaoRecurso", $this->stDestinacaoRecurso );
            $this->obFOrcamentoRazaoDespesa->setDado("inCodDetalhamento", $this->inCodDetalhamento );

            /* CHAMADA DA FUNÇÃO ORCAMENTO RAZÃO DESPESA */
            $obErro = $this->obFOrcamentoRazaoDespesa->recuperaTodos( $rsRecordSet1, $stFiltro, $stOrder );

            $arRecord[0]['coluna1']  = "Dotação:";
            $arRecord[0]['coluna2']  = substr($rsRecordSet->getCampo('cod_despesa').' '.$rsRecordSet->getCampo('descricao'),0,80);

            if ($inTmp == 0) {
                $arRecord[0]['coluna3']  = "Dotação Inicial:";
                $arRecord[0]['coluna4']  = number_format($rsRecordSet->getCampo('vl_original'),2,',','.');
            } else {
                $arRecord[0]['coluna3']  = "";
                $arRecord[0]['coluna4']  = "";
            }

            $arRecord[1]['coluna1']  = "Orgão:";
            $arRecord[1]['coluna2']  = substr($rsRecordSet->getCampo('num_orgao').' '.$rsRecordSet->getCampo('nom_orgao'),0,80);
            if ($inTmp == 0) {
                $arRecord[1]['coluna3']  = "Crédito Suplementar no Período:";
                $arRecord[1]['coluna4']  = number_format($rsRecordSet->getCampo('suplementacoes'),2,',','.');
            } else {
                $arRecord[1]['coluna3'] = "";
                $arRecord[1]['coluna4'] = "";
            }

            $arRecord[2]['coluna1']  = "Unidade:";
            $arRecord[2]['coluna2']  = substr($rsRecordSet->getCampo('num_unidade').' '.$rsRecordSet->getCampo('nom_unidade'),0,80);
            if ($inTmp == 0) {
                $arRecord[2]['coluna3']  = "Redução Orçamentária no Período:";
                $arRecord[2]['coluna4']  = number_format($rsRecordSet->getCampo('reducoes'),2,',','.');
            } else {
                $arRecord[2]['coluna3'] = "";
                $arRecord[2]['coluna4'] = "";
            }

            $arRecord[3]['coluna1']  = "Função:";
            $arRecord[3]['coluna2']  = substr($rsRecordSet->getCampo('cod_funcao').' '.$rsRecordSet->getCampo('nom_funcao'),0,80);
            if ($inTmp == 0) {
                $arRecord[3]['coluna3']  = "Empenhado no Período:";
                $arRecord[3]['coluna4']  = number_format($totalEmpenhadoPeriodo,2,',','.');
            } else {
                $arRecord[0]['coluna3']  = "Empenhado no Período:";
                $arRecord[0]['coluna4']  = number_format($rsRecordSet->getCampo('empenhado_per'),2,',','.');
            }

            $arRecord[4]['coluna1']  = "SubFunção:";
            $arRecord[4]['coluna2']  = substr($rsRecordSet->getCampo('cod_subfuncao').' '.$rsRecordSet->getCampo('nom_subfuncao'),0,80);
            if ($inTmp == 0) {
                $arRecord[4]['coluna3']  = "Liquidado no Período:";
                $arRecord[4]['coluna4']  = number_format($totalLiquidadoPeriodo,2,',','.');
            } else {
                $arRecord[1]['coluna3']  = "Liquidado no Período:";
                $arRecord[1]['coluna4']  = number_format($rsRecordSet->getCampo('liquidado_per'),2,',','.');
            }

            $arRecord[5]['coluna1']  = "Programa:";
            $arRecord[5]['coluna2']  = substr($rsRecordSet->getCampo('num_programa').' '.$rsRecordSet->getCampo('nom_programa'),0,80);
            if ($inTmp == 0) {
                $arRecord[5]['coluna3']  = "Anulado no Período:";
                $arRecord[5]['coluna4']  = number_format($totalAnuladoPeriodo,2,',','.');
            } else {
                $arRecord[2]['coluna3']  = "Anulado no Período:";
                $arRecord[2]['coluna4']  = number_format($rsRecordSet->getCampo('anulado_per'),2,',','.');
            }

            $arRecord[6]['coluna1']  = "PAO:";
            $arRecord[6]['coluna2']  = substr($rsRecordSet->getCampo('num_acao').' '.$rsRecordSet->getCampo('nom_pao'),0,80);

            if ($inTmp == 0) {
                $arRecord[6]['coluna3']  = "Pago no Período:";
                $arRecord[6]['coluna4']  = number_format($totalPagoPeriodo,2,',','.');
            } else {
                $arRecord[3]['coluna3']  = "Pago no Período:";
                $arRecord[3]['coluna4']  = number_format($rsRecordSet->getCampo('pago_per'),2,',','.');
            }

            $arRecord[7]['coluna1']  = "Cat. Econômica:";
            $arRecord[7]['coluna2']  = substr($rsRecordSet->getCampo('classificacao').' '.$rsRecordSet->getCampo('descricao'),0,80);

            if ($inTmp == 0) {
                $arRecord[7]['coluna3']  = "Empenhado no Ano:";
                $arRecord[7]['coluna4']  = number_format($totalEmpenhadoAno,2,',','.');
            } else {
                $arRecord[4]['coluna3']  = "Empenhado no Ano:";
                $arRecord[4]['coluna4']  = number_format($rsRecordSet->getCampo('empenhado_ano'),2,',','.');
            }

            $arRecord[8]['coluna1']  = "Recurso:";
            $arRecord[8]['coluna2']  = $rsRecordSet->getCampo('num_recurso').' '.$rsRecordSet->getCampo('nom_recurso');
            if ($inTmp == 0) {
                $arRecord[8]['coluna3']  = "Liquidado no Ano:";
                $arRecord[8]['coluna4']  = number_format($totalLiquidadoAno,2,',','.');
            } else {
                $arRecord[5]['coluna3']  = "Liquidado no Ano:";
                $arRecord[5]['coluna4']  = number_format($rsRecordSet->getCampo('liquidado_ano'),2,',','.');
            }

            $arRecord[9]['coluna1']  = "";
            $arRecord[9]['coluna2']  = "";
            if ($inTmp == 0) {
                $arRecord[9]['coluna3']  = "Anulado no Ano:";
                $arRecord[9]['coluna4']  = number_format($totalAnuladoAno,2,',','.');
            } else {
                $arRecord[6]['coluna3']  = "Anulado no Ano:";
                $arRecord[6]['coluna4']  = number_format($rsRecordSet->getCampo('anulado_ano'),2,',','.');
            }

            $arRecord[10]['coluna1']  = "";
            $arRecord[10]['coluna2']  = "";
            if ($inTmp == 0) {
                $arRecord[10]['coluna3']  = "Pago no Ano:";
                $arRecord[10]['coluna4']  = number_format($totalPagoAno,2,',','.');
            } else {
                $arRecord[7]['coluna3']  = "Pago no Ano:";
                $arRecord[7]['coluna4']  = number_format($rsRecordSet->getCampo('pago_ano'),2,',','.');
            }

            $arRecord[11]['coluna1']  = "";
            $arRecord[11]['coluna2']  = "";
            if ($inTmp == 0) {
                $arRecord[11]['coluna3']  = "Saldo a Pagar:";
                $arRecord[11]['coluna4']  = number_format(($rsRecordSet->getCampo('liquidado_ano')-$rsRecordSet->getCampo('pago_ano')),2,',','.');
            } else {
                $arRecord[8]['coluna3']  = "Saldo a Pagar:";
                $arRecord[8]['coluna4']  = number_format(($rsRecordSet->getCampo('liquidado_ano')-$rsRecordSet->getCampo('pago_ano')),2,',','.');
            }

            $arRecord[12]['coluna1']  = "";
            $arRecord[12]['coluna2']  = "";
            if ($inTmp == 0) {
                $arRecord[12]['coluna3']  = "Saldo Disponível:";
                if ( !is_null($this->getCodDesdobramento()) && !is_null($this->getCodReduzido()) ) {
                    $arTotais = $rsRecordSet->getSomaCampo( 'empenhado_ano,anulado_ano' );
                    $arRecord[12]['coluna4']  = number_format(bcadd(bcsub($rsRecordSet->getCampo('total_creditos'),$arTotais['empenhado_ano'],4),$arTotais['anulado_ano'],4),2,',','.');
                } else {
                    $arRecord[12]['coluna4']  = number_format(bcadd(bcsub($rsRecordSet->getCampo('total_creditos'),$rsRecordSet->getCampo('empenhado_ano'),4),$rsRecordSet->getCampo('anulado_ano'),4),2,',','.');
                }
            } else {
                $arRecord[9]['coluna3'] = "";
                $arRecord[9]['coluna4'] = "";
            }

            if ($inTmp != 0) {
                $arRecord[10]['coluna3'] = "";
                $arRecord[10]['coluna4'] = "";

                $arRecord[11]['coluna3'] = "";
                $arRecord[11]['coluna4'] = "";

                $arRecord[12]['coluna3'] = "";
                $arRecord[12]['coluna4'] = "";
            }

            $arRecordSet[$inCount] = new RecordSet;
            $arRecordSet[$inCount]->preenche( $arRecord );

            $arTemp = array();
            $inCc = 0;

            $rsRecordSet1->setPrimeiroElemento();

            while ( !$rsRecordSet1->eof() ) {
                $arData=explode('-',$rsRecordSet1->getCampo('data'));
                $arTemp[$inCc]['data'] = $arData[2].'/'.$arData[1].'/'.$arData[0];
                $arTemp[$inCc]['historico'] = $rsRecordSet1->getcampo('historico');
                $stComplemento = explode('-',$rsRecordSet1->getCampo('complemento') );
                $arTemp[$inCc]['complemento'] = $stComplemento[0];
                if (( $rsRecordSet1->getCampo('tipo') == 'E' )||( $rsRecordSet1->getCampo('tipo') == 'L' )) {
                    $arTemp[$inCc]['contrapartida'] = $rsRecordSet1->getCampo('numcgm').' '.$rsRecordSet1->getCampo('nom_cgm');
                } elseif ( $rsRecordSet1->getCampo('tipo') == 'P' ) {
                      $arTemp[$inCc]['contrapartida'] = $rsRecordSet1->getCampo('numcgm').' '.$rsRecordSet1->getCampo('nom_cgm');
                }
                $arTemp[$inCc]['valor'] = number_format($rsRecordSet1->getCampo('valor'),2,',','.');
                $arTemp[$inCc]['dotacao'] = $rsRecordSet->getCampo('cod_despesa');
                $rsRecordSet1->proximo();
                $inCc++;
            }
            $rsRecordSet1 = new RecordSet;
            $rsRecordSet1->preenche($arTemp);

            $arRecordSet1[$inCount] = new RecordSet;
            $arRecordSet1[$inCount] = $rsRecordSet1;

            $rsRecordSet->proximo();
            $inCount++;
          //  $inTmp++;
        }

    } else {
        $obROrcamentoDespesa = new ROrcamentoDespesa;
//        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $this->getCodDesdobramento() );
//        $obROrcamentoDespesa->listarCodEstruturalDespesa( $rsDotacao );

//        $arCodigo = explode('.',$this->getCodDesdobramentoFinal() );
//        $x = count($arCodigo)-1;
//        while ( ((integer) $arCodigo[$x] == 0) && ($x > 0) ) {
//            $x--;
//        }
//        $arCodigo[$x] = preg_replace( "/[0-9]/","0",$arCodigo[$x]);
//        $stCodEstruturalMae = implode('.',$arCodigo);
//        $this->obFOrcamentoBalanceteDespesa->setDado("stCodEstruturalInicial",$stCodEstruturalMae);
//        $this->obFOrcamentoBalanceteDespesa->setDado("stCodEstruturalFinal"  ,$stCodEstruturalMae);
//		$obErro = $this->obFOrcamentoBalanceteDespesa->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

        $rsRecordSet = $rsDotacao = new RecordSet;
        $arRecord = array();

        $arRecord[0]['coluna1']  = "Dotação:";
        $arRecord[0]['coluna2']  = $rsRecordSet->getCampo('cod_despesa').' '.$rsDotacao->getCampo('descricao');
        $arRecord[0]['coluna3']  = "Dotação Inicial:";
        $arRecord[0]['coluna4']  = number_format($rsRecordSet->getCampo('vl_original'),2,',','.');

        $arRecord[1]['coluna1']  = "Orgão:";
        $arRecord[1]['coluna2']  = $rsRecordSet->getCampo('num_orgao').' '.$rsRecordSet->getCampo('nom_orgao');
        $arRecord[1]['coluna3']  = "Crédito Suplementar no Período:";
        $arRecord[1]['coluna4']  = number_format($rsRecordSet->getCampo('suplementacoes'),2,',','.');

        $arRecord[2]['coluna1']  = "Unidade:";
        $arRecord[2]['coluna2']  = $rsRecordSet->getCampo('num_unidade').' '.$rsRecordSet->getCampo('nom_unidade');
        $arRecord[2]['coluna3']  = "Redução Orçamentária no Período:";
        $arRecord[2]['coluna4']  = number_format($rsRecordSet->getCampo('reducoes'),2,',','.');

        $arRecord[3]['coluna1']  = "Função:";
        $arRecord[3]['coluna2']  = $rsRecordSet->getCampo('cod_funcao').' '.$rsRecordSet->getCampo('nom_funcao');
        $arRecord[3]['coluna3']  = "Empenhado no Período:";
        $arRecord[3]['coluna4']  = number_format($rsRecordSet->getCampo('empenhado_per'),2,',','.');

        $arRecord[4]['coluna1']  = "SubFunção:";
        $arRecord[4]['coluna2']  = $rsRecordSet->getCampo('cod_subfuncao').' '.$rsRecordSet->getCampo('nom_subfuncao');
        $arRecord[4]['coluna3']  = "Liquidado no Período:";
        $arRecord[4]['coluna4']  = number_format($rsRecordSet->getCampo('liquidado_per'),2,',','.');

        $arRecord[5]['coluna1']  = "Programa:";
        $arRecord[5]['coluna2']  = $rsRecordSet->getCampo('cod_programa').' '.$rsRecordSet->getCampo('nom_programa');
        $arRecord[5]['coluna3']  = "Anulado no Período:";
        $arRecord[5]['coluna4']  = number_format($rsRecordSet->getCampo('anulado_per'),2,',','.');

        $arRecord[6]['coluna1']  = "PAO:";
        $arRecord[6]['coluna2']  = $rsRecordSet->getCampo('num_pao').' '.$rsRecordSet->getCampo('nom_pao');
        $arRecord[6]['coluna3']  = "Pago no Período:";
        $arRecord[6]['coluna4']  = number_format($rsRecordSet->getCampo('pago_per'),2,',','.');

        $arRecord[7]['coluna1']  = "Cat. Econômica:";
        $arRecord[7]['coluna2']  = $rsDotacao->getCampo('cod_estrutural').' '.$rsDotacao->getCampo('descricao');
        $arRecord[7]['coluna3']  = "Empenhado no Ano:";
        $arRecord[7]['coluna4']  = number_format($rsRecordSet->getCampo('empenhado_ano'),2,',','.');

        $arRecord[8]['coluna1']  = "Recurso:";
        $arRecord[8]['coluna2']  = $rsRecordSet->getCampo('cod_recurso').' '.$rsRecordSet->getCampo('nom_recurso');
        $arRecord[8]['coluna3']  = "Liquidado no Ano:";
        $arRecord[8]['coluna4']  = number_format($rsRecordSet->getCampo('liquidado_ano'),2,',','.');

        $arRecord[9]['coluna1']  = "";
        $arRecord[9]['coluna2']  = "";
        $arRecord[9]['coluna3']  = "Anulado no Ano:";
        $arRecord[9]['coluna4']  = number_format($rsRecordSet->getCampo('anulado_ano'),2,',','.');

        $arRecord[10]['coluna1']  = "";
        $arRecord[10]['coluna2']  = "";
        $arRecord[10]['coluna3']  = "Pago no Ano:";
        $arRecord[10]['coluna4']  = number_format($rsRecordSet->getCampo('pago_ano'),2,',','.');

        $arRecord[11]['coluna1']  = "";
        $arRecord[11]['coluna2']  = "";
        $arRecord[11]['coluna3']  = "Saldo a Pagar:";
        $arRecord[11]['coluna4']  = number_format(($rsRecordSet->getCampo('liquidado_ano')-$rsRecordSet->getCampo('pago_ano')),2,',','.');

        $arRecord[12]['coluna1']  = "";
        $arRecord[12]['coluna2']  = "";
        $arRecord[12]['coluna3']  = "Saldo Disponível:";
        $arRecord[12]['coluna4']  = number_format(bcadd(bcsub($rsRecordSet->getCampo('total_creditos'),$rsRecordSet->getCampo('empenhado_ano'),4),$rsRecordSet->getCampo('anulado_ano'),4),2,',','.');

        $arRecordSet[$inCount] = new RecordSet;
        $arRecordSet[$inCount]->preenche( $arRecord );

        $rsRecordSet->proximo();
        $inCount++;
    }
}

}
?>
