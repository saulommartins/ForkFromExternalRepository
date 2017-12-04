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
    * Página de Listagem de Terminais
    * Data de Criação   : 09/09/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 31059 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.02
*/

/*
$Log$
Revision 1.11  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTerminal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormCon = "FMConsultarTerminal.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho   = CAM_GF_TES_INSTANCIAS."configuracao/";

$obRTesourariaBoletim = new RTesourariaBoletim;

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
    $_REQUEST['stCodVerificador'    ] = $arFiltro['stCodVerificador'   ];
    $_REQUEST['inNumTerminal'       ] = $arFiltro['inNumTerminal'      ];
    $_REQUEST['inNumCGM'            ] = $arFiltro['inNumCGM'           ];
    $_REQUEST['stSituacao'          ] = $arFiltro['stSituacao'         ];
    $_GET['stAcao'                  ] = $arFiltro['stAcao'             ];
}

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}
$stSituacao = $_REQUEST['stSituacao'];
//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'consultar': $pgProx = $pgFormCon; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    DEFAULT         : $pgProx = $pgForm;
}

$obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal      ( $_REQUEST['inNumTerminal']   );
$obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->addUsuarioTerminal();
$obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->roUltimoUsuario->obRCGM->setNumCGM( $_REQUEST['inNumCgm']);

if (($stSituacao=='a') or ($stAcao=='excluir')) {
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->listarTerminalAtivo($rsLista);
    if ( $rsLista->getNumLinhas() == 1 ) {
        $obRTesourariaBoletim->listarBoletimAberto( $rsBoletimAberto );
        if ( $rsBoletimAberto->getNumLinhas() > -1 ) {
            $rsLista = new RecordSet;
            while (!$rsBoletimAberto->eof()) {
                $stEntidades .= $rsBoletimAberto->getCampo("cod_entidade").",";
                $rsBoletimAberto->proximo();
            }
            $stEntidades = substr($stEntidades, 0, strlen($stEntidades)-1);
            SistemaLegado::exibeAviso("Existe(m) ".$rsBoletimAberto->getNumLinhas()." boletim(s) aberto(s) para a(s) entidade(s) (".$stEntidades."). Feche-o(s) antes de desativar o terminal!","","erro");
        }
    }
} elseif ($stSituacao=='i') {
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->listarTerminalInativo($rsLista);
} else {
    $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->listar($rsLista);
}

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nr. Terminal");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Responsável");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Situação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_terminal]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cgm_usuario]-[nom_cgm]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[situacao]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo( "&inNumTerminal"        , "cod_terminal"        );
$obLista->ultimaAcao->addCampo( "stCodVerificador"      , "cod_verificador"     );
$obLista->ultimaAcao->addCampo( "inCgm"                 , "cgm_usuario"         );
$obLista->ultimaAcao->addCampo( "stSituacao"            , "situacao"            );
$obLista->ultimaAcao->addCampo( "stTimestampTerminal"   , "timestamp_terminal"  );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?stAcao=".$stAcao."&".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?stAcao=".$stAcao."&".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
?>
