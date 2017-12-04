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
    * Data de Criação: 03/10/2008

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.10.12

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );

$stPrograma = "ManterConfiguracaoFormato";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$jsOnload  = " montaParametrosGET('processaOnLoad','boFormatoColuna'); ";
$stAcao    = $_REQUEST["stAcao"];
$inId      = $_REQUEST["inId"];

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnInId = new Hidden;
$obHdnInId->setName( "inId" );
$obHdnInId->setId( "inId" );
$obHdnInId->setValue( $inId );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo              ( "Descrição"   );
$obTxtDescricao->setTitle               ( "Informe a descrição do formato de importação, para referência no momento da importação do arquivo.");
$obTxtDescricao->setName                ( "stDescricao" );
$obTxtDescricao->setId                  ( "stDescricao" );
$obTxtDescricao->setValue               ( $stDescricao  );
$obTxtDescricao->setSize                ( 40            );
$obTxtDescricao->setMaxLength           ( 50            );
$obTxtDescricao->setNullBarra           ( false         );

$obRadioReferenciaCadastroMatricula = new Radio();
$obRadioReferenciaCadastroMatricula->setName   ( "boReferenciaCadastro" );
$obRadioReferenciaCadastroMatricula->setRotulo ( "Referência do Cadastro" );
$obRadioReferenciaCadastroMatricula->setTitle  ( "Selecione o campo (cadastro do servidor) de referência para a importação: matrícula ou número do cartão ponto." );
$obRadioReferenciaCadastroMatricula->setLabel  ( "Matrícula" );
$obRadioReferenciaCadastroMatricula->setValue  ( "MATRICULA" );
$obRadioReferenciaCadastroMatricula->setId     ( "boReferenciaCadastroMatricula" );
if ( trim($boReferenciaCadastro) == "" ) {
    $obRadioReferenciaCadastroMatricula->setChecked ( true );
}

$obRadioReferenciaCadastroCartaoPonto = new Radio();
$obRadioReferenciaCadastroCartaoPonto->setName   ( "boReferenciaCadastro" );
$obRadioReferenciaCadastroCartaoPonto->setRotulo ( "Referência do Cadastro" );
$obRadioReferenciaCadastroCartaoPonto->setTitle  ( "Selecione o campo (cadastro do servidor) de referência para a importação: matrícula ou número do cartão ponto." );
$obRadioReferenciaCadastroCartaoPonto->setLabel  ( "Cartão Ponto" );
$obRadioReferenciaCadastroCartaoPonto->setValue  ( "CARTAO_PONTO" );
$obRadioReferenciaCadastroCartaoPonto->setId     ( "boReferenciaCadastroCartaoPonto" );

$obRadioFormatoColunasFixo = new Radio();
$obRadioFormatoColunasFixo->setName   ( "boFormatoColuna" );
$obRadioFormatoColunasFixo->setRotulo ( "Formato das Colunas" );
$obRadioFormatoColunasFixo->setTitle  ( "Selecione o formato de importação das colunas do arquivo: colunas de tamanho fixo ou colunas com caracter delimitador." );
$obRadioFormatoColunasFixo->setLabel  ( "Tamanho Fixo" );
$obRadioFormatoColunasFixo->setValue  ( "FIXO" );
$obRadioFormatoColunasFixo->setId     ( "boFormatoColunaFixo");
$obRadioFormatoColunasFixo->obEvento->setOnChange( "montaParametrosGET('montaSpanFormatoColunas','boFormatoColuna');" );
if ( trim($boFormatoColuna) == "" ) {
    $obRadioFormatoColunasFixo->setChecked ( true );
}

$obRadioFormatoColunasDelimitador = new Radio();
$obRadioFormatoColunasDelimitador->setName   ( "boFormatoColuna" );
$obRadioFormatoColunasDelimitador->setRotulo ( "Formato das Colunas" );
$obRadioFormatoColunasDelimitador->setTitle  ( "Selecione o formato de importação das colunas do arquivo: colunas de tamanho fixo ou colunas com caracter delimitador." );
$obRadioFormatoColunasDelimitador->setLabel  ( "Delimitador entre colunas" );
$obRadioFormatoColunasDelimitador->setValue  ( "DELIMITADOR" );
$obRadioFormatoColunasDelimitador->setId     ( "boFormatoColunaDelimitador" );
$obRadioFormatoColunasDelimitador->obEvento->setOnChange( "montaParametrosGET('montaSpanFormatoColunas','boFormatoColuna');" );

$obSpnFormatoColunas = new Span;
$obSpnFormatoColunas->setId ( "spnInfFormatoColunas" );

$obSpnListaFormatos = new Span;
$obSpnListaFormatos->setId ( "spnListaFormatos" );

$arCampos = array( $obTxtDescricao );

$obBtnOk = new Ok();

$obFormulario = new Formulario;
$obFormulario->addForm           ( $obForm                                                          );
$obFormulario->addTitulo         ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden         ( $obHdnCtrl                                                       );
$obFormulario->addHidden         ( $obHdnAcao                                                       );
$obFormulario->addHidden         ( $obHdnInId                                                       );
$obFormulario->addTitulo         ( "Configuração dos Formatos de Importação"                        );
$obFormulario->addComponente     ( $obTxtDescricao                                                  );
$obFormulario->addTitulo         ( "Campos para a Importação(somente arquivos do tipo texto)"       );
$obFormulario->agrupaComponentes ( array($obRadioReferenciaCadastroMatricula, $obRadioReferenciaCadastroCartaoPonto));
$obFormulario->agrupaComponentes ( array($obRadioFormatoColunasFixo, $obRadioFormatoColunasDelimitador));
$obFormulario->addSpan           ( $obSpnFormatoColunas                                             );
$obFormulario->IncluirAlterar    ( "ConfiguracaoImportacao", array(), false                         );
$obFormulario->addSpan           ( $obSpnListaFormatos                                              );
$obFormulario->defineBarra       ( array($obBtnOk)                                                  );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
