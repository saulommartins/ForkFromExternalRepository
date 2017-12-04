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
    * Data de Criação   : 02/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Evandro Melos
    $Id: TTCEPERelacionamentoReceitaOrcamentaria.class.php 60505 2014-10-24 16:22:51Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPERelacionamentoReceitaOrcamentaria extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPERelacionamentoReceitaOrcamentaria()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = "  SELECT 
                              SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,11) as cod_receita_gestora
                            , regexp_replace(conta_receita.descricao, '[?|\–]', '-', 'gi')as denominacao
                            , CASE WHEN configuracao_lancamento_receita.cod_conta_receita IS NULL
                                    THEN '0'
                                ELSE
                                    REPLACE(plano_conta.cod_estrutural,'.','')
                            END as cod_conta
                            , SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,11) as cod_receita_orcamentaria
                    FROM orcamento.receita
                    JOIN orcamento.conta_receita            
                      ON conta_receita.exercicio  = receita.exercicio
                     AND conta_receita.cod_conta = receita.cod_conta     
               LEFT JOIN contabilidade.configuracao_lancamento_receita
                      ON configuracao_lancamento_receita.exercicio        = conta_receita.exercicio
                     AND configuracao_lancamento_receita.cod_conta_receita   = conta_receita.cod_conta
               LEFT JOIN contabilidade.plano_conta
		      ON plano_conta.cod_conta = configuracao_lancamento_receita.cod_conta
                   WHERE receita.cod_recurso IS NOT NULL 
                     AND receita.exercicio = '".$this->getDado('exercicio')."'
                     AND receita.cod_entidade IN (".$this->getDado('cod_entidade').")
                     AND configuracao_lancamento_receita.exercicio = '".$this->getDado('exercicio')."'
                     AND configuracao_lancamento_receita.estorno = 'f'
                GROUP BY cod_receita_gestora,
                         denominacao,
                         conta_receita.cod_estrutural,
                         plano_conta.cod_estrutural,
                         configuracao_lancamento_receita.cod_conta_receita,
                         conta_receita.descricao
                ORDER BY conta_receita.cod_estrutural
        
        ";
        return $stSql;
    }
}

?>