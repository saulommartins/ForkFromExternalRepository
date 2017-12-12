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
    * Relatório de posição financeira por Natureza
    * Data de Criação   : 08/04/2003

    * @author Desenvolvedor  Ricardo Lopes de Alencar

    * @ignore

    $Revision: 22607 $
    $Name$
    $Autor: $
    $Date: 2007-05-15 18:56:37 -0300 (Ter, 15 Mai 2007) $

    * Casos de uso: uc-03.01.09
*/

/*
$Log$
Revision 1.13  2007/05/15 21:56:37  leandro.zis
Bug #8347#

Revision 1.12  2006/10/31 13:44:39  larocca
Bug #6775#

Revision 1.11  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.10  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'; //Insere o início da página html
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';// Classe para paginar os dados
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/botoesPdfLegado.class.php';// Classe para gerar relatório em pdf

$stLigacao = " LEFT JOIN
            patrimonio.bem_baixado
         ON
            bem_baixado.cod_bem  = bem.cod_bem
         INNER JOIN
            patrimonio.bem_comprado
         ON
            bem_comprado.cod_bem = bem.cod_bem
         INNER JOIN
            orcamento.entidade
         ON
             entidade.cod_entidade = bem_comprado.cod_entidade
         AND entidade.exercicio    = bem_comprado.exercicio \n";

function montaWhere($tabela = "", $codInicial, $codFinal)
{
    $stWhere = " WHERE
                bem_baixado.cod_bem is null
            AND bem.cod_bem between '".(int) $codInicial."' AND '".(int) $codFinal."' \n";

    if ($_REQUEST["codEntidade"] != 'xxx') {
        $stWhere .= "        AND entidade.numcgm = ".$_REQUEST["codEntidade"]." \n" ;
    }

    if ($_REQUEST["exercicio"] != '')
       $stWhere .= "        AND bem_comprado.exercicio = ".$_REQUEST["exercicio"]." \n" ;

    if ($_REQUEST["codNatureza"] != 'xxx') {
        $stWhere .= "        AND ".$tabela.".cod_natureza = ".$_REQUEST["codNatureza"]." \n";
    }

    return $stWhere;
}

$sqlPDF = "
SELECT
        natureza.nom_natureza
     , natureza.cod_natureza
     , SUM(bem.vl_bem) as total_natureza
FROM
    patrimonio.natureza
    INNER JOIN
        patrimonio.bem
    ON
        bem.cod_natureza = natureza.cod_natureza
".$stLigacao." ".montaWhere("natureza", $codInicial, $codFinal)." \n";

$sqlPDF .= " GROUP BY \n";
$sqlPDF .= "         natureza.cod_natureza \n";
$sqlPDF .= "       , natureza.nom_natureza \n";
$sqlPDF .= " ORDER BY \n";
$sqlPDF .= "         natureza.nom_natureza \n";
$sqlPDF .= "       , natureza.cod_natureza; \n";

$sqlPDF .= "
SELECT
        grupo.nom_grupo
     ,  grupo.cod_grupo
     ,  grupo_plano_analitica.cod_plano
     , SUM(bem.vl_bem) AS total_grupo
FROM
    patrimonio.grupo
    INNER JOIN
        patrimonio.bem
    ON
             bem.cod_grupo = grupo.cod_grupo
        AND  bem.cod_natureza = grupo.cod_natureza
    INNER JOIN
        patrimonio.grupo_plano_analitica
    ON
            grupo_plano_analitica.cod_grupo = grupo.cod_grupo
        AND grupo_plano_analitica.cod_natureza = grupo.cod_natureza \n";
$sqlPDF .= " ".$stLigacao." ".montaWhere("grupo", $codInical, $codFinal)." \n";
$sqlPDF .= " AND grupo.cod_natureza = &cod_natureza \n";

$sqlPDF .= " GROUP BY \n";
$sqlPDF .= "         grupo.cod_grupo \n";
$sqlPDF .= "       , grupo_plano_analitica.cod_plano \n";
$sqlPDF .= "       , grupo.nom_grupo \n";
$sqlPDF .= " ORDER BY \n";
$sqlPDF .= "         grupo.nom_grupo \n";
$sqlPDF .= "       , grupo.cod_grupo; \n";

$sqlPDF .= "
SELECT
        especie.nom_especie
     ,  especie.cod_especie
     ,  SUM(bem.vl_bem) AS total_especie
FROM
    patrimonio.especie
    INNER JOIN
        patrimonio.bem
    ON
            bem.cod_especie = especie.cod_especie
        AND bem.cod_grupo = especie.cod_grupo
        AND bem.cod_natureza = especie.cod_natureza \n";
$sqlPDF .= " ".$stLigacao." ".montaWhere("especie", $codInicial, $codFinal)." \n";
$sqlPDF .= " AND especie.cod_natureza = &cod_natureza \n";
$sqlPDF .= " AND especie.cod_grupo = &cod_grupo \n";

$sqlPDF .= " GROUP BY \n";
$sqlPDF .= "         especie.cod_especie \n";
$sqlPDF .= "       , especie.nom_especie \n";
$sqlPDF .= " ORDER BY \n";
$sqlPDF .= "         especie.nom_especie \n";
$sqlPDF .= "       , especie.cod_especie; \n";

$sqlPDF .="
SELECT
    SUM(bem.vl_bem) as valor_total
FROM
    patrimonio.bem \n";

$sqlPDF .= " ".$stLigacao." ".montaWhere("bem", $codInicial, $codFinal)."; \n";

$sXML       = '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/relatorios/financeiraNatureza.xml';
$sSubTitulo = "";
$botoesPDF  = new botoesPdfLegado;
$botoesPDF->imprimeBotoes($sXML,$sqlPDF,'',$sSubTitulo);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';

?>
