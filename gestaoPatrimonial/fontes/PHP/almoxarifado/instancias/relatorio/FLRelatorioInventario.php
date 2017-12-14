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

    * $Id: $

    * Casos de uso : uc-03.03.24
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GP_ALM_MAPEAMENTO . "TAlmoxarifadoInventario.class.php";
include_once CAM_GF_ORC_COMPONENTES. "ITextBoxSelectEntidadeUsuario.class.php";

$stPrograma = 'RelatorioInventario';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';
$pgGera = 'OCGeraRelatorioInventario.php';

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

$obHdnCodEntidade = new Hidden;

//Define objeto de select multiplo de entidade por usuários
$obISelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
$obISelectEntidadeUsuario->setNull(false);
$obISelectEntidadeUsuario->obSelect->setNull(false);
$obISelectEntidadeUsuario->obTextBox->setNull(false);

if ($obISelectEntidadeUsuario->inCodEntidade != '') {
    $obHdnCodEntidade->setValue( $obISelectEntidadeUsuario->inCodEntidade );
}

$obTAlmoxarifadoInventario = new TAlmoxarifadoInventario;
$stFiltro = " ";
$stOrdem = " order by inventario.dt_inventario desc, inventario.cod_inventario desc ";
$obTAlmoxarifadoInventario->listarInventario($rsInventario,$stFiltro,$stOrdem);
$obCmbInventario = new Select();
$obCmbInventario->setTitle     ( "Selecione o Inventário"          );
$obCmbInventario->setName      ( "idInventario"                    );
$obCmbInventario->setId        ( "idInventario"                    );
$obCmbInventario->setRotulo    ( "Inventário"                      );
$obCmbInventario->addOption    ( "", "Selecione"                   );
$obCmbInventario->setCampoId   ( "[exercicio]-[cod_almoxarifado]-[cod_inventario]"                  );
$obCmbInventario->setCampoDesc ( "[desc_almoxarifado] - [cod_inventario] - [dt_inventario]" );
$obCmbInventario->preencheCombo( $rsInventario                     );
$obCmbInventario->setNull      ( false                             );

$obRdTipoRelatorioCompleto = new Radio;
$obRdTipoRelatorioCompleto->setRotulo                      ( "Tipo de Relatório"               );
$obRdTipoRelatorioCompleto->setTitle                       ( "Selecione se deve ser Completo ou somente com as Diferenças de Estoque." );
$obRdTipoRelatorioCompleto->setName                        ( "stTipoRelatorio"                 );
$obRdTipoRelatorioCompleto->setLabel                       ( 'Completo'                        );
$obRdTipoRelatorioCompleto->setValue                       ( 'completo'                        );
$obRdTipoRelatorioCompleto->setChecked                     ( true                              );

$obRdTipoRelatorioDiferenca = new Radio;
$obRdTipoRelatorioDiferenca->setName                        ( "stTipoRelatorio"                );
$obRdTipoRelatorioDiferenca->setLabel                       ( 'Com Diferença'                  );
$obRdTipoRelatorioDiferenca->setValue                       ( 'diferenca'                      );
$obRdTipoRelatorioDiferenca->setChecked                     ( false                            );

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

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
//$obFormulario->setAjuda("UC-03.03.24");
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnStCtrl );
//$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addTitulo( "Dados para o filtro" );
$obFormulario->addComponente( $obISelectEntidadeUsuario );
$obFormulario->addComponente( $obCmbInventario );
$obFormulario->agrupaComponentes( array( $obRdTipoRelatorioCompleto, $obRdTipoRelatorioDiferenca      ) );
$obFormulario->agrupaComponentes( array( $obRdOrdemClassificacao, $obRdOrdemItem, $obRdOrdemDescricao ) );
$obMontaAssinaturas->geraFormulario( $obFormulario );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
