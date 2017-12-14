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
    * Página de Filtro de Ordem de Compra
    * Data de Criação   : 14/12/2006

    * @author Analista: Gelson W. Golçalves
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * Casos de uso: uc-03.04.24

    $Id: FLManterOrdemCompra.php 59612 2014-09-02 12:00:51Z gelson $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );

$stPrograma = "ManterOrdemCompra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

require_once ($pgJs);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
$stTipoOrdem = ( strpos($stAcao,'OS')===false ) ? 'C' : 'S';
$stDesc = ($stTipoOrdem=='C') ? 'Compra' : 'Serviço';

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obForm = new Form;
$obForm->setAction ( $pgList );

$rsRecordset = new RecordSet;
$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
$stMascaraRubrica = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

// Define SELECT multiplo para codigo da entidade
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades." );
#$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
    $rsRecordset = $rsEntidades;
    $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidades );
// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordset );

// Define objeto TextBox para Armazenar Exercicio
$obTxtAno = new TextBox;
$obTxtAno->setName      ( "stExercicio" );
$obTxtAno->setId        ( "stExercicio" );
$obTxtAno->setValue     ( Sessao::getExercicio() );
$obTxtAno->setRotulo    ( "Exercício do Empenho" );
$obTxtAno->setTitle     ( "Informe o exercício do empenho." );
$obTxtAno->setNull      ( false );
$obTxtAno->setMaxLength ( 4 );
$obTxtAno->setSize      ( 4 );
$obTxtAno->setInteiro   ( true );

// Define Objeto BuscaInner para Despesa
$obBscDespesa = new BuscaInner;
$obBscDespesa->setRotulo ( "Dotação Orçamentária" );
$obBscDespesa->setTitle  ( "Informe a dotação orçamentária." );
$obBscDespesa->setNulL   ( true );
$obBscDespesa->setId     ( "stNomDespesa" );
$obBscDespesa->setValue  ( $stNomDespesa );
$obBscDespesa->obCampoCod->setName ( "inCodDespesa" );
$obBscDespesa->obCampoCod->setSize ( 10 );
$obBscDespesa->obCampoCod->setMaxLength( 5 );
$obBscDespesa->obCampoCod->setValue ( $inCodDespesa );
$obBscDespesa->obCampoCod->setAlign ("left");
$obBscDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDespesa','stNomDespesa','','".Sessao::getId()."','800','550');");
$obBscDespesa->setValoresBusca ( CAM_GF_ORC_POPUPS."despesa/OCDespesa.php?".Sessao::getId(), $obForm->getName(), '');

//Define o objeto TEXT para Codigo do Empenho Inicial
$obTxtCodEmpenhoInicial = new TextBox;
$obTxtCodEmpenhoInicial->setName     ( "inCodEmpenhoInicial" );
$obTxtCodEmpenhoInicial->setValue    ( $inCodEmpenhoInicial  );
$obTxtCodEmpenhoInicial->setRotulo   ( "Número do Empenho"   );
$obTxtCodEmpenhoInicial->setTitle    ( "Informe o número do empenho." );
$obTxtCodEmpenhoInicial->setInteiro  ( true                  );
$obTxtCodEmpenhoInicial->setNull     ( true                  );

//Define objeto Label
$obLblEmpenho = new Label;
$obLblEmpenho->setValue( "a" );

//Define o objeto TEXT para Codigo do Empenho Final
$obTxtCodEmpenhoFinal = new TextBox;
$obTxtCodEmpenhoFinal->setName     ( "inCodEmpenhoFinal" );
$obTxtCodEmpenhoFinal->setValue    ( $inCodEmpenhoFinal  );
$obTxtCodEmpenhoFinal->setRotulo   ( "Número do Empenho" );
$obTxtCodEmpenhoFinal->setInteiro  ( true                );
$obTxtCodEmpenhoFinal->setNull     ( true                );

//Define o objeto TEXT para Código da Autorização Inicial
$obTxtCodAutorizacaoInicial = new TextBox;
$obTxtCodAutorizacaoInicial->setName     ( "inCodAutorizacaoInicial" );
$obTxtCodAutorizacaoInicial->setValue    ( $inCodAutorizacaoInicial  );
$obTxtCodAutorizacaoInicial->setRotulo   ( "Número da Autorização"   );
$obTxtCodAutorizacaoInicial->setTitle    ( "Informe o número da autorização."   );
$obTxtCodAutorizacaoInicial->setInteiro  ( true                      );
$obTxtCodAutorizacaoInicial->setNull     ( true                      );

//Define objeto Label
$obLblAutorizacao = new Label;
$obLblAutorizacao->setValue( "a" );

//Define o objeto TEXT para Codigo da Autorização Final
$obTxtCodAutorizacaoFinal = new TextBox;
$obTxtCodAutorizacaoFinal->setName     ( "inCodAutorizacaoFinal" );
$obTxtCodAutorizacaoFinal->setValue    ( $inCodAutorizacaoFinal  );
$obTxtCodAutorizacaoFinal->setRotulo   ( "Número da Autorização" );
$obTxtCodAutorizacaoFinal->setInteiro  ( true                    );
$obTxtCodAutorizacaoFinal->setNull     ( true                    );

$obIpopUpCgm = new IPopUpCGMVinculado( $obForm                 );
$obIpopUpCgm->setTabelaVinculo       ( 'compras.fornecedor'    );
$obIpopUpCgm->setCampoVinculo        ( 'cgm_fornecedor'        );
$obIpopUpCgm->setNomeVinculo         ( 'Fornecedor'            );
$obIpopUpCgm->setRotulo              ( 'Fornecedor'            );
$obIpopUpCgm->setTitle               ( 'Informe o fornecedor.' );
$obIpopUpCgm->setName                ( 'stNomCGM'              );
$obIpopUpCgm->setId                  ( 'stNomCGM'              );
$obIpopUpCgm->obCampoCod->setName    ( 'inCodFornecedor'       );
$obIpopUpCgm->obCampoCod->setId      ( 'inCodFornecedor'       );
$obIpopUpCgm->obCampoCod->setNull    ( true                    );
$obIpopUpCgm->setNull                ( true                    );

// Define objeto Data para Periodo
$obDtInicial = new Data;
$obDtInicial->setName     ( "stDtInicial" );
$obDtInicial->setRotulo   ( "Período" );
$obDtInicial->setTitle    ( 'Informe o período.' );
$obDtInicial->setNull     ( true );

// Define Objeto Label
$obLabel = new Label;
$obLabel->setValue( " até " );

// Define objeto Data para validade final
$obDtFinal = new Data;
$obDtFinal->setName     ( "stDtFinal" );
$obDtFinal->setRotulo   ( "Período" );
$obDtFinal->setTitle    ( '' );
$obDtFinal->setNull     ( true );

if ( strpos($stAcao,'incluir') === false ) {
//if ($stAcao != "incluir") {
    // Define o objeto TextBox para o exercicio da ordem de compra
    $obTxtExercicioOrdemCompra = new TextBox;
    $obTxtExercicioOrdemCompra->setId('stExercicioOrdemCompra');
    $obTxtExercicioOrdemCompra->setTitle("Informe o exercício da Ordem de $stDesc.");
    $obTxtExercicioOrdemCompra->setName('stExercicioOrdemCompra');
    $obTxtExercicioOrdemCompra->setInteiro( true );
    $obTxtExercicioOrdemCompra->setRotulo("Exercício Ordem $stDesc");

    // Define o objeto TextBox para o exercicio do código de compra
    $obTxtOrdemCompra = new TextBox;
    $obTxtOrdemCompra->setId('inCodOrdemCompra');
    $obTxtOrdemCompra->setName('inCodOrdemCompra');
    $obTxtOrdemCompra->setInteiro( true );

    $obTxtOrdemCompra->setRotulo("Ordem de $stDesc");
    $obTxtOrdemCompra->setTitle("Informe o código da Ordem de $stDesc.");
}

// Botão de limpar usando o evento limparFiltro
$obBtnClean = new Button;
$obBtnClean->setName                    ( "btnClean"       );
$obBtnClean->setValue                   ( "Limpar"         );
$obBtnClean->setTipo                    ( "button"         );
$obBtnClean->obEvento->setOnClick       ( "limparFiltro();" );
$obBtnClean->setDisabled                ( false            );

$obBtnOK = new Ok;
$botoesForm = array ( $obBtnOK , $obBtnClean );

/****************************************
            MONTA FORMULARIO
****************************************/
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda( "UC-03.04.24" );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );

$obFormulario->addTitulo( "Dados para Filtro"  );
$obFormulario->addComponente( $obCmbEntidades  );
$obFormulario->addComponente( $obTxtAno    );
$obFormulario->addComponente( $obBscDespesa );
$obFormulario->agrupaComponentes( array( $obTxtCodEmpenhoInicial, $obLblEmpenho, $obTxtCodEmpenhoFinal ) );
$obFormulario->agrupaComponentes( array( $obTxtCodAutorizacaoInicial, $obLblAutorizacao, $obTxtCodAutorizacaoFinal ) );
$obFormulario->addComponente($obIpopUpCgm);
$obFormulario->agrupaComponentes( array( $obDtInicial,$obLabel, $obDtFinal ) );
if ( strpos($stAcao,'incluir') === false ) {
//if ($stAcao != "incluir") {
    $obFormulario->addComponente( $obTxtExercicioOrdemCompra );
    $obFormulario->addComponente( $obTxtOrdemCompra );
}
if ( strpos($stAcao,'reemitir') !== false ) {
    include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
    $obMontaAssinaturas = new IMontaAssinaturas;
    $obMontaAssinaturas->geraFormulario( $obFormulario );

    //Radios de Inclusão de Assinatura do Usuário Logado
    $obRadioAssinaturaUsuarioSim = new Radio;
    $obRadioAssinaturaUsuarioSim->setRotulo ( "Incluir Assinatura do Usuário" );
    $obRadioAssinaturaUsuarioSim->setName   ( "stIncluirAssinaturaUsuario" );
    $obRadioAssinaturaUsuarioSim->setId     ( "stIncluirAssinaturaUsuarioSim" );
    $obRadioAssinaturaUsuarioSim->setChecked ( false );
    $obRadioAssinaturaUsuarioSim->setValue   ( "sim" );
    $obRadioAssinaturaUsuarioSim->setLabel   ( "Sim" );
    $obRadioAssinaturaUsuarioSim->setNull    ( false );

    $obRadioAssinaturaUsuarioNao = new Radio;
    $obRadioAssinaturaUsuarioNao->setName ( "stIncluirAssinaturaUsuario" );
    $obRadioAssinaturaUsuarioNao->setId   ( "stIncluirAssinaturaUsuarioNao" );
    $obRadioAssinaturaUsuarioNao->setChecked ( true );
    $obRadioAssinaturaUsuarioNao->setValue   ( "nao" );
    $obRadioAssinaturaUsuarioNao->setLabel   ( "Não" );
    $obRadioAssinaturaUsuarioNao->setNull    ( false );
    $obFormulario->agrupaComponentes( array( $obRadioAssinaturaUsuarioSim, $obRadioAssinaturaUsuarioNao ) );

    Sessao::write ('stIncluirAssinaturaUsuario', $_REQUEST['stIncluirAssinaturaUsuario']);
}

$obFormulario->defineBarra( $botoesForm );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
