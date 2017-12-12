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
    * Relatório de Carga Patrimonial - Totalizador
    * Data de Criação   : 14/04/2003

    * @author Desenvolvedor  Ricardo Lopes de Alencar

    * @ignore

    $Revision: 12234 $
    $Name$
    $Autor: $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.01.09
*/

/*
$Log$
Revision 1.10  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.9  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'; //Insere o início da página html
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/botoesPdfLegado.class.php';
include_once '../relatorio.class.php'; //Classe com os métodos para gereção de relatórios

if ($_POST['codNatureza']       AND $_POST['codNatureza'] != 'xxx') {
    $natureza = $_POST['codNatureza'];
}
if ($_POST['codGrupo']          AND $_POST['codGrupo'] != 'xxx') {
    $grupo = $_POST['codGrupo'];
}
if ($_POST['codEspecie']        AND $_POST['codEspecie'] != 'xxx') {
    $especie = $_POST['codEspecie'];
}

if ($_REQUEST['inCodLocal'] != '') {
    $local = $_REQUEST['inCodLocal'];
}

if ($_REQUEST['inCodOrgao'] != '') {
    $orgao = $_REQUEST['inCodOrgao'];
}

$relatorio = new relatorio;
$filtro   = $relatorio->montaFiltro( $natureza, $grupo, $especie, $orgao, $local, $exercicioLocal,"","","", "","", "h");

if ($filtro) {
    $filtroInner = "INNER JOIN patrimonio.vw_ultimo_historico as u ON
                        u.cod_bem = b.cod_bem
                    INNER JOIN patrimonio.historico_bem as h ON
                        h.cod_bem   = u.cod_bem AND
                        h.timestamp = u.timestamp
                    INNER JOIN organograma.local as l ON
                        l.cod_local = h.cod_local";
}

//** Início do totalizador **//
//Seleciona Natureza, valor total por natureza  quantidade total por natureza
$sqlPDF = " SELECT
                n.nom_natureza, n.cod_natureza,
                sum(b.vl_bem) as total_natureza,
                count(b.cod_bem) as qtd_natureza
            FROM
                patrimonio.natureza n
                LEFT OUTER JOIN patrimonio.bem AS b ON
                    n.cod_natureza = b.cod_natureza
                LEFT OUTER JOIN patrimonio.bem_baixado AS bb ON
                    bb.cod_bem = b.cod_bem
                ".$filtroInner."
            WHERE
                bb.cod_bem IS NULL ".$filtro."
            GROUP BY
                n.nom_natureza,n.cod_natureza
            ORDER BY
                n.nom_natureza;";

//Seleciona os grupos, valor total por grupo e qtd total por grupo
$sqlPDF .= " SELECT
                g.nom_grupo, g.cod_grupo, n.cod_natureza,
                sum(b.vl_bem) as total_grupo,
                count(b.cod_bem) as qtd_grupo
            FROM
                patrimonio.natureza n
                INNER JOIN patrimonio.grupo AS g ON
                    n.cod_natureza = g.cod_natureza
                LEFT OUTER JOIN patrimonio.bem AS b ON
                    g.cod_natureza = b.cod_natureza AND
                    g.cod_grupo    = b.cod_grupo
                LEFT OUTER JOIN patrimonio.bem_baixado AS bb ON
                    bb.cod_bem = b.cod_bem
                 ".$filtroInner."
            WHERE
                    bb.cod_bem IS NULL ".$filtro."
                AND n.cod_natureza = &cod_natureza
            GROUP BY
                g.nom_grupo, g.cod_grupo, n.cod_natureza
            ORDER BY
                g.nom_grupo;";

//Seleciona as especies, valor total por especie e qtd total por especie
$sqlPDF .= " SELECT
                e.nom_especie, e.cod_especie,
                sum(b.vl_bem) as total_especie,
                count(b.cod_bem) as qtd_especie
            FROM
                patrimonio.natureza n
                INNER JOIN patrimonio.grupo AS g ON
                    n.cod_natureza = g.cod_natureza
                INNER JOIN patrimonio.especie AS e ON
                    e.cod_natureza = g.cod_natureza AND
                    e.cod_grupo    = g.cod_grupo
                LEFT OUTER JOIN patrimonio.bem AS b ON
                    e.cod_natureza = b.cod_natureza AND
                    e.cod_grupo    = b.cod_grupo AND
                    e.cod_especie  = b.cod_especie
                LEFT OUTER JOIN patrimonio.bem_baixado AS bb ON
                    bb.cod_bem = b.cod_bem
                ".$filtroInner."
            WHERE
                    bb.cod_bem IS NULL
                AND n.cod_natureza = &cod_natureza
                AND g.cod_grupo    = &cod_grupo
                ".$filtro."
            GROUP BY
                e.nom_especie, e.cod_especie
            ORDER BY
                e.nom_especie;";

//Seleciona o valor total e a quantidade total de bens ativos
$sqlPDF .= " SELECT
                sum(b.vl_bem) as valor_total,
                count(b.cod_bem) as qtd_total
            FROM
                patrimonio.bem AS b
                LEFT OUTER JOIN patrimonio.bem_baixado AS bb ON
                    bb.cod_bem = b.cod_bem
                ".$filtroInner."
            WHERE
                bb.cod_bem IS NULL ".$filtro.";";

$sXML       = '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/instancias/relatorio/cargaPatrimonialTotalizador.xml';
$sSubTitulo = "Totalizador";
$botoesPDF  = new botoesPdfLegado;
$botoesPDF->imprimeBotoes($sXML,$sqlPDF,'',$sSubTitulo);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php'; //Insere o fim da página html
?>
