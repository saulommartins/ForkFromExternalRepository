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
    * Página de Filtro para Relatório Despesas SIOPE
    * Data de Criação  : 20/06/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso : uc-02.01.40

    * $Id: FLDespesasSIOPE.php 62527 2015-05-18 17:44:34Z carlos.silva $

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );
require_once CAM_GF_ORC_COMPONENTES."ISelectOrgao.class.php";

$stPrograma = "DespesasSIOPE";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgGera = "OCGera".$stPrograma."php";

Sessao::remove('filtro');

$obForm = new Form;
$obForm->setTarget ( 'oculto' );
$obForm->setAction ( 'OCGeraDespesasSIOPE.php' );

//Definição dos componentes
$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval" );
$obHdnEval->setValue ( $stEval  );

//Definição dos componentes
$obHdnValidado = new HiddenEval;
$obHdnValidado->setName  ( "stValidado" );
$obHdnValidado->setValue ( 0 );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnStCtrl = new Hidden;
$obHdnStCtrl->setName ( "stCtrl" );
$obHdnStCtrl->setValue( $stCtrl );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue(CAM_GF_ORC_INSTANCIAS.'relatorios/FLDespesasSIOPE.php?pgGera='.$pgGera.'&'.Sessao::getId());

$stJs = "montaParametrosGET( 'inCodEntidade, stCodEntidade, inCodOrgao, stAcao' )";

$obISelectEntidade = new ISelectMultiploEntidadeUsuario();
$obISelectEntidade->SetNomeLista2("inCodEntidade");

$obInCodOrgao = new ISelectOrgao;
$obInCodOrgao->setExercicio( Sessao::getExercicio() );
$obInCodOrgao->setNull(false);

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio       ( Sessao::getExercicio() );
$obPeriodicidade->setValue           ( 4                  );
$obPeriodicidade->setValidaExercicio ( true               );
$obPeriodicidade->setObrigatorio     ( true               );

include_once CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php";
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades($obISelectEntidade);

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnStCtrl);
$obFormulario->addHidden($obHdnValidado);
$obFormulario->addHidden($obHdnCaminho);
$obFormulario->addTitulo("Dados para o filtro");
$obFormulario->addComponente($obISelectEntidade);
$obFormulario->addComponente($obInCodOrgao);
$obFormulario->addComponente($obPeriodicidade);
$obMontaAssinaturas->geraFormulario($obFormulario);

$obBtnOK = new Ok();
$obBtnOK->obEvento->setOnClick("BloqueiaFrames(true,false);Salvar();");

$obFormulario->defineBarra(array($obBtnOK));
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
