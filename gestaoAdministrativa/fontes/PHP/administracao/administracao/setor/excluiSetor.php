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
* Manutneção de setores
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 18677 $
$Name$
$Author: cassiano $
$Date: 2006-12-11 14:28:56 -0200 (Seg, 11 Dez 2006) $

Casos de uso: uc-01.03.97
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include (CAM_FW_LEGADO."mascarasLegado.lib.php");
include (CAM_FW_LEGADO."funcoesLegado.lib.php");
include (CAM_FW_LEGADO."auditoriaLegada.class.php");
include (CAM_FW_LEGADO."configuracaoLegado.class.php");
include (CAM_FW_LEGADO."paginacaoLegada.class.php");

$stMascaraSetor = pegaConfiguracao("mascara_setor");

$excluir = $_REQUEST['excluir'];
$controle = $_REQUEST['controle'];
$pagina = $_REQUEST['pagina'];

if (isset($excluir)) {
    $controle        = 1;
    $excluir         = explode("-",$excluir);
    $codOrgao        = $excluir[0];
    $codUnidade      = $excluir[1];
    $anoE            = $excluir[2];
    $codDepartamento = $excluir[3];
    $codSetor        = $excluir[4];
    $pagina          = $excluir[5];
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
        $sSQLs = "SELECT s.cod_setor, s.nom_setor, d.cod_departamento, d.nom_departamento,
                    d.cod_orgao, d.cod_unidade, u.nom_unidade, d.ano_exercicio, o.nom_orgao
                    FROM administracao.setor as s, administracao.departamento as d, administracao.unidade as u, administracao.orgao as o
                    WHERE s.cod_departamento = d.cod_departamento
                    and s.cod_unidade = d.cod_unidade
                    and s.cod_orgao = d.cod_orgao
                    and s.ano_exercicio = d.ano_exercicio
                    and s.ano_exercicio = u.ano_exercicio
                    and s.ano_exercicio = o.ano_exercicio
                    and d.cod_unidade = u.cod_unidade
                    and d.cod_orgao = u.cod_orgao
                    and u.cod_orgao = o.cod_orgao
                    and s.cod_setor > 0";

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sSQLs,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(nom_orgao), lower(nom_unidade), lower(nom_departamento), lower(nom_setor)","ASC");
        $sSQL = $paginacao->geraSQL();
        //print $sSQL;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        if ($dbEmp->numeroDeLinhas==0 && $pagina != 0) {
            mudaTelaPrincipal($_SERVER['PHP_SELF']);
        }
        $exec = "";
        $exec .= "<table width=100%>
               <tr>
                   <td class='alt_dados' colspan='8'>Registros de setor</td>
               </tr>
               <tr>
                  <td class='labelleft' width='5%'>&nbsp;</td>
                  <td class='labelleft'>Código</td>
                  <td class='labelleft'>Órgão</td>
                  <td class='labelleft'>Unidade</td>
                  <td class='labelleft'>Departamento</td>
                  <td class='labelleft'>Setor</td>
                  <td class='labelleft''>&nbsp;</td>
               </tr>";
            $count = $paginacao->contador();
            while (!$dbEmp->eof()) {
                $codSetor  = trim($dbEmp->pegaCampo("cod_setor"));
                $nomSetor  = trim($dbEmp->pegaCampo("nom_setor"));
                $codDepartamento  = trim($dbEmp->pegaCampo("cod_departamento"));
                $nomDepartamento  = trim($dbEmp->pegaCampo("nom_departamento"));
                $codUnidade  = trim($dbEmp->pegaCampo("cod_unidade"));
                $codOrgao  = trim($dbEmp->pegaCampo("cod_orgao"));
                $nomUnidade  = trim($dbEmp->pegaCampo("nom_unidade"));
                $anoE  = trim($dbEmp->pegaCampo("ano_exercicio"));
                $nomOrgao  = trim($dbEmp->pegaCampo("nom_orgao"));
                $exclusao = $codOrgao."-".$codUnidade."-".$anoE."-".$codDepartamento."-".$codSetor."-".$pagina;
                $dbEmp->vaiProximo();
                $stCodSetor = $codOrgao.".".$codUnidade.".".$codDepartamento.".".$codSetor."/".$anoE;
                $arCodSetor = validaMascara($stMascaraSetor,$stCodSetor);
                if ($arCodSetor[0]) {
                    $stCodSetor = $arCodSetor[1];
                }
                $exec .= "
                <tr>
                    <td class='labelcenter'>".$count++."</td>
                    <td class=show_dados>".$stCodSetor."</td>
                    <td class=show_dados>".$nomOrgao."</td>
                    <td class=show_dados>".$nomUnidade."</td>
                    <td class=show_dados>".$nomDepartamento."</td>
                    <td class=show_dados>".$nomSetor."</td>
                    <td class='botao'>
                    <a href='' onClick=\"alertaQuestao('".$_SERVER['PHP_SELF']."?".Sessao::getId()."','excluir','".$exclusao."','".$nomSetor."','sn_excluir','".Sessao::getId()."');\">
                    <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border=0></a></td>
                </tr>\n";
        }
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo "$exec";
        echo "<table width=450 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td>
        </table>";

break;
case 1:
    $stCodSetor = $codOrgao.".".$codUnidade.".".$codDepartamento.".".$codSetor."/".$anoE;
    $arCodSetor = validaMascara($stMascaraSetor,$stCodSetor);
    if ($arCodSetor[0]) {
        $stCodSetor = $arCodSetor[1];
    }
    $nomSetor = pegaDado("nom_setor","administracao.setor","where cod_orgao='".$codOrgao."' and cod_unidade='".$codUnidade."' and cod_departamento='".$codDepartamento."' and cod_setor='".$codSetor."' and ano_exercicio = '".$anoE."'");
    $mensagem = $stCodSetor." - ".$nomSetor;

    $configuracao = new configuracaoLegado;
    $configuracao->setaValorSetor($codSetor,$codDepartamento,$codUnidade,$codOrgao,"",$anoE);
    $pag = $PHP_SELF."?".Sessao::getId()."&pagina=".$pagina;
    if ($configuracao->deleteSetor()) {
        $audicao = new auditoriaLegada;
        $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $codDepartamento);
        $audicao->insereAuditoria();
        alertaAviso($pag,$mensagem,"excluir","aviso");
    } else {
        if ( strpos($configuracao->stErro, "fk_") ) {
            $mensagem = "O Setor $mensagem não pode ser excluído porque está sendo utilizado";
        }
        alertaAviso($pag,$mensagem,"n_excluir","erro");
    }

}
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
