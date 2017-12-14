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
 * Formulario para o vinculo de impressora de cheques com o terminal
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_TES_NEGOCIO . 'RTesourariaImpressoraCheque.class.php';

$stAcao = $request->get('stAcao');

switch ($stAcao) {

case 'incluir' :
    
    $obRTesourariaImpressoraCheque = new RTesourariaImpressoraCheque();
    $obRTesourariaImpressoraCheque->recuperaCodigoTimestampTerminal(Sessao::read('numCgm'));
    
    $obRTesourariaImpressoraCheque->removeImpressoraTerminal();

    $obRTesourariaImpressoraCheque->inCodImpressora = $request->get('inCodImpressora');
    
    $obErro = $obRTesourariaImpressoraCheque->insertImpressoraTerminal();

    if ( !$obErro->ocorreu() ) {
        SistemaLegado::alertaAviso("FMVincularImpressoraCheque.php?".Sessao::getId()."&stAcao=incluir","Impressora vinculada com sucesso","incluir","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
    }

    break;

}

?>