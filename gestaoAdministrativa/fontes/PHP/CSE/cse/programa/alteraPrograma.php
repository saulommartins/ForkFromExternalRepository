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
* Arquivo de instância para Programa
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 3219 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 14:25:34 -0200 (Qui, 01 Dez 2005) $

* Casos de uso: uc-01.07.95
*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
    include_once '../cse.class.php';
    include_once (CAM_FW_LEGADO."paginacaoLegada.class.php");
    include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"); //Inclui classe para inserir auditoria

if (!isset($controle)) {
    $controle = 0;
    $sessao->transf = "";
}

switch ($controle) {
case 0:
$sql = "Select cod_programa, exercicio, nom_programa
        From cse.programa_social Where cod_programa > 0 ";
//echo $sql;
//Inicia o relatório em html
$paginacao = new paginacaoLegada;
$paginacao->pegaDados($sql,"10");
$paginacao->pegaPagina($pagina);
$paginacao->geraLinks();
$paginacao->pegaOrder("lower(nom_programa)","ASC");
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
        <td colspan="4" class="alt_dados">
            Programas Sociais Cadastrados
        </td>
    <tr>
        <td width="5%" class="label">
            &nbsp;
        </td>
        <td class="labelcenter" width="12%">
            Código
        </td>
        <td class='labelcenter' width='80%'>
            Nome do Programa
        </td>
        <td class='label' width='10%'>
            &nbsp;
        </td>
    </tr>
<?php
    if ( !$conn->eof() ) {
        $iCont = $paginacao->contador();
        while (!$conn->eof()) {
            $cod = $conn->pegaCampo("cod_programa");
            $ex = $conn->pegaCampo("exercicio");
            $nom = $conn->pegaCampo("nom_programa");
            $conn->vaiProximo();
?>
    <tr>
        <td class="label">
            <?=$iCont++?>
        </td>
        <td class='show_dados_right'>
            <?=$cod;?>
        </td>
        <td class='show_dados'>
            <?=$nom;?>
        </td>
        <td class='botao'>
            <a href='<?=$PHP_SELF?>?<?=$sessao->id?>&controle=1&codPrograma=<?=$cod;?>&exercicio=<?=$ex;?>&pagina=<?=$pagina;?>' >
            <img src='<?=CAM_FW_IMAGENS."btneditar.gif";?>' border='0'>
            </a>
        </td>
    </tr>
<?php
        }
    } else {
?>
    <tr>
        <td class="show_dados_center" colspan="4">
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
$nomPrograma = pegaDado("nom_programa","cse.programa_social","
               Where cod_programa = '".$codPrograma."' And exercicio = '".$exercicio."' ");
$descricao = pegaDado("descricao","cse.programa_social","
             Where cod_programa = '".$codPrograma."' And exercicio = '".$exercicio."' ");

?>
<script type="text/javascript">
    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f = document.frm;

        campo = f.nomPrograma.value.length;
            if (campo==0) {
                mensagem += "@Campo Programa inválido!()";
                erro = true;
            }

        campo = f.descricao.value.length;
            if (campo==0) {
                mensagem += "@Campo Descrição inválido!()";
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
        document.frm.target= "telaPrincipal";
        document.frm.controle.value = 0;
        document.frm.submit();
    }
</script>
<form name='frm' method='post' action='<?=$PHP_SELF;?>?<?=$sessao->id;?>' target='oculto'>
<input type='hidden' name='controle' value='2'>
<input type='hidden' name='codPrograma' value="<?=$codPrograma;?>">
<input type='hidden' name='exercicio' value="<?=$exercicio;?>">
<input type='hidden' name='pagina' value="<?=$pagina;?>">
<table width='100%'>
    <tr>
        <td class="alt_dados" colspan="2">
            Dados do Programa Social
        </td>
    </tr>
    <tr>
        <td class='label' width='20%' title="Nome do programa social">
            *Programa
        </td>
        <td class='field' width='80%'>
            <textarea name='nomPrograma' cols='40' rows='1'
            onKeyPress="return(maxTextArea(this.form.nomPrograma,160,event));"
            onBlur="return(maxTextArea(this.form.nomPrograma,160,event,true));"
            ><?=$nomPrograma;?></textarea>
        </td>
    </tr>
    <tr>
        <td class='label' title="Descrição do programa social">
            *Descrição
        </td>
        <td class='field'>
        <textarea name='descricao' cols='40' rows='2'
        onKeyPress="return(maxTextArea(this.form.descricao,240,event));"
        onBlur="return(maxTextArea(this.form.descricao,240,event,true));"
        ><?=$descricao;?></textarea>
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
    if (!comparaValor("nom_programa", $nomPrograma, "cse.programa_social","And cod_programa <> '".$codPrograma."' And exercicio = '".$exercicio."' ",1)) {
        $js .= "mensagem += '@O nome ".$nomPrograma." já existe'; \n";
        $ok = false;
    }

/*** Se não houver restrições faz a inclusão dos dados ***/
    if ($ok) {
        $cse = new cse();

        $objeto = $nomPrograma;
        if ($cse->alterarPrograma($_POST) ) {
            //Insere auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $objeto);
            $audicao->insereAuditoria();
            //Exibe mensagem e retorna para a página padrão
            sistemaLegado::alertaAviso($PHP_SELF."?pagina=".$pagina,$objeto,"alterar","aviso","");
        } else {
            sistemaLegado::exibeAviso($objeto,"n_alterar","erro");
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
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
