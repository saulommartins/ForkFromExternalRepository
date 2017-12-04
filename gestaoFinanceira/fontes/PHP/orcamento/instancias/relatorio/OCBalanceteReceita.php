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

    $Id: OCBalanceteReceita.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioBalanceteReceita.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsTotalEntidades , " ORDER BY cod_entidade" );

$obRRelatorio      = new RRelatorio;
$obROrcamentoBalanceteReceita = new ROrcamentoRelatorioBalanceteReceita;

//seta elementos do filtro para ENTIDADE
$arFiltro = Sessao::read('filtroRelatorio');
if ($arFiltro['inCodEntidade'] != "") {
    $inCount = 0;
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        if (!isset($stEntidade)) {
            $stEntidade = $valor.",";
        } else {
            $stEntidade .= $valor.",";
        }
        $inCount++;
    }
    $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
} else {
    $stEntidade .= $arFiltro['stTodasEntidades'];
}

if ( $rsTotalEntidades->getNumLinhas() == $inCount ) {
   $arFiltro['relatorio'] = "Consolidado";
} else {
   $arFiltro['relatorio'] = "";
}

$stFiltro = "";

$obROrcamentoBalanceteReceita->setFiltro                 ( $stFiltro );
$obROrcamentoBalanceteReceita->setCodEntidade            ( $stEntidade );
$obROrcamentoBalanceteReceita->setExercicio              ( Sessao::getExercicio() );
$obROrcamentoBalanceteReceita->setCodReduzidoInicial     ( $arFiltro['inCodReceitaInicial'] );
$obROrcamentoBalanceteReceita->setCodReduzidoFinal       ( $arFiltro['inCodReceitaFinal'] );
$obROrcamentoBalanceteReceita->setCodEstruturalInicial   ( $arFiltro['stCodEstruturalInicial'] );
$obROrcamentoBalanceteReceita->setCodEstruturalFinal     ( $arFiltro['stCodEstruturalFinal'] );

$obROrcamentoBalanceteReceita->setDataInicial            ( $arFiltro['stDataInicial'] );
$obROrcamentoBalanceteReceita->setDataFinal              ( $arFiltro['stDataFinal'] );
$obROrcamentoBalanceteReceita->setCodRecurso             ( $arFiltro['inCodRecurso'] );

if(isset($arFiltro['inCodUso']) && $arFiltro['inCodUso'] != "" && isset($arFiltro['inCodDestinacao']) && $arFiltro['inCodDestinacao'] != "" && isset($arFiltro['inCodEspecificacao']) && $arFiltro['inCodEspecificacao'] != "")
    $obROrcamentoBalanceteReceita->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );

if (isset($arFiltro['inCodDetalhamento'])) {
    $obROrcamentoBalanceteReceita->setCodDetalhamento( $arFiltro['inCodDetalhamento'] );
}

$obROrcamentoBalanceteReceita->geraRecordSet($rsBalanceteReceita);

Sessao::write('rsBalanceteReceita',$rsBalanceteReceita);
Sessao::write('rsResumoRecurso',$obROrcamentoBalanceteReceita->getRsResumoRecurso());

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioBalanceteReceita.php" );

?>
