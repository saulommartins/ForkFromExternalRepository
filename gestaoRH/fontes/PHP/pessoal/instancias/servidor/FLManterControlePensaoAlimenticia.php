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
* Página de Formulario de filtro de servidor para o Controle de Pensão alimenticia
* Data de Criação   : 03/04/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Bruce Cruz de Sena

* @ignore

* Casos de uso: uc-04.04.45

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ManterControlePensaoAlimenticia';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once( $pgJS   );
include_once( $pgOcul );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( 'stCtrl' );
$obHdnCtrl->setValue    ( ''       );

$obHdnNumCGM = new Hidden;
$obHdnNumCGM->setName  ( 'numCGM' );
$obHdnNumCGM->setValue ( ''       );

$obRdoContrato = new Radio;
$obRdoContrato->setname               ( 'rdoOpcao'                                               );
$obRdoContrato->setID                 ( 'stRdoContrato'                                          );
$obRdoContrato->setTitle              ( 'Selecione o tipo de filtro para o contrato do Servidor' );
$obRdoContrato->setRotulo             ( 'Opções'                                                 );
$obRdoContrato->setLabel              ( 'Matrícula'                                              );
$obRdoContrato->setValue              ( 'C'                                                      );
$obRdoContrato->setChecked            ( true                                                     );
$obRdoContrato->obEvento->setOnChange ( "buscaValor('montaSpanContrato')"                        );

$obRdoCGM = new Radio;
$obRdoCGM->setname               ( 'rdoOpcao'                                               );
$obRdoCGM->setTitle              ( 'Selecione o tipo de filtro para o contrato do Servidor' );
$obRdoCGM->setRotulo             ( 'Opções'                                                 );
$obRdoCGM->setLabel              ( 'CGM/Matrícula'                                          );
$obRdoCGM->setValue              ( 'G'                                                      );
$obRdoCGM->setChecked            ( false                                                    );
$obRdoCGM->obEvento->setOnChange ( "buscaValor('montaSpanCGMContrato')"                     );

$obspnFiltroServidor = new Span;
$obspnFiltroServidor->setID ( 'spnFiltro' );

$obForm = new Form;
$obForm->setAction ($pgForm );

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval" );
$obHdnEval->setValue ( "" );

//Definição do Formulário
$obFormulario = new Formulario;
$obFormulario->addHidden         ( $obHdnEval,true                   );
$obFormulario->addForm           ( $obForm                           );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->setLarguraRotulo  ( 25                                );
$obFormulario->addHidden         ( $obHdnCtrl                        );
$obFormulario->addTitulo         ( 'Dados do Servidor'               );
$obFormulario->AgrupaComponentes ( array($obRdoContrato, $obRdoCGM ) );
$obFormulario->AddSpan           ( $obspnFiltroServidor              );

// mostrando o filtro por Contrato
    $obBtnLimparCampos = new Button;
    $obBtnLimparCampos->setName                    ( "btnLimparCampos"              );
    $obBtnLimparCampos->setValue                   ( "Limpar"                );
    $obBtnLimparCampos->setTipo                    ( "button"                );
    $obBtnLimparCampos->obEvento->setOnClick       ( "buscaValor('limparCampos');" );
    $obBtnLimparCampos->setDisabled                ( false                   );

    $obBtnOK = new Ok;
    $botoesForm  = array ( $obBtnOK , $obBtnLimparCampos );

    $obFormulario->defineBarra($botoesForm);

$obFormulario->show();

montaSpanContrato( true );

?>
