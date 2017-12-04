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
    * Classe de mapeamento do arquivo PRL.TXT
    * Data de Criação: 26/01/2015
    * @author Analista: Ane Caroline Fiegenbaum Pereira
    * @author Desenvolvedor: Evandro melos
    * @package URBEM
    * @subpackage Mapeamento
    * $Revision: $
    * $Id: $
    * $Name: $
    * $Author: evandro $
    * $Date: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOParecerLicitacao extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/

    public function recuperaPareceLicitacaoRegistro10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaPareceLicitacaoRegistro10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaPareceLicitacaoRegistro10()
    {
      
           $stSql = " SELECT  10 AS tipo_registro
                            , LPAD(despesa.num_orgao::varchar,2,'0') AS cod_orgao
                            , LPAD(despesa.num_unidade::varchar,2, '0') AS cod_unidade
                            , licitacao.exercicio AS exercicio_licitacao
                            , licitacao.exercicio::varchar||LPAD(''||licitacao.cod_entidade::varchar,2, '0')||LPAD(''||licitacao.cod_modalidade::varchar,2, '0')||LPAD(''||licitacao.cod_licitacao::varchar,4, '0') AS num_processo_licitatorio 
                            , to_char(edital.dt_aprovacao_juridico, 'ddmmyyyy') AS data_parecer
                            , 2 AS tipo_parecer
                            , sw_cgm_pessoa_fisica.cpf AS cpf
                            , sw_cgm.nom_cgm AS nome_resp_parecer                            
                            , sw_cgm.logradouro AS logra_res
                            , ''::varchar(20) AS setor_logra
                            , sw_municipio.nom_municipio AS cidade_logra
                            , sw_uf.sigla_uf AS uf_cidade_logra
                            , sw_cgm.cep AS cep_logra_responsavel
                            , CASE WHEN sw_cgm.fone_residencial != '' THEN
                                        sw_cgm.fone_residencial 
                                    ELSE
                                        sw_cgm.fone_celular 
                              END AS fone
                            , sw_cgm.e_mail AS email
                            
                    FROM licitacao.licitacao 
   
                 INNER JOIN licitacao.edital
                         ON edital.exercicio_licitacao = licitacao.exercicio
                        AND edital.cod_licitacao       = licitacao.cod_licitacao
                        AND edital.cod_modalidade      = licitacao.cod_modalidade
                        AND edital.cod_entidade        = licitacao.cod_entidade
                        
                 INNER JOIN licitacao.cotacao_licitacao
                         ON cotacao_licitacao.cod_licitacao       = licitacao.cod_licitacao
                        AND cotacao_licitacao.cod_modalidade      = licitacao.cod_modalidade
                        AND cotacao_licitacao.cod_entidade        = licitacao.cod_entidade
                        AND cotacao_licitacao.exercicio_licitacao = licitacao.exercicio
                        
                 INNER JOIN licitacao.adjudicacao
                         ON adjudicacao.cod_licitacao        = cotacao_licitacao.cod_licitacao
                        AND adjudicacao.cod_modalidade       = cotacao_licitacao.cod_modalidade
                        AND adjudicacao.cod_entidade         = cotacao_licitacao.cod_entidade
                        AND adjudicacao.exercicio_licitacao  = cotacao_licitacao.exercicio_licitacao
                        AND adjudicacao.lote                 = cotacao_licitacao.lote
                        AND adjudicacao.cod_cotacao          = cotacao_licitacao.cod_cotacao
                        AND adjudicacao.cod_item             = cotacao_licitacao.cod_item
                        AND adjudicacao.exercicio_cotacao    = cotacao_licitacao.exercicio_cotacao
                        AND adjudicacao.cgm_fornecedor       = cotacao_licitacao.cgm_fornecedor
                        
                 INNER JOIN licitacao.homologacao
                         ON homologacao.num_adjudicacao      = adjudicacao.num_adjudicacao
                        AND homologacao.cod_entidade         = adjudicacao.cod_entidade
                        AND homologacao.cod_modalidade       = adjudicacao.cod_modalidade
                        AND homologacao.cod_licitacao        = adjudicacao.cod_licitacao
                        AND homologacao.exercicio_licitacao  = adjudicacao.exercicio_licitacao
                        AND homologacao.cod_item             = adjudicacao.cod_item
                        AND homologacao.cod_cotacao          = adjudicacao.cod_cotacao
                        AND homologacao.lote                 = adjudicacao.lote
                        AND homologacao.exercicio_cotacao    = adjudicacao.exercicio_cotacao
                        AND homologacao.cgm_fornecedor       = adjudicacao.cgm_fornecedor
                        AND (   SELECT num_homologacao
                                  FROM licitacao.homologacao_anulada
                                 WHERE homologacao_anulada.num_homologacao      = homologacao.num_homologacao
                                   AND homologacao_anulada.cod_licitacao        = homologacao.cod_licitacao
                                   AND homologacao_anulada.cod_modalidade       = homologacao.cod_modalidade
                                   AND homologacao_anulada.cod_entidade         = homologacao.cod_entidade
                                   AND homologacao_anulada.num_adjudicacao      = homologacao.num_adjudicacao
                                   AND homologacao_anulada.exercicio_licitacao  = homologacao.exercicio_licitacao
                                   AND homologacao_anulada.lote                 = homologacao.lote
                                   AND homologacao_anulada.cod_cotacao          = homologacao.cod_cotacao
                                   AND homologacao_anulada.cod_item             = homologacao.cod_item
                                   AND homologacao_anulada.exercicio_cotacao    = homologacao.exercicio_cotacao
                                   AND homologacao_anulada.cgm_fornecedor       = homologacao.cgm_fornecedor
                            ) IS NULL  
                    
                INNER JOIN public.sw_cgm_pessoa_fisica
                        ON sw_cgm_pessoa_fisica.numcgm = edital.responsavel_juridico

                INNER JOIN sw_cgm
                        ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
                        
                INNER JOIN sw_municipio
                        ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
                       AND sw_municipio.cod_uf        = sw_cgm.cod_uf

                INNER JOIN sw_uf
                        ON sw_uf.cod_uf = sw_municipio.cod_uf

                    INNER JOIN compras.mapa
                          ON mapa.exercicio = licitacao.exercicio_mapa
                         AND mapa.cod_mapa  = licitacao.cod_mapa
                    
                    JOIN compras.mapa_solicitacao
                          ON mapa_solicitacao.exercicio = mapa.exercicio
                         AND mapa_solicitacao.cod_mapa  = mapa.cod_mapa
                         
                    INNER JOIN compras.mapa_item
                          ON mapa_item.exercicio             = mapa_solicitacao.exercicio
                         AND mapa_item.cod_entidade          = mapa_solicitacao.cod_entidade
                         AND mapa_item.cod_solicitacao       = mapa_solicitacao.cod_solicitacao
                         AND mapa_item.cod_mapa              = mapa_solicitacao.cod_mapa
                         AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
                        
                    INNER JOIN compras.mapa_item_dotacao
                            ON mapa_item_dotacao.exercicio              = mapa_item.exercicio
                            AND mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
                            AND mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                            AND mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
                            AND mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
                            AND mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
                            AND mapa_item_dotacao.cod_item              = mapa_item.cod_item
                            AND mapa_item_dotacao.lote                  = mapa_item.lote
                                            
                    INNER JOIN orcamento.despesa
                            ON despesa.exercicio    = mapa_item_dotacao.exercicio
                            AND despesa.cod_despesa = mapa_item_dotacao.cod_despesa


                     WHERE homologacao.timestamp BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                       AND licitacao.cod_modalidade NOT IN (8,9)
                       AND NOT EXISTS (SELECT 1
                                         FROM licitacao.licitacao_anulada
                                        WHERE licitacao_anulada.exercicio      = licitacao.exercicio
                                          AND licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                                          AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                          AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                                      )
                                      
                  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16 ";

        return $stSql;
    }
}
