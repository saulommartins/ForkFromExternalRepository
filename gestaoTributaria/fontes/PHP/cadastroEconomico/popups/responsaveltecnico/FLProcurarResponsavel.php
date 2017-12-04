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
    * Filtro do Popup para Responsavel Tecnico
    * Data de Criação   : 20/04/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo
    *
    * @ignore

    * $Id: FLProcurarResponsavel.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.8  2006/09/15 13:50:37  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMResponsavelTecnico.class.php"     );
include_once ( CAM_GA_CSE_NEGOCIO."RProfissao.class.php"                 );
include_once ( CAM_GA_CSE_NEGOCIO."RConselho.class.php"                  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"           );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividadeProfissao.class.php"   );
include_once ( CAM_GA_ADM_NEGOCIO."RAdministracaoUF.class.php");

//Define o nome dos arquivos PHP
$stPrograma    = "ProcurarResponsavel";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

Sessao::remove( "link" );

//sistemaLegado::debugRequest();
// INSTANCIA REGRAS UTILIZADAS
$obRConselho            = new RConselho                 ;
$obRResponsavelTecnico  = new RCEMResponsavelTecnico    ;
$obRCEMAtividadeProfissao = new RCEMAtividadeProfissao;
$obRProfissao           = new RProfissao                ;
$obRUF                  = new RUF                       ;

if ( Sessao::read( "arProfissoes" ) ) {
    $obRResponsavelTecnico->setProfissoes( Sessao::read( "arProfissoes" ) );
    $obRResponsavelTecnico->listarProfissoes( $rsProfissoes );
}else
if ($_REQUEST["Profissoes"]) {
    $obRResponsavelTecnico->setProfissoes( $_REQUEST["Profissoes"] );
    $obRResponsavelTecnico->listarProfissoes( $rsProfissoes );
}else
if ($_REQUEST["AtividadesInscricao"]) {
    $obRCEMAtividadeProfissao->setCodigosAtividades( $_REQUEST["AtividadesInscricao"] );
    $obRCEMAtividadeProfissao->listarAtividadesProfissoes ( $rsProfissoes );
} else {
    $obRProfissao->listarProfissao($rsProfissoes);
}

$obRUF->listarUF($rsUF);

// HIDDENS
$obHdnAcao  = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $_REQUEST['stAcao']  );

$obHdnCtrl  = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue($_REQUEST['stCtrl']);

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

$obHdnProfissao = new Hidden;
$obHdnProfissao->setName ( "stProfissao" );
$obHdnProfissao->setValue( $_REQUEST['stProfissao'] );

$obHdnSequencia = new Hidden;
$obHdnSequencia->setName ( "inSequencia" );
$obHdnSequencia->setValue( $_REQUEST['inSequencia']  );

$obHdnCodProfissao = new Hidden;
$obHdnCodProfissao->setName ( "inCodProfissao" );
$obHdnCodProfissao->setValue( $_REQUEST['inCodProfissao']  );

$obHdnNomForm = new Hidden;
$obHdnNomForm->setName( "nomForm" );
$obHdnNomForm->setValue( $_REQUEST["nomForm"] );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName ( "tipoBusca" );
$obHdnTipoBusca->setValue( $_REQUEST['tipoBusca'] );

$obHdnFuncionalidade = new Hidden;
$obHdnFuncionalidade->setName  ( "funcionalidade"            );
$obHdnFuncionalidade->setValue ( $_REQUEST["funcionalidade"] );

// COMPONENTES
$obTxtProfissao = new TextBox;
$obTxtProfissao->setRotulo        ( "Profissão"                         );
$obTxtProfissao->setTitle         ( "Profissão do Respónsavel Técnico"  );
$obTxtProfissao->setName          ( "inCodigoProfissao"                 );
$obTxtProfissao->setValue         ( $_REQUEST["inCodigoProfissao"]      );
$obTxtProfissao->setSize          ( 8                                   );
$obTxtProfissao->setMaxLength     ( 8                                   );
$obTxtProfissao->setInteiro       ( true                                );
$obTxtProfissao->obEvento->setOnChange("montaAtributosProfissao();"     );

$obCmbProfissao = new Select;
$obCmbProfissao->setRotulo        ( "Profissão"                         );
$obCmbProfissao->setName          ( "cmbProfissao"                      );
$obCmbProfissao->setValue         ( $_REQUEST["inCodigoProfissao"]     );
$obCmbProfissao->addOption        ( "", "Selecione"                     );
$obCmbProfissao->setCampoId       ( "cod_profissao"                     );
$obCmbProfissao->setCampoDesc     ( "nom_profissao"                     );
$obCmbProfissao->preencheCombo    ( $rsProfissoes                       );
if ($_REQUEST["AtividadesInscricao"]) {
    $obCmbProfissao->setNull          ( false                               );
} else {
    $obCmbProfissao->setNull          ( true                               );
}
$obCmbProfissao->setStyle         ( "width: 220px"                      );
$obCmbProfissao->obEvento->setOnChange("montaAtributosProfissao();"     );

$obTxtNumCGM = new TextBox;
$obTxtNumCGM->setRotulo    	( "CGM"                             );
$obTxtNumCGM->setTitle     	( "Filtro de Numero de CGM"       	);
$obTxtNumCGM->setId        	( "inNumCGM"                        );
$obTxtNumCGM->setNull      	( true                             	);
$obTxtNumCGM->setName  		( "inNumCGM"    					);
$obTxtNumCGM->setInteiro	( true		    					);

$obTxtNomCGM = new TextBox;
$obTxtNomCGM->setRotulo    	( "Nome"                            );
$obTxtNomCGM->setTitle     	( "Filtro de Numero de CGM"       	);
$obTxtNomCGM->setId        	( "inNomCGM"                        );
$obTxtNomCGM->setNull      	( true                             	);
$obTxtNomCGM->setSize      	( 40                             	);
$obTxtNomCGM->setName  		( "inNomCGM"    					);

$obTxtNomeRegistro = new TextBox;
$obTxtNomeRegistro->setRotulo        ( "<span id='rotRegistro'>Registro</span>"    );
$obTxtNomeRegistro->setTitle         ( "Numero do registro no conselho de classe " );
$obTxtNomeRegistro->setName          ( "stRegistro"         );
$obTxtNomeRegistro->setId            ( "stRegistro"         );
$obTxtNomeRegistro->setSize          ( 10                   );
$obTxtNomeRegistro->setMaxLength     ( 10                   );
$obTxtNomeRegistro->setNull          ( true                 );

$obTxtUf = new TextBox;
$obTxtUf->setRotulo        ( "UF"                               );
$obTxtUf->setTitle         ( "Estado Correspondente ao conselho de classe"  );
$obTxtUf->setName          ( "inCodigoUf"                       );
$obTxtUf->setValue         ( $_REQUEST["codUF"]                 );
$obTxtUf->setSize          ( 8                                  );
$obTxtUf->setMaxLength     ( 2                                  );
$obTxtUf->setNull          ( true                               );
$obTxtUf->setInteiro       ( true                               );
$obTxtUf->obEvento->setOnChange("montaAtributosUf();"           );

$obCmbUf = new Select;
$obCmbUf->setName          ( "cmbUf"                    );
$obCmbUf->setValue         ( $_REQUEST["codUF"]         );
$obCmbUf->addOption        ( "", "Selecione"            );
$obCmbUf->setCampoId       ( "cod_uf"                   );
$obCmbUf->setCampoDesc     ( "nom_uf"                   );
$obCmbUf->preencheCombo    ( $rsUF                      );
$obCmbUf->setNull          ( true                       );
$obCmbUf->setStyle         ( "width: 220px"             );
$obCmbUf->obEvento->setOnChange("montaAtributosUf();"   );

$obIFrame = new IFrame;
$obIFrame->setName("oculto");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("0");

$obIFrame2 = new IFrame;
$obIFrame2->setName   ( "telaMensagem" );
$obIFrame2->setWidth  ( "100%"         );
$obIFrame2->setHeight ( "50"           );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm                           );
$obFormulario->addTitulo    ( "Dados para Respónsavel Técnico"  );
$obFormulario->addHidden    ( $obHdnCtrl                        );
$obFormulario->addHidden    ( $obHdnAcao                        );
$obFormulario->addHidden    ( $obHdnCampoNom                    );
$obFormulario->addHidden    ( $obHdnCampoNum                    );
$obFormulario->addHidden    ( $obHdnFuncionalidade              );
$obFormulario->addHidden    ( $obHdnNomForm                     );
$obFormulario->addHidden    ( $obHdnProfissao                   );
$obFormulario->addHidden    ( $obHdnCodProfissao                );
$obFormulario->addHidden    ( $obHdnSequencia );
if ($_GET['tipoBusca'] == 'Profissao') {
    $obFormulario->addHidden( $obHdnTipoBusca                   );
}
$obFormulario->addComponenteComposto($obTxtProfissao,$obCmbProfissao);
$obFormulario->addComponente        ( $obTxtNumCGM              );
$obFormulario->addComponente        ( $obTxtNomCGM              );
$obFormulario->addComponente        ( $obTxtNomeRegistro        );
$obFormulario->addComponenteComposto($obTxtUf,$obCmbUf          );

$obFormulario->OK();
$obFormulario->show();
$obIFrame->show();
$obIFrame2->show();

?>
