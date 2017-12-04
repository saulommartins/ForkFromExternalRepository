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
    * Página de Filtro para Relatorio de Resumo de Lotes
    * Data de Criação   : 06/03/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: FLResumoLotes.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ResumoLotes";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

Sessao::write( 'sessao_transf4', array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false ) );
Sessao::write( 'link', array() );

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
$obForm->setAction( "OCGeraRelatorioResumoLote.php" );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
if (isset($stCtrl)) {
    $obHdnCtrl->setValue ( $stCtrl );
}

//Lote Automatico
$obRdbLoteAutomatico = new Radio;
$obRdbLoteAutomatico->setRotulo   ( "Tipo de Lote" );
$obRdbLoteAutomatico->setTitle    ( "Tipo de lote." );
$obRdbLoteAutomatico->setName     ( "stTipoLote" );
$obRdbLoteAutomatico->setLabel    ( "Automático" );
$obRdbLoteAutomatico->setValue    ( "automatico" );
$obRdbLoteAutomatico->setChecked  ( true );
$obRdbLoteAutomatico->setNull     ( true );

//Lote Manual
$obRdbLoteManual = new Radio;
$obRdbLoteManual->setRotulo   ( "Tipo de Lote" );
$obRdbLoteManual->setTitle    ( "Tipo de lote." );
$obRdbLoteManual->setName     ( "stTipoLote" );
$obRdbLoteManual->setLabel    ( "Manual" );
$obRdbLoteManual->setValue    ( "manual" );
$obRdbLoteManual->setNull     ( true );

//Lote Ambos
$obRdbLoteAmbos = new Radio;
$obRdbLoteAmbos->setRotulo   ( "Tipo de Lote" );
$obRdbLoteAmbos->setTitle    ( "Tipo de lote." );
$obRdbLoteAmbos->setName     ( "stTipoLote" );
$obRdbLoteAmbos->setLabel    ( "Ambos" );
$obRdbLoteAmbos->setValue    ( "ambos" );
$obRdbLoteAmbos->setNull     ( true );

$obBscContribuinte = new BuscaInner;
$obBscContribuinte->setId                ( "stContribuinte"          );
$obBscContribuinte->setRotulo        ( "Usuário"            );
$obBscContribuinte->setTitle            ( "Código do usuário."  );
$obBscContribuinte->setNull             ( true                     );
$obBscContribuinte->obCampoCod->setName         ("inCodContribuinte"    );
if (isset($inCodContribuinte)) {
    $obBscContribuinte->obCampoCod->setValue        ( $inCodContribuinte    );
}
$obBscContribuinte->obCampoCod->obEvento->setOnChange("buscaValor('buscaContribuinte');");
$obBscContribuinte->obCampoCod->obEvento->setOnKeyPress("iniciaAutoComplete(event,this);");
$obBscContribuinte->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinte','stContribuinte','','".Sessao::getId()."','800','450');" );

$obBscLoteInicio = new TextBox;
$obBscLoteInicio->setRotulo           ( "Lote"                            );
$obBscLoteInicio->setTitle               ( "Intervalo" );
$obBscLoteInicio->setName            ( "inCodLoteInicio"                       );
if (isset($inCodLoteInicio)) {
    $obBscLoteInicio->setValue            ( $inCodLoteInicio                        );
}
$obBscLoteInicio->setSize               ( 10                                 );
$obBscLoteInicio->setMaxLength    ( 10                                  );
$obBscLoteInicio->setNull               ( true                              );
$obBscLoteInicio->setInteiro           ( true                               );

$obBscLoteFinal = new TextBox;
$obBscLoteFinal->setRotulo           ( "Lote"                            );
$obBscLoteFinal->setTitle               ( "Intervalo" );
$obBscLoteFinal->setName            ( "inCodLoteFinal"                       );
if (isset($inCodLoteFinal)) {
    $obBscLoteFinal->setValue            ( $inCodLoteFinal                        );
}
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
if (isset($inNumBanco)) {
    $obTxtBanco->setValue         ( $inNumBanco                        );
}
$obTxtBanco->setSize          ( 10                                 );
$obTxtBanco->setMaxLength     ( 6                                  );
$obTxtBanco->setNull          ( true                              );
$obTxtBanco->setInteiro       ( true                               );
$obTxtBanco->obEvento->setOnChange ( "preencheAgencia('');" );

$obCmbBanco = new Select;
$obCmbBanco->setName          ( "cmbBanco"                   );
$obCmbBanco->addOption        ( "", "Selecione"              );
$obCmbBanco->setValue         ( $request->get('inNumBanco')  );
$obCmbBanco->setCampoId       ( "num_banco"                  );
$obCmbBanco->setCampoDesc     ( "nom_banco"                  );
$obCmbBanco->preencheCombo    ( $rsBanco                     );
$obCmbBanco->setNull          ( true                         );
$obCmbBanco->setStyle         ( "width: 220px"               );
$obCmbBanco->obEvento->setOnChange ( "preencheAgencia('');"  );

$obTxtAgencia = new TextBox;
$obTxtAgencia->setRotulo        ( "Agência"                                     );
$obTxtAgencia->setTitle         ( "Agência bancária na qual a conta foi aberta" );
$obTxtAgencia->setName          ( "inNumAgencia"                                );
if (isset($inNumAgencia)) {
    $obTxtAgencia->setValue         ( $inNumAgencia                             );
}
$obTxtAgencia->setSize          ( 10                                            );
$obTxtAgencia->setMaxLength     ( 6                                             );
$obTxtAgencia->setNull          ( true                                         );
$obTxtAgencia->setInteiro       ( true                                          );

$obCmbAgencia = new Select;
$obCmbAgencia->setName          ( "cmbAgencia"                   );
$obCmbAgencia->addOption       ( "", "Selecione"                );
$obCmbAgencia->setValue         ( $request->get('inNumAgencia')  );
$obCmbAgencia->setCampoId       ( "num_agencia"                  );
$obCmbAgencia->setCampoDesc     ( "nom_agencia"                  );
$obCmbAgencia->preencheCombo    ( $rsAgencia                     );
$obCmbAgencia->setNull           ( true                          );
$obCmbAgencia->setStyle         ( "width: 220px"                 );

//------------------------
$obDtInicio  = new Data;
$obDtInicio->setName             ( "dtInicio"                );
if (isset($dtInicio)) {
    $obDtInicio->setValue             ( $dtInicio                 );
}
$obDtInicio->setRotulo            ( "Data Inicial do Lote"        );
$obDtInicio->setTitle               ( "Intervalo inicial dos pagamentos por lote" );
$obDtInicio->setMaxLength     ( 20                                );
$obDtInicio->setSize                ( 10                                );
$obDtInicio->setNull                ( true                             );

$obLabelIntervalo = new Label;
$obLabelIntervalo->setValue ( "até" );

$obDtFinal  = new Data;
$obDtFinal->setName             ( "dtFinal"                );
if (isset($dtFinal)) {
    $obDtFinal->setValue             ( $dtFinal                );
}
$obDtFinal->setRotulo            ( "Data Final do Lote"        );
$obDtFinal->setTitle               ( "Intervalo final dos pagamentos por lote" );
$obDtFinal->setMaxLength     ( 20                                );
$obDtFinal->setSize                ( 10                                );
$obDtFinal->setNull                ( true                             );

//relatorio sintetico
$obRdbSintetico = new Radio;
$obRdbSintetico->setRotulo   ( "Tipo de Relatório" );
$obRdbSintetico->setTitle    ( "Tipo de relatório." );
$obRdbSintetico->setName     ( "stTipoRelatorio" );
$obRdbSintetico->setLabel    ( "Sintético" );
$obRdbSintetico->setValue    ( "sintetico" );
$obRdbSintetico->setChecked  ( true );
$obRdbSintetico->setNull     ( true );

//relatorio analitico
$obRdbAnalitico = new Radio;
$obRdbAnalitico->setRotulo   ( "Tipo de Relatório" );
$obRdbAnalitico->setTitle    ( "Tipo de relatório." );
$obRdbAnalitico->setName     ( "stTipoRelatorio" );
$obRdbAnalitico->setLabel    ( "Analítico" );
$obRdbAnalitico->setValue    ( "analitico" );
$obRdbAnalitico->setNull     ( true );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao          );
$obFormulario->addHidden( $obHdnCtrl            );
$obFormulario->addTitulo( "Dados para Filtro"   );
$obFormulario->agrupaComponentes ( array ( $obBscLoteInicio , $obLabelIntervalo, $obBscLoteFinal ) );
$obFormulario->agrupaComponentes ( array ( $obDtInicio, $obLabelIntervalo, $obDtFinal ) );
$obFormulario->agrupaComponentes ( array ( $obRdbLoteAutomatico, $obRdbLoteManual, $obRdbLoteAmbos ) );
$obFormulario->addComponenteComposto    ($obTxtBanco,$obCmbBanco      );
$obFormulario->addComponenteComposto    ($obTxtAgencia,$obCmbAgencia  );
$obFormulario->addComponente( $obTxtExercicio          );
$obFormulario->addComponente( $obBscContribuinte   );
$obFormulario->agrupaComponentes ( array ( $obRdbSintetico, $obRdbAnalitico ) );

$obFormulario->ok();
$obFormulario->show();

?>
