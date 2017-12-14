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
    * Página de Relatório RREO Anexo 16.
    * Data de Criação: 20/05/2008

    * @author Henrique Boaventura

    * Casos de uso: uc-06.01.15

    $Id: OCGeraRREOAnexo16.php 61605 2015-02-12 16:04:02Z diogo.zarpelon $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

if ($_REQUEST['stAcao'] == 'anexo12novo') {
    switch ($_REQUEST['stTipoRelatorio']) {
        case 'Mes':
            if (Sessao::getExercicio()>'2014') {
                $preview = new PreviewBirt(6,36,47);   
            }
            else if ($_REQUEST['cmbMes'] != 12) {
                $preview = new PreviewBirt(6,36,47);
            } else {
                $preview = new PreviewBirt(6,36,48);
            }
        break;
        case 'Bimestre':
            if (Sessao::getExercicio()>'2014') {
                $preview = new PreviewBirt(6,36,47);   
            }
            else if ( $_REQUEST['cmbBimestre'] != 6) {
                $preview = new PreviewBirt(6,36,47);
            } else {
                $preview = new PreviewBirt(6,36,48);
            }
        break;
    }
} else {
    $preview = new PreviewBirt(6,36,32);
}

$preview->setTitulo('Dem da Receita Líquida de Impostos e das Despesas Proprias com ASPS');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel( true );

/**
 * Faz o update do parametro meta_resultado_nominal_fixada na table administracao.configuracao
 */
$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
$obTAdministracaoConfiguracao->setDado('exercicio',Sessao::getExercicio());
$obTAdministracaoConfiguracao->setDado('cod_modulo',36);
$obTAdministracaoConfiguracao->setDado('parametro','stn_anexo16_porcentagem');
$obTAdministracaoConfiguracao->setDado('valor',$_REQUEST['flPct']);
$obTAdministracaoConfiguracao->alteracao();

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$preview->addParametro ( 'porcentagem', $_REQUEST['flPct'] );
$preview->addParametro ( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade'] ) );
$preview->addParametro ( 'cod_recurso', substr($stCodRecurso,0,-1) );

if ( count($_REQUEST['inCodEntidade']) == 1 ) {
     $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
} else {
    $inCodEntidadePrefeitura = SistemaLegado::pegaDado('valor','administracao.configuracao'," WHERE parametro = 'cod_entidade_prefeitura' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 8 ");
    $obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade = ".$inCodEntidadePrefeitura );
    while ( !$rsEntidade->eof() ) {
     if ( $rsEntidade->getCampo('cod_entidade') == $inCodEntidadePrefeitura ) {
        $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
     }
      $rsEntidade->proximo();
    }
}

//$preview->addParametro( 'tipo_periodo', $_REQUEST['stTipoRelatorio'] );

if ( preg_match( "/c[âa]mara/i", $rsEntidade->getCampo( 'nom_cgm' ) ) ) {
    $preview->addParametro( 'poder' , 'Legislativo' );
} else {
    $preview->addParametro( 'poder' , 'Executivo' );
}
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
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', $stDataEmissao );

if ($_REQUEST['stAcao'] == 'anexo12novo') {
    switch ($_REQUEST['stTipoRelatorio']) {
        case 'Mes':
            $preview->addParametro( 'descricaoPeriodo', SistemaLegado::mesExtensoBR((intval($_REQUEST['cmbMes'])))." de ". Sessao::getExercicio());
            $preview->addParametro( 'periodo_extenso','Mês');
            $preview->addParametro( 'periodo'  , $_REQUEST['cmbMes'] );
            $preview->addParametro( 'data_inicial', '01/'.$_REQUEST['cmbMes'].'/'.Sessao::getExercicio() );
            $preview->addParametro( 'data_final', SistemaLegado::retornaUltimoDiaMes($_REQUEST['cmbMes'],Sessao::getExercicio()));
        break;
        case 'Bimestre':
            SistemaLegado::periodoInicialFinalBimestre($stDataInicial,$stDataFinal,$_REQUEST['cmbBimestre'],Sessao::getExercicio());
            $preview->addParametro( 'descricaoPeriodo', $_REQUEST['cmbBimestre']."º Bimestre de ".Sessao::getExercicio() );
            $preview->addParametro( 'periodo_extenso', 'Bimestre');
            $preview->addParametro( 'periodo', $_REQUEST['cmbBimestre'] );
            $preview->addParametro( 'data_inicial', '01/01/'.Sessao::getExercicio() );
            $preview->addParametro( 'data_final'  , $stDataFinal );
        break;
    }
    $preview->addParametro( 'relatorio_novo', 'sim' );

} else {
    $preview->addParametro( 'periodo', $_REQUEST['cmbBimestre'] );
    $preview->addParametro( 'relatorio_novo', 'nao' );
}
#################################################################################################
$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
