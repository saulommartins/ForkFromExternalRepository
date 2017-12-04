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
     * Classe de regra de Relatório de Valores Lançados
     * Data de Criação: 06/04/2006

     * @author Analista: Fábio Bertoldi Rodrigues
     * @author Desenvolvedor: Diego Bueno Coelho

     * @package URBEM
     * @subpackage Regra

    * $Id: RARRRelatorioValoresLancados.class.php 63839 2015-10-22 18:08:07Z franver $

     * Casos de uso: uc-05.03.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."PersistenteRelatorio.class.php"                 );

//include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoImovelValor.class.php"          );
include_once ( CAM_GT_ARR_MAPEAMENTO."FARRRelatorioValoresLancados.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php"                      );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                   );

set_time_limit(0);

/**
    * Classe de Regra para relatório de CadastroImobiliario
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho
*/
class RARRRelatorioValoresLancados extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFARRRelatorioValoresLancados;
/**
    * @var Object
    * @access Private
*/
var $obRARRConfiguracao;
/**
    * @access Private
    * @var String
*/
var $stTipoRelatorio;
/**
    * @access Private
    * @var Array
*/
//var $arAtributos;
/**
    * @access Private
    * @var Integer
*/
var $inCodGrupoCreditoInicio;
/**
    * @access Private
    * @var Integer
*/
var $inCodGrupoCreditoTermino;
/**
    * @access Private
    * @var Integer
*/
var $inCodCreditoInicio;
/**
    * @access Private
    * @var Integer
*/
var $inCodCreditoTermino;
/**
    * @access Private
    * @var Integer
*/
var $inNumCGMInicio;
/**
    * @access Private
    * @var Integer
*/
var $inNumCGMTermino;
/**
    * @access Private
    * @var Integer
*/
var $inInscricaoImobiliariaInicio;
/**
    * @access Private
    * @var Integer
*/
var $inInscricaoImobiliariaTermino;
/**
    * @access Private
    * @var Integer
*/
var $inInscricaoEconomicaInicio;
/**
    * @access Private
    * @var Integer
*/
var $inInscricaoEconomicaTermino;
/**
    * @access Private
    * @var Integer
*/
var $stExercicio;
/**
    * @access Private
    * @var Integer
*/
var $stOrder;
/**
    * @access Private
    * @var Float
*/
var $nuValorInicial;
/**
    * @access Private
    * @var Double
*/
var $nuValorFinal;
/**
    * @access Private
    * @var Integer
*/
var $inCodLogradouro;
/**
    * @access Private
    * @var String
*/
var $stNomLogradouro;
/**
    * @access Private
    * @var Integer
*/
var $inCodCondominioInicial;
/**
    * @access Private
    * @var String
*/
var $stNomCondominioInicial;
/**
    * @access Private
    * @var Integer
*/
var $inCodCondominioFinal;
/**
    * @access Private
    * @var String
*/
var $stNomCondominioFinal;
/**
    * @access Private
    * @var String
*/
var $stSituacao;
/**
    * @access Private
    * @var Date
*/
var $dtInicio;
/**
    * @access Private
    * @var Date
*/
var $dtFinal;
var $stCodEstAtivInicial;
var $stCodEstAtivFinal;

// ####### SETTERS
/**
    * @access Public
    * @param Integer $valor
*/
function setCodGrupoCreditoInicio($valor) { $this->inCodGrupoCreditoInicio = $valor; }
function setCodEstAtivInicial($valor) { $this->stCodEstAtivInicial = $valor; }
function setCodEstAtivFinal($valor) { $this->stCodEstAtivFinal = $valor; }
function getCodEstAtivInicial() { return $this->stCodEstAtivInicial; }
function getCodEstAtivFinal() { return $this->stCodEstAtivFinal; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodGrupoCreditoTermino($valor) { $this->inCodGrupoCreditoTermino = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodCreditoInicio($valor) { $this->inCodCreditoInicio = $valor;        }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodCreditoTermino($valor) { $this->inCodCreditoTermino = $valor;        }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodLogradouro($valor) { $this->inCodLogradouro = $valor;        }
/**
    * @access Public
    * @param String $valor
*/
function setNomLogradouro($valor) { $this->stNomLogradouro = $valor;        }
/**
    * @access Public
    * @param Integer $valor
*/
function setNumCGMInicio($valor) { $this->inNumCGMInicio = $valor;   }
/**
    * @access Public
    * @param Integer $valor
*/
function setNumCGMTermino($valor) { $this->inNumCGMTermino = $valor;   }
/**
    * @access Public
    * @param Integer $valor
*/
function setInscricaoImobiliariaInicio($valor) { $this->inInscricaoImobiliariaInicio = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setInscricaoImobiliariaTermino($valor) { $this->inInscricaoImobiliariaTermino = $valor;   }
/**
    * @access Public
    * @param Integer $valor
*/
function setInscricaoEconomicaInicio($valor) { $this->inInscricaoEconomicaInicio = $valor;   }
/**
    * @access Public
    * @param Integer $valor
*/
function setInscricaoEconomicaTermino($valor) { $this->inInscricaoEconomicaTermino = $valor;   }
/**
    * @access Public
    * @param Integer $valor
*/
function setExercicio($valor) { $this->stExercicio  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setOrder($valor) { $this->stOrder = $valor;                }
/**
    * @access Public
    * @param String $valor
*/
function setTipoRelatorio($valor) { $this->stTipoRelatorio = $valor;         }
/**
    * @access Public
    * @param Array $valor
*/
function setAtributos($valor) { $this->arAtributos[] = $valor;           }
/**
    * @access Public
    * @param Array $valor
*/
function setValorInicial($valor) { $this->nuValorInicial= $valor;           }
/**
    * @access Public
    * @param Array $valor
*/
function setValorFinal($valor) { $this->nuValorFinal  = $valor;           }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodCondominioInicial($valor) { $this->inCodCondominioInicial = $valor;        }
/**
    * @access Public
    * @param String $valor
*/
function setNomCondominioInicial($valor) { $this->stNomCondominioInicial = $valor;        }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodCondominioFinal($valor) { $this->inCodCondominioFinal = $valor;        }
/**
    * @access Public
    * @param String $valor
*/
function setNomCondominioFinal($valor) { $this->stNomCondominioFinal = $valor;        }
/**
    * @access Public
    * @param String $valor
*/
function setSituacao($valor) { $this->stSituacao = $valor;  }
/**
    * @access Public
    * @param Date $valor
*/
function setDtInicio($valor) { $this->dtInicio = $valor;    }
/**
    * @access Public
    * @param Date $valor
*/
function setDtFinal($valor) { $this->dtFinal = $valor;     }

// ####### GETTERS
/**
    * @access Public
    * @return Integer
*/
function getCodGrupoCreditoInicio() { return $this->inCodGrupoCreditoInicio;  }
/**
    * @access Public
    * @return Integer
*/
function getCodGrupoCreditoTermino() { return $this->inCodGrupoCreditoTermino;  }
/**
    * @access Public
    * @return Integer
*/
function getCodCreditoInicio() { return $this->inCodCreditoInicio;         }
/**
    * @access Public
    * @return Integer
*/
function getCodCreditoTermino() { return $this->inCodCreditoTermino;         }
/**
    * @access Public
    * @return Integer
*/
function getNumCGMInicio() { return $this->inNumCGMInicio;     }
/**
    * @access Public
    * @return Integer
*/
function getNumCGMTermino() { return $this->inNumCGMTermino;     }
/**
    * @access Public
    * @return Integer
*/
function getInscricaoImobiliariaInicio() { return $this->inInscricaoImobiliariaInicio;   }
/**
    * @access Public
    * @return Integer
*/
function getInscricaoImobiliariaTermino() { return $this->inInscricaoImobiliariaTermino;     }
/**
    * @access Public
    * @return Integer
*/
function getInscricaoEconomicaInicio() { return $this->inInscricaoEconomicaInicio;     }
/**
    * @access Public
    * @return Integer
*/
function getInscricaoEconomicaTermino() { return $this->inInscricaoEconomicaTermino;     }
/**
    * @access Public
    * @return Integer
*/
function getExercicio() { return $this->stExercicio;        }
/**
    * @access Public
    * @return Integer
*/
function getOrder() { return $this->stOrder;                  }
/**
    * @access Public
    * @return String
*/
function getTipoRelatorio() { return $this->stTipoRelatorio;           }
/**
    * @access Public
    * @return Array
*/
function getAtributos() { return $this->arAtributos;               }
/**
    * @access Public
    * @return Array
*/
function getValorInicial() { return $this->nuValorInicial;            }
/**
    * @access Public
    * @return Array
*/
function getValorFinal() { return $this->nuValorFinal;              }
/**
    * @access Public
    * @return Integer
*/
function getCodLogradouro() { return $this->inCodLogradouro;         }
/**
    * @access Public
    * @return String
*/
function getNomLogradouro() { return $this->stNomLogradouro;         }
/**
    * @access Public
    * @return Integer
*/
function getCodCondominioInicial() { return $this->inCodCondominioInicial; }
/**
    * @access Public
    * @return String
*/
function getNomCondominioInicial() { return $this->stNomCondominioInicial; }
/**
    * @access Public
    * @return Integer
*/
function getCodCondominioFinal() { return $this->inCodCondominioFinal;   }
/**
    * @access Public
    * @return String
*/
function getNomCondominioFinal() { return $this->stNomCondominioFinal;   }
/**
    * @access Public
    * @return String
*/
function getSituacao() { return $this->stSituacao; }
/**
    * @access Public
    * @return String
*/
function getDtInicio() { return $this->dtInicio;   }
/**
    * @access Public
    * @return String
*/
function getDtFinal() { return $this->dtFinal;    }

/**
    * Método Construtor
    * @access Private
*/
function RARRRelatorioValoresLancados()
{
    $this->obRCadastroDinamico                = new RCadastroDinamico;
    $this->obFARRRelatorioValoresLancados = new FARRRelatorioValoresLancados;
    $this->obRARRConfiguracao                 = new RARRConfiguracao;

    /*$this->obRCadastroDinamico->setPersistenteValores   ( new TCIMAtributoImovelValor );
    $this->obRCadastroDinamico->setCodCadastro          ( 4 );
    $this->obRCadastroDinamico->obRModulo->setCodModulo ( 12 );*/

    //$this->obRARRConfiguracao->setCodigoModulo( 25 );
    //$this->obRARRConfiguracao->setAnoExercicio( Sessao::getExercicio() );
    //$this->obRARRConfiguracao->consultarConfiguracao();
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSetRelatorio(&$rsRecordSet, &$rsRecordSetSomas, &$arCabecalho, $stOrder = "")
{
    $obErro = new Erro;
    $stFiltro = "";

    $arAtributos = $this->getAtributos();
    $arAtributos = $arAtributos[0];
    $this->obFARRRelatorioValoresLancados->setDado  ( "stFiltro"   , $stFiltro    );

    #echo 'TIPO DO RELATORIO: '.$this->getTipoRelatorio().'<br>'; exit;

        # ==========================================================================================
        // RELATORIO ANALITICO
        # ==========================================================================================

        if ( $this->getCodCreditoInicio() || $this->getCodCreditoTermino() ) {

            $arDadosIniciais =  explode( ".", $this->getCodCreditoInicio() );
            $arDadosFinais = explode(".", $this->getCodCreditoTermino() );
            $arTipos = array("cod_credito", "cod_especie", "cod_genero", "cod_natureza");
            for ($inX=0; $inX<4; $inX++) {
                if ($arDadosIniciais[$inX] && $arDadosFinais[$inX]) {
                    if ($arDadosIniciais[$inX] > $arDadosFinais[$inX]) {
                        $inTmp = $arDadosIniciais[$inX];
                        $arDadosIniciais[$inX] = ltrim ( $arDadosFinais[$inX], '0' );
                        $arDadosFinais[$inX] = ltrim ( $inTmp, '0' );
                    }

                    $stFiltro .= " ".$arTipos[$inX]." BETWEEN ". ltrim ( $arDadosIniciais[$inX], '0')." AND ". ltrim ( $arDadosFinais[$inX] , '0' )." AND ";
                } elseif ($arDadosIniciais[$inX] && !$arDadosFinais[$inX]) {
                    $stFiltro .= " ".$arTipos[$inX]." = ". ltrim ( $arDadosIniciais[$inX], '0' )." AND ";
                        //           " cod_credito >= xxx AND "
                } elseif (!$arDadosIniciais[$inX] && $arDadosFinais[$inX]) {
                    $stFiltro .= " ".$arTipos[$inX]." = ". ltrim ( $arDadosFinais[$inX], '0') ." AND ";
                            //           " cod_credito <= xxx AND "
                }
            }
        }

        $inCodCreditoInicial    = ltrim ( $arDadosIniciais[0], '0');
        $inCodEspecieInicial    = ltrim ( $arDadosIniciais[1], '0');
        $inCodGeneroInicial     = ltrim ( $arDadosIniciais[2], '0');
        $inCodNaturezaInicial   = ltrim ( $arDadosIniciais[3], '0');

        $inCodCreditoFinal  = ltrim ( $arDadosFinais[0], '0');
        $inCodEspecieFinal  = ltrim ( $arDadosFinais[1], '0');
        $inCodGeneroFinal   = ltrim ( $arDadosFinais[2], '0');
        $inCodNaturezaFinal = ltrim ( $arDadosFinais[3], '0');

        $arGrupoInicial = explode ('/', $this->getCodGrupoCreditoInicio() );
        $inCodGrupoInicial = $arGrupoInicial[0];
        $inExercicioGrupoInicial = $arGrupoInicial[1];
        $arGrupoFinal   = explode ('/', $this->getCodGrupoCreditoTermino());
        $inCodGrupoFinal = $arGrupoFinal[0];
        $inExercicioGrupoFinal = $arGrupoFinal[1];

        $stFiltro =     "'".$inCodCreditoInicial."', '".$inCodEspecieInicial."', '".$inCodGeneroInicial."', '";
        $stFiltro .=    $inCodNaturezaInicial."', '".$inCodCreditoFinal."', '".$inCodEspecieFinal."', '";
        $stFiltro .=    $inCodGeneroFinal."', '".$inCodNaturezaFinal."', '";

        $stFiltro .=    $inCodGrupoInicial."','".$inExercicioGrupoInicial."', '";
        $stFiltro .=    $inCodGrupoFinal."','".$inExercicioGrupoFinal."', '";

        $stFiltro .=   $this->getInscricaoImobiliariaInicio()."', '".$this->getInscricaoImobiliariaTermino()."', '";
        $stFiltro .=   $this->getInscricaoEconomicaInicio()."', '".$this->getInscricaoEconomicaTermino()."', '";
        $stFiltro .=   $this->getNumCGMInicio()."', '".$this->getNumCGMTermino()."', '";

        $stFiltro .=    $this->getCodLogradouro()."', '"."', '";
        $stFiltro .=    $this->getCodCondominioInicial()."', '".$this->getCodCondominioFinal()."', '";

        $stFiltro .=    $this->getValorInicial()."', '".$this->getValorFinal()."', '";

        $stFiltro .=    $this->getSituacao()."', '";

        if (!$inExercicioGrupoInicial) {
            $stFiltro .=    $this->getExercicio()."'";
        } else {
            $stFiltro .= "'";
        }

        $stFiltro .= ", '".$this->getCodEstAtivInicial()."', '".$this->getCodEstAtivFinal()."' ";

        if ( $this->getTipoRelatorio() == 'sintetico' ) {

            $obErro = $this->obFARRRelatorioValoresLancados->recuperaRelatorioSintetico ( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );


            $arListaSintetico = array();
            $arListaSomas = array();

            $nuSumPago              = 0.00;
            $nuSumPagoJuros         = 0.00;
            $nuSumPagoMulta         = 0.00;
            $nuSumPagoCorrecao      = 0.00;
            $nuSumPagoDiferenca     = 0.00;
            $nuSumPagoTotal         = 0.00;
            $nuSumLancado           = 0.00;
            $nuSumAberto            = 0.00;

            $rsRecordSet->setPrimeiroElemento();
            while ( !$rsRecordSet->eof() ) {

                #echo '<br>valor: '.$rsRecordSet->getCampo ( "diferenca_real" ).'x';

                $nuSumPago          += $rsRecordSet->getCampo ( "pagamento_valor" );
                $nuSumPagoJuros     += $rsRecordSet->getCampo ( "juros_pago" );
                $nuSumPagoMulta     += $rsRecordSet->getCampo ( "multa_pago" );
                $nuSumPagoCorrecao  += $rsRecordSet->getCampo ( "correcao_pago" );
                $nuSumPagoDiferenca += $rsRecordSet->getCampo ( "diferenca_real" );
                $nuSumPagoTotal     += $rsRecordSet->getCampo ( "soma_pago" );
                #$nuSumLancado       += $rsRecordSet->getCampo ( "lancado" );
                $nuSumAberto        += $rsRecordSet->getCampo ( "soma_aberto" );

                $rsRecordSet->proximo();
            }
#exit;

            $arTmp[] = array (
                "incodgrupo" => "",
                "st_total"          => "TOTAL:",
                "pagamento_valor"   => $nuSumPago,
                "juros_pago"        => $nuSumPagoJuros,
                "multa_pago"        => $nuSumPagoMulta,
                "correcao_pago"     => $nuSumPagoCorrecao,
                "diferenca_real"    => $nuSumPagoDiferenca,
                "soma_pago"         => $nuSumPagoTotal,
                #"lancado"           => $nuSumLancado,
                "soma_aberto"       => $nuSumAberto
            );

            $rsRecordSetSomas = new RecordSet;
            $rsRecordSetSomas->preenche ( $arTmp );

            $rsRecordSet->addFormatacao ( 'pagamento_valor', 'NUMERIC_BR');
            $rsRecordSet->addFormatacao ( 'juros_pago', 'NUMERIC_BR');
            $rsRecordSet->addFormatacao ( 'multa_pago', 'NUMERIC_BR');
            $rsRecordSet->addFormatacao ( 'correcao_pago', 'NUMERIC_BR');
            $rsRecordSet->addFormatacao ( 'diferenta_real', 'NUMERIC_BR');
            $rsRecordSet->addFormatacao ( 'soma_pago', 'NUMERIC_BR');
            #$rsRecordSet->addFormatacao ( 'lancado', 'NUMERIC_BR');
            $rsRecordSet->addFormatacao ( 'soma_aberto', 'NUMERIC_BR');

            $rsRecordSetSomas->addFormatacao ( 'pagamento_valor', 'NUMERIC_BR');
            $rsRecordSetSomas->addFormatacao ( 'juros_pago', 'NUMERIC_BR');
            $rsRecordSetSomas->addFormatacao ( 'multa_pago', 'NUMERIC_BR');
            $rsRecordSetSomas->addFormatacao ( 'correcao_pago', 'NUMERIC_BR');
            $rsRecordSetSomas->addFormatacao ( 'diferenca_real', 'NUMERIC_BR');
            $rsRecordSetSomas->addFormatacao ( 'soma_pago', 'NUMERIC_BR');
            #$rsRecordSetSomas->addFormatacao ( 'lancado', 'NUMERIC_BR');
            $rsRecordSetSomas->addFormatacao ( 'soma_aberto', 'NUMERIC_BR');

            $rsRecordSet->setPrimeiroElemento();
            $rsRecordSetSomas->setPrimeiroElemento();

        } else {
            $obErro = $this->obFARRRelatorioValoresLancados->recuperaRelatorioAnalitico ( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
        }

    return $obErro;
}

function geraRecordSetPeriodico(&$rsRecordSet, &$rsRecordSetSomas, &$arCabecalho, $stOrder = "")
{
    $stFiltro = '\''.STRTOUPPER($this->getTipoRelatorio ()).'\',';

    $arDataIni =explode('/',$this->getDtInicio());
    $arDataFim =$this->getDtFinal()?explode('/',$this->getDtFinal()):$arDataIni;

    $stFiltro .= '\''.$arDataIni[2].'-'.$arDataIni[1].'-'.$arDataIni[0].'\',';
    $stFiltro .= '\''.$arDataFim[2].'-'.$arDataFim[1].'-'.$arDataFim[0].'\',';

    $stFiltro .= $this->getCodCreditoInicio()  ? '\''.$this->getCodCreditoInicio().'\',' : '\'\',';
    $stFiltro .= $this->getCodCreditoTermino() ? '\''.$this->getCodCreditoTermino().'\',' : '\'\',';

    $stFiltro .= $this->getCodGrupoCreditoInicio() ? '\''.$this->getCodGrupoCreditoInicio().'\',' : '\'\',';
    $stFiltro .= $this->getCodGrupoCreditoTermino()? '\''.$this->getCodGrupoCreditoTermino().'\',' :'\'\',';

    $stFiltro .= $this->getInscricaoImobiliariaInicio()  ? '\''.$this->getInscricaoImobiliariaInicio().'\',' : 'null,';
    $stFiltro .= $this->getInscricaoImobiliariaTermino() ? '\''.$this->getInscricaoImobiliariaTermino().'\',' : 'null,';

    $stFiltro .= $this->getInscricaoEconomicaInicio() ? '\''.$this->getInscricaoEconomicaInicio().'\',' : 'null,';
    $stFiltro .= $this->getInscricaoEconomicaTermino()? '\''.$this->getInscricaoEconomicaTermino().'\',' : 'null,';

    $stFiltro .= $this->getNumCGMInicio()  ? '\''.$this->getNumCGMInicio().'\',' : 'null,';
    $stFiltro .= $this->getNumCGMTermino() ? '\''.$this->getNumCGMTermino().'\',' : 'null,';

    $stFiltro .= $this->getCodEstAtivInicial() ? '\''.$this->getCodEstAtivInicial().'\',' : 'null,';
    $stFiltro .= $this->getCodEstAtivFinal() ? '\''.$this->getCodEstAtivFinal().'\'' : 'null';
    if ( STRTOUPPER($this->getTipoRelatorio()) == 'SINTETICO' ) {
        $obErro = $this->obFARRRelatorioValoresLancados->recuperaRelatorioPeriodico ( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    } else {
        $obErro = $this->obFARRRelatorioValoresLancados->recuperaRelatorioPeriodicoPorCGM ( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    }

    return $obErro;

    exit($stFiltro);

    $obErro = new Erro;

    if ( $this->getCodCreditoInicio() || $this->getCodCreditoTermino() ) {

        $arDadosIniciais =  explode( ".", $this->getCodCreditoInicio() );
        $arDadosFinais = explode(".", $this->getCodCreditoTermino() );
        $arTipos = array("cod_credito", "cod_especie", "cod_genero", "cod_natureza");
        for ($inX=0; $inX<4; $inX++) {
            if ($arDadosIniciais[$inX] && $arDadosFinais[$inX]) {
                if ($arDadosIniciais[$inX] > $arDadosFinais[$inX]) {
                    $inTmp = $arDadosIniciais[$inX];
                    $arDadosIniciais[$inX] = ltrim ( $arDadosFinais[$inX], '0' );
                    $arDadosFinais[$inX] = ltrim ( $inTmp, '0' );
                }

                $stFiltro .= " ".$arTipos[$inX]." BETWEEN ". ltrim ( $arDadosIniciais[$inX], '0')." AND ". ltrim ( $arDadosFinais[$inX] , '0' )." AND ";
            } elseif ($arDadosIniciais[$inX] && !$arDadosFinais[$inX]) {
                $stFiltro .= " ".$arTipos[$inX]." = ". ltrim ( $arDadosIniciais[$inX], '0' )." AND ";
                    //           " cod_credito >= xxx AND "
            } elseif (!$arDadosIniciais[$inX] && $arDadosFinais[$inX]) {
                $stFiltro .= " ".$arTipos[$inX]." = ". ltrim ( $arDadosFinais[$inX], '0') ." AND ";
                        //           " cod_credito <= xxx AND "
            }
        }
    }

    $inCodCreditoInicial    = ltrim ( $arDadosIniciais[0], '0');
    $inCodEspecieInicial    = ltrim ( $arDadosIniciais[1], '0');
    $inCodGeneroInicial     = ltrim ( $arDadosIniciais[2], '0');
    $inCodNaturezaInicial   = ltrim ( $arDadosIniciais[3], '0');

    $inCodCreditoFinal  = ltrim ( $arDadosFinais[0], '0');
    $inCodEspecieFinal  = ltrim ( $arDadosFinais[1], '0');
    $inCodGeneroFinal   = ltrim ( $arDadosFinais[2], '0');
    $inCodNaturezaFinal = ltrim ( $arDadosFinais[3], '0');

    $arGrupoInicial = explode ('/', $this->getCodGrupoCreditoInicio() );
    $inCodGrupoInicial = $arGrupoInicial[0];
    $inExercicioGrupoInicial = $arGrupoInicial[1];
    $arGrupoFinal   = explode ('/', $this->getCodGrupoCreditoTermino());
    $inCodGrupoFinal = $arGrupoFinal[0];
    $inExercicioGrupoFinal = $arGrupoFinal[1];

    $arDtInicio = explode ( '/', $this->getDtInicio() );
    $this->setDtInicio( $arDtInicio[2].'-'.$arDtInicio[1].'-'.$arDtInicio[0] );
    if ( !$this->getDtFinal() ) {
        $this->setDtFinal( $this->getDtInicio() );
    } else {
        $arDtFinal = explode ( '/', $this->getDtFinal() );
        $this->setDtFinal( $arDtFinal[2].'-'.$arDtFinal[1].'-'.$arDtFinal[0] );
    }

    #---------------------- FILTRO
    $stFiltro =     "'".$inCodCreditoInicial."', '".$inCodEspecieInicial."', '".$inCodGeneroInicial."', '";
    $stFiltro .=    $inCodNaturezaInicial."', '".$inCodCreditoFinal."', '".$inCodEspecieFinal."', '";
    $stFiltro .=    $inCodGeneroFinal."', '".$inCodNaturezaFinal."', '";

    $stFiltro .=    $inCodGrupoInicial."','".$inExercicioGrupoInicial."', '";
    $stFiltro .=    $inCodGrupoFinal."','".$inExercicioGrupoFinal."', '";

    $stFiltro .=    $this->getInscricaoImobiliariaInicio()."', '".$this->getInscricaoImobiliariaTermino()."', '";
    $stFiltro .=    $this->getInscricaoEconomicaInicio()."', '".$this->getInscricaoEconomicaTermino()."', '";
    $stFiltro .=    $this->getNumCGMInicio()."', '".$this->getNumCGMTermino()."', '";

    $stFiltro .=    $this->getDtInicio()."', '".$this->getDtFinal()."' ";

    //echo '<hr>FILTRO: '.$stFiltro;
    //exit();
    if ( $this->getTipoRelatorio() == 'sintetico' ) {
        $obErro = $this->obFARRRelatorioValoresLancados->recuperaRelatorioPeriodico ( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    } else {
        $obErro = $this->obFARRRelatorioValoresLancados->recuperaRelatorioPeriodicoPorCGM ( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    }

    return $obErro;

}

}
