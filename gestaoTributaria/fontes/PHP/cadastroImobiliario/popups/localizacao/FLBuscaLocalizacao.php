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
    * Página de Listagem para PopUp de Localização
    * Data de Criação   : 14/02/2006

    * @author Analista: Fabio Bertold Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: FLBuscaLocalizacao.php 63781 2015-10-09 20:50:07Z arthur $

    * Casos de uso: uc-05.01.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacaoCombos.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php" );

$stPrograma = "BuscaLocalizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
Sessao::remove('link');
Sessao::remove('stLink');

$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $request->get('stAcao') );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $request->get('nomForm') );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $request->get('campoNum') );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $request->get('campoNom') );

$obRCIMNivel = new RCIMNivel;
$obRCIMNivel->recuperaVigenciaAtual( $rsVigenciaAtual );
$inCodigoVigencia = $rsVigenciaAtual->getCampo("cod_vigencia");

$obHdnCodigoVigencia = new Hidden;
$obHdnCodigoVigencia->setName( "inCodigoVigencia" );
$obHdnCodigoVigencia->setValue( $inCodigoVigencia );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "tipoBusca" );
$obHdnTipoBusca->setValue( $request->get('tipoBusca') );

$obTxtNome = new TextBox;
$obTxtNome->setName      ( "stNome" );
$obTxtNome->setRotulo    ( "Nome" );
$obTxtNome->setTitle     ( "Nome da Localização" );
$obTxtNome->setSize      ( 80 );
$obTxtNome->setMaxLength ( 100 );
$obTxtNome->setNull      ( true );

$obMontaLocalizacaoCombos = new MontaLocalizacaoCombos;
$obMontaLocalizacaoCombos->boPopUp = true;
$obMontaLocalizacaoCombos->setObrigatorio        ( false );
$obMontaLocalizacaoCombos->setCadastroLocalizacao( false );

$inCodigoNivel = Sessao::read('inCodigoNivel');
if ($inCodigoNivel) {
    $obMontaLocalizacaoCombos->setCadastroLocalizacao( true );
    $obMontaLocalizacaoCombos->setCodigoNivel        ( $inCodigoNivel );
}

$obIFrame = new IFrame;
$obIFrame->setName("oculto");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("0");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo            ( "Dados para Filtro" );
$obFormulario->addForm              ( $obForm       );
$obFormulario->addHidden            ( $obHdnAcao    );
$obFormulario->addHidden            ( $obHdnCtrl    );
$obFormulario->addHidden            ( $obHdnForm    );
$obFormulario->addHidden            ( $obHdnCampoNum    );
$obFormulario->addHidden            ( $obHdnCampoNom    );
$obFormulario->addHidden            ( $obHdnTipoBusca   );
$obFormulario->addHidden            ( $obHdnCodigoVigencia );
$obFormulario->addComponente        ( $obTxtNome        );
$obMontaLocalizacaoCombos->geraFormulario ( $obFormulario   );
$obFormulario->OK();
$obFormulario->show();
$obIFrame->show();

?>