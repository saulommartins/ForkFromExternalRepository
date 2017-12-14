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
    *
    $Id: $

    Casos de uso: uc-03.03.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/bancoDados/postgreSQL/ConexaoSecundaria.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/administracao/classes/mapeamento/TAdministracaoUsuario.class.php';

$stPrograma = "ValidaUsuario";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obErro = new Erro();

//essa conexão secundária serve somente para validar um usuário que não está logado
$obConexao = new ConexaoSecundaria($urbem_config['urbem']['connection']['username'], $urbem_config['urbem']['connection']['password']);
$obErro = $obConexao->abreConexao();

if (!$obErro->ocorreu()) {
    $obConexao->setUserLogin($_REQUEST['stUsuario']);
    $obConexao->setPassWordLogin($_REQUEST['stSenha']);
    $obErro = $obConexao->verificaConexao();
}
if ($obConexao->getConnection()) {
    $obConexao->fechaConexao();
}

if ($obErro->ocorreu()) {
    $stJs = "
    principal.alertaAviso('Erro ao validar usuário(".$obErro->getDescricao().")','form','erro','<?=Sessao::getId();?>');
    principal.LiberaFrames();
    f.Ok.removeAttribute('disabled');";
} else {
    $stJs = 'f.stCGMUsuario.value = "'.$_REQUEST['stUsuario'].'";
    f.Ok.setAttribute("onclick", "javascript:'.$_REQUEST["clickForm"].'");
    principal.Salvar();';
}

print '<script type="text/javascript">
           function executa()
           {
                var mensagem = "";
                var erro = false;
                var f = window.opener.parent.frames["telaPrincipal"].document.frm;
                var d = window.opener.parent.frames["telaPrincipal"].document;
                var jq_ = window.opener.parent.frames["telaPrincipal"].jQuery;
                var principal = window.opener.parent.frames["telaPrincipal"];
                var aux;
                '.$stJs.'
                window.close();

                if (erro) alertaAviso(mensagem,"form","erro","'.Sessao::getId().'");
           }
           executa();
           </script>';
