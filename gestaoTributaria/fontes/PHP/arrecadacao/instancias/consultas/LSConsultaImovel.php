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
    * Pagina de Lista de Imoveis para Consulta de Arrecadação
    * Data de Criação   : 20/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: LSConsultaImovel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.3  2006/09/15 11:04:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRLancamento.class.php"                                             );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaImovel";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgFormNL = "FM".$stPrograma."NaoLanc.php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";

// instancia regra de lancamento
$obRARRLancamento = new RARRLancamento ( new RARRCalculo );

// verificar
// constroi filtros
$obRARRLancamento->obRCIMImovel->setNumeroInscricao( $_REQUEST["inInscricaoImobiliaria"] );
$obRARRLancamento->obRCgm->setNumCgm($_REQUEST["inCodContribuinte"]);
$obRARRLancamento->roRARRCalculo->setExercicio($_REQUEST["stExercicio"]);

// imoveis nao lançados
$obRARRLancamento->listaImoveisNaoLancados($rsListaImoveisNL);

Sessao::write( 'filtro', "&inInscricaoImobiliaria=".((isset($_REQUEST["inInscricaoImobiliaria"])) ? $_REQUEST["inInscricaoImobiliaria"] : '')."&inInscricaoEconomica=".((isset($_REQUEST["inInscricaoEconomica"])) ? $_REQUEST["inInscricaoEconomica"] : '')."&inCodContribuinte=".((isset($_REQUEST["inCodContribuinte"])) ? $_REQUEST["inCodContribuinte"] : '')."&stExercicio=".((isset($_REQUEST["stExercicio"])) ? $_REQUEST["stExercicio"] : '')."" );

//MONTA LISTA DE IMOVEIS
$obListaNL = new Lista;
$obListaNL->setTitulo   ( "Lista de Imóveis");
$obListaNL->setRecordSet( $rsListaImoveisNL );

$obListaNL->addCabecalho();
$obListaNL->ultimoCabecalho->addConteudo("&nbsp;");
$obListaNL->ultimoCabecalho->setWidth( 5 );
$obListaNL->commitCabecalho();
$obListaNL->addCabecalho();
$obListaNL->ultimoCabecalho->addConteudo("Contribuinte");
$obListaNL->ultimoCabecalho->setWidth( 30 );
$obListaNL->commitCabecalho();
$obListaNL->addCabecalho();
$obListaNL->ultimoCabecalho->addConteudo("Inscrição");
$obListaNL->ultimoCabecalho->setWidth( 20 );
$obListaNL->commitCabecalho();
$obListaNL->addCabecalho();
$obListaNL->addCabecalho();
$obListaNL->ultimoCabecalho->addConteudo("Endereço");
$obListaNL->ultimoCabecalho->setWidth( 40 );
$obListaNL->commitCabecalho();
$obListaNL->addCabecalho();
$obListaNL->ultimoCabecalho->addConteudo("&nbsp;");
$obListaNL->ultimoCabecalho->setWidth( 5 );
$obListaNL->commitCabecalho();

$obListaNL->addDado();
$obListaNL->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
$obListaNL->ultimoDado->setAlinhamento( 'ESQ' );
$obListaNL->commitDado();
$obListaNL->addDado();
$obListaNL->addDado();
$obListaNL->ultimoDado->setCampo( "inscricao_municipal" );
$obListaNL->ultimoDado->setAlinhamento( 'CENTRO' );
$obListaNL->commitDado();
$obListaNL->addDado();
$obListaNL->ultimoDado->setCampo( "dados" );
$obListaNL->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obListaNL->commitDado();

// Define ACOES
$obListaNL->addAcao();
$stAcao = "consultar";
$obListaNL->ultimaAcao->setAcao   ( $stAcao );
$obListaNL->ultimaAcao->addCampo  ( "&inInscricao"    , "inscricao_municipal"     );
$obListaNL->ultimaAcao->addCampo  ( "&inNumCgm"       , "numcgm"                  );
$obListaNL->ultimaAcao->addCampo  ( "&inNomCgm"       , "nom_cgm"                 );
$obListaNL->ultimaAcao->addCampo  ( "&stDados"        , "dados"                   );

$obListaNL->ultimaAcao->setLink($pgForm."?".Sessao::getId()."&stAcao=".$stAcao."&stCtrl=consultar" );
$obListaNL->commitAcao();

$obListaNL->show();
