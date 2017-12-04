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
    * Filtro de Rescisão de Contrato TCEMG
    * Data de Criação   : 05/05/2014
    
    * @author Analista      Silvia Martins Silva
    * @author Desenvolvedor Michel Teixeira
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: FLRescindirContrato.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "RescindirContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList  );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obTxtContrato = new TextBox;
$obTxtContrato->setName   ( "inNumContrato"      );
$obTxtContrato->setValue  ( $inNumContrato       );
$obTxtContrato->setRotulo ( "Número do Contrato" );
$obTxtContrato->setTitle  ( "Informe o contrato.");
$obTxtContrato->setNull   ( true                 );
$obTxtContrato->setInteiro( true                 );

$obDtPublicacao = new Data;
$obDtPublicacao->setName   ( "dtPublicacao"                   	);
$obDtPublicacao->setRotulo ( "Data de Publicação"             	);
$obDtPublicacao->setValue  ( $dtPublicacao                      );
$obDtPublicacao->setTitle  ( 'Informe a data de publicação.'	);
$obDtPublicacao->setNull   ( true                              	);

$obDtInicial = new Data;
$obDtInicial->setName     ( "dtInicial"                      );
$obDtInicial->setRotulo   ( "Período do Contrato"            );
$obDtInicial->setTitle    ( 'Informe o período do contrato.' );
$obDtInicial->setNull     ( true                             );

$obLabel = new Label;
$obLabel->setValue( " até " );

$obDtFinal = new Data;
$obDtFinal->setName     ( "dtFinal"   );
$obDtFinal->setRotulo   ( "Período"   );
$obDtFinal->setTitle    ( ''          );
$obDtFinal->setNull     ( true        );

$obTxtObjContrato = new TextBox;
$obTxtObjContrato->setName   ( "stObjContrato"         );
$obTxtObjContrato->setId     ( "stObjContrato"         );
$obTxtObjContrato->setRotulo ( "Objeto do Contrato"    );
$obTxtObjContrato->setValue  ( $stObjContrato          );
$obTxtObjContrato->setNull   ( true                    );
$obTxtObjContrato->setSize   ( 40                      );

$obDtRescisao = new Data;
$obDtRescisao->setName     ( "dtRescisao"                                );
$obDtRescisao->setRotulo   ( "Data da Rescisão"                          );
$obDtRescisao->setTitle    ( 'Informe a data da rescisão do contrato.'   );
$obDtRescisao->setNull     ( true                                        );


//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden          ( $obHdnAcao                                    );
$obFormulario->addHidden          ( $obHdnCtrl                                    );
$obFormulario->addComponente      ( $obTxtContrato                                );
$obFormulario->addComponente      ( $obDtPublicacao                               );
$obFormulario->agrupaComponentes  ( array( $obDtInicial,$obLabel, $obDtFinal )    );
$obFormulario->addComponente      ( $obTxtObjContrato                             );
if($stAcao == "excluir")
    $obFormulario->addComponente  ( $obDtRescisao                                 );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
