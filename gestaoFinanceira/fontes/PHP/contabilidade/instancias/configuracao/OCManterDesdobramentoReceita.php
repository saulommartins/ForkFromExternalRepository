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
    * Página de Processamento Oculto de Desdobramento da Receita
    * Data de Criação   : 10/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: OCManterDesdobramentoReceita.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeHistoricoPadrao.class.php";
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php";

/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "ManterDesdobramentoReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

function montaListaReceitaSecundaria(&$rsLista)
{
    if ( !$rsLista->eof() ) {
         $obLista = new Lista;
         $obLista->setMostraPaginacao( false );
         $obLista->setRecordSet( $rsLista );
         $obLista->setTitulo ("Registros de receita secundárias");

         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 5 );
         $obLista->ultimoCabecalho->setRowSpan( 2 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("Receita");
         $obLista->ultimoCabecalho->setColSpan ( 3 );
         $obLista->ultimoCabecalho->setAlign( "center" );
         $obLista->ultimoCabecalho->setWidth( 45 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Recurso" );
         $obLista->ultimoCabecalho->setColSpan ( 2 );
         $obLista->ultimoCabecalho->setWidth( 35 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setRowSpan ( 2 );
         $obLista->ultimoCabecalho->addConteudo( "Percentual" );
         $obLista->ultimoCabecalho->setVAlign ( "bottom" );
         $obLista->ultimoCabecalho->setWidth( 10 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setRowSpan( 2 );
         $obLista->ultimoCabecalho->setWidth( 5 );
         $obLista->commitCabecalho();

         $obLista->addCabecalho( true );
         $obLista->ultimoCabecalho->addConteudo("Cod");
         $obLista->ultimoCabecalho->setWidth( 10 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Estrutural" );
         $obLista->ultimoCabecalho->setWidth( 10 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Nome" );
         $obLista->ultimoCabecalho->setWidth( 28 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Cod" );
         $obLista->ultimoCabecalho->setWidth( 10 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Nome" );
         $obLista->ultimoCabecalho->setWidth( 28 );
         $obLista->commitCabecalho();

         $obLista->addDado();
         $obLista->ultimoDado->setAlinhamento( "DIREITA" );
         $obLista->ultimoDado->setCampo( "cod_receita" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setAlinhamento( "CENTRO" );
         $obLista->ultimoDado->setCampo( "cod_estrutural" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "nom_receita" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setAlinhamento( "DIREITA" );
         $obLista->ultimoDado->setCampo( "cod_recurso" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "nom_recurso" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setAlinhamento( "DIREITA" );
         $obLista->ultimoDado->setCampo( "percentual" );
         $obLista->commitDado();
         $obLista->addAcao();
         $obLista->ultimaAcao->setAcao( "EXCLUIR" );
         $obLista->ultimaAcao->setFuncao( true );
         $obLista->ultimaAcao->addCampo( "1","linha" );
         $obLista->ultimaAcao->setLink( "javascript: excluirDado('excluirReceitaSecundaria');" );
         $obLista->commitAcao();
         $obLista->montaHTML();
         $stHTML = $obLista->getHTML();
         $stHTML = str_replace("\n","",$stHTML);
         $stHTML = str_replace("  ","",$stHTML);
         $stHTML = str_replace("'","\\'",$stHTML);
     } else {
         $stHTML = "";
     }
     $stJs = "d.getElementById('spnReceitaSecundaria').innerHTML = '".$stHTML."';";

     return $stJs;
}

$arReceitasSecundarias = Sessao::read('arReceitasSecundarias');

switch ($_REQUEST["stCtrl"]) {
    case "buscaReceita":
        if ($_POST['inCodReceita'] != "") {
            $obROrcamentoReceita = new ROrcamentoReceita;
            $obROrcamentoReceita->setCodReceita( $_POST['inCodReceita'] );
            $obROrcamentoReceita->setExercicio( Sessao::getExercicio() );
            $obROrcamentoReceita->listar( $rsReceita );
            $stNomReceita = $rsReceita->getCampo( "descricao" );
            if (!$stNomReceita) {
                $js .= 'f.inCodReceita.value = "";';
                $js .= 'f.inCodReceita.focus();';
                $js .= 'd.getElementById("stNomReceita").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodReceita"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomReceita").innerHTML = "'.$stNomReceita.'";';
            }
        } else {
            $js .= 'd.getElementById("stNomReceita").innerHTML = "&nbsp;";';
        }
        SistemaLegado::executaFrameOculto($js);
    break;
    case "incluirReceitaSecundaria":
        $obROrcamentoReceita = new ROrcamentoReceita;
        $obROrcamentoReceita->setCodReceita( $_REQUEST['inCodReceita'] );
        $obROrcamentoReceita->setExercicio( Sessao::getExercicio() );
        $obErro = $obROrcamentoReceita->listar( $rsReceita );
        if ( !$obErro->ocorreu() ) {
            $stCodigoEstrutural = $rsReceita->getCampo( "mascara_classificacao" );
            $stNomeReceita      = $rsReceita->getCampo( "descricao" );
            $inCodigoRecurso    = $rsReceita->getCampo( "masc_recurso_red" );
            $stNomeRecurso      = $rsReceita->getCampo( "nom_recurso" );

            $obROrcamentoEntidade = new ROrcamentoEntidade;
            $obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
            $obROrcamentoEntidade->setCodigoEntidade( $rsReceita->getCampo("cod_entidade") );
            $obErro = $obROrcamentoEntidade->consultar( $rs );
            if ( !$obErro->ocorreu() ) {
                if ($_REQUEST['inCodReceita'] != $_REQUEST['inCodigoReceitaPrincipal']) {
   //                 if ( $obROrcamentoEntidade->getCodigoEntidade() == $_REQUEST['inCodigoEntidadePrincipal'] ) {
                        foreach ($arReceitasSecundarias as $arReceita) {
                            if ($arReceita["cod_receita"] == $_REQUEST['inCodReceita']) {
                                $stReceita = $_REQUEST['inCodReceita']." - ".$stCodigoEstrutural." - ".$stNomeRecurso;
                                $obErro->setDescricao( "Receita já informada!( ".$stReceita.") " );
                                break;
                            }
                        }
   //                 } else {
   //                     $obErro->setDescricao( "Só podem ser incluídas receitas de uma mesma entidade!" );
   //                 }
                } else {
                    $obErro->setDescricao( "A receita informada deve ser diferente da receita principal!" );
                }
                if ( !$obErro->ocorreu() ) {
                    $arReceitasSecundarias = Sessao::read('arReceitasSecundarias');
                    $arReceitasSecundarias[] = array( "cod_receita"    => $_REQUEST['inCodReceita'],
                                                "cod_estrutural" => $stCodigoEstrutural,
                                                "nom_receita"    => $stNomeReceita,
                                                "cod_recurso"    => $inCodigoRecurso,
                                                "nom_recurso"    => $stNomeRecurso,
                                                "percentual"     => $_REQUEST["flPercentual"],
                                                "linha"          => count( $arReceitasSecundarias ) + 1
                                               );
                    Sessao::write('arReceitasSecundarias', $arReceitasSecundarias);
                    $rsLista = new RecordSet;
                    $stPercentualAtualizado = $_REQUEST["stPercentualAtualizado"] - str_replace( ",", ".", $_REQUEST["flPercentual"] );
                    $rsLista->preenche( $arReceitasSecundarias );
                    //$rsLista->addStrPad( "cod_recurso", strlen($_REQUEST["stMascaraRecurso"]) );
                    $stJs  = "f.inCodReceita.value = \"\";\n";
                    $stJs .= "d.getElementById(\"stNomReceita\").innerHTML = \"&nbsp;\";\n";
                    $stJs .= "f.flPercentual.value = \"\";\n";
                    $stJs .= "f.stPercentualAtualizado.value = \"".$stPercentualAtualizado."\";\n";
                    $stPercentualAtualizadoInner = str_replace( ".", ",",$stPercentualAtualizado )."%";
                    $stJs .= "d.getElementById(\"stPercentualAtualizado\").innerHTML = \"".$stPercentualAtualizadoInner."\";\n";
                    $stJs .= montaListaReceitaSecundaria( $rsLista );
                }
            }
        }
        if ( $obErro->ocorreu() ) {
            $stJs = "alertaAviso('".urlencode($obErro->getDescricao())."','frm','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "excluirReceitaSecundaria":
        $arReceitaSecundaria = array();
        foreach ($arReceitasSecundarias as $inIndice => $arReceita) {
            if ( ( $inIndice + 1 ) != $_REQUEST["inLinha"] ) {
                $arReceita["linha"] = count( $arReceitaSecundaria ) + 1;
                $arReceitaSecundaria[] = $arReceita;
            } else {
                $stPercentualAtualizado = $_REQUEST["stPercentualAtualizado"] + str_replace( ",", ".", $arReceita["percentual"] );
            }
        }
        Sessao::write('arReceitasSecundarias', $arReceitaSecundaria);
        $rsLista = new RecordSet;
        $rsLista->preenche( $arReceitaSecundaria );
        $stJs = montaListaReceitaSecundaria( $rsLista );
        $stJs .= "f.stPercentualAtualizado.value = \"".$stPercentualAtualizado."\";\n";
        $stPercentualAtualizadoInner = str_replace( ".", ",",$stPercentualAtualizado )."%";
        $stJs .= "d.getElementById(\"stPercentualAtualizado\").innerHTML = \"".$stPercentualAtualizadoInner."\";
\n";
        SistemaLegado::executaFrameOculto( $stJs );
    break;
    case "limparReceitaSecundaria":
        Sessao::write('arReceitasSecundarias', array());
        $stJs  = "f.inCodReceita.value = '';\n";
        $stJs .= "d.getElementById('stNomReceita').innerHTML = '&nbsp;';\n";
        $stJs .= "f.flPercentual.value = '';\n";
        $stJs .= "d.getElementById('stPercentualAtualizado').innerHTML = '100,00%';";
        $stJs .= montaListaReceitaSecundaria( new RecordSet );
        SistemaLegado::executaFrameOculto( $stJs );
    break;
}
?>
