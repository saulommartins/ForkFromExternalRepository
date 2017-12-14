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
    * Página de Relatório RGF Anexo6
    * Data de Criação   : 15/02/2008

    * @author Analista: Valtair Lacerda
    * @author Desenvolvedor: Leopoldo Barreiro

    * @ignore

    $Revision: $
    $Name$
    $Author: $
    $Date: $

    * Casos de uso :
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , $_REQUEST['stExercicio'] );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, " and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$obErro = new Erro();

if (Sessao::getExercicio() < '2015') {
    $preview = new PreviewBirt(6,36,9);
    $preview->setTitulo('Demonstrativo dos Restos a Pagar');
    $preview->setVersaoBirt( '2.5.0' );
    $preview->setExportaExcel ( true );
} else {
    # Alterar para códigos do novo relatório
    die();
    //$preview = new PreviewBirt(6,36,9); 
    //$preview->setTitulo('Dem Disponibilidades de Caixa');
    //$preview->setVersaoBirt( '2.5.0' );
    //$preview->setExportaExcel( true );
}

$stDataInicial = "01/01/".$_REQUEST['stExercicio'];

if ( strtolower($_REQUEST['stTipoRelatorio']) == 'ultimoquadrimestre'  ) {
    switch ($_REQUEST['cmbQuadrimestre']) {
        case 3:
            $stDataFinal = '31/12';
            $stIntervalo = '3º Quadrimestre de ' . $_REQUEST['stExercicio'];
        break;
    }
    $nuPeriodo = $_REQUEST['cmbQuadrimestre'] ;
} elseif ( strtolower($_REQUEST['stTipoRelatorio']) == 'bimestre' ) {
    switch ($_REQUEST['cmbBimestre']) {
        case 1:
            $stDataFinal = '29/02';
            $stIntervalo = '1º Bimestre de ' . $_REQUEST['stExercicio'];
        break;
        case 2:
            $stDataFinal = '30/04';
            $stIntervalo = '2º Bimestre de ' . $_REQUEST['stExercicio'];
        break;
        case 3:
            $stDataFinal = '30/06';
            $stIntervalo = '3º Bimestre de ' . $_REQUEST['stExercicio'];
        break;
        case 4:
            $stDataFinal = '31/08';
            $stIntervalo = '4º Bimestre de ' . $_REQUEST['stExercicio'];
        break;
        case 5:
            $stDataFinal = '31/10';
            $stIntervalo = '5º Bimestre de ' . $_REQUEST['stExercicio'];
        break;
        case 6:
            $stDataFinal = '31/12';
            $stIntervalo = '6º Bimestre de ' . $_REQUEST['stExercicio'];
        break;
    }
    $nuPeriodo = $_REQUEST['cmbBimestre'] ;
} elseif ( strtolower($_REQUEST['stTipoRelatorio']) == 'quadrimestre' ) {
    switch ($_REQUEST['cmbQuadrimestre']) {
        case 1:
            $stDataFinal = '30/04';
            $stIntervalo = '1º Quadrimestre de ' . $_REQUEST['stExercicio'];
        break;
        case 2:
            $stDataFinal = '31/08';
            $stIntervalo = '2º Quadrimestre de ' . $_REQUEST['stExercicio'];
        break;
        case 3:
            $stDataFinal = '31/12';
            $stIntervalo = '3º Quadrimestre de ' . $_REQUEST['stExercicio'];
        break;
    }
    $nuPeriodo = $_REQUEST['cmbQuadrimestre'] ;
} elseif ( strtolower($_REQUEST['stTipoRelatorio']) == 'semestre' or  strtolower($_REQUEST['stTipoRelatorio']) == 'ultimosemestre' ) {
    switch ($_REQUEST['cmbSemestre']) {
        case 1:
            $stDataFinal = '30/06';
            $stIntervalo = '1º Semestre de ' . $_REQUEST['stExercicio'];
        break;
        case 2:
            $stDataFinal = '31/12';
            $stIntervalo = '2º Semestre de ' . $_REQUEST['stExercicio'];
        break;
    }
    $nuPeriodo = $_REQUEST['cmbSemestre'] ;
}

$stDataFinal = $stDataFinal . '/' . $_REQUEST['stExercicio'];

$preview->addParametro( 'entidade', implode(',', $_REQUEST['inCodEntidade'] ) );
$preview->addParametro( 'data_inicio', $stDataInicial );
$preview->addParametro( 'data_fim', $stDataFinal );
$preview->addParametro( 'exercicio', $_REQUEST['stExercicio'] );
$preview->addParametro( 'intervalo', $stIntervalo );

if (preg_match( "/prefeitura/i", $rsEntidade->getCampo( 'nom_cgm' ) ) || ( count($_REQUEST['inCodEntidade']) > 0 ) ) {
    $preview->addParametro( 'poder' , 'Executivo' );
} elseif (preg_match( "/c[âa]mara/i", $rsEntidade->getCampo( 'nom_cgm' ) )) {
    $preview->addParametro( 'poder' , 'Legislativo' );
}

$rsEntidade->setPrimeiroElemento();
while (!$rsEntidade->eof()) {
    $stNomeEntidade = $rsEntidade->getCampo('nom_cgm');
    if (preg_match( "/prefeitura/i", $stNomeEntidade ) || preg_match( "/c[âa]mara/i", $stNomeEntidade )) {
        $preview->addParametro('nom_entidade', $stNomeEntidade);
    }
    $rsEntidade->proximo();
}

if ($_REQUEST['stAcao'] == 'anexo6novo') {
    $preview->addParametro( 'relatorio_novo', 'sim' );
} else {
    $preview->addParametro( 'relatorio_novo', 'nao' );
}

//adiciona unidade responsável ao relatório
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php";

$stFiltro = " WHERE sw_cgm.numcgm = ".Sessao::read('numCgm');
$obTAdministracaoUsuario = new TAdministracaoUsuario;

$obTAdministracaoUsuario->recuperaRelacionamento($rsUsuario, $stFiltro);
$preview->addParametro( 'unidade_responsavel', $rsUsuario->getCampo('orgao') );

//adicionada data de emissão no rodapé do relatório
$dtDataEmissao = date('d/m/Y');
$dtHoraEmissao = date('H:i');
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', $stDataEmissao );

$preview->addAssinaturas(Sessao::read('assinaturas'));
if( !$obErro->ocorreu() )
    $preview->preview();
else
    SistemaLegado::alertaAviso("FLModelosRGF.php?'.Sessao::getId().&stAcao=$stAcao", $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");
