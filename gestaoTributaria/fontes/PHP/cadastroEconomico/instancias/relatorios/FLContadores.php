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
    * Data de Criação   : 28/04/2005

    * @author Analista Fabio Bertoldi
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    * $Id: FLContadores.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.14

*/

/*
$Log$
Revision 1.9  2006/09/15 14:33:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

Sessao::write( "sessao_transf5", "" );
//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

$obForm = new Form;
//$obForm->setAction( "../../../popups/popups/relatorio/OCRelatorio.php" );
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
//$obHdnCaminho->setValue( "../../../modulos/cadastroEconomico/relatorios/OCContadores.php" );
$obHdnCaminho->setValue( CAM_GT_CEM_INSTANCIAS."relatorios/OCContadores.php" );

$obTxtNomContador = new TextBox;
$obTxtNomContador->setName  ( "stNomCGM" );
$obTxtNomContador->setRotulo( "Nome" );
$obTxtNomContador->setTitle ( "" ) ;
$obTxtNomContador->setSize  ( "80" ) ;
$obTxtNomContador->setInteiro( false );

$obCodInicio = new TextBox;
$obCodInicio ->setName  ( "inCodInicio" );
$obCodInicio ->setRotulo( "CGM" );
$obCodInicio ->setTitle ( "Informe um período" ) ;
$obCodInicio->setInteiro( true );

$obLblPeriodo = new Label;
$obLblPeriodo->setValue( " até " );

$obCodTermino = new TextBox;
$obCodTermino->setName     ( "inCodTermino" );
$obCodTermino->setRotulo   ( "CGM" );
$obCodTermino->setTitle    ( "Informe um período" );
$obCodTermino->setInteiro  ( true );

$obCodInicioCadEconomico = new TextBox;
$obCodInicioCadEconomico ->setName  ( "inCodInicioCadEconomico" );
$obCodInicioCadEconomico ->setRotulo( "Cadastro Econômico" );
$obCodInicioCadEconomico ->setTitle ( "Informe um período" ) ;
$obCodInicioCadEconomico->setInteiro( true );

$obLblPeriodoCadEconomico = new Label;
$obLblPeriodoCadEconomico->setValue( " até " );

$obCodTerminoCadEconomico = new TextBox;
$obCodTerminoCadEconomico->setName     ( "inCodTerminoCadEconomico" );
$obCodTerminoCadEconomico->setRotulo   ( "Cadastro Econômico" );
$obCodTerminoCadEconomico->setTitle    ( "Informe um período" );
$obCodTerminoCadEconomico->setInteiro  ( true );

$obCmbTipo = new Select;
$obCmbTipo->setName      ( "stTipoRelatorio"                );
$obCmbTipo->setRotulo    ( "Tipo de Relatório"              );
$obCmbTipo->setTitle     ( "Selecione o tipo de relatório"  );
$obCmbTipo->addOption    ( ""          , "Selecione"        );
$obCmbTipo->addOption    ( "analitico" , "Analítico"        );
$obCmbTipo->addOption    ( "sintetico" , "Sintético"        );
$obCmbTipo->setCampoDesc ( "stTipo"                         );
$obCmbTipo->setNull      ( false                            );
$obCmbTipo->setStyle     ( "width: 200px"                   );

$obCmbOrder = new Select;
$obCmbOrder->setName      ( "stOrder"                            );
$obCmbOrder->setRotulo    ( "Ordenação"                          );
$obCmbOrder->setTitle     ( "Selecione a ordenação do relatório" );
$obCmbOrder->addOption    ( ""              , "Selecione"        );
$obCmbOrder->addOption    ( "codigo"        , "Código CGM"       );
$obCmbOrder->addOption    ( "nome" , "Nome do Contador"          );
$obCmbOrder->setCampoDesc ( "stOrder"          );
$obCmbOrder->setNull      ( false              );
$obCmbOrder->setStyle     ( "width: 200px"     );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda("UC-05.02.14");
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addHidden( $obHdnCtrl    );
$obFormulario->addTitulo( "Dados para filtro" );
$obFormulario->addComponente( $obTxtNomContador ) ;
$obFormulario->agrupaComponentes( array( $obCodInicio, $obLblPeriodo ,$obCodTermino) );
$obFormulario->agrupaComponentes( array( $obCodInicioCadEconomico, $obLblPeriodoCadEconomico, $obCodTerminoCadEconomico) );
$obFormulario->addComponente( $obCmbOrder );
$obFormulario->addComponente( $obCmbTipo );
$obFormulario->OK();
$obFormulario->show();

?>
