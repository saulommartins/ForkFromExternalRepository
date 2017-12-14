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

class TTCMBAProvApo extends Persistente
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
        $stSql = "  SELECT  
                          1 as tipo_registro
                        ,'".$this->getDado('unidade_gestora')."' AS unidade_gestora
                        , servidores.tipo_ato_pessoal            AS tipo_ato_pessoal
                        , contrato.registro                      AS matricula
                        , servidores.data_validade_ato           AS data_validade_ato
                        , evento.codigo                          AS sequencial_provento
                        , ''                                     AS reservado_tcm
                        , remove_acentos(evento.descricao)       AS nome_provento
                        , remove_acentos(evento.descricao)       AS descricao_provento
                        , evento_calculado.valor                 AS valor_provento
                        , '".$this->getDado('ano_mes')."'        AS competencia
                        , ''::VARCHAR                            AS fundamentacao_legal
                        , servidores.tipo_provento               AS tipo_provento
                        , ''::VARCHAR                            AS data_ato

                    FROM pessoal".$this->getDado('entidade_rh').".contrato
                    
                    INNER JOIN (
                                SELECT 
                                    36 as tipo_ato_pessoal
                                    , 1 as tipo_provento
                                    , aposentadoria.cod_contrato
                                    , aposentadoria.dt_concessao as  data_validade_ato
            
                                FROM pessoal".$this->getDado('entidade_rh').".contrato_servidor
            
                                INNER JOIN pessoal".$this->getDado('entidade_rh').".aposentadoria
                                        ON aposentadoria.cod_contrato = contrato_servidor.cod_contrato 
                                       AND aposentadoria.timestamp = (SELECT MAX(timestamp)
                                                                        FROM pessoal".$this->getDado('entidade_rh').".aposentadoria as max
                                                                       WHERE max.cod_contrato = aposentadoria.cod_contrato)
            
                                UNION 
            
                                SELECT 
                                    35 as tipo_ato_pessoal
                                    , 2 as tipo_provento
                                    , contrato_pensionista.cod_contrato
                                    , contrato_pensionista.dt_inicio_beneficio as data_validade_ato
            
                                FROM pessoal".$this->getDado('entidade_rh').".contrato_servidor
            
                                INNER JOIN pessoal".$this->getDado('entidade_rh').".pensionista
                                       ON pensionista.cod_contrato_cedente = contrato_servidor.cod_contrato
            
                                INNER JOIN pessoal".$this->getDado('entidade_rh').".contrato_pensionista
                                        ON contrato_pensionista.cod_pensionista         = pensionista.cod_pensionista
                                       AND contrato_pensionista.cod_contrato_cedente   = pensionista.cod_contrato_cedente
                                )as servidores
                        ON servidores.cod_contrato = contrato.cod_contrato

                    INNER JOIN folhapagamento".$this->getDado('entidade_rh').".registro_evento_periodo
                            ON registro_evento_periodo.cod_contrato = servidores.cod_contrato
                           AND registro_evento_periodo.cod_periodo_movimentacao = (SELECT cod_periodo_movimentacao 
                                                                                     FROM folhapagamento".$this->getDado('entidade_rh').".periodo_movimentacao
                                                                                    WHERE TO_CHAR(dt_inicial,'mmyyyy')= '".$this->getDado('mes_ano')."')

                    INNER JOIN folhapagamento".$this->getDado('entidade_rh').".registro_evento
                            ON registro_evento.cod_registro = registro_evento_periodo.cod_registro

                    INNER JOIN folhapagamento".$this->getDado('entidade_rh').".evento_calculado
                            ON evento_calculado.cod_evento         = registro_evento.cod_evento
                           AND evento_calculado.cod_registro       = registro_evento.cod_registro
                           AND evento_calculado.timestamp_registro = registro_evento.timestamp

                    INNER JOIN folhapagamento".$this->getDado('entidade_rh').".evento
                            ON evento.cod_evento = evento_calculado.cod_evento

                    ORDER BY servidores.cod_contrato
                            , sequencial_provento
        ";
        
        return $stSql;
    }

}
