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
    * Classe de mapeamento da tabela empenho.ordem_pagamento_retencao
    * Data de Criação: 21/03/2007

    * @author Analista: Muriel Karine Preuss
    * @author Desenvolvedor: Anderson C. Konze

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: tonismar $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.03.28,uc-02.03.05

*/
/*
$Log$
Revision 1.2  2007/06/20 18:16:47  cako
Bug#9378#

Revision 1.1  2007/04/30 19:19:57  cako
implementação uc-02.03.28

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  empenho.ordem_pagamento_retencao
  * Data de Criação: 21/03/2007

  * @author Analista: Muriel Karine Preuss
  * @author Desenvolvedor: Anderson C. Konze

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoOrdemPagamentoRetencao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoOrdemPagamentoRetencao()
{
    parent::Persistente();
    $this->setTabela("empenho.ordem_pagamento_retencao");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_entidade,cod_ordem,cod_plano,sequencial');

    $this->AddCampo('exercicio'   ,'char'   ,true  ,'4'  ,true,'TContabilidadePlanoAnalitica');
    $this->AddCampo('cod_entidade','integer',true  ,''   ,true,'TEmpenhoOrdemPagamento');
    $this->AddCampo('cod_ordem'   ,'integer',true  ,''   ,true,'TEmpenhoOrdemPagamento');
    $this->AddCampo('cod_plano'   ,'integer',true  ,''   ,true,'TContabilidadePlanoAnalitica');
    $this->AddCampo('vl_retencao' ,'integer',true  ,''   ,false,false);
    $this->AddCampo('cod_receita' ,'integer',false ,''   ,true,'TOrcamentoReceita');
    $this->AddCampo('sequencial'  ,'integer',true  ,''   ,true,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= " SELECT opr.exercicio                                               \n";
    $stSql .= "       ,opr.cod_entidade                                            \n";
    $stSql .= "       ,opr.cod_ordem                                               \n";
    $stSql .= "       ,opr.cod_plano                                               \n";
    if (Sessao::getExercicio() > 2012) {
        $stSql .= "       ,CASE WHEN tipo.cod_receita is not null                  \n";
        $stSql .= "            THEN tipo.nom_conta_receita                         \n";
        $stSql .= "            ELSE pc.nom_conta                                   \n";
        $stSql .= "       END AS nom_conta                                         \n";
    } else {
        $stSql .= "       ,pc.nom_conta                                            \n";
    }
    $stSql .= "       ,opr.vl_retencao                                             \n";
    $stSql .= "       ,tipo.cod_receita                                            \n";
    $stSql .= "       ,CASE WHEN tipo.cod_receita is not null                      \n";
    $stSql .= "            THEN 'O'                                                \n";
    $stSql .= "            ELSE 'E'                                                \n";
    $stSql .= "       END AS tipo                                                  \n";
    $stSql .= "   FROM empenho.ordem_pagamento_retencao as opr                     \n";

    $stSql .= "        JOIN empenho.ordem_pagamento as OP                          \n";
    $stSql .= "             ON (     opr.cod_ordem      = op.cod_ordem             \n";
    $stSql .= "                  AND opr.cod_entidade   = op.cod_entidade          \n";
    $stSql .= "                  AND opr.exercicio      = op.exercicio             \n";
    $stSql .= "             )                                                      \n";
    $stSql .="        JOIN contabilidade.plano_analitica as PA                             \n";
    $stSql .="        ON (     pa.cod_plano = opr.cod_plano                                \n";
    $stSql .="             AND pa.exercicio = opr.exercicio )                              \n";
    $stSql .="        JOIN contabilidade.plano_conta as PC                                 \n";
    $stSql .="        ON (     pa.cod_conta = pc.cod_conta                                 \n";
    $stSql .="             AND pa.exercicio = pc.exercicio )                               \n";
    $stSql .="        LEFT JOIN (                                                          \n";
    $stSql .="            SELECT rec.cod_receita                                           \n";
    $stSql .="                   ,ocr.cod_estrutural                                       \n";
    if (Sessao::getExercicio() > 2012) {
        $stSql .="                   ,configuracao_lancamento_receita.cod_conta                \n";
    }
    $stSql .="                   ,ocr.exercicio                                            \n";
    $stSql .="                   ,ocr.descricao AS nom_conta_receita                       \n";
    $stSql .="              FROM orcamento.conta_receita as OCR                            \n";
    $stSql .="                   JOIN orcamento.receita as rec                             \n";
    $stSql .="                   ON (     rec.cod_conta = ocr.cod_conta                    \n";
    $stSql .="                        AND rec.exercicio = ocr.exercicio )                  \n";
    if (Sessao::getExercicio() > 2012) {
        $stSql .="                    join contabilidade.configuracao_lancamento_receita       \n";
        $stSql .="                      on configuracao_lancamento_receita.cod_conta_receita = OCR.cod_conta \n";
        $stSql .="                     and configuracao_lancamento_receita.exercicio = OCR.exercicio \n";
        $stSql .="               LEFT JOIN contabilidade.desdobramento_receita \n";
        $stSql .="                      ON desdobramento_receita.cod_receita_secundaria = rec.cod_receita \n";
        $stSql .="                     AND desdobramento_receita.exercicio   = rec.exercicio \n";
    }
    $stSql .="             WHERE ocr.exercicio = '".$this->getDado('exercicio')."'         \n";
    $stSql .="                   AND rec.cod_entidade = ".$this->getDado('cod_entidade')." \n";
    if (Sessao::getExercicio() > 2012) {
        $stSql .="                   AND configuracao_lancamento_receita.estorno = 'f'         \n";
        $stSql .="                   AND desdobramento_receita.cod_receita_secundaria IS NULL  \n";
    }
    $stSql .="        ) as tipo                                                             \n";
    if (Sessao::getExercicio() > 2012) {
        $stSql .="        ON (     tipo.cod_conta = pc.cod_conta                \n";
        $stSql .="             AND tipo.exercicio = pc.exercicio                     \n";
        $stSql .="             AND tipo.cod_receita = opr.cod_receita  )             \n";
    } else {
        $stSql .="        ON (     '4.'||tipo.cod_estrutural = pc.cod_estrutural                \n";
        $stSql .="             AND tipo.exercicio            = pc.exercicio  )                  \n";
    }

    $stSql .= "  WHERE opr.cod_ordem is not null                                         \n";
    if($this->getDado('cod_ordem'))
        $stSql .= " AND opr.cod_ordem = ".$this->getDado('cod_ordem')."            \n";
    if($this->getDado('cod_entidade'))
        $stSql .= " AND opr.cod_entidade = ".$this->getDado('cod_entidade')."      \n";
    if($this->getDado('exercicio'))
        $stSql .= " AND opr.exercicio = '".$this->getDado('exercicio')."'            \n";

    return $stSql;
}

function recuperaContaLancamento(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaContaLancamento().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContaLancamento()
{
    $stSql .= " SELECT contabilidade.fn_recupera_conta_lancamento('".$this->getDado('exercicio')."'   \n";
    $stSql .= "                                                   ,".$this->getDado('cod_entidade')." \n";
    $stSql .= "                                                   ,".$this->getDado('cod_lote')."     \n";
    $stSql .= "                                                   ,'P'                                \n";
    $stSql .= "                                                   ,".$this->getDado('sequencia')."    \n";
    $stSql .= "                                                   ,'".$this->getDado('tipo_valor')."') as cod_plano  \n";

    return $stSql;
}

function recuperaCodPlanoReceita(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if ( Sessao::getExercicio() > '2012' ) {
        $stSql = $this->montaRecuperaCodPlanoReceitaTCE().$stOrdem;
    } else {
        $stSql = $this->montaRecuperaCodPlanoReceita().$stOrdem;
    }
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCodPlanoReceita()
{
    $stSql .= "SELECT pa.cod_plano, rec.cod_receita                                          \n";
    $stSql .= "  FROM orcamento.receita as rec                                               \n";
    $stSql .= "       JOIN orcamento.conta_receita as ocr                                    \n";
    $stSql .= "       ON (   ocr.cod_conta = rec.cod_conta                                   \n";
    $stSql .= "          AND ocr.exercicio = rec.exercicio )                                 \n";
    $stSql .= "       JOIN contabilidade.plano_conta as pc                                   \n";
    $stSql .= "       ON (   '4.'||ocr.cod_estrutural = pc.cod_estrutural                    \n";
    $stSql .= "          AND ocr.exercicio            = pc.exercicio )                       \n";
    $stSql .= "       JOIN contabilidade.plano_analitica as pa                               \n";
    $stSql .= "       ON (   pa.cod_conta = pc.cod_conta                                     \n";
    $stSql .= "          AND pc.exercicio = pa.exercicio )                                   \n";
    $stSql .= " WHERE pa.exercicio = '".$this->getDado('exercicio')."'                       \n";
if($this->getDado('cod_plano'))
    $stSql .= "   AND pa.cod_plano = '".$this->getDado('cod_plano')."'                       \n";
if($this->getDado('cod_receita'))
    $stSql .= "   AND rec.cod_receita = ".$this->getDado('cod_receita')."                    \n";
    $stSql .= "   AND rec.cod_entidade = ".$this->getDado('cod_entidade')."                  \n";

/*  $stSql .= "   AND NOT EXISTS (  SELECT dr.cod_receita_secundaria -- Secundárias          \n";
    $stSql .= "                       FROM contabilidade.desdobramento_receita as dr         \n";
    $stSql .= "                      WHERE   rec.cod_receita = dr.cod_receita_secundaria     \n";
    $stSql .= "                          AND rec.exercicio   = dr.exercicio );               \n"; */

    return $stSql;
}

function montaRecuperaCodPlanoReceitaTCE()
{
    $stSql  = "     SELECT plano_analitica.cod_plano                                                    \n";
    $stSql .= "          , receita.cod_receita                                                          \n";
    $stSql .= "       FROM orcamento.receita                                                            \n";
    $stSql .= "       JOIN orcamento.conta_receita                                                      \n";
    $stSql .= "         ON receita.cod_conta = conta_receita.cod_conta                                  \n";
    $stSql .= "        AND receita.exercicio = conta_receita.exercicio                                  \n";
    $stSql .= "                                                                                         \n";
    $stSql .= "       JOIN contabilidade.configuracao_lancamento_receita                                \n";
    $stSql .= "         ON configuracao_lancamento_receita.cod_conta_receita = conta_receita.cod_conta  \n";
    $stSql .= "        AND configuracao_lancamento_receita.exercicio = conta_receita.exercicio          \n";
    $stSql .= "                                                                                         \n";
    $stSql .= "       JOIN contabilidade.plano_conta                                                    \n";
    $stSql .= "         ON plano_conta.cod_conta = configuracao_lancamento_receita.cod_conta            \n";
    $stSql .= "        AND plano_conta.exercicio = configuracao_lancamento_receita.exercicio            \n";
    $stSql .= "                                                                                         \n";
    $stSql .= "       JOIN contabilidade.plano_analitica                                                \n";
    $stSql .= "         ON plano_analitica.cod_conta = plano_conta.cod_conta                            \n";
    $stSql .= "        AND plano_analitica.exercicio = plano_conta.exercicio                            \n";
    $stSql .= "                                                                                         \n";
    $stSql .= "      WHERE plano_conta.exercicio = '".$this->getDado('exercicio')."'                    \n";
    if( $this->getDado('cod_receita') )
    $stSql .= "        AND receita.cod_receita = ".$this->getDado('cod_receita')."                      \n";
    if( $this->getDado('cod_plano') )
    $stSql .= "        AND plano_analitica.cod_plano = '".$this->getDado('cod_plano')."'                \n";
    $stSql .= "        AND receita.cod_entidade = ".$this->getDado('cod_entidade')."                    \n";
    $stSql .= "        AND configuracao_lancamento_receita.estorno = '".$this->getDado('estorno')."'    \n";

    return $stSql;
}

function proximoSequencial(&$inCod, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = "SELECT COALESCE(MAX(sequencial),0) AS codigo FROM empenho.ordem_pagamento_retencao
              WHERE cod_ordem = ".$this->getDado('cod_ordem')." AND cod_entidade = ".$this->getDado('cod_entidade')." AND exercicio = '".$this->getDado('exercicio')."'";
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSql($rsRecordSet,$stSql,$boTransacao);

    if ( !$obErro->ocorreu() ) {
        if ($rsRecordSet->getCampo("codigo") > 0) {
            $inCod = $rsRecordSet->getCampo("codigo")+1;
        } else {
            $inCod = 1;
        }
    }

    return $obErro;
}

}
?>
