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
    * Página de Filtro para Relatório de Ïtens
    * Data de Criação   : 24/01/2006

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: FLItensEstoque.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.03.20
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoAlmoxarife.class.php";
require_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php";
require_once CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoPermissaoCentroDeCustos.class.php";
require_once CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCatalogoClassificacao.class.php";
require_once CAM_GP_ALM_COMPONENTES. "IMontaCatalogoClassificacao.class.php";
require_once CAM_GP_ALM_COMPONENTES. "IIntervaloPopUpItem.class.php";
$stPrograma = 'ItensEstoque';

$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';
$pgGera = 'OCGeraRelatorio'.$stPrograma.'php';

$sessao = $_SESSION['sessao'];

include_once( $pgJS );

Sessao::remove('filtro');

$obForm = new Form;
$obForm->setAction ( "OCComponente.php" ); //chama o birt
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
$obRAlmoxarifadoPermissaoCentroCustos->listarDisponiveis ( $rsDisponiveis );
$obRAlmoxarifadoPermissaoCentroCustos->listarRelacionados( $rsRelacionados );

$obRAlmoxarifadoAlmoxarife->listarDisponiveis( $rsDisponiveisAlmox , "codigo" );
$obRAlmoxarifadoAlmoxarife->listarPadrao ( $rsPermitidosAlmox      , "codigo" );

while ( !$rsDisponiveisAlmox->eof() ) {
    $stNomeAlmoxarifados[$rsDisponiveisAlmox->getCampo('codigo')] = $rsDisponiveisAlmox->getCampo( 'nom_a');
    foreach ($rsPermitidosAlmox->arElementos as $key => $valor) {
        if ( $valor['nom_a'] == $rsDisponiveisAlmox->getCampo('nom_a') ) {
            unset($rsDisponiveisAlmox->arElementos[$rsDisponiveisAlmox->getCorrente()-1]);
        }
    }
    $rsDisponiveisAlmox->proximo();
}

  Sessao::write('stNomeAlmoxarifados', $stNomeAlmoxarifados);

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
$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setNull ( false );

/* Define SELECT multiplo para Centro de Custo */
$obCmbCentroCusto = new SelectMultiplo();
$obCmbCentroCusto->setName       ( 'inCodCentroCusto'           );
$obCmbCentroCusto->setRotulo     ( "Centro de Custo"            );
$obCmbCentroCusto->setTitle      ( "Selecione os centros de custo."                           );

/* Lista de atributos disponiveis */
$obCmbCentroCusto->SetNomeLista1 ('inCodCentroCustoDisponivel'  );
$obCmbCentroCusto->setCampoId1   ( 'cod_centro'                 );
$obCmbCentroCusto->setCampoDesc1 ( 'descricao'                  );
$obCmbCentroCusto->SetRecord1    ( $rsDisponiveis               );

/* lista de atributos selecionados */
$obCmbCentroCusto->SetNomeLista2 ( 'inCodCentroCustoRelacionado');
$obCmbCentroCusto->setCampoId2   ( 'cod_centro'                 );
$obCmbCentroCusto->setCampoDesc2 ( 'descricao'                  );
$obCmbCentroCusto->SetRecord2    ( $rsRelacionados              );

/* MONTA ITEM*/
$obItem = new IIntervaloPopUpItem( $obForm );
$obItem->setItemComposto( true );
$obItem->setTipoNaoInformado( true );

$obTxtItem = new TextBox;
$obTxtItem->setRotulo( 'Descrição do Item'     );
$obTxtItem->setName  ( 'inDescItem'  );
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
$obRdTipo->setRotulo   ( "Tipo"                                      );
$obRdTipo->setTitle    ( "Selecione o tipo de item desejado."         );
$obRdTipo->setName     ( "inCodTipo"                                 );
$obRdTipo->setLabel    ( "Todos"                                     );
$obRdTipo->setValue    ( "0"                                         );
$obRdTipo->setChecked  ( true                                        );
$obRdTipo->setNull     ( false                                       );
$arRdTipo[] = $obRdTipo;

for ($i = 0; $i < $rsTipo->getNumLinhas(); $i++) {
   if ($rsTipo->getCampo('cod_tipo') != 0) {
     $obRdTipo = new Radio;
     $obRdTipo->setRotulo    ( "Tipo"                                      );
     $obRdTipo->setName      ( "inCodTipo"                                 );
     $obRdTipo->setLabel     ( $rsTipo->getCampo('descricao')              );
     $obRdTipo->setValue     ( $rsTipo->getCampo('cod_tipo')               );
     $obRdTipo->setChecked   ( $inCodTipo == $rsTipo->getCampo('cod_tipo') );
     $obRdTipo->setNull      ( false                                       );
     $arRdTipo[] = $obRdTipo;
     $rsTipo->proximo();
   }
}

/* Natureza de Entrada */
require_once ( CAM_GP_ALM_MAPEAMENTO . "TAlmoxarifadoNatureza.class.php");
$obTAlmoxarifadoNatureza = new TAlmoxarifadoNatureza;
$stFiltro = "\n WHERE tipo_natureza='E'    \n";
//Entrada por Empréstimo, filtro existente enquanto a rotina não for implementada.
$stFiltro.= "   AND cod_natureza not in (4)  ";
$obTAlmoxarifadoNatureza->recuperaTodos ( $rsNatureza, $stFiltro );

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

$obDtSituacao = new Data;
$obDtSituacao->setRotulo ("Situação até");
$obDtSituacao->setTitle  ("Situação do saldo até a data informada");
$obDtSituacao->setName   ("stDataSituacao");
$obDtSituacao->setValue  (date('d/m/Y'));
$obDtSituacao->setNull   (false);

$obRdOrdemClassificacao = new Radio;
$obRdOrdemClassificacao->setRotulo  ( "Ordenado por"                    );
$obRdOrdemClassificacao->setTitle   ( "Selecione a ordenação desejada." );
$obRdOrdemClassificacao->setName    ( "stOrdem"                         );
$obRdOrdemClassificacao->setLabel   ( 'Classificação'                   );
$obRdOrdemClassificacao->setValue   ( 'classificacao'                   );
$obRdOrdemClassificacao->setChecked ( true                              );

$obRdOrdemItem = new Radio;
$obRdOrdemItem->setRotulo  ( "Ordenado por"                   );
$obRdOrdemItem->setTitle   ( "Selecione a ordenação desejada." );
$obRdOrdemItem->setName    ( "stOrdem"                        );
$obRdOrdemItem->setLabel   ( 'Item'                           );
$obRdOrdemItem->setValue   ( 'item'                           );
$obRdOrdemItem->setChecked ( false                            );

$obRdOrdemDescricao = new Radio;
$obRdOrdemDescricao->setRotulo  ( "Ordenado por"                   );
$obRdOrdemDescricao->setTitle   ( "Selecione a ordenação desejada." );
$obRdOrdemDescricao->setName    ( "stOrdem"                        );
$obRdOrdemDescricao->setLabel   ( 'Descrição do Item'              );
$obRdOrdemDescricao->setValue   ( 'descricao'                      );
$obRdOrdemDescricao->setChecked ( false                            );

$obItensSaldo = new Radio;
$obItensSaldo->setRotulo  ( "Itens com Saldo" );
$obItensSaldo->setTitle   ( "Deseja que o relatório demonstre apenas itens com saldo." );
$obItensSaldo->setName    ( "stItensSaldo" );
$obItensSaldo->setLabel   ( 'Sim' );
$obItensSaldo->setValue   ( 'sim' );
$obItensSaldo->setChecked ( true );

$obItensSemSaldo = new Radio;
$obItensSemSaldo->setRotulo  ( "Itens com Saldo" );
$obItensSemSaldo->setTitle   ( "Deseja que o relatório demonstre apenas itens com saldo." );
$obItensSemSaldo->setName    ( "stItensSaldo" );
$obItensSemSaldo->setLabel   ( 'Não' );
$obItensSemSaldo->setValue   ( 'nao' );
$obItensSemSaldo->setChecked ( false  );

$obItensSaldoTodos = new Radio;
$obItensSaldoTodos->setRotulo  ( "Itens com Saldo" );
$obItensSaldoTodos->setTitle   ( "Deseja que o relatório demonstre apenas itens com saldo." );
$obItensSaldoTodos->setName    ( "stItensSaldo" );
$obItensSaldoTodos->setLabel   ( 'Todos' );
$obItensSaldoTodos->setValue   ( 'todos' );
$obItensSaldoTodos->setChecked ( false   );

$obTipoQuebraCentroCusto = new Radio;
$obTipoQuebraCentroCusto->setRotulo  ( "Tipo de Quebra" );
$obTipoQuebraCentroCusto->setTitle   ( "Selecione o Tipo de Quebra do Relatório: Por Centro de Custo ou Por Item" );
$obTipoQuebraCentroCusto->setName    ( "stTipoQuebra" );
$obTipoQuebraCentroCusto->setLabel   ( 'Centro de Custo' );
$obTipoQuebraCentroCusto->setValue   ( 'centro_custo' );
$obTipoQuebraCentroCusto->setChecked ( true );

$obTipoQuebralItem = new Radio;
$obTipoQuebralItem->setRotulo  ( "Itens com Saldo" );
$obTipoQuebralItem->setTitle   ( "Deseja que o relatório demonstre apenas itens com saldo." );
$obTipoQuebralItem->setName    ( "stTipoQuebra" );
$obTipoQuebralItem->setLabel   ( 'Item' );
$obTipoQuebralItem->setValue   ( 'item' );
$obTipoQuebralItem->setChecked ( false  );

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
//$obFormulario->setAjuda("UC-03.03.20");
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnStCtrl );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addTitulo( "Dados para o filtro" );
$obFormulario->addComponente( $obCmbAlmoxarifado );
$obIMontaCatalogoClassificacao->geraFormulario($obFormulario);
$obFormulario->addComponente( $obCmbCentroCusto );
$obFormulario->addComponente( $obItem );
$obFormulario->addComponente( $obCmbItem );
$obFormulario->agrupaComponentes( $arRdTipo );
$obFormulario->addComponente( $obCmbNatureza );
$obFormulario->addComponente( $obDtSituacao );
$obFormulario->agrupaComponentes( array($obRdOrdemClassificacao, $obRdOrdemItem, $obRdOrdemDescricao ) );
$obFormulario->agrupaComponentes( array($obItensSaldoTodos, $obItensSaldo, $obItensSemSaldo) );
$obFormulario->agrupaComponentes( array($obTipoQuebraCentroCusto, $obTipoQuebralItem) );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
