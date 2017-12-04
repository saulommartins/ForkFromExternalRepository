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
    * Filtro para Economico >> Responsavel Tecnico
    * Data de Criação   : 18/04/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: FLProcurarRespTecnico.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.2  2006/09/15 13:50:33  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMResponsavelTecnico.class.php"     );
include_once ( CAM_GA_CSE_NEGOCIO."RProfissao.class.php"                 );
include_once ( CAM_GA_CSE_NEGOCIO."RConselho.class.php"                  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"           );
include_once ( CAM_GA_ADM_NEGOCIO."RAdministracaoUF.class.php"                        );

//Define o nome dos arquivos PHP
$stPrograma    = "ProcurarRespTecnico";
$pgList        = "LS".$stPrograma.".php";

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "link", "" );

// HIDDENS
$obHdnAcao  = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setId       ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obHdnCtrl  = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );

$obHdnNomForm = new Hidden;
$obHdnNomForm->setName( "nomForm" );
$obHdnNomForm->setValue( $_REQUEST["nomForm"] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );
// COMPONENTES
$obTxtNome = new TextBox;
$obTxtNome->setRotulo        ( "Nome" );
$obTxtNome->setTitle         ( "Nome" );
$obTxtNome->setName          ( "stNome" );
$obTxtNome->setNull          ( true );
$obTxtNome->setInteiro       ( false );
$obTxtNome->setSize          ( 80 );
$obTxtNome->setId            ( "stNome" );

$obTxtCPF = new TextBox;
$obTxtCPF->setName( "stCPF" );
$obTxtCPF->setRotulo( "CPF" );
$obTxtCPF->setNull( true );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );
//$obForm->setTarget( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm                           );
$obFormulario->setAjuda     ( "UC-5.2.4"                        );
$obFormulario->addTitulo    ( "Dados para Responsável"  );
$obFormulario->addHidden    ( $obHdnNomForm );
$obFormulario->addHidden    ( $obHdnCtrl                        );
$obFormulario->addHidden    ( $obHdnAcao                        );
$obFormulario->addHidden    ( $obHdnCampoNom );
$obFormulario->addHidden    ( $obHdnCampoNum );
$obFormulario->addComponente ( $obTxtNome );
$obFormulario->addComponente ( $obTxtCPF );

$obFormulario->setFormFocus ( $obTxtNome->getid() );

$obFormulario->OK();
$obFormulario->show();

?>
