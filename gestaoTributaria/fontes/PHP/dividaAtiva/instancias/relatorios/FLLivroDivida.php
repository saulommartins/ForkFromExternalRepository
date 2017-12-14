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

    * Página de Formulario de Filtro para relatorio de Divida Ativa

    * Data de Criação   : 16/10/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Vitor Hugo
    * @ignore

    * $Id: FLLivroDivida.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.10

*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresaIntervalo.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovelIntervalo.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpDividaIntervalo.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpLivroIntervalo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "LivroDivida";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$obIPopUpEmpresa         = new IPopUpEmpresaIntervalo;
$obIPopUpImovel	         = new IPopUpImovelIntervalo;
$obIPopUpDividaIntervalo = new IPopUpDividaIntervalo;
$obIPopUpLivroIntervalo  = new IPopUpLivroIntervalo;

$obIPopUpEmpresa = new IPopUpEmpresaIntervalo;
$obIPopUpImovel	 = new IPopUpImovelIntervalo;

$obInnerFolhas = new BuscaInnerIntervalo;
$obInnerFolhas->setNull                    ( true );
$obInnerFolhas->setTitle                   ( "Busca Folhas" );
$obInnerFolhas->setRotulo                  ( "Folha" );
$obInnerFolhas->obLabelIntervalo->setValue ( "até" );
$obInnerFolhas->obCampoCod->setName        ( "inFolhaInicial" );
$obInnerFolhas->obCampoCod->setInteiro     ( false );
$obInnerFolhas->obCampoCod2->setName       ( "inFolhaFinal" );
$obInnerFolhas->obCampoCod2->setInteiro    ( false );
$obInnerFolhas->setFuncaoBusca ( "abrePopUp('".CAM_GT_DAT_POPUPS."inscricao/FLProcurarFolha.php','frm','".$obInnerFolhas->obCampoCod->stName ."','','todos','".Sessao::getId() ."','800','550');" );
$obInnerFolhas->setFuncaoBusca2 ( "abrePopUp('" . CAM_GT_DAT_POPUPS."inscricao/FLProcurarFolha.php','frm', '".$obInnerFolhas->obCampoCod2->stName."','','todos','".Sessao::getId()."','800','550');" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( 'OCMontaLivroDivida.php' );
//$obForm->setTarget( "telaPrincipal" );
$obForm->setTarget( 'oculto' );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ("Dados para Filtro");

$obIPopUpDividaIntervalo->obInnerDividaIntervalo->setNull(true);
$obIPopUpDividaIntervalo->setVerifica( false );
$obIPopUpDividaIntervalo->geraFormulario( $obFormulario );

$obIPopUpLivroIntervalo->obInnerLivroIntervalo->setNull(true);
$obIPopUpLivroIntervalo->geraFormulario( $obFormulario);

$obFormulario->addComponente( $obInnerFolhas );

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( true );
$obPopUpCGM->setRotulo ( "CGM" );
$obPopUpCGM->setTitle ( "Informe o número do CGM." );

$obFormulario->addComponente( $obPopUpCGM );

$obIPopUpImovel->geraFormulario ( $obFormulario );
$obIPopUpEmpresa->geraFormulario ( $obFormulario );

$obFormulario->Ok ();
$obFormulario->show();

$stJs .= 'f.inCodInscricao.focus();';
sistemaLegado::executaFrameOculto ( $stJs );
