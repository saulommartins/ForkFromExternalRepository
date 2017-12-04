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
    * Página de processamento oculto para o cadastro de imóvel
    * Data de Criação   : 01/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: OCManterImovel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.20  2007/07/18 14:45:24  dibueno
Bug #9691#

Revision 1.19  2006/11/08 11:02:22  dibueno
Alteração na função para buscar inscricao, pertencente ao componente de IPopUpImovelIntervalo

Revision 1.18  2006/10/09 10:10:44  cercato
alterada consultada de busca do imovel.

Revision 1.17  2006/09/18 10:30:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                 );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php"           );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"             );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php"       );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php"            );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretagem.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"           );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );
include_once( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php");
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovel.class.php"                    );

function BuscaCGM()
{
    $obRCGM = new RCGM;

    $stText = "inNumCGM";
    $stSpan = "inNomCGM";
    if ( $request->get($stText) != "" ) {
        $obRCGM->setNumCGM( $request->get($stText) );
        $obRCGM->consultar( $rsCGM );
        $stNull = "&nbsp;";

        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$request->get($stText).")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

function montaListaProprietario(&$rsListaProprietario)
{
     if ( !$rsListaProprietario->eof() ) {
         $obLista = new Lista;
         $obLista->setMostraPaginacao( false );
         $obLista->setRecordSet( $rsListaProprietario );
         $obLista->setTitulo ("Listas de proprietários");
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("CGM");
         $obLista->ultimoCabecalho->setWidth( 25 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Nome" );
         $obLista->ultimoCabecalho->setWidth( 45 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Quota" );
         $obLista->ultimoCabecalho->setWidth( 26 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();

         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "inNumCGM" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stNomeCGM" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "flQuota" );
         $obLista->commitDado();

         $obLista->addAcao();
         $obLista->ultimaAcao->setAcao( "EXCLUIR" );
         $obLista->ultimaAcao->setFuncao( true );
         $obLista->ultimaAcao->addCampo( "1","inLinha" );
         $obLista->ultimaAcao->setLink( "javascript: excluirDado('excluirProprietario');" );
         $obLista->commitAcao();
         $obLista->montaHTML();
         $stHTML = $obLista->getHTML();
         $stHTML = str_replace("\n","",$stHTML);
         $stHTML = str_replace("  ","",$stHTML);
         $stHTML = str_replace("'","\\'",$stHTML);
     } else {
         $stHTML = "";
     }
     $stJs = "d.getElementById('lsListaProprietarios').innerHTML = '".$stHTML."';";

     return $stJs;
}

function montaListaPromitente(&$rsListaPromitente)
{
     if ( !$rsListaPromitente->eof() ) {
         $obLista = new Lista;
         $obLista->setMostraPaginacao( false );
         $obLista->setRecordSet( $rsListaPromitente );
         $obLista->setTitulo ("Listas de promitentes");
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("CGM");
         $obLista->ultimoCabecalho->setWidth( 25 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Nome" );
         $obLista->ultimoCabecalho->setWidth( 45 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Quota" );
         $obLista->ultimoCabecalho->setWidth( 26 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();

         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "inNumCGM" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stNomeCGM" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "flQuota" );
         $obLista->commitDado();

         $obLista->addAcao();
         $obLista->ultimaAcao->setAcao( "EXCLUIR" );
         $obLista->ultimaAcao->setFuncao( true );
         $obLista->ultimaAcao->addCampo( "1","inLinha" );
         $obLista->ultimaAcao->setLink( "javascript: excluirDado('excluirPromitente');" );
         $obLista->commitAcao();
         $obLista->montaHTML();
         $stHTML = $obLista->getHTML();
         $stHTML = str_replace("\n","",$stHTML);
         $stHTML = str_replace("  ","",$stHTML);
         $stHTML = str_replace("'","\\'",$stHTML);
     } else {
         $stHTML = "";
     }
     $stJs = "d.getElementById('lsListaPromitentes').innerHTML = '".$stHTML."';";

     return $stJs;
}

function montaFormEnderecoEntrega( $boEnderecoEntrega=true , $sessao, $arEnderecoEntrega=array() )
{
    global $stAcao;
    $arEnderecoSessao = Sessao::read('endereco_entrega');
    if ($boEnderecoEntrega == true) {

        $obHdnNomeLogradouro = new Hidden;
        $obHdnNomeLogradouro->setName ( "stNomeLogradouro" );
        $obHdnNomeLogradouro->setValue( $arEnderecoEntrega['nom_logradouro'] );

        $obBscLogradouro = new BuscaInner;
        $obBscLogradouro->setRotulo ( "*Logradouro"                               );
        $obBscLogradouro->setId     ( "campoInnerLogr"                           );
        $obBscLogradouro->obCampoCod->setName  ( "inNumLogradouro"               );
        $obBscLogradouro->obCampoCod->obEvento->setOnChange ( "buscaLogradouro();" );
        $obBscLogradouro->obCampoCod->obEvento->setOnBlur   ( "buscaLogradouro();" );
        $stBusca  = "abrePopUp('".CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm','inNumLogradouro','campoInnerLogr', ''";
        $stBusca .= " ,'".$sessao."&stCadastro=imovel','800','550')";
        $obBscLogradouro->setFuncaoBusca ( $stBusca );
        if ($arEnderecoSessao['cod_logradouro']) {
            $obBscLogradouro->obCampoCod->setValue( $arEnderecoEntrega['cod_logradouro'] );
        }

        $obLblMunicipio = new Label ;
        $obLblMunicipio->setRotulo    ( "Município" );
        $obLblMunicipio->setId        ("stLabelMunicipio");
        $obLblMunicipio->setName      ( "stLabelMunicipio" );
        $obLblMunicipio->setValue     ( $stLabelMunicipio );

        $obLblEstado = new Label ;
        $obLblEstado->setRotulo    ( "Estado" );
        $obLblEstado->setId        ( "stLabelEstado" );
        $obLblEstado->setName      ( "stLabelEstado" );
        $obLblEstado->setValue     ( $stLabelEstado );

        $obHdnCodUF = new Hidden;
        $obHdnCodUF->setName ( "inCodUF" );
        $obHdnCodUF->setValue( $inCodUF  );

        $obHdnCodMunicipio = new Hidden;
        $obHdnCodMunicipio->setName ( "inCodMunicipio" );
        $obHdnCodMunicipio->setValue( $inCodMunicipio  );

        $obTxtNumero = new TextBox;
        $obTxtNumero->setName                ( "stNumero" );
        $obTxtNumero->setRotulo              ( "*Número"   );
        $obTxtNumero->setsize                ( 6          );
        $obTxtNumero->setMaxLength           ( 6          );
        $obTxtNumero->setValue               ( $arEnderecoEntrega['numero'] );

        $obTxtComplemento = new TextBox;
        $obTxtComplemento->setName           ( "stComplemento" );
        $obTxtComplemento->setRotulo         ( "Complemento"   );
        $obTxtComplemento->setSize           ( 53             );
        $obTxtComplemento->setMaxLength      ( 50             );
        $obTxtComplemento->setValue          ( $arEnderecoEntrega['complemento'] );

        $obTxtCaixaPostal = new TextBox;
        $obTxtCaixaPostal->setName           ( "stCaixaPostal" );
        $obTxtCaixaPostal->setRotulo         ( "Caixa Postal"   );
        $obTxtCaixaPostal->setMaxLength      ( 6             );
        $obTxtCaixaPostal->setValue          ( $arEnderecoEntrega['caixa_postal'] );
        $rsCep = new RecordSet;
        $rsBairro = new RecordSet;

        if ($stAcao == "alterar") {
            $obRCIMTrecho = new RCIMTrecho;
            $obRCIMTrecho->setCodigoLogradouro( $arEnderecoEntrega["cod_logradouro"] ) ;
            /* ceps do logradouro */

            if ( $obRCIMTrecho->getCodigoLogradouro() ) {
                $obRCIMTrecho->listarCEP( $rsCep );
                $obRCIMTrecho->listarBairroLogradouro( $rsBairro );
            }
        }

        $obCmbCep  = new Select;
        $obCmbCep->setId           ("cbCep"                         );
        $obCmbCep->setName         ("cbCep"                         );
        $obCmbCep->addOption       ( "", "Selecione"                );
        $obCmbCep->setRotulo       ( "CEP   "                       );
        $obCmbCep->setTitle        ( "Selecione o CEP"              );
        $obCmbCep->setCampoId      ( "cep"                      );
        $obCmbCep->setCampoDesc    ( "cep"                          );
        $obCmbCep->preencheCombo   ( $rsCep                         );
        $obCmbCep->setValue        ( $arEnderecoEntrega["cep"]      );
        $obCmbCep->setNull         ( false                          );
        $obCmbCep->setStyle        ( "width: 220px"                 );

        $obCmbBairro  = new Select;
        $obCmbBairro->setId           ("codBairro"                     );
        $obCmbBairro->setName         ("codBairro"                     );
        $obCmbBairro->addOption       ( "", "Selecione"                );
        $obCmbBairro->setRotulo       ( "Bairro"                       );
        $obCmbBairro->setTitle        ( "Selecione o Bairro"           );
        $obCmbBairro->setCampoId      ( "cod_bairro"                   );
        $obCmbBairro->setCampoDesc    ( "nom_bairro"                   );
        $obCmbBairro->preencheCombo   ( $rsBairro                      );
        $obCmbBairro->setValue        ( $arEnderecoEntrega["cod_bairro"]);
        $obCmbBairro->setNull         ( false                          );
        $obCmbBairro->setStyle        ( "width: 220px"                 );

        $obFormulario = new Formulario;
        $obFormulario->addTitulo    ( "Endereço de entrega" );
        $obFormulario->addHidden    ( $obHdnCodUF           );
        $obFormulario->addHidden    ( $obHdnCodMunicipio    );
        $obFormulario->addComponente( $obBscLogradouro      );
        $obFormulario->addComponente( $obCmbCep             );
        $obFormulario->addComponente( $obCmbBairro          );
        $obFormulario->addComponente( $obTxtCaixaPostal     );
        $obFormulario->addComponente( $obTxtNumero          );
        $obFormulario->addComponente( $obTxtComplemento     );
        $obFormulario->addComponente( $obLblMunicipio );
        $obFormulario->addComponente( $obLblEstado );
        $obFormulario->montaInnerHTML();

        if (empty($arEnderecoEntrega['nom_logradouro'])) {
            $arTmp = "&nbsp;";
        } else {
            $arTmp = $arEnderecoEntrega["nom_logradouro"];
        }

        $stJs = "d.getElementById('spnEnderecoEntrega').innerHTML = '".$obFormulario->getHTML()."';\n";
        $stJs .= "d.getElementById('campoInnerLogr').innerHTML     = '".$arTmp."';\n";
        $stJs .= "f.inNumLogradouro.value                          = '".$arEnderecoEntrega['cod_logradouro']."';\n";

        if ($stAcao == "alterar") {
            //pegando estado e municipio para preencher label
            $obRCIMTrecho = new RCIMTrecho;
            $obRCIMTrecho->setCodigoLogradouro( $arEnderecoEntrega["cod_logradouro"] ) ;
            if ( $obRCIMTrecho->getCodigoLogradouro() ) {
                $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro );

                $stLabelEstado = $rsLogradouro->getCampo ("nom_uf");
                $stJs .= "d.getElementById('stLabelEstado').innerHTML = '".$stLabelEstado."';\n";

                $stLabelMunicipio = $rsLogradouro->getCampo ("nom_municipio");
                $stJs .= "d.getElementById('stLabelMunicipio').innerHTML = '".$stLabelMunicipio."';\n";
            }
        }

    } else {
        $stHtml = "&nbsp;";
        $stJs   = "d.getElementById('spnEnderecoEntrega').innerHTML = '".$stHtml."';";
    }

    return $stJs;
}

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setCadastroLocalizacao( false );

switch ($request->get('stCtrl')) {
    case "PreencheImovel":
        $obTCIMImovel = new TCIMImovel;
        if ( $request->get("inCodImovel") ) {
            $stFiltro = " AND I.inscricao_municipal = ".$request->get("inCodImovel");
            $obTCIMImovel->recuperaInscricaoImobiliario( $rsImoveis, $stFiltro );
            if ( !$rsImoveis->eof() ) {
                $stEnderecoImovel = $rsImoveis->getCampo("logradouro");
                if ( $rsImoveis->getCampo("numero") )
                    $stEnderecoImovel .= ", ".$rsImoveis->getCampo("numero");

                if ( $rsImoveis->getCampo("complemento") )
                    $stEnderecoImovel .= " - ".$rsImoveis->getCampo("complemento");

                $stJs = "jQuery('#stImovel').html('".$stEnderecoImovel."');\n";
            } else {
                $stJs  = "jQuery('#inCodImovel').val('');\n";
                $stJs .= "jQuery('#inCodImovel').focus()\n";
                $stJs .= "jQuery('#stImovel').html('&nbsp;');\n";
                $stJs .= "alertaAviso('@Imóvel informado não existe. (".$request->get("inCodImovel").")','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "d.getElementById('stImovel').innerHTML = '&nbsp;';\n";
        }

        echo $stJs;
        exit;
        break;

    case "PreencheImovelIntervaloInicial":
        $stJs = '';
        $obTCIMImovel = new TCIMImovel;
        if ( $request->get("inCodImovel") ) {
            $stFiltro = "AND ";
            $stFiltro .= " I.inscricao_municipal = ".$request->get("inCodImovel");
            $obTCIMImovel->recuperaInscricaoImobiliario( $rsImoveis, $stFiltro );
            //$obTCIMImovel->debug(); exit;
            if ( $rsImoveis->eof() ) {
                $stJs = "f.inCodImovelInicial.value ='';\n";
                $stJs .= "f.inCodImovelInicial.focus();\n";
                $stJs .= "alertaAviso('@Imóvel informado não existe. (".$request->get("inCodImovel").")','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs = "f.inCodImovelInicial.value ='';\n";
            $stJs .= "f.inCodImovelInicial.focus();\n";
        }

        echo $stJs;
        exit;
        break;

    case "PreencheImovelIntervaloFinal":
        $stJs = '';
        $obTCIMImovel = new TCIMImovel;
        if ( $request->get("inCodImovel") ) {
            $stFiltro = "AND ";
            $stFiltro .= " I.inscricao_municipal = ".$request->get("inCodImovel");
            $obTCIMImovel->recuperaInscricaoImobiliario( $rsImoveis, $stFiltro );
            if ( $rsImoveis->eof() ) {
                $stJs = "f.inCodImovelFinal.value ='';\n";
                $stJs .= "f.inCodImovelFinal.focus();\n";
                $stJs .= "alertaAviso('@Imóvel informado não existe. (".$request->get("inCodImovel").")','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs = "f.inCodImovelFinal.value ='';\n";
            $stJs .= "f.inCodImovelFinal.focus();\n";
        }

        echo $stJs;
        exit;
        break;

    case "LimparSessao":
         $arLimpa = array();
         Sessao::write('proprietarios', $arLimpa);
         Sessao::write('promitentes'  , $arLimpa);
    break;
    case "buscaCGM":
         SistemaLegado::executaFrameOculto( BuscaCGM() );
    break;
    case "preencheProxCombo":
        $stNomeComboLocalizacao = "inCodLocalizacao_".( $request->get("inPosicao") - 1);
        $stChaveLocal = $request->get($stNomeComboLocalizacao);
        $inPosicao = $request->get("inPosicao");
        if ( empty( $stChaveLocal ) and $request->get("inPosicao") > 2 ) {
            $stNomeComboLocalizacao = "inCodLocalizacao_".( $request->get("inPosicao") - 2);
            $stChaveLocal = $request->get($stNomeComboLocalizacao);
            $inPosicao = $request->get("inPosicao") - 1;
        }
        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaLocalizacao->setCodigoVigencia    ( $request->get("inCodigoVigencia") );
        $obMontaLocalizacao->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaLocalizacao->setCodigoLocalizacao ( $arChaveLocal[1] );
        $obMontaLocalizacao->setValorReduzido     ( $arChaveLocal[3] );
        $obMontaLocalizacao->preencheProxCombo( $inPosicao , $request->get("inNumNiveis") );
    break;
    case "preencheCombos":
        $obMontaLocalizacao->setCodigoVigencia( $request->get("inCodigoVigencia")   );
        $obMontaLocalizacao->setCodigoNivel   ( $request->get("inCodigoNivel")      );
        $obMontaLocalizacao->setValorReduzido ( $request->get("stChaveLocalizacao") );
        $obMontaLocalizacao->preencheCombos();
    break;
    case "buscaLote":
        $obRCIMLote = new RCIMLote;
        $obRCIMLote->setCodigoLote( $request->get("inCodigoLote") );
        $obRCIMLote->consultarLote();
        if ( $obRCIMLote->getNumeroLote() ) {
            $stJs = 'd.getElementById("inNumeroLote").innerHTML = "'.$obRCIMLote->getNumeroLote().'";';
        } else {
            $stJs  = 'd.getElementById("inNumeroLote").innerHTML = "&nbsp;";';
            $stJs .= "f.inCodigoLote.value = '';";
            $stJs .= "alertaAviso('@Valor inválido. (".$request->get("inCodigoLote").")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto( $stJs );
    break;
    case "buscaBairro":
        $obRCIMBairro       = new RCIMBairro;
        $obRCIMConfiguracao = new RCIMConfiguracao;
        $obRCIMConfiguracao->listaDadosMunicipio( $arConfiguracao );

        $inCodUF        = $arConfiguracao["cod_uf"];
        $inCodMunicipio = $arConfiguracao["cod_municipio"];
        $obRCIMBairro->setCodigoBairro    ( $request->get("inCodigoBairroEntrega") );
        $obRCIMBairro->setCodigoUF        ( $arConfiguracao["cod_uf"] );
        $obRCIMBairro->setCodigoMunicipio ( $arConfiguracao["cod_municipio"] );
        $obErro = $obRCIMBairro->consultarBairro();

        if ( $obRCIMBairro->getNomeBairro() ) {
            $stJs .= 'f.inCodigoUF.value = '.$inCodUF.';';
            $stJs .= 'f.inCodigoMunicipio.value = '.$inCodMunicipio.';';
            $stJs .= 'd.getElementById("innerBairroEntrega").innerHTML = "'.$obRCIMBairro->getNomeBairro().'";';
        } else {
            $stJs .= 'f.inCodigoUF.value = "";';
            $stJs .= 'f.inCodigoMunicipio.value = "";';
            $stJs .= 'd.getElementById("innerBairroEntrega").innerHTML = "&nbsp;";';
            $stJs .= "alertaAviso('@Valor inválido. (".$request->get("inCodBairroEntrega").")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaLogradouro":
        $obRCIMTrecho     = new RCIMTrecho;
        $rsLogradouro  = new RecordSet;
        $rsCep  = new RecordSet;
        $stJs .= "limpaSelect(f.codBairro,0); \n";
        $stJs .= "limpaSelect(f.cbCep,0); \n";
        $stJs .= "f.codBairro[0] = new Option('Selecione','', 'selected');\n";
        $stJs .= "f.cbCep[0] = new Option('Selecione','', 'selected');\n";

        if ( $request->get("inNumLogradouro", false) ) {
            $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
        } else {
            $obRCIMTrecho->setCodigoLogradouro( $request->get("inNumLogradouro") ) ;
            $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro );
            /* ceps do logradouro*/

            $obRCIMTrecho->listarCEP( $rsCep );
            $obRCIMTrecho->listarBairroLogradouro( $rsBairro );

            if ( $rsLogradouro->eof() ) {
                $stJs .= 'd.getElementById("stLabelMunicipio").innerHTML = "&nbsp;";';
                $stJs .= 'd.getElementById("stLabelEstado").innerHTML = "&nbsp;";';
                $stJs .= 'f.inNumLogradouro.value = "";';
                $stJs .= 'f.inNumLogradouro.focus();';
                $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Valor inválido. (".$request->get("inNumLogradouro").")','form','erro','".Sessao::getId()."');";
            } else {
                $stLabelEstado = $rsLogradouro->getCampo ("nom_uf");
                $stJs .= "d.getElementById('stLabelEstado').innerHTML = '".$stLabelEstado."';\n";

                $stLabelMunicipio = $rsLogradouro->getCampo ("nom_municipio");
                $stJs .= "d.getElementById('stLabelMunicipio').innerHTML = '".$stLabelMunicipio."';\n";

                $stNomeLogradouro = $rsLogradouro->getCampo ("tipo_nome");
                $stJs .= "f.stNomeLogradouro.value = '$stNomeLogradouro';\n";
                $stJs .= "d.getElementById('campoInnerLogr').innerHTML = '".$stNomeLogradouro."';\n";

                /* cep *******************/

                $inContador = 1;
                while ( !$rsCep->eof() ) {
                    $stCep = $rsCep->getCampo( "cep" );
                    $stJs .= "f.cbCep.options[$inContador] = new Option('".$stCep."','".$stCep."'); \n";
                    $inContador++;
                    $rsCep->proximo();
                }
                /* bairro ****************/

                $inContador = 1;
                while ( !$rsBairro->eof() ) {
                    $inCodBairro  = $rsBairro->getCampo( "cod_bairro" );
                    $stNomeBairro = $rsBairro->getCampo( "nom_bairro" );
                    $stJs .= "f.codBairro.options[$inContador] = new Option('".$stNomeBairro."','".$inCodBairro."'); \n";
                    $inContador++;
                    $rsBairro->proximo();
                }
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaCepLogradouro";
        if ( $request->get('inCodigoConfrontacao') != "" ) {
            //RECUPERA O LOGRADOURO DA CONFRONTACAO INFORMADA
            $obRCIMImovel = new RCIMImovel( new RCIMLote );
            $obRCIMImovel->obRCIMConfrontacaoTrecho->setCodigoConfrontacao( $request->get('inCodigoConfrontacao') );
            $obRCIMImovel->roRCIMLote->setCodigoLote( $request->get('inCodigoLote') );
            $obRCIMImovel->obRCIMConfrontacaoTrecho->consultarConfrontacao( $boTransacao );

            //RECUPERA OS CEPS DO LOGRADOURO
            $obRCIMImovel->obRCIMLogradouro->setCodigoLogradouro( $obRCIMImovel->obRCIMConfrontacaoTrecho->obRCIMTrecho->getCodigoLogradouro() );
            $obRCIMImovel->obRCIMLogradouro->listarCEP( $rsCep );

            //PREENCHE COMBO COM OS CEPS SELECIONADOS
            $stJs .= "limpaSelect(f.inCEP,0); \n";
            $stJs .= "f.inCEP[0] = new Option('Selecione','', 'selected');\n";
            $inContador = 1;
            while ( !$rsCep->eof() ) {
                $stCep = $rsCep->getCampo( "cep" );
                $stJs .= "f.inCEP.options[$inContador] = new Option('".$stCep."','".$stCep."'); \n";
                $inContador++;
                $rsCep->proximo();
            }
        } else {
            $stJs  = "limpaSelect(f.inCEP,0); \n";
            $stJs .= "f.inCEP[0] = new Option('Selecione','', 'selected');\n";
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaLogradouroFiltro":
        $obRCIMTrecho     = new RCIMTrecho;
        $rsLogradouro  = new RecordSet;
        if ( $request->get("inNumLogradouro") ) {
            $obRCIMTrecho->setCodigoLogradouro( $request->get("inNumLogradouro") ) ;
            $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro );
        }

        if ( $rsLogradouro->eof() ) {
            $stJs .= 'f.inNumLogradouro.value = "";';
            $stJs .= 'f.inNumLogradouro.focus();';
            $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
            $stJs .= "alertaAviso('@Valor invÃ¡lido. (".$request->get("inNumLogradouro").")','form','erro','".Sessao::getId()."');";
        } else {
            $stLabelEstado = $rsLogradouro->getCampo ("nom_uf");
            $stJs .= "d.getElementById('stLabelEstado').innerHTML = '".$stLabelEstado."';\n";

            $stLabelMunicipio = $rsLogradouro->getCampo ("nom_municipio");
            $stJs .= "d.getElementById('stLabelMunicipio').innerHTML = '".$stLabelMunicipio."';\n";

            $stNomeLogradouro = $rsLogradouro->getCampo ("tipo_nome");
            $stJs .= "f.stNomeLogradouro.value = '$stNomeLogradouro';";
            $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "'.$stNomeLogradouro.'";';
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "incluirProprietario":
        $rsProprietarios = new RecordSet;
        $obRCGM = new RCGM;
        $obRCGM->setNumCGM( $request->get("inNumCGM") );
        $obRCGM->consultar( $rsCGM );

        //VERIFICA SE O CGM JA FOI INFORMADO
        $boErro = false;
        $arProprietariosSessao = Sessao::read('proprietarios');
        if ($arProprietariosSessao) {
        foreach ($arProprietariosSessao as  $inChave => $arProprietarios) {
            if ( $arProprietarios["inNumCGM"] == $request->get("inNumCGM") ) {
                $boErro = true;
                break;
            }
        }}
        if (!$boErro) {
            $arPromitentesSessao = Sessao::read('promitentes');
            if ($arPromitentesSessao) {
            foreach ($arPromitentesSessao as  $inChave => $arProprietarios) {
                if ( $arProprietarios["inNumCGM"] == $request->get("inNumCGM") ) {
                    $boErro = true;
                    break;
                }
            }}
        }//FIM DA VERIFICACAO

        if ($boErro) {
            $stJs = "alertaAviso('CGM já informado!(".$request->get("inNumCGM").")','form','erro','".Sessao::getId()."', '../');";
        } else {
            $stJs  = "f.inNumCGM.value = '';\n";
            $stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;';\n";
            $stJs .= "f.flQuota.value = '';\n";
            $stJs .= "f.boProprietario[0].checked = true;\n";
            $arProprietario = array();
            $arProprietario = array( "inNumCGM"  => $request->get("inNumCGM"),
                                     "stNomeCGM" => $rsCGM->getCampo('nom_cgm'),
                                     "flQuota"   => $request->get("flQuota") );
            if ( $request->get("boProprietario") == "true" ) {
                $arProprietariosSessao     = Sessao::read('proprietarios');
                $arProprietario["inLinha"] = count( $arProprietariosSessao );
                $arProprietariosSessao[]   = $arProprietario;

                $rsProprietarios->preenche( $arProprietariosSessao );
                Sessao::write('proprietarios', $arProprietariosSessao);
                $stJs .= montaListaProprietario( $rsProprietarios  );
            } else {
                $arPromitentesSessao            = Sessao::read('promitentes');
                $arProprietario["inLinha"]      = count( $arPromitentesSessao );
                $arPromitentesSessao[]          = $arProprietario;

                $rsProprietarios->preenche( $arPromitentesSessao );
                Sessao::write('promitentes', $arPromitentesSessao);
                $stJs .= montaListaPromitente( $rsProprietarios  );
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "excluirProprietario":
        $inLinha = $request->get("inLinha") ? $request->get("inLinha") : 0;
        $arNovaListaProprietario = array();
        $inContLinha = 0;
        $arProprietariosSessao = Sessao::read('proprietarios');
        foreach ($arProprietariosSessao as $inChave => $arProprietarios) {
            if ($inChave != $inLinha) {
                $arProprietarios["inLinha"] = $inContLinha++;
                $arNovaListaProprietario[] = $arProprietarios;
            }
        }
        $arProprietariosSessao = array();
        $arProprietariosSessao = $arNovaListaProprietario;
        $rsListaProprietario = new RecordSet;
        $rsListaProprietario->preenche( $arProprietariosSessao );
        Sessao::write('proprietarios', $arProprietariosSessao);

        $stJs = montaListaProprietario( $rsListaProprietario );
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "excluirPromitente":
        $inLinha = $request->get("inLinha") ? $request->get("inLinha") : 0;
        $arNovaListaPromitente = array();
        $inContLinha = 0;
        $arPromitentesSessao = Sessao::read('promitentes');
        foreach ($arPromitentesSessao as $inChave => $arPromitentes) {
            if ($inChave != $inLinha) {
                $arPromitentes["inLinha"] = $inContLinha++;
                $arNovaListaPromitente[] = $arPromitentes;
            }
        }
        $arPromitentesSessao = array();
        $arPromitentesSessao = $arNovaListaPromitente;
        $rsListaPromitente = new RecordSet;
        $rsListaPromitente->preenche( $arPromitentesSessao );
        Sessao::write('promitentes', $arPromitentesSessao);
        $stJs = montaListaPromitente( $rsListaPromitente );
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "validaDataInscricaoLote":
        $stDataLimite = "15000421";
        $stDataInscricaoLote = $request->get("dtDataInscricaoImovel");
        $stDiaInscricaoLote = substr($stDataInscricaoLote,0,2);
        $stMesInscricaoLote = substr($stDataInscricaoLote,3,5);
        $stAnoInscricaoLote = substr($stDataInscricaoLote,6);
        $stDataInscricaoLote = $stAnoInscricaoLote.$stMesInscricaoLote.$stDiaInscricaoLote;
        if ($stDataInscricaoLote < $stDataLimite) {
            $stJs .= "    erro = true;                                                                      ";
            $stJs .= "    f.dtDataInscricaoImovel.value=\"\";                                                 ";
            $stJs .= "    mensagem += \"@Campo Data da Inscrição deve ser posterior a 21/04/1500!\";        ";
            $stJs .= "    alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');                     ";
            $stJs .= "    f.dtDataInscricaoImovel.focus();                                                    ";
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;
    case "buscaCondominio":
        $obRCIMCondominio  = new RCIMCondominio;
        if ( $request->get('inCodigoCondominio') != '' ) {
            $obRCIMCondominio->obRCIMLote = new RCIMLote;
            $obRCIMCondominio->obRCIMLote->setCodigoLote( $request->get('inCodigoLote')        );
            $obRCIMCondominio->setCodigoCondominio      ( $request->get('inCodigoCondominio')  );
            $obErro = $obRCIMCondominio->consultarCondominio( $rsCondominio );
            if ( $obErro->ocorreu() or $rsCondominio->eof() ) {
                $stJs .= 'f.inCodigoCondominio.value = "";';
                $stJs .= 'f.inCodigoCondominio.focus();';
                $stJs .= 'd.getElementById("stNomeCondominio").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Condomínio não encontrado. (".$request->get("inCodigoCondominio").")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("stNomeCondominio").innerHTML = "'.$rsCondominio->getCampo('nom_condominio').'";';
            }
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;
    case "buscaProcesso":
        $obRProcesso  = new RProcesso;
        if ( $request->get('inProcesso') != '' ) {
            list($inProcesso,$inExercicio) = explode("/",$request->get('inProcesso'));
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();

            if ( $obErro->ocorreu() ) {
                $stJs .= 'f.inProcesso.value = "";';
                $stJs .= 'f.inProcesso.focus();';
                $stJs .= "alertaAviso('@Processo não encontrado. (".$request->get("inProcesso").")','form','erro','".Sessao::getId()."');";
            }
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;
    case "buscaCreci":
        $obRCIMCorretagem = new RCIMCorretagem;
        $obRCIMCorretagem->setRegistroCreci( $request->get("stCreciResponsavel"));
        $obRCIMCorretagem->listarCorretagem( $rsCorretagem );

        if ( $rsCorretagem->eof() ) {
            $stJs  = 'f.stCreciResponsavel.value = "";';
            $stJs .= 'f.stCreciResponsavel.focus();';
            $stJs .= 'd.getElementById("stNomeResponsavel").innerHTML = "&nbsp;";';
            $stJs .= "erro = true;\n";
            $stJs .= "mensagem += 'Registro Creci inválido!';\n";
            $stJs .= "SistemaLegado::alertaAviso(mensagem,'form','erro','".Sessao::getId."', '../');\n";
        } else {
            $stJs .= 'd.getElementById("stNomeResponsavel").innerHTML = "'.$rsCorretagem->getCampo("nom_cgm").'";';
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "buscaLocalizacao":
        $obRCIMLocalizacao = new RCIMLocalizacao;
        $obRCIMLocalizacao->setValorComposto( $request->get("stChaveLocalizacao") );
        $obRCIMLocalizacao->listarLocalizacao( $rsLocalizacao );
        if ( $rsLocalizacao->eof() ) {
            $stJs .= 'f.stChaveLocalizacao.value = "";';
            $stJs .= 'f.stChaveLocalizacao.focus();';
            $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "&nbsp;";';
            $stJs .= "alertaAviso('@Chave Localização inválida. (".$request->get("stChaveLocalizacao").")','form','erro','".Sessao::getId()."');";
        } else {
            $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "'.$rsLocalizacao->getCampo("nom_localizacao").'";';
        }
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaCreciCondominio":
        $boOk = true;

        $obRCIMCorretagem = new RCIMCorretagem;
        $obRCIMCorretagem->setRegistroCreci( $request->get("stCreciResponsavel"));
        $obRCIMCorretagem->listarCorretagem( $rsCorretagem );

        if ( $rsCorretagem->eof() ) {
            $stJs  = 'f.stCreciResponsavel.value = "";';
            $stJs .= 'f.stCreciResponsavel.focus();';
            $stJs .= 'd.getElementById("stNomeResponsavel").innerHTML = "&nbsp;";';
            $stJs .= "erro = true;\n";
            $stJs .= "mensagem += 'Registro Creci inválido!';\n";
            $stJs .= "alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');\n";
            $boOk  = false;
        } else {
            $stJs  = 'd.getElementById("stNomeResponsavel").innerHTML = "'.$rsCorretagem->getCampo("nom_cgm").'";';
        }

        if ($boOk) {

            $obRCIMCondominio  = new RCIMCondominio;
            $obRCIMCondominio->setCodigoCondominio( $request->get('inCodigoCondominio')  );
            $obErro = $obRCIMCondominio->consultarCondominio( $rsCondominio );
            if ( $obErro->ocorreu() or $rsCondominio->eof() ) {
                $stJs .= 'f.inCodigoCondominio.value = "";';
                $stJs .= 'f.inCodigoCondominio.focus();';
                $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Condomínio não encontrado. (".$request->get("inCodigoCondominio").")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("campoInner").innerHTML = "'.$rsCondominio->getCampo('nom_condominio').'";';
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "visualizarProcesso":
        $obRCIMImovel = new RCIMImovel( new RCIMLote );

        $arChaveAtributoImovelProcesso =  array( "inscricao_municipal" => $request->get("inscricao_municipal"), "timestamp" => "'".$request->get('timestamp')."'", "cod_processo" => $request->get("cod_processo") );
        $obRCIMImovel->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoImovelProcesso );
        $obRCIMImovel->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosImovelProcesso );

        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo    ( "Processo" );
        $obLblProcesso->setValue     ( str_pad($request->get("cod_processo"),5,"0",STR_PAD_LEFT) . "/" . $request->get("ano_exercicio")  );

        $obMontaAtributosImovelProcesso = new MontaAtributos;
        $obMontaAtributosImovelProcesso->setTitulo     ( "Atributos" );
        $obMontaAtributosImovelProcesso->setName       ( "Atributo_" );
        $obMontaAtributosImovelProcesso->setLabel      ( true );
        $obMontaAtributosImovelProcesso->setRecordSet  ( $rsAtributosImovelProcesso );

        $obFormularioProcesso = new Formulario;
        $obFormularioProcesso->addComponente( $obLblProcesso  );
        $obMontaAtributosImovelProcesso->geraFormulario ( $obFormularioProcesso );
        $obFormularioProcesso->montaInnerHTML();
        $stHtml = $obFormularioProcesso->getHTML();

        $stJs = "d.getElementById('spnAtributosProcesso').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "montaFormEnderecoEntrega":
        if ( $request->get('boEnderecoEntrega') ) {
            $stJs = montaFormEnderecoEntrega( true , Sessao::getId(), Sessao::read('endereco_entrega') );
        } else {
            $stJs = montaFormEnderecoEntrega( false, Sessao::getId() );
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

}
SistemaLegado::LiberaFrames();
?>
