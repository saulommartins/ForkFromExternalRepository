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
    * Relatório Patrimonial Completo
    * Data de Criação   : 02/04/2003

    * @author Desenvolvedor  Ricardo Lopes de Alencar

    * @ignore

    $Revision: 28506 $
    $Name$
    $Autor: $
    $Date: 2008-03-12 08:59:02 -0300 (Qua, 12 Mar 2008) $

    * Casos de uso: uc-03.01.09
*/

/*
$Log$
Revision 1.15  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.14  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/botoesPdfLegado.class.php';
include_once '../relatorio.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';

//Pega os dados gravados na sessão em forma de vetor e gera as variáveis locais
$arFiltro = Sessao::read('filtro');
if (is_array($arFiltro)) {
    foreach ($arFiltro as $chave=>$valor) {
        $$chave = $valor;
    }
}

//Gera o filtro de acordo com os parâmetros fornecidos pelo cliente
if ( !isset($filtro) ) {

    $arLocal = preg_split( "/[^a-zA-Z0-9]/", $_POST['codMasSetor'] );

    if ($_POST['inCodBemInicial']) {
        $codBemInicial = $_POST['inCodBemInicial'];
    }
    if ($_POST['inCodBemFinal']) {
        $codBemFinal = $_POST['inCodBemFinal'];
    }

    if ($_POST['codNatureza']       AND $_POST['codNatureza'] != 'xxx') {
        $natureza = $_POST['codNatureza'];
    }
    if ($_POST['codGrupo']          AND $_POST['codGrupo'] != 'xxx') {
        $grupo = $_POST['codGrupo'];
    }
    if ($_POST['codEspecie']        AND $_POST['codEspecie'] != 'xxx') {
        $especie = $_POST['codEspecie'];
    }
    if ($arLocal[0] > 0) {
        $orgao = $arLocal[0];
    }
    if ($arLocal[1] > 0) {
        $unidade = $arLocal[1];
    }
    if ($arLocal[2] > 0) {
        $departamento = $arLocal[2];
    }
    if ($arLocal[3] > 0) {
        $setor = $arLocal[3];
    }
    if ($arLocal[4] > 0) {
        $local = $arLocal[4];
    }
    if ($arLocal[5] > 0) {
        $exercicio = $arLocal[5];
    }

    $relatorio = new relatorio;
    $filtro    = $relatorio->montaFiltro( $natureza, $grupo, $especie, $orgao, $unidade, $departamento, $setor, $local, $exercicioLocal, $codBemInicial, $codBemFinal,$dataInicial,$dataFinal);
}

//Monta a query padrão e adiciona o filtro
$sql = "
SELECT
    B.*,
    case when B.identificacao='t' Then 'Sim'
         else 'Não'
    end as identificacao,
    E.nom_especie,
    G.nom_grupo,
    N.nom_natureza,
    L.nom_local,
    C.nom_cgm,
    S.nom_situacao
FROM
    patrimonio.bem as B
    LEFT OUTER JOIN patrimonio.bem_baixado as BB ON
        BB.cod_bem = B.cod_bem
    INNER JOIN patrimonio.especie as E ON
        E.cod_natureza = B.cod_natureza AND
        E.cod_grupo = B.cod_grupo AND
        E.cod_especie = B.cod_especie
    INNER JOIN patrimonio.grupo as G ON
        G.cod_natureza = B.cod_natureza AND
        G.cod_grupo = B.cod_grupo
    INNER JOIN patrimonio.natureza as N ON
        N.cod_natureza = B.cod_natureza
    INNER JOIN sw_cgm as C ON
        C.numcgm = B.numcgm
    INNER JOIN patrimonio.vw_ultimo_historico as U ON
        U.cod_bem = B.cod_bem
    INNER JOIN patrimonio.historico_bem as H ON
        H.cod_bem   = U.cod_bem AND
        H.timestamp = U.timestamp
    INNER JOIN administracao.local as L ON
        L.cod_local = H.cod_local AND
        L.cod_setor = H.cod_setor AND
        L.cod_departamento = H.cod_departamento AND
        L.cod_unidade = H.cod_unidade AND
        L.cod_orgao = H.cod_orgao AND
        L.ano_exercicio = H.ano_exercicio
    INNER JOIN patrimonio.situacao_bem as S ON
        S.cod_situacao = H.cod_situacao
WHERE
    BB.COD_BEM IS NULL ".$filtro." order by h.cod_bem;";
//Mostra a opção de imprimir ou salvar o relatório
$sqlPDF  = $sql;
$sqlPDF .= "
   SELECT atributo_dinamico.nom_atributo
         , atributo_dinamico.cod_atributo
         , bem_atributo_especie.valor AS valor_atributo
      FROM administracao.atributo_dinamico
INNER JOIN patrimonio.especie_atributo
        ON especie_atributo.cod_atributo = atributo_dinamico.cod_atributo
       AND especie_atributo.cod_cadastro = atributo_dinamico.cod_cadastro
       AND especie_atributo.cod_modulo = atributo_dinamico.cod_modulo
       AND especie_atributo.ativo = true
INNER JOIN patrimonio.bem_atributo_especie
        ON bem_atributo_especie.cod_modulo = especie_atributo.cod_modulo
       AND bem_atributo_especie.cod_cadastro = especie_atributo.cod_cadastro
       AND bem_atributo_especie.cod_atributo = especie_atributo.cod_atributo
       AND bem_atributo_especie.cod_especie = especie_atributo.cod_especie
       AND bem_atributo_especie.cod_natureza = especie_atributo.cod_natureza
       AND bem_atributo_especie.cod_grupo = especie_atributo.cod_grupo
     WHERE bem_atributo_especie.cod_bem = &cod_bem
       AND especie_atributo.ativo = true;
";
/*
            SQl select do atributo
";*/

$sXML     = '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/relatorios/relatorioPatrimonialCompleto.xml';

$botoesPDF  = new botoesPdfLegado;
$botoesPDF->imprimeBotoes( $sXML , $sqlPDF, '' , '' );
/*
$x = 0;

while (!$conn->eof()) {
    ++$x;
    if ($x % 2) {
        $estilo = "show_dados";
    } else {
        $estilo = "field";
    }
    $codBem           = $conn->pegaCampo("cod_bem");
    $descricao        = $conn->pegaCampo("descricao");
    $detalhamento     = $conn->pegaCampo("detalhamento");
    $dtAquisicao      = $conn->pegaCampo("dt_aquisicao");
    $dtDepreciacao    = $conn->pegaCampo("dt_depreciacao");
    $dtGarantia       = $conn->pegaCampo("dt_garantia");
    $valorBem         = $conn->pegaCampo("vl_bem");
    $valorDepreciacao = $conn->pegaCampo("vl_depreciacao");
    $identificacao    = $conn->pegaCampo("identificacao");
    if ($identificacao=='S') {
        $identificacao = 'Sim';
    } elseif ($identificacao=='N') {
        $identificacao = 'Não';
    }
    $nomSituacao = $conn->pegaCampo("nom_situacao");
    $fornecedor  = $conn->pegaCampo("nom_cgm");
    $nomEspecie  = $conn->pegaCampo("nom_especie");
    $nomLocal    = $conn->pegaCampo("nom_local");
    //Verifica dados financeiros
    $ordemCompra = 0;
    $notaFiscal  = 0;
    $empenho     = 0;
    $financ      = false;
    $query = "SELECT
                BC.cod_empenho, BC.exercicio
              FROM
                ".BEM_COMPRADO." as BC
              WHERE
                BC.cod_bem = '".$codBem."' ";
    $conn2 = new dataBaseLegado;
    $conn2->abreBD();
    $conn2->abreSelecao($query);
    $conn2->fechaBD();
    $conn2->vaiPrimeiro();
    if ( !$conn2->eof() ) {
        $financ           = true;
        $codEmpenho       = $conn2->pegaCampo("cod_empenho");
        $exercicioEmpenho = $conn2->pegaCampo("exercicio");
        $empenho          = $codEmpenho."/".$exercicioEmpenho;
    }
    $conn2->limpaSelecao();


    $conn->vaiProximo();
?>

<table width='100%'>
<tr>
    <td class='<?=$estilo;?>' width="20%";><b>Código do Bem:</b> <?=$codBem;?></td>
    <td class='<?=$estilo;?>'><b>Descrição:</b> <?=$descricao;?></td>
</tr>
</table>
<table width='100%'>
<tr>
    <td class='<?=$estilo;?>'><b>Detalhamento:</b> <?=ereg_replace("\n","<br>",$detalhamento);?></td>
</tr>
</table>
<table width='100%'>
<tr>
    <td class='<?=$estilo;?>' width="75%"><b>Fornecedor:</b> <?=$fornecedor;?></td>
    <td class='<?=$estilo;?>'><b>Placa de Identificação:</b> <?=$identificacao;?></td>
</tr>
</table>
<table width='100%'>
<tr>
    <td class='<?=$estilo;?>' width="75%"><b>Localização atual:</b> <?=$nomLocal;?></td>
    <td class='<?=$estilo;?>'><b>Situação atual:</b> <?=$nomSituacao;?></td>
</tr>
</table>

<?php if ($financ) { ?>

    <table width='100%'>
    <tr>
        <td class='<?=$estilo;?>'><b>Empenho:</b> <?=$empenho;?></td>
    </tr>
    </table>

<?php } ?>

<table width='100%'>
<tr>
    <td class='<?=$estilo;?>' width="33%"><b>Data Aquisição:</b> <?=dataToBr($dtAquisicao);?></td>
    <td class='<?=$estilo;?>' width="33%"><b>Data Depreciação:</b> <?=dataToBr($dtDepreciacao);?></td>
    <td class='<?=$estilo;?>' width="33%"><b>Data Garantia:</b> <?=dataToBr($dtGarantia);?></td>
</tr>
</table>
<table width='100%'>
<tr>
    <td class='<?=$estilo;?>'><b>Atributos:</b></td>
</tr>
<?php
Sql Select do atributo
    <tr>
        <td class='<?=$estilo;?>'><b><?=$nomAtributo;?>:</b> <?=$valorAtributo;?></td>
    </tr>
<?php
}
$conn2->limpaSelecao();
?>
</table>

<hr style='width: 100%; border-width: 1px; border: solid; color: black;'>
<?php
}
$conn->limpaSelecao();
?>
<table width="100%" align="center">
<tr>
    <td align="center"><font size=2><?php $paginacao->mostraLinks(); ?></font></td>
</tr>
</table>
<?php
*/

//Setar variáveis que devem permanecer em todas as páginas
$transf[filtro]  = $filtro;
$transf[ordenar] = $ordenar;
Sessao::write('filtro',$transf);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php'; //Insere o fim da página html
