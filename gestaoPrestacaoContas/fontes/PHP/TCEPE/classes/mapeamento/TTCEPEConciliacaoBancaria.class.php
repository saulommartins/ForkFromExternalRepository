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
    * Data de Criação: 09/10/2014
    * @author Analista: 
    * @author Desenvolvedor: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEPEConciliacaoBancaria extends Persistente
{

    public function montaRecuperaTodos() 
    {
        $stSql  = "
         SELECT '1' as forma_conciliacao                                                             
              , case when conciliacao.vl_extrato != 0 then                                           
                  'Conciliacao bancaria conforme extrato do dia'                                    
                else 'Sem saldo a conciliar' end as descricao                                         
              , trim(upper(replace(plano_banco.conta_corrente,'-',''))) as conta_corrente           
              , to_char(conciliacao.dt_extrato, 'dd/mm/yyyy') as data_fato                          
              , to_char(conciliacao.dt_extrato, 'yymm') as sequencial   
              , '' as nro_documento                            
              , LPAD(REPLACE(conciliacao.vl_extrato::VARCHAR,'.',','), 16, '0') as valor_conciliado 
              , plano_banco_tipo_conta_banco.cod_tipo_conta_banco
              , 0 as tipo_documento_bancario
              , conciliacao.cod_plano 

           FROM tesouraria.conciliacao


     INNER JOIN contabilidade.plano_banco
             ON plano_banco.cod_plano     = conciliacao.cod_plano                                   
            AND plano_banco.exercicio     = conciliacao.exercicio

     INNER JOIN monetario.banco
             ON plano_banco.cod_banco = banco.cod_banco    

      LEFT JOIN tcepe.plano_banco_tipo_conta_banco
             ON plano_banco_tipo_conta_banco.exercicio  = plano_banco.exercicio
            AND plano_banco_tipo_conta_banco.cod_plano  = plano_banco.cod_plano

          WHERE conciliacao.mes       = '".$this->getDado('mes')."'                     
            AND conciliacao.exercicio = '".$this->getDado('exercicio')."'               
           -- AND banco.num_banco       != '000'                                                        
           -- AND banco.num_banco       != '999'                                                        
            AND plano_banco.cod_entidade IN (".$this->getDado('cod_entidade').")  ";

        return $stSql;
    }

}


/*
    function montaRecuperaTodos()
    {
        $stSql = "  SELECT                                                                          
                            lpad(regexp_replace(plano_banco.conta_corrente,'[.|-]','','gi'),12,'0') as conta_corrente           
                            , 1 as sequencial    
                            , 0 as forma_conciliacao
                            , conciliacao_lancamento_manual.descricao
                            , to_char(conciliacao.dt_extrato, 'ddmmyyyy') as data_fato                          
                            , '' as nro_documento -- campo em branco segundo combinado com Ane Pereira e Silvia Martins para ser analisado posteriormente.
                            , REPLACE(conciliacao.vl_extrato::VARCHAR,'.',',') as valor_conciliado 
                            , plano_banco_tipo_conta_banco.cod_tipo_conta_banco
                            , 0 as tipo_documento_bancario
                            
                    FROM tesouraria.conciliacao
                    
                    JOIN contabilidade.plano_banco
                      ON plano_banco.cod_plano     = conciliacao.cod_plano                                   
                     AND plano_banco.exercicio     = conciliacao.exercicio
                     
               LEFT JOIN tcepe.plano_banco_tipo_conta_banco
                      ON plano_banco_tipo_conta_banco.exercicio  = plano_banco.exercicio
                     AND plano_banco_tipo_conta_banco.cod_plano  = plano_banco.cod_plano
                     
                    JOIN monetario.conta_corrente
                      ON conta_corrente.cod_banco          = plano_banco.cod_banco
                     AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                     AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                     
               LEFT JOIN tesouraria.cheque
                      ON cheque.cod_agencia        = conta_corrente.cod_agencia
                     AND cheque.cod_banco          = conta_corrente.cod_banco
                     AND cheque.cod_conta_corrente = conta_corrente.cod_conta_corrente
                     
               LEFT JOIN tesouraria.cheque_emissao
                      ON cheque_emissao.cod_agencia        = cheque.cod_agencia
                     AND cheque_emissao.cod_banco          = cheque.cod_banco
                     AND cheque_emissao.cod_conta_corrente = cheque.cod_conta_corrente
                     AND cheque_emissao.num_cheque         = cheque.num_cheque
                     
               LEFT JOIN tesouraria.conciliacao_lancamento_contabil
                      ON conciliacao_lancamento_contabil.cod_plano             = conciliacao.cod_plano
                     AND conciliacao_lancamento_contabil.exercicio_conciliacao = conciliacao.exercicio
                     AND conciliacao_lancamento_contabil.mes                   = conciliacao.mes
                     
               LEFT JOIN tesouraria.conciliacao_lancamento_manual
                      ON conciliacao_lancamento_manual.cod_plano = conciliacao.cod_plano
                     AND conciliacao_lancamento_manual.exercicio = conciliacao.exercicio
                     AND conciliacao_lancamento_manual.mes = conciliacao.mes
                     
                   WHERE conciliacao.mes = ".$this->getDado('mes')."
                     AND conciliacao.exercicio = '".$this->getDado('exercicio')."'
                     AND plano_banco.cod_entidade IN (".$this->getDado('cod_entidade').")
                GROUP BY conta_corrente, conciliacao_lancamento_manual.descricao, cod_tipo_conta_banco, data_fato, valor_conciliado        
            ";
            
        return $stSql;

        
        Ver com a Silvia o relacionamento da Tabela Interna 15
            1 Saldo conforme extrato bancário
            2 Entrada não considerada pelo banco
            3 Saída não considerada pela contabilidade
            4 Entrada não considerada pela contabilidade
            5 Saída não considerada pelo banco
    
        Dados que temos no sistemas
            X
            S
            L
            I
            M
            P
            T
            E
            A

        */

?>