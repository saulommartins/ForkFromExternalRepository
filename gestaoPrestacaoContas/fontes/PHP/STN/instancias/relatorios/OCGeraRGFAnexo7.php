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
    * Página de Relatório RGF Anexo7
    * Data de Criação   : 08/10/2007

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: OCGeraRGFAnexo7.php 66512 2016-09-08 21:12:48Z michel $

    * Casos de uso : uc-06.01.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";

$obErro = new Erro();

#RGFAnexo7.rptdesign
$preview = new PreviewBirt(6, 36, 7);

$stAcao = $request->get('stAcao');
$obTOrcamentoEntidade = new TOrcamentoEntidade();
//Buscando todas as entidades
$obTOrcamentoEntidade->setDado( 'exercicio'   , $request->get('stExercicio') );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$request->get('inCodEntidade')).")" );
//Buscando dados da entidade Câmara
$inCodEntidadeCamara = SistemaLegado::pegaConfiguracao("cod_entidade_camara",8,Sessao::getExercicio());
if (!empty($inCodEntidadeCamara)) {
    $obTOrcamentoEntidade->recuperaEntidades( $rsEntidadeCamara, "and e.cod_entidade = ".$inCodEntidadeCamara );
}
//Quantidade de entidade selecionadas no filtro
$inCountEntidades = count($request->get('inCodEntidade'));
//A entidade Câmara deve ser selecionada separadamente das demais
if ( $inCountEntidades > 1 ) {
    if (in_array($inCodEntidadeCamara, $request->get('inCodEntidade'))) {
        $obErro->setDescricao('Entidade '.$rsEntidadeCamara->getCampo('nom_cgm').' deve ser selecionada sozinha das demais entidades.');
        SistemaLegado::alertaAviso("FLModelosRGF.php?&stAcao=".$stAcao, $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");        
    }
}

if (Sessao::getExercicio() < 2013) {
    $preview->setTitulo('Demonstrativo dos Limites');
} else {
    $preview->setTitulo('Demonstrativo Simplificado do Relatório de Gestão Fiscal');
}
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel ( true );

if ( count($request->get('inCodEntidade')) == 1 ) {
    $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));
    $preview->addParametro('cod_entidade', $_POST['inCodEntidade'][0]  );

} else {
    foreach ($rsEntidade->arElementos as $key => $value) {
        if (preg_match("/prefeitura/i", $value['nom_cgm'])) {
            $preview->addParametro( 'nom_entidade', $value['nom_cgm']);
        }
    }
    $preview->addParametro( 'cod_entidade', implode(',', $request->get('inCodEntidade') ) );
}

$stDataInicial = "01/01/".$request->get('stExercicio');

$stAno = $request->get('stExercicio');

if ( $request->get('stTipoRelatorio') == 'Quadrimestre' || $request->get('stTipoRelatorio') == 'UltimoQuadrimestre' ) {
    switch ( $request->get('cmbQuadrimestre') ) {
        case 1:
            $stDataFinal = '30/04';
        break;
        case 2:
            $stDataFinal = '31/08';
        break;
        case 3:
            $stDataFinal = '31/12';
        break;
    }
    $nuPeriodo = $request->get('cmbQuadrimestre') ;
} elseif ( $request->get('stTipoRelatorio') == 'Semestre' || $request->get('stTipoRelatorio') == 'UltimoSemestre') {
    switch ( $request->get('cmbSemestre') ) {
        case 1:
            $stDataFinal = '30/06';
        break;
        case 2:
            $stDataFinal = '31/12';
        break;
    }
    $nuPeriodo = $request->get('cmbSemestre') ;
}

$stDataFinal = "$stDataFinal/" . $request->get('stExercicio');

$preview->addParametro( 'data_inicio', $stDataInicial );
$preview->addParametro( 'data_fim', $stDataFinal );
$preview->addParametro( 'exercicio', $request->get('stExercicio') );
$preview->addParametro( 'periodo', $nuPeriodo );
$preview->addParametro( 'tipo_periodo', $request->get('stTipoRelatorio') );

if ( preg_match( "/prefeitura/i", $rsEntidade->getCampo( 'nom_cgm' ) ) || ( count($request->get('inCodEntidade')) > 1 ) ) {
    $preview->addParametro( 'poder' , 'Poder Executivo' );
} elseif ( preg_match( "/c(â|a)mara/i", $rsEntidade->getCampo( 'nom_cgm' ) ) ) {
    $preview->addParametro( 'poder' , 'Poder Legislativo' );
}

#############################Modificações do tce para o novo layout##############################
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
#################################################################################################

$preview->addAssinaturas(Sessao::read('assinaturas'));

if( !$obErro->ocorreu() ){
    $preview->preview();
}else{
    SistemaLegado::alertaAviso("FLModelosRGF.php?&stAcao=".$stAcao, $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");
}
