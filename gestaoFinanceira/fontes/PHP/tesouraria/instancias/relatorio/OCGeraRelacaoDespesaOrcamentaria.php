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
    * Arquivo que passa para o Birt as informações do relatório Relação de Despesa Orçamentária
    * Data de Criação   : 02/04/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Pacuslki Schitz

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(2,30,5);
$preview->setTitulo('Relação de Despesa Orçamentária');
$preview->setVersaoBirt('2.5.0');

$preview->addParametro("cod_entidade", implode(',', $_REQUEST['inCodEntidade']));

$preview->addParametro("data_inicial", $_REQUEST['stDataInicial']);
$preview->addParametro("data_final", $_REQUEST['stDataFinal']);

if ($_REQUEST['inCodDotacao'] != '') {
    $preview->addParametro('cod_dotacao', $_REQUEST['inCodDotacao']);
} else {
    $preview->addParametro('cod_dotacao', '');
}

if ($_REQUEST['inCodDespesa'] != '') {
    $preview->addParametro('cod_despesa', $_REQUEST['inCodDespesa']);
    $preview->addParametro('masc_classificacao', $_REQUEST['inCodDespesa']);
} else {
    $preview->addParametro('cod_despesa', '');
    $preview->addParametro('masc_classificacao', '');
}

if ($_REQUEST['inCodOrgao'] != '') {
    $preview->addParametro('cod_orgao', $_REQUEST['inCodOrgao']);
} else {
    $preview->addParametro('cod_orgao', '');
}

if ($_REQUEST['inCodUnidade'] != '') {
    $preview->addParametro('cod_unidade', $_REQUEST['inCodUnidade']);
} else {
    $preview->addParametro('cod_unidade', '');
}

if ($_REQUEST['inCodFuncao'] != '') {
    $preview->addParametro('cod_funcao', $_REQUEST['inCodFuncao']);
} else {
    $preview->addParametro('cod_funcao', '');
}

if ($_REQUEST['inCodSubFuncao'] != '') {
    $preview->addParametro('cod_subfuncao', $_REQUEST['inCodSubFuncao']);
} else {
    $preview->addParametro('cod_subfuncao', '');
}

if ($_REQUEST['inCodPrograma'] != '') {
    $preview->addParametro('cod_programa', $_REQUEST['inCodPrograma']);
} else {
    $preview->addParametro('cod_programa', '');
}

if ($_REQUEST['inCodPao'] != '') {
    $preview->addParametro('cod_pao', $_REQUEST['inCodPao']);
} else {
    $preview->addParametro('cod_pao', '');
}

if ($_REQUEST['inCodRecursoIni'] != '') {
    $preview->addParametro('cod_recurso_ini', $_REQUEST['inCodRecursoIni']);
} else {
    $preview->addParametro('cod_recurso_ini', '');
}

if ($_REQUEST['inCodRecursoFim'] != '') {
    $preview->addParametro('cod_recurso_fim', $_REQUEST['inCodRecursoFim']);
} else {
    $preview->addParametro('cod_recurso_fim', '');
}

if ($_REQUEST['inCodContaBancoInicial'] != '') {
    $preview->addParametro('cod_plano_ini', $_REQUEST['inCodContaBancoInicial']);
} else {
    $preview->addParametro('cod_plano_ini', '');
}

if ($_REQUEST['inCodContaBancoFinal'] != '') {
    $preview->addParametro('cod_plano_fim', $_REQUEST['inCodContaBancoFinal']);
} else {
    $preview->addParametro('cod_plano_fim', '');
}

$preview->addParametro('cod_ordenacao', $_REQUEST['inOrdenacao']);

$preview->preview();
?>
