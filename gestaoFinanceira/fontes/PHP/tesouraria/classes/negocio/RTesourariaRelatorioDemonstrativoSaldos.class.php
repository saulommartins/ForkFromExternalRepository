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
    * Classe Relatório Demonstrativo Saldos
    * Data de Criação   : 23/08/2006

    * @author Analista:
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30835 $
    $Name$
    $Autor:$
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.24
*/

/*
$Log$
Revision 1.4  2006/12/05 16:21:17  cako
Bug #7239#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO );
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_GF_TES_MAPEAMENTO."FTesourariaDemonstrativoSaldos.class.php" );
include_once (CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");

/**
    * Classe de Regra de Negócios Demonstrativo Saldos
    * @author Desenvolvedor: Tonismar Régis Bernardo
*/

class RTesourariaRelatorioDemonstrativoSaldos extends PersistenteRelatorio
{
    /**
        * @var Integer
        * @access Private
    */
    public $inCodEntidade;

    /**
        * @var Date
        * @access Private
    */
    public $dtInicioPeriodo;

    /**
        * @var Date
        * @access Private
    */
    public $dtFimPeriodo;

    /**
        * @var Integer
        * @access Private
    */
    public $inInicioReduzido;

    /**
        * @var Integer
        * @access Private
    */
    public $inFimReduzido;

    /**
        * @var String
        * @access Private
    */
    public $stInicioEstrutural;

    /**
        * @var String
        * @access Private
    */
    public $stFimEstrutural;

    /**
        * @var Integer
        * @access Private
    */
    public $inCodRecurso;

    /**
        * @var String
        * @access Private
    */
    public $stOrdenacao;

    /**
        * @var Boolean
        * @access Private
    */
    public $boSemMovimentacao;

    /**
        * @var Boolean
        * @access Private
    */
    public $boAgruparContaCorrente;

    /**
        * @var Object
        * @access Private
    */
    public $obFTesourariaDemonstrativoSaldos;

    /**
        * @var String
        * @access Private
    */
    public $stExercicio;

    public $stDestinacaoRecurso;
    public $inCodDetalhamento;

    /** Setters */
    public function setCodEntidade($valor) { $this->inCodEntidade = $valor;      }
    public function setInicioPeriodo($valor) { $this->dtInicioPeriodo = $valor;    }
    public function setFimPeriodo($valor) { $this->dtFimPeriodo = $valor;       }
    public function setInicioReduzido($valor) { $this->inInicioReduzido = $valor;   }
    public function setFimReduzido($valor) { $this->inFimReduzido = $valor;      }
    public function setInicioEstrutural($valor) { $this->stInicioEstrutural = $valor; }
    public function setFimEstrutural($valor) { $this->stFimEstrutural = $valor;    }
    public function setCodRecurso($valor) { $this->inCodRecurso = $valor;       }
    public function setOrdenacao($valor) { $this->stOrdenacao = $valor;        }
    public function setSemMovimentacao($valor) { $this->boSemMovimentacao = $valor;  }
    public function setExercicio($valor) { $this->stExercicio = $valor;        }
    public function setDestinacaoRecurso($valor) { $this->stDestinacaoRecurso = $valor;}
    public function setCodDetalhamento($valor) { $this->inCodDetalhamento = $valor;  }
    public function setAgruparContaCorrente($valor) { $this->boAgruparContaCorrente = $valor;  }

    /** Getters */
    public function getCodEntidade() { return $this->inCodEntidade;      }
    public function getInicioPeriodo() { return $this->dtInicioPeriodo;    }
    public function getFimPeriodo() { return $this->dtFimPeriodo;       }
    public function getInicioReduzido() { return $this->inInicioReduzido;   }
    public function getFimReduzido() { return $this->inFimReduzido;      }
    public function getInicioEstrutural() { return $this->stInicioEstrutural; }
    public function getFimEstrutural() { return $this->stFimEstrutural;    }
    public function getCodRecurso() { return $this->inCodRecurso;       }
    public function getOrdenacao() { return $this->stOrdenacao;        }
    public function getSemMovimento() { return $this->boSemMovimentacao;  }
    public function getAgruparContaCorrente() { return $this->boAgruparContaCorrente;  }
    public function getExercicio() { return $this->stExercicio;        }

    /**
        * Método Constructor
        * @access Private
    */
    public function RTesourariaRelatorioDemonstrativoSaldos()
    {
        $this->obRRelatorio = new RRelatorio;
        $this->obFTesourariaDemonstrativoSaldos = new FTesourariaDemonstrativoSaldos;
    }

    public function geraRecordSetBanco(&$arRelatorio)
    {
        
        $this->obFTesourariaDemonstrativoSaldos->setDado('inCodEntidade', $this->getCodEntidade());
        $this->obFTesourariaDemonstrativoSaldos->setDado('stExercicio', $this->getExercicio());
        $this->obFTesourariaDemonstrativoSaldos->setDado('dtDataInicio',$this->getInicioPeriodo());
        $this->obFTesourariaDemonstrativoSaldos->setDado('dtDataFim', $this->getFimPeriodo());
        if ( $this->getInicioEstrutural() ) {
            $this->obFTesourariaDemonstrativoSaldos->setDado('stCodEstruturalInicio', $this->getInicioEstrutural());
        }

        if ( $this->getFimEstrutural() ) {
            $this->obFTesourariaDemonstrativoSaldos->setDado('stCodEstruturalFim', $this->getFimEstrutural());
        }

        if ( $this->getInicioReduzido() ) {
            $this->obFTesourariaDemonstrativoSaldos->setDado('inCodReduzidoInicio', $this->getInicioReduzido());
        }

        if ( $this->getFimReduzido() ) {
            $this->obFTesourariaDemonstrativoSaldos->setDado('inCodReduzidoFim', $this->getFimReduzido());
        }

        if ( $this->getCodRecurso() ) {
            $this->obFTesourariaDemonstrativoSaldos->setDado('inCodRecurso', $this->getCodRecurso());
        }
        if ( $this->getSemMovimento() ) {
            $this->obFTesourariaDemonstrativoSaldos->setDado('boSemMovimento', $this->getSemMovimento() );
        }
        if ( $this->getOrdenacao() ) {
            $stOrderBy = $this->getOrdenacao();
        }

        $this->obFTesourariaDemonstrativoSaldos->setDado('stDestinacaoRecurso', $this->stDestinacaoRecurso );
        $this->obFTesourariaDemonstrativoSaldos->setDado('inCodDetalhamento', $this->inCodDetalhamento );
        $this->obFTesourariaDemonstrativoSaldos->setDado('boUtilizaEstruturalTCE', 'false' );

        if (Sessao::getExercicio() > '2012') {
            $this->obFTesourariaDemonstrativoSaldos->setDado('boUtilizaEstruturalTCE', 'true' );
        }
        
        $obErro = $this->obFTesourariaDemonstrativoSaldos->recuperaDemonstrativoSaldos( $rsDemonstrativoSaldos, $stOrderBy, $boTransacao );    

        if ( !$obErro->ocorreu() ) {
            $inCount = 0;
            $nuVlMBSaldoAnteriorTotal = '0.00';
            $nuVlMBCreditoTotal       = '0.00';
            $nuVlMBDebitoTotal        = '0.00';
            $nuVlMBSaldoAtualTotal    = '0.00';
            $nuVlMBSaldoAnteriorSubTotal = '0.00';
            $nuVlMBSaldoAtualSubTotal = '0.00';

            $nuVlMBSaldoAnteriorTotalRecurso = '0.00';
            $nuVlMBDebitoTotalRecurso        = '0.00';
            $nuVlMBCreditoTotalRecurso       = '0.00';
            $nuVlMBSaldoAtualTotalRecurso    = '0.00';

            $inArmazenaAnteriorCodRecurso = 0;

            $boMostrarUltimoSubTotalRecurso = false;
            while ( !$rsDemonstrativoSaldos->eof() ) {

                if ( $this->getSemMovimento() == 'N') {
                    if ($rsDemonstrativoSaldos->getCampo("saldo_anterior") != '0.00' OR $rsDemonstrativoSaldos->getCampo("vl_debito") != '0.00' OR $rsDemonstrativoSaldos->getCampo("vl_credito") != '0.00' ) {
                        $boListar = true;
                    } else {
                        $boListar = false;
                        $rsDemonstrativoSaldos->proximo();
                    }
                } else $boListar = true;

                if ($boListar) {
                    if ($this->getOrdenacao() == "recurso") {
                        if ($rsDemonstrativoSaldos->getCampo( "cod_recurso") != $inArmazenaAnteriorCodRecurso) {
                            if ($inCount > 0) {
                                $arDemonstrativoSaldos[$inCount]["cod_estrutural"] = "Sub-Total";
                                $arDemonstrativoSaldos[$inCount]["saldo_anterior"] = $nuVlMBSaldoAnteriorTotalRecurso;
                                $arDemonstrativoSaldos[$inCount]["vl_debito"     ] = $nuVlMBDebitoTotalRecurso;
                                $arDemonstrativoSaldos[$inCount]["vl_credito"    ] = ( abs($nuVlMBCreditoTotalRecurso) ) ? abs($nuVlMBCreditoTotalRecurso) : '0.00';
                                $arDemonstrativoSaldos[$inCount]["saldo_atual"   ] = $nuVlMBSaldoAtualTotalRecurso;
                                $inCount++;

                                $nuVlMBSaldoAnteriorTotalRecurso = '0.00';
                                $nuVlMBDebitoTotalRecurso        = '0.00';
                                $nuVlMBCreditoTotalRecurso       = '0.00';
                                $nuVlMBSaldoAtualTotalRecurso    = '0.00';

                                $boMostrarUltimoSubTotalRecurso = true;

                                $arDemonstrativoSaldos[$inCount]["cod_estrutural"] = "";
                                $inCount++;
                            }

                            $inArmazenaAnteriorCodRecurso = $rsDemonstrativoSaldos->getCampo( "cod_recurso" );
                            $arDemonstrativoSaldos[$inCount]["cod_recurso"] = "Recurso : " . $rsDemonstrativoSaldos->getCampo( "cod_recurso" );
                            $arDemonstrativoSaldos[$inCount]["cod_plano"  ] = "-----";
                            $arDemonstrativoSaldos[$inCount]["nom_recurso"] = $rsDemonstrativoSaldos->getCampo( "nom_recurso" );
                            $inCount++;
                        }
                    }

                    $arDemonstrativoSaldos[$inCount]["cod_estrutural"] = $rsDemonstrativoSaldos->getCampo( "cod_estrutural" );
                    $arDemonstrativoSaldos[$inCount]["cod_plano"]      = $rsDemonstrativoSaldos->getCampo( "cod_plano"      );
                    $arDemonstrativoSaldos[$inCount]["saldo_anterior"] = $rsDemonstrativoSaldos->getCampo( "saldo_anterior" );
                    if ($boLiberado) {
                        $arDemonstrativoSaldos[$inCount]["vl_debito"]  = $rsDemonstrativoSaldos->getCampo("vl_debito");
                        $arDemonstrativoSaldos[$inCount]["vl_credito"] = abs($rsDemonstrativoSaldos->getCampo("vl_credito"));
                    } else {
                        $arDemonstrativoSaldos[$inCount]["vl_debito"]  = bcadd( $rsDemonstrativoSaldos->getCampo("vl_debito"), $arSaldoContaArrecadacao[$rsDemonstrativoSaldos->getCampo("cod_plano")]['vl_conta_debito'], 4 );
                        $arDemonstrativoSaldos[$inCount]["vl_credito"] = abs(bcadd( abs($rsDemonstrativoSaldos->getCampo("vl_credito")), $arSaldoContaArrecadacao[$rsDemonstrativoSaldos->getCampo("cod_plano")]['vl_conta_credito'], 4 ));
                    }
                    $arDemonstrativoSaldos[$inCount]["vl_credito"] = ( $arDemonstrativoSaldos[$inCount]["vl_credito"] ) ? $arDemonstrativoSaldos[$inCount]["vl_credito"] : '0.00';
                    $arDemonstrativoSaldos[$inCount]["saldo_atual"] = bcsub( bcadd($rsDemonstrativoSaldos->getCampo( "saldo_anterior" ), $arDemonstrativoSaldos[$inCount]["vl_debito"], 4 ), $arDemonstrativoSaldos[$inCount]["vl_credito"], 4 );

                    $nuVlMBDebitoSubTotal        = bcadd( $nuVlMBDebitoSubTotal       , $arDemonstrativoSaldos[$inCount]["vl_debito"] , 4 );
                    $nuVlMBCreditoSubTotal       = bcadd( $nuVlMBCreditoSubTotal      , $arDemonstrativoSaldos[$inCount]["vl_credito"], 4 );
                    $nuVlMBSaldoAnteriorSubTotal = bcadd( $nuVlMBSaldoAnteriorSubTotal, $rsDemonstrativoSaldos->getCampo( "saldo_anterior" ), 4 );
                    $nuVlMBSaldoAtualSubTotal    = bcadd( $nuVlMBSaldoAtualSubTotal   , $arDemonstrativoSaldos[$inCount]["saldo_atual"], 4 );

                    if ($this->getOrdenacao() == "recurso") {
                        $nuVlMBSaldoAnteriorTotalRecurso = bcadd( $nuVlMBSaldoAnteriorTotalRecurso, $rsDemonstrativoSaldos->getCampo( "saldo_anterior" ), 4 );
                        $nuVlMBDebitoTotalRecurso        = bcadd( $nuVlMBDebitoTotalRecurso       , $arDemonstrativoSaldos[$inCount]["vl_debito"]       , 4 );
                        $nuVlMBCreditoTotalRecurso       = bcadd( $nuVlMBCreditoTotalRecurso      , $arDemonstrativoSaldos[$inCount]["vl_credito"]      , 4 );
                        $nuVlMBSaldoAtualTotalRecurso    = bcadd( $nuVlMBSaldoAtualTotalRecurso   , $arDemonstrativoSaldos[$inCount]["saldo_atual"]   , 4 );
                    }

                    $arNomConta = array();
                    $stNomConta = $rsDemonstrativoSaldos->getCampo( "nom_conta" );
                    $stNomConta = str_replace( chr(10), '', $stNomConta );
                    $stNomConta = wordwrap( $stNomConta, 35, chr(13) );
                    $arNomConta = explode( chr(13), $stNomConta );
                    foreach ($arNomConta as $stNomConta) {
                        $arDemonstrativoSaldos[$inCount]["nom_conta"] = $stNomConta;
                        $inCount++;
                    }
                    $inCount--;

                    $stCodEstruturalOld = $rsDemonstrativoSaldos->getCampo( "cod_estrutural" );
                    $inCount++;
                    $rsDemonstrativoSaldos->proximo();

                    if ( substr($rsDemonstrativoSaldos->getCampo( "cod_estrutural" ),0,9 ) != substr( $stCodEstruturalOld, 0, 9 ) or $rsDemonstrativoSaldos->eof() ) {
                        if ($this->getOrdenacao() == "recurso" && $boMostrarUltimoSubTotalRecurso) {
                            $arDemonstrativoSaldos[$inCount]["cod_estrutural"] = "Sub-Total";
                            $arDemonstrativoSaldos[$inCount]["saldo_anterior"] = $nuVlMBSaldoAnteriorTotalRecurso;
                            $arDemonstrativoSaldos[$inCount]["vl_debito"     ] = $nuVlMBDebitoTotalRecurso;
                            $arDemonstrativoSaldos[$inCount]["vl_credito"    ] = ( abs($nuVlMBCreditoTotalRecurso) ) ? abs($nuVlMBCreditoTotalRecurso) : '0.00';
                            $arDemonstrativoSaldos[$inCount]["saldo_atual"   ] = $nuVlMBSaldoAtualTotalRecurso;
                            $inCount++;

                            $arDemonstrativoSaldos[$inCount]["cod_estrutural"] = "";
                            $inCount++;

                            $nuVlMBSaldoAnteriorTotalRecurso = '0.00';
                            $nuVlMBDebitoTotalRecurso        = '0.00';
                            $nuVlMBCreditoTotalRecurso       = '0.00';
                            $nuVlMBSaldoAtualTotalRecurso    = '0.00';
                        }

                        $arDemonstrativoSaldos[$inCount]["cod_estrutural"] = "Sub-Total";
                        $arDemonstrativoSaldos[$inCount]["saldo_anterior"] = $nuVlMBSaldoAnteriorSubTotal;
                        $arDemonstrativoSaldos[$inCount]["vl_debito"     ] = $nuVlMBDebitoSubTotal;
                        $arDemonstrativoSaldos[$inCount]["vl_credito"    ] = ( abs($nuVlMBCreditoSubTotal) ) ? abs($nuVlMBCreditoSubTotal) : '0.00';
                        $arDemonstrativoSaldos[$inCount]["saldo_atual"   ] = $nuVlMBSaldoAtualSubTotal;

                        $nuVlMBSaldoAnteriorTotal = bcadd( $nuVlMBSaldoAnteriorTotal, $nuVlMBSaldoAnteriorSubTotal, 4 );
                        $nuVlMBDebitoTotal        = bcadd( $nuVlMBDebitoTotal       , $nuVlMBDebitoSubTotal       , 4 );
                        $nuVlMBCreditoTotal       = bcadd( $nuVlMBCreditoTotal      , $nuVlMBCreditoSubTotal      , 4 );
                        $nuVlMBSaldoAtualTotal    = bcadd( $nuVlMBSaldoAtualTotal   , $nuVlMBSaldoAtualSubTotal   , 4 );

                        $nuVlMBSaldoAnteriorSubTotal = '0.00';
                        $nuVlMBCreditoSubTotal       = '0.00';
                        $nuVlMBDebitoSubTotal        = '0.00';
                        $nuVlMBSaldoAtualSubTotal    = '0.00';

                        $inCount++;
                        $arDemonstrativoSaldos[$inCount]["cod_estrutural"] = "";
                        $inCount++;
                    }
                }
            }
            $arDemonstrativoSaldos[$inCount]["cod_estrutural"] = "Total Caixa / Bancos";
            $arDemonstrativoSaldos[$inCount]["saldo_anterior"] = $nuVlMBSaldoAnteriorTotal;
            $arDemonstrativoSaldos[$inCount]["vl_debito"     ] = $nuVlMBDebitoTotal;
            $arDemonstrativoSaldos[$inCount]["vl_credito"    ] = ( abs($nuVlMBCreditoTotal) ) ? abs($nuVlMBCreditoTotal) : '0.00';
            $arDemonstrativoSaldos[$inCount]["saldo_atual"   ] = $nuVlMBSaldoAtualTotal;

            $rsDemonstrativoSaldos = new RecordSet();
            $rsDemonstrativoSaldos->preenche( $arDemonstrativoSaldos );
        }
        $arRelatorio = $rsDemonstrativoSaldos;
    }

    //Relatorio quando for agrupado por conta corrente
    public function geraRecordSetBancoContaCorrente(&$arRelatorio)
    {
        
        $this->obFTesourariaDemonstrativoSaldos->setDado('inCodEntidade', $this->getCodEntidade());
        $this->obFTesourariaDemonstrativoSaldos->setDado('stExercicio', $this->getExercicio());
        $this->obFTesourariaDemonstrativoSaldos->setDado('dtDataInicio',$this->getInicioPeriodo());
        $this->obFTesourariaDemonstrativoSaldos->setDado('dtDataFim', $this->getFimPeriodo());
        if ( $this->getInicioEstrutural() ) {
            $this->obFTesourariaDemonstrativoSaldos->setDado('stCodEstruturalInicio', $this->getInicioEstrutural());
        }

        if ( $this->getFimEstrutural() ) {
            $this->obFTesourariaDemonstrativoSaldos->setDado('stCodEstruturalFim', $this->getFimEstrutural());
        }

        if ( $this->getInicioReduzido() ) {
            $this->obFTesourariaDemonstrativoSaldos->setDado('inCodReduzidoInicio', $this->getInicioReduzido());
        }

        if ( $this->getFimReduzido() ) {
            $this->obFTesourariaDemonstrativoSaldos->setDado('inCodReduzidoFim', $this->getFimReduzido());
        }

        if ( $this->getCodRecurso() ) {
            $this->obFTesourariaDemonstrativoSaldos->setDado('inCodRecurso', $this->getCodRecurso());
        }
        if ( $this->getSemMovimento() ) {
            $this->obFTesourariaDemonstrativoSaldos->setDado('boSemMovimento', $this->getSemMovimento() );
        }
        if ( $this->getOrdenacao() ) {
            $stOrderBy = $this->getOrdenacao();
        }

        $this->obFTesourariaDemonstrativoSaldos->setDado('stDestinacaoRecurso', $this->stDestinacaoRecurso );
        $this->obFTesourariaDemonstrativoSaldos->setDado('inCodDetalhamento', $this->inCodDetalhamento );
        $this->obFTesourariaDemonstrativoSaldos->setDado('boUtilizaEstruturalTCE', 'false' );

        if (Sessao::getExercicio() > '2012') {
            $this->obFTesourariaDemonstrativoSaldos->setDado('boUtilizaEstruturalTCE', 'true' );
        }
        
        $obErro = $this->obFTesourariaDemonstrativoSaldos->recuperaDemonstrativoSaldosAgrupadoContaCorrente( $rsDemonstrativoSaldos, $stOrderBy, $boTransacao );            

        if ( !$obErro->ocorreu() ) {
            $inCount = 0;
            $nuVlMBSaldoAnteriorTotal = '0.00';
            $nuVlMBCreditoTotal       = '0.00';
            $nuVlMBDebitoTotal        = '0.00';
            $nuVlMBSaldoAtualTotal    = '0.00';
            $nuVlMBSaldoAnteriorSubTotal = '0.00';
            $nuVlMBSaldoAtualSubTotal = '0.00';

            $nuVlMBSaldoAnteriorTotalRecurso = '0.00';
            $nuVlMBDebitoTotalRecurso        = '0.00';
            $nuVlMBCreditoTotalRecurso       = '0.00';
            $nuVlMBSaldoAtualTotalRecurso    = '0.00';

            $inArmazenaAnteriorCodRecurso = 0;

            $boMostrarUltimoSubTotalRecurso = false;
            while ( !$rsDemonstrativoSaldos->eof() ) {

                if ( $this->getSemMovimento() == 'N') {
                    if ($rsDemonstrativoSaldos->getCampo("saldo_anterior") != '0.00' OR $rsDemonstrativoSaldos->getCampo("vl_debito") != '0.00' OR $rsDemonstrativoSaldos->getCampo("vl_credito") != '0.00' ) {
                        $boListar = true;
                    } else {
                        $boListar = false;
                        $rsDemonstrativoSaldos->proximo();
                    }
                } else $boListar = true;

                if ($boListar) {                    
                    $arDemonstrativoSaldos[$inCount]["des_conta"] = $rsDemonstrativoSaldos->getCampo( "des_conta" );                    
                    $arDemonstrativoSaldos[$inCount]["saldo_anterior"] = $rsDemonstrativoSaldos->getCampo( "saldo_anterior" );
                    if ($boLiberado) {
                        $arDemonstrativoSaldos[$inCount]["vl_debito"]  = $rsDemonstrativoSaldos->getCampo("vl_debito");
                        $arDemonstrativoSaldos[$inCount]["vl_credito"] = abs($rsDemonstrativoSaldos->getCampo("vl_credito"));
                    } else {
                        $arDemonstrativoSaldos[$inCount]["vl_debito"]  = bcadd( $rsDemonstrativoSaldos->getCampo("vl_debito"), $arSaldoContaArrecadacao[$rsDemonstrativoSaldos->getCampo("cod_plano")]['vl_conta_debito'], 4 );
                        $arDemonstrativoSaldos[$inCount]["vl_credito"] = abs(bcadd( abs($rsDemonstrativoSaldos->getCampo("vl_credito")), $arSaldoContaArrecadacao[$rsDemonstrativoSaldos->getCampo("cod_plano")]['vl_conta_credito'], 4 ));
                    }
                    $arDemonstrativoSaldos[$inCount]["vl_credito"] = ( $arDemonstrativoSaldos[$inCount]["vl_credito"] ) ? $arDemonstrativoSaldos[$inCount]["vl_credito"] : '0.00';
                    $arDemonstrativoSaldos[$inCount]["saldo_atual"] = bcsub( bcadd($rsDemonstrativoSaldos->getCampo( "saldo_anterior" ), $arDemonstrativoSaldos[$inCount]["vl_debito"], 4 ), $arDemonstrativoSaldos[$inCount]["vl_credito"], 4 );

                    $nuVlMBDebitoSubTotal        = bcadd( $nuVlMBDebitoSubTotal       , $arDemonstrativoSaldos[$inCount]["vl_debito"] , 4 );
                    $nuVlMBCreditoSubTotal       = bcadd( $nuVlMBCreditoSubTotal      , $arDemonstrativoSaldos[$inCount]["vl_credito"], 4 );
                    $nuVlMBSaldoAnteriorSubTotal = bcadd( $nuVlMBSaldoAnteriorSubTotal, $rsDemonstrativoSaldos->getCampo( "saldo_anterior" ), 4 );
                    $nuVlMBSaldoAtualSubTotal    = bcadd( $nuVlMBSaldoAtualSubTotal   , $arDemonstrativoSaldos[$inCount]["saldo_atual"], 4 );

                    $arNomConta = array();
                    $stNomConta = $rsDemonstrativoSaldos->getCampo( "nom_conta" );
                    $stNomConta = str_replace( chr(10), '', $stNomConta );
                    $stNomConta = wordwrap( $stNomConta, 35, chr(13) );
                    $arNomConta = explode( chr(13), $stNomConta );
                    foreach ($arNomConta as $stNomConta) {
                        $arDemonstrativoSaldos[$inCount]["nom_conta"] = $stNomConta;
                        $inCount++;
                    }
                    $inCount--;

                    $stCodEstruturalOld = $rsDemonstrativoSaldos->getCampo( "cod_estrutural" );
                    $inCount++;
                    $rsDemonstrativoSaldos->proximo();

                    if ( substr($rsDemonstrativoSaldos->getCampo( "cod_estrutural" ),0,9 ) != substr( $stCodEstruturalOld, 0, 9 ) or $rsDemonstrativoSaldos->eof() ) {
                        
                        $arDemonstrativoSaldos[$inCount]["des_conta"]      = "Sub-Total";
                        $arDemonstrativoSaldos[$inCount]["saldo_anterior"] = $nuVlMBSaldoAnteriorSubTotal;
                        $arDemonstrativoSaldos[$inCount]["vl_debito"     ] = $nuVlMBDebitoSubTotal;
                        $arDemonstrativoSaldos[$inCount]["vl_credito"    ] = ( abs($nuVlMBCreditoSubTotal) ) ? abs($nuVlMBCreditoSubTotal) : '0.00';
                        $arDemonstrativoSaldos[$inCount]["saldo_atual"   ] = $nuVlMBSaldoAtualSubTotal;

                        $nuVlMBSaldoAnteriorTotal = bcadd( $nuVlMBSaldoAnteriorTotal, $nuVlMBSaldoAnteriorSubTotal, 4 );
                        $nuVlMBDebitoTotal        = bcadd( $nuVlMBDebitoTotal       , $nuVlMBDebitoSubTotal       , 4 );
                        $nuVlMBCreditoTotal       = bcadd( $nuVlMBCreditoTotal      , $nuVlMBCreditoSubTotal      , 4 );
                        $nuVlMBSaldoAtualTotal    = bcadd( $nuVlMBSaldoAtualTotal   , $nuVlMBSaldoAtualSubTotal   , 4 );

                        $nuVlMBSaldoAnteriorSubTotal = '0.00';
                        $nuVlMBCreditoSubTotal       = '0.00';
                        $nuVlMBDebitoSubTotal        = '0.00';
                        $nuVlMBSaldoAtualSubTotal    = '0.00';

                        $inCount++;
                        $arDemonstrativoSaldos[$inCount]["des_conta"] = "";
                        $inCount++;
                    }
                }
            }
            $arDemonstrativoSaldos[$inCount]["des_conta"]      = "Total Caixa / Bancos";
            $arDemonstrativoSaldos[$inCount]["saldo_anterior"] = $nuVlMBSaldoAnteriorTotal;
            $arDemonstrativoSaldos[$inCount]["vl_debito"     ] = $nuVlMBDebitoTotal;
            $arDemonstrativoSaldos[$inCount]["vl_credito"    ] = ( abs($nuVlMBCreditoTotal) ) ? abs($nuVlMBCreditoTotal) : '0.00';
            $arDemonstrativoSaldos[$inCount]["saldo_atual"   ] = $nuVlMBSaldoAtualTotal;

            $rsDemonstrativoSaldos = new RecordSet();
            $rsDemonstrativoSaldos->preenche( $arDemonstrativoSaldos );
        }
        $arRelatorio = $rsDemonstrativoSaldos;
    }


}
