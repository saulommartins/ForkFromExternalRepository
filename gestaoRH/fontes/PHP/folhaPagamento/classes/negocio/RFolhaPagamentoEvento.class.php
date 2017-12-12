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
* Classe de regra de negócio para RFolhaPagamentoEvento
* Data de Criação: 26/08/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Eduardo Antunez

* @package URBEM
* @subpackage Regra de Negócio

$Revision: 32866 $
$Name$
$Author: souzadl $
$Date: 2008-03-17 10:40:17 -0300 (Seg, 17 Mar 2008) $

$Id: RFolhaPagamentoEvento.class.php 60472 2014-10-23 16:25:44Z michel $

* Casos de uso: uc-04.05.06
                uc-04.05.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php"                                );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoEvento.class.php"                          );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoAtributoEventoValor.class.php"                   );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoCadastroAtributo.class.php"                      );
include_once ( CAM_GRH_FOL_NEGOCIO   ."RFolhaPagamentoConfiguracaoEvento.class.php"                    );
include_once ( CAM_GA_ADM_NEGOCIO    ."RCadastroDinamico.class.php"                                    );
include_once ( CAM_GRH_FOL_NEGOCIO   ."RFolhaPagamentoSequencia.class.php"                             );

class RFolhaPagamentoEvento
{
/**
   * @access Private
   * @var Object
*/
var $obTransacao;
/**
   * @access Private
   * @var Object
*/
var $obTFolhaPagamentoEvento;
/**
   * @access Private
   * @var Object
*/
var $obTFolhaPagamentoEventoEvento;
/**
   * @access Private
   * @var Array
*/
var $arRFolhaPagamentoConfiguracaoEvento;
/**
   * @access Private
   * @var Object
*/
var $roUltimoConfiguracaoEvento;
/**
   * @access Private
   * @var Object
*/
var $obRFolhaPagamentoSequencia;
/**
   * @access Private
   * @var Object
*/
var $obRCadastroDinamico;
/**
   * @access Private
   * @var Integer
*/
var $inCodEvento;
/**
   * @access Private
   * @var String
*/
var $stTimestamp;
/**
   * @access Private
   * @var String
*/
var $stCodigo;
/**
   * @access Private
   * @var Array
*/
var $arCodigos;
/**
   * @access Private
   * @var String
*/
var $stDescricao;
/**
   * @access Private
   * @var String
*/
var $stSigla;
/**
   * @access Private
   * @var String
*/
var $stObservacao;
/**
   * @access Private
   * @var Numeric
*/
var $nuValor;
/**
   * @access Private
   * @var Numeric
*/
var $nuUnidadeQuantitativa;
/**
   * @access Private
   * @var String
*/
var $stNatureza;
/**
   * @access Private
   * @var String
*/
var $stCodVerbaRescisoriaMTE;
/**
   * @access Private
   * @var Array
*/
var $arNaturezas;
/**
   * @access Private
   * @var Boolean
*/
var $boLimiteCalculo;
/**
   * @access Private
   * @var Boolean
*/
var $boApresentaParcela;
/**
   * @access Private
   * @var String
*/
var $boApresentaContraCheque;
/**
   * @access Private
   * @var String
*/
var $stTipo;
/**
   * @access Private
   * @var Array
*/
var $arTipo;
/**
   * @access Private
   * @var String
*/
var $stFixado;
/**
   * @access Private
   * @var Array
*/
var $arFixado;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoCasoEvento;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoPrevidencia;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoIRRF;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoEventoIRRF;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoFGTS;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoFGTSEvento;
/**
   * @access Private
   * @var Integer
*/
var $inCodTipo;
/**
   * @access Private
   * @var String
*/
var $stOrdenacao;
/**
   * @access Private
   * @var Boolean
*/
var $boEventoSistema;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                              = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTFolhaPagamentoEvento($valor) { $this->obTFolhaPagamentoEvento                  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTFolhaPagamentoEventoEvento($valor) { $this->obTFolhaPagamentoEventoEvento            = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setArRFolhaPagamentoConfiguracaoEvento($valor) { $this->arFolhaPagamentoConfiguracaoEvento       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRoUltimoConfiguracaoEvento($valor) { $this->roUltimoConfiguracaoEvento               = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRFolhaPagamentoSequencia($valor) { $this->obRFolhaPagamentoSequencia               = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRCadastroDinamico($valor) { $this->obRCadastroDinamico                      = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodEvento($valor) { $this->inCodEvento                              = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTimestamp($valor) { $this->stTimestamp                              = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodigo($valor) { $this->stCodigo                                 = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setCodigos($valor) { $this->arCodigos[]                              = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao                              = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setSigla($valor) { $this->stSigla                                  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setObservacao($valor) { $this->stObservacao                             = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setValor($valor) { $this->nuValor                                  = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setUnidadeQuantitativa($valor) { $this->nuUnidadeQuantitativa                    = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNatureza($valor) { $this->stNatureza                               = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodVerbaRescisoriaMTE($valor) { $this->stCodVerbaRescisoriaMTE       = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setNaturezas($valor) { $this->arNaturezas[]                            = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setLimiteCalculo($valor) { $this->boLimiteCalculo                          = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setApresentaParcela($valor) { $this->boApresentaParcela                       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setApresentaContraCheque($valor) { $this->boApresentaContraCheque                  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTipo($valor) { $this->stTipo                                   = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setTipos($valor) { $this->arTipos[]                                = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setFixado($valor) { $this->stFixado                                 = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setFixados($valor) { $this->arFixados[]                              = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setOrdenacao($valor) { $this->stOrdenacao                              = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoCasoEvento(&$valor) { $this->roRFolhaPagamentoCasoEvento              = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoPrevidencia(&$valor) { $this->roRFolhaPagamentoPrevidencia              = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoIRRF(&$valor) { $this->roRFolhaPagamentoIRRF                     = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoEventoIRRF(&$valor) { $this->roRFolhaPagamentoEventoIRRF               = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoFGTS(&$valor) { $this->roRFolhaPagamentoFGTS                     = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoFGTSEvento(&$valor) { $this->roRFolhaPagamentoFGTSEvento               = &$valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTipo($valor) { $this->inCodTipo                                 = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setEventoSistema($valor) { $this->boEventoSistema                           = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                              }
/**
    * @access Public
    * @return Object
*/
function getTFolhaPagamentoEvento() { return $this->obTFolhaPagamentoEvento;                  }
/**
    * @access Public
    * @return Object
*/
function getTFolhaPagamentoEventoEvento() { return $this->obTFolhaPagamentoEventoEvento;            }
/**
    * @access Public
    * @return Object
*/
function getArFolhaPagamentoConfiguracaoEvento() { return $this->arFolhaPagamentoConfiguracaoEvento;       }
/**
    * @access Public
    * @return Object
*/
function getRoUltimoCoonfiguracaoEvento() { return $this->roUltimoConfiguracaoEvento;               }
/**
    * @access Public
    * @return Object
*/
function getRCadastroDinamico() { return $this->obRCadastroDinamico;                      }
/**
    * @access Public
    * @return Integer
*/
function getCodEvento() { return $this->inCodEvento;                              }
/**
    * @access Public
    * @return String
*/
function getTimestamp() { return $this->stTimestamp;                              }
/**
    * @access Public
    * @return String
*/
function getCodigo() { return $this->stCodigo;                                 }
/**
    * @access Public
    * @return Array
*/
function getCodigos($inIndex) { return $this->arCodigos[$inIndex-1];            }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;                              }
/**
    * @access Public
    * @return String
*/
function getSigla() { return $this->stSigla;                                  }
/**
    * @access Public
    * @return String
*/
function getObservacao() { return $this->stObservacao;                             }
/**
    * @access Public
    * @return Numeric
*/
function getValor() { return $this->nuValor;                                  }
/**
    * @access Public
    * @return Numeric
*/
function getApresentaContraCheque() { return $this->boApresentaContraCheque;                  }
/**
    * @access Public
    * @return Numeric
*/
function getUnidadeQuantitativa() { return $this->nuUnidadeQuantitativa;                    }
/**
    * @access Public
    * @return String
*/
function getNatureza() { return $this->stNatureza;                               }
/**
    * @access Public
    * @return String
*/
function getCodVerbaRescisoriaMTE() { return $this->stCodVerbaRescisoriaMTE;       }
/**
    * @access Public
    * @return Array
*/
function getNaturezas() { return $this->arNaturezas;                             }
/**
    * @access Public
    * @return Boolean
*/

var $stEventoAutomaticoSistema;
function setEventoAutomaticoSistema($stValor) { $this->stEventoAutomaticoSistema = $stValor;}
function getEventoAutomaticoSistema() { return $this->stEventoAutomaticoSistema ; }

function getLimiteCalculo() { return $this->boLimiteCalculo;                          }
/**
    * @access Public
    * @return Boolean
*/
function getApresentaParcela() { return $this->boApresentaParcela;                       }
/**
    * @access Public
    * @return String
*/
function getTipo() { return $this->stTipo;                                   }
/**
    * @access Public
    * @return Array
*/
function getTipos() { return $this->arTipos;                                  }
/**
    * @access Public
    * @return String
*/
function getFixado() { return $this->stFixado;                                 }
/**
    * @access Public
    * @return Array
*/
function getFixados() { return $this->arFixados;                                }
/**
    * @access Public
    * @return String
*/
function getOrdenacao() { return $this->stOrdenacao;                             }
/**
    * @access Public
    * @return Integer
*/
function getCodTipo() { return $this->inCodTipo;                                  }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoPrevidencia() { return $this->roRFolhaPagamentoPrevidencia;               }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoIRRF() { return $this->roRFolhaPagamentoIRRF;                      }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoEventoIRRF() { return $this->roRFolhaPagamentoEventoIRRF;                }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoFGTS() { return $this->roRFolhaPagamentoFGTS;                      }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoFGTSEvento() { return $this->roRFolhaPagamentoFGTSEvento;                }
/**
    * @access Public
    * @return Boolean
*/
function getEventoSistema() { return $this->boEventoSistema;                            }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoEvento()
{
    $this->setTransacao                                ( new Transacao                          );
    $this->setTFolhaPagamentoEvento                    ( new TFolhaPagamentoEvento              );
    $this->setTFolhaPagamentoEventoEvento              ( new TFolhaPagamentoEventoEvento        );
    $this->setArRFolhaPagamentoConfiguracaoEvento      ( array()                                );
    $this->setRCadastroDinamico                        ( new RCadastroDinamico                  );
    $this->obRCadastroDinamico->setCodCadastro         ( 4                                      );
    $this->obRCadastroDinamico->obRModulo->setCodModulo( 27                                     );
    $this->setRFolhaPagamentoSequencia                 ( new RFolhaPagamentoSequencia           );
}

/**
    * Adiciona um objeto RFolhaPagamentoConfiguracaoEvento ao array de referencia-objeto
    * @access Public
*/
function addConfiguracaoEvento()
{
    $this->arRFolhaPagamentoConfiguracaoEvento[] = new RFolhaPagamentoConfiguracaoEvento ( $this );
    $this->roUltimoConfiguracaoEvento = &$this->arRFolhaPagamentoConfiguracaoEvento[ count($this->arRFolhaPagamentoConfiguracaoEvento) - 1 ];
}

/**
    * Salva um evento
    * @access Private
*/

function salvarEvento($acao, $boTransacao="")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() && $acao == 'incluir' ) {
        $this->obTFolhaPagamentoEvento->setDado ( "cod_evento"    , $this->getCodEvento()               );
        $this->obTFolhaPagamentoEvento->setDado ( "codigo"        , $this->getCodigo()                  );
        $this->obTFolhaPagamentoEvento->setDado ( "descricao"     , $this->getDescricao()               );
        $this->obTFolhaPagamentoEvento->setDado ( "sigla"         , $this->getSigla()                   );
        $this->obTFolhaPagamentoEvento->setDado ( "natureza"      , $this->getNatureza()                );
        
        $this->obTFolhaPagamentoEvento->setDado ( "cod_verba"     , $this->getCodVerbaRescisoriaMTE()   );
        
        $this->obTFolhaPagamentoEvento->setDado ( "tipo"          , $this->getTipo()                    );
        $this->obTFolhaPagamentoEvento->setDado ( "fixado"        , $this->getFixado()                  );
        $this->obTFolhaPagamentoEvento->setDado ( "evento_sistema", $this->getEventoAutomaticoSistema() );
        if ($this->getApresentaContraCheque() == 't') {
            $this->obTFolhaPagamentoEvento->setDado ( "apresentar_contracheque" , true   );
        } else {
            $this->obTFolhaPagamentoEvento->setDado ( "apresentar_contracheque" , false   );
        }
        if ( $this->getLimiteCalculo() == 'S' )
            $this->obTFolhaPagamentoEvento->setDado ( "limite_calculo"   , true );
        if ( $this->getApresentaParcela() == 'S' )
            $this->obTFolhaPagamentoEvento->setDado ( "apresenta_parcela", true );
        $obErro = $this->obTFolhaPagamentoEvento->inclusao ( $boTransacao );
    } else {
        $this->obTFolhaPagamentoEvento->setDado ( "cod_evento"           , $this->getCodEvento()               );
        
        $this->obTFolhaPagamentoEvento->consultar($boTransacao);
        $this->obTFolhaPagamentoEvento->setDado("sigla", $this->getSigla());
        $this->obTFolhaPagamentoEvento->setDado("cod_verba", $this->getCodVerbaRescisoriaMTE() );
        
        if ($this->getApresentaContraCheque() == 't') {
            $this->obTFolhaPagamentoEvento->setDado ( "apresentar_contracheque" , true   );
        } else {
            $this->obTFolhaPagamentoEvento->setDado ( "apresentar_contracheque" , false   );
        } 
        
        $obErro = $this->obTFolhaPagamentoEvento->alteracao($boTransacao);
    }

    if ( !$obErro->ocorreu() ) {
        $this->obTFolhaPagamentoEventoEvento->setDado     ( "cod_evento"           , $this->getCodEvento()           );
        $this->obTFolhaPagamentoEventoEvento->setDado     ( "observacao"           , $this->getObservacao()          );
        if ($this->getValor())
            $this->obTFolhaPagamentoEventoEvento->setDado ( "valor_quantidade"     , $this->getValor()               );
        if ($this->getUnidadeQuantitativa())
            $this->obTFolhaPagamentoEventoEvento->setDado ( "unidade_quantitativa" , $this->getUnidadeQuantitativa() );
        $obErro = $this->obTFolhaPagamentoEventoEvento->inclusao ( $boTransacao );
    }

    if (!$obErro->ocorreu()) {
        $stFiltro = 'WHERE cod_evento = '.$this->getCodEvento();
        $obErro = $this->obTFolhaPagamentoEventoEvento->recuperaUltimoTimeStamp($stTimestamp, $stFiltro, '', $boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->setTimestamp($stTimestamp);
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSequenciaCalculoEvento.class.php");
        $obTFolhaPagamentoSequenciaCalculoEvento = new TFolhaPagamentoSequenciaCalculoEvento;
        $obTFolhaPagamentoSequenciaCalculoEvento->setDado('cod_evento' , $this->getCodEvento());
        $obTFolhaPagamentoSequenciaCalculoEvento->exclusao($boTransacao);
        $obTFolhaPagamentoSequenciaCalculoEvento->setDado("cod_evento"   , $this->getCodEvento()                                );
        $obTFolhaPagamentoSequenciaCalculoEvento->setDado("cod_sequencia", $this->obRFolhaPagamentoSequencia->getCodSequencia() );
        $obErro = $obTFolhaPagamentoSequenciaCalculoEvento->inclusao( $boTransacao );
    }

    //Incluindo atributos dinamicos
    if ( !$obErro->ocorreu() ) {
        $arChaveAtributoEvento = array( "cod_evento" => $this->getCodEvento() );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoEvento );
        $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
    }

    //Incluindo ConfiguracaoEvento(s)
    if ( !$obErro->ocorreu () ) {
        foreach ($this->arRFolhaPagamentoConfiguracaoEvento as $obRConfiguracaoEvento) {
            $obErro = $obRConfiguracaoEvento->incluirConfiguracaoEvento( $boTransacao );
             if ( $obErro->ocorreu() )
                 break;
        }
    }
    
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoEvento );

    return $obErro;
}

/**
    * Inclui um evento
    * @access Public
*/
function incluirEvento($boTransacao="")
{
    //Calcula proximo id
    $obErro = $this->obTFolhaPagamentoEvento->proximoCod( $inCodEvento, $boTransacao);

    if ( !$obErro->ocorreu() ) {
        $this->setCodEvento ( $inCodEvento );
        $obErro = $this->salvarEvento('incluir', $boTransacao);
    }

    return $obErro;
}

/**
    * Altera um evento
    * @access Public
*/

function alterarEvento($boTransacao="")
{
    $obErro = $this->salvarEvento('', $boTransacao);

    return $obErro;
}

/**
    * Exclui um evento
    * @access Public
*/
function excluirEvento()
{
    $stFiltro  = " AND evento.cod_evento IN (".$this->getCodEvento().") \n";
    $stFiltro .= " LIMIT 1                                              \n";
    $this->obTFolhaPagamentoEvento->recuperaRelatorioCustomizavelEventos( $rsRecordset, $stFiltro, '', $boTransacao );

    if($rsRecordset->getNumLinhas()>0){
        $obErro = new Erro();
        $obErro->setDescricao("Evento está sendo utilizado");
    }
    else{
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    
        if ( !$obErro->ocorreu() ) {
            $this->addConfiguracaoEvento();
            $obErro = $this->roUltimoConfiguracaoEvento->excluirConfiguracaoEvento($boTransacao);
        }
    
        //Sequencia
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSequenciaCalculoEvento.class.php");
            $obTFolhaPagamentoSequenciaCalculoEvento = new TFolhaPagamentoSequenciaCalculoEvento;
            $obTFolhaPagamentoSequenciaCalculoEvento->setDado('cod_evento' , $this->getCodEvento());
            $obTFolhaPagamentoSequenciaCalculoEvento->exclusao($boTransacao);
        }
    
        //Atributos dinamicos
        if ( !$obErro->ocorreu() ) {
            $arChaveAtributoEvento = array( "cod_evento" => $this->getCodEvento() );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoEvento );
            $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
        }
    
        //EventoEvento
        if ( !$obErro->ocorreu() ) {
            $this->obTFolhaPagamentoEventoEvento->setDado( 'cod_evento' , $this->getCodEvento() );
            $obErro = $this->obTFolhaPagamentoEventoEvento->exclusao($boTransacao);
        }
    
        //Evento
        if ( !$obErro->ocorreu() ) {
            $this->obTFolhaPagamentoEvento->setDado( 'cod_evento' , $this->getCodEvento() );
            $obErro = $this->obTFolhaPagamentoEvento->exclusao($boTransacao);
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoEvento );
    }

    return $obErro;
}

/**
    * Lista eventos
    * @access Private
*/
function listar(&$rsRecordSet, $stFiltro="", $stOrder = "", $boTransacao = "")
{
    $obErro = $this->obTFolhaPagamentoEvento->recuperaEventos($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Lista eventos filtrando código e descrição
    * @access Public
*/
function listarEvento(&$rsLista, $boTransacao="")
{
    $stFiltro = "";
    $stOrder  = " Order by codigo";
    if ( strlen($this->stCodigo) > 0 )
        $stFiltro .= " AND codigo::integer = '".$this->getCodigo()."'::integer ";
    if ( $this->getCodigos(1) ) {
        $stFiltro .= " AND codigo::integer >= '".$this->getCodigos(1)."'::integer";
    }
    if ( $this->getCodigos(2) ) {
        $stFiltro .= " AND codigo::integer <= '".$this->getCodigos(2)."'::integer";
    }
    if ( strlen($this->getDescricao()) > 0 )
        $stFiltro .= " AND LOWER(descricao) LIKE LOWER('".strtolower(trim($this->getDescricao()))."%') ";
    if ( $this->getNatureza() ) {
        $stFiltro .= " AND natureza = '".$this->getNatureza()."'";
    }
    if ( $this->getNaturezas() ) {
        foreach ($this->getNaturezas() as $stNatureza) {
            $stNaturezas .= "'".$stNatureza."',";
        }
        $stNaturezas = substr($stNaturezas,0,strlen($stNaturezas)-1);
        $stFiltro .= " AND natureza IN (".$stNaturezas.")";
    }
    if ( $this->getTipos() ) {
        foreach ($this->getTipos() as $stTipo) {
            $stTipos .= "'".$stTipo."',";
        }
        $stTipos = substr($stTipos,0,strlen($stTipos)-1);
        $stFiltro .= " AND tipo IN (".$stTipos.")";
    }
    if ( $this->getFixados() ) {
        foreach ($this->getFixados() as $stFixado) {
            $stFixados .= "'".$stFixado."',";
        }
        $stFixados = substr($stFixados,0,strlen($stFixados)-1);
        $stFiltro .= " AND fixado IN (".$stFixados.")";
    }
    if ( $this->getCodEvento() ) {
        $stFiltro .= " AND FPE.cod_evento = ".$this->getCodEvento();
    }
    if ( $this->getTipo() ) {
        $stFiltro .= " AND FPE.tipo = '".$this->getTipo()."'";
    }
    if ( $this->obRFolhaPagamentoSequencia->getCodSequencia() ) {
        $stFiltro .= " AND FSCE.cod_sequencia = ". $this->obRFolhaPagamentoSequencia->getCodSequencia() ;
    }
    if ( $this->getTimestamp() ) {
        $stFiltro .= " AND FPEE.timestamp <= '".$this->getTimestamp()."'";
    }
    if ( $this->getEventoSistema() != "" ) {
        $stFiltro .= " AND evento_sistema = ".$this->getEventoSistema();
    }

    //Ordenação
    if ( $this->getOrdenacao() != "" ) {
        $stOrder = " ORDER BY ". $this->getOrdenacao();
    }
    $obErro = $this->listar( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaLista na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarTiposEventoSalarioFamilia(&$rsRecordSet, $boTransacao = "")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoEventoSalarioFamilia.class.php");
    $obTTipoEventoSalarioFamilia= new TFolhaPagamentoTipoEventoSalarioFamilia;
    $obErro = $obTTipoEventoSalarioFamilia->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaLista na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarTiposEventoPrevidencia(&$rsRecordSet, $boTransacao = "")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoEventoPrevidencia.class.php");
    $obTFolhaPagamentoTiposEventoPrevidencia = new TFolhaPagamentoTipoEventoPrevidencia;
    $obErro = $obTFolhaPagamentoTiposEventoPrevidencia->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista eventos de previdencia vinculados a um contrato
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Integer $inCodContrato
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarEventosDePrevidenciaPorContrato(&$rsRecordSet, $inCodContrato, $boTransacao = "")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaEvento.class.php");
    $obTFolhaPagamentoPrevidenciaEvento = new TFolhaPagamentoPrevidenciaEvento;
    $stFiltro = " AND contrato_servidor_previdencia.cod_contrato = ".$inCodContrato;
    $obErro = $obTFolhaPagamentoPrevidenciaEvento->recuperaEventosDePrevidenciaPorContrato( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista eventos de irrf vinculados a um contrato
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Integer $inCodContrato
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarEventosDeIRRFPorContrato(&$rsRecordSet, $inCodContrato, $boTransacao = "")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfEvento.class.php");
    $obTFolhaPagamentoTabelaIrrfEvento = new TFolhaPagamentoTabelaIrrfEvento;
    $stFiltro = " AND cod_contrato = ".$inCodContrato;
    $obErro = $obTFolhaPagamentoTabelaIrrfEvento->recuperaEventosDeIrrfPorContrato( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista eventos de irrf vinculados a um contrato
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Integer $inCodContrato
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarEventoDeIRRFPorContratoEvento(&$rsRecordSet, $inCodContrato,$inCodEvento,$inCodTipo, $boTransacao = "")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfEvento.class.php");
    $obTFolhaPagamentoTabelaIrrfEvento = new TFolhaPagamentoTabelaIrrfEvento;
    $stFiltro  = " AND cod_contrato = ".$inCodContrato;
    $stFiltro .= " AND evento.cod_evento = ".$inCodEvento;
    $stFiltro .= " AND cod_tipo = ".$inCodTipo;
    $obErro = $obTFolhaPagamentoTabelaIrrfEvento->recuperaEventosDeIrrfPorContrato( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function consultarCodigo($boTransacao="")
{
    $stFiltro = " and FPE.codigo = '" . $this->getCodigo()."'";
    $obErro = $this->obTFolhaPagamentoEvento->recuperaEventos( $rsEvento , $stFiltro , '' , $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setTimestamp           ( $rsEvento->getCampo('timestamp')            );
        $this->setCodigo              ( $rsEvento->getCampo('codigo')               );
        $this->setCodEvento           ( $rsEvento->getCampo('cod_evento')           );
        $this->setDescricao           ( $rsEvento->getCampo('descricao')            );
        $this->setObservacao          ( $rsEvento->getCampo('observacao')           );
        $this->setValor               ( $rsEvento->getCampo('valor_quantidade')     );
        $this->setUnidadeQuantitativa ( $rsEvento->getCampo('unidade_quantitativa') );
        $this->setNatureza            ( $rsEvento->getCampo('natureza')             );
        $this->setTipo                ( $rsEvento->getCampo('tipo')                 );
        $this->setFixado              ( $rsEvento->getCampo('fixado')               );
        $this->setLimiteCalculo       ( ($rsEvento->getCampo('limite_calculo') == 't') ? 'S' : 'N' );
        $this->setApresentaParcela    ( ($rsEvento->getCampo('apresenta_parcela') == 't') ? 'S' : 'N' );

        //Sequencia
        $this->obRFolhaPagamentoSequencia->setCodSequencia($rsEvento->getCampo('cod_sequencia'));

    }

    return $obErro;
}

/**
    * Consultea ua evento
    * @access Public
*/
function consultarEvento($boTransacao="")
{
    //Evento
    $this->obTFolhaPagamentoEvento->setDado( 'cod_evento' , $this->getCodEvento() );
    $stFiltro = " AND FPE.cod_evento = ".$this->getCodEvento()." ";
    $obErro = $this->obTFolhaPagamentoEvento->recuperaEventos( $rsEvento , $stFiltro , '' , $boTransacao );
    $rsEvento->addFormatacao('valor_quantidade'    ,'NUMERIC_BR');
    $rsEvento->addFormatacao('unidade_quantitativa','NUMERIC_BR');
    if ( !$obErro->ocorreu() ) {
        $this->setTimestamp           ( $rsEvento->getCampo('timestamp')            );
        $this->setCodigo              ( $rsEvento->getCampo('codigo')               );
        $this->setCodEvento           ( $rsEvento->getCampo('cod_evento')           );
        $this->setDescricao           ( $rsEvento->getCampo('descricao')            );
        $this->setSigla               ( $rsEvento->getCampo('sigla')                );
        $this->setObservacao          ( $rsEvento->getCampo('observacao')           );
        $this->setValor               ( $rsEvento->getCampo('valor_quantidade')     );
        $this->setUnidadeQuantitativa ( $rsEvento->getCampo('unidade_quantitativa') );
        $this->setNatureza            ( $rsEvento->getCampo('natureza')             );
        $this->setTipo                ( $rsEvento->getCampo('tipo')                 );
        $this->setFixado              ( $rsEvento->getCampo('fixado')               );
        $this->setLimiteCalculo       ( ($rsEvento->getCampo('limite_calculo') == 't') ? 'S' : 'N' );
        $this->setApresentaParcela    ( ($rsEvento->getCampo('apresenta_parcela') == 't') ? 'S' : 'N' );
        $this->setEventoAutomaticoSistema( ($rsEvento->getCampo('evento_sistema') == 't') ? true : false  );
        $this->setCodVerbaRescisoriaMTE( $rsEvento->getCampo('cod_verba')  );

        //Sequencia
        $this->obRFolhaPagamentoSequencia->setCodSequencia($rsEvento->getCampo('cod_sequencia'));

    }

    return $obErro;
}

/**
    * Lista eventos base filtrando código e descrição
    * @access Public
*/
function listarEventosBase(&$rsLista, $boTransacao="")
{
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoBase.class.php"                   );
    $obTFolhaPagamentoEventoBase = new TFolhaPagamentoEventoBase;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodEvento() ) {
        $stFiltro .= " AND evento_base.cod_evento = ".$this->getCodEvento();
    }
    if ( is_object($this->roUltimoConfiguracaoEvento->roUltimoCasoEvento) ) {
        if ( $this->roUltimoConfiguracaoEvento->roUltimoCasoEvento->getCodCaso() ) {
            $stFiltro .= " AND evento_base.cod_caso = ".$this->roUltimoConfiguracaoEvento->roUltimoCasoEvento->getCodCaso();
        }
    }
    if ( is_object($this->roUltimoConfiguracaoEvento) ) {
        if ( $this->roUltimoConfiguracaoEvento->getCodConfiguracao() ) {
            $stFiltro .= " AND evento_base.cod_configuracao = ".$this->roUltimoConfiguracaoEvento->getCodConfiguracao();
        }
    }
    if ( $this->getTimestamp() ) {
        $stFiltro .= " AND evento_base.timestamp = '".$this->getTimestamp()."'";
    }
    $obErro = $obTFolhaPagamentoEventoBase->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Lista eventos base filtrando código e descrição
    * @access Public
*/
function listarEventoBase(&$rsRecordset, $inCodEvento="",$inCodCaso="",$inCodConfiguracao="",$stTimestamp="",$boTransacao="")
{
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoBase.class.php"                   );
    $obTFolhaPagamentoEventoBase = new TFolhaPagamentoEventoBase;
    $stFiltro .= ( $inCodEvento       != "" ) ? " AND evento_base.cod_evento_base = ".$inCodEvento : "";
    $stFiltro .= ( $inCodCaso         != "" ) ? " AND evento_base.cod_caso_base = ".$inCodCaso     : "";
    //$stFiltro .= ( $inCodConfiguracao != "" ) ? " AND evento_base.cod_configuracao_base = ".$inCodConfiguracao : "";
    $stFiltro .= ( $stTimestamp       != "" ) ? " AND evento_base.timestamp_base = '".$stTimestamp."'" : "";
    $obTFolhaPagamentoEventoBase->setDado("cod_configuracao_base",$inCodConfiguracao);
    $obErro = $obTFolhaPagamentoEventoBase->recuperaEventoBase( $rsRecordset, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Lista as informações do evento base
    * @access Public
*/
function listarInformacoesEventoBase(&$rsLista, $boTransacao="")
{
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoBase.class.php"                   );
    $obTFolhaPagamentoEventoBase = new TFolhaPagamentoEventoBase;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodEvento() ) {
        $obTFolhaPagamentoEventoBase->setDado('cod_evento_base',$this->getCodEvento());
    }
    if ( is_object($this->roUltimoConfiguracaoEvento) ) {
        if ( $this->roUltimoConfiguracaoEvento->getCodConfiguracao() ) {
            $obTFolhaPagamentoEventoBase->setDado('cod_configuracao_base',$this->roUltimoConfiguracaoEvento->getCodConfiguracao());
        }
    }

    $obErro = $obTFolhaPagamentoEventoBase->recuperaPorChave( $rsLista, $boTransacao );

    return $obErro;
}

/**
    * Lista eventos base filtrados de cod_caso
    * @access Public
*/
function listarEventosBasePorCaso(&$rsLista, $boTransacao="")
{
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoBase.class.php"                   );
    $obTFolhaPagamentoEventoBase = new TFolhaPagamentoEventoBase;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodEvento() ) {
        $stFiltro .= " AND evento_base.cod_evento = ".$this->getCodEvento();
    }
    if ( is_object($this->roUltimoConfiguracaoEvento->roUltimoCasoEvento) ) {
        if ( $this->roUltimoConfiguracaoEvento->roUltimoCasoEvento->getCodCaso() ) {
            $stFiltro .= " AND evento_caso.cod_caso = ".$this->roUltimoConfiguracaoEvento->roUltimoCasoEvento->getCodCaso();
        }
    }
    if ( is_object($this->roUltimoConfiguracaoEvento) ) {
        if ( $this->roUltimoConfiguracaoEvento->getCodConfiguracao() ) {
            $stFiltro .= " AND evento_caso.cod_configuracao = ".$this->roUltimoConfiguracaoEvento->getCodConfiguracao();
        }
    }
    if ( $this->getTimestamp() ) {
        $stFiltro .= " AND evento_caso.timestamp = '".$this->getTimestamp()."'";
    }
    $obErro = $obTFolhaPagamentoEventoBase->recuperaRelacionamentoPorCaso( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Lista eventos de eventos base
    * @access Public
*/
function listarEventosDeEventosBase(&$rsLista, $boTransacao="")
{
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoBase.class.php"                   );
    $obTFolhaPagamentoEventoBase = new TFolhaPagamentoEventoBase;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodEvento() ) {
        $stFiltro .= " AND  evento_base.cod_evento_base = ".$this->getCodEvento();
    }
    if ( is_object($this->roUltimoConfiguracaoEvento) ) {
        if ( $this->roUltimoConfiguracaoEvento->getCodConfiguracao() ) {
            $stFiltro .= " AND evento_base.cod_configuracao = ".$this->roUltimoConfiguracaoEvento->getCodConfiguracao();
        }
    }

    $obErro = $obTFolhaPagamentoEventoBase->recuperaRelacionamentoEventosDeEventosBase( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Lista eventos para o relatório customivável de eventos
    * @access Public
*/
function listarRelatorioCustomizavelEventos(&$rsRecordset, $arFiltros, $stOrdem, $boTransacao="")
{
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php"                   );
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
    $stOrdem = " ORDER BY ".$stOrdem;
    if ( isset($arFiltros['arEventos']) ) {
        foreach ($arFiltros['arEventos'] as $inCodEvento) {
            $stEventos .= $inCodEvento . ",";
        }
        $stEventos = substr($stEventos,0,strlen($stEventos)-1);
        $stFiltro .= " AND evento.cod_evento IN (".$stEventos.")";
    }
    if ($arFiltros['boAtivo'] and $arFiltros['boInativo']) {
        $stFiltro .= " AND (ativo = true or ativo = false)";
    } elseif ($arFiltros['boAtivo'] and !$arFiltros['boInativo']) {
         $stFiltro .= " AND ativo = true";
    } elseif (!$arFiltros['boAtivo'] and $arFiltros['boInativo']) {
         $stFiltro .= " AND ativo = false";
    }
    //if ($arFiltros['boPensionista']) {
    //    $stFiltro .= " AND ativo = true";
    //}
    if ( is_array($arFiltros['arRegistros']) ) {
        $stRegistros = "";
        foreach ($arFiltros['arRegistros'] as $inRegistros) {
            $stRegistros .= $inRegistros.",";
        }
        $stRegistros = substr($stRegistros,0,strlen($stRegistros)-1);
        $stFiltro .= " AND registro IN (".$stRegistros.")";
    }
    if ( isset($arFiltros['cod_periodo_movimentacao']) ) {
        $stFiltro .= " AND registro_evento_periodo.cod_periodo_movimentacao = ".$arFiltros['cod_periodo_movimentacao'];
    }
    if ( is_array($arFiltros['arEspecialidades']) ) {
        $stEspecialidades = "";
        foreach ($arFiltros['arEspecialidades'] as $inEspecialidade) {
            $stEspecialidades .= $inEspecialidade.",";
        }
        $stEspecialidades = substr($stEspecialidades,0,strlen($stEspecialidades)-1);
        $stFiltro .= " AND especialidade.cod_especialidade IN (".$stEspecialidades.")";
    }
    if ( is_array($arFiltros['arCargos']) ) {
        $stCargos = "";
        foreach ($arFiltros['arCargos'] as $inCargo) {
            $stCargos .= $inCargo.",";
        }
        $stCargos = substr($stCargos,0,strlen($stCargos)-1);
        $stFiltro .= " AND cargo.cod_cargo IN (".$stCargos.")";
    }
    if ( is_array($arFiltros['arFuncoes']) ) {
        $stCargos = "";
        foreach ($arFiltros['arFuncoes'] as $inCargo) {
            $stCargos .= $inCargo.",";
        }
        $stCargos = substr($stCargos,0,strlen($stCargos)-1);
        $stFiltro .= " AND funcao.cod_funcao IN (".$stCargos.")";
    }
    if ( is_array($arFiltros['arEspecialidadesFunc']) ) {
        $stEspecialidades = "";
        foreach ($arFiltros['arEspecialidadesFunc'] as $inEspecialidade) {
            $stEspecialidades .= $inEspecialidade.",";
        }
        $stEspecialidades = substr($stEspecialidades,0,strlen($stEspecialidades)-1);
        $stFiltro .= " AND especialidade_funcao.cod_especialidade_funcao IN (".$stEspecialidades.")";
    }
    if ( is_array($arFiltros['arPadrao']) ) {
        $stPadrao = "";
        foreach ($arFiltros['arPadrao'] as $inPadrao) {
            $stPadrao .= $inPadrao.",";
        }
        $stPadrao = substr($stPadrao,0,strlen($stPadrao)-1);
        $stFiltro .= " AND cod_padrao IN (".$stPadrao.")";
    }
    if ( is_array($arFiltros['arLotacao']) ) {
        $stLotacao = "";
        foreach ($arFiltros['arLotacao'] as $inLotacao) {
            $stLotacao .= "'".$inLotacao."',";
        }
        $stLotacao = substr($stLotacao,0,strlen($stLotacao)-1);
        $stFiltro .= " AND cod_orgao IN (".$stLotacao.")";
    }
    if ( is_array($arFiltros['arLocal']) ) {
        $stLocal = "";
        foreach ($arFiltros['arLocal'] as $inLocal) {
            $stLocal .= $inLocal.",";
        }
        $stLocal = substr($stLocal,0,strlen($stLocal)-1);
        $stFiltro .= " AND cod_local IN (".$stLocal.")";
    }
    $obErro = $obTFolhaPagamentoEvento->recuperaRelatorioCustomizavelEventos( $rsRecordset, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

}//end class
?>
