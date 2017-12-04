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
    * Página de formulário do Manter documentos
    * Data de Criacao: 06/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_NORMAS_CLASSES . "componentes/IPopUpNorma.class.php" );
include_once( CAM_GT_FIS_NEGOCIO . "RFISConfiguracao.class.php");
require_once( CAM_GA_ADM_COMPONENTES . "ITextBoxSelectDocumento.class.php" );
require_once( CAM_GT_MON_COMPONENTES ."IPopUpIndicadorEconomico.class.php" );
include_once( CAM_GT_FIS_VISAO . "VFISManterConfiguracao.class.php");

$stAcao = $request->get('stAcao');
Sessao::write( 'arValores', array() );

if ( empty( $stAcao ) ) { $stAcao = "configurar"; }

//Define o nome dos arquivos
$stPrograma = "ManterConfiguracao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc    	= "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";

include_once( $pgJs );

$obRFISConfiguracao = new RFISConfiguracao;
$obVFISConfiguracao = new VFISManterConfiguracao($obRFISConfiguracao);

$obRFISConfiguracao->consultar();

$stNormaInicio      	    	= $obRFISConfiguracao->getNormaInicio();
$inNormaTermino   	    		= $obRFISConfiguracao->getNormaTermino();
$inDocumentoAutoInfracao    	= $obRFISConfiguracao->getDocumentoAutoInfracao();
$inIndicadorEconomico    	    = $obRFISConfiguracao->getIndicadorEconomico();

//Acao do Form
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

//Campos Hidden
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($_REQUEST['stAcao']);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue($_REQUEST['stCtrl']);

//MONTA FORMLÁRIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                    );

$obFormulario->addHidden     ( $obHdnAcao                 );
$obFormulario->addHidden     ( $obHdnCtrl                 );
$obFormulario->addTitulo ( "Dados para Configuração" );

# Define fundamentacao legal inicio
$obIPopUpNormaInicio = new IPopUpNorma();
$obIPopUpNormaInicio->obInnerNorma->setRotulo( "Fundamentação Legal Início" );
$obIPopUpNormaInicio->obInnerNorma->setTitle( "Norma que regulamenta o início de processos fiscais" );
$obIPopUpNormaInicio->obInnerNorma->obCampoCod->setValue( $stNormaInicio  );
$obIPopUpNormaInicio->obInnerNorma->obCampoCod->setName( "norma_inicio" );
$obIPopUpNormaInicio->obInnerNorma->obCampoCod->setId( "norma_inicio" );
$obIPopUpNormaInicio->obInnerNorma->setNull( false );
$obIPopUpNormaInicio->obInnerNorma->setId('stNormaInicio');
$obIPopUpNormaInicio->geraFormulario( $obFormulario );
$obIPopUpNormaInicio->setCodNorma($stNormaInicio);
if ($stNormaInicio) {
    $obIPopUpNormaInicio->obInnerNorma->setValue ( $obVFISConfiguracao->descricao($stNormaInicio) );
}

# Define fundamentacao legal encerramento
$obIPopUpNormaEncerramento = new IPopUpNorma();
$obIPopUpNormaEncerramento->obInnerNorma->setRotulo( "Fundamentação Legal Encerramento" );
$obIPopUpNormaEncerramento->obInnerNorma->obCampoCod->setValue( $inNormaTermino );
$obIPopUpNormaEncerramento->obInnerNorma->obCampoCod->setValue( $inNormaTermino );
$obIPopUpNormaEncerramento->obInnerNorma->obCampoCod->setName( "norma_termino" );
$obIPopUpNormaEncerramento->obInnerNorma->obCampoCod->setId( "norma_termino" );
$obIPopUpNormaEncerramento->obInnerNorma->setTitle( "Norma que regulamenta o encerramento de processos fiscais" );
$obIPopUpNormaEncerramento->obInnerNorma->setId('stNormaTermino');
$obIPopUpNormaEncerramento->geraFormulario( $obFormulario );
$obIPopUpNormaEncerramento->setCodNorma($inNormaTermino);
if ($inNormaTermino) {
    $obIPopUpNormaEncerramento->obInnerNorma->setValue ( $obVFISConfiguracao->descricao($inNormaTermino) );
}

# Indicador Econômico
$obIndicadorEconomico = new IPopUpIndicadorEconomico();
$obIndicadorEconomico->setRotulo( 'Indicador Econômico' );
$obIndicadorEconomico->setTitle( 'Informe o código do Indicador Econômico.' );
$obIndicadorEconomico->obCampoCod->setValue( $inIndicadorEconomico );
$obIndicadorEconomico->setNull( false );
$obIndicadorEconomico->geraFormulario( $obFormulario );
if ($inIndicadorEconomico) {
    $obIndicadorEconomico->setValue( $obVFISConfiguracao->nomeIndicador($inIndicadorEconomico) );
}
//echo "<pre>",print_r($obIndicadorEconomico),"</pre>";

# Define documento de auto de infração
$obDocumentoInfracao = new ITextBoxSelectDocumento;
$obDocumentoInfracao->setCodAcao( substr($_SESSION["acao"], 5,-2) ) ;
$obDocumentoInfracao->obTextBoxSelectDocumento->setRotulo( "Documento de Auto de Infração" );
$obDocumentoInfracao->obTextBoxSelectDocumento->setTitle( "Selecione o Termo de Entrega." );
$obDocumentoInfracao->obTextBoxSelectDocumento->obTextBox->setSize( 10 );
$obDocumentoInfracao->obTextBoxSelectDocumento->obSelect->setStyle( "width: 261px;" );
$obDocumentoInfracao->obTextBoxSelectDocumento->setNull( false );
$obDocumentoInfracao->obTextBoxSelectDocumento->setId( "nom_documento" );
$obDocumentoInfracao->obTextBoxSelectDocumento->obSelect->setValue($inDocumentoAutoInfracao, "Selecione");
$obDocumentoInfracao->obTextBoxSelectDocumento->obTextBox->setValue($inDocumentoAutoInfracao);
$obDocumentoInfracao->obTextBoxSelectDocumento->obTextBox->setInteiro(true);
$obDocumentoInfracao->geraFormulario( $obFormulario );

$stCodNorma 	= $obIPopUpNormaInicio->obInnerNorma->obCampoCod->getName();
$stLNorma 	    = $obIPopUpNormaInicio->obInnerNorma->getId();
$stCodNormaFIM 	= $obIPopUpNormaEncerramento->obInnerNorma->obCampoCod->getName();
$stLNormaFIM 	= $obIPopUpNormaEncerramento->obInnerNorma->getId();

$obBtnIncluir = new Button;
$obBtnIncluir->setName                  ( "Ok"        );
$obBtnIncluir->setValue                 ( "Ok"        );
$obBtnIncluir->setTipo                  ( "button"    );
$obBtnIncluir->obEvento->setOnClick     ( "Salvar();" );
$obBtnIncluir->setDisabled              (  false      );

$obBtnLimpar = new Button;
$obBtnLimpar->setName                   ( "Limpar"    );
$obBtnLimpar->setValue                  ( "Limpar"    );
$obBtnLimpar->setTipo                   ( "button"    );
$obBtnLimpar->obEvento->setOnClick      ( "LimparConfiguracao();" );
$obBtnLimpar->setDisabled               (  false      );

$obFormulario->defineBarra( array( $obBtnIncluir, $obBtnLimpar ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
