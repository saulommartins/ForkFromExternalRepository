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
  * Página de Formulário de Configuração de Tipos de Salários
  * Data de Criação: 27/10/2015

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Arthur Cruz
  * @ignore
  *
  * $Id: $
  * $Revision: $
  * $Author: $
  * $Date: $
*/
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php";
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php";
require_once CAM_GF_ORC_COMPONENTES."ILabelEntidade.class.php";
require_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php";
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATipoFuncaoServidor.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATipoFuncaoServidorTemporario.class.php';
include_once CAM_GT_MON_MAPEAMENTO.'TMONBanco.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php";
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAFonteRecursoServidor.class.php';
include_once CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php";
include_once CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoTipoSalario";
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
$obHdnAcao->setValue( $request->get('stAcao') );

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

/*** Tipo Função Servidor ***/
$obTTCMBATipoFuncaoServidor = new TTCMBATipoFuncaoServidor();
$obTTCMBATipoFuncaoServidor->recuperaTodos($rsTipoFuncaoServidor, ' ORDER BY cod_tipo_funcao');

$obCmbTipoFuncaoServidor = new Select();
$obCmbTipoFuncaoServidor->setName             ('cmbTipofuncaoServidor');
$obCmbTipoFuncaoServidor->setId               ('cmbTipofuncaoServidor');
$obCmbTipoFuncaoServidor->setValue            ('[cod_tipo_funcao]');
$obCmbTipoFuncaoServidor->addOption           ('','Selecione');
$obCmbTipoFuncaoServidor->setRotulo           ('*Tipo Função do Servidor');
$obCmbTipoFuncaoServidor->setCampoId          ('[cod_tipo_funcao]');
$obCmbTipoFuncaoServidor->setCampodesc        ('[cod_tipo_funcao] - [descricao]');
$obCmbTipoFuncaoServidor->setTitle            ('Informe o Tipo Funçao do Servidor.');
$obCmbTipoFuncaoServidor->preencheCombo       ($rsTipoFuncaoServidor);
// ver se tem como reorganizar de forma numerica a lista

$obTPessoalCargo = new TPessoalCargo ;
$obTPessoalCargo->recuperaTodos( $rsPessoalCargo, " ORDER BY cod_cargo" );

$obCmbCargosServidor = new SelectMultiplo();
$obCmbCargosServidor->setName  ( 'arCargosServidor' );
$obCmbCargosServidor->setRotulo( "Cargo" );
$obCmbCargosServidor->setNull  ( true );
$obCmbCargosServidor->setObrigatorioBarra (true);
$obCmbCargosServidor->setTitle ( 'Cargos Disponíveis' );

$obCmbCargosServidor->SetNomeLista1( 'arCargosDisponiveisServidor' );
$obCmbCargosServidor->setCampoId1  ( '[cod_cargo]' );
$obCmbCargosServidor->setCampoDesc1( '[cod_cargo] - [descricao]' );
$obCmbCargosServidor->SetRecord1   ( $rsPessoalCargo  );

$obCmbCargosServidor->SetNomeLista2( 'arCargosSelecionadosServidor' );
$obCmbCargosServidor->setCampoId2  ( '[cod_cargo]' );
$obCmbCargosServidor->setCampoDesc2( '[cod_cargo] - [descricao]' );
$obCmbCargosServidor->SetRecord2   ( new RecordSet() );

$obspnListaFuncaoServidor = new Span;
$obspnListaFuncaoServidor->setId   ( 'spnListaFuncaoServidor' );

$obBtnIncluirCargoServidor = new Button;
$obBtnIncluirCargoServidor->setValue( 'Incluir' );
$obBtnIncluirCargoServidor->obEvento->setOnClick( "montaParametrosGET('incluirFuncaoServidorLista','cmbTipofuncaoServidor,arCargosSelecionadosServidor');" );

$obBtnLimparCargoServidor = new Button;
$obBtnLimparCargoServidor->setValue( 'Limpar' );
$obBtnLimparCargoServidor->obEvento->setOnClick( "executaFuncaoAjax('limparListaCargoServidor');" );

/*** Tipo Função Servidor Temporário ***/
$obTTCMBATipoFuncaoServidorTemporario = new TTCMBATipoFuncaoServidorTemporario();
$obTTCMBATipoFuncaoServidorTemporario->recuperaTodos($rsTipoFuncaoServidorTemporario, ' ORDER BY cod_tipo_funcao');

$obCmbTipoFuncaoServidorTemporario = new Select();
$obCmbTipoFuncaoServidorTemporario->setName             ('cmbTipofuncaoServidorTemporario');
$obCmbTipoFuncaoServidorTemporario->setId               ('cmbTipofuncaoServidorTemporario');
$obCmbTipoFuncaoServidorTemporario->setValue            ('[cod_tipo_funcao]');
$obCmbTipoFuncaoServidorTemporario->addOption           ('','Selecione');
$obCmbTipoFuncaoServidorTemporario->setRotulo           ('*Função Servidor Temporário');
$obCmbTipoFuncaoServidorTemporario->setCampoId          ('[cod_tipo_funcao]');
$obCmbTipoFuncaoServidorTemporario->setCampodesc        ('[cod_tipo_funcao] - [descricao]');
$obCmbTipoFuncaoServidorTemporario->setTitle            ('Informe o Tipo Funçao do Servidor Temporário.');
$obCmbTipoFuncaoServidorTemporario->preencheCombo       ($rsTipoFuncaoServidorTemporario);

$obCmbCargosServidorTemporario = new SelectMultiplo();
$obCmbCargosServidorTemporario->setName  ( 'arCargosServidorTemporario' );
$obCmbCargosServidorTemporario->setRotulo( "Cargo" );
$obCmbCargosServidorTemporario->setNull  ( true );
$obCmbCargosServidorTemporario->setObrigatorioBarra (true);
$obCmbCargosServidorTemporario->setTitle ( 'Cargos Disponíveis' );

$obCmbCargosServidorTemporario->SetNomeLista1( 'arCargosDisponiveisServidorTemporario' );
$obCmbCargosServidorTemporario->setCampoId1  ( '[cod_cargo]' );
$obCmbCargosServidorTemporario->setCampoDesc1( '[cod_cargo] - [descricao]' );
$obCmbCargosServidorTemporario->SetRecord1   ( $rsPessoalCargo  );

$obCmbCargosServidorTemporario->SetNomeLista2( 'arCargosSelecionadosServidorTemporario' );
$obCmbCargosServidorTemporario->setCampoId2  ( '[cod_cargo]' );
$obCmbCargosServidorTemporario->setCampoDesc2( '[cod_cargo] - [descricao]' );
$obCmbCargosServidorTemporario->SetRecord2   ( new RecordSet() );

$obspnListaFuncaoServidorTemporario = new Span;
$obspnListaFuncaoServidorTemporario->setId   ( 'spnListaFuncaoServidorTemporario' );

$obBtnIncluirCargoServidorTemporario = new Button;
$obBtnIncluirCargoServidorTemporario->setValue( 'Incluir' );
$obBtnIncluirCargoServidorTemporario->obEvento->setOnClick( "montaParametrosGET('incluirFuncaoServidorListaTemporario','cmbTipofuncaoServidorTemporario,arCargosSelecionadosServidorTemporario');" );

$obBtnLimparCargoServidorTemporario = new Button;
$obBtnLimparCargoServidorTemporario->setValue( 'Limpar' );
$obBtnLimparCargoServidorTemporario->obEvento->setOnClick( "executaFuncaoAjax('limparListaCargoServidorTemporario');" );

/*** Banco Empréstimo ***/
$obTMONBanco = new TMONBanco();
$obTMONBanco->recuperaTodos($rsBancoEmprestimo, " WHERE num_banco IN ('001','237','218','318','104','412','229','081','265','399','341','004','638','453','033') ");

$obCmbBancoEmprestimo = new Select();
$obCmbBancoEmprestimo->setName             ('cmbBancoEmprestimo');
$obCmbBancoEmprestimo->setId               ('cmbBancoEmprestimo');
$obCmbBancoEmprestimo->setValue            ('[cod_banco]');
$obCmbBancoEmprestimo->addOption           ('','Selecione');
$obCmbBancoEmprestimo->setRotulo           ('*Código de compensação do banco do Empréstimo');
$obCmbBancoEmprestimo->setCampoId          ('[cod_banco]');
$obCmbBancoEmprestimo->setCampodesc        ('[num_banco] - [nom_banco]');
$obCmbBancoEmprestimo->setTitle            ('Informe o código de compensação do banco do empréstimo.');
$obCmbBancoEmprestimo->preencheCombo       ($rsBancoEmprestimo);

$obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
$obTFolhaPagamentoEvento->recuperaTodos( $rsEventosEmprestimo, ' ORDER BY cod_evento' );

$obCmbEventosBancoEmprestimo = new SelectMultiplo();
$obCmbEventosBancoEmprestimo->setName  ( 'arEventosBancoEmprestimo' );
$obCmbEventosBancoEmprestimo->setRotulo( "Empréstimos Consignados" );
$obCmbEventosBancoEmprestimo->setNull  ( true );
$obCmbEventosBancoEmprestimo->setObrigatorioBarra (true);
$obCmbEventosBancoEmprestimo->setTitle ( 'Empréstimos Consignados Disponíveis' );

$obCmbEventosBancoEmprestimo->SetNomeLista1( 'arEventosDisponiveis' );
$obCmbEventosBancoEmprestimo->setCampoId1  ( '[cod_evento]' );
$obCmbEventosBancoEmprestimo->setCampoDesc1( '[codigo] - [descricao]' );
$obCmbEventosBancoEmprestimo->SetRecord1   ( $rsEventosEmprestimo  );

$obCmbEventosBancoEmprestimo->SetNomeLista2( 'arEventosSelecionados' );
$obCmbEventosBancoEmprestimo->setCampoId2  ( '[cod_evento]' );
$obCmbEventosBancoEmprestimo->setCampoDesc2( '[codigo] - [descricao]' );
$obCmbEventosBancoEmprestimo->SetRecord2   ( new RecordSet() );

$obspnListaBancoEmprestimo = new Span;
$obspnListaBancoEmprestimo->setId   ( 'spnListaBancoEmprestimo' );

$obBtnIncluirEventosEmprestimo = new Button;
$obBtnIncluirEventosEmprestimo->setValue( 'Incluir' );
$obBtnIncluirEventosEmprestimo->obEvento->setOnClick( "montaParametrosGET('incluirBancoEmprestimo','cmbBancoEmprestimo,arEventosSelecionados');" );

$obBtnLimparEventosEmprestimo = new Button;
$obBtnLimparEventosEmprestimo->setValue( 'Limpar' );
$obBtnLimparEventosEmprestimo->obEvento->setOnClick( "executaFuncaoAjax('limparListaEventosEmprestimo');" );

/*** Informações de Vantagens/Descontos ***/
$obRFolhaPagamentoEvento = new RFolhaPagamentoEvento();
$obRFolhaPagamentoEvento->listar($rsEventosProventos, " AND natureza = 'P' ", " ORDER BY codigo");

/*** Salário Base ***/
$obCmbSalarioBase = new SelectMultiplo();
$obCmbSalarioBase->setName  ( 'arSalariosBase' );
$obCmbSalarioBase->setRotulo( "Salário Base" );
$obCmbSalarioBase->setTitle ( 'Salários Base Disponíveis' );

$obCmbSalarioBase->SetNomeLista1( 'arSalariosBaseDisponiveis' );
$obCmbSalarioBase->setCampoId1  ( '[cod_evento]' );
$obCmbSalarioBase->setCampoDesc1( '[codigo] - [descricao]' );
$obCmbSalarioBase->SetRecord1   ( $rsEventosProventos  );

$obCmbSalarioBase->SetNomeLista2( 'arSalarioBaseSelecionados' );
$obCmbSalarioBase->setCampoId2  ( '[cod_evento]' );
$obCmbSalarioBase->setCampoDesc2( '[codigo] - [descricao]' );
$obCmbSalarioBase->SetRecord2   ( new RecordSet() );

/*** Demais Vantagens Salariais ***/
$obCmbVantagensSalariais = new SelectMultiplo();
$obCmbVantagensSalariais->setName  ( 'arVantagensSalariais' );
$obCmbVantagensSalariais->setRotulo( "Demais vantagens salariais" );
$obCmbVantagensSalariais->setTitle ( 'Demais vantagens salariais Disponíveis' );

$obCmbVantagensSalariais->SetNomeLista1( 'arVantagensSalariaisDisponiveis' );
$obCmbVantagensSalariais->setCampoId1  ( '[cod_evento]' );
$obCmbVantagensSalariais->setCampoDesc1( '[codigo] - [descricao]' );
$obCmbVantagensSalariais->SetRecord1   ( $rsEventosProventos  );

$obCmbVantagensSalariais->SetNomeLista2( 'arVantagensSalariaisSelecionados' );
$obCmbVantagensSalariais->setCampoId2  ( '[cod_evento]' );
$obCmbVantagensSalariais->setCampoDesc2( '[codigo] - [descricao]' );
$obCmbVantagensSalariais->SetRecord2   ( new RecordSet() );

/*** Gratificação de função ***/
$obCmbGratificacaoFuncao = new SelectMultiplo();
$obCmbGratificacaoFuncao->setName  ( 'arGratificacaoFuncao' );
$obCmbGratificacaoFuncao->setRotulo( "Gratificação de função" );
$obCmbGratificacaoFuncao->setTitle ( 'Gratificação de função Disponíveis' );

$obCmbGratificacaoFuncao->SetNomeLista1( 'arGratificacaoFuncaoDisponiveis' );
$obCmbGratificacaoFuncao->setCampoId1  ( '[cod_evento]' );
$obCmbGratificacaoFuncao->setCampoDesc1( '[codigo] - [descricao]' );
$obCmbGratificacaoFuncao->SetRecord1   ( $rsEventosProventos  );

$obCmbGratificacaoFuncao->SetNomeLista2( 'arGratificacaoFuncaoSelecionados' );
$obCmbGratificacaoFuncao->setCampoId2  ( '[cod_evento]' );
$obCmbGratificacaoFuncao->setCampoDesc2( '[codigo] - [descricao]' );
$obCmbGratificacaoFuncao->SetRecord2   ( new RecordSet() );

/*** Salário Família ***/
$obCmbSalarioFamilia = new SelectMultiplo();
$obCmbSalarioFamilia->setName  ( 'arSalarioFamilia' );
$obCmbSalarioFamilia->setRotulo( "Salário Família" );
$obCmbSalarioFamilia->setTitle ( 'Salário Família Disponíveis' );

$obCmbSalarioFamilia->SetNomeLista1( 'arSalarioFamiliaDisponiveis' );
$obCmbSalarioFamilia->setCampoId1  ( '[cod_evento]' );
$obCmbSalarioFamilia->setCampoDesc1( '[codigo] - [descricao]' );
$obCmbSalarioFamilia->SetRecord1   ( $rsEventosProventos  );

$obCmbSalarioFamilia->SetNomeLista2( 'arSalarioFamiliaSelecionados' );
$obCmbSalarioFamilia->setCampoId2  ( '[cod_evento]' );
$obCmbSalarioFamilia->setCampoDesc2( '[codigo] - [descricao]' );
$obCmbSalarioFamilia->SetRecord2   ( new RecordSet() );

/*** Horas Extras trabalhadas ***/
$obCmbHorasExtras = new SelectMultiplo();
$obCmbHorasExtras->setName  ( 'arHorasExtras' );
$obCmbHorasExtras->setRotulo( "Horas Extras trabalhadas" );
$obCmbHorasExtras->setTitle ( 'Horas Extras trabalhadas Disponíveis' );

$obCmbHorasExtras->SetNomeLista1( 'arHorasExtrasDisponiveis' );
$obCmbHorasExtras->setCampoId1  ( '[cod_evento]' );
$obCmbHorasExtras->setCampoDesc1( '[codigo] - [descricao]' );
$obCmbHorasExtras->SetRecord1   ( $rsEventosProventos  );

$obCmbHorasExtras->SetNomeLista2( 'arHorasExtrasSelecionados' );
$obCmbHorasExtras->setCampoId2  ( '[cod_evento]' );
$obCmbHorasExtras->setCampoDesc2( '[codigo] - [descricao]' );
$obCmbHorasExtras->SetRecord2   ( new RecordSet() );

/*** Demais Descontos ***/
$obCmbDemaisDescontos = new SelectMultiplo();
$obCmbDemaisDescontos->setName  ( 'arDemaisDescontos' );
$obCmbDemaisDescontos->setRotulo( "Demais Descontos" );
$obCmbDemaisDescontos->setTitle ( 'Informar o total dos demais descontos EXCETO IR, INSS, Consignado, pensão e plano de saúde' );

$obCmbDemaisDescontos->SetNomeLista1( 'arDemaisDescontosDisponiveis' );
$obCmbDemaisDescontos->setCampoId1  ( '[cod_evento]' );
$obCmbDemaisDescontos->setCampoDesc1( '[codigo] - [descricao]' );
$obCmbDemaisDescontos->SetRecord1   ( $rsEventosProventos  );

$obCmbDemaisDescontos->SetNomeLista2( 'arDemaisDescontosSelecionados' );
$obCmbDemaisDescontos->setCampoId2  ( '[cod_evento]' );
$obCmbDemaisDescontos->setCampoDesc2( '[codigo] - [descricao]' );
$obCmbDemaisDescontos->SetRecord2   ( new RecordSet() );

/*** Plano de Saúde/Odontológico ***/
$obRFolhaPagamentoEvento->listar($rsEventosDescontos, " AND natureza = 'D' ", " ORDER BY codigo");

$obCmbPlanoSaude = new SelectMultiplo();
$obCmbPlanoSaude->setName  ( 'arPlanoSaude' );
$obCmbPlanoSaude->setRotulo( "Plano de Saúde/Odontológico" );
$obCmbPlanoSaude->setTitle ( 'Planos de Saúde/Odontológico Disponíveis' );

$obCmbPlanoSaude->SetNomeLista1( 'arPlanoSaudeDisponiveis' );
$obCmbPlanoSaude->setCampoId1  ( '[cod_evento]' );
$obCmbPlanoSaude->setCampoDesc1( '[codigo] - [descricao]' );
$obCmbPlanoSaude->SetRecord1   ( $rsEventosDescontos  );

$obCmbPlanoSaude->SetNomeLista2( 'arPlanoSaudeSelecionados' );
$obCmbPlanoSaude->setCampoId2  ( '[cod_evento]' );
$obCmbPlanoSaude->setCampoDesc2( '[codigo] - [descricao]' );
$obCmbPlanoSaude->SetRecord2   ( new RecordSet() );

/*** Classe/Aplicação do Salário do Servidor ***/
$obTTCMBAFonteRecursoServidor = new TTCMBAFonteRecursoServidor();
$obTTCMBAFonteRecursoServidor->recuperaTodos($rsFonteRecurso, ' ORDER BY cod_tipo_fonte');

$obCmbFonteRecursoServidor = new Select();
$obCmbFonteRecursoServidor->setName              ('cmbFonteRecursoServidor');
$obCmbFonteRecursoServidor->setId                ('cmbFonteRecursoServidor');
$obCmbFonteRecursoServidor->setValue             ('[cod_tipo_fonte]');
$obCmbFonteRecursoServidor->addOption            ('','Selecione');
$obCmbFonteRecursoServidor->setRotulo            ('*Classe/Aplicação do Salário do Servidor');
$obCmbFonteRecursoServidor->setCampoId           ('[cod_tipo_fonte]');
$obCmbFonteRecursoServidor->setCampodesc         ('[cod_tipo_fonte] - [descricao]');
$obCmbFonteRecursoServidor->setTitle             ('Informe a Classe/Aplicação do Salário do Servidor.');
$obCmbFonteRecursoServidor->preencheCombo        ($rsFonteRecurso);

$obISelectMultiploLotacao = new ISelectMultiploLotacao;
$obISelectMultiploLotacao->setRotulo("*Lotação");

$obISelectMultiploLocal = new ISelectMultiploLocal;

$obspnListaFonteecursoServidor = new Span;
$obspnListaFonteecursoServidor->setId   ( 'spnListaFonteRecursoServidor' );

$obBtnIncluirFonteRecursoServidor = new Button;
$obBtnIncluirFonteRecursoServidor->setValue( 'Incluir' );
$obBtnIncluirFonteRecursoServidor->obEvento->setOnClick( "montaParametrosGET('incluirFonteRecursoServidor','cmbFonteRecursoServidor,inCodLotacaoSelecionados,inCodLocalSelecionados');" );

$obBtnLimparFonteRecursoServidor = new Button;
$obBtnLimparFonteRecursoServidor->setValue( 'Limpar' );
$obBtnLimparFonteRecursoServidor->obEvento->setOnClick( "executaFuncaoAjax('limparListaFonteRecursoServidor');" );

$obOk  = new Ok;
$obOk->setId   ("btnOk");
$obOk->setName ("btnOk");

$obLimpar = new Button;
$obLimpar->setValue ( "Limpar" );
$obLimpar->setId    ( "btnLimpar" );
$obLimpar->setName  ( "btnLimpar" );
$obLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparTudo');" );

$obFormulario = new Formulario();
$obFormulario->addForm          ( $obForm );
$obFormulario->addHidden        ( $obHdnAcao ); 
$obFormulario->addHidden        ( $obHdnSchema );
$obFormulario->addHidden        ( $obHdnEntidade );

$obFormulario->addComponente    ( $obLblEntidade );

/*** Função Servidor ***/
$obFormulario->addTitulo 	( 'Informações de Função do Servidor' );
$obFormulario->addComponente    ( $obCmbTipoFuncaoServidor );
$obFormulario->addComponente    ( $obCmbCargosServidor );
$obFormulario->agrupaComponentes( array( $obBtnIncluirCargoServidor, $obBtnLimparCargoServidor ) );
$obFormulario->addSpan          ( $obspnListaFuncaoServidor );

$obFormulario->addTitulo 	( 'Informações de Função Temporário' );
$obFormulario->addComponente    ( $obCmbTipoFuncaoServidorTemporario );
$obFormulario->addComponente    ( $obCmbCargosServidorTemporario );
$obFormulario->agrupaComponentes( array( $obBtnIncluirCargoServidorTemporario, $obBtnLimparCargoServidorTemporario ) );
$obFormulario->addSpan          ( $obspnListaFuncaoServidorTemporario );

$obFormulario->addTitulo 	( 'Informações de Aplicação do Salário do Servidor' );
$obFormulario->addComponente    ( $obCmbFonteRecursoServidor );
$obFormulario->addComponente    ( $obISelectMultiploLotacao );
$obFormulario->addComponente    ( $obISelectMultiploLocal   );
$obFormulario->agrupaComponentes( array( $obBtnIncluirFonteRecursoServidor, $obBtnLimparFonteRecursoServidor ) );
$obFormulario->addSpan          ( $obspnListaFonteecursoServidor );

$obFormulario->addTitulo 	( 'Informações de Vantagens/Descontos' );
$obFormulario->addComponente    ( $obCmbSalarioBase );
$obFormulario->addComponente    ( $obCmbVantagensSalariais );
$obFormulario->addComponente    ( $obCmbGratificacaoFuncao );
$obFormulario->addComponente    ( $obCmbSalarioFamilia );
$obFormulario->addComponente    ( $obCmbHorasExtras );
$obFormulario->addComponente    ( $obCmbDemaisDescontos );
$obFormulario->addComponente    ( $obCmbPlanoSaude );

$obFormulario->addTitulo 	( 'Informações de Empréstimos' );
$obFormulario->addComponente    ( $obCmbBancoEmprestimo );
$obFormulario->addComponente    ( $obCmbEventosBancoEmprestimo );
$obFormulario->agrupaComponentes( array( $obBtnIncluirEventosEmprestimo, $obBtnLimparEventosEmprestimo ) );
$obFormulario->addSpan          ( $obspnListaBancoEmprestimo );

$obFormulario->defineBarra      ( array( $obOk,$obLimpar ) );
$obFormulario->show();

// Carrega as listas já cadastradas
$jsOnLoad  = " executaFuncaoAjax('funcoesExistentes'); ";
$jsOnLoad .= " executaFuncaoAjax('funcoesTemporarioExistentes');";
$jsOnLoad .= " executaFuncaoAjax('bancoEventosExistentes');";
$jsOnLoad .= " executaFuncaoAjax('salarioBaseExistentes');";
$jsOnLoad .= " executaFuncaoAjax('vantagensSalariaisExistentes');";
$jsOnLoad .= " executaFuncaoAjax('gratificacaoFuncaoExistentes');";
$jsOnLoad .= " executaFuncaoAjax('salarioFamiliaExistentes');";
$jsOnLoad .= " executaFuncaoAjax('horasExtrasExistentes');";
$jsOnLoad .= " executaFuncaoAjax('demaisDescontosExistentes');";
$jsOnLoad .= " executaFuncaoAjax('planoSaudeExistentes');";
$jsOnLoad .= " executaFuncaoAjax('fonteRecursoExistentes');";

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>