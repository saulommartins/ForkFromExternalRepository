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
    * Data de Criação   : 07/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * $Id: OCGeraRelatorioConciliacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.19

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaRelatorioConciliacao.class.php";

$obRRelatorio = new RRelatorio;
$obPDF = new ListaFormPDF();
$rsVazio = new RecordSet;
$arFiltro = Sessao::read('filtroRelatorio');
$arRecordSet = Sessao::read('arDados');

$obRTesourariaConciliacao = new RTesourariaConciliacao();
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->setExercicio($arFiltro['stExercicio']);
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->setCodPlano($arFiltro['inCodPlano']);
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->listarPlanoConta($rsRecordSet, '', '', $boTransacao);
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade($arFiltro['inCodEntidade']);
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->obROrcamentoEntidade->listar($rsEntidade, '', '', $boTransacao);

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio(Sessao::getExercicio());
$obRRelatorio->recuperaCabecalho($arConfiguracao);
$obPDF->setModulo("Conciliação Bancária");

if ($arFiltro['inCodEntidade']) {
    $obPDF->setAcao($arFiltro['inCodEntidade']." - ".$rsEntidade->getCampo("nom_cgm"));
} else {
    $obPDF->setAcao("Conciliar");
}

$obPDF->setSubTitulo($arFiltro['inCodPlano']." - ".$rsRecordSet->getCampo("nom_conta"));
$obPDF->setUsuario(Sessao::getUsername());
$obPDF->setData(date("d/m/Y"));
$obPDF->setEnderecoPrefeitura($arConfiguracao);

$obPDF->addRecordSet        ($arRecordSet[0]);
$obPDF->setAlinhamento      ("L");
$obPDF->addCabecalho        ("", 25, 8);
$obPDF->addCabecalho        ("", 70, 8);
$obPDF->addCampo            ("descricao", 8, 'B', '', 'LTRB');
$obPDF->addCampo            ("valor", 8, '', '', 'LTRB');

$obPDF->addRecordSet        ($arRecordSet[1]);
$obPDF->setQuebraPaginaLista(false);
$obPDF->setAlinhamento      ("C");
$obPDF->addCabecalho        ("", 95, 8);
$obPDF->addCampo            ("movimentacao_conciliada", 8, 'B', '', 'LTRB','205,206,205');

$obPDF->addRecordSet        ($rsVazio);
$obPDF->setAlturaCabecalho  (5);
$obPDF->setQuebraPaginaLista(false);
$obPDF->setAlinhamento      ("C");
$obPDF->addCabecalho        ("Entradas não consideradas pelo banco", 95, 8, 'B', '', 'LTRB','205,206,205');

if ($arRecordSet[2]->getNumLinhas() > 0) {
    $obPDF->addRecordSet        ($arRecordSet[2]);
    $obPDF->setAlturaCabecalho  (5);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento      ("C");
    $obPDF->addCabecalho        ("DATA", 15, 8, 'B', '', 'LTRB','205,206,205');
    $obPDF->addCabecalho        ("DESCRIÇÃO", 60, 8, 'B', '', 'LTRB','205,206,205');
    $obPDF->addCabecalho        ("VALOR", 20, 8, 'B', '', 'LTRB','205,206,205');
    $obPDF->setAlinhamento      ("C");
    $obPDF->addCampo            ("movimentacao", 8, '', '', 'LTRB' );
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCampo            ("descricao", 8, '', '', 'LTRB' );
    $obPDF->setAlinhamento      ("R");
    $obPDF->addCampo            ("valor", 8, '', '', 'LTRB' );
}

$obPDF->addRecordSet        ($rsVazio);
$obPDF->setAlturaCabecalho  (5);
$obPDF->setQuebraPaginaLista(false);
$obPDF->setAlinhamento      ("C");
$obPDF->addCabecalho        ("Saídas não consideradas pelo banco", 95, 8, 'B', '', 'LTRB','205,206,205');

if ($arRecordSet[3]->getNumLinhas() > 0) {
    $obPDF->addRecordSet        ($arRecordSet[3]);
    $obPDF->setAlturaCabecalho  (5);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento      ("C");
    $obPDF->addCabecalho        ("DATA", 15, 8, 'B', '', 'LTRB','205,206,205');
    $obPDF->addCabecalho        ("DESCRIÇÃO", 60, 8, 'B', '', 'LTRB','205,206,205');
    $obPDF->addCabecalho        ("VALOR", 20, 8, 'B', '', 'LTRB','205,206,205');
    $obPDF->setAlinhamento      ("C");
    $obPDF->addCampo            ("movimentacao", 8, '', '', 'LTRB' );
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCampo            ("descricao", 8, '', '', 'LTRB' );
    $obPDF->setAlinhamento      ("R");
    $obPDF->addCampo            ("valor", 8, '', '', 'LTRB' );
}

$obPDF->addRecordSet        ($rsVazio);
$obPDF->setAlturaCabecalho  (5);
$obPDF->setQuebraPaginaLista(false);
$obPDF->setAlinhamento      ("C");
$obPDF->addCabecalho        ("Entradas não consideradas pela tesouraria", 95, 8, 'B', '', 'LTRB','205,206,205');

if ($arRecordSet[4]->getNumLinhas() > 0) {
    $obPDF->addRecordSet        ($arRecordSet[4]);
    $obPDF->setAlturaCabecalho  (5);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento      ("C");
    $obPDF->addCabecalho        ("DATA", 15, 8, 'B', '', 'LTRB','205,206,205');
    $obPDF->addCabecalho        ("DESCRIÇÃO", 60, 8, 'B', '', 'LTRB','205,206,205');
    $obPDF->addCabecalho        ("VALOR", 20, 8, 'B', '', 'LTRB','205,206,205');
    $obPDF->setAlinhamento      ("C");
    $obPDF->addCampo            ("movimentacao", 8, '', '', 'LTRB' );
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCampo            ("descricao", 8, '', '', 'LTRB' );
    $obPDF->setAlinhamento      ("R");
    $obPDF->addCampo            ("valor", 8, '', '', 'LTRB' );
}

$obPDF->addRecordSet        ($rsVazio);
$obPDF->setAlturaCabecalho  (5);
$obPDF->setQuebraPaginaLista(false);
$obPDF->setAlinhamento      ("C");
$obPDF->addCabecalho        ("Saídas não consideradas pela tesouraria", 95, 8, 'B', '', 'LTRB','205,206,205');

if ($arRecordSet[5]->getNumLinhas() > 0) {
    $obPDF->addRecordSet        ($arRecordSet[5]);
    $obPDF->setAlturaCabecalho  (5);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento      ("C");
    $obPDF->addCabecalho        ("DATA", 15, 8, 'B', '', 'LTRB','205,206,205');
    $obPDF->addCabecalho        ("DESCRIÇÃO", 60, 8, 'B', '', 'LTRB','205,206,205');
    $obPDF->addCabecalho        ("VALOR", 20, 8, 'B', '', 'LTRB','205,206,205');
    $obPDF->setAlinhamento      ("C");
    $obPDF->addCampo            ("movimentacao", 8, '', '', 'LTRB' );
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCampo            ("descricao", 8, '', '', 'LTRB' );
    $obPDF->setAlinhamento      ("R");
    $obPDF->addCampo            ("valor", 8, '', '', 'LTRB' );
}

$obPDF->addRecordSet        ($arRecordSet[6]);
$obPDF->setAlturaCabecalho  (5);
$obPDF->setQuebraPaginaLista(false);
$obPDF->addCabecalho        ("", 15, 1);
$obPDF->addCabecalho        ("", 60, 1);
$obPDF->addCabecalho        ("", 20, 1);
$obPDF->setAlinhamento      ("C");
$obPDF->addCampo            ("movimentacao", 8, '', '', 'LTRB','205,206,205' );
$obPDF->setAlinhamento      ("L");
$obPDF->addCampo            ("descricao", 8, '', '', 'LTRB','205,206,205' );
$obPDF->setAlinhamento      ("R");
$obPDF->addCampo            ("valor", 8, '', '', 'LTRB','205,206,205' );

$obPDF->addRecordSet        ($rsVazio);
$obPDF->setAlturaCabecalho  (5);
$obPDF->setQuebraPaginaLista(false);
$obPDF->addCabecalho        ("", 6, 5);

$obPDF->addRecordSet        ($rsVazio);
$obPDF->setAlturaCabecalho  (5);
$obPDF->setQuebraPaginaLista(false);
$obPDF->addCabecalho        ("", 6, 5);

$obPDF->addRecordSet        ($arRecordSet[7]);
$obPDF->setQuebraPaginaLista(false);
$obPDF->setAlturaCabecalho  (5);
$obPDF->addCabecalho        ("", 31, 8);
$obPDF->addCabecalho        ("", 32, 8);
$obPDF->addCabecalho        ("", 33, 8);
$obPDF->setAlinhamento      ("C");
$obPDF->addCampo            ("assinatura1", 8);
$obPDF->addCampo            ("assinatura2", 8);
$obPDF->addCampo            ("assinatura3", 8);

$obPDF->show();

?>
