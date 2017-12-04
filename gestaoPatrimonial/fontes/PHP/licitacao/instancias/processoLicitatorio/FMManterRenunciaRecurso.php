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
    * Página de Formulário para manter participantes
    * Data de Criação   : 06/10/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Maicon Brauwers

    * @ignore

    * Casos de uso : uc-03.05.18

    * $Id: FMManterManutencaoParticipante.php 57380 2014-02-28 17:45:35Z diogo.zarpelon $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
include_once CAM_GP_LIC_COMPONENTES."IPopUpNumeroEdital.class.php";

//Definições padrões do framework
$stPrograma = "ManterRenunciaRecurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

if (empty($stAcao))
  $stAcao = 'manter';

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

/*
 * Definição dos componentes(objetos) que irão ser adicionados no formulário
 */
//Define o form que será incluido no formulário (padrão no framework)
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );
//Define o Hidden de ação (padrão no framework)
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );
//Define o Hidde de controle (padrão no framework)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

include_once(CAM_GP_LIC_COMPONENTES."IMontaNumeroLicitacao.class.php");

$obPopUpNumeroEdital = new IPopUpNumeroEdital( $obForm );
$obPopUpNumeroEdital->obCampoCod->setId("numEdital");
$obPopUpNumeroEdital->obCampoCod->setName ( "numEdital" );
$obPopUpNumeroEdital->setNull(false);
$obPopUpNumeroEdital->setId('');
$obPopUpNumeroEdital->obCampoCod->obEvento->setOnBlur("if (jQuery(this).val() != '') { ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&numEdital='+this.value,'exibeLicitacao'); }");

//$obObjetoLic = new ILabelObjetoProcessoLicitatorio();
$obObjetoLic = new Hidden;
$obObjetoLic->setName("objetoLicitatorio");
$obObjetoLic->setId("objetoLicitatorio");
$obObjetoLic->setRotulo("Objeto");
$obObjetoLic->setValue( $_REQUEST['objetoProcessoLicitatorio'] );

// Define objeto span para lista de participantes da licitação
$obSpnListaParticipantes = new Span;
$obSpnListaParticipantes->setId("spnListaParticipantesLic");

// Define objeto span para lista de participantes da licitação
$obSpnEdital = new Span;
$obSpnEdital->setId("spnEdital");

/*
 * Define o formulário
 */
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                        );
//Define o caminho de ajuda do Caso de uso (padrão no Framework)
$obFormulario->setAjuda         ("UC-03.05.18"                   );
$obFormulario->addHidden        ( $obHdnCtrl                     );
$obFormulario->addHidden        ( $obHdnAcao                     );

$obFormulario->addTitulo        ( "Renúncia ao Prazo de Recurso" );
$obFormulario->addComponente    ( $obPopUpNumeroEdital );
$obFormulario->addHidden        ( $obObjetoLic );
$obFormulario->addSpan          ( $obSpnEdital );

//lista dos participantes
$obFormulario->addSpan          ( $obSpnListaParticipantes );

$obBtnOk = new Ok();
$obBtnCancelar = new Limpar();
$obBtnCancelar->setValue('Cancelar');

$obFormulario->defineBarra(array($obBtnOk , $obBtnCancelar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
