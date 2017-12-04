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
 * Página de Listagem de Ação
 * Data de Criação   : 14/07/2004

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: LSManterAcao.php 35664 2008-11-17 18:48:50Z pedro.medeiros $

 * Casos de uso: uc-02.09.04
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GF_PPA_NEGOCIO . 'RPPAManterAcao.class.php';
include_once CAM_GF_PPA_VISAO   . 'VPPAManterAcao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ConsultarAcao';
$pgFilt = 'FL' . $stPrograma . '.php';
$pgList = 'LS' . $stPrograma . '.php';
$pgForm = 'FM' . $stPrograma . '.php';
$pgProc = 'PR' . $stPrograma . '.php';
$pgOcul = 'OC' . $stPrograma . '.php';
$pgExcl = 'FMExcluirAcao.php';
$pgCons = 'FMConsultarAcao.php';

//$stCaminho = CAM_GF_PPA_INSTANCIAS . 'acao/';

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ? $_GET['stAcao'] : $_POST['stAcao'];

if (empty($stAcao)) {
    $stAcao = 'alterar';
}

# Define arquivos de instância para cada ação.
switch ($stAcao) {
case 'alterar':
    $pgProx = $pgForm;
    break;

case 'excluir':
    $pgProx = $pgExcl;
    break;

case 'consultar':
    $pgProx = $pgCons;
    break;

default:
    $pgProx = $pgForm;
    break;
}

# Mantem dados de filtro e paginação.
if ($_GET['pg'] and $_GET['pos']) {
    $sessao->link['pg']  = $_GET['pg'];
    $sessao->link['pos'] = $_GET['pos'];
} elseif (is_array($sessao->link)) {
    $_GET = $sessao->link;
    $_REQUEST = $sessao->link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $sessao->link[$key] = $valor;
    }
}

$obRPPAManterAcao = new RPPAManterAcao();
$obVPPAManterAcao = new VPPAManterAcao( $obRPPAManterAcao );

$rsAcoes = $obVPPAManterAcao->recuperaListaAcoes($_REQUEST['inNumPrograma'], $_REQUEST['inCodAcaoInicio'], $_REQUEST['inCodAcaoFim']);

$obLista = new Lista();

$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Lista de Ações');
$obLista->setRecordSet($rsAcoes);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Código Ação');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Descrição da Ação');
$obLista->ultimoCabecalho->setWidth(70);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Valor da Ação');
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

# Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('cod_acao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('descricao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('valor');
$obLista->commitDado();

# Define ação e caminho.
$stCaminho = $pgProx . '?' . Sessao::getID() . '&stAcao=' . $stAcao;

$obLista->addAcao();
$obLista->ultimaAcao->setAcao(strtoupper($stAcao));
$obLista->ultimaAcao->addCampo('&inCodAcao', 'cod_acao');
$obLista->ultimaAcao->addCampo('inNumPrograma', 'num_programa');
$obLista->ultimaAcao->addCampo('inCodPrograma', 'cod_programa');
$obLista->ultimaAcao->addCampo('stDescricao', 'descricao');
$obLista->ultimaAcao->addCampo('tsAcaoDados', 'ultimo_cod_acao_dados');
$obLista->ultimaAcao->setLink($stCaminho);
$obLista->commitAcao();

$obLista->show();

?>
