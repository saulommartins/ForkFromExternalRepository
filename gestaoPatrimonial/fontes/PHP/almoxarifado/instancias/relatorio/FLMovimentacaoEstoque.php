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
    * Página de Filtro para Relatório de Itens
    * Data de Criação   : 24/01/2006

    * @author Gelson W. Gonçalves

    * @ignore

    * $Id: FLMovimentacaoEstoque.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.03.24
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoAlmoxarife.class.php";
require_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php";
require_once CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoPermissaoCentroDeCustos.class.php";
require_once CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCatalogoClassificacao.class.php";
require_once CAM_GP_ALM_COMPONENTES. "IMontaCatalogoClassificacao.class.php";
require_once CAM_GP_ALM_COMPONENTES. "IIntervaloPopUpItem.class.php";
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );

$stPrograma = 'MovimentacaoEstoque';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';
$pgGera = 'OCGeraMovimentacaoEstoque.php';

Sessao::remove('filtro');

$obForm = new Form;
$obForm->setAction ( $pgGera );
$obForm->setTarget ( 'telaPrincipal'     );

//Definição dos componentes
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnStCtrl = new Hidden;
$obHdnStCtrl->setName ( "stCtrl" );
$obHdnStCtrl->setValue( $stCtrl );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GP_ALM_INSTANCIAS."relatorio/OCItensEstoque.php" );

$obRAlmoxarifadoCatalogoClassificacao = new RAlmoxarifadoCatalogoClassificacao;
$obRAlmoxarifadoPermissaoCentroCustos = new RAlmoxarifadoPermissaoCentroDeCustos;
$obRAlmoxarifadoAlmoxarife = new RAlmoxarifadoAlmoxarife;

$rsDisponiveis  = new Recordset;
$rsRelacionados = new Recordset;
$rsDisponiveisAlmox = new Recordset;
$rsPermitidosAlmox  = new RecordSet;

$obRAlmoxarifadoPermissaoCentroCustos->obRCGMPessoaFisica->setNumCGM( Sessao::read('numCgm') );
$obRAlmoxarifadoPermissaoCentroCustos->listarRelacionados( $rsRelacionados );

$obRAlmoxarifadoAlmoxarife->listarDisponiveis( $rsDisponiveisAlmox , "codigo" );
$obRAlmoxarifadoAlmoxarife->listarPadrao ( $rsPermitidosAlmox      , "codigo" );

$stNomeAlmoxarifados = Sessao::read('stNomeAlmoxarifados');
while ( !$rsDisponiveisAlmox->eof() ) {
    $stNomeAlmoxarifados[$rsDisponiveisAlmox->getCampo('codigo')] = $rsDisponiveisAlmox->getCampo( 'nom_a');
    foreach ($rsPermitidosAlmox->arElementos as $key => $valor) {
        if ( $valor['nom_a'] == $rsDisponiveisAlmox->getCampo('nom_a') ) {
            unset($rsDisponiveisAlmox->arElementos[$rsDisponiveisAlmox->getCorrente()-1]);
        }
    }
    $rsDisponiveisAlmox->proximo();
}

$arDiff = array();
if (is_array($rsDisponiveisAlmox->arElementos)&&is_array($rsPermitidosAlmox->arElementos)) {
  $arDiff = array_diff_assoc($rsDisponiveisAlmox->arElementos, $rsPermitidosAlmox->arElementos );
}

$arTmp = array();
foreach ($arDiff as $Valor) {
    array_push($arTmp,$Valor);
}
$rsDisponiveisAlmox = new RecordSet;
$rsDisponiveisAlmox->preenche($arTmp);

/* Define SELECT multiplo para Almoxarifado */
$obCmbAlmoxarifado = new SelectMultiplo();
$obCmbAlmoxarifado->setName       ( 'inCodAlmoxarifado' );
$obCmbAlmoxarifado->setRotulo     ( "Almoxarifados"     );
$obCmbAlmoxarifado->setTitle      ( "Selecione os almoxarifados."     );

/* Lista de atributos disponiveis */
$obCmbAlmoxarifado->setNomeLista1 ( 'inCodAlmoxarifadoDisponivel' );
$obCmbAlmoxarifado->setCampoId1   ( 'codigo'            );
$obCmbAlmoxarifado->setCampoDesc1 ( '[codigo]-[nom_a]'  );
$obCmbAlmoxarifado->setRecord1    ( $rsDisponiveisAlmox );

/* lista de atributos selecionados */
$obCmbAlmoxarifado->setNomeLista2 ( 'inCodAlmoxarifadoSelecionado' );
$obCmbAlmoxarifado->setCampoId2   ( 'codigo'                       );
$obCmbAlmoxarifado->setCampoDesc2 ( '[codigo]-[nom_a]'             );
$obCmbAlmoxarifado->setRecord2    ( $rsPermitidosAlmox             );

$obIMontaCatalogoClassificacao = new IMontaCatalogoClassificacao();
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setUltimoNivelRequerido(false);
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setClassificacaoRequerida(false);
$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setNaoPermiteManutencao( true );

$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setNull(true);

/* Define SELECT multiplo para Centro de Custo */
$obCmbCentroCusto = new SelectMultiplo();
$obCmbCentroCusto->setName       ( 'inCodCentroCusto'           );
$obCmbCentroCusto->setRotulo     ( "Centro de Custo"            );
$obCmbCentroCusto->setTitle      ( "Selecione os centros de custo.");

/* Lista de atributos disponiveis */
$obCmbCentroCusto->SetNomeLista1 ('inCodCentroCustoDisponivel'  );
$obCmbCentroCusto->setCampoId1   ( 'cod_centro'                 );
$obCmbCentroCusto->setCampoDesc1 ( 'descricao'                  );
$obCmbCentroCusto->SetRecord1    ( $rsRelacionados              );

/* lista de atributos selecionados */
$rsTeste = new RecordSet();
$rsTeste->preenche( array());
$obCmbCentroCusto->SetNomeLista2 ( 'inCodCentroCustoRelacionado');
$obCmbCentroCusto->setCampoId2   ( 'cod_centro'                 );
$obCmbCentroCusto->setCampoDesc2 ( 'descricao'                  );
$obCmbCentroCusto->SetRecord2    ( $rsTeste			            );

// campo fornecedor
$obIPopUpFornecedor = new IPopUpCGMVinculado( $obForm                 );
$obIPopUpFornecedor->setTabelaVinculo       ( 'compras.nota_fiscal_fornecedor'    );
$obIPopUpFornecedor->setCampoVinculo        ( 'cgm_fornecedor'        );
$obIPopUpFornecedor->setNomeVinculo         ( 'Fornecedor'            );
$obIPopUpFornecedor->setRotulo              ( 'Fornecedor'            );
$obIPopUpFornecedor->setTitle               ( 'Informe o fornecedor.' );
$obIPopUpFornecedor->setName                ( 'stNomCGM'              );
$obIPopUpFornecedor->setId                  ( 'stNomCGM'              );
$obIPopUpFornecedor->obCampoCod->setName    ( 'inCGM'                 );
$obIPopUpFornecedor->obCampoCod->setId      ( 'inCGM'                 );
$obIPopUpFornecedor->obCampoCod->setNull    ( true                    );
$obIPopUpFornecedor->setNull                ( true                    );

//nota fiscal
$obNF = new Inteiro;
$obNF->setRotulo( 'Número da Nota Fiscal'   );
$obNF->setName  ( 'inNF'                    );
$obNF->setSize  ( 10   );
$obNF->setMaxLength( 9 );
$obNF->setTitle ( 'Informe o Número da Nota Fiscal' );
$obNF->setNegativo (false);
$obNF->setValue ( $inNotaFiscal   );

$obSerieNF = new TextBox;
$obSerieNF->setRotulo( 'Número de Série'   );
$obSerieNF->setName  ( 'stSerieNF'  );
$obSerieNF->setSize  ( 10   );
$obSerieNF->setMaxLength( 9 );
$obSerieNF->setTitle ( 'Informe a Série da Nota Fiscal' );
$obSerieNF->setValue ( $stSerieNF  );

/* MONTA ITEM*/
$obItem = new IIntervaloPopUpItem( $obForm );
$obItem->setItemComposto( true );
$obItem->setTipoNaoInformado(true);

$obTxtItem = new TextBox;
$obTxtItem->setRotulo( 'Descrição do Item'     );
$obTxtItem->setName  ( 'stDescItem'  );
$obTxtItem->setSize  ( 50              );
$obTxtItem->setMaxLength( 160           );
$obTxtItem->setTitle ( 'Selecione a descrição do item' );
$obTxtItem->setValue ( $inCodItem );

$obCmbItem = new TipoBusca( $obTxtItem );

$obRAlmoxarifadoCatalogoItem = new RAlmoxarifadoCatalogoItem;
$obRAlmoxarifadoCatalogoItem->obRAlmoxarifadoTipoItem->listar( $rsTipo ) ;
$arRdTipo = array();
$inCodTipo = 0;

$obRdTipo = new Radio;
$obRdTipo->setRotulo                      ( "Tipo"                                      );
$obRdTipo->setTitle                       ( "Selecione o tipo de item desejado."         );
$obRdTipo->setName                        ( "inCodTipo"                                 );
$obRdTipo->setLabel                       ( "Todos"                                     );
$obRdTipo->setValue                       ( "0"                                         );
$obRdTipo->setChecked                     ( true                                        );
$obRdTipo->setNull                        ( false                                       );
$arRdTipo[] = $obRdTipo;

for ($i = 0; $i < $rsTipo->getNumLinhas(); $i++) {
   if ($rsTipo->getCampo('cod_tipo') != 0) {
     $obRdTipo = new Radio;
     $obRdTipo->setRotulo                      ( "Tipo"                                      );
     $obRdTipo->setName                        ( "inCodTipo"                                 );
     $obRdTipo->setLabel                       ( $rsTipo->getCampo('descricao')              );
     $obRdTipo->setValue                       ( $rsTipo->getCampo('cod_tipo')               );
     $obRdTipo->setChecked                     ( $inCodTipo == $rsTipo->getCampo('cod_tipo') );
     $obRdTipo->setNull                        ( false                                       );
     $arRdTipo[] = $obRdTipo;
     $rsTipo->proximo();
   }
}

/* TIPO DE MOVIMENTAÇÃO
$arRdTipoMovimentacao = array();

// radio todos
$obRdTipoMovimentacao = new Radio;
$obRdTipoMovimentacao->setRotulo                      ( "Tipo de Movimentação"                      );
$obRdTipoMovimentacao->setTitle                       ( "Selecione o tipo de movimentação desejado.");
$obRdTipoMovimentacao->setName                        ( "inCodTipoMovimentacao"                     );
$obRdTipoMovimentacao->setLabel                       ( "Entradas e Saídas"                                     );
$obRdTipoMovimentacao->setValue                       ( "todos"                                     );
$obRdTipoMovimentacao->setChecked                     ( true                                        );
$obRdTipoMovimentacao->setNull                        ( false                                       );
$arRdTipoMovimentacao[] = $obRdTipoMovimentacao;

// radio entrada
$obRdTipoMovimentacao = new Radio;
$obRdTipoMovimentacao->setRotulo                      ( "Tipo de Movimentação"                      );
$obRdTipoMovimentacao->setTitle                       ( "Selecione o tipo de movimentação desejado.");
$obRdTipoMovimentacao->setName                        ( "inCodTipoMovimentacao"                     );
$obRdTipoMovimentacao->setLabel                       ( "Entrada"                                   );
$obRdTipoMovimentacao->setValue                       ( "entrada"                                   );
$obRdTipoMovimentacao->setChecked                     ( false                                       );
$obRdTipoMovimentacao->setNull                        ( false                                       );
$arRdTipoMovimentacao[] = $obRdTipoMovimentacao;

// radio saida
$obRdTipoMovimentacao = new Radio;
$obRdTipoMovimentacao->setRotulo                      ( "Tipo de Movimentação"                      );
$obRdTipoMovimentacao->setTitle                       ( "Selecione o tipo de movimentação desejado.");
$obRdTipoMovimentacao->setName                        ( "inCodTipoMovimentacao"                     );
$obRdTipoMovimentacao->setLabel                       ( "Saída"                                     );
$obRdTipoMovimentacao->setValue                       ( "saida"                                     );
$obRdTipoMovimentacao->setChecked                     ( false                                       );
$obRdTipoMovimentacao->setNull                        ( false                                       );
$arRdTipoMovimentacao[] = $obRdTipoMovimentacao;

// radio sem movimentacao
$obRdTipoMovimentacao = new Radio;
$obRdTipoMovimentacao->setRotulo                      ( "Tipo de Movimentação"                      );
$obRdTipoMovimentacao->setTitle                       ( "Selecione o tipo de movimentação desejado.");
$obRdTipoMovimentacao->setName                        ( "inCodTipoMovimentacao"                     );
$obRdTipoMovimentacao->setLabel                       ( "Sem Movimentação"                                     );
$obRdTipoMovimentacao->setValue                       ( "semmov"                                     );
$obRdTipoMovimentacao->setChecked                     ( false                                       );
$obRdTipoMovimentacao->setNull                        ( false                                       );
$arRdTipoMovimentacao[] = $obRdTipoMovimentacao;
*/
//////
// unidade de medida
//////

//sigla
$obRdUnidadeAbrev = new Radio();
$obRdUnidadeAbrev->setRotulo    ( "Unidade de Medida" );
$obRdUnidadeAbrev->setTitle     ( "Selecione o tipo de descrição." );
$obRdUnidadeAbrev->setName      ( "inUnidadeAbrev" );
$obRdUnidadeAbrev->setLabel     ( "Sigla" );
$obRdUnidadeAbrev->setValue     ( true );
$obRdUnidadeAbrev->setChecked   ( true );
$arRdUnidadeAbrev[] = $obRdUnidadeAbrev;

//descricao
$obRdUnidadeAbrev = new Radio();
$obRdUnidadeAbrev->setRotulo    ( "Unidade de Medida" );
$obRdUnidadeAbrev->setTitle     ( "Selecione o tipo de descrição." );
$obRdUnidadeAbrev->setName      ( "inUnidadeAbrev" );
$obRdUnidadeAbrev->setLabel     ( "Descrição" );
$obRdUnidadeAbrev->setValue     ( false );
$obRdUnidadeAbrev->setChecked   ( false );
$arRdUnidadeAbrev[] = $obRdUnidadeAbrev;

//////
// unidade de medida
//////

//sigla
$obRdNaturezaAbrev = new Radio();
$obRdNaturezaAbrev->setRotulo    ( "Demonstrar a Natureza por" );
$obRdNaturezaAbrev->setTitle     ( "Selecione o tipo de descrição." );
$obRdNaturezaAbrev->setName      ( "inNaturezaAbrev" );
$obRdNaturezaAbrev->setLabel     ( "Sigla" );
$obRdNaturezaAbrev->setValue     ( true );
$obRdNaturezaAbrev->setChecked   ( true );
$arRdNaturezaAbrev[] = $obRdNaturezaAbrev;

//descricao
$obRdNaturezaAbrev = new Radio();
$obRdNaturezaAbrev->setRotulo    ( "Demonstrar a Natureza por" );
$obRdNaturezaAbrev->setTitle     ( "Selecione o tipo de descrição." );
$obRdNaturezaAbrev->setName      ( "inNaturezaAbrev" );
$obRdNaturezaAbrev->setLabel     ( "Descrição" );
$obRdNaturezaAbrev->setValue     ( false );
$obRdNaturezaAbrev->setChecked   ( false );
$arRdNaturezaAbrev[] = $obRdNaturezaAbrev;

/* Natureza de Entrada */
require_once ( CAM_GP_ALM_MAPEAMENTO . "TAlmoxarifadoNatureza.class.php");
$obTAlmoxarifadoNatureza = new TAlmoxarifadoNatureza;
$stFiltro = "\n WHERE tipo_natureza='E'    \n";
//Entrada por Empréstimo, filtro existente enquanto a rotina não for implementada.
$stFiltro.= "   AND cod_natureza not in (4)  ";
$obTAlmoxarifadoNatureza->recuperaTodos ( $rsNatureza );

/* Define SELECT multiplo para Natureza */
$obCmbNatureza = new SelectMultiplo();
$obCmbNatureza->setName       ( 'inCodNatureza'           );
$obCmbNatureza->setRotulo     ( "Natureza"            );
$obCmbNatureza->setTitle      ( "Selecione as Naturezas.");

/* Lista de atributos disponiveis */
$obCmbNatureza->SetNomeLista1 ('inCodNaturezaDisponivel'  );
$obCmbNatureza->setCampoId1   ( '[cod_natureza]-[tipo_natureza]'                 );
$obCmbNatureza->setCampoDesc1 ( 'descricao'                  );
$obCmbNatureza->SetRecord1    ( $rsNatureza              );

/* lista de atributos selecionados */
$rsSelecionados = new RecordSet();
$rsSelecionados->preenche( array());
$obCmbNatureza->SetNomeLista2 ( 'inCodNaturezaRelacionados');
$obCmbNatureza->setCampoId2   ( 'cod_Natureza'                 );
$obCmbNatureza->setCampoDesc2 ( 'descricao'                  );
$obCmbNatureza->SetRecord2    ( $rsSelecionados              );

/*
$obCmbNatureza = new Select;
$obCmbNatureza->setTitle     ( "Selecione a Natureza de Entrada" );
$obCmbNatureza->setName      ( "inCodNatureza"                   );
$obCmbNatureza->setId        ( "inCodNatureza" 				     );
$obCmbNatureza->setRotulo    ( "Natureza de Entrada"		     );
$obCmbNatureza->addOption    ( "", "Selecione" 				     );
$obCmbNatureza->setCampoId   ( "cod_natureza" 					 );
$obCmbNatureza->setCampoDesc ( "descricao" 					     );
$obCmbNatureza->preencheCombo( $rsNatureza 					     );
$obCmbNatureza->setNull      ( true  );
*/

//Define o objeto de periodicidade para o formulário
$obDtPeriodicidade = new Periodicidade();
$obDtPeriodicidade->setExercicio( Sessao::getExercicio() );
$obDtPeriodicidade->setNull     ( false );
//$obDtPeriodicidade->obDia->obEvento->setOnChange( $obDtPeriodicidade->obDia->obEvento->getOnChange()."; montaParametrosGET( 'preencheDataSaldo','stDataFinal,stTipoRelatorio' )" );
//$obDtPeriodicidade->obMes->obEvento->setOnChange( "preencheMes(1); montaParametrosGET( 'preencheDataSaldo','stDataFinal,stTipoRelatorio' )" );
//$obDtPeriodicidade->obAnoMes->obEvento->setOnChange( "preencheMes(2); montaParametrosGET( 'preencheDataSaldo','stDataFinal,stTipoRelatorio' )" );
//$obDtPeriodicidade->obAno->obEvento->setOnChange( $obDtPeriodicidade->obAno->obEvento->getOnChange()."; montaParametrosGET( 'preencheDataSaldo','stDataFinal,stTipoRelatorio' )" );
//$obDtPeriodicidade->obPeriodicidade->obEvento->setOnChange( $obDtPeriodicidade->obPeriodicidade->obEvento->getOnChange()."; montaParametrosGET( 'preencheDataSaldo','stDataFinal,stTipoRelatorio' )" );
//$obDtPeriodicidade->obPeriodoFinal->obEvento->setOnChange( $obDtPeriodicidade->obPeriodoFinal->obEvento->getOnChange()."; montaParametrosGET( 'preencheDataSaldo','stDataFinal,stTipoRelatorio' )" );

$obRdOrdemClassificacao = new Radio;
$obRdOrdemClassificacao->setRotulo                      ( "Ordenar por"                    );
$obRdOrdemClassificacao->setTitle                       ( "Selecione a ordenação desejada." );
$obRdOrdemClassificacao->setName                        ( "stOrdem"                         );
$obRdOrdemClassificacao->setLabel                       ( 'Classificação'                   );
$obRdOrdemClassificacao->setValue                       ( 'classificacao'                   );
$obRdOrdemClassificacao->setChecked                     ( true                              );

$obRdOrdemItem = new Radio;
$obRdOrdemItem->setRotulo                      ( "Ordenar por"                   );
$obRdOrdemItem->setTitle                       ( "Selecione a ordenação desejada." );
$obRdOrdemItem->setName                        ( "stOrdem"                        );
$obRdOrdemItem->setLabel                       ( 'Item'                           );
$obRdOrdemItem->setValue                       ( 'item'                           );
$obRdOrdemItem->setChecked                     ( false                            );

$obRdOrdemDescricao = new Radio;
$obRdOrdemDescricao->setRotulo                      ( "Ordenar por"                   );
$obRdOrdemDescricao->setTitle                       ( "Selecione a ordenação desejada." );
$obRdOrdemDescricao->setName                        ( "stOrdem"                        );
$obRdOrdemDescricao->setLabel                       ( 'Descrição do Item'              );
$obRdOrdemDescricao->setValue                       ( 'descricao'                      );
$obRdOrdemDescricao->setChecked                     ( false                            );

$obRdTipoRelatorioAnalitico = new Radio;
$obRdTipoRelatorioAnalitico->setRotulo                      ( "Tipo do Relatório"              );
$obRdTipoRelatorioAnalitico->setTitle                       ( "Selecione o Tipo de Relatório." );
$obRdTipoRelatorioAnalitico->setName                        ( "stTipoRelatorio"                );
$obRdTipoRelatorioAnalitico->setId 							( $obRdTipoRelatorioAnalitico->getName() );
$obRdTipoRelatorioAnalitico->setLabel                       ( 'Analítico'                      );
$obRdTipoRelatorioAnalitico->setValue                       ( 'A'		                       );
$obRdTipoRelatorioAnalitico->setChecked                     ( true                             );
$obRdTipoRelatorioAnalitico->obEvento->setOnChange 			( "montaParametrosGET( 'montaQuebra', 'stTipoRelatorio' );" );

$obRdTipoRelatorioSintetico = new Radio;
$obRdTipoRelatorioSintetico->setRotulo                      ( "Tipo do Relatório"              );
$obRdTipoRelatorioSintetico->setTitle                       ( "Selecione o Tipo de Relatório." );
$obRdTipoRelatorioSintetico->setName                        ( "stTipoRelatorio"                );
$obRdTipoRelatorioSintetico->setId 							( $obRdTipoRelatorioSintetico->getName() );
$obRdTipoRelatorioSintetico->setLabel                       ( 'Sintético'                      );
$obRdTipoRelatorioSintetico->setValue                       ( 'S'		                       );
$obRdTipoRelatorioSintetico->setChecked                     ( false                            );
$obRdTipoRelatorioSintetico->obEvento->setOnChange			( "montaParametrosGET( 'montaQuebra', 'stTipoRelatorio' );" );

$obSpnQuebra = new Span();
$obSpnQuebra->setId( 'spnQuebra' );

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
//$obFormulario->setAjuda("UC-03.03.24");
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnStCtrl );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addTitulo( "Dados para o filtro" );
$obFormulario->addComponente( $obCmbAlmoxarifado );
$obIMontaCatalogoClassificacao->geraFormulario($obFormulario);
$obFormulario->addComponente( $obCmbCentroCusto );
$obFormulario->addComponente($obIPopUpFornecedor);
$obFormulario->addComponente($obNF);
$obFormulario->addComponente($obSerieNF);
$obFormulario->addComponente($obItem);
$obFormulario->addComponente( $obCmbItem );
$obFormulario->agrupaComponentes( $arRdTipo );
//$obFormulario->agrupaComponentes( $arRdTipoMovimentacao );
$obFormulario->agrupaComponentes( $arRdUnidadeAbrev );
$obFormulario->agrupaComponentes( $arRdNaturezaAbrev );
$obFormulario->addComponente( $obCmbNatureza );
$obFormulario->addComponente( $obDtPeriodicidade );
$obFormulario->agrupaComponentes( array( $obRdOrdemClassificacao, $obRdOrdemItem, $obRdOrdemDescricao ) );
$obFormulario->agrupaComponentes( array( $obRdTipoRelatorioAnalitico, $obRdTipoRelatorioSintetico ) );
$obFormulario->addSpan( $obSpnQuebra );
//$obFormulario->agrupaComponentes( array( $obChkGrupoAlmoxarifado,$obChkGrupoCentroCusto,$obChkGrupoMarca ) );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
include_once( $pgJs );
