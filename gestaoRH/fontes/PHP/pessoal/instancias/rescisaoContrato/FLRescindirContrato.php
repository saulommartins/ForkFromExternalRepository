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
* Página filtro para Pessoal - Rescisão Contrato
* Data de Criação   : 15/10/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @ignore

$Id: FLRescindirContrato.php 66479 2016-09-01 18:39:28Z michel $

* Casos de uso: uc-04.04.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma    = "RescindirContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

Sessao::remove('link');

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$jsOnload = "executaFuncaoAjax('atualizaSpanFiltro', '&rdoOpcao=2&stAcao=".$stAcao."');";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName('stCtrl');

$obHdnAcao = new Hidden;
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);

$obRdoRegistro = new Radio;
$obRdoRegistro->setName    ( "rdoOpcao"      );
$obRdoRegistro->setId      ( "rdoOpcao"      );
$obRdoRegistro->setRotulo  ( "Opções"        );
$obRdoRegistro->setLabel   ( "Matrícula"      );
$obRdoRegistro->setValue   ( 2             );
$obRdoRegistro->setChecked ( true            );
$obRdoRegistro->obEvento->setOnChange( "montaParametrosGET('atualizaSpanFiltro', 'rdoOpcao');" );

$obRdoCGMRegistro = new Radio;
$obRdoCGMRegistro->setName    ( "rdoOpcao"      );
$obRdoCGMRegistro->setId      ( "rdoOpcao"      );
$obRdoCGMRegistro->setRotulo  ( "Opções"        );
$obRdoCGMRegistro->setLabel   ( "CGM/Matrícula"  );
$obRdoCGMRegistro->setValue   ( 1               );
$obRdoCGMRegistro->setChecked ( false           );
$obRdoCGMRegistro->obEvento->setOnChange( "montaParametrosGET('atualizaSpanFiltro', 'rdoOpcao');" );

$obSpnOpcao = new Span;
$obSpnOpcao->setID("spnOpcao");

$obHdnEval = new HiddenEval;
$obHdnEval->setName ( "stEval" );
$obHdnEval->setValue( "" );

$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "" );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                       );
$obFormulario->addTitulo			( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden            ( $obHdnCtrl                    );
$obFormulario->addHidden            ( $obHdnAcao                    );
$obFormulario->addHidden            ( $obHdnEval,true               );
$obFormulario->addTitulo            ( "Opções para Busca"           );
$obFormulario->agrupaComponentes    ( array ( $obRdoRegistro, $obRdoCGMRegistro ) );
$obFormulario->addSpan              ( $obSpnOpcao                   );

$obBtnClean = new Button;
$obBtnClean->setName                    ( "btnClean"              );
$obBtnClean->setValue                   ( "Limpar"                );
$obBtnClean->setTipo                    ( "button"                );
$obBtnClean->obEvento->setOnClick       ( "montaParametrosGET('atualizaSpanFiltro', 'rdoOpcao');" );
$obBtnClean->setDisabled                ( false                   );

$obBtnOK = new Ok;
$botoesForm  = array ( $obBtnOK , $obBtnClean );
$obFormulario->defineBarra($botoesForm);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
