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
* Arquivo de instância para Instituição de Saúde
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 19067 $
$Name$
$Author: rodrigo_sr $
$Date: 2007-01-03 09:33:57 -0200 (Qua, 03 Jan 2007) $

* Casos de uso: uc-01.07.88
*/

  include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
  include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"    );
  include_once '../cse.class.php';
  include_once (CAM_FW_LEGADO."paginacaoLegada.class.php");
  include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"); //Inclui classe para inserir auditoria

if (!isset($controle)) {
    $controle = 0;
    $sessao->transf = "";
}

switch ($controle) {
case 0:
$sql  = " SELECT ";
$sql .= "   cod_instituicao as cod, ";
$sql .= "   nom_instituicao as nom ";
$sql .= " FROM ";
$sql .= "   cse.instituicao_saude ";
$sql .= " WHERE ";
$sql .= "   cod_instituicao > 0 ";
//echo $sql;
//Inicia o relatório em html
$paginacao = new paginacaoLegada;
$paginacao->pegaDados($sql,"10");
$paginacao->pegaPagina($pagina);
$paginacao->geraLinks();
$paginacao->pegaOrder("lower(nom_instituicao)","ASC");
$sSQL = $paginacao->geraSQL();

//Pega os dados encontrados em uma query
$conn = new dataBaseLegado;
$conn->abreBD();
$conn->abreSelecao($sSQL);
$conn->fechaBD();
$conn->vaiPrimeiro();
?>
<table width='100%'>
    <tr>
        <td class='alt_dados' colspan="4">
            Instituições Cadastradas
        </td>
    </tr>
    <tr>
        <td width="5%" class="label">
            &nbsp;
        </td>
        <td width="12%" class="labelcenter">
            Código
        </td>
        <td width="80%" class="labelcenter">
            Instituição
        </td>
        <td class="label">
            &nbsp;
        </td>
    </tr>
<?php
    if ( !$conn->eof() ) {
        $iCont = $paginacao->contador();
       // print_r($conn);
        while (!$conn->eof()) {
            $cod = $conn->pegaCampo("cod");
            $nom = $conn->pegaCampo("nom");
            $conn->vaiProximo();
?>
    <tr>
        <td class='label'>
            <?=$iCont++;?>
        </td>
        <td class='show_dados_right'>
            <?=$cod;?>
        </td>
        <td class='show_dados'>
            <?=$nom;?>
        </td>
        <td class='botao'>
            <a href='<?=$PHP_SELF;?>?<?=$sessao->id;?>&controle=1&codInstituicao=<?=$cod;?>&pagina=<?=$pagina?>' >
            <img src='<?=CAM_FW_IMAGENS."btneditar.gif";?>' border='0'></a>
        </td>
    </tr>
<?php
        }
    } else {
?>
    <tr>
        <td class='show_dados_center' colspan="4">
            <b>Nenhum registro encontrado!</b>
        </td>
    </tr>
<?php
    }
?>
</table>
<table width='450' align='center'>
    <tr>
        <td align='center'>
            <font size='2'>
            <?php $paginacao->mostraLinks();  ?>
            </font>
        </td>
    </tr>
</table>
<?php
    break;

//Formulário em HTML para entrada de dados
case 1:
$nomInstituicao = pegaDado("nom_instituicao","cse.instituicao_saude","Where cod_instituicao = '".$codInstituicao."' ");
?>
<script type="text/javascript">
    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f = document.frm;

        campo = f.nomInstituicao.value.length;
        if (campo==0) {
            mensagem += "@Campo Nome inválido!()";
            erro = true;
        }

        if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>','');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        document.frm.ok.disabled = true;
        if (Valida()) {
            document.frm.submit();
        } else {
            document.frm.ok.disabled = false;
        }
    }

    function Cancela()
    {
        document.frm.target="telaPrincipal";
        document.frm.controle.value = 0;
        document.frm.submit();
    }
</script>
<form name='frm' method='post' action='<?=$PHP_SELF;?>?<?=$sessao->id;?>' target="oculto" onSubmit="return false;">
<input type='hidden' name='controle' value='2'>
<input type='hidden' name='codInstituicao' value="<?=$codInstituicao;?>">
<input type='hidden' name='pagina' value="<?=$pagina;?>">
<table width='100%'>
    <tr>
        <td class="alt_dados" colspan="2">
            Instituição
        </td>
    </tr>
    <tr>
        <td class='label' width='20%' title="Nome da instituição">
            *Nome
        </td>
        <td class='field' width='80%'>
            <input type='text' name='nomInstituicao' value="<?=htmlentities($nomInstituicao);?>" size='40' maxlength='80' onKeyUp="return autoTab(this, 80, event);" >
        </td>
    </tr>
    <tr>
        <td colspan='2' class='field'>
            <?php geraBotaoAltera(); ?>
        </td>
    </tr>
</table>
</form>
<?php
    break;

//Inclusão, alteração ou exclusão de dados
case 2:
    $js = "";
    $ok = true;
    //Verifica se já existe o registro a ser incluido
    if (!comparaValor("nom_instituicao", $nomInstituicao, "cse.instituicao_saude","And cod_instituicao <> '".$codInstituicao."' ",1)) {
        $js .= "mensagem += '@O nome ".$nomInstituicao." já existe'; \n";
        $ok = false;
    }
/*** Se não houver restrições faz a inclusão dos dados ***/
    if ($ok) {
        $obj = new cse();

        $objeto = urlencode($nomInstituicao);
        if ($obj->alterarInstituicaoSaude($codInstituicao,$nomInstituicao) ) {
            //Insere auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $objeto);
            $audicao->insereAuditoria();
            //Exibe mensagem e retorna para a página padrão
            alertaAviso($PHP_SELF."?pagina=".$pagina,$objeto,"alterar","aviso","");
        } else {
            exibeAviso($objeto,"n_alterar","erro");
            $js .= "f.ok.disabled = false; \n";
        }
    } else {
        $js .= "f.ok.disabled = false; \n";
        $js .= "erro = true; \n";
    }
    break;

}//Fim switch

?>
<html>
<head>
<script type="text/javascript">
function executa()
{
    var mensagem = "";
    var erro = false;
    var f = window.parent.frames["telaPrincipal"].document.frm;
    var d = window.parent.frames["telaPrincipal"].document;
    var aux;
    <?php echo $js; ?>

    if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>','');
}
</script>
</head>

<body onLoad="javascript:executa();">

</body>
</html>
<?php
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
