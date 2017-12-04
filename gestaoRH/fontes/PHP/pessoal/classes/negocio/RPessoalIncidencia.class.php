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
    * Classe de regra de negócio para RPessoalIncidencia
    * Data de Criação: 05/04/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Regra de Negócio

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class RPessoalIncidencia
{
/**
   * @access Private
   * @var Object
*/
var $obTransacao;
/**
   * @access Private
   * @var Erro
*/
var $cod_incidencia;
/**
   * @access Private
   * @var Erro
*/
var $descricao;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao     = $valor; }
/**
    * @access Public
    * @param Erro $valor
*/
function setIncidencia($valor) { $this->cod_incidencia  = $valor; }
/**
    * @access Public
    * @param Erro $valor
*/
function setDescricao($valor) { $this->descricao       = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;     }
/**
    * @access Public
    * @return Erro
*/
function get_incidencia() { return $this->cod_incidencia;  }
/**
    * @access Public
    * @return Erro
*/
function getDescricao() { return $this->descricao;       }

/**
     * Método construtor
     * @access Private
*/
function RPessoalIncidencia()
{
    $this->setTransacao   ( new Transacao );
}

/**
    * Inclui
    * @access Public
*/
function incluir($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."T.class.php");
    $obTPessoalIncidencia = new TPessoalIncidencia;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalIncidencia );

    return $obErro;
}

/**
    * Alterar
    * @access Public
*/
function alterar($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."T.class.php");
    $obTPessoalIncidencia = new TPessoalIncidencia;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obT );

    return $obErro;
}

/**
    * Excluir
    * @access Public
*/
function excluir($boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TPessoalIncidencia.class.php");
    $obTPessoalIncidencia = new TPessoalIncidencia;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalIncidencia );

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TPessoalIncidencia.class.php");
    $obTPessoalIncidencia = new TPessoalIncidencia;
    $obErro = $obTPessoalIncidencia->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

}
?>
