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
    * Página de Formulario de Filtro para relatorio de Divida Ativa

    * Data de Criação   : 19/04/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FLRelatorioDivida.php 59979 2014-09-24 16:41:13Z michel $

    *Casos de uso: uc-05.04.10

*/

/*
$Log$
Revision 1.1  2007/04/19 16:06:56  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresaIntervalo.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovelIntervalo.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpDividaIntervalo.class.php" );
include_once ( CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php" );

if ( empty( $_REQUEST['stAcao'] ) || $_REQUEST['stAcao'] == "incluir" ) {
    $_REQUEST['stAcao'] = "inscrever";
}

//Define o nome dos arquivos PHP
$stPrograma    = "RelatorioDivida";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove('link');
Sessao::remove('stLink');
Sessao::remove( "arListaGrupoCredito" );
Sessao::remove( "arListaCredito" );

//DEFINICAO DOS COMPONENTES
$obRdbGrupo = new Radio;
$obRdbGrupo->setRotulo     ( "Filtrar por"                                                            );
$obRdbGrupo->setName       ( "stRdbGrupo"                                                                 );
$obRdbGrupo->setLabel      ( "Grupo de Créditos"                                                      );
$obRdbGrupo->setValue      ( "grupo"                                                                  );
$obRdbGrupo->setNull       ( false                                                                    );
$obRdbGrupo->setChecked    ( false                                                                    );
$obRdbGrupo->obEvento->setOnClick ( "montaParametrosGET('montaGrupoCredito');" );

$obRdbCredito = new Radio;
$obRdbCredito->setTitle    ( "Informe o filtro a ser utilizado." );
$obRdbCredito->setRotulo   ( "Filtrar por"                                                       	 );
$obRdbCredito->setName     ( "stRdbGrupo"                                                                );
$obRdbCredito->setLabel    ( "Crédito"                                                               );
$obRdbCredito->setValue    ( "credito"                                                               );
$obRdbCredito->setNull     ( false                                                                   );
$obRdbCredito->setChecked  ( false                                                                    );
$obRdbCredito->obEvento->setOnClick ( "montaParametrosGET('montaCredito');" );

$obSpnGrupoCredito = new Span;
$obSpnGrupoCredito->setID("spnGrupoCredito");

$obSpnListaGrupos = new Span;
$obSpnListaGrupos->setID("spnListaGrupos");

$obIPopUpEmpresa = new IPopUpEmpresaIntervalo;
$obIPopUpImovel	 = new IPopUpImovelIntervalo;
$obIPopUpDividaIntervalo	 = new IPopUpDividaIntervalo;

$obBscLogradouro = new BuscaInner;
$obBscLogradouro->setRotulo          ( "Logradouro"      );
$obBscLogradouro->setNull            ( true              );
$obBscLogradouro->setId              ( "campoInnerLogr" );
$obBscLogradouro->obCampoCod->setName( "inNumLogradouro" );
$obBscLogradouro->obCampoCod->obEvento->setOnChange("montaParametrosGET('buscaLogradouro');");
$obBscLogradouro->setFuncaoBusca( "abrePopUp('".CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm','inNumLogradouro','campoInnerLogr','' ,'".Sessao::getId()."','800','550')" );

$obTxtValorInicial = new Moeda;
$obTxtValorInicial->setName (   'flValorInicial'    );
$obTxtValorInicial->setTitle ( 'Informe o intervalo de valores a ser inscrito em dívida ativa.' );
$obTxtValorInicial->setNull (   true    );
$obTxtValorInicial->setRotulo     ( "Valor"     );

$obLblValor = new Label;
$obLblValor->setValue("até");

$obTxtValorFinal = new Moeda;
$obTxtValorFinal->setName   (   'flValorFinal'  );
$obTxtValorFinal->setTitle ( 'Informe o intervalo de valores a ser iscrito em dívida ativa.' );
$obTxtValorFinal->setNull   (   true    );
$obTxtValorFinal->setRotulo     ( "Valor"     );

$arValores = array ($obTxtValorInicial, $obLblValor, $obTxtValorFinal);

$obPeriodicidade= new Periodicidade();
$obPeriodicidade->setRotulo       ( "Periodicidade" );
$obPeriodicidade->setTitle          ( "Informe a Periodicidade" );
$obPeriodicidade->setExibeDia	( false );
$obPeriodicidade->setNull 		( true );
$obPeriodicidade->setValue        ( 4 );

//SITUACOES PO
$obRdSituacaoRemida = new Radio;
$obRdSituacaoRemida->setRotulo('*Situação');
$obRdSituacaoRemida->setName("inCodSituacao");
$obRdSituacaoRemida->setId("inCodSituacao");
$obRdSituacaoRemida->setLabel('Remida');
$obRdSituacaoRemida->setValue(1);
$obRdSituacaoRemida->obEvento->setOnClick("montaParametrosGET('montaCriterio', 'inCodSituacao')");

$obRdSituacaoSemCobranca = new Radio;
$obRdSituacaoSemCobranca->setName("inCodSituacao");
$obRdSituacaoSemCobranca->setId("inCodSituacao");
$obRdSituacaoSemCobranca->setLabel('Sem Cobrança');
$obRdSituacaoSemCobranca->setValue(2);
$obRdSituacaoSemCobranca->obEvento->setOnClick("montaParametrosGET('montaCriterio', 'inCodSituacao')");

$obRdSituacaoCCobranca = new Radio;
$obRdSituacaoCCobranca->setName("inCodSituacao");
$obRdSituacaoCCobranca->setId("inCodSituacao");
$obRdSituacaoCCobranca->setLabel('Com Cobrança');
$obRdSituacaoCCobranca->setValue(6);
$obRdSituacaoCCobranca->obEvento->setOnClick("montaParametrosGET('montaCriterio', 'inCodSituacao')");

$obSpnCriterio = new Span;
$obSpnCriterio->setId('spnCriterio');

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_DAT_INSTANCIAS."relatorios/OCMonta1RelatorioDivida.php" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( "OCMonta1RelatorioDivida.php"  );
$obForm->setTarget( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.10" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnCaminho );
$obFormulario->addTitulo     ("Dados para Filtro");

$obIPopUpDividaIntervalo->setVerifica( true );
$obIPopUpDividaIntervalo->obInnerDividaIntervalo->setNull( true );
$obIPopUpDividaIntervalo->geraFormulario ( $obFormulario );

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( true );
$obPopUpCGM->setRotulo ( "CGM" );
$obPopUpCGM->setTitle ( "Informe o número do CGM." );

$obFormulario->addComponente( $obPopUpCGM );
$obIPopUpImovel->geraFormulario ( $obFormulario );
$obIPopUpImovel->obInnerImovelIntervalo->obCampoCod->obEvento->setOnChange ( "" );
$obIPopUpImovel->obInnerImovelIntervalo->obCampoCod2->obEvento->setOnChange ( "" );
$obIPopUpEmpresa->geraFormulario ( $obFormulario );
$obIPopUpEmpresa->obInnerEmpresaIntervalo->obCampoCod->obEvento->setOnChange( "" );
$obIPopUpEmpresa->obInnerEmpresaIntervalo->obCampoCod2->obEvento->setOnChange( "" );

$obFormulario->addComponente( $obBscLogradouro );
$obFormulario->agrupaComponentes ( $arValores );
$obFormulario->addComponente($obPeriodicidade);
$obFormulario->agrupaComponentes (
    array(
        $obRdSituacaoRemida,
        $obRdSituacaoSemCobranca,
        $obRdSituacaoCCobranca,
    )
);

$obFormulario->addSpan($obSpnCriterio);
$obFormulario->agrupaComponentes     ( array( $obRdbCredito, $obRdbGrupo) );
$obFormulario->addSpan( $obSpnGrupoCredito );

$obFormulario->addSpan( $obSpnListaGrupos );

$obBtnOK = new Ok();
$obBtnOK->obEvento->setOnClick  ( "submeteFiltro();" );

$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick  ( "Limpar();" );

$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
//$obFormulario->Ok (true);
$obFormulario->show();

$stJs .= 'f.inCodInscricaoInicial.focus();';
sistemaLegado::executaFrameOculto ( $stJs );
