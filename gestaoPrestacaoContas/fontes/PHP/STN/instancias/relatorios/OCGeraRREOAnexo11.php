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
    * Página de Relatório RREO Anexo11
    * Data de Criação   : 04/06/2008

    * @author Leopoldo Braga Barreiro

    * @ignore

    * Casos de uso :

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"                                    );

$obErro = new Erro();

if (!$request->get("cmbMes") && !$request->get("cmbBimestre")) {
    $stTipoRelatorio = $request->get("stTipoRelatorio") == "Mes" ? "Mês" : $request->get("stTipoRelatorio");
    $obErro->setDescricao('É preciso selecionar ao menos um '.$stTipoRelatorio.'.');
}

if ( $request->get("stAcao") == "anexo9novo" && $request->get("stTipoRelatorio") == "Mes" ){
    $preview = new PreviewBirt(6,36,62);    
} elseif ($request->get("stAcao") == "anexo9novo") {
    $preview = new PreviewBirt(6,36,43);
} else {
    $preview = new PreviewBirt(6,36,29);
}

$preview->setTitulo('Demonstrativo das Receitas Operações de Crédito e Despesas Capital');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel ( true );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$preview->addParametro ( 'exercicio', Sessao::getExercicio() );
$preview->addParametro ( 'entidade', implode(',', $_REQUEST['inCodEntidade'] ) );
$preview->addParametro ( 'exercicio_anterior', (Sessao::getExercicio() - 1));

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
    case 'Mes':
        $preview->addParametro( 'periodo', $_REQUEST['cmbMes']          );
        $stMesExtenso = sistemaLegado::mesExtensoBR(intval($_REQUEST['cmbMes']))." de ".Sessao::getExercicio();
        $preview->addParametro( 'mes_extenso'  , $stMesExtenso  );
        $preview->addParametro( 'peridiocidade', "mes"          );
    break;
    case 'Bimestre'    :
        $preview->addParametro( 'periodo'      , $_REQUEST['cmbBimestre'] );
        $preview->addParametro( 'peridiocidade', "bimestre"               );
    break;
    case 'Quadrimestre':
        $preview->addParametro( 'periodo'      , $_REQUEST['cmbQuadrimestre'] );
        $preview->addParametro( 'peridiocidade', "quadrimestre"               );
    break;
    case 'Semestre'    :
        $preview->addParametro( 'periodo'      , $_REQUEST['cmbSemestre'] );
        $preview->addParametro( 'peridiocidade', "semestre"               );
    break;
}

$stDtInicio = '';
$stDtFinal = '';

if(isset($_REQUEST['cmbBimestre'])){

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
    
} elseif (isset($_REQUEST['cmbMes'])) {
    
    $stDtInicio  = "01/".$_REQUEST['cmbMes']."/".Sessao::getExercicio();
    $stDtFinal   = sistemaLegado::retornaUltimoDiaMes($_REQUEST['cmbMes'], Sessao::getExercicio());
        
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

if ($_REQUEST['stAcao'] == 'anexo9novo') {
    $preview->addParametro( 'relatorio_novo', 'sim' );
} else {
    $preview->addParametro( 'relatorio_novo', 'nao' );
}
#################################################################################################

if ($request->get("stTipoRelatorio") == "Mes") {
    $preview->addParametro( 'mes'     , $_REQUEST['cmbMes'] );
}
$preview->addParametro( 'bimestre', $_REQUEST['cmbBimestre'] );

$preview->addParametro( 'dt_inicio', $stDtInicio );
$preview->addParametro( 'dt_final', $stDtFinal );
$preview->addAssinaturas(Sessao::read('assinaturas'));

if ( !$obErro->ocorreu() ) {
    $preview->preview();
} else {
    SistemaLegado::alertaAviso("FLModelosRREO.php?'.Sessao::getId().&stAcao=".$_REQUEST['stAcao'], $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");
}

?>