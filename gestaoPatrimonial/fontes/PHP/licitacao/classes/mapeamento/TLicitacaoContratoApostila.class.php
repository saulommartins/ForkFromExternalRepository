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
    * Classe de mapeamento da tabela licitacao.contrato_apostila
    * Data de Criação: 15/02/2016

    * @author Analista:      Gelson W. Gonçalves  <gelson.goncalves@cnm.org.br>
    * @author Desenvolvedor: Carlos Adriano       <carlos.silva@cnm.org.br>

    * @package    URBEM
    * @subpackage Mapeamento

    $Id: TLicitacaoContratoApostila.class.php 64923 2016-04-13 17:45:44Z jean $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TLicitacaoContratoApostila extends Persistente {
  
    public function TLicitacaoContratoApostila() {
        parent::Persistente();

        $this->setTabela("licitacao.contrato_apostila");
        
        $this->setCampoCod('cod_apostila');
        $this->setComplementoChave('num_contrato, cod_entidade, exercicio');
        
        $this->AddCampo('cod_apostila'   , 'integer' , true , ''     , true  , false );
        $this->AddCampo('num_contrato'   , 'integer' , true , ''     , true  , true  );
        $this->AddCampo('cod_entidade'   , 'integer' , true , '4'    , true  , true  );
        $this->AddCampo('exercicio'      , 'varchar' , true , '4'    , true  , true  );
        $this->AddCampo('cod_tipo'       , 'integer' , true , ''     , false , false );
        $this->AddCampo('cod_alteracao'  , 'integer' , true , ''     , false , false );
        $this->AddCampo('descricao'      , 'text'    , true , ''     , false , false );
        $this->AddCampo('data_apostila'  , 'date'    , true , ''     , false , false );
        $this->AddCampo('valor_apostila' , 'integer' , true , '14,2' , false , false );
    }

    public function recuperaDadosContrato(&$rsRecordSet, $stFiltro='', $stOrdem='') {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaRecuperaDadosContrato().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);
        return $obErro;
    }

    public function montaRecuperaDadosContrato() {
        $stSql .= " SELECT 
                         num_contrato ,
                         contrato.exercicio ,
                         contrato.cod_entidade ,
                         cod_tipo_documento ,
                         cod_tipo_contrato ,
                         cod_documento ,
                         cgm_responsavel_juridico ,
                         cgm_contratado ,
                         TO_CHAR(dt_assinatura,'dd/mm/yyyy') AS dt_assinatura ,
                         TO_CHAR(vencimento,'dd/mm/yyyy') AS vencimento ,
                         valor_contratado ,
                         valor_garantia ,
                         TO_CHAR(inicio_execucao,'dd/mm/yyyy') AS inicio_execucao ,
                         TO_CHAR(fim_execucao,'dd/mm/yyyy') AS fim_execucao ,
                         num_orgao ,
                         num_unidade ,
                         numero_contrato ,
                         tipo_objeto ,
                         objeto ,
                         forma_fornecimento ,
                         forma_pagamento ,
                         cgm_signatario ,
                         prazo_execucao ,
                         multa_rescisoria ,
                         justificativa ,
                         razao ,
                         fundamentacao_legal,
                         tipo_contrato.descricao AS instrumento,
                         sw_cgm.nom_cgm,
                      
                         ( SELECT modalidade.descricao
                             FROM compras.modalidade
                       INNER JOIN licitacao.contrato_licitacao
                               ON contrato_licitacao.num_contrato = contrato.num_contrato
                              AND contrato_licitacao.exercicio = contrato.exercicio
                              AND contrato_licitacao.cod_entidade = contrato.cod_entidade
                              AND contrato_licitacao.cod_modalidade = modalidade.cod_modalidade
                         ) AS modalidade
                      
                          FROM licitacao.contrato
                    
                    INNER JOIN licitacao.tipo_contrato
                            ON tipo_contrato.cod_tipo = contrato.cod_tipo_contrato
                    
                    INNER JOIN orcamento.entidade
                            ON entidade.cod_entidade = contrato.cod_entidade
                           AND entidade.exercicio = contrato.exercicio
                    
                    INNER JOIN sw_cgm
                            ON sw_cgm.numcgm = entidade.numcgm ";
    
        return $stSql;
    }
    

    public function recuperaContratoApostila(&$rsRecordSet, $stFiltro='', $stOrdem='') {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaRecuperaContratoApostila().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);
        return $obErro;
    }

    public function montaRecuperaContratoApostila() {
        $stSql .= " SELECT 
                         contrato.num_contrato ,
                         contrato.exercicio ,
                         contrato.cod_entidade ,
                         contrato_apostila.cod_apostila ,
                         TO_CHAR(contrato_apostila.data_apostila, 'dd/mm/yyyy') AS data_apostila ,
                         cod_tipo_documento ,
                         cod_tipo_contrato ,
                         cod_documento ,
                         cgm_responsavel_juridico ,
                         cgm_contratado ,
                         TO_CHAR(dt_assinatura,'dd/mm/yyyy') AS dt_assinatura ,
                         TO_CHAR(vencimento,'dd/mm/yyyy') AS vencimento ,
                         valor_contratado ,
                         valor_garantia ,
                         TO_CHAR(inicio_execucao,'dd/mm/yyyy') AS inicio_execucao ,
                         TO_CHAR(fim_execucao,'dd/mm/yyyy') AS fim_execucao ,
                         num_orgao ,
                         num_unidade ,
                         numero_contrato ,
                         tipo_objeto ,
                         objeto ,
                         forma_fornecimento ,
                         forma_pagamento ,
                         cgm_signatario ,
                         prazo_execucao ,
                         multa_rescisoria ,
                         justificativa ,
                         razao ,
                         fundamentacao_legal,
                         tipo_contrato.descricao AS instrumento,
                         sw_cgm.nom_cgm,
                      
                         ( SELECT modalidade.descricao
                             FROM compras.modalidade
                       INNER JOIN licitacao.contrato_licitacao
                               ON contrato_licitacao.num_contrato = contrato.num_contrato
                              AND contrato_licitacao.exercicio = contrato.exercicio
                              AND contrato_licitacao.cod_entidade = contrato.cod_entidade
                              AND contrato_licitacao.cod_modalidade = modalidade.cod_modalidade
                         ) AS modalidade
                      
                          FROM licitacao.contrato
                    
                    INNER JOIN licitacao.tipo_contrato
                            ON tipo_contrato.cod_tipo = contrato.cod_tipo_contrato
                            
                    INNER JOIN licitacao.contrato_apostila
                            ON contrato_apostila.num_contrato = contrato.num_contrato
                           AND contrato_apostila.exercicio    = contrato.exercicio
                           AND contrato_apostila.cod_entidade = contrato.cod_entidade
                    
                    INNER JOIN orcamento.entidade
                            ON entidade.cod_entidade = contrato.cod_entidade
                           AND entidade.exercicio = contrato.exercicio
                    
                    INNER JOIN sw_cgm
                            ON sw_cgm.numcgm = entidade.numcgm ";
    
        return $stSql;
    }
}