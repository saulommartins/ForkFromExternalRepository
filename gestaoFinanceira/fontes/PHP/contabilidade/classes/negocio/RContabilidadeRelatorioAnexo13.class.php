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
    * Regra de negocio para anexo 13
    * Data de Criação   : 10/08/2005

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    * $Id: RContabilidadeRelatorioAnexo13.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                       );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                              );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeBalancoFinanceiro.class.php"            );

class RContabilidadeRelatorioAnexo13 extends PersistenteRelatorio
{
    /**
        * @var Object
        * @access Private
    */
    public $stFiltro;
    /**
        * @var Object
        * @access Private
    */
    public $stExercicio;
    /**
        * @var Object
        * @access Private
    */
    public $stDataInicial;
    /**
        * @var Object
        * @access Private
    */
    public $stTipoRelatorio;
    /**
        * @var Object
        * @access Private
    */
    public $stDataFinal;
    /**
        * @var Object
        * @access Private
    */
    public $inCodDemonstracaoDespesa;
    /**
        * @var Object
        * @access Private
    */
    public $obTContabilidadeBalancoFinanceiro;
    /**
        * @var Object
        * @access Private
    */
    public $obTOrcamentoEntidade;
    /**
        * @var String
        * @access Private
    */
    public $stEntidades;
    /**
        * @var Numeric
        * @access Private
    */
    public $nuVlVariacaoReceita;
    /**
        * @var Numeric
        * @access Private
    */
    public $nuVlVariacaoDespesa;
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setTContabilidadeBalancoFinanceiro($valor) { $this->obTContabilidadeBalancoFinanceiro = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setEntidades($valor) { $this->stEntidades      = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setTipoRelatorio($valor) { $this->stTipoRelatorio      = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setCodDemonstracaoDespesa($valor) { $this->inCodDemonstracaoDespesa   = $valor; }
    /**
         * @access Public
         * @param Object $valor
    */
    public function setFiltro($valor) { $this->stFiltro  = $valor; }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setExercicio($valor) { $this->stExercicio = $valor; }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setDataInicial($valor) { $this->stDataInicial        = $valor; }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setDataFinal($valor) { $this->stDataFinal          = $valor; }
    /**
        * @access Public
        * @param Numeric $valor
    */
    public function setVlVariacaoReceita($valor) { $this->nuVlVariacaoReceita  = $valor; }
    /**
        * @access Public
        * @param Numeric $valor
    */
    public function setVlVariacaoDespesa($valor) { $this->nuVlVariacaoDespesa  = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function getEntidades() { return $this->stEntidades                                 ; }
    /**
         * @access Public
         * @param String $valor
    */
    public function getTipoRelatorio() { return $this->stTipoRelatorio                               ; }
    /**
         * @access Public
         * @param String $valor
    */
    public function getCodDemonstracaoDespesa() { return $this->inCodDemonstracaoDespesa                    ; }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function getTContabilidadePlanoConta() { return $this->obTContabilidadePlanoConta ;}
    /**
        * @access Public
        * @param Integer $valor
    */
    public function getTContabilidadeBalancoFinanceiro() { return $this->obTContabilidadeBalancoFinanceiro ;}
    /**
         * @access Public
         * @param Object $valor
    */
    public function getFiltro() {return  $this->stFiltro  ; }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function getExercicio() {return  $this->stExercicio ; }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function getDataInicial() {return  $this->stDataInicial ; }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function getDataFinal() {return $this->stDataFinal ; }
    /**
        * @access Public
        * @return Numeric $valor
    */
    public function getVlVariacaoReceita() {return $this->nuVlVariacaoReceita; }
    /**
        * @access Public
        * @return Numeric $valor
    */
    public function getVlVariacaoDespesa() {return $this->nuVlVariacaoDespesa; }
    /**
        * Método Construtor
        * @access Private
    */
    public function RContabilidadeRelatorioAnexo13()
    {
          $this->setTContabilidadeBalancoFinanceiro  (new TContabilidadeBalancoFinanceiro   );
          $this->obROrcamentoEntidade             = new ROrcamentoEntidade;
    }

    public function geraRecordSet(&$rsRecordSet , $stFiltro = "" , $stOrder = "")
    {
        // $this->getCodDemonstracaoDespesa() = 1 (FUNÇÃO)
        // $this->getCodDemonstracaoDespesa() = 2 (CATEGORIA)

        $this->obTContabilidadeBalancoFinanceiro->setDado('exercicio'            ,$this->stExercicio              );
        $this->obTContabilidadeBalancoFinanceiro->setDado('dt_final'             ,$this->stDataFinal              );
        $this->obTContabilidadeBalancoFinanceiro->setDado('dt_inicial'           ,$this->stDataInicial            );
        $this->obTContabilidadeBalancoFinanceiro->setDado('cod_entidade'         ,$this->stEntidades              );
        $this->obTContabilidadeBalancoFinanceiro->setDado('stTipoRelatorio'      ,$this->stTipoRelatorio          );
        $this->obTContabilidadeBalancoFinanceiro->setDado('stDemonstracaoDespesa',$this->inCodDemonstracaoDespesa );
        $this->obTContabilidadeBalancoFinanceiro->recuperaTodos( $rsRecordSetReceita, $stFiltro , $stOrder );

        $this->obTContabilidadeBalancoFinanceiro->recuperaSaldoVariacao( $rsSaldoVariacao );

        $this->setVlVariacaoReceita( $rsSaldoVariacao->getCampo( 'variacao_receita' ) );
        $this->setVlVariacaoDespesa( $rsSaldoVariacao->getCampo( 'variacao_despesa' ) );

        $arRecordSet[0]['nivel_receita']       = 1;
        $arRecordSet[0]['nom_conta_receita']   = "I-ORÇAMENTÁRIA";
        $arRecordSet[0]['nom_conta_despesa']   = "I-ORÇAMENTÁRIA";

        $inCount     = 0;
        $inRecordSet = 0;

        while ( !$rsRecordSetReceita->eof() ) {

            // Monta saldo do exercicio Seguinte
            if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 9 ), array( '1.1.1.1.1', '1.1.1.1.2', '1.1.1.1.3' ) ) ) {
                while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 9 ), array( '1.1.1.1.1', '1.1.1.1.2', '1.1.1.1.3' ) ) ) {
                    if ( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 9 ) != $stCodEstruturalOld ) {
                        $arSaldoDespesa[$inCount]['nom_conta_despesa'] = $rsRecordSetReceita->getCampo( 'nom_conta' );
                        $arSaldoDespesa[$inCount]['nivel_despesa'    ] = $rsRecordSetReceita->getCampo( 'nivel'     )-2;
                    }

                    $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado' ), 4 );

                    $stCodEstruturalOld = substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 9 );
                    $rsRecordSetReceita->proximo();
                    if ( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 9 ) != $stCodEstruturalOld ) {
                        $arSaldoDespesa[$inCount]['valor_despesa'] = $nuValor;
                        $nuValor = 0;
                        $inCount++;
                    }
                }
            }

            // Monta valor de Credito a receber da receita
            if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 5 ), array( '1.1.2' ) ) ) {
                $nuValor = 0;
                $arCreditoReceber[ 'nom_conta_receita' ] = $rsRecordSetReceita->getCampo( 'nom_conta' );
                $arCreditoReceber[ 'nivel_receita'     ] = $rsRecordSetReceita->getCampo( 'nivel'     )-1;
                while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 5 ), array( '1.1.2' ) ) ) {
                    $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado_credito' ), 4 );
                    $nuValorDespesa = bcadd( $nuValorDespesa, $rsRecordSetReceita->getCampo( 'vl_arrecadado_debito' ), 4 );
                    $rsRecordSetReceita->proximo();
                }
                if($nuValor<0)
                    $arCreditoReceber[ 'valor_receita'     ] = abs($nuValor);
                else
                    $arCreditoReceber[ 'valor_receita'     ] = $nuValor;
                if($nuValorDespesa<0)
                    $arCreditoReceber[ 'valor_despesa'     ] = abs($nuValorDespesa);
                else
                    $arCreditoReceber[ 'valor_despesa'     ] = $nuValorDespesa;
                $nuValor = 0;
                $nuValorDespesa = 0;
            }

            // Monta saldo do exercicio Seguinte
            if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 5 ), array( '1.1.5' ) ) ) {
                $nuValor = 0;
                while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 5 ), array( '1.1.5' ) ) ) {
                    if ( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 5 ) != $stCodEstruturalOld ) {
                        $arSaldoDespesa[$inCount]['nom_conta_despesa'] = $rsRecordSetReceita->getCampo( 'nom_conta' );
                        $arSaldoDespesa[$inCount]['nivel_despesa'    ] = 3;
                    }

                    $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado' ), 4 );

                    $stCodEstruturalOld = substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 5 );
                    $rsRecordSetReceita->proximo();
                    if ( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 5 ) != $stCodEstruturalOld ) {
                        $arSaldoDespesa[$inCount]['valor_despesa'] = $nuValor;
                        $nuValor = 0;
                        $inCount++;
                    }
                }
            }

            // Monta Depósitos
            if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 5 ), array( '2.1.1' ) ) ) {
                $nuValor = 0;
                $arDeposito['nom_conta_receita'] = $rsRecordSetReceita->getCampo( 'nom_conta' );
                $arDeposito['nivel_receita'    ] = $rsRecordSetReceita->getCampo( 'nivel'     )-1;
                while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 5 ), array( '2.1.1' ) ) ) {
                    $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado_credito' ), 4 );
                    $nuValorD = bcadd( $nuValorD, $rsRecordSetReceita->getCampo( 'vl_arrecadado_debito' )*-1, 4 );
                    $rsRecordSetReceita->proximo();
                }
                $arDeposito['valor_receita'] = $nuValor;
                $arDeposito['valor_despesa'] = $nuValorD;
                $nuValor = 0;
                $nuValorD = 0;
            }

            // Monta Outras Obrigações
            if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 9 ), array( '2.1.2.1.9' ) ) ) {
                $nuValor = 0;
                $arOutrasObrigacoes['nom_conta_receita'] = 'OUTRAS OBRIGAÇÕES';
                $arOutrasObrigacoes['nivel_receita'    ] = 2;
                while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 9 ), array( '2.1.2.1.9' ) )  ) {
                    $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado_credito' ), 4 );
                    $nuValorD = bcadd( $nuValorD, $rsRecordSetReceita->getCampo( 'vl_arrecadado_debito' )*-1, 4 );
                    $rsRecordSetReceita->proximo();
                }
                $arOutrasObrigacoes['valor_receita'] = $nuValor;
                $arOutrasObrigacoes['valor_despesa'] = $nuValorD;
                $nuValor = 0;
                $nuValorD = 0;
            }
    /* */
            // Monta Depósitos
            if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 5 ), array(  '2.2.1' ) ) ) {
                $nuValor = 0;
                while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 5 ), array( '2.2.1' ) ) ) {
                    $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado_credito' ), 4 );
                    $nuValorD = bcadd( $nuValorD, $rsRecordSetReceita->getCampo( 'vl_arrecadado_debito' )*-1, 4 );
                    $rsRecordSetReceita->proximo();
                }
                $arDeposito['valor_receita'] += $nuValor;
                $arDeposito['valor_despesa'] += $nuValorD;
                $nuValor = 0;
                $nuValorD = 0;
            }
    /* */

            if ($this->getTipoRelatorio()=="E") {
                // Monta Valores de Liquidacao e liquidados a pagar
                if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 15 ), array( '2.9.2.4.1.04.01', '2.9.2.4.1.04.02' ) ) ) {
                    $nuValor = 0;
                    $arLiquidacao['nom_conta_receita'] = 'RESTOS A PAGAR';
                    $arLiquidacao['nivel_receita'    ] = 2;
                    while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 15 ), array( '2.9.2.4.1.04.01', '2.9.2.4.1.04.02' ) )  ) {
                        $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado' ), 4 );
                        $rsRecordSetReceita->proximo();
                    }
                  //if($nuValor<0)
                        $arLiquidacao['valor_receita'] = $nuValor;
                    //else
                    //  $arLiquidacao['valor_receita'] = $nuValor*(-1);
                    $nuValor = 0;
                }
            } elseif ($this->getTipoRelatorio()=="L") {
                // Monta Valores de Liquidacao e liquidados a pagar
                if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 15 ), array( '2.9.2.4.1.04.02' ) ) ) {
                    $nuValor = 0;
                    $arLiquidacao['nom_conta_receita'] = 'RESTOS A PAGAR';
                    $arLiquidacao['nivel_receita'    ] = 2;
                    while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 15 ), array( '2.9.2.4.1.04.02' ) )  ) {
                        $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado' ), 4 );
                        $rsRecordSetReceita->proximo();
                    }
                  //if($nuValor<0)
                        $arLiquidacao['valor_receita'] = $nuValor;
                  //else
                  //    $arLiquidacao['valor_receita'] = $nuValor*(-1);

                    $nuValor = 0;
                }
            } else {
                $arLiquidacao['nom_conta_receita'] = 'RESTOS A PAGAR';
                $arLiquidacao['nivel_receita'    ] = 2;
                $arLiquidacao['valor_receita'] = "0,00";
            }

            // Monta restos a pagar
            if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 ), array( '2.9.5.2' ) ) ) {
                $arTransferenciaDespesa['nom_conta_despesa'] = $rsRecordSetReceita->getCampo( 'nom_conta' );
                $arTransferenciaDespesa['nivel_despesa'    ] = $rsRecordSetReceita->getCampo( 'nivel'     )-2;
                while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 ), array( '2.9.5.2' ) ) ) {
                    $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado' ), 4 );
                    $rsRecordSetReceita->proximo();
                }
                $arTransferenciaDespesa['valor_despesa'] = number_format($nuValor,4,'.','');
            }

            $nuValor = 0;

            // Monta RecordSet com valores orçamentario da receita
            if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 3 ), array ( '4.1', '4.2', '4.7', '4.8', '4.9' ) ) OR  in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 1 ), array ( '9' ) ) ) {
                $nuValor = 0;
                while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 3 ), array ( '4.1', '4.2', '4.7', '4.8', '4.9' ) )OR  in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 1 ), array ( '9' ) )  ) {
                    if ( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 5 ) != $stCodEstruturalOld ) {
                        $arRsReceita[$inRecordSet][$inCount]['nom_conta_receita'] = $rsRecordSetReceita->getCampo( 'nom_conta' );
                        $arRsReceita[$inRecordSet][$inCount]['nivel_receita'    ] = $rsRecordSetReceita->getCampo( 'nivel'     );
                    }

                    $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado' ), 4 );

                    $stCodEstruturalOld = substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 5 );
                    $rsRecordSetReceita->proximo();
                    if ( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 5 ) != $stCodEstruturalOld ) {
                        if($arRsReceita[$inRecordSet][$inCount]['nivel_receita']==2)
                            $arRsReceita[$inRecordSet][$inCount]['valor_receita'] = "";
                        else
                           $arRsReceita[$inRecordSet][$inCount]['valor_receita'] = $nuValor;
                        $nuVlTotal = bcadd( $nuVlTotal, $nuValor, 4 );
                        $nuValor = 0;
                        $inCount++;
                        if ( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 3 ) != substr($stCodEstruturalOld, 0, 3 ) ) {
                            $arVlTotalReceita[$inRecordSet] = $nuVlTotal;
                            $nuVlTotal = 0;
                            $inRecordSet++;
                            $inCount = 0;
                        }
                    }
                }
                $nuValor = 0;
                // Monta receitas correntes intra
                if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 3 ), array ( '4.7' ) ) ) {
                    $arReceitaICorrente[$inCount]['nom_conta_receita'] = $rsRecordSetReceita->getCampo( 'nom_conta' );
                    $arReceitaICorrente[$inCount]['nivel_receita'    ] = $rsRecordSetReceita->getCampo( 'nivel'     );
                    while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 3 ), array ( '4.7' ) ) ) {
                        $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado' ), 4 );
                        $rsRecordSetReceita->proximo();
                    }

                    $arReceitaICorrente[$inCount]['valor_receita'] = number_format($nuValor,4,'.','');
                    $nuVlReceitaICorrente = number_format($nuValor,4,'.','');
                    $inCount++;
                }
                $nuValor = 0;
                // Monta receitas de capital intra
                if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 3 ), array ( '4.8' ) ) ) {
                    $arReceitaICapital[$inCount]['nom_conta_receita'] = $rsRecordSetReceita->getCampo( 'nom_conta' );
                    $arReceitaICapital[$inCount]['nivel_receita'    ] = $rsRecordSetReceita->getCampo( 'nivel'     );
                    while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 3 ), array ( '4.8' ) ) ) {
                        $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado' ), 4 );
                        $rsRecordSetReceita->proximo();
                    }

                    $arReceitaICapital[$inCount]['valor_receita'] = number_format($nuValor,4,'.','');
                    $nuVlReceitaICapital = number_format($nuValor,4,'.','');
                    $inCount++;
                }
                $nuValor = 0;
                if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 3 ), array ( '4.9', '9.1' ) ) ) {
                    $arRsReceita[$inRecordSet][$inCount]['nom_conta_receita'] = $rsRecordSetReceita->getCampo( 'nom_conta' );
                    $arRsReceita[$inRecordSet][$inCount]['nivel_receita'    ] = $rsRecordSetReceita->getCampo( 'nivel'     );
                    while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 3 ), array ( '4.9', '9.1' ) ) ) {
                        $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado' ), 4 );
                        $rsRecordSetReceita->proximo();
                    }
                    $arRsReceita[$inRecordSet][$inCount]['valor_receita'] = $nuValor;
                    $arVlTotalReceita[$inRecordSet] = $nuValor;
                    $inCount++;
                }

            }

            // Monta RecordSet de despesa extra-orcamentaria
            $inTemp = "";
            if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 ), array ( '5.2.1.9',  '5.2.2.2', '5.1.2.1'  ) ) ) {
                $arExtraDespesa[$inCount]['nom_conta_despesa'] = $arCreditoReceber['nom_conta_receita'];
                $arExtraDespesa[$inCount]['nivel_despesa'    ] = $arCreditoReceber['nivel_receita'];
                $arExtraDespesa[$inCount]['valor_despesa'    ] = $arCreditoReceber[ 'valor_despesa' ];
                $inCount++;

                $nuValor = 0;
                while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 ), array ( '5.2.1.9',  '5.2.2.2', '5.1.2.1' ) )  ) {
                    if ( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 ) != $stCodEstruturalOld and substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 )!='5.1.2.1') {
                        if ($rsRecordSetReceita->getCampo( 'nom_conta' )=='TRANSFERENCIAS FINANCEIRAS CONCEDIDAS') {
                            $inTemp = $inCount;
                        }
                        $arExtraDespesa[$inCount]['nom_conta_despesa'] = $rsRecordSetReceita->getCampo( 'nom_conta' );
                        $arExtraDespesa[$inCount]['nivel_despesa'    ] = $rsRecordSetReceita->getCampo( 'nivel'     )-2;
                    }

                    if (substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 )=='5.1.2.1') {
                        $nuValorTmp = bcadd( $nuValorTmp, abs($rsRecordSetReceita->getCampo( 'vl_arrecadado' )), 4 );

                    } else {
                        $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado' ), 4 );
                    }

                    $stCodEstruturalOld = substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 );
                    $rsRecordSetReceita->proximo();

                    if ( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 ) != $stCodEstruturalOld and substr($stCodEstruturalOld, 0, 7 )!='5.1.2.1') {
                        if($nuValor < 0)
                            $arExtraDespesa[$inCount]['valor_despesa'] = abs($nuValor);
                        else
                            $arExtraDespesa[$inCount]['valor_despesa'] = $nuValor;
                        $nuValor = 0;
                        $inCount++;
                    }
                }
                //$arExtraDespesa[$inTemp]['valor_despesa'] += $nuValorTmp;
                $arExtraDespesa[$inTemp]['valor_despesa'] = bcadd($arExtraDespesa[$inTemp]['valor_despesa'], $nuValorTmp, 4);
            }

            $nuVariacaoReceita = 0;

            // Receitas de outras entidades e transferencias
            if ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 ), array( '6.2.1.9', '6.2.2.2', '6.1.2.1' ) ) ) {
                $stCodEstruturalOld = '';
                $inCount            = 0;
                $nuValor            = 0;
                while ( in_array( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 ), array( '6.2.1.9', '6.2.2.2', '6.1.2.1' ) ) ) {
                    if ( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 ) != $stCodEstruturalOld and substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 )!='6.1.2.1') {
                        if ($rsRecordSetReceita->getCampo( 'nom_conta' )=='TRANSFERENCIAS FINANCEIRAS RECEBIDAS') {
                            $inTemp2 = $inCount;
                        }
                        $arTransferencias[$inCount]['nom_conta_receita'] = $rsRecordSetReceita->getCampo( 'nom_conta' );
                        $arTransferencias[$inCount]['nivel_receita'    ] = $rsRecordSetReceita->getCampo( 'nivel'     )-2;
                    }

                    if (substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 )=='6.1.2.1') {
                        $nuValorTmp2 = bcadd( $nuValorTmp2, abs($rsRecordSetReceita->getCampo( 'vl_arrecadado' )), 4 );

                    } else {
                        $nuValor = bcadd( $nuValor, $rsRecordSetReceita->getCampo( 'vl_arrecadado' ), 4 );
                    }

                    $stCodEstruturalOld = substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 );
                    $rsRecordSetReceita->proximo();
                    if ( substr( $rsRecordSetReceita->getCampo( 'cod_estrutural' ), 0, 7 ) != $stCodEstruturalOld and substr($stCodEstruturalOld, 0, 7 )!='6.1.2.1') {
                        $arTransferencias[$inCount]['valor_receita'] = $nuValor;
                        $nuValor = 0;
                        $inCount++;
                    }
                }
                //$arTransferencias[$inTemp2]['valor_receita'] += $nuValorTmp2;
                $arTransferencias[$inTemp2]['valor_receita'] = bcadd($arTransferencias[$inTemp2]['valor_receita'], $nuValorTmp2, 4);
            }

            $rsRecordSetReceita->proximo();
        }

        // Concatena os valores num unico array
        $count = 0;
        if (count($arRsReceita[4]) > 0) {
            $arRsReceitas = array();
            if (is_array($arRsReceita[5])) {
                if (!is_null($arRsReceita[5])) {
                    $arRsReceitas = array_merge($arRsReceita[4], $arRsReceita[5]);
                } else {
                    $arRsReceitas = $arRsReceita[4];
                }
            } else {
                $arRsReceitas = $arRsReceita[4];
            }
            $rsDedutoras = new RecordSet();
            $rsDedutoras->preenche($arRsReceitas);
            while ( !$rsDedutoras->eof() ) {
                $nuTotalDedutoras = $nuTotalDedutoras + $rsDedutoras->getCampo('valor_receita');
                $rsDedutoras->proximo();
            }
            $arRsDedutoras[0]['nom_conta_receita'] = "(R) DEDUCOES DA RECEITA CORRENTE";
            $arRsDedutoras[0]['nivel_receita'] = "";
            $arRsDedutoras[0]['valor_receita'] = number_format($nuTotalDedutoras,4,'.','');
        }

        // Concatena receitas 4.1 e 4.9
        $arRecordSet  = array_merge_recursive( $arRecordSet, $arRsReceita[0], $arRsDedutoras );

        $inCountTotal = count( $arRecordSet );
        $arRecordSet[$inCountTotal]  = $arRsReceita[2][0];

        $vlTotalReceita = bcadd( $arVlTotalReceita[0], $arVlTotalReceita[2], 4 );
        $vlTotalReceita = bcadd( $vlTotalReceita, $nuTotalDedutoras, 4 );

        // Totaliza receitas 4.1 e 4.9
        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = "SUB-TOTAL";
        $arRecordSet[$inCountTotal]['nivel_receita'    ] = 1;
        $arRecordSet[$inCountTotal]['total_receita'    ] = $vlTotalReceita;
        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = '';
        $inCountTotal++;

        $arRecordSet = array_merge_recursive( $arRecordSet, $arRsReceita[1] );
        $inCountTotal = count( $arRecordSet );
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = '';
        $inCountTotal++;

        // Monta valor da despesa de acordo com o tipo ( função ou categoria economioca )
        if ($this->inCodDemonstracaoDespesa == 1) {
            $obErro = $this->obTContabilidadeBalancoFinanceiro->recuperaDespesaPorFuncao( $rsDespesa );
        } else {
            $obErro = $this->obTContabilidadeBalancoFinanceiro->recuperaDespesaCategoriaEconomica( $rsDespesa );
        }

        if ( !$obErro->ocorreu() ) {
            $inCount = 1;
            while ( !$rsDespesa->eof() ) {
                if ( $rsDespesa->getCampo( 'vl_total' ) != 0 ) {
                    $arRecordSet[$inCount]['nom_conta_despesa'] = $rsDespesa->getCampo( 'descricao' );
                    $arRecordSet[$inCount]['nivel_despesa'    ] = 2;
                    $arRecordSet[$inCount]['valor_despesa'    ] = $rsDespesa->getCampo( 'vl_total'  );
                    $nuValorDespesa = bcadd( $nuValorDespesa, $rsDespesa->getCampo( 'vl_total' ), 4 );
                    $inCount++;
                }
                $rsDespesa->proximo();
            }
        }

        // TOTALIZADORES
        $inCountTotal = count( $arRecordSet );
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = 'SUB-TOTAL';
        $arRecordSet[$inCountTotal]['total_receita'    ] = $arVlTotalReceita[1];
        $arRecordSet[$inCountTotal]['nom_conta_despesa'] = 'SUB-TOTAL';
        $arRecordSet[$inCountTotal]['total_despesa'    ] = $nuValorDespesa;
        $inCountTotal++;

        $nuTotalReceita = bcadd( $arVlTotalReceita[0], bcadd( $arVlTotalReceita[1], bcadd($arVlTotalReceita[2], $nuTotalDedutoras,4), 4 ), 4 );
        $nuTotalDespesa = $nuValorDespesa;

        if (	count($arReceitaICorrente) > 0 ) {
            // ALTERACOES PARA INTRA-ORCAMENTARIAS
            // RECEITAS CORRENTES INTRA-ORÇAMENTÁRIAS
            $arRecordSet[$inCountTotal]['nom_conta_receita'] = '';
            $inCountTotal++;
            $arRecordSet  = array_merge_recursive( $arRecordSet, $arReceitaICorrente );
            $inCountTotal++;
            $arRecordSet[$inCountTotal]['nom_conta_receita'] = '';
            $inCountTotal++;
            if ($nuVlReceitaICorrente) {
                $arRecordSet[$inCountTotal]['nom_conta_receita'] = 'SUB-TOTAL';
                $arRecordSet[$inCountTotal]['total_receita'    ] = $nuVlReceitaICorrente;
            }
            $inCountTotal++;

            // RECEITAS DE CAPITAL INTRA-ORÇAMENTÁRIAS e DESPESAS INTRA-ORÇAMENTÁRIAS
            $arRecordSet[$inCountTotal]['nom_conta_receita'] = '';
            $inCountTotal++;
            $arRecordSet  = array_merge_recursive( $arRecordSet, $arReceitaICapital );
            $inCountTotal++;
            $arRecordSet[$inCountTotal]['nom_conta_receita'] = '';
            $inCountTotal++;
            if ($nuVlReceitaICapital) {
                $arRecordSet[$inCountTotal]['nom_conta_receita'] = 'SUB-TOTAL';
                $arRecordSet[$inCountTotal]['total_receita'    ] = $nuVlReceitaICapital;
            }
            $inCountTotal++;
        }

        // Soma receitas intra capital e corrente ao total receita
        $nuTotalReceita = bcadd($nuTotalReceita,$nuVlReceitaICorrente,4);
        $nuTotalReceita = bcadd($nuTotalReceita,$nuVlReceitaICapital,4);

        // Soma despesas intra com total despesa
        //$nuTotalDespesa = bcadd($nuTotalDespesa,$nuVlDespesaI,4);

        $arRecordSet[$inCountTotal] = '';
        $inCountTotal++;

        $arRecordSet[$inCountTotal]['nom_conta_receita'] = 'TOTAL';
        $arRecordSet[$inCountTotal]['total_receita'    ] = $nuTotalReceita;
        $arRecordSet[$inCountTotal]['nom_conta_despesa'] = 'TOTAL';
        $arRecordSet[$inCountTotal]['total_despesa'    ] = $nuTotalDespesa;
        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = '';

        //Concatena Extra-Orçamentarias

        $arExtraOrcamentario[0]['nom_conta_receita'] = 'II-EXTRA-ORÇAMENTÁRIA';
        $arExtraOrcamentario[0]['nom_conta_despesa'] = 'II-EXTRA-ORÇAMENTÁRIA';

        $arExtraOrcamentario[1] = $arCreditoReceber;
        $arExtraOrcamentario[2] = $arTransferencias[0];
        $arExtraOrcamentario[3] = $arTransferencias[1];
        $arExtraOrcamentario[4] = $arLiquidacao;
        $arExtraOrcamentario[5] = $arDeposito;
        $arExtraOrcamentario[6] = $arOutrasObrigacoes;

        // CONCATENA EXTRA ORCAMENTARIA DA DESPESA
        $arExtraDespesa[] = $arTransferenciaDespesa;
        $inCount = 1;
        foreach ($arExtraDespesa as $arExtra) {
            $arExtraOrcamentario[$inCount]['nom_conta_despesa'] = $arExtra['nom_conta_despesa'];
            $arExtraOrcamentario[$inCount]['nivel_despesa'    ] = $arExtra['nivel_despesa'    ];
            $arExtraOrcamentario[$inCount]['valor_despesa'    ] = $arExtra['valor_despesa'    ];
            $nuValorExtraDespesa = bcadd( $nuValorExtraDespesa, $arExtra['valor_despesa'    ], 4 );
            $inCount++;
        }
        $arExtraOrcamentario[$inCount]['nom_conta_despesa'] = $arDeposito['nom_conta_receita'];
        $arExtraOrcamentario[$inCount]['nivel_despesa'    ] = $arDeposito['nivel_receita'    ];
        $arExtraOrcamentario[$inCount]['valor_despesa'    ] = $arDeposito['valor_despesa'    ];

        $nuValorExtraReceita = bcadd( $arCreditoReceber['valor_receita'], bcadd( $arTransferencias[0]['valor_receita'], $arTransferencias[1]['valor_receita'], 4 ), 4 );
        $nuValorExtraReceita = bcadd( $nuValorExtraReceita, bcadd( $arLiquidacao['valor_receita'], $arDeposito['valor_receita'], 4 ), 4 );
        $nuValorExtraDespesa = bcadd( $nuValorExtraDespesa, $arDeposito['valor_despesa'], 4 );

        $inCount++;
        $arExtraOrcamentario[$inCount]['nom_conta_despesa'] = $arOutrasObrigacoes['nom_conta_receita'];
        $arExtraOrcamentario[$inCount]['nivel_despesa'    ] = $arOutrasObrigacoes['nivel_receita'    ];
        $arExtraOrcamentario[$inCount]['valor_despesa'    ] = $arOutrasObrigacoes['valor_despesa'    ];

        $nuValorExtraReceita = bcadd( $nuValorExtraReceita, $arOutrasObrigacoes['valor_receita'], 4 );
        $nuValorExtraDespesa = bcadd( $nuValorExtraDespesa, $arOutrasObrigacoes['valor_despesa'], 4 );

        // FAZ
        $inCount++;
        $arExtraOrcamentario[$inCount]['nom_conta_receita'] = 'VARIAÇÃO FINANCEIRA';
        $arExtraOrcamentario[$inCount]['valor_receita'    ] = $this->nuVlVariacaoReceita;
        $arExtraOrcamentario[$inCount]['nivel_receita'    ] = 2;
        $arExtraOrcamentario[$inCount]['nom_conta_despesa'] = 'VARIAÇÃO FINANCEIRA';
        $arExtraOrcamentario[$inCount]['valor_despesa'    ] = $this->nuVlVariacaoDespesa;
        $arExtraOrcamentario[$inCount]['nivel_despesa'    ] = 2;

        $nuValorExtraReceita = bcadd( $nuValorExtraReceita, $this->nuVlVariacaoReceita, 4 );
        $nuValorExtraDespesa = bcadd( $nuValorExtraDespesa, $this->nuVlVariacaoDespesa, 4 );

        $inCount++;
        $arExtraOrcamentario[$inCount]['nom_conta_despesa'] = "";
        $arExtraOrcamentario[$inCount]['nivel_despesa'    ] = "";
        $arExtraOrcamentario[$inCount]['valor_despesa'    ] = "";

        $arRecordSet = array_merge_recursive( $arRecordSet, $arExtraOrcamentario );

        // TOTALIZADORES DO EXTRA ORCAMENTARIO
        $inCountTotal = count( $arRecordSet );
        $arRecordSet[$inCountTotal]['nom_conta_despesa'] = '';
        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_despesa'] = 'SUB-TOTAL';
        $arRecordSet[$inCountTotal]['total_despesa']     = $nuValorExtraDespesa;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = 'SUB-TOTAL';
        $arRecordSet[$inCountTotal]['total_receita']     = $nuValorExtraReceita;

        $nuTotalReceita = bcadd( $nuTotalReceita, $nuValorExtraReceita, 4 );
        $nuTotalDespesa = bcadd( $nuTotalDespesa, $nuValorExtraDespesa, 4 );

        $inCountTotal++;
        $arRecordSet[$inCountTotal] = '';
        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = 'TOTAL';
        $arRecordSet[$inCountTotal]['total_receita'    ] = $nuValorExtraReceita;
        $arRecordSet[$inCountTotal]['nom_conta_despesa'] = 'TOTAL';
        $arRecordSet[$inCountTotal]['total_despesa'    ] = $nuValorExtraDespesa;
        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = '';

        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = 'SALDO DO EXERCÍCIO ANTERIOR';
        $arRecordSet[$inCountTotal]['nom_conta_despesa'] = 'SALDO P/ EXERCÍCIO SEGUINTE';
        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = 'DISPONíVEL';
        $arRecordSet[$inCountTotal]['nivel_receita']     = 2;
        $arRecordSet[$inCountTotal]['nom_conta_despesa'] = 'DISPONíVEL';
        $arRecordSet[$inCountTotal]['nivel_despesa']     = 2;

        // Seta data para um dia anterior da data selecionada.
        // Se for 1º de Janeiro, a data inicial será 31 de Dezembro do ano anterior.
        list( $stDia, $stMes, $stAno ) = explode( '/', $this->stDataInicial );

        $stDataFinal = date( 'd/m/Y', mktime( 0,0,0, $stMes, $stDia-1, $stAno ) );

        if ($stMes == "01" && $stDia == "01") {
            $this->obTContabilidadeBalancoFinanceiro->setDado( 'dt_final'  , '01/01/'.$stAno );
            $this->obTContabilidadeBalancoFinanceiro->setDado( 'dt_inicial', $stDataFinal );
        } else {
            $this->obTContabilidadeBalancoFinanceiro->setDado( 'dt_final'  , $stDataFinal );
            $this->obTContabilidadeBalancoFinanceiro->setDado( 'dt_inicial', '01/01/'.$stAno );
        }

        $obErro = $this->obTContabilidadeBalancoFinanceiro->recuperaSaldoReceita( $rsSaldoReceita );

        if ( !$obErro->ocorreu() ) {
            $inCount = 0;
            /* SALDO ANTERIOR */
            while ( !$rsSaldoReceita->eof() ) {
                if ( substr( $rsSaldoReceita->getCampo( 'cod_estrutural' ), 0, 5 ) == '1.1.5' ) {
                    $nuVlSaldo = 0;
                    $stNomConta = $rsSaldoReceita->getCampo( 'nom_conta' );
                    while ( substr( $rsSaldoReceita->getCampo( 'cod_estrutural' ), 0, 5 ) == '1.1.5' ) {
                        // O saldo anterior correto vem na coluna vl_arrecadado_debito
                        // Se não for 01/01, tem q considerar a movimentação do período, que o campo vl_arrecadado tras.
                        if ($stMes == "01" && $stDia == "01") {
                            $nuValorSaldo = bcadd( $nuValorSaldo, $rsSaldoReceita->getCampo( 'vl_arrecadado_debito' ), 4 );
                        } else {
                            $nuValorSaldo = bcadd( $nuValorSaldo, $rsSaldoReceita->getCampo( 'vl_arrecadado' ), 4 );
                        }
                        $rsSaldoReceita->proximo();
                    }
                    $arSaldo[$inCount]['nom_conta_receita'] = $stNomConta;
                    $arSaldo[$inCount]['nivel_receita'    ] = 3;
                    $arSaldo[$inCount]['valor_receita'    ] = $nuValorSaldo;
                    $nuVlSaldoReceita = bcadd( $nuVlSaldoReceita, $nuValorSaldo,4);
                } else {
                    $arSaldo[$inCount]['nom_conta_receita'] = $rsSaldoReceita->getCampo( 'nom_conta'     );
                    $arSaldo[$inCount]['nivel_receita'    ] = $rsSaldoReceita->getCampo( 'nivel'         )-2;

                    // Idem comentário acima.
                    if ($stMes == "01" && $stDia == "01") {
                        $arSaldo[$inCount]['valor_receita'    ] = $rsSaldoReceita->getCampo( 'vl_arrecadado_debito' );
                        $nuVlSaldoReceita = bcadd( $nuVlSaldoReceita, $rsSaldoReceita->getCampo( 'vl_arrecadado_debito' ) , 4);
                    } else {
                        $arSaldo[$inCount]['valor_receita'    ] = $rsSaldoReceita->getCampo( 'vl_arrecadado' );
                        $nuVlSaldoReceita = bcadd( $nuVlSaldoReceita, $rsSaldoReceita->getCampo( 'vl_arrecadado' ) , 4);
                    }
                }
                $inCount++;
                $rsSaldoReceita->proximo();
            }
        }
        $inCount = 0;
        foreach ($arSaldo as $arSaldoReceita) {
            $arSaldo[$inCount]['nom_conta_despesa'] = $arSaldoDespesa[$inCount]['nom_conta_despesa'];
            $arSaldo[$inCount]['nivel_despesa']     = $arSaldoDespesa[$inCount]['nivel_despesa'    ];
            $arSaldo[$inCount]['valor_despesa']     = bcadd( ($arSaldoDespesa[$inCount]['valor_despesa']*(-1)), $arSaldoReceita['valor_receita'], 4 );
            $nuVlSaldoDespesa = bcadd( $nuVlSaldoDespesa, $arSaldo[$inCount]['valor_despesa'], 4 );
            $inCount++;
        }

        $arRecordSet = array_merge_recursive( $arRecordSet, $arSaldo );

        // MONTA TOTLIZADORES
        $inCountTotal = count( $arRecordSet );
        $arRecordSet[$inCountTotal]['nom_conta_despesa'] = '';
        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_despesa'] = 'SUB-TOTAL';
        $arRecordSet[$inCountTotal]['total_despesa']     = $nuVlSaldoDespesa;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = 'SUB-TOTAL';
        $arRecordSet[$inCountTotal]['total_receita']     = $nuVlSaldoReceita;

        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_despesa'] = '';
        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_despesa'] = 'TOTAL';
        $arRecordSet[$inCountTotal]['total_despesa']     = $nuVlSaldoDespesa;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = 'TOTAL';
        $arRecordSet[$inCountTotal]['total_receita']     = $nuVlSaldoReceita;

        $nuTotalReceita = bcadd( $nuTotalReceita, $nuVlSaldoReceita, 4 );
        $nuTotalDespesa = bcadd( $nuTotalDespesa, $nuVlSaldoDespesa, 4 );

        $inCountTotal++;
        $arRecordSet[$inCountTotal] = '';
        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = 'TOTAL GERAL';
        $arRecordSet[$inCountTotal]['total_receita'    ] = $nuTotalReceita;
        $arRecordSet[$inCountTotal]['nom_conta_despesa'] = 'TOTAL GERAL';
        $arRecordSet[$inCountTotal]['total_despesa'    ] = $nuTotalDespesa;

        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = '';
        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = 'Entidades Relacionadas';
        $inCountTotal++;
        $arEntidade = explode( ',', $this->stEntidades );
        $inCountEnt = 0;
        foreach ($arEntidade as $inCodEntidade) {
            $this->obROrcamentoEntidade->setExercicio     ( $this->stExercicio );
            $this->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade     );
            $this->obROrcamentoEntidade->consultarNomes( $rsEntidade );
            $arTmpEntidade[$inCountEnt]['cod_entidade'] = $inCodEntidade;
            $arTmpEntidade[$inCountEnt]['nom_entidade'] = $rsEntidade->getCampo( 'entidade' );
            $inCountEnt++;
        }
        $inCountTotal = count( $arRecordSet );
        foreach ($arTmpEntidade as $arEntidade) {
            $arRecordSet[$inCountTotal]['nom_conta_receita'] = $arEntidade['nom_entidade'];
            $inCountTotal++;
            $rsEntidade->proximo();
        }

        $arRecordSet[$inCountTotal]['nom_conta_receita'] = '';
        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = '_____________________________________________';
        $arRecordSet[$inCountTotal]['nom_conta_despesa'] = '_____________________________________________';
        $inCountTotal++;
        $arRecordSet[$inCountTotal]['nom_conta_receita'] = '                Assinatura do Representante';
        $arRecordSet[$inCountTotal]['nom_conta_despesa'] = '            Assinatura do Contador Responsável';

        $rsRecordSet = new RecordSet();
        $rsRecordSet->preenche( $arRecordSet );

        return $obErro;

    }

}
