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

    $Id: relatorioPermissao.php 63829 2015-10-22 12:06:07Z franver $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CAM_FW_LEGADO."paginacaoLegada.class.php";
include_once CAM_FW_LEGADO."botoesPdfLegado.class.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";

setAjuda("UC-01.03.94");

$pagina   = $_REQUEST['pagina'];
$controle = $_REQUEST['controle'];

if (isset($pagina)) {
    $controle = 1;
}

if (!isset($controle)) {
    $controle = 0;
}

switch ($controle) {
case 0:
?>
<form action="<?=$_SERVER['PHP_SELF'];?>?<?=Sessao::getId()?>&filtro=1" method="POST" name="frm">
<input type="hidden" name="controle" value="1">
<table width="100%">
<tr>
    <td class="alt_dados" colspan=2>Filtrar por:</td>
</tr>
<tr>
    <td class='label' width='20%'>Usuário</td>
    <td class='field' nowrap>
        <input type='text' name="numCgm" value="" size='5' maxlength='10' onBlur="validacao(5);" onKeyPress="return(isValido(this,event,'0123456789'))" >
        <input type='text' name="nomCgm" size=25 readonly="" value="">
        &nbsp;<a href="javascript:procurarCgm('frm','numCgm','nomCgm','usuario','<?=Sessao::getId();?>');"><img
        src='<?=CAM_FW_IMAGENS."procuracgm.gif";?>' alt='Busca' width='20' height='20' border='0' align='absmiddle'></a>
    </td>
</tr>
<tr>
    <td class="label">Módulo</td>
    <td class='field'>
    <?php
        echo montaComboGenerico("codModulo", "administracao.modulo", "cod_modulo", "nom_modulo", "",
                "style='width: 200px;' ", "", true, false, false,"","Todos");
    ?>
    </td>
</tr>
</table>

<?php

    $obFormulario = new Formulario;
    $obFormulario->setForm(null);

    $obIMontaOrganograma = new IMontaOrganograma(true);
    $obIMontaOrganograma->geraFormulario($obFormulario);

    $obFormulario->montaHTML();
    echo $obFormulario->getHTML();

?>
<script type="text/javascript">
    function validacao(cod)
    {
        var f = document.frm;
        f.target = 'oculto';
        f.controle.value = cod;
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
            f.controle.value = 1;
            f.target = "telaPrincipal";
            f.submit();
        } else {
            f.ok.disabled = false;
        }
    }
</script>

<table width="100%">
<tr>
    <td class="label" width="20%">Ordenar por</td>
    <td class="field">
        <select name="orderby">
        <option value="u.numcgm" SELECTED>CGM</option>
        <option value="lower(c.nom_cgm)">Nome</option>
        <option value="u.username">Username</option>
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
    break;
case 1:

    $arFiltro = Sessao::read('filtro');
    foreach ($_REQUEST as $chave=>$filtro) {
        $arFiltro[$chave] = $filtro;
    }
    Sessao::write('filtro', $arFiltro);

    $cmbOrgao   = $arFiltro['cmbOrgao'];
    $numCgm     = $arFiltro['numCgm'];
    $nomCgm     = $arFiltro['nomCgm'];
    $codModulo  = $arFiltro['codModulo'];
    $codDpto    = $arFiltro['codDpto'];
    $codUnidade = $arFiltro['codUnidade'];
    $codSetor   = $arFiltro['codSetor'];
    $orderby    = $arFiltro['orderby'];

    $arCodOrgao = explode("/",$cmbOrgao);
    $inCodOrgao = $arCodOrgao[0];
    $stExercicio = $arCodOrgao[1];

?>

<form action="permissao.php?<?=Sessao::getId()?>" method="POST" name="frm">
    <script type="text/javascript">
        document.frm.action = "permissao.php?<?=Sessao::getId()?>&filtro=1&controle=1&numCgm=<?=$numCgm?>&nomCgm=<?=$nomCgm?>&codModulo=<?=$codModulo?>&codOrgao=<?=$inCodOrgao?>&codUnidade=<?=$codUnidade?>&codDpto=<?=$codDpto?>&codSetor=<?=$codSetor?>&orderby=<?=$orderby?>";
        document.frm.submit();
    </script>
</form>

<?php

    break;

//Altera a combo de Unidades conforme o Órgão selecionado
case 2:

    $cmbOrgao = $_REQUEST['cmbOrgao'];

    $js = "";
    $js .= "limpaSelect(f.codUnidade,1); \n";
    $js .= "limpaSelect(f.codDpto,1); \n";
    $js .= "limpaSelect(f.codSetor,1); \n";
    if ($cmbOrgao != "XXX") {
        $arCodOrgao = explode("/",$cmbOrgao);
        $inCodOrgao = $arCodOrgao[0];
        $stExercicio = $arCodOrgao[1];
        $sql = "SELECT cod_unidade, nom_unidade FROM administracao.unidade
                 WHERE cod_orgao = ".$inCodOrgao."
                 AND ano_exercicio = ".$stExercicio."
                 AND cod_unidade > 0
                 ORDER by lower(nom_unidade) ";
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
        $cont = 1;
        $js .= "campo = f.codUnidade; \n";
            while (!$conn->eof()) {
                $cod = $conn->pegaCampo("cod_unidade");
                $nom = $conn->pegaCampo("nom_unidade");
                $js .= 'campo.options['.$cont.'] = new Option("'.$nom.'",'.$cod.'); ';
                $conn->vaiProximo();
                $cont++;
            }
        $conn->limpaSelecao();
    }

    break;

//Altera a combo de Departamento conforme a unidade selecionada
case 3:

    $codUnidade = $_REQUEST['codUnidade'];
    $cmbOrgao = $_REQUEST['cmbOrgao'];

    $js = "";
    $js .= "limpaSelect(f.codDpto,1); \n";
    $js .= "limpaSelect(f.codSetor,1); \n";
    if ($codUnidade != "XXX") {
        $arCodOrgao = explode("/",$cmbOrgao);
        $inCodOrgao = $arCodOrgao[0];
        $stExercicio = $arCodOrgao[1];
        $js = "";
        $sql = "Select cod_departamento, nom_departamento
                From administracao.departamento
                Where ano_exercicio = '".$stExercicio."'
                And cod_orgao = '".$inCodOrgao."'
                And cod_unidade = '".$codUnidade."' ";
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
        $cont = 1;
        $js .= "campo = f.codDpto; \n";
            while (!$conn->eof()) {
                $cod = $conn->pegaCampo("cod_departamento");
                $nom = $conn->pegaCampo("nom_departamento");
                $js .= 'campo.options['.$cont.'] = new Option("'.$nom.'",'.$cod.'); ';
                $conn->vaiProximo();
                $cont++;
            }
        $conn->limpaSelecao();
    }
    break;

//Altera a combo de Setor conforme o Departamento selecionado
case 4:

    $codUnidade = $_REQUEST['codUnidade'];
    $cmbOrgao = $_REQUEST['cmbOrgao'];
    $codDpto = $_REQUEST['codDpto'];

    $js = "";
    $js .= "limpaSelect(f.codSetor,1); \n";
    if ($codDpto != "XXX") {
        $arCodOrgao = explode("/",$cmbOrgao);
        $inCodOrgao = $arCodOrgao[0];
        $stExercicio = $arCodOrgao[1];
        $sql = "Select cod_setor, nom_setor
                From administracao.setor
                Where ano_exercicio = '".$stExercicio."'
                And cod_orgao = '".$inCodOrgao."'
                And cod_unidade = '".$codUnidade."'
                And cod_departamento = '".$codDpto."' ";
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
        $cont = 1;
        $js .= "campo = f.codSetor; \n";
            while (!$conn->eof()) {
                $cod = $conn->pegaCampo("cod_setor");
                $nom = $conn->pegaCampo("nom_setor");
                $js .= 'campo.options['.$cont.'] = new Option("'.$nom.'",'.$cod.'); ';
                $conn->vaiProximo();
                $cont++;
            }
        $conn->limpaSelecao();
    }
    break;

//Busca o nome do usuário de acordo com o cgm fornecido
case 5:

    $numCgm = $_REQUEST['numCgm'];

    $js = "";
    if (strlen($numCgm) > 0) {
        $sql = "Select u.numcgm, u.username, c.nom_cgm
                From administracao.usuario as u, sw_cgm as c
                Where u.numcgm = c.numcgm
                And u.numcgm = '".$numCgm."' ";
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
            if (!$conn->eof()) {
                $nom = AddSlashes($conn->pegaCampo("nom_cgm"));
                $js .= "f.nomCgm.value = '".$nom."' ";
            } else {
                $js .= "erro = true; \n";
                $js .= "f.nomCgm.value = 'USUÁRIO INVÁLIDO' ";
            }
        $conn->limpaSelecao();

    }
    break;
}

executaFrameOculto($js);

 include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
