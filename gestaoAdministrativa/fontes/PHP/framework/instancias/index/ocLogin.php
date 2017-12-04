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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';

if (isset($_POST['usuario'])) { //Se houver post
    if ($_POST['exercicio']) {
        //Verifica se o usuário informado existe
        $sessao = new Sessao;
        $sessao->setUsername( $_POST['usuario'] );
        $sessao->setPassword( $_POST['senha'] );
        $sessao->exercicio = $_POST['exercicio'];
        $obConexao = new Conexao;
        $obErro = $obConexao->abreConexao();
        if ( !$obErro->ocorreu() ) {
            $sessao->validaSessao();
            $obErro = $sessao->consultarDadosSessao();
            if ( !$obErro->ocorreu() ) {
                $obErro = $sessao->verificarSistemaAtivo();
                if ( !$obErro->ocorreu() ) {
                    $obErro = $sessao->buscarLinksMaisAcessados( $arLinksMaisAcessaodos );
                }
            }
        } else {
            include_once( CAM_GA_ADM_NEGOCIO.'RUsuario.class.php' );
            $obRUsuario = new RUsuario;
            $obRUsuario->setUsername( $_POST['usuario'] );
            $obRUsuario->setPassword( $_POST['senha'] );
            $obErro = $obRUsuario->atualizarCadastroUsuario();
            if ( !$obErro->ocorreu() ) {
                $sessao->validaSessao();
                $obErro = $sessao->consultarDadosSessao();
                if ( !$obErro->ocorreu() ) {
                    $obErro = $sessao->verificarSistemaAtivo();
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $sessao->buscarLinksMaisAcessados( $arLinksMaisAcessaodos );
                    }
                }
            }
        }
    } else {
        $obErro = new Erro;
        $obErro->setDescricao( "O exercício deve ser informado!" );
    }

    if ( !$obErro->ocorreu() ) {
    ?>
    <script type="text/javascript">
    function logar(sessao)
    {
        var x = "1";
        var y = "1";
        var w = (window.screen.width - 10);
        var h = (window.screen.height - 75);
        var rnd = String(Math.random());
        var random = rnd.substr(10,6);
        var sessaoid = sessao.substr(10,6);
        var sArq = "index2.php?"+sessao;
        var sAux = "ini"+ sessaoid +" = window.open(sArq,\'ini"+ sessaoid +"\',\'screenX=" + window.screenLeft + ",screenY=" + window.screenTop + ",width=" + w +",height=" + h + ",status=1,resizable=1,scrollbars=0,left="+x+",top="+y+"\');";
        eval(sAux);
        parent.frames["telaPrincipal"].location.replace("login.php");
        parent.frames["oculto"].location.replace("ivFrame.php");
    }
    logar("<?=$sessao->id;?>");
    </script>
<?php
    } else {
?>
    <script type="text/javascript">
    window.parent.frames["telaPrincipal"].document.getElementById("erro").style.display = 'block';
    window.parent.frames["telaPrincipal"].document.getElementById("erro").innerHTML = '<p><?=$obErro->getDescricao();?></p>';
    window.parent.frames["telaPrincipal"].document.getElementById("frm").reset();
    window.parent.frames["telaPrincipal"].document.getElementById("usuario").focus();
    </script>
<?php
    }
}
?>
