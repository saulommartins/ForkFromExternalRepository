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
    * Página Oculto : AMF Demonstrativo 3
    * Data de Criação   : 28/09/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Schitz

    * $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDOIndicadores.class.php';

$preview = new PreviewBirt(6, 36, 41);
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);

$stAcao = $request->get('stAcao');

$stExercicio = $_REQUEST['stExercicio'];
$obTLDOIndicadores = new TLDOIndicadores;
for ($inCount = 0; $inCount <= 3; $inCount++) {
    if ($inCount != 3) {
        $stExercicioFiltro = $stExercicio + $inCount;

        $stFiltro  = " WHERE exercicio = '".$stExercicioFiltro."'";
        $stFiltro .= '   AND cod_tipo_indicador = '.$_REQUEST['inCodInflacao'];
        $obTLDOIndicadores->recuperaTodos($rsIndicadores, $stFiltro);
        if ($rsIndicadores->getNumLinhas() < 1) {
            SistemaLegado::alertaAviso('FLModelosAMF.php?'.Sessao::getId().'&stAcao='.$stAcao, 'Não existe Inflação cadastrado para o exercício '.$stExercicioFiltro.'!','','aviso', Sessao::getId(), '../');
        }
    }

    if ($inCount != 0) {
        $stExercicioFiltro = $stExercicio - $inCount;

        $stFiltro  = " WHERE exercicio = '".$stExercicioFiltro."'";
        $stFiltro .= '   AND cod_tipo_indicador = '.$_REQUEST['inCodInflacao'];
        $obTLDOIndicadores->recuperaTodos($rsIndicadores, $stFiltro);
        if ($rsIndicadores->getNumLinhas() < 1) {
            die;
            SistemaLegado::alertaAviso('FLModelosAMF.php?'.Sessao::getId().'&stAcao='.$stAcao, 'Não existe Inflação cadastrado para o exercício '.$stExercicioFiltro.'!','','aviso', Sessao::getId(), '../');
        }
    }

}

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado('exercicio'   , Sessao::getExercicio());
$obTOrcamentoEntidade->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
$obTOrcamentoEntidade->recuperaEntidades($rsEntidade, "AND e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")");

if (preg_match("/PREFEITURA.*/i", strtoupper($rsEntidade->getCampo('nom_cgm'))) || (count($_REQUEST['inCodEntidade']) > 1)) {
    $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));
    $preview->addParametro('poder'       , 'Executivo');
} elseif (preg_match("/CÂMARA.*/i", strtoupper($rsEntidade->getCampo('nom_cgm'))) || preg_match( "/CAMARA.*/i", strtoupper($rsEntidade->getCampo('nom_cgm')))) {
    $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));
    $preview->addParametro('poder'       , 'Legislativo');
}

$preview->addParametro('ano_referencia', $_REQUEST['stExercicio']);
$preview->addParametro('exercicio'     , $_REQUEST['stExercicio']);
$preview->addParametro('cod_inflacao'  , $_REQUEST['inCodInflacao']);
$preview->addParametro('cod_ppa'       , $_REQUEST['inCodPPA']);
$preview->addParametro('cod_entidade'  , implode(',', $_REQUEST['inCodEntidade']));

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
