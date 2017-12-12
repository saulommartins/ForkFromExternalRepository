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
    * Titulo do arquivo : Arquivo da listagem dos orgaos do organograma
    * Data de Criação   : 07/01/2009

    * @author Analista      Gelson
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id:$

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include (CAM_FW_LEGADO."funcoesLegado.lib.php"    );
include (CAM_FW_LEGADO."paginacaoLegada.class.php"); //Classe para gerar paginação dos dados
include (CAM_FW_LEGADO."mascarasLegado.lib.php"   );
include (CAM_FW_LEGADO."botoesPdfLegado.class.php");

?>
<script type="text/javascript">
    function SalvarRelatorio()
    {
        document.frm.action = "organograma.php?<?=Sessao::getId()?>";
        document.frm.submit();
    }
</script>

<form action="organograma.php?<?=Sessao::getId()?>" method="POST" name="frm">

<?php

$pagina = $_REQUEST['pagina'];
if ($_REQUEST['inCodOrganograma']) {
    Sessao::write('inCodOrganogramaTEMP', $_REQUEST['inCodOrganograma']);
}

setAjuda("UC-01.03.94");

$select = "
SELECT
     o.cod_orgao,
     o.num_cgm_pf,
     o.cod_calendar,
     o.cod_norma,
     recuperadescricaoorgao(o.cod_orgao, now()::date) as descricao,
     o.criacao,
     o.inativacao,
     o.sigla_orgao,
     orn.cod_organograma,
     organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao) AS orgao,
     publico.fn_mascarareduzida(organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao)) AS orgao_reduzido,
     publico.fn_nivel(organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao)) AS nivel,
    case when to_char(o.inativacao,'dd/mm/yyyy') is null then 'ativo' else 'inativo' end as situacao
  FROM
     organograma.orgao o,
     organograma.orgao_nivel orn
 WHERE
     o.cod_orgao = orn.cod_orgao
     and orn.cod_organograma = ".Sessao::read('inCodOrganogramaTEMP')."
 GROUP BY
     o.cod_orgao,
     o.num_cgm_pf,
     o.cod_calendar,
     o.cod_norma,
     o.criacao,
     o.inativacao,
     o.sigla_orgao,
     orn.cod_organograma,
     orgao,
     orgao_reduzido,
     nivel
";
$order = " orgao,cod_orgao ";

$sqlPDF = $select." order by ".$order."ASC";

Sessao::write('select',$select);
Sessao::write('order',$order);

$paginacao = new paginacaoLegada;
$paginacao->pegaDados($select,"10");
$paginacao->pegaPagina($pagina);
$paginacao->geraLinks();
$paginacao->pegaOrder($order,"ASC");
$sSQL = $paginacao->geraSQL();

print '
            <table width="100">
                 <tr>
                     <td class="labelcenter" title="Salvar Relatório">
                     <a href="javascript:SalvarRelatorio();"><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a>
                 </tr>
             </table>
             ';

    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();
        $exec = "";
        $exec .= "
            <form action=\"relatorioOrganograma.php?".Sessao::getId()."\" method=\"POST\" name=\"frm\">
            </form>
            <table width=100%>
               <tr>
                  <td class='labelleft' width='5%'>&nbsp;</td>
                  <td class='labelleft'>Código - Descrição</td>
               </tr>";
        $count = $paginacao->contador();
        while (!$dbEmp->eof()) {
                $stCodOrgao = trim($dbEmp->pegaCampo("orgao"));
                $stCodOrgao .= ' - '.trim($dbEmp->pegaCampo("descricao"));
                $dbEmp->vaiProximo();
                $exec .= "
                <tr>
                   <td class='labelcenter'>".$count++."</td>
                   <td class=show_dados>".$stCodOrgao."</td>";
        }
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo "$exec";
        echo "<table width=450 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";

echo "</form>";

    include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
