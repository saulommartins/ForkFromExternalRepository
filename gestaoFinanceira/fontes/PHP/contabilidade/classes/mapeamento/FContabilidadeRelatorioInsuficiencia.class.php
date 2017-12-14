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
 * Mapeamento da funcao contabilidade.relatorio_insuficiencia
 *
 * @category    Urbem
 * @package     Contabilidade
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 *
 * $Id: $
 */

include_once CLA_PERSISTENTE;

class FContabilidadeRelatorioInsuficiencia extends Persistente
{
    /**
     * Método Construtor da classe FContabilidadeRelatorioInsuficiencia
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('contabilidade.relatorio_insuficiencia');

        $this->AddCampo('exercicio'   , 'varchar', true, '4', false, false);
        $this->AddCampo('cod_entidade', 'varchar', true, '' , false, false);
        $this->AddCampo('dt_final'    , 'varchar', true, '' , false, false);
    }

    /*
     * Método que constroi a string SQL para o metodo montaRecuperaTodos
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return string $stSql
     */
    public function montaRecuperaTodos()
    {
        $stSql = "
        SELECT *
             , (  saldo
                - restos_processados
                - restos_nao_processados
                - liquidado_a_pagar
                - a_liquidar
               ) AS saldo_inscrito
          FROM (
                    SELECT lpad(cod_recurso::varchar,4,'0') as cod_recurso
                         , tipo
                         , cod_entidade
                         , COALESCE(contabilidade.saldo_conta_banco_recurso('" . $this->getDado('exercicio') . "',cod_recurso,cod_entidade),0) AS saldo
                         , (SUM(total_processados_exercicios_anteriores) + SUM(total_processados_exercicio_anterior)) AS restos_processados
                         , (SUM(total_nao_processados_exercicios_anteriores) + SUM(total_nao_processados_exercicio_anterior)) AS restos_nao_processados
                         , SUM(liquidados_nao_pagos) AS liquidado_a_pagar
                         , SUM(empenhados_nao_liquidados) AS a_liquidar
                      FROM " . $this->getTabela() . "('" . $this->getDado('exercicio') . "','" . $this->getDado('cod_entidade') . "','" . $this->getDado('dt_final') . "') AS tb
                           (  cod_recurso integer
                            , tipo varchar
                            , cod_entidade integer
                            , total_processados_exercicios_anteriores numeric
                            , total_processados_exercicio_anterior numeric
                            , total_nao_processados_exercicios_anteriores numeric
                            , total_nao_processados_exercicio_anterior numeric
                            , liquidados_nao_pagos numeric
                            , empenhados_nao_liquidados numeric
                         )
                  GROUP BY cod_recurso, tipo, cod_entidade

                  ORDER BY cod_recurso, tipo
               ) AS tabela
         WHERE (   saldo > 0
                OR restos_processados > 0
                OR restos_nao_processados > 0
                OR liquidado_a_pagar > 0
                OR a_liquidar > 0
               )
       ";

        return $stSql;
    }

}
