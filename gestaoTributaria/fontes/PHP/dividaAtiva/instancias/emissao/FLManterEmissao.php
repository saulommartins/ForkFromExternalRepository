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
    * Página de Formulario de Filtro para Emissao

    * Data de Criação   : 26/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FLManterEmissao.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.03

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpDocumento.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpTipoDocumentoNumeracao.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresaIntervalo.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovelIntervalo.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpDividaIntervalo.class.php" );

$stAcao = $request->get('stAcao') ? $request->get('stAcao') : 'alterar';

unset($_SESSION['inCodInscricaoInicial']);
unset($_SESSION['inCodInscricaoFinal']);

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEmissao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma."Combo.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );
Sessao::remove('link'  );
Sessao::remove('stLink');

$obIPopUpEmpresa = new IPopUpEmpresaIntervalo;
$obIPopUpEmpresa->obInnerEmpresaIntervalo->obCampoCod->setId("inNumInscricaoEconomicaInicial"  );
$obIPopUpEmpresa->obInnerEmpresaIntervalo->obCampoCod2->setId("inNumInscricaoEconomicaFinal"  );

$obIPopUpImovel  = new IPopUpImovelIntervalo;
$obIPopUpImovel->obInnerImovelIntervalo->obCampoCod->setId ( "inCodImovelInicial" );
$obIPopUpImovel->obInnerImovelIntervalo->obCampoCod2->setId ( "inCodImovelFinal" );

$obIPopUpDividaIntervalo = new IPopUpDividaIntervalo;
$obIPopUpDividaIntervalo->obInnerDividaIntervalo->obCampoCod->setId ( "inCodInscricaoInicial" );
$obIPopUpDividaIntervalo->obInnerDividaIntervalo->obCampoCod2->setId ( "inCodInscricaoFinal" );
$obIPopUpDividaIntervalo->obInnerDividaIntervalo->setNull( true );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $request->get('stCtrl') );

//DEFINICAO DOS COMPONENTES
$obHdnstNome =  new Hidden;
$obHdnstNome->setName   ( "stNome" );

//tipo (emissao)
$obRdbEmissao = new Radio;
$obRdbEmissao->setRotulo   ( "Tipo" );
$obRdbEmissao->setName     ( "stTipoModalidade" );
$obRdbEmissao->setId       ( "stTipoModalidadeEmissao" );
$obRdbEmissao->setLabel    ( "Emissão" );
$obRdbEmissao->setTitle    ( "Selecione o tipo de emissão do documento." );
$obRdbEmissao->setValue    ( "emissao" );
$obRdbEmissao->setNull     ( true );
$obRdbEmissao->setChecked  ( true );
$obRdbEmissao->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".$pgOcul."?".Sessao::getId()."&stTipoModalidade='+this.value, 'tipoEmissao' );" );

//tipo (reemissao)
$obRdbReemissao = new Radio;
$obRdbReemissao->setRotulo   ( "Tipo" );
$obRdbReemissao->setName     ( "stTipoModalidade" );
$obRdbReemissao->setId       ( "stTipoModalidadeReemissao" );
$obRdbReemissao->setLabel    ( "Reemissão" );
$obRdbReemissao->setTitle    ( "Selecione o tipo de emissão do documento." );
$obRdbReemissao->setValue    ( "reemissao" );
$obRdbReemissao->setNull     ( true );
$obRdbReemissao->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".$pgOcul."?".Sessao::getId()."&stTipoModalidade='+this.value, 'tipoEmissao' );" );

//tipo documento e numeração
$obIPopUpTipoDocumentoNumeracao = new IPopUpTipoDocumentoNumeracao;
$obIPopUpTipoDocumentoNumeracao->obCmbTipoDocumento->setNull(true);
$obIPopUpTipoDocumentoNumeracao->obInnerDocumento->setNull( true );

$obBscContribuinte = new BuscaInnerIntervalo;
$obBscContribuinte->setRotulo           ( "Contribuinte"    );
$obBscContribuinte->obLabelIntervalo->setValue ( "até"          );
$obBscContribuinte->obCampoCod->setName     ("inCodContribuinteInicial"  );
$obBscContribuinte->obCampoCod->setId     ("inCodContribuinteInicial"  );
$obBscContribuinte->obCampoCod->obEvento->setOnChange("buscaValor('buscaContribuinteInicio');");
$obBscContribuinte->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteInicial','stNome','','".Sessao::getId()."','800','450');" ));
$obBscContribuinte->obCampoCod2->setName        ("inCodContribuinteFinal"  );
$obBscContribuinte->obCampoCod2->setId        ("inCodContribuinteFinal"  );
$obBscContribuinte->obCampoCod2->obEvento->setOnChange("buscaValor('buscaContribuinteFinal');");
$obBscContribuinte->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteFinal','stNome','','".Sessao::getId()."','800','450');" ));

$obSpnReemissao = new Span;
$obSpnReemissao->setID("spnReemissao");

//DEFINICAO DOS BOTOES
$obOk = new Ok();
$obOk->obEvento->setOnClick('verificaCamposFiltro();'); 
$obLimpar = new Limpar();

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.03" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnstNome );
$obFormulario->addTitulo     ( "Dados para Filtro" );
$obFormulario->agrupaComponentes ( array( $obRdbEmissao, $obRdbReemissao ) );
$obIPopUpTipoDocumentoNumeracao->geraFormulario ( $obFormulario );
$obFormulario->addComponente( $obBscContribuinte );
$obIPopUpImovel->geraFormulario ( $obFormulario );
$obIPopUpEmpresa->geraFormulario ( $obFormulario );
$obIPopUpDividaIntervalo->geraFormulario ( $obFormulario );
$obFormulario->addSpan ( $obSpnReemissao );
$obFormulario->defineBarra( array( $obOk, $obLimpar ) );
$obFormulario->show();
