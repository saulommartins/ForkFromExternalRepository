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
    $Id: TTCEPEDotacao.class.php 60202 2014-10-06 20:02:40Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEDotacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPEDotacao()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = " SELECT despesa.exercicio   
                        , lpad(despesa.num_orgao::varchar, 2, '0')||lpad(despesa.num_unidade::varchar, 2, '0') as unidade   
                        , despesa.cod_funcao   
                        , despesa.cod_subfuncao   
                        , programa.num_programa AS cod_programa
                        , acao.num_acao
                        , CASE 
                            -- PROJETOS
                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(despesa.exercicio,acao.num_acao)) = 1 )               
                                THEN 1                                                                                                               
                            --ATIVIDADE
                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(despesa.exercicio,acao.num_acao)) = 2 )                            
                                THEN 2
                            --OPERACOES ESPECIAIS
                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(despesa.exercicio,acao.num_acao)) = 3 )
                                THEN 9                           
                        END AS tipo_acao  
                        , CASE WHEN codigo_fonte_recurso.cod_fonte IS NULL 
                            THEN '00'
                        END as cod_fonte
                        , substr(replace(cod_estrutural,'.',''),1,1) as categoria_economica   
                        , substr(replace(cod_estrutural,'.',''),2,1) as natureza   
                        , substr(replace(cod_estrutural,'.',''),3,2) as modalidade
                        , substr(replace(cod_estrutural,'.',''),5,2) as elemento   
                        , replace(SUM(vl_original)::varchar,'.',',') AS vl_original  
                  
                FROM orcamento.despesa 
            
                JOIN orcamento.conta_despesa AS cont
                     ON despesa.exercicio = cont.exercicio
                    AND despesa.cod_conta = cont.cod_conta
            
                JOIN orcamento.despesa_acao
                     ON despesa_acao.exercicio_despesa = despesa.exercicio
                    AND despesa_acao.cod_despesa       = despesa.cod_despesa

                JOIN ppa.acao
                     ON acao.cod_acao            = despesa_acao.cod_acao

                JOIN ppa.programa
                     ON programa.cod_programa    = acao.cod_programa

                JOIN orcamento.recurso
                     ON recurso.exercicio   = despesa.exercicio
                    AND recurso.cod_recurso = despesa.cod_recurso
                --retirar o LEFT quando configuracao estiver pronta
                LEFT JOIN tcepe.codigo_fonte_recurso
                     ON codigo_fonte_recurso.cod_recurso    = recurso.cod_recurso
                    AND codigo_fonte_recurso.exercicio  = recurso.exercicio
                   
                 WHERE 1=1 ";
                 
    if ( $this->getDado('exercicio') ) {
        $stSql .= " AND despesa.exercicio = '".$this->getDado('exercicio')."' \n";
    }
    if ( $this->getDado('cod_entidade') ) {
        $stSql .= " AND despesa.cod_entidade in (".$this->getDado('cod_entidade').")  \n";
    }
                 
    $stSql .=  "
             GROUP BY despesa.exercicio
                  , despesa.num_orgao
                  , despesa.num_unidade
                  , despesa.cod_funcao
                  , despesa.cod_subfuncao
                  , programa.num_programa
                  , acao.num_acao
                  , codigo_fonte_recurso.cod_fonte
                  , cod_estrutural
                  
             ORDER BY   despesa.exercicio
                  , despesa.num_orgao
                  , despesa.num_unidade
                  , despesa.cod_funcao
                  , despesa.cod_subfuncao
                  , programa.num_programa
                  , acao.num_acao
                  , codigo_fonte_recurso.cod_fonte

        ";
        return $stSql;
    }
}

?>