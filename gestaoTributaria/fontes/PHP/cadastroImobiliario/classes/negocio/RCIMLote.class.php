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

     * Classe de regra de negócio para lote
     * Data de Criação: 22/11/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMLote.class.php 60949 2014-11-26 11:39:00Z evandro $

     * Casos de uso: uc-05.01.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLote.class.php"              );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLoteBairro.class.php"        );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMProfundidadeMedia.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAreaLote.class.php"          );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMBaixaLote.class.php"         );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLoteLocalizacao.class.php"   );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLoteProcesso.class.php"      );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMParcelamentoSolo.class.php"  );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLoteParcelado.class.php"     );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelLote.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfrontacaoLote.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfrontacaoTrecho.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfrontacaoDiversa.class.php"    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php"                 );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"                 );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"                 );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"            );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php"                  );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"           );
include_once ( CAM_GA_ADM_MAPEAMENTO."TUnidadeMedida.class.php"        );

class RCIMLote
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoLote;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoLoteOriginal;
/**
    * @access Private
    * @var Integer
*/
var $inNumeroLote;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoGrandeza;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoUnidade;
/**
    * @access Private
    * @var Array
*/

var $arRCIMConfrontacaoLote;
/*
    * @access Private
    * @var Timestamp
*/
var $tmTimestampLote;
/**
    * @access Private
    * @var Object
*/
var $roUltimaConfrontacaoLote;
/**
    * @access Private
    * @var Array
*/
var $arRCIMConfrontacaoDiversa;
/**
    * @access Private
    * @var Object
*/
var $roUltimaConfrontacaoDiversa;
/**
    * @access Private
    * @var Array
*/
var $arRCIMConfrontacaoTrecho;
/**
    * @access Private
    * @var Object
*/
var $roUltimaConfrontacaoTrecho;
/**
    * @access Private
    * @var Date
*/
var $dtDataInscricao;
/**
    * @access Private
    * @var Float
*/
var $flProfundidadeMedia;
/**
var $
    * @access Private
    * @var Float
*/
var $flAreaLote;
/**
    * @access Private
    * @var Float
*/
var $flAreaEdificada;
/**
    * @access Private
    * @var Date
*/
var $dtDataTermino;
/**
    * @access Private
    * @var Date
*/
var $dtDataBaixa;

/**
    * @access Private
    * @var String
*/
var $stJustificativaReativar;

/**
    * @access Private
    * @var String
*/
var $stJustificativa;
/**
    * @access Private
    * @var String
*/
var $stChaveEnderecoImovel;
/**
    * @access Private
    * @var Date
*/
var $dtDataAglutinacao;
/**
    * @access Private
    * @var Object
*/
var $obTCIMLote;
/**
    * @access Private
    * @var Object
*/
var $obTCIMProfundidadeMedia;
/**
    * @access Private
    * @var Object
*/
var $obTCIMAreaLote;
/**
    * @access Private
    * @var Object
*/
var $obTCIMBaixaLote;
/**
    * @access Private
    * @var Object
*/
var $obTCIMLoteBairro;
/**
    * @access Private
    * @var Object
*/
var $obTCIMLoteLocalizacao;
/**
    * @access Private
    * @var Object
*/
var $obTCIMConfrontacao;
/**
    * @access Private
    * @var Object
*/
var $obRCIMTrecho;
/**
    * @access Private
    * @var Object
*/
var $obRCIMBairro;
/**
    * @access Private
    * @var Object
*/
var $obRCIMImovel;
/**
    * @access Private
    * @var Object
*/
var $obRCIMLocalizacao;
/**
    * @access Private
    * @var Object
*/
var $obTUnidadeMedida;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $obTCIMParcelamentoSolo;
/**
    * @access Private
    * @var Object
*/
var $obTCIMLoteParcelado;
/**
    * @access Private
    * @var Array
*/
var $arRCIMLote;//ARRAY DE LOTES PARA AGLUTINAR
/**
    * @access Private
    * @var Boolean
*/
var $boCaucionado;
/**
    * @access Private
    * @var Boolean
*/
var $boLoteDesmembrado;

/**
    * @access Private
    * @var Data
*/
var $dtDataParcelamento;

/**
    * @access Private
    * @var integer
*/
var $inCodigoParcelamentoSolo;

/**
    * @access Private10,00
    * @var boolean
*/
var $boValidado;

/**
    * @access Private10,00
    * @var integer
*/
var $inQuantLotes;
/**
    * @access Public
    * @var Array
*/
var $arEdificacoes;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoLote($valor) { $this->inCodigoLote        = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoLoteOriginal($valor) { $this->inCodigoLoteOriginal = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNumeroLote($valor) { $this->inNumeroLote        = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoGrandeza($valor) { $this->inCodigoGrandeza    = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoUnidade($valor) { $this->inCodigoUnidade     = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataInscricao($valor) { $this->dtDataInscricao     = $valor; }
/**
    * @access Public
    * @param Float $valor
*/
function setProfundidadeMedia($valor) { $this->flProfundidadeMedia = $valor; }
/**
    * @access Public
    * @param Timestamp $valor
*/
function setTimestampLote($valor) { $this->tmTimestampLote     = $valor; }
/**
    * @access Public
    * @param Float $valor
*/
function setAreaLote($valor) { $this->flAreaLote          = $valor; }
/**
    * @access Public
    * @param Float $valor
*/
function setAreaEdificadaLote($valor) { $this->flAreaEdificada     = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataTermino($valor) { $this->dtDataTermino     = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataBaixa($valor) { $this->dtDataBaixa         = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setJustificativa($valor) { $this->stJustificativa     = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setJustificativaReativar($valor) { $this->stJustificativaReativar = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setChaveEnderecoImovel($valor) { $this->stChaveEnderecoImovel = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataAglutinacao($valor) { $this->dtDataAglutinacao   = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataParcelamento($valor) { $this->dtDataParcelamento  = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setCaucionado($valor) { $this->boCaucionado        = $valor; }

/**
    * @access Public
    * @param integer $valor
*/
function setCodigoParcelamento($valor) { $this->inCodigoParcelamentoSolo = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setValidado($valor) { $this->boValidado        = $valor; }
/**
    * @access Public
    * @param integer $valor
*/
function setQuantLotes($valor) { $this->inQuantLotes        = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setEdificacoes($valor) { $this->arEdificacoes        = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoLote() { return $this->inCodigoLote;        }
/**
    * @access Public
    * @return Integer
*/
function getCodigoLoteOriginal() { return $this->inCodigoLoteOriginal;}
/**
    * @access Public
    * @return Integer
*/
function getNumeroLote() { return $this->inNumeroLote;        }
/**
    * @access Public
    * @return Integer
*/
function getCodigoGrandeza() { return $this->inCodigoGrandeza;    }
/**
    * @access Public
    * @return Integer
*/
function getCodigoUnidade() { return $this->inCodigoUnidade;     }
/**
    * @access Public
    * @return Date
*/
function getDataInscricao() { return $this->dtDataInscricao;     }
/**
    * @access Public
    * @return Float
*/
function getProfundidadeMedia() { return $this->flProfundidadeMedia; }
/**
    * @access Public
    * @return Float
*/
function getAreaLote() { return $this->flAreaLote;          }
/**
    * @access Public
    * @return Float
*/
function getAreaEdificadaLote() { return $this->flAreaEdificada;     }
/**
    * @access Public
    * @return Float
*/
function getTimestampLote() { return $this->tmTimestampLote;     }
/**
    * @access Public
    * @param Date $valor
*/
function getDataTermino() { return $this->dtDataTermino;       }
/**
    * @access Public
    * @return Date
*/
function getDataBaixa() { return $this->dtDataBaixa;         }

/**
    * @access Public
    * @return String
*/
function getJustificativa() { return $this->stJustificativa;     }

/**
    * @access Public
    * @return String
*/
function getJustificativaReativar() { return $this->stJustificativaReativar; }

/**
    * @access Public
    * @return Date
*/
function getDataAglutinacao() { return $this->dtDataAglutinacao;   }
/**
    * @access Public
    * @return Date
*/
function getDataParcelamento() { return $this->dtDataParcelamento;  }
/**
    * @access Public
    * @return Boolean
*/
function getCaucionado() { return $this->boCaucionado;        }
/**
    * @access Public
    * @return integer
*/
function getCodigoParcelamento() { return $this->inCodigoParcelamentoSolo;        }
/**
    * @access Public
    * @return Boolean
*/
function getValidado() { return $this->boValidado;         }
/**
    * @access Public
    * @return integer
*/
function getQuantLotes() { return $this->inQuantLotes;       }
/**
    * @access Public
    * @return integer
*/
function getEdificacoes() { return $this->arEdificacoes;      }

/**
     * Método construtor
     * @access Private
*/
function RCIMLote()
{
    $this->obTCIMLote                = new TCIMLote;
    $this->obTCIMProfundidadeMedia   = new TCIMProfundidadeMedia;
    $this->obTCIMAreaLote            = new TCIMAreaLote;
    $this->obTCIMBaixaLote           = new TCIMBaixaLote;
    $this->obTCIMLoteBairro          = new TCIMLoteBairro;
    $this->obTCIMLoteLocalizacao     = new TCIMLoteLocalizacao;
    $this->obTCIMConfrontacao        = new TCIMConfrontacao;
    $this->obTCIMLoteProcesso        = new TCIMLoteProcesso;
    $this->obTCIMImovelLote          = new TCIMImovelLote;
    $this->obTransacao               = new Transacao;
    $this->obRCIMTrecho              = new RCIMTrecho;
    $this->obRCIMLocalizacao         = new RCIMLocalizacao;
    $this->obRCIMImovel              = new RCIMImovel( $this );
    $this->obRCIMBairro              = new RCIMBairro;
    $this->obRProcesso               = new RProcesso;
    $this->obRCIMConfiguracao        = new RCIMConfiguracao;
    $this->obTCIMParcelamentoSolo    = new TCIMParcelamentoSolo;
    $this->obTCIMLoteParcelado       = new TCIMLoteParcelado;
    $this->obTUnidadeMedida          = new TUnidadeMedida;
    $this->obRCIMConfrontacaoTrecho  = new RCIMConfrontacaoTrecho( $this );
    $this->arRCIMLote                = array();
    $this->arRCIMConfrontacaoTrecho  = array();
    $this->arRCIMConfrontacaoDiversa = array();
    $this->arRCIMConfrontacaoLote    = array();
}

/**
    * Inclui os dados setados para Lote
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirLote($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCIMLote->proximoCod( $this->inCodigoLote , $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMLote->setDado( "cod_lote",     $this->inCodigoLote    );
            $this->obTCIMLote->setDado( "dt_inscricao", $this->dtDataInscricao );
            $obErro = $this->obTCIMLote->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMProfundidadeMedia->setDado( "cod_lote",        $this->inCodigoLote        );
                $this->obTCIMProfundidadeMedia->setDado( "vl_profundidade_media", $this->flProfundidadeMedia );
                $obErro = $this->obTCIMProfundidadeMedia->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTCIMAreaLote->setDado( "cod_lote",     $this->inCodigoLote     );
                    $this->obTCIMAreaLote->setDado( "cod_grandeza", $this->inCodigoGrandeza );
                    $this->obTCIMAreaLote->setDado( "cod_unidade" , $this->inCodigoUnidade  );
                    $this->obTCIMAreaLote->setDado( "area_real",    $this->flAreaLote       );
                    $obErro = $this->obTCIMAreaLote->inclusao( $boTransacao );

                    if ( !$obErro->ocorreu() ) {
                        $this->obTCIMLoteBairro->setDado( "cod_lote",      $this->inCodigoLote                       );
                        $this->obTCIMLoteBairro->setDado( "cod_bairro",    $this->obRCIMBairro->getCodigoBairro()    );
                        $this->obTCIMLoteBairro->setDado( "cod_uf",        $this->obRCIMBairro->getCodigoUF()        );
                        $this->obTCIMLoteBairro->setDado( "cod_municipio", $this->obRCIMBairro->getCodigoMunicipio() );
                        $obErro = $this->obTCIMLoteBairro->inclusao( $boTransacao );

                        if ( !$obErro->ocorreu() ) {
                            $obErro = $this->validaLocalizacao( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $this->obTCIMLoteLocalizacao->setDado( "cod_lote",        $this->inCodigoLote );
                                $this->obTCIMLoteLocalizacao->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                                $this->obTCIMLoteLocalizacao->setDado( "valor",           $this->inNumeroLote );

                                $obErro = $this->obTCIMLoteLocalizacao->inclusao( $boTransacao );
                                if ( !$obErro->ocorreu() AND !$this->boLoteDesmembrado ) {
                                    $obErro = $this->salvarConfrontacoes( $boTransacao );
                                }
                            }
                            if ( !$obErro->ocorreu() and  $this->obRProcesso->getCodigoProcesso() and $this->obRProcesso->getExercicio() ) {
                                $this->obTCIMLoteProcesso->setDado( "cod_lote"     , $this->inCodigoLote                     );
                                $this->obTCIMLoteProcesso->setDado( "cod_processo" , $this->obRProcesso->getCodigoProcesso() );
                                $this->obTCIMLoteProcesso->setDado( "ano_exercicio", $this->obRProcesso->getExercicio()      );
                                $obErro = $this->obTCIMLoteProcesso->inclusao( $boTransacao );
                            }
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLote );

    return $obErro;
}

/**
    * Altera os dados do Lote Setado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarLote($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMLote->setDado( "cod_lote",     $this->inCodigoLote    );
        $this->obTCIMLote->setDado( "dt_inscricao", $this->dtDataInscricao );
        $obErro = $this->obTCIMLote->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMProfundidadeMedia->setDado( "cod_lote",        $this->inCodigoLote        );
            $this->obTCIMProfundidadeMedia->setDado( "vl_profundidade_media", $this->flProfundidadeMedia );
            $obErro = $this->obTCIMProfundidadeMedia->alteracao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMAreaLote->setDado( "cod_lote",     $this->inCodigoLote     );
                $this->obTCIMAreaLote->setDado( "cod_grandeza", $this->inCodigoGrandeza );
                $this->obTCIMAreaLote->setDado( "cod_unidade" , $this->inCodigoUnidade  );
                $this->obTCIMAreaLote->setDado( "area_real",    $this->flAreaLote       );
                $obErro = $this->obTCIMAreaLote->inclusao( $boTransacao );

                if ( !$obErro->ocorreu() && $this->obRCIMBairro->getCodigoBairro() ) {
                    $this->obTCIMLoteBairro->setDado( "cod_lote",      $this->inCodigoLote         );
                    $this->obTCIMLoteBairro->setDado( "cod_bairro",$this->obRCIMBairro->getCodigoBairro() );
                    $this->obTCIMLoteBairro->setDado( "cod_uf", $this->obRCIMBairro->getCodigoUF()     );
                    $this->obTCIMLoteBairro->setDado( "cod_municipio", $this->obRCIMBairro->getCodigoMunicipio() );

                    $obErro = $this->obTCIMLoteBairro->alteracao( $boTransacao );

                }

                if ( !$obErro->ocorreu() ) {
                    $this->obTCIMLoteLocalizacao->setDado( "cod_lote",        $this->inCodigoLote );
                    $obErro = $this->salvarConfrontacoes( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        if ( $this->obRProcesso->getCodigoProcesso() ) {
                            $this->obTCIMLoteProcesso->setDado( "cod_lote"     , $this->inCodigoLote                     );
                            $this->obTCIMLoteProcesso->setDado( "cod_processo" , $this->obRProcesso->getCodigoProcesso() );
                            $this->obTCIMLoteProcesso->setDado( "ano_exercicio", $this->obRProcesso->getExercicio()      );
                            /* Add em 07/04/2005 por Lucas Stephanou para Bug #1459  */
                            $this->obTCIMLoteProcesso->setDado( "timestamp"    , $this->getTimestampLote()               );
                            $obErro = $this->obTCIMLoteProcesso->inclusao( $boTransacao );
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLote );

    return $obErro;
}

/**
    * Exclui os dados de Lote setado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirLote($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
       $this->obTCIMProfundidadeMedia->setDado( "cod_lote", $this->inCodigoLote );
       $obErro = $this->obTCIMProfundidadeMedia->exclusao( $boTransacao );
       if ( !$obErro->ocorreu() ) {
           $this->obTCIMLoteProcesso->setDado( "cod_lote", $this->inCodigoLote );
           $obErro = $this->obTCIMLoteProcesso->exclusao( $boTransacao );
           if ( !$obErro->ocorreu() ) {
               $this->obTCIMAreaLote->setDado( "cod_lote", $this->inCodigoLote );
               $obErro = $this->obTCIMAreaLote->exclusao( $boTransacao );
               if ( !$obErro->ocorreu() ) {
                   $this->obTCIMLoteBairro->setDado( "cod_lote",      $this->inCodigoLote                       );
                   $this->obTCIMLoteBairro->setDado( "cod_bairro",    $this->obRCIMBairro->getCodigoBairro()    );
                   $this->obTCIMLoteBairro->setDado( "cod_uf",        $this->obRCIMBairro->getCodigoUF()        );
                   $this->obTCIMLoteBairro->setDado( "cod_municipio", $this->obRCIMBairro->getCodigoMunicipio() );
                   $obErro = $this->obTCIMLoteBairro->exclusao( $boTransacao );
                   if ( !$obErro->ocorreu() ) {
                       $this->obTCIMLoteLocalizacao->setDado( "cod_lote", $this->inCodigoLote );
                       $this->obTCIMLoteLocalizacao->exclusao( $boTransacao );
                       if ( !$obErro->ocorreu() ) {
                           $obErro = $this->salvarConfrontacoes( $boTransacao , true );
                           if ( !$obErro->ocorreu() ) {
                               $this->obTCIMLote->setDado( "cod_lote", $this->inCodigoLote );
                               $obErro = $this->obTCIMLote->exclusao( $boTransacao );
                          }
                       }
                   }
               }
           }
       }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLote );

    return $obErro;
}

/**
    * Executa a baixa do lote setado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function baixarLote($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obRCIMImovel->roRCIMLote->setNumeroLote( $this->inCodigoLote );
        //$this->obRCIMImovel->listarImoveis( $rsImovel,$boTransacao );
        $this->obRCIMImovel->listarImoveisAtivosLote( $rsImovel,$boTransacao );
        if ( !$rsImovel->eof() ) {
            $obErro->setDescricao( "Lote deve estar vazio!" );

            return $obErro;
        }
/*
        while ( !$rsImovel->eof() ) {
            if ($rsImovel->getCampo("inscricao_municipal")) {
                $this->obRCIMImovel->setNumeroInscricao( $rsImovel->getCampo("inscricao_municipal") );
                $this->obRCIMImovel->setJustificativa( $this->stJustificativa );
                $this->obRCIMImovel->baixarImovel( $boTransacao );
            }
            $rsImovel->proximo();
        }
*/
        $dtdiaHOJE = date ("d-m-Y");
        $this->obTCIMBaixaLote->setDado( "dt_inicio",     $dtdiaHOJE );
        $this->obTCIMBaixaLote->setDado( "cod_lote",      $this->inCodigoLote    );
        $this->obTCIMBaixaLote->setDado( "justificativa", $this->stJustificativa );
        $obErro = $this->obTCIMBaixaLote->inclusao( $boTransacao );

        if ( !$obErro->ocorreu() and  $this->obRProcesso->getCodigoProcesso() and $this->obRProcesso->getExercicio() ) {
            $this->obTCIMLoteProcesso->setDado( "cod_lote"     , $this->inCodigoLote                     );
            $this->obTCIMLoteProcesso->setDado( "cod_processo" , $this->obRProcesso->getCodigoProcesso() );
            $this->obTCIMLoteProcesso->setDado( "ano_exercicio", $this->obRProcesso->getExercicio()      );
            $obErro = $this->obTCIMLoteProcesso->inclusao( $boTransacao );
        }

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMBaixaLote );

    return $obErro;
}

/**
    * Executa a reativacao do lote setado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function reativarLote($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $dtdiaHOJE = date ("d-m-Y");
        $this->obTCIMBaixaLote->setDado( "dt_termino",    $dtdiaHOJE             );
        $this->obTCIMBaixaLote->setDado( "timestamp",     $this->dtDataInscricao );
        $this->obTCIMBaixaLote->setDado( "cod_lote",      $this->inCodigoLote    );
        $this->obTCIMBaixaLote->setDado( "justificativa", $this->stJustificativa );
        $this->obTCIMBaixaLote->setDado( "justificativa_termino", $this->stJustificativaReativar );

        $obErro = $this->obTCIMBaixaLote->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() and  $this->obRProcesso->getCodigoProcesso() and $this->obRProcesso->getExercicio() ) {
            $this->obTCIMLoteProcesso->setDado( "cod_lote"     , $this->inCodigoLote                     );
            $this->obTCIMLoteProcesso->setDado( "cod_processo" , $this->obRProcesso->getCodigoProcesso() );
            $this->obTCIMLoteProcesso->setDado( "ano_exercicio", $this->obRProcesso->getExercicio()      );
            $obErro = $this->obTCIMLoteProcesso->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMBaixaLote );

    return $obErro;
}

/**
    *
    * @access Public
    * @param  Objecat $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarLotes(&$rsRecordSet ,$boTransacao = "")
{
    $stFiltro = "";
    if ($this->obRCIMLocalizacao->inCodigoLocalizacao) {
        $stFiltro .= " AND LL.COD_LOCALIZACAO = ".$this->obRCIMLocalizacao->inCodigoLocalizacao;
    }
    if ($this->inNumeroLote) {
        $stFiltro .= " AND LPAD( UPPER( LL.VALOR) , 10,'0') = LPAD( UPPER('".$this->inNumeroLote."'), 10,'0') ";
    }
    if ($this->inCodigoLote) {
        $stFiltro .= " AND LL.VALOR != '".$this->inCodigoLote."'";
    }
    $stOrdem  = " ORDER BY LL.VALOR ";
    $obErro = $this->obTCIMLote->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    *
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function buscarLotes(&$rsRecordSet ,$boTransacao = "")
{
    $stFiltro = "";
    if ($this->obRCIMLocalizacao->inCodigoLocalizacao) {
        $stFiltro .= " AND LL.COD_LOCALIZACAO = ".$this->obRCIMLocalizacao->inCodigoLocalizacao;
    }
    if ($this->inNumeroLote) {
        $stFiltro .= " AND LPAD( UPPER( LL.VALOR) , 10,'0') = LPAD( UPPER('".$this->inNumeroLote."'), 10,'0') ";
    }

    if ( $this->obRCIMLocalizacao->getValorComposto() ) {
        $stFiltro .= " AND LA.valor_composto = '".$this->obRCIMLocalizacao->getValorComposto()."'";
    }

    if ($this->inCodigoLote) {
        $stFiltro .= " AND L.COD_LOTE = ".$this->inCodigoLote;
    }
    $stOrdem  = " ORDER BY VALOR ";
    $obErro = $this->obTCIMLote->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    *
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function buscarLotesCadastrados(&$rsRecordSet ,$boTransacao = "")
{
    $stFiltro = "";
    if ($this->obRCIMLocalizacao->inCodigoLocalizacao) {
        $stFiltro .= " AND LL.COD_LOCALIZACAO = ".$this->obRCIMLocalizacao->inCodigoLocalizacao;
    }
    if ($this->inNumeroLote) {
        $stFiltro .= " AND LPAD( UPPER( LL.VALOR) , 10,'0') = LPAD( UPPER('".$this->inNumeroLote."'), 10,'0') ";
    }
    if ($this->inCodigoLote) {
        $stFiltro .= " AND L.COD_LOTE = ".$this->inCodigoLote;
    }
    $stOrdem  = " ORDER BY VALOR ";
    $obErro = $this->obTCIMLote->recuperaRelacionamentoLotesCadastrados( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    *
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarLote($boTransacao = "")
{
//  $stFiltro = " AND L.COD_LOTE = ".$this->inCodigoLote; - Otimização da consulta - GRIS - 04/01/2006
    $stFiltro = " AND lote.cod_lote = ".$this->inCodigoLote;
    $obErro = $this->obTCIMLote->recuperaRelacionamentoConsulta( $rsRecordSet, $stFiltro, "", $boTransacao );

    $this->obRCIMConfiguracao->consultarConfiguracao();
    $this->obRCIMConfiguracao->setCodigoModulo( 12 );
    $this->obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
    $this->obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $rsRecordSet->addStrPad( "valor", strlen( $stMascaraLote ), "0" );
        $this->inNumeroLote        = $rsRecordSet->getCampo( "valor"         );
        $this->inCodigoGrandeza    = $rsRecordSet->getCampo( "cod_grandeza"  );
        $this->inCodigoUnidade     = $rsRecordSet->getCampo( "cod_unidade"   );
        $this->dtDataInscricao     = $rsRecordSet->getCampo( "dt_inscricao"  );
        $this->tmTimestampLote     = $rsRecordSet->getCampo( "timestamp"     );
        $this->dtDataBaixa         = $rsRecordSet->getCampo( "dt_baixa"      );
        $this->stJustificativa     = $rsRecordSet->getCampo( "justificativa" );
        $this->flProfundidadeMedia = number_format( $rsRecordSet->getCampo( "vl_profundidade_media" ), 2, ',', '.');
        $this->flAreaLote          = number_format( $rsRecordSet->getCampo( "area_real" ), 2, ',', '.');// $rsRecordSet->getCampo( "area_real" );
        $this->obRCIMBairro->setCodigoBairro    ( $rsRecordSet->getCampo( "cod_bairro" )    );
        $this->obRCIMBairro->setCodigoUF        ( $rsRecordSet->getCampo( "cod_uf" )        );
        $this->obRCIMBairro->setCodigoMunicipio ( $rsRecordSet->getCampo( "cod_municipio" ) );
        $obErro = $this->obRCIMBairro->consultarBairro ( $boTransacao );
        if ( !$obErro->ocorreu() and $this->obRCIMLocalizacao->getCodigoLocalizacao() ) {
            $obErro = $this->obRCIMLocalizacao->consultarLocalizacao();
        }
        /*
        Aguardando a aglutinacao e a desmenbeção
        if ( !$obErro->ocorreu() ) {
            $stFiltro = " AND C.COD_LOTE = ".$this->inCodigoLote;
            $obErro = $this->obTCIMConfrontacao( $rsConfrontacaoes , $stFiltro , "", $boTransacao );
            if ( !$obErro->ocorreu() ) {

            }
        }
        */
    }

    return $obErro;
}

/**
    * Função para consulta de lote pelo caminho da validação de desmembramento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarLoteValidacao($boTransacao = "")
{
    $stFiltro = " AND L.COD_LOTE = ".$this->inCodigoLote;
    $obErro = $this->obTCIMLote->recuperaRelacionamentoLoteValidado( $rsRecordSet, $stFiltro, "", $boTransacao );
    $this->obRCIMConfiguracao->consultarConfiguracao();
    $this->obRCIMConfiguracao->setCodigoModulo( 12 );
    $this->obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
    $this->obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $rsRecordSet->addStrPad( "valor", strlen( $stMascaraLote ), "0" );
        $this->inNumeroLote        = $rsRecordSet->getCampo( "valor" );
        $this->inCodigoGrandeza    = $rsRecordSet->getCampo( "cod_grandeza" );
        $this->inCodigoUnidade     = $rsRecordSet->getCampo( "cod_unidade" );
        $this->dtDataInscricao     = $rsRecordSet->getCampo( "dt_inscricao" );
        $this->flProfundidadeMedia = number_format( $rsRecordSet->getCampo( "vl_profundidade_media" ), 2, ',', '.');
        $this->flAreaLote          = number_format( $rsRecordSet->getCampo( "area_real" ), 2, ',', '.');// $rsRecordSet->getCampo( "area_real" );
        $this->obRCIMBairro->setCodigoBairro    ( $rsRecordSet->getCampo( "cod_bairro" )    );
        $this->obRCIMBairro->setCodigoUF        ( $rsRecordSet->getCampo( "cod_uf" )        );
        $this->obRCIMBairro->setCodigoMunicipio ( $rsRecordSet->getCampo( "cod_municipio" ) );
        $obErro = $this->obRCIMBairro->consultarBairro ( $boTransacao );
        if ( !$obErro->ocorreu() and $this->obRCIMLocalizacao->getCodigoLocalizacao() ) {
            $obErro = $this->obRCIMLocalizacao->consultarLocalizacao();
        }
        /*
        Aguardando a aglutinacao e a desmenbeção
        if ( !$obErro->ocorreu() ) {
            $stFiltro = " AND C.COD_LOTE = ".$this->inCodigoLote;
            $obErro = $this->obTCIMConfrontacao( $rsConfrontacaoes , $stFiltro , "", $boTransacao );
            if ( !$obErro->ocorreu() ) {

            }
        }
        */
    }

    return $obErro;
}

/**
    * Adiciona lotes
    * @access Public
    * @param  Integer $inCodigoLote Código do lote adicionado
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function addLote($inCodigoLote , $boTrasacao = "")
{
    $this->arRCIMLote[] = new RCIMLote();
    $this->arRCIMLote[ count( $this->arRCIMLote ) - 1 ]->setCodigoLote( $inCodigoLote );
    $obErro = $this->arRCIMLote[ count( $this->arRCIMLote ) - 1 ]->consultarLote( $boTrasacao );

    return $obErro;
}

function addImovel()
{
    $this->obRCIMImovel = new RCIMImovel( $this );
}

/**
    * Adiciona um objeto de confrontação de lote no lote
    * @access Public
*/
function addConfrontacaoLote()
{
    $this->arRCIMConfrontacaoLote[] = new RCIMConfrontacaoLote( $this );
    $this->roUltimaConfrontacaoLote = &$this->arRCIMConfrontacaoLote[ count($this->arRCIMConfrontacaoLote) - 1 ];
}

/**
    * Adiciona um objeto de confrontação diversa no lote
    * @access Public
*/
function addConfrontacaoDiversa()
{
    $this->arRCIMConfrontacaoDiversa[] = new RCIMConfrontacaoDiversa( $this );
    $this->roUltimaConfrontacaoDiversa = &$this->arRCIMConfrontacaoDiversa[ count($this->arRCIMConfrontacaoDiversa) - 1 ];
}
/**
    * Adiciona um objeto de confrontação de trecho no lote
    * @access Public
*/
function addConfrontacaoTrecho($stChaveTrecho)
{
    $arChaveTrecho = explode( ".", $stChaveTrecho );
    $this->arRCIMConfrontacaoTrecho[] = new RCIMConfrontacaoTrecho( $this );
    $this->arRCIMConfrontacaoTrecho[ count($this->arRCIMConfrontacaoTrecho) - 1 ]->obRCIMTrecho->setCodigoLogradouro( $arChaveTrecho[0] );
    $this->arRCIMConfrontacaoTrecho[ count($this->arRCIMConfrontacaoTrecho) - 1 ]->obRCIMTrecho->setSequencia( $arChaveTrecho[1] );
    $obErro = $this->arRCIMConfrontacaoTrecho[ count($this->arRCIMConfrontacaoTrecho) - 1 ]->obRCIMTrecho->consultarTrecho( $rs);
    $this->roUltimaConfrontacaoTrecho = &$this->arRCIMConfrontacaoTrecho[ count($this->arRCIMConfrontacaoTrecho) - 1 ];

    return $obErro;
}

/**
    * Salva as confrontações informadas para um Lote
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarConfrontacoes($boTransacao = "" , $boExclusao = false)
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obFlagTrechoPrincipal = false;
        $stFiltro = " WHERE C.COD_LOTE = ".$this->inCodigoLote;
        $obErro = $this->obTCIMConfrontacao->recuperaRelacionamento( $rsRecordSet, $stFiltro, "", $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $arConfrontacoes = array( "TRECHO" => array(), "LOTE" => array(), "OUTROS" => array() );
            //MONTA UM ARRAY COM AS CONFRONTACOES DE UM DETERMINADO LOTE
            while ( !$rsRecordSet->eof() ) {
                 switch (  strtoupper( $rsRecordSet->getCampo("tipo") ) ) {
                     case "TRECHO":
                         $arConfrontacoes["TRECHO"][$rsRecordSet->getCampo("cod_confrontacao")] = true;
                         $arConfrontacaoTrecho[$rsRecordSet->getCampo("cod_confrontacao")]["cod_logradouro"] = $rsRecordSet->getCampo("cod_logradouro");
                         $arConfrontacaoTrecho[$rsRecordSet->getCampo("cod_confrontacao")]["sequencia"] = $rsRecordSet->getCampo("sequencia");
                     break;
                     case "LOTE":
                         $arConfrontacoes["LOTE"][$rsRecordSet->getCampo("cod_confrontacao")] = true;
                     break;
                     case "OUTROS":
                         $arConfrontacoes["OUTROS"][$rsRecordSet->getCampo("cod_confrontacao")] = true;
                     break;
                 }
                 $rsRecordSet->proximo();
            }
            //COMPARA AS CONFRONTACOES INFORMADAS COM AS CADASTRADAS
            if ( !$obErro->ocorreu() ) {
                if ($this->stChaveEnderecoImovel) {
                    $arEnderecoImovel = explode( ".", $this->stChaveEnderecoImovel );
                }
                foreach ($this->arRCIMConfrontacaoTrecho as $obRCIMConfrontacao) {
                    if ( $obRCIMConfrontacao->getPrincipal() == "t" ) {
                        $obFlagTrechoPrincipal = true;
                    }
                    if ( $obRCIMConfrontacao->getCodigoConfrontacao() ) {
                        unset( $arConfrontacoes["TRECHO"][$obRCIMConfrontacao->getCodigoConfrontacao()] );
                        $obRCIMConfrontacao->alterarConfrontacao( $boTransacao );
                    } else {
                        $obErro = $obRCIMConfrontacao->incluirConfrontacao( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        } else {
                             $inCodigoLogradouro = $obRCIMConfrontacao->obRCIMTrecho->getCodigoLogradouro();
                             $inSequencia = $obRCIMConfrontacao->obRCIMTrecho->getSequencia();
                             if ($this->stChaveEnderecoImovel and $inCodigoLogradouro == $arEnderecoImovel[0] and $inSequencia == $arEnderecoImovel[1]) {
                                 $this->obRCIMImovel->obRCIMConfrontacaoTrecho->setCodigoConfrontacao( $obRCIMConfrontacao->getCodigoConfrontacao() );
                            }
                        }
                    }
                }
            }

            if ( !$obErro->ocorreu() ) {
                foreach ($this->arRCIMConfrontacaoDiversa as $obRCIMConfrontacao) {
                    if ( $obRCIMConfrontacao->getCodigoConfrontacao() ) {
                        unset( $arConfrontacoes["OUTROS"][$obRCIMConfrontacao->getCodigoConfrontacao()] );
                        $obRCIMConfrontacao->alterarConfrontacao( $boTransacao );
                    } else {
                        $obErro = $obRCIMConfrontacao->incluirConfrontacao( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
            }
            if ( !$obErro->ocorreu() ) {
                foreach ($this->arRCIMConfrontacaoLote as $obRCIMConfrontacao) {
                    if ( $obRCIMConfrontacao->getCodigoConfrontacao() ) {
                        unset( $arConfrontacoes["LOTE"][$obRCIMConfrontacao->getCodigoConfrontacao()] );
                        $obRCIMConfrontacao->alterarConfrontacao( $boTransacao );
                    } else {
                        $obErro = $obRCIMConfrontacao->incluirConfrontacao( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
            }
            if ( !$obErro->ocorreu() ) {
                foreach ($arConfrontacoes["TRECHO"]  AS $inCodigoConfrontacao => $boValor) {
                    $obRCIMConfrontacao = new RCIMConfrontacaoTrecho( $this );
                    $obRCIMConfrontacao->setCodigoConfrontacao( $inCodigoConfrontacao );
                    $obRCIMConfrontacao->obRCIMTrecho->setCodigoLogradouro( $arConfrontacaoTrecho[$inCodigoConfrontacao]["cod_logradouro"]);
                    $obRCIMConfrontacao->obRCIMTrecho->setSequencia( $arConfrontacaoTrecho[$inCodigoConfrontacao]["sequencia"]);
                    $obErro = $obRCIMConfrontacao->excluirConfrontacao( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    foreach ($arConfrontacoes["LOTE"]  AS $inCodigoConfrontacao => $boValor) {
                        $obRCIMConfrontacao = new RCIMConfrontacaoLote( $this );
                        $obRCIMConfrontacao->setCodigoConfrontacao( $inCodigoConfrontacao );
                        $obErro = $obRCIMConfrontacao->excluirConfrontacao( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                    if ( !$obErro->ocorreu() ) {
                         foreach ($arConfrontacoes["OUTROS"]  AS $inCodigoConfrontacao => $boValor) {
                             $obRCIMConfrontacao = new RCIMConfrontacaoDiversa( $this );
                             $obRCIMConfrontacao->setCodigoConfrontacao( $inCodigoConfrontacao );
                             $obErro = $obRCIMConfrontacao->excluirConfrontacao( $boTransacao );
                             if ( $obErro->ocorreu() ) {
                                 break;
                             }
                         }
                    }
                }
            }
        }
        if (!$obFlagTrechoPrincipal and !$boExclusao) {
            $obErro->setDescricao( "Deve haver ao menos uma confrontação de trecho definida como testada!" );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLoteLocalizacao );

    return $obErro;
}

function listarUnidadeMedida(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro  = " WHERE COD_GRANDEZA = 2 AND ";
    $stFiltro .= " ( COD_UNIDADE = 1 OR COD_UNIDADE = 3 ) ";
    $obErro = $this->obTUnidadeMedida->recuperaTodos( $rsRecordSet, $stFiltro, "", $boTransacao );

    return $obErro;
}

function listarProcessos(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodigoLote()) {
        $stFiltro .= " lp.cod_lote = ".$this->getCodigoLote()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY lp.timestamp";
    $obErro = $this->obTCIMLote->recuperaRelacionamentoProcesso( $rsRecordSet, $stFiltro,$stOrdem, $boTransacao );

    return $obErro;
}

function validaLocalizacao($obTransacao = "")
{
    $stFiltro  = " WHERE COD_LOCALIZACAO = ".$this->obRCIMLocalizacao->getCodigoLocalizacao()." AND ";
    $stFiltro .= " LPAD( UPPER( VALOR ), 10,'0' ) = LPAD( UPPER('".$this->inNumeroLote."'),10,'0')";
    $obErro = $this->obTCIMLoteLocalizacao->recuperaTodos( $rsLocalizacao, $stFiltro, "", $obTransacao );
    if ( !$obErro->ocorreu() and !$rsLocalizacao->eof() ) {
        $obErro->setDescricao( "Número do lote já cadastrado nesta localização!" );
    }

    return $obErro;
}

/**
    *
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erroimobiliario.lote_localizacao
*/
function mostrarLotes(&$rsRecordSet ,$boTransacao = "")
{
    $stFiltro = "";
    if ($this->obRCIMLocalizacao->inCodigoLocalizacao) {
        $stFiltro .= " AND LL.COD_LOCALIZACAO = ".$this->obRCIMLocalizacao->inCodigoLocalizacao;
    }
    if ($this->inNumeroLote) {
        $stFiltro .= " AND LPAD( UPPER( LL.VALOR) , 10,'0') = LPAD( UPPER('".$this->inNumeroLote."'), 10,'0') ";
    }
    if ($this->inCodigoLote) {
        $stFiltro .= " AND L.COD_LOTE != ".$this->inCodigoLote;
    }
    $stOrdem  = " ORDER BY VALOR ";
    $obErro = $this->obTCIMLote->mostraLote( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    *
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erroimobiliario.lote_localizacao
*/
function mostrarLotesParcelamento(&$rsRecordSet ,$boTransacao = "")
{
    $stFiltro = "";
    if ($this->obRCIMLocalizacao->inCodigoLocalizacao) {
        $stFiltro .= " AND LL.COD_LOCALIZACAO = ".$this->obRCIMLocalizacao->inCodigoLocalizacao;
    }
    if ($this->inNumeroLote) {
        $stFiltro .= " AND LPAD( UPPER( LL.VALOR) , 10,'0') = LPAD( UPPER('".$this->inNumeroLote."'), 10,'0') ";
    }
    if ($this->inCodigoLote) {
        $stFiltro .= " AND L.COD_LOTE != ".$this->inCodigoLote;
    }
    $stOrdem  = " ORDER BY VALOR ";
    $obErro = $this->obTCIMLote->mostraLoteParcelamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
* Desmembra o Lote Selecionado em "X" Lotes resultantes
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function desmembramentodeLotes($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arLetras = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','r','s','t','u','v','x','z');
        $stFiltro  = " WHERE COD_LOCALIZACAO = ".$this->obRCIMLocalizacao->getCodigoLocalizacao()." AND ";
        $stFiltro .= " VALOR LIKE '%".$this->getNumeroLote()."%' ";

        $obErro = $this->obTCIMLoteLocalizacao->recuperaTodos( $rsLoteLocalizacao, $stFiltro, "", $boTransacao );
        $inCountPartida = $rsLoteLocalizacao->getNumLinhas() - 1;

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->alterarLote( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->desmembrarLote( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $inNumeroLote = $this->getNumeroLote();
                    $this->obTCIMLoteLocalizacao->recuperaTodos( $rsLoteLocalizacao, $stFiltro, "", $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        for ($inCont = $inCountPartida;$inCont < $this->getQuantLotes()+$inCountPartida-1;$inCont++) {
                            $stLetra = "";
                            $stCont = "'".$inCont."'";
                            for ($inX=0; $inX<strlen($stCont); $inX++ ) {
                                $stLetra .= $arLetras[$stCont[$inX]];
                            }

                            $this->setNumeroLote( $inNumeroLote.$stLetra );
                            $this->boLoteDesmembrado = true;
                            $obErro = $this->incluirLote( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $obErro = $this->desmembrarLote( $boTransacao );
                            }
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLote );

    return $obErro;
}

/**
* Desmembra o Lote
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function desmembrarLote($boTransacao = "")
{
    $boFlagParcelamento = false;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if (!$this->inCodigoParcelamentoSolo) {
            $boFlagParcelamento = true;
            $obErro = $this->obTCIMParcelamentoSolo->proximoCod( $this->inCodigoParcelamentoSolo, $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            if ($boFlagParcelamento) {
                $this->obTCIMParcelamentoSolo->setDado( "cod_parcelamento" , $this->getCodigoParcelamento()     );
                $this->obTCIMParcelamentoSolo->setDado( "cod_lote"         , $this->getCodigoLote()             );
                $this->obTCIMParcelamentoSolo->setDado( "cod_tipo"         , 2                                  );
                $this->obTCIMParcelamentoSolo->setDado( "dt_parcelamento"  , $this->getDataParcelamento()       );
                $obErro = $this->obTCIMParcelamentoSolo->inclusao( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                //inclusão da tabela lote parcelado
                $this->obTCIMLoteParcelado->setDado( "cod_lote",            $this->getCodigoLote()          );
                $this->obTCIMLoteParcelado->setDado( "cod_parcelamento",    $this->getCodigoParcelamento()  );
                $this->obTCIMLoteParcelado->setDado( "validado",            false                           );
                $obErro = $this->obTCIMLoteParcelado->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLote );

    return $obErro;
}

/**
* Aglutinar os Lotes Selecionados em um Lote  remanescente
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function aglutinarLote($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->alterarLote( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTCIMParcelamentoSolo->proximoCod( $this->inCodigoParcelamentoSolo, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMParcelamentoSolo->setDado( "cod_lote"         , $this->getCodigoLote()             );
                $this->obTCIMParcelamentoSolo->setDado( "cod_parcelamento" , $this->getCodigoParcelamento()     );
                $this->obTCIMParcelamentoSolo->setDado( "cod_tipo"         , 1                                  );
                $this->obTCIMParcelamentoSolo->setDado( "dt_parcelamento"  , $this->getDataParcelamento()       );
                $obErro = $this->obTCIMParcelamentoSolo->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    if ( count($this->arRCIMLote) > 0 ) {
                        foreach ($this->arRCIMLote as $inChave => $obRCIMLote) {
                            $this->obTCIMLoteParcelado->setDado( "cod_lote",         $obRCIMLote->getCodigoLote()      );
                            $this->obTCIMLoteParcelado->setDado( "cod_parcelamento", $this->getCodigoParcelamento()    );
                            $this->obTCIMLoteParcelado->setDado( "validado",         true                              );
                            $obErro = $this->obTCIMLoteParcelado->inclusao( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $obErro = $obRCIMLote->listarLotesAglutinar( $rsAglutinar, $boTransacao );
                                while ( !$rsAglutinar->eof() ) {
                                    $this->obTCIMImovelLote->setDado( "cod_lote",  $this->getCodigoLote() );
                                    $this->obTCIMImovelLote->setDado( "inscricao_municipal", $rsAglutinar->getCampo( "inscricao_municipal") );
                                    $obErro = $this->obTCIMImovelLote->inclusao( $boTransacao );
                                    if ( !$obErro->ocorreu() ) {
                                        $stFiltro  = " AND L.COD_LOTE = ".$this->getCodigoLote();
                                        $stFiltro .= " AND CT.PRINCIPAL = TRUE";
                                        $obErro    = $this->obRCIMConfrontacaoTrecho->obTCIMConfrontacaoTrecho->recuperaRelacionamento( $rsRecordSet, $stFiltro, "", $boTransacao );
                                        $inCodConfrontacao = $rsRecordSet->getCampo('cod_confrontacao');
                                        $this->obRCIMConfrontacaoTrecho->obTCIMImovelConfrontacao->setDado( "inscricao_municipal" , $rsAglutinar->getCampo( "inscricao_municipal") );
                                        $this->obRCIMConfrontacaoTrecho->obTCIMImovelConfrontacao->setDado( "cod_lote"            , $this->getCodigoLote()   );
                                        $this->obRCIMConfrontacaoTrecho->obTCIMImovelConfrontacao->setDado( "cod_confrontacao"    , $inCodConfrontacao       );
                                        $this->obRCIMConfrontacaoTrecho->obTCIMImovelConfrontacao->alteracao( $boTransacao );
                                        if ( $obErro->ocorreu() ) {
                                            break;
                                        }
                                    }
                                    $rsAglutinar->proximo();
                                }
                            }
                        }
                    } else {
                        $obErro->setDescricao( "É necessário ao menos um lote para efetuar a aglutinação." );
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLote );

    return $obErro;
}

function listarLotesAglutinar(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodigoLote() ) {
        $stFiltro .= " IML.COD_LOTE =  ".$this->getCodigoLote()." AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $stOrdem = " ORDER BY IML.INSCRICAO_MUNICIPAL ";
    $obErro = $this->obTCIMImovelLote->recuperaRelacionamentoAglutinar( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Altera os dados do Lote Setado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarLoteParcelado($boTransacao = "", $stOrigem)
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $stOrigem == "validar" and !$obErro->ocorreu() ) {
        $stFiltro  = " WHERE COD_LOTE = ".$this->inCodigoLote;
        $stFiltro .= " AND VALIDADO = FALSE";
        $this->obTCIMLoteParcelado->recuperaTodos( $rsRecordSetParcelado,$stFiltro, "", $boTransacao  );
        $this->obTCIMLoteParcelado->setDado( "cod_lote",            $this->inCodigoLote    );
        $this->obTCIMLoteParcelado->setDado( "validado",            true                   );
        $this->obTCIMLoteParcelado->setDado( "cod_parcelamento",    $rsRecordSetParcelado->getCampo("cod_parcelamento")    );
        $obErro = $this->obTCIMLoteParcelado->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $stFiltro  = " WHERE COD_LOTE = ".$this->inCodigoLote;
            $obErro = $this->obTCIMParcelamentoSolo->recuperaTodos($rsRecordSetParcelamento,$stFiltro,"",$boTransacao);
            if ( !$obErro->ocorreu() ) {
                if ( $rsRecordSetParcelamento->getNumLinhas() < 1 ) {
                    $obErro = $this->validaLocalizacao( $boTransacao );
                }
                if ( !$obErro->ocorreu() ) {
                    $this->obTCIMLoteLocalizacao->setDado( "cod_lote",        $this->inCodigoLote );
                    $this->obTCIMLoteLocalizacao->setDado( "cod_localizacao", $this->obRCIMLocalizacao->getCodigoLocalizacao() );
                    $this->obTCIMLoteLocalizacao->setDado( "valor",           $this->inNumeroLote );
                    $obErro = $this->obTCIMLoteLocalizacao->alteracao( $boTransacao );
                }
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->alterarLote( $boTransacao );
        // vincular inscrições ao lote
        if ( !$obErro->ocorreu() ) {
            if ( is_array( $this->arEdificacoes ) ) {
                foreach ($this->arEdificacoes as $valor) {
                    $arInsMun = explode(",",$valor["inscricao_municipal"]);
                    foreach ($arInsMun  as $valor) {
                        $this->obRCIMImovel->obTCIMImovelLote->setDado( "cod_lote"           , $this->inCodigoLote   );
                        $this->obRCIMImovel->obTCIMImovelLote->setDado( "inscricao_municipal", $valor                );
                        $stInscricaoMunicipal = $valor;
                        $obErro = $this->obRCIMImovel->obTCIMImovelLote->inclusao($boTransacao);
                        if ( !$obErro->ocorreu() ) {
                            $stFiltro  = " AND L.COD_LOTE = ".$this->inCodigoLote;
                            $stFiltro .= " AND CT.PRINCIPAL = TRUE";
                            $obErro = $this->obRCIMConfrontacaoTrecho->obTCIMConfrontacaoTrecho->recuperaRelacionamento( $rsRecordSet, $stFiltro, "", $boTransacao );

                            $inCodConfrontacao = $rsRecordSet->getCampo('cod_confrontacao');
                            $this->obRCIMConfrontacaoTrecho->obTCIMImovelConfrontacao->setDado( "inscricao_municipal" , $stInscricaoMunicipal );
                            $this->obRCIMConfrontacaoTrecho->obTCIMImovelConfrontacao->setDado( "cod_lote"            , $this->inCodigoLote   );
                            $this->obRCIMConfrontacaoTrecho->obTCIMImovelConfrontacao->setDado( "cod_confrontacao"    , $inCodConfrontacao    );
                            $this->obRCIMConfrontacaoTrecho->obTCIMImovelConfrontacao->alteracao( $boTransacao );
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLote );

    return $obErro;
}

/**
    * Lista os proprietarios de determinado lote
    * @access Public
    * @param Boolean $boTransacao
    * @param Object $rsRecordSet
    * @return Object Object Erro
*/
function listarLoteProprietarios(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = $this->inCodigoLote;
    $obErro = $this->obTCIMLote->recuperaLoteProprietarios( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function recuperaAreaEdificadaLote($boTransacao = "")
{
    $stFiltro = $this->inCodigoLote;
    $obErro = $this->obTCIMLote->recuperaAreaEdificadaLote( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    $rsRecordSet->addFormatacao( 'area_lote', 'NUMERIC_BR' );
    $this->setAreaEdificadaLote( $rsRecordSet->getCampo('area_lote') );
}

/**
    * Consulta o Lote original de um lote parcelado
    * @access   Public
    * @param    Boolean $boTransacao
    * @param    Integer $inCodLoteOriginal
    * @return Object Object Erro
*/
function consultaLoteOriginal(&$inCodLoteOriginal, &$nuLotesValidacao, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoLote) {
        $stFiltro .= " cod_lote = ".$this->inCodigoLote." and validado = FALSE";
    }
    $stFiltro = "\r\n\t WHERE ".$stFiltro;
    $obErro = $this->obTCIMLoteParcelado->recuperaTodos( $rsLoteParcelado, $stFiltro, "", $boTransacao );
    $this->setDataParcelamento( $rsLoteParcelado->getCampo('timestamp') );
    if (!$obErro->ocorreu() ) {
        $inCodParcelamento = $rsLoteParcelado->getCampo( "cod_parcelamento" );
        if ($inCodParcelamento) {
            $stFiltro = " cod_parcelamento =".$inCodParcelamento;
        }
        $stFiltro = "\r\n\t WHERE ".$stFiltro;
        $obErro = $this->obTCIMParcelamentoSolo->recuperaTodos( $rsParcelamento, $stFiltro, "",  $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $inCodLoteOriginal = $rsParcelamento->getCampo( "cod_lote" );
        }
    }
    $stFiltro = "";
    if ($inCodParcelamento) {
        $stFiltro = " WHERE cod_parcelamento = ".$inCodParcelamento." and validado = FALSE";
    }
    $obErro = $this->obTCIMLoteParcelado->recuperaTodos( $rsRecordSet, $stFiltro, "", $boTransacao);
    $nuLotesValidacao = $rsRecordSet->getNumLinhas();

    return $obErro;
}

/**
    * Verifica se a área informada na validação do lote não é superior a área original do lote desmembrado
    * @access   Public
    * @param    Boolean $boTransacao
    * @param    Boolean $boValidaArea
    * @return Object Object Erro
*/
function verificaAreaLoteValidado(&$flAreaRestante, $boTransacao = "")
{
    //recupera Area Original do Lote Desmembrado
    $stFiltro = "";
    if ( $this->getCodigoLoteOriginal() ) {
        $stFiltro = " cod_lote = ".$this->getCodigoLoteOriginal();
    }
    if ( $this->getDataParcelamento() ) {
        $stFiltro .= " AND timestamp < '".$this->getDataParcelamento()."'";
    }
    $stOrdem = " ORDER BY timestamp desc LIMIT 1";
    $this->obTCIMLote->recuperaAreaLoteOriginal( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    $flAreaLoteOriginal = $rsRecordSet->getCampo( 'area_real' );

    //recupera a Area dos outros Lotes validados
    $flAreaValidada = 0;
    $stFiltro = "";
    if ( $this->getCodigoLoteOriginal() ) {
        $stFiltro = " AND PS.cod_lote = ".$this->getCodigoLoteOriginal();
    }

    if ( $this->getCodigoParcelamento() ) {
        $stFiltro .= " AND LP.cod_parcelamento = ".$this->getCodigoParcelamento();
    }

    $stOrdem = "";
    $this->obTCIMLote->recuperaLotesParcelados( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    while ( !$rsRecordSet->eof() ) {
        $flAreaValidada += $rsRecordSet->getCampo( "area_real");
        $rsRecordSet->proximo();
    }

    $flAreaRestante = round(( $flAreaLoteOriginal - $flAreaValidada ) , 2 );
}
function listarImoveisLote(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodigoLote()) {
        $stFiltro .= " l.cod_lote = ".$this->getCodigoLote()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY il.inscricao_municipal";
    $obErro = $this->obTCIMLote->recuperaImoveisLote( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

} //fecha classe
?>
