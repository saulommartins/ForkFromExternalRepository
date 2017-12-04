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
    * Data de Criação   : 11/11/2005

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30835 $
    $Name$
    $Autor:$
    $Date: 2007-07-04 15:13:07 -0300 (Qua, 04 Jul 2007) $

    * Casos de uso: uc-02.04.10
*/

/*
$Log$
Revision 1.8  2007/07/04 18:11:31  leandro.zis
Bug #9362#

Revision 1.7  2007/05/30 19:25:14  bruce
Bug #9116#

Revision 1.6  2006/07/05 20:39:48  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioExtratoBancario.class.php"  );

$obRRelatorio                          = new RRelatorio;
$obRTesourariaRelatorioExtratoBancario = new RTesourariaRelatorioExtratoBancario;

$stEntidade = "";
$arFiltro = Sessao::read('filtroRelatorio');

foreach ($arFiltro['inCodigoEntidadesSelecionadas'] as $key => $valor) {
    $stEntidade.= $valor . ",";
}

$stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );

$arCodPlano = array ();
if ($arFiltro['inCodContaBancoInicial']) {
    $arCodPlano[] = $arFiltro['inCodContaBancoInicial'];
}
if ($arFiltro['inCodContaBancoFinal']) {
    $arCodPlano[] = $arFiltro['inCodContaBancoFinal'];
}

$obRTesourariaRelatorioExtratoBancario->setCodPlano ( $arCodPlano );
$obRTesourariaRelatorioExtratoBancario->setExercicio($arFiltro['stExercicio']);
$obRTesourariaRelatorioExtratoBancario->setEntidade($stEntidade);
$obRTesourariaRelatorioExtratoBancario->setDataInicial($arFiltro['stDataInicial']);
$obRTesourariaRelatorioExtratoBancario->setDataFinal($arFiltro['stDataFinal']);
$obRTesourariaRelatorioExtratoBancario->boImprimeContasSemMov = ($arFiltro['stImprimirSemMovimentacao'] == 'sim');
$obRTesourariaRelatorioExtratoBancario->geraRecordSet( $rsExtratoBancario );

Sessao::write('arDados', $rsExtratoBancario);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioExtratoBancario.php" );

?>
