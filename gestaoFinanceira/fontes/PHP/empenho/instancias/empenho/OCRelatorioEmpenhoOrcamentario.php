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

    * $Id: OCRelatorioEmpenhoOrcamentario.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.03.03
                    uc-02.03.17
*/
/* include de sistema */
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/* include de regra de negocio */
include CAM_FW_PDF."RRelatorio.class.php";
include CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioNotaEmpenho.class.php";

$obRRelatorio  = new RRelatorio;
$obREmpenhoRelatorioNotaEmpenho = new REmpenhoRelatorioNotaEmpenho;

$arFiltros = Sessao::read('filtroRelatorio');

switch ($arFiltros['stCtrl']) {
case 'imprimirTodos':
    $arRecordSetTodos = array();
    $rsListaImpressao = Sessao::read('rsListaImpressao');

    while (!$rsListaImpressao->eof()) {

        if ($rsListaImpressao->getCampo('exercicio') != '') {
            $obREmpenhoRelatorioNotaEmpenho->setExercicio($rsListaImpressao->getCampo('exercicio'));
        } else {
            $obREmpenhoRelatorioNotaEmpenho->setExercicio(Sessao::getExercicio());
        }

        $obREmpenhoRelatorioNotaEmpenho->setCodEntidade($rsListaImpressao->getCampo('cod_entidade'));
        $obREmpenhoRelatorioNotaEmpenho->setCodEmpenho($rsListaImpressao->getCampo('cod_empenho'));
        $obREmpenhoRelatorioNotaEmpenho->setCodDespesaFixa('');
        $obREmpenhoRelatorioNotaEmpenho->geraRecordSet($arRecordSet);
        $arRecordSetTodos[] = $arRecordSet;

        $rsListaImpressao->proximo();
    }

    break;
default:
    if ($arFiltros['stExercicioEmpenho']) {
        $obREmpenhoRelatorioNotaEmpenho->setExercicio($arFiltros['stExercicioEmpenho']);
    } else {
        $obREmpenhoRelatorioNotaEmpenho->setExercicio(Sessao::getExercicio());
    }

    $obREmpenhoRelatorioNotaEmpenho->setCodEntidade($arFiltros['inCodEntidade']);
    $obREmpenhoRelatorioNotaEmpenho->setCodEmpenho($arFiltros['inCodEmpenho']);
    $obREmpenhoRelatorioNotaEmpenho->setCodDespesaFixa($arFiltros['inCodDespesaFixa']);

    $obREmpenhoRelatorioNotaEmpenho->geraRecordSet($arRecordSet);
    $arRecordSetTodos[] = $arRecordSet;
}

Sessao::write('rsRecordSet', $arRecordSetTodos);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioEmpenhoOrcamentario.php" );
?>
