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

    $Id: OCGeraRREOAnexo18.php 35993 2008-11-26 13:15:21Z hboaventura $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
if ($_REQUEST['stAcao'] == 'anexo10novo') {
    $preview = new PreviewBirt(6,36,55);
    $preview->setVersaoBirt( '2.5.0' );
    $preview->setTitulo('Dem. Projeção Atuarial do RPPS');
    $preview->setExportaExcel( true );
    $preview->addParametro( 'relatorio_novo', 'sim' );
} elseif ($_REQUEST['stAcao'] == 'anexo13') {
    $preview = new PreviewBirt(6,36,55);
    $preview->setVersaoBirt( '2.5.0' );
    $preview->setTitulo('Dem. Projeção Atuarial do RPPS');
    $preview->setExportaExcel( true );
    $preview->addParametro( 'relatorio_novo', 'nao' );
} else {
    $preview = new PreviewBirt(6,36,35);
    $preview->setVersaoBirt( '2.5.0' );
    $preview->setTitulo('Dem. Projeção Atuarial do RPPS');
    $preview->setExportaExcel( true );
    $preview->addParametro( 'relatorio_novo', 'nao' );
}

$inCodEntidadeRPPS = SistemaLegado::pegaDado('valor','administracao.configuracao',"WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 8 AND parametro = 'cod_entidade_rpps'");

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade = " . $inCodEntidadeRPPS . " ");

$preview->addParametro ( 'cod_entidade', $inCodEntidadeRPPS );

$preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
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
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', $stDataEmissao );
#################################################################################################

$preview->preview();