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
    * Página de Frame Oculto para Consulta de Imóveis
    * Data de Criação   : 09/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    * $Id: OCConsultaImovel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.18
*/

/*
$Log$
Revision 1.8  2006/09/18 10:30:20  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                      );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php"                );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"            );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"          );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php"            );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretagem.class.php"            );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"                );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"            );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNaturezaTransferencia.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTransferencia.class.php"         );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php"      );

function montaListaConfrontacao(&$rsListaConfrontacao)
{
     if ( !$rsListaConfrontacao->eof() ) {
         $obLista = new Lista;
         $obLista->setMostraPaginacao( false );
         $obLista->setRecordSet( $rsListaConfrontacao );
         $obLista->setTitulo ("Listas de confrontações");
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 5 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("Ponto Cardeal");
         $obLista->ultimoCabecalho->setWidth( 15 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Tipo" );
         $obLista->ultimoCabecalho->setWidth( 15 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Descrição" );
         $obLista->ultimoCabecalho->setWidth( 30 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Extensão" );
         $obLista->ultimoCabecalho->setWidth( 18 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Testada" );
         $obLista->ultimoCabecalho->setWidth( 18 );
         $obLista->commitCabecalho();

         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stNomePontoCardeal" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stLsTipoConfrotacao" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stDescricao" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setAlinhamento( "DIREITA" );
         $obLista->ultimoDado->setCampo( "flExtensao" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stTestada" );
         $obLista->commitDado();

         $obLista->montaHTML();
         $stHTML = $obLista->getHTML();
         $stHTML = str_replace("\n","",$stHTML);
         $stHTML = str_replace("  ","",$stHTML);
         $stHTML = str_replace("'","\\'",$stHTML);
     } else {
         $stHTML = "";
     }
     $stJs = "d.getElementById('spnListaConfrontacoes').innerHTML = '".$stHTML."';";

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
         $obLista->ultimoCabecalho->setWidth( 5 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("CGM");
         $obLista->ultimoCabecalho->setWidth( 15 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Nome" );
         $obLista->ultimoCabecalho->setWidth( 65 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Quota (%)" );
         $obLista->ultimoCabecalho->setWidth( 15 );
         $obLista->commitCabecalho();

         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "inNumCGM" );
         $obLista->ultimoDado->setAlinhamento( 'RIGHT' );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stNomeCGM" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "flQuota" );
         $obLista->ultimoDado->setAlinhamento( 'RIGHT' );
         $obLista->commitDado();

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
         $obLista->ultimoCabecalho->setWidth( 5 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("CGM");
         $obLista->ultimoCabecalho->setWidth( 15 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Nome" );
         $obLista->ultimoCabecalho->setWidth( 65 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Quota" );
         $obLista->ultimoCabecalho->setWidth( 15 );
         $obLista->commitCabecalho();

         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "inNumCGM" );
         $obLista->ultimoDado->setAlinhamento( 'RIGHT' );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stNomeCGM" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "flQuota" );
         $obLista->ultimoDado->setAlinhamento( 'RIGHT' );
         $obLista->commitDado();

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

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setCadastroLocalizacao( false );

$stCtrl = $request->get("stCtrl");

switch ($stCtrl) {
    case "buscaLocalizacao":

        $stJs = "";

        if (!$_REQUEST["stChaveLocalizacao"]) {
           $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "&nbsp;";';
        } else {
            $obRCIMLocalizacao = new RCIMLocalizacao;
            $obRCIMLocalizacao->setValorComposto( $_REQUEST["stChaveLocalizacao"] );
            $obErro = $obRCIMLocalizacao->consultaCodigoLocalizacao($inCodigoLocalizacao);
            if ( $obErro->ocorreu() || !$inCodigoLocalizacao) {
                $stJs .= 'f.stChaveLocalizacao.value = "";';
                $stJs .= 'f.stChaveLocalizacao.focus();';
                $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "&nbsp;";';

                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["stChaveLocalizacao"].")','form','erro','".Sessao::getId()."');";
            } else {
                $obRCIMLocalizacao->setCodigoLocalizacao( $inCodigoLocalizacao );
                $obErro = $obRCIMLocalizacao->consultarLocalizacao();
                if ( $obErro->ocorreu() ) {
                    $stJs .= 'f.stChaveLocalizacao.value = "";';
                    $stJs .= 'f.stChaveLocalizacao.focus();';
                    $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "&nbsp;";';

                    $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["stChaveLocalizacao"].")','form','erro','".Sessao::getId()."');";
                } else {
                    $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "'.$obRCIMLocalizacao->getNomeLocalizacao().'";';
                }
            }
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "montaListaConfrontacoes":
        $rsListaConfrontacao = new RecordSet;
        $rsListaConfrontacao->preenche( Sessao::read('confrontacoes') );
        $rsListaConfrontacao->addFormatacao("stDescricao","N_TO_BR");
        $stJs =  montaListaConfrontacao( $rsListaConfrontacao );
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "visualizarProcessoImovel":
        $obRCIMImovel = new RCIMImovel( new RCIMLote );
        $arChaveAtributoImovel = array( "inscricao_municipal" => $_REQUEST["inscricao_municipal"], "timestamp" => "$_REQUEST[timestamp]", "cod_processo" => $_REQUEST["cod_processo"] );

        $obRCIMImovel->obRCadastroDinamico->setChavePersistenteValores          ( $arChaveAtributoImovel     );
        $obRCIMImovel->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosImovelProcesso );

        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo( "Processo" );
        $obLblProcesso->setValue ( str_pad($_REQUEST["cod_processo"],5,"0",STR_PAD_LEFT) . "/" . $_REQUEST["ano_exercicio"]  );

        $obMontaAtributosLoteProcesso = new MontaAtributos;
        $obMontaAtributosLoteProcesso->setTitulo   ( "Atributos"                );
        $obMontaAtributosLoteProcesso->setName     ( "Atributo_"                );
        $obMontaAtributosLoteProcesso->setLabel    ( true                       );
        $obMontaAtributosLoteProcesso->setRecordSet( $rsAtributosImovelProcesso );

        $obFormularioProcesso = new Formulario;
        $obFormularioProcesso->addComponente( $obLblProcesso  );
        $obMontaAtributosLoteProcesso->geraFormulario ( $obFormularioProcesso );
        $obFormularioProcesso->montaInnerHTML();
        $stHtml = $obFormularioProcesso->getHTML();

        $stJs = "d.getElementById('spnAtributosProcesso').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "visualizarProcessoLote":
        if ($_REQUEST['stTipoLote'] == 'Urbano') {
            $obRCIMLote = new RCIMLoteUrbano;
        } elseif ($_REQUEST['stTipoLote'] == 'Rural') {
            $obRCIMLote = new RCIMLoteRural;
        }
        $arChaveAtributoLote = array( "cod_lote" => $_REQUEST["cod_lote"], "timestamp" => $_REQUEST["timestamp"], "cod_processo" => $_REQUEST["cod_processo"] );

        $obRCIMLote->obRCadastroDinamico->setChavePersistenteValores          ( $arChaveAtributoLote     );
        $obRCIMLote->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosLoteProcesso );

        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo    ( "Processo" );
        $obLblProcesso->setValue     ( str_pad($_REQUEST["cod_processo"],5,"0",STR_PAD_LEFT) . "/" . $_REQUEST["ano_exercicio"]  );

        $obMontaAtributosLoteProcesso = new MontaAtributos;
        $obMontaAtributosLoteProcesso->setTitulo   ( "Atributos"              );
        $obMontaAtributosLoteProcesso->setName     ( "Atributo_"              );
        $obMontaAtributosLoteProcesso->setLabel    ( true                     );
        $obMontaAtributosLoteProcesso->setRecordSet( $rsAtributosLoteProcesso );

        $obFormularioProcesso = new Formulario;
        $obFormularioProcesso->addComponente( $obLblProcesso  );
        $obMontaAtributosLoteProcesso->geraFormulario ( $obFormularioProcesso );
        $obFormularioProcesso->montaInnerHTML();
        $stHtml = $obFormularioProcesso->getHTML();

        $stJs = "d.getElementById('spnAtributosProcesso').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "visualizarProcessoCondominio":
        $obRCIMCondominio = new RCIMCondominio;
        $arChaveAtributo = array( "cod_condominio" => $_REQUEST["cod_condominio"], "timestamp" => "'$_REQUEST[timestamp]'", "cod_processo" => $_REQUEST["cod_processo"] );

        $obRCIMCondominio->obRCadastroDinamico->setChavePersistenteValores          ( $arChaveAtributo               );
        $obRCIMCondominio->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosCondominioProcesso );

        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo( "Processo" );
        $obLblProcesso->setValue ( str_pad($_REQUEST["cod_processo"],5,"0",STR_PAD_LEFT) . "/" . $_REQUEST["ano_exercicio"]  );

        $obLblArea = new Label;
        $obLblArea->setRotulo( "Área" );
        $obLblArea->setValue ( $_REQUEST["area"] );

        $obMontaAtributosCondominioProcesso = new MontaAtributos;
        $obMontaAtributosCondominioProcesso->setTitulo     ( "Atributos"                    );
        $obMontaAtributosCondominioProcesso->setName       ( "Atributo_"                    );
        $obMontaAtributosCondominioProcesso->setLabel      ( true                           );
        $obMontaAtributosCondominioProcesso->setRecordSet  ( $rsAtributosCondominioProcesso );

        $obFormularioProcesso = new Formulario;
        $obFormularioProcesso->addComponente( $obLblProcesso );
        $obFormularioProcesso->addComponente( $obLblArea );
        $obMontaAtributosCondominioProcesso->geraFormulario ( $obFormularioProcesso );
        $obFormularioProcesso->montaInnerHTML();
        $stHtml = $obFormularioProcesso->getHTML();

        $stJs = "d.getElementById('spnAtributosProcessoCondominio').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "preencheProxCombo":
        $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaLocalizacao->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaLocalizacao->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaLocalizacao->setCodigoLocalizacao ( $arChaveLocal[1] );
        $obMontaLocalizacao->setValorReduzido     ( $arChaveLocal[3] );
        $obMontaLocalizacao->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );
    break;
    case "preencheCombos":
        $obMontaLocalizacao->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaLocalizacao->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaLocalizacao->setValorReduzido ( $_REQUEST["stChaveLocalizacao"] );
        $obMontaLocalizacao->preencheCombos();
    break;
    case "buscaCondominio":
        $obRCIMCondominio  = new RCIMCondominio;
        if ($_POST['inCodCondominio'] != '') {
            $obRCIMCondominio->setCodigoCondominio( $_POST['inCodCondominio']  );
            $obErro = $obRCIMCondominio->consultarCondominio( $rsCondominio );
            if ( $obErro->ocorreu() or $rsCondominio->eof() ) {
                $stJs .= 'f.inCodCondominio.value = "";';
                $stJs .= 'f.inCodCondominio.focus();';
                $stJs .= 'd.getElementById("innerCondominio").innerHTML = "&nbsp;";';
                $stJs .= "SistemaLegado::alertaAviso('@Condomínio não encontrado. (".$_POST["inCodCondominio"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("innerCondominio").innerHTML = "'.$rsCondominio->getCampo('nom_condominio').'";';
            }
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;
    case "buscaLogradouro":
        $obRCIMTrecho  = new RCIMTrecho;
        $rsLogradouro  = new RecordSet;
        if ( empty( $_REQUEST["inNumLogradouro"] ) ) {
            $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
        } else {
            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] ) ;
            $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro );
            if ( $rsLogradouro->eof() ) {
                $stJs .= 'f.inNumLogradouro.value = "";';
                $stJs .= 'f.inNumLogradouro.focus();';
                $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
                $stJs .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST["inNumLogradouro"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomeLogradouro = $rsLogradouro->getCampo("tipo_nome");
                $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "'.$stNomeLogradouro.'";';
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaBairro":
        $obRCIMBairro       = new RCIMBairro;
        $obRCIMConfiguracao = new RCIMConfiguracao;
        $obRCIMConfiguracao->listaDadosMunicipio( $arConfiguracao );

        $inCodUF        = $arConfiguracao["cod_uf"];
        $inCodMunicipio = $arConfiguracao["cod_municipio"];
        $obRCIMBairro->setCodigoBairro    ( $_REQUEST["inCodBairro"] );
        $obRCIMBairro->setCodigoUF        ( $arConfiguracao["cod_uf"] );
        $obRCIMBairro->setCodigoMunicipio ( $arConfiguracao["cod_municipio"] );
        $obErro = $obRCIMBairro->consultarBairro();
        if ( $obRCIMBairro->getNomeBairro() ) {
            $stJs .= 'f.inCodigoUF.value = '.$inCodUF.';';
            $stJs .= 'f.inCodigoMunicipio.value = '.$inCodMunicipio.';';
            $stJs .= 'd.getElementById("innerBairro").innerHTML = "'.$obRCIMBairro->getNomeBairro().'";';
        } else {
            $stJs .= 'f.inCodigoUF.value = "";';
            $stJs .= 'f.inCodigoMunicipio.value = "";';
            $stJs .= 'f.inCodBairro.value = "";';
            $stJs .= 'f.inCodBairro.focus();';
            $stJs .= 'd.getElementById("innerBairro").innerHTML = "&nbsp;";';
            $stJs .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST["inCodBairro"].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaCGM":
        $obRCGM = new RCGM;
        if ($_REQUEST[ 'inNumCGM' ] != "") {
            $obRCGM->setNumCGM( $_REQUEST['inNumCGM'] );
            $obRCGM->consultar( $rsCGM );
            $stNull = "&nbsp;";
            if ( $rsCGM->getNumLinhas() <= 0) {
                $stJs .= 'f.inNumCGM.value = "";';
                $stJs .= 'f.inNumCGM.focus();';
                $stJs .= 'd.getElementById("innerCGM").innerHTML = "'.$stNull.'";';
                $stJs .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST['inNumCGM'].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("innerCGM").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaCreci":
        $obRCIMCorretagem = new RCIMCorretagem;
        $obRCIMCorretagem->setRegistroCreci( $_REQUEST["stCreci"]);
        $obRCIMCorretagem->listarCorretagem( $rsCorretagem );
        if ( $rsCorretagem->eof() ) {
            $stJs  = 'f.stCreci.value = "";';
            $stJs .= 'f.stCreci.focus();';
            $stJs .= 'd.getElementById("innerCreci").innerHTML = "&nbsp;";';
            $stJs .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST["stCreci"].")','form','erro','".Sessao::getId()."');";
        } else {
            $stJs .= 'd.getElementById("innerCreci").innerHTML = "'.$rsCorretagem->getCampo("nom_cgm").'";';
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
}
sistemaLegado::LiberaFrames();
?>
