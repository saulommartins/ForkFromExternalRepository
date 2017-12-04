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
    * Página de Listagem de Transferências
    * Data de Criação   : 09/11/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30835 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.09
*/

/*
$Log$
Revision 1.6  2006/07/05 20:40:06  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRTesourariaBoletim = new RTesourariaBoletim();

$arFiltro = array();

if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write( 'pg', $_GET['pg'] ? $_GET['pg'] : 0 );
    Sessao::write( 'pos', $_GET['pos']? $_GET['pos'] : 0 );
    Sessao::write( 'paginando', true );
} else {
    Sessao::write('pg', $_GET['pg']);
    Sessao::write('pos', $_GET['pos']);
    $_REQUEST['inNumCgm'           ] = $arFiltro['filtro']['inNumCgm'           ];
    $_REQUEST['inCodEntidade'      ] = $arFiltro['filtro']['inCodEntidade'      ];
    $_REQUEST['inCodTerminal'      ] = $arFiltro['filtro']['inCodTerminal'      ];
    $_REQUEST['stTimestampTerminal'] = $arFiltro['filtro']['stTimestampTerminal'];
    $_REQUEST['stTimestampUsuario' ] = $arFiltro['filtro']['stTimestampUsuario' ];
    $_REQUEST['stDataBoletim'      ] = $arFiltro['filtro']['stDataBoletim'      ];
    $_REQUEST['stNumeroBoletim'    ] = $arFiltro['filtro']['stNumeroBoletim'    ];
    $_REQUEST['stContaDebito'      ] = $arFiltro['filtro']['stContaDebito'      ];
    $_REQUEST['stContaCredito'     ] = $arFiltro['filtro']['stContaCredito'     ];
    $_GET['stAcao'                 ] = $arFiltro['filtro']['stAcao'             ];
}

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'incluir'  : $pgProx = $pgForm; break;
    case 'consultar': $pgProx = $pgForm; break;
    DEFAULT         : $pgProx = $pgForm;
}

if ( is_array( $_REQUEST['inCodEntidade'] ) ) {
    $stCodEntidade = implode( ',', $_REQUEST['inCodEntidade'] );
}

$obRTesourariaBoletim->setExercicio     ( Sessao::getExercicio()            );
$obRTesourariaBoletim->setCodBoletim    ( $_REQUEST['inNumeroBoletim']  );
$obRTesourariaBoletim->setDataBoletim   ( $_REQUEST['stDataBoletim']    );
$obRTesourariaBoletim->addTransferencia();
$obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade );
$obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->setContaDebito   ( $_REQUEST['inCodPlanoDebito']  );
$obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->setContaCredito  ( $_REQUEST['inCodPlanoCredito'] );

if ($_REQUEST['stAcao'] == 'excluir') {
    $obRTesourariaBoletim->roUltimaTransferencia->listarTransferenciaAtiva( $rsLista );
}

$rsLista->addFormatacao( 'valor' , 'NUMERIC_BR' );

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Boletim");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Lote");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Conta a Crédito");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Conta a Débito");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_boletim] - [exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_boletim" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_lote] - [exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_plano_credito] - [nom_conta_credito]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_plano_debito] - [nom_conta_debito]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->addAcao();

$stLink  = "&inCodTerminal=".$_REQUEST['inCodTerminal']."&stTimestampTerminal=".$_REQUEST['stTimestampTerminal'];
$stLink .= "&stTimestampUsuario=".$_REQUEST['stTimestampUsuario'];
if ($stAcao == 'excluir') {
    $obLista->ultimaAcao->setAcao( 'Estornar' );
}
$obLista->ultimaAcao->addCampo( "&inCodEntidade" , "cod_entidade" );
$obLista->ultimaAcao->addCampo( "inCodLote"      , "cod_lote"     );
$obLista->ultimaAcao->addCampo( "stExercicio"    , "exercicio"    );
if ($stAcao == 'excluir') {
    $obLista->ultimaAcao->addCampo( "stTimestampTransferencia", "timestamp_transferencia" );
}
$obLista->ultimaAcao->setLink ( $pgProx."?stAcao=".$stAcao."&".Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->show();
?>
