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

* Casos de uso :uc-03.04.07

$Id: LSManterObjeto.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_COM_MAPEAMENTO."TComprasObjeto.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterObjeto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GP_COM_INSTANCIAS."configuracao/";

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

if ($_REQUEST['stDescricao'] || $_REQUEST['inCodObjeto']) {
    foreach ($_REQUEST as $key => $value) {
        #sessao->transf4['filtro'][$key] = $value;
        $filtro[$key] = $value;
    }
    Sessao::write('filtro', $filtro);
} else {
    if ( Sessao::read('filtro') ) {
        foreach ( Sessao::read('filtro') as $key => $value ) {
            $_REQUEST[$key] = $value;
        }
    }

    Sessao::write('paginando' , true);
}

$obTComprasObjeto = new TComprasObjeto;
$rsLista = new RecordSet;

$stFiltro = "";
$filtro = Sessao::read('filtro');

if ($filtro['stDescricao']) {
   $stFiltro .= " descricao ilike '". $filtro['stHdnDescricao']."' AND \n";
}
if ($filtro['inCodObjeto']) {
   $stFiltro .= " cod_objeto = ".$filtro['inCodObjeto']." AND \n";
}

$stFiltro= $stFiltro ? " WHERE ".substr($stFiltro,0,strlen( $stFiltro) - 5 ): "";

$stFiltro .= " ORDER BY cod_objeto ";

$obTComprasObjeto->recuperaTodos($rsLista, $stFiltro );

while ( !$rsLista->eof() ) {
    $rsLista->setCampo( 'descricao', stripslashes($rsLista->getCampo('descricao') )       );
    $rsLista->proximo();
}

$rsLista->setPrimeiroElemento();

$obLista = new Lista;

$obLista->setAjuda('UC-03.04.07');
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

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodigo"          , "cod_objeto" );
// Comentado, pois se o texto for muito grande, ultrapassa o tamanho da URL.
//$obLista->ultimaAcao->addCampo( "&stDescricao"    , "descricao" );
$obLista->ultimaAcao->addCampo( "&stDescQuestao"  ,"[cod_objeto]");
$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
$obLista->commitAcao();
$obLista->show();

?>
