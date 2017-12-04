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
    * Classe de mapeamento da tabela EMPENHO.NOTA_LIQUIDACAO
    * Data de Criação: 30/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: leandro.zis $
    $Date: 2007-09-05 18:59:08 -0300 (Qua, 05 Set 2007) $

    * Casos de uso: uc-02.03.16
                    uc-02.03.04
*/

/*
$Log$
Revision 1.9  2007/09/05 21:53:40  leandro.zis
esfinge

Revision 1.8  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  EMPENHO.NOTA_LIQUIDACAO
  * Data de Criação: 30/11/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoNotaLiquidacaoItemAnulado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoNotaLiquidacaoItemAnulado()
{
    parent::Persistente();
    $this->setTabela('empenho.nota_liquidacao_item_anulado');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_empenho,cod_nota');

    $this->AddCampo('exercicio','char',true,'04',false,true);
    $this->AddCampo('cod_nota','integer',true,'',true,false);
    $this->AddCampo('num_item','integer',true,'',true,false);
    $this->AddCampo('exercicio_item','char',true,'04',false,true);
    $this->AddCampo('cod_pre_empenho','integer',true,'',true,true);
    $this->AddCampo('cod_entidade','integer',true,'',true,true);
    $this->AddCampo('vl_anulado','numeric',true,'14.2',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoManutencaoDatas(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoManutencaoDatas().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoManutencaoDatas()
{
    $stSql .= " SELECT                                                                      \n";
    $stSql .= "    nlia.exercicio,                                                          \n";
    $stSql .= "    nlia.cod_nota,                                                           \n";
    //$stSql .= "    nlia.num_item,                                                           \n";
    $stSql .= "        nlia.vl_anulado  as vl_anulado,                                      \n";
    $stSql .= "    to_char(nlia.timestamp,'dd/mm/yyyy') as dt_anulacao,                      \n";
    $stSql .= "    replace(replace(nlia.timestamp,' ',';'),'.','@') as timestamp_alterado   \n";
    $stSql .= " FROM                                                                        \n";
    $stSql .= "    empenho.empenho                      e,                              \n";
    $stSql .= "    empenho.nota_liquidacao              nl,                             \n";
    $stSql .= "    empenho.nota_liquidacao_item         nli,                            \n";
    $stSql .= "    empenho.nota_liquidacao_item_anulado nlia                            \n";
    $stSql .= " WHERE                                                                       \n";
    $stSql .= "    e.cod_empenho       = nl.cod_empenho        AND                          \n";
    $stSql .= "    e.cod_entidade      = nl.cod_entidade       AND                          \n";
    $stSql .= "    e.exercicio         = nl.exercicio_empenho  AND                          \n";
    $stSql .= "                                                                             \n";
    $stSql .= "    nl.exercicio        = nli.exercicio         AND                          \n";
    $stSql .= "    nl.cod_nota         = nli.cod_nota          AND                          \n";
    $stSql .= "    nl.cod_entidade     = nli.cod_entidade      AND                          \n";
    $stSql .= "                                                                             \n";
    $stSql .= "    nli.exercicio       = nlia.exercicio        AND                          \n";
    $stSql .= "    nli.cod_nota        = nlia.cod_nota         AND                          \n";
    $stSql .= "    nli.num_item        = nlia.num_item         AND                          \n";
    $stSql .= "    nli.exercicio_item  = nlia.exercicio_item   AND                          \n";
    $stSql .= "    nli.cod_pre_empenho = nlia.cod_pre_empenho  AND                          \n";
    $stSql .= "    nli.cod_entidade    = nlia.cod_entidade     AND                          \n";
    $stSql .= "                                                                             \n";
    $stSql .= "    e.cod_empenho       = '".$this->getDado('cod_empenho')."'         AND    \n";
    $stSql .= "    e.cod_entidade      = '".$this->getDado('cod_entidade')."'        AND    \n";
    $stSql .= "    e.exercicio         = '".$this->getDado('exercicio')."'                  \n";
    $stSql .= " GROUP BY                                                                    \n";
    $stSql .= "    nlia.exercicio,                                                          \n";
    $stSql .= "    nlia.cod_nota,                                                           \n";
    //$stSql .= "    nlia.num_item,                                                           \n";
    $stSql .= "        nlia.vl_anulado,                                                     \n";
    $stSql .= "    to_char(nlia.timestamp,'dd/mm/yyyy'),                                    \n";
    $stSql .= "    nlia.timestamp                                                           \n";

    return $stSql;
}

function montaRecuperaTimestampAnuladoLiquidacao()
{
    $stSql  = "SELECT                                                   \n";
    $stSql .= "    timestamp as timestampAnulado                        \n";
    $stSql .= "FROM                                                     \n";
    $stSql .= "    empenho.nota_liquidacao_item_anulado                 \n";

    return $stSql;

}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaTimestampAnuladoLiquidacao(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaTimestampAnuladoLiquidacao().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEstornoLiquidacaoEsfinge()
{
    $stSql  = "
select nota_liquidacao_item_anulado.cod_entidade
      ,nota_liquidacao.cod_empenho
      ,to_char(nota_liquidacao.dt_liquidacao, 'dd/mm/yyyy') as dt_liquidacao
      ,to_char(nota_liquidacao_item_anulado.timestamp, 'dd/mm/yyyy') as timestamp
      ,sum(nota_liquidacao_item_anulado.vl_anulado) as vl_anulado
from empenho.nota_liquidacao_item_anulado
join empenho.nota_liquidacao
  on nota_liquidacao_item_anulado.exercicio = nota_liquidacao.exercicio
 and nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao.cod_entidade
 and nota_liquidacao_item_anulado.cod_nota = nota_liquidacao.cod_nota
where nota_liquidacao_item_anulado.cod_entidade in (".$this->getDado('cod_entidade').")
  and nota_liquidacao_item_anulado.exercicio = '".$this->getDado('exercicio')."'
  and nota_liquidacao_item_anulado.timestamp between to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy')
  and to_date('".$this->getDado("dt_final")."','dd/mm/yyyy')
group by nota_liquidacao.cod_empenho, nota_liquidacao_item_anulado.cod_entidade,
nota_liquidacao.dt_liquidacao, nota_liquidacao_item_anulado.timestamp ";

  return $stSql;

}

/**
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaEstornoLiquidacaoEsfinge(&$rsRecordSet, $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaEstornoLiquidacaoEsfinge();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
