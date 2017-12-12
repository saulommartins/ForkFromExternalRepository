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
    * Data de Criação   : 05/12/2005

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    * $Id: OCResumoDespesa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioResumoDespesa.class.php"  );

$obRRelatorio                          = new RRelatorio;
$obRTesourariaRelatorioResumoDespesa = new RTesourariaRelatorioResumoDespesa;

$stEntidade = "";

$arFiltro = Sessao::read('filtroRelatorio');

foreach ($arFiltro['inCodigoEntidadesSelecionadas'] as $key => $valor) {
    $stEntidade.= $valor . ",";
}

$stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );

if (trim($arFiltro['inDespesaInicial']) == "") {
    $arFiltro['inDespesaInicial'] = 0;
}
if (trim($arFiltro['inDespesaFinal']) == "") {
    $arFiltro['inDespesaFinal'] = 0;
}

if (trim($arFiltro['inContaBancoInicial']) == "") {
    $arFiltro['inContaBancoInicial'] = 0;
}
if (trim($arFiltro['inContaBancoFinal']) == "") {
    $arFiltro['inContaBancoFinal'] = 0;
}

if (trim($arFiltro['inCodRecurso']) == "") {
    $arFiltro['inCodRecurso'] = 0;
}

$obRTesourariaRelatorioResumoDespesa->setEntidade           ( $stEntidade                            );
$obRTesourariaRelatorioResumoDespesa->setExercicio          ( $arFiltro['stExercicio']         );
$obRTesourariaRelatorioResumoDespesa->setDataInicial        ( $arFiltro['stDataInicial']       );
$obRTesourariaRelatorioResumoDespesa->setDataFinal          ( $arFiltro['stDataFinal']         );
$obRTesourariaRelatorioResumoDespesa->setTipoRelatorio      ( $arFiltro['stTipoRelatorio']     );
$obRTesourariaRelatorioResumoDespesa->setDespesaInicial     ( $arFiltro['inDespesaInicial']    );
$obRTesourariaRelatorioResumoDespesa->setDespesaFinal       ( $arFiltro['inDespesaFinal']      );
$obRTesourariaRelatorioResumoDespesa->setContaBancoInicial  ( $arFiltro['inContaBancoInicial'] );
$obRTesourariaRelatorioResumoDespesa->setContaBancoFinal    ( $arFiltro['inContaBancoFinal']   );
$obRTesourariaRelatorioResumoDespesa->setCodRecurso         ( $arFiltro['inCodRecurso']        );
if ($arFiltro['inCodUso'] != "" && $arFiltro['inCodDestinacao'] != "" && $arFiltro['inCodEspecificacao'] != "") {
    $obRTesourariaRelatorioResumoDespesa->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );
}

$obRTesourariaRelatorioResumoDespesa->setCodDetalhamento( $arFiltro['inCodDetalhamento'] );

$obRTesourariaRelatorioResumoDespesa->geraRecordSet( $rsResumoDespesa );

Sessao::write('arDados', $rsResumoDespesa);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioResumoDespesa.php" );

?>
