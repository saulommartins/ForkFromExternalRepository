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
 * Arquivo de popup para manutenção de usuários
 * Data de Criação: 25/07/2005

 * @author Analista: Cassiano
 * @author Desenvolvedor: Cassiano

 Casos de uso: uc-03.03.02

 $Id:$

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ValidaUsuario";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "" );

$obTxtUsuario = new TextBox;
$obTxtUsuario->setName( "stUsuario" );
$obTxtUsuario->setRotulo( "Usuário" );
$obTxtUsuario->setTitle( "Informe o usuário" );
$obTxtUsuario->setSize( 20 );
$obTxtUsuario->setMaxLength ( 18 );
$obTxtUsuario->setNull( false );

$obTxtSenha = new PassWord();
$obTxtSenha->setName( "stSenha" );
$obTxtSenha->setRotulo( "Senha" );
$obTxtSenha->setTitle( "Informe a senha do usuário" );
$obTxtSenha->setNull( false );

$obHdClickForm = new Hidden();
$obHdClickForm->setName('clickForm');
$obHdClickForm->setValue($_REQUEST['clickForm']);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo( "Validação de Usuário para saída de Materiais" );
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdClickForm );
$obFormulario->addComponente( $obTxtUsuario );
$obFormulario->addComponente( $obTxtSenha );
$obFormulario->OK();
$obFormulario->show();

print '
<script type="text/javascript">
window.onbeforeunload = function () {
        if (typeof jq == "undefined") {
            var jq = window.opener.parent.frames["telaPrincipal"].jQuery;
        }

        jq("input:button").each(function () {
            this.disabled = false;
        });

        jq("input#Ok").removeAttr("readonly");
        for (i=1;i<4;i++) {
            jq("div#containerPopUp",opener.parent.frames[i].document).each(function () {
                                                                        jq(this).remove();
                                                                   });
                jq("html",opener.parent.frames[i].document).css({"overflow":"auto"});
        }
};
</script>';
