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
    * Data de Criação   : 30/11/2005

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30835 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF.'RRelatorio.class.php';
include_once CAM_GF_TES_NEGOCIO.'RTesourariaRelatorioTransferenciasBancarias.class.php';

$obRRelatorio = new RRelatorio;
$obRTesourariaRelatorioTransferenciasBancarias = new RTesourariaRelatorioTransferenciasBancarias;

$stEntidade = '';

$arFiltro = Sessao::read('filtroRelatorio');

foreach ($arFiltro['inCodigoEntidadesSelecionadas'] as $key => $valor) {
    $stEntidade.= $valor . ',';
}

$stEntidade = substr($stEntidade, 0, strlen($stEntidade) - 1);

if (trim($arFiltro['inContaBancoInicial']) == '') {
    $inContaBancoInicial = 0;
} else {
    $inContaBancoInicial = $arFiltro['inContaBancoInicial'];
}
if (trim($arFiltro['inContaBancoFinal']) == '') {
    $inContaBancoFinal = 0;
} else {
    $inContaBancoFinal = $arFiltro['inContaBancoFinal'];
}

$obRTesourariaRelatorioTransferenciasBancarias->setEntidade            ($stEntidade);
$obRTesourariaRelatorioTransferenciasBancarias->setExercicio           ($arFiltro['stExercicio']);
$obRTesourariaRelatorioTransferenciasBancarias->setDataInicial         ($arFiltro['stDataInicial']);
$obRTesourariaRelatorioTransferenciasBancarias->setDataFinal           ($arFiltro['stDataFinal']);
$obRTesourariaRelatorioTransferenciasBancarias->setContaBancoInicial   ($inContaBancoInicial);
$obRTesourariaRelatorioTransferenciasBancarias->setContaBancoFinal     ($inContaBancoFinal);
$obRTesourariaRelatorioTransferenciasBancarias->setCodTipoTransferencia($arFiltro['inCodTipoTransferencia']);

$obRTesourariaRelatorioTransferenciasBancarias->geraRecordSet($rsTransferenciasBancarias);

Sessao::write('arDados', $rsTransferenciasBancarias);
$obRRelatorio->executaFrameOculto('OCGeraRelatorioTransferenciasBancarias.php');

?>
