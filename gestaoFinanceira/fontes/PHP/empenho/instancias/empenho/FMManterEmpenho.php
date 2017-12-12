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
    * Página de Formulario de Inclusao/Alteracao de Empenho
    * Data de Criação   : 05/12/2004

    * @author Analista Jorge B. Ribarr
    * @author Desenvolvedor Anderson R. M. Buzo

    * @ignore

    $Id: FMManterEmpenho.php 66418 2016-08-25 21:02:27Z michel $

    * Casos de uso: uc-02.01.08
                    uc-02.03.03
                    uc-02.03.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoConfiguracao.class.php";
include_once CAM_GPC_TCERN_MAPEAMENTO.'TTCERNFundeb.class.php';
include_once CAM_GPC_TCERN_MAPEAMENTO.'TTCERNRoyalties.class.php';
include_once CAM_FW_HTML."MontaAtributos.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoContrapartidaAutorizacao.class.php";
include_once CAM_GP_LIC_COMPONENTES.'IPopUpContrato.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterEmpenho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'incluir');

Sessao::remove('arBuscaContrato');

$inCodUf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio());

if ($inCodUf == 20) {
    $obTTCERNRoyalties  = new TTCERNRoyalties;
    $obTTCERNFundeb     = new TTCERNFundeb;

    $obTTCERNRoyalties->recuperaTodos($rsRoyalties, '', 'codigo');
    $obTTCERNFundeb->recuperaTodos($rsFundeb, '', 'codigo');
}

$obREmpenhoConfiguracao = new REmpenhoConfiguracao;
$obREmpenhoConfiguracao->consultar();

$boLiquidacaoAutomatica = $obREmpenhoConfiguracao->getLiquidacaoAutomatica();

$rsClassificacao = new RecordSet;
$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->listar( $rsTipo, "cod_tipo <> 0" );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->listar( $rsHistorico );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->listarUnidadeMedida( $rsUnidade );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
$stMascaraRubrica = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();

Sessao::remove('arItens');
Sessao::remove('link');

$inCodEntidade = $request->get('inCodEntidade');
$inCodPreEmpenho = $request->get('inCodPreEmpenho');
$inCodAutorizacao = $request->get('inCodAutorizacao');

$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodAutorizacao( $request->get('inCodAutorizacao') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodPreEmpenho( $request->get('inCodPreEmpenho') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->setCodReserva( $request->get('inCodReserva') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->consultar();

$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento( $stFormaExecucao );

$stNomEmpenho       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->getDescricao();
$stNomEntidade      = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->getNomCGM();
$stNomTipo          = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->getNomTipo();
$inNumUnidade       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade();
$stNomUnidade       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNomUnidade();
$inNumOrgao         = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
$stNomOrgao         = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNomeOrgao();
$inCodDespesa       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa();
$stNomDespesa       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getDescricao();
$stCodClassificacao = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao();
$stNomClassificacao = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->getDescricao();
$inCodFornecedor    = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCGM->getNumCGM();
$stNomFornecedor    = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCGM->getnomCGM();
$inCodHistorico     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->getCodHistorico();
$stNomHistorico     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->getNomHistorico();
$stDtValidadeInicial= $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->getDtValidadeInicial();
$stDtValidadeFinal  = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->getDtValidadeFinal();
$stDtInclusao       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->getDtInclusao();
$nuVlReserva        = number_format($obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->getVlReserva(),2,',','.');
$arItemPreEmpenho   = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->getItemPreEmpenho();
$inCodCategoria     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->getCodCategoria();
$stNomCategoria     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->getNomCategoria();

if ($inCodCategoria == 2 || $inCodCategoria == 3) {
    $obTEmpenhoContrapartidaAutorizacao = new TEmpenhoContrapartidaAutorizacao;
    $obTEmpenhoContrapartidaAutorizacao->setDado( 'cod_autorizacao', $request->get('inCodAutorizacao') );
    $obTEmpenhoContrapartidaAutorizacao->setDado( 'cod_entidade'   , $request->get('inCodEntidade')    );
    $obTEmpenhoContrapartidaAutorizacao->setDado( 'exercicio'      , Sessao::getExercicio()            );
    $obTEmpenhoContrapartidaAutorizacao->recuperaContrapartidaLancamento( $rsContrapartida );

    $inCodContrapartida = $rsContrapartida->getCampo('conta_contrapartida');
    $stNomContrapartida = $rsContrapartida->getCampo('conta_contrapartida').' - '.$rsContrapartida->getCampo('nom_conta');
}

$arItens = array();

foreach ($arItemPreEmpenho as $inCount => $obItemPreEmpenho) {
    $nuVlUnitario = ($obItemPreEmpenho->getValorTotal()/$obItemPreEmpenho->getQuantidade());
    $nuVlUnitario = number_format($nuVlUnitario,4,',','.');

    $inCodMarca = $obItemPreEmpenho->getCodigoMarca();
    $stDescrisaoItemMarca = '';
    if(!empty($inCodMarca)){
        $stDescrisaoItemMarca = SistemaLegado::pegaDado('descricao', 'almoxarifado.marca', " WHERE cod_marca = ".$inCodMarca, $boTransacao);
    }

    $arItens[$inCount]['num_item']     = $obItemPreEmpenho->getNumItem();
    $arItens[$inCount]['nom_item']     = $obItemPreEmpenho->getNomItem();
    $arItens[$inCount]['complemento']  = $obItemPreEmpenho->getComplemento();
    $arItens[$inCount]['quantidade']   = $obItemPreEmpenho->getQuantidade();
    $arItens[$inCount]['cod_unidade']  = $obItemPreEmpenho->obRUnidadeMedida->getCodUnidade();
    $arItens[$inCount]['cod_grandeza'] = $obItemPreEmpenho->obRUnidadeMedida->obRGrandeza->getCodGrandeza();
    $arItens[$inCount]['nom_unidade']  = $obItemPreEmpenho->getNomUnidade();
    $arItens[$inCount]['cod_marca']    = $inCodMarca;
    $arItens[$inCount]['nome_marca']   = $stDescrisaoItemMarca;
    $arItens[$inCount]['vl_total']     = $obItemPreEmpenho->getValorTotal();
    $arItens[$inCount]['vl_unitario']  = $nuVlUnitario;
    if($obItemPreEmpenho->getCodItemPreEmp()!='')
        $arItens[$inCount]['cod_item']     = $obItemPreEmpenho->getCodItemPreEmp();
    Sessao::write('arItens', $arItens);
}
$arChaveAtributo =  array( "cod_pre_empenho" => $request->get('inCodPreEmpenho'),
                           "exercicio"       => Sessao::getExercicio()         );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

if ($inCodDespesa){
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodEntidade($request->get('inCodEntidade'));
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setTipoEmissao('R');
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->consultaSaldoAnteriorDataEmpenho( $nuSaldoAnterior );
}

$nuSaldoAnterior = number_format( $nuSaldoAnterior, 2, ',', '.');

if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}

$obHdnUltimaDataEmpenho = new Hidden;
$obHdnUltimaDataEmpenho->setName ( "dtUltimaDataEmpenho" );
$obHdnUltimaDataEmpenho->setValue( '' );

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

// Define objeto Hidden para Codigo da Autorizacao
$obHdnCodAutorizacao = new Hidden;
$obHdnCodAutorizacao->setName ( "inCodAutorizacao" );
$obHdnCodAutorizacao->setValue( $inCodAutorizacao );

// Define objeto Hidden para Data da Autorizacao
$obHdnDtAutorizacao = new Hidden;
$obHdnDtAutorizacao->setName ( "stDtAutorizacao" );
$obHdnDtAutorizacao->setValue( $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->getDtAutorizacao() );

// Define objeto Hidden para Codigo da Pre Empenho
$obHdnCodPreEmpenho = new Hidden;
$obHdnCodPreEmpenho->setName ( "inCodPreEmpenho" );
$obHdnCodPreEmpenho->setValue( $inCodPreEmpenho );

// Define objeto Hidden para Codigo da Entidade
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setId ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $inCodEntidade );

// Define objeto Hidden para Codigo da Reserva
$obHdnCodReserva = new Hidden;
$obHdnCodReserva->setName  ( "inCodReserva" );
$obHdnCodReserva->setValue ( $request->get('inCodReserva') );

// Define objeto Hidden para Codigo da Classificacao
$obHdnCodClassificacao = new Hidden;
$obHdnCodClassificacao->setName  ( "stCodClassificacao" );
$obHdnCodClassificacao->setValue ( $stCodClassificacao  );

// Define Objeto Label para Fornecedor
$obHdnCodFornecedor = new Hidden;
$obHdnCodFornecedor->setName     ( "inCodFornecedor" );
$obHdnCodFornecedor->setValue    ( $inCodFornecedor  );

// Define objeto Hidden para Codigo da Classificacao
$obHdnCodDespesa = new Hidden;
$obHdnCodDespesa->setName  ( "inCodDespesa" );
$obHdnCodDespesa->setValue ( $inCodDespesa  );

// Define objeto Hidden para Codigo da Classificacao
$obHdnCodHistorico = new Hidden;
$obHdnCodHistorico->setName  ( "inCodHistorico" );
$obHdnCodHistorico->setValue ( $inCodHistorico  );

$obHdnVlReserva = new Hidden;
$obHdnVlReserva->setName  ( "nuVlReserva" );
$obHdnVlReserva->setValue ( $nuVlReserva  );

// Hidden para num_orgao
$obHdnNumOrgao = new Hidden;
$obHdnNumOrgao->setName   ( "inNumOrgao" );
$obHdnNumOrgao->setValue  ( $inNumOrgao  );

// Hidden para num_unidade
$obHdnNumUnidade = new Hidden;
$obHdnNumUnidade->setName   ( "inNumUnidade" );
$obHdnNumUnidade->setValue  ( $inNumUnidade  );

// Define objeto HiddenEval para travar o botão OK
$obHdnTrava = new HiddenEval;
$obHdnTrava->setName ( "hdnValidaData" );
$obHdnTrava->setValue( "document.frm.Ok.disabled = true;" );

// Hidden para categoria do empenho
$obHdnCategoriaEmpenho = new Hidden;
$obHdnCategoriaEmpenho->setName   ( "inCodCategoria" );
$obHdnCategoriaEmpenho->setValue  ( $inCodCategoria  );

// Hidden para contrapartida do empenho
$obHdnContrapartida = new Hidden;
$obHdnContrapartida->setName   ( "inCodContrapartida" );

//Define o objeto para validacao da data do fornecedor
$obHdnValidaFornecedor = new Hidden;
$obHdnValidaFornecedor->setName ( "boMsgValidadeFornecedor" );
$obHdnValidaFornecedor->setId   ( "boMsgValidadeFornecedor" );
$obHdnValidaFornecedor->setValue( 'false' );

//Define o objeto para validacao da data do fornecedor
$obHdnBoAutorizacao = new Hidden;
$obHdnBoAutorizacao->setName ( "obHdnBoAutorizacao" );
$obHdnBoAutorizacao->setId   ( "obHdnBoAutorizacao" );
$obHdnBoAutorizacao->setValue( 'false' );

//Define o nome da ação para controle no oculto para o mostrar o calcúlo do saldo anterior na label
$obHdnEmitirEmpenhoAutorizacao = new Hidden;
$obHdnEmitirEmpenhoAutorizacao->setName ( "hdnNomeAcao" );
$obHdnEmitirEmpenhoAutorizacao->setId   ( "hdnNomeAcao" );
$obHdnEmitirEmpenhoAutorizacao->setValue( "stEmitirEmpenhoAutorizacao" );

//Define o objeto TextArea para Descrição do Empenho
$obTxtNomEmpenho = new TextArea;
$obTxtNomEmpenho->setName   ( "stNomEmpenho"         );
$obTxtNomEmpenho->setId     ( "stNomEmpenho"         );
$obTxtNomEmpenho->setRotulo ( "Descrição do Empenho" );
$obTxtNomEmpenho->setValue  ( $stNomEmpenho          );
$obTxtNomEmpenho->setNull   ( true                   );
$obTxtNomEmpenho->setRows   ( 6                      );
$obTxtNomEmpenho->setCols   ( 100                    );
$obTxtNomEmpenho->setMaxCaracteres(640);

// Define Objeto TextBox para Codigo do Tipo de Empenho
$obTxtCodTipo = new TextBox;
$obTxtCodTipo->setName   ( "inCodTipo"                   );
$obTxtCodTipo->setId     ( "inCodTipo"                   );
$obTxtCodTipo->setValue  ( ''                            );
$obTxtCodTipo->setRotulo ( "Tipo de Empenho"             );
$obTxtCodTipo->setTitle  ( "Selecione o tipo de empenho" );
$obTxtCodTipo->setInteiro( true  );
$obTxtCodTipo->setNull   ( false );

// Define Objeto Select para Nome do tipo de empenho
$obCmbNomTipo = new Select;
$obCmbNomTipo->setName      ( "stNomTipo"     );
$obCmbNomTipo->setId        ( "stNomTipo"     );
$obCmbNomTipo->setValue     ( 1               );
$obCmbNomTipo->setCampoId   ( "cod_tipo"      );
$obCmbNomTipo->setCampoDesc ( "nom_tipo"      );
$obCmbNomTipo->addOption    ( '','Selecione'  );
$obCmbNomTipo->preencheCombo( $rsTipo         );
$obCmbNomTipo->setNull      ( false           );
$obCmbNomTipo->setValue     ( '' );

// Define objeto Data para validade final
$obDtEmpenho = new Data;
$obDtEmpenho->setName     ( "stDtEmpenho" );
$obDtEmpenho->setId       ( "stDtEmpenho" );
$obDtEmpenho->setRotulo   ( "Data de Empenho"                          );
$obDtEmpenho->setTitle    ( 'Informe a data do empenho'                );
$obDtEmpenho->setNull     ( false                                      );
$obDtEmpenho->obEvento->setOnBlur( "validaDataEmpenho('autorizacao');" );
$obDtEmpenho->obEvento->setOnChange( "montaParametrosGET('verificaFornecedor'); buscaDado('montaLabelSaldoAnterior');" );
$obDtEmpenho->setLabel    ( TRUE );
$obDtEmpenho->setValue    ( ''   );

$jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','LiberaDataEmpenho');";

// Define objeto Data para Data de Vencimento
$obDtVencimento = new Data;
$obDtVencimento->setName  ( "stDtVencimento"            );
$obDtVencimento->setId    ( "stDtVencimento"            );
$obDtVencimento->setValue ( '31/12/'.Sessao::getExercicio() );
$obDtVencimento->setRotulo( "Data de Vencimento"        );
$obDtVencimento->setNull  ( false                       );
$obDtVencimento->obEvento->setOnChange( "validaVencimento();" );

// Define objeto Label para saldo anterior
$obLblSaldoAnterior = new Label;
$obLblSaldoAnterior->setId    ( "nuSaldoAnterior" );
$obLblSaldoAnterior->setName  ( "nuSaldoAnterior" );
$obLblSaldoAnterior->setValue ( $nuSaldoAnterior  );
$obLblSaldoAnterior->setRotulo( "Saldo Anterior"  );

// Define objeto Label para Entidade
$obLblEntidade = new Label;
$obLblEntidade->setRotulo( "Entidade" );
$obLblEntidade->setValue ( $inCodEntidade.' - '.$stNomEntidade );

if ($inCodDespesa) {
    // Define Objeto Label para Despesa
    $obLblDespesa = new Label;
    $obLblDespesa->setRotulo ( "Dotação Orcamentária" );
    $obLblDespesa->setId     ( "stNomDespesa"  );
    $obLblDespesa->setValue  ( $inCodDespesa.' - '.$stNomDespesa );
} else {
    // Define Objeto BuscaInner para Despesa
    $obBscDespesa = new BuscaInner;
    $obBscDespesa->setRotulo ( "Dotação Orçamentária"   );
    $obBscDespesa->setTitle  ( ""                       );
    $obBscDespesa->setNulL   ( false                    );
    $obBscDespesa->setId     ( "stNomDespesa"           );
    $obBscDespesa->setValue  ( $stNomDespesa            );
    $obBscDespesa->obCampoCod->setName ( "inCodDespesa" );
    $obBscDespesa->obCampoCod->setSize ( 10 );
    $obBscDespesa->obCampoCod->setMaxLength( 5 );
    $obBscDespesa->obCampoCod->setValue ( $inCodDespesa );
    $obBscDespesa->obCampoCod->setId ( inCodDespesa );
    $obBscDespesa->obCampoCod->setAlign ("left");
    $obBscDespesa->obCampoCod->obEvento->setOnBlur("buscaDado('buscaDespesa');");
    $obBscDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDespesa','stNomDespesa','autorizacaoEmpenho&inNumOrgao='+document.frm.inNumOrgao.value + '&inNumUnidade='+document.frm.inNumUnidade.value + '&inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");
}

if ($stCodClassificacao != null) {
    // Define Objeto Label para Classificacao da Despesa
    $obLblClassificacao = new Label;
    $obLblClassificacao->setRotulo ( "Rubrica de Despesa" );
    $obLblClassificacao->setId     ( "stNomClassificacao" );
    $obLblClassificacao->setValue  ( $stCodClassificacao.' - '.$stNomClassificacao );
} else {
    // Define Objeto Select para Classificacao da Despesa
    $obCmbClassificacao = new Select;
    $obCmbClassificacao->setRotulo               ( "Desdobramento" );
    $obCmbClassificacao->setTitle                ( "Informe a rubrica de despesa" );
    $obCmbClassificacao->setName                 ( "stCodClassificacao" );
    $obCmbClassificacao->setId                   ( "stCodClassificacao" );
    $obCmbClassificacao->setValue                ( $stCodClassificacao );
    $obCmbClassificacao->obEvento->setOnChange   ( "validaDesdobramento();" );
    $obCmbClassificacao->setStyle                ( "width: 600" );
    if ($stFormaExecucao) {
        $obCmbClassificacao->setNull  ( false );
    } else {
        $obCmbClassificacao->setNull  ( true );
        $obCmbClassificacao->setDisabled( true );
    }
    $obCmbClassificacao->addOption               ( "", "Selecione"  );
    $obCmbClassificacao->setCampoId              ( "cod_estrutural" );
    $obCmbClassificacao->setCampoDesc            ( "cod_estrutural" );
    $obCmbClassificacao->preencheCombo           ( $rsClassificacao );
}

// Define Objeto Label para Orgao
$obLblOrgao = new Label;
$obLblOrgao->setRotulo ( "Orgão Orçamentário" );
$obLblOrgao->setId     ( "inCodOrgao" );
$obLblOrgao->setValue  ( $inNumOrgao.' - '.$stNomOrgao  );

// Define Objeto Label para Unidade
$obLblUnidade = new Label;
$obLblUnidade->setRotulo ( "Unidade Orçamentária" );
$obLblUnidade->setId     ( "inCodUnidade" );
$obLblUnidade->setValue  ( $inNumUnidade.' - '.$stNomUnidade  );

// Define Objeto Label para Fornecedor
$obLblFornecedor = new Label;
$obLblFornecedor->setRotulo ( "Fornecedor" );
$obLblFornecedor->setId     ( "stNomFornecedor" );
$obLblFornecedor->setValue  ( $inCodFornecedor.' - '.$stNomFornecedor  );

// Label para categoria do empenho
$obLblCategoriaEmpenho = new Label;
$obLblCategoriaEmpenho->setRotulo ( "Categoria do Empenho" );
$obLblCategoriaEmpenho->setValue  ( $stNomCategoria        );

// Label para categoria do empenho
$obLblContrapartida = new Label;
$obLblContrapartida->setRotulo ( "Contrapartida" );

// Define Objeto Label para Histórico
$obLblHistorico = new Label;
$obLblHistorico->setRotulo    ( "Histórico"      );
$obLblHistorico->setId        ( "stNomHistorico" );
$obLblHistorico->setValue     ( $inCodHistorico.' - '.$stNomHistorico  );

if ($inCodUf == 20) {
    $obCmbFundeb = new Select;
    $obCmbFundeb->setName      ('inCodFundeb');
    $obCmbFundeb->setRotulo    ('Fundeb');
    $obCmbFundeb->setTitle     ('Selecione Fundeb.');
    $obCmbFundeb->setId        ('inCodFundeb');
    $obCmbFundeb->setCampoId   ('cod_fundeb');
    $obCmbFundeb->setCampoDesc ('codigo');
    $obCmbFundeb->preencheCombo($rsFundeb);
    $obCmbFundeb->setNull      (false);

    $obCmbRoyalties = new Select;
    $obCmbRoyalties->setName      ('inCodRoyalties');
    $obCmbRoyalties->setRotulo    ('Royalties');
    $obCmbRoyalties->setTitle     ('Selecione Royalties.');
    $obCmbRoyalties->setId        ('inCodRoyalties');
    $obCmbRoyalties->setCampoId   ('cod_royalties');
    $obCmbRoyalties->setCampoDesc ('codigo');
    $obCmbRoyalties->preencheCombo($rsRoyalties);
    $obCmbRoyalties->setNull      (false);
}

if ($inCodUf == 9 && Sessao::getExercicio() >= 2012) {
    $obTxtProcessoLicitacao = new TextBox;
    $obTxtProcessoLicitacao->setName            ('stProcessoLicitacao');
    $obTxtProcessoLicitacao->setId              ('stProcessoLicitacao');
    $obTxtProcessoLicitacao->setRotulo          ('Número Processo Licitação');
    $obTxtProcessoLicitacao->setTitle           ('Informe o número do Processo de Licitação.');
    $obTxtProcessoLicitacao->setNull            (true);
    $obTxtProcessoLicitacao->setMaxLength       (8);
    $obTxtProcessoLicitacao->setSize            (8);

    $obTxtExercicioLicitacao = new TextBox;
    $obTxtExercicioLicitacao->setName           ('stExercicioLicitacao');
    $obTxtExercicioLicitacao->setId             ('stExercicioLicitacao');
    $obTxtExercicioLicitacao->setRotulo         ('Ano Processo Licitação');
    $obTxtExercicioLicitacao->setTitle          ('Informe o ano do Processo de Licitação.');
    $obTxtExercicioLicitacao->setInteiro        (true);
    $obTxtExercicioLicitacao->setNull           (true);
    $obTxtExercicioLicitacao->setMaxLength      (4);
    $obTxtExercicioLicitacao->setSize           (4);

    $obTxtProcessoAdministrativo = new TextBox;
    $obTxtProcessoAdministrativo->setName            ('stProcessoAdministrativo');
    $obTxtProcessoAdministrativo->setId              ('stProcessoAdministrativo');
    $obTxtProcessoAdministrativo->setRotulo          ('Número Processo Administrativo');
    $obTxtProcessoAdministrativo->setTitle           ('Informe o número do Processo Administrativo.');
    $obTxtProcessoAdministrativo->setNull            (true);
    $obTxtProcessoAdministrativo->setMaxLength       (20);
    $obTxtProcessoAdministrativo->setSize            (20);
}

while (!$rsAtributos->EOF()) {
    $boTransacao = "";
    include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoItemPreEmpenhoJulgamento.class.php';
    $obTEmpenhoItemPreEmpenhoJulgamento = new TEmpenhoItemPreEmpenhoJulgamento;

    $stFiltro  = " WHERE cod_pre_empenho = ".$request->get('inCodPreEmpenho');
    $stFiltro .= "   AND exercicio = '".Sessao::getExercicio()."'";
    $obTEmpenhoItemPreEmpenhoJulgamento->recuperaTodos($rsPreEmpenhoItemJulgamento, $stFiltro, '', $boTransacao);

    if ($rsAtributos->getCampo('nom_atributo') == 'Característica Peculiar' ||  $rsAtributos->getCampo('nom_atributo') == 'Número da Licitação')
        $rsAtributos->setCampo('label', false);
    else
        $rsAtributos->setCampo('label', true);

    $rsAtributos->proximo();
}
$rsAtributos->setPrimeiroElemento();

// Atributos Dinamicos
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

// Define Objeto Span Para lista de itens
$obSpan = new Span;
$obSpan->setId( "spnLista" );

// Define Objeto Label para Valor Total dos Itens
$obLblVlTotal = new Label;
$obLblVlTotal->setId( "nuValorTotal" );
$obLblVlTotal->setRotulo( "TOTAL: " );

if($boLiquidacaoAutomatica=="true")
    $stLiquidacaoAutomatica = "SIM";
else
    $stLiquidacaoAutomatica = "NAO";

// Define Objeto SimNao para emitir liquidacao
$obSimNaoEmitirLiquidacao = new SimNao();
$obSimNaoEmitirLiquidacao->setRotulo ( "Liquidar este empenho após sua emissão" );
$obSimNaoEmitirLiquidacao->setName   ( 'boEmitirLiquidacao'      );
$obSimNaoEmitirLiquidacao->setNull   ( true                      );
$obSimNaoEmitirLiquidacao->setChecked( $stLiquidacaoAutomatica   );

include_once CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php";
$obMontaAssinaturas = new IMontaAssinaturas(null, 'nota_empenho_autorizacao');
$obMontaAssinaturas->definePapeisDisponiveis('nota_empenho_autorizacao');
$obMontaAssinaturas->setOpcaoAssinaturas( false );

if ($inCodUf == 9 && Sessao::getExercicio() >= 2012) {
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
$obFormulario->addTitulo( "Dados do empenho" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCodAutorizacao   );
$obFormulario->addHidden( $obHdnDtAutorizacao    );
$obFormulario->addHidden( $obHdnCodPreEmpenho    );
$obFormulario->addHidden( $obHdnCodEntidade      );
$obFormulario->addHidden( $obHdnCodReserva       );
$obFormulario->addHidden( $obHdnNumOrgao         );
$obFormulario->addHidden( $obHdnNumUnidade       );
$obFormulario->addHidden( $obHdnCodFornecedor    );
$obFormulario->addHidden( $obHdnCategoriaEmpenho );
$obFormulario->addHidden( $obHdnContrapartida    );
$obFormulario->addHidden( $obHdnCodHistorico     );
$obFormulario->addHidden( $obHdnVlReserva        );
$obFormulario->addHidden( $obHdnTrava, true      );
$obFormulario->addHidden( $obHdnUltimaDataEmpenho );
$obFormulario->addHidden( $obHdnValidaFornecedor  );
$obFormulario->addHidden( $obHdnBoAutorizacao     );
$obFormulario->addHidden( $obHdnEmitirEmpenhoAutorizacao );

$obFormulario->addComponente( $obLblEntidade      );
if ($inCodDespesa) {
    $obFormulario->addHidden( $obHdnCodDespesa       );
    $obFormulario->addHidden( $obHdnCodClassificacao );
    $obFormulario->addComponente( $obLblDespesa       );
    $obFormulario->addComponente( $obLblClassificacao );
} else {
    $obFormulario->addComponente( $obBscDespesa       );
    $obFormulario->addComponente( $obCmbClassificacao );
}
$obFormulario->addComponente( $obLblSaldoAnterior );
$obFormulario->addComponente( $obLblOrgao         );
$obFormulario->addComponente( $obLblUnidade       );
$obFormulario->addComponente( $obLblFornecedor    );
$obFormulario->addComponente( $obLblCategoriaEmpenho   );

if ($inCodCategoria == 2 || $inCodCategoria == 3)
    $obFormulario->addComponente( $obLblContrapartida );

$obFormulario->addComponente( $obTxtNomEmpenho    );
$obFormulario->addComponente( $obDtEmpenho        );
$obFormulario->addComponente( $obDtVencimento     );
$obFormulario->addComponenteComposto( $obTxtCodTipo, $obCmbNomTipo );
$obFormulario->addComponente( $obLblHistorico     );

if ($inCodUf == 20) {
    $obFormulario->addComponente($obCmbFundeb);
    $obFormulario->addComponente($obCmbRoyalties);
}

if ($inCodUF == 9 && Sessao::getExercicio() >= 2012) {
    //informações sobre a licitação
    $obFormulario->addComponente($obTxtProcessoLicitacao);
    $obFormulario->addComponente($obTxtExercicioLicitacao);
    $obFormulario->addComponente($obTxtProcessoAdministrativo);

    $obFormulario->addTitulo('Modalidade TCMGO');
    $obFormulario->addComponente($obCmbModalidadeLicitacao);
    $obFormulario->addSpan($obSpanFundamentacaoLegal);
}

$obMontaAtributos->geraFormulario ( $obFormulario );

$obFormulario->addTitulo('Contrato');
$obFormulario->addHidden( $obHdnDtContrato );
$obContrato->geraFormulario($obFormulario);

$obFormulario->addTitulo( "Itens do empenho" );
$obFormulario->addSpan( $obSpan );
$obFormulario->addComponente( $obLblVlTotal             );
$obFormulario->addComponente( $obSimNaoEmitirLiquidacao );

$obMontaAssinaturas->geraFormulario( $obFormulario );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;
$obFormulario->Cancelar( $stLocation );
$obFormulario->show();

$jsOnload .= "montaParametrosGET('montaListaItemPreEmpenho');montaParametrosGET('buscaDtEmpenho');";

if ( $obMontaAssinaturas->getOpcaoAssinaturas() ) {
    echo $obMontaAssinaturas->disparaLista();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
