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
    * Página Oculta de Requisito
    * Data de Criação   : 22/10/2012

    * @author Desenvolvedor: Davi Ritter Aroldi

    * @ignore

    * Caso de uso: uc-04.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalRequisito.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCargoRequisito.class.php" );

function excluirRequisito($inCodRequisito)
{
    $stJs = '';
    $obErro = new Erro;
    $obTPessoalCargoRequisito = new TPessoalCargoRequisito;
    $obTPessoalCargoRequisito->recuperaTodos($rsCargoRequisito, " WHERE cod_requisito = ".$inCodRequisito);

    $stMensagem = '';
    if ( $rsCargoRequisito->getNumLinhas() > 0 ) {
        $obErro->setDescricao('Este requisito está sendo usado!');
    }

    if ( !$obErro->ocorreu() ) {
        $obTPessoalRequisito = new TPessoalRequisito;
        $obTPessoalRequisito->setDado('cod_requisito', $inCodRequisito);

        $obErro = $obTPessoalRequisito->exclusao();
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','n_incluir','erro','".Sessao::getId()."',''); \n";
        $stJs .= "mudaTelaPrincipal('".CAM_GRH_PES_INSTANCIAS."cargo/LSManterRequisito.php'); \n";
    } else {
        $stJs .= atualizaCombo();
        $stJs .= "alertaAviso('Requisito excluído com sucesso!','incluir','aviso','".Sessao::getId()."');  \n";
        $stJs .= "mudaTelaPrincipal('".CAM_GRH_PES_INSTANCIAS."cargo/FMManterRequisito.php'); \n";
    }

    return $stJs;
}

function atualizaCombo()
{
    $stJs = '';
    $stFiltroRequisitosDisponiveis = '';

    if ($_REQUEST["cod_cargo"]) {
        $stFiltroRequisitosDisponiveis = 'WHERE requisito.cod_requisito NOT IN (SELECT cod_requisito
                                                                                  FROM pessoal.cargo_requisito
                                                                                 WHERE cod_cargo = '.$_REQUEST["cod_cargo"].'
                                                                              GROUP BY cod_requisito)';
    }
    $obTPessoalRequisito = new TPessoalRequisito;
    $obTPessoalRequisito->recuperaRequisitosDisponiveisCargo($rsRequisitosDisponiveis, $stFiltroRequisitosDisponiveis, ' ORDER BY descricao ');

    $stJs .= "var lengthSelect; \n";
    $stJs .= "window.parent.window.opener.document.getElementById('inCodRequisitosDisponiveis').length = 0; \n";

    foreach ($rsRequisitosDisponiveis->arElementos as $requisito) {
        $stJs .= "lengthSelect = window.parent.window.opener.document.getElementById('inCodRequisitosDisponiveis').length; \n";
        $stJs .= "window.parent.window.opener.document.getElementById('inCodRequisitosDisponiveis').options[lengthSelect] = new Option('".addslashes(trim($requisito['descricao']))."', ".$requisito['cod_requisito']."); \n";
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case 'excluirRequisito':
        $stJs = excluirRequisito($_GET['cod_requisito']);
        break;
}

if ($stJs) {
   echo $stJs;
}
?>
