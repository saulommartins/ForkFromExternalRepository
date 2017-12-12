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
    * Página de Listagem de Borderos
    * Data de Criação   : 16/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 31732 $
    $Name$
    $Author: cako $
    $Date: 2007-04-30 16:21:28 -0300 (Seg, 30 Abr 2007) $

    * Casos de uso: uc-02.04.20,uc-02.03.28
*/

/*
$Log$
Revision 1.7  2007/04/30 19:21:27  cako
implementação uc-02.03.28

Revision 1.6  2007/03/30 21:58:02  cako
Bug #7884#

Revision 1.5  2006/07/05 20:39:28  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBordero";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$arFiltro = Sessao::read('filtro');
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg', $_GET['pg'] ? $_GET['pg'] : 0);
    Sessao::write('pos', $_GET['pos']? $_GET['pos'] : 0);
    Sessao::write('paginando', true);
} else {
    Sessao::write('pg', $_GET['pg']);
    Sessao::write('pos', $_GET['pos']);
    $_REQUEST['inCodConta'            ] = $arFiltro['inCodConta'            ];
    $_REQUEST['stDtInicial'           ] = $arFiltro['stDtInicial'           ];
    $_REQUEST['stDtFinal'             ] = $arFiltro['stDtFinal'             ];
    $_REQUEST['inCodigoBorderoInicial'] = $arFiltro['inCodigoBorderoInicial'];
    $_REQUEST['inCodigoBorderoFinal'  ] = $arFiltro['inCodigoBorderoFinal'  ];
    $_REQUEST['stExercicio'           ] = $arFiltro['stExercicio'           ];
    $_REQUEST['inCodEntidade'         ] = $arFiltro['inCodEntidade'         ];
    $_GET['stAcao'                    ] = $arFiltro['stAcao'                ];
}

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "imprimir";
}

$stEntidades = "";

foreach ($_REQUEST['inCodEntidade'] as $key => $valor) {
    $stEntidades .= $valor . ", ";
}

$stEntidades = substr($stEntidades, 0, strlen($stEntidades) - 2);

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->addBordero();

$obRTesourariaBoletim->roUltimoBordero->obROrcamentoEntidade->setCodigoEntidade($stEntidades);

$obRTesourariaBoletim->roUltimoBordero->setExercicio($_REQUEST['stExercicio']);
$obRTesourariaBoletim->roUltimoBordero->obRContabilidadePlanoBanco->setCodPlano($_REQUEST['inCodConta']);
$obRTesourariaBoletim->roUltimoBordero->setCodBorderoInicial($_REQUEST['inCodigoBorderoInicial']);
$obRTesourariaBoletim->roUltimoBordero->setCodBorderoFinal($_REQUEST['inCodigoBorderoFinal']);
$obRTesourariaBoletim->roUltimoBordero->setTimestampBorderoInicial($_REQUEST['stDtInicial']);
$obRTesourariaBoletim->roUltimoBordero->setTimestampBorderoFinal($_REQUEST['stDtFinal']);

$obRTesourariaBoletim->roUltimoBordero->listarDadosBordero( $rsLista );

$rsLista->addFormatacao("vl_liquido","NUMERIC_BR");

$obLista = new Lista;
$obLista->setTitulo( "Registros" );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entidade" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Borderô" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Banco / Ag. / Cta. Corr." );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Valor Borderô" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[bordero] / [exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[num_banco] / [num_agencia] / [conta_corrente]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_liquido" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addAcao();

$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodBordero"     , "cod_bordero"     );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"    , "cod_entidade"    );
$obLista->ultimaAcao->addCampo( "&stExercicio"      , "exercicio"       );
$obLista->ultimaAcao->addCampo( "&stTipoBordero"    , "tipo_bordero"    );

$pgProx = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
$stLink .= "&stCaminho=".CAM_GF_TES_INSTANCIAS."pagamentos/OCRelatorioBordero.php";
$obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();
?>
