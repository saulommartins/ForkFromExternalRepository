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
 * Arquivo filtro - Detalhameto da Adesão a Registro de Preços TCE/MG
 *
 * @category    Urbem
 * @package     TCE/MG
 * @author      Eduardo Schitz   <eduardo.schitz@cnm.org.br>
 * $Id: FLManterRegistroPreco.php 63501 2015-09-03 17:22:53Z michel $
 * $Date: 2015-09-03 14:22:53 -0300 (Thu, 03 Sep 2015) $
 * $Author: michel $
 * $Rev: 63501 $
 *
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeGeral.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasModalidade.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroPreco";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obHdnAcao =  new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

$obForm = new Form;
$obForm->setAction($pgList);

$obTxtExercicioRegistroPreco = new TextBox();
$obTxtExercicioRegistroPreco->setName       ( 'stExercicioRegistroPreco' );
$obTxtExercicioRegistroPreco->setId         ( 'stExercicioRegistroPreco' );
$obTxtExercicioRegistroPreco->setRotulo     ( 'Exercício' );
$obTxtExercicioRegistroPreco->setMaxLength  ( 4 );
$obTxtExercicioRegistroPreco->setSize       ( 5 );
$obTxtExercicioRegistroPreco->setNull       ( true );
$obTxtExercicioRegistroPreco->setInteiro    ( true );
$obTxtExercicioRegistroPreco->setValue      ( Sessao::getExercicio() );
$obTxtExercicioRegistroPreco->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioRegistroPreco.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value, 'carregaLicitacaoFiltro');");

$obITextBoxSelectEntidade = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidade->setId            ( 'stEntidade' );
$obITextBoxSelectEntidade->setName          ( 'stEntidade' );
$obITextBoxSelectEntidade->setObrigatorio   ( true );
$obITextBoxSelectEntidade->obTextBox->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioRegistroPreco.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value, 'carregaLicitacaoFiltro');");
$obITextBoxSelectEntidade->obSelect->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioRegistroPreco.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value, 'carregaLicitacaoFiltro');");

$obTxtCodigoProcesso = new TextBox();
$obTxtCodigoProcesso->setName('stCodigoProcesso');
$obTxtCodigoProcesso->setId('stCodigoProcesso');
$obTxtCodigoProcesso->setRotulo('Nro. do Processo de Registro de Preços');
$obTxtCodigoProcesso->setTitle('Número do processo de Registro de Preços.');
$obTxtCodigoProcesso->setMaxLength(12);
$obTxtCodigoProcesso->setNull(true);
$obTxtCodigoProcesso->setInteiro(true);

//Consulta para Buscar Modalidades Licitação
$obComprasModalidade = new TComprasModalidade();
$stFiltro = " WHERE cod_modalidade IN (3,6,7) ";
$stOrdem  = " ORDER BY cod_modalidade, descricao ";
$obComprasModalidade->recuperaTodos($rsModalidadeLicit, $stFiltro, $stOrdem);

//Montando Licitação Urbem
$obISelectModalidade = new Select();
$obISelectModalidade->setName       ( 'inCodModalidade' );
$obISelectModalidade->setId         ( 'inCodModalidade' );
$obISelectModalidade->setRotulo     ( 'Modalidade' );
$obISelectModalidade->setTitle      ( 'Selecione a Modalidade.' );
$obISelectModalidade->setCampoID    ( 'cod_modalidade' );
$obISelectModalidade->setValue      ( $inCodModalidade );
$obISelectModalidade->setCampoDesc  ( '[cod_modalidade] - [descricao]' );
$obISelectModalidade->addOption     ( '','Selecione' );
$obISelectModalidade->setNull       ( true );
$obISelectModalidade->preencheCombo ( $rsModalidadeLicit );
$obISelectModalidade->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioRegistroPreco.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value, 'carregaLicitacaoFiltro');");

$obISelectLicitacao = new Select();
$obISelectLicitacao->setName    ( 'inCodLicitacao' );
$obISelectLicitacao->setId      ( 'inCodLicitacao' );
$obISelectLicitacao->setRotulo  ( 'Licitação' );
$obISelectLicitacao->setTitle   ( 'Selecione a Licitação.' );
$obISelectLicitacao->addOption  ( '','Selecione' );
$obISelectLicitacao->setNull    ( true );
$obISelectLicitacao->setValue   ( $inCodLicitacao );

$obTxtExercicioEmpenho = new TextBox;
$obTxtExercicioEmpenho->setName     ( "stExercicioEmpenho"              );
$obTxtExercicioEmpenho->setValue    ( Sessao::getExercicio()            );
$obTxtExercicioEmpenho->setRotulo   ( "Exercício do Empenho"            );
$obTxtExercicioEmpenho->setTitle    ( "Informe o exercício do empenho." );
$obTxtExercicioEmpenho->setInteiro  ( false                             );
$obTxtExercicioEmpenho->setNull     ( true                              );
$obTxtExercicioEmpenho->setMaxLength( 4                                 );
$obTxtExercicioEmpenho->setSize     ( 5                                 );
$obTxtExercicioEmpenho->obEvento->setOnChange("montaParametrosGET('limpaEmpenho');");

$obBscEmpenho = new BuscaInner;
$obBscEmpenho->setTitle            ( "Informe o número do empenho." );
$obBscEmpenho->setRotulo           ( "Número do Empenho"            );
$obBscEmpenho->setId               ( "stEmpenho"                    );
$obBscEmpenho->setValue            ( $_REQUEST['stEmpenho']         );
$obBscEmpenho->setNull             ( true                           );
$obBscEmpenho->obCampoCod->setName ( "numEmpenho"                   );
$obBscEmpenho->obCampoCod->setId   ( "numEmpenho"                   );
$obBscEmpenho->obCampoCod->setValue(  $numEmpenho                   );
$obBscEmpenho->obCampoCod->obEvento->setOnBlur( "montaParametrosGET('buscaEmpenho','numEmpenho, inCodEntidade, stExercicioEmpenho');" );
$obBscEmpenho->setFuncaoBusca("abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLEmpenho.php','frm','numEmpenho','stEmpenho','empenhoNotaFiscal&inCodEntidade='+document.frm.inCodEntidade.value + '&stCampoExercicio=stExercicioEmpenho&stExercicioEmpenho='+document.frm.stExercicioEmpenho.value,'".Sessao::getId()."','800','550');");

$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm    );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addTitulo("Dados para filtro");
$obFormulario->addComponente($obTxtExercicioRegistroPreco);
$obFormulario->addComponente($obITextBoxSelectEntidade);
$obFormulario->addComponente($obTxtCodigoProcesso);
$obFormulario->addTitulo("Licitação");
$obFormulario->addComponente($obISelectModalidade);
$obFormulario->addComponente($obISelectLicitacao);
$obFormulario->addTitulo("Empenho");
$obFormulario->addComponente($obTxtExercicioEmpenho);
$obFormulario->addComponente($obBscEmpenho);
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>