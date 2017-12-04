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

* @author Analista: Diego Victoria
* @author Desenvolvedor: Leandro André Zis

* @ignore

* Casos de uso :uc-03.04.07, uc-03.04.05
*/

/*
$Log$
Revision 1.8  2007/01/31 17:46:49  luciano
#8262#

Revision 1.7  2007/01/15 11:32:34  bruce
Bug #8064#

Revision 1.6  2007/01/15 10:20:35  bruce
Bug #8064#

Revision 1.5  2006/11/24 11:31:21  bruce
Bug's.: 7458, 759, 7496, 7662, 7607

Revision 1.4  2006/10/04 08:53:22  cleisson
novo componente IPopUpObjeto

Revision 1.3  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:10  diego


*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_COM_MAPEAMENTO."TComprasObjeto.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarObjeto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stFncJavaScript  = " function insereObjeto(num,nom) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').value = sNom; \n";
if (strstr($_REQUEST['stTipoBusca'],'popup')) {
    $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
}
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"  ].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"  ].".focus(); \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";


$stCaminho = CAM_GP_COM_INSTANCIAS."objeto/";

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

$stLink  = "&stAcao=".$stAcao;
$stLink .= '&campoNom='.$_REQUEST['campoNom'];
$stLink .= '&nomForm='.$_REQUEST['nomForm'];
$stLink .= '&campoNum='.$_REQUEST['campoNum'];
$stLink .= '&stTipoBusca='.$_REQUEST['stTipoBusca'];

$obTComprasObjeto = new TComprasObjeto;
$rsLista = new RecordSet;
$stFiltro = isset($stFiltro) ? $stFiltro : "";

if ( $request->get('stDescricao') ) {
   $stFiltro .= " where descricao ilike '". $_REQUEST['stHdnDescricao']."'";
   $stLink   .= "&stDescricao=".$_REQUEST['stDescricao'];
   $stLink   .= "&stHdnDescricao=".$_REQUEST['stHdnDescricao'];

}

$stLink .= "&stAcao=".$stAcao;
$stOrdem = " objeto.cod_objeto ";

$obTComprasObjeto->recuperaTodos($rsLista, $stFiltro, $stOrdem );

while ( !$rsLista->eof() ) {
    $rsLista->setCampo( 'descricao', htmlentities(stripslashes($rsLista->getCampo('descricao') ), ENT_NOQUOTES, 'UTF-8' ) );
    $rsLista->proximo();
}
$rsLista->setPrimeiroElemento();

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Objetos cadastrados");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_objeto" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();
$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereObjeto();" );
$obLista->ultimaAcao->addCampo("1","cod_objeto");
$obLista->ultimaAcao->addCampo("2","descricao");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

?>
