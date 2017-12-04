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
 * Tela do formulário para inclusão de Solicitação de compra
 * Data de Criação: 10/09/2006

 * @author Analista     : Diego Victoria
 * @author Desenvolvedor: Rodrigo

 * Casos de uso: uc-03.04.01

 $Id: FMManterSolicitacaoCompra.php 66087 2016-07-18 20:33:50Z carlos.silva $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GP_COM_COMPONENTES."IValidaExercicio.class.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/CGM/classes/componentes/ILabelCGM.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/CGM/classes/componentes/IPopUpCGM.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/IMontaQuantidadeValores.class.php';
include_once CAM_GP_ALM_COMPONENTES."ISelectAlmoxarifado.class.php";
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
include_once CAM_GP_ALM_COMPONENTES."ILabelAlmoxarifado.class.php";
include_once CAM_GP_ALM_COMPONENTES."IPopUpCentroCustoUsuario.class.php";
include_once CAM_GP_ALM_COMPONENTES."IMontaItemUnidade.class.php";
include_once CAM_GP_COM_COMPONENTES."IPopUpEditObjeto.class.php";
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";
include_once CAM_GP_COM_COMPONENTES."IMontaDotacaoDesdobramento.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoConvenio.class.php";

$stPrograma = "ManterSolicitacaoCompra";
$pgFilt         = "FL".$stPrograma.".php";
$pgList         = "LS".$stPrograma.".php";
$pgForm         = "FM".$stPrograma.".php";
$pgProc         = "PR".$stPrograma.".php";
$pgOcul         = "OC".$stPrograma.".php";
$pgJs           = "JS".$stPrograma.".js";

include_once($pgJs);
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

$stAcao = $request->get('stAcao');

Sessao::write('arValores'		   , array());
Sessao::write('arSaldoDotacoes'    , array());
Sessao::write('arValoresExcluidos' , array());

$boReservaRigida = SistemaLegado::pegaConfiguracao('reserva_rigida', '35', Sessao::getExercicio());
$boReservaRigida = ($boReservaRigida == 'true') ? true : false;

$boReservaAutorizacao = SistemaLegado::pegaConfiguracao('reserva_autorizacao', '35', Sessao::getExercicio());
$boReservaAutorizacao = ($boReservaAutorizacao == 'true') ? true : false;

if(!$boReservaRigida && !$boReservaAutorizacao){
    $stMsg = "Obrigatório Configurar o Tipo de Reserva em: Gestão Patrimonial :: Compras :: Configuração :: Alterar Configuração";

    $obLblMsgTipoReserva = new Label();
    $obLblMsgTipoReserva->setRotulo ( "Aviso" );
    $obLblMsgTipoReserva->setValue  ( $stMsg  );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo     ( 'Configuração Tipo de Reserva'   );
    $obFormulario->addComponente ( $obLblMsgTipoReserva             );
    $obFormulario->show();

    exit();
}

// Pegar informações da solicitação( para data de solicitação)
$obTComprasSolicitacao = new TComprasSolicitacao();
$obTComprasSolicitacao->setDado( 'cod_solicitacao', $request->get('cod_solicitacao') );
$obTComprasSolicitacao->setDado( 'exercicio'      , $request->get('exercicio')       );
$obTComprasSolicitacao->setDado( 'cod_entidade'   , $request->get('cod_entidade')    );
$obTComprasSolicitacao->consultar();

//Formatar data buscada no banco (timestamp)
$data = $obTComprasSolicitacao->getDado( 'timestamp' );
$ano = substr($data, 0,4); // mes
$mes = substr($data, 5, 2); // dia
$dia = substr($data, 8, 2); // ano
$dataFormatada = $dia.'/'.$mes.'/'.$ano;

//Define Registro de Preço
$obRadioRegistroPrecoSim = new Radio;
$obRadioRegistroPrecoSim->setRotulo( "Registro de Preços" );
$obRadioRegistroPrecoSim->setTitle( "Selecione se esta Solicitação de Compras servirá para um Registro de Preços." );
$obRadioRegistroPrecoSim->setName( "boRegistroPreco" );
$obRadioRegistroPrecoSim->setId( "boRegistroPrecoSim" );
$obRadioRegistroPrecoSim->setValue( "true" );
$obRadioRegistroPrecoSim->setLabel( "Sim" );
$obRadioRegistroPrecoSim->setNull( false );
$obRadioRegistroPrecoSim->obEvento->setOnChange( "montaParametrosGET('montaDotacao', 'inCodEntidade, inCodCentroCusto,nuVlTotal,boRegistroPreco');" );

$obRadioRegistroPrecoNao = new Radio;
$obRadioRegistroPrecoNao->setRotulo( "Registro de Preços" );
$obRadioRegistroPrecoNao->setTitle( "Selecione se esta Solicitação de Compras servirá para um Registro de Preços." );
$obRadioRegistroPrecoNao->setName( "boRegistroPreco" );
$obRadioRegistroPrecoNao->setId( "boRegistroPrecoNao" );
$obRadioRegistroPrecoNao->setValue( "false" );
$obRadioRegistroPrecoNao->setLabel( "Não" );
$obRadioRegistroPrecoNao->setNull( false );
$obRadioRegistroPrecoNao->obEvento->setOnChange( "montaParametrosGET('montaDotacao', 'inCodEntidade, inCodCentroCusto,nuVlTotal,boRegistroPreco');montaParametrosGET('calculaValorReservadoDotacao', 'boRegistroPreco');" );

if($obTComprasSolicitacao->getDado('registro_precos') == 't'){
    $obRadioRegistroPrecoSim->setChecked( true );
    $obRadioRegistroPrecoNao->setChecked( false );
}
else{
    $obRadioRegistroPrecoSim->setChecked( false );
    $obRadioRegistroPrecoNao->setChecked( true );
}

//Define o exercicio corrente
$obLblExercicio = new Label;
$obLblExercicio->setRotulo( "Exercício" );
if ($stAcao == 'alterar') {
    $obLblExercicio->setValue ( $request->get('exercicio') );
} else {
    $obLblExercicio->setValue ( Sessao::getExercicio() );
}

if ($stAcao == 'alterar') {
    $obLblDataSolicitacao = new Label;
    $obLblDataSolicitacao->setRotulo( "Data Solicitação" );
    $obLblDataSolicitacao->setValue ( $dataFormatada );

    # Código da Solicitação.
    $obLblSolicitacao = new Label;
    $obLblSolicitacao->setId     ( 'stSolicitacao'                  );
    $obLblSolicitacao->setrotulo ( 'Solicitação'                    );
    $obLblSolicitacao->setValue  ( $request->get('cod_solicitacao') );
}

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setId   ( "stAcao" );
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//Define o objeto de configuracao
$rsConfiguracao = new RecordSet();

$obTConfiguracao = new TAdministracaoConfiguracao();
$obTConfiguracao->setDado ( "cod_modulo", 35 );
$obTConfiguracao->setDado ( "parametro" , "dotacao_obrigatoria_solicitacao" );
$obTConfiguracao->setDado ( "exercicio" , Sessao::getExercicio() );
$obTConfiguracao->consultar($rsConfiguracao);

$obHdnConfiguracao = new Hidden;
$obHdnConfiguracao->setName  ( "boConfiguracao"                   );
$obHdnConfiguracao->setValue ( $obTConfiguracao->getDado("valor") );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obHdnItem = new Hidden;
$obHdnItem->setName  ( "HdnNomItem" );
$obHdnItem->setId    ( "HdnNomItem" );
$obHdnItem->setValue ( ""           );

$obHdnUnidade = new Hidden;
$obHdnUnidade->setName  ( "HdnNomUnidade" );
$obHdnUnidade->setId    ( "HdnNomUnidade" );
$obHdnUnidade->setValue ( ""              );

if ($stAcao=="alterar") {
    $obHdnSolicitacao = new Hidden;
    $obHdnSolicitacao->setId    ( "HdnInSolicitacao"               );
    $obHdnSolicitacao->setName  ( "HdnInSolicitacao"               );
    $obHdnSolicitacao->setValue ( $request->get('cod_solicitacao') );

    $obHdnExercicio = new Hidden;
    $obHdnExercicio->setId    ( 'hdnExercicio'             );
    $obHdnExercicio->setName  ( 'hdnExercicio'             );
    $obHdnExercicio->setValue ( $request->get('exercicio') );
}

$obHdnCodItem= new Hidden;
$obHdnCodItem->setName  ( "HdnCodItem" );
$obHdnCodItem->setId    ( "HdnCodItem" );
$obHdnCodItem->setValue ( ""           );

$obHdnCentroDeCusto = new Hidden;
$obHdnCentroDeCusto->setName  ( "HdnNomCentroCusto" );
$obHdnCentroDeCusto->setId    ( "HdnNomCentroCusto" );
$obHdnCentroDeCusto->setValue ( ""                  );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName( 'HdnCodEntidade' );
$obHdnCodEntidade->setId  ( 'HdnCodEntidade' );

$obHdnCodClassificacao = new Hidden;
$obHdnCodClassificacao->setName( 'HdnCodClassificacao' );
$obHdnCodClassificacao->setId  ( 'HdnCodClassificacao' );

//Define objeto de select multiplo de entidade por usuários
$obISelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
$obISelectEntidadeUsuario->setNull(false);
$obISelectEntidadeUsuario->obSelect->setNull(false);
$obISelectEntidadeUsuario->obTextBox->setNull(false);
$obISelectEntidadeUsuario->obSelect->obEvento->setOnChange( "montaParametrosGET('montaDotacao', 'inCodEntidade,inCodCentroCusto,nuVlTotal,boRegistroPreco'); document.getElementById('HdnCodEntidade').value = this.value; montaParametrosGET('recuperaDataContabil', 'inCodEntidade', '');" );
$obISelectEntidadeUsuario->obTextBox->obEvento->setOnChange( "montaParametrosGET('montaDotacao', 'inCodEntidade,inCodCentroCusto,nuVlTotal,boRegistroPreco'); document.getElementById('HdnCodEntidade').value = this.value; montaParametrosGET('recuperaDataContabil', 'inCodEntidade', '');" );

if ($obISelectEntidadeUsuario->inCodEntidade != '') {
    $obHdnCodEntidade->setValue( $obISelectEntidadeUsuario->inCodEntidade );
}

// Se houver um único registro de Entidade, executa comando para preencher Data solicitação
if ((count($obISelectEntidadeUsuario->obSelect->arOption) == 1) && ($stAcao=="incluir")) {
    $stJs .= "montaParametrosGET('montaDotacao', 'inCodEntidade,inCodCentroCusto,nuVlTotal,boRegistroPreco'); montaParametrosGET('recuperaDataContabil', 'inCodEntidade', '');";
}

// Define objeto Data da Solicitação
$obDtSolicitacao = new Data;
$obDtSolicitacao->setName   ( "stDtSolicitacao" );
$obDtSolicitacao->setId     ( "stDtSolicitacao" );
$obDtSolicitacao->setValue  ( "" );
$obDtSolicitacao->setRotulo ( "Data da Solicitação" );
$obDtSolicitacao->setTitle  ( 'Informe a data da solicitação.' );
$obDtSolicitacao->setNull   ( false );
$obDtSolicitacao->setLabel  ( TRUE );

$obHdnDtSolicitacao = new Hidden();
$obHdnDtSolicitacao->setName('HdnDtSolicitacao');
$obHdnDtSolicitacao->setId  ('HdnDtSolicitacao');

$obObjeto = new IPopUpEditObjeto($obForm);
$obObjeto->setNull  (true      );
$obObjeto->setRotulo("*Objeto" );
$obObjeto->setName  ("stObjeto");

//Define objeto de requisitante
$obLblRequisitante = new ILabelCGM();
$obLblRequisitante->setRotulo( "Requisitante" );
$obLblRequisitante->setNumCGM ( Sessao::read('numCgm'));

$stFiltro = "\n AND tabela_vinculo.ativo = 't'";

$obSolicitante = new IPopUpCGMVinculado( $obForm );
$obSolicitante->setTabelaVinculo    ( 'compras.solicitante' );
$obSolicitante->setCampoVinculo     ( 'solicitante' );
$obSolicitante->setNomeVinculo      ( 'Solicitante' );
$obSolicitante->setRotulo           ( 'Solicitante' );
$obSolicitante->setTitle            ( 'Informe o Solicitante.' );
$obSolicitante->setName             ( 'stNomCGM' );
$obSolicitante->setId               ( 'stNomCGM' );
$obSolicitante->obCampoCod->setName ( 'inCGM' );
$obSolicitante->obCampoCod->setId   ( 'inCGM' );
$obSolicitante->setNull             ( false );
$obSolicitante->setFiltroVinculado  ( $stFiltro );

$obAlmoxarifado = new ISelectAlmoxarifado($obForm);
$obAlmoxarifado->setNull ( false                       );
$obAlmoxarifado->setTitle( "Selecione o almoxarifado." );
$obLblAlmoxarifado = new ILabelAlmoxarifado($obForm);

$obLocalizacaoEntrega = new IPopUpCGMVinculado( $obForm );
$obLocalizacaoEntrega->setTabelaVinculo ( 'sw_cgm_pessoa_juridica' );
$obLocalizacaoEntrega->setCampoVinculo ( 'numcgm' );
$obLocalizacaoEntrega->setNomeVinculo ( 'Localização de Entrega' );
$obLocalizacaoEntrega->setRotulo ( 'Localização de Entrega' );
$obLocalizacaoEntrega->setTitle  ( 'Informe a localização de entrega.' );
$obLocalizacaoEntrega->setId     ( 'stNomEntrega' );
$obLocalizacaoEntrega->obCampoCod->setName ( 'inEntrega' );
$obLocalizacaoEntrega->obCampoCod->setId ( 'inEntrega' );

$obTxtPrazoEntrega = new TextBox;
$obTxtPrazoEntrega->setName      ( "stPrazoEntrega" );
$obTxtPrazoEntrega->setValue     ( "" );
$obTxtPrazoEntrega->setRotulo    ( "Prazo de Entrega" );
$obTxtPrazoEntrega->setTitle     ( "Informe o prazo de entrega.");
$obTxtPrazoEntrega->setNull      ( false );
$obTxtPrazoEntrega->setInteiro   ( true );
$obTxtPrazoEntrega->setMaxLength ( 4 );
$obTxtPrazoEntrega->setSize      ( 5 );

$obLblDia = new Label();
$obLblDia->setRotulo( "dias" );
$obLblDia->setValue ("dias"  );

// Define Objeto TextArea para observações/justificativas
$stObservacao = $request->get("stObservacao");
$obTxtObs = new TextArea;
$obTxtObs->setName   ( "stObservacao" );
$obTxtObs->setId     ( "stObservacao" );
$obTxtObs->setValue  ( $stObservacao );
$obTxtObs->setRotulo ( "OBS/Justificativa" );
$obTxtObs->setTitle  ( "Informe a observação/justificativa." );
$obTxtObs->setNull   ( false );
$obTxtObs->setRows   ( 2 );
$obTxtObs->setCols   ( 100 );

/* CONVENIO */
require_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoConvenio.class.php";
$obTLicitacaoConvenio = new TLicitacaoConvenio;
$obTLicitacaoConvenio->recuperaConvenioSolicitacao ( $rsConvenio );

$obCmbConvenio = new Select();
$obCmbConvenio->setTitle      ( "Selecione o convênio" );
$obCmbConvenio->setRotulo     ( "Convênio" );
$obCmbConvenio->setName       ( "inCodConvenio" );
$obCmbConvenio->setId         ( "inCodConvenio" );
$obCmbConvenio->setNull       ( true  );
$obCmbConvenio->setCampoId    ( "[exercicio]-[num_convenio]" );
$obCmbConvenio->addOption     ( "", "Selecione" );
$obCmbConvenio->setCampoDesc  ( "[exercicio]-[num_convenio]-[fundamentacao]" );
$obCmbConvenio->preencheCombo ( $rsConvenio );

//Itens de Outra Solicitação
$obRdbItensOutraSolicitacaoSim = new Radio;
$obRdbItensOutraSolicitacaoSim->setRotulo('Incluir Itens de Outra Solicitação');
$obRdbItensOutraSolicitacaoSim->setName('boItensOutraSolicitacao');
$obRdbItensOutraSolicitacaoSim->setId('boItensOutraSolicitacaoSim');
$obRdbItensOutraSolicitacaoSim->setLabel('Sim');
$obRdbItensOutraSolicitacaoSim->setValue('sim');
$obRdbItensOutraSolicitacaoSim->obEvento->setOnClick( "montaParametrosGET('preencheSpnItensOutraSolicitacao', 'HdnInSolicitacao, HdnCodEntidade, hdnExercicio, stAcao');" );

$obRdbItensOutraSolicitacaoNao = new Radio;
$obRdbItensOutraSolicitacaoNao->setRotulo('Incluir Itens de Outra Solicitação');
$obRdbItensOutraSolicitacaoNao->setName('boItensOutraSolicitacao');
$obRdbItensOutraSolicitacaoNao->setId('boItensOutraSolicitacaoNao');
$obRdbItensOutraSolicitacaoNao->setLabel('Não');
$obRdbItensOutraSolicitacaoNao->setValue('nao');
$obRdbItensOutraSolicitacaoNao->setChecked( true );
$obRdbItensOutraSolicitacaoNao->obEvento->setOnClick( "executaFuncaoAjax('limparSpnItensOutraSolicitacao');" );

include_once CAM_GP_COM_COMPONENTES."IMontaDotacaoDesdobramentoPadrao.class.php";

//Define o objeto de configuracao
$rsConfiguracao = new RecordSet();
$obTConfiguracao = new TAdministracaoConfiguracao();
$obTConfiguracao->setDado( "parametro" ,"dotacao_obrigatoria_solicitacao" );
$obTConfiguracao->setDado( "exercicio" , Sessao::getExercicio()           );
$obTConfiguracao->recuperaPorChave( $rsConfiguracao );

$obHdnConfiguracao = new Hidden;
$obHdnConfiguracao->setName  ("boConfiguracao");
$obHdnConfiguracao->setValue ($rsConfiguracao->getCampo("valor") );

$obDotacaoPadrao = new IMontaDotacaoDesdobramentoPadrao();
$obDotacaoPadrao->obBscDespesa->obCampoCod->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodEntidade='+document.frm.inCodEntidade.value+'&inCodDespesaPadrao='+document.frm.inCodDespesaPadrao.value,'verificaEntidadeDotacao');");
$obDotacaoPadrao->setConsideraReserva( true );

$obSpnItensOutraSolicitacao = new Span();
$obSpnItensOutraSolicitacao->setID ('spnItensOutraSolicitacao');

$obMontaItemUnidade = new IMontaItemUnidade($obForm);
$obMontaItemUnidade->obIPopUpCatalogoItem->setRotulo("*Item");
$obMontaItemUnidade->obIPopUpCatalogoItem->setNull(true);

//Em IMontaItemUnidade, os Hiddens stNomUnidade e inCodUnidadeMedida estão desabilitados.
//Habilitando estes campos, necessários no funcionamento da ação.
$obLblUnidadeMedida = new Label;
$obLblUnidadeMedida->setRotulo  ('Unidade de Medida'    );
$obLblUnidadeMedida->setId      ('stUnidadeMedida'      );
$obLblUnidadeMedida->setValue   ('&nbsp;'               );

$obHdnCodUnidadeMedida = new Hidden;
$obHdnCodUnidadeMedida->setName ( 'inCodUnidadeMedida'  );
$obHdnCodUnidadeMedida->setId   ( 'inCodUnidadeMedida'  );

$obHdnNomUnidadeMedida = new Hidden;
$obHdnNomUnidadeMedida->setName ( 'stNomUnidade'        );
$obHdnNomUnidadeMedida->setId   ( 'stNomUnidade'        );

$obFormularioSpan = new Formulario;
$obFormularioSpan->addComponente( $obLblUnidadeMedida       );
$obFormularioSpan->addHidden    ( $obHdnCodUnidadeMedida    );
$obFormularioSpan->addHidden    ( $obHdnNomUnidadeMedida    );
$obFormularioSpan->montaInnerHTML();
$stHtmlSpanItem = $obFormularioSpan->getHTML();

$obMontaItemUnidade->obSpnInformacoesItem->setValue( $stHtmlSpanItem );
//Fim da Habilitação dos Hiddens stNomUnidade e inCodUnidadeMedida.

$stJsOnBlur  = "montaParametrosGET('valorUnitarioUltimaCompra');";
$stJsOnBlur .= "montaParametrosGET('saldoEstoque', 'inCodItem, inCodCentroCusto');";
$stJsOnBlur .= "montaParametrosGET('BuscaComplemento');";

$obMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->obEvento->setOnChange($stJsOnBlur);

$obSpnVlrReferencia = new Span;
$obSpnVlrReferencia->setId ( "spnVlrReferencia" );

$obTxtComplemento = new TextArea;
$obTxtComplemento->setName   ( "stComplemento"          );
$obTxtComplemento->setId     ( "stComplemento"          );
$obTxtComplemento->setValue  ( $stObservacao            );
$obTxtComplemento->setRotulo ( "Complemento"            );
$obTxtComplemento->setTitle  ( "Informe o complemento." );
$obTxtComplemento->setNull   ( true                     );
$obTxtComplemento->setRows   ( 2                        );
$obTxtComplemento->setCols   ( 100                      );
$obTxtComplemento->setMaxCaracteres( 200                );

$obCentroCustoUsuario = new IPopUpCentroCustoUsuario($obForm);
$obCentroCustoUsuario->setNull  (true);
$obCentroCustoUsuario->setRotulo('*Centro de Custo');
$obCentroCustoUsuario->obCampoCod->setId ('inCodCentroCusto');

$stJsOnBlur  = "if (saldoCentroItem(this.value)) { ";
$stJsOnBlur .= "montaParametrosGET('montaDotacao', 'inCodEntidade, inCodCentroCusto,nuVlTotal,boRegistroPreco');";
$stJsOnBlur .= "montaParametrosGET('saldoEstoque', 'inCodItem, inCodCentroCusto');";
$stJsOnBlur .= "montaParametrosGET('BuscaComplemento'); }";

$obCentroCustoUsuario->obCampoCod->obEvento->setOnBlur($obCentroCustoUsuario->obCampoCod->obEvento->getOnBlur()." if (this.value != '') { ".$stJsOnBlur." }");

//Define o exercicio corrente
$obLblSaldoEstoque = new Label;
$obLblSaldoEstoque->setRotulo( "Saldo em Estoque" );
$obLblSaldoEstoque->setId    ( "lblSaldoEstoque"  );
$obLblSaldoEstoque->setValue ( ""                 );

$obMontaQuantidadeValores = new IMontaQuantidadeValores();
$obMontaQuantidadeValores->obValorUnitario->setRotulo( 'Valor Unitário' );
$obMontaQuantidadeValores->obValorUnitario->setValue( '0,00' );
$obMontaQuantidadeValores->obValorUnitario->setDecimais ( 2 );
$obMontaQuantidadeValores->obValorUnitario->setNull( true );
$obMontaQuantidadeValores->obValorTotal->setRotulo( 'Valor Total' );
$obMontaQuantidadeValores->obValorTotal->setValue( '0,00' );
$obMontaQuantidadeValores->obValorTotal->setDecimais( 2 );
$obMontaQuantidadeValores->obValorTotal->setNull( true );

$obSpanDotacao = new Span();
$obSpanDotacao->setID( 'spnDotacao' );

// Define Objeto Button para Incluir Item
$obBtnIncluirItemSolicitacao = new Button;
$obBtnIncluirItemSolicitacao->setValue( "Incluir" );
$obBtnIncluirItemSolicitacao->setId( "incluiItem" );
$obBtnIncluirItemSolicitacao->obEvento->setOnClick( "montaParametrosGET('incluirListaItens');" );

// Define Objeto Button para Limpar Item
$obBtnLimparItemSolicitacao = new Button;
$obBtnLimparItemSolicitacao->setValue( "Limpar" );
$obBtnLimparItemSolicitacao->obEvento->setOnClick( "LimparItensSolicitacao();" );

$obSpnListaSolicitacoes = new Span;
$obSpnListaSolicitacoes->setID( "spnListaSolicitacoes" );

$obChkRelatorio = new CheckBox();
$obChkRelatorio->setName( 'boRelatorio' );
$obChkRelatorio->setRotulo( 'Emitir Relatório' );
$obChkRelatorio->setTitle( 'Emitir Relatório da Solicitação de Compra.' );

$obBtnOkSolicitacao = new Ok(true);
$obBtnOkSolicitacao->setId( 'Ok' );

$obBtnLimparSolicitacao = new Limpar();
$obBtnLimparSolicitacao->obEvento->setOnClick( "limpaFormulario(); setFocusEntidade(); $('inCodEntidade').focus(); montaParametrosGET( 'deletarListaItens', 'noParameters' );" );

$obFormulario = new Formulario();
$obFormulario->setAjuda('UC-03.04.01');
$obFormulario->addForm   ( $obForm );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnConfiguracao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnItem );
$obFormulario->addHidden ( $obHdnUnidade );
$obFormulario->addHidden ( $obHdnCodEntidade );
$obFormulario->addHidden ( $obHdnCodClassificacao );

if ($stAcao == "alterar") {
  $obFormulario->addHidden ( $obHdnSolicitacao );
  $obFormulario->addHidden ( $obHdnExercicio );
}

$obFormulario->addHidden  ( $obHdnCodItem );
$obFormulario->addHidden  ( $obHdnCentroDeCusto );
$obFormulario->addTitulo  ( "Dados da Solicitação" );
$obFormulario->agrupaComponentes( array( $obRadioRegistroPrecoSim, $obRadioRegistroPrecoNao ) );

if ($stAcao == "alterar") {
   $obFormulario->addComponente ( $obLblSolicitacao );
}

$obFormulario->addComponente    ( $obLblExercicio );

if ($stAcao == "alterar") {
    $obFormulario->addComponente( $obLblDataSolicitacao );
}
$obFormulario->addComponente    ( $obISelectEntidadeUsuario );
if ($stAcao == "incluir") {
    $obFormulario->addComponente( $obDtSolicitacao );
}
$obFormulario->addHidden        ( $obHdnDtSolicitacao );
$obFormulario->addComponente    ( $obObjeto );
$obFormulario->addComponente    ( $obLblRequisitante );
$obFormulario->addComponente    ( $obSolicitante );
$obFormulario->addComponente    ( $obAlmoxarifado );
$obFormulario->addComponente    ( $obLocalizacaoEntrega );
$obFormulario->agrupaComponentes( array( $obTxtPrazoEntrega, $obLblDia ));
$obFormulario->addComponente    ( $obTxtObs );
$obFormulario->addComponente    ( $obCmbConvenio );
$obFormulario->agrupaComponentes( array( $obRdbItensOutraSolicitacaoSim, $obRdbItensOutraSolicitacaoNao ) );
$obFormulario->addSpan          ( $obSpnItensOutraSolicitacao );

$obFormulario->addTitulo        ( "Dotação Padrão" );
$obDotacaoPadrao->geraFormulario( $obFormulario );

$obFormulario->addTitulo        ( "Dados do Item" );
$obMontaItemUnidade->geraFormulario( $obFormulario );
$obFormulario->addComponente    ( $obTxtComplemento );
$obFormulario->addComponente    ( $obCentroCustoUsuario );
$obFormulario->addComponente    ( $obLblSaldoEstoque );
$obFormulario->addSpan          ( $obSpnVlrReferencia );
$obMontaQuantidadeValores->geraFormulario($obFormulario );
$obMontaQuantidadeValores->obQuantidade->obEvento->setOnChange( $obMontaQuantidadeValores->obQuantidade->obEvento->getOnChange()."montaParametrosGET( 'calculaValorReservadoDotacao' );" );
$obMontaQuantidadeValores->obValorTotal->obEvento->setOnChange( $obMontaQuantidadeValores->obValorTotal->obEvento->getOnChange()."montaParametrosGET( 'calculaValorReservadoDotacao' );" );
$obMontaQuantidadeValores->obValorUnitario->obEvento->setOnChange( $obMontaQuantidadeValores->obValorUnitario->obEvento->getOnChange()."montaParametrosGET( 'calculaValorReservadoDotacao' );" );

$obFormulario->addSpan          ( $obSpanDotacao );
$obFormulario->agrupaComponentes( array($obBtnIncluirItemSolicitacao, $obBtnLimparItemSolicitacao) );
$obFormulario->addSpan          ( $obSpnListaSolicitacoes );

if ($stAcao == 'incluir' || $stAcao =='alterar') {
    include_once CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php";
    $obMontaAssinaturas = new IMontaAssinaturas;
    $obMontaAssinaturas->geraFormulario( $obFormulario );
}

$obFormulario->addComponente	( $obChkRelatorio );

if ($stAcao == 'alterar') {
    $obFormulario->Cancelar( $pgList."?".Sessao::getId()."&stAcao=".$stAcao."&pos=".$request->get('pos')."&pg=".$request->get('pg')  );
} else {
    $obFormulario->defineBarra( array($obBtnOkSolicitacao, $obBtnLimparSolicitacao) );
}

$obFormulario->show();

if ($stAcao=="alterar") {
    $stJs .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&cod_solicitacao=".$request->get('cod_solicitacao')."&cod_entidade=".$request->get('cod_entidade')."&exercicio=".$request->get('exercicio')."','carregaSolicitacao');";
} else {
    $arValores = Sessao::read('arValores');
    if (count($arValores) > 0) {
        $stJs .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','carregaListaItens');";
    }
}

$jsOnLoad = $stJs;

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
