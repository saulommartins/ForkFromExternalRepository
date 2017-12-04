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
    * Página de Processamento de Configuração do módulo Tesouraria
    * Data de Criação   : 15/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    * $Id: OCRelatorioConciliacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaRelatorioConciliacao.class.php";

$obRRelatorio  = new RRelatorio;
$obRegra = new RTesourariaRelatorioConciliacao;
$arFiltro = Sessao::read('filtroRelatorio');
$arFiltroForm = Sessao::read('filtroGeraRel');

if ($arFiltro['stExercicio']) {
    $obRegra->obRTesourariaConciliacao->obRContabilidadePlanoBanco->setExercicio($arFiltro['stExercicio']);
} else {
    $obRegra->obRTesourariaConciliacao->obRContabilidadePlanoBanco->setExercicio(Sessao::getExercicio());
}

$obRegra->obRTesourariaConciliacao->obRContabilidadePlanoBanco->setCodPlano( $arFiltro['inCodPlano'] );
$obRegra->obRTesourariaConciliacao->setMes(intval($arFiltro['inMes']));
$obRegra->obRTesourariaConciliacao->setDataInicial($arFiltroForm['filtro']['stDataInicial']);
$obRegra->obRTesourariaConciliacao->setDataFinal($arFiltroForm['stDtExtrato']);
$obRegra->obRTesourariaConciliacao->obRTesourariaAssinatura->obROrcamentoEntidade->setCodigoEntidade($arFiltro['inCodEntidade']);
$obRegra->setSaldoTesouraria($arFiltro['nuSaldoTesouraria']);
$obRegra->setAgrupar($arFiltro['boAgrupar']);
$obRegra->geraRecordSet($arRecordSet);

Sessao::write('arDados', $arRecordSet);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioConciliacao.php" );
?>
