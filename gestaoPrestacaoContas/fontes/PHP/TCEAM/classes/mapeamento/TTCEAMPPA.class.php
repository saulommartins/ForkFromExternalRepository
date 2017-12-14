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

    * @author Tonismar R. Bernardo

    * @date: 21/03/2011

    * @ignore
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEAMPPA extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMPPA()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function montaRecuperaTodos()
    {
        $stSQL = "
            SELECT *
              FROM (
                SELECT LPAD(acao.cod_acao::varchar,4,0::varchar) AS cod_acao
                     , LPAD( acao.num_acao::varchar,4,0::varchar) AS num_acao
                     , acao_dados.descricao
                     , acao_dados.titulo
                     , programa.cod_programa
                     , programa_dados.identificacao
                     , programa_dados.objetivo
                     , programa_dados.diagnostico
                     , programa_dados.diretriz
                     , programa_dados.publico_alvo
                     , programa_dados.continuo
                     , to_real(SUM(acao_recurso.valor)) AS valor
                     , acao.ultimo_timestamp_acao_dados
                     , ppa.cod_ppa
                     , acao_dados.cod_funcao
                     , acao_dados.cod_subfuncao
                     , funcao.descricao AS desc_funcao
                     , subfuncao.descricao AS desc_subfuncao
                     , SUBSTR(acao.cod_acao::varchar,1,1) AS cod_tipo
--                     , acao_dados.cod_tipo
                     , tipo_acao.descricao as desc_tipo
                     , '' AS exercicio
                  FROM ppa.acao
            INNER JOIN ppa.acao_recurso
                    ON acao.cod_acao = acao_recurso.cod_acao
                   AND acao.ultimo_timestamp_acao_dados = acao_recurso.timestamp_acao_dados
            INNER JOIN ppa.acao_dados
                    ON acao.cod_acao = acao_dados.cod_acao
                   AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
            INNER JOIN ppa.tipo_acao
                    ON acao_dados.cod_tipo = tipo_acao.cod_tipo
             LEFT JOIN orcamento.funcao
                    ON acao_dados.exercicio = funcao.exercicio
                   AND acao_dados.cod_funcao = funcao.cod_funcao
             LEFT JOIN orcamento.subfuncao
                    ON acao_dados.exercicio = subfuncao.exercicio
                   AND acao_dados.cod_subfuncao = subfuncao.cod_subfuncao
            INNER JOIN ppa.programa
                    ON acao.cod_programa = programa.cod_programa
            INNER JOIN ppa.programa_dados
                    ON programa.ultimo_timestamp_programa_dados = programa_dados.timestamp_programa_dados
            INNER JOIN ppa.programa_setorial
                    ON programa.cod_setorial = programa_setorial.cod_setorial
            INNER JOIN ppa.macro_objetivo
                    ON macro_objetivo.cod_macro = programa_setorial.cod_macro
            INNER JOIN ppa.ppa
                    ON macro_objetivo.cod_ppa = ppa.cod_ppa
        ";
        $stSQL .= "GROUP BY acao.cod_acao
                          , acao_dados.descricao
                          , acao_dados.titulo
                          , programa.cod_programa
                          , programa_dados.identificacao
                          , programa_dados.objetivo
                          , programa_dados.diagnostico
                          , programa_dados.diretriz
                          , programa_dados.publico_alvo
                          , programa_dados.continuo
                          , acao.ultimo_timestamp_acao_dados
                          , ppa.cod_ppa
                          , acao_dados.cod_funcao
                          , acao_dados.cod_subfuncao
                          , funcao.descricao
                          , subfuncao.descricao
                          , acao_dados.cod_tipo
                          , tipo_acao.cod_tipo
                          , tipo_acao.descricao";
        $stSQL .= "
                      UNION

                SELECT LPAD(pao.num_pao::varchar,4,0::varchar) AS cod_acao
                     , LPAD( acao.num_acao::varchar,4,0::varchar) AS num_acao
                     , pao.nom_pao AS descricao
                     , pao.nom_pao AS titulo
                     , null AS cod_programa
                     , '' AS identificacao
                     , '' AS objetivo
                     , '' AS diagnostico
                     , '' AS diretriz
                     , '' AS publico_alvo
                     , null AS continuo
                     , TO_REAL(0) AS valor
                     , null AS ultimo_timestamp_acao_dados
                     , null AS cod_ppa
                     , null AS cod_funcao
                     , null AS cod_subfuncao
                     , '' AS desc_funcao
                     , '' AS desc_subfuncao
                    -- , (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) AS cod_tipo
                     , SUBSTR(pao.num_pao::varchar,1,1) AS cod_tipo
                     , CASE WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) = 1 )
                            THEN 'Projeto'
     WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) = 2 )
                            THEN 'Atividade'
                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) = 3 )
                            THEN 'Operações Especiais'
                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) = 4 )
                            THEN 'Não Orçamentária'
                       END AS desc_tipo
                     , pao.exercicio
                  FROM orcamento.pao
                 INNER JOIN ( SELECT num_pao
                                   , MAX(exercicio) AS exercicio
                                FROM orcamento.pao
                            GROUP BY num_pao
                            ) AS max_pao
                         ON max_pao.num_pao   = pao.num_pao
                        AND max_pao.exercicio = pao.exercicio
                 
                 INNER JOIN  orcamento.pao_ppa_acao
                         ON  pao_ppa_acao.exercicio=pao.exercicio
                        AND  pao_ppa_acao.num_pao=pao.num_pao

                 INNER JOIN  ppa.acao
                         ON  acao.cod_acao=pao_ppa_acao.cod_acao
                  
                      WHERE NOT EXISTS ( SELECT 1
                                           FROM orcamento.pao_ppa_acao
                                          WHERE pao.exercicio = pao_ppa_acao.exercicio
                                            AND pao.num_pao   = pao_ppa_acao.num_pao)
                    ) AS tabela ";

        return $stSQL . $stCondicao . ' ' . $stOrdem;

    }
}
