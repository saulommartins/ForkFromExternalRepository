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
  * Página de Filtro para Relatório da Log do Cálculo
  * Data de Criação   : 11/09/2008

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Diego Bueno Coelho

  * @ignore

  * $Id: $

  * Casos de uso:
  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/componentes/MontaGrupoCredito.class.php';
include_once CAM_GT_CIM_COMPONENTES."IPopUpImovelIntervalo.class.php";
include_once CAM_GT_CEM_COMPONENTES."IPopUpEmpresaIntervalo.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioLogCalculo";
$pgFilt     = "FL".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

include_once $pgJS;

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgOcul );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
if (isset($stCtrl)) {
    $obHdnCtrl->setValue( $stCtrl );
}

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick( "BloqueiaFrames(true,false); submeteFiltro();" );

$onBtnLimpar = new Limpar;

$obSpnFiltro = new Span;
$obSpnFiltro->setId ( 'spnFiltro' );

$obMontaGrupoCredito = new MontaGrupoCredito;

$obPopUpImovel = new IPopUpImovelIntervalo;
$obPopUpImovel->setVerificaInscricao   ( false );

$obPopUpEmpresa = new IPopUpEmpresaIntervalo;
$obPopUpEmpresa->setVerificaInscricao    ( false );

$obRbdSituacaoErro = new Radio;
$obRbdSituacaoErro->setName  ( 'stSituacao' );
$obRbdSituacaoErro->setValue ( 'E' );
$obRbdSituacaoErro->setLabel ( 'Com Erro' );
$obRbdSituacaoErro->setNull  ( false );
$obRbdSituacaoErro->setTitle ( 'Situação do Cálculo' );
$obRbdSituacaoErro->setRotulo( 'Situação do Cálculo' );

$obRbdSituacaoCorreto = new Radio;
$obRbdSituacaoCorreto->setName  ( 'stSituacao' );
$obRbdSituacaoCorreto->setValue ( 'C' );
$obRbdSituacaoCorreto->setLabel ( 'Correto' );
$obRbdSituacaoCorreto->setNull  ( false );
$obRbdSituacaoCorreto->setTitle ( 'Situação do Cálculo' );
$obRbdSituacaoCorreto->setRotulo( 'Situação do Cálculo' );

$obRbdSituacaoTodos = new Radio;
$obRbdSituacaoTodos->setName  ( 'stSituacao' );
$obRbdSituacaoTodos->setValue ( 'T' );
$obRbdSituacaoTodos->setLabel ( 'Todos' );
$obRbdSituacaoTodos->setNull  ( false );
$obRbdSituacaoTodos->setTitle ( 'Situação do Cálculo' );
$obRbdSituacaoTodos->setRotulo( 'Situação do Cálculo' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );

$obFormulario->addHidden    ( $obHdnAcao            );
$obFormulario->addHidden    ( $obHdnCtrl            );
$obFormulario->addTitulo    ( "Dados para filtro"   );

$obMontaGrupoCredito->geraFormulario( $obFormulario, true, false );
$obPopUpImovel->geraFormulario      ( $obFormulario     );
$obPopUpEmpresa->geraFormulario     ( $obFormulario     );

$obFormulario->agrupaComponentes (array($obRbdSituacaoErro, $obRbdSituacaoCorreto, $obRbdSituacaoTodos) );

$obFormulario->addSpan ($obSpnFiltro );
$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
$obFormulario->show();

?>
