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
    * Lista do Popup para Responsavel Tecnico
    * Data de Criação   : 20/04/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo
    *
    * @ignore

    * $Id: LSProcurarInscricaoEconomica.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.9  2006/09/15 13:48:10  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

//echo "<pre>", print_r($_REQUEST),"</pre>";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarInscricaoEconomica";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get("stAcao");

$stLink = "&stAcao=".$stAcao;
$stLink .= "&campoNom=".$request->get("campoNom");
$stLink .= "&campoNum=".$request->get("campoNum");
$stLink .= "&nomForm=".$request->get("nomForm"	);
$stLink .= "&boTipoCorretagem=".$request->get("boTipoCorretagem");

//DEFINICAO DO FILTRO PARA CONSULTA
$obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
if ( $request->get("inCGM") ) {
    $obRCEMInscricaoEconomica->obRCGM->setNumCGM( $_REQUEST["inCGM"] );
    $stLink .= "&inCGM=".$_REQUEST["inCGM"];
}

if ( $request->get("stNome") ) {
    $obRCEMInscricaoEconomica->obRCGM->setNomCGM( $_REQUEST["stHdnNome"] );
    $stLink .= "&stNome=".$_REQUEST["stNome"]."&stHdnNome=".$_REQUEST["stHdnNome"];
}

$obRCEMInscricaoEconomica->setTipoListagem( "domicilio" ); //pra consulta não levar em conta a baixa
$obRCEMInscricaoEconomica->listarInscricao( $rsLista );

//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inscrição Econômica");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "CGM" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome");
$obLista->ultimoCabecalho->setWidth( 57 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao_economica" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "numcgm"   );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm"  );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( $stAcao                   				);
$obLista->ultimaAcao->setFuncao( true                      				);
$obLista->ultimaAcao->addCampo ( "1","inscricao_economica"				);
$obLista->ultimaAcao->addCampo ( "2","nom_cgm" 							);
$obLista->ultimaAcao->addCampo ( "3","numcgm" 							);
$obLista->ultimaAcao->setLink  ( "JavaScript:window.close(); Insere();"	);
$obLista->commitAcao();
$obLista->show();

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName	( "campoNum" );
$obHdnCampoNum->setId	( "campoNum" );
$obHdnCampoNum->setValue( $request->get("campoNum") );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName	( "campoNom" );
$obHdnCampoNom->setId	( "campoNom" );
$obHdnCampoNom->setValue( $request->get("campoNom") );

$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );

$botoes = array ( $obBtnFiltro );

$obFormulario = new Formulario;
$obFormulario->addHidden($obHdnCampoNum);
$obFormulario->addHidden($obHdnCampoNom);
$obFormulario->defineBarra ( $botoes, 'left', '' );
$obFormulario->show();

?>
