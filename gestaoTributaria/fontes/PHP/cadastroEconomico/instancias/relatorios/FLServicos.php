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
    * Página de Filtro para relatorico de Contas
    * Data de Criação   : 02/05/2005

    * @author Analista Fabio Bertoldi
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    * $Id: FLServicos.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.16

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

Sessao::write( "sessao_transf5", "" );
//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

include_once 'JSServicos.js';

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_CEM_INSTANCIAS."relatorios/OCServicos.php" );

$obTxtNomServico = new TextBox;
$obTxtNomServico->setName  ( "stNomServico" );
$obTxtNomServico->setRotulo( "Nome do Serviço" );
$obTxtNomServico->setTitle ( "" ) ;
$obTxtNomServico->setSize  ( "80" ) ;
$obTxtNomServico->setMaxLength  ( 240 ) ;
$obTxtNomServico->setInteiro( false );

$obCodInicio = new TextBox;
$obCodInicio->setName  ( "inCodInicio" );
$obCodInicio->setRotulo( "Serviço" );
$obCodInicio->setTitle ( "Informe um período" ) ;
$obCodInicio->setInteiro( false );
$obCodInicio->obEvento->setOnKeyPress( "return validar(event)" );

$obLblPeriodo = new Label;
$obLblPeriodo->setValue( " até " );

$obCodTermino = new TextBox;
$obCodTermino->setName     ( "inCodTermino" );
$obCodTermino->setRotulo   ( "Serviço" );
$obCodTermino->setTitle    ( "Informe um período" );
$obCodTermino->setInteiro  ( false );
$obCodTermino->obEvento->setOnKeyPress( "return validar(event)" );

$obCodInicioVigencia = new Data;
$obCodInicioVigencia->setName  ( "inCodInicioVigencia" );
$obCodInicioVigencia->setRotulo( "Vigência" );
$obCodInicioVigencia->setTitle ( "Informe um período" ) ;

$obLblPeriodoVigencia = new Label;
$obLblPeriodoVigencia->setValue( " até " );

$obCodTerminoVigencia = new Data;
$obCodTerminoVigencia->setName     ( "inCodTerminoVigencia" );
$obCodTerminoVigencia->setRotulo   ( "Vigência" );
$obCodTerminoVigencia->setTitle    ( "Informe um período" );

$obCmbOrder = new Select;
$obCmbOrder->setName      ( "stOrder" );
$obCmbOrder->setRotulo    ( "Ordenação" );
$obCmbOrder->setTitle     ( "Selecione a ordenação do relatório" );
$obCmbOrder->addOption    ( "", "Selecione" );
$obCmbOrder->addOption    ( "codigo", "Código Serviço" );
$obCmbOrder->addOption    ( "descricao", "Descrição Serviço" );
$obCmbOrder->setCampoDesc ( "stOrder" );
$obCmbOrder->setNull      ( false );
$obCmbOrder->setStyle     ( "width: 200px" );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda("UC-05.02.16");
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Dados para filtro" );
$obFormulario->addComponente( $obTxtNomServico ) ;
$obFormulario->agrupaComponentes( array( $obCodInicio, $obLblPeriodo, $obCodTermino ) );
$obFormulario->agrupaComponentes( array( $obCodInicioVigencia, $obLblPeriodoVigencia, $obCodTerminoVigencia ) );
$obFormulario->addComponente( $obCmbOrder );
$obFormulario->OK();
$obFormulario->show();

?>
