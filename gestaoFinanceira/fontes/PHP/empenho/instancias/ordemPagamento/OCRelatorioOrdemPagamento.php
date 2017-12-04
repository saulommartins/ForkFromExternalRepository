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

    * $Id: OCRelatorioOrdemPagamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.03.05
                    uc-02.03.22
*/

/* includes de sistema */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/* includes de regra de negócio */
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioOrdemPagamento.class.php";

$obRRelatorio = new RRelatorio;
$obRegra = new REmpenhoRelatorioOrdemPagamento;

$arFiltro = Sessao::read('filtroRelatorio');
$arRecordSetTodos = array();

switch ($arFiltro['stCtrl']) {
case 'imprimirTodos':
    $rsListaImpressao = Sessao::read('rsListaImpressao');
    while (!$rsListaImpressao->eof()) {

        $obRegra->setCodOrdem   ($rsListaImpressao->getCampo('cod_ordem'));
        $obRegra->setExercicio  ($rsListaImpressao->getCampo('exercicio'));
        $obRegra->setCodEntidade($rsListaImpressao->getCampo('cod_entidade'));
        $obRegra->setImplantado ($rsListaImpressao->getCampo('implantado'));
        $obRegra->setExercicioEmpenho($rsListaImpressao->getCampo('exercicio_empenho'));
        $obRegra->geraRecordSet($arRecordSet);
        $arRecordSetTodos[] = $arRecordSet;

        $rsListaImpressao->proximo();
    }
    break;

default:
    $obRegra->setCodOrdem   ($arFiltro['inCodigoOrdem']);
    $obRegra->setExercicio  ($arFiltro['stExercicio']);
    $obRegra->setCodEntidade($arFiltro['inCodEntidade']);
    $obRegra->setImplantado ($arFiltro['boImplantado']);
    $obRegra->setExercicioEmpenho($arFiltro['stExercicioEmpenho']);
    $obRegra->geraRecordSet($arRecordSet);
    $arRecordSetTodos[] = $arRecordSet;
}

Sessao::write('inCodAcaoTMP', Sessao::read('acao'));
Sessao::write('rsRecordSet', $arRecordSetTodos);
$obRRelatorio->executaFrameOculto("OCGeraRelatorioOrdemPagamento.php");
