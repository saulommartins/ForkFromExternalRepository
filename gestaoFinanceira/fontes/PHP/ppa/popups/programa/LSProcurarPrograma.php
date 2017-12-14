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
* Página de Listagem de Procura de Programas
* Data de Criação   : 21/10/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Fellipe Esteves dos Santos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_PPA_CLASSES.'negocio/RPPAManterPrograma.class.php');
include_once(CAM_GF_PPA_CLASSES.'visao/VPPAManterPrograma.class.php');

//Define o nome dos arquivos PHP
$stPrograma 	= "ProcurarPrograma";
$pgFilt 		= "FL".$stPrograma.".php";
$pgList 		= "LS".$stPrograma.".php";
$pgJs   		= "JS".$stPrograma.".php";

include_once($pgJs);

$obRPPAManterPrograma = new RPPAManterPrograma();
$obVPPAManterPrograma = new VPPAManterPrograma($obRPPAManterPrograma);

//Define os valores necessários para fechar a janela e incaminhar para o Incluir Programa ('FMManterPrograma.php')
$stFiltro = " m.nom_modulo = 'PPA' AND f.nom_funcionalidade = 'Programa' AND a.nom_arquivo = 'FMManterPrograma.php'  \n";
$rsFuncPrograma = $obVPPAManterPrograma->exibirFuncionalidadePrograma($stFiltro);

//Valores
$inCodFunc = $rsFuncPrograma->arElementos[0]['cod_funcionalidade'];
$noTitulo = $rsFuncPrograma->arElementos[0]['nom_funcionalidade'];
$inCodModulo = $rsFuncPrograma->arElementos[0]['cod_modulo'];
$noModulo = $rsFuncPrograma->arElementos[0]['nom_modulo'];
$inCodAcao = $rsFuncPrograma->arElementos[0]['cod_acao'];

//Setando o JavaScript
$stJs = "javascript:novoPrograma(".$inCodFunc.",'".$noTitulo."',".$inCodModulo.",'".$noModulo."',".$inCodAcao.");";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

// Definicao dos objetos hidden
$obHdnForm = new Hidden();
$obHdnForm->setName( "nomForm" );
$obHdnForm->setId  ( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden();
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setId  ( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

$obHdnCampoNom = new Hidden();
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnCodPPA = new Hidden();
$obHdnCodPPA->setName( "inCodPPA" );
$obHdnCodPPA->setValue( $_REQUEST['inCodPPA'] );

$rsPrograma = new RecordSet();
if ($stAcao == 'alterar') {
    $rsPrograma = $obVPPAManterPrograma->buscaProgramaLista($_REQUEST);
} else {
    $rsPrograma = $obVPPAManterPrograma->buscaProgramaListaExclusao($_REQUEST);
}

$obLista = new Lista;
$obLista->setRecordSet($rsPrograma);
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("PPA ");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Número ");
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição ");
$obLista->ultimoCabecalho->setWidth(75);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo('cod_ppa');
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo('num_programa');
$obLista->ultimoDado->setAlinhamento('DIREITA');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo('identificacao');
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->setFuncao(true);
$obLista->ultimaAcao->setLink("JavaScript:inserePrograma()");
$obLista->ultimaAcao->addCampo('1',	'num_programa');
$obLista->ultimaAcao->addCampo('2',	'identificacao');
$obLista->ultimaAcao->addCampo('3',	'cod_programa');

$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario();

$obBtnCancelar = new Button();
$obBtnCancelar->setName( 'cancelar' );
$obBtnCancelar->setValue( 'Cancelar' );
$obBtnCancelar->obEvento->setOnClick( "window.close();" );

$obBtnFiltro = new Button();
$obBtnFiltro->setName( 'filtro' );
$obBtnFiltro->setValue( 'Filtro' );
$obBtnFiltro->obEvento->setOnClick( "Javascript:history.back(-1);" );

//Novo Programa
$obBtnIncluir = new Button();
$obBtnIncluir->setName('novo');
$obBtnIncluir->setValue('Novo');
$obBtnIncluir->obEvento->setOnClick($stJs);

$obFormulario->defineBarra(array($obBtnCancelar, $obBtnFiltro, $obBtnIncluir), '', '');
$obFormulario->addHidden($obHdnForm);
$obFormulario->addHidden($obHdnCampoNum);
$obFormulario->addHidden($obHdnCampoNom);
$obFormulario->addHidden($obHdnCodPPA);

$obFormulario->show();

?>
