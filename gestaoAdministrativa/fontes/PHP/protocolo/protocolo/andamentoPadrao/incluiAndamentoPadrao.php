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

  /**
    * Arquivo de implementação de andamento padrão
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-01.06.97

    $Id: incluiAndamentoPadrao.php 65675 2016-06-08 17:32:23Z jean $

    */

include '../../../framework/include/cabecalho.inc.php';
include CAM_FRAMEWORK."legado/funcoesLegado.lib.php";
include CAM_FRAMEWORK."legado/mascarasLegado.lib.php";
include '../andamento.class.php';
include CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";

setAjuda('uc-01.06.97');

$ctrl = $_REQUEST["ctrl"];

$mascaraAssunto = pegaConfiguracao('mascara_assunto',5);
$mascaraSetor   = pegaConfiguracao('mascara_setor',2);

switch ($ctrl) {
case 0:
Sessao::write('ordem', 0);
?>

<script type="text/javascript">
<!--
function ValidaForm()
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

function SalvarForm()
{
    if (ValidaForm()) {
        document.frm.action = "incluiAndamentoPadrao.php?<?=Sessao::getId();?>&ctrl=1";
        document.frm.submit();
    }
}

function BuscaValores(variavel, valor)
{
   var targetTmp = document.frm.target;
   document.frm.target = "oculto";
   var actionTmp = document.frm.action;
   document.frm.action += "&variavel="+variavel+"&valor="+escape(valor)+"&ctrl=4";
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
<form name="frm" action="incluiAndamentoPadrao.php?<?=Sessao::getId();?>" method="POST" onSubmit="return Valida();">
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
         <input type="text" name="codClassifAssunto" value="<?=$codClassifAssunto;?>" size="<?=strlen($mascaraAssunto);?>" maxlength="<?=strlen($mascaraAssunto);?>" value="<?=$codClassifAssunto?>" onKeyUp="JavaScript: mascaraDinamico('<?=$mascaraAssunto?>', this, event);" onChange="JavaScript: BuscaValores( 'codClassifAssunto', this.value );">
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
          <input type="button" value="OK" name="ok" style="width: 70px" onClick="SalvarForm();" disabled>
          <input type="button" value="Limpar" style="width: 70px" onClick="Limpar();">
      </td>
   </tr>
</table>
</form>
<?php
break;
case 1:

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

    include_once CAM_GA_PROT_MAPEAMENTO."TClassificacao.class.php";

    $obTClassificacao = new TClassificacao;
    $obTClassificacao->recuperaClassificacaoAssunto($rsRecord, " WHERE sw_classificacao.cod_classificacao = ".$_REQUEST['codClassificacao']."
                                                                        AND sw_assunto.cod_assunto = ".$_REQUEST['codAssunto']);
    ?>

    <form name="frm" action="incluiAndamentoPadrao.php?<?=Sessao::getId();?>&ctrl=1" method="POST" onSubmit="return Valida();">
    <input type="hidden" name="codAssunto" value="<?=$_REQUEST["codAssunto"];?>">
    <input type="hidden" name="codClassificacao" value="<?=$_REQUEST["codClassificacao"];?>">
    <table width=100%>
    <tr>
        <td colspan="2" class="alt_dados">Assuntos de processo</td>
    </tr>
    <tr>
        <td rowspan="3" class="label" width="30%">Classificação/Assunto</td>
    <?php
    $arCodClassifAssunto = validaMascaraDinamica($mascaraAssunto,$_REQUEST['codClassificacao']."-".$_REQUEST['codAssunto']);
    ?>
        <td class="field"><?=$arCodClassifAssunto[1];?></td>
    </tr>
    <tr>
        <td class="field"><?=$rsRecord->getCampo("nom_classificacao");?></td>
    </tr>
    <tr>
        <td class="field"><?=$rsRecord->getCampo("nom_assunto");?></td>
    </tr>
    <tr>
        <td class="alt_dados" colspan=2>Dados para trâmite</td>
    </tr>
    </table>

        <?php
            $obFormulario = new Formulario;
            $obFormulario->setLarguraRotulo(30);
            $obFormulario->setForm(null);

            $obIMontaOrganograma = new IMontaOrganograma;
            $obIMontaOrganograma->setNivelObrigatorio(1);
            $obIMontaOrganograma->geraFormulario($obFormulario);

            $obFormulario->montaHtml();
            echo $obFormulario->getHTML();
        ?>
<script type="text/javascript">
    <!--
    function ValidaForm()
    {
        var mensagem = "";
        var erro = false;
        var erroSetor = false;
        var campo;
        var campoaux;

        var expReg = /\n/g;
        campo = document.frm.descricao.value.replace( expReg, "");
        campo = trim(campo);
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
            if (ValidaForm()) {
                document.frm.action = "incluiAndamentoPadrao.php?<?=Sessao::getId();?>&ctrl=2&codAssunto=<?=$_REQUEST["codAssunto"];?>&codClassificacao=<?=$_REQUEST["codClassificacao"];?>";
                document.frm.submit();
            }
        }
    }

    function Limpar()
    {
  //      document.frm.codMasSetor.value = "";
  //      document.frm.codOrgao.options[0].selected = true;
  //      limpaSelect(document.frm.codUnidade,1);
  //      limpaSelect(document.frm.codDepartamento,1);
  //      limpaSelect(document.frm.codSetor,1);
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
    //-->
    </script>
    <table width=100%>
    <tr>
        <td class=label title="Descrição do trâmite" width='30%'>*Descrição</td>
        <td class=field><textarea name=descricao cols=50 rows=4 style="width:400px"></textarea></td>
    </tr>
    <tr>
        <td class="label" title="Tempo máximo de permanência no setor (em dias)">*Qtd. de dias</td>
        <td class="field">
            <input type="text" name="numDia" size=4 maxlength='10' onKeyUp="return autoTab(this, 10, event);" onKeyPress="return(isNumber(this, event))">
        </td>
    </tr>
    <tr>
        <td class=field colspan="2">
            <table width="100%" cellspacing=0 border=0 cellpadding=0>
                <tr>
                    <td>
                         <input type="button" name="ok" value="OK" style="width: 70px" onClick="Salvar();">&nbsp;
                        <input type="button" name="limpar" value="Limpar" style="width: 70px" onClick="Limpar();">
                    </td>
                    <td class="fieldright_noborder">
                        <b>*Campos obrigatórios</b>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    </table>
    </form>
    <?php
    if ( count($listaTramitePadrao) ) {
    ?>
    <table width=100%>
    <tr>
        <td colspan="8" class="alt_dados">Registros de trâmite</td>
    </tr>

    <tr>
        <td class="labelleft" width="3%">&nbsp;</td>
        <td class="labelleft" width="9%">Ordem</td>
        <td class="labelleft" width="9%">Código</td>
        <td class="labelleft" width="70%">Descrição</td>
        <td class="labelleft" width="9%">Qtd. dias</td>
    </tr>

<?php
    $inCont = 1;
    foreach ($listaTramitePadrao as $arTramitePadrao) {
?>

    <tr>
        <td class="labelcenter" rowspan="2"><?=$inCont++;?></td>
        <td class="show_dados"><?=$arTramitePadrao[0];?></td>
        <td class="show_dados"><?=$arTramitePadrao[2];?></td>
        <td class="show_dados"><?=$arTramitePadrao[3];?></td>
        <td class="show_dados"><?=$arTramitePadrao[5];?></td>
    </tr>
    <tr>
    </tr>
<?php
    }//endforeach
    }
?>

    <script type="text/javascript">
    <!--
    <?php

    if ( empty ($_REQUEST["descricao"]) ) {
        echo "document.frm.descricao.focus();";
    } elseif ( empty ($_REQUEST["numDia"]) ) {
        echo "document.frm.numDia.focus();";
    }
        echo "\n";
    ?>
    //-->
    </script>
<?php
break;

case 2:

    # $chave = (integer) $variaveis[0].".".(integer) $variaveis[1].".".(integer) $variaveis[2].".".(integer) $variaveis[3]."/".$codAnoS;

    $sFiltro  = " where cod_assunto = ".$_REQUEST["codAssunto"]." AND ";
    $sFiltro .= " cod_classificacao = ".$_REQUEST["codClassificacao"];

    $codOrgao = $_REQUEST['hdnUltimoOrgaoSelecionado'];

    $ordem = pegaID("ordem", "sw_andamento_padrao", $sFiltro);
    $nomAssunto = pegaDado("nom_assunto","sw_assunto", $sFiltro);

    $sFiltro .= " AND cod_orgao = ".$codOrgao;

    $numPassagens = pegaID("num_passagens", "sw_andamento_padrao", $sFiltro );

    $andamento = new andamento;
    $andamento->assunto = $_REQUEST["codAssunto"];
    $andamento->classificacao = $_REQUEST["codClassificacao"];
    $andamento->codOrgao = $codOrgao;
    $andamento->numPassagens = $numPassagens;
    $andamento->anoE = $codAnoS;
    $andamento->descricao = $_REQUEST["descricao"];
    $andamento->ordem = $ordem;
    $andamento->numDia = $_REQUEST["numDia"];

    if ($andamento->insereAndamentoPadrao()) {

       Sessao::write('ordem', $ordem);
       include '../../../framework/legado/auditoriaLegada.class.php';
       $audicao = new auditoriaLegada;
       $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $chave);
       $audicao->insereAuditoria();

       alertaAviso($PHP_SELF."?".Sessao::getId().'&ctrl=1&codAssunto='.$_REQUEST["codAssunto"].'&codClassificacao='.$_REQUEST["codClassificacao"].'&msg=1', $codOrgao,"incluir" ,"aviso");
    } else {
       alertaAviso($PHP_SELF."?".Sessao::getId().'&ctrl=1&codAssunto='.$_REQUEST["codAssunto"].'&codClassificacao='.$_REQUEST["codClassificacao"].'&msg=2',$codOrgao,"n_incluir","erro");
    }

break;

# Utilizado para montar Classificação e Assunto.
case 4:    
    $codClassifAssunto = $_REQUEST["codClassifAssunto"];
    $js .=  "f.ok.disabled = true;\n";
    switch ($_REQUEST["variavel"]) {
    case 'codClassifAssunto':
        if($_REQUEST["valor"] != '') {                  
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
            if ($codClassificacaof == $_REQUEST["codClassificacao"]) {
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
            } else {
                $sSQL  = "SELECT cod_assunto, cod_classificacao, nom_assunto FROM sw_assunto ";
                $sSQL .= "WHERE cod_classificacao = '".$codClassificacaof."' ORDER by nom_assunto";

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

            }
        } else {           
              $js .=  "f.codAssunto.options[0].selected = true;\n";
              $js .=  "f.codClassificacao.options[0].selected = true;\n";              
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
        if ($_REQUEST["codAssunto"] == "xxx") {
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
    case 100:
        include '../../../framework/legado/filtrosCASEAndamentoPadraoLegado.inc.php';
    break;
}
?>
