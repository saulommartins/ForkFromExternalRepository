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
 * Lista de cheques
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_TES_CONTROLE . 'CTesourariaCheque.class.php';
include CAM_GF_TES_NEGOCIO . 'RTesourariaCheque.class.php';

$stAcao = $request->get('stAcao');

$pgOcul = 'OCManterCheque.php';

$arFiltro = Sessao::read('filtro');
if ($_POST OR $_GET['pg']) {
    foreach ($_REQUEST as $stCampo => $stValor) {
        $arFiltro['filtro'][$stCampo] = $stValor;
    }
    $boPaginando = true;

    Sessao::write('filtro',$arFiltro);
    Sessao::write('paginando',$boPaginando);
} else {
    foreach ($arFiltro['filtro'] AS $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
    $_GET['pg']  = $_REQUEST['pg' ];
    $_GET['pos'] = $_REQUEST['pos'];
}

//Instancia o model e o controller
$obModel      = new RTesourariaCheque();
$obController = new CTesourariaCheque($obModel);

$obController->listChequesBaixa($rsCheque, $_REQUEST);

$rsCheque->addFormatacao('valor','NUMERIC_BR');

$obLista = new Lista;
$obLista->setRecordSet($rsCheque         );
$obLista->setTitulo   ('Lista de Cheques a serem baixados');
$obLista->setMostraPaginacao(false);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Conta Crédito");
$obLista->ultimoCabecalho->setWidth(20);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Conta Débito");
$obLista->ultimoCabecalho->setWidth(20);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Credor");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_entidade" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_plano_credito] - [nom_conta_credito]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_plano_debito] - [nom_conta_debito]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_credor" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obChkCheque = new CheckBox();
$obChkCheque->setName ('transferencia_[cod_lote]_[cod_entidade]_[exercicio]_[tipo]_');
$obChkCheque->setValue('true');

$obLista->addDadoComponente($obChkCheque);
$obLista->commitDadoComponente();

$obLista->montaHTML();

//Cheque para selecionar todos os checkbox
$obChkTodos = new Checkbox;
$obChkTodos->setName                        ( "boTodos" );
$obChkTodos->setId                          ( "boTodos" );
$obChkTodos->setRotulo                      ( "Selecionar Todas" );
$obChkTodos->obEvento->setOnChange          ( "selecionarTodos();" );
$obChkTodos->montaHTML();

$obTabelaCheckbox = new Tabela;
$obTabelaCheckbox->addLinha();
$obTabelaCheckbox->ultimaLinha->addCelula();
$obTabelaCheckbox->ultimaLinha->ultimaCelula->setColSpan ( 2 );
$obTabelaCheckbox->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
$obTabelaCheckbox->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Selecionar Todos".$obChkTodos->getHTML()."&nbsp;</div>");
$obTabelaCheckbox->ultimaLinha->commitCelula();
$obTabelaCheckbox->commitLinha();

$obTabelaCheckbox->montaHTML();

$obHdnAcao = new Hidden();
$obHdnAcao->setName    ('stAcao');
$obHdnAcao->setId      ('stAcao');
$obHdnAcao->setValue   ($stAcao );

//Instancia um span no formulario para comportar a lista
$obSpnLista = new Span();
$obSpnLista->setId    ('spnLista');
$obSpnLista->setValue ($obLista->getHTML().$obTabelaCheckbox->getHTML());

//Instancia um formulario
$obFormulario = new Formulario();
$obFormulario->addHidden($obHdnAcao);

$obFormulario->addSpan  ($obSpnLista);

$obFormulario->Cancelar('FLManterChequeEmissaoBaixa.php');
$obFormulario->show();

include 'JSManterChequeEmissaoBaixa.js';

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
