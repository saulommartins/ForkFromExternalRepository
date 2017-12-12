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
 * Lista das especificaçõs que não possuem contas contábeis
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Eduardo Schitz <eduardo.schitz@cnm.org.br>
 * $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoRecurso.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ContaDestinacao';
$pgProc = 'PR'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.php';

include_once( $pgJs );

$stCaminho   = CAM_GF_ORC_INSTANCIAS.'destinacaoRecursos/';

$obROrcamentoRecurso = new ROrcamentoRecurso;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = 'incluir';
}

$arFiltro = Sessao::read('filtro');
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro['filtro'][$stCampo] = $stValor;
    }
    $inPg = $_GET['pg'] ? $_GET['pg'] : 0;
    $inPos = $_GET['pos']? $_GET['pos'] : 0;
    $boPaginando = true;

    Sessao::write('filtro',$arFiltro);
    Sessao::write('pg',$inPg);
    Sessao::write('pos',$inPos);
    Sessao::write('paginando',$boPaginando);
} else {
    $inPg = $_GET['pg'];
    $inPos = $_GET['pos'];
    foreach ($arFiltro['filtro'] AS $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
}

$obROrcamentoRecurso->setExercicio(Sessao::getExercicio());

if ($_REQUEST['inCodEspecificacaoInicial'] != '') {
    $obROrcamentoRecurso->setCodEspecificacaoInicial($_REQUEST['inCodEspecificacaoInicial'] );
}

if ($_REQUEST['inCodEspecificacaoFinal'] != '') {
    $obROrcamentoRecurso->setCodEspecificacaoFinal($_REQUEST['inCodEspecificacaoFinal'] );
}

//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('telaPrincipal'); //oculto - telaPrincipal

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obROrcamentoRecurso->listarRecursoEspecificacoesSemConta($rsLista);
$obLista = new Lista;
$obLista->setMostraPaginacao(false);
$obLista->setMostraSelecionaTodos(true);
$obLista->setRecordSet($rsLista);
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Código');
$obLista->ultimoCabecalho->setWidth(7);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Descrição ');
$obLista->ultimoCabecalho->setWidth(60);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo('cod_especificacao');
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo('descricao');
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();
$obChkCriarConta = new CheckBox;
$obChkCriarConta->setName ('boCriarConta_[cod_especificacao]_');
$obChkCriarConta->setValue('true');

$obLista->addDadoComponente($obChkCriarConta);
$obLista->commitDadoComponente();

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);

$obFormulario->addHidden($obHdnAcao);
$obFormulario->addLista($obLista);

$obFormulario->OK();
$obFormulario->show();
?>
