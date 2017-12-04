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
 * Classe de Visão para Configuração e de Homologação
 * Data de Criação: 02/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Heleno Menezes dos Santos <heleno.santos>
 * @author Pedro de Medeiros <pedro.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.01 - Manter
 * @uc 02.10.02 - Manter
 */

require_once CAM_FW_COMPONENTES.'Table/Table.class.php';
require_once CAM_GF_LDO_NEGOCIO.'RLDOManterConfiguracao.class.php';
require_once CAM_GF_LDO_UTIL.'LDOLista.class.php';
require_once CAM_GF_LDO_VISAO.'VLDOPadrao.class.php';
require_once CAM_GF_LDO_MAPEAMENTO.'TLDO.class.php';

class VLDOManterConfiguracao extends VLDOPadrao implements IVLDOPadrao
{
    /**
     * Mostra a Exceção gerada
     * @return void
     */
    public function emitirExcecao()
    {
        throw new VLDOExcecao('teste_de_ticket', $this->recuperarAnotacoes());
    }

    /**
     * Recupera a instância da classe
     * @return void
     */
    public static function recuperarInstancia($ob = NULL)
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    /**
     * Inicia as Regras da Classe
     * @return void
     */
    public function inicializar()
    {
        parent::inicializarRegra(__CLASS__);
    }

    /**
     * Método incluir
     * @return void
     */
    public function incluir(array $arParametros) {}

    /**
     * Método que altera a Configuração atual
     * @return void
     */
    public function alterar(array $arParametros)
    {
        try {
            $this->recuperarRegra()->alterar($arParametros);
            $stJS = "alertaAviso('Configurar LDO concluído com sucesso!', 'form', 'alterar', '". Sessao::getId() ."');";
            $stCaminho = 'FMManterConfiguracao.php?stAcao=alterar';
            $stJS.= "window.parent.frames['telaPrincipal'].location.href = '".$stCaminho."';";
            SistemaLegado::executaFrameOculto($stJS);

        } catch (VLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_alterar', 'erro');
        }
    }

    /**
     * Método que Homologa LDO
     * @return void
     */
    public function homologar(array $arParametros)
    {
        try {
            $inAnoLDO = $this->recuperarRegra()->homologar($arParametros);
            $stCaminho = 'FMHomologarLDO.php?stAcao=homologar';
            SistemaLegado::alertaAviso($stCaminho, $inAnoLDO, 'incluir', 'aviso', Sessao::getId(), '../');
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_homologar', 'erro');
        }
    }

    /**
     * Método excluir
     * @return void
     */
    public function excluir(array $arParametros) {}

    /**
     * Método monta a forma de arredondamento
     * @return String com js
     */
    public function montarArredondamentos()
    {
        $obNivelArrendondamento = new Select();
        $obNivelArrendondamento->setRotulo('Nível Arredondamento');
        $obNivelArrendondamento->setName('stNivel');
        $obNivelArrendondamento->setId('stNivel');
        $obNivelArrendondamento->addOption(0, 'Selecione');
        $obNivelArrendondamento->addOption('centavos', 'Centavos');
        $obNivelArrendondamento->addOption('unidade', 'Unidade');
        $obNivelArrendondamento->addOption('dezena', 'Dezena');
        $obNivelArrendondamento->addOption('centena', 'Centena');
        $obNivelArrendondamento->addOption('Milhar', 'Milhar');
        $obNivelArrendondamento->setObrigatorio(false);

        $obFormulario = new Formulario;
        $obFormulario->addComponente($obNivelArrendondamento);
        $obFormulario->montaInnerHTML();
        $return = "jq('#spnArredondamentos').html('".$obFormulario->getHTML()."');";

        return $return;
    }

    /**
     * Método que Valida os Indicadores para a lista
     * @param  array   $arParametros parâmetros da função
     * @param  String  $stAno
     * @return boolean
     */
    public function validarIndicadores(array $arParametros, $stAno)
    {
        $boLista = true;

        if (is_array($arParametros)) {
            foreach ($arParametros as $key => $value) {
                if ($stAno == $arParametros[$key]['stAno']) {
                    $boLista = false;
                }
            }
        }

        return $boLista;
    }

    /**
     * Recupera o LDO atual.
     * @return integer
     */
    public function recuperarLDOAtual()
    {
        try {
            return $this->recuperarRegra()->recuperarLDOAtual();
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_homologar', 'erro');
            throw new VLDOExcecao($e->getMessage());
        }
    }

    /**
     * Método que recebe o array da listagem de indicadores e retorna o array ordenado por exercicio
     *
     * @param   array array de dados da listagem de indicadores
     * @return array araay dos dados da listagem de indicadores ordenados por exercicio
     */
    public function ordenaArrayIndicadores(array $arDadosTipoIndicadores)
    {
        $arAux = array();
        foreach ($arDadosTipoIndicadores as $inChave => $arDados) {
            $arAux[$arDados['exercicio']] = $arDados;
        }
        sort($arAux);

        return $arAux;
    }

    /**
     * Método que realiza a validação dos dados na hora de inserir, caso encontre algum problema, retorna a mensagem de erro
     *
     * @author Henrique Girardi dos Santos
     * @param  array  $arParametros parâmetros da função
     * @return string contém a mensagem de erro caso encontre algum problema nos dados na hora de inserí-los
     */
    public function getMensagemValidaCamposInclusao(array $arParametros)
    {
        $stMensagem = '';
        // Valida se todos os campos estão preenchidos
        if ($arParametros['exercicio'] == '') {
            $stMensagem = 'O campo Exercício deve ser preenchido.';
        } elseif ($arParametros['inCodTipoIndicador'] == 0) {
            $stMensagem = 'O campo Tipo deve ser selecionado.';
        } elseif ($arParametros['flIndice'] == '' || $arParametros['flIndice'] == 'NaN') {
            $stMensagem = 'O campo Índice deve ser preenchido.';
        }

        if ($stMensagem == '') {
            // Verifica se não irá inserir dados repetidos na listagem
            $arLista = Sessao::read('arLista');
            if (is_array($arLista[$arParametros['inCodTipoIndicador']])) {
                $arAux = $arLista[$arParametros['inCodTipoIndicador']];
                foreach ($arAux as $arDados) {
                    if ($arDados['exercicio'] == $arParametros['exercicio']) {
                        $stMensagem = 'O Tipo de Indicador "'.$arParametros['descricao'].'" já foi informado para o exercício de '.$arParametros['exercicio'];
                        break;
                    }
                }
            }
        }

        return $stMensagem;
    }

    /**
     * Método que Insere os Indicadores em um array de Hidden
     *
     * @author Henrique Girardi dos Santos
     * @param  array  $arParametros parâmetros da função
     * @return string código javascript
     */
    public function incluirIndicador(array $arParametros)
    {
        // É validado os dados para inserir os dados, caso haja algum erro
        $stMensagem = $this->getMensagemValidaCamposInclusao($arParametros);
        $arCodTipoTipoIndicador = explode('_', $arParametros['inCodTipoIndicador']);
        $stJs = '';
        if ($stMensagem == '') {
            $arLista = Sessao::read('arLista');
            $arDados = array(
                  'cod_tipo_indicador'       => $arCodTipoTipoIndicador[0]
                , 'tipo_indicador_descricao' => $arParametros['descricao']
                , 'exercicio'                => $arParametros['exercicio']
                , 'indice'                   => $arParametros['flIndice']
            );
            $arLista[$arCodTipoTipoIndicador[0]][] = $arDados;
            $arLista[$arCodTipoTipoIndicador[0]] = $this->ordenaArrayIndicadores($arLista[$arCodTipoTipoIndicador[0]]);
            Sessao::write('arLista', $arLista);

            $stCaminho  = 'OCManterConfiguracao.php?cod_tipo_indicador='.$arParametros['inCodTipoIndicador'];
            $stCaminho .= '&stCtrl=montaListaIndicadores&linha_table_tree=obTblIndicadores_row_'.$arCodTipoTipoIndicador[0];
            $stCaminho .= '&componente=table_tree&descricao='.$arParametros['descricao'];

            // Depois de inserido, atualiza-se o valor do hidden que guarda os dados json da listagem para ser lido quando for gravado os dados
            $stJs .= "\n jq('#arLista').val('".json_encode($arLista)."'); ";

            // Como foi inserido algum dado na listagem, é chamado o método que abre a árvore da listagem, identificando assim para o usuário
            // onde que foi inserido os dados. Após isso é limpado os dados da tela para que possa ser inserido novos dados
            $stJs .= "\n TableTreeReq('obTblIndicadores_row_".$arCodTipoTipoIndicador[0]."', '".$stCaminho."');";
            $stJs .= "\n limparIndicador();";

        } else {
            // Caso retorne alguma mensagem de erro, é exibido um aviso na tela
            $stJs .= "\n alertaAviso('".$stMensagem."', 'n_incluir', 'erro', '".Sessao::getId()."');";
        }

        return $stJs;
    }

    /**
     * Método que Exclui os Indicadores da listagem de indicadores
     *
     * @author Henrique Girardi dos Santos
     * @param  array  $arParametros parâmetros da função
     * @return string código javascript
     */
    public function excluirIndicador(array $arParametros)
    {
        $stJs = '';

        // O parametro identificador traz os dados do ID da linha, onde tem os dados do codigo do tipo e a posicao do array seria a linha da
        // listagem menos 1, fechando assim a posição no array
        $arIndentificador = explode('_', $arParametros['identificador']);
        $inCodTipo = $arIndentificador[1];
        $inPosicao = --$arIndentificador[3];

        $arLista = Sessao::read('arLista');

        // Se é passado no parametro tipo um valor unico, ele pega diretamente na listagem a posição para deleta-lo da listagem,
        // Caso seja todos, limpa o array naquela posição
        switch ($arParametros['tipo']) {
        case 'unico':
            unset($arLista[$inCodTipo][$inPosicao]);
            sort($arLista[$inCodTipo]);
            break;
        case 'todos':
            $arLista[$inCodTipo] = array();
        }

        // Depois insere novamente a lista atualizada para que ela seja relida quando necessario
        Sessao::write('arLista', $arLista);

        $stJs  = "jq('#arLista').val('".json_encode($arLista)."');";

        // Se a listagem dos valores do tipo de indicador ficar vazia, fecha a table tree
        if (empty($arLista[$inCodTipo])) {
            $stJs .= "TableTreeLineControl('obTblIndicadores_row_".$inCodTipo."', 'none', '', 'none')";

        // Caso nao fique fazia, após exlcuir o indicador, é atualizada a listagem
        } else {
            $stJs .= "jq.post('OCManterConfiguracao.php'
                          , {
                              'stCtrl':'montaListaIndicadores'
                            , 'cod_tipo_indicador':'".$inCodTipo."'
                            , 'descricao':'".$arParametros['descricao']."'
                            , 'return':'script'
                            }
                          , ''
                          , 'script'
                         );";
        }

        return $stJs;
    }

    /**
     * Monta lista de dos Tipos de Indicadores quando o programa é carregado
     *
     * @author Henrique Girardi dos Santos
     * @param  array  $arParametros parâmetros da função
     * @return string código HTML
     */
    public function montaListaTipoIndicadores(array $arParametros)
    {
        $rsListaTipoIndicador = $this->recuperarListaTipoIndicador();

        $arLista = array();
        // Percorre os tipos de indicadores e busca os dados referentes a cada tipo para montar o array de dados
        while (!$rsListaTipoIndicador->eof()) {
            $stDscTipo = $rsListaTipoIndicador->getCampo('descricao');
            $inCodTipo = $rsListaTipoIndicador->getCampo('cod_tipo_indicador');

            $rsListaIndicador = $this->recuperarListaIndicador($inCodTipo);
            $arListaAux = array();
            foreach ($rsListaIndicador->getElementos() as $arCampos) {
                $arAux['cod_tipo_indicador']       = $inCodTipo;
                $arAux['tipo_indicador_descricao'] = $stDscTipo;
                $arAux['exercicio']                = $arCampos['exercicio'];
                $arAux['indice']                   = $arCampos['indice'];

                // adiciona valor aos indicadores
                $arListaAux[] = $arAux;

                $rsListaIndicador->proximo();
            }
            $arLista[$inCodTipo] = $arListaAux;
            $rsListaTipoIndicador->proximo();
        }

        // O valor esta sendo passado via JS para que nao haja problemas de escapes e nem de aspas na hora que o valor for setado com o json_encode
        $stJs .= "\n jq('#arLista').val('".json_encode($arLista)."');";
        Sessao::write('arLista', $arLista);

        $stJsExcuir = " jq.post('OCManterConfiguracao.php'
                            ,  {    'stCtrl':'excluirIndicador'
                                ,   'identificador':'_%s'
                                ,   'descricao':'%s'
                                ,   'tipo':'todos'
                               }
                            ,  ''
                            ,  'script'
                        );";
        $stJsExcuir = str_replace("'", "&quot;", $stJsExcuir);

        // Seta o primeiro elemento para montar a listagem
        $rsListaTipoIndicador->setPrimeiroElemento();
        $obTblIndicadores = new TableTree;
        $obTblIndicadores->setId('obTblIndicadores');
        $obTblIndicadores->setSummary('Lista de Indicadores');
        //$obTblIndicadores->setConditional(true, "#efefef");
        $obTblIndicadores->setArquivo('OCManterConfiguracao.php');
        $obTblIndicadores->setRecordSet($rsListaTipoIndicador);
        $obTblIndicadores->setParametros(array('cod_tipo_indicador', 'descricao'));
        $obTblIndicadores->setComplementoParametros('stCtrl=montaListaIndicadores');
        $obTblIndicadores->Head->addCabecalho('Tipo de Indicador', 90);
        $obTblIndicadores->Body->addCampo('descricao', 'C');
        $obTblIndicadores->Body->addAcao('excluir', $stJsExcuir, array('cod_tipo_indicador', 'descricao'));
        $obTblIndicadores->montaHTML(true);

        // Monta a listagem no span da lista
        $stJs .= "\n jq('#spnListaIndicadores').html('".$obTblIndicadores->getHtml()."');";
        $stJs .= "jq('#obTblIndicadores img[title=\'Excluir\']').each(function () {
            this.title = 'Excluir todos indicadores';
        })";

        return $stJs;
    }

    /**
     * Monta lista de Indicadores.
     *
     * @author Henrique Girardi dos Santos
     * @param  array  $arParametros parâmetros da função
     * @return string código HTML ou Javascript dependendo do momento. Se for clicado no '+' da table tree será retornado um html
     *                             se for deletado algum item da listagem, é chamado novamente esse metodo para remontar a listagem, e isso é feito via JS
     */
    public function montaListaIndicadores(array $arParametros)
    {
        $rsListaIndicador = new RecordSet;
        $arCodTipoTipoIndicador = explode('_', $arParametros['cod_tipo_indicador']);
        $arLista = Sessao::read('arLista');
        $rsListaIndicador->preenche($arLista[$arCodTipoTipoIndicador[0]]);
        $rsListaIndicador->addFormatacao('indice', 'NUMERIC_BR');

        $stTitulo = 'Lista de Indicadores de '.urldecode($arParametros['descricao']);

        $stJsExcuir = " jq.post('OCManterConfiguracao.php'
                            ,  {    'stCtrl':'excluirIndicador'
                                ,   'identificador':this.parentNode.id
                                ,   'descricao':'%s'
                                ,   'tipo':'unico'
                               }
                            ,  ''
                            ,  'script'
                        );";
        $stJsExcuir = str_replace("'", "&quot;", $stJsExcuir);

        $obTblIndicadores = new Table;
        $obTblIndicadores->setSummary($stTitulo);
        $obTblIndicadores->setId('obTbl_'.$arCodTipoTipoIndicador[0]);
        //$obTblIndicadores->setConditional(true, "#efefef");
        $obTblIndicadores->setRecordSet($rsListaIndicador);
        $obTblIndicadores->Head->addCabecalho('Exercício', 50);
        $obTblIndicadores->Head->addCabecalho('Índice', 40);
        $obTblIndicadores->Body->addCampo('exercicio', 'C');
        $obTblIndicadores->Body->addCampo('indice', 'D');
        $obTblIndicadores->Body->addAcao('excluir', $stJsExcuir, array('tipo_indicador_descricao'));
        $obTblIndicadores->montaHTML(true);

        if ($arParametros['return'] == 'script') {
            $stReturn .= "jq('#obTblIndicadores_row_".$arCodTipoTipoIndicador[0]."_sub_cell_2').html('".$obTblIndicadores->getHtml()."')";
        } else {
            $stReturn .= $obTblIndicadores->getHtml();
        }

        return $stReturn;
    }

    /**
     * Monta o simbolo do tipo de unidade de medida que fica ao lado do input de valor do indicador
     *
     * @author Henrique Girardi dos Santos
     * @param  array  $arParametros parâmetros da função
     * @return string código Javascript que insere um valor para o label que fica ao lado do input de valor do Indice
     */
    public function montaSimboloTipoIndicador(array $arParametros)
    {
        $stSimbolo = '';
        if (!empty($arParametros['inCodTipoIndicador'])) {
            $stSimbolo = $this->recuperarRegra()->retornaSimboloTipoIndicador($arParametros);
        }

        return "\n jq('span[name=\'spnSimbolo\']').text('".$stSimbolo."');";;
    }

    /**
     * Recupera os tipos de indicadores.
     * @return array array com os tipos de indicadores
     */
    public function recuperarListaTipoIndicador()
    {
        return $this->recuperarRegra()->recuperarListaTipoIndicador();
    }

    /**
     * Recupera lista de indicadores por código do tipo
     * @param $inCodTipo código do tipo
     * @param RecordSet o objeto contendo os elementos
     */
    public function recuperarListaIndicador($inCodTipo)
    {
        return $this->recuperarRegra()->recuperarListaIndicador($inCodTipo);
    }
}
