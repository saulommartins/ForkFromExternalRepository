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
 * Página de Formulario de Manter Inventario
 * Data de Criação: 01/10/2007

 * @author Analista:      Gelson Wolowski
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>

 $Id:$

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

# Define o nome dos arquivos PHP
$stPrograma = "ManterInventario";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgList     = "LS".$stPrograma.".php";

$stAcao = $request->get('stAcao');

Sessao::write('arInventario', array());

include $pgJs;

$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ("stAcao");
$obHdnAcao->setId    ("stAcao");
$obHdnAcao->setValue ($stAcao);

$obHdnIdInventario = new Hidden;
$obHdnIdInventario->setId    ('inIdInventario');
$obHdnIdInventario->setName  ('inIdInventario');
$obHdnIdInventario->setValue ($_REQUEST['inIdInventario']);

$obExercicio = new TextBox;
$obExercicio->setId     ('stExercicio');
$obExercicio->setName   ('stExercicio');
$obExercicio->setRotulo ('Exercício');
$obExercicio->setLabel  (true);
$obExercicio->setValue  ((($stAcao == 'incluir') ? Sessao::getExercicio() : $_REQUEST['stExercicio']));

# Componente que mostra o código do inventário.
$obStCodInventario = new Label;
$obStCodInventario->setId     ('stIdInventario');
$obStCodInventario->setName   ('stIdInventario');
$obStCodInventario->setRotulo ('Código do Inventário');
$obStCodInventario->setValue  ($_REQUEST['inIdInventario']);

$obDataInicial = new Data();
$obDataInicial->setName     ('stDataInicial');
$obDataInicial->setId       ('stDataInicial');
$obDataInicial->setRotulo   ('Data Inicial');
$obDataInicial->setValue    ( (($stAcao == 'incluir') ? date("d/m/Y") : $_REQUEST['stDataInicial']) );
$obDataInicial->setSize     (7);

$obTxtObservacao = new TextArea;
$obTxtObservacao->setId     ('stObservacao');
$obTxtObservacao->setName   ('stObservacao');
$obTxtObservacao->setRotulo ('Observação ');
$obTxtObservacao->setTitle  ('Informe uma observação.');
$obTxtObservacao->setValue  ($_REQUEST['stObservacao']);

$obBtnOk = new Ok;
$obBtnOk->setName  ( "btnOk" );
$obBtnOk->setValue ( "Ok" );

$obBtnLimparTela = new Button;
$obBtnLimparTela->setName  ( "btnLimparTela" );
$obBtnLimparTela->setValue ( "Limpar" );
$obBtnLimparTela->setTipo  ( "button" );
$obBtnLimparTela->obEvento->setOnClick ( "executaFuncaoAjax('limpartela', '&stAcao=".$_REQUEST['stAcao']."');" );

$stProxPage = $pgList."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'];

$obBtnCancelar = new Button;
$obBtnCancelar->setName  ( "btnCancelar" );
$obBtnCancelar->setValue ( "Cancelar" );
$obBtnCancelar->setTipo  ( "button" );
$obBtnCancelar->obEvento->setOnClick( "Cancelar('".$stProxPage."');" );

$obSpan = new Span();
$obSpan->setId ('spnBemPatrimonio');

$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnIdInventario );

$obFormulario->addTitulo     ( 'Dados do Inventário' );
$obFormulario->addComponente ( $obExercicio );
$obFormulario->addComponente ( $obStCodInventario);
$obFormulario->addComponente ( $obDataInicial );
$obFormulario->addComponente ( $obTxtObservacao);

$obFormulario->addSpan($obSpan);

if ($stAcao == 'incluir') {
    $obFormulario->defineBarra( array($obBtnOk, $obBtnLimparTela), "left", "" );
} else {
    $obFormulario->defineBarra( array($obBtnOk, $obBtnCancelar), "left", "" );
}

$obFormulario->show();

if ($stAcao == "processar") {
    $jsOnLoad  .= "jQuery('#Ok').attr('disabled', 'disabled');";
}

//$jsOnLoad .= "BloqueiaFrames(true, false);";
# Chamada para a função que irá popular as tabelas do Inventário com o último status dos Bens do Patrimônio.
$jsOnLoad .= "montaParametrosGET('recuperaCargaInicial', '');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
