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
    * Data de Criação: 18/04/2007

    * @author Analista: Lucas Stephanou
    * @author Desenvolvedor: Lucas Stephanou

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.5  2007/05/29 14:18:33  domluc
*** empty log message ***

Revision 1.4  2007/05/29 14:17:46  domluc
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBCancelamentoRestos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBCancelamentoRestos()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{
    $stSql = "SELECT e.exercicio AS ano_empenho
                   , lpad(despesa.num_orgao::VARCHAR,2,'0') || lpad(despesa.num_unidade::VARCHAR,2,'0') || '' AS unidade_orcamentaria 
                   , e.cod_empenho AS num_empenho
                   , nlpa.cod_nota AS num_estorno  
                   , to_char(nlpa.timestamp_anulada,'ddmmyyyy') AS data_estorno
                   , nlpa.vl_anulado AS valor_estorno
                   , nlpa.observacao AS motivo
                   , 'S' AS liquidado
                FROM empenho.empenho AS e 
                JOIN  empenho.nota_liquidacao nl
                  ON nl.exercicio = e.exercicio
                 AND nl.cod_entidade = e.cod_entidade     
                 AND nl.cod_empenho = e.cod_empenho  
                JOIN empenho.nota_liquidacao_paga nlp
                  ON nl.exercicio = nlp.exercicio
                 AND nl.cod_nota = nlp.cod_nota
                 AND nl.cod_entidade = nlp.cod_entidade            
                JOIN empenho.nota_liquidacao_paga_anulada nlpa
                  ON nlp.exercicio = nlpa.exercicio
                 AND nlp.cod_nota = nlpa.cod_nota
                 AND nlp.cod_entidade = nlpa.cod_entidade
                 AND nlp.timestamp = nlpa.timestamp
                 AND to_date(to_char(nlpa.timestamp_anulada,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('01/".$this->getDado('inMes')."/".$this->getDado('exercicio')."', 'DD/MM/YYYY')::TIMESTAMP AND (TO_DATE('01/".$this->getDado('inMes')."/".$this->getDado('exercicio')."', 'DD/MM/YYYY') + INTERVAL '1 MONTH')	
          INNER JOIN  ( SELECT  pre_empenho.exercicio
                             ,  pre_empenho.cod_pre_empenho
                             ,  CASE WHEN ( pre_empenho.implantado = true )
                                     THEN restos_pre_empenho.num_orgao
                                     ELSE despesa.num_orgao
                                END as num_orgao
                             ,  CASE WHEN ( pre_empenho.implantado = true )
                                     THEN restos_pre_empenho.num_unidade
                                     ELSE despesa.num_unidade
                                END as num_unidade
                          FROM  empenho.pre_empenho
                     LEFT JOIN  empenho.restos_pre_empenho
                            ON  restos_pre_empenho.exercicio = pre_empenho.exercicio
                           AND  restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     LEFT JOIN  empenho.pre_empenho_despesa
                            ON  pre_empenho_despesa.exercicio = pre_empenho.exercicio
                           AND  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     LEFT JOIN  orcamento.despesa
                            ON  despesa.exercicio = pre_empenho_despesa.exercicio
                           AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                    ) AS despesa
              ON  despesa.exercicio = e.exercicio
             AND  despesa.cod_pre_empenho = e.cod_pre_empenho     

           WHERE nl.exercicio < '".$this->getDado('exercicio')."'";
            
    return $stSql;
}
}
