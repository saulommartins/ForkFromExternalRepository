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

 $Id: cabecalho.inc.php 64012 2015-11-18 16:42:41Z diogo.zarpelon $

 Casos de uso: uc-01.01.00
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/SessaoLegada.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/Sessao.class.php';
include_once CAM_FW_LEGADO."dataBaseLegado.class.php";

Sessao::open();
Sessao::setTrataExcecao(false);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

if (isset($_SERVER["HTTP_REFERER"])) {
    if (strstr($_SERVER["HTTP_REFERER"],'menu.php')) {
        Sessao::clean();
    }
}
/* muda local de js e mostra debugs caso esteja em modo de desenvolvimento */
$jsPath = constant('ENV_TYPE') == 'dev' ? '' : 'compressed/';

?>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv='Pragma' content='no-cache'>
<meta http-equiv='Cache-Control' content='no-store, no-cache, must-revalidate'>
<META HTTP-EQUIV="expires" CONTENT="Tue, 23 Jun 2002 01:46:05 GMT">
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>ifuncoesJs.js" type="text/javascript"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>funcoesJs.js" type="text/javascript"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>genericas.js" type="text/javascript"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>mascaras.js" type="text/javascript"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>tipo.js" type="text/javascript"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>arvore.js" type="text/javascript"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>ajax.js" type="text/javascript"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>prototype.js" type="text/javascript"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>jquery.js" type="text/javascript"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>jquery-ui.js" type="text/javascript"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>jquery.selectboxes.js" type="text/javascript"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>jquery.price_format.1.2.js" type="text/javascript"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>table_tree.js" type="text/javascript"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>jquery-migrate-1.2.1.js" type="text/javascript"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>jquery.formalize.js" type="text/javascript">></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>jquery.meiomask.js" type="text/javascript" charset="utf-8"></script>
<script src="<?=CAM_GA;?>javaScript/<?=$jsPath?>qTip.js" type="text/javascript"></script> <!-- IMPORTANTE - o arquivo qTip.js, reescreve o window.onLoad, que é usado em Window.js acima-->

<script type="text/javascript">
    jq = jQuery.noConflict();

    MontaCSS();

    // a cada 5s atualiza tooltips
    setInterval('tooltip.init();',5000);

    function mostraDebugOculto()
    {
        if (document.getElementById('div_debug')) {
            document.body.removeChild(document.getElementById("div_debug"));
        } else {
            var eDiv=document.createElement("DIV");
            eDiv.setAttribute('id' , 'div_debug');
            eDiv.style.width=600;
            eDiv.style.height=400;
            eDiv.style.position="fixed";
            eDiv.style.backgroundColor="white";
            eDiv.style.left='25%';
            eDiv.style.top='20%';
            eDiv.style.padding=5;
            eDiv.style.border='1px solid #000';

            var container=document.createElement("DIV");
            container.style.width = '98%';
            container.style.height = '94%';
            container.style.overflow='scroll';
            container.style.padding=2;
            eDiv.style.border='1px solid #aaa';

            container.innerHTML = parent.frames['oculto'].document.body.innerHTML;
            var html = "<table width='100%' cellspacing='2' cellpadding='2' border='0'><tr><td colspan='2' class='alt_dados'>";
            html= html + "<a style='color: #fff;' href='#' onclick='document.body.removeChild(document.getElementById(\"div_debug\"))'>FECHAR DEBUG</a>";
            html= html + "</td></tr></table>";
            eDiv.innerHTML = html;
            eDiv.appendChild(container);

            document.body.appendChild(eDiv);
        }
    }

    var oldValue;

</script>

<link rel="stylesheet" href="<?=CAM_GA;?>PHP/framework/temas/padrao/CSS/formalize.css" />
<link rel="stylesheet" href="<?=CAM_GA;?>PHP/framework/temas/padrao/CSS/paginacao.css" />

</head>
<body bgcolor="#E6E6E6" leftmargin=0 topmargin=0>

<!-- *******************  Layers para bloquear frames ******************************** -->
<script type="text/javascript">
    function anulaTecla()
    {
    }
</script>
<div id="fundo_carregando">
<div id="texto_carregando">
<table width="100%" cellspacing="2" cellpadding="2" border="0">
    <tr>
        <td colspan="2" class="alt_dados">Processando</td>
    </tr>
</table>

<table border=0 align="center" valign="middle">
    <tr>
        <td align="center"><img id="img_carregando"
            src="<?=CAM_FW_IMAGENS ;?>loading.gif"><br>
        <br>
        </td>
    </tr>
</table>
</div>
</div>
<script>
    document.getElementById('fundo_carregando').style.visibility='hidden';
</script>
<center>
<div id="titulo1">
<?php
$sSQL = "select parametro,valor from administracao.configuracao WHERE parametro = 'mensagem' and exercicio = '".Sessao::getExercicio()."'";
$dbEmp = new dataBaseLegado;
$dbEmp->abreBD();
$dbEmp->abreSelecao($sSQL);
$dbEmp->vaiPrimeiro();
$janela="";
while (!$dbEmp->eof()) {
   $divulga  = strip_tags(trim($dbEmp->pegaCampo("valor")));
   $dbEmp->vaiProximo();
   $janela .= "$divulga";
}
$dbEmp->limpaSelecao();
$dbEmp->fechaBD();
?>
<table width='100%' border=0>
    <tr>
        <td class='titulocabecalho' height='5' width='100%'>
        <table cellspacing=0 cellpadding=0 class='titulocabecalho_gestao'
            width='100%'>
            <tr>
                <td width='80%' class='caminho' ><?php
                    if ( array_key_exists('acao',$_GET) || Sessao::read('acao')) {
                        $rsAcao = new Recordset();
              $codAcao = array_key_exists('acao',$_GET) ? $_GET['acao'] : Sessao::read('acao');
                        $obRAdministracaoAcao = new RAdministracaoAcao();
                        $obRAdministracaoAcao->setCodigoAcao($codAcao);
                        $obRAdministracaoAcao->listar($rsAcao,null,null,null);

                        $stTituloAcao  = 'Gestão ' . $rsAcao->getCampo('nom_gestao') . ' :: ';
                        $stTituloAcao .= $rsAcao->getCampo('nom_modulo') . ' :: ';
                        $stTituloAcao .= $rsAcao->getCampo('nom_funcionalidade') . ' :: ';
                        $stTituloAcao .= $rsAcao->getCampo('nom_acao') . '';
                    } else {
                        //$stTituloAcao = Sessao::getTituloPagina();
                        $stTituloAcao = $janela;
                    }

                    echo $stTituloAcao;
                    $stPopUpVersao = CAM_GA_ADM_POPUPS."versao/LSMostrarHistorico.php?".Sessao::getId()."&stArquivoHistorico=".Sessao::getHistoricoVersao();

                    $flVersao = "<a class='versaoFundo' href='javascript: exibeHistorico( \"".$stPopUpVersao."\" );' title='Clique Aqui <br> Para ver as atualizações disponibilizadas na ultima versão'>".Sessao::getVersao()."</a>";

                ?></td>

                <td width='20%' class='versao'>
                    <?php  if (Sessao::getVersao() != null) { ?>

                            <div class="versaoFundo"><?=$flVersao;?></div>
                    <?php }  ?>

                </td>
            </tr>
        </table>
        </td>
    </tr>
</table>
</div>
<!-- Ajax Loading -->
<p id="carregando"
    style="display: none; position: fixed; bottom: 0px; left: 1px; background-color: transparent;">
<img id="ajax_carregando" src="<?php echo CAM_FW_IMAGENS; ?>ajax_carregando.gif" alt="Carregando" /></p>
<span id="spnErro" style="display: none;"></span> <!-- FIM Ajax Loading -->
    <?php
    if (isset($_SERVER["HTTP_REFERER"])) {
        if (strstr($_SERVER["HTTP_REFERER"],'menu.php')) {
           Sessao::setNumeroLinhas(0);
        }
    }
    if ( Sessao::getVoltarProtocolo() ) {				    //
        $stHtml = '
        <!-- INICIO VOLTA-->
        <script type=\'text/javascript\'>
            if (window.parent.frames["telaPrincipal"]) {
                var stLink = \'<a id="link_volta" href="'.CAM_GA_PROT_INSTANCIAS.'processo/OCManterProcesso.php?'.Sessao::getId().'&stCtrl=voltarProcesso"  alt="Voltar para processo">\';
                stLink += \'<span alt="Voltar">Voltar</span>\';
                document.write(stLink);
            }
        </script>
        <!-- FIM VOLTA-->
        ';
                    echo $stHtml;
                }
                $cam = CAM_FW_IMAGENS;
                $stMostraDebug = <<<html
    <a style=" text-decoration:none;" href="#" accesskey="d" onclick="mostraDebugOculto()"></a>
html;
    echo $stMostraDebug;

    $swConfig = Sessao::read('urbemoot');
    if (isset($_REQUEST['acao']) AND isset($swConfig['urbem']['manuais']['url_app'])) {
        $json = file_get_contents($swConfig['urbem']['manuais']['url_app'] . '/check/' . $_REQUEST['acao'] . '.json');
        $arManual = json_decode($json);
        if ($arManual->manual) {
?>
<a href="<?php echo $swConfig['urbem']['manuais']['url_app'] . '/manual/' . $_REQUEST['acao']; ?>" id="btnHelp"
   style="background-image:url(<?php echo CAM_FW_IMAGENS."btnHelpOff.png"; ?>);" target="_blank">Visualizar o Manual</a>
<?php
        }
    }
if (constant('ENV_TYPE') == 'dev') {
                ?>
<div id="swTools">
<?php

    $acao = isset($_REQUEST['acao']) ? $_REQUEST['acao'] : '';

    if (!empty($acao) AND isset($swConfig['urbem']['manuais']['url_app'])) {
        $stLinkManual = $swConfig['urbem']['manuais']['url_app'] . '/backend.php/manual/edit/' . $acao;
?>
    <a href="<?php echo $stLinkManual; ?>" title="Editar o manual" target="_blank"><img src="<?php echo CAM_FW_IMAGENS."btnincluir.gif"; ?>" /></a>
<?php
    }
?>
    <a href="#" id="menu-abrir" title="Mostrar Menu"
        onclick="   parent.document.getElementById('frTela').cols = '180,*';
                        document.getElementById('menu-abrir').style.display = 'none';
                        document.getElementById('menu-fechar').style.display = 'inline';

                        return false;"
    ><img src="<?php echo CAM_FW_IMAGENS."btnAvancaProcesso.png"; ?>" /></a>
    <a href="#" id="menu-fechar" title="Esconder Menu"
        onclick="   parent.document.getElementById('frTela').cols = '0,*';
                    document.getElementById('menu-abrir').style.display = 'inline';
                    document.getElementById('menu-fechar').style.display = 'none';

                    return false;"
     ><img src="<?php echo CAM_FW_IMAGENS."btnRetornoProcesso.png"; ?>" /></a>
    <a href="#" title="Recarregar Frame Principal" onclick="window.location.reload();" ><img src="<?php echo CAM_FW_IMAGENS."btnRefresh.png"; ?>" /></a>
    <a href="#" title="Voltar para página anterior" onclick="window.history.back();" ><img src="<?php echo CAM_FW_IMAGENS."btnEstornar.png"; ?>" /></a>
    <a href="#" id="menu-fechar-oculto" title="Esconder Frame Oculto"
        onclick="parent.document.getElementById('frOculto').rows = '70,*,22,0';
                 document.getElementById('menu-abrir-oculto').style.display = 'inline';
                 document.getElementById('menu-fechar-oculto').style.display = 'none';

                 return false;"
    ><img src="<?php echo CAM_FW_IMAGENS."look_off.gif"; ?>" /></a>
    <a href="#" id="menu-abrir-oculto" title="Mostrar Frame Oculto"
        onclick="   parent.document.getElementById('frOculto').rows = '70,*,22,220';
                        document.getElementById('menu-abrir-oculto').style.display = 'none';
                        document.getElementById('menu-fechar-oculto').style.display = 'inline';

                        return false;"
    ><img src="<?php echo CAM_FW_IMAGENS."look_on.gif"; ?>" /></a>
</div>
<?php
    }
?>
