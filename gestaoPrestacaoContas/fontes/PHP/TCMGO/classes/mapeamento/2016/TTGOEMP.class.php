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

    $Id: TTGOEMP.class.php 65220 2016-05-03 21:30:22Z michel $

    * Casos de uso: uc-06.04.00
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTGOEMP extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/

    public function recuperaEmpenho(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEmpenho",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaEmpenho()
    {
        $stSql = "
           SELECT  '10'    AS  tipo_registro
                ,
                         CASE WHEN empenho_modalidade.cod_modalidade = '01' OR empenho_modalidade.cod_modalidade = '02' THEN
                                   '10'
                              ELSE
                                   empenho_modalidade.cod_modalidade
                              END AS modalidade
                ,  '99' as assunto
                ,  programa.num_programa
                ,  despesa.num_orgao
                ,  despesa.num_unidade
                ,  despesa.cod_funcao
                ,  despesa.cod_subfuncao
                ,  SUBSTR(acao.num_acao::varchar,1,1)     AS  cod_natureza
                ,  SUBSTR(acao.num_acao::varchar,2,3)     AS  numero_pao
                ,  SUBSTR(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''),1,6)    AS  elemento_despesa
                ,  CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                        THEN SUBSTR(REPLACE(elemento_de_para.estrutural::varchar,'.',''),7,2)
                        ELSE '00'
                   END AS subelemento_despesa
             -- ,  SUBSTR(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''),7,2)    AS subelemento_despesa
                ,  empenho.cod_empenho
                ,  '01'    AS  tipo_empenho
                ,  TO_CHAR(empenho.dt_empenho,'dd/mm/yyyy')     AS  dt_empenho
                ,  SUM(item_pre_empenho.vl_total)  AS  vl_total
                ,  credor.nom_cgm
                           ,  CASE WHEN empenho_modalidade.cod_modalidade <> '10' AND empenho_modalidade.cod_modalidade <> '11'
                                   THEN ''
                                   ELSE empenho_modalidade.cod_fundamentacao
                              END AS fundamentacao
                           ,  CASE WHEN empenho_modalidade.cod_modalidade <> '10' AND empenho_modalidade.cod_modalidade <> '11'
                                   THEN ''
                                   ELSE TRIM(regexp_replace(empenho_modalidade.justificativa ,E'\\r\\n',''))
                              END AS justificativa
                           ,  CASE WHEN empenho_modalidade.cod_modalidade <> '10' AND empenho_modalidade.cod_modalidade <> '11'
                                   THEN ''
                                   ELSE TRIM(regexp_replace(empenho_modalidade.razao_escolha ,E'\\r\\n',''))
                              END AS escolha
                           ,  CASE WHEN credor.cod_pais <> 1
                                   THEN   3
                                   WHEN sw_cgm_pessoa_fisica.numcgm ISNULL
                                   THEN   2
                                   ELSE   1
                              END     AS  tipo_credor
                           ,  CASE WHEN sw_cgm_pessoa_fisica.numcgm ISNULL
                                   THEN   sw_cgm_pessoa_juridica.cnpj
                                   ELSE   sw_cgm_pessoa_fisica.cpf
                              END     AS  documento
                  ,  pre_empenho.descricao   AS  descricao
                  , 'Fabio Oliveira de Lima' AS nom_resp
                    ,'41880854104' AS cpf_resp
                    , contrato.nro_contrato as nro_instrumento_contrato
                    , processos.numero_processo as nro_processo_licitacao
                    , exercicio_processo as ano_processo_licitacao
                    , processo_administrativo as nro_processo_administrativo
                    , '0' AS  numero_sequencial
             FROM  empenho.empenho
       INNER JOIN  empenho.pre_empenho
               ON  empenho.exercicio = pre_empenho.exercicio
              AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
       INNER JOIN  empenho.pre_empenho_despesa
               ON  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
              AND  pre_empenho_despesa.exercicio = pre_empenho.exercicio
       INNER JOIN  empenho.item_pre_empenho
               ON  item_pre_empenho.exercicio = pre_empenho.exercicio
              AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
       INNER JOIN  orcamento.despesa
               ON  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
              AND  despesa.exercicio = pre_empenho_despesa.exercicio
       LEFT JOIN   orcamento.despesa_acao
               ON  despesa_acao.exercicio_despesa = despesa.exercicio
              AND  despesa_acao.cod_despesa       = despesa.cod_despesa
       LEFT JOIN   ppa.acao
               ON  acao.cod_acao = despesa_acao.cod_acao
       LEFT JOIN   ppa.programa
               ON  programa.cod_programa = acao.cod_programa
       INNER JOIN  orcamento.conta_despesa
               ON  conta_despesa.exercicio = pre_empenho_despesa.exercicio
              AND  conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
       INNER JOIN  sw_cgm AS credor
               ON  credor.numcgm = pre_empenho.cgm_beneficiario
       INNER JOIN  empenho.atributo_empenho_valor
               ON  pre_empenho.exercicio = atributo_empenho_valor.exercicio
              AND  atributo_empenho_valor.cod_pre_empenho = pre_empenho.cod_pre_empenho
        LEFT JOIN  sw_cgm_pessoa_fisica
               ON  sw_cgm_pessoa_fisica.numcgm = credor.numcgm
        LEFT JOIN  sw_cgm_pessoa_juridica
               ON  sw_cgm_pessoa_juridica.numcgm = credor.numcgm
        LEFT JOIN  tcmgo.elemento_de_para
               ON  elemento_de_para.cod_conta = conta_despesa.cod_conta
              AND  elemento_de_para.exercicio = conta_despesa.exercicio
                LEFT JOIN  tcmgo.processos
                       ON  processos.cod_empenho  = empenho.cod_empenho
                      AND  processos.cod_entidade = empenho.cod_entidade
                      AND  processos.exercicio    = empenho.exercicio
                LEFT JOIN  tcmgo.contrato_empenho
                       ON  empenho.exercicio    = contrato_empenho.exercicio_empenho
                      AND  empenho.cod_entidade = contrato_empenho.cod_entidade
                      AND  empenho.cod_empenho  = contrato_empenho.cod_empenho
                LEFT JOIN  tcmgo.contrato
                       ON  contrato_empenho.cod_contrato = contrato.cod_contrato
                      AND  contrato_empenho.cod_entidade = contrato.cod_entidade
                      AND  contrato_empenho.exercicio    = contrato.exercicio
                     JOIN  tcmgo.empenho_modalidade
                       ON  empenho_modalidade.exercicio = empenho.exercicio
                      AND  empenho_modalidade.cod_entidade = empenho.cod_entidade
                      AND  empenho_modalidade.cod_empenho = empenho.cod_empenho

            WHERE  empenho.exercicio = '".$this->getDado('exercicio')."'
              AND  atributo_empenho_valor.cod_atributo = 101
              AND  atributo_empenho_valor.cod_modulo = 10
              AND  empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
              AND  empenho.dt_empenho BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
         GROUP BY
                 programa.num_programa
                 , despesa.num_orgao
                 , despesa.num_unidade
                 , despesa.cod_funcao
                 , despesa.cod_subfuncao
                 , conta_despesa.cod_estrutural
                 , cod_natureza
                 , elemento_despesa
                 , empenho.cod_empenho
                 , empenho.dt_empenho
                 , credor.nom_cgm
                 , tipo_credor
                 , documento
                 , pre_empenho.descricao
                 , elemento_de_para.estrutural
                 , empenho.atributo_empenho_valor.valor
                 , despesa.num_pao
                 , acao.num_acao 
                 , programa.num_programa
                 , empenho_modalidade.cod_modalidade
                             , empenho_modalidade.cod_fundamentacao
                             , empenho_modalidade.justificativa
                             , empenho_modalidade.razao_escolha
                             , empenho.cod_entidade
                             , empenho.exercicio
                             , contrato.nro_processo
                             , contrato.nro_contrato
                             , processos.numero_processo
                             , exercicio_processo
                             , processo_administrativo
                             , credor.cod_pais
        ORDER BY
                 programa.num_programa
                 , despesa.num_orgao
                 , despesa.num_unidade
                 , despesa.cod_funcao
                 , despesa.cod_subfuncao
                 , cod_natureza
                 , num_pao
                 , elemento_despesa
                 , subelemento_despesa
                 , empenho.cod_empenho
                 , empenho.dt_empenho
                 , credor.nom_cgm
                 , tipo_credor
                 , documento
                 , pre_empenho.descricao
                 , empenho.cod_entidade
                 , empenho.exercicio";

        return $stSql;
    }

    public function recuperaEmpenhoRecurso(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEmpenhoRecurso",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaEmpenhoRecurso()
    {
        $stSql = "
        SELECT  '11'    AS  tipo_registro
             ,  programa.num_programa
             ,  despesa.num_orgao
             ,  despesa.num_unidade
             ,  despesa.cod_funcao
             ,  despesa.cod_subfuncao
             ,  SUBSTR(acao.num_acao::varchar,1,1)     AS  cod_natureza
             ,  SUBSTR(acao.num_acao::varchar,2,3)     AS  numero_pao
             ,  SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),1,6)    AS  elemento_despesa
             ,  CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                        THEN SUBSTR(REPLACE(elemento_de_para.estrutural,'.',''),7,2)
                        ELSE '00'
                   END AS subelemento_despesa
             ,  empenho.cod_empenho
             ,  (recurso_direto.codigo_tc) AS cod_fonte
             ,  SUM(item_pre_empenho.vl_total)    AS  vl_total
             ,  '0'     AS  numero_sequencial
          FROM  empenho.empenho
    INNER JOIN  empenho.pre_empenho
            ON  empenho.exercicio = pre_empenho.exercicio
           AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
    INNER JOIN  empenho.pre_empenho_despesa
            ON  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
           AND  pre_empenho_despesa.exercicio = pre_empenho.exercicio
    INNER JOIN  empenho.item_pre_empenho
            ON  item_pre_empenho.exercicio = pre_empenho.exercicio
           AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
    INNER JOIN  orcamento.despesa
            ON  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
           AND  despesa.exercicio = pre_empenho_despesa.exercicio
     LEFT JOIN  orcamento.despesa_acao
            ON  despesa_acao.exercicio_despesa = despesa.exercicio
           AND  despesa_acao.cod_despesa       = despesa.cod_despesa
     LEFT JOIN  ppa.acao
            ON  acao.cod_acao = despesa_acao.cod_acao
     LEFT JOIN  ppa.programa
            ON  programa.cod_programa = acao.cod_programa
    INNER JOIN  orcamento.recurso
            ON  recurso.exercicio = despesa.exercicio
           AND  recurso.cod_recurso = despesa.cod_recurso
    INNER JOIN  orcamento.recurso_direto
            ON  recurso_direto.exercicio = despesa.exercicio
           AND  recurso_direto.cod_recurso = despesa.cod_recurso
--    INNER JOIN  orcamento.receita
--            ON  receita.exercicio = recurso.exercicio
--           AND  receita.cod_recurso = recurso.cod_recurso
--    INNER JOIN  orcamento.fonte_recurso
--            ON  fonte_recurso.cod_fonte = recurso.cod_fonte
    INNER JOIN  orcamento.conta_despesa
            ON  conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND  conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
     LEFT JOIN  tcmgo.elemento_de_para
            ON  elemento_de_para.cod_conta = conta_despesa.cod_conta
           AND  elemento_de_para.exercicio = conta_despesa.exercicio
         WHERE  empenho.exercicio = '".$this->getDado('exercicio')."'
           AND  empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
           AND  empenho.dt_empenho BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
      GROUP BY  programa.num_programa
              , despesa.num_orgao
              , despesa.num_unidade
              , despesa.cod_funcao
              , despesa.cod_subfuncao
              , conta_despesa.cod_estrutural
              , cod_natureza
              , elemento_despesa
              , cod_empenho
              , recurso_direto.codigo_tc
              , elemento_de_para.estrutural
              , acao.num_acao 
              
      order BY  programa.num_programa
              , despesa.num_orgao
              , despesa.num_unidade
              , despesa.cod_funcao
              , despesa.cod_subfuncao
              , cod_natureza
              , elemento_despesa
              , cod_empenho
        ";

        return $stSql;
    }

    public function recuperaEmpenhoObra(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEmpenhoObra",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaEmpenhoObra()
    {
        $stSql = "
        SELECT  '12'    AS  tipo_registro
             ,  programa.num_programa
             ,  despesa.num_orgao
             ,  despesa.num_unidade
             ,  despesa.cod_funcao
             ,  despesa.cod_subfuncao
             ,  SUBSTR(acao.num_acao::varchar,1,1)     AS  cod_natureza
             ,  SUBSTR(acao.num_acao::varchar,2,3)     AS  numero_pao
             ,  SUBSTR(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''),1,6)    AS  elemento_despesa
             ,  CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                        THEN SUBSTR(REPLACE(elemento_de_para.estrutural::varchar,'.',''),7,2)
                        ELSE '00'
                   END AS subelemento_despesa
             ,  empenho.cod_empenho
             ,  (LPAD(obra_empenho.cod_obra::varchar,4,'0') || obra_empenho.ano_obra) AS cod_obra
             ,  SUM(item_pre_empenho.vl_total)    AS  vl_total
             ,  '0'     AS  numero_sequencial
          FROM  empenho.empenho
    INNER JOIN  empenho.pre_empenho
            ON  empenho.exercicio = pre_empenho.exercicio
           AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
    INNER JOIN  empenho.pre_empenho_despesa
            ON  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
           AND  pre_empenho_despesa.exercicio = pre_empenho.exercicio
    INNER JOIN  empenho.item_pre_empenho
            ON  item_pre_empenho.exercicio = pre_empenho.exercicio
           AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
    INNER JOIN  orcamento.despesa
            ON  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
           AND  despesa.exercicio = pre_empenho_despesa.exercicio
     LEFT JOIN  orcamento.despesa_acao
            ON  despesa_acao.exercicio_despesa = despesa.exercicio
           AND  despesa_acao.cod_despesa       = despesa.cod_despesa
     LEFT JOIN  ppa.acao
            ON  acao.cod_acao = despesa_acao.cod_acao
     LEFT JOIN  ppa.programa
            ON  programa.cod_programa = acao.cod_programa
    INNER JOIN  orcamento.recurso
            ON  recurso.exercicio = despesa.exercicio
           AND  recurso.cod_recurso = despesa.cod_recurso
--    INNER JOIN  orcamento.fonte_recurso
--            ON  fonte_recurso.cod_fonte = recurso.cod_fonte
    INNER JOIN  orcamento.conta_despesa
            ON  conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND  conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
    INNER JOIN  tcmgo.obra_empenho
            ON  obra_empenho.cod_entidade = empenho.cod_entidade
           AND  obra_empenho.cod_empenho = empenho.cod_empenho
           AND  obra_empenho.exercicio = empenho.exercicio
     LEFT JOIN  tcmgo.elemento_de_para
            ON  elemento_de_para.cod_conta = conta_despesa.cod_conta
           AND  elemento_de_para.exercicio = conta_despesa.exercicio
         WHERE  empenho.exercicio = '".$this->getDado('exercicio')."'
           AND  empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
           AND  empenho.dt_empenho BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
      GROUP BY  programa.num_programa
              , despesa.num_orgao
              , despesa.num_unidade
              , despesa.cod_funcao
              , despesa.cod_subfuncao
              , conta_despesa.cod_estrutural
              , cod_natureza
              , elemento_despesa
              , empenho.cod_empenho
              , elemento_de_para.estrutural
              , obra_empenho.cod_obra
              , obra_empenho.ano_obra
              , acao.num_acao
      order BY  programa.num_programa
              , despesa.num_orgao
              , despesa.num_unidade
              , despesa.cod_funcao
              , despesa.cod_subfuncao
              , cod_natureza
              , elemento_despesa
              , empenho.cod_empenho
        ";

        return $stSql;
    }

    public function recuperaEmpenhoContrato(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEmpenhoContrato",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaEmpenhoContrato()
    {
        $stSql = "
       SELECT  '13'    AS  tipo_registro
             ,  programa.num_programa
             ,  despesa.num_orgao
             ,  despesa.num_unidade
             ,  despesa.cod_funcao
             ,  despesa.cod_subfuncao
             ,  SUBSTR(acao.num_acao::varchar,1,1)     AS  cod_natureza
             ,  SUBSTR(acao.num_acao::varchar,2,3)     AS  numero_pao
             ,  SUBSTR(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''),1,6)    AS  elemento_despesa
             ,  CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                        THEN SUBSTR(REPLACE(elemento_de_para.estrutural::varchar,'.',''),7,2)
                        ELSE '00'
                   END AS subelemento_despesa
             ,  empenho.cod_empenho
             ,  SUM(item_pre_empenho.vl_total)    AS  vl_total
             , contrato.nro_contrato
             , contrato.exercicio
             , '1' AS tipo_ajuste
             ,  '0'     AS  numero_sequencial
          FROM  empenho.empenho
    INNER JOIN  tcmgo.contrato_empenho
            ON  contrato_empenho.cod_empenho = empenho.cod_empenho
           AND  contrato_empenho.exercicio_empenho = empenho.exercicio
           AND  contrato_empenho.cod_entidade = empenho.cod_entidade
   INNER JOIN  tcmgo.contrato
            ON contrato.cod_contrato = contrato_empenho.cod_contrato
           AND contrato.exercicio = contrato_empenho.exercicio
           AND contrato.cod_entidade = contrato_empenho.cod_entidade
    INNER JOIN  empenho.pre_empenho
            ON  empenho.exercicio = pre_empenho.exercicio
           AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
    INNER JOIN  empenho.pre_empenho_despesa
            ON  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
           AND  pre_empenho_despesa.exercicio = pre_empenho.exercicio
    INNER JOIN  empenho.item_pre_empenho
            ON  item_pre_empenho.exercicio = pre_empenho.exercicio
           AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
    INNER JOIN  orcamento.despesa
            ON  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
           AND  despesa.exercicio = pre_empenho_despesa.exercicio
     LEFT JOIN  orcamento.despesa_acao
            ON  despesa_acao.exercicio_despesa = despesa.exercicio
           AND  despesa_acao.cod_despesa       = despesa.cod_despesa
     LEFT JOIN  ppa.acao
            ON  acao.cod_acao = despesa_acao.cod_acao
     LEFT JOIN  ppa.programa
            ON  programa.cod_programa = acao.cod_programa
    INNER JOIN  orcamento.recurso
            ON  recurso.exercicio = despesa.exercicio
           AND  recurso.cod_recurso = despesa.cod_recurso
--    INNER JOIN  orcamento.fonte_recurso
--            ON  fonte_recurso.cod_fonte = recurso.cod_fonte
    INNER JOIN  orcamento.conta_despesa
            ON  conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND  conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
      LEFT JOIN  tcmgo.elemento_de_para
            ON  elemento_de_para.cod_conta = conta_despesa.cod_conta
           AND  elemento_de_para.exercicio = conta_despesa.exercicio
          WHERE  empenho.exercicio = '".$this->getDado('exercicio')."'
           AND  empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
           AND  empenho.dt_empenho BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
      GROUP BY  programa.cod_programa
              , despesa.num_orgao
              , despesa.num_unidade
              , despesa.cod_funcao
              , despesa.cod_subfuncao
              , conta_despesa.cod_estrutural
              , cod_natureza
              , despesa.num_pao
              , elemento_despesa
              , empenho.cod_empenho
              , elemento_de_para.estrutural
              , nro_contrato
              , contrato.exercicio
              , programa.num_programa
              , acao.num_acao
      order BY  programa.cod_programa
              , despesa.num_orgao
              , despesa.num_unidade
              , despesa.cod_funcao
              , despesa.cod_subfuncao
              , cod_natureza
              , num_pao
              , elemento_despesa
              , empenho.cod_empenho
        ";

        return $stSql;
    }

}
