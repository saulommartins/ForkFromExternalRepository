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
    * Página de Formulário da Caonfiguração do cadastro imobiliario
    * Data de Criação   : 23/11/2004

    * @author Diego Barbosa Victoria

    * @ignore

    * $Id: FMManterConfiguracao.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.01

*/

/*
$Log$
Revision 1.8  2007/03/28 19:15:33  dibueno
Bug #8633#

Revision 1.7  2006/09/15 14:33:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obErro = $obRCEMConfiguracao->consultarConfiguracao();

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//DEFINICAO DOS COMPONENTES
$obForm  = new Form;
$obForm->setAction                     ( $pgProc                );
$obForm->settarget                     ( "oculto"               );

$obHdnAcao = new Hidden;
$obHdnAcao->setName                    ( "stAcao"               );
$obHdnAcao->setValue                   ( $stAcao                );

$stNomCGM = null;
if ( $obRCEMConfiguracao->getCGMDiretorTributos() ) {

    include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                      );
    $obRCGM = new RCGM;
    $obRCGM->setNumCGM( $obRCEMConfiguracao->getCGMDiretorTributos() );
    $obRCGM->consultar( $rsCGM );
    $stNomCGM = $rsCGM->getCampo('nom_cgm');

}

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull    ( false );
$obPopUpCGM->obCampoCod->setValue   ( $obRCEMConfiguracao->getCGMDiretorTributos() );
$obPopUpCGM->setValue   ( $stNomCGM );
$obPopUpCGM->setRotulo  ( "Diretor de Tributos" );
$obPopUpCGM->setTitle   ( "Informe o CGM no Diretor de Tributos do municpipio." );

$obRdbEmissaoCertidaoBaixaSim = new Radio;
$obRdbEmissaoCertidaoBaixaSim->setRotulo     ( "Certidão de Baixa" );
$obRdbEmissaoCertidaoBaixaSim->setName       ( "stEmissaoCertidaoBaixa" );
$obRdbEmissaoCertidaoBaixaSim->setLabel      ( "Emitir" );
$obRdbEmissaoCertidaoBaixaSim->setValue      ( "sim" );
$obRdbEmissaoCertidaoBaixaSim->setChecked    ( ( $obRCEMConfiguracao->getEmissaoCertidaoBaixa() == "sim" ) );
$obRdbEmissaoCertidaoBaixaSim->setTitle      ( "Emissão de certidão de baixa." );
$obRdbEmissaoCertidaoBaixaSim->setNull       ( false );

$obRdbEmissaoCertidaoBaixaNao = new Radio;
$obRdbEmissaoCertidaoBaixaNao->setRotulo     ( "Certidão de Baixa" );
$obRdbEmissaoCertidaoBaixaNao->setName       ( "stEmissaoCertidaoBaixa" );
$obRdbEmissaoCertidaoBaixaNao->setLabel      ( "Não Emitir" );
$obRdbEmissaoCertidaoBaixaNao->setValue      ( "nao" );
$obRdbEmissaoCertidaoBaixaNao->setChecked    ( ( $obRCEMConfiguracao->getEmissaoCertidaoBaixa() == "nao" ) );
$obRdbEmissaoCertidaoBaixaNao->setTitle      ( "Emissão de certidão de baixa." );
$obRdbEmissaoCertidaoBaixaNao->setNull       ( false );

$obRdbLicencaAutomatico = new Radio;
$obRdbLicencaAutomatico->setRotulo     ( "Número Licença"                                            );
$obRdbLicencaAutomatico->setName       ( "inNumeroLicenca"                                           );
$obRdbLicencaAutomatico->setLabel      ( "Automático"                                                );
$obRdbLicencaAutomatico->setValue      ( "Automatico"                                                );
$obRdbLicencaAutomatico->setChecked    ( ( $obRCEMConfiguracao->getNumeroLicenca() == 0 )            );
$obRdbLicencaAutomatico->setTitle      ( "Define se o número da licença será informado ou gerado automaticamente" );
$obRdbLicencaAutomatico->setNull       ( false                                                       );

$obRdbLicencaExercicio = new Radio;
$obRdbLicencaExercicio->setRotulo      ( "Número Licença"                                            );
$obRdbLicencaExercicio->setName        ( "inNumeroLicenca"                                           );
$obRdbLicencaExercicio->setLabel       ( "Automático por exercício"                                  );
$obRdbLicencaExercicio->setValue       ( "Exercicio"                                                 );
$obRdbLicencaExercicio->setChecked     ( ( $obRCEMConfiguracao->getNumeroLicenca() == 2 )            );
$obRdbLicencaExercicio->setNull        ( false                                                       );

$obRdbLicencaManual = new Radio;
$obRdbLicencaManual->setRotulo         ( "Número Licença"                                            );
$obRdbLicencaManual->setName           ( "inNumeroLicenca"                                           );
$obRdbLicencaManual->setLabel          ( "Manual"                                                    );
$obRdbLicencaManual->setValue          ( "Manual"                                                    );
$obRdbLicencaManual->setChecked        ( ( $obRCEMConfiguracao->getNumeroLicenca() == 1 )            );
$obRdbLicencaManual->setNull           ( false                                                       );

$obRdbLicencaNroAlvaraExercicio = new Radio;
$obRdbLicencaNroAlvaraExercicio->setRotulo ( "Número Alvará Licença" );
$obRdbLicencaNroAlvaraExercicio->setName ( "stAlvaraLicenca" );
$obRdbLicencaNroAlvaraExercicio->setLabel ( "Exercício" );
$obRdbLicencaNroAlvaraExercicio->setValue ( "Exercicio" );
$obRdbLicencaNroAlvaraExercicio->setChecked ( ( $obRCEMConfiguracao->getNroAlvara() == "Exercicio" ) );
$obRdbLicencaNroAlvaraExercicio->setNull ( false );

$obRdbLicencaNroAlvaraDocumento = new Radio;
$obRdbLicencaNroAlvaraDocumento->setRotulo ( "Número Alvará Licença" );
$obRdbLicencaNroAlvaraDocumento->setName ( "stAlvaraLicenca" );
$obRdbLicencaNroAlvaraDocumento->setLabel ( "Documento" );
$obRdbLicencaNroAlvaraDocumento->setValue ( "Documento" );
$obRdbLicencaNroAlvaraDocumento->setChecked ( ( $obRCEMConfiguracao->getNroAlvara() == "Documento" ) );
$obRdbLicencaNroAlvaraDocumento->setNull ( false );

$obTxtMascaraLicenca = new TextBox;
$obTxtMascaraLicenca->setName            ( "stMascaraLicenca"                                        );
$obTxtMascaraLicenca->setValue           ( $obRCEMConfiguracao->getMascaraLicenca()                  );
$obTxtMascaraLicenca->setRotulo          ( "*Máscara Licença"                                        );
$obTxtMascaraLicenca->setSize            ( "30"                                                      );
$obTxtMascaraLicenca->setMaxLength       ( ""                                                        );
$obTxtMascaraLicenca->setNull            ( true                                                      );
$obTxtMascaraLicenca->obEvento->setOnKeyPress( "return validaTecla(event);"                          );

$obRdbInscricaoAutomatico = new Radio;
$obRdbInscricaoAutomatico->setRotulo   ( "Inscrição Econômica"                                       );
$obRdbInscricaoAutomatico->setName     ( "boNumeroInscricao"                                                      );
$obRdbInscricaoAutomatico->setLabel    ( "Automático"                                                             );
$obRdbInscricaoAutomatico->setNull     ( false                                                                    );
$obRdbInscricaoAutomatico->setChecked  ( ( $obRCEMConfiguracao->getNumeroInscricao() == 't' )                     );
$obRdbInscricaoAutomatico->setTitle    ( "Define se o número da inscrição econômica será informado ou gerado"     );
$obRdbInscricaoAutomatico->setValue    ( "Automatico"                                                             );

$obRdbInscricaoManual = new Radio;
$obRdbInscricaoManual->setRotulo       ( "Inscrição Econômica"                                            );
$obRdbInscricaoManual->setName         ( "boNumeroInscricao"                                                      );
$obRdbInscricaoManual->setLabel        ( "Manual"                                                                 );
$obRdbInscricaoManual->setNull         ( false                                                                    );
$obRdbInscricaoManual->setChecked      ( ( $obRCEMConfiguracao->getNumeroInscricao() == 'f' )                     );
$obRdbInscricaoManual->setTitle        ( "Define se o número da inscrição econômica será informado ou gerado"     );
$obRdbInscricaoManual->setValue        ( "Manual"                                                                 );

$obTxtMascaraInscricao = new TextBox;
$obTxtMascaraInscricao->setName        ( "stMascaraInscricao"                                                     );
$obTxtMascaraInscricao->setValue       ( $obRCEMConfiguracao->getMascaraInscricao()                               );
$obTxtMascaraInscricao->setRotulo      ( "*Máscara Inscrição Econômica"                                           );
$obTxtMascaraInscricao->setSize        ( "30"                                                                     );
$obTxtMascaraInscricao->setMaxLength   ( ""                                                                       );
$obTxtMascaraInscricao->setNull        ( true                                                                     );
$obTxtMascaraInscricao->setInteiro     ( true                                                                     );

$obRdbCNAEVincular = new Radio;
$obRdbCNAEVincular->setRotulo          ( "CNAE Fiscal"                                                            );
$obRdbCNAEVincular->setName            ( "boCNAE"                                                                 );
$obRdbCNAEVincular->setLabel           ( "Vincular"                                                               );
$obRdbCNAEVincular->setNull            ( false                                                                    );
$obRdbCNAEVincular->setChecked         ( ( $obRCEMConfiguracao->getCNAE() ==  'Vincular' )                        );
$obRdbCNAEVincular->setTitle           ( "Define se deverá ser informado o CNAE fiscal correspondente"            );
$obRdbCNAEVincular->setValue           ( "Vincular"                                                               );

if ( !$obRCEMConfiguracao->getCNAE() ) {
    $obRCEMConfiguracao->setCNAE('NaoVincular');
}
$obRdbCNAENaoVincular = new Radio;
$obRdbCNAENaoVincular->setRotulo       ( "CNAE Fiscal"                                                  );
$obRdbCNAENaoVincular->setName         ( "boCNAE"                                                       );
$obRdbCNAENaoVincular->setLabel        ( "Não Vincular"                                                 );
$obRdbCNAENaoVincular->setNull         ( false                                                          );
$obRdbCNAENaoVincular->setChecked      ( ( $obRCEMConfiguracao->getCNAE() == 'NaoVincular' )            );
$obRdbCNAENaoVincular->setTitle        ( "Define se deverá ser informado o CNAE fiscal correspondente"  );
$obRdbCNAENaoVincular->setValue        ( "NaoVincular"                                                  );

$obTxtVgSanitSecretaria = new TextBox;
$obTxtVgSanitSecretaria->setName            ( "stVgSanitSecretaria" );
$obTxtVgSanitSecretaria->setValue           ( $obRCEMConfiguracao->getVgSanitSecretaria() );
$obTxtVgSanitSecretaria->setRotulo          ( "Vig. Sanitária Secretaria" );
$obTxtVgSanitSecretaria->setNull            ( false );
$obTxtVgSanitSecretaria->setSize            ( 64 );
$obTxtVgSanitSecretaria->setMaxLength         ( 256 );

$obTxtVgSanitDepartamento = new TextBox;
$obTxtVgSanitDepartamento->setName            ( "stVgSanitDepartamento" );
$obTxtVgSanitDepartamento->setValue           ( $obRCEMConfiguracao->getVgSanitDepartamento() );
$obTxtVgSanitDepartamento->setRotulo          ( "Vig. Sanitária Departamento" );
$obTxtVgSanitDepartamento->setNull            ( false );
$obTxtVgSanitDepartamento->setSize            ( 64 );
$obTxtVgSanitDepartamento->setMaxLength         ( 256 );

$obBtnClean = new Button;
$obBtnClean->setName                    ( "btnClean"              );
$obBtnClean->setValue                   ( "Cancelar"              );
$obBtnClean->setTipo                    ( "button"                );
$obBtnClean->obEvento->setOnClick       ( "document.frm.reset();" );
$obBtnClean->setDisabled                ( false                   );

$obBtnOK = new Ok;
$botoesForm     = array ( $obBtnOK , $obBtnClean );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm               ( $obForm                                                 );
$obFormulario->setAjuda              ( "UC-05.02.01"                                           );
$obFormulario->addHidden             ( $obHdnAcao                                              );
$obFormulario->addTitulo             ( "Dados para configuração"                               );
$obFormulario->agrupaComponentes     ( array( $obRdbLicencaAutomatico, $obRdbLicencaExercicio, $obRdbLicencaManual) );
$obFormulario->addComponenteComposto ( $obRdbLicencaNroAlvaraExercicio, $obRdbLicencaNroAlvaraDocumento );

$obFormulario->addComponente         ( $obTxtMascaraLicenca                                    );
$obFormulario->addComponenteComposto ( $obRdbInscricaoAutomatico, $obRdbInscricaoManual        );
$obFormulario->addComponente         ( $obTxtMascaraInscricao                                  );
$obFormulario->addComponenteComposto ( $obRdbCNAEVincular, $obRdbCNAENaoVincular               );
//$obFormulario->OK();

$obFormulario->addComponente( $obPopUpCGM               );

$obFormulario->addComponente( $obTxtVgSanitSecretaria );
$obFormulario->addComponente( $obTxtVgSanitDepartamento );
$obFormulario->addComponenteComposto( $obRdbEmissaoCertidaoBaixaSim, $obRdbEmissaoCertidaoBaixaNao );
$obFormulario->defineBarra($botoesForm);
$obFormulario->show();

?>
