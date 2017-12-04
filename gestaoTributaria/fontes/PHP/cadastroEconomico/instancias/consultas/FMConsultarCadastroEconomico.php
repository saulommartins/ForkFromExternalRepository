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
    * Página  para Consulta de Cadastro Economico
    * Data de Criação: 21/09/2005

    * @author  Marcelo B. Paulino

    * @ignore

    * $Id: FMConsultarCadastroEconomico.php 63376 2015-08-21 18:55:42Z arthur $

    * Casos de uso: uc-05.02.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php";
include_once CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeFato.class.php";
include_once CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeDireito.class.php";
include_once CAM_GT_CEM_NEGOCIO."RCEMAutonomo.class.php";
include_once CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarCadastroEconomico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
include_once( $pgJS   );
include_once( $pgOcul );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

// CONSULTA CONFIGURACAO DO MODULO ECONOMICO
$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setCodigoModulo( 14 );
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCEMConfiguracao->getMascaraInscricao();

// RECUPERA OS DADOS DA INSCRICAO ECONOMICA
$obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
$obRCEMInscricaoEconomica->setInscricaoEconomica      ( $_REQUEST['inCodInscricao'] );
$obRCEMInscricaoEconomica->consultarInscricaoEconomica( $rsInscricaoEconomica       );

$obRCEMInscricaoEconomica->ConsultaBaixaProcesso();

$arChaveAtributoInscricao =  array( "inscricao_economica" => $rsInscricaoEconomica->getCampo("inscricao_economica") );

$rsHorarios = new RecordSet;

// RECUPERA ATRIBUTOS DE ACORDO COM O ENQUADRAMENTO
if ( $rsInscricaoEconomica->getCampo("enquadramento") == "F" ) {
    $tipoInscricao = "Empresa de fato";
    $obRCEMEmpresaDeFato = new RCEMEmpresaDeFato;
    $obRCEMEmpresaDeFato->obRCadastroDinamico->setChavePersistenteValores          ( $arChaveAtributoInscricao );
    $obRCEMEmpresaDeFato->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
    $obRCEMInscricaoEconomica->listarInscricaoHorarios( $rsHorarios );
}

if ( $rsInscricaoEconomica->getCampo("enquadramento") == "A" ) {
    $tipoInscricao = "Autônomo";
    $obRCEMAutonomo = new RCEMAutonomo;
    $obRCEMAutonomo->obRCadastroDinamico->setChavePersistenteValores          ( $arChaveAtributoInscricao );
    $obRCEMAutonomo->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
}

// SE FOR EMPRESA DE DIREITO RECUPERA OS SOCIOS
if ( $rsInscricaoEconomica->getCampo("enquadramento") == "D" ) {
    $tipoInscricao = "Empresa de direito";
    $obRCEMEmpresaDeDireito = new RCEMEmpresaDeDireito;
    $obRCEMEmpresaDeDireito->setInscricaoEconomica        ( $_REQUEST['inCodInscricao'] );
    $obRCEMEmpresaDeDireito->listarEmpresaDireitoSociedade( $rsSocios                   );
    $obRCEMEmpresaDeDireito->listarInscricaoHorarios      ( $rsHorarios                 );

    $obRCEMEmpresaDeDireito->obRCadastroDinamico->setChavePersistenteValores          ( $arChaveAtributoInscricao );
    $obRCEMEmpresaDeDireito->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

    // SOMA A QUOTA DE CADA SOCIO PARA OBTER O CAPITAL SOCIAL DA EMPRESA
    // MONTA ARRAY DE SOCIOS PARA EXIBIR LISTA
    $flCapitalSocial = 0;
    $arSociosSessao = array();
    while ( !$rsSocios->eof() ) {
        $flCapitalSocial += $rsSocios->getCampo('quota_socio');
        $arSociosSessao[] = array(
                                        "inNumCGM"  => $rsSocios->getCampo('numcgm'),
                                        "stNomeCGM" => $rsSocios->getCampo('nom_cgm'),
                                        "flQuota"   => number_format( $rsSocios->getCampo('quota_socio'),2, ",", "." )
                                       );
        $rsSocios->proximo();
    }

    Sessao::write( "socios", $arSociosSessao );
    $flCapitalSocial = number_format( $flCapitalSocial, '2', ',', '.' );
    $rsSociosLista = new RecordSet;
    if ( count($arSociosSessao) > 0) {
        $rsSociosLista->preenche( $arSociosSessao );
    }
    $stJs .= montaListaSocios( $rsSociosLista );
}

// MONTA ARRAY DE HORARIOS PARA EXIBIR LISTA
$arHorariosSessao = array();
while ( !$rsHorarios->eof() ) {
    $arHorariosSessao[] = array(
                                    "stNomDia"  => $rsHorarios->getCampo('nom_dia'),
                                    "hrInicio"  => $rsHorarios->getCampo('hr_inicio'),
                                    "hrTermino" => $rsHorarios->getCampo('hr_termino')
                                    );
    $rsHorarios->proximo();
}

Sessao::write( "horarios", $arHorariosSessao );
$rsHorariosLista = new RecordSet;
if ( count($arHorariosSessao) > 0 ) {
    $rsHorariosLista->preenche( $arHorariosSessao );
}
$stJs .= montaListaHorarios( $rsHorariosLista );

SistemaLegado::executaFramePrincipal( $stJs );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $stCtrl  );

$obLblInscricaoEconomica = new Label;
$obLblInscricaoEconomica->setRotulo( "Inscrição Econômica" );

$obLblInscricaoEconomica->setValue ( $rsInscricaoEconomica->getCampo("inscricao_economica")." - ".$tipoInscricao);

$obLblNomeRazao = new Label;
$obLblNomeRazao->setRotulo         ( "Nome / Razão Social" );
$obLblNomeRazao->setValue          ( $rsInscricaoEconomica->getCampo("numcgm")." - ".$rsInscricaoEconomica->getCampo("nom_cgm") );

if ( $rsInscricaoEconomica->getCampo("cpf") != "" ) {
    $stCPFCNPJ = $rsInscricaoEconomica->getCampo("cpf");
    $obMascara = new MascaraCPF;
} else {
    $stCPFCNPJ = $rsInscricaoEconomica->getCampo("cnpj");
    $obMascara = new MascaraCNPJ;
}
$obMascara->setDesmascarado( $stCPFCNPJ );
$obMascara->mascaraDinamica();
$stCPFCNPJ = $obMascara->getMascarado();

$obLblCPFCNPJ = new Label;
$obLblCPFCNPJ->setRotulo           ( "CPF / CNPJ" );
$obLblCPFCNPJ->setValue            ( $stCPFCNPJ   );

$obLblRegistroJunta = new Label;
$obLblRegistroJunta->setRotulo     ( "Registro na Junta" );
$obLblRegistroJunta->setValue      ( $rsInscricaoEconomica->getCampo("num_registro_junta") );

$stCodNatureza  = substr($rsInscricaoEconomica->getCampo("cod_natureza"),0,3);
$stCodNatureza .= "-".substr($rsInscricaoEconomica->getCampo("cod_natureza"),3,1);

$obLblNatureza = new Label;
$obLblNatureza->setRotulo          ( "Natureza Jurídica" );
$obLblNatureza->setValue           ( $stCodNatureza."  ".$rsInscricaoEconomica->getCampo("nom_natureza") );

$obLblCategoria = new Label;
$obLblCategoria->setRotulo         ( "Categoria" );
$obLblCategoria->setValue          ( $rsInscricaoEconomica->getCampo("nom_categoria") );

$obLblDtAbertura = new Label;
$obLblDtAbertura->setRotulo        ( "Data de Abertura" );
$obLblDtAbertura->setValue         ( $rsInscricaoEconomica->getCampo("dt_abertura") );

$obLblDtInclusao = new Label;
$obLblDtInclusao->setRotulo        ( "Data de Inclusão" );
$obLblDtInclusao->setValue         ( $rsInscricaoEconomica->getCampo("dt_inclusao") );

$stDomicilioCEP = $rsInscricaoEconomica->getCampo("cep");
$stDomicilioCaixaPostal = $rsInscricaoEconomica->getCampo("caixa_postal");
$stDomicilioBairro = $rsInscricaoEconomica->getCampo("cod_bairro");
if ($stDomicilioBairro) {
    $stDomicilioBairro .= " - ".$rsInscricaoEconomica->getCampo("nom_bairro");
}

$stDomicilioMunicipio = $rsInscricaoEconomica->getCampo("nom_municipio");
$stDomicilioEstado = $rsInscricaoEconomica->getCampo("nom_uf");
$stDomicilioNumero = $rsInscricaoEconomica->getCampo("numero_i");
if ($rsInscricaoEconomica->getCampo("complemento_i")) {
    $stDomicilioNumero .= " - ".$rsInscricaoEconomica->getCampo("complemento_i");
}

$stDomicilioLogradouro = $rsInscricaoEconomica->getCampo("cod_logradouro");
if ($stDomicilioLogradouro) {
    $stDomicilioLogradouro .= " - ".$rsInscricaoEconomica->getCampo("logradouro_i");
}

$stDomicilioFiscal = $rsInscricaoEconomica->getCampo("logradouro_f");
if ( $rsInscricaoEconomica->getCampo("numero_f") != "" ) {
    $stDomicilioFiscal .= ", ".$rsInscricaoEconomica->getCampo("numero_f");
}
if ( $rsInscricaoEconomica->getCampo("complemento_f") != "" ) {
    $stDomicilioFiscal .= " - ".$rsInscricaoEconomica->getCampo("complemento_f");
}

$obLblInscricaoMunicipal = new Label;
$obLblInscricaoMunicipal->setRotulo         ( "Inscrição Municipal" );
$obLblInscricaoMunicipal->setValue          ( $rsInscricaoEconomica->getCampo("inscricao_municipal") );

$obLblDomicilio = new Label;
$obLblDomicilio->setRotulo         ( "Domicílio Fiscal" );
$obLblDomicilio->setValue          ( $stDomicilioFiscal );

if ( $rsInscricaoEconomica->getCampo("dt_baixa") ) {
    $stSituacao = "Baixada";
} else {
    $stSituacao = "Ativa";
}
$obLblSituacao = new Label;
$obLblSituacao->setRotulo          ( "Situação" );
$obLblSituacao->setValue           ( $stSituacao );

$obLblProcessoBaixa = new Label;
$obLblProcessoBaixa->setRotulo           ( "Processo de Baixa" );
$obLblProcessoBaixa->setValue            ( $obRCEMInscricaoEconomica->getCodProcessoBaixa() . '/' . $obRCEMInscricaoEconomica->getExercicioBaixa());

$arTMPDados = explode( " ", $rsInscricaoEconomica->getCampo("dt_baixa") );
$dia = explode ( '-', $arTMPDados[0] );

$obLblDtBaixa = new Label;
$obLblDtBaixa->setRotulo           ( "Data de Baixa" );
$obLblDtBaixa->setValue            ( $dia[2].'/'.$dia[1].'/'.$dia[0] );

$obLblMotivoBaixa = new Label;
$obLblMotivoBaixa->setRotulo       ( "Motivo" );
$obLblMotivoBaixa->setValue        ( $rsInscricaoEconomica->getCampo("motivo") );

$obLblCapitalSocial = new Label;
$obLblCapitalSocial->setRotulo     ( "Capital Social (R$)" );
$obLblCapitalSocial->setValue      ( $flCapitalSocial );

$obLblDomicilioCEP = new Label;
$obLblDomicilioCEP->setRotulo      ( "CEP" );
$obLblDomicilioCEP->setValue       ( $stDomicilioCEP );

$obLblDomicilioBairro = new Label;
$obLblDomicilioBairro->setRotulo   ( "Bairro" );
$obLblDomicilioBairro->setValue    ( $stDomicilioBairro );

$obLblDomicilioLogradouro = new Label;
$obLblDomicilioLogradouro->setRotulo     ( "Logradouro" );
$obLblDomicilioLogradouro->setValue      ( $stDomicilioLogradouro );

$obLblDomicilioNumero = new Label;
$obLblDomicilioNumero->setRotulo     ( "Número / Complemento" );
$obLblDomicilioNumero->setValue      ( $stDomicilioNumero );

$obLblDomicilioCaixaPostal = new Label;
$obLblDomicilioCaixaPostal->setRotulo     ( "Caixa Postal" );
$obLblDomicilioCaixaPostal->setValue      ( $stDomicilioCaixaPostal );

$obLblDomicilioMunicipio = new Label;
$obLblDomicilioMunicipio->setRotulo     ( "Município" );
$obLblDomicilioMunicipio->setValue      ( $stDomicilioMunicipio );

$obLblDomicilioEstado = new Label;
$obLblDomicilioEstado->setRotulo     ( "Estado" );
$obLblDomicilioEstado->setValue      ( $stDomicilioEstado );

$obSpnListaSocios = new Span;
$obSpnListaSocios->setId( "lsListaSocios" );

$obSpnListaHorarios = new Span;
$obSpnListaHorarios->setId( "lsListaHorarios" );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setLabel      ( TRUE         );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

$obButtonVoltar = new Button;
$obButtonVoltar->setName ( "Voltar" );
$obButtonVoltar->setValue( "Voltar" );
$obButtonVoltar->obEvento->setOnClick( "Cancelar('".$stLocation."');" );

$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

// DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm    );
$obFormulario->setAjuda      ( "UC-05.02.21");
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( "Dados da Inscrição Econômica" );

$obFormulario->addComponente( $obLblInscricaoEconomica );
$obFormulario->addComponente( $obLblNomeRazao          );
$obFormulario->addComponente( $obLblCPFCNPJ            );

// EXIBE SOMENTE SE FOR EMPRESA DE DIREITO
if ( $rsInscricaoEconomica->getCampo("enquadramento") == "D" ) {
    $obFormulario->addComponente( $obLblRegistroJunta  );
    $obFormulario->addComponente( $obLblNatureza       );
    $obFormulario->addComponente( $obLblCategoria      );
}

$obFormulario->addComponente( $obLblDtAbertura         );
$obFormulario->addComponente( $obLblDtInclusao         );
$obFormulario->addComponente( $obLblSituacao           );

$obFormulario->addTitulo    ( "Dados do Domicílio Fiscal" );
if ( $rsInscricaoEconomica->getCampo("tipo_domicilio") == "I" ) {
    $obFormulario->addComponente( $obLblDomicilioLogradouro );
    $obFormulario->addComponente( $obLblDomicilioNumero );
    $obFormulario->addComponente( $obLblDomicilioBairro );
    $obFormulario->addComponente( $obLblDomicilioCEP );
    $obFormulario->addComponente( $obLblDomicilioCaixaPostal );
    $obFormulario->addComponente( $obLblDomicilioMunicipio );
    $obFormulario->addComponente( $obLblDomicilioEstado );
} else {
    $obFormulario->addComponente( $obLblInscricaoMunicipal );
    $obFormulario->addComponente( $obLblDomicilio );
}

// EXIBE SOMENTE SE ESTIVER BAIXADA
if ( $rsInscricaoEconomica->getCampo("dt_baixa") ) {
    $obFormulario->addComponente( $obLblProcessoBaixa );
    $obFormulario->addComponente( $obLblDtBaixa        );
    $obFormulario->addComponente( $obLblMotivoBaixa    );
}

// EXIBE SOMENTE SE FOR EMPRESA DE DIREITO
if ( $rsInscricaoEconomica->getCampo("enquadramento") == "D" ) {
    $obFormulario->addTitulo    ( "Dados da Sociedade" );
    $obFormulario->addComponente( $obLblCapitalSocial  );
    $obFormulario->addSpan      ( $obSpnListaSocios    );
}

$obFormulario->addSpan( $obSpnListaHorarios );

$obMontaAtributos->geraFormulario( $obFormulario );

$obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );

$obFormulario->show();
