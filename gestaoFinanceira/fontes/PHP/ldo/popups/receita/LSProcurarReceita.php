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
 * Pagina de PopUp Receita tipo do uc-02.10.04
 * Data de Criação: 17/02/209
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author <analista> Bruno Ferreira Santos <bruno.ferreira>
 * @author <desenvolvedor> Jânio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage ldo
 * @uc uc-02.10.04
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_PPA_CLASSES.'negocio/RPPAManterPrograma.class.php');
include_once(CAM_GF_PPA_CLASSES.'visao/VPPAManterPrograma.class.php');
include_once(CAM_GF_PPA_NEGOCIO.'RPPAManterReceita.class.php');

//Define o nome dos arquivos PHP
$stPrograma 	= "ProcurarReceita";
$pgFilt 		= "FL".$stPrograma.".php";
$pgList 		= "LS".$stPrograma.".php";
$pgJs   		= "JS".$stPrograma.".php";

include_once($pgJs);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

// Definicao dos objetos hidden
$obHdnForm = new Hidden();
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden();
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

$obHdnCampoNom = new Hidden();
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnTipoBusca = new Hidden();
$obHdnTipoBusca->setName( "tipoBusca" );
$obHdnTipoBusca->setValue( $_REQUEST['tipoBusca'] );

$obHdnExibeValorReceita = new Hidden();
$obHdnExibeValorReceita->setName ('boExibeValorReceita');
$obHdnExibeValorReceita->setValue($_REQUEST['boExibeValorReceita']);

$inAnoVigente = sessao::read('exercicio')+1;

$stCriterio .= " WHERE ppa.ano_inicio <=".$inAnoVigente." AND ppa.ano_final >= ".$inAnoVigente."\n";

if ($_REQUEST['inDescricaoReceita']) {
    $stCriterio .= " AND OCR.descricao LIKE '%".$_REQUEST['inDescricaoReceita']."%'";
}

//busca de referencia da receita
$obRPPAManterReceita = new RPPAManterReceita();
$stCriterio .= " AND PR.ativo = 't'                                 \n";
$stGroupBy  = " GROUP BY PR.cod_receita,                            \n";
$stGroupBy .= "          PR.cod_ppa,                                \n";
$stGroupBy .= "          PR.exercicio,                              \n";
$stGroupBy .= "          PR.cod_conta,                              \n";
$stGroupBy .= "          PR.cod_entidade,                           \n";
$stGroupBy .= "          PR.valor_total,                            \n";
$stGroupBy .= "          ppa.ano_inicio,                            \n";
$stGroupBy .= "          ppa.ano_final,                             \n";
$stGroupBy .= "          ppa.destinacao_recurso,                    \n";
$stGroupBy .= "          OCR.descricao,                             \n";
$stGroupBy .= "          PN.cod_norma,                              \n";
$stGroupBy .= "          CGM.nom_cgm ,                               \n";
$stGroupBy .= "          OCR.cod_estrutural                         \n";
$stCriterio .= $stGroupBy;
$stOrdem     = ' ORDER BY PR.cod_conta';

$rsReceita = $obRPPAManterReceita->pesquisar("TPPAReceita","recuperaListaReceitas",$stCriterio);

$obLista = new Lista();

$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Lista de Receitas');
$obLista->setRecordSet($rsReceita);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Código Receita');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Descrição da Receita');
$obLista->ultimoCabecalho->setWidth(70);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

# Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('cod_estrutural');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('descricao');
$obLista->commitDado();

# Define ação e caminho.
$stCaminho = $pgProx . '?' . Sessao::getID() . '&stReceita=' . $stReceita;

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->setFuncao(true);
$obLista->ultimaAcao->setLink("JavaScript:insereReceita()");
$obLista->ultimaAcao->addCampo( '&cod_receita' , 'cod_receita' );
$obLista->ultimaAcao->addCampo( 'cod_estrutural' , 'cod_estrutural' );
$obLista->ultimaAcao->addCampo( 'descricao'    , 'descricao' );
$obLista->ultimaAcao->addCampo( 'valorTotal'   , 'valor_total' );

$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario();

$obBtnCancelar = new Button();
$obBtnCancelar->setName( 'cancelar' );
$obBtnCancelar->setValue( 'Cancelar' );
$obBtnCancelar->obEvento->setOnClick( "window.close();" );

$obBtnFiltro = new Button();
$obBtnFiltro->setName( 'filtro' );
$obBtnFiltro->setValue( 'Filtro' );
$obBtnFiltro->obEvento->setOnClick( "Javascript:history.back(-1);" );

$obFormulario->defineBarra( array( $obBtnCancelar,$obBtnFiltro ) , '', '' );
$obFormulario->addHidden($obHdnForm);
$obFormulario->addHidden($obHdnCampoNum);
$obFormulario->addHidden($obHdnCampoNom);
$obFormulario->addHidden($obHdnTipoBusca);
$obFormulario->addHidden($obHdnExibeValorReceita);

$obFormulario->show();
