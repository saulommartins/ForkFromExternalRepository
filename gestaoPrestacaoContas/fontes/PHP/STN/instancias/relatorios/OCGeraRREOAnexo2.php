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
    * PÃ¡gina de RelatÃ³rio RREO Anexo1
    * Data de CriaÃ§Ã£o   : 14/11/2007

    * @author Tonismar RÃ©gis Bernardo

    * @ignore

    * Casos de uso : uc-06.01.02

    $Id: OCGeraRREOAnexo2.php 66000 2016-07-06 16:17:55Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"                                      );

if (Sessao::getExercicio() >= '2015') {
    //RREOAnexo2_2015.rptdesign
    $preview = new PreviewBirt(6,36,66);
}else{
    //RREOAnexo2.rptdesign
    $preview = new PreviewBirt(6,36,21);
}

$preview->setTitulo('Demonstrativo da Execução das Despesas por Função/Subfunção');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel( true );

$stNomeArquivo = "RREO_anexo2_";

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$preview->addParametro('exercicio'   , Sessao::getExercicio() );
$preview->addParametro('cod_entidade', implode(',', $_REQUEST['inCodEntidade']) );
$preview->addParametro('tipo_periodo', $_REQUEST['stTipoRelatorio'] );

switch ($_REQUEST['stTipoRelatorio']) {
    case 'Mes':
        $preview->addParametro( 'titulo_periodo',  SistemaLegado::mesExtensoBR($_REQUEST['cmbMes']).' de '.Sessao::getExercicio());
        $preview->addParametro( 'periodo'  , $_REQUEST['cmbMes'] );
        $preview->addParametro( 'dt_inicial', '01/'.$_REQUEST['cmbMes'].'/'.Sessao::getExercicio() );
        $preview->addParametro( 'dt_final', SistemaLegado::retornaUltimoDiaMes($_REQUEST['cmbMes'],Sessao::getExercicio()));
        $stNomeArquivo .= $_REQUEST['cmbMes'] . "mes";
    break;
    case 'Bimestre':
        $preview->addParametro( 'titulo_periodo',  $_REQUEST['cmbBimestre'].'° bimestre de '.Sessao::getExercicio());
        $preview->addParametro( 'periodo', $_REQUEST['cmbBimestre'] );
        SistemaLegado::periodoInicialFinalBimestre($stDtInicial, $stDtFinal, $_REQUEST['cmbBimestre'], Sessao::getExercicio());
        
        $preview->addParametro( 'periodo', $_REQUEST['cmbBimestre'] );
        $preview->addParametro( 'dt_inicial', $stDtInicial);
        $preview->addParametro( 'dt_final', $stDtFinal);
        $stNomeArquivo .= $_REQUEST['cmbBimestre'] . "bimestre";
    break;
    case 'Quadrimestre':
        $preview->addParametro( 'periodo', $_REQUEST['cmbQuadrimestre'] );
        $stNomeArquivo .= $_REQUEST['cmbQuadrimestre'] . "quadrimestre";
    break;
    case 'Semestre':
        $preview->addParametro( 'periodo', $_REQUEST['cmbSemestre'] );
        $stNomeArquivo .= $_REQUEST['cmbSemestre'] . "semestre";
    break;
}

while ( !$rsEntidade->eof() ) {
    if ( preg_match( "/prefeitura/i", $rsEntidade->getCampo('nom_cgm')) || preg_match( "/tribunal/i", $rsEntidade->getCampo('nom_cgm'))) {
        $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm'));
        break;
    }
    $rsEntidade->proximo();
}

if ( preg_match( "/prefeitura/i", $rsEntidade->getCampo( 'nom_cgm' ) ) || ( count($_REQUEST['inCodEntidade']) > 1 ) ) {
    $preview->addParametro( 'poder' , 'Executivo' );
} else {
    if (preg_match("/c(â|a)mara/i", $rsEntidade->getCampo( 'nom_cgm' )) and count($_REQUEST['inCodEntidade']) == 1) {
       $preview->addParametro( 'poder' , 'Legislativo' );
    } else {
       $preview->addParametro( 'poder' , 'Executivo' );
    }
}

//adiciona unidade responsável ao relatório
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php"                                   );
$stFiltro = " WHERE sw_cgm.numcgm = ".Sessao::read('numCgm');
$obTAdministracaoUsuario = new TAdministracaoUsuario;
$obTAdministracaoUsuario->recuperaRelacionamento($rsUsuario, $stFiltro);

$preview->addParametro( 'unidade_responsavel', $rsUsuario->getCampo('orgao') );

//adicionada data de emissão no rodapé do relatório
$dtDataEmissao = date('d/m/Y');
$dtHoraEmissao = date('H:i');
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', $stDataEmissao );

if ($_REQUEST['stAcao'] == 'anexo2novo') {
    $preview->addParametro( 'relatorio_novo', 'sim' );
} else {
    $preview->addParametro( 'relatorio_novo', 'nao' );
}

$preview->setNomeArquivo($stNomeArquivo);
$preview->setNomeRelatorio($stNomeArquivo);
$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
