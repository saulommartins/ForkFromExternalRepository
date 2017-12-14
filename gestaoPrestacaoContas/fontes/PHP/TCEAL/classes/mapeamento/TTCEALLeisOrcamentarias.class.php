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

    * Extensão da Classe de Mapeamento TTCEALLeisOrcamentarias
    *
    * Data de Criação: 25/06/2014
    *
    * @author: Evandro Melos
    *
    * $Id: TTCEALLeisOrcamentarias.class.php 64806 2016-04-04 21:09:58Z carlos.silva $
    *
    * @ignore
    *
*/
class TTCEALLeisOrcamentarias extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALLeisOrcamentarias()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
       
    public function listarExportacaoLeisOrcamentarias(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
    {
        $stSql = "                
                SELECT
                    (SELECT PJ.cnpj
                        FROM orcamento.entidade
                        INNER JOIN sw_cgm_pessoa_juridica as PJ
                            ON PJ.numcgm = entidade.numcgm
                         WHERE entidade.exercicio = '".$this->getDado('exercicio')."'
                           AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                       ) AS cod_und_gestora
                     , ( SELECT CASE WHEN valor = '' 
                                   THEN '0000'::VARCHAR
                                   ELSE valor
                                   END valor
                               FROM administracao.configuracao_entidade
                              WHERE exercicio = '".$this->getDado('exercicio')."'
                            AND cod_entidade = ".$this->getDado('cod_entidade')."
                            AND cod_modulo = 62
                            AND parametro = 'tceal_configuracao_unidade_autonoma'
                        ) AS codigo_ua
                      , norma.cod_norma AS num_loa
                      , '".$this->getDado('exercicio')."'
                      , SUBSTR((SELECT CASE WHEN valor = ''
                                        THEN '0'
                                        ELSE valor
                                        END AS valor
                           FROM administracao.configuracao
                           WHERE configuracao.cod_modulo = 62
                             AND configuracao.exercicio = '".$this->getDado('exercicio')."'
                             AND configuracao.parametro like 'tceal_config_complementacao_loa'
                          ), 0, 254) AS complementacao
                                                   
                      , TO_CHAR(norma.dt_assinatura ,'DD/MM/YYYY') AS data_loa
                      , TO_CHAR(norma.dt_publicacao ,'DD/MM/YYYY') AS data_pub_loa
                          , CAST(( SELECT CASE WHEN valor = ''
                                THEN to_number('0','99')::numeric(14,2)
                                ELSE to_number(valor,'99')::numeric(14,2)
                                END AS valor
                         FROM administracao.configuracao
                        WHERE configuracao.cod_modulo = 62
                          AND configuracao.exercicio = '".$this->getDado('exercicio')."'
                          AND configuracao.parametro like 'tceal_config_credito_adicional'
                         ) AS int8) AS perc_credito_adicional
                         
                       , CAST(( SELECT CASE WHEN valor = ''
                                THEN to_number('0','99')::numeric(14,2)
                                ELSE to_number(valor,'99')::numeric(14,2)
                                END AS valor
                         FROM administracao.configuracao
                        WHERE configuracao.cod_modulo = 62
                          AND configuracao.exercicio = '".$this->getDado('exercicio')."'
                          AND configuracao.parametro like 'tceal_config_credito_antecipacao'
                         ) AS int8) AS perc_op_credito_antecipacao
                
                       , CAST(( SELECT CASE WHEN valor = ''
                                THEN to_number('0','99')::numeric(14,2)
                                ELSE to_number(valor,'99')::numeric(14,2)
                                END AS valor
                        FROM administracao.configuracao
                           WHERE configuracao.cod_modulo = 62
                         AND configuracao.exercicio = '".$this->getDado('exercicio')."'
                         AND configuracao.parametro like 'tceal_config_credito_interno'
                         ) AS int8) AS perc_op_credito_interno
                
                       , CAST(( SELECT CASE WHEN valor = ''
                                THEN to_number('0','99')::numeric(14,2)
                                ELSE to_number(valor,'99')::numeric(14,2)
                                END AS valor
                         FROM administracao.configuracao
                         WHERE configuracao.cod_modulo = 62
                           AND configuracao.exercicio = '".$this->getDado('exercicio')."'
                           AND configuracao.parametro like 'tceal_config_credito_externo'
                         ) AS int8) AS perc_op_credito_externo
                
                       , homologacao.cod_norma AS num_ldo
                       , TO_CHAR(ldo.timestamp ,'DD/MM/YYYY') AS data_ldo
                       , TO_CHAR(homologacao.timestamp ,'DD/MM/YYYY') AS data_pub_ldo
                       , ppa_publicacao.cod_norma AS num_lei_ppa
                       , TO_CHAR(ppa.timestamp ,'DD/MM/YYYY') AS data_lei_ppa
                       , ppa.fn_verifica_homologacao(ppa.cod_ppa) AS homologado
                       , TO_CHAR(ppa_publicacao.timestamp ,'DD/MM/YYYY') AS data_pub_lei_ppa
                       , '".$this->getDado('exercicio')."' AS exercicio

                      FROM normas.norma
                
                INNER JOIN ( SELECT * FROM ldo.homologacao ORDER BY timestamp DESC LIMIT 1) AS homologacao
                        ON homologacao.cod_norma = norma.cod_norma
                
                INNER JOIN ldo.ldo
                    ON ldo.cod_ppa = homologacao.cod_ppa
                       AND ldo.ano = homologacao.ano
                
                INNER JOIN ppa.ppa
                    ON ppa.cod_ppa = ldo.cod_ppa
                
                INNER JOIN ( SELECT * FROM ppa.ppa_publicacao ORDER BY timestamp DESC LIMIT 1) AS ppa_publicacao
                    ON ppa_publicacao.cod_ppa = ppa.cod_ppa
                
                WHERE '".$this->getDado('exercicio')."' BETWEEN ppa.ano_inicio AND ppa.ano_final
                ";
        
        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }
     
}
?>
