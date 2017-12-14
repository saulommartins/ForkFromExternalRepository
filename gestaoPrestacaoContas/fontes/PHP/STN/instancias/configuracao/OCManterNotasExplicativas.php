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
 * Página Oculto de Incluir Notas Explicativas
 *
 * Data de Criação: 23/06/2009
 * @author Analista      : Tonismar Regis Bernardo <tonismar.bernardo@cnm.org.br>
 * @author Desenvolvedor : Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * $Id: $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
require_once CAM_GPC_STN_MAPEAMENTO.'TSTNNotaExplicativa.class.php';

$stPrograma = "ManterNotasExplicativas";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

/**
 * retornaErroValidaCadastro
 *
 * realiza a validação dos dados do cadastro que manda os dados a listagem, caso seja encontrado algum erro, é retornado a string contendo
 * a mensagem de erro
 *
 * @author Analista      : Tonismar Regis Bernardo <tonismar.bernardo@cnm.org.br>
 * @author Desenvolvedor : Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 *
 * @return string
 */
function retornaErroValidaCadastro()
{
    $stMensagem = '';
    if (trim($_REQUEST['inCodAcao']) == '') {
        $stMensagem = '@Selecione um Anexo.';

    } elseif (trim($_REQUEST['stDtInicial']) == '') {
        $stMensagem = '@Informe a Data Inicial.';

    } elseif (trim($_REQUEST['stDtFinal']) == '') {
        $stMensagem = '@Informe a Data Final.';

    } elseif (SistemaLegado::comparaDatas($_REQUEST['stDtInicial'], $_REQUEST['stDtFinal'])) {
        $stMensagem = '@A Data Inicial não pode ser maior que a Data Final.';

    } elseif (trim($_REQUEST['stNotaExplicativa']) == '') {
        $stMensagem = '@Informe a Nota Explicativa.';
    }

    return $stMensagem;
}

/**
 * montaListaItens
 *
 * Monta a lista dos itens que são cadastrados na tela
 *
 * @author Analista      : Tonismar Regis Bernardo <tonismar.bernardo@cnm.org.br>
 * @author Desenvolvedor : Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 *
 * @return string
 */
function montaListaItens($arRecordSet)
{
    $rsDotacoesItem = new RecordSet;
    $rsDotacoesItem->preenche($arRecordSet);

    $table = new Table;
    $table->setRecordset  ($rsDotacoesItem);
    $table->setSummary    ('Itens Incluídos');
    //$table->setConditional(true, "#efefef");

    $table->Head->addCabecalho('Anexo'       , 60);
    $table->Head->addCabecalho('Data Inicial', 10);
    $table->Head->addCabecalho('Data Final'  , 10);

    $table->Body->addCampo('stAnexo'    , "E");
    $table->Body->addCampo('stDtInicial', "C");
    $table->Body->addCampo('stDtFinal'  , "C");

    $table->Body->addAcao('consultar', "jq.post('OCManterNotasExplicativas.php', {'id':'%s', 'stCtrl':'consultarItem'}, ''   , 'script');", array('id'));
    $table->Body->addAcao('alterar'  , "jq.post('OCManterNotasExplicativas.php', {'id':'%s', 'stCtrl':'alterarItem'}, ''     , 'script');", array('id'));
    $table->Body->addAcao('excluir'  , "jq.post('OCManterNotasExplicativas.php', {'id':'%s', 'stCtrl':'excluirItemLista'}, '', 'script');", array('id'));

    $table->montaHTML(true);

    return "\n jq('#spnListaItens').html('".$table->getHtml()."');";
}

// Pega os dados da listagem da sessao para manipulá-los localmente
$arValores = Sessao::read('arValores');

// Inicializa-se a variável que receberá os javascript necessários a executar
$stJs = '';

switch ($_REQUEST["stCtrl"]) {

// Responsável por incluir os dados do formulário na listagem, é chamado quando o formulário está no seu estado normal de origem
case 'incluirListaCadastro':

    // Método que realiza a validação dos campos, retornando uma mensagem de erro caso encontre algum erro
    $stMensagem = retornaErroValidaCadastro();

    // Se não encontrou nenhum erro, então realiza o processo de inclusão na lista
    if ($stMensagem == '') {
        $boDotacaoRepetida = false;

        // Percorre o array dos dados da listagem para verificar se já existe o dado na listagem
        foreach ($arValores as $arTEMP) {
            if ($arTEMP['inCodAcao'] == $_REQUEST['inCodAcao'] && $arTEMP['stDtInicial'] == $_REQUEST['stDtInicial']
             && $arTEMP['stDtFinal'] == $_REQUEST['stDtFinal']) {
                $boDotacaoRepetida = true;
                break;
            }
        }

        // Caso já exista o dado na listagem, não adiciona os dados ao array da sessão e retorna um erro
        if (!$boDotacaoRepetida) {

            // Explode o dado para poder pegar o nome da ação e a funcionalidade, que são os dados compostos na combo de anexo
            $arAnexo = explode(' - ', $_REQUEST['stAnexo']);
            $stNotaExplicativa = str_replace('\\\n', " \n", $_REQUEST['stNotaExplicativa']);

            $arValoresAux['id']                  = count($arValores);
            $arValoresAux['stNomAcao']           = $arAnexo[1];
            $arValoresAux['stNomFuncionalidade'] = $arAnexo[0];
            $arValoresAux['inCodAcao']           = $_REQUEST['inCodAcao'];
            $arValoresAux['stDtInicial']         = $_REQUEST['stDtInicial'];
            $arValoresAux['stDtFinal']           = $_REQUEST['stDtFinal'];
            $arValoresAux['stNotaExplicativa']   = utf8_decode($stNotaExplicativa);
            $arValoresAux['stAnexo']             = $_REQUEST['stAnexo'];

            // Insere-se os dados novos ao array da listagem
            $arValores[] = $arValoresAux;
            Sessao::write('arValores', $arValores);

            // Limpa os dados via função javascript
            $stJs .= "\n limparCadastro();";

            // remonta a listagem dos itens com o novo item
            $stJs .= montaListaItens($arValores);

        } else {
            $stJs .= "alertaAviso('Esse item já consta na listagem.','form','erro','".Sessao::getId()."');";
        }
    } else {
        $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    }
    break;

// Usado quando o programa é carregado lá no document.ready do arquivo JS. Ele é chamado somente nessa hora e serve para mostrar a listagem
// e inicializar os dados na sessao
case 'carregarListagem':

    // realiza a busca de todos os dados da tabela
    $rsRecordSetItem = new RecordSet;
    $obTSTNNotaExplicativa = new TSTNNotaExplicativa;
    $obTSTNNotaExplicativa->listNotaExplicativa($rsRecordSetItem);

    $inCount = 0;
    // percorre os dados e inicializa-se o array, o id representa a posição do array dos dados no $arValores, logo começa com o valor ZERO
    // depois ele é usado para poder passar a linha da listagem para poder alterar, consultar e excluir mais facilmente os dados da listagem
    while (!$rsRecordSetItem->eof()) {
        $arValoresTmp['id']                  = $inCount++;
        $arValoresTmp['stNomAcao']           = $rsRecordSetItem->getCampo('nom_acao');
        $arValoresTmp['stNomFuncionalidade'] = $rsRecordSetItem->getCampo('nom_funcionalidade');
        $arValoresTmp['inCodAcao']           = $rsRecordSetItem->getCampo('cod_acao');
        $arValoresTmp['stDtInicial']         = sistemaLegado::dataToBr($rsRecordSetItem->getCampo('dt_inicial'));
        $arValoresTmp['stDtFinal']           = sistemaLegado::dataToBr($rsRecordSetItem->getCampo('dt_final'));
        $arValoresTmp['stNotaExplicativa']   = $rsRecordSetItem->getCampo('nota_explicativa');
        $arValoresTmp['stAnexo']             = $rsRecordSetItem->getCampo('nom_funcionalidade').' - '.$rsRecordSetItem->getCampo('nom_acao');

        $arValores[] = $arValoresTmp;
        $rsRecordSetItem->proximo();
    }
    Sessao::write('arValores', $arValores);
    $stJs .= montaListaItens($arValores);
    break;

// Chamado pela ação alterar da listagem, ele carrega os dados novamente no formulário, pegando o id passado como parametro e assim identificando
// o dado necessário no array da listagem
case 'alterarItem':
    $inKey = $_REQUEST['id'];
    $stNotaExplicativa = addslashes($arValores[$inKey]['stNotaExplicativa']);
    $stJs.= "\n jq('#stNotaExplicativa').val('".$stNotaExplicativa."').attr('disabled', false).focus();";
    $stJs.= "\n jq('#stHdnId').val('".$_REQUEST['id']."');";
    $stJs.= "\n jq('#stDtInicial').val('".$arValores[$inKey]['stDtInicial']."').attr('disabled', false);";
    $stJs.= "\n jq('#stDtFinal').val('".$arValores[$inKey]['stDtFinal']."').attr('disabled', false);";
    $stJs.= "\n jq('#inCodAcao').val('".$arValores[$inKey]['inCodAcao']."').attr('disabled', true);";
    $stJs.= "\n jq('#limpar').attr('disabled', false);";
    $stJs.= "\n jq('#incluir').attr('value', 'Alterar').unbind('click').bind('click', function () {chamaOcultoPost('alterarListaCadastro')});";
    break;

// Chamado pela ação consultar da listagem, ele carrega os dados novamente no formulário, pegando o id passado como parametro e assim identificando
// o dado necessário no array da listagem
case 'consultarItem':
    $inKey = $_REQUEST['id'];
    $stNotaExplicativa = addslashes($arValores[$inKey]['stNotaExplicativa']);
    $stJs.= "\n jq('#stNotaExplicativa').val('".$stNotaExplicativa."').attr('disabled', true);";
    $stJs.= "\n jq('#stHdnId').val('".$_REQUEST['id']."');";
    $stJs.= "\n jq('#stDtInicial').val('".$arValores[$inKey]['stDtInicial']."').attr('disabled', true);";
    $stJs.= "\n jq('#stDtFinal').val('".$arValores[$inKey]['stDtFinal']."').attr('disabled', true);";
    $stJs.= "\n jq('#inCodAcao').val('".$arValores[$inKey]['inCodAcao']."').attr('disabled', true);";
    $stJs.= "\n jq('#incluir').val('Retornar').unbind('click').bind('click', function () {limparCadastro()});";
    $stJs.= "\n jq('#limpar').attr('disabled', true);";
    break;

// Chamado pela ação excluir da listagem, ele realiza o processo de excluir o dado do array da sessao que contem os dados da listagem
case 'excluirItemLista':
    // deleta a linha selecionada
    unset($arValores[$_REQUEST['id']]);

    // reordena novamente as chaves do array, colocando-as em ordem crescente
    sort($arValores);

    // reorganiza o valor do id de acordo com a nova chave
    foreach ($arValores as $key => $value) {
        $arValores[$key]['id'] = $key;
    }

    Sessao::write('arValores', $arValores);
    $stJs .= "\n limparCadastro();";
    $stJs .= montaListaItens($arValores);
    break;

// Quando selecina-se a ação alterar na listagem, o botão incluir tem seu valor mudado para alterar e essa opção torna-se a ação do botão
// na hora de realizar a alteração de um dado.
case 'alterarListaCadastro':

    // Verifica-se novamente se existe algum erro no cadastro
    $stMensagem = retornaErroValidaCadastro();

    if ($stMensagem == '') {
        // verifica-se se o dado a ser alterado já não possui na listagem, assim evitando a inclusão de dados duplicados
        $inCodAcao = $arValores[$_REQUEST['id']]['inCodAcao'];
        $boDotacaoRepetida = false;
        foreach ($arValores as $arTEMP) {
            if ($arTEMP['inCodAcao']   == $inCodAcao
             && $arTEMP['stDtInicial'] == $_REQUEST['stDtInicial']
             && $arTEMP['stDtFinal']   == $_REQUEST['stDtFinal']
             && $arTEMP['id']          != $_REQUEST['id']) {
                $boDotacaoRepetida = true;
                break;
            }
        }

        if (!$boDotacaoRepetida) {
            // Caso o dado não seja duplicado, é alterado os valores do dado para a sua posição no array de dados da listagem
            // sendo necessário somente esses 3 dados, pois o Anexo não é alterado e o id continuará o mesmo
            $arValores[$_REQUEST['id']]['stNotaExplicativa'] = utf8_decode($_REQUEST['stNotaExplicativa']);
            $arValores[$_REQUEST['id']]['stDtInicial']       = $_REQUEST['stDtInicial'];
            $arValores[$_REQUEST['id']]['stDtFinal']         = $_REQUEST['stDtFinal'];
            Sessao::write('arValores', $arValores);

            $stJs .= "\n limparCadastro();";
            $stJs .= "alertaAviso('Nota Explicativa alterada com sucesso!','form','erro','".Sessao::getId()."');";
            $stJs .= montaListaItens($arValores);
        } else {
            $stJs .= "alertaAviso('Esse item já consta na listagem.','form','erro','".Sessao::getId()."');";
        }
    } else {
        $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    }
    break;
}

echo $stJs;
