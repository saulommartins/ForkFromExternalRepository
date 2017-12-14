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
* Manutneção de usuários
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 27766 $
$Name$
$Author: luiz $
$Date: 2008-01-28 07:56:40 -0200 (Seg, 28 Jan 2008) $

Casos de uso: uc-01.03.93
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_ADM_NEGOCIO.'RUsuario.class.php'   );
include_once(CAM_FW_LEGADO."funcoesLegado.lib.php");
setAjuda( "UC-01.03.93" );

$stAcao = $_REQUEST['stAcao'] ? $_REQUEST['stAcao'] : $stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterSenha";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = "alterar";

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( 'stAcao' );
$obHdnAcao->setValue( $stAcao  );

$obTxtUserName =  new TextBox;
$obTxtUserName->setName      ( 'stUserName' );
$obTxtUserName->setRotulo    ( 'Username'   );
$obTxtUserName->setNull      ( false        );
$obTxtUserName->setSize      ( 15           );
$obTxtUserName->setMaxLength ( 15           );
$obTxtUserName->setValue     ( $stUserName  );

$obLblUserName = new Label;
$obLblUserName->setRotulo    ( 'Username'   );
$obLblUserName->setValue     ( Sessao::getUsername() );

$obHdnUserName =  new Hidden;
$obHdnUserName->setName      ( 'stUserName' );
$obHdnUserName->setValue     ( Sessao::getUsername() );

$obPswSenhaAtual = new Password;
$obPswSenhaAtual->setName      ( 'stSenhaAtual' );
$obPswSenhaAtual->setRotulo    ( 'Senha Atual'  );
$obPswSenhaAtual->setTitle( "Informe a senha atual.");
$obPswSenhaAtual->setNull      ( false          );
$obPswSenhaAtual->setSize      ( 34             );
$obPswSenhaAtual->setMaxLength ( 34             );

$obPswSenha = new Password;
$obPswSenha->setName      ( 'stSenha'    );
$obPswSenha->setRotulo    ( 'Nova Senha' );
$obPswSenha->setTitle( "Informe a nova senha.");
$obPswSenha->setNull      ( false        );
$obPswSenha->setSize      ( 34           );
$obPswSenha->setMaxLength ( 34           );

$obPswConfirmacaoSenha = new Password;
$obPswConfirmacaoSenha->setName      ( 'stConfirmacaoSenha' );
$obPswConfirmacaoSenha->setRotulo    ( 'Confirmação Senha'  );
$obPswConfirmacaoSenha->setTitle( "Confirme a nova senha.");
$obPswConfirmacaoSenha->setNull      ( false                );
$obPswConfirmacaoSenha->setSize      ( 34                   );
$obPswConfirmacaoSenha->setMaxLength ( 34                   );

$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( 'oculto' );
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm            );
$obFormulario->addTitulo     ( 'Dados para Senha' );
$obFormulario->addHidden     ( $obHdnAcao         );
if ( Sessao::read('numCgm') != 0 ) {
    $obFormulario->addHidden     ( $obHdnUserName  );
    $obFormulario->addComponente ( $obLblUserName  );
    $obFormulario->addComponente ( $obPswSenhaAtual );
} else {
    $obFormulario->addComponente ( $obTxtUserName     );
}
$obFormulario->addComponente ( $obPswSenha            );
$obFormulario->addComponente ( $obPswConfirmacaoSenha );
$obFormulario->Ok();
$obFormulario->show();
