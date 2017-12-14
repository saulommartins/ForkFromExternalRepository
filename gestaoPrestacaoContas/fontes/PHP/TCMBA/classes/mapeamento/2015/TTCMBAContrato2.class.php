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
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 19/10/2007

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    $Id $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 19/10/2007

  * @author Analista: Gelson Wolvowski
  * @author Desenvolvedor: Jean Felipe da Silva

*/

class TTCMBAContrato2 extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct() 
    {
      parent::Persistente();
      $this->getDado('exercicio',Sessao::getExercicio());
    }

    public function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosTribunal()
    {
        $stSql = " SELECT 1 AS tipo_registro 
                        , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                        , licitacao.exercicio||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo 
                        , contrato.numero_contrato 
                        , 1 as tipo_moeda
                        , objeto.descricao AS objeto_contrato
                        , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN 1 
                               WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL THEN 2
                               ELSE NULL
                        END AS pessoa_contratado
                        , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN sw_cgm_pessoa_fisica.cpf 
                               ELSE sw_cgm_pessoa_juridica.cnpj
                        END AS cic_contratado
                        , cgm_contratado.nom_cgm AS nom_contratado
                        , TO_CHAR(contrato.dt_assinatura, 'dd/mm/yyyy') AS dt_assinatura
                        , TO_CHAR(contrato.vencimento, 'dd/mm/yyyy') AS dt_vencimento
                        , SUBSTR(TRIM(cgm_imprensa.nom_cgm), 1, 50) AS diario_oficial
                        , TO_CHAR(publicacao_contrato.dt_publicacao, 'dd/mm/yyyy') AS dt_publicacao
                        , contrato.valor_contratado AS vl_contrato
                        , patrimonio.fn_soma_valor_item_servico('2015', mapa_cotacao.cod_mapa) AS custo_pessoal -- PL para calcular custo dos itens de serviço
                        , TO_CHAR(contrato.dt_assinatura, 'yyyymm') AS competencia
                        , '' AS num_processo_dispensa
                        , TO_CHAR(contrato.inicio_execucao, 'dd/mm/yyyy') AS dt_inicio_execucao
                        , 'N' AS exame_previo
                        , '1' AS anterior_siga
                        , tipo_contrato.tipo_tc AS tipo_contrato
                        , 'S' AS indicador_licitacao -- (forçado por enquanto)
                        , 'N' AS indicador_dispensa -- (forçado por enquanto)

                     FROM licitacao.contrato

               INNER JOIN licitacao.contrato_licitacao
                       ON contrato_licitacao.num_contrato = contrato.num_contrato
                      AND contrato_licitacao.cod_entidade = contrato.cod_entidade
                      AND contrato_licitacao.exercicio = contrato.exercicio
                     
               INNER JOIN sw_cgm AS cgm_contratado
                       ON contrato.cgm_contratado = cgm_contratado.numcgm
                     
                LEFT JOIN sw_cgm_pessoa_fisica
                       ON cgm_contratado.numcgm = sw_cgm_pessoa_fisica.numcgm
                     
                LEFT JOIN sw_cgm_pessoa_juridica
                       ON cgm_contratado.numcgm = sw_cgm_pessoa_juridica.numcgm
                     
               INNER JOIN licitacao.publicacao_contrato
                       ON contrato.num_contrato = publicacao_contrato.num_contrato
                      AND contrato.exercicio = publicacao_contrato.exercicio
                      AND contrato.cod_entidade = publicacao_contrato.cod_entidade
                     
               INNER JOIN sw_cgm AS cgm_imprensa
                       ON publicacao_contrato.numcgm = cgm_imprensa.numcgm
                     
               INNER JOIN licitacao.licitacao
                       ON contrato_licitacao.cod_licitacao = licitacao.cod_licitacao
                      AND contrato_licitacao.cod_modalidade = licitacao.cod_modalidade
                      AND contrato_licitacao.cod_entidade = licitacao.cod_entidade
                      AND contrato_licitacao.exercicio = licitacao.exercicio
                     
               INNER JOIN administracao.configuracao_entidade
                       ON licitacao.cod_entidade = configuracao_entidade.cod_entidade
                      AND licitacao.exercicio = configuracao_entidade.exercicio
                     
               INNER JOIN compras.mapa
                       ON licitacao.exercicio_mapa = mapa.exercicio
                      AND licitacao.cod_mapa = mapa.cod_mapa
                     
               INNER JOIN compras.objeto
                       ON mapa.cod_objeto = objeto.cod_objeto
                   
               INNER JOIN compras.mapa_cotacao
                       ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                      AND mapa.cod_mapa = mapa_cotacao.cod_mapa
                     
               INNER JOIN compras.cotacao_item
                       ON mapa_cotacao.exercicio_cotacao = cotacao_item.exercicio
                      AND mapa_cotacao.cod_cotacao = cotacao_item.cod_cotacao

               INNER JOIN almoxarifado.catalogo_item
                       ON catalogo_item.cod_item = cotacao_item.cod_item

               INNER JOIN almoxarifado.tipo_item
                       ON tipo_item.cod_tipo = catalogo_item.cod_tipo
                     
               INNER JOIN compras.cotacao_fornecedor_item
                       ON cotacao_item.exercicio = cotacao_fornecedor_item.exercicio
                      AND cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                      AND cotacao_item.cod_item = cotacao_fornecedor_item.cod_item
                      AND cotacao_item.lote = cotacao_fornecedor_item.lote
                     
               INNER JOIN compras.julgamento_item
                       ON cotacao_fornecedor_item.exercicio = julgamento_item.exercicio
                      AND cotacao_fornecedor_item.cod_cotacao = julgamento_item.cod_cotacao
                      AND cotacao_fornecedor_item.cod_item = julgamento_item.cod_item
                      AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                      AND cotacao_fornecedor_item.lote = julgamento_item.lote
                     
               INNER JOIN empenho.item_pre_empenho_julgamento
                       ON julgamento_item.exercicio = item_pre_empenho_julgamento.exercicio_julgamento
                      AND julgamento_item.cod_cotacao = item_pre_empenho_julgamento.cod_cotacao
                      AND julgamento_item.cod_item = item_pre_empenho_julgamento.cod_item
                      AND julgamento_item.lote = item_pre_empenho_julgamento.lote
                      AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
                     
               INNER JOIN empenho.item_pre_empenho
                       ON item_pre_empenho_julgamento.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                      AND item_pre_empenho_julgamento.exercicio = item_pre_empenho.exercicio
                      AND item_pre_empenho_julgamento.num_item = item_pre_empenho.num_item
                     
               INNER JOIN empenho.pre_empenho
                       ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                      AND item_pre_empenho.exercicio = pre_empenho.exercicio
                     
               INNER JOIN empenho.pre_empenho_despesa
                       ON pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                      AND pre_empenho.exercicio = pre_empenho_despesa.exercicio
                     
               INNER JOIN orcamento.despesa
                       ON pre_empenho_despesa.exercicio = despesa.exercicio
                      AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                     
               INNER JOIN orcamento.recurso
                       ON despesa.exercicio = recurso.exercicio
                      AND despesa.cod_recurso = recurso.cod_recurso
                     
               INNER JOIN orcamento.recurso_direto
                       ON recurso_direto.exercicio = recurso.exercicio
                      AND recurso_direto.cod_recurso = recurso.cod_recurso
                     
               INNER JOIN orcamento.fonte_recurso
                       ON recurso_direto.cod_fonte = fonte_recurso.cod_fonte
                     
                LEFT JOIN licitacao.contrato_anulado
                       ON contrato_anulado.num_contrato = contrato.num_contrato
                      AND contrato_anulado.exercicio = contrato.exercicio
                      AND contrato_anulado.cod_entidade = contrato.cod_entidade

                LEFT JOIN compras.cotacao_anulada
                       ON cotacao_item.exercicio = cotacao_anulada.exercicio
                      AND cotacao_item.cod_cotacao = cotacao_anulada.cod_cotacao

                LEFT JOIN empenho.empenho_anulado_item
                       ON item_pre_empenho.exercicio = empenho_anulado_item.exercicio
                      AND item_pre_empenho.cod_pre_empenho = empenho_anulado_item.cod_pre_empenho
                      AND item_pre_empenho.num_item = empenho_anulado_item.num_item

               INNER JOIN licitacao.tipo_contrato
                       ON tipo_contrato.cod_tipo = contrato.cod_tipo_contrato

                    WHERE contrato_anulado.num_contrato IS NULL
                      AND cotacao_anulada.cod_cotacao IS NULL
                      AND julgamento_item.ordem = 1
                      AND empenho_anulado_item.num_item IS NULL
                      AND licitacao.cod_entidade IN (".$this->getDado('entidades').")
                      AND licitacao.exercicio = '".$this->getDado('exercicio')."'
                      AND contrato.dt_assinatura BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                     AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')

                 GROUP BY licitacao.cod_processo,
                          contrato.numero_contrato,
                          objeto.descricao,
                          sw_cgm_pessoa_fisica.cpf,
                          sw_cgm_pessoa_juridica.cnpj,
                          cgm_contratado.nom_cgm,
                          contrato.dt_assinatura,
                          contrato.vencimento,
                          cgm_imprensa.nom_cgm,
                          publicacao_contrato.dt_publicacao,
                          contrato.valor_contratado,
                          tipo_item.cod_tipo,
                          contrato.inicio_execucao,
                          licitacao.exercicio,
                          licitacao.cod_entidade,
                          licitacao.cod_modalidade,
                          licitacao.cod_licitacao,
                          mapa_cotacao.cod_mapa,
                          tipo_contrato.tipo_tc
        ";
        return $stSql;
    }

}
