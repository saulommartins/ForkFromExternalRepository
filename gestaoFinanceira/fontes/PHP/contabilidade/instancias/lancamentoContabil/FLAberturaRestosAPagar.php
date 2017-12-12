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
    * Página de Formulário - Abertura Restos a Pagar

    * Data de Criação   : 20/12/2005

    * @author Analista: Valtair
    * @author Desenvolvedor: Carolina Schwaab Marcal

    * @ignore

    $Id: FLAberturaRestosAPagar.php 62541 2015-05-18 21:39:12Z arthur $

    $Revision: $
    $Name$
    $Autor: $
    $Date: $

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php" );
//Define o nome dos arquivos PHP
$stPrograma = "AberturaRestosAPagar";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
//****************************************//
// Define COMPONENTES DO FORMULARIO
//****************************************//

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obRConfiguracao = new RConfiguracaoConfiguracao;
$obRConfiguracao->setParametro('abertura_RP');
$obRConfiguracao->setExercicio( Sessao::getExercicio());
$obRConfiguracao->setCodModulo( 9 );
$obRConfiguracao->consultar($boTransacao);

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval"            );
$obHdnEval->setValue ( $stEval             );

if ( $obRConfiguracao->getValor() == 'T' ) {
    $stObs = "Este processo já foi executado! Se deseja prosseguir faça a Anulação de Restos à pagar primeiro!";
} else {
    $stObs = "Este processo é lento devido aos cálculos de restos a pagar. </br> Recomenda-se que o mesmo seja executado após o término do expediente.";
}

$obLblObs = new Label;
$obLblObs->setValue   ( $stObs );
$obLblObs->setRotulo  ( "Observação: " );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addTitulo( "Restos a Pagar Abertura do Exercício."        );
$obFormulario->addComponente($obLblObs);
$obBtnOk = new Ok(true);
if ( $obRConfiguracao->getValor() != 'T') {
    $obFormulario->defineBarra( array($obBtnOk) );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
