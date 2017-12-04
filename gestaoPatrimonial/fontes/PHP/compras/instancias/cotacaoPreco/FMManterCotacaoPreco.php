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
    * Tela do formulário para inclusão de Cotação de Preço
    * Data de Criação   : 18/09/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    Casos de uso: uc-03.04.04
*/

/*
$Log$
Revision 1.2  2007/08/15 16:03:44  hboaventura
Bug#9925#

Revision 1.1  2006/09/18 14:42:15  cleisson
Inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_COM_COMPONENTES."IPopUpFornecedor.class.php"                                                          );
//include_once(CAM_GP_COM_COMPONENTES."IMontaMapa.class.php"                                                                );

$stPrograma = "ManterCotacaoPreco";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o exercicio corrente
$obLblExercicio = new Label;
$obLblExercicio->setRotulo( "Exercicio"        );
$obLblExercicio->setValue ( Sessao::getExercicio() );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

//Define componente para armazenar se a compra a ser realizada é por Lote
$obRdCompraLote = new SimNao;
$obRdCompraLote->setRotulo ( "Compra por Lote");
$obRdCompraLote->setTitle  ( "Selecione se compra por lote." );
$obRdCompraLote->setName   ( "boCompraLote" );
$obRdCompraLote->obRadioSim->setValue('true');
$obRdCompraLote->obRadioNao->setValue('false');
$obRdCompraLote->setChecked  ( "N" );

//Define componente para selecionar o Mapa de compras
/*
INCLUIR COMPONENTE
*/

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

//Define componente para Fornecedor
$obFornecedor = new IPopUpFornecedor($obForm);
$obFornecedor->setId ( "stNomFornecedor" );
$obFornecedor->setObrigatorioBarra(true);

$obSpnFornecedor = new Span;
$obSpnFornecedor->setId( "spnFornecedor" );

$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-03.04.04');
$obFormulario->addForm        ( $obForm                                             );
$obFormulario->addHidden      ( $obHdnAcao                                          );
$obFormulario->addHidden      ( $obHdnCtrl                                          );
$obFormulario->addTitulo      ( "Dados da Cotação"                                  );
$obFormulario->addComponente  ( $obLblExercicio                                     );
$obFormulario->addComponente  ( $obRdCompraLote                                     );
$obFormulario->addTitulo      ( "Dados do Mapa"                                     );
//$obFormulario->addComponente  ( $obMontaMapa                                        );
$obFormulario->addTitulo      ( "Dados dos Fornecedores Participantes da Cotação"   );
$obFormulario->addComponente  ( $obFornecedor                                       );
$obFormulario->Incluir('montaListaFornecedores', array( $obFornecedor ) );
$obFormulario->addSpan( $obSpnFornecedor );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
