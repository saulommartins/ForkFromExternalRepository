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
    * Configuração para Consideração-Execução Variação.
    * Data de Criação   : 03/09/2013

    * @author Desenvolvedor : Grace Mungunda Waka

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGExecucaoVariacao.class.php" );

$stPrograma = "ConsideracaoExecucaoVariacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( "$pgJS");

$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

$rsExecucaoVariacao = new RecordSet();
$obTTCEMGExecucaoVariacao = new TTCEMGExecucaoVariacao();
$obTTCEMGExecucaoVariacao->setDado('cod_mes', (int) $_REQUEST['inMes']);
$obTTCEMGExecucaoVariacao->recuperaDadosArquivo($rsExecucaoVariacao);
//if ($rsExecucaoVariacao->eof()) {
//    $_REQUEST['stAcao'] = 'incluir';
//} else {
//    $_REQUEST['stAcao'] = 'alterar';
//}

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName                    ( "stAcao"                               );
$obHdnAcao->setValue                   ( $_REQUEST['stAcao']                    );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                    ( "stCtrl"                               );
$obHdnCtrl->setValue                   ( $_REQUEST['stCtrl']                    );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo             ( "Mês/Exercício"                            );
$obLblExercicio->setValue              ( $_REQUEST['inMes']."/".Sessao::getExercicio()           );

$obMes = new Hidden;
$obMes->setName                    ( "inMes"                               );
$obMes->setValue                   ( $_REQUEST['inMes']                   );

$obTxtAdmDireta = new TextArea;
$obTxtAdmDireta->setRotulo             ( "Administração Direta"                 );
$obTxtAdmDireta->setName               ( "stAdmDireta"                          );
$obTxtAdmDireta->setValue              (  $rsExecucaoVariacao->getCampo('cons_adm_dir'));
$obTxtAdmDireta->setNull               ( false                                  );
$obTxtAdmDireta->setMaxCaracteres      ( 4000                                   );
$obTxtAdmDireta->setTitle              ( "Detalhamento da origem e aplicação dos recursos obtidos com a alienação de ativos para administração direta.");

$obTxtConsAut = new TextArea;
$obTxtConsAut->setRotulo               ( "Ativos para autarquias"               );
$obTxtConsAut->setName                 ( "stConsAut"                            );
$obTxtConsAut->setValue                ( $rsExecucaoVariacao->getCampo('cons_aut')          );
$obTxtConsAut->setNull                 ( false                                  );
$obTxtConsAut->setMaxCaracteres        ( 4000                                   );
$obTxtConsAut->setTitle                ( "Detalhamento da origem e aplicação dos recursos obtidos com alienação de ativos para autarquias.");

$obTxtFund = new TextArea;
$obTxtFund->setRotulo                  ( "Ativos para fundações"                );
$obTxtFund->setName                    ( "stFund"                               );
$obTxtFund->setValue                   ($rsExecucaoVariacao->getCampo('cons_fund')                 );
$obTxtFund->setNull                    ( false                                  );
$obTxtFund->setMaxCaracteres           ( 4000                                   );
$obTxtFund->setTitle                   ( "Detalhamento da origem e aplicação dos recursos obtidos com a alienação de ativos para fundações.");

$obTxtEmpEstDep = new TextArea;
$obTxtEmpEstDep->setRotulo             ( "Ativos para empresas estatais dependentes");
$obTxtEmpEstDep->setName               ( "stEmpEstDep"                          );
$obTxtEmpEstDep->setValue              (  $rsExecucaoVariacao->getCampo('cons_empe_est_dep')     );
$obTxtEmpEstDep->setNull               ( false                                  );
$obTxtEmpEstDep->setMaxCaracteres      ( 4000                                   );
$obTxtEmpEstDep->setTitle              ( "Detalhamento da origem e aplicação dos recursos obtidos com a alienação de ativos para empresas estatais dependentes.");

$obTxtDemaisEntidades = new TextArea;
$obTxtDemaisEntidades->setRotulo       ( "Demais Entidades"                     );
$obTxtDemaisEntidades->setName         ( "stDemaisEntidades"                    );
$obTxtDemaisEntidades->setValue        (  $rsExecucaoVariacao->getCampo('cons_dem_ent')            );
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
$obFormulario->addHidden        ( $obMes                        );
$obFormulario->addTItulo        ( 'Dados para o arquivo'            );
$obFormulario->addComponente    ( $obLblExercicio                   );
$obFormulario->addComponente    ( $obTxtAdmDireta                   );
$obFormulario->addComponente    ( $obTxtConsAut                     );
$obFormulario->addComponente    ( $obTxtFund                        );
$obFormulario->addComponente    ( $obTxtEmpEstDep                   );
$obFormulario->addComponente    ( $obTxtDemaisEntidades             );
$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
