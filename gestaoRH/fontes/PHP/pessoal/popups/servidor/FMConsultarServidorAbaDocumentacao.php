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

* @author Analista: ???
* @author Desenvolvedor: ???

* @ignore

$Id: FMConsultarServidorAbaDocumentacao.php 66023 2016-07-08 15:01:19Z michel $

* Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorDocumentoDigital.class.php";

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
        $arArquivosDocumentos[$inId]['tmp_name']            = $stDirANEXO.$arqDigital['arquivo_digital'];

        $inId++;
    }
}

Sessao::write("arArquivosDocumentos", $arArquivosDocumentos);

$obLblCPF = new Label;
$obLblCPF->setRotulo      ( "CPF"     );
$obLblCPF->setId          ( stCPF  );
$obLblCPF->setValue       ( $stCPF );

$obLblRG = new Label;
$obLblRG->setRotulo      ( "RG"     );
$obLblRG->setId          ( stRG );
$obLblRG->setValue       ( $stRG );

$obLblOrgaoEmissor = new Label;
$obLblOrgaoEmissor->setRotulo      ( "Órgão Emissor");
$obLblOrgaoEmissor->setId          ( stOrgaoEmissor );
$obLblOrgaoEmissor->setValue       ($stOrgaoEmissor );

$obLblCNH = new Label;
$obLblCNH->setRotulo      ( "Número da CNH"     );
$obLblCNH->setId          ( stNumeroCnh        );
$obLblCNH->setValue       ($stNumeroCnh );

$obLblCategoriaCNH = new Label;
$obLblCategoriaCNH->setRotulo      ( "Categoria CNH" );
$obLblCategoriaCNH->setId          ( stCategoriaCnh  );
$obLblCategoriaCNH->setValue       ($stCategoriaCnh  );

$obLblPisPasep = new Label;
$obLblPisPasep->setRotulo      ( "PIS/PASEP"         );
$obLblPisPasep->setName        ( "stPisPasep"        );
$obLblPisPasep->setValue       ( $stPisPasep         );

$obLblCadastroPis = new Label;
$obLblCadastroPis->setName   ( "dtCadastroPis" );
$obLblCadastroPis->setValue  ( $dtCadastroPis  );
$obLblCadastroPis->setRotulo ( "Cadastro no PIS" );

$obLblTituloEleitor = new Label;
$obLblTituloEleitor->setRotulo      ( "Título de Eleitor" );
$obLblTituloEleitor->setName        ( "inTituloEleitor"   );
$obLblTituloEleitor->setValue       ( $inTituloEleitor    );

$obLblSecaoTitulo = new Label;
$obLblSecaoTitulo->setRotulo      ( "Seção do Título"   );
$obLblSecaoTitulo->setName        ( "inSecaoTitulo"     );
$obLblSecaoTitulo->setValue       ( $inSecaoTitulo      );

$obLblZonaTitulo = new Label;
$obLblZonaTitulo->setRotulo      ( "Zona do Título"    );
$obLblZonaTitulo->setName        ( "inZonaTitulo"      );
$obLblZonaTitulo->setValue       ( $inZonaTitulo       );

$obLblCertificadoReservista = new Label;
$obLblCertificadoReservista->setRotulo      ( "Certificado de Reservista"      );
$obLblCertificadoReservista->setName        ( "inCertificadoReservista"        );
$obLblCertificadoReservista->setValue       ( $inCertificadoReservista         );

$obLblCategoriaCertificado = new Label;
$obLblCategoriaCertificado->setRotulo    ( "Categoria do Certificado"   );
$obLblCategoriaCertificado->setName      ( "inCodCategoriaCertificado"  );
switch ($inCodCategoriaCertificado) {
    case "0":
        $stCategoriaCertificado = $inCodCategoriaCertificado."-Não Informado";
        break;
    case "1":
        $stCategoriaCertificado = $inCodCategoriaCertificado."-1a";
        break;
    case "2":
        $stCategoriaCertificado = $inCodCategoriaCertificado."-2a";
        break;
    case "4":
        $stCategoriaCertificado = $inCodCategoriaCertificado."-3a";
        break;
    default:
        $stCategoriaCertificado = "";
        break;
}
$obLblCategoriaCertificado->setValue     ( $stCategoriaCertificado   );

$obLblOrgaoExpedidorCertificado = new Label;
$obLblOrgaoExpedidorCertificado->setRotulo    ( "Órgão Expedidor do Certificado"  );
$obLblOrgaoExpedidorCertificado->setName      ( "inCodOrgaoExpedidorCertificado"  );
switch ($inCodOrgaoExpedidorCertificado) {
    case "1":
        $stOrgaoExpedidorCertificado = $inCodOrgaoExpedidorCertificado."-Exército";
        break;
    case "2":
        $stOrgaoExpedidorCertificado = $inCodOrgaoExpedidorCertificado."-Marinha";
        break;
    case "3":
        $stOrgaoExpedidorCertificado = $inCodOrgaoExpedidorCertificado."-Aeronáutica";
        break;
    default:
        $stOrgaoExpedidorCertificado = "";
        break;
}
$obLblOrgaoExpedidorCertificado->setValue     ( $stOrgaoExpedidorCertificado   );

$obRPessoalServidor->setCodServidor( $inCodServidor );
$obRPessoalServidor->addRPessoalCTPS();
$obRPessoalServidor->roRPessoalCTPS->listarCTPS( $rsCTPS, $boTransacao );

$obLista = new Lista;
$obLista->setMostraPaginacao( false );
$obLista->setTitulo( "Dados de CTPS" );

$obLista->setRecordSet( $rsCTPS );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Número" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Série" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data emissão" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Orgão expedidor" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "numero" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "serie" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_emissao" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "orgao_expedidor" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->montaHTML();
$stHtml = $obLista->getHTML();
$stHtml = str_replace("\n","",$stHtml);
$stHtml = str_replace("  ","",$stHtml);
$stHtml = str_replace("'","\\'",$stHtml);

$obSpnCTPS = new Span;
$obSpnCTPS->setId ( "spnCTPS" );
$obSpnCTPS->setValue($stHtml);

//Span da Listagem de Arquivos Digitais
$obSpnListaArqDigital = new Span;
$obSpnListaArqDigital->setID("spnListaArqDigital");
