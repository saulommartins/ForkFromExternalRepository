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
/*
    * Formulário para lista para procura de calendário
    * Data de Criação   : 13/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCalendario";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId();
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stFncJavaScript .= " function insereCalendario(num,nom) {  \n";
$stFncJavaScript .= " var doc = jQuery(window.opener.parent.frames['telaPrincipal'].document); \n";
$stFncJavaScript .= " var stPopup = 'abrePopUp(\'".CAM_GRH_CAL_POPUPS."calendario/FMConsultarCalendario.php\',\'frm\',\'\',\'\',\'\',\'".Sessao::getId()."&inCodCalendario='+num+'\',\'800\',\'550\')'; \n";
$stFncJavaScript .= " doc.find('#".$_REQUEST["campoNum"]."').attr('value',num); \n";
$stFncJavaScript .= " doc.find('#".$_REQUEST["campoNom"]."').html(nom); \n";
$stFncJavaScript .= " doc.find('#linkConsultarCalendario').attr('href','JavaScript: '+stPopup+';'); \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$stLink .= "&stNomeCalendario=".$_REQUEST["stNomeCalendario"];
$stLink .= "&stAcao=".$stAcao;

$rsLista = new RecordSet;

$stNome = $_REQUEST["stNomeCalendario"];

if ($stNome) {
    $stFiltro = " WHERE lower(descricao) like '%'|| lower('".trim($stNome)."%')||'%' ";
}

include_once(CAM_GRH_CAL_MAPEAMENTO."TCalendarioCalendarioCadastro.class.php");
$obTCalendarioCalendarioCadastro = new TCalendarioCalendarioCadastro();
$obTCalendarioCalendarioCadastro->recuperaTodos($rsCalendarios,$stFiltro," ORDER BY descricao");

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsCalendarios );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Calendário");
$obLista->ultimoCabecalho->setWidth( 90 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereCalendario();" );
$obLista->ultimaAcao->addCampo("1","cod_calendar");
$obLista->ultimaAcao->addCampo("2","descricao");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;

$obBtnCancelar = new Button;
$obBtnCancelar->setName                 ( 'cancelar'                                        );
$obBtnCancelar->setValue                ( 'Cancelar'                                        );
$obBtnCancelar->obEvento->setOnClick    ( "window.close();"                                 );

$obBtnFiltro = new Button;
$obBtnFiltro->setName                   ( 'filtro'                                          );
$obBtnFiltro->setValue                  ( 'Filtro'                                          );
$obBtnFiltro->obEvento->setOnClick      ( "Cancelar('".$pgFilt.$stLink."','telaPrincipal');");

$obFormulario->defineBarra              ( array( $obBtnCancelar,$obBtnFiltro ) , '', ''     );
$obFormulario->obJavaScript->addFuncao  ( $stFncJavaScript                                  );
$obFormulario->show();

?>
