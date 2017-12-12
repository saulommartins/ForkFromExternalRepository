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
    * Página oculta pra pré-processar o relatorio
    * Data de Criação   : 27/09/2004

    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Gustavo Tourinho

    * @ignore

    $Revision: 30762 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.17
*/

/*
$Log$
Revision 1.5  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexoDetalhamento.class.php" );

$obRRelatorio                  = new RRelatorio;
$obROrcamentoAnexoDetalhamento = new ROrcamentoRelatorioAnexoDetalhamento;

//seta elementos do filtro
$arFiltro = Sessao::read('filtroRelatorio');
$stFiltro = "";
if ($arFiltro['inCodEntidade'] != "") {
    $stFiltro = " AND cod_entidade IN  (";
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stEntidades .= $valor.",";
    }
    if ($stEntidades != "") {
        $stEntidades = substr( $stEntidades, 0, strlen($stEntidades) - 1 );
    }
    $stFiltro .= $stEntidades . ")";
}

$obROrcamentoAnexoDetalhamento->setFiltro( $stFiltro );
$obROrcamentoAnexoDetalhamento->setSituacao($arFiltro['stSituacao']);
$obROrcamentoAnexoDetalhamento->setTipoRelatorio( $arFiltro['stTipoRelatorio']);
$obROrcamentoAnexoDetalhamento->setDataInicial($arFiltro['stDataInicial']);
$obROrcamentoAnexoDetalhamento->setDataFinal( $arFiltro['stDataFinal']);
$obROrcamentoAnexoDetalhamento->setExercicio ( Sessao::getExercicio()   );
$obROrcamentoAnexoDetalhamento->setEntidades($stEntidades);
$obROrcamentoAnexoDetalhamento->geraRecordSet( $rsAnexoDetalhamento );

Sessao::write('rsAnexoDetalhamento',$rsAnexoDetalhamento);
Sessao::write('stSituacao',$arFiltro['stSituacao']);
//sessao->transf5[0] = $rsAnexoDetalhamento;
//sessao->transf5[1] = //sessao->filtro['stSituacao'];

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioAnexoDetalhamento.php" );
?>
