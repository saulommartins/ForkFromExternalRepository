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
    * Classe de regra de negócio para Inclusão do levantamento Fiscal
    * Data de Criação: 14/08/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Jânio Eduardo Vascocellos de Magalhães

    * @package URBEM
    * @subpackage Regra

    * Casos de uso:

    $Id:$
*/

require_once CAM_GT_FIS_MAPEAMENTO . 'TFISFaturamentoServico.class.php';
require_once CAM_GT_FIS_MAPEAMENTO . 'TFISServicoComRetencao.class.php';
require_once CAM_GT_FIS_MAPEAMENTO . 'TFISServicoSemRetencao.class.php';
require_once CAM_GT_FIS_MAPEAMENTO . 'TFISProcessoLevantamento.class.php';
include_once CAM_GT_CEM_MAPEAMENTO . 'TCEMServico.class.php';

class RFISManterServico
{
    # Arquivos de mapeamento.
    private $obTFISFaturamentoServico;
    private $obTFISServicoComRetencao;
    private $obTFISServicoSemReTencao;
    private $obTFISProcessoLevantamento;
    private $ocorrencia;
    private $competencia;

    public function __construct()
    {
        $this->obTFISProcessoLevantamento = new TFISProcessoLevantamento;
        $this->obTFISFaturamentoServico = new TFISFaturamentoServico;
        $this->obTFISServicoComRetencao = new TFISServicoComRetencao;
        $this->obTFISServicoSemRetencao = new TFISServicoSemRetencao;
    }

    public function incluir($param)
    {
        $obServicosRetencao = Sessao::read('servicos_retencao');

        if (empty($obServicosRetencao)) {
            return sistemaLegado::exibeAviso('Lista de serviço!', 'n_incluir', 'erro');
        }

        $obTCEMServico = new TCEMServico;
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = '';

        $this->competencia = $param['stCompetencia'].'/'.$param['stExercicio'];
        $stFiltro = " where competencia ='".$this->competencia."' AND cod_processo = '".$param["inCodProcesso"]."'";
        $this->obTFISProcessoLevantamento->recuperaTodos($rsCompetencia,$stFiltro);

        if ($rsCompetencia->eof()) {
            $this->obTFISProcessoLevantamento->setDado('cod_processo',$param["inCodProcesso"]);
            $this->obTFISProcessoLevantamento->setDado('competencia',$this->competencia);
            $this->obTFISProcessoLevantamento->inclusao();
        }

        if (!$_REQUEST['boReter']) {
            # Sem retenção.
            $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

            foreach ($obServicosRetencao as $servico) {
                $stFiltro = " WHERE cod_estrutural = '".$servico['stServico']."'";

                $obTCEMServico->recuperaTodos($rsListaServico, $stFiltro, '', $boTransacao);

                $rsTFISFaturamentoServico = new recordSet();
                $stFiltro = null;
                $this->obTFISFaturamentoServico->proximoCodigo($rsTFISFaturamentoServico, $stFiltro, '', $boTransacao);
                $this->ocorrencia = $rsTFISFaturamentoServico->getCampo('max') + 1;

                $this->obTFISFaturamentoServico->setDado('cod_processo',$param['inCodProcesso']);
                $this->obTFISFaturamentoServico->setDado('competencia',$this->competencia);
                $this->obTFISFaturamentoServico->setDado('cod_servico',$rsListaServico->getCampo('cod_servico'));
                $this->obTFISFaturamentoServico->setDado('cod_atividade',$param['inCodAtividade']);
                $this->obTFISFaturamentoServico->setDado('ocorrencia',$this->ocorrencia);
                $this->obTFISFaturamentoServico->setDado('cod_modalidade',$param['inModalidade']);
                $this->obTFISFaturamentoServico->setDado('dt_emissao',$param['dtEmissao']);
                $obErro = $this->obTFISFaturamentoServico->inclusao($boTransacao);

                if ($obErro->ocorreu()) {
                    return sistemaLegado::exibeAviso( "Vefifique se a modalidade desta atividade foi definida!","n_incluir","erro" );
                }

                $this->obTFISServicoSemRetencao->setDado('cod_processo',$param['inCodProcesso']);
                $this->obTFISServicoSemRetencao->setDado('competencia',$this->competencia);
                $this->obTFISServicoSemRetencao->setDado('cod_servico',$rsListaServico->getCampo('cod_servico'));
                $this->obTFISServicoSemRetencao->setDado('cod_atividade',$param['inCodAtividade']);
                $this->obTFISServicoSemRetencao->setDado('ocorrencia',$this->ocorrencia);
                $this->obTFISServicoSemRetencao->setDado('valor_declarado',$servico['flValorDeclarado']);
                $this->obTFISServicoSemRetencao->setDado('valor_deducao',$servico['flDeducaoIncondicional']);
                $this->obTFISServicoSemRetencao->setDado('valor_deducao_legal',$servico['flDeducao']);
                $this->obTFISServicoSemRetencao->setDado('valor_lancado',$servico['flValorLancado']);
                $this->obTFISServicoSemRetencao->setDado('aliquota',$servico['flAliquota']);
                $obErro = $this->obTFISServicoSemRetencao->inclusao($boTransacao);

                if ($obErro->ocorreu()) {
                    return sistemaLegado::exibeAviso('Erro ao incluir serviço sem retenção', 'n_incluir', 'erro');
                }
            }

            $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTFISServicoSemRetencao);
            $result = sistemaLegado::alertaAviso('FLManterLevantamento.php' , $param['inCodProcesso'] ,"incluir","aviso", Sessao::getId(), "../");
        } else {
            # Com retenção.
            $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

            foreach ($obServicosRetencao as $servico) {
                $stFiltro = " WHERE cod_estrutural = '".$servico['stServico']."'";
                $obTCEMServico->recuperaTodos($rsListaServico, $stFiltro, '', $boTransacao);
                $rsTFISFaturamentoServico = new recordSet();
                $stFiltro = null;
                $this->obTFISFaturamentoServico->proximoCodigo($rsTFISFaturamentoServico,$stFiltro, '', $boTransacao);
                $this->ocorrencia = $rsTFISFaturamentoServico->getCampo('max') + 1;
                $stFiltro = " WHERE faturamento_servico.cod_processo = '". $param["inCodProcesso"].
                        "' AND faturamento_servico.cod_servico = '".$rsListaServico->getCampo('cod_servico').
                        "' AND faturamento_servico.cod_atividade = '".$param['inCodAtividade']."'";

                $this->obTFISFaturamentoServico->setDado('cod_processo',$param['inCodProcesso']);
                $this->obTFISFaturamentoServico->setDado('competencia',$this->competencia);
                $this->obTFISFaturamentoServico->setDado('cod_servico',$rsListaServico->getCampo('cod_servico'));
                $this->obTFISFaturamentoServico->setDado('cod_atividade',$param['inCodAtividade']);
                $this->obTFISFaturamentoServico->setDado('ocorrencia',$this->ocorrencia);
                $this->obTFISFaturamentoServico->setDado('cod_modalidade',$param['inModalidade']);
                $this->obTFISFaturamentoServico->setDado('dt_emissao',$param['dtEmissao']);
                $obErro = $this->obTFISFaturamentoServico->inclusao($boTransacao);

                if ($obErro->ocorreu()) {
                    return sistemaLegado::exibeAviso('Vefifique se a modalidade desta atividade foi definida!', 'n_incluir', 'erro');
                }

                $this->obTFISServicoComRetencao->setDado('cod_processo',$param['inCodProcesso']);
                $this->obTFISServicoComRetencao->setDado('competencia',$this->competencia);
                $this->obTFISServicoComRetencao->setDado('cod_servico',$rsListaServico->getCampo('cod_servico'));
                $this->obTFISServicoComRetencao->setDado('cod_atividade',$param['inCodAtividade']);
                $this->obTFISServicoComRetencao->setDado('ocorrencia',$this->ocorrencia);
                $this->obTFISServicoComRetencao->setDado('cod_municipio',$servico['stMunicipio']);
                $this->obTFISServicoComRetencao->setDado('cod_uf',$servico['stEstado']);
                $this->obTFISServicoComRetencao->setDado('numcgm',$servico['inCGM']);
                $this->obTFISServicoComRetencao->setDado('valor_retido',$servico['flValorRetido']);
                $obErro = $this->obTFISServicoComRetencao->inclusao($boTransacao);

                if ($obErro->ocorreu()) {
                    return sistemaLegado::exibeAviso('Erro ao incluir serviço com retenção', 'n_incluir', 'erro');
                }
            }

            $result = sistemaLegado::alertaAviso('FLManterLevantamento.php', $param['inCodProcesso'], 'incluir', 'aviso', Sessao::getId(), '../');
            $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTFISServicoComRetencao);
        }

        return $result;
    }
}
