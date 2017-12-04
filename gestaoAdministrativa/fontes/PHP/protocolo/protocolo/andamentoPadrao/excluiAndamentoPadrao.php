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
* Arquivo de implementação de andamento padrão
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 24723 $
$Name$
$Author: domluc $
$Date: 2007-08-13 18:15:24 -0300 (Seg, 13 Ago 2007) $

Casos de uso: uc-01.06.97
*/

include '../../../framework/include/cabecalho.inc.php';
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"               );
include (CAM_FRAMEWORK."legado/mascarasLegado.lib.php"              );
include '../andamento.class.php';
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
include (CAM_FRAMEWORK."legado/paginacaoLegada.class.php");
setAjuda('uc-01.06.97');

if (!(isset($_REQUEST["ctrl"]))) {
    $ctrl = 0;
} else {
    $ctrl = $_REQUEST["ctrl"];
}

$mascaraAssunto = pegaConfiguracao('mascara_assunto',5);
$mascaraSetor = pegaConfiguracao('mascara_setor',2);
$codClassificacao = $_REQUEST["codClassificacao"];
$codAssunto       = $_REQUEST["codAssunto"];
$variavel         = $_REQUEST["variavel"];
$valor            = $_REQUEST["valor"];
$codOrgao       = $_REQUEST["codOrgao"];

?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>
<?php

switch ($ctrl) {
case 0:

Sessao::write('ordem', 0);
?>

<script type="text/javascript">
<!--
function Valida()
{
    var mensagem = "";
    var erro = false;
    var campo;
    var campoaux;

    campo = document.frm.codClassificacao.value;
        if (campo == 'xxx') {
        mensagem += "@O campo de Classificação é obrigatório";
        erro = true;
    }
    campo = document.frm.codAssunto.value;
        if (campo == 'xxx') {
        mensagem += "@O campo de Assunto é obrigatório";
        erro = true;
    }
        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
        return !(erro);
}

function Salvar()
{
    if (Valida()) {
    document.frm.action = "excluiAndamentoPadrao.php?<?=Sessao::getId();?>&ctrl=2";
    document.frm.submit();
    }
}

function BuscaValores(variavel, valor)
{
   var targetTmp = document.frm.target;
   document.frm.target = "oculto";
   var actionTmp = document.frm.action;
   document.frm.action += "&variavel="+variavel+"&valor="+escape(valor)+"&ctrl=1";
   document.frm.submit();
   document.frm.action = actionTmp;
   document.frm.target = targetTmp;
}

function Limpar()
{
    document.frm.codClassifAssunto.value = "";
    document.frm.codClassificacao.options[0].selected = true;
    limpaSelect(document.frm.codAssunto,1);
}

//-->
</script>
<form name="frm" action="excluiAndamentoPadrao.php?<?=Sessao::getId();?>" method="POST" onSubmit="return false;">
<table width=100%>
   <tr>
      <td class="alt_dados" colspan=2>Assuntos de processo</td>
   </tr>
   <tr>
      <td class=label width=30% rowspan=3 title="Classificação e assunto de processo">Classificação/Assunto</td>
      <td class=field>
<?php
if ($codClassificacao and $codAssunto) {
    $arCodClassifAssunto =  validaMascaraDinamica($mascaraAssunto, $codClassificacao."-".$codAssunto);
    $codClassifAssunto = $arCodClassifAssunto[1];
} else {
    $codClassifAssunto = "";
}

?>
         <input type="text" name="codClassifAssunto" value="<?=$codClassifAssunto;?>" size="<?=strlen($mascaraAssunto);?>" maxlength="<?=strlen($mascaraAssunto);?>" value="<?=$codClassifAssunto?>" onKeyUp="JavaScript: mascaraDinamico('<?=$mascaraAssunto?>', this, event);" onChange="JavaScript: BuscaValores( 'codClassifAssunto', this.value )">
      </td>
   </tr>
   <tr>
      <td class=field>
      <select name="codClassificacao" onChange="JavaScript: BuscaValores( 'codClassificacao', this.value )" style="width: 200px">
         <option value="xxx">Selecione</option>
<?php
$sSQL = "SELECT * FROM sw_classificacao ORDER by nom_classificacao";
$dbEmp = new dataBaseLegado;
$dbEmp->abreBD();
$dbEmp->abreSelecao($sSQL);
$dbEmp->vaiPrimeiro();
$comboCla = "";
while (!$dbEmp->eof()) {
   $codClassificacaof  = trim($dbEmp->pegaCampo("cod_classificacao"));
   $nomClassificacaof  = trim($dbEmp->pegaCampo("nom_classificacao"));
   $dbEmp->vaiProximo();
   $comboCla .= "         <option value=".$codClassificacaof;
   if (isset($codClassificacao)) {
      if ($codClassificacaof == $codClassificacao)
         $comboCla .= " SELECTED";
   }
   $comboCla .= ">".$nomClassificacaof."</option>\n";
}
$dbEmp->limpaSelecao();
$dbEmp->fechaBD();
echo $comboCla;
?>
      </select>
      </td>
   </tr>
   <tr>
      <td class=field>
      <select name="codAssunto" onChange="JavaScript: BuscaValores( 'codAssunto', this.value )" style="width: 200px">
         <option value="xxx" SELECTED>Selecione</option>
<?php
if ((isset($codClassificacao)) AND ($codClassificacao != "xxx")) {
   $sSQL = "SELECT * FROM sw_assunto WHERE cod_classificacao = ".$codClassificacao." ORDER by nom_assunto";
   $dbEmp = new dataBaseLegado;
   $dbEmp->abreBD();
   $dbEmp->abreSelecao($sSQL);
   $dbEmp->vaiPrimeiro();
   $comboAss = "";
   while (!$dbEmp->eof()) {
      $codAssuntof  = trim($dbEmp->pegaCampo("cod_assunto"));
      $nomAssuntof  = trim($dbEmp->pegaCampo("nom_assunto"));
      $dbEmp->vaiProximo();
      $comboAss .= "         <option value=".$codAssuntof;
      if (isset($codAssunto)) {
         if ($codAssuntof == $codAssunto)
            $comboAss .= " SELECTED";
      }
      $comboAss .= ">".$nomAssuntof."</option>\n";
   }
   $dbEmp->limpaSelecao();
   $dbEmp->fechaBD();
   echo $comboAss;
}
?>
      </select>
      </td>
   </tr>
   <tr>
      <td class=field colspan=2>
          <input type="button" value="OK" name="ok" style="width: 70px" onClick="Salvar();" disabled>
          <input type="button" value="Limpar" style="width: 70px" onClick="Limpar();">
      </td>
   </tr>
</table>
</form>
<?php
break;
case 1:
$js .=  "f.ok.disabled = true;\n";
$codClassifAssunto = $_REQUEST["codClassifAssunto"];
$variavel          = $_REQUEST["variavel"];
switch ($variavel) {
    case 'codClassifAssunto':
        $codClassifAssuntof = validaMascaraDinamica($mascaraAssunto,$valor);        
        $js .= "f.codClassifAssunto.value = \"".$codClassifAssuntof[1]."\";\n";
        $variaveis = preg_split( "/[^a-zA-Z0-9]/", $codClassifAssunto );
        $codClassificacaof = $variaveis[0];
        $codAssuntof = $variaveis[1];
        $js .=  "var iContClass = 0;\n";
        $js .=  "var iTamClass = f.codClassificacao.options.length - 1;\n";
        $js .=  "while (iTamClass >= iContClass) {\n";
        $js .=  "    if ( f.codClassificacao.options[iContClass].value == ".(integer) $codClassificacaof.") {\n";
        $js .=  "        f.codClassificacao.options[iContClass].selected = true;\n";
        $js .=  "        break;\n";
        $js .=  "    }\n";
        $js .=  "    iContClass++;\n";
        $js .=  "}\n";
        $js .=  "if (iContClass > iTamClass) {\n";
        $js .=  "    f.codClassificacao.options[0].selected = true;\n";
        $js .=  "    limpaSelect(f.codAssunto,1); \n";
        $js .=  "}\n";
        //Faz o combo de Assunto
        if ($codClassificacaof === $codClassificacao) {
            $js .=  "var iContAss = 0;\n";
            $js .=  "var iTamAss = f.codAssunto.options.length - 1;\n";
            $js .=  "while (iTamAss >= iContAss) {\n";
            $js .=  "    if ( f.codAssunto.options[iContAss].value == ".(integer) $codAssuntof.") {\n";
            $js .=  "        f.codAssunto.options[iContAss].selected = true;\n";
            $js .=  "		  f.ok.disabled = false;\n";
            $js .=  "        break;\n";
            $js .=  "    }\n";
            $js .=  "    iContAss++;\n";
            $js .=  "}\n";
            $js .=  "if (iContAss > iTamAss) {\n";
            $js .=  "    f.codAssunto.options[0].selected = true;\n";
            $js .=  "}\n";
        } elseif($codClassificacaof != ""){
            $sSQL  = "SELECT cod_assunto, cod_classificacao, nom_assunto FROM sw_assunto ";
            $sSQL .= "WHERE cod_classificacao = '".$codClassificacaof."' ORDER by nom_assunto";
            //echo  $sSQL;
            $dbAss = new dataBaseLegado;
            $dbAss->abreBD();
            $dbAss->abreSelecao($sSQL);
            $dbAss->vaiPrimeiro();
            $contAss = 1;
            $js .= "limpaSelect(f.codAssunto,1); \n";
            while (!$dbAss->eof()) {
                $codAssuntoW  = trim($dbAss->pegaCampo("cod_assunto"));
                $nomAssuntoW  = trim($dbAss->pegaCampo("nom_assunto"));
                $dbAss->vaiProximo();
                if ($codAssuntoW == $codAssuntof) {
                    $selected = ", true";
                    $js .= "f.ok.disabled = false;\n";
                } else {
                    $selected = "";
                }
                $js .= "f.codAssunto.options[$contAss] = new Option('".$nomAssuntoW."','".$codAssuntoW."'".$selected."); \n";
                $contAss++;
            }
            $dbAss->limpaSelecao();
            $dbAss->fechaBD();
        }else{
            $js .= "limpaSelect(f.codAssunto,1); \n";
            $js .= "f.codClassifAssunto.value = ''; \n";
        }
    break;
    case 'codClassificacao':
        if ($valor == "xxx") {
            $codClassifAssuntof = validaMascaraDinamica($mascaraAssunto,"");
            $default = ", true";
            $js .= "f.codClassifAssunto.value = '".$codClassifAssuntof[1]."';\n";
            $js .= "limpaSelect(f.codAssunto,0); \n";
            $js .= "f.codAssunto.options[0] = new Option('Selecione','xxx'".$default.");\n";
        } else {
            //Faz o combo de Assunto
            $sSQL  = "SELECT cod_assunto, cod_classificacao, nom_assunto FROM sw_assunto ";
            $sSQL .= "WHERE cod_classificacao = ".$valor." ORDER by nom_assunto";
            //echo  $sSQL;
            $codClassifAssuntof = validaMascaraDinamica($mascaraAssunto,$valor);
            $js .= "f.codClassifAssunto.value = '".$codClassifAssuntof[1]."';\n";
            $dbAss = new dataBaseLegado;
            $dbAss->abreBD();
            $dbAss->abreSelecao($sSQL);
            $dbAss->vaiPrimeiro();
            $contAss = 1;
            $js .= "limpaSelect(f.codAssunto,1); \n";
            while (!$dbAss->eof()) {
                $codAssuntof  = trim($dbAss->pegaCampo("cod_assunto"));
                $nomAssuntof  = trim($dbAss->pegaCampo("nom_assunto"));
                $dbAss->vaiProximo();
                $js .= "f.codAssunto.options[$contAss] = new Option('".$nomAssuntof."','".$codAssuntof."'); \n";
                $contAss++;
            }
            if ($contAss == 1) {
                $js .= "limpaSelect(f.codAssunto,0); \n";
                $js .= "f.codAssunto.options[0] = new Option('Selecione','xxx'".$default.");\n";
            }
            $dbAss->limpaSelecao();
            $dbAss->fechaBD();
        }
    break;
    case 'codAssunto':
        //Faz o combo de Assunto
        $sSQL  = "SELECT cod_assunto, cod_classificacao, nom_assunto FROM sw_assunto ";
        $sSQL .= " WHERE cod_classificacao = ".$codClassificacao." ORDER by nom_assunto";
        //echo  $sSQL;
        $dbAss = new dataBaseLegado;
        $dbAss->abreBD();
        $dbAss->abreSelecao($sSQL);
        $dbAss->vaiPrimeiro();
        if ($codAssunto == "xxx") {
            $codAssunto = "00";
        }
        $valor = $codClassificacao."-".$codAssunto;
        $codClassifAssuntof = validaMascaraDinamica($mascaraAssunto, $valor);
        $js .= "f.codClassifAssunto.value = '".$codClassifAssuntof[1]."';\n";
        $contAss = 1;
        while (!$dbAss->eof()) {
            $codAssuntof  = trim($dbAss->pegaCampo("cod_assunto"));
            $nomAssuntof  = trim($dbAss->pegaCampo("nom_assunto"));
            $dbAss->vaiProximo();
            $js .= "f.codAssunto.options[$contAss] = new Option('".$nomAssuntof."','".$codAssuntof."'); \n";
            if ($codAssuntof == $codAssunto) {
                $js .= "f.codAssunto.options[$contAss].selected = true; \n";
                $js .= "f.ok.disabled = false;\n";
            }
            $contAss++;
        }
        if ($contAss == 1) {
            $js .= "limpaSelect(f.codAssunto,1); \n";
        }
        $dbAss->limpaSelecao();
        $dbAss->fechaBD();
    break;
    }
executaFrameOculto($js);
break;
case 2:
    $sQuebra = "\n";
    $sSQL  = "    SELECT sw_andamento_padrao.ordem                                                 ".$sQuebra;
    $sSQL .= "         , orgao.cod_orgao                                                           ".$sQuebra;
    $sSQL .= "         , vw_orgao_nivel.orgao                                                      ".$sQuebra;
    $sSQL .= "         , recuperaDescricaoOrgao(orgao.cod_orgao, now()::date) as nom_orgao         ".$sQuebra;
    $sSQL .= "         , sw_andamento_padrao.descricao as despacho                                 ".$sQuebra;
    $sSQL .= "         , sw_andamento_padrao.num_dia                                               ".$sQuebra;
    $sSQL .= "         , sw_andamento_padrao.num_passagens                                         ".$sQuebra;
    $sSQL .= "         , sw_assunto.nom_assunto                                                    ".$sQuebra;
    $sSQL .= "         , sw_classificacao.nom_classificacao                                        ".$sQuebra;
    $sSQL .= "      FROM sw_andamento_padrao                                                       ".$sQuebra;
    $sSQL .= "           JOIN sw_assunto                                                           ".$sQuebra;
    $sSQL .= "             ON sw_assunto.cod_classificacao = sw_andamento_padrao.cod_classificacao ".$sQuebra;
    $sSQL .= "            AND sw_assunto.cod_assunto = sw_andamento_padrao.cod_assunto             ".$sQuebra;
    $sSQL .= "           JOIN sw_classificacao                                                     ".$sQuebra;
    $sSQL .= "             ON sw_classificacao.cod_classificacao = sw_assunto.cod_classificacao    ".$sQuebra;
    $sSQL .= "           JOIN organograma.orgao                                                    ".$sQuebra;
    $sSQL .= "             ON orgao.cod_orgao = sw_andamento_padrao.cod_orgao                      ".$sQuebra;
    $sSQL .= "           JOIN organograma.orgao_nivel                                              ".$sQuebra;
    $sSQL .= "             ON orgao_nivel.cod_orgao = orgao.cod_orgao                              ".$sQuebra;
    $sSQL .= "           JOIN organograma.vw_orgao_nivel                                           ".$sQuebra;
    $sSQL .= "             ON vw_orgao_nivel.cod_orgao       = orgao.cod_orgao                     ".$sQuebra;
    $sSQL .= "            AND vw_orgao_nivel.cod_organograma = orgao_nivel.cod_organograma         ".$sQuebra;
    $sSQL .= "     WHERE sw_andamento_padrao.cod_classificacao = ".$_REQUEST['codClassificacao']." ".$sQuebra;
    $sSQL .= "       AND sw_andamento_padrao.cod_assunto = ".$_REQUEST['codAssunto']."             ".$sQuebra;
    $sSQL .= "  GROUP BY sw_andamento_padrao.ordem                                                 ".$sQuebra;
    $sSQL .= "         , orgao.cod_orgao                                                           ".$sQuebra;
    $sSQL .= "         , nom_orgao                                                                 ".$sQuebra;
    $sSQL .= "         , vw_orgao_nivel.orgao                                                      ".$sQuebra;
    $sSQL .= "         , sw_andamento_padrao.descricao                                             ".$sQuebra;
    $sSQL .= "         , sw_andamento_padrao.num_dia                                               ".$sQuebra;
    $sSQL .= "         , sw_assunto.nom_assunto                                                    ".$sQuebra;
    $sSQL .= "         , sw_classificacao.nom_classificacao                                        ".$sQuebra;
    $sSQL .= "         , sw_andamento_padrao.num_passagens                                         ".$sQuebra;

    //echo $sSQL;
    $sSQL = str_replace("<br>", "", $sSQL);
    $dbEmp = new dataBaseLegado;

    $paginacao = new paginacaoLegada;
    Sessao::write('sSQLs', $sSQL);
    $registros = 10;
    $paginacao->pegaDados(Sessao::read('sSQLs'),$registros);
    $paginacao->pegaPagina($pagina);
    $paginacao->complemento = "&codClassificacao=".$codClassificacao."&codAssunto=".$codAssunto."&ctrl=".$ctrl;
    $paginacao->geraLinks();
    $paginacao->pegaOrder("sw_andamento_padrao.ordem", "");
    $sSQL2 = $paginacao->geraSQL();
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL2);
    $dbEmp->fechaBd();
    Sessao::remove('sSQLs');

    $stNomClassificacao = trim($dbEmp->pegaCampo("nom_classificacao"));
    $stNomAssunto = trim($dbEmp->pegaCampo("nom_assunto"));
    $listaTramitePadrao = array();
    while ( !$dbEmp->eof() ) {
    if ( trim($dbEmp->pegaCampo("ordem")) ) {
        $listaTmp = array( trim($dbEmp->pegaCampo("ordem") ),
                           trim($dbEmp->pegaCampo("orgao") ),
                           trim($dbEmp->pegaCampo("nom_orgao")),
                           trim($dbEmp->pegaCampo("num_dia")),
               trim($dbEmp->pegaCampo("cod_orgao")),
               trim($dbEmp->pegaCampo("num_passagens"))
                        );
        $listaTramitePadrao[]= $listaTmp;
    }
    $dbEmp->vaiProximo();
    }
    ?>

    <script type="text/javascript">
    <!--
    function Valida()
    {
    var mensagem = "";
    var erro = false;
    var erroSetor = false;
    var campo;
    var campoaux;

    campo = document.frm.descricao.value.length;
    if (campo == 0) {
        mensagem += "@Campo Descrição inválido! ()";
        erro = true;
    }

    campo = document.frm.numDia.value;length;
    if (campo == 0) {
        mensagem += "@Campo Número de Dias inválido! ()";
        erro = true;
    }

    if (erro) {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
    }

    return !(erro);
    }

    function Salvar()
    {
    if (Valida()) {
        document.frm.action = "excluiAndamentoPadrao.php?<?=Sessao::getId();?>&ctrl=2&codAssunto=<?=$codAssunto;?>&codClassificacao=<?=$codClassificacao;?>";
        document.frm.submit();
    }
    }

    function Limpar()
    {
        document.frm.codMasSetor.value = "";
        document.frm.descricao.value = "";
        document.frm.numDia.value = "";
    }

    function BuscaValores(variavel, valor)
    {
    var targetTmp = document.frm.target;
    document.frm.target = "oculto";
    var actionTmp = document.frm.action;
    document.frm.action += "&variavel="+variavel+"&valor="+valor+"&ctrl=3";
    document.frm.submit();
    document.frm.action = actionTmp;
    document.frm.target = targetTmp;
    }

    function trocaOrdem(posNova , posAtual)
    {
    document.frm.posicaoAtual.value = posAtual;
        document.frm.posicaoNova.value = posNova;
        document.frm.ctrl.value = 3;
        document.frm.submit();
    }
    //-->
    </script>
    <form name="frm" action="excluiAndamentoPadrao.php?<?=Sessao::getId();?>" method="POST" onSubmit="return false;">
    <input type="hidden" name="codAssunto" value="<?=$codAssunto;?>">
    <input type="hidden" name="codClassificacao" value="<?=$codClassificacao;?>">
    <input type="hidden" name="ctrl" value="">
    <table width=100%>
    <tr>
        <td colspan="2" class="alt_dados">Assuntos de processo</td>
    </tr>
    <tr>
        <td rowspan="3" class="label" width="30%">Classificação/Assunto</td>
    <?php
    $arCodClassifAssunto = validaMascaraDinamica($mascaraAssunto,$codClassificacao."-".$codAssunto);
    ?>
        <td class="field"><?=$arCodClassifAssunto[1];?></td>
    </tr>
    <tr>
        <td class="field"><?=$stNomClassificacao;?></td>
    </tr>
    <tr>
        <td class="field"><?=$stNomAssunto;?></td>
    </tr>
    <?php

    if ( count($listaTramitePadrao) ) {
    ?>
    <table width=100%>
    <tr>
        <td colspan="9" class="alt_dados">Registros de trâmite</td>
    </tr>
    <tr>
        <td class="labelleft" width="5%">&nbsp;</td>
        <td class="labelleft" width="8%">Ordem</td>
        <td class="labelleft" width="12%">Código</td>
        <td class="labelleft" width="65%">Localização</td>
        <td class="labelleft" width="10%">Qtd. dias</td>
        <td class="labelleft">&nbsp;</td>
    </tr>
    <?php
    $inCont = $paginacao->contador() ;
    foreach ($listaTramitePadrao as $arTramitePadrao) {
    ?>
        <tr>
            <td class="labelcenter"><?=$inCont++;?></td>
            <td class="show_dados_right"><?=$arTramitePadrao[0];?></td>
            <td class="show_dados"><?=$arTramitePadrao[1];?></td>
            <td class="show_dados"><?=$arTramitePadrao[2];?></td>
            <td class="show_dados"><?=$arTramitePadrao[3];?></td>

            <td class="botao">
        <center>
<?php

echo  "<a href='#' onClick=\"alertaQuestao('".CAM_PROTOCOLO."protocolo/andamentoPadrao/excluiAndamentoPadrao.php?".Sessao::getId()."','codOrgao','".$arTramitePadrao[4].urlencode("&")."ctrl=3".urlencode("&")."codAssunto=".$codAssunto.urlencode("&")."codClassificacao=".$codClassificacao.urlencode("&")."numPassagens=".$arTramitePadrao[5].urlencode("&")."ordem=".$arTramitePadrao[0].urlencode("&")."orgao=".$arTramitePadrao[1]."','Trâmite Padrão para ".$stNomAssunto."','sn_excluir', '".Sessao::getId()."');\">
       <img src=".CAM_FW_IMAGENS."btnexcluir.gif width=22 height=22 border=0>";
?>
            </center>
        </td>
    </tr>
    <?php
    }//endforeach
    echo "</table>\n";
    echo "<table width=450 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
    echo "</font></tr></td></table>";
    }
    ?>
    <input type="hidden" name="posicaoAtual" value="">
    <input type="hidden" name="posicaoNova" value="">
    </form>
<?php
break;
case 3:
    $ordem =  $_REQUEST["ordem"];
    $numPassagens = $_REQUEST["numPassagens"];

    $sQuebra = "<br>";
    $condDelete  = " WHERE ".$sQuebra;
    $condDelete .= "     COD_CLASSIFICACAO = ".$codClassificacao." AND ".$sQuebra;
    $condDelete .= "     COD_ASSUNTO = ".$codAssunto." AND ".$sQuebra;
    $condDelete .= "     ORDEM = ".$ordem." ".$sQuebra;

    $condNumPassagens  = " WHERE ".$sQuebra;
    $condNumPassagens .= " cod_classificacao = ".$codClassificacao." AND ".$sQuebra;
    $condNumPassagens .= " cod_assunto = ".$codAssunto." AND ".$sQuebra;
    $condNumPassagens .= " cod_orgao = ".$codOrgao." AND ".$sQuebra;
    $condNumPassagens .= " num_passagens > ".$numPassagens." ".$sQuebra;

    $obAndamento = new andamento;
    $obAndamento->classificacao = $codClassificacao;
    $obAndamento->assunto = $codAssunto;
    $boErro = $obAndamento->deletaAndamentoCompleto( $condDelete, $condNumPassagens, $ordem );

if ($boErro) {
   Sessao::write('ordem', $ordem);
   $audicao = new auditoriaLegada;
   $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $_REQUEST["orgao"]);
   $audicao->insereAuditoria();
   alertaAviso($PHP_SELF."?".Sessao::getId().'&ctrl=2&codAssunto='.$codAssunto.'&codClassificacao='.$codClassificacao.'&msg=1',$_REQUEST["orgao"] ,"excluir" ,"aviso");
} else {
   alertaAviso($PHP_SELF."?".Sessao::getId().'&ctrl=2&codAssunto='.$codAssunto.'&codClassificacao='.$codClassificacao.'&msg=2',$_REQUEST["orgao"] ,"n_excluir","erro");
}
break;
}
include '../../../framework/include/rodape.inc.php';

?>
