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
 * $Id: RTCEMGExportacaoInclusaoProgramas.class.php 64340 2016-01-15 19:31:57Z jean $ RTCEMGExportarAcompanhamentoMensal.class.php 57095 2014-02-04 11:34:18Z lisiane $
 */

/* Includes */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EXP_MAPEAMENTO."TExportacaoTCEMGUniOrcam.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGRegistrosArquivoPrograma.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGArquivoINCAMP.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGArquivoIUOC.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGincamp.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGArquivoMensalIDE.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGConsideracaoArquivo.class.php";

/**
    * Classe de Regra para geração de arquivo de planejamento para o ExportacaoTCE-MG

    * @author   Desenvolvedor :   Eduardo Schitz

*/
class RTCEMGExportacaoInclusaoProgramas
{
    /* Valores entre*/
    public $stCodEntidades ;
    public $stExercicio    ;
    public $stMes;
    public $arArquivos = array();
    public $obTTCEMGArquivoMensalIDE;
    public $obTExportacaoTCEMGUniOrcam;
    public $obTTCEMGRegistrosArquivoPrograma;
    public $obTTCEMGArquivoIUOC;
    public $obTTCEMGConsideracaoArquivo;
    
    /**
    * Metodo Construtor
    * @access Private
    */
    public function RTCEMGExportacaoInclusaoProgramas()
    {
        $this->obTTCEMGArquivoMensalIDE             = new TTCEMGArquivoMensalIDE;
        $this->obTExportacaoTCEMGUniOrcam           = new TExportacaoTCEMGUniOrcam;
        $this->obTTCEMGincamp                       = new TTCEMGincamp;
        $this->obTAdministracaoConfiguracao         = new TAdministracaoConfiguracao;
        $this->obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade;
        $this->obTTCEMGRegistrosArquivoPrograma     = new TTCEMGRegistrosArquivoPrograma;
        $this->obTTCEMGArquivoIncamp                = new TTCEMGArquivoINCAMP;
        $this->obTTCEMGArquivoIUOC                  = new TTCEMGArquivoIUOC;
        $this->obTTCEMGConsideracaoArquivo          = new TTCEMGConsideracaoArquivo;
    }

    // SETANDO
    public function setCodEntidades($valor)    {   $this->stCodEntidades   =   $valor; }
    public function setExercicio($valor)       {   $this->stExercicio      =   $valor; }
    public function setMes($valor)             {   $this->stMes            =   $valor; }
    public function setArquivos($valor)        {   $this->arArquivos       =   $valor; }

    // GETANDO
    public function getCodEntidades()          {   return $this->stCodEntidades;   }
    public function getExercicio()             {   return $this->stExercicio   ;   }
    public function getMes()                   {   return $this->stMes         ;   }
    public function getArquivos()              {   return $this->arArquivos    ;   }

    // Gerando Recordset
    public function geraRecordset(&$arRecordSetArquivos)
    {
        if (in_array("IDE.csv",$this->getArquivos())) {
            $this->obTTCEMGArquivoMensalIDE->setDado('exercicio',$this->getExercicio());
            $this->obTTCEMGArquivoMensalIDE->setDado('entidades',$this->getCodEntidades());
            $this->obTTCEMGArquivoMensalIDE->setDado('mes', $this->getMes());
            $this->obTTCEMGArquivoMensalIDE->recuperaDadosExportacao($rsRecordSet);

            $arRecordSetArquivos["IDE.csv"] = $rsRecordSet;
        }

        if (in_array("IUOC.csv",$this->getArquivos())) {
            $this->obTExportacaoTCEMGUniOrcam->setDado('exercicio',$this->getExercicio());
            $this->obTExportacaoTCEMGUniOrcam->setDado('entidades', $this->getCodEntidades());
            $this->obTExportacaoTCEMGUniOrcam->recuperaDadosEntidade($rsRecordSet);

            foreach ($rsRecordSet->arElementos as $elemento) {
                $this->obTExportacaoTCEMGUniOrcam->setDado('exercicio',$this->getExercicio());
                $this->obTExportacaoTCEMGUniOrcam->setDado('entidades', $elemento['cod_entidade']);
                $this->obTExportacaoTCEMGUniOrcam->setDado('cod_orgao', $elemento['valor']);
                $this->obTExportacaoTCEMGUniOrcam->setDado('mes'      , $this->getMes());
                $this->obTExportacaoTCEMGUniOrcam->recuperaDadosExportacaoIUOC($rsRecordSetUOC);

                foreach ($rsRecordSetUOC->arElementos as $elementoUOC) {
                    $arRegistros[] = $elementoUOC;

                    $this->obTTCEMGArquivoIUOC->setDado('num_orgao'  , $elementoUOC['num_orgao']);
                    $this->obTTCEMGArquivoIUOC->setDado('num_unidade', $elementoUOC['num_unidade']);
                    $this->obTTCEMGArquivoIUOC->setDado('exercicio'  , $this->getExercicio());
                    $this->obTTCEMGArquivoIUOC->setDado('mes'        , $this->getMes());
                    $this->obTTCEMGArquivoIUOC->recuperaPorChave($rsArquivoUOC);

                    if ($rsArquivoUOC->getNumLinhas() < 1) {
                        $this->obTTCEMGArquivoIUOC->inclusao();
                    }
                }
            }

            $rsRecordSet->arElementos = $arRegistros;
            $rsRecordSet->inNumLinhas = count($arRegistros);

            $arRecordSetArquivos["IUOC.csv"] = $rsRecordSet;
        }
        
        if (in_array("INCAMP.csv",$this->getArquivos())) {
            $this->obTTCEMGincamp->setDado('exercicio', $this->getExercicio());
            $this->obTTCEMGincamp->setDado('entidades', $this->getCodEntidades());
            $this->obTTCEMGincamp->setDado('mes'      , $this->getMes());
            
            // Registro 10
            $this->obTTCEMGincamp->recuperaRegistro10($rsRecordSet);
            $arRecordSetArquivos["INCAMP10"] = $rsRecordSet;

            foreach ($rsRecordSet->arElementos AS $arIncamp) {
                $this->obTTCEMGArquivoIncamp->setDado('cod_acao' , $arIncamp['cod_acao']);
                $this->obTTCEMGArquivoIncamp->setDado('exercicio', $this->getExercicio());
                $this->obTTCEMGArquivoIncamp->setDado('mes'      , $this->getMes());
                $this->obTTCEMGArquivoIncamp->recuperaPorChave($rsArquivoIncamp);

                if ($rsArquivoIncamp->getNumLinhas() < 1) {
                    $this->obTTCEMGArquivoIncamp->inclusao();
                }
            }
            
            // Registro 11 Por enquanto não existe sub-ações entao não terá registro 11
            //$this->obTTCEMGincamp->recuperaRegistro11($rsRecordSet);
            //$arRecordSetArquivos["INCAMP11"] = $rsRecordSet;
            
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
            
            $this->obTTCEMGincamp->setDado('cod_orgao',$rsAdminConfigEntidade->getCampo('valor'));
            $this->obTTCEMGincamp->recuperaRegistro12($rsRecordSet12);
            $arRecordSetArquivos["INCAMP12"] = $rsRecordSet12;
        }
        
        if (in_array("INCPRO.csv",$this->getArquivos())) {
            $rsInclusaoProgramas = new Recordset();
            $this->obTTCEMGRegistrosArquivoPrograma->setDado('dt_final',SistemaLegado::retornaUltimoDiaMes($this->getMes(),Sessao::getExercicio()));
            $this->obTTCEMGRegistrosArquivoPrograma->setDado('exercicio', Sessao::getExercicio());
            $this->obTTCEMGRegistrosArquivoPrograma->recuperaRecursosIncluisaoPrograma($rsInclusaoProgramas);
            
            $arRecordSetArquivos["INCPRO.csv"] = $rsInclusaoProgramas;
        }

        if ( Sessao::getExercicio() == '2016' ) {
            if (in_array("CONSID.csv",$this->getArquivos())) {
                $this->obTTCEMGConsideracaoArquivo->setDado('exercicio'   , $this->getExercicio());
                $this->obTTCEMGConsideracaoArquivo->setDado('entidade'    , $this->getCodEntidades());
                $this->obTTCEMGConsideracaoArquivo->setDado('mes'         , $this->getMes());
                $this->obTTCEMGConsideracaoArquivo->setDado('modulo_sicom', 'inclusao');
            
                $this->obTTCEMGConsideracaoArquivo->recuperaConsid($rsConsideracaoArquivo);
                $arRecordSetArquivos["CONSID.csv"] = $rsConsideracaoArquivo;
            }
        }
        return $obErro;
    }
}
?>
