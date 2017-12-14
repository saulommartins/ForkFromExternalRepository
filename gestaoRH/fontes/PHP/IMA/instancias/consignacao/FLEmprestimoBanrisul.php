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
 * Filtro de empréstimos Banrisul
 * Data de Criação   : 01/09/2009

 * @author Analista      Dagine Rodrigues Vieira
 * @author Desenvolvedor Cassiano de Vasconcellos Ferreira

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoPeriodoMovimentacao.class.php';
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );
$stTitulo = $obRFolhaPagamentoFolhaSituacao->consultarCompetencia();

//Define o nome dos arquivos PHP
$stPrograma = "EmprestimoBanrisul";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgFormImp  = "FMImportar".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

Sessao::remove('arHeader'  );
Sessao::remove('arDetalhe' );
Sessao::remove('arTrailler');
Sessao::remove('inCodPeriodoMovimentacao');
Sessao::remove('stFiltro');
/*
 * Definição dos componentes
 *
 */
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( 'stAcao');
$obHdnAcao->setId   ( 'stAcao');
$obHdnAcao->setValue( $stAcao );

$obRdoAcaoImportar = new Radio();
$obRdoAcaoImportar->setRotulo("Ação");
$obRdoAcaoImportar->setTitle("Informe o cadastro para filtro para emissão do arquivo.");
$obRdoAcaoImportar->setName("stAcaoEmprestimo");
$obRdoAcaoImportar->setValue("importar");
$obRdoAcaoImportar->setLabel("Importar Arquivo Banco");
$obRdoAcaoImportar->setNull(false);
$obRdoAcaoImportar->setChecked(true);
$obRdoAcaoImportar->obEvento->setOnChange("montaParametrosGET('geraSpanImportacao');");

$obRdoAcaoExportar = new Radio();
$obRdoAcaoExportar->setRotulo("Ação");
$obRdoAcaoExportar->setTitle("Informe o cadastro para filtro para emissão do arquivo.");
$obRdoAcaoExportar->setName("stAcaoEmprestimo");
$obRdoAcaoExportar->setValue("exportar");
$obRdoAcaoExportar->setLabel("Retornar ao Banco");
$obRdoAcaoExportar->setNull(false);
$obRdoAcaoExportar->obEvento->setOnChange("montaParametrosGET('geraSpanExportacao');");

$obHdnValidaArquivo = new hiddenEval;
$obHdnValidaArquivo->setName('stHdnArquivoImportacao');
$obHdnValidaArquivo->setId('stHdnArquivoImportacao');

$obSpnFiltroArquivo = new Span();
$obSpnFiltroArquivo->setId("spnFiltroArquivo");

$obForm = new  Form;
$obForm->setAction( $pgProc);
$obForm->setTarget("oculto");
$obForm->setEncType('multipart/form-data');

$jsOnload = 'montaParametrosGET(\'geraSpanImportacao\');';

$obButtonOK = new OK;
$obButtonOK->setName  ( "BtnEmitir" );
$obButtonOK->setValue ( "OK" );
$obButtonOK->obEvento->setOnClick( "javascript: EnviaFormulario();");

$obBtnLimpar = new Limpar;
$obBtnLimpar->obEvento->setOnClick( "Limpar();" );

/*
 * Definição do formulário
 *
 */
$obFormulario = new Formulario;
$obFormulario->setForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addTitulo($stTitulo,'right');
$obFormulario->addTitulo('Seleção do Filtro');
$obFormulario->addHidden($obHdnValidaArquivo,true);
$obFormulario->agrupaComponentes(array($obRdoAcaoImportar,$obRdoAcaoExportar));
$obFormulario->addSpan($obSpnFiltroArquivo);

$obFormulario->defineBarra( array( $obButtonOK, $obBtnLimpar ), "left", "" );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

include $pgJS;

?>
