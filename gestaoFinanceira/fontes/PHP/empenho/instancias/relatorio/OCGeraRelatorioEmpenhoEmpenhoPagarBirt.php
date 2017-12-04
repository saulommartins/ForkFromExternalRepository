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
 * Página de Oculto para gerar relatório birt de Empenhos à Ppagar
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar R. Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * $Id:$
 */

/* includes de sistema */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
/* includes de classes */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(2,10,8);
$preview->setTitulo('Relatório Empenhos a Pagar');
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);

if (count($_REQUEST['inCodEntidade']) > 1) {
    $stWhere = "where exercicio= '".Sessao::getExercicio()."' and parametro='cod_entidade_prefeitura'";
    $inCodEntidade = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stWhere);
    $preview->addParametro('entidade', $inCodEntidade);
} else {
    $preview->addParametro('entidade', $_REQUEST['inCodEntidade'][0]);
}

$arEntidadesAux = array();
$arEntidades = Sessao::read('arNomeEntidades');
if (is_array($arEntidades)) {
    foreach ($arEntidades as $inChave => $stEntidade) {
        if (array_search($inChave, $_REQUEST['inCodEntidade']) !== false) {
            $arEntidadesAux[] = $stEntidade;
        }
    }
}

$stNomeEntidade = implode("<br/>", $arEntidadesAux);
$stCodEntidades = implode(', ', $_REQUEST['inCodEntidade']);

$preview->addParametro('cod_entidade_empenho', $stCodEntidades);
$preview->addParametro('nome_entidade', $stNomeEntidade);
$preview->addParametro('exercicio', Sessao::getExercicio());
$preview->addParametro('exercicio_empenho', $_REQUEST['inExercicio']);

if ($_REQUEST['inCodEmpenhoInicial'] != "") {
    $preview->addParametro('cod_empenho_inicial', $_REQUEST['inCodEmpenhoInicial']);
} else {
    $preview->addParametro('cod_empenho_inicial', '');
}
if ($_REQUEST['inCodEmpenhoFinal'] != "") {
    $preview->addParametro('cod_empenho_final', $_REQUEST['inCodEmpenhoFinal']);
} else {
    $preview->addParametro('cod_empenho_final', '');
}

if ($_REQUEST['inCodFornecedor'] != "") {
    $preview->addParametro('cgm', $_REQUEST['inCodFornecedor']);
} else {
    $preview->addParametro('cgm', '');
}

if ($_REQUEST['inNumOrgao'] != "") {
    $preview->addParametro('num_orgao', $_REQUEST['inNumOrgao']);
} else {
    $preview->addParametro('num_orgao', '');
}

if ($_REQUEST['stDataInicial'] != "") {
    $preview->addParametro('data_inicial', $_REQUEST['stDataInicial']);
} else {
    $preview->addParametro('data_inicial', '');
}

if ($_REQUEST['stDataFinal'] != "") {
    $preview->addParametro('data_final', $_REQUEST['stDataFinal']);
} else {
    $preview->addParametro('data_final', '');
}

if ($_REQUEST['stDataSituacao'] != "") {
    $preview->addParametro('data_situacao', $_REQUEST['stDataSituacao']);
} else {
    $preview->addParametro('data_situacao', '');
}

if (trim($_REQUEST['inCodRecurso']) != "") {
    $preview->addParametro('cod_recurso', trim($_REQUEST['inCodRecurso']));
} else {
    $preview->addParametro('cod_recurso', '');
}

if ($_REQUEST['inCodUso'] != '' && $_REQUEST['inCodDestinacao'] != '' && $_REQUEST['inCodEspecificacao'] != '') {
    $preview->addParametro('destinacao_recurso', $_REQUEST['inCodUso'].".".$_REQUEST['inCodDestinacao'].".".$_REQUEST['inCodEspecificacao']);
} else {
    $preview->addParametro('destinacao_recurso', '');
}

if ($_REQUEST['inOrdenacao'] != "") {
   $preview->addParametro('ordenacao', $_REQUEST['ordenacao']);
} else {
    $preview->addParametro('ordenacao', '');
}

if ($_REQUEST['stMostrarCodigoRecurso'] == 'S') {
    $preview->addParametro('mostrar_cod_recurso', 'mostrar');
} else {
    $preview->addParametro('mostrar_cod_recurso', '');
}

if ($_REQUEST['stMostrarDescricaoRecurso'] == 'S') {
    $preview->addParametro('mostrar_desc_recurso', 'mostrar');
} else {
    $preview->addParametro('mostrar_desc_recurso', '');
}

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();

?>
