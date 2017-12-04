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
* Classe de regra de negócio para PessoalRecisao
* Data de Criação: 01/07/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida

* @package URBEM
* @subpackage Regra

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-00.00.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCausaRescisao.class.php"   );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalContratoServidor.class.php"               );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCausaRescisao.class.php"                  );

class RPessoalRescisao
{
/**
    * @access Private
    * @var date
**/
var $dtDataRescisao;

/**
    * @access Private
    * @var object
**/
var $roPessoalContratoServidor;

/**
    * @access Private
    * @var Object
*/
var $arRPessoalContratoServidor;

/**
    * @access Private
    * @var Object
*/
var $roUltimoPessoalContratoServidor;
/**
    * @access Private
    * @var Object
*/
var $obRPessoalCausaRescisao;

/**
    * @access Public
    * @param array $valor
*/
function setDataRescisao($valor) { $this->dtDataRescisao  = $valor; }

/**
    * @access Public
    * @param array $valor
*/
function setRPessoalCausaRescisao($valor) { $this->obRPessoalCausaRescisao = $valor; }

/**
    * @access Public
    * @return date
*/
function getDataRescisao() { return $this->dtDataRescisao; }

/**
* @access Public
* @param Object $valor
*/
function getRPessoalCausaRescisao() { return $this->obRPessoalCausaRescisao;                               }

/**
     * Método construtor
     * @access Private
*/
function RPessoalRescisao(&$roRPessoalContratoServidor)
{
    $this->obTPessoalContratoServidorCausaRescisao = new TPessoalContratoServidorCausaRescisao;
    $this->roRPessoalContratoServidor         = &$roRPessoalContratoServidor;
    $this->obTransacao                       = new Transacao;
    $this->setRPessoalCausaRescisao           ( new RPessoalCausaRescisao );
}

function incluirRescisao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalContratoServidorCausaRescisao->setDado('cod_contrato', $this->roRPessoalContratoServidor->getCodContrato() );
        $this->obTPessoalContratoServidorCausaRescisao->setDado('cod_causa_rescisao',$this->obRPessoalCausaRescisao->getCodCausaRescisao() );
        $this->obTPessoalContratoServidorCausaRescisao->setDado('dt_rescisao', $this->getDataRescisao() );
        $obErro = $this->obTPessoalContratoServidorCausaRescisao->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalContratoServidorCausaRescisao );

    return $obErro;
}

function excluirRescisao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
              $this->obTPessoalContratoServidorCausaRescisao->setDado('cod_contrato', $this->roRPessoalContratoServidor->getCodContrato() );
              $obErro = $this->obTPessoalContratoServidorCausaRescisao->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalContratoServidorCausaRescisao );

    return $obErro;
}

}
