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
    * Página  de LIstagem  para cadastro de Monviemtação de SEFIP
    * Data de Criação: 14/02/2006

    *Autor: Bruce Cruz de Sena

    * Caso de uso: uc-04.04.40
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalSefip.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterMovimentacaoSEFIP";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);
include_once ($pgOcul);

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stCaminho = CAM_GRH_PES_INSTANCIAS."assentamento/";
$stAcao = $request->get('stAcao');

if ($stAcao != 'SELECIONAR') {
    //MANTEM FILTRO E PAGINACAO
    $arLink = Sessao::read('link');
    $stLink .= "&stAcao=".$stAcao;
    if ($_GET["pg"] and  $_GET["pos"]) {
        $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
        $arLink["pg"]  = $_GET["pg"];
        $arLink["pos"] = $_GET["pos"];
    }
    //USADO QUANDO EXISTIR FILTRO
    //NA FL O VAR LINK DEVE SER RESETADA
    if ( is_array($arLink) ) {
        $_REQUEST = $arLink;
    } else {
        foreach ($_REQUEST as $key => $valor) {
            $arLink[$key] = $valor;
        }
        Sessao::write('link', $arLink);
    }
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}

// pegando os parametros para a consulta
$stCodigoSEFIP   = $_REQUEST['stCodigoSEFIP'     ] ;
$stDescricao     = $_REQUEST['stDescricao'       ] ;
$stTipo          = $_REQUEST['stMovimentacao'    ] ;
$boMovSaida      = $_REQUEST['stMovimentacaoAfas'] ;
$boMovRetorno    = $_REQUEST['stMovimentacaoRet' ] ;
$boApenasRetorno = $_REQUEST['boApenasRetorno'   ] ;

if ($boApenasRetorno) {
    $boMovRetorno = true;
    $boMovSaida   = false;
}

// definindo filtro de dados
$obRsSefip = new RecordSet;

// definindo o tipo de sefip que será lista pode ser retorno, afastamento ou ambas
if ($boMovSaida == $boMovRetorno) {
      // listar ambos;
      include_once ( CAM_GRH_PES_NEGOCIO.'RPessoalSefip.class.php' );
      $obRSefip = new RPessoalSefip;
} else { if ($boMovRetorno) {
         // listar apenas de retorno;
         include_once ( CAM_GRH_PES_NEGOCIO."RPessoalMovimentoSefipRetorno.class.php"  );
         $obRSefip = new RPessoalMovimentoSefipRetorno;
      } else {
         // listar apenas de afastamento;
         include_once ( CAM_GRH_PES_NEGOCIO.'RPessoalMovimentoSefipSaida.class.php' );
         $obRSefip = new RPessoalMovimentoSefipSaida;
      }
}

if ($stCodigoSEFIP) { $obRSefip->setNumSefip( $stCodigoSEFIP ); }
if ($stDescricao) { $obRSefip->setDescricao( $stDescricao);   }

if ($boMovRetorno == $boMovSaida) {$obErro   = $obRSefip->listar( $obRsSefip) ; } elseif ($boMovRetorno) { $obErro = $obRSefip->listarMovimentoSefipRetorno ( $obRsSefip, $stFiltro );} else { $obErro = $obRSefip->listarMovSefipSaida( $obRsSefip, $stFiltro );}

// Criação da lista
$obLista = new Lista;
//$obLista->setTitulo ("Eventos Cadastrados");
$stTitulo = ' </div></td></tr><tr><td colspan="5" class="alt_dados">Eventos Cadastrados';
$obLista->setTitulo             ('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia().$stTitulo);

$obLista->setRecordSet($obRsSefip);
// cabecalho da lista
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 1 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição");
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
// fim cabecalho

//Dados da lista
$obLista->addDado();
$obLista->ultimoDado->setCampo( "num_sefip" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
//Fim dados

$obLista->addAcao();

if ($stAcao == "SELECIONAR") {

    $obLista->ultimaAcao->addCampo("&num_sefip", "num_sefip" );
    $obLista->ultimaAcao->addCampo ("&descricao","descricao");

    $stFncJavaScript .= " function insereSefip(num,nom) {  \n";
    $stFncJavaScript .= " var sNum;                  \n";
    $stFncJavaScript .= " var sNom;                  \n";
    $stFncJavaScript .= " sNum = num;                \n";
    $stFncJavaScript .= " sNom = nom;                \n";
    $stFncJavaScript .= " if ( window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."') ) { window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; } \n";
    $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum; \n";
    $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".focus(); \n";
    $stFncJavaScript .= " window.close();            \n";
    $stFncJavaScript .= " }                          \n";

    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:insereSefip();" );

} else {
    $obLista->ultimaAcao->addCampo("&cod_sefip", "cod_sefip" );

    $obLista->ultimaAcao->setAcao( $stAcao );
    if ($stAcao == "excluir") {
        $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"descricao");
        $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink);
    } elseif ($stAcao == "alterar") {
        $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
    }
}

$obLista->commitAcao();

$obLista->Show();

if ($stAcao == "SELECIONAR") {
    $obFormulario = new Formulario;
    $obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
    $obFormulario->show();
}

?>
