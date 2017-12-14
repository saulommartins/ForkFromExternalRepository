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
    * Página de Processamento de Arquivos de Exportação
    * Data de Criação   : 04/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Id: PRExportador.php 46941 2012-06-29 11:36:31Z tonismar $

    * Casos de uso: uc-02.00.00
                    uc-02.08.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
SistemaLegado::executaFramePrincipal("BloqueiaFrames(true,false);");

$stPrograma = "Exportador";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
$pgAnterior = $HTTP_REFERER; //.'?'.Sessao::getId();

//************************************************/
// Limpa a variavel de sessão para o filtro
//***********************************************/

$arPrefeitura = array();
$arFiltro = array();
foreach ($_POST as $key=>$value) {
    $arFiltro[$key] = $value;
    if ( !is_array($value) ) {
        $pgAnterior .= "&$key=$value";
    }
}
Sessao::write('filtroRelatorio', $arFiltro);

// Seta os dados para o cabeçalho
Sessao::write('inPeriodo', $arFiltro['inPeriodo']);
Sessao::write('inTipoPeriodo', $arFiltro['slTipoArquivo']);
Sessao::write('prefeitura_cnpj', substr($_REQUEST["stCnpjSetor"],0,14));
Sessao::write('prefeitura_nome', substr($_REQUEST["stCnpjSetor"],15,strlen($_REQUEST["stCnpjSetor"])-1));

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $_POST['hdnPaginaExportacao'] );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
if ($_POST['hdnPaginaExportacao']) {
    include_once($pgJs);
}
?>
