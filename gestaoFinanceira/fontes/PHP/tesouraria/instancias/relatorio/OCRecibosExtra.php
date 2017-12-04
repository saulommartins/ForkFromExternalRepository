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
    * Página de formulário oculto do relatório de recibos extra
    * Data de Criação   :

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Revision: 30835 $
    $Name$
    $Autor:$
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.32
*/

/**
$Log$
Revision 1.1  2006/09/04 17:20:10  fernando
formulário oculto do relatório de recibos extra

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioRecibosExtra.class.php"  );

$arFiltro = Sessao::read('filtroRelatorio');

if (trim($arFiltro['inCodCredor']) == "") {
    $arFiltro['inCodCredor'] = 0;
}

if (trim($arFiltro['inCodRecurso']) == "") {
    $arFiltro['inCodRecurso'] = 0;
}

if (trim($arFiltro['inCodContaBanco']) == "") {
    $arFiltro['inCodContaBanco'] = 0;
}
if (trim($arFiltro['inCodContaAnalitica']) == "") {
    $arFiltro['inCodContaAnalitica'] = 0;
}
$obRRelatorio  = new RRelatorio();
$obRTesourariaRelatorioRecibosExtra = new RTesourariaRelatorioRecibosExtra();

$obRTesourariaRelatorioRecibosExtra->setCodEntidade          ( implode(',',$arFiltro['inCodEntidade']) );
$obRTesourariaRelatorioRecibosExtra->setDataInicial          ( $arFiltro['stDataInicial']              );
$obRTesourariaRelatorioRecibosExtra->setDataFinal            ( $arFiltro['stDataFinal']                );
$obRTesourariaRelatorioRecibosExtra->setExercicio            ( $arFiltro['stExercicio']                );
$obRTesourariaRelatorioRecibosExtra->setTipoDemonstracao     ( $arFiltro['stTipoDemonstracao']      );
$obRTesourariaRelatorioRecibosExtra->setCodCredor            ( $arFiltro['inCodCredor']                );
$obRTesourariaRelatorioRecibosExtra->setCodRecurso           ( $arFiltro['inCodRecurso']               );
if($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao'])
    $obRTesourariaRelatorioRecibosExtra->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );

$obRTesourariaRelatorioRecibosExtra->setCodDetalhamento      ( $arFiltro['inCodDetalhamento']          );
$obRTesourariaRelatorioRecibosExtra->setCodContaBanco        ( $arFiltro['inCodContaBanco']            );
$obRTesourariaRelatorioRecibosExtra->setCodContaAnalitica    ( $arFiltro['inCodContaAnalitica']        );

$obRTesourariaRelatorioRecibosExtra->geraRecordSet( $rsRecibosExtra );
Sessao::write('arDados',$rsRecibosExtra);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioRecibosExtra.php" );
?>
