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
    * Classe de regra de negócio para levantamento fiscal com rentenção na fonte
    * Data de Criação: 17/08/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Jânio Eduardo Vasconcellos de Magalhães

    * @package URBEM
    * @subpackage Regra

    * Casos de uso:

    $Id:$
*/

include_once( CAM_GT_CEM_MAPEAMENTO."TCEMServico.class.php" );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISRetencaoServico.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISRetencaoFonte.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISFaturamentoServico.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISRetencaoNota.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISProcessoLevantamento.class.php' );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterNota";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

class RFISManterRetido
{
private $obTFISFaturamentoServico;
private $obTFISRetencaoServico;
private $obTFISRetencaoFonte;
private $TFISRetencaoNota;
private $obTFISProcessoLevantamento;

private $ocorrencia = 0;
private $totalRetido;
private $competencia;

    public function __construct()
    {
        $this->obTFISFaturamentoServico = new TFISFaturamentoServico;
        $this->obTFISRetencaoServico = new TFISRetencaoServico;
        $this->obTFISRetencaoFonte = new TFISRetencaoFonte;
        $this->obTFISRetencaoNota = new TFISRetencaoNota;
        $this->obTFISProcessoLevantamento = new TFISProcessoLevantamento;

    }

    public function incluir($param)
    {
        $arNotasRetencao = Sessao::read( "notas_retencao" );
        if (empty($arNotasRetencao)) {
                        return sistemaLegado::exibeAviso( "Lista de nota vazia!","n_incluir","erro" );
                    }

        $obTransacao = new Transacao();
            $boFlagTransacao = false;
               $boTransacao = "";
        $this->competencia = $param['stCompetencia'].'/'.$param['stExercicio'];
        $stFiltro = " where competencia ='".$this->competencia."' AND cod_processo = '".$param["inCodProcesso"]."'";
        $rsCompetencia = new RecordSet();
        $this->obTFISProcessoLevantamento->recuperaTodos($rsCompetencia,$stFiltro);

        if ($rsCompetencia->Eof()) {
            $this->obTFISProcessoLevantamento->setDado('cod_processo',$param["inCodProcesso"]);
            $this->obTFISProcessoLevantamento->setDado('competencia',$this->competencia);
            $this->obTFISProcessoLevantamento->inclusao();

        }

        $stFiltro = " WHERE retencao_fonte.cod_processo = '". $param["inCodProcesso"]."' AND competencia = '".$this->competencia."'";

        $rsProcesso = new RecordSet;

        $this->obTFISRetencaoFonte->recuperaTodos($rsProcesso,$stFiltro);

        foreach ($arNotasRetencao as $valorRetido) {

             $this->totalRetido = $this->totalRetido + $valorRetido['flValorRetido'];
          }

        if ($rsProcesso->Eof()) {

           $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

           $this->obTFISRetencaoFonte->setDado('cod_processo',$param['inCodProcesso']);
           $this->obTFISRetencaoFonte->setDado('competencia',$this->competencia);
           $this->obTFISRetencaoFonte->setDado('valor_retencao',$this->totalRetido);
           $obErro = $this->obTFISRetencaoFonte->inclusao($boTransacao);
        } else {

           $this->obTFISRetencaoFonte->setDado('cod_processo',$param['inCodProcesso']);
           $this->obTFISRetencaoFonte->setDado('competencia',$this->competencia);
           $this->obTFISRetencaoFonte->setDado('valor_retencao',$this->totalRetido + $rsProcesso->getCampo('valor_retencao'));
           $obErro = $this->obTFISRetencaoFonte->alteracao($boTransacao);

        }

           if ($obErro->ocorreu()) {
            return sistemaLegado::exibeAviso( "Erro ao incluir retencao fonte","n_incluir","erro" );
                    } else {

           foreach ($arNotasRetencao as $nota) {

                $this->obTFISRetencaoNota->proximoCod($numNota,$boTransacao);
                $this->obTFISRetencaoNota->setDado('cod_processo',$param['inCodProcesso']);
                $this->obTFISRetencaoNota->setDado('competencia',$this->competencia);
                $this->obTFISRetencaoNota->setDado('cod_nota',$numNota);
                $this->obTFISRetencaoNota->setDado('numcgm',$nota['inCGM']);
                $this->obTFISRetencaoNota->setDado('cod_municipio',$nota['stMunicipio']);
                $this->obTFISRetencaoNota->setDado('cod_uf',$nota['stEstado']);
                $this->obTFISRetencaoNota->setDado('num_serie',"'".$nota['inSerie']."'");
                $this->obTFISRetencaoNota->setDado('num_nota',$nota['inNumeroNota']);
                $this->obTFISRetencaoNota->setDado('dt_emissao',$nota['dtEmissao']);
                $this->obTFISRetencaoNota->setDado('valor_nota',$nota['flValorDeclarado']);
                $obErro = $this->obTFISRetencaoNota->inclusao($boTransacao);
                }
                if ($obErro->ocorreu()) {
                        return sistemaLegado::exibeAviso( "Erro ao incluir retencao nota","n_incluir","erro" );
                } else {

                $arServico = $nota['arServicos'];

                foreach ($arServico as $servico) {

                    $stFiltroServico = " WHERE cod_estrutural = '".$servico['stServico']."';";
                    $obTCEMServico = new TCEMServico;
                    $rsListaServico = new RecordSet;
                    $obTCEMServico->recuperaTodos( $rsListaServico, $stFiltroServico,null,$boTransacao);

                    $stFiltro = null;
                        $rsTFISFaturamentoServico = new RecordSet;
                    $this->obTFISFaturamentoServico->proximoCodigo($rsTFISFaturamentoServico,$stFiltro,null,$boTransacao);
                    $this->ocorrencia = $rsTFISFaturamentoServico->getCampo('max')+1;

                    $this->obTFISFaturamentoServico->setDado('cod_processo',$param['inCodProcesso']);
                    $this->obTFISFaturamentoServico->setDado('competencia',$this->competencia);
                    $this->obTFISFaturamentoServico->setDado('cod_servico',$rsListaServico->getCampo('cod_servico'));
                    $this->obTFISFaturamentoServico->setDado('cod_atividade',$param['inCodAtividade']);
                    $this->obTFISFaturamentoServico->setDado('ocorrencia',$this->ocorrencia);
                    $this->obTFISFaturamentoServico->setDado('cod_modalidade',$param['inModalidade']);
                    $this->obTFISFaturamentoServico->setDado('dt_emissao',$nota['dtEmissao']);
                    $obErro = $this->obTFISFaturamentoServico->inclusao($boTransacao);
                    }
                    if ($obErro->ocorreu()) {
                        return sistemaLegado::exibeAviso( "Vefifique se a modalidade desta atividade foi definida!","n_incluir","erro" );
                    } else {

                    $this->obTFISRetencaoServico->proximoCod($num_servico,$boTransacao);
                    $this->obTFISRetencaoServico->setDado('cod_processo',$param['inCodProcesso']);
                    $this->obTFISRetencaoServico->setDado('competencia',$this->competencia);
                    $this->obTFISRetencaoServico->setDado('cod_nota',$numNota);
                    $this->obTFISRetencaoServico->setDado('num_servico',$num_servico);
                    $this->obTFISRetencaoServico->setDado('cod_servico',$rsListaServico->getCampo('cod_servico'));
                    $this->obTFISRetencaoServico->setDado('valor_declarado',$servico['flValorDeclarado']);
                    $this->obTFISRetencaoServico->setDado('valor_deducao',$servico['flDeducao']);
                    $this->obTFISRetencaoServico->setDado('valor_lancado',$servico['flValorLancado']);
                    $this->obTFISRetencaoServico->setDado('aliquota',$servico['flAliquota']);
                    $obErro = $this->obTFISRetencaoServico->inclusao($boTransacao);
                    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFISRetencaoServico);
                    }
                    if ($obErro->ocorreu()) {
                        return sistemaLegado::exibeAviso( "Erro ao incluir retenção servico","n_incluir","erro" );
                    }

                }//fim foreach servico

               }//fim foreach notas

            $result =  sistemaLegado::alertaAviso('FLManterLevantamento.php' , $param['inCodProcesso'] ,"incluir","aviso", Sessao::getId(), "../");

        return 	$result;

    }//fim função incluir

}
