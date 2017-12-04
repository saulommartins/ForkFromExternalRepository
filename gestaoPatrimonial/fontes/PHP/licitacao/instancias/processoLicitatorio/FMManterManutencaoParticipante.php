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

    * $Id: FMManterManutencaoParticipante.php 61786 2015-03-03 21:11:42Z arthur $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
include_once CAM_GP_LIC_COMPONENTES."IPopUpNumeroEdital.class.php";

//Definições padrões do framework
$stPrograma = "ManterManutencaoParticipante";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$stAcao = $request->get('stAcao');

if (empty($stAcao))
  $stAcao = 'manter';
  
$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

// Recupera a licitação atraves do request
$jsOnload = "executaFuncaoAjax('exibeLicitacao','"."&numEdital=".$request->get('stNumEdital').".&inCodModalidade=".$request->get('inCodModalidade')."&stExercicioLicitacao=".$request->get('stExercicioLicitacao')."&inCodLicitacao=".$request->get('inCodLicitacao')."&inCodEntidade=".$request->get('inCodEntidade')."');";

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

$obHdnEdital = new Hidden;
$obHdnEdital->setName ("numEdital");
$obHdnEdital->setId   ("numEdital");
$obHdnEdital->setValue($request->get('stNumEdital'));

$obObjetoLic = new Hidden;
$obObjetoLic->setName("objetoLicitatorio");
$obObjetoLic->setId("objetoLicitatorio");
$obObjetoLic->setRotulo("Objeto");
$obObjetoLic->setValue( $_REQUEST['objetoProcessoLicitatorio'] );

// Define objeto span para lista de participantes da licitação
$obSpnListaParticipantes = new Span;
$obSpnListaParticipantes->setId("spnListaParticipantesLic");

// Define objeto span para lista de participantes da licitação
$obSpnCGMConsorcio = new Span;
$obSpnCGMConsorcio->setId("spnCGMConsorcio");

// Define objeto span para lista de participantes da licitação
$obSpnEdital = new Span;
$obSpnEdital->setId("spnEdital");

$obSpnParticipante = new Span();
$obSpnParticipante->setId( 'spnParticipante' );

/*
 * Define o formulário
 */
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                        );

//Define o caminho de ajuda do Caso de uso (padrão no Framework)
$obFormulario->setAjuda         ("UC-03.05.18"                   );
$obFormulario->addHidden        ( $obHdnCtrl                     );
$obFormulario->addHidden        ( $obHdnAcao                     );
$obFormulario->addHidden        ( $obHdnEdital                   );


$obFormulario->addTitulo        ( "Manutenção de Participantes da Licitação"  );
$obFormulario->addHidden        ( $obObjetoLic );
$obFormulario->addSpan          ( $obSpnEdital );

$obFormulario->addTitulo        ( "Participante"     );
$obFormulario->addSpan          ( $obSpnParticipante );

//lista dos participantes
$obFormulario->addTitulo        ( "Participantes da Licitação"  );
$obFormulario->addSpan          ( $obSpnListaParticipantes );

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>