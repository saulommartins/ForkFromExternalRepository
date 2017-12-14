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
    * Data de Criação: 27/04/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: andre.almeida $
    $Date: 2007-05-07 16:41:15 -0300 (Seg, 07 Mai 2007) $

    * Casos de uso: uc-02.08.18
*/

/*
$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAssentamento.class.php"  );
include_once( CAM_GF_EXP_MAPEAMENTO."TExportacaoTCESCMotivoLicencaEsfinge.class.php"  );

$obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento;
$obTPessoalAssentamentoAssentamento->recuperaTodos( $rsMotivoLicencaUrbem );

$obTExportacaoTCESCMotivoLicencaEsfinge = new TExportacaoTCESCMotivoLicencaEsfinge;
$obTExportacaoTCESCMotivoLicencaEsfinge->recuperaTodos( $rsMotivoLicencaEsfinge );

//Define o nome dos arquivos PHP
$stPrograma = "ConfigurarMotivoLicencaAfastamento";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

$jsOnload   = "executaFuncaoAjax( 'configuracoesIniciais' );";

$sessao->transf['configuracoesMotivosLicencas'] = array();

$obCmbMotivoLicencaEsfinge = new Select;
$obCmbMotivoLicencaEsfinge->setName      ( 'inMotivoLicensaEsfinge' );
$obCmbMotivoLicencaEsfinge->setId        ( 'inMotivoLicensaEsfinge' );
$obCmbMotivoLicencaEsfinge->setRotulo    ( 'Motivo da Licença Temporária do e-Sfinge' );
$obCmbMotivoLicencaEsfinge->setTitle     ( 'Selecione o Motivo da Licença Temporária do e-Sfinge.' );
$obCmbMotivoLicencaEsfinge->addOption    ( "", "Selecione" );
$obCmbMotivoLicencaEsfinge->setCampoID   ( "cod_motivo_licenca_esfinge" );
$obCmbMotivoLicencaEsfinge->setCampoDesc ( "descricao" );
$obCmbMotivoLicencaEsfinge->preencheCombo( $rsMotivoLicencaEsfinge );
$obCmbMotivoLicencaEsfinge->setObrigatorioBarra( true );

$obCmbMotivoLicencaUrbem = new Select;
$obCmbMotivoLicencaUrbem->setName      ( 'inMotivoLicensaUrbem' );
$obCmbMotivoLicencaUrbem->setId        ( 'inMotivoLicensaUrbem' );
$obCmbMotivoLicencaUrbem->setRotulo    ( 'Motivo da Licença Temporária do Urbem' );
$obCmbMotivoLicencaUrbem->setTitle     ( 'Selecione Motivo da Licença Temporária do Urbem.' );
$obCmbMotivoLicencaUrbem->addOption    ( "", "Selecione" );
$obCmbMotivoLicencaUrbem->setCampoID   ( "cod_assentamento" );
$obCmbMotivoLicencaUrbem->setCampoDesc ( "descricao" );
$obCmbMotivoLicencaUrbem->preencheCombo( $rsMotivoLicencaUrbem );
$obCmbMotivoLicencaUrbem->setObrigatorioBarra( true );

$obSpnListaMotivoLicenca = new Span;
$obSpnListaMotivoLicenca->setId    ( "spnListaMotivosLicensas" );
$obSpnListaMotivoLicenca->setValue ( ""                      );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( 'Configuração do Motivo da Licença de Afastamento' );
$obFormulario->addComponente( $obCmbMotivoLicencaEsfinge );
$obFormulario->addComponente( $obCmbMotivoLicencaUrbem );
$obFormulario->incluir( 'ConfiguracaoMotivosLicencas', array( $obCmbMotivoLicencaEsfinge, $obCmbMotivoLicencaUrbem ) );
$obFormulario->addSpan( $obSpnListaMotivoLicenca );

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
