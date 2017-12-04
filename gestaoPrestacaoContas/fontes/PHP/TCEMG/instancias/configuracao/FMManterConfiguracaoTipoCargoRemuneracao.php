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
  * Página de Formulario de Configurar Tipos de Cargos e Remuneração
  * Data de Criação: 16/03/2016

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Evandro Melos
  * @ignore
  *
  * $Id: $
  * $Revision: $
  * $Author: $
  * $Date: $
*/
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php";
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GF_ORC_COMPONENTES.'ILabelEntidade.class.php';
require_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TTCEMGTipoRequisitosCargo.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TTCEMGTipoRemuneracao.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/TTCEMGTipoCargoServidor.class.php';
include_once CAM_GRH_PES_MAPEAMENTO.'TPessoalCargo.class.php';
include_once CAM_GRH_PES_MAPEAMENTO.'TPessoalSubDivisao.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEvento.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoTipoCargoRemuneracao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

Sessao::write('cod_entidade', $request->get('inCodEntidade'));

// Busca a entidade definida como prefeitura na configuração do orçamento
$stCampo   = "valor";
$stTabela  = "administracao.configuracao";
$stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
$stFiltro .= "   AND parametro = 'cod_entidade_prefeitura' ";

$inCodEntidadePrefeitura = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);

// Se foi selecionada a entidade definida como prefeitura, não vai "_" no schema
if ($request->get('inCodEntidade') == $inCodEntidadePrefeitura) {
    $stFiltro = " WHERE nspname = 'pessoal'";
    $stSchema = '';
} else {
    $stFiltro = " WHERE nspname = 'pessoal_".$request->get('inCodEntidade')."'";
    $stSchema = '_'.$request->get('inCodEntidade');
}

$obTEntidade = new TEntidade();
$obTEntidade->recuperaEsquemasCriados($rsEsquemas, $stFiltro);

// Verifica se existe o schema para a entidade selecionada
if ($rsEsquemas->getNumLinhas() < 1) {
    SistemaLegado::alertaAviso($pgFilt.'?stAcao='.$request->get('stAcao'), 'Não existe entidade criada no RH para a entidade selecionada!' , '', 'aviso', Sessao::getId(), '../');
}

// Se foi selecionada a entidade definida como prefeitura, não vai "_" no schema
if ($request->get('inCodEntidade') == $inCodEntidadePrefeitura) {
    Sessao::setEntidade('');
} else {
    // Se não foi selecionada a entidade definida como prefeitura
    // ao executar as consultas, automaticamente é adicionado o "_" + cod_entidade selecionada
    $arSchemasRH = array();
    $obTEntidade->recuperaSchemasRH($rsSchemasRH);
    while (!$rsSchemasRH->eof()) {
        $arSchemasRH[] = $rsSchemasRH->getCampo("schema_nome");
        $rsSchemasRH->proximo();
    }
    Sessao::write('arSchemasRH', $arSchemasRH, true);
    Sessao::setEntidade($request->get('inCodEntidade'));
}

$obForm = new Form();
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden();
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setId ( "stAcao" );
$obHdnAcao->setValue( $request->get('stAcao') );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId ( "stCtrl" );
$obHdnCtrl->setValue( $request->get('stCtrl') );

$obHdnSchema = new Hidden;
$obHdnSchema->setName ( 'stSchema' );
$obHdnSchema->setValue( $stSchema );

$obHdnEntidade = new Hidden();
$obHdnEntidade->setName( "inCodEntidade" );
$obHdnEntidade->setValue( $request->get('inCodEntidade') );

$obLblEntidade = new Label;
$obLblEntidade->setRotulo('Entidade');
$obLblEntidade->setName  ('stEntidade');
$obLblEntidade->setId    ('stEntidade');
$obLblEntidade->setValue ($request->get('stNomEntidade'));

//----------------------------------------------------
//Informações do Tipo de Cargo do Servidor 
//----------------------------------------------------
$obTTCEMGTipoCargoServidor = new TTCEMGTipoCargoServidor();
$obTTCEMGTipoCargoServidor->recuperaTodos($rsTipoCargoServidor,'','',$boTransacao);

$obCmbTipoCargo = new Select();
$obCmbTipoCargo->setName      ('cmbTipoCargo');
$obCmbTipoCargo->setId        ('cmbTipoCargo');
$obCmbTipoCargo->setValue     ('[cod_tipo]');
$obCmbTipoCargo->addOption    ('','Selecione');
$obCmbTipoCargo->setRotulo    ('*Tipo de Cargo');
$obCmbTipoCargo->setCampoId   ('[cod_tipo]');
$obCmbTipoCargo->setCampodesc ('[cod_tipo] - [descricao]');
$obCmbTipoCargo->setTitle     ('Informe o Tipo de Cargo.');
$obCmbTipoCargo->preencheCombo($rsTipoCargoServidor);

$obTPessoalSubDivisao = new TPessoalSubDivisao();
$obTPessoalSubDivisao->recuperaRelacionamento($rsRegimeSubDivisao,'',' ORDER BY nom_sub_divisao ',$boTransacao);

$obCmbRegimeSubDivisao = new SelectMultiplo();
$obCmbRegimeSubDivisao->setName  ( 'arRegimeSubdivisao' );
$obCmbRegimeSubDivisao->setRotulo( "Regime/Subdivisão" );
$obCmbRegimeSubDivisao->setTitle ( 'Regime/Subdivisão Disponíveis' );
$obCmbRegimeSubDivisao->setOrdenacao('valueText');
$obCmbRegimeSubDivisao->setNull  ( true );
$obCmbRegimeSubDivisao->setObrigatorioBarra (true);

$obCmbRegimeSubDivisao->SetNomeLista1( 'arRegimeSubdivisaoDisponiveis' );
$obCmbRegimeSubDivisao->setCampoId1  ( '[cod_sub_divisao]' );
$obCmbRegimeSubDivisao->setCampoDesc1( '[nom_regime] - [nom_sub_divisao]' );
$obCmbRegimeSubDivisao->SetRecord1   ( $rsRegimeSubDivisao  );

$obCmbRegimeSubDivisao->SetNomeLista2( 'arRegimeSubdivisaoSelecionados' );
$obCmbRegimeSubDivisao->setCampoId2  ( '[cod_sub_divisao]' );
$obCmbRegimeSubDivisao->setCampoDesc2( '[nom_regime] - [nom_sub_divisao]' );
$obCmbRegimeSubDivisao->SetRecord2   ( new RecordSet() );

$obTPessoalCargo = new TPessoalCargo ;
$obTPessoalCargo->recuperaTodos( $rsPessoalCargo, " ORDER BY descricao" );

$obCmbCargosRegime = new SelectMultiplo();
$obCmbCargosRegime->setName  ( 'arCargosRegime' );
$obCmbCargosRegime->setRotulo( "Cargo" );
$obCmbCargosRegime->setNull  ( true );
$obCmbCargosRegime->setObrigatorioBarra (true);
$obCmbCargosRegime->setOrdenacao('valueText');
$obCmbCargosRegime->setTitle ( 'Cargos Disponíveis' );

$obCmbCargosRegime->SetNomeLista1( 'arCargosRegimeDisponiveis' );
$obCmbCargosRegime->setCampoId1  ( '[cod_cargo]' );
$obCmbCargosRegime->setCampoDesc1( '[cod_cargo] - [descricao]' );
$obCmbCargosRegime->SetRecord1   ( $rsPessoalCargo  );

$obCmbCargosRegime->SetNomeLista2( 'arCargosRegimeSelecionados' );
$obCmbCargosRegime->setCampoId2  ( '[cod_cargo]' );
$obCmbCargosRegime->setCampoDesc2( '[cod_cargo] - [descricao]' );
$obCmbCargosRegime->SetRecord2   ( new RecordSet() );

$obSpnListaRegimeSubDivisao = new Span;
$obSpnListaRegimeSubDivisao->setId   ( 'spnListaRegimeSubDivisao' );

$obBtnIncluirRegimeSubDivisao = new Button;
$obBtnIncluirRegimeSubDivisao->setValue( 'Incluir' );
$obBtnIncluirRegimeSubDivisao->setId('btnIncluirRegimeSubDivisao');
$obBtnIncluirRegimeSubDivisao->setName('btnIncluirRegimeSubDivisao');
$obBtnIncluirRegimeSubDivisao->obEvento->setOnClick( "montaParametrosPOST('incluirRegimeSubDivisao','cmbTipoCargo,arRegimeSubdivisaoSelecionados,arCargosRegimeSelecionados,stAcao');" );

$obBtnLimparCargoServidor = new Button;
$obBtnLimparCargoServidor->setValue( 'Limpar' );
$obBtnLimparCargoServidor->obEvento->setOnClick( "executaFuncaoAjax('limparListaRegimeSubDivisao');" );

//----------------------------------------------------
//Informações de Requisitos dos Cargos
//----------------------------------------------------
$obTTCEMGTipoRequisitosCargo = new TTCEMGTipoRequisitosCargo();
$obTTCEMGTipoRequisitosCargo->recuperaTodos($rsTipoRequisitosCargo,'','',$boTransacao);

$obCmbTipoRequisitosCargos = new Select();
$obCmbTipoRequisitosCargos->setName      ('cmbTipoRequisitosCargos');
$obCmbTipoRequisitosCargos->setId        ('cmbTipoRequisitosCargos');
$obCmbTipoRequisitosCargos->setValue     ('[cod_tipo]');
$obCmbTipoRequisitosCargos->addOption    ('','Selecione');
$obCmbTipoRequisitosCargos->setRotulo    ('*Requisito do Cargo');
$obCmbTipoRequisitosCargos->setCampoId   ('[cod_tipo]');
$obCmbTipoRequisitosCargos->setCampodesc ('[cod_tipo] - [descricao]');
$obCmbTipoRequisitosCargos->setTitle     ('Informe o Requisito do Cargo.');
$obCmbTipoRequisitosCargos->preencheCombo($rsTipoRequisitosCargo);

$obTPessoalCargo = new TPessoalCargo ;
$obTPessoalCargo->recuperaTodos( $rsPessoalCargo, " ORDER BY descricao" );

$obCmbCargosServidor = new SelectMultiplo();
$obCmbCargosServidor->setName  ( 'arRequisitosCargos' );
$obCmbCargosServidor->setRotulo( "Cargo" );
$obCmbCargosServidor->setNull  ( true );
$obCmbCargosServidor->setObrigatorioBarra (true);
$obCmbCargosServidor->setOrdenacao('valueText');
$obCmbCargosServidor->setTitle ( 'Cargos Disponíveis' );

$obCmbCargosServidor->SetNomeLista1( 'arRequisitosCargosDisponivel' );
$obCmbCargosServidor->setCampoId1  ( '[cod_cargo]' );
$obCmbCargosServidor->setCampoDesc1( '[cod_cargo] - [descricao]' );
$obCmbCargosServidor->SetRecord1   ( $rsPessoalCargo  );

$obCmbCargosServidor->SetNomeLista2( 'arRequisitosCargosSelecionados' );
$obCmbCargosServidor->setCampoId2  ( '[cod_cargo]' );
$obCmbCargosServidor->setCampoDesc2( '[cod_cargo] - [descricao]' );
$obCmbCargosServidor->SetRecord2   ( new RecordSet() );

$obspnListaCargos = new Span;
$obspnListaCargos->setId   ( 'spnListaRequisitosCargos' );

$obBtnIncluirCargo = new Button;
$obBtnIncluirCargo->setValue( 'Incluir' );
$obBtnIncluirCargo->setId('btnIncluirRequisitosCargos');
$obBtnIncluirCargo->setName('btnIncluirRequisitosCargos');
$obBtnIncluirCargo->obEvento->setOnClick( "montaParametrosPOST('incluirRequisitosCargos','cmbTipoRequisitosCargos,arRequisitosCargosSelecionados,stAcao');" );

$obBtnLimparCargo = new Button;
$obBtnLimparCargo->setValue( 'Limpar' );
$obBtnLimparCargo->obEvento->setOnClick( "executaFuncaoAjax('limparRequisitosCargos');" );

//----------------------------------------------------
//Informações de Tipos de Remuneração
//----------------------------------------------------
$obTTCEMGTipoRemuneracao = new TTCEMGTipoRemuneracao();
$obTTCEMGTipoRemuneracao->recuperaTodos($rsTipoRemuneracao,'','',$boTransacao);

$obCmbTipoRemuneracao = new Select();
$obCmbTipoRemuneracao->setName      ('cmbTipoRemuneracao');
$obCmbTipoRemuneracao->setId        ('cmbTipoRemuneracao');
$obCmbTipoRemuneracao->setValue     ('[cod_tipo]');
$obCmbTipoRemuneracao->addOption    ('','Selecione');
$obCmbTipoRemuneracao->setRotulo    ('*Tipo de Remuneração');
$obCmbTipoRemuneracao->setCampoId   ('[cod_tipo]');
$obCmbTipoRemuneracao->setCampodesc ('[cod_tipo] - [descricao]');
$obCmbTipoRemuneracao->setTitle     ('Informe o Tipo de Remuneração.');
$obCmbTipoRemuneracao->preencheCombo($rsTipoRemuneracao);

$obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
$obTFolhaPagamentoEvento->recuperaTodos($rsEventos,'',' ORDER BY descricao ',$boTransacao);

$obCmbEventos = new SelectMultiplo();
$obCmbEventos->setName  ( 'arEventos' );
$obCmbEventos->setRotulo( 'Eventos' );
$obCmbEventos->setNull  ( true );
$obCmbEventos->setObrigatorioBarra (true);
$obCmbEventos->setOrdenacao('valueText');
$obCmbEventos->setTitle ( 'Eventos Disponíveis' );

$obCmbEventos->SetNomeLista1( 'arEventosDisponiveis' );
$obCmbEventos->setCampoId1  ( '[cod_evento]' );
$obCmbEventos->setCampoDesc1( '[codigo] - [descricao]' );
$obCmbEventos->SetRecord1   ( $rsEventos  );

$obCmbEventos->SetNomeLista2( 'arEventosSelecionados' );
$obCmbEventos->setCampoId2  ( '[cod_evento]' );
$obCmbEventos->setCampoDesc2( '[codigo] - [descricao]' );
$obCmbEventos->SetRecord2   ( new RecordSet() );

$obspnListaEventos = new Span;
$obspnListaEventos->setId   ( 'spnListaEventos' );

$obBtnIncluirEventos = new Button;
$obBtnIncluirEventos->setValue( 'Incluir' );
$obBtnIncluirEventos->setId   ( 'btnIncluirEventos' );
$obBtnIncluirEventos->setName ( 'btnIncluirEventos' );
$obBtnIncluirEventos->obEvento->setOnClick( "montaParametrosPOST('incluirEventos','cmbTipoRemuneracao,arEventosSelecionados,stAcao');" );

$obBtnLimparEventos = new Button;
$obBtnLimparEventos->setValue( 'Limpar' );
$obBtnLimparEventos->obEvento->setOnClick( "executaFuncaoAjax('limparEventos');" );

//FORM SUBMIT BUTTONS 
$obOk  = new Ok(true);
$obOk->setId   ("btnOk");
$obOk->setName ("btnOk");

$obLimpar = new Button;
$obLimpar->setValue ( "Limpar" );
$obLimpar->setId    ( "btnLimpar" );
$obLimpar->setName  ( "btnLimpar" );
$obLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparTudo');" );

//------------------------------------------------------
// FORMULARIO
//-----------------------------------------------------
$obFormulario = new Formulario();
$obFormulario->addForm          ( $obForm );
$obFormulario->addHidden        ( $obHdnAcao ); 
$obFormulario->addHidden        ( $obHdnCtrl ); 
$obFormulario->addHidden        ( $obHdnSchema );
$obFormulario->addHidden        ( $obHdnEntidade );
$obFormulario->addComponente    ( $obLblEntidade );

$obFormulario->addTitulo        ( 'Informações do Tipo de Cargo do Servidor' );
$obFormulario->addComponente    ( $obCmbTipoCargo );
$obFormulario->addComponente    ( $obCmbRegimeSubDivisao );
$obFormulario->addComponente    ( $obCmbCargosRegime );
$obFormulario->agrupaComponentes( array( $obBtnIncluirRegimeSubDivisao, $obBtnLimparCargoServidor ) );
$obFormulario->addSpan          ( $obSpnListaRegimeSubDivisao );

$obFormulario->addTitulo        ( 'Informações de Requisitos dos Cargos' );
$obFormulario->addComponente    ( $obCmbTipoRequisitosCargos );
$obFormulario->addComponente    ( $obCmbCargosServidor );
$obFormulario->agrupaComponentes( array( $obBtnIncluirCargo, $obBtnLimparCargo ) );
$obFormulario->addSpan          ( $obspnListaCargos );

$obFormulario->addTitulo        ( 'Informações de Tipos de Remuneração' );
$obFormulario->addComponente    ( $obCmbTipoRemuneracao );
$obFormulario->addComponente    ( $obCmbEventos );
$obFormulario->agrupaComponentes( array( $obBtnIncluirEventos, $obBtnLimparEventos ) );
$obFormulario->addSpan          ( $obspnListaEventos );

$obFormulario->defineBarra      ( array( $obOk,$obLimpar ) );
$obFormulario->show();

//Carrega dados cadastrados
$jsOnLoad  = " BloqueiaFrames(true,false); executaFuncaoAjax('carregaDados'); ";


require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>