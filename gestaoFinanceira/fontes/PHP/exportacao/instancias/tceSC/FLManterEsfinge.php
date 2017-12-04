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
    * Página de Formulario de Manter Configuração e-Sfinge
    * Data de Criação: 27/02/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: andre.almeida $
    $Date: 2007-03-05 15:47:03 -0300 (Seg, 05 Mar 2007) $

    * Casos de uso: uc-02.08.17
*/

/*
$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once( CAM_GF_EXP_MAPEAMENTO."TExportacaoTCESCTipoCertidao.class.php"  );
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoDocumento.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEsfinge";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

$jsOnload   = "executaFuncaoAjax( 'configuracoesIniciais' );";

$sessao->transf['configuracoesCertidoes'] = array();

//Consultas para preencher os Selects
$obTExportacaoTCESCTipoCertidao = new TExportacaoTCESCTipoCertidao;
$obTExportacaoTCESCTipoCertidao->recuperaTipoCertidao( $rsTipoCertidaoEsfinge );

$obTLicitacaoDocumento = new TLicitacaoDocumento;
$obTLicitacaoDocumento->recuperaDocumentos( $rsTipoCertidaoUrbem, "", "order by nom_documento" );

$obCmbTipoEsfinge = new Select;
$obCmbTipoEsfinge->setName      ( 'inTipoEsfinge' );
$obCmbTipoEsfinge->setId        ( 'inTipoEsfinge' );
$obCmbTipoEsfinge->setRotulo    ( 'Tipo de Certidão do e-Sfinge' );
$obCmbTipoEsfinge->setTitle     ( 'Selecione o Tipo de Certidão do e-Sfinge.' );
$obCmbTipoEsfinge->addOption    ( "", "Selecione" );
$obCmbTipoEsfinge->setCampoID   ( "cod_tipo_certidao" );
$obCmbTipoEsfinge->setCampoDesc ( "descricao" );
$obCmbTipoEsfinge->preencheCombo( $rsTipoCertidaoEsfinge );
$obCmbTipoEsfinge->setObrigatorioBarra( true );

$obCmbTipoUrbem = new Select;
$obCmbTipoUrbem->setName      ( 'inTipoUrbem' );
$obCmbTipoUrbem->setId        ( 'inTipoUrbem' );
$obCmbTipoUrbem->setRotulo    ( 'Tipo de Certidão do Urbem' );
$obCmbTipoUrbem->addOption    ( "", "Selecione" );
$obCmbTipoUrbem->setCampoID   ( "cod_documento" );
$obCmbTipoUrbem->setCampoDesc ( "nom_documento" );
$obCmbTipoUrbem->preencheCombo( $rsTipoCertidaoUrbem );
$obCmbTipoUrbem->setObrigatorioBarra( true );

$obSpnListaCertidoes = new Span;
$obSpnListaCertidoes->setId    ( "spnListaCertidoes" );
$obSpnListaCertidoes->setValue ( ""                );

$obForm = new Form;
$obForm->setAction( $pgProc );
// $obForm->setTarget( "telaPrincipal" );
$obForm->setTarget( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( 'Configuração de Tipo de Certidão' );
$obFormulario->addComponente( $obCmbTipoEsfinge );
$obFormulario->addComponente( $obCmbTipoUrbem );
$obFormulario->incluir( 'ConfiguracaoCertidao', array( $obCmbTipoEsfinge, $obCmbTipoUrbem ) );
$obFormulario->addSpan( $obSpnListaCertidoes );

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "Limpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo( "Reset" );
$obBtnLimpar->obEvento->setOnClick( "executaFuncaoAjax('configuracoesIniciais')" );

$obFormulario->defineBarra( array ( $obBtnOk , $obBtnLimpar ),"","" );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
// include_once( $pgJs );
?>
