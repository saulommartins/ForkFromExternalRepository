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
    * Página de Formulario de Inclusao/Alteracao de Serviços

    * Data de Criação   : 13/04/2005

    * @author Fernando Zank Correa Evangelista

    * @ignore

    * $Id: FMManterNaturezaBaixar.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.08
*/

/*
$Log$
Revision 1.7  2007/02/14 12:14:12  rodrigo
#6474#

Revision 1.6  2006/09/15 14:33:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNaturezaJuridica.class.php" );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
    }

//Define o nome dos arquivos PHP
$stPrograma    = "ManterNatureza";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );
$obRCEMNatureza = new RCEMNaturezaJuridica;

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCodigoNatureza =  new Hidden;
$obHdnCodigoNatureza->setName   ( "inCodigoNatureza" );
$obHdnCodigoNatureza->setValue  ( $_REQUEST["inCodigoNatureza"]  );

$obHdnNomeNatureza =  new Hidden;
$obHdnNomeNatureza->setName   ( "stNomeNatureza" );
$obHdnNomeNatureza->setValue  ( $_REQUEST["inCodigoNatureza"]." - ".$_REQUEST["stNomeNatureza"] );

$obLblNomeNatureza = new Label ;
$obLblNomeNatureza->setRotulo    ( "Nome" );
$obLblNomeNatureza->setName      ( "stLblNomeNatureza");
$obLblNomeNatureza->setValue     ( $_REQUEST["stNomeNatureza"] );
$obLblNomeNatureza->setTitle     ( "Nome da Natureza" );

$obLblCodigoNatureza = new Label ;
$obLblCodigoNatureza->setRotulo    ( "Código" );
$obLblCodigoNatureza->setName      ( "labelCodigoNatureza");
$obLblCodigoNatureza->setValue     ( $_REQUEST["inCodigoNatureza"] );
$obLblCodigoNatureza->setTitle     ( "Código da Natureza" );

// Define Objeto TextArea para Motivo
$obTxtMotivo = new TextArea;
$obTxtMotivo->setName   ( "stMotivoBaixa" );
$obTxtMotivo->setId     ( "stMotivoBaixa" );
$obTxtMotivo->setValue  ( $_REQUEST["stMotivoBaixa"]  );
$obTxtMotivo->setRotulo ( "Motivo" );
$obTxtMotivo->setId     ( "motivoBaixa" );
$obTxtMotivo->setTitle  ( "" );
$obTxtMotivo->setNull   ( true );
$obTxtMotivo->setRows   ( 2 );
$obTxtMotivo->setCols   ( 100 );
$obTxtMotivo->setNull   ( false );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( "Dados para natureza Jurídica" );

$obFormulario->addHidden     ( $obHdnAcao           );
$obFormulario->addHidden     ( $obHdnCodigoNatureza );
$obFormulario->addHidden     ( $obHdnNomeNatureza );
$obFormulario->addComponente ( $obLblCodigoNatureza );
$obFormulario->addComponente ( $obLblNomeNatureza   );
$obFormulario->addComponente ( $obTxtMotivo         );
if ($stAcao == "baixar") {
    $obFormulario->setFormFocus( $obTxtMotivo->getid() );
}

if ($stAcao == "incluir") {
    $obFormulario->Ok       ();
} else {
    $obFormulario->Cancelar ();
}
$obFormulario->show();

sistemaLegado::executaFrameOculto( $stJs );
