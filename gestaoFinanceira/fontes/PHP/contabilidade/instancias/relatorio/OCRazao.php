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
set_time_limit(0);
/**
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 29/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: OCRazao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                        );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioRazao.class.php"  );

$obRRelatorio                   = new RRelatorio;
$obRContabilidadeRelatorioRazao = new RContabilidadeRelatorioRazao;

//seta elementos do filtro
$stFiltro = "";

//seta elementos do filtro para ENTIDADE
$arFiltro = Sessao::read('filtroRelatorio');
if ($arFiltro['inCodEntidade'] != "") {
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stEntidade .= $valor.",";
    }
    $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
} else {
    $stEntidade .= $arFiltro['stTodasEntidades'];
}

$obRContabilidadeRelatorioRazao->setFiltro( $stFiltro );
$obRContabilidadeRelatorioRazao->setCodEntidade( $stEntidade );
$obRContabilidadeRelatorioRazao->setExercicio( Sessao::getExercicio() );
$obRContabilidadeRelatorioRazao->setCodPlanoInicial( $arFiltro['inCodPlanoInicial'] );
$obRContabilidadeRelatorioRazao->setCodPlanoFinal( $arFiltro['inCodPlanoFinal'] );
$obRContabilidadeRelatorioRazao->setCodEstruturalInicial( $arFiltro['stCodEstruturalInicial'] );
$obRContabilidadeRelatorioRazao->setCodEstruturalFinal( $arFiltro['stCodEstruturalFinal'] );
$obRContabilidadeRelatorioRazao->setDtInicial( $arFiltro['stDataInicial'] );
$obRContabilidadeRelatorioRazao->setDtFinal( $arFiltro['stDataFinal'] );
$boHistoricoCompleto = ( $arFiltro['boHistoricoCompleto'] == 'S' ) ? true : false;
$obRContabilidadeRelatorioRazao->setHistoricoCompleto( $boHistoricoCompleto );
$boQuebraPagina = ( $arFiltro['boQuebraPaginaConta'] == 'S' ) ? true : false;
$obRContabilidadeRelatorioRazao->setQuebraPaginaConta( $boQuebraPagina );
$obRContabilidadeRelatorioRazao->setMovimentacaoConta( $arFiltro['boMovimentacaoConta'] );

$obRContabilidadeRelatorioRazao->geraRecordSet( $arRazao );

Sessao::remove('arRazao');
Sessao::write('arRazao', $arRazao);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioRazao.php" );
?>
