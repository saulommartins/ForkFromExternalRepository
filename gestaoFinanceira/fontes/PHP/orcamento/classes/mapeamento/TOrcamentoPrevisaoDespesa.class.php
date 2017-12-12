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
    * Classe de mapeamento da tabela ORCAMENTO_PREVISAO_DESPESA
    * Data de Criação: 16/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: lbbarreiro $
    $Date: 2008-04-07 10:06:52 -0300 (Seg, 07 Abr 2008) $

    * Casos de uso: uc-02.01.06, uc-02.01.33
*/

/*
$Log$
Revision 1.10  2006/10/24 13:56:52  bruce
Bug #7201#

Revision 1.9  2006/09/25 12:04:08  cleisson
Bug #7031#

Revision 1.8  2006/08/31 14:18:07  bruce
desenvolvimento

Revision 1.7  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORCAMENTO_PREVISAO_DESPESA
  * Data de Criação: 16/07/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Marcelo B. Paulino

*/
class TOrcamentoPrevisaoDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoPrevisaoDespesa()
{
    parent::Persistente();
    $this->setTabela('orcamento.previsao_despesa');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_despesa,periodo');

    $this->AddCampo('exercicio','char',true,'04',true,true);
    $this->AddCampo('cod_despesa','integer',true,'',true,true);
    $this->AddCampo('periodo','integer',true,'',true,false);
    $this->AddCampo('vl_previsto','numeric',true,'14,02',false,false);

}

function recuperaLimpaDespesa(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaLimpaTabelaDespesa().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaLimpaTabelaDespesa()
{
    $stQuebra = "\n";
    $stSql .= " DELETE FROM ".$this->getTabela()."                      ".$stQuebra;
    $stSql .= " WHERE exercicio   = '". $this->getDado('exercicio')."'    ".$stQuebra;
    $stSql .= " AND   cod_despesa = ". $this->getDado('cod_despesa')."  ".$stQuebra;

    return $stSql;
}

function montaRecuperaRelacionamento()
{
    $stQuebra = "\n";
    $stSql .= "  SELECT                                                         ".$stQuebra;
    $stSql .= "    CR.mascara_classificacao,                                    ".$stQuebra;
    $stSql .= "    CR.descricao,                                                ".$stQuebra;
    $stSql .= "    O.*,                                                         ".$stQuebra;
    $stSql .= "    UE.*                                                         ".$stQuebra;
    $stSql .= "  FROM                                                           ".$stQuebra;
    $stSql .= "    orcamento.vw_classificacao_despesa        AS CR,                   ".$stQuebra;
    $stSql .= "    orcamento.despesa               AS O,                    ".$stQuebra;
    $stSql .= "    orcamento.usuario_entidade      AS UE                    ".$stQuebra;
    $stSql .= "  WHERE                                                          ".$stQuebra;
    $stSql .= "        CR.exercicio IS NOT NULL                                 ".$stQuebra;
    $stSql .= "    AND O.cod_conta     = CR.cod_conta                           ".$stQuebra;
    $stSql .= "    AND O.exercicio     = CR.exercicio                           ".$stQuebra;
    $stSql .= "    AND UE.exercicio    = O.exercicio                            ".$stQuebra;
    $stSql .= "    AND UE.cod_entidade = O.cod_entidade                         ".$stQuebra;

    return $stSql;
}

function recuperaPrevisoesSintetico(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = '';
    if ($stFiltro) { $stFiltro = ' where '. $stFiltro ; }
    $stSql = $this->montaRecuperaPrevisoesSintetico( $stFiltro ) . $stOrdem ;
    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPrevisoesSintetico($stFiltro = '')
{
    $stSql .= '';
    $stSql .= "select conta_despesa.descricao                                                                            \n " ;
    $stSql .= "      ,conta_despesa.cod_conta                                                                            \n " ;
    $stSql .= "      ,conta_despesa.cod_estrutural                                                                       \n " ;
    $stSql .= "      ,previsoes.periodo                                                                                  \n " ;
    $stSql .= "      ,sum ( previsoes.vl_previsto ) as vl_previsto                                                       \n " ;
    $stSql .= "                                                                                                          \n " ;
    $stSql .= " from orcamento.conta_despesa                                                                             \n " ;
    $stSql .= " inner join                                                                                               \n " ;
    $stSql .= "    (                                                                                                     \n " ;
    $stSql .= "        select conta_despesa.cod_estrutural                                                               \n " ;
    $stSql .= "              ,previsao_despesa.periodo                                                                   \n " ;
    $stSql .= "              ,previsao_despesa.vl_previsto                                                               \n " ;
    $stSql .= "              ,previsao_despesa.exercicio                                                                 \n " ;
    $stSql .= "        from orcamento.conta_despesa                                                                      \n " ;
    $stSql .= "        inner join orcamento.despesa                                                                      \n " ;
    $stSql .= "            on ( conta_despesa.cod_conta = despesa.cod_conta                                              \n " ;
    $stSql .= "             and conta_despesa.exercicio = despesa.exercicio )                                            \n " ;
    $stSql .= "        JOIN orcamento.recurso('".$this->getDado('exercicio')."') as recurso                              \n " ;
    $stSql .= "        ON ( recurso.cod_recurso = despesa.cod_recurso AND recurso.exercicio = despesa.exercicio )        \n " ;
    $stSql .= "        inner join orcamento.previsao_despesa                                                             \n " ;
    $stSql .= "            on ( previsao_despesa.exercicio  = despesa.exercicio                                          \n " ;
    $stSql .= "             and previsao_despesa.cod_despesa = despesa.cod_despesa )                                     \n " ;
    $stSql .= "  $stFiltro                                                                                               \n " ;
    $stSql .= "    ) as previsoes                                                                                        \n " ;
    $stSql .= "    on ( previsoes.cod_estrutural like (publico.fn_mascarareduzida(conta_despesa.cod_estrutural) || '%')) \n " ;
    $stSql .= " where  conta_despesa.exercicio = '" . $this->getDado('exercicio'). "'                                    \n " ;
    $stSql .= "group by conta_despesa.descricao                                                                          \n " ;
    $stSql .= "      ,conta_despesa.cod_conta                                                                            \n " ;
    $stSql .= "      ,conta_despesa.cod_estrutural                                                                       \n " ;
    $stSql .= "      ,previsoes.periodo                                                                                  \n " ;
    $stSql .= "order by conta_despesa.cod_estrutural, previsoes.periodo                                                  \n " ;

    return $stSql;
}

function recuperaPrevisoesAnalitico(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if ($stFiltro) { $stFiltro = ' where '. $stFiltro ; }
    $stSql = $this->montaRecuperaPrevisoesAnalitico(). $stFiltro . $stOrdem ;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montarecuperaPrevisoesAnalitico()
{
    $stSql .= "select conta_despesa.cod_estrutural                                \n   " ;
    $stSql .= "      ,conta_despesa.cod_conta                                     \n   " ;
    $stSql .= "      ,conta_despesa.descricao                                     \n   " ;
    $stSql .= "      ,previsao_despesa.periodo                                    \n   " ;
    $stSql .= "      ,previsao_despesa.vl_previsto                                \n   " ;
    $stSql .= "      ,previsao_despesa.exercicio                                  \n   " ;
    $stSql .= "      ,recurso.masc_recurso_red                                    \n   " ;
    $stSql .= "      ,recurso.cod_detalhamento                                    \n   " ;
    $stSql .= "from orcamento.conta_despesa                                       \n   " ;
    $stSql .= "inner join orcamento.despesa                                       \n   " ;
    $stSql .= "    on ( conta_despesa.cod_conta = despesa.cod_conta               \n   " ;
    $stSql .= "     and conta_despesa.exercicio = despesa.exercicio )             \n   " ;
    $stSql .= "JOIN orcamento.recurso('".$this->getDado('exercicio')."') as recurso                              \n " ;
    $stSql .= "ON ( recurso.cod_recurso = despesa.cod_recurso AND recurso.exercicio = despesa.exercicio )        \n " ;
    $stSql .= "inner join orcamento.previsao_despesa                              \n   " ;
    $stSql .= "    on ( previsao_despesa.exercicio  = despesa.exercicio           \n   " ;
    $stSql .= "     and previsao_despesa.cod_despesa = despesa.cod_despesa )      \n   " ;

    return $stSql;

}

}
