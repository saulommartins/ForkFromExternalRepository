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
    * Página de lista para o cadastro de tipo de edificação
    * Data de Criação   : 15/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcelos Ferreira

    * @ignore

    * $Id: LSManterTipoEdificacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.10
*/

/*
$Log$
Revision 1.7  2006/09/18 10:31:41  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMTipoEdificacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoEdificacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GT_CIM_INSTANCIAS."tipoEdificacao/";

$obRCIMTipoEdificacao = new RCIMTipoEdificacao;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'baixar' : $pgProx = $pgBaix; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
} else {
     $link = array();
}

Sessao::write('link', $link);
Sessao::write('stLink', $stLink);

//USADO QUANDO EXISTIR FILTRO
/*
if ( is_array($sessao->link) ) {
    $_REQUEST = $sessao->link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $sessao->link[$key] = $valor;
    }
}
*/

//FIM MANTEM FILTRO E PAGINACAO

$obRCIMTipoEdificacao->listarTiposEdificacao( $rsListaTipo );

$obLista = new Lista;

$obLista->setRecordSet( $rsListaTipo );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_tipo" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_tipo" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodigoTipo","cod_tipo");
$obLista->ultimaAcao->addCampo("&stNomeTipo","nom_tipo");
$obLista->ultimaAcao->addCampo("&stDescQuestao","[cod_tipo]-[nom_tipo]");
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.10" );
$obFormulario->show();

?>
