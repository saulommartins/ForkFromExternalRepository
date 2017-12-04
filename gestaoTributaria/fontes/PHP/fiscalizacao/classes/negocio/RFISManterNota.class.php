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
    * Classe de regra de negócio para levantamento fiscal com notas
    * Data de Criação: 28/07/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Jânio Eduardo

    * @package URBEM
    * @subpackage Regra

    * Casos de uso:

    $Id:$
*/

require_once( CAM_GT_FIS_MAPEAMENTO.'TFISFaturamentoServico.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISServicoComRetencao.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISServicoSemRetencao.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISProcessoLevantamento.class.php' );
include_once( CAM_GT_CEM_MAPEAMENTO."TCEMServico.class.php" );
include_once( CAM_GT_FIS_MAPEAMENTO."TFISNotaServico.class.php" );
include_once( CAM_GT_FIS_MAPEAMENTO."TFISNota.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterNota";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

class RFISManterNota
{
private $obTFISFaturamentoServico;
private $obTFISServicoComRetencao;
private $obTFISServicoSemRetencao;
private $obTFISServicoNota;
private $obTFISNota;
private $obTFISProcessoLevantamento;

private $ocorrencia;
private $competencia;

    public function __construct()
    {
        $this->obTFISFaturamentoServico = new TFISFaturamentoServico;
        $this->obTFISServicoComRetencao = new TFISServicoComRetencao;
        $this->obTFISServicoSemRetencao = new TFISServicoSemRetencao;
        $this->obTFISProcessoLevantamento = new TFISProcessoLevantamento;
        $this->obTFISNotaServico = new TFISNotaServico;
        $this->obTFISNota = new TFISNota;

    }

    public function incluir($param)
    {
        //$obServicosRetencao = Sessao::read("servicos_retencao");
        $notas_retencao = Sessao::read("notas_retencao_semrt");

        $obTCEMServico = new TCEMServico;

        $obTransacao = new Transacao();
            $boFlagTransacao = false;
               $boTransacao = "";

        $this->competencia = $param['stCompetencia'].'/'.$param['stExercicio'];
        $stFiltro = " where competencia ='".$this->competencia."' AND cod_processo = '".$param["inCodProcesso"]."'";
        $rsCompetencia = new RecordSet();
        $this->obTFISProcessoLevantamento->recuperaTodos($rsCompetencia,$stFiltro);

        if (!$_REQUEST['boReter']) {//SEM RETENÇÃO
                    if (empty($notas_retencao)) {
                        return sistemaLegado::exibeAviso( "Lista de nota vazia!","n_incluir","erro" );
                    }
                    if ($rsCompetencia->Eof()) {
            $this->obTFISProcessoLevantamento->setDado('cod_processo',$param["inCodProcesso"]);
            $this->obTFISProcessoLevantamento->setDado('competencia',$this->competencia);
            $this->obTFISProcessoLevantamento->inclusao();

                    }
            foreach ($notas_retencao as $nota) {
                            $arServicos = $nota['arServicos'];
                            foreach ($arServicos as $servico) {

                $stFiltro = " WHERE cod_estrutural = '".$servico['stServico']."'";

                $obTCEMServico->recuperaTodos( $rsListaServico, $stFiltro );

                $rsTFISFaturamentoServico = new recordSet();
                $stFiltro = null;
                $this->obTFISFaturamentoServico->proximoCodigo($rsTFISFaturamentoServico,$stFiltro);
                $this->ocorrencia = $rsTFISFaturamentoServico->getCampo('max')+1;

                $stFiltro = " WHERE faturamento_servico.cod_processo = '". $param["inCodProcesso"]."
                        ' AND faturamento_servico.cod_servico = '".$rsListaServico->getCampo('cod_servico')."
                        ' AND faturamento_servico.cod_atividade = '".$param['inCodAtividade']."'";

                if (!$rsTFISFaturamentoServico->Eof()) {
                    $stFiltro .= " AND faturamento_servico.ocorrencia = '".$this->ocorrencia."'";
                }

                $this->obTFISFaturamentoServico->recuperaTodos($rsTFISFaturamentoServico,$stFiltro);
                $this->obTFISNota->ProximoCod($inNumNota);

                if ($rsTFISFaturamentoServico->Eof()) {

                    $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

                    $this->obTFISFaturamentoServico->setDado('cod_processo',$param['inCodProcesso']);
                    $this->obTFISFaturamentoServico->setDado('competencia',$this->competencia);
                    $this->obTFISFaturamentoServico->setDado('cod_servico',$rsListaServico->getCampo('cod_servico'));
                    $this->obTFISFaturamentoServico->setDado('cod_atividade',$param['inCodAtividade']);
                    $this->obTFISFaturamentoServico->setDado('ocorrencia',$this->ocorrencia);
                    $this->obTFISFaturamentoServico->setDado('cod_modalidade',$param['inModalidade']);
                    $this->obTFISFaturamentoServico->setDado('dt_emissao',$nota['dtEmissao']);
                    $obErro = $this->obTFISFaturamentoServico->inclusao($boTransacao);

                    if ($obErro->ocorreu()) {
                        return sistemaLegado::exibeAviso( "Vefifique se a modalidade desta atividade foi definida!","n_incluir","erro" );
                    } else {

                    $this->obTFISServicoSemRetencao->setDado('cod_processo',$param['inCodProcesso']);
                    $this->obTFISServicoSemRetencao->setDado('competencia',$this->competencia);
                    $this->obTFISServicoSemRetencao->setDado('cod_servico',$rsListaServico->getCampo('cod_servico'));
                    $this->obTFISServicoSemRetencao->setDado('cod_atividade',$param['inCodAtividade']);
                    $this->obTFISServicoSemRetencao->setDado('ocorrencia',$this->ocorrencia);
                    $this->obTFISServicoSemRetencao->setDado('valor_declarado',$servico['flValorDeclarado']);
                    $this->obTFISServicoSemRetencao->setDado('valor_deducao',$servico['flDeducao']);
                    $this->obTFISServicoSemRetencao->setDado('valor_lancado',$servico['flValorLancado']);
                    $this->obTFISServicoSemRetencao->setDado('aliquota',$servico['flAliquota']);
                    $this->obTFISServicoSemRetencao->setDado('valor_deducao_legal',0);
                    $obErro  = $this->obTFISServicoSemRetencao->inclusao($boTransacao);

                    }
                    if ($obErro->ocorreu()) {
                        return sistemaLegado::exibeAviso( "Erro ao incluir serviço sem retencao","n_incluir","erro" );
                    } else {

                    $this->obTFISNota->setDado('cod_nota',$inNumNota);
                    $this->obTFISNota->setDado('nro_serie',"'".$nota['inSerie']."'");
                    $this->obTFISNota->setDado('nro_nota',$nota['inNumeroNota']);
                    //if (!$nota['flValorMercadoria']) $nota['flValorMercadoria'] = 0;
                    //$this->obTFISNota->setDado('valor_mercadoria',$nota['flValorMercadoria']);
                    $this->obTFISNota->setDado('valor_nota',$nota['flValorLancado']);
                    $obErro = $this->obTFISNota->inclusao($boTransacao);
                    }
                    if ($obErro->ocorreu()) {
                        return sistemaLegado::exibeAviso( "Erro ao incluir nota","n_incluir","erro" );
                    } else {

                    $this->obTFISNotaServico->setDado('cod_nota',$inNumNota);
                    $this->obTFISNotaServico->setDado('cod_processo',$param['inCodProcesso']);
                    $this->obTFISNotaServico->setDado('competencia',$this->competencia);
                    $this->obTFISNotaServico->setDado('cod_servico',$rsListaServico->getCampo('cod_servico'));
                    $this->obTFISNotaServico->setDado('cod_atividade',$param['inCodAtividade']);
                    $this->obTFISNotaServico->setDado('ocorrencia',$this->ocorrencia);
                    $obErro = $this->obTFISNotaServico->inclusao($boTransacao);

                    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFISNota);
                    }
                    if ($obErro->ocorreu()) {
                        return sistemaLegado::exibeAviso( "Erro ao incluir serviço nota","n_incluir","erro" );
                    }
                $result = sistemaLegado::alertaAviso('FLManterLevantamento.php' , $param['inCodProcesso'] ,"incluir","aviso", Sessao::getId(), "../");

            } else $result =  sistemaLegado::exibeAviso( "Lançamento já efetuado","n_incluir","erro" );
            }//fim forach notas
        }//fim foreach serviço

        } else {//COM RETENÇÃO
        $notas_retencao = Sessao::read("notas_retencao_comrt");
                if (empty($notas_retencao)) {
                        return sistemaLegado::exibeAviso( "Lista de nota vazia!","n_incluir","erro" );
                }
                if ($rsCompetencia->Eof()) {
            $this->obTFISProcessoLevantamento->setDado('cod_processo',$param["inCodProcesso"]);
            $this->obTFISProcessoLevantamento->setDado('competencia',$this->competencia);
            $this->obTFISProcessoLevantamento->inclusao();

        }
        foreach ($notas_retencao as $nota) {
                    $arServicos = $nota['arServicos'];
                    foreach ($arServicos as $servico) {

                $stFiltro = " WHERE cod_estrutural = '".$servico['stServico']."'";

                $obTCEMServico->recuperaTodos( $rsListaServico, $stFiltro );

                $rsTFISFaturamentoServico = new recordSet();
                $stFiltro = null;
                $this->obTFISFaturamentoServico->proximoCodigo($rsTFISFaturamentoServico,$stFiltro);
                $this->ocorrencia = $rsTFISFaturamentoServico->getCampo('max')+1;

                $stFiltro = " WHERE faturamento_servico.cod_processo = '". $param["inCodProcesso"]."
                        ' AND faturamento_servico.cod_servico = '".$rsListaServico->getCampo('cod_servico')."
                        ' AND faturamento_servico.cod_atividade = '".$param['inCodAtividade']."'";

                if (!$rsTFISFaturamentoServico->Eof()) {
                    $stFiltro .= " AND faturamento_servico.ocorrencia = '".$this->ocorrencia."'";}
                $this->obTFISFaturamentoServico->recuperaTodos($rsTFISFaturamentoServico,$stFiltro);

                if ($rsTFISFaturamentoServico->Eof()) {

                    $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

                    $this->obTFISFaturamentoServico->setDado('cod_processo',$param['inCodProcesso']);
                    $this->obTFISFaturamentoServico->setDado('competencia',$this->competencia);
                    $this->obTFISFaturamentoServico->setDado('cod_servico',$rsListaServico->getCampo('cod_servico'));
                    $this->obTFISFaturamentoServico->setDado('cod_atividade',$param['inCodAtividade']);
                    $this->obTFISFaturamentoServico->setDado('ocorrencia',$this->ocorrencia);
                    $this->obTFISFaturamentoServico->setDado('cod_modalidade',$param['inModalidade']);
                    $this->obTFISFaturamentoServico->setDado('dt_emissao',$nota['dtEmissao']);
                    $obErro = $this->obTFISFaturamentoServico->inclusao($boTransacao);

                    if ($obErro->ocorreu()) {
                        return sistemaLegado::exibeAviso( "Vefifique se a modalidade desta atividade foi definida!","n_incluir","erro" );
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
                        return sistemaLegado::exibeAviso( "Erro ao incluir servico com retencao","n_incluir","erro" );
                    }

                    $this->obTFISNota->ProximoCod($inNumNota,$boTransacao);

                    $this->obTFISNota->setDado('cod_nota',$inNumNota);
                    $this->obTFISNota->setDado('nro_serie',"'".$nota['inSerie']."'");
                    $this->obTFISNota->setDado('nro_nota',$nota['inNumeroNota']);
                    //if (!$nota['flValorMercadoria']) $nota['flValorMercadoria'] = 0;
                    //$this->obTFISNota->setDado('valor_mercadoria',$nota['flValorMercadoria']);
                    $this->obTFISNota->setDado('valor_nota',$nota['flValorLancado']);
                    $obErro = $this->obTFISNota->inclusao($boTransacao);

                    if ($obErro->ocorreu()) {
                        return sistemaLegado::exibeAviso( "Erro ao incluir nota","n_incluir","erro" );
                    }

                    $this->obTFISNotaServico->setDado('cod_nota',$inNumNota);
                    $this->obTFISNotaServico->setDado('cod_processo',$param['inCodProcesso']);
                    $this->obTFISNotaServico->setDado('competencia',$this->competencia);
                    $this->obTFISNotaServico->setDado('cod_servico',$rsListaServico->getCampo('cod_servico'));
                    $this->obTFISNotaServico->setDado('cod_atividade',$param['inCodAtividade']);
                    $this->obTFISNotaServico->setDado('ocorrencia',$this->ocorrencia);
                    $obErro = $this->obTFISNotaServico->inclusao($boTransacao);

                    if ($obErro->ocorreu()) {
                        return sistemaLegado::exibeAviso( "Erro ao incluir serviço nota","n_incluir","erro" );
                    }

                $result = sistemaLegado::alertaAviso('FLManterLevantamento.php' , $param['inCodProcesso'] ,"incluir","aviso", Sessao::getId(), "../");

            } else $result =  sistemaLegado::exibeAviso( "Lançamento já efetuado","n_incluir","erro" );
            }//fim forach notas
        }//fim foreach serviço

          }//fim else

    }

}
