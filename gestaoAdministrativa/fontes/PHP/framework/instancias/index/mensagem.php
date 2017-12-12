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

include '../../../../../../config.php';
include URBEM_ROOT_PATH . 'gestaoAdministrativa/fontes/PHP/framework/bancoDados/postgreSQL/Transacao.class.php';

include URBEM_ROOT_PATH . 'gestaoAdministrativa/fontes/PHP/framework/excecao/Erro.class.php';
include URBEM_ROOT_PATH . 'gestaoAdministrativa/fontes/PHP/framework/excecao/Excecao.class.php';

include_once URBEM_ROOT_PATH . 'gestaoAdministrativa/fontes/PHP/framework/URBEM/SessaoLegada.class.php';
include_once URBEM_ROOT_PATH . 'gestaoAdministrativa/fontes/PHP/framework/URBEM/Sessao.class.php';

include URBEM_ROOT_PATH . 'gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include URBEM_ROOT_PATH . 'gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Bloco para verificar status do sistema
include(CAM_FW_LEGADO."funcoesLegado.lib.php");
include(CAM_FW_LEGADO."sistema.class.php");
if((strtolower(Sistema::consultaStatus()) == 'i')&&(Sessao::read('username') != 'admin')){
    echo("<script type='text/javascript'>parent.window.location.href='".URBEM_ROOT_URL."/index.php?action=sair';</script>");
}

$obj = array_key_exists('obj',$_GET) ? $_GET['obj'] : null;
$tipo = array_key_exists('tipo',$_GET) ? $_GET['tipo'] : null;
$chamada = array_key_exists('chamada',$_GET) ? $_GET['chamada'] : null;

$msgs = "";
$tempo = getdate();
//Declara as variáveis referentes aos botões
$btErro = "botao_erro.png";
$btInfo = "botao_info.png";
$btAviso = "botao_aviso.png";

$vetMensagens = null;

if (strlen($tempo['hours']) == 1) {
    $tempo['hours'] = "0".$tempo['hours'];
}
if (strlen($tempo['minutes']) == 1) {
    $tempo['minutes'] = "0".$tempo['minutes'];
}
$hora = $tempo['hours'].":".$tempo['minutes'];

//Grava o nome da ação realizada
if (is_numeric(Sessao::read('acao')) AND Sessao::read('acao') > 0) {
    $nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'));
} else {
    $nomAcao = "";
}

//Retira as barras das strings
if (is_string($obj)) {
    $obj = stripslashes($obj);
}

if (!is_array(Sessao::getMensagens())) {
    Sessao::setMensagens(array());
}

$vetMensagens = array_reverse(Sessao::getMensagens());

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">

<html>
<head>
<title>URBEM</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
@import
    url("../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/CSS/stylos_ns.css")
    ;
</style>
</head>
<body bgcolor=#FFFFFF  leftmargin="0" topmargin="0"
    onload="window.status=':::::::: URBEM ::::::::';"
    style="margin-top: 0;">

<center>
<table width=99%>
<?php
if ($tipo=="form") {
    $vetMensagens = array();
    $vetAux = explode("@",$obj);
    foreach ($vetAux as $msgForm) {
        if ($msgForm=="") {
            continue;
        }
        $vetForm = array('imagem'=>$btAviso,'mensagem'=>$msgForm,'hora'=>$hora);
        $vetMensagens[] = $vetForm;
    }
} elseif ($tipo=="formulario") {
    $vetMensagens = array();
    $vetAux = explode("@",$obj);
    foreach ($vetAux as $campo) {
        if ($campo=="") {
            continue;
        }
        $vetTmp = explode(":",$campo);
        $msgForm = "Campo ".$vetTmp[0]." inválido! (".$vetTmp[1].")";
        $vetForm = array('imagem'=>$btAviso,'mensagem'=>$msgForm,'hora'=>$hora);
        $vetMensagens[] = $vetForm;
    }
} elseif (strlen($obj)>0) { //else (not form)
    $arquivaSessao = false;
    $vetMsg = array('hora'=>$hora);
    switch ($chamada) {
        case "erro":
            $vetMsg['imagem'] = $btErro;
            break;
        case "aviso":
            $vetMsg['imagem'] = $btInfo;
            break;
        case "info":
            $vetMsg['imagem'] = $btInfo;
            break;
        default:
            $vetMsg['imagem'] = $btAviso;
            break;
    }

    switch ($tipo) {
        case "incluir":
            //$vetMsg['mensagem'] = $obj." incluído com sucesso";
            $vetMsg['mensagem'] = $nomAcao." concluído com sucesso! (".$obj.") ";
            $arquivaSessao = true;
            break;
        case "n_incluir":
            //$vetMsg['mensagem'] = "Não foi possível incluir ".$obj.", contate o Administrador";
            $vetMsg['mensagem'] = "Erro ao ".$nomAcao."! (".$obj.") ";
            break;
        case "alterar":
            //$vetMsg['mensagem'] = $obj." alterado com sucesso";
            $vetMsg['mensagem'] = $nomAcao." concluído com sucesso! (".$obj.") ";
            $arquivaSessao = true;
            break;
        case "n_alterar":
            //$vetMsg['mensagem'] = "Não foi possível alterar ".$obj.", contate o Administrador";
            $vetMsg['mensagem'] = "Erro ao ".$nomAcao."! (".$obj.") ";
            break;
        case "anular":
            //$vetMsg['mensagem'] = $obj." anulado com sucesso";
            $vetMsg['mensagem'] = $nomAcao." concluído com sucesso! (".$obj.") ";
            $arquivaSessao = true;
            break;
        case "n_anular":
            //$vetMsg['mensagem'] = "Não foi possível anular ".$obj.", contate o Administrador";
            $vetMsg['mensagem'] = "Erro ao ".$nomAcao."! (".$obj.") ";
            break;
        case "sn_excluir":
            $vetMsg['mensagem'] = "Deseja excluir ".$obj."?";
            break;
        case "excluir":
            //$vetMsg['mensagem'] = $obj." excluído com sucesso";
            $vetMsg['mensagem'] = $nomAcao." concluído com sucesso! (".$obj.") ";
            $arquivaSessao = true;
            break;
        case "n_excluir":
            //$vetMsg['mensagem'] = "Não foi possível excluir ".$obj.", contate o Administrador";
            $vetMsg['mensagem'] = "Erro ao ".$nomAcao."! (".$obj.") ";
            break;
        case "baixar":
            //$vetMsg['mensagem'] = $obj." excluído com sucesso";
            $vetMsg['mensagem'] = $nomAcao." concluído com sucesso! (".$obj.") ";
            $arquivaSessao = true;
            break;
        case "n_baixar":
            //$vetMsg['mensagem'] = "Não foi possível excluir ".$obj.", contate o Administrador";
            $vetMsg['mensagem'] = "Erro ao ".$nomAcao."! (".$obj.") ";
            break;
        case "historico":
            $vetMsg['mensagem'] = $obj;
            $arquivaSessao = true;
            break;
        case "unica":
            $vetMsg['mensagem'] = $obj;
            break;
        default:
            $vetMsg['mensagem'] = $obj;
            break;
    }
    //Verifica se a mensagem deve ser arquivada no histórico
    if ($arquivaSessao) {
        $mensagens = $vetMensagens;
        $mensagens[] = $vetMsg;
        $vetMensagens = array_reverse($mensagens);

    } else {
        $aux = $vetMensagens;
        $aux[] = $vetMsg;
        $vetMensagens = array_reverse($aux);
    }
    Sessao::setMensagens($vetMensagens);
} else {
    $vetMensagens = array_reverse($vetMensagens);
}

$stCaminhoImagens = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/';
if (is_array($vetMensagens)) {
    foreach ($vetMensagens as $vet) {
        ?>
    <tr>
        <td width="1%" class="fieldMensagem"><img
            src="<?=$stCaminhoImagens.$vet['imagem'];?>" width="16" height="16"
            border="0"></td>
        <td width="94%" class="fieldMensagem"><?=$vet['mensagem']?></td>
        <td width="5%" class="fieldMensagem"><?=$vet['hora']?></td>
    </tr>
    <?php
    }
}
?>
</table>
</center>
</body>
</html>
