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
/*
 * Formulário de Consulta de Itens
 * Data de Criação   : 27/06/2009

 * @author Analista      Gelson Gonçalves
 * @author Desenvolvedor Alexandre Melo

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stProjeto = 'ManterItemConsulta';
$pgFilt = 'FL'.$stProjeto.'.php';
$pgList = 'LSManterItem.php';
$pgForm = 'FM'.$stProjeto.'.php';
$pgProc = 'PR'.$stProjeto.'.php';
$pgOcul = 'OC'.$stProjeto.'.php';
$pgJS   = 'JS'.$stProjeto.'.php';

Sessao::write('pg', $_GET['pg']);
Sessao::write('pos', $_GET['pos']);

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"        );
$obHdnAcao->setValue ( $_GET['stAcao'] );

$obHdnCodigo = new Hidden;
$obHdnCodigo->setName  ( "inCodigo"        );
$obHdnCodigo->setId    ( "inCodigo"        );
$obHdnCodigo->setValue ( $_GET['inCodigo'] );

$obForm = new Form;
$obForm->setAction   ( "LSManterItem.php"  );
$obForm->setTarget   ( "telaPrincipal"     );

$obSpnDadosClassificacao = new Span;
$obSpnDadosClassificacao->setId('spnDadosCLassificacao');

$obLblCodigo = new Label;
$obLblCodigo->setRotulo ( "Código"              );
$obLblCodigo->setName   ( "inCodigo"            );
$obLblCodigo->setId     ( "inCodigo"            );
$obLblCodigo->setValue  ( $_REQUEST['inCodigo'] );

$obLblTipo = new Label;
$obLblTipo->setRotulo  ( "Tipo"              );
$obLblTipo->setName    ( "stTipo"            );
$obLblTipo->setId      ( "stTipo"            );
$obLblTipo->setValue   ( $_REQUEST['stTipo'] );

$obLblDescricaoRes = new Label;
$obLblDescricaoRes->setRotulo ( "Descrição Resumida"        );
$obLblDescricaoRes->setName   ( "stDescResumida"            );
$obLblDescricaoRes->setId     ( "stDescResumida"            );
$obLblDescricaoRes->setValue  ( $_REQUEST['stDescResumida'] );

$obLblDescricao = new Label;
$obLblDescricao->setRotulo ( "Descrição"                );
$obLblDescricao->setName   ( "stDescricao"              );
$obLblDescricao->setId     ( "stDescricao"              );
$obLblDescricao->setValue  ( $_REQUEST['stDescQuestao'] );

$obLblVlUltCompra = new Label;
$obLblVlUltCompra->setRotulo ( "Valor da Última Compra"   );
$obLblVlUltCompra->setName   ( "nuVlUltCompra"            );
$obLblVlUltCompra->setId     ( "nuVlUltCompra"            );
$obLblVlUltCompra->setValue  ( $_REQUEST['nuVlUltCompra'] );

$obLblStatus = new Label;
$obLblStatus->setRotulo ( "Status"                 );
$obLblStatus->setName   ( "stStatus"               );
$obLblStatus->setId     ( "stStatus"               );
$obLblStatus->setValue  ( $_REQUEST['stStatus']    );

$obLblUnMedida = new Label;
$obLblUnMedida->setRotulo ( "Unidade Medida"             );
$obLblUnMedida->setName   ( "stUnidadeMedida"            );
$obLblUnMedida->setId     ( "stUnidadeMedida"            );
$obLblUnMedida->setValue  ( $_REQUEST['stUnidadeMedida'] );

$obLblEstoqueMinimo = new Label;
$obLblEstoqueMinimo->setRotulo ( "Estoque Mínimo"             );
$obLblEstoqueMinimo->setName   ( "nuEstoqueMinimo"            );
$obLblEstoqueMinimo->setId     ( "nuEstoqueMinimo"            );
$obLblEstoqueMinimo->setValue  ( $_REQUEST['nuEstoqueMinimo'] );

$obLblPontoDePedido = new Label;
$obLblPontoDePedido->setRotulo ( "Ponto de Pedido"            );
$obLblPontoDePedido->setName   ( "nuPontoDePedido"            );
$obLblPontoDePedido->setId     ( "nuPontoDePedido"            );
$obLblPontoDePedido->setValue  ( $_REQUEST['nuPontoDePedido'] );

$obLblEstoqueMaximo = new Label;
$obLblEstoqueMaximo->setRotulo ( "Estoque Máximo"             );
$obLblEstoqueMaximo->setName   ( "nuEstoqueMaximo"            );
$obLblEstoqueMaximo->setId     ( "nuEstoqueMaximo"            );
$obLblEstoqueMaximo->setValue  ( $_REQUEST['nuEstoqueMaximo'] );

$obSpnAtributos = new Span;
$obSpnAtributos->setId('spnAtributos');

$pgList .= "?pg=".$_GET['pg']."&pos=".$_GET['pos']."&stAcao=".$_GET['stAcao'];

$obButtonVoltar = new Button;
$obButtonVoltar->setName  ( "Voltar" );
$obButtonVoltar->setValue ( "Voltar" );
$obButtonVoltar->obEvento->setOnClick( "Cancelar('".$pgList."');" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCodigo );
$obFormulario->addSpan($obSpnDadosClassificacao);
$obFormulario->addTitulo("Dados do Item");
$obFormulario->addComponente($obLblCodigo);
$obFormulario->addComponente($obLblTipo);
$obFormulario->addComponente($obLblDescricaoRes);
$obFormulario->addComponente($obLblDescricao);
$obFormulario->addComponente($obLblVlUltCompra);
$obFormulario->addComponente($obLblStatus);
$obFormulario->addComponente($obLblUnMedida);
$obFormulario->addTitulo("Controle de Estoque");
$obFormulario->addComponente($obLblEstoqueMinimo);
$obFormulario->addComponente($obLblPontoDePedido);
$obFormulario->addComponente($obLblEstoqueMaximo);
$obFormulario->addSpan($obSpnAtributos);
$obFormulario->defineBarra ( array( $obButtonVoltar ), 'left', '' );
$obFormulario->show();

$jsOnLoad = "montaParametrosGET('carregaDados','inCodigo');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
