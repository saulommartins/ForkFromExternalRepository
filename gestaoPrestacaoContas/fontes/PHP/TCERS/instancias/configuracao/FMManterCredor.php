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
    * Página Formulário - Parâmetros do Arquivo CREDOR
    * Data de Criação   : 11/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Id: $

    * Casos de uso: uc-02.08.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//SistemaLegado::BloqueiaFrames();
//Define o nome dos arquivos PHP
$stPrograma = "ManterCredor";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$stAno = $_POST['stAno'];

if (($stAno) && ((integer) $stAno < (integer) Sessao::getExercicio()) ) {
    SistemaLegado::executaFramePrincipal( "BloqueiaFrames(true,false);buscaDado('MontaListaManterCredorConversao');" );
} elseif ($stAno == Sessao::getExercicio()) {
    SistemaLegado::executaFramePrincipal( "BloqueiaFrames(true,false);buscaDado('MontaListaManterCredor');" );
} elseif (!$stAno) {
    SistemaLegado::executaFramePrincipal( "BloqueiaFrames(true,false);buscaDado('MontaListaManterCredorGeral');" );
}

Sessao::write('stAno', $stAno);

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

/**
*************************************
* NOTA : 01-28022005
* Adicionado por Lucas Stephanpu
* Data: 28/02/2005
*/

//Define Span para Select de preenchimento automagico
$obSpanSelectAutoMagico = new Span;
$obSpanSelectAutoMagico->setId  (   "spnSelect" );
/**
* FIM-NOTA: 01-28022005
*******************************************
*/

//Define Span para DataGrid

$obSpnManterCredor = new Span;
$obSpnManterCredor->setId ( "spnManterCredor" );
$obSpnManterCredorConversao = new Span;
$obSpnManterCredorConversao->setId ( "spnManterCredorConversao" );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );

$obFormulario->addSpan      ( $obSpanSelectAutoMagico   );
$obFormulario->addSpan      ( $obSpnManterCredor        );

// $obFormulario->addTitulo( "Dados de Credores da Conversão de Dados" );
$obFormulario->addSpan( $obSpnManterCredorConversao );

$stLocation = $pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao ;
$obFormulario->defineBarra(array(new ok(true)));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
