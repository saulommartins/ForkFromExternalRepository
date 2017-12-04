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
    * Filtro para Alteração de Terminais - Tesouraria
    * Data de Criação   : 09/09/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 30835 $
    $Name$
    $Autor: $
    $Date: 2006-08-08 16:27:18 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-02.04.02
*/

/*
$Log$
Revision 1.15  2006/08/08 19:27:18  jose.eduardo
Bug #6699#

Revision 1.14  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma      = "ManterTerminal";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

// DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget ( "oculto" );
$obForm->setTarget( "telaPrincipal");

// OBJETOS HIDDEN

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl"            );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"            );
$obHdnAcao->setValue ( $stAcao );

$obHdnCgmUsuario = new Hidden;
$obHdnCgmUsuario->setName( "cgmUsuario" );
$obHdnCgmUsuario->setValue( Sessao::read('numCgm') );

//Define Objeto Text para Nr. do Terminal
$obTxtNroTerminal = new TextBox;
$obTxtNroTerminal->setName      ( "inNumTerminal"                                                       );
$obTxtNroTerminal->setValue     ( $inNumTerminal                                                        );
$obTxtNroTerminal->setRotulo    ( "Nr. Terminal"                                                        );
$obTxtNroTerminal->setTitle     ( "Informe o número do terminal de Caixa a ser alterado                ");
$obTxtNroTerminal->setNull      ( true                                                                  );
$obTxtNroTerminal->setMaxLength ( 3                                                                     );
$obTxtNroTerminal->setSize      ( 4                                                                     );

// Define objeto BuscaInner para cgm
$obBscCGM = new BuscaInner();
$obBscCGM->setRotulo                 ( "Usuário Responsável pelo Terminal"                                         );
$obBscCGM->setTitle                  ( "Informe o código cgm do Usuário de Terminal de Caixa que deseja pesquisar"  );
$obBscCGM->setId                     ( "stNomCgm"                                                                   );
$obBscCGM->setValue                  ( $stNomCgm                                                                    );
$obBscCGM->setNull                   ( true                                                                         );
$obBscCGM->obCampoCod->setName       ( "inNumCgm"                                                                   );
$obBscCGM->obCampoCod->setSize       ( 10                                                                           );
$obBscCGM->obCampoCod->setMaxLength  ( 8                                                                            );
$obBscCGM->obCampoCod->setValue      ( $inNumCgm                                                                    );
$obBscCGM->obCampoCod->setAlign      ( "left"                                                                       );
$obBscCGM->setFuncaoBusca            ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCgm','stNomCgm','usuario','".Sessao::getId()."','800','550');");
$obBscCGM->setValoresBusca           ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() );

// Define Objeto Select para informar se o usuário é Responsável pelo terminal
$obCmbSituacao = new Select();
$obCmbSituacao->setRotulo ( "Situação do Terminal"          );
$obCmbSituacao->setTitle  ( "Informe a situação do Terminal");
$obCmbSituacao->setName   ( "stSituacao"                    );
$obCmbSituacao->addOption ( "","Selecione"                  );
$obCmbSituacao->addOption ( "a","Ativo"                     );
$obCmbSituacao->addOption ( "i","Inativo"                   );
$obCmbSituacao->setValue  ( $boSituacao                     );
$obCmbSituacao->setStyle  ( "width: 120px"                  );
$obCmbSituacao->setNull   ( true                            );

$obOk = new Ok;
$obLimpar = new Limpar;

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm                                   );
$obFormulario->addHidden    ( $obHdnCtrl                                );
$obFormulario->addHidden    ( $obHdnAcao                                );
$obFormulario->addHidden    ( $obHdnCgmUsuario                          );
$obFormulario->addTitulo    ( "Dados para Filtro"                       );
$obFormulario->addComponente( $obTxtNroTerminal                         );
$obFormulario->addComponente( $obBscCGM                                 );
if($stAcao<>"excluir")
    $obFormulario->addComponente( $obCmbSituacao                            );
$obFormulario->defineBarra  ( array( $obOk, $obLimpar )                            );

$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
