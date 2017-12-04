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

    $Id: TTGOLQD.class.php 65220 2016-05-03 21:30:22Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTGOLQD extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/

    public function recuperaLiquidacaoDespesa(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaLiquidacaoDespesa",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaLiquidacaoDespesa()
    {
        $stSql = "
          SELECT
                  '10' AS tipo_registro
               ,  pre_empenho.exercicio
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    0
                          ELSE    programa.num_programa
                  END     AS      codprograma
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    restos_pre_empenho.num_orgao
                          ELSE    despesa.num_orgao
                  END     AS      codorgao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    restos_pre_empenho.num_unidade
                          ELSE    despesa.num_unidade
                  END     AS      numunidade
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    0
                          ELSE    despesa.cod_funcao
                  END     AS      codfuncao
               ,  CASE    WHEN    empenho.exercicio = '".$this->getDado('exercicio')."'
                          THEN    despesa.cod_subfuncao
                          ELSE    0
                  END     AS      codsubfuncao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(acao.num_acao::varchar,1,1)
                  END     AS      codnatureza
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(acao.num_acao::varchar,2,3)
                  END     AS      numpao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(replace(conta_despesa.cod_estrutural::varchar,'.',''),1,6)
                  END     AS      elementodespesa
               ,  substr(replace(elemento_de_para.estrutural::varchar,'.',''),7,2) AS subelementodespesa
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    LPAD(restos_pre_empenho.num_unidade::varchar,4,'0') ||
                                  LPAD(restos_pre_empenho.cod_funcao::varchar,2,'0')||
                                  LPAD(SUBSTR(restos_pre_empenho.cod_programa::varchar,1,2),2,'0') ||
                                  LPAD(SUBSTR(restos_pre_empenho.cod_programa::varchar,3,3),3,'0') ||
                                  LPAD(restos_pre_empenho.num_pao::varchar,4,'0') ||
                                  LPAD(SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural::varchar,'.',''),1,6),6,'0')
                          ELSE    '0'
                  END     AS  dotacaoresto
               ,  empenho.cod_empenho
               ,  to_char(empenho.dt_empenho,'dd/mm/yyyy') as dtempenho
               ,  to_char(nota_liquidacao.dt_liquidacao,'dd/mm/yyyy') as dtliquidacao
               ,  TCMGO.numero_nota_liquidacao('".$this->getDado('exercicio')."',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho)  AS  numeroliquidacao
               ,  CASE    WHEN    empenho.exercicio = '".$this->getDado('exercicio')."'
                          THEN    1
                          ELSE    2
                  END     AS tipoliquidacao
            --   ,  nota_liquidacao.observacao as especificacaoliquidacao
                  , 'LIQUIDACAO CFE EMPENHO' as especificacaoliquidacao
               ,  LPAD(REPLACE(SUM(nota_liquidacao_item.vl_total)::varchar,'.',','),13,'0') AS vl_liquidado
               ,  'Fabio Oliveira de Lima' AS nom_contador

                      ,'41880854104' AS cpf_resp
                      , recurso.cod_fonte AS codFonteRecurso
               , despesa.vl_original AS vlDespFR
               , '' AS branco
               , 0  AS  numero_sequencial
               , nota_liquidacao.cod_nota
               
            FROM  empenho.empenho
            
      INNER JOIN  empenho.nota_liquidacao
              ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
             AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
             AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
             
      INNER JOIN  empenho.nota_liquidacao_item
              ON  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
             AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
             AND  nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
             
      INNER JOIN  empenho.pre_empenho
              ON  pre_empenho.exercicio = empenho.exercicio
             AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
             
       LEFT JOIN  empenho.restos_pre_empenho
              ON  restos_pre_empenho.exercicio = pre_empenho.exercicio
             AND  restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
             
            JOIN  empenho.pre_empenho_despesa
              ON  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
             AND  pre_empenho_despesa.exercicio = pre_empenho.exercicio
             
            JOIN  orcamento.despesa
              ON  despesa.exercicio = pre_empenho_despesa.exercicio
             AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
             
            JOIN  orcamento.recurso
              ON  despesa.cod_recurso = recurso.cod_recurso
             AND  despesa.exercicio = recurso.exercicio
             
            JOIN  orcamento.conta_despesa
              ON  conta_despesa.exercicio = despesa.exercicio
             AND  conta_despesa.cod_conta = despesa.cod_conta
             
       LEFT JOIN  orcamento.despesa_acao
              ON  despesa_acao.exercicio_despesa = despesa.exercicio
             AND  despesa_acao.cod_despesa = despesa.cod_despesa
             
       LEFT JOIN  ppa.acao
              ON  acao.cod_acao = despesa_acao.cod_acao
              
       LEFT JOIN  ppa.programa
              ON  programa.cod_programa = acao.cod_programa
              
       LEFT JOIN  (       SELECT  conta_despesa.exercicio
                               ,  conta_despesa.cod_estrutural
                               ,  conta_despesa.cod_conta
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
            JOIN  tcmgo.orgao
              ON  orgao.exercicio = empenho.exercicio
            JOIN  sw_cgm
              ON  sw_cgm.numcgm = orgao.numcgm_contador
           WHERE  nota_liquidacao.exercicio = '".$this->getDado('exercicio')."'
             AND  nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
             AND  nota_liquidacao.cod_entidade IN (".$this->getDado('cod_entidade').")
        GROUP BY  pre_empenho.exercicio, codprograma, codorgao, numunidade,  codfuncao, codsubfuncao, codnatureza
               ,  numpao
               ,  elementodespesa
               ,  subelementodespesa
               ,  dotacaoresto
               ,  empenho.cod_empenho
               ,  dtempenho
               ,  dtliquidacao
               ,  TCMGO.numero_nota_liquidacao('".$this->getDado('exercicio')."',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho)
               ,  tipoliquidacao
               ,  especificacaoliquidacao
               ,  empenho.cod_entidade
               ,  nota_liquidacao.exercicio_empenho
               ,  nota_liquidacao.exercicio, nota_liquidacao.cod_entidade, nota_liquidacao.cod_nota
               ,  elemento_de_para.estrutural
               ,  sw_cgm.nom_cgm
               ,  codFonteRecurso
               ,  vlDespFR
               , nota_liquidacao.cod_nota
               , acao.num_acao
        ORDER BY nota_liquidacao.exercicio, nota_liquidacao.cod_entidade, nota_liquidacao.cod_nota
    ";
    return $stSql;
    }

    public function recuperaLiquidacaoDespesaFR(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaLiquidacaoDespesaFR",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaLiquidacaoDespesaFR()
    {
        $stSql = "
          SELECT
                  '11' AS tipo_registro
               ,  pre_empenho.exercicio
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    0
                          ELSE    programa.num_programa
                  END     AS      codprograma
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    restos_pre_empenho.num_orgao
                          ELSE    despesa.num_orgao
                  END     AS      codorgao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    restos_pre_empenho.num_unidade
                          ELSE    despesa.num_unidade
                  END     AS      numunidade
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    0
                          ELSE    despesa.cod_funcao
                  END     AS      codfuncao
               ,  CASE    WHEN    empenho.exercicio = '".$this->getDado('exercicio')."'
                          THEN    despesa.cod_subfuncao
                          ELSE    0
                  END     AS      codsubfuncao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(acao.num_acao::varchar,1,1)
                  END     AS      codnatureza
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(acao.num_acao::varchar,2,3)
                  END     AS      numpao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(replace(conta_despesa.cod_estrutural::varchar,'.',''),1,6)
                  END     AS      elementodespesa
               ,  substr(replace(elemento_de_para.estrutural::varchar,'.',''),7,2) AS subelementodespesa
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    LPAD(restos_pre_empenho.num_unidade::varchar,4,'0') ||
                                  LPAD(restos_pre_empenho.cod_funcao::varchar,2,'0')||
                                  LPAD(SUBSTR(restos_pre_empenho.cod_programa::varchar,1,2),2,'0') ||
                                  LPAD(SUBSTR(restos_pre_empenho.cod_programa::varchar,3,3),3,'0') ||
                                  LPAD(restos_pre_empenho.num_pao::varchar,4,'0') ||
                                  LPAD(SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural::varchar,'.',''),1,6),6,'0')
                          ELSE    '0'
                  END     AS  dotacaoresto
               ,  empenho.cod_empenho
               ,  to_char(empenho.dt_empenho,'dd/mm/yyyy') as dtempenho
               ,  to_char(nota_liquidacao.dt_liquidacao,'dd/mm/yyyy') as dtliquidacao
               ,  TCMGO.numero_nota_liquidacao('".$this->getDado('exercicio')."',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho)  AS  numeroliquidacao
               ,  'Fabio Oliveira de Lima' AS nom_contador
               ,  recurso.cod_fonte AS codfonterecurso
               ,  REPLACE(SUM(nota_liquidacao_item.vl_total)::varchar,'.',',') AS vldespfr
               ,  '' AS branco
               ,  0  AS  numero_sequencial
               , nota_liquidacao.cod_nota
            FROM  empenho.empenho
            
      INNER JOIN  empenho.nota_liquidacao
              ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
             AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
             AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
             
      INNER JOIN  empenho.nota_liquidacao_item
              ON  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
             AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
             AND  nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
             
      INNER JOIN  empenho.pre_empenho
              ON  pre_empenho.exercicio = empenho.exercicio
             AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
             
       LEFT JOIN  empenho.restos_pre_empenho
              ON  restos_pre_empenho.exercicio = pre_empenho.exercicio
             AND  restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
             
       JOIN  empenho.pre_empenho_despesa
              ON  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
             AND  pre_empenho_despesa.exercicio = pre_empenho.exercicio
             
            JOIN  orcamento.despesa
              ON  despesa.exercicio = pre_empenho_despesa.exercicio
             AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
             
            JOIN  orcamento.recurso
              ON  despesa.cod_recurso = recurso.cod_recurso
             AND  despesa.exercicio = recurso.exercicio
             
            JOIN  orcamento.conta_despesa
              ON  conta_despesa.exercicio = despesa.exercicio
             AND  conta_despesa.cod_conta = despesa.cod_conta
             
       LEFT JOIN  orcamento.despesa_acao
              ON  despesa_acao.exercicio_despesa = despesa.exercicio
             AND  despesa_acao.cod_despesa = despesa.cod_despesa
             
       LEFT JOIN  ppa.acao
              ON  acao.cod_acao = despesa_acao.cod_acao
              
       LEFT JOIN  ppa.programa
              ON  programa.cod_programa = acao.cod_programa

       LEFT JOIN  (       SELECT  conta_despesa.exercicio
                               ,  conta_despesa.cod_estrutural
                               ,  conta_despesa.cod_conta
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
             
            JOIN  tcmgo.orgao
              ON  orgao.exercicio = empenho.exercicio
              
            JOIN  sw_cgm
              ON  sw_cgm.numcgm = orgao.numcgm_contador
              
           WHERE  nota_liquidacao.exercicio = '".$this->getDado('exercicio')."'
             AND  nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
             AND  nota_liquidacao.cod_entidade IN (".$this->getDado('cod_entidade').")
             
        GROUP BY  pre_empenho.exercicio, codprograma, codorgao, numunidade,  codfuncao, codsubfuncao, codnatureza
               ,  numpao
               ,  elementodespesa
               ,  subelementodespesa
               ,  dotacaoresto
               ,  empenho.cod_empenho
               ,  dtempenho
               ,  dtliquidacao
               ,  TCMGO.numero_nota_liquidacao('".$this->getDado('exercicio')."',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho)
               ,  empenho.cod_entidade
               ,  nota_liquidacao.exercicio_empenho
               ,  nota_liquidacao.exercicio, nota_liquidacao.cod_entidade, nota_liquidacao.cod_nota
               ,  elemento_de_para.estrutural
               ,  sw_cgm.nom_cgm
               ,  codFonteRecurso
               ,  acao.num_acao
               
       ORDER BY   nota_liquidacao.exercicio
                , nota_liquidacao.cod_entidade
                , nota_liquidacao.cod_nota
    ";
        return $stSql;
    }

    public function recuperaDocumentosFiscais(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDocumentosFiscais",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDocumentosFiscais()
    {
        $stSql = "
          SELECT
                  '12' AS tipo_registro
               ,  pre_empenho.exercicio
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    0
                          ELSE    programa.num_programa
                  END     AS      codprograma
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    restos_pre_empenho.num_orgao
                          ELSE    despesa.num_orgao
                  END     AS      codorgao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    restos_pre_empenho.num_unidade
                          ELSE    despesa.num_unidade
                  END     AS      numunidade
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    0
                          ELSE    despesa.cod_funcao
                  END     AS      codfuncao
               ,  CASE    WHEN    empenho.exercicio = '".$this->getDado('exercicio')."'
                          THEN    despesa.cod_subfuncao
                          ELSE    0
                  END     AS      codsubfuncao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(acao.num_acao::varchar,1,1)
                  END     AS      codnatureza
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(acao.num_acao::varchar,2,3)
                  END     AS      numpao
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    '0'
                          ELSE    substr(replace(conta_despesa.cod_estrutural::varchar,'.',''),1,6)
                  END     AS      elementodespesa
               ,  substr(replace(elemento_de_para.estrutural::varchar,'.',''),7,2) AS subelementodespesa
               ,  CASE    WHEN    pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001'
                          THEN    LPAD(restos_pre_empenho.num_unidade::varchar,4,'0') ||
                                  LPAD(restos_pre_empenho.cod_funcao::varchar,2,'0')||
                                  LPAD(SUBSTR(restos_pre_empenho.cod_programa::varchar,1,2),2,'0') ||
                                  LPAD(SUBSTR(restos_pre_empenho.cod_programa::varchar,3,3),3,'0') ||
                                  LPAD(restos_pre_empenho.num_pao::varchar,4,'0') ||
                                  LPAD(SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural::varchar,'.',''),1,6),6,'0')
                          ELSE    '0'
                  END     AS  dotacaoresto
               ,  empenho.cod_empenho
               ,  to_char(empenho.dt_empenho,'dd/mm/yyyy') as dtempenho
               ,  to_char(nota_liquidacao.dt_liquidacao,'dd/mm/yyyy') AS dtliquidacao
               ,  TCMGO.numero_nota_liquidacao('".$this->getDado('exercicio')."',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho)  AS  numeroliquidacao
               --,  BTRIM(sw_cgm.nom_cgm) AS nom_contador
               , 'Fabio Oliveira de Lima' AS nom_contador
               ,  nota_fiscal.cod_tipo AS tipo_docfiscal
               ,  nota_fiscal.nro_nota AS num_docfiscal
               ,  nota_fiscal.nro_serie AS serie_docfiscal
               ,  to_char(nota_fiscal.data_emissao, 'dd/mm/yyyy') AS dt_docfiscal
               ,  nota_fiscal.chave_acesso AS chaveacesso
               ,  nota_fiscal.vl_nota AS vl_docvltotal
               ,  nota_fiscal_empenho_liquidacao.vl_associado AS vl_docassoc
               ,  CASE WHEN sw_cgm_pessoa_juridica.cnpj is NULL
                       THEN sw_cgm_pessoa_fisica.cpf
                       ELSE sw_cgm_pessoa_juridica.cnpj
                  END AS cnpj_cpf
               ,  CASE WHEN credor.cod_pais <> 1
                       THEN 3
                       WHEN sw_cgm_pessoa_juridica.cnpj is NULL
                       THEN 1
                       ELSE 2
                  END AS tipo_credor
               ,  nota_fiscal.inscricao_estadual AS num_inscest
               ,  nota_fiscal.inscricao_municipal AS num_inscmun
               ,  credor.cep AS cep_municipio
               ,  credor.cod_uf AS uf_credor
               ,  sem_acentos(credor.nom_cgm) AS nom_credor 
               ,  '' AS branco
               ,  0  AS  numero_sequencial
               ,  nota_liquidacao.cod_nota
            FROM  empenho.empenho
            
      INNER JOIN  empenho.nota_liquidacao
              ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
             AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
             AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
             
      INNER JOIN  tcmgo.nota_fiscal_empenho_liquidacao
              ON  nota_fiscal_empenho_liquidacao.exercicio = empenho.exercicio
             AND  nota_fiscal_empenho_liquidacao.cod_entidade = empenho.cod_entidade
             AND  nota_fiscal_empenho_liquidacao.cod_empenho = empenho.cod_empenho
             
      INNER JOIN  tcmgo.nota_fiscal
              ON  nota_fiscal.cod_nota = nota_fiscal_empenho_liquidacao.cod_nota
              
      INNER JOIN  empenho.nota_liquidacao_item
              ON  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
             AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
             AND  nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
             AND  nota_fiscal_empenho_liquidacao.exercicio = nota_liquidacao.exercicio
             AND  nota_fiscal_empenho_liquidacao.cod_entidade = nota_liquidacao.cod_entidade
             AND  nota_fiscal_empenho_liquidacao.cod_nota_liquidacao = nota_liquidacao.cod_nota
             
      INNER JOIN  empenho.pre_empenho
              ON  pre_empenho.exercicio = empenho.exercicio
             AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
             
       LEFT JOIN  empenho.restos_pre_empenho
              ON  restos_pre_empenho.exercicio = pre_empenho.exercicio
             AND  restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
             
       JOIN  empenho.pre_empenho_despesa
              ON  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
             AND  pre_empenho_despesa.exercicio = pre_empenho.exercicio
             
            JOIN  orcamento.despesa
              ON  despesa.exercicio = pre_empenho_despesa.exercicio
             AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
             
            JOIN  orcamento.recurso
              ON  despesa.cod_recurso = recurso.cod_recurso
             AND  despesa.exercicio = recurso.exercicio
             
            JOIN  orcamento.conta_despesa
              ON  conta_despesa.exercicio = despesa.exercicio
             AND  conta_despesa.cod_conta = despesa.cod_conta
             
       LEFT JOIN  orcamento.despesa_acao
              ON  despesa_acao.exercicio_despesa = despesa.exercicio
             AND  despesa_acao.cod_despesa = despesa.cod_despesa
             
       LEFT JOIN  ppa.acao
              ON  acao.cod_acao = despesa_acao.cod_acao
              
       LEFT JOIN  ppa.programa
              ON  programa.cod_programa = acao.cod_programa

       LEFT JOIN  (       SELECT  conta_despesa.exercicio
                               ,  conta_despesa.cod_estrutural
                               ,  conta_despesa.cod_conta
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
             
            JOIN  tcmgo.orgao
              ON  orgao.exercicio = empenho.exercicio
              
            JOIN  sw_cgm
              ON  sw_cgm.numcgm = orgao.numcgm_contador
              
            JOIN  sw_cgm AS credor
              ON  credor.numcgm = pre_empenho.cgm_beneficiario
              
       LEFT JOIN  sw_cgm_pessoa_fisica
              ON  sw_cgm_pessoa_fisica.numcgm = credor.numcgm
              
       LEFT JOIN  sw_cgm_pessoa_juridica
              ON  sw_cgm_pessoa_juridica.numcgm = credor.numcgm
              
           WHERE  nota_liquidacao.exercicio = '".$this->getDado('exercicio')."'
             AND  nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
             AND  nota_liquidacao.cod_entidade IN (".$this->getDado('cod_entidade').")
             
        GROUP BY  pre_empenho.exercicio, codprograma, codorgao, numunidade,  codfuncao, codsubfuncao, codnatureza
               ,  numpao
               ,  elementodespesa
               ,  subelementodespesa
               ,  dotacaoresto
               ,  empenho.cod_empenho
               ,  dtempenho
               ,  dtliquidacao
               ,  TCMGO.numero_nota_liquidacao('".$this->getDado('exercicio')."',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho)
               ,  empenho.cod_entidade
               ,  nota_liquidacao.exercicio_empenho
               ,  nota_liquidacao.exercicio, nota_liquidacao.cod_entidade, nota_liquidacao.cod_nota
               ,  elemento_de_para.estrutural
               ,  sw_cgm.nom_cgm
               ,  tipo_docFiscal
               ,  num_docFiscal
               ,  vl_docVlTotal
               ,  vl_docAssoc
               ,  cnpj_cpf
               ,  tipo_credor
               ,  num_inscEst
               ,  num_inscMun
               ,  cep_municipio
               ,  uf_credor
               ,  nom_credor
               ,  nota_fiscal.cod_tipo
               ,  nota_fiscal.nro_nota
               ,  nota_fiscal.nro_serie
               ,  nota_fiscal.data_emissao
               ,  nota_fiscal.chave_acesso
               ,  acao.num_acao
               ,  credor.cod_pais

       ORDER BY   nota_liquidacao.exercicio, nota_liquidacao.cod_entidade, nota_liquidacao.cod_nota
        ";
        return $stSql;
    }
}
