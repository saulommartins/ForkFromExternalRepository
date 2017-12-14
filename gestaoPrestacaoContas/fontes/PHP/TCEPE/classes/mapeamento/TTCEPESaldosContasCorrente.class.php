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
    * 
    * Data de Criação   : 21/10/2014

    * @author Analista: Silvia Martins
    * @author Desenvolvedor:  Lisiane Morais
    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPESaldosContasCorrente extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPESaldosContasCorrente()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = "
           SELECT REPLACE(retorno.cod_estrutural, '.', '')  AS cod_conta_contabil  
		, plano_banco_tipo_conta_banco.cod_tipo_conta_banco AS cod_cc                                                                                                                                                                                                                                                      
                , CASE WHEN retorno.vl_saldo_anterior < 0.00 THEN COALESCE((retorno.vl_saldo_anterior*-1), 0,00) ELSE COALESCE(retorno.vl_saldo_anterior, 0,00) END AS saldo_inicial
                , CASE WHEN  retorno.vl_saldo_anterior < 0 THEN 'C' WHEN  retorno.vl_saldo_anterior = 0 THEN ' ' ELSE 'D' END AS natureza_saldo_inicial                                                          
                , CASE WHEN retorno.vl_saldo_debitos < 0.00 THEN COALESCE((retorno.vl_saldo_debitos*-1), 0,00) ELSE COALESCE(retorno.vl_saldo_debitos, 0,00) END AS movimento_debito                                                              
                , CASE WHEN retorno.vl_saldo_creditos < 0.00 THEN COALESCE((retorno.vl_saldo_creditos*-1), 0,00) ELSE COALESCE(retorno.vl_saldo_creditos, 0,00) END AS movimento_credito                                                                  
                , CASE WHEN retorno.vl_saldo_atual < 0.00 THEN COALESCE((retorno.vl_saldo_atual*-1), 0,00) ELSE COALESCE(retorno.vl_saldo_atual, 0,00) END AS saldo_final 
                , CASE WHEN  retorno.vl_saldo_atual < 0 THEN 'C' WHEN  retorno.vl_saldo_atual = 0 THEN ' ' ELSE 'D' END AS natureza_saldo_final  ";
                
             if ( $this->getDado('competencia') == 12 OR $this->getDado('competencia') == 13 ) { 
                    $stSql .= "
                        , CASE WHEN final_anual.vl_saldo_debitos < 0.00 THEN COALESCE((final_anual.vl_saldo_debitos*-1), 0,00) ELSE COALESCE(final_anual.vl_saldo_debitos,0.00) END AS movimento_debito_encerramento                                                               
                        , CASE WHEN final_anual.vl_saldo_creditos < 0.00 THEN COALESCE((final_anual.vl_saldo_creditos*-1), 0,00) ELSE COALESCE(final_anual.vl_saldo_creditos,0.00) END AS movimento_credito_encerramento ";
            } else {
                $stSql .= "
                        , '' AS movimento_debito_encerramento     
                        , '' AS movimento_credito_encerramento ";
            }
            
        $stSql .= "
            FROM                                                                                        
                tcepe.saldo_contas_contabeis('".$this->getDado('exercicio')."',
                                                          'cod_entidade IN (".$this->getDado('cod_entidade').")',
                                                          '".$this->getDado('dt_inicial')."',
                                                          '".$this->getDado('dt_final')."')
                as retorno( cod_estrutural varchar  
                            ,nivel integer
                            ,nom_conta varchar
                            ,cod_sistema integer
                            ,indicador_superavit char(12)  
                            ,exercicio char(4)                                 
                            ,vl_saldo_anterior numeric                                                   
                            ,vl_saldo_debitos  numeric                                                   
                            ,vl_saldo_creditos numeric                                                   
                            ,vl_saldo_atual    numeric                                                  
                        )
            JOIN contabilidade.plano_conta as pc
              ON pc.cod_estrutural =  retorno.cod_estrutural
             AND pc.exercicio = retorno.exercicio

	    JOIN contabilidade.plano_analitica AS pa
              ON pa.exercicio = pc.exercicio
             AND pa.cod_conta = pc.cod_conta
               
            JOIN contabilidade.plano_banco AS pb
              ON pb.exercicio = pa.exercicio
             AND pb.cod_plano = pa.cod_plano

            JOIN monetario.conta_corrente as cc
              ON cc.cod_banco = pb.cod_banco
             AND cc.cod_agencia = pb.cod_agencia
             AND cc.cod_conta_corrente = pb.cod_conta_corrente

       LEFT JOIN tcepe.plano_banco_tipo_conta_banco
              ON plano_banco_tipo_conta_banco.cod_plano = pb.cod_plano
             AND plano_banco_tipo_conta_banco.exercicio = pb.exercicio ";
             
        if ( $this->getDado('competencia') == 12 OR $this->getDado('competencia') == 13 ) {
            $stSql .= "
                   JOIN(
                        SELECT *
                          FROM contabilidade.fn_rl_balancete_verificacao('".$this->getDado('exercicio')."',
                                                                       'cod_entidade IN (".$this->getDado('cod_entidade').")',
                                                                       '01/01/".$this->getDado('exercicio')."','31/12/".$this->getDado('exercicio')."','A'::CHAR)
                            as retorno( cod_estrutural varchar                                                      
                                        ,nivel integer                                                               
                                        ,nom_conta varchar                                                           
                                        ,cod_sistema integer                                                         
                                        ,indicador_superavit char(12)                                                
                                        ,vl_saldo_anterior numeric                                                   
                                        ,vl_saldo_debitos  numeric                                                   
                                        ,vl_saldo_creditos numeric                                                   
                                        ,vl_saldo_atual    numeric                                                   
                                    )
                        ) AS final_anual
                    ON final_anual.cod_estrutural = retorno.cod_estrutural";
        }
        
        return $stSql;
    }
}

?>