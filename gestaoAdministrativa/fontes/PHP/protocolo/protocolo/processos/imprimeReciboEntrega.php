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

    $Id: imprimeReciboEntrega.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_LEGADO."botoesPdfLegado.class.php";
include_once CAM_FW_LEGADO."processosLegado.class.php";
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CAM_FW_LEGADO."auditoriaLegada.class.php";
include_once CAM_FW_LEGADO."paginacaoLegada.class.php";
include_once CAM_FW_LEGADO."mascarasLegado.lib.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";

setAjuda('uc-01.06.98');

$sOpcao           = $_REQUEST["sOpcao"];
$pagina           = $_REQUEST["pagina"];
$codProcesso      = $_REQUEST["codProcesso"];
$numCgm           = $_REQUEST['numCgm'];
$stChaveProcesso  = $_REQUEST['stChaveProcesso'];
$dataInicial      = $_REQUEST["dataInicio"];
$dataFinal        = $_REQUEST["dataTermino"];
$resumo           = $_REQUEST["resumo"];
$codClassificacao = $_REQUEST["codClassificacao"];
$codAssunto       = $_REQUEST["codAssunto"];
$mascaraProcesso  = pegaConfiguracao("mascara_processo", 5);
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
switch ($sOpcao) {
    case "imprimeRecibo":

        $iExercicio = $_REQUEST['iExercicio'];
        $iNumRecibo = $_REQUEST['iNumRecibo'];

        imprimeRecibo($iNumRecibo, $iExercicio);

        ?>
            <script type="text/javascript">
                function SalvarImprimir()
                {
                    document.frm.action = "reciboEntrega.php?<?=Sessao::getId()?>&sOpcao=imprimeRecibo&iNumRecibo=<?=$iNumRecibo?>&iExercicio=<?=$iExercicio?>";
                    document.frm.submit();
                }
            </script>
            <form name=frm action="imprimeReciboEntrega.php?<?=Sessao::getId()?>&sOpcao=imprimeRecibo" method="post">
        <?php

        break;
    case "salvaDados":
        salvaDados();
        break;
    case "montaFormulario" :
        montaFormulario();
        break;
    case "montaFormularioReimpressao" :
        montaFormularioReimpressao();
        break;
    case "listaProcessos" :
        listaProcessos();
        break;
    default :
        filtros();
    break;
}

function filtros()
{
    foreach ($_REQUEST AS $indice => $valorIndice) {
        $$indice = $valorIndice;
    }

    if (!(isset($ctrl))) {
        $ctrl = 0;
    }
    switch ($ctrl) {
        case 0:
            $stAuxNome  = "sOpcao";
            $stAuxValor = "listaProcessos";
            include(CAM_FW_LEGADO."filtrosProcessoLegado.inc.php");
        break;
        case 100:
            include(CAM_FW_LEGADO."filtrosCASELegado.inc.php");
        break;
    }

}

function montaFormulario()
{
    print '
    <script type="text/javascript">
        function Valida()
        {
            var mensagem = "";
            var erro = false;
            var campo;
            var campoaux;
            var f;

            f = document.frmSelSetor;

            campo = f.codSetor.value.length;
                if (campo==0) {
                    erro = true;
                    mensagem += "@O campo Setor é obrigatório!";
                }

            if (erro) alertaAviso(mensagem,"form","erro","'.Sessao::getId().'");
            return !(erro);

        }// Fim da function Valida

        function Salvar()
        {
            if (Valida()) {
                document.frmSelSetor.submit();
            }
        }
    </script>
    <br>
    <form name="frmSelSetor" action="'.$_SERVER['PHP_SELF'].'?'.Sessao::getId().'" method="POST">
    <input type="hidden" name="sOpcao" value="listaProcessos">
    <table width="100%">
        <tr>
                    <td class=label title="Setor que receberá o processo">
                        *Setor
                    </td>
                    <td class=field>
                        <input type="text" name="mascaraSetor" size="15" value="<?=$mascaraSetor;?>">
                        <br>
                        <select name=codOrgao onChange="document.frm.submit();" style="width: 300px;">
                            <option value=xxx>Selecione o órgão</option>//echo $codOrgaoAP;
                            //Faz o combo de Órgãos
        <tr>
            <td class="label">Exercício:</td>
            <td class="field">
                <input type="text" name="codExercicio" size=4 maxlength=4
                value="'.Sessao::getExercicio().'">
                </td>
        </tr>
        <tr>
            <td class="field" colspan="3"><input type="button"  style="{width: 60px;}" value="OK" onclick="Salvar();"></td>
        </tr>
    </table>
    </form>';
}

function montaFormularioReimpressao()
{
    print '
    <script type="text/javascript">
        function Valida()
        {
            var mensagem = "";
            var erro = false;
            var campo;
            var campoaux;
            var f;

            f = document.frmSelSetor;

            campo = f.iNumRecibo.value.length;
                if (campo==0) {
                    erro = true;
                    mensagem += "@O campo número do recibo é obrigatório!";
                }

            campo = f.iExercicio.value.length;
                if (campo==0) {
                    erro = true;
                    mensagem += "@O campo exercício é obrigatório!";
                }

            if (erro) alertaAviso(mensagem,"form","erro","'.Sessao::getId().'");
            return !(erro);

        }// Fim da function Valida

        function Salvar()
        {
            if (Valida()) {
                document.frmSelSetor.submit();
            }
        }
    </script>

    <form name="frmSelSetor" action="'.$_SERVER['PHP_SELF'].'?'.Sessao::getId().'" method="POST">
    <input type="hidden" name="sOpcao" value="imprimeRecibo">
    <table width="100%">
        <tr>
            <td class=alt_dados colspan="2">2° Via do Recibo de Processo</td>
        </tr>
        <tr>
            <td class="label" width="20%">* Número do Recibo</td>
            <td class="field" width="80%"><input type="text" name="iNumRecibo" size=6 maxlength=6></td>
        </tr>
        <tr>
            <td class="label">* Exercício</td>
            <td class="field">
                <input type="text" name="iExercicio" size=4 maxlength=4
                value="'.Sessao::getExercicio().'">
            </td>
        </tr>
        <tr>
            <td class="field" colspan="2"><input type="button" onclick="Salvar();" style="{width: 60px;}" value="OK"></td>
        </tr>
    </table>
    </form>';
}

function listaProcessos()
{
    foreach ($_REQUEST AS $indice => $valorIndice) {
        $$indice = $valorIndice;
    }
    if (!(isset($ctrl))) {
        $ctrl = 0;
    }

    $dataInicial = (!isset($dataInicial) ? $dataInicio : $dataInicial);
    $dataFinal   = (!isset($dataFinal) ? $dataTermino : $dataFinal);

    switch ($ctrl) {
        case 0:
        $aSetor          = explode("-",$codSetor);
        $mascara         = pegaConfiguracao("mascara_setor");
        $mascaraProcesso = pegaConfiguracao("mascara_processo", 5);
        if (Sessao::read('vet') != "") {
            $vet = Sessao::read('vet');
            foreach ($vet AS $indice => $valor) {
                $indice = $valor;
            }
        }

        print '
        <script type="text/javascript">
        <!--
            function Reimpressao()
            {
                document.frm.sOpcao.value = "montaFormularioReimpressao";
                document.frm.submit();
            }

            function outroAndamento()
            {
                procuraSetor("frm","nomSetor","chaveSetor","anoExercicioSetor","'.Sessao::getId().'");
            }

            // Validação JS.
            function ValidaForm()
            {
                if (Valida())
                    return true;
                else
                    return false;
            }
            // Fim da function Valida

            function SalvarForm()
            {
                if (ValidaForm()) {
                    document.frm.sOpcao.value = "salvaDados";
                    document.frm.submit();
                }
            }
        //-->
        </script>
        <form name="frm" action="imprimeReciboEntrega.php?'.Sessao::getId().'&chaveS='.$codSetor.'" method="POST">
        <input type="hidden" name="sOpcao" value="listaProcessos">
        <table width="100%">
            <tr>
                <td class="alt_dados" colspan="2">Selecione o destino dos processos</td>
            </tr>';

            # ANTIGO componente do atual Organogrma.
            # include(CAM_FW_LEGADO."filtrosSELegado.inc.php");

            # NOVO componente do atual Organogrma.
            $obFormulario = new Formulario;
            $obFormulario->setForm(null);

            # Instancia para o novo objeto Organograma
            $obIMontaOrganograma = new IMontaOrganograma;
            $obIMontaOrganograma->setNivelObrigatorio(1);
            $obIMontaOrganograma->geraFormulario($obFormulario);

            $obFormulario->montaHtml();
            echo $obFormulario->getHTML();

    print '
        </table>
        <table width="100%" id="processos">
            <tr>
                    <td class=alt_dados colspan="11">
                        Registros de processos
                    </td>
                </tr>
                <tr>
                    <td class="labelcenterCabecalho"  style="vertical-align: middle;">&nbsp;</td>
                    <td class="labelcenterCabecalho"  style="vertical-align: middle;">&nbsp;</td>
                    <td class="labelcenterCabecalho"  style="vertical-align: middle;" width="8%">Código</td>
                    <td class="labelcenterCabecalho"  style="vertical-align: middle;">Interessado</td>
                    <td class="labelcenterCabecalho"  style="vertical-align: middle;">Classificação</td>
                    <td class="labelcenterCabecalho"  style="vertical-align: middle;">Assunto</td>
                    <td class="labelcenterCabecalho"  style="vertical-align: middle;">Inclusão</td>
                    <td class="labelcenterCabecalho" >&nbsp;</td>
                </tr>
                ';

               $inCodOrganograma = SistemaLegado::pegaDado('cod_organograma', 'organograma.vw_orgao_nivel', ' WHERE cod_orgao = '.Sessao::read('codOrgao'));
               $boPermissaoHierarquica = SistemaLegado::pegaDado('permissao_hierarquica', 'organograma.organograma', ' WHERE cod_organograma = '.$inCodOrganograma);

                $sql .="
                       SELECT DISTINCT sw_processo.ano_exercicio
                     , sw_processo.cod_processo
                     , sw_processo.timestamp
                     , sw_ultimo_andamento.cod_andamento
                     , sw_classificacao.nom_classificacao
                     , sw_assunto.nom_assunto
                     ,  array_to_string(array_agg(nom_cgm), ', ')as nom_cgm
                 FROM  public.sw_processo
                 LEFT JOIN sw_assunto_atributo_valor ON sw_assunto_atributo_valor.cod_processo  = sw_processo.cod_processo
                                                    AND sw_assunto_atributo_valor.exercicio     = sw_processo.ano_exercicio
                     , public.sw_ultimo_andamento
                     , public.sw_classificacao
                     , public.sw_assunto
                     , public.sw_cgm
                     , public.sw_situacao_processo
                     , public.sw_processo_interessado
                 WHERE sw_processo.cod_classificacao     = sw_assunto.cod_classificacao
                   AND sw_processo.cod_assunto           = sw_assunto.cod_assunto
                   AND sw_processo_interessado.cod_processo  = sw_processo.cod_processo
                   AND sw_processo_interessado.ano_exercicio = sw_processo.ano_exercicio
                   AND sw_processo_interessado.numcgm    = sw_cgm.numcgm
                   AND sw_processo.cod_situacao          = sw_situacao_processo.cod_situacao
                   AND sw_ultimo_andamento.ano_exercicio = sw_processo.ano_exercicio
                   AND sw_ultimo_andamento.cod_processo  = sw_processo.cod_processo
                   AND sw_assunto.cod_classificacao      = sw_classificacao.cod_classificacao
                   AND sw_situacao_processo.cod_situacao = 3
                   AND sw_ultimo_andamento.cod_orgao IN (SELECT cod_orgao
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
                    $sql .= " AND sw_processo.cod_processo  = ".(int) $codProcessoFl[0];
                    $vet["stChaveProcesso"] = $stChaveProcesso;
                }
                if ($codProcessoFl[1] != "") {
                    $sql .= " AND sw_processo.ano_exercicio = '".$codProcessoFl[1]."' ";
                    $vet["anoExercicio"]  = $codProcessoFl[1];
                }

                /*
                    * Foi inserido este if acima, pois caso o usuário não digitasse o ano exercicio dava erro.
                */
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

                if ($dataInicial != "" && $dataFinal != "") {
                    $arrData     = explode("/", $dataInicial);
                    $dataInicial = $arrData[2]."-".$arrData[1]."-".$arrData[0];
                    $arrData     = explode("/", $dataFinal);
                    $dataFinal   = $arrData[2]."-".$arrData[1]."-".$arrData[0];

                    $sql .= " AND to_date( sw_processo.timestamp::varchar, 'yyyy-mm-dd')  >= '".$dataInicial."'";
                    $sql .= " AND to_date( sw_processo.timestamp::varchar, 'yyyy-mm-dd')  <= '".$dataFinal."'";
                    $vet["dataInicial"] = $dataInicial;
                    $vet["dataFinal"]   = $dataFinal;
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
          if (Sessao::read('ordem') =='') {
              Sessao::write('ordem',$ordem);
          }
          $sql .= " ORDER BY ".$st_ordenacao[Sessao::read('ordem')];

        $dbProc = new databaseLegado;
        $dbProc->abreBd();
        $dbProc->abreSelecao($sql);
        $paginacao = new paginacaoLegada;
        $count = $paginacao->contador();
        while (!$dbProc->eof()) {
            $iCodProcesso     = $dbProc->pegaCampo("cod_processo");
            $nomClassificacao = $dbProc->pegaCampo("nom_classificacao");
            $nomAssunto       = $dbProc->pegaCampo("nom_assunto");
            $interessado      = $dbProc->pegaCampo("nom_cgm");
            $iAnoExercicio    = $dbProc->pegaCampo("ano_exercicio");
            $iCodAndamento    = $dbProc->pegaCampo("cod_andamento");
            $timestamp        = $dbProc->pegaCampo("timestamp");
            $dbProc->vaiProximo();
            $arr                = explode(" ", $timestamp);
            $arrData            = explode("-", $arr[0]);
            $dataInclusao       = $arrData[2]."/".$arrData[1]."/".$arrData[0];

            $codProcessoMascara = mascaraProcesso($iCodProcesso, $iAnoExercicio);
                    print '
                    <tr>
                        <td class="show_dados_center_bold">
                            '.$count++.'
                        </td>
                        <td class="show_dados">
                            <input type="checkbox" name="codProcesso[]" value="'.$iCodProcesso.'-'.$iAnoExercicio.'-'.$iCodAndamento.'">
                        </td>
                        <td class="show_dados">
                            '.$codProcessoMascara.'
                        </td>
                        <td class="show_dados">
                            '.$interessado.'
                        </td>
                        <td class="show_dados">
                            '.$nomClassificacao.'
                        </td>
                        <td class="show_dados">
                            '.$nomAssunto.'
                        </td>
                        <td class="show_dados">
                            '.$dataInclusao.'
                        </td>
                        <td class="botao"><div align="center" title="Consultar processo">
                        <a href="consultaProcesso.php?'.
                        Sessao::getId().'&codProcesso='.
                        $iCodProcesso.
                        '&dataInicial='.
                        $_REQUEST["dataInicio"].
                        '&dataFinal='.
                        $_REQUEST["dataTermino"].
                        '&anoExercicio='.
                        $iAnoExercicio.'&controle=0&ctrl=2&verificador=true">
                        <img src="'.CAM_FW_IMAGENS.'procuracgm.gif" alt="" border=0>
                        </a></div>
                    </td>
                    </tr>';
        }

        $dbProc->limpaSelecao();
        $dbProc->fechaBd();
        print '
            <tr>
                <td class="show_dados" colspan="11">
                    <input type="button"  style="{width: 60px;}" value="OK" onclick="javascript: SalvarForm();">&nbsp;
                    <input type="button" value="Segunda Via" onClick ="javascript:Reimpressao();" name="botao">
                </td>
            </tr>
        </table>
        </form>';
?>
        <script>zebra('processos','zb');</script>
<?php
        break;

        case 100:
            include(CAM_FW_LEGADO."filtrosCASELegado.inc.php");
        break;
    }

}

function salvaDados()
{
    $codProcesso = $_REQUEST['codProcesso'];
    $codMasSetor = $_REQUEST['codMasSetor'];
    $codOrgao    = $_REQUEST['hdnUltimoOrgaoSelecionado'];

    if (isset($codProcesso)) {
        $processos = new processosLegado;
        $numCgm = Sessao::read('numCgm');
        $iRecNum = $processos->recebeProcessosManualmente($codProcesso, $numCgm, $codOrgao);

        if ($iRecNum > 0) {
            //Insere auditoria
            $audicao = new auditoriaLegada;
            foreach ($codProcesso as $valor) {
                $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), 'Recibo num. '.$iRecNum);
                $audicao->insereAuditoria();
            }

            foreach ($codProcesso as $valor) {
                $arDadosProcesso = explode("-", $valor);
                $codProcesso = $arDadosProcesso[0];
                $iExercicio  = $arDadosProcesso[1];
                break;
            }

            # Errado pois não pega o exercício do processo propriamente.
            # $iExercicio = pegaConfiguracao('ano_exercicio');

            /*
             * Terá o mesmo comportamento de quando se emite segunda via do recibo,
             * para mostrar os apensados vinculados ao processo.
             *
             */
            imprimeRecibo($iRecNum, $iExercicio, $codProcesso, $codOrgao);
            exibeAviso("Recibo $iRecNum","incluir","aviso");

        } else {
            alertaAviso($_SERVER['PHP_SELF'],"Erro ao receber processos!","unica","erro");
        }
    } else {
        alertaAviso($_SERVER['PHP_SELF'],"Nenhum processo selecionado","unica","aviso");
    }
}

function imprimeRecibo($iNumRecibo, $iExercicio, $codProcesso="", $codOrgao="")
{
    $mascaraProcesso = pegaConfiguracao("mascara_processo", 5);
    $recibo = pegaDado("cod_recibo","sw_recibo_impresso","Where cod_recibo = '".$iNumRecibo."' and ano_exercicio= '".$iExercicio."' ");
    $objeto = $iNumRecibo;

    if (is_null($recibo)) {

        echo '<script type="text/javascript">
              alertaAviso("Recibo não encontrado: '.$objeto.'/'.$iExercicio.'","unica","erro","'.Sessao::getId().'");
              mudaTelaPrincipal("imprimeReciboEntrega.php?'.Sessao::getId().'");
              </script>';
    } else {
    $sSQL = "";
    $sSQL .= "
    SELECT DISTINCT
        sw_processo.cod_processo || '/' || sw_processo.ano_exercicio as cod_ano_processo
      , sw_processo_interessado.numcgm
      , TO_CHAR(sw_processo.timestamp, 'DD/MM/YYYY - HH:MI:ss') AS timestamp
      , sw_cgm.nom_cgm
      , sw_classificacao.nom_classificacao
      , sw_assunto.nom_assunto
      , usuario.username
      , ( case when filho.cod_processo_filho is not null
               then 'Em Apenso'
               end ) AS apensado
    FROM
        sw_processo

     LEFT JOIN  sw_processo_apensado AS filho
            ON  filho.cod_processo_filho = sw_processo.cod_processo
           AND  filho.exercicio_filho    = sw_processo.ano_exercicio
           AND  filho.timestamp_desapensamento IS NULL

     LEFT JOIN  sw_processo_apensado AS pai
            ON  pai.cod_processo_pai = sw_processo.cod_processo
           AND  pai.exercicio_pai    = sw_processo.ano_exercicio
           AND  pai.timestamp_desapensamento IS NULL

    INNER JOIN  sw_processo_interessado
            ON  sw_processo.cod_processo = sw_processo_interessado.cod_processo
           AND  sw_processo.ano_exercicio = sw_processo_interessado.ano_exercicio

    INNER JOIN  sw_cgm
            ON  sw_cgm.numcgm = sw_processo_interessado.numcgm

    INNER JOIN  sw_recibo_impresso
            ON  sw_recibo_impresso.cod_processo  = sw_processo.cod_processo
           AND  sw_recibo_impresso.ano_exercicio = sw_processo.ano_exercicio

    INNER JOIN  sw_assunto
            ON  sw_assunto.cod_assunto       = sw_processo.cod_assunto
           AND  sw_assunto.cod_classificacao = sw_processo.cod_classificacao

    INNER JOIN  sw_classificacao
            ON  sw_classificacao.cod_classificacao = sw_assunto.cod_classificacao

    INNER JOIN  sw_ultimo_andamento
            ON  sw_ultimo_andamento.cod_processo = sw_processo.cod_processo
           AND  sw_ultimo_andamento.cod_processo = sw_recibo_impresso.cod_processo

    INNER JOIN  administracao.usuario
            ON  usuario.numcgm = sw_processo.cod_usuario

         WHERE  1=1
           AND  sw_recibo_impresso.cod_recibo    = '$iNumRecibo'
           AND  sw_recibo_impresso.ano_exercicio = '".$iExercicio."'";

    $sParam  = "hoje=".Hoje(true).";";
    $sParam .= "cidade=".pegaConfiguracao('nom_municipio').";";
    $sParam .= "num_recibo=".$iNumRecibo."/".$iExercicio.";";

    $sSubTitulo = "Recibo de Entrega n. ".$iNumRecibo."/".$iExercicio;
    $sXML       = CAM_PROTOCOLO.'protocolo/processos/imprimeReciboEntrega.xml';

    print '
        <script type="text/javascript">
            function SalvarImprimir()
            {
                document.frm.action = "reciboEntrega.php?'.Sessao::getId().'&sOpcao=imprimeRecibo&iNumRecibo='.$iNumRecibo.'&iExercicio='.$iExercicio.'";
                document.frm.submit();
            }
        </script>
        <form name=frm action="imprimeReciboEntrega.php?'.Sessao::getId().'&sOpcao=imprimeRecibo" method="post">
        <table width="100">
             <tr>
                 <td class="labelcenter" title="Salvar Relatório">
                 <a href="javascript:SalvarImprimir();"><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a>
             </tr>
         </table>
         ';

    $paginacao = new paginacaoLegada;
    $count = $paginacao->contador();
?>
        <table width="100%">
            <tr>
                <td class=alt_dados colspan="2">
                    Dados do recibo
                </td>
            </tr>
            <tr>
                <td class=label width="30%">
                    Código
                </td>
                <td class=field width="70%">
                    <?=$iNumRecibo?>
                </td>
            </tr>
            <tr>
                <td class=label>
                    Data/Hora de geração
                </td>
                <td class=field>
                    <?php
                    echo $dataGeracao = hoje()." - ".agora();
                    ?>
                </td>
            </tr>
            <tr>
                <td class=alt_dados colspan="2">
                    Dados de destino
                </td>
            </tr>
        </table>
 <?php
        $sSQL = "
            SELECT  A.cod_processo,
                    A.ano_exercicio,
                    A.cod_orgao

              FROM  sw_andamento A,
                    sw_recibo_impresso R

             WHERE  R.cod_recibo    = '".$iNumRecibo."'
               AND  A.cod_andamento = R.cod_andamento
               AND  A.cod_processo  = R.cod_processo
               AND  A.ano_exercicio = R.ano_exercicio
               AND  R.ano_exercicio = '".$iExercicio."'";

        $dbConfig = new databaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($sSQL);

        $codProcessoAux = array();

        while (!($dbConfig->eof())) {
            $codOrgao         = $dbConfig->pegaCampo("cod_orgao");
            $codProcessoAux[] = $dbConfig->pegaCampo("cod_processo")."-".$dbConfig->pegaCampo("ano_exercicio");

            $dbConfig->vaiProximo();
        }

        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();

        # NOVO componente do atual Organogrma.
        $obFormulario = new Formulario;
        $obFormulario->setForm(null);
        $obFormulario->setLarguraRotulo(30);

        # Instancia para o novo objeto Organograma
        $obIMontaOrganograma = new IMontaOrganograma(true);
        $obIMontaOrganograma->setCodOrgao($codOrgao);
        $obIMontaOrganograma->setComponenteSomenteLeitura(true);
        $obIMontaOrganograma->geraFormulario($obFormulario);

        $obFormulario->montaHtml();
        echo $obFormulario->getHTML();

    ?>
        <table width="100%">
            <tr>
                <td class=alt_dados colspan="7">
                    Registros de processos
                </td>
            </tr>

    <?php

    while (list($key, $val) = each($codProcessoAux)) {
        $codP = explode("-", $val);
        $select = 	"SELECT
                           P.cod_processo,
                           P.ano_exercicio,
                           C.nom_classificacao,
                           A.nom_assunto,
                           U.username,
                           TO_CHAR(P.timestamp, 'DD/MM/YYYY - HH:MI:SS') AS timestamp

                     FROM  sw_processo           AS P,
                           sw_classificacao      AS C,
                           sw_assunto            AS A,
                           sw_cgm                AS G,
                           administracao.usuario AS U

                    WHERE  1=1

                      AND  P.cod_processo      = ".$codP[0]."
                      AND  P.ano_exercicio     = '".$codP[1]."'
                      AND  P.cod_classificacao = C.cod_classificacao
                      AND  P.cod_assunto       = A.cod_assunto
                      AND  A.cod_classificacao = C.cod_classificacao
                      AND  P.cod_usuario = U.numcgm";

        $dbConfig = new databaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($select);
        $codProcessoImprime = $dbConfig->pegaCampo("cod_processo");
        $anoExerciciImprime = $dbConfig->pegaCampo("ano_exercicio");
        $codProcessoMascara = mascaraProcesso($codProcessoImprime, $anoExerciciImprime);
        echo 	"<tr>
                    <td class='labelcenter'>&nbsp;</td>
                    <td class='labelcenter'>Código</td>
                    <td class='labelcenter'>Classificação</td>
                    <td class='labelcenter'>Assunto</td>
                    <td class='labelcenter'>Data</td>
                    <td class='labelcenter'>Usuário</td>
                </tr>";
        echo 	"<tr>
                    <td class=labelcenter>
                        ".$count++."
                    </td>
                    <td class=show_dados>
                        ".$codProcessoMascara."
                    </td>
                    <td class=show_dados>
                        ".$dbConfig->pegaCampo("nom_classificacao")."
                    </td>
                    <td class=show_dados>
                        ".$dbConfig->pegaCampo("nom_assunto")."
                    </td>
                    <td class=show_dados>
                        ".$dbConfig->pegaCampo("timestamp")."
                    </td>
                    <td class=show_dados>
                        ".$dbConfig->pegaCampo("username")."
                    </td>
                </tr>";

              // Busca os interessados da base de dados ( ação de alterar processo )
        $sqlQueryInteressado =
               "SELECT  sw_cgm.nom_cgm, sw_processo_interessado.numcgm
                  FROM  sw_processo_interessado
            INNER JOIN  sw_cgm
                    ON  sw_cgm.numcgm = sw_processo_interessado.numcgm
                 WHERE  sw_processo_interessado.cod_processo = ".$codP[0]."
                   AND  sw_processo_interessado.ano_exercicio = '".$codP[1]."'";

        $sqlInteressado = new databaseLegado;
        $sqlInteressado->abreBd();
        $sqlInteressado->abreSelecao($sqlQueryInteressado);

        // Lista os interessados do processo
?>
            <tr>
                <td class='labelcenter'>&nbsp;</td>
                <td class=label colspan="6" style="text-align:left;">
                    Interessado(s)
                </td>
            </tr>
<?php
        $i = 1;
        while (!$sqlInteressado->eof()) {
            $numCgm = $sqlInteressado->pegaCampo("numcgm");
            $nomCgm = $sqlInteressado->pegaCampo("nom_cgm");

?>
            <tr>
                <td class='show_dados'>&nbsp;</td>
                <td class='show_dados' colspan="6"><?=$numCgm;?> - <?=$nomCgm;?></td>
            </tr>
<?php
            $i++;
            $sqlInteressado->vaiProximo();
        }
?>
            <tr>
                <td class='' colspan="6"></td>
            </tr>

<?php

    }
    $sqlInteressado->limpaSelecao();
    $dbConfig->limpaSelecao();
    $dbConfig->fechaBd();
}

};
    echo "</table>";

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
