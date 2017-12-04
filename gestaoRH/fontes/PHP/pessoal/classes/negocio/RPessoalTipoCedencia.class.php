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
* Classe de regra de negócio para RPessoalTipoCedencia
* Data de Criação: 08/09/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Negócio

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalTipoCedencia.class.php"               );

class RPessoalTipoCedencia
{
/**
   * @access Private           Transacao
   * @var Object
*/
var $obTransacao;
/**
   * @access Private
   * @var Object
*/
var $obTPessoalTipoCedencia;
/**
   * @access Private
   * @var Erro
*/
var $inCodTipo;
/**
   * @access Private
   * @var Erro
*/
var $stDescricao;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao             = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTPessoalTipoCedencia($valor) { $this->obTPessoalTipoCedencia  = $valor; }
/**
    * @access Public
    * @param Erro $valor
*/
function setCodTipo($valor) { $this->inCodTipo               = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->inDescricao             = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;             }
/**
    * @access Public
    * @return Object
*/
function getTPessoalTipoCedencia() { return $this->obTPessoalTipoCedencia;  }
/**
    * @access Public
    * @return String
*/
function getCodTipo() { return $this->inCodTipo;               }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;             }

/**
     * Método construtor
     * @access Private
*/
function RPessoalTipoCedencia()
{
    $this->setTransacao                         ( new Transacao                         );
    $this->setTPessoalTipoCedencia              ( new TPessoalTipoCedencia              );
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro,$stOrder,$boTransacao)
{
    $obErro = $this->obTPessoalTipoCedencia->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listar
    * @access Public
*/
function listarCedencia(&$rsRecordSet,$boTransacao)
{
    $stOrder = " ORDER BY cod_tipo";
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

}
?>
