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
    * Página de Mapeamento arquivo CadastroObra.txt TCM-BA
    * Data de Criação   : 30/09/2015
    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Evandro melos
    * $Id:$
*/

include_once ( CLA_PERSISTENTE );

class TTCMBACadastroObra extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaCadastroObra(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaCadastroObra().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCadastroObra()
    {
        $stSql =" SELECT 1 AS tipo_registro
                        , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                        , obra.nro_obra
                        , obra.data_cadastro
                        , obra.descricao
                        , obra.prazo
                        , obra_andamento.data_situacao
                        , obra.data_recebimento
                        , obra.data_inicio
                        , obra.data_aceite
                        , obra_andamento.cod_situacao as situacao_obra
                        , obra.vl_obra
                        , CASE WHEN obra.cod_modalidade = 8 THEN
                                licitacao.exercicio||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') 
                        END as processo_dispensa
                        , CASE WHEN obra.cod_modalidade != 8 THEN
                                licitacao.exercicio||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') 
                        END as processo_licitatorio
                        , '' as reservado_tcm
                        , CASE WHEN obra_andamento.cod_situacao IN(2,3) THEN
                                obra_andamento.justificativa
                            ELSE
                                ''
                        END as justificativa
                        , 'N' as anterior_siga
                        , CASE WHEN obra_contratos.nro_contrato IS NOT NULL THEN
                                'S'
                            ELSE
                                'N'
                        END as situacao_contrato
                        , obra.local
                        , obra.cep
                        , sw_bairro.nom_bairro
                        , tipo_funcao_obra.nro_funcao
                        , obra.cod_tipo
                        , obra_medicao.cod_medida
                        , obra_medicao.vl_medicao::NUMERIC(14,3) as vl_medicao
                        , '".$this->getDado('competencia')."' as competencia
                   FROM tcmba.obra

              LEFT JOIN tcmba.obra_andamento
                     ON obra_andamento.cod_obra     = obra.cod_obra
                    AND obra_andamento.cod_entidade = obra.cod_entidade
                    AND obra_andamento.exercicio    = obra.exercicio
                    AND obra_andamento.cod_tipo     = obra.cod_tipo
                    AND obra_andamento.data_situacao = (SELECT MAX(data_situacao) 
                                                        FROM tcmba.obra_andamento as TOA
                                                        WHERE obra_andamento.cod_obra   = obra.cod_obra
                                                        AND obra_andamento.cod_entidade = obra.cod_entidade
                                                        AND obra_andamento.exercicio    = obra.exercicio
                                                        AND obra_andamento.cod_tipo     = obra.cod_tipo )
              LEFT JOIN tcmba.obra_medicao
                     ON obra_medicao.cod_obra     = obra.cod_obra
                    AND obra_medicao.cod_entidade = obra.cod_entidade
                    AND obra_medicao.exercicio    = obra.exercicio
                    AND obra_medicao.cod_tipo     = obra.cod_tipo

             LEFT JOIN sw_bairro
                    ON sw_bairro.cod_bairro    = obra.cod_bairro
                   AND sw_bairro.cod_uf        = obra.cod_uf
                   AND sw_bairro.cod_municipio = obra.cod_municipio

             LEFT JOIN tcmba.obra_contratos
                    ON obra_contratos.cod_obra     = obra.cod_obra
                   AND obra_contratos.cod_entidade = obra.cod_entidade
                   AND obra_contratos.exercicio    = obra.exercicio
                   AND obra_contratos.cod_tipo     = obra.cod_tipo

             LEFT JOIN licitacao.licitacao
                    ON licitacao.cod_licitacao  = obra.cod_licitacao
                   AND licitacao.cod_modalidade = obra.cod_modalidade
                   AND licitacao.cod_entidade   = obra.cod_entidade
                   AND licitacao.exercicio      = obra.exercicio
                   
            INNER JOIN tcmba.tipo_funcao_obra 
                    ON tipo_funcao_obra.cod_funcao= obra.cod_funcao 


                 WHERE obra.data_inicio <= TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy') 
                   AND obra.data_recebimento >= TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy')
                   AND obra.cod_entidade IN (".$this->getDado('entidades').")
        ";
        
        return $stSql;
    }

}
