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
    * Data de Criação   : 31/08/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FMManterConfiguracao.php 63766 2015-10-07 19:09:57Z arthur $

    * Casos de uso: uc-05.01.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php";
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRCIMConfiguracao = new RCIMConfiguracao();
$obErro = $obRCIMConfiguracao->consultarConfiguracao();

$boCodLocal = $obRCIMConfiguracao->getCodigoLocal();
$boNumIM    = $obRCIMConfiguracao->getNumeroIM();

$stAcao = $request->get('stAcao');

//DEFINICAO DOS COMPONENTES
$obForm  = new Form;
$obForm->setAction      ( $pgProc );
$obForm->settarget      ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao );

$obRdbCodLocalAutomatico = new Radio;
$obRdbCodLocalAutomatico->setRotulo     ( "Código de Localização" );
$obRdbCodLocalAutomatico->setName       ( "boCodigoLocal" );
$obRdbCodLocalAutomatico->setId         ( "boCodigoLocal" );
$obRdbCodLocalAutomatico->setLabel      ( "Automático" );
$obRdbCodLocalAutomatico->setValue      ( "true" );
$obRdbCodLocalAutomatico->setChecked    ( ( $boCodLocal == 'true' ) );
$obRdbCodLocalAutomatico->setTitle      ( "Define se o código de localização será informado ou gerado automaticamente." );
$obRdbCodLocalAutomatico->setNull       ( false );

$obRdbCodLocalManual = new Radio;
$obRdbCodLocalManual->setRotulo      ( "Código de Localização" );
$obRdbCodLocalManual->setName        ( "boCodigoLocal" );
$obRdbCodLocalManual->setiD          ( "boCodigoLocal" );
$obRdbCodLocalManual->setLabel       ( "Manual" );
$obRdbCodLocalManual->setValue       ( "false" );
$obRdbCodLocalManual->setChecked     ( ( empty($boCodLocal) || $boCodLocal == 'false') ? 'false' : "" );
$obRdbCodLocalManual->setNull        ( false );

$obTxtMascaraLote = new TextBox;
$obTxtMascaraLote->setName      ( "stMascaraLote" );
$obTxtMascaraLote->setId        ( "stMascaraLote" );
$obTxtMascaraLote->setValue     ( $obRCIMConfiguracao->getMascaraLote() );
$obTxtMascaraLote->setRotulo    ( "Máscara de Lote" );
$obTxtMascaraLote->setSize      ( "30" );
$obTxtMascaraLote->setMaxLength ( "" );
$obTxtMascaraLote->setNull      ( false );

$obRdbNumInscAutomatico = new Radio;
$obRdbNumInscAutomatico->setRotulo     ( "Número de Inscrição Imobiliária" );
$obRdbNumInscAutomatico->setName       ( "boNumeroIM" );
$obRdbNumInscAutomatico->setLabel      ( "Automático" );
$obRdbNumInscAutomatico->setValue      ( "true" );
$obRdbNumInscAutomatico->setChecked    ( ( $boNumIM == 'true' ) );
$obRdbNumInscAutomatico->setTitle      ( "Define se o número da inscrição imobiliária será informado ou gerado automaticamente." );
$obRdbNumInscAutomatico->setNull       ( false );

$obRdbNumInscManual = new Radio;
$obRdbNumInscManual->setRotulo      ( "Número de Inscrição Imobiliária" );
$obRdbNumInscManual->setName        ( "boNumeroIM" );
$obRdbNumInscManual->setLabel       ( "Manual" );
$obRdbNumInscManual->setValue       ( "false" );
$obRdbNumInscManual->setChecked     ( ( empty($boNumIM) || $boNumIM == 'false') ? 'false' : "" );
$obRdbNumInscManual->setNull        ( false );

$obTxtMascaraInscImob = new TextBox;
$obTxtMascaraInscImob->setName      ( "stMascaraIM" );
$obTxtMascaraInscImob->setValue     ( $obRCIMConfiguracao->getMascaraIM() );
$obTxtMascaraInscImob->setRotulo    ( "Máscara de Inscrição Imobiliária" );
$obTxtMascaraInscImob->setSize      ( "30" );
$obTxtMascaraInscImob->setMaxLength ( "" );
$obTxtMascaraInscImob->setNull      ( false );

$obCmbOrdemEntrega = new SelectMultiplo();
$obCmbOrdemEntrega->setName   ('inCodAtributoSelecionados');
$obCmbOrdemEntrega->setRotulo ( "Ordem de Entrega" );
$obCmbOrdemEntrega->setNull   ( false );
$obCmbOrdemEntrega->setTitle  ( "Define a ordem dos endereços de entrega." );

// lista de atributos disponiveis
$obCmbOrdemEntrega->SetNomeLista1 ('inCodOrdemDisponivel');
$obCmbOrdemEntrega->setCampoId1   ('empty');
$obCmbOrdemEntrega->setCampoDesc1 ('empty');
$obCmbOrdemEntrega->SetRecord1    ( new RecordSet );

$rsRSOrdemEntrega = $obRCIMConfiguracao->getRSOrdemEntrega();

// lista de atributos selecionados
$obCmbOrdemEntrega->SetNomeLista2 ('inCodOrdemSelecionados');
$obCmbOrdemEntrega->setCampoId2   ('[cod_ordem] - [nom_ordem]');
$obCmbOrdemEntrega->setCampoDesc2 ('nom_ordem');
$obCmbOrdemEntrega->SetRecord2    ( $rsRSOrdemEntrega );

$obCmbOrdemEntrega->setOrdenacao('cod_ordem');

$arMD = array();
$rsMDSelecionados = $obRCIMConfiguracao->getRSMD();
$inX = 0;
$boEncontrou = false;
while ( !$rsMDSelecionados->Eof() ) {
    if ( $rsMDSelecionados->getCampo( "nome" ) == "Trecho" ) {
        $boEncontrou = true;
        break;
    }

    $rsMDSelecionados->proximo();
}

$rsMDSelecionados->setPrimeiroElemento();
if (!$boEncontrou) {
    $arMD[$inX]["nome"] = "Trecho";
    $arMD[$inX]["numero"] = 1;
    $inX++;
}

$boEncontrou = false;
while ( !$rsMDSelecionados->Eof() ) {
    if ( $rsMDSelecionados->getCampo( "nome" ) == "Face de Quadra" ) {
        $boEncontrou = true;
        break;
    }

    $rsMDSelecionados->proximo();
}

$rsMDSelecionados->setPrimeiroElemento();
if (!$boEncontrou) {
    $arMD[$inX]["nome"] = "Face de Quadra";
    $arMD[$inX]["numero"] = 2;
    $inX++;
}

$boEncontrou = false;
while ( !$rsMDSelecionados->Eof() ) {
    if ( $rsMDSelecionados->getCampo( "nome" ) == "Bairro" ) {
        $boEncontrou = true;
        break;
    }

    $rsMDSelecionados->proximo();
}

$rsMDSelecionados->setPrimeiroElemento();
if (!$boEncontrou) {
    $arMD[$inX]["nome"] = "Bairro";
    $arMD[$inX]["numero"] = 3;
    $inX++;
}

$boEncontrou = false;
while ( !$rsMDSelecionados->Eof() ) {
    if ( $rsMDSelecionados->getCampo( "nome" ) == "Localização" ) {
        $boEncontrou = true;
        break;
    }

    $rsMDSelecionados->proximo();
}

$rsMDSelecionados->setPrimeiroElemento();
if (!$boEncontrou) {
    $arMD[$inX]["nome"] = "Localização";
    $arMD[$inX]["numero"] = 4;
    $inX++;
}

$boEncontrou = false;
while ( !$rsMDSelecionados->Eof() ) {
    if ( $rsMDSelecionados->getCampo( "nome" ) == "Tipo de Edificação" ) {
        $boEncontrou = true;
        break;
    }

    $rsMDSelecionados->proximo();
}

$rsMDSelecionados->setPrimeiroElemento();
if (!$boEncontrou) {
    $arMD[$inX]["nome"] = "Tipo de Edificação";
    $arMD[$inX]["numero"] = 5;
}

$rsMD = new RecordSet;
$rsMD->preenche( $arMD );

$obCmbValorMD = new SelectMultiplo();
$obCmbValorMD->setName   ('inCodValorMDSelecionados');
$obCmbValorMD->setRotulo ( "Valores por M²" );
//$obCmbValorMD->setNull   ( false );
$obCmbValorMD->setTitle  ( "Define quais cadastros devem ter a opção de valores por metro quadrado." );

// lista de atributos disponiveis
$obCmbValorMD->SetNomeLista1 ('inCodValorMDDisponivel');
$obCmbValorMD->setCampoId1   ('[numero] - [nome]');
$obCmbValorMD->setCampoDesc1 ('nome');
$obCmbValorMD->SetRecord1    ( $rsMD );

// lista de atributos selecionados
$obCmbValorMD->SetNomeLista2 ('inCodValorMDSelecionados');
$obCmbValorMD->setCampoId2   ('[numero] - [nome]');
$obCmbValorMD->setCampoDesc2 ('nome');
$obCmbValorMD->SetRecord2    ( $rsMDSelecionados );

$arMD = array();
$arAliquota = array();
$rsAliquotaSelecionados = $obRCIMConfiguracao->getRSAliquota();

$inX = 0;
$boEncontrou = false;
while ( !$rsAliquotaSelecionados->Eof() ) {
    if ( $rsAliquotaSelecionados->getCampo( "nome" ) == "Trecho" ) {
        $boEncontrou = true;
        break;
    }

    $rsAliquotaSelecionados->proximo();
}

$rsAliquotaSelecionados->setPrimeiroElemento();
if (!$boEncontrou) {
    $arMD[$inX]["nome"] = "Trecho";
    $arMD[$inX]["numero"] = 1;
    $inX++;
}

$boEncontrou = false;
while ( !$rsAliquotaSelecionados->Eof() ) {
    if ( $rsAliquotaSelecionados->getCampo( "nome" ) == "Face de Quadra" ) {
        $boEncontrou = true;
        break;
    }

    $rsAliquotaSelecionados->proximo();
}

$rsAliquotaSelecionados->setPrimeiroElemento();
if (!$boEncontrou) {
    $arMD[$inX]["nome"] = "Face de Quadra";
    $arMD[$inX]["numero"] = 2;
    $inX++;
}

$boEncontrou = false;
while ( !$rsAliquotaSelecionados->Eof() ) {
    if ( $rsAliquotaSelecionados->getCampo( "nome" ) == "Bairro" ) {
        $boEncontrou = true;
        break;
    }

    $rsAliquotaSelecionados->proximo();
}

$rsAliquotaSelecionados->setPrimeiroElemento();
if (!$boEncontrou) {
    $arMD[$inX]["nome"] = "Bairro";
    $arMD[$inX]["numero"] = 3;
    $inX++;
}

$boEncontrou = false;
while ( !$rsAliquotaSelecionados->Eof() ) {
    if ( $rsAliquotaSelecionados->getCampo( "nome" ) == "Localização" ) {
        $boEncontrou = true;
        break;
    }

    $rsAliquotaSelecionados->proximo();
}

$rsAliquotaSelecionados->setPrimeiroElemento();
if (!$boEncontrou) {
    $arMD[$inX]["nome"] = "Localização";
    $arMD[$inX]["numero"] = 4;
    $inX++;
}

$boEncontrou = false;
while ( !$rsAliquotaSelecionados->Eof() ) {
    if ( $rsAliquotaSelecionados->getCampo( "nome" ) == "Tipo de Edificação" ) {
        $boEncontrou = true;
        break;
    }

    $rsAliquotaSelecionados->proximo();
}

$rsAliquotaSelecionados->setPrimeiroElemento();
if (!$boEncontrou) {
    $arMD[$inX]["nome"] = "Tipo de Edificação";
    $arMD[$inX]["numero"] = 5;
}

$rsAliquota = new RecordSet;
$rsAliquota->preenche( $arMD );

$obCmbAliquotas = new SelectMultiplo();
$obCmbAliquotas->setName   ('inCodAliquotasSelecionados');
$obCmbAliquotas->setRotulo ( "Alíquotas" );
//$obCmbAliquotas->setNull   ( false );
$obCmbAliquotas->setTitle  ( "Define quais cadastros devem ter a opção de alíquotas." );

// lista de atributos disponiveis
$obCmbAliquotas->SetNomeLista1 ('inCodAliquotasDisponivel');
$obCmbAliquotas->setCampoId1   ('[numero] - [nome]');
$obCmbAliquotas->setCampoDesc1 ('nome');
$obCmbAliquotas->SetRecord1    ( $rsAliquota );

// lista de atributos selecionados
$obCmbAliquotas->SetNomeLista2 ('inCodAliquotasSelecionados');
$obCmbAliquotas->setCampoId2   ('[numero] - [nome]');
$obCmbAliquotas->setCampoDesc2 ('nome');
$obCmbAliquotas->SetRecord2    ( $rsAliquotaSelecionados );

$obRdbNavegacaoAutomatico = new Radio;
$obRdbNavegacaoAutomatico->setRotulo     ( "Opções de Navegação Automática" );
$obRdbNavegacaoAutomatico->setName       ( "stNavegacaoAuto" );
$obRdbNavegacaoAutomatico->setLabel      ( "Ativo" );
$obRdbNavegacaoAutomatico->setValue      ( "ativo" );
$obRdbNavegacaoAutomatico->setChecked    ( ( $obRCIMConfiguracao->getNavegacaoAutomatico() == 'ativo' ) );
$obRdbNavegacaoAutomatico->setTitle      ( "Define o modo de navegação entre formulários." );
$obRdbNavegacaoAutomatico->setNull       ( false );

$obRdbNavegacaoManual = new Radio;
$obRdbNavegacaoManual->setRotulo      ( "Opções de Navegação Automática" );
$obRdbNavegacaoManual->setName        ( "stNavegacaoAuto" );
$obRdbNavegacaoManual->setLabel       ( "Inativo" );
$obRdbNavegacaoManual->setValue       ( "inativo" );
$obRdbNavegacaoManual->setChecked     ( ( $obRCIMConfiguracao->getNavegacaoAutomatico() == 'inativo' ) );
$obRdbNavegacaoManual->setNull        ( false );

$rsAtributoImovelSelecionado = new RecordSet;
$rsAtributoImovel = new RecordSet;
$rsAtributoLoteUrbano = new RecordSet;
$rsAtributoLoteUrbanoSelecionado = new RecordSet;
$rsAtributoLoteRural = new RecordSet;
$rsAtributoLoteRuralSelecionado = new RecordSet;
$rsAtributoEdificacaoSelecionado = new RecordSet;
$rsAtributoEdificacao = new RecordSet;

$obRRegra = new RCadastroDinamico;

//lote urbano
$obRRegra->setCodCadastro('2');
$obRRegra->recuperaAtributosSelecionados ( $rsAtributoLoteUrbano );
$arDadosSelecionados = $obRCIMConfiguracao->arAtbLoteUrbano; //configuracao com dados selecionados
$arDados = $rsAtributoLoteUrbano->getElementos(); //todos elementos
$arSelecionado = array(); //lista com dados selecionados
$arNSelecionado = array(); //lista com dados n selecionados
for ( $inX=0; $inX<count( $arDados ); $inX++ ) {
    $boEncontrou = false;
    for ( $inY=0; $inY<count( $arDadosSelecionados ); $inY++ ) {
        if ($arDados[$inX]["cod_atributo"] == $arDadosSelecionados[$inY]) {
            $arSelecionado[] = $arDados[$inX];
            $boEncontrou = true;
            break;
        }
    }

    if (!$boEncontrou) {
        $arNSelecionado[] = $arDados[$inX];
    }
}

$rsAtributoLoteUrbano->preenche( $arNSelecionado );
$rsAtributoLoteUrbanoSelecionado->preenche( $arSelecionado );

$obCmbAtributosLoteUrbano = new SelectMultiplo();
$obCmbAtributosLoteUrbano->setName  ('inCodAtributosLote2');
$obCmbAtributosLoteUrbano->setRotulo( "Atributos Lote Urbano para Consulta Cadastro Imobiliário" );
$obCmbAtributosLoteUrbano->setNull  ( true );
$obCmbAtributosLoteUrbano->setTitle ( "Selecione os atributos a serem utilizados no filtro da consulta do cadastro imobiliário." );

// lista de atributos disponiveis
$obCmbAtributosLoteUrbano->SetNomeLista1('inCodAtributosLoteUrbanoDisponiveis');
$obCmbAtributosLoteUrbano->setCampoId1  ('cod_atributo');
$obCmbAtributosLoteUrbano->setCampoDesc1('nom_atributo');
$obCmbAtributosLoteUrbano->SetRecord1   ( $rsAtributoLoteUrbano );

// lista de atributos selecionados
$obCmbAtributosLoteUrbano->SetNomeLista2('inCodAtributosLoteUrbanoSelecionados');
$obCmbAtributosLoteUrbano->setCampoId2  ('cod_atributo');
$obCmbAtributosLoteUrbano->setCampoDesc2('nom_atributo');
$obCmbAtributosLoteUrbano->SetRecord2   ( $rsAtributoLoteUrbanoSelecionado );
//-------------------------------

//lote rural
$obRRegra->setCodCadastro('3');
$obRRegra->recuperaAtributosSelecionados ( $rsAtributoLoteRural );
$arDadosSelecionados = $obRCIMConfiguracao->arAtbLoteRural; //configuracao com dados selecionados
$arDados = $rsAtributoLoteRural->getElementos(); //todos elementos
unset( $arSelecionado );
unset( $arNSelecionado );
$arSelecionado = array(); //lista com dados selecionados
$arNSelecionado = array(); //lista com dados n selecionados
for ( $inX=0; $inX<count( $arDados ); $inX++ ) {
    $boEncontrou = false;
    for ( $inY=0; $inY<count( $arDadosSelecionados ); $inY++ ) {
        if ($arDados[$inX]["cod_atributo"] == $arDadosSelecionados[$inY]) {
            $arSelecionado[] = $arDados[$inX];
            $boEncontrou = true;
            break;
        }
    }

    if (!$boEncontrou) {
        $arNSelecionado[] = $arDados[$inX];
    }
}

$rsAtributoLoteRural->preenche( $arNSelecionado );
$rsAtributoLoteRuralSelecionado->preenche( $arSelecionado );

$obCmbAtributosLoteRural = new SelectMultiplo();
$obCmbAtributosLoteRural->setName  ('inCodAtributosLote3');
$obCmbAtributosLoteRural->setRotulo( "Atributos Lote Rural para Consulta Cadastro Imobiliário" );
$obCmbAtributosLoteRural->setNull  ( true );
$obCmbAtributosLoteRural->setTitle ( "Selecione os atributos a serem utilizados no filtro da consulta do cadastro imobiliário." );

// lista de atributos disponiveis
$obCmbAtributosLoteRural->SetNomeLista1('inCodAtributosLoteRuralDisponiveis');
$obCmbAtributosLoteRural->setCampoId1  ('cod_atributo');
$obCmbAtributosLoteRural->setCampoDesc1('nom_atributo');
$obCmbAtributosLoteRural->SetRecord1   ( $rsAtributoLoteRural );

// lista de atributos selecionados
$obCmbAtributosLoteRural->SetNomeLista2('inCodAtributosLoteRuralSelecionados');
$obCmbAtributosLoteRural->setCampoId2  ('cod_atributo');
$obCmbAtributosLoteRural->setCampoDesc2('nom_atributo');
$obCmbAtributosLoteRural->SetRecord2   ( $rsAtributoLoteRuralSelecionado );
//-------------------------------------------------

//imovel
$obRRegra->setCodCadastro('4');
$obRRegra->recuperaAtributosSelecionados ( $rsAtributoImovel );

$arDadosSelecionados = $obRCIMConfiguracao->arAtbImovel; //configuracao com dados selecionados
$arDados = $rsAtributoImovel->getElementos(); //todos elementos
unset( $arSelecionado );
unset( $arNSelecionado );
$arSelecionado = array(); //lista com dados selecionados
$arNSelecionado = array(); //lista com dados n selecionados
for ( $inX=0; $inX<count( $arDados ); $inX++ ) {
    $boEncontrou = false;
    for ( $inY=0; $inY<count( $arDadosSelecionados ); $inY++ ) {
        if ($arDados[$inX]["cod_atributo"] == $arDadosSelecionados[$inY]) {
            $arSelecionado[] = $arDados[$inX];
            $boEncontrou = true;
            break;
        }
    }

    if (!$boEncontrou) {
        $arNSelecionado[] = $arDados[$inX];
    }
}

$rsAtributoImovel->preenche( $arNSelecionado );
$rsAtributoImovelSelecionado->preenche( $arSelecionado );

$obCmbAtributosImovel = new SelectMultiplo(); //atributos dinamicos a serem utilizados no filtro da consulta de imoveis
$obCmbAtributosImovel->setName  ('inCodAtributosImovel');
$obCmbAtributosImovel->setRotulo( "Atributos Imóvel para Consulta Cadastro Imobiliário" );
$obCmbAtributosImovel->setNull  ( true );
$obCmbAtributosImovel->setTitle ( "Selecione os atributos a serem utilizados no filtro da consulta do cadastro imobiliário." );

// lista de atributos disponiveis
$obCmbAtributosImovel->SetNomeLista1('inCodAtributosImovelDisponiveis');
$obCmbAtributosImovel->setCampoId1  ('cod_atributo');
$obCmbAtributosImovel->setCampoDesc1('nom_atributo');
$obCmbAtributosImovel->SetRecord1   ( $rsAtributoImovel );

// lista de atributos selecionados
$obCmbAtributosImovel->SetNomeLista2('inCodAtributosImovelSelecionados');
$obCmbAtributosImovel->setCampoId2  ('cod_atributo');
$obCmbAtributosImovel->setCampoDesc2('nom_atributo');
$obCmbAtributosImovel->SetRecord2   ( $rsAtributoImovelSelecionado );
//-------------------------------

//Edificacao
$obRRegra->setCodCadastro('5');
$obRRegra->recuperaAtributosSelecionados ( $rsAtributoEdificacao );

$arDadosSelecionados = $obRCIMConfiguracao->arAtbEdificacao; //configuracao com dados selecionados
$arDados = $rsAtributoEdificacao->getElementos(); //todos elementos
unset( $arSelecionado );
unset( $arNSelecionado );
$arSelecionado = array(); //lista com dados selecionados
$arNSelecionado = array(); //lista com dados n selecionados
for ( $inX=0; $inX<count( $arDados ); $inX++ ) {
    $boEncontrou = false;
    for ( $inY=0; $inY<count( $arDadosSelecionados ); $inY++ ) {
        if ($arDados[$inX]["cod_atributo"] == $arDadosSelecionados[$inY]) {
            $arSelecionado[] = $arDados[$inX];
            $boEncontrou = true;
            break;
        }
    }

    if (!$boEncontrou) {
        $arNSelecionado[] = $arDados[$inX];
    }
}

$rsAtributoEdificacao->preenche( $arNSelecionado );
$rsAtributoEdificacaoSelecionado->preenche( $arSelecionado );

$obCmbAtributosEdificacao = new SelectMultiplo(); //atributos dinamicos a serem utilizados no filtro da consulta de imoveis
$obCmbAtributosEdificacao->setName  ('inCodAtributosEdificacao');
$obCmbAtributosEdificacao->setRotulo( "Atributos Edificação para Consulta Cadastro Imobiliário" );
$obCmbAtributosEdificacao->setNull  ( true );
$obCmbAtributosEdificacao->setTitle ( "Selecione os atributos a serem utilizados no filtro da consulta do cadastro imobiliário." );

// lista de atributos disponiveis
$obCmbAtributosEdificacao->SetNomeLista1('inCodAtributosEdificacaoDisponiveis');
$obCmbAtributosEdificacao->setCampoId1  ('cod_atributo');
$obCmbAtributosEdificacao->setCampoDesc1('nom_atributo');
$obCmbAtributosEdificacao->SetRecord1   ( $rsAtributoEdificacao );

// lista de atributos selecionados
$obCmbAtributosEdificacao->SetNomeLista2('inCodAtributosEdificacaoSelecionados');
$obCmbAtributosEdificacao->setCampoId2  ('cod_atributo');
$obCmbAtributosEdificacao->setCampoDesc2('nom_atributo');
$obCmbAtributosEdificacao->SetRecord2   ( $rsAtributoEdificacaoSelecionado );
//---------------------------------------------------

$obHdnNumeroOrdens = new Hidden;
$obHdnNumeroOrdens->setName  ( "inNumeroOrdens" );
$obHdnNumeroOrdens->setValue ( $rsRSOrdemEntrega->getNumLinhas() );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                   );
$obFormulario->setAjuda      ( "UC-05.01.01" );
$obFormulario->addHidden     ( $obHdnAcao                );
$obFormulario->addHidden     ( $obHdnNumeroOrdens        );
$obFormulario->addTitulo     ( "Dados para Configuração" );
$obFormulario->agrupaComponentes( array( $obRdbCodLocalAutomatico, $obRdbCodLocalManual ) );
$obFormulario->addComponente ( $obTxtMascaraLote         );
$obFormulario->agrupaComponentes( array( $obRdbNumInscAutomatico, $obRdbNumInscManual ) );
$obFormulario->addComponente ( $obTxtMascaraInscImob     );
$obFormulario->addComponente ( $obCmbOrdemEntrega        );

$obFormulario->agrupaComponentes( array( $obRdbNavegacaoAutomatico, $obRdbNavegacaoManual ) );
$obFormulario->addComponente ( $obCmbValorMD );
$obFormulario->addComponente ( $obCmbAliquotas );

$obFormulario->addComponente ( $obCmbAtributosLoteUrbano );
$obFormulario->addComponente ( $obCmbAtributosLoteRural );
$obFormulario->addComponente ( $obCmbAtributosImovel );
$obFormulario->addComponente ( $obCmbAtributosEdificacao );

$obFormulario->OK();
$obFormulario->setFormFocus  ( $obTxtMascaraLote->getId() );
$obFormulario->show();

?>