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
    * Arquivo que faz a alteração dos atributos
    * Data de Criação   : 26/03/2003

    * @author Desenvolvedor Leonardo Tremper
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 28299 $
    $Name$
    $Autor: $
    $Date: 2008-02-29 14:31:59 -0300 (Sex, 29 Fev 2008) $

    * Casos de uso: uc-03.01.02
*/

/*
$Log$
Revision 1.21  2006/08/14 17:32:04  fernando
Alterações para funcionar o ajuda.

Revision 1.20  2006/08/14 15:07:34  fernando
Alterações para funcionar o ajuda.

Revision 1.19  2006/07/13 13:49:24  fernando
Alteração de hints

Revision 1.18  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.17  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda('UC-03.01.02');

$anoE = Sessao::getExercicio();

if (!(isset($ctrl)))
    $ctrl = 0;

// valor do $ctrl e passado por hidden nos formularios deste arquivo
switch ($ctrl) {

// lista atributos cadastrados...
case 0:

    // seleciona atributos...
    if (isset($acao)) {
        $sSQLs = "SELECT cod_atributo, nom_atributo FROM administracao.atributo_dinamico";
        $sessao->transf = $sSQLs;
    }

    // gera lista de atributos com paginacao
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
    $paginacao = new paginacaoLegada;
    $paginacao->pegaDados($sessao->transf,"10");
    $paginacao->pegaPagina($pagina);
    $paginacao->geraLinks();
    $paginacao->pegaOrder("nom_atributo","ASC");
    $sSQL = $paginacao->geraSQL();

    //print $sSQL;
    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();

?>
    <table width="100%">
    <tr>
        <td class="alt_dados" colspan="4">Registros de Atributo</td>
    </tr>
    <tr>
        <td class="labelcenter" width="5%">&nbsp;</td>
        <td class="labelcenter" width="12%">Código</td>
        <td class="labelcenter" width="80%">Atributo</td>
        <td class="labelcenter">&nbsp;</td>
    </tr>
<?php
    $cont = $paginacao->contador();

    // lista atributos encontrados
    while (!$dbEmp->eof()) {
        $codAtributof  = trim($dbEmp->pegaCampo("cod_atributo"));
        $nomAtributof  = trim($dbEmp->pegaCampo("nom_atributo"));
        $dbEmp->vaiProximo();
?>
        <tr>
            <td class="labelcenter" width="5%"><?=$cont++;?></td>
            <td class="show_dados_right">&nbsp;<?=$codAtributof;?></td>
            <td class="show_dados">&nbsp;<?=$nomAtributof;?></td>
            <td class="botao" title="Alterar">
                <a href='alteraAtributo.php?<?=Sessao::getId();?>&codAtributo=<?=$codAtributof;?>&ctrl=1'>
                <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif'   border="0">
                </a>
            </td>
        </tr>
<?php
    }
?>
    </table>
<?php
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();

    echo "<table width=100% align=center><tr><td align=center><font size=2>";
    $paginacao->mostraLinks();
    echo "</font></tr></td></table>";

    break;

// abre atributo para edicao
case 1:

    // busca dados do atributo...
    $sSQL = "select cod_atributo, nom_atributo, tipo, valor_padrao from administracao.atributo_dinamico WHERE cod_atributo
    = ".$codAtributo;
    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();

    // atribui dados as variaveis
    $codAtributo = trim($dbEmp->pegaCampo("cod_atributo"));
    $nomAtributo  = trim($dbEmp->pegaCampo("nom_atributo"));
    $tipo  = trim($dbEmp->pegaCampo("tipo"));
    $valorPadrao  = trim($dbEmp->pegaCampo("valor_padrao"));

    // free BD
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();

?>
    <script type="text/javascript">
        // valida campos do formularioZZ
        function Valida()
        {
            var mensagem = "";
            var erro = false;
            var campo;
            var campoaux;

            campo = trim(document.frm.nomAtributo.value).length;
                if (campo == 0) {
                mensagem += "@O campo Descrição do Atributo é obrigatório.";
                erro = true;
            }

                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                return !(erro);
        }

        // executa formulario
        function Salvar()
        {
            var f = document.frm;
            f.ok.disabled = true;
            if (Valida()) {
                f.submit();
            } else {
                f.ok.disabled = false;
            }
        }

        function Cancela()
        {
            mudaTelaPrincipal("alteraAtributo.php?<?=Sessao::getId();?>&ctrl=0&pagina=<?=$pagina;?>");
        }
    </script>
<?php
    // monta formulario com os dados do atributo selecionado
?>
    <form name="frm" action="alteraAtributo.php?<?=Sessao::getId()?>" method="POST" target='oculto'>

    <table width='100%'>

    <tr>
        <td colspan="2" class="alt_dados">Dados para o Atributo</td>
    </tr>

    <tr>
        <td class="label" width="20%">Código</td>
        <td class="field"><?=$codAtributo?></td>
    </tr>

    <tr>
        <td class="label" title="Informe a descrição do atributo.">*Descrição do Atributo</td>
        <td class="field">
            <input type="text" name="nomAtributo" size="40" maxlength="40" value="<?=$nomAtributo;?>">
            <input type="hidden" name="codAtributo" value="<?=$codAtributo;?>">
            <input type="hidden" name="tipo" value="<?=$tipo;?>">
            <input type="hidden" name="ctrl" value="2">
        </td>
    </tr>

    <tr>
        <td class="label">Tipo</td>
        <td class="field">
<?php
        switch ($tipo) {
        case 't':
            echo "Texto";
        break;
        case 'n':
            echo "Número";
        break;
        case 'l':
            echo "Lista";
        break;
        }
?>
        </td>
    </tr>

    <tr>
        <td class="label"  style="vertical-align: top;" title="Informe o valor pré-definido para o atributo.">Valor Padrão</td>
        <td class="field">
            <textarea name="valorPadrao" cols="30" rows="6"><?=$valorPadrao;?></textarea>
        </td>
    </tr>

    <tr>
        <td colspan='2' class='field'>
            <?=geraBotaoAltera();?>
        </td>
    </tr>

    </table>

    </form>
<?php
    break;

// quando o botao de OK é clicado vem para esse 'case'
case 2:

    // se o 'tipo' do atributo for numero ( n ), aceita somente numeros no campo de Valor Padrao
    if ($tipo == "n") {
            if (!is_numeric($valorPadrao) && ($valorPadrao != null || $valorPadrao != '')) {
            $js .= "window.parent.frames['telaPrincipal'].document.frm.ok.disabled = false;\n";
            $js .= 'alertaAviso("O Valor Padrão deve conter apenas números quando o tipo for Número.","unica","erro","'.Sessao::getId().'");';
            break;
         }
    }

    include_once '../configPatrimonio.class.php';

    $objeto = "Atributo: ".$nomAtributo;
    $patrimonio = new configPatrimonio;
    $patrimonio->setaVariaveisAtributos($codAtributo, $nomAtributo, $tipo, $valorPadrao);

    /* verifca se ja existe um atributo cadastrat com o nomAtributo digitado
    se nao possuir nenhum atributo cadastrado com o nome informado,
    atualiza (update) os dados do atributo selecionado */
    if (comparaValor("nom_atributo", $nomAtributo,"administracao.atributo_dinamico", "and cod_atributo <>
    $codAtributo",1)){

        // executa o update do atributo selecionado...
        if ($patrimonio->updateAtributo()) {
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $nomAtributo);
            $audicao->insereAuditoria();

            echo '<script type="text/javascript">
            alertaAviso("'.$objeto.'","alterar","aviso","'.Sessao::getId().'");
            mudaTelaPrincipal("alteraAtributo.php?'.Sessao::getId().'");
            </script>';

       } else {
            echo '<script type="text/javascript">
            alertaAviso("'.$objeto.'","n_alterar","erro","'.Sessao::getId().'");
            mudaTelaPrincipal("alteraAtributo.php'.Sessao::getId().'");
            </script>';
        }

    // se ja existe um atributo com este nome...
    } else {
        $js = "window.parent.frames['telaPrincipal'].document.frm.ok.disabled = false;";
        echo '
            <script type="text/javascript">
            alertaAviso("O Atributo '.$nomAtributo.' já existe","unica","erro","'.Sessao::getId().'");
            </script>';
    }
    break;
}

//executaFrameOculto($js);
$jsOnLoad = $js;

//include_once "../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
