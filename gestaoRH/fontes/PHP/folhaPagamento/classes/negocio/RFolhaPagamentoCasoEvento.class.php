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
* Classe de regra de negócio para RFolhaPagamentoCasoEvento
* Data de Criação: 26/08/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Eduardo Antunez

* @package URBEM
* @subpackage Regra de Negócio

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-11-20 15:48:41 -0200 (Ter, 20 Nov 2007) $

* Casos de uso: uc-04.05.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEvento.class.php"                    );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEventoCaso.class.php"                );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEventoCasoCargo.class.php"           );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEventoCasoEspecialidade.class.php"   );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEventoCasoSubDivisao.class.php"      );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoEventoConfiguracaoMedia.class.php"           );
include_once ( CAM_GRH_PES_NEGOCIO   ."RPessoalSubDivisao.class.php"                                   );
include_once ( CAM_GRH_PES_NEGOCIO   ."RPessoalRegime.class.php"                                       );

class RFolhaPagamentoCasoEvento
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
var $obTFolhaPagamentoConfiguracaoEvento;
/**
   * @access Private
   * @var Object
*/
var $obTFolhaPagamentoConfiguracaoEventoCaso;
/**
   * @access Private
   * @var Object
*/
var $obTFolhaPagamentoConfiguracaoEventoCasoCargo;
/**
   * @access Private
   * @var Object
*/
var $obTFolhaPagamentoTipoEventoConfiguracaoMedia;
/**
   * @access Private
   * @var Object
*/
var $obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade;
/**
   * @access Private
   * @var Object
*/
var $obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao;
/**
   * @access Private
   * @var Array
*/
var $arRPessoalSubDivisao;
/**
   * @access Private
   * @var Array
*/
var $arCodSubDivisao;
/**
   * @access Private
   * @var Array
*/
var $arCodCargoCodEspecialidade;
/**
   * @access Private
   * @var Object
*/
var $roUltimoSubDivisao;
/**
   * @access Private
   * @var Object
*/
var $roRFuncao;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoConfiguracaoEvento;
/**
   * @access Private
   * @var Integer
*/
var $inCodCaso;
/**
   * @access Private
   * @var Integer
*/
var $inCodTipoMedia;
/**
   * @access Private
   * @var String
*/
var $stDescricao;
/**
   * @access Private
   * @var Boolean
*/
var $boConsProporcaoAdiantamento;
/**
   * @access Private
   * @var Boolean
*/
var $boProporcaoAbono;
/**
   * @access Private
   * @var Array
*/
var $arRFolhaPagamentoEvento;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoEvento;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                                      = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTFolhaPagamentoConfiguracaoEvento($valor) { $this->obTFolhaPagamentoConfiguracaoEvento              = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTFolhaPagamentoConfiguracaoEventoCaso($valor) { $this->obTFolhaPagamentoConfiguracaoEventoCaso          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTFolhaPagamentoConfiguracaoEventoCasoCargo($valor) { $this->obTFolhaPagamentoConfiguracaoEventoCasoCargo     = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTFolhaPagamentoConfiguracaoEventoCasoEspecialidade($valor) { $this->obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTFolhaPagamentoConfiguracaoEventoCasoSubDivisao($valor) { $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao= $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTFolhaPagamentoTipoEventoConfiguracaoMedia($valor) { $this->obTFolhaPagamentoTipoEventoConfiguracaoMedia    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCodTipoMedia($valor) { $this->inCodTipoMedia                                  = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setArRPessoalSubDivisao($valor) { $this->arRPessoalSubDivisao                             = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setUltimoSubDivisao($valor) { $this->roUltimoSubDivisao                               = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRFuncao(&$valor) { $this->roRFuncao                                      = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRoRFolhaPagamentoConfiguracaoEvento(&$valor) { $this->roRFolhaPagamentoConfiguracaoEvento             = &$valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodCaso($valor) { $this->inCodCaso                                        = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao                                      = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setProporcaoAdiantamento($valor) { $this->boConsProporcaoAdiantamento                                      = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setProporcaoAbono($valor) { $this->boProporcaoAbono                                      = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setARRFolhaPagamentoEvento($valor) { $this->arFolhaPagamentoEvento   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORFolhaPagamentoEvento(&$valor) { $this->roRFolhaPagamentoEvento   = &$valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                                        }
/**
    * @access Public
    * @return Object
*/
function getTFolhaPagamentoConfiguracaoEvento() { return $this->obTFolhaPagamentoConfiguracaoEvento;                }
/**
    * @access Public
    * @return Object
*/
function getTFolhaPagamentoConfiguracaoEventoCaso() { return $this->obTFolhaPagamentoConfiguracaoEventoCaso;            }
/**
    * @access Public
    * @return Object
*/
function getTFolhaPagamentoConfiguracaoEventoCasoCargo() { return $this->obTFolhaPagamentoConfiguracaoEventoCasoCargo;       }
/**
    * @access Public
    * @return Object
*/
function getTFolhaPagamentoConfiguracaoEventoCasoEspecialidade() { return $this->obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade;}
/**
    * @access Public
    * @return Object
*/
function getTFolhaPagamentoConfiguracaoEventoCasoSubDivisao() { return $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao;  }
/**
    * @access Public
    * @return Object
*/
function getTFolhaPagamentoTipoEventoConfiguracaoMedia() { return $this->obTFolhaPagamentoTipoEventoConfiguracaoMedia;  }
/**
    * @access Public
    * @return Array
*/
function getRPessoalSubDivisao() { return $this->arRPessoalSubDivisao;                               }
/**
    * @access Public
    * @return Object
*/
function getUltimoSubDivisao() { return $this->roUltimoSubDivisao;                                 }
/**
    * @access Public
    * @return Object
*/
function getRFuncao() { return $this->roRFuncao;                                          }
/**
    * @access Public
    * @return Object
*/
function getRoRFolhaPagamentoConfiguracaoEvento() { return $this->roRFolhaPagamentoConfiguracaoEvento;                }
/**
    * @access Public
    * @return Integer
*/
function getCodCaso() { return $this->inCodCaso;                                          }
/**
    * @access Public
    * @return Integer
*/
function getCodTipoMedia() { return $this->inCodTipoMedia;                                     }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;                                        }
/**
    * @access Public
    * @return Boolean
*/
function getProporcaoAdiantamento() { return $this->boConsProporcaoAdiantamento;                                        }
/**
    * @access Public
    * @return Boolean
*/
function getProporcaoAbono() { return $this->boProporcaoAbono;                                        }
/**
    * @access Public
    * @return Object
*/
function getARRFolhaPagamentoEvento() { return $this->arFolhaPagamentoEvento;   }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoEvento() { return $this->roRFolhaPagamentoEvento;   }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoCasoEvento(&$roRFolhaPagamentoConfiguracaoEvento, &$roRFuncao)
{
    $this->setTransacao                                          ( new Transacao                                          );
    $this->setTFolhaPagamentoConfiguracaoEvento                  ( new TFolhaPagamentoConfiguracaoEvento                  );
    $this->setTFolhaPagamentoConfiguracaoEventoCaso              ( new TFolhaPagamentoConfiguracaoEventoCaso              );
    $this->setTFolhaPagamentoConfiguracaoEventoCasoCargo         ( new TFolhaPagamentoConfiguracaoEventoCasoCargo         );
    $this->setTFolhaPagamentoConfiguracaoEventoCasoEspecialidade ( new TFolhaPagamentoConfiguracaoEventoCasoEspecialidade );
    $this->setTFolhaPagamentoConfiguracaoEventoCasoSubDivisao    ( new TFolhaPagamentoConfiguracaoEventoCasoSubDivisao    );
    $this->setTFolhaPagamentoTipoEventoConfiguracaoMedia         ( new TFolhaPagamentoTipoEventoConfiguracaoMedia         );
    $this->setRFuncao                                            ( $roRFuncao                                             );
    $this->setRoRFolhaPagamentoConfiguracaoEvento                ( $roRFolhaPagamentoConfiguracaoEvento                   );
}

/**
    * Adiciona um objeto RPessoalSubDivisao ao array de referencia-objeto
    * @access Public
*/
function addSubDivisao()
{
    $this->arRPessoalSubDivisao[] = new RPessoalSubDivisao ( new RPessoalRegime );
    $this->roUltimoSubDivisao     = &$this->arRPessoalSubDivisao [ count($this->arRPessoalSubDivisao) - 1 ];
}

/**
    * Adiciona um objeto RFolhaPagamentoEvento ao array de referencia-objeto
    * @access Public
*/
function addRFolhaPagamentoEvento()
{
    $this->arRFolhaPagamentoEvento[] = new RFolhaPagamentoEvento();
    $this->roRFolhaPagamentoEvento= &$this->arRFolhaPagamentoEvento[ count($this->arRFolhaPagamentoEvento) -1 ];
    $this->roRFolhaPagamentoEvento->setRORFolhaPagamentoCasoEvento( $this );
}

/**
     * Inclui um CasoEvento
     * @access Public
*/
function incluirCasoEvento($boTransacao="")
{
    //Incluindo ConfiguracaoEventoCaso
    $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado ( "cod_evento"       , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento() );
    $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado ( "cod_configuracao" , $this->roRFolhaPagamentoConfiguracaoEvento->getCodConfiguracao()                    );
    $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCaso->proximoCod ( $inCodCaso, $boTransacao );
    $this->setCodCaso ( $inCodCaso );
    if ( !$obErro->ocorreu() ) {
        $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado ( "cod_caso"         , $this->getCodCaso()                                        );
        $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado ( "cod_funcao"       , $this->roRFuncao->getCodFuncao()                           );
        $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado ( "cod_modulo"       , $this->roRFuncao->obRBiblioteca->roRModulo->getCodModulo() );
        $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado ( "cod_biblioteca"   , $this->roRFuncao->obRBiblioteca->getCodigoBiblioteca()     );
        $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado ( "descricao"        , $this->getDescricao()                                      );
        $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado ( "proporcao_adiantamento" , $this->getProporcaoAdiantamento()                                      );
        $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado ( "proporcao_abono" , $this->getProporcaoAbono()                                      );
        $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado ( "timestamp"        , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getTimestamp());
        $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCaso->inclusao ( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCaso->recuperaNow3($now,$boTransacao);
    }
    if ( !$obErro->ocorreu() and $this->getCodTipoMedia() != "" ) {
        $this->obTFolhaPagamentoTipoEventoConfiguracaoMedia->obTFolhaPagamentoConfiguracaoEventoCaso = $this->obTFolhaPagamentoConfiguracaoEventoCaso;
        $this->obTFolhaPagamentoTipoEventoConfiguracaoMedia->setDado("cod_tipo" ,$this->getCodTipoMedia());
        $obErro = $this->obTFolhaPagamentoTipoEventoConfiguracaoMedia->inclusao( $boTransacao );
    }
    //Incluindo ConfiguracaoEventoCasoSubDivisao(s)
    //if ( !$obErro->ocorreu() and is_array($this->arRPessoalSubDivisao) ) {
    //    foreach ($this->arRPessoalSubDivisao as $obRPessoalSubDivisao) {
    if ( !$obErro->ocorreu() and is_array($this->arCodSubDivisao) ) {
        foreach ($this->arCodSubDivisao as $inCodSubDivisao) {
            $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao->setDado ( "cod_caso"         , $this->getCodCaso()                                                                 );
            $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao->setDado ( "cod_evento"       , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento() );
            $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao->setDado ( "cod_configuracao" , $this->roRFolhaPagamentoConfiguracaoEvento->getCodConfiguracao()                    );
            $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao->setDado ( "cod_sub_divisao"  , $inCodSubDivisao                                                                  );
            $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao->setDado ( "timestamp"        , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getTimestamp());
            $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao->inclusao ( $boTransacao );
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }

    //Incluindo Cargos e Especialidades
    //if ( !$obErro->ocorreu() and is_array($this->roUltimoSubDivisao->arRPessoalCargo) ) {
    //    foreach ($this->roUltimoSubDivisao->arRPessoalCargo as $obRPessoalCargo) {
    if ( !$obErro->ocorreu() and is_array($this->arCodCargoCodEspecialidade) ) {
        foreach ($this->arCodCargoCodEspecialidade as $stCodCargoCodEspecialidade) {
            $arCodCargoCodEspecialidade = explode("-",$stCodCargoCodEspecialidade);
            $inCodCargo                 = $arCodCargoCodEspecialidade[0];
            $inCodEspecialidade         = $arCodCargoCodEspecialidade[1];
            $this->obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado ( "cod_caso"         , $this->getCodCaso()                                                                 );
            $this->obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado ( "cod_evento"       , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento() );
            $this->obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado ( "cod_configuracao" , $this->roRFolhaPagamentoConfiguracaoEvento->getCodConfiguracao()                    );
            $this->obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado ( "cod_cargo"        , $inCodCargo                                                     );
            $this->obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado ( "timestamp"        , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getTimestamp());
            $this->obTFolhaPagamentoConfiguracaoEventoCasoCargo->recuperaPorChave( $rsConfiguracaoEventoCasoCargo, $boTransacao );

            if ( $rsConfiguracaoEventoCasoCargo->getNumLinhas() === -1 ) {
                $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCasoCargo->inclusao ( $boTransacao );
            }

            if ( $obErro->ocorreu() ) {
                break;
            }
            if ($inCodEspecialidade != 0) {
                $this->obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade->setDado ( "cod_caso"           , $this->getCodCaso()                                                                 );
                $this->obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade->setDado ( "cod_evento"         , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento() );
                $this->obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade->setDado ( "cod_configuracao"   , $this->roRFolhaPagamentoConfiguracaoEvento->getCodConfiguracao()                    );
                $this->obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade->setDado ( "cod_cargo"          , $inCodCargo                                                     );
                $this->obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade->setDado ( "cod_especialidade"  , $inCodEspecialidade                                     );
                $this->obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade->setDado ( "timestamp"          , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getTimestamp() );
                $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade->inclusao ( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }
    //Incluindo eventos base
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoBase.class.php");
        $obTFolhaPagamentoEventoBase =  new TFolhaPagamentoEventoBase;
        if ( count($this->arRFolhaPagamentoEvento) > 0 ) {
            for ($inIndex=0;$inIndex<count($this->arRFolhaPagamentoEvento);$inIndex++) {
                $obRFolhaPagamentoEvento = &$this->arRFolhaPagamentoEvento[$inIndex];
                $obTFolhaPagamentoEventoBase->setDado('cod_evento'              , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento());
                $obTFolhaPagamentoEventoBase->setDado('cod_evento_base'         , $obRFolhaPagamentoEvento->getCodEvento()                                           );
                $obTFolhaPagamentoEventoBase->setDado('cod_caso_base'           , $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->getCodCaso());
                $obTFolhaPagamentoEventoBase->setDado('cod_configuracao_base'   , $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->getCodConfiguracao());
                $obTFolhaPagamentoEventoBase->setDado('timestamp_base'          , $obRFolhaPagamentoEvento->getTimestamp());
                $obTFolhaPagamentoEventoBase->setDado('cod_caso'                , $this->getCodCaso());
                $obTFolhaPagamentoEventoBase->setDado('cod_configuracao'        , $this->roRFolhaPagamentoConfiguracaoEvento->getCodConfiguracao());
                $obTFolhaPagamentoEventoBase->setDado('timestamp'               , $now);
                $obErro = $obTFolhaPagamentoEventoBase->inclusao($boTransacao);
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        } else {
            $obErro = $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->consultarEvento($boTransacao);
            if ( !$obErro->ocorreu() and $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getNatureza() == 'B' ) {
                $obErro = $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->listarInformacoesEventoBase($rsEventoBase,$boTransacao);
                if ( !$obErro->ocorreu() ) {
                    $inCodEvento = $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento();
                    $inCodConfiguracao = $this->roRFolhaPagamentoConfiguracaoEvento->getCodConfiguracao();
                    $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->listarEventoBase($rsEventoBase,$inCodEvento,"",$inCodConfiguracao,$stTimestamp,$boTransacao);
                    $stComplementoChave = $obTFolhaPagamentoEventoBase->getComplementoChave();
                    $obTFolhaPagamentoEventoBase->setComplementoChave('cod_evento,cod_evento_base,cod_caso,cod_configuracao,timestamp,cod_configuracao_base');

                    while ( !$rsEventoBase->eof() ) {
                        $obTFolhaPagamentoEventoBase->setDado('cod_evento'              , $rsEventoBase->getCampo('cod_evento'));
                        $obTFolhaPagamentoEventoBase->setDado('cod_evento_base'         , $rsEventoBase->getCampo('cod_evento_base'));
                        $obTFolhaPagamentoEventoBase->setDado('cod_caso_base'           , $this->getCodCaso());
                        $obTFolhaPagamentoEventoBase->setDado('cod_configuracao_base'   , $rsEventoBase->getCampo('cod_configuracao_base'));
                        $obTFolhaPagamentoEventoBase->setDado('cod_caso'                , $rsEventoBase->getCampo('cod_caso'));
                        $obTFolhaPagamentoEventoBase->setDado('cod_configuracao'        , $rsEventoBase->getCampo('cod_configuracao'));
                        $obTFolhaPagamentoEventoBase->setDado('timestamp'               , $rsEventoBase->getCampo('timestamp'));
                        $obTFolhaPagamentoEventoBase->setDado('timestamp_base'          , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getTimestamp());
                        $obErro = $obTFolhaPagamentoEventoBase->alteracao($boTransacao);
                        $rsEventoBase->proximo();
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                    $obTFolhaPagamentoEventoBase->setComplementoChave($stComplementoChave);
                }
            }
        }
    }

    return $obErro;
}

/**
     * Exclui um CasoEvento
     * @access Public
*/
function excluirCasoEvento($boTransacao="")
{
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoBase.class.php"  );
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoEventoConfiguracaoMedia.class.php"  );
    $obTFolhaPagamentoEventoBase = new TFolhaPagamentoEventoBase;
    $obTFolhaPagamentoTipoEventoConfiguracaoMedia = new TFolhaPagamentoTipoEventoConfiguracaoMedia();

    //Especialidade
    $this->obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade->setDado( 'cod_evento' , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento() );
    $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade->exclusao($boTransacao);

    //Cargo
    if ( !$obErro->ocorreu() ) {
        $this->obTFolhaPagamentoConfiguracaoEventoCasoCargo->setDado( 'cod_evento' , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento() );
        $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCasoCargo->exclusao($boTransacao);
    }

    //SubDivisao
    if ( !$obErro->ocorreu() ) {
        $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao->setDado( 'cod_evento' , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento() );
        $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao->exclusao($boTransacao);
    }

    //Evento Base
    if ( !$obErro->ocorreu() ) {
        $obTFolhaPagamentoEventoBase->setDado('cod_evento', $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento());
        $obErro = $obTFolhaPagamentoEventoBase->exclusao($boTransacao);
    }

    //Evento Configuração Media
    if ( !$obErro->ocorreu() ) {
        $obTFolhaPagamentoTipoEventoConfiguracaoMedia->setDado('cod_evento', $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento());
        $obErro = $obTFolhaPagamentoTipoEventoConfiguracaoMedia->exclusao($boTransacao);
    }

    //Caso
    if ( !$obErro->ocorreu() ) {
        $this->obTFolhaPagamentoConfiguracaoEventoCaso->setCampoCod('');
        $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado( 'cod_evento' , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento() );
        $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCaso->exclusao($boTransacao);
    }

    return $obErro;
}

/**
     * Consulta um CasoEvento
     * @access Public
*/
function consultarCasoEvento($boTransacao="")
{
    //Consulta descricao e funcao do caso
    $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado( 'cod_caso'         , $this->getCodCaso()                                                                 );
    $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado( 'cod_evento'       , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento() );
    $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado( 'timestamp'        , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getTimestamp() );
    $this->obTFolhaPagamentoConfiguracaoEventoCaso->setDado( 'cod_configuracao' , $this->roRFolhaPagamentoConfiguracaoEvento->getCodConfiguracao()                    );
    $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCaso->recuperaPorChave( $rsEventoCaso ,$boTransacao);

    //Adiciona descricao e funcao retornados
    if ( !$obErro->ocorreu() and $rsEventoCaso->getNumLinhas() > 0 ) {
        $this->setDescricao( $rsEventoCaso->getCampo('descricao') );
        $this->setProporcaoAdiantamento( $rsEventoCaso->getCampo('proporcao_adiantamento') );
        $this->setProporcaoAbono( $rsEventoCaso->getCampo('proporcao_abono') );
        $this->roRFuncao->setCodFuncao( $rsEventoCaso->getCampo('cod_funcao') );
        $this->roRFuncao->setCodFuncao( $rsEventoCaso->getCampo('cod_funcao') );
        $this->roRFuncao->obRBiblioteca->setCodigoBiblioteca( $rsEventoCaso->getCampo('cod_biblioteca') );
        $this->roRFuncao->obRBiblioteca->roRModulo->setCodModulo( $rsEventoCaso->getCampo('cod_modulo') );
        $this->roRFuncao->consultar();
    }

    if ( !$obErro->ocorreu() ) {
        //Consulta subdivisoes do caso
        $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao->setDado( 'cod_caso'         , $this->getCodCaso()                                                                 );
        $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao->setDado( 'cod_evento'       , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento() );
        $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao->setDado( 'timestamp'        , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getTimestamp() );
        $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao->setDado( 'cod_configuracao' , $this->roRFolhaPagamentoConfiguracaoEvento->getCodConfiguracao()                    );
        $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCasoSubDivisao->recuperaPorChave( $rsSubDivisao,$boTransacao );
        $this->setArRPessoalSubDivisao ( array() );
        if ( !$obErro->ocorreu() ) {
            //Adiciona subdivisoes retornadas
            while ( !$rsSubDivisao->eof() ) {
                $this->addSubDivisao();
                $this->roUltimoSubDivisao->setCodSubDivisao( $rsSubDivisao->getCampo('cod_sub_divisao') );
                $this->roUltimoSubDivisao->listarSubDivisao( $rsTemp , "" ,$boTransacao);
                $this->roUltimoSubDivisao->roPessoalRegime->setCodRegime($rsTemp->getCampo('cod_regime'));
                $this->roUltimoSubDivisao->roPessoalRegime->setDescricao($rsTemp->getCampo('nom_regime'));
                $this->roUltimoSubDivisao->setDescricao( $rsTemp->getCampo('nom_sub_divisao') );
                $rsSubDivisao->proximo();
            }
        }
    }
    if (!$obErro->ocorreu()) {
        $this->obTFolhaPagamentoTipoEventoConfiguracaoMedia->setDado("cod_caso"     ,$this->getCodCaso());
        $this->obTFolhaPagamentoTipoEventoConfiguracaoMedia->setDado( 'cod_evento'       , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento() );
        $this->obTFolhaPagamentoTipoEventoConfiguracaoMedia->setDado( 'timestamp'        , $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getTimestamp() );
        $this->obTFolhaPagamentoTipoEventoConfiguracaoMedia->setDado( 'cod_configuracao' , $this->roRFolhaPagamentoConfiguracaoEvento->getCodConfiguracao()                    );
        $obErro = $this->obTFolhaPagamentoTipoEventoConfiguracaoMedia->recuperaPorChave($rsTipoEventoConfiguracao);
        $this->setCodTipoMedia($rsTipoEventoConfiguracao->getCampo("cod_tipo"));
    }

    return $obErro;
}

function listar(&$rsRecordSet, $stFiltro="", $stOrder = "", $boTransacao = "")
{
    $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCaso->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

function listarCasoEvento(&$rsRecordSet,$boTransacao = "")
{
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento() ) {
        $stFiltro .= " AND configuracao_evento_caso.cod_evento = ".$this->roRFolhaPagamentoConfiguracaoEvento->roRFolhaPagamentoEvento->getCodEvento();
    }
    if ( $this->roRFolhaPagamentoConfiguracaoEvento->getCodConfiguracao() ) {
        $stFiltro .= " AND configuracao_evento_caso.cod_configuracao = ".$this->roRFolhaPagamentoConfiguracaoEvento->getCodConfiguracao();
    }
    $obErro = $this->listar($rsRecordSet, $stFiltro, $stOrder, $boTransacao);

    return $obErro;
}

function listarConfiguracaoEventoCaso(&$rsRecordSet,$inCodEvento="",$stTimestamp="",$inCodConfiguracao="",$boTransacao = "")
{
    $stFiltro .= ( $inCodConfiguracao ) ? " AND configuracao_evento_caso.cod_configuracao = ".$inCodConfiguracao : "";
    $stFiltro .= ( $inCodEvento )       ? " AND configuracao_evento_caso.cod_evento = ".$inCodEvento             : "";
    $stFiltro .= ( $stTimestamp )       ? " AND configuracao_evento_caso.timestamp = '".$stTimestamp."'"         : "";
    $obErro = $this->listar($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

    return $obErro;
}

function listarConfiguracaoEventoCasoCargo(&$rsRecordSet,$inCodEvento="",$stTimestamp="",$inCodConfiguracao="",$inCodCaso="",$boTransacao = "")
{
    $stFiltro .= ($inCodCaso)         ? " AND cod_caso = ". $inCodCaso                 : "" ;
    $stFiltro .= ($inCodEvento)       ? " AND cod_evento = ". $inCodEvento             : "" ;
    $stFiltro .= ($stTimestamp)       ? " AND timestamp = '". $stTimestamp."'"         : "" ;
    $stFiltro .= ($inCodConfiguracao) ? " AND cod_configuracao = ". $inCodConfiguracao : "" ;
    $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCasoCargo->recuperaTodos($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

    return $obErro;
}

function listarConfiguracaoEventoCasoEspecialidade(&$rsRecordSet,$inCodEvento,$stTimestamp,$inCodConfiguracao,$inCodCaso,$inCodCargo,$boTransacao = "")
{
    $stFiltro .= ($inCodCaso)         ? " AND cod_caso = ". $inCodCaso                 : "" ;
    $stFiltro .= ($inCodEvento)       ? " AND cod_evento = ". $inCodEvento             : "" ;
    $stFiltro .= ($stTimestamp)       ? " AND timestamp = '". $stTimestamp."'"         : "" ;
    $stFiltro .= ($inCodConfiguracao) ? " AND cod_configuracao = ". $inCodConfiguracao : "" ;
    $stFiltro .= ($inCodCargo)        ? " AND cod_cargo = ".$inCodCargo                : "" ;
    $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    $obErro = $this->obTFolhaPagamentoConfiguracaoEventoCasoEspecialidade->recuperaTodos($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}
}//end class
?>
