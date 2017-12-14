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
    * Data de Criação   : 03/10/2007

    * Casos de uso : uc-03.03.27
*/

/*
$Log$
Revision 1.1  2007/10/17 13:24:19  bruce
Ticket#10291#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoAlmoxarife.class.php");
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php");
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoPermissaoCentroDeCustos.class.php" );
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCatalogoClassificacao.class.php" );
include_once(CAM_GP_ALM_COMPONENTES. "IMontaCatalogoClassificacao.class.php" );
include_once(CAM_GP_ALM_COMPONENTES. "ISelectAlmoxarifado.class.php"         );

$stPrograma = 'ManterRelatorioEtiquetas';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';
$pgGera = 'OCGeraRelatorioEtiquetas.php';

$sessao = $_SESSION['sessao'];

//include_once( $pgJS );

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

  Sessao::write('stNomeAlmoxarifados' , $stNomeAlmoxarifados);

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
$obSelectAlmoxarifado = new ISelectAlmoxarifado;
$obSelectAlmoxarifado->setNull ( false );

$obIMontaCatalogoClassificacao = new IMontaCatalogoClassificacao();
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setUltimoNivelRequerido(false);
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setClassificacaoRequerida(false);
$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setNaoPermiteManutencao( true );

$obTxtItem = new TextBox;
$obTxtItem->setRotulo( 'Descrição do Item'     );
$obTxtItem->setName  ( 'inDescItem'  );
$obTxtItem->setSize  ( 50              );
$obTxtItem->setMaxLength( 160           );
$obTxtItem->setTitle ( 'Selecione a descrição do item' );
$obTxtItem->setValue ( $inCodItem );

$obCmbItem = new TipoBusca( $obTxtItem );

$obRdOrdemClassificacao = new Radio;
$obRdOrdemClassificacao->setRotulo                      ( "Ordenado por"                    );
$obRdOrdemClassificacao->setTitle                       ( "Selecione a ordenação desejada." );
$obRdOrdemClassificacao->setName                        ( "stOrdem"                         );
$obRdOrdemClassificacao->setLabel                       ( 'Classificação'                   );
$obRdOrdemClassificacao->setValue                       ( 'classificacao'                   );
$obRdOrdemClassificacao->setChecked                     ( true                              );

$obRdOrdemItem = new Radio;
$obRdOrdemItem->setRotulo                      ( "Ordenado por"                    );
$obRdOrdemItem->setTitle                       ( "Selecione a ordenação desejada." );
$obRdOrdemItem->setName                        ( "stOrdem"                        );
$obRdOrdemItem->setLabel                       ( 'Item'                           );
$obRdOrdemItem->setValue                       ( 'item'                           );
$obRdOrdemItem->setChecked                     ( false                            );

$obRdOrdemDescricao = new Radio;
$obRdOrdemDescricao->setRotulo    ( "Ordenado por"                    );
$obRdOrdemDescricao->setTitle     ( "Selecione a ordenação desejada." );
$obRdOrdemDescricao->setName      ( "stOrdem"                         );
$obRdOrdemDescricao->setLabel     ( 'Descrição do Item'               );
$obRdOrdemDescricao->setValue     ( 'descricao'                       );
$obRdOrdemDescricao->setChecked   ( false                             );

$obSNComSlado = new SimNao;
$obSNComSlado->setRotulo ( 'Com Saldo'                                  );
$obSNComSlado->setName   ( 'boComSaldo'                                 );
$obSNComSlado->setTitle  ( 'Selecione se o item deve ter saldo ou não.' );

/* este código foi comentado porque o modelo de etiquetas 3x6 não será feito agora
$obCmbTipoEtiqueta = new Select;
$obCmbTipoEtiqueta->setRotulo    ('Selecione o formato'             );
$obCmbTipoEtiqueta->setTitle     ("Selecione o formato da etiqueta.");
$obCmbTipoEtiqueta->setName      ('inCodformatoEtiqueta'            );
$obCmbTipoEtiqueta->setNull      (false                             );
$obCmbTipoEtiqueta->addOption    ("16", "Modelo 16(2x8)"            );
$obCmbTipoEtiqueta->addOption    ("18", "Modelo 18(3x6)"            );
*/

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->setAjuda         ( "UC-03.03.27"             );
$obFormulario->addHidden        ( $obHdnAcao                );
$obFormulario->addHidden        ( $obHdnStCtrl              );
$obFormulario->addHidden        ( $obHdnCaminho             );
$obFormulario->addTitulo        ( "Dados para o filtro"     );
$obFormulario->addComponente    ( $obSelectAlmoxarifado     );
$obIMontaCatalogoClassificacao->geraFormulario($obFormulario);
$obFormulario->addComponente    ( $obCmbItem                                                            );
$obFormulario->agrupaComponentes( array( $obRdOrdemClassificacao, $obRdOrdemItem, $obRdOrdemDescricao ) );
$obFormulario->addComponente    ( $obSNComSlado                                                         );
/*
$obFormulario->addTitulo        ( 'Etiqueta'                                                            );
$obFormulario->addComponente    ( $obCmbTipoEtiqueta                                                    );
*/
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
