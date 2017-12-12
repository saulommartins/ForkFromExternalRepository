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
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TLicitacaoParticipante.class.php 57380 2014-02-28 17:45:35Z diogo.zarpelon $

    * Casos de uso: uc-03.05.18
            uc-03.05.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.participante
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Thiago La Delfa Cabelleira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTCEMGDispensaInexigibilidade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

    public function recuperaExportacao10(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaExportacao10", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }
    
    public function montaRecuperaExportacao10()
    {
        $stSql = "
                   SELECT 10 AS tipo_registro
                        , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp     
                        , LPAD(orgao.valor::VARCHAR, 2, '0') AS cod_orgao_resp
                        , config_licitacao.exercicio_licitacao AS exercicio_processo
                        , config_licitacao.num_licitacao AS num_processo
                        , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                               WHEN modalidade.cod_modalidade = 9 THEN 2
                          END AS tipo_processo
                        , TO_CHAR(licitacao.timestamp,'dd/mm/yyyy') AS dt_abertura
                        , justificativa_razao.justificativa
                        , justificativa_razao.razao
                        , TO_CHAR(licitacao.timestamp,'ddmmyyyy') AS dt_publicacao_termo_ratificacao
                        , CASE WHEN tipo_objeto.cod_tipo_objeto = 1 THEN 
				    CASE WHEN (SUM(cotacao_fornecedor_item.vl_cotacao) > 15000) THEN 2
											  ELSE 99
					  END
                               WHEN tipo_objeto.cod_tipo_objeto = 2 THEN 
				    CASE WHEN (SUM(cotacao_fornecedor_item.vl_cotacao) > 8000) THEN 1
											  ELSE 99
					 END
                               WHEN tipo_objeto.cod_tipo_objeto = 3 THEN 3
                               WHEN tipo_objeto.cod_tipo_objeto = 4 THEN 3
                          END AS natureza_objeto
                        , objeto.descricao AS objeto
                        , sw_cgm.nom_cgm AS veiculo_publicacao     
                        , CASE WHEN mapa.cod_tipo_licitacao = 2 THEN 1
                               WHEN mapa.cod_tipo_licitacao = 1 OR  mapa.cod_tipo_licitacao = 3 THEN 2
                          END AS processo_por_lote
                   
                    FROM licitacao.licitacao as licitacao                                          
                   
              INNER JOIN (SELECT *
                            FROM administracao.configuracao_entidade
                           WHERE configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.parametro  = 'tcemg_codigo_orgao_entidade_sicom'
                         ) AS orgao
                      ON orgao.valor::integer = licitacao.cod_entidade
                                                              
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
                   
               LEFT JOIN licitacao.veiculos_publicidade
                      ON veiculos_publicidade.numcgm = publicacao_edital.numcgm
              
              INNER JOIN orcamento.entidade
                      ON licitacao.cod_entidade = entidade.cod_entidade                                  
                     AND licitacao.exercicio    = entidade.exercicio                                        
              
               LEFT JOIN sw_cgm        
                      ON entidade.numcgm = sw_cgm.numcgm
                     AND sw_cgm.numcgm = veiculos_publicidade.numcgm        
              
              INNER JOIN compras.mapa
                      ON mapa.exercicio = licitacao.exercicio_mapa
                          AND mapa.cod_mapa = licitacao.cod_mapa     

              INNER JOIN compras.mapa_cotacao
	              ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                     AND mapa.cod_mapa = mapa_cotacao.cod_mapa
                
              INNER JOIN compras.julgamento
		      ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao
                     AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
                
              INNER JOIN compras.julgamento_item
		      ON  julgamento_item.exercicio = julgamento.exercicio
                     AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
                     AND julgamento_item.ordem = 1

                    JOIN licitacao.homologacao
                      ON homologacao.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao.cod_entidade=licitacao.cod_entidade
                     AND homologacao.exercicio_licitacao=licitacao.exercicio
                     AND homologacao.cod_item=julgamento_item.cod_item
                     AND homologacao.lote=julgamento_item.lote
                     AND (
                             SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                             WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                             AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                             AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                             AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                             AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                             AND homologacao.cod_item=homologacao_anulada.cod_item
                             AND homologacao.lote=homologacao_anulada.lote
                         ) IS NULL
                     
              INNER JOIN compras.cotacao_fornecedor_item
                      ON julgamento_item.exercicio = cotacao_fornecedor_item.exercicio
                     AND julgamento_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                     AND julgamento_item.cod_item = cotacao_fornecedor_item.cod_item
                     AND julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                     AND julgamento_item.lote = cotacao_fornecedor_item.lote
                    
              LEFT JOIN licitacao.justificativa_razao
                     ON justificativa_razao.cod_entidade = licitacao.cod_entidade
	            AND justificativa_razao.cod_licitacao = licitacao.cod_licitacao
	            AND justificativa_razao.exercicio = licitacao.exercicio
	            AND justificativa_razao.cod_modalidade = licitacao.cod_modalidade
				
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
                   
                  WHERE licitacao.cod_entidade in (" . $this->getDado('entidades') . ")
                    AND TO_DATE(homologacao.timestamp::varchar,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')   
                    AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')    
                    AND ( licitacao.cod_modalidade = 8 OR licitacao.cod_modalidade = 9 )
                    AND NOT EXISTS( SELECT 1
                                     FROM licitacao.licitacao_anulada
                                     WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                                         AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                         AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                         AND licitacao_anulada.exercicio = licitacao.exercicio
                                 )
                        
               GROUP BY tipo_registro
                      , cod_unidade_resp
                      , cod_orgao_resp
                      , exercicio_processo
                      , num_processo
                      , tipo_processo
                      , dt_abertura
                      , dt_publicacao_termo_ratificacao
                      , objeto,veiculo_publicacao
                      , processo_por_lote
                      , justificativa_razao.justificativa
                      , justificativa_razao.razao
                      , tipo_objeto.cod_tipo_objeto
					  , config_licitacao.exercicio_licitacao
					  , config_licitacao.num_licitacao
                  
                  order by num_processo
        ";
        return $stSql;
    }
    
    public function recuperaExportacao11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao11()
    {
        $stSql = "
            SELECT 11 AS tipo_registro
                 , LPAD(orgao.valor::VARCHAR, 2, '0') AS cod_orgao_resp
                 , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                 , config_licitacao.exercicio_licitacao AS exercicio_processo
                 , config_licitacao.num_licitacao AS num_processo
                 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                        WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , mapa_cotacao.cod_cotacao::INTEGER AS num_lote
                 , 'Lote n.'||mapa_cotacao.cod_cotacao::VARCHAR AS desc_lote

            FROM licitacao.licitacao
            
            JOIN (SELECT *
                            FROM administracao.configuracao_entidade
                            WHERE configuracao_entidade.cod_modulo = 55
                              AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                        ) AS orgao
              ON orgao.valor::integer = licitacao.cod_entidade
            
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade 
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
              
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             
       LEFT JOIN licitacao.publicacao_edital
              ON publicacao_edital.num_edital = edital.num_edital
             AND publicacao_edital.exercicio = edital.exercicio
             
       LEFT JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico
              
       LEFT JOIN sw_cgm_pessoa_fisica
              ON sw_cgm_pessoa_fisica.numcgm = responsavel.numcgm
              
            JOIN sw_municipio
              ON sw_municipio.cod_municipio = responsavel.cod_municipio
             AND sw_municipio.cod_uf = responsavel.cod_uf
             
            JOIN sw_uf
              ON sw_uf.cod_uf = sw_municipio.cod_uf
              
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.mapa_cotacao
	      ON mapa.exercicio = mapa_cotacao.exercicio_mapa
             AND mapa.cod_mapa = mapa_cotacao.cod_mapa
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             
      INNER JOIN compras.julgamento
              ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao
             AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
           
      INNER JOIN compras.julgamento_item
              ON  julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             AND julgamento_item.ordem = 1

            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote
             AND (
                     SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                     WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                     AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                     AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                     AND homologacao.cod_item=homologacao_anulada.cod_item
                     AND homologacao.lote=homologacao_anulada.lote
                 ) IS NULL
				 
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
             
           WHERE licitacao.cod_entidade in (" . $this->getDado('entidades') . ")
             AND TO_DATE(homologacao.timestamp::varchar,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')   
             AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
             AND ( licitacao.cod_modalidade = 8 OR licitacao.cod_modalidade = 9 )
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
               , exercicio_processo
               , num_processo
               , tipo_processo
               , num_lote
               , desc_lote
			   , config_licitacao.exercicio_licitacao
               , config_licitacao.num_licitacao
        
        ORDER BY num_processo
        ";
        return $stSql;
    }
    
    public function recuperaExportacao12(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao12",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao12()
    {
        $stSql = "
            SELECT 12 AS tipo_registro
                 , LPAD(orgao.valor::VARCHAR, 2, '0') AS cod_orgao_resp
                 , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                 , config_licitacao.exercicio_licitacao AS exercicio_processo
                 , config_licitacao.num_licitacao AS num_processo
                 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                        WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , mapa_item.cod_item AS cod_item
                 , mapa_item.cod_item AS num_item
                 , mapa_cotacao.cod_cotacao::INTEGER AS num_lote
                 
            FROM licitacao.licitacao
            
            JOIN (SELECT *
                            FROM administracao.configuracao_entidade
                            WHERE configuracao_entidade.cod_modulo = 55
                              AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                        ) AS orgao
              ON orgao.valor::integer = licitacao.cod_entidade
            
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
              
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             
       LEFT JOIN licitacao.publicacao_edital
              ON publicacao_edital.num_edital = edital.num_edital
             AND publicacao_edital.exercicio = edital.exercicio
             
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.mapa_cotacao
	      ON mapa.exercicio = mapa_cotacao.exercicio_mapa
             AND mapa.cod_mapa = mapa_cotacao.cod_mapa
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             
             INNER JOIN compras.julgamento
              ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao
             AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
           
      INNER JOIN compras.julgamento_item
              ON  julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             AND julgamento_item.ordem = 1

            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote
             AND (
                     SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                     WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                     AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                     AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                     AND homologacao.cod_item=homologacao_anulada.cod_item
                     AND homologacao.lote=homologacao_anulada.lote
                 ) IS NULL
				 
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
             
           WHERE licitacao.cod_entidade in (" . $this->getDado('entidades') . ")
             AND TO_DATE(homologacao.timestamp::varchar,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')   
             AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
             AND ( licitacao.cod_modalidade = 8 OR licitacao.cod_modalidade = 9 )
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
               , exercicio_processo
               , num_processo
               , tipo_processo
               , mapa_item.cod_item
               , num_item
               , mapa_cotacao.cod_cotacao
			   , config_licitacao.exercicio_licitacao
               , config_licitacao.num_licitacao
        
        ORDER BY num_processo
        ";
        return $stSql;
    }
    
    public function recuperaExportacao13(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao13",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao13()
    {
        $stSql = "
            SELECT 13 AS tipo_registro
                 , LPAD(orgao.valor::VARCHAR, 2, '0') AS cod_orgao_resp
                 , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                 , config_licitacao.exercicio_licitacao AS exercicio_processo
                 , config_licitacao.num_licitacao AS num_processo
                 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                        WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , mapa_cotacao.cod_cotacao::INTEGER AS num_lote
                 , mapa_item.cod_item AS cod_item
                 
            FROM licitacao.licitacao
            
            JOIN (SELECT *
                            FROM administracao.configuracao_entidade
                            WHERE configuracao_entidade.cod_modulo = 55
                              AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                        ) AS orgao
              ON orgao.valor::integer = licitacao.cod_entidade
            
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
              
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             
       LEFT JOIN licitacao.publicacao_edital
              ON publicacao_edital.num_edital = edital.num_edital
             AND publicacao_edital.exercicio = edital.exercicio
             
            JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico
              
            JOIN sw_cgm_pessoa_fisica
              ON sw_cgm_pessoa_fisica.numcgm = responsavel.numcgm
              
            JOIN sw_municipio
              ON sw_municipio.cod_municipio = responsavel.cod_municipio
             AND sw_municipio.cod_uf = responsavel.cod_uf
             
            JOIN sw_uf
              ON sw_uf.cod_uf = sw_municipio.cod_uf
              
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.mapa_cotacao
	      ON mapa.exercicio = mapa_cotacao.exercicio_mapa
             AND mapa.cod_mapa = mapa_cotacao.cod_mapa
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             
      INNER JOIN compras.julgamento
              ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao
             AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
           
      INNER JOIN compras.julgamento_item
              ON  julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             AND julgamento_item.ordem = 1

            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote
             AND (
                     SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                     WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                     AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                     AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                     AND homologacao.cod_item=homologacao_anulada.cod_item
                     AND homologacao.lote=homologacao_anulada.lote
                 ) IS NULL
				 
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
             
           WHERE licitacao.cod_entidade in (" . $this->getDado('entidades') . ")
             AND TO_DATE(homologacao.timestamp::varchar,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')   
             AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
             AND (licitacao.cod_modalidade = 8 OR licitacao.cod_modalidade = 9)
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
               , exercicio_processo
               , num_processo
               , tipo_processo
               , num_lote
               , mapa_item.cod_item
			   , config_licitacao.exercicio_licitacao
               , config_licitacao.num_licitacao
        ORDER BY num_processo
        ";
        return $stSql;
    }
    
    public function recuperaExportacao14(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao14",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao14()
    {
        $stSql = "
            SELECT 14 AS tipo_registro
                 , LPAD(orgao.valor::VARCHAR, 2, '0') AS cod_orgao_resp
                 , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                 , config_licitacao.exercicio_licitacao AS exercicio_processo
                 , config_licitacao.num_licitacao AS num_processo
				 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                        WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , 1 AS tipo_resp
                 , sw_cgm_pessoa_fisica.cpf AS num_cpf_resp
                 , mapa_cotacao.cod_cotacao::INTEGER AS num_lote
                 
            FROM licitacao.licitacao
            
            JOIN (SELECT *
                            FROM administracao.configuracao_entidade
                            WHERE configuracao_entidade.cod_modulo = 55
                              AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                        ) AS orgao
              ON orgao.valor::integer = licitacao.cod_entidade
            
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
              
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
       
       LEFT JOIN licitacao.publicacao_edital
              ON publicacao_edital.num_edital = edital.num_edital
             AND publicacao_edital.exercicio = edital.exercicio
             
       LEFT JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico
              
       LEFT JOIN sw_cgm_pessoa_fisica
              ON sw_cgm_pessoa_fisica.numcgm = responsavel.numcgm
              
            JOIN sw_municipio
              ON sw_municipio.cod_municipio = responsavel.cod_municipio
             AND sw_municipio.cod_uf = responsavel.cod_uf
             
            JOIN sw_uf
              ON sw_uf.cod_uf = sw_municipio.cod_uf
              
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_cotacao
	      ON mapa.exercicio = mapa_cotacao.exercicio_mapa
             AND mapa.cod_mapa = mapa_cotacao.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             
      INNER JOIN compras.julgamento
              ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao
             AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
           
      INNER JOIN compras.julgamento_item
              ON  julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             AND julgamento_item.ordem = 1

            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote
             AND (
                     SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                     WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                     AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                     AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                     AND homologacao.cod_item=homologacao_anulada.cod_item
                     AND homologacao.lote=homologacao_anulada.lote
                 ) IS NULL
				 
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
             
           WHERE licitacao.cod_entidade in (" . $this->getDado('entidades') . ")
             AND TO_DATE(homologacao.timestamp::varchar,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')   
             AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
             AND ( licitacao.cod_modalidade = 8 OR licitacao.cod_modalidade = 9 )
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
               , exercicio_processo
               , num_processo
               , tipo_processo
               , tipo_resp
               , num_cpf_resp
               , mapa_cotacao.cod_cotacao
			   , config_licitacao.exercicio_licitacao
               , config_licitacao.num_licitacao
        ORDER BY num_processo
        ";
        return $stSql;
    }
    
    public function recuperaExportacao15(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao15",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao15()
    {
        $stSql = "
            SELECT 15 AS tipo_registro
                 , LPAD(orgao.valor::VARCHAR, 2, '0') AS cod_orgao_resp
                 , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                 , config_licitacao.exercicio_licitacao AS exercicio_processo
                 , config_licitacao.num_licitacao AS num_processo
				 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                        WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , mapa_item.cod_item AS cod_item
                 , TRUNC((mapa_item.vl_total/mapa_item.quantidade), 2)::NUMERIC(14,4) AS vl_cot_precos_unitario
                 , mapa_item.quantidade AS quantidade
                 , CASE WHEN mapa.cod_tipo_licitacao = 2 THEN mapa_cotacao.cod_cotacao::VARCHAR
                    ELSE ' '
                   END AS num_lote
                 
            FROM licitacao.licitacao
            
            JOIN (SELECT *
                            FROM administracao.configuracao_entidade
                            WHERE configuracao_entidade.cod_modulo = 55
                              AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                        ) AS orgao
              ON orgao.valor::integer = licitacao.cod_entidade
            
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
              
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             
       LEFT JOIN licitacao.publicacao_edital
              ON publicacao_edital.num_edital = edital.num_edital
             AND publicacao_edital.exercicio = edital.exercicio
             
            JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico
              
            JOIN sw_cgm_pessoa_fisica
              ON sw_cgm_pessoa_fisica.numcgm = responsavel.numcgm
              
            JOIN sw_municipio
              ON sw_municipio.cod_municipio = responsavel.cod_municipio
             AND sw_municipio.cod_uf = responsavel.cod_uf
             
            JOIN sw_uf
              ON sw_uf.cod_uf = sw_municipio.cod_uf
              
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.mapa_cotacao
	      ON mapa.exercicio = mapa_cotacao.exercicio_mapa
             AND mapa.cod_mapa = mapa_cotacao.cod_mapa
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             
      INNER JOIN compras.julgamento
              ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao
             AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
           
      INNER JOIN compras.julgamento_item
              ON  julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             AND julgamento_item.ordem = 1

            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote
             AND (
                     SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                     WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                     AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                     AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                     AND homologacao.cod_item=homologacao_anulada.cod_item
                     AND homologacao.lote=homologacao_anulada.lote
                 ) IS NULL
             
            JOIN compras.mapa_item_dotacao
              ON mapa_item_dotacao.exercicio = mapa_item.exercicio
             AND mapa_item_dotacao.cod_mapa = mapa_item.cod_mapa
             AND mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
             AND mapa_item_dotacao.cod_entidade = mapa_item.cod_entidade
             AND mapa_item_dotacao.cod_solicitacao = mapa_item.cod_solicitacao
             AND mapa_item_dotacao.cod_centro = mapa_item.cod_centro
             AND mapa_item_dotacao.cod_item = mapa_item.cod_item
             AND mapa_item_dotacao.lote = mapa_item.lote
             
            JOIN orcamento.despesa
              ON despesa.exercicio = mapa_item_dotacao.exercicio
             AND despesa.cod_despesa = mapa_item_dotacao.cod_despesa
			 
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
             
           WHERE licitacao.cod_entidade in (" . $this->getDado('entidades') . ")
             AND TO_DATE(homologacao.timestamp::varchar,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')   
             AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
             AND ( licitacao.cod_modalidade = 8 OR licitacao.cod_modalidade = 9 )
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
               , exercicio_processo
               , num_processo
               , tipo_processo
               , mapa_item.cod_item
               , vl_cot_precos_unitario
               , mapa_item.quantidade
               , mapa_cotacao.cod_cotacao
               , mapa.cod_tipo_licitacao
			   , config_licitacao.exercicio_licitacao
               , config_licitacao.num_licitacao
               
       ORDER BY num_processo
        ";
        return $stSql;
    }
    
    public function recuperaExportacao16(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao16",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao16()
    {
        $stSql = "
            SELECT 16 AS tipo_registro
                 , LPAD(orgao.valor::VARCHAR, 2, '0') AS cod_orgao_resp
                 , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                 , config_licitacao.exercicio_licitacao AS exercicio_processo
                 , config_licitacao.num_licitacao AS num_processo
				 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                        WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , LPAD(orgao.valor::VARCHAR, 2, '0') AS cod_orgao
                 , LPAD(LPAD(despesa.num_orgao::VARCHAR, 2, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_subunidade
                 , LPAD(despesa.cod_funcao::VARCHAR, 2, '0') AS cod_funcao
                 , despesa.cod_subfuncao AS cod_subfuncao                
				 , LPAD(ppa_programa.num_programa::VARCHAR, 4, '0') AS cod_programa
				 
                 , LPAD((ppa_acao.num_acao::varchar), 4, '0') AS id_acao
				 
                 , '0000' AS id_sub_acao
				 , REPLACE(conta_despesa.cod_estrutural, '.', '') AS natureza_despesa
                 , despesa.cod_recurso AS cod_font_recurso
                 , despesa.vl_original vl_recurso
                 , mapa_cotacao.cod_cotacao::INTEGER AS num_lote
                 
            FROM licitacao.licitacao
            
            JOIN (SELECT *
                            FROM administracao.configuracao_entidade
                            WHERE configuracao_entidade.cod_modulo = 55
                              AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                        ) AS orgao
              ON orgao.valor::integer = licitacao.cod_entidade
            
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
              
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             
       LEFT JOIN licitacao.publicacao_edital
              ON publicacao_edital.num_edital = edital.num_edital
             AND publicacao_edital.exercicio = edital.exercicio
             
       LEFT JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico
              
       LEFT JOIN sw_cgm_pessoa_fisica
              ON sw_cgm_pessoa_fisica.numcgm = responsavel.numcgm
              
            JOIN sw_municipio
              ON sw_municipio.cod_municipio = responsavel.cod_municipio
             AND sw_municipio.cod_uf = responsavel.cod_uf
             
            JOIN sw_uf
              ON sw_uf.cod_uf = sw_municipio.cod_uf
              
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.mapa_cotacao
	          ON mapa.exercicio = mapa_cotacao.exercicio_mapa
             AND mapa.cod_mapa = mapa_cotacao.cod_mapa
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             
      INNER JOIN compras.julgamento
              ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao
             AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
           
      INNER JOIN compras.julgamento_item
              ON  julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             AND julgamento_item.ordem = 1

            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote
             AND (
                     SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                     WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                     AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                     AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                     AND homologacao.cod_item=homologacao_anulada.cod_item
                     AND homologacao.lote=homologacao_anulada.lote
                 ) IS NULL
             
            JOIN compras.mapa_item_dotacao
              ON mapa_item_dotacao.exercicio = mapa_item.exercicio
             AND mapa_item_dotacao.cod_mapa = mapa_item.cod_mapa
             AND mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
             AND mapa_item_dotacao.cod_entidade = mapa_item.cod_entidade
             AND mapa_item_dotacao.cod_solicitacao = mapa_item.cod_solicitacao
             AND mapa_item_dotacao.cod_centro = mapa_item.cod_centro
             AND mapa_item_dotacao.cod_item = mapa_item.cod_item
             AND mapa_item_dotacao.lote = mapa_item.lote
             
            JOIN orcamento.despesa
              ON despesa.exercicio = mapa_item_dotacao.exercicio
             AND despesa.cod_despesa = mapa_item_dotacao.cod_despesa
			 
            JOIN orcamento.programa
              ON programa.exercicio    = despesa.exercicio
             AND programa.cod_programa = despesa.cod_programa
			 		 
            JOIN orcamento.programa_ppa_programa
              ON programa_ppa_programa.exercicio    = programa.exercicio
             AND programa_ppa_programa.cod_programa = programa.cod_programa
			 
            JOIN ppa.programa AS ppa_programa
              ON ppa_programa.cod_programa = programa_ppa_programa.cod_programa_ppa
			  
            JOIN orcamento.pao
              ON despesa.num_pao   = pao.num_pao    
             AND despesa.exercicio = pao.exercicio                           
                                                          
            JOIN orcamento.pao_ppa_acao              
              ON pao_ppa_acao.num_pao   = pao.num_pao
             AND pao_ppa_acao.exercicio = pao.exercicio                     
       
            JOIN ppa.acao AS ppa_acao                                           
              ON ppa_acao.cod_acao = pao_ppa_acao.cod_acao
			  
            JOIN orcamento.conta_despesa AS conta_despesa
              ON conta_despesa.exercicio = despesa.exercicio
             AND conta_despesa.cod_conta = despesa.cod_conta
			 
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
              
           WHERE licitacao.cod_entidade in (" . $this->getDado('entidades') . ")
             AND TO_DATE(homologacao.timestamp::varchar,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')   
             AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
             AND ( licitacao.cod_modalidade = 8 OR licitacao.cod_modalidade = 9 )
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
               , exercicio_processo
               , num_processo
               , tipo_processo
               , cod_orgao
               , cod_subunidade
               , cod_funcao
               , cod_subfuncao
               , num_programa
               , num_acao
               , id_sub_acao
               , natureza_despesa
               , cod_font_recurso
               , vl_recurso
               , mapa_cotacao.cod_cotacao
			   , config_licitacao.exercicio_licitacao
               , config_licitacao.num_licitacao
        ORDER BY num_processo";
		
        return $stSql;
    }
    
    public function recuperaExportacao17(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao17",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao17()
    {
        $stSql = "
            SELECT 17 AS tipo_registro
                 , LPAD(orgao.valor::VARCHAR, 2, '0') AS cod_orgao_resp
                 , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                 , config_licitacao.exercicio_licitacao AS exercicio_processo
                 , config_licitacao.num_licitacao AS num_processo
				 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                        WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , documento_pessoa.tipo_documento AS tipo_documento
                 , documento_pessoa.num_documento AS num_documento
                 , sw_cgm_pessoa_juridica.insc_estadual AS num_inscricao_estadual
                 , sw_uf.sigla_uf AS uf_inscricao_estadual
				 
                 , CASE WHEN certificacao_documentos.cod_documento = 5 AND documento_pessoa.tipo_documento = 2 THEN certificacao_documentos.num_certificacao::VARCHAR 	      ELSE '' END AS num_certidao_regularidade_inss
                 , CASE WHEN certificacao_documentos.cod_documento = 5 AND documento_pessoa.tipo_documento = 2 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy')  ELSE '' END AS dt_emissao_certidao_regularidade_inss
                 , CASE WHEN certificacao_documentos.cod_documento = 5 AND documento_pessoa.tipo_documento = 2 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_certidao_regularidade_inss
                 , CASE WHEN certificacao_documentos.cod_documento = 6 AND documento_pessoa.tipo_documento = 2 THEN certificacao_documentos.num_certificacao::VARCHAR 	      ELSE '' END AS num_certidao_regularidade_fgts
                 , CASE WHEN certificacao_documentos.cod_documento = 6 AND documento_pessoa.tipo_documento = 2 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy')  ELSE '' END AS dt_emissao_certidao_regularidade_fgts
                 , CASE WHEN certificacao_documentos.cod_documento = 6 AND documento_pessoa.tipo_documento = 2 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_certidao_regularidade_fgts
                 , CASE WHEN certificacao_documentos.cod_documento = 7 AND documento_pessoa.tipo_documento = 2 THEN certificacao_documentos.num_certificacao::VARCHAR         ELSE '' END AS num_cndt
                 , CASE WHEN certificacao_documentos.cod_documento = 7 AND documento_pessoa.tipo_documento = 2 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy')  ELSE '' END AS dt_emissao_cndt
                 , CASE WHEN certificacao_documentos.cod_documento = 7 AND documento_pessoa.tipo_documento = 2 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_cndt
				 
                 , CASE WHEN mapa.cod_tipo_licitacao = 2 THEN mapa_cotacao.cod_cotacao::VARCHAR
                        ELSE ' '
                    END AS num_lote
                 , mapa_item.cod_item AS cod_item
                 , mapa_item.quantidade AS quantidade
                 , mapa_item.vl_total::NUMERIC(14,4) AS valor_item
                 
            FROM licitacao.licitacao
            
            JOIN (SELECT *
                            FROM administracao.configuracao_entidade
                            WHERE configuracao_entidade.cod_modulo = 55
                              AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                        ) AS orgao
              ON orgao.valor::integer = licitacao.cod_entidade
            
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
              
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             
       LEFT JOIN licitacao.publicacao_edital
              ON publicacao_edital.num_edital = edital.num_edital
             AND publicacao_edital.exercicio = edital.exercicio
             
            JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico
              
       LEFT JOIN sw_cgm_pessoa_juridica
              ON sw_cgm_pessoa_juridica.numcgm = responsavel.numcgm
              
       LEFT JOIN ( SELECT num_documento, numcgm, tipo_documento
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
              
            JOIN sw_municipio
              ON sw_municipio.cod_municipio = responsavel.cod_municipio
             AND sw_municipio.cod_uf = responsavel.cod_uf
             
            JOIN sw_uf
              ON sw_uf.cod_uf = sw_municipio.cod_uf
              
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_cotacao
	      ON mapa.exercicio = mapa_cotacao.exercicio_mapa
             AND mapa.cod_mapa = mapa_cotacao.cod_mapa
            
            JOIN compras.mapa_item
              ON mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             
      INNER JOIN compras.julgamento
              ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao
             AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
           
      INNER JOIN compras.julgamento_item
              ON  julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             AND julgamento_item.ordem = 1

            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote
             AND (
                     SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                     WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                     AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                     AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                     AND homologacao.cod_item=homologacao_anulada.cod_item
                     AND homologacao.lote=homologacao_anulada.lote
                 ) IS NULL
             
            JOIN licitacao.licitacao_documentos
              ON licitacao_documentos.cod_licitacao = licitacao.cod_licitacao
             AND licitacao_documentos.cod_entidade = licitacao.cod_entidade
             AND licitacao_documentos.exercicio = licitacao.exercicio
             
            JOIN licitacao.documento
              ON documento.cod_documento = licitacao_documentos.cod_documento
              
       LEFT JOIN licitacao.certificacao_documentos
              ON certificacao_documentos.cod_documento = documento.cod_documento
			  
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

           WHERE licitacao.cod_entidade in (" . $this->getDado('entidades') . ")
             AND TO_DATE(homologacao.timestamp::varchar,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')   
             AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
             AND ( licitacao.cod_modalidade = 8 OR licitacao.cod_modalidade = 9 )
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
               , exercicio_processo
               , num_processo
               , tipo_processo
               , tipo_documento
               , documento_pessoa.num_documento
               , num_inscricao_estadual
               , uf_inscricao_estadual
               , num_certidao_regularidade_inss
               , dt_emissao_certidao_regularidade_inss
               , dt_validade_certidao_regularidade_inss
               , num_certidao_regularidade_fgts
               , dt_emissao_certidao_regularidade_fgts
               , dt_validade_certidao_regularidade_fgts
               , num_cndt
               , dt_emissao_cndt
               , dt_validade_cndt
               , num_lote
               , mapa_item.cod_item
               , mapa_item.quantidade
               , valor_item
               , mapa_cotacao.cod_cotacao
               , mapa.cod_tipo_licitacao
			   , config_licitacao.exercicio_licitacao
               , config_licitacao.num_licitacao
               
        ORDER BY num_processo";
		
        return $stSql;
    }
    
    public function recuperaExportacao18(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao18",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao18()
    {
        $stSql = "
            SELECT 18 AS tipo_registro
                 , LPAD(orgao.valor::VARCHAR, 2, '0') AS cod_orgao_resp
                 , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                 , config_licitacao.exercicio_licitacao AS exercicio_processo
                 , config_licitacao.num_licitacao AS num_processo
				 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                        WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , documento_pessoa.tipo_documento AS tipo_documento
                 , documento_pessoa.num_documento AS num_documento
                 , TO_CHAR (participante_certificacao.dt_registro, 'dd/mm/yyyy') AS dt_credenciamento
                 , CASE WHEN mapa.cod_tipo_licitacao = 2 THEN mapa_cotacao.cod_cotacao::VARCHAR
                        ELSE ' '
                    END AS num_lote
                 , mapa_item.cod_item AS cod_item
                 , sw_cgm_pessoa_juridica.insc_estadual AS num_inscricao_estadual
                 , sw_uf.sigla_uf AS uf_inscricao_estadual
                 , CASE WHEN certificacao_documentos.cod_documento = 5 THEN certificacao_documentos.num_certificacao ELSE 0 END AS num_certidao_regularidade_inss
                 , CASE WHEN certificacao_documentos.cod_documento = 5 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy') ELSE '' END AS dt_emissao_certidao_regularidade_inss
                 , CASE WHEN certificacao_documentos.cod_documento = 5 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_certidao_regularidade_inss
                 , CASE WHEN certificacao_documentos.cod_documento = 6 THEN certificacao_documentos.num_certificacao ELSE 0 END AS num_certidao_regularidade_fgts
                 , CASE WHEN certificacao_documentos.cod_documento = 6 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy') ELSE '' END AS dt_emissao_certidao_regularidade_fgts
                 , CASE WHEN certificacao_documentos.cod_documento = 6 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_certidao_regularidade_fgts
                 , CASE WHEN certificacao_documentos.cod_documento = 7 THEN certificacao_documentos.num_certificacao ELSE 0 END AS num_cndt
                 , CASE WHEN certificacao_documentos.cod_documento = 7 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy') ELSE '' END AS dt_emissao_cndt
                 , CASE WHEN certificacao_documentos.cod_documento = 7 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_cndt
                 
            FROM licitacao.licitacao
            
            JOIN (SELECT *
                            FROM administracao.configuracao_entidade
                            WHERE configuracao_entidade.cod_modulo = 55
                              AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                        ) AS orgao
              ON orgao.valor::integer = licitacao.cod_entidade
            
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
              
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             
       LEFT JOIN licitacao.publicacao_edital
              ON publicacao_edital.num_edital = edital.num_edital
             AND publicacao_edital.exercicio = edital.exercicio
             
            JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico
              
       LEFT JOIN sw_cgm_pessoa_juridica
              ON sw_cgm_pessoa_juridica.numcgm = responsavel.numcgm
              
       LEFT JOIN ( SELECT num_documento, numcgm, tipo_documento
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
              
            JOIN sw_municipio
              ON sw_municipio.cod_municipio = responsavel.cod_municipio
             AND sw_municipio.cod_uf = responsavel.cod_uf
             
            JOIN sw_uf
              ON sw_uf.cod_uf = sw_municipio.cod_uf
              
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa

            JOIN compras.mapa_cotacao
	      ON mapa.exercicio = mapa_cotacao.exercicio_mapa
             AND mapa.cod_mapa = mapa_cotacao.cod_mapa
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             
      INNER JOIN compras.julgamento
              ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao
             AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
           
      INNER JOIN compras.julgamento_item
              ON  julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             AND julgamento_item.ordem = 1

            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote
             AND (
                     SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                     WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                     AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                     AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                     AND homologacao.cod_item=homologacao_anulada.cod_item
                     AND homologacao.lote=homologacao_anulada.lote
                 ) IS NULL
             
            JOIN licitacao.licitacao_documentos
              ON licitacao_documentos.cod_licitacao = licitacao.cod_licitacao
             AND licitacao_documentos.cod_entidade = licitacao.cod_entidade
             AND licitacao_documentos.exercicio = licitacao.exercicio
             
            JOIN licitacao.documento
              ON documento.cod_documento = licitacao_documentos.cod_documento
              
       LEFT JOIN licitacao.certificacao_documentos
              ON certificacao_documentos.cod_documento = documento.cod_documento
              
       LEFT JOIN licitacao.participante_certificacao
              ON participante_certificacao.num_certificacao = certificacao_documentos.num_certificacao
             AND participante_certificacao.exercicio = certificacao_documentos.exercicio
             AND participante_certificacao.cgm_fornecedor = certificacao_documentos.cgm_fornecedor
			 
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
             
           WHERE licitacao.cod_entidade in (" . $this->getDado('entidades') . ")
             AND TO_DATE(homologacao.timestamp::varchar,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')   
             AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
             AND ( licitacao.cod_modalidade = 8 OR licitacao.cod_modalidade = 9 )
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
               , exercicio_processo
               , num_processo
               , tipo_processo
               , tipo_documento
               , documento_pessoa.num_documento
               , dt_credenciamento
               , num_lote
	       , mapa_item.cod_item
	       , num_inscricao_estadual
	       , uf_inscricao_estadual
               , num_certidao_regularidade_inss
               , dt_emissao_certidao_regularidade_inss
               , dt_validade_certidao_regularidade_inss
               , num_certidao_regularidade_fgts
               , dt_emissao_certidao_regularidade_fgts
               , dt_validade_certidao_regularidade_fgts
               , num_cndt
               , dt_emissao_cndt
               , dt_validade_cndt
               , mapa.cod_tipo_licitacao
			   , config_licitacao.exercicio_licitacao
               , config_licitacao.num_licitacao
        ORDER BY num_processo
        ";
        return $stSql;
    }
	
	public function __destruct(){}

    
}//fim da classe
