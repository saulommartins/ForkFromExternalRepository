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
 * Classe Negocio do 02.10.03 - Manter Ação
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santos>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcao.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOAcao.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOAcaoDados.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOAcaoRecurso.class.php';
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDOAcaoInativaNorma.class.php';
include_once CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoUnidade.class.php';
include_once CAM_GF_LDO_NEGOCIO    . 'RLDOManterReceita.class.php';
include_once CAM_GF_LDO_NEGOCIO    . 'RLDOManterLDO.class.php';
include_once CAM_GF_LDO_NEGOCIO    . 'RLDOPadrao.class.php';

class RLDOManterAcao extends RLDOPadrao implements IRLDOPadrao
{
    private $obTLDOAcao;
    private $obTPPAAcao;
    private $obTLDOAcaoDados;
    private $obTLDOAcaoRecurso;
    private $obTLDOAcaoInativaNorma;
    private $obTOrcamentoUnidade;

    public static function recuperarInstancia()
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    protected function inicializar()
    {
        $this->obTLDOAcao             = new TLDOAcao();
        $this->obTPPAAcao             = new TPPAAcao();
        $this->obTLDOAcaoDados        = new TLDOAcaoDados();
        $this->obTLDOAcaoRecurso      = new TLDOAcaoRecurso();
        $this->obTLDOAcaoInativaNorma = new TLDOAcaoInativaNorma();
        $this->obTOrcamentoUnidade    = new TOrcamentoUnidade();
    }

    public function incluir(array $arArgs)
    {
        $obTransacao     = new Transacao();
        $boFlagTransacao = false;
        $boTransacao     = null;

        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ($this->validarDuplicidadeAcao($arArgs)) {
            throw new RLDOExcecao('Ação já vinculada ao LDO');
        }

        if (!$this->validarListaRecurso($arArgs)) {
            throw new RLDOExcecao('Nenhum recurso vinculado a ação');
        }

        $this->obTLDOAcao->proximoCod($arArgs['inCodAcao'], $obTransacao);
        $this->obTLDOAcaoDados->proximoCod($arArgs['inCodAcaoDados'], $obTransacao);

        $rsOrcamentario = $this->recuperarOrcamentario($arArgs['stUnidadeOrcamentaria'], $arArgs['inExercicioUnidade'], $obTransacao);

        if ($rsOrcamentario->eof()) {
            throw new RLDOExcecao('Ocorreu um erro ao ler a unidade orçamentária');
        }

        $arArgs['inCodOrgao']   = $rsOrcamentario->getCampo('num_orgao');
        $arArgs['inCodUnidade'] = $rsOrcamentario->getCampo('num_unidade');
        $arArgs['boAtivo']      = 't';

        $obErro = $this->incluirAcao($arArgs, $obTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao incluir a ação');
        }

        $obErro = $this->incluirAcaoDados($arArgs, $obTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao incluir os dados da ação');
        }

        $obErro = $this->incluirAcaoRecurso($arArgs, $obTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao incluir a(s) classificação(ões) econômica(s) da ação');
        }

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTPPAAcao);

        return $arArgs['inCodAcao'];
    }

    private function incluirAcao(array $arArgs, $obTransacao = null)
    {
        $obErro = new Erro();

        $this->obTLDOAcao->setDado('cod_acao', $arArgs['inCodAcao']);
        $this->obTLDOAcao->setDado('cod_acao_ppa', $arArgs['inCodAcaoPPA']);
        $this->obTLDOAcao->setDado('ano', $arArgs['stAno']);
        $this->obTLDOAcao->setDado('ativo', $arArgs['boAtivo']);

        $obErro = $this->obTLDOAcao->inclusao($obTransacao);

        return $obErro;
    }

    private function incluirAcaoDados(array $arArgs, $obTransacao = null)
    {
        $obErro = new Erro();

        $this->obTLDOAcaoDados->setDado('cod_acao', $arArgs['inCodAcao']);
        $this->obTLDOAcaoDados->setDado('cod_acao_dados', $arArgs['inCodAcaoDados']);
        $this->obTLDOAcaoDados->setDado('num_orgao', $arArgs['inCodOrgao']);
        $this->obTLDOAcaoDados->setDado('num_unidade', $arArgs['inCodUnidade']);
        $this->obTLDOAcaoDados->setDado('exercicio', Sessao::read('exercicio'));
        $this->obTLDOAcaoDados->setDado('cod_entidade', $arArgs['inCodEntidade']);
        $this->obTLDOAcaoDados->setDado('cod_norma', 1);

        $obErro = $this->obTLDOAcaoDados->inclusao($obTransacao);

        return $obErro;
    }

    private function incluirAcaoRecurso(array $arArgs, $obTransacao = null)
    {
        $obErro = new Erro();

        for ($i = 0; $i < count($arArgs['arCodRecurso']); $i++) {
            $this->obTLDOAcaoRecurso->setDado('cod_acao', $arArgs['inCodAcao']);
            $this->obTLDOAcaoRecurso->setDado('cod_acao_dados', $arArgs['inCodAcaoDados']);
            $this->obTLDOAcaoRecurso->setDado('cod_conta', $arArgs['arCodConta'][$i]);
            $this->obTLDOAcaoRecurso->setDado('cod_recurso', $arArgs['arCodRecurso'][$i]);
            $this->obTLDOAcaoRecurso->setDado('exercicio', Sessao::read('exercicio'));
            $this->obTLDOAcaoRecurso->setDado('valor', $arArgs['arValorRecurso'][$i]);

            $obErro = $this->obTLDOAcaoRecurso->inclusao($obTransacao);

            if ($obErro->ocorreu()) {
                return $obErro;
            }
        }

        return $obErro;
    }

    private function incluirAcaoInativaNorma(array $arArgs, $obTransacao = null)
    {
        $obErro = new Erro();

        $this->obTLDOAcaoInativaNorma->setDado('cod_acao', $arArgs['inCodAcao']);
        $this->obTLDOAcaoInativaNorma->setDado('cod_norma', $arArgs['inCodNorma']);
        $this->obTLDOAcaoInativaNorma->setDado('timestamp', date('Y-m-d h:m:s'));

        $obErro = $this->obTLDOAcaoInativaNorma->inclusao($obTransacao);

        return $obErro;
    }

    public function alterar(array $arArgs)
    {
        $obTransacao     = new Transacao();
        $boFlagTransacao = false;
        $boTransacao     = null;

        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $this->obTLDOAcaoDados->proximoCod($arArgs['inCodAcaoDados'], $obTransacao);

        $arArgs['boAtivo'] = 't';

        $obErro = $this->alterarAcao($arArgs, $obTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao alterar a ação');
        }

        $rsOrcamentario = $this->recuperarOrcamentario($arArgs['stUnidadeOrcamentaria'], $obTransacao);

        if ($rsOrcamentario->eof()) {
            throw new RLDOExcecao('Ocorreu um erro ao ler a unidade orçamentária');
        }

        $arArgs['inCodOrgao']   = $rsOrcamentario->getCampo('num_orgao');
        $arArgs['inCodUnidade'] = $rsOrcamentario->getCampo('num_unidade');

        $obErro = $this->alterarAcaoDados($arArgs, $obTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao alterar os dados da ação');
        }

        $obErro = $this->alterarAcaoRecurso($arArgs, $obTransacao);

        if ($obErro->ocorreu()) {
            throw new RLDOExcecao('Ocorreu um erro ao alterar a(s) classificação(ões) econômica(s) da ação');
        }

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro);

        return $arArgs['inCodAcao'];
    }

    private function alterarAcao(array $arArgs, $obTransacao = null)
    {
        $obErro = new Erro();

        $this->obTLDOAcao->setDado('cod_acao', $arArgs['inCodAcao']);
        $this->obTLDOAcao->setDado('cod_acao_ppa', $arArgs['inCodAcaoPPA']);
        $this->obTLDOAcao->setDado('ano', $arArgs['stAno']);
        $this->obTLDOAcao->setDado('ativo', $arArgs['boAtivo']);

        $obErro = $this->obTLDOAcao->alteracao($obTransacao);

        return $obErro;
    }

    private function alterarAcaoDados(array $arArgs, $obTransacao = null)
    {
        $obErro = new Erro();

        $obErro = $this->incluirAcaoDados($arArgs, $obTransacao);

        return $obErro;
    }

    private function alterarAcaoRecurso(array $arArgs, $obTransacao = null)
    {
        $obErro = new Erro();

        $obErro = $this->incluirAcaoRecurso($arArgs, $obTransacao);

        return $obErro;
    }

    public function excluir(array $arArgs)
    {
        $obTransacao     = new Transacao();
        $boFlagTransacao = false;
        $boTransacao     = null;

        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $arArgs['boAtivo'] = 'f';

        if (RLDOManterLDO::recuperarInstancia()->recuperarLDOHomologado($arArgs['stAno'])) {
            if ($this->validarDuplicidadeNorma($arArgs)) {
                $obErro = $this->incluirAcaoInativaNorma($arArgs, $obTransacao);

                if ($obErro->ocorreu()) {
                    throw new RLDOExcecao('Ocorreu um erro ao incluir a norma de inativação');
                }
            } else {
                throw new RLDOExcecao('Norma já inclusa para está ação de inativação');
            }
        } else {
            $obErro = $this->excluirAcaoInativaNorma($arArgs, $obTransacao);

            if ($obErro->ocorreu()) {
                throw new RLDOExcecao('Ocorreu um erro ao excluir a norma de inativação da ação');
            }

            $obErro = $this->excluirAcaoRecurso($arArgs, $obTransacao);

            if ($obErro->ocorreu()) {
                throw new RLDOExcecao('Ocorreu um erro ao excluir os recursos da ação');
            }

            $obErro = $this->excluirAcaoDados($arArgs, $obTransacao);

            if ($obErro->ocorreu()) {
                throw new RLDOExcecao('Ocorreu um erro ao exluir os dados da ação');
            }

            $obErro = $this->excluirAcao($arArgs, $obTransacao);

            if ($obErro->ocorreu()) {
                throw new RLDOExcecao('Ocorreu um erro ao excluir a ação');
            }
        }

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTPPAAcao);

        return $arArgs['inCodAcao'];
    }

    private function excluirAcao(array $arArgs, $obTransacao = null)
    {
        $obErro = new Erro();

        $this->obTLDOAcao->setDado('cod_acao', $arArgs['inCodAcao']);

        $obErro = $this->obTLDOAcao->exclusao($obTransacao);

        return $obErro;
    }

    private function excluirAcaoDados(array $arArgs, $obTransacao = null)
    {
        $obErro = new Erro();

        $this->obTLDOAcaoDados->recuperaTodos($rsAcaoDados, ' WHERE cod_acao = '.$arArgs['inCodAcao']);

        while (!$rsAcaoDados->eof()) {
            $this->obTLDOAcaoDados->setDado('cod_acao', $rsAcaoDados->getCampo('cod_acao'));
            $this->obTLDOAcaoDados->setDado('cod_acao_dados', $rsAcaoDados->getCampo('cod_acao_dados'));

            $obErro = $this->obTLDOAcaoDados->exclusao($obTransacao);

            if ($obErro->ocorreu()) {
                return $obErro;
            }

            $rsAcaoDados->proximo();
        }

        return $obErro;
    }

    private function excluirAcaoRecurso(array $arArgs, $obTransacao = null)
    {
        $obErro = new Erro();

        $this->obTLDOAcaoRecurso->recuperaTodos($rsAcaoRecurso, ' WHERE cod_acao = '.$arArgs['inCodAcao']);

        while (!$rsAcaoRecurso->eof()) {
            $this->obTLDOAcaoRecurso->setDado('cod_acao', $rsAcaoRecurso->getCampo('cod_acao'));
            $this->obTLDOAcaoRecurso->setDado('cod_acao_dados', $rsAcaoRecurso->getCampo('cod_acao_dados'));
            $this->obTLDOAcaoRecurso->setDado('cod_recurso', $rsAcaoRecurso->getCampo('cod_recurso'));

            $obErro = $this->obTLDOAcaoRecurso->exclusao($obTransacao);

            if ($obErro->ocorreu()) {
                return $obErro;
            }

            $rsAcaoRecurso->proximo();
        }

        return $obErro;
    }

    private function excluirAcaoInativaNorma(array $arArgs, $obTransacao = null)
    {
        $obErro = new Erro();

        $this->obTLDOAcaoInativaNorma->recuperaTodos($rsAcaoInativaNorma, ' WHERE cod_acao = '.$arArgs['inCodAcao']);

        while (!$rsAcaoInativaNorma->eof()) {
            $this->obTLDOAcaoInativaNorma->setDado('cod_acao', $rsAcaoRecurso->getCampo('cod_acao'));
            $this->obTLDOAcaoInativaNorma->setDado('cod_recurso', $rsAcaoRecurso->getCampo('cod_norma'));

            $obErro = $this->obTLDOAcaoInativaNorma->exclusao($obTransacao);

            if ($obErro->ocorreu()) {
                return $obErro;
            }

            $rsAcaoInativaNorma->proximo();
        }

        return $obErro;
    }

    public function consultar(array $arArgs)
    {
        $stFiltro = $this->recuperarFiltro($arArgs);

        $this->obTLDOAcao->recuperaAcaoDados($rsAcao, $stFiltro);

        return $rsAcao;
    }

    public function listar($inCodAcaoInicio, $inCodAcaoFim)
    {
        $stFiltro = " acao.cod_acao >= " . (int) $inCodAcaoInicio;

        if ($inCodAcaoFim) {
            $stFiltro.= " AND acao.cod_acao <= " . (int) $inCodAcaoFim;
        }

        $stFiltro.= " AND acao.ativo = 't'";

        $this->obTLDOAcao->recuperaAcaoDados($rsAcao, $stFiltro);

        return $rsAcao;
    }

    public function recuperarRecurso(array $arArgs)
    {
        unset($arArgs['inCodNorma']);
        unset($arArgs['inCodAcaoPPA']);

        $stFiltro = $this->recuperarFiltro($arArgs);

        $this->obTLDOAcaoRecurso->recuperaRecurso($rsRecurso, $stFiltro);

        return $rsRecurso;
    }

    public function recuperarRecursoOrcamento(array $arArgs)
    {
        unset($arArgs['inCodNorma']);
        unset($arArgs['inCodAcaoPPA']);

        $arArgs['setExercicio'] = $arArgs['stExercicio'] ? $arArgs['stExercicio'] : Sessao::read('exercicio');

        $stFiltro = $this->recuperarFiltro($arArgs);

        $this->obTLDOAcaoRecurso->recuperarRecursoOrcamento($rsRecurso, $stFiltro);

        return $rsRecurso;
    }

    private function recuperarOrcamentario($arUnidadeOrcamentaria, $obTransacao = null)
    {
        $arUnidadeOrcamentaria = explode('.', $arUnidadeOrcamentaria);

        $obTOrcamentoUnidade = new TOrcamentoUnidade();
        $rsOrcamentario      = new RecordSet();

        $stFiltro  = ' AND unidade.num_orgao = ' . $arUnidadeOrcamentaria[0] . ' AND unidade.num_unidade = ' . $arUnidadeOrcamentaria[1];
        $stFiltro .= " AND unidade.exercicio = '" . Sessao::getExercicio() . "'";
        $stOrdem   = '';

        $obErro = $this->obTOrcamentoUnidade->recuperaRelacionamento($rsOrcamentario, $stFiltro, $stOrdem, $obTransacao);

        return $rsOrcamentario;
    }

    public function recuperarAcaoPPA(array $arArgs)
    {
        if ($arArgs['inNumAcao'])
            $stFiltro = " acao.num_acao = " .$arArgs['inNumAcao']. "\n";
        else if ($arArgs['inCodAcaoPPA'])
            $stFiltro = " acao.cod_acao = " .$arArgs['inCodAcaoPPA']. "\n";

        $stFiltro.= " AND ppa.cod_ppa = " .$arArgs['inCodPPA']. "\n";

        $this->obTPPAAcao->recuperaListaAcoesProgramas($rsAcao, $stFiltro);

        return $rsAcao;
    }

    public function recuperarTotalAcaoLDO(array $arArgs)
    {
        unset($arArgs['inCodAcao']);
        unset($arArgs['stExercicio']);
        unset($arArgs['inCodAcaoPPA']);
        unset($arArgs['inCodAcaoDados']);
        unset($arArgs['inCodAcaoDadosRecurso']);

        $arArgs['stAnoLDO'] = $arArgs['stAno'];

        $stFiltro = $this->recuperarFiltro($arArgs);

        $this->obTLDOAcao->recuperaTotalAcao($rsRecurso, $stFiltro, $stOrdem);

        return $rsRecurso;
    }

    public function validarDuplicidadeAcao(array $arArgs)
    {
        unset($arArgs['inCodAcao']);
        unset($arArgs['inCodNorma']);
        unset($arArgs['stExercicio']);
        unset($arArgs['inCodAcaoDados']);
        unset($arArgs['inCodAcaoDadosRecurso']);

        $arArgs['inCodAcaoPPA'] = $arArgs['inNumAcao'];

        $rsLDO = RLDOManterLDO::recuperarInstancia()->recuperarLDO();

        $arArgs['stAnoLDO'] = $rsLDO->getCampo('ano');

        $rsAcao = $this->consultar($arArgs);

        return (int) !$rsAcao->eof();
    }

    public function validarDuplicidadeNorma(array $arArgs)
    {
        unset($arArgs['stExercicio']);
        unset($arArgs['inCodAcaoPPA']);
        unset($arArgs['inCodAcaoDados']);
        unset($arArgs['inCodAcaoDadosRecurso']);

        $stFiltro = $this->recuperarFiltro($arArgs);

        $this->obTLDOAcaoInativaNorma->recuperaNormaAcao($rsNorma, $stFiltro);

        return (int) $rsNorma->eof();
    }

    public function validarListaRecurso($arArgs)
    {
        return (bool) count($arArgs['arCodRecurso']);
    }

    public function recuperarFiltro(array $arArgs = null)
    {
        $return = null;

        if ($arArgs['inCodAcao'] != "") {
            $stFiltro[] .= " acao.cod_acao = " .$arArgs['inCodAcao']. "\n";
        }
        if ($arArgs['inCodAcaoPPA'] != "") {
            $stFiltro[] .= " ppa_acao.cod_acao = " .$arArgs['inCodAcaoPPA']. "\n";
        }
        if ($arArgs['inCodAcaoDados'] != "") {
            $stFiltro[] .= " acao_dados.cod_acao_dados = " .$arArgs['inCodAcaoDados']. "\n";
        }
        if ($arArgs['inCodAcaoDadosRecurso'] != "") {
            $stFiltro[] .= " acao_recurso.cod_acao_dados = " .$arArgs['inCodAcaoDados']. "\n";
        }
        if ($arArgs['inCodRecurso'] != "") {
            $stFiltro[] .= " cod_recurso = " .$arArgs['inCodRecurso']. "\n";
        }
        if ($arArgs['inCodNorma'] != "") {
            $stFiltro[] .= " acao_inativa_norma.cod_norma = " .$arArgs['inCodNorma']. "\n";
        }
        if ($arArgs['stAnoLDO'] != "") {
            $stFiltro[] .= " acao.ano = " .$arArgs['stAnoLDO']. "\n";
        }
        if ($arArgs['stExercicio'] != "") {
            $stFiltro[] .= " exercicio = " .$arArgs['stExercicio']. "\n";
        }

        if ($stFiltro) {
            foreach ($stFiltro as $chave => $valor) {
                if ($chave == 0) {
                    $return .= $valor;
                } else {
                    $return .= " AND ".$valor;
                }
            }

            return $return;
        }
    }

}
