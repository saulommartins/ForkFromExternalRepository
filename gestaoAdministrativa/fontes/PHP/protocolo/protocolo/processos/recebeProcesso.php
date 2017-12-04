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

        $Id: recebeProcesso.php 66029 2016-07-08 20:55:48Z carlos.silva $
        */

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_FW_LEGADO."processosLegado.class.php"; //Insere a classe que manipula os dados do processo
include CAM_FW_LEGADO."auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
include 'interfaceProcessos.class.php'; //Inclui classe que contém a interface html
include CAM_FW_LEGADO."mascarasLegado.lib.php";
include CAM_FW_LEGADO."funcoesLegado.lib.php" ;
setAjuda('uc-01.06.98');

$mascaraProcesso  = pegaConfiguracao("mascara_processo", 5);
$pagina           = $request->get('pagina');
$codProcesso      = $request->get('codProcesso');
$pagina           = $request->get('pagina');
$numCgm           = $request->get('numCgm');
$stChaveProcesso  = $request->get('stChaveProcesso');
$dataInicio       = $request->get('dataInicio');
$dataTermino      = $request->get('dataTermino');
$resumo           = $request->get('resumo');
$codClassificacao = $request->get('codClassificacao');
$codAssunto       = $request->get('codAssunto');

if (!isset($_REQUEST["controle"])) {
        $controle = 0;
} else {
    $controle = $_REQUEST["controle"];
}

$codProcessoFl = isset($codProcessoFl) ? $codProcessoFl : null;

$ctrl = $request->get('ctrl');
if ($ctrl == 2) {
    $controle = 1;
}

?>
<script type="text/javascript">
    function Salvar()
    {
        document.frm.action = "recebeProcesso.php?<?=Sessao::getId()?>&controle=1";
        document.frm.submit();
    }
    function zebra(id, classe)
    {
       var tabela = document.getElementById(id);
        var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
            ((i%2) == 0) ? linhas[i].className = classe : void(0);
        }
    }
</script>

<?php

$exec = isset($exec) ? $exec : null;

switch ($controle) {
case 0:
    include(CAM_FW_LEGADO."filtrosProcessoLegado.inc.php");
break;
case 1:

    $vet = Sessao::read('vet');
    if (is_array($vet)) {
        foreach ($vet AS $indice => $valor) {
            $$indice = $valor;
        }
    }
        
        $inCodOrganograma = SistemaLegado::pegaDado('cod_organograma', 'organograma.vw_orgao_nivel', ' WHERE cod_orgao = '.Sessao::read('codOrgao'));
        $boPermissaoHierarquica = SistemaLegado::pegaDado('permissao_hierarquica', 'organograma.organograma', ' WHERE cod_organograma = '.$inCodOrganograma);

        $sql  = "";
        $sql .= "
                  SELECT
                          DISTINCT sw_assunto.nom_assunto
                       ,  sw_assunto.cod_assunto
                       ,  array_to_string(array_agg(nom_cgm), ', ')as nom_cgm
                       ,  sw_classificacao.nom_classificacao
                       ,  sw_classificacao.cod_classificacao
                       ,  sw_processo.ano_exercicio
                       ,  sw_processo.cod_processo
                       ,  sw_processo.timestamp
                       ,  sw_ultimo_andamento.cod_andamento
                       ,  ( EXISTS ( SELECT 1 FROM SW_DESPACHO WHERE COD_PROCESSO = sw_processo.cod_processo AND ANO_EXERCICIO = sw_processo.ano_exercicio ) ) as despacho
                       ,  ( EXISTS ( SELECT 1 FROM SW_PROCESSO_APENSADO WHERE COD_PROCESSO_PAI = sw_processo.cod_processo AND EXERCICIO_PAI = sw_processo.ano_exercicio AND TIMESTAMP_DESAPENSAMENTO IS NULL ) ) as apenso

                   FROM  sw_processo

             INNER JOIN  sw_processo_interessado
                     ON  sw_processo_interessado.cod_processo = sw_processo.cod_processo
                    AND  sw_processo_interessado.ano_exercicio = sw_processo.ano_exercicio

             INNER JOIN  sw_assunto
                     ON  sw_assunto.cod_assunto       = sw_processo.cod_assunto
                    AND  sw_assunto.cod_classificacao = sw_processo.cod_classificacao

             INNER JOIN  sw_classificacao
                     ON  sw_assunto.cod_classificacao = sw_classificacao.cod_classificacao

             INNER JOIN  sw_cgm
                     ON  sw_cgm.numcgm = sw_processo_interessado.numcgm

             INNER JOIN  sw_situacao_processo
                     ON  sw_processo.cod_situacao  = sw_situacao_processo.cod_situacao

             INNER JOIN  sw_ultimo_andamento
                     ON  sw_processo.ano_exercicio = sw_ultimo_andamento.ano_exercicio
                    AND  sw_processo.cod_processo  = sw_ultimo_andamento.cod_processo

              LEFT JOIN  sw_assunto_atributo_valor
                     ON  sw_assunto_atributo_valor.cod_processo  = sw_processo.cod_processo
                    AND  sw_assunto_atributo_valor.exercicio     = sw_processo.ano_exercicio

                  WHERE  sw_situacao_processo.cod_situacao       = 2
                    AND  sw_ultimo_andamento.cod_orgao IN (SELECT cod_orgao
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
                  $sql .= " AND sw_processo.cod_processo  = ".(int) $codProcessoFl[0];
                  $vet["stChaveProcesso"] = $stChaveProcesso;
                }

                if ($codProcessoFl[1] != "") {
                  $sql .= " AND sw_processo.ano_exercicio = '".$codProcessoFl[1]."' ";
                  $vet["anoExercicio"]  = $codProcessoFl[1];
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
                  $sql .= " AND sw_processo.timestamp::date between TO_DATE('".$dataInicio."','dd/mm/yyyy') AND ";
                  $sql .= " TO_DATE('".$dataTermino."','dd/mm/yyyy') ";
                  $vet["dataInicio"] = $dataInicio;
                  $vet["dataTermino"]   = $dataTermino;
                }

                //FILTRO POR ATRIBUTO DE ASSUNTO
                if ( $request->get('valorAtributoTxt') ) {
                  foreach ($_REQUEST['valorAtributoTxt'] as $key => $value) {
                    if ($_REQUEST['valorAtributoTxt'][$key]) {
                      $sql .= " AND sw_assunto_atributo_valor.valor ILIKE ( '%".$_REQUEST['valorAtributoTxt'][$key]."%' ) \n";
                      $sql .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
                    }
                  }
                }

                if ( $request->get('valorAtributoNum') ) {
                  foreach ($_REQUEST['valorAtributoNum'] as $key => $value) {
                    if ($_REQUEST['valorAtributoNum'][$key]) {
                      $sql .= " AND sw_assunto_atributo_valor.valor = '".$_REQUEST['valorAtributoNum'][$key]."' \n";
                      $sql .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
                    }
                  }
                }

                if ( $request->get('valorAtributoCmb') ) {
                  foreach ($_REQUEST['valorAtributoCmb'] as $key => $value) {
                    if ($_REQUEST['valorAtributoCmb'][$key]) {
                      $sql .= " AND sw_assunto_atributo_valor.valor = '".$_REQUEST['valorAtributoCmb'][$key]."' \n";
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

                $st_ordenacao = array(
                     1 => "sw_processo.ano_exercicio
                         , sw_processo.cod_processo",
                     2 => "sw_cgm.nom_cgm",
                     3 => "sw_classificacao.nom_classificacao
                         , sw_assunto.nom_assunto
                         , sw_processo.ano_exercicio
                         , sw_processo.cod_processo",
                     4 => "sw_processo.timestamp");

                Sessao::write('vet', $vet);
                $inOrdem = Sessao::read('ordem');

                if (!$inOrdem) {
                  Sessao::write('sSQLs', $sql);
                  Sessao::write('ordem', $_REQUEST["ordem"]);
                }

    $numCgmU      = isset($numCgmU)      ? $numCgmU      : null;
    $numCgmUltimo = isset($numCgmUltimo) ? $numCgmUltimo : null;
    $dataInicial  = isset($dataInicial)  ? $dataInicial  : null;
    $dataFinal    = isset($dataFinal)    ? $dataFinal    : null;
    $ordem        = isset($ordem)        ? $ordem        : null;

        include(CAM_FW_LEGADO."paginacaoLegada.class.php");
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($pagina);
    $paginacao->complemento = "&controle=1&codProcessoFl=".$codProcessoFl."&codClassificacao=".$codClassificacao."&codAssunto=".$codAssunto."&numCgm=".$numCgm."&numCgmU=".$numCgmU."&numCgmUltimo=".$numCgmUltimo."&dataInicial=".$dataInicial."&dataFinal=".$dataFinal."&ordem=".$ordem;
        $paginacao->geraLinks();
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
            $paginacao->complemento = "&controle=1&codProcessoFl=".$codProcessoFl."&codClassificacao=".$codClassificacao."&codAssunto=".$codAssunto."&numCgm=".$numCgm."&numCgmU=".$numCgmU."&numCgmUltimo=".$numCgmUltimo."&dataInicial=".$dataInicial."&dataFinal=".$dataFinal."&ordem=".$ordem;
            $paginacao->geraLinks();
            $paginacao->pegaOrder($st_ordenacao[Sessao::read('ordem')],"ASC");
            $sSQL = $paginacao->geraSQL();
            $count = $paginacao->contador();
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();

            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
        }

        $exec .= "
        <form action='".$PHP_SELF."?".Sessao::getId()."' name=frm method=post onsubmit='return false'>
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
                <td class='labelcenterCabecalho' style='vertical-align : middle;'>Apenso</td>
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
                $stApenso    = $dbEmp->pegaCampo("apenso") == "t" ? "Sim" : "Não";

                $chave = $codProcesso."-".$anoEx."-".$codAndamento;
                $dbEmp->vaiProximo();
                $arr                = explode(" ", $timestamp);
                $arrData            = explode("-", $arr[0]);
                $dataInclusao       = $arrData[2]."/".$arrData[1]."/".$arrData[0];
                $codProcessoC    = $codProcesso.$anoEx;
                $numCasas        = strlen($mascaraProcesso) - 1;
                $iCodProcessoS   = str_pad($codProcessoC, $numCasas, "0" ,STR_PAD_LEFT);
                $iCodProcessoS   = geraMascaraDinamica($mascaraProcesso, $iCodProcessoS);
                $anoExSetor      = isset($anoExSetor) ? $anoExSetor : null;
        $exec .= "
                 <tr>
                 <td class='show_dados_center_bold'>
                    ".$count++."
                 </td>
                <td class='show_dados'>
                    ".$iCodProcessoS."
                </td>
                <td class='show_dados'>
                    ".$interessado."
                </td>
                <td class='show_dados'>
                    ".$classificacao."
                </td>
                <td class='show_dados'>
                    ".$assunto."
                </td>
                <td class='show_dados'>
                     ".$dataInclusao."
                </td>
                <td class='show_dados'>
                     ".$stDespacho."
                </td>
                <td class='show_dados'>
                     ".$stApenso."
                </td>
                <td class='botao'><div align='center' title='Consultar processo'>
                    <a href='consultaProcesso.php?".
                    Sessao::getId()."&codProcesso=".
                    $codProcesso."&stChaveProcesso=".$stChaveProcesso."&anoExercicio=".
                    $anoEx."&anoExercicioSetor=".
                    $anoExSetor."&controle=0&ctrl=2&pagina=".
                    $pagina."&verificador=true&codClassificacao=".$_REQUEST['codClassificacao'].
                    "&dataInicial=".$request->get('dataInicial')."&dataFinal=".$request->get('dataFinal')."'>
                    <img src='".CAM_FW_IMAGENS."procuracgm.gif' alt='Consultar Processo' border=0>
                    </a></div>
                </td>
                <td class='botao' title='Receber processo'>
                    <a href='javascript:mudaTelaPrincipal(\"recebeProcesso.php?".
                    Sessao::getId()."&codProcesso=".
                    $chave."&controle=2&pagina=".$pagina."&stChaveProcesso=".$request->get('stChaveProcesso')."&anoExercicio=".
                    $request->get('anoEx')."&numCgm=".$_REQUEST['numCgm']."&codClassificacao=".$_REQUEST['codClassificacao'].
                    "&dataInicial=".$request->get('dataInicial')."&dataFinal=".$request->get('dataFinal')."\");'>
                        <img src='".CAM_FW_IMAGENS."botao_receber.png' border=0>
                    </a>
                </td>
            </tr>";
        }
        $exec .= "</table>";
        if ($dbEmp->numeroDeLinhas <= 0) {
            $exec .=  "<b>Não Existem Processos a Receber!</b>";
        }
        $exec .= "</form>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $exec;
        echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
break;

    case 2:
        //Verifica se algum processo foi selecionado
        if (isset($codProcesso)) {

            $arProcesso = explode("-",$codProcesso);
            $codProc = $arProcesso[0];
            $anoExe = $arProcesso[1];

            $codOrgaoUltimoAndamento = SistemaLegado::pegaDado("cod_orgao","sw_ultimo_andamento"," where cod_processo = ".$codProc." and ano_exercicio = '".$anoExe."' ");
            $classificacaoOrgaoUsuario = SistemaLegado::pegaDado('orgao_reduzido','organograma.vw_orgao_nivel', 'where cod_orgao='.Sessao::read('codOrgao').' order by criacao limit 1' );

            //Verifica se a classificação do usuário é superior hierarquicamente a classificação do ultimo andamento
            if (verificaHierarquiaOrgao($classificacaoOrgaoUsuario,$codOrgaoUltimoAndamento)) {

                $processos = new processosLegado;
                //Executa o recebimento dos processos
                if ($processos->recebeProcessos($codProcesso,Sessao::read('numCgm'))) {
                    //Insere auditoria
                    $audicao = new auditoriaLegada;
                    $vet = explode("-",$codProcesso);
                    $codP = $vet[0];
                    $anoEx = $vet[1];
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $codP."/".$anoEx);
                    $audicao->insereAuditoria();

                    $verificaProcessoApenso = pegaDado('cod_processo_pai','sw_processo_apensado',' where cod_processo_pai = '.$codP);
                    if ($verificaProcessoApenso) {
                        alertaAviso($PHP_SELF."?".Sessao::getId()."&pagina=".$pagina."&controle=1&numCgm=".$_REQUEST['numCgm']."&stChaveProcesso=".$_REQUEST['stChaveProcesso']."","Processo ".$codP."/".$anoEx." recebido com sucesso! Existe(m) processo(s) apenso(s) a ele. Consulte.","historico","aviso");
                    }

                    alertaAviso($PHP_SELF."?".Sessao::getId()."&pagina=".$pagina."&controle=1&numCgm=".$_REQUEST['numCgm']."&stChaveProcesso=".$_REQUEST['stChaveProcesso']."","Processo ".$codP."/".$anoEx." recebido com sucesso","historico","aviso");
                } else {
                   alertaAviso($PHP_SELF."?".Sessao::getId()."&pagina=".$pagina."&controle=1&numCgm=".$_REQUEST['numCgm']."&stChaveProcesso=".$_REQUEST['stChaveProcesso'],"Erro ao receber processos","unica","erro");
                }
            } else {
                alertaAviso($PHP_SELF."?".Sessao::getId()."&pagina=".$pagina."&controle=1&numCgm=".$_REQUEST['numCgm']."&stChaveProcesso=".$_REQUEST['stChaveProcesso'],"Erro ao receber processos (Processo ".$codProc."/".$anoExe." alterado por outro usuário)","unica","erro");
                break;
            }
        } else {
            alertaAviso($PHP_SELF."?".Sessao::getId()."&pagina=".$pagina."&controle=1","Nenhum processo selecionado","unica","aviso");
        }
        break;
case 100:
    include(CAM_FW_LEGADO."filtrosCASELegado.inc.php");
break;
} //Fim switch
?>
<script>zebra('processos','zb');</script>
<?php
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
