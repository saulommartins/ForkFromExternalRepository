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
* Manutenção de departamento
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 18678 $
$Name$
$Author: cassiano $
$Date: 2006-12-11 14:29:53 -0200 (Seg, 11 Dez 2006) $

Casos de uso: uc-01.03.97
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include (CAM_FW_LEGADO."mascarasLegado.lib.php");
include (CAM_FW_LEGADO."funcoesLegado.lib.php");
include (CAM_FW_LEGADO."auditoriaLegada.class.php");
include (CAM_FW_LEGADO."configuracaoLegado.class.php");
include (CAM_FW_LEGADO."paginacaoLegada.class.php");

$stMascaraSetor = pegaConfiguracao("mascara_setor");
$arMascaraSetor = explode(".",$stMascaraSetor);
$stMascaraOrgao = $arMascaraSetor[0]."/9999";
$stMascaraUnidade = $arMascaraSetor[1]."/9999";
$stMascaraDpto = $arMascaraSetor[0].".".$arMascaraSetor[1].".".$arMascaraSetor[2]."/9999";

$controle = $_REQUEST['controle'];
$excluir = $_REQUEST['excluir'];
$pagina = $_REQUEST['pagina'];

if (isset($excluir)) {
    $controle        = 1;
    $excluir         = explode("-",$excluir);
    $codOrgao        = $excluir[0];
    $codUnidade      = $excluir[1];
    $anoE            = $excluir[2];
    $codDepartamento = $excluir[3];
    $pagina          = $excluir[4];
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
        $sSQLs = "SELECT d.cod_departamento, d.nom_departamento, d.ano_exercicio, u.cod_unidade, u.nom_unidade, u.cod_orgao,
        o.nom_orgao FROM administracao.departamento as d, administracao.unidade as u, administracao.orgao as o
        WHERE u.cod_orgao = d.cod_orgao
        AND o.cod_orgao = d.cod_orgao
        and u.ano_exercicio = d.ano_exercicio
        and o.ano_exercicio = d.ano_exercicio
        AND u.cod_unidade = d.cod_unidade
        and d.cod_departamento > 0";

        Sessao::write('sSQLs',$sSQLs);
        $paginacao = new paginacaoLegada;

        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(nom_orgao), lower(nom_unidade), lower(nom_departamento)","ASC");
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
        <table width='100%'>
        <tr>
             <td class='alt_dados' colspan='7'>Registros de Departamento</td>
        </tr>
        <tr>
            <td class='labelleft' width='5%'>&nbsp;</td>
            <td class='labelleft' width='10%' >Código</td>
            <td class='labelleft' width='30%'>Órgão</td>
            <td class='labelleft' width='30%'>Unidade</td>
            <td class='labelleft' width='24%' >Departamento</td>
            <td class='labelleft' width='1%'>&nbsp;</td>
        </tr>";
        $count = $paginacao->contador();
        while (!$dbEmp->eof()) {
                $codDepartamento  = trim($dbEmp->pegaCampo("cod_departamento"));
                $codOrgao  = trim($dbEmp->pegaCampo("cod_orgao"));
                $nomUnidade  = trim($dbEmp->pegaCampo("nom_unidade"));
                $nomOrgao  = trim($dbEmp->pegaCampo("nom_orgao"));
                $codUnidade  = trim($dbEmp->pegaCampo("cod_unidade"));
                $nomDepartamento  = trim($dbEmp->pegaCampo("nom_departamento"));
                $anoE  = trim($dbEmp->pegaCampo("ano_exercicio"));
                $exclusao = $codOrgao."-".$codUnidade."-".$anoE."-".$codDepartamento."-".$pagina;
                $dbEmp->vaiProximo();
                $nomDepartamento = AddSlashes($nomDepartamento);
                $stCodDpto = $codOrgao.".".$codUnidade.".".$codDepartamento."/".$anoE;
                $arCodDpto = validaMascara($stMascaraDpto,$stCodDpto);
                if ($arCodDpto[0]) {
                    $stCodDpto = $arCodDpto[1];
                }
                $exec .= "
                <tr>
                <td class='labelcenter'>".$count++."</td>
                <td class=show_dados>".$stCodDpto."</td>
                <td class=show_dados>".$nomOrgao."</td>
                <td class=show_dados>".$nomUnidade."</td>
                <td class=show_dados>".$nomDepartamento."</td>
                <!-- class=show_dados>".$anoE."</td>-->
                <td class='botao'>
                <a
                   href='#' onClick=\"alertaQuestao('".$_SERVER['PHP_SELF']."?".Sessao::getId()."','excluir','".$exclusao."','".$nomDepartamento."','sn_excluir','".Sessao::getId()."');\">
                   <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border=0>
                </a></td>
                </tr>\n";
        }
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo "$exec";
        echo "
        <table width=450 align=center>
        <tr>
        <td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font>
        </tr></td>
        </table>";
break;

case 1:

    $configuracao = new configuracaoLegado;
    $configuracao->setaValorDepartamento($codDepartamento,$codUnidade,$codOrgao,"",$anoE);
    $nomDepartamento = pegaDado("nom_departamento","administracao.departamento","where cod_orgao = '".$codOrgao."' and cod_unidade = '".$codUnidade."' and cod_departamento = '".$codDepartamento."' and ano_exercicio = '".$anoE."'");
    $pag = $_SERVER['PHP_SELF']."?".Sessao::getId()."&pagina=".$pagina;
    $stCodDpto = $codOrgao.".".$codUnidade.".".$codDepartamento."/".$anoE;
    $arCodDpto = validaMascara($stMascaraDpto,$stCodDpto);
    if ($arCodDpto[0]) {
        $stCodDpto = $arCodDpto[1];
    }
    $mensagem = $stCodDpto." - ".$nomDepartamento;

    if ($configuracao->deleteDepartamento()) {
        /// Insere Auditoria
        $audicao = new auditoriaLegada;
        $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $codDepartamento);
        $audicao->insereAuditoria();
        alertaAviso($pag,$mensagem,"excluir","aviso");
    } else {
        if ( strpos($configuracao->stErro, "fk_") ) {
            $mensagem = "O Departamento $mensagem não pode ser excluído porque está sendo utilizado";
        }
        alertaAviso($pag,$mensagem,"n_excluir","erro");
    }

}
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
