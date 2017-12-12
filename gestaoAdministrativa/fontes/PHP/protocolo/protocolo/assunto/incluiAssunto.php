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
include '../assunto.class.php';
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
setAjuda('uc-01.06.95');

/**************************************************************************
 Gera uma combo marcando um valor pré-selecionado, se houver
/**************************************************************************/
    function comboClass($default="",$nome="codClass")
    {
        $combo = "";
        $combo .= "<select name='".$nome."' style='width: 200px;' onChange=\"JavaScript: PreencheCampo(document.frm.codClass, document.frm.codClassTxt);\">\n";
            if ($default=="") {
                $selected = "selected";
                $combo .= "<option value='xxx' ".$selected.">Selecione uma opção</option>\n";
            }
            $sql = "Select cod_classificacao, nom_classificacao
                    From sw_classificacao
                    Order by nom_classificacao";
            //echo "<!--".$sql."-->";
            $conn = new dataBaseLegado;
            $conn->abreBD();
            $conn->abreSelecao($sql);
            $conn->fechaBD();
            $conn->vaiPrimeiro();
                while (!$conn->eof()) {
                    $cod = $conn->pegaCampo("cod_classificacao");
                    $nom = trim($conn->pegaCampo("nom_classificacao"));
                    $selected = "";
                        //Verifica se o valor passado para a função deve estar marcado
                    if ($cod==$default) {
                        $selected = "selected";
                    }
                    $conn->vaiProximo();
                    $combo .= "<option value='".$cod."' ".$selected.">".$nom."</option>\n";
                }
            $conn->limpaSelecao();
        $combo .= "</select>";

        return $combo;
    }//Fim da function combo

if (!(isset($ctrl)))
    $ctrl = 0;

$inclui = new assunto;

switch ($ctrl) {
    case 0:
        $montaDocs = $inclui->listaDocumentos();
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

        campo = f.codClass.value;
            if (campo=='xxx') {
                mensagem += "@O campo Classificação é obrigatório";
                erro = true;
            }

        campo = trim( f.nom.value );
            if (campo=="") {
                mensagem += "@O campo Descrição é obrigatório";
                erro = true;
            }

    if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
    return !(erro);
}

function Salvar()
{
    if (Valida()) {
        document.frm.action = "incluiAssunto.php?<?=Sessao::getId();?>&ctrl=1";
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
</script>
<form action='incluiAssunto.php?<?=Sessao::getId()?>&ctrl=0' method='POST' name='frm'>
<table width='100%'>
    <tr><td class='alt_dados' colspan='2'>Dados para assunto</td></tr>
    <tr>
        <td class=label width=30% title="Classificação para o assunto">*Classificação</td>
        <td class=field>
        <input type=text size=4 maxlength=8 name=codClassTxt onChange="JavaScript: PreencheCampo(document.frm.codClassTxt, document.frm.codClass);" value=<?=$codClass;?>>
            <?php echo comboClass($codClass); ?>
        </td>
    </tr>
    <tr>
        <td class=label title="Descrição do assunto">*Descrição</td>
        <td class=field>
            <input type="text" name="nom" size="30" maxlength="60">
        </td>
    </tr>
    <tr>
        <td class=label title="Define se o processo será confidencial">*Confidencial</td>
    <td class=field>
            <input type="radio" name="conf" value="f" checked>Não
            <input type="radio" name="conf" value="t">Sim
    </td>
    <tr>
    <td class='alt_dados' colspan=2>Documentos para assunto</td>
    </tr>
    <?php
        if ($montaDocs != "") {
            while (list ($key, $val) = each ($montaDocs)) {
              print "<tr>
                  <td class=label align=right>$val</td>
            <td class=field><input type='checkbox' name='doc[]' value='$key'></td>
             </tr>";//Cria as checkbox com o nome dos Documentos
        }
        } else {
            print "<tr>
                      <td class=label>&nbsp;</td>
                      <td class=field>Documentos não cadastrados</td>
                   </tr>";
        }
    ?>
    <tr>
       <td class='alt_dados' colspan=2>Atributos para assunto</td>
    </tr>
    <?php
        $montaAtributos = $inclui->listaAtributos($codAssunto, $codClass);//retorna uma matriz com os atributos
    if (count($montaAtributos)) {//CRIAR METODO PARA RECUPERAR OS ATRIBUTOS
        foreach ($montaAtributos as $arAtributos) {
            print "<tr>
                  <td class=label align=right>".$arAtributos[nomAtributo]."</td>
              <td class=field><input type='checkbox' name='atrib[]' value='".$arAtributos[codAtributo]."'></td>
               </tr>";
        }
    } else {
        print "<tr>
                      <td class=label>&nbsp;</td>
                      <td class=field>Atributos não cadastrados</td>
                   </tr>";
    }
    ?>
    <tr>
        <td class=field colspan="2">
            <?=geraBotaoOk();?>
       </td>
    </tr>
</table>
</form>
<?php
        break;
case 1:
    //Verifica se já existe um nome cadastrado
    if (!comparaValor("nom_assunto", $nom, "sw_assunto","And cod_classificacao = '".$codClass."'",1) ) {
        alertaAviso($PHP_SELF."?".Sessao::getId()."&ctrl=0&codClass=".$codClass,"O assunto ".$nom." já existe para esta classificação!","unica","erro", "'.Sessao::getId().'");
    } else {
        $inclui->codigo = pegaId("cod_assunto", "sw_assunto", "where cod_classificacao = $codClass");
        $inclui->setaVariaveis($codClass, $nom, $conf);
        if ($inclui->incluiAssunto()) { //Tenta incluir o tipo, caso consiga retorna mensagem de sucesso
            $inclui->incluiDocumentos($inclui->codigo, $codClass, $doc);
            $inclui->incluiAtributos($inclui->codigo, $codClass, $atrib);
            include '../../classes/auditoria.class.php'; //Inclui classe para inserir auditoria
            //Insere auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $inclui->codigo);
            $audicao->insereAuditoria();
            echo '
            <script type="text/javascript">
            alertaAviso("'.$inclui->nome.'","incluir","aviso", "'.Sessao::getId().'");
            mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'&ctrl=0&codClass='.$codClass.'");
            </script>';
        } else {
            echo '
            <script type="text/javascript">
            alertaAviso("'.$inclui->nome.'","n_incluir","erro", "'.Sessao::getId().'");
            mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'&ctrl=0&codClass='.$codClass.'");
            </script>';
         }
    }
}
include '../../../framework/include/rodape.inc.php';
?>
