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
include CAM_GF_TES_NEGOCIO . 'RTesourariaCheque.class.php';

$stAcao = ($_REQUEST['stAcao'] == '') ? 'selecionar' : $_REQUEST['stAcao'];

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

$obRTesourariaCheque = new RTesourariaCheque();
$obRTesourariaCheque->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco = $_GET['inCodBanco'   ];
$obRTesourariaCheque->obRMONContaCorrente->obRMONAgencia->stNumAgencia            = $_GET['stNumAgencia' ];
$obRTesourariaCheque->obRMONContaCorrente->stNumeroConta                          = $_GET['stNumeroConta'];
$obRTesourariaCheque->stTipoBusca                                                 = $_GET['stTipoBusca'  ];
$obRTesourariaCheque->listCheque($rsCheque,array('stTipoBusca' => $_GET['stTipoBusca']));

$stFncJavaScript .= " function insereReceita(num) {
                          var sNum;
                          sNum = num;
                          window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["stNomForm"].".".$_REQUEST["stCampoNum"].".value = sNum;
                          window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["stNomForm"].".".$_REQUEST["stCampoNum"].".focus();
                          window.close();
                      }";

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
$obLista->ultimoCabecalho->setWidth   (50     );
$obLista->commitCabecalho             (       );

$obLista->addCabecalho                (                );
$obLista->ultimoCabecalho->addConteudo('Conta Corrente');
$obLista->ultimoCabecalho->setWidth   (20              );
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

$obLista->addAcao                   (                             );
$obLista->ultimaAcao->setAcao       ($stAcao                      );
$obLista->ultimaAcao->setFuncao     (true                         );
$obLista->ultimaAcao->setLink       ("JavaScript:insereReceita();");
$obLista->ultimaAcao->addCampo      ('1'   , 'num_cheque'         );
$obLista->commitAcao();

$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

?>
