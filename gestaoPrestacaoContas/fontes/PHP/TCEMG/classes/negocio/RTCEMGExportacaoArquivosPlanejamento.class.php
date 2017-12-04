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
 * Classe de regra de exportacao dos arquivos de planejamento TCE/MG
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Eduardo Schitz   <eduardo.schitz@cnm.org.br>
 * $Id: $
 */

/* Includes */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_LDO_MAPEAMENTO."TLDOTipoIndicadores.class.php";
include_once CAM_GF_PPA_MAPEAMENTO."TPPAPrograma.class.php";
include_once CAM_GF_PPA_MAPEAMENTO."TPPA.class.php";
include_once CAM_GF_PPA_MAPEAMENTO."TPPAAcao.class.php";
include_once CAM_GF_PPA_MAPEAMENTO."TPPAAcaoQuantidade.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrevisaoReceita.class.php";
include_once CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php";
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php';
include_once CAM_GPC_STN_MAPEAMENTO.'TSTNRiscosFiscais.class.php';
include_once CAM_GPC_STN_MAPEAMENTO.'TSTNProvidencias.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGConfiguracaoLOA.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGConfigurarIDE.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TExportacaoTCEMGItem.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGConfiguracaoPERC.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGMetasFiscais.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGRegistrosArquivoPrograma.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGAMP.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGArquivoAMP.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGArquivoUOC.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGORGAO.class.php";
include_once CAM_GF_EXP_MAPEAMENTO."TExportacaoTCEMGUniOrcam.class.php";
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGConsideracaoArquivo.class.php";

/**
    * Classe de Regra para geração de arquivo de planejamento para o ExportacaoTCE-MG

    * @author   Desenvolvedor :   Eduardo Schitz
*/
class RTCEMGExportacaoArquivosPlanejamento
{
    /* Valores entre*/
    public $stCodEntidades ;
    public $stExercicio    ;
    public $stMes          ;
    public $arArquivos = array();
    public $obTPPPrograma;
    public $obTPPAAcao;
    public $obTPPAAcaoQuantidade;
    public $obTOrcamentoPrevisaoReceita;
    public $obTNorma;
    public $obTOrcamentoReceita;
    public $obTAdministracaoConfiguracaoEntidade;
    public $obTSTNRiscosFiscais;
    public $obTSTNProvidencias;
    public $obTExportacaoTCEMGItem;
    public $obTTCEMGRegistrosArquivoPrograma;
    public $obTTCEMGArquivoAMP;
    public $obTTCEMGArquivoUOC;
    public $obTTCEMGORGAO;
    
    
    /**
    * Metodo Construtor
    * @access Private
    */
    public function RTCEMGExportacaoArquivosPlanejamento()
    {
        $this->obTPPPrograma                        = new TPPAPrograma();
        $this->obTPPAAcao                           = new TPPAAcao();
        $this->obTPPAAcaoQuantidade                 = new TPPAAcaoQuantidade();
        $this->obTOrcamentoPrevisaoReceita          = new TOrcamentoPrevisaoReceita;
        $this->obTNorma                             = new TNorma;
        $this->obTOrcamentoReceita                  = new TOrcamentoReceita;
        $this->obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade;
        $this->obTTCEMGConfiguracaoLOA              = new TTCEMGConfiguracaoLOA;
        $this->obTSTNRiscosFiscais                  = new TSTNRiscosFiscais;
        $this->obTSTNProvidencias                   = new TSTNProvidencias;
        $this->obTTCEMGConfigurarIDE                = new TTCEMGConfigurarIDE;
        $this->obTExportacaoTCEMGUniOrcam           = new TExportacaoTCEMGUniOrcam;
        $this->obTExportacaoTCEMGItem               = new TExportacaoTCEMGItem;
        $this->obTTCEMGConfiguracaoPERC             = new TTCEMGConfiguracaoPERC;
        $this->obTTCEMGAMP                          = new TTCEMGAMP;
        $this->obTTCEMGArquivoAMP                   = new TTCEMGArquivoAMP;
        $this->obTTCEMGArquivoUOC                   = new TTCEMGArquivoUOC;
        $this->obTTCEMGRegistrosArquivoPrograma     = new TTCEMGRegistrosArquivoPrograma;
        $this->obTTCEMGORGAO                        = new TTCEMGORGAO;
        $this->obTTCEMGCONSID                       = new TTCEMGConsideracaoArquivo;
    }

    // SETANDO
    public function setCodEntidades($valor) {   $this->stCodEntidades   =   $valor; }
    public function setExercicio($valor) {   $this->stExercicio      =   $valor; }
    public function setMes($valor) {   $this->stMes      =   $valor; }
    public function setArquivos($valor) {   $this->arArquivos       =   $valor; }

    // GETANDO
    public function getCodEntidades() {   return $this->stCodEntidades;   }
    public function getExercicio() {   return $this->stExercicio   ;   }
    public function getMes() {   return $this->stMes   ;   }
    public function getArquivos() {   return $this->arArquivos    ;   }

    // Gerando Recordset
    public function geraRecordset(&$arRecordSetArquivos)
    {
        if (in_array("AMP.csv",$this->getArquivos())) {
            
            //Tipo Registro 10
            $this->obTTCEMGAMP->setDado('exercicio', $this->getExercicio());
            $this->obTTCEMGAMP->setDado('mes'      , $this->getMes());
            $this->obTTCEMGAMP->recuperaDadosExportacaoTipo10($rsRecordSet10);
            
            $arRecordSetArquivos["AMP10"] = $rsRecordSet10;

            foreach ($rsRecordSet10->arElementos AS $arAMP) {
                $this->obTTCEMGArquivoAMP->setDado('cod_acao' , $arAMP['cod_acao']);
                $this->obTTCEMGArquivoAMP->setDado('exercicio', $this->getExercicio());
                $this->obTTCEMGArquivoAMP->setDado('mes'      , $this->getMes());
                $this->obTTCEMGArquivoAMP->recuperaPorChave($rsArquivoAMP);

                if ($rsArquivoAMP->getNumLinhas() < 1) {
                    $this->obTTCEMGArquivoAMP->inclusao();
                }
            }
            
            //Tipo Registro 12
            $rsAdminConfiguracao = new Recordset();
            $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
            $obTAdministracaoConfiguracao->setDado('exercicio', Sessao::getExercicio());
            $obTAdministracaoConfiguracao->setDado('parametro', 'cod_entidade_prefeitura');
            $obTAdministracaoConfiguracao->setDado('cod_modulo', 8);
            $obTAdministracaoConfiguracao->recuperaPorChave($rsAdminConfiguracao, $boTransacao);

            $this->obTAdministracaoConfiguracaoEntidade->setDado('cod_modulo', 55);
            $this->obTAdministracaoConfiguracaoEntidade->setDado('cod_entidade', $rsAdminConfiguracao->getCampo('valor'));
            $this->obTAdministracaoConfiguracaoEntidade->setDado('parametro', 'tcemg_codigo_orgao_entidade_sicom');
            $this->obTAdministracaoConfiguracaoEntidade->setDado('exercicio', Sessao::getExercicio());
            $this->obTAdministracaoConfiguracaoEntidade->recuperaPorChave($rsAdminConfigEntidade);

            if ($rsAdminConfigEntidade->getNumLinhas() < 0) {
                $obTAdministracaoConfiguracao->setDado('exercicio', Sessao::getExercicio());
                $obTAdministracaoConfiguracao->setDado('parametro', 'nom_prefeitura');
                $obTAdministracaoConfiguracao->setDado('cod_modulo', 2);
                $obTAdministracaoConfiguracao->recuperaPorChave($rsAdminConfiguracao2, $boTransacao);
                
                SistemaLegado::alertaAviso("FLExportarArquivosPlanejamento.php?".Sessao::getId()."&stAcao=$stAcao", "As configuração de Orgão não está configuradas. Por favor configurar a Entidade (".$rsAdminConfiguracao2->getCampo('valor').").", "", "aviso", Sessao::getId(), "../");
                die;
            }
            
            
            $this->obTTCEMGAMP->setDado('entidades', $this->getCodEntidades());
            $this->obTTCEMGAMP->setDado('cod_orgao',$rsAdminConfigEntidade->getCampo('valor'));
            $this->obTTCEMGAMP->recuperaDadosExportacaoTipo12($rsRecordSet12);

            $arRecordSetArquivos["AMP12"] = $rsRecordSet12;
        }
        
        if (in_array("DSP.csv",$this->getArquivos())) {
            $rsRecordSet10 = new RecordSet();
            $rsRecordSet11 = new RecordSet();

            $this->obTPPAAcao->setDado('entidades', $this->getCodEntidades());
            $this->obTPPAAcao->setDado('exercicio', $this->getExercicio());
            $this->obTPPAAcao->recuperaDadosExportacaoDespesa($rsRecordSet10);
            
            $arRecordSetArquivos["DSP10"] = $rsRecordSet10;

            //Tipo Registro 11
            $this->obTPPAAcao->setDado('entidades', $this->getCodEntidades());
            $this->obTPPAAcao->recuperaDadosExportacaoDespesaFonteRecurso($rsRecordSet11);
            
            $arRecordSetArquivos["DSP11"] = $rsRecordSet11;
        }

        if (in_array("IDE.csv",$this->getArquivos())) {
            $this->obTTCEMGConfigurarIDE->setDado('exercicio',$this->getExercicio());
            $this->obTTCEMGConfigurarIDE->setDado('entidades',$this->getCodEntidades());
            $this->obTTCEMGConfigurarIDE->recuperaDadosExportacao($rsRecordSet);

            $arRecordSetArquivos["IDE.csv"] = $rsRecordSet;
        }


        if (in_array("LDO.csv",$this->getArquivos())) {
            $this->obTNorma->setDado('exercicio', $this->getExercicio());
            $this->obTNorma->recuperaDadosExportacaoLDO($rsRecordSet);

            $arRecordSetArquivos["LDO.csv"] = $rsRecordSet;
        }

        if (in_array("LOA.csv",$this->getArquivos())) {
            //Tipo Registro 10
            $this->obTTCEMGConfiguracaoLOA->setDado('exercicio',$this->getExercicio());
            $this->obTTCEMGConfiguracaoLOA->recuperaRegistro10( $rsRecordSet );
            $arRecordSetArquivos["LOA10"] = $rsRecordSet;

            //Tipo Registro 11
            $inIndex = 0;
            $rsRecordSet = new RecordSet();
            $rsRecordSet11 = new RecordSet();
            $arRecordSet11 = array();

            while ($inIndex < 3) {
                $this->obTTCEMGConfiguracaoLOA->setDado('tipo', $inIndex+1);
                $this->obTTCEMGConfiguracaoLOA->setDado('exercicio', Sessao::getExercicio());
                $this->obTTCEMGConfiguracaoLOA->recuperaRegistro11( $rsRecordSet11 );
                
                $arRecordSet11[] = $rsRecordSet11->arElementos[0];
                $inIndex++;
            }
            $rsRecordSet->preenche($arRecordSet11);
            $arRecordSetArquivos["LOA11"] = $rsRecordSet;
        }

        if (in_array("LPP.csv",$this->getArquivos())) {
            $this->obTNorma->setDado('exercicio',$this->getExercicio());
            $this->obTNorma->recuperaDadosExportacaoLPP($rsRecordSet);
            $arRecordSetArquivos["LPP.csv"] = $rsRecordSet;
        }

        if (in_array("MTBIARREC.csv",$this->getArquivos())) {
            $this->obTOrcamentoPrevisaoReceita->setDado('entidades', $this->getCodEntidades());
            $this->obTOrcamentoPrevisaoReceita->recuperaDadosExportacaoMeta($rsRecordSet);

            $arRecordSetArquivos["MTBIARREC.csv"] = $rsRecordSet;
        }

        if (in_array("MTFIS.csv",$this->getArquivos())) {
            $stExercicios = '';
            for ($i=0;$i<3;$i++) {
                if ($i == 2) {
                    $stExercicios .= Sessao::getExercicio()+$i;
                } else {
                    $stExercicios .= Sessao::getExercicio()+$i.',';
                }
            }

            $rsTTCEMGMetasFiscais = new RecordSet();
            $obTTCEMGMetasFiscais = new TTCEMGMetasFiscais();
            $obTTCEMGMetasFiscais->setDado('exercicio', $stExercicios);
            $obTTCEMGMetasFiscais->recuperaValoresMetasFiscais($rsTTCEMGMetasFiscais);

            if ($rsTTCEMGMetasFiscais->getNumLinhas() < 3) {
                SistemaLegado::alertaAviso("FLExportarArquivosPlanejamento.php?".Sessao::getId()."&stAcao=$stAcao", "As configurações das Metas Fiscais não estão configuradas. Por favor configurar as Metas Fiscais para os anos(".$stExercicios.").", "", "aviso", Sessao::getId(), "../");
                die;
            }
            $arRecordSetArquivos["MTFIS.csv"] = $rsTTCEMGMetasFiscais;
        }

        if (in_array("ORGAO.csv",$this->getArquivos())) {
            $this->obTTCEMGORGAO->setDado('entidade', $this->getCodEntidades());
            $this->obTTCEMGORGAO->recuperaExportacaoOrgaoPlanejamento($rsRecordSet);
            
            $arRecordSetArquivos["ORGAO.csv"] = $rsRecordSet;
        }

        if (in_array("PERC.csv",$this->getArquivos())) {
            $obErro = $this->obTTCEMGConfiguracaoPERC->recuperaExportacao($rsRecordSet);
            $arRecordSetArquivos["PERC.csv"] = $rsRecordSet;
        }

        if (in_array("PRO.csv",$this->getArquivos())) {
           $rsVerificaProgramas = new RecordSet();
            $this->obTTCEMGRegistrosArquivoPrograma->setDado('exercicio', Sessao::getExercicio());
            $this->obTTCEMGRegistrosArquivoPrograma->recuperaPorChave($rsVerificaProgramas);
            
            if($rsVerificaProgramas->getNumLinhas() > 0){
                $this->obTTCEMGRegistrosArquivoPrograma->setDado('boReemissao','true');
            }else{
                $this->obTTCEMGRegistrosArquivoPrograma->setDado('boReemissao','false');
            }
            
            $obErro = $this->obTTCEMGRegistrosArquivoPrograma->recuperaTotalRecursos($rsRecordSet);

            $arRecordSetArquivos["PRO.csv"] = $rsRecordSet;
        }

        if (in_array("REC.csv",$this->getArquivos())) {
            $this->obTOrcamentoReceita->setDado('entidades' , $this->getCodEntidades());
            $stDataInicial = "01/".$this->getMes()."/".Sessao::getExercicio();
            $stDataFinal   = SistemaLegado::retornaUltimoDiaMes($this->getMes(), Sessao::getExercicio());
            $this->obTOrcamentoReceita->setDado('dt_inicial', $stDataInicial );
            $this->obTOrcamentoReceita->setDado('dt_final'  , $stDataFinal   );
            
            //Tipo Registro 10
            $this->obTOrcamentoReceita->recuperaReceitaExportacaoPlanejamento10($rsRecordSet, $boTransacao);

            $arRecordSetArquivos["REC10"] = $rsRecordSet;

            //Tipo Registro 11
            $this->obTOrcamentoReceita->recuperaReceitaExportacaoPlanejamento11($rsRecordSet, $boTransacao);

            $arRecordSetArquivos["REC11"] = $rsRecordSet;

            //Tipo Registro 99
            $arRecordSetRECS99 = array(
                '0' => array(
                    'tipo_registro' => '99',
                )
            );

            $rsRecordSet = new RecordSet();
            $rsRecordSet->preenche($arRecordSetRECS99);

            $arRecordSetArquivos["REC99"] = $rsRecordSet;
        }

        if (in_array("RFIS.csv",$this->getArquivos())) {
            //Tipo Registro 10
            $this->obTSTNRiscosFiscais->setDado('entidades', $this->getCodEntidades());
            $this->obTSTNRiscosFiscais->listaRiscosFiscaisExportacao10($rsRecordSet10);

            $arRecordSetArquivos["RFIS10"] = $rsRecordSet10;

            //Tipo Registro 11
            $stFiltro  = " WHERE providencias.exercicio = '".Sessao::getExercicio()."' ";
            $stFiltro .= "   AND providencias.cod_entidade IN (".$this->getCodEntidades().") ";
            $this->obTSTNProvidencias->listProvidencias($rsRecordSet11, $stFiltro);

            $arRecordSetArquivos["RFIS11"] = $rsRecordSet11;

            //Tipo Registro 99
            $arRecordSetRFIS99 = array(
                '0' => array(
                    'tipo_registro' => '99',
                )
            );

            $rsRecordSet99 = new RecordSet();
            $rsRecordSet99->preenche($arRecordSetRFIS99);

            $arRecordSetArquivos["RFIS99"] = $rsRecordSet99;
        }

        if (in_array("MTFIS.csv",$this->getArquivos())) {
            //Tipo Registro 10
            $this->obTSTNRiscosFiscais->setDado('entidades', $this->getCodEntidades());
            $this->obTSTNRiscosFiscais->listaRiscosFiscaisExportacao10($rsRecordSet10);

            $arRecordSetArquivos["MTFIS10"] = $rsRecordSet10;

            //Tipo Registro 11
            $stFiltro  = " WHERE providencias.exercicio = '".Sessao::getExercicio()."' ";
            $stFiltro .= "   AND providencias.cod_entidade IN (".$this->getCodEntidades().") ";
            $this->obTSTNProvidencias->listProvidencias($rsRecordSet11, $stFiltro);

            $arRecordSetArquivos["MTFIS11"] = $rsRecordSet11;

            //Tipo Registro 99
            $arRecordSetRFIS99 = array(
                '0' => array(
                    'tipo_registro' => '99',
                )
            );

            $rsRecordSet99 = new RecordSet();
            $rsRecordSet99->preenche($arRecordSetRFIS99);

            $arRecordSetArquivos["RFIS99"] = $rsRecordSet99;
        }

        if (in_array("RSP.csv",$this->getArquivos())) {
            //Tipo Registro 10
            $this->obTRSP->setDado('exercicio', $this->getExercicio());
            $this->obTRSP->setDado('entidades', $this->getCodEntidades());
            $this->obTRSP->recuperaExportacaoRSP10($rsRecordSet10);

            $arRecordSetArquivos["RSP10"] = $rsRecordSet10;

            //Tipo Registro 11
            $this->obTRSP->setDado('exercicio', $this->getExercicio());
            $this->obTRSP->recuperaExportacaoRSP11($rsRecordSet11);

            $arRecordSetArquivos["RSP11"] = $rsRecordSet11;
        }

        if (in_array("UOC.csv",$this->getArquivos())) {
            $this->obTExportacaoTCEMGUniOrcam->setDado('exercicio',$this->getExercicio());
            $this->obTExportacaoTCEMGUniOrcam->setDado('entidades', $this->getCodEntidades());
            $this->obTExportacaoTCEMGUniOrcam->recuperaDadosEntidade($rsRecordSet);

            foreach ($rsRecordSet->arElementos as $elemento) {
                $this->obTExportacaoTCEMGUniOrcam->setDado('exercicio',$this->getExercicio());
                $this->obTExportacaoTCEMGUniOrcam->setDado('entidades', $elemento['cod_entidade']);
                $this->obTExportacaoTCEMGUniOrcam->setDado('cod_orgao', $elemento['valor']);
                $this->obTExportacaoTCEMGUniOrcam->setDado('mes'      , $this->getMes());
                $this->obTExportacaoTCEMGUniOrcam->recuperaDadosExportacaoUOC($rsRecordSetUOC);

                foreach ($rsRecordSetUOC->arElementos as $elementoUOC) {
                    $arRegistros[] = $elementoUOC;

                    $this->obTTCEMGArquivoUOC->setDado('num_orgao'  , $elementoUOC['num_orgao']);
                    $this->obTTCEMGArquivoUOC->setDado('num_unidade', $elementoUOC['num_unidade']);
                    $this->obTTCEMGArquivoUOC->setDado('exercicio'  , $this->getExercicio());
                    $this->obTTCEMGArquivoUOC->setDado('mes'        , $this->getMes());
                    $this->obTTCEMGArquivoUOC->recuperaPorChave($rsArquivoUOC);

                    if ($rsArquivoUOC->getNumLinhas() < 1) {
                        $this->obTTCEMGArquivoUOC->inclusao();
                    }
                }
            }

            $rsRecordSet->arElementos = $arRegistros;
            $rsRecordSet->inNumLinhas = count($arRegistros);

            $arRecordSetArquivos["UOC.csv"] = $rsRecordSet;
        }

        if (in_array("CONSID.csv",$this->getArquivos())) {
            //Tipo Registro 10
            $this->obTTCEMGCONSID->setDado('exercicio'   , $this->getExercicio());
            $this->obTTCEMGCONSID->setDado('entidade'    , $this->getCodEntidades());
            $this->obTTCEMGCONSID->setDado('mes'         , $this->getMes());
            $this->obTTCEMGCONSID->setDado('modulo_sicom','planejamento');
            $this->obTTCEMGCONSID->recuperaConsid($rsRecordSet10);
            
            $arRecordSetArquivos["CONSID10"] = $rsRecordSet10;
            
             //Tipo Registro 99
            $arCONSID99 = array(
                '0' => array(
                    'tipo_registro' => '99',
                )
            );

            $rsRecordSet99 = new RecordSet();
            $rsRecordSet99->preenche($arCONSID99);

            $arRecordSetArquivos["CONSID99"] = $rsRecordSet99;
        }

        return $obErro;
    }
}
?>
