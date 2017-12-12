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
    * Filtro do Popup para Responsavel Tecnico
    * Data de Criação   : 20/04/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo
    *
    * @ignore

    * $Id: FLProcurarInscricaoEconomica.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.9  2008/07/23 16:30:10  Janilson
adicionando nas variáveis dos Hidden's seus respectivos request's.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarInscricaoEconomica";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
include_once( $pgJs );

//variáveis que recebem os nomes dos campos.
$stCtrl   = $request->get("stCtrl"  );
$stAcao   = $request->get("stAcao"  );
$nomForm  = $request->get("nomForm" );
$campoNum = $request->get("campoNum");
$campoNom = $request->get("campoNom");

//destroi arrays de sessao que armazenam os dados do FILTRO
Sessao::write( "filtro", "" );
Sessao::write( "link", ""   );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName         ( "stCtrl"   );
$obHdnCtrl->setValue        ( $request->get("stCtrl")    );

$obHdnAcao = new Hidden;
$obHdnAcao->setName         ( "stAcao"   );
$obHdnAcao->setValue        ( $request->get("stAcao")    );

$obHdnForm = new Hidden;
$obHdnForm->setName         ( "nomForm"  );
$obHdnForm->setValue        ( $request->get("nomForm")   );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName     ( "campoNum" );
$obHdnCampoNum->setValue    ( $request->get("campoNum")  );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName     ( "campoNom" );
$obHdnCampoNom->setValue    ( $request->get("campoNom")  );

// DEFINE OBJETOS DO FILTRO ATIVIDADE/INSCRICAO
$obTxtCGM = new TextBox;
$obTxtCGM->setRotulo     ( "CGM"                         );
$obTxtCGM->setTitle      ( "CGM da Inscrição Econômica"  );
$obTxtCGM->setName       ( "inCGM"                       );
$obTxtCGM->setSize       ( 10                            );
$obTxtCGM->setMaxLength  ( 10                            );
$obTxtCGM->setNull       ( true                          );
$obTxtCGM->setId         ( "inNumCGM"                    );
$obTxtCGM->setInteiro    ( true                          );

$obTxtNome = new TextBox;
$obTxtNome->setRotulo    ( "Nome"                        );
$obTxtNome->setTitle     ( "Nome da Inscrição Econômica" );
$obTxtNome->setName      ( "stNome"                      );
$obTxtNome->setMaxLength ( 200                           );
$obTxtNome->setNull      ( true                          );
$obTxtNome->setStyle     ( "width: 340px"                );

//Componente que define o tipo de busca
$obTipoBuscaNomCgm = new TipoBusca( $obTxtNome );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                          );
$obFormulario->addHidden     ( $obHdnCtrl                       );
$obFormulario->addHidden     ( $obHdnAcao                       );
$obFormulario->addHidden     ( $obHdnForm                       );
$obFormulario->addHidden     ( $obHdnCampoNum                   );
$obFormulario->addHidden     ( $obHdnCampoNom                   );
$obFormulario->addTitulo     ( "Dados para Inscrição Econômica" );
$obFormulario->addComponente ( $obTxtCGM                        );
$obFormulario->addComponente ( $obTipoBuscaNomCgm );
$obFormulario->setFormFocus  ( $obTxtCGM->getId()               );
$obFormulario->Ok();
$obFormulario->show();

SistemaLegado::executaFramePrincipal("document.frm.stRegistroCreci.focus();");

?>
