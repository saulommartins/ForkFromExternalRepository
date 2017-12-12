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
    * Classe de mapeamento da tabela licitacao.participante
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Jean da Silva

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCEMGAberturaLicitacao.class.php 64106 2015-12-02 19:13:45Z michel $

    * Casos de uso: uc-03.05.18
            uc-03.05.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEMGAberturaLicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

    public function recuperaDetalhamento10(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento10", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaDetalhamento10()
    {
        $stSql = "
          SELECT 10 AS tipo_registro
               , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao_resp
               , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
               , licitacao.exercicio_licitacao
               , licitacao.num_processo_licitatorio
               , CASE licitacao.cod_modalidade
                      WHEN 4 THEN 7
                      WHEN 5 THEN 4
                      WHEN 6 THEN 5
                      WHEN 7 THEN 6
                      ELSE licitacao.cod_modalidade
                  END AS cod_modalidade_licitacao
               , licitacao.cod_modalidade AS num_modalidade
               , CASE licitacao.cod_modalidade
                      WHEN 11 THEN 2
                      WHEN 9  THEN 2
                      WHEN 10 THEN 3
                      ELSE 1
                  END AS natureza_procedimento
               , TO_CHAR(licitacao.timestamp,'ddmmyyyy') AS dt_abertura
               --, TO_CHAR(edital.dt_aprovacao_juridico,'ddmmyyyy') AS dt_edital_convite
               , TO_CHAR(licitacao.timestamp,'ddmmyyyy') AS dt_edital_convite
               , diario_oficial.dt_publicacao_edital
               , publicacao_edital_veiculo1.dt_publicacao_edital_veiculo1
               , publicacao_edital_veiculo1.veiculo1_publicacao
               , publicacao_edital_veiculo2.dt_publicacao_edital_veiculo2
               , publicacao_edital_veiculo2.veiculo2_publicacao
               --, TO_CHAR(edital.dt_entrega_propostas,'ddmmyyyy') AS dt_recebimento_doc
               , TO_CHAR(licitacao.timestamp,'ddmmyyyy') AS dt_recebimento_doc
               , licitacao.cod_criterio AS tipo_licitacao
               , CASE licitacao.cod_tipo_objeto
                      WHEN 2 THEN 1
                      WHEN 1 THEN 2
                      WHEN 3 THEN 4
                      WHEN 4 THEN 6
                  END AS natureza_objeto
               , objeto.descricao AS objeto
               , CASE WHEN licitacao.cod_tipo_objeto = 2
                      THEN licitacao.cod_regime
                      ELSE NULL
                  END AS regime_execucao_obras
               , CASE WHEN (licitacao.cod_modalidade = 1)
                      THEN convidados.nrm_convidado
                  END AS num_convidado
               , LPAD('',250,'') AS clausula_prorrogacao
               , 1 AS undade_medida_prazo_execucao
               , (DATE(contrato.fim_execucao)-DATE(contrato.inicio_execucao)) AS prazo_execucao
               , regexp_replace(sem_acentos(edital.condicoes_pagamento),'[º|°|%|§]', '', 'gi') AS forma_pagamento
               , LPAD('',80,'') AS citerio_aceitabilidade
               , 2 AS desconto_tabela
               , CASE WHEN mapa.cod_tipo_licitacao = 2
                      THEN 1
                      ELSE 2
                  END AS processo_lote
               , 1 AS criterio_desempate
               , 2 AS destinacao_exclusiva
               , 2 AS subcontratacao
               , 2 AS limite_contratacao
            FROM licitacao.homologacao

       LEFT JOIN licitacao.homologacao_anulada
              ON homologacao.num_homologacao     = homologacao_anulada.num_homologacao
             AND homologacao.cod_licitacao       = homologacao_anulada.cod_licitacao
             AND homologacao.cod_modalidade      = homologacao_anulada.cod_modalidade
             AND homologacao.cod_entidade        = homologacao_anulada.cod_entidade
             AND homologacao.num_adjudicacao     = homologacao_anulada.num_adjudicacao
             AND homologacao.exercicio_licitacao = homologacao_anulada.exercicio_licitacao
             AND homologacao.lote                = homologacao_anulada.lote
             AND homologacao.cod_cotacao         = homologacao_anulada.cod_cotacao
             AND homologacao.cod_item            = homologacao_anulada.cod_item
             AND homologacao.exercicio_cotacao   = homologacao_anulada.exercicio_cotacao
             AND homologacao.cgm_fornecedor      = homologacao_anulada.cgm_fornecedor

      INNER JOIN licitacao.adjudicacao
              ON adjudicacao.num_adjudicacao     = homologacao.num_adjudicacao
             AND adjudicacao.cod_entidade        = homologacao.cod_entidade
             AND adjudicacao.cod_modalidade      = homologacao.cod_modalidade
             AND adjudicacao.cod_licitacao       = homologacao.cod_licitacao
             AND adjudicacao.exercicio_licitacao = homologacao.exercicio_licitacao
             AND adjudicacao.cod_item            = homologacao.cod_item
             AND adjudicacao.cod_cotacao         = homologacao.cod_cotacao
             AND adjudicacao.lote                = homologacao.lote
             AND adjudicacao.exercicio_cotacao   = homologacao.exercicio_cotacao
             AND adjudicacao.cgm_fornecedor      = homologacao.cgm_fornecedor

      INNER JOIN licitacao.cotacao_licitacao
              ON cotacao_licitacao.cod_modalidade      = adjudicacao.cod_modalidade
             AND cotacao_licitacao.cod_licitacao       = adjudicacao.cod_licitacao
             AND cotacao_licitacao.cod_entidade        = adjudicacao.cod_entidade
             AND cotacao_licitacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
             AND cotacao_licitacao.lote                = adjudicacao.lote
             AND cotacao_licitacao.cod_cotacao         = adjudicacao.cod_cotacao
             AND cotacao_licitacao.cod_item            = adjudicacao.cod_item
             AND cotacao_licitacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
             AND cotacao_licitacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor

      INNER JOIN (SELECT licitacao.*
                       , (SELECT exercicio
                            FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
                          VALUES ( cod_licitacao       INTEGER
                                 , cod_modalidade      INTEGER
                                 , cod_entidade        INTEGER
                                 , exercicio           CHAR(4)
                                 , exercicio_licitacao VARCHAR
                                 , num_licitacao       TEXT ) 
                           WHERE cod_entidade   = licitacao.cod_entidade
                             AND cod_licitacao  = licitacao.cod_licitacao
                             AND cod_modalidade = licitacao.cod_modalidade
                             AND exercicio      = licitacao.exercicio
                         ) AS exercicio_licitacao
                       , (SELECT num_licitacao
                            FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
                          VALUES ( cod_licitacao       INTEGER
                                 , cod_modalidade      INTEGER
                                 , cod_entidade        INTEGER
                                 , exercicio           CHAR(4)
                                 , exercicio_licitacao VARCHAR
                                 , num_licitacao       TEXT ) 
                           WHERE cod_entidade   = licitacao.cod_entidade
                             AND cod_licitacao  = licitacao.cod_licitacao
                             AND cod_modalidade = licitacao.cod_modalidade
                             AND exercicio      = licitacao.exercicio
                         ) AS num_processo_licitatorio
                    FROM licitacao.licitacao
                 ) AS licitacao
              ON licitacao.cod_licitacao  = cotacao_licitacao.cod_licitacao
             AND licitacao.cod_modalidade = cotacao_licitacao.cod_modalidade
             AND licitacao.cod_entidade   = cotacao_licitacao.cod_entidade
             AND licitacao.exercicio      = cotacao_licitacao.exercicio_licitacao

       LEFT JOIN licitacao.licitacao_anulada
              ON licitacao.cod_licitacao  = licitacao_anulada.cod_licitacao
             AND licitacao.cod_modalidade = licitacao_anulada.cod_modalidade
             AND licitacao.cod_entidade   = licitacao_anulada.cod_entidade
             AND licitacao.exercicio      = licitacao_anulada.exercicio

      INNER JOIN licitacao.edital
              ON edital.cod_licitacao       = licitacao.cod_licitacao
             AND edital.cod_modalidade      = licitacao.cod_modalidade
             AND edital.cod_entidade        = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio

       LEFT JOIN licitacao.edital_anulado
              ON edital.num_edital = edital_anulado.num_edital
             AND edital.exercicio  = edital_anulado.exercicio

       LEFT JOIN licitacao.contrato_licitacao
              ON licitacao.cod_licitacao  = contrato_licitacao.cod_licitacao
             AND licitacao.cod_modalidade = contrato_licitacao.cod_modalidade
             AND licitacao.cod_entidade   = contrato_licitacao.cod_entidade
             AND licitacao.exercicio      = contrato_licitacao.exercicio_licitacao

       LEFT JOIN licitacao.contrato
              ON contrato_licitacao.num_contrato = contrato.num_contrato
             AND contrato_licitacao.cod_entidade = contrato.cod_entidade
             AND contrato_licitacao.exercicio    = contrato.exercicio

      INNER JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto

      INNER JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa  = licitacao.cod_mapa

      INNER JOIN compras.mapa_cotacao
              ON mapa_cotacao.exercicio_mapa = mapa.exercicio
             AND mapa_cotacao.cod_mapa       = mapa.cod_mapa  

      INNER JOIN compras.cotacao
              ON cotacao.exercicio   = mapa_cotacao.exercicio_cotacao
             AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao

      INNER JOIN compras.julgamento
              ON julgamento.exercicio   = cotacao.exercicio
             AND julgamento.cod_cotacao = cotacao.cod_cotacao

      INNER JOIN compras.julgamento_item
              ON julgamento_item.exercicio   = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             AND julgamento_item.cgm_fornecedor = homologacao.cgm_fornecedor
             AND julgamento_item.lote           = homologacao.lote
             AND julgamento_item.cod_item       = homologacao.cod_item
             AND julgamento_item.ordem = 1

      INNER JOIN compras.cotacao_fornecedor_item
              ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
             AND cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
             AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
             AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
             AND cotacao_fornecedor_item.lote           = julgamento_item.lote

      INNER JOIN compras.fornecedor
              ON fornecedor.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
             AND fornecedor.ativo IS TRUE

       LEFT JOIN (SELECT TO_CHAR(publicacao_edital.data_publicacao,'ddmmyyyy') AS dt_publicacao_edital
                       , publicacao_edital.num_edital
                       , publicacao_edital.exercicio
                    FROM licitacao.publicacao_edital
              INNER JOIN licitacao.veiculos_publicidade
                      ON veiculos_publicidade.numcgm = publicacao_edital.numcgm
                   WHERE veiculos_publicidade.cod_tipo_veiculos_publicidade = 5
                 ) AS diario_oficial
              ON diario_oficial.num_edital = edital.num_edital
             AND diario_oficial.exercicio  = edital.exercicio

       LEFT JOIN (SELECT row_number() OVER(ORDER BY tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade ASC) AS pos
                       , TO_CHAR(publicacao_edital.data_publicacao,'ddmmyyyy') AS dt_publicacao_edital_veiculo1
                       , tipo_veiculos_publicidade.descricao AS veiculo1_publicacao
                       , publicacao_edital.num_edital
                       , publicacao_edital.exercicio
                    FROM licitacao.publicacao_edital
              INNER JOIN licitacao.veiculos_publicidade
                      ON veiculos_publicidade.numcgm = publicacao_edital.numcgm
              INNER JOIN licitacao.tipo_veiculos_publicidade
                      ON tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = veiculos_publicidade.cod_tipo_veiculos_publicidade
                   WHERE tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade <> 5
                 ) AS publicacao_edital_veiculo1
              ON publicacao_edital_veiculo1.num_edital = edital.num_edital
             AND publicacao_edital_veiculo1.exercicio  = edital.exercicio
             AND publicacao_edital_veiculo1.pos = 1

       LEFT JOIN (SELECT row_number() OVER(ORDER BY tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade ASC) AS pos
                       , TO_CHAR(publicacao_edital.data_publicacao,'ddmmyyyy') AS dt_publicacao_edital_veiculo2
                       , tipo_veiculos_publicidade.descricao AS veiculo2_publicacao
                       , publicacao_edital.num_edital
                       , publicacao_edital.exercicio
                    FROM licitacao.publicacao_edital
              INNER JOIN licitacao.veiculos_publicidade
                      ON veiculos_publicidade.numcgm = publicacao_edital.numcgm
              INNER JOIN licitacao.tipo_veiculos_publicidade
                      ON tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = veiculos_publicidade.cod_tipo_veiculos_publicidade
                   WHERE tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade <> 5
                 ) AS publicacao_edital_veiculo2
              ON publicacao_edital_veiculo2.num_edital = edital.num_edital
             AND publicacao_edital_veiculo2.exercicio  = edital.exercicio
             AND publicacao_edital_veiculo2.pos = 2

       LEFT JOIN (SELECT COUNT (numcgm_representante) AS nrm_convidado
                       , participante.cod_licitacao
                       , participante.cod_modalidade 
                       , participante.cod_entidade 
                       , participante.exercicio 
                    FROM licitacao.participante
                GROUP BY cod_licitacao,cod_modalidade,cod_entidade,exercicio
                 ) as convidados
              ON convidados.cod_licitacao  = licitacao.cod_licitacao
             AND convidados.cod_modalidade = licitacao.cod_modalidade
             AND convidados.cod_entidade   = licitacao.cod_entidade
             AND convidados.exercicio      = licitacao.exercicio				  

      INNER JOIN administracao.configuracao_entidade
              ON configuracao_entidade.parametro    = 'tcemg_codigo_orgao_entidade_sicom'
             AND configuracao_entidade.cod_modulo   = 55
             AND configuracao_entidade.exercicio    = licitacao.exercicio
             AND configuracao_entidade.cod_entidade = licitacao.cod_entidade

           WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy')
                                                                                        AND last_day(TO_DATE('".$this->getDado('exercicio')."'||'-'||'".$this->getDado('mes')."'||'-'||'01', 'yyyy-mm-dd'))
             AND homologacao_anulada.num_homologacao IS NULL
             AND licitacao_anulada.cod_licitacao IS NULL
             AND edital_anulado.num_edital IS NULL
             AND homologacao.homologado IS TRUE
             AND licitacao.cod_modalidade NOT IN (8,9)
             AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")

        GROUP BY tipo_registro
               , cod_orgao_resp
               , cod_unidade_resp
               , licitacao.exercicio_licitacao
               , licitacao.num_processo_licitatorio
               , cod_modalidade_licitacao
               , num_modalidade
               , natureza_procedimento
               , dt_abertura
               , dt_edital_convite
               , dt_publicacao_edital
               , dt_publicacao_edital_veiculo1
               , veiculo1_publicacao
               , dt_publicacao_edital_veiculo2
               , veiculo2_publicacao
               , dt_recebimento_doc
               , tipo_licitacao
               , natureza_objeto
               , objeto
               , regime_execucao_obras
               , num_convidado
               , clausula_prorrogacao
               , undade_medida_prazo_execucao
               , (DATE(contrato.fim_execucao)-DATE(contrato.inicio_execucao))
               , forma_pagamento
               , citerio_aceitabilidade
               , desconto_tabela
               , processo_lote
               , criterio_desempate
               , destinacao_exclusiva
               , subcontratacao
               , limite_contratacao
               , objeto.descricao
               , edital.condicoes_pagamento
        ";
        return $stSql;
    }
    
    public function recuperaDetalhamento11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamento11()
    {
        $stSql = "
          SELECT 11 AS tipo_registro
               , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao_resp
               , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
               , config_licitacao.exercicio_licitacao
               , config_licitacao.num_licitacao AS num_processo_licitatorio
               , homologacao.lote AS num_lote
               , homologacao.lote AS desc_lote

            FROM licitacao.licitacao

      INNER JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo

      INNER JOIN licitacao.criterio_julgamento
              ON criterio_julgamento.cod_criterio = licitacao.cod_criterio

      INNER JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio

       LEFT JOIN licitacao.edital_anulado
              ON edital.num_edital=edital_anulado.num_edital
             AND edital.exercicio =edital_anulado.exercicio

      INNER JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto

      INNER JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto

      INNER JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade

      INNER JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             AND mapa.cod_tipo_licitacao = 2

      INNER JOIN compras.tipo_licitacao
              ON tipo_licitacao.cod_tipo_licitacao = licitacao.cod_tipo_licitacao

      INNER JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa

      INNER JOIN compras.mapa_cotacao
              ON mapa_cotacao.exercicio_mapa = mapa.exercicio
             AND mapa_cotacao.cod_mapa = mapa.cod_mapa

      INNER JOIN compras.cotacao
              ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
             AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao

      INNER JOIN compras.julgamento
              ON julgamento.exercicio = cotacao.exercicio
             AND julgamento.cod_cotacao = cotacao.cod_cotacao

      INNER JOIN compras.julgamento_item
              ON julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao

      INNER JOIN compras.cotacao_fornecedor_item
              ON cotacao_fornecedor_item.exercicio = julgamento_item.exercicio
             AND cotacao_fornecedor_item.cod_cotacao = julgamento_item.cod_cotacao
             AND cotacao_fornecedor_item.cod_item = julgamento_item.cod_item
             AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
             AND cotacao_fornecedor_item.lote = julgamento_item.lote

      INNER JOIN compras.fornecedor
              ON fornecedor.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor

      INNER JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote

       LEFT JOIN licitacao.homologacao_anulada
              ON homologacao.num_homologacao     = homologacao_anulada.num_homologacao
             AND homologacao.cod_licitacao       = homologacao_anulada.cod_licitacao
             AND homologacao.cod_modalidade      = homologacao_anulada.cod_modalidade
             AND homologacao.cod_entidade        = homologacao_anulada.cod_entidade
             AND homologacao.num_adjudicacao     = homologacao_anulada.num_adjudicacao
             AND homologacao.exercicio_licitacao = homologacao_anulada.exercicio_licitacao
             AND homologacao.lote                = homologacao_anulada.lote
             AND homologacao.cod_cotacao         = homologacao_anulada.cod_cotacao
             AND homologacao.cod_item            = homologacao_anulada.cod_item
             AND homologacao.exercicio_cotacao   = homologacao_anulada.exercicio_cotacao
             AND homologacao.cgm_fornecedor      = homologacao_anulada.cgm_fornecedor

      INNER JOIN compras.solicitacao_homologada
              ON solicitacao_homologada.exercicio=mapa_solicitacao.exercicio_solicitacao
             AND solicitacao_homologada.cod_entidade=mapa_solicitacao.cod_entidade
             AND solicitacao_homologada.cod_solicitacao=mapa_solicitacao.cod_solicitacao

      LEFT JOIN compras.solicitacao_homologada_reserva
              ON solicitacao_homologada_reserva.exercicio=solicitacao_homologada.exercicio
             AND solicitacao_homologada_reserva.cod_entidade=solicitacao_homologada.cod_entidade
             AND solicitacao_homologada_reserva.cod_solicitacao=solicitacao_homologada.cod_solicitacao

      LEFT JOIN orcamento.despesa
              ON despesa.exercicio = solicitacao_homologada_reserva.exercicio
             AND despesa.cod_despesa = solicitacao_homologada_reserva.cod_despesa

      INNER JOIN administracao.configuracao_entidade
              ON configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
             AND configuracao_entidade.cod_modulo = 55
             AND configuracao_entidade.exercicio = licitacao.exercicio
             AND configuracao_entidade.cod_entidade = licitacao.cod_entidade

      INNER JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico

      INNER JOIN ( SELECT num_documento, numcgm, tipo_documento
                     FROM (
                            SELECT cpf AS num_documento, numcgm, 1 AS tipo_documento
                            FROM sw_cgm_pessoa_fisica
                            UNION
                            SELECT cnpj AS num_documento, numcgm, 2 AS tipo_documento
                            FROM sw_cgm_pessoa_juridica
                         ) AS tabela
                    GROUP BY numcgm, num_documento, tipo_documento
                 ) AS documento_pessoa
              ON documento_pessoa.numcgm = responsavel.numcgm

       LEFT JOIN licitacao.contrato_licitacao
              ON contrato_licitacao.cod_licitacao=licitacao.cod_licitacao
             AND contrato_licitacao.cod_modalidade=licitacao.cod_modalidade
             AND contrato_licitacao.cod_entidade=licitacao.cod_entidade
             AND contrato_licitacao.exercicio_licitacao=licitacao.exercicio

       LEFT JOIN licitacao.contrato
              ON contrato.num_contrato=contrato_licitacao.num_contrato
             AND contrato.cod_entidade=contrato_licitacao.cod_entidade
             AND contrato.exercicio=contrato_licitacao.exercicio

      INNER JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 ) AS config_licitacao
              ON config_licitacao.cod_entidade = licitacao.cod_entidade
             AND config_licitacao.cod_licitacao = licitacao.cod_licitacao
             AND config_licitacao.cod_modalidade = licitacao.cod_modalidade
             AND config_licitacao.exercicio = licitacao.exercicio

           WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
             AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
             AND homologacao_anulada.num_homologacao IS NULL
             AND edital_anulado.num_edital IS NULL
             AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
             AND licitacao.cod_modalidade NOT IN (8,9)
             AND NOT EXISTS( SELECT 1
                               FROM licitacao.licitacao_anulada
                              WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                                AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                AND licitacao_anulada.exercicio = licitacao.exercicio
                           )
        GROUP BY licitacao.cod_licitacao, licitacao.cod_modalidade, tipo_registro, cod_orgao_resp, cod_unidade_resp, licitacao.exercicio, num_processo_licitatorio, num_lote, config_licitacao.exercicio_licitacao, config_licitacao.num_licitacao

        ORDER BY licitacao.cod_licitacao, licitacao.cod_modalidade, num_processo_licitatorio, cod_unidade_resp
        ";

        return $stSql;
    }

    public function recuperaDetalhamento12(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento12",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamento12()
    {
        $stSql = "
          SELECT 12 AS tipo_registro
               , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao_resp
               , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
               , licitacao.exercicio_licitacao
               , licitacao.num_processo_licitatorio
               , homologacao.cod_item AS cod_item
            FROM licitacao.homologacao

       LEFT JOIN licitacao.homologacao_anulada
              ON homologacao.num_homologacao     = homologacao_anulada.num_homologacao
             AND homologacao.cod_licitacao       = homologacao_anulada.cod_licitacao
             AND homologacao.cod_modalidade      = homologacao_anulada.cod_modalidade
             AND homologacao.cod_entidade        = homologacao_anulada.cod_entidade
             AND homologacao.num_adjudicacao     = homologacao_anulada.num_adjudicacao
             AND homologacao.exercicio_licitacao = homologacao_anulada.exercicio_licitacao
             AND homologacao.lote                = homologacao_anulada.lote
             AND homologacao.cod_cotacao         = homologacao_anulada.cod_cotacao
             AND homologacao.cod_item            = homologacao_anulada.cod_item
             AND homologacao.exercicio_cotacao   = homologacao_anulada.exercicio_cotacao
             AND homologacao.cgm_fornecedor      = homologacao_anulada.cgm_fornecedor

      INNER JOIN licitacao.adjudicacao
              ON adjudicacao.num_adjudicacao     = homologacao.num_adjudicacao
             AND adjudicacao.cod_entidade        = homologacao.cod_entidade
             AND adjudicacao.cod_modalidade      = homologacao.cod_modalidade
             AND adjudicacao.cod_licitacao       = homologacao.cod_licitacao
             AND adjudicacao.exercicio_licitacao = homologacao.exercicio_licitacao
             AND adjudicacao.cod_item            = homologacao.cod_item
             AND adjudicacao.cod_cotacao         = homologacao.cod_cotacao
             AND adjudicacao.lote                = homologacao.lote
             AND adjudicacao.exercicio_cotacao   = homologacao.exercicio_cotacao
             AND adjudicacao.cgm_fornecedor      = homologacao.cgm_fornecedor

      INNER JOIN licitacao.cotacao_licitacao
              ON cotacao_licitacao.cod_modalidade      = adjudicacao.cod_modalidade
             AND cotacao_licitacao.cod_licitacao       = adjudicacao.cod_licitacao
             AND cotacao_licitacao.cod_entidade        = adjudicacao.cod_entidade
             AND cotacao_licitacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
             AND cotacao_licitacao.lote                = adjudicacao.lote
             AND cotacao_licitacao.cod_cotacao         = adjudicacao.cod_cotacao
             AND cotacao_licitacao.cod_item            = adjudicacao.cod_item
             AND cotacao_licitacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
             AND cotacao_licitacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor

      INNER JOIN (SELECT licitacao.*
                       , (SELECT exercicio
                            FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
                          VALUES ( cod_licitacao       INTEGER
                                 , cod_modalidade      INTEGER
                                 , cod_entidade        INTEGER
                                 , exercicio           CHAR(4)
                                 , exercicio_licitacao VARCHAR
                                 , num_licitacao       TEXT ) 
                           WHERE cod_entidade   = licitacao.cod_entidade
                             AND cod_licitacao  = licitacao.cod_licitacao
                             AND cod_modalidade = licitacao.cod_modalidade
                             AND exercicio      = licitacao.exercicio
                         ) AS exercicio_licitacao
                       , (SELECT num_licitacao
                            FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
                          VALUES ( cod_licitacao       INTEGER
                                 , cod_modalidade      INTEGER
                                 , cod_entidade        INTEGER
                                 , exercicio           CHAR(4)
                                 , exercicio_licitacao VARCHAR
                                 , num_licitacao       TEXT ) 
                           WHERE cod_entidade   = licitacao.cod_entidade
                             AND cod_licitacao  = licitacao.cod_licitacao
                             AND cod_modalidade = licitacao.cod_modalidade
                             AND exercicio      = licitacao.exercicio
                         ) AS num_processo_licitatorio
                    FROM licitacao.licitacao
                 ) AS licitacao
              ON licitacao.cod_licitacao  = cotacao_licitacao.cod_licitacao
             AND licitacao.cod_modalidade = cotacao_licitacao.cod_modalidade
             AND licitacao.cod_entidade   = cotacao_licitacao.cod_entidade
             AND licitacao.exercicio      = cotacao_licitacao.exercicio_licitacao

       LEFT JOIN licitacao.licitacao_anulada
              ON licitacao.cod_licitacao  = licitacao_anulada.cod_licitacao
             AND licitacao.cod_modalidade = licitacao_anulada.cod_modalidade
             AND licitacao.cod_entidade   = licitacao_anulada.cod_entidade
             AND licitacao.exercicio      = licitacao_anulada.exercicio

      INNER JOIN licitacao.edital
              ON edital.cod_licitacao       = licitacao.cod_licitacao
             AND edital.cod_modalidade      = licitacao.cod_modalidade
             AND edital.cod_entidade        = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio

       LEFT JOIN licitacao.edital_anulado
              ON edital.num_edital=edital_anulado.num_edital
             AND edital.exercicio =edital_anulado.exercicio

      INNER JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa  = licitacao.cod_mapa

      INNER JOIN administracao.configuracao_entidade
              ON configuracao_entidade.parametro    = 'tcemg_codigo_orgao_entidade_sicom'
             AND configuracao_entidade.cod_modulo   = 55
             AND configuracao_entidade.exercicio    = licitacao.exercicio
             AND configuracao_entidade.cod_entidade = licitacao.cod_entidade

           WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy')
                                                                                        AND last_day(TO_DATE('".$this->getDado('exercicio')."'||'-'||'".$this->getDado('mes')."'||'-'||'01', 'yyyy-mm-dd'))
             AND homologacao_anulada.num_homologacao IS NULL
             AND homologacao.homologado IS TRUE
             AND licitacao_anulada.cod_licitacao IS NULL
             AND edital_anulado.num_edital IS NULL
             AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
             AND licitacao.cod_modalidade NOT IN (8,9)

        GROUP BY tipo_registro
               , cod_orgao_resp
               , cod_unidade_resp
               , licitacao.exercicio_licitacao
               , licitacao.num_processo_licitatorio
               , homologacao.cod_item ";

        return $stSql;
    }

    public function recuperaDetalhamento13(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento13",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamento13()
    {
        $stSql = "
          SELECT 13 AS tipo_registro
               , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao_resp
               , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
               , licitacao.exercicio_licitacao
               , licitacao.num_processo_licitatorio
               , homologacao.lote AS num_lote
               , homologacao.cod_item AS cod_item
            FROM licitacao.homologacao

       LEFT JOIN licitacao.homologacao_anulada
              ON homologacao.num_homologacao     = homologacao_anulada.num_homologacao
             AND homologacao.cod_licitacao       = homologacao_anulada.cod_licitacao
             AND homologacao.cod_modalidade      = homologacao_anulada.cod_modalidade
             AND homologacao.cod_entidade        = homologacao_anulada.cod_entidade
             AND homologacao.num_adjudicacao     = homologacao_anulada.num_adjudicacao
             AND homologacao.exercicio_licitacao = homologacao_anulada.exercicio_licitacao
             AND homologacao.lote                = homologacao_anulada.lote
             AND homologacao.cod_cotacao         = homologacao_anulada.cod_cotacao
             AND homologacao.cod_item            = homologacao_anulada.cod_item
             AND homologacao.exercicio_cotacao   = homologacao_anulada.exercicio_cotacao
             AND homologacao.cgm_fornecedor      = homologacao_anulada.cgm_fornecedor

      INNER JOIN licitacao.adjudicacao
              ON adjudicacao.num_adjudicacao     = homologacao.num_adjudicacao
             AND adjudicacao.cod_entidade        = homologacao.cod_entidade
             AND adjudicacao.cod_modalidade      = homologacao.cod_modalidade
             AND adjudicacao.cod_licitacao       = homologacao.cod_licitacao
             AND adjudicacao.exercicio_licitacao = homologacao.exercicio_licitacao
             AND adjudicacao.cod_item            = homologacao.cod_item
             AND adjudicacao.cod_cotacao         = homologacao.cod_cotacao
             AND adjudicacao.lote                = homologacao.lote
             AND adjudicacao.exercicio_cotacao   = homologacao.exercicio_cotacao
             AND adjudicacao.cgm_fornecedor      = homologacao.cgm_fornecedor

      INNER JOIN licitacao.cotacao_licitacao
              ON cotacao_licitacao.cod_modalidade      = adjudicacao.cod_modalidade
             AND cotacao_licitacao.cod_licitacao       = adjudicacao.cod_licitacao
             AND cotacao_licitacao.cod_entidade        = adjudicacao.cod_entidade
             AND cotacao_licitacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
             AND cotacao_licitacao.lote                = adjudicacao.lote
             AND cotacao_licitacao.cod_cotacao         = adjudicacao.cod_cotacao
             AND cotacao_licitacao.cod_item            = adjudicacao.cod_item
             AND cotacao_licitacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
             AND cotacao_licitacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor

      INNER JOIN (SELECT licitacao.*
                       , (SELECT exercicio
                            FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
                          VALUES ( cod_licitacao       INTEGER
                                 , cod_modalidade      INTEGER
                                 , cod_entidade        INTEGER
                                 , exercicio           CHAR(4)
                                 , exercicio_licitacao VARCHAR
                                 , num_licitacao       TEXT ) 
                           WHERE cod_entidade   = licitacao.cod_entidade
                             AND cod_licitacao  = licitacao.cod_licitacao
                             AND cod_modalidade = licitacao.cod_modalidade
                             AND exercicio      = licitacao.exercicio
                         ) AS exercicio_licitacao
                       , (SELECT num_licitacao
                            FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
                          VALUES ( cod_licitacao       INTEGER
                                 , cod_modalidade      INTEGER
                                 , cod_entidade        INTEGER
                                 , exercicio           CHAR(4)
                                 , exercicio_licitacao VARCHAR
                                 , num_licitacao       TEXT ) 
                           WHERE cod_entidade   = licitacao.cod_entidade
                             AND cod_licitacao  = licitacao.cod_licitacao
                             AND cod_modalidade = licitacao.cod_modalidade
                             AND exercicio      = licitacao.exercicio
                         ) AS num_processo_licitatorio
                    FROM licitacao.licitacao
                 ) AS licitacao
              ON licitacao.cod_licitacao  = cotacao_licitacao.cod_licitacao
             AND licitacao.cod_modalidade = cotacao_licitacao.cod_modalidade
             AND licitacao.cod_entidade   = cotacao_licitacao.cod_entidade
             AND licitacao.exercicio      = cotacao_licitacao.exercicio_licitacao

       LEFT JOIN licitacao.licitacao_anulada
              ON licitacao.cod_licitacao  = licitacao_anulada.cod_licitacao
             AND licitacao.cod_modalidade = licitacao_anulada.cod_modalidade
             AND licitacao.cod_entidade   = licitacao_anulada.cod_entidade
             AND licitacao.exercicio      = licitacao_anulada.exercicio

      INNER JOIN licitacao.edital
              ON edital.cod_licitacao       = licitacao.cod_licitacao
             AND edital.cod_modalidade      = licitacao.cod_modalidade
             AND edital.cod_entidade        = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio

       LEFT JOIN licitacao.edital_anulado
              ON edital.num_edital=edital_anulado.num_edital
             AND edital.exercicio =edital_anulado.exercicio

      INNER JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa  = licitacao.cod_mapa
             AND mapa.cod_tipo_licitacao = 2

      INNER JOIN compras.mapa_item
              ON mapa_item.exercicio             = licitacao.exercicio_mapa
             AND mapa_item.cod_mapa              = licitacao.cod_mapa
             AND mapa_item.cod_entidade          = homologacao.cod_entidade
             AND mapa_item.cod_item              = homologacao.cod_item

      INNER JOIN administracao.configuracao_entidade
              ON configuracao_entidade.parametro    = 'tcemg_codigo_orgao_entidade_sicom'
             AND configuracao_entidade.cod_modulo   = 55
             AND configuracao_entidade.exercicio    = licitacao.exercicio
             AND configuracao_entidade.cod_entidade = licitacao.cod_entidade

           WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy')
                                                                                        AND last_day(TO_DATE('".$this->getDado('exercicio')."'||'-'||'".$this->getDado('mes')."'||'-'||'01', 'yyyy-mm-dd'))
             AND homologacao_anulada.num_homologacao IS NULL
             AND homologacao.homologado IS TRUE
             AND licitacao_anulada.cod_licitacao IS NULL
             AND edital_anulado.num_edital IS NULL
             AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
             AND licitacao.cod_modalidade NOT IN (8,9)

        GROUP BY tipo_registro
               , cod_orgao_resp
               , cod_unidade_resp
               , licitacao.exercicio_licitacao
               , licitacao.num_processo_licitatorio
               , num_lote
               , homologacao.cod_item ";

        return $stSql;
    }

    public function recuperaDetalhamento14(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento14",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamento14()
    {
        $stSql = "
          SELECT 14 AS tipo_registro
               , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao_resp
               , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
               , licitacao.exercicio_licitacao
               , licitacao.num_processo_licitatorio
               , CASE WHEN mapa.cod_tipo_licitacao = 2
                      THEN homologacao.lote
                      ELSE NULL
                 END AS num_lote
               , homologacao.cod_item AS cod_item
               --, TO_CHAR(cotacao.timestamp,'ddmmyyyy') AS dt_cotacao
               , TO_CHAR(licitacao.timestamp,'ddmmyyyy') AS dt_cotacao
               , CASE WHEN licitacao.cod_tipo_objeto = 4
                      THEN ('0,0000')
                      ELSE ((cotacao_fornecedor_item.vl_cotacao/SUM(((mapa_item.quantidade)-COALESCE(mapa_item_anulacao.quantidade, 0.0000))))::NUMERIC(14,4))::VARCHAR
                 END AS vl_cot_precos_unitario
               , SUM((mapa_item.quantidade)-COALESCE(mapa_item_anulacao.quantidade, 0.0000)) AS quantidade
               , '0,00' AS vl_min_alien_bens
            FROM licitacao.homologacao

       LEFT JOIN licitacao.homologacao_anulada
              ON homologacao.num_homologacao     = homologacao_anulada.num_homologacao
             AND homologacao.cod_licitacao       = homologacao_anulada.cod_licitacao
             AND homologacao.cod_modalidade      = homologacao_anulada.cod_modalidade
             AND homologacao.cod_entidade        = homologacao_anulada.cod_entidade
             AND homologacao.num_adjudicacao     = homologacao_anulada.num_adjudicacao
             AND homologacao.exercicio_licitacao = homologacao_anulada.exercicio_licitacao
             AND homologacao.lote                = homologacao_anulada.lote
             AND homologacao.cod_cotacao         = homologacao_anulada.cod_cotacao
             AND homologacao.cod_item            = homologacao_anulada.cod_item
             AND homologacao.exercicio_cotacao   = homologacao_anulada.exercicio_cotacao
             AND homologacao.cgm_fornecedor      = homologacao_anulada.cgm_fornecedor

      INNER JOIN licitacao.adjudicacao
              ON adjudicacao.num_adjudicacao     = homologacao.num_adjudicacao
             AND adjudicacao.cod_entidade        = homologacao.cod_entidade
             AND adjudicacao.cod_modalidade      = homologacao.cod_modalidade
             AND adjudicacao.cod_licitacao       = homologacao.cod_licitacao
             AND adjudicacao.exercicio_licitacao = homologacao.exercicio_licitacao
             AND adjudicacao.cod_item            = homologacao.cod_item
             AND adjudicacao.cod_cotacao         = homologacao.cod_cotacao
             AND adjudicacao.lote                = homologacao.lote
             AND adjudicacao.exercicio_cotacao   = homologacao.exercicio_cotacao
             AND adjudicacao.cgm_fornecedor      = homologacao.cgm_fornecedor

      INNER JOIN licitacao.cotacao_licitacao
              ON cotacao_licitacao.cod_modalidade      = adjudicacao.cod_modalidade
             AND cotacao_licitacao.cod_licitacao       = adjudicacao.cod_licitacao
             AND cotacao_licitacao.cod_entidade        = adjudicacao.cod_entidade
             AND cotacao_licitacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
             AND cotacao_licitacao.lote                = adjudicacao.lote
             AND cotacao_licitacao.cod_cotacao         = adjudicacao.cod_cotacao
             AND cotacao_licitacao.cod_item            = adjudicacao.cod_item
             AND cotacao_licitacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
             AND cotacao_licitacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor

      INNER JOIN (SELECT licitacao.*
                       , (SELECT exercicio
                            FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
                          VALUES ( cod_licitacao       INTEGER
                                 , cod_modalidade      INTEGER
                                 , cod_entidade        INTEGER
                                 , exercicio           CHAR(4)
                                 , exercicio_licitacao VARCHAR
                                 , num_licitacao       TEXT ) 
                           WHERE cod_entidade   = licitacao.cod_entidade
                             AND cod_licitacao  = licitacao.cod_licitacao
                             AND cod_modalidade = licitacao.cod_modalidade
                             AND exercicio      = licitacao.exercicio
                         ) AS exercicio_licitacao
                       , (SELECT num_licitacao
                            FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
                          VALUES ( cod_licitacao       INTEGER
                                 , cod_modalidade      INTEGER
                                 , cod_entidade        INTEGER
                                 , exercicio           CHAR(4)
                                 , exercicio_licitacao VARCHAR
                                 , num_licitacao       TEXT ) 
                           WHERE cod_entidade   = licitacao.cod_entidade
                             AND cod_licitacao  = licitacao.cod_licitacao
                             AND cod_modalidade = licitacao.cod_modalidade
                             AND exercicio      = licitacao.exercicio
                         ) AS num_processo_licitatorio
                    FROM licitacao.licitacao
                 ) AS licitacao
              ON licitacao.cod_licitacao  = cotacao_licitacao.cod_licitacao
             AND licitacao.cod_modalidade = cotacao_licitacao.cod_modalidade
             AND licitacao.cod_entidade   = cotacao_licitacao.cod_entidade
             AND licitacao.exercicio      = cotacao_licitacao.exercicio_licitacao

       LEFT JOIN licitacao.licitacao_anulada
              ON licitacao.cod_licitacao  = licitacao_anulada.cod_licitacao
             AND licitacao.cod_modalidade = licitacao_anulada.cod_modalidade
             AND licitacao.cod_entidade   = licitacao_anulada.cod_entidade
             AND licitacao.exercicio      = licitacao_anulada.exercicio

      INNER JOIN licitacao.edital
              ON edital.cod_licitacao       = licitacao.cod_licitacao
             AND edital.cod_modalidade      = licitacao.cod_modalidade
             AND edital.cod_entidade        = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio

       LEFT JOIN licitacao.edital_anulado
              ON edital.num_edital=edital_anulado.num_edital
             AND edital.exercicio =edital_anulado.exercicio

      INNER JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa  = licitacao.cod_mapa

      INNER JOIN compras.mapa_item
              ON mapa_item.exercicio             = licitacao.exercicio_mapa
             AND mapa_item.cod_mapa              = licitacao.cod_mapa
             AND mapa_item.cod_entidade          = homologacao.cod_entidade
             AND mapa_item.cod_item              = homologacao.cod_item

       LEFT JOIN compras.mapa_item_anulacao
              ON mapa_item.exercicio             = mapa_item_anulacao.exercicio
             AND mapa_item.cod_entidade          = mapa_item_anulacao.cod_entidade
             AND mapa_item.cod_solicitacao       = mapa_item_anulacao.cod_solicitacao
             AND mapa_item.cod_mapa              = mapa_item_anulacao.cod_mapa
             AND mapa_item.cod_centro            = mapa_item_anulacao.cod_centro
             AND mapa_item.cod_item              = mapa_item_anulacao.cod_item
             AND mapa_item.exercicio_solicitacao = mapa_item_anulacao.exercicio_solicitacao
             AND mapa_item.lote                  = mapa_item_anulacao.lote

      INNER JOIN administracao.configuracao_entidade
              ON configuracao_entidade.parametro    = 'tcemg_codigo_orgao_entidade_sicom'
             AND configuracao_entidade.cod_modulo   = 55
             AND configuracao_entidade.exercicio    = licitacao.exercicio
             AND configuracao_entidade.cod_entidade = licitacao.cod_entidade

      INNER JOIN compras.mapa_cotacao
              ON mapa_cotacao.exercicio_mapa = mapa.exercicio
             AND mapa_cotacao.cod_mapa       = mapa.cod_mapa

      INNER JOIN compras.cotacao
              ON cotacao.exercicio   = mapa_cotacao.exercicio_cotacao
             AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao

      INNER JOIN compras.julgamento
              ON julgamento.exercicio   = cotacao.exercicio
             AND julgamento.cod_cotacao = cotacao.cod_cotacao

      INNER JOIN compras.julgamento_item
              ON julgamento_item.exercicio   = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             AND julgamento_item.cgm_fornecedor = homologacao.cgm_fornecedor
             AND julgamento_item.lote           = homologacao.lote
             AND julgamento_item.cod_item       = homologacao.cod_item
             AND julgamento_item.ordem = 1

      INNER JOIN compras.cotacao_fornecedor_item
              ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
             AND cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
             AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
             AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
             AND cotacao_fornecedor_item.lote           = julgamento_item.lote

           WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
             AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
             AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
             AND licitacao.cod_modalidade NOT IN (8,9)
			 AND homologacao_anulada.num_homologacao IS NULL
             AND licitacao_anulada.cod_licitacao IS NULL
			 AND homologacao.homologado IS TRUE 
             AND edital_anulado.num_edital IS NULL

        GROUP BY tipo_registro
               , cod_orgao_resp
               , cod_unidade_resp
               , licitacao.exercicio_licitacao
               , licitacao.num_processo_licitatorio
               , num_lote
               , dt_cotacao
               , homologacao.cod_item
               , licitacao.cod_tipo_objeto
               , cotacao_fornecedor_item.vl_cotacao ";

        return $stSql;
    }

    public function recuperaDetalhamento15(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento15",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamento15()
    {
        $stSql = "
            SELECT
                    15 AS tipo_registro
                  , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao_resp
                  --, LPAD(LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                  , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                  , config_licitacao.exercicio_licitacao
                  , config_licitacao.num_licitacao AS num_processo_licitatorio
                  , mapa_item.lote AS num_lote
                  , mapa_item.cod_item AS cod_item
                  , (mapa_item.vl_total / mapa_item.quantidade) AS vl_item
                  
            FROM licitacao.licitacao
            
            JOIN licitacao.participante
              ON participante.cod_licitacao = licitacao.cod_licitacao
             AND participante.cod_modalidade = licitacao.cod_modalidade
             AND participante.cod_entidade = licitacao.cod_entidade
             AND participante.exercicio = licitacao.exercicio
             
            JOIN compras.fornecedor
              ON fornecedor.cgm_fornecedor = participante.cgm_fornecedor
              
            JOIN compras.nota_fiscal_fornecedor
              ON nota_fiscal_fornecedor.cgm_fornecedor = fornecedor.cgm_fornecedor
             
            JOIN compras.nota_fiscal_fornecedor_ordem
              ON nota_fiscal_fornecedor_ordem.cgm_fornecedor = nota_fiscal_fornecedor.cgm_fornecedor
             AND nota_fiscal_fornecedor_ordem.cod_nota = nota_fiscal_fornecedor.cod_nota
             
            JOIN compras.ordem
              ON ordem.exercicio = nota_fiscal_fornecedor_ordem.exercicio
             AND ordem.cod_entidade = nota_fiscal_fornecedor_ordem.cod_entidade
             AND ordem.cod_ordem = nota_fiscal_fornecedor_ordem.cod_ordem
             AND ordem.tipo = nota_fiscal_fornecedor_ordem.tipo
             
            JOIN compras.ordem_item
              ON ordem_item.cod_entidade = ordem.cod_entidade
             AND ordem_item.cod_ordem = ordem.cod_ordem
             AND ordem_item.exercicio = ordem.exercicio
             AND ordem_item.tipo = ordem.tipo
            
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             
            JOIN orcamento.despesa
              ON despesa.exercicio = solicitacao_item_dotacao.exercicio
             AND despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
             
            JOIN administracao.configuracao_entidade
              ON configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
             AND configuracao_entidade.cod_modulo = 55
             AND configuracao_entidade.exercicio = despesa.exercicio
             AND configuracao_entidade.cod_entidade = despesa.cod_entidade
			 
            JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 ) AS config_licitacao
              ON config_licitacao.cod_entidade = licitacao.cod_entidade
             AND config_licitacao.cod_licitacao = licitacao.cod_licitacao
             AND config_licitacao.cod_modalidade = licitacao.cod_modalidade
             AND config_licitacao.exercicio = licitacao.exercicio  			 
              
            WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
              AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND modalidade.cod_modalidade NOT IN (8,9) 
              AND NOT EXISTS( SELECT 1
			       FROM licitacao.licitacao_anulada
			      WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
			        AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                AND licitacao_anulada.exercicio = licitacao.exercicio
                           )
              
            GROUP BY tipo_registro, cod_orgao_resp, cod_unidade_resp, config_licitacao.exercicio_licitacao, config_licitacao.num_licitacao, num_lote, cod_item
        ";
        
        return $stSql;
    }
    
    public function recuperaDetalhamento16(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento16",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamento16()
    {
        $stSql = "
          SELECT 16 AS tipo_registro
               , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao_resp
               , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
               , config_licitacao.exercicio_licitacao
               , config_licitacao.num_licitacao AS num_processo_licitatorio
               , LPAD(orgao_despesa.valor::varchar,2,'0') AS cod_orgao
               , LPAD(LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade
               , despesa.cod_funcao AS cod_funcao
               , despesa.cod_subfuncao AS cod_subfuncao
               , ppa.programa.num_programa AS cod_programa
               , ppa.acao.num_acao AS id_acao
               , '' AS id_subacao
               , (LPAD(''||REPLACE(conta_despesa.cod_estrutural, '.', ''),6, '')) AS natureza_despesa
               , COALESCE(recurso.cod_fonte, '100') AS cod_font_recursos
               , SUM(mapa_item_dotacao.vl_dotacao) AS vl_recurso

            FROM licitacao.licitacao

      INNER JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo

      INNER JOIN licitacao.criterio_julgamento
              ON criterio_julgamento.cod_criterio = licitacao.cod_criterio

      INNER JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio

       LEFT JOIN licitacao.edital_anulado
              ON edital_anulado.num_edital=edital.num_edital
             AND edital_anulado.exercicio=edital.exercicio

      INNER JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto

      INNER JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto

      INNER JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade

      INNER JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa

      INNER JOIN compras.tipo_licitacao
              ON tipo_licitacao.cod_tipo_licitacao = licitacao.cod_tipo_licitacao

      INNER JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa

      INNER JOIN compras.mapa_cotacao
              ON mapa_cotacao.exercicio_mapa = mapa.exercicio
             AND mapa_cotacao.cod_mapa = mapa.cod_mapa

      INNER JOIN compras.cotacao
              ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
             AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao

      INNER JOIN compras.julgamento
              ON julgamento.exercicio = cotacao.exercicio
             AND julgamento.cod_cotacao = cotacao.cod_cotacao

      INNER JOIN compras.julgamento_item
              ON julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao

      INNER JOIN compras.cotacao_fornecedor_item
              ON cotacao_fornecedor_item.exercicio = julgamento_item.exercicio
             AND cotacao_fornecedor_item.cod_cotacao = julgamento_item.cod_cotacao
             AND cotacao_fornecedor_item.cod_item = julgamento_item.cod_item
             AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
             AND cotacao_fornecedor_item.lote = julgamento_item.lote

      INNER JOIN compras.fornecedor
              ON fornecedor.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor

      INNER JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item

       LEFT JOIN licitacao.homologacao_anulada
              ON homologacao_anulada.cod_licitacao=homologacao.cod_licitacao
             AND homologacao_anulada.cod_modalidade=homologacao.cod_modalidade
             AND homologacao_anulada.cod_entidade=homologacao.cod_entidade
             AND homologacao_anulada.exercicio_licitacao=homologacao.exercicio_licitacao
             AND homologacao_anulada.num_homologacao=homologacao.num_homologacao
             AND homologacao_anulada.cod_item=homologacao.cod_item

      INNER JOIN compras.solicitacao_homologada
              ON solicitacao_homologada.exercicio=mapa_solicitacao.exercicio_solicitacao
             AND solicitacao_homologada.cod_entidade=mapa_solicitacao.cod_entidade
             AND solicitacao_homologada.cod_solicitacao=mapa_solicitacao.cod_solicitacao

      INNER JOIN compras.mapa_item_dotacao
              ON mapa_item_dotacao.exercicio=solicitacao_homologada.exercicio
             AND mapa_item_dotacao.cod_entidade=solicitacao_homologada.cod_entidade
             AND mapa_item_dotacao.cod_solicitacao=solicitacao_homologada.cod_solicitacao
             AND mapa_item_dotacao.cod_item=homologacao.cod_item
             AND mapa_item_dotacao.cod_mapa=mapa.cod_mapa

      INNER JOIN orcamento.despesa
              ON despesa.exercicio = mapa_item_dotacao.exercicio
             AND despesa.cod_despesa = mapa_item_dotacao.cod_despesa

      INNER JOIN orcamento.conta_despesa
              ON conta_despesa.exercicio = despesa.exercicio
             AND conta_despesa.cod_conta = despesa.cod_conta

      INNER JOIN orcamento.programa
              ON programa.cod_programa = despesa.cod_programa
             AND programa.exercicio    = despesa.exercicio

      INNER JOIN orcamento.programa_ppa_programa
              ON programa_ppa_programa.cod_programa = programa.cod_programa
             AND programa_ppa_programa.exercicio    = programa.exercicio

      INNER JOIN ppa.programa
              ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa

      INNER JOIN orcamento.pao
              ON pao.num_pao   = despesa.num_pao
             AND pao.exercicio = despesa.exercicio

      INNER JOIN orcamento.pao_ppa_acao
              ON pao_ppa_acao.num_pao = pao.num_pao
             AND pao_ppa_acao.exercicio = pao.exercicio

      INNER JOIN ppa.acao
              ON acao.cod_acao = pao_ppa_acao.cod_acao

       LEFT JOIN orcamento.recurso
              ON recurso.cod_recurso = despesa.cod_recurso
             AND recurso.exercicio = despesa.exercicio

      INNER JOIN administracao.configuracao_entidade
              ON configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
             AND configuracao_entidade.cod_modulo = 55
             AND configuracao_entidade.exercicio = licitacao.exercicio
             AND configuracao_entidade.cod_entidade = licitacao.cod_entidade

      INNER JOIN administracao.configuracao_entidade AS orgao_despesa
              ON orgao_despesa.parametro = 'tcemg_codigo_orgao_entidade_sicom'
             AND orgao_despesa.cod_modulo = 55
             AND orgao_despesa.exercicio = despesa.exercicio
             AND orgao_despesa.cod_entidade = despesa.cod_entidade

      INNER JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico

      INNER JOIN ( SELECT num_documento, numcgm, tipo_documento
                    FROM (
                            SELECT cpf AS num_documento, numcgm, 1 AS tipo_documento
                            FROM sw_cgm_pessoa_fisica
                            UNION
                            SELECT cnpj AS num_documento, numcgm, 2 AS tipo_documento
                            FROM sw_cgm_pessoa_juridica
                         ) AS tabela
                    GROUP BY numcgm, num_documento, tipo_documento
                 ) AS documento_pessoa
              ON documento_pessoa.numcgm = responsavel.numcgm

       LEFT JOIN licitacao.contrato_licitacao
              ON contrato_licitacao.cod_licitacao=licitacao.cod_licitacao
             AND contrato_licitacao.cod_modalidade=licitacao.cod_modalidade
             AND contrato_licitacao.cod_entidade=licitacao.cod_entidade
             AND contrato_licitacao.exercicio_licitacao=licitacao.exercicio

       LEFT JOIN licitacao.contrato
              ON contrato.num_contrato=contrato_licitacao.num_contrato
             AND contrato.cod_entidade=contrato_licitacao.cod_entidade
             AND contrato.exercicio=contrato_licitacao.exercicio

      INNER JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 ) AS config_licitacao
              ON config_licitacao.cod_entidade = licitacao.cod_entidade
             AND config_licitacao.cod_licitacao = licitacao.cod_licitacao
             AND config_licitacao.cod_modalidade = licitacao.cod_modalidade
             AND config_licitacao.exercicio = licitacao.exercicio

           WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
             AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
             AND homologacao_anulada.cod_licitacao IS NULL
             AND edital_anulado.num_edital IS NULL
             AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
             AND licitacao.cod_modalidade NOT IN (8,9)
             AND NOT EXISTS( SELECT 1
                               FROM licitacao.licitacao_anulada
                              WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                                AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                AND licitacao_anulada.exercicio = licitacao.exercicio
                           )

        GROUP BY tipo_registro
               , cod_orgao_resp
               , cod_unidade_resp
               , config_licitacao.exercicio_licitacao
               , num_processo_licitatorio
               , cod_orgao
               , cod_unidade
               , cod_funcao
               , cod_subfuncao
               , ppa.programa.num_programa
               , id_acao
               , natureza_despesa
               , cod_font_recursos ";

        return $stSql;
    }

    public function __destruct(){}
}