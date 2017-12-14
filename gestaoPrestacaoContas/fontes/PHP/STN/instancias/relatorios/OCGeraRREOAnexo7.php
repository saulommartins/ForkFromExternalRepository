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
    * Data de CriaÃ§Ã£o   : 05/05/2008

    * @author Leopoldo Braga Barreiro

    * @ignore

    * Casos de uso :

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"                                    );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

if ($_REQUEST['stAcao'] == 'anexo6novo') {
    if (Sessao::getExercicio() >= '2015') {
        //gestaoPrestacaoContas/fontes/RPT/STN/report/design/RREOAnexo6_2015.rptdesign
        $preview = new PreviewBirt(6,36,64);
    }else{
        $preview = new PreviewBirt(6,36,25);
    }
} else {
    $preview = new PreviewBirt(6,36,56);
}
$preview->setTitulo('Demonstrativo do Resultado Primário');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel ( true );

/**
 * Faz a inserção/update do parametro meta_resultado_nominal_fixada na table administracao.configuracao
 */
$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
$obTAdministracaoConfiguracao->setDado('exercicio',Sessao::getExercicio());
$obTAdministracaoConfiguracao->setDado('cod_modulo',36);
$obTAdministracaoConfiguracao->setDado('parametro','meta_resultado_primario_fixada');
$obTAdministracaoConfiguracao->setDado('valor',$_REQUEST['inVlMetaFixada']);

$obTAdministracaoConfiguracao->recuperaPorChave($rsAdministracaoConfiguracao);

if ( $rsAdministracaoConfiguracao->getNumLinhas() > 0 ) {
  $obTAdministracaoConfiguracao->alteracao();
} else {
  $obTAdministracaoConfiguracao->inclusao();
}

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );
asort($_REQUEST['inCodEntidade']);

$preview->addParametro ( 'exercicio', Sessao::getExercicio() );
$preview->addParametro ( 'entidade', implode(',', $_REQUEST['inCodEntidade'] ) );

if ( count($_REQUEST['inCodEntidade']) == 1 ) {
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
} else {
    $obTAdministracaoConfiguracao->recuperaTodos($rsPrefeitura, " WHERE exercicio = '".Sessao::getExercicio()."' AND parametro = 'cod_entidade_prefeitura'");
    foreach ($rsEntidade->arElementos as $key => $field) {
      if ($rsPrefeitura->getCampo('valor') == $field['cod_entidade']) {
    $preview->addParametro( 'nom_entidade', $field['nom_cgm'] );
      }
    }
}

$preview->addParametro( 'tipo_periodo', $_REQUEST['stTipoRelatorio'] );

if ( preg_match( "/prefeitura/i", $rsEntidade->getCampo( 'nom_cgm' ) ) || ( count($_REQUEST['inCodEntidade']) > 1 ) ) {
    $preview->addParametro( 'poder' , 'Executivo' );
} else {
    if (preg_match("/c(â|a)mara/i", $rsEntidade->getCampo( 'nom_cgm' )) and count($_REQUEST['inCodEntidade']) == 1) {
       $preview->addParametro( 'poder' , 'Legislativo' );
    } else {
       $preview->addParametro( 'poder' , 'Executivo' );
    }
}

switch ($_REQUEST['stTipoRelatorio']) {
    case 'Bimestre'    :$preview->addParametro( 'periodo', $_REQUEST['cmbBimestre'    ] ); break;
    case 'Quadrimestre':$preview->addParametro( 'periodo', $_REQUEST['cmbQuadrimestre'] ); break;
    case 'Semestre'    :$preview->addParametro( 'periodo', $_REQUEST['cmbSemestre'    ] ); break;
}

$stDtInicio = '';
$stDtFinal = '';

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
        $stDtInicio = '01/11/' . Sessao::getExercicio();
        $stDtFinal = '31/12/' . Sessao::getExercicio();
    break;
}

($request->get('cmbBimestre')) ? $periodicidade = 'bimestre' : $periodicidade = 'mes';

if ($request->get('cmbBimestre')) {
    $descricaoPeriodo = $request->get('cmbBimestre')."º Bimestre de ".Sessao::getExercicio();
} else if ($request->get('cmbMes')) {
    $descricaoPeriodo = sistemaLegado::mesExtensoBR(intval($_REQUEST['cmbMes']))." de ".Sessao::getExercicio();
}

if ($request->get('cmbMes')) {
    $stDtInicio = "01/".$request->get('cmbMes')."/".Sessao::getExercicio();
    $stDtFinal = SistemaLegado::retornaUltimoDiaMes($request->get('cmbMes'),Sessao::getExercicio());
}


#############################Modificações do tce para o novo layout##############################
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

if ($_REQUEST['stAcao'] == 'anexo6novo') {
    $preview->addParametro( 'relatorio_novo', 'sim' );
} else {
    $preview->addParametro( 'relatorio_novo', 'nao' );
}
#################################################################################################
$preview->addParametro( 'periodicidade', $periodicidade );
$preview->addParametro( 'dt_inicio', $stDtInicio );
$preview->addParametro( 'dt_final', $stDtFinal );
$preview->addParametro( 'descricao', $descricaoPeriodo );

$preview->addAssinaturas(Sessao::read('assinaturas'));

$preview->preview();
