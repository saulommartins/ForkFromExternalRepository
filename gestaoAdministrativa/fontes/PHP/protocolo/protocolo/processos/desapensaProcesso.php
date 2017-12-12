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

    * $Id: desapensaProcesso.php 66029 2016-07-08 20:55:48Z carlos.silva $

    Casos de uso: uc-01.06.98

    */

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_FW_LEGADO."funcoesLegado.lib.php";
include CAM_FW_LEGADO."mascarasLegado.lib.php";
include CAM_FW_LEGADO."auditoriaLegada.class.php";
include '../apensamento.class.php';
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
</script>
<?php

setAjuda('uc-01.06.98');

    if ($_REQUEST['controle'] == 1 || $_REQUEST['ctrl'] == 1) {
        $arFiltro = Sessao::read("filtro");
        if (count($arFiltro) > 0) {
            $_REQUEST = $arFiltro;
        } else {
            foreach ($_REQUEST as $stChave => $stValor) {
                $arFiltro[$stChave] = $stValor;
            }
        }
        Sessao::write("filtro", $arFiltro);
    }

    $mascaraProcesso = pegaConfiguracao("mascara_processo",5);
    $verificador = $_REQUEST["verificador"];
    $controle    = $_REQUEST["controle"];
    $pagina      = $_REQUEST["pagina"];
    $codProcesso      = $_REQUEST["codProcesso"];
    $ExeProcesso      = $_REQUEST["ExeProcesso"];
    $flag      = $_REQUEST["flag"];
    $aDesapensamentos      = $_REQUEST["aDesapensamentos"];

    if (Sessao::read('controle') != '') {
        $controle = 0;
    Sessao::write('pagina','');
        $flag = 0;
    }

    if ($verificador) {
        $controle = 1;
    }

    if ($ctrl == 2) {
       $controle = 1;
    }

    if (isset($pagina)) {
        Sessao::write('pagina',$pagina);
    }

    switch ($controle) {
        case 0:
        /*****************************************************************
        *   Tela de Filtros                                              *
        *****************************************************************/
            echo '
            <script type="text/javascript">
                function Salvar()
                {
                    document.frm.action = "desapensaProcesso.php?'.Sessao::getId().'&controle=1";
                    document.frm.submit();
                }
            </script>';

            include '../../../framework/legado/filtrosProcessoLegado.inc.php';
            break;
        case 1:
        /*****************************************************************
        *   Lista de processos que terão processos apensados             *
        *****************************************************************/

            if (Sessao::read('vet') != "") {
                $vet = Sessao::read('vet');
                foreach ($vet AS $indice => $valor) {
                    $$indice = $valor;
                }
            }

    $inCodOrganograma = SistemaLegado::pegaDado('cod_organograma', 'organograma.vw_orgao_nivel', ' WHERE cod_orgao = '.Sessao::read('codOrgao'));
    $boPermissaoHierarquica = SistemaLegado::pegaDado('permissao_hierarquica', 'organograma.organograma', ' WHERE cod_organograma = '.$inCodOrganograma);

        $sql  = "";
        $sql  = "
                SELECT DISTINCT sw_processo.ano_exercicio
                     , sw_processo.cod_processo
                     , sw_processo.timestamp
                     , sw_ultimo_andamento.cod_andamento
                     , sw_classificacao.nom_classificacao
                     , sw_assunto.nom_assunto
                     , array_to_string(array_agg(nom_cgm), ', ')as nom_cgm
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

                       , (SELECT cod_processo_pai
                             , exercicio_pai
                      FROM sw_processo_apensado
                     WHERE timestamp_desapensamento IS NULL
                         GROUP BY cod_processo_pai
                            , exercicio_pai) AS processo_pai

                     WHERE  processo_pai.exercicio_pai         = sw_processo.ano_exercicio
                       AND processo_pai.cod_processo_pai      = sw_processo.cod_processo
                       AND  sw_ultimo_andamento.cod_orgao IN (  SELECT cod_orgao
                                                                  FROM organograma.vw_orgao_nivel
                                                                 WHERE orgao_reduzido LIKE (
                                                                                        SELECT distinct(vw_orgao_nivel.orgao_reduzido)
                                                                                          FROM organograma.vw_orgao_nivel
                                                                                         WHERE vw_orgao_nivel.cod_orgao = ".Sessao::read('codOrgao')."
                                                                                       )";
                                                         # Permissão hierárquica define se o usuário pode ver processos de órgãos em níveis menores ou somente do seu nível.
                                                         $sql .= ($boPermissaoHierarquica == 't') ? "||'%'" : "";
                                                         $sql .= " GROUP BY cod_orgao) ";

    $stChaveProcesso = $_REQUEST["stChaveProcesso"];
    if ($stChaveProcesso != "") {
        $codProcessoFl = preg_split( "/[^a-zA-Z0-9]/", $stChaveProcesso);
        $sql .= " AND sw_processo.cod_processo  = ".(int) $codProcessoFl[0];
        $vet["stChaveProcesso"] = $stChaveProcesso;
    }
    if ($codProcessoFl[1] != "") {
        $sql .= " AND sw_processo.ano_exercicio = '".$codProcessoFl[1]."' ";
        $vet["anoExercicio"]  = $codProcessoFl[1];
    }

    $codClassificacao = $_REQUEST["codClassificacao"];
    if ($codClassificacao != "" && $codClassificacao != "xxx") {
        $sql .= " AND sw_processo.cod_classificacao = ".$codClassificacao;
        $vet["codClassificacao"] = $codClassificacao;
    }

    $codAssunto = $_REQUEST["codAssunto"];
    if ($codAssunto != "" && $codAssunto != "xxx") {
        $sql .= " AND sw_processo.cod_assunto = ".$codAssunto;
                $vet["codAssunto"] = $codAssunto;
    }

    $numCgm = $_REQUEST["numCgm"];
    if ($numCgm != "") {
        $sql .= " AND sw_processo_interessado.numcgm = ".$numCgm;
                $vet["numCgm"] = $numCgm;
    }

        //FILTRO PRO ASSUNTO REDUZIDO
    $resumo = $_REQUEST["resumo"];
    if ($resumo != "") {
        $stSQL .= " AND sw_processo.resumo_assunto ILIKE '".$resumo."%' ";
                $vet["resumo"] = $resumo;
    }

        //FILTRO POR PERIODO
    $dataInicio = $_REQUEST["dataInicio"];
    $dataTermino = $_REQUEST["dataTermino"];
    if ($dataInicio != "" && $dataTermino != "" && $dataInicio != $dataTermino) {
        $vet["dataInicio"] = $dataInicio;
        $vet["dataTermino"]   = $dataTermino;
        $arrData     = explode("/", $dataInicio);
        $dataInicio = $arrData[2]."-".$arrData[1]."-".$arrData[0];
        $arrData     = explode("/", $dataTermino);
        $dataTermino   = $arrData[2]."-".$arrData[1]."-".$arrData[0];
        $sql .= " AND TO_DATE(sw_processo.timestamp::varchar, 'yyyy-mm-dd') ";
        $sql .= " BETWEEN '".$dataInicio."' AND '".$dataTermino."' ";
    } elseif ( $dataInicio != "" && $dataTermino == "" or ( $dataInicio != "" && $dataInicio == $dataTermino ) ) {
        $vet["dataInicio"] = $dataInicio;
        $vet["dataTermino"]   = $dataTermino;
        $arrData     = explode("/", $dataInicio);
        $dataInicio = $arrData[2]."-".$arrData[1]."-".$arrData[0];
        $arrData     = explode("/", $dataTermino);
        $dataTermino   = $arrData[2]."-".$arrData[1]."-".$arrData[0];
        $sql .= " AND TO_DATE(sw_processo.timestamp::varchar, 'yyyy-mm-dd') = '".$dataInicio."'";
    }

    //FILTRO POR ATRIBUTO DE ASSUNTO
    if ($_REQUEST[valorAtributoTxt]) {
        foreach ($_REQUEST[valorAtributoTxt] as $key => $value) {
            if ($_REQUEST[valorAtributoTxt][$key]) {
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

    Sessao::write('sSQLs',$sql);
    //sessao->transf = $sql;

    $st_ordenacao = array(
         1 => "sw_processo.ano_exercicio
             , sw_processo.cod_processo",
         2 => "sw_cgm.nom_cgm",
         3 => "sw_classificacao.nom_classificacao
             , sw_assunto.nom_assunto
             , sw_processo.ano_exercicio
             , sw_processo.cod_processo",
         4 => "sw_processo.timestamp");

     Sessao::write('vet',$vet);

$ordem = $_REQUEST["ordem"];
if (Sessao::read('ordem') =='') {
    Sessao::write('ordem',$ordem);
}
    if ($ordem=='') {
        Sessao::write('sSQLs',$sql);
    }

    include '../../../framework/legado/paginacaoLegada.class.php';
    $paginacao = new paginacaoLegada;
    $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
    $paginacao->pegaPagina(Sessao::read('pagina'));
    $paginacao->complemento = "&controle=1&codProcessoFl=".
    $codProcessoFl."&codClassificacao=".
    $codClassificacao."&codAssunto=".
    $codAssunto."&numCgm=".
    $numCgm."&dataInicial=".
    $dataInicial."&dataFinal=".
    $dataFinal."&ordem=".
    $ordem;
    $paginacao->geraLinks();
    $paginacao->pegaOrder($st_ordenacao[Sessao::read('ordem')],"ASC");
    $sSQL = $paginacao->geraSQL();
    $count = $paginacao->contador();
    //print $sSQL;
    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();
    $exec .= "
    <table width='100%' id='processos'>
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
            <td class='labelcenterCabecalho' >&nbsp;</td>
            <td class='labelcenterCabecalho' >&nbsp;</td>
        </tr>
    ";
    if ($dbEmp->eof()) {
        if ($vet["codProcessoFl"] && $vet["anoExercicio"]) {
            $stMensagem = " Não existem processos apensados a ".$vet["codProcessoFl"].'/'.$vet["anoExercicio"];
        } else {
            $stMensagem = " Não existem processos apensados para o filtro informado!";
        }
        $exec .= "<tr><td class='show_dados_center' colspan=10>".$stMensagem."</td></tr>";
    }
    while (!$dbEmp->eof()) {
        $codProcesso      = $dbEmp->pegaCampo("cod_processo");
        $anoEx            = $dbEmp->pegaCampo("ano_exercicio");
        $classificacao    = $dbEmp->pegaCampo("nom_classificacao");
        $assunto          = $dbEmp->pegaCampo("nom_assunto");
        $interessado      = $dbEmp->pegaCampo("nom_cgm");
        $codAndamento     = $dbEmp->pegaCampo("cod_andamento");
        $timestamp        = $dbEmp->pegaCampo("timestamp");

        $date = timestamptobr($timestamp);

        $chave = $codProcesso."-".$anoEx."-".$codClassificacao."-".$codAssunto;
        $dbEmp->vaiProximo();

        $codProcessoMascara = mascaraProcesso($codProcesso,$anoEx);

        $exec .= "
         <tr>
         <td class='labelcenterTable'>
            ".$count++."
         </td>
        <td class='labelcenterTable'>
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

        <td class='show_dados'><div align='center' title='Consultar processo'><a
            href='consultaProcesso.php?".Sessao::getId().
            "&codProcesso=".$codProcesso.
            "&anoExercicio=".$anoEx.
            "&controle=0&ctrl=2&pagina=".$pagina."&verificador=true'><img
            src='".CAM_FW_IMAGENS."procuracgm.gif' alt='' border=0></a></div>
        </td>

        <td class='show_dados' title='Desapensar processo'><a
            href='javascript:mudaTelaPrincipal(\"desapensaProcesso.php?".Sessao::getId().
            "&codProcesso=".         $chave.
            "&ExeProcesso=".         $anoEx.
            "&controle=2&pagina=".   $pagina.
            "&flag=0&codProcessoFl=".$codProcessoFl.
            "&codClassificacao=".    $codClassificacao.
            "&codAssunto=".          $codAssunto.
            "&numCgm=".              $numCgm.
            "&numCgmU=".             $numCgmU.
            "&numCgmUltimo=".        $numCgmUltimo.
            "&dataInicial=".         $dataInicial.
            "&dataFinal=".           $dataFinal.
            "&ordem=".               $ordem."\");'><img
            src='".CAM_FW_IMAGENS."btnselecionar.png' border=0></a></td>
        </tr>";
    }
    $exec .= "</table>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo $exec;
    echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
    $paginacao->mostraLinks();
    echo "</font></tr></td></table>";

    break;

    case 2:
    /*****************************************************************
    *   Lista de processos desapensados de um processo                  *
    *****************************************************************/
        echo '
        <script type="text/javascript">
            function Valida()
            {
                var numElementos = document.frm.elements.length;
                var boChecado = false;
                for (var i =0; i < numElementos; i++) {
                    if (document.frm.elements[i].type == "checkbox" && document.frm.elements[i].checked == true) {
                        boChecado = true;
                    }
                }
                if (!boChecado) {
                    alertaAviso("É necessário selecionar pelo menos um  processo!","unica","erro","'.Sessao::getId().'");
                }

                return boChecado;
            }

            function Salvar()
            {
                if ( Valida() ) {
                    document.frm.action = "desapensaProcesso.php?'.Sessao::getId().'&controle=3";
                    document.frm.submit();
                }
            }

        function Cancela()
        {
            document.frm.target = "telaPrincipal";
            document.frm.controle.value = 1;
            document.frm.action = "desapensaProcesso.php?'.Sessao::getId().'";
            document.frm.submit();
        }
        </script>';
        if (Sessao::read('vet') != "") {
            $vet = Sessao::read('vet');
            foreach ($vet AS $indice => $valor) {
                $$indice = $valor;
            }
        }
        $aProc = explode("-",$codProcesso);
        $codProcesso = $aProc[0];
        $ExeProcesso = $aProc[1];
            // Pega configuração para processo
            $mascaraProcesso = pegaConfiguracao('mascara_processo',5);
            $arCodProcesso =  validaMascaraDinamica($mascaraProcesso, $codProcesso."-".$ExeProcesso);
            $codProc   = $arCodProcesso[1];

    $sSQL = "
              SELECT
              --INFORMAÇÕES PARA A TELA
                  SW_PROCESSO.COD_PROCESSO,
                  SW_PROCESSO.ANO_EXERCICIO,
                  TO_CHAR( SW_PROCESSO.TIMESTAMP, 'DD/MM/YYYY' ) AS DT_PROCESSO,
                  SW_CGM.NOM_CGM,
                  SW_CLASSIFICACAO.NOM_CLASSIFICACAO,
                  SW_ASSUNTO.NOM_ASSUNTO,
                  SW_PROCESSO_APENSADO.TIMESTAMP_APENSAMENTO AS DT_APENSAMENTO
              FROM
                  SW_PROCESSO,
                  SW_PROCESSO_APENSADO,
                  SW_CGM,
                  SW_CLASSIFICACAO,
                  SW_ASSUNTO,
                  SW_PROCESSO_INTERESSADO
              WHERE
              --JOIN CLASSIFICACAO X ASSUNTO
                  SW_CLASSIFICACAO.COD_CLASSIFICACAO  = SW_ASSUNTO.COD_CLASSIFICACAO      AND
              --INFORMACOES DO PROCESSO
                  SW_PROCESSO.COD_PROCESSO    = SW_PROCESSO_APENSADO.COD_PROCESSO_FILHO   AND
                  SW_PROCESSO.ANO_EXERCICIO   = SW_PROCESSO_APENSADO.EXERCICIO_FILHO      AND
                  SW_PROCESSO_INTERESSADO.ANO_EXERCICIO = SW_PROCESSO.ANO_EXERCICIO       AND
                  SW_PROCESSO_INTERESSADO.COD_PROCESSO  = SW_PROCESSO.COD_PROCESSO        AND
                  SW_PROCESSO_INTERESSADO.NUMCGM  = SW_CGM.NUMCGM                         AND
                  SW_PROCESSO.COD_CLASSIFICACAO   = SW_ASSUNTO.COD_CLASSIFICACAO          AND
                  SW_PROCESSO.COD_ASSUNTO         = SW_ASSUNTO.COD_ASSUNTO                AND
              --REGRAS PARA BUSCAR OS PROCESSOS APENSADOS À UM PROCESSO
                  SW_PROCESSO_APENSADO.TIMESTAMP_DESAPENSAMENTO IS NULL                   AND
                  SW_PROCESSO_APENSADO.COD_PROCESSO_PAI   = ".$codProcesso ."             AND
                  SW_PROCESSO_APENSADO.EXERCICIO_PAI      = '".$ExeProcesso."'";

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $exec .= "
        <form name='frm' action='apensaProcesso.php?".Sessao::getId()."' method='POST' target='oculto'>
        <input type='hidden' name='controle' value='3'>
        <input type='hidden' name='codProcesso' value='".$codProcesso."-".$ExeProcesso."'>
        <table width='100%' >
            <tr>
                <td class=alt_dados colspan=2>
                    Dados do processo pai
                </td>
            </tr>
            <tr>
                <td class=label width='20%'>
                    Código
                </td>
                <td class=field width='80%'>
                    $codProc<br>
                </td>
            </tr>";

            $sql_class = "
                SELECT DISTINCT
                    P.cod_classificacao,
                    P.cod_assunto,
                    P.timestamp,
                    P.cod_usuario,
                    U.username,
                    C.nom_classificacao,
                    S.nom_assunto
                FROM
                    sw_processo      AS P,
                    sw_assunto       AS S,
                    sw_classificacao AS C,
                    administracao.usuario AS U
                WHERE
                    P.ANO_EXERCICIO = '".$ExeProcesso."' AND
                    P.COD_PROCESSO = '".$codProcesso."' AND
                    P.cod_classificacao = S.cod_classificacao AND
                    C.cod_classificacao = S.cod_classificacao AND
                    P.cod_assunto       = S.cod_assunto AND
                    P.cod_usuario       = U.numcgm";
            $dbClass = new dataBaseLegado;
            $dbClass->abreBD();
            $dbClass->abreSelecao($sql_class);
            $dbClass->vaiPrimeiro();

            $cod_classificacao = $dbClass->pegaCampo("cod_classificacao");
            $classificacao     = $dbClass->pegaCampo("nom_classificacao");
            $cod_assunto       = $dbClass->pegaCampo("cod_assunto");
            $assunto           = $dbClass->pegaCampo("nom_assunto");
            $dt_inclusao       = $dbClass->pegaCampo("timestamp");
            $username          = $dbClass->pegaCampo("username");
            $cod_usuario       = $dbClass->pegaCampo("cod_usuario");

            $arrData      = explode(" ", $dt_inclusao);
            $vet          = explode("-", $arrData[0]);
            $dt_inclusao = $vet[2]."/".$vet[1]."/".$vet[0];

            $mascaraAssunto = pegaConfiguracao('mascara_assunto',5);
            $arCodClassifAssunto =  validaMascaraDinamica($mascaraAssunto, $cod_classificacao."-".$cod_assunto);
            $codClassifAssunto   = $arCodClassifAssunto[1];

            $exec .= "


    <tr>
                <td class=label width='20%'>
                    Classificação / Assunto
                </td>
                <td class=field width='80%'>
                    $codClassifAssunto<br>
                    $classificacao<br>$assunto
                </td>
            </tr>
            </table>
            <table width='100%' id='processos'>
            <tr>
                <td class=alt_dados colspan='11'>
                    Processos apensados
                </td>
            </tr>
            <tr>
                <td class='labelcentercabecalho' style='vertical-align: middle;'>&nbsp;</td>
                <td class='labelcentercabecalho' style='vertical-align: middle;'>&nbsp;</td>
                <td class='labelcentercabecalho' style='vertical-align: middle;'>Código</td>
                <td class='labelcentercabecalho' style='vertical-align: middle;'>Interessado</td>
                <td class='labelcentercabecalho' style='vertical-align: middle;'>Classificação</td>
                <td class='labelcentercabecalho' style='vertical-align: middle;'>Assunto</td>
                <td class='labelcentercabecalho' style='vertical-align: middle;'>Inclusão</td>
            </tr>
        ";
        while (!$dbEmp->eof()) {

            $codProcFilho     = $dbEmp->pegaCampo("cod_processo");
            $anoExFilho       = $dbEmp->pegaCampo("ano_exercicio");
            $classificacao    = $dbEmp->pegaCampo("nom_classificacao");
            $assunto          = $dbEmp->pegaCampo("nom_assunto");
            $interessado      = $dbEmp->pegaCampo("nom_cgm");
            $date             = $dbEmp->pegaCampo("dt_processo");
            $dDataHoraApensa  = $dbEmp->pegaCampo("dt_apensamento");

            $chave = $codProcFilho."-".$anoExFilho."-".$codClassificacao."-".$codAssunto;
            $dbEmp->vaiProximo();

            $codProcessoMascara = mascaraProcesso($codProcFilho,$anoExFilho);

            $sValor = $codProcFilho."_".$anoExFilho."_".$dDataHoraApensa;
            $chequebox = '<input type="checkbox" name="aDesapensamentos[]" value="'.$sValor.'">';
            $count++;

            $codProcFilho = $codProcessoMascara;
            $exec .= "
            <tr>
                <td class='labelcenter'>".$count."</td>
                <td class='labelcenter'>".$chequebox."</td>
                <td class='show_dados_right'>".$codProcFilho."</td>
                <td class='show_dados'>".$interessado."</td>
                <td class='show_dados'>".$classificacao."</td>
                <td class='show_dados'>".$assunto."</td>
                <td class='show_dados_center'>".$date."</td>
            </tr>";
        }
       $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $exec;
        echo "
        <tr>
            <td class=field colspan='20'>";
        geraBotaoOk(1,1,0,1);
        echo    "</td>
        </tr>
        </table>
        </form>";
    break;
    case 3:
        /*****************************************************************
        *   Salvar desapensamentos                                          *
        *****************************************************************/
            $oApensamento = new apensamento;
            if ($oApensamento->incluiDesapensamentos($codProcesso, $aDesapensamentos)) {
                //Insere auditoria
                if (is_array($aDesapensamentos)) {
                    $sDesapensamentos = "";
                    while (list($vKey,$vValor) = each($aDesapensamentos)) {
                        $aCodDesa   = explode('_',$vValor);
                        $aDesaAux[] = $aCodDesa[0]."_".$aCodDesa[1];
                    }
                    $sDesapensados  = implode(", ", $aDesaAux);
                    $sDesapensados  = str_replace("_","/",$sDesapensados);
                    $aCodProc    = explode('-',$codProcesso);
                    $codProcesso = $aCodProc[0].'_'.$aCodProc[1];
                    $codProcesso = str_replace("_","/",$codProcesso);
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'),
                                            substr($sDesapensados.' desapens. de '.$codProcesso, 0, 100));
                    $audicao->insereAuditoria();
                    alertaAviso($PHP_SELF."?".Sessao::getId()."&pagina=".$pagina.
                                "&controle=1&codProcessoFl=".$codProcessoFl."&codClassificacao=".$codClassificacao.
                                "&codAssunto=".$codAssunto."&numCgm=".$numCgm."&numCgmU=".$numCgmU.
                                "&numCgmUltimo=".$numCgmUltimo."&dataInicial=".$dataInicial."&dataFinal=".$dataFinal.
                                "&ordem=".$ordem,'Processos '.$sDesapensados.
                                " desapensados do processo ".$codProcesso."!","historico","aviso", "'.Sessao::getId().'");
                }
            }
            break;
            case 4:

                $aux = validaSetor($chaveSetor, $anoExSetor);
                $js = "";
                $js .= "f.nomSetor.value = '".$aux["nomSetor"]."';\n";

                executaFrameOculto($js);

            break;
            case 100:
                include '../../../framework/legado/filtrosCASELegado.inc.php';
            break;
    }
    include '../../../framework/include/rodape.inc.php';
?>
<script>zebra('processos','zb');</script>
