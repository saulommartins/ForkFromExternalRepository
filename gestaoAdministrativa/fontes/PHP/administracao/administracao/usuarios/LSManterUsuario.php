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
    * Manutneção de usuários
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-01.03.93

    $Id: LSManterUsuario.php 65393 2016-05-18 19:05:03Z jean $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_NEGOCIO.'RUsuario.class.php';

# Define o nome dos arquivos PHP
$stPrograma = "ManterUsuario";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$pgProx     = $pgForm;

$stAcao = $request->get('stAcao');

$obRUsuario = new RUsuario;

# MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;

if ($_GET["pg"] && $_GET["pos"]) {
    Sessao::write('link_pg',$_GET["pg"]);
    Sessao::write('link_pos',$_GET["pos"]);
}

//DEFINICAO DO FILTRO PARA CONSULTA

if ($request->get("inNumCGM")) {
    $stLink .= "&inNumCGM=".$request->get("inNumCGM");
}
if ($request->get("stNomCGM")) {
    $stLink .= "&stNomCGM=".$request->get("stNomCGM");
}
if ($request->get("stUserName")) {
    $stLink .= "&stUserName=".$request->get("stUserName");
}

$obRUsuario->obRCGM->setNumCGM ( $request->get('inNumCGM')   );
$obRUsuario->obRCGM->setNomCGM ( $request->get('stNomCGM')   );
$obRUsuario->setUsername       ( $request->get('stUserName') );

if ($stAcao == 'usuario') {
    $obErro = $obRUsuario->obRCGM->listarFisicoJuridico( $rsRecordSet, preg_replace("/[^a-zA-Z0-9]/",'',$request->get('CNPJ')), preg_replace("/[^a-zA-Z0-9]/",'',$request->get('CPF')), $request->get('inRG') );
} else {
     $obErro = $obRUsuario->listarUsuarioCGM( $rsRecordSet,preg_replace("/[^a-zA-Z0-9]/",'',$request->get('CNPJ')), preg_replace("/[^a-zA-Z0-9]/",'',$request->get('CPF')), $request->get('inRG') );
}

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsRecordSet );
$obLista->setTitulo ("Registros de CGM");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "CGM" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento ( 'RIGHT' );
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inNumCGM"    ,"numcgm");
$obLista->ultimaAcao->addCampo("&stNomCGM"    ,"nom_cgm");
$obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );

$obLista->commitAcao();

$obLista->show();

$obFormulario = new Formulario();
$obFormulario->setAjuda( "UC-01.03.93" );
$obFormulario->show();
?>
