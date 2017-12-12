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
  * Página de
  * Data de criação : 07/07/2005

    * @author Analista:
    * @author Programador: rafaelpa

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.06.06
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_BEN_MAPEAMENTO."TBeneficioVigencia.class.php"        );
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioFaixaDesconto.class.php"        );

class RBeneficioVigencia
{
/**
    * @access Private
    * @var Integer
*/
var $inCodVigencia;

/**
    * @access Private
    * @var Date
*/
var $dtVigencia;

/**
    * @access Private
    * @var String
*/
var $stTipo;

/**
    * @access Private
    * @var String
*/
var $arRBeneficioFaixaDesconto;

/**
    * @access Private
    * @var String
*/
var $roUltimoFaixaDesconto;

/**
    * @access Private
    * @var String
*/
var $obTBeneficioVigencia;

/**
     * @access Public
     * @param Object $valor
*/
function setCodVigencia($valor) { $this->inCodVigencia                    = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setDataVigencia($valor) { $this->dtVigencia                       = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setTipo($valor) { $this->stTipo                           = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodNorma($valor) { $this->inCodNorma                       = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setTBeneficioVigencia($valor) { $this->obTBeneficioVigencia             = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getCodVigencia() { return $this->inCodVigencia;                       }
/**
     * @access Public
     * @param Object $valor
*/
function getDataVigencia() { return $this->dtVigencia;                          }
/**
     * @access Public
     * @param Object $valor
*/
function getTipo() { return $this->stTipo;                              }
/**
     * @access Public
     * @param Object $valor
*/
function getCodNorma() { return $this->inCodNorma;                          }
/**
     * @access Public
     * @param Object $valor
*/
function getTBeneficioVigencia() { return $this->obTBeneficioVigencia;                }
/**
     * @access Public
     * @param Object $valor
*/
function getTransacao() { return $this->obTransacao;                         }

/**
* Método Construtor
* @access Private
*/
function RBeneficioVigencia()
{
    $this->setTransacao                             ( new Transacao                     );
    $this->setTBeneficioVigencia                    ( new TBeneficioVigencia        );
    $this->arFaixas                                 = array();
    $this->arRBeneficioFaixaDesconto                = array();
}

function addBeneficioFaixaDesconto()
{
    $this->arRBeneficioFaixaDesconto[] = new RBeneficioFaixaDesconto( $this );
    $this->roUltimoFaixaDesconto       = &$this->arRBeneficioFaixaDesconto[ count($this->arRBeneficioFaixaDesconto) - 1 ];
}

function incluirVigencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro =  $this->obTBeneficioVigencia->proximoCod( $inCodVigencia, $boTransacao );
        $this->setCodVigencia( $inCodVigencia );
        if ( !$obErro->ocorreu() ) {
            $this->obTBeneficioVigencia->setDado("cod_vigencia"           , $this->getCodVigencia()  );
            $this->obTBeneficioVigencia->setDado("vigencia"               , $this->getDataVigencia() );
            $this->obTBeneficioVigencia->setDado("tipo"                   , $this->getTipo()         );
            $this->obTBeneficioVigencia->setDado("cod_norma"              , $this->getCodNorma()     );

            $obErro = $this->obTBeneficioVigencia->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->roUltimoFaixaDesconto->incluirFaixaDesconto( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPrevidencia );

    return $obErro;
}

function excluirVigencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->roUltimoFaixaDesconto->excluirFaixaDesconto( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTBeneficioVigencia->setDado('cod_vigencia', $this->getCodVigencia() );
            $obErro = $this->obTBeneficioVigencia->exclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPrevidencia );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarVigencia(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if( $this->stTipo )
        $stFiltro .= " WHERE tipo = '".$this->stTipo."'";
    if( $this->getCodVigencia() )
        $stFiltro .= " WHERE cod_vigencia = " . $this->getCodVigencia();
    $stOrder = ($stOrder)?$stOrder:" ORDER BY vigencia DESC";
    $obErro = $this->obTBeneficioVigencia->recuperaBeneficio( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
