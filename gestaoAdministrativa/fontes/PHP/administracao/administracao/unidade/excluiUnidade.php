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
* Manutneção do unidade
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 18685 $
$Name$
$Author: cassiano $
$Date: 2006-12-11 15:56:32 -0200 (Seg, 11 Dez 2006) $

Casos de uso: uc-01.03.97
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include (CAM_FW_LEGADO."mascarasLegado.lib.php");
include (CAM_FW_LEGADO."funcoesLegado.lib.php");
include (CAM_FW_LEGADO."auditoriaLegada.class.php");
include (CAM_FW_LEGADO."configuracaoLegado.class.php");
include (CAM_FW_LEGADO."paginacaoLegada.class.php");

$controle = $_REQUEST['controle'];
$excluir = $_REQUEST['excluir'];
$pagina = $_REQUEST['pagina'];

$stMascaraSetor = pegaConfiguracao("mascara_setor");
$arMascaraSetor = explode(".",$stMascaraSetor);
$stMascaraOrgao = $arMascaraSetor[0]."/9999";
$stMascaraUnidade = $arMascaraSetor[0].".".$arMascaraSetor[1]."/9999";

if (isset($excluir)) {
    $controle = 1;
    $excluir  = explode("-",$excluir);
    $codOrgao = $excluir[0];
    $codUnidade = $excluir[1];
    $anoE = $excluir[2];
    $pagina = $excluir[3];
}

if (!isset($controle)) {
    $controle = 0;
    $codOrgao = "";
    $codUnidade = "";
    $anoE = "";
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
        $sSQLs = "SELECT u.cod_unidade, u.cod_orgao, u.nom_unidade, u.ano_exercicio, o.nom_orgao
                  FROM administracao.unidade as u, administracao.orgao as o
                  WHERE o.cod_orgao = u.cod_orgao
                  AND o.ano_exercicio = u.ano_exercicio
                  AND u.cod_unidade > 0";
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sSQLs,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(nom_orgao), lower(nom_unidade)","ASC");
        $sSQL = $paginacao->geraSQL();

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        if ($dbEmp->numeroDeLinhas==0 && $pagina != 0) {
            mudaTelaPrincipal($_SERVER['PHP_SELF']);
        }
        if ($dbEmp->numeroDeLinhas==0) {
           exit("<br><b>Nenhum registro encontrado!</b>");
        }
        $exec = "";
        $exec .= "
        <table width=100%>
        <tr>
             <td class='alt_dados' colspan='7'>Registros de Unidade</td>
        </tr>
        <tr>
        <td class='labelleft' width='5%'>&nbsp;</td>
        <td class='labelleft'>Código</td>
        <td class='labelleft'>Órgão</td>
        <td class='labelleft'>Unidade</td>
        <td class='labelleft' width='1%'>&nbsp;</td>
        </tr>";
        $count = $paginacao->contador();
        while (!$dbEmp->eof()) {
                $codOrgao    = trim($dbEmp->pegaCampo("cod_orgao"));
                $nomOrgao    = trim($dbEmp->pegaCampo("nom_orgao"));
                $codUnidade  = trim($dbEmp->pegaCampo("cod_unidade"));
                $nomUnidade  = trim($dbEmp->pegaCampo("nom_unidade"));
                $anoE        = trim($dbEmp->pegaCampo("ano_exercicio"));
                $exclusao    = $codOrgao."-".$codUnidade."-".$anoE."-".$pagina;
                $dbEmp->vaiProximo();
                $stCodUnidade = $codOrgao.".".$codUnidade."/".$anoE;
                $arCodUnidade = validaMascara($stMascaraUnidade,$stCodUnidade);
                if ($arCodUnidade[0]) {
                    $stCodUnidade = $arCodUnidade[1];
                }
                $exec .= "<tr>
                <td class='labelcenter'>".$count++."</td>
                <td class=show_dados>".$stCodUnidade."</td>
                <td class=show_dados>".$nomOrgao."</td>
                <td class=show_dados>".$nomUnidade."</td>
                <!--class=show_dados>".$anoE."</td>-->
                <td class=botao width=20>
                <a href='#' onClick=\"alertaQuestao('".$_SERVER['PHP_SELF']."?".Sessao::getId()."','excluir','".$exclusao."','".$nomUnidade."','sn_excluir','".Sessao::getId()."');\">
                <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border=0></a></td>
                </tr>\n";
        }
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo "$exec";
        ?>
        <table width=450 align=center><tr><td align=center><font size=2>
        <?php $paginacao->mostraLinks();  ?>
        </font></tr></td></table>
        <?php
    break;

    case 1:

        $nomUnidade = pegaDado("nom_unidade","administracao.unidade","where cod_orgao = '".$codOrgao."' and cod_unidade = '".$codUnidade."' and ano_exercicio = '".$anoE."'");
        $unidade = new configuracaoLegado();
        $unidade->setaValorUnidade($codUnidade,$codOrgao,"",$anoE,"");
        $stCodUnidade = $codOrgao.".".$codUnidade."/".$anoE;
        $arCodUnidade = validaMascara($stMascaraUnidade,$stCodUnidade);
        if ($arCodUnidade[0]) {
            $stCodUnidade = $arCodUnidade[1];
        }
        $objeto = $stCodUnidade." - ".$nomUnidade;
        $pag = $_SERVER['PHP_SELF']."?".Sessao::getId()."&pagina=".$pagina;
        if ($unidade->deleteUnidade()) {
            //Insere auditoria
             $audicao = new auditoriaLegada;
             $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $objeto);
             $audicao->insereAuditoria();
             //Exibe mensagem e retorna para a página padrão
             alertaAviso($pag,$objeto,"excluir","aviso");
        } else {
            if ( strpos($unidade->stErro, "fk_") ) {
                $objeto = "A Unidade $objeto não pode ser excluída porque está sendo utilizada";
            }
            alertaAviso($pag,$objeto,"n_excluir","erro");
        }
} // Fim Switch

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
