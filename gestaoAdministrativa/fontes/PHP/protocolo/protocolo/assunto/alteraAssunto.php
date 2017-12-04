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
* Arquivo de implementação de manutenção de assunto
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 24726 $
$Name$
$Author: domluc $
$Date: 2007-08-13 18:34:38 -0300 (Seg, 13 Ago 2007) $

Casos de uso: uc-01.06.95
*/

include '../../../framework/include/cabecalho.inc.php';
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
include (CAM_FRAMEWORK."legado/paginacaoLegada.class.php");
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
include '../assunto.class.php';
setAjuda('uc-01.06.95');

  if (!(isset($ctrl)))
        $ctrl=0;
    switch ($ctrl) {
        case 0:
    $sSQL = "SELECT * FROM sw_classificacao ORDER BY nom_classificacao";
    $dbConfig = new databaseLegado;
    $dbConfig->abreBd();
    $dbConfig->abreSelecao($sSQL);
    $dbConfig->fechaBd();

    $dbConfig2 = new databaseLegado;
    $paginacao = new paginacaoLegada;
    if ($codClassificacao and $codClassificacao != "xxx") {
        $select = "SELECT * FROM sw_assunto WHERE cod_classificacao = ".$codClassificacao." ";
        Sessao::write('filtro',lower(nom_assunto));
        Sessao::write('sSQLs',$select);
        //sessao->transf2 ="lower(nom_assunto)";
        //sessao->transf = $select;
        $registros = 10;
        $paginacao->pegaDados(Sessao::read('sSQLs'),$registros);
            $paginacao->pegaPagina($pagina);
        $paginacao->complemento = "&codClassificacao=".$codClassificacao;
            $paginacao->geraLinks();
            $paginacao->pegaOrder(Sessao::read('filtro'),"ASC");
            $sSQL2 = $paginacao->geraSQL();
        $dbConfig2->abreBd();
        $dbConfig2->abreSelecao($sSQL2);
        $dbConfig2->fechaBd();
        Sessao::write('filtro','');
        Sessao::write('sSQLs','');
        //sessao->transf2 = "";
        //sessao->transf = "";
    }
?>

<form name=frm action=alteraAssunto.php?<?=Sessao::getId();?>&ctrl=0 method=post>
<table width=100%>
    <tr>
        <td colspan=2 class=alt_dados>Classificações de assunto</td>
    <tr>
        <td class=label width=30% title="Filtra os assuntos por classificação">Classificação</td>
    <td class=field>
        <input type="text" size="4" maxlength="8" name="codClassTxt" onChange="JavaScript: if (preencheCampo(document.frm.codClassTxt, document.frm.codClassificacao)) {document.frm.submit();}" value="<?=$codClassificacao;?>">
        <select name=codClassificacao onChange="JavaScript: if (preencheCampo(document.frm.codClassificacao, document.frm.codClassTxt)) {document.frm.submit();}">
            <option value='xxx'>Selecione</option>
<?php
    while (!$dbConfig->eof()) {
        $codClass = $dbConfig->pegaCampo('cod_classificacao');
        $nomClass = $dbConfig->pegaCampo('nom_classificacao');
        if ($codClass == $codClassificacao) {
            $selected = " selected";
        } else {
            $selected = "";
        }
?>
                 <option value='<?=$codClass;?>'<?=$selected?>><?=$nomClass;?></option>
<?php
        $dbConfig->vaiProximo();
    }
?>
            </select>
    </td>
    </tr>
</table>
<table width=100%>
   <tr>
      <td colspan=4 class=alt_dados>Registros de assunto</td>
   </tr>
   <tr>
      <td class=labelleft width=5%>&nbsp;</td>
      <td class=labelleft width=12%>Código</td>
      <td class=labelleft width=80%>Descrição</td>
      <td class=label>&nbsp;</td>
   </tr>
<?php
    $cont = $paginacao->contador();
    while (!$dbConfig2->eof()) {
        $codAssunto = $dbConfig2->pegaCampo('cod_assunto');
    $nomAssunto = $dbConfig2->pegaCampo('nom_assunto');
?>
   <tr>
      <td class=labelcenter><?=$cont++;?></td>
      <td class=show_dados><?=$codAssunto;?></td>
      <td class=show_dados><?=$nomAssunto;?></td>
      <td class=botao><center><a href='alteraAssunto.php?<?=Sessao::getId();?>&codClassificacao=<?=$codClassificacao;?>&codAssunto=<?=$codAssunto;?>&ctrl=1&pagina=<?=$pagina?>'><img src='<?=CAM_FW_IMAGENS."btneditar.gif";?>' border=0></a></center>
      </td>
   </tr>
<?php
    $dbConfig2->vaiProximo();
    }
?>
</table>
</form>
<?php
    echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
    echo "</font></tr></td></table>";
    break;
/**********************************************************/
/**** FORMULARIO DE ALTERACAO				***/
/**********************************************************/
    case 1:

    $altera = new assunto;
        $altera->codigo = $codAssunto;
        $montaClass = $altera->listaClassificacao();
        $montaDocs = $altera->listaDocumentos();
        $altera->mostraAssunto($codAssunto, $codClassificacao);
    $condicao  = " AND ass.cod_assunto = ".$codAssunto;
    $condicao .= " AND ass.cod_classificacao = ".$codClassificacao;
    $propAssunto = $altera->listaClassificacaoAssunto( $condicao );
    $propAssunto = current($propAssunto);
?>
<script type="text/javascript">
    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f;

        f = document.frm;

        campo = trim( f.nomAssunto.value );
            if (campo== "") {
                mensagem += "@O campo Descrição é obrigatório";
                erro = true;
            }

        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        if (Valida()) {
            //document.frm.X.value = "";
            //document.frm.action = "";
            document.frm.submit();
        }
    }

    function PreencheCampo(selecionado,selecionar)
    {
       var valor = "";
       if (selecionado.value != 'xxx') {
          valor = selecionado.value;
       }
       selecionar.value = valor;
       if (selecionar.value != valor) {
          alertaAviso('Classificação inválida','form','erro','<?=Sessao::getId()?>');
          selecionado.value = "";
       }
    }

    function Limpar()
    {
      var len = document.frm.elements.length;
      var contador = 0;
      while (contador < len) {
         if (document.frm.elements[ contador ].type != 'hidden') {
            if (document.frm.elements[contador].type == 'select') {
               document.frm.elements[contador].value = 'xxx';
        }
        if (document.frm.elements[contador].type == 'checkbox') {
           document.frm.elements[contador].checked = false;
        }
        if (document.frm.elements[contador].type == 'text') {
           document.frm.elements[contador].value = '';
        }
     }
     contador++;
      }
    }

    function Cancela()
    {
<?php
  $sAction = Sessao::getId()."&codAssunto=".$codAssunto."&codClassificacao=".$codClassificacao;
  $sAction.= "&ctrl=0&pagina=".$pagina;
?>
       document.frm.action = 'alteraAssunto.php?<?=$sAction?>';
       document.frm.submit();
    }
</script>
<?php
  $sAction = Sessao::getId()."&codAssunto=".$codAssunto."&codClassificacao=".$codClassificacao;
  $sAction.= "&ctrl=2&pagina=".$pagina;
?>
<form action="alteraAssunto.php?<?=$sAction;?>" method="POST" name="frm">
<input type="hidden" name="pagina" value="<?=$pagina?>">
<table width='100%'>
    <tr>
        <td class=alt_dados colspan=2>Dados para assunto</td></tr>
    <tr>
        <td class=label width=30%>Código:</td>
        <td class=field>
       <input type="hidden" name="codAssunto" value="<?=$propAssunto['codAssunto'];?>">
       <?=$propAssunto['codAssunto'];?>
    </td>
    </tr>
    <tr>
       <td class=label>Classificação</td>
       <td class=field>
          <?=$propAssunto['codClassificacao'];?>&nbsp;<?=$propAssunto['nomClassificacao'];?>
      <input type="hidden" name="codClassificacao" value="<?=$propAssunto['codClassificacao'];?>">
       </td>
    </tr>
    <tr>
       <td class=label title="Descrição do assunto">*Descrição:</td>
       <td class=field>
          <input type="text" name="nomAssunto" size="30" maxlength="60" value="<?=$propAssunto['nomAssunto'];?>">
      <input type="hidden" name="nomAssuntoConfirma" value="<?=$propAssunto['nomAssunto'];?>">
       </td>
    </tr>
    <tr>
       <td class=label title="Define se o processo será confidencial">*Confidencial</td>
       <td class=field>
<?php
if ($propAssunto['confidencial'] == "f") {
  $selectedF = " checked";
  $selectedT = "";
} else {
  $selectedF = "";
  $selectedT = " checked";
}
?>
            <input type="radio" name="confidencial" value="f" <?=$selectedF;?>>Não
            <input type="radio" name="confidencial" value="t" <?=$selectedT;?>>Sim
      <input type="hidden" name="confidencialConfirma" value="<?=$valorConfirma?>">
       </td>
    <tr>
       <td class=alt_dados colspan=2>Documentos para assunto</td>
    </tr>
<?php
    while (list ($key, $val) = each ($montaDocs)) {
        if ($altera->documento[$key] == "s") {
       $checked = " checked";
    } else {
           $checked = "";
    }
?>
     <tr>
        <td class=label><?=$val;?></td>
        <td class=field>
       <input type='checkbox' name='doc[]' value='<?=$key;?>'<?=$checked;?>>
    </td>
     </tr>
<?php
    }
?>
     <tr>
        <td class=alt_dados colspan=2>Atributos para assunto</td>
     </tr>
<?php
$mtAtributos = $altera->listaAtributos($codAssunto, $codClassificacao);//retorna uma matriz com os atributos
if ( count($mtAtributos) ) {
   foreach ($mtAtributos as $arAtributos) {
      if ($arAtributos['codAssunto']) {
         $checked = " checked";
      } else {
         $checked = "";
      }
?>
     <tr>
        <td class=label><?=$arAtributos['nomAtributo'];?></td>
    <td class=field>
       <input type='checkbox' name='atributo[]' value='<?=$arAtributos['codAtributo'];?>'<?=$checked;?>>
        </td>
     </tr>
<?PHP
   }//FIM DO foreach( $mtAtributos as $arAtributos )
} else {
?>
    <tr>
       <td class=label>&nbsp;</td>
       <td class=field>Nenhum atributo cadastrado</td>
    </tr>
<?php
}//FIM DO if ( count($mtAtributos) )
?>
     <tr>
        <td class=field colspan=2>
            <?=geraBotaoAltera();?>
    </td>
     </tr>
</table>
</form>
<?php
    break;
    case 2:
        $sql = "";
        $altera= new assunto;
    $altera->setaVariaveis($codClassificacao, $nomAssunto, $confidencial);
    $sWhere = "And cod_assunto <> '".$codAssunto."' And cod_classificacao = '".$codClassificacao."'";
    if (!comparaValor("nom_assunto", $altera->nome, "sw_assunto", $sWhere,1) ) {
       alertaAviso("alteraAssunto.php?".Sessao::getId()."&codClassificacao=".$codClassificacao,"O assunto ".$altera->nome." já existe nesta classificação!","unica","erro", "'.Sessao::getId().'");
    } else {
           if ( $altera->alteraAssunto($codAssunto, $codClassificacao) ) {
              $altera->incluiDocumentos($codAssunto, $codClassificacao, $doc);
          if ($altera->excluiAtributos($codAssunto, $codClassificacao)) {
             $altera->incluiAtributos($codAssunto, $codClassificacao, $atributo);
          }
          $audicao = new auditoriaLegada;
              $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), "Assunto: ".$nom);
              $audicao->insereAuditoria();
              $script  = "<script type=\"text/javascript\"> \n";
              $script .= "   alertaAviso(\"".$altera->nome."\",\"alterar\",\"aviso\", \"".Sessao::getId()."\");\n";
              $script .= "   mudaTelaPrincipal(\"alteraAssunto.php?".Sessao::getId()."&pagina=".$pagina."&codClassificacao=".$codClassificacao."\");\n";
              $script .= "</script>\n";
       } else {
          $script  = "<script type=\"text/javascript\"> \n";
              $script .= "   alertaAviso(\"".$altera->nome."\",\"n_alterar\",\"erro\", \"".Sessao::getId()."\");\n";
              $script .= "   mudaTelaPrincipal(\"alteraAssunto.php?".Sessao::getId()."&pagina=".$pagina."\");\n";
              $script .= "</script>\n";
       }
       echo $script;
    }
   }
    include '../../../framework/include/rodape.inc.php';
?>
