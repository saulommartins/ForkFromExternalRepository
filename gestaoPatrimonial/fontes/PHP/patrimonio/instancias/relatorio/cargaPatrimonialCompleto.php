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
    * Relatório Carga Patrimonial Completo/Cadastral
    * Data de Criação   : 11/04/2003

    * @author Desenvolvedor  Ricardo Lopes de Alencar

    * @ignore

    $Id:$

    * Casos de uso: uc-03.01.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'; //Insere o início da página html
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/botoesPdfLegado.class.php';
include_once '../relatorio.class.php'; //Classe com os métodos para gereção de relatórios

    $orgao = $_REQUEST['inCodOrgao'];
    $local = $_REQUEST['inCodLocal'];
    $exercicio = 'xxx';

//Pega os dados gravados na sessão em forma de vetor e gera as variáveis locais
//if (is_array($sessao->transf)) {
//    foreach ($sessao->transf as $chave=>$valor) {
//        $$chave = $valor;
//    }
//}

if (!isset($tipoRelatorio)) {
    $tipoRelatorio = 0;
}

//Gera o filtro de acordo com os parâmetros fornecidos pelo cliente
if (!isset($filtro)) {
    $relatorio = new relatorio;

    if ($_POST['codNatureza']       AND $_POST['codNatureza'] != 'xxx') {
        $natureza = $_POST['codNatureza'];
    }
    if ($_POST['codGrupo']          AND $_POST['codGrupo'] != 'xxx') {
        $grupo = $_POST['codGrupo'];
    }
    if ($_POST['codEspecie']        AND $_POST['codEspecie'] != 'xxx') {
        $especie = $_POST['codEspecie'];
    }

    $orgao = $_REQUEST['inCodOrgao'];

    $local = $_REQUEST['inCodLocal'];

    $filtro = $relatorio->montaFiltro( $natureza, $grupo, $especie, $orgao, $local, "" );
}

$sFrom = "From patrimonio.natureza as n, patrimonio.grupo as g, patrimonio.especie as e,
         patrimonio.bem as bae, patrimonio.vw_bem_ativo as b,
         patrimonio.vw_ultimo_historico as uh, patrimonio.historico_bem as h,  patrimonio.situacao_bem as s ";

$sFrom2 = "From patrimonio.bem as sbe, patrimonio.vw_bem_ativo as sb,
         patrimonio.vw_ultimo_historico as suh, patrimonio.historico_bem as sh ";

$sWhere =  "Where n.cod_natureza = e.cod_natureza
            And g.cod_natureza = n.cod_natureza
            And g.cod_grupo = e.cod_grupo
            And bae.cod_natureza = e.cod_natureza
            And bae.cod_grupo = e.cod_grupo
            And bae.cod_especie = e.cod_especie

            And bae.cod_bem = b.cod_bem

            And uh.cod_bem = h.cod_bem
            And uh.timestamp = h.timestamp
            And h.cod_bem = b.cod_bem
            And h.cod_situacao = s.cod_situacao
            ".$filtro." ";

//Monta a query padrão e adiciona o filtro
$sql = "Select n.nom_natureza, g.nom_grupo, e.nom_especie,
        b.cod_bem, to_real(b.vl_bem) as vl_bem, b.descricao, b.detalhamento, b.dt_aquisicao, s.nom_situacao
        ".$sFrom."
        ".$sWhere."
        Group by n.nom_natureza, g.nom_grupo, e.nom_especie,
            b.cod_bem, to_real(b.vl_bem) as vl_bem, b.descricao, b.detalhamento,
            b.dt_aquisicao, s.nom_situacao ";

$ordenar = "n.nom_natureza, g.nom_grupo, e.nom_especie";

    //Mostra a opção de imprimir ou salvar o relatório
    //** Inicia query da carga patrimonial **//
    //Seleciona os dados de classificação: natureza, grupo, espécie
    $sqlPDF =  "Select n.nom_natureza, n.cod_natureza, g.nom_grupo, g.cod_grupo, e.nom_especie, e.cod_especie
                ".$sFrom."
                ".$sWhere."
                Group by n.nom_natureza, n.cod_natureza, g.nom_grupo, g.cod_grupo, e.nom_especie, e.cod_especie
                Order by ".$ordenar." ASC ; ";
    //Seleciona os dados do bem de acordo com a classificação
    $sqlPDF .= "Select b.cod_bem, to_real(b.vl_bem) as vl_bem, replace(b.descricao, chr(13), ' ') as descricao, b.detalhamento, b.dt_aquisicao,
    s.nom_situacao,bae.num_placa
                ".$sFrom."
                ".$sWhere."
                And e.cod_natureza = &cod_natureza
                And e.cod_grupo = &cod_grupo
                And e.cod_especie = &cod_especie
                Order by ".$ordenar." ASC ;";
    //Seleciona os atributos de um bem
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
Sql referente a atributo patrimonial que não existe mais
";*/
    //Seleciona os dados financeiros do bem
    $sqlPDF .= "Select BC.cod_empenho || '/' || BC.exercicio as num_empenho
                From patrimonio.bem_comprado as BC
                Where BC.cod_bem = &cod_bem; ";
    //** Início do totalizador **//
    //Seleciona Natureza, valor total por natureza  quantidade total por natureza
    $sqlPDF .= "Select n.nom_natureza, n.cod_natureza,
                (Select to_real(SUM(sb.vl_bem)) as total
                    ".$sFrom2."

                    Where sb.cod_bem = sbe.cod_bem

                    And sbe.cod_natureza = e.cod_natureza

                    And suh.cod_bem = sh.cod_bem
                    And suh.timestamp = sh.timestamp
                    And sh.cod_bem = sb.cod_bem";

    if ($orgao != "") {
        $sqlPDF .="    AND sh.cod_orgao = '".$orgao."'";
    }

    if ($local != "") {
        $sqlPDF .="    AND sh.cod_local = '".$local."'";
    }

    $sqlPDF .="
                ) as total_natureza,
                (Select Count(*) as qtdn
                    ".$sFrom2."

                    Where sb.cod_bem = sbe.cod_bem

                    And sbe.cod_natureza = e.cod_natureza

                    And suh.cod_bem = sh.cod_bem
                    And suh.timestamp = sh.timestamp
                    And sh.cod_bem = sb.cod_bem";

    if ($orgao != "") {
        $sqlPDF .="    AND sh.cod_orgao = '".$orgao."'";
    }

    if ($local != "") {
        $sqlPDF .="    AND sh.cod_local = '".$local."'";
    }

    $sqlPDF .=" ) as qtd_natureza
                    ".$sFrom."
                    ".$sWhere."

                    Group by n.nom_natureza, n.cod_natureza, total_natureza, qtd_natureza
                    Order by nom_natureza; ";

    //Seleciona os grupos, valor total por grupo e qtd total por grupo
    $sqlPDF .= "Select g.nom_grupo, g.cod_grupo, g.cod_natureza,
                (Select to_real(SUM(sb.vl_bem)) as total
                    ".$sFrom2."
                    Where sb.cod_bem = sbe.cod_bem

                    And sbe.cod_natureza = e.cod_natureza
                    And sbe.cod_grupo = e.cod_grupo

                    And suh.cod_bem = sh.cod_bem
                    And suh.timestamp = sh.timestamp
                    And sh.cod_bem = sb.cod_bem";

    if ($orgao != "") {
        $sqlPDF .="    AND sh.cod_orgao = '".$orgao."'";
    }

    if ($local != "") {
        $sqlPDF .="    AND sh.cod_local = '".$local."'";
    }

    $sqlPDF .=" ) as total_grupo,
                (Select Count(*) as qtdg
                    ".$sFrom2."

                    Where sb.cod_bem = sbe.cod_bem

                    And sbe.cod_natureza = e.cod_natureza
                    And sbe.cod_grupo = e.cod_grupo

                    And suh.cod_bem = sh.cod_bem
                    And suh.timestamp = sh.timestamp
                    And sh.cod_bem = sb.cod_bem";

    if ($orgao != "") {
        $sqlPDF .="    AND sh.cod_orgao = '".$orgao."'";
    }

    if ($local != "") {
        $sqlPDF .="    AND sh.cod_local = '".$local."'";
    }


    $sqlPDF .=" ) as qtd_grupo
                    ".$sFrom."
                    ".$sWhere."

--                  And n.cod_natureza = &cod_natureza

                    Group by g.nom_grupo, g.cod_grupo, g.cod_natureza, total_grupo, qtd_grupo
                    Order by nom_grupo;  ";

    //Seleciona as especies, valor total por especie e qtd total por especie
    $sqlPDF .= "Select e.nom_especie, e.cod_especie,
                (Select to_real(SUM(sb.vl_bem)) as total
                    ".$sFrom2."

                    Where sb.cod_bem = sbe.cod_bem

                    And sbe.cod_natureza = e.cod_natureza
                    And sbe.cod_grupo = e.cod_grupo
                    And sbe.cod_especie = e.cod_especie

                    And suh.cod_bem = sh.cod_bem
                    And suh.timestamp = sh.timestamp
                    And sh.cod_bem = sb.cod_bem";

    if ($orgao != "") {
        $sqlPDF .="    AND sh.cod_orgao = '".$orgao."'";
    }

    if ($local != "") {
        $sqlPDF .="    AND sh.cod_local = '".$local."'";
    }

    $sqlPDF .="    ) as total_especie,
                (Select Count(*) as qtde
                    ".$sFrom2."

                    Where sb.cod_bem = sbe.cod_bem

                    And sbe.cod_natureza = e.cod_natureza
                    And sbe.cod_grupo = e.cod_grupo
                    And sbe.cod_especie = e.cod_especie

                    And suh.cod_bem = sh.cod_bem
                    And suh.timestamp = sh.timestamp
                    And sh.cod_bem = sb.cod_bem";

    if ($orgao != "") {
        $sqlPDF .="    AND sh.cod_orgao = '".$orgao."'";
    }

    if ($local != "") {
        $sqlPDF .="    AND sh.cod_local = '".$local."'";
    }


    $sqlPDF .="    ) as qtd_especie
                    ".$sFrom."
                    ".$sWhere."

--                    And n.cod_natureza = &cod_natureza
--                    And g.cod_grupo = &cod_grupo

                    Group by e.nom_especie, e.cod_especie, total_especie, qtd_especie
                    Order by nom_especie;  ";

    //Seleciona o valor total e a quantidade total de bens ativos
    $sqlPDF .= "Select
                (Select to_real(SUM(sb.vl_bem)) as total
                    ".$sFrom2."

                    Where sb.cod_bem = sbe.cod_bem

                    And suh.cod_bem = sh.cod_bem
                    And suh.timestamp = sh.timestamp
                    And sh.cod_bem = sb.cod_bem";

    if ($orgao != "") {
        $sqlPDF .="    AND sh.cod_orgao = '".$orgao."'";
    }

    if ($local != "") {
        $sqlPDF .="    AND sh.cod_local = '".$local."'";
    }

    $sqlPDF .="    ) as valor_total,
                (Select Count(*) as qtd
                    ".$sFrom2."

                    Where sb.cod_bem = sbe.cod_bem

                    And suh.cod_bem = sh.cod_bem
                    And suh.timestamp = sh.timestamp
                    And sh.cod_bem = sb.cod_bem";

    if ($orgao != "") {
        $sqlPDF .="    AND sh.cod_orgao = '".$orgao."'";
    }

    if ($local != "") {
        $sqlPDF .="    AND sh.cod_local = '".$local."'";
    }


    $sqlPDF .=" ) as qtd_total
                    ".$sFrom."
                    ".$sWhere."

                    Group by valor_total, qtd_total; ";
    if ($tipoRelatorio==0) { //Completo
        $sXML       = '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/relatorios/cargaPatrimonialCompleto.xml';
        $sSubTitulo = "Completo";
    } elseif ($tipoRelatorio==1) { //Cadastral
        $sXML       = '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/relatorios/cargaPatrimonialCadastral.xml';
        $sSubTitulo = "Cadastral";
    }

    $botoesPDF  = new botoesPdfLegado;
    $botoesPDF->imprimeBotoes($sXML,$sqlPDF,'',$sSubTitulo);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php'; //Insere o fim da página html
?>
