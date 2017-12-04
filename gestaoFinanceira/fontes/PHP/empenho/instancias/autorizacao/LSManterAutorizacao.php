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
    * Página de Listagem de Itens
    * Data de Criação   : 04/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31087 $
    $Name$
    $Autor:$
    $Date: 2007-02-23 13:15:05 -0200 (Sex, 23 Fev 2007) $

    * Casos de uso: uc-02.03.02
                    uc-02.03.19
                    uc-02.03.20
                    uc-02.01.08
*/

/*
$Log$
Revision 1.11  2007/02/23 15:15:05  gelson
Sempre que for autorização tem que ir a reserva. Adicionado em todos arquivos o caso de uso da reserva.

Revision 1.10  2006/07/05 20:47:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho   = CAM_GF_EMP_INSTANCIAS."empenho/autorizacao/";

$obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

if ($stAcao == "imprimirAN") {
    $stAcao = "reemitir";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'anular'   : $pgProx = 'FMAnularAutorizacao.php'; break;
    DEFAULT         : $pgProx = $pgForm;
}

if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    
    $inPg = $_GET['pg'] ? $_GET['pg'] : 0;
    $inPos = $_GET['pos']? $_GET['pos'] : 0;
    Sessao::write('paginando', true);
    Sessao::write('filtro', $arFiltro);
} else {
    $inPg = $_GET['pg'];
    $inPos = $_GET['pos'];
    $arFiltro = Sessao::read('filtro');
    $_REQUEST['inCodAutorizacaoInicial' ] = $arFiltro['inCodAutorizacaoInicial' ];
    $_REQUEST['inCodAutorizacaoFinal'   ] = $arFiltro['inCodAutorizacaoFinal'   ];
    $_REQUEST['stDtInicial'             ] = $arFiltro['stDtInicial'             ];
    $_REQUEST['stDtFinal'               ] = $arFiltro['stDtFinal'               ];
    $_REQUEST['inCodEntidade'           ] = $arFiltro['inCodEntidade'           ];
    $_REQUEST['inCodDespesa'            ] = $arFiltro['inCodDespesa'            ];
    $_REQUEST['inCodFornecedor'         ] = $arFiltro['inCodFornecedor'         ];
    $_REQUEST['stExercicio'             ] = $arFiltro['stExercicio'             ];
}

Sessao::write('pg', $inPg);
Sessao::write('pos', $inPos);

if (is_array($_REQUEST['inCodEntidade']) ) {
    foreach ($_REQUEST['inCodEntidade'] as $value) {
        $stCodEntidade .= $value . " , ";
    }
}

$stCodEntidade = substr($stCodEntidade,0,strlen($stCodEntidade)-2);

$obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $_REQUEST['inCodDespesa'] );
$obREmpenhoAutorizacaoEmpenho->setExercicio( $_REQUEST['stExercicio'] );
$obREmpenhoAutorizacaoEmpenho->setCodAutorizacaoInicial( $_REQUEST['inCodAutorizacaoInicial'] );
$obREmpenhoAutorizacaoEmpenho->setCodAutorizacaoFinal  ( $_REQUEST['inCodAutorizacaoFinal'  ] );
$obREmpenhoAutorizacaoEmpenho->setDtAutorizacaoInicial( $_REQUEST['stDtInicial'] );
$obREmpenhoAutorizacaoEmpenho->setDtAutorizacaoFinal( $_REQUEST['stDtFinal'] );
$obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade );
$obREmpenhoAutorizacaoEmpenho->obRCGM->setNumCGM( $_REQUEST['inCodFornecedor'] );

$obREmpenhoAutorizacaoEmpenho->setBoEmpenhoCompraLicitacao( true );
$obREmpenhoAutorizacaoEmpenho->setCodModalidadeCompra( $_REQUEST['inCodModalidadeCompra'] );
$obREmpenhoAutorizacaoEmpenho->setCompraInicial( $_REQUEST['inCompraInicial'] );
$obREmpenhoAutorizacaoEmpenho->setCompraFinal( $_REQUEST['inCompraFinal'] );
$obREmpenhoAutorizacaoEmpenho->setCodModalidadeLicitacao( $_REQUEST['inCodModalidadeLicitacao'] );
$obREmpenhoAutorizacaoEmpenho->setLicitacaoInicial( $_REQUEST['inLicitacaoInicial'] );
$obREmpenhoAutorizacaoEmpenho->setLicitacaoFinal( $_REQUEST['inLicitacaoFinal'] );

if (Sessao::getExercicio() > '2015') {
    $obREmpenhoAutorizacaoEmpenho->setCentroCusto ( $_REQUEST['inCentroCusto']);
}

if ($stAcao == 'alterar') {
    $obREmpenhoAutorizacaoEmpenho->setAlterar( true );
}

if ($stAcao == 'imprimir') {
    $obREmpenhoAutorizacaoEmpenho->listarTodos( $rsLista );
} elseif ($stAcao == 'reemitir') {
    $obREmpenhoAutorizacaoEmpenho->listarReemitirAnulados( $rsLista );
    $rsLista->addFormatacao("valor","NUMERIC_BR");
} else {
    $obREmpenhoAutorizacaoEmpenho->listar( $rsLista );
}

$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
}
$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Autorização");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
if ($stAcao == 'reemitir') {
    $obLista->ultimoCabecalho->addConteudo("Data Anulação");
} else {
    $obLista->ultimoCabecalho->addConteudo("Data da Autorização");
}
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
if ($stAcao == 'reemitir') {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Credor");
    $obLista->ultimoCabecalho->setWidth( 45 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor ");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
} else {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Descrição ");
    $obLista->ultimoCabecalho->setWidth( 45 );
    $obLista->commitCabecalho();
}
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_autorizacao]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
if ($stAcao == 'reemitir') {
    $obLista->ultimoDado->setCampo( "dt_anulacao" );
} else {
    $obLista->ultimoDado->setCampo( "dt_autorizacao" );
}
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
if ($stAcao == 'reemitir') {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
} else {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "descricao" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
}
$obLista->addAcao();
    if ($stAcao == 'reemitir' || $stAcao == 'imprimir') {
    $obLista->ultimaAcao->setAcao( 'reemitir' );
} else
    if ($stAcao == 'alterar') {
    $obLista->ultimaAcao->setAcao( 'Alterar' );
} else {
    $obLista->ultimaAcao->setAcao( 'Anular' );
}

$obLista->ultimaAcao->addCampo( "&inCodAutorizacao" , "cod_autorizacao" );
$obLista->ultimaAcao->addCampo( "&stExercicio"      , "exercicio"       );
$obLista->ultimaAcao->addCampo( "&inCodPreEmpenho"  , "cod_pre_empenho" );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"    , "cod_entidade"    );
$obLista->ultimaAcao->addCampo( "&inCodReserva"     , "cod_reserva"     );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("&stDescQuestao", "descricao");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $stLink .= "&stCaminho=".CAM_GF_EMP_INSTANCIAS."autorizacao/OCGeraRelatorioAutorizacao.php?";
    if ($_REQUEST['stAcao'] == 'imprimirAN') {
        $pgProx = CAM_GF_EMP_INSTANCIAS."autorizacao/OCGeraRelatorioAutorizacao.php";
    } else
    if ($_REQUEST['stAcao'] == 'imprimir') {
        $stLink .= 'stAcao=autorizacao&';
    } else {
        $stLink .= 'stAcao=anulacao&';
    }
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();
if (in_array($_REQUEST['stAcao'],array('imprimir','reemitir'))) {
echo <<<JS
    <script type="text/javascript">
    jq('td.botao a').each(function () {
        jq(this).attr('target','oculto');
        newHref = ((this.href).split('stCaminho='))[1];
        newHref = newHref.substr(0,newHref.length-15);
        jq(this).attr('href',newHref);
    });

    jq('td.botaoEscuro a').each(function () {
        jq(this).attr('target','oculto');
        newHref = ((this.href).split('stCaminho='))[1];
        newHref = newHref.substr(0,newHref.length-15);
        jq(this).attr('href',newHref);
    });
    </script>
JS;
}
?>
