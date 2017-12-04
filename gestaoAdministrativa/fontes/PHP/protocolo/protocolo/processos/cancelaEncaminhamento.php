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

    * $Id: cancelaEncaminhamento.php 66029 2016-07-08 20:55:48Z carlos.silva $

    * Casos de uso: uc-01.06.98

    */

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_FW_LEGADO."mascarasLegado.lib.php";
include CAM_FW_LEGADO."funcoesLegado.lib.php";
setAjuda('uc-01.06.98');

$codProcesso = $_REQUEST['codProcesso'];
$mascaraProcesso = pegaConfiguracao("mascara_processo", 5);

$stChaveProcesso  = $_REQUEST["stChaveProcesso"];
$codProcessoFl    = $_REQUEST['codProcessoFl'];
$codClassificacao = $_REQUEST['codClassificacao'];
$codAssunto       = $_REQUEST['codAssunto'];
$numCgm           = $_REQUEST['numCgm'];
$numCgmU          = $_REQUEST['numCgmU'];
$numCgmUltimo     = $_REQUEST['numCgmUltimo'];

$dataInicial      = $_REQUEST['dataInicial'];
$dataFinal        = $_REQUEST['dataFinal'];

$dataInicio       = $_REQUEST['dataInicio'];
$dataTermino      = $_REQUEST['dataTermino'];

$pagina           = $_REQUEST['pagina'];
$ctrl			  = $_REQUEST["ctrl"];
$ordem			  = $_REQUEST["ordem"];

$pagina           = $_REQUEST['pagina'];

if (!(isset($ctrl))) {
    $ctrl = 0;
}

if ($_REQUEST['chave'] != "") {
    $ctrl = 2;
}

if ($_REQUEST['controle'] == 1) {
    $ctrl = $_REQUEST['controle'];
} ?>

<script type="text/javascript">
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
switch ($ctrl) {
case 0:
    Sessao::remove('filtro');
?>
<script type="text/javascript">
    function Valida()
    {
        var erro = false;
        var mensagem = "";
        stCampo = document.frm.dataInicio;
        if (stCampo.value != "") {
            if ( !verificaData(stCampo) ) {
                erro = true;
                mensagem += "@Campo Periodo de inclusão inválido!()";
            }
        }
        stCampo = document.frm.dataTermino;
        if (stCampo.value != "") {
            if ( !verificaData(stCampo) ) {
                erro = true;
                mensagem += "@Campo Periodo de inclusão inválido!()";
            }
        }
        if (erro) {
             alertaAviso(mensagem,'form','erro','PHPSESSID=d654a01e86f0b7d173b8be35f39e5833&iURLRandomica=20051216151440.263', '../');
        }

        return !erro;
    }

    function Salvar()
    {
        if ( Valida() ) {
            document.frm.action = "cancelaEncaminhamento.php?<?=Sessao::getId()?>&ctrl=1";
            document.frm.submit();
        }
    }
</script>
<?php

include '../../../framework/legado/filtrosProcessoLegado.inc.php';
break;

case 1:

if (Sessao::read('vet') != "") {
    $vet = Sessao::read('vet');
    foreach ($vet AS $indice => $valor) {
        $$indice = $valor;
    }
}

if (Sessao::read('ordem') != "") {
    $ordem = Sessao::read('ordem');
}
        // U - Ultimo andamento
        // P - penultimo andamento

    $inCodOrganograma = SistemaLegado::pegaDado('cod_organograma', 'organograma.vw_orgao_nivel', ' WHERE cod_orgao = '.Sessao::read('codOrgao'));
    $boPermissaoHierarquica = SistemaLegado::pegaDado('permissao_hierarquica', 'organograma.organograma', ' WHERE cod_organograma = '.$inCodOrganograma);

        $stSQL = "
                    SELECT
                        DISTINCT sw_processo.ano_exercicio AS exercicio
                        , sw_processo.cod_processo  AS codprocesso
                        , sw_processo.timestamp
                        , sw_classificacao.nom_classificacao AS nomclassificacao
                        , sw_assunto.nom_assunto AS nomassunto
                        , array_to_string(array_agg(nom_cgm), ', ')AS nominteressado
                        , sw_ultimo_andamento.cod_orgao
                        -- , sw_ultimo_andamento.cod_unidade
                        -- , sw_ultimo_andamento.cod_departamento
                        -- , sw_ultimo_andamento.cod_setor
                        -- , sw_ultimo_andamento.ano_exercicio_setor

                    FROM  sw_processo

              INNER JOIN  sw_processo_interessado
                      ON  sw_processo_interessado.cod_processo = sw_processo.cod_processo
                     AND  sw_processo_interessado.ano_exercicio = sw_processo.ano_exercicio

              INNER JOIN  sw_cgm
                      ON  sw_cgm.numcgm = sw_processo_interessado.numcgm

              INNER JOIN  sw_assunto
                      ON  sw_assunto.cod_assunto       = sw_processo.cod_assunto
                     AND  sw_assunto.cod_classificacao = sw_processo.cod_classificacao

              INNER JOIN  sw_classificacao
                      ON  sw_assunto.cod_classificacao = sw_classificacao.cod_classificacao

              INNER JOIN  sw_situacao_processo
                      ON  sw_processo.cod_situacao  = sw_situacao_processo.cod_situacao

              INNER JOIN  sw_andamento
                      ON  sw_andamento.ano_exercicio        = sw_processo.ano_exercicio
                     AND  sw_andamento.cod_processo         = sw_processo.cod_processo

              INNER JOIN  sw_ultimo_andamento
                      ON  sw_processo.ano_exercicio = sw_ultimo_andamento.ano_exercicio
                     AND  sw_processo.cod_processo  = sw_ultimo_andamento.cod_processo

               LEFT JOIN  sw_assunto_atributo_valor
                      ON  sw_assunto_atributo_valor.cod_processo  = sw_processo.cod_processo
                     AND  sw_assunto_atributo_valor.exercicio     = sw_processo.ano_exercicio

                   WHERE  1=1 ";

        //FILTORS DE REGRA DE NEGOCIO
        $stSQL .= " AND sw_andamento.cod_andamento        = (sw_ultimo_andamento.cod_andamento - 1)     \n";
        $stSQL .= " AND sw_situacao_processo.cod_situacao = 2                                           \n";
        $stSQL .= " AND sw_andamento.cod_orgao       IN (  SELECT cod_orgao
                                                             FROM organograma.vw_orgao_nivel
                                                            WHERE orgao_reduzido LIKE (
                                                                                        SELECT distinct(vw_orgao_nivel.orgao_reduzido)
                                                                                          FROM organograma.vw_orgao_nivel
                                                                                         WHERE vw_orgao_nivel.cod_orgao = ".Sessao::read('codOrgao')."
                                                                                       )";
                                                         # Permissão hierárquica define se o usuário pode ver processos de órgãos em níveis menores ou somente do seu nível.
                                                         $stSQL .= ($boPermissaoHierarquica == 't') ? "||'%'" : "";
                                                         $stSQL .= " GROUP BY cod_orgao) ";

        if ($stChaveProcesso != "") {
            $codProcessoFl = preg_split( "/[^a-zA-Z0-9]/", $stChaveProcesso);
            $stSQL .= " AND sw_processo.cod_processo  = ".(int) $codProcessoFl[0];
            $vet["stChaveProcesso"] = $stChaveProcesso;
        }

        if ($codProcessoFl[1] != "") {
            $stSQL .= " AND sw_processo.ano_exercicio = '".$codProcessoFl[1]."' ";
            $vet["anoExercicio"]  = $codProcessoFl[1];
        }

        //FILTRO POR CLASSIFICACAO
        $codClassificacao = $_REQUEST["codClassificacao"];

        if ($codClassificacao != "" && $codClassificacao != "xxx") {
            $stSQL .= " AND sw_processo.cod_classificacao = ".$codClassificacao;
            $vet["codClassificacao"] = $codClassificacao;
        }

        //FILTRO PRO ASSUNTO
        $codAssunto = $_REQUEST["codAssunto"];
        if ($codAssunto != "" && $codAssunto != "xxx") {
                $stSQL .= " AND sw_processo.cod_assunto = ".$codAssunto;
            $vet["codAssunto"] = $codAssunto;
        }

        //FILTRO POR INERESSADO
        $numCgm = $_REQUEST["numCgm"];
        if ($numCgm != "") {
            $stSQL .= " AND sw_processo_interessado.numcgm = ".$numCgm;
                $vet["numCgm"] = $numCgm;
        }

        //FILTRO POR PERIODO
        $dataInicio = $_REQUEST["dataInicio"];
        $dataTermino = $_REQUEST["dataTermino"];

        if ($dataInicio != "" && $dataTermino != "" && $dataInicio != $dataTermino) {
            $vet["dataInicio"] = $dataInicio;
            $vet["dataTermino"]   = $dataTermino;
            $arrData     = explode("/", $dataInicio);
            $dataInicioAux = $arrData[2]."-".$arrData[1]."-".$arrData[0];
            $arrData     = explode("/", $dataTermino);
            $dataTerminoAux   = $arrData[2]."-".$arrData[1]."-".$arrData[0];
            $stSQL .= " AND TO_DATE(sw_processo.timestamp::varchar, 'yyyy-mm-dd') ";
            $stSQL .= " BETWEEN '".$dataInicioAux."' AND '".$dataTerminoAux."' ";
        } elseif ( $dataInicio != "" && $dataTermino == "" or ( $dataInicio != "" && $dataInicio == $dataTermino ) ) {
            $vet["dataInicio"] = $dataInicio;
            $vet["dataTermino"]   = $dataTermino;
            $arrData     = explode("/", $dataInicio);
            $dataInicioAux = $arrData[2]."-".$arrData[1]."-".$arrData[0];
            $arrData     = explode("/", $dataTermino);
            $dataTerminoAux   = $arrData[2]."-".$arrData[1]."-".$arrData[0];
            $stSQL .= " AND TO_DATE(sw_processo.timestamp::varchar, 'yyyy-mm-dd') = '".$dataInicioAux."'";
        }

        //FILTRO PRO ASSUNTO REDUZIDO
        $resumo = $_REQUEST["resumo"];
        if ($resumo != "") {
             $stSQL .= " AND sw_processo.resumo_assunto ILIKE '".$resumo."%' ";
             $vet["resumo"] = $resumo;
        }

        //FILTRO POR ATRIBUTO DE ASSUNTO
        if ($_REQUEST['valorAtributoTxt']) {
            foreach ($_REQUEST['valorAtributoTxt'] as $key => $value) {
                if ($_REQUEST['valorAtributoTxt'][$key]) {
                    $stSQL .= " AND sw_assunto_atributo_valor.valor ILIKE ( '%".$_REQUEST['valorAtributoTxt'][$key]."%' ) \n";
                    $stSQL .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
                }
            }
        }
        if ($_REQUEST['valorAtributoNum']) {
            foreach ($_REQUEST['valorAtributoNum'] as $key => $value) {
                if ($_REQUEST['valorAtributoNum'][$key]) {
                    $stSQL .= " AND sw_assunto_atributo_valor.valor = '".$_REQUEST['valorAtributoNum'][$key]."' \n";
                    $stSQL .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
                }
            }
        }
        if ($_REQUEST['valorAtributoCmb']) {
            foreach ($_REQUEST['valorAtributoCmb'] as $key => $value) {
                if ($_REQUEST['valorAtributoCmb'][$key]) {
                    $stSQL .= " AND sw_assunto_atributo_valor.valor = '".$_REQUEST[valorAtributoCmb][$key]."' \n";
                    $stSQL .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
                }
            }
        }

        $stSQL .= " GROUP BY sw_processo.ano_exercicio
                        , sw_processo.cod_processo
                        , sw_processo.timestamp
                        , sw_classificacao.nom_classificacao
                        , sw_assunto.nom_assunto
                        , sw_ultimo_andamento.cod_orgao                                                   ";

        Sessao::write('sSQLs',$stSQL);

        switch ($ordem) {
            case "1":
                $ordem = " sw_processo.ano_exercicio, sw_processo.cod_processo  ";
            break;
            case "2":
                $ordem = " sw_cgm.nom_cgm ";
            break;
            case "3":
                $ordem = " sw_classificacao.nom_classificacao, sw_assunto.nom_assunto, sw_processo.ano_exercicio, sw_processo.cod_processo ";
            break;
            case "4":
                $ordem = " sw_processo.timestamp ";
            break;
        }
        $vet["ordem"] = $ordem;

        Sessao::write('vet',$vet);
        Sessao::write('ordem',$ordem);

        include(CAM_FW_LEGADO."paginacaoLegada.class.php");
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($pagina);

        Sessao::write('pagina',$pagina);

        # Parâmetros utilizados na paginação da consulta para manter os filtros.
        $stComplemento  = "&ctrl=1";
        $stComplemento .= "&codProcessoFl=".$codProcessoFl;
        $stComplemento .= "&codClassificacao=".$codClassificacao;
        $stComplemento .= "&codAssunto=".$codAssunto;
        $stComplemento .= "&numCgm=".$numCgm;
        $stComplemento .= "&numCgmU=".$numCgmU;
        $stComplemento .= "&numCgmUltimo=".$numCgmUltimo;
        $stComplemento .= "&dataInicial=".$dataInicial;
        $stComplemento .= "&dataFinal=".$dataFinal;
        $stComplemento .= "&dataInicio=".$dataInicio;
        $stComplemento .= "&dataTermino=".$dataTermino;
        $stComplemento .= "&ordem=".$ordem;

        $paginacao->complemento = $stComplemento;
        $paginacao->geraLinks();
        $paginacao->pegaOrder($ordem,"ASC");
        $sSQL = $paginacao->geraSQL();
        $count = $paginacao->contador();
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $exec = "";
        $exec .= 	"<table width='100%' id='processos'>
                        <tr>
                            <td class=alt_dados colspan='11'>
                                Registros de processos
                            </td>
                        </tr>
                        <tr>
                            <td class='labelcenterCabecalho' style='vertical-align: middle;'>&nbsp;</td>
                            <td class='labelcenterCabecalho' style='vertical-align: middle;'>Código</td>
                            <td class='labelcenterCabecalho' style='vertical-align: middle;'>Interessado</td>
                            <td class='labelcenterCabecalho' style='vertical-align: middle;'>Classificação</td>
                            <td class='labelcenterCabecalho' style='vertical-align: middle;'>Assunto</td>
                            <td class='labelcenterCabecalho' style='vertical-align: middle;'>Inclusão</td>
                            <td class='labelcenterCabecalho' colspan='2'>&nbsp;</td>
                        </tr>";
        $dbEmp2 = new dataBaseLegado;
        $dbEmp2->abreBD();
        while (!$dbEmp->eof()) {
            $codProcesso     = $dbEmp->pegaCampo("codProcesso");
            $anoEx           = $dbEmp->pegaCampo("exercicio");
            $classificacao   = $dbEmp->pegaCampo("nomClassificacao");
            $assunto         = $dbEmp->pegaCampo("nomAssunto");
            $interessado     = $dbEmp->pegaCampo("nomInteressado");
            $timestamp       = $dbEmp->pegaCampo("timestamp");
            $date 			 = timestamptobr($timestamp);
            $codOrgao        = trim($dbEmp->pegaCampo("cod_orgao"));

            $stSQLOrgao = "
                    SELECT
                            (
                              SELECT  publico.fn_mascarareduzida(organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao_nivel.cod_orgao))
                                FROM  organograma.orgao_nivel
                               WHERE  orgao_nivel.cod_orgao = orgao_descricao.cod_orgao
                               LIMIT 1
                            ) AS orgao_reduzido
                         ,  orgao_descricao.descricao

                      FROM  organograma.orgao_descricao

                INNER JOIN  organograma.orgao_nivel
                        ON  orgao_nivel.cod_orgao = orgao_descricao.cod_orgao

                     WHERE  orgao_descricao.cod_orgao = ".$codOrgao."
                     LIMIT  1";

            $dbEmpOrgao = new dataBaseLegado;
            $dbEmpOrgao->abreBD();
               $dbEmpOrgao->abreSelecao($stSQLOrgao);
            $dbEmpOrgao->vaiPrimeiro();
            $nomOrgao      = $dbEmpOrgao->pegaCampo("descricao");
            $orgaoReduzido = $dbEmpOrgao->pegaCampo("orgao_reduzido");
            $dbEmpOrgao->limpaSelecao();

            $chave = $anoEx."-".$codProcesso;
            $dbEmp->vaiProximo();

            $codProcessoMascara = mascaraProcesso($codProcesso, $anoEx);
            $exec .= "
                 <tr>
                 <td class='show_dados_center_bold'>
                    ".$count++."
                 </td>
                <td class='show_dados'>
                    ".$codProcessoMascara."
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
                     ".$date."
                </td>
                <td class='botao'><div align='center' title='Consultar processo'>
                    <a href='consultaProcesso.php?codProcessoFl=".$codProcessoFl."&codClassificacao=".$codClassificacao."&codAssunto=".$codAssunto."&numCgm=".$numCgm."&numCgmU=".$numCgmU."&numCgmUltimo=".$numCgmUltimo."&dataInicial=".$dataInicial."&dataFinal=".$dataFinal."&ordem=".$ordem.
                    Sessao::getId()."&codProcesso=".
                    $codProcesso."&anoExercicio=".
                    $anoEx."&controle=0&ctrl=2&pagina=".
                    $pagina."&verificador=true&stChaveProcesso=".$stChaveProcesso."&dataInicial=".$dataInicio."&dataFinal=".$dataTermino."'>
                    <img src='".CAM_FW_IMAGENS."procuracgm.gif' alt='' border=0>
                    </a></div>
                </td>
                <td class='botao'>
                    <a href=\"javascript:alertaQuestao('".CAM_PROTOCOLO."protocolo/processos/cancelaEncaminhamento.php?chave=".$chave."*_*ctrl=2*_*stDescQuestao=".urlencode('Deseja cancelar o trâmite do processo '.$codProcessoMascara.' para o Setor '.$orgaoReduzido.' - '.$nomOrgao )."*_*".Sessao::getId()."','sn','".Sessao::getId()."');\">
                        <img src='".CAM_FW_IMAGENS."botao_cancela_encaminhamento.png' border=0>
                    </a>
                </td>
            </tr>\n";

        }

        $exec .= "</table>";

        if ($dbEmp->numeroDeLinhas == 0) {
            $exec .=  "<b>Não Existem Processos Encaminhados e não Recebidos!</b>";
        }

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBd();
        $dbEmp2->fechaBd();
        echo "$exec";
        echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
?>
    <script>zebra('processos','zb');</script>
<?php
    break;

    case 2:

        $variaveis = explode("-",$_REQUEST["chave"]);
        $anoE = $variaveis[0];
        $codProcesso = $variaveis[1];
        include '../andamento.class.php';
        $andamento = new andamento;
        $andamento->codProcesso = $codProcesso;
        $andamento->anoE = $anoE;
        $andamento->codAndamento = $andamento->ultimoAndamento();
        $pagina = Sessao::read('pagina');
        $ordem  = Sessao::read('ordem');

        if ($andamento->cancelaEncaminhamento()) {
            include(CAM_FW_LEGADO."auditoriaLegada.class.php");
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $codProcesso);
            $audicao->insereAuditoria();
            echo '<script type="text/javascript">
            alertaAviso("Encaminhamento para Processo cod. '.$codProcesso.' cancelado com sucesso","unica","aviso", "'.Sessao::getId().'");
            window.location = "cancelaEncaminhamento.php?'.Sessao::getId().'&ctrl=1&pagina='.$pagina.'&codProcessoFl='.$codProcessoFl.'&codClassificacao='.$codClassificacao.'&codAssunto='.$codAssunto.'&numCgm='.$numCgm.'&numCgmU='.$numCgmU.'&numCgmUltimo='.$numCgmUltimo.'&dataInicial='.$dataInicial.'&dataFinal='.$dataFinal.'&ordem='.$ordem.'";
            </script>';
        } else {
            echo '<script type="text/javascript">
            alertaAviso("Encaminhamento para Processo cod. '.$codProcesso.'","n_alterar","aviso", "'.Sessao::getId().'");
            window.location = "cancelaEncaminhamento.php?'.Sessao::getId().'&ctrl=1&pagina='.$pagina.'&codProcessoFl='.$codProcessoFl.'&codClassificacao='.$codClassificacao.'&codAssunto='.$codAssunto.'&numCgm='.$numCgm.'&numCgmU='.$numCgmU.'&numCgmUltimo='.$numCgmUltimo.'&dataInicial='.$dataInicial.'&dataFinal='.$dataFinal.'&ordem='.$ordem.'";
            </script>';
        }

    break;

    case 100:
        include '../../../framework/legado/filtrosCASELegado.inc.php';
    break;
}

    include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
