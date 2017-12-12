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
    * Classe de regra de negócio para arrecadacao grupo
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * $Id: OCManterPermissoes.php 63839 2015-10-22 18:08:07Z franver $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.7  2006/09/15 11:10:42  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RUsuario.class.php"       );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"      );
include_once ( CAM_GT_ARR_NEGOCIO."RARRPermissao.class.php"  );

$stCtrl = $_REQUEST['stCtrl'];

$stJs = "";

$obRARRGrupo = new RARRGrupo;

function BuscaUsuario()
{
    global $_REQUEST;
    $obRUsuario = new RUsuario;

    $stText = "inNumCGM";
    $stSpan = "inUsername";
    if ($_REQUEST[ $stText ] != "") {
        $obRUsuario->obRCGM->setNumCGM( $_REQUEST[ $stText ] );
        $obRUsuario->consultarUsuario( $rsUsuario );
        $stNull = "&nbsp;";

        if ( $rsUsuario->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsUsuario->getCampo('username')?$rsUsuario->getCampo('username'):$stNull).'";';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

function montaListaGrupos($rsListaGrupos)
{
    $request = new Request($_REQUEST);
    $stAcao = $request->get("stAcao");

    $rsListaGrupos->setPrimeiroElemento();
    if ( !$rsListaGrupos->eof() ) {
         $obLista = new Lista;
         $obLista->setMostraPaginacao( false );
         $obLista->setRecordSet( $rsListaGrupos );
         $obLista->setTitulo ("Lista de Grupos");
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("Código");
         $obLista->ultimoCabecalho->setWidth( 30 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Descrição" );
         $obLista->ultimoCabecalho->setWidth( 40 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Exercício" );
         $obLista->ultimoCabecalho->setWidth( 20 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();

         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "codgrupo" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "descricao" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "exercicio" );
         $obLista->commitDado();

         $obLista->addAcao();
         $obLista->ultimaAcao->setAcao( "EXCLUIR" );
         $obLista->ultimaAcao->setFuncao( true );
         $obLista->ultimaAcao->addCampo( "1","inLinha" );
         $obLista->ultimaAcao->setLink( "javascript: excluirDado('excluirGrupo');" );
         $obLista->commitAcao();
         $obLista->montaHTML();
         $stHTML = $obLista->getHTML();
         $stHTML = str_replace("\n","",$stHTML);
         $stHTML = str_replace("  ","",$stHTML);
         $stHTML = str_replace("'","\\'",$stHTML);
     } else {
         $stHTML = "";
     }
     $stJs = "d.getElementById('spnGrupos').innerHTML = '".$stHTML."';";

     return $stJs;
}
function excluirGrupo($inLinha)
{
        $arNovaListaGrupo = array();
        $inContLinha = 0;
        foreach ( Sessao::read( "grupos" ) as $inChave => $arGrupos ) {
            if ($inChave != $inLinha) {
                $arGrupos["inLinha"] = $inContLinha;
                $arNovaListaGrupo[] = $arGrupos;
                $inContLinha++;
            }
        }

        Sessao::write( "grupos", $arNovaListaGrupo );

        $rsListaGrupos = new RecordSet;
        $rsListaGrupos->preenche( $arNovaListaGrupo );
        $stJs = montaListaGrupos( $rsListaGrupos );

    return $stJs;
}

function BuscarCredito($stParam1, $stParam2)
{
    ;
    $obRegra = new RARRGrupo;

    if ($_REQUEST[$stParam1]) {
        $arDados = explode("/", $_REQUEST[$stParam1]);
        $stMascara = "";
        $obRARRGrupo = new RARRGrupo;
        $obRARRGrupo->RecuperaMascaraGrupoCredito( $stMascara );
        $stMascara .= "/9999";

        if ( strlen($_REQUEST[$stParam1]) < strlen($stMascara) ) {
            $stJs = 'f.'.$stParam1.'.value= "";';
            $stJs .= 'f.'.$stParam1.'.focus();';
            $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
            $stJs .= "alertaAviso('@Código Grupo/Ano exercício incompleto. (".$_REQUEST[$stParam1].")', 'form','erro','".Sessao::getId()."');";
        } else {
            $obRARRGrupo->setCodGrupo( $arDados[0] );
            $obRARRGrupo->setExercicio( $arDados[1] );

            $obRARRGrupo->listarGrupos( $rsListaGrupo );
            if ( $rsListaGrupo->Eof() ) {
                $stJs = 'f.'.$stParam1.'.value= "";';
                $stJs .= 'f.'.$stParam1.'.focus();';
                $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Código Grupo/Ano exercício inválido. (".$_REQUEST[$stParam1].")', 'form','erro','".Sessao::getId()."');";
            } else {
                $stJs = 'd.getElementById("'.$stParam2.'").innerHTML = "'.$rsListaGrupo->getCampo("descricao").'";';
            }
        }
    } else {
        $stJs = 'f.inCodGrupo.value= "";';
        $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

switch ($_REQUEST ["stCtrl"]) {
    case "BuscaCodCredito":
        $stJs = BuscarCredito( "inCodGrupo", "stGrupo" );
        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaUsuario":
         $stJs .= BuscaUsuario();
        break;

    case "buscaGrupo":
        if ( !empty($_REQUEST["inCodGrupo"]) ) {
            $obRARRGrupo->setCodGrupo($_REQUEST["inCodGrupo"]);
            $obRARRGrupo->consultarGrupo();

            $inCodGrupo     = $obRARRGrupo->getCodGrupo () ;
            $stDescricao    = $obRARRGrupo->getDescricao() ;

            if ( !empty($stDescricao) ) {
                $stJs .= "d.getElementById('stGrupo').innerHTML = '".$stDescricao."';\n";
            } else {
                $stJs .= "f.inCodGrupo.value ='';\n";
                $stJs .= "f.inCodGrupo.focus();\n";
                $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";
                $stJs .= "alertaAviso('@Grupo informado nao existe. (".$_REQUEST["inCodGrupo"].")','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";
        }
        break;

    case "incluirGrupo":
        /**
        * @desc Monta array com os valores dos atributos do elemento inserido
        */
        // Grupo informafo
        $inNumCGM     = $_REQUEST["inNumCGM"]   ;
        if ($_REQUEST["inCodGrupo"] == "") {
            $stJs .= "alertaAviso('Grupo de Créditos está vazio.','form','erro','".Sessao::getId()."', '../');";
        } else {
            $arDadosGrupoCredito = explode( "/", $_REQUEST["inCodGrupo"] );

            // pegar valores do Grupo acima
            $inCodGrupo = $arDadosGrupoCredito[0];
            $obRARRGrupo->setCodGrupo( $arDadosGrupoCredito[0] );
            $obRARRGrupo->setExercicio( $arDadosGrupoCredito[1] );
            $obRARRGrupo->consultarGrupo();
            $stDescricao    = $obRARRGrupo->getDescricao();
            $stExercicio    = $obRARRGrupo->getExercicio();

            $rsGrupos = new RecordSet;
            //VERIFICA SE O Elemento ja foi informado
            $boErro = false;

            foreach ( Sessao::read( "grupos" ) as $arGrupos ) {
                 $arAuxiliar= explode ("/", $_REQUEST["inCodGrupo"]);
                 if ($arGrupos["codgrupo"] == $arAuxiliar[0] and  $arGrupos["exercicio"]==$arAuxiliar[1]) {
                    $stMensagem = "Grupo de Crédito já informado!(".$_REQUEST["inCodGrupo"]." - ".$stDescricao."/".$stExercicio.")";
                    $boErro = true;
                break;
                } else {
                    $boErro = false;
                }
            }

            if ($boErro) {
                $stJs .= "f.inCodGrupo.value = '';\n";
                $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";
                $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."', '../');";
            } else {
                $stJs  = "f.inCodGrupo.value = '';\n";
                $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";

                $arGrupos = array(
                    "codgrupo"   => $inCodGrupo    ,
                    "numcgm"     => $inNumCGM      ,
                    "descricao"  => $stDescricao   ,
                    "exercicio"  => $stExercicio
                );

                $arGruposSessao = Sessao::read( "grupos" );
                $arGrupos["inLinha"] = count( $arGruposSessao );
                $arGruposSessao[] = $arGrupos;
                Sessao::write( "grupos", $arGruposSessao );

                $rsGrupos->preenche( $arGruposSessao );
                $stJs .= montaListaGrupos($rsGrupos  );
            }
        }
        break;

    case "excluirGrupo":
        $stJs .= excluirGrupo($_REQUEST["inLinha"]);
    break;
    case "montaGrupos":
        $obRARRPermissao = new RARRPermissao;
        $obRARRPermissao->obRCGM->inNumCGM = $_REQUEST["inNumCGM"];
        $obRARRPermissao->listarPermissoes($rsGrupos);
        // numero de Grupos
        $inNumGrupos = $rsGrupos->getNumLinhas();
        // array
        $arGruposAgrupados = array();
        $arGruposSessao = Sessao::read( "grupos" );
        for ($inCount=0;$inNumGrupos > $inCount;$inCount++) {
            $arTmp["codgrupo"   ] = $rsGrupos->arElementos[$inCount]["cod_grupo"    ] ;
            $arTmp["descricao"  ] = $rsGrupos->arElementos[$inCount]["descricao"    ] ;
            $arTmp["exercicio"  ] = $rsGrupos->arElementos[$inCount]["ano_exercicio"] ;
            $arTmp["inLinha"    ] = $inCount;
            $arGruposSessao[] = $arTmp;
        }

        Sessao::write( "grupos", $arGruposSessao );
        $rsGrupos->preenche( $arGruposSessao );
        $stJs .= montaListaGrupos($rsGrupos  );

    break;
}
SistemaLegado::executaFrameOculto($stJs);
SistemaLegado::LiberaFrames();
?>
