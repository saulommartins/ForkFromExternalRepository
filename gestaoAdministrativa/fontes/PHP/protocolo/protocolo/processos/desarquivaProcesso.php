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

    $Id: desarquivaProcesso.php 66029 2016-07-08 20:55:48Z carlos.silva $

    Casos de uso: uc-01.06.98
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
include (CAM_FW_LEGADO."mascarasLegado.lib.php"     );
setAjuda('uc-01.06.98');

$mascaraProcesso = pegaConfiguracao("mascara_processo", 5);

$verificador = $_REQUEST["verificador"];
$controle    = $_REQUEST["controle"];
$pagina      = $_REQUEST["pagina"];

$ctrl = $_REQUEST['ctrl'];

if (!(isset($ctrl))) {
    $ctrl = 0;
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
</script>
<?php
switch ($ctrl) {
case 0:
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
            document.frm.action = "desarquivaProcesso.php?<?=Sessao::getId()?>&ctrl=1";
            document.frm.submit();
        }
    }
</script>
<?php
    $stAuxNome  = "ctrl";
    $stAuxValor = "1";
    include '../../../framework/legado/filtrosProcessoLegado.inc.php';
break;
case 1:
?>
<script type="text/javascript">
    function mudarPag(y)
    {
    window.location.replace(y);
}
</script>
<?php
    $stChaveProcesso = $_REQUEST['stChaveProcesso'];
    $codClassificacao = $_REQUEST['codClassificacao'];
    $codAssunto = $_REQUEST['codAssunto'];
    $numCgm = $_REQUEST['numCgm'];
    $dataInicio = $_REQUEST['dataInicio'];
    $dataTermino = $_REQUEST['dataTermino'];
    $ordem = $_REQUEST['ordem'];

    if (Sessao::read('vet') != "") {
        $vet = Sessao::read('vet');
        foreach ($vet AS $indice => $valor) {
            $$indice = $valor;
        }
    }
    $anoExercicio = Sessao::read('anoExercicio');
    $inCodOrganograma = SistemaLegado::pegaDado('cod_organograma', 'organograma.vw_orgao_nivel', ' WHERE cod_orgao = '.Sessao::read('codOrgao'));
    $boPermissaoHierarquica = SistemaLegado::pegaDado('permissao_hierarquica', 'organograma.organograma', ' WHERE cod_organograma = '.$inCodOrganograma);
      
        $stSQL .= " SELECT DISTINCT sw_processo.ano_exercicio                                           \n";
        $stSQL .= "     , sw_processo.cod_processo                                                      \n";
        $stSQL .= "     , sw_processo.timestamp                                                         \n";
        $stSQL .= "     , sw_ultimo_andamento.cod_andamento                                             \n";
        $stSQL .= "     , sw_classificacao.nom_classificacao                                            \n";
        $stSQL .= "     , sw_assunto.nom_assunto                                                        \n";
        $stSQL .= "     , array_to_string(array_agg(nom_cgm), ', ')as nom_cgm                           \n";
        $stSQL .= "
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

                  ";

        $stSQL .= "      WHERE sw_ultimo_andamento.cod_orgao IN (  SELECT cod_orgao
                                                             FROM organograma.vw_orgao_nivel
                                                            WHERE orgao_reduzido LIKE (
                                                                                        SELECT distinct(vw_orgao_nivel.orgao_reduzido)
                                                                                          FROM organograma.vw_orgao_nivel
                                                                                         WHERE vw_orgao_nivel.cod_orgao = ".Sessao::read('codOrgao')."
                                                                                       )";
                                                         # Permissão hierárquica define se o usuário pode ver processos de órgãos em níveis menores ou somente do seu nível.
                                                         $stSQL .= ($boPermissaoHierarquica == 't') ? "||'%'" : "";
                                                         $stSQL .= " GROUP BY cod_orgao) ";

        $stSQL .= "   AND (sw_situacao_processo.cod_situacao      =       5                             \n";
        $stSQL .= "        OR sw_situacao_processo.cod_situacao   =       9)                            \n";

        //FILTRO PRO ASSUNTO REDUZIDO
        if ($resumo != "") {
             $stSQL .= " AND sw_processo.resumo_assunto ILIKE '".$resumo."%' ";
             $vet["resumo"] = $resumo;
        }

        if ($stChaveProcesso != "") {
            $codProcessoFl = preg_split("/[^a-zA-Z0-9]/", $stChaveProcesso);
            $stSQL .= " AND sw_processo.cod_processo  = ".(int) $codProcessoFl[0];
            $vet["stChaveProcesso"] = $stChaveProcesso;
        }
        if ($codProcessoFl[1] != "") {
            $stSQL .= " AND sw_processo.ano_exercicio = '".$codProcessoFl[1]."' ";
            $vet["anoExercicio"]  = $codProcessoFl[1];
        }

        if ($codClassificacao != "" && $codClassificacao != "xxx") {
            $stSQL .= " AND sw_processo.cod_classificacao = ".$codClassificacao;
            $vet["codClassificacao"] = $codClassificacao;
        }

        if ($codAssunto != "" && $codAssunto != "xxx") {
            $stSQL .= " AND sw_processo.cod_assunto = ".$codAssunto;
            $vet["codAssunto"] = $codAssunto;
        }

        if ($numCgm != "") {
            $stSQL .= " AND  sw_processo_interessado.numcgm = ".$numCgm;
            $vet["numCgm"] = $numCgm;
        }

        if ($dataInicio != "" && $dataTermino != "" && $dataInicio != $dataTermino) {
            $vet["dataInicio"] = $dataInicio;
            $vet["dataTermino"]   = $dataTermino;
            $arrData     = explode("/", $dataInicio);
            $dataInicio = $arrData[2]."-".$arrData[1]."-".$arrData[0];
            $arrData     = explode("/", $dataTermino);
            $dataTermino   = $arrData[2]."-".$arrData[1]."-".$arrData[0];
            $stSQL .= " AND TO_DATE(sw_processo.timestamp::varchar, 'yyyy-mm-dd') BETWEEN '".$dataInicio."' AND ";
            $stSQL .= " '".$dataTermino."'";
        } elseif ( $dataInicio != "" && $dataTermino == "" or ( $dataInicio != "" && $dataInicio == $dataTermino ) ) {
            $vet["dataInicio"] = $dataInicio;
            $vet["dataTermino"]   = $dataTermino;
            $arrData     = explode("/", $dataInicio);
            $dataInicio = $arrData[2]."-".$arrData[1]."-".$arrData[0];
            $arrData     = explode("/", $dataTermino);
            $dataTermino   = $arrData[2]."-".$arrData[1]."-".$arrData[0];
            $stSQL .= " AND TO_DATE(sw_processo.timestamp::varchar, 'yyyy-mm-dd') = '".$dataInicio."' ";
        }

        //FILTRO POR ATRIBUTO DE ASSUNTO
        if ($_REQUEST[valorAtributoTxt]) {
            foreach ($_REQUEST[valorAtributoTxt] as $key => $value) {
                if ($_REQUEST[valorAtributoTxt][$key]) {
                    $stSQL .= " AND sw_assunto_atributo_valor.valor ILIKE ( '%".$_REQUEST[valorAtributoTxt][$key]."%' ) \n";
                    $stSQL .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
                }
            }
        }
        if ($_REQUEST[valorAtributoNum]) {
            foreach ($_REQUEST[valorAtributoNum] as $key => $value) {
                if ($_REQUEST[valorAtributoNum][$key]) {
                    $stSQL .= " AND sw_assunto_atributo_valor.valor = '".$_REQUEST[valorAtributoNum][$key]."' \n";
                    $stSQL .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
                }
            }
        }
        if ($_REQUEST[valorAtributoCmb]) {
            foreach ($_REQUEST[valorAtributoCmb] as $key => $value) {
                if ($_REQUEST[valorAtributoCmb][$key]) {
                    $stSQL .= " AND sw_assunto_atributo_valor.valor = '".$_REQUEST[valorAtributoCmb][$key]."' \n";
                    $stSQL .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
                }
            }
        }

        $stSQL .= " GROUP BY sw_assunto.nom_assunto
                                    ,  sw_assunto.cod_assunto
                                    ,  sw_classificacao.nom_classificacao
                                    ,  sw_classificacao.cod_classificacao
                                    ,  sw_processo.ano_exercicio
                                    ,  sw_processo.cod_processo
                                    ,  sw_processo.timestamp
                                    ,  sw_ultimo_andamento.cod_andamento ";

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
            //sessao->transf5 = $vet;

            include '../../../framework/legado/paginacaoLegada.class.php';
            $paginacao = new paginacaoLegada;
            $paginacao->pegaDados($stSQL,"10");
            $paginacao->pegaPagina($pagina);
            $paginacao->complemento = "&ctrl=1&codProcessoFl=".
            $codProcessoFl."&codClassificacao=".
            $codClassificacao."&codAssunto=".
            $codAssunto."&numCgm=".
            $numCgm."&numCgmU=".
            $numCgmU."&numCgmUltimo=".
            $numCgmUltimo."&dataInicial=".
            $dataInicial."&dataFinal=".
            $dataFinal."&ordem=".
            $ordem;
            $paginacao->geraLinks();
            $paginacao->pegaOrder($ordem,"ASC");
            $count = $paginacao->contador();
            $sSQL = $paginacao->geraSQL();
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $exec = "";
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
                     <td class='labelcenterCabecalho' colspan='2'>&nbsp;</td>
                </tr>
            ";
            while (!$dbEmp->eof()) {
                    $codProcesso       = trim($dbEmp->pegaCampo("cod_processo"));
                    $codAndamento      = trim($dbEmp->pegaCampo("cod_andamento"));
                    $anoEx             = trim($dbEmp->pegaCampo("ano_exercicio"));
                    $nomClassificacao  = trim($dbEmp->pegaCampo("nom_classificacao"));
                    $nomAssunto        = trim($dbEmp->pegaCampo("nom_assunto"));
                    $interessado       = $dbEmp->pegaCampo("nom_cgm");
                    $date              = timestamptobr($dbEmp->pegaCampo("timestamp"));

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
                    ".$nomClassificacao."
                </td>
                <td class='show_dados'>
                    ".$nomAssunto."
                </td>
                <td class='show_dados'>
                     ".$date."
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
                <td class=botao width=5 title='Desarquivar Processo'>
                    <a href='javascript:mudarPag(\"desarquivaProcesso.php?".Sessao::getId()."&codProcesso=".$codProcesso."&ctrl=2&anoExercicio=".$anoEx."\");'>
                        <img src='".CAM_FW_IMAGENS."botao_desarquivar.png' border=0>
                    </a>
                </td>
            </tr>\n";
            }

            //marcia
            //					<a href='javascript:mudarPag(\"desarquivaProcesso.php?".Sessao::getId()."&codProcesso=".$codProcesso."&ctrl=2&anoExercicio=".$anoExercicio."\");'>
            //<input type=button value=Desarquivar OnClick=\"mudarPag('desarquivaProcesso.php?".Sessao::getId()."&codProcesso=".$codProcesso."&ctrl=1&anoExercicio=".$anoExercicio."');\">
            $exec .= "</table>";

            if ($dbEmp->numeroDeLinhas <= 0) {
                //$exec .=  "<tr><td class=show_dados colspan='3'>Não Existem Processos Arquivados</td></tr>";
                $exec .=  "<b>Não Existem Processos Arquivados!</b>";
            }
            echo $exec;
            echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
            $paginacao->mostraLinks();
            echo "</font></tr></td></table>";

            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
?>
 <script>zebra('processos','zb');</script>
<?php
break;
case 2:
$codProcesso  = $_REQUEST["codProcesso"];
$anoExercicio = $_REQUEST["anoExercicio"];

include '../situacaoProcesso.class.php';

$situacaoProcesso = new situacaoProcesso;
$situacaoProcesso->setaVariaveisArquivamento("3",$codProcesso,"",$anoExercicio,$stLocalizacaoFisica);
if ($situacaoProcesso->apagaArquivamento()) {
                    include '../../../framework/legado/auditoriaLegada.class.php';
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $codProcesso."/".$anoExercicio);
                    $audicao->insereAuditoria();
                    echo '<script type="text/javascript">
                    alertaAviso("Processo '.$codProcesso.'/'.$anoExercicio.' desarquivado com sucesso","unica","aviso", "'.Sessao::getId().'");
                    window.location="desarquivaProcesso.php?'.Sessao::getId().'&ctrl=1";
                    </script>';
    } else {
                    echo '<script type="text/javascript">
                    alertaAviso("Não foi possível desarquivar o Processo '.$codProcesso.'/'.$anoExercicio.'","unica","erro", "'.Sessao::getId().'");
                    window.location = "desarquivaProcesso.php?'.Sessao::getId().'&ctrl=1";
                    </script>';
    }

break;
case 100:
    include '../../../framework/legado/filtrosCASELegado.inc.php';
break;
}
include '../../../framework/include/rodape.inc.php';
?>
