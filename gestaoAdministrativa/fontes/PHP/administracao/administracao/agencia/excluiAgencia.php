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

$Revision: 3149 $
$Name$
$Author: pablo $
$Date: 2005-11-30 13:54:33 -0200 (Qua, 30 Nov 2005) $

Casos de uso: uc-01.03.97
*/

 include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
 include(CAM_FW_LEGADO."funcoesLegado.lib.php");
 include CAM_FW_LEGADO."auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
 include '../agencia.class.php';
 include CAM_FW_LEGADO."paginacaoLegada.class.php"; //Classe para gerar paginação dos dados

if (isset($excluir)) {
    $controle   = 1;
    $excluir    = explode("-",$excluir);
    $codBanco   = $excluir[0];
    $codAgencia = $excluir[1]."-".$excluir[2];
    $pagina     = $excluir[3];
}

if (!isset($controle)) {
    $controle = 0;
}
if (!isset($pagina)) {
    $pagina = 0;
}
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>
<?php

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
    //$html .= "
    //     <table width='100%'>
    //         <tr>
    //             <td class='alt_dados' width='30%'>Nome do Banco</td>
    //             <td class='alt_dados' width='20%'>Cód. Agência</td>
    //             <td class='alt_dados' width='49%'>Nome da Agência</td>
    //             <td class='alt_dados' width='1%'>&nbsp;</td>
    //         </tr>
    //     ";
        $count = $paginacao->contador();
        while (!$conn->eof()) {
            $codAgencia  = $conn->pegaCampo("cod_agencia");
            $nomAgencia  = $conn->pegaCampo("nom_agencia");
            $codBanco    = $conn->pegaCampo("cod_banco");
            $nomBanco    = $conn->pegaCampo("nom_banco");
            $sNomAgencia = AddSlashes($nomAgencia);
            $conn->vaiProximo();
            $html .= "
                <tr>
                    <td class='labelcenter'>".$count++."</td>
                    <td class='show_dados'>".$codBanco."</td>
                    <td class='show_dados'>".$nomBanco."</td>
                    <td class='show_dados'>".$codAgencia."</td>
                    <td class='show_dados'>".$nomAgencia."</td>
                    <td class='botao'>
                    <a href='#' onClick=\"alertaQuestao('".$PHP_SELF.'?'.Sessao::getId()."','excluir','".$codBanco."-".$codAgencia."-".$pagina."','".$sNomAgencia."','sn_excluir','".Sessao::getId()."');\">
                    <img src='".CAM_FW_IMAGENS."btnexcluir.gif'  border='0'>
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
    $objeto = "Agência ".$codAgencia." - ".$agencia->nomAgencia;
    $pag = $PHP_SELF."?".Sessao::getId()."&pagina=".$pagina;
        if ($agencia->excluirAgencia()) {
            //Insere auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $objeto);
            $audicao->insereAuditoria();
            //Exibe mensagem e retorna para a página padrão
            //alertaAviso($PHP_SELF,$objeto,"excluir","aviso",".Sessao::getId().");
            alertaAviso($pag,$objeto,"excluir","aviso");
        } else {
            alertaAviso($pag,$objeto,"n_excluir","erro");
        }
    break;
}//Fim switch

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
