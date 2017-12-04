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
    * Data de Criação: 15/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Revision: 26126 $
    $Name$
    $Author: girardi $
    $Date: 2007-10-16 17:23:35 -0200 (Ter, 16 Out 2007) $

    * Casos de uso : uc-03.05.29
*/

/*
$Log:
*/

define("CASO_DE_USO", "UC-03.05.29");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php");

if ($_REQUEST['stAcao'] == 'incluir') {
    $stAcao = "incluir";
} elseif ($_REQUEST['stAcao'] == 'alterar') {
    $stAcao = "alterar";
} elseif ($_REQUEST['stAcao'] == 'anular') {
    $stAcao = "anular";
}

Sessao::remove('requestConvenioAditivo');

$stPrograma = "ManterAditivoConvenio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setId  ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtNumeroConvenio = new Inteiro;
$obTxtNumeroConvenio->setRotulo('Número do Convênio');
$obTxtNumeroConvenio->setName('inNumConvenio');
$obTxtNumeroConvenio->setId('inNumConvenio');
$obTxtNumeroConvenio->setTitle('Informe o número do convênio.');

$obTxtExercicioConvenio = new Inteiro;
$obTxtExercicioConvenio->setRotulo('Exercício do Convênio');
$obTxtExercicioConvenio->setName('stExercicio');
$obTxtExercicioConvenio->setId('stExercicio');
$obTxtExercicioConvenio->setTitle('Informe o exercício do convênio.');

$obDtConvenio = new Data;
$obDtConvenio->setRotulo('Data do Convênio');
$obDtConvenio->setName('dtConvenio');
$obDtConvenio->setId('dtConvenio');
$obDtConvenio->setTitle('Informe a data da assinatura do convênio.');

if ($stAcao != "incluir") {
    $obTxtNumeroAditivo = new Inteiro;
    $obTxtNumeroAditivo->setRotulo('Número do Aditivo');
    $obTxtNumeroAditivo->setName('inNumAditivo');
    $obTxtNumeroAditivo->setId('inNumAditivo');
    $obTxtNumeroAditivo->setTitle('Informe o número do aditivo.');

    $obTxtExercicioAditivo = new Inteiro;
    $obTxtExercicioAditivo->setRotulo('Exercício do Aditivo');
    $obTxtExercicioAditivo->setName('stExercicioAditivo');
    $obTxtExercicioAditivo->setId('stExercicioAditivo');
    $obTxtExercicioAditivo->setTitle('Informe o exercício do aditivo.');
}

//monta o popUp de contratado
$obParticipante = new IPopUpCGMVinculado( $obForm );
$obParticipante->setTabelaVinculo       ( 'licitacao.participante_convenio' );
$obParticipante->setCampoVinculo        ( 'cgm_fornecedor' );
$obParticipante->setNomeVinculo         ( 'Participante' );
$obParticipante->setRotulo              ( 'Participante' );
$obParticipante->setName                ( 'stParticipante');
$obParticipante->setId                  ( 'stParticipante');
$obParticipante->obCampoCod->setName    ( "inCodParticipante" );
$obParticipante->obCampoCod->setId      ( "inCodParticipante" );
$obParticipante->obCampoCod->setNull    ( true );
$obParticipante->setNull                ( true );
$obParticipante->setTitle("Informe o CGM do participante.");

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm );
$obFormulario->setAjuda         ( CASO_DE_USO );
$obFormulario->addHidden        ( $obHdnCtrl );
$obFormulario->addHidden        ( $obHdnAcao );
$obFormulario->addTitulo        ( "Dados para Filtro" );
$obFormulario->addComponente    ( $obTxtNumeroConvenio );
$obFormulario->addComponente    ( $obTxtExercicioConvenio );
$obFormulario->addComponente    ( $obDtConvenio );
if ($stAcao != "incluir") {
    $obFormulario->addComponente    ( $obTxtNumeroAditivo );
    $obFormulario->addComponente    ( $obTxtExercicioAditivo );
}
$obFormulario->addComponente    ( $obParticipante );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
