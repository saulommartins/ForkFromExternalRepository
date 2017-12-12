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
    * Página de filtro para o relatório de corretagem
    * Data de Criação   : 30/03/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: FLCorretagem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.21
*/

/*
$Log$
Revision 1.7  2006/09/18 10:31:34  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretagem.class.php"  );

$obRCIMCorretagem  = new RCIMCorretagem;
Sessao::remove('sessao_transf5');

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
$obHdnCaminho->setValue( CAM_GT_CIM_INSTANCIAS."relatorios/OCCorretagem.php" );

$sessao->nomFiltro['tipo_corretagem']['imobiliaria'] = "Imobiliária";
$sessao->nomFiltro['tipo_corretagem']['corretor']    = "Corretor";
$sessao->nomFiltro['tipo_corretagem']['todos']       = "Todos";

$obCmbTipo = new Select;
$obCmbTipo->setName       ( "stTipoCorretagem"            );
$obCmbTipo->setId         ( "stTipoCorretagem"            );
$obCmbTipo->setRotulo     ( "Tipo de Corretagem"          );
$obCmbTipo->addOption     ( "", "Selecione"               );
$obCmbTipo->addOption     ( "imobiliaria" , "Imobiliária" );
$obCmbTipo->addOption     ( "corretor"    , "Corretor"    );
$obCmbTipo->addOption     ( "todos"       , "Todos"       );
$obCmbTipo->setCampoDesc  ( "stTipoCorretagem"            );
$obCmbTipo->setNull       ( false                         );
$obCmbTipo->setStyle      ( "width: 200px"                );

$obTxtNomCGM = new TextBox;
$obTxtNomCGM->setName      ( "stNomCGM"  );
$obTxtNomCGM->setRotulo    ( "Nome"      );
$obTxtNomCGM->setSize      ( "80" ) ;
$obTxtNomCGM->setMaxLength ( "80" ) ;

$obCGMInicio = new TextBox;
$obCGMInicio ->setName  ( "inCGMInicio" );
$obCGMInicio ->setRotulo( "CGM" );
$obCGMInicio ->setTitle ( "Informe um período" ) ;
$obCGMInicio->setInteiro( true );

$obLblPeriodo = new Label;
$obLblPeriodo->setValue( " até " );

$obCGMTermino = new TextBox;
$obCGMTermino->setName     ( "inCGMTermino" );
$obCGMTermino->setRotulo   ( "CGM" );
$obCGMTermino->setTitle    ( "Informe um período" );
$obCGMTermino->setInteiro  ( true );

$sessao->nomFiltro['ordenacao']['cgm']  = "CGM";
$sessao->nomFiltro['ordenacao']['resp'] = "Responsável";

$obCmbOrder = new Select;
$obCmbOrder->setName      ( "stOrder"          );
$obCmbOrder->setRotulo    ( "Ordenação"        );
$obCmbOrder->setTitle     ( "Escolha a ordenação do relatório" );
$obCmbOrder->addOption    ( "", "Selecione"                    );
$obCmbOrder->addOption    ( "cgm"  , "CGM"         );
$obCmbOrder->addOption    ( "resp" , "Responsável" );
$obCmbOrder->setCampoDesc ( "stOrder"          );
$obCmbOrder->setNull      ( false              );
$obCmbOrder->setStyle     ( "width: 200px"     );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-05.01.21" );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addHidden( $obHdnCtrl    );
$obFormulario->addTitulo( "Dados para filtro" );
$obFormulario->addComponente( $obCmbTipo   );
$obFormulario->addComponente( $obTxtNomCGM );
$obFormulario->agrupaComponentes( array( $obCGMInicio, $obLblPeriodo ,$obCGMTermino) );
$obFormulario->addComponente( $obCmbOrder  );
$obFormulario->OK();
$obFormulario->setFormFocus( $obCmbTipo->getId() );
$obFormulario->show();
?>
