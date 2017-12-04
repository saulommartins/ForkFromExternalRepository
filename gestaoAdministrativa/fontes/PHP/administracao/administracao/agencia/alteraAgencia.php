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
* Manutenção de agência
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3242 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 15:59:40 -0200 (Qui, 01 Dez 2005) $

Casos de uso: uc-01.03.97
*/

 include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
 include(CAM_FW_LEGADO."funcoesLegado.lib.php");
 include CAM_FW_LEGADO."auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
 include '../agencia.class.php';
 include CAM_FW_LEGADO."paginacaoLegada.class.php"; //Classe para gerar paginação dos dados

/**************************************************************************
 Gera uma combo marcando um valor pré-selecionado, se houver
/**************************************************************************/
function comboBanco($default="",$nome="nomBanco")
{
    $combo = "";
    $combo .= "<select name='".$nome."' style='width: 200px;' onchange='retornaCodBanco(this.value);'>\n";
        if($default=="")
            $selected = "selected";
    $combo .= "<option value='xxx' ".$selected.">Selecione uma opção</option>\n";
        $sql = "Select cod_banco, nom_banco
                From administracao.banco
                Order by nom_banco";
        //echo "<!--".$sql."-->";
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
            while (!$conn->eof()) {
                $cod = $conn->pegaCampo("cod_banco");
                $nom = trim($conn->pegaCampo("nom_banco"));
                $selected = "";
                    //Verifica se o valor passado para a função deve estar marcado
                    if($cod==$default)
                        $selected = "selected";
                $conn->vaiProximo();
                $combo .= "<option value='".$cod."' ".$selected.">".$nom."</option>\n";
            }
        $conn->limpaSelecao();
    $combo .= "</select>";
    print $combo;
}//Fim da function comboBanco

if (!isset($controle)) {
    $controle = 0;
}

if (!isset($pagina)) {
    $pagina = 0;
}

switch ($controle) {
case 0:
$sql = "Select a.cod_banco, a.cod_agencia, b.nom_banco, a.nom_agencia
        From administracao.banco as b, administracao.agencia as a
        Where b.cod_banco = a.cod_banco ";

//Inicia o relatório em html
$paginacao = new paginacaoLegada;
$paginacao->pegaDados($sql,"10");
$paginacao->pegaPagina($pagina);
$paginacao->geraLinks();
$paginacao->pegaOrder("lower(nom_banco), lower(nom_agencia)","ASC");
$sSQL = $paginacao->geraSQL();

//Pega os dados encontrados em uma query
$conn = new dataBaseLegado;
$conn->abreBD();
$conn->abreSelecao($sSQL);
$conn->fechaBD();
$conn->vaiPrimeiro();
    if($conn->numeroDeLinhas==0)
        exit("<br><b>Nenhum registro encontrado!</b>");

    $html = "";
    $html .= "
        <table width='100%'>
            <tr>
                <td class='alt_dados' colspan='6'>Registros de Agência</td>
            </tr>
            <tr>
                <td class='labelleft' width='5%'>&nbsp;</td>
                <td class='labelleft' width='10%' nowrap=''>Cód. do Banco</td>
                <td class='labelleft' width='30%'>Nome do Banco</td>
                <td class='labelleft' width='10%' nowrap=''>Cód. da Agência</td>
                <td class='labelleft' width='44%'>Nome da Agência</td>
                <td class='labelleft' width='1%'>&nbsp;</td>
            </tr>
        ";
        $count = $paginacao->contador();
        while (!$conn->eof()) {
            $codAgencia = $conn->pegaCampo("cod_agencia");
            $nomAgencia = $conn->pegaCampo("nom_agencia");
            $codBanco = $conn->pegaCampo("cod_banco");
            $nomBanco = $conn->pegaCampo("nom_banco");
            $conn->vaiProximo();
            $html .= "
                <tr>
                    <td class='labelcenter'>".$count++."</td>
                    <td class='show_dados'>".$codBanco."</td>
                    <td class='show_dados'>".$nomBanco."</td>
                    <td class='show_dados'>".$codAgencia."</td>
                    <td class='show_dados'>".$nomAgencia."</td>
                    <td class='botao'>
                    <a href='".$PHP_SELF."?".Sessao::getId()."&controle=1&codBanco=".$codBanco."&codAgencia=".$codAgencia."&nomBanco=".$nomBanco."&nomAgencia=".$nomAgencia."&pagina=".$pagina."' >
                    <img src='".CAM_FW_IMAGENS."btneditar.gif' border='0'>
                    </a>
                </td>
                </tr>";
        }
        $html .= "</table>";
echo $html;
?>
        <table width='450' align='center'><tr><td align='center'><font size='2'>
         <?php $paginacao->mostraLinks();  ?>
        </font></tr></td></table>
<?php
    break;
case 1:
$agencia = new agencia($codBanco,$codAgencia);
$agencia->retornaAgencia();
$nomBanco = $agencia->nomBanco;
$nomAgencia = $agencia->nomAgencia;
?>
<script type="text/javascript">
    function retornaCodBanco(cod)
    {
        var f;

        f = document.frm;

        if (cod=="xxx") {
            f.codBanco.value = "";
        } else {
            f.codBanco.value = cod;
        }
    }

    function Cancela()
    {
        pag = "<?=$PHP_SELF?>?<?=Sessao::getId()?>&pagina=<?=$pagina?>&controle=0";
        mudaTelaPrincipal(pag);

    }
    function validaCodBanco(iCod)
    {
        var cod = parseInt(iCod);
        var val;
        var erro = true;
        var msg = "O Código do Banco "+iCod+" é Inválido";
        var f = document.frm;
        var campo = f.nomBanco;
        var tam = campo.options.length - 1;
        if(f.codBanco.value.length==0)

            return false;
        //Percorre todos os valores para encontrar qual item da combo tem o valor digitado
        while (tam >= 0) {
            val = parseInt(f.nomBanco.options[tam].value);
            if (cod==val) {
                f.nomBanco[tam].selected = true;
                erro = false;
            }
            tam = tam - 1 ;
        }
        //Se não encontrou o valor o código digitado é inválido
        if (erro) {
            f.codBanco.value = "";
            f.codBanco.focus();
            f.nomBanco[0].selected = true;
            alertaAviso(msg,'unica','erro','<?=Sessao::getId()?>','');
        }
    }

    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var f;

        f = document.frm;

        campo = f.codBanco.value.length;
            if (campo==0) {
                mensagem += "@Código do Banco";
                erro = true;
            }

        campo = f.codAgencia.value.length;
            if (campo==0) {
                mensagem += "@Código da Agência";
                erro = true;
            }

        campo = f.nomAgencia.value.length;
            if (campo==0) {
                mensagem += "@Nome";
                erro = true;
            }

        if (erro) alertaAviso(mensagem,'formulario','','<?=Sessao::getId()?>','');
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
</script>

<form name='frm' method='post' target="oculto" action='<?=$PHP_SELF;?>?<?=Sessao::getId();?>'>
<input type='hidden' name='controle' value='2'>
<input type='hidden' name='pagina' value='<?=$pagina;?>'>
<input type='hidden' name='codBanco' value='<?=$codBanco;?>'>
<input type='hidden' name='codAgencia' value='<?=$codAgencia;?>'>
<table width='100%'>
<tr><td class='alt_dados' colspan='2'>Dados para Agência</td></tr>
<tr>
    <td class='label' width='30%'>Código do Banco:</td>
    <td class='field' width='70%'><?=$codBanco;?></td>
</tr>
<tr>
    <td class='label' width='30%'>Nome do Banco:</td>
    <td class='field' width='70%'><?=$_REQUEST["nomBanco"];?></td>
</tr>

<tr>
    <td class='label' width='30%'>Código da Agência:</td>
    <td class='field' width='70%'><?=$codAgencia;?></td>
</tr>
<tr>
    <td class='label' width='30%'>*Nome da Agência:</td>
    <td class='field' width='70%'>
        <input type='text' name='nomAgencia' value="<?=$_REQUEST["nomAgencia"];?>" size='40' maxlength='80'>
    </td>
</tr>
<tr>
    <td colspan='2' class='field'>
        <?php geraBotaoOk(1,0,1,1); ?>
    </td>
</tr>
</table>
</form>

<?php
    break;
case 2:
    $ok = true;
    $objeto = "Agência ".$codAgencia." - ".$nomAgencia;
    $pag = $PHP_SELF."?".Sessao::getId()."&pagina=".$pagina;
/*** Primeiro valida os dados, depois faz a alteração ***/
    //Verifica se já existe o registro a ser incluido
    if (!comparaValor("nom_agencia", $nomAgencia, "administracao.agencia","And cod_banco = '".$codBanco."' And cod_agencia <> '".$codAgencia."'",1)) {
        alertaAviso($pag,"O nome ".$nomAgencia." já existe!","unica","erro",'');
        $ok = false;
    }
/*** Se não houver restrições faz a inclusão dos dados ***/
    if ($ok) {
        $agencia = new agencia($codBanco,$codAgencia,$nomAgencia);
        if ($agencia->alterarAgencia()) {
            //Insere auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $objeto);
            $audicao->insereAuditoria();
            //Exibe mensagem e retorna para a página padrão
            alertaAviso($pag,$objeto,"alterar","aviso",'');
        } else {
            //alertaAviso($pag,$objeto,"n_alterar","erro");
            exibeAviso($objeto,"n_alterar","erro");
            $js = "f.ok.disabled = false;";
            executaFrameOculto($js);
        }
    }
    break;
}//Fim switch

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
