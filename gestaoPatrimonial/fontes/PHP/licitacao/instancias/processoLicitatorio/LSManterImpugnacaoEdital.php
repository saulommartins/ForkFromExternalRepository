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
    * Página de Filtro de fornecedor
    * Data de Criação   : 06/10/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    * Casos de uso: uc-03.05.15
*/
/*
$Log$
Revision 1.1  2007/06/11 19:01:06  hboaventura
Bug #9146#

Revision 1.8  2007/04/16 20:56:53  bruce
corrigida a ligação de adjudicação com licitacao na consulta.

Revision 1.7  2007/04/12 21:31:13  bruce
retirada linha que limpava o resultado da consulta

Revision 1.6  2007/04/11 20:11:07  bruce
alterei a consulta para excluir do resultado os julgamentos que já foram adjudicados

Revision 1.5  2007/04/10 21:48:15  bruce
Bug #9039#

Revision 1.4  2007/04/09 15:04:08  bruce
Bug #9018#

Revision 1.3  2007/03/27 21:36:44  hboaventura
Inclusão de filtros e listas

Revision 1.2  2007/03/27 21:30:44  hboaventura
Inclusão de filtros e listas

Revision 1.1  2007/03/27 13:42:23  hboaventura
Inclusão de filtros e listas

Revision 1.3  2007/01/29 17:50:39  hboaventura
Mudança da tabela tipo_objeto de licitação para compras

Revision 1.2  2006/10/30 17:05:05  fernando
Filtro para alterar e anular processo licitatorio

Revision 1.1  2006/10/06 17:47:23  fernando
inclusão do uc-03.05.15

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( TLIC."TLicitacaoEditalImpugnado.class.php" );

$stPrograma = "ManterImpugnacaoEdital";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTLicitacaoImpugnacaoEdital = new TLicitacaoEditalImpugnado();

$stCaminho = CAM_GP_LIC_INSTANCIAS."processoLicitatorio/";

$stAcao = $request->get("stAcao");

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$stFiltro="";

//MANTEM FILTRO E PAGINACAO
$stLink = "&stAcao=".$stAcao;
if ( isset($_GET["pg"]) and  isset($_GET["pos"]) ) {

    Sessao::write("link['pg']" , $_GET["pg"]);
    Sessao::write("link['pos']", $_GET["pos"]);
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array(Sessao::read('link')) ) {
    $_REQUEST = Sessao::read('link');
} else {
    foreach ($_REQUEST as $key => $valor) {
        Sessao::write("link[".$key."]", $valor);
    }
}

if ( isset($_REQUEST['stExercicioLicitacao']) ) {
    $obTLicitacaoImpugnacaoEdital->setDado( 'exercicio_licitacao', $_REQUEST['stExercicioLicitacao'] );
}

if ( isset($_REQUEST['inCodEntidade'])&&count($_REQUEST['inCodEntidade']) > 0 ) {
    $obTLicitacaoImpugnacaoEdital->setDado( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade']) );
}

if ( isset($_REQUEST['inCodModalidade']) ) {
    $obTLicitacaoImpugnacaoEdital->setDado( 'cod_modalidade', $_REQUEST['inCodModalidade'] );
}

if ( isset($_REQUEST['inCodLicitacao']) ) {
    $obTLicitacaoImpugnacaoEdital->setDado( 'cod_licitacao', $_REQUEST['inCodLicitacao'] );
}

if ( isset($_REQUEST['stChaveProcesso']) ) {
    $arProcesso = explode('/', $_REQUEST['stChaveProcesso']);
    $obTLicitacaoImpugnacaoEdital->setDado( 'cod_processo', intval($arProcesso[0]) );
}

if ( isset($_REQUEST['numEdital']) ) {
    $arEdital = explode('/',$_REQUEST['numEdital']);
    $obTLicitacaoImpugnacaoEdital->setDado( 'num_edital', $arEdital[0] );
}

if ( isset($_REQUEST['stMapaCompras']) ) {
    $arMapa = explode('/', $_REQUEST['stMapaCompras'] );
    $obTLicitacaoImpugnacaoEdital->setDado( 'cod_mapa', $arMapa[0] );
}

if ( isset($_REQUEST['inCodTipoLicitacao']) ) {
    $obTLicitacaoImpugnacaoEdital->setDado( 'cod_tipo_licitacao', $_REQUEST['stMapaCompras'] );
}

if ( isset($_REQUEST['inCodCriterio']) ) {
    $obTLicitacaoImpugnacaoEdital->setDado( 'cod_criterio', $_REQUEST['inCodCriterio'] );
}

if ( isset($_REQUEST['stObjeto']) ) {
    $obTLicitacaoImpugnacaoEdital->setDado( 'cod_objeto', $_REQUEST['stObjeto'] );
}

if ( isset($_REQUEST['inCodComissao']) ) {
    $obTLicitacaoImpugnacaoEdital->setDado( 'cod_comissao', $_REQUEST['inCodComissao'] );
}

if ($stAcao == 'incluir') {
    $stFiltro.= "
            AND  NOT EXISTS ( SELECT 1
                        FROM licitacao.edital_impugnado
                                LEFT JOIN licitacao.anulacao_impugnacao_edital
                                  ON anulacao_impugnacao_edital.num_edital = edital_impugnado.num_edital
                                 AND anulacao_impugnacao_edital.exercicio = edital_impugnado.exercicio
                                 AND anulacao_impugnacao_edital.cod_processo = edital_impugnado.cod_processo
                                 AND anulacao_impugnacao_edital.exercicio_processo = edital_impugnado.exercicio_processo
                               WHERE edital_impugnado.num_edital = le.num_edital
                                 AND edital_impugnado.exercicio = le.exercicio
                                 AND anulacao_impugnacao_edital.cod_processo is null
                            )";
} else {
    $stFiltro.= "
        AND  EXISTS ( SELECT 1
                        FROM licitacao.edital_impugnado
                        LEFT JOIN licitacao.anulacao_impugnacao_edital
                          ON anulacao_impugnacao_edital.num_edital = edital_impugnado.num_edital
                         AND anulacao_impugnacao_edital.exercicio = edital_impugnado.exercicio
                         AND anulacao_impugnacao_edital.cod_processo = edital_impugnado.cod_processo
                         AND anulacao_impugnacao_edital.exercicio_processo = edital_impugnado.exercicio_processo
                       WHERE edital_impugnado.num_edital = le.num_edital
                         AND edital_impugnado.exercicio = le.exercicio
                         AND anulacao_impugnacao_edital.num_edital IS NULL
                    )";
}

$stOrder = "
            ORDER BY
                le.exercicio DESC,
                le.num_edital,
                ll.exercicio DESC,
                ll.cod_entidade,
                ll.cod_licitacao,
                ll.cod_modalidade
";

$obTLicitacaoImpugnacaoEdital->recuperaEditalImpugnacao( $rsEdital,$stFiltro,$stOrder );

$rsEdital->setCampo( 'cod_processo', str_pad($rsEdital->getCampo( 'cod_processo' ), 5, "0", STR_PAD_LEFT), true );

$obLista = new Lista();

$rsEdital->setPrimeiroElemento();

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsEdital );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Licitação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Edital" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Processo" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Modalidade" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "num_licitacao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_entidade] - [entidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[num_edital]/[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[cod_processo]/[exercicio_processo]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_modalidade] - [descricao]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( 'selecionar' );

$obLista->ultimaAcao->addCampo("&inNumEdital" , "num_edital" );
$obLista->ultimaAcao->addCampo("&stExercicio" , "exercicio"  );

$obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );

$obLista->setAjuda("UC-03.05.16");
$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
