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
    * Página de Formulário para o cadastro de imóvel
    * Data de Criação   : 30/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FMManterImovel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.9  2006/09/18 10:30:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LSBuscaLote.php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}
Sessao::remove('link');
Sessao::remove('stLink');

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );

$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraLote = $obRCIMConfiguracao->getMascaraLote();

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setCadastroLocalizacao( false );
$obMontaLocalizacao->setPopUp( true );
$obMontaLocalizacao->setObrigatorio( false );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obTxtNumeroLote = new TextBox;
$obTxtNumeroLote->setName      ( "stNumeroLote"           );
$obTxtNumeroLote->setId        ( "stNumeroLote"           );
$obTxtNumeroLote->setMaxLength ( strlen( $stMascaraLote ) );
$obTxtNumeroLote->setSize      ( strlen( $stMascaraLote ) );
$obTxtNumeroLote->setRotulo    ( "Número do Lote"         );
$obTxtNumeroLote->setTitle     ( "Informe o número do lote" );
$stNumeroLote = (isset($stNumeroLote)) ? $stNumeroLote : '';
$obTxtNumeroLote->setValue     ( $stNumeroLote            );
$obTxtNumeroLote->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLote."', this, event);" );

$obRdoTipoUrbano = new Radio;
$obRdoTipoUrbano->setRotulo  ( "Tipo"   );
$obRdoTipoUrbano->setValue   ( "urbano" );
$obRdoTipoUrbano->setName    ( "stTipo" );
$obRdoTipoUrbano->setLabel   ( "Urbano" );
$obRdoTipoUrbano->setTitle   ( "Informe o tipo do imóvel" );
if ( isset($stTipo) && ($stTipo == "Urbano" or $stTipo == "") ) {
    $obRdoTipoUrbano->setChecked ( true     );
}

$obRdoTipoRural = new Radio;
$obRdoTipoRural->setRotulo  ( "Tipo"   );
$obRdoTipoRural->setValue   ( "rural"  );
$obRdoTipoRural->setName    ( "stTipo" );
$obRdoTipoRural->setLabel   ( "Rural"  );
$obRdoTipoRural->setTitle   ( "Informe o tipo do imóvel" );
if ( isset($stTipo) && $stTipo == "Rural" ) {
    $obRdoTipoRural->setChecked ( true     );
} else {
    $obRdoTipoUrbano->setChecked ( true    );
}

$obBtnOK = new OK;

$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick( "limparFiltro()" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm               );
$obFormulario->setAjuda ( "UC-05.01.09" );
$obFormulario->addHidden            ( $obHdnCtrl            );
$obFormulario->addHidden            ( $obHdnAcao            );
$obFormulario->addTitulo            ( "Dados para filtro"   );
$obFormulario->addComponente        ( $obTxtNumeroLote      );
$obFormulario->agrupaComponentes    ( array( $obRdoTipoUrbano, $obRdoTipoRural ) );
//$obMontaLocalizacao->geraFormulario ( $obFormulario, false  );
$obMontaLocalizacao->geraFormulario ( $obFormulario         );
$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
$obFormulario->setFormFocus( $obTxtNumeroLote->getId() );
$obFormulario->show();
?>
