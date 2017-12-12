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
    * Página de Aba de Documentação
    * Data de Criação   : ???

    * @author Desenvolvedor: ???

    * Casos de uso: uc-04.04.07

    $Id: FMManterServidorAbaDocumentacao.php 66017 2016-07-07 17:31:31Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalTipoDocumentoDigital.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorDocumentoDigital.class.php";
include_once $pgOculDocumentacao;

$arArquivosDocumentos = array();

if(isset($inCodServidor)){
    $obTPessoalServidorDocumentoDigital = new TPessoalServidorDocumentoDigital();
    $obTPessoalServidorDocumentoDigital->setDado('cod_servidor', $inCodServidor);
    $obTPessoalServidorDocumentoDigital->recuperaServidorDocumentoDigital($rsServidorArqDigitais);

    $inId = 0;
    $stDirANEXO = CAM_GRH_PESSOAL."anexos/";

    foreach($rsServidorArqDigitais->getElementos() AS $chave => $arqDigital){
        $arArquivosDocumentos[$inId]['inTipoDocDigital']    = $arqDigital['cod_tipo'];
        $arArquivosDocumentos[$inId]['stTipoArqDocDigital'] = $arqDigital['descricao'];
        $arArquivosDocumentos[$inId]['stArquivo']           = $stDirANEXO.$arqDigital['arquivo_digital'];
        $arArquivosDocumentos[$inId]['arquivo_digital']     = $arqDigital['arquivo_digital'];
        $arArquivosDocumentos[$inId]['name']                = $arqDigital['nome_arquivo'];
        $arArquivosDocumentos[$inId]['inId']                = $inId;
        $arArquivosDocumentos[$inId]['boCopiado']           = 'TRUE';
        $arArquivosDocumentos[$inId]['tmp_name']            = $stDirANEXO.$arqDigital['arquivo_digital'];
        $arArquivosDocumentos[$inId]['boExcluido']          = 'FALSE';

        $inId++;
    }

    $jsOnload .= "buscaValor('montaListaArqDigital',2);";
}

Sessao::write("arArquivosDocumentos", $arArquivosDocumentos);

$obLblCPF = new Label;
$obLblCPF->setRotulo      ( "CPF"  );
$obLblCPF->setId          ( 'stCPF');
$obLblCPF->setValue       ( $stCPF );

$obTxtCPF = new CPF;
$obTxtCPF->setName( "stCPF" );
$obTxtCPF->setRotulo( "CPF" );
$obTxtCPF->setTitle("Informe o CPF do servidor.");
$obTxtCPF->setNull( false );

$obLblRG = new Label;
$obLblRG->setRotulo      ( "RG"   );
$obLblRG->setId          ( 'stRG' );
$obLblRG->setValue       ( $stRG  );

$obLblOrgaoEmissor = new Label;
$obLblOrgaoEmissor->setRotulo      ( "Órgão Emissor");
$obLblOrgaoEmissor->setId          ( 'stOrgaoEmissor' );
$obLblOrgaoEmissor->setValue       ($stOrgaoEmissor   );

$obLblCNH = new Label;
$obLblCNH->setRotulo      ( "Número da CNH"     );
$obLblCNH->setId          ( 'stNumeroCnh'       );
$obLblCNH->setValue       ($stNumeroCnh );

$obLblCategoriaCNH = new Label;
$obLblCategoriaCNH->setRotulo      ( "Categoria CNH" );
$obLblCategoriaCNH->setId          ( 'stCategoriaCnh'  );
$obLblCategoriaCNH->setValue       ($stCategoriaCnh  );

$obTxtPisPasep = new TextBox;
$obTxtPisPasep->setRotulo      ( "PIS/PASEP"         );
$obTxtPisPasep->setTitle       ( "Informe o número do PIS/PASEP. Ao digitar zeros no campo, o sistema não fará a validação do número");
$obTxtPisPasep->setName        ( "stPisPasep"        );
$obTxtPisPasep->setValue       ( $stPisPasep         );
$obTxtPisPasep->setSize        ( 15                  );
$obTxtPisPasep->setMaxLength   ( 14                  );
$obTxtPisPasep->setNull        ( false               );
$obTxtPisPasep->obEvento->setOnKeyUp("mascaraDinamico('999.99999.99-9', this, event);" );
$obTxtPisPasep->obEvento->setOnChange ( "buscaValor('validaNumeroPis',2)" );

$obLblPisPasep = new Label;
$obLblPisPasep->setRotulo      ( "PIS/PASEP"         );
$obLblPisPasep->setValue       ( $stPisPasep         );

$obHdnPisPasep = new Hidden;
$obHdnPisPasep->setName        ( "stPisPasep"        );
$obHdnPisPasep->setValue       ( $stPisPasep         );

$obTxtCadastroPis = new Data;
$obTxtCadastroPis->setName   ( "dtCadastroPis" );
$obTxtCadastroPis->setValue  ( $dtCadastroPis  );
$obTxtCadastroPis->setTitle  ( "Informe a data de cadastramento no PIS/PASEP.");
$obTxtCadastroPis->setNull   ( false );
$obTxtCadastroPis->setRotulo ( "Cadastro no PIS" );
$obTxtCadastroPis->setSize   ( 15                );
$obTxtCadastroPis->obEvento->setOnChange ( "buscaValor('validaDataCadPis',2)" );

$obTxtTituloEleitor = new TextBox;
$obTxtTituloEleitor->setRotulo      ( "Título de Eleitor" );
$obTxtTituloEleitor->setTitle       ( "Informe o número do título de eleitor." );
$obTxtTituloEleitor->setName        ( "inTituloEleitor"   );
$obTxtTituloEleitor->setValue       ( $inTituloEleitor    );
$obTxtTituloEleitor->setSize        ( 15                  );
$obTxtTituloEleitor->setMaxLength   ( 12                  );
$obTxtTituloEleitor->setNull        ( false               );
$obTxtTituloEleitor->setInteiro     ( true                );

$obTxtSecaoTitulo = new TextBox;
$obTxtSecaoTitulo->setRotulo      ( "Seção do Título"   );
$obTxtSecaoTitulo->setTitle       ("Informe a seção do título de eleitor.");
$obTxtSecaoTitulo->setName        ( "inSecaoTitulo"     );
$obTxtSecaoTitulo->setValue       ( $inSecaoTitulo      );
$obTxtSecaoTitulo->setSize        ( 15                  );
$obTxtSecaoTitulo->setMaxLength   ( 5                   );
$obTxtSecaoTitulo->setNull        ( false               );
$obTxtSecaoTitulo->setInteiro     ( true                );

$obTxtZonaTitulo = new TextBox;
$obTxtZonaTitulo->setRotulo      ( "Zona do Título"    );
$obTxtZonaTitulo->setTitle       ("Informe a zona do título de eleitor.");
$obTxtZonaTitulo->setName        ( "inZonaTitulo"      );
$obTxtZonaTitulo->setValue       ( $inZonaTitulo       );
$obTxtZonaTitulo->setSize        ( 15                  );
$obTxtZonaTitulo->setMaxLength   ( 5                  );
$obTxtZonaTitulo->setNull        ( false               );
$obTxtZonaTitulo->setInteiro     ( true                );

$obTxtCertificadoReservista = new TextBox;
$obTxtCertificadoReservista->setRotulo      ( "Certificado de Reservista"      );
$obTxtCertificadoReservista->setTitle       ("Informe o número da carteira de reservista.");
$obTxtCertificadoReservista->setName        ( "stCertificadoReservista"        );
$obTxtCertificadoReservista->setValue       ( $stCertificadoReservista         );
$obTxtCertificadoReservista->setSize        ( 15                 );
$obTxtCertificadoReservista->setMaxLength   ( 14                 );
$obTxtCertificadoReservista->setNull        ( true                );

$obTxtCategoriaCertificado = new TextBox;
$obTxtCategoriaCertificado->setRotulo    ( "Categoria do Certificado"   );
$obTxtCategoriaCertificado->setTitle     ("Informe a categoria do certificado de reservista.");
$obTxtCategoriaCertificado->setName      ( "inCodCategoriaCertificado"  );
$obTxtCategoriaCertificado->setValue     ( $inCodCategoriaCertificado   );
$obTxtCategoriaCertificado->setMaxLength ( 3                            );
$obTxtCategoriaCertificado->setSize      ( 12                           );
$obTxtCategoriaCertificado->setInteiro   ( true                         );
$obTxtCategoriaCertificado->setNull      ( true                         );

$obCmbCategoriaCertificado = new Select;
$obCmbCategoriaCertificado->setRotulo     ( "Categoria do Certificado"  );
$obCmbCategoriaCertificado->setTitle      ("Informe a categoria do certificado de reservista.");
$obCmbCategoriaCertificado->setName       ( "inCategoriaCertificado"    );
$obCmbCategoriaCertificado->setValue      ( $inCodCategoriaCertificado  );
$obCmbCategoriaCertificado->setStyle      ( "width: 250px"              );
$obCmbCategoriaCertificado->addOption     ( "", "Selecione"             );
$obCmbCategoriaCertificado->addOption     ( "0", "Não informado"        );
$obCmbCategoriaCertificado->addOption     ( "1", "1a"                   );
$obCmbCategoriaCertificado->addOption     ( "2", "2a"                   );
$obCmbCategoriaCertificado->addOption     ( "3", "3a"                   );
$obCmbCategoriaCertificado->setNull       ( true                        );

$obTxtOrgaoExpedidorCertificado = new TextBox;
$obTxtOrgaoExpedidorCertificado->setRotulo    ( "Órgão Expedidor do Certificado"  );
$obTxtOrgaoExpedidorCertificado->setTitle     ( "Informe o órgão expedidor do certificado de reservista."  );
$obTxtOrgaoExpedidorCertificado->setName      ( "inCodOrgaoExpedidorCertificado"  );
$obTxtOrgaoExpedidorCertificado->setValue     ( $inCodOrgaoExpedidorCertificado   );
$obTxtOrgaoExpedidorCertificado->setMaxLength ( 3  );
$obTxtOrgaoExpedidorCertificado->setSize      ( 12 );
$obTxtOrgaoExpedidorCertificado->setInteiro   ( true                );
$obTxtOrgaoExpedidorCertificado->setNull      ( true );

$obCmbOrgaoExpedidorCertificado = new Select;
$obCmbOrgaoExpedidorCertificado->setRotulo     ( "Órgão Expedidor do Certificado" );
$obCmbOrgaoExpedidorCertificado->setTitle      ( "Informe o órgão expedidor do certificado de reservista." );
$obCmbOrgaoExpedidorCertificado->setName       ( "inOrgaoExpedidorCertificado" );
$obCmbOrgaoExpedidorCertificado->setValue      ( $inCodOrgaoExpedidorCertificado );
$obCmbOrgaoExpedidorCertificado->setStyle      ( "width: 250px" );
$obCmbOrgaoExpedidorCertificado->addOption     ( "", "Selecione" );
$obCmbOrgaoExpedidorCertificado->addOption     ( "1", "Exército" );
$obCmbOrgaoExpedidorCertificado->addOption     ( "2", "Marinha" );
$obCmbOrgaoExpedidorCertificado->addOption     ( "3", "Aeronáutica" );
$obCmbOrgaoExpedidorCertificado->setNull       ( true );

//Dados de CTPS

//Define obejeto textbox para armazenar o numero de CTPS
$obTxtNumeroCTPS = new TextBox;
$obTxtNumeroCTPS->setRotulo     ( "Número" );
$obTxtNumeroCTPS->setName       ( "inNumeroCTPS" );
$obTxtNumeroCTPS->setValue      ( isset($inNumeroCTPS) ? $inNumeroCTPS : "" );
$obTxtNumeroCTPS->setTitle      ( "Informe o número da CTPS." );
$obTxtNumeroCTPS->setMaxLength  ( 10    );
$obTxtNumeroCTPS->setSize       ( 15   );
$obTxtNumeroCTPS->setInteiro    ( true );

//Define objeto TEXTBOX para armazenar o orgao expedidor CTPS
$obTxtOrgaoExpedidorCTPS = new TextBox;
$obTxtOrgaoExpedidorCTPS->setRotulo     ( "Órgão Expedidor" );
$obTxtOrgaoExpedidorCTPS->setName       ( "stOrgaoExpedidorCTPS" );
$obTxtOrgaoExpedidorCTPS->setValue      ( isset($stOrgaoExpedidorCTPS) ? $stOrgaoExpedidorCTPS : "");
$obTxtOrgaoExpedidorCTPS->setTitle      ( "Informe o órgão expedidor da CTPS." );
$obTxtOrgaoExpedidorCTPS->setMaxLength  ( 10   );
$obTxtOrgaoExpedidorCTPS->setSize       ( 15   );

//Define objeto TEXTBOX para armazenar a série do CTPS
$obTxtSerieCTPS = new TextBox;
$obTxtSerieCTPS->setRotulo     ( "Série" );
$obTxtSerieCTPS->setName       ( "stSerieCTPS" );
$obTxtSerieCTPS->setValue      ( isset($stSerieCTPS) ? $stSerieCTPS : ""  );
$obTxtSerieCTPS->setTitle      ( "Informe a série da CTPS." );
$obTxtSerieCTPS->setMaxLength  ( 5     );
$obTxtSerieCTPS->setSize       ( 15    );

// Estado da CTPS
$obRPessoalServidor->recuperaTodosUF( $rsCtpsUF);
$obCmbCodUFCTPS = new Select;
$obCmbCodUFCTPS->setName               ( "stSiglaUF"                           );
$obCmbCodUFCTPS->setRotulo             ( "UF"                                  );
$obCmbCodUFCTPS->setValue              ( isset($stSiglaUF) ? $stSiglaUF : ""   );
$obCmbCodUFCTPS->setTitle              ( "Selecione o estado da naturalidade." );
$obCmbCodUFCTPS->setNull               ( true                                  );
$obCmbCodUFCTPS->setCampoId            ( "[cod_uf]"                            );
$obCmbCodUFCTPS->setCampoDesc          ( "[sigla_uf]"                          );
$obCmbCodUFCTPS->addOption             ( "", "Selecione"                       );
$obCmbCodUFCTPS->preencheCombo         ( $rsCtpsUF                             );
$obCmbCodUFCTPS->setStyle              ( "width: 100px"                        );

//Define objeto para armazenar a data de emissao do CTPS
$obTxtDataCTPS = new Data;
$obTxtDataCTPS->setRotulo ( "Data de Emissão" );
$obTxtDataCTPS->setName   ( "dtDataCTPS" );
$obTxtDataCTPS->setValue  ( isset($dtDataCTPS) ? $dtDataCTPS : ""  );
$obTxtDataCTPS->setTitle  ( "Informe a data de emissão da CTPS." );
$obTxtDataCTPS->setSize   ( 15                );
$obTxtDataCTPS->obEvento->setOnChange("buscaValor('validaDataEmissaoCTPS',2);");

$obBtnIncluirCTPS = new Button;
$obBtnIncluirCTPS->setId("btnIncluir");
$obBtnIncluirCTPS->setName ( "btnIncluir" );
$obBtnIncluirCTPS->setValue( "Incluir" );
$obBtnIncluirCTPS->setTipo ( "button" );
$obBtnIncluirCTPS->setDisabled(false);
$obBtnIncluirCTPS->obEvento->setOnClick ( " buscaValor('incluirCTPS',2); " );

$obBtnAlterarCTPS = new Button;
$obBtnAlterarCTPS->setId("btnAlterar");
$obBtnAlterarCTPS->setName ( "btnAlterar" );
$obBtnAlterarCTPS->setValue( "Alterar" );
$obBtnAlterarCTPS->setTipo ( "button" );
$obBtnAlterarCTPS->setDisabled(true);
$obBtnAlterarCTPS->obEvento->setOnClick ( "buscaValor('alterarCTPS',2);" );

$obBtnLimparCTPS = new Button;
$obBtnLimparCTPS->setName( "btnLimpar" );
$obBtnLimparCTPS->setValue( "Limpar" );
$obBtnLimparCTPS->setTipo( "button" );
$obBtnLimparCTPS->obEvento->setOnClick ( "buscaValor('limparCTPS',2);" );

$obSpnCTPS = new Span;
$obSpnCTPS->setId ( "spnCTPS" );

//Arquivos Digitais
$obTPessoalTipoDocumentoDigital = new TPessoalTipoDocumentoDigital();
$obTPessoalTipoDocumentoDigital->recuperaTodos($rsTipoDocDigital, "", " ORDER BY descricao ");

$obTipoArqDocDigital = new Select;
$obTipoArqDocDigital->setRotulo( 'Tipo de documento'            );
$obTipoArqDocDigital->setTitle( 'Selecione o tipo de documento' );
$obTipoArqDocDigital->setName ( 'inTipoDocDigital'     );
$obTipoArqDocDigital->setId   ( 'inTipoDocDigital'     );
$obTipoArqDocDigital->setCampoId   ( 'cod_tipo'        );
$obTipoArqDocDigital->setCampoDesc ( 'descricao'       );
$obTipoArqDocDigital->addOption    ( '', 'Selecione'   );
$obTipoArqDocDigital->preencheCombo( $rsTipoDocDigital );
$obTipoArqDocDigital->setNull      ( true              );

$obLnkTipoArqDocDigital = new Link;
$obLnkTipoArqDocDigital->setRotulo( "&nbsp;" );
$obLnkTipoArqDocDigital->setHref  ( "JavaScript:abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FMManterTipoDocumentoDigital.php','frm','','','','".Sessao::getId()."&stAcao=incluir','800','600');" );
$obLnkTipoArqDocDigital->setValue ( "Cadastrar Tipo de Documento" );

$obFileArqDigital = new FileBox;
$obFileArqDigital->setId     ( "stArqDigital"  );
$obFileArqDigital->setName   ( "stArqDigital"  );
$obFileArqDigital->setValue  ( ""              );
$obFileArqDigital->setRotulo ( "Arquivo"       );
$obFileArqDigital->setTitle  ( "Selecione o arquivo." );

$stTamLimite = ini_get("upload_max_filesize");
switch (substr($stTamLimite, -1)){
    case 'M': case 'm':
        $stTamLimite = (int)(((int) $stTamLimite * 1048576)/1000000).'Mb';
    break;
    case 'K': case 'k':
        $stTamLimite = (int)(((int) $stTamLimite * 1024)/1000).'Kb';
    break;
    case 'G': case 'g':
        $stTamLimite = (int)(((int) $stTamLimite * 1073741824)/1000000000).'Gb';
    break;
}

$stArqValidos  = "JPG, JPEG, GIF, PNG, ODT, DOC e DOCX. ";
$stArqValidos .= "Tamanho Limite: ".$stTamLimite;

$obLblArqValidos = new Label;
$obLblArqValidos->setRotulo ( "Tipos de Arquivos Válidos");
$obLblArqValidos->setValue ( $stArqValidos  );

$obBtnIncluirArqDigital = new Button;
$obBtnIncluirArqDigital->setValue             ( "Incluir arquivo"  );
$obBtnIncluirArqDigital->setName              ( "incluiArqDigital" );
$obBtnIncluirArqDigital->setId                ( "incluiArqDigital" );
$obBtnIncluirArqDigital->obEvento->setOnClick ( "buscaValor('incluirArqDigital', 2);" );

$obBtnLimparArqDigital = new Button;
$obBtnLimparArqDigital->setValue             ( "Limpar"  );
$obBtnLimparArqDigital->setName              ( "limpaArqDigital" );
$obBtnLimparArqDigital->setId                ( "limpaArqDigital" );
$obBtnLimparArqDigital->obEvento->setOnClick ( "buscaValor('limparArqDigital', 2);" );

//Span da Listagem de Arquivos Digitais
$obSpnListaArqDigital = new Span;
$obSpnListaArqDigital->setID("spnListaArqDigital");

