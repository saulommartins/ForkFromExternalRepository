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
    * Data de Criação   : 11/04/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.10
*/

/*
$Log$
Revision 1.7  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_ERRO );
include_once ( CAM_GF_EXP_NEGOCIO . "RExportacaoTCERJContaDespesa.class.php" );

class RExportacaoTCERJRelDespesa
{
/**
    * @access Private
    * @var Array
*/
var $arContas;
/**
    * @access Private
    * @var Object
*/
var $roUltimaConta;

/**
    * @access Private
    * @var Object
*/
var $obRExportacaoTCERJContaDespesa;

/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * Método Construtor
    * @access Private
*/
function RExportacaoTCERJRelDespesa()
{
    $this->arContas = array();
    $this->obRExportacaoTCERJContaDespesa = new RExportacaoTCERJContaDespesa;
    $this->obTransacao = new Transacao;
}

function addConta()
{
    $this->arContas[]     = new RExportacaoTCERJContaDespesa;
    $this->roUltimaConta  = &$this->arContas[ count( $this->arContas ) -1 ];
}

function salvarRelacionamento($boTransacao='')
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        foreach ($this->arContas as $obRExportacaoTCERJContaDespesa) {
            $obErro = $obRExportacaoTCERJContaDespesa->salvarConta($boTransacao);
            if($obErro->ocorreu())
                break;
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->$obRExportacaoTCERJContaDespesa );

    return $obErro;
}

}

?>
