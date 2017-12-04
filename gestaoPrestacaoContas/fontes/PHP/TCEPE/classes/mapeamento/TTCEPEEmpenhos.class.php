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
  * Arquivo de mapeamento para a criação do arquivo Empenhos.txt. TCE-PE
  * Data de Criação: 27/10/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  $Id: TTCEPEEmpenhos.class.php 60661 2014-11-06 16:50:59Z evandro $
  $Date: 2014-11-06 14:50:59 -0200 (Thu, 06 Nov 2014) $
  $Author: evandro $
  $Rev: 60661 $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEPEEmpenhos extends Persistente {
    
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEPEEmpenhos()
    {
        parent::Persistente();
    }
    
    public function recuperaEmpenhos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
	    $obConexao   = new Conexao;
	    $rsRecordSet = new RecordSet;
	    $stSql = $this->montaRecuperaEmpenhos().$stFiltro.$stOrdem;
	    $this->stDebug = $stSql;
	    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }
    
    public function montaRecuperaEmpenhos()
    {
        $stSql = "
                SELECT  empenho.exercicio AS exercicio_empenho
                        , LPAD(despesa.num_orgao::VARCHAR,2,'0') || LPAD(despesa.num_unidade::VARCHAR,2,'0') AS cod_unidade_orcamentaria
                        , despesa.cod_funcao AS cod_funcao
                        , despesa.cod_subfuncao AS cod_subfuncao
                        , p_programa.num_programa AS cod_programa
                        , p_acao.num_acao AS cod_acao
                        , CASE SUBSTR(LPAD(p_acao.num_acao::VARCHAR,4,'0'),1,1)
                                WHEN '0'THEN '9'
                                ELSE SUBSTR(LPAD(p_acao.num_acao::VARCHAR,4,'0'),1,1)
                         END AS identificacao_acao
                        , SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),1,1) AS cod_categoria_economica
                        , SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),2,1) AS cod_grupo_natureza_despesa
                        , SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),3,2) AS cod_modalidade_aplicacao
                        , SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),5,2) AS cod_elemento_despesa_dotacao
                        , CASE WHEN SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),7,2) = '00'
                                THEN '999'
                                ELSE SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),7,2)
                         END AS cod_subelemento_despesa
                        , CASE  WHEN TRIM(atributo_empenho_valor.valor) IS NULL THEN '9'
                                WHEN TRIM(atributo_empenho_valor.valor) = '4'  THEN '1'
                                WHEN TRIM(atributo_empenho_valor.valor) = '3'  THEN '2'
                                WHEN TRIM(atributo_empenho_valor.valor) = '2'  THEN '3'
                                WHEN TRIM(atributo_empenho_valor.valor) = '1'  THEN '4'
                                WHEN TRIM(atributo_empenho_valor.valor) = '5'  THEN '7'
                                WHEN TRIM(atributo_empenho_valor.valor) = '6'  THEN '8'
                                WHEN TRIM(atributo_empenho_valor.valor) = '7'  THEN '9'
                                WHEN TRIM(atributo_empenho_valor.valor) = '14' THEN '10'
                                WHEN TRIM(atributo_empenho_valor.valor) = '10' THEN '0'
                                WHEN TRIM(atributo_empenho_valor.valor) = '11' THEN '0'
                                WHEN TRIM(atributo_empenho_valor.valor) = '12' THEN '0'
                                WHEN TRIM(atributo_empenho_valor.valor) = '20' THEN '6'                            
                        END AS modalidade_licitacao
                        , empenho.cod_empenho AS num_empenho
                        , CASE pre_empenho.cod_tipo
                            WHEN 1 THEN 1
                            WHEN 2 THEN 3
                            WHEN 3 THEN 2
                        END AS tipo_empenho
                        , TO_CHAR(empenho.dt_empenho, 'ddmmyyyy') AS dt_emissao_empenho
                        , empenho.fn_consultar_valor_empenhado( pre_empenho.exercicio, empenho.cod_empenho, empenho.cod_entidade) AS vl_empenho
                        , pre_empenho.descricao AS historico_empenho
                        , CASE WHEN sw_cgm_documento.documento IS NOT NULL
                                THEN sw_cgm_documento.documento
                                ELSE entidade_documento.documento
                         END AS cpf_cnpj_credor
                        , LPAD(interna.cod_licitacao::VARCHAR,4,'0')||'/'||interna.exercicio_licitacao AS num_processo_licitacao
                        , despesa.cod_recurso AS cod_fonte_recurso
                        , ordenador.cpf AS cpf_ordenador_despesa
                        , SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),5,2) AS cod_elemento_despesa_empenho
                FROM empenho.empenho
            
                JOIN empenho.pre_empenho
                     ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    AND pre_empenho.exercicio = empenho.exercicio
            
                JOIN empenho.pre_empenho_despesa
                     ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho                       
                    AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio       
            
                LEFT JOIN empenho.atributo_empenho_valor
                     ON atributo_empenho_valor.exercicio = pre_empenho.exercicio
                    AND atributo_empenho_valor.cod_pre_empenho = pre_empenho.cod_pre_empenho
                    AND atributo_empenho_valor.timestamp = (SELECT MAX(timestamp) FROM empenho.atributo_empenho_valor AS aev 
                                                                                WHERE aev.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho
                                                                                  AND aev.exercicio = atributo_empenho_valor.exercicio )
	                AND atributo_empenho_valor.cod_atributo = 101

                JOIN empenho.tipo_empenho
                     ON tipo_empenho.cod_tipo =  pre_empenho.cod_tipo
            
                LEFT JOIN ( SELECT  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                            THEN sw_cgm_pessoa_fisica.cpf
                                        WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                            THEN sw_cgm_pessoa_juridica.cnpj
                                        ELSE NULL
                                    END AS documento
                                    , sw_cgm.numcgm
                            FROM sw_cgm
                            LEFT JOIN sw_cgm_pessoa_fisica
                                ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                            LEFT JOIN sw_cgm_pessoa_juridica
                                ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                ) AS sw_cgm_documento
                        ON sw_cgm_documento.numcgm = pre_empenho.cgm_beneficiario
            
                LEFT JOIN ( SELECT  entidade.exercicio
                                    , entidade.cod_entidade
                                    , CASE  WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                                THEN sw_cgm_pessoa_fisica.cpf
                                            WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                                THEN sw_cgm_pessoa_juridica.cnpj
                                        ELSE NULL
                                    END AS documento
                            FROM orcamento.entidade
                            LEFT JOIN sw_cgm_pessoa_fisica
                                 ON sw_cgm_pessoa_fisica.numcgm = entidade.numcgm
                            LEFT JOIN sw_cgm_pessoa_juridica
                                 ON sw_cgm_pessoa_juridica.numcgm = entidade.numcgm
                ) AS entidade_documento
                     ON entidade_documento.exercicio = empenho.exercicio
                    AND entidade_documento.cod_entidade = empenho.cod_entidade
            
                LEFT JOIN (
                        SELECT  item_pre_empenho.cod_pre_empenho
                                , item_pre_empenho.exercicio
                                , licitacao.cod_licitacao
                                , licitacao.exercicio AS exercicio_licitacao
                        FROM empenho.item_pre_empenho
                        LEFT JOIN empenho.item_pre_empenho_julgamento
                             ON item_pre_empenho_julgamento.cod_pre_empenho  = item_pre_empenho.cod_pre_empenho   
                            AND item_pre_empenho_julgamento.exercicio        = item_pre_empenho.exercicio
                            AND item_pre_empenho_julgamento.num_item         = item_pre_empenho.num_item
                        LEFT JOIN compras.julgamento_item
                             ON julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio_julgamento
                            AND julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao 
                            AND julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item
                            AND julgamento_item.lote           = item_pre_empenho_julgamento.lote
                            AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
                        LEFT JOIN compras.julgamento
                             ON julgamento.exercicio   = julgamento_item.exercicio
                            AND julgamento.cod_cotacao = julgamento_item.cod_cotacao
                        LEFT JOIN compras.cotacao
                             ON cotacao.cod_cotacao = julgamento.cod_cotacao
                            AND cotacao.exercicio   = julgamento.exercicio
                        LEFT JOIN compras.mapa_cotacao
                             ON mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                            AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                        LEFT JOIN compras.mapa
                             ON mapa.cod_mapa  = mapa_cotacao.cod_mapa
                            AND mapa.exercicio = mapa_cotacao.exercicio_mapa
                        LEFT JOIN licitacao.licitacao
                             ON licitacao.exercicio_mapa = mapa.exercicio
                            AND licitacao.cod_mapa = mapa.cod_mapa
                        WHERE licitacao.exercicio = '".$this->getDado('exercicio')."'
                        GROUP BY  item_pre_empenho.cod_pre_empenho
                                , item_pre_empenho.exercicio
                                , licitacao.cod_licitacao
                                , licitacao.exercicio
                ) AS interna
                     ON interna.cod_pre_empenho = pre_empenho.cod_pre_empenho
                    AND interna.exercicio       = pre_empenho.exercicio
                JOIN orcamento.despesa
                     ON despesa.exercicio   = pre_empenho_despesa.exercicio                             
                    AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa                           
            
                JOIN orcamento.conta_despesa
                     ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                    AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
            
                JOIN orcamento.programa AS o_programa
                     ON o_programa.exercicio = despesa.exercicio
                    AND o_programa.cod_programa = despesa.cod_programa
            
                JOIN orcamento.programa_ppa_programa
                     ON programa_ppa_programa.exercicio = o_programa.exercicio
                    AND programa_ppa_programa.cod_programa = o_programa.cod_programa
            
                JOIN ppa.programa AS p_programa
                     ON p_programa.cod_programa = programa_ppa_programa.cod_programa_ppa
            
                JOIN orcamento.pao AS o_pao
                     ON o_pao.exercicio = despesa.exercicio
                    AND o_pao.num_pao = despesa.num_pao
            
                JOIN orcamento.pao_ppa_acao
                     ON pao_ppa_acao.exercicio = o_pao.exercicio
                    AND pao_ppa_acao.num_pao = o_pao.num_pao
            
                JOIN ppa.acao AS p_acao
                     ON p_acao.cod_acao = pao_ppa_acao.cod_acao
                    
                LEFT JOIN ( SELECT  sw_cgm_pessoa_fisica.cpf
                                    , configuracao_ordenador.num_unidade
                                    , configuracao_ordenador.num_orgao
                                    , configuracao_ordenador.exercicio
                                    , configuracao_ordenador.dt_inicio_vigencia
                                    , configuracao_ordenador.dt_fim_vigencia
                            FROM tcepe.configuracao_ordenador
                            JOIN orcamento.unidade
                                 ON unidade.num_unidade = configuracao_ordenador.num_unidade
                                AND unidade.num_orgao   = configuracao_ordenador.num_orgao
                                AND unidade.exercicio   = configuracao_ordenador.exercicio
                            JOIN sw_cgm_pessoa_fisica
                                 ON sw_cgm_pessoa_fisica.numcgm = configuracao_ordenador.cgm_ordenador
                            WHERE configuracao_ordenador.exercicio = '".$this->getDado('exercicio')."'
                ) AS ordenador
                     ON ordenador.num_unidade = despesa.num_unidade
                    AND ordenador.num_orgao   = despesa.num_orgao
                    AND ordenador.exercicio   = despesa.exercicio
                    AND empenho.dt_empenho BETWEEN ordenador.dt_inicio_vigencia AND ordenador.dt_fim_vigencia
                WHERE empenho.exercicio = '".$this->getDado('exercicio')."'
                AND empenho.cod_entidade = ".$this->getDado('stEntidade')."
                AND empenho.dt_empenho BETWEEN TO_DATE('".$this->getDado('stDataInicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('stDataFinal')."', 'dd/mm/yyyy')
                GROUP BY empenho.exercicio 
                        , cod_unidade_orcamentaria
                        , despesa.cod_funcao 
                        , despesa.cod_subfuncao 
                        , p_programa.num_programa 
                        , p_acao.num_acao 
                        , identificacao_acao
                        , conta_despesa.cod_estrutural
                        , atributo_empenho_valor.valor
                        , empenho.cod_empenho 
                        , tipo_empenho
                        , empenho.dt_empenho
                        , vl_empenho
                        , pre_empenho.descricao 
                        , cpf_cnpj_credor
                        , num_processo_licitacao
                        , despesa.cod_recurso
                        , ordenador.cpf
                        , empenho.cod_pre_empenho
			
              ORDER BY empenho.cod_empenho, empenho.exercicio ASC
        ";
        return $stSql;
    }
}

?>