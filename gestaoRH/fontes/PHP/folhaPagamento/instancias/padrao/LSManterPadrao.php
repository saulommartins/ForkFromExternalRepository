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
    * Página de Listagem de Padrao
    * Data de Criação   : 03/12/2004

    * @author Analista: ???
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-04 12:26:42 -0300 (Qua, 04 Jul 2007) $

    * Casos de uso :uc-04.05.05

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php"                                   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPadrao";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

$stCaminho = CAM_GRH_FOL_INSTANCIAS."padrao/";

$obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}
//MANTEM FILTRO E PAGINACAO
$stLink = "&stAcao=".$stAcao;
$link = Sessao::read("link");
if ( $request->get("pg") and  $request->get("pos") ) {
    $stLink.= "&pg=".$request->get("pg")."&pos=".$request->get("pos");
    $link["pg"]  = $request->get("pg");
    $link["pos"] = $request->get("pos");
    Sessao::write("link",$link);
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write("link",$link);
}

//MONTA O FILTRO
if ($_REQUEST["stCodPadrao"]) {
    $obRFolhaPagamentoPadrao->setCodPadrao( $_REQUEST["stCodPadrao"] );
}
if ($_REQUEST["stDescricao"]) {
    $obRFolhaPagamentoPadrao->setDescricaoPadrao( $_REQUEST["stDescricao"] );
}

$stFiltro = " AND FPP.cod_padrao <> 0";
$obRFolhaPagamentoPadrao->listarPadrao( $rsListaPadrao, "", $stFiltro );
$rsListaPadrao->addFormatacao("valor", "NUMERIC_BR");

$obLista = new Lista;

$obLista->setRecordSet( $rsListaPadrao );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Vigência" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_padrao" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vigencia" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&stDescricao"     , "descricao"      );
$obLista->ultimaAcao->addCampo("&inCodPadrao"     , "cod_padrao"     );
$obLista->ultimaAcao->addCampo("&inCodNorma"      , "cod_norma"      );
$obLista->ultimaAcao->addCampo("&inCodNormaTxt"   , "cod_norma"      );
$obLista->ultimaAcao->addCampo("&stHorasMensais"  , "horas_mensais"  );
$obLista->ultimaAcao->addCampo("&stHorasSemanais" , "horas_semanais" );
$obLista->ultimaAcao->addCampo("&stValorPadrao"         , "valor"          );
$obLista->ultimaAcao->addCampo("&stDescQuestao"   , "descricao"      );
$obLista->ultimaAcao->addCampo("dtVigencia"       , "vigencia"       );
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();
?>
