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
              uc-03.04.14
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

/**************************************************************************/
/**** Pega os códigos de erro que vieram                                ***/
/**************************************************************************/
$msgs = "";
$imagem = "erroa.png";

$nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao", "where cod_acao = ".Sessao::read('acao'));

$chamada       = $request->get('chamada');
$chave         = $request->get('chave');
$valor         = $request->get('valor');
$tipo          = $request->get('tipo');
$pagQuestao    = $request->get('pagQuestao');
$obj           = $request->get('obj');
$stDescQuestao = $request->get('stDescQuestao');

//Retira as barras das strings
$pagQuestao = str_replace("*_*", "&", $pagQuestao);
$pagQuestao = str_replace("*-*-*", "?", $pagQuestao);
$arPagQuestao = explode('&',$pagQuestao);

foreach ($arPagQuestao as $stKey => $stValue) {

    $arKey = explode('=',$stValue);
    if ($arKey[0] == 'stDescQuestao') {
        $stDescQuestao = $arKey[1];
    }
    if($arKey[0] == 'nomAcao')
        $nomAcao= $arKey[1];

    if($arKey[0] == 'frameDestino')
        $frameDestino = $arKey[1];
}

if (!isset($frameDestino)) {
    $frameDestino = "";
}

if (stripslashes($stDescQuestao) > '' ) {
   $obj = stripslashes($stDescQuestao);
} else {
   $obj = stripslashes( $obj );
}

$frameDestino = $frameDestino ? $frameDestino : 'telaPrincipal';

$pag = $pagQuestao;

switch ($tipo) {
    case "incluir": 
        $msgs .= $obj." incluído com sucesso";
    break;

    case "n_incluir":
        $msgs .= "Não foi possível incluir ".$obj.", contate o Administrador";
    break;

    case "alterar":
        $msgs .= $obj." alterado com sucesso";
    break;

    case "n_alterar":
        $msgs .= "Não foi possível alterar ".$obj.", contate o Administrador";
    break;

    case "sn_excluir":
        $msgs .= "Confirma ".$nomAcao." (".$obj.") ?";
        $imagem = "botao_confirma.png";
    break;

    case "sn_cancelar":
        $msgs .= "Confirma ".$nomAcao." (".$obj.") ?";
        $imagem = "botao_confirma.png";
    break;

    case "pp_excluir":
        $msgs .= "Confirma ".$nomAcao." (".$obj.") ?";
        $imagem = "botao_confirma.png";
    break;

    case "cc":
        $msgs .= $obj;
    break;

    case "excluir":
        $msgs .= $obj." excluído com sucesso";
    break;

    case "n_excluir":
        $msgs .= "Não foi possível excluir ".$obj.", contate o Administrador";
    break;

    case "cancelar":
        $msgs .= $obj." cancelado com sucesso";
    break;

    case "n_cancelar":
        $msgs .= "Não foi possível cancelar ".$obj.", contate o Administrador";
    break;

    case "form":
        $ch_mensagem = substr($obj, 1);
        $rel_mensagem = str_replace("@","\n","$ch_mensagem");
        $msgs .= $rel_mensagem;
    break;

    case "unica":
        $msgs .= $obj;
    break;

    case "ccform":
        $msgs .= $obj;
        $imagem = "botao_confirma.png";
    break;

    case "sn":
        $msgs .= $obj;
        $imagem = "botao_confirma.png";
    break;

    case "pp":
        $msgs .= $obj;
        $imagem = "botao_confirma.png";
    break;

}

switch ($chamada) {
    case "erro":
    $sTitulo = "ERRO";
    break;

    case "aviso":
    $sTitulo = "Aviso";
    break;

    case "cc":
    case "sn":
    case "pp":
    case "decisao":
    case "ccform":
    case "oculto":
    case "decisao_sem_acao":
        $sTitulo = "Confirmação";
    break;
}

?>

<script type="text/javascript"><!--
var skipcycle = false

function fcsOnMe()
{
    if (!skipcycle) {
        window.focus();
    }
    mytimer = setTimeout('fcsOnMe()', 500);
}

function LiberaFramesPopUp(){
    if (typeof jq == 'undefined') {
        var jq = window.opener.parent.frames["telaPrincipal"].jQuery;
    }

    jq("input:button").each(function(){ this.disabled = false; });

    jq("input#Ok").removeAttr('readonly');

    for(i=1;i<4;i++){
        jq('div#containerPopUp',window.opener.parent.frames[i].document).each(function(){
            jq(this).remove();
        });
        
        jq('html',window.opener.parent.frames[i].document).css({'overflow':'auto'});
    }   
}

</script>

</head>
<body class="tela_erro" bgcolor=#FFFFFF leftmargin="0" topmargin="0" onload = "mytimer = setTimeout('fcsOnMe()', 500);">

<center>
<table width=270>
    <tr>
        <td width="23" height="23"><img src="<?=CAM_FW_IMAGENS.$imagem;?>" width=23 height=23 border=0></td>
        <td><font color="#4a6491" face="Futura, Arial, Helvetica, sans-serif" size=4><b>&nbsp;<?=$sTitulo?></b></font></td>
    </tr>
    <tr>
        <td colspan=2>
        <?php
        if ($tipo == "decisao") {
        ?>
            <textarea disabled=true cols=36 rows=5 class="tela_erro"></textarea>
        <?php
        } else {
        ?>
        <textarea disabled=true cols=36 rows=5 style="width: 331px; height: 37px;" class="tela_erro"><?=$msgs?></textarea>
        <?php
        }
        ?>
        </td>
    </tr>
    <tr>
        <td colspan=2 align=center>
<?php
switch ($chamada) {
    case "erro":
    case "aviso":
        echo '<input type="button" value="OK" style="font-size : 14px; color : #4A6491; font-weight : bold; background-color : #FFFFFF; height: 30px; width : 100px;" onClick="LiberaFramesPopUp(); window.close();">';
    break;

    case "cc":
        echo "
        <script type='text/javascript'>
        function mudaPagina()
        {
        sPag = '$pag?".Sessao::getId()."&".$chave."=".$valor."';
        window.opener.parent.frames['$frameDestino'].document.location.replace(sPag);
        window.close();
        }
        </script>
        <input type='button' value='Confirmar' style='font-size : 14px; color : #4A6491; font-weight : bold; background-color : #FFFFFF; height: 30px; width : 100px;'
        onClick='mudaPagina();'> <input type='button' value='Cancelar' style='font-size : 14px; color : #4A6491; font-weight : bold; background-color : #FFFFFF; height: 30px; width : 100px;' onClick='LiberaFramesPopUp(); window.close();'>";
    break;

    case "sn":
        echo " 
        <script type='text/javascript'>
            function mudaPagina() {
                sPag = '$pag&".$chave."=".$valor."';
                window.opener.parent.frames['$frameDestino'].document.location.replace(sPag);
                window.close();
            }
        </script>
        <input type='button' value='Sim' style='font-size : 14px; color : #4A6491; font-weight : bold; background-color : #FFFFFF; height: 30px; width : 100px;' onClick='mudaPagina();'> 
        <input type='button' value='Não' style='font-size : 14px; color : #4A6491; font-weight : bold; background-color : #FFFFFF; height: 30px; width : 100px;' onClick='LiberaFramesPopUp(); window.close();'>";
    break;

    case "pp":
        echo "
        <script type='text/javascript'>
        function mudaPagina()
        {
        sPag = '$pag&".$chave."=".$valor."';
        window.opener.document.location.replace(sPag);
        window.close();
        }
        </script>
        <input type='button' value='Sim' style='font-size : 14px; color : #4A6491; font-weight : bold; background-color : #FFFFFF; height: 30px; width : 100px;'
        onClick='mudaPagina();'> <input type='button' value='Não' style='font-size : 14px; color : #4A6491; font-weight : bold; background-color : #FFFFFF; height: 30px; width : 100px;' onClick='LiberaFramesPopUp(); window.close();'>";
    break;

    case "decisao":
        echo "
        <script type='text/javascript'>
        function mudaPagina()
        {
        sPag = '$pag?".Sessao::getId()."';
        window.opener.parent.frames['$frameDestino'].document.location.replace(sPag);
        window.close();
        }
        </script>
        <input type='button' value='Sim' style='font-size : 14px; color : #4A6491; font-weight : bold; background-color : #FFFFFF; height: 30px; width : 100px;' onClick='window.close()'> <input type='button' value='Não' style='font-size : 14px; color : #4A6491; font-weight : bold; background-color : #FFFFFF; height: 30px; width : 100px;' onClick='mudaPagina();'>";
    break;

    case "ccform":
        echo "
        <script type='text/javascript'>
        function mudaPagina()
        {
        window.opener.parent.frames['$frameDestino'].document.frm.submit();
        window.close();
        }
        </script>
        <input type='button' value='Confirmar' style='font-size : 14px; color : #4A6491; font-weight : bold; background-color : #FFFFFF; height: 30px; width : 100px;'
        onClick='mudaPagina();'> <input type='button' value='Cancelar' style='font-size : 14px; color :#4A6491; font-weight : bold; background-color : #FFFFFF; height: 30px; width : 100px;' onClick='window.close();'>";
    break;

    case "oculto":
        echo "
        <script type='text/javascript'>
        function mudaPagina()
        {
        window.opener.parent.frames['oculto'].document.frm.submit();
        window.close();
        }
        </script>
        <input type='button' value='Sim' style='font-size : 14px; color : #4A6491; font-weight : bold; background-color : #FFFFFF; height: 30px; width : 100px;'
        onClick='mudaPagina();'> <input type='button' value='Não' style='font-size : 14px; color : #4A6491; font-weight : bold; background-color : #FFFFFF; height: 30px; width : 100px;' onClick='window.close();'>";
    break;
}
?>
        </td>
    </tr>
</table>
</center>
</body>
</html>
