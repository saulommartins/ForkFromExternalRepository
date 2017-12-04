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
    * Página de Formulário para configuração
    * Data de Criação   : 22/01/2007

    * @author Henrique Boaventura

    * @ignore

    * Casos de uso : uc-06.03.00

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");
include_once(CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );
include_once(TTPB."TTPBConfiguracaoEntidade.class.php");

$stPrograma = "ManterOrcamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Instancia o componente ITextBoxSelectEntidadeGeral
$obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidadeGeral->setNull( false );
$obITextBoxSelectEntidadeGeral->obTextBox->obEvento->setOnchange("montaParametrosGET('buscaDadosEntidade','inCodEntidade');");
$obITextBoxSelectEntidadeGeral->obSelect->obEvento->setOnchange("montaParametrosGET('buscaDadosEntidade','inCodEntidade');");

//Cria um label para o ano de vigencia da lei orcamentaria
$obLblAnoVigencia = new Label();
$obLblAnoVigencia->setRotulo('Ano Vigência');
$obLblAnoVigencia->setValue( Sessao::getExercicio() );

//Cria um campo data para a data de aprovacao da LOA
$obDtAprovacaoLOA = new Data();
$obDtAprovacaoLOA->setRotulo( 'Data de Aprovação da LOA');
$obDtAprovacaoLOA->setName('dtAprovacaoLOA');
$obDtAprovacaoLOA->setId('dtAprovacaoLOA');
$obDtAprovacaoLOA->setNull( false );

//Cria um campo para o numero da lei orcamentaria
$obInNumeroLei = new Inteiro();
$obInNumeroLei->setRotulo('Número da Lei Orçamentária');
$obInNumeroLei->setName('numLeiOrcamentaria');
$obInNumeroLei->setId('numLeiOrcamentaria');
$obInNumeroLei->setNull( false );
$obInNumeroLei->setMaxLength( 8 );

//Cria um campo data para a data de aprovacao da LDO
$obDtAprovacaoLDO = new Data();
$obDtAprovacaoLDO->setRotulo('Data de Aprovação da LDO');
$obDtAprovacaoLDO->setName('dtAprovacaoLDO');
$obDtAprovacaoLDO->setId('dtAprovacaoLDO');
$obDtAprovacaoLDO->setNull( false );

//Cria um campo para o numero da LDO
$obInNumeroLDO = new Inteiro();
$obInNumeroLDO->setRotulo('Número da LDO');
$obInNumeroLDO->setName('numLDO');
$obInNumeroLDO->setId('numLDO');
$obInNumeroLDO->setNull( false );
$obInNumeroLDO->setMaxLength( 8 );

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addComponente( $obITextBoxSelectEntidadeGeral );
$obFormulario->addComponente( $obLblAnoVigencia );
$obFormulario->addComponente( $obDtAprovacaoLOA );
$obFormulario->addComponente( $obInNumeroLei );
$obFormulario->addComponente( $obDtAprovacaoLDO );
$obFormulario->addComponente( $obInNumeroLDO );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
