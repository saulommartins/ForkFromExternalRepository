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
    * Classe de mapeamento da tabela EMPENHO.EMPENHO_AUTORIZACAO
    * Data de Criação: 30/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-09-06 17:58:13 -0300 (Qui, 06 Set 2007) $

    * Casos de uso: uc-02.03.03
                    uc-02.03.15
                    uc-02.03.02
*/

/*
$Log$
Revision 1.8  2007/09/06 20:57:17  luciano
Adicionada ao repositorio
Ticket#9094#

Revision 1.7  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  EMPENHO.EMPENHO_AUTORIZACAO
  * Data de Criação: 30/11/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoEmpenhoAutorizacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoEmpenhoAutorizacao()
{
    parent::Persistente();
    $this->setTabela('empenho.empenho_autorizacao');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_empenho,cod_autorizacao,cod_entidade');

    $this->AddCampo('cod_empenho','integer',true,'',true,true);
    $this->AddCampo('cod_autorizacao','integer',true,'',true,true);
    $this->AddCampo('cod_entidade','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'04',true,true);

}

function recuperaRelacionamentoAutorizacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoAutorizacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaRelacionamentoAutorizacao()
{
    $stSql  = "SELECT                                                                 \n";
    $stSql .= "    tabela.*,                                                          \n";
    $stSql .= "    D.cod_recurso,                                                     \n";
    $stSql .= "    CD.cod_estrutural  AS cod_estrutural_conta                         \n";
    $stSql .= "FROM (                                                                 \n";
    $stSql .= "    SELECT                                                             \n";
    $stSql .= "            AE.cod_autorizacao,                                        \n";
    $stSql .= "            EA.cod_empenho,                                            \n";
    $stSql .= "            TO_CHAR(AE.dt_autorizacao,'dd/mm/yyyy') AS dt_autorizacao, \n";
    $stSql .= "            PD.cod_despesa,                                            \n";
    $stSql .= "            D.cod_conta,                                               \n";
    $stSql .= "            CD.cod_estrutural AS cod_estrutural_rubrica,               \n";
    $stSql .= "            PE.descricao,                                              \n";
    $stSql .= "            PE.exercicio,                                              \n";
    $stSql .= "            PE.cod_pre_empenho,                                        \n";
    $stSql .= "            PE.cgm_beneficiario as credor,                             \n";
    $stSql .= "            PE.cod_historico,                                          \n";
    $stSql .= "            AE.cod_entidade,                                           \n";
    $stSql .= "            AE.num_orgao,                                              \n";
    $stSql .= "            AE.num_unidade,                                            \n";
    $stSql .= "            AE.cod_categoria,                                          \n";
    $stSql .= "            AR.cod_reserva,                                            \n";
    $stSql .= "             C.nom_cgm as nom_fornecedor,                              \n";
    $stSql .= "             CASE WHEN AA.cod_autorizacao > 0 THEN                     \n";
    $stSql .= "                 'Anulada'                                             \n";
    $stSql .= "             ELSE                                                      \n";
    $stSql .= "                 CASE WHEN EA.cod_autorizacao > 0 THEN                 \n";
    $stSql .= "                     'Empenhada'                                       \n";
    $stSql .= "                 ELSE                                                  \n";
    $stSql .= "                     'Não Empenhada'                                   \n";
    $stSql .= "                 END                                                   \n";
    $stSql .= "             END as situacao,                                          \n";
    $stSql .= "        CASE WHEN O.anulada IS NOT NULL                                \n";
    $stSql .= "        THEN O.anulada                                                 \n";
    $stSql .= "        ELSE 'f'                                                       \n";
    $stSql .= "        END AS anulada,                                                \n";
    $stSql .= "        sum(IPE.vl_total) as vl_empenhado                              \n";
    $stSql .= "    FROM                                                               \n";
    $stSql .= "            empenho.autorizacao_empenho AS AE                      \n";
    $stSql .= "     LEFT JOIN                                                         \n";
    $stSql .= "            empenho.autorizacao_reserva AS AR                      \n";
    $stSql .= "            ON (                                                       \n";
    $stSql .= "                 AR.exercicio       = AE.exercicio       AND           \n";
    $stSql .= "                 AR.cod_entidade    = AE.cod_entidade    AND           \n";
    $stSql .= "                 AR.cod_autorizacao = AE.cod_autorizacao               \n";
    $stSql .= "            )                                                          \n";
    $stSql .= "     LEFT JOIN                                                         \n";
    $stSql .= "          empenho.autorizacao_anulada AS AA                        \n";
    $stSql .= "            ON (                                                       \n";
    $stSql .= "                AA.cod_autorizacao = AE.cod_autorizacao AND            \n";
    $stSql .= "                AA.exercicio       = AE.exercicio       AND            \n";
    $stSql .= "                AA.cod_entidade    = AE.cod_entidade       )           \n";
    $stSql .= "     LEFT JOIN                                                         \n";
    $stSql .= "            orcamento.reserva           AS  O                      \n";
    $stSql .= "            ON (                                                       \n";
    $stSql .= "                 O.exercicio   = AR.exercicio   AND                    \n";
    $stSql .= "                 O.cod_reserva = AR.cod_reserva                        \n";
    $stSql .= "            )                                                         \n";
    $stSql .= "     LEFT JOIN                                                         \n";
    $stSql .= "            empenho.empenho_autorizacao AS EA                      \n";
    $stSql .= "            ON (                                                       \n";
    $stSql .= "                 EA.exercicio       = AE.exercicio       AND           \n";
    $stSql .= "                 EA.cod_entidade    = AE.cod_entidade    AND           \n";
    $stSql .= "                 EA.cod_autorizacao = AE.cod_autorizacao               \n";
    $stSql .= "            ),                                                          \n";
    $stSql .= "            sw_cgm                         AS  C,                     \n";
    $stSql .= "            empenho.pre_empenho         AS PE                      \n";
    $stSql .= "     LEFT JOIN                                                         \n";
    $stSql .= "            empenho.pre_empenho_despesa AS PD                      \n";
    $stSql .= "             ON (                                                      \n";
    $stSql .= "                 PD.cod_pre_empenho = PE.cod_pre_empenho AND           \n";
    $stSql .= "                 PD.exercicio       = PE.exercicio                     \n";
    $stSql .= "                )                                                      \n";
    $stSql .= "     LEFT JOIN                                                         \n";
    $stSql .= "            empenho.item_pre_empenho AS IPE                        \n";
    $stSql .= "             ON (                                                      \n";
    $stSql .= "                 IPE.cod_pre_empenho = PE.cod_pre_empenho AND           \n";
    $stSql .= "                 IPE.exercicio       = PE.exercicio                     \n";
    $stSql .= "                )                                                      \n";
    $stSql .= "     LEFT JOIN                                                         \n";
    $stSql .= "            orcamento.conta_despesa     AS CD                      \n";
    $stSql .= "             ON (                                                      \n";
    $stSql .= "                 CD.exercicio = PD.exercicio  AND                      \n";
    $stSql .= "                 CD.cod_conta = PD.cod_conta                           \n";
    $stSql .= "             )                                                         \n";
    $stSql .= "     LEFT JOIN                                                         \n";
    $stSql .= "            orcamento.despesa         AS D                         \n";
    $stSql .= "            ON (                                                       \n";
    $stSql .= "               D.exercicio   = PD.exercicio   AND                      \n";
    $stSql .= "               D.cod_despesa = PD.cod_despesa                          \n";
    $stSql .= "            )                                                          \n";
    $stSql .= "    WHERE                                                              \n";
    $stSql .= "            AE.cod_pre_empenho = PE.cod_pre_empenho   AND              \n";
    $stSql .= "            AE.exercicio       = PE.exercicio         AND              \n";
    $stSql .= "             C.numcgm          = PE.cgm_beneficiario                   \n";
    $stSql .= "     GROUP BY                                                          \n";
    $stSql .= "             AE.cod_autorizacao,                                       \n";
    $stSql .= "             EA.cod_empenho,                                            \n";
    $stSql .= "             TO_CHAR(AE.dt_autorizacao,'dd/mm/yyyy'),                  \n";
    $stSql .= "             PD.cod_despesa,                                           \n";
    $stSql .= "             D.cod_conta,                                              \n";
    $stSql .= "             CD.cod_estrutural,                                        \n";
    $stSql .= "             PE.descricao,                                             \n";
    $stSql .= "             PE.exercicio,                                             \n";
    $stSql .= "             PE.cod_pre_empenho,                                       \n";
    $stSql .= "             PE.cgm_beneficiario,                                      \n";
    $stSql .= "             PE.cod_historico,                                         \n";
    $stSql .= "             AE.cod_entidade,                                          \n";
    $stSql .= "             AE.num_orgao,                                             \n";
    $stSql .= "             AE.num_unidade,                                           \n";
    $stSql .= "             AE.cod_categoria,                                         \n";
    $stSql .= "             AR.cod_reserva,                                           \n";
    $stSql .= "             C.nom_cgm,                                                \n";
    $stSql .= "             situacao,                                                 \n";
    $stSql .= "             anulada                                                   \n";
    $stSql .= ") AS tabela                                                            \n";
    $stSql .= "LEFT JOIN                                                              \n";
    $stSql .= "    orcamento.conta_despesa AS CD                                  \n";
    $stSql .= "    ON(                                                                \n";
    $stSql .= "        CD.exercicio = tabela.exercicio  AND                           \n";
    $stSql .= "        CD.cod_conta = tabela.cod_conta                                \n";
    $stSql .= "    )                                                                  \n";
    $stSql .= "LEFT JOIN                                                              \n";
    $stSql .= "    orcamento.despesa AS D                                         \n";
    $stSql .= "    ON (                                                               \n";
    $stSql .= "        D.cod_despesa = tabela.cod_despesa AND                         \n";
    $stSql .= "        D.exercicio   = tabela.exercicio                               \n";
    $stSql .= "    )                                                                  \n";
    $stSql .= "WHERE                                                                  \n";
    $stSql .= "       tabela.num_orgao::varchar||tabela.num_unidade::varchar                            \n";
    $stSql .= "       IN (                                                            \n";
    $stSql .= "            SELECT                                                     \n";
    $stSql .= "                  num_orgao::varchar||num_unidade::varchar                               \n";
    $stSql .= "            FROM                                                       \n";
    $stSql .= "                 empenho.permissao_autorizacao                     \n";
    $stSql .= "            WHERE                                                      \n";
    $stSql .= "                 numcgm    = ".$this->getDado("numcgm")."     AND      \n";
    $stSql .= "                 exercicio =  '".$this->getDado("exercicio")."'        \n";
    $stSql .= "       )                                                               \n";

//    echo $stSql;
    return $stSql;
}

}
