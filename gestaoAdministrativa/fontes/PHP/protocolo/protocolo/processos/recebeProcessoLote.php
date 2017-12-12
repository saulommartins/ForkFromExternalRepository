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
  * Arquivo de implementação de manutenção de processo
  * Data de Criação: 25/07/2005

  * @author Analista: Cassiano
  * @author Desenvolvedor: Cassiano

  Casos de uso: uc-01.06.98

  $Id: recebeProcessoLote.php 66029 2016-07-08 20:55:48Z carlos.silva $

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_FW_LEGADO."processosLegado.class.php"; //Insere a classe que manipula os dados do processo
include CAM_FW_LEGADO."auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
include 'interfaceProcessos.class.php'; //Inclui classe que contém a interface html
include CAM_FW_LEGADO."mascarasLegado.lib.php";
include CAM_FW_LEGADO."funcoesLegado.lib.php" ;
setAjuda('uc-01.06.98');

$pagina           = $_REQUEST['pagina'];
$codProcesso      = $_REQUEST['codProcesso'];
$numCgm           = $_REQUEST['numCgm'];
$stChaveProcesso  = $_REQUEST['stChaveProcesso'];
$dataInicio       = $_REQUEST['dataInicio'];
$dataInicial      = $dataInicio;
$dataTermino      = $_REQUEST['dataTermino'];
$dataFinal	 	  = $dataTermino;
$resumo           = $_REQUEST['resumo'];
$codClassificacao = $_REQUEST['codClassificacao'];
$codAssunto       = $_REQUEST['codAssunto'];
$ctrl			  = $_REQUEST['ctrl'];
$mascaraProcesso  = pegaConfiguracao("mascara_processo", 5);

if (!isset($_REQUEST["controle"])) {
        $controle = 0;
} else {
    $controle = $_REQUEST["controle"];
}

$ctrl = $_REQUEST["ctrl"];
if ($ctrl == 2) {
    $controle = 1;
}

?>
<script type="text/javascript">
    function zebra(id, classe)
    {
       var tabela = document.getElementById(id);
        var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
            ((i%2) == 0) ? linhas[i].className = classe : void(0);
        }
    }
    function Salvar()
    {
        document.frm.action = "recebeProcessoLote.php?<?=Sessao::getId()?>&controle=1&codProcesso=<?=$codProcesso;?>&numCgm=<?=$numCgm;?>&stChaveProcesso=<?=$stChaveProcesso;?>&codAssunto=<?=$codAssunto;?>&codClassificacao=<?=$codClassificacao;?>&resumo=<?=$resumo;?>&dataInicio=<?=$dataInicio;?>&dataTermino=<?=$dataTermino;?>";
        document.frm.submit();
    }

    function receberProcessos()
    {
        document.frm.action = "recebeProcessoLote.php?<?=Sessao::getId()?>&controle=2&codProcesso=<?=$codProcesso;?>&numCgm=<?=$numCgm;?>&stChaveProcesso=<?=$stChaveProcesso;?>&codAssunto=<?=$codAssunto;?>&codClassificacao=<?=$codClassificacao;?>&resumo=<?=$resumo;?>&dataInicio=<?=$dataInicio;?>&dataTermino=<?=$dataTermino;?>";
        document.frm.submit();
    }

    function enviaProcesso(pagina)
    {
        document.frm.action += "&" + pagina + "&controle=1&codProcesso=<?=$codProcesso;?>&numCgm=<?=$numCgm;?>&stChaveProcesso=<?=$stChaveProcesso;?>&codAssunto=<?=$codAssunto;?>&codClassificacao=<?=$codClassificacao;?>&resumo=<?=$resumo;?>&dataInicio=<?=$dataInicio;?>&dataTermino=<?=$dataTermino;?>";
        document.frm.submit();
    }

    function marcarTodos(componente)
    {
        var i = 0;
        for (i = 0; i < document.frm.elements.length; i++) {
            if (document.frm.elements[i].type == "checkbox") {
                document.frm.elements[i].checked = componente.checked;
            }
        }
        if (componente.checked) {
            document.getElementById('marcador').innerHTML = "Desmarcar todos";
        } else {
            document.getElementById('marcador').innerHTML = "Marcar todos";
        }
    }
</script>

<?php

switch ($controle) {
case 0:
    include(CAM_FW_LEGADO."filtrosProcessoLegado.inc.php");
break;
case 1:
    $vet = Sessao::read('arVet');

    if (is_array($vet)) {
        foreach ($vet AS $indice => $valor) {
            $$indice = $valor;
        }
    }

    $inCodOrganograma = SistemaLegado::pegaDado('cod_organograma', 'organograma.vw_orgao_nivel', ' WHERE cod_orgao = '.Sessao::read('codOrgao'));
    $boPermissaoHierarquica = SistemaLegado::pegaDado('permissao_hierarquica', 'organograma.organograma', ' WHERE cod_organograma = '.$inCodOrganograma);

    $sql  = "        SELECT                                                                      \n";
    $sql .= "            DISTINCT sw_assunto.nom_assunto                                         \n";
    $sql .= "            , sw_assunto.cod_assunto                                                \n";
    $sql .= "            ,  array_to_string(array_agg(nom_cgm), ', ')as nom_cgm                  \n";
    $sql .= "            , sw_classificacao.nom_classificacao                                    \n";
    $sql .= "            , sw_classificacao.cod_classificacao                                    \n";
    $sql .= "            , sw_processo.ano_exercicio                                             \n";
    $sql .= "            , sw_processo.cod_processo                                              \n";
    $sql .= "            , sw_processo.timestamp                                                 \n";
    $sql .= "            , sw_ultimo_andamento.cod_andamento                                     \n";
    $sql .= "            , ( EXISTS ( SELECT                                                     \n";
    $sql .= "                             1                                                      \n";
    $sql .= "                         FROM                                                       \n";
    $sql .= "                             SW_DESPACHO                                            \n";
    $sql .= "                         WHERE                                                      \n";
    $sql .= "                             COD_PROCESSO = sw_processo.cod_processo                \n";
    $sql .= "                             AND ANO_EXERCICIO = sw_processo.ano_exercicio )        \n";
    $sql .= "              ) as despacho                                                         \n";
    $sql .= "        FROM  sw_processo                                                           \n";

    $sql .= "  INNER JOIN  sw_processo_interessado                                               \n";
    $sql .= "          ON  sw_processo_interessado.cod_processo = sw_processo.cod_processo       \n";
    $sql .= "         AND  sw_processo_interessado.ano_exercicio = sw_processo.ano_exercicio     \n";

    $sql .= "  INNER JOIN  sw_assunto                                                            \n";
    $sql .= "          ON  sw_assunto.cod_assunto       = sw_processo.cod_assunto                \n";
    $sql .= "         AND  sw_assunto.cod_classificacao = sw_processo.cod_classificacao          \n";

    $sql .= "  INNER JOIN  sw_classificacao                                                      \n";
    $sql .= "          ON  sw_assunto.cod_classificacao = sw_classificacao.cod_classificacao     \n";

    $sql .= "  INNER JOIN  sw_cgm                                                                \n";
    $sql .= "          ON  sw_cgm.numcgm = sw_processo_interessado.numcgm                        \n";

    $sql .= "  INNER JOIN  sw_situacao_processo                                                  \n";
    $sql .= "          ON  sw_processo.cod_situacao  = sw_situacao_processo.cod_situacao         \n";

    $sql .= "  INNER JOIN  sw_ultimo_andamento                                                   \n";
    $sql .= "          ON  sw_processo.ano_exercicio = sw_ultimo_andamento.ano_exercicio         \n";
    $sql .= "         AND  sw_processo.cod_processo  = sw_ultimo_andamento.cod_processo          \n";

    $sql .= "   LEFT JOIN  sw_assunto_atributo_valor                                             \n";
    $sql .= "          ON  sw_assunto_atributo_valor.cod_processo  = sw_processo.cod_processo    \n";
    $sql .= "         AND  sw_assunto_atributo_valor.exercicio     = sw_processo.ano_exercicio   \n";
    $sql .= " WHERE 1=1                                                                          \n";
    $sql .= "   AND sw_situacao_processo.cod_situacao       = 2                                  \n";
    $sql .= "   AND  sw_ultimo_andamento.cod_orgao IN (SELECT cod_orgao
                                                             FROM organograma.vw_orgao_nivel
                                                            WHERE orgao_reduzido LIKE (
                                                                                        SELECT distinct(vw_orgao_nivel.orgao_reduzido)
                                                                                          FROM organograma.vw_orgao_nivel
                                                                                         WHERE vw_orgao_nivel.cod_orgao = ".Sessao::read('codOrgao')."
                                                                                       )";
                                                         # Permissão hierárquica define se o usuário pode ver processos de órgãos em níveis menores ou somente do seu nível.
                                                         $sql .= ($boPermissaoHierarquica == 't') ? "||'%'" : "";
                                                         $sql .= " GROUP BY cod_orgao) ";
                                                         
    if ($stChaveProcesso != "") {
        $codProcessoFl = preg_split("/[^a-zA-Z0-9]/", $stChaveProcesso);
        if ( (int) $codProcessoFl[0] ) {
            $sql .= " AND sw_processo.cod_processo  = ".(int) $codProcessoFl[0];
            $vet["stChaveProcesso"] = $stChaveProcesso;
        }
        if ($codProcessoFl[1] != "") {
            $sql .= " AND sw_processo.ano_exercicio = '".$codProcessoFl[1]."' ";
            $vet["anoExercicio"]  = $codProcessoFl[1];
        }
    }

    // FILTRA PELO ASSUNTO REDUZIDO
    if ($resumo) {
        $resumo = str_replace ("*", "%", $resumo);
        $sql .= " AND sw_processo.resumo_assunto like ('".$resumo."%') ";
        $vet["resumo"] = $resumo;
    }

    if ($codClassificacao != "" && $codClassificacao != "xxx") {
        $sql .= " AND sw_classificacao.cod_classificacao = ".$codClassificacao;
        $vet["codClassificacao"] = $codClassificacao;
    }

    if ($codAssunto != "" && $codAssunto != "xxx") {
        $sql .= " AND sw_assunto.cod_assunto = ".$codAssunto;
        $vet["codAssunto"] = $codAssunto;
    }

    if ($numCgm != "") {
        $sql .= " AND sw_processo_interessado.numcgm = ".$numCgm;
        $vet["numCgm"] = $numCgm;
    }

    if ($dataInicio != "" && $dataTermino != "") {
        $arrData     = explode("/", $dataInicio);
        $dataInicio = $arrData[2]."-".$arrData[1]."-".$arrData[0];
        $arrData     = explode("/", $dataTermino);
        $dataTermino   = $arrData[2]."-".$arrData[1]."-".$arrData[0];

        $sql .= " AND substr((sw_processo.timestamp)::varchar,1,10) >= '".$dataInicio."'";
        $sql .= " AND substr((sw_processo.timestamp)::varchar,1,10) <= '".$dataTermino."'";

        $vet["dataInicio"]  = $dataInicio;
        $vet["dataTermino"] = $dataTermino;
    }

    //FILTRO POR ATRIBUTO DE ASSUNTO
    if ($_REQUEST['valorAtributoTxt']) {
        foreach ($_REQUEST['valorAtributoTxt'] as $key => $value) {
            if ($_REQUEST['valorAtributoTxt'][$key]) {
                $sql .= " AND sw_assunto_atributo_valor.valor ILIKE ( '%".$_REQUEST[valorAtributoTxt][$key]."%' ) \n";
                $sql .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
            }
        }
    }
    if ($_REQUEST[valorAtributoNum]) {
        foreach ($_REQUEST[valorAtributoNum] as $key => $value) {
            if ($_REQUEST[valorAtributoNum][$key]) {
                $sql .= " AND sw_assunto_atributo_valor.valor = '".$_REQUEST[valorAtributoNum][$key]."' \n";
                $sql .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
            }
        }
    }

    if ($_REQUEST[valorAtributoCmb]) {
        foreach ($_REQUEST[valorAtributoCmb] as $key => $value) {
            if ($_REQUEST[valorAtributoCmb][$key]) {
                $sql .= " AND sw_assunto_atributo_valor.valor = '".$_REQUEST[valorAtributoCmb][$key]."' \n";
                $sql .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
            }
        }
    }

    $sql .= " GROUP BY sw_assunto.nom_assunto
                                    ,  sw_assunto.cod_assunto
                                    ,  sw_classificacao.nom_classificacao
                                    ,  sw_classificacao.cod_classificacao
                                    ,  sw_processo.ano_exercicio
                                    ,  sw_processo.cod_processo
                                    ,  sw_processo.timestamp
                                    ,  sw_ultimo_andamento.cod_andamento ";

    Sessao::write('sSQLs', $sql);
    Sessao::write('vet', $vet);

    $st_ordenacao = array(
         1 => "sw_processo.ano_exercicio
             , sw_processo.cod_processo",
         2 => "sw_cgm.nom_cgm",
         3 => "sw_classificacao.nom_classificacao
             , sw_assunto.nom_assunto
             , sw_processo.ano_exercicio
             , sw_processo.cod_processo",
         4 => "sw_processo.timestamp");

    $inOrdem = Sessao::read('ordem');
    if (!$inOrdem) {
        Sessao::write('sSQLs', $sql);
        Sessao::write('ordem', $_REQUEST["ordem"]);
    }

    $arProcessos = Sessao::read('arProcessos');

    if ($_POST['minProcesso']) {
        for ($inContador = $_POST['minProcesso'] ; $inContador <= $_POST['maxProcesso']; $inContador++) {
            if ($stChaveProcessoCkb[$inContador]) {
                $arProcessos[$inContador] = $stChaveProcessoCkb[$inContador];
            } else {
                unset( $arProcessos[$inContador] );
            }
        }
    }
    Sessao::write('arProcessos', $arProcessos);

    include(CAM_FW_LEGADO."paginacaoLegada.class.php");
    $paginacao = new paginacaoLegada;
    $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
    $paginacao->pegaPagina($pagina);
    $paginacao->complemento = "";
    $paginacao->geraLinksFuncao( 'enviaProcesso' );
    $paginacao->pegaOrder($st_ordenacao[Sessao::read('ordem')],"ASC");
    $sSQL = $paginacao->geraSQL();

    $count = $paginacao->contador();
    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();

    if ( $dbEmp->eof() ) {
        $pagina--;

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->complemento = "";
        $paginacao->geraLinksFuncao( 'enviaProcesso' );
        $paginacao->pegaOrder($st_ordenacao[Sessao::read('ordem')],"ASC");
        $sSQL = $paginacao->geraSQL();
        $count = $paginacao->contador();
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
    }

    $exec .= "
    <form action='".$_SERVER['PHP_SELF']."?".Sessao::getId()."' name=frm method=post onsubmit='return false'>
    <input type='hidden' name='minProcesso' value='$count'>
    <input type='hidden' name='maxProcesso' value='".($count + 9)."'>
    <table width='100%' id='processos'>
        <tr>
            <td class=alt_dados colspan='11'>
                Registros de processos
            </td>
        </tr>
        <tr>
            <td class='labelcenterCabecalho' width='5%'>&nbsp;</td>
            <td class='labelcenterCabecalho' style='vertical-align : middle;'>Código</td>
            <td class='labelcenterCabecalho' style='vertical-align : middle;'>Interessado</td>
            <td class='labelcenterCabecalho' style='vertical-align : middle;'>Classificação</td>
            <td class='labelcenterCabecalho' style='vertical-align : middle;'>Assunto</td>
            <td class='labelcenterCabecalho' style='vertical-align : middle;'>Inclusão</td>
            <td class='labelcenterCabecalho' style='vertical-align : middle;'>Despacho</td>
            <td class='labelcenterCabecalho' >&nbsp;</td>
            <td class='labelcenterCabecalho' >&nbsp;</td>
        </tr>
    ";
    while (!$dbEmp->eof()) {
        $codProcesso   = $dbEmp->pegaCampo("cod_processo");
        $anoEx         = $dbEmp->pegaCampo("ano_exercicio");
        $interessado   = $dbEmp->pegaCampo("nom_cgm");
        $classificacao = $dbEmp->pegaCampo("nom_Classificacao");
        $assunto       = $dbEmp->pegaCampo("nom_assunto");
        $codAndamento  = $dbEmp->pegaCampo("cod_Andamento");
        $timestamp     = $dbEmp->pegaCampo("timestamp");
        $stDespacho    = $dbEmp->pegaCampo("despacho") == "t" ? "Sim" : "Não";

        $chave = $codProcesso."-".$anoEx."-".$codAndamento;
        $dbEmp->vaiProximo();
        $arr                = explode(" ", $timestamp);
        $arrData            = explode("-", $arr[0]);
        $dataInclusao       = $arrData[2]."/".$arrData[1]."/".$arrData[0];
        $codProcessoC    = $codProcesso.$anoEx;
        $numCasas        = strlen($mascaraProcesso) - 1;
        $iCodProcessoS   = str_pad($codProcessoC, $numCasas, "0" ,STR_PAD_LEFT);
        $iCodProcessoS   = geraMascaraDinamica($mascaraProcesso, $iCodProcessoS);
        $exec .= "
        <tr>
            <td class='show_dados_center_bold'>".$count++."</td>
            <td class='show_dados'>".$iCodProcessoS."</td>
            <td class='show_dados'>".$interessado."</td>
            <td class='show_dados'>".$classificacao."</td>
            <td class='show_dados'>".$assunto."</td>
            <td class='show_dados'>".$dataInclusao."</td>
            <td class='show_dados'>".$stDespacho."</td>
            <td class='botao'><div align='center' title='Consultar processo'>
                <a href='consultaProcesso.php?".
                Sessao::getId()."&codProcesso=".
                $codProcesso."&anoExercicio=".
                $anoEx."&anoExercicioSetor=".
                $anoExSetor."&controle=0&ctrl=2&pagina=".
                $pagina."&verificador=true&codClassificacao=".$codClassificacao.
                    "&dataInicial=".$dataInicial."&dataFinal=".$dataFinal."&stChaveProcesso=".$stChaveProcesso."'>
                <img src='".CAM_FW_IMAGENS."procuracgm.gif' alt='Consultar Processo' border=0>
                </a></div>
            </td>
            <td class='botao' title='Receber processo'>
        ";

        if ($arProcessos[$count- 1]) {
            $stChecked = " checked";
        } else {
            $stChecked = "";
        }
        $exec .="   <input type='checkbox' value='$chave' name='stChaveProcessoCkb[".($count- 1)."]'".$stChecked."></td>";
        $exec .="</tr>";
    }
    if ($dbEmp->numeroDeLinhas <= 0) {
        $exec .= "</table>";
        $exec .=  "<b>Não Existem Processos a Receber!</b>";
    } else {
        $exec .= "<tr>";
        $exec .= "    <td class='fieldright' colspan='8' nowrap>";
        $exec .= "         <div id='marcador'>Marcar todos</div>";
        $exec .= "    </td>";
        $exec .= "    <td class='field' nowrap>";
        $exec .= "        <input type='checkbox' onChange='marcarTodos(this);'>";
        $exec .= "    </td>";
        $exec .= "</tr>";
        $exec .= "</table>";
    }

    $exec .= "</form>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo $exec;
    echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
    $paginacao->mostraLinks();
    echo "</font></tr></td></table>";
    if ($dbEmp->numeroDeLinhas >= 1) {
?>
        <table width='100%'>
            <tr>
                <td class='field'>
                    &nbsp;<input type="button" value="Receber" style='width: 60px;' onClick="receberProcessos();">
                </td>
            </tr>
        </table>
<?php
    }
?>
<script>zebra('processos','zb');</script>
<?php
break;

    case 2:

    $stChaveProcessoCkb = $_REQUEST['stChaveProcessoCkb'];

    /*  { Legado }
     *  Devido a inclusão de multi-requerentes, é necessário unificar o array
     *  para que não tenha duplicidade no código do processo.
     *  Quando o módulo for refeito, terá uma table-tree para organizar
     *  os multi-requerentes na tela de listagem, sem precisar listar mais
     *  de uma vez o mesmo processo devido ao multi-requerentes.
     */
    $stChaveProcessoCkb = array_unique($stChaveProcessoCkb);

    $arProcessos = Sessao::read('arProcessos');

    if ($_REQUEST['minProcesso']) {
        for ($inContador = $_REQUEST['minProcesso']; $inContador <= $_REQUEST['maxProcesso']; $inContador++) {
            if ($stChaveProcessoCkb[$inContador]) {
                $arProcessos[$inContador] = $stChaveProcessoCkb[$inContador];
            } else {
                unset( $arProcessos[$inContador] );
            }
        }
    }

    Sessao::write('arProcessos', $arProcessos);

        if ( count( $arProcessos ) > 0 ) {

            foreach ($arProcessos as $stProcessos) {
                $arProcesso = explode( "-", $stProcessos );
                $codProc = $arProcesso[0];
                $anoExe = $arProcesso[1];

                $codOrgaoUltimoAndamento = SistemaLegado::pegaDado("cod_orgao","sw_ultimo_andamento"," where cod_processo = ".$codProc." and ano_exercicio = '".$anoExe."' ");
                $classificacaoOrgaoUsuario = SistemaLegado::pegaDado('orgao_reduzido','organograma.vw_orgao_nivel', 'where cod_orgao='.Sessao::read('codOrgao').' order by criacao limit 1' );
                $inCodOrgao = Sessao::read('codOrgao');

                //Verifica se a classificação do usuário é superior hierarquicamente a classificação do ultimo andamento
                if (verificaHierarquiaOrgao($classificacaoOrgaoUsuario,$codOrgaoUltimoAndamento)) {

                    $processos = new processosLegado;
                    //Executa o recebimento dos proceessos
                    if ($processos->recebeProcessosLote($arProcessos,Sessao::read('numCgm'))) {
                        //Insere auditoria
                        $stAuditoria = "";
                        $arAuditoria =  array();
                        reset( $arProcessos );
                        foreach ($arProcessos as $stProcessos) {
                            $arProcesso = explode( "-", $stProcessos );
                            $codProcesso  = (integer) $arProcesso[0];
                            $anoExercicio = $arProcesso[1];
                            //SALVA UMA NOVA AUDITORIA QUANTO A STRING DOS PROCESSO ULTRAPASSAR 500 CARACTERES
                            //ESTE CONTROLE É FEITO PROQUE O CAMPO DA AUDITORIA É UM VARCHAR DE 500 POSIÇÕES
                            if ( strlen( $stAuditoria.$arProcesso[0].'_'.$arProcesso[1]."-" ) >= 500 ) {
                                $stAuditoria = substr( $stAuditoria, 0, strlen($stAuditoria) - 1 );
                                $arAuditoria[] = $stAuditoria;
                                $stAuditoria = "";
                            }
                            $stAuditoria .= $arProcesso[0].'_'.$arProcesso[1]."-";
                        }
                        $stAuditoria = substr( $stAuditoria, 0, strlen($stAuditoria) - 1 );
                        $arAuditoria[] = $stAuditoria;
                        unset( $arProcessos );
                        Sessao::remove('arProcessos');
                        $audicao = new auditoriaLegada;
                        foreach ($arAuditoria as $stChavesProcessos) {
                            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $stChavesProcessos );
                            $audicao->insereAuditoria();
                        }

                        alertaAviso($PHP_SELF."?".Sessao::getId()."&pagina=".$pagina."&controle=1&codProcesso=".$codProcesso."&numCgm=".$numCgm."&stChaveProcesso=".$stChaveProcesso."&codAssunto=".$codAssunto."&codClassificacao=".$codClassificacao."&resumo=".$resumo."&dataInicio=".$dataInicio."&dataTermino=".$dataTermino,"Processos recebidos com sucesso","historico","aviso");
                    } else {
                       alertaAviso($PHP_SELF."?".Sessao::getId()."&pagina=".$pagina."&controle=1&codProcesso=".$codProcesso."&numCgm=".$numCgm."&stChaveProcesso=".$stChaveProcesso."&codAssunto=".$codAssunto."&codClassificacao=".$codClassificacao."&resumo=".$resumo."&dataInicio=".$dataInicio."&dataTermino=".$dataTermino,"Erro ao receber processos","unica","erro");
                    }
                } else {
                    alertaAviso($PHP_SELF."?".Sessao::getId()."&pagina=".$pagina."&controle=1&codProcesso=".$codProcesso."&numCgm=".$numCgm."&stChaveProcesso=".$stChaveProcesso."&codAssunto=".$codAssunto."&codClassificacao=".$codClassificacao."&resumo=".$resumo."&dataInicio=".$dataInicio."&dataTermino=".$dataTermino,"Erro ao receber processos (Processo(s) ".$codProc."/".$anoExe." alterado(s) por outro usuário)","unica","erro");
                    break;
                }
            }
        } else {
            alertaAviso($PHP_SELF."?".Sessao::getId()."&pagina=".$pagina."&controle=1&codProcesso=".$codProcesso."&numCgm=".$numCgm."&stChaveProcesso=".$stChaveProcesso."&codAssunto=".$codAssunto."&codClassificacao=".$codClassificacao."&resumo=".$resumo."&dataInicio=".$dataInicio."&dataTermino=".$dataTermino,"Nenhum processo selecionado","unica","aviso");
        }
        break;
case 100:
    include(CAM_FW_LEGADO."filtrosCASELegado.inc.php");
break;
}//Fim switch

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html

/*
*verificaHierarquiaOrgao
*
*Verifica se o processo pode ser recebido se esta em alguma hierarquia permitida ao usuário
*@param $classificacaoReduzidoUsuario codigo de orgao_reduzido do usuário
*@param $codOrgaoUltimoAndamento codigo de orgao do ultimo andamento
*@return boolean TRUE pode executar ação FALSE sem permissão
*
**/
function verificaHierarquiaOrgao($classificacaoReduzidoUsuario,$codOrgaoUltimoAndamento)
{
    $stSql = "SELECT cod_orgao FROM ORGANOGRAMA.VW_ORGAO_NIVEL
               where cod_orgao = ".$codOrgaoUltimoAndamento."
                 and cod_orgao in (SELECT cod_orgao FROM ORGANOGRAMA.VW_ORGAO_NIVEL WHERE orgao_reduzido like '".$classificacaoReduzidoUsuario."%')
               limit 1";

    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();

    $dbEmp->abreSelecao($stSql);

    if ($dbEmp->numeroDeLinhas > 0) {
        return true;
    } else {
        return false;
    }
}

?>
