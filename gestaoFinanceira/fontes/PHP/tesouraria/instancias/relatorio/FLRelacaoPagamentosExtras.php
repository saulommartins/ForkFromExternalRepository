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
    * PÃ¡gina de Filtro para RelatÃ³rio de Pagamentos Extras
    * Data de CriaÃ§Ã£o   : 10/08/2007
    *
    * @author Analista: Gelson
    * @author Desenvolvedor: Tonismar RÃ©gis Bernardo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: tonismar $
    $Date: 2007-08-28 16:50:04 -0300 (Ter, 28 Ago 2007) $

    * Casos de uso: uc-02.04.38

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_COMPONENTES."IPopUpContaAnalitica.class.php");
include_once( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php");

$obForm = new Form;
$obForm->setAction( 'OCGeraRelacaoPagamentosExtras.php' );
$obForm->setTarget( 'telaPrincipal' );

// Define Objeto Select Múltiplo de entidade
$obISelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario();

//Define objeto de Periodicidade
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setValidaExercicio( true );
$obPeriodicidade->setValue          ( 4                 );
$obPeriodicidade->setRotulo("Periodicidade Pagamento");
$obPeriodicidade->setTitle("Informe a periodicidade de emissão.");
$obPeriodicidade->setNull (false);

// Define Objeto BuscaInner da conta de despesa
$obBscContaDebito = new IPopUpContaAnalitica();
$obBscContaDebito->setRotulo                      ( "Conta de Despesa" );
$obBscContaDebito->setTitle                       ( "Informe a conta de despesa extra-orÃ§amentÃ¡ria vinculada a este recibo." );
$obBscContaDebito->setId                          ( "stNomContaDebito" );
$obBscContaDebito->setNull                        (  true             );
$obBscContaDebito->obCampoCod->setName            ( "inCodPlanoDebito" );
$obBscContaDebito->obCampoCod->setId              ( "inCodPlanoDebito" );
$obBscContaDebito->obImagem->setId                ( "imgPlanoDebito"   );
$obBscContaDebito->setTipoBusca                   ( "tes_pagamento_extra_despesa" );

// Define Objeto BuscaInner da conta para caixa/banco
$obBscContaCredito = new IPopUpContaAnalitica(  $obIEntidade->obSelect  );
$obBscContaCredito->setRotulo                      ( "Conta Caixa/Banco"    );
$obBscContaCredito->setTitle                       ( "Informe a conta Caixa/Banco onde foi efetuado o pagamento da despesa extra." );
$obBscContaCredito->setId                          ( "stNomContaCredito" );
$obBscContaCredito->setNull                        (  true              );
$obBscContaCredito->obCampoCod->setName            ( "inCodPlanoCredito" );
$obBscContaCredito->obCampoCod->setId              ( "inCodPlanoCredito" );
$obBscContaCredito->obImagem->setId                ( "imgPlanoCredito"   );
$obBscContaCredito->setTipoBusca                   ( "tes_pagamento_extra_caixa_banco"    );

// Monta formulÃ¡rio
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo    ( 'Relação Pagamentos Extras' );
$obFormulario->addComponente( $obISelectMultiploEntidadeUsuario );
$obFormulario->addComponente( $obPeriodicidade );
$obFormulario->addComponente( $obBscContaCredito          );
$obFormulario->addComponente( $obBscContaDebito           );
$obFormulario->Ok();
$obFormulario->Show();
?>
