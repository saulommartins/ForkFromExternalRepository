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

 $Id: despachaProcesso.php 66029 2016-07-08 20:55:48Z carlos.silva $

 */

include '../../../framework/include/cabecalho.inc.php';
include '../../../framework/legado/mascarasLegado.lib.php';
include '../../../framework/legado/funcoesLegado.lib.php';
include '../../../framework/legado/paginacaoLegada.class.php';
include '../../../framework/legado/processosLegado.class.php';
include CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";

setAjuda('uc-01.06.98');

$ctrl             = $_REQUEST["ctrl"];
$pagina           = $_REQUEST["pagina"];
$codAndamento     = $_REQUEST['codAndamento'];
$codProcesso      = $_REQUEST['codProcesso'];
$anoExercicio     = $_REQUEST['anoExercicio'];
$nomClassificacao = $_REQUEST['nomClassificacao'];
$codClassificacao = $_REQUEST['codClassificacao'];
$nomAssunto       = $_REQUEST['nomAssunto'];
$descricao        = $_REQUEST['descricao'];
$chave            = $_REQUEST['chave'];
$anoE             = $_REQUEST['anoE'];
$numCgm           = $_REQUEST['numCgm'];
$stChaveProcesso  = $_REQUEST['stChaveProcesso'];
$codAssunto       = $_REQUEST['codAssunto'];
$dataInicio       = $_REQUEST["dataInicio"];
$dataTermino      = $_REQUEST["dataTermino"];

$mascaraProcesso = pegaConfiguracao("mascara_processo", 5);

if (!(isset($ctrl))) {
    $ctrl = 0;
}

$verifica = $_REQUEST["verifica"];
if ($verifica == true) {
    $ctrl = 1;
}

if ($ctrl == 1) {
    $ctrl = 1;
    $verifica = false;
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
        document.frm.action = "despachaProcesso.php?<?=Sessao::getId()?>&ctrl=1";
        document.frm.submit();
    }
</script>
<?php

switch ($ctrl) {
case 0:
    $stAuxNome  = "ctrl";
    $stAuxValor = "1";
    include '../../../framework/legado/filtrosProcessoLegado.inc.php';
break;

case 1:
    if (is_array(Sessao::read('vet'))) {
        $vet = Sessao::read('vet');
        foreach ($vet AS $indice => $valor) {
            $$indice = $valor;
        }
    }

    $inCodOrganograma = SistemaLegado::pegaDado('cod_organograma', 'organograma.vw_orgao_nivel', ' WHERE cod_orgao = '.Sessao::read('codOrgao'));
    $boPermissaoHierarquica = SistemaLegado::pegaDado('permissao_hierarquica', 'organograma.organograma', ' WHERE cod_organograma = '.$inCodOrganograma);

    $sql  = " SELECT DISTINCT sw_processo.ano_exercicio                                                 \n";
    $sql .= "     , sw_processo.cod_processo                                                            \n";
    $sql .= "     , sw_processo.timestamp                                                               \n";
    $sql .= "     , sw_ultimo_andamento.cod_andamento                                                   \n";
    $sql .= "     , sw_classificacao.nom_classificacao                                                  \n";
    $sql .= "     , sw_assunto.nom_assunto                                                              \n";
    $sql .= "     , array_to_string(array_agg(nom_cgm), ', ')as nom_cgm                                 \n";
    $sql .= "     , ( EXISTS ( SELECT 1 FROM SW_DESPACHO WHERE COD_PROCESSO = sw_processo.cod_processo AND ANO_EXERCICIO = sw_processo.ano_exercicio ) ) as despacho \n";
    $sql .= "     , ( EXISTS ( SELECT 1 FROM SW_PROCESSO_APENSADO WHERE COD_PROCESSO_PAI = sw_processo.cod_processo AND EXERCICIO_PAI = sw_processo.ano_exercicio AND TIMESTAMP_DESAPENSAMENTO IS NULL ) ) as apenso \n";

    $sql .= "  FROM  sw_processo                                                                        \n";

    $sql .= "      INNER JOIN  sw_processo_interessado                                                  \n";
    $sql .= "              ON  sw_processo_interessado.cod_processo = sw_processo.cod_processo          \n";
    $sql .= "             AND  sw_processo_interessado.ano_exercicio = sw_processo.ano_exercicio        \n";

    $sql .= "      INNER JOIN  sw_assunto                                                               \n";
    $sql .= "              ON  sw_assunto.cod_assunto       = sw_processo.cod_assunto                   \n";
    $sql .= "             AND  sw_assunto.cod_classificacao = sw_processo.cod_classificacao             \n";

    $sql .= "      INNER JOIN  sw_classificacao                                                         \n";
    $sql .= "              ON  sw_assunto.cod_classificacao = sw_classificacao.cod_classificacao        \n";

    $sql .= "      INNER JOIN  sw_cgm                                                                   \n";
    $sql .= "              ON  sw_cgm.numcgm = sw_processo_interessado.numcgm                           \n";

    $sql .= "      INNER JOIN  sw_situacao_processo                                                     \n";
    $sql .= "              ON  sw_processo.cod_situacao  = sw_situacao_processo.cod_situacao            \n";

    $sql .= "      INNER JOIN  sw_ultimo_andamento                                                      \n";
    $sql .= "              ON  sw_processo.ano_exercicio = sw_ultimo_andamento.ano_exercicio            \n";
    $sql .= "             AND  sw_processo.cod_processo  = sw_ultimo_andamento.cod_processo             \n";

    $sql .= "       LEFT JOIN  sw_assunto_atributo_valor                                                \n";
    $sql .= "              ON  sw_assunto_atributo_valor.cod_processo  = sw_processo.cod_processo       \n";
    $sql .= "             AND  sw_assunto_atributo_valor.exercicio     = sw_processo.ano_exercicio      \n";
    $sql .= "           WHERE  1=1                                                                      \n";

    $sql .= "     AND sw_situacao_processo.cod_situacao        = 3                                      \n";

    $sql .= "     AND  sw_ultimo_andamento.cod_orgao IN (SELECT cod_orgao
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
        $dataInicioAux = $arrData[2]."-".$arrData[1]."-".$arrData[0];
        $arrData     = explode("/", $dataTermino);
        $dataTerminoAux   = $arrData[2]."-".$arrData[1]."-".$arrData[0];
        $sql .= " AND SUBSTR(sw_processo.timestamp::varchar,1,10) >= '".$dataInicioAux."'";
        $sql .= " AND SUBSTR(sw_processo.timestamp::varchar,1,10) <= '".$dataTerminoAux."'";

        # $vet["dataInicio"]  = $dataInicio;
        # $vet["dataTermino"] = $dataTermino;
    }

    //FILTRO POR ATRIBUTO DE ASSUNTO
    if ($_REQUEST['valorAtributoTxt']) {
        foreach ($_REQUEST['valorAtributoTxt'] as $key => $value) {
            if ($_REQUEST['valorAtributoTxt'][$key]) {
                $sql .= " AND sw_assunto_atributo_valor.valor ILIKE ( '%".$_REQUEST['valorAtributoTxt'][$key]."%' ) \n";
                $sql .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
            }
        }
    }
    if ($_REQUEST['valorAtributoNum']) {
        foreach ($_REQUEST['valorAtributoNum'] as $key => $value) {
            if ($_REQUEST['valorAtributoNum'][$key]) {
                $sql .= " AND sw_assunto_atributo_valor.valor = '".$_REQUEST['valorAtributoNum'][$key]."' \n";
                $sql .= " AND sw_assunto_atributo_valor.cod_atributo = '".$key."' \n";
            }
        }
    }
    if ($_REQUEST['valorAtributoCmb']) {
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
    Sessao::write('sSQLs',$sql);

    $st_ordenacao = array(1 => "sw_processo.ano_exercicio
                              , sw_processo.cod_processo",
                          2 => "sw_cgm.nom_cgm",
                          3 => "sw_classificacao.nom_classificacao
                              , sw_assunto.nom_assunto
                              , sw_processo.ano_exercicio
                              , sw_processo.cod_processo",
                          4 => "sw_processo.timestamp");

    Sessao::write('vet',$vet);

    if (Sessao::read('ordem') =='') {
        Sessao::write('ordem',$_REQUEST["ordem"]);
    }

    if ($_REQUEST["ordem"]=='') {
        Sessao::write('sSQLs',$sql);
    }

    $paginacao = new paginacaoLegada;
    $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
    $paginacao->pegaPagina($pagina);
    $paginacao->complemento = "&ctrl=1&codProcessoFl=".$codProcessoFl."&codClassificacao=".$codClassificacao."&codAssunto=".$codAssunto."&numCgm=".$numCgm."&dataInicio=".$dataInicio."&dataTermino=".$dataTermino."&ordem=".$ordem;
    $paginacao->geraLinks();
    $paginacao->pegaOrder($st_ordenacao[Sessao::read('ordem')], "ASC");
    $sSQL = $paginacao->geraSQL();
    $count = $paginacao->contador();

    $dbConfig = new databaseLegado;
    $dbConfig->abreBd();
    $dbConfig->abreSelecao($sSQL);

    echo "<table width='100%' id='processos'>\n";
    echo "	<tr>\n";
    echo "		<td class=alt_dados colspan=11>Registros de processos</td>\n";
    echo "	</tr>\n";
    echo "	<tr>\n";
    echo "  <tr>\n";
    echo "      <td class='labelcenterCabecalho' width='5%'>&nbsp;</td>                              \n";
    echo "      <td class='labelcenterCabecalho' style='vertical-align : middle;'>Código</td>        \n";
    echo "      <td class='labelcenterCabecalho' style='vertical-align : middle;'>Interessado</td>   \n";
    echo "      <td class='labelcenterCabecalho' style='vertical-align : middle;'>Classificação</td> \n";
    echo "      <td class='labelcenterCabecalho' style='vertical-align : middle;'>Assunto</td>       \n";
    echo "      <td class='labelcenterCabecalho' style='vertical-align : middle;'>Inclusão</td>      \n";
    echo "      <td class='labelcenterCabecalho' style='vertical-align: middle;'>Despacho</td>      \n";
    echo "      <td class='labelcenterCabecalho' style='vertical-align: middle;'>Apenso</td>        \n";
    echo "      <td class='labelcenterCabecalho' >&nbsp;</td>                                        \n";
    echo "      <td class='labelcenterCabecalho' >&nbsp;</td>                                        \n";
    echo "  </tr>                                                                           \n";

    while (!($dbConfig->eof())) {
        $codProcessoImprime  = $dbConfig->pegaCampo("cod_processo");
        $anoExercicioImprime = $dbConfig->pegaCampo("ano_exercicio");
        $codProcessoMascara = mascaraProcesso($codProcessoImprime, $anoExercicioImprime);
        $stDespacho  = $dbConfig->pegaCampo("despacho") == "t" ? "Sim" : "Não";
        $stApenso    = $dbConfig->pegaCampo("apenso") == "t" ? "Sim" : "Não";
        echo "	<tr>\n";
        echo "		<td class=show_dados_center_bold>".$count++."</td>\n";
        echo "		<td class=show_dados>".$codProcessoMascara."</td>\n";

        echo "		<td class=show_dados>".$dbConfig->pegaCampo("nom_cgm")."</td>\n";
        echo "		<td class=show_dados>".$dbConfig->pegaCampo("nom_classificacao")."</td>\n";
        echo "		<td class=show_dados>".$dbConfig->pegaCampo("nom_assunto")."</td>\n";
        echo "		<td class=show_dados_center>".timestamptobr($dbConfig->pegaCampo("timestamp"))."</td>\n";
        echo "		<td class=show_dados_center>".$stDespacho."</td>\n";
        echo "		<td class=show_dados_center>".$stApenso."</td>\n";
        
        echo "		<td class=botao width='2%'>\n";
        echo "		<a href='consultaProcesso.php?".Sessao::getId()."&ctrl=2";
        echo "&pagina=".$pagina."&codProcesso=".$dbConfig->pegaCampo("cod_processo");
        echo "&anoExercicio=".$dbConfig->pegaCampo("ano_exercicio")."&dataInicial=".$dataInicio."&dataFinal=".$dataTermino."&verificador=true&stChaveProcesso=".$_REQUEST['stChaveProcesso']."&numCgm=".$_REQUEST['numCgm']."' ";
        echo "title='Consultar Processo'>\n";
        echo "		<img src='".CAM_FW_IMAGENS."procuracgm.gif' alt='Procurar Usuário' ";
        echo "border=0></a></td>\n";
        echo "		<td class=botao>\n";
        //Link com os dados sendo passados por Get
        echo "		<a href='despachaProcesso.php?".Sessao::getId()."&ctrl=2";
        echo "&codAndamento=".$dbConfig->pegaCampo("cod_andamento");
        echo "&codProcesso=".$dbConfig->pegaCampo("cod_processo");
        echo "&anoExercicio=".$dbConfig->pegaCampo("ano_exercicio");
        echo "&nomClassificacao=".$dbConfig->pegaCampo("nom_classificacao");
        echo "&nomAssunto=".$dbConfig->pegaCampo("nom_assunto");
        echo "&ordem=".$ordem;
        echo "&pagina=".$pagina."&numCgm=".$_REQUEST['numCgm']."&stChaveProcesso=".$_REQUEST['stChaveProcesso']."&codClassificacao=".$_REQUEST['codClassificacao']."'";
        echo "title='Despachar Processo'>\n";
        echo "<img src='".CAM_FW_IMAGENS."botao_despachar.png' border=0></a></td>\n";
        echo "	</tr>\n";
        $dbConfig->vaiProximo();
    }

    $dbConfig->limpaSelecao();
    $dbConfig->fechaBd();

    if ($dbConfig->numeroDeLinhas == 0) {
        $exec .=  "<b>Não Existem Processos a Despachar!</b>";
    }
    echo "</table>";
    echo "$exec";

    echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
    echo "</font></tr></td></table>";

    //Deleta diretorios e arquivos que possam ter ficado na pasta TMP, ou se o usuario cancelou o despacho do processo.
    if ($_REQUEST['deletaDiretorios'] == true) {
        //sistema cria pasta de acordo com a sessao.
        $diretorio = CAM_PROTOCOLO."tmp/".$_REQUEST['iURLRandomica'];
        if (file_exists($diretorio)) {
            foreach (scandir($diretorio) as $pasta) {
            if ($pasta == "." || $pasta == "..") {
                continue;
            } else {
                $subPasta = $diretorio."/".$pasta;
                foreach (scandir($subPasta) as $arquivo) {
                    if ($arquivo == "." || $arquivo == "..") {
                        continue;
                    } else {
                        unlink($subPasta."/".$arquivo);
                    }
                }
            }
            rmdir($diretorio."/".$pasta);
        }
        rmdir($diretorio);
        }
    }

?>
    <script>zebra('processos','zb');</script>
<?php
break;

       case 2:
           //Buscando os Despachos cadastrados
           $select  = 	"SELECT \n".$quebra;
           $select .= 	"	D.descricao, \n".$quebra;
           $select .= 	"	TO_CHAR(D.timestamp,'DD/MM/YYYY (HH:MI)') AS data \n".$quebra;
           $select .=	"FROM \n".$quebra;
           $select .=	"	sw_despacho            AS D,\n".$quebra;
           $select .=	"	sw_ultimo_andamento AS UA \n".$quebra;
           $select .=	"WHERE \n".$quebra;
           $select .=	"	D.cod_andamento = ".$codAndamento." AND \n".$quebra;
           $select .=	"	D.cod_processo  = ".$codProcesso."  AND \n".$quebra;
           $select .=	"	D.ano_exercicio = '".$anoExercicio."' AND \n".$quebra;
           $select .=	"	D.cod_usuario   = ".Sessao::read('numCgm')."   AND \n".$quebra;
           $select .=	"	D.cod_andamento = UA.cod_andamento  AND \n".$quebra;
           $select .=	"	D.cod_processo  = UA.cod_processo; \n".$quebra;

           $dbConfig = new databaseLegado;
           $dbConfig->abreBd();
           $dbConfig->abreSelecao($select);

           $descricao   = $dbConfig->pegaCampo("descricao");

           $dbConfig->limpaSelecao();
           $dbConfig->fechaBd();

           //Buscando os dados do processo selecionado
           $select  =	"SELECT \n".$quebra;
           $select .=	"	observacoes \n".$quebra;
           $select .=	"FROM \n".$quebra;
           $select .=	"	sw_processo \n".$quebra;
           $select .=	"WHERE \n".$quebra;
           $select .=	"	cod_processo  = ".$codProcesso."  AND \n".$quebra;
           $select .=	"	ano_exercicio = '".$anoExercicio."';  \n".$quebra;

           $dbConfig = new databaseLegado;
           $dbConfig->abreBd();
           $dbConfig->abreSelecao($select);

           $observacoes = $dbConfig->pegaCampo("observacoes");

           $observacoes_min = substr($observacoes,0,100);

           $dbConfig->limpaSelecao();
           $dbConfig->fechaBd();
?>

<script type="text/javascript">
   function Valida()
   {
       var mensagem = "";
       var erro = false;
       var campo;
       var campoaux;

       campo = document.frm.descricao.value.length;
           if (campo == 0) {
           mensagem += "@O campo Descrição é obrigatório";
           erro = true;
       }
       if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
           return !(erro);
   }
       function Salvar()
       {
           if (Valida()) {
               document.frm.action = "despachaProcesso.php?<?=Sessao::getId()?>&ctrl=3&pagina=<?=$pagina?>numCgm=<?=$_REQUEST['numCgm']?>&stChaveProcesso=<?=$_REQUEST['stChaveProcesso']?>&codClassificacao=<?=$_REQUEST['codClassificacao']?>&dataInicio=<?=$_REQUEST['dataInicio']?>&dataFinal=<?=$_REQUEST['dataFinal']?>";
               document.frm.submit();
           }
       }

       function copiaDigital(cod, acao, codProcesso, anoExercicio)
       {
            var x = 200;
            var y = 140;
            var sArq = '<?=CAM_GA_PROT_POPUPS."documento/FMDocumentoProcesso.php";?>?<?=Sessao::getId();?>&codDoc='+cod+'&acao='+acao+'&inCodProcesso='+codProcesso+'&stAnoProcesso='+anoExercicio;
            var wVolta=false;
            tela = window.open(sArq,'tela','titlebar=no,hotkeys=no,width=550px,height=320px,resizable=1,scrollbars=1,left='+x+',top='+y);
            window.tela.focus();

       }

       function Cancela()
       {
           mudaTelaPrincipal("despachaProcesso.php?<?=Sessao::getId()?>&pagina=<?=$pagina?>&ctrl=1&numCgm=<?=$_REQUEST['numCgm']?>&stChaveProcesso=<?=$_REQUEST['stChaveProcesso']?>&codClassificacao=<?=$_REQUEST['codClassificacao']?>&dataInicio=<?=$_REQUEST['dataInicio']?>&dataFinal=<?=$_REQUEST['dataFinal']?>&deletaDiretorios=<?=true?>");
       }

</script>

<form name=frm action="despachaProcesso.php?<?=Sessao::getId()?>&ctrl=3" method="post">
   <table width="100%">
       <tr>
           <td class=alt_dados colspan="2">Dados do Processo</td>
       </tr>

       <tr>
           <td class=label width="30%">Código</td>
           <td class=field width="70%">
           <?php
               $codProcessoMascara = mascaraProcesso($codProcesso, $anoExercicio);
               echo $codProcessoMascara;
           ?></td>
           <input type="hidden" name="codProcesso" value="<?=$codProcesso?>">
           <input type="hidden" name="codAndamento" value="<?=$codAndamento?>">
           <input type="hidden" name="anoE" value="<?=$anoExercicio?>">
           <input type="hidden" name="pagina" value="<?=$pagina?>">
           <input type="hidden" name="stChaveProcesso" value="<?=$stChaveProcesso?>">
           <input type="hidden" name="numCgm" value="<?=$numCgm?>">
           <input type="hidden" name="ctrl" value="3">
       </tr>
        <?php
            $select =   "SELECT
                            P.cod_usuario,
                            U.username,
                            TO_CHAR(P.timestamp,'DD/MM/YYYY (HH24:MI)') AS data,
                            P.cod_classificacao,
                            P.cod_assunto,
                            SP.nom_situacao
                        FROM
                            sw_processo AS P,
                            administracao.usuario  AS U,
                            sw_situacao_processo  AS SP
                        WHERE
                            P.cod_usuario   = U.numcgm         AND
                            P.cod_situacao  = SP.cod_situacao  AND
                            P.cod_processo  = ".$codProcesso." AND
                            P.ano_exercicio = '".$anoExercicio."' ";

            $dbConfig       = new databaseLegado;
            $dbConfig->abreBd();
            $dbConfig->abreSelecao($select);
            $dataInclusao   = $dbConfig->pegaCampo("data");
            $numcgmInclusao = $dbConfig->pegaCampo("cod_usuario");
            $nomcgmInclusao = $dbConfig->pegaCampo("username");
            $codClassificacao = $dbConfig->pegaCampo("cod_classificacao");
            $codAssunto = $dbConfig->pegaCampo("cod_assunto");
            $nomSituacao = $dbConfig->pegaCampo("nom_situacao");
            $dbConfig->limpaSelecao();
            $dbConfig->fechaBd();

            $mascaraAssunto = pegaConfiguracao('mascara_assunto',5);
            $arCodClassifAssunto =  validaMascaraDinamica($mascaraAssunto, $codClassificacao."-".$codAssunto);
            $codClassifAssunto   = $arCodClassifAssunto[1];
        ?>

       <tr>
           <td class=label>Classificação/Assunto</td>
            <td class=field><?=$codClassifAssunto; ?><br>
                            <?=$nomClassificacao?><br>
                            <?=$nomAssunto?></td>
        </tr>

        <tr>
            <td class=label>Observações</td>
            <td class=field>
            <?php
                $observacoes = str_replace("\n", "<br>", $observacoes);
                echo $observacoes
            ?>
            <!--<a href="#" onclick="mostraDadosProcesso('observacoes','<?=Sessao::getId();?>');">[mais]</a></td>-->
        </tr>

        <tr>
            <td class=label>Situação</td>
            <td class=field><?=$nomSituacao?></td>
        </tr>

        <tr>
            <td class=label>Data de inclusão</td>
            <td class=field><?=$dataInclusao?></td>
        </tr>

        <tr>
            <td class=label>Usuário que incluiu</td>
            <td class=field><?=$numcgmInclusao." - ".$nomcgmInclusao?></td>
        </tr>

<?php
            //Verifica se o documento cadastrado foi entregue
            $sSQL  =	"SELECT \n".$quebra;
            $sSQL .=	"	DP.cod_documento, \n".$quebra;
            $sSQL .=	"	cod_processo \n".$quebra;
            $sSQL .=	"FROM \n".$quebra;
            $sSQL .=	"	sw_documento_processo AS DP, \n".$quebra;
            $sSQL .=	"	sw_documento          AS D \n".$quebra;
            $sSQL .=	"WHERE \n".$quebra;
            $sSQL .=	"	DP.cod_documento = D.cod_documento   AND \n".$quebra;
            $sSQL .=	"	DP.cod_processo  = ".$codProcesso."  AND \n".$quebra;
            $sSQL .=	"	DP.exercicio     = '".$anoExercicio."' \n".$quebra;

            $dbConfig = new databaseLegado;
            $dbConfig->abreBd();
            $dbConfig->abreSelecao($sSQL);
            $i = 0;
            while (!$dbConfig->eof()) {
                $cod = $i;
                $lista_documentos_entregues[$cod] = $dbConfig->pegaCampo("cod_documento");
                $dbConfig->vaiProximo();
                $i++;
            }
            $dbConfig->limpaSelecao();
            $dbConfig->fechaBd();

            //Buscando os documentos cadastrados no assunto
            $select  =	"SELECT \n".$quebra;
            $select .=	"	DOC.cod_documento, \n".$quebra;
            $select .=	"	DOC.nom_documento \n".$quebra;
            $select .=	"FROM \n".$quebra;
            $select .=	"	sw_documento         AS DOC, \n".$quebra;
            $select .=	"	sw_documento_assunto AS DA, \n".$quebra;
            $select .=	"	sw_assunto           AS A, \n".$quebra;
            $select .=	"	sw_classificacao     AS C \n".$quebra;
            $select .=	"WHERE \n".$quebra;
            $select .=	"	DA.cod_documento       = DOC.cod_documento     AND \n".$quebra;
            $select .=	"	C.cod_classificacao    = A.cod_classificacao   AND \n".$quebra;
            $select .=	"	C.cod_classificacao    = DA.cod_classificacao  AND \n".$quebra;
            $select .=	"	DA.cod_classificacao   = A.cod_classificacao   AND \n".$quebra;
            $select .=	"	DA.cod_assunto         = A.cod_assunto         AND \n".$quebra;
            $select .=	"	C.cod_classificacao    = ".$codClassificacao." AND \n".$quebra;
            $select .=	"	A.cod_assunto          = ".$codAssunto." \n".$quebra;

            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $dbConfig->abreSelecao($select);
            echo "		<tr>\n";
            while (!($dbConfig->eof())) {
                $codDocumento                = $dbConfig->pegaCampo("cod_documento");
                $nomDocumento[$codDocumento] = $dbConfig->pegaCampo("nom_documento");

                if (is_array($lista_documentos_entregues)) {
                    //continue;
                } else {
                    $lista_documentos_entregues = array();
                }
                if (in_array($codDocumento,$lista_documentos_entregues)) {
                    echo "		<td class=label>Documentos</td>";
                    echo "		<td class=field>\n";
                    echo "<table>\n";
                    echo "	<tr>\n";
                    echo "		<td class=fieldcenter width='5%'>\n";
                    echo "			<input type='checkbox' name=cod_documentos[".$codDocumento."] checked disabled>\n";
                    echo "		</td>\n";
                    echo "		<td class=field>\n";
                    echo "			".$nomDocumento[$codDocumento]."<br>\n";
                    echo "		</td>\n";
                    echo "		<input type=hidden name=cod_documentos[".$codDocumento."] value=".$codDocumento.">";
                    echo "		<td width='10%' class=fieldright>\n";
                    echo "			<input type=button name=copiaDigitalB value='Copia Digital' onClick='javascript: copiaDigital(".$codDocumento.",".Sessao::read('acao').",".$codProcesso.",".$anoExercicio.");'>\n";
                    echo "		</td>\n";
                    echo "	</tr>\n";
                    echo "</table>\n";
                    echo "		</tr>\n";
                } else {
                    echo "		<td class=label>Documentos</td>";
                    echo "		<td class=field>\n";
                    echo "<table>\n";
                    echo "	<tr>\n";
                    echo "		<td class=field width='5%'>\n";
                    echo "			<input type='checkbox' name=cod_documentos[".$codDocumento."] value='".$codDocumento."'>\n";
                    echo "		</td>\n";
                    echo "		<td class=field>\n";
                    echo "			".$nomDocumento[$codDocumento]."<br>\n";
                    echo "		</td>\n";
                    echo "		<td width='10%' class=fieldright>\n";
                    echo "			<input type=button name=copiaDigitalB value='Copia Digital' onClick='javascript: copiaDigital(".$codDocumento.",".Sessao::read('acao').",".$codProcesso.",".$anoExercicio.");'>\n";
                    echo "		</td>\n";
                    echo "	</tr>\n";
                    echo "</table>\n";
                    //echo "			</td>\n";
                    echo "		</tr>\n";
                }
                $dbConfig->vaiProximo();
            }
            $dbConfig->limpaSelecao();
            $dbConfig->fechaBd();
?>

        <tr>
            <td class=label>*Descrição</td>
            <td class=field><textarea name="descricao" cols="38" rows="5"><?=$descricao?></textarea></td>
        </tr>

        <tr>
            <td class=field colspan="2">
                <?php echo geraBotaoAltera();?>
            </td>
        </tr>
    </table>
</form>
<?php
    break;

    case 3:
        $obj = "Despacho para Processo: ".$codProcesso."/".$anoE;

        $processo = new processosLegado;
        $processo->setaValorDespacho($codAndamento,$codProcesso,$anoE,Sessao::read('numCgm'),$descricao);
        if ($processo->insertDespacho()) {
            if ($processo->updateDocumento($_REQUEST['cod_documentos'],$codProcesso,$anoE)) {
                include CAM_FW_LEGADO."auditoriaLegada.class.php";
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $obj);
                $audicao->insereAuditoria();
                $chave = $anoE."-".$codAndamento."-".$codProcesso;
                echo 	'<script type="text/javascript">
                            alertaAviso("'.$obj.'","incluir","aviso", "'.Sessao::getId().'");
                            window.location = "despachaProcesso.php?'.Sessao::getId().'&ctrl=4&chave='.$chave.'";
                        </script>';
            } else {
                include CAM_FW_LEGADO."auditoriaLegada.class.php";
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $obj);
                $audicao->insereAuditoria();
                $chave = $anoE."-".$codAndamento."-".$codProcesso;
                echo 	'<script type="text/javascript">
                            alertaAviso("'.$obj.'","incluir","aviso", "'.Sessao::getId().'");
                            window.location = "despachaProcesso.php?'.Sessao::getId().'&ctrl=4&chave='.$chave.'";
                        </script>';
            }
        } else {
            $chave = $anoE."-".$codAndamento."-".$codProcesso;
            echo 	'<script type="text/javascript">
                        alertaAviso("'.$obj.'","n_incluir","erro", "'.Sessao::getId().'");
                        window.location = "despachaProcesso.php?'.Sessao::getId().'&ctrl=4&chave='.$chave.'";
                    </script>';
        }

    break;

    case 4:
        ?>
            <script type="text/javascript">
                function SalvarForm()
                {
                    document.frm.action = "despacho.php?<?=Sessao::getId()?>&ctrl=4&chave=<?=$chave?>";
                    document.frm.submit();
                }
            </script>
            <form name=frm action="despachaProcesso.php?<?=Sessao::getId()?>&ctrl=4" method="post">
        <?php

        include '../../../framework/legado/botoesPdfLegado.class.php';
        $variaveis = explode("-",$chave);
        $anoE = $variaveis[0];
        $codAndamento = $variaveis[1];
        $codProcesso = $variaveis[2];
        $codUsuario = Sessao::read('numCgm');

        $sSQL  = " SELECT                                                  \n";
        $sSQL .= "    P.COD_PROCESSO || '/' || P.ANO_EXERCICIO AS CHAVE,   \n";
        $sSQL .= "    P.COD_PROCESSO,                                      \n";
        $sSQL .= "    --P.NUMCGM,                                          \n";
        $sSQL .= "    CLA.NOM_CLASSIFICACAO,                               \n";
        $sSQL .= "    ASS.NOM_ASSUNTO,                                     \n";
        $sSQL .= "    --INT.NOM_CGM AS nom_interessado,                    \n";
        $sSQL .= "    SIT.NOM_SITUACAO,                                    \n";
        $sSQL .= "    A.COD_ORGAO,                                         \n";
        $sSQL .= "    CGM_DESP.NOM_CGM AS nom_usuario,                     \n";
        $sSQL .= "    TO_CHAR( D.TIMESTAMP, 'DD/MM/YYYY' ) AS DATA_DESP,   \n";
        $sSQL .= "    D.TIMESTAMP,                                         \n";
        $sSQL .= "    D.DESCRICAO                                          \n";
        $sSQL .= " FROM                                                    \n";
        $sSQL .= "    SW_PROCESSO        AS P,                             \n";
        $sSQL .= " --SUB_TABELAS DO PROCESSO                               \n";
        $sSQL .= "      SW_CLASSIFICACAO     AS CLA,                       \n";
        $sSQL .= "      SW_ASSUNTO           AS ASS,                       \n";
        $sSQL .= "      --SW_CGM               AS INT,--INTERESSADO        \n";
        $sSQL .= "      SW_SITUACAO_PROCESSO AS SIT,                       \n";
        $sSQL .= " --FIM SUB TABELAS                                       \n";
        $sSQL .= "    SW_ANDAMENTO       AS A,                             \n";
        $sSQL .= " --SUB_TABELAS DE ANDAMENTO                              \n";
        $sSQL .= " --FIM SUB_TABELAS                                       \n";
        $sSQL .= "    SW_DESPACHO        AS D,                             \n";
        $sSQL .= " --SUB_TABELAS DE DESPACHO                               \n";
        $sSQL .= "      ADMINISTRACAO.USUARIO AS US_DESP,                  \n";
        $sSQL .= "      SW_CGM  AS CGM_DESP                                \n";
        $sSQL .= " WHERE                                                   \n";
        $sSQL .= "    P.COD_PROCESSO   = A.COD_PROCESSO    AND             \n";
        $sSQL .= "    P.ANO_EXERCICIO  = A.ANO_EXERCICIO   AND             \n";
        $sSQL .= " --JOINS PROCESSO                                        \n";
        $sSQL .= "       P.COD_CLASSIFICACAO   = ASS.COD_CLASSIFICACAO AND \n";
        $sSQL .= "       P.COD_ASSUNTO         = ASS.COD_ASSUNTO       AND \n";
        $sSQL .= "       ASS.COD_CLASSIFICACAO = CLA.COD_CLASSIFICACAO AND \n";
        $sSQL .= "      -- P.NUMCGM              = INT.NUMCGM            AND \n";
        $sSQL .= "       P.COD_SITUACAO        = SIT.COD_SITUACAO      AND \n";
        $sSQL .= " --FIM JOINS PROCESSO                                    \n";
        $sSQL .= "    A.COD_ANDAMENTO  = D.COD_ANDAMENTO   AND             \n";
        $sSQL .= "    A.COD_PROCESSO   = D.COD_PROCESSO    AND             \n";
        $sSQL .= "    A.ANO_EXERCICIO  = D.ANO_EXERCICIO   AND             \n";
        $sSQL .= " --JOINS ANDAMENTO                                       \n";
        $sSQL .= " --FIM JOINS ANDAMENTO                                   \n";
        $sSQL .= " --JOINS DESPACHO                                        \n";
        $sSQL .= "       D.COD_USUARIO  = US_DESP.NUMCGM  AND              \n";
        $sSQL .= "       US_DESP.NUMCGM = CGM_DESP.NUMCGM AND              \n";
        $sSQL .= " --FIM JOINS DESPACHO                                    \n";
        $sSQL .= "    D.COD_PROCESSO   = ".$codProcesso." AND              \n";
        $sSQL .= "    D.ANO_EXERCICIO  = '".$anoE."'   AND                   \n";
        $sSQL .= "    D.COD_ANDAMENTO  = ".$codAndamento." AND             \n";
        $sSQL .= "    D.COD_USUARIO    = ".Sessao::read('numCgm')."               \n";

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();

        $chave            = trim($dbEmp->pegaCampo("chave"));
        #$anoE             = trim($dbEmp->pegaCampo("ano_exercicio"));
        $nomAssunto       = trim($dbEmp->pegaCampo("nom_assunto"));
        $nomClassificacao = trim($dbEmp->pegaCampo("nom_classificacao"));
        $descricao        = trim($dbEmp->pegaCampo("descricao"));
        #$numcgm           = trim($dbEmp->pegaCampo("numcgm"));
        #$nomcgm_int       = trim($dbEmp->pegaCampo("nom_interessado"));
        $nomUsuario       = trim($dbEmp->pegaCampo("nom_usuario"));
        $nomSituacao      = trim($dbEmp->pegaCampo("nom_situacao"));
        $codOrgao         = trim($dbEmp->pegaCampo("cod_orgao"));
        $data             = trim($dbEmp->pegaCampo("DATA_DESP"));

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        $datafim = $data;
        $sSQL .= 	";SELECT \n";
        $sSQL .=	"	C.valor||',' AS nom_municipio, \n";
        $sSQL .=	"	current_date AS hoje \n";
        $sSQL .=	"FROM \n";
        $sSQL .=	"	administracao.configuracao C \n";
        $sSQL .=	"WHERE \n";
        $sSQL .=	"C.parametro = 'nom_municipio' \n";
        $sSQL .=	"AND C.exercicio = '".Sessao::getExercicio()."' \n";

        print '
        <table width="100">
             <tr>
                 <td class="labelcenter" title="Salvar Relatório">
                 <a href="javascript:SalvarForm();"><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a>
             </tr>
         </table>
         ';
?>

        <table width=100%>

            <tr>
                <td class="alt_dados" colspan=2>Relatório de Despacho para Processo n. <?=$codProcesso;?></td>
            </tr>

            <tr>
                <td class="alt_dados" colspan=2><b>Dados do Processo</b></td>
            </tr>

            <tr>
                <td class=label width=30%>Número do Processo</td>
                <td class=field><?=$chave;?></td>
            </tr>

            <tr>
                <td class=label width=30%>Classificação</td>
                <td class=field><?=$nomClassificacao;?></td>
            </tr>

            <tr>
                <td class=label width=30%>Assunto</td>
                <td class=field><?=$nomAssunto;?></td>
            </tr>

<?php
            // Busca os interessados da base de dados ( ação de alterar processo )

            list($iCodProcesso, $sAnoExercicio) = explode("/", $chave);

            $sqlQueryInteressado =
                   "SELECT  sw_cgm.nom_cgm, sw_processo_interessado.numcgm
                      FROM  sw_processo_interessado
                INNER JOIN  sw_cgm
                        ON  sw_cgm.numcgm = sw_processo_interessado.numcgm
                     WHERE  sw_processo_interessado.cod_processo  = ".$iCodProcesso."
                       AND  sw_processo_interessado.ano_exercicio = '".$sAnoExercicio."'";

            $sqlInteressado = new databaseLegado;
            $sqlInteressado->abreBd();
            $sqlInteressado->abreSelecao($sqlQueryInteressado);

            $i = 1;
            while (!$sqlInteressado->eof()) {
                $numCgm = $sqlInteressado->pegaCampo("numcgm");
                $nomCgm = $sqlInteressado->pegaCampo("nom_cgm");

?>
    <tr>
        <td class=label width="35%" style="text-align:right;">
            Interessado <?=$i;?>
        </td>
        <td class=field width="65%">
            <?=$numCgm?> - <?=$nomCgm?>
        </td>
    </tr>
<?php
            $i++;
            $sqlInteressado->vaiProximo();
        }

        $sqlInteressado->limpaSelecao();
        $sqlInteressado->fechaBd();

 ?>
            <tr>
                <td class="alt_dados" colspan=2><b>Trâmite atual do Processo</b></td>
            </tr>

            <tr>
                <td class=label width=30%>Situação atual</td>
                <td class=field><?=$nomSituacao;?></td>
            </tr>
            <?php

                # Novo componente do atual Organograma.

                $obFormulario = new Formulario;
                $obFormulario->addForm(null);
                $obFormulario->setLarguraRotulo(35);

                $obIMontaOrganograma = new IMontaOrganograma(true);
                $obIMontaOrganograma->setCodOrgao($codOrgao);
                $obIMontaOrganograma->setComponenteSomenteLeitura(true);
                $obIMontaOrganograma->geraFormulario($obFormulario);

                $obFormulario->montaHTML();
                echo $obFormulario->getHTML();

            ?>

        <table width='100%'>
            <tr>
                <td class=label width=35%>Ano de Exercício</td>
                <td class=field><?=$anoE;?></td>
            </tr>

            <tr>
                <td class="alt_dados" colspan=2><b>Despacho</b></td>
            </tr>

            <tr>
                <td class=label width=30%>Despachado por</td>
                <td class=field><?=$nomUsuario;?></td>
            </tr>

            <tr>
                <td class=label width=30%>Data de Despacho</td>
                <td class=field><?=$datafim;?></td>
            </tr>

            <tr>
                <td class=label width=30%>Descrição</td>
                <td class=field><?=$descricao;?></td>
            </tr>

            <tr>
                <td class=field colspan="2">
                    <table width="100%" cellspacing=0 border=0 cellpadding=0>
                        <tr>
                            <td>
                                <input type="button" name="voltar" value="Voltar" style="width: 60px"
                                onClick="JavaScript:mudaTelaPrincipal('despachaProcesso.php?<?=Sessao::getId()?>&pagina=&ctrl=1');"
                                >
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
<?php
    break;

    case 100:
        include '../../../framework/legado/filtrosCASELegado.inc.php';
    break;
}
include '../../../framework/include/rodape.inc.php';
?>
