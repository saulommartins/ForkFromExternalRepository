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
    * PÃ¡gina de Relatório RREO Anexo1
    * Data de Criação   : 14/11/2007

    * @author Tonismar Régis Bernardo

    * @ignore

    * Casos de uso : uc-06.01.01

    $Id: OCGeraAMFDemonstrativo5.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once CAM_GPC_STN_MAPEAMENTO.'TSTNRecursoRREOAnexo14.class.php';

 # CADASTRA OS RECURSOS NA TABLE stn.recurso_rreo_anexo_14

//instancia a classe
$obTSTN = new TSTNRecursoRREOAnexo14();

//remove todos os recursos do exercício
$obTSTN->setDado('exercicio',Sessao::getExercicio());
$obTSTN->exclusao();

//insere os recursos selecionados na base
$arValores = Sessao::read('arValores');
for ($i=0;$i<count($arValores);$i++) {
    $obTSTN->setDado('cod_recurso',$arValores[$i]['inCodRecurso']);
    $obTSTN->inclusao();

    $stCodRecurso .= $arValores[$i]['inCodRecurso'].',';
}

$preview = new PreviewBirt(6,36,30);
$preview->setTitulo('Origem e Aplicação dos Recursos Obtidos com a Alienação de Ativos');
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado('exercicio', Sessao::getExercicio());
$obTOrcamentoEntidade->recuperaEntidades($rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")");

$preview->addParametro('cod_entidade', implode(',', $_REQUEST['inCodEntidade']));
$preview->addParametro('cod_recurso' , substr($stCodRecurso,0,-1));

if (count($_REQUEST['inCodEntidade']) == 1) {
    $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));
} else {
    $stCampo = 'valor';
    $stTabela = 'administracao.configuracao';
    $stFiltro  = " WHERE parametro = 'cod_entidade_prefeitura' ";
    $stFiltro .= "   AND exercicio = '".Sessao::getExercicio()."' ";
    $stFiltro .= "   AND cod_modulo = 8 ";
    $inCodEntidadePrefeitura = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);
    while (!$rsEntidade->eof()) {
        if ($rsEntidade->getCampo('cod_entidade') == $inCodEntidadePrefeitura) {
            $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));
        }
        $rsEntidade->proximo();
    }
}

$preview->addParametro('exercicio_referencia', $_REQUEST['stExercicio']);

if (preg_match("/prefeitura.*/", $rsEntidade->getCampo('nom_cgm')) || (count($_REQUEST['inCodEntidade']) > 1)) {
    $preview->addParametro('poder', 'Executivo');
} elseif (preg_match( "/câmara.*/", $rsEntidade->getCampo('nom_cgm'))) {
    $preview->addParametro('poder', 'Legislativo');
}

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
