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
   /*
    * Formulário de Cadastro para o arquivo execucaoVariacao.txt
    * Data de Criação   : 19/01/2009

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ExecucaoVariacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName                    ( "stAcao"                               );
$obHdnAcao->setValue                   ( $_REQUEST['stAcao']                    );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                    ( "stCtrl"                               );
$obHdnCtrl->setValue                   ( $_REQUEST['stCtrl']                    );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo             ( "Exercício"                            );
$obLblExercicio->setValue              ( "12/".Sessao::getExercicio()           );

$obTxtAdmDireta = new TextArea;
$obTxtAdmDireta->setRotulo             ( "Administração Direta"                 );
$obTxtAdmDireta->setName               ( "stAdmDireta"                          );
$obTxtAdmDireta->setValue              (  $_REQUEST['stAdmDireta']              );
$obTxtAdmDireta->setNull               ( false                                  );
$obTxtAdmDireta->setMaxCaracteres      ( 4000                                   );
$obTxtAdmDireta->setTitle              ( "Detalhamento da origem e aplicação dos recursos obtidos com a alienação de ativos para administração direta.");

$obTxtConsAut = new TextArea;
$obTxtConsAut->setRotulo               ( "Ativos para autarquias"               );
$obTxtConsAut->setName                 ( "stConsAut"                            );
$obTxtConsAut->setValue                ( $_REQUEST['stConsAut']                 );
$obTxtConsAut->setNull                 ( false                                  );
$obTxtConsAut->setMaxCaracteres        ( 4000                                   );
$obTxtConsAut->setTitle                ( "Detalhamento da origem e aplicação dos recursos obtidos com alienação de ativos para autarquias.");

$obTxtFund = new TextArea;
$obTxtFund->setRotulo                  ( "Ativos para fundações"                );
$obTxtFund->setName                    ( "stFund"                               );
$obTxtFund->setValue                   ( $_REQUEST['stFund']                    );
$obTxtFund->setNull                    ( false                                  );
$obTxtFund->setMaxCaracteres           ( 4000                                   );
$obTxtFund->setTitle                   ( "Detalhamento da origem e aplicação dos recursos obtidos com a alienação de ativos para fundações.");

$obTxtEmpEstDep = new TextArea;
$obTxtEmpEstDep->setRotulo             ( "Ativos para empresas estatais dependentes");
$obTxtEmpEstDep->setName               ( "stEmpEstDep"                          );
$obTxtEmpEstDep->setValue              ( $_REQUEST['stEmpEstDep']               );
$obTxtEmpEstDep->setNull               ( false                                  );
$obTxtEmpEstDep->setMaxCaracteres      ( 4000                                   );
$obTxtEmpEstDep->setTitle              ( "Detalhamento da origem e aplicação dos recursos obtidos com a alienação de ativos para empresas estatais dependentes.");

$obTxtDemaisEntidades = new TextArea;
$obTxtDemaisEntidades->setRotulo       ( "Demais Entidades"                     );
$obTxtDemaisEntidades->setName         ( "stDemaisEntidades"                    );
$obTxtDemaisEntidades->setValue        (  $_REQUEST['stDemaisEntidades']        );
$obTxtDemaisEntidades->setNull         ( false                                  );
$obTxtDemaisEntidades->setMaxCaracteres( 4000                                   );
$obTxtDemaisEntidades->setTitle        ( "Detalhamento da origem e aplicação dos recursos obtidos com a alienação de ativos para demais entidades.");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                           );
$obFormulario->addHidden        ( $obHdnAcao                        );
$obFormulario->addHidden        ( $obHdnCtrl                        );
$obFormulario->addTItulo        ( 'Dados para o arquivo'            );
$obFormulario->addComponente    ( $obLblExercicio                   );
$obFormulario->addComponente    ( $obTxtAdmDireta                   );
$obFormulario->addComponente    ( $obTxtConsAut                     );
$obFormulario->addComponente    ( $obTxtFund                        );
$obFormulario->addComponente    ( $obTxtEmpEstDep                   );
$obFormulario->addComponente    ( $obTxtDemaisEntidades             );
$obFormulario->ok();
$obFormulario->show();

if ($_REQUEST['stAcao'] == 'alterar') {
    $jsOnload = "executaFuncaoAjax('carregaFrmAlteracao');";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
