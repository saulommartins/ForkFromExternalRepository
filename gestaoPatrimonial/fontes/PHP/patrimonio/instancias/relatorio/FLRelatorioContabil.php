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
    * Data de Criação: 22/04/2015

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    $Id: FLRelatorioContabil.php 66139 2016-07-21 14:22:58Z lisiane $

    * Casos de uso: uc-03.01.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_COMPONENTES.'ISelectEspecie.class.php';
include_once CAM_GP_PAT_COMPONENTES."IMontaClassificacao.class.php";
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioContabil";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgGera = "OCGera".$stPrograma.".php";
$pgJs = "JS".$stPrograma.".js";

include $pgJs;

$obForm = new Form;
$obForm->setAction( $pgGera );

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

$obISelectEntidade = new ITextBoxSelectEntidadeUsuario();
$obISelectEntidade->obTextBox->setNull(false);
$obISelectEntidade->setNull(false);

//instancia o componente IMontaClassificacao
$obIMontaClassificacao = new IMontaClassificacao( $obFormClass );
$obIMontaClassificacao->setNull( true );

//instancia componente TextBox para o ano do empenho
$obExercicioEmpenho = new Exercicio();
$obExercicioEmpenho->setRotulo( 'Exercício' );
$obExercicioEmpenho->setNull( false);
$obExercicioEmpenho->setId( 'stExercicio' );
$obExercicioEmpenho->setValue( Sessao::getExercicio() );
$obExercicioEmpenho->obEvento->setOnChange("montaParametrosGET( 'validaExercicio' );");

//instancia um componente periodicidade
$obPeriodicidadeIncorporacao = new Periodicidade();
$obPeriodicidadeIncorporacao->setIdComponente( 'Incorporacao' );
$obPeriodicidadeIncorporacao->setRotulo( 'Periodicidade de Incorporação');
$obPeriodicidadeIncorporacao->setTitle( 'Selecione o Período de Incorporação.' );
$obPeriodicidadeIncorporacao->obPeriodicidade->setId('inPeriodicidadeIncorporacao');
$obPeriodicidadeIncorporacao->obDataInicial->setId('stDataInicialIncorporacao');
$obPeriodicidadeIncorporacao->obDataFinal->setId('stDataFinalIncorporacao');
$obPeriodicidadeIncorporacao->setNull( true );
$obPeriodicidadeIncorporacao->setExercicio ( Sessao::getExercicio() );

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm );
$obFormulario->addHidden        ( $obHdnAcao );
$obFormulario->addHidden        ( $obHdnCtrl );
$obFormulario->setAjuda         ("UC-03.01.09");
$obFormulario->addTitulo        ( 'Dados para o Filtro' );
$obFormulario->addComponente    ( $obExercicioEmpenho   );
$obFormulario->addComponente    ( $obISelectEntidade    );
$obIMontaClassificacao->geraFormulario( $obFormulario   );
$obFormulario->addComponente    ( $obPeriodicidadeIncorporacao );

// Botao de Ok
$obBtnOK = new Ok();
$obBtnOK->obEvento->setOnClick("validaCampos();");

// Botao de Limpar
$obBtnLimpar = new Limpar;

$arBotoes = array($obBtnOK, $obBtnLimpar);

$obFormulario->defineBarra( $arBotoes );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
