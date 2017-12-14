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
 * Data de Criação: 14/02/2012

 * @author Analista: Carlos Adriano
 * @author Desenvolvedor: Carlos Adriano

 * @package URBEM
 * @subpackage Mapeamento

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTRNAnexo23 extends Persistente
{
    public function TTRNAnexo23()
    {
        $this->setEstrutura( array() );
        $this->setEstruturaAuxiliar( array() );
        $this->setDado('exercicio', Sessao::getExercicio());
    }

    public function recuperaHeader(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaHeader",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaHeader()
    {
        $stSql = "SELECT '0' AS tipo_registro
                        , 'OBRAS' AS nome_arquivo
                        , '".$this->getDado('exercicio')."0".$this->getDado('inBimestre')."' AS bimestre
                        , 'O' AS tipo_arquivo
                        , to_char(CURRENT_DATE,'dd/mm/yyyy') AS dt_arquivo
                        , substr(CAST(CURRENT_TIME AS text),1,8) AS hr_arquivo
                        , configuracao_entidade.valor AS cod_orgao
                        , sw_cgm.nom_cgm AS nom_orgao
                        , '0000000001' AS num_registro

                    FROM administracao.configuracao_entidade

              INNER JOIN orcamento.entidade
                      ON entidade.exercicio    = configuracao_entidade.exercicio
                     AND entidade.cod_entidade = configuracao_entidade.cod_entidade

              INNER JOIN sw_cgm
                      ON sw_cgm.numcgm = entidade.numcgm

                   WHERE configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
                     AND configuracao_entidade.cod_entidade = ( SELECT valor::INTEGER
                                                                  FROM administracao.configuracao
                                                                 WHERE parametro = 'cod_entidade_prefeitura'
                                                                   AND exercicio = '".$this->getDado('exercicio')."' )
                     AND configuracao_entidade.cod_modulo   = 49
                     AND configuracao_entidade.parametro    = 'cod_orgao_tce'";

        return $stSql;
    }

    public function recuperaObras(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaObras().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaObras()
    {
        $stSql = "SELECT
                          '1' AS tipo_registro
                        , o.num_obra
                        , o.obra
                        , o.objetivo
                        , o.localizacao
                        , o.cod_cidade
                        , (SELECT cod_recurso FROM orcamento.recurso WHERE cod_recurso = o.cod_recurso_1 AND exercicio = '".$this->getDado('exercicio')."') AS fonte_recurso_1
                        , (SELECT cod_recurso FROM orcamento.recurso WHERE cod_recurso = o.cod_recurso_2 AND exercicio = '".$this->getDado('exercicio')."') AS fonte_recurso_2
                        , (SELECT cod_recurso FROM orcamento.recurso WHERE cod_recurso = o.cod_recurso_3 AND exercicio = '".$this->getDado('exercicio')."') AS fonte_recurso_3
                        , replace(lpad(trim(to_char(o.valor_recurso_1, '9999999999D99')), 15, '0'), ',', '') AS valor_fonte_1
                        , replace(lpad(trim(to_char(o.valor_recurso_2, '9999999999D99')), 15, '0'), ',', '') AS valor_fonte_2
                        , replace(lpad(trim(to_char(o.valor_recurso_3, '9999999999D99')), 15, '0'), ',', '') AS valor_fonte_3
                        , replace(lpad(trim(to_char(o.valor_orcamento_base, '9999999999D99')), 15, '0'), ',', '') AS valor_orcamento_base
                        , o.projeto_existente
                        , o.observacao
                        , replace(lpad(trim(to_char(o.latitude, '999D99')), 6, '0'), ',', '') AS latitude
                        , replace(lpad(trim(to_char(o.longitude, '999D99')), 6, '0'), ',', '') AS longitude
                        , o.rdc

                    FROM tcern.obra AS o

                   WHERE o.exercicio    = '".$this->getDado('exercicio')."'
                     AND o.cod_entidade IN (".$this->getDado('inCodEntidade').")";

        return $stSql;
    }

    public function recuperaObraContratos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaObraContratos().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaObraContratos()
    {
        $stSql = "SELECT
             '2' AS tipo_registro
                , obra_contrato.id
                , obra_contrato.num_obra
                , obra_contrato.servico
                , obra_contrato.processo_licitacao
                , sw_cgm.nom_cgm
                , sw_cgm_pessoa_juridica.cnpj
                , obra_contrato.num_contrato
                , replace(lpad(trim(to_char(obra_contrato.valor_contrato, '9999999999D99')), 15, '0'), ',', '') AS valor_contrato
                , replace(lpad(trim(to_char(obra_contrato.valor_executado_exercicio, '9999999999D99')), 15, '0'), ',', '') AS valor_executado_exercicio
                , replace(lpad(trim(to_char(obra_contrato.valor_a_exercutar, '9999999999D99')), 15, '0'), ',', '') AS valor_a_exercutar
                , TO_CHAR(obra_contrato.dt_inicio_contrato, 'dd/mm/yyyy') AS dt_inicio_contrato
                , TO_CHAR(obra_contrato.dt_termino_contrato, 'dd/mm/yyyy') AS dt_termino_contrato
                , obra_contrato.num_art
                , replace(lpad(trim(to_char(obra_contrato.valor_iss, '9999999999D99')), 15, '0'), ',', '') AS valor_iss
                , obra_contrato.num_dcms
                , replace(lpad(trim(to_char(obra_contrato.valor_inss, '9999999999D99')), 15, '0'), ',', '') AS valor_inss
                , sw_cgm_fiscal.nom_cgm AS nom_cgm_fiscal

               FROM tcern.obra_contrato

               INNER JOIN tcern.obra
                       ON obra.num_obra = obra_contrato.num_obra

               INNER JOIN sw_cgm
                       ON sw_cgm.numcgm = obra_contrato.numcgm

               INNER JOIN sw_cgm_pessoa_juridica
                       ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm

               INNER JOIN sw_cgm AS sw_cgm_fiscal
                       ON sw_cgm_fiscal.numcgm = obra_contrato.numcgm_fiscal

              WHERE obra.exercicio    = '".$this->getDado('exercicio')."'
                AND obra.cod_entidade IN (".$this->getDado('inCodEntidade').")";

        return $stSql;
    }

    public function recuperaObraAcompanhamentos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaObraAcompanhamentos().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaObraAcompanhamentos()
    {
        $stSql = "SELECT
            '3' AS tipo_registro
                , obra_acompanhamento.id
                , obra_acompanhamento.obra_contrato_id
                , TO_CHAR(obra_acompanhamento.dt_evento, 'dd/mm/yyyy') AS dt_evento
                , sw_cgm.nom_cgm
                , obra_acompanhamento.cod_situacao
                , obra_acompanhamento.justificativa

                FROM tcern.obra_acompanhamento

          INNER JOIN tcern.obra_contrato
                  ON obra_contrato.id = obra_acompanhamento.obra_contrato_id

          INNER JOIN tcern.obra
                  ON obra.num_obra = obra_contrato.num_obra

          INNER JOIN sw_cgm
                  ON sw_cgm.numcgm = obra_contrato.numcgm

         WHERE obra.exercicio    = '".$this->getDado('exercicio')."'
           AND obra.cod_entidade IN (".$this->getDado('inCodEntidade').")";

        return $stSql;
    }

    public function recuperaObraAditivos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaObraAditivos().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaObraAditivos()
    {
        $stSql = "SELECT
            '4' AS tipo_registro
                , obra_aditivo.id
                , obra_aditivo.obra_contrato_id
                , obra_aditivo.num_aditivo
                , TO_CHAR(obra_aditivo.dt_aditivo, 'dd/mm/yyyy') AS dt_aditivo
                , obra_aditivo.prazo
                , obra_aditivo.prazo_aditado
                , replace(lpad(trim(to_char(obra_aditivo.valor, '9999999999D99')), 15, '0'), ',', '') AS valor
                , replace(lpad(trim(to_char(obra_aditivo.valor_aditado, '9999999999D99')), 15, '0'), ',', '') AS valor_aditado
                , obra_aditivo.num_art
                , obra_aditivo.motivo

                FROM tcern.obra_aditivo

          INNER JOIN tcern.obra_contrato
                  ON obra_contrato.id = obra_aditivo.obra_contrato_id

          INNER JOIN tcern.obra
                  ON obra.num_obra = obra_contrato.num_obra

         WHERE obra.exercicio    = '".$this->getDado('exercicio')."'
           AND obra.cod_entidade IN (".$this->getDado('inCodEntidade').")";

        return $stSql;
    }

}
