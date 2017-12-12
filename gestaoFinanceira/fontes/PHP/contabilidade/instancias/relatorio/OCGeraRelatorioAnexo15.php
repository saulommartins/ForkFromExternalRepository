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
    * Data de Criação   : 03/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: OCGeraRelatorioAnexo15.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.12

*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once CAM_FW_PDF.'RRelatorio.class.php';

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF('L');

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');
if (count($arFiltro['inCodEntidade']) == 1) {
    $obRRelatorio->setCodigoEntidade($arFiltro['inCodEntidade'][0]);
    $obRRelatorio->setExercicioEntidade(Sessao::getExercicio());
}

$obRRelatorio->setExercicio  (Sessao::getExercicio());
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ('Contabilidade');

$inCodEntidadeRPPS = SistemaLegado::pegaDado('valor', 'administracao.configuracao',
"WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 8 AND parametro = 'cod_entidade_rpps'");

if (COUNT($arFiltro['inCodEntidade']) == 1 && in_array($inCodEntidadeRPPS, $arFiltro['inCodEntidade'])) {
    $obPDF->setAcao('Anexo 15 - Demonstração das Variações Patrimoniais do RPPS');
} else {
    $obPDF->setAcao('Anexo 15 - Demonstração das Variações Patrimoniais');
}
$dtPeriodo = "Período: ".$arFiltro['stDataInicial']." a ".$arFiltro['stDataFinal']."".$arFiltro['relatorio'];
$obPDF->setSubTitulo($dtPeriodo);
$obPDF->setUsuario(Sessao::getUsername());
$obPDF->setEnderecoPrefeitura($arConfiguracao);

$rsAnexo15 = Sessao::read('rsAnexo15');
$rsAnexo15->addFormatacao('vl_receita'      , 'NUMERIC_BR_NULL');
$rsAnexo15->addFormatacao('vl_total_receita', 'NUMERIC_BR_NULL');
$rsAnexo15->addFormatacao('vl_despesa'      , 'NUMERIC_BR_NULL');
$rsAnexo15->addFormatacao('vl_total_despesa', 'NUMERIC_BR_NULL');

$obPDF->addRecordSet(new RecordSet);
$obPDF->setAlinhamento('C');
$obPDF->addCabecalho  ('VARIAÇÕES ATIVAS'   , 50, 12 );
$obPDF->addCabecalho  ('VARIAÇÕES PASSIVAS' , 50, 12 );

$obPDF->addRecordSet($rsAnexo15, false);
$obPDF->setQuebraPaginaLista(false);

$obPDF->addIndentacao  ('nivel_receita', 'descricao_receita', '  ');
$obPDF->addIndentacao  ('nivel_despesa', 'descricao_despesa', '  ');

$obPDF->setAlinhamento('C');
$obPDF->addCabecalho  ('TITULOS', 24, 10);
$obPDF->setAlinhamento('R');
$obPDF->addCabecalho  (' ', 7, 10 );
$obPDF->addCabecalho  (' ', 7, 10 );
$obPDF->addCabecalho  (' ', 10, 10);
$obPDF->setAlinhamento('C');
$obPDF->addCabecalho  ('TITULOS', 24, 10);
$obPDF->setAlinhamento('R');
$obPDF->addCabecalho  (' ', 7, 10);
$obPDF->addCabecalho  (' ', 7, 10);

$obPDF->setAlinhamento('L');
$obPDF->addCampo      ('descricao_receita', 7);
$obPDF->setAlinhamento('R');
$obPDF->addCampo      ('vl_receita'      , 7);
$obPDF->addCampo      ('vl_total_receita', 7);
$obPDF->addCampo      (' '               , 7);
$obPDF->setAlinhamento('L');
$obPDF->addCampo      ('descricao_despesa', 7);
$obPDF->setAlinhamento('R');
$obPDF->addCampo      ('vl_despesa'      , 7);
$obPDF->addCampo      ('vl_total_despesa', 7);

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
    $obPDF->addCabecalho("", 1,  10);
    $obPDF->addCabecalho("", 1, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("nota", 8 );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("", 8 );
}

$obPDF->show();
?>
