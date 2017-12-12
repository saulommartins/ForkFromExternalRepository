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
 * Arquivo de implementação de relatórios
 * Data de Criação: 25/07/2005

 * @author Analista: Cassiano
 * @author Desenvolvedor: Cassiano

 Casos de uso: uc-01.06.99

 $Id: relatorioProcesso.php 66029 2016-07-08 20:55:48Z carlos.silva $

 */

include_once '../../../pacotes/FrameworkHTML.inc.php';
include_once '../../../framework/include/cabecalho.inc.php';   //Insere o início da página html
include_once CAM_FRAMEWORK."legado/processosLegado.class.php"; //Insere a classe que manipula os dados do processo
include_once CAM_FRAMEWORK."legado/auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
include_once '../processos/interfaceProcessos.class.php';      //Inclui classe que contém a interface html
include_once CAM_FRAMEWORK."legado/funcoesLegado.lib.php";
include_once CAM_FRAMEWORK."legado/botoesPdfLegado.class.php";
include_once CAM_FRAMEWORK."legado/paginacaoLegada.class.php";
include_once CAM_FRAMEWORK."legado/mascarasLegado.lib.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";

$ordem            = $_REQUEST["ordem"];
$pagina           = $_REQUEST["pagina"];
$numCgm           = $_REQUEST["numCgm"];
$dataInicial      = $_REQUEST["dataInicial"];
$dataFinal        = $_REQUEST["dataFinal"];
$codProcesso      = $_REQUEST["codProcesso"];
$codClassificacao = $_REQUEST["codClassificacao"];
$codAssunto       = $_REQUEST["codAssunto"];
$resumo           = $_REQUEST["resumo"];
$codMasSetor      = $_REQUEST["codMasSetor"];
$stCodSituacao    = $_REQUEST["stCodSituacao"];

$obFormulario = new FormularioAbas;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda('UC-01.06.99');
$obFormulario->show();

?>
<script type="text/javascript">

//compara a data 1 com a data 2
//retorna 1 => data 1 maior que data 2
//        0 => datas iguais
//       -1 => data 1 menor que data 2
//        2 => numero errado de digitos
//hora => coloca a hora na comparacao
function compareDateTime(valDate1, valDate2, bhora)
{
    dateMask = "E";
    if (bhora == true) {
        if (valDate1.length < 18 || valDate2.length < 18) {
            return 2;
        }
    } else {
        if (valDate1.length < 10 || valDate2.length < 10) {
            return 2;
        }
    }
    var dia = "";
    var mes = "";
    var ano = "";
    var hora = "";
    var minuto1 = "";
    var minuto = "";
    if (bhora == true) {
        hora = valDate1.substring(13, 15);
        minuto = valDate1.substring(15);
        minuto1 = minuto;
    }
    if (dateMask == 'U') { //Americano
        mes = valDate1.substring(0, 2);
        dia = valDate1.substring(3, 5);
        ano = valDate1.substring(6, 10);
    } else if (dateMask == 'E') { //Europeu
        dia = valDate1.substring(0, 2);
        mes = valDate1.substring(3, 5);
        ano = valDate1.substring(6, 10);
   } else { //Geral
        ano = valDate1.substring(0, 4);
        mes = valDate1.substring(5, 7);
        dia = valDate1.substring(8, 10);
    }
    var somaTotal1 = parseInt(ano+mes+dia, 10);
    if (bhora == true) {
        somaTotal1 = parseInt(ano+mes+dia+hora+minuto, 10);
    }
    if (bhora == true) {
        hora = valDate2.substring(13, 15);
        minuto = valDate2.substring(15);
    }
    if (dateMask == 'U') { //Americano
        mes = valDate2.substring(0, 2);
        dia = valDate2.substring(3, 5);
        ano = valDate2.substring(6, 10);
    } else if (dateMask == 'E') { //Europeu
        dia = valDate2.substring(0, 2);
        mes = valDate2.substring(3, 5);
        ano = valDate2.substring(6, 10);
    } else { //Geral
        ano = valDate2.substring(0, 4);
        mes = valDate2.substring(5, 7);
        dia = valDate2.substring(8, 10);
    }
    var somaTotal2 = parseInt(ano+mes+dia, 10);
    if (bhora == true) {
        somaTotal2 = parseInt(ano+mes+dia+hora+minuto, 10);
    }

    somaTotal1 = somaTotal1+minuto1;
    somaTotal2 = somaTotal2+minuto;

    if (somaTotal1 == somaTotal2) {
        return 0;
    } else if (somaTotal1 > somaTotal2) {
        return 1;
    } else {
        return -1;
    }
}

//A função Valida() faz a verfificação dos campos, monte-a conforme a sua necessidade.
    function ValidaData()
    {
        var mensagem = "";
        var erro = false;
        var dataI;
        var dataF;

        dataI = document.frm.dataInicial.value;
        dataF = document.frm.dataFinal.value;

    //Não executar a busca sem que haja pelo menos um parâmetro informado
    if ( compareDateTime(dataI, dataF, false) == 1 ) {
        mensagem += "@Data inicial não pode ser maior que a data final.";
        erro = true;
    }
    if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return !(erro);
    }

    function procuraCGM()
    {
       var tmpTarget = document.frm.target;
       document.frm.action = "relatorioProcesso.php?<?=Sessao::getId()?>&ctrl=2";
       document.frm.target = 'oculto';
       document.frm.submit();
       document.frm.target = tmpTarget;
    }

    function preencheComZerosProcesso(mascara, campo, posicao, exercicio, tamanhocod, tamanho)
    {
        var expReg = new RegExp("[a-zA-Z0-9]","ig");
        var stComplemento   = '';
        var inInicio  = 0;
        var inFim     = 0;
        var sub       = "";
        var sub1      = "";
        var sub2      = "";
        var sub3      = "";
        //posicao == 'E' || posicao == 'D'
        if (posicao == 'E') {
            inInicio  = 0;
            inFim     = mascara.length - campo.value.length;
        } else {
            inInicio  = campo.value.length;
            inFim     = mascara.length;
        }

        for (var inCount=inInicio; inCount<inFim; inCount++) {
            if ( mascara.charAt(inCount).search(expReg) == -1 ) {
                stComplemento = stComplemento + mascara.charAt(inCount);
            } else {
                stComplemento = stComplemento + '0';
            }
        }
        tamanhocod = parseInt(tamanhocod , 10) + 1;

        if (posicao == 'E') {
            sub = document.frm.codProcesso.value.substring(tamanhocod , tamanho);
            sub2 = document.frm.codProcesso.value.substring(0, tamanhocod );
            sub3 = document.frm.codProcesso.value.substring(0, parseInt(tamanhocod , 10) - 1);
            sub1 = sub.length;
            campo.clear;
                if (sub1 == 3) {
                    campo.value = stComplemento + sub2 + "0" + sub;
                }
                if (sub1 == 2) {
                    campo.value = stComplemento + sub2 + "00" + sub;
                }
                if (sub1 == 1) {
                    campo.value = stComplemento + sub2 + "000" + sub;
                }
                if (sub1 == 0) {
                    campo.value = stComplemento + sub3 +"/"+  exercicio;
                }
        } else {
            campo.value = campo.value + stComplemento;
        }
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
<style>
    tr.zb td { background:#D0E4F2; }
</style>

<?php

    function removeZerosEsquerda($valor)
    {
        $inTotal = strlen($valor);
        $count = 0;
        for ($inKey=0; $inKey<=$inTotal; $inKey++) {
            if ($valor[$inKey] == 0) {
                $count++;
            } else {
                break;
            }
        }
        $valor = (string) $valor;
        $valor2 = substr($valor, $count);

        return $valor2;
    }

    $ctrl = $_REQUEST["ctrl"];
    if (!(isset($ctrl))) {
        $ctrl = 0;
    }

    $pagina = $_REQUEST["pagina"];
    if (isset($pagina)) {
        $ctrl = 1;
    }

    switch ($ctrl) {
        case 0:

        $anoExercicio      = pegaConfiguracao("ano_exercicio");
        $anoExercicioSetor = pegaConfiguracao("ano_exercicio");
    ?>
        <form name=frm action="relatorioProcesso.php?<?=Sessao::getId()?>&ctrl=0" method="post">
        <table width="100%">
            <tr>
                <td class=alt_dados colspan="2">
                    Dados para filtro
                </td>
            </tr>
            <tr>
                <td class=label width="30%" title="Número do processo">
                    Processo
                </td>
                <td class=field width="70%">
    <?php
                    $mascaraProcesso = pegaConfiguracao("mascara_processo",5);
                    $tamanho = strlen($mascaraProcesso);
                    $arr = explode("/", $mascaraProcesso);
                    $tamanhocod = strlen($arr[0]);
    ?>
                    <input type="text" name="codProcesso" value="<?=$_POST['codProcesso'];?>"
                       size="<?=$tamanho+1?>" maxlength="<?=$tamanho?>"
                       onBlur="if (this.value != '') preencheComZerosProcesso('<?=$arr[0];?>', this, 'E', '<?=Sessao::getExercicio();?>','<?=$tamanhocod;?>','<?=$tamanho;?>');"
                       onKeyUp="mascaraDinamico('<?=$mascaraProcesso;?>', this, event);">
                </td>
            </tr>
    <?php include("../../../framework/legado/filtrosCALegado.inc.php"); ?>
        </table>

    <?php
        # ANTIGO COMPONENTE
        # include("../../../framework/legado/filtrosSELegado.inc.php");

        # NOVO COMPONENTE
        $obFormulario = new Formulario;
        $obFormulario->setLarguraRotulo(30);
        $obFormulario->addForm(null);

        $obIMontaOrganograma = new IMontaOrganograma(true);
        $obIMontaOrganograma->geraFormulario($obFormulario);

        $obFormulario->montaHTML();
        echo $obFormulario->getHTML();

    ?>
        <table width='100%'>
            <tr>
                <td width='25%' class=label title="Situação atual do processo">
                    Situação
                </td>
                <td class=field>
                    <?php
                        $selectS =  "SELECT
                                        cod_situacao,
                                        nom_situacao
                                    FROM
                                        sw_situacao_processo
                                    ORDER BY
                                        nom_situacao";
                        $dbConfig = new dataBaseLegado;
                        $dbConfig->abreBd();
                        $dbConfig->abreSelecao($selectS);
                        while (!$dbConfig->eof()) {
                            $codS = $dbConfig->pegaCampo("cod_situacao");

                            $listaS[$codS] =$dbConfig->pegaCampo("nom_situacao");
                            $dbConfig->vaiProximo();
                        }
                        $dbConfig->limpaSelecao();
                        $dbConfig->fechaBd();
                        if ($listaS != "") {
                            while (list($key, $val) = each($listaS)) {
                                $selected = "";
                                if ($codSituacao == $key) {
                                    $selected = "checked";
                                }
                                echo "<input type='checkbox' name='codSituacao[".$key."]' value=".$key." ".$selected.">".$val."<br>\n";

                            }
                        }
                    ?>
                </td>
            </tr>
           <tr>
                <td class=label title="Interessado pelo processo">
                    Interessado
                </td>
                <td class="field" width="60%">
                    <input type="text" size='8' maxlength='8' onKeyPress="return(isValido(this, event, '0123456789'))" name="numCgm" value='<?=$numCgm;?>' onblur="javascript: procuraCGM();">
                    <input type="text" name="nomCgm" size="30" value="<?=$nomCgm;?>"  readonly="">&nbsp;&nbsp;
                    <a href='javascript:procurarCgm("frm","numCgm","nomCgm","geral","<?=Sessao::getId()?>");'>
                        <img src="<?=CAM_FW_IMAGENS."procuracgm.gif"?>" alt="Procurar Interessado" width=22 height=22 border=0>
                    </a>
                </td>
            </tr>

                    <tr>
                        <td class='label'>
                            Periodo de inclusão
                        </td>
                        <td class='field'>
                            <?php geraCampoData("dataInicial", $dataInicial, false, "onKeyPress=\" return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida! ('+this.value+')','form','erro','Sessao::getId()');};\"" );?>&nbsp;a&nbsp;
                            <?php geraCampoData("dataFinal", $dataFinal, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');};\"" );?>
                        </td>
                    </tr>
                    <tr>
                        <td class=label>
                            Ordem
                        </td>
                        <td class=field>
                            <select name=ordem>
                                <option value="1">Código/Exercício</option>
                                <option value="2">Nome</option>
                                <option value="3">Classificação/Assunto</option>
                                <option value="4">Data</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class=field colspan="2">
                            <?=geraBotaoOk4();?>
                        </td>
                    </tr>
                </table>
            </form>

    <script type='text/javascript'>
    function Salvar()
    {
        if (ValidaData()) {
            document.frm.action = "relatorioProcesso.php?<?=Sessao::getId()?>&ctrl=1";
            document.frm.submit();
        }
    }
    </script>

    <?php
    break;

    case 1:
        $codOrgao = $_REQUEST["hdnUltimoOrgaoSelecionado"];
        $codProcesso = $_REQUEST["codProcesso"];
        if (isset($_REQUEST['codSituacao']) && is_array($_REQUEST['codSituacao'])) {
            $stCodSituacao = implode(".",$_REQUEST['codSituacao']);
        } else {
            $stCodSituacao = '';
        }

        ?>
        <script type="text/javascript">
           function SalvarRelatorio()
           {
                document.frm.action = "relProcesso.php?<?=Sessao::getId()?>&ctrl=1&ordem=<?=$ordem?>&codProcesso=<?=$codProcesso?>&codClassificacao=<?=$codClassificacao?>&codAssunto=<?=$codAssunto?>&resumo=<?=$resumo?>&codMasSetor=<?=$codMasSetor?>&codSituacao=<?=$stCodSituacao?>&numCgm=<?=$numCgm?>&dataInicial=<?=$dataInicial?>&dataFinal=<?=$dataFinal?>&codOrgao=<?=$codOrgao?>";
                document.frm.submit();
            }
        </script>
            <form name=frm action="relatorioProcesso.php?<?=Sessao::getId()?>&ctrl=1" method="post">
        <?php

        $verificador = false;
        $ok = true;
        $html = "
                <table width=100%  id='processos'>
                <tr>
                    <td class='alt_dados' colspan='11'>
                        Registros de processos
                    </td>
                </tr>
                <tr>
                    <td class='labelcenterCabecalho' width='5%' >&nbsp;</td>
                    <td class='labelcenterCabecalho' style='vertical-align : middle;'>Código</td>
                    <td class='labelcenterCabecalho' style='vertical-align : middle;'>Classificação</td>
                    <td class='labelcenterCabecalho' style='vertical-align : middle;'>Assunto</td>
                    <td class='labelcenterCabecalho' style='vertical-align : middle;'>Nome</td>
                    <td class='labelcenterCabecalho' style='vertical-align : middle;'>Setor</td>
                    <td class='labelcenterCabecalho' style='vertical-align : middle;'>Situação</td>
                    <td class='labelcenterCabecalho' style='vertical-align : middle;'>Data Inclusão</td>
                </tr>
                ";

        $sqlaux = "";
        if($vinculo=='1')
            $sqlaux = ", sw_processo_matricula as m";

        if($vinculo=='2')
            $sqlaux = ", sw_processo_inscricao as i";

        $sql  = "";
        $sql .= "
                 SELECT distinct
                        sw_processo.cod_processo
                      , sw_processo.ano_exercicio
                      , sw_processo_interessado.numcgm
                      , sw_cgm.nom_cgm
                      , sw_processo.cod_classificacao
                      , sw_processo.cod_assunto
                      , sw_classificacao.nom_classificacao
                      , sw_assunto.nom_assunto
                      , sw_processo.timestamp
                      , sw_ultimo_andamento.cod_orgao
                      , organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao)||' - '||
                        recuperaDescricaoOrgao(orgao.cod_orgao, sw_ultimo_andamento.timestamp::date) as nom_setor
                      , sw_situacao_processo.nom_situacao
                      , publico.fn_mascara_dinamica((select valor from administracao.configuracao
                where parametro = 'mascara_assunto' and cod_modulo = 5 and exercicio = '".Sessao::getExercicio()."'),cast(sw_processo.cod_classificacao as varchar)) as Classificacao

                                      , publico.fn_mascara_dinamica((select valor from administracao.configuracao
                where parametro = 'mascara_assunto' and cod_modulo = 5 and exercicio = '".Sessao::getExercicio()."'),cast(sw_processo.cod_classificacao as varchar))
                ||'.'||publico.fn_mascara_dinamica((select valor from administracao.configuracao
                where parametro = 'mascara_assunto' and cod_modulo = 5 and exercicio = '".Sessao::getExercicio()."'),cast(sw_processo.cod_assunto as varchar)) as ClaAss

                                      , publico.fn_mascara_dinamica((select valor from administracao.configuracao
                where parametro = 'mascara_processo' and cod_modulo = 5 and exercicio = '".Sessao::getExercicio()."'),
                cast(sw_processo.cod_processo || '/' || sw_processo.ano_exercicio as varchar)) as codigoProcesso

                                  FROM   sw_processo
                                      ,  sw_cgm
                                      ,  sw_classificacao
                                      ,  sw_assunto
                                      ,  sw_situacao_processo
                                      ,  sw_ultimo_andamento
                                      ,  organograma.orgao
                                      ,  organograma.orgao_nivel
                                      ,  sw_processo_interessado

                                  WHERE  sw_processo_interessado.cod_processo  = sw_processo.cod_processo
                                    AND  sw_processo_interessado.ano_exercicio = sw_processo.ano_exercicio
                                    AND  sw_processo_interessado.numcgm		   = sw_cgm.numcgm
                                    AND  sw_processo.cod_assunto			   = sw_assunto.cod_assunto
                                    AND  sw_processo.cod_classificacao		   = sw_assunto.cod_classificacao
                                    AND  sw_processo.cod_situacao			   = sw_situacao_processo.cod_situacao
                                    AND  sw_processo.cod_processo			   = sw_ultimo_andamento.cod_processo
                                    AND  sw_processo.ano_exercicio			   = sw_ultimo_andamento.ano_exercicio
                                    AND  sw_assunto.cod_classificacao		   = sw_classificacao.cod_classificacao
                                    AND  sw_ultimo_andamento.cod_orgao		   = orgao.cod_orgao
                                    AND  orgao.cod_orgao					   = orgao_nivel.cod_orgao
                                ";

    if ($codProcesso != "") {
        $codProcesso = preg_split("[/]", $codProcesso);
        $sql .= " AND sw_processo.cod_processo = ".$codProcesso[0]." ";
    }

    if ($codProcesso[1] != "") {
        $sql .= " AND sw_processo.ano_exercicio = '".$codProcesso[1]."' ";
    }

    // FILTRA PELO ASSUNTO REDUZIDO
    $resumo = $_REQUEST["resumo"];
    if ($resumo) {
        $resumo = str_replace ("*", "%", $resumo);
        $sql .= " AND sw_processo.resumo_assunto ilike ('%".$resumo."%') ";
    }

    $codClassificacao = $_REQUEST["codClassificacao"];
    if($codClassificacao!='0' && $codClassificacao != "" && $codClassificacao != 'xxx')
        $sql .= " AND sw_processo.cod_classificacao = '".$codClassificacao."' ";

    $codAssunto = $_REQUEST["codAssunto"];
    if($codAssunto!='0' && $codAssunto != "" && $codAssunto != 'xxx')
        $sql .= " AND sw_processo.cod_assunto = '".$codAssunto."' ";

    $codSituacao = $_REQUEST["codSituacao"];
    if (is_array($codSituacao)) {
        $sql .= " AND ( ";
        $verS =  count($codSituacao);

        while (list($key, $val) = each($codSituacao)) {
            $sql .= " sw_processo.cod_Situacao = '".$val."' OR ";
        }

        $sql = substr( $sql , 0 , (strlen($sql) - 3));
        $sql .= ")";
    }

    $numCgm = $_REQUEST["numCgm"];

    if (strlen($numCgm) > 0)
        $sql .= " AND sw_processo_interessado.numcgm = '".$numCgm."' ";

    //Filtra pelo exercicio do setor
    $exercioSetor = $_REQUEST["exercioSetor"];
    $chaveSetor   = $_REQUEST["chaveSetor"];
    if (strlen($exercioSetor)==0) {
        $exercioSetor = pegaConfiguracao("ano_exercicio");
    } elseif (isset($chaveSetor)) {
        $sql .= " AND exercicioSetor = '".$exercicioSetor."' ";
    }

    $codOrgao = $_REQUEST["hdnUltimoOrgaoSelecionado"];
    if ($codOrgao != "xxx" and $codOrgao != "") {
         $vetSetor = explode("-", $codOrgao);
        if ($vetSetor) {
             $sql .= " AND sw_ultimo_andamento.cod_orgao = '".$vetSetor[0]."'";
        } else {
            exibeAviso("Orgao inválido","unica","aviso");
            $ok = false;
        }
    }

    $stSubTituloPeriodo = "";
    //Filtra pelo período
    $dataInicial = $_REQUEST["dataInicial"];
    $dataFinal   = $_REQUEST["dataFinal"];
    if ( (strlen($dataInicial)>0) and (strlen($dataFinal)==0) ) {
        $dtInicial = dataToSql($dataInicial);
        $sql .= " AND sw_processo.timestamp >= '".$dtInicial."' ";
        $stSubTituloPeriodo = "Período: ".$dataInicial;
    } elseif ( (strlen($dataInicial)==0) and (strlen($dataFinal)>0) ) {
        $dtFinal = dataToSql($dataFinal);
        $sql .= " AND sw_processo.timestamp <= '".$dtFinal."' ";
        $stSubTituloPeriodo = "Período: ".$dataFinal;
    } elseif ( (strlen($dataInicial)>0) and (strlen($dataFinal)>0) ) {
        $dtInicial = dataToSql($dataInicial);
        $dtFinal = dataToSql($dataFinal)." 23:59:59.999";
        $sql .= " AND sw_processo.timestamp Between '".$dtInicial."' and '".$dtFinal."' ";
        $stSubTituloPeriodo = "Período: ".$dataInicial." a ".$dataFinal;
    }

    $st_ordenacao = array(
         1 => "sw_processo.ano_exercicio, sw_processo.cod_processo",
         2 => "sw_cgm.nom_cgm",
         3 => "sw_classificacao.nom_classificacao, sw_assunto.nom_assunto, sw_processo.ano_exercicio, sw_processo.cod_processo",
         4 => "sw_processo.timestamp");

    $stOrderBy = "";
    if ($ordem != '') {
        Sessao::write('sSQLs',$sql);
        Sessao::write('ordem',$ordem);
        Sessao::write('arOrdem',$st_ordenacao);
    }

    $stOrderBy = " ".$st_ordenacao[Sessao::read('ordem')];
    $paginacao = new paginacaoLegada;
    $paginacao->pegaDados(Sessao::read('sSQLs'), "10");
    $paginacao->pegaPagina($pagina);
    $paginacao->complemento = "&numCgm=$numCgm&dataInicial=$dataInicial&dataFinal=$dataFinal&codProcesso=$codProcesso&codClassificacao=$codClassificacao&codAssunto=$codAssunto&resumo=$resumo&codMasSetor=$codMasSetor&stCodSituacao=$stCodSituacao&codSituacao=$codSituacao&anoExercicioSetor=$anoExercicioSetor&codOrgao=$codOrgao&codSetor=$codSetor&codDepartamento=$codDepartamento&codUnidade=$codUnidade&hdnUltimoOrgaoSelecionado=$codOrgao";
    $paginacao->geraLinks();

    $paginacao->pegaOrder($stOrderBy, "ASC");

    $sSQL = $paginacao->geraSQL();
    $dbEmp = new dataBaseLegado; ;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $sqlPDF .= Sessao::read('sSQLs'). " order by " . $stOrderBy;
    $dbEmp->fechaBD();
    $dbEmp->vaiPrimeiro();

    if($dbEmp->numeroDeLinhas==0)
        $ok = false;
    if ($ok) {

        print '
            <table id=paginacao width="100">
                <tr>
                    <td class="labelcenterTable" title="Salvar Relatório">
                    <a href="javascript:SalvarRelatorio();"><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a>
                </tr>
            </table>
        ';

        $count = $paginacao->contador();
        while (!$dbEmp->eof()) {
            $processo            = $dbEmp->pegaCampo("cod_processo")."/".$dbEmp->pegaCampo("ano_exercicio");
            $codAssunto          = $dbEmp->pegaCampo("cod_assunto");
            $assunto             = $dbEmp->pegaCampo("nom_assunto");
            $codProc             = $dbEmp->pegaCampo("cod_processo");
            $anoEx               = $dbEmp->pegaCampo("ano_exercicio");
            $codClassificacao    = $dbEmp->pegaCampo("cod_classificacao");
            $classificacao       = $dbEmp->pegaCampo("nom_classificacao");
            $numcgm              = $dbEmp->pegaCampo("numcgm");
            $nomUsuario          = $dbEmp->pegaCampo("nom_cgm");
            $dataInclusao        = $dbEmp->pegaCampo("timestamp");
            $hora                = timestampToBr($dataInclusao,"hs");
            $codOrgao            = $dbEmp->pegaCampo("cod_orgao");
            $situacao            = $dbEmp->pegaCampo("nom_situacao");
            $nom_setor           = $dbEmp->pegaCampo("nom_setor");

            $dbEmp->vaiProximo();
            $codProcesso        = explode("/", $processo);

            //mascara para classificacao e assunto
            $mascaraClaAss = pegaConfiguracao('mascara_assunto',5);
            $arCodClaAss   = validaMascaraDinamica($mascaraClaAss, $codClassificacao."-".$codAssunto);
            $codClaAss     = $arCodClaAss[1];
            $codCla        = explode(".", $arCodClaAss[1]);

            //mascara para o processo
            $mascaraProcesso = pegaConfiguracao('mascara_processo',5);
            $arCodProcesso =  validaMascaraDinamica($mascaraProcesso, $codProc."-".$anoEx);
            $codProcesso   = $arCodProcesso[1];

            // mascara o codigo do Setor
            $mascaraSetor = pegaConfiguracao('mascara_setor',2);
            $stCodSetor = $codOrgao.".".$codUnidade.".".$codDepartamento.".".$codSetor."/".$anoExercicioSetor;
            $arCodSetor = validaMascara($mascaraSetor,$stCodSetor);

            $html .= "<tr>";
            $html .= "<td class=labelcenterTable>".$count++."</td>\n";
            $html .= "<td class=show_dados_right>".$codProcesso."</td>\n";
            $html .= "<td class=show_dados>".$codCla[0]." ".$classificacao."</td>\n";
            $html .= "<td class=show_dados>".$arCodClaAss[1]." ".$assunto."</td>\n";
            $html .= "<td class=show_dados>".$numcgm." ".$nomUsuario."</td>\n";
            $html .= "<td class=show_dados>".$arCodSetor[1]." ".$nom_setor."</td>\n";
            $html .= "<td class=show_dados>".$situacao."</td>\n";
            $html .= "<td class=show_dados>".timestamptobr($dataInclusao)." - ".$hora."</td>\n";
            $html .= "</tr>";
        }
    }
    $dbEmp->limpaSelecao();
    $html .= "</table>";

if(!$ok)
    echo "<br><b><span class='itemText'>Nenhum registro encontrado!</span></b><br><br>";
else
    echo $html;
    echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
    echo "</font></tr></td></table>";

    break;
        case 2:

           $numCgm = $_REQUEST['numCgm'];

           if ($numCgm != "") {
               $select =   "SELECT
                               nom_cgm
                           FROM
                               sw_cgm
                           WHERE
                               numcgm = ".$numCgm;
               $dbConfig = new dataBaseLegado;
               $dbConfig->abreBd();
               $dbConfig->abreselecao($select);
               $nomCgm = $dbConfig->pegaCampo("nom_cgm");
               $dbConfig->limpaSelecao();
               $dbConfig->fechaBd();
           }
            $js .=  "f.nomCgm.value = \"".$nomCgm."\";\n";
            sistemaLegado::executaframeoculto($js);

        break;

        case 100:
            $valor = $_REQUEST['valor'];
            $codClassificacao = $_REQUEST['codClassificacao'];
            $codAssunto = $_REQUEST['codAssunto'];
            $codOrgao = $_REQUEST['hdnUltimoOrgaoSelecionado'];
            $nomOrgao = $_REQUEST['nomOrgao'];
            $codDepartamento = $_REQUEST['codDepartamento'];
            $nomDepartamento = $_REQUEST['nomDepartamento'];
            $codUnidade = $_REQUEST['codUnidade'];
            $nomUnidade = $_REQUEST['nomUnidade'];
            $codSetor = $_REQUEST['codSetor'];
            $nomSetor = $_REQUEST['nomSetor'];
            include(CAM_FW_LEGADO."filtrosCASELegado.inc.php");
        break;

}
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
<script>zebra('processos','zb');</script>
