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
    * PÃ¡gina do Frame oculto para Cadastro de Licença Geral(Diversa)
    * Data de CriaÃ§Ã£o   : 15/04/2005

    * @author Lucas Teixeira Stephanou

    * @ignore

    * $Id: OCConcederLicencaGeral.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
Revision 1.10  2007/05/14 20:32:50  dibueno
Alterações para possibilitar a emissao do alvará diverso

Revision 1.9  2007/02/01 16:41:55  cercato
Bug #7332#

Revision 1.8  2006/09/15 14:33:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicencaDiversa.class.php"             );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMTipoLicencaDiversa.class.php"         );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"                   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElementoLicencaDiversa.class.php"     );

;

//$stPrograma  = "ConcederLicencaGeral";
//$pgJs        = "JS".$stPrograma.".js";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];
//$stUtil = $_GET['stUtil'] ?  $_GET['stUtil'] : $_POST['stUtil'];

function BuscaCGM()
{
    global $_REQUEST;
    $obRCGM = new RCGM;

    $stText = "inNumCGM";
    $stSpan = "inNomCGM";
    if ($_REQUEST[ $stText ] != "") {
        $obRCGM->setNumCGM( $_REQUEST[ $stText ] );
        $obRCGM->consultar( $rsCGM );
        $stNull = "&nbsp;";

        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

function excluirElemento($inLinha)
{
    $arNovaListaElementos = array();
    $inContLinha = 0;
    foreach ( Sessao::read( "lsElementos" ) as $inChave => $arElementos ) {
        if ($inChave != $inLinha) {
            $arElementos["inLinha"] = $inContLinha++;
            $arNovaListaElementos[] = $arElementos;
        }
    }

    Sessao::write( "lsElementos", $arNovaListaElementos );
    $rsListaElementos = new RecordSet;
    $rsListaElementos->preenche( $arNovaListaElementos );
    $stJs = montaListaElementos( $rsListaElementos );

    return $stJs;
}

function montaListaElementos(&$rsListaElementos)
{
    global $request;
    $stAcao = $request->get('stAcao');
    $rsListaElementos->setPrimeiroElemento();
     if ( !$rsListaElementos->eof() ) {
         $obLista = new Lista;
         $obLista->setMostraPaginacao( false );
         $obLista->setRecordSet( $rsListaElementos );
         $obLista->setTitulo ("Lista de Elementos");
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("Código");
         $obLista->ultimoCabecalho->setWidth( 25 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Ocorrência" );
         $obLista->ultimoCabecalho->setWidth( 20 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Nome" );
         $obLista->ultimoCabecalho->setWidth( 40 );
         $obLista->commitCabecalho();
/*         if ($stAcao == "elemento") {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Editar");
            $obLista->ultimoCabecalho->setWidth( 2 );
            $obLista->commitCabecalho();
         }*/
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("Ações");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();

         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "inCodigoElemento" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stOcorrencia" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stNomeElemento" );
         $obLista->commitDado();
         if ($stAcao == "elemento") {
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "ALTERAR" );
            $obLista->ultimaAcao->addCampo( "1","inCodigoElemento" );
            $obLista->ultimaAcao->addCampo( "2","stNomeElemento"   );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "javascript:montaAtributosElementos();" );
            $obLista->commitAcao();
         }
         $obLista->addAcao();
         $obLista->ultimaAcao->setAcao( "EXCLUIR" );
         $obLista->ultimaAcao->setFuncao( true );
         $obLista->ultimaAcao->addCampo( "1","inLinha" );
         $obLista->ultimaAcao->setLink( "javascript: excluirDado('excluirElemento');" );
         $obLista->commitAcao();
         $obLista->montaHTML();
         $stHTML = $obLista->getHTML();
         $stHTML = str_replace("\n","",$stHTML);
         $stHTML = str_replace("  ","",$stHTML);
         $stHTML = str_replace("'","\\'",$stHTML);
     } else {
         $stHTML = "";
     }
     $stJs = "d.getElementById('spnListaElementos').innerHTML = '".$stHTML."';";

     return $stJs;
}

switch ($stCtrl) {
    case "LimparSessao":
         Sessao::write( "lsElementos", array() );
    break;

    case "montaAtributosElementos":
        $obRCEMLicencaDiversa   = new RCEMLicencaDiversa;
        /* Atributos Dinamicos */
        $obRCEMLicencaDiversa->obRCadastroDinamicoElemento->setChavePersistenteValores( array("cod_elemento"=>$_REQUEST["stCodigoElemento"]) );
        if ( ($_REQUEST["stAcao"] == "elemento") && (in_array($_REQUEST["stCodigoElemento"], Sessao::read( "arCodElementos" ) ))) {
            $obRCEMLicencaDiversa->obRCadastroDinamicoElemento->recuperaAtributosSelecionadosValores( $rsAtributosElementos );
        } else {
            $obRCEMLicencaDiversa->obRCadastroDinamicoElemento->recuperaAtributosSelecionados( $rsAtributosElementos );
        }

        $obMontaAtributosElemento = new MontaAtributos;
        $obMontaAtributosElemento->setTitulo     ( "Atributos do Elemento" );
        $obMontaAtributosElemento->setName       ( "AtributoElemento_" );
        $arlsElementosSessao = Sessao::read( "lsElementos" );
        foreach ($arlsElementosSessao as $key => $chave) {
            if ($arlsElementosSessao[$key]["inCodigoElemento"] == $_REQUEST["stCodigoElemento"]) {
                $inLinha = $key;
            }
        }

        foreach ($rsAtributosElementos->arElementos as $key => $value) {
            foreach ($rsAtributosElementos->arElementos[$key] as $chave => $valor) {
                $rsAtributosElementos->arElementos[$key]["valor"] = $arlsElementosSessao[$inLinha]["elementos"][$_REQUEST["stCodigoElemento"]][$rsAtributosElementos->arElementos[$key]["cod_atributo"]];
            }
        }

        $obMontaAtributosElemento->setRecordSet ( $rsAtributosElementos );
        $obMontaAtributosElemento->recuperaValores();

        $obFormulario = new Formulario;
        $obFormulario->addTitulo ( 'Dados para '.$_REQUEST["stNomElemento"] );

        $obMontaAtributosElemento->geraFormulario($obFormulario);

        $inNumeroAtributos = count($obMontaAtributosElemento->arNomeInput);
        // salva array com nome dos atributos
        $obHdnArrayAtributos= new Hidden;
        $obHdnArrayAtributos->setName   ("arNomeAtributosElemento");
        $obHdnArrayAtributos->setId     ("arNomeAtributosElemento");
        $obHdnArrayAtributos->setValue  (implode(",",$obMontaAtributosElemento->arNomeInput));

        $obBtnIncluirElemento = new Button;
        $obBtnIncluirElemento->setName( "stIncluirElemento" );
        $obBtnIncluirElemento->setValue( "Incluir" );
        $obBtnIncluirElemento->obEvento->setOnClick( "incluirElemento();" );

        $obBtnLimparElemento= new Button;
        $obBtnLimparElemento->setName( "stLimparElemento" );
        $obBtnLimparElemento->setValue( "Limpar" );
        $obBtnLimparElemento->obEvento->setOnClick( "limparElementos();" );

        $obFormulario->addHidden    ($obHdnArrayAtributos);
        $obFormulario->defineBarra  ( array( $obBtnIncluirElemento, $obBtnLimparElemento ),"","" );
        $obFormulario->montaInnerHtml();

        $stHTML = $obFormulario->getHTML();
        $stHTML = str_replace("\n","",$stHTML);
        $stHTML = str_replace("  ","",$stHTML);
        $stHTML = str_replace("\"","'",$stHTML);
        $stHTML = str_replace("'","\\'",$stHTML);

        $stJs .= 'd.getElementById("spnAtributosElemento").innerHTML = "'.$stHTML.'";';
        $stJs .= 'd.getElementById("inNumAtributos").value = "'.$inNumeroAtributos.'"; ';
        $stJs .= 'colocaValoresAtributos('.implode(",",$obMontaAtributosElemento->arNomeInput).')';
        sistemaLegado::executaFrameOculto($stJs);

    break;

    case "buscaCGM":
    if ($_REQUEST["inNumCGM"] != "") {
        $obRCGMPessoaFisica = new RCGMPessoaFisica;
        $obRCGMPessoaFisica->setNumCGM ( $_REQUEST["inNumCGM"] );
        $stWhere = " numcgm = ".$obRCGMPessoaFisica->getNumCGM();
        $null = "&nbsp;";
        $obRCGMPessoaFisica->consultarCGM($rsCgm, $stWhere);
        $inNumLinhas = $rsCgm->getNumLinhas();
        if ($inNumLinhas <= 0) {
            $js .= 'f.inNumCGM.value = "";';
            $js .= 'f.inNumCGM.focus();';
            $js .= 'd.getElementById("inNomCGM").innerHTML = "'.$null.'";';
            $js .= "alertaAviso('@CGM deve ser de Pessoa Fisica. (".$_REQUEST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
        } else {
            $stNomCgm = $rsCgm->getCampo("nom_cgm");
            $js .= 'd.getElementById("inNomCGM").innerHTML = "'.$stNomCgm.'";';
        }
    } else {
        $js .= 'f.inNumCGM.value = "";';
        $js .= 'f.inNumCGM.focus();';
        $js .= 'd.getElementById("inNomCGM").innerHTML = "&nbsp;";';
    }
    sistemaLegado::executaFrameOculto($js);

    break;
    case "incluirElemento":
        /**
        * @desc Monta array com os valores dos atributos do elemento inserido
        */
        // Verifica se qual a linha do elemento na edição de elemento

        $stAcao = $request->get('stAcao');
        $arlsElementosSessao = Sessao::read( "lsElementos" );
        if ($stAcao == "elemento") {
            foreach ($arlsElementosSessao as $key => $chave) {
                if ($arlsElementosSessao[$key]["inCodigoElemento"] == $_REQUEST["stCodigoElemento"]) {
                    $inLinhaDelete = $arlsElementosSessao[$key]["inLinha"];
                }
            }
        }

        $stNomAtribElem     = $_REQUEST["arNomeAtributosElemento"]  ;
        $stCodElemento      = $_REQUEST["stCodigoElemento"]         ;

        $arAtribElem        = explode(",",$stNomAtribElem)          ;

        $arAtribElemValor[$stCodElemento] = array()                 ;
        // array de indice =  a cod_elemento criado com valores dos atributos.
        for ( $inCount =0; $inCount < count($arAtribElem); $inCount++) {
            #$arAtribElemValor[$stCodElemento][substr( $arAtribElem[$inCount],-3,1)] = $_REQUEST[$arAtribElem[$inCount]] ;
            $cod_atributo_atual = explode('_', $stNomAtribElem );
            $cod_atributo_atual = $cod_atributo_atual[1];
            $arAtribElemValor[$stCodElemento][ $cod_atributo_atual ] = $_REQUEST[$arAtribElem[$inCount]] ;
        }

        $rsElementos = new RecordSet;
        $obRCEMElemento = new RCEMElemento( $obAtividade );
        $obRCEMElemento->setCodigoElemento( $_REQUEST["stCodigoElemento"] );
        $obRCEMElemento->listarElementoTipoLicencaDiversa( $rsElementos );

        //VERIFICA SE O Elemento ja foi informado
        $boErro = false;

        foreach ($arlsElementosSessao as  $inChave => $arElementos) {
            if ($arElementos["inCodigoElemento"] == $_REQUEST["stCodigoElemento"]) {
                if ($_REQUEST["stAcao"] == "elemento") {
                    excluirElemento($inLinhaDelete);
                    $boErro = false;
                } else {
                    $boErro = true;
                }
                break;
            }
        }

        if ($boErro) {
            $stJs .= "f.boElemento.value = '0';\n";
            $stJs .= "alertaAviso('Elemento já informado!(".$_REQUEST["stCodigoElemento"].")','form','erro','".Sessao::getId()."', '../');";
        } else {
            $stJs  = "f.stCodigoElemento.value = '';\n";
            $stJs  = "f.boElemento.value = '1';\n";
            $stJs .= "d.getElementById('spnAtributosElemento').innerHTML = '';\n";
            $stJs .= "d.getElementById('cmbElementos').selectedIndex = '0';\n";

            $arElementos = array( "inCodigoElemento" => $_REQUEST["stCodigoElemento"],
                                     "stOcorrencia" => count($arlsElementosSessao)+1 ,
                                     "stNomeElemento" => $rsElementos->getCampo('nom_elemento') ,
                                     "elementos" => $arAtribElemValor
                                );
            $arElementos["inLinha"] = count( $arlsElementosSessao );
            $arlsElementosSessao[] = $arElementos;

            Sessao::write( "lsElementos", $arlsElementosSessao );
            $rsElementos->preenche( $arlsElementosSessao );
            $stJs .= montaListaElementos( $rsElementos  );
            Sessao::write( "inNumElementos", Sessao::read( "inNumElementos" ) + 1 );
        }
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "excluirElemento":
        sistemaLegado::executaFrameOculto( excluirElemento($_REQUEST["inLinha"]) );

    break;
    case "validaElementos":
    if ( count(Sessao::read( "lsElementos")) <= 0 ) {
        $stJs .= "alertaAviso('@Ao menos 1(um) elemento deve ser informado! .','form','erro','".Sessao::getId()."');";
    } else {
        $stJs .= "f.submit();\r\n";
        $stJs .= "f.reset();\r\n";
    }
    sistemaLegado::executaFrameOculto($stJs);
    break;
case "carregarElementos":
        /**
        * @desc Monta array com os valores dos atributos do elemento inserido
        */
        $stNomAtribElem     = $_REQUEST["arNomeAtributosElemento"]  ;
        $stCodElemento      = $_REQUEST["stCodigoElemento"]         ;

        $arAtribElem        = explode(",",$stNomAtribElem)          ;

        $arAtribElemValor[$stCodElemento] = array()                 ;
        // array de indice =  a cod_elemento criado com valores dos atributos.
        for ($inCount =0;$inCount < count($arAtribElem);$inCount++) {
            $arAtribElemValor[$stCodElemento][substr($arAtribElem[$inCount],-5,3)]= $_REQUEST[$arAtribElem[$inCount]];
        }

        $rsElementos = new RecordSet;
        $obRCEMElemento = new RCEMElemento( $obAtividade );
        $obRCEMElemento->setCodigoElemento( $_REQUEST["stCodigoElemento"] );
        $obRCEMElemento->listarElementoTipoLicencaDiversa( $rsElementos );

        //VERIFICA SE O Elemento ja foi informado
        $boErro = false;
        $arlsElementosSessao = Sessao::read("lsElementos");
        foreach ($arlsElementosSessao as  $inChave => $arElementos) {
            if ($arElementos["inCodigoElemento"] == $_REQUEST["stCodigoElemento"]) {
                $boErro = true;
                break;
            }
        }

        if ($boErro) {
            $stJs .= "f.boElemento.value = '0';\n";
            $stJs .= "alertaAviso('Elemento já informado!(".$_REQUEST["stCodigoElemento"].")','form','erro','".Sessao::getId()."', '../');";
        } else {
            $stJs  = "f.stCodigoElemento.value = '';\n";
            $stJs  = "f.boElemento.value = '1';\n";
            $stJs .= "d.getElementById('spnAtributosElemento').innerHTML = '';\n";
            $stJs .= "d.getElementById('cmbElementos').selectedIndex = '0';\n";

            $arElementos = array( "inCodigoElemento" => $_REQUEST["stCodigoElemento"],
                                     "stOcorrencia"     => count($sessao->transf4["lsElementos"])+1 ,
                                     "stNomeElemento"   => $rsElementos->getCampo('nom_elemento') ,
                                     "elementos" => $arAtribElemValor
                                );
            $arElementos["inLinha"] = count( $arlsElementosSessao );
            $arlsElementosSessao[] = $arElementos;
            Sessao::write( "lsElementos", $arlsElementosSessao );
            $rsElementos->preenche( $arlsElementosSessao );
            $stJs .= montaListaElementos( $rsElementos  );
            Sessao::write( "inNumElementos", Sessao::read( "inNumElementos" ) + 1 );
        }
        sistemaLegado::executaFrameOculto($stJs);
    break;
 case "montaAlteracaoAtributosElementos":
        $obRCEMLicencaDiversa   = new RCEMLicencaDiversa;
        $obRCEMLicencaDiversa->setCodigoLicenca( $_REQUEST["inCodigoLicenca"] );
        $obRCEMLicencaDiversa->listarElementoLicencaDiversa($rsElementosdaLicenca);
        /* Monta Arrray com os elementos, no formato lsElementos da sessao*/
        $inCount = 0 ;
        /* Array com Elementos da Licenca e limpa a sessao*/
        Sessao::write( 'arCodElementos', array() );
        $arCodElementos = array();
        while ( !$rsElementosdaLicenca->eof() ) {
        // stributos dinamicos do elemento
            array_push($arCodElementos,$rsElementosdaLicenca->getCampo('cod_elemento'));
            $rsAtributosLicencaDiversa = new RecordSet;
            $obRCEMLicencaDiversa->obRCadastroDinamicoElemento->setChavePersistenteValores  (
                                array   (
                                "cod_elemento"  => $rsElementosdaLicenca->getCampo ( 'cod_elemento' ) ,
                                "cod_tipo"      => $rsElementosdaLicenca->getCampo ( 'cod_tipo'     ) ,
                                "cod_licenca"   => $rsElementosdaLicenca->getCampo ( 'cod_licenca'  ) ,
                                "exercicio"     => $rsElementosdaLicenca->getCampo ( 'exercicio'    ) ,
                                "ocorrencia"    => $rsElementosdaLicenca->getCampo ( 'ocorrencia'   )
                                ) );
            $obRCEMLicencaDiversa->obRCadastroDinamicoElemento->recuperaAtributosSelecionadosValores( $rsAtributosLicencaDiversa );
            /* Monta array com atributos do elemento */
            while ( !$rsAtributosLicencaDiversa->eof() ) {
                $arAtributos[$rsElementosdaLicenca->getCampo ( 'cod_elemento' )][$rsAtributosLicencaDiversa->getCampo('cod_atributo')] = $rsAtributosLicencaDiversa->getCampo('valor');
                $rsAtributosLicencaDiversa->proximo();
            }
            $arElementos[$inCount] =
            array( "inCodigoElemento" => $rsElementosdaLicenca->getCampo( 'cod_elemento' ) ,
                   "stOcorrencia"     => $rsElementosdaLicenca->getCampo( 'ocorrencia'   ) ,
                   "stNomeElemento"   => $rsElementosdaLicenca->getCampo( 'nom_elemento' ) ,
                   "elementos"        => array(
                                                $rsElementosdaLicenca->getCampo('cod_elemento')=>$arAtributos[$rsElementosdaLicenca->getCampo('cod_elemento')]
                        ),
                    "inLinha"         => count( $arElementos ),
                    "acao"            => 'a'
                 );
            $inCount++;
            $rsElementosdaLicenca->proximo();
//        $arElementos[$inCount][$rsElementosdaLicenca->getCampo('cod_elemento')]["inLinha"] = count( $arElementos );
        }

        Sessao::write( "lsElementos", $arElementos );

        $rsElementos = new RecordSet;
        $rsElementos->preenche( $arElementos );

        $stJs .= montaListaElementos( $rsElementos  );
        Sessao::write( "inNumElementos", count($arElementos) - 1 );
        Sessao::write( 'arCodElementos', $arCodElementos );
        $stJs .= "f.boElemento.value = '1';\n";

        sistemaLegado::executaFrameOculto($stJs);
//        echo $stJs;
        // salva codigos dos elementos na sessao
    break;
    case "selecionaFormulario":

    $obRCEMTipoLicencaDiversa = new RCEMTipoLicencaDiversa;
    $obRCEMTipoLicencaDiversa->setCodigoTipoLicencaDiversa($_REQUEST['inCodigoTipoLicenca']);
    $obRCEMTipoLicencaDiversa->listarTipoLicencaDiversa($rsTiposLicenca);

    //$pgProx = CAM_GT_ECONOMICO."instancias/licenca/FMConcederLicencaGeralTipo.php";
    $pgProx = CAM_GT_ECONOMICO."instancias/licenca/FMConcederLicencaAtividade.php";

  $inSessaoCorrente = session_id();
  session_regenerate_id();
  $inNovaSessao = session_id();
  session_start( $inSessaoCorrente );

  $stFiltroEmissaoOP  = "&acao=462&modulo=14&funcionalidade=141";
 print "<script type=\"text/javascript\">
                                window.location.target = 'telaPrincipal';
                                location.href='".$pgProx."?&stAcao=incluir".$stFiltroEmissaoOP."';
                                //mudaMenu         ( \"Conceder Outras Licenças\",\"141\" );
                           </script>";

  SistemaLegado::alertaAviso($pgProx."?&stAcao=incluir".$stFiltroEmissaoOP, "", "incluir", "aviso", Sessao::getId(), "../");
//  sistemaLegado::alertaAviso($pgProx.'?'.Sessao::getId(), "" , "", "",$sessao, "../");

}
sistemaLegado::LiberaFrames();
?>
