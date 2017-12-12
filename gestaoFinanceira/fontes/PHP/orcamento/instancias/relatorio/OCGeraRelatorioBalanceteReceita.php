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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 15/02/2005

    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    * $Id: OCGeraRelatorioBalanceteReceita.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.21
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";

$obRRelatorio = new RRelatorio;
$obPDF = new ListaPDF("L");
$arFiltro = Sessao::read('filtroRelatorio');

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

//$obRRelatorio->setExercicio      ( Sessao::getExercicio()                               );
$obRRelatorio->recuperaCabecalho ( $arConfiguracao                                              );
$obPDF->setModulo                ( "Orçamento Geral - ".Sessao::getExercicio()                      );
$obPDF->setTitulo                ( "Balancete da Receita"  );

$dtPeriodo="";
if (isset($arFiltro['stDataInicial'])) {
    $dtPeriodo = $arFiltro['stDataInicial'];
}
if (isset($arFiltro['stDataFinal'])) {
    $dtPeriodo = $dtPeriodo." a ".$arFiltro['stDataFinal'];
}
if (isset($arFiltro['relatorio'])) {
    $dtPeriodo = $dtPeriodo."  ".$arFiltro['relatorio'];
}
$obPDF->setSubTitulo             ( $dtPeriodo  );

$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsBalanceteReceita = Sessao::read('rsBalanceteReceita');
$obPDF->addRecordSet( $rsBalanceteReceita );

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("RECEITA", 12, 10);

    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php"        );
    $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
    $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
    $obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
    $obTOrcamentoConfiguracao->consultar();
    if($obTOrcamentoConfiguracao->getDado("valor") == 'true') // Recurso com Destinação de Recurso || 2008 em diante
        $obPDF->addCabecalho("DEST. RECURSO", 9, 10);
    else $obPDF->addCabecalho("CÓD. RECURSO", 9, 10);
$obPDF->addCabecalho("CÓD. REDUZIDO", 9, 10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("DESCRIÇÃO", 30, 10);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("VALOR PREVISTO",9, 10);
$obPDF->addCabecalho("ARRECADADO NO PERÍODO",10, 10);
$obPDF->addCabecalho("ARRECADADO NO ANO", 10, 10);
$obPDF->addCabecalho("DIFERENÇA", 9, 10);
$obPDF->addQuebraLinha("nivel",2,5);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("cod_estrutural", 8 );
$obPDF->addCampo("recurso", 8 );
$obPDF->addCampo("receita", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("descricao", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("valor_previsto", 8 );
$obPDF->addCampo("arrecadado_periodo", 8 );
$obPDF->addCampo("arrecadado_ano", 8 );
$obPDF->addCampo("diferenca", 8 );

if ($arFiltro['radResumoRecurso'] == 'S') {
    $obPDF->addRecordSet(Sessao::read('rsResumoRecurso'));
    $obPDF->setQuebraPaginaLista(true);
    $obPDF->setAlinhamento("C");
    $obPDF->addCabecalho("", 20, 10);
    $obPDF->addCabecalho("", 5, 10);
    $obPDF->addCabecalho("", 5, 10);
    $obPDF->setAlinhamento("L");
    $obPDF->addCabecalho("", 30, 10);
    $obPDF->setAlinhamento("C");
    $obPDF->addCabecalho("VALOR PREVISTO",9, 10);
    $obPDF->addCabecalho("ARRECADADO NO PERÍODO",10, 10);
    $obPDF->addCabecalho("ARRECADADO NO ANO", 10, 10);
    $obPDF->addCabecalho("DIFERENÇA", 9, 10);
    $obPDF->addQuebraLinha("nivel",2,5);

    $obPDF->setAlinhamento("C");
    $obPDF->addCampo("cod_estrutural", 8);
    $obPDF->addCampo("recurso", 8);
    $obPDF->addCampo("receita", 8);
    $obPDF->setAlinhamento("L");
    $obPDF->addCampo("descricao", 8);
    $obPDF->setAlinhamento("R");
    $obPDF->addCampo("valor_previsto", 8);
    $obPDF->addCampo("arrecadado_periodo", 8);
    $obPDF->addCampo("arrecadado_ano", 8);
    $obPDF->addCampo("diferenca", 8);
}

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
    //$obRAssinaturas->montaPDF( $obPDF );
}

$obPDF->show();
//$obPDF->montaPDF();
//$obPDF->OutPut();
?>
