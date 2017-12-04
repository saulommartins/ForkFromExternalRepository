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
    * Relatório em Birt que apresenta o julgamento da proposta
    * Data de Criação: 06/09/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Diogo Zarpelon

    * @ignore

    $Id: OCGeraManterJulgamentoProposta.php 63841 2015-10-22 19:14:30Z michel $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$inCodCotacao       = $_REQUEST['inCodCotacao'];
$stExercicioCotacao = $_REQUEST['stExercicioCotacao'];
$stDtEmissao        = $_REQUEST['stDtEmissao'];
$stHrEmissao        = $_REQUEST['stHrEmissao'];
$inCodCompraDireta  = $_REQUEST['inCodCompraDireta'];
$stLicitacao        = $_REQUEST['inCodLicitacao'];
$inCodModalidade    = $_REQUEST['inCodModalidade'];
$inCodEntidade      = (!empty($_REQUEST['inCodEntidade']) ? $_REQUEST['inCodEntidade'] : $_REQUEST['stEntidade']);
$stIncluirAssinaturas = $_REQUEST['stIncluirAssinaturas'];
$stAcao             = $_REQUEST['stAcao'];

# Instancia Preview.
$preview = new PreviewBirt(3,37,2);
$preview->setVersaoBirt( '2.5.0' );
$preview->setNomeArquivo('julgamentoPropostas');
$preview->setTitulo('Relatório de Julgamento de Propostas');

# Para Compra Direta.
if ($stAcao == "dispensaLicitacao" || !empty($inCodCompraDireta)) {
    include_once( CAM_GP_COM_MAPEAMENTO."TComprasCompraDireta.class.php" );

    $obTComprasCompraDireta = new TComprasCompraDireta;
    $stFiltro  = " WHERE compra_direta.cod_compra_direta  =  ".$inCodCompraDireta;
    $stFiltro .= "   AND compra_direta.cod_entidade       = '".$inCodEntidade."'";
    $stFiltro .= "   AND compra_direta.exercicio_entidade = '".Sessao::getExercicio()."'";
    $stFiltro .= "   AND compra_direta.cod_modalidade     =  ".$inCodModalidade;
    $obTComprasCompraDireta->recuperaCompraDireta($rsRecordSet, $stFiltro);

    if ($rsRecordSet->getNumLinhas() == 1) {
        $preview->addParametro( 'prm_compra_direta' , $rsRecordSet->getCampo('cod_compra_direta'));

        $stModalidade = $rsRecordSet->getCampo('modalidade');
        $stTipoObjeto = $rsRecordSet->getCampo('desc_tipo_objeto');
        $stObjeto     = $rsRecordSet->getCampo('desc_objeto');
        $stEntidade   = $rsRecordSet->getCampo('cod_entidade')." - ".$rsRecordSet->getCampo('entidade');

    }
} elseif ($stAcao == "manter" || !empty($stLicitacao)) {
    # Para Licitação.

    list($inCodLicitacao, $stExercicioLicitacao) = explode('/', $stLicitacao);

    $inCodTipoObjeto   = SistemaLegado::pegaDado("cod_tipo_objeto", "licitacao.licitacao", "WHERE cod_licitacao = ".$inCodLicitacao." AND cod_modalidade = ".$inCodModalidade." AND cod_entidade = ".$inCodEntidade." AND exercicio = ".$stExercicioLicitacao."::VARCHAR");
    $stTipoObjeto      = SistemaLegado::pegaDado("descricao", "compras.tipo_objeto", "WHERE cod_tipo_objeto = ".$inCodTipoObjeto);

    $inCodObjeto       = SistemaLegado::pegaDado("cod_objeto", "licitacao.licitacao", "WHERE cod_licitacao = ".$inCodLicitacao." AND cod_modalidade = ".$inCodModalidade." AND cod_entidade = ".$inCodEntidade." AND exercicio = ".$stExercicioLicitacao."::VARCHAR");
    $stObjeto          = SistemaLegado::pegaDado("descricao", "compras.objeto", "WHERE cod_objeto = ".$inCodObjeto);

    $inCodEdital       = SistemaLegado::pegaDado("num_edital", "licitacao.edital", "WHERE not exists( Select 1 FROM licitacao.edital_anulado WHERE edital_anulado.num_edital = edital.num_edital) AND cod_licitacao = ".$inCodLicitacao." AND cod_modalidade = ".$inCodModalidade." AND cod_entidade = ".$inCodEntidade." AND exercicio_licitacao = ".$stExercicioLicitacao."::VARCHAR");
    $stExercicioEdital = SistemaLegado::pegaDado("exercicio", "licitacao.edital", "WHERE not exists( Select 1 FROM licitacao.edital_anulado WHERE edital_anulado.num_edital = edital.num_edital) AND cod_licitacao = ".$inCodLicitacao." AND cod_modalidade = ".$inCodModalidade." AND cod_entidade = ".$inCodEntidade." AND exercicio_licitacao = ".$stExercicioLicitacao."::VARCHAR");

    $inCodModalidade   = SistemaLegado::pegaDado("cod_modalidade", "licitacao.licitacao", "WHERE cod_licitacao = ".$inCodLicitacao." AND cod_modalidade = ".$inCodModalidade." AND cod_entidade = ".$inCodEntidade." AND exercicio = ".$stExercicioLicitacao."::VARCHAR");
    $stModalidade      = SistemaLegado::pegaDado("descricao", "compras.modalidade", "WHERE cod_modalidade = ".$inCodModalidade);

    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"                                 );
    $obTOrcamentoEntidade = new TOrcamentoEntidade();
    $obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
    $obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "AND e.cod_entidade = ".$inCodEntidade);
    $stNomCgm          = $rsEntidade->getCampo('nom_cgm');

    $stEdital   = $inCodEdital."/".$stExercicioEdital;
    $stEntidade = $inCodEntidade." - ".$stNomCgm;

    $preview->addParametro('prm_edital'    , $stEdital);
    $preview->addParametro('prm_licitacao' , $stLicitacao);
}

# Seta os parâmetros do relatório.
$preview->addParametro('cod_cotacao'       , $inCodCotacao);
$preview->addParametro('exercicio_cotacao' , $stExercicioCotacao);
$preview->addParametro('data_emissao'      , (($stDtEmissao != '') ? $stDtEmissao : ''));
$preview->addParametro('hora_emissao'      , (($stHrEmissao != '') ? $stHrEmissao : ''));
$preview->addParametro('prm_modalidade'    , $stModalidade );
$preview->addParametro('prm_tipo_objeto'   , $stTipoObjeto );
$preview->addParametro('prm_objeto'        , $stObjeto     );
$preview->addParametro('prm_entidade'      , $stEntidade   );

$stIncluirAssinaturas = $_REQUEST['stIncluirAssinaturas'];
if ($stIncluirAssinaturas == 'sim') {
    $stIncluirAssinaturas = 'sim';
} else {
    $stIncluirAssinaturas = 'não';
}

$preview->addParametro('incluir_assinaturas', $stIncluirAssinaturas );
$preview->addAssinaturas(Sessao::read('assinaturas'));

$preview->preview();
