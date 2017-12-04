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
    * Página Formulário - Parâmetros do Arquivo RDEXTRA.
    * Data de Criação   : 14/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 12203 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 20:51:50 +0000 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.04
*/

/*
$Log$
Revision 1.7  2006/07/05 20:46:25  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );
include_once( CAM_GF_EXP_NEGOCIO . "RExportacaoTCERSArqRDExtra.class.php"    );
include_once( CAM_GF_CONT_COMPONENTES."IPopUpContaAnalitica.class.php");
SistemaLegado::BloqueiaFrames();
//Define o nome dos arquivos PHP
$stPrograma = "ManterExtra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
  $stAcao = "incluir";
}

$obRegra = new RContabilidadeLancamentoValor;
$obRegra->obRContabilidadePlanoContaAnalitica->recuperaMascaraConta( $stMascara );
$obRExportacaoTCERSArqRDExtra = new RExportacaoTCERSArqRDEXTRA();

SistemaLegado::executaFramePrincipal( "buscaDado('MontaListaSessao');" );

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define o objeto de controle de exclusão do itens da lista
$obHdnDel = new Hidden;
$obHdnDel->setName ( "inApagar" );
$obHdnDel->setValue( "" );

//Define o objeto BuscaInner para Popup do arquivo RD_EXTRA.
$obBscContaContabil = new IPopUpContaAnalitica( );
$obBscContaContabil->setRotulo                      ( "Conta Contábil" );
$obBscContaContabil->setTitle                       ( "Informe a conta contábil." );
$obBscContaContabil->setId                          ( "stNomContaContabil" );
$obBscContaContabil->setNull                        ( true                );
$obBscContaContabil->obCampoCod->setName            ( "stCodReduzido" );
$obBscContaContabil->obCampoCod->setId              ( "stCodReduzido" );
$obBscContaContabil->setTipoBusca                   ( "gpc_parametros_rd_extra" );

//Define o objeto Combo para a classificação
$obCmbClassificacao = new Select();
$obCmbClassificacao->setName      ( "inClassificacao" );
$obCmbClassificacao->setRotulo    ( "* Classificação" );
$obCmbClassificacao->addOption    ( "", "Selecione" );
$obCmbClassificacao->setCampoId   ( "cod_classificacao" );
$obCmbClassificacao->setCampoDesc ( "[cod_classificacao] - [nom_classificacao]" );
$obRExportacaoTCERSArqRDExtra->listaClassificacao( $rsClassificacao ) ;
$obCmbClassificacao->preencheCombo($rsClassificacao);
$obCmbClassificacao->setNull      ( true );
$obCmbClassificacao->setTitle     ( 'Selecione uma Classificação' );

//Define Span para Lista de contas contábeis
$obSpnExtra = new Span;
$obSpnExtra->setId ( "spnExtra" );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados para o arquivo" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnDel );

$obFormulario->addComponente     ( $obBscContaContabil         );
$obFormulario->addComponente     ( $obCmbClassificacao         );

$obBtnIncluir = new Button;
$obBtnIncluir->setName ( "btnIncluir" );
$obBtnIncluir->setValue( "Incluir" );
$obBtnIncluir->setTipo ( "button" );
$obBtnIncluir->obEvento->setOnClick ( "incluirdados( 'Incluir' )" );

$obFormulario->defineBarra(array($obBtnIncluir),"left","");

$obFormulario->addSpan( $obSpnExtra );

$obFormulario->defineBarra( array( new Ok(true) ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
