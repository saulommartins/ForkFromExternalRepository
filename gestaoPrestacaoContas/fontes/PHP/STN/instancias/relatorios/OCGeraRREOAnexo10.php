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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";

$preview = new PreviewBirt(6,36,27);
$preview->addParametro( 'relatorio_novo', 'nao' );
$preview->setTitulo('Demonst. Receitas/Despesas com Manut. e Desenvol. do Ensino - MDE');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel ( true );

# Faz o update do parametro meta_resultado_nominal_fixada na table administracao.configuracao
$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
$obTAdministracaoConfiguracao->setDado('exercicio',Sessao::getExercicio());
$obTAdministracaoConfiguracao->setDado('cod_modulo',36);
$obTAdministracaoConfiguracao->setDado('parametro','stn_anexo10_porcentagem');
$obTAdministracaoConfiguracao->setDado('valor',$_REQUEST['flPct']);
$obTAdministracaoConfiguracao->alteracao();

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$preview->addParametro ( 'exercicio', Sessao::getExercicio() );
$preview->addParametro ( 'entidade', implode(',', $_REQUEST['inCodEntidade'] ) );
$preview->addParametro ( 'exercicio_anterior', (Sessao::getExercicio() - 1));

$flPctEducacao = str_replace(".", "", $_REQUEST['flPct']);
$flPctEducacao = str_replace(",", ".", $_REQUEST['flPct']);
$preview->addParametro ( 'pct_educacao', $flPctEducacao );

$stNomeEntidade = '';

while (!$rsEntidade->eof()) {
    if ( strpos( strtolower($rsEntidade->getCampo('nom_cgm')),'prefeitura') > -1 ) {
        $stNomeEntidade = $rsEntidade->getCampo('nom_cgm');
        break;
    }
    $rsEntidade->proximo();
}

if ($stNomeEntidade == '') {
   $rsEntidade->setPrimeiroElemento();
   $stNomeEntidade = $rsEntidade->getCampo('nom_cgm');
}

if ( count($_REQUEST['inCodEntidade']) > 0 ) {
    $preview->addParametro( 'nom_entidade', $stNomeEntidade );
} else {
    $preview->addParametro( 'nom_entidade', '' );
}

$preview->addParametro( 'tipo_periodo', $_REQUEST['stTipoRelatorio'] );

if ( preg_match( "/prefeitura/i", $rsEntidade->getCampo( 'nom_cgm' ) ) || ( count($_REQUEST['inCodEntidade']) > 1 ) ) {
    $preview->addParametro( 'poder' , 'Executivo' );
} elseif ( preg_match( "/c[âa]mara/i", $rsEntidade->getCampo( 'nom_cgm' ) ) ) {
    $preview->addParametro( 'poder' , 'Legislativo' );
}

$stDtInicio = $stDtFinal = '';

switch ($_REQUEST['stTipoRelatorio']) {
    case 'Mes':
        $preview->addParametro( 'periodo'      , $_REQUEST['cmbMes'] );
        $preview->addParametro( 'nome_periodo' , SistemaLegado::mesExtensoBR($_REQUEST['cmbMes'])." de ".Sessao::getExercicio() );
        $preview->addParametro( 'tipo_periodo', "Mês" ); 

        $stDtInicio = "01/".$request->get('cmbMes')."/".Sessao::getExercicio();
        $stDtFinal  = SistemaLegado::retornaUltimoDiaMes($request->get('cmbMes'),Sessao::getExercicio());
    break;

    case 'Bimestre':
        $preview->addParametro( 'bimestre'     , $_REQUEST['cmbBimestre'] );
        $preview->addParametro( 'periodo'      , $_REQUEST['cmbBimestre'] ); 
        $preview->addParametro( 'nome_periodo' , $_REQUEST['cmbBimestre']."º Bimestre de ".Sessao::getExercicio() );
        $preview->addParametro( 'tipo_periodo' , "Bimestre" ); 

        $stDtInicio = Bimestre::getDataInicial( $_REQUEST['cmbBimestre'], Sessao::getExercicio() );
        $stDtFinal  = Bimestre::getDataFinal( $_REQUEST['cmbBimestre'], Sessao::getExercicio() );
    break;

    case 'Quadrimestre':
        $preview->addParametro( 'periodo', $_REQUEST['cmbQuadrimestre'] ); 
        $preview->addParametro( 'tipo_periodo', "Quadrimestre" ); 
    break;
    
    case 'Semestre':
        $preview->addParametro( 'periodo', $_REQUEST['cmbSemestre'] ); 
        $preview->addParametro( 'tipo_periodo', "Semestre" ); 
    break;
}


/*
switch ($_REQUEST['cmbBimestre']) {
    case '1':
        $stDtInicio = '01/01/' . Sessao::getExercicio();
        if ( ( Sessao::getExercicio() % 4 ) == 0 ) {
            $stDtFinal = '29/02/' . Sessao::getExercicio();
        } else {
           $stDtFinal = '28/02/' . Sessao::getExercicio();
        }
    break;
    case '2':
        $stDtInicio = '01/03/' . Sessao::getExercicio();
        $stDtFinal = '30/04/' . Sessao::getExercicio();
    break;
    case '3':
        $stDtInicio = '01/05/' . Sessao::getExercicio();
        $stDtFinal = '30/06/' . Sessao::getExercicio();
    break;
    case '4':
        $stDtInicio = '01/07/' . Sessao::getExercicio();
        $stDtFinal = '31/08/' . Sessao::getExercicio();
    break;
    case '5':
        $stDtInicio = '01/09/' . Sessao::getExercicio();
        $stDtFinal = '31/10/' . Sessao::getExercicio();
    break;
    case '6':
        $stDtInicio = '01/10/' . Sessao::getExercicio();
        $stDtFinal = '31/12/' . Sessao::getExercicio();
    break;
}

if ($request->get('cmbMes')) {
    $stDtInicio = "01/".$request->get('cmbMes')."/".Sessao::getExercicio();
    $stDtFinal = SistemaLegado::retornaUltimoDiaMes($request->get('cmbMes'),Sessao::getExercicio());
}
*/

$obTAdministracaoUsuario = new TAdministracaoUsuario;

$stFiltro = " WHERE sw_cgm.numcgm = ".Sessao::read('numCgm');
$obTAdministracaoUsuario->recuperaRelacionamento($rsUsuario, $stFiltro);

$preview->addParametro( 'unidade_responsavel', $rsUsuario->getCampo('orgao') );

# Data de emissão no rodapé
$dtDataEmissao = date('d/m/Y');
$dtHoraEmissao = date('H:i');
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', $stDataEmissao );
$preview->addParametro( 'dt_inicio' , $stDtInicio );
$preview->addParametro( 'dt_final'  , $stDtFinal );
$preview->addAssinaturas(Sessao::read('assinaturas'));

$preview->preview();
