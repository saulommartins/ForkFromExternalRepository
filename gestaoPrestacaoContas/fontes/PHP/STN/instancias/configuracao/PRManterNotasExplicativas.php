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
 * Página de Processamento de Incluir Notas Explicativas
 *
 * Data de Criação: 23/06/2009
 * @author     Analista      : Tonismar Regis Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor : Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * $Id: $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GPC_STN_MAPEAMENTO.'TSTNNotaExplicativa.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterNotasExplicativas";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJs      = "JS".$stPrograma.".js";

// Pega-se os dados da listagem para poder manipulá-los localmente
$arValores = Sessao::read('arValores');

switch ($_POST["stAcao"]) {
case 'incluir':

    // Abre-se uma transação para poder atualizar dos os dados da tabela
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao($boFlagTransacao, $obTransacao);

    if (!$obErro->ocorreu()) {

        // Todos os dados da tabela são excluidos para que sejam inserido os novos dados
        $obTSTNNotaExplicativa = new TSTNNotaExplicativa;
        $obErro = $obTSTNNotaExplicativa->excluirTodos($obTransacao);

        // Se não ocorrer nenhum problema com a exclusão de todos os itens da tabela, vai para a segunda parte que é de inserir os novos dados
        if (!$obErro->ocorreu()) {

            // percorre-se o array da listagem para inserir os dados na tabela
            foreach ($arValores as $arDados) {
                $obTSTNNotaExplicativa->setDado('cod_acao'        , $arDados['inCodAcao']);
                $obTSTNNotaExplicativa->setDado('nota_explicativa', $arDados['stNotaExplicativa']);
                $obTSTNNotaExplicativa->setDado('dt_inicial'      , $arDados['stDtInicial']);
                $obTSTNNotaExplicativa->setDado('dt_final'        , $arDados['stDtFinal']);
                $obErro = $obTSTNNotaExplicativa->inclusao($obTransacao);

                // Caso haja algum erro na hora de inserir qualquer um dos dados, é parado o processamento
                if ($obErro->ocorreu()) {
                    break;
                }
            }
        }
    }

    // Caso não haja nenhum erro durante o processo de inclusão do dados, é exibido o aviso que a lista foi atualizada e os dados
    // são comitados e a transação é fechada
    if (!$obErro->ocorreu()) {
        $obTransacao->commitAndClose();
        $stCampos = 'modulo='.$_POST['inModulo'].'&funcionalidade='.$_POST['inFuncionalidade'].'&stAcao='.$_POST['stAcao'];
        SistemaLegado::alertaAviso($pgForm.'?'.$stCampos, 'Lista atualizada', 'incluir', 'aviso', Sessao::getId(), '../');

    } else {
        // Caso tenha ocorrido algum erro durante o processamento, é avisao ao usuário que não foi possível atualizar os dados e da-se um
        // rollback dos dados e fecha-se a transação
        $obTransacao->rollbackAndClose();
        SistemaLegado::exibeAviso('Não foi possível atualizar a listagem das Notas Explicativas.');
    }

    break;
}
