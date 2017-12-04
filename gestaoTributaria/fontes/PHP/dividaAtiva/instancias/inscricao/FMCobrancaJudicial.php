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
    * Página de Formulario de Filtro para Abertura de Cobranca Judicial

    * Data de Criação   : 11/09/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FMCobrancaJudicial.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.02

*/

/*
$Log$
Revision 1.1  2007/09/11 20:44:13  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpDivida.class.php" );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "cobrar";
}

//Define o nome dos arquivos PHP
$stPrograma    = "CobrancaJudicial";
$pgList        = "LS".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();

Sessao::remove('link');
Sessao::remove('stLink');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

//inscricao imobiliaria
$obIPopUpImovel = new IPopUpImovel;
$obIPopUpImovel->obInnerImovel->setNull ( true );
$obIPopUpImovel->obInnerImovel->setTitle ( "Informe o código da inscrição imobiliária." );

//inscricao economica
$obIPopUpEmpresa = new IPopUpEmpresa;
$obIPopUpEmpresa->obInnerEmpresa->setNull ( true );
$obIPopUpEmpresa->obInnerEmpresa->setTitle ( "Informe o código da inscrição econômica." );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo         ( "CGM" );
$obBscCGM->setTitle          ( "Informe o código do CGM.");
$obBscCGM->setId             ( "stCGM" );
$obBscCGM->obCampoCod->setName       ("inCGM"  );
$obBscCGM->obCampoCod->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."&inCGM='+this.value,'PreencheCGM');");
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGM','stCGM','','".Sessao::getId()."','800','450');" );

//inscricao divida
$obIPopUpDivida = new IPopUpDivida;
$obIPopUpDivida->obInnerDivida->setNull( true );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.02" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ( "Dados para Filtro" );
$obFormulario->addComponente ( $obBscCGM );
$obIPopUpImovel->geraFormulario ( $obFormulario );
$obIPopUpEmpresa->geraFormulario ( $obFormulario );
$obIPopUpDivida->geraFormulario ( $obFormulario );

$obFormulario->Ok ();
$obFormulario->show();
