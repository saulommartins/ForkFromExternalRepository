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
    * Relatório em Birt que apresenta o mapa comparativo das propostas
    * Data de Criação: 18/11/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Diogo Zarpelon

    * @ignore

    $Id: OCGeraManterManutencaoProposta.php 63841 2015-10-22 19:14:30Z michel $

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";

$stAcao             = $_REQUEST['stAcao'];
$inCodCotacao       = $_REQUEST['inCodCotacao'];
$stExercicioCotacao = Sessao::getExercicio();

//Busca nome da entidade
if ($_REQUEST['inCodEntidade']) {
    $obTOrcamentoEntidade = new TOrcamentoEntidade();
    $obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
    $obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "AND e.cod_entidade = '".$_REQUEST['inCodEntidade']."'");

    $inCodEntidade = $rsEntidade->getCampo('cod_entidade');
    $stNomEntidade = $rsEntidade->getCampo('nom_cgm');
}

if ($stAcao == 'reemitir') {
    list($inCodLicitacao, $stExercicioLicitacao) = explode("/", $_REQUEST['inCodLicitacao']);
    $stModalidade       = SistemaLegado::pegaDado("descricao", "compras.modalidade", "WHERE cod_modalidade = ".$_REQUEST['inCodModalidade']);
    $inCodObjeto        = SistemaLegado::pegaDado("cod_objeto", "compras.mapa", "WHERE cod_mapa = ".$_REQUEST['inCodMapa']." AND exercicio = ".$_REQUEST['stExercicioMapa']."::VARCHAR");
    $inCodTipoObjeto    = SistemaLegado::pegaDado("cod_tipo_objeto", "licitacao.licitacao", "WHERE cod_licitacao = ".$inCodLicitacao." AND cod_modalidade = ".$_REQUEST['inCodModalidade']." AND cod_entidade = ".$inCodEntidade." AND exercicio = ".$stExercicioLicitacao."::VARCHAR");
    $inCodCotacao       = SistemaLegado::pegaDado("cod_cotacao"      , "compras.mapa_cotacao", "WHERE cod_mapa = ".$_REQUEST['inCodMapa']." AND exercicio_mapa = ".$_REQUEST['stExercicioMapa']."::VARCHAR AND NOT EXISTS (SELECT 1 FROM compras.cotacao_anulada AS CA WHERE CA.exercicio=mapa_cotacao.exercicio_cotacao AND CA.cod_cotacao=mapa_cotacao.cod_cotacao)");
    $stExercicioCotacao = SistemaLegado::pegaDado("exercicio_cotacao", "compras.mapa_cotacao", "WHERE cod_mapa = ".$_REQUEST['inCodMapa']." AND exercicio_mapa = ".$_REQUEST['stExercicioMapa']."::VARCHAR");
    $inCodTipoLicitacao = $_REQUEST['inCodTipoLicitacao'];

    $stDataManutencao   = SistemaLegado::pegaDado("timestamp"   , "compras.cotacao", "WHERE cod_cotacao = ".$inCodCotacao." AND exercicio = ".$stExercicioCotacao."::VARCHAR");
    $stDataManutencao   = SistemaLegado::dataToBr($stDataManutencao);

    $stHeader           = "Edital nº ".$_REQUEST['inNumEdital']."/".$_REQUEST['stExercicioEdital']." - Modalidade: ".$stModalidade." nº ".$inCodLicitacao."/".$stExercicioLicitacao;

} elseif ($stAcao == 'reemitirCompra') {
    $inCodEntidade       = $_REQUEST['inCodEntidade'];
    $inCodModalidade     = $_REQUEST['inCodModalidade'];
    $inCompraDireta      = $_REQUEST['inCompraDireta'];
    $stExercicioEntidade = $_REQUEST['stExercicioEntidade'];

    $inCodCotacao       = SistemaLegado::pegaDado("cod_cotacao" , "compras.mapa_cotacao", "WHERE cod_mapa = ".$_REQUEST['inCodMapa']." AND exercicio_mapa = '".$_REQUEST['stExercicioMapa']."' AND NOT EXISTS (SELECT 1 FROM compras.cotacao_anulada AS CA WHERE CA.exercicio=mapa_cotacao.exercicio_cotacao AND CA.cod_cotacao=mapa_cotacao.cod_cotacao)");
    $stExercicioCotacao = SistemaLegado::pegaDado("exercicio_cotacao" , "compras.mapa_cotacao", "WHERE cod_mapa = ".$_REQUEST['inCodMapa']." AND exercicio_mapa = '".$_REQUEST['stExercicioMapa']."'");

    $inCodObjeto        = SistemaLegado::pegaDado("cod_objeto", "compras.mapa", "WHERE cod_mapa = ".$_REQUEST['inCodMapa']." AND exercicio = '".$_REQUEST['stExercicioMapa']."'");
    $inCodTipoObjeto    = SistemaLegado::pegaDado("cod_tipo_objeto", "compras.compra_direta", "WHERE cod_compra_direta = ".$inCompraDireta." AND cod_modalidade = ".$inCodModalidade." AND cod_entidade = ".$inCodEntidade." AND exercicio_entidade = '".$stExercicioEntidade."'");
    $stModalidade       = SistemaLegado::pegaDado("descricao", "compras.modalidade", "WHERE cod_modalidade = ".$inCodModalidade);
    $inCodTipoLicitacao = $_REQUEST['inCodTipoLicitacao'];

    $stDataManutencao   = SistemaLegado::pegaDado("timestamp"   , "compras.cotacao", "WHERE cod_cotacao = ".$inCodCotacao." AND exercicio = '".$stExercicioCotacao."'");
    $stDataManutencao   = SistemaLegado::dataToBr($stDataManutencao);

    $stHeader           = "Compra Direta nº ".$inCompraDireta."/".$stExercicioEntidade." - Modalidade: ".$stModalidade;

} else {
    list($inCodMapa, $stExercicioMapa) = explode("/", $_REQUEST['stMapaCompras']);

    // Passando o objeto da Compra Direta ou da Licitação.
    if ($stAcao == "dispensaLicitacao") {
        # Indica que a proposta foi feita através de uma Compra Direta.
        $inCodEntidade       = $_REQUEST['inCodEntidade'];
        $inCodModalidade     = $_REQUEST['inCodModalidade'];
        $inCompraDireta      = $_REQUEST['inCompraDireta'];
        $stExercicioEntidade = $_REQUEST['stExercicioEntidade'];
        $stDataManutencao    = $_REQUEST['stDataManutencao'];

        $inCodTipoObjeto = SistemaLegado::pegaDado("cod_tipo_objeto", "compras.compra_direta", "WHERE cod_compra_direta = ".$inCompraDireta." AND cod_modalidade = ".$inCodModalidade." AND cod_entidade = ".$inCodEntidade." AND exercicio_entidade = '".$stExercicioEntidade."'");
        $stModalidade    = SistemaLegado::pegaDado("descricao", "compras.modalidade", "WHERE cod_modalidade = ".$inCodModalidade);
        $stHeader        = "Compra Direta nº ".$inCompraDireta."/".$stExercicioEntidade." - Modalidade: ".$stModalidade;

    } else {
        # Indica que a proposta foi feita através de uma Licitação.
        list($inCodLicitacao, $stExercicioLicitacao) = explode("/", $_REQUEST['inCodLicitacao']);
        $inCodModalidade  = $_REQUEST['inCodModalidade'];
        $inCodEntidade    = $_REQUEST['inCodEntidade'];
        $stDataManutencao = $_REQUEST['stDataManutencao'];

        $inCodObjeto        = SistemaLegado::pegaDado("cod_objeto", "compras.mapa", "WHERE cod_mapa = ".$inCodMapa." AND exercicio = ".$stExercicioMapa."::VARCHAR");
        $inCodTipoLicitacao = SistemaLegado::pegaDado("cod_tipo_licitacao", "compras.mapa", "WHERE cod_mapa = ".$inCodMapa." AND exercicio = ".$stExercicioMapa."::VARCHAR");

        $inCodTipoObjeto   = SistemaLegado::pegaDado("cod_tipo_objeto", "licitacao.licitacao", "WHERE cod_licitacao = ".$inCodLicitacao." AND cod_modalidade = ".$inCodModalidade." AND cod_entidade = ".$inCodEntidade." AND exercicio = ".$stExercicioLicitacao."::VARCHAR");
        $inCodEdital       = SistemaLegado::pegaDado("num_edital", "licitacao.edital", "WHERE not exists( Select 1 FROM licitacao.edital_anulado WHERE edital_anulado.num_edital = edital.num_edital) AND  cod_licitacao = ".$inCodLicitacao." AND cod_modalidade = ".$inCodModalidade." AND cod_entidade = ".$inCodEntidade." AND exercicio_licitacao = ".$stExercicioLicitacao."::VARCHAR");
        $stExercicioEdital = SistemaLegado::pegaDado("exercicio", "licitacao.edital", "WHERE not exists( Select 1 FROM licitacao.edital_anulado WHERE edital_anulado.num_edital = edital.num_edital) AND  cod_licitacao = ".$inCodLicitacao." AND cod_modalidade = ".$inCodModalidade." AND cod_entidade = ".$inCodEntidade." AND exercicio_licitacao = ".$stExercicioLicitacao."::VARCHAR");
        $stModalidade      = SistemaLegado::pegaDado("descricao", "compras.modalidade", "WHERE cod_modalidade = ".$inCodModalidade);
        $stHeader          = "Edital nº ".$inCodEdital."/".$stExercicioEdital." - Modalidade: ".$stModalidade." nº ".$inCodLicitacao."/".$stExercicioLicitacao;
    }

    $inCodObjeto        = SistemaLegado::pegaDado("cod_objeto", "compras.mapa", "WHERE cod_mapa = ".$inCodMapa." AND exercicio = ".$stExercicioMapa."::VARCHAR");
    $inCodTipoLicitacao = SistemaLegado::pegaDado("cod_tipo_licitacao", "compras.mapa", "WHERE cod_mapa = ".$inCodMapa." AND exercicio = ".$stExercicioMapa."::VARCHAR");
}

$preview = new PreviewBirt(3,37,3);

$preview->setVersaoBirt('2.5.0');
$preview->setNomeArquivo('mapaComparativoProposta');
$preview->setTitulo('Mapa Comparativo de Propostas');

if ($rsEntidade->getNumLinhas() == 1) {
    $preview->addParametro( 'codigo_e_entidade', $inCodEntidade." - ".$stNomEntidade );
}

# Seta os valores dos parâmetros do relatório.
$preview->addParametro( 'cod_cotacao'        , $inCodCotacao       );
$preview->addParametro( 'exercicio_cotacao'  , $stExercicioCotacao );
$preview->addParametro( 'cod_tipo_objeto'    , $inCodTipoObjeto    );
$preview->addParametro( 'data_emissao'       , $stDataManutencao   );
$preview->addParametro( 'cod_objeto'         , $inCodObjeto        );
$preview->addParametro( 'cod_tipo_licitacao' , $inCodTipoLicitacao );
$preview->addParametro( 'st_header'          , $stHeader           );
$preview->preview();
