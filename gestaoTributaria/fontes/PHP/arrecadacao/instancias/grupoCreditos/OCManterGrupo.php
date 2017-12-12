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

    * $Id: OCManterGrupo.php 63839 2015-10-22 18:08:07Z franver $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.17  2006/10/30 13:18:12  dibueno
Adição da coluna ORDEM

Revision 1.16  2006/10/19 18:01:16  cercato
correcao da formatacao da lista do grupo de creditos e da lista de creditos por grupo.

Revision 1.15  2006/09/27 09:50:39  dibueno
Inclusao do método BuscaGrupoCredito, utilizado pelo componente MontaGrupoCredito

Revision 1.14  2006/09/15 11:10:42  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RUsuario.class.php"       );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"  );

function alertaAvisoCalendario($stPagina,$stMensagem,$stVariavel)
{
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
            window.open("'.$stPagina.'&stMsg='.$stMensagem.'&'.Sessao::getId().'","_blank","toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=350, height=200, top=200 , left=350");
           </script>';
}

$stCtrl = $_REQUEST['stCtrl'];

$stJs = "";

$obRARRGrupo = new RARRGrupo;
$obErro = new Erro;

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

function montaListaCreditos(&$rsListaCreditos)
{
    global $request;

    $stAcao = $request->get('stAcao');
    $rsListaCreditos->setPrimeiroElemento();
    //
    if ( !$rsListaCreditos->eof() ) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsListaCreditos );
        $obLista->setTitulo ("Lista de Créditos");
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Código");
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Descrição" );
        $obLista->ultimoCabecalho->setWidth( 60 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Ordem" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Descontos" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[codcredito].[codgenero].[codespecie].[codnatureza]" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "descricao" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "ordem" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "desconto" );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->addCampo( "1","inLinha" );
        $obLista->ultimaAcao->setLink( "javascript: excluirDado('excluirCredito');" );
        $obLista->commitAcao();
        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace("\n","",$stHTML);
        $stHTML = str_replace("  ","",$stHTML);
        $stHTML = str_replace("'","\\'",$stHTML);
     } else {
         $stHTML = "";
     }
     $stJs = "d.getElementById('spnCreditos').innerHTML = '".$stHTML."';";

     return $stJs;
}
function montaListaAcrescimos(&$rsListaAcrescimos)
{
   $stAcao = $request->get('stAcao');
    $rsListaAcrescimos->setPrimeiroElemento();
     if ( !$rsListaAcrescimos->eof() ) {
         $obLista = new Lista;
         $obLista->setMostraPaginacao( false );
         $obLista->setRecordSet( $rsListaAcrescimos );
         $obLista->setTitulo ("Lista de Acréscimos");
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("Código");
         $obLista->ultimoCabecalho->setWidth( 20 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Descrição" );
         $obLista->ultimoCabecalho->setWidth( 80 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();

         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "codacrescimo" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "descricao" );
        // $obLista->ultimoDado->setCampo( "cod_tipo" );
         $obLista->commitDado();

         $obLista->addAcao();
         $obLista->ultimaAcao->setAcao( "EXCLUIR" );
         $obLista->ultimaAcao->setFuncao( true );
         $obLista->ultimaAcao->addCampo( "1","inLinha" );
         $obLista->ultimaAcao->setLink( "javascript: excluirDado('excluirAcrescimo');" );
         $obLista->commitAcao();
         $obLista->montaHTML();
         $stHTML = $obLista->getHTML();
         $stHTML = str_replace("\n","",$stHTML);
         $stHTML = str_replace("  ","",$stHTML);
         $stHTML = str_replace("'","\\'",$stHTML);
     } else {
         $stHTML = "";
     }
     $stJs = "d.getElementById('spnAcrescimos').innerHTML = '".$stHTML."';";

     return $stJs;
}

function excluirCredito($inLinha)
{
    $arNovaListaCredito = array();
    $inContLinha = 0;
    foreach ( Sessao::read( "creditos" ) as $inChave => $arCreditos ) {
        if ($inChave != $inLinha) {
            $arCreditos["inLinha"] = $inContLinha;
            $arNovaListaCredito[] = $arCreditos;
            $inContLinha++;
        }
    }

    Sessao::write( "creditos", $arNovaListaCredito );

    $rsListaCreditos = new RecordSet;
    $rsListaCreditos->preenche( $arNovaListaCredito );
    $stJs = montaListaCreditos( $rsListaCreditos );

    return $stJs;
}
function excluirAcrescimo($inLinha)
{
        $arNovaListaAcrescimo = array();
        $inContLinha = 0;

        foreach ( Sessao::read( "acrescimos" ) as $inChave => $arAcrescimos ) {
            if ($inChave != $inLinha) {
                $arAcrescimos["inLinha"] = $inContLinha;
                $arNovaListaAcrescimo[] = $arAcrescimos;
                $inContLinha++;
            }
        }

        Sessao::write( "acrescimos", $arNovaListaAcrescimo );
        $rsListaAcrescimos = new RecordSet;
        $rsListaAcrescimos->preenche( $arNovaListaAcrescimo );
        $stJs = montaListaAcrescimos( $rsListaAcrescimos );

    return $stJs;
}

switch ($_REQUEST ["stCtrl"]) {
    case "buscaFuncao":
        include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php" );
        $obRFuncao = new RFuncao;

        if ($_REQUEST['inCodigoFormula'] != "") {
            $arCodFuncao = explode('.',$_REQUEST["inCodigoFormula"]);
            if ( ($arCodFuncao[0] != 25) OR ($arCodFuncao[1] != 2) ) {
               $stJs .= "f.inCodigoFormula.value ='';\n";
               $stJs .= "f.inCodigoFormula.focus();\n";
               $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
               $stJs .= "alertaAviso('@Função inválida. (".$_REQUEST["inCodigoFormula"].")','form','erro','".Sessao::getId()."');";
               SistemaLegado::executaFrameOculto($stJs);
               break;
            }

            $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
            $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
            $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
            $obRFuncao->consultar();

            $inCodFuncao = $obRFuncao->getCodFuncao () ;
            $stDescricao = "&nbsp;";
            $stDescricao = $obRFuncao->getComentario() ;
            $stNomeFuncao = $obRFuncao->getNomeFuncao();
       }

        if ($stDescricao || $stNomeFuncao) {
            $stJs .= "d.getElementById('stFormula').innerHTML = '".$inCodFuncao." - ".$stNomeFuncao."';\n";
        } else {
            $stJs .= "f.inCodigoFormula.value ='';\n";
            $stJs .= "f.inCodigoFormula.focus();\n";
            $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Função informada não existe. (".$_REQUEST["inCodigoFormula"].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "limpar":
        Sessao::write( "creditos", array() );
        Sessao::write( "acrescimos", array() );
    break;
    case "buscaUsuario":
         $stJs .= BuscaUsuario();
    break;
    case "buscaCredito":
        //credito.especie.genero.natureza

        $inCodCreditoComposto  = explode('.',$_REQUEST["inCodCredito"]);

        $obRARRGrupo->obRMONCredito->setCodCredito  ($inCodCreditoComposto[0]);
        $obRARRGrupo->obRMONCredito->setCodEspecie  ($inCodCreditoComposto[1]);
        $obRARRGrupo->obRMONCredito->setCodGenero   ($inCodCreditoComposto[2]);
        $obRARRGrupo->obRMONCredito->setCodNatureza ($inCodCreditoComposto[3]);
        $obRARRGrupo->obRMONCredito->consultarCredito();

        $inCodCredito = $obRARRGrupo->obRMONCredito->getCodCredito();
        $stDescricao = $obRARRGrupo->obRMONCredito->getDescricao() ;

        if ( !empty($stDescricao) ) {
            $stJs .= "d.getElementById('stCredito').innerHTML = '".$stDescricao."';\n";
        } else {
            $stJs .= "f.inCodCredito.value ='';\n";
            $stJs .= "f.inCodCredito.focus();\n";
            $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Crédito informado não existe. (".$_REQUEST["inCodCredito"].")','form','erro','".Sessao::getId()."');";
        }

    break;
    case "incluirCredito":
        /**
        * @desc Monta array com os valores dos atributos do elemento inserido
        */
        // credito informafo
        //credito.especie.genero.natureza
        if ($_REQUEST["inCodCredito"]) {
            $inCodCreditoComposto  = explode('.',$_REQUEST["inCodCredito"]);
            if ( !$_REQUEST["stExercicio"])
                $obErro->setDescricao("Deve ser especificado o Exercício antes de incluir um crédito para o Grupo de Créditos");

            if ( !$_REQUEST["inOrdem"])
                $obErro->setDescricao("Deve ser especificado a ordem de execução do cálculo para o crédito a adicionar para o Grupo de Créditos");
        } else {
            $obErro->setDescricao("Código do crédito ínvalido!");
        }

        $obRARRGrupo->obRMONCredito->setCodCredito  ( $inCodCreditoComposto[0] );
        $obRARRGrupo->obRMONCredito->setCodNatureza ( $inCodCreditoComposto[3] );
        $obRARRGrupo->obRMONCredito->setCodEspecie  ( $inCodCreditoComposto[1] );
        $obRARRGrupo->obRMONCredito->setCodGenero   ( $inCodCreditoComposto[2] );

        $obRARRGrupo->obRMONCredito->consultarCredito();

        $inCodCredito   = $obRARRGrupo->obRMONCredito->getCodCredito  ();
        $inCodNatureza  = $obRARRGrupo->obRMONCredito->getCodNatureza ();
        $inCodEspecie   = $obRARRGrupo->obRMONCredito->getCodEspecie  ();
        $inCodGenero    = $obRARRGrupo->obRMONCredito->getCodGenero   ();
        $stDescricao    = $obRARRGrupo->obRMONCredito->getDescricao   ();
        $inCodNoema     = $obRARRGrupo->obRMONCredito->obRNorma->getCodNorma    ();

        //verifica se credito ja esta agrupado
        $obRARRGrupo = new RARRGrupo;
        $obRARRGrupo->setExercicio($_REQUEST["stExercicio"]);
        $obRARRGrupo->addCredito();
        $obRARRGrupo->roUltimoCredito->setCodCredito ( $inCodCredito  );
        $obRARRGrupo->roUltimoCredito->setCodNatureza( $inCodNatureza );
        $obRARRGrupo->roUltimoCredito->setCodEspecie ( $inCodEspecie  );
        $obRARRGrupo->roUltimoCredito->setCodGenero  ( $inCodGenero   );
        $obRARRGrupo->roUltimoCredito->setDesconto   ( $_REQUEST['boDesconto'] );
        $obRARRGrupo->roUltimoCredito->listarCreditos( $rsGrupos      );
        $stErro = false;
/*
        if ( $rsGrupos->getCampo("cod_grupo") != '' && !$obErro->ocorreu() ) {
            $obRARRGrupo->setCodGrupo($rsGrupos->getCampo("cod_grupo"));
            $obRARRGrupo->consultarGrupo();
            $stMsg = $rsGrupos->getCampo('cod_grupo')."-".$obRARRGrupo->getDescricao()."/".$obRARRGrupo->getExercicio();
            $stErro = "Crédito já agrupado no Grupo de Crédito(".$stMsg.")";
        }
*/
        $rsCreditos = new RecordSet;

        //VERIFICA SE O Elemento ja foi informado
        foreach ( Sessao::read( "creditos" ) as $arCreditos ) {
            $codComparacao = $arCreditos["codcredito"].".".$arCreditos["codespecie"].".".$arCreditos["codgenero"].".".$arCreditos["codnatureza"];
            $ordemComparacao = $arCreditos['ordem'];
            if ($codComparacao == $_REQUEST["inCodCredito"]) {
                $stErro = "Crédito já informado!(".$_REQUEST['inCodCredito'].")";
                break;
            } elseif ($ordemComparacao == $_REQUEST["inOrdem"]) {
                $stErro = "O ". $ordemComparacao."º cálculo já está definido. Escolha outra ordem!(". $_REQUEST['inCodCredito'].")";
                break;
            } else {
                $stErro = false;
            }
        }

        if ($stErro) {
            $stJs .= "f.inCodCredito.value = ''; \n";
            $stJs .= "f.inOrdem.value = '';\n";
            $stJs .= "f.inCodCredito.focus();\n";
            $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('".$stErro."','form','erro','".Sessao::getId()."', '../');";
        } elseif($obErro->ocorreu() )
            $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."', '../');";
        else{
            $stJs  = "f.inCodCredito.value = '';\n";
            $stJs .= "f.inOrdem.value = '';\n";
            $stJs .= "f.inCodCredito.focus();\n";
            $inNumCreditos = Sessao::read ( "inNumCreditos" );
            $stJs .= "f.inNumCreditos.value = '".++$inNumCreditos."'; \n";
            Sessao::write ( "inNumCreditos", $inNumCreditos );
            $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";

            $arCreditos = array( "codcredito"   => $inCodCredito    ,
                                 "descricao"    => $stDescricao     ,
                                 "codnatureza"  => $inCodNatureza   ,
                                 "codgenero"    => $inCodGenero     ,
                                 "codespecie"   => $inCodEspecie    ,
                                 "desconto"     => $_REQUEST['boDesconto'] ,
                                 "ordem"     => $_REQUEST['inOrdem'] ,
                                 "codnorma"     => $inCodNorma      ,
                                );

            $arCreditosSessao = Sessao::read( "creditos" );
            $arCreditos["inLinha"] = count( $arCreditosSessao );
            $arCreditosSessao[] = $arCreditos;
            Sessao::write( "creditos", $arCreditosSessao );
            $rsCreditos->preenche( $arCreditosSessao );
            $stJs .= montaListaCreditos( $rsCreditos );
        }
    break;
    case "excluirCredito":
        $stJs .= excluirCredito($_REQUEST["inLinha"]);
    break;
    case "buscaAcrescimo":
        if ($_REQUEST["inCodAcrescimo"]) {
            $obRARRGrupo->obRMONAcrescimo->setCodAcrescimo($_REQUEST["inCodAcrescimo"]);
            $obRARRGrupo->obRMONAcrescimo->consultarAcrescimo( $rsAcrescimos );

            $inCodAcrescimo = $obRARRGrupo->obRMONAcrescimo->getCodAcrescimo();
            $stDescricao = $obRARRGrupo->obRMONAcrescimo->getDescricao() ;
            $inCodTipo      = $obRARRGrupo->obRMONAcrescimo->getCodTipo();

            if ( !empty($stDescricao) ) {
                $stJs .= "d.getElementById('stAcrescimo').innerHTML = '".$stDescricao."';\n";
            } else {
                $stJs .= "f.inCodAcrescimo.value ='';\n";
                $stJs .= "f.inCodAcrescimo.focus();\n";
                $stJs .= "d.getElementById('stAcrescimo').innerHTML = '&nbsp;';\n";
                $stJs .= "SistemaLegado::alertaAviso('@Acréscimo informado não existe. (".$_REQUEST["inCodAcrescimo"].")','form','erro','".Sessao::getId()."');";
            }
        } else {

              $stJs .= "f.inCodAcrescimo.value ='';\n";
              $stJs .= "d.getElementById('stAcrescimo').innerHTML = '&nbsp;';\n";

        }

    break;
case "incluirAcrescimo":
        /**
        * @desc Monta array com os valores dos atributos do elemento inserido
        */
        // Acrescimo informafo
        $inCodAcrescimo   = $_REQUEST["inCodAcrescimo"];
        // pegar valores do Acrescimo acima
        $obRARRGrupo->obRMONAcrescimo->setCodAcrescimo( $inCodAcrescimo );
        $obRARRGrupo->obRMONAcrescimo->consultarAcrescimo( $rsAcrescimo );
        $inCodAcrescimo = $obRARRGrupo->obRMONAcrescimo->getCodAcrescimo();
        $stDescricao    = $obRARRGrupo->obRMONAcrescimo->getDescricao();

        $inCodTipo      = $obRARRGrupo->obRMONAcrescimo->getCodTipo();

        $rsAcrescimos = new RecordSet;

        //VERIFICA SE O Elemento ja foi informado
        $boErro = false;
            foreach ( Sessao::read( "acrescimos" ) as $arAcrescimos ) {
                if ($arAcrescimos["codacrescimo"] == $_REQUEST["inCodAcrescimo"]) {
                    $boErro = true;
                break;
                } else {
                    $boErro = false;
                }
            }

        if ($boErro) {
            $stJs .= "f.inCodAcrescimo.value = '';\n";
            $stJs .= "d.getElementById('stAcrescimo').innerHTML = '&nbsp;';\n";
            $stJs .= "SistemaLegado::alertaAviso('Acréscimo já informado!(".$_REQUEST["inCodAcrescimo"].")','form','erro','".Sessao::getId()."', '../');";
        } else {
            $stJs  = "f.inCodAcrescimo.value = '';\n";
            $stJs .= "d.getElementById('stAcrescimo').innerHTML = '&nbsp;';\n";

            $arAcrescimos = array( "codacrescimo"   => $inCodAcrescimo    ,
                                 "descricao"    => $stDescricao     ,
                                 "cod_tipo" => $inCodTipo
                                );

            $arAcrescimosSessao = Sessao::read( "acrescimos" );
            $arAcrescimos["inLinha"] = count( $arAcrescimosSessao );
            $arAcrescimosSessao[] = $arAcrescimos;
            Sessao::write( "acrescimos", $arAcrescimosSessao );
            $rsAcrescimos->preenche( $arAcrescimosSessao );
            $stJs .= montaListaAcrescimos( $rsAcrescimos  );
            Sessao::write( "inNumAcrescimos", Sessao::read( "inNumAcrescimos" ) + 1 );
        }
    break;
    case "excluirAcrescimo":
        $stJs .= excluirAcrescimo($_REQUEST["inLinha"]);
    break;
    case "montaCreditos":
        $obRARRGrupo->setCodGrupo( $_REQUEST["inCodGrupo"] );
        $obRARRGrupo->setExercicio( $_REQUEST["stExercicio"] );
        $obRARRGrupo->listarCreditos($rsCreditos);

        // numero de creditos
        $inNumCreditos = $rsCreditos->getNumLinhas();
        // array
        $arCreditosAgrupados = array();
        $arCreditos = Sessao::read( "creditos" );
        for ($inCount=0;$inNumCreditos > $inCount;$inCount++) {
            $arTmp["codcredito" ] 	= $rsCreditos->arElementos[$inCount]["cod_credito"        ] ;
            $arTmp["descricao"  ] 	= $rsCreditos->arElementos[$inCount]["descricao_credito"  ] ;
            $arTmp["codgenero"  ] 	= $rsCreditos->arElementos[$inCount]["cod_genero"         ] ;
            $arTmp["codespecie" ] 	= $rsCreditos->arElementos[$inCount]["cod_especie"        ] ;
            $arTmp["codnatureza"] 	= $rsCreditos->arElementos[$inCount]["cod_natureza"       ] ;
            $arTmp["ordem"] 		= $rsCreditos->arElementos[$inCount]["ordem"       ] ;
            if ($rsCreditos->arElementos[$inCount]["desconto"] == 't') { $stDesconto = 'Sim'; } else { $stDesconto = 'Não'; }
            $arTmp["desconto"]      = $stDesconto;
            $arTmp["inLinha"    ] 	= $inCount;
            $arCreditos[] = $arTmp;
        }

        Sessao::write( "creditos", $arCreditos );
        $rsCreditos->preenche( $arCreditos );
        $rsCreditos->ordena ("ordem");
        $stJs .= montaListaCreditos( $rsCreditos  );

    break;
    case "montaAcrescimos":
        $obRARRGrupo->setCodGrupo($_REQUEST["inCodGrupo"]);
        $obRARRGrupo->listarAcrescimos($rsAcrescimos);
        // numero de acrescimos
        $inNumAcrescimos = $rsAcrescimos->getNumLinhas();
        // array
        $arAcrescimos = Sessao::read( "acrescimos" );
        for ($inCount=0;$inNumAcrescimos > $inCount;$inCount++) {
            $arTmp["codacrescimo" ] = $rsAcrescimos->arElementos[$inCount]["cod_acrescimo"        ] ;
            $arTmp["descricao"    ] = $rsAcrescimos->arElementos[$inCount]["descricao_acrescimo"  ] ;
            $arTmp["cod_tipo"    ] =  $rsAcrescimos->arElementos[$inCount]["cod_tipo"  ] ;
            $arTmp["inLinha"      ] = $inCount;
            $arAcrescimos[] = $arTmp;
        }

        Sessao::write( "acrescimos", $arAcrescimos );
        $rsAcrescimos->preenche( $arAcrescimos );
        $stJs .= montaListaAcrescimos( $rsAcrescimos  );

    break;

//-----------------
    case "BuscaGrupoCredito":
        ;

        $obRARRGrupo = new RARRGrupo;
        if ($_REQUEST["inCodGrupo"]) {
            $arDados = explode("/", $_REQUEST["inCodGrupo"]);
            $stMascara = "";
            //$obRARRGrupo = new RARRGrupo;
            $obRARRGrupo->RecuperaMascaraGrupoCredito( $stMascara );
            $stGrupoCredito = str_replace( "/", "", $_REQUEST["inCodGrupo"] );

            $inTamanhoOrigem=strlen($stGrupoCredito);
            if ($inTamanhoOrigem<5) {
                $js  = 'f.inCodGrupo.value= "";';
                $js .= 'f.inCodGrupo.focus();';
                $js .= 'd.getElementById("stGrupo").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Código Grupo/Ano exercício incompleto. (".$_REQUEST["inCodGrupo"].")', 'form','erro','".Sessao::getId()."');";

            } else {
                $arDados[0]= substr($stGrupoCredito,0, $inTamanhoOrigem-4);
                $arDados[1]= substr($stGrupoCredito,$inTamanhoOrigem-4, 4);

                $obRARRGrupo->setCodGrupo( $arDados[0] );
                $obRARRGrupo->setExercicio( $arDados[1] );
                $obRARRGrupo->consultarGrupo ();

                $inCodGrupo     = $obRARRGrupo->getCodGrupo () ;
                $stDescricao    = $obRARRGrupo->getDescricao() ;
                $inCodModulo    = $obRARRGrupo->getCodModulo() ;

                if ($stDescricao) {
                                    $js = "d.getElementById('inCodGrupo').value = '".str_pad($arDados[0],strlen($stMascara),'0',STR_PAD_RIGHT).'/'.$arDados[1]."';\n";
                                    $js .= "d.getElementById('stGrupo').innerHTML = '".$stDescricao."';\n";
                } else {
                    $js  = 'f.inCodGrupo.value= ""; ';
                    $js .= 'f.inCodGrupo.focus(); ';
                    $js .= 'd.getElementById("stGrupo").innerHTML = "&nbsp;"; ';
                    $js .= "alertaAviso('@Código Grupo/Ano exercício inexistente. (".$_REQUEST["inCodGrupo"].")', 'form','erro','".Sessao::getId()."'); ";
                }

            }

        } else {
            $js  = 'f.inCodGrupo.value= "";';
            $js .= 'f.inCodGrupo.focus();';
            $js .= 'd.getElementById("stGrupo").innerHTML = "&nbsp;";';
        }
        if ($js) {
            echo $js;
            exit;
        }
        break;

}
SistemaLegado::executaFrameOculto($stJs);
SistemaLegado::LiberaFrames();
?>
