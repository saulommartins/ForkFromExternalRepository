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

    * @author Henrique Boaventura

    * @ignore

    * $Id: FLItensCatalogo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.03.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoAlmoxarife.class.php");
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php");
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoPermissaoCentroDeCustos.class.php" );
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCatalogoClassificacao.class.php" );
include_once(CAM_GP_ALM_COMPONENTES. "IMontaCatalogoClassificacao.class.php" );

$stPrograma = 'ItensCatalogo';

$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';

Sessao::remove('filtro');

$obForm = new Form;
$obForm->setAction ( "OCRelatorio".$stPrograma.".php" );
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

$obRAlmoxarifadoAlmoxarife->listarDisponiveis( $rsDisponiveisAlmox );
$obRAlmoxarifadoAlmoxarife->listarPadrao ( $rsPermitidosAlmox );

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

$obIMontaCatalogoClassificacao = new IMontaCatalogoClassificacao();
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setUltimoNivelRequerido(false);
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setClassificacaoRequerida(false);
$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setNaoPermiteManutencao( true );

$obTxtCodigoItem = new Inteiro();
$obTxtCodigoItem->setRotulo( 'Item' );
$obTxtCodigoItem->setName( 'inCodItem' );
$obTxtCodigoItem->setNull( true );

$obTxtItem = new TextBox;
$obTxtItem->setRotulo( 'Descrição do Item'     );
$obTxtItem->setName  ( 'inDescItem'  );
$obTxtItem->setSize  ( 50              );
$obTxtItem->setMaxLength( 160           );
$obTxtItem->setTitle ( 'Selecione a descrição do item' );
$obTxtItem->setValue ( $inCodItem );

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

$obCmbItem = new TipoBusca( $obTxtItem );

$obRdOrdemClassificacao = new Radio;
$obRdOrdemClassificacao->setRotulo                      ( "Ordenado por"                   );
$obRdOrdemClassificacao->setTitle                       ( "Selecione a ordenação desejada." );
$obRdOrdemClassificacao->setName                        ( "stOrdem"                        );
$obRdOrdemClassificacao->setLabel                       ( 'Classificação'                  );
$obRdOrdemClassificacao->setValue                       ( 'classificacao'                  );
$obRdOrdemClassificacao->setChecked                     ( true                             );
$obRdOrdemClassificacao->setNull                        ( false                            );

$obRdOrdemItem = new Radio;
$obRdOrdemItem->setRotulo                      ( "Ordenado por"                   );
$obRdOrdemItem->setTitle                       ( "Selecione a ordenação desejada." );
$obRdOrdemItem->setName                        ( "stOrdem"                        );
$obRdOrdemItem->setLabel                       ( 'Item'                           );
$obRdOrdemItem->setValue                       ( 'item'                           );
$obRdOrdemItem->setChecked                     ( false                            );

$obRdOrdemDescricao = new Radio;
$obRdOrdemDescricao->setRotulo                      ( "Ordenado por"                   );
$obRdOrdemDescricao->setTitle                       ( "Selecione a ordenação desejada." );
$obRdOrdemDescricao->setName                        ( "stOrdem"                        );
$obRdOrdemDescricao->setLabel                       ( 'Descrição do Item'              );
$obRdOrdemDescricao->setValue                       ( 'descricao'                      );
$obRdOrdemDescricao->setChecked                     ( false                            );

/* Filtro de movimentação */
$obRdMovimentacaTodos = new Radio;
$obRdMovimentacaTodos->setRotulo ( "Listar por Movientação"             );
$obRdMovimentacaTodos->setTitle  ( "Selecione a movimentação desejada." );
$obRdMovimentacaTodos->setName   ( "stMovimentacao"                     );
$obRdMovimentacaTodos->setLabel  ( 'Todos'                              );
$obRdMovimentacaTodos->setValue  ( 'todos'                              );
$obRdMovimentacaTodos->setChecked( true                                 );
$obRdMovimentacaTodos->setNull   ( false                                );

$obRdComMovimentacao = new Radio;
$obRdComMovimentacao->setRotulo ( "Listar por Movientação"             );
$obRdComMovimentacao->setTitle  ( "Selecione a movimentação desejada." );
$obRdComMovimentacao->setName   ( "stMovimentacao"                     );
$obRdComMovimentacao->setLabel  ( 'Com Movimentação'                   );
$obRdComMovimentacao->setValue  ( 'comMovimentacao'                    );
$obRdComMovimentacao->setChecked( false                                );

$obRdSemMovimentacao = new Radio;
$obRdSemMovimentacao->setRotulo ( "Listar por Movientação"             );
$obRdSemMovimentacao->setTitle  ( "Selecione a movimentação desejada." );
$obRdSemMovimentacao->setName   ( "stMovimentacao"                     );
$obRdSemMovimentacao->setLabel  ( 'Sem Movimentação'                   );
$obRdSemMovimentacao->setValue  ( 'semMovimentacao'                    );
$obRdSemMovimentacao->setChecked( false                                );

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
//$obFormulario->setAjuda("UC-03.03.20");
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnStCtrl );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addTitulo( "Dados para o filtro" );
$obIMontaCatalogoClassificacao->geraFormulario($obFormulario);
$obFormulario->addComponente( $obTxtCodigoItem );
$obFormulario->addComponente( $obCmbItem );
$obFormulario->agrupaComponentes    ( $arRdTipo );
$obFormulario->agrupaComponentes( array( $obRdOrdemClassificacao, $obRdOrdemItem, $obRdOrdemDescricao ) );
$obFormulario->agrupaComponentes( array( $obRdMovimentacaTodos, $obRdComMovimentacao, $obRdSemMovimentacao ) );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
