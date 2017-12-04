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
# coding: utf-8
/**
 *
 * Data de Criação: 26/05/2008
 * @author Desenvolvedor: Lucas T. Stephanou
 */

# validar server
require 'valida_server.php';

# adiciona arquivo de configuração
include 'config.php';
include_once URBEM_ROOT_PATH . 'gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';

//default timezone for php5..3
date_default_timezone_set('America/Sao_Paulo');
$logged_user = null;

$host   = $urbem_config['urbem']['connection']['host'];
$dbname = $urbem_config['urbem']['connection']['database'];
$port   = $urbem_config['urbem']['connection']['port'];

if (array_key_exists('action',$_GET)) {
    if ($_GET['action'] == 'sair') {
        Sessao::close();
        header('Location: ' . URBEM_ROOT_URL . 'index.php' );
    }
}

if (Sessao::getUsername()) {
   $logged_user = Sessao::getUsername();
   $pag_inicial = URBEM_ROOT_URL.'gestaoAdministrativa/fontes/PHP/framework/instancias/index/index2.php';
}

if (defined('ENV_TYPE') && constant('ENV_TYPE') == 'dev') {
    #enum de ips
    $db_hosts = array('selecione','172.16.30.181','172.16.30.34');
    if (isset($_GET['db_host']) && !isset($_GET['db_dev'])) {
        # fake connection
        $dh = $_GET['db_host'];
        $inConnection = pg_connect("host=$dh port=2345 user=urbem password=UrB3m dbname=template1");
        $sql_dbs = 'SELECT datname from pg_database order by datname';

        $gurl = URBEM_ROOT_URL.'index.php';
        $change = "new Ajax.Request(\"$gurl\"
                                    ,{ \"method\":\"get\"
                                      ,parameters: { db_dev: this.value, db_host : $(\"db_host\").value  }
                                     });
                  ";
        echo "<label>Base: <select id='db_dev' name='db_dev' onchange='$change'>";
        $arBases = pg_fetch_all(pg_query($inConnection,$sql_dbs));
        foreach($arBases as $base) :
            echo "<option value='${base['datname']}'>${base['datname']}</option>";
        endforeach;
        echo '</select></label>';
        exit();
    }

    if (isset($_GET['db_host']) && isset($_GET['db_dev'])) {
        $urbem_config['urbem']['connection']['database'] = $_GET['db_dev'];
        $urbem_config['urbem']['connection']['host'] = $_GET['db_host'];
        $_SESSION['urbemoot'] = serialize($urbem_config);
        exit;
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-BR" lang="pt-BR">

    <head profile="http://www.w3.org/2000/08/w3c-synd/#">
        <meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
        <meta name="keywords" content="doc, php-doc, documentacaos" />
        <meta name="description" content="doc" />

        <title>Urbem 1.0 :: Login</title>

        <style type="text/css">
            @import url(<?php echo URBEM_ROOT_URL.'gestaoAdministrativa/fontes/PHP/framework/temas/padrao/CSS/login.css)'?>;
            <?php if ( $logged_user ) echo "#status{display:block} #formulario1{display:none}"?>
        </style>

        <script src="<?php echo URBEM_ROOT_URL.'gestaoAdministrativa/fontes/javaScript/compressed/prototype.js'?>" type="text/javascript"></script>
        <script	src="<?php echo URBEM_ROOT_URL.'gestaoAdministrativa/fontes/javaScript/compressed/login.js'?>" type="text/javascript"></script>

        <?php if (isset($db_hosts)) { ?>
            <script	type="text/javascript">
                Event.observe(window, 'load',function () {
                    Event.observe($('db_host'), 'change',function () {
                        new Ajax.Updater('bases'
                                        , "<?php echo URBEM_ROOT_URL.'index.php'; ?>"
                                        ,{ 'method':'get'
                                          ,parameters: { db_host : this.value  }
                                        })
                    })

                });
            </script>
        <?php } ?>
    </head>

    <body>
    <div id="logon">
    <?php
        if (defined('ENV_TYPE') && constant('ENV_TYPE') == 'dev') {
            if (isset($db_hosts)) {
    ?>
            <label>Banco:
                <select id='db_host' name='db_host'>
                    <?php foreach($db_hosts as $host) : ?>
                        <option value='<?php echo $host;?>'><?php echo $host;?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <div id='bases'> </div>
        <?php } ?>
        <p><strong><?php echo $urbem_config['urbem']['connection']['host'].' - '.$urbem_config['urbem']['connection']['database']; ?></strong></p>
    <?php
        }
    ?>
    <div id="formulario1">
        <form action="<?php echo URBEM_ROOT_URL.'gestaoAdministrativa/fontes/PHP/framework/instancias/index/'?>PRLogin.php" id="frmLogin" method="post" onsubmit="return false;">

            <div id="logo_urbem">
                <img alt="Logo Urbem" id="img_logo_urbem" src="<?php echo URBEM_ROOT_URL.'gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/'?>logo_urbem_login.png" />
            </div>

            <div id="label-frm"><label for="usuario" id="label_usuario"	title="Usuario">Usuário</label><br />
                <label for="senha" id="label_senha" title="Logon">Senha</label><br />
                <label for="exercicio" id="label_exercicio" title="Exercicio">Exercício</label>
            </div>

            <div id="input-frm">
                <input accesskey="u" type="text" id="usuario" name="usuario" title="Usuario" tabindex="1" /><br />
                <input accesskey="s" type="password" id="senha" name="senha" title="Senha" tabindex="2"/><br />
                <input accesskey="x" type="text" id="exercicio" name="exercicio" title="Exercicio" size="4" maxlength="4" value="<?php echo date( 'Y', time());?>" tabindex="4"/>
            </div>

            <div id="submit-frm">
                <div id="input_login">
                    <input accesskey="e" type="submit" id="enviar" name="enviar" title="Login" value="Login" tabindex="3" />
                </div>
                <div id="input_limpar">
                    <input accesskey="l" type="reset" id="limpar" name="limpar" title="Limpar" value="Limpar" />
                </div>
            </div>

        </form>
    </div>

    <div id="status">
    <?php
        $msg_status = '';

        if (isset($logged_user)) {
            $msg_status = '<p>Usuário logado: <strong>'.$logged_user.'</strong></p>
                           <a id=\'entrar\' href=\''.$pag_inicial.'\'>Voltar ao Urbem</a> <br />
                           <a id=\'entrar\' href=\''.URBEM_ROOT_URL.'index.php?action=sair'.'\'>Terminar Sessão</a>
                           ';
        }

        echo $msg_status;
    ?>
    </div>

    </div>

    </body>

</html>
