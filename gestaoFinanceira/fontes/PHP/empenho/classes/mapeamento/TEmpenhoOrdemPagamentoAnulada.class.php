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
    * Classe de mapeamento da tabela EMPENHO.ORDEM_PAGAMENTO_ANULADA
    * Data de Criação: 19/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: eduardo $
    $Date: 2006-09-28 06:56:56 -0300 (Qui, 28 Set 2006) $

    * Casos de uso: uc-02.03.16,uc-02.03.05,uc-02.04.05
*/

/*
$Log$
Revision 1.9  2006/09/28 09:52:53  eduardo
Bug #7060#

Revision 1.8  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  EMPENHO.ORDEM_PAGAMENTO_ANULADA
  * Data de Criação: 19/12/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Fábio Bertoldi Rodrigues

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoOrdemPagamentoAnulada extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoOrdemPagamentoAnulada()
{
    parent::Persistente();
    $this->setTabela('empenho.ordem_pagamento_anulada');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_ordem,exercicio,cod_entidade,timestamp');

    $this->AddCampo('cod_ordem'   ,  'integer', true, '',  true,  true);
    $this->AddCampo('exercicio'   ,  'varchar', true,'4',  true,  true);
    $this->AddCampo('cod_entidade',  'integer', true, '',  true,  true);
    $this->AddCampo('timestamp'   ,'timestamp', true, '',  true,  true);
    $this->AddCampo('motivo'      ,     'text', true, '', false, false);

}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaRelacionamento()
{
    $stSql  = "                                                                 ";
    $stSql .= "SELECT                                                           \n";
    $stSql .= "    EOP.COD_ORDEM,                                               \n";
    $stSql .= "    EOP.EXERCICIO,                                               \n";
    $stSql .= "    EOP.COD_ENTIDADE,                                            \n";
    $stSql .= "    EPL.EXERCICIO_LIQUIDACAO,                                    \n";
    $stSql .= "    EPL.COD_NOTA,                                                \n";
    $stSql .= "    TO_CHAR ( ENL.DT_LIQUIDACAO ,'dd/mm/yyyy') AS DT_LIQUIDACAO, \n";
    $stSql .= "    publico.fn_numeric_br( EPL.VL_PAGAMENTO ) AS VL_PAGAMENTO,   \n";
    $stSql .= "    CGME.NOM_CGM AS ENTIDADE,                                    \n";
    $stSql .= "    ENL.COD_EMPENHO,                                             \n";
    $stSql .= "    ENL.EXERCICIO AS EXERCICIO_NOTA,                             \n";
    $stSql .= "    ENL.EXERCICIO_EMPENHO,                                       \n";
    $stSql .= "    TO_CHAR ( EE.DT_EMPENHO ,'dd/mm/yyyy') AS DT_EMPENHO,        \n";
    $stSql .= "    EPE.CGM_BENEFICIARIO,                                        \n";
    $stSql .= "    CGM.NOM_CGM AS BENEFICIARIO                                  \n";
    $stSql .= "FROM                                                             \n";
    $stSql .= "  empenho.ordem_pagamento AS EOP                             \n";
    $stSql .= "LEFT JOIN                                                        \n";
    $stSql .= "  empenho.pagamento_liquidacao AS EPL                        \n";
    $stSql .= "ON                                                               \n";
    $stSql .= "    EPL.COD_ORDEM    = EOP.COD_ORDEM AND                         \n";
    $stSql .= "    EPL.EXERCICIO    = EOP.EXERCICIO AND                         \n";
    $stSql .= "    EPL.COD_ENTIDADE = EOP.COD_ENTIDADE                          \n";
    $stSql .= "LEFT JOIN                                                        \n";
    $stSql .= "  empenho.nota_liquidacao AS ENL                             \n";
    $stSql .= "ON                                                               \n";
    $stSql .= "    ENL.EXERCICIO    = EPL.EXERCICIO_LIQUIDACAO AND              \n";
    $stSql .= "    ENL.COD_ENTIDADE = EOP.COD_ENTIDADE AND                      \n";
    $stSql .= "    ENL.COD_NOTA     = EPL.COD_NOTA                              \n";
    $stSql .= "LEFT JOIN                                                        \n";
    $stSql .= "  empenho.empenho AS EE                                      \n";
    $stSql .= "ON                                                               \n";
    $stSql .= "    EE.COD_EMPENHO  = ENL.COD_EMPENHO AND                        \n";
    $stSql .= "    EE.EXERCICIO    = ENL.EXERCICIO AND                          \n";
    $stSql .= "    EE.COD_ENTIDADE = ENL.COD_ENTIDADE                           \n";
    $stSql .= "LEFT JOIN                                                        \n";
    $stSql .= "  empenho.pre_empenho AS EPE                                 \n";
    $stSql .= "ON                                                               \n";
    $stSql .= "    EPE.EXERCICIO       = EE.EXERCICIO AND                       \n";
    $stSql .= "    EPE.COD_PRE_EMPENHO = EE.COD_PRE_EMPENHO                     \n";
    $stSql .= "LEFT JOIN                                                        \n";
    $stSql .= "  sw_cgm AS CGM                                              \n";
    $stSql .= "ON                                                               \n";
    $stSql .= "    CGM.NUMCGM = EPE.CGM_BENEFICIARIO                            \n";
    $stSql .= "LEFT JOIN                                                        \n";
    $stSql .= "  orcamento.entidade AS OE                                   \n";
    $stSql .= "ON                                                               \n";
    $stSql .= "    OE.COD_ENTIDADE = EOP.COD_ENTIDADE                           \n";
    $stSql .= "LEFT JOIN                                                        \n";
    $stSql .= "  sw_cgm AS CGME                                             \n";
    $stSql .= "ON                                                               \n";
    $stSql .= "    CGME.NUMCGM = OE.NUMCGM                                      \n";

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
    $stSql  = " SELECT                                                                 \n";
    $stSql .= "    opa.exercicio,                                                      \n";
    $stSql .= "    opa.cod_ordem,                                                      \n";
    $stSql .= "    sum(opa.vl_anulado) as vl_anulado,                                  \n";
    $stSql .= "    to_char(opa.timestamp,'dd/mm/yyyy') as dt_anulacao,                 \n";
    $stSql .= "    replace(replace(opa.timestamp,' ',';'),'.','@') as timestamp_alterado     \n";
    $stSql .= " FROM                                                                   \n";
    $stSql .= "    empenho.empenho                 e,                              \n";
    $stSql .= "    empenho.nota_liquidacao         nl,                             \n";
    $stSql .= "    empenho.pagamento_liquidacao    pl,                             \n";
    $stSql .= "    empenho.ordem_pagamento         op,                             \n";
    $stSql .= "    empenho.ordem_pagamento_anulada opa                             \n";
    $stSql .= " WHERE                                                                  \n";
    $stSql .= "    e.cod_empenho       = nl.cod_empenho            AND                 \n";
    $stSql .= "    e.cod_entidade      = nl.cod_entidade           AND                 \n";
    $stSql .= "    e.exercicio         = nl.exercicio_empenho      AND                 \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    nl.exercicio        = pl.exercicio_liquidacao   AND                 \n";
    $stSql .= "    nl.cod_nota         = pl.cod_nota               AND                 \n";
    $stSql .= "    nl.cod_entidade     = pl.cod_entidade           AND                 \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    pl.exercicio        = op.exercicio              AND                 \n";
    $stSql .= "    pl.cod_ordem        = op.cod_ordem              AND                 \n";
    $stSql .= "    pl.cod_entidade     = op.cod_entidade           AND                 \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    op.exercicio        = opa.exercicio             AND                 \n";
    $stSql .= "    op.cod_ordem        = opa.cod_ordem             AND                 \n";
    $stSql .= "    op.cod_entidade     = opa.cod_entidade          AND                 \n";
    $stSql .= "                                                                        \n";
    $stSql .= "    e.cod_empenho       = '".$this->getDado('cod_empenho')."'    AND    \n";
    $stSql .= "    e.cod_entidade      = '".$this->getDado('cod_entidade')."'   AND    \n";
    $stSql .= "    e.exercicio         = '".$this->getDado('exercicio')."'             \n";
    $stSql .= " GROUP BY                                                               \n";
    $stSql .= "    opa.exercicio,                                                      \n";
    $stSql .= "    opa.cod_ordem,                                                      \n";
    $stSql .= "    to_char(opa.timestamp,'dd/mm/yyyy'),                                \n";
    $stSql .= "    opa.timestamp                                                       \n";

    return $stSql;
}
}
