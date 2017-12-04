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
    * Data de Criação   : 07/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * $Id: OCRelatorioNotaLiquidacaoEmpenho.php 60003 2014-09-25 12:51:26Z michel $

    * Casos de uso: uc-02.03.21
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include CAM_FW_PDF."RRelatorio.class.php";
include CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioNotaLiquidacaoEmpenho.class.php";

$obRRelatorio = new RRelatorio;
$obRegra = new REmpenhoRelatorioNotaLiquidacaoEmpenho;

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

switch ($arFiltroRelatorio['stCtrl']) {
case 'imprimirTodos':
    $arRecordSetTodos = array();
    $rsListaImpressao = Sessao::read('rsListaImpressao');

    while (!$rsListaImpressao->eof()) {
        if ($rsListaImpressao->getCampo('exercicio_nota') != "") {
            $obRegra->setExercicio($rsListaImpressao->getCampo('exercicio_nota'));
        } else {
            $obRegra->setExercicio(Sessao::getExercicio());
        }

        $obRegra->setCodEntidade($rsListaImpressao->getCampo('cod_entidade'));
        $obRegra->setCodNota    ($rsListaImpressao->getCampo('cod_nota'));
        $obRegra->setImplantado ('');
        $obRegra->setExercicioEmpenho($rsListaImpressao->getCampo('exercicio'));

        $obRegra->geraRecordSet($arRecordSet);
        $arRecordSetTodos[] = $arRecordSet;

        $rsListaImpressao->proximo();
    }
    break;

default:

    if ($arFiltroRelatorio['stExercicioNota']) {
        $obRegra->setExercicio($arFiltroRelatorio['stExercicioNota']);
    } else {
        $obRegra->setExercicio(Sessao::getExercicio());
    }

    $obRegra->setCodEntidade($arFiltroRelatorio['inCodEntidade']);
    $obRegra->setCodNota    ($arFiltroRelatorio['inCodNota']);
    $obRegra->setImplantado ($arFiltroRelatorio['boImplantado']);
    $obRegra->setExercicioEmpenho($arFiltroRelatorio['dtExercicioEmpenho']);

    $obRegra->geraRecordSet($arRecordSet);
    $arRecordSetTodos[] = $arRecordSet;

}

Sessao::write('arRecordSet', $arRecordSetTodos);
$obRRelatorio->executaFrameOculto("OCGeraRelatorioNotaLiquidacaoEmpenho.php");

SistemaLegado::liberaFrames(true,true);
?>
