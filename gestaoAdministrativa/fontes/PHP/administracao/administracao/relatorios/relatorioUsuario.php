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
    * Manutneção de relatórios
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-01.03.94

    $Id: relatorioUsuario.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";

setAjuda("UC-01.03.94");

?>

<script type="text/javascript">
    function validacao(cod)
    {
        var f = document.frm;
        f.action = "<?=$_SERVER['PHP_SELF'];?>?<?=Sessao::getId()?>&controle="+cod;
        f.target = 'oculto';
        f.submit();
    }

    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f = document.frm;

        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        var f = document.frm;
        f.ok.disabled = true;
        if (Valida()) {
            f.action = "relatorioUsuarioMostra.php?<?=Sessao::getId()?>";
            f.target = "telaPrincipal";
            f.submit();
        } else {
            f.ok.disabled = false;
        }
    }
</script>

<form action="relatorioUsuarioMostra.php?<?=Sessao::getId()?>" method="POST" name="frm">
<input type="hidden" name="comboOrg">
<input type="hidden" name="anoOrg">

<table width=100%>
    <tr>
        <td class="alt_dados" colspan=2>Filtrar por:</td>
    </tr>
</table>
<?php

    $obFormulario = new Formulario;
    $obFormulario->addForm(null);

    $obIMontaOrganograma = new IMontaOrganograma(true);
    $obIMontaOrganograma->geraFormulario($obFormulario);

    $obFormulario->montaHtml();
    echo $obFormulario->getHTML();

?>
<table width=100%>
    <tr>
        <td class="label" width=20%>Ordenar por &nbsp;</td>
        <td class="field">
            <select name="orderby">
            <option value="sw_cgm.numcgm" SELECTED>CGM</option>
            <option value="sw_cgm.nom_cgm">Nome</option>
            <option value="usuario.username">Username</option>
            <option value="descricao">Órgão</option>
            </select>
        </td>
    </tr>
    <tr>
        <td colspan='2' class='field'>
            <?php geraBotaoOk(1,1,0); ?>
        </td>
    </tr>
</table>
</form>

<?php

    executaFrameOculto($js);

    include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
