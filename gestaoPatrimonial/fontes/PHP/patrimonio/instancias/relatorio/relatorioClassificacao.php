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
    * Relatório Classificação
    * Data de Criação   : 01/04/2003

    * @author Desenvolvedor  Alessandro La-Rocca Silveira

    * @ignore

    $Id:$

    * Casos de uso: uc-03.01.17
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../relatorio.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/botoesPdfLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.17");
if (!(isset($pagina))) {
    unset($sessao->transf2);
}
$select = "SELECT
            natu.cod_natureza|| '.' ||grupo.cod_grupo|| '.' ||especie.cod_especie as codigo,
            natu.nom_natureza,
            grupo.nom_grupo,
            especie.nom_especie
        FROM
            patrimonio.natureza AS natu,
            patrimonio.grupo    AS grupo,
            patrimonio.especie  AS especie
        WHERE
            grupo.cod_natureza   = natu.cod_natureza AND
            especie.cod_natureza = natu.cod_natureza AND
            especie.cod_grupo    = grupo.cod_grupo";
if (!(isset($sessao->transf2))) {
    $sessao->transf = "";
    $sessao->transf = $select;
    $sessao->transf2 = "especie.cod_natureza, grupo.cod_grupo, cod_especie";
}
$botoesPdf = new botoesPdfLegado;
$paginacao = new paginacaoLegada;

$sqlPDF = $sessao->transf;;
$sqlPDF .= " order by ".$sessao->transf2." ASC";

$botoesPdf->imprimeBotoes('../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/relatorios/relatorioClassificacao.xml',$sqlPDF,'Relatório de Classificação');

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
