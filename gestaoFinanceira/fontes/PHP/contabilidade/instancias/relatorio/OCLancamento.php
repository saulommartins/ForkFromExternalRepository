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
    * Data de Criação   : 26/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desencolvedor: Gelson W. Gonçalves

    * @ignore

    * $Id: OCLancamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioLancamento.class.php"  );

$obRRelatorio                        = new RRelatorio;
$obRContabilidadeRelatorioLancamento = new RContabilidadeRelatorioLancamento;

//seta elementos do filtro
$stFiltro = "";

//seta elementos do filtro para ENTIDADE
$arFiltro = Sessao::read('filtroRelatorio');
if ($arFiltro['inCodEntidade'] != "") {
    $stFiltro .= " l.cod_entidade IN  (";
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stFiltro .= $valor." , ";
    }
    $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 2 ) . ") AND ";

} else {
    $stFiltro .= $arFiltro['stTodasEntidades'];
}

$obRContabilidadeRelatorioLancamento->setFiltro( $stFiltro );
$obRContabilidadeRelatorioLancamento->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
$obRContabilidadeRelatorioLancamento->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( $arFiltro['stDtLote'] );
$obRContabilidadeRelatorioLancamento->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $arFiltro['inCodLote'] );
$obRContabilidadeRelatorioLancamento->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote( $arFiltro['stNomLote'] );
$obRContabilidadeRelatorioLancamento->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLoteInicial( $arFiltro['stDataInicial'] );
$obRContabilidadeRelatorioLancamento->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLoteTermino( $arFiltro['stDataFinal'] );

$stCod_entidade = "";
if ($arFiltro['inCodEntidade'] != "") {
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
    $stCod_entidade .= $valor." , ";
    }
    $stCod_entidade = substr( $stCod_entidade, 0, strlen($stCod_entidade) - 2 );
    $obRContabilidadeRelatorioLancamento->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $stCod_entidade );
} else {
    $obRContabilidadeRelatorioLancamento->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $arFiltro['stTodasEntidades']  );
}

$obRContabilidadeRelatorioLancamento->geraRecordSet( $rsLancamento, " cod_entidade, cod_lote, dt_lote, tipo, sequencia, tipo_valor DESC" );
Sessao::write('rsLancamento', $rsLancamento);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioLancamento.php" );
?>
