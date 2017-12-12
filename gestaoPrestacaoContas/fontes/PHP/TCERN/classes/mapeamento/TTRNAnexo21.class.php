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
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTRNAnexo21 extends Persistente
{
function TTRNAnexo21()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
    $this->setDado('exercicio', Sessao::getExercicio());
}

function recuperaHeader(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaHeader",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaHeader()
{
    $stSql = "SELECT '0' AS tipo_registro
                    , 'CONVENIOS' AS nome_arquivo
                    , '".$this->getDado('exercicio')."0".$this->getDado('inBimestre')."' AS bimestre
                    , 'O' AS tipo_arquivo
                    , to_char(CURRENT_DATE,'dd/mm/yyyy') AS dt_arquivo
                    , substr(CAST(CURRENT_TIME AS text),1,8) AS hr_arquivo
                    , configuracao_entidade.valor AS cod_orgao
                    , sw_cgm.nom_cgm AS nom_orgao

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

function recuperaConvenios(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaConvenios().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConvenios()
{
    $stSql = "SELECT
                      '1' AS tipo_registro
                    , '".$this->getDado('exercicio')."0".$this->getDado('inBimestre')."' AS bimestre
                    , c.cod_processo AS cod_processo
                    , sw_cgm_pessoa_juridica.cnpj
                    , sw_cgm.nom_cgm
                    , objeto.descricao AS objeto
                    , (SELECT nom_recurso FROM orcamento.recurso WHERE cod_recurso = c.cod_recurso_1 AND exercicio = '".$this->getDado('exercicio')."') AS fonte_recurso_1
                    , (SELECT nom_recurso FROM orcamento.recurso WHERE cod_recurso = c.cod_recurso_2 AND exercicio = '".$this->getDado('exercicio')."') AS fonte_recurso_2
                    , (SELECT nom_recurso FROM orcamento.recurso WHERE cod_recurso = c.cod_recurso_3 AND exercicio = '".$this->getDado('exercicio')."') AS fonte_recurso_3
                    , replace(lpad(trim(to_char(c.valor_recurso_1, '9999999999D99')), 15, '0'), ',', '') AS valor_fonte_1
                    , replace(lpad(trim(to_char(c.valor_recurso_2, '9999999999D99')), 15, '0'), ',', '') AS valor_fonte_2
                    , replace(lpad(trim(to_char(c.valor_recurso_3, '9999999999D99')), 15, '0'), ',', '') AS valor_fonte_3
                    , TO_CHAR(c.dt_inicio_vigencia, 'dd/mm/yyyy') AS inicio_vigencia
                    , TO_CHAR(c.dt_termino_vigencia, 'dd/mm/yyyy') AS termino_vigencia
                    , TO_CHAR(c.dt_assinatura, 'dd/mm/yyyy')   AS data_assinatura
                    , TO_CHAR(c.dt_publicacao, 'dd/mm/yyyy')   AS data_publicacao
                    , c.num_convenio

                FROM tcern.convenio AS c

          INNER JOIN sw_cgm
                  ON sw_cgm.numcgm = c.numcgm_recebedor

          INNER JOIN sw_cgm_pessoa_juridica
                  ON sw_cgm_pessoa_juridica.numcgm = c.numcgm_recebedor

          INNER JOIN compras.objeto
                  ON objeto.cod_objeto = c.cod_objeto

               WHERE c.exercicio    = '".$this->getDado('exercicio')."'
                 AND c.cod_entidade IN (".$this->getDado('inCodEntidade').")";

    return $stSql;
}

function recuperaContratos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaContratos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratos()
{
    $stSql = "SELECT
             '2' AS tipo_registro
            , '".$this->getDado('exercicio')."0".$this->getDado('inBimestre')."' AS bimestre
            , contrato.num_contrato
            , contrato.cod_conta_especifica
            , TO_CHAR(contrato.dt_entrega_recurso, 'dd/mm/yyyy') AS dt_entrega_recurso
            , replace(lpad(trim(to_char(contrato.valor_repasse, '9999999999D99')), 15, '0'), ',', '') AS valor_repasse
            , replace(lpad(trim(to_char(contrato.receita_aplicacao_financeira, '9999999999D99')), 15, '0'), ',', '') AS receita_aplicacao_financeira
            , replace(lpad(trim(to_char(contrato.valor_executado, '9999999999D99')), 15, '0'), ',', '') AS valor_executado
            , TO_CHAR(contrato.dt_recebimento_saldo, 'dd/mm/yyyy') AS dt_recebimento_saldo
            , TO_CHAR(contrato.dt_prestacao_contas, 'dd/mm/yyyy') AS dt_prestacao_contas
            , contrato.num_convenio
            , contrato.cod_processo AS cod_processo

           FROM tcern.contrato

          WHERE contrato.exercicio    = '".$this->getDado('exercicio')."'
            AND contrato.cod_entidade IN (".$this->getDado('inCodEntidade').")";

    return $stSql;
}

function recuperaAditivos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaAditivos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAditivos()
{
    $stSql = "SELECT
            '3' AS tipo_registro
             , '".$this->getDado('exercicio')."0".$this->getDado('inBimestre')."' AS bimestre
             , contrato_aditivo.num_contrato_aditivo
             , objeto.descricao AS objeto
             , replace(lpad(trim(to_char(contrato_aditivo.valor_aditivo, '9999999999D99')), 15, '0'), ',', '') AS valor_aditivo
             , TO_CHAR(contrato_aditivo.dt_inicio_vigencia, 'dd/mm/yyyy') AS inicio_vigencia
             , TO_CHAR(contrato_aditivo.dt_termino_vigencia, 'dd/mm/yyyy') AS termino_vigencia
             , TO_CHAR(contrato_aditivo.dt_assinatura, 'dd/mm/yyyy') AS data_assinatura
             , TO_CHAR(contrato_aditivo.dt_publicacao, 'dd/mm/yyyy') AS data_publicacao
             , contrato_aditivo.num_convenio
             , contrato_aditivo.cod_processo

             FROM tcern.contrato_aditivo

       INNER JOIN compras.objeto
               ON objeto.cod_objeto = contrato_aditivo.cod_objeto

            WHERE contrato_aditivo.exercicio    = '".$this->getDado('exercicio')."'
              AND contrato_aditivo.cod_entidade IN (".$this->getDado('inCodEntidade').")";

    return $stSql;
}

}
