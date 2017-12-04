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
    * Extensão da Classe de mapeamento
    * Data de Criação: 30/10/2015
    * @author Analista: Gelson
    * @author Desenvolvedor: Evandro Melos
    * @package URBEM
    * @subpackage Mapeamento
    * $Id:$
*/

include_once ( CLA_PERSISTENTE );

class TTCMBAEditPregaoElet extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }
    
    function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaDadosTribunal()
    {
        $stSql = "  SELECT  1 as tipo_registro 
                            , '".$this->getDado('unidade_gestora')."' as unidade_gestora
                            , edital.exercicio||LPAD(edital.num_edital::VARCHAR,8,'0') as num_edital
                            , CASE  WHEN modalidade.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 1 THEN 1
                                    WHEN modalidade.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 2 THEN 2
                                    WHEN modalidade.cod_modalidade = 3 AND licitacao.registro_precos = TRUE THEN 3
                                    WHEN modalidade.cod_modalidade = 5 THEN 4
                                    WHEN modalidade.cod_modalidade = 1 AND tipo_objeto.cod_tipo_objeto = 1 THEN 5
                                    WHEN modalidade.cod_modalidade = 1 AND tipo_objeto.cod_tipo_objeto = 2 THEN 6
                                    WHEN modalidade.cod_modalidade = 4 THEN 7
                                    WHEN modalidade.cod_modalidade = 2 AND tipo_objeto.cod_tipo_objeto = 1 THEN 10
                                    WHEN modalidade.cod_modalidade = 2 AND tipo_objeto.cod_tipo_objeto = 2 THEN 12
                                    WHEN modalidade.cod_modalidade = 6 AND licitacao.registro_precos = FALSE THEN 14
                                    WHEN modalidade.cod_modalidade = 7 AND licitacao.registro_precos = FALSE THEN 15
                                    WHEN modalidade.cod_modalidade = 1 AND licitacao.registro_precos = TRUE THEN 16
                                    WHEN modalidade.cod_modalidade = 2 AND licitacao.registro_precos = TRUE THEN 17
                                    WHEN modalidade.cod_modalidade = 6 AND licitacao.registro_precos = TRUE THEN 18
                                    WHEN modalidade.cod_modalidade = 7 AND licitacao.registro_precos = TRUE THEN 19
                                    WHEN modalidade.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 4 THEN 22
                                    WHEN modalidade.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 3 THEN 23
                            END AS edital_modalidade
                            , TO_CHAR(edital.dt_entrega_propostas,'ddmmyyyy') as data_recebimento_proposta
                            , REPLACE(edital.hora_entrega_propostas,':','')::varchar as hora_recebimento_proposta
                            , TO_CHAR(edital.dt_final_entrega_propostas,'ddmmyyyy') as data_final_entrega
                            , REPLACE(edital.hora_final_entrega_propostas,':','')::varchar as hora_final_entrega
                            , TO_CHAR(edital.dt_abertura_propostas,'ddmmyyyy') as data_sessao_disputa
                            , REPLACE(edital.hora_abertura_propostas,':','') as hora_inicio_disputa

                    FROM licitacao.edital

                    INNER JOIN licitacao.publicacao_edital
                            ON publicacao_edital.num_edital = edital.num_edital
                           AND publicacao_edital.exercicio  = edital.exercicio

                    INNER JOIN licitacao.licitacao
                            ON licitacao.cod_licitacao  = edital.cod_licitacao
                           AND licitacao.cod_modalidade = edital.cod_modalidade
                           AND licitacao.cod_entidade   = edital.cod_entidade   
                           AND licitacao.exercicio      = edital.exercicio_licitacao 

                    INNER JOIN licitacao.homologacao
                            ON homologacao.cod_licitacao  = licitacao.cod_licitacao
                           AND homologacao.cod_modalidade = licitacao.cod_modalidade
                           AND homologacao.cod_entidade   = licitacao.cod_entidade
                           AND homologacao.exercicio_licitacao = licitacao.exercicio

                    INNER JOIN compras.objeto
                            ON objeto.cod_objeto = licitacao.cod_objeto

                    INNER JOIN compras.tipo_objeto
                            ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto

                    INNER JOIN compras.modalidade
                            ON modalidade.cod_modalidade = licitacao.cod_modalidade

                    WHERE edital.exercicio = '".$this->getDado('exercicio')."'
                      AND edital.cod_entidade IN (".$this->getDado('entidades').")
                      AND edital.dt_aprovacao_juridico BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') 
                                                           AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                      AND edital.cod_modalidade IN (6,7) 
             ";                    
        return $stSql;
    }

}

?>