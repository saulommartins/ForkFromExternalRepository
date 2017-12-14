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
    * Página de Vínculo de Conta Fundeb
    * Data de Criação   : 01/06/2011

    * @author

    * @ignore

    * Casos de uso :

    $Id: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php" );
include_once (CAM_GPC_STN_MAPEAMENTO."TSTNVinculoFundeb.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "VincularContaFundeb";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
include_once($pgJS);

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

$obTSTNVinculoFundeb = new TSTNVinculoFundeb();
$obTSTNVinculoFundeb->setDado ('exercicio', Sessao::getExercicio());
$obTSTNVinculoFundeb->recuperaVinculoConta($rsVinculoFundeb, '', ' ORDER BY vinculo_fundeb.cod_plano ');

Sessao::write('arContas', $rsVinculoFundeb->getElementos());
$jsSL = "buscaDado('montaSpanListaConta')";

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
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

// Define Objeto Select para Entidade
$obCmbEntidade = new Select();
$obCmbEntidade->setRotulo    ( "*Entidade"                );
$obCmbEntidade->setName      ( "inCodEntidade"            );
$obCmbEntidade->setId        ( "inCodEntidade"            );
$obCmbEntidade->setTitle     ( "Selecione a Entidade"     );
$obCmbEntidade->setCampoId   ( "cod_entidade"             );
$obCmbEntidade->setCampoDesc ( "nom_cgm"                  );
$obCmbEntidade->setValue     ( $inCodEntidade             );
$obCmbEntidade->setNull      ( true                       );
if ($rsEntidade->getNumLinhas() > 1) {
      $obCmbEntidade->addOption    ( ""            ,"Selecione" );
      $obCmbEntidade->obEvento->setOnChange( "buscaDado('mostraSpanContaBanco');" );
} else $jsSL = "buscaDado('mostraSpanContaBanco');";
$obCmbEntidade->preencheCombo( $rsEntidade                );

// Define Objeto BuscaInner para conta de banco
$obBscContaBanco = new BuscaInner;
$obBscContaBanco->setRotulo ( "*Conta de Banco" );
$obBscContaBanco->setTitle  ( "Informe a Conta Banco para implantação de saldo" );
$obBscContaBanco->setId     ( "stNomConta"  );
$obBscContaBanco->setValue  ( $stNomConta   );
$obBscContaBanco->setNull   ( true          );
$obBscContaBanco->obCampoCod->setName     ( "inCodPlano" );
$obBscContaBanco->obCampoCod->setId       ( "inCodPlano" );
$obBscContaBanco->obCampoCod->setSize     ( 10           );
$obBscContaBanco->obCampoCod->setNull     ( false        );
$obBscContaBanco->obCampoCod->setMaxLength( 8            );
$obBscContaBanco->obCampoCod->setValue    ( $inCodPlano  );
$obBscContaBanco->obCampoCod->setAlign    ( "left"       );
$obBscContaBanco->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodPlano','stNomConta','banco','".Sessao::getId()."','800','550');");
$obBscContaBanco->setValoresBusca(CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(),$obForm->getName(),'banco');

$obSpanContaBanco = new Span;
$obSpanContaBanco->setId( "spnContaBanco" );

$obSpanListaContaBanco = new Span;
$obSpanListaContaBanco->setId( "spnListaConta" );

$obBtIncluir = new Button();
$obBtIncluir->setId('btnIncluir');
$obBtIncluir->setValue('Incluir');
$obBtIncluir->obEvento->setOnClick("buscaDado('incluiContaLista')");

$obBtLimpar = new Button();
$obBtLimpar->setId('btnLimpar');
$obBtLimpar->setValue('Limpar');
$obBtLimpar->obEvento->setOnClick("limpaContaEntidade()");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnCtrl            );
$obFormulario->addHidden        ( $obHdnAcao            );

$obFormulario->addTitulo        ( "Dados para Configuração de Contas Fundeb"   );
$obFormulario->addComponente    ( $obCmbEntidade        );
$obFormulario->addComponente    ( $obBscContaBanco      );
$obFormulario->addSpan          ( $obSpanContaBanco     );
$obFormulario->agrupaComponentes ( array($obBtIncluir,$obBtLimpar)          );
$obFormulario->addSpan          ( $obSpanListaContaBanco );

$obFormulario->Ok();

$obFormulario->show();

if ($jsSL) {
    SistemaLegado::executaFrameOculto($jsSL);
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
