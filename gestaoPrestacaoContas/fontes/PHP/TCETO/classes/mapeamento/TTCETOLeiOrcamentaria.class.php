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

    * Extensão da Classe de Mapeamento TTCETOLeisOrcamentarias
    *
    * Data de Criação: 07/11/2014
    *
    * @author: Evandro Melos
    *
    * $Id: TTCETOLeiOrcamentaria.class.php 60996 2014-11-27 18:40:19Z evandro $
    *
    * @ignore
    *
*/
class TTCETOLeiOrcamentaria extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCETOLeiOrcamentaria()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
       
    public function listarExportacaoLeiOrcamentaria(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
    {
        $stSql = "  SELECT (SELECT COALESCE(PJ.cnpj,'0') as cnpj
                                FROM orcamento.entidade
                                INNER JOIN sw_cgm_pessoa_juridica as PJ
                                        ON PJ.numcgm = entidade.numcgm
                                 WHERE entidade.exercicio = '".$this->getDado('exercicio')."'
                                   AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                            ) AS cod_und_gestora                         
                            , exercicio
                            , complementacao
                            , num_lei_ppa                
                            , data_lei_ppa
                            , num_ldo
                            , data_ldo
                            , data_pub_ldo
                            , num_loa
                            , data_loa
                            , data_pub_loa
                            , COALESCE(perc_credito_adicional,'0.00')      as perc_credito_adicional
                            , COALESCE(perc_op_credito_antecipacao,'0.00') as perc_op_credito_antecipacao
                            , COALESCE(perc_op_credito_interno,'0.00')     as perc_op_credito_interno
                            , COALESCE(perc_op_credito_externo,'0.00')     as perc_op_credito_externo
                            , num_lei_alteracao_ppa
                            , data_pub_lei_alteracao_ppa
                            , data_pub_lei_ppa
                    FROM(
                            SELECT '".$this->getDado('exercicio')."'::VARCHAR AS exercicio
                                  , (SELECT CASE WHEN valor = ''
                                                    THEN '0'
                                                    ELSE valor
                                                    END AS valor
                                       FROM administracao.configuracao
                                       WHERE configuracao.cod_modulo = 64
                                         AND configuracao.exercicio = '".$this->getDado('exercicio')."'
                                         AND configuracao.parametro like 'tceto_config_complementacao_loa'
                                   ) AS complementacao
                                   , ppa_publicacao.cod_norma AS num_lei_ppa
                                   , TO_CHAR(ppa.timestamp ,'yyyy-mm-dd') AS data_lei_ppa
                                   , ppa.fn_verifica_homologacao(ppa.cod_ppa) AS homologado
                                   , homologacao.cod_norma AS num_ldo
                                   , TO_CHAR(ldo.timestamp ,'yyyy-mm-dd') AS data_ldo
                                   , TO_CHAR(homologacao.timestamp ,'yyyy-mm-dd') AS data_pub_ldo
                                   , ( SELECT CASE WHEN valor = ''
                                                    THEN '0'
                                                    ELSE valor
                                                    END AS valor
                                         FROM administracao.configuracao
                                        WHERE configuracao.cod_modulo = 64
                                          AND configuracao.exercicio = '".$this->getDado('exercicio')."'
                                          AND configuracao.parametro like 'tceto_config_cod_norma'
                                     ) AS num_loa                                    
                                   , TO_CHAR(norma.dt_assinatura ,'yyyy-mm-dd') AS data_loa
                                   , TO_CHAR(norma.dt_publicacao ,'yyyy-mm-dd') AS data_pub_loa
                                   , ( SELECT CASE WHEN valor = ''
                                                    THEN to_number('0','99')::numeric(14,2)
                                                    ELSE to_number(valor,'99')::numeric(14,2)
                                                    END AS valor
                                         FROM administracao.configuracao
                                        WHERE configuracao.cod_modulo = 64
                                          AND configuracao.exercicio = '".$this->getDado('exercicio')."'
                                          AND configuracao.parametro like 'tceto_config_credito_adicional'
                                     ) AS perc_credito_adicional
                                   , ( SELECT CASE WHEN valor = ''
                                                    THEN to_number('0','99')::numeric(14,2)
                                                    ELSE to_number(valor,'99')::numeric(14,2)
                                                    END AS valor
                                         FROM administracao.configuracao
                                        WHERE configuracao.cod_modulo = 64
                                          AND configuracao.exercicio = '".$this->getDado('exercicio')."'
                                          AND configuracao.parametro like 'tceto_config_credito_antecipacao'
                                     ) AS perc_op_credito_antecipacao
                                   , ( SELECT CASE WHEN valor = ''
                                                    THEN to_number('0','99')::numeric(14,2)
                                                    ELSE to_number(valor,'99')::numeric(14,2)
                                                    END AS valor
                                        FROM administracao.configuracao
                                       WHERE configuracao.cod_modulo = 64
                                         AND configuracao.exercicio = '".$this->getDado('exercicio')."'
                                         AND configuracao.parametro like 'tceto_config_credito_interno'
                                     ) AS perc_op_credito_interno
                                   , ( SELECT CASE WHEN valor = ''
                                                    THEN to_number('0','99')::numeric(14,2)
                                                    ELSE to_number(valor,'99')::numeric(14,2)
                                                    END AS valor
                                         FROM administracao.configuracao
                                         WHERE configuracao.cod_modulo = 64
                                           AND configuracao.exercicio = '".$this->getDado('exercicio')."'
                                           AND configuracao.parametro like 'tceto_config_credito_externo'
                                     ) AS perc_op_credito_externo
                                    , alteracao_lei_ppa.cod_norma AS num_lei_alteracao_ppa
                                    , alteracao_lei_ppa.data_alteracao AS data_pub_lei_alteracao_ppa
                                    , TO_CHAR(ppa_publicacao.timestamp ,'yyyy-mm-dd') AS data_pub_lei_ppa

                                FROM ppa.ppa
                          
                                JOIN ppa.ppa_publicacao
                                  ON ppa_publicacao.cod_ppa = ppa.cod_ppa                             
                          
                                JOIN ldo.ldo 
                                  ON ldo.cod_ppa  = ppa.cod_ppa
                                 
                                JOIN ldo.homologacao
                                  ON homologacao.cod_ppa  = ldo.cod_ppa
                                 AND homologacao.ano = ldo.ano           
                            
                                JOIN normas.norma
                                  ON ppa_publicacao.cod_norma  = norma.cod_norma
                        
                                LEFT JOIN tceto.alteracao_lei_ppa
                                    ON alteracao_lei_ppa.cod_norma = norma.cod_norma
                               
                                WHERE '".$this->getDado('exercicio')."' BETWEEN ppa.ano_inicio AND ppa.ano_final
                    )AS leis_orcamentarias

                GROUP BY cod_und_gestora                       
                       , exercicio
                       , complementacao
                       , num_lei_ppa                
                       , data_lei_ppa
                       , num_ldo
                       , data_ldo
                       , data_pub_ldo
                       , num_loa
                       , data_loa
                       , data_pub_loa
                       , perc_credito_adicional
                       , perc_op_credito_antecipacao
                       , perc_op_credito_interno
                       , perc_op_credito_externo
                       , num_lei_alteracao_ppa
                       , data_pub_lei_alteracao_ppa
                       , data_pub_lei_ppa

                ORDER BY num_lei_ppa ";
        
        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }
     
}
?>
