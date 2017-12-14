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
    * Formulário de Migração do Organograma
    * Data de Criação   : 06/01/2009

    * @author Analista      Gelson Gonçalves
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ProcessaMigracaoOrganograma";
$pgForm     = "FM".$stPrograma.".php";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$jsOnload = "montaParametrosGET('verificaStatus');";

$obForm = new Form;
$obForm->setAction          ( $pgProc                  );
$obForm->setTarget          ( "oculto"                 );

$obHdnAcao = new Hidden;
$obHdnAcao->setName         ( "stAcao"                 );
$obHdnAcao->setValue        ( $_REQUEST['stAcao']      );

$obLblLocal = new Label();
$obLblLocal->setRotulo      ( "Migrar Local"           );
$obLblLocal->setName        ( "stLocal"                );
$obLblLocal->setId          ( "stLocal"                );

$obLblSetor = new Label();
$obLblSetor->setRotulo      ( "Migrar Setor"           );
$obLblSetor->setName        ( "stSetor"                );
$obLblSetor->setId          ( "stSetor"                );

$obLblVersaoAnt = new Label();
$obLblVersaoAnt->setRotulo  ( "Versões Anteriores"     );
$obLblVersaoAnt->setName    ( "stObsVersaoAnt"         );
$obLblVersaoAnt->setValue   ( "     GA_1.92.2
                               <br> GF_1.93.5
                               <br> GP_1.91.3
                               <br> GT_1.95.1
                               <br> GRH_1.95.0
                               <br> GPC_1.91.2 "       );
$obLblVersaoAnt->setTitle   ( "Versão máxima antes da virada do Organograma");

$obLblVersaoPos = new Label();
$obLblVersaoPos->setRotulo  ( "Versões Posteriores"    );
$obLblVersaoPos->setValue   ( "     GA_1.93.0
                               <br> GF_1.93.6
                               <br> GP_1.92.0
                               <br> GT_1.96.0
                               <br> GRH_1.95.1
                               <br> GPC_1.91.3 "       );
$obLblVersaoPos->setTitle   ( "Versão mínima após a virada do Organograma");

$obBtnOk = new Ok(true);
$obBtnOk->setId( 'Ok' );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                                               );
$obFormulario->addHidden            ( $obHdnAcao                                            );
$obFormulario->addTitulo            ( "Status"                                              );
$obFormulario->addComponente        ( $obLblLocal                                           );
$obFormulario->addComponente        ( $obLblSetor                                           );
$obFormulario->addTitulo            ( "Requisitos de versões para Migração do Organograma"  );
$obFormulario->addComponente        ( $obLblVersaoAnt                                       );
$obFormulario->addComponente        ( $obLblVersaoPos                                       );
$obFormulario->defineBarra          ( array( $obBtnOk )                                     );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
