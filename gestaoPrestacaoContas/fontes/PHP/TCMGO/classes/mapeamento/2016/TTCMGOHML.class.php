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
    * Classe de mapeamento da tabela TTCEMGO
    * Data de Criação: 14/03/2014

    * @author Analista: Luciana
    * @author Desenvolvedor: Jean da Silva
    *
    * $Id: TTCMGOHML.class.php 65190 2016-04-29 19:36:51Z michel $

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMGOHML extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
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
    public function recupera10(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stGroup = "
            GROUP BY tiporegistro 
                   , cod_orgao
                   , cod_unidadesub
                   , homologacao.exercicio_licitacao
                   , nro_processolicitatorio
                   , nro_lote
                   , cotacao_item.quantidade
                   , desc_item
                   , valor_unitario
                   , documento_pessoa.tipo_documento
                   , documento_pessoa.num_documento
                   , catalogo_item.cod_unidade
                   , mapa_item.exercicio
                   , mapa_item.cod_mapa
                   , mapa_item.lote
                   , mapa_item.cod_item ";
        $stOrdem = "
            ORDER BY tiporegistro
                   , cod_orgao
                   , cod_unidadesub
                   , exercicio_licitacao
                   , nro_processolicitatorio
                   , nro_lote
                   , num_item
                   , tipo_documento
                   , nro_documento ";

        $stSql = $this->montaRecupera10().$stGroup.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        return $obErro;
    }

    /** Para descobrir o cod_item referente ao num_item, deverar pesquisar atravez dessa consulta. Terá que utilizar os filtros do : mapa_item.exercicio, mapa_item.cod_mapa, mapa_item.lote.
     *     SELECT DISTINCT exercicio
     *          , cod_mapa
     *          , lote
     *          , cod_item
     *          , ROW_NUMBER() OVER(PARTITION BY exercicio, cod_mapa, lote ORDER BY exercicio, cod_mapa, lote, cod_item) AS num_item
     *       FROM compras.mapa_item
     *   ORDER BY exercicio
     *          , cod_mapa
     *          , lote
     *          , cod_item;
     *
     */
    public function montaRecupera10()
    {
        $stSql = "
              SELECT '10' AS tiporegistro
                   , despesa.num_orgao AS cod_orgao
                   , despesa.num_unidade AS cod_unidadesub
                   , homologacao.exercicio_licitacao
                   , homologacao.exercicio_licitacao || LPAD(homologacao.cod_entidade::VARCHAR,2,'0') || LPAD(homologacao.cod_modalidade::VARCHAR,2,'0') || LPAD(homologacao.cod_licitacao::VARCHAR,4,'0') || LPAD(licitacao.tipo_chamada_publica::VARCHAR,2,'0') AS nro_processolicitatorio
                   , CASE WHEN mapa.cod_tipo_licitacao = 2
                          THEN homologacao.lote
                          ELSE 1
                      END AS nro_lote
                   , ROW_NUMBER() OVER(PARTITION BY mapa_item.exercicio,mapa_item.cod_mapa,mapa_item.lote ORDER BY mapa_item.exercicio,mapa_item.cod_mapa,mapa_item.lote,mapa_item.cod_item) AS num_item
                   , cotacao_item.quantidade
                   , remove_acentos(REPLACE(REPLACE(catalogo_item.descricao,'”','\"'),'–','-')) as desc_item
                   , (cotacao_fornecedor_item.vl_cotacao / cotacao_item.quantidade)::numeric(14,4) as valor_unitario
                   , documento_pessoa.tipo_documento
                   , documento_pessoa.num_documento as nro_documento
                   , catalogo_item.cod_unidade as unidade
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
          INNER JOIN almoxarifado.catalogo_item
                  ON catalogo_item.cod_item = cotacao_item.cod_item
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
          INNER JOIN (SELECT num_documento
                           , numcgm
                           , tipo_documento
                        FROM (SELECT cpf AS num_documento
                                   , numcgm
                                   , 1 AS tipo_documento
                                FROM sw_cgm_pessoa_fisica
                               UNION
                              SELECT cnpj AS num_documento
                                   , numcgm
                                   , 2 AS tipo_documento
                                FROM sw_cgm_pessoa_juridica
                             ) AS tabela
                    GROUP BY numcgm
                           , num_documento
                           , tipo_documento
                     ) AS documento_pessoa
                  ON documento_pessoa.numcgm = julgamento_item.cgm_fornecedor
          INNER JOIN compras.mapa       
                  ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                 AND mapa.cod_mapa  = mapa_cotacao.cod_mapa 
          INNER JOIN compras.mapa_item
                  ON mapa_item.exercicio = mapa.exercicio
                 AND mapa_item.cod_mapa  = mapa.cod_mapa
                 AND mapa_item.cod_item  = cotacao_fornecedor_item.cod_item
                 AND mapa_item.lote      = cotacao_fornecedor_item.lote
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
          INNER JOIN orcamento.unidade
                  ON unidade.exercicio = despesa.exercicio
                 AND unidade.num_unidade = despesa.num_unidade
                 AND unidade.num_orgao = despesa.num_orgao
           LEFT JOIN licitacao.licitacao_anulada
                  ON licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                 AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                 AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                 AND licitacao_anulada.exercicio      = licitacao.exercicio 
               WHERE licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
                 AND licitacao.exercicio = '".$this->getDado('exercicio')."'
                 AND TO_DATE(homologacao.timestamp::VARCHAR, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                               AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                 AND licitacao.cod_modalidade NOT IN (8,9)
                 AND licitacao_anulada.cod_licitacao IS NULL
        ";
        return $stSql;
    }

    public function recupera20(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGroup = "
            GROUP BY tiporegistro
                   , cod_orgao
                   , cod_unidadesub
                   , homologacao.exercicio_licitacao
                   , nro_processolicitatorio
                   , nro_lote
                   , documento_pessoa.num_documento
                   , tipo_documento
                   , mapa_item.exercicio
                   , mapa_item.cod_mapa
                   , mapa_item.lote
                   , mapa_item.cod_item ";
        $stOrdem = "
            ORDER BY tiporegistro
                   , cod_orgao
                   , cod_unidadesub
                   , exercicio_licitacao
                   , nro_processolicitatorio
                   , nro_lote
                   , num_item
                   , tipo_documento
                   , nro_documento ";
        $stSql = $this->montaRecupera20().$stGroup.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        return $obErro;
    }

    /** Para descobrir o cod_item referente ao num_item, deverar pesquisar atravez dessa consulta. Terá que utilizar os filtros do : mapa_item.exercicio, mapa_item.cod_mapa, mapa_item.lote.
     *     SELECT DISTINCT exercicio
     *          , cod_mapa
     *          , lote
     *          , cod_item
     *          , ROW_NUMBER() OVER(PARTITION BY exercicio, cod_mapa, lote ORDER BY exercicio, cod_mapa, lote, cod_item) AS num_item
     *       FROM compras.mapa_item
     *   ORDER BY exercicio
     *          , cod_mapa
     *          , lote
     *          , cod_item;
     *
     */
    public function montaRecupera20()
    {
        $stSql  = "
              SELECT '20'::char(2) AS tiporegistro
                   , despesa.num_orgao AS cod_orgao
                   , despesa.num_unidade AS cod_unidadesub
                   , homologacao.exercicio_licitacao
                   , homologacao.exercicio_licitacao || LPAD(homologacao.cod_entidade::VARCHAR,2,'0') || LPAD(homologacao.cod_modalidade::VARCHAR,2,'0') || LPAD(homologacao.cod_licitacao::VARCHAR,4,'0') || LPAD(licitacao.tipo_chamada_publica::VARCHAR,2,'0') as nro_processolicitatorio
                   , CASE WHEN mapa.cod_tipo_licitacao = 2
                          THEN homologacao.lote
                          ELSE 1
                      END AS nro_lote
                   , ROW_NUMBER() OVER(PARTITION BY mapa_item.exercicio,mapa_item.cod_mapa,mapa_item.lote ORDER BY mapa_item.exercicio,mapa_item.cod_mapa,mapa_item.lote,mapa_item.cod_item) AS num_item
                   , documento_pessoa.tipo_documento
                   , documento_pessoa.num_documento as nro_documento
                   , LPAD('0',6,'0')::numeric(14,2) as perc_desconto
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
          INNER JOIN licitacao.participante_documentos      
                  ON participante_documentos.cod_licitacao   = participante.cod_licitacao
                 AND participante_documentos.cgm_fornecedor  = participante.cgm_fornecedor
                 AND participante_documentos.cod_modalidade  = participante.cod_modalidade
                 AND participante_documentos.cod_entidade    = participante.cod_entidade
                 AND participante_documentos.exercicio       = participante.exercicio 
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
                  ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                 AND cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                 AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                 AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                 AND cotacao_fornecedor_item.lote           = julgamento_item.lote
          INNER JOIN (SELECT num_documento
                           , numcgm
                           , tipo_documento
                        FROM (SELECT cpf AS num_documento
                                   , numcgm
                                   , 1 AS tipo_documento
                                FROM sw_cgm_pessoa_fisica
                               UNION
                              SELECT cnpj AS num_documento
                                   , numcgm
                                   , 2 AS tipo_documento
                                FROM sw_cgm_pessoa_juridica
                             ) AS tabela
                    GROUP BY numcgm
                           , num_documento
                           , tipo_documento
                     ) AS documento_pessoa
                  ON documento_pessoa.numcgm = julgamento_item.cgm_fornecedor
          INNER JOIN compras.mapa       
                  ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                 AND mapa.cod_mapa  = mapa_cotacao.cod_mapa 
          INNER JOIN compras.mapa_item
                  ON mapa_item.exercicio = mapa.exercicio
                 AND mapa_item.cod_mapa  = mapa.cod_mapa
                 AND mapa_item.cod_item  = cotacao_fornecedor_item.cod_item
                 AND mapa_item.lote      = cotacao_fornecedor_item.lote
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
          INNER JOIN orcamento.unidade
                  ON unidade.exercicio   = despesa.exercicio
                 AND unidade.num_unidade = despesa.num_unidade
                 AND unidade.num_orgao   = despesa.num_orgao
           LEFT JOIN licitacao.licitacao_anulada
                  ON licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                 AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                 AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                 AND licitacao_anulada.exercicio      = licitacao.exercicio 
               WHERE licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
                 AND licitacao.exercicio = '".$this->getDado('exercicio')."'
                 AND TO_DATE(homologacao.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                               AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                 AND licitacao.cod_modalidade NOT IN (8,9)
                 AND licitacao_anulada.cod_licitacao IS NULL
        ";
        
        return $stSql;
    }

    public function recupera30(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stGroup = " 
            GROUP BY tiporegistro
                   , cod_orgao
                   , cod_unidadesub
                   , homologacao.exercicio_licitacao
                   , nro_processolicitatorio
                   , nro_lote
                   , tipo_documento
                   , documento_pessoa.num_documento
                   , mapa_item.exercicio
                   , mapa_item.cod_mapa
                   , mapa_item.lote
                   , mapa_item.cod_item 
                   , dt_homologacao
                   , dt_adjudicacao
        ";
        $stOrdem = "
            ORDER BY tiporegistro
                   , cod_orgao
                   , cod_unidadesub
                   , exercicio_licitacao
                   , nro_processolicitatorio
                   , nro_lote
                   , num_item
                   , tipo_documento
                   , nro_documento 
        ";

        $stSql = $this->montaRecupera30().$stGroup.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    /** Para descobrir o cod_item referente ao num_item, deverar pesquisar atravez dessa consulta. Terá que utilizar os filtros do : mapa_item.exercicio, mapa_item.cod_mapa, mapa_item.lote.
     *     SELECT DISTINCT exercicio
     *          , cod_mapa
     *          , lote
     *          , cod_item
     *          , ROW_NUMBER() OVER(PARTITION BY exercicio, cod_mapa, lote ORDER BY exercicio, cod_mapa, lote, cod_item) AS num_item
     *       FROM compras.mapa_item
     *   ORDER BY exercicio
     *          , cod_mapa
     *          , lote
     *          , cod_item;
     *
     */
    public function montaRecupera30(){
        $stSql  = "
              SELECT '30'::char(2) AS tiporegistro
                   , despesa.num_orgao AS cod_orgao
                   , despesa.num_unidade AS cod_unidadesub
                   , homologacao.exercicio_licitacao
                   , homologacao.exercicio_licitacao||LPAD(homologacao.cod_entidade::VARCHAR,2,'0')||LPAD(homologacao.cod_modalidade::VARCHAR,2,'0')||LPAD(homologacao.cod_licitacao::VARCHAR,4,'0')||LPAD(licitacao.tipo_chamada_publica::VARCHAR,2,'0') as nro_processolicitatorio
                   , TO_CHAR(TO_DATE(homologacao.timestamp::varchar, 'YYYY-MM-DD'),'DDMMYYYY') as dt_homologacao
                   , TO_CHAR(TO_DATE(adjudicacao.timestamp::varchar, 'YYYY-MM-DD'),'DDMMYYYY') as dt_adjudicacao
                   , documento_pessoa.tipo_documento
                   , documento_pessoa.num_documento as nro_documento
                   , ROW_NUMBER() OVER(PARTITION BY mapa_item.exercicio,mapa_item.cod_mapa,mapa_item.lote ORDER BY mapa_item.exercicio,mapa_item.cod_mapa,mapa_item.lote,mapa_item.cod_item) AS num_item
                   , CASE WHEN mapa.cod_tipo_licitacao = 2
                          THEN homologacao.lote
                          ELSE 1
                      END AS nro_lote
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
                  ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                 AND cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                 AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                 AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                 AND cotacao_fornecedor_item.lote           = julgamento_item.lote
          INNER JOIN (SELECT num_documento
                           , numcgm
                           , tipo_documento
                        FROM (SELECT cpf AS num_documento
                                   , numcgm
                                   , 1 AS tipo_documento
                                FROM sw_cgm_pessoa_fisica
                               UNION
                              SELECT cnpj AS num_documento
                                   , numcgm
                                   , 2 AS tipo_documento
                                FROM sw_cgm_pessoa_juridica
                             ) AS tabela
                    GROUP BY numcgm
                           , num_documento
                           , tipo_documento
                     ) AS documento_pessoa
                  ON documento_pessoa.numcgm = julgamento_item.cgm_fornecedor
          INNER JOIN compras.mapa       
                  ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                 AND mapa.cod_mapa  = mapa_cotacao.cod_mapa 
          INNER JOIN compras.mapa_item
                  ON mapa_item.exercicio = mapa.exercicio
                 AND mapa_item.cod_mapa  = mapa.cod_mapa
                 AND mapa_item.cod_item  = cotacao_fornecedor_item.cod_item
                 AND mapa_item.lote      = cotacao_fornecedor_item.lote
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
           LEFT JOIN licitacao.licitacao_anulada
                  ON licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                 AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                 AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                 AND licitacao_anulada.exercicio      = licitacao.exercicio 
          INNER JOIN orcamento.unidade
                  ON unidade.exercicio   = despesa.exercicio
                 AND unidade.num_unidade = despesa.num_unidade
                 AND unidade.num_orgao   = despesa.num_orgao
               WHERE licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
                 AND licitacao.exercicio = '".$this->getDado('exercicio')."'
                 AND TO_DATE(homologacao.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                               AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                 AND licitacao.cod_modalidade NOT IN (8,9)
                 AND licitacao_anulada.cod_licitacao IS NULL
        ";
        return $stSql;
    }

}

?>