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
    * PÃ¡gina do Frame oculto para Cadastro de Licença Geral(Uso Solo)
    * Data de CriaÃ§Ã£o   : 07/04/2008

    * @author André Machado

    * @ignore

    * $Id: OCConcederLicencaGeralUsoSolo.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicencaDiversa.class.php"             );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMTipoLicencaDiversa.class.php"         );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"                   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElementoLicencaDiversa.class.php"     );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php"  );

//;

//Define o nome dos arquivos PHP
$stPrograma     = "ConcederLicencaGeralUsoSolo";
$pgFilt         = "FL".$stPrograma.".php"       ;
$pgList         = "LS".$stPrograma.".php"       ;
$pgForm         = "FM".$stPrograma.".php"       ;
$pgFormTipo     = "FM".$stPrograma.".php"   ;
$pgProc         = "PR".$stPrograma.".php"       ;
$pgOcul         = "OC".$stPrograma.".php"       ;
$pgJs           = "JS".$stPrograma.".js"        ;

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

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

switch ($_REQUEST['stCtrl']) {
    case "LimparSessao":
         Sessao::write( "lsElementos", array() );
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

    case "montaVinculos1":
        $obFormulario = new Formulario;
            include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php");

            $obPopUpImovel = new IPopUpImovel;
            $obPopUpImovel->obInnerImovel->setNull (true);

            if ( Sessao::read( "UsoSoloImovel" ) ) {
                $stFiltro = " AND I.inscricao_municipal = ".Sessao::read( "UsoSoloImovel" );

                include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovel.class.php"                    );
                $obTCIMImovel = new TCIMImovel;
                $obTCIMImovel->recuperaInscricaoImobiliario( $rsImoveis, $stFiltro );

                if ( !$rsImoveis->eof() ) {
                     $stEnderecoImovel = $rsImoveis->getCampo("logradouro");
                     if ( $rsImoveis->getCampo("numero") )
                          $stEnderecoImovel .= ", ".$rsImoveis->getCampo("numero");

                if ( $rsImoveis->getCampo("complemento") )
                     $stEnderecoImovel .= " - ".$rsImoveis->getCampo("complemento");

                $obPopUpImovel->obInnerImovel->setValue( $stEnderecoImovel );
                $obPopUpImovel->obInnerImovel->obCampoCod->setValue( Sessao::read( "UsoSoloImovel" ) );
                }
            }

            $obPopUpImovel->geraFormulario ( $obFormulario );

            $obFormulario->montaInnerHtml();
            echo "d.getElementById('spnVinculo').innerHTML = '".$obFormulario->getHtml()."';";
    break;

    case "montaVinculos2":
        $obFormulario = new Formulario;
            if ( Sessao::read( "UsoSoloLogradouro" ) ) {
                $inNumLogradouro = Sessao::read( "UsoSoloLogradouro" );
                include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php"                );
                $obRCIMTrecho  = new RCIMTrecho;

                $obRCIMTrecho->setCodigoLogradouro( $inNumLogradouro ) ;
                $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro );
                $stNomLogradouro = $rsLogradouro->getCampo("tipo_nome");
            }
            //-------------------------------------------------------- COMPONENTES BUSCA LOGRADOURO
            $obBscLogradouro = new BuscaInner;
            $obBscLogradouro->setRotulo ( "Logradouro"                               );
            $obBscLogradouro->setTitle  ( "Logradouro onde o trecho está localizado" );
            $obBscLogradouro->setId     ( "campoInnerLogr"                               );
            $obBscLogradouro->setNull   ( false                                      );
            $obBscLogradouro->setValue  ( $stNomLogradouro );
            $obBscLogradouro->obCampoCod->setName  ( "inNumLogradouro"               );
            $obBscLogradouro->obCampoCod->setValue( $inNumLogradouro );
            $obBscLogradouro->obCampoCod->obEvento->setOnChange( "buscaValor ('buscaLogradouro');" );
            $obBscLogradouro->obCampoCod->obEvento->setOnBlur   ( "buscaValor ('buscaLogradouro');" );
            $stBusca  = "abrePopUp('".CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm','inNumLogradouro','campoInnerLogr',''";
            $stBusca .= " ,'".Sessao::getId()."&stCadastro=imovel','800','550')";
            $obBscLogradouro->setFuncaoBusca                    ( $stBusca );

            $obFormulario->addComponente($obBscLogradouro);
            $obFormulario->montaInnerHtml();
            $stJs .= 'f.inCodMunicipio = ""; ';
            $stJs .= 'f.inCodUF = "";';
            $stJs .=  "d.getElementById('spnVinculo').innerHTML = '".$obFormulario->getHtml()."';";
            echo $stJs;
//    echo "teste2";
    break;

    case "buscaInscricao":
        if ($_REQUEST["inInscricaoEconomica"] != "") {
            $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
            $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST["inInscricaoEconomica"] );
            $obRCEMInscricaoEconomica->listarInscricaoConsulta( $rsLista );

            $inNumLinhas = $rsLista->getNumLinhas();
            if ($inNumLinhas <= 0) {
                $js .= "alertaAviso('@Número de inscrição inválido. (".$_REQUEST["inInscricaoEconomica"].")','form','erro','".Sessao::getId()."');";
                $js .= "d.getElementById('stInscricaoEconomica').innerHTML = '&nbsp;';\n";
                $js .= "f.inInscricaoEconomica.value ='';\n";
                $js .= "f.inInscricaoEconomica.focus();\n";
            } else {
                $stNomeCGM = str_replace ("'", "\'", $rsLista->getCampo("nom_cgm") );

//                $js .= "f.inInscricaoEconomica.focus();\n";
                $js .= 'd.getElementById("stInscricaoEconomica").innerHTML = "'.$stNomeCGM.'";';
            }
        } else {
            $js .= "d.getElementById('stInscricaoEconomica').innerHTML = '&nbsp;';\n";
        }

            sistemaLegado::executaFrameOculto( $js );
        break;

    case "buscaLogradouro":
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php"                );
        $obRCIMTrecho  = new RCIMTrecho;
        $rsLogradouro  = new RecordSet;
        if ( empty( $_REQUEST["inNumLogradouro"] ) ) {
            $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
        } else {
            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] ) ;
            $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro );
            if ( $rsLogradouro->eof() ) {
                $stJs .= 'f.inNumLogradouro.value = "";';
                $stJs .= 'f.stNomLogradouro.value = "";';
                $stJs .= 'f.inNumLogradouro.focus();';
                $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
                $stJs .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST["inNumLogradouro"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomeLogradouro = $rsLogradouro->getCampo("tipo_nome");
                $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "'.$stNomeLogradouro.'";';
                $stJs .= 'f.stNomLogradouro.value = "'. $stNomeLogradouro.'";';
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

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

}
//sistemaLegado::LiberaFrames();
?>
