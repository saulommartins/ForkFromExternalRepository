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
    * Página de Formulario de Filtro para Inscrição de Dívida Ativa

    * Data de Criação   : 26/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FLManterInscricao.php 61352 2015-01-09 18:14:18Z evandro $

    *Casos de uso: uc-05.04.02

*/

/*
$Log$
Revision 1.6  2007/06/22 19:10:58  cercato
adicionando filtro para componente modalidade utilizar apenas modalidades de inscricao em divida ativa.

Revision 1.5  2007/03/01 14:12:35  cercato
Bug #8537#

Revision 1.4  2007/03/01 14:06:13  cercato
Bug #8538#

Revision 1.3  2006/10/09 09:07:25  dibueno
Controle da data de inscrição em Divida

Revision 1.2  2006/10/05 11:40:11  dibueno
*** empty log message ***

Revision 1.1  2006/10/02 09:16:13  dibueno
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php" );
include_once ( CAM_GT_MON_COMPONENTES."IPopUpCredito.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresaIntervalo.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovelIntervalo.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpModalidade.class.php" );

if ( empty( $_REQUEST['stAcao'] ) || $_REQUEST['stAcao'] == "incluir" ) {
    $_REQUEST['stAcao'] = "inscrever";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterInscricao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove('link');
Sessao::remove('stLink');

$obIPopUpGrupoCredito = new MontaGrupoCredito;
$obIPopUpGrupoCredito->setRotulo ( "Grupo de Crédito" );
$obIPopUpGrupoCredito->setTitulo ( "Informe o código do grupo de crédito." );

$obIPopUpCredito = new IPopUpCredito;
$obIPopUpCredito->setRotulo ( "Crédito" );
$obIPopUpCredito->setTitle  ( "Informe o código de crédito." );
$obIPopUpCredito->setNull   ( true );

$obTxtExercicio = new TextBox ;
$obTxtExercicio->setName      ( "stExercicio" );
$obTxtExercicio->setId        ( "stExercicio" );
$obTxtExercicio->setInteiro   ( true          );
$obTxtExercicio->setMaxLength ( 4             );
$obTxtExercicio->setSize      ( 4             );
$obTxtExercicio->setRotulo    ( "Exercício"   );
$obTxtExercicio->setTitle     ( "Exercício"   );
$obTxtExercicio->setNull      ( true          );

$obIPopUpEmpresa = new IPopUpEmpresaIntervalo;
$obIPopUpEmpresa->obInnerEmpresaIntervalo->setTitle( "Informe o código da Inscrição Econômica." );
$obIPopUpEmpresa->setVerificaInscricao(false);

$obIPopUpImovel = new IPopUpImovelIntervalo;
$obIPopUpImovel->obInnerImovelIntervalo->setTitle( "Informe o código da Inscrição Imobiliária." );
$obIPopUpImovel->setVerificaInscricao(false);

$obIPopUpModalidade = new IPopUpModalidade;
$obIPopUpModalidade->setTipoModalidade(1); //inscricao
$obIPopUpModalidade->obInnerModalidade->setTitle ( "Informe o código para a Modalidade" );
$obIPopUpModalidade->obInnerModalidade->setNull  ( false );

$dtDiaHoje = date ("d/m/Y");

$obHdnDataHoje = new Hidden;
$obHdnDataHoje->setName ( "dtHoje" );
$obHdnDataHoje->setValue( $dtDiaHoje );

$obDtInscricao = new Data;
$obDtInscricao->setName               ( "dtInscricao" );
$obDtInscricao->setId                 ( "dtInscricao" );
$obDtInscricao->setTitle              ( "Informe a data de inscrição em dívida ativa" );
$obDtInscricao->setRotulo             ( "Data de Inscrição" );
$obDtInscricao->setValue              ( $dtDiaHoje );
$obDtInscricao->setNull               ( false );
$obDtInscricao->obEvento->setOnChange ( "validaData( this.value, '".$dtDiaHoje."' )" );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

//DEFINICAO DO PERIODO
$obPeriodo = new Periodo;
$obPeriodo->setRotulo ("Período");
$obPeriodo->setTitle  ("Informe o período a ser inscrito em dívida ativa DD/MM/AAAA.");

$obTxtValorInicial = new Moeda;
$obTxtValorInicial->setName   ( 'flValorInicial' );
$obTxtValorInicial->setTitle  ( 'Informe o intervalo de valores a ser inscrito em dívida ativa.' );
$obTxtValorInicial->setNull   ( true );
$obTxtValorInicial->setRotulo ( "Valor" );

$obLblValor = new Label;
$obLblValor->setValue("até");

$obTxtValorFinal = new Moeda;
$obTxtValorFinal->setName   (	'flValorFinal'	);
$obTxtValorFinal->setTitle  ( 'Informe o intervalo de valores a ser iscrito em dívida ativa.' );
$obTxtValorFinal->setNull	(	true	);
$obTxtValorFinal->setRotulo ( "Valor"     );

$arValores = array ($obTxtValorInicial, $obLblValor, $obTxtValorFinal);

$obBscContribuinte = new BuscaInnerIntervalo;
$obBscContribuinte->setRotulo                   ( "Contribuinte"    );
$obBscContribuinte->obLabelIntervalo->setValue  ( "até"          );
$obBscContribuinte->obCampoCod->setName         ("inCGMInicial"  );
$obBscContribuinte->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGMInicial','stNaoExiste','','".Sessao::getId()."','800','450');" ));
$obBscContribuinte->obCampoCod2->setName        ("inCGMFinal"  );
$obBscContribuinte->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGMFinal','stNaoExiste','','".Sessao::getId()."','800','450');" ));

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );
$obForm->setTarget ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->setAjuda  ( "UC-05.04.02" );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnDataHoje );
$obFormulario->addTitulo ("Dados para Filtro");

$obIPopUpGrupoCredito->geraFormulario( $obFormulario, true, true );
$obIPopUpCredito->geraFormulario     ( $obFormulario             );
$obFormulario->addComponente         ( $obTxtExercicio           );
$obFormulario->addComponente         ( $obBscContribuinte        );
$obIPopUpEmpresa->geraFormulario     ( $obFormulario             );
$obIPopUpImovel->geraFormulario      ( $obFormulario             );
$obFormulario->addComponente         ( $obPeriodo                );
$obFormulario->agrupaComponentes     ( $arValores                );
$obFormulario->addTitulo             ( "Dados para Inscrição"    );
$obIPopUpModalidade->geraFormulario  ( $obFormulario             );
$obFormulario->addComponente         ( $obDtInscricao            );

$obBtnOK = new Ok;
$obBtnOK->obEvento->setOnClick( "submeteFiltro()" );

$onBtnLimpar = new Limpar;

$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>