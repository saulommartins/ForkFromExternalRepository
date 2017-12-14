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
 * Página de listagem de Manter Ajuste de Anexo
 * Data de Criação: 17/02/2009
 *
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.05 - Manter Ajuste de Anexo
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GF_LDO_VISAO . 'VLDOManterNotaExplicativa.class.php';

# Define o nome dos arquivos PHP
$stModulo = 'ManterNotaExplicativa';
$pgForm   = 'FM' . $stModulo . '.php';
$pgFilt   = 'FL' . $stModulo . '.php';
$pgList   = 'LS' . $stModulo . '.php';
$pgProc   = 'PR' . $stModulo . '.php';
$pgOcul   = 'OC' . $stModulo . '.php';
$pgJS     = 'JS' . $stModulo . '.php';
$pgCons   = 'FMConsultarNotaExplicativa.php';

$inCodAnexo = (int) $_REQUEST['inCodAnexo'];

# Define destino da ação
$stAcao = trim(strtolower($_REQUEST['stAcao']));

# Cria filtro e paginação
if ($_GET["pg"] && $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $sessao->link["pg"]  = $_GET["pg"];
    $sessao->link["pos"] = $_GET["pos"];
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

# Obtem lista de Notas Explicativas
$rsListaNotaExplicativa = VLDOManterNotaExplicativa::recuperarInstancia()->recuperarListaNotaExplicativa($inCodAnexo);

# Formata campos
$arListaNotaExplicativa = $rsListaNotaExplicativa->getElementos();

# Formata descrição para caber na coluna.
foreach ($arListaNotaExplicativa as $inLinha => &$arColunas) {
    if (strlen($arColunas['descricao']) > 100) {
        $arColunas['descricao'] = stripslashes(substr($arColunas['descricao'], 0, 100)) . '...';
    }
}

$rsListaNotaExplicativa->preenche($arListaNotaExplicativa);

# Define lista de Notas Explicativas
$obLista = new Lista();
$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Lista de Notas Explicativas');
$obLista->setRecordSet($rsListaNotaExplicativa);

# Número da coluna
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

# Coluna Código
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Código');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

# Coluna Descrição
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Resumo');
$obLista->ultimoCabecalho->setWidth(70);
$obLista->commitCabecalho();

# Coluna Ação
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Ação');
$obLista->ultimoCabecalho->setWidth(7);
$obLista->commitCabecalho();

#
# Dados da lista
#

# Código da Nota Explicativa
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('cod_nota_explicativa');
$obLista->commitDado();

# Descrição da Nota Explicativa
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('descricao');
$obLista->commitDado();

# Define ação da lista
if ($stAcao == 'excluir') {
    $stCaminho  = CAM_GF_LDO_INSTANCIAS . 'notaExplicativa/' . $pgProc;
    $stCaminho .= '?' . Sessao::getId() . '&stAcao=' . $stAcao . $stLink;
} else {
    $stCaminho = $pgForm . '?' . Sessao::getID() . '&stAcao=' . $stAcao . $stLink;
}

$obLista->addAcao();
$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->addCampo('&inCodNotaExplicativa', 'cod_nota_explicativa');
$obLista->ultimaAcao->addCampo('stNotaExplicativa', 'descricao');
$obLista->ultimaAcao->addCampo('inAnoLDO', 'ano');
$obLista->ultimaAcao->addCampo( "&stDescQuestao","[cod_nota_explicativa] - [descricao]" );
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
