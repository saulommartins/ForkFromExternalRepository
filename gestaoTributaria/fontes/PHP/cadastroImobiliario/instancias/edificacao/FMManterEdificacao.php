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
    * Página de formulário para o cadastro de edificação
    * Data de Criação   : 17/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FMManterEdificacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.6  2006/09/18 10:30:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEdificacao";
$pgFilt        = "FL".$stPrograma.".php";
$pgFiltVinculo = "FL".$stPrograma."Vinculo.php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

// DEFINE OBJETOS DO FILTRO IMOVEL/CONDOMINIO - INCLUIR
$obRadioVinculoImovel = new Radio;
$obRadioVinculoImovel->setName        ( "boVinculoEdificacao"   );
$obRadioVinculoImovel->setId          ( "boVinculoEdificacao"   );
$obRadioVinculoImovel->setTitle       ( "Vínculo da Edificação" );
$obRadioVinculoImovel->setRotulo      ( "Vínculo"               );
$obRadioVinculoImovel->setValue       ( "Imóvel"                );
$obRadioVinculoImovel->setLabel       ( "Imóvel"                );
$obRadioVinculoImovel->setNull        ( false                   );
$obRadioVinculoImovel->setChecked     ( !$boVinculo             );
$obRadioVinculoImovel->obEvento->setOnClick( "habilitaVinculo( 'true' );" );

$obRadioVinculoCondominio = new Radio;
$obRadioVinculoCondominio->setName    ( "boVinculoEdificacao"   );
$obRadioVinculoCondominio->setId      ( "boVinculoEdificacao"   );
$obRadioVinculoCondominio->setValue   ( "Condomínio"            );
$obRadioVinculoCondominio->setLabel   ( "Condomínio"            );
$obRadioVinculoCondominio->setNull    ( false                   );
$obRadioVinculoCondominio->setChecked ( $boVinculo              );
$obRadioVinculoCondominio->obEvento->setOnClick( "habilitaVinculo( 'false' );" );

// Define objetos do filtro adicionar à edificação

$obRadioAddEdificacaoNao = new Radio;
$obRadioAddEdificacaoNao->setName          ( "boAdicionarEdificacao"               );
$obRadioAddEdificacaoNao->setTitle         ( "Adicionar à edificação existente"    );
$obRadioAddEdificacaoNao->setRotulo        ( "Adicionar à Edificação"              );
$obRadioAddEdificacaoNao->setValue         ( "Não"                                 );
$obRadioAddEdificacaoNao->setLabel         ( "Não"                                 );
$obRadioAddEdificacaoNao->setNull          ( false                                 );
$obRadioAddEdificacaoNao->setChecked       ( !$boAdicionarEdificacao               );

$obRadioAddEdificacaoSim = new Radio;
$obRadioAddEdificacaoSim->setName          ( "boAdicionarEdificacao"               );
$obRadioAddEdificacaoSim->setValue         ( "Sim"                                 );
$obRadioAddEdificacaoSim->setLabel         ( "Sim"                                 );
$obRadioAddEdificacaoSim->setNull          ( false                                 );
$obRadioAddEdificacaoSim->setChecked       ( $boAdicionarEdificacao                );

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick( "verificaAction()" );
$obBtnLimpar = new Limpar;
$obBtnLimpar->obEvento->setOnClick( "LimparFL();" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
//$obForm->setAction ( $pgFormVinculo  );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm          );
$obFormulario->setAjuda ( "UC-05.01.11" );
$obFormulario->addHidden ( $obHdnCtrl       );
$obFormulario->addHidden ( $obHdnAcao       );

$obFormulario->addTitulo   ( "Dados para edificação" );

if ($stAcao == "incluir") {
    $obFormulario->addComponenteComposto ( $obRadioVinculoImovel, $obRadioVinculoCondominio           );
    $obFormulario->agrupaComponentes     ( array( $obRadioAddEdificacaoNao,$obRadioAddEdificacaoSim ) );
    $obFormulario->defineBarra           ( array( $obBtnOK, $obBtnLimpar)                             );
    //$obFormulario->OK    ();
}

$obFormulario->addIFrameOculto("oculto");
$obFormulario->obIFrame->setHeight("0");
$obFormulario->obIFrame->setWidth("100%");
$obFormulario->setFormFocus( $obRadioVinculoImovel->getId() );
$obFormulario->show ();

//DEFINICAO DO IFRAME MENSAGEM
$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("50");
$obIFrame->show();
?>
