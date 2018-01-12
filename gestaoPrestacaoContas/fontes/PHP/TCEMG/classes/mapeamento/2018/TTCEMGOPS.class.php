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
    * Classe de mapeamento da tabela TTCEMG
    * Data de Criação: 26/02/2014

    * @author Analista: Valtair
    * @author Desenvolvedor: Carlos Adriano

    $Id: TTCEMGOPS.class.php 64106 2015-12-02 19:13:45Z michel $

    * @package URBEM
    * @subpackage Mapeamento
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TTCEMGOPS extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosOPS10.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosOPS10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosOPS10().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );        
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosOPS10()
    {
        $stSql  = " 
                  SELECT '10' AS tiporegistro
                       , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS codorgao
                       , CASE WHEN pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho AND pre_empenho.implantado = 't'
                              THEN CASE WHEN uniorcam_restos_atual.num_orgao IS NOT NULL
                                        THEN LPAD(LPAD(uniorcam_restos_atual.num_orgao::VARCHAR,2,'0')||LPAD(uniorcam_restos_atual.num_unidade::VARCHAR,2,'0'),5,'0')::VARCHAR
                                        ELSE LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')::VARCHAR
                                    END
                              ELSE LPAD((lpad(despesa.num_orgao::VARCHAR, 3, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0')),5,'0')::VARCHAR
                          END AS codunidadesub
                       , TO_CHAR(nlp.timestamp,'yyyymmddHH24MI')||LPAD(ordem_pagamento.cod_ordem::VARCHAR,10,'0') AS nroop -- MI : minuto (00-59)
                       , TO_CHAR(nlp.timestamp,'ddmmyyyy') AS dtpagamento
                       , empenho.cod_empenho
                       , SUM(nlp.vl_pago) AS vlop
                       , CASE WHEN ordem_pagamento.observacao <> ''
                              THEN trim(regexp_replace(sem_acentos(ordem_pagamento.observacao), '[º|°]', '', 'gi'))
                              ELSE 'pagamento OP' || ordem_pagamento.cod_ordem::varchar
                          END AS especificacaoop
                       , sw_cgm_pessoa_fisica.cpf AS cpfresppgto
                    FROM empenho.nota_liquidacao_paga AS nlp
               LEFT JOIN empenho.nota_liquidacao_paga_anulada AS nlpa
                      ON nlpa.exercicio    = nlp.exercicio
                     AND nlpa.cod_nota     = nlp.cod_nota
                     AND nlpa.cod_entidade = nlp.cod_entidade
                     AND nlpa.timestamp    = nlp.timestamp
               LEFT JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga AS plnlp
                      ON nlp.cod_entidade = plnlp.cod_entidade
                     AND nlp.cod_nota     = plnlp.cod_nota
                     AND nlp.exercicio    = plnlp.exercicio_liquidacao
                     AND nlp.timestamp    = plnlp.timestamp
               LEFT JOIN empenho.pagamento_liquidacao AS pl
                      ON pl.cod_entidade         = plnlp.cod_entidade
                     AND pl.cod_nota             = plnlp.cod_nota
                     AND pl.exercicio            = plnlp.exercicio
                     AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
                     AND pl.cod_ordem            = plnlp.cod_ordem
               LEFT JOIN empenho.nota_liquidacao AS nl
                      ON nl.exercicio    = pl.exercicio_liquidacao
                     AND nl.cod_nota     = pl.cod_nota
                     AND nl.cod_entidade = pl.cod_entidade
               LEFT JOIN empenho.empenho
                      ON empenho.exercicio    = nl.exercicio_empenho
                     AND empenho.cod_entidade = nl.cod_entidade
                     AND empenho.cod_empenho  = nl.cod_empenho
               LEFT JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = nlp.cod_entidade
                     AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
                     AND configuracao_entidade.cod_modulo   = 55
                     AND configuracao_entidade.parametro    = 'tcemg_codigo_orgao_entidade_sicom'
               LEFT JOIN empenho.ordem_pagamento
                      ON pl.exercicio    = ordem_pagamento.exercicio
                     AND pl.cod_entidade = ordem_pagamento.cod_entidade
                     AND pl.cod_ordem    = ordem_pagamento.cod_ordem
               LEFT JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

               LEFT JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
        
               LEFT JOIN empenho.restos_pre_empenho
                      ON pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = restos_pre_empenho.exercicio 
        
               LEFT JOIN tcemg.uniorcam AS uniorcam_restos
                      ON uniorcam_restos.num_unidade = restos_pre_empenho.num_unidade
                     AND uniorcam_restos.num_orgao   = restos_pre_empenho.num_orgao
                     AND uniorcam_restos.exercicio   = restos_pre_empenho.exercicio    
                     AND uniorcam_restos.num_orgao_atual IS NOT NULL

               LEFT JOIN tcemg.uniorcam AS uniorcam_restos_atual
                      ON uniorcam_restos_atual.num_unidade = uniorcam_restos.num_unidade_atual
                     AND uniorcam_restos_atual.num_orgao   = uniorcam_restos.num_orgao_atual
                     AND uniorcam_restos_atual.exercicio   = '".$this->getDado('exercicio')."' 
        
               LEFT JOIN orcamento.despesa
                      ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                     AND despesa.exercicio   = pre_empenho_despesa.exercicio

               LEFT JOIN tesouraria.pagamento
                      ON pagamento.exercicio    = nlp.exercicio
                     AND pagamento.cod_nota     = nlp.cod_nota
                     AND pagamento.cod_entidade = nlp.cod_entidade
                     AND pagamento.timestamp    = nlp.timestamp

               LEFT JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = pagamento.cgm_usuario

                   WHERE nlp.cod_entidade IN (".$this->getDado('entidade').")
                     AND TO_DATE(nlp.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                           AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                GROUP BY tiporegistro
                       , codorgao
                       , codunidadesub
                       , nroop
                       , empenho.cod_empenho
                       , dtpagamento
                       , especificacaoop
                       , cpfresppgto
                ORDER BY nroop
                  ";
        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosOPS11.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosOPS11(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosOPS11().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );                
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosOPS11()
    {
        $stSql = "
                  SELECT '11' AS tiporegistro
                       , LPAD(ordem_pagamento.cod_ordem::VARCHAR,7,'0')||ordem_pagamento.exercicio||TO_CHAR(nlp.timestamp,'HH24MI') AS codreduzidoop -- MI : minuto (00-59)
                       , CASE WHEN pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho AND pre_empenho.implantado = 't'
                              THEN CASE WHEN uniorcam_restos_atual.num_orgao IS NOT NULL
                                        THEN LPAD(LPAD(uniorcam_restos_atual.num_orgao::VARCHAR,2,'0')||LPAD(uniorcam_restos_atual.num_unidade::VARCHAR,2,'0'),5,'0')::VARCHAR
                                        ELSE LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')::VARCHAR
                                    END
                              ELSE LPAD((lpad(despesa.num_orgao::VARCHAR, 3, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0')),5,'0')::VARCHAR
                          END AS codunidadesub
                       , TO_CHAR(nlp.timestamp,'yyyymmddHH24MI')||LPAD(ordem_pagamento.cod_ordem::VARCHAR,10,'0') AS nroop -- MI : minuto (00-59)
                       , TO_CHAR(nlp.timestamp,'ddmmyyyy') AS dtpagamento
                       , CASE WHEN TO_CHAR(empenho.dt_empenho, 'yyyy')::INTEGER < ".$this->getDado('exercicio')." AND TO_CHAR(nl.dt_liquidacao, 'yyyy')::INTEGER < ".$this->getDado('exercicio')." AND TO_CHAR(plnlp.timestamp,'yyyy')::INTEGER = ".$this->getDado('exercicio')."
                              THEN '3'
                              WHEN TO_CHAR(empenho.dt_empenho, 'yyyy')::INTEGER < ".$this->getDado('exercicio')." AND TO_CHAR(nl.dt_liquidacao, 'yyyy')::INTEGER = ".$this->getDado('exercicio')." AND TO_CHAR(plnlp.timestamp,'yyyy')::INTEGER = ".$this->getDado('exercicio')."
                              THEN '4'
                              WHEN TO_CHAR(empenho.dt_empenho, 'yyyy')::INTEGER = ".$this->getDado('exercicio')." AND TO_CHAR(nl.dt_liquidacao, 'yyyy')::INTEGER = ".$this->getDado('exercicio')." AND TO_CHAR(plnlp.timestamp,'yyyy')::INTEGER = ".$this->getDado('exercicio')." AND conta_despesa.cod_estrutural ILIKE '4.6%'
                              THEN '2'
                              ELSE '1'
                          END AS tipopagamento
                       , empenho.cod_empenho AS nroempenho
                       , empenho.dt_empenho AS dtempenho
                       , TCEMG.numero_nota_liquidacao( '".$this->getDado('exercicio')."'
                                                     , empenho.cod_entidade
                                                     , nl.cod_nota
                                                     , nl.exercicio_empenho
                                                     , empenho.cod_empenho
                         ) AS nroliquidacao
                       , nl.dt_liquidacao AS dtliquidacao
                       , CASE WHEN restos_pre_empenho.recurso IS NOT NULL
                              THEN restos_pre_empenho.recurso
                              ELSE despesa.cod_recurso
                          END AS codfontrecursos
                       , SUM(nlp.vl_pago) AS valorfonte
                       , CASE WHEN documento_cgm.numero IS NOT NULL OR TRIM(documento_cgm.numero) <> ''
                              THEN documento_cgm.tipo
                              ELSE 2
                          END AS tipodocumentocredor
                       , CASE WHEN documento_cgm.numero IS NOT NULL OR TRIM(documento_cgm.numero) <> ''
                              THEN documento_cgm.numero
                              ELSE (SELECT cnpj 
                                     FROM sw_cgm_pessoa_juridica 
                                    WHERE numcgm = (SELECT numcgm 
                                                      FROM orcamento.entidade 
                                                     WHERE exercicio = '".$this->getDado('exercicio')."'
                                                       AND cod_entidade = ".$this->getDado('entidade')."))::VARCHAR
                            END AS nrodocumento
                    FROM empenho.nota_liquidacao_paga AS nlp
               LEFT JOIN empenho.nota_liquidacao_paga_anulada AS nlpa
                      ON nlpa.exercicio    = nlp.exercicio
                     AND nlpa.cod_nota     = nlp.cod_nota
                     AND nlpa.cod_entidade = nlp.cod_entidade
                     AND nlpa.timestamp    = nlp.timestamp
               LEFT JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga AS plnlp
                      ON nlp.cod_entidade = plnlp.cod_entidade
                     AND nlp.cod_nota     = plnlp.cod_nota
                     AND nlp.exercicio    = plnlp.exercicio_liquidacao
                     AND nlp.timestamp    = plnlp.timestamp
               LEFT JOIN empenho.pagamento_liquidacao AS pl
                      ON pl.cod_entidade         = plnlp.cod_entidade
                     AND pl.cod_nota             = plnlp.cod_nota
                     AND pl.exercicio            = plnlp.exercicio
                     AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
                     AND pl.cod_ordem            = plnlp.cod_ordem
               LEFT JOIN empenho.nota_liquidacao AS nl
                      ON nl.exercicio    = pl.exercicio_liquidacao
                     AND nl.cod_nota     = pl.cod_nota
                     AND nl.cod_entidade = pl.cod_entidade
               LEFT JOIN empenho.empenho
                      ON empenho.exercicio    = nl.exercicio_empenho
                     AND empenho.cod_entidade = nl.cod_entidade
                     AND empenho.cod_empenho  = nl.cod_empenho
               LEFT JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = nlp.cod_entidade
                     AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
                     AND configuracao_entidade.cod_modulo   = 55
                     AND configuracao_entidade.parametro    = 'tcemg_codigo_orgao_entidade_sicom'
               LEFT JOIN empenho.ordem_pagamento
                      ON pl.exercicio    = ordem_pagamento.exercicio
                     AND pl.cod_entidade = ordem_pagamento.cod_entidade
                     AND pl.cod_ordem    = ordem_pagamento.cod_ordem
               LEFT JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
               LEFT JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
               LEFT JOIN empenho.restos_pre_empenho
                      ON pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = restos_pre_empenho.exercicio 
               LEFT JOIN tcemg.uniorcam AS uniorcam_restos
                      ON uniorcam_restos.num_unidade = restos_pre_empenho.num_unidade
                     AND uniorcam_restos.num_orgao   = restos_pre_empenho.num_orgao
                     AND uniorcam_restos.exercicio   = restos_pre_empenho.exercicio    
                     AND uniorcam_restos.num_orgao_atual IS NOT NULL
               LEFT JOIN tcemg.uniorcam AS uniorcam_restos_atual
                      ON uniorcam_restos_atual.num_unidade = uniorcam_restos.num_unidade_atual
                     AND uniorcam_restos_atual.num_orgao   = uniorcam_restos.num_orgao_atual
                     AND uniorcam_restos_atual.exercicio   = '".$this->getDado('exercicio')."' 
               LEFT JOIN orcamento.despesa
                      ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                     AND despesa.exercicio   = pre_empenho_despesa.exercicio
               LEFT JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
              INNER JOIN (
                          SELECT numcgm
                               , cpf AS numero
                               , 1 AS tipo
                            FROM sw_cgm_pessoa_fisica
                           UNION
                          SELECT numcgm
                               , cnpj AS numero
                               , 2 AS tipo
                            FROM sw_cgm_pessoa_juridica
                         ) AS documento_cgm
                      ON documento_cgm.numcgm = pre_empenho.cgm_beneficiario
               LEFT JOIN tesouraria.pagamento
                      ON pagamento.exercicio    = nlp.exercicio
                     AND pagamento.cod_nota     = nlp.cod_nota
                     AND pagamento.cod_entidade = nlp.cod_entidade
                     AND pagamento.timestamp    = nlp.timestamp
               LEFT JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = pagamento.cgm_usuario
                   WHERE nlp.cod_entidade IN (".$this->getDado('entidade').")
                     AND TO_DATE(nlp.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                           AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                GROUP BY tiporegistro
                       , codreduzidoop
                       , codunidadesub
                       , nroop
                       , dtpagamento
                       , tipopagamento
                       , nroempenho
                       , dtempenho
                       , nroliquidacao
                       , dtliquidacao
                       , codfontrecursos
                       , tipodocumentocredor
                       , nrodocumento
                ORDER BY nroop
        ";
        return $stSql;
    }

        /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosOPS12.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    function recuperaDadosOPS12( &$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "" ){
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosOPS12().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );                
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        return $obErro;
    }    
    
    function montaRecuperaDadosOPS12(){
        $stSql  = "
                  SELECT '12' AS tiporegistro
                       , LPAD(ordem_pagamento.cod_ordem::VARCHAR,7,'0')||ordem_pagamento.exercicio||TO_CHAR(nlp.timestamp,'HH24MI') AS codreduzidoop -- MI : minuto (00-59)
                       , CASE WHEN plano_conta.cod_estrutural like '1.1.1.1.1.01%'
                              THEN '05'
                              ELSE CASE WHEN pagamento_tipo_documento.cod_tipo_documento IS NOT NULL
                                        THEN pagamento_tipo_documento.cod_tipo_documento::varchar
                                        ELSE '99'
                                    END
                          END AS tipodocumentoop
                       , CASE WHEN plano_conta.cod_estrutural like '1.1.1.1.1.01%'
                              THEN ''
                              ELSE CASE WHEN pagamento_tipo_documento.num_documento IS NULL
                                        THEN '0000'
                                        ELSE pagamento_tipo_documento.num_documento
                                    END
                          END AS nrodocumento
                       , CASE WHEN conta_bancaria.cod_ctb_anterior IS NULL
                              THEN plano_analitica.cod_plano
                              ELSE conta_bancaria.cod_ctb_anterior
                          END AS codctb
                       , plano_recurso.cod_recurso AS codfontectb
                       , CASE WHEN plano_conta.cod_estrutural like '1.1.1.1.1.01%'
                              THEN ' '
                              ELSE CASE WHEN pagamento_tipo_documento.cod_tipo_documento = 99
                                        THEN (SELECT td.descricao FROM tcemg.tipo_documento AS td WHERE td.cod_tipo = pagamento_tipo_documento.cod_tipo_documento)
                                        WHEN pagamento_tipo_documento.cod_tipo_documento IS NULL
                                        THEN 'Outros'
                                        ELSE ' '
                                    END
                          END AS desc_tipo_documento_op
                       , TO_CHAR(plnlp.timestamp,'ddmmyyyy') AS dtemissao
                       , SUM(nlp.vl_pago) AS vldocumento
                    FROM empenho.nota_liquidacao_paga AS nlp
               LEFT JOIN empenho.nota_liquidacao_paga_anulada AS nlpa
                      ON nlpa.exercicio    = nlp.exercicio
                     AND nlpa.cod_nota     = nlp.cod_nota
                     AND nlpa.cod_entidade = nlp.cod_entidade
                     AND nlpa.timestamp    = nlp.timestamp
               LEFT JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga AS plnlp
                      ON nlp.cod_entidade = plnlp.cod_entidade
                     AND nlp.cod_nota     = plnlp.cod_nota
                     AND nlp.exercicio    = plnlp.exercicio_liquidacao
                     AND nlp.timestamp    = plnlp.timestamp
               LEFT JOIN empenho.pagamento_liquidacao AS pl
                      ON pl.cod_entidade         = plnlp.cod_entidade
                     AND pl.cod_nota             = plnlp.cod_nota
                     AND pl.exercicio            = plnlp.exercicio
                     AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
                     AND pl.cod_ordem            = plnlp.cod_ordem
               LEFT JOIN empenho.nota_liquidacao AS nl
                      ON nl.exercicio    = pl.exercicio_liquidacao
                     AND nl.cod_nota     = pl.cod_nota
                     AND nl.cod_entidade = pl.cod_entidade
               LEFT JOIN empenho.empenho
                      ON empenho.exercicio    = nl.exercicio_empenho
                     AND empenho.cod_entidade = nl.cod_entidade
                     AND empenho.cod_empenho  = nl.cod_empenho
               LEFT JOIN empenho.ordem_pagamento
                      ON pl.exercicio    = ordem_pagamento.exercicio
                     AND pl.cod_entidade = ordem_pagamento.cod_entidade
                     AND pl.cod_ordem    = ordem_pagamento.cod_ordem
               LEFT JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
               LEFT JOIN empenho.restos_pre_empenho
                      ON pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = restos_pre_empenho.exercicio 

               LEFT JOIN empenho.nota_liquidacao_conta_pagadora
                      ON nota_liquidacao_conta_pagadora.exercicio_liquidacao = nlp.exercicio
                     AND nota_liquidacao_conta_pagadora.cod_entidade         = nlp.cod_entidade
                     AND nota_liquidacao_conta_pagadora.cod_nota             = nlp.cod_nota
                     AND nota_liquidacao_conta_pagadora.timestamp            = nlp.timestamp

               LEFT JOIN contabilidade.plano_analitica
                      ON plano_analitica.cod_plano = nota_liquidacao_conta_pagadora.cod_plano
                     AND plano_analitica.exercicio = nota_liquidacao_conta_pagadora.exercicio

               LEFT JOIN contabilidade.plano_recurso
                      ON plano_recurso.cod_plano = plano_analitica.cod_plano
                     AND plano_recurso.exercicio = plano_analitica.exercicio
                     
              INNER JOIN contabilidade.plano_conta 
                      ON plano_analitica.cod_conta = plano_conta.cod_conta
                     AND plano_analitica.exercicio = plano_conta.exercicio
              
               LEFT JOIN tcemg.conta_bancaria
                      ON conta_bancaria.cod_conta = plano_conta.cod_conta
                     AND conta_bancaria.exercicio = plano_conta.exercicio

               LEFT JOIN tesouraria.pagamento
                      ON pagamento.exercicio    = nlp.exercicio
                     AND pagamento.cod_nota     = nlp.cod_nota
                     AND pagamento.cod_entidade = nlp.cod_entidade
                     AND pagamento.timestamp    = nlp.timestamp
               LEFT JOIN tcemg.pagamento_tipo_documento
                      ON pagamento_tipo_documento.exercicio    = nlp.exercicio
                     AND pagamento_tipo_documento.cod_nota     = nlp.cod_nota
                     AND pagamento_tipo_documento.cod_entidade = nlp.cod_entidade
                     AND pagamento_tipo_documento.timestamp    = nlp.timestamp

                   WHERE nlp.cod_entidade IN (".$this->getDado('entidade').")
                     AND TO_DATE(nlp.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                           AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')

                GROUP BY tiporegistro
                       , codreduzidoop
                       , tipodocumentoop
                       , nrodocumento
                       , codctb
                       , codfontectb
                       , desc_tipo_documento_op
                       , dtemissao
                ORDER BY codreduzidoop
        ";
        return $stSql;
    }
    
     /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosOPS13.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    function recuperaDadosOPS13( &$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "" ){
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosOPS13().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        return $obErro;
    }    
    
    function montaRecuperaDadosOPS13(){
        
        $stSql  = "
                  SELECT '13' AS tiporegistro
                       , LPAD(ordem_pagamento.cod_ordem::VARCHAR,7,'0')||ordem_pagamento.exercicio||TO_CHAR(nlp.timestamp,'HH24MI') AS codreduzidoop -- MI : minuto (00-59)
                       , CASE WHEN conta_receita_orcamentaria.descricao SIMILAR TO ('%INSS%')
                              THEN '0001'
                              WHEN conta_receita_orcamentaria.descricao SIMILAR TO ('%RPPS%')
                              THEN '0002'
                              WHEN conta_receita_orcamentaria.descricao SIMILAR TO ('%IRRF%')
                              THEN '0003'
                              WHEN conta_receita_orcamentaria.descricao SIMILAR TO ('%ISS%')
                              THEN '0004'
                              ELSE LPAD(ordem_pagamento_retencao.cod_receita::VARCHAR , 4, '0')
                          END AS tiporetencao
                       , conta_receita_orcamentaria.descricao AS descricaoretencao
                       , SUM(ordem_pagamento_retencao.vl_retencao) AS vlretencao
                    FROM empenho.nota_liquidacao_paga AS nlp
               LEFT JOIN empenho.nota_liquidacao_paga_anulada AS nlpa
                      ON nlpa.exercicio    = nlp.exercicio
                     AND nlpa.cod_nota     = nlp.cod_nota
                     AND nlpa.cod_entidade = nlp.cod_entidade
                     AND nlpa.timestamp    = nlp.timestamp
               LEFT JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga AS plnlp
                      ON nlp.cod_entidade = plnlp.cod_entidade
                     AND nlp.cod_nota     = plnlp.cod_nota
                     AND nlp.exercicio    = plnlp.exercicio_liquidacao
                     AND nlp.timestamp    = plnlp.timestamp
               LEFT JOIN empenho.pagamento_liquidacao AS pl
                      ON pl.cod_entidade         = plnlp.cod_entidade
                     AND pl.cod_nota             = plnlp.cod_nota
                     AND pl.exercicio            = plnlp.exercicio
                     AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
                     AND pl.cod_ordem            = plnlp.cod_ordem

               LEFT JOIN empenho.nota_liquidacao AS nl
                      ON nl.exercicio    = pl.exercicio_liquidacao
                     AND nl.cod_nota     = pl.cod_nota
                     AND nl.cod_entidade = pl.cod_entidade

               LEFT JOIN empenho.empenho
                      ON empenho.exercicio    = nl.exercicio_empenho
                     AND empenho.cod_entidade = nl.cod_entidade
                     AND empenho.cod_empenho  = nl.cod_empenho

               LEFT JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

               LEFT JOIN empenho.restos_pre_empenho
                      ON pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = restos_pre_empenho.exercicio 

               LEFT JOIN empenho.ordem_pagamento
                      ON pl.exercicio    = ordem_pagamento.exercicio
                     AND pl.cod_entidade = ordem_pagamento.cod_entidade
                     AND pl.cod_ordem    = ordem_pagamento.cod_ordem

              INNER JOIN empenho.ordem_pagamento_retencao
                      ON ordem_pagamento_retencao.exercicio    = ordem_pagamento.exercicio
                     AND ordem_pagamento_retencao.cod_entidade = ordem_pagamento.cod_entidade
                     AND ordem_pagamento_retencao.cod_ordem    = ordem_pagamento.cod_ordem
                     
               LEFT JOIN orcamento.receita AS receita_retencao_orcamentaria
                      ON receita_retencao_orcamentaria.cod_receita    = ordem_pagamento_retencao.cod_receita
                     AND receita_retencao_orcamentaria.exercicio      = ordem_pagamento_retencao.exercicio
            
               LEFT JOIN orcamento.conta_receita AS conta_receita_orcamentaria
                      ON conta_receita_orcamentaria.cod_conta = receita_retencao_orcamentaria.cod_conta
                     AND conta_receita_orcamentaria.exercicio = receita_retencao_orcamentaria.exercicio

               LEFT JOIN empenho.nota_liquidacao_conta_pagadora
                      ON nota_liquidacao_conta_pagadora.exercicio_liquidacao = nlp.exercicio
                     AND nota_liquidacao_conta_pagadora.cod_entidade         = nlp.cod_entidade
                     AND nota_liquidacao_conta_pagadora.cod_nota             = nlp.cod_nota
                     AND nota_liquidacao_conta_pagadora.timestamp            = nlp.timestamp

               LEFT JOIN contabilidade.plano_analitica
                      ON plano_analitica.cod_plano = nota_liquidacao_conta_pagadora.cod_plano
                     AND plano_analitica.exercicio = nota_liquidacao_conta_pagadora.exercicio

               LEFT JOIN contabilidade.plano_conta 
                      ON plano_analitica.cod_conta = plano_conta.cod_conta
                     AND plano_analitica.exercicio = plano_conta.exercicio

                   WHERE nlp.cod_entidade IN (".$this->getDado('entidade').")
                     AND ordem_pagamento_retencao.cod_receita IS NOT NULL
                     AND TO_DATE(nlp.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                           AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')

                GROUP BY tiporegistro
                       , codreduzidoop
                       , tiporetencao
                       , descricaoretencao
                ORDER BY codreduzidoop
        ";
        return $stSql;
    }
    
    public function __destruct(){}

}