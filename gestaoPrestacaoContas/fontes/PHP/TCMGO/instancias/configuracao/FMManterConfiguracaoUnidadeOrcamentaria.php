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
    * Data de Criação   : 11/01/2011

    * @author Carlos Adriano

    * @ignore
    *
    * $Id: FMManterConfiguracaoUnidadeOrcamentaria.php 45121 2011-01-27 19:52:49Z silvia $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");
include_once(CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );
include_once(TTGO."TTGOTipoOrgao.class.php");
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
//include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php" );
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php" );

$stPrograma = "ManterConfiguracaoUnidadeOrcamentaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include( $pgJs );
$stAcao = $request->get('stAcao');

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTOrcamentoOrgao = new TOrcamentoOrgao();
$obTOrcamentoOrgao->setDado('exercicio', Sessao::getExercicio());
$obTOrcamentoOrgao->recuperaDadosExercicio( $rsOrgao );

$obCmbOrgao = new Select();
$obCmbOrgao->setRotulo( 'Orgão' );
$obCmbOrgao->setTitle( 'Selecione o orgão' );
$obCmbOrgao->setName( 'inOrgao' );
$obCmbOrgao->setId( 'inOrgao' );
$obCmbOrgao->addOption( '', 'Selecione' );
$obCmbOrgao->setCampoId( 'num_orgao' );
$obCmbOrgao->setCampoDesc( 'nom_orgao' );
$obCmbOrgao->setStyle('width: 520');
$obCmbOrgao->obEvento->setOnChange('buscaUnidade(this);');
$obCmbOrgao->preencheCombo( $rsOrgao );
$obCmbOrgao->setNull( false );

$obSpanUnidade = new Span;
$obSpanUnidade->setId('spnUnidade');

$obSpanConfiguracao = new Span;
$obSpanConfiguracao->setId('spnConfiguracao');

//****************************************//
// Monta formulário
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addTitulo('Configuração da Unidade Orçamentária');

$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);

$obFormulario->addComponente($obCmbOrgao);
$obFormulario->addSpan($obSpanUnidade);
$obFormulario->addSpan($obSpanConfiguracao);

$obOk = new Ok();
$obLimpar = new Limpar();
//$obLimpar->obEvento->setOnClick('limparOrdem();');
$obFormulario->defineBarra(array($obOk, $obLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
