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
* Manutenção de banco
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3551 $
$Name$
$Author: lizandro $
$Date: 2005-12-07 11:23:21 -0200 (Qua, 07 Dez 2005) $

Casos de uso: uc-01.03.97
*/

 include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
 include(CAM_FW_LEGADO."funcoesLegado.lib.php");
 include CAM_FW_LEGADO."auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
 include CAM_FW_LEGADO."paginacaoLegada.class.php"; //Classe para gerar paginação dos dados
 include '../banco.class.php';

if (isset($excluir)) {
    $controle = 1;
    $excluir  = explode("-",$excluir);
    $codBanco = $excluir[0];
    $pagina   = $excluir[1];

}

if (!isset($controle)) {
    $controle = 0;
    $codBanco = "";
    $nomBanco = "";
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
$sql = "Select cod_banco, nom_banco
        From administracao.banco";

//Inicia o relatório em html
$paginacao = new paginacaoLegada;
$paginacao->pegaDados($sql,"10");
$paginacao->pegaPagina($pagina);
$paginacao->geraLinks();
$paginacao->pegaOrder("lower(nom_banco)","ASC");
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
                <td class='alt_dados' colspan='6'>Registros de Banco</td>
            </tr>
            <tr>
                <td class='labelleft' width='5%'>&nbsp;</td>
                <td class='labelleft' width='10%'>Código</td>
                <td class='labelleft' width='84%'>Nome</td>
                <td class='labelleft' width='1%'>&nbsp;</td>
            </tr>
        ";
        $count = $paginacao->contador();
        while (!$conn->eof()) {
            $codBanco = $conn->pegaCampo("cod_banco");
            $nomBanco = $conn->pegaCampo("nom_banco");
            $sNomBanco = AddSlashes($nomBanco);
            $conn->vaiProximo();
            $html .= "
                <tr>
                    <td class='labelcenter'>".$count++."</td>
                    <td class='show_dados'>".$codBanco."</td>
                    <td class='show_dados'>".$nomBanco."</td>
                    <td class='botao'>
                    <a href='#' onClick=\"alertaQuestao('$PHP_SELF?".Sessao::getId()."','excluir','".$codBanco."-".$pagina."','".$sNomBanco."','sn_excluir','".Sessao::getId()."');\">
                    <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border='0'>
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
    $nomBanco = pegaDado("nom_banco","administracao.banco","Where cod_banco = '".$codBanco."'");
    $objeto = "Banco ".$codBanco." - ".$nomBanco;
    $banco = new banco($codBanco);
    $pag = $PHP_SELF."?".Sessao::getId()."&pagina=".$pagina;
    if ($banco->excluirBanco()) {
        //Insere auditoria
        $audicao = new auditoriaLegada;
        $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $objeto);
        $audicao->insereAuditoria();
        //Exibe mensagem e retorna para a página padrão
        alertaAviso($pag,$objeto,"excluir","aviso","");
    } else {
        alertaAviso($pag,$objeto,"n_excluir","erro","");
    }
    break;
}//Fim switch

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
