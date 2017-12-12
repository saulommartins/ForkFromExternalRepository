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
* Arquivo de instância para ClassificaçãoTratamento
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 3219 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 14:25:34 -0200 (Qui, 01 Dez 2005) $

* Casos de uso: uc-01.07.91
*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
    include_once '../cse.class.php';
    include_once (CAM_FW_LEGADO."paginacaoLegada.class.php");
    include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"); //Inclui classe para inserir auditoria

if (isset($excluir)) {
    $codClassTratamento = $excluir;
    $controle = 1;
}

if (!isset($controle)) {
    $controle = 0;
    $sessao->transf = "";
}
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

switch ($controle) {
case 0:
$sql = "Select cod_classificacao as cod, nom_classificacao as nom
        From cse.classificacao_tratamento Where cod_classificacao > 0 ";
//echo $sql;
//Inicia o relatório em html
$paginacao = new paginacaoLegada;
$conn = new dataBaseLegado;
$paginacao->pegaDados($sql,"10");
$paginacao->pegaPagina($pagina);
$paginacao->geraLinks();
$paginacao->pegaOrder("lower(nom_classificacao)","ASC");
$sSQL = $paginacao->geraSQL();
//Pega os dados encontrados em uma query
$conn->abreBD();
$conn->abreSelecao($sSQL);

if ( $pagina > 0 and $conn->eof() ) {
    $pagina--;
    $paginacao->pegaDados($sql,"15");
    $paginacao->pegaPagina($pagina);
    $paginacao->geraLinks();
    $paginacao->pegaOrder("lower(nom_classificacao)","ASC");
    $sSQL = $paginacao->geraSQL();
    //Pega os dados encontrados em uma query
    $conn->abreBD();
    $conn->abreSelecao($sSQL);
}
$conn->fechaBD();
$conn->vaiPrimeiro();
?>
<table width='100%'>
    <tr>
        <td colspan="4" class="alt_dados">
            Classificações Cadastradas
        </td>
    </tr>
    <tr>
        <td class="label" width="5%">
            &nbsp;
        </td>
        <td class='labelcenter' width='12%'>
            Código
        </td>
        <td class='labelcenter' width='80%'>
            Classificação
        </td>
        <td class='label'>
            &nbsp;
        </td>
    </tr>
<?php
    if ( !$conn->eof() ) {
        $iCont=$paginacao->contador();
        while (!$conn->eof()) {
            $cod = $conn->pegaCampo("cod");
            $nom = $conn->pegaCampo("nom");
            $conn->vaiProximo();
?>
    <tr>
        <td class="label">
            <?=$iCont++;?>
        </td>
        <td class='show_dados_right'>
            <?=$cod;?>
        </td>
        <td class='show_dados'>
            <?=$nom;?>
        </td>
        <td class='botao' width='10%'>

            <?php echo "

        <a href='#' onClick=\"alertaQuestao('".CAM_CSE."cse/classTratamento/excluiClassTratamento.php?".$sessao->id."&stDescQuestao=".$nom."','excluir','".$cod."','".$nom."','sn_excluir','$sessao->id');\">
                                    <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border='0'></a>";?>

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
            <?php $paginacao->mostraLinks();?>
            </font>
        </td>
    </tr>
</table>
<?php
    break;

//Formulário em HTML para entrada de dados

//Inclusão, alteração ou exclusão de dados
case 1:
    $js = "";
    $ok = true;
    $obj = new cse();

    $nomClassTratamento = pegaDado("nom_classificacao","cse.classificacao_tratamento","Where cod_classificacao = '".$codClassTratamento."' ");

    $objeto = "Classe tratamento = ".$codClassTratamento;
    if ($obj->excluirClassTratamento($codClassTratamento) ) {
        //Insere auditoria
        $audicao = new auditoriaLegada;
        $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $objeto);
        $audicao->insereAuditoria();
        //Exibe mensagem e retorna para a página padrão
        sistemaLegado::alertaAviso($PHP_SELF."?pagina=".$pagina,$nomClassTratamento,"excluir","aviso");
    } else {
        sistemaLegado::exibeAviso($objeto,"n_excluir","erro");
        $js .= "f.ok.disabled = false; \n";
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

    if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>');
}
</script>
</head>

<body onLoad="javascript:executa();">

</body>
</html>
<?php
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
