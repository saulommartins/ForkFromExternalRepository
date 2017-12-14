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
 * Página de formulário de Cadastro de Tipo de Indicador
 * Data de Criação: 09/01/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Analista     : Tonismar Regis Bernardo     <tonismar.bernardo@cnm.org.br>
 * @author Desenvolvedor: Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @package gestaoFinanceira
 * @subpackage ldo
 * @uc uc-02.10.02
 */

require_once CAM_GF_LDO_NEGOCIO.'RLDOPadrao.class.php';
require_once CAM_GF_LDO_MAPEAMENTO.'TLDOTipoIndicadores.class.php';
require_once CAM_GF_LDO_MAPEAMENTO.'TLDOIndicadores.class.php';

class RLDOManterTipoIndicador extends RLDOPadrao implements IRLDOPadrao
{
    /**
     * Recebe a instancia da classe TLDOTipoIndicadores
     * @var TLDOTipoIndicadores object
     */
    public $obTLDOTipoIndicadores;

    /**
     * Recebe a instancia da classe TLDOIndicadores
     * @var TLDOIndicadores object
     */
    public $obTLDOIndicadores;

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
    protected function inicializar()
    {
        $this->obTLDOTipoIndicadores = new TLDOTipoIndicadores;
        $this->obTLDOIndicadores     = new TLDOIndicadores;
    }

    /**
     * Metodo de inclusao dos dados passado na tela para a tabela ldo.tipo_indicador
     * Caso retorne algum erro na hora de inserir os dados na tabela, é lançada uma exceção para ser tratada na visão
     *
     * @throw RLDOExcecao caso retorne algum erro na hora de inserir os dados na tabela ldo.tipo_indicador
     * @return void
     */
    public function incluir(array $arArgs)
    {
        $obErro = new Erro;
        $this->obTLDOTipoIndicadores->recuperaTodos($rsTipoIndicadores);
        while (!$rsTipoIndicadores->EOF()) {
            $stDescricao = $rsTipoIndicadores->getCampo('descricao');
            $inCodUnidade = $rsTipoIndicadores->getCampo('cod_unidade');
            $inCodGrandeza = $rsTipoIndicadores->getCampo('cod_grandeza');
            if ($arArgs['inCodUnidade']  == $inCodUnidade
             && $arArgs['inCodGrandeza'] == $inCodGrandeza
             && $arArgs['stDescricao']   == $stDescricao) {
                throw new RLDOExcecao('Já existe um indicador cadastrado com esta descrição e unidade.', $this->recuperarAnotacoes());
            }
            $rsTipoIndicadores->proximo();
        }

        if (!$obErro->ocorreu()) {
            $obErro = $this->obTLDOTipoIndicadores->proximoCod($inCodTipoIndicador);
        }

        if (!$obErro->ocorreu()) {
            $this->obTLDOTipoIndicadores->setDado('cod_tipo_indicador', $inCodTipoIndicador);
            $this->obTLDOTipoIndicadores->setDado('cod_unidade'       , $arArgs['inCodUnidade']);
            $this->obTLDOTipoIndicadores->setDado('cod_grandeza'      , $arArgs['inCodGrandeza']);
            $this->obTLDOTipoIndicadores->setDado('descricao'         , $arArgs['stDescricao']);
            $obErro = $this->obTLDOTipoIndicadores->inclusao();
        }

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao incluir o tipo de indicador.', $this->recuperarAnotacoes());
        }
    }

    /**
     * Metodo de alteracao dos dados passado na tela para a tabela ldo.tipo_indicador
     * Caso retorne algum erro na hora de alterar os dados na tabela, é lançada uma exceção para ser tratada na visão
     *
     * @throw RLDOExcecao caso retorne algum erro na hora de inserir os dados na tabela ldo.tipo_indicador
     * @return void
     */
    public function alterar(array $arArgs)
    {
        $obErro = new Erro;

        $this->obTLDOTipoIndicadores->recuperaTodos($rsTipoIndicadores);
        while (!$rsTipoIndicadores->EOF()) {
            $stDescricao = $rsTipoIndicadores->getCampo('descricao');
            $inCodUnidade = $rsTipoIndicadores->getCampo('cod_unidade');
            $inCodGrandeza = $rsTipoIndicadores->getCampo('cod_grandeza');
            if ($arArgs['inCodUnidade']  == $inCodUnidade
             && $arArgs['inCodGrandeza'] == $inCodGrandeza
             && $arArgs['stDescricao']   == $stDescricao) {
                throw new RLDOExcecao('Já existe um indicador cadastrado com esta descrição e unidade.', $this->recuperarAnotacoes());
            }
            $rsTipoIndicadores->proximo();
        }

        $this->obTLDOTipoIndicadores->setDado('cod_tipo_indicador', $arArgs['inCodTipoIndicador']);
        $this->obTLDOTipoIndicadores->setDado('cod_unidade'       , $arArgs['inCodUnidade']);
        $this->obTLDOTipoIndicadores->setDado('cod_grandeza'      , $arArgs['inCodGrandeza']);
        $this->obTLDOTipoIndicadores->setDado('descricao'         , $arArgs['stDescricao']);
        $obErro = $this->obTLDOTipoIndicadores->alteracao();

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao alterar o tipo de indicador.', $this->recuperarAnotacoes());
        }
    }

    /**
     * Metodo de exclusao dos dados passado na tela para a tabela ldo.tipo_indicador
     * Caso retorne algum erro na hora de excluir os dados na tabela, é lançada uma exceção para ser tratada na visão
     *
     * @throw RLDOExcecao caso retorne algum erro na hora de inserir os dados na tabela ldo.tipo_indicador
     * @return void
     */
    public function excluir(array $arArgs)
    {
        $obErro = new Erro;
        $rsIndicadores = new RecordSet;

        $obErro = $this->obTLDOIndicadores->recuperaTodos($rsIndicadores, ' WHERE indicadores.cod_tipo_indicador = '.$arArgs['inCodTipoIndicador']);

        if (!$obErro->ocorreu()) {
            if ($rsIndicadores->getNumLinhas() > 0) {
                throw new RLDOExcecao('Não é possível excluir o tipo de indicador selecionado pois ele possui indicadores referenciados à ele.'
                                    , $this->recuperarAnotacoes());
            } else {
                $this->obTLDOTipoIndicadores->setDado('cod_tipo_indicador', $arArgs['inCodTipoIndicador']);
                $obErro = $this->obTLDOTipoIndicadores->exclusao();
            }
        }

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao excluir o tipo de indicador.', $this->recuperarAnotacoes());
        }
    }

    /**
     * Metodo que retorna os dados da tabela ldo.tipo_indicador
     * No primeiro parametro é passado se gostaria que o retorno fosse um array ou um recordset
     * Pode ser passado um array contendo os dados que gostaria de filtrar ou não passar nada que retorna tudo
     *
     * @author  Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     *
     * @param   array       contendo os dados que gostaria que fossem usado para filtrar os dados buscados da tabela ldo.tipo_indicador
     * @return recordSet recordSet contendo os dados que foram buscados
     */
    public function retornaDadosTipoIndicador(array $arFiltro = array())
    {
        if ($arFiltro['inCodTipoIndicador'] != '') {
            $this->obTLDOTipoIndicadores->setDado('cod_tipo_indicador', $arFiltro['inCodTipoIndicador']);
        }
        if ($arFiltro['inCodUnidade'] != '') {
            $this->obTLDOTipoIndicadores->setDado('cod_unidade', $arFiltro['inCodUnidade']);
        }
        if ($arFiltro['inCodGrandeza'] != '') {
            $this->obTLDOTipoIndicadores->setDado('cod_grandeza', $arFiltro['inCodGrandeza']);
        }
        if ($arFiltro['stDescricao'] != '') {
            $this->obTLDOTipoIndicadores->setDado('descricao', $arFiltro['stDescricao']);
        }

        $rsTipoIndicador = new RecordSet;
        $obErro = new Erro;
        $obErro = $this->obTLDOTipoIndicadores->recuperaRelacionamento($rsTipoIndicador, '', " \n ORDER BY tipo_indicadores.cod_tipo_indicador");

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao buscar os dados para verificar a similaridade.', $this->recuperarAnotacoes());
        }

        return $rsTipoIndicador;

    }
}
