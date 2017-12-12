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
    * Página de lista para o cadastro de loteamento
    * Data de Criação   : 21/03/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: LSManterLoteamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.15
*/

/*
$Log$
Revision 1.7  2006/09/18 10:30:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteamento.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterLoteamento";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

$stCaminho = CAM_GT_CIM_INSTANCIAS."loteamento/";

switch ($_REQUEST['stAcao']) {
    case 'alterar'   : $pgProx = $pgForm; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    DEFAULT          : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read('link');

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}

$stLink .= "&stAcao=".$_REQUEST['stAcao'];

if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink .= Sessao::read('stLink');
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link', $link);
Sessao::write('stLink', $stLink);

$obRCIMLoteamento = new RCIMLoteamento;

if ($_REQUEST['inCodigoLoteamento']) {
    $obRCIMLoteamento->setCodigoLoteamento( $_REQUEST['inCodigoLoteamento'] );
}

if ($_REQUEST['stNomeLoteamento']) {
    $obRCIMLoteamento->setNomeLoteamento( $_REQUEST['stNomeLoteamento'] );
}

$obRCIMLoteamento->listarLoteamento( $rsLotes );

$obLista = new Lista;
$obLista->setRecordSet( $rsLotes );
$obLista->setTitulo ("Registros de loteamento");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_loteamento" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_loteamento" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );

$obLista->ultimaAcao->addCampo("&inCodigoLoteamento" , "cod_loteamento"  );
$obLista->ultimaAcao->addCampo("&stNomLoteamento"    , "nom_loteamento"  );
$obLista->ultimaAcao->addCampo("&inProcesso"         , "cod_processo"    );
$obLista->ultimaAcao->addCampo("&stExercicio"        , "exercicio"       );
$obLista->ultimaAcao->addCampo("&dtAprovacao"        , "dt_aprovacao"    );
$obLista->ultimaAcao->addCampo("&dtLiberacao"        , "dt_liberacao"    );
$obLista->ultimaAcao->addCampo("&inAreaComunitaria"  , "area_comunitaria");
$obLista->ultimaAcao->addCampo("&inAreaLogradouro"   , "area_logradouro" );
$obLista->ultimaAcao->addCampo("&inNumLoteamento"    , "cod_lote"        );
$obLista->ultimaAcao->addCampo("&inNumLoteOrigem"    , "valor"           );
$obLista->ultimaAcao->addCampo("&stDescQuestao"      , "[cod_loteamento]-[nom_loteamento]"  );
$obLista->ultimaAcao->addCampo("&inCodLocalizacao"   , "cod_localizacao" );
$obLista->ultimaAcao->addCampo("&stLocalizacao"      , "codigo_composto" );

if ($_REQUEST['stAcao'] == "excluir") {
   $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.15" );
$obFormulario->show();
