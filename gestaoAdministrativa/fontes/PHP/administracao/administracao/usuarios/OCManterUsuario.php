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
* Manutneção de usuários
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 15837 $
$Name$
$Author: cassiano $
$Date: 2006-09-22 12:07:46 -0300 (Sex, 22 Set 2006) $

Casos de uso: uc-01.03.93
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_FW_HTML.'MontaOrgUniDepSet.class.php' );

$obMontaOrgUniDepSet = new MontaOrgUniDepSet;
switch ($_REQUEST['stCtrl']) {
    case 'unidade':
        $arChaveOrgao = explode( '-', $_POST['stChaveOrgao'] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setCodOrgao( $arChaveOrgao[0] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setExercicio( $arChaveOrgao[1] );
        $obErro = $obMontaOrgUniDepSet->montarUnidade();
    break;
    case 'departamento':
        $arChaveOrgao = explode( '-', $_POST['stChaveOrgao'] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setCodOrgao( $arChaveOrgao[0] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setExercicio( $arChaveOrgao[1] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->setCodUnidade( $_POST['stChaveUnidade'] );
        $obErro = $obMontaOrgUniDepSet->montarDepartamento();
    break;
    case 'setor':
        $arChaveOrgao = explode( '-', $_POST['stChaveOrgao'] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setCodOrgao( $arChaveOrgao[0] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setExercicio( $arChaveOrgao[1] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->setCodUnidade( $_POST['stChaveUnidade'] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->setCodDepartamento( $_POST['stChaveDepartamento'] );
        $obErro = $obMontaOrgUniDepSet->montarSetor();
    break;
    case 'chaveSetor':
        $arChaveOrgao = explode( '-', $_POST['stChaveOrgao'] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setCodOrgao( $arChaveOrgao[0] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->obOrgao->setExercicio( $arChaveOrgao[1] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->obUnidade->setCodUnidade( $_POST['stChaveUnidade'] );
        $obMontaOrgUniDepSet->obRSetor->obDepartamento->setCodDepartamento( $_POST['stChaveDepartamento'] );
        $obMontaOrgUniDepSet->obRSetor->setCodSetor( $_POST['stChaveSetor'] );
        $obMontaOrgUniDepSet->montarChaveSetor();
    break;
    case 'montarPorChave':
        $obMontaOrgUniDepSet->setChaveSetor( $_POST['stChaveSetorTxt'] );
        $obMontaOrgUniDepSet->montarPorChave();
    break;
}
?>
