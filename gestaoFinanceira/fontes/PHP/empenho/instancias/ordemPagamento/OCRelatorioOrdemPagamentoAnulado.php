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
    * Data de Criação   : 18/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 31627 $
    $Name$
    $Autor: $
    $Date: 2006-09-28 06:56:56 -0300 (Qui, 28 Set 2006) $

    * Casos de uso: uc-02.03.05
                    uc-02.03.25
*/

/*
$Log$
Revision 1.7  2006/09/28 09:51:34  eduardo
Bug #7060#

Revision 1.6  2006/07/14 16:01:19  jose.eduardo
Bug #6525#

Revision 1.5  2006/07/11 13:39:58  cako
Bug #6525#

Revision 1.4  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                          );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioOrdemPagamentoAnulado.class.php" );

$obRRelatorio  = new RRelatorio;
$obRegra       = new REmpenhoRelatorioOrdemPagamentoAnulado;
$arRecordSet   = new RecordSet;
$arFiltro = Sessao::read('filtroRelatorio');

$obRegra->setExercicio      ( Sessao::getExercicio() );
$obRegra->setCodOrdem       ( $arFiltro['inCodigoOrdem']      );
$obRegra->setExercicioOrdem ( $arFiltro['stExercicioOrdem']   );
$obRegra->setCodEntidade    ( $arFiltro['inCodEntidade']   );
$obRegra->setImplantado     ( $arFiltro['boImplantado']       );
$obRegra->setTimestamp      ( $arFiltro['stTimestampAnulado'] );

$obRegra->geraRecordSet( $arRecordSet );
Sessao::write('rsRecordSet', $arRecordSet);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioOrdemPagamentoAnulado.php" );
?>
