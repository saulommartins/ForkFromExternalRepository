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
* Arquivo de instância para Empresa
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 19058 $
$Name$
$Author: rodrigo_sr $
$Date: 2007-01-03 08:58:36 -0200 (Qua, 03 Jan 2007) $

* Casos de uso: uc-01.07.89
*/

  include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
  include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
  include_once '../cse.class.php';
  include_once (CAM_FW_LEGADO."paginacaoLegada.class.php"  );
  include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"  ); //Inclui classe para inserir auditoria

if (isset($excluir)) {
    $codEmpresa = $excluir;
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
$sql  = " SELECT ";
$sql .= "   cod_empresa, ";
$sql .= "   nom_empresa, ";
$sql .= "   cnpj ";
$sql .= " FROM ";
$sql .= "   cse.empresa ";
$sql .= " WHERE ";
$sql .= "   cod_empresa > 0 ";
//echo $sql;
//Inicia o relatório em html
$paginacao = new paginacaoLegada;
$paginacao->pegaDados($sql,"15");
$paginacao->pegaPagina($pagina);
$paginacao->geraLinks();
$paginacao->pegaOrder("lower(nom_empresa)","ASC");
$sSQL = $paginacao->geraSQL();

//Pega os dados encontrados em uma query
$conn = new dataBaseLegado;
$conn->abreBD();
$conn->abreSelecao($sSQL);
if ( $pagina > 0 and $conn->eof() ) {
    $pagina--;
    $paginacao->pegaPagina($pagina);
    $paginacao->geraLinks();
    $paginacao->pegaOrder("lower(nom_empresa)","ASC");
    $sSQL = $paginacao->geraSQL();
    $conn->abreSelecao($sSQL);
}
$conn->fechaBD();
$conn->vaiPrimeiro();
?>
<table width='100%'>
    <tr>
        <td colspan="4" class="alt_dados">
            Empresas Cadastradas
        </td>
    </tr>
    <tr>
        <td class="label" width="5%">
            &nbsp;
        </td>
        <td class="labelcenter" width="12%">
            Código
        </td>
        <td class='labelcenter' width='80%'>
            Nome
        </td>
        <td class='label'>
            &nbsp;
        </td>
    </tr>
<?php
    if ( !$conn->eof() ) {
        $iCont = $paginacao->contador();
        while (!$conn->eof()) {
            $cod = $conn->pegaCampo("cod_empresa");
            $nom = $conn->pegaCampo("nom_empresa");
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
        <td class='botao'>
            <?php echo "

        <a href='#' onClick=\"alertaQuestao('".CAM_CSE."cse/empresa/excluiEmpresa.php?".$sessao->id."&stDescQuestao=".urlencode($nom)."','excluir','".$cod."','".$nom."','sn_excluir','$sessao->id');\">
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
            <?php $paginacao->mostraLinks();  ?>
            </font>
        </td>
    </tr>
</table>
<?php
    break;

//Inclusão, alteração ou exclusão de dados
case 1:
    $js = "";
    $ok = true;
    $cse = new cse();

    $nomEmpresa = pegaDado("nom_empresa","cse.empresa","Where cod_empresa = '".$codEmpresa."' ");

    $objeto = urlencode($nomEmpresa);
    if ($cse->excluirEmpresa($codEmpresa) ) {
        //Insere auditoria
        $audicao = new auditoriaLegada;
        $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $objeto);
        $audicao->insereAuditoria();
        //Exibe mensagem e retorna para a página padrão
        alertaAviso($PHP_SELF.'?pagina='.$pagina,$objeto,"excluir","aviso");
    } else {
        exibeAviso($objeto,"n_excluir","erro");
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
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
