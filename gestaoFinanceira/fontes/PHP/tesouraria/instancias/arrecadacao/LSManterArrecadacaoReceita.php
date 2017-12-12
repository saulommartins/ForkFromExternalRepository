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
    * Página de Listagem de Arrecadações
    * Data de Criação   : 21/11/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 24254 $
    $Name$
    $Autor:$
    $Date: 2007-07-25 11:49:14 -0300 (Qua, 25 Jul 2007) $

    * Casos de uso: uc-02.04.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"    );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaConfiguracao.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterArrecadacaoReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$arFiltro = Sessao::read('filtro');

$stCaminho = CAM_GF_TES_INSTANCIAS."arrecadacao/";

$obRTesourariaArrecadacao = new RTesourariaArrecadacao( new RTesourariaBoletim() );
$obRTesourariaArrecadacao->roRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
$obRTesourariaArrecadacao->roRTesourariaBoletim->setDataBoletim( date( 'd/m/'.Sessao::getExercicio()) );
/*
*   O teste abaixo se refere a existencia da variavel - inCodigoEntidadeBoletim - setada no arquivo PRManterArrecadacaoReceita.php
*   Esta variavel é setada quando não é encontrado nenhum boletim para ser efetuado o estorno de arrecadacao.
*/

if( $_REQUEST['inCodigoEntidadeBoletim'] )
    $obRTesourariaArrecadacao->roRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodigoEntidadeBoletim'] );
else if ($_REQUEST['inCodEntidade']) {
    if (isset($arFiltro['inCodEntidade'])) {
         $_REQUEST['inCodEntidade'] = $arFiltro['inCodEntidade'];
    }
    $obRTesourariaArrecadacao->roRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( implode( ',' , $_REQUEST['inCodEntidade'] ) );
}
$obErro = $obRTesourariaArrecadacao->roRTesourariaBoletim->buscarCodigoBoletim( $inCodBoletim, $stDtBoletim );
if( $obErro->ocorreu() ) SistemaLegado::exibeAviso( urlencode($obErro->getDescricao()),"","erro" );
$obRTesourariaArrecadacao->roRTesourariaBoletim->setExercicio( null );
$obRTesourariaArrecadacao->roRTesourariaBoletim->setDataBoletim( null );

$stLink .= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
if ( !Sessao::read('paginando') ) {
    $arPaginando = array();
    foreach ($_REQUEST as $stCampo => $stValor) {
        $arPaginando[$stCampo] = $stValor;
    }
    Sessao::write('filtro',$arPaginando);
    Sessao::write('pg',  $_GET['pg'] ? $_GET['pg'] : 0);
    Sessao::write('pos', $_GET['pos']? $_GET['pos'] : 0);
    Sessao::write('paginando', true );
} else {
    Sessao::write('pg', $_GET['pg']);
    Sessao::write('pos', $_GET['pos']);
    $_REQUEST['inCodEntidade'       ] = $arFiltro['inCodEntidade'          ];
    $_REQUEST['inCodTerminal'       ] = $arFiltro['inCodTerminal'          ];
    $_REQUEST['stTimestampTerminal' ] = $arFiltro['stTimestampTerminal'    ];
    $_REQUEST['stTimestampUsuario'  ] = $arFiltro['stTimestampUsuario'     ];
    $_REQUEST['inCodBoletim'        ] = $arFiltro['inCodBoletim'           ];
    $_REQUEST['stDtBoletim'         ] = $arFiltro['stDtBoletim'            ];
    $_REQUEST['inCodPlano'          ] = $arFiltro['inCodPlano'             ];
    $_REQUEST['inCodReceita'        ] = $arFiltro['inCodReceita'           ];
    $_REQUEST['inCodReceitaDedutora'] = $arFiltro['inCodReceitaDedutora'   ];
    $_REQUEST['stDtArrecadacao'     ] = $arFiltro['stDtArrecadacao'        ];
    $_GET['stAcao'                  ] = $arFiltro['stAcao'                 ];
    $_REQUEST['stDataInicial'       ] = $arFiltro['stDataInicial'          ];
    $_REQUEST['stDataFinal'         ] = $arFiltro['stDataFinal'            ];
}

$stLink.="&inCodTerminal=".$_REQUEST['inCodTerminal']."&stTimestampTerminal=".$_REQUEST['stTimestampTerminal'];
$stLink.="&stTimestampUsuario=".$_REQUEST['stTimestampUsuario'];

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'incluir'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'alterar'  : $pgProx = "FMEstornarArrecadacaoReceita.php"; break;
    DEFAULT         : $pgProx = $pgForm;
}

if ( is_array( $_REQUEST['inCodEntidade'] ) ) {
    $stCodEntidade = implode( ',', $_REQUEST['inCodEntidade'] );
}
$obRTesourariaArrecadacao->roRTesourariaBoletim->setExercicio           ( Sessao::getExercicio()            );
$obRTesourariaArrecadacao->roRTesourariaBoletim->setCodBoletim          ( $_REQUEST['inCodBoletim']         );
$obRTesourariaArrecadacao->roRTesourariaBoletim->setDataBoletim         ( $_REQUEST['stDtBoletim']          );
$obRTesourariaArrecadacao->obROrcamentoReceita->setCodReceita           ( $_REQUEST['inCodReceita']         );
$obRTesourariaArrecadacao->obROrcamentoReceitaDedutora->setCodReceita   ( $_REQUEST['inCodReceitaDedutora'] );
$obRTesourariaArrecadacao->obROrcamentoEntidade->setCodigoEntidade      ( $stCodEntidade                    );
$obRTesourariaArrecadacao->obRContabilidadePlanoBanco->setCodPlano      ( $_REQUEST['inCodPlano']           );
$obRTesourariaArrecadacao->setDtInicial( $_REQUEST['stDataInicial'] );
$obRTesourariaArrecadacao->setDtFinal  ( $_REQUEST['stDataFinal'] );

$obRTesourariaArrecadacao->listarArrecadacaoNaoEstornadaReceita( $rsLista );

$rsLista->addFormatacao("vl_arrecadacao","NUMERIC_BR");
$rsLista->addFormatacao("vl_estornado","NUMERIC_BR");
$rsLista->addFormatacao("vl_deducao","NUMERIC_BR");
$rsLista->addFormatacao("vl_deducao_estornado","NUMERIC_BR");

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Conta");
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Receita");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data");
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor Arrecadado");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor Estornado");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_plano" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_receita] - [descricao]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_arrecadacao" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_arrecadacao" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_estornado" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodArrecadacao"       , "cod_arrecadacao"       );
$obLista->ultimaAcao->addCampo( "&stExercicio"            , "exercicio"             );
$obLista->ultimaAcao->addCampo( "&stTimestampArrecadacao" , "timestamp_arrecadacao" );
$obLista->ultimaAcao->addCampo( "&inCodReceita"           , "cod_receita"           );
$obLista->ultimaAcao->addCampo( "&inCodReceitaDedutora"   , "cod_receita_dedutora"  );
$obLista->ultimaAcao->addCampo( "&stDescQuestao"          , "cod_receita"           );
$obLista->ultimaAcao->addCampo( "&inCodBoletim"           , "cod_boletim"           );
$obLista->ultimaAcao->addCampo( "&stDtBoletim"            , "dt_arrecadacao"        );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"          , "cod_entidade"          );
$obLista->ultimaAcao->addCampo( "&inCodPlano"             , "cod_plano"             );
$obLista->ultimaAcao->addCampo( "&nuValor"                , "vl_arrecadacao"        );
$obLista->ultimaAcao->addCampo( "&nuValorEstornado"       , "vl_estornado"          );
$obLista->ultimaAcao->addCampo( "&nuValorDeducao"         , "vl_deducao"            );
$obLista->ultimaAcao->addCampo( "&nuValorDeducaoEstornado", "vl_deducao_estornado"  );
$obLista->ultimaAcao->addCampo( "&inCodBemAlienacao"      , "cod_bem"               );

$obLista->ultimaAcao->setLink ( $stCaminho.$pgProx."?stAcao=".$stAcao."&".Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->show();

SistemaLegado::LiberaFrames();
?>
