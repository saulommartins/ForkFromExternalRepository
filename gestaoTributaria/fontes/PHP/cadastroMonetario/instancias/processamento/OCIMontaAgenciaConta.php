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
 * Arquivo Oculto do componente IMontaAgenciaConta
 *
 * @category    Urbem
 * @package     Monetario
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include CAM_GT_MON_NEGOCIO . 'RMONContaCorrente.class.php';

$stJs = '';
switch ($_GET['stCtrl']) {
case 'buscaConta':
    $obErro = new Erro();

    if ($_GET['inCodBanco'] == '') {
        $obErro->setDescricao('Selecione o Banco');
    } elseif ($_GET['stNumAgencia'] == '') {
        $obErro->setDescricao('Selecione a Agência');
    } elseif ($_GET['stNumeroConta'] == '') {
        $obErro->setDescricao('');
    }

    if (!$obErro->ocorreu()) {
        $obRMONContaCorrente = new RMONContaCorrente();
        $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setNumBanco($_GET['inCodBanco']   );
        $obRMONContaCorrente->obRMONAgencia->setNumAgencia           ($_GET['stNumAgencia'] );
        $obRMONContaCorrente->setNumeroConta                         ($_GET['stNumeroConta']);
        if ($_GET['boVinculoPlanoBanco']) {
            $obRMONContaCorrente->boVinculoPlanoBanco = $_GET['boVinculoPlanoBanco'];
        }

        $obRMONContaCorrente->listarContaCorrente($rsContaCorrente);

        if ($rsContaCorrente->getNumLinhas() <= 0) {
            $obErro->setDescricao('Conta Corrente inválida');
        }
    }

    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','frm','erro','".Sessao::getId()."'); \n";
        $stJs .= "jq('#stContaCorrente').val('');";
    }

    break;
case 'limpaFiltro':
    Sessao::remove('paginando');
    break;
}

echo $stJs;
