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

    $Id: encaminhaProcessoLote.php 63829 2015-10-22 12:06:07Z franver $

    */

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_FW_LEGADO."funcoesLegado.lib.php";
include CAM_FW_LEGADO."processosLegado.class.php";  # Insere a classe que manipula os dados do processo
include CAM_FW_LEGADO."auditoriaLegada.class.php";  # Inclui classe para inserir auditoria
include 'interfaceProcessos.class.php'; 			# Inclui classe que contém a interface html
include CAM_FW_LEGADO."mascarasLegado.lib.php";

setAjuda('uc-01.06.98');

$mascaraProcesso = pegaConfiguracao("mascara_processo", 5);
$anoExercicio    = Sessao::read("anoExercicio");

$pagina           = $_REQUEST["pagina"];
$codProcesso      = $_REQUEST["codProcesso"];
$numCgm           = $_REQUEST['numCgm'];
$stChaveProcesso  = $_REQUEST['stChaveProcesso'];
$dataInicio       = $_REQUEST["dataInicio"];
$dataTermino      = $_REQUEST["dataTermino"];
$resumo           = $_REQUEST["resumo"];
$codClassificacao = $_REQUEST["codClassificacao"];
$codAssunto       = $_REQUEST["codAssunto"];

if (!isset($_REQUEST["controle"])) {
        $controle = 0;
    $flag = 0;
} else {
    $controle = $_REQUEST["controle"];
}

if ($_REQUEST["verificador"]) {
    $controle = 1;
}

$ctrl = $_REQUEST["ctrl"];
if ($ctrl == 2) {
    $controle = 1;
}

$pagina = $_REQUEST["pagina"];
if (isset($pagina)) {
    Sessao::write('pagina',$pagina);
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
        document.frm.action = "encaminhaProcessoLote.php?<?=Sessao::getId()?>&controle=1&codProcesso=<?=$codProcesso;?>&numCgm=<?=$numCgm;?>&stChaveProcesso=<?=$stChaveProcesso;?>&codAssunto=<?=$codAssunto;?>&codClassificacao=<?=$codClassificacao;?>&resumo=<?=$resumo;?>&dataInicio=<?=$dataInicio;?>&dataTermino=<?=$dataTermino;?>";
        document.frm.submit();
    }

    function encaminharProcessos()
    {
        document.frm.action = "encaminhaProcessoLote.php?<?=Sessao::getId()?>&controle=2&codProcesso=<?=$codProcesso;?>&numCgm=<?=$numCgm;?>&stChaveProcesso=<?=$stChaveProcesso;?>&codAssunto=<?=$codAssunto;?>&codClassificacao=<?=$codClassificacao;?>&resumo=<?=$resumo;?>&dataInicio=<?=$dataInicio;?>&dataTermino=<?=$dataTermino;?>";
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
    include '../../../framework/legado/filtrosProcessoLegado.inc.php';
break;

case 1:
if (Sessao::read('vet') != "") {
    $vet = Sessao::read('vet');
    foreach ($vet AS $indice => $valor) {
        $$indice = $valor;
    }
}

    $inCodOrganograma = SistemaLegado::pegaDado('cod_organograma', 'organograma.vw_orgao_nivel', ' WHERE cod_orgao = '.Sessao::read('codOrgao'));
    $boPermissaoHierarquica = SistemaLegado::pegaDado('permissao_hierarquica', 'organograma.organograma', ' WHERE cod_organograma = '.$inCodOrganograma);

       $sql = "
                 SELECT
                       DISTINCT sw_processo.ano_exercicio
                       , sw_processo.cod_processo
                       , sw_processo.timestamp
                       , sw_processo.cod_classificacao
                       , sw_processo.cod_assunto
                       , sw_ultimo_andamento.cod_andamento
                       , sw_classificacao.nom_classificacao
                       , sw_assunto.nom_assunto
                       ,  array_to_string(array_agg(nom_cgm), ', ')as nom_cgm
                       , ( EXISTS ( SELECT 1 FROM SW_DESPACHO WHERE COD_PROCESSO = sw_processo.cod_processo AND ANO_EXERCICIO = sw_processo.ano_exercicio ) ) as despacho
                       , ( EXISTS ( SELECT 1 FROM SW_PROCESSO_APENSADO WHERE COD_PROCESSO_PAI = sw_processo.cod_processo AND EXERCICIO_PAI = sw_processo.ano_exercicio AND TIMESTAMP_DESAPENSAMENTO IS NULL ) ) as apenso

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

                WHERE  sw_situacao_processo.cod_situacao = 3

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

        if ($stChaveProcesso != "") {
            $codProcessoFl = preg_split( "/[^a-zA-Z0-9]/", $stChaveProcesso);
            if ( (int) $codProcessoFl[0] ) {
              $sql .= " AND sw_processo.cod_processo  = ".(int) $codProcessoFl[0];
            }
            $vet["stChaveProcesso"] = $stChaveProcesso;
        }
        if ($codProcessoFl[1] != "") {
            $sql .= " AND sw_processo.ano_exercicio = '".$codProcessoFl[1]."' ";
            $vet["anoExercicio"]  = $codProcessoFl[1];
        }

        if (!isset($codClassificacao_base)) {
                $codClassificacao_base = $codClassificacao;
            $codAssunto_base=$codAssunto;
        }

        if (isset($codClassificacao_base)) {
                $codClassificacao = $codClassificacao_base;
            $codAssunto=$codAssunto_base;
        }

        if ($codClassificacao != "" && $codClassificacao != "xxx") {
            $sql .= " AND sw_processo.cod_classificacao = ".$codClassificacao;
                    $vet["codClassificacao"] = $codClassificacao;
        }

        if ($codAssunto != "" && $codAssunto != "xxx") {
            $sql .= " AND sw_processo.cod_assunto = ".$codAssunto;
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
            $vet["dataInicio"] = $dataInicio;
                    $vet["dataTermino"]   = $dataTermino;
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
            Sessao::write('vet',$vet);

            $st_ordenacao = array(
                 1 => "sw_processo.ano_exercicio
                     , sw_processo.cod_processo",
                 2 => "sw_cgm.nom_cgm",
                 3 => "sw_classificacao.nom_classificacao
                     , sw_assunto.nom_assunto
                     , sw_processo.ano_exercicio
                     , sw_processo.cod_processo",
                 4 => "sw_processo.timestamp");

if (Sessao::read('ordem') =='') {
    Sessao::write('ordem',$_REQUEST["ordem"]);
}
    if ($_REQUEST["ordem"]=='') {
        Sessao::write('sSQLs',$sql);
    }

    if ($_REQUEST['minProcesso']) {
        $arProcessos = Sessao::read('arProcessos');
        $stChaveProcessoCkb = $_REQUEST['stChaveProcessoCkb'];
        for ($inContador = $_REQUEST['minProcesso'] ; $inContador <= $_REQUEST['maxProcesso']; $inContador++) {
            if ($stChaveProcessoCkb[$inContador]) {
                $arProcesso[$inContador] = $stChaveProcessoCkb[$inContador];
            } else {
                unset( $arProcessos );
            }
        }
        Sessao::write('arProcessos',$arProcessos);
    }

        include(CAM_FW_LEGADO."paginacaoLegada.class.php");
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina(Sessao::read('pagina'));
        $paginacao->complemento = "";
        $paginacao->geraLinksFuncao( 'enviaProcesso' );
        $paginacao->pegaOrder($st_ordenacao[Sessao::read('ordem')],"ASC");
        $sSQL = $paginacao->geraSQL();
        $count = $paginacao->contador();
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();

        //DIEGO - VERIFICANDO...
        if ( $dbEmp->eof() ) {
            Sessao::write('pagina',Sessao::read('pagina')-1);
            //sessao->transf4 = //sessao->transf4 - 1;
            $pagina = Sessao::read('pagina');

            $paginacao = new paginacaoLegada;
            $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
            $paginacao->pegaPagina(Sessao::read('pagina'));
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
        <form action='".$PHP_SELF."?".Sessao::getId()."' name='frm' method='post' onsubmit='return false'>
        <input type='hidden' name='minProcesso' value='$count'>
        <input type='hidden' name='maxProcesso' value='".($count + 9)."'>
        <table width='100%' id='processos'>
            <tr>
                <td class=alt_dados colspan='11'>
                    Registros de processos
                </td>
            </tr>
            <tr>
                <td class='labelcenterCabecalho'  style='vertical-align: middle;'>&nbsp;</td>
                <td class='labelcenterCabecalho'  style='vertical-align: middle;'>Código</td>
                <td class='labelcenterCabecalho'  style='vertical-align: middle;'>Interessado</td>
                <td class='labelcenterCabecalho'  style='vertical-align: middle;'>Classificação</td>
                <td class='labelcenterCabecalho'  style='vertical-align: middle;'>Assunto</td>
                <td class='labelcenterCabecalho'  style='vertical-align: middle;'>Inclusão</td>
                <td class='labelcenterCabecalho'  style='vertical-align: middle;'>Despacho</td>
                <td class='labelcenterCabecalho'  style='vertical-align: middle;'>Apenso</td>
                <td class='labelcenterCabecalho'  >&nbsp;</td>
            </tr>
        ";

        while (!$dbEmp->eof()) {

                $codProcesso      = $dbEmp->pegaCampo("cod_processo");
                $anoEx            = $dbEmp->pegaCampo("ano_exercicio");
                $classificacao    = $dbEmp->pegaCampo("nom_classificacao");
                $assunto          = $dbEmp->pegaCampo("nom_assunto");
                $interessado      = $dbEmp->pegaCampo("nom_cgm");
                $codAndamento     = $dbEmp->pegaCampo("cod_andamento");
                $timestamp        = $dbEmp->pegaCampo("timestamp");
                $codClassificacao = $dbEmp->pegaCampo("cod_classificacao");
                $codAssunto       = $dbEmp->pegaCampo("cod_assunto");
                $date = timestamptobr($timestamp);
                $stDespacho  = $dbEmp->pegaCampo("despacho") == "t" ? "Sim" : "Não";
                $stApenso    = $dbEmp->pegaCampo("apenso") == "t" ? "Sim" : "Não";
                $chave = $codProcesso."-".$anoEx."-".$codClassificacao."-".$codAssunto;
                $dbEmp->vaiProximo();
                $codProcessoMascara = mascaraProcesso($codProcesso,$anoEx);
                $arProcessos = Sessao::read('arProcessos');
                $exec .= "
                 <tr>
                 <td class='show_dados_center_bold'>
                    ".$count++."
                 </td>
                <td class='show_dados_right'>
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
                <td class='show_dados_center'>
                     ".$date."
                </td>
                <td class='show_dados_center'>
                    ".$stDespacho."
                </td>
                <td class='show_dados_center'>
                    ".$stApenso."
                </td>
                <td class='show_dados'>";
                if ($arProcessos[$count-1]) {
                    $stChecked = " checked";
                } else {
                    $stChecked = "";
                }

                $exec .="   <input type='checkbox' value='$chave' name='stChaveProcessoCkb[".($count- 1)."]'".$stChecked.">
                </td>";
            $exec .= "
            </tr>";

        }

        if ($dbEmp->numeroDeLinhas <= 0) {
            $exec .= "</table>";
            $exec .=  "<b>Não Existem Processos a Encaminhar!</b>";
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

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $exec;
        echo "</form><table id= 'paginacao' width=450 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
        if ($dbEmp->numeroDeLinhas >= 1) {
?>
        <table width='100%'>
            <tr>
                <td colspan='2' class='field'>
                    &nbsp;<input type="button" value="Enviar" style='width: 60px;' onClick="encaminharProcessos();">
                </td>
            </tr>
        </table>
<?php
        } ?>
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
    if(is_array($stChaveProcessoCkb))
        $stChaveProcessoCkb = array_unique($stChaveProcessoCkb);

    if ($_REQUEST['minProcesso']) {
        $arProcessos = Sessao::read('arProcessos');
        for ($inContador = $_REQUEST['minProcesso'] ; $inContador <= $_REQUEST['maxProcesso']; $inContador++) {
            if ($stChaveProcessoCkb[$inContador]) {
                $arProcessos[$inContador] = $stChaveProcessoCkb[$inContador];
            } else {
                unset( $arProcesso[$inContador] );
            }
        }
    }

    Sessao::write('arProcessos', $arProcessos);

    if ( count(Sessao::read('arProcessos')) > 0 ) {
            $vet = explode("-",$codProcesso);
            $codP = $vet[0];
            $anoEx = $vet[1];
            $codClassif = $vet[2];
            $codAssunto = $vet[3];
        $html = new interfaceProcessos;
        $html->formEncaminhaProcessoLote($codP, $anoEx);
    } else {
        alertaAviso($PHP_SELF."?".Sessao::getId()."&pagina=".$pagina."&controle=1","Nenhum processo selecionado","historico","aviso");
    }
    break;

    case 3:

        $orgao = (int) $_REQUEST['codOrgao'];

        $processos = new processosLegado;
        //Executa o encaminhamento do processo
        if ($processos->encaminhaProcessoLote(Sessao::read('arProcessos'), $orgao, Sessao::read('numCgm'))) {
            //Insere auditoria
            $stAuditoria = "";
            $arAuditoria =  array();
            //reset( //sessao->transf6 );
            $arProcessos = Sessao::read('arProcessos');
            foreach ($arProcessos as $stProcessos) {
                $arProcesso = explode( "-", $stProcessos );
                $codProcesso  = (int) $arProcesso[0];
                $anoExercicio = $arProcesso[1];
                # SALVA UMA NOVA AUDITORIA QUANTO A STRING DOS PROCESSO ULTRAPASSAR 500 CARACTERES
                # ESTE CONTROLE É FEITO PROQUE O CAMPO DA AUDITORIA É UM VARCHAR DE 500 POSIÇÕES
                if ( strlen( $stAuditoria.$arProcesso[0].'_'.$arProcesso[1]."-" ) >= 500 ) {
                    $stAuditoria = substr( $stAuditoria, 0, strlen($stAuditoria) - 1 );
                    $arAuditoria[] = $stAuditoria;
                    $stAuditoria = "";
                }
                $stAuditoria .= $arProcesso[0].'_'.$arProcesso[1]."-";
            }
            $stAuditoria = substr( $stAuditoria, 0, strlen($stAuditoria) - 1 );
            $arAuditoria[] = $stAuditoria;
            Sessao::remove('arProcessos');
            //unset( //sessao->transf6 );
            $audicao = new auditoriaLegada;
            foreach ($arAuditoria as $stChavesProcessos) {
                $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $stChavesProcessos );
                $audicao->insereAuditoria();
            }
            // DIEGO - ENVIO OK
            $stLink  = $PHP_SELF."?".Sessao::getId()."&pagina=".$pagina."&controle=1&codProcessoFl=".$codProcessoFl;
            $stLink .= "&codClassificacao=".$codClassificacao."&codAssunto=".$codAssunto."&numCgm=".$numCgm;
            $stLink .= "&numCgmUltimo=".$numCgmUltimo."&dataInicio=".$dataInicio."&dataTermino=".$dataTermino;
            $stLink .= "&ordem=".$ordem;
            $stMensagem = "Processos encaminhados com sucesso!";
            alertaAviso($stLink, $stMensagem,"historico","aviso", "'.Sessao::getId().'");
        } else {
            $conn = new dataBaseLegado;
            alertaAviso($PHP_SELF,"Erro ao encaminhar processos($conn->pegaUltimoErro())","unica","erro", "'.Sessao::getId().'");
        }

        break;
        case 4:

            $aux = validaSetor($chaveSetor, $anoExSetor);
            $js = "";
            $js .= "f.nomSetor.value = '".$aux["nomSetor"]."';\n";

            executaFrameOculto($js);

        break;
        case 100:
           include(CAM_FRAMEWORK."legado/filtrosCASELegado.inc.php");
        break;

}//Fim switch
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
