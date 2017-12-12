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

    $Id: relatorioAuditoriaMostra.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAuditoria.class.php");

include(CAM_FW_LEGADO."funcoesLegado.lib.php");
include CAM_FW_LEGADO."paginacaoLegada.class.php";
include CAM_FW_LEGADO."botoesPdfLegado.class.php";

$moduloCod = $_REQUEST['moduloCod'];
$numCgm    = $_REQUEST['numCgm'];
$nomCgm    = $_REQUEST['nomCgm'];
$sDataIni  = $_REQUEST['sDataIni'];
$sDataFim  = $_REQUEST['sDataFim'];
$orderby   = $_REQUEST['orderby'];
$pagina    = $_REQUEST['pagina'];

?>
<script type="text/javascript">
    function Salvar()
    {
        document.frm.action = "auditoria.php?<?=Sessao::getId()?>&moduloCod=<?=$moduloCod?>&numCgm=<?=$numCgm?>&nomCgm=<?=$nomCgm?>&sDataIni=<?=$sDataIni?>&sDataFim=<?=$sDataFim?>&orderby=<?=$orderby?>";
        document.frm.submit();
    }
</script>

<form action="auditoria.php?<?=Sessao::getId()?>" method="POST" name="frm">

<?php

if (isset($moduloCod)) {
   while ( list( $key, $val ) = each( $_REQUEST ) ) {
   $aVarWhere[$key] = $val;
}

// Sessao::write('aWhere', $aVarWhere);
// $sSQLs = "SELECT  u.username,
// 				  a.nom_acao,
// 				  to_char(au.timestamp, 'dd/mm/yyyy - hh:mm:ss') as timestamp,
// 				  au.objeto,
// 				  m.nom_modulo,
// 				  f.nom_funcionalidade,
//                   au_d.valores
//             FROM  administracao.auditoria as au,
//                administracao.usuario as u,
//                administracao.acao as a,
//                administracao.funcionalidade as f,
//                administracao.modulo as m,
//                   administracao.auditoria_detalhe as au_d
//            WHERE  au.numcgm = u.numcgm
//              AND  au.cod_acao = a.cod_acao
//              AND  a.cod_funcionalidade = f.cod_funcionalidade
//           AND  f.cod_modulo = m.cod_modulo
//              AND  au_d.numcgm = au.numcgm AND au_d.cod_acao = au.cod_acao AND au_d.timestamp = au.timestamp";
// $sSQLs .= (!empty($moduloCod) && strtolower($moduloCod) != "xxx") ? " AND m.cod_modulo = ".$moduloCod : "";
// $sSQLs .= (!empty($numCgm)    && strtolower($numCgm)    != "xxx") ? " AND u.numcgm     = ".$numCgm    : "";
// $sSQLs .= (!empty($sDataIni)  && strtolower($sDataIni)  != "xxx") ? " AND au.timestamp > '".dataToSql($sDataIni)."'" : "";
// $sSQLs .= (!empty($sDataFim)  && strtolower($sDataFim)  != "xxx") ? " AND au.timestamp <= '".dataToSql($sDataFim)." 23:59:59.999'" : "";
// Sessao::write('sSQLs'  , $sSQLs  );
Sessao::write('orderby', $orderby);
// echo $sSQLs.' ORDER BY '.$orderby;

// $paginacao = new paginacaoLegada;
// $paginacao->complemento = "&moduloCod=".$moduloCod."&numCgm=".$numCgm."&sDataIni=".$sDataIni."&sDataFim=".$sDataFim."&orderby=".$orderby;
// $paginacao->pegaDados(Sessao::read('sSQLs'),"15");
// $paginacao->pegaPagina($pagina);
// $paginacao->geraLinks();
// $paginacao->pegaOrder(Sessao::read('orderby'),"DESC");
// $sSQL = $paginacao->geraSQL();

// $dbEmp = new dataBaseLegado;
// $dbEmp->abreBD();
// $dbEmp->abreSelecao($sSQL);
// $dbEmp->vaiPrimeiro();
}

//FILTRO
$arFiltro = array();
if (!empty($moduloCod) && strtolower($moduloCod) != "xxx") {
    $arFiltro[] = "m.cod_modulo = $moduloCod";
}
if (!empty($numCgm) && strtolower($numCgm) != "xxx") {
    $arFiltro[] = "u.numcgm = $numCgm";
}
if (!empty($sDataIni) && strtolower($sDataIni) != "xxx") {
    $arFiltro[] = "au.timestamp > '".dataToSql($sDataIni)."'";
}
if (!empty($sDataFim) && strtolower($sDataFim) != "xxx") {
    $arFiltro[] = "au.timestamp <= '".dataToSql($sDataFim)." 23:59:59.999'";
}

$stFiltro = implode(" AND ", $arFiltro);

//CONSULTA
$rsRecordSet = null;
$obTAuditoria = new TAuditoria();
$obErro = $obTAuditoria->recuperaAuditoria($rsRecordSet, $stFiltro, $orderby, false);

if (!$obErro->ocorreu()) {
    $obLista = new TableTree();
    $obLista->setRecordset($rsRecordSet);
    $obLista->setParametros(array("numcgm","cod_acao", "timestamp"));
    $obLista->setArquivo("DTRelatorioAuditoriaDetalhes.php");

    $obLista->Head->addCabecalho("Usuário"        ,10);
    $obLista->Head->addCabecalho("Módulo"         ,15);
    $obLista->Head->addCabecalho("Funcionalidade" ,20);
    $obLista->Head->addCabecalho("Ação"           ,20);
    $obLista->Head->addCabecalho("Objeto"         ,50);
    $obLista->Head->addCabecalho("Data / Hora"    ,20);

    $obLista->Body->addCampo('[username]');
    $obLista->Body->addCampo('[nom_modulo]');
    $obLista->Body->addCampo('[nom_funcionalidade]');
    $obLista->Body->addCampo('[nom_acao]');
    $obLista->Body->addCampo('[objeto]');
    $obLista->Body->addCampo('[timestamp]');

    $obLista->montaHTML(false, false);
    $stHtml = $obLista->getHtml();
}

    $botoesPDF = new botoesPdfLegado;

    // if($dbEmp->numeroDeLinhas==0)
    //     exit("<br><b>Nenhum registro encontrado!</b>");

    // $exec = "";
    // $exec .= "<table width=95%><tr><td class=alt_dados>Usuário</td><td class=alt_dados>Módulo</td><td class=alt_dados>Funcionalidade</td><td class=alt_dados>Ação</td><td class=alt_dados>Objeto</td><td class=alt_dados>Data / Hora</td></tr>";
    // while (!$dbEmp->eof()) {
    //         $username  = trim($dbEmp->pegaCampo("username"));
    //         $nom_acao  = trim($dbEmp->pegaCampo("nom_acao"));
    //         $timestamp  = trim($dbEmp->pegaCampo("timestamp"));
    //         $objeto  = trim($dbEmp->pegaCampo("objeto"));
    //         $nom_modulo  = trim($dbEmp->pegaCampo("nom_modulo"));
    //         $nom_funcionalidade  = trim($dbEmp->pegaCampo("nom_funcionalidade"));
    //         $dbEmp->vaiProximo();
    //         $exec .= "<tr>
    //                         <td class=show_dados>".$username."</td>
    //                         <td class=show_dados>".$nom_modulo."</td>
    //                         <td class=show_dados>".$nom_funcionalidade."</td>
    //                         <td class=show_dados>".$nom_acao."</td>
    //                         <td class=show_dados>".$objeto."</td>
    //                         <td class=show_dados>".$timestamp."</td>
    //                 </tr>\n";
    // }
    // $exec .= "</table>";
    // $dbEmp->limpaSelecao();
    // $dbEmp->fechaBD();
    // $sqlPDF = Sessao::read('sSQLs');
    // $sqlPDF .= " order by " . Sessao::read('orderby')." DESC";

    print '
       <table width="100">
            <tr>
                <td class="labelcenter" title="Salvar Relatório">
                <a href="javascript:Salvar();"><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a>
            </tr>
        </table>
        ';

    // echo "$exec";
    echo $stHtml;
    echo "<table width=450 align=center><tr><td align=center><font size=2>";
    // $paginacao->mostraLinks();
    echo "</font></tr></td></table>";
    echo "</form>";

    include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
