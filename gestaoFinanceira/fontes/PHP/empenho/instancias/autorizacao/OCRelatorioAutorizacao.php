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
    * Arquivo Oculo para processamento do relatório
    * Data de Criação   : 07/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desencolvedor: Eduardo Martins

    * @ignore

    $Revision: 32570 $
    $Name$
    $Author: lbbarreiro $
    $Date: 2008-01-02 08:44:54 -0200 (Qua, 02 Jan 2008) $

    * Casos de uso: uc-02.03.02
                    uc-02.03.19
                    uc-02.03.20
                    uc-02.01.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioAutorizacao.class.php"         );

$obRRelatorio    = new RRelatorio;
$obRegra         = new REmpenhoRelatorioAutorizacao;

//seta elementos do filtro
$stFiltro = "";

$arFiltro = Sessao::read('filtroRelatorio');

$arAutorizacoesHomologacao = Sessao::read('stImpressaoAutorizacao');

//seta elementos do filtro para ENTIDADE
if (!$arAutorizacoesHomologacao) {
    if ($arFiltro['inCodEntidade'] != "") {
        $stFiltro .= " AND ae.cod_entidade = " . $arFiltro['inCodEntidade'];
    }
    if ($arFiltro['inCodAutorizacao'] != "") {
        $stFiltro .= " AND ae.cod_autorizacao = " . $arFiltro['inCodAutorizacao'];
    }
    if ($arFiltro['inCodPreEmpenho'] != "") {
        $stFiltro .= " AND ae.cod_pre_empenho = " . $arFiltro['inCodPreEmpenho'];
    }

    $obRegra->setDotacao($arFiltro['inCodDespesa']);

    if ( $arFiltro['stExercicio'] != "" )
        $stFiltro .= " AND ae.exercicio = '" . $arFiltro['stExercicio'] . "' ";
    else
        $stFiltro .= " AND ae.exercicio = '" . Sessao::getExercicio() . "' ";

    if ($arFiltro['stAcao'] == 'imprimirAnulacao' or $arFiltro['stAcao'] == 'reemitir') {
        Sessao::write('tipoRelatorio', 'anulacao');
        $obRegra->geraRecordSet( $arRecordSet,"cod_pre_empenho, num_item",'anulacao', $stFiltro);
    } else {
        Sessao::write('tipoRelatorio', 'autorizacao');
        $obRegra->geraRecordSet( $arRecordSet , "", 'autorizacao', $stFiltro);
    }

    Sessao::write('rsRecordSet',$arRecordSet );
    $obRRelatorio->executaFrameOculto( "OCGeraRelatorioAutorizacao.php" );

} else {

    // Irá montar o PDF com cabeçalho personalizado.
    $obRRelatorio->executaFrameOculto( "OCGeraRelatorioAutorizacaoHomologacao.php" );

}

?>
