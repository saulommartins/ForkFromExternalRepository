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
    * Página Oculto : AMF Demonstrativo 6
    * Data de Criação   : 27/06/2008

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Henrique Girardi dos Santos

    * @ignore

    * Casos de uso : uc-06.01.04

    * $Id: OCGeraAMFDemonstrativo6.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$stAcao = $request->get('stAcao');

$preview = new PreviewBirt(6, 36, 31);
$preview->setTitulo('Receitas e Despesas Previdenciárias do RPPS');
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado('exercicio', Sessao::getExercicio());
$obTOrcamentoEntidade->recuperaEntidades($rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")");

$preview->addParametro('entidade', implode(',', $_REQUEST['inCodEntidade']));

$inCount=0;
while (!$rsEntidade->eof()) {
    $stValor = SistemaLegado::pegaDado('valor', 'administracao.configuracao', " where exercicio= '".Sessao::getExercicio()."' and parametro= 'cod_entidade_rpps' and valor= '".$rsEntidade->getCampo('cod_entidade')."'");
    if ($stValor == "") {
        SistemaLegado::alertaAviso("FLModelosAMF.php?".Sessao::getId()."&stAcao=".$stAcao, $rsEntidade->getCampo('nom_cgm').' não é uma entidade RPPS',"","aviso", Sessao::getId(), "../");
    }
    $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));
    $rsEntidade->proximo();
    $inCount++;
}

if ($inCount > 1) {
    $preview->addParametro('nom_entidade', '');
}

$preview->addParametro('stExercicio', $_REQUEST['stExercicio']);

if (preg_match("/prefeitura.*/", $rsEntidade->getCampo('nom_cgm')) || (count($_REQUEST['inCodEntidade']) > 1)) {
    $preview->addParametro('poder', 'Executivo');
} elseif (preg_match("/câmara.*/", $rsEntidade->getCampo('nom_cgm'))) {
    $preview->addParametro('poder', 'Legislativo');
}

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
