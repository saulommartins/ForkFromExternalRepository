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
    * Classe de mapeamento da tabela EMPENHO.NOTA_LIQUIDACAO_PAGA_ANULADA
    * Data de Criação: 11/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson Wolowski Gonçalves

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: leandro.zis $
    $Date: 2007-09-05 18:59:08 -0300 (Qua, 05 Set 2007) $

    * Casos de uso: uc-02.03.16,uc-02.03.04,uc-02.04.05
*/

/*
$Log$
Revision 1.10  2007/09/05 21:54:03  leandro.zis
esfinge

Revision 1.9  2007/08/15 19:32:35  vitor
Efetuada remoção de campos com parâmetros fixos

Revision 1.8  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoNotaLiquidacaoPagaAnulada extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoNotaLiquidacaoPagaAnulada()
{
    parent::Persistente();
    $this->setTabela('empenho.nota_liquidacao_paga_anulada');

    $this->setCampoCod('');
    $this->setComplementoChave('timestamp,exercicio,cod_nota,cod_entidade,timestamp_anulada');

    $this->AddCampo('timestamp',        'timestamp', true, '',     true,  true );
    $this->AddCampo('exercicio',        'char',      true, '04',   true,  true );
    $this->AddCampo('cod_nota',         'integer',   true, '',     true,  true );
    $this->AddCampo('cod_entidade',     'integer',   true, '',     true,  true );
    $this->AddCampo('timestamp_anulada','timestamp', true, '04',   true,  false);
    $this->AddCampo('vl_anulado',       'numeric',   true, '14,02',false, false);
    $this->AddCampo('observacao',       'varchar',   false,'',     false, false);

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
    $stSql  = "SELECT                                                                      \n";
    $stSql .= "     op.exercicio,                                                          \n";
    $stSql .= "     op.cod_ordem,                                                          \n";
    $stSql .= "     empenho.retorna_notas(op.exercicio,op.cod_ordem,op.cod_entidade) as notas,      \n";
    $stSql .= "     coalesce(sum(nlpa.vl_anulado),0.00) as vl_anulado,                                                  \n";
    $stSql .= "     to_char(nlpa.timestamp_anulada,'dd/mm/yyyy')  as timestamp                          \n";
    $stSql .= "FROM                                                                        \n";
    $stSql .= "    empenho.empenho                                     e,              \n";
    $stSql .= "    empenho.nota_liquidacao                             nl,             \n";
    $stSql .= "    empenho.nota_liquidacao_paga                        nlp,            \n";
    $stSql .= "    empenho.nota_liquidacao_paga_anulada                nlpa,           \n";
    $stSql .= "    empenho.pagamento_liquidacao_nota_liquidacao_paga   plnlp,          \n";
    $stSql .= "    empenho.pagamento_liquidacao                        pl,             \n";
    $stSql .= "    empenho.ordem_pagamento                             op              \n";
    $stSql .= "WHERE                                                                       \n";
    $stSql .= "     e.cod_empenho       = nl.cod_empenho                   AND             \n";
    $stSql .= "     e.cod_entidade      = nl.cod_entidade                  AND             \n";
    $stSql .= "     e.exercicio         = nl.exercicio_empenho             AND             \n";
    $stSql .= "                                                                            \n";
    $stSql .= "     nl.exercicio        = nlp.exercicio                    AND             \n";
    $stSql .= "     nl.cod_nota         = nlp.cod_nota                     AND             \n";
    $stSql .= "     nl.cod_entidade     = nlp.cod_entidade                 AND             \n";
    $stSql .= "                                                                            \n";
    $stSql .= "     nlp.exercicio       = nlpa.exercicio                   AND             \n";
    $stSql .= "     nlp.cod_nota        = nlpa.cod_nota                    AND             \n";
    $stSql .="     nlp.cod_entidade    = nlpa.cod_entidade                AND             \n";
    $stSql .="     nlp.timestamp       = nlpa.timestamp                   AND             \n";
    $stSql .= "                                                                            \n";
    $stSql .= "     nlp.cod_entidade    = plnlp.cod_entidade               AND             \n";
    $stSql .= "     nlp.cod_nota        = plnlp.cod_nota                   AND             \n";
    $stSql .= "     nlp.exercicio       = plnlp.exercicio_liquidacao       AND             \n";
    $stSql .= "     nlp.timestamp       = plnlp.timestamp                  AND             \n";
    $stSql .= "                                                                            \n";
    $stSql .= "     plnlp.cod_ordem             = pl.cod_ordem             AND             \n";
    $stSql .= "     plnlp.exercicio             = pl.exercicio             AND             \n";
    $stSql .= "     plnlp.cod_entidade          = pl.cod_entidade          AND             \n";
    $stSql .= "     plnlp.exercicio_liquidacao  = pl.exercicio_liquidacao  AND             \n";
    $stSql .= "     plnlp.cod_nota              = pl.cod_nota              AND             \n";
    $stSql .= "                                                                            \n";
    $stSql .= "     pl.exercicio        = op.exercicio                     AND             \n";
    $stSql .= "     pl.cod_ordem        = op.cod_ordem                     AND             \n";
    $stSql .= "     pl.cod_entidade     = op.cod_entidade                  AND             \n";
    $stSql .= "                                                                            \n";
    $stSql .= "    e.cod_empenho       = '".$this->getDado('cod_empenho')."'    AND        \n";
    $stSql .= "    e.cod_entidade      = '".$this->getDado('cod_entidade')."'   AND        \n";
    $stSql .= "    e.exercicio         = '".$this->getDado('exercicio')."'                 \n";
    $stSql .= "GROUP BY                                                                    \n";
    $stSql .= "     op.exercicio,                                                          \n";
    $stSql .= "     op.cod_ordem,                                                          \n";
    $stSql .= "     op.cod_entidade,                                                       \n";
    $stSql .= "     to_char(nlpa.timestamp_anulada,'dd/mm/yyyy')                           \n";

    return $stSql;
}

function montaRecuperaEstornoPagamentoEsfinge()
{
    $stSql  = "
select nota_liquidacao_paga_anulada.cod_entidade
      ,nota_liquidacao.cod_empenho
      ,to_char(nota_liquidacao_paga_anulada.timestamp_anulada, 'dd/mm/yyyy') as timestamp_anulada
      ,to_char(nota_liquidacao_paga_anulada.timestamp, 'dd/mm/yyyy') as timestamp
      ,sum(nota_liquidacao_paga_anulada.vl_anulado) as vl_anulado
from empenho.nota_liquidacao_paga_anulada
join empenho.nota_liquidacao
  on nota_liquidacao_paga_anulada.exercicio = nota_liquidacao.exercicio
 and nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao.cod_entidade
 and nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao.cod_nota
where nota_liquidacao_paga_anulada.cod_entidade in (".$this->getDado('cod_entidade').")
  and nota_liquidacao_paga_anulada.exercicio = '".$this->getDado('exercicio')."'
  and nota_liquidacao_paga_anulada.timestamp between to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy')
  and to_date('".$this->getDado("dt_final")."','dd/mm/yyyy')
group by nota_liquidacao_paga_anulada.cod_entidade
      ,nota_liquidacao.cod_empenho
      ,nota_liquidacao_paga_anulada.timestamp
      ,nota_liquidacao_paga_anulada.timestamp_anulada
";

  return $stSql;

}

/**
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaEstornoPagamentoEsfinge(&$rsRecordSet, $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaEstornoPagamentoEsfinge();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
