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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63835 $
    $Name$
    $Author: franver $
    $Date: 2015-10-22 11:53:31 -0200 (Thu, 22 Oct 2015) $

    * Casos de uso: uc-06.04.00
*/

/*
$Log$
Revision 1.7  2007/10/10 23:35:33  hboaventura
correção dos arquivos

Revision 1.6  2007/10/05 18:35:14  hboaventura
Correção dos desdobramentos

Revision 1.5  2007/06/14 19:31:03  hboaventura
Correção de bugs

Revision 1.4  2007/06/12 20:44:11  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.3  2007/06/12 18:34:05  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.2  2007/06/07 15:36:31  hboaventura
Inclusão do filtro por periodicidade

Revision 1.1  2007/04/21 20:22:43  hboaventura
Arquivos para geração do TCMGO

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOALQ extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/

    public function recuperaAnulacaoLiquidacaoDespesa(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaAnulacaoRecuperaLiquidacaoDespesa",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaAnulacaoRecuperaLiquidacaoDespesa()
    {
        $stSql = "
        SELECT
                  '10' AS tipo_registro
               ,  pre_empenho.exercicio
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    0
                          ELSE    despesa.num_programa
                  END     AS      cod_programa
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    restos_pre_empenho.num_orgao
                          ELSE    despesa.num_orgao
                  END     AS      cod_orgao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    restos_pre_empenho.num_unidade
                          ELSE    despesa.num_unidade
                  END     AS      num_unidade
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    0
                          ELSE    despesa.cod_funcao
                  END     AS      cod_funcao
               ,  CASE    WHEN    empenho.exercicio = '".$this->getDado('exercicio')."'
                          THEN    despesa.cod_subfuncao
                          ELSE    0
                  END     AS      cod_subfuncao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(despesa.num_acao::varchar,1,'1')
                  END     AS      cod_natureza
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(despesa.num_acao::varchar,2,'3')
                  END     AS      num_pao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(replace(despesa.cod_estrutural,'.',''),1,6)
                  END     AS      elemento_despesa
               , SUBSTR(REPLACE(elemento_de_para.estrutural,'.',''),7,2)   AS      subelemento_despesa
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    LPAD(restos_pre_empenho.num_unidade::varchar,4,'0') ||
                                  LPAD(restos_pre_empenho.cod_funcao::varchar,2,'0')||
                                  LPAD(SUBSTR(restos_pre_empenho.cod_programa::varchar,1,2),2,'0') ||
                                  LPAD(SUBSTR(restos_pre_empenho.cod_programa::varchar,3,3),3,'0') ||
                                  LPAD(restos_pre_empenho.num_pao::varchar,4,'0') ||
                                  LPAD(SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,'.',''),1,6),6,'0')
                          ELSE    '0'
                  END     AS  dotacao_resto
               ,  empenho.cod_empenho
               ,  to_char(empenho.dt_empenho,'dd/mm/yyyy') as dt_empenho
               ,  TCMGO.numero_nota_liquidacao('".$this->getDado('exercicio')."',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho)  AS  numeroliquidacao
               ,  to_char(nota_liquidacao.dt_liquidacao,'dd/mm/yyyy') as dt_liquidacao
               ,  TCMGO.numero_anulacao_liquidacao('".$this->getDado('exercicio')."',pre_empenho.exercicio,empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao_item_anulado.timestamp)  AS  numero_anulacao
               ,  to_char(nota_liquidacao_item_anulado.timestamp,'dd/mm/yyyy') as dt_anulacao
               ,  CASE    WHEN    empenho.exercicio = '".$this->getDado('exercicio')."'
                          THEN    1
                          ELSE    2
                  END     AS tipo_liquidacao

               ,  LPAD(REPLACE((   SELECT  SUM(vl_total)
                        FROM  empenho.nota_liquidacao_item
                       WHERE  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                         AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                         AND nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
                  )::varchar,'.',','),13,'0')   AS    vl_liquidado
               ,  LPAD(REPLACE(SUM(nota_liquidacao_item_anulado.vl_anulado)::varchar,'.',','),13,'0') as vl_anulado
               ,  0  AS  numero_sequencial
            FROM  empenho.empenho
      INNER JOIN  empenho.nota_liquidacao
              ON  nota_liquidacao.exercicio = empenho.exercicio
             AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
             AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
      INNER JOIN  empenho.nota_liquidacao_item_anulado
              ON  nota_liquidacao_item_anulado.exercicio = nota_liquidacao.exercicio
             AND  nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao.cod_entidade
             AND  nota_liquidacao_item_anulado.cod_nota = nota_liquidacao.cod_nota
      INNER JOIN  empenho.pre_empenho
              ON  pre_empenho.exercicio = empenho.exercicio
             AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
       LEFT JOIN  empenho.restos_pre_empenho
              ON  restos_pre_empenho.exercicio = pre_empenho.exercicio
             AND  restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
       LEFT JOIN  (       SELECT  despesa.*
                               ,  conta_despesa.cod_estrutural
                               ,  pre_empenho_despesa.cod_pre_empenho
                               ,  acao.num_acao
                               ,  programa.num_programa
                            FROM  empenho.pre_empenho_despesa
                      INNER JOIN  orcamento.despesa
                              ON  despesa.exercicio = pre_empenho_despesa.exercicio
                             AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                      INNER JOIN  orcamento.conta_despesa
                              on  conta_despesa.exercicio = despesa.exercicio
                             and  conta_despesa.cod_conta = despesa.cod_conta
                            JOIN  orcamento.despesa_acao
                              ON despesa_acao.exercicio_despesa = despesa.exercicio
                             AND despesa_acao.cod_despesa = despesa.cod_despesa
                            JOIN ppa.acao
                              ON acao.cod_acao = despesa_acao.cod_acao
                            JOIN ppa.programa
                              ON programa.cod_programa = acao.cod_programa
                  )   AS  despesa
              ON  despesa.exercicio = pre_empenho.exercicio
             AND  despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
       LEFT JOIN  (       SELECT  conta_despesa.cod_estrutural
                               ,  conta_despesa.cod_conta
                               ,  conta_despesa.exercicio
                               ,  pre_empenho_despesa.cod_pre_empenho
                            FROM  empenho.pre_empenho_despesa
                      INNER JOIN  orcamento.conta_despesa
                              on  conta_despesa.exercicio = pre_empenho_despesa.exercicio
                             and  conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                  )   AS  despesa_sub_elemento
              ON  despesa_sub_elemento.exercicio = pre_empenho.exercicio
             AND  despesa_sub_elemento.cod_pre_empenho = pre_empenho.cod_pre_empenho
       LEFT JOIN  tcmgo.elemento_de_para
              ON  elemento_de_para.exercicio = despesa_sub_elemento.exercicio
             AND  elemento_de_para.cod_conta = despesa_sub_elemento.cod_conta
           WHERE  TO_DATE(nota_liquidacao_item_anulado.timestamp::varchar, 'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
             AND  nota_liquidacao_item_anulado.cod_entidade IN (".$this->getDado('cod_entidade').")
         GROUP BY  pre_empenho.exercicio
               ,  pre_empenho.implantado
               ,  despesa.num_programa
               ,  despesa.num_orgao
               ,  despesa.num_pao
               ,  restos_pre_empenho.num_orgao
               ,  restos_pre_empenho.num_unidade
               ,  despesa.num_unidade
               ,  despesa.cod_funcao
               ,  empenho.exercicio
               ,  despesa.cod_subfuncao
               ,  despesa.num_acao
               ,  despesa.cod_estrutural
               ,  restos_pre_empenho.cod_funcao
               ,  restos_pre_empenho.cod_programa
               ,  restos_pre_empenho.num_pao
               ,  restos_pre_empenho.cod_estrutural
               ,  empenho.cod_empenho
               ,  empenho.dt_empenho
               ,  TCMGO.numero_nota_liquidacao('2007',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho)
               ,  nota_liquidacao.dt_liquidacao
               ,  empenho.cod_entidade
               ,  nota_liquidacao.cod_nota
               ,  nota_liquidacao.exercicio_empenho
               ,  nota_liquidacao_item_anulado.timestamp
               ,  nota_liquidacao.exercicio
               ,  nota_liquidacao.cod_entidade
               ,  elemento_de_para.estrutural
        --ORDER BY  nota_liquidacao_item_anulado.timestamp
        ORDER BY  nota_liquidacao.exercicio, nota_liquidacao.cod_entidade, nota_liquidacao.cod_nota;
    ";

        return $stSql;
    }

    public function recuperaAnulacaoLiquidacaoDespesaFR(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaAnulacaoRecuperaLiquidacaoDespesaFR",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaAnulacaoRecuperaLiquidacaoDespesaFR()
    {
        $stSql = "
        SELECT
                  '11' AS tipo_registro
               ,  pre_empenho.exercicio
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    0
                          ELSE    despesa.num_programa
                  END     AS      cod_programa
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    restos_pre_empenho.num_orgao
                          ELSE    despesa.num_orgao
                  END     AS      cod_orgao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    restos_pre_empenho.num_unidade
                          ELSE    despesa.num_unidade
                  END     AS      num_unidade
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    0
                          ELSE    despesa.cod_funcao
                  END     AS      cod_funcao
               ,  CASE    WHEN    empenho.exercicio = '".$this->getDado('exercicio')."'
                          THEN    despesa.cod_subfuncao
                          ELSE    0
                  END     AS      cod_subfuncao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(despesa.num_acao::varchar,1,1)
                  END     AS      cod_natureza
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(despesa.num_acao::varchar,2,3)
                  END     AS      num_pao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(replace(despesa.cod_estrutural,'.',''),1,6)
                  END     AS      elemento_despesa
               , SUBSTR(REPLACE(elemento_de_para.estrutural,'.',''),7,2)   AS      subelemento_despesa
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    LPAD(restos_pre_empenho.num_unidade::varchar,4,'0') ||
                                  LPAD(restos_pre_empenho.cod_funcao::varchar,2,'0')||
                                  LPAD(SUBSTR(restos_pre_empenho.cod_programa::varchar,1,2),2,'0') ||
                                  LPAD(SUBSTR(restos_pre_empenho.cod_programa::varchar,3,3),3,'0') ||
                                  LPAD(restos_pre_empenho.num_pao::varchar,4,'0') ||
                                  LPAD(SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural::varchar,'.',''),1,6),6,'0')
                          ELSE    '0'
                  END     AS  dotacao_resto
               ,  empenho.cod_empenho
               ,  to_char(empenho.dt_empenho,'dd/mm/yyyy') as dt_empenho
               ,  TCMGO.numero_nota_liquidacao('".$this->getDado('exercicio')."',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho)  AS  numeroliquidacao
               ,  to_char(nota_liquidacao.dt_liquidacao,'dd/mm/yyyy') as dt_liquidacao
               ,  TCMGO.numero_anulacao_liquidacao('".$this->getDado('exercicio')."',pre_empenho.exercicio,empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao_item_anulado.timestamp)  AS  numero_anulacao
               ,  to_char(nota_liquidacao_item_anulado.timestamp,'dd/mm/yyyy') as dt_anulacao
               ,  recurso.cod_fonte AS cod_fonte_recurso

               ,  LPAD(REPLACE((   SELECT  SUM(vl_total)
                        FROM  empenho.nota_liquidacao_item
                       WHERE  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                         AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                         AND nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
                  )::varchar,'.',','),13,'0')   AS    vl_liquidadofr
               ,  LPAD(REPLACE(SUM(nota_liquidacao_item_anulado.vl_anulado)::varchar,'.',','),13,'0') as vl_anuladofr
               , '' AS brancos
               ,  0  AS  numero_sequencial
            FROM  empenho.empenho
      INNER JOIN  empenho.nota_liquidacao
              ON  nota_liquidacao.exercicio = empenho.exercicio
             AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
             AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
      INNER JOIN  empenho.nota_liquidacao_item_anulado
              ON  nota_liquidacao_item_anulado.exercicio = nota_liquidacao.exercicio
             AND  nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao.cod_entidade
             AND  nota_liquidacao_item_anulado.cod_nota = nota_liquidacao.cod_nota
      INNER JOIN  empenho.pre_empenho
              ON  pre_empenho.exercicio = empenho.exercicio
             AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
       LEFT JOIN  empenho.restos_pre_empenho
              ON  restos_pre_empenho.exercicio = pre_empenho.exercicio
             AND  restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
       LEFT JOIN  (       SELECT  despesa.*
                               ,  conta_despesa.cod_estrutural
                               ,  pre_empenho_despesa.cod_pre_empenho
                               ,  acao.num_acao
                               ,  programa.num_programa
                            FROM  empenho.pre_empenho_despesa
                      INNER JOIN  orcamento.despesa
                              ON  despesa.exercicio = pre_empenho_despesa.exercicio
                             AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                      INNER JOIN  orcamento.conta_despesa
                              on  conta_despesa.exercicio = despesa.exercicio
                             and  conta_despesa.cod_conta = despesa.cod_conta
                            JOIN  orcamento.despesa_acao
                              ON despesa_acao.exercicio_despesa = despesa.exercicio
                             AND despesa_acao.cod_despesa = despesa.cod_despesa
                            JOIN ppa.acao
                              ON acao.cod_acao = despesa_acao.cod_acao
                            JOIN ppa.programa
                              ON programa.cod_programa = acao.cod_programa
                  )   AS  despesa
              ON  despesa.exercicio = pre_empenho.exercicio
             AND  despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

       LEFT JOIN  (       SELECT  conta_despesa.cod_estrutural
                               ,  conta_despesa.cod_conta
                               ,  conta_despesa.exercicio
                               ,  pre_empenho_despesa.cod_pre_empenho
                            FROM  empenho.pre_empenho_despesa
                      INNER JOIN  orcamento.conta_despesa
                              on  conta_despesa.exercicio = pre_empenho_despesa.exercicio
                             and  conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                  )   AS  despesa_sub_elemento
              ON  despesa_sub_elemento.exercicio = pre_empenho.exercicio
             AND  despesa_sub_elemento.cod_pre_empenho = pre_empenho.cod_pre_empenho

      INNER JOIN  orcamento.recurso
              ON  recurso.exercicio = despesa.exercicio
             AND  recurso.cod_recurso = despesa.cod_recurso
       LEFT JOIN  tcmgo.elemento_de_para
              ON  elemento_de_para.exercicio = despesa_sub_elemento.exercicio
             AND  elemento_de_para.cod_conta = despesa_sub_elemento.cod_conta
           WHERE  TO_DATE(nota_liquidacao_item_anulado.timestamp::varchar, 'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
             AND  nota_liquidacao_item_anulado.cod_entidade IN (".$this->getDado('cod_entidade').")
         GROUP BY  pre_empenho.exercicio
               ,  pre_empenho.implantado
               ,  despesa.num_programa
               ,  despesa.num_orgao
               ,  despesa.num_pao
               ,  restos_pre_empenho.num_orgao
               ,  restos_pre_empenho.num_unidade
               ,  despesa.num_unidade
               ,  despesa.cod_funcao
               ,  empenho.exercicio
               ,  despesa.cod_subfuncao
               ,  despesa.num_acao
               ,  despesa.cod_estrutural
               ,  restos_pre_empenho.cod_funcao
               ,  restos_pre_empenho.cod_programa
               ,  restos_pre_empenho.num_pao
               ,  restos_pre_empenho.cod_estrutural
               ,  empenho.cod_empenho
               ,  empenho.dt_empenho
               ,  TCMGO.numero_nota_liquidacao('2007',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho)
               ,  nota_liquidacao.dt_liquidacao
               ,  empenho.cod_entidade
               ,  recurso.cod_fonte
               ,  nota_liquidacao.cod_nota
               ,  nota_liquidacao.exercicio_empenho
               ,  nota_liquidacao_item_anulado.timestamp
               ,  nota_liquidacao.exercicio
               ,  nota_liquidacao.cod_entidade
               ,  elemento_de_para.estrutural
        --ORDER BY  nota_liquidacao_item_anulado.timestamp
        ORDER BY  nota_liquidacao.exercicio, nota_liquidacao.cod_entidade, nota_liquidacao.cod_nota;
    ";

        return $stSql;
    }

    public function recuperaAnulacaoDocumentosFiscais(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaAnulacaoDocumentosFiscais",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaAnulacaoDocumentosFiscais()
    {
        $stSql = "
        SELECT
                  '12' AS tipo_registro
               ,  pre_empenho.exercicio
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    0
                          ELSE    despesa.num_programa
                  END     AS      cod_programa
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    restos_pre_empenho.num_orgao
                          ELSE    despesa.num_orgao
                  END     AS      cod_orgao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    restos_pre_empenho.num_unidade
                          ELSE    despesa.num_unidade
                  END     AS      num_unidade
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    0
                          ELSE    despesa.cod_funcao
                  END     AS      cod_funcao
               ,  CASE    WHEN    empenho.exercicio = '".$this->getDado('exercicio')."'
                          THEN    despesa.cod_subfuncao
                          ELSE    0
                  END     AS      cod_subfuncao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(despesa.num_acao::varchar,1,1)
                  END     AS      cod_natureza
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(despesa.num_acao::varchar,2,3)
                  END     AS      num_pao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(replace(despesa.cod_estrutural::varchar,'.',''),1,6)
                  END     AS      elemento_despesa
               , SUBSTR(REPLACE(elemento_de_para.estrutural::varchar,'.',''),7,2)   AS      subelemento_despesa
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    LPAD(restos_pre_empenho.num_unidade::varchar,4,'0') ||
                                  LPAD(restos_pre_empenho.cod_funcao::varchar,2,'0')||
                                  LPAD(SUBSTR(restos_pre_empenho.cod_programa::varchar,1,2),2,'0') ||
                                  LPAD(SUBSTR(restos_pre_empenho.cod_programa::varchar,3,3),3,'0') ||
                                  LPAD(restos_pre_empenho.num_pao::varchar,4,'0') ||
                                  LPAD(SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural::varchar,'.',''),1,6),6,'0')
                          ELSE    '0'
                  END     AS  dotacao_resto
               ,  empenho.cod_empenho
               ,  to_char(empenho.dt_empenho,'dd/mm/yyyy') as dt_empenho
               ,  TCMGO.numero_nota_liquidacao('".$this->getDado('exercicio')."',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho)  AS  numeroliquidacao
               ,  to_char(nota_liquidacao.dt_liquidacao,'dd/mm/yyyy') as dt_liquidacao
               ,  TCMGO.numero_anulacao_liquidacao('".$this->getDado('exercicio')."',pre_empenho.exercicio,empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao_item_anulado.timestamp)  AS  numero_anulacao
               ,  to_char(nota_liquidacao_item_anulado.timestamp,'dd/mm/yyyy') as dt_anulacao
               ,  nota_fiscal.cod_tipo AS tipo_docfiscal
               ,  nota_fiscal.nro_nota AS num_docfiscal
               ,  nota_fiscal.nro_serie AS serie_docfiscal
               ,  to_char(nota_fiscal.data_emissao, 'dd/mm/yyyy') AS dt_docfiscal
               ,  LPAD(REPLACE(SUM(nota_liquidacao_item_anulado.vl_anulado)::varchar,'.',','),13,'0') as vl_anulado
               ,  CASE    WHEN    sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                          THEN    sw_cgm_pessoa_juridica.cnpj
                          ELSE    sw_cgm_pessoa_fisica.cpf
                  END     AS  cnpj_cpf ";
      if (Sessao::getExercicio() > 2012) {
        $stSql .= "
                 ,  CASE    WHEN    sw_cgm.cod_pais <> 1
                            THEN    3
                            WHEN    sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                            THEN    2
                            ELSE    1
                    END AS tipo_credor ";
      } else {
        $stSql .= "
                 ,  CASE    WHEN    sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                            THEN    2
                            ELSE    1
                    END AS tipo_credor ";
      }
      $stSql .= "
               ,  nota_fiscal.inscricao_estadual AS num_inscest
               ,  nota_fiscal.inscricao_municipal AS num_inscmun
               ,  sw_cgm.cep AS cep_municipio
               ,  sw_cgm.cod_uf AS uf_credor
               ,  sw_cgm.nom_cgm AS nom_credor
               ,  0  AS  numero_sequencial
            FROM  empenho.empenho
      INNER JOIN  empenho.nota_liquidacao
              ON  nota_liquidacao.exercicio = empenho.exercicio
             AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
             AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
      INNER JOIN  empenho.nota_liquidacao_item_anulado
              ON  nota_liquidacao_item_anulado.exercicio = nota_liquidacao.exercicio
             AND  nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao.cod_entidade
             AND  nota_liquidacao_item_anulado.cod_nota = nota_liquidacao.cod_nota
      INNER JOIN  empenho.pre_empenho
              ON  pre_empenho.exercicio = empenho.exercicio
             AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
      INNER JOIN  sw_cgm
              ON  sw_cgm.numcgm = pre_empenho.cgm_beneficiario
       LEFT JOIN  sw_cgm_pessoa_fisica
              ON  sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
       LEFT JOIN  sw_cgm_pessoa_juridica
              ON  sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
       LEFT JOIN  empenho.restos_pre_empenho
              ON  restos_pre_empenho.exercicio = pre_empenho.exercicio
             AND  restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
       LEFT JOIN  (       SELECT  despesa.*
                               ,  conta_despesa.cod_estrutural
                               ,  pre_empenho_despesa.cod_pre_empenho
                               ,  acao.num_acao
                               ,  programa.num_programa
                            FROM  empenho.pre_empenho_despesa
                      INNER JOIN  orcamento.despesa
                              ON  despesa.exercicio = pre_empenho_despesa.exercicio
                             AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                      INNER JOIN  orcamento.conta_despesa
                              on  conta_despesa.exercicio = despesa.exercicio
                             and  conta_despesa.cod_conta = despesa.cod_conta
                            JOIN  orcamento.despesa_acao
                              ON despesa_acao.exercicio_despesa = despesa.exercicio
                             AND despesa_acao.cod_despesa = despesa.cod_despesa
                            JOIN ppa.acao
                              ON acao.cod_acao = despesa_acao.cod_acao
                            JOIN ppa.programa
                              ON programa.cod_programa = acao.cod_programa
                  )   AS  despesa
              ON  despesa.exercicio = pre_empenho.exercicio
             AND  despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

       LEFT JOIN  (       SELECT  conta_despesa.cod_estrutural
                               ,  conta_despesa.cod_conta
                               ,  conta_despesa.exercicio
                               ,  pre_empenho_despesa.cod_pre_empenho
                            FROM  empenho.pre_empenho_despesa
                      INNER JOIN  orcamento.conta_despesa
                              on  conta_despesa.exercicio = pre_empenho_despesa.exercicio
                             and  conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                  )   AS  despesa_sub_elemento
              ON  despesa_sub_elemento.exercicio = pre_empenho.exercicio
             AND  despesa_sub_elemento.cod_pre_empenho = pre_empenho.cod_pre_empenho

        INNER JOIN  tcmgo.nota_fiscal_empenho_liquidacao
              ON  nota_fiscal_empenho_liquidacao.exercicio_liquidacao = nota_liquidacao.exercicio
             AND  nota_fiscal_empenho_liquidacao.cod_entidade = nota_liquidacao.cod_entidade
             AND  nota_fiscal_empenho_liquidacao.cod_nota_liquidacao = nota_liquidacao.cod_nota
        INNER JOIN  tcmgo.nota_fiscal
              ON  nota_fiscal.cod_nota = nota_fiscal_empenho_liquidacao.cod_nota
       LEFT JOIN  tcmgo.elemento_de_para
              ON  elemento_de_para.exercicio = despesa_sub_elemento.exercicio
             AND  elemento_de_para.cod_conta = despesa_sub_elemento.cod_conta
           WHERE  TO_DATE(nota_liquidacao_item_anulado.timestamp::varchar, 'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
             AND  TO_DATE(nota_fiscal.data_emissao::varchar, 'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
             AND  nota_liquidacao_item_anulado.cod_entidade IN (".$this->getDado('cod_entidade').")
         GROUP BY  pre_empenho.exercicio
               ,  pre_empenho.implantado
               ,  despesa.num_programa
               ,  despesa.num_orgao
               ,  despesa.num_pao
               ,  restos_pre_empenho.num_orgao
               ,  restos_pre_empenho.num_unidade
               ,  despesa.num_unidade
               ,  despesa.cod_funcao
               ,  empenho.exercicio
               ,  despesa.cod_subfuncao
               ,  despesa.num_acao
               ,  despesa.cod_estrutural
               ,  restos_pre_empenho.cod_funcao
               ,  restos_pre_empenho.cod_programa
               ,  restos_pre_empenho.num_pao
               ,  restos_pre_empenho.cod_estrutural
               ,  empenho.cod_empenho
               ,  empenho.dt_empenho
               ,  TCMGO.numero_nota_liquidacao('2007',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho)
               ,  nota_liquidacao.dt_liquidacao
               ,  empenho.cod_entidade
               ,  nota_liquidacao.cod_nota
               ,  nota_liquidacao.exercicio_empenho
               ,  nota_liquidacao_item_anulado.timestamp
               ,  nota_liquidacao.exercicio
               ,  nota_liquidacao.cod_entidade
               ,  nota_fiscal.cod_tipo
               ,  nota_fiscal.nro_nota
               ,  nota_fiscal.nro_serie
               ,  nota_fiscal.data_emissao
               ,  pre_empenho.cod_tipo
               ,  nota_fiscal.inscricao_estadual
               ,  nota_fiscal.inscricao_municipal
               ,  sw_cgm.cep
               ,  sw_cgm.cod_uf
               ,  sw_cgm.nom_cgm
               ,  sw_cgm_pessoa_juridica.cnpj
               ,  sw_cgm_pessoa_fisica.cpf
               ,  elemento_de_para.estrutural
               ,  tipo_docfiscal
               ,  num_docfiscal
               ,  serie_docfiscal ";
      if (Sessao::getExercicio() > 2012) {
        $stSql .= " ,  sw_cgm.cod_pais ";
      }
      $stSql .= "
        --ORDER BY  nota_liquidacao_item_anulado.timestamp
        ORDER BY  nota_liquidacao.exercicio, nota_liquidacao.cod_entidade, nota_liquidacao.cod_nota;
    ";

        return $stSql;
    }
}
