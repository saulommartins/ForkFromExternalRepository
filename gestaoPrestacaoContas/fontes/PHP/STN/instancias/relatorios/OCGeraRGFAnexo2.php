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
    * Página de Relatório RGF Anexo2
    * Data de Criação   : 28/11/2007

    * @author Tonismar Régis Bernardo

    * @ignore

     * Casos de uso : uc-06.01.21

     $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$obErro = new Erro();

if ( !$request->get('cmbBimestre') && !$request->get('cmbQuadrimestre') && !$request->get('cmbSemestre') && !$request->get('cmbMensal') ) {
    $obErro->setDescricao('É preciso selecionar ao menos um '.$_REQUEST['stTipoRelatorio'].'.');
}

$stAno = Sessao::getExercicio();

if ($_REQUEST['stTipoRelatorio'] == "Mes") {    
    $preview = new PreviewBirt(6,36,60);
    $preview->setTitulo('Dem Dívida Consolidada Líquida');
    $preview->setVersaoBirt( '2.5.0' );
    $preview->setExportaExcel( true );
}else{
    $preview = new PreviewBirt(6,36,2);
    $preview->setTitulo('Dem Dívida Consolidada Líquida');
    $preview->setVersaoBirt( '2.5.0' );
    $preview->setExportaExcel( true );
}

$preview->addParametro( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade'] ) );
if ( count($_REQUEST['inCodEntidade']) == 1 ) {
    $preview->addParametro( 'nome_entidade', $rsEntidade->getCampo('nom_cgm') );
} else {
    while ( !$rsEntidade->eof() ) {
        if ( preg_match( "/prefeitura.*/i", $rsEntidade->getCampo('nom_cgm')) ) {
            $preview->addParametro( 'nome_entidade', $rsEntidade->getCampo('nom_cgm') );
            break;
        }
        $rsEntidade->proximo();
    }
}

$rsEntidade->setPrimeiroElemento();

if ( preg_match( "/câmara.*/i", $rsEntidade->getCampo( 'nom_cgm' ) ) && ( count($rsEntidade) == 1 ) ) {
    $preview->addParametro( 'poder' , 'Legislativo' );
} else {
    $preview->addParametro( 'poder' , 'Executivo' );
}

$preview->addParametro( 'tipo_periodo', $_REQUEST['stTipoRelatorio'] );
$preview->addParametro( 'entidade'    , implode(',', $_REQUEST['inCodEntidade']) );

$stEntidadeRPPS = SistemaLegado::pegaConfiguracao('cod_entidade_rpps', 8);
if (in_array($stEntidadeRPPS, $_REQUEST['inCodEntidade'])) {
    $preview->addParametro( 'entidade_rpps', $stEntidadeRPPS );
} else {
    $preview->addParametro( 'entidade_rpps'    , '' );
}

$inPeriodo="";
switch( $_REQUEST['stTipoRelatorio'] ):
    case 'Mes':
        $preview->addParametro( 'periodo', $_REQUEST['cmbMensal'] );
        $preview->addParametro( 'mes'    , SistemaLegado::mesExtensoBR($_REQUEST['cmbMensal']) );
        $numPeriodo = $_REQUEST['cmbMensal'];
        $inPeriodo = $_REQUEST['cmbMensal'];
    case 'Quadrimestre':
        $preview->addParametro( 'periodo', $_REQUEST['cmbQuadrimestre'] );
        $numPeriodo = $_REQUEST['cmbQuadrimestre'];
        $inPeriodo = $_REQUEST['cmbQuadrimestre'];
    break;
    case 'Semestre':
        $preview->addParametro( 'periodo', $_REQUEST['cmbSemestre'] );
        $numPeriodo = $_REQUEST['cmbSemestre'];
        $inPeriodo = $_REQUEST['cmbSemestre'];
    break;
endswitch;

if ( preg_match( "/prefeitura/i", $rsEntidade->getCampo( 'nom_cgm' ) ) || ( count($_REQUEST['inCodEntidade']) > 1 ) ) {
    $preview->addParametro( 'poder' , 'Executivo' );
} elseif ( preg_match( "/c[âa]mara/i", $rsEntidade->getCampo( 'nom_cgm' ) ) ) {
    $preview->addParametro( 'poder' , 'Legislativo' );
}

// verificando se foi selecionado Câmara e outra entidade junto
$rsEntidade->setPrimeiroElemento();
if ( !$obErro->ocorreu() && ( count($_REQUEST['inCodEntidade']) != 1 ) ) {
    while ( !$rsEntidade->eof() ) {
        if ( preg_match( "/c[âa]mara/i", $rsEntidade->getCampo( 'nom_cgm' ) ) ) {
            $obErro->setDescricao( "Entidade ".$rsEntidade->getCampo('nom_cgm')." deve ser selecionada sozinha.");
            $boPreview = false;
            break;
        }
        $rsEntidade->proximo();
    }
}
$preview->addAssinaturas(Sessao::read('assinaturas'));

#############################Modificações do tce para o novo layout##############################
//adiciona unidade responsável ao relatório
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php" );
$stFiltro = " WHERE sw_cgm.numcgm = ".Sessao::read('numCgm');
$obTAdministracaoUsuario = new TAdministracaoUsuario;
$obTAdministracaoUsuario->recuperaRelacionamento($rsUsuario, $stFiltro);

$preview->addParametro( 'unidade_responsavel', $rsUsuario->getCampo('orgao') );

//adicionada data de emissão no rodapé do relatório
$dtDataEmissao = date('d/m/Y');
$dtHoraEmissao = date('H:i');
//necessário codificar os caracteres especias em ascii para o birt interpretar corretamente
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', $stDataEmissao );
#################################################################################################

if( !$obErro->ocorreu() )
    $preview->preview();
else
    SistemaLegado::alertaAviso("FLModelosRGF.php?'.Sessao::getId().&stAcao=$stAcao", $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");
