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
/*
    * Arquivo de geracao do arquivo RetencaoEmpresa.txt TCM/BA
    * Data de Criação   : 11/09/2015
    * @author Analista      Valtair Santos
    * @author Desenvolvedor Michel Teixeira
    * 
    * $Id: TTCMBARetencaoEmpresa.class.php 64041 2015-11-23 17:09:34Z lisiane $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBARetencaoEmpresa extends Persistente {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaRetencaoEmpresa(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaRetencaoEmpresa().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRetencaoEmpresa()
    {
        $stSql = "                       
                SELECT  1 AS tipo_registro
                       , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
                       , pagamento_liquidacao.cod_ordem AS num_pagamento
                       , to_char(nota_liquidacao_paga.timestamp,'yyyy') AS ano    
                       , empenho.exercicio AS ano_criacao
                       , to_char(nota_liquidacao_paga.timestamp,'dd/mm/yyyy') AS dt_pagamento_empenho    
                       , REPLACE(plano_conta.cod_estrutural,'.','') AS conta_contabil
                       , COALESCE(ordem_pagamento_retencao.vl_retencao,0.00) AS vl_retencao
                       , '".$this->getDado('competencia')."' AS competencia
                       , '' AS reservado_tcm

                 FROM empenho.empenho 

           INNER JOIN empenho.pre_empenho
                   ON empenho.exercicio = pre_empenho.exercicio    
                  AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho 

           INNER JOIN empenho.pre_empenho_despesa
                   ON pre_empenho.exercicio = pre_empenho_despesa.exercicio    
                  AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho   
                   
           INNER JOIN empenho.nota_liquidacao
                   ON empenho.exercicio = nota_liquidacao.exercicio_empenho    
                  AND empenho.cod_entidade = nota_liquidacao.cod_entidade    
                  AND empenho.cod_empenho = nota_liquidacao.cod_empenho  

           INNER JOIN empenho.pagamento_liquidacao
                   ON nota_liquidacao.exercicio = pagamento_liquidacao.exercicio_liquidacao
                  AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade    
                  AND nota_liquidacao.cod_nota = pagamento_liquidacao.cod_nota

           INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                   ON pagamento_liquidacao_nota_liquidacao_paga.exercicio = pagamento_liquidacao.exercicio
                  AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade = pagamento_liquidacao.cod_entidade
                  AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem = pagamento_liquidacao.cod_ordem
                  AND pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = pagamento_liquidacao.exercicio_liquidacao
                  AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota = pagamento_liquidacao.cod_nota

           INNER JOIN empenho.nota_liquidacao_paga
                   ON pagamento_liquidacao_nota_liquidacao_paga.cod_entidade = nota_liquidacao_paga.cod_entidade
                  AND pagamento_liquidacao_nota_liquidacao_paga.timestamp = nota_liquidacao_paga.timestamp
                  AND pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                  AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota = nota_liquidacao_paga.cod_nota

           INNER JOIN empenho.nota_liquidacao_conta_pagadora
                   ON nota_liquidacao_conta_pagadora.cod_entidade = nota_liquidacao_paga.cod_entidade
                  AND nota_liquidacao_conta_pagadora.cod_nota = nota_liquidacao_paga.cod_nota
                  AND nota_liquidacao_conta_pagadora.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                  AND nota_liquidacao_conta_pagadora.timestamp = nota_liquidacao_paga.timestamp

           INNER JOIN ( 
                        SELECT plano_analitica.exercicio   
                             , plano_analitica.cod_plano                        
                             , plano_conta.nom_conta                        
                             , recurso.cod_recurso                     
                             , recurso.nom_recurso
                             , plano_conta.cod_estrutural
                          FROM contabilidade.plano_analitica
                    INNER JOIN contabilidade.plano_conta
                            ON plano_conta.cod_conta = plano_analitica.cod_conta
                           AND plano_conta.exercicio = plano_analitica.exercicio
                    INNER JOIN contabilidade.plano_recurso
                            ON plano_recurso.cod_plano = plano_analitica.cod_plano
                           AND plano_recurso.exercicio = plano_analitica.exercicio
                    INNER JOIN orcamento.recurso
                            ON recurso.exercicio = plano_recurso.exercicio
                           AND recurso.cod_recurso = plano_recurso.cod_recurso
                     ) AS conta
                   ON conta.cod_plano = nota_liquidacao_conta_pagadora.cod_plano            
                  AND conta.exercicio = nota_liquidacao_conta_pagadora.exercicio
                  AND conta.cod_estrutural ilike '1.1.1.1.1.01%'

           INNER JOIN empenho.ordem_pagamento
                   ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio    
                  AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade    
                  AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem

           INNER JOIN empenho.ordem_pagamento_retencao
                   ON ordem_pagamento.exercicio = ordem_pagamento_retencao.exercicio    
                  AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade    
                  AND ordem_pagamento.cod_ordem = ordem_pagamento_retencao.cod_ordem

           INNER JOIN contabilidade.plano_analitica
                   ON ordem_pagamento_retencao.exercicio = plano_analitica.exercicio    
                  AND ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano

           INNER JOIN contabilidade.plano_conta
                   ON plano_analitica.exercicio = plano_conta.exercicio    
                  AND plano_analitica.cod_conta = plano_conta.cod_conta                                                         

           INNER JOIN orcamento.despesa
                   ON pre_empenho_despesa.exercicio = despesa.exercicio    
                  AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa

           INNER JOIN orcamento.conta_despesa 
                   ON pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
                  AND pre_empenho_despesa.exercicio = conta_despesa.exercicio
                     
                WHERE to_char(nota_liquidacao_paga.timestamp,'yyyy') = '".$this->getDado('exercicio')."'
                  AND to_date(to_char(nota_liquidacao_paga.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                                                     AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                  AND nota_liquidacao.cod_entidade IN (".$this->getDado('cod_entidade').")
                  --EXCETO FOLHA DE PAGAMENTO
                  AND conta_despesa.cod_estrutural NOT LIKE ('3.1%')

              GROUP BY to_char(nota_liquidacao_paga.timestamp,'dd/mm/yyyy')
                     , to_char(nota_liquidacao_paga.timestamp,'yyyy')
                     , empenho.exercicio
                     , empenho.cod_empenho
                     , plano_conta.cod_estrutural
                     , conta_despesa.cod_estrutural
                     , pagamento_liquidacao.cod_ordem
                     , ordem_pagamento_retencao.vl_retencao

              ORDER BY pagamento_liquidacao.cod_ordem
                     , empenho.cod_empenho
                     , dt_pagamento_empenho ";
        return $stSql;
    }
    
}

?>