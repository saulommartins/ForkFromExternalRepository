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
 * Arquivo de Formulário
 * Data de Criação: 25/10/2007

 * @author Analista: Dagiane	Vieira
 * @author Desenvolvedor: Diego Lemos de Souza

 * @ignore

 $Id: FMManterConfiguracaoRais.php 46943 2012-06-29 12:10:50Z tonismar $

 * Casos de uso: uc-04.08.12
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_COMPONENTES."ISelectMultiploEvento.class.php";
include_once CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TConfiguracaoTransparencia.class.php";

$stPrograma = "ConfiguracaoTransparencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

# Carrega as configurações salvas na base.
$jsOnload = "executaFuncaoAjax('preencherDados');";

$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao"  );
$obHdnAcao->setValue ( "alterar" );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );
$obHdnEval->setId("stEval");

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

# Form Exportação Automática
$obHashIdentificador = new TextBox;
$obHashIdentificador->setRotulo    ( "Identificador (Hash)"      );
$obHashIdentificador->setTitle     ( "Informe o código recebido para envio de arquivos ao Portal da Transparência. Esse código é de uso exclusivo do município." );
$obHashIdentificador->setName      ( "stHashIdentificador" );
$obHashIdentificador->setId        ( "stHashIdentificador" );
$obHashIdentificador->setValue     ( $stHashIdentificador);
$obHashIdentificador->setSize      ( 33 );
$obHashIdentificador->setMaxLength ( 32 );

$obCheckExportaAutomatico = new CheckBox;
$obCheckExportaAutomatico->setId("boExportaAutomatico");
$obCheckExportaAutomatico->setName("boExportaAutomatico");
$obCheckExportaAutomatico->setValue('true');
$obCheckExportaAutomatico->setLabel('Marque essa opção para o processo de geração e envio dos arquivos seja automático.');
$obCheckExportaAutomatico->setRotulo('Exportação Automática');

$exportacaoAutomatica = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'exporta_automatico'");

if ($exportacaoAutomatica == 'true') {
    $obCheckExportaAutomatico->setChecked(true);
}
# Fim Form Exportação Automática

$obISelectMultiploEventoRemuneracao = new ISelectMultiploEvento();
$obISelectMultiploEventoRemuneracao->setRotulo("Remuneração Eventual");
$obISelectMultiploEventoRemuneracao->SetNomeLista1("inCodEventoDisponiveisRemuneracao");
$obISelectMultiploEventoRemuneracao->SetNomeLista2("inCodEventoSelecionadosRemuneracao");
$obISelectMultiploEventoRemuneracao->setNull(true);
$obISelectMultiploEventoRemuneracao->setTitle("Selecione os eventos utilizados para pagamento de acertos de meses anteriores, exercícios anteriores ou decisões judiciais, e outras remunerações eventuais. ");
$obISelectMultiploEventoRemuneracao->setProventos();
$obISelectMultiploEventoRemuneracao->montarEventosDisponiveis();

$obISelectMultiploEventoRedutorTeto = new ISelectMultiploEvento();
$obISelectMultiploEventoRedutorTeto->setRotulo("Redutor de Teto");
$obISelectMultiploEventoRedutorTeto->SetNomeLista1("inCodEventoDisponiveisRedutorTeto");
$obISelectMultiploEventoRedutorTeto->SetNomeLista2("inCodEventoSelecionadosRedutorTeto");
$obISelectMultiploEventoRedutorTeto->setNull(true);
$obISelectMultiploEventoRedutorTeto->setTitle("Selecione os eventos utilizados para redução do teto na folha de pagamento.");
$obISelectMultiploEventoRedutorTeto->setDescontos();
$obISelectMultiploEventoRedutorTeto->montarEventosDisponiveis();

$obISelectMultiploEventoVerba = new ISelectMultiploEvento();
$obISelectMultiploEventoVerba->setRotulo("Verbas Indenizatórias");
$obISelectMultiploEventoVerba->SetNomeLista1("inCodEventoDisponiveisVerba");
$obISelectMultiploEventoVerba->SetNomeLista2("inCodEventoSelecionadosVerba");
$obISelectMultiploEventoVerba->setNull(true);
$obISelectMultiploEventoVerba->setTitle("Selecione os eventos utilizados para pagamento de abonos, auxílio alimentação, auxílio bolsas de estudos, etc.");
$obISelectMultiploEventoVerba->setProventos();
$obISelectMultiploEventoVerba->montarEventosDisponiveis();

$obISelectMultiploEventoDeducoes = new ISelectMultiploEvento();
$obISelectMultiploEventoDeducoes->SetNomeLista1("inCodEventoDisponiveisDeducoes");
$obISelectMultiploEventoDeducoes->SetNomeLista2("inCodEventoSelecionadosDeducoes");
$obISelectMultiploEventoDeducoes->setRotulo("Demais Deduções");
$obISelectMultiploEventoDeducoes->setNull(true);
$obISelectMultiploEventoDeducoes->setTitle("Selecione os eventos utilizados para pagamento de adiantamentos excluídos os descontos pessoais como pensão alimenticia/empréstimos");
$obISelectMultiploEventoDeducoes->setDescontos();
$obISelectMultiploEventoDeducoes->montarEventosDisponiveis();

$obISelectMultiploEventoJetons = new ISelectMultiploEvento();
$obISelectMultiploEventoJetons->SetNomeLista1("inCodEventoDisponiveisJetons");
$obISelectMultiploEventoJetons->SetNomeLista2("inCodEventoSelecionadosJetons");
$obISelectMultiploEventoJetons->setRotulo("Pagamento de Jetons");
$obISelectMultiploEventoJetons->setNull(true);
$obISelectMultiploEventoJetons->setTitle("Selecione os eventos utilizados para pagamento de Jetons");
$obISelectMultiploEventoJetons->setProventos();
$obISelectMultiploEventoJetons->montarEventosDisponiveis();

$obTxtOrgaoExecutivo = new TextBox;
$obTxtOrgaoExecutivo->setName        ( "inCodOrgaoExecutivo" );
$obTxtOrgaoExecutivo->setId          ( "inCodOrgaoExecutivo" );
$obTxtOrgaoExecutivo->setRotulo      ( "Órgão Poder Executivo" );
$obTxtOrgaoExecutivo->setTitle       ( "Informe o código do orgão relativo ao poder executivo");
$obTxtOrgaoExecutivo->setInteiro     ( true );
$obTxtOrgaoExecutivo->setSize        ( 3 );
$obTxtOrgaoExecutivo->setMaxLength   ( "5" );
$obTxtOrgaoExecutivo->setNull        ( false );

$obTxtUnidadeExecutivo = new TextBox;
$obTxtUnidadeExecutivo->setName        ( "inCodUnidadeExecutivo" );
$obTxtUnidadeExecutivo->setId          ( "inCodUnidadeExecutivo" );
$obTxtUnidadeExecutivo->setRotulo      ( "Unidade Poder Executivo" );
$obTxtUnidadeExecutivo->setTitle       ( "Informe o código do unidade relativo ao poder executivo");
$obTxtUnidadeExecutivo->setInteiro     ( true );
$obTxtUnidadeExecutivo->setSize        ( 3 );
$obTxtUnidadeExecutivo->setMaxLength   ( "5" );
$obTxtUnidadeExecutivo->setNull        ( false );

$obTxtOrgaoLegislativo = new Textbox;
$obTxtOrgaoLegislativo->setName        ( "inCodOrgaoLegislativo" );
$obTxtOrgaoLegislativo->setId          ( "inCodOrgaoLegislativo" );
$obTxtOrgaoLegislativo->setRotulo      ( "ÓrgãoPoder Legislativo" );
$obTxtOrgaoLegislativo->setTitle       ( "Informe o código do orgão relativo ao poder legislativo");
$obTxtOrgaoLegislativo->setInteiro     ( true );
$obTxtOrgaoLegislativo->setSize        ( 3 );
$obTxtOrgaoLegislativo->setMaxLength   ( "5" );
$obTxtOrgaoLegislativo->setNull        ( false );

$obTxtUnidadeLegislativo = new Textbox;
$obTxtUnidadeLegislativo->setName        ( "inCodUnidadeLegislativo" );
$obTxtUnidadeLegislativo->setId          ( "inCodUnidadeLegislativo" );
$obTxtUnidadeLegislativo->setRotulo      ( "Unidade Poder Legislativo" );
$obTxtUnidadeLegislativo->setTitle       ( "Informe o código do unidade relativo ao poder legislativo");
$obTxtUnidadeLegislativo->setInteiro     ( true );
$obTxtUnidadeLegislativo->setSize        ( 3 );
$obTxtUnidadeLegislativo->setMaxLength   ( "5" );
$obTxtUnidadeLegislativo->setNull        ( false );

$obTxtOrgaoRPPS = new Textbox;
$obTxtOrgaoRPPS->setName        ( "inCodOrgaoRPPS" );
$obTxtOrgaoRPPS->setId          ( "inCodOrgaoRPPS" );
$obTxtOrgaoRPPS->setRotulo      ( "Órgão RPPS" );
$obTxtOrgaoRPPS->setTitle       ( "Informe o código do orgão relativo ao RPPS");
$obTxtOrgaoRPPS->setInteiro     ( true );
$obTxtOrgaoRPPS->setSize        ( 3 );
$obTxtOrgaoRPPS->setMaxLength   ( "5" );

$obTxtUnidadeRPPS = new Textbox;
$obTxtUnidadeRPPS->setName        ( "inCodUnidadeRPPS" );
$obTxtUnidadeRPPS->setId          ( "inCodUnidadeRPPS" );
$obTxtUnidadeRPPS->setRotulo      ( "Unidade RPPS" );
$obTxtUnidadeRPPS->setTitle       ( "Informe o código do unidade relativo ao RPPS");
$obTxtUnidadeRPPS->setInteiro     ( true );
$obTxtUnidadeRPPS->setSize        ( 3 );
$obTxtUnidadeRPPS->setMaxLength   ( "5" );

$obTxtOrgaoOutros = new Textbox;
$obTxtOrgaoOutros->setName        ( "inCodOrgaoOutros" );
$obTxtOrgaoOutros->setId          ( "inCodOrgaoOutros" );
$obTxtOrgaoOutros->setRotulo      ( "Órgão Outros" );
$obTxtOrgaoOutros->setTitle       ( "Informe o código do orgão para outros poderes");
$obTxtOrgaoOutros->setInteiro     ( true );
$obTxtOrgaoOutros->setSize        ( 3 );
$obTxtOrgaoOutros->setMaxLength   ( "5" );

$obTxtUnidadeOutros = new Textbox;
$obTxtUnidadeOutros->setName        ( "inCodUnidadeOutros" );
$obTxtUnidadeOutros->setId          ( "inCodUnidadeOutros" );
$obTxtUnidadeOutros->setRotulo      ( "Unidade Outros" );
$obTxtUnidadeOutros->setTitle       ( "Informe o código do unidade para outros poderes");
$obTxtUnidadeOutros->setInteiro     ( true );
$obTxtUnidadeOutros->setSize        ( 3 );
$obTxtUnidadeOutros->setMaxLength   ( "5" );
$obBtnOk = new Ok;

$obBtnLimpar = new Button();
$obBtnLimpar->setName		   ( "btnLimpar" );
$obBtnLimpar->setValue             ( "Limpar" );
$obBtnLimpar->setTitle		   ( "Clique para limpar os dados dos campos." );
$obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limpar', '', true);" );

$obFormulario = new Formulario;
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addForm	 ( $obForm );

$obFormulario->addTitulo("Exportação Automática");
$obFormulario->addComponente ($obHashIdentificador);
$obFormulario->addComponente ($obCheckExportaAutomatico);

$obFormulario->addTitulo("Configuração Transparência");
$obFormulario->addComponente($obISelectMultiploEventoRemuneracao);
$obFormulario->addComponente($obISelectMultiploEventoRedutorTeto);
$obFormulario->addComponente($obISelectMultiploEventoVerba);
$obFormulario->addComponente($obISelectMultiploEventoDeducoes);
$obFormulario->addComponente($obISelectMultiploEventoJetons);

$obFormulario->addTitulo ( "Configuração de Órgão/Unidade"     );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnEval , true);
$obFormulario->addComponente( $obTxtOrgaoExecutivo );
$obFormulario->addComponente( $obTxtUnidadeExecutivo );
$obFormulario->addComponente( $obTxtOrgaoLegislativo );
$obFormulario->addComponente( $obTxtUnidadeLegislativo );
$obFormulario->addComponente( $obTxtOrgaoRPPS );
$obFormulario->addComponente( $obTxtUnidadeRPPS );
$obFormulario->addComponente( $obTxtOrgaoOutros );
$obFormulario->addComponente( $obTxtUnidadeOutros );
$obFormulario->defineBarra( array( $obBtnOk, $obBtnLimpar ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
