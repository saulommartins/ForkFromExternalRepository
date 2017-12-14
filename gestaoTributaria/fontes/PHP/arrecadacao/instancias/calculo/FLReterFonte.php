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
    * Página de Formulario de Filtro da Retencao de Fonte

    * Data de Criação   : 26/10/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FLReterFonte.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.03.22

*/

/*
$Log$
Revision 1.1  2006/10/30 13:00:16  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php" );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//RARREscriturarReceita
//Define o nome dos arquivos PHP
$stPrograma    = "ReterFonte";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::write( "link", "" );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$stCtrl = "";
$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo         ( "Retentor de Serviço" );
$obBscCGM->setTitle          ( "Informe o CGM do retentor." );
$obBscCGM->setId             ( "stCGM" );
$obBscCGM->obCampoCod->setName       ( "inCGM" );
$obBscCGM->obCampoCod->obEvento->setOnChange( "buscaValor('PreencheCGM');" );
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGM','stCGM','','".Sessao::getId()."','800','450');" );

//inscricao economica
$obIPopUpEmpresa = new IPopUpEmpresa;
$obIPopUpEmpresa->obInnerEmpresa->setNull ( true );
$obIPopUpEmpresa->obInnerEmpresa->setTitle ( "Informe o código da inscrição econômica de empresa." );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.03.22" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ( "Dados para Filtro" );
$obFormulario->addComponente ( $obBscCGM );
$obIPopUpEmpresa->geraFormulario ( $obFormulario );

$obFormulario->Ok ();
$obFormulario->show();
