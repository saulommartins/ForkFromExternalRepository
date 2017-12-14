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
    * PÃ¡gina de RelatÃ³rio RREO Anexo3
    * Data de CriaÃ§Ã£o   : 14/11/2007

    * @author Tonismar RÃ©gis Bernardo

    * @ignore

    * Casos de uso : uc-06.01.03

    $Id: OCGeraRREOAnexo3.php 64455 2016-02-24 16:59:33Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
// gestaoPrestacaoContas/fontes/RPT/STN/report/design/RREOAnexo3.rptdesign
$preview = new PreviewBirt(6,36,22);
$preview->setTitulo('Demonstrativo da Receita Corrente Liquída');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel (true );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$preview->addParametro( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade'] ) );
if ( count($_REQUEST['inCodEntidade']) == 1 ) {
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
} else {
    while ( !$rsEntidade->eof() ) {
        if ( preg_match( "/prefeitura/i", $rsEntidade->getCampo('nom_cgm')) ) {
            $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
            break;
        }
        $rsEntidade->proximo();
    }
}

if ( preg_match( "/C[âa]mara/i", $rsEntidade->getCampo( 'nom_cgm' ) ) ) {
    $preview->addParametro( 'poder' , 'Legislativo' );
} else {
    $preview->addParametro( 'poder' , 'Executivo' );
}

$preview->addParametro( 'tipo_periodo', $_REQUEST['stTipoRelatorio'] );
$preview->addParametro( 'exercicio_anterior', Sessao::getExercicio() -1 );
$preview->addParametro( 'dtInicioBimestre'  , '01/03/2008' );
$preview->addParametro( 'dtFimBimestre'     , '30/04/2008' );

$preview->addParametro( 'dtInicioBimestreAnterior'  , '01/01/2008' );
$preview->addParametro( 'dtFimBimestreAnterior'     , '28/02/2008' );

    if ($_REQUEST['stTipoRelatorio'] == 'Bimestre') {
        switch( $_REQUEST['cmbBimestre'] ):
        case 1:
            if ( (Sessao::getExercicio() % 4) == 0 ) {
                $preview->addParametro( 'dt_final_periodo', '29/02/'.Sessao::getExercicio() );
            } else {
                $preview->addParametro( 'dt_final_periodo', '28/02/'.Sessao::getExercicio() );
            }
            $preview->addParametro( 'mes', 2 );
            break;
        case 2:
            $preview->addParametro( 'dt_final_periodo', '30/04/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 4 );
            break;
        case 3:
            $preview->addParametro( 'dt_final_periodo', '30/06/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 6 );
            break;
        case 4:
            $preview->addParametro( 'dt_final_periodo', '31/08/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 8 );
            break;
        case 5:
            $preview->addParametro( 'dt_final_periodo', '31/10/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 10 );
            break;
        case 6:
            $preview->addParametro( 'dt_final_periodo', '31/12/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 12 );
            break;
        endswitch;
        $preview->addParametro( 'periodo', $_REQUEST['cmbBimestre'] );
    } elseif ($_REQUEST['stTipoRelatorio'] == 'Quadrimestre') {
        switch( $_REQUEST['cmbQuadrimestre']):
        case 1:
            $preview->addParametro( 'dt_final_periodo', '30/04/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 4 );
            break;
        case 2:
            $preview->addParametro( 'dt_final_periodo', '31/08/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 8 );
            break;
        case 3:
            $preview->addParametro( 'dt_final_periodo', '31/12/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 12 );
            break;
        endswitch;
        $preview->addParametro( 'periodo', $_REQUEST['cmbQuadrimestre'] );
    } elseif ($_REQUEST['stTipoRelatorio'] == 'Semestre') {
        switch( $_REQUEST['cmbSemestre']):
        case 1:
            $preview->addParametro( 'dt_final_periodo', '30/06/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 6 );
            break;
        case 2:
            $preview->addParametro( 'dt_final_periodo', '31/12/'.Sessao::getExercicio() );
            $preview->addParametro( 'mes', 12 );
            break;
        endswitch;
        $preview->addParametro( 'periodo', $_REQUEST['cmbSemestre'] );
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

if ($_REQUEST['stAcao'] == 'anexo3novo') {
    $preview->addParametro( 'relatorio_novo', 'sim' );
} else {
    $preview->addParametro( 'relatorio_novo', 'nao' );
}
#################################################################################################

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
