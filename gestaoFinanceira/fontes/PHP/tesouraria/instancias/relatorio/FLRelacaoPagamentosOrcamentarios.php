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
    * Página de Formulario de Filtro do Relatório de Pagamentos Orçamentários
    * Data de Criação   : 31/07/2007

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30835 $
    $Name$
    $Author: tonismar $
    $Date: 2007-08-08 11:19:36 -0300 (Qua, 08 Ago 2007) $

    * Casos de uso: uc-02.04.35
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."IIntervaloPopUpDotacao.class.php" );

// componente da tag form
$obForm = new Form;
$obForm->setAction( "OCGeraRelacaoPagamentosOrcamentarios.php" );
$obForm->setTarget( "telaPrincipal" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_TES_INSTANCIAS."relatorio/OCRelacaoPagamentosOrcamentarios.php" );

// componente de seleção de período
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setNull           ( false             );

// popup de busca de fornecedor
$obIpopUpCgm = new IPopUpCGMVinculado( $obForm                 );
$obIpopUpCgm->setTabelaVinculo       ( 'empenho.pre_empenho'   );
$obIpopUpCgm->setCampoVinculo        ( 'cgm_beneficiario'      );
$obIpopUpCgm->setNomeVinculo         ( 'Fornecedor'            );
$obIpopUpCgm->setRotulo              ( 'Fornecedor'            );
$obIpopUpCgm->setTitle               ( 'Informe o fornecedor.' );
$obIpopUpCgm->setName                ( 'stNomCGM'              );
$obIpopUpCgm->setId                  ( 'stNomCGM'              );
$obIpopUpCgm->obCampoCod->setName    ( 'inCGM'                 );
$obIpopUpCgm->obCampoCod->setId      ( 'inCGM'                 );
$obIpopUpCgm->obCampoCod->setNull    ( true                    );
$obIpopUpCgm->setNull                ( true                    );

// intervalo de número de empenho
$obTxtEmpenhoInicio = new TextBox();
$obTxtEmpenhoInicio->setName  ("inCodEmpenhoInicial");
$obTxtEmpenhoInicio->setRotulo("Número do Empenho");
$obTxtEmpenhoInicio->setTitle ("Informe a faixa de números de empenho para o filtro.");

$obLblEmpenho = new Label();
$obLblEmpenho->setValue(" a ");

$obTxtEmpenhoFinal = new TextBox();
$obTxtEmpenhoFinal->setName("inCodEmpenhoFinal");
$obTxtEmpenhoFinal->setRotulo("Número do Empenho");

// Define intervalo de codigo estrutural de contas
$obIIntervaloPopUpDotacao = new IIntervaloPopUpDotacao();

// FORMULÁRIO \\
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
    $obFormulario->addHidden    ( $obHdnCaminho );
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obPeriodicidade    );
$obFormulario->addComponente( $obIpopUpCgm        );
$obFormulario->agrupaComponentes( array( $obTxtEmpenhoInicio, $obLblEmpenho ,$obTxtEmpenhoFinal) );
$obFormulario->addComponente( $obIIntervaloPopUpDotacao );
$obFormulario->Ok();
$obFormulario->show();
?>
