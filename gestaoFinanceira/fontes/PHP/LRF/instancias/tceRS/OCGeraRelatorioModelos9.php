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
    * Página de Geração do layoult do relatório
    * Data de Criação   : 25/05/2005

    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-10-27 16:37:56 -0300 (Sex, 27 Out 2006) $

    * Casos de uso :uc-02.05.10, uc-02.01.35
*/

/*
$Log$
Revision 1.8  2006/10/27 19:37:33  cako
Bug #6773#

Revision 1.7  2006/08/25 17:50:22  fernando
Bug #6773#

Revision 1.6  2006/07/25 17:46:55  cako
Bug #6642#

Revision 1.5  2006/07/05 20:45:22  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_LRF_NEGOCIO."RLRFRelatorioModelos5.class.php"  );

$obRegra      = new RLRFRelatorioModelos5;
$obPDF        = new ListaPDF();

$obRegra->obRRelatorio->recuperaCabecalho ( $arConfiguracao          );
$obPDF->setModulo                ( "LRF - ".Sessao::getExercicio()   );
$obPDF->setTitulo                ( "Modelo " . $sessao->filtro['inCodModelo'] );

$obRegra->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRegra->obROrcamentoEntidade->setVerificaConfiguracao  ( true );
$obRegra->obROrcamentoEntidade->obRCGM->consultar($rsCGM);

$subTitulo = "Período: de " . $sessao->transf4[1] . " até " . $sessao->transf4[2];
$obPDF->setSubTitulo             ( $subTitulo  );

$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsVazio = new RecordSet;

$obPDF->SetMargins(2,2,2);
$obPDF->recalculaDimensoes();

switch ($sessao->filtro['stTipoDespesa']) {
    case 'E': $stTipoDespesa = 'Empenhada'; break;
    case 'L': $stTipoDespesa = 'Liquidada'; break;
    case 'P': $stTipoDespesa = 'Paga';      break;
}

$obPDF->addRecordSet($rsVazio);
$obPDF->setAlinhamento ( "C" );
if (Sessao::read('modulo') != 8)
    $obPDF->addCabecalho("Modelo 9 - Demonstrativo dos Limites - RGF", 100, 10);

$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
if (Sessao::read('modulo') != 8)
    $obPDF->addCabecalho("Executivo/Indiretas Municipais a partir do Exercício de ".Sessao::getExercicio(), 100, 9);

//#MODELO 1
if (!empty($sessao->transf5[1])) {
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
if (Sessao::read('modulo') != 8) {
    $obPDF->addCabecalho("MODELO 1 - DEMONSTRATIVO DA RECEITA CORRENTE LÍQUIDA", 100, 9);
} else {
    $obPDF->addCabecalho("DEMONSTRATIVO DA RECEITA CORRENTE LÍQUIDA", 100, 9);
}
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCabecalho("Despesa ".$stTipoDespesa." - Em R$", 100, 9);

    $obPDF->addRecordSet($sessao->transf5[1]);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("RECEITA CORRENTE LÍQUIDA", 70, 8);
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("VALOR AJUSTADO", 30, 8);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna2", 6 );
}

//MODELO 2

if (!empty($sessao->transf5[2])) {
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
if (Sessao::read('modulo') != 8 ) {
    $obPDF->addCabecalho("MODELO 2 - DEMONSTRATIVO DA DESPESA COM PESSOAL", 100, 9);
} else {
    $obPDF->addCabecalho("DEMONSTRATIVO DA DESPESA COM PESSOAL", 100, 9);
}
    $obPDF->addRecordSet($sessao->transf5[2]);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("DESPESA COM PESSOAL", 70, 8);
    $obPDF->addCabecalho("VALOR AJUSTADO", 22, 8);
    $obPDF->addCabecalho("% S/ RCL", 8, 8);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->addCampo("coluna3", 6 );
}

//MODELO 4

if (!empty($sessao->transf5[4])) {
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
if (Sessao::read('modulo') != 8) {
    $obPDF->addCabecalho("MODELO 4 - DEMONSTRATIVO DA DÍVIDA CONSOLIDADA LÍQUIDA", 100, 9);
} else {
    $obPDF->addCabecalho("DEMONSTRATIVO DA DÍVIDA CONSOLIDADA LÍQUIDA - DCL", 100, 9);
}

    $obPDF->addRecordSet($sessao->transf5[4]);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("DÍVIDA", 70, 8);
    $obPDF->addCabecalho("VALOR AJUSTADO", 22, 8);
    $obPDF->addCabecalho("% S/ RCL", 8, 8);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->addCampo("coluna3", 6 );
}

//MODELO 5

if (!empty($sessao->transf5[5])) {
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
if (Sessao::read('modulo') != 8) {
    $obPDF->addCabecalho("MODELO 5 - DEMONSTRATIVO DAS GARANTIAS E CONTRAGARANTIAS DE VALORES", 100, 9);
} else {
    $obPDF->addCabecalho("DEMONSTRATIVO DAS GARANTIAS E CONTRAGARANTIAS", 100, 9);
}
    $obPDF->addRecordSet($sessao->transf5[5]);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("GARANTIA DE VALORES", 70, 8);
    $obPDF->addCabecalho("VALOR AJUSTADO", 22, 8);
    $obPDF->addCabecalho("% S/ RCL", 8, 8);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->addCampo("coluna3", 6 );
}

if (!empty($sessao->transf5[6])) {
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
if (Sessao::read('modulo') != 8) {
    $obPDF->addCabecalho("MODELO 6 - DEMONSTRATIVO DAS OPERAÇÕES DE CRÉDITO", 100, 9);
} else {
    $obPDF->addCabecalho("DEMONSTRATIVO DAS OPERAÇÕES DE CRÉDITO", 100, 9);
}

    $obPDF->addRecordSet($sessao->transf5[6]);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCabecalho("OPERAÇÕES DE CRÉDITO", 70, 8);
    $obPDF->addCabecalho("VALOR AJUSTADO", 22, 8);
    $obPDF->addCabecalho("% S/ RCL", 8, 8);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("coluna1", 6 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo("coluna2", 6 );
    $obPDF->addCampo("coluna3", 6 );
}

$obPDF->show();
?>
