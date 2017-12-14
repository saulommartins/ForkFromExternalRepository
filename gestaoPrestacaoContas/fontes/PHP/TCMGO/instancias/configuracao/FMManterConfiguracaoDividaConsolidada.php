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
 * Página de Formulário para configuração
 * Data de Criação   : 11/01/2011

 * @author Carlos Adriano

 * @ignore
 *
 * $Id: FMManterConfiguracaoUnidadeOrcamentaria.php 45121 2011-01-27 19:52:49Z silvia $

 * Casos de uso : uc-06.04.00
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php";
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php";
include_once TTGO."TTGOTipoOrgao.class.php";
include_once TTGO."TTCMGOTipoLancamento.class.php";
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php";

//Esvazia array que acumula lista de dívidas
Sessao::remove('arDivida');

$stPrograma = "ManterConfiguracaoDividaConsolidada";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include ($pgJs);
$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "alterar";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;
Sessao::write('arGestor', array());

if (isset($inCodigo)) {
    $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTOrcamentoOrgao = new TOrcamentoOrgao();
$obTOrcamentoOrgao->setDado('exercicio', Sessao::getExercicio());
$obTOrcamentoOrgao->recuperaDadosExercicio( $rsOrgao );

$obTTCMGOTipoLancamento = new TTCMGOTipoLancamento();
$obTTCMGOTipoLancamento->recuperaTodos( $rsTipoLancamento, '', 'cod_lancamento ASC' );

$obHdnId = new Hidden();
$obHdnId->setId( 'hdnId' );
$obHdnId->setName( 'hdnId' );
$obHdnId->setValue( '' );

$obCmbMes = new Select();
$obCmbMes->setRotulo( 'Período' );
$obCmbMes->setTitle( 'Selecione o mês' );
$obCmbMes->setName( 'inMes' );
$obCmbMes->setId( 'inMes' );
$obCmbMes->addOption( '',   'Selecione' );
$obCmbMes->addOption( '01',  'Janeiro'   );
$obCmbMes->addOption( '02',  'Fevereiro' );
$obCmbMes->addOption( '03',  'Março'     );
$obCmbMes->addOption( '04',  'Abril'     );
$obCmbMes->addOption( '05',  'Maio'      );
$obCmbMes->addOption( '06',  'Junho'     );
$obCmbMes->addOption( '07',  'Julho'     );
$obCmbMes->addOption( '08',  'Agosto'    );
$obCmbMes->addOption( '09',  'Setembro'  );
$obCmbMes->addOption( '10', 'Outubro'   );
$obCmbMes->addOption( '11', 'Novembro'  );
$obCmbMes->addOption( '12', 'Dezembro'  );
$obCmbMes->setStyle('width: 520');
$obCmbMes->obEvento->setOnChange('buscaDividas(this);');

$obCmbOrgao = new Select();
$obCmbOrgao->setRotulo( 'Orgão' );
$obCmbOrgao->setTitle( 'Selecione o orgão' );
$obCmbOrgao->setName( 'inOrgao' );
$obCmbOrgao->setId( 'inOrgao' );
$obCmbOrgao->addOption( '', 'Selecione' );
$obCmbOrgao->setCampoId( 'num_orgao' );
$obCmbOrgao->setCampoDesc( 'nom_orgao' );
$obCmbOrgao->setStyle('width: 520');
$obCmbOrgao->obEvento->setOnChange('buscaUnidade(this);');
$obCmbOrgao->preencheCombo( $rsOrgao );

$obCmbTipoLancamento = new Select();
$obCmbTipoLancamento->setRotulo( 'Tipo do lançamento' );
$obCmbTipoLancamento->setTitle( 'Selecione o tipo do lançamento' );
$obCmbTipoLancamento->setName( 'inTipoLancamento' );
$obCmbTipoLancamento->setId( 'inTipoLancamento' );
$obCmbTipoLancamento->addOption( '', 'Selecione' );
$obCmbTipoLancamento->setCampoId( 'cod_lancamento' );
$obCmbTipoLancamento->setCampoDesc( 'descricao' );
$obCmbTipoLancamento->setStyle('width: 520');
$obCmbTipoLancamento->preencheCombo( $rsTipoLancamento );

$obTxtLeiAutorizacao = new TextBox();
$obTxtLeiAutorizacao->setRotulo( 'Número da lei de autorização' );
$obTxtLeiAutorizacao->setName( 'stLeiAutorizacao' );
$obTxtLeiAutorizacao->setId( 'stLeiAutorizacao' );
$obTxtLeiAutorizacao->setSize( 7 );
$obTxtLeiAutorizacao->setMaxLength( 7 );
$obTxtLeiAutorizacao->obEvento->setOnKeyUp("mascaraDinamico('9999/99', this, event);");

$obTxtDtLeiAutorizacao = new Data();
$obTxtDtLeiAutorizacao->setName( 'dtLeiAutorizacao' );
$obTxtDtLeiAutorizacao->setId( 'dtLeiAutorizacao' );
$obTxtDtLeiAutorizacao->setRotulo( 'Data da lei de autorização' );
$obTxtDtLeiAutorizacao->setSize( 10 );
$obTxtDtLeiAutorizacao->setMaxLength( 10 );

$obCGM = new IPopUpCGMVinculado( $obForm );
$obCGM->setTabelaVinculo    ( 'sw_cgm'         );
$obCGM->setCampoVinculo     ( 'numcgm'         );
$obCGM->setNomeVinculo      ( 'Nome do credor' );
$obCGM->setRotulo           ( 'Nome do credor' );
$obCGM->setName             ( 'stCGM' );
$obCGM->setId               ( 'stCGM' );
$obCGM->obCampoCod->setName ( 'inCGM' );
$obCGM->obCampoCod->setId   ( 'inCGM' );
$obCGM->setNull             ( true );

$obTxtVlSaldoAnterior = new Moeda;
$obTxtVlSaldoAnterior->setName     ('vlSaldoAnterior');
$obTxtVlSaldoAnterior->setId       ('vlSaldoAnterior');
$obTxtVlSaldoAnterior->setValue    ('');
$obTxtVlSaldoAnterior->setRotulo   ('Valor do Saldo Anterior');
$obTxtVlSaldoAnterior->setTitle    ('Informe o valor saldo anterior.');
$obTxtVlSaldoAnterior->setSize     (14);
$obTxtVlSaldoAnterior->setMaxLength(14);

$obTxtVlContratacao = new Moeda;
$obTxtVlContratacao->setName     ('vlContratacao');
$obTxtVlContratacao->setId       ('vlContratacao');
$obTxtVlContratacao->setValue    ('');
$obTxtVlContratacao->setRotulo   ('Valor de Contratação');
$obTxtVlContratacao->setTitle    ('Informe o valor de contratação');
$obTxtVlContratacao->setSize     (14);
$obTxtVlContratacao->setMaxLength(14);

$obTxtVlAmortizacao = new Moeda;
$obTxtVlAmortizacao->setName     ('vlAmortizacao');
$obTxtVlAmortizacao->setId       ('vlAmortizacao');
$obTxtVlAmortizacao->setValue    ('');
$obTxtVlAmortizacao->setRotulo   ('Valor de Amortização');
$obTxtVlAmortizacao->setTitle    ('Informe o valor de amortização');
$obTxtVlAmortizacao->setSize     (14);
$obTxtVlAmortizacao->setMaxLength(14);

$obTxtVlCancelamento = new Moeda;
$obTxtVlCancelamento->setName     ('vlCancelamento');
$obTxtVlCancelamento->setId       ('vlCancelamento');
$obTxtVlCancelamento->setValue    ('');
$obTxtVlCancelamento->setRotulo   ('Valor de Cancelamento');
$obTxtVlCancelamento->setTitle    ('Informe o valor de cancelamento');
$obTxtVlCancelamento->setSize     (14);
$obTxtVlCancelamento->setMaxLength(14);

$obTxtVlEncampacao = new Moeda;
$obTxtVlEncampacao->setName     ('vlEncampacao');
$obTxtVlEncampacao->setId       ('vlEncampacao');
$obTxtVlEncampacao->setValue    ('');
$obTxtVlEncampacao->setRotulo   ('Valor de Encampação');
$obTxtVlEncampacao->setTitle    ('Informe o valor de encampação');
$obTxtVlEncampacao->setSize     (14);
$obTxtVlEncampacao->setMaxLength(14);

$obTxtVlAtualizacao = new Moeda;
$obTxtVlAtualizacao->setName     ('vlAtualizacao');
$obTxtVlAtualizacao->setId       ('vlAtualizacao');
$obTxtVlAtualizacao->setValue    ('');
$obTxtVlAtualizacao->setRotulo   ('Valor de atualização');
$obTxtVlAtualizacao->setTitle    ('Informe o valor de atualização');
$obTxtVlAtualizacao->setSize     (14);
$obTxtVlAtualizacao->setMaxLength(14);

$obTxtVlSaldoAtual = new Moeda;
$obTxtVlSaldoAtual->setName     ('vlSaldoAtual');
$obTxtVlSaldoAtual->setId       ('vlSaldoAtual');
$obTxtVlSaldoAtual->setValue    ('');
$obTxtVlSaldoAtual->setRotulo   ('Valor do Saldo Atual');
$obTxtVlSaldoAtual->setTitle    ('Informe o valor saldo atual');
$obTxtVlSaldoAtual->setSize     (14);
$obTxtVlSaldoAtual->setMaxLength(14);

$obSpanUnidade = new Span;
$obSpanUnidade->setId('spnUnidade');

$obBtOk = new Button();
$obBtOk->setValue( 'Incluir' );
$obBtOk->setId( 'btIncluir' );
$obBtOk->obEvento->setOnCLick( "montaParametrosGET( 'incluiDivida', 'hdnId,inMes,inOrgao,inUnidade,inTipoLancamento,stLeiAutorizacao,dtLeiAutorizacao,inCGM,inTipoPessoa,stCpfCnpj,vlSaldoAnterior,vlContratacao,vlAmortizacao,vlCancelamento,vlEncampacao,vlAtualizacao,vlSaldoAtual' );" );

$obBtLimpar = new Button();
$obBtLimpar->setValue( 'Limpar' );
$obBtLimpar->obEvento->setOnClick( "limparDivida();");

$obSpanDivida = new Span;
$obSpanDivida->setId('spnDivida');

//****************************************//
// Monta formulário
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addTitulo('Configuração da dívida consolidada');

$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnId);

$obFormulario->addComponente($obCmbMes);
$obFormulario->addComponente($obCmbOrgao);
$obFormulario->addSpan($obSpanUnidade);
$obFormulario->addComponente($obCmbTipoLancamento);
$obFormulario->addComponente($obTxtLeiAutorizacao);
$obFormulario->addComponente($obTxtDtLeiAutorizacao);
$obFormulario->addComponente($obCGM);
$obFormulario->addComponente($obTxtVlSaldoAnterior);
$obFormulario->addComponente($obTxtVlContratacao);
$obFormulario->addComponente($obTxtVlAmortizacao);
$obFormulario->addComponente($obTxtVlCancelamento);
$obFormulario->addComponente($obTxtVlEncampacao);
$obFormulario->addComponente($obTxtVlAtualizacao);
$obFormulario->addComponente($obTxtVlSaldoAtual);
$obFormulario->defineBarra( array( $obBtOk, $obBtLimpar ) );
$obFormulario->addSpan($obSpanDivida);

$obOk = new Ok();
$obLimpar = new Limpar();
$obFormulario->defineBarra(array($obOk, $obLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
