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
    * Página de Formulário Oculto do Relatorio de Razão de Empenho
    * Data de Criação   : 02/06/2005

    * @author Analista:         Diego B. Victoria
    * @author Desenvolvedor:    Cleisson S. Barboza

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.14
*/

/*
$Log$
Revision 1.4  2006/07/05 20:48:34  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRazaoEmpenho.class.php");

$arFiltroRelatorio = Sessao::read('filtroRelatorio');
$obRRelatorio  = new RRelatorio;
$obRegra       = new REmpenhoRelatorioRazaoEmpenho;
$obRegra->setExercicio  ( $arFiltroRelatorio['stExercicio']    );
$obRegra->setCodEntidade( $arFiltroRelatorio['inCodEntidade']  );
$obRegra->setCodEmpenho ( $arFiltroRelatorio['inCodEmpenho']   );
$obRegra->setArItens      ( Sessao::read('arItens'));

$obRegra->geraRecordSet( $arRecordSet, $arRecordSet1, $arRecordSet2, $arRecordSet3, $arRecordSet4, $arRecordSet5, $arRecordSet6, $arRecordLinha );
Sessao::write('arRecordSet0', $arRecordSet);
Sessao::write('arRecordSet1', $arRecordSet1);
Sessao::write('arRecordSet2', $arRecordSet2);
Sessao::write('arRecordSet3', $arRecordSet3);
Sessao::write('arRecordSet4', $arRecordSet4);
Sessao::write('arRecordSet5', $arRecordSet5);
Sessao::write('arRecordSet6', $arRecordLinha);
Sessao::write('arRecordSet7', $arRecordSet6);
//exit();
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioEmpenhoRazaoEmpenho.php" );

?>
