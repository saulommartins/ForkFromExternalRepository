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
 * Formulario de Emissão de Cheques
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

$pgOcul = 'OCManterEmitirCheque.php';

//Instancia o model e o controller
$obModel      = new RTesourariaCheque();
$obController = new CTesourariaCheque($obModel);

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

//Lista de acordo com o filtro
$obController->listChequesAnular($rsLista, $_REQUEST);
$rsLista->addFormatacao( "valor", "NUMERIC_BR" );

$obLista = new Lista;
// Nr. Cheque | banco | entidade | credor | tipo de emissão | valor
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nr. do Cheque");
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth(25);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Credor");
$obLista->ultimoCabecalho->setWidth(20);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Tipo de Emissão");
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo("num_cheque");
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo("nom_entidade");
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo("nom_credor");
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo("tipo_emissao");
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo("valor");
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ($stAcao                                    );
$obLista->ultimaAcao->addCampo('&inCodBanco'        , 'cod_banco'         );
$obLista->ultimaAcao->addCampo('&inCodAgencia'      , 'cod_agencia'       );
$obLista->ultimaAcao->addCampo('&inCodContaCorrente', 'cod_conta_corrente');
$obLista->ultimaAcao->addCampo('&stNumCheque'       , 'num_cheque'        );
$obLista->ultimaAcao->setLink ('PRManterCheque.php?stAcao=' . $stAcao . '&stTipoEmissaoCheque=' . $_REQUEST['stTipoPagamento'] . '&' . Sessao::getId());

$obLista->commitAcao();
$obLista->show();

$jsOnload = '
    //percorre todos as links das imagens e coloca um listener
    jq("td.botao a").each(function () {
                            //substitui o href original por um # para não
                            var href = jq(this).attr("href");
                            var method = "ajaxJavaScript(\'OCManterCheque.php?url="+href+"&stAcao=anular\',\'anular\');";
                            jq(this).removeAttr("href");
                            //adiciona o estilo para o cursor ficar como link
                            jq(this).css("cursor","pointer");
                            //para cada acao, chama a popup de confirmacao
                            jq(this).click(function () {
                                            confirmPopUp("Confirmar Anulação da Emissão"
                                                        ,"Deseja anular a emissão do cheque?"
                                                        ,method);
                                           });
                     });
';

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
