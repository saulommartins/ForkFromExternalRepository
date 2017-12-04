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
    * Formulário de Cadastro para o arquivo obsMetaArrecadacao.txt
    * Data de Criação   : 21/01/2009

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ObsMetaArrecadacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//Verificar se já existe no banco, se não existir, incluir, senão alterar.

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName                    ( "stAcao"                               );
$obHdnAcao->setId                      ( "stAcao"                               );
$obHdnAcao->setValue                   ( $_REQUEST['stAcao']                    );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                    ( "stCtrl"                               );
$obHdnCtrl->setValue                   ( $_REQUEST['stCtrl']                    );

$obHdnMes = new Hidden;
$obHdnMes->setName                    ( "inMes"                               );
$obHdnMes->setId                      ( "inMes"                               );
$obHdnMes->setValue                   ( $_REQUEST['inMes']                    );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo             ( "Exercício"                            );
$obLblExercicio->setValue              ( $_REQUEST['inMes']."/".Sessao::getExercicio()           );

$obTxtOserv = new TextArea;
$obTxtOserv->setRotulo                 ( "Observações"                          );
$obTxtOserv->setName                   ( "stObserv"                             );
$obTxtOserv->setValue                  (  $_REQUEST['stObserv']                 );
$obTxtOserv->setNull                   ( false                                  );
$obTxtOserv->setMaxCaracteres          ( 4000                                   );
$obTxtOserv->setTitle                  ( "Observações sobre metas bimestrais de arrecadação do anexo 14.");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                           );
$obFormulario->addHidden        ( $obHdnAcao                        );
$obFormulario->addHidden        ( $obHdnMes                        );
$obFormulario->addHidden        ( $obHdnCtrl                        );
$obFormulario->addTItulo        ( 'Dados para o arquivo'            );
$obFormulario->addComponente    ( $obLblExercicio                   );
$obFormulario->addComponente    ( $obTxtOserv                       );
$obFormulario->ok();
$obFormulario->show();

$jsOnload = "montaParametrosGET('carregaFrmAlteracao', 'inMes');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
