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
    * Data de Criação   : 10/02/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Regras

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.06
*/

/*
$Log$
Revision 1.7  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_ERRO );
include_once ( CAM_GF_EXP_NEGOCIO . "RExportacaoTCERSCredor.class.php" );

class RExportacaoTCERSArqCredor
{
/**
    * @access Private
    * @var Array
*/
var $arCredores;
/**
    * @access Private
    * @var Object
*/
var $roUltimoCredor;

/**
    * @access Private
    * @var Object
*/
var $obRExportacaoTCERSCredor;

/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * Método Construtor
    * @access Private
*/
function RExportacaoTCERSArqCredor()
{
    $this->arCredores = array();
    $this->obRExportacaoTCERSCredor = new RExportacaoTCERSCredor;
    $this->obTransacao = new Transacao;
}

function addCredor()
{
    $this->arCredores[]     = new RExportacaoTCERSCredor();
    $this->roUltimoCredor  = &$this->arCredores[ count( $this->arCredores ) -1 ];
}

function salvar($boTransacao='')
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        foreach ($this->arCredores as $obRExportacaoTCERSCredor) {
            $obErro = $obRExportacaoTCERSCredor->salvar($boTransacao);
            if($obErro->ocorreu())
                break;
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->$obRExportacaoTCERSCredor );

    return $obErro;
}

}

?>
