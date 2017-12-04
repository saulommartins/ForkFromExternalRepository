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
* Classe de regra de negócio para RFolhaPagamentoConfiguracaoEvento
* Data de Criacao: 26/08/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Eduardo Antunez

* @package URBEM
* @subpackage Regra de Negócio

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-07-24 16:42:34 -0300 (Ter, 24 Jul 2007) $

* Casos de uso: uc-04.05.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoConfiguracaoEvento.class.php"              );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEventoDespesa.class.php"             );
include_once ( CAM_GRH_FOL_NEGOCIO   ."RFolhaPagamentoCasoEvento.class.php"                            );
include_once ( CAM_GF_ORC_NEGOCIO    ."ROrcamentoDespesa.class.php"                                    );
include_once ( CAM_GA_ADM_NEGOCIO    ."RFuncao.class.php"                                              );

class RFolhaPagamentoConfiguracaoEvento
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
var $obTFolhaPagamentoEventoConfiguracaoEvento;
/**
   * @access Private
   * @var Object
*/
var $obTFolhaPagamentoConfiguracaoEventoDespesa;
/**
   * @access Private
   * @var Array
*/
var $arRFolhaPagamentoCasoEvento;
/**
   * @access Private
   * @var Object
*/
var $roUltimoCasoEvento;
/**
   * @access Private
   * @var Object
*/
var $roRFolhaPagamentoEvento;
/**
   * @access Private
   * @var Object
*/
var $obROrcamentoDespesa;
/**
   * @access Private
   * @var Integer
*/
var $inCodConfiguracao;
/**
   * @access Private
   * @var String
*/
var $stDescricao;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                                 = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTFolhaPagamentoEventoConfiguracaoEvento($valor) { $this->obTFolhaPagamentoEventoConfiguracaoEvento   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTFolhaPagamentoConfiguracaoEventoDespesa($valor) { $this->obTFolhaPagamentoConfiguracaoEventoDespesa  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setArRFolhaPagamentoCasoEvento($valor) { $this->arRFolhaPagamentoCasoEvento                 = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRoUltimoCasoEvento($valor) { $this->roUltimoCasoEvento                          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRoRFolhaPagamentoEvento(&$valor) { $this->roRFolhaPagamentoEvento                     = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setROrcamentoDespesa($valor) { $this->obROrcamentoDespesa                          = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodConfiguracao($valor) { $this->inCodConfiguracao                           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao                                 = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                                 }
/**
    * @access Public
    * @return Object
*/
function getTFolhaPagamentoEventoConfiguracaoEvento() { return $this->obTFolhaPagamentoEventoConfiguracaoEvento;   }
/**
    * @access Public
    * @return Object
*/
function getTFolhaPagamentoConfiguracaoEventoDespesa() { return $this->obTFolhaPagamentoConfiguracaoEventoDespesa;  }
/**
    * @access Public
    * @return Object
*/
function getArRFolhaPagamentoCasoEvento() { return $this->arRFolhaPagamentoCasoEvento;                 }
/**
    * @access Public
    * @return Object
*/
function getRoUltimoCasoEvento() { return $this->roUltimoCasoEvento;                          }
/**
    * @access Public
    * @return Object
*/
function getRoRFolhaPagamentoEvento() { return $this->roRFolhaPagamentoEvento;                     }
/**
    * @access Public
    * @return Object
*/
function getROrcamentoDespesa() { return $this->obROrcamentoDespesa;                         }
/**
    * @access Public
    * @return Integer
*/
function getCodConfiguracao() { return $this->inCodConfiguracao;                           }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;                                 }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoConfiguracaoEvento(&$obRFolhaPagamentoEvento)
{
    $this->setRoRFolhaPagamentoEvento                    ( $obRFolhaPagamentoEvento                     );
    $this->setTransacao                                  ( new Transacao                                );
    $this->setTFolhaPagamentoEventoConfiguracaoEvento    ( new TFolhaPagamentoEventoConfiguracaoEvento  );
    $this->setTFolhaPagamentoConfiguracaoEventoDespesa   ( new TFolhaPagamentoConfiguracaoEventoDespesa );
    $this->setArRFolhaPagamentoCasoEvento                ( array()                                      );
    $this->setROrcamentoDespesa                          ( new ROrcamentoDespesa                        );
}

/**
     * Adiciona um objeto RFolhaPagamentoCasoEvento ao array de referencia-objeto
     * @access Public
*/
function addCasoEvento()
{
    $obRFuncao = new RFuncao;
    $this->arRFolhaPagamentoCasoEvento[] = new RFolhaPagamentoCasoEvento( $this , $obRFuncao );
    $this->roUltimoCasoEvento = &$this->arRFolhaPagamentoCasoEvento[ count($this->arRFolhaPagamentoCasoEvento) - 1 ];
}

/**
     * Inclui uma configuração de evento
     * @access Public
*/
function incluirConfiguracaoEvento($boTransacao = "")
{
    //Incluindo EventoConfiguracaoEvento
    $this->obTFolhaPagamentoEventoConfiguracaoEvento->setDado ( "cod_configuracao" , $this->getCodConfiguracao()                    );
    $this->obTFolhaPagamentoEventoConfiguracaoEvento->setDado ( "cod_evento"       , $this->roRFolhaPagamentoEvento->getCodEvento() );
    $this->obTFolhaPagamentoEventoConfiguracaoEvento->setDado ( "timestamp"        , $this->roRFolhaPagamentoEvento->getTimestamp() );
    $obErro = $this->obTFolhaPagamentoEventoConfiguracaoEvento->inclusao ( $boTransacao );

    //Incluindo ConfiguracaoEventoDespesa
    if ( !$obErro->ocorreu() ) {
        if ( $this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getMascClassificacao() ) {
            $this->obROrcamentoDespesa->listarContaDespesa( $rsDespesa , $boTransacao );
            $this->obTFolhaPagamentoConfiguracaoEventoDespesa->setDado ( "cod_evento"       , $this->roRFolhaPagamentoEvento->getCodEvento());
            $this->obTFolhaPagamentoConfiguracaoEventoDespesa->setDado ( "timestamp"        , $this->roRFolhaPagamentoEvento->getTimestamp());
            $this->obTFolhaPagamentoConfiguracaoEventoDespesa->setDado ( "cod_configuracao" , $this->getCodConfiguracao()                   );
            $this->obTFolhaPagamentoConfiguracaoEventoDespesa->setDado ( "exercicio"        , $rsDespesa->getCampo('exercicio')             );
            $this->obTFolhaPagamentoConfiguracaoEventoDespesa->setDado ( "cod_conta"        , $rsDespesa->getCampo('cod_conta')             );
            $obErro = $this->obTFolhaPagamentoConfiguracaoEventoDespesa->inclusao ( $boTransacao );
        }
    }

    //Incluindo CasoEvento(s)
    if ( !$obErro->ocorreu() ) {
        foreach ($this->arRFolhaPagamentoCasoEvento as $obCasoEvento) {
            $obErro = $obCasoEvento->incluirCasoEvento( $boTransacao );
            if ( $obErro->ocorreu() )
                break;
        }
    }

    return $obErro;
}

/**
     * Exclui uma configuração de evento
     * @access Public
*/
function excluirConfiguracaoEvento($boTransacao="")
{
    $this->addCasoEvento();
    $obErro = $this->roUltimoCasoEvento->excluirCasoEvento( $boTransacao );

    //Despesa
    if ( !$obErro->ocorreu() ) {
        $this->obTFolhaPagamentoConfiguracaoEventoDespesa->setDado( 'cod_evento' , $this->roRFolhaPagamentoEvento->getCodEvento() );
        $obErro = $this->obTFolhaPagamentoConfiguracaoEventoDespesa->exclusao($boTransacao);
    }

    //ConfiguracaoEvento
    if ( !$obErro->ocorreu() ) {
        $this->obTFolhaPagamentoEventoConfiguracaoEvento->setDado( 'cod_evento' , $this->roRFolhaPagamentoEvento->getCodEvento() );
        $obErro = $this->obTFolhaPagamentoEventoConfiguracaoEvento->exclusao($boTransacao);
    }

    return $obErro;
}

/**
     * Consulta uma configuração de evento
     * @access Public
*/
function consultarConfiguracaoEvento($boTransacao="")
{
    //Despesa
    $this->obTFolhaPagamentoConfiguracaoEventoDespesa->setDado( 'cod_evento'        , $this->roRFolhaPagamentoEvento->getCodEvento() );
    $this->obTFolhaPagamentoConfiguracaoEventoDespesa->setDado( 'timestamp'         , $this->roRFolhaPagamentoEvento->getTimestamp() );
    $this->obTFolhaPagamentoConfiguracaoEventoDespesa->setDado( 'cod_configuracao'  , $this->getCodConfiguracao()                    );
    $obErro = $this->obTFolhaPagamentoConfiguracaoEventoDespesa->recuperaPorChave( $rsDespesa ,$boTransacao);
    if ( !$obErro->ocorreu() and $rsDespesa->getNumLinhas() > 0 ) {
        $this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setCodConta ( $rsDespesa->getCampo('cod_conta') );
        $this->obROrcamentoDespesa->setExercicio( $rsDespesa->getCampo('exercicio') );
        $obErro = $this->obROrcamentoDespesa->listarContaDespesa( $rsContaDespesa ,$boTransacao);
        if ( !$obErro->ocorreu() ) {
            $this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $rsContaDespesa->getCampo('cod_estrutural') );
        }
    }

    return $obErro;
}

/**
     * Método listar
     * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEvento.class.php");
    $obTFolhaPagamentoConfiguracaoEvento = new TFolhaPagamentoConfiguracaoEvento;
    $obErro = $obTFolhaPagamentoConfiguracaoEvento->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
     * Método listarConfiguracaoEvento
     * @access Public
*/
function listarConfiguracaoEvento(&$rsRecordSet,$boTransacao="")
{
    $stFiltro ="";
    if ( $this->getCodConfiguracao() ) {
        $stFiltro .= " WHERE cod_configuracao = ".$this->getCodConfiguracao();
    }
    $obErro = $this->listar($rsRecordSet,$stFiltro,'',$boTransacao);

    return $obErro;
}

/**
    * Método listarRubrica
    * @access Public
*/
function listarRubrica(&$rsRecordSet,$boTransacao="")
{
    if ( $this->roRFolhaPagamentoEvento->getCodEvento() ) {
        $stFiltro .= " AND configuracao_evento_despesa.cod_evento = ".$this->roRFolhaPagamentoEvento->getCodEvento();
    }
    if ( $this->roRFolhaPagamentoEvento->getTimestamp() ) {
        $stFiltro .= " AND configuracao_evento_despesa.timestamp = '".$this->roRFolhaPagamentoEvento->getTimestamp()."'";
    }
    if ( $this->getCodConfiguracao() ) {
        $stFiltro .= " AND configuracao_evento_despesa.cod_configuracao = ".$this->getCodConfiguracao();
    }
    $obErro = $this->obTFolhaPagamentoConfiguracaoEventoDespesa->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarConfiguracaoEventoPorChave
    * @access Public
*/
function listarConfiguracaoEventoPorChave(&$rsRecordSet,$inCodEvento,$stTimestamp,$inCodConfiguracao,$boTransacao="")
{
    $this->obTFolhaPagamentoEventoConfiguracaoEvento->setDado("cod_evento",$inCodEvento);
    $this->obTFolhaPagamentoEventoConfiguracaoEvento->setDado("timestamp",$stTimestamp);
    $this->obTFolhaPagamentoEventoConfiguracaoEvento->setDado("cod_configuracao",$inCodConfiguracao);
    $obErro = $this->obTFolhaPagamentoEventoConfiguracaoEvento->recuperaPorChave($rsRecordSet,$boTransacao);

    return $obErro;
}

}//end class
?>
