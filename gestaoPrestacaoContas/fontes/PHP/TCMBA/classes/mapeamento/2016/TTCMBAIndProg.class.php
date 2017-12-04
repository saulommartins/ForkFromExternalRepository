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
    * Página de Include Oculta - Exportação Arquivos TCMBA

    * Data de Criação   : 03/07/2015

    * @author Analista: Ane Caroline
    * @author Desenvolvedor: Lisiane Morais

    $Id $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBAIndProg extends Persistente
{

    public function recuperaIndicadores(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaIndicadores().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaIndicadores()
    {
        $stSql = " SELECT 1 AS tipo_registro
                        , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
                        , programa.num_programa AS cod_programa
                        , programa_dados.num_unidade
                        , SUBSTR(TRIM(programa_indicadores.descricao), 0, 100) AS descricao_indicador
                        , COALESCE(SUM(acao_recurso.valor),0.00) AS situacao_inicial
                        , 0.00 AS situacao_modificada
                        , ppa.ano_inicio AS ano_inicial
                        , ppa.ano_final AS ano_final
                        , row_number() over( order by programa.num_programa ) as sequencial

                     FROM ppa.acao

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

                LEFT JOIN ppa.acao_recurso
                       ON acao.cod_acao = acao_recurso.cod_acao
                      AND acao.ultimo_timestamp_acao_dados = acao_recurso.timestamp_acao_dados

               INNER JOIN ppa.programa
                       ON acao.cod_programa = programa.cod_programa

               INNER JOIN ppa.programa_dados
                       ON programa_dados.cod_programa = programa.cod_programa
                      AND programa_dados.timestamp_programa_dados = programa.ultimo_timestamp_programa_dados

               INNER JOIN ppa.programa_setorial
                       ON programa.cod_setorial = programa_setorial.cod_setorial

               INNER JOIN ppa.macro_objetivo
                       ON macro_objetivo.cod_macro = programa_setorial.cod_macro

               INNER JOIN ppa.ppa
                       ON macro_objetivo.cod_ppa = ppa.cod_ppa

               INNER JOIN ppa.programa_indicadores
                       ON programa_dados.cod_programa = programa_indicadores.cod_programa
                      AND programa_indicadores.timestamp_programa_dados = programa_dados.timestamp_programa_dados

                    WHERE ppa.ano_inicio <= '".$this->getDado('exercicio')."'
                      AND ppa.ano_final >= '".$this->getDado('exercicio')."'

                 GROUP BY programa.num_programa
                        , programa_dados.num_unidade
                        , programa_indicadores.descricao
                        , ppa.ano_inicio
                        , ppa.ano_final
        ";

        return $stSql;
    }
}
