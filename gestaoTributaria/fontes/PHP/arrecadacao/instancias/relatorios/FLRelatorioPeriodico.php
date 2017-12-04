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
    * Página de Filtro para Relatório da Arrecadação Periódico
    * Data de Criação   : 22/05/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: FLRelatorioPeriodico.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.13
*/

/*
$Log$
Revision 1.1  2007/05/23 19:34:52  dibueno
Bug #9279#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRParametroCalculo.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioPeriodico";
$pgFilt     = "FL".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

include_once( $pgJS );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

// pegar mascara de credito
$obRARRParametroCalculo = new RARRParametroCalculo;
$obRARRParametroCalculo->obRARRGrupo->obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRARRParametroCalculo->obRARRGrupo->obRMONCredito->getMascaraCredito();

//mascara grupo de credito
$obRARRGrupo = new RARRGrupo;
$stMascaraGrupoCredito = "";
$obRARRGrupo->RecuperaMascaraGrupoCredito( $stMascaraGrupoCredito );
$stMascaraGrupoCredito .= "/9999";

//mascara insc econ
$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
$obTAdministracaoConfiguracao->setDado( "cod_modulo", 14 );
$obTAdministracaoConfiguracao->setDado( "exercicio" , Sessao::getExercicio() );
$obTAdministracaoConfiguracao->setDado( "parametro" , "mascara_inscricao_economica");
$obTAdministracaoConfiguracao->recuperaPorChave( $rsMascaraInscEco );
if ( !$rsMascaraInscEco->Eof() ) {
    $stMascaraInscricaoEconomica = $rsMascaraInscEco->getCampo( "valor" ) ;
}else
    $stMascaraInscricaoEconomica = "99";

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_ARR_INSTANCIAS."relatorios/OCGeraRelatorioPeriodicoConsulta.php" );

$obHdnNomeGrupo = new Hidden;
$obHdnNomeGrupo->setName    ( "stNomeGrupo" );
$obHdnNomeGrupo->setId      ("stNomeGrupo");

$obHdnCodigoCredito = new Hidden;
$obHdnCodigoCredito->setName    ('inCodigoCredito');
$obHdnCodigoCredito->setId          ('inCodigoCredito');
$obHdnCodigoCredito->setValue    ( $inCodigoCredito );

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick( "submeteFiltro()" );

$onBtnLimpar = new Limpar;

$obSpnFiltro = new Span;
$obSpnFiltro->setId ( 'spnFiltro' );

$obDtInicio  = new Data;
$obDtInicio->setName        ( "dtInicio"                    );
$obDtInicio->setValue       ( $dtInicio                     );
$obDtInicio->setRotulo      ( "Data Inicial do Intervalo"   );
$obDtInicio->setTitle       ( "Intervalo inicial"           );
$obDtInicio->setMaxLength   ( 20                            );
$obDtInicio->setSize        ( 10                            );
$obDtInicio->setNull        ( true                          );

$obLabelIntervalo = new Label;
$obLabelIntervalo->setValue ( "até" );

$obDtFinal  = new Data;
$obDtFinal->setName         ( "dtFinal"                     );
$obDtFinal->setValue        ( $dtFinal                      );
$obDtFinal->setRotulo       ( "Data Final do Lote"          );
$obDtFinal->setTitle        ( "Intervalo fina"              );
$obDtFinal->setMaxLength    ( 20                            );
$obDtFinal->setSize         ( 10                            );
$obDtFinal->setNull         ( true                          );

$obBscGrupoCredito = new BuscaInnerIntervalo;
$obBscGrupoCredito->setRotulo           ( "Grupo de Crédito"    );
$obBscGrupoCredito->setTitle            ( "Informe o intervalo de Grupo de Crédito");
$obBscGrupoCredito->obLabelIntervalo->setValue ( "e"          );
$obBscGrupoCredito->obCampoCod->setName     ("inCodGrupoInicio"  );
$obBscGrupoCredito->obCampoCod->setMascara ( $stMascaraGrupoCredito );
$obBscGrupoCredito->obCampoCod->setMaxLength ( strlen($stMascaraGrupoCredito) );
$obBscGrupoCredito->obCampoCod->setMinLength ( strlen($stMascaraGrupoCredito) );
$obBscGrupoCredito->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inCodGrupoInicio','stNomeGrupo','','".Sessao::getId()."','800','450');" ));
$obBscGrupoCredito->obCampoCod2->setName        ("inCodGrupoTermino"  );
$obBscGrupoCredito->obCampoCod2->setMascara ( $stMascaraGrupoCredito );
$obBscGrupoCredito->obCampoCod2->setMaxLength ( strlen($stMascaraGrupoCredito) );
$obBscGrupoCredito->obCampoCod2->setMinLength ( strlen($stMascaraGrupoCredito) );
$obBscGrupoCredito->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inCodGrupoTermino','stNomeGrupo','','".Sessao::getId()."','800','450');" ));

$obBscCredito = new BuscaInnerIntervalo;
$obBscCredito->setRotulo           ( "Crédito" );
$obBscCredito->setTitle            ( "Informe o intervalo de Crédito");
$obBscCredito->obLabelIntervalo->setValue ( "e"          );
$obBscCredito->obCampoCod->setName     ("inCodCreditoInicio"  );
$obBscCredito->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCreditoInicio','stNomeGrupo','','".Sessao::getId()."','800','450');" ));
$obBscCredito->obCampoCod2->setName        ("inCodCreditoTermino"  );
$obBscCredito->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCreditoTermino','stNomeGrupo','','".Sessao::getId()."','800','450');" ));
$obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod->setMascara   ( $stMascaraCredito         );
$obBscCredito->obCampoCod2->setMaxLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod2->setMinLength ( strlen($stMascaraCredito) );
$obBscCredito->obCampoCod2->setMascara   ( $stMascaraCredito         );

$obNumCGMInicio = new TextBox;
$obNumCGMInicio->setName          ( "inNumCGMInicio"        );
$obNumCGMInicio->setRotulo         ( "Contribuinte"   );
$obNumCGMInicio->setTitle             ( "Informe o Número de CGM inicial" ) ;
$obNumCGMInicio->setInteiro          ( true                );

$obNumCGMTermino = new TextBox;
$obNumCGMTermino->setName       ( "inNumCGMTermino"       );
$obNumCGMTermino->setRotulo      ( "Contribuinte"   );
$obNumCGMTermino->setTitle          ( "Informe o Número de CGM final" );
$obNumCGMTermino->setInteiro       ( true                 );

$obBscContribuinte = new BuscaInnerIntervalo;
$obBscContribuinte->setRotulo           ( "Contribuinte"    );
$obBscContribuinte->obLabelIntervalo->setValue ( "até"          );
$obBscContribuinte->obCampoCod->setName     ("inCodContribuinteInicial"  );
$obBscContribuinte->obCampoCod->setValue        ( $inCodContribuinteInicio  );
$obBscContribuinte->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteInicial','stNomeGrupo','','".Sessao::getId()."','800','450');" ));
$obBscContribuinte->obCampoCod2->setName        ("inCodContribuinteFinal"  );
$obBscContribuinte->obCampoCod2->setValue       ( $inCodContribuinteFinal  );
$obBscContribuinte->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteFinal','stNomeGrupo','','".Sessao::getId()."','800','450');" ));

$obBscInscricaoImobiliaria = new BuscaInnerIntervalo;
$obBscInscricaoImobiliaria->setRotulo           ( "Inscrição Imobiliária"   );
$obBscInscricaoImobiliaria->obLabelIntervalo->setValue ( "até"          );
$obBscInscricaoImobiliaria->obCampoCod->setName     ("inNumInscricaoImobiliariaInicial"  );
$obBscInscricaoImobiliaria->obCampoCod->setValue        ( $inNumInscricaoImobiliariaInicial  );
$obBscInscricaoImobiliaria->setFuncaoBusca      ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaInicial','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));
$obBscInscricaoImobiliaria->obCampoCod2->setName        ( "inNumInscricaoImobiliariaFinal" );
$obBscInscricaoImobiliaria->obCampoCod2->setValue       ( $inNumInscricaoImobiliariaFinal  );     //$obBscInscricaoImobiliaria->obCampoCod2->obEvento->setOnChange("buscaValor('buscaIImobiliariaFinal');");
$obBscInscricaoImobiliaria->setFuncaoBusca2     ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaFinal','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));

$obBscInscricaoEconomica = new BuscaInnerIntervalo;
$obBscInscricaoEconomica->setRotulo         ( "Inscrição Econômica"    );
$obBscInscricaoEconomica->obLabelIntervalo->setValue ( "até"            );
$obBscInscricaoEconomica->obCampoCod->setName       ("inNumInscricaoEconomicaInicial"  );
$obBscInscricaoEconomica->obCampoCod->setValue      ( $inNumInscricaoEconomicaInicial  );
$obBscInscricaoEconomica->obCampoCod->setSize     ( strlen( $stMascaraInscricaoEconomica ) );
$obBscInscricaoEconomica->obCampoCod->setMaxLength( strlen( $stMascaraInscricaoEconomica ) );
$obBscInscricaoEconomica->obCampoCod->setMascara  ( $stMascaraInscricaoEconomica   );
$obBscInscricaoEconomica->setFuncaoBusca("abrePopUp(&quot;".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php&quot;,&quot;frm&quot;,&quot;inNumInscricaoEconomicaInicial&quot;,&quot;stNomeGrupo&quot;,&quot;todos&quot;,&quot;".Sessao::getId()."&quot;,&quot;800&quot;,&quot;550&quot;);");
$obBscInscricaoEconomica->obCampoCod2->setName          ( "inNumInscricaoEconomicaFinal" );
$obBscInscricaoEconomica->obCampoCod2->setValue         ( $inNumInscricaoEconomicaFinal  );
$obBscInscricaoEconomica->obCampoCod2->setSize     ( strlen( $stMascaraInscricaoEconomica ) );
$obBscInscricaoEconomica->obCampoCod2->setMaxLength( strlen( $stMascaraInscricaoEconomica ) );
$obBscInscricaoEconomica->obCampoCod2->setMascara  ( $stMascaraInscricaoEconomica   );
//$obBscInscricaoEconomica->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inNumInscricaoEconomicaInicial','stNomeGrupo','todos','".Sessao::getId()."','800','550');"));
$obBscInscricaoEconomica->setFuncaoBusca2("abrePopUp(&quot;".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php&quot;,&quot;frm&quot;,&quot;inNumInscricaoEconomicaFinal&quot;,&quot;stNomeGrupo&quot;,&quot;todos&quot;,&quot;".Sessao::getId()."&quot;,&quot;800&quot;,&quot;550&quot;);");

$obTCEMAtividade = new TCEMAtividade;
$obTCEMAtividade->recuperaMaxCodEstrutural( $rsListaEstrutura );

$obRCEMAtividade = new RCEMAtividade;
$obRCEMAtividade->recuperaVigenciaAtual( $rsVigenciaAtual );

Sessao::write( "CodigoVigencia", $rsVigenciaAtual->getCampo( "cod_vigencia" ) );

$arEstrutura = explode( ".", $rsListaEstrutura->getCampo("cod_estrutural") );
$stEstrutura = "";
for ( $inX=0; $inX<count($arEstrutura); $inX++ ) {
    if ( $inX )
        $stEstrutura .= ".";

    for ( $inY=0; $inY<strlen($arEstrutura[$inX]); $inY++ ) {
        $stEstrutura .= "9";
    }
}

$obHdnCampoInner = new Hidden;
$obHdnCampoInner->setName ( "campoInner" );
$obHdnCampoInner->setID ( "campoInner" );

$obHdnInCodigoAtividade = new Hidden;
$obHdnInCodigoAtividade->setName ( "inCodigoAtividade" );
$obHdnInCodigoAtividade->setID ( "inCodigoAtividade" );

$obBscAtividade = new BuscaInnerIntervalo;
$obBscAtividade->setRotulo ( "Atividade" );
$obBscAtividade->setTitle ( "Atividade Econômica" );
$obBscAtividade->setNull ( true );
$obBscAtividade->obLabelIntervalo->setValue ( "até" );
$obBscAtividade->obCampoCod->setName ( "inCodInicio" );
$obBscAtividade->obCampoCod->setInteiro ( false );
$obBscAtividade->obCampoCod->setMascara ( $stEstrutura );
$stBusca  = "abrePopUp('".CAM_GT_CEM_POPUPS."atividadeeconomica/FLProcurarAtividade.php','frm','inCodInicio','campoInner',''";
$stBusca .= " ,'".Sessao::getId()."&campoFoco=inCodInicio','800','550')";
$obBscAtividade->setFuncaoBusca ( $stBusca );
$obBscAtividade->obCampoCod2->setName ( "inCodTermino" );
$obBscAtividade->obCampoCod2->setInteiro ( false );
$obBscAtividade->obCampoCod2->setMascara ( $stEstrutura );
$stBusca  = "abrePopUp('".CAM_GT_CEM_POPUPS."atividadeeconomica/FLProcurarAtividade.php','frm','inCodTermino','campoInner',''";
$stBusca .= " ,'".Sessao::getId()."&campoFoco=inCodTermino','800','550')";
$obBscAtividade->setFuncaoBusca2 ( $stBusca );

$obRdbRelatorioSintetico = new Radio;
$obRdbRelatorioSintetico->setRotulo   ( "Tipo de Relatório" );
$obRdbRelatorioSintetico->setTitle    ( "Informe o tipo de relatório." );
$obRdbRelatorioSintetico->setName     ( "stTipoRelatorio" );
$obRdbRelatorioSintetico->setLabel    ( "Sintético" );
$obRdbRelatorioSintetico->setValue    ( "sintetico" );
$obRdbRelatorioSintetico->setNull     ( false );
$obRdbRelatorioSintetico->setChecked  ( true );

$obRdbRelatorioAnalitico = new Radio;
$obRdbRelatorioAnalitico->setRotulo   ( "Tipo de Relatório" );
$obRdbRelatorioAnalitico->setTitle    ( "Informe o tipo de relatório." );
$obRdbRelatorioAnalitico->setName     ( "stTipoRelatorio" );
$obRdbRelatorioAnalitico->setLabel    ( "Analítico" );
$obRdbRelatorioAnalitico->setValue    ( "analitico" );
$obRdbRelatorioAnalitico->setNull     ( false );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );

$obFormulario->addHidden    ( $obHdnNomeGrupo );
$obFormulario->addHidden    ( $obHdnCaminho );
$obFormulario->addHidden    ( $obHdnAcao            );
$obFormulario->addHidden    ( $obHdnCtrl            );
$obFormulario->addHidden    ( $obHdnCodigoCredito );
$obFormulario->addHidden    ( $obHdnCampoInner );
$obFormulario->addHidden    ( $obHdnInCodigoAtividade );
$obFormulario->addTitulo    ( "Dados para filtro"   );

$obFormulario->agrupaComponentes ( array ( $obDtInicio, $obLabelIntervalo, $obDtFinal ) );
$obFormulario->addComponente ( $obBscCredito );
$obFormulario->addComponente ( $obBscGrupoCredito );
$obFormulario->addComponente ( $obBscInscricaoImobiliaria );
$obFormulario->addComponente ( $obBscInscricaoEconomica );
$obFormulario->addComponente ( $obBscContribuinte );
//$obFormulario->agrupaComponentes( array( $obCodInicio, $obLblPeriodo, $obCodTermino ) );
$obFormulario->addComponente ( $obBscAtividade );
$obFormulario->addComponenteComposto ( $obRdbRelatorioSintetico, $obRdbRelatorioAnalitico );
#$obFormulario->addSpan       ( $obSpnFiltro );
$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );

$obFormulario->show();

?>
