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
    * Página de Formulario de Inclusao/Alteracao de Autorização
    * Data de Criação   : 23/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: gelson $
    $Date: 2007-02-23 13:15:05 -0200 (Sex, 23 Fev 2007) $

    * Casos de uso: uc-02.03.02
                    uc-02.01.08
*/

/*
$Log$
Revision 1.8  2007/02/23 15:15:05  gelson
Sempre que for autorização tem que ir a reserva. Adicionado em todos arquivos o caso de uso da reserva.

Revision 1.7  2006/07/05 20:47:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"     );
include_once ( CAM_FW_HTML."MontaAtributos.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacaoLicitacao";
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

$obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
$obROrcamentoConfiguracao     = new ROrcamentoConfiguracao;

$obROrcamentoConfiguracao->setExercicio( Sessao::getExercicio() );
$obROrcamentoConfiguracao->setCodModulo( 8 );
$obROrcamentoConfiguracao->consultarConfiguracao();
$inCodEntidadePrefeitura = $obROrcamentoConfiguracao->getCodEntidadePrefeitura();

$obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->setExercicio( Sessao::getExercicio() );
$obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

$obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
$stMascaraRubrica = $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();

$obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

Sessao::remove('arItens');

SistemaLegado::executaFramePrincipal( "BloqueiaFrames(true,false); buscaDado( 'montaListaLicitacao' );" );

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

//Define o objeto hidden para codigo da licitação
$obHdnCodLicitacao = new Hidden;
$obHdnCodLicitacao->setName ( "inCodLicitacao" );
$obHdnCodLicitacao->setValue( $_REQUEST['inCodLicitacao'] );

//Define o objeto hidden para tipo da modalidade
$obHdnTipoModalidade = new Hidden;
$obHdnTipoModalidade->setName ( "stTipoModalidade" );
$obHdnTipoModalidade->setValue( $_REQUEST['stTipoModalidade'] );

// Define Objeto TextBox para Codigo da Entidade
$obTxtCodEntidade = new TextBox;
$obTxtCodEntidade->setName   ( "inCodEntidade"          );
$obTxtCodEntidade->setId     ( "inCodEntidade"          );
$obTxtCodEntidade->setValue  ( $inCodEntidadePrefeitura );
$obTxtCodEntidade->setRotulo ( "Entidade"               );
$obTxtCodEntidade->setTitle  ( "Selecione a Entidade"   );
$obTxtCodEntidade->setInteiro( true                     );
$obTxtCodEntidade->setNull   ( false                    );

// Define Objeto Select para Nome da Entidade
$obCmbNomEntidade = new Select;
$obCmbNomEntidade->setName      ( "stNomEntidade"          );
$obCmbNomEntidade->setId        ( "stNomEntidade"          );
$obCmbNomEntidade->setValue     ( $inCodEntidadePrefeitura );
$obCmbNomEntidade->addOption    ( "", "Selecione"          );
$obCmbNomEntidade->setCampoId   ( "cod_entidade"           );
$obCmbNomEntidade->setCampoDesc ( "nom_cgm"                );
$obCmbNomEntidade->preencheCombo( $rsEntidade              );
$obCmbNomEntidade->setNull      ( true                     );

// Define Objeto Span Para lista de itens
$obSpan = new Span;
$obSpan->setId( "spnLista" );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados da autorização" );

$obFormulario->addHidden( $obHdnCtrl           );
$obFormulario->addHidden( $obHdnAcao           );
$obFormulario->addHidden( $obHdnCodLicitacao   );
$obFormulario->addHidden( $obHdnTipoModalidade );

$obFormulario->addComponenteComposto( $obTxtCodEntidade, $obCmbNomEntidade );
$obFormulario->addSpan( $obSpan );
$obMontaAtributos->geraFormulario ( $obFormulario );

$stLocation = $pgList.'?'.Sessao::getId().$stFiltro;
$obFormulario->cancelar( $stLocation );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
