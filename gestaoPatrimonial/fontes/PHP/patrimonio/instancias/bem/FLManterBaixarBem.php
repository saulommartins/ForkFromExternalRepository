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
    * Data de Criação: 24/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 26727 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-11-12 16:31:31 -0200 (Seg, 12 Nov 2007) $

    * Casos de uso: uc-03.01.06
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_COMPONENTES.'IIntervaloPopUpBem.class.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioTipoBaixa.class.php';

$stPrograma = "ManterBaixarBem";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get("stAcao");

$obTPatrimonioTipoBaixa = new TPatrimonioTipoBaixa();
$obTPatrimonioTipoBaixa->recuperaTodos($rsTipoBaixa, " ORDER BY cod_tipo");

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgList);

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//instancia o componente iintervalopopupbem
$obIIntervaloPopUpBem = new IIntervaloPopUpBem( $obForm );

//instancia o componente periodicidade
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio( Sessao::getExercicio() );
$obPeriodicidade->setRotulo( 'Data de Baixa' );
$obPeriodicidade->setTitle( 'Informe o intervalo da data de baixa.' );

$obCmbTipoBaixa = new Select();
$obCmbTipoBaixa->setName       ( "inTipoBaixa"   );
$obCmbTipoBaixa->setRotulo     ( "Tipo da baixa" );
$obCmbTipoBaixa->setId         ( "inTipoBaixa"   );
$obCmbTipoBaixa->setCampoId    ( "cod_tipo"      );
$obCmbTipoBaixa->setCampoDesc  ( "[cod_tipo] - [descricao]" );
$obCmbTipoBaixa->addOption     ( '','Selecione'  );
$obCmbTipoBaixa->preencheCombo ( $rsTipoBaixa    ); 

$obFormulario = new Formulario();
$obFormulario->setAjuda('UC-03.01.06');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addTitulo    ( "Dados para o Filtro" );
$obFormulario->addComponente( $obIIntervaloPopUpBem );
$obFormulario->addComponente( $obPeriodicidade );
$obFormulario->addComponente( $obCmbTipoBaixa );
$obFormulario->Ok();
$obFormulario->show();

?>