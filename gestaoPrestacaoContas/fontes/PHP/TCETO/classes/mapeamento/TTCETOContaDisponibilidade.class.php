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

    * Extensão da Classe de Mapeamento TTCETOContaDisponibilidade
    *
    * Data de Criação: 11/11/2014
    *
    * @author: Evandro Melos
    *
    * $Id: $
    *
    * @ignore
    *
*/
class TTCETOContaDisponibilidade extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCETOContaDisponibilidade()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function listarExportacaoContaDisponibilidade(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
    {
        $stSql = "
                    SELECT  sw_cgm_pessoa_juridica.cnpj as cod_und_gestora
                            , plano_analitica.exercicio
                            , LPAD(tceto.recupera_codigo_orgao('".$this->getDado('exercicio')."',despesa.cod_entidade::INTEGER, 'orgao')::varchar,2,'0') AS cod_orgao
                            , LPAD(tceto.recupera_codigo_orgao('".$this->getDado('exercicio')."',despesa.cod_entidade::INTEGER, 'unidade')::VARCHAR,4,'0') AS cod_und_orcamentaria
                            , RPAD(REPLACE(plano_conta.cod_estrutural,'.',''),17,'0') as cod_conta_contabil
                            , recurso.cod_recurso::VARCHAR as cod_rec_vinculado
                            , LPAD(banco.num_banco::VARCHAR,3, '0') as cod_banco
                            , LPAD(REPLACE(agencia.num_agencia,'-',''),5,'0') as cod_agencia_banco
                            , plano_banco.conta_corrente as num_conta_corrente
                            , CASE WHEN SUBSTR(REPLACE(plano_conta.cod_estrutural::varchar,'.',''),1,7) = '1111101' THEN '1'
                                   WHEN SUBSTR(REPLACE(plano_conta.cod_estrutural::varchar,'.',''),1,7) = '1111106' THEN '2'
                                   WHEN SUBSTR(REPLACE(plano_conta.cod_estrutural::varchar,'.',''),1,7) = '1111119' THEN '2'
                                   WHEN SUBSTR(REPLACE(plano_conta.cod_estrutural::varchar,'.',''),1,7) = '1111150' THEN '3'
                                   ELSE '9'
                               END AS tipo
                            , (SELECT CASE WHEN parametro = 'cod_entidade_prefeitura' THEN '1'
                                            WHEN parametro = 'cod_entidade_camara' THEN '2'
                                            WHEN parametro = 'cod_entidade_rpps' THEN '3'
                                            ELSE 9
                                      END AS classificacao
                                    FROM administracao.configuracao
                                    WHERE valor = despesa.cod_entidade::varchar
                                    AND cod_modulo = 8
                                    AND exercicio = '".$this->getDado('exercicio')."'
                                    AND parametro like 'cod_entidade_%'
                                    ORDER BY parametro DESC
                                    LIMIT 1
                            ) AS classificacao

                    FROM contabilidade.plano_banco

                    JOIN contabilidade.plano_analitica
                         ON plano_analitica.exercicio = plano_banco.exercicio
                        AND plano_analitica.cod_plano = plano_banco.cod_plano

                    JOIN contabilidade.plano_conta
                         ON plano_conta.exercicio = plano_analitica.exercicio
                        AND plano_conta.cod_conta = plano_analitica.cod_conta

                    JOIN contabilidade.plano_recurso
                         ON plano_recurso.exercicio = plano_analitica.exercicio
                        AND plano_recurso.cod_plano = plano_analitica.cod_plano

                    JOIN orcamento.recurso
                         ON recurso.exercicio   = plano_recurso.exercicio
                        AND recurso.cod_recurso = plano_recurso.cod_recurso

                    JOIN orcamento.despesa
                         ON despesa.exercicio   = recurso.exercicio
                        AND despesa.cod_recurso = recurso.cod_recurso
                        AND despesa.cod_entidade =  plano_banco.cod_entidade
                        
                    JOIN orcamento.entidade
                         ON entidade.exercicio      = despesa.exercicio
                        AND entidade.cod_entidade   = despesa.cod_entidade

                    JOIN monetario.conta_corrente
                        ON conta_corrente.cod_banco             = plano_banco.cod_banco
                        AND conta_corrente.cod_agencia          = plano_banco.cod_agencia
                        AND conta_corrente.cod_conta_corrente   = plano_banco.cod_conta_corrente

                    JOIN monetario.agencia
                        ON agencia.cod_banco    = conta_corrente.cod_banco
                        AND agencia.cod_agencia = conta_corrente.cod_agencia

                    JOIN  monetario.banco
                        ON banco.cod_banco  = agencia.cod_banco

                    LEFT JOIN sw_cgm_pessoa_juridica
                         ON sw_cgm_pessoa_juridica.numcgm = entidade.numcgm
                    
                    WHERE plano_analitica.exercicio ='".$this->getDado('exercicio')."'
                    AND plano_banco.cod_entidade IN (".$this->getDado('cod_entidade').")

                    GROUP BY  cod_und_gestora
                            , plano_analitica.exercicio
                            , despesa.cod_entidade
                            , plano_conta.cod_estrutural
                            , recurso.cod_recurso
                            , tipo
                            , banco.num_banco
                            , agencia.num_agencia
                            , plano_banco.conta_corrente
                            , classificacao
                    ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }

}

?>
