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
    * Data de Criação   : 27/09/2004

    * @author Desenvolvedor: Cassiano de Vasconcellos Fereira

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo8.class.php" );

$obRRelatorio             = new RRelatorio;
$obROrcamentoAnexo8 = new ROrcamentoRelatorioAnexo8;

//seta elementos do filtro
$stFiltro = "";
$arFiltro = Sessao::read('filtroRelatorio');

if ($arFiltro['inCodEntidade'] != "") {
    $stFiltro = " AND despesa.cod_entidade IN  (";
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stEntidades .= $valor.",";
    }
    if ($stEntidades != "") {
        $stEntidades = substr( $stEntidades, 0, strlen($stEntidades) - 1 );
    }
    $stFiltro .= $stEntidades . ")";
}
if ($arFiltro['stIntra']) {
    $stFiltro .= "  AND CONTA_DESPESA.COD_ESTRUTURAL ILIKE ''___.9.1%''  ";
}

$obROrcamentoAnexo8->setFiltro( $stFiltro );
$obROrcamentoAnexo8->setSituacao($arFiltro['stSituacao']);
$obROrcamentoAnexo8->setTipoRelatorio( $arFiltro['stTipoRelatorio']);
$obROrcamentoAnexo8->setDataInicial($arFiltro['stDataInicial']);
$obROrcamentoAnexo8->setDataFinal( $arFiltro['stDataFinal']);
$obROrcamentoAnexo8->setEntidades($stEntidades);

$obROrcamentoAnexo8->setExercicio (Sessao::getExercicio());
$obROrcamentoAnexo8->geraRecordSet( $rsAnexo8, $rsTotal, $arEntidade );

Sessao::write('rsAnexo8',$rsAnexo8);
Sessao::write('rsTotal',$rsTotal);
Sessao::write('arEntidade', $arEntidade);
Sessao::write('stSituacao', $arFiltro['stSituacao']);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioAnexo8.php" );
?>
