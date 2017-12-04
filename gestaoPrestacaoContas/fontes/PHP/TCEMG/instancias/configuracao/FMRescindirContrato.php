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
    * Formulário de Rescisão de Contrato TCEMG
    * Data de Criação   : 05/05/2014
    
    * @author Analista      Silvia Martins Silva
    * @author Desenvolvedor Michel Teixeira
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: FMRescindirContrato.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_CGM_COMPONENTES.'IPopUpCGMVinculado.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContrato.class.php';

$stAcao = $_REQUEST['stAcao'];

$stPrograma = "RescindirContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$obForm = new Form;
$obForm->setAction(  $pgProc );
$obForm->setTarget( "oculto" );

//Hidden's
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnExercicioContrato= new Hidden;
$obHdnExercicioContrato->setName( "stExercicioContrato" );
$obHdnExercicioContrato->setValue( $_REQUEST['stExercicioContrato'] );

$obHdnNumContrato= new Hidden;
$obHdnNumContrato->setName( "inNumContrato" );
$obHdnNumContrato->setValue( $_REQUEST['inNumContrato'] );

$obHdnCodEntidadeContrato= new Hidden;
$obHdnCodEntidadeContrato->setName( "inCodEntidadeContrato" );
$obHdnCodEntidadeContrato->setValue( $_REQUEST['inCodEntidade'] );

$obHdnCodContrato= new Hidden;
$obHdnCodContrato->setName( "inCodContrato" );
$obHdnCodContrato->setValue( $_REQUEST['inCodContrato'] );

$obHdnDtInicioContrato = new Hidden;
$obHdnDtInicioContrato->setName ( "dtInicioContrato" );
$obHdnDtInicioContrato->setId   ( "dtInicioContrato" );

//Consulta de Existencia do Contrato
$obTTCEMGContrato = new TTCEMGContrato;
$stFiltro  = "   WHERE contrato.exercicio = '".$_REQUEST['stExercicioContrato']."'";
$stFiltro .= "   AND contrato.nro_contrato = ".$_REQUEST['inNumContrato'];
$stFiltro .= "   AND contrato.cod_entidade = ".$_REQUEST['inCodEntidade'];
$stFiltro .= "   AND contrato.cod_contrato = ".$_REQUEST['inCodContrato'];

if($_REQUEST['stExercicioContrato']!=''&&$_REQUEST['inNumContrato']!=''&&$_REQUEST['inCodEntidade']!=''&&$_REQUEST['inCodContrato']!='')
    $obTTCEMGContrato->recuperaContrato($rsContratos, $stFiltro, $stOrder);

//Montando Valores do Contrato
if($rsContratos->inNumLinhas==1){    
    //Valores de Contrato para os Labels
    $inNumContrato = $rsContratos->arElementos[0]['nro_contrato']."/".$rsContratos->arElementos[0]['exercicio'];
    $inCodEntidade = $rsContratos->arElementos[0]['nom_entidade'];
    $dtAssinatura = explode("-", $rsContratos->arElementos[0]['data_assinatura']);
    $dtAssinatura = $dtAssinatura[2]."/".$dtAssinatura[1]."/".$dtAssinatura[0];
    $stModalidadeLicit = $rsContratos->arElementos[0]['st_modalidade'];
    $stNatureza = $rsContratos->arElementos[0]['st_natureza'];
    $stObjeto = $rsContratos->arElementos[0]['objeto_contrato'];
    $stInstrumento = $rsContratos->arElementos[0]['st_instrumento'];
    $stPeriodoContrato = explode("-", $rsContratos->arElementos[0]['data_inicio'].'-'.$rsContratos->arElementos[0]['data_final']);
    $obHdnDtInicioContrato->setValue( $stPeriodoContrato[2]."/".$stPeriodoContrato[1]."/".$stPeriodoContrato[0] );
    $stPeriodoContrato = $stPeriodoContrato[2]."/".$stPeriodoContrato[1]."/".$stPeriodoContrato[0]." até ".$stPeriodoContrato[5]."/".$stPeriodoContrato[4]."/".$stPeriodoContrato[3];
    $vlContrato = number_format($rsContratos->arElementos[0]['vl_contrato'],2,',','.');
}

/* Informações do Contrato */
$obLblNumContrato = new Label;
$obLblNumContrato->setRotulo    ( "Número do Contrato"      );
$obLblNumContrato->setId        ( "inNumContrato"           );
$obLblNumContrato->setValue     ( $inNumContrato            );

$obLblCodEntidade = new Label;
$obLblCodEntidade->setRotulo    ( "Entidade"                );
$obLblCodEntidade->setId        ( "stEntidade"              );
$obLblCodEntidade->setValue     ( $inCodEntidade            );

$obLblDtAssinatura = new Label;
$obLblDtAssinatura->setRotulo   ( "Data de Assinatura"      );
$obLblDtAssinatura->setId       ( "dtAssinatura"            );
$obLblDtAssinatura->setValue    ( $dtAssinatura             );

$obLblModalidadeLicit = new Label;
$obLblModalidadeLicit->setRotulo( "Modalidade de Licitação" );
$obLblModalidadeLicit->setId    ( "stModalidadeLicit"       );
$obLblModalidadeLicit->setValue ( $stModalidadeLicit        );

$obLblStNatureza = new Label;
$obLblStNatureza->setRotulo     ( "Natureza do Objeto"      );
$obLblStNatureza->setId         ( "stNatureza"              );
$obLblStNatureza->setValue      ( $stNatureza               );

$obLblStObjeto = new Label;
$obLblStObjeto->setRotulo       ( "Objeto do Contrato"      );
$obLblStObjeto->setId           ( "stObjeto"                );
$obLblStObjeto->setValue        ( $stObjeto                 );

$obLblStInstrumento = new Label;
$obLblStInstrumento->setRotulo  ( "Tipo de Instrumento"     );
$obLblStInstrumento->setId      ( "stInstrumento"           );
$obLblStInstrumento->setValue   ( $stInstrumento            );

$obLblStPeriodo = new Label;
$obLblStPeriodo->setRotulo      ("Período do Contrato"      );
$obLblStPeriodo->setId          ("stPeriodoContrato"        );
$obLblStPeriodo->setValue       ($stPeriodoContrato         );

$obLblVlContrato = new Label;
$obLblVlContrato->setRotulo     ( "Valor do Contrato"       );
$obLblVlContrato->setId         ( "vlContrato"              );
$obLblVlContrato->setValue      ( $vlContrato               );
/* Fim das Informações do Contrato */

/* Rescisão */
$obDtRescisao = new Data;
$obDtRescisao->setId    ( "dtRescisao"                              );
$obDtRescisao->setName  ( "dtRescisao"                              );
$obDtRescisao->setRotulo( "Data da Rescisão"                        );
$obDtRescisao->setTitle ( 'Informe a Data da Rescisão Contratual.'  );
$obDtRescisao->setNull  ( false );
$obDtRescisao->setValue ( ''    );

$obTxtVlRescisao = new Moeda;
$obTxtVlRescisao->setTitle      ( 'Informe o Valor da Rescisão Contratual.' );
$obTxtVlRescisao->setName       ( "nuVlRescisao"                            );
$obTxtVlRescisao->setId         ( "nuVlRescisao"                            );
$obTxtVlRescisao->setRotulo     ( "Valor da Rescisão"                       );
$obTxtVlRescisao->setAlign      ( 'RIGHT'                                   );
$obTxtVlRescisao->setTitle      ( ""                                        );
$obTxtVlRescisao->setMaxLength  ( 19    );
$obTxtVlRescisao->setSize       ( 21    );
$obTxtVlRescisao->setNull       ( false );
$obTxtVlRescisao->setValue      ( ''    );
/* Fim Rescisão */

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnExercicioContrato   );
$obFormulario->addHidden( $obHdnCodContrato         );
$obFormulario->addHidden( $obHdnNumContrato         );
$obFormulario->addHidden( $obHdnCodEntidadeContrato );
$obFormulario->addHidden( $obHdnDtInicioContrato    );
$obFormulario->addTitulo    ( "Dados do Contrato"   );
$obFormulario->addComponente( $obLblNumContrato     );
$obFormulario->addComponente( $obLblCodEntidade     );
$obFormulario->addComponente( $obLblDtAssinatura    );
$obFormulario->addComponente( $obLblModalidadeLicit );
$obFormulario->addComponente( $obLblStNatureza      );
$obFormulario->addComponente( $obLblStObjeto        );
$obFormulario->addComponente( $obLblStInstrumento   );
$obFormulario->addComponente( $obLblStPeriodo       );
$obFormulario->addComponente( $obLblVlContrato      );
$obFormulario->addTitulo    ( "Dados da Rescisão de Contrato"   );
$obFormulario->addComponente( $obDtRescisao                     );
$obFormulario->addComponente( $obTxtVlRescisao                  );

$obOk  = new Ok();
$obOk->obEvento->setOnClick("ValidaRescindir();");

$stFiltro  = "&pg=".Sessao::read('pg');
$stFiltro .= "&pos=".Sessao::read('pos');
$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

$obCancelar  = new Cancelar;
$obCancelar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");

$obFormulario->defineBarra( array( $obOk, $obCancelar ) );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';