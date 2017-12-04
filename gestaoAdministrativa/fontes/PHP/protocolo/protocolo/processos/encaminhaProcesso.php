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

    $Id: encaminhaProcesso.php 66029 2016-07-08 20:55:48Z carlos.silva $

    */

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_FW_LEGADO."funcoesLegado.lib.php";
include CAM_FW_LEGADO."processosLegado.class.php";
include CAM_FW_LEGADO."auditoriaLegada.class.php";
include 'interfaceProcessos.class.php';
include CAM_FW_LEGADO."mascarasLegado.lib.php";

setAjuda('uc-01.06.98');

$mascaraProcesso = pegaConfiguracao("mascara_processo", 5);
$anoExercicio = $_REQUEST['anoExercicio'];
$ctrl = $_REQUEST['ctrl'];

if (!isset($_REQUEST["controle"])) {
    $controle = 0;
    $flag = 0;
} else {
    $controle = $_REQUEST["controle"];
}

$verificador = $_REQUEST["verificador"];

if ($verificador) {
    $controle = 1;
}

if ($ctrl == 2) {
    $controle = 1;
}

$pagina = $_REQUEST["pagina"];
if (isset($pagina)) {
    Sessao::write('pagina',$pagina);
}?>
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
switch ($controle) {
case 0:
?>
<script type="text/javascript">
    function Salvar()
    {
        document.frm.action = "encaminhaProcesso.php?<?=Sessao::getId()?>&controle=1";
        document.frm.submit();
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

$arFiltro = Sessao::read("filtro");
if ($arFiltro) {
    $_REQUEST = $arFiltro;
} else {
    foreach ($_REQUEST as $stChave => $stValor) {
        $arFiltro[$stChave] = $stValor;
    }
}
Sessao::write("filtro", $arFiltro);

if (Sessao::read('ordem_6') != "") {
    $ordem = Sessao::read('ordem_6');
}
 
    $inCodOrganograma = SistemaLegado::pegaDado('cod_organograma', 'organograma.vw_orgao_nivel', ' WHERE cod_orgao = '.Sessao::read('codOrgao'));
    $boPermissaoHierarquica = SistemaLegado::pegaDado('permissao_hierarquica', 'organograma.organograma', ' WHERE cod_organograma = '.$inCodOrganograma);

    $sql  = "";
    $sql .= "
               SELECT DISTINCT sw_processo.ano_exercicio
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

                WHERE  sw_situacao_processo.cod_situacao  =  3
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

        $codClassificacao_base = $_REQUEST["codClassificacao_base"];
                $codClassificacao = $_REQUEST["codClassificacao"];
        $codAssunto = $_REQUEST["codAssunto"];
        if (!isset($codClassificacao_base)) {
             $codClassificacao_base = $codClassificacao;
             $codAssunto_base=$codAssunto;
        }

        if (isset($codClassificacao_base)) {
            $codClassificacao = $codClassificacao_base;
                $codAssunto=$codAssunto_base;
        }

        // FILTRA PELO ASSUNTO REDUZIDO
        $resumo = $_REQUEST["resumo"];
        if ($resumo) {
            $resumo = str_replace ("*", "%", $resumo);
            $sql .= " AND sw_processo.resumo_assunto like ('".$resumo."%') ";
            $vet["resumo"] = $resumo;
        }

        if ($codClassificacao != "" && $codClassificacao != "xxx") {
            $sql .= " AND sw_processo.cod_classificacao = ".$codClassificacao;
            $vet["codClassificacao"] = $codClassificacao;
        }

        if ($codAssunto != "" && $codAssunto != "xxx") {
            $sql .= " AND sw_processo.cod_assunto = ".$codAssunto;
            $vet["codAssunto"] = $codAssunto;
        }

        $numCgm = $_REQUEST["numCgm"];
        if ($numCgm != "") {
            $sql .= " AND sw_processo_interessado.numcgm = ".$numCgm;
            $vet["numCgm"] = $numCgm;
        }

        $dataInicio  = $_REQUEST["dataInicio"];
        $dataTermino = $_REQUEST["dataTermino"];
        if ($dataInicio != "" && $dataTermino != "") {
            $arrData     = explode("/", $dataInicio);
            $dataInicio = $arrData[2]."-".$arrData[1]."-".$arrData[0];
            $arrData     = explode("/", $dataTermino);
                    $dataTermino   = $arrData[2]."-".$arrData[1]."-".$arrData[0];
            $sql .= " AND substr((sw_processo.timestamp::varchar),1,10) >= '".$dataInicio."'";
            $sql .= " AND substr((sw_processo.timestamp::varchar),1,10) <= '".$dataTermino."'";
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
            //sessao->transf = $sql;
            //sessao->transf5 = $vet;
            $st_ordenacao = array(
                 1 => "sw_processo.ano_exercicio, sw_processo.cod_processo",
                 2 => "sw_cgm.nom_cgm",
                 3 => "sw_classificacao.nom_classificacao, sw_assunto.nom_assunto, sw_processo.ano_exercicio, sw_processo.cod_processo",
                 4 => "sw_processo.timestamp");

    $ordem = $_REQUEST["ordem"];
    if (Sessao::read('ordem') =='') {
        Sessao::write('ordem',$ordem);
    }
    if ($ordem=='') {
        Sessao::write('sSQLs',$sql);
    }

        include(CAM_FW_LEGADO."paginacaoLegada.class.php");
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina(Sessao::read('pagina'));
        $paginacao->complemento = "&controle=1&codProcessoFl=".$codProcessoFl."&codClassificacao=".$codClassificacao."&codAssunto=".$codAssunto."&numCgm=".$numCgm."&numCgmU=".$numCgmU."&numCgmUltimo=".$numCgmUltimo."&dataInicial=".$dataInicial."&dataFinal=".$dataFinal."&ordem=".$ordem;
        $paginacao->geraLinks();
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
            $pagina = Sessao::read('pagina');

            $paginacao = new paginacaoLegada;
            $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
            $paginacao->pegaPagina(Sessao::read('pagina'));
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
                <td class='show_dados'>
                    ".$stDespacho."
                </td>
                <td class='show_dados'>
                    ".$stApenso."
                </td>
                <td class='botao'><div align='center' title='Consultar processo'>
                    <a href='consultaProcesso.php?".
                    Sessao::getId()."&codProcesso=".
                    $codProcesso."&anoExercicio=".
                    $anoEx."&controle=0&ctrl=2&pagina=".
                    $pagina."&verificador=true'>
                    <img src='".CAM_FW_IMAGENS."procuracgm.gif' alt='Consultar Processo' border=0>
                    </a></div>
                </td>
                <td class='botao' title='Encaminhar processo'>
                    <a href='javascript:mudaTelaPrincipal(\"encaminhaProcesso.php?".
                    Sessao::getId()."&codProcesso=".
                    $chave."&controle=2&pagina=".$pagina."&flag=0
                    &codProcessoFl=".$codProcessoFl."
                    &codClassificacao=".$codClassificacao."
                    &codAssunto=".$codAssunto."
                    &numCgm=".$numCgm."
                    &dataInicial=".$dataInicial."
                    &dataFinal=".$dataFinal."
                    &ordem=".$ordem."
                    &codClassificacao_base=".$codClassificacao_base."
                    &codAssunto_base=".$codAssunto_base.
                    "\");'>
                        <img src='".CAM_FW_IMAGENS."botao_encaminhar.png' border=0>
                    </a>
                </td>
            </tr>";

        }
        $exec .= "</table>";

        if ($dbEmp->numeroDeLinhas == 0) {
            //$exec .= "<b>Nenhum Registro Encontrado!</b>";
            $exec .=  "<b>Não Existem Processos a Encaminhar!</b>";
        }

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $exec;
        echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
        ?>
        <script>zebra('processos','zb');</script>
    <?php
    break;

    case 2:
        $codProcesso = $_REQUEST["codProcesso"];

        if (isset($codProcesso)) {
            $vet = explode("-",$codProcesso);
            $codP = $vet[0];
            $anoEx = $vet[1];
            $codClassif = $vet[2];
            $codAssunto = $vet[3];
            $html = new interfaceProcessos;
            $html->formEncaminhaProcesso("encaminhaProcesso.php", $codP, $anoEx, $codClassif, $codAssunto, Sessao::read('codOrgao'), $pagina, $flag);
        } else {
            alertaAviso($PHP_SELF."?".Sessao::getId()."&pagina=".$pagina,"Nenhum processo selecionado","historico","aviso");
        }
    break;

    case 3:

        $andamento    = $_REQUEST["andamento"];
        $codProcesso  = $_REQUEST["codProcesso"];
        $anoExercicio = $_REQUEST["anoExercicio"];

        if ($andamento == 'padrao') {
            $proxSetor = explode("-",$_REQUEST["chaveSetorPadrao"]);
            $anoExercicioSetor = $_REQUEST["exercicioPadrao"];
        } elseif ($andamento == 'outro') {
            echo "2";
            $proxSetor = explode(".",$_REQUEST["chaveSetor"]);
            $anoExercicioSetor = $_REQUEST["anoExSetor"];
        } elseif ($andamento == 'anterior') {
            echo "3";
            $proxSetor = explode(".", $_REQUEST["chaveSetorAnt"]);
            $anoExercicioSetor = $_REQUEST["anoExAnt"];
        }

        $usuario = new usuarioLegado;
        $dadosUsuario = array();
        $dadosUsuario = $usuario->pegaDadosUsuario(Sessao::read('numCgm'));
        //Codigo do orgão deve ser o que está configurado para o usuárioa que está logado, do setor que está encaminhando.
        //$orgao = $dadosUsuario['codOrgao'];
        //Comitado anterior, para encaminhar o processo para o setor definido no formulário de encaminhamento.
        $orgao = $_REQUEST['codOrgao'];
        
        $processos = new processosLegado;
        //Executa o encaminhamento do processo
        if ($processos->encaminhaProcesso($codProcesso, $anoExercicio, $orgao, Sessao::read('numCgm'))) {

            //Insere auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $codProcesso."/".$anoExercicio);
            $audicao->insereAuditoria();

            $verificaProcessoApenso = pegaDado('cod_processo_pai','sw_processo_apensado',' where cod_processo_pai = '.$codProcesso);
            if ($verificaProcessoApenso) {
                alertaAviso($PHP_SELF."?".Sessao::getId()."&pagina=".$pagina."&controle=1&codProcessoFl=".$codProcessoFl."&codClassificacao=".$codClassificacao."&codAssunto=".$codAssunto."&numCgm=".$numCgm."&numCgmU=".$numCgmU."&numCgmUltimo=".$numCgmUltimo."&dataInicial=".$dataInicial."&dataFinal=".$dataFinal."&ordem=".$ordem,"Processo ".$codProcesso."/".$anoExercicio." encaminhado com sucesso! Existe(m) processo(s) apenso(s) a ele. Consulte.","historico","aviso", "'.Sessao::getId().'");
            }

            // DIEGO - ENVIO OK
            alertaAviso($PHP_SELF."?".Sessao::getId()."&pagina=".$pagina."&controle=1&codProcessoFl=".$codProcessoFl."&codClassificacao=".$codClassificacao."&codAssunto=".$codAssunto."&numCgm=".$numCgm."&numCgmU=".$numCgmU."&numCgmUltimo=".$numCgmUltimo."&dataInicial=".$dataInicial."&dataFinal=".$dataFinal."&ordem=".$ordem,"Processo ".$codProcesso."/".$anoExercicio." encaminhado com sucesso!","historico","aviso", "'.Sessao::getId().'");
        } else {
            alertaAviso($PHP_SELF,"Erro ao encaminhar processo","unica","erro", "'.Sessao::getId().'");
        }

        break;

        case 4:
            $aux = validaSetor($_REQUEST["chaveSetor"], $_REQUEST["anoExSetor"]);
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
