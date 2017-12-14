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
    * Arquivo de geracao do arquivo sertTerceiros TCM/MG
    * Data de Criação   : 11/09/2015
    * 
    * @author Analista      Valtair Santos
    * @author Desenvolvedor Lisiane da Rosa Morais
    * 
    * @package URBEM
    * @subpackage
    * 
    * @ignore
    * 
    * $Id: $
    * $Rev: $
    * $Author:$
    * $Date:$
    * 
*/
include_once CLA_PERSISTENTE;

class TTCMBADocDiver extends Persistente {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaDados(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDados().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaDados()
    {   $stSql = " SELECT 1 AS tipo_registro
                        , despesa.num_unidade AS unidade_orcamentaria
                        , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
                        , empenho.cod_empenho AS num_empenho
                        , empenho.cod_empenho AS subempenho
		                    , nota_liq_paga.data_pagamento AS dt_pagamento_empenho   
		                    , nota_liq_paga.num_documento AS num_documento                   
                        , despesa.num_orgao AS cod_orgao
                        , ordem_pagamento.exercicio AS dt_ano
                        , cgm_pe.tipo_pessoa
                        , cgm_pe.documento
                        , cgm_pe.nom_cgm AS emitente    
                        , nota_liq_paga.tipo_documento
                        , nota_liq_paga.data_emissao
                        , nota_liq_paga.vl_pago AS vl_doc
                        , ordem_pagamento.observacao AS objeto     
                        , TO_CHAR(nota_liq_paga.data_pagamento, 'yyyymm') AS competencia

                     FROM empenho.ordem_pagamento      

                LEFT JOIN empenho.ordem_pagamento_anulada
                       ON ordem_pagamento.cod_ordem     = ordem_pagamento_anulada.cod_ordem                                                                                        
                      AND ordem_pagamento.exercicio     = ordem_pagamento_anulada.exercicio                                                                                        
                      AND ordem_pagamento.cod_entidade  = ordem_pagamento_anulada.cod_entidade                                                                                      
                      AND ordem_pagamento.exercicio     =  '".$this->getDado('exercicio')."'

                     JOIN empenho.pagamento_liquidacao
                       ON ordem_pagamento.cod_ordem    = pagamento_liquidacao.cod_ordem                                                                                          
                      AND ordem_pagamento.cod_entidade = pagamento_liquidacao.cod_entidade                                                                                       
                      AND ordem_pagamento.exercicio    = '".$this->getDado('exercicio')."'                                
                      AND ordem_pagamento.exercicio    = pagamento_liquidacao.exercicio

                     JOIN empenho.nota_liquidacao
                       ON pagamento_liquidacao.cod_nota              = nota_liquidacao.cod_nota                                             
                      AND pagamento_liquidacao.cod_entidade          = nota_liquidacao.cod_entidade                                         
                      AND pagamento_liquidacao.exercicio_liquidacao  = nota_liquidacao.exercicio        

                LEFT JOIN ( SELECT nlp.cod_entidade                                                                                           
                                 , nlp.cod_nota                                                                                               
                                 , plnlp.cod_ordem                                                                                            
                                 , plnlp.exercicio                                                                                            
                                 , nlp.exercicio as exercicio_liquidacao                                                                      
                                 , sum(coalesce(nlp.vl_pago ,0.00)) as vl_pago      
                                 , sum(coalesce(nlpa.vl_anulado ,0.00)) as vl_anulado                                                          
                                 , TO_DATE(TO_CHAR(nlp.timestamp, 'dd/mm/yyyy'),'dd/mm/yyyy') AS data_pagamento      
                                 , ptdp.num_documento
                                 , tipo_documento_pagamento.descricao AS tipo_documento
                                 , TO_DATE(TO_CHAR(ptdp.timestamp, 'dd/mm/yyyy'),'dd/mm/yyyy') AS data_emissao    
                              FROM empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp   
                                 , tesouraria.pagamento as tp     
                         LEFT JOIN tcmba.pagamento_tipo_documento_pagamento as ptdp
                                ON ptdp.cod_entidade = tp.cod_entidade                                                                    
                               AND ptdp.cod_nota     = tp.cod_nota                                                                        
                               AND ptdp.exercicio    = tp.exercicio                                                            
                               AND ptdp.timestamp    = tp.timestamp  
                         LEFT JOIN tcmba.tipo_documento_pagamento 
                                ON tipo_documento_pagamento.cod_tipo = ptdp.cod_tipo
                                 , empenho.nota_liquidacao_paga as nlp                
                         LEFT JOIN (
                                    SELECT exercicio                                                                  
                                         , cod_nota                                                                   
                                         , cod_entidade                                                               
                                         , timestamp                                                                  
                                         , coalesce(sum(nlpa.vl_anulado),0.00) as vl_anulado                          
                                      FROM empenho.nota_liquidacao_paga_anulada as nlpa                               
                                  GROUP BY exercicio, cod_nota, cod_entidade, timestamp                             
                                   ) AS nlpa
                                ON nlp.exercicio    = nlpa.exercicio                                              
                               AND nlp.cod_nota     = nlpa.cod_nota             
                               AND nlp.cod_entidade = nlpa.cod_entidade         
                               AND nlp.timestamp    = nlpa.timestamp 
                             WHERE nlp.cod_entidade = plnlp.cod_entidade                                                                    
                               AND nlp.cod_nota     = plnlp.cod_nota                                                                        
                               AND nlp.exercicio    = plnlp.exercicio_liquidacao                                                            
                               AND nlp.timestamp    = plnlp.timestamp                    
                               AND plnlp.exercicio = '".$this->getDado('exercicio')."'   
                               AND nlp.cod_entidade = tp.cod_entidade                                                                    
                               AND nlp.cod_nota     = tp.cod_nota                                                                        
                               AND nlp.exercicio    = tp.exercicio                                                            
                               AND nlp.timestamp    = tp.timestamp 
                               AND nlpa.cod_nota IS NULL
                          GROUP BY nlp.cod_entidade                                      
                                 , nlp.cod_nota                                          
                                 , nlp.exercicio                                         
                                 , nlpa.vl_anulado                                       
                                 , plnlp.cod_ordem                                       
                                 , plnlp.exercicio                                       
                                 , nlp.timestamp     
                                 , num_documento
                                 , tipo_documento_pagamento.descricao
                                 , ptdp.timestamp                                                   
                          ) AS nota_liq_paga
                       ON pagamento_liquidacao.cod_nota             = nota_liq_paga.cod_nota                       
                      AND pagamento_liquidacao.cod_entidade         = nota_liq_paga.cod_entidade                   
                      AND pagamento_liquidacao.exercicio            = nota_liq_paga.exercicio                      
                      AND pagamento_liquidacao.cod_ordem            = nota_liq_paga.cod_ordem  
                      AND pagamento_liquidacao.exercicio_liquidacao = nota_liq_paga.exercicio_liquidacao  

                     JOIN empenho.empenho
                       ON nota_liquidacao.cod_empenho       = empenho.cod_empenho                          
                      AND nota_liquidacao.exercicio_empenho = empenho.exercicio                            
                      AND nota_liquidacao.cod_entidade      = empenho.cod_entidade                         
                      AND empenho.exercicio                 = '".$this->getDado('exercicio')."' 

                     JOIN empenho.pre_empenho
                       ON empenho.exercicio       = pre_empenho.exercicio                              
                      AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho                        
                      AND empenho.exercicio       = '".$this->getDado('exercicio')."'  

                     JOIN ( SELECT sw_cgm.numcgm
			                           , nom_cgm
                                 , sw_cgm_pessoa_fisica.cpf AS documento
                                 , 1 AS tipo_pessoa
                              FROM sw_cgm
                              JOIN sw_cgm_pessoa_fisica
                                ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                             UNION
                             SELECT sw_cgm.numcgm
                                  , nom_cgm
                                  , sw_cgm_pessoa_juridica.cnpj AS documento
                                  , 2 AS tipo_pessoa
                              FROM sw_cgm
                              JOIN sw_cgm_pessoa_juridica
                                ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                        ) AS cgm_pe
                      ON pre_empenho.cgm_beneficiario = cgm_pe.numcgm

               LEFT JOIN empenho.pre_empenho_despesa
                      ON pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho                       
                     AND pre_empenho.exercicio       = pre_empenho_despesa.exercicio            

               LEFT JOIN orcamento.despesa
                      ON pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                     AND pre_empenho_despesa.exercicio   = despesa.exercicio             

                   WHERE nota_liq_paga.data_pagamento BETWEEN TO_DATE('".$this->getDado('dt_inicial')."' , 'dd/mm/yyyy')
                                                          AND TO_DATE('".$this->getDado('dt_final')."' , 'dd/mm/yyyy')    
                     AND ordem_pagamento_anulada.cod_ordem IS NULL
        ";
        return $stSql;
    }
    
    /**
        * Método Destruct
        * @access Private
    */
    public function __destruct(){}
}


?>