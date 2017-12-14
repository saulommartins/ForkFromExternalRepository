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
    * Página Oculto : AMF Demonstrativo 2
    * Data de Criação   : 30/06/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDOIndicadores.class.php';

$stAcao = $request->get('stAcao');

$preview = new PreviewBirt(6, 36, 38);
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);

$inCodEntidade = $request->get('inCodEntidade');

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado('exercicio', Sessao::getExercicio());
$obTOrcamentoEntidade->recuperaEntidades($rsEntidade, "AND e.cod_entidade in (".implode(',',$inCodEntidade).")");

$stExercicioAnterior = $request->get('stExercicio') - 2;
$obTLDOIndicadores = new TLDOIndicadores;
$stFiltro  = " WHERE exercicio = '".$stExercicioAnterior."'";
$stFiltro .= "   AND cod_tipo_indicador = ".$request->get('inCodPIB');
$obTLDOIndicadores->recuperaTodos($rsIndicadores, $stFiltro);
if ($rsIndicadores->getNumLinhas() < 1) {
    SistemaLegado::alertaAviso("FLModelosAMF.php?".Sessao::getId()."&stAcao=".$stAcao, 'Não existe PIB cadastrado para o exercício '.$stExercicioAnterior.'!',"","aviso", Sessao::getId(), "../");
}

$preview->addParametro('cod_entidade'  , implode(',', $inCodEntidade));
$preview->addParametro('stExercicio'   , $stExercicioAnterior);
$preview->addParametro('ano_referencia', $request->get('stExercicio'));
$preview->addParametro('cod_pib'       , $request->get('inCodPIB'));

if (preg_match("/prefeitura.*/i", $rsEntidade->getCampo('nom_cgm')) || (count($inCodEntidade) > 1)) {
    $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));
    $preview->addParametro('poder'       , 'Executivo');
} elseif (preg_match("/câmara.*/i", $rsEntidade->getCampo('nom_cgm')) || preg_match( "/camara.*/i", $rsEntidade->getCampo('nom_cgm'))) {
    $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));
    $preview->addParametro('poder'       , 'Legislativo');
}

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
