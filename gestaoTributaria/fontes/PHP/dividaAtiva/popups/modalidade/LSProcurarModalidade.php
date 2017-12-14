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
  * Página de Lista de Modalidade
  * Data de criação : 26/09/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: LSProcurarModalidade.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.04.07
**/

/*
$Log$
Revision 1.5  2007/09/13 19:52:37  cercato
correcao da paginacao da lista da modalidade.

Revision 1.4  2007/07/13 18:07:28  cercato
validando modalidades pela vigencia.

Revision 1.3  2007/06/22 19:11:55  cercato
setando tipo de modalidade no componente da modalidade.

Revision 1.2  2007/06/21 20:59:00  cercato
adicionado filtro por tipo de modalidade no componente.

Revision 1.1  2006/09/26 10:01:40  cercato
popup de busca modalidade.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarModalidade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?";
$pgJS   = "JS".$stPrograma.".js";

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read('link');

if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link', $link);

$stLink  = "&campoNom=".$request->get("campoNom");
$stLink .= "&campoNum=".$request->get("campoNum");
$stLink .= "&nomForm=".$request->get("nomForm");
$stLink .= "&tipoModalidade=". $_REQUEST['tipoModalidade'] ;

//MONTAGEM DO FILTRO
$stFiltro = ' dm.ativa = \'t\' AND dmv.vigencia_inicial <= \''.date('Y-m-d').'\' AND dmv.vigencia_final >= \''.date('Y-m-d').'\' AND ';

if ( $request->get('inCodigo') ) {
    $stFiltro .= " \n dm.cod_modalidade = '".$request->get('inCodigo')."' AND ";
}

if ( $request->get('stDescricao') ) {
    $stFiltro .= " \n dm.descricao LIKE '%".$request->get('stDescricao')."%' AND ";
}

if ( $request->get('dtVigenciaInicio') ) {
    $arData = explode( "/", $request->get('dtVigenciaInicio') );
    $stFiltro .= " \n dmv.vigencia_inicial = '".$arData[2]."-".$arData[1]."-".$arData[0]."' AND ";
}

if ( $request->get('dtVigenciaFim') ) {
    $arData = explode( "/", $request->get('dtVigenciaFim') );
    $stFiltro .= " \n dmv.vigencia_final = '".$arData[2]."-".$arData[1]."-".$arData[0]."' AND ";
}

if ( $request->get('tipoModalidade') ) {
    if ($_REQUEST["tipoModalidade"] == 4) {
        $stFiltro .= "  \n ( dmv.cod_tipo_modalidade = 2 OR dmv.cod_tipo_modalidade = 3 ) AND";
    } else {
        $stFiltro .= " \n dmv.cod_tipo_modalidade = ".$request->get('tipoModalidade')." AND ";
    }
} else {
        $stFiltro .= "  \n ( dmv.cod_tipo_modalidade = 2 OR dmv.cod_tipo_modalidade = 3 ) AND";
}

$stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

$obTDATModalidade = new TDATModalidade;
$obTDATModalidade->recuperaListaModalidade( $rsModalidade, $stFiltro, " ORDER BY dm.cod_modalidade " );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );
$obLista->setRecordSet( $rsModalidade );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Vigência" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_modalidade" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[vigencia_inicial] - [vigencia_final]" );
$obLista->commitDado();

$stAcao = "SELECIONAR";

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo("1","cod_modalidade");
$obLista->ultimaAcao->addCampo("2","descricao");
$obLista->commitAcao();
$obLista->show();

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );

$botoes = array ( $obBtnFiltro );

$obFormulario = new Formulario;
$obFormulario->addHidden($obHdnCampoNom);
$obFormulario->addHidden($obHdnCampoNum);
$obFormulario->defineBarra ( $botoes, 'left', '' );
$obFormulario->show();

include_once ( $pgJS );
