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
    * Página de Formulário para o cadastro de alteração de características de imóvel
    * Data de Criação   : 10/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FMManterImovelCaracteristica.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.8  2006/09/18 10:30:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"           );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"       );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId().$stLink;
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );
include_once( $pgOcul );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "historico";
}

$obRCIMImovel = new RCIMImovel( new RCIMLote );
$obRCIMImovel->obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMImovel->obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$obRCIMImovel->roRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );
$obRCIMImovel->roRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST["inCodigoLocalizacao"] );
$obRCIMImovel->roRCIMLote->obRCIMLocalizacao->consultarLocalizacao();
$stLocalizacao = $obRCIMImovel->roRCIMLote->obRCIMLocalizacao->getValorComposto();
$obRCIMImovel->roRCIMLote->consultarLote();

$obRCIMImovel->setNumeroInscricao( $_REQUEST["inInscricaoMunicipal"] );
$obErro = $obRCIMImovel->consultarImovel();

$obRCIMImovel->obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$obRCIMImovel->listarProcessos( $rsListaProcesso );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnLote = new Hidden;
$obHdnLote->setName  ( "inCodigoLote" );
$obHdnLote->setValue ( $_REQUEST["inCodigoLote"] );

$obHdnSubLote = new Hidden;
$obHdnSubLote->setName  ( "inCodigoSubLote" );
$obHdnSubLote->setValue ( $_REQUEST["inCodigoSubLote"] );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

include_once 'FMManterImovelCaracteristicaAbaCaracteristica.php';
include_once 'FMManterImovelCaracteristicaAbaProcesso.php';

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm      ( $obForm               );
$obFormulario->setAjuda ( "UC-05.01.09" );
$obFormulario->addHidden    ( $obHdnAcao            );
$obFormulario->addHidden    ( $obHdnLote            );
$obFormulario->addHidden    ( $obHdnSubLote         );
$obFormulario->addHidden    ( $obHdnCtrl            );

$obFormulario->addAba       ( "Características"     );
$obFormulario->addTitulo    ( "Dados para Imóvel"   );
$obFormulario->addHidden    ( $obHdnNumeroInscricao );

$obFormulario->addComponente( $obLblLocalizacao     );
$obFormulario->addComponente( $obLblNumeroLote      );
$obFormulario->addComponente( $obLblNumeroInscricao );
$obFormulario->addComponente( $obBscProcesso        );
$obMontaAtributosImovel->geraFormulario ( $obFormulario );

$obFormulario->addAba       ( "Processos" );
$obFormulario->addTitulo    ( "Dados para Imóvel"  );
$obFormulario->addComponente( $obLblLocalizacao     );
$obFormulario->addComponente( $obLblNumeroLote      );
$obFormulario->addComponente( $obLblNumeroInscricao );
$obFormulario->addSpan      ( $obSpnProcesso        );
$obFormulario->addSpan      ( $obSpnAtributosProcesso );

$obFormulario->Cancelar( $pgList );
$obFormulario->show();

$js = "focusIncluir();";
SistemaLegado::executaFramePrincipal($js);

?>
