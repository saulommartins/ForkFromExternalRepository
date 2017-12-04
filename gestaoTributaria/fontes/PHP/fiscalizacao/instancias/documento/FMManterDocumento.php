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
    * Página de Formulario de Inclusao/Alteracao de Documentos

    * Data de Criação   : 17/07/2007

    * @author Analista      : Heleno Santos
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_FIS_COMPONENTES."ITextBoxSelectTipoFiscalizacao.class.php"                       );
include_once( CAM_GT_FIS_NEGOCIO."RFISDocumento.class.php"                                               );
include_once( CAM_GT_FISCALIZACAO."classes/visao/VFISManterDocumento.class.php"          );

$stAcao = $request->get('stAcao');
Sessao::write( 'arValores', array() );
if ( empty( $stAcao ) ) { $stAcao = "incluir"; }

//Define o nome dos arquivos PHP
$stPrograma = "ManterDocumento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $_GET['stCtrl']  );

$obTipoFiscalizacao = new ITextBoxSelectTipoFiscalizacao;
$obTipoFiscalizacao->setNull(false);

//Acao do Form
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obTxtNomDocumento = new TextBox;
$obTxtNomDocumento->setRotulo   ( 'Descrição do Documento'            );
$obTxtNomDocumento->setTitle    ( 'Informe a descrição do Documento'   );
$obTxtNomDocumento->setName     ( 'nom_documento'                      );
$obTxtNomDocumento->setSize     ( 50                                   );
$obTxtNomDocumento->setMaxLength( 80                                   );
$obTxtNomDocumento->setNull ( false                          );

$obRadioUsoInterno = new Radio;
$obRadioUsoInterno->setName   ( "boUsoInterno"                         );
$obRadioUsoInterno->setRotulo ( "Tipo do Documento"                    );
$obRadioUsoInterno->setTitle  ( "Informe tipo do documento"            );
$obRadioUsoInterno->setValue  ( "UsoInterno"                           );
$obRadioUsoInterno->setLabel  ( "Uso Interno"                          );
$obRadioUsoInterno->setNull   ( false                                  );
$obRadioUsoInterno->setChecked( true );

$obRadioSolicitaContribuinte = new Radio;
$obRadioSolicitaContribuinte->setName ( "boUsoInterno"                 );
$obRadioSolicitaContribuinte->setValue( "SolicitaContribuinte"         );
$obRadioSolicitaContribuinte->setLabel( "Solicitados ao Contribuinte"  );
$obRadioSolicitaContribuinte->setNull ( false                          );

$obRadioAtivoSim = new Radio;
$obRadioAtivoSim->setName   ( "boAtivo"                                  );
$obRadioAtivoSim->setRotulo ( "Ativo"                                    );
$obRadioAtivoSim->setTitle  ( "Informe se o documento está ativo ou não" );
$obRadioAtivoSim->setValue  ( "sim"                                      );
$obRadioAtivoSim->setLabel  ( "Sim"                                      );
$obRadioAtivoSim->setNull   ( false                                      );
$obRadioAtivoSim->setChecked  ( true                                     );

$obRadioAtivoNao = new Radio;
$obRadioAtivoNao->setName ( "boAtivo"                                    );
$obRadioAtivoNao->setValue( "nao"                                        );
$obRadioAtivoNao->setLabel( "Não"                                        );
$obRadioAtivoNao->setNull ( false                                        );

if ($stAcao=="alterar") {

    $obRegra = new RFISDocumento();
    $obVisao = new VFISManterDocumento( $obRegra );

    # Filtros da pesquisa
    $obRsDocumento = $obVisao->listarDocumentoAlterar( $_REQUEST );
    $obRsDocumento->addFormatacao("nom_documento","STRIPSLASHES");

    //Nome do Documento
    $stNomDocumento = $obRsDocumento->getCampo("nom_documento");
    $obTxtNomDocumento->setValue( $stNomDocumento );

    $obHdnDocumento =  new Hidden;
    $obHdnDocumento->setName ( "cod_documento"              );
    $obHdnDocumento->setValue( $_REQUEST['cod_documento']   );

    $obHdnTipoFiscalizacao =  new Hidden;
    $obHdnTipoFiscalizacao->setName ( "cod_tipo_fiscalizacao"            );
    $obHdnTipoFiscalizacao->setValue( $_REQUEST['cod_tipo_fiscalizacao'] );

    $obTxtCodDocumento = new Label;
    $obTxtCodDocumento->setRotulo   ( 'Código'                   );
    $obTxtCodDocumento->setTitle    ( 'Código do documento'      );
    $obTxtCodDocumento->setValue    ( $_REQUEST['cod_documento'] );
    $obTxtCodDocumento->setName     ( 'cod_documento'            );

    $obTxtTipoFiscalizacao = new Label;
    $obTxtTipoFiscalizacao->setRotulo   ( 'Tipo de Fiscalização'              );
    $obTxtTipoFiscalizacao->setTitle    ( 'Tipo de Fiscalização'              );
    $obTxtTipoFiscalizacao->setValue    ( $_REQUEST['descricao_fiscalizacao'] );
    $obTxtTipoFiscalizacao->setName     ( 'cod_documento'                     );

    if ($_REQUEST['ativo'] == 'Ativo') {
        $obRadioAtivoSim->setChecked( true );
    } else {
        $obRadioAtivoNao->setChecked( true );
    }

    if ($_REQUEST['uso_interno'] == 't') {
        $obRadioUsoInterno->setChecked( true );
    } else {
        $obRadioSolicitaContribuinte->setChecked( true );
    }
}

//MONTA FORMLÁRIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                    );

$obFormulario->addHidden     ( $obHdnAcao                 );
$obFormulario->addHidden     ( $obHdnCtrl                 );
$obFormulario->addTitulo ( "Dados para Documento Fiscal" );

if ($stAcao=='incluir') {
    $obTipoFiscalizacao->geraFormulario($obFormulario);
} else {
    $obFormulario->addComponente ( $obTxtCodDocumento       );
    $obFormulario->addComponente ( $obTxtTipoFiscalizacao   );
    $obFormulario->addHidden     ( $obHdnDocumento          );
    $obFormulario->addHidden     ( $obHdnTipoFiscalizacao   );
}

$obFormulario->addComponente ( $obTxtNomDocumento);

$obFormulario->agrupaComponentes ( array($obRadioUsoInterno,$obRadioSolicitaContribuinte) );
$obFormulario->agrupaComponentes ( array($obRadioAtivoSim,$obRadioAtivoNao) );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
