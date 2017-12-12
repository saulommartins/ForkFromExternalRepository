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
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
include (CAM_FRAMEWORK."legado/mascarasLegado.lib.php"   );
include '../andamento.class.php';
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
setAjuda('uc-01.06.97');

if (!(isset($_REQUEST["ctrl"]))) {
    $ctrl = 0;
} else {
    $ctrl = $_REQUEST["ctrl"];
}

$mascaraAssunto = pegaConfiguracao('mascara_assunto',5);
$mascaraSetor = pegaConfiguracao('mascara_setor',2);

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
    document.frm.action = "alteraAndamentoPadrao.php?<?=Sessao::getId();?>&ctrl=2";
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
<form name="frm" action="alteraAndamentoPadrao.php?<?=Sessao::getId();?>" method="POST" onSubmit="return false;">
<table width=100%>
<tr>
    <td class="alt_dados" colspan=2>Assuntos de processo</td>
</tr>
<tr>
    <td class=label width=30% rowspan=3 title="Classificação e assunto de processo">Classificação/Assunto</td>
    <td class=field>
<?php
if ($_REQUEST["codClassificacao"] and $_REQUEST["codAssunto"]) {
    $arCodClassifAssunto =  validaMascaraDinamica($mascaraAssunto, $_REQUEST["codClassificacao"]."-".$_REQUEST["codAssunto"]);
    $codClassifAssunto = $arCodClassifAssunto[1];
} else {
    $codClassifAssunto = "";
}
?>
        <input type="text" name="codClassifAssunto" value="<?=$_REQUEST["codClassifAssunto"];?>" size="<?=strlen($mascaraAssunto);?>" maxlength="<?=strlen($mascaraAssunto);?>" value="<?=$codClassifAssunto?>" onKeyUp="JavaScript: mascaraDinamico('<?=$mascaraAssunto?>', this, event);" onChange="JavaScript: BuscaValores( 'codClassifAssunto', this.value )">
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
if (isset($_REQUEST["codClassificacao"])) {
    if ($codClassificacaof == $_REQUEST["codClassificacao"])
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
if ((isset($_REQUEST["codClassificacao"])) AND ($_REQUEST["codClassificacao"] != "xxx")) {
$sSQL = "SELECT * FROM sw_assunto WHERE cod_classificacao = ".$_REQUEST["codClassificacao"]." ORDER by nom_assunto";
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
    if (isset($_REQUEST["codAssunto"])) {
        if ($codAssuntof == $_REQUEST["codAssunto"])
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
        <input type="button" value="OK"  name="ok" style="width: 70px" onClick="Salvar();" disabled>
        <input type="button" value="Limpar" style="width: 70px" onClick="Limpar();">
    </td>
</tr>
</table>
</form>
<?php
break;
case 1:
$codClassifAssunto = $_REQUEST["codClassifAssunto"];
$js .=  "f.ok.disabled = true;\n";
switch ($_REQUEST["variavel"]) {
    case 'codClassifAssunto':
        $codClassifAssuntof = validaMascaraDinamica($mascaraAssunto,$_REQUEST["valor"]);
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
        if ($_REQUEST["valor"] == "xxx") {
            $codClassifAssuntof = validaMascaraDinamica($mascaraAssunto,"");
            $default = ", true";
            $js .= "f.codClassifAssunto.value = '".$codClassifAssuntof[1]."';\n";
            $js .= "limpaSelect(f.codAssunto,0); \n";
            $js .= "f.codAssunto.options[0] = new Option('Selecione','xxx'".$default.");\n";
        } else {
            //Faz o combo de Assunto
            $sSQL  = "SELECT cod_assunto, cod_classificacao, nom_assunto FROM sw_assunto ";
            $sSQL .= "WHERE cod_classificacao = ".$_REQUEST["valor"]." ORDER by nom_assunto";
            //echo  $sSQL;
            $codClassifAssuntof = validaMascaraDinamica($mascaraAssunto,$_REQUEST["valor"]);
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
        $sSQL .= " WHERE cod_classificacao = ".$_REQUEST["codClassificacao"]." ORDER by nom_assunto";
        //echo  $sSQL;
        $dbAss = new dataBaseLegado;
        $dbAss->abreBD();
        $dbAss->abreSelecao($sSQL);
        $dbAss->vaiPrimeiro();
        if ($codAssunto == "xxx") {
            $codAssunto = "00";
        }
        $valor = $_REQUEST["codClassificacao"]."-".$_REQUEST["codAssunto"];
        $codClassifAssuntof = validaMascaraDinamica($mascaraAssunto, $valor);
        $js .= "f.codClassifAssunto.value = '".$codClassifAssuntof[1]."';\n";
        $contAss = 1;
        while (!$dbAss->eof()) {
            $codAssuntof  = trim($dbAss->pegaCampo("cod_assunto"));
            $nomAssuntof  = trim($dbAss->pegaCampo("nom_assunto"));
            $dbAss->vaiProximo();
            $js .= "f.codAssunto.options[$contAss] = new Option('".$nomAssuntof."','".$codAssuntof."'); \n";
            if ($codAssuntof == $_REQUEST["codAssunto"]) {
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
    $sSQL .= "  ORDER BY sw_andamento_padrao.ordem                                                 ".$sQuebra;

    //echo $sSQL;
    $sSQL = str_replace("<br>", "", $sSQL);

    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();

    $stNomClassificacao = trim($dbEmp->pegaCampo("nom_classificacao"));
    $stNomAssunto = trim($dbEmp->pegaCampo("nom_assunto"));
    $listaTramitePadrao = array();

    while ( !$dbEmp->eof() ) {
    if ( trim($dbEmp->pegaCampo("ordem")) ) {
        $listaTmp = array( trim($dbEmp->pegaCampo("ordem") ),
                            trim($dbEmp->pegaCampo("orgao") ),
                            trim($dbEmp->pegaCampo("nom_orgao")),
                            trim($dbEmp->pegaCampo("num_dia")),
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
/*
    campo = document.frm.codOrgao.value;
    if (campo == 'xxx') {
        erroSetor = true;
    }
          */
    campo = document.frm.codUnidade.value;
    if (campo == 'xxx') {
        erroSetor = true;
    }

    campo = document.frm.codDepartamento.value;
    if (campo == 'xxx') {
        erroSetor = true;
    }

    campo = document.frm.codSetor.value;
    if (campo == 'xxx') {
    }

    if (erroSetor) {
        mensagem += "@Campo Setor inválido!("+document.frm.codMasSetor.value+") ";
        erro = true;
    }

    campo = trim( document.frm.descricao.value );
    if (campo == "") {
        mensagem += "@Campo Descrição inválido!()";
        erro = true;
    }

    campo = trim( document.frm.numDia.value );
    if (campo == "") {
        mensagem += "@Campo Quantidade de dias inválido!()";
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
        document.frm.action = "alteraAndamentoPadrao.php?<?=Sessao::getId();?>&ctrl=2&codAssunto=<?=$_REQUEST["codAssunto"];?>&codClassificacao=<?=$_REQUEST["codClassificacao"];?>";
        document.frm.submit();
    }
    }

    function Limpar()
    {
        document.frm.codMasSetor.value = "";
//        document.frm.codOrgao.options[0].selected = true;
        limpaSelect(document.frm.codUnidade,1);
        limpaSelect(document.frm.codDepartamento,1);
        limpaSelect(document.frm.codSetor,1);
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
    <form name="frm" action="alteraAndamentoPadrao.php?<?=Sessao::getId();?>" method="POST" onSubmit="return false;">
    <input type="hidden" name="codAssunto" value="<?=$_REQUEST["codAssunto"];?>">
    <input type="hidden" name="codClassificacao" value="<?=$_REQUEST["codClassificacao"];?>">
    <input type="hidden" name="ctrl" value="">
    <table width=100%>
    <tr>
        <td colspan="2" class="alt_dados">Assuntos de processo</td>
    </tr>
    <tr>
        <td rowspan="3" class="label" width="30%">Classificação/Assunto</td>
    <?php
    $arCodClassifAssunto = validaMascaraDinamica($mascaraAssunto,$_REQUEST["codClassificacao"]."-".$_REQUEST["codAssunto"]);
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
        <td class="labelleft" width="10%">Código</td>
        <td class="labelleft" width="64%">Localização</td>
        <td class="labelleft" width="10%">Qtd. dias</td>
        <td class="labelleft">&nbsp;</td>
    </tr>
    <?php
    $inCont = 1;
    foreach ($listaTramitePadrao as $arTramitePadrao) {
?>
    <tr>
        <td class="labelcenter"><?=$inCont++;?></td>
        <td class="show_dados_right">
            <select name="ordem" onchange="JavaScript: trocaOrdem( this.value ,<?=$arTramitePadrao[0]?> );">
<?php
        $inContOrdem = 1;

        while ( count($listaTramitePadrao) >= $inContOrdem ) {
            if ($inContOrdem != $arTramitePadrao[0]) {
                echo "<option value='".$inContOrdem."'>".$inContOrdem."</option>\n";
            } else {
                echo "<option value='".$inContOrdem."' selected>".$inContOrdem."</option>\n";
            }
        $inContOrdem++;
        }
?>
        </select>
        </td>
        <td class="show_dados"><?=$arTramitePadrao[1]?></td>
        <td class="show_dados"><?=$arTramitePadrao[2]?></td>
        <td class="show_dados"><?=$arTramitePadrao[3]?></td>
        <td class="botao">
            <center>
<?php
echo  "            	<a href='alteraAndamentoPadrao.php?".Sessao::getId()."&codChaveClassAss=".rawurlencode($arCodClassifAssunto[1])."&codTramite=".rawurlencode($arTramitePadrao[1])."&numOrdem=".$arTramitePadrao[0]."&ctrl=4'>\n";
?>
                    <img src='<?=CAM_FW_IMAGENS."btneditar.gif";?>' border=0>
                </a>
            </center>
        </td>
    </tr>
    <?php
    }//endforeach
    echo "</table>\n";
    }
    ?>
    <input type="hidden" name="posicaoAtual" value="">
    <input type="hidden" name="posicaoNova" value="">
    </form>
<?php
include '../../../framework/include/rodape.inc.php';
break;
case 3:
$obAndamento = new andamento;
$sSQL  = " SELECT * FROM sw_andamento_padrao WHERE COD_CLASSIFICACAO = ".$_REQUEST["codClassificacao"]." AND ";
$sSQL .= " COD_ASSUNTO = ".$_REQUEST["codAssunto"]." AND ORDEM = ".$_REQUEST["posicaoAtual"]." ";
$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$dbConfig->abreSelecao($sSQL);
$dbConfig->fechaBd();
if ( !$dbConfig->eof() ) {
    $obAndamento->assunto = $dbConfig->pegaCampo('cod_assunto');
    $obAndamento->classificacao = $dbConfig->pegaCampo('cod_classificacao');
    $obAndamento->codOrgao = $dbConfig->pegaCampo('cod_orgao');
    $obAndamento->numPassagens = $dbConfig->pegaCampo('num_passagens');
    $obAndamento->descricao = $dbConfig->pegaCampo('descricao');
    $obAndamento->ordem = $dbConfig->pegaCampo('ordem');
    $obAndamento->numDia = $dbConfig->pegaCampo('num_dia');
    if ($_REQUEST["posicaoAtual"] > $_REQUEST["posicaoNova"]) {
        $boErro = $obAndamento->alteraOrdemAsc( $_REQUEST["posicaoAtual"], $_REQUEST["posicaoNova"] );
    } else {
        $boErro = $obAndamento->alteraOrdemDesc( $_REQUEST["posicaoAtual"], $_REQUEST["posicaoNova"] );
    }
} else {
    $boErro = false;
}

if ($boErro) {
    Sessao::write('ordem', $ordem);
    $audicao = new auditoriaLegada;
    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $_REQUEST["codClassificacao"]."-".$_REQUEST["codAssunto"]);
    $audicao->insereAuditoria();
    alertaAviso($PHP_SELF."?".Sessao::getId().'&ctrl=2&codAssunto='.$_REQUEST["codAssunto"].'&codClassificacao='.$_REQUEST["codClassificacao"].'&msg=1',$_REQUEST["hdnUltimoOrgaoSelecionado"] ,"alterar" ,"aviso");
} else {
    alertaAviso($PHP_SELF."?".Sessao::getId().'&ctrl=2&codAssunto='.$_REQUEST["codAssunto"].'&codClassificacao='.$_REQUEST["codClassificacao"].'&msg=2',$_REQUEST["hdnUltimoOrgaoSelecionado"],"n_alterar","erro");
}
break;
case 4:
    $arCodChaveAss = preg_split( '/[^0-9a-zA-Z]/', $_REQUEST["codChaveClassAss"], 2);
    $codClassificacao = $arCodChaveAss[0];
    $codAssunto = $arCodChaveAss[1];

    //echo $codTramite;
    $arTramite = preg_split( '/[^0-9a-zA-Z]/', $_REQUEST["codTramite"]);
    $anoExercicio = $arTramite[4];
    $codOrgao = $arTramite[0];
    $anoOrgao = $arTramite[4];

    $arCodTramite = validaMascaraDinamica($mascaraSetor,$arTramite[0]."-".$arTramite[1]."-".$arTramite[2]."-".$arTramite[3]."-".$arTramite[4]);
    $obAndamento = new andamento;
    $arAndamento = array();
    $stCondicao .= "     AND andamento.cod_classificacao = ".intval($codClassificacao);
    $stCondicao .= "     AND andamento.cod_assunto = ".intval($codAssunto);
    $stCondicao .= "     AND andamento.ordem = ".$_REQUEST["numOrdem"];
    $obAndamento->recuperaCompleto($arAndamento, $stCondicao);
    $arAndamento = $arAndamento[0];

?>

    <form name="frm" action="alteraAndamentoPadrao.php?<?=Sessao::getId();?>" method="POST" onSubmit="return false;">
    <input type="hidden" name="codAssunto" value="<?=$codAssunto;?>">
    <input type="hidden" name="codClassificacao" value="<?=$codClassificacao;?>">
    <input type="hidden" name="numOrdem" value="<?=$_REQUEST["numOrdem"];?>">
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
        <td class="field"><?=$arAndamento['nom_classificacao'];?></td>
    </tr>
    <tr>
        <td class="field"><?=$arAndamento['nom_assunto'];?></td>
    </tr>
    <tr>
        <td class="alt_dados" colspan=2>Dados para trâmite</td>
    </tr>
        </table>
    <?php
       //include(CAM_FW_LEGADO."filtrosAndamentoPadraoLegado.inc.php");

       include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
        $obFormulario = new Formulario;
        $obFormulario->setForm(null);
        $obFormulario->setLarguraRotulo(30);
        $obFormulario->addForm(null);

        $obIMontaOrganograma = new IMontaOrganograma(true);
        $obIMontaOrganograma->setNivelObrigatorio(1);
        $obIMontaOrganograma->setCodOrgao($arAndamento["cod_orgao"]);
        $obIMontaOrganograma->geraFormulario($obFormulario);

        $obFormulario->montaHTML();
        echo $obFormulario->getHTML();
    ?>
<table width='100%'>
    <tr>
        <td width="30%" class=label title="Descrição do trâmite">*Descrição</td>
        <td class=field><textarea name=descricao cols=50 rows=4 style="width:400px"><?=$arAndamento[descricao];?></textarea></td>
    </tr>
    <tr>
        <td class="label" title="Tempo máximo de permanência no setor (em dias)">*Qtd. de dias</td>
        <td class="field">
            <input type="text" name="numDia" size=4 maxlength='10' onKeyUp="return autoTab(this, 10, event);" onKeyPress="return(isNumber(this, event))" value="<?=$arAndamento[num_dia];?>">
        </td>
    </tr>
    <tr>
        <td class=field colspan="2">
            <table width="100%" cellspacing=0 border=0 cellpadding=0>
                <tr>
                    <td>
                        <?=geraBotaoAltera();?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    </table>
    </form>
    <script type="text/javascript">
    <!--

    function Valida()
    {
    var mensagem = "";
    var erro = false;
    var erroSetor = false;
    var campo;
    var campoaux;

    if (erroSetor) {
        mensagem += "@Campo Setor inválido! ";
        erro = true;
    }
    var expReg = /\n/g;
    campo = document.frm.descricao.value.replace( expReg, "");
    campo = trim(campo);
    if (campo == "") {
        mensagem += "@Campo Descrição inválido!()";
        erro = true;
    }

    campo = trim(document.frm.numDia.value);
    if (campo == "") {
        mensagem += "@Campo Quantidade de dias inválido!()";
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
        document.frm.ctrl.value = 6;
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
    document.frm.ctrl.value = 100;
    document.frm.action += "&variavel="+variavel+"&valor="+valor+"&ctrl=5";
    document.frm.submit();
    document.frm.action = actionTmp;
    document.frm.target = targetTmp;
    }

    function Cancela()
    {
        document.frm.ctrl.value = 2;
        document.frm.submit();
    }

    BuscaValores( 'codMasSetor', '<?=$arCodTramite[1];?>' );
    //-->
    </script>
<?php
break;

case 5:

break;

case 6:
    $variaveis        = explode("-",$_REQUEST["codSetor"]);
    $codSetorS        = (integer) $variaveis[0];
    $codDepartamentoS = (integer) $variaveis[1];
    $codUnidadeS      = (integer) $variaveis[2];
    $codOrgaoS        = (integer) $variaveis[3];
    $anoOrgaoS        = (integer) $variaveis[4];

    if ($anoOrgaoS==0) {
        $anoOrgaoS = $_REQUEST["anoExercicio"];
    }

    $obAndamento = new andamento;
    $arAndamento = array();
    $stCondicao .= "     AND andamento.cod_classificacao = ".intval($_REQUEST["codClassificacao"]);
    $stCondicao .= "     AND andamento.cod_assunto = ".intval($_REQUEST["codAssunto"]);
    $stCondicao .= "     AND andamento.ordem = ".$_REQUEST["numOrdem"];
    $obAndamento->recuperaCompleto($arAndamento, $stCondicao);
    $arAndamento = $arAndamento[0];

    $sFiltro .= " WHERE cod_orgao = ".$_REQUEST["hdnUltimoOrgaoSelecionado"];
    $sFiltro .= " AND ordem != ".$_REQUEST["numOrdem"]." ";

    $numPassagens = pegaID("num_passagens", "sw_andamento_padrao", $sFiltro );

    $obAndamento->numPassagens	        = $numPassagens;
    $obAndamento->classificacao	        = $_REQUEST["codClassificacao"];
    $obAndamento->assunto		= $_REQUEST["codAssunto"];
    $obAndamento->codOrgao		= $_REQUEST["hdnUltimoOrgaoSelecionado"];
    $obAndamento->descricao		= $_REQUEST["descricao"];
    $obAndamento->numDia		= $_REQUEST["numDia"];
    $obAndamento->ordem			= $_REQUEST["numOrdem"];
    $js .= "f.ok.disabled = false; \n";

    $sFiltro = " WHERE ";
    $sFiltro .= " cod_classificacao = ".$arAndamento['cod_classificacao']." AND ";
    $sFiltro .= " cod_assunto = ".$arAndamento['cod_assunto']." AND ";
    $sFiltro .= " cod_orgao = ".$arAndamento['cod_orgao']." AND ";
    $sFiltro .= " num_passagens > ".$arAndamento['num_passagens']." ";

    if ( $obAndamento->alteraAndamentoCompleto($sFiltro) ) {

        //Insere auditoria
        $audicao = new auditoriaLegada;
        $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $_REQUEST["hdnUltimoOrgaoSelecionado"]);
        $audicao->insereAuditoria();

        alertaAviso($PHP_SELF.'?'.Sessao::getId()."&codClassificacao=".$_REQUEST["codClassificacao"]."&codAssunto=".$_REQUEST["codAssunto"]."&ctrl=2",$_REQUEST["hdninCodOrganograma"],"alterar","aviso");
    } else {
        exibeAviso($sChave,"n_alterar","erro");
        $js .= "f.ok.disabled = false; \n";
    }
executaFrameOculto($js);
break;
    case 100:
        include(CAM_FW_LEGADO."filtrosCASEAndamentoPadraoLegado.inc.php");
    break;

}
include '../../../framework/include/rodape.inc.php';
?>
