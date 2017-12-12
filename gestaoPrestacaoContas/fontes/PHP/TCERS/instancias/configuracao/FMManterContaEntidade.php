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
    * Página de Formulario de relacionamento entre Conta(Plano) e Entidade - TCE-RS
    * Data de Criação   : 09/02/2008

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Id: $

    * Casos de uso: uc-02.08.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCERS_MAPEAMENTO."TExportacaoTCERSPlanoContaEntidade.class.php" );
include_once ( CAM_GF_CONT_COMPONENTES."IPopUpContaAnalitica.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterContaEntidade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

Sessao::write('arValores', array());

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obBscPlano = new IPopUpContaAnalitica();
$obBscPlano->setRotulo                      ( "Código Reduzido" );
$obBscPlano->setTitle                       ( "Informe o Código Reduzido da Conta." );
$obBscPlano->setId                          ( "stNomContaDebito" );
$obBscPlano->setNull                        (  true             );
$obBscPlano->obCampoCod->setName            ( "stCodReduzido" );
$obBscPlano->obCampoCod->setId              ( "stCodReduzido" );
$obBscPlano->setTipoBusca                   ( "con_relac_conta_entidade" );

$obITextBoxSelectEntidadeGeral  = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidadeGeral->setNull(true);

$obSpnListaPlanoContaEntidade = new Span;
$obSpnListaPlanoContaEntidade->setID("spnListaPlanoContaEntidade");

// Define Objeto Button para Incluir Item
$obBtnIncluirPlanoContaEntidade = new Button;
$obBtnIncluirPlanoContaEntidade->setValue( "Incluir" );
$obBtnIncluirPlanoContaEntidade->setId("Incluir");
$obBtnIncluirPlanoContaEntidade->obEvento->setOnClick( "montaParametrosGET('inserirPlanoContaEntidade', '', '');");

// Define Objeto Button para Limpar Item
$obBtnLimparPlanoContaEntidade = new Button;
$obBtnLimparPlanoContaEntidade->setValue( "Limpar" );
$obBtnLimparPlanoContaEntidade->obEvento->setOnClick("LimparPlanoContaEntidade();");

//Monta FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addTitulo    ( "Configuração Estrutural do Plano de Contas/Entidade"     );
$obFormulario->addHidden    ( $obHdnAcao                     );
$obFormulario->addHidden    ( $obHdnCtrl                     );
$obFormulario->addComponente( $obBscPlano                    );
$obFormulario->addComponente( $obITextBoxSelectEntidadeGeral );
$obFormulario->agrupaComponentes( array( $obBtnIncluirPlanoContaEntidade, $obBtnLimparPlanoContaEntidade ));
$obFormulario->addSpan      ( $obSpnListaPlanoContaEntidade  );

$obOk  = new Ok;

$obFormulario->defineBarra( array( $obOk ) );
$obFormulario->show();

$stJs = "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','carregaListaPlanoContaEntidade');";
$jsOnLoad = $stJs;

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
