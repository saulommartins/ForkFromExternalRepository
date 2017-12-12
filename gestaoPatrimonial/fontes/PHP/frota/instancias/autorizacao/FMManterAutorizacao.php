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
 * Data de Criação: 26/11/2007

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Henrique Boaventura

 $Id: FMManterAutorizacao.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-03.02.13

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
include_once CAM_GP_FRO_COMPONENTES.'IPopUpVeiculo.class.php';
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaAutorizacao.class.php';

$stPrograma = "ManterAutorizacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

if ($stAcao == 'alterar') {
   $obTFrotaAutorizacao = new TFrotaAutorizacao();
   $obTFrotaAutorizacao->setDado( 'cod_autorizacao', $_REQUEST['inCodAutorizacao'] );
   $obTFrotaAutorizacao->setDado( 'exercicio', $_REQUEST['stExercicio'] );
   $obTFrotaAutorizacao->recuperaAutorizacao( $rsAutorizacao );

   $obInCodAutorizacao = new Textbox();
   $obInCodAutorizacao->setName( 'inCodAutorizacao' );
   $obInCodAutorizacao->setRotulo( 'Código' );
   $obInCodAutorizacao->setNull( true );
   $obInCodAutorizacao->setValue( $rsAutorizacao->getCampo( 'cod_autorizacao' ).'/'.$rsAutorizacao->getCampo( 'exercicio' ) );
   $obInCodAutorizacao->setLabel( true );
} else {
   $rsAutorizacao = new RecordSet();
}

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue( $stAcao );

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//instancia o componente IPopUpVeiculo
$obIPopUpVeiculo = new IPopUpVeiculo($obForm);
$obIPopUpVeiculo->obCampoCod->obEvento->setOnBlur ("montaParametrosGET('montaVeiculo','inCodVeiculo');");
$obIPopUpVeiculo->obCampoCod->setObrigatorioBarra ( true );
$obIPopUpVeiculo->setNull                         ( false );
$obIPopUpVeiculo->setValue                        ( $rsAutorizacao->getCampo('nom_modelo') );
$obIPopUpVeiculo->obCampoCod->setValue            ( $rsAutorizacao->getCampo('cod_veiculo') );

//instancia um textbox para o numero da placa
$obTxtPlaca = new TextBox();
$obTxtPlaca->setRotulo              ( 'Placa do Veículo' );
$obTxtPlaca->setTitle               ( 'Informe a placa do veículo.' );
$obTxtPlaca->setName                ( 'stNumPlaca' );
$obTxtPlaca->setId                  ( 'stNumPlaca' );
$obTxtPlaca->obEvento->setOnKeyUp   ( "mascaraPlacaVeiculo(this);" );
$obTxtPlaca->obEvento->setOnBlur    ( "mascaraPlacaVeiculo(this);" );
$obTxtPlaca->setValue               ( $rsAutorizacao->getCampo('placa_masc') );
$obTxtPlaca->obEvento->setOnChange  ( "montaParametrosGET('montaVeiculo','stNumPlaca');");

//instancia textbox para o prefixo
$obTxtPrefixo = new TextBox();
$obTxtPrefixo->setRotulo              ( 'Prefixo' );
$obTxtPrefixo->setTitle               ( 'Informe prefixo do veículo.' );
$obTxtPrefixo->setName                ( 'stPrefixo' );
$obTxtPrefixo->setId                  ( 'stPrefixo' );
$obTxtPrefixo->setSize                ( 15 );
$obTxtPrefixo->setMaxLength           ( 15 );
$obTxtPrefixo->setValue               ( $rsAutorizacao->getCampo('prefixo') );
$obTxtPrefixo->obEvento->setOnChange  ("montaParametrosGET('montaVeiculo','stPrefixo');");

//instancia um ipopupcgm para quem autorizou
$obIPopUpAutorizador = new IPopUpCGM( $obForm );
$obIPopUpAutorizador->setName               ( 'stNomAutorizador' );
$obIPopUpAutorizador->setId                 ( 'stNomAutorizador' );
$obIPopUpAutorizador->obCampoCod->setName   ( 'inCodAutorizador' );
$obIPopUpAutorizador->obCampoCod->setId     ( 'inCodAutorizador' );
$obIPopUpAutorizador->setRotulo             ( 'Autorizado Por' );
$obIPopUpAutorizador->setTitle              ( 'Informe o responsável pela autorização do veículo.' );
$obIPopUpAutorizador->obCampoCod->setValue  ( $rsAutorizacao->getCampo( 'cgm_resp_autorizacao' ) );
$obIPopUpAutorizador->setValue              ( $rsAutorizacao->getCampo( 'nom_resp_autorizacao' ) );

//instancia o componente IPopUpCGMVinculado para a abastecedora
$obIPopUpAbastecedora = new IPopUpCGMVinculado( $obForm );
$obIPopUpAbastecedora->setTabelaVinculo       ( 'frota.posto' );
$obIPopUpAbastecedora->setCampoVinculo        ( 'cgm_posto'               );
$obIPopUpAbastecedora->setFiltroVinculado     (' and tabela_vinculo.ativo = true ');
$obIPopUpAbastecedora->setNomeVinculo         ( 'Posto'         );
$obIPopUpAbastecedora->setRotulo              ( 'CGM do Posto' );
$obIPopUpAbastecedora->setTitle               ( 'Informe o CGM do Posto.' );
$obIPopUpAbastecedora->setName                ( 'stNomAbastecedora'    );
$obIPopUpAbastecedora->setId                  ( 'stNomAbastecedora'    );
$obIPopUpAbastecedora->obCampoCod->setName    ( 'inCodAbastecedora'    );
$obIPopUpAbastecedora->obCampoCod->setId      ( 'inCodAbastecedora'    );
$obIPopUpAbastecedora->setNull                ( false                  );
$obIPopUpAbastecedora->setValue               ( $rsAutorizacao->getCampo( 'nom_fornecedor' ) );
$obIPopUpAbastecedora->obCampoCod->setValue   ( $rsAutorizacao->getCampo( 'cgm_fornecedor' ) );

//instancia do componente de seleção do cgm do motorista
$obBscMotorista = new IPopUpCGMVinculado( $obForm );
$obBscMotorista->setTabelaVinculo       ( 'frota.motorista' );
$obBscMotorista->setCampoVinculo        ( 'cgm_motorista' );
$obBscMotorista->setNomeVinculo         ( 'Motorista' );
$obBscMotorista->setRotulo              ( 'Motorista' );
$obBscMotorista->setName                ( 'stNomMotorista');
$obBscMotorista->setId                  ( 'stNomMotorista');
$obBscMotorista->setValue               ( $rsAutorizacao->getCampo( 'nom_cgm' ) );
$obBscMotorista->obCampoCod->setName    ( "inCodMotorista"   );
$obBscMotorista->obCampoCod->setId      ( "inCodMotorista"   );
$obBscMotorista->obCampoCod->setValue   ( $rsAutorizacao->getCampo( 'cgm_motorista' ) );
$obBscMotorista->obCampoCod->setNull    ( false              );
$obBscMotorista->setNull                ( false              );

//instancia um combo para o combustivel
$obSelectCombustivel = new Select();
$obSelectCombustivel->setName     ( 'slCombustivel' );
$obSelectCombustivel->setId       ( 'slCombustivel' );
$obSelectCombustivel->addOption   ( '','Selecione' );
$obSelectCombustivel->setRotulo   ( 'Combustível' );
$obSelectCombustivel->setNull     ( false );

//Data da Autorizacao
$obDtAutorizacao = new Data;
$obDtAutorizacao->setName   ( 'stDtAutorizacao' );
$obDtAutorizacao->setId     ( 'stDtAutorizacao' );
$obDtAutorizacao->setRotulo ( 'Data da Autorização' );
$obDtAutorizacao->setTitle  ( 'Informe a data que ocorreu a Autorização de Abastecimento.' );
$obDtAutorizacao->setNull   ( false );
if ($stAcao == 'alterar') {
    $obDtAutorizacao->setValue  ( $rsAutorizacao->getCampo( 'dt_autorizacao' ) );
}else{
    $obDtAutorizacao->setValue  ( date('d/m/Y') );
}

//instancia um radio para completar o tanque sim
$obRdCompletarSim = new Radio();
$obRdCompletarSim->setName                ( 'boCompletar' );
$obRdCompletarSim->setId                  ( 'boCompletarSim' );
$obRdCompletarSim->setRotulo              ( 'Completar Tanque' );
$obRdCompletarSim->setTitle               ( 'Selecione a opção para completar o tanque.' );
$obRdCompletarSim->setLabel               ( 'Sim' );
$obRdCompletarSim->obEvento->setOnChange  ( "montaParametrosGET('montaDetalhe','boCompletar');" );
$obRdCompletarSim->setValue               ( true );
if ( $stAcao == 'incluir' OR $rsAutorizacao->getCampo('completar') == 't' ) {
    $obRdCompletarSim->setChecked( true );
}

//instancia um radio para completar o tanque nao
$obRdCompletarNao = new Radio();
$obRdCompletarNao->setName                ( 'boCompletar' );
$obRdCompletarNao->setId                  ( 'boCompletarNao' );
$obRdCompletarNao->setRotulo              ( 'Completar Tanque' );
$obRdCompletarNao->setTitle               ( 'Selecione a opção para completar o tanque.' );
$obRdCompletarNao->setLabel               ( 'Não' );
$obRdCompletarNao->obEvento->setOnChange  ( "montaParametrosGET('montaDetalhe','boCompletar');" );
$obRdCompletarNao->setValue               ( false );
if ( $rsAutorizacao->getCampo('completar') == 'f' ) {
    $obRdCompletarNao->setChecked( true );
}

//cria um span para os dados do autorizacao
$obSpnDetalhe = new Span();
$obSpnDetalhe->setId( 'spnDetalhe' );

//instancia um textarea para comentarios
$obTxtComentario = new TextArea();
$obTxtComentario->setName           ( 'stComentario' );
$obTxtComentario->setMaxCaracteres  ( 160 );
$obTxtComentario->setId             ( 'stComentario' );
$obTxtComentario->setRotulo         ( 'Comentário' );
$obTxtComentario->setTitle          ( 'Informe o comentário.' );
$obTxtComentario->setValue          ( $rsAutorizacao->getCampo('observacao') );

$obRdoUmaVia = new Radio();
$obRdoUmaVia->setName     ( "boVias" );
$obRdoUmaVia->setId       ( "boVias" );
$obRdoUmaVia->setRotulo   ( "Vias por página" );
$obRdoUmaVia->setValue    ( "false" );
$obRdoUmaVia->setLabel    ( "Uma" );

$obRdoDuasVias = new Radio();
$obRdoDuasVias->setName   ( "boVias" );
$obRdoDuasVias->setId     ( "boVias" );
$obRdoDuasVias->setValue  ( "true" );
$obRdoDuasVias->setLabel  ( "Duas" );
$obRdoDuasVias->setChecked( true );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addTitulo    ( 'Autorização de Abastecimento' );

if ($stAcao == 'alterar') {
   $obFormulario->addComponente( $obInCodAutorizacao );
}

$obFormulario->addComponente    ( $obIPopUpVeiculo );
$obFormulario->addComponente    ( $obTxtPlaca );
$obFormulario->addComponente    ( $obTxtPrefixo );
$obFormulario->addComponente    ( $obIPopUpAutorizador );
$obFormulario->addComponente    ( $obIPopUpAbastecedora );
$obFormulario->addComponente    ( $obBscMotorista );
$obFormulario->addComponente    ( $obDtAutorizacao );
$obFormulario->addComponente    ( $obSelectCombustivel );
$obFormulario->agrupaComponentes( array( $obRdCompletarSim, $obRdCompletarNao ) );
$obFormulario->addSpan          ( $obSpnDetalhe );
$obFormulario->addComponente    ( $obTxtComentario );
$obFormulario->agrupaComponentes( array( $obRdoUmaVia, $obRdoDuasVias ) );

if ($stAcao == 'alterar') {
   $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
} else {
   $obFormulario->OK();
}

$obFormulario->show();

if ($stAcao == 'alterar') {
   $jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodVeiculo=".$rsAutorizacao->getCampo('cod_veiculo')."&slCombustivel=".$rsAutorizacao->getCampo('cod_item')."','montaCombustivel' );";

   if ($rsAutorizacao->getCampo('completar') == 'f') {
      $jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inQuantidade=".$rsAutorizacao->getCampo('quantidade')."&inValor=".$rsAutorizacao->getCampo('valor')."','montaDetalhe' );";
   }
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
