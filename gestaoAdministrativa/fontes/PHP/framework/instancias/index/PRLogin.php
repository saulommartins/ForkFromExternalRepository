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
/* captura parametros e faz validação basica */

$paramUsuario   = $_POST['usuario'];
$paramSenha     = $_POST['senha'];
$paramExercicio = $_POST['exercicio'];

if (!isset($paramExercicio) || !isset($paramSenha) || !isset($paramUsuario)) {
    exit('Por favor, preencha os todos os campos!');
}

/* validação do usuario */

include '../../../../../../config.php';
include_once URBEM_ROOT_PATH . 'gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once URBEM_ROOT_PATH . 'gestaoAdministrativa/fontes/PHP/framework/URBEM/Sessao.class.php';
include CAM_GA."PHP/pacotes/FrameworkDB.inc.php";

Sessao::open();
Sessao::setUsername($paramUsuario);
Sessao::setPassword($paramSenha);
Sessao::setExercicio($paramExercicio);
$obConexao = new Conexao();
$obConexao->setUser($urbem_config['urbem']['connection']['username']);
$obConexao->setPassWord($urbem_config['urbem']['connection']['password']);

try {
    $obErro = $obConexao->abreConexao();
    if ( !$obErro->ocorreu() ) {
        $obErro = Sessao::consultarDadosSessao();
        if ( !$obErro->ocorreu() ) {
            $obErro = Sessao::verificarSistemaAtivo();
            if ( !$obErro->ocorreu() ) {
                $current_url = str_replace('PRLogin.php','',$_SERVER['SCRIPT_NAME']);
                $urbem_index = $current_url.'index2.php';
                
                echo 'Login efetuado com sucesso <br />';
                echo 'Abrindo Urbem...';
                echo "<script type='text/javascript'>\n";
                echo "window.location = '",$urbem_index,"';\n";
                echo "</script>\n";
            } else {
                Sessao::close();
                echo $obErro->getDescricao();
            }
        } else {
            Sessao::close();
            echo $obErro->getDescricao();
        }
    } else {
        Sessao::close();
        echo "Erro ao logar no Urbem!";
    }
} catch (Exception $e) {
    echo '<strong>Erro ao logar:</strong> <p>' . $e->getMessage() . '</p>';
    Sessao::close();
}
