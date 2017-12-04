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
    * Página de Formulario de Ajustes do ContaCont.txt com o Elenco de Contas do TCERJ
    * Data de Criação   : 26/07/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-07-28 11:18:27 -0300 (Sex, 28 Jul 2006) $

    * Casos de uso: uc-02.08.16
*/

/*
$Log$
Revision 1.1  2006/07/28 14:14:49  cako
Bug #6568#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAjustesContaCont";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;

//Recupera Mascara
$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->recuperaMascaraConta( $stMascara );

$obTxtCodEstrutural = new TextBox;
$obTxtCodEstrutural->setName             ( "stCodEstrutural" );
$obTxtCodEstrutural->setValue            ( $stCodEstrutural );
$obTxtCodEstrutural->setRotulo           ( "Conta" );
$obTxtCodEstrutural->setMascara          ( $stMascara );
$obTxtCodEstrutural->setPreencheComZeros ( 'D' );
$obTxtCodEstrutural->setNull             ( false );
$obTxtCodEstrutural->setTitle            ( 'Informe o código estrutural da conta' );
$obTxtCodEstrutural->obEvento->setOnKeyPress( "return validaExpressao( this, event, '[0-9.]');" );

$obSpanListaContas = new Span();
$obSpanListaContas->setId( "spnListaContas");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addTitulo    ( "Ajustes do Elenco de Contas do TCE"     );
$obFormulario->addHidden    ( $obHdnAcao            );
$obFormulario->addHidden    ( $obHdnCtrl            );
//$obFormulario->addComponente( $obTxtCodEstrutural   );

$obOk  = new Ok;
$obOk->setStyle ( "display: none");
$obOk->setId ("btnOk");

$obBtnFiltrar = new Button;
$obBtnFiltrar->setValue( "Filtrar" );
$obBtnFiltrar->obEvento->setOnClick( "montaParametrosGET( 'montaListaContas', 'stCodEstrutural');" );

$obBtnLimpar = new Button;
$obBtnLimpar->setValue ("Limpar");
$obBtnLimpar->obEvento->setOnClick( "executaFuncaoAjax('limpaListaContas');" );

$obFormulario->agrupaComponentes( array ( $obTxtCodEstrutural, $obBtnFiltrar, $obBtnLimpar) );

$obFormulario->addSpan ( $obSpanListaContas );

$obFormulario->defineBarra( array( $obOk ) );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
