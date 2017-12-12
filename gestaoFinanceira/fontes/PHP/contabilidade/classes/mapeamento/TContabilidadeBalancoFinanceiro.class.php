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
    * Classe de mapeamento para relatorio Anexo 13
    * Data de Criação: 10/08/2005

    * @atuhor Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2008-04-15 11:30:35 -0300 (Ter, 15 Abr 2008) $

    * Casos de uso: uc-02.02.10
*/

/*
$Log$
Revision 1.21  2007/10/16 13:51:15  cako
Ticket#10366#

Revision 1.20  2007/10/15 17:59:12  cako
Ticket#10366#

Revision 1.19  2007/10/15 17:35:48  cako
Ticket#10366#

Revision 1.18  2007/10/11 20:00:10  cako
Ticket#10366#

Revision 1.17  2007/04/19 15:54:16  luciano
#9134#

Revision 1.16  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Classe de mapeamento para relatorio Anexo 13
  * Data de Criação: 10/08/2005

  * @atuhor Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TContabilidadeBalancoFinanceiro extends Persistente
{
/**
    * @método Construtor
    * @access Private
*/
function TContabilidadeBalancoFinanceiro()
{
    parent::Persistente();
    $this->setTabela('');

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo('cod_conta','integer',true,'',true,false);
    $this->AddCampo('exercicio','char',true,'04',true,true);
    $this->AddCampo('nom_conta','varchar',true,'160',false,false);
    $this->AddCampo('cod_classificacao','integer',true,'',false,true);
    $this->AddCampo('cod_sistema','integer',true,'',false,true);
    $this->AddCampo('cod_estrutural','varchar',false,'150',false,false);

}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaTodos.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaTodos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = " ORDER BY cod_grupo";
    $stSql = $this->montaRecuperaTodos();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTodos()
{
    $stSql  = "SELECT                                                                                                       \n";
    $stSql .= "    tbl.cod_estrutural,                                                                                      \n";
    $stSql .= "    publico.fn_nivel(tbl.cod_estrutural) as nivel,                                                           \n";
    $stSql .= "    sum( coalesce(tbl.vl_arrecadado_debito,0.00) ) + sum( coalesce(tbl.vl_arrecadado_credito,0.00) )  as vl_arrecadado,                    \n";
    $stSql .= "    sum(coalesce(tbl.vl_arrecadado_credito,0.00)) as vl_arrecadado_credito,                    \n";
    $stSql .= "    sum(coalesce(tbl.vl_arrecadado_debito,0.00)) as vl_arrecadado_debito,                    \n";
    $stSql .= "    OCR.nom_conta,                                                                                            \n";
    $stSql .= "    tbl.nom_sistema_debito,                                                                                  \n";
    $stSql .= "    tbl.nom_sistema_credito                                                                                  \n";
    $stSql .= "FROM(                                                                                                        \n";
    $stSql .= "    SELECT                                                                                                   \n";
    $stSql .= "        substr( OPC.cod_estrutural, 1,15 ) AS cod_estrutural,                                                \n";
    $stSql .= "        OPC.exercicio,                                                                                       \n";
    $stSql .= "        sum( coalesce( CCD.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_debito,                         \n";
    $stSql .= "        sum( coalesce( CCC.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_credito,                        \n";
    $stSql .= "        CCD.nom_sistema as nom_sistema_debito,                                                               \n";
    $stSql .= "        CCC.nom_sistema as nom_sistema_credito                                                               \n";
    $stSql .= "    FROM                                                                                                     \n";
    $stSql .= "        contabilidade.plano_conta      AS OPC                                                            \n";
    $stSql .= "            LEFT JOIN contabilidade.plano_analitica AS OCA ON (                                          \n";
    $stSql .= "                OPC.cod_conta = OCA.cod_conta AND                                                            \n";
    $stSql .= "                OPC.exercicio = OCA.exercicio                                                                \n";
    $stSql .= "            )                                                                                                \n";
    $stSql .= "            LEFT JOIN (                                                                                      \n";
    $stSql .= "                SELECT                                                                                       \n";
    $stSql .= "                    CCD.cod_plano,                                                                           \n";
    $stSql .= "                    CCD.exercicio,                                                                           \n";
    $stSql .= "                    sum( vl_lancamento ) as vl_lancamento,                                                   \n";
    $stSql .= "                    CSC.nom_sistema                                                                          \n";
    $stSql .= "                FROM                                                                                         \n";
    $stSql .= "                    contabilidade.plano_conta      AS CPC,                                                   \n";
    $stSql .= "                    contabilidade.plano_analitica  AS CPA,                                                   \n";
    $stSql .= "                    contabilidade.conta_debito     AS CCD,                                                   \n";
    $stSql .= "                    contabilidade.valor_lancamento AS CVLD,                                                  \n";
    $stSql .= "                    contabilidade.lancamento       AS CLA,                                                   \n";
    $stSql .= "                    contabilidade.lote             AS CLO,                                                   \n";
    $stSql .= "                    contabilidade.sistema_contabil AS CSC                                                    \n";
    $stSql .= "                WHERE                                                                                        \n";
    $stSql .= "                        CPC.cod_conta      = CPA.cod_conta                                                   \n";
    $stSql .= "                    AND CPC.exercicio      = CPA.exercicio                                                   \n";
    $stSql .= "                    AND CPC.cod_sistema    != 2                                                            \n";

    $stSql .= "                    AND CPA.cod_plano      = CCD.cod_plano                                                  \n";
    $stSql .= "                    AND CPA.exercicio      = CCD.exercicio                                                   \n";

    $stSql .= "                    AND CCD.cod_lote       = CVLD.cod_lote                                                   \n";
    $stSql .= "                    AND CCD.tipo           = CVLD.tipo                                                       \n";
    $stSql .= "                    AND CCD.sequencia      = CVLD.sequencia                                                  \n";
    $stSql .= "                    AND CCD.exercicio      = CVLD.exercicio                                                  \n";
    $stSql .= "                    AND CCD.tipo_valor     = CVLD.tipo_valor                                                 \n";
    $stSql .= "                    AND CCD.cod_entidade   = CVLD.cod_entidade                                               \n";
    $stSql .= "                    AND CVLD.tipo_valor    = 'D'                                                             \n";

    $stSql .= "                    AND CVLD.cod_lote      = CLA.cod_lote                                                 \n";
    $stSql .= "                    AND CVLD.tipo          = CLA.tipo                                                     \n";
    $stSql .= "                    AND CVLD.cod_entidade  = CLA.cod_entidade                                             \n";
    $stSql .= "                    AND CVLD.exercicio     = CLA.exercicio                                                \n";
    $stSql .= "                    AND CVLD.sequencia     = CLA.sequencia                                                \n";

    $stSql .= "                    AND CLA.cod_lote      = CLO.cod_lote                                                \n";
    $stSql .= "                    AND CLA.tipo          = CLO.tipo                                                    \n";
    $stSql .= "                    AND CLA.cod_entidade  = CLO.cod_entidade                                            \n";
    $stSql .= "                    AND CLA.exercicio     = CLO.exercicio                                               \n";
    $stSql .= "                    AND CSC.exercicio     = CPC.exercicio                                                    \n";
    $stSql .= "                    AND CSC.cod_sistema   = CPC.cod_sistema                                                  \n";

    $stSql .= "                    AND CCD.exercicio      = '".$this->getDado('exercicio')."'                               \n";
    $stSql .= "                    AND CVLD.cod_entidade  IN( ".$this->getDado('cod_entidade')." )                          \n";
    $stSql .= "                    AND CLO.dt_lote BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )      \n";
    $stSql .= "                                        AND TO_DATE( '".$this->getDado('dt_final')  ."', 'dd/mm/yyyy' )      \n";
    $stSql .= "                    AND CLO.tipo != 'I'                                                                      \n";

    $stSql .= "                    AND CLA.cod_historico not between 800 and 899                                          \n";
    $stSql .= "                GROUP BY                                                                                     \n";
    $stSql .= "                    CCD.cod_plano,                                                                           \n";
    $stSql .= "                    CCD.exercicio,                                                                            \n";
    $stSql .= "                    CSC.nom_sistema                                                                          \n";
    $stSql .= "                ORDER BY                                                                                     \n";
    $stSql .= "                    CCD.cod_plano,                                                                           \n";
    $stSql .= "                    CCD.exercicio                                                                            \n";
    $stSql .= "            ) AS CCD ON (                                                                                    \n";
    $stSql .= "                OCA.cod_plano = CCD.cod_plano AND                                                            \n";
    $stSql .= "                OCA.exercicio = CCD.exercicio                                                                \n";
    $stSql .= "            )                                                                                                \n";
    $stSql .= "            LEFT JOIN (                                                                                      \n";
    $stSql .= "                SELECT                                                                                       \n";
    $stSql .= "                    CCC.cod_plano,                                                                           \n";
    $stSql .= "                    CCC.exercicio,                                                                           \n";
    $stSql .= "                    sum(vl_lancamento) as vl_lancamento,                                                     \n";
    $stSql .= "                    CSC.nom_sistema                                                                         \n";
    $stSql .= "                FROM                                                                                         \n";
    $stSql .= "                    contabilidade.plano_conta      AS CPC,                                               \n";
    $stSql .= "                    contabilidade.plano_analitica  AS CPA,                                               \n";
    $stSql .= "                    contabilidade.conta_credito    AS CCC,                                               \n";
    $stSql .= "                    contabilidade.valor_lancamento AS CVLC,                                              \n";
    $stSql .= "                    contabilidade.lancamento       AS CLA,                                                \n";
    $stSql .= "                    contabilidade.lote             AS CLO,                                                \n";
    $stSql .= "                    contabilidade.sistema_contabil AS CSC                                                \n";
    $stSql .= "                WHERE                                                                                        \n";
    $stSql .= "                        CPC.cod_conta      = CPA.cod_conta                                                   \n";
    $stSql .= "                    AND CPC.exercicio      = CPA.exercicio                                                   \n";
    $stSql .= "                    AND CPC.cod_sistema    != 2                                                             \n";

    $stSql .= "                    AND CPA.cod_plano      = CCC.cod_plano                                                  \n";
    $stSql .= "                    AND CPA.exercicio      = CCC.exercicio                                                   \n";

    $stSql .= "                    AND CCC.cod_lote       = CVLC.cod_lote                                                   \n";
    $stSql .= "                    AND CCC.tipo           = CVLC.tipo                                                       \n";
    $stSql .= "                    AND CCC.sequencia      = CVLC.sequencia                                                  \n";
    $stSql .= "                    AND CCC.exercicio      = CVLC.exercicio                                                  \n";
    $stSql .= "                    AND CCC.tipo_valor     = CVLC.tipo_valor                                                 \n";
    $stSql .= "                    AND CCC.cod_entidade   = CVLC.cod_entidade                                               \n";
    $stSql .= "                    AND CVLC.tipo_valor    = 'C'                                                             \n";

    $stSql .= "                    AND CVLC.cod_lote      = CLA.cod_lote                                                 \n";
    $stSql .= "                    AND CVLC.tipo          = CLA.tipo                                                     \n";
    $stSql .= "                    AND CVLC.cod_entidade  = CLA.cod_entidade                                             \n";
    $stSql .= "                    AND CVLC.exercicio     = CLA.exercicio                                                \n";
    $stSql .= "                    AND CVLC.sequencia     = CLA.sequencia                                                \n";

    $stSql .= "                    AND CLA.cod_lote      = CLO.cod_lote                                                \n";
    $stSql .= "                    AND CLA.tipo          = CLO.tipo                                                    \n";
    $stSql .= "                    AND CLA.cod_entidade  = CLO.cod_entidade                                            \n";
    $stSql .= "                    AND CLA.exercicio     = CLO.exercicio                                               \n";
    $stSql .= "                    AND CSC.exercicio     = CPC.exercicio                                               \n";
    $stSql .= "                    AND CSC.cod_sistema   = CPC.cod_sistema                                             \n";

    $stSql .= "                    AND CCC.exercicio      = '".$this->getDado('exercicio')."'                               \n";
    $stSql .= "                    AND CVLC.cod_entidade  IN( ".$this->getDado('cod_entidade')." )                          \n";
    $stSql .= "                    AND CLO.dt_lote BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )      \n";
    $stSql .= "                                        AND TO_DATE( '".$this->getDado('dt_final')  ."', 'dd/mm/yyyy' )      \n";
    $stSql .= "                    AND CLO.tipo != 'I'                                                                      \n";
    $stSql .= "                    AND CLA.cod_historico not between 800 and 899                                          \n";
    $stSql .= "                GROUP BY                                                                                     \n";
    $stSql .= "                    CCC.cod_plano,                                                                           \n";
    $stSql .= "                    CCC.exercicio,                                                                           \n";
    $stSql .= "                    CSC.nom_sistema                                                                          \n";
    $stSql .= "                ORDER BY                                                                                     \n";
    $stSql .= "                    CCC.cod_plano,                                                                           \n";
    $stSql .= "                    CCC.exercicio                                                                            \n";
    $stSql .= "            ) AS CCC ON (                                                                                    \n";
    $stSql .= "                OCA.cod_plano = CCC.cod_plano AND                                                            \n";
    $stSql .= "                OCA.exercicio = CCC.exercicio                                                                \n";
    $stSql .= "            )                                                                                                \n";
    $stSql .= "        WHERE                                                                                                \n";
    $stSql .= "                OPC.exercicio = '".$this->getDado('exercicio')."' --AND                                      \n";
    $stSql .= "           AND (OPC.cod_estrutural    like  '1.1.1.1.1%'                                                     \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '1.1.1.1.2%'                                                     \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '1.1.1.1.3%'                                                     \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '1.1.2%'                                                         \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '1.1.5%'                                                         \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '2.1.1%'                                                         \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '2.1.2.1.9%'                                                     \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '2.2.1%'                                                         \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '2.9.2.4.1.04.01%'                                               \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '2.9.2.4.1.04.02%'                                               \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '2.9.5.2%'                                                       \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '3.3%'                                                           \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '3.4%'                                                           \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '3.9%'                                                           \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '4.1%'                                                           \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '4.2%'                                                           \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '4.7%'                                                           \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '4.8%'                                                           \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '5.2.1.9%'                                                       \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '5.2.2.2%'                                                       \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '5.1.2.1%'                                                       \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '4.9%'                                                           \n";
    if ($this->getDado('exercicio') == '2008') {
        $stSql .= "            OR  OPC.cod_estrutural    like  '9%'                                                        \n";
    }
    $stSql .= "            OR  OPC.cod_estrutural    like  '6.2.1.9%'                                                       \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '6.1.2.1%'                                                       \n";
    $stSql .= "            OR  OPC.cod_estrutural    like  '6.2.2.2%')                                                      \n";
    $stSql .= "      GROUP BY                                                                                               \n";
    $stSql .= "            OPC.cod_estrutural,                                                                              \n";
    $stSql .= "            OPC.exercicio,                                                                                    \n";
    $stSql .= "            CCD.nom_sistema,                                                                                 \n";
    $stSql .= "            CCC.nom_sistema                                                                                  \n";
    $stSql .= "      ORDER BY                                                                                               \n";
    $stSql .= "            OPC.cod_estrutural,                                                                              \n";
    $stSql .= "            OPC.exercicio                                                                                   \n";
    $stSql .= "    ) AS tbl,                                                                                                \n";
    $stSql .= "    contabilidade.plano_conta AS OCR                                                                     \n";
    $stSql .= "WHERE                                                                                                        \n";
    $stSql .= "    tbl.cod_estrutural = substr( OCR.cod_estrutural, 1, 15 ) AND                                             \n";
    $stSql .= "    (length( publico.fn_mascarareduzida( OCR.cod_estrutural ) ) <= 15 OR                                     \n";
    $stSql .= "             (  OCR.cod_estrutural = '4.9.7.2.1.01.05.04.00.00'                                              \n";
    $stSql .= "             OR OCR.cod_estrutural = '4.9.7.2.2.01.02.04.00.00'                                              \n";
    $stSql .= "             )) AND                                                                                          \n";
    $stSql .= "    tbl.exercicio      = OCR.exercicio                                                                       \n";
    $stSql .= "GROUP BY                                                                                                     \n";
    $stSql .= "    tbl.cod_estrutural,                                                                                      \n";
    $stSql .= "    OCR.nom_conta,                                                                                           \n";
    $stSql .= "    tbl.nom_sistema_debito,                                                                                  \n";
    $stSql .= "    tbl.nom_sistema_credito                                                                                  \n";
    $stSql .= "ORDER BY                                                                                                     \n";
    $stSql .= "    tbl.cod_estrutural,                                                                                      \n";
    $stSql .= "    OCR.nom_conta;                                                                                           \n";

    return $stSql;
}

function recuperaSaldoVariacao(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaSaldoVariacao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSaldoVariacao()
{
    $stSql = "SELECT max(abs(saldo_receita)) as variacao_receita , max(abs(saldo_despesa)) as variacao_despesa from (       \n";
    $stSql .= "    SELECT sum( coalesce(tabela.vl_arrecadado, 0.00) ) as saldo_receita, 0.00 as saldo_despesa from (        \n";
    $stSql .= "    SELECT                                                                                                   \n";
    $stSql .= "        tbl.cod_estrutural,                                                                                  \n";
    $stSql .= "        sum( coalesce(tbl.vl_arrecadado_debito,0.00) ) + sum(coalesce(tbl.vl_arrecadado_credito,0.00)) as vl_arrecadado, \n";
    $stSql .= "        sum(coalesce(tbl.vl_arrecadado_credito,0.00)) as vl_arrecadado_credito,                              \n";
    $stSql .= "        sum(coalesce(tbl.vl_arrecadado_debito,0.00)) as vl_arrecadado_debito                                 \n";
    $stSql .= "    FROM(                                                                                                    \n";
    $stSql .= "        SELECT                                                                                               \n";
    $stSql .= "            substr( OPC.cod_estrutural, 1,15 ) AS cod_estrutural,                                            \n";
    $stSql .= "            OPC.exercicio,                                                                                   \n";
    $stSql .= "            sum( coalesce( CCD.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_debito,                     \n";
    $stSql .= "            sum( coalesce( CCC.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_credito                     \n";
    $stSql .= "        FROM                                                                                                 \n";
    $stSql .= "            contabilidade.plano_conta      AS OPC                                                            \n";
    $stSql .= "                LEFT JOIN contabilidade.plano_analitica AS OCA ON (                                          \n";
    $stSql .= "                    OPC.cod_conta = OCA.cod_conta AND                                                        \n";
    $stSql .= "                    OPC.exercicio = OCA.exercicio                                                            \n";
    $stSql .= "                )                                                                                            \n";
    $stSql .= "                LEFT JOIN (                                                                                  \n";
    $stSql .= "                    SELECT                                                                                   \n";
    $stSql .= "                        CCD.cod_plano,                                                                       \n";
    $stSql .= "                        CCD.exercicio,                                                                       \n";
    $stSql .= "                        sum( vl_lancamento ) as vl_lancamento                                                \n";
    $stSql .= "                    FROM                                                                                     \n";
    $stSql .= "                        contabilidade.plano_conta      AS CPC,                                               \n";
    $stSql .= "                        contabilidade.plano_analitica  AS CPA,                                               \n";
    $stSql .= "                        contabilidade.conta_debito     AS CCD,                                               \n";
    $stSql .= "                        contabilidade.valor_lancamento AS CVLD,                                              \n";
    $stSql .= "                        contabilidade.lancamento       AS CLA,                                               \n";
    $stSql .= "                        contabilidade.lote             AS CLO                                                \n";
    $stSql .= "                    WHERE                                                                                    \n";
    $stSql .= "                            CPC.cod_conta      = CPA.cod_conta                                               \n";
    $stSql .= "                        AND CPC.exercicio      = CPA.exercicio                                               \n";
    $stSql .= "                        AND CPC.cod_sistema    = 1                                                           \n";
    $stSql .= "                        AND CPA.cod_plano      = CCD.cod_plano                                               \n";
    $stSql .= "                        AND CPA.exercicio      = CCD.exercicio                                               \n";
    $stSql .= "                        AND CCD.cod_lote       = CVLD.cod_lote                                               \n";
    $stSql .= "                        AND CCD.tipo           = CVLD.tipo                                                   \n";
    $stSql .= "                        AND CCD.sequencia      = CVLD.sequencia                                              \n";
    $stSql .= "                        AND CCD.exercicio      = CVLD.exercicio                                              \n";
    $stSql .= "                        AND CCD.tipo_valor     = CVLD.tipo_valor                                             \n";
    $stSql .= "                        AND CCD.cod_entidade   = CVLD.cod_entidade                                           \n";
    $stSql .= "                        AND CVLD.tipo_valor    = 'D'                                                         \n";
    $stSql .= "                        AND CVLD.cod_lote      = CLA.cod_lote                                                \n";
    $stSql .= "                        AND CVLD.tipo          = CLA.tipo                                                    \n";
    $stSql .= "                        AND CVLD.cod_entidade  = CLA.cod_entidade                                            \n";
    $stSql .= "                        AND CVLD.exercicio     = CLA.exercicio                                               \n";
    $stSql .= "                        AND CVLD.sequencia     = CLA.sequencia                                               \n";
    $stSql .= "                        AND CLA.cod_lote      = CLO.cod_lote                                                 \n";
    $stSql .= "                        AND CLA.tipo          = CLO.tipo                                                     \n";
    $stSql .= "                        AND CLA.cod_entidade  = CLO.cod_entidade                                             \n";
    $stSql .= "                        AND CLA.exercicio     = CLO.exercicio                                                \n";
    $stSql .= "                        AND CCD.exercicio      = '".$this->getDado('exercicio')."'                           \n";
    $stSql .= "                        AND CVLD.cod_entidade  IN( ".$this->getDado('cod_entidade')." )                      \n";
    $stSql .= "                        AND CLO.dt_lote BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                                            AND TO_DATE( '".$this->getDado('dt_final')  ."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                        AND CLO.tipo != 'I'                                                                  \n";
    $stSql .= "                        AND CLA.cod_historico not between 800 and 899                                        \n";
    $stSql .= "                    GROUP BY                                                                                 \n";
    $stSql .= "                        CCD.cod_plano,                                                                       \n";
    $stSql .= "                        CCD.exercicio                                                                        \n";
    $stSql .= "                    ORDER BY                                                                                 \n";
    $stSql .= "                        CCD.cod_plano,                                                                       \n";
    $stSql .= "                        CCD.exercicio                                                                        \n";
    $stSql .= "                ) AS CCD ON (                                                                                \n";
    $stSql .= "                    OCA.cod_plano = CCD.cod_plano AND                                                        \n";
    $stSql .= "                    OCA.exercicio = CCD.exercicio                                                            \n";
    $stSql .= "                )                                                                                            \n";
    $stSql .= "                LEFT JOIN (                                                                                  \n";
    $stSql .= "                    SELECT                                                                                   \n";
    $stSql .= "                        CCC.cod_plano,                                                                       \n";
    $stSql .= "                        CCC.exercicio,                                                                       \n";
    $stSql .= "                        sum(vl_lancamento) as vl_lancamento                                                  \n";
    $stSql .= "                    FROM                                                                                     \n";
    $stSql .= "                        contabilidade.plano_conta      AS CPC,                                               \n";
    $stSql .= "                        contabilidade.plano_analitica  AS CPA,                                               \n";
    $stSql .= "                        contabilidade.conta_credito    AS CCC,                                               \n";
    $stSql .= "                        contabilidade.valor_lancamento AS CVLC,                                              \n";
    $stSql .= "                        contabilidade.lancamento       AS CLA,                                               \n";
    $stSql .= "                        contabilidade.lote             AS CLO                                                \n";
    $stSql .= "                    WHERE                                                                                    \n";
    $stSql .= "                            CPC.cod_conta      = CPA.cod_conta                                               \n";
    $stSql .= "                        AND CPC.exercicio      = CPA.exercicio                                               \n";
    $stSql .= "                        AND CPC.cod_sistema    = 1                                                           \n";
    $stSql .= "                        AND CPA.cod_plano      = CCC.cod_plano                                               \n";
    $stSql .= "                        AND CPA.exercicio      = CCC.exercicio                                               \n";
    $stSql .= "                        AND CCC.cod_lote       = CVLC.cod_lote                                               \n";
    $stSql .= "                        AND CCC.tipo           = CVLC.tipo                                                   \n";
    $stSql .= "                        AND CCC.sequencia      = CVLC.sequencia                                              \n";
    $stSql .= "                        AND CCC.exercicio      = CVLC.exercicio                                              \n";
    $stSql .= "                        AND CCC.tipo_valor     = CVLC.tipo_valor                                             \n";
    $stSql .= "                        AND CCC.cod_entidade   = CVLC.cod_entidade                                           \n";
    $stSql .= "                        AND CVLC.tipo_valor    = 'C'                                                         \n";
    $stSql .= "                        AND CVLC.cod_lote      = CLA.cod_lote                                                \n";
    $stSql .= "                        AND CVLC.tipo          = CLA.tipo                                                    \n";
    $stSql .= "                        AND CVLC.cod_entidade  = CLA.cod_entidade                                            \n";
    $stSql .= "                        AND CVLC.exercicio     = CLA.exercicio                                               \n";
    $stSql .= "                        AND CVLC.sequencia     = CLA.sequencia                                               \n";
    $stSql .= "                        AND CLA.cod_lote      = CLO.cod_lote                                                 \n";
    $stSql .= "                        AND CLA.tipo          = CLO.tipo                                                     \n";
    $stSql .= "                        AND CLA.cod_entidade  = CLO.cod_entidade                                             \n";
    $stSql .= "                        AND CLA.exercicio     = CLO.exercicio                                                \n";
    $stSql .= "                        AND CCC.exercicio      = '".$this->getDado('exercicio')."'                           \n";
    $stSql .= "                        AND CVLC.cod_entidade  IN( ".$this->getDado('cod_entidade')." )                      \n";
    $stSql .= "                        AND CLO.dt_lote BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                                            AND TO_DATE( '".$this->getDado('dt_final')  ."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                        AND CLO.tipo != 'I'                                                                  \n";
    $stSql .= "                        AND CLA.cod_historico not between 800 and 899                                        \n";
    $stSql .= "                    GROUP BY                                                                                 \n";
    $stSql .= "                        CCC.cod_plano,                                                                       \n";
    $stSql .= "                        CCC.exercicio                                                                        \n";
    $stSql .= "                    ORDER BY                                                                                 \n";
    $stSql .= "                        CCC.cod_plano,                                                                       \n";
    $stSql .= "                        CCC.exercicio                                                                        \n";
    $stSql .= "                ) AS CCC ON (                                                                                \n";
    $stSql .= "                    OCA.cod_plano = CCC.cod_plano AND                                                        \n";
    $stSql .= "                    OCA.exercicio = CCC.exercicio                                                            \n";
    $stSql .= "                )                                                                                            \n";
    $stSql .= "            WHERE                                                                                            \n";
    $stSql .= "                OPC.exercicio = '".$this->getDado('exercicio')."'                                            \n";
    $stSql .= "               AND  OPC.cod_estrutural    like  '6.%'                                                        \n";
    $stSql .= "               AND  OPC.cod_estrutural   not like  '6.1.1%'                                                  \n";
    $stSql .= "               AND  OPC.cod_estrutural   not like  '6.1.2%'                                                  \n";
    $stSql .= "               AND  OPC.cod_estrutural   not like  '6.2.3.3.1.05%'                                           \n";
    $stSql .= "          GROUP BY                                                                                           \n";
    $stSql .= "                OPC.cod_estrutural,                                                                          \n";
    $stSql .= "                OPC.exercicio                                                                                \n";
    $stSql .= "          ORDER BY                                                                                           \n";
    $stSql .= "                OPC.cod_estrutural,                                                                          \n";
    $stSql .= "                OPC.exercicio                                                                                \n";
    $stSql .= "        ) AS tbl                                                                                            \n";
    $stSql .= "    GROUP BY                                                                                                 \n";
    $stSql .= "        tbl.cod_estrutural                                                                                  \n";
    $stSql .= "    ORDER BY                                                                                                 \n";
    $stSql .= "        tbl.cod_estrutural                                                                                  \n";
    $stSql .= "    ) as tabela                                                                                              \n";
    $stSql .= "    where tabela.vl_arrecadado <> 0                                                                          \n";
    $stSql .= "                                                                                                             \n";
    $stSql .= "UNION ALL                                                                                                    \n";
    $stSql .= "                                                                                                             \n";
    $stSql .= "    SELECT 0.00 as saldo_receita, sum( coalesce(tabela.vl_arrecadado, 0.00) ) as saldo_despesa from (        \n";
    $stSql .= "    SELECT                                                                                                   \n";
    $stSql .= "        tbl.cod_estrutural,                                                                                  \n";
    $stSql .= "        sum( coalesce(tbl.vl_arrecadado_debito,0.00) ) + sum(coalesce(tbl.vl_arrecadado_credito,0.00)) as vl_arrecadado, \n";
    $stSql .= "        sum(coalesce(tbl.vl_arrecadado_credito,0.00)) as vl_arrecadado_credito,                              \n";
    $stSql .= "        sum(coalesce(tbl.vl_arrecadado_debito,0.00)) as vl_arrecadado_debito                                 \n";
    $stSql .= "    FROM(                                                                                                    \n";
    $stSql .= "        SELECT                                                                                               \n";
    $stSql .= "            substr( OPC.cod_estrutural, 1,15 ) AS cod_estrutural,                                            \n";
    $stSql .= "            OPC.exercicio,                                                                                   \n";
    $stSql .= "            sum( coalesce( CCD.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_debito,                     \n";
    $stSql .= "            sum( coalesce( CCC.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_credito                     \n";
    $stSql .= "        FROM                                                                                                 \n";
    $stSql .= "            contabilidade.plano_conta      AS OPC                                                            \n";
    $stSql .= "                LEFT JOIN contabilidade.plano_analitica AS OCA ON (                                          \n";
    $stSql .= "                    OPC.cod_conta = OCA.cod_conta AND                                                        \n";
    $stSql .= "                    OPC.exercicio = OCA.exercicio                                                            \n";
    $stSql .= "                )                                                                                            \n";
    $stSql .= "                LEFT JOIN (                                                                                  \n";
    $stSql .= "                    SELECT                                                                                   \n";
    $stSql .= "                        CCD.cod_plano,                                                                       \n";
    $stSql .= "                        CCD.exercicio,                                                                       \n";
    $stSql .= "                        sum( vl_lancamento ) as vl_lancamento                                                \n";
    $stSql .= "                    FROM                                                                                     \n";
    $stSql .= "                        contabilidade.plano_conta      AS CPC,                                               \n";
    $stSql .= "                        contabilidade.plano_analitica  AS CPA,                                               \n";
    $stSql .= "                        contabilidade.conta_debito     AS CCD,                                               \n";
    $stSql .= "                        contabilidade.valor_lancamento AS CVLD,                                              \n";
    $stSql .= "                        contabilidade.lancamento       AS CLA,                                               \n";
    $stSql .= "                        contabilidade.lote             AS CLO                                                \n";
    $stSql .= "                    WHERE                                                                                    \n";
    $stSql .= "                            CPC.cod_conta      = CPA.cod_conta                                               \n";
    $stSql .= "                        AND CPC.exercicio      = CPA.exercicio                                               \n";
    $stSql .= "                        AND CPC.cod_sistema    = 1                                                           \n";
    $stSql .= "                        AND CPA.cod_plano      = CCD.cod_plano                                               \n";
    $stSql .= "                        AND CPA.exercicio      = CCD.exercicio                                               \n";
    $stSql .= "                        AND CCD.cod_lote       = CVLD.cod_lote                                               \n";
    $stSql .= "                        AND CCD.tipo           = CVLD.tipo                                                   \n";
    $stSql .= "                        AND CCD.sequencia      = CVLD.sequencia                                              \n";
    $stSql .= "                        AND CCD.exercicio      = CVLD.exercicio                                              \n";
    $stSql .= "                        AND CCD.tipo_valor     = CVLD.tipo_valor                                             \n";
    $stSql .= "                        AND CCD.cod_entidade   = CVLD.cod_entidade                                           \n";
    $stSql .= "                        AND CVLD.tipo_valor    = 'D'                                                         \n";
    $stSql .= "                        AND CVLD.cod_lote      = CLA.cod_lote                                                \n";
    $stSql .= "                        AND CVLD.tipo          = CLA.tipo                                                    \n";
    $stSql .= "                        AND CVLD.cod_entidade  = CLA.cod_entidade                                            \n";
    $stSql .= "                        AND CVLD.exercicio     = CLA.exercicio                                               \n";
    $stSql .= "                        AND CVLD.sequencia     = CLA.sequencia                                               \n";
    $stSql .= "                        AND CLA.cod_lote      = CLO.cod_lote                                                 \n";
    $stSql .= "                        AND CLA.tipo          = CLO.tipo                                                     \n";
    $stSql .= "                        AND CLA.cod_entidade  = CLO.cod_entidade                                             \n";
    $stSql .= "                        AND CLA.exercicio     = CLO.exercicio                                                \n";
    $stSql .= "                        AND CCD.exercicio      = '".$this->getDado('exercicio')."'                           \n";
    $stSql .= "                        AND CVLD.cod_entidade  IN( ".$this->getDado('cod_entidade')." )                      \n";
    $stSql .= "                        AND CLO.dt_lote BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                                            AND TO_DATE( '".$this->getDado('dt_final')  ."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                        AND CLO.tipo != 'I'                                                                  \n";
    $stSql .= "                        AND CLA.cod_historico not between 800 and 899                                        \n";
    $stSql .= "                    GROUP BY                                                                                 \n";
    $stSql .= "                        CCD.cod_plano,                                                                       \n";
    $stSql .= "                        CCD.exercicio                                                                        \n";
    $stSql .= "                    ORDER BY                                                                                 \n";
    $stSql .= "                        CCD.cod_plano,                                                                       \n";
    $stSql .= "                        CCD.exercicio                                                                        \n";
    $stSql .= "                ) AS CCD ON (                                                                                \n";
    $stSql .= "                    OCA.cod_plano = CCD.cod_plano AND                                                        \n";
    $stSql .= "                    OCA.exercicio = CCD.exercicio                                                            \n";
    $stSql .= "                )                                                                                            \n";
    $stSql .= "                LEFT JOIN (                                                                                  \n";
    $stSql .= "                    SELECT                                                                                   \n";
    $stSql .= "                        CCC.cod_plano,                                                                       \n";
    $stSql .= "                        CCC.exercicio,                                                                       \n";
    $stSql .= "                        sum(vl_lancamento) as vl_lancamento                                                  \n";
    $stSql .= "                    FROM                                                                                     \n";
    $stSql .= "                        contabilidade.plano_conta      AS CPC,                                               \n";
    $stSql .= "                        contabilidade.plano_analitica  AS CPA,                                               \n";
    $stSql .= "                        contabilidade.conta_credito    AS CCC,                                               \n";
    $stSql .= "                        contabilidade.valor_lancamento AS CVLC,                                              \n";
    $stSql .= "                        contabilidade.lancamento       AS CLA,                                               \n";
    $stSql .= "                        contabilidade.lote             AS CLO--,                                             \n";
    $stSql .= "                    WHERE                                                                                    \n";
    $stSql .= "                            CPC.cod_conta      = CPA.cod_conta                                               \n";
    $stSql .= "                        AND CPC.exercicio      = CPA.exercicio                                               \n";
    $stSql .= "                        AND CPC.cod_sistema    = 1                                                           \n";
    $stSql .= "                        AND CPA.cod_plano      = CCC.cod_plano                                               \n";
    $stSql .= "                        AND CPA.exercicio      = CCC.exercicio                                               \n";
    $stSql .= "                        AND CCC.cod_lote       = CVLC.cod_lote                                               \n";
    $stSql .= "                        AND CCC.tipo           = CVLC.tipo                                                   \n";
    $stSql .= "                        AND CCC.sequencia      = CVLC.sequencia                                              \n";
    $stSql .= "                        AND CCC.exercicio      = CVLC.exercicio                                              \n";
    $stSql .= "                        AND CCC.tipo_valor     = CVLC.tipo_valor                                             \n";
    $stSql .= "                        AND CCC.cod_entidade   = CVLC.cod_entidade                                           \n";
    $stSql .= "                        AND CVLC.tipo_valor    = 'C'                                                         \n";
    $stSql .= "                        AND CVLC.cod_lote      = CLA.cod_lote                                                \n";
    $stSql .= "                        AND CVLC.tipo          = CLA.tipo                                                    \n";
    $stSql .= "                        AND CVLC.cod_entidade  = CLA.cod_entidade                                            \n";
    $stSql .= "                        AND CVLC.exercicio     = CLA.exercicio                                               \n";
    $stSql .= "                        AND CVLC.sequencia     = CLA.sequencia                                               \n";
    $stSql .= "                        AND CLA.cod_lote      = CLO.cod_lote                                                 \n";
    $stSql .= "                        AND CLA.tipo          = CLO.tipo                                                     \n";
    $stSql .= "                        AND CLA.cod_entidade  = CLO.cod_entidade                                             \n";
    $stSql .= "                        AND CLA.exercicio     = CLO.exercicio                                                \n";
    $stSql .= "                        AND CCC.exercicio      = '".$this->getDado('exercicio')."'                           \n";
    $stSql .= "                        AND CVLC.cod_entidade  IN( ".$this->getDado('cod_entidade')." )                      \n";
    $stSql .= "                        AND CLO.dt_lote BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                                            AND TO_DATE( '".$this->getDado('dt_final')  ."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                        AND CLO.tipo != 'I'                                                                  \n";
    $stSql .= "                        AND CLA.cod_historico not between 800 and 899                                        \n";
    $stSql .= "                    GROUP BY                                                                                 \n";
    $stSql .= "                        CCC.cod_plano,                                                                       \n";
    $stSql .= "                        CCC.exercicio                                                                        \n";
    $stSql .= "                    ORDER BY                                                                                 \n";
    $stSql .= "                        CCC.cod_plano,                                                                       \n";
    $stSql .= "                        CCC.exercicio                                                                        \n";
    $stSql .= "                ) AS CCC ON (                                                                                \n";
    $stSql .= "                    OCA.cod_plano = CCC.cod_plano AND                                                        \n";
    $stSql .= "                    OCA.exercicio = CCC.exercicio                                                            \n";
    $stSql .= "                )                                                                                            \n";
    $stSql .= "            WHERE                                                                                            \n";
    $stSql .= "                OPC.exercicio = '".$this->getDado('exercicio')."'                                            \n";
    $stSql .= "                AND OPC.cod_estrutural    like  '5.%'                                                        \n";
    $stSql .= "                AND  OPC.cod_estrutural   not like  '5.1.1%'                                                  \n";
    $stSql .= "                AND  OPC.cod_estrutural   not like  '5.1.2%'                                                  \n";
    $stSql .= "          GROUP BY                                                                                           \n";
    $stSql .= "                OPC.cod_estrutural,                                                                          \n";
    $stSql .= "                OPC.exercicio                                                                                \n";
    $stSql .= "          ORDER BY                                                                                           \n";
    $stSql .= "                OPC.cod_estrutural,                                                                          \n";
    $stSql .= "                OPC.exercicio                                                                                \n";
    $stSql .= "        ) AS tbl                                                                                            \n";
    $stSql .= "    GROUP BY                                                                                                 \n";
    $stSql .= "        tbl.cod_estrutural                                                                                  \n";
    $stSql .= "    ORDER BY                                                                                                 \n";
    $stSql .= "        tbl.cod_estrutural                                                                                  \n";
    $stSql .= "    ) as tabela                                                                                              \n";
    $stSql .= "where tabela.vl_arrecadado <> 0                                                                              \n";
    $stSql .= ") as saldo                                                                                                   \n";

    return $stSql;
}
/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaOrcamentoFuncao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDespesaPorFuncao(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDespesaPorFuncao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/*
 * Monta Relatório Anexo 13
 *
 * Filtros : Demonstrar Despesa "Pagos"
 *           Tipo de Relatório  "por Função"
 * retorna stSql para o método: montaRecuperaDespesaPorFuncao()
 *
 * @access private
 * @return string $stSql
*/
function montaRelatotorioPorFuncaoTipoPagos()
{
    $stSql  = "SELECT funcao.exercicio                                                                                                        \n";
    $stSql .= "     , funcao.cod_funcao                                                                                                       \n";
    $stSql .= "     , funcao.descricao                                                                                                        \n";
    $stSql .= "     , sum( COALESCE(nota_liquidacao_paga.vl_total                , 0.00)) -                                                   \n";
    $stSql .= "       sum( COALESCE(nota_liquidacao_paga_anulada.vl_total_anulado, 0.00)) as vl_total                                         \n";
    $stSql .= "  FROM orcamento.funcao                                                                                                        \n";
    $stSql .= "     , orcamento.despesa                                                                                                       \n";
    $stSql .= "     , empenho.pre_empenho_despesa                                                                                             \n";
    $stSql .= "     , empenho.pre_empenho                                                                                                     \n";
    $stSql .= "     , empenho.empenho                                                                                                         \n";
    $stSql .= "     , empenho.nota_liquidacao                                                                                                 \n";
    $stSql .= "       LEFT JOIN ( SELECT cod_entidade                                                                                         \n";
    $stSql .= "                        , exercicio                                                                                            \n";
    $stSql .= "                        , cod_nota                                                                                             \n";
    $stSql .= "                        , sum( vl_pago ) as vl_total                                                                           \n";
    $stSql .= "                     FROM empenho.nota_liquidacao_paga                                                                         \n";
    $stSql .= "                    WHERE COALESCE( TO_DATE( timestamp, 'yyyy-mm-dd' ), TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' ) )\n";
    $stSql .= "                                                                BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                                                                    AND TO_DATE( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                    GROUP BY exercicio                                                                                         \n";
    $stSql .= "                           , cod_entidade                                                                                      \n";
    $stSql .= "                           , cod_nota                                                                                          \n";
    $stSql .= "                 ) AS nota_liquidacao_paga ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio                    \n";
    $stSql .= "                                          AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade                 \n";
    $stSql .= "                                          AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota                     \n";
    $stSql .= "       LEFT JOIN ( SELECT exercicio                                                                                            \n";
    $stSql .= "                        , cod_entidade                                                                                         \n";
    $stSql .= "                        , cod_nota                                                                                             \n";
    $stSql .= "                        , sum( coalesce( vl_anulado, 0.00 ) ) as vl_total_anulado                                              \n";
    $stSql .= "                     FROM empenho.nota_liquidacao_paga_anulada                                                                 \n";
    $stSql .= "                    WHERE coalesce( TO_DATE( timestamp_anulada, 'yyyy-mm-dd' ), TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' ) )\n";
    $stSql .= "                                                                        BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                                                                            AND TO_DATE( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                    GROUP BY exercicio                                                                                         \n";
    $stSql .= "                           , cod_entidade                                                                                      \n";
    $stSql .= "                           , cod_nota                                                                                          \n";
    $stSql .= "                 ) AS nota_liquidacao_paga_anulada ON nota_liquidacao.exercicio    = nota_liquidacao_paga_anulada.exercicio    \n";
    $stSql .= "                                                  AND nota_liquidacao.cod_entidade = nota_liquidacao_paga_anulada.cod_entidade \n";
    $stSql .= "                                                  AND nota_liquidacao.cod_nota     = nota_liquidacao_paga_anulada.cod_nota     \n";
    $stSql .= "WHERE funcao.exercicio     = despesa.exercicio                                                                                 \n";
    $stSql .= "  AND funcao.cod_funcao    = despesa.cod_funcao                                                                                \n";
    $stSql .= "  AND despesa.cod_despesa  = pre_empenho_despesa.cod_despesa                                                                   \n";
    $stSql .= "  AND despesa.exercicio    = pre_empenho_despesa.exercicio                                                                     \n";
    $stSql .= "  AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho                                                        \n";
    $stSql .= "  AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio                                                              \n";
    $stSql .= "  AND pre_empenho.cod_pre_empenho         = empenho.cod_pre_empenho                                                            \n";
    $stSql .= "  AND pre_empenho.exercicio               = empenho.exercicio                                                                  \n";
    $stSql .= "  AND empenho.cod_empenho  = nota_liquidacao.cod_empenho                                                                       \n";
    $stSql .= "  AND empenho.exercicio    = nota_liquidacao.exercicio_empenho                                                                 \n";
    $stSql .= "  AND empenho.cod_entidade = nota_liquidacao.cod_entidade                                                                      \n";
    $stSql .= "  AND despesa.cod_entidade IN ( ".$this->getDado('cod_entidade' )." )                                                          \n";
    $stSql .= "  AND funcao.exercicio = '".$this->getDado( 'exercicio' )."'                                                                   \n";
    $stSql .= "GROUP BY funcao.exercicio                                                                                                      \n";
    $stSql .= "       , funcao.cod_funcao                                                                                                     \n";
    $stSql .= "       , funcao.descricao                                                                                                      \n";
    $stSql .= "ORDER BY funcao.exercicio                                                                                                      \n";
    $stSql .= "       , funcao.cod_funcao                                                                                                     \n";
    $stSql .= "       , funcao.descricao                                                                                                      \n";

    return $stSql;
}

/*
 * Monta Relatório Anexo 13
 *
 * Filtros : Demonstrar Despesa "Liquidados"
 *           Tipo de Relatório  "por Função"
 * retorna stSql para o método: montaRecuperaDespesaPorFuncao()
 *
 * @access private
 * @return string $stSql
*/
function montaRelatotorioPorFuncaoTipoLiquidados()
{
    $stSql  = "SELECT   \n";
    $stSql .= "       funcao.exercicio  \n";
    $stSql .= "     , funcao.cod_funcao  \n";
    $stSql .= "     , funcao.descricao  \n";
    $stSql .= "     , sum( coalesce( nota_liquidacao_item.vl_total                , 0.00 ) ) -  \n";
    $stSql .= "       sum( coalesce( nota_liquidacao_item_anulado.vl_total_anulado, 0.00 ) ) AS vl_total  \n";
    $stSql .= "  \n";
    $stSql .= "FROM   orcamento.funcao  \n";
    $stSql .= "     , orcamento.despesa  \n";
    $stSql .= "     , empenho.pre_empenho_despesa  \n";
    $stSql .= "     , empenho.pre_empenho  \n";
    $stSql .= "     , empenho.empenho  \n";
    $stSql .= "  \n";
    $stSql .= "LEFT JOIN ( SELECT sum( nota_liquidacao_item.vl_total ) AS vl_total  \n";
    $stSql .= "                  ,nota_liquidacao.exercicio_empenho  \n";
    $stSql .= "                  ,nota_liquidacao.cod_empenho  \n";
    $stSql .= "                  ,nota_liquidacao.cod_entidade  \n";
    $stSql .= "  \n";
    $stSql .= "            FROM  empenho.nota_liquidacao  \n";
    $stSql .= "                , empenho.nota_liquidacao_item  \n";
    $stSql .= "  \n";
    $stSql .= "            WHERE nota_liquidacao.exercicio    = nota_liquidacao_item.exercicio  \n";
    $stSql .= "              AND nota_liquidacao.cod_nota     = nota_liquidacao_item.cod_nota  \n";
    $stSql .= "              AND nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade  \n";
    $stSql .= "              AND nota_liquidacao.dt_liquidacao  BETWEEN  TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                                                     AND  TO_DATE( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )   \n";
    $stSql .= "  \n";
    $stSql .= "            GROUP BY nota_liquidacao.exercicio_empenho  \n";
    $stSql .= "                    ,nota_liquidacao.cod_empenho  \n";
    $stSql .= "                    ,nota_liquidacao.cod_entidade   \n";
    $stSql .= "          ) AS nota_liquidacao_item  \n";
    $stSql .= "                ON ( empenho.exercicio    = nota_liquidacao_item.exercicio_empenho  \n";
    $stSql .= "               AND   empenho.cod_empenho  = nota_liquidacao_item.cod_empenho   \n";
    $stSql .= "               AND   empenho.cod_entidade = nota_liquidacao_item.cod_entidade)   \n";
    $stSql .= "  \n";
    $stSql .= "LEFT JOIN ( SELECT sum( nota_liquidacao_item_anulado.vl_anulado ) AS vl_total_anulado  \n";
    $stSql .= "                  ,nota_liquidacao.exercicio_empenho  \n";
    $stSql .= "                  ,nota_liquidacao.cod_empenho  \n";
    $stSql .= "                  ,nota_liquidacao.cod_entidade  \n";
    $stSql .= "  \n";
    $stSql .= "            FROM  empenho.nota_liquidacao  \n";
    $stSql .= "                , empenho.nota_liquidacao_item_anulado  \n";
    $stSql .= "  \n";
    $stSql .= "            WHERE nota_liquidacao.exercicio    = nota_liquidacao_item_anulado.exercicio  \n";
    $stSql .= "              AND nota_liquidacao.cod_nota     = nota_liquidacao_item_anulado.cod_nota  \n";
    $stSql .= "              AND nota_liquidacao.cod_entidade = nota_liquidacao_item_anulado.cod_entidade  \n";
    $stSql .= "              AND coalesce( TO_DATE( timestamp, 'yyyy-mm-dd' ), TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' ) )  \n";
    $stSql .= "                                                        BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                                                            AND TO_DATE( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )   \n";
    $stSql .= "  \n";
    $stSql .= "            GROUP BY nota_liquidacao.exercicio_empenho  \n";
    $stSql .= "                    ,nota_liquidacao.cod_empenho  \n";
    $stSql .= "                    ,nota_liquidacao.cod_entidade  \n";
    $stSql .= "          ) AS nota_liquidacao_item_anulado   \n";
    $stSql .= "                ON ( empenho.exercicio    = nota_liquidacao_item_anulado.exercicio_empenho  \n";
    $stSql .= "               AND   empenho.cod_empenho  = nota_liquidacao_item_anulado.cod_empenho  \n";
    $stSql .= "               AND   empenho.cod_entidade = nota_liquidacao_item_anulado.cod_entidade)  \n";
    $stSql .= "  \n";
    $stSql .= "WHERE funcao.exercicio     = despesa.exercicio  \n";
    $stSql .= "  AND funcao.cod_funcao    = despesa.cod_funcao  \n";
    $stSql .= "  AND despesa.cod_despesa  = pre_empenho_despesa.cod_despesa  \n";
    $stSql .= "  AND despesa.exercicio    = pre_empenho_despesa.exercicio  \n";
    $stSql .= "  AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho  \n";
    $stSql .= "  AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio  \n";
    $stSql .= "  AND pre_empenho.cod_pre_empenho         = empenho.cod_pre_empenho  \n";
    $stSql .= "  AND pre_empenho.exercicio               = empenho.exercicio  \n";
    $stSql .= "  \n";
    $stSql .= "  AND despesa.cod_entidade IN ( ".$this->getDado('cod_entidade' )." )  \n";
    $stSql .= "  AND funcao.exercicio = '".$this->getDado( 'exercicio' )."' \n";
    $stSql .= "  \n";
    $stSql .= "GROUP BY funcao.exercicio  \n";
    $stSql .= "       , funcao.cod_funcao  \n";
    $stSql .= "       , funcao.descricao  \n";
    $stSql .= "  \n";
    $stSql .= "ORDER BY funcao.exercicio  \n";
    $stSql .= "       , funcao.cod_funcao  \n";
    $stSql .= "       , funcao.descricao  \n";

    return $stSql;
}

function montaRecuperaDespesaPorFuncao()
{
    $stSql .= "SELECT    OFU.exercicio                                                                                                    \n";
    $stSql .= "         ,OFU.cod_funcao                                                                                                   \n";
    $stSql .= "         ,OFU.descricao                                                                                                    \n";
    $stSql .= "       -- EMPENHADO                                                                                                    \n";
    $stSql .= "       ,sum( coalesce( EIPE.vl_total       , 0.00 ) ) -                                                                \n";
    $stSql .= "        sum( coalesce( EEA.vl_total_anulado, 0.00 ) ) AS vl_total                                                      \n";
    $stSql .= "       FROM orcamento.funcao        AS OFU                                                                                 \n";
    $stSql .= "       -- Join com orcamento.despesa                                                                                       \n";
    $stSql .= "       LEFT JOIN orcamento.despesa  AS OD                                                                                  \n";
    $stSql .= "       ON( OFU.exercicio  = OD.exercicio                                                                                   \n";
    $stSql .= "       AND OFU.cod_funcao = OD.cod_funcao )                                                                                \n";
    $stSql .= "       -- Join com empenho.pre_empenho_despesa                                                                             \n";
    $stSql .= "       LEFT JOIN empenho.pre_empenho_despesa AS EPED                                                                       \n";
    $stSql .= "       ON( OD.cod_despesa = EPED.cod_despesa                                                                               \n";
    $stSql .= "       AND OD.exercicio   = EPED.exercicio  )                                                                              \n";
    $stSql .= "       -- Join com empenho.pre_empenho                                                                                     \n";
    $stSql .= "       LEFT JOIN empenho.pre_empenho AS EPE                                                                                \n";
    $stSql .= "       ON( EPED.cod_pre_empenho = EPE.cod_pre_empenho                                                                      \n";
    $stSql .= "       AND EPED.exercicio       = EPE.exercicio       )                                                                    \n";
    $stSql .= "       -- Join com empenho.empenho                                                                                         \n";
    $stSql .= "       LEFT JOIN empenho.empenho AS EE                                                                                     \n";
    $stSql .= "       ON( EPE.cod_pre_empenho = EE.cod_pre_empenho                                                                        \n";
    $stSql .= "       AND EPE.exercicio       = EE.exercicio         )                                                                    \n";
    $stSql .= "       -- EMPENHADO                                                                                                        \n";
    $stSql .= "       -- Join com empenho.item_pre_empenho                                                                                \n";
    $stSql .= "       LEFT JOIN ( SELECT sum( coalesce( EIPE.vl_total, 0.00 ) ) as vl_total                                               \n";
    $stSql .= "                         ,EIPE.cod_pre_empenho                                                                             \n";
    $stSql .= "                         ,EIPE.exercicio                                                                                   \n";
    $stSql .= "                   FROM empenho.item_pre_empenho AS EIPE                                                                   \n";
    $stSql .= "                   GROUP BY EIPE.exercicio                                                                                 \n";
    $stSql .= "                           ,EIPE.cod_pre_empenho                                                                           \n";
    $stSql .= "                   ORDER BY EIPE.exercicio                                                                                 \n";
    $stSql .= "                           ,EIPE.cod_pre_empenho                                                                           \n";
    $stSql .= "       ) AS EIPE ON( EPE.cod_pre_empenho  = EIPE.cod_pre_empenho                                                           \n";
    $stSql .= "                 AND EPE.exercicio        = EIPE.exercicio                                                                 \n";
    $stSql .= "                 AND EIPE.cod_pre_empenho = EE.cod_pre_empenho                                                             \n";
    $stSql .= "                 AND EIPE.exercicio       = EE.exercicio                                                                   \n";
    $stSql .= "                 AND coalesce( EE.dt_empenho, '".$this->getDado('dt_inicial')."' )                                         \n";
    $stSql .= "                             BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )                           \n";
    $stSql .= "                                 AND TO_DATE( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )                           \n";
    $stSql .= "       )                                                                                                                   \n";
    $stSql .= "       -- Join com empenho.empenho_anulado                                                                                 \n";
    $stSql .= "       LEFT JOIN ( SELECT sum( coalesce( EEAI.vl_anulado, 0.00 ) ) AS vl_total_anulado                                     \n";
    $stSql .= "                         ,EEA.exercicio                                                                                    \n";
    $stSql .= "                         ,EEA.cod_entidade                                                                                 \n";
    $stSql .= "                         ,EEA.cod_empenho                                                                                  \n";
    $stSql .= "                   FROM empenho.empenho_anulado AS EEA                                                                     \n";
    $stSql .= "                       ,empenho.empenho_anulado_item AS EEAI                                                               \n";
    $stSql .= "                   WHERE EEA.exercicio    = EEAI.exercicio                                                                 \n";
    $stSql .= "                   AND   EEA.cod_entidade = EEAI.cod_entidade                                                              \n";
    $stSql .= "                   AND   EEA.cod_empenho  = EEAI.cod_empenho                                                               \n";
    $stSql .= "                   AND   EEA.timestamp    = EEAI.timestamp                                                                 \n";
    $stSql .= "                   AND coalesce( TO_DATE( EEA.timestamp::text, 'yyyy-mm-dd' ), TO_DATE( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' ) ) \n";
    $stSql .= "                                                                 BETWEEN TO_DATE( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )   \n";
    $stSql .= "                                                                     AND TO_DATE( '".$this->getDado( 'dt_final'   )."', 'dd/mm/yyyy' )   \n";
    $stSql .= "                   GROUP BY EEA.exercicio                                                                                  \n";
    $stSql .= "                           ,EEA.cod_entidade                                                                               \n";
    $stSql .= "                           ,EEA.cod_empenho                                                                                \n";
    $stSql .= "                   ORDER BY EEA.exercicio                                                                                  \n";
    $stSql .= "                           ,EEA.cod_entidade                                                                               \n";
    $stSql .= "                           ,EEA.cod_empenho                                                                                \n";
    $stSql .= "       ) AS EEA ON( EE.exercicio    = EEA.exercicio                                                                        \n";
    $stSql .= "                AND EE.cod_entidade = EEA.cod_entidade                                                                     \n";
    $stSql .= "                AND EE.cod_empenho  = EEA.cod_empenho   )                                                                  \n";
    $stSql .= "    WHERE OFU.exercicio = '".$this->getDado( 'exercicio' )."'                                                                \n";
    $stSql .= "    AND   coalesce( OD.cod_entidade, 0 ) IN ( 0,".$this->getDado('cod_entidade' )." )                                        \n";
    $stSql .= "    GROUP BY OFU.exercicio                                                                                                   \n";
    $stSql .= "            ,OFU.cod_funcao                                                                                                  \n";
    $stSql .= "            ,OFU.descricao                                                                                                   \n";
    $stSql .= "    ORDER BY OFU.exercicio                                                                                                   \n";
    $stSql .= "            ,OFU.cod_funcao                                                                                                  \n";
    $stSql .= "            ,OFU.descricao                                                                                                   \n";

    switch ( $this->getDado( 'stTipoRelatorio' ) ) {
        case 'P':
            $stSql = $this->montaRelatotorioPorFuncaoTipoPagos();
        break;
        case 'L':
            $stSql = $this->montaRelatotorioPorFuncaoTipoLiquidados();
        break;
    }

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaOrcamentoFuncao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDespesaCategoriaEconomica(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDespesaPorCategoriaEconomica();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/*
 * Monta Relatório Anexo 13
 *
 * Filtros : Demonstrar Despesa "Liquidados"
 *           Tipo de Relatório  "por Categoria Econômica"
 * retorna stSql para o método: montaRecuperaDespesaPorFuncao()
 *
 * @access private
 * @return string $stSql
*/
function montaRelatotorioPorCategoriaEconomicaTipoLiquidados()
{
    $stSql  = "select   conta_despesa.exercicio\n";
    $stSql .= "       , publico.fn_mascarareduzida( substr( conta_despesa.cod_estrutural,1,3) )  as cod_estrutural\n";
    $stSql .= "       , conta_despesa.descricao\n";
    $stSql .= "       , sum ( anexo.vl_total ) as vl_total\n";
    $stSql .= "from (\n";
    $stSql .= "        SELECT   \n";
    $stSql .= "               conta_despesa.exercicio  \n";
    $stSql .= "             , publico.fn_mascarareduzida( substr( conta_despesa.cod_estrutural,1,3) )  as cod_estrutural\n";
    $stSql .= "             , sum( coalesce( nota_liquidacao_item.vl_total                , 0.00 ) ) -  \n";
    $stSql .= "               sum( coalesce( nota_liquidacao_item_anulado.vl_total_anulado, 0.00 ) ) AS vl_total  \n";
    $stSql .= "          \n";
    $stSql .= "        FROM   orcamento.despesa \n";
    $stSql .= "             , orcamento.conta_despesa\n";
    $stSql .= "             , empenho.pre_empenho_despesa  \n";
    $stSql .= "             , empenho.pre_empenho  \n";
    $stSql .= "             , empenho.empenho  \n";
    $stSql .= "          \n";
    $stSql .= "        LEFT JOIN ( SELECT sum( nota_liquidacao_item.vl_total ) AS vl_total  \n";
    $stSql .= "                          ,nota_liquidacao.exercicio_empenho  \n";
    $stSql .= "                          ,nota_liquidacao.cod_empenho  \n";
    $stSql .= "                          ,nota_liquidacao.cod_entidade  \n";
    $stSql .= "          \n";
    $stSql .= "                    FROM  empenho.nota_liquidacao  \n";
    $stSql .= "                        , empenho.nota_liquidacao_item  \n";
    $stSql .= "          \n";
    $stSql .= "                    WHERE nota_liquidacao.exercicio    = nota_liquidacao_item.exercicio  \n";
    $stSql .= "                      AND nota_liquidacao.cod_nota     = nota_liquidacao_item.cod_nota  \n";
    $stSql .= "                      AND nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade  \n";
    $stSql .= "                      AND nota_liquidacao.dt_liquidacao  BETWEEN  TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                                                             AND  TO_DATE( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )   \n";
    $stSql .= "          \n";
    $stSql .= "                    GROUP BY nota_liquidacao.exercicio_empenho  \n";
    $stSql .= "                            ,nota_liquidacao.cod_empenho  \n";
    $stSql .= "                            ,nota_liquidacao.cod_entidade   \n";
    $stSql .= "                  ) AS nota_liquidacao_item  \n";
    $stSql .= "                        ON ( empenho.exercicio    = nota_liquidacao_item.exercicio_empenho  \n";
    $stSql .= "                       AND   empenho.cod_empenho  = nota_liquidacao_item.cod_empenho   \n";
    $stSql .= "                       AND   empenho.cod_entidade = nota_liquidacao_item.cod_entidade)   \n";
    $stSql .= "          \n";
    $stSql .= "        LEFT JOIN ( SELECT sum( nota_liquidacao_item_anulado.vl_anulado ) AS vl_total_anulado  \n";
    $stSql .= "                          ,nota_liquidacao.exercicio_empenho  \n";
    $stSql .= "                          ,nota_liquidacao.cod_empenho  \n";
    $stSql .= "                          ,nota_liquidacao.cod_entidade  \n";
    $stSql .= "          \n";
    $stSql .= "                    FROM  empenho.nota_liquidacao  \n";
    $stSql .= "                        , empenho.nota_liquidacao_item_anulado  \n";
    $stSql .= "          \n";
    $stSql .= "                    WHERE nota_liquidacao.exercicio    = nota_liquidacao_item_anulado.exercicio  \n";
    $stSql .= "                      AND nota_liquidacao.cod_nota     = nota_liquidacao_item_anulado.cod_nota  \n";
    $stSql .= "                      AND nota_liquidacao.cod_entidade = nota_liquidacao_item_anulado.cod_entidade  \n";
    $stSql .= "                      AND coalesce( TO_DATE( timestamp, 'yyyy-mm-dd' ), TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' ) )  \n";
    $stSql .= "                                                                BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                                                                    AND TO_DATE( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )   \n";
    $stSql .= "          \n";
    $stSql .= "                    GROUP BY nota_liquidacao.exercicio_empenho  \n";
    $stSql .= "                            ,nota_liquidacao.cod_empenho  \n";
    $stSql .= "                            ,nota_liquidacao.cod_entidade  \n";
    $stSql .= "                  ) AS nota_liquidacao_item_anulado   \n";
    $stSql .= "                        ON ( empenho.exercicio    = nota_liquidacao_item_anulado.exercicio_empenho  \n";
    $stSql .= "                       AND   empenho.cod_empenho  = nota_liquidacao_item_anulado.cod_empenho  \n";
    $stSql .= "                       AND   empenho.cod_entidade = nota_liquidacao_item_anulado.cod_entidade)  \n";
    $stSql .= "          \n";
    $stSql .= "        WHERE conta_despesa.exercicio  = despesa.exercicio  \n";
    $stSql .= "          AND conta_despesa.cod_conta  = despesa.cod_conta\n";
    $stSql .= "          AND despesa.cod_despesa  = pre_empenho_despesa.cod_despesa  \n";
    $stSql .= "          AND despesa.exercicio    = pre_empenho_despesa.exercicio  \n";
    $stSql .= "          AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho  \n";
    $stSql .= "          AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio  \n";
    $stSql .= "          AND pre_empenho.cod_pre_empenho         = empenho.cod_pre_empenho  \n";
    $stSql .= "          AND pre_empenho.exercicio               = empenho.exercicio  \n";
    $stSql .= "          \n";
    $stSql .= "          AND conta_despesa.exercicio = '".$this->getDado( 'exercicio')."' \n";
    $stSql .= "          AND despesa.cod_entidade IN ( ".$this->getDado( 'cod_entidade' )." )\n";
    $stSql .= "          \n";
    $stSql .= "        GROUP BY conta_despesa.exercicio  \n";
    $stSql .= "                , publico.fn_mascarareduzida( substr( conta_despesa.cod_estrutural,1,3) ) \n";
    $stSql .= "          \n";
    $stSql .= "        ORDER BY  conta_despesa.exercicio  \n";
    $stSql .= "                , publico.fn_mascarareduzida( substr( conta_despesa.cod_estrutural,1,3) ) \n";
    $stSql .= "        \n";
    $stSql .= ")as anexo\n";
    $stSql .= "\n";
    $stSql .= "join orcamento.conta_despesa \n";
    $stSql .= "     on (    anexo.cod_estrutural = publico.fn_mascarareduzida( conta_despesa.cod_estrutural )\n";
    $stSql .= "         and anexo.exercicio      = conta_despesa.exercicio \n";
    $stSql .= "        )\n";
    $stSql .= "group by   conta_despesa.exercicio\n";
    $stSql .= "         , publico.fn_mascarareduzida( substr( conta_despesa.cod_estrutural,1,3) )\n";
    $stSql .= "         , conta_despesa.descricao\n";
    $stSql .= "\n";
    $stSql .= "order by publico.fn_mascarareduzida( substr( conta_despesa.cod_estrutural,1,3) )\n";

    return $stSql;
}

function montaRecuperaDespesaPorCategoriaEconomica()
{
    $stSql  = "SELECT tbl.cod_estrutural                                                                \n";
    $stSql .= "      ,sum( tbl.vl_total ) as vl_total                                                   \n";
    $stSql .= "      ,OCD.descricao                                                                     \n";
    $stSql .= "FROM( SELECT substr( OCD.cod_estrutural, 1, 3 ) as cod_estrutural                        \n";
    $stSql .= "            ,EE.exercicio                                                                \n";

    if ( $this->getDado( 'stTipoRelatorio' ) == 'E' ) {
        $stSql .= "       -- EMPENHADO                                                                                                    \n";
        $stSql .= "       ,sum( coalesce( EIPE.vl_total       , 0.00 ) ) -                                                                \n";
        $stSql .= "        sum( coalesce( EEA.vl_total_anulado, 0.00 ) ) AS vl_total                                                      \n";
    } elseif ( $this->getDado( 'stTipoRelatorio' ) == 'P' ) {
        $stSql .= "       -- PAGO                                                                                                         \n";
        $stSql .= "       ,sum( coalesce( ENLP.vl_total         , 0.00 ) ) -                                                              \n";
        $stSql .= "        sum( coalesce( ENLPA.vl_total_anulado, 0.00 ) ) AS vl_total                                                    \n";
    }

    $stSql .= "      FROM orcamento.conta_despesa     AS OCD                                            \n";
    $stSql .= "          ,empenho.pre_empenho_despesa AS EPED                                           \n";
    $stSql .= "          ,empenho.pre_empenho         AS EPE                                            \n";
    $stSql .= "          --Ligação pre_empenho : empenho                                                \n";
    $stSql .= "          LEFT JOIN empenho.empenho    AS EE                                             \n";
    $stSql .= "          ON( EPE.exercicio       = EE.exercicio                                         \n";
    $stSql .= "          AND EPE.cod_pre_empenho = EE.cod_pre_empenho  )                                \n";

    if ( $this->getDado( 'stTipoRelatorio' ) == 'E' ) {
        $stSql .= "       -- EMPENHADO                                                                                                        \n";
        $stSql .= "       -- Join com empenho.item_pre_empenho                                                                                \n";
        $stSql .= "       LEFT JOIN ( SELECT sum( coalesce( EIPE.vl_total, 0.00 ) ) as vl_total                                               \n";
        $stSql .= "                         ,EIPE.cod_pre_empenho                                                                             \n";
        $stSql .= "                         ,EIPE.exercicio                                                                                   \n";
        $stSql .= "                   FROM empenho.item_pre_empenho AS EIPE                                                                   \n";
        $stSql .= "                   GROUP BY EIPE.exercicio                                                                                 \n";
        $stSql .= "                           ,EIPE.cod_pre_empenho                                                                           \n";
        $stSql .= "                   ORDER BY EIPE.exercicio                                                                                 \n";
        $stSql .= "                           ,EIPE.cod_pre_empenho                                                                           \n";
        $stSql .= "       ) AS EIPE ON( EPE.cod_pre_empenho  = EIPE.cod_pre_empenho                                                           \n";
        $stSql .= "                 AND EPE.exercicio        = EIPE.exercicio                                                                 \n";
        $stSql .= "                 AND EIPE.cod_pre_empenho = EE.cod_pre_empenho                                                             \n";
        $stSql .= "                 AND EIPE.exercicio       = EE.exercicio                                                                   \n";
        $stSql .= "                 AND coalesce( EE.dt_empenho, '".$this->getDado('dt_inicial')."' )                                         \n";
        $stSql .= "                             BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )                           \n";
        $stSql .= "                                 AND TO_DATE( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )                           \n";
        $stSql .= "       )                                                                                                                   \n";
        $stSql .= "       -- Join com empenho.empenho_anulado                                                                                 \n";
        $stSql .= "       LEFT JOIN ( SELECT sum( coalesce( EEAI.vl_anulado, 0.00 ) ) AS vl_total_anulado                                     \n";
        $stSql .= "                         ,EEA.exercicio                                                                                    \n";
        $stSql .= "                         ,EEA.cod_entidade                                                                                 \n";
        $stSql .= "                         ,EEA.cod_empenho                                                                                  \n";
        $stSql .= "                   FROM empenho.empenho_anulado AS EEA                                                                     \n";
        $stSql .= "                       ,empenho.empenho_anulado_item AS EEAI                                                               \n";
        $stSql .= "                   WHERE EEA.exercicio    = EEAI.exercicio                                                                 \n";
        $stSql .= "                   AND   EEA.cod_entidade = EEAI.cod_entidade                                                              \n";
        $stSql .= "                   AND   EEA.cod_empenho  = EEAI.cod_empenho                                                               \n";
        $stSql .= "                   AND   EEA.timestamp    = EEAI.timestamp                                                                 \n";
        $stSql .= "                   AND coalesce( TO_DATE( EEA.timestamp::text, 'yyyy-mm-dd' ), TO_DATE( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' ) ) \n";
        $stSql .= "                                                                 BETWEEN TO_DATE( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )   \n";
        $stSql .= "                                                                     AND TO_DATE( '".$this->getDado( 'dt_final'   )."', 'dd/mm/yyyy' )   \n";
        $stSql .= "                   GROUP BY EEA.exercicio                                                                                  \n";
        $stSql .= "                           ,EEA.cod_entidade                                                                               \n";
        $stSql .= "                           ,EEA.cod_empenho                                                                                \n";
        $stSql .= "                   ORDER BY EEA.exercicio                                                                                  \n";
        $stSql .= "                           ,EEA.cod_entidade                                                                               \n";
        $stSql .= "                           ,EEA.cod_empenho                                                                                \n";
        $stSql .= "       ) AS EEA ON( EE.exercicio    = EEA.exercicio                                                                        \n";
        $stSql .= "                AND EE.cod_entidade = EEA.cod_entidade                                                                     \n";
        $stSql .= "                AND EE.cod_empenho  = EEA.cod_empenho   )                                                                  \n";
    } elseif ( $this->getDado( 'stTipoRelatorio' ) == 'P' ) {
        $stSql .= "    -- PAGO                                                                                                              \n";
        $stSql .= "    -- Join com empenho.nota_liquidacao                                                                                  \n";
        $stSql .= "    LEFT JOIN empenho.nota_liquidacao AS ENL                                                                             \n";
        $stSql .= "    ON( EE.cod_empenho  = ENL.cod_empenho                                                                                \n";
        $stSql .= "    AND EE.exercicio    = ENL.exercicio_empenho                                                                          \n";
        $stSql .= "    AND EE.cod_entidade = ENL.cod_entidade       )                                                                       \n";
        $stSql .= "    -- Join com empenho.nota+liquidacao_paga                                                                             \n";
        $stSql .= "    LEFT JOIN( SELECT ENLP.cod_entidade                                                                                  \n";
        $stSql .= "                     ,ENLP.exercicio                                                                                     \n";
        $stSql .= "                     ,ENLP.cod_nota                                                                                      \n";
        $stSql .= "                     ,ENLP.timestamp                                                                                     \n";
        $stSql .= "                     ,sum( coalesce( ENLP.vl_pago, 0.00 ) ) as vl_total                                                  \n";
        $stSql .= "               FROM  empenho.nota_liquidacao_paga AS ENLP                                                                \n";
        $stSql .= "               WHERE coalesce( TO_DATE( ENLP.timestamp, 'yyyy-mm-dd' ), TO_DATE( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' ) ) \n";
        $stSql .= "                                                                BETWEEN TO_DATE( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )   \n";
        $stSql .= "                                                                    AND TO_DATE( '".$this->getDado( 'dt_final'   )."', 'dd/mm/yyyy' )   \n";
        $stSql .= "               GROUP BY ENLP.exercicio                                                                                   \n";
        $stSql .= "                       ,ENLP.cod_entidade                                                                                \n";
        $stSql .= "                       ,ENLP.cod_nota                                                                                    \n";
        $stSql .= "                       ,ENLP.timestamp                                                                                   \n";
        $stSql .= "               ORDER BY ENLP.exercicio                                                                                   \n";
        $stSql .= "                       ,ENLP.cod_entidade                                                                                \n";
        $stSql .= "                       ,ENLP.cod_nota                                                                                    \n";
        $stSql .= "                       ,ENLP.timestamp                                                                                   \n";
        $stSql .= "    ) AS ENLP ON( ENL.exercicio    = ENLP.exercicio                                                                      \n";
        $stSql .= "              AND ENL.cod_entidade = ENLP.cod_entidade                                                                   \n";
        $stSql .= "              AND ENL.cod_nota     = ENLP.cod_nota     )                                                                 \n";
        $stSql .= "    -- Join com empenho.nota+liquidacao_paga_anulada                                                                     \n";
        $stSql .= "    LEFT JOIN( SELECT ENLPA.exercicio                                                                                    \n";
        $stSql .= "                     ,ENLPA.cod_entidade                                                                                 \n";
        $stSql .= "                     ,ENLPA.cod_nota                                                                                     \n";
        $stSql .= "                     ,ENLPA.timestamp                                                                                    \n";
        $stSql .= "                     ,sum( coalesce( ENLPA.vl_anulado, 0.00 ) ) as vl_total_anulado                                      \n";
        $stSql .= "               FROM empenho.nota_liquidacao_paga_anulada AS ENLPA                                                        \n";
        $stSql .= "               WHERE coalesce( TO_DATE( ENLPA.timestamp_anulada, 'yyyy-mm-dd' ),                                         \n";
        $stSql .= "                                                         TO_DATE( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' ) ) \n";
        $stSql .= "                                                 BETWEEN TO_DATE( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )   \n";
        $stSql .= "                                                     AND TO_DATE( '".$this->getDado( 'dt_final'   )."', 'dd/mm/yyyy' )   \n";
        $stSql .= "               GROUP BY ENLPA.exercicio                                                                                  \n";
        $stSql .= "                       ,ENLPA.cod_entidade                                                                               \n";
        $stSql .= "                       ,ENLPA.cod_nota                                                                                   \n";
        $stSql .= "                       ,ENLPA.timestamp                                                                                  \n";
        $stSql .= "               ORDER BY ENLPA.exercicio                                                                                  \n";
        $stSql .= "                       ,ENLPA.cod_entidade                                                                               \n";
        $stSql .= "                       ,ENLPA.cod_nota                                                                                   \n";
        $stSql .= "                       ,ENLPA.timestamp                                                                                  \n";
        $stSql .= "    ) AS ENLPA ON( ENLP.exercicio    = ENLPA.exercicio                                                                   \n";
        $stSql .= "               AND ENLP.cod_entidade = ENLPA.cod_entidade                                                                \n";
        $stSql .= "               AND ENLP.cod_nota     = ENLPA.cod_nota                                                                    \n";
        $stSql .= "               AND ENLP.timestamp    = ENLPA.timestamp     )                                                             \n";
    }

    $stSql .= "      WHERE                                                                              \n";
    $stSql .= "        --Ligação conta_despesa : pre_empenho_despesa                                    \n";
    $stSql .= "            OCD.cod_conta        = EPED.cod_conta                                        \n";
    $stSql .= "        AND OCD.exercicio        = EPED.exercicio                                        \n";
    $stSql .= "        --Ligação pre_empenho_despesa : pre_empenho                                      \n";
    $stSql .= "        AND EPED.exercicio       = EPE.exercicio                                         \n";
    $stSql .= "        AND EPED.cod_pre_empenho = EPE.cod_pre_empenho                                   \n";
    $stSql .= "        -- FILTRO                                                                        \n";
    $stSql .= "        AND EE.exercicio         = '".$this->getDado( 'exercicio')."'                    \n";
    $stSql .= "        AND EE.cod_entidade     IN ( ".$this->getDado( 'cod_entidade' )." )              \n";
    $stSql .= "        AND ( OCD.cod_estrutural  like ( '3%' )                                          \n";
    $stSql .= "           OR OCD.cod_estrutural  like ( '4%' )                                          \n";
    $stSql .= "           OR OCD.cod_estrutural  like ( '9%' ) )                                        \n";
    $stSql .= "      GROUP BY OCD.cod_estrutural                                                        \n";
    $stSql .= "              ,EE.exercicio                                                              \n";
    $stSql .= "      ORDER BY OCD.cod_estrutural                                                        \n";
    $stSql .= "              ,EE.exercicio                                                              \n";
    $stSql .= ") as tbl                                                                                 \n";
    $stSql .= ",orcamento.conta_despesa AS OCD                                                          \n";
    $stSql .= "WHERE tbl.exercicio = OCD.exercicio                                                      \n";
    $stSql .= "  AND tbl.cod_estrutural = publico.fn_mascarareduzida( OCD.cod_estrutural )              \n";
    $stSql .= "GROUP BY tbl.cod_estrutural                                                              \n";
    $stSql .= "        ,OCD.descricao                                                                   \n";
    $stSql .= "ORDER BY tbl.cod_estrutural                                                              \n";

    if ( $this->getDado( 'stTipoRelatorio' ) == 'L' ) {
        $stSql = $this->montaRelatotorioPorCategoriaEconomicaTipoLiquidados();
    }

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaSaldoDespesa.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaSaldoReceita(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = " ORDER BY cod_grupo";
    $stSql = $this->montaRecuperaSaldoReceita();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSaldoReceita()
{
 $stSql .= "    SELECT tbl.cod_estrutural                                                                           \n";
 $stSql .= "          ,publico.fn_nivel(tbl.cod_estrutural) as nivel                                                \n";
 $stSql .= "          ,sum( tbl.vl_arrecadado_debito ) as vl_arrecadado_debito, sum( tbl.vl_arrecadado_credito )  as vl_arrecadado_credito   \n";
 $stSql .= "          ,abs( sum( tbl.vl_arrecadado_debito ) + sum( tbl.vl_arrecadado_credito ) ) as vl_arrecadado   \n";
 $stSql .= "          ,OCR.nom_conta                                                                                \n";
 $stSql .= "    FROM(                                                                                               \n";
 $stSql .= "          SELECT substr( OPC.cod_estrutural, 1,9)    AS cod_estrutural                                  \n";
 $stSql .= "                ,OPC.exercicio                                                                          \n";
 $stSql .= "                ,sum( coalesce( CCD.vl_lancamento, 0.00 ) ) AS vl_arrecadado_debito            \n";
 $stSql .= "                ,sum( coalesce( CCC.vl_lancamento, 0.00 ) ) AS vl_arrecadado_credito           \n";
 $stSql .= "          FROM contabilidade.plano_conta    AS OPC                                                  \n";
 $stSql .= "          -- Join com plano analitica                                                                   \n";
 $stSql .= "          LEFT JOIN contabilidade.plano_analitica AS OCA                                            \n";
 $stSql .= "          ON( OPC.cod_conta = OCA.cod_conta                                                             \n";
 $stSql .= "          AND OPC.exercicio = OCA.exercicio  )                                                          \n";
 $stSql .= "          LEFT JOIN contabilidade.plano_banco AS pb                                            \n";
 $stSql .= "          ON( pb.cod_plano = OCA.cod_plano                                                             \n";
 $stSql .= "          AND pb.exercicio = OCA.exercicio  )                                                          \n";
 $stSql .= "          -- Join com contabilidade.valor_lancamento                                                    \n";
 $stSql .= "          LEFT JOIN ( SELECT CCD.cod_plano                                                              \n";
 $stSql .= "                            ,CCD.exercicio                                                              \n";
 $stSql .= "                            ,sum( CVLD.vl_lancamento ) as vl_lancamento                                      \n";
// $stSql .= "               ,CASE WHEN TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy' ) = TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') \n";
// $stSql .= "                                 THEN CASE WHEN CVLD.tipo = 'I'                                         \n";
// $stSql .= "                                     THEN sum(coalesce(vl_lancamento,0.00))                             \n";
// $stSql .= "                                 END                                                                    \n";
// $stSql .= "                                 ELSE sum(coalesce(vl_lancamento,0.00))                                 \n";
// $stSql .= "                             END as vl_lancamento                                                       \n";
 $stSql .= "                      FROM contabilidade.conta_debito     AS CCD                                    \n";
 $stSql .= "                          ,contabilidade.valor_lancamento AS CVLD                                   \n";
 $stSql .= "                          ,contabilidade.lote             AS CLO                                    \n";
 $stSql .= "                      WHERE CCD.cod_lote       = CVLD.cod_lote                                          \n";
 $stSql .= "                        AND CCD.tipo           = CVLD.tipo                                              \n";
 $stSql .= "                        AND CCD.sequencia      = CVLD.sequencia                                         \n";
 $stSql .= "                        AND CCD.exercicio      = CVLD.exercicio                                         \n";
 $stSql .= "                        AND CCD.tipo_valor     = CVLD.tipo_valor                                        \n";
 $stSql .= "                        AND CCD.cod_entidade   = CVLD.cod_entidade                                      \n";
 $stSql .= "                        AND CVLD.tipo_valor    = 'D'                                                    \n";
 $stSql .= "                        AND CVLD.cod_lote      = CLO.cod_lote                                           \n";
 $stSql .= "                        AND CVLD.tipo          = CLO.tipo                                               \n";
 $stSql .= "                        AND CASE WHEN TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy' ) = TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') \n";
 $stSql .= "                                 THEN CASE WHEN CVLD.tipo = 'I'                                         \n";
 $stSql .= "                                        THEN true                                                          \n";
 $stSql .= "                                        ELSE false                                                         \n";
 $stSql .= "                                      END                                                                    \n";
 $stSql .= "                                 ELSE true                                                              \n";
 $stSql .= "                             END                                                                        \n";
 $stSql .= "                        AND CVLD.cod_entidade  = CLO.cod_entidade                                       \n";
 $stSql .= "                        AND CVLD.exercicio     = CLO.exercicio                                          \n";
 $stSql .= "                        AND CCD.exercicio      = '".$this->getDado('exercicio')."'                      \n";
 $stSql .= "                        AND CVLD.cod_entidade  IN( ".$this->getDado('cod_entidade')." )                 \n";
 $stSql .= "                        AND CLO.dt_lote BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )  \n";
 $stSql .= "                                            AND TO_DATE( '".$this->getDado('dt_final')  ."', 'dd/mm/yyyy' )  \n";
 $stSql .= "                      GROUP BY CCD.cod_plano                                                            \n";
 $stSql .= "                              ,CCD.exercicio                                                            \n";
// $stSql .= "                              ,CLO.dt_lote                                                              \n";
// $stSql .= "                              ,CVLD.tipo                                                                \n";
 $stSql .= "                      ORDER BY CCD.cod_plano                                                            \n";
 $stSql .= "                              ,CCD.exercicio                                                            \n";
// $stSql .= "                              ,CLO.dt_lote                                                              \n";
// $stSql .= "                              ,CVLD.tipo                                                                \n";
 $stSql .= "          ) AS CCD ON( OCA.cod_plano = CCD.cod_plano                                                    \n";
 $stSql .= "                   AND OCA.exercicio = CCD.exercicio                                                    \n";
 $stSql .= "          )                                                                                             \n";
 $stSql .= "          -- Join com contabilidade.valor_lancamento                                                    \n";
 $stSql .= "          LEFT JOIN ( SELECT CCC.cod_plano                                                              \n";
 $stSql .= "                            ,CCC.exercicio                                                              \n";
 $stSql .= "                            ,sum( CVLC.vl_lancamento ) as vl_lancamento                                 \n";
// $stSql .= "                  ,CASE WHEN TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy') = TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') \n";
// $stSql .= "                                 THEN CASE WHEN CVLC.tipo = 'I'                                         \n";
// $stSql .= "                                     THEN sum(coalesce(vl_lancamento,0.00))                             \n";
// $stSql .= "                                 END                                                                    \n";
// $stSql .= "                                 ELSE sum(coalesce(vl_lancamento,0.00))                                 \n";
// $stSql .= "                             END as vl_lancamento                                                       \n";
 $stSql .= "                      FROM contabilidade.conta_credito    AS CCC                                    \n";
 $stSql .= "                          ,contabilidade.valor_lancamento AS CVLC                                   \n";
 $stSql .= "                          ,contabilidade.lote             AS CLO                                    \n";
 $stSql .= "                      WHERE CCC.cod_lote       = CVLC.cod_lote                                          \n";
 $stSql .= "                        AND CCC.tipo           = CVLC.tipo                                              \n";
 $stSql .= "                        AND CCC.sequencia      = CVLC.sequencia                                         \n";
 $stSql .= "                        AND CCC.exercicio      = CVLC.exercicio                                         \n";
 $stSql .= "                        AND CCC.tipo_valor     = CVLC.tipo_valor                                        \n";
 $stSql .= "                        AND CCC.cod_entidade   = CVLC.cod_entidade                                      \n";
 $stSql .= "                        AND CVLC.tipo_valor    = 'C'                                                    \n";
 $stSql .= "                        AND CVLC.cod_lote      = CLO.cod_lote                                           \n";
 $stSql .= "                        AND CVLC.tipo          = CLO.tipo                                               \n";
 $stSql .= "                        AND CASE WHEN TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy' ) = TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') \n";
 $stSql .= "                                 THEN CASE WHEN CVLC.tipo = 'I'                                         \n";
 $stSql .= "                                        THEN true                                                       \n";
 $stSql .= "                                        ELSE false                                                      \n";
 $stSql .= "                                      END                                                               \n";
 $stSql .= "                                 ELSE true                                                              \n";
 $stSql .= "                             END                                                                        \n";
 $stSql .= "                        AND CVLC.cod_entidade  = CLO.cod_entidade                                       \n";
 $stSql .= "                        AND CVLC.exercicio     = CLO.exercicio                                          \n";
 $stSql .= "                        AND CCC.exercicio      = '".$this->getDado('exercicio')."'                      \n";
 $stSql .= "                        AND CVLC.cod_entidade  IN( ".$this->getDado('cod_entidade')." )                 \n";
 $stSql .= "                        AND CLO.dt_lote BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' ) \n";
 $stSql .= "                                            AND TO_DATE( '".$this->getDado('dt_final')  ."', 'dd/mm/yyyy' ) \n";
 $stSql .= "                      GROUP BY CCC.cod_plano                                                            \n";
 $stSql .= "                              ,CCC.exercicio                                                            \n";
// $stSql .= "                              ,CLO.dt_lote                                                              \n";
// $stSql .= "                              ,CVLC.tipo                                                                \n";
 $stSql .= "                      ORDER BY CCC.cod_plano                                                            \n";
 $stSql .= "                              ,CCC.exercicio                                                            \n";
// $stSql .= "                              ,CLO.dt_lote                                                              \n";
// $stSql .= "                              ,CVLC.tipo                                                                \n";
 $stSql .= "          ) AS CCC ON ( OCA.cod_plano = CCC.cod_plano                                                   \n";
 $stSql .= "                   AND  OCA.exercicio = CCC.exercicio                                                   \n";
 $stSql .= "          )                                                                                             \n";
 $stSql .= "          WHERE OPC.exercicio = '".$this->getDado('exercicio')."'                                       \n";
 $stSql .= "          AND (OPC.cod_estrutural    like  '1.1.1.1.1%'                                                 \n";
 $stSql .= "           OR  OPC.cod_estrutural    like  '1.1.1.1.2%'                                                 \n";
 $stSql .= "           OR  OPC.cod_estrutural    like  '1.1.1.1.3%'                                                 \n";
 $stSql .= "           OR  OPC.cod_estrutural    like  '1.1.5%'     )                                               \n";
 $stSql .= "          AND CASE WHEN     OPC.cod_estrutural like '1.1.5%'
                                    AND ( CCC.cod_plano is not null OR CCD.cod_plano is not null )                  \n";
 $stSql .= "                   THEN pb.cod_plano is not null                                                        \n";
 $stSql .= "                   ELSE true                                                                            \n";
 $stSql .= "               END                                                                                       \n";
 $stSql .= "          GROUP BY OPC.cod_estrutural                                                                   \n";
 $stSql .= "                  ,OPC.exercicio                                                                        \n";
 $stSql .= "          ORDER BY OPC.cod_estrutural                                                                   \n";
 $stSql .= "                  ,OPC.exercicio                                                                        \n";
 $stSql .= "    ) AS tbl                                                                                            \n";
 $stSql .= "    ,contabilidade.plano_conta AS OCR                                                                   \n";
 $stSql .= "    WHERE tbl.cod_estrutural = substr( OCR.cod_estrutural, 1, 9 )                                       \n";
 $stSql .= "    AND   length( publico.fn_mascarareduzida( OCR.cod_estrutural ) ) <= 9                               \n";
 $stSql .= "    AND   tbl.exercicio      = OCR.exercicio                                                            \n";
 $stSql .= "    GROUP BY tbl.cod_estrutural                                                                         \n";
 $stSql .= "            ,OCR.nom_conta                                                                              \n";
 $stSql .= "    ORDER BY tbl.cod_estrutural                                                                         \n";
 $stSql .= "            ,OCR.nom_conta;                                                                             \n";

 return $stSql;
}

}
