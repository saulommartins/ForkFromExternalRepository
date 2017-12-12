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
    * Data de Criação: 04/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-03.01.01

    $Id: FMManterConfiguracao.php 65516 2016-05-30 13:43:30Z arthur $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_COMPONENTES."IPopUpContaSintetica.class.php";

$stPrograma = "ManterConfiguracao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//recupera os valores da base de dados
$stColetoraDigitosLocal = sistemaLegado::pegaDado( 'valor', 'administracao.configuracao', "WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 6 AND parametro='coletora_digitos_local'");
$stColetoraDigitosPlaca = sistemaLegado::pegaDado( 'valor', 'administracao.configuracao', "WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 6 AND parametro='coletora_digitos_placa'");
$stColetoraCaracterSeparador = sistemaLegado::pegaDado( 'valor', 'administracao.configuracao', "WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 6 AND parametro='coletora_separador'");

$stContaAtivo             = sistemaLegado::pegaConfiguracao( 'grupo_contas_permanente',6, Sessao::getExercicio() );
$stDescricaoContaAtivo    = sistemaLegado::pegaDado( 'nom_conta', 'contabilidade.plano_conta', " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_estrutural = '".$stContaAtivo."' " );
$stTextoTransferencia     = sistemaLegado::pegaConfiguracao( 'texto_ficha_transferencia',6 );
$boAlterarBens            = sistemaLegado::pegaConfiguracao( 'alterar_bens_exercicio_anterior',6 );
$boPlacaAlfa              = sistemaLegado::pegaConfiguracao( 'placa_alfanumerica',6 );
$flValorMinimoDepreciacao = sistemaLegado::pegaDado( 'valor', 'administracao.configuracao', "WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 6 AND parametro='valor_minimo_depreciacao'");

if ($flValorMinimoDepreciacao != '') {
    $flValorMinimoDepreciacao = number_format($flValorMinimoDepreciacao,2,',','.');
}

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

$obHdnCodContaSintetica = new Hidden;
$obHdnCodContaSintetica->setName ("hdnCodContaSintetica" );
$obHdnCodContaSintetica->setValue($stContaAtivo);

$obHdnNomContaSintetica = new Hidden;
$obHdnNomContaSintetica->setName ("hdnNomContaSintetica" );
$obHdnNomContaSintetica->setValue($stDescricaoContaAtivo);

//cria text para descrição da transferência
$obTxtTransferencia = new TextArea();
$obTxtTransferencia->setId        ( 'stTransferencia' );
$obTxtTransferencia->setName      ( 'stTransferencia' );
$obTxtTransferencia->setRotulo    ( 'Texto da Transferência' );
$obTxtTransferencia->setTitle     ( 'Informe o texto a ficha de transferência.' );
$obTxtTransferencia->setNull      ( false );
$obTxtTransferencia->setValue     ( $stTextoTransferencia );

//instancia o componente IPopUpContaSintetica
$obIPopUpContaAtivo = new IPopUpContaSintetica();
$obIPopUpContaAtivo->setRotulo( 'Conta do Ativo Imobilizado' );
$obIPopUpContaAtivo->setTitle( 'Selecione a conta do ativo imobilizado.' );
$obIPopUpContaAtivo->setNull (false );
$obIPopUpContaAtivo->obCampoCod->setValue( $stContaAtivo );
$obIPopUpContaAtivo->setValue( $stDescricaoContaAtivo );

//cria radio Alterar Bens de Exercício Anterior
$obRdAlterarBensSim = new Radio();
$obRdAlterarBensSim->setTitle  ( 'Selecione se é permitido alterar bens do exercício anterior.' );
$obRdAlterarBensSim->setRotulo ( 'Alterar Bens de Exercício Anterior' );
$obRdAlterarBensSim->setId     ( 'boAlterarBem' );
$obRdAlterarBensSim->setName   ( 'boAlterarBem' );
$obRdAlterarBensSim->setValue  ( 'true' );
$obRdAlterarBensSim->setLabel  ( 'Sim' );
$obRdAlterarBensSim->setNull   ( false );

$obRdAlterarBensNao = new Radio();
$obRdAlterarBensNao->setId     ( 'boAlterarBem' );
$obRdAlterarBensNao->setName   ( 'boAlterarBem' );
$obRdAlterarBensNao->setValue  ( 'false' );
$obRdAlterarBensNao->setLabel  ( 'Não' );
$obRdAlterarBensNao->setNull   ( false );

//seta com os valores do banco
if ($boAlterarBens == 'true') {
    $obRdAlterarBensSim->setChecked( true );
} else {
    $obRdAlterarBensNao->setChecked( true );
}

//cria radio Alterar Placa Alfanumérica
$obRdPlacaAlfaSim = new Radio();
$obRdPlacaAlfaSim->setTitle  ( 'Selecione se a placa de identificação do bem permite caracteres alfanuméricos.' );
$obRdPlacaAlfaSim->setRotulo ( 'Placa Alfanumérica' );
$obRdPlacaAlfaSim->setId     ( 'boPlacaAlfa' );
$obRdPlacaAlfaSim->setName   ( 'boPlacaAlfa' );
$obRdPlacaAlfaSim->setValue  ( 'true' );
$obRdPlacaAlfaSim->setLabel  ( 'Sim' );
$obRdPlacaAlfaSim->setNull   ( false );

$obRdPlacaAlfaNao = new Radio();
$obRdPlacaAlfaNao->setId     ( 'boPlacaAlfa' );
$obRdPlacaAlfaNao->setName   ( 'boPlacaAlfa' );
$obRdPlacaAlfaNao->setValue  ( 'false' );
$obRdPlacaAlfaNao->setLabel  ( 'Não' );
$obRdPlacaAlfaNao->setNull   ( false );

//seta com os valores do banco
if ($boPlacaAlfa == 'true') {
    $obRdPlacaAlfaSim->setChecked( true );
} else {
    $obRdPlacaAlfaNao->setChecked( true );
}
/****************************/
/* Configuracao da Coletora */
/****************************/
$obTxtColetoraDigitosLocal = new TextBox();
$obTxtColetoraDigitosLocal->setId        ( 'stColetoraDigitosLocal' );
$obTxtColetoraDigitosLocal->setName      ( 'stColetoraDigitosLocal' );
$obTxtColetoraDigitosLocal->setRotulo    ( 'Quantidade de Dígitos Local' );
$obTxtColetoraDigitosLocal->setTitle     ( 'Informe a quantidade de dígitos do local.' );
$obTxtColetoraDigitosLocal->setNull      ( false );
$obTxtColetoraDigitosLocal->setValue     ( $stColetoraDigitosLocal );

$obTxtColetoraDigitosPlaca = new TextBox();
$obTxtColetoraDigitosPlaca->setId        ( 'stColetoraDigitosPlaca' );
$obTxtColetoraDigitosPlaca->setName      ( 'stColetoraDigitosPlaca' );
$obTxtColetoraDigitosPlaca->setRotulo    ( 'Quantidade de Dígitos Placa' );
$obTxtColetoraDigitosPlaca->setTitle     ( 'Informe a quantidade de dígitos da placa.' );
$obTxtColetoraDigitosPlaca->setNull      ( false );
$obTxtColetoraDigitosPlaca->setValue     ( $stColetoraDigitosPlaca );

$obTxtColetoraCaracterSeparador = new TextBox();
$obTxtColetoraCaracterSeparador->setId    ( 'stColetoraCaracterSeparador' );
$obTxtColetoraCaracterSeparador->setName  ( 'stColetoraCaracterSeparador' );
$obTxtColetoraCaracterSeparador->setRotulo( 'Caracter Separador' );
$obTxtColetoraCaracterSeparador->setTitle ( 'Informe a quantidade de dígitos da placa.' );
$obTxtColetoraCaracterSeparador->setNull  ( true );
$obTxtColetoraCaracterSeparador->setValue ( $stColetoraCaracterSeparador );

$obVlrMinDepreciacao = new Numerico;
$obVlrMinDepreciacao->setName  ( 'flValorMinimoDepreciacao' );
$obVlrMinDepreciacao->setTitle ( 'Informe o Valor Mínimo do Bem para Depreciação.' );
$obVlrMinDepreciacao->setRotulo( 'Valor Mínimo do Bem para Depreciação ' );
$obVlrMinDepreciacao->setValue ( $flValorMinimoDepreciacao );
$obVlrMinDepreciacao->setNull  ( false );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-03.01.01');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnCodContaSintetica );
$obFormulario->addHidden    ( $obHdnNomContaSintetica );
$obFormulario->addTitulo    ( "Parâmetros do Módulo Patrimônio" );
$obFormulario->addComponente( $obTxtTransferencia );
$obFormulario->addComponente( $obIPopUpContaAtivo );
$obFormulario->agrupaComponentes( array( $obRdAlterarBensSim, $obRdAlterarBensNao ) );
$obFormulario->agrupaComponentes( array( $obRdPlacaAlfaSim, $obRdPlacaAlfaNao ) );
$obFormulario->addComponente ( $obVlrMinDepreciacao );

$obFormulario->addTitulo    ( "Configuração da Coletora de Dados" );
$obFormulario->addComponente( $obTxtColetoraDigitosLocal );
$obFormulario->addComponente( $obTxtColetoraDigitosPlaca );
$obFormulario->addComponente( $obTxtColetoraCaracterSeparador );

$obFormulario->OK();
$obFormulario->show();

?>
