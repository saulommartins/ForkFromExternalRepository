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
/*
 * Extensão da Classe de mapeamento Arquivo: Salario2.txt
 *
 * @package URBEM
 * @subpackage Mapeamento
 * @version $Id: TTCMBASalario2.class.php 63946 2015-11-10 21:10:32Z michel $
 * @author Michel Teixeira
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBASalario2 extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct() {
        parent::__construct();
        $this->setEstrutura( array() );
        $this->setEstruturaAuxiliar( array() );
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDados(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDados().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaDados()
    {
        $stSql = "  SELECT 1 AS tipo_registro
                         , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                         , '".$this->getDado('competencia')."' AS competencia
                         , *
                      FROM tcmba.recuperaServidorSalario2(  '".$this->getDado('exercicio')."'
                                                          , '".$this->getDado('entidades')."'
                                                          , '".$this->getDado('entidade_rh')."'
                                                          , '".$this->getDado('mes_ano')."' )
                ";

        return $stSql;
    }

    function recuperaLogErro(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaLogErro().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaLogErro()
    {
        $stSql = "
            SELECT count(contrato.cod_contrato)                         AS registros
                 , ( SELECT count(cod_evento)
                       FROM folhapagamento".$this->getDado('entidade_rh').".tcmba_salario_base
                      WHERE cod_entidade IN (".$this->getDado('entidades').")
                        AND exercicio = '".$this->getDado('exercicio')."'
                   ) AS obrigatorio1
                 , ( SELECT count(cod_evento)
                       FROM folhapagamento".$this->getDado('entidade_rh').".tcmba_vantagens_salariais
                      WHERE cod_entidade IN (".$this->getDado('entidades').")
                        AND exercicio = '".$this->getDado('exercicio')."'
                   ) AS obrigatorio2
                 , ( SELECT count(cod_evento)
                       FROM folhapagamento".$this->getDado('entidade_rh').".tcmba_gratificacao_funcao
                      WHERE cod_entidade IN (".$this->getDado('entidades').")
                        AND exercicio = '".$this->getDado('exercicio')."'
                   ) AS obrigatorio3
                 , ( SELECT count(cod_evento)
                       FROM folhapagamento".$this->getDado('entidade_rh').".tcmba_salario_familia
                      WHERE cod_entidade IN (".$this->getDado('entidades').")
                        AND exercicio = '".$this->getDado('exercicio')."'
                   ) AS obrigatorio4
                 , ( SELECT count(cod_evento)
                       FROM folhapagamento".$this->getDado('entidade_rh').".tcmba_salario_horas_extras
                      WHERE cod_entidade IN (".$this->getDado('entidades').")
                        AND exercicio = '".$this->getDado('exercicio')."'
                   ) AS obrigatorio5
                 , ( SELECT count(cod_evento)
                       FROM folhapagamento".$this->getDado('entidade_rh').".tcmba_salario_descontos
                      WHERE cod_entidade IN (".$this->getDado('entidades').")
                        AND exercicio = '".$this->getDado('exercicio')."'
                   ) AS obrigatorio6
                 , ( SELECT count(cod_evento)
                       FROM folhapagamento".$this->getDado('entidade_rh').".tcmba_plano_saude
                      WHERE cod_entidade IN (".$this->getDado('entidades').")
                        AND exercicio = '".$this->getDado('exercicio')."'
                   ) AS obrigatorio7
                 , count(contrato_servidor.num_orgao)                   AS obrigatorio8
                 , count(de_para_tipo_cargo_tcmba.cod_tipo_cargo_tce)   AS obrigatorio9
                 , count(tcmba_cargo_servidor.cod_cargo)                AS obrigatorio10
                 , count(fonte_recurso_lotacao.cod_orgao)               AS obrigatorio11
                 , count(tcmba_cargo_servidor_temporario.cod_cargo)     AS obrigatorio12
              FROM (
                    SELECT contrato.cod_contrato
                      FROM pessoal".$this->getDado('entidade_rh').".contrato

                INNER JOIN folhapagamento".$this->getDado('entidade_rh').".periodo_movimentacao
                        ON to_char(periodo_movimentacao.dt_inicial,'mmyyyy') = '".$this->getDado('mes_ano')."'

                 LEFT JOIN folhapagamento".$this->getDado('entidade_rh').".registro_evento_periodo
                        ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao
                       AND contrato.cod_contrato = registro_evento_periodo.cod_contrato 

                 LEFT JOIN ( select max(cod_complementar) as cod_complementar
                                  , cod_contrato
                                  , cod_periodo_movimentacao 
                               from folhapagamento".$this->getDado('entidade_rh').".registro_evento_complementar
                           group by registro_evento_complementar.cod_contrato
                                  , registro_evento_complementar.cod_periodo_movimentacao 
                           ) AS registro_evento_complementar
                        ON contrato.cod_contrato = registro_evento_complementar.cod_contrato
                       AND registro_evento_complementar.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao

                 LEFT JOIN folhapagamento".$this->getDado('entidade_rh').".registro_evento_ferias
                        ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_ferias.cod_periodo_movimentacao
                       AND contrato.cod_contrato = registro_evento_ferias.cod_contrato

                 LEFT JOIN folhapagamento".$this->getDado('entidade_rh').".registro_evento_decimo
                        ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_decimo.cod_periodo_movimentacao
                       AND contrato.cod_contrato = registro_evento_decimo.cod_contrato 

                     WHERE registro_evento_periodo.cod_contrato      IS NOT NULL
                        OR registro_evento_complementar.cod_contrato IS NOT NULL
                        OR registro_evento_decimo.cod_contrato       IS NOT NULL
                        OR registro_evento_ferias.cod_contrato       IS NOT NULL

                  GROUP BY contrato.cod_contrato
                   ) AS contrato

        INNER JOIN (   SELECT servidor_contrato_servidor.cod_contrato
                            , contrato_servidor_orgao.cod_orgao
                            , de_para_lotacao_orgao.num_orgao
                            , contrato_servidor.cod_sub_divisao
                            , contrato_servidor.cod_cargo
                         FROM pessoal".$this->getDado('entidade_rh').".servidor
                   INNER JOIN pessoal".$this->getDado('entidade_rh').".servidor_contrato_servidor
                           ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                   INNER JOIN pessoal".$this->getDado('entidade_rh').".contrato_servidor
                           ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato
                   INNER JOIN (SELECT contrato_servidor_orgao.cod_contrato
                                    , contrato_servidor_orgao.cod_orgao
                                    , contrato_servidor_orgao.timestamp
                                 FROM pessoal".$this->getDado('entidade_rh').".contrato_servidor_orgao
                                WHERE contrato_servidor_orgao.timestamp = (
                                                                            SELECT max(CSO.timestamp) AS timestamp
                                                                              FROM pessoal".$this->getDado('entidade_rh').".contrato_servidor_orgao AS CSO
                                                                             WHERE CSO.cod_contrato = contrato_servidor_orgao.cod_contrato
                                                                          )
                              ) AS contrato_servidor_orgao
                           ON contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato
                    LEFT JOIN pessoal".$this->getDado('entidade_rh').".de_para_lotacao_orgao
                           ON de_para_lotacao_orgao.cod_orgao = contrato_servidor_orgao.cod_orgao
                          AND de_para_lotacao_orgao.exercicio = '".$this->getDado('exercicio')."'

                        UNION

                       SELECT contrato_pensionista.cod_contrato
                            , contrato_pensionista_orgao.cod_orgao
                            , de_para_lotacao_orgao.num_orgao
                            , contrato_servidor.cod_sub_divisao
                            , contrato_servidor.cod_cargo
                         FROM pessoal".$this->getDado('entidade_rh').".pensionista
                   INNER JOIN pessoal".$this->getDado('entidade_rh').".contrato_pensionista
                           ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                          AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
                   INNER JOIN pessoal".$this->getDado('entidade_rh').".contrato_servidor
                           ON contrato_servidor.cod_contrato = contrato_pensionista.cod_contrato_cedente
                   INNER JOIN (SELECT contrato_pensionista_orgao.*
                                 FROM pessoal".$this->getDado('entidade_rh').".contrato_pensionista_orgao
                                WHERE contrato_pensionista_orgao.timestamp = (
                                                                                SELECT max(CPO.timestamp) AS timestamp
                                                                                  FROM pessoal".$this->getDado('entidade_rh').".contrato_pensionista_orgao AS CPO
                                                                                 WHERE CPO.cod_contrato = contrato_pensionista_orgao.cod_contrato
                                                                             )
                              ) AS contrato_pensionista_orgao
                           ON contrato_pensionista_orgao.cod_contrato = contrato_pensionista.cod_contrato
                    LEFT JOIN pessoal".$this->getDado('entidade_rh').".de_para_lotacao_orgao
                           ON de_para_lotacao_orgao.cod_orgao = contrato_pensionista_orgao.cod_orgao
                          AND de_para_lotacao_orgao.exercicio = '".$this->getDado('exercicio')."'
                   ) AS contrato_servidor
                ON contrato.cod_contrato = contrato_servidor.cod_contrato

         LEFT JOIN pessoal".$this->getDado('entidade_rh').".de_para_tipo_cargo_tcmba
                ON de_para_tipo_cargo_tcmba.cod_sub_divisao = contrato_servidor.cod_sub_divisao

         LEFT JOIN ( select cod_cargo
                          , cod_entidade
                          , exercicio
                       from folhapagamento".$this->getDado('entidade_rh').".tcmba_cargo_servidor
                   group by cod_cargo
                          , cod_entidade
                          , exercicio
                   ) AS tcmba_cargo_servidor
                ON tcmba_cargo_servidor.cod_cargo = contrato_servidor.cod_cargo
               AND tcmba_cargo_servidor.cod_entidade IN (".$this->getDado('entidades').")
               AND tcmba_cargo_servidor.exercicio = '".$this->getDado('exercicio')."'

         LEFT JOIN ( select cod_orgao
                          , cod_entidade
                          , exercicio
                       from tcmba.fonte_recurso_lotacao
                   group by cod_orgao
                          , cod_entidade
                          , exercicio
                   ) AS fonte_recurso_lotacao
                ON fonte_recurso_lotacao.cod_orgao = contrato_servidor.cod_orgao
               AND fonte_recurso_lotacao.cod_entidade IN (".$this->getDado('entidades').")
               AND fonte_recurso_lotacao.exercicio = '".$this->getDado('exercicio')."'

         LEFT JOIN ( select cod_cargo
                          , cod_entidade
                          , exercicio
                       from folhapagamento".$this->getDado('entidade_rh').".tcmba_cargo_servidor_temporario
                   group by cod_cargo
                          , cod_entidade
                          , exercicio
                   ) AS tcmba_cargo_servidor_temporario
                ON tcmba_cargo_servidor_temporario.cod_cargo = contrato_servidor.cod_cargo
               AND tcmba_cargo_servidor_temporario.cod_entidade IN (".$this->getDado('entidades').")
               AND tcmba_cargo_servidor_temporario.exercicio = '".$this->getDado('exercicio')."' ";

        return $stSql;
    }

}
