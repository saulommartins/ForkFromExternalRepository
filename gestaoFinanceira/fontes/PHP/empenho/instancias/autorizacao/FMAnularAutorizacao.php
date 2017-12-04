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
    * Página de Formulario de Anulacao de Empenho
    * Data de Criação   : 05/12/2004

    * @author Analista Jorge B. Ribarr
    * @author Desenvolvedor Anderson R. M. Buzo

    * @ignore

    $Id: FMAnularAutorizacao.php 65373 2016-05-17 12:31:43Z michel $

    * Casos de uso: uc-02.03.02
                    uc-02.01.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";
include_once CAM_FW_HTML."MontaAtributos.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OCAnularAutorizacao.php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'incluir');

if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}

$stAcao = 'anular';

$inCodPreEmpenho  = $request->get('inCodPreEmpenho');
$inCodEntidade    = $request->get('inCodEntidade');
$inCodReserva     = $request->get('inCodReserva');
$inCodAutorizacao = $request->get('inCodAutorizacao');

if($request->get('stExercicio'))
    $stExercicio = $request->get('stExercicio');
else
    $stExercicio = Sessao::getExercicio();

$obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
$obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio( $stExercicio );
$obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

$obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->listar( $rsTipo );

$obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->setExercicio( $stExercicio );
$obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->listar( $rsHistorico );
$obREmpenhoAutorizacaoEmpenho->listarUnidadeMedida( $rsUnidade );

$obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( $stExercicio );
$stMascaraRubrica = $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();

Sessao::remove('arItens');

$obREmpenhoAutorizacaoEmpenho->setExercicio( $stExercicio );
$obREmpenhoAutorizacaoEmpenho->setCodAutorizacao( $request->get('inCodAutorizacao') );
$obREmpenhoAutorizacaoEmpenho->setCodPreEmpenho( $request->get('inCodPreEmpenho') );
$obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );
$obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setCodReserva( $request->get('inCodReserva') );
$obREmpenhoAutorizacaoEmpenho->consultar();

if ( $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa() )
    $obREmpenhoAutorizacaoEmpenho->consultaSaldoAnterior( $nuVlSaldoDotacao );

$nuVlSaldoDotacao = number_format($nuVlSaldoDotacao,2,',','.');

$stNomEmpenho       = $obREmpenhoAutorizacaoEmpenho->getDescricao();
$stNomEntidade      = $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->getNomCGM();
$inCodTipo          = $obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->getCodTipo();
$stNomTipo          = $obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->getNomTipo();
$inCodDespesa       = $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->getCodDespesa();
$stNomDespesa       = $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getDescricao();
$stCodClassificacao = $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao();
$stNomClassificacao = $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->getDescricao();
$inCodUnidade       = $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade();
$stNomUnidade       = $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNomUnidade();
$inCodOrgao         = $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
$stNomOrgao         = $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNomeOrgao();
$inCodFornecedor    = $obREmpenhoAutorizacaoEmpenho->obRCGM->getNumCGM();
$stNomFornecedor    = $obREmpenhoAutorizacaoEmpenho->obRCGM->getnomCGM();
$stDescricao        = $obREmpenhoAutorizacaoEmpenho->getDescricao();
$dtVencimento       = $obREmpenhoAutorizacaoEmpenho->getDtAutorizacao();
$inCodHistorico     = $obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->getCodHistorico();
$stNomHistorico     = $obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->getNomHistorico();
$stDtValidadeInicial= $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->getDtValidadeInicial();
$stDtValidadeFinal  = $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->getDtValidadeFinal();
$stDtInclusao       = $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->getDtInclusao();
if($obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->getVlReserva()!='')
    $nuVlReserva = number_format($obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->getVlReserva(),2,',','.');
$arItemPreEmpenho = $obREmpenhoAutorizacaoEmpenho->getItemPreEmpenho();
foreach ($arItemPreEmpenho as $inCount => $obItemPreEmpenho) {
    $nuVlUnitario = ($obItemPreEmpenho->getValorTotal()/$obItemPreEmpenho->getQuantidade());
    $nuVlUnitario = number_format($nuVlUnitario,4,',','.');

    $arItens[$inCount]['num_item']     = $obItemPreEmpenho->getNumItem();
    $arItens[$inCount]['nom_item']     = $obItemPreEmpenho->getNomItem();
    $arItens[$inCount]['complemento']  = $obItemPreEmpenho->getComplemento();
    $arItens[$inCount]['vl_unitario']  = $nuVlUnitario;
    $arItens[$inCount]['quantidade']   = $obItemPreEmpenho->getQuantidade();
    $arItens[$inCount]['cod_unidade']  = $obItemPreEmpenho->obRUnidadeMedida->getCodUnidade();
    $arItens[$inCount]['cod_grandeza'] = $obItemPreEmpenho->obRUnidadeMedida->obRGrandeza->getCodGrandeza();
    $arItens[$inCount]['nom_unidade']  = $obItemPreEmpenho->getNomUnidade();
    $arItens[$inCount]['vl_total']     = $obItemPreEmpenho->getValorTotal();
    if($obItemPreEmpenho->getCodItemPreEmp()!='')
        $arItens[$inCount]['cod_item']     = $obItemPreEmpenho->getCodItemPreEmp();
    if($obItemPreEmpenho->getCodigoMarca()!=''){
        $stDescricaoItemMarca = SistemaLegado::pegaDado('descricao', 'almoxarifado.marca', " WHERE cod_marca = ".$obItemPreEmpenho->getCodigoMarca());
        $arItens[$inCount]['cod_marca']    = $obItemPreEmpenho->getCodigoMarca();
        $arItens[$inCount]['nome_marca']   = $stDescricaoItemMarca;
    }
}
Sessao::write('arItens', $arItens);
$jsOnload = "buscaDado('montaListaItemPreEmpenhoAnular');";
$arChaveAtributo =  array( "cod_pre_empenho" => $request->get("inCodPreEmpenho"),
                           "exercicio"       => $stExercicio );
$obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
$obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

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
$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ( "stExercicio" );
$obHdnExercicio->setValue( $stExercicio );

// Define objeto Hidden para Codigo da Autorizacao
$obHdnCodAutorizacao = new Hidden;
$obHdnCodAutorizacao->setName ( "inCodAutorizacao" );
$obHdnCodAutorizacao->setValue( $inCodAutorizacao );

// Define objeto Hidden para Codigo da Pre Empenho
$obHdnCodPreEmpenho = new Hidden;
$obHdnCodPreEmpenho->setName ( "inCodPreEmpenho" );
$obHdnCodPreEmpenho->setValue( $inCodPreEmpenho );

// Define objeto Hidden para Codigo da Entidade
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $inCodEntidade );

// Define objeto Hidden para Codigo da Reserva
$obHdnCodReserva = new Hidden;
$obHdnCodReserva->setName  ( "inCodReserva" );
$obHdnCodReserva->setValue ( $inCodReserva  );

// Define objeto Hidden para Codigo da Despesa
$obHdnCodDespesa = new Hidden;
$obHdnCodDespesa->setName  ( "inCodDespesa" );
$obHdnCodDespesa->setValue ( $inCodDespesa  );

// Define objeto Label para Entidade
$obLblEntidade = new Label;
$obLblEntidade->setRotulo( "Entidade" );
$obLblEntidade->setValue ( $inCodEntidade.' - '.$stNomEntidade );

// Define Objeto Label para Despesa
$obLblDespesa = new Label;
$obLblDespesa->setRotulo ( "Dotação Orçamentária" );
$obLblDespesa->setId     ( "stNomDespesa"  );
$obLblDespesa->setValue  ( $inCodDespesa.' - '.$stNomDespesa );

// Define Objeto Label para Classificacao da Despesa
$obLblClassificacao = new Label;
$obLblClassificacao->setRotulo ( "Desdobramento" );
$obLblClassificacao->setId     ( "stNomClassificacao" );
$obLblClassificacao->setValue  ( $stCodClassificacao.' - '.$stNomClassificacao );

// Define Objeto Label para Saldo da Dotacao
$obLblSaldoDotacao = new Label;
$obLblSaldoDotacao->setRotulo ( "Saldo da Dotação" );
$obLblSaldoDotacao->setId     ( "nuVlSaldoDotacao" );
$obLblSaldoDotacao->setValue  ( $nuVlSaldoDotacao  );

// Define Objeto Label para Orgao Orcamentario
$obLblOrgaoOrcamento = new Label;
$obLblOrgaoOrcamento->setRotulo ( "Orgão Orçamentário" );
$obLblOrgaoOrcamento->setId     ( "inCodOrgao" );
$obLblOrgaoOrcamento->setValue  ( $inCodOrgao.' - '.$stNomOrgao );

// Define Objeto Label para Unidade Orcamentaria
$obLblUnidadeOrcamento = new Label;
$obLblUnidadeOrcamento->setRotulo ( "Unidade Orçamentária" );
$obLblUnidadeOrcamento->setId     ( "inCodUnidade" );
$obLblUnidadeOrcamento->setValue  ( $inCodUnidade.' - '.$stNomUnidade );

// Define Objeto Label para Fornecedor
$obLblFornecedor = new Label;
$obLblFornecedor->setRotulo ( "Fornecedor" );
$obLblFornecedor->setId     ( "stNomFornecedor" );
$obLblFornecedor->setValue  ( $inCodFornecedor.' - '.$stNomFornecedor  );

// Define Objeto Label para Descricao
$obLblDescricao = new Label;
$obLblDescricao->setRotulo ( "Descrição" );
$obLblDescricao->setId     ( "stDescricao" );
$obLblDescricao->setValue  ( $stDescricao  );

// Define Objeto Label para Descricao
$obLblDtVencimento = new Label;
$obLblDtVencimento->setRotulo ( "Data de Vencimento" );
$obLblDtVencimento->setId     ( "stDataVencimento" );
$obLblDtVencimento->setValue  ( $dtVencimento  );

// Define Objeto Label para Histórico
$obLblHistorico = new Label;
$obLblHistorico->setRotulo ( "Histórico"      );
$obLblHistorico->setId     ( "stNomHistorico" );
$obLblHistorico->setValue  ( $inCodHistorico.' - '.$stNomHistorico  );

// Atributos Dinamicos
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );
$obMontaAtributos->setLabel      ( true );

// Define Objeto Span Para lista de itens
$obSpan = new Span;
$obSpan->setId( "spnLista" );

// Define Objeto Label para Valor Total dos Itens
$obLblVlTotal = new Label;
$obLblVlTotal->setId     ( "nuValorTotal" );
$obLblVlTotal->setRotulo ( "TOTAL: "      );

// Define Objeto Label para valor da reserva
$obLblVlReserva = new Label;
$obLblVlReserva->setId     ( "nuVlReserva" );
$obLblVlReserva->setValue  ( $nuVlReserva  );
$obLblVlReserva->setRotulo ( "Valor da Reserva" );

// Define objeto Label para validade final
$obLblValidadeFinal = new Label;
$obLblValidadeFinal->setId     ( "stDtValidadeFinal" );
$obLblValidadeFinal->setValue  ( $stDtValidadeFinal );
$obLblValidadeFinal->setRotulo ( "Data Validade Final" );

// Define Objeto TextArea para Motivo da Anulação
$obTxtMotivo = new TextArea;
$obTxtMotivo->setId    ( "stMotivo" );
$obTxtMotivo->setName  ( "stMotivo" );
$obTxtMotivo->setRotulo( "Motivo"   );
$obTxtMotivo->setNull  ( false      );
$obTxtMotivo->setCols  ( 100        );
$obTxtMotivo->setCols  ( 3          );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados do empenho" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnExercicio );
$obFormulario->addHidden( $obHdnCodAutorizacao );
$obFormulario->addHidden( $obHdnCodPreEmpenho  );
$obFormulario->addHidden( $obHdnCodEntidade    );
$obFormulario->addHidden( $obHdnCodReserva     );
$obFormulario->addHidden( $obHdnCodDespesa     );

$obFormulario->addComponente( $obLblEntidade         );
$obFormulario->addComponente( $obLblDespesa          );
$obFormulario->addComponente( $obLblClassificacao    );
$obFormulario->addComponente( $obLblSaldoDotacao     );
$obFormulario->addComponente( $obLblOrgaoOrcamento   );
$obFormulario->addComponente( $obLblUnidadeOrcamento );
$obFormulario->addComponente( $obLblFornecedor       );
$obFormulario->addComponente( $obLblDescricao        );
$obFormulario->addComponente( $obLblDtVencimento     );
$obFormulario->addComponente( $obLblHistorico        );

$obMontaAtributos->geraFormulario ( $obFormulario );

$obFormulario->addTitulo( "Itens do empenho" );
$obFormulario->addSpan( $obSpan );
$obFormulario->addComponente( $obLblVlTotal         );
$obFormulario->addComponente( $obLblVlReserva       );
$obFormulario->addComponente( $obLblValidadeFinal   );
$obFormulario->addComponente( $obTxtMotivo          );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
$obFormulario->Cancelar( $stLocation );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
