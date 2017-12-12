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
    * Filtro para funcionalidade Manter Arrecadacao
    * Data de Criação   : 21/11/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 24404 $
    $Name$
    $Autor:$
    $Date: 2007-07-31 11:01:08 -0300 (Ter, 31 Jul 2007) $

    * Casos de uso: uc-02.04.34
*/

/*
$Log$
Revision 1.6  2007/07/31 13:57:15  domluc
Ajuste do Caso de Uso

Revision 1.5  2007/07/25 16:14:18  domluc
Atualizado Arr por Carne

Revision 1.4  2006/07/05 20:38:50  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeGeral.class.php" );
//include_once    ( CLA_IAPPLETTERMINAL );
include_once ( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"          );
SistemaLegado::BloqueiaFrames();

//Define o nome dos arquivos PHP
$stPrograma      = "ManterArrecadacaoCarne";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio( sessao::read('exercicio') );
$obRTesourariaBoletim->setDataBoletim( date( 'd/m/'.sessao::read('exercicio')  ) );
$obRTesourariaBoletim->addPagamento();
$obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setExercicio( sessao::read('exercicio') );
$obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( sessao::read('numCgm') );
$obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades );
$obErro = $obRTesourariaBoletim->buscarCodigoBoletim( $inCodBoletim, $stDtBoletim );
if( $obErro->ocorreu() ) SistemaLegado::exibeAviso( urlencode($obErro->getDescricao()),"","erro" );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

// DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget ( "oculto" );
$obForm->setTarget( "telaPrincipal");

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl"            );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"            );
$obHdnAcao->setValue ( $stAcao );

//$obApplet = new IAppletTerminal( $obForm );

// DEFINE OBJETOS DO FORMULARIO

$obISelectEntidadeGeral = new ISelectMultiploEntidadeGeral();
$obISelectEntidadeGeral->setNull ( true );

//Define Objeto TextBox para codigo do boletim
$obTxtCodBoletim = new TextBox();
$obTxtCodBoletim->setId      ( "inCodBoletim"       );
$obTxtCodBoletim->setName    ( "inCodBoletim"       );
$obTxtCodBoletim->setValue   ( $inCodBoletim        );
$obTxtCodBoletim->setRotulo  ( "Número Boletim"     );
$obTxtCodBoletim->setTitle   ( "Número do Boletim"  );
$obTxtCodBoletim->setInteiro ( true                 );

//Define Objeto Data para data do boletim
$obTxtDtBoletim = new Data();
$obTxtDtBoletim->setId        ( "stDtBoletim"     );
$obTxtDtBoletim->setName      ( "stDtBoletim"     );
$obTxtDtBoletim->setValue     ( $stDtBoletim      );
$obTxtDtBoletim->setRotulo    ( "Data do Boletim" );
$obTxtDtBoletim->setTitle     ( "Data do Boletim" );

//Define Objeto Label
$obLabel = new Label();
$obLabel->setValue   ( ' até ' );

// Define Objeto BuscaInner para conta
$obBscConta = new BuscaInner;
$obBscConta->setRotulo ( "Conta"       );
$obBscConta->setTitle  ( "Informe a Conta Banco que deseja pesquisar" );
$obBscConta->setId     ( "stNomConta"  );
$obBscConta->setValue  ( $stNomConta   );
$obBscConta->setNull   ( true          );
$obBscConta->obCampoCod->setName     ( "inCodPlano" );
$obBscConta->obCampoCod->setSize     ( 10           );
$obBscConta->obCampoCod->setNull     ( true         );
$obBscConta->obCampoCod->setMaxLength( 8            );
$obBscConta->obCampoCod->setValue    ( $inCodPlano  );
$obBscConta->obCampoCod->setAlign    ( "left"       );
$obBscConta->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodPlano','stNomConta','tes_arrec','".Sessao::getId()."','800','550');");
$obBscConta->setValoresBusca(CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(),$obForm->getName(),'tes_arrec');

// Define Objeto TextBox para número do carnê
$obTxtCarne = new TextBox();
$obTxtCarne->setRotulo   ( "Carnê"     );
$obTxtCarne->setName     ( "stCarne"   );
$obTxtCarne->setTitle    ( "Informe a númeração do Carnê que deseja pesquisar" );
$obTxtCarne->setValue    ( $stCarne    );
$obTxtCarne->setNull     ( true        );
$obTxtCarne->setMaxLength( 17          );
$obTxtCarne->setSize     ( 20          );
$obTxtCarne->obEvento->setOnChange( "buscaDado( 'montaDados');" );

//Define objeto Data para data da arrecadação
$obDtArrecadacao = new Data();
$obDtArrecadacao->setRotulo( "Data Arrecadação" );
$obDtArrecadacao->setTitle ( "Informe a data da arrecadação que deseja pesquisar" );
$obDtArrecadacao->setId    ( "stDtArrecadacao" );
$obDtArrecadacao->setName  ( "stDtArrecadacao" );
$obDtArrecadacao->setNull  ( true              );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm               );
$obFormulario->addHidden    ( $obHdnCtrl            );
$obFormulario->addHidden    ( $obHdnAcao            );
//$obFormulario->addHidden    ( $obApplet             );
$obFormulario->addTitulo    ( "Dados para Filtro"   );
$obFormulario->addComponente( $obISelectEntidadeGeral);
$obFormulario->addComponente( $obTxtCodBoletim      );
$obFormulario->addComponente( $obTxtDtBoletim       );
$obFormulario->addComponente( $obBscConta           );
$obFormulario->addComponente( $obTxtCarne           );
$obFormulario->addComponente( $obDtArrecadacao      );

$obFormulario->Ok();

$obFormulario->show();
include_once( $pgJs );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
