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
    * Página de formulário para alteração de características de construção
    * Data de Criação   : 10/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Leusin Oigen

    * @ignore

    * $Id: FMManterConstrucaoCaracteristica.php 63279 2015-08-12 13:11:09Z arthur $

    * Casos de uso: uc-05.01.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"           );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"             );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConstrucao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$stLink = Sessao::read('stLink');
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$obRCIMConstrucao = new RCIMConstrucaoOutros;
$obRCIMImovel = new RCIMImovel( new RCIMLote );

$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$obRCIMImovel->obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMImovel->obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$stMascaraIM = $obRCIMImovel->obRCIMConfiguracao->getMascaraIM();
$obRCIMImovel->obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$obRCIMConstrucao->setCodigoConstrucao( $_REQUEST["inCodigoConstrucao"] );
$obRCIMConstrucao->consultarConstrucao();
if ( $obRCIMConstrucao->obRProcesso->getCodigoProcesso() ) {
    $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $stMascaraProcesso );
    $stProcesso = str_pad( $obRCIMConstrucao->obRProcesso->getCodigoProcesso() , strlen( $arProcesso[0] ), "0", STR_PAD_LEFT );
    $stSeparador = preg_replace( "/[a-zA-Z0-9]/","", $stMascaraProcesso );
    $stProcesso .= $stSeparador.$obRCIMConstrucao->obRProcesso->getExercicio();
}

$obRCIMConstrucao->listarProcessos( $rsListaProcesso );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCodigoConstrucao = new Hidden;
$obHdnCodigoConstrucao->setName  ( "inCodigoConstrucao" );
$obHdnCodigoConstrucao->setValue ( $obRCIMConstrucao->getCodigoConstrucao() );

$obHdnNumeroInscricao = new Hidden;
$obHdnNumeroInscricao->setName  ( "inNumeroInscricao" );
$obHdnNumeroInscricao->setValue ( $_REQUEST["inNumeroInscricao"] );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

include_once 'FMManterConstrucaoCaracteristicaAbaCaracteristica.php';
include_once 'FMManterConstrucaoCaracteristicaAbaProcesso.php';

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm            ( $obForm                   );
$obFormulario->setAjuda             ( "UC-05.01.12" );
$obFormulario->addHidden          ( $obHdnCtrl                );
$obFormulario->addHidden          ( $obHdnAcao                );
$obFormulario->addHidden          ( $obHdnCodigoConstrucao    );
$obFormulario->addHidden          ( $obHdnNumeroInscricao     );

$obFormulario->addAba       ( "Características"     );
$obFormulario->addTitulo    ( "Dados para Construção"   );
$obFormulario->addComponente      ( $obLblCodigoConstrucao    );
if ($_REQUEST["boVinculoConstrucao"] == "Condomínio") {
    $obFormulario->addComponente      ( $obLblNomeCondominio      );
} else {
    $obFormulario->addComponente      ( $obLblNumeroInscricao     );
}
$obFormulario->addComponente      ( $obLblDescricaoConstrucao );
$obFormulario->addComponente( $obBscProcesso        );
$obMontaAtributos->geraFormulario ( $obFormulario );

$obFormulario->addAba       ( "Processos" );
$obFormulario->addTitulo    ( "Dados para Construção"  );
$obFormulario->addComponente      ( $obLblCodigoConstrucao    );
if ($_REQUEST["boVinculoConstrucao"] == "Condomínio") {
    $obFormulario->addComponente      ( $obLblNomeCondominio      );
} else {
    $obFormulario->addComponente      ( $obLblNumeroInscricao     );
}
$obFormulario->addComponente      ( $obLblDescricaoConstrucao );
$obFormulario->addSpan      ( $obSpnProcesso        );
$obFormulario->addSpan      ( $obSpnAtributosProcesso );

$obFormulario->Cancelar( $pgList."?".$stLink );
$obFormulario->setFormFocus( $obBscProcesso->obCampoCod->getId() );
$obFormulario->show();

?>