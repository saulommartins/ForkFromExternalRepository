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

 $Id: OCGeraRREOAnexo18.php 66350 2016-08-15 18:40:42Z carlos.silva $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";

if ($_REQUEST['stAcao'] == 'anexo14novo') {
    $preview = new PreviewBirt(6,36,34);
}else{    
    $preview = new PreviewBirt(6,36,58);
}

$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo('Demonstrativo Simplificado do Relatório Resumido da Execução Orçamentária');
$preview->setExportaExcel( true );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$preview->addParametro ( 'porcentagem', $request->get('flPct') );
$preview->addParametro ( 'cod_entidade', implode(',', $request->get('inCodEntidade')) );

if (count($request->get('inCodEntidade')) == 1 ) {
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
} else {
    $inCodEntidadePrefeitura = SistemaLegado::pegaDado('valor','administracao.configuracao'," WHERE parametro = 'cod_entidade_prefeitura' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 8 ");
    $obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade = ".$inCodEntidadePrefeitura );

    while ( !$rsEntidade->eof() ) {
        $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
        $rsEntidade->proximo();
    }
}

$preview->addParametro( 'entidade_cabecalho', $rsEntidade->getCampo('nom_cgm') );

$inCodEntidadeRPPS = SistemaLegado::pegaDado('valor','administracao.configuracao',"WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 8 AND parametro = 'cod_entidade_rpps'");

if (in_array($inCodEntidadeRPPS, $_REQUEST['inCodEntidade'])) {
    $preview->addParametro('cod_entidade_rpps', $inCodEntidadeRPPS );
} else {
    $preview->addParametro('cod_entidade_rpps', '0' );
}

if (preg_match( "/prefeitura/i", $rsEntidade->getCampo( 'nom_cgm' ) ) || ( count($_REQUEST['inCodEntidade']) > 1 ) ) {
    $preview->addParametro( 'poder' , 'Executivo' );
} else {
    if (preg_match("/c(â|a)mara/i", $rsEntidade->getCampo( 'nom_cgm' )) and count($_REQUEST['inCodEntidade']) == 1) {
       $preview->addParametro( 'poder' , 'Legislativo' );
    } else {
       $preview->addParametro( 'poder' , 'Executivo' );
    }
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
//necessário codificar os caracteres especias em ascii para o birt interpretar corretamente
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', $stDataEmissao);

if ($_REQUEST['stAcao'] == 'anexo14novo') {
    $preview->addParametro( 'relatorio_novo', 'sim' );
} else {
    $preview->addParametro( 'relatorio_novo', 'nao' );
}
#################################################################################################

SistemaLegado::periodoInicialFinalBimestre($dtDataInicio, $dtDataFim, $_REQUEST['cmbBimestre'], Sessao::getExercicio());

$preview->addParametro( 'data_inicio', $dtDataInicio );
$preview->addParametro( 'data_fim', $dtDataFim );
$preview->addParametro( 'periodo', $_REQUEST['cmbBimestre'] );
$preview->addParametro( 'peridiocidade', "bimestre" );
$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();

?>