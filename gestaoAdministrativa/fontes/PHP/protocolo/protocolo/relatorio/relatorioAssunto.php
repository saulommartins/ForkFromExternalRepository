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

    $Id: relatorioAssunto.php 66029 2016-07-08 20:55:48Z carlos.silva $

    */

include_once '../../../pacotes/FrameworkHTML.inc.php';
include_once '../../../framework/include/cabecalho.inc.php';
include_once '../../../framework/legado/funcoesLegado.lib.php';
include_once '../assunto.class.php';
include_once '../../../framework/legado/botoesPdfLegado.class.php';
include_once '../../../framework/legado/paginacaoLegada.class.php';
include_once '../../../framework/legado/mascarasLegado.lib.php';

$ctrl   = $_REQUEST["ctrl"];
$pagina = $_REQUEST["pagina"];
$tipo   = $_REQUEST["tipo"];
$ordem  = $_REQUEST["ordem"];

$botoesPDF = new botoesPdfLegado;
$relatorio = new assunto;
$relatorio->setaVariaveisRel($tipo, $ordem);
$mascaraAssunto = pegaConfiguracao('mascara_assunto',5);

$obFormulario = new FormularioAbas;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda('UC-01.06.99');
$obFormulario->show();

if (!(isset($ctrl))) {
    $ctrl = 0;
}

if (isset($pagina)) {
    $ctrl = 1;
    $tipo = Sessao::read('tipo');
}

switch ($ctrl) {
    case 0:

?>
 <script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.tipo.value;
            if (campo == 0) {
            mensagem += "@O campo Opção de Relatório é obrigatório";
            erro = true;
        }

        campo = document.frm.ordem.value;
            if (campo == 0) {
            mensagem += "@O campo Ordem de Relatório é Obrigatório";
            erro = true;
        }

            if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
            return !(erro);
      }
      function Salvar()
      {
         if (Valida()) {
            document.frm.submit();
         }
      }
   </script>
<form action="relatorioAssunto.php?<?=Sessao::getId();?>&order=<?=$_REQUEST["ordem"];?>&ctrl=1" method="POST" name="frm">
<table width=100%>
<tr><td class=alt_dados colspan=2>Geração de Relatório de Assunto</td></tr>
    <tr><td class=label width='20%'>Opção de Relatório:</td><td class=field><select name="tipo">
                                                               <option value=0 selected>Selecione uma opção</option>
                                                               <option value=1>Sintético</option>
                                                               <option value=2>Analítico</option>
                                                               </select></td></tr>
    <tr><td class=label>Ordenado por:</td><td class=field><select name="ordem">
                                                              <option value=0 selected>Selecione uma opção</option>
                                                              <option value="assunto.cod_classificacao,assunto.cod_assunto">Código</option>
                                                              <option value="lower(nom_classificacao),lower(nom_assunto)">Nome</option>
                                                              </select></td></tr>

<tr><td class=field colspan=2><input type="button" name="gerar" value="OK" style="width: 60px" onClick="Salvar();"></td></tr>
</table>

</form>

<?php
    break;
    case 1:
        if ($tipo == 1) {

            ?>
                <script type="text/javascript">
                    function SalvarSintetico()
                    {
                        document.frm.action = "assuntoSintetico.php?<?=Sessao::getId()?>&ctrl=1&tipo=1&ordem=<?=$_REQUEST["ordem"];?>";
                        document.frm.submit();
                    }
                </script>
                <form name=frm action="relatorioAssunto.php?<?=Sessao::getId()?>&ctrl=1" method="post">
            <?php

            $select =   "SELECT
                        assunto.cod_classificacao || '.' || cod_assunto as codigo,
                        nom_assunto,
                        class.nom_classificacao
                        FROM
                        sw_assunto as assunto,
                        sw_classificacao as class
                        WHERE
                        assunto.cod_classificacao = class.cod_classificacao";

            if ($ordem != '') {
                Sessao::write('sSQLs',$select);
                Sessao::write('ordem',$ordem);
                Sessao::write('tipo',$tipo);
            }

            $botoesPdf = new botoesPdfLegado;
            $paginacao = new paginacaoLegada;
            $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
            $paginacao->pegaPagina($pagina);
            $paginacao->geraLinks();
            $paginacao->pegaOrder(Sessao::read('ordem'),"ASC");

            $sSQL = $paginacao->geraSQL();
            $sqlPDF = Sessao::read('sSQLs');
            $sqlPDF .= " order by ".Sessao::read('ordem')." ASC";

            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $dbConfig->abreSelecao($sSQL);

            if (!$dbConfig->eof()) {

                while (!$dbConfig->eof()) {
                    $lista[] = $dbConfig->pegaCampo("codigo")."##-@-##".$dbConfig->pegaCampo("nom_assunto")."##-@-##".$dbConfig->pegaCampo("nom_classificacao");
                    $dbConfig->vaiProximo();
                }

                  print '
                <table width="100">
                    <tr>
                        <td class="labelcenter" title="Salvar Relatório">
                        <a href="javascript:SalvarSintetico();"><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a>
                    </tr>
                </table>
                    ';

                $dbConfig->limpaSelecao();
                $dbConfig->fechaBd();
                echo "<table width=100%>";
                echo "<tr>
                        <td class=alt_dados width='5%'>&nbsp;</td>
                        <td class=alt_dados width='5%'>Código</td>
                        <td class=alt_dados>Nome da Classificação</td>
                        <td class=alt_dados>Nome do Assunto</td>
                      </tr>";
                if ($lista != "") {
                    $count = $paginacao->contador();
                    while (list ($cod, $val) = each ($lista)) { //mostra os tipos de processos na tela
                        $fim = explode("##-@-##", $val);
                        $arCodClassifAssunto = validaMascaraDinamica($mascaraAssunto,$fim[0]);
                        print "<tr>
                                <td class='labelcenter'>".$count++."</td>
                                <td class=show_dados_right>$arCodClassifAssunto[1]</td>
                                <td class=show_dados>$fim[2]</td>
                                <td class=show_dados>$fim[1]</td>
                              </tr>";
                    }
                }
                echo "</table>";
                echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
                    $paginacao->mostraLinks();
                echo "</font></tr></td></table>";

            } else {
                echo "<b>Nenhum Registro Encontrado!</b>";
            }
        }
        if ($tipo == 2) {
            ?>
                <script type="text/javascript">
                    function SalvarAnalitico()
                    {
                        document.frm.action = "assuntoAnalitico.php?<?=Sessao::getId()?>&ctrl=1&tipo=2&ordem=<?=$ordem?>";
                        document.frm.submit();
                    }
                </script>
                <form name=frm action="relatorioAssunto.php?<?=Sessao::getId()?>&ctrl=1" method="post">
            <?php

            $select =   "SELECT
                        assunto.cod_classificacao || '.' || cod_assunto as codigo,
                        c.nom_classificacao,
                        assunto.nom_assunto,
                        assunto.cod_classificacao,
                        cod_assunto
                        FROM
                        sw_assunto as assunto,
                        sw_classificacao as c
                        WHERE
                        assunto.cod_classificacao = c.cod_classificacao";

            if ($ordem != '') {
                Sessao::write('sSQLs',$select);
                Sessao::write('ordem',$ordem);
                Sessao::write('tipo',$tipo);
            }
            $botoesPdf = new botoesPdfLegado;
            $paginacao = new paginacaoLegada;
            $paginacao->pegaDados(Sessao::read('sSQLs'),"1", "1");
            $paginacao->pegaPagina($pagina);
            $paginacao->geraLinks();
            $paginacao->pegaOrder(Sessao::read('ordem'),"ASC");
            $sSQL = $paginacao->geraSQL();
            $sqlPDF = Sessao::read('sSQLs');
            $sqlPDF .= " order by ".Sessao::read('ordem')." ASC;";
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $dbConfig->abreSelecao($sSQL);

            if (!$dbConfig->eof()) {

                while (!$dbConfig->eof()) {
                    $codclass = $dbConfig->pegaCampo("cod_classificacao");
                    $codass = $dbConfig->pegaCampo("cod_assunto");
                    $codigo = $dbConfig->pegaCampo("codigo");
                    $lista[$codigo]["ass"] = $dbConfig->pegaCampo("codigo")."/".$dbConfig->pegaCampo("nom_classificacao")."/".
                    $dbConfig->pegaCampo("nom_assunto");
                    $sqlDoc =   "SELECT
                                nom_documento
                                FROM
                                sw_documento as doc,
                                sw_documento_assunto as docass
                                WHERE
                                docass.cod_documento = doc.cod_documento";
                    $sqlPDF .= $sqlDoc;
                    $sqlPDF .=  " and docass.cod_assunto = &cod_assunto
                                and docass.cod_classificacao = &cod_classificacao;";
                    $sqlDoc .=  " and docass.cod_assunto = $codass
                                and docass.cod_classificacao = $codclass";

                    $dbDoc = new dataBaseLegado;
                    $dbDoc->abreBd();
                    $dbDoc->abreSelecao($sqlDoc);
                    while (!$dbDoc->eof()) {
                        $lista[$codigo]["doc"][] = $dbDoc->pegaCampo("nom_documento");
                        $dbDoc->vaiProximo();
                    }
                    $dbDoc->limpaSelecao();
                    $sqlAnda =  "
                        SELECT
                                anda.cod_assunto,
                                anda.descricao,
                                anda.ordem,
                                (
                                    SELECT  descricao
                                      FROM  organograma.orgao_descricao
                                     WHERE  orgao_descricao.cod_orgao = orgao.cod_orgao
                                  ORDER BY  timestamp DESC
                                     LIMIT  1
                                ) as nom_setor
                          FROM  sw_andamento_padrao as anda,
                                organograma.orgao

                         WHERE  anda.cod_orgao = orgao.cod_orgao";
                    $sqlPDF .= $sqlAnda;
                    $sqlPDF .=  " and cod_assunto = &cod_assunto
                                and cod_classificacao = &cod_classificacao";
                    $sqlAnda .=  " and cod_assunto = $codass
                                and cod_classificacao = $codclass";
                    $sqlAnda .= " Order By ordem";
                    $sqlPDF .= " Order By ordem";

                    $dbDoc->abreselecao($sqlAnda);
                    while (!$dbDoc->eof()) {
                        $lista[$codigo]["anda"][] = "<b>Setor: </b>".$dbDoc->pegaCampo("nom_setor")."<br><b>Ordem: </b>".
                        $dbDoc->pegaCampo("ordem")."<br> <b>Descrição:</b> ".$dbDoc->pegaCampo("descricao");
                        $dbDoc->vaiProximo();
                    }
                    $dbConfig->vaiProximo();
                }

                 print '
                <table id= paginacao width="100">
                    <tr>
                        <td class="labelcenter" title="Salvar Relatório">
                        <a href="javascript:SalvarAnalitico();"><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a>
                    </tr>
                </table>
                    ';

                $dbDoc->limpaSelecao();
                $dbConfig->limpaSelecao();
                $dbDoc->fechaBd();
                $dbConfig->fechaBd();
                echo "<table width=70%>";
                while (list ($cod, $val) = each ($lista)) {
                    $result = explode("/", $val["ass"]);
                    if (is_array($val["doc"]))
                        $docs = implode("<br>", $val["doc"]);
                    else
                        $docs = "&nbsp;";
                    if (is_array($val["anda"]))
                        $anda = implode("<br><br>", $val["anda"]);
                    else
                        $anda = "&nbsp;";
                    echo "<tr><td class=alt_dados colspan=2>Assunto</td></tr>";
                    echo "<tr><td class=label>Código</td><td class=show_dados>$result[0]</td></tr>";
                    echo "<tr><td class=label>Classificação</td><td class=show_dados>$result[1]</td></tr>";
                    echo "<tr><td class=label>Assunto</td><td class=show_dados>$result[2]</td></tr>";
                    echo "<tr><td class=alt_dados colspan=2>Documento</td></tr>";
                    echo "<tr><td class=label>Documentos</td><td class=show_dados>$docs</td></tr>";
                    echo "<tr><td class=alt_dados colspan=2>Andamento Padrão</td></tr>";
                    echo "<tr><td class=show_dados colspan=2>$anda</td></tr>";
                    echo "</table><table width = 70%><br>";
                }
                echo "</table>";
                echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
                    $paginacao->mostraLinks();
                echo "</font></tr></td></table>";

            } else {
                echo "<b>Nenhum Registro Encontrado!</b>";
            }
        }

    break;

    }

include '../../../framework/include/rodape.inc.php';
?>
