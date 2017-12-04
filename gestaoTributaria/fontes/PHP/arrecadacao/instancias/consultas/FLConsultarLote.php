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
    * Página de Filtro para Consulta de Lotes
    * Data de Criação   : 20/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: FLConsultarLote.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.6  2007/05/02 18:27:48  cercato
Bug #9138#

Revision 1.5  2007/02/07 17:51:36  rodrigo
#8344#

Revision 1.4  2007/02/05 11:50:34  cercato
Bug #7335#

Revision 1.3  2006/09/15 11:04:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarLote";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

Sessao::write( 'transf4', array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false ) );
Sessao::write( 'link', "" );

// CONSULTA CONFIGURACAO DO MODULO IMOBILIARIO
$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCIMConfiguracao->getMascaraIM();

// CONSULTA CONFIGURACAO DO MODULO ECONOMICO
$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setCodigoModulo( 14 );
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMConfiguracao->consultarConfiguracao();
$stMascaraInscricaoEconomico = $obRCEMConfiguracao->getMascaraInscricao();

$rsBanco = new RecordSet;
$rsAgencia = new RecordSet;
$obRMONConta = new RMONContaCorrente;
$obRMONConta->obRMONAgencia->obRMONBanco->listarBanco($rsBanco);
//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obBscContribuinte = new BuscaInner;
$obBscContribuinte->setId                ( "stContribuinte"          );
$obBscContribuinte->setRotulo        ( "Contribuinte"            );
$obBscContribuinte->setTitle            ( "Código do Contribuinte"  );
$obBscContribuinte->setNull             ( true                     );
$obBscContribuinte->obCampoCod->setName         ("inCodContribuinte"    );
$obBscContribuinte->obCampoCod->setValue        ( $_REQUEST["inCodContribuinte"]    );
$obBscContribuinte->obCampoCod->obEvento->setOnChange("buscaValor('buscaContribuinte');");
$obBscContribuinte->obCampoCod->obEvento->setOnKeyPress("iniciaAutoComplete(event,this);");
$obBscContribuinte->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinte','stContribuinte','','".Sessao::getId()."','800','450');" );

$obBscLoteInicio = new TextBox;
$obBscLoteInicio->setRotulo           ( "Lote"                            );
$obBscLoteInicio->setTitle               ( "Intervalo" );
$obBscLoteInicio->setName            ( "inCodLoteInicio"                  );
$obBscLoteInicio->setValue            ( $_REQUEST["inCodLoteInicio"]      );
$obBscLoteInicio->setSize               ( 10                              );
$obBscLoteInicio->setMaxLength    ( 10                                  );
$obBscLoteInicio->setNull               ( true                              );
$obBscLoteInicio->setInteiro           ( true                               );

$obBscLoteFinal = new TextBox;
$obBscLoteFinal->setRotulo           ( "Lote"                            );
$obBscLoteFinal->setTitle               ( "Intervalo" );
$obBscLoteFinal->setName            ( "inCodLoteFinal"                      );
$obBscLoteFinal->setValue            ( $_REQUEST["inCodLoteFinal"]          );
$obBscLoteFinal->setSize               ( 10                                 );
$obBscLoteFinal->setMaxLength    ( 10                                  );
$obBscLoteFinal->setNull               ( true                              );
$obBscLoteFinal->setInteiro           ( true                               );

$obTxtExercicio = new Exercicio;
$obTxtExercicio->setName       ( "stExercicio"     );
$obTxtExercicio->setId             ( "stExercicio"     );
$obTxtExercicio->setInteiro       ( true          );
$obTxtExercicio->setMaxLength ( 4             );
$obTxtExercicio->setSize           ( 4             );
$obTxtExercicio->setRotulo      ( "Exercício"       );
$obTxtExercicio->setTitle         ( "Exercício"       );
$obTxtExercicio->setNull          ( true              );
$obTxtExercicio->setValue       ( Sessao::getExercicio() );

$obTxtBanco = new TextBox;
$obTxtBanco->setRotulo        ( "Banco"                            );
$obTxtBanco->setTitle         ( "Banco ao qual a agência pertence" );
$obTxtBanco->setName          ( "inNumBanco"                       );
$obTxtBanco->setValue         ( $_REQUEST["inNumBanco"]            );
$obTxtBanco->setSize          ( 10                                 );
$obTxtBanco->setMaxLength     ( 6                                  );
$obTxtBanco->setNull          ( true                              );
$obTxtBanco->setInteiro       ( true                               );
$obTxtBanco->obEvento->setOnChange ( "preencheAgencia('');" );

$obCmbBanco = new Select;
$obCmbBanco->setName          ( "cmbBanco"                   );
$obCmbBanco->addOption        ( "", "Selecione"              );
$obCmbBanco->setValue         ( $_REQUEST['inNumBanco']      );
$obCmbBanco->setCampoId       ( "num_banco"                  );
$obCmbBanco->setCampoDesc     ( "nom_banco"                  );
$obCmbBanco->preencheCombo    ( $rsBanco                     );
$obCmbBanco->setNull          ( true                        );
$obCmbBanco->setStyle         ( "width: 220px"               );
$obCmbBanco->obEvento->setOnChange ( "preencheAgencia('');"  );

$obTxtAgencia = new TextBox;
$obTxtAgencia->setRotulo        ( "Agência"                                     );
$obTxtAgencia->setTitle         ( "Agência bancária na qual a conta foi aberta" );
$obTxtAgencia->setName          ( "inNumAgencia"                                );
$obTxtAgencia->setValue         ( $_REQUEST["inNumAgencia"]                     );
$obTxtAgencia->setSize          ( 10                                            );
$obTxtAgencia->setMaxLength     ( 6                                             );
$obTxtAgencia->setNull          ( true                                         );
$obTxtAgencia->setInteiro       ( true                                          );

$obCmbAgencia = new Select;
$obCmbAgencia->setName          ( "cmbAgencia"                   );
$obCmbAgencia->addOption       ( "", "Selecione"                );
$obCmbAgencia->setValue         ( $_REQUEST['inNumAgencia']      );
$obCmbAgencia->setCampoId       ( "num_agencia"                  );
$obCmbAgencia->setCampoDesc     ( "nom_agencia"                  );
$obCmbAgencia->preencheCombo    ( $rsAgencia                     );
$obCmbAgencia->setNull           ( true                          );
$obCmbAgencia->setStyle         ( "width: 220px"                 );

//------------------------
$obDtInicio  = new Data;
$obDtInicio->setName             ( "dtInicio"                );
$obDtInicio->setValue             ( $_REQUEST["dtInicio"]    );
$obDtInicio->setRotulo            ( "Data Inicial do Lote"        );
$obDtInicio->setTitle               ( "Intervalo inicial dos pagamentos por lote" );
$obDtInicio->setMaxLength     ( 20                                );
$obDtInicio->setSize                ( 10                                );
$obDtInicio->setNull                ( true                             );
//$obDtInicio->obEvento->setOnChange ( "validaData1500( this );"         );

$obLabelIntervalo = new Label;
$obLabelIntervalo->setValue ( "até" );

$obDtFinal  = new Data;
$obDtFinal->setName             ( "dtFinal"                );
$obDtFinal->setValue             ( $_REQUEST["dtFinal"]    );
$obDtFinal->setRotulo            ( "Data Final do Lote"        );
$obDtFinal->setTitle               ( "Intervalo final dos pagamentos por lote" );
$obDtFinal->setMaxLength     ( 20                                );
$obDtFinal->setSize                ( 10                                );
$obDtFinal->setNull                ( true                             );
//$obDtFinal->obEvento->setOnChange ( "validaData1500( this );"         );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao          );
$obFormulario->addHidden( $obHdnCtrl            );
$obFormulario->addTitulo( "Dados para Filtro"   );
$obFormulario->agrupaComponentes ( array ( $obBscLoteInicio , $obLabelIntervalo, $obBscLoteFinal ) );
$obFormulario->agrupaComponentes  ( array( $obDtInicio, $obLabelIntervalo, $obDtFinal ));
$obFormulario->addComponenteComposto    ($obTxtBanco,$obCmbBanco      );
$obFormulario->addComponenteComposto    ($obTxtAgencia,$obCmbAgencia  );
$obFormulario->addComponente( $obTxtExercicio          );
$obFormulario->addComponente( $obBscContribuinte   );

$obFormulario->ok();
$obFormulario->show();

?>
