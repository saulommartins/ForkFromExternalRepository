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
    * Data de Criação   : 12/04/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Regra

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-02.08.13
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:05  hboaventura
Ticket#10234#

Revision 1.7  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_ERRO );
include_once ( CAM_GF_EXP_NEGOCIO . "RExportacaoTCERJTipoAlteracao.class.php" );

class RExportacaoTCERJRelTipoAlteracao
{
/**
    * @access Private
    * @var Array
*/
var $arTipoAlteracao;
/**
    * @access Private
    * @var Object
*/
var $roUltimoTipoAlteracao;

/**
    * @access Private
    * @var Object
*/
var $obRExportacaoTCERJTipoAlteracao;

/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * Método Construtor
    * @access Private
*/
function RExportacaoTCERJRelTipoAlteracao()
{
    $this->arTipoAlteracao = array();
    $this->obRExportacaoTCERJTipoAlteracao = new RExportacaoTCERJTipoAlteracao;
    $this->obTransacao = new Transacao;
}

function addTipoAlteracao()
{
    $this->arTipoAlteracao[]     = new RExportacaoTCERJTipoAlteracao;
    $this->roUltimoTipoAlteracao  = &$this->arTipoAlteracao[ count( $this->arTipoAlteracao ) -1 ];
}

function salvarRelacionamento($boTransacao='')
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        foreach ($this->arTipoAlteracao as $obRExportacaoTCERJTipoAlteracao) {
            $obErro = $obRExportacaoTCERJTipoAlteracao->salvarTipoAlteracao($boTransacao);
            if($obErro->ocorreu())
                break;
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->$obRExportacaoTCERJTipoAlteracao );

    return $obErro;
}

}

?>
