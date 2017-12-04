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
    * Data de Criação: 30/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTPBEmpenho.class.php 29242 2008-04-16 12:37:41Z hboaventura $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 22/01/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTPBEmpenho extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBEmpenho()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaEmpenhos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaEmpenhos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEmpenhos()
{
    $stSql = "
         SELECT  despesa.exercicio
              ,  LPAD(despesa.num_orgao,2,'0') || LPAD(despesa.num_unidade,2,'0') as unidade
              ,  despesa.cod_funcao
              ,  despesa.cod_subfuncao
              ,  despesa.cod_programa
              ,  despesa.num_pao
              ,  SUBSTR(replace(estrutural.cod_estrutural,'.',''),1,1) as categoria_economica
              ,  SUBSTR(replace(estrutural.cod_estrutural,'.',''),2,1) as natureza
              ,  SUBSTR(replace(estrutural.cod_estrutural,'.',''),3,2) as modalidade
              ,  SUBSTR(replace(estrutural.cod_estrutural,'.',''),5,2) as elemento
              ,  SUBSTR(replace(estrutural.cod_estrutural,'.',''),7,2) as subelemento
              ,  empenho.cod_empenho
              ,  pre_empenho.cod_tipo
              ,  CASE WHEN pre_empenho.cod_tipo = 2 THEN 3
                      WHEN pre_empenho.cod_tipo = 3 THEN 2
                      WHEN pre_empenho.cod_tipo = 1 THEN 1
                 END as tipo_empenho
              ,  TO_CHAR(empenho.dt_empenho,'dd/mm/yyyy') as dt_empenho
              ,  LPAD(TRIM(TRANSLATE(TO_CHAR(item_pre_empenho.valor_empenhado,'999,999,990.99' ),',.', '.,')),16,'0') as valor_empenhado
              ,  item_pre_empenho.valor_empenhado as valor_empenhado_nao_formato
              ,  pre_empenho.descricao as historico
              ,  'sem complemento' as complemento_historico
              ,  7 as tipo_meta
              ,  CASE WHEN  sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN LPAD(sw_cgm_pessoa_fisica.cpf,14,'0')
                      WHEN  sw_cgm_pessoa_juridica.cnpj IS NOT NULL THEN LPAD(sw_cgm_pessoa_juridica.cnpj,14,'0')
                      ELSE  ''
                 END as cpf_cnpj
           FROM  empenho.empenho
     INNER JOIN  empenho.pre_empenho
             ON  pre_empenho.exercicio = empenho.exercicio
            AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
     INNER JOIN  sw_cgm
             ON  sw_cgm.numcgm = pre_empenho.cgm_beneficiario
      LEFT JOIN  sw_cgm_pessoa_fisica
             ON  sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
      LEFT JOIN  sw_cgm_pessoa_juridica
             ON  sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

     INNER JOIN  ( SELECT  exercicio
                        ,  cod_pre_empenho
                        ,  SUM(vl_total) as valor_empenhado
                     FROM  empenho.item_pre_empenho
                 GROUP BY  exercicio, cod_pre_empenho
                 ) AS item_pre_empenho
             ON  item_pre_empenho.exercicio = pre_empenho.exercicio
            AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
     INNER JOIN  empenho.pre_empenho_despesa
             ON  pre_empenho_despesa.exercicio = pre_empenho.exercicio
            AND  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
     INNER JOIN  orcamento.despesa
             ON  despesa.exercicio = pre_empenho_despesa.exercicio
            AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
     INNER JOIN  orcamento.conta_despesa
             ON  conta_despesa.exercicio = despesa.exercicio
            AND  conta_despesa.cod_conta = despesa.cod_conta

     INNER JOIN  orcamento.conta_despesa AS estrutural
             ON  estrutural.cod_conta  = pre_empenho_despesa.cod_conta
            AND  estrutural.exercicio = pre_empenho_despesa.exercicio

          WHERE  despesa.exercicio = '".$this->getDado('exercicio')."'
            AND  TO_CHAR(empenho.dt_empenho,'mm') = '".$this->getDado('inMes')."'
    ";
    if ( $this->getDado('stEntidades') ) {
        $stSql .= " AND despesa.cod_entidade in (".$this->getDado('stEntidades').") ";
    }
    $stSql .= " ORDER BY conta_despesa.exercicio, empenho.cod_empenho  \n";

    return $stSql;
}

function recuperaEstornos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaEstornos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEstornos()
{
    $stSQL  = " SELECT   to_char(ean.timestamp,'yyyy') as exercicio  \n";
    $stSQL .= "         ,lpad(desp.num_orgao, 2, '0')||lpad(desp.num_unidade, 2, '0') as unidade  \n";
    $stSQL .= "         ,emp.cod_empenho  \n";
    $stSQL .= "         ,substr(ean.oid,length(ean.oid)-6,7) as numero_empenho_anulado  \n";
    $stSQL .= "         ,to_char(ean.timestamp,'dd/mm/yyyy') as data_anulacao  \n";
    $stSQL .= "         ,sume.valor_anulado  \n";
    $stSQL .= "         ,case when liq.cod_entidade is not null then 'S' else  'N' end as foi_liquidada   \n";
    $stSQL .= "         ,'Anulação do Empenho Nro: '||emp.cod_empenho||' de '||to_char(emp.dt_empenho,'dd/mm/yyyy') as motivo    \n";
    $stSQL .= " FROM     empenho.empenho            as emp  \n";
    $stSQL .= "         LEFT JOIN  \n";
    $stSQL .= "                    ( SELECT  exercicio_empenho  \n";
    $stSQL .= "                             ,cod_entidade  \n";
    $stSQL .= "                             ,cod_empenho  \n";
    $stSQL .= "                     FROM    empenho.nota_liquidacao as liq  \n";
    $stSQL .= "                     WHERE   exercicio = '".$this->getDado('exercicio')."'   \n";
    if ( $this->getDado('stEntidades') ) {
        $stSQL .= "                 AND     cod_entidade in (".$this->getDado('stEntidades').")  \n";
    }
    $stSQL .= "                     GROUP BY exercicio_empenho, cod_entidade, cod_empenho  \n";
    $stSQL .= "                     ) as liq  \n";
    $stSQL .= "                   ON (  \n";
    $stSQL .= "                         emp.exercicio    = liq.exercicio_empenho  \n";
    $stSQL .= "                     AND emp.cod_entidade = liq.cod_entidade  \n";
    $stSQL .= "                     AND emp.cod_empenho  = liq.cod_empenho  \n";
    $stSQL .= "                     )  \n";
    $stSQL .= "         ,empenho.empenho_anulado    as ean  \n";
    $stSQL .= "         ,empenho.pre_empenho        as pre  \n";
    $stSQL .= "         ,(  \n";
    $stSQL .= "             SELECT   exercicio  \n";
    $stSQL .= "                     ,cod_entidade  \n";
    $stSQL .= "                     ,cod_empenho  \n";
    $stSQL .= "                     ,timestamp  \n";
    $stSQL .= "                     ,sum(vl_anulado) as valor_anulado  \n";
    $stSQL .= "             FROM    empenho.empenho_anulado_item as ipe  \n";
    $stSQL .= "             WHERE   exercicio = '".$this->getDado('exercicio')."'   \n";
    if ( $this->getDado('exercicio') ) {
        $stSQL .= "         AND   cod_entidade in ( ".$this->getDado('stEntidades').")   \n";
    }
    $stSQL .= "             GROUP BY exercicio, cod_entidade, cod_empenho, timestamp  \n";
    $stSQL .= "         ) as sume  \n";
    $stSQL .= "         ,empenho.pre_empenho_despesa as pred  \n";
    $stSQL .= "         ,orcamento.despesa          as desp  \n";
    $stSQL .= "         ,orcamento.conta_despesa    as cont  \n";
    $stSQL .= " WHERE   emp.exercicio       =  pre.exercicio  \n";
    $stSQL .= " AND     emp.cod_pre_empenho = pre.cod_pre_empenho  \n";
    $stSQL .= " AND     emp.exercicio       = ean.exercicio  \n";
    $stSQL .= " AND     emp.cod_entidade    = ean.cod_entidade  \n";
    $stSQL .= " AND     emp.cod_empenho     = ean.cod_empenho  \n";
    $stSQL .= " AND     pre.exercicio       = pred.exercicio  \n";
    $stSQL .= " AND     pre.cod_pre_empenho = pred.cod_pre_empenho  \n";
    $stSQL .= " AND     pred.exercicio      = desp.exercicio  \n";
    $stSQL .= " AND     pred.cod_despesa    = desp.cod_despesa  \n";
    $stSQL .= " AND     desp.exercicio      = cont.exercicio  \n";
    $stSQL .= " AND     desp.cod_conta      = cont.cod_conta  \n";
    $stSQL .= " AND     ean.exercicio       = sume.exercicio  \n";
    $stSQL .= " AND     ean.cod_entidade    = sume.cod_entidade  \n";
    $stSQL .= " AND     ean.cod_empenho     = sume.cod_empenho  \n";
    $stSQL .= " AND     ean.timestamp       = sume.timestamp  \n";
    $stSQL .= " AND     to_char(ean.timestamp,'yyyy') = '".$this->getDado('exercicio')."'   \n";
    if ( $this->getDado('stEntidades') ) {
        $stSQL .= " AND     ean.cod_entidade in (".$this->getDado('stEntidades').")  \n";
    }
    $stSQL .= " ORDER BY desp.num_orgao, desp.num_unidade, emp.cod_empenho, ean.oid   \n";

    return $stSQL;

}
}
