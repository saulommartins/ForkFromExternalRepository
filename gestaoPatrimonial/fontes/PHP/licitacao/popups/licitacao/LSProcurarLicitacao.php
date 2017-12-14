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
* Página de Listagem da Objeto
* Data de Criação   : 04/07/2007

* @author Desenvolvedor: Leandro André Zis

* @ignore

* $Id: LSProcurarLicitacao.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso :uc-03.05.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarLicitacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

  $stFncJavaScript .= " function insereLicitacao(num,ent,mod,obj) {  \n";
  $stFncJavaScript .= " var sNum;                  \n";
  $stFncJavaScript .= " sNum = num;                \n";
//  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').value = sNom; \n";
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('inCodEntidade').value = ent; \n";
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('inCodModalidade').value = mod; \n";
  $stFncJavaScript .= " if (window.opener.parent.frames['telaPrincipal'].document.getElementById('hdnDescObjeto')) { \n";
  $stFncJavaScript .= "   window.opener.parent.frames['telaPrincipal'].document.getElementById('stDescObjeto').innerHTML = obj; \n";
  $stFncJavaScript .= "   window.opener.parent.frames['telaPrincipal'].document.getElementById('hdnDescObjeto').value = obj; \n";
  $stFncJavaScript .= " }   \n";
if (strstr($_REQUEST['stTipoBusca'],'popup')) {
//    $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
}
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"  ].".value = sNum; \n";
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"  ].".focus(); \n";
  $stFncJavaScript .= " window.close();            \n";
  $stFncJavaScript .= " }                          \n";

$stCaminho = CAM_GP_COM_INSTANCIAS."licitacao/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}

$stLink = "&stAcao=".$stAcao;

$filtro = Sessao::read('filtro');
if ($_REQUEST[''] || $_REQUEST['inCodEntidade']) {
    foreach ($_REQUEST as $key => $value) {
        $filtro[$key] = $value;
    }
} else {
    if ($filtro) {
        foreach ($filtro as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }
    Sessao::write('paginando', true);
}
Sessao::write('filtro', $filtro);

$obTLicitacaoLicitacao = new TLicitacaoLicitacao;
$rsLista = new RecordSet;

if ($_REQUEST['inCodLicitacao']) {
   $stFiltro .= " ll.cod_licitacao = ". $_REQUEST['inCodLicitacao']." and ";
}
if ($_REQUEST['inCodModalidade']) {
   $stFiltro .= " ll.cod_modalidade = ". $_REQUEST['inCodModalidade']." and ";
}
if ($_REQUEST['inCodEntidade']) {
   $stFiltro .= " ll.cod_entidade = ". $_REQUEST['inCodEntidade']." and ";
}
$stFiltro .= " ll.exercicio = '". Sessao::getExercicio() ."' and ";

$stFiltro = ($stFiltro)?' and '.substr($stFiltro,0,strlen($stFiltro)-4):'';

$obTLicitacaoLicitacao->recuperaLicitacao($rsLista, $stFiltro );

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Licitações cadastradas");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entidade" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Número da Licitação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Processo Administrativo" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Modalidade" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_licitacao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "processo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "modalidade" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereLicitacao();" );
$obLista->ultimaAcao->addCampo("1","cod_licitacao");
$obLista->ultimaAcao->addCampo("2","cod_entidade");
$obLista->ultimaAcao->addCampo("3","cod_modalidade");
$obLista->ultimaAcao->addCampo("4","desc_objeto");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

?>
