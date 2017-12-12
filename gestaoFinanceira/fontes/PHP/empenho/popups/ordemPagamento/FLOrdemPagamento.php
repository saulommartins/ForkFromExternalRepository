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
* Arquivo instância para popup de Ordem Pagamento
* Data de Criação: 15/02/2006

* @author Analista: Lucas Leusin Oaigen
* @author Desenvolvedor: Jose Eduardo Porto

$Revision: 30805 $
$Name$
$Author: cleisson $
$Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

Casos de uso: uc-02.04.20
*/

/*
$Log$
Revision 1.3  2006/07/05 20:49:46  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"                                      );

//Define o nome dos arquivos PHP
$stPrograma = "OrdemPagamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include( $pgJS );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->setDataBoletim( date( 'd/m/'.Sessao::getExercicio() ) );
$obRTesourariaBoletim->addArrecadacao();
$obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

$nomForm 				= $_REQUEST['nomForm'];
$campoNum 				= $_REQUEST['campoNum'];
$campoNom 				= $_REQUEST['campoNom'];
$stAcao 				= $_REQUEST['stAcao'];
$stCtrl 				= $_REQUEST['stCtrl'];
$inCodOrdemPagamento 	= $_REQUEST['inCodOrdemPagamento'];

//destroi arrays de sessao que armazenam os da dos do FILTRO
Sessao::remove('filtro');
Sessao::remove('link');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
//$obForm->setTarget( "telaPrincipal" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $nomForm );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "stTipoBusca" );
$obHdnTipoBusca->setValue( $_GET['tipoBusca'] );
if($_GET['tipoBusca']=='usuario') $_GET['tipoBusca']='fisica';

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $campoNum );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $campoNom );

//Definne Hidden para buscar OP de contas com mesmo Recurso/Fonte
if ( $_REQUEST['inCodPlano'] ) {
    $obHdnCodPlano = new Hidden;
    $obHdnCodPlano->setName( "hdnCodPlano" );
    $obHdnCodPlano->setValue( $_REQUEST['inCodPlano'] );    
}

//Define HIDDEN para a entidade
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName( "inCodEntidade" );
$obHdnCodEntidade->setValue( $_REQUEST['inCodEntidade'] );

//Define Objeto Text para o Exercicio
$obTxtExercicio = new TextBox;
$obTxtExercicio->setName      ( "stExercicio"         );
$obTxtExercicio->setValue     ( Sessao::getExercicio()    );
$obTxtExercicio->setRotulo    ( "Exercício"           );
$obTxtExercicio->setTitle     ( "Informe o Exercício da Ordem de Pagamento" );
$obTxtExercicio->setNull      ( true                  );
$obTxtExercicio->setMaxLength ( 4                     );
$obTxtExercicio->setSize      ( 5                     );

////Define Objeto TextBox para Ordem de Pagamento
$obTxtOrdemPagamento = new TextBox();
$obTxtOrdemPagamento->setTitle     ( 'Informe o número da Ordem de Pagamento'              );
$obTxtOrdemPagamento->setRotulo    ( 'Nr. Ordem de Pagamento'                              );
$obTxtOrdemPagamento->setName      ( 'inCodOrdemPagamento'                                 );
$obTxtOrdemPagamento->setId        ( 'inCodOrdemPagamento'                                 );
$obTxtOrdemPagamento->setInteiro   ( true                                                  );
$obTxtOrdemPagamento->setNull      ( true                                                  );
$obTxtOrdemPagamento->setSize      ( 25                                                    );
$obTxtOrdemPagamento->setMaxLength ( 20                                                    );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnForm );
$obFormulario->addHidden( $obHdnTipoBusca );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addHidden( $obHdnCodEntidade );
if ( $_REQUEST['inCodPlano'] ) 
    $obFormulario->addHidden( $obHdnCodPlano );
$obFormulario->addTitulo( "Dados para Ordem de Pagamento" );
$obFormulario->addComponente( $obTxtExercicio );
$obFormulario->addComponente( $obTxtOrdemPagamento );
$obFormulario->OK();
$obFormulario->show();

$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
//$obIFrame->setSrc("../../../includes/mensagem.php?".Sessao::getId());
$obIFrame->setHeight("10%");
$obIFrame->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
