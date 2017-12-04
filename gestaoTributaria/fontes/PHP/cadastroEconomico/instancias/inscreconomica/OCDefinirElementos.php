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
    * Página do Frame oculto para Definir Elementos
    * Data de Criação   : 27/04/2005

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: OCDefinirElementos.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.13  2007/03/20 14:40:11  cassiano
Bug #8771#

Revision 1.12  2006/11/17 12:43:15  domluc
Correção Bug #7437#

Revision 1.11  2006/09/15 14:33:07  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php");
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php" );

$obErro = new Erro;
$obRCEMInscricaoAtividade = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );
$obRCEMInscricaoAtividade->addAtividade();
$obRCEMInscricaoAtividade->roUltimaAtividade->addAtividadeElemento();

$obMontaAtividade = new MontaAtividade;
$obMontaAtividade->setCadastroAtividade( false );

function montaAtributos()
{
        GLOBAL $obRCEMInscricaoAtividade;
        $obRCEMInscricaoAtividade->roUltimaAtividade->roUltimoElemento->setCodigoElemento ( $_REQUEST['stElemento'] );
        $obRCEMInscricaoAtividade->roUltimaAtividade->roUltimoElemento->obRCadastroDinamico->setPersistenteAtributos( new TCEMAtributoElemento() );
        $obRCEMInscricaoAtividade->roUltimaAtividade->roUltimoElemento->obRCadastroDinamico->setChavePersistenteValores( array( "cod_elemento" => $obRCEMInscricaoAtividade->roUltimaAtividade->roUltimoElemento->getCodigoElemento() ) );
        $obRCEMInscricaoAtividade->roUltimaAtividade->roUltimoElemento->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "Atributo_"  );
        $obMontaAtributos->setRecordSet  ( $rsAtributos );

        $obFormulario = new Formulario;
        $obMontaAtributos->geraFormulario( $obFormulario );

        $obMontaAtributos->recuperaValores();

        $inNumeroAtributos = count($obMontaAtributos->arNomeInput);
        // salva array com nome dos atributos
        $obHdnArrayAtributos= new Hidden;
        $obHdnArrayAtributos->setName   ("arNomeAtributosElemento");
        $obHdnArrayAtributos->setId     ("arNomeAtributosElemento");
        $obHdnArrayAtributos->setValue  (implode(",",$obMontaAtributos->arNomeInput));

        $inNumeroAtributos = count($obMontaAtributos->arNomeInput);
        $obFormulario->addHidden( $obHdnArrayAtributos );
        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();

        $stJs .= "d.getElementById(\"spnElementos\").innerHTML = '".$stHTML."';";

        return $stJs;
}

function montaListaElementos(&$rsListaElementos)
{
    GLOBAL $inOcorrencia;
    if ( !$rsListaElementos->eof() ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListaElementos      );
        $obLista->setTitulo                    ( "Lista de Elementos"   );
        $obLista->setMostraPaginacao           ( false                  );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Atividade"            );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Código"               );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Nome do Elemento"     );
        $obLista->ultimoCabecalho->setWidth    ( 30                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Atributos"            );
        $obLista->ultimoCabecalho->setWidth    ( 40                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "[inCodigoAtividade] - [stChaveAtividade]");
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "inCodigoElemento"     );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "stElemento"           );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "atributos"            );
        $obLista->commitDado                   (                        );

        if ($_REQUEST['stAcao'] == 'elemento') {
            $obLista->addAcao                      (                        );
            $obLista->ultimaAcao->setAcao          ( "ALTERAR"              );
            $obLista->ultimaAcao->setFuncao        ( true                   );
            $obLista->ultimaAcao->setLink( "JavaScript:montaAtributosElementos();" );
            $obLista->ultimaAcao->addCampo         ( "1","inCodigoElemento"   );
            $obLista->commitAcao                   (                        );
        }

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirDado('excluirElementos');" );
        $obLista->ultimaAcao->addCampo         ( "1","inLinha"   );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
    } else {
        $stHTML = "&nbsp;";
    }
    $stJs .= "d.getElementById('spnElementos').innerHTML = '';";
    $stJs .= "d.getElementById('lsElementos').innerHTML = '".$stHTML."';\n";
    $stJs .= "f.stElemento.selectedIndex = 0;\n";

    return $stJs;
}

function excluirElemento($inLinha)
{
    $arNovaListaElementos = array();
    $inContLinha = 0;
    $arElementosSessao = Sessao::read( "elementos" );
    foreach ($arElementosSessao as $inChave => $arElementos) {
        if ($inChave != $inLinha) {
            $arElementos["inLinha"] = $inContLinha++;
            $arNovaListaElementos[] = $arElementos;
        }
    }

    Sessao::write( "elementos", $arNovaListaElementos );
    $rsListaElementos = new RecordSet;
    $rsListaElementos->preenche( $arNovaListaElementos );
    $stJs = montaListaElementos( $rsListaElementos );

    return $stJs;
}

switch ($_REQUEST["stCtrl"]) {
    case "preencheElemento":
        $stJs .= "d.frm.stElemento.value = d.frm.inCodigoElemento.value;";
        $stJs .= montaAtributos();
        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "preencheCodigoElemento":
        $stJs .= "d.frm.inCodigoElemento.value = d.frm.stElemento.value;";
        $stJs .= montaAtributos();
        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "montaElementos":
        /**
        * @desc Monta array com os valores dos atributos do elemento inserido
        */
        // Verifica se qual a linha do elemento na edição de elemento
        $stAcao = $request->get('stAcao');
        $arElementosSessao = Sessao::read( "elementos" );
        if ($stAcao == "elemento") {
            foreach ($arElementosSessao as $key => $chave) {
                if ($arElementosSessao[$key]["inCodigoElemento"] == $_REQUEST["inCodigoElemento"]) {
                    $inLinhaDelete = $arElementosSessao[$key]["inLinha"];
                }
            }
        }

        $stNomAtribElem     = $_REQUEST["arNomeAtributosElemento"]  ;
        $stCodElemento      = $_REQUEST["inCodigoElemento"]         ;

        $arAtribElem        = explode(",",$stNomAtribElem)          ;

        $arAtribElemValor[$stCodElemento] = array()                 ;
        $stNomesValores = "";
        for ($inCount =0;$inCount < count($arAtribElem);$inCount++) {
            $arAtribTmp = explode('_' , $arAtribElem[$inCount]);
            $arAtribElemValor[$stCodElemento][$arAtribTmp[1]]= $_REQUEST[$arAtribElem[$inCount]];
            //$arAtribElemValor[$stCodElemento][substr($arAtribElem[$inCount],-4,2)]= $_REQUEST[$arAtribElem[$inCount]];
            $stWhere  = 'where cod_atributo = '.$arAtribTmp[1];
            $stWhere .= ' and cod_modulo = 14 and cod_cadastro = 5 ';
            $stNome = SistemaLegado::pegaDado('nom_atributo','administracao.atributo_dinamico',$stWhere);
            $obConexao = new Conexao;
            $obConexao->executaSQL( $rsRecordSet, 'select economico.valor_padrao_desc(' . $arAtribTmp[ 1 ] . ',14,5,\'' . $_REQUEST[$arAtribElem[$inCount]] . '\') as valor' );
            $stValor = $rsRecordSet->getCampo( 'valor' );
            $stNomesValores .= "" . $stNome . " : " . $stValor . " <br>";
        }
        $rsElementos = new RecordSet;
        $rsListaElementos = new RecordSet;
        $obRCEMInscricaoAtividade->roUltimaAtividade->roUltimoElemento->setCodigoElemento ( $_REQUEST['inCodigoElemento'] );
        $obRCEMInscricaoAtividade->roUltimaAtividade->setCodigoAtividade( $_REQUEST['cmbAtividade'] );
        $obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST["inInscricaoEconomica"]);
        $obRCEMInscricaoAtividade->listarElementoAtividade( $rsElementos );
        $stMsg = "";

        //VERIFICA SE O ELEMENTO JA FOI INFORMADO
        $boErro = false;
        foreach ($arElementosSessao as  $inChave => $arElementos) {
            if ($arElementos["inCodigoElemento"] == $_REQUEST["inCodigoElemento"] && $arElementos["inCodigoAtividade"] == $_REQUEST["cmbAtividade"]) {
                if ($_REQUEST["stAcao"] == "elemento") {
                    excluirElemento($inLinhaDelete);
                    $boErro = false;
                } else {
                    $boErro = true;
                    $stMsg  = "Elemento já informado!";
                }
                break;
            }
        }

        $inOcorrencia++;

        if ($boErro) {
            $stJs = "alertaAviso('".$stMsg."(".$_REQUEST["stElemento"].")','form','erro','".Sessao::getId()."', '../');";
        } else {
            $stJs .= "f.inCodigoElemento.value = '';\n";
            $stJs .= "f.cmbAtividade.value = '';";
            //$stJs .= "f.stElemento.selectedIndex = 0;\n";
            // verificar se elemento ja foi incluida pra esta inscricao
            $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica ;
            $obRCEMInscricaoEconomica->addElemento();
            $obRCEMInscricaoEconomica->roUltimoElemento->setCodigoElemento( $_REQUEST["inCodigoElemento"] );
            $obRCEMInscricaoEconomica->setInscricaoEconomica($_REQUEST["inInscricaoEconomica"]);
            $obRCEMInscricaoEconomica->consultarMaxOcorrenciaElemento($inOcorrencia);

            $arElementos = array( "inCodigoElemento" => $_REQUEST["inCodigoElemento"],
                                  "stElemento"       => $rsElementos->getCampo( "nom_elemento" ),
                                  //"stChaveAtividade" => $_REQUEST["stChaveAtividade"],
                                  "stChaveAtividade" => $rsElementos->getCampo("cod_estrutural"),
                                  "inCodigoAtividade"=> $rsElementos->getCampo( "cod_atividade" ),
                                  "inOcorrenciaAtividade" => $rsElementos->getCampo( "ocorrencia_atividade" ),
                                  "inOcorrencia"     => $inOcorrencia,
//           =====>                       "inOcorrencia"     => $rsElementos->getCampo( "ocorrencia_elemento" ),
                                  "arElementos"      => $arAtribElemValor,
                                  "atributos"        => $stNomesValores
                                 );
            $arElementos["inLinha"] = count( $arElementosSessao );
            $arElementosSessao[] = $arElementos;
            Sessao::write( "elementos", $arElementosSessao );
            $rsListaElementos->preenche( $arElementosSessao );
            $stJs .= montaListaElementos( $rsListaElementos  );
        }
        sistemaLegado::executaFrameOculto($stJs);
        break;

    case "limparElementos":
        $stJs = '';
        $stJs.= "f.cmbAtividade.value = '';";
        $stJs.= "f.stElemento.value = '';";
        $stJs.= "d.getElementById('spnElementos').innerHTML = '&nbsp;';";

        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "excluirElementos":
        $inLinha = $_REQUEST["inLinha"] ? $_REQUEST["inLinha"] : 0;
        $arNovaListaElementos = array();
        $inContLinha = 0;
        $arElementosSessao = Sessao::read( "elementos" );
        foreach ($arElementosSessao as $inChave => $arElementos) {
            if ($inChave != $inLinha) {
                $arElementos["inLinha"] = $inContLinha++;
                $arNovaListaElementos[] = $arElementos;
            }
        }

        Sessao::write( "elementos", $arNovaListaElementos );
        $rsListaElementos = new RecordSet;
        $rsListaElementos->preenche( $arNovaListaElementos );
        $stJs .= montaListaElementos( $rsListaElementos );
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "montaElementosAlteracao":
        $stNomAtribElem     = $_REQUEST["arNomeAtributosElemento"]  ;
        $stCodElemento      = $_REQUEST["inCodigoElemento"]         ;

        $arAtribElem        = explode(",",$stNomAtribElem)          ;

        $arAtribElemValor[$stCodElemento] = array()                 ;
        // array de indice =  a cod_elemento criado com valores dos atributos.
        for ($inCount =0;$inCount < count($arAtribElem);$inCount++) {
            $arAtribElemValor[$stCodElemento][substr($arAtribElem[$inCount],-5,3)]= $_REQUEST[$arAtribElem[$inCount]];
        }

        $rsElementos = new RecordSet;
        $rsListaElementos = new RecordSet;
        $obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
        $obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->listarElementosAtivos($rsElementos);
        //$obRCEMInscricaoAtividade->listarElementoAtividadeEconomico($rsElementos);
//        $obRCEMInscricaoAtividade->listarElementoAtividade($rsElementos);

        $inCount = 0;
        Sessao::write( 'elementos', array() );
        $arCodElementos = array();

        while ( !$rsElementos->eof() ) {
            array_push( $arCodElementos, $rsElementos->getCampo( "cod_elemento" ) );

            $rsAtributoElemento = new RecordSet;
            $obRCEMInscricaoAtividade->addAtividade();
            $obRCEMInscricaoAtividade->roUltimaAtividade->addAtividadeElemento();
            $obRCEMInscricaoAtividade->roUltimaAtividade->roUltimoElemento->obRCadastroDinamico->setChavePersistenteValores(
                                array   (
                                "cod_elemento"        => $rsElementos->getCampo ( 'cod_elemento'        ) ,
                                "cod_atividade"       => $rsElementos->getCampo ( 'cod_atividade'       ) ,
                                "ocorrencia_atvidade" => $rsElementos->getCampo ( 'ocorrencia_atividade') ,
                                "inscricao_economica" => $rsElementos->getCampo ( 'inscricao_economica' ) ,
                                "ocorrencia_elemento" => $rsElementos->getCampo ( 'ocorrencia_elemento' ) ,
                                ) );

            $obRCEMInscricaoAtividade->roUltimaAtividade->roUltimoElemento->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributoElemento );
            while ( !$rsAtributoElemento->eof() ) {
                $arAtributos[$rsElementos->getCampo( "cod_elemento" )][$rsAtributoElemento->getCampo( "cod_atributo" )] = $rsAtributoElemento->getCampo( "valor" );
                $rsAtributoElemento->proximo();
            }

            $arElementos[$inCount] =
            array( "inCodigoElemento"      => $rsElementos->getCampo( 'cod_elemento' ) ,
                   "inOcorrencia"          => $rsElementos->getCampo( 'ocorrencia_elemento'   ) ,
                   "inCodigoAtividade"     => $rsElementos->getCampo( 'cod_atividade' ),
                   "stChaveAtividade"      => $rsElementos->getCampo( 'cod_estrutural' ),
                   "inOcorrenciaAtividade" => $rsElementos->getCampo( 'ocorrencia_atividade' ),
                   "stElemento"            => $rsElementos->getCampo( 'nom_elemento' ) ,
                   "arElementos"           => array( $rsElementos->getCampo('cod_elemento') => $arAtributos[$rsElementos->getCampo('cod_elemento')]),
                   "inLinha"               => count( $arElementos ),
                   "acao"                  => 'a' ,
                   "atributos"             => $rsElementos->getCampo ( 'atributos'        )

                 );

            $inCount++;
            $rsElementos->proximo();
        }

        if ( count($arElementos) > 0 ) {
            Sessao::write( "elementos", $arElementos );
            $rsListaElementos = new RecordSet;
            $rsListaElementos->preenche( $arElementos );
            $stJs .= montaListaElementos( $rsListaElementos );

            Sessao::write( "inNumElementos", count($arElementos) - 1 );
            Sessao::write( 'arCodElementos', $arCodElementos );
            //$stJs .= "f.boElemento.value = '1';\n";
            sistemaLegado::executaFrameOculto($stJs);
        }
    break;
    case "montaAtributosElementos":

        $obRCEMInscricaoAtividade->roUltimaAtividade->roUltimoElemento->obRCadastroDinamico->setChavePersistenteValores( array("cod_elemento"=>$_REQUEST["inCodigoElemento"]) );

        if ( ($_REQUEST["stAcao"] == "elemento") && (in_array($_REQUEST["inCodigoElemento"],Sessao::read( "arCodElementos") ))) {
            $obRCEMInscricaoAtividade->roUltimaAtividade->roUltimoElemento->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosElementos );
        } else {
            $obRCEMInscricaoAtividade->roUltimaAtividade->roUltimoElemento->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosElementos );
        }

        $obMontaAtributosElemento = new MontaAtributos;
        $obMontaAtributosElemento->setTitulo     ( "Atributos Elemento"   );
        $obMontaAtributosElemento->setName       ( "AtributoElemento_"    );
        $arElementosSessao = Sessao::read( "elementos" );
        foreach ($arElementosSessao as $key => $chave) {
            if ($arElementosSessao[$key]["inCodigoElemento"] == $_REQUEST["inCodigoElemento"]) {
                $inLinha = $key;
            }
        }

        foreach ($rsAtributosElementos->arElementos as $key => $value) {
            foreach ($rsAtributosElementos->arElementos[$key] as $chave => $valor) {
               $rsAtributosElementos->arElementos[$key]["valor"] = $arElementosSessao[$inLinha]["arElementos"][$_REQUEST["inCodigoElemento"]][$rsAtributosElementos->arElementos[$key]["cod_atributo"]];
            }
        }

        $obMontaAtributosElemento->setRecordSet  ( $rsAtributosElementos  );
        $obMontaAtributosElemento->recuperaValores();

        $obFormulario = new Formulario;
        $obFormulario->addTitulo ( 'Dados para '.$_REQUEST["stElemento"] );

        $obMontaAtributosElemento->geraFormulario($obFormulario);

        $inNumeroAtributos = count($obMontaAtributosElemento->arNomeInput);
        // salva array com nome dos atributos
        $obHdnArrayAtributos= new Hidden;
        $obHdnArrayAtributos->setName   ("arNomeAtributosElemento");
        $obHdnArrayAtributos->setId     ("arNomeAtributosElemento");
        $obHdnArrayAtributos->setValue  (implode(",",$obMontaAtributosElemento->arNomeInput));

        $obFormulario->addHidden    ($obHdnArrayAtributos);
        $obFormulario->montaInnerHtml();

        $stHTML = $obFormulario->getHTML();
        $stHTML = str_replace("\n","",$stHTML);
        $stHTML = str_replace("  ","",$stHTML);
        $stHTML = str_replace("\"","'",$stHTML);
        $stHTML = str_replace("'","\\'",$stHTML);

        $stJs .= "d.getElementById('spnElementos').innerHTML = '".$stHTML."';";

        /*
        $stJs .= 'd.getElementById("spnAtributosElemento").innerHTML = "'.$stHTML.'";';
        $stJs .= 'd.getElementById("inNumAtributos").value = "'.$inNumeroAtributos.'";';*/
        sistemaLegado::executaFrameOculto($stJs);

    break;
    case "preencheProxCombo":
        $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaAtividade->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaAtividade->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaAtividade->setCodigoAtividade   ( $arChaveLocal[1] );
        $obMontaAtividade->setValorReduzido     ( $arChaveLocal[3] );
        if ($_REQUEST["inPosicao"] == $_REQUEST["inNumNiveis"]) {
            // Monta Atividade retorna Javascript
            $obMontaAtividade->setRetornaJs(true);
            $rsElementos = new RecordSet;
            // instancia objeto de inscricao atividade para buscar elementos da atividade setada!
            $obRCEMInscricaoAtividade = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );
            $obRCEMInscricaoAtividade->addAtividade();
            $obRCEMInscricaoAtividade->roUltimaAtividade->addAtividadeElemento();
            // seta atividade
            $obRCEMInscricaoAtividade->roUltimaAtividade->setCodigoAtividade($arChaveLocal[1]);
            $obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
            $obRCEMInscricaoAtividade->listarElementoAtividade( $rsElementos );
            $js .= "limpaSelect(f.stElemento,0);\n";
            $js .= "f.stElemento.options[0] = new Option('Selecione Elemento','','selected');\n";
            if ( !$rsElementos->eof()) {
                $inContador = 1;
                while ( !$rsElementos->eof() ) {
                    $js .= "f.stElemento.options[$inContador] = ";
                    $js .= "new Option('".$rsElementos->getCampo("nom_elemento")."','".$rsElementos->getCampo("cod_elemento")."',''); \n";
                    $inContador++;
                    $rsElementos->proximo();
                }
            } else {
                $js .= "alertaAviso('Atividade ".$arChaveLocal[3]."  não definida para esta Inscrição Econômica ','form','aviso','".Sessao::getId()."');";
            }
            $js .= $obMontaAtividade->preencheProxCombo    ( $inPosicao , $_REQUEST["inNumNiveis"] );
            sistemaLegado::executaFrameOculto($js);
        } else {
            $obMontaAtividade->preencheProxCombo    ( $inPosicao , $_REQUEST["inNumNiveis"] );
        }
    break;
    case "preencheCombosAtividade":
        $obMontaAtividade->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaAtividade->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaAtividade->setValorReduzido ( $_REQUEST["stChaveAtividade"] );
        $obMontaAtividade->preencheCombosAtividade();
    break;
    case "montaElementoAtividade":
        $rsElementos = new RecordSet;
        // instancia objeto de inscricao atividade para buscar elementos da atividade setada!
        $obRCEMInscricaoAtividade = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );
        $obRCEMInscricaoAtividade->addAtividade();
        $obRCEMInscricaoAtividade->roUltimaAtividade->addAtividadeElemento();
        // seta atividade
        $obRCEMInscricaoAtividade->roUltimaAtividade->setCodigoAtividade($_REQUEST["cmbAtividade"]);
        $obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
        $obRCEMInscricaoAtividade->listarElementoPorUltimaOcorrenciaAtividade( $rsElementos );
        $js .= "limpaSelect(f.stElemento,0);\n";
        $js .= "f.stElemento.options[0] = new Option('Selecione Elemento','','selected');\n";
        if ( !$rsElementos->eof()) {
            $inContador = 1;
            while ( !$rsElementos->eof() ) {
                $js .= "f.stElemento.options[$inContador] = ";
                $js .= "new Option('".$rsElementos->getCampo("nom_elemento")."','".$rsElementos->getCampo("cod_elemento")."',''); \n";
                $inContador++;
                $rsElementos->proximo();
            }
        } else {
            $js .= "alertaAviso('Atividade ".$arChaveLocal[3]."  não definida para esta Inscrição Econômica ','form','aviso','".Sessao::getId()."');";
        }
        $js .= $obMontaAtividade->preencheProxCombo    ( $inPosicao , $_REQUEST["inNumNiveis"] );
        sistemaLegado::executaFrameOculto($js);
    break;
}
?>
