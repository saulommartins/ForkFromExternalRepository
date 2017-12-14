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
 * Classe de regra de Validar Acao
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id: $
 */

include_once CAM_GF_PPA_NEGOCIO.'RPPAManterAcao.class.php';
include_once CAM_GF_LDO_NEGOCIO.'RLDOLDO.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDOAcaoValidada.class.php';
include_once CAM_GF_PPA_MAPEAMENTO.'TPPAAcaoQuantidade.class.php';

class RLDOValidarAcao
{
    public $obTransacao,
           $obRPPAManterAcao,
           $obRLDOLDO,
           $obTLDOAcaoValidada,
           $flQuantidade,
           $flValor;

    /**
     * Método contrutor, instancia as classes necessarias.
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        $this->obTransacao        = new Transacao();
        $this->obRPPAManterAcao   = new RPPAManterAcao();
        $this->obRLDOLDO          = new RLDOLDO();
        $this->obTLDOAcaoValidada = new TLDOAcaoValidada();
    }

    /**
     * Método para incluir a validacao da acao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function incluir($boTransacao = '')
    {
        $this->obTLDOAcaoValidada->setDado('cod_acao'            , $this->obRPPAManterAcao->inCodAcao);
        $this->obTLDOAcaoValidada->setDado('ano'                 , $this->obRPPAManterAcao->inAno);
        $this->obTLDOAcaoValidada->setDado('timestamp_acao_dados', $this->obRPPAManterAcao->stTimestampAcaoDados);
        $this->obTLDOAcaoValidada->setDado('valor'               , $this->flValor);
        $this->obTLDOAcaoValidada->setDado('quantidade'          , $this->flQuantidade);
        $this->obTLDOAcaoValidada->setDado('cod_recurso'         , $this->obRPPAManterAcao->inCodRecurso);
        $this->obTLDOAcaoValidada->setDado('exercicio_recurso'   , $this->obRPPAManterAcao->stExercicioRecurso);

        $obErro = $this->obTLDOAcaoValidada->inclusao($boTransacao);

        if (!$obErro->ocorreu()) {
            $obErro = $this->obRLDOLDO->updateTimestamp($boTransacao);
        }

        return $obErro;
    }

    /**
     * Método para alterar a validacao da acao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function alterar($boTransacao = '')
    {
        $this->obTLDOAcaoValidada->setDado('cod_acao'            , $this->obRPPAManterAcao->inCodAcao);
        $this->obTLDOAcaoValidada->setDado('ano'                 , $this->obRPPAManterAcao->inAno);
        $this->obTLDOAcaoValidada->setDado('timestamp_acao_dados', $this->obRPPAManterAcao->stTimestampAcaoDados);
        $this->obTLDOAcaoValidada->setDado('valor'               , $this->flValor);
        $this->obTLDOAcaoValidada->setDado('quantidade'          , $this->flQuantidade);
        $this->obTLDOAcaoValidada->setDado('cod_recurso'         , $this->obRPPAManterAcao->inCodRecurso);
        $this->obTLDOAcaoValidada->setDado('exercicio_recurso'   , $this->obRPPAManterAcao->stExercicioRecurso);

        $obErro = $this->obTLDOAcaoValidada->alteracao($boTransacao);

        if (!$obErro->ocorreu()) {
            $obErro = $this->obRLDOLDO->updateTimestamp($boTransacao);
        }

        return $obErro;
    }

    /**
     * Método para alterar a validacao da acao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function excluir($boTransacao = '')
    {
        $this->obTLDOAcaoValidada->setDado('cod_acao'            , $this->obRPPAManterAcao->inCodAcao);
        $this->obTLDOAcaoValidada->setDado('ano'                 , $this->obRPPAManterAcao->inAno);
        $this->obTLDOAcaoValidada->setDado('timestamp_acao_dados', $this->obRPPAManterAcao->stTimestampAcaoDados);
        $this->obTLDOAcaoValidada->setDado('exercicio_recurso'   , $this->obRPPAManterAcao->stExercicioRecurso);
        $this->obTLDOAcaoValidada->setDado('cod_recurso'         , $this->obRPPAManterAcao->inCodRecurso);

        $obErro = $this->obTLDOAcaoValidada->exclusao($boTransacao);

        if (!$obErro->ocorreu()) {
            $obErro = $this->obRLDOLDO->updateTimestamp($boTransacao);
        }

        return $obErro;
    }

    /**
     * Método para buscar acoes que acao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listAcao(&$rsAcao, $stAcao)
    {
        $stOrder = ' ORDER BY acao.num_acao ';

        if ($this->obRPPAManterAcao->inCodAcao != '') {
            $stFiltro .= ' AND acao.num_acao >= ' . $this->obRPPAManterAcao->inCodAcao . ' ';
        }
        if ($this->obRPPAManterAcao->inCodAcaoFim != '') {
            $stFiltro .= ' AND acao.num_acao <= ' . $this->obRPPAManterAcao->inCodAcaoFim . ' ';
        }
        if ($this->obRPPAManterAcao->obRPPAManterPrograma->codPrograma != '') {
            $stFiltro .= ' AND programa.num_programa = ' . $this->obRPPAManterAcao->obRPPAManterPrograma->codPrograma . ' ';
        }
        if ($this->obRPPAManterAcao->obRPPAManterPrograma->obRPPAManterPPA->inCodPPA != '') {
            $this->obTLDOAcaoValidada->setDado('cod_ppa',$this->obRPPAManterAcao->obRPPAManterPrograma->obRPPAManterPPA->inCodPPA);
        }
        if ($this->obRPPAManterAcao->inAno != '') {
            $this->obTLDOAcaoValidada->setDado('ano',$this->obRPPAManterAcao->inAno);
        }

        if ($stAcao == 'incluir') {
            $obErro = $this->obTLDOAcaoValidada->listAcaoNaoValidada($rsAcao, $stFiltro, $stOrder);
        } else {
            $obErro = $this->obTLDOAcaoValidada->listAcaoValidada($rsAcao, $stFiltro, $stOrder);
        }

        return $obErro;
    }

    /**
     * Método para buscar acoes para despesa
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listAcaoDespesa(&$rsAcao, $stOrder = '')
    {
        $stFiltro = '';
        if ($this->obRPPAManterAcao->inCodAcao != '') {
            $stFiltro .= ' AND acao.num_acao >= ' . $this->obRPPAManterAcao->inCodAcao . ' ';
        }
        if ($this->obRPPAManterAcao->inCodAcaoFim != '') {
            $stFiltro .= ' AND acao.num_acao <= ' . $this->obRPPAManterAcao->inCodAcaoFim . ' ';
        }
        if ($this->obRPPAManterAcao->inCodRecurso != '') {
            $stFiltro .= ' AND acao_quantidade_disponivel.cod_recurso = '.$this->obRPPAManterAcao->inCodRecurso.' ';
        }
        if ($this->obRPPAManterAcao->stTitulo != '') {
            $stFiltro .= " AND acao_dados.titulo ILIKE '%".$this->obRPPAManterAcao->stTitulo."%' ";
        }
        if ($this->obRPPAManterAcao->obRPPAManterPrograma->codPrograma != '') {
            $stFiltro .= ' AND programa.num_programa = ' . $this->obRPPAManterAcao->obRPPAManterPrograma->codPrograma . ' ';
        }
        if ($this->obRPPAManterAcao->obRPPAManterPrograma->obRPPAManterPPA->inCodPPA != '') {
            $this->obTLDOAcaoValidada->setDado('cod_ppa',$this->obRPPAManterAcao->obRPPAManterPrograma->obRPPAManterPPA->inCodPPA);
        }
        if ($this->obRPPAManterAcao->inAno != '') {
            $this->obTLDOAcaoValidada->setDado('ano',$this->obRPPAManterAcao->inAno);
        }

        $obErro = $this->obTLDOAcaoValidada->listAcaoValidadaDespesa($rsAcao, $stFiltro, $stOrder);

        return $obErro;
    }

    /**
     * Método para buscar acoes
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function getAcao(&$rsAcao, $stAcao)
    {
        $stFiltro .= ' AND acao.cod_acao = '.$this->obRPPAManterAcao->inCodAcao.' ';
        $this->obTLDOAcaoValidada->setDado('cod_ppa', $this->obRPPAManterAcao->obRPPAManterPrograma->obRPPAManterPPA->inCodPPA);
        $this->obTLDOAcaoValidada->setDado('ano'    , $this->obRPPAManterAcao->inAno);

        if ($stAcao == 'incluir') {
            $obErro = $this->obTLDOAcaoValidada->listAcaoNaoValidada($rsAcao, $stFiltro);
        } else {
            $obErro = $this->obTLDOAcaoValidada->listAcaoValidada($rsAcao, $stFiltro);
        }

        return $obErro;
    }

    public function getListagemRecurso(&$rsAcaoQuantidade, array $arParam)
    {
        if ($arParam['stAcao'] == 'incluir') {
            $obMapeamento = new TPPAAcaoQuantidade;
        } else {
            $obMapeamento = new TLDOAcaoValidada;
        }

        $obMapeamento->setDado('cod_acao', $arParam['inCodAcao']);
        $obMapeamento->setDado('timestamp_acao_dados', $arParam['stTimestamp']);
        $obMapeamento->setDado('ano', $arParam['inAno']);
        $boErro = $obMapeamento->recuperaListagemRecursos($rsAcaoQuantidade);

        return $boErro;
    }
}

?>
