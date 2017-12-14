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
 * Classe de Regra de Negócio para Configuração.
 * Data de Criação: 02/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Heleno Menezes dos Santos <heleno.santos>
 * @author Pedro de Medeiros <pedro.medeiros>
 * @package gestaoFinanceira
 * @subpackage ldo
 * @uc uc-02.10.01 / uc-02.10.02
 */

include_once CAM_GF_LDO_NEGOCIO    . 'RLDOManterConfiguracao.class.php';
include_once CAM_GF_LDO_NEGOCIO    . 'RLDOPadrao.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOHomologacaoLDO.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOTipoIndicadores.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOIndicadores.class.php';

class RLDOManterConfiguracao extends RLDOPadrao implements IRLDOPadrao
{
    public function emitirExcecao()
    {
        throw new RLDOExcecao('Excecao de Teste', $this->recuperarAnotacoes());
    }

    /**
     * Invoca classe de mapeamento com chamada e critério em uma só etapa.
     * @param  string    $stMapeamento o nome da classe de mapeamento
     * @param  string    $stMetodo     o método invocado para a classe de mapeamento
     * @param  string    $stFiltro     o critério que delimita a busca
     * @return RecordSet objeto contendo o resultado da consulta
     */
    protected function pesquisar($stMapeamento, $stMetodo, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $obMapeamento = new $stMapeamento();
        $obErro = $obMapeamento->$stMetodo($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro de SQL', $this->recuperarAnotacoes());
        }

        return $rsRecordSet;
    }

    public static function recuperarInstancia($ob = NULL)
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    protected function inicializar() {}

    /**
     * Exclui indicador
     * @param  integer $inAno       ano do indicador
     * @param  float   $flIndice    índice do indicador
     * @param  integer $inCodTipo   o código do tipo do indicador
     * @param  boolean $boTransacao se a operação usa ou não transação
     * @return Erro    erro se existir
     */
    public function excluirIndicador($inAno, $flIndice, $inCodTipo, $boTransacao = '')
    {
        $obMapeamento = new TLDOIndicadores();
        $obMapeamento->setDado('exercicio', $inAno);
        $obMapeamento->setDado('indice', $flIndice);
        $obMapeamento->setDado('cod_tipo_indicador', $inCodTipo);

        return $obMapeamento->exclusao($boTransacao);
    }

    /**
     * Exclui Todos indicadores
     * @param  boolean $boTransacao se a operação usa ou não transação
     * @return Erro    erro se existir
     */
    public function excluirTodosIndicadores($boTransacao = '')
    {
        $obMapeamento = new TLDOIndicadores();

        return $obMapeamento->excluirTodos($boTransacao);
    }

    /**
     * Inclui indicador
     * @param  integer $inAno       ano do indicador
     * @param  float   $flIndice    índice do indicador
     * @param  integer $inCodTipo   o código do tipo do indicador
     * @param  boolean $boTransacao se a operação usa ou não transação
     * @return Erro    erro se existir
     */
    public function incluirIndicador($inAno, $flIndice, $inCodTipo, $boTransacao = '')
    {
        $obMapeamento = new TLDOIndicadores();
        $obMapeamento->setDado('exercicio', $inAno);
        $obMapeamento->setDado('indice', $flIndice);
        $obMapeamento->setDado('cod_tipo_indicador', $inCodTipo);

        return $obMapeamento->inclusao($boTransacao);
    }

    /**
     * Agrupa lista de Indicadores para comparação e inclusão no banco de dados.
     * @param  array $arParametros os parametros recebidos do formulário
     * @return array lista formatada com os indicadores
     */
    public function agruparIndicadores(array $arParametros)
    {
        $arListaIndicador = array();
        $arDscTipo = $arParametros['arDscTipo'];

        foreach ($arDscTipo as $inCodTipo => $stDscTipo) {
            $arListaAnoLDO = $arParametros["arLista{$stDscTipo}AnoLDO"];
            $arListaIndice = $arParametros["arLista{$stDscTipo}Indice"];

            for ($i = 0; $i < count($arListaAnoLDO); $i++) {
                $arCampos = array();
                $arCampos['cod_tipo_indicador'] = $inCodTipo + 1;
                $arCampos['exercicio'] = $arListaAnoLDO[$i];
                $arCampos['indice'] = $arListaIndice[$i];

                array_push($arListaIndicador, $arCampos);
            }
        }

        return $arListaIndicador;
    }

    /**
     *
     *
     *
     */
    public function retornaSimboloTipoIndicador(array $arParametros)
    {
        $obMapeamento = new TLDOTipoIndicadores;
        if (isset($arParametros['inCodTipoIndicador'])) {
            $obMapeamento->setDado('cod_tipo_indicador', $arParametros['inCodTipoIndicador']);
        }
        if (isset($arParametros['inCodGrandeza'])) {
            $obMapeamento->setDado('cod_grandeza', $arParametros['inCodGrandeza']);
        }
        if (isset($arParametros['inCodUnidade'])) {
            $obMapeamento->setDado('cod_unidade', $arParametros['inCodUnidade']);
        }
        $obMapeamento->recuperaSimboloTipoIndicador($rsTipoIndicador);

        return $rsTipoIndicador->getCampo('simbolo');
    }

    /**
     * Compara os Indicadores atuais com os Indicadores já salvos para incluir e
     * excluir de acordo com os resultados.
     * @param  array $arParametros os parametros recebidos do formulário
     * @return Erro  erro se existir
     */
    private function alterarIndicadores(array $arParametros, $boTransacao = '')
    {
        $obErro = new Erro();
        $arIndicadores = array();
        $arIndicadores = get_object_vars(json_decode(stripslashes($arParametros['arLista'])));

        // Exclui todos os indicadores para que eles sejam inseridos novamente
        $obTLDOIndicadores = new TLDOIndicadores();
        $obTLDOIndicadores->recuperaTodos($rsIndicadores);
        if($rsIndicadores->getNumLinhas() >0){
            while(!$rsIndicadores->EOF()){
                $obErro = $this->excluirIndicador($rsIndicadores->getCampo('exercicio'), $rsIndicadores->getCampo('indice'), $rsIndicadores->getCampo('cod_tipo_indicador'), $boTransacao);
                $rsIndicadores->proximo();
            }
        }
        
        if (!$obErro->ocorreu()) {
            // Inclui os Indicadores.
            foreach ($arIndicadores as $inCodTipoIndicador => $arDadosTipo) {
                foreach ($arDadosTipo as $arCampos) {
                    $obErro = $this->incluirIndicador($arCampos->exercicio, $arCampos->indice, $inCodTipoIndicador, $boTransacao);
                    if ($obErro->ocorreu()) {
                        break;
                    }
                }
            }
        }

        return $obErro;
    }

    public function incluir(array $arArgs) {}

    public function excluir(array $arArgs) {}

    /**
     * Altera os dados de Configuração
     * @param  array   $arArgs os parâmetros da função
     * @return integer o ano do LDO
     */
    public function alterar(array $arArgs)
    {
        $obTransacao = new Transacao();
        $obFlagTransacao = false;
        $boTransacao = '';

        # Inicia transação
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('erro ao tentar iniciar uma transacao', $this->recuperarAnotacoes());
        }

        # Compara estado atual com o estado anterior e faz as operações necessárias.
        $this->alterarIndicadores($arArgs, $boTransacao);

        # Fecha transação se tudo OK.
        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->incluirIndicadores);

        return $arArgs['inAnoLDO'];
    }

    /**
     * Inclui registro na tabela ldo.homologar_ldo
     * @param  array   $arParametros os parâmetros da função
     * @return integer o ano do LDO
     */
    public function incluirHomologacaoLDO($arParametros, $boTransacao = '')
    {
        list($inCodProcesso, $inAnoExercicio) = explode('/', $arParametros['stChaveProcesso']);

        $obMapeamento = new TLDOHomologacaoLDO();

        if ($this->pesquisarNorma($arParametros, $boTransacao)) {
            throw new RLDOExcecao('Norma já cadastrada para este LDO.', $this->recuperarAnotacoes());
        }

        $obMapeamento->setDado('ano', $arParametros['inAnoLDO']);
        $obMapeamento->setDado('cod_norma', $arParametros['inCodNorma']);
        $obMapeamento->setDado('cod_processo', $inCodProcesso);
        $obMapeamento->setDado('ano_exercicio', $inAnoExercicio);
        $obMapeamento->setDado('numcgm_veiculo', $arParametros['inCodEmpresa']);
        $obMapeamento->setDado('dt_encaminhamento', $arParametros['dtEncaminhamento']);
        $obMapeamento->setDado('dt_devolucao', $arParametros['dtDevolucao']);

        if ($obMapeamento->recuperaTodos($rsLDO, $stFiltro, '', $boTransacao)) {
            if (!empty($rsLDO->arElementos[0])) {
                throw new RLDOExcecao('LDO já Homologado!', $this->recuperarAnotacoes());
            }
        }

        $obErro = $obMapeamento->inclusao($boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao homologar o LDO.', $this->recuperarAnotacoes());
        }

        return $arParametros['inAnoLDO'];
    }

    /**
     * Homologa o LDO especificado
     * @param  array   $arArgs os parâmetros da função
     * @return integer o ano do LDO
     */
    public function homologar(array $arArgs)
    {
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = '';

        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            $inAnoLDO = $this->incluirHomologacaoLDO($arArgs, $boTransacao);
        }

        if (!$obErro->ocorreu()) {
            $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, new TLDOHomologacaoLDO());
        }

        return $inAnoLDO;
    }

    /**
     * Retorna se existe LDO com o código da Norma especificado
     * @param  array   $arParametros os parâmetros da função
     * @return boolean se o LDO existe
     */
    public function pesquisarNorma(array $arParametros, $boTransacao = '')
    {
        $obMapeamento = new TLDOHomologacaoLDO();

        $stFiltro = ' WHERE cod_norma = ' . $arParametros['inCodNorma'];
        $obErro = $obMapeamento->recuperaTodos($rsNormaLDO, $stFiltro, '', $boTransacao);

        if (!$obErro->ocorreu()) {
            return !$rsNormaLDO->eof();
        }

        return false;
    }

    /**
     * Recupera os dados do LDO atual
     * @param  boolean   $boTransacao se há transação ativa
     * @return RecordSet objeto contendo os dados do LDO
     */
    public function recuperarLDOAtual($boTransacao = '')
    {
        $stFiltro = ' WHERE ano = ' . Sessao::getExercicio();

        $obMapeamento = new TLDO();
        $obErro = $obMapeamento->recuperaTodos($rsLDO, $stFiltro, '', $boTransacao);

        if ($rsLDO->eof()) {
            throw new RLDOExcecao('Erro ao obter o LDO.', $this->recuperarAnotacoes());
        }

        return $rsLDO->getCampo('ano') + 1;
    }

    /**
     * Recupera lista de tipo de Indicador
     * @param  boolean   $boTransacao se há transação ativa
     * @return RecordSet objeto contendo lista de tipo de Indicador
     */
    public function recuperarListaTipoIndicador($boTransacao = '')
    {
        $obMapeamento = new TLDOTipoIndicadores();
        $obErro = @$obMapeamento->recuperaTodos($rsTiposIndicadores, '', '', $boTransacao);

        return $rsTiposIndicadores;
    }

    /**
     * Cria filtro para recuperar dados relativos a LDO
     * @param  array  $arParametros os parâmetros da função
     * @return string filtro com os valores especificados
     */
    private function recuperarFiltro(array $arParametros)
    {
        $stFiltro = '';
        $stSepararador = ' ';

        if ($arParametros['inCodTipoIndicador']) {
            $stFiltro .= $stSepararador . 'cod_tipo_indicador = ' . $arParametros['inCodTipoIndicador'];
            $stSepararador = ' AND ';
        }

        return $stFiltro;
    }

    /**
     * Recupera lista de Indicadores
     * @param  integer   $inCodTipo   o código do tipo dos Indicadores
     * @param  boolean   $boTransacao se há transação ativa
     * @return RecordSet objeto contendo a lista de objetos
     */
    public function recuperarListaIndicador($inCodTipo, $boTransacao = '')
    {
        $obMapeamento = new TLDOIndicadores();
        $stFiltro = ' WHERE cod_tipo_indicador = ' . $inCodTipo;
        $stOrdem  = ' ORDER BY exercicio';

        $obErro = @$obMapeamento->recuperaTodos($rsListaIndicador, $stFiltro, $stOrdem, $boTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Erro ao tentar obter a lista de Indicadores.', $this->recuperarAnotacoes());
        }

        return $rsListaIndicador;
    }
}
