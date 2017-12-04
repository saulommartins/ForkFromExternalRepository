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
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"   );
include (CAM_FRAMEWORK."legado/mascarasLegado.lib.php"  );
include '../andamento.class.php';
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
    document.frm.action = "consultaAndamentoPadrao.php?<?=Sessao::getId();?>&ctrl=2";
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
<form name="frm" action="consultaAndamentoPadrao.php?<?=Sessao::getId();?>" method="POST" onSubmit="return false;">
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
$dbEmp = new dataBaseLegado ;
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
   $dbEmp = new dataBaseLegado ;
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

$codClassifAssunto = $_REQUEST["codClassifAssunto"];
$js .=  "f.ok.disabled = true;\n";
switch ($variavel) {
    case 'codClassifAssunto':
        if ( $codClassifAssunto != '') {
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
            if ($codClassificacaof == $codClassificacao) {
                $js .=  "var iContAss = 0;\n";
                $js .=  "var iTamAss = f.codAssunto.options.length - 1;\n";
                $js .=  "while (iTamAss >= iContAss) {\n";
                $js .=  "    if ( f.codAssunto.options[iContAss].value == ".(integer) $codAssuntof.") {\n";
                $js .=  "        f.codAssunto.options[iContAss].selected = true;\n";
                $js .=  "        f.ok.disabled = false;\n";
                $js .=  "        break;\n";
                $js .=  "    }\n";
                $js .=  "    iContAss++;\n";
                $js .=  "}\n";
                $js .=  "if (iContAss > iTamAss) {\n";
                $js .=  "    f.codAssunto.options[0].selected = true;\n";
                $js .=  "}\n";
            } else {
                $sSQL  = "SELECT cod_assunto, cod_classificacao, nom_assunto FROM sw_assunto ";
                $sSQL .= "WHERE cod_classificacao = '".$codClassificacaof."' ORDER by nom_assunto";
                //echo  $sSQL;
                $dbAss = new dataBaseLegado ;
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
            }          
        }else{
            $js .= " f.codClassifAssunto.value = '';\n";            
            $js .= " f.codClassificacao.options[0].selected = true;\n";
            $js .= " f.codAssunto.options[0].selected = true;\n";
            $js .= " limpaSelect(f.codAssunto,1);\n";
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
            $dbAss = new dataBaseLegado ;
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
        $dbAss = new dataBaseLegado ;
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
    $sSQL .= "         , orgao_descricao.cod_orgao                                                 ".$sQuebra;
    $sSQL .= "         , vw_orgao_nivel.orgao                                                      ".$sQuebra;
    $sSQL .= "         , MAX(orgao_descricao.timestamp)                                            ".$sQuebra;
    $sSQL .= "         , orgao_descricao.descricao                                                 ".$sQuebra;
    $sSQL .= "         , sw_andamento_padrao.descricao as despacho                                 ".$sQuebra;
    $sSQL .= "         , sw_andamento_padrao.num_dia                                               ".$sQuebra;
    $sSQL .= "         , sw_assunto.nom_assunto                                                    ".$sQuebra;
    $sSQL .= "         , sw_classificacao.nom_classificacao                                        ".$sQuebra;
    $sSQL .= "      FROM                                                                           ".$sQuebra;
    $sSQL .= "           sw_andamento_padrao                                                       ".$sQuebra;
    $sSQL .= "           JOIN sw_assunto                                                           ".$sQuebra;
    $sSQL .= "             ON sw_assunto.cod_classificacao = sw_andamento_padrao.cod_classificacao ".$sQuebra;
    $sSQL .= "            AND sw_assunto.cod_assunto = sw_andamento_padrao.cod_assunto             ".$sQuebra;
    $sSQL .= "           JOIN sw_classificacao                                                     ".$sQuebra;
    $sSQL .= "             ON sw_classificacao.cod_classificacao = sw_assunto.cod_classificacao    ".$sQuebra;
    $sSQL .= "           JOIN organograma.orgao                                                    ".$sQuebra;
    $sSQL .= "             ON orgao.cod_orgao = sw_andamento_padrao.cod_orgao                      ".$sQuebra;
    $sSQL .= "           JOIN organograma.orgao_nivel                                              ".$sQuebra;
    $sSQL .= "             ON orgao_nivel.cod_orgao = orgao.cod_orgao                              ".$sQuebra;
    $sSQL .= "           JOIN organograma.orgao_descricao                                          ".$sQuebra;
    $sSQL .= "             ON orgao_descricao.cod_orgao = orgao.cod_orgao                          ".$sQuebra;
    $sSQL .= "           JOIN organograma.vw_orgao_nivel                                           ".$sQuebra;
    $sSQL .= "             ON vw_orgao_nivel.cod_orgao       = orgao.cod_orgao                     ".$sQuebra;
    $sSQL .= "            AND vw_orgao_nivel.cod_organograma = orgao_nivel.cod_organograma         ".$sQuebra;
    $sSQL .= "     WHERE                                                                           ".$sQuebra;
    $sSQL .= "           sw_andamento_padrao.cod_classificacao = ".$_REQUEST['codClassificacao']." ".$sQuebra;
    $sSQL .= "       AND sw_andamento_padrao.cod_assunto = ".$_REQUEST['codAssunto']."             ".$sQuebra;
    $sSQL .= "  GROUP BY                                                                           ".$sQuebra;
    $sSQL .= "           sw_andamento_padrao.ordem                                                 ".$sQuebra;
    $sSQL .= "         , orgao_descricao.cod_orgao                                                 ".$sQuebra;
    $sSQL .= "         , orgao_descricao.descricao                                                 ".$sQuebra;
    $sSQL .= "         , vw_orgao_nivel.orgao                                                      ".$sQuebra;
    $sSQL .= "         , sw_andamento_padrao.descricao                                             ".$sQuebra;
    $sSQL .= "         , sw_andamento_padrao.num_dia                                               ".$sQuebra;
    $sSQL .= "         , sw_assunto.nom_assunto                                                    ".$sQuebra;
    $sSQL .= "         , sw_classificacao.nom_classificacao                                        ".$sQuebra;
    $sSQL .= "  ORDER BY sw_andamento_padrao.ordem                                                 ".$sQuebra;

    $sSQL = str_replace("<br>", "", $sSQL);

    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->fechaBd();

    $stNomClassificacao = trim($dbEmp->pegaCampo("nom_classificacao"));
    $stNomAssunto = trim($dbEmp->pegaCampo("nom_assunto"));

    while ( !$dbEmp->eof() ) {
        $listaTmp = array( trim($dbEmp->pegaCampo("ordem")     ),
                           trim($dbEmp->pegaCampo("cod_orgao") ),
                           trim($dbEmp->pegaCampo("orgao")     ),
                           trim($dbEmp->pegaCampo("descricao") ),
                           trim($dbEmp->pegaCampo("despacho")  ),
                           trim($dbEmp->pegaCampo("num_dia")   )

                        );
        $listaTramitePadrao[]= $listaTmp;
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

    campo = document.frm.codOrgao.value;
    if (campo == 'xxx') {
        //mensagem += "@O campo Orgao é obrigatório";
        erroSetor = true;
    }

    campo = document.frm.codUnidade.value;
    if (campo == 'xxx') {
        //mensagem += "@O campo Unidade é obrigatório";
        erroSetor = true;
    }

    campo = document.frm.codDepartamento.value;
    if (campo == 'xxx') {
        //mensagem += "@O campo Departamento é obrigatório";
        erroSetor = true;
    }

    campo = document.frm.codSetor.value;
    if (campo == 'xxx') {
        //mensagem += "@O campo Setor é obrigatório";
        erroSetor = true;
    }

    if (erroSetor) {
        mensagem += "@Campo Setor inválido! ("+document.formulario.codMasSetor.value+") ";
        erro = true;
    }

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
        document.frm.action = "consultaAndamentoPadrao.php?<?=Sessao::getId();?>&ctrl=2&codAssunto=<?=$codAssunto;?>&codClassificacao=<?=$codClassificacao;?>";
        document.frm.submit();
    }
    }

    function Limpar()
    {
        document.frm.codMasSetor.value = "";
        document.frm.codOrgao.options[0].selected = true;
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
    <form name="frm" action="consultaAndamentoPadrao.php?<?=Sessao::getId();?>" method="POST" onSubmit="return false;">
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
    if (count($listaTramitePadrao) ) {
    ?>
    <table width=100%>
    <tr>
        <td colspan="9" class="alt_dados">Registros de trâmite</td>
    </tr>
    <tr>
        <td class="labelleft" width="3%">&nbsp;</td>
        <td class="labelleft" width="9%">Ordem</td>
        <td class="labelleft" width="9%">Código</td>
        <td class="labelleft" width="70%">Orgão</td>
        <td class="labelleft" width="9%">Qtd. dias</td>

    </tr>
    <tr>
        <td colspan="9" class="labelcenter">Despachos</td>
    </tr>

<?php
    $inCont = 1;

    foreach ($listaTramitePadrao as $arTramitePadrao) {
        // mascara o codigo do Setor
        $stCodSetor = $arTramitePadrao[1]."/".$arTramitePadrao[7];
        $arCodSetor = validaMascara($mascaraSetor,$stCodSetor);
?>
        <tr>
            <td class="labelcenter" rowspan="2"><?=$inCont++;?></td>
            <td class="show_dados"><?=$arTramitePadrao[0];?></td>
            <td class="show_dados"><?=$arTramitePadrao[2];?></td>
            <td class="show_dados"><?=$arTramitePadrao[3];?></td>
            <td class="show_dados"><?=$arTramitePadrao[5];?></td>
        </tr>
        <tr>
            <td class="show_dados" colspan="7"><?=$arTramitePadrao[4];?></td>
        </tr>
<?php
    }//endforeach
    echo "</table>\n";
    echo "<table width=450 align=center><tr><td align=center><font size=2>";
    echo "</font></tr></td></table>";
    }
    ?>
    <input type="hidden" name="posicaoAtual" value="">
    <input type="hidden" name="posicaoNova" value="">
    </form>
<?php
break;
}
include '../../../framework/include/rodape.inc.php';
?>
