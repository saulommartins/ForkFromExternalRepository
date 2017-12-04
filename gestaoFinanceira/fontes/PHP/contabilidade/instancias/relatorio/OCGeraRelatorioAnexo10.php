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
    * Pagina oculta para gerar relatorio
    * Data de Criação   : 06/10/2004

    * @author Desenvolvedor: Anderson Buzo
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    * $Id: OCGeraRelatorioAnexo10.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.07
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"            );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioAnexo10.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

// Monta cabecalho Principal
$obRRelatorio->recuperaCabecalho ( $arConfiguracao                                  );
$obPDF->setModulo                ( "Orçamento Geral - ".Sessao::getExercicio()           );
$obPDF->setAcao                  ( "Anexo 10 - Comparativo da Receita Orçada com a Arrecadada" );
$dtPeriodo = $arFiltro['stDataInicial']." à ".$arFiltro['stDataFinal'] ."  ".$arFiltro['relatorio'];
$obPDF->setSubTitulo             ( $dtPeriodo  );
$obPDF->setUsuario               ( Sessao::getUsername()                                );
$obPDF->setEnderecoPrefeitura    ( $arConfiguracao                                  );
$obPDF->addRecordSet( Sessao::read('rsAnexo10') );

// Monta cabecalho dos dados
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ("RECEITA"    ,12, 10 );
$obPDF->addCabecalho   ("DESCRIÇÃO"  ,48, 10 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho   ("ORÇADO"     ,10, 10 );
$obPDF->addCabecalho   ("ARRECADADO" ,10, 10 );
$obPDF->addCabecalho   ("PARA MAIS"  ,10, 10 );
$obPDF->addCabecalho   ("PARA MENOS" ,10, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ("receita" , 8 );
$obPDF->addCampo       ("descricao"     , 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ("vl_orcado"     , 8 );
$obPDF->addCampo       ("vl_arrecadado" , 8 );
$obPDF->addCampo       ("vl_mais"       , 8 );
$obPDF->addCampo       ("vl_menos"      , 8 );

$stDataInicial = implode('-',array_reverse(explode('/',$arFiltro['stDataInicial'])));
$stDataFinal = implode('-',array_reverse(explode('/',$arFiltro['stDataFinal'])));

include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeNotasExplicativas.class.php';
$obTContabilidadeNotaExplicativa = new TContabilidadeNotasExplicativas;
$obTContabilidadeNotaExplicativa->setDado('cod_acao', Sessao::read('acao'));
$obTContabilidadeNotaExplicativa->setDado('dt_inicial', $stDataInicial);
$obTContabilidadeNotaExplicativa->setDado('dt_final', $stDataFinal);
$obTContabilidadeNotaExplicativa->recuperaNotaExplicativaRelatorio($rsAnexo);

$arNota = explode("\n", $rsAnexo->getCampo('nota_explicativa'));
$inCount = 0;
foreach ($arNota as $arNotaTMP) {
    $arRecordSetNota[$inCount]['nota'] = $arNotaTMP;
    $inCount++;
}

if ($rsAnexo->getCampo('nota_explicativa')) {
    $rsNota = new RecordSet;
    $rsNota->preenche($arRecordSetNota);
    $obPDF->addRecordSet($rsNota);
    $obPDF->setQuebraPaginaLista(false);

    $obPDF->addCabecalho("", 1,  10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("NOTAS EXPLICATIVAS", 90, 10);
    $obPDF->addCabecalho("", 1,  10);
    $obPDF->addCabecalho("", 1, 10);
    $obPDF->addCabecalho("", 1,  10);
    $obPDF->addCabecalho("", 1, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("nota", 8 );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("", 8 );
}

$obPDF->show();
?>
