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
    * Página de Consulta de Autorização
    * Data de Criação   : 06/05/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Id: FMConsultarAutorizacao.php 65373 2016-05-17 12:31:43Z michel $

    * Casos de uso: uc-02.03.02
                    uc-02.01.08

*/

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarAutorizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";
include_once CAM_FW_HTML."MontaAtributos.class.php";
include_once CAM_GF_PPA_MAPEAMENTO."TPPAAcao.class.php";
include_once $pgJS;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'incluir');

$arFiltro = array();
$stFiltro = '';
if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}
$rsCompraLicitacao = new RecordSet();
$obRegra = new REmpenhoAutorizacaoEmpenho;
$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;

$obRegra->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRegra->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );

$obRegra->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
$stMascaraRubrica = $obRegra->obROrcamentoClassificacaoDespesa->recuperaMascara();

Sessao::remove('arItens');

$obRegra->obROrcamentoReserva->setCodReserva( $request->get('inCodReserva') );

if($request->get('inCodEmpenho'))
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenho( $request->get('inCodEmpenho') );

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( $request->get('stExercicio') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->setCodAutorizacao( $request->get('inCodAutorizacao') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodPreEmpenho( $request->get('inCodPreEmpenho') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );

$obRegra->setExercicio( $request->get('stExercicio') );
$obRegra->setCodAutorizacao( $request->get('inCodAutorizacao') );
$obRegra->setCodPreEmpenho( $request->get('inCodPreEmpenho') );
$obRegra->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );

if($request->get('inCodEmpenho'))
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultar();

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setBoEmpenhoCompraLicitacao( true );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodModalidadeCompra     ( $arFiltro['inCodModalidadeCompra'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCompraInicial           ( $arFiltro['inCompraInicial'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCompraFinal             ( $arFiltro['inCompraFinal'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodModalidadeLicitacao  ( $arFiltro['inCodModalidadeLicitacao'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setLicitacaoInicial        ( $arFiltro['inLicitacaoInicial'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setLicitacaoFinal          ( $arFiltro['inLicitacaoFinal'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarConsultaEmpenho($rsCompraLicitacao);

$obRegra->setBoEmpenhoCompraLicitacao( true );
$obRegra->listarConsulta( $rsSaldos );
$obRegra->consultar();

$stDespesa = $obRegra->obROrcamentoDespesa->getCodDespesa();

$obRegra->obROrcamentoDespesa->setCodDespesa( $rsSaldos->getCampo( "cod_despesa" ) );

$stAutorizacao      = $request->get('inCodAutorizacao')." / ".$request->get('stExercicio');
$stDtAutorizacao    = $obRegra->getDtAutorizacao();
$stNomEntidade      = $obRegra->obROrcamentoEntidade->obRCGM->getNomCGM();
$inNumUnidade       = $obRegra->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade();
$stNomUnidade       = $obRegra->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNomUnidade();
$inNumOrgao         = $obRegra->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
$stNomOrgao         = $obRegra->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNomeOrgao();
$stOrgao            = $inNumOrgao.' - '.$stNomOrgao;
$stUnidade          = $inNumUnidade.' - '.$stNomUnidade;
$inCodDespesa       = $obRegra->obROrcamentoDespesa->getCodDespesa();
$stNomDespesa       = $obRegra->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getDescricao();
$stCodClassificacao = $obRegra->obROrcamentoClassificacaoDespesa->getMascClassificacao();
$stNomClassificacao = $obRegra->obROrcamentoClassificacaoDespesa->getDescricao();

$inCodPao           = $obRegra->obROrcamentoDespesa->obROrcamentoProjetoAtividade->getNumeroProjeto();
$stNomPao           = $obRegra->obROrcamentoDespesa->obROrcamentoProjetoAtividade->getNome();
$inCodFornecedor    = $obRegra->obRCGM->getNumCGM();
$stNomFornecedor    = $obRegra->obRCGM->getnomCGM();
$stDtValidade       = $obRegra->obROrcamentoReserva->getDtValidadeFinal();
$inCodHistorico     = $obRegra->obREmpenhoHistorico->getCodHistorico();
$stNomHistorico     = $obRegra->obREmpenhoHistorico->getNomHistorico();
$stDtAnulacao       = $obRegra->getDtAnulacao();
$stMotivoAnulacao   = $obRegra->getMotivoAnulacao();
$stDescricao        = $obRegra->getDescricao();

if ($obRegra->obROrcamentoReserva->getVlReserva()!=null) {
    $nuVlReserva = number_format($obRegra->obROrcamentoReserva->getVlReserva(),2,',','.');
}
$arItemPreEmpenho = $obRegra->getItemPreEmpenho();

$inCodEmpenho = $request->get('inCodEmpenho');

if ($inCodEmpenho) {
    $stEmpenho = $inCodEmpenho." / ".$request->get('stExercicio');
    $stDtEmpenho = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getDtEmpenho();
}
foreach ($arItemPreEmpenho as $inCount => $obItemPreEmpenho) {
    $nuVlUnitario = ($obItemPreEmpenho->getValorTotal()/$obItemPreEmpenho->getQuantidade());
    if ($nuVlUnitario!='') {
        $nuVlUnitario = number_format($nuVlUnitario,4,',','.');
    }

    $arItens = Sessao::read('arItens');
    $arItens[$inCount]['num_item']     = $inCount+1;
    $arItens[$inCount]['nom_item']     = $obItemPreEmpenho->getNomItem();
    $arItens[$inCount]['complemento']  = $obItemPreEmpenho->getComplemento();
    $arItens[$inCount]['quantidade']   = $obItemPreEmpenho->getQuantidade();
    $arItens[$inCount]['vl_unitario']  = $nuVlUnitario;
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

    Sessao::write('arItens', $arItens);
}
$nuVlUnitario = "";
SistemaLegado::executaFramePrincipal("buscaDado('montaListaItemPreEmpenho');");
$arChaveAtributo =  array( "cod_pre_empenho" => $request->get("inCodPreEmpenho"),
                           "exercicio"       => Sessao::getExercicio() );
$obRegra->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
$obRegra->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

// Define objeto Hidden para Codigo da Autorizacao
$obHdnCodAutorizacao = new Hidden;
$obHdnCodAutorizacao->setName ( "inCodAutorizacao" );
$obHdnCodAutorizacao->setValue( $inCodAutorizacao  );

$obLblAutorizacao = new Label;
$obLblAutorizacao->setRotulo( "Autorização"      );
$obLblAutorizacao->setId    ( "inCodAutorizacao" );
$obLblAutorizacao->setValue ( $stAutorizacao     );

$obLblDtAutorizacao = new Label;
$obLblDtAutorizacao->setRotulo( "Data da Autorização" );
$obLblDtAutorizacao->setId    ( "dtDataAutorizacao"   );
$obLblDtAutorizacao->setValue ( $stDtAutorizacao      );

// Define objeto Hidden para Codigo da Entidade
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade"                );
$obHdnCodEntidade->setValue( $request->get('inCodEntidade') );
$obHdnCodEntidade->setId   ( "inCodEntidade"                );

//Define objeto label entidade
$obLblEntidade = new Label;
$obLblEntidade->setRotulo( "Entidade"       );
$obLblEntidade->setId    ( "stNomeEntidade" );
$obLblEntidade->setValue ( $request->get('inCodEntidade').' - '.$stNomEntidade );

$obLblOrgao = new Label;
$obLblOrgao->setRotulo( "Orgão Orçamentário" );
$obLblOrgao->setId    ( "inCodOrgao"         );
$obLblOrgao->setValue ( $stOrgao             );

$obLblUnidade = new Label;
$obLblUnidade->setRotulo( "Unidade Orçamentária" );
$obLblUnidade->setId    ( "inCodUnidade"         );
$obLblUnidade->setValue ( $stUnidade             );

// Define objeto Hidden para Codigo da Classificacao
$obHdnCodClassificacao = new Hidden;
$obHdnCodClassificacao->setName  ( "stCodClassificacao" );
$obHdnCodClassificacao->setValue ( $stCodClassificacao  );

$obLblDotacao = new Label;
$obLblDotacao->setRotulo( "Dotação Orçamentária" );
$obLblDotacao->setId    ( "stNomDespesa"         );
$obLblDotacao->setValue ( $stDespesa             );

$obLblDesdobramento = new Label;
$obLblDesdobramento->setRotulo( "Desdobramento"                               );
$obLblDesdobramento->setId    ( "stNomClassificacao"                          );
$obLblDesdobramento->setValue ( $stCodClassificacao.' - '.$stNomClassificacao );

// Define objeto Label para PAO
$obLblPAO = new Label;
$obLblPAO->setRotulo( 'PAO' );
$obLblPAO->setId    ( 'pao' );
$obLblPAO->setValue ( $inCodPao.' - '.$stNomPao );

$obLblFornecedor = new Label;
$obLblFornecedor->setRotulo( "Fornecedor"      );
$obLblFornecedor->setId    ( "stNomFornecedor" );
$obLblFornecedor->setValue ( $inCodFornecedor.' - '.$stNomFornecedor );

$obLblDescricao = new Label;
$obLblDescricao->setRotulo( "Descrição"     );
$obLblDescricao->setId    ( "stDescricao"   );
$obLblDescricao->setValue ( $stDescricao    );

// Define Objeto Label para Fornecedor
$obHdnCodFornecedor = new Hidden;
$obHdnCodFornecedor->setName ( "inCodFornecedor" );
$obHdnCodFornecedor->setValue( $inCodFornecedor  );

// Define objeto Label para Empenho
$obLblEmpenho = new Label;
$obLblEmpenho->setRotulo( "N° Empenho" );
$obLblEmpenho->setValue ( $stEmpenho);

// Define objeto Label para Data de Empenho
$obLblDataEmpenho = new Label;
$obLblDataEmpenho->setRotulo( "Data de Empenho" );
$obLblDataEmpenho->setValue ( $stDtEmpenho );

// Define objeto Label para Data de Validade
$obLblDataValidade = new Label;
$obLblDataValidade->setRotulo( "Data de Validade" );
$obLblDataValidade->setValue ( $stDtValidade );

// Define objeto Label para Data de Anulação
$obLblDataAnulacao = new Label;
$obLblDataAnulacao->setRotulo( "Data de Anulação" );
$obLblDataAnulacao->setValue ( $stDtAnulacao );

// Define objeto Label para Motivo da Anulação
$obLblMotivoAnulacao = new Label;
$obLblMotivoAnulacao->setRotulo( "Motivo da Anulação" );
$obLblMotivoAnulacao->setValue ( $stMotivoAnulacao );

// Define objeto Hidden para Codigo da Classificacao
$obHdnCodHistorico = new Hidden;
$obHdnCodHistorico->setName  ( "inCodHistorico" );
$obHdnCodHistorico->setValue ( $inCodHistorico  );

$obLblHistorico = new Label;
$obLblHistorico->setRotulo( "Histórico Padrão"   );
$obLblHistorico->setId    ( "stNomHistorico" );
$obLblHistorico->setValue ( $inCodHistorico." - ".$stNomHistorico  );

$obLblSituacao = new Label;
$obLblSituacao->setRotulo( "Situação"   );
$obLblSituacao->setId    ( "stSituacao" );
$obLblSituacao->setValue ( $request->get('stSituacao') );

// Atributos Dinamicos
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );
$obMontaAtributos->setLabel      ( true );

$obLblCodCompraDireta = new Label;
$obLblCodCompraDireta->setRotulo( "Compra Direta"      );
$obLblCodCompraDireta->setId    ( "inCodCompraDireta"  );
$obLblCodCompraDireta->setValue ( $rsSaldos->getCampo('cod_compra_direta') );

$obLblModalidadeCompraDireta = new Label;
$obLblModalidadeCompraDireta->setRotulo( "Modalidade da Compra Direta" );
$obLblModalidadeCompraDireta->setId    ( "inModalidadeCompraDireta"    );
$obLblModalidadeCompraDireta->setValue ( $rsSaldos->getCampo('compra_cod_modalidade').' - '.$rsSaldos->getCampo('compra_modalidade') );

$obLblCodLicitacao = new Label;
$obLblCodLicitacao->setRotulo( "Licitação"            );
$obLblCodLicitacao->setId    ( "inCodLicitacao"       );
$obLblCodLicitacao->setValue ( $rsSaldos->getCampo('cod_licitacao')."/".$rsSaldos->getCampo('exercicio') );

$obLblModalidadeLicitacao = new Label;
$obLblModalidadeLicitacao->setRotulo( "Modalidade da Licitação" );
$obLblModalidadeLicitacao->setId    ( "inModalidadeLicitacao"   );
$obLblModalidadeLicitacao->setValue ( $rsSaldos->getCampo('licitacao_cod_modalidade').' - '.$rsSaldos->getCampo('licitacao_modalidade') );

$obSpnLista = new Span;
$obSpnLista->setId ( "spnLista" );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
$obButtonVoltar = new Button;
$obButtonVoltar->setName  ( "Voltar" );
$obButtonVoltar->setValue ( "Voltar" );
$obButtonVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm                    );
$obFormulario->addTitulo( "Dados da autorização"   );
$obFormulario->addHidden( $obHdnCtrl               );
$obFormulario->addHidden( $obHdnAcao               );
$obFormulario->addHidden( $obHdnCodAutorizacao     );
$obFormulario->addHidden( $obHdnCodEntidade        );
$obFormulario->addHidden( $obHdnCodFornecedor      );
$obFormulario->addHidden( $obHdnCodHistorico       );

$obFormulario->addComponente( $obLblAutorizacao    );
$obFormulario->addComponente( $obLblDtAutorizacao  );
$obFormulario->addComponente( $obLblEntidade       );
$obFormulario->addComponente( $obLblOrgao          );
$obFormulario->addComponente( $obLblUnidade        );
$obFormulario->addComponente( $obLblDotacao        );
$obFormulario->addComponente( $obLblDesdobramento  );
$obFormulario->addComponente( $obLblPAO            );
$obFormulario->addComponente( $obLblFornecedor     );
$obFormulario->addComponente( $obLblDescricao      );
$obFormulario->addComponente( $obLblEmpenho        );
$obFormulario->addComponente( $obLblDataEmpenho    );
$obFormulario->addComponente( $obLblDataValidade   );
$obFormulario->addComponente( $obLblHistorico      );
$obFormulario->addComponente( $obLblSituacao       );
$obFormulario->addComponente( $obLblDataAnulacao   );
$obFormulario->addComponente( $obLblMotivoAnulacao );

$obMontaAtributos->geraFormulario( $obFormulario   );

if ( $rsSaldos->getNumLinhas() > 0 ) {
    if ( $rsSaldos->getCampo("compra_cod_modalidade") != "" ) {
        $obFormulario->addTitulo( "Compra Direta"  );
        $obFormulario->addComponente( $obLblCodCompraDireta        );
        $obFormulario->addComponente( $obLblModalidadeCompraDireta );
    }

    if ( $rsSaldos->getCampo("licitacao_cod_modalidade") != "" ) {
        $obFormulario->addTitulo( "Licitação"  );
        $obFormulario->addComponente( $obLblCodLicitacao        );
        $obFormulario->addComponente( $obLblModalidadeLicitacao );
    }
}
$obFormulario->addSpan($obSpnLista);

include_once CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php";
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setCodDocumento( $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao() );
$obMontaAssinaturas->setCodEntidade( $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
$obMontaAssinaturas->setExercicio( $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getExercicio() );
$obMontaAssinaturas->geraListaLeituraFormulario( $obFormulario, 'autorizacao_empenho' );

$obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );

$obFormulario->show();
?>
