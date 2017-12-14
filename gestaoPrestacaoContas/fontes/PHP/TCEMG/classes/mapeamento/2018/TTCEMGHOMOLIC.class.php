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
    * Classe de mapeamento da tabela TTCEMG
    * Data de Criação: 14/03/2014

    * @author Analista: Luciana
    * @author Desenvolvedor: Evandro Melos

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGHOMOLIC extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGHOMOLIC()
    {
        parent::Persistente();
    }


    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosHOMOLIC10.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosHOMOLIC10(&$rsRecordSet, $boTransacao = ""){
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGroup = "GROUP BY tiporegistro 
                            , cod_orgao
                            , cod_unidadesub
                            , homologacao.exercicio_licitacao
                            , nro_processolicitatorio
                            , tipo_documento
                            , nro_lote
                            , cotacao_fornecedor_item.cod_item
                            , cotacao_item.quantidade
                            , cotacao_fornecedor_item.vl_cotacao
                            , licitacao.cod_licitacao
                            , licitacao.cod_modalidade
                            , sw_cgm_pessoa_juridica.cnpj
                            , sw_cgm_pessoa_fisica.cpf
                            , config_licitacao.exercicio_licitacao
                            , config_licitacao.num_licitacao
                    ";
        $stOrdem = " ORDER BY nro_processolicitatorio
                            , licitacao.cod_licitacao
                            , licitacao.cod_modalidade
                            , cod_unidadesub
                            , tiporegistro 
                            , cod_orgao
                            , exercicio_licitacao
                            , tipo_documento
                            , nro_documento
                            , nro_lote
                            , cod_item
                    ";
        $stSql = $this->montaRecuperaDadosHOMOLIC10().$stGroup.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosHOMOLIC10(){
        $stSql  = "  SELECT '10'::char(2) AS tiporegistro
                          , LPAD((SELECT valor 
                                    FROM administracao.configuracao_entidade 
                                   WHERE exercicio = '".$this->getDado('exercicio')."' 
                                     AND parametro like 'tcemg_codigo_orgao_entidade_sicom' 
                                     AND cod_entidade = licitacao.cod_entidade), 2, '0') AS cod_orgao
                           , CASE WHEN homologacao.exercicio_licitacao <= '2013'
                                  THEN ''
                                  ELSE LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||lpad(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0')
                             END AS cod_unidadesub
                           , config_licitacao.exercicio_licitacao
                           , config_licitacao.num_licitacao AS nro_processolicitatorio
                           , CASE WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL THEN 2
                                   WHEN sw_cgm_pessoa_fisica.cpf  IS NOT NULL THEN 1
                                   ELSE 3
                              END AS tipo_documento 
                           , CASE WHEN mapa.cod_tipo_licitacao = 2
                                  THEN homologacao.lote
                                  ELSE NULL
                              END AS nro_lote
                            , cotacao_fornecedor_item.cod_item
                            , cotacao_item.quantidade
                            , (cotacao_fornecedor_item.vl_cotacao / cotacao_item.quantidade)::numeric(14,4) as valor_unitario
                            , CASE WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                   THEN sw_cgm_pessoa_juridica.cnpj
                                   ELSE sw_cgm_pessoa_fisica.cpf 
                              END AS nro_documento

                    FROM licitacao.homologacao
                    
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
                      ON cotacao_licitacao.cod_licitacao       = adjudicacao.cod_licitacao
                     AND cotacao_licitacao.cod_modalidade      = adjudicacao.cod_modalidade
                     AND cotacao_licitacao.cod_entidade        = adjudicacao.cod_entidade
                     AND cotacao_licitacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                     AND cotacao_licitacao.lote                = adjudicacao.lote
                     AND cotacao_licitacao.cod_cotacao         = adjudicacao.cod_cotacao
                     AND cotacao_licitacao.cod_item            = adjudicacao.cod_item
                     AND cotacao_licitacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                     AND cotacao_licitacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor 
                        
              INNER JOIN licitacao.licitacao 
                      ON licitacao.cod_licitacao  = cotacao_licitacao.cod_licitacao
                     AND licitacao.cod_modalidade = cotacao_licitacao.cod_modalidade
                     AND licitacao.cod_entidade   = cotacao_licitacao.cod_entidade
                     AND licitacao.exercicio      = cotacao_licitacao.exercicio_licitacao

              INNER JOIN compras.cotacao_fornecedor_item
                      ON cotacao_fornecedor_item.cgm_fornecedor = cotacao_licitacao.cgm_fornecedor
                     AND cotacao_fornecedor_item.cod_cotacao    = cotacao_licitacao.cod_cotacao
                     AND cotacao_fornecedor_item.exercicio      = cotacao_licitacao.exercicio_cotacao
                     AND cotacao_fornecedor_item.lote           = cotacao_licitacao.lote
                     AND cotacao_fornecedor_item.cod_item       = cotacao_licitacao.cod_item
                     
              INNER JOIN compras.cotacao_item
                      ON cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
                     AND cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                     AND cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item
                     AND cotacao_item.lote        = cotacao_fornecedor_item.lote 
                        
              INNER JOIN compras.cotacao
                      ON cotacao.exercicio   = cotacao_item.exercicio
                     AND cotacao.cod_cotacao = cotacao_item.cod_cotacao
                        
              INNER JOIN compras.mapa_cotacao
                      ON mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                     AND mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                        
              INNER JOIN compras.julgamento_item
                      ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                     AND cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                     AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                     AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                     AND cotacao_fornecedor_item.lote           = julgamento_item.lote
                    
              INNER JOIN licitacao.participante
                      ON participante.cod_licitacao  = licitacao.cod_licitacao
                     AND participante.cod_modalidade = licitacao.cod_modalidade
                     AND participante.cod_entidade   = licitacao.cod_entidade
                     AND participante.exercicio      = licitacao.exercicio
                     AND participante.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                     
               LEFT JOIN sw_cgm_pessoa_juridica
                      ON sw_cgm_pessoa_juridica.numcgm = participante.cgm_fornecedor
               
               LEFT JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = participante.cgm_fornecedor
               
              INNER JOIN compras.fornecedor
                      ON fornecedor.cgm_fornecedor = participante.cgm_fornecedor
                     
              INNER JOIN compras.mapa       
                      ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                     AND mapa.cod_mapa  = mapa_cotacao.cod_mapa 
                        
              INNER JOIN compras.mapa_item
                      ON mapa_item.exercicio = mapa.exercicio
                     AND mapa_item.cod_mapa  = mapa.cod_mapa
                     AND mapa_item.cod_item  = cotacao_fornecedor_item.cod_item
                     AND mapa_item.lote      = cotacao_fornecedor_item.lote
                 
              INNER JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('cod_entidade')."')
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
                        
                   WHERE licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
                     AND TO_DATE(homologacao.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                     AND licitacao.cod_modalidade NOT IN (8,9)
                     AND NOT EXISTS( SELECT 1 FROM licitacao.licitacao_anulada
                                     WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                                     AND licitacao_anulada.cod_modalidade   = licitacao.cod_modalidade
                                     AND licitacao_anulada.cod_entidade     = licitacao.cod_entidade
                                     AND licitacao_anulada.exercicio        = licitacao.exercicio )
        ";
        
        return $stSql;
    }


    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosHOMOLIC20.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
      // detalhamento 20 foi removido porque não tem os filtros necessários ainda.
    /*
    public function recuperaDadosHOMOLIC20(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGroup = " GROUP BY tiporegistro 
                            , cod_orgao
                            , cod_unidadesub
                            , homologacao.exercicio_licitacao
                            , nro_processolicitatorio
                            , tipo_documento
                            , participante_documentos.num_documento
                            , nro_lote
                            , cotacao_fornecedor_item.cod_item
                            , cotacao_item.quantidade
                            , cotacao_fornecedor_item.vl_cotacao
                    ";
        $stOrdem = " ORDER BY tiporegistro 
                            , cod_orgao
                            , cod_unidadesub
                            , exercicio_licitacao
                            , nro_processolicitatorio
                            , tipo_documento
                            , nro_lote
                            , cod_item
                    ";
        $stSql = $this->montaRecuperaDadosHOMOLIC20().$stGroup.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosHOMOLIC20(){
        $stSql  = " SELECT '20'::char(2) AS tiporegistro
                            ,LPAD((SELECT valor 
                                        FROM administracao.configuracao_entidade 
                                        WHERE exercicio = '".$this->getDado('exercicio')."' 
                                        AND parametro like 'tcemg_codigo_orgao_entidade_sicom' 
                                        AND cod_entidade = licitacao.cod_entidade), 2, '0') 
                              AS cod_orgao
                            , CASE WHEN homologacao.exercicio_licitacao <= '2013'
                                  THEN ''
                                  ELSE LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||lpad(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0')
                              END AS cod_unidadesub
                            , homologacao.exercicio_licitacao
                            , homologacao.exercicio_licitacao || LPAD(homologacao.cod_entidade::VARCHAR,2,'0') || LPAD(homologacao.cod_modalidade::VARCHAR,2,'0') || LPAD(homologacao.cod_licitacao::VARCHAR,4,'0') as nro_processolicitatorio
                            , CASE WHEN participante_documentos.cod_documento = 4 THEN 1
                                   WHEN participante_documentos.cod_documento = 8 THEN 2
                               END AS tipo_documento
                            , regexp_replace(participante_documentos.num_documento,'[.|/|\-]','','gi') as nro_documento
                             , CASE WHEN mapa.cod_tipo_licitacao = 2 THEN
                                homologacao.lote
                              ELSE
                                NULL
                              END AS nro_lote
                            , cotacao_fornecedor_item.cod_item
                            , cotacao_item.quantidade
                            , (cotacao_fornecedor_item.vl_cotacao / cotacao_item.quantidade)::numeric(14,4) as valor_unitario
                            , LPAD('0',6,'0')::numeric(14,2) as perc_desconto
                    FROM licitacao.homologacao
                    
                    JOIN licitacao.adjudicacao
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
                        
                    JOIN licitacao.cotacao_licitacao
                      ON cotacao_licitacao.cod_licitacao       = adjudicacao.cod_licitacao
                     AND cotacao_licitacao.cod_modalidade      = adjudicacao.cod_modalidade
                     AND cotacao_licitacao.cod_entidade        = adjudicacao.cod_entidade
                     AND cotacao_licitacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                     AND cotacao_licitacao.lote                = adjudicacao.lote
                     AND cotacao_licitacao.cod_cotacao         = adjudicacao.cod_cotacao
                     AND cotacao_licitacao.cod_item            = adjudicacao.cod_item
                     AND cotacao_licitacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                     AND cotacao_licitacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor 
                        
                    JOIN licitacao.licitacao 
                      ON licitacao.cod_licitacao  = cotacao_licitacao.cod_licitacao
                     AND licitacao.cod_modalidade = cotacao_licitacao.cod_modalidade
                     AND licitacao.cod_entidade   = cotacao_licitacao.cod_entidade
                     AND licitacao.exercicio      = cotacao_licitacao.exercicio_licitacao
                     
                    JOIN licitacao.participante
                      ON participante.cod_licitacao  = licitacao.cod_licitacao
                     AND participante.cod_modalidade = licitacao.cod_modalidade
                     AND participante.cod_entidade   = licitacao.cod_entidade
                     AND participante.exercicio      = licitacao.exercicio 
                        
                    JOIN licitacao.participante_documentos      
                      ON participante_documentos.cod_licitacao   = participante.cod_licitacao
                     AND participante_documentos.cgm_fornecedor  = participante.cgm_fornecedor
                     AND participante_documentos.cod_modalidade  = participante.cod_modalidade
                     AND participante_documentos.cod_entidade    = participante.cod_entidade
                     AND participante_documentos.exercicio       = participante.exercicio 
                        
                    JOIN compras.fornecedor
                      ON fornecedor.cgm_fornecedor = participante.cgm_fornecedor
                        
                    JOIN compras.cotacao_fornecedor_item
                      ON cotacao_fornecedor_item.cgm_fornecedor = cotacao_licitacao.cgm_fornecedor
                     AND cotacao_fornecedor_item.cod_cotacao    = cotacao_licitacao.cod_cotacao
                     AND cotacao_fornecedor_item.exercicio      = cotacao_licitacao.exercicio_cotacao
                     AND cotacao_fornecedor_item.lote           = cotacao_licitacao.lote
                     AND cotacao_fornecedor_item.cod_item       = cotacao_licitacao.cod_item
                     
                    JOIN compras.cotacao_item
                      ON cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
                     AND cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                     AND cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item
                     AND cotacao_item.lote        = cotacao_fornecedor_item.lote 
                        
                    JOIN compras.cotacao
                      ON cotacao.exercicio   = cotacao_item.exercicio
                     AND cotacao.cod_cotacao = cotacao_item.cod_cotacao
                        
                    JOIN compras.mapa_cotacao
                      ON mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                     AND mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                        
                    JOIN compras.julgamento_item
                      ON  cotacao_fornecedor_item.exercicio       = julgamento_item.exercicio
                     AND   cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                     AND   cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                     AND   cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                     AND   cotacao_fornecedor_item.lote           = julgamento_item.lote
                     
                    JOIN compras.mapa       
                      ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                     AND mapa.cod_mapa  = mapa_cotacao.cod_mapa 
                        
                    JOIN  compras.mapa_item
                      ON  mapa_item.exercicio = mapa.exercicio
                     AND  mapa_item.cod_mapa  = mapa.cod_mapa
                     AND  mapa_item.cod_item  = cotacao_fornecedor_item.cod_item
                     AND  mapa_item.lote      = cotacao_fornecedor_item.lote
                     
                    JOIN compras.mapa_item_dotacao
                      ON mapa_item_dotacao.exercicio             = mapa_item.exercicio
                     AND mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
                     AND mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                     AND mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
                     AND mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
                     AND mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
                     AND mapa_item_dotacao.cod_item              = mapa_item.cod_item
                     AND mapa_item_dotacao.lote                  = mapa_item.lote
                     
                    JOIN compras.solicitacao_item_dotacao
                      ON solicitacao_item_dotacao.exercicio       = mapa_item_dotacao.exercicio_solicitacao
                     AND solicitacao_item_dotacao.cod_entidade    = mapa_item_dotacao.cod_entidade
                     AND solicitacao_item_dotacao.cod_solicitacao = mapa_item_dotacao.cod_solicitacao
                     AND solicitacao_item_dotacao.cod_centro      = mapa_item_dotacao.cod_centro
                     AND solicitacao_item_dotacao.cod_item        = mapa_item_dotacao.cod_item
                     AND solicitacao_item_dotacao.cod_conta       = mapa_item_dotacao.cod_conta
                     AND solicitacao_item_dotacao.cod_despesa     = mapa_item_dotacao.cod_despesa
                     
                    JOIN orcamento.despesa
                      ON despesa.exercicio   = solicitacao_item_dotacao.exercicio
                     AND despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
                    
                    WHERE licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
                    AND licitacao.exercicio = '".$this->getDado('exercicio')."'
                    AND TO_DATE(homologacao.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                    AND licitacao.cod_modalidade NOT IN (8,9)
                    AND NOT EXISTS( SELECT 1 FROM licitacao.licitacao_anulada
                                     WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                                     AND licitacao_anulada.cod_modalidade   = licitacao.cod_modalidade
                                     AND licitacao_anulada.cod_entidade     = licitacao.cod_entidade
                                     AND licitacao_anulada.exercicio        = licitacao.exercicio )
        ";

        return $stSql;
    }
*/
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosHOMOLIC30.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosHOMOLIC30(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
      /*  $stGroup = " GROUP BY tiporegistro 
                            , cod_orgao
                            , cod_unidadesub
                            , homologacao.exercicio_licitacao
                            , nro_processolicitatorio
                            , tipo_documento
                            , participante_documentos.num_documento
                            , nro_lote
                            , cotacao_fornecedor_item.cod_item
                            , cotacao_item.quantidade
                            , cotacao_fornecedor_item.vl_cotacao
                            ,homologacao.timestamp
                            ,adjudicacao.timestamp
                    ";*/
    //    $stOrdem = "ORDER BY nro_processolicitatorio";
        $stSql = $this->montaRecuperaDadosHOMOLIC30();//.$stGroup.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosHOMOLIC30(){
        $stSql  = " SELECT '30'::char(2) AS tiporegistro
                         , LPAD((SELECT valor 
                                   FROM administracao.configuracao_entidade 
                                  WHERE exercicio = '".$this->getDado('exercicio')."' 
                                    AND parametro like 'tcemg_codigo_orgao_entidade_sicom' 
                                    AND cod_entidade = licitacao.cod_entidade), 2, '0') AS cod_orgao
                          , CASE WHEN homologacao.exercicio_licitacao <= '2013'
                                 THEN ''
                                 ELSE LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||lpad(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0')
                            END AS cod_unidadesub
                          , config_licitacao.exercicio_licitacao
                          , config_licitacao.num_licitacao AS nro_processolicitatorio
                          , TO_CHAR(TO_DATE(homologacao.timestamp::varchar, 'YYYY-MM-DD'),'DDMMYYYY') as dt_homologacao
                          , TO_CHAR(TO_DATE(adjudicacao.timestamp::varchar, 'YYYY-MM-DD'),'DDMMYYYY') as dt_adjudicacao
                          
                    FROM licitacao.homologacao
                    
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
                      ON cotacao_licitacao.cod_licitacao       = adjudicacao.cod_licitacao
                     AND cotacao_licitacao.cod_modalidade      = adjudicacao.cod_modalidade
                     AND cotacao_licitacao.cod_entidade        = adjudicacao.cod_entidade
                     AND cotacao_licitacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                     AND cotacao_licitacao.lote                = adjudicacao.lote
                     AND cotacao_licitacao.cod_cotacao         = adjudicacao.cod_cotacao
                     AND cotacao_licitacao.cod_item            = adjudicacao.cod_item
                     AND cotacao_licitacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                     AND cotacao_licitacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor 
                        
              INNER JOIN licitacao.licitacao 
                      ON licitacao.cod_licitacao  = cotacao_licitacao.cod_licitacao
                     AND licitacao.cod_modalidade = cotacao_licitacao.cod_modalidade
                     AND licitacao.cod_entidade   = cotacao_licitacao.cod_entidade
                     AND licitacao.exercicio      = cotacao_licitacao.exercicio_licitacao
                     
              INNER JOIN licitacao.participante
                      ON participante.cod_licitacao  = licitacao.cod_licitacao
                     AND participante.cod_modalidade = licitacao.cod_modalidade
                     AND participante.cod_entidade   = licitacao.cod_entidade
                     AND participante.exercicio      = licitacao.exercicio 

              INNER JOIN compras.fornecedor
                      ON fornecedor.cgm_fornecedor = participante.cgm_fornecedor
                        
              INNER JOIN compras.cotacao_fornecedor_item
                      ON cotacao_fornecedor_item.cgm_fornecedor = cotacao_licitacao.cgm_fornecedor
                     AND cotacao_fornecedor_item.cod_cotacao    = cotacao_licitacao.cod_cotacao
                     AND cotacao_fornecedor_item.exercicio      = cotacao_licitacao.exercicio_cotacao
                     AND cotacao_fornecedor_item.lote           = cotacao_licitacao.lote
                     AND cotacao_fornecedor_item.cod_item       = cotacao_licitacao.cod_item
                     
              INNER JOIN compras.cotacao_item
                      ON cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
                     AND cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                     AND cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item
                     AND cotacao_item.lote        = cotacao_fornecedor_item.lote 
                        
              INNER JOIN compras.cotacao
                      ON cotacao.exercicio   = cotacao_item.exercicio
                     AND cotacao.cod_cotacao = cotacao_item.cod_cotacao
                        
              INNER JOIN compras.mapa_cotacao
                      ON mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                     AND mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                        
              INNER JOIN compras.julgamento_item
                      ON  cotacao_fornecedor_item.exercicio       = julgamento_item.exercicio
                     AND   cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                     AND   cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                     AND   cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                     AND   cotacao_fornecedor_item.lote           = julgamento_item.lote
                     
              INNER JOIN compras.mapa       
                      ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                     AND mapa.cod_mapa  = mapa_cotacao.cod_mapa 
                        
              INNER JOIN  compras.mapa_item
                      ON  mapa_item.exercicio = mapa.exercicio
                     AND  mapa_item.cod_mapa  = mapa.cod_mapa
                     AND  mapa_item.cod_item  = cotacao_fornecedor_item.cod_item
                     AND  mapa_item.lote      = cotacao_fornecedor_item.lote
                     
              INNER JOIN compras.mapa_item_dotacao
                      ON mapa_item_dotacao.exercicio             = mapa_item.exercicio
                     AND mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
                     AND mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                     AND mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
                     AND mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
                     AND mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
                     AND mapa_item_dotacao.cod_item              = mapa_item.cod_item
                     AND mapa_item_dotacao.lote                  = mapa_item.lote
                     
              INNER JOIN compras.solicitacao_item_dotacao
                      ON solicitacao_item_dotacao.exercicio       = mapa_item_dotacao.exercicio_solicitacao
                     AND solicitacao_item_dotacao.cod_entidade    = mapa_item_dotacao.cod_entidade
                     AND solicitacao_item_dotacao.cod_solicitacao = mapa_item_dotacao.cod_solicitacao
                     AND solicitacao_item_dotacao.cod_centro      = mapa_item_dotacao.cod_centro
                     AND solicitacao_item_dotacao.cod_item        = mapa_item_dotacao.cod_item
                     AND solicitacao_item_dotacao.cod_conta       = mapa_item_dotacao.cod_conta
                     AND solicitacao_item_dotacao.cod_despesa     = mapa_item_dotacao.cod_despesa
                     
              INNER JOIN orcamento.despesa
                      ON despesa.exercicio   = solicitacao_item_dotacao.exercicio
                     AND despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
                     
                 INNER JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('cod_entidade')."')
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
                    
              WHERE licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
                AND TO_DATE(homologacao.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                AND licitacao.cod_modalidade NOT IN (8,9)
                AND NOT EXISTS( SELECT 1 FROM licitacao.licitacao_anulada
                                WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                                AND licitacao_anulada.cod_modalidade   = licitacao.cod_modalidade
                                AND licitacao_anulada.cod_entidade     = licitacao.cod_entidade
                                AND licitacao_anulada.exercicio        = licitacao.exercicio )
            GROUP BY 1,2,3,4,5,6,7
            ORDER BY nro_processolicitatorio ";
            
        return $stSql;
    
    }
    
    public function __destruct(){}

}

?>