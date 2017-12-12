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
 * Página de filtro para o cadastro de construção
 * Data de Criação   : 10/01/2005

 * @author Analista: Ricardo Lopes de Alencar
 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
 * @author Desenvolvedor: Fábio Bertoldi Rodrigues

 * @ignore

 * $Id: FLManterConstrucao.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.12
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMLote.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConstrucao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once $pgJs;

$pgProx = $pgList;

Sessao::remove('link');

$stAcao = $request->get("stAcao");

if (empty($stAcao)) {
    $stAcao = "incluir";
}

$obRCIMConstrucao = new RCIMConstrucaoOutros;

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );

$obRCIMImovel = new RCIMImovel( new RCIMLote );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$obRCIMImovel->obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMImovel->obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$stMascaraIM = $obRCIMImovel->obRCIMConfiguracao->getMascaraIM();
$obRCIMImovel->obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnLimpaFiltro = new Hidden;
$obHdnLimpaFiltro->setName( "boLimpaFiltro" );
$obHdnLimpaFiltro->setValue( "true" );

$obTxtCodigoConstrucao = new TextBox;
$obTxtCodigoConstrucao->setName      ( "inCodigoConstrucao" );
$obTxtCodigoConstrucao->setId        ( "inCodigoConstrucao" );
$obTxtCodigoConstrucao->setRotulo    ( "Código" );
$obTxtCodigoConstrucao->setInteiro   ( true );
$obTxtCodigoConstrucao->setSize      ( 10 );
$obTxtCodigoConstrucao->setMaxLength ( 10 );

$obRadioVinculoImovel = new Radio;
$obRadioVinculoImovel->setName         ( "boVinculoConstrucao"   );
$obRadioVinculoImovel->setTitle        ( "Vínculo da Construção" );
$obRadioVinculoImovel->setRotulo       ( "Vínculo"               );
$obRadioVinculoImovel->setValue        ( "Imóvel"                );
$obRadioVinculoImovel->setLabel        ( "Imóvel"                );
$obRadioVinculoImovel->setNull         ( false                   );
$obRadioVinculoImovel->setChecked      ( true                    );
$obRadioVinculoImovel->obEvento->setOnChange ( "habilitaSpnImovelCond();"    );

$obRadioVinculoCondominio = new Radio;
$obRadioVinculoCondominio->setName     ( "boVinculoConstrucao" );
$obRadioVinculoCondominio->setValue    ( "Condomínio"          );
$obRadioVinculoCondominio->setLabel    ( "Condomínio"          );
$obRadioVinculoCondominio->setNull     ( false                 );
$obRadioVinculoCondominio->obEvento->setOnChange ( "habilitaSpnImovelCond();");

$obSpnImovelCond = new Span;
$obSpnImovelCond->setID("spnImovelCond");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProx );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                  );
$obFormulario->setAjuda      ( "UC-05.01.12" );
$obFormulario->addHidden     ( $obHdnCtrl               );
$obFormulario->addHidden     ( $obHdnAcao               );
$obFormulario->addHidden     ( $obHdnLimpaFiltro        );
$obFormulario->addTitulo     ( "Dados para filtro"      );
$obFormulario->addComponente ( $obTxtCodigoConstrucao   );
$obFormulario->addComponenteComposto( $obRadioVinculoImovel,$obRadioVinculoCondominio);
$obFormulario->addSpan       ( $obSpnImovelCond         );
$obFormulario->OK();
$obFormulario->setFormFocus( $obTxtCodigoConstrucao->getId() );
$obFormulario->show();

SistemaLegado::executaFrameoculto( "habilitaSpnImovelCond();" );

?>
