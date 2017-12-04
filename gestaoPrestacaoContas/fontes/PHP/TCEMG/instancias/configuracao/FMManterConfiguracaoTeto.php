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
    * Página de Formulario de Configuração de Leis do LDO
  * Data de Criação: 15/01/2014

  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  *
  * $Id: FMManterConfiguracaoTeto.php 64798 2016-04-01 18:31:13Z michel $

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
include_once CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php";
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once CAM_GRH_FOL_COMPONENTES."IBscEvento.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoTeto";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once($pgJs);

$stAcao   = $request->get('stAcao', 'alterar');
$stModulo = $request->get('modulo');

$inCodEntidade = $request->get('inCodEntidade');

$vlTeto = "0,00";
$inCodigo = "";
$stDescricao = "";

$obEntidade = new ITextBoxSelectEntidadeUsuario;
$obEntidade->setNull ( false );
$obEntidade->setCodEntidade($inCodEntidade);
$obEntidade->obTextBox->setDisabled(true);
$obEntidade->obSelect->setDisabled(true);

$obVlTetoRemuneratorio = new Moeda;
$obVlTetoRemuneratorio->setId        ( "vlTeto"                                                                                       );
$obVlTetoRemuneratorio->setName      ( "vlTeto"                                                                                       );
$obVlTetoRemuneratorio->setRotulo    ( "*Teto Remuneratório"                                                                          );
$obVlTetoRemuneratorio->setAlign     ( 'RIGHT'                                                                                        );
$obVlTetoRemuneratorio->setTitle     ( "Informar o valor bruto menos os valores que não entram para o cálculo do teto remuneratório." );
$obVlTetoRemuneratorio->setMaxLength ( 19                                                                                             );
$obVlTetoRemuneratorio->setSize      ( 21                                                                                             );
$obVlTetoRemuneratorio->setValue     ( $vlTeto                                                                                        );
$obVlTetoRemuneratorio->setNull      ( true                                                                                           );

$obTxtJustificativa = new TextBox;
$obTxtJustificativa->setId        ( "stJustificativa"                                             );
$obTxtJustificativa->setName      ( "stJustificativa"                                             );
$obTxtJustificativa->setRotulo    ( "Justificativa"                                               );
$obTxtJustificativa->setTitle     ( "Informar a justificativa para o valor / alteração de valor." );
$obTxtJustificativa->setInteiro   ( false                                                         );
$obTxtJustificativa->setNull      ( true                                                          );
$obTxtJustificativa->setMaxLength ( 100                                                           );
$obTxtJustificativa->setSize      ( 100                                                           );

$obDtVigência = new Data;
$obDtVigência->setId       ( "dtVigencia"                                );
$obDtVigência->setName     ( "dtVigencia"                                );
$obDtVigência->setRotulo   ( "*Vigência"                                 );
$obDtVigência->setTitle    ( 'Informe a Data da Assinatura do Processo.' );
$obDtVigência->setNull     ( true                                        );

$obEventoAbateTeto = new IBscEvento("inCodigoEvento","stEvento");
$obEventoAbateTeto->obBscInnerEvento->setRotulo            ( "Evento abate teto" );
$obEventoAbateTeto->setEventoSistema                       ( false               );
$obEventoAbateTeto->obBscInnerEvento->obCampoCod->setValue ( $inCodigo           );
$obEventoAbateTeto->obBscInnerEvento->setValue             ( $stDescricao        );
$obEventoAbateTeto->setTextoComplementar                   ( false               );

$obBtnIncluir = new Button;
$obBtnIncluir->setValue             ( "Incluir" );
$obBtnIncluir->setName              ( "btnIncluir"  );
$obBtnIncluir->setId                ( "btnIncluir"  );
$obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET('incluirTeto');" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimpar");
$obBtnLimpar->setId                ( "limpar" );
$obBtnLimpar->setValue             ( "Limpar" );
$obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limparFormTeto');" );

// HIDDENS ------------------------------------

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName ( "hdnCodEntidade"  );
$obHdnEntidade->setId   ( "hdnCodEntidade"  );
$obHdnEntidade->setValue( $inCodEntidade );

$obHdnModulo = new Hidden;
$obHdnModulo->setName ( "hdnStModulo"  );
$obHdnModulo->setId   ( "hdnStModulo"  );
$obHdnModulo->setValue( $stModulo );

$obHdnId = new Hidden;
$obHdnId->setName( "inIdTeto" );
$obHdnId->setId  ( "inIdTeto" );

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

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );

//****************************************//
//Define LISTA DE TETOS
//****************************************//
$spnLista = new Span;
$spnLista->setId  ( 'spnListaTetos' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addTitulo( "Configuração de Teto Remuneratório" );
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnEntidade );
$obFormulario->addHidden     ( $obHdnModulo );
$obFormulario->addHidden     ( $obHdnId );

$obFormulario->addComponente($obEntidade);
$obFormulario->addComponente($obVlTetoRemuneratorio);
$obFormulario->addComponente($obDtVigência);
$obFormulario->addComponente($obTxtJustificativa);
$obEventoAbateTeto->geraFormulario($obFormulario);
$obFormulario->agrupaComponentes( array( $obBtnIncluir, $obBtnLimpar ),"","" );

$obFormulario->addSpan      ( $spnLista );

$obOk  = new Ok();
$obFormulario->defineBarra(array( $obOk ));

$obFormulario->show();

$jsOnload = "montaParametrosGET('carregaDados');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
