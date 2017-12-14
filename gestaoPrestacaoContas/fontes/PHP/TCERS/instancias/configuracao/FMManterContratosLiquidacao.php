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
    * Página Formulário - Parâmetros do Arquivo RDEXTRA.
    * Data de Criação   : 14/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 12203 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 20:51:50 +0000 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.04
*/

/*
$Log$
Revision 1.7  2006/07/05 20:46:25  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterContratosLiquidacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

if ($request->get('stAcao') == '') {
    $stAcao = 'incluir';
} else {
    $stAcao = $request->get('stAcao');
}

$jsOnload   = "JavaScript:modificaDado( 'configuracoesIniciais' );";

Sessao::remove('sessaoLista');
Sessao::write('sessaoLista',array());

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnId= new Hidden;
$obHdnId->setId ("inId");
$obHdnId->setName("inId");

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId( "stCtrl" );

//Define o objeto de controle de exclusão do itens da lista
//$obHdnDel = new Hidden;
//$obHdnDel->setName ( "inApagar" );
//$obHdnDel->setValue( "" );

$obNumLiquidacao = new Inteiro();
$obNumLiquidacao->setRotulo           ( "*Número da Liquidação" );
$obNumLiquidacao->setTitle            ( "Informe o número da liquidação." );
$obNumLiquidacao->setId               ( "inLiquidacao" );
$obNumLiquidacao->setName             ( "inLiquidacao" );
$obNumLiquidacao->setNull             ( true );
$obNumLiquidacao->setMaxlength        ( 20 );

$obNumContrato = new TextBox();
$obNumContrato->setRotulo           ( "*Número do Contrato" );
$obNumContrato->setTitle            ( "Informe o número do contrato." );
$obNumContrato->setId               ( "inContrato" );
$obNumContrato->setName             ( "inContrato" );
$obNumContrato->setNull             ( true );
$obNumContrato->setMaxlength        ( 20 );

$obNumContratoTCE = new Inteiro();
$obNumContratoTCE->setRotulo           ( "*Número do Contrato TCE" );
$obNumContratoTCE->setTitle            ( "Informe o número do contrato TCE." );
$obNumContratoTCE->setId               ( "inContratoTCE" );
$obNumContratoTCE->setName             ( "inContratoTCE" );
$obNumContratoTCE->setNull             ( true );
$obNumContratoTCE->setMaxlength        ( 20 );

$obAnoContrato = new Exercicio();
$obAnoContrato->setRotulo           ( "*Ano do Contrato" );
$obAnoContrato->setTitle            ( "Informe o ano do contrato." );
$obAnoContrato->setId               ( "stAno" );
$obAnoContrato->setName             ( "stAno" );
$obAnoContrato->setNull             ( true );

//Define Span para Lista
$obSpnLista = new Span;
$obSpnLista->setId ( "spnLista" );

$obBtnIncluir = new Button;
$obBtnIncluir->setName              ( "btnIncluir"                                                       );
$obBtnIncluir->setId                ( "btnIncluir"                                                       );
$obBtnIncluir->setValue             ( "Incluir"                                                          );
$obBtnIncluir->obEvento->setOnClick ( "buscaValor('incluirContrato');"                                   );
$obBtnIncluir->setTitle             ( "Clique para incluir"                                              );

$obBtnAlterar = new Button;
$obBtnAlterar->setName              ( "btnAlterar"                                                       );
$obBtnAlterar->setId                ( "btnAlterar"                                                       );
$obBtnAlterar->setValue             ( "Alterar"                                                          );
$obBtnAlterar->obEvento->setOnClick ( "buscaValor('alterarContrato');"                                   );
$obBtnAlterar->setTitle             ( "Clique para Alterar"                                              );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados para o arquivo" );

$obFormulario->addHidden( $obHdnId );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );

$obFormulario->addComponente ( $obNumLiquidacao  );
$obFormulario->addComponente ( $obNumContrato    );
$obFormulario->addComponente ( $obNumContratoTCE );
$obFormulario->addComponente ( $obAnoContrato    );

$obFormulario->defineBarra(array($obBtnIncluir, $obBtnAlterar),"left","");

$obFormulario->addSpan( $obSpnLista );

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "Limpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo( "Reset" );
$obBtnLimpar->obEvento->setOnClick( "document.frm.reset(); modificaDado( 'configuracoesIniciais' );" );

$obFormulario->defineBarra( array ( $obBtnOk, $obBtnLimpar ),"","" );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
