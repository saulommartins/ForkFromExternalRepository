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
    * Página de Filtro para Relatório de Ïtens Perecíveis
    * Data de Criação   : 22/08/2007

    * @author Henrique Boaventura

    * @ignore

    * $Id: FLMovimentacaoEstoquePerecivel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.03.25
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoAlmoxarife.class.php";
require_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php";
require_once CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoPermissaoCentroDeCustos.class.php";
require_once CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCatalogoClassificacao.class.php";
require_once CAM_GP_ALM_COMPONENTES. "IMontaCatalogoClassificacao.class.php";
require_once CAM_GP_ALM_COMPONENTES. "IIntervaloPopUpItem.class.php";
$stPrograma = 'MovimentacaoEstoquePerecivel';

$pgOcul = 'OC'.$stPrograma.'.php';
$pgGera = 'OCGera'.$stPrograma.'.php';

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
$obCmbCentroCusto->setTitle      ( "Selecione os centros de custo."                           );

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

//periodicidade de entrada do item
$obDtPeriodicidadeEntrada = new Periodicidade();
$obDtPeriodicidadeEntrada->setExercicio( Sessao::getExercicio() );
$obDtPeriodicidadeEntrada->setRotulo( 'Periodicidade entrada' );
$obDtPeriodicidadeEntrada->setNull     ( true );

//periodicidade de fabricação
$obDtPeriodicidadeFabricacao = new Periodicidade();
$obDtPeriodicidadeFabricacao->setExercicio( Sessao::getExercicio() );
$obDtPeriodicidadeFabricacao->setRotulo   ( 'Periodicidade fabricação' );
$obDtPeriodicidadeFabricacao->setIdComponente( 'Fabricacao' );
$obDtPeriodicidadeFabricacao->setNull     ( true );

//periodicidade de validade
$obDtPeriodicidadeValidade = new Periodicidade();
$obDtPeriodicidadeValidade->setExercicio( Sessao::getExercicio() );
$obDtPeriodicidadeValidade->setRotulo   ( 'Periodicidade validade' );
$obDtPeriodicidadeValidade->setIdComponente( 'Validade' );
$obDtPeriodicidadeValidade->setNull     ( true );

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

$obChkGrupoAlmoxarifado = new Checkbox();
$obChkGrupoAlmoxarifado->setRotulo( "Quebrar por" );
$obChkGrupoAlmoxarifado->setTitle ( "Selecione uma quebra de relatório."  );
$obChkGrupoAlmoxarifado->setName  ( "stGrupoAlmoxarifado" );
$obChkGrupoAlmoxarifado->setLabel ( "Almoxarifado" );
$obChkGrupoAlmoxarifado->setValue ( "almoxarifado" );

$obChkGrupoMarca = new Checkbox();
$obChkGrupoMarca->setRotulo( "Quebrar por" );
$obChkGrupoMarca->setTitle ( "Selecione uma quebra de relatório."  );
$obChkGrupoMarca->setName  ( "stGrupoMarca" );
$obChkGrupoMarca->setLabel ( "Marca" );
$obChkGrupoMarca->setValue ( "marca" );

$obChkGrupoCentroCusto = new Checkbox();
$obChkGrupoCentroCusto->setRotulo( "Quebrar por" );
$obChkGrupoCentroCusto->setTitle ( "Selecione uma quebra de relatório."  );
$obChkGrupoCentroCusto->setName  ( "stGrupoCentroCusto" );
$obChkGrupoCentroCusto->setLabel ( "Centro de Custo" );
$obChkGrupoCentroCusto->setValue ( "centrocusto" );

$obDtSaldo = new Data();
$obDtSaldo->setRotulo( 'Situação até' );
$obDtSaldo->setTitle ( 'Selecione a data para o saldo.' );
$obDtSaldo->setName  ( 'stDataSaldo' );
$obDtSaldo->setId    ( $obDtSaldo->getName() );
$obDtSaldo->setValue ( date('d/m/Y') );
$obDtSaldo->setNull  ( false );

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
$obFormulario->addComponente( $obItem );
$obFormulario->addComponente( $obCmbItem );
$obFormulario->addComponente( $obDtPeriodicidadeEntrada );
$obFormulario->addComponente( $obDtPeriodicidadeFabricacao );
$obFormulario->addComponente( $obDtPeriodicidadeValidade );
$obFormulario->agrupaComponentes( array( $obRdOrdemClassificacao, $obRdOrdemItem, $obRdOrdemDescricao ) );
$obFormulario->agrupaComponentes( array( $obChkGrupoAlmoxarifado,$obChkGrupoCentroCusto,$obChkGrupoMarca ) );
$obFormulario->addComponente( $obDtSaldo );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
