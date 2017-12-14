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
    * Data de Criação   : 01/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Evandro Melos
    $Id: TTCEPEAcao.class.php 60271 2014-10-09 19:01:36Z carlos.silva $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEAcao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPEAcao()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql =" SELECT                                                                         
                        LPAD(acao.num_acao::varchar,6,'0') as num_acao                      
                        ,acao_dados.titulo as denominacao
                        , CASE 
                            -- PROJETOS
                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,acao.num_acao)) = 1 )               
                                THEN 1                                                                                                               
                            --ATIVIDADE
                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,acao.num_acao)) = 2 )                            
                                THEN 2
                            --OPERACOES ESPECIAIS
                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,acao.num_acao)) = 3 )
                                THEN 9                           
                        END AS tipo_acao
                                                 
                FROM orcamento.pao                                                                     
                
                JOIN orcamento.pao_ppa_acao                                                            
                  ON pao_ppa_acao.exercicio=pao.exercicio                                              
                 AND pao_ppa_acao.num_pao=pao.num_pao                                                  

                JOIN ppa.acao                                                                          
                  ON acao.cod_acao=pao_ppa_acao.cod_acao                                               

                JOIN ppa.programa                                                                          
                  ON programa.cod_programa=acao.cod_programa
                
                JOIN ppa.programa_setorial                                                                      
                  ON programa_setorial.cod_setorial=programa.cod_setorial
                
                JOIN ppa.macro_objetivo                                                                      
                  ON macro_objetivo.cod_macro=programa_setorial.cod_macro
                
                JOIN ppa.ppa                                                                      
                  ON ppa.cod_ppa=macro_objetivo.cod_ppa
                    
                JOIN ppa.acao_dados                                                                    
                  ON acao_dados.cod_acao=acao.cod_acao                                                 
                 AND acao_dados.timestamp_acao_dados=acao.ultimo_timestamp_acao_dados                  

                JOIN administracao.unidade_medida                                                      
                  ON unidade_medida.cod_unidade=acao_dados.cod_unidade_medida                          
                 AND unidade_medida.cod_grandeza=acao_dados.cod_grandeza          
                
                WHERE pao.exercicio = '".$this->getDado('exercicio')."'
                  AND ppa.ano_inicio::INTEGER <= ".$this->getDado('exercicio')." 
                  AND ppa.ano_final::INTEGER  >= ".$this->getDado('exercicio')."
        ";
        return $stSql;
    }
}

?>