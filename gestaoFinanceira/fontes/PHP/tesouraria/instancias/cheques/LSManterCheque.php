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

$obController->listar($rsCheque, $_REQUEST);

$obLista = new Lista;
$obLista->setRecordSet($rsCheque         );
$obLista->setTitulo   ('Lista de Cheques');

$obLista->addCabecalho                (        );
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth   (5       );
$obLista->commitCabecalho             (        );

$obLista->addCabecalho                (               );
$obLista->ultimoCabecalho->addConteudo('Número Cheque');
$obLista->ultimoCabecalho->setWidth   (15             );
$obLista->commitCabecalho             (               );

$obLista->addCabecalho                (       );
$obLista->ultimoCabecalho->addConteudo('Banco');
$obLista->ultimoCabecalho->setWidth   (40     );
$obLista->commitCabecalho             (       );

$obLista->addCabecalho                (                );
$obLista->ultimoCabecalho->addConteudo('Conta Corrente');
$obLista->ultimoCabecalho->setWidth   (20              );
$obLista->commitCabecalho             (                );

$obLista->addCabecalho                (                );
$obLista->ultimoCabecalho->addConteudo('Emitido'       );
$obLista->ultimoCabecalho->setWidth   (10              );
$obLista->commitCabecalho             (                );

$obLista->addCabecalho                (      );
$obLista->ultimoCabecalho->addConteudo('Ação');
$obLista->ultimoCabecalho->setWidth   (5     );
$obLista->commitCabecalho             (      );

$obLista->addDado                   (            );
$obLista->ultimoDado->setAlinhamento('ESQUERDA'  );
$obLista->ultimoDado->setCampo      ('num_cheque');
$obLista->commitDado                (            );

$obLista->addDado                   (                           );
$obLista->ultimoDado->setAlinhamento('ESQUERDA'                 );
$obLista->ultimoDado->setCampo      ('[num_banco] - [nom_banco]');
$obLista->commitDado                (                           );

$obLista->addDado                   (                    );
$obLista->ultimoDado->setAlinhamento('ESQUERDA'          );
$obLista->ultimoDado->setCampo      ('num_conta_corrente');
$obLista->commitDado                (                    );

$obLista->addDado                   (                    );
$obLista->ultimoDado->setAlinhamento('CENTRO'            );
$obLista->ultimoDado->setCampo      ('emitido'           );
$obLista->commitDado                (                    );

$obLista->addAcao             (                                      );
$obLista->ultimaAcao->setAcao ($stAcao                               );
$obLista->ultimaAcao->addCampo('&inCodBanco'   , 'cod_banco'         );
$obLista->ultimaAcao->addCampo('inCodAgencia'  , 'cod_agencia'       );
$obLista->ultimaAcao->addCampo('inCodConta'    , 'cod_conta_corrente');
$obLista->ultimaAcao->addCampo('stNumCheque'   , 'num_cheque'        );
$obLista->ultimaAcao->addCampo('&stDescQuestao', 'num_cheque'        );
if ($stAcao == 'excluir') {
    $obLista->ultimaAcao->setLink (CAM_GF_TES_INSTANCIAS . 'cheques/PRManterCheque.php' . '?stAcao=' . $stAcao . '&' . Sessao::getId() );
} elseif ($stAcao == 'emitir') {
    $obLista->ultimaAcao->setLink (CAM_GF_TES_INSTANCIAS . 'cheques/FMManterEmitirCheque.php' . '?stAcao=' . $stAcao . '&' . Sessao::getId() );
} else {
    $obLista->ultimaAcao->setLink (CAM_GF_TES_INSTANCIAS . 'cheques/FMManterConsultarCheque.php' . '?stAcao=' . $stAcao . '&' . Sessao::getId() );
}
$obLista->commitAcao();

if ($stAcao == 'consultar') {
    $obLista->addAcao              (                                              );
    $obLista->ultimaAcao->setAcao  ('imprimir'                                    );
    $obLista->ultimaAcao->addCampo ('&cod_banco'   , 'cod_banco'                  );
    $obLista->ultimaAcao->addCampo ('cod_agencia'  , 'cod_agencia'                );
    $obLista->ultimaAcao->addCampo ('cod_conta_corrente'    , 'cod_conta_corrente');
    $obLista->ultimaAcao->addCampo ('num_cheque'   , 'num_cheque'                 );
    $obLista->ultimaAcao->addCampo ('emitido'      , 'emitido'                    );
    $obLista->ultimaAcao->setLink  ('PRManterCheque.php?stAcao=' . $stAcao . '&' . Sessao::getId() . '&cod_terminal=' . $_REQUEST['inCodTerminal'] . '&timestamp_terminal=' . $_REQUEST['stTimestampTerminal'] );
    $obLista->ultimaAcao->setLinkId('impressao');
    $obLista->commitAcao();
}

$obLista->show();

$jsOnload = '
    //percorre todos as links das imagens e coloca um listener
    jq("td.botao a[id^=impressao]").each(function () {
                            //substitui o href original por um # para não
                            var href = jq(this).attr("href");
                            jq(this).removeAttr("href");
                            if (href.match("emitido=Sim") != null) {
                                href = href.substring(href.search(/\?/),href.search(/\',/));
                                arHref= href.split("&");
                                jQuery.each(arHref, function () {
                                                        href = href + "&"
                                                    });
                                var method = "ajaxJavaScript(\'OCManterEmitirCheque.php?"+href+"\',\'imprimirCheque\');";
                                //adiciona o estilo para o cursor ficar como link
                                jq(this).css("cursor","pointer");
                                //para cada acao chama o metodo imprimirCheque
                                jq(this).click(function () {
                                                    confirmPopUp(
                                                                  "Imprimir Cheque",
                                                                  "Certifique-se de que a folha de cheque esteja inserida na impressora. <br/><br/>Deseja prosseguir com a impressão?",
                                                                  method);
                                               });
                            } else {
                                jq(this).click(function () {
                                                    alertPopUp(
                                                              "Cheque não emitido",
                                                              "Este cheque não pode ser impresso porque ainda não foi emitido",
                                                              "");
                                                          });
                            }
                     });
';

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
