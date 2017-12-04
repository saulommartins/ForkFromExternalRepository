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
    * Página de lista
    * Data de Criação: 13/02/2009

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Grasiele Torres

    * @ignore

    * Caso de uso: uc-03.03.31

    $Id:$

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoNaturezaLancamento.class.php";

# Define o nome dos arquivos PHP
$stPrograma = "ReemitirEntrada";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

$stLink = '&stAcao='.$stAcao;

$arFiltro = Sessao::read('arFiltro');

foreach ($_REQUEST as $key => $value)
    $arFiltro[$key] = $value;

Sessao::write("paginando",true);
Sessao::write('arFiltro', $arFiltro);

# Filtra pelo CGM do Fornecedor, pesquisa nas tabelas de nota_fiscal_fornecedor e patrimonio_bem (compatibilidade)
if (!empty($arFiltro['inCGM'])) {

    $stFiltro .= "  LEFT JOIN  compras.nota_fiscal_fornecedor                                                         \n";
    $stFiltro .= "         ON  nota_fiscal_fornecedor.exercicio_lancamento = natureza_lancamento.exercicio_lancamento \n";
    $stFiltro .= "        AND  nota_fiscal_fornecedor.num_lancamento       = natureza_lancamento.num_lancamento       \n";
    $stFiltro .= "        AND  nota_fiscal_fornecedor.cod_natureza         = natureza_lancamento.cod_natureza         \n";
    $stFiltro .= "        AND  nota_fiscal_fornecedor.tipo_natureza        = natureza_lancamento.tipo_natureza        \n";
    $stFiltro .= "                                                                                                    \n";
    $stFiltro .= "  LEFT JOIN (                                                                                       \n";
    $stFiltro .= "                SELECT  lancamento_bem.*                                                            \n";
    $stFiltro .= "                     ,  bem.*                                                                       \n";
    $stFiltro .= "                  FROM  almoxarifado.lancamento_bem                                                 \n";
    $stFiltro .= "                                                                                                    \n";
    $stFiltro .= "             LEFT JOIN  almoxarifado.lancamento_material                                            \n";
    $stFiltro .= "                    ON  lancamento_bem.cod_lancamento   = lancamento_material.cod_lancamento        \n";
    $stFiltro .= "                   AND  lancamento_bem.cod_item         = lancamento_material.cod_item              \n";
    $stFiltro .= "                   AND  lancamento_bem.cod_marca        = lancamento_material.cod_marca             \n";
    $stFiltro .= "                   AND  lancamento_bem.cod_almoxarifado = lancamento_material.cod_almoxarifado      \n";
    $stFiltro .= "                   AND  lancamento_bem.cod_centro       = lancamento_material.cod_centro            \n";
    $stFiltro .= "                                                                                                    \n";
    $stFiltro .= "            INNER JOIN  patrimonio.bem                                                              \n";
    $stFiltro .= "                    ON  bem.cod_bem = lancamento_bem.cod_bem                                        \n";
    $stFiltro .= "                                                                                                    \n";
    $stFiltro .= "                 WHERE  bem.numcgm = ".$arFiltro['inCGM']."										  \n";
    $stFiltro .= "                                                                                                    \n";
    $stFiltro .= "            ) as lancamento_bem                                                                     \n";
    $stFiltro .= "         ON  lancamento_bem.cod_lancamento   = lancamento_material.cod_lancamento                   \n";
    $stFiltro .= "        AND  lancamento_bem.cod_item         = lancamento_material.cod_item                         \n";
    $stFiltro .= "        AND  lancamento_bem.cod_marca        = lancamento_material.cod_marca                        \n";
    $stFiltro .= "        AND  lancamento_bem.cod_almoxarifado = lancamento_material.cod_almoxarifado                 \n";
    $stFiltro .= "        AND  lancamento_bem.cod_centro       = lancamento_material.cod_centro                       \n";
}

# Busca somente movimentações de Entrada.
$stFiltro .= " WHERE natureza_lancamento.tipo_natureza = 'E' \n";

# Filtra pelo codigo do item.
if (!empty($arFiltro['inCodItem'])) {
    $stFiltro .= " AND lancamento_material.cod_item = ".$arFiltro['inCodItem'];
}

# Monta uma string com os códigos da Natureza vindos de um array.
if (is_array($arFiltro['inCodNaturezaSelecionados'])) {
    $arCodNatureza = implode($arFiltro['inCodNaturezaSelecionados'], ',');
    $stFiltro .= " AND natureza_lancamento.cod_natureza IN (".$arCodNatureza.") \n";
}

if (!empty($arFiltro['inNumEntrada']))
    $stFiltro .= "  AND natureza_lancamento.num_lancamento = ".$arFiltro["inNumEntrada"]. "\n";

if (!empty($arFiltro['inPeriodicidade'])) {
    if ($arFiltro['stDataInicial']) {
        $dtDataInicial = $arFiltro["stDataInicial"];
        $dtDataFinal   = $arFiltro["stDataFinal"];

        $stFiltro .= "  AND TO_DATE(to_char(timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$dtDataInicial."','dd/mm/yyyy')   \n";
        $stFiltro .= "  AND TO_DATE('".$dtDataFinal."','dd/mm/yyyy')                                   \n";
    }
}

# Filtra pelo CGM do Fornecedor na Tabela de Nota_Fiscal_Fornecedor.
if (!empty($arFiltro['inCGM'])) {
    $stFiltro .= "  AND  nota_fiscal_fornecedor.cgm_fornecedor = ".$arFiltro['inCGM'];
}

$stGrupo = " GROUP BY  natureza_lancamento.TIMESTAMP
                    ,  natureza_lancamento.num_lancamento
                    ,  natureza.cod_natureza
                    ,  natureza.descricao
                    ,  natureza_lancamento.exercicio_lancamento
                       ,  lancamento_requisicao.exercicio  \n";

$stOrdem = "ORDER BY  natureza.descricao
                   ,  natureza_lancamento.num_lancamento  DESC";

$obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;
$obTAlmoxarifadoNaturezaLancamento->recuperaDadosReemissao($rsDados, $stFiltro, $stGrupo, $stOrdem);

$obLista = new Lista;
$obLista->setAjuda('UC-03.03.31');
$obLista->obPaginacao->setFiltro('&stLink='.$stLink);

$obLista->setRecordSet ( $rsDados );
$obLista->setTitulo    ( "Registros" );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Natureza" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entrada" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "data" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("E");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "num_lancamento" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( 'Selecionar' );

$obLista->ultimaAcao->addCampo("&inNumLancamento"    , "num_lancamento"       );
$obLista->ultimaAcao->addCampo("&inCodNatureza"      , "cod_natureza"         );
$obLista->ultimaAcao->addCampo("&exercicio"  	     , "exercicio_lancamento" );
$obLista->ultimaAcao->setLink ( $pgProc."?".Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
