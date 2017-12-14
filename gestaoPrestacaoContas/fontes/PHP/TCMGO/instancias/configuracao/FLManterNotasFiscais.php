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
/*
    * Filtro para listagem dos contratos
    * Data de Criação   : 23/09/2008

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterNotasFiscais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$numEmpenho = '';

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList  );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao"            );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obTxtNroNota = new TextBox;
$obTxtNroNota->setName   ( "inNumNota"                  );
$obTxtNroNota->setRotulo ( "Número da Nota"             );
$obTxtNroNota->setTitle  ( "Informe o número da nota."  );
$obTxtNroNota->setNull   ( true                         );
$obTxtNroNota->setInteiro( true                         );

$obTxtSerieNota = new TextBox;
$obTxtSerieNota->setName   ( "inNumSerie"                     );
$obTxtSerieNota->setRotulo ( "Série da Nota Fiscal"           );
$obTxtSerieNota->setTitle  ( "Informe a série da nota fiscal.");
$obTxtSerieNota->setNull   ( true                             );

$obDtEmissao = new Data;
$obDtEmissao->setName   ( "dtEmissao"                          );
$obDtEmissao->setRotulo ( "Data de Emissão"                    );
$obDtEmissao->setTitle  ( 'Informe a data de emissão da nota.' );
$obDtEmissao->setNull   ( true                                 );

$obExercicio = new Exercicio;
$obExercicio->setRotulo        ( "Exercício"           );
$obExercicio->setTitle         ( "Exercício."		   );
$obExercicio->setName          ( "stExercicio"         );
$obExercicio->setInteiro       ( true                  );
$obExercicio->setMaxLength     ( 4                     );
$obExercicio->setSize          ( 4                     );
$obExercicio->setDefinicao     ( "stExercicio"         );
$obExercicio->setNull          ( FALSE                 );
$obExercicio->setValue         ( Sessao::getExercicio()    );

$obBscEmpenho = new BuscaInner;
$obBscEmpenho->setTitle            ( "Informe o número do empenho."  );
$obBscEmpenho->setRotulo           ( "Número do Empenho"             );
$obBscEmpenho->setId               ( "stEmpenho"                     );
$obBscEmpenho->setValue            ( isset($_REQUEST['stEmpenho'])   );
$obBscEmpenho->setMostrarDescricao ( true                            );
$obBscEmpenho->obCampoCod->setName ( "numEmpenho"                    );
$obBscEmpenho->obCampoCod->setId   ( "numEmpenho"                    );
$obBscEmpenho->obCampoCod->setValue(  $numEmpenho                    );
$obBscEmpenho->obCampoCod->obEvento->setOnBlur( "montaParametrosGET('buscaEmpenho','numEmpenho, inCodEntidade, stExercicio');" );

$obBscEmpenho->setFuncaoBusca("abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLEmpenho.php','frm','numEmpenho','stEmpenho','&stExercicioEmpenho='+document.frm.stExercicio.value+'&tipoBusca=buscaEmpenhoNota','".Sessao::getId()."','800','550');");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden          ( $obHdnAcao      );
$obFormulario->addHidden          ( $obHdnCtrl      );
$obFormulario->addComponente      ( $obTxtNroNota   );
$obFormulario->addComponente      ( $obTxtSerieNota );
$obFormulario->addComponente      ( $obDtEmissao    );
$obFormulario->addComponente      ( $obExercicio    );
$obFormulario->addComponente      ( $obBscEmpenho   );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
