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
    * Página de Formulario Emitir Empenho Complementar
    * Data de Criação: ??/09/2007

    * @author Analista      : Anderson R. M. Buzo
    * @author Desenvolvedor : Rodrigo S. Rodrigues

    * @ignore

    $Id: FMManterEmpenhoComplementar.php 65311 2016-05-11 20:42:32Z michel $

    * Casos de uso: uc-02.03.36
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_INCLUDE."validaGF.inc.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoConfiguracao.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";
include_once CAM_FW_HTML."MontaAtributos.class.php";
include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeUsuario.class.php';
include_once CAM_GP_LIC_COMPONENTES.'IPopUpContrato.class.php';
require_once CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php";

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEmpenho";
$pgFormDiverso = "FMManterEmpenhoComplementar.php";
$pgForm        = "FMManterEmpenhoComplementar.php";
$pgProc        = "PRManterEmpenhoDiversos.php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJS          = "JS".$stPrograma.".js";

$stOrder = "";
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'incluir');

$hdnNumItem = "";
$stNomFornecedor = "";
$stDtEmpenho = "";
$stNomItem = "";
$stComplemento = "";
$nuQuantidade = "";
$nuVlUnitario = "";
$nuVlTotal = "";
include_once ($pgJS);

Sessao::remove('arBuscaContrato');

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($rsUltimoMesEncerrado->getCampo('mes') >= $mesAtual AND $boUtilizarEncerramentoMes == 'true') {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois o mês atual está encerrado!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    $inCodHistorico = 0;

    $obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
    $obREmpenhoConfiguracao       = new REmpenhoConfiguracao;
    $obREmpenhoEmpenho            = new REmpenhoEmpenho;

    $obREmpenhoConfiguracao->consultar();

    $boLiquidacaoAutomatica   = $obREmpenhoConfiguracao->getLiquidacaoAutomatica();

    $rsOrgao = $rsUnidade = new RecordSet ;

    $rsClassificacao = new RecordSet;
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->setExercicio( Sessao::getExercicio() );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarEntidadeRestos( $rsEntidade );
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->listar( $rsTipo, " cod_tipo <> 0 " );
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->listar( $rsHistorico );

    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->setExercicio( Sessao::getExercicio() );
    $obREmpenhoAutorizacaoEmpenho->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );

    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarOrgaoDespesaEntidadeUsuario( $rsOrgao, $stOrder );

    $obREmpenhoAutorizacaoEmpenho->listarUnidadeMedida( $rsUnidade );
    while ( !$rsUnidade->eof() ) {
        if ( $rsUnidade->getCampo("nom_unidade" ) == 'Unidade' ) {
            $inCodUnidade = $rsUnidade->getCampo("cod_unidade" ).'-'.$rsUnidade->getCampo("cod_grandeza" ).'-'.$rsUnidade->getCampo("nom_unidade" );
            $inCodUnidadePadrao = $rsUnidade->getCampo("cod_unidade" ).'-'.$rsUnidade->getCampo("cod_grandeza" ).'-'.$rsUnidade->getCampo("nom_unidade" );
        }
        $rsUnidade->proximo();
    }
    $rsUnidade->setPrimeiroElemento();

    $obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento( $stFormaExecucao );

    $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
    $stMascaraRubrica = $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();

    $stDtValidadeInicial = date('d/m')."/".Sessao::getExercicio();
    $stDtVencimento = "31/12/".Sessao::getExercicio();

    Sessao::remove('arItens');

    $obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

    //*****************************************************//
    // Define COMPONENTES DO FORMULARIO
    //*****************************************************//
    //Instancia o formulário
    $obForm = new Form;
    $obForm->setAction( $pgProc );
    $obForm->setTarget( "oculto" );

    //Define o objeto da data do ultimo Empenho cadastrado
    $obHdnUltimaDataEmpenho = new Hidden;
    $obHdnUltimaDataEmpenho->setName ( "dtUltimaDataEmpenho" );
    $obHdnUltimaDataEmpenho->setValue( '' );

    //Define o objeto da ação stAcao
    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ( "stAcao" );
    $obHdnAcao->setValue( $stAcao );

    //Define o Hidden para valor padrao da unidade
    $obHdnUnidadePadrao = new Hidden;
    $obHdnUnidadePadrao->setName ( "inCodUnidadePadrao" );
    $obHdnUnidadePadrao->setValue( $inCodUnidadePadrao );

    //Define o objeto de controle
    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName ( "stCtrl" );
    $obHdnCtrl->setValue( "" );

    $obHdnNumItem = new Hidden;
    $obHdnNumItem->setName ( "hdnNumItem" );
    $obHdnNumItem->setValue( $hdnNumItem );

    //Define o Hidden de Valor de Reserva
    $obHdnValorReserva = new Hidden;
    $obHdnValorReserva->setId   ( "hdnVlReserva");
    $obHdnValorReserva->setName ( "nuVlReserva" );
    $obHdnValorReserva->setValue( 0 );

    //Define o objeto para validacao da data do fornecedor
    $obHdnValidaFornecedor = new Hidden;
    $obHdnValidaFornecedor->setName ( "boMsgValidadeFornecedor" );
    $obHdnValidaFornecedor->setId   ( "boMsgValidadeFornecedor" );
    $obHdnValidaFornecedor->setValue( 'false' );
    
    //Define o nome da ação para controle no oculto para o mostrar o calcúlo do saldo anterior na label
    $obHdnEmitirEmpenhoComplementar = new Hidden;
    $obHdnEmitirEmpenhoComplementar->setName ( "hdnNomeAcao" );
    $obHdnEmitirEmpenhoComplementar->setId   ( "hdnNomeAcao" );
    $obHdnEmitirEmpenhoComplementar->setValue( "stEmitirEmpenhoComplementar" );

    // Define Objeto TextBox para Codigo da Entidade
    //$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
    //$obEntidadeUsuario->obTextBox->obEvento->setOnChange( 'getIMontaAssinaturas()' );
    //$obEntidadeUsuario->obSelect->obEvento->setOnChange( 'getIMontaAssinaturas()' );
    
    // Define Objeto TextBox para Codigo da Entidade
    $obTxtCodEntidade = new TextBox;
    $obTxtCodEntidade->setName('inCodEntidade');
    $obTxtCodEntidade->setId  ('inCodEntidade');

    if ($rsEntidade->getNumLinhas()==1) {
         $obTxtCodEntidade->setValue($rsEntidade->getCampo("cod_entidade"));
           $jsOnload = "montaParametrosGET('buscaDtEmpenho', 'inCodEntidade');";
    }

    $obTxtCodEntidade->setRotulo ('Entidade');
    $obTxtCodEntidade->setTitle  ('Selecione a entidade.');
    $obTxtCodEntidade->setInteiro(true);
    $obTxtCodEntidade->setNull   (false);
    
    // Define Objeto Select para Nome da Entidade
    $obEntidadeUsuario = new Select;
    $obEntidadeUsuario->setName ('stNomEntidade');
    $obEntidadeUsuario->setId   ('stNomEntidade');
    //$obEntidadeUsuario->setValue($inCodEntidade);

    if ($rsEntidade->getNumLinhas()>1) {
        $obEntidadeUsuario->addOption            ('', 'Selecione');
        $obEntidadeUsuario->obEvento->setOnChange("limparCampos();montaParametrosGET('buscaDtEmpenho', 'inCodEntidade,inCodContrato,inCodFornecedor,stExercicioContrato');getIMontaAssinaturas();");
        $obTxtCodEntidade->obEvento->setOnChange("limparCampos();montaParametrosGET('buscaDtEmpenho', 'inCodEntidade,inCodContrato,inCodFornecedor,stExercicioContrato');getIMontaAssinaturas();");
    }
    $obEntidadeUsuario->setCampoId   ('cod_entidade');
    $obEntidadeUsuario->setCampoDesc ('nom_cgm');
    $obEntidadeUsuario->setStyle     ('width: 520');
    $obEntidadeUsuario->preencheCombo($rsEntidade);
    $obEntidadeUsuario->setNull      (false);

    // Define Objeto BuscaInner para Empenho
    $obBscEmpenho = new BuscaInner;
    $obBscEmpenho->setRotulo ( "*Empenho Original" );
    $obBscEmpenho->setTitle  ( "Informe o número do empenho original"             );
    $obBscEmpenho->setId     ( "stNomFornecedor" );
    $obBscEmpenho->setValue  ( $stNomFornecedor  );
    $obBscEmpenho->setNull   ( true );
    $obBscEmpenho->obCampoCod->setName ( "inCodigoEmpenho" );
    $obBscEmpenho->obCampoCod->setId   ( "inCodigoEmpenho" );
    $obBscEmpenho->obCampoCod->setSize ( 8 );
    $obBscEmpenho->obCampoCod->setNull   ( true );
    $obBscEmpenho->obCampoCod->setInteiro  ( true );
    $obBscEmpenho->obCampoCod->setMaxLength( 8 );
    $obBscEmpenho->obCampoCod->setValue ( $request->get('inCodigoEmpenho') );
    $obBscEmpenho->obCampoCod->setAlign ("left");
    $obBscEmpenho->obCampoCod->obEvento->setOnBlur("montaParametrosGET('buscaEmpenho');");
    $obBscEmpenho->setFuncaoBusca("if(document.frm.".$obEntidadeUsuario->getName().".value) abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLEmpenho.php','frm','inCodigoEmpenho','stNomFornecedor','empenhoComplementar&inCodigoEntidade='+document.frm.inCodEntidade.value+'&stExercicioEmpenho='+".Sessao::getExercicio().",'".Sessao::getId()."','800','550');else alertaAviso('É necessário informar uma entidade para a conta.','frm','aviso','".Sessao::getId()."');");

    // Define Objeto Span Para Empenho
    $obSpanEmpenho = new Span;
    $obSpanEmpenho->setId( "spnEmpenho" );

    // Define Objeto TextArea para Descricao do Item
    $obTxtNomItem = new TextArea;
    $obTxtNomItem->setName   ( "stNomItem" );
    $obTxtNomItem->setId     ( "stNomItem" );
    $obTxtNomItem->setValue  ( $stNomItem  );
    $obTxtNomItem->setRotulo ( "*Descrição do Item" );
    $obTxtNomItem->setTitle  ( "Informe a descrição do item." );
    $obTxtNomItem->setNull   ( true );
    $obTxtNomItem->setRows   ( 1 );
    $obTxtNomItem->setCols   ( 100 );
    $obTxtNomItem->setMaxCaracteres(160);
    $obTxtNomItem->obEvento->setOnBlur( "proximoFoco(this.value);" );

    // Define Objeto TextArea para Complemento
    $obTxtComplemento = new TextArea;
    $obTxtComplemento->setName   ( "stComplemento" );
    $obTxtComplemento->setId     ( "stComplemento" );
    $obTxtComplemento->setValue  ( $stComplemento  );
    $obTxtComplemento->setRotulo ( "Complemento"   );
    $obTxtComplemento->setTitle  ( "Informe o complemento." );
    $obTxtComplemento->setNull   ( true );
    $obTxtComplemento->setRows   ( 2 );
    $obTxtComplemento->setCols   ( 100 );

    $obMarca = new IPopUpMarca($obForm);
    $obMarca->setNull               ( true );
    $obMarca->setRotulo             ( 'Marca' );
    $obMarca->setId                 ( 'stNomeMarca' );
    $obMarca->setName               ( 'stNomeMarca' );
    $obMarca->obCampoCod->setName   ( 'inMarca' );
    $obMarca->obCampoCod->setId     ( 'inMarca' );

    // Define Objeto Numeric para Quantidade
    $obTxtQuantidade = new Numerico;
    $obTxtQuantidade->setName     ( "nuQuantidade" );
    $obTxtQuantidade->setId       ( "nuQuantidade" );
    $obTxtQuantidade->setValue    ( $nuQuantidade  );
    $obTxtQuantidade->setRotulo   ( "*Quantidade"   );
    $obTxtQuantidade->setTitle    ( "Informe a quantidade." );
    $obTxtQuantidade->setNegativo ( false );
    $obTxtQuantidade->setNull     ( true );
    $obTxtQuantidade->setSize     ( 23 );
    $obTxtQuantidade->setMaxLength( 10 );
    $obTxtQuantidade->setDecimais ( 4 );
    $obTxtQuantidade->setFormatarNumeroBR  ( true );
    $obTxtQuantidade->obEvento->setOnChange( "gerarValorTotal(this);" );

    // Define Objeto Select para Unidade
    $obCmbUnidade = new Select;
    $obCmbUnidade->setName      ( "inCodUnidade" );
    $obCmbUnidade->setId        ( "inCodUnidade" );
    $obCmbUnidade->setRotulo    ( "*Unidade" );
    $obCmbUnidade->setTitle     ( "Informe a unidade." );
    $obCmbUnidade->setValue     ( $inCodUnidade  );
    $obCmbUnidade->addOption    ( "", "Selecione"  );
    $obCmbUnidade->setCampoId   ( "[cod_unidade]-[cod_grandeza]-[nom_unidade]" );
    $obCmbUnidade->setCampoDesc ( "nom_unidade"  );
    $obCmbUnidade->preencheCombo( $rsUnidade     );
    $obCmbUnidade->setNull      ( true          );

    // Define Objeto Moeda para Valor Unitário
    $obTxtVlUnitario = new ValorUnitario;
    $obTxtVlUnitario->setName     ( "nuVlUnitario" );
    $obTxtVlUnitario->setId       ( "nuVlUnitario" );
    $obTxtVlUnitario->setValue    ( $nuVlUnitario  );
    $obTxtVlUnitario->setRotulo   ( "*Valor Unitário" );
    $obTxtVlUnitario->setTitle    ( "Informe o valor unitário." );
    $obTxtVlUnitario->setNull     ( true );
    $obTxtVlUnitario->setDecimais ( 4  );
    $obTxtVlUnitario->setSize     ( 21 );
    $obTxtVlUnitario->setMaxLength( 10 );
    $obTxtVlUnitario->setFormatarNumeroBR  ( true );
    $obTxtVlUnitario->obEvento->setOnChange( "gerarValorTotal(this);" );

    // Define Objeto Moeda para Valor Total
    $obTxtVlTotal = new ValorTotal;
    $obTxtVlTotal->setName     ( "nuVlTotal" );
    $obTxtVlTotal->setId       ( "nuVlTotal" );
    $obTxtVlTotal->setValue    ( $nuVlTotal  );
    $obTxtVlTotal->setRotulo   ( "*Valor Total" );
    $obTxtVlTotal->setTitle    ( "Informe o valor total." );
    $obTxtVlTotal->setNull     ( true );
    $obTxtVlTotal->setReadOnly ( true );
    $obTxtVlTotal->setSize     ( 21 );
    $obTxtVlTotal->setMaxLength( 12 );
    $obTxtVlTotal->setFormatarNumeroBR  ( true );
    $obTxtVlTotal->obEvento->setOnChange( "gerarValorTotal(this);" );

    // Define Objeto Button para  Incluir Item
    $obBtnIncluir = new Button;
    $obBtnIncluir->setValue( "Incluir Item" );
    $obBtnIncluir->obEvento->setOnClick( "incluirItem();" );
    $obBtnIncluir->setName( "btnIncluir" );
    $obBtnIncluir->setId( "btnIncluir" );

    // Define Objeto Button para Limpar
    $obBtnLimpar = new Button;
    $obBtnLimpar->setValue( "Limpar" );
    $obBtnLimpar->obEvento->setOnClick( "limparItem();document.getElementById('stNomItem').focus();" );

    // Define Objeto Span Para lista de itens
    $obSpan = new Span;
    $obSpan->setId( "spnLista" );

    // Define Objeto Label para Valor Total dos Itens
    $obLblVlTotal = new Label;
    $obLblVlTotal->setId( "nuValorTotal" );
    $obLblVlTotal->setRotulo( "TOTAL: " );

    // Define Objeto Label para Valor da Reserva
    $obLblReserva = new Label;
    $obLblReserva->setId( "nuVlReserva" );
    $obLblReserva->setRotulo( "Valor da reserva: " );

    if($boLiquidacaoAutomatica=="true")
        $stLiquidacaoAutomatica = "SIM";
    else
        $stLiquidacaoAutomatica = "NAO";

    $inCodUF = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio());
    if ($inCodUF == 9 && Sessao::getExercicio() >= 2012) {

        include_once CAM_GPC_TGO_MAPEAMENTO.'TTCMGOModalidade.php';

        /* Monta combo com modalidades de licitação */
        $obModalidadeLicitacao = new TTCMGOModalidade();
        $obModalidadeLicitacao->recuperaTodos($rsModalidadeLicitacao);

        $obCmbModalidadeLicitacao = new Select;
        $obCmbModalidadeLicitacao->setRotulo ('Modalidade');
        $obCmbModalidadeLicitacao->setName('inModalidadeLicitacao');
        $obCmbModalidadeLicitacao->setId('inModalidadeLicitacao');
        $obCmbModalidadeLicitacao->setStyle('width: 520');
        $obCmbModalidadeLicitacao->setCampoId('cod_modalidade');
        $obCmbModalidadeLicitacao->setCampoDesc('descricao');
        $obCmbModalidadeLicitacao->addOption('', 'Selecione');
        $obCmbModalidadeLicitacao->preencheCombo($rsModalidadeLicitacao);
        $obCmbModalidadeLicitacao->setNull(false);
        $obCmbModalidadeLicitacao->obEvento->setOnChange('verificaModalidade(this);');

        // Define Objeto Span Para lista de itens
        $obSpanFundamentacaoLegal = new Span;
        $obSpanFundamentacaoLegal->setId('spnFundamentacaoLegal');
    }

      // Define Objeto SimNao para emitir liquidacao
    $obSimNaoEmitirLiquidacao = new SimNao();
    $obSimNaoEmitirLiquidacao->setRotulo ( "Liquidar este empenho após sua emissão" );
    $obSimNaoEmitirLiquidacao->setTitle  ( "Selecione liquidar este empenho após sua emissão." );
    $obSimNaoEmitirLiquidacao->setName   ( 'boEmitirLiquidacao'      );
    $obSimNaoEmitirLiquidacao->setNull   ( true                      );
    $obSimNaoEmitirLiquidacao->setChecked( $stLiquidacaoAutomatica   );

    include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
    $obMontaAssinaturas = new IMontaAssinaturas(null, 'nota_empenho');
    $obMontaAssinaturas->definePapeisDisponiveis('nota_empenho');
    $obMontaAssinaturas->setOpcaoAssinaturas( false );

    //Radio para definicao de tipo Item
    $obRdTipoItemC = new Radio;
    $obRdTipoItemC->setTitle      ( "Selecione o tipo de Item" );
    $obRdTipoItemC->setRotulo     ( "**Item de Almoxarifado" );
    $obRdTipoItemC->setName       ( "stTipoItemRadio" );
    $obRdTipoItemC->setId         ( "stTipoItemRadio1" );
    $obRdTipoItemC->setValue      ( "Catalogo" );
    $obRdTipoItemC->setLabel      ( "Sim" );
    $obRdTipoItemC->obEvento->setOnClick( "habilitaCampos('Catalogo');" );
    $obRdTipoItemC->setChecked( false );

    $obRdTipoItemD = new Radio;
    $obRdTipoItemD->setRotulo   ( "**Item de Almoxarifado" );
    $obRdTipoItemD->setName     ( "stTipoItemRadio" );
    $obRdTipoItemD->setId       ( "stTipoItemRadio2" );
    $obRdTipoItemD->setValue    ( "Descricao" );
    $obRdTipoItemD->setLabel    ( "Não" );
    $obRdTipoItemD->obEvento->setOnClick( "habilitaCampos('Descricao');" );
    $obRdTipoItemD->setChecked( true );

    $obHdnTipoItem = new Hidden;
    $obHdnTipoItem->setName ('stTipoItem');
    $obHdnTipoItem->setValue('Catalogo');

    $arRadios = array( $obRdTipoItemC, $obRdTipoItemD );

    include_once CAM_GP_ALM_COMPONENTES."IMontaItemUnidade.class.php";
    $obMontaItemUnidade = new IMontaItemUnidade($obForm);
    $obMontaItemUnidade->obIPopUpCatalogoItem->setRotulo("*Item");
    $obMontaItemUnidade->obIPopUpCatalogoItem->setNull(true);
    $obMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->setId("inCodItem");
    $obMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->obEvento->setOnBlur("javascript: unidadeItem(this.value);");
    $obMontaItemUnidade->obIPopUpCatalogoItem->setId( 'stNomItemCatalogo' );
    $obMontaItemUnidade->obIPopUpCatalogoItem->setName( 'stNomItemCatalogo' );
    $obMontaItemUnidade->obSpnInformacoesItem->setStyle('visibility:hidden; display:none');

    //Define o objeto para validacao da data do contrato
    $obHdnDtContrato = new Hidden;
    $obHdnDtContrato->setName ('dtContrato');
    $obHdnDtContrato->setId   ('dtContrato');
    $obHdnDtContrato->setValue('');
    
    $obContrato = new IPopUpContrato( $obForm );
    $obContrato->obHdnBoFornecedor->setValue(TRUE);
    $obContrato->obBuscaInner->obCampoCod->obEvento->setOnBlur("montaParametrosGET('validaContrato', 'inCodContrato,inCodEntidade,inCodFornecedor,stExercicioContrato');");
    $obContrato->obBuscaInner->setValoresBusca('', '', '');
    $obContrato->obBuscaInner->setFuncaoBusca("montaParametrosGET('montaBuscaContrato', 'inCodContrato,inCodEntidade,inCodFornecedor,stExercicioContrato');".$obContrato->obBuscaInner->getFuncaoBusca());

    //****************************************//
    // Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );
    $obFormulario->addTitulo( "Dados do Empenho" );

    $obFormulario->addHidden( $obHdnCtrl              );
    $obFormulario->addHidden( $obHdnNumItem           );
    $obFormulario->addHidden( $obHdnValorReserva      );
    $obFormulario->addHidden( $obHdnUltimaDataEmpenho );
    $obFormulario->addHidden( $obHdnAcao              );
    $obFormulario->addHidden( $obHdnUnidadePadrao     );
    $obFormulario->addHidden( $obHdnValidaFornecedor  );
    $obFormulario->addHidden( $obHdnEmitirEmpenhoComplementar  );
    
    $obFormulario->addComponenteComposto($obTxtCodEntidade, $obEntidadeUsuario);
    //$obFormulario->addComponente( $obEntidadeUsuario );
    $obFormulario->addComponente( $obBscEmpenho );
    $obFormulario->addSpan( $obSpanEmpenho );

    if ($inCodUF == 9 && Sessao::getExercicio() >= 2012) {
        $obFormulario->addTitulo('Modalidade TCMGO');
        $obFormulario->addComponente($obCmbModalidadeLicitacao);
        $obFormulario->addSpan($obSpanFundamentacaoLegal);
    }

    $obFormulario->addTitulo('Contrato');
    $obFormulario->addHidden( $obHdnDtContrato );
    $obContrato->geraFormulario($obFormulario);

    $obFormulario->addTitulo( "Insira os Ítens do Empenho Complementar" );
    $obFormulario->addHidden($obHdnTipoItem);
    $obFormulario->agrupaComponentes($arRadios);
    $obMontaItemUnidade->geraFormulario($obFormulario);
    $obFormulario->addComponente( $obTxtNomItem );
    $obFormulario->addComponente( $obTxtComplemento );
    $obFormulario->addComponente( $obMarca );
    $obFormulario->addComponente( $obTxtQuantidade );
    $obFormulario->addComponente( $obCmbUnidade );
    $obFormulario->addComponente( $obTxtVlUnitario );
    $obFormulario->addComponente( $obTxtVlTotal );
    $obFormulario->agrupaComponentes( array( $obBtnIncluir, $obBtnLimpar ) );
    $obFormulario->addSpan( $obSpan );
    $obFormulario->addComponente( $obLblVlTotal );
    $obFormulario->addComponente( $obSimNaoEmitirLiquidacao );

    $obOk = new Ok();
    $obLimpar = new Limpar();
    $obLimpar->obEvento->setOnClick( 'limparOrdem();' );

    $obMontaAssinaturas->geraFormulario( $obFormulario );

    $obFormulario->defineBarra( array( $obOk, $obLimpar ) );

    $obFormulario->show();

    if ( $obMontaAssinaturas->getOpcaoAssinaturas() ) {
        echo $obMontaAssinaturas->disparaLista();
    }

}
echo ("<script>habilitaCampos('Descricao')</script>");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
