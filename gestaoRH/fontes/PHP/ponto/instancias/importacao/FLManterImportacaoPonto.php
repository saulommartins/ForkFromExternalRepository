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
    * Formulário
    * Data de Criação: 07/10/2008

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.10.04

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoFormatoImportacao.class.php"                               );

$stPrograma = "ManterImportacaoPonto";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao   = $_REQUEST["stAcao"];

$obForm = new Form;
$obForm->setAction  ( $pgProc               );
$obForm->setTarget  ( "oculto"              );
$obForm->setEncType ( "multipart/form-data" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$stOrdem = " descricao ";
$obTPontoFormatoImportacao = new TPontoFormatoImportacao();
$obTPontoFormatoImportacao->recuperaTodos($rsFormatoImportacao, $stFiltro="", $stOrdem);

$obCmbFormatoImportacao = new Select();
$obCmbFormatoImportacao->setTitle      ( "Informe a configuração do formato de importação previamente cadastrado."  );
$obCmbFormatoImportacao->setRotulo     ( "Formato de Importação"                                                    );
$obCmbFormatoImportacao->setName       ( "inCodFormato"                                                             );
$obCmbFormatoImportacao->setId         ( "inCodFormato"                                                             );
$obCmbFormatoImportacao->addOption     ( "", "Selecione"                                                            );
$obCmbFormatoImportacao->setCampoDesc  ( "descricao"                                                                );
$obCmbFormatoImportacao->setCampoId    ( "cod_formato"                                                              );
$obCmbFormatoImportacao->preencheCombo ( $rsFormatoImportacao                                                       );
$obCmbFormatoImportacao->setValue      ( $inCodCampo                                                                );
$obCmbFormatoImportacao->setNull       ( false                                                                      );

$obFilArquivo = new FileBox;
$obFilArquivo->setRotulo                ( "Caminho do Arquivo" );
$obFilArquivo->setTitle                 ( "Informe ou selecione o caminho e o nome do arquivo(TXT) para importação das informações do relógio ponto." );
$obFilArquivo->setName                  ( "stCaminho" );
$obFilArquivo->setId                    ( "stCaminho" );
$obFilArquivo->setSize                  ( 40          );
$obFilArquivo->setMaxLength             ( 100         );
$obFilArquivo->setNull                  ( false       );
$obFilArquivo->obEvento->setOnClick     ( "montaParametrosGET('importarArquivo'" );

$obChkSubstituirDados = new CheckBox();
$obChkSubstituirDados->setRotulo  ( "Substituir Dados?" );
$obChkSubstituirDados->setName    ( "boSubstituirDados" );
$obChkSubstituirDados->setId      ( "boSubstituirDados" );
$obChkSubstituirDados->setTitle   ( "Marque para que os dados importados substituam os dados no sistema caso já existam." );
$obChkSubstituirDados->setChecked ( true);
$obChkSubstituirDados->setValue   ( true);

$obPeriodo = new Periodo();
$obPeriodo->setRotulo("Período a Importar");
$obPeriodo->setTitle("Informe o período a ser importado, no caso de importar todos os dias do relógio, deixar este campo em branco.");

$obChkImportacaoParcial = new CheckBox();
$obChkImportacaoParcial->setRotulo  ( "Importação Parcial"  );
$obChkImportacaoParcial->setName    ( "boImportacaoParcial" );
$obChkImportacaoParcial->setId      ( "boImportacaoParcial" );
$obChkImportacaoParcial->setTitle   ( "Marque para importação parcial de servidores." );
$obChkImportacaoParcial->setChecked ( false );
$obChkImportacaoParcial->setValue   ( true  );
$obChkImportacaoParcial->obEvento->setOnChange("montaParametrosGET('gerarSpanImportacaoParcial', 'boImportacaoParcial');");

$obSpnFiltrar = new Span;
$obSpnFiltrar->setId( "spnImportacaoParcial" );

$obBntOk = new ok();
$obBntOk->obEvento->setOnClick( "BloqueiaFrames(true,false); Salvar();" );

$obBtnLimpar = new Limpar();

$obFormulario = new Formulario;
$obFormulario->addForm           ( $obForm                                                          );
$obFormulario->addTitulo         ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden         ( $obHdnCtrl                                                       );
$obFormulario->addHidden         ( $obHdnAcao                                                       );
$obFormulario->addTitulo         ( "Importação de Pontos"                                           );
$obFormulario->addComponente     ( $obCmbFormatoImportacao                                          );
$obFormulario->addComponente     ( $obFilArquivo                                                    );
$obFormulario->addComponente     ( $obChkSubstituirDados                                            );
$obFormulario->addComponente     ( $obPeriodo                                                       );
$obFormulario->addComponente     ( $obChkImportacaoParcial                                          );
$obFormulario->addSpan           ( $obSpnFiltrar                                                    );
$obFormulario->defineBarra       (array($obBntOk, $obBtnLimpar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
