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
 * Página de listagem de Manter Expansão das Despesas de Caráter Continuado
 * Data de Criação: 23/03/2009
 *
 *
 * @author Analista: Bruno Ferreira <bruno.ferreira>
 * @author Programador: Pedro Vaz de Mello de Medeiros <pedro.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.05 - Manter Ajuste de Anexo
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GF_LDO_VISAO . 'VLDOManterDespesaContinua.class.php';

# Define o nome dos arquivos PHP
$stModulo = 'ManterDespesaContinua';
$pgForm   = 'FM' . $stModulo . '.php';
$pgFilt   = 'FL' . $stModulo . '.php';
$pgList   = 'LS' . $stModulo . '.php';
$pgProc   = 'PR' . $stModulo . '.php';
$pgOcul   = 'OC' . $stModulo . '.php';
$pgJS     = 'JS' . $stModulo . '.php';

# Define destino da ação
$stAcao = $request->get('stAcao');

# Cria filtro e paginação
if ($_GET["pg"] && $_GET["pos"]) {
    $stLink .= '&pg=' . $_GET['pg'] . '&pos=' . $_GET['pos'];
    $sessao->link['pg']  = $_GET['pg'];
    $sessao->link['pos'] = $_GET['pos'];
}

# Armeza dados dos filtros
$link = Sessao::read('link');

if (is_array($link)) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }

    Sessao::write('link', $link);
}

# Define campos Hidden
$obHdnAcao = new Hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);

# Define Formulário
$obForm = new Form();
$obForm->setAction($pgFilt);
$obForm->setTarget('telaPrincipal');
$obForm->setEncType('multipart/form-data');

# Obtem lista de Despesas Contínuas
$rsLista = VLDOManterDespesaContinua::recuperarInstancia()->recuperarLista($_REQUEST);
$rsLista->addFormatacao('aumento_permanente', NUMERIC_BR);
$rsLista->addFormatacao('transferencia_constitucional', NUMERIC_BR);
$rsLista->addFormatacao('transferencia_fundeb', NUMERIC_BR);
$rsLista->addFormatacao('reducao_permanente', NUMERIC_BR);
$rsLista->addFormatacao('saldo_utilizado_margem_bruta', NUMERIC_BR);
$rsLista->addFormatacao('docc', NUMERIC_BR);
$rsLista->addFormatacao('docc_ppp', NUMERIC_BR);

# Define lista de Despesas Contínuas
$obLista = new Lista();
$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Lista de Expansões das Despesas de Caráter Continuado');
$obLista->setRecordSet($rsLista);

# Número da coluna
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

# Número da coluna
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Código da Despesa');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

# Coluna Aumento Permanente da Receita
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Aumento Permanente da Receita');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

# Coluna Transferências Constitucionais
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Transferências Constitucionais');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

# Coluna Transferências ao FUNDEB
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Transferências ao FUNDEB');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

# Coluna Redução Permanente de Despesa
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Redução Permanente de Despesa');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

# Coluna Saldo Utilizado da Margem Bruta
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Saldo Utilizado da Margem Bruta');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

# Coluna Novas DOCC
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Novas DOCC');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

# Coluna Novas DOCC geradas por PPP
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Novas DOCC geradas por PPP');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

# Coluna Ação
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Ação');
$obLista->ultimoCabecalho->setWidth(7);
$obLista->commitCabecalho();

#
# Dados da lista
#

# Coluna do Código da Despesa Contínua
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('cod_despesa');
$obLista->commitDado();

# Coluna do Aumento Permanente
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('aumento_permanente');
$obLista->commitDado();

# Coluna de Transferência Constitucional
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('transferencia_constitucional');
$obLista->commitDado();

# Coluna de Transferência ao FUNDEB
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('transferencia_fundeb');
$obLista->commitDado();

# Coluna de Redução Permanente de Despesa
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('reducao_permanente');
$obLista->commitDado();

# Coluna de Saldo Utilizado da Margem Bruta
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('saldo_utilizado_margem_bruta');
$obLista->commitDado();

# Coluna de Novas DOCC
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('docc');
$obLista->commitDado();

# Coluna de Novas DOCC geradas por PPP
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('docc_ppp');
$obLista->commitDado();

# Define coluna da Ação
$obLista->addAcao();
$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->addCampo('&inCodDespesa',         'cod_despesa');
$obLista->ultimaAcao->addCampo('inAnoLDO',              'ano');
$obLista->ultimaAcao->addCampo('flAumentoPermanente',   'aumento_permanente');
$obLista->ultimaAcao->addCampo('flTransConstitucional', 'transferencia_constitucional');
$obLista->ultimaAcao->addCampo('flTransFUNDEB',         'transferencia_fundeb');
$obLista->ultimaAcao->addCampo('flReducaoPermanente',   'reducao_permanente');
$obLista->ultimaAcao->addCampo('flMargemBruta',         'saldo_utilizado_margem_bruta');
$obLista->ultimaAcao->addCampo('flDOCC',                'docc');
$obLista->ultimaAcao->addCampo('flDOCCPPP',             'docc_ppp');

# Define ação da lista
if ($stAcao == 'excluir') {
    $obLista->ultimaAcao->addCampo('stDescQuestao', '[cod_despesa] - [ano]');
    $stCaminho  = CAM_GF_LDO_INSTANCIAS . 'configuracao/' . $pgProc;
    $stCaminho .= '?' . Sessao::getId() . '&stAcao=' . $stAcao . $stLink;
} else {
    $stCaminho = $pgForm . '?' . Sessao::getID() . '&stAcao=' . $stAcao . $stLink;
}

$obLista->ultimaAcao->setLink($stCaminho);
$obLista->commitAcao();

$obLista->montaHTML();

# Define Span que contem lista de Notas Explicativas
$obSpnListaNotaExplicativa = new Span();
$obSpnListaNotaExplicativa->setValue($obLista->getHTML());

# Acrescenta elementos na tela.
$obFormulario = new Formulario();
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addForm($obForm);
$obFormulario->addSpan($obSpnListaNotaExplicativa);

$obFormulario->show();

?>
