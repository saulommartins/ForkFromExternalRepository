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
    * Página de processamento oculto e geração do relatório Periódico
    * Data de Criação   : 22/05/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: OCGeraRelatorioPeriodico.php 30003 2008-05-27 18:25:08Z cercato $

    * Casos de uso: uc-05.03.23
*/

/*
$Log$
Revision 1.2  2007/05/30 13:01:35  dibueno
Bug #9279#

Revision 1.1  2007/05/23 19:34:52  dibueno
Bug #9279#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php" );
include_once ( CAM_FW_PDF."ListaPDF.class.php" );
#include_once ( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php" );
#include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPagamento.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRRelatorioValoresLancados.class.php" );

$obRRelatorio = new RRelatorio;

$arFiltro = Sessao::read( 'filtroRelatorio' );

$obRARRRelatorioValoresLancados = new RARRRelatorioValoresLancados;
$obRARRRelatorioValoresLancados->setTipoRelatorio         ( "Periódico" );

$obRARRRelatorioValoresLancados->setCodEstAtivInicial ( $arFiltro['inCodInicio'] );
$obRARRRelatorioValoresLancados->setCodEstAtivFinal ( $arFiltro['inCodTermino'] );

$obRARRRelatorioValoresLancados->setCodGrupoCreditoInicio ( $arFiltro['inCodGrupoInicio']         );
$obRARRRelatorioValoresLancados->setCodGrupoCreditoTermino( $arFiltro['inCodGrupoTermino']        );

$obRARRRelatorioValoresLancados->setCodCreditoInicio      ( $arFiltro['inCodCreditoInicio']       );
$obRARRRelatorioValoresLancados->setCodCreditoTermino     ( $arFiltro['inCodCreditoTermino']      );

$obRARRRelatorioValoresLancados->setNumCGMInicio          ( $arFiltro['inCodContribuinteInicial'] );
$obRARRRelatorioValoresLancados->setNumCGMTermino         ( $arFiltro['inCodContribuinteFinal']   );

$obRARRRelatorioValoresLancados->setInscricaoImobiliariaInicio($arFiltro['inNumInscricaoImobiliariaInicial'] );
$obRARRRelatorioValoresLancados->setInscricaoImobiliariaTermino ( $arFiltro['inNumInscricaoImobiliariaFinal']);

$obRARRRelatorioValoresLancados->setInscricaoEconomicaInicio    ( $arFiltro['inNumInscricaoEconomicaInicial']);
$obRARRRelatorioValoresLancados->setInscricaoEconomicaTermino   ( $arFiltro['inNumInscricaoEconomicaFinal']  );

$obRARRRelatorioValoresLancados->setDtInicio              ( $arFiltro['dtInicio']                 );
$obRARRRelatorioValoresLancados->setDtFinal               ( $arFiltro['dtFinal']                  );
$obRARRRelatorioValoresLancados->setTipoRelatorio ( $arFiltro['stTipoRelatorio'] );

$obErro = $obRARRRelatorioValoresLancados->geraRecordSetPeriodico ( $rsRecordSet, $rsRecordSetSomas, $arCabecalho, $stOrder );

Sessao::write( 'sessao_transf4', $rsRecordSet );

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioPeriodico.php" );
#=====================================================================================
?>
