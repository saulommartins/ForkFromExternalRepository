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
    * @author Desenvolvedor: Arthur Cruz

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TLicitacaoParticipante.class.php 57380 2014-02-28 17:45:35Z diogo.zarpelon $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMGOAberturaLicitacao extends Persistente
{
    /**
      * Método Construtor
      * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaExportacao10(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaExportacao10().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );
    }
    
    public function montaRecuperaExportacao10()
    {
        $stSql = "
		      SELECT 10 as tipo_registro
                   , LPAD(despesa.num_orgao::VARCHAR,2, '0') as cod_orgao
                   , LPAD(despesa.num_unidade::VARCHAR,2, '0') AS cod_unidade
                   , licitacao.exercicio as exercicio_licitacao
		           , licitacao.exercicio::varchar||LPAD(licitacao.cod_entidade::varchar,2, '0')||LPAD(licitacao.cod_modalidade::varchar,2, '0')||LPAD(licitacao.cod_licitacao::varchar,4, '0') AS num_processo_licitatorio
                   , CASE WHEN modalidade.cod_modalidade = 1 THEN 1
                          WHEN modalidade.cod_modalidade = 2 THEN 2 
                          WHEN modalidade.cod_modalidade = 3 THEN 3
                          WHEN modalidade.cod_modalidade = 4 THEN 7
                          WHEN modalidade.cod_modalidade = 5 THEN 4
                          WHEN modalidade.cod_modalidade = 6 THEN 5
                          WHEN modalidade.cod_modalidade = 7 THEN 6
                      END AS cod_modalidade_licitacao
                   , modalidade.cod_modalidade AS num_modalidade
                   , CASE WHEN (modalidade.cod_modalidade = 3 AND licitacao.registro_precos = TRUE) THEN 2
                          WHEN (modalidade.cod_modalidade = 5 AND licitacao.registro_precos = TRUE) THEN 2
                          WHEN (modalidade.cod_modalidade = 6 AND licitacao.registro_precos = TRUE) THEN 2
                          WHEN (modalidade.cod_modalidade = 8 AND licitacao.tipo_chamada_publica <> 0) THEN 3
                          WHEN (modalidade.cod_modalidade = 9 AND licitacao.tipo_chamada_publica <> 0) THEN 3
                          WHEN (modalidade.cod_modalidade = 10 AND licitacao.tipo_chamada_publica <> 0) THEN 3
                          ELSE 1
                      END AS natureza_procedimento
                   , TO_CHAR(sw_processo.timestamp,'ddmmyyyy') AS dt_abertura
                   , TO_CHAR(edital.dt_aprovacao_juridico,'ddmmyyyy') AS dt_edital_convite
                   , (SELECT TO_CHAR(publicacao_edital.data_publicacao,'ddmmyyyy')
                        FROM licitacao.publicacao_edital
                  INNER JOIN licitacao.veiculos_publicidade
                          ON veiculos_publicidade.numcgm = publicacao_edital.numcgm
                  INNER JOIN licitacao.tipo_veiculos_publicidade
                          ON tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = veiculos_publicidade.cod_tipo_veiculos_publicidade
                       WHERE publicacao_edital.num_edital = edital.num_edital AND publicacao_edital.exercicio = edital.exercicio
                         AND tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = 5
                     ) AS dt_edital_publicacao_do
                   , TO_CHAR(edital.dt_entrega_propostas,'ddmmyyyy') AS dt_recebimento_doc
                   , criterio_julgamento.cod_criterio AS tipo_licitacao
                   , CASE WHEN tipo_objeto.cod_tipo_objeto = 2 THEN 1
                          WHEN tipo_objeto.cod_tipo_objeto = 1 THEN 2
                          WHEN tipo_objeto.cod_tipo_objeto = 3 THEN 4
                          WHEN tipo_objeto.cod_tipo_objeto = 4 THEN 6
                      END AS natureza_objeto
                   , remove_acentos(objeto.descricao)::VARCHAR(500) AS objeto
                   , CASE WHEN tipo_objeto.cod_tipo_objeto = 2
                          THEN licitacao.cod_regime
                          ELSE NULL
                      END AS regime_execucao_obras
                   , CASE WHEN (modalidade.cod_modalidade = 1)
                          THEN convidados.nrm_convidado
                      END AS num_convidado
                   , LPAD('',250,'') AS clausula_prorrogacao
                   , 1 AS undade_medida_prazo_execucao
                   , DATE(contrato.fim_execucao)-DATE(contrato.inicio_execucao) AS prazo_execucao
                   , remove_acentos(edital.condicoes_pagamento)::VARCHAR(100) AS forma_pagamento
                   , LPAD('',80,'') AS citerio_aceitabilidade
                   , 2 AS desconto_tabela
                FROM licitacao.licitacao
          INNER JOIN sw_processo
                  ON sw_processo.cod_processo  = licitacao.cod_processo
                 AND sw_processo.ano_exercicio = licitacao.exercicio_processo
          INNER JOIN licitacao.criterio_julgamento
                  ON criterio_julgamento.cod_criterio = licitacao.cod_criterio
          INNER JOIN licitacao.edital
                  ON edital.cod_licitacao       = licitacao.cod_licitacao
                 AND edital.cod_modalidade      = licitacao.cod_modalidade
                 AND edital.cod_entidade        = licitacao.cod_entidade
                 AND edital.exercicio_licitacao = licitacao.exercicio
                 AND (SELECT edital_anulado.num_edital
                        FROM licitacao.edital_anulado
			           WHERE edital_anulado.num_edital = edital.num_edital
                         AND edital_anulado.exercicio  = edital.exercicio
                     ) IS NULL
          INNER JOIN compras.objeto
                  ON objeto.cod_objeto = licitacao.cod_objeto
          INNER JOIN compras.tipo_objeto
                  ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
          INNER JOIN compras.modalidade
                  ON modalidade.cod_modalidade = licitacao.cod_modalidade
          INNER JOIN compras.mapa
                  ON mapa.exercicio = licitacao.exercicio_mapa
                 AND mapa.cod_mapa  = licitacao.cod_mapa
          INNER JOIN compras.tipo_licitacao
                  ON tipo_licitacao.cod_tipo_licitacao = licitacao.cod_tipo_licitacao
          INNER JOIN compras.mapa_solicitacao
                  ON mapa_solicitacao.exercicio = mapa.exercicio
                 AND mapa_solicitacao.cod_mapa  = mapa.cod_mapa
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
          INNER JOIN compras.cotacao_fornecedor_item
                  ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                 AND cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                 AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                 AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                 AND cotacao_fornecedor_item.lote           = julgamento_item.lote
          INNER JOIN compras.fornecedor
                  ON fornecedor.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
          INNER JOIN licitacao.homologacao
                  ON homologacao.cod_licitacao       = licitacao.cod_licitacao
                 AND homologacao.cod_modalidade      = licitacao.cod_modalidade
                 AND homologacao.cod_entidade        = licitacao.cod_entidade
                 AND homologacao.exercicio_licitacao = licitacao.exercicio
                 AND homologacao.cod_item            = julgamento_item.cod_item
                 AND (SELECT homologacao_anulada.num_homologacao
                        FROM licitacao.homologacao_anulada
			           WHERE homologacao_anulada.cod_licitacao       = licitacao.cod_licitacao
			             AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
			             AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
			             AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
			             AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
			             AND homologacao.cod_item                    = homologacao_anulada.cod_item
			         ) IS NULL
          INNER JOIN compras.solicitacao_homologada
                  ON solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                 AND solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                 AND solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao
          INNER JOIN compras.solicitacao_homologada_reserva
                  ON solicitacao_homologada_reserva.exercicio       = solicitacao_homologada.exercicio
                 AND solicitacao_homologada_reserva.cod_entidade    = solicitacao_homologada.cod_entidade
                 AND solicitacao_homologada_reserva.cod_solicitacao = solicitacao_homologada.cod_solicitacao
          INNER JOIN orcamento.despesa
                  ON despesa.exercicio  = solicitacao_homologada_reserva.exercicio
                 AND despesa.cod_despesa = solicitacao_homologada_reserva.cod_despesa
          INNER JOIN sw_cgm AS responsavel
                  ON responsavel.numcgm = edital.responsavel_juridico
	       LEFT JOIN licitacao.contrato_licitacao
                  ON contrato_licitacao.cod_licitacao       = licitacao.cod_licitacao
                 AND contrato_licitacao.cod_modalidade      = licitacao.cod_modalidade
                 AND contrato_licitacao.cod_entidade        = licitacao.cod_entidade
                 AND contrato_licitacao.exercicio_licitacao = licitacao.exercicio
           LEFT JOIN licitacao.contrato
                  ON contrato.num_contrato = contrato_licitacao.num_contrato
                 AND contrato.cod_entidade = contrato_licitacao.cod_entidade
                 AND contrato.exercicio    = contrato_licitacao.exercicio
           LEFT JOIN (SELECT COUNT (*) AS nrm_convidado
                           , participante.cod_licitacao
                           , participante.cod_modalidade 
                           , participante.cod_entidade 
                           , participante.exercicio 
                        FROM licitacao.participante
                    GROUP BY cod_licitacao
					       , cod_modalidade
						   , cod_entidade
						   , exercicio    
                     ) AS convidados
                  ON convidados.cod_licitacao  = licitacao.cod_licitacao
                 AND convidados.cod_modalidade = licitacao.cod_modalidade
                 AND convidados.cod_entidade   = licitacao.cod_entidade
                 AND convidados.exercicio      = licitacao.exercicio
               WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/".$this->getDado('inMes')."/".$this->getDado('stExercicio')."', 'dd/mm/yyyy')
                 AND last_day(TO_DATE('".$this->getDado('stExercicio')."'||'-'||'".$this->getDado('inMes')."'||'-'||'01','yyyy-mm-dd'))
                 AND licitacao.exercicio = '".$this->getDado('stExercicio')."'
                 AND licitacao.cod_entidade IN (".$this->getDado('stEntidades').")
                 AND licitacao.cod_modalidade NOT IN (8,9,10)
                 AND NOT EXISTS(SELECT 1
		    	                  FROM licitacao.licitacao_anulada
		    	                 WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
		    	                   AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                   AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                                   AND licitacao_anulada.exercicio      = licitacao.exercicio
                               )
            GROUP BY tipo_registro
                   , cod_orgao
                   , cod_unidade
                   , licitacao.exercicio
                   , num_processo_licitatorio
                   , cod_modalidade_licitacao
                   , num_modalidade
                   , natureza_procedimento
                   , dt_abertura
                   , dt_edital_convite
                   , dt_edital_publicacao_do
                   , dt_recebimento_doc
                   , tipo_licitacao
                   , natureza_objeto
                   , regime_execucao_obras
                   , num_convidado
                   , clausula_prorrogacao
                   , undade_medida_prazo_execucao
                   , prazo_execucao
                   , citerio_aceitabilidade
                   , desconto_tabela
                   , licitacao.cod_licitacao
                   , licitacao.cod_modalidade
                   , objeto.descricao
                   , edital.condicoes_pagamento
                   , contrato.inicio_execucao
                   , contrato.fim_execucao
				   
            ORDER BY licitacao.cod_licitacao
                   , licitacao.cod_modalidade
                   , num_processo_licitatorio
		";
        return $stSql;
    }
    
    public function recuperaExportacao11(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaExportacao11().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );
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
    public function montaRecuperaExportacao11()
    {
        $stSql = "
              SELECT 11 AS tipo_registro
                   , LPAD(despesa.num_orgao::VARCHAR,2, '0') AS cod_orgao
                   , LPAD(despesa.num_unidade::VARCHAR,2, '0') AS cod_unidade
                   , licitacao.exercicio AS exercicio_licitacao
                   , licitacao.exercicio::VARCHAR||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                   , mapa_item.lote AS num_lote
                   , ROW_NUMBER() OVER(PARTITION BY mapa_item.exercicio,mapa_item.cod_mapa,mapa_item.lote ORDER BY mapa_item.exercicio,mapa_item.cod_mapa,mapa_item.lote,mapa_item.cod_item) AS num_item
                   , TO_CHAR(cotacao.timestamp,'ddmmyyyy') AS dt_cotacao
                   , remove_acentos(REPLACE(REPLACE(catalogo_item.descricao,Chr('216'),'diametro'),Chr('8221'),'\"'))::VARCHAR(250) AS descricao_item
                   , (mapa_item.vl_total / mapa_item.quantidade)::numeric(14,2) AS vl_unitario
                   , mapa_item.quantidade::numeric(14,2) AS quantidade
                   , CASE CONCAT(unidade_medida.cod_unidade,unidade_medida.cod_grandeza)
                          WHEN '00' THEN 1
                          WHEN '17' THEN 1
                          WHEN '67' THEN 1
                          WHEN '18' THEN 2
                          WHEN '34' THEN 11
                          WHEN '44' THEN 12
                          WHEN '12' THEN 21
                          WHEN '22' THEN 22
                          WHEN '32' THEN 23
                          WHEN '25' THEN 31
                          WHEN '15' THEN 32
                          WHEN '23' THEN 41
                          WHEN '33' THEN 42
                          WHEN '31' THEN 51
                          WHEN '41' THEN 52
                          WHEN '51' THEN 53
                          WHEN '61' THEN 54
                          ELSE 1                                 
                      END AS unidade
                   , 0.00 AS vl_alienacao_bem
                FROM licitacao.licitacao
          INNER JOIN licitacao.participante
                  ON participante.cod_licitacao  = licitacao.cod_licitacao
                 AND participante.cod_modalidade = licitacao.cod_modalidade
                 AND participante.cod_entidade   = licitacao.cod_entidade 
                 AND participante.exercicio      = licitacao.exercicio
          INNER JOIN compras.mapa
                  ON mapa.exercicio = licitacao.exercicio_mapa
                 AND mapa.cod_mapa  = licitacao.cod_mapa
          INNER JOIN compras.mapa_solicitacao
                  ON mapa_solicitacao.exercicio = mapa.exercicio
                 AND mapa_solicitacao.cod_mapa  = mapa.cod_mapa
          INNER JOIN compras.mapa_item
                  ON mapa_item.exercicio             = mapa_solicitacao.exercicio
                 AND mapa_item.cod_entidade          = mapa_solicitacao.cod_entidade
                 AND mapa_item.cod_solicitacao       = mapa_solicitacao.cod_solicitacao
                 AND mapa_item.cod_mapa              = mapa_solicitacao.cod_mapa
                 AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
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
                  ON despesa.exercicio   = solicitacao_item_dotacao.exercicio
                 AND despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
          INNER JOIN compras.mapa_cotacao
                  ON mapa_cotacao.exercicio_mapa = mapa.exercicio
                 AND mapa_cotacao.cod_mapa       = mapa.cod_mapa
          INNER JOIN compras.cotacao
                  ON cotacao.exercicio   = mapa_cotacao.exercicio_cotacao
                 AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
          INNER JOIN compras.cotacao_item
                  ON cotacao_item.exercicio   = cotacao.exercicio
                 AND cotacao_item.cod_cotacao = cotacao.cod_cotacao
                 AND cotacao_item.cod_item    = mapa_item.cod_item
          INNER JOIN almoxarifado.catalogo_item
                  ON catalogo_item.cod_item = cotacao_item.cod_item
          INNER JOIN administracao.unidade_medida
                  ON unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
                 AND unidade_medida.cod_unidade  = catalogo_item.cod_unidade
          INNER JOIN compras.julgamento
                  ON julgamento.exercicio   = cotacao.exercicio
                 AND julgamento.cod_cotacao = cotacao.cod_cotacao
          INNER JOIN compras.julgamento_item
                  ON julgamento_item.exercicio   = julgamento.exercicio
                 AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
                 AND julgamento_item.cod_item    = mapa_item.cod_item 
          INNER JOIN licitacao.homologacao
                  ON homologacao.cod_licitacao       = licitacao.cod_licitacao
                 AND homologacao.cod_modalidade      = licitacao.cod_modalidade
                 AND homologacao.cod_entidade        = licitacao.cod_entidade
                 AND homologacao.exercicio_licitacao = licitacao.exercicio
                 AND homologacao.cod_item            = julgamento_item.cod_item
                 AND homologacao.lote                = julgamento_item.lote
                 AND (SELECT homologacao_anulada.num_homologacao 
                        FROM licitacao.homologacao_anulada
                       WHERE homologacao_anulada.cod_licitacao       = licitacao.cod_licitacao
                         AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
                         AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
                         AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
                         AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
                         AND homologacao.cod_item                    = homologacao_anulada.cod_item
                         AND homologacao.lote                        = homologacao_anulada.lote
                     ) IS NULL
          INNER JOIN sw_cgm AS responsavel
                  ON responsavel.numcgm = participante.numcgm_representante
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
               WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('inMes') . "/" . $this->getDado('stExercicio') . "', 'dd/mm/yyyy')
                 AND last_day(TO_DATE('" . $this->getDado('stExercicio') . "' || '-' || '".$this->getDado('inMes') . "' || '-' || '01','yyyy-mm-dd'))
                 AND licitacao.exercicio    = '" . $this->getDado('stExercicio') . "'
                 AND licitacao.cod_entidade IN (" . $this->getDado('stEntidades') . ")
                 AND licitacao.cod_modalidade NOT IN (8,9,10,11)
                 AND NOT EXISTS ( SELECT 1
                                    FROM licitacao.licitacao_anulada
                                   WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                                     AND licitacao_anulada.cod_modalidade  = licitacao.cod_modalidade
                                     AND licitacao_anulada.cod_entidade    = licitacao.cod_entidade
                                     AND licitacao_anulada.exercicio       = licitacao.exercicio
                                    )
            GROUP BY tipo_registro
                   , cod_orgao
                   , despesa.num_unidade
                   , licitacao.exercicio
                   , num_processo_licitatorio
                   , num_lote
                   , mapa_item.exercicio
                   , mapa_item.cod_mapa
                   , mapa_item.lote
                   , mapa_item.cod_item
                   , dt_cotacao
                   , descricao_item
                   , vl_unitario
                   , mapa_item.quantidade
                   , unidade
                   , vl_alienacao_bem
            ORDER BY tipo_registro
                   , cod_orgao
                   , despesa.num_unidade
                   , licitacao.exercicio
                   , num_processo_licitatorio
                   , num_lote
                   , num_item
		";
        return $stSql;
    }
    
    public function recuperaExportacao12(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaExportacao12().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );
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
    public function montaRecuperaExportacao12()
    {
        $stSql = "
              SELECT 12 AS tipo_registro
                   , LPAD(despesa.num_orgao::VARCHAR,2, '0') AS cod_orgao
                   , LPAD(despesa.num_unidade::VARCHAR,2, '0') AS cod_unidade
                   , licitacao.exercicio AS exercicio_licitacao
                   , licitacao.exercicio::VARCHAR || LPAD(licitacao.cod_entidade::VARCHAR,2,'0') || LPAD(licitacao.cod_modalidade::VARCHAR,2,'0') || LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                   , mapa_item.lote AS num_lote
                   , ROW_NUMBER() OVER(PARTITION BY mapa_item.exercicio,mapa_item.cod_mapa,mapa_item.lote ORDER BY mapa_item.exercicio,mapa_item.cod_mapa,mapa_item.lote,mapa_item.cod_item) AS num_item
                   , remove_acentos(REPLACE(REPLACE(catalogo_item.descricao,Chr('216'),'diametro'),Chr('8221'),'\"'))::VARCHAR(250) AS descricao_item
                   , despesa.cod_despesa as elemento_despesa
                   , (mapa_item.vl_total / mapa_item.quantidade)::numeric(14,2) AS vl_item
                FROM licitacao.licitacao
          INNER JOIN licitacao.participante
                  ON participante.cod_licitacao  = licitacao.cod_licitacao
                 AND participante.cod_modalidade = licitacao.cod_modalidade
                 AND participante.cod_entidade   = licitacao.cod_entidade 
                 AND participante.exercicio      = licitacao.exercicio
          INNER JOIN compras.mapa
                  ON mapa.exercicio = licitacao.exercicio_mapa
                 AND mapa.cod_mapa  = licitacao.cod_mapa
          INNER JOIN compras.mapa_solicitacao
                  ON mapa_solicitacao.exercicio = mapa.exercicio
                 AND mapa_solicitacao.cod_mapa  = mapa.cod_mapa
          INNER JOIN compras.mapa_item
                  ON mapa_item.exercicio             = mapa_solicitacao.exercicio
                 AND mapa_item.cod_entidade          = mapa_solicitacao.cod_entidade
                 AND mapa_item.cod_solicitacao       = mapa_solicitacao.cod_solicitacao
                 AND mapa_item.cod_mapa              = mapa_solicitacao.cod_mapa
                 AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
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
                  ON despesa.exercicio   = solicitacao_item_dotacao.exercicio
                 AND despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
          INNER JOIN compras.mapa_cotacao
                  ON mapa_cotacao.exercicio_mapa = mapa.exercicio
                 AND mapa_cotacao.cod_mapa       = mapa.cod_mapa
          INNER JOIN compras.cotacao
                  ON cotacao.exercicio   = mapa_cotacao.exercicio_cotacao
                 AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
          INNER JOIN compras.cotacao_item
                  ON cotacao_item.exercicio   = cotacao.exercicio
                 AND cotacao_item.cod_cotacao = cotacao.cod_cotacao
                 AND cotacao_item.cod_item    = mapa_item.cod_item
          INNER JOIN almoxarifado.catalogo_item
                  ON catalogo_item.cod_item = cotacao_item.cod_item
          INNER JOIN administracao.unidade_medida
                  ON unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
                 AND unidade_medida.cod_unidade  = catalogo_item.cod_unidade
          INNER JOIN compras.julgamento
                  ON julgamento.exercicio    = cotacao.exercicio
                 AND julgamento.cod_cotacao  = cotacao.cod_cotacao
          INNER JOIN compras.julgamento_item
                  ON julgamento_item.exercicio   = julgamento.exercicio
                 AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
                 AND julgamento_item.cod_item    = mapa_item.cod_item 
          INNER JOIN licitacao.homologacao
                  ON homologacao.cod_licitacao       = licitacao.cod_licitacao
                 AND homologacao.cod_modalidade      = licitacao.cod_modalidade
                 AND homologacao.cod_entidade        = licitacao.cod_entidade
                 AND homologacao.exercicio_licitacao = licitacao.exercicio
                 AND homologacao.cod_item            = julgamento_item.cod_item
                 AND homologacao.lote                = julgamento_item.lote
                 AND (SELECT homologacao_anulada.num_homologacao 
                        FROM licitacao.homologacao_anulada
                       WHERE homologacao_anulada.cod_licitacao       = licitacao.cod_licitacao
                         AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
                         AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
                         AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
                         AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
                         AND homologacao.cod_item                    = homologacao_anulada.cod_item
                         AND homologacao.lote                        = homologacao_anulada.lote
                     ) IS NULL
          INNER JOIN sw_cgm AS responsavel
                  ON responsavel.numcgm = participante.numcgm_representante
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
               WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('inMes') . "/" . $this->getDado('stExercicio') . "', 'dd/mm/yyyy')
                 AND last_day(TO_DATE('" . $this->getDado('stExercicio') . "' || '-' || '".$this->getDado('inMes') . "' || '-' || '01','yyyy-mm-dd'))
                 AND licitacao.exercicio    = '" . $this->getDado('stExercicio') . "'
                 AND licitacao.cod_entidade IN (" . $this->getDado('stEntidades') . ")
                 AND licitacao.cod_modalidade NOT IN (8,9,10,11)
                 AND NOT EXISTS (SELECT 1
                                   FROM licitacao.licitacao_anulada
                                  WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                                    AND licitacao_anulada.cod_modalidade  = licitacao.cod_modalidade
                                    AND licitacao_anulada.cod_entidade    = licitacao.cod_entidade
                                    AND licitacao_anulada.exercicio       = licitacao.exercicio
                                )
            GROUP BY tipo_registro
                   , cod_orgao
                   , despesa.num_unidade
                   , licitacao.exercicio
                   , num_processo_licitatorio
                   , num_lote
                   , mapa_item.exercicio
                   , mapa_item.cod_mapa
                   , mapa_item.lote
                   , mapa_item.cod_item
                   , descricao_item
                   , elemento_despesa
                   , vl_item
            ORDER BY tipo_registro
                   , cod_orgao
                   , despesa.num_unidade
                   , licitacao.exercicio
                   , num_processo_licitatorio
                   , num_lote
                   , num_item
		";
        return $stSql;
    }
    
    public function recuperaExportacao13(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaExportacao13().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );
    }
    
    public function montaRecuperaExportacao13()
    {
        $stSql = " 
              SELECT 13 AS tipo_registro
                   , LPAD(despesa.num_orgao::VARCHAR,2, '0') as cod_orgao
                   , LPAD(despesa.num_unidade::VARCHAR,2, '0') AS cod_unidade
                   , licitacao.exercicio AS exercicio_licitacao
                   , licitacao.exercicio::VARCHAR||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                   , despesa.cod_funcao AS cod_funcao
                   , despesa.cod_subfuncao AS cod_subfuncao
                   , despesa.cod_programa AS cod_programa
                   , 0 AS natureza_acao
                   , 0 AS num_proj_atividade
                   , despesa.cod_despesa AS elemento_despesa
                   , orcamento.recuperaEstruturalDespesa(despesa.cod_conta, despesa.exercicio, 2, TRUE, FALSE) AS subelemento
                   , recurso.cod_fonte AS cod_fonte_recursos
				   , mapa_item_dotacao.cod_item as num_item
                   , mapa_item_dotacao.lote AS num_lote
                   , SUM(mapa_item_dotacao.vl_dotacao)::numeric(14,2) AS vl_recurso
                FROM licitacao.licitacao
          INNER JOIN sw_processo
                  ON sw_processo.cod_processo  = licitacao.cod_processo
                 AND sw_processo.ano_exercicio = licitacao.exercicio_processo
          INNER JOIN licitacao.criterio_julgamento
                  ON criterio_julgamento.cod_criterio = licitacao.cod_criterio
          INNER JOIN licitacao.edital
                  ON edital.cod_licitacao       = licitacao.cod_licitacao
                 AND edital.cod_modalidade      = licitacao.cod_modalidade
                 AND edital.cod_entidade        = licitacao.cod_entidade
                 AND edital.exercicio_licitacao = licitacao.exercicio
                 AND (SELECT edital_anulado.num_edital
	                    FROM licitacao.edital_anulado
                       WHERE edital_anulado.num_edital = edital.num_edital
                         AND edital_anulado.exercicio  = edital.exercicio
                      ) IS NULL
          INNER JOIN compras.objeto
                  ON objeto.cod_objeto = licitacao.cod_objeto
          INNER JOIN compras.tipo_objeto
                  ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
          INNER JOIN compras.modalidade
                  ON modalidade.cod_modalidade = licitacao.cod_modalidade
          INNER JOIN compras.mapa
                  ON mapa.exercicio = licitacao.exercicio_mapa
                 AND mapa.cod_mapa  = licitacao.cod_mapa
          INNER JOIN compras.tipo_licitacao
                  ON tipo_licitacao.cod_tipo_licitacao = licitacao.cod_tipo_licitacao
          INNER JOIN compras.mapa_solicitacao
                  ON mapa_solicitacao.exercicio = mapa.exercicio
                 AND mapa_solicitacao.cod_mapa  = mapa.cod_mapa
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
          INNER JOIN compras.cotacao_fornecedor_item
                  ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                 AND cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                 AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                 AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                 AND cotacao_fornecedor_item.lote           = julgamento_item.lote
          INNER JOIN compras.fornecedor
                  ON fornecedor.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
          INNER JOIN licitacao.homologacao
                  ON homologacao.cod_licitacao       = licitacao.cod_licitacao
                 AND homologacao.cod_modalidade      = licitacao.cod_modalidade
                 AND homologacao.cod_entidade        = licitacao.cod_entidade
                 AND homologacao.exercicio_licitacao = licitacao.exercicio
                 AND homologacao.cod_item            = julgamento_item.cod_item
                 AND (SELECT homologacao_anulada.num_homologacao
				        FROM licitacao.homologacao_anulada
                       WHERE homologacao_anulada.cod_licitacao       = licitacao.cod_licitacao
                         AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
                         AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
                         AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
                         AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
                         AND homologacao.cod_item                    = homologacao_anulada.cod_item
                     ) IS NULL
          INNER JOIN compras.solicitacao_homologada
                  ON solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                 AND solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                 AND solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao
          INNER JOIN compras.solicitacao_homologada_reserva
                  ON solicitacao_homologada_reserva.exercicio       = solicitacao_homologada.exercicio
                 AND solicitacao_homologada_reserva.cod_entidade    = solicitacao_homologada.cod_entidade
                 AND solicitacao_homologada_reserva.cod_solicitacao = solicitacao_homologada.cod_solicitacao
                 AND solicitacao_homologada_reserva.cod_item        = homologacao.cod_item
          INNER JOIN orcamento.despesa
                  ON despesa.exercicio   = solicitacao_homologada_reserva.exercicio
                 AND despesa.cod_despesa = solicitacao_homologada_reserva.cod_despesa
          INNER JOIN orcamento.conta_despesa
                  ON conta_despesa.exercicio = despesa.exercicio
                 AND conta_despesa.cod_conta = despesa.cod_conta
          INNER JOIN compras.mapa_item_dotacao
                  ON mapa_item_dotacao.exercicio       = solicitacao_homologada.exercicio
                 AND mapa_item_dotacao.cod_entidade    = solicitacao_homologada.cod_entidade
                 AND mapa_item_dotacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao
                 AND mapa_item_dotacao.cod_item        = homologacao.cod_item
                 AND mapa_item_dotacao.cod_mapa        = mapa.cod_mapa
                 AND mapa_item_dotacao.cod_despesa     = despesa.cod_despesa
          INNER JOIN orcamento.despesa_acao
                  ON despesa_acao.cod_despesa       = despesa.cod_despesa
                 AND despesa_acao.exercicio_despesa = despesa.exercicio
          INNER JOIN orcamento.recurso
                  ON recurso.cod_recurso = despesa.cod_recurso
                 AND recurso.exercicio   = despesa.exercicio
          INNER JOIN sw_cgm AS responsavel
                  ON responsavel.numcgm = edital.responsavel_juridico
           LEFT JOIN licitacao.contrato_licitacao
                  ON contrato_licitacao.cod_licitacao       = licitacao.cod_licitacao
                 AND contrato_licitacao.cod_modalidade      = licitacao.cod_modalidade
                 AND contrato_licitacao.cod_entidade        = licitacao.cod_entidade
                 AND contrato_licitacao.exercicio_licitacao = licitacao.exercicio
           LEFT JOIN licitacao.contrato
                  ON contrato.num_contrato = contrato_licitacao.num_contrato
                 AND contrato.cod_entidade = contrato_licitacao.cod_entidade
                 AND contrato.exercicio    = contrato_licitacao.exercicio
               WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/".$this->getDado('inMes')."/".$this->getDado('stExercicio')."', 'dd/mm/yyyy')
                                                                                            AND last_day(TO_DATE('".$this->getDado('stExercicio')."'||'-'||'".$this->getDado('inMes')."'||'-'||'01','yyyy-mm-dd'))
                 AND licitacao.exercicio = '".$this->getDado('stExercicio')."'
                 AND licitacao.cod_entidade IN (".$this->getDado('stEntidades').")
                 AND licitacao.cod_modalidade NOT IN (8,9,10,11)
                 AND NOT EXISTS( SELECT 1
                                   FROM licitacao.licitacao_anulada
                                  WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                                    AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                    AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                                    AND licitacao_anulada.exercicio      = licitacao.exercicio
                               )
            GROUP BY tipo_registro
                   , cod_orgao
                   , cod_unidade
                   , licitacao.exercicio
                   , num_processo_licitatorio
                   , despesa.cod_funcao 
                   , despesa.cod_subfuncao 
                   , despesa.cod_programa
                   , natureza_acao
                   , num_proj_atividade
                   , despesa.cod_despesa 
                   , subelemento
                   , recurso.cod_fonte 
                   , num_item
                   , num_lote
            ORDER BY tipo_registro
                   , cod_unidade
                   , licitacao.exercicio
                   , num_processo_licitatorio
                   , despesa.cod_funcao 
                   , despesa.cod_subfuncao 
                   , despesa.cod_programa
                   , natureza_acao
                   , num_proj_atividade
                   , despesa.cod_despesa 
                   , subelemento
                   , recurso.cod_fonte
	   ";
        return $stSql;
    }
    
    public function __destruct(){}    
}