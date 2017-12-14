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
    * PÃ¡gina de Relatório RREO Anexo1
    * Data de Criação   : 14/11/2007

    * @author Tonismar Régis Bernardo

    * @ignore

    * Casos de uso : uc-06.01.01

    $Id: OCGeraRREOAnexo1.php 28481 2008-03-11 13:00:15Z tonismar $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"                                    );
include CAM_GPC_STN_MAPEAMENTO . 'TSTNRecursoRREOAnexo14.class.php';

$obTSTNRecursoRREOAnexo14 = new TSTNRecursoRREOAnexo14();
$obTSTNRecursoRREOAnexo14->setDado('exercicio',Sessao::getExercicio());
$stChave = $obTSTNRecursoRREOAnexo14->getComplementoChave();
$obTSTNRecursoRREOAnexo14->setComplementoChave('exercicio');
$obTSTNRecursoRREOAnexo14->exclusao();

$arValores = Sessao::read('arValores');
for ($i=0; $i< count($arValores); $i++ ) {
    $obTSTNRecursoRREOAnexo14->setDado('cod_recurso',$arValores[$i]['inCodRecurso']);
    $obTSTNRecursoRREOAnexo14->inclusao();
    if ($stcodRecurso != '') {
        $stcodRecurso = $stcodRecurso.",".$arValores[$i]["inCodRecurso"];
    } else {
        $stcodRecurso = $arValores[$i]["inCodRecurso"];
    }
}

if ($_POST['stAcao'] == "anexo11novo" && Sessao::getExercicio() < '2015') {
    $preview = new PreviewBirt(6,36,44);
} else if ($_POST['stAcao'] == "anexo11novo" && Sessao::getExercicio() >= '2015' ) {
    $preview = new PreviewBirt(6,36,65);
}else {
    $preview = new PreviewBirt(6,36,23);
}
$preview->setTitulo('Dem. Receita de Alienação Ativos e Aplicação Recursos');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel ( true );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->setDado( 'exercicio_anterior', Sessao::getExercicio()  -1 );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$preview->addParametro ( 'exercicio_anterior', (Sessao::getExercicio() - 1)   );

$stDataInicialAnterior = '01/01/'.(Sessao::getExercicio() - 1);
$preview->addParametro ( 'data_inicial_anterior', $stDataInicialAnterior );

$stDataFinalAnterior = '31/12/'.(Sessao::getExercicio() - 1);
$preview->addParametro ( 'data_final_anterior', $stDataFinalAnterior );
$preview->addParametro ( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade'] ) );
$preview->addParametro ( 'cod_recurso', $stcodRecurso );

if ( count($_REQUEST['inCodEntidade']) == 1 ) {
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
} else {
    $rsEntidade->setPrimeiroElemento();

    while ( !$rsEntidade->eof() ) {
        if (preg_match("/prefeitura/i", $rsEntidade->getCampo( 'nom_cgm' ))) {
            $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm'));
            break;
        }
        $rsEntidade->proximo();
    }
}

$preview->addParametro( 'tipo_periodo', $_REQUEST['stTipoRelatorio'] );

if ( preg_match( "/c[âa]mara/i", $rsEntidade->getCampo( 'nom_cgm' ) ) && count($_REQUEST['inCodEntidade']) == 1 ) {
    $preview->addParametro( 'poder' , 'Legislativo' );
} else {
    $preview->addParametro( 'poder' , 'Executivo' );
}

switch ($_REQUEST['stTipoRelatorio']) {
    case 'Bimestre'    :$preview->addParametro( 'periodo', $_REQUEST['cmbBimestre'    ] ); break;
    case 'Quadrimestre':$preview->addParametro( 'periodo', $_REQUEST['cmbQuadrimestre'] ); break;
    case 'Semestre'    :$preview->addParametro( 'periodo', $_REQUEST['cmbSemestre'    ] ); break;
}

switch ($_REQUEST['cmbBimestre'    ]) {
    case '1':
         $preview->addParametro( 'data_inicial_periodo', '01/01/'.Sessao::getExercicio() );
        if ( ( Sessao::getExercicio() % 4 ) == 0 ) {
           $preview->addParametro( 'data_final_periodo', '29/02/'.Sessao::getExercicio() );
        } else {
           $preview->addParametro( 'data_final_periodo', '28/02/'.Sessao::getExercicio() );
        }
    break;
    case '2':
        $preview->addParametro( 'data_inicial_periodo', '01/03/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_final_periodo', '30/04/'.Sessao::getExercicio() );
    break;
    case '3':
        $preview->addParametro( 'data_inicial_periodo', '01/05/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_final_periodo', '30/06/'.Sessao::getExercicio() );
    break;
    case '4':
        $preview->addParametro( 'data_inicial_periodo', '01/07/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_final_periodo', '31/08/'.Sessao::getExercicio() );
    break;
    case '5':
        $preview->addParametro( 'data_inicial_periodo', '01/09/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_final_periodo', '31/10/'.Sessao::getExercicio() );
    break;
    case '6':
        $preview->addParametro( 'data_inicial_periodo', '01/11/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_final_periodo', '31/12/'.Sessao::getExercicio() );
    break;
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

if ($_REQUEST['stAcao'] == 'anexo11novo') {
    $preview->addParametro( 'relatorio_novo', 'sim' );
} else {
    $preview->addParametro( 'relatorio_novo', 'nao' );
}
#################################################################################################

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
