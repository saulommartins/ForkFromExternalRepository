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

    * Classe para exportar arquivos XML com informações da Execução
    * Data de Criação   : 17/09/2013

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal
    
    $Id: RExportacaoExecucao.class.php 59612 2014-09-02 12:00:51Z gelson $
    
    * @ignore

    * Casos de uso: uc-06.01.22
*/

include_once(CAM_GPC_TCEAL_MAPEAMENTO."TExportacaoExecucao.class.php");
include_once(CLA_ARQUIVO_ZIP);

class RExportacaoExecucao
{
    private $obXML;
    private $obTExportacaoExecucao;
    private $obArquivoZip;
    private $nomeDocumento;
    private $inBimestre;
    private $stEntidades;

    private $dtInicio;
    private $dtFim;

    public function __construct()
    {
        $this->obXML = new XMLWriter();
        $this->obTExportacaoExecucao = new TExportacaoExecucao();
        $this->obArquivoZip = new ArquivoZip();
    }

    public function iniciaDocumento()
    {
        # Cria memoria para armazenar a saida
        $this->obXML->openMemory();

        # Seta identação para visualizar melhor
        $this->obXML->setIndent(true);

        # Inicia o cabeçalho do documento XML
        $this->obXML->startDocument( '1.0' , 'UTF-8', 'yes' );
    }

    //seta o nome do documento xml que vai ser gerado
    private function setNomeDocumento($nomeDocumento)
    {
        $this->nomeDocumento = $nomeDocumento;
        if (!preg_match("/xml|XML/", $nomeDocumento)) {
            $this->nomeDocumento .= ".xml";
        }
    }

    //seta o bimestre ao qual os documentos vão ser gerados
    public function setBimestre($inBimestre)
    {
        $this->inBimestre = $inBimestre;
    }

     //seta o bimestre ao qual os documentos vão ser gerados
    public function setEntidades($stEntidades)
    {
        $this->stEntidades = $stEntidades;
    }
        
     //seta a versão ao qual os documentos vão ser gerados
    public function setVersao($stVersao)
    {
        $this->stVersao = $stVersao;
    }
    
    //retorna a versão ao qual os documentos vão ser gerados
    public function getVersao()
    {
        return $this->stVersao;
    }

    public function finalizaDocumento()
    {
        file_put_contents(CAM_FRAMEWORK."tmp/".$this->nomeDocumento, $this->obXML->outputMemory(true));
    }

    public function geraDocumentoXMLContaDisponibilidade()
    {
        $arResult = $this->geraDadosXMLContaDisponibilidade();

        $this->iniciaDocumento();

        $this->obXML->startElement("SICAP");
        $this->obXML->writeAttribute("versao", "1.0");

        if (count($arResult)) {
            foreach ($arResult as $result) {
                $this->obXML->startElement("ContaDisponibilidade");
                foreach ($result as $key=>$value) {
                    $this->obXML->startElement($key);
                    $this->obXML->text($value);
                    $this->obXML->endElement();
                }
                $this->obXML->endElement();
            }
        }

        $this->obXML->endElement();
        // finaliza elemento INFORMACAO
        $this->obXML->endDocument();
        $this->finalizaDocumento();

        //define a lista de arquivos para download
        $arArquivos = Sessao::read('arArquivosDownload');
        $arArquivos[] = array('stLink' => CAM_FRAMEWORK."tmp/".$this->nomeDocumento, 'stNomeArquivo' => $this->nomeDocumento);
        Sessao::write('arArquivosDownload', $arArquivos);
    }

    public function geraDocumentoXMLRecDespExtraOrcamentarias()
    {
        $arResult = $this->geraDadosXMLRecDespExtraOrcamentarias();

        $this->iniciaDocumento();

        $this->obXML->startElement("SICAP");
        $this->obXML->writeAttribute("versao", "1.0");

        if (count($arResult)) {
            foreach ($arResult as $result) {
                $this->obXML->startElement("RecDespExtraOrcamentarias");
                foreach ($result as $key=>$value) {
                    $this->obXML->startElement($key);
                    $this->obXML->text($value);
                    $this->obXML->endElement();
                }
                $this->obXML->endElement();
            }
        }

        $this->obXML->endElement();
        // finaliza elemento INFORMACAO
        $this->obXML->endDocument();
        $this->finalizaDocumento();

        //define a lista de arquivos para download
        $arArquivos = Sessao::read('arArquivosDownload');
        $arArquivos[] = array('stLink' => CAM_FRAMEWORK."tmp/".$this->nomeDocumento, 'stNomeArquivo' => $this->nomeDocumento);
        Sessao::write('arArquivosDownload', $arArquivos);
    }

    public function geraDocumentoXMLServidores()
    {
        $arResult = $this->geraDadosXMLServidores();

        $this->iniciaDocumento();

        $this->obXML->startElement("SICAP");
        $this->obXML->writeAttribute("versao", "1.0");

        if (count($arResult)) {
            foreach ($arResult as $result) {
                $this->obXML->startElement("Servidores");
                foreach ($result as $key=>$value) {
                    $this->obXML->startElement($key);
                    $this->obXML->text($value);
                    $this->obXML->endElement();
                }
                $this->obXML->endElement();
            }
        }

        $this->obXML->endElement();
        // finaliza elemento INFORMACAO
        $this->obXML->endDocument();
        $this->finalizaDocumento();

        //define a lista de arquivos para download
        $arArquivos = Sessao::read('arArquivosDownload');
        $arArquivos[] = array('stLink' => CAM_FRAMEWORK."tmp/".$this->nomeDocumento, 'stNomeArquivo' => $this->nomeDocumento);
        Sessao::write('arArquivosDownload', $arArquivos);
    }
     
    public function geraDadosXMLContaDisponibilidade()
    {
        $this->obTExportacaoExecucao->setDado('stExercicio', Sessao::getExercicio());
        $this->obTExportacaoExecucao->setDado('stEntidades', $this->stEntidades );
        $this->obTExportacaoExecucao->listarExportacaoContaDisponibilidade($rsRecordSet);

        $this->setNomeDocumento("ContaDisponibilidade");
        $arResult = array();
        $idCount = 0;
        while (!$rsRecordSet->eof()) {
            $arResult[$idCount]['CodUndGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
            $arResult[$idCount]['CodigoUA'] = $rsRecordSet->getCampo('codigo_ua');
            $arResult[$idCount]['Bimestre'] = $this->inBimestre;
            $arResult[$idCount]['Exercicio'] = Sessao::getExercicio();
            $arResult[$idCount]['CodOrgao'] = $rsRecordSet->getCampo('cod_orgao');
            $arResult[$idCount]['CodUndOrcamentaria'] = $rsRecordSet->getCampo('cod_und_orcamentaria');
            $arResult[$idCount]['CodUndBalancete'] = $rsRecordSet->getCampo('cod_conta_balancete');
            $arResult[$idCount]['CodRecVinculado'] = $rsRecordSet->getCampo('cod_rec_vinculado');
            $arResult[$idCount]['Tipo'] = $rsRecordSet->getCampo('tipo');
            $arResult[$idCount]['CodBanco'] = $rsRecordSet->getCampo('cod_banco');
            $arResult[$idCount]['CodAgenciaBanco'] = $rsRecordSet->getCampo('cod_agencia_banco');
            $arResult[$idCount]['NumContaCorrente'] = $rsRecordSet->getCampo('num_conta_corrente');
            $arResult[$idCount]['Classificacao'] = $rsRecordSet->getCampo('classificacao');
            $idCount++;

            $rsRecordSet->proximo();
        }

        return $arResult;
    }

    public function geraDadosXMLRecDespExtraOrcamentarias()
    {
        if ($this->inBimestre =! 0 && $this->inBimestre != 7) {
            $this->obTExportacaoExecucao->setDado('stExercicio', Sessao::getExercicio());
            $this->obTExportacaoExecucao->setDado('inBimestre', $this->inBimestre);
            $this->obTExportacaoExecucao->buscaDatas($rsData);

            $dtInicial= explode('=', $rsData->getCampo('bimestre'));
            $dtInicial= explode(',', $dtInicial[1]);
            $dtInicial =substr($dtInicial[0], 1);

            $dtFinal = explode('=', $rsData->getCampo('bimestre'));
            $dtFinal = explode(',', $dtFinal[1]);
            $dtFinal =substr($dtFinal[1], 0, -1);

        } else {
            $dtInicial= '01/01/'.Sessao::getExercicio();
            $dtFinal= '31/12/'.Sessao::getExercicio();
        }

        $this->obTExportacaoExecucao->setDado('dtInicial', $dtInicial);
        $this->obTExportacaoExecucao->setDado('dtFinal', $dtFinal);
        $this->obTExportacaoExecucao->setDado('stExercicio', Sessao::getExercicio());
        $this->obTExportacaoExecucao->setDado('stEntidades', $this->stEntidades );
        $this->obTExportacaoExecucao->listarExportacaoRecDespExtraOrcamentarias($rsRecordSet);

        $this->setNomeDocumento("RecDespExtraOrcamentarias");
        $arResult = array();
        $idCount = 0;

        while (!$rsRecordSet->eof()) {
            $arResult[$idCount]['CodUndGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
            $arResult[$idCount]['CodigoUA'] = $rsRecordSet->getCampo('codigo_ua');
            $arResult[$idCount]['Bimestre'] = $this->inBimestre;
            $arResult[$idCount]['Exercicio'] = Sessao::getExercicio();
            $arResult[$idCount]['NumeroDaExtraOrcamentario'] = $rsRecordSet->getCampo('numero_da_extra_orcamentario');
            $arResult[$idCount]['CodContaBalancete'] = $rsRecordSet->getCampo('cod_conta_balancete');
            $arResult[$idCount]['IdentificadorDc'] = $rsRecordSet->getCampo(' identificador_dc');
            $arResult[$idCount]['Valor'] = $rsRecordSet->getCampo('transferencia.valor');
            $arResult[$idCount]['IdentificadorDr'] = $rsRecordSet->getCampo('identificador_dr');
            $arResult[$idCount]['TipoMovimentacao'] = $rsRecordSet->getCampo('tipo_movimentacao');
            $arResult[$idCount]['CodBanco'] = $rsRecordSet->getCampo('CodBanco');
            $arResult[$idCount]['CodAgenciaBanco'] = $rsRecordSet->getCampo('CodAgenciaBanco');
            $arResult[$idCount]['NumContaCorrente '] = $rsRecordSet->getCampo('num_conta_corrente ');
            $arResult[$idCount]['Classificacao'] = $rsRecordSet->getCampo('classificacao');
            $arResult[$idCount]['TipoPagamento'] = $rsRecordSet->getCampo('tipo_pagamento');
            $arResult[$idCount]['Descricao'] = $rsRecordSet->getCampo('descricao');
            $idCount++;

            $rsRecordSet->proximo();
        }

        return $arResult;
    }

    public function geraDadosXMLServidores()
    {
        $codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());
        include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        foreach (explode(',',$this->stEntidades) as $inCodEntidade) {
            $obTEntidade = new TEntidade();
            $stFiltro = " WHERE nspname = 'pessoal_".$inCodEntidade."'";
            $obTEntidade->recuperaEsquemasCriados($rsEsquema,$stFiltro);

            if ($rsEsquema->getNumLinhas() > 0 || $codEntidadePrefeitura ==$inCodEntidade ) {
                $arEsquemasEntidades[] = $inCodEntidade;
            }
        }

        foreach ($arEsquemasEntidades as $inCodEntidade) {
            if ($codEntidadePrefeitura !=$inCodEntidade) {
                $stEntidade = '_'.$inCodEntidade;
                $entidade = $inCodEntidade;
            } else {
                $stEntidade = '';
            }
           if ($inCodEntidade ==  $codEntidadePrefeitura) {
               $inCodEntidade='';
               $entidade= $codEntidadePrefeitura;
           }

            if ($this->inBimestre =! 0 && $this->inBimestre != 7) {
                $this->obTExportacaoExecucao->setDado('stExercicio', Sessao::getExercicio());
                $this->obTExportacaoExecucao->setDado('inBimestre', $this->inBimestre);
                $this->obTExportacaoExecucao->buscaDatas($rsData);

                $dtInicial= explode('=', $rsData->getCampo('bimestre'));
                $dtInicial= explode(',', $dtInicial[1]);
                $dtInicial =substr($dtInicial[0], 1);

                $dtFinal = explode('=', $rsData->getCampo('bimestre'));
                $dtFinal = explode(',', $dtFinal[1]);
                $dtFinal =substr($dtFinal[1], 0, -1);

            } else {
                $dtInicial= '01/01/'.Sessao::getExercicio();
                $dtFinal= '31/12/'.Sessao::getExercicio();
            }

            $stPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $stPeriodoMovimentacao->setDado('dtInicial', $dtInicial);
            $stPeriodoMovimentacao->setDado('dtFinal', $dtFinal);
            $stPeriodoMovimentacao->recuperaPeriodoMovimentacaoTCEAL($rsPeriodoMovimentacao);

            $rsRecordSet = "rsServidores";
            $rsRecordSet .= $stEntidade;
            $$rsRecordSet = new RecordSet();

            $this->obTExportacaoExecucao->setDado('dtInicial', $dtInicial);
            $this->obTExportacaoExecucao->setDado('dtFinal', $dtFinal);
            $this->obTExportacaoExecucao->setDado('codPeriodoMovimentacao', $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
            $this->obTExportacaoExecucao->setDado('stExercicio', Sessao::getExercicio());
            $this->obTExportacaoExecucao->setDado('stEntidade', $stEntidade  );
            $this->obTExportacaoExecucao->setDado('inCodEntidade', $inCodEntidade  );
            $this->obTExportacaoExecucao->setDado('entidade', $entidade  );

            $this->obTExportacaoExecucao->listarExportacaoServidores($rsRecordSet);

            $this->setNomeDocumento("Servidores");
            $arResult = array();
            $idCount = 0;

            while (!$rsRecordSet->eof()) {
                $arResult[$idCount]['CodUndGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
                $arResult[$idCount]['CodigoUA'] = $rsRecordSet->getCampo('codigo_ua');
                $arResult[$idCount]['Bimestre'] = $this->inBimestre;
                $arResult[$idCount]['Exercicio'] = Sessao::getExercicio();
                $arResult[$idCount]['Cpf'] = $rsRecordSet->getCampo('cpf');
                $arResult[$idCount]['Nome'] = $rsRecordSet->getCampo('nome');
                $arResult[$idCount]['DataNascimento'] = $rsRecordSet->getCampo('data_nascimento');
                $arResult[$idCount]['NomeMae'] = $rsRecordSet->getCampo('nome_mae');
                $arResult[$idCount]['NomePai'] = $rsRecordSet->getCampo('nome_pai');
                $arResult[$idCount]['PisPasep'] = $rsRecordSet->getCampo('pis_pasep');
                $arResult[$idCount]['TituloEleitoral'] = $rsRecordSet->getCampo('titulo_eleitoral');
                $arResult[$idCount]['DataAdmissao'] = $rsRecordSet->getCampo('dt_admissao');
                $arResult[$idCount]['CodVinculoEmpregaticio'] = $rsRecordSet->getCampo('cod_vinculo_empregaticio');
                $arResult[$idCount]['CodRegimePrevidenciario'] = $rsRecordSet->getCampo('cod_regime_previdenciario');
                $arResult[$idCount]['CodEscolaridade'] = $rsRecordSet->getCampo('cod_escolaridade');
                $arResult[$idCount]['SobCessao'] = $rsRecordSet->getCampo('sob_cessao');
                $arResult[$idCount]['CnpjEntidade'] = $rsRecordSet->getCampo('cnpj_entidade');
                $arResult[$idCount]['NomeEntidade'] = $rsRecordSet->getCampo('nome_entidade');
                $arResult[$idCount]['DataCessao'] = $rsRecordSet->getCampo('data_cessao');
                $arResult[$idCount]['DataRetornoCessao'] = $rsRecordSet->getCampo('data_retorno_cessao');
                $arResult[$idCount]['SalarioBruto'] = $rsRecordSet->getCampo('salario_bruto');
                $arResult[$idCount]['SalarioLiquido'] = $rsRecordSet->getCampo('salario_liquido');
                $arResult[$idCount]['MargemConsignada'] = $rsRecordSet->getCampo('margem_consignada');
                $arResult[$idCount]['CBO'] = $rsRecordSet->getCampo('cbo');
                $arResult[$idCount]['CodCargo'] = $rsRecordSet->getCampo('cod_cargo');
                $arResult[$idCount]['CodLotacao'] = $rsRecordSet->getCampo('cod_lotacao');
                $arResult[$idCount]['CodFuncao'] = $rsRecordSet->getCampo('cod_funcao');
                $arResult[$idCount]['Matricula'] = $rsRecordSet->getCampo('matricula');
                $idCount++;

                $rsRecordSet->proximo();
            }
          }

            return $arResult;
    }

    public function geraDocumentoXML($arResult, $stNomeArquivo = '', $stVersao="1.0")
    {        
        $this->iniciaDocumento();
        
        $this->obXML->startElement("SICAP");
        $this->obXML->writeAttribute("versao", $stVersao);
        
        $this->setNomeDocumento($stNomeArquivo);
        
        if (count($arResult)) {
            
            foreach ($arResult as $result) {    
                $this->obXML->startElement($stNomeArquivo);
                
                foreach ($result as $key=>$value) {
                    $this->obXML->startElement($key);
                    $this->obXML->text($value);
                    $this->obXML->endElement();
                }
                
                $this->obXML->endElement();
            }
        }
        
        $this->obXML->endElement();
        
        // finaliza elemento INFORMACAO
        $this->obXML->endDocument();
        $this->finalizaDocumento();
        
        //define a lista de arquivos para download
        $arArquivos = Sessao::read('arArquivosDownload');
        $arArquivos[] = array('stLink' => CAM_FRAMEWORK."tmp/".$this->nomeDocumento, 'stNomeArquivo' => $this->nomeDocumento);
        Sessao::write('arArquivosDownload', $arArquivos);
    }

    public function doZipArquivos()
    {
        $arArquivosDownload = Sessao::read('arArquivosDownload');
        $stLabelZip = 'ArquivosExportacao.zip';
        $stCaminho = CAM_FRAMEWORK.'tmp/';

        foreach ($arArquivosDownload as $arquivo) {
            $this->obArquivoZip->AdicionarArquivo($arquivo['stLink'],$arquivo['stNomeArquivo']);
        }

        $stNomeZip = $this->obArquivoZip->Show();
        $arArquivosDownload = array();
        $arArquivosDownload[0]['stNomeArquivo'] = $stLabelZip;
        $arArquivosDownload[0]['stLink'       ] = $stCaminho.$stNomeZip;
        // Manda array de arquivos para a sessao
        Sessao::write('arArquivosDownload',$arArquivosDownload);
    }
}
