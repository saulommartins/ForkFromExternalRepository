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
 * Arquivo Oculto do componente IMontaContaCheque
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include CAM_GF_TES_NEGOCIO . 'RTesourariaCheque.class.php';

switch ($_GET['stCtrl']) {
case 'buscaCheque':
    $obErro = new Erro();

    if ($_GET['inCodBanco'] == '') {
        $obErro->setDescricao('Selecione o Banco');
    } elseif ($_GET['stNumAgencia'] == '') {
        $obErro->setDescricao('Selecione a Agência');
    } elseif ($_GET['stNumeroConta'] == '') {
        $obErro->setDescricao('Informe a Conta Corrente');
    }

    if (!$obErro->ocorreu() AND $_GET['stNumCheque'] != '') {
        $obRTesourariaCheque = new RTesourariaCheque();
        $obRTesourariaCheque->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco = $_GET['inCodBanco'   ];
        $obRTesourariaCheque->obRMONContaCorrente->obRMONAgencia->stNumAgencia            = $_GET['stNumAgencia' ];
        $obRTesourariaCheque->obRMONContaCorrente->stNumeroConta                          = $_GET['stNumeroConta'];
        $obRTesourariaCheque->stNumCheque                                                 = $_GET['stNumCheque'  ];
        $obRTesourariaCheque->stTipoBusca                                                 = $_GET['stTipoBusca'  ];
        $obRTesourariaCheque->listCheque($rsCheque,array('stTipoBusca' => $_GET['stTipoBusca']));

        if ($rsCheque->getNumLinhas() <= 0) {
            $obErro->setDescricao('Número do Cheque inválido');
        }
    }
    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','frm','erro','".Sessao::getId()."'); \n";
        $stJs .= "jq('#stNumCheque').val('');";
    }

    break;
case 'limpaFiltro':
    Sessao::remove('paginando');

    break;
}

echo $stJs;
