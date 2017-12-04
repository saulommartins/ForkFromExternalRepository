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
    * Formulario para Cadastro Economico >> Conceder Outras Licenças
    * Data de Criação   : 22/04/2005
    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra

    * $Id: FMAlterarLicencaGeralTipo.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
Revision 1.8  2006/11/22 11:20:11  cercato
Bug #7329#

Revision 1.7  2006/09/15 14:33:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicencaDiversa.class.php"     );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMTipoLicencaDiversa.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"           );

//Define o nome dos arquivos PHP
$stPrograma     = "ConcederLicencaGeral";
$pgFilt         = "FL".$stPrograma.".php"       ;
$pgList         = "LS".$stPrograma.".php"       ;
$pgForm         = "FM".$stPrograma.".php"       ;
$pgFormTipo     = "FM".$stPrograma."Tipo.php"   ;
$pgProc         = "PR".$stPrograma.".php"       ;
$pgOcul         = "OC".$stPrograma.".php"       ;
$pgJs           = "JS".$stPrograma.".js"        ;
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "lsElementos", array() );
Sessao::write( "inNumElementos", 0 );

Sessao::write('acao', '462' );
$inCodAlvara = $_REQUEST["cod_documento"];
/* Atributos Dinamicos */
$obRCEMLicencaDiversa = new RCEMLicencaDiversa;
$obRCEMLicencaDiversa->obRCadastroDinamico->setChavePersistenteValores(
    array   (
            "cod_tipo"      => $_REQUEST["inCodigoTipo"]    ,
            "cod_licenca"   => $_REQUEST["inCodigoLicenca"]
            )
);
$obRCEMLicencaDiversa->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosLicencaDiversa );

$obMontaAtributosLicencaDiversa = new MontaAtributos;
$obMontaAtributosLicencaDiversa->setTitulo     ( "Atributos"              );
$obMontaAtributosLicencaDiversa->setName       ( "AtributoLicenca_"    );
$obMontaAtributosLicencaDiversa->setRecordSet  ( $rsAtributosLicencaDiversa );

$arConfiguracao = array();
$obRCEMLicencaDiversa->recuperaConfiguracao( $arConfiguracao );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                      ( "stCtrl"                      );
$obHdnCtrl->setValue                     ( $stCtrl                       );

$obHdnAcao = new Hidden;
$obHdnAcao->setName                      ( "stAcao"                      );
$obHdnAcao->setValue                     ( $stAcao                       );

$obHdnCodTipoLicenca = new Hidden;
$obHdnCodTipoLicenca->setName            ( "inCodigoTipoLicenca"            );
$obHdnCodTipoLicenca->setValue           ( $_REQUEST["inCodigoTipoLicenca"] );

$obHdnNumAtributos = new Hidden;
$obHdnNumAtributos->setName     ( "inNumAtributos"  );
$obHdnNumAtributos->setId       ( "inNumAtributos"  );
$obHdnNumAtributos->setValue    ( 0                 );

$obHdnboElemento = new Hidden;
$obHdnboElemento->setId     ( "boElemento" );
$obHdnboElemento->setName   ( "boElemento" );
$obHdnboElemento->setValue  ("0");

$obHdnNumLicenca= new Hidden;
$obHdnNumLicenca->setName   ( "inCodigoLicenca"             );
$obHdnNumLicenca->setValue  ($_REQUEST["inCodigoLicenca"]   );

$obHdnCodTipoLicenca= new Hidden;
$obHdnCodTipoLicenca->setName   ( "inCodigoTipoLicenca"      );
$obHdnCodTipoLicenca->setValue  ($_REQUEST["inCodigoTipo"]   );

$obHdnCGM= new Hidden;
$obHdnCGM->setName   ( "inNumCGM"           );
$obHdnCGM->setValue  ($_REQUEST["inNumCGM"] );

$obRCEMTipoLicenca =  new RCEMTipoLicencaDiversa;
$obRCEMTipoLicenca->setCodigoTipoLicencaDiversa($_REQUEST["inCodigoTipoLicenca"]);
$obRCEMTipoLicenca->consultar( $rsTipoLicenca );

$obRCEMTipoLicenca->recuperaTipoModeloDocumento( $rsModeloLicenca );

$obHdnCodDocumento = new Hidden;
$obHdnCodDocumento->setName     ('inCodigoDocumento');
$obHdnCodDocumento->setValue    ( $rsModeloLicenca->getCampo('cod_documento') );

$obHdnCodTipoDocumento = new Hidden;
$obHdnCodTipoDocumento->setName ('inCodigoTipoDocumento');
$obHdnCodTipoDocumento->setValue( $rsModeloLicenca->getCampo('cod_tipo_documento') );

$obHdnNomeDocumento = new Hidden;
$obHdnNomeDocumento->setName ('stNomeDocumento');
$obHdnNomeDocumento->setValue( $rsModeloLicenca->getCampo('nome_documento') );

$obHdnNomeArquivo = new Hidden;
$obHdnNomeArquivo->setName ('stNomeArquivo');
$obHdnNomeArquivo->setValue( $rsModeloLicenca->getCampo('nome_arquivo_agt') );

//DEFINIÇÃO DOS COMPONENTES
// DADOS PARA LICENÇA
$obRCEMLicencaDiversa->obRCEMConfiguracao->consultarConfiguracao();
$stMascaraLicenca = $obRCEMLicencaDiversa->obRCEMConfiguracao->getMascaraLicenca();
//echo "Mask>".$stMascaraLicenca;
$obLblNumLicenca = new Label;
$obLblNumLicenca->setRotulo         ( "Número da Licença"               );
$obLblNumLicenca->setTitle          ( "Número da Licença"               );
$obLblNumLicenca->setName           ( "lblCodigoLicenca"                );
//$obLblNumLicenca->setMask           ( $stMascaraLicenca );
$obLblNumLicenca->setValue          ( $_REQUEST["inCodigoLicenca"] ." - ".$_REQUEST["stNomTipo"]     );

$obLblCGM = new Label;
$obLblCGM->setRotulo( "CGM" );
$obLblCGM->setTitle ( "Pessoa fisica ou juridica cadastrada" );
$obLblCGM->setId    ( "inNomCGM" );
$obLblCGM->setValue ($_REQUEST["inNumCGM"]." - ".$_REQUEST["stNomeCGM"]);

// VALIDADE DA LICENÇA
$obDtDataInicio = new Data;
$obDtDataInicio->setName                ( "dtDataInicio"            );
$obDtDataInicio->setId                  ( "dtDataInicio"            );
$obDtDataInicio->setRotulo              ( "Data de Início"          );
$obDtDataInicio->setNull                ( false                     );
$obDtDataInicio->setMaxLength           ( 20                        );
$obDtDataInicio->setSize                ( 10                        );
$obDtDataInicio->setValue               ( $_REQUEST["dtInicio"]     );
$obDtDataInicio->obEvento->setOnChange  ( "validaData1500( this );" );

$obDtDataTermino = new Data;
$obDtDataTermino->setName               ( "dtDataTermino"           );
$obDtDataTermino->setId                 ( "dtDataTermino"           );
$obDtDataTermino->setRotulo             ( "Data de Término"        );
$obDtDataTermino->setNull               ( true                      );
$obDtDataTermino->setMaxLength          ( 20                        );
$obDtDataTermino->setSize               ( 10                        );
$obDtDataTermino->setValue              ( $_REQUEST["dtTermino"]    );
$obDtDataTermino->obEvento->setOnChange ( "validaDataLicenca();"    );

$obCBoxEmissaoDocumento = new CheckBox;
$obCBoxEmissaoDocumento->setName    ( "boEmissaoDocumento"  );
$obCBoxEmissaoDocumento->setLabel   ( "Impressão Local"     );
$obCBoxEmissaoDocumento->setRotulo  ( "Emissão de Alvará"   );

$obITextBoxSelectDocumento = new ITextBoxSelectDocumento;
$obITextBoxSelectDocumento->setCodAcao( Sessao::read('acao') );
$obITextBoxSelectDocumento->setCodModeloDocumento ( $inCodAlvara );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setRotulo ( "Modelo do Alvará" );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setNULL ( false );
$obITextBoxSelectDocumento->setDisabledSelectDoc( "True" );

//Observação
$obTxtObservacao = new TextArea;
$obTxtObservacao->setName   ("stObservacao");
$obTxtObservacao->setRotulo ("Observações");
$obTxtObservacao->setTitle  ("Observações referentes à licença");
$obTxtObservacao->setValue  ( $stObservacao );

//Buscar Observação
    include_once( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaObservacao.class.php" );
    $obTCEMLicencaObservacao = new TCEMLicencaObservacao;
    $stFiltro = " WHERE cod_licenca = ".$_REQUEST["inCodigoLicenca"]." AND exercicio = '".$_REQUEST["stExercicio"]."'";
    $obTCEMLicencaObservacao->recuperaTodos( $rsLicencaObservacao, $stFiltro );
    if ( !$rsLicencaObservacao->Eof() ) {
        $stObservacao = $rsLicencaObservacao->getCampo( "observacao" );
        $obTxtObservacao->setValue  ( $stObservacao );
    }

//DEFINICAO DO FORM

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                  ( $obForm                       );
$obFormulario->setAjuda      ( "UC-05.02.12");
$obFormulario->addTitulo                ( "Dados para Licença"          );
$obFormulario->addHidden                ( $obHdnCtrl                    );
$obFormulario->addHidden                ( $obHdnAcao                    );
$obFormulario->addHidden                ( $obHdnCodDocumento            );
$obFormulario->addHidden                ( $obHdnCodTipoDocumento        );
$obFormulario->addHidden                ( $obHdnNomeDocumento           );
$obFormulario->addHidden                ( $obHdnNomeArquivo             );
$obFormulario->addHidden                ( $obHdnNumAtributos            );
$obFormulario->addHidden                ( $obHdnNumLicenca              );
$obFormulario->addHidden                ( $obHdnCodTipoLicenca          );
$obFormulario->addComponente            ( $obLblNumLicenca              );
$obFormulario->addComponente            ( $obLblCGM                     );
$obFormulario->addComponente            ( $obTxtObservacao );
$obFormulario->addTitulo                ( "Validade da Licença"         );
$obFormulario->addComponente            ( $obDtDataInicio               );
$obFormulario->addComponente            ( $obDtDataTermino              );
$obMontaAtributosLicencaDiversa->geraFormulario ($obFormulario);
$obITextBoxSelectDocumento->geraFormulario ( $obFormulario );
$obFormulario->addComponente            ( $obCBoxEmissaoDocumento       );

$obFormulario->ok();
$obFormulario->show();
?>
