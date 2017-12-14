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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBAFaixaSalario2 extends Persistente
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
        $stSql = "  SELECT  1 as tipo_registro
                            , '".$this->getDado('unidade_gestora')."' as unidade_gestora
                            , cargo_padrao.cod_cargo AS cod_cargo_emprego
                            , padrao_padrao.vigencia AS data_inicio_salario
                            , ''::VARCHAR            AS data_final_salario
                            , padrao_padrao.valor    AS faixa_inicial_salario
                            , padrao_padrao.valor    AS faixa_final_salario
                            , norma.num_norma        AS numero_lei
                            , norma.dt_publicacao    AS data_publicacao
        
                    FROM folhapagamento".$this->getDado('entidade_rh').".padrao

                    INNER JOIN folhapagamento".$this->getDado('entidade_rh').".padrao_padrao
                            ON padrao_padrao.cod_padrao = padrao.cod_padrao

                    INNER JOIN (SELECT  padrao_padrao.cod_padrao
                                        , max(padrao_padrao.timestamp) as timestamp
                                FROM folhapagamento".$this->getDado('entidade_rh').".padrao_padrao
                                --ultimoTimestampPeriodoMovimentacao(cod_periodo_movimentacao, cod_entidade_rh)
                                WHERE padrao_padrao.timestamp <= (SELECT ultimoTimestampPeriodoMovimentacao(
                                                                                        (SELECT cod_periodo_movimentacao 
                                                                                         FROM folhapagamento".$this->getDado('entidade_rh').".periodo_movimentacao
                                                                                         WHERE TO_CHAR(dt_inicial,'mmyyyy')= '".$this->getDado('mes_ano')."')
                                                                                        ,'".$this->getDado('entidade_rh')."')
                                                                )::TIMESTAMP
                                GROUP BY padrao_padrao.cod_padrao
                                ) as max_padrao_padrao
                            ON max_padrao_padrao.cod_padrao = padrao_padrao.cod_padrao
                           AND max_padrao_padrao.timestamp = padrao_padrao.timestamp
    
                    INNER JOIN pessoal".$this->getDado('entidade_rh').".cargo_padrao
                            ON cargo_padrao.cod_padrao  = padrao.cod_padrao
 
                    INNER JOIN (SELECT  cargo_padrao.cod_cargo
                                        , max(cargo_padrao.timestamp) as timestamp
                                FROM pessoal".$this->getDado('entidade_rh').".cargo_padrao
                                --ultimoTimestampPeriodoMovimentacao(cod_periodo_movimentacao, cod_entidade_rh)
                                WHERE cargo_padrao.timestamp <= (SELECT ultimoTimestampPeriodoMovimentacao(
                                                                                        (SELECT cod_periodo_movimentacao 
                                                                                         FROM folhapagamento".$this->getDado('entidade_rh').".periodo_movimentacao
                                                                                         WHERE TO_CHAR(dt_inicial,'mmyyyy')= '".$this->getDado('mes_ano')."')
                                                                                        ,'".$this->getDado('entidade_rh')."')
                                                                )::TIMESTAMP
                                GROUP BY cargo_padrao.cod_cargo
                                ) as max_padrao_cargo
                           ON max_padrao_cargo.cod_cargo = cargo_padrao.cod_cargo
                          AND max_padrao_cargo.timestamp = cargo_padrao.timestamp

                    INNER JOIN normas.norma
                            ON norma.cod_norma = padrao_padrao.cod_padrao

                    ORDER BY cargo_padrao.cod_cargo
        ";
        
        return $stSql;
    }

}
