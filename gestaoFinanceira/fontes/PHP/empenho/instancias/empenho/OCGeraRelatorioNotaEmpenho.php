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
    * Página de Relatório
    * Data de Criação   : 04/05/2009

    * @author Analista:      Tonismar Régis Bernardo
    * @author Desenvolvedor: Eduardo Paculski Schitz

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_EMP_MAPEAMENTO . 'TEmpenhoAutorizacaoEmpenho.class.php';

$preview = new PreviewBirt(2,10,10);
$preview->setVersaoBirt('2.5.0');
$preview->setFormato('pdf');

$preview->addParametro('exercicio',Sessao::getExercicio());

if ($_REQUEST['stCtrl'] == 'imprimirTodos') {
    $rsListaImpressao = Sessao::read('rsListaImpressao');
    if ($rsListaImpressao > -1) {
        $inCount = 0;
        $arEmpenhos = array();
        $arEntidades = array();
        while (!$rsListaImpressao->EOF()) {
            $arEmpenhos[$inCount] = $rsListaImpressao->getCampo('cod_empenho');
            if (!in_array($rsListaImpressao->getCampo('cod_entidade'), $arEntidades)) {
                $arEntidades[$inCount] = $rsListaImpressao->getCampo('cod_entidade');
            }
            $inCount++;
            $rsListaImpressao->proximo();
        }
        $preview->addParametro('cod_empenho', implode(',', $arEmpenhos));
        $preview->addParametro('cod_entidade', implode(',', $arEntidades));
    }
} else {
    $preview->addParametro('cod_empenho',$_REQUEST['inCodEmpenho']);
    $preview->addParametro('cod_entidade',$_REQUEST['inCodEntidade']);
}

$boReemitir = Sessao::read('reemitir');
if (isset($boReemitir) && $boReemitir == 't') {
    $preview->addParametro('bo_reemissao', 'true');
} else {
    $preview->addParametro('bo_reemissao', 'false');
}

$preview->preview();
?>
