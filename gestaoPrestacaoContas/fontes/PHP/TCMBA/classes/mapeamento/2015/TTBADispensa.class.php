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
    * Extensão da Classe de mapeamento
    * Data de Criação: 15/10/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    * $Revision: 63677 $
    * $Name$
    * $Author: diego $
    * $Date: 2007-10-16 01:38:47 +0000 (Ter, 16 Out 2007) $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTBADispensa extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setEstrutura         ( array() );
        $this->setEstruturaAuxiliar ( array() );
    }

    public function montaRecuperaTodos()
    {
      $stSql = " SELECT tipo_registro
                      , unidade_gestora
                      , exercicio_processo
                      , num_processo
                      , dt_publicacao
                      , documento_fornecedor
                      , imprensa_oficial
                      , cpf_responsavel
                      , dt_inicio_gestao_respons
                      , tp_ordenador_respons
                      , cpf_ratificador
                      , dt_inicio_gestao_ratificador
                      , tp_ordenador_ratificador
                      , objeto
                      , SUM(COALESCE(valor,0.00)) AS valor
                      , fundamentacao_legal
                      , competencia
                      , tipo_dispensa
                      , nome_fornecedor
                      ,regime_execucao
                      , justificativa
                      , dt_ratificacao
                      , dt_dispensa
                      tipo_documento

              FROM (    
                SELECT 1 AS tipo_registro
                       , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                       , licitacao.exercicio AS exercicio_processo
                       , licitacao.exercicio||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||licitacao.cod_licitacao AS num_processo
                       , TO_CHAR(licitacao.timestamp,'ddmmyyyy') AS dt_publicacao
                       , documento_pessoa.num_documento AS documento_fornecedor
                       , sw_cgm.nom_cgm AS imprensa_oficial
                       , ordenador.cpf AS cpf_responsavel
                       , configuracao_ordenador.dt_inicio_vigencia AS dt_inicio_gestao_respons
                       , configuracao_ordenador.cod_tipo_responsavel AS tp_ordenador_respons
                       , '' AS cpf_ratificador
                       , '' AS dt_inicio_gestao_ratificador
                       , '' AS tp_ordenador_ratificador
                       , objeto.descricao AS objeto
                       , vl_cotacao  as valor
                       , justificativa_razao.fundamentacao_legal as fundamentacao_legal
                       , TO_CHAR(homologacao.timestamp, 'yyyymm') AS competencia
                       , CASE WHEN modalidade.cod_modalidade = 8 THEN 5
                               WHEN modalidade.cod_modalidade = 9 THEN 6
                           END AS tipo_dispensa
                       , documento_pessoa.nom_cgm AS nome_fornecedor 
                       , CASE WHEN licitacao.cod_regime  = 1 THEN 7
                               WHEN licitacao.cod_regime = 2 THEN 5
                               WHEN licitacao.cod_regime = 3 THEN 8
                               WHEN licitacao.cod_regime = 4 THEN 6
                               WHEN licitacao.cod_regime = 5 THEN 4 
                           END AS regime_execucao
                       , justificativa_razao.justificativa
                       , TO_DATE(homologacao.timestamp::varchar,'yyyy-mm-dd') AS dt_ratificacao
                       , TO_DATE(licitacao.timestamp::varchar,'yyyy-mm-dd') AS dt_dispensa
                       , documento_pessoa.tipo_documento
                   
                   FROM licitacao.licitacao
             
             INNER JOIN compras.modalidade
                     ON modalidade.cod_modalidade = licitacao.cod_modalidade
             
             INNER JOIN compras.objeto
                     ON objeto.cod_objeto = licitacao.cod_objeto
             
             INNER JOIN compras.tipo_objeto
                     ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
             
             INNER JOIN licitacao.edital
                     ON edital.cod_licitacao  = licitacao.cod_licitacao
                    AND edital.cod_modalidade = licitacao.cod_modalidade
                    AND edital.cod_entidade   = licitacao.cod_entidade
                    AND edital.exercicio_licitacao = licitacao.exercicio
             
              LEFT JOIN licitacao.publicacao_edital
                     ON publicacao_edital.num_edital = edital.num_edital
                    AND publicacao_edital.exercicio  = edital.exercicio
             
             INNER JOIN sw_cgm AS responsavel
                     ON responsavel.numcgm = edital.responsavel_juridico
             
              LEFT JOIN licitacao.veiculos_publicidade
                     ON veiculos_publicidade.numcgm = publicacao_edital.numcgm
             
              LEFT JOIN sw_cgm        
                     ON sw_cgm.numcgm = veiculos_publicidade.numcgm        
             
             INNER JOIN compras.mapa
                     ON mapa.exercicio = licitacao.exercicio_mapa
                    AND mapa.cod_mapa  = licitacao.cod_mapa     
             
             INNER JOIN compras.mapa_cotacao
                     ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                    AND mapa.cod_mapa  = mapa_cotacao.cod_mapa
                    
             INNER JOIN compras.julgamento
                     ON julgamento.exercicio   = mapa_cotacao.exercicio_cotacao
                    AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
                    
             INNER JOIN compras.julgamento_item
                     ON julgamento_item.exercicio   = julgamento.exercicio
                    AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
                    AND julgamento_item.ordem = 1
                    
             INNER JOIN licitacao.homologacao
                     ON homologacao.cod_licitacao       = licitacao.cod_licitacao
                    AND homologacao.cod_modalidade      = licitacao.cod_modalidade
                    AND homologacao.cod_entidade        = licitacao.cod_entidade
                    AND homologacao.exercicio_licitacao = licitacao.exercicio
                    AND homologacao.cod_item            = julgamento_item.cod_item
                    AND homologacao.lote                = julgamento_item.lote
                    AND (
                          SELECT homologacao_anulada.num_homologacao
                            FROM licitacao.homologacao_anulada
                           WHERE homologacao_anulada.cod_licitacao       = licitacao.cod_licitacao
                             AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
                             AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
                             AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
                             AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
                             AND homologacao.cod_item                    = homologacao_anulada.cod_item
                             AND homologacao.lote                        = homologacao_anulada.lote
                        ) IS NULL
                        
              LEFT JOIN ( SELECT num_documento
                               , tabela.numcgm
                               , tipo_documento
                               , sw_cgm.nom_cgm
                               FROM ( SELECT cpf AS num_documento
                                           , numcgm
                                           , 1 AS tipo_documento
                                       FROM sw_cgm_pessoa_fisica
                                    UNION
                                      SELECT cnpj AS num_documento
                                           , numcgm, 2 AS tipo_documento
                                       FROM sw_cgm_pessoa_juridica
                                   ) AS tabela 
                       INNER JOIN sw_cgm
                               ON  sw_cgm.numcgm = tabela.numcgm
                         GROUP BY tabela.numcgm
                                , num_documento
                                , tipo_documento
                                , sw_cgm.nom_cgm
                       ) AS documento_pessoa
                       ON documento_pessoa.numcgm = homologacao.cgm_fornecedor
               
               INNER JOIN compras.cotacao_fornecedor_item
                       ON julgamento_item.exercicio      = cotacao_fornecedor_item.exercicio
                      AND julgamento_item.cod_cotacao    = cotacao_fornecedor_item.cod_cotacao
                      AND julgamento_item.cod_item       = cotacao_fornecedor_item.cod_item
                      AND julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                      AND julgamento_item.lote           = cotacao_fornecedor_item.lote
               
               LEFT JOIN licitacao.justificativa_razao
                      ON justificativa_razao.cod_entidade   = licitacao.cod_entidade
                     AND justificativa_razao.cod_licitacao  = licitacao.cod_licitacao
                     AND justificativa_razao.exercicio      = licitacao.exercicio
                     AND justificativa_razao.cod_modalidade = licitacao.cod_modalidade
                       
               LEFT JOIN tcmba.configuracao_ordenador
                      ON configuracao_ordenador.num_orgao    = licitacao.num_orgao
                     AND configuracao_ordenador.num_unidade  = licitacao.num_unidade
                     AND configuracao_ordenador.cod_entidade = licitacao.cod_entidade
                     AND configuracao_ordenador.exercicio    = licitacao.exercicio
               
               LEFT JOIN sw_cgm_pessoa_fisica AS ordenador       
                      ON ordenador.numcgm = configuracao_ordenador.cgm_ordenador 
                   
                   WHERE licitacao.cod_entidade in (".$this->getDado('stEntidades').")
                     AND TO_DATE(homologacao.timestamp::VARCHAR, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','DD/MM/YYYY')
                                                                                   AND TO_DATE('".$this->getDado('dt_final')."', 'DD/MM/YYYY')
                     AND ( licitacao.cod_modalidade = 8 OR licitacao.cod_modalidade = 9 )
                     AND NOT EXISTS( SELECT 1
                                        FROM licitacao.licitacao_anulada
                                       WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                                         AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                         AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                                         AND licitacao_anulada.exercicio      = licitacao.exercicio
                       )       
               GROUP BY tipo_registro
                       , exercicio_processo
                       , num_processo
                       , tipo_dispensa
                       , dt_publicacao
                       , objeto
                       , imprensa_oficial     
                       , justificativa_razao.justificativa
                       , justificativa_razao.razao
                       , licitacao.exercicio 
                       , documento_fornecedor
                       , vl_cotacao
                       , competencia
                       , nome_fornecedor
                       , dt_ratificacao
                       , dt_dispensa
                       , tipo_documento
                       , fundamentacao_legal
                       , regime_execucao
                       , cpf_responsavel
                       , dt_inicio_gestao_respons
                       , tp_ordenador_respons
                               
                   UNION
                   
                   SELECT 1 AS tipo_registro
                       , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                       ,  compra_direta.exercicio_entidade AS exercicio_processo
                       , compra_direta.exercicio_entidade||LPAD(compra_direta.cod_entidade::VARCHAR,2,'0')||LPAD(compra_direta.cod_modalidade::VARCHAR,2,'0')||compra_direta.cod_compra_direta AS num_processo 
                       , TO_CHAR(compra_direta.timestamp,'ddmmyyyy') AS dt_publicacao
                       , documento_pessoa.num_documento AS documento_fornecedor
                       , sw_cgm.nom_cgm AS imprensa_oficial
                       , ordenador.cpf AS cpf_responsavel
                       , configuracao_ordenador.dt_inicio_vigencia AS dt_inicio_gestao_respons
                       , configuracao_ordenador.cod_tipo_responsavel AS tp_ordenador_respons
                       , '' AS cpf_ratificador
                       , '' AS dt_inicio_gestao_ratificador
                       , '' AS tp_ordenador_ratificador
                       , objeto.descricao AS objeto
                       , vl_cotacao  as valor
                       , justificativa_razao.fundamentacao_legal as fundamentacao_legal
                       , TO_CHAR(homologacao.timestamp, 'yyyymm') AS competencia
                       , CASE WHEN compra_direta.cod_modalidade  = 8 THEN 5
                               WHEN compra_direta.cod_modalidade = 9 THEN 6
                           END AS tipo_dispensa
                       , documento_pessoa.nom_cgm AS nome_fornecedor
                       , 4 AS regime_execucao
                       , justificativa_razao.justificativa
                       , TO_DATE(homologacao.timestamp::varchar,'yyyy-mm-dd') AS dt_ratificacao
                       , TO_DATE(compra_direta.timestamp::varchar,'yyyy-mm-dd') AS dt_dispensa
                       , documento_pessoa.tipo_documento
                    
                    FROM compras.compra_direta
              
              INNER JOIN compras.objeto
                      ON compra_direta.cod_objeto = objeto.cod_objeto 
              
              INNER JOIN compras.mapa_item
                      ON compra_direta.cod_mapa = mapa_item.cod_mapa
                     AND compra_direta.exercicio_mapa = mapa_item.exercicio
              
              INNER JOIN compras.tipo_objeto
                      ON tipo_objeto.cod_tipo_objeto = compra_direta.cod_tipo_objeto
              
               LEFT JOIN compras.publicacao_compra_direta
                      ON publicacao_compra_direta.cod_compra_direta  = compra_direta.cod_compra_direta
                     AND publicacao_compra_direta.exercicio_entidade = compra_direta.exercicio_entidade
               
               LEFT JOIN licitacao.veiculos_publicidade
                      ON veiculos_publicidade.numcgm = publicacao_compra_direta.cgm_veiculo
                      
               LEFT JOIN sw_cgm        
                      ON sw_cgm.numcgm = veiculos_publicidade.numcgm
                      
              INNER JOIN compras.mapa
                      ON mapa.exercicio = compra_direta.exercicio_mapa
                     AND mapa.cod_mapa  = compra_direta.cod_mapa     
              
              INNER JOIN compras.mapa_cotacao
                      ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                     AND mapa.cod_mapa  = mapa_cotacao.cod_mapa
                     
              INNER JOIN compras.julgamento
                      ON julgamento.exercicio   = mapa_cotacao.exercicio_cotacao
                     AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
                     
              INNER JOIN compras.julgamento_item
                      ON julgamento_item.exercicio   = julgamento.exercicio
                     AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
                     AND julgamento_item.ordem       = 1
               
              INNER JOIN compras.homologacao
                      ON homologacao.cod_compra_direta       = compra_direta.cod_compra_direta
                     AND homologacao.cod_modalidade          = compra_direta.cod_modalidade
                     AND homologacao.cod_entidade            = compra_direta.cod_entidade
                     AND homologacao.exercicio_compra_direta = compra_direta.exercicio_entidade
                     AND homologacao.cod_item                = julgamento_item.cod_item
                     AND homologacao.lote                    = julgamento_item.lote
              
               LEFT JOIN ( SELECT num_documento
                               , tabela.numcgm
                               , tipo_documento
                               , sw_cgm.nom_cgm
                            FROM ( SELECT cpf AS num_documento
                                        , numcgm
                                        , 1 AS tipo_documento
                                     FROM sw_cgm_pessoa_fisica
                                UNION
                                   SELECT cnpj AS num_documento
                                        , numcgm
                                        , 2 AS tipo_documento
                                     FROM sw_cgm_pessoa_juridica
                               ) AS tabela 
                      INNER JOIN sw_cgm
                             ON  sw_cgm.numcgm = tabela.numcgm
                       GROUP BY tabela.numcgm
                              , num_documento
                              , tipo_documento
                              , sw_cgm.nom_cgm
                       ) AS documento_pessoa
                      ON documento_pessoa.numcgm = homologacao.cgm_fornecedor
              
              INNER JOIN compras.cotacao_fornecedor_item
                      ON julgamento_item.exercicio      = cotacao_fornecedor_item.exercicio
                     AND julgamento_item.cod_cotacao    = cotacao_fornecedor_item.cod_cotacao
                     AND julgamento_item.cod_item       = cotacao_fornecedor_item.cod_item
                     AND julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                     AND julgamento_item.lote           = cotacao_fornecedor_item.lote
                     
               LEFT JOIN compras.justificativa_razao
                      ON justificativa_razao.cod_entidade       = compra_direta.cod_entidade
                     AND justificativa_razao.cod_compra_direta  = compra_direta.cod_compra_direta
                     AND justificativa_razao.exercicio_entidade = compra_direta.exercicio_entidade
                     AND justificativa_razao.cod_modalidade     = compra_direta.cod_modalidade
                     
               INNER JOIN compras.solicitacao_item
                       ON solicitacao_item.exercicio       = mapa_item.exercicio_solicitacao
                      AND solicitacao_item.cod_entidade    = mapa_item.cod_entidade
                      AND solicitacao_item.cod_solicitacao = mapa_item.cod_solicitacao
                      AND solicitacao_item.cod_centro      = mapa_item.cod_centro
                      AND solicitacao_item.cod_item        = mapa_item.cod_item 
               
               INNER JOIN compras.solicitacao_item_dotacao
                       ON solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
                      AND solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade 
                      AND solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao 
                      AND solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro 
                      AND solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item
               
               INNER JOIN orcamento.despesa
                       ON despesa.exercicio =solicitacao_item_dotacao.exercicio
                      AND despesa.cod_despesa =solicitacao_item_dotacao.cod_despesa
                
                LEFT JOIN tcmba.configuracao_ordenador
                       ON configuracao_ordenador.num_orgao    = despesa.num_orgao
                      AND configuracao_ordenador.num_unidade  = despesa.num_unidade
                      AND configuracao_ordenador.cod_entidade = despesa.cod_entidade
                      AND configuracao_ordenador.exercicio    = despesa.exercicio
               
                LEFT JOIN sw_cgm_pessoa_fisica AS ordenador       
                       ON ordenador.numcgm = configuracao_ordenador.cgm_ordenador 
                    
                    WHERE compra_direta.cod_entidade IN (".$this->getDado('stEntidades').")
                      AND TO_DATE(homologacao.timestamp::VARCHAR, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','DD/MM/YYYY')
                                                                                    AND TO_DATE('".$this->getDado('dt_final')."', 'DD/MM/YYYY')
                 AND NOT EXISTS( SELECT 1
                                   FROM compras.compra_direta_anulacao 
                                  WHERE compra_direta_anulacao.cod_compra_direta  = compra_direta.cod_compra_direta
                                    AND compra_direta_anulacao.cod_modalidade     = compra_direta.cod_modalidade
                                    AND compra_direta_anulacao.cod_entidade       = compra_direta.cod_entidade
                                    AND compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                                )
                  AND NOT EXISTS( SELECT 1
                                    FROM compras.mapa_item_anulacao  
                                   WHERE mapa_item_anulacao.cod_solicitacao       = mapa_item.cod_solicitacao 
                                     AND mapa_item_anulacao.exercicio             = mapa_item.exercicio
                                     AND mapa_item_anulacao.cod_entidade          = mapa_item.cod_entidade
                                     AND mapa_item_anulacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                                     AND mapa_item_anulacao.cod_mapa              = mapa_item.cod_mapa
                                     AND mapa_item_anulacao.cod_centro            = mapa_item.cod_centro
                                     AND mapa_item_anulacao.lote                  = mapa_item.lote
                                     AND mapa_item_anulacao.cod_item              = mapa_item.cod_item
                       )
                       
               GROUP BY tipo_registro
                       , exercicio_processo
                       , num_processo
                       , tipo_dispensa
                       , dt_publicacao
                       , objeto
                       , imprensa_oficial     
                       , justificativa_razao.justificativa
                       , justificativa_razao.razao
                       , compra_direta.exercicio_entidade 
                       , documento_fornecedor
                       , vl_cotacao
                       , competencia
                       , nome_fornecedor
                       , dt_ratificacao
                       , dt_dispensa
                       , tipo_documento
                       , fundamentacao_legal
                       , regime_execucao
                       , cpf_responsavel
                       , dt_inicio_gestao_respons
                       , tp_ordenador_respons
                ORDER BY num_processo  
        ) AS retornado

GROUP BY tipo_registro
       , unidade_gestora
       , exercicio_processo
       , num_processo
       , dt_publicacao
       , documento_fornecedor
       , imprensa_oficial
       , cpf_responsavel
       , dt_inicio_gestao_respons
       , tp_ordenador_respons
       , cpf_ratificador
       , dt_inicio_gestao_ratificador
       , tp_ordenador_ratificador
       , objeto
       , fundamentacao_legal
       , competencia
       , tipo_dispensa
       , nome_fornecedor
       , regime_execucao
       , justificativa
       , dt_ratificacao
       , dt_dispensa
       , tipo_documento
        ";
        
        return $stSql;
    }
}

?>