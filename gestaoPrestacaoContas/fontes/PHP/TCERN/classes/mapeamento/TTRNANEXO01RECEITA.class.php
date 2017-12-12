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

    * @author Desenvolvedor: Jean Felipe da Silva

    * @package URBEM
    * @subpackage Mapeamento
/
/*
$Log$
Revision 1.1  2007/07/11 04:46:53  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 14/02/2012

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTRNANEXO01RECEITA extends Persistente
{
function TTRNANEXO01RECEITA()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
    $this->setDado('exercicio',Sessao::getExercicio());
}

/**
    * Método Construtor
    * @access Private
*/
/*function TTRNAnex1Receita() {
    $this->setEstrutura(array());
    $this->setEstruturaAuxiliar(array());
    $this->setDado('exercicio', Sessao::getExercicio() );
} */

function recuperaHeader(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaHeader().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaHeader()
{
    $stSql = "SELECT '0' AS tipo_registro
                    , 'RECEITA' AS nome_arquivo
                    , '".$this->getDado('exercicio')."0".$this->getDado('inBimestre')."' AS bimestre
                    , 'O' AS tipo_arquivo
                    , to_char(CURRENT_DATE,'dd/mm/yyyy') AS dt_arquivo
                    , substr(CAST(CURRENT_TIME AS text),1,8) AS hr_arquivo
                    , configuracao_entidade.valor AS cod_orgao
            , sw_cgm.nom_cgm AS nom_orgao

                FROM administracao.configuracao_entidade
        JOIN orcamento.entidade
          ON entidade.exercicio = configuracao_entidade.exercicio
         AND entidade.cod_entidade = configuracao_entidade.cod_entidade
        JOIN sw_cgm
          ON sw_cgm.numcgm = entidade.numcgm

        WHERE configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
          AND configuracao_entidade.cod_entidade = ( SELECT valor
                                                   FROM administracao.configuracao
                                                  WHERE parametro = 'cod_entidade_prefeitura'
                                                    AND exercicio = '".$this->getDado('exercicio')."' )
          AND configuracao_entidade.cod_modulo = 49
          AND configuracao_entidade.parametro = 'cod_orgao_tce'";

    return $stSql;
}

function recuperaDetalhe(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaDetalhe",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaDetalhe()
{
    $stSql = "SELECT '1' AS tipo_registro
                    , receita.cod_recurso AS nr_fonte
                    , recurso.nom_recurso AS descricao

                FROM orcamento.receita
                JOIN orcamento.conta_receita
                  ON conta_receita.exercicio = receita.exercicio
                 AND conta_receita.cod_conta = receita.cod_conta
                JOIN orcamento.recurso
                  ON recurso.exercicio = receita.exercicio
                 AND recurso.cod_recurso = receita.cod_recurso
                 WHERE receita.cod_entidade IN (".$this->getDado('inCodEntidade').") AND
                       receita.exercicio IN ('".$this->getDado('exercicio')."')
                GROUP BY nr_fonte
                        , nom_recurso
                ORDER BY nr_fonte";

    return $stSql;
}

function montaRecuperaRelacionamento()
{
    $stSql = "SELECT '2' AS tipo_registro
                      , '' AS brancos
                      , CASE WHEN substr(cod_tc,1,1) = '9'
                             THEN substr(replace(cod_tc,'.',''),1,9)
                             ELSE substr(replace(cod_tc,'.',''),1,8)||' '
                        END AS cod_receita
                      , lpad(replace(coalesce(max(vl_original),000)::varchar,'.',''), 14,'0') AS vl_inicial
                      , lpad(replace(coalesce(max(vl_original),000)::varchar,'.',''), 14,'0') AS vl_atualizado
                      , lpad(replace(coalesce(sum(valor),000)::varchar,'.',''), 14,'0') AS vl_bimestre
                      , lpad(replace(coalesce(sum(valor_exercicio),000)::varchar,'.',''), 14,'0') AS vl_exercicio
                      , substr(cod_fonte, 2, 4) AS fonte_recurso

                FROM tcern.fn_exportacao_receita_anexo ('".$this->getDado('exercicio')."',
                            '".$this->getDado('inCodEntidade')."',
                            ".$this->getDado('inBimestre').")
                        AS tabela ( cod_estrutural varchar
                                    , cod_tc char(9)
                                    , valor numeric
                                    , valor_exercicio numeric
                                    , vl_original numeric
                                    , cod_fonte varchar
                                  )
                WHERE contabilidade.fn_tipo_conta_plano('".$this->getDado('exercicio')."',cod_estrutural)='A'
                GROUP BY tabela.cod_tc
                        , tabela.cod_fonte
                ORDER BY cod_receita
            ";

    return $stSql;
}

}
