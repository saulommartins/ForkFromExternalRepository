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
    * Mapeamento da tabela orcamento.receita_credito_tributario
    * Data de Criação   : 16/04/2014

    * @author Desenvolvedor: Eduardo Schitz

    $Id: TOrcamentoReceitaCreditoTributario.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  orcamento.receita_credito_desconto
  * Data de Criação: 16/04/2014
*/

class TOrcamentoReceitaCreditoTributario extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TOrcamentoReceitaCreditoTributario()
    {
        parent::Persistente();

        $this->setTabela('orcamento.receita_credito_tributario');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_receita, exercicio');

        $this->AddCampo('cod_receita' ,'integer', true, '' , true , true);
        $this->AddCampo('exercicio'	  ,'char'	, true, '4', true , true);
        $this->AddCampo('cod_conta'   ,'integer', true, '' , false, true);
    }

    /**
     * Método que retorna a conta crédito tributário da receita
     *
     * @author    Eduardo Schitz <eduardo.schitz@cnm.org.br>
     * @param recordset $rsRecordSet
     * @param string    $stFiltro    Filtros alternativos que podem ser passados
     * @param string    $stOrder     Ordenacao do SQL
     * @param boolean   $boTransacao Usar transacao
     *
     * @return recordset
     */
    public function recuperaContaCreditoTributario(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContaCreditoTributario",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
     * Método que retorna a conta crédito tributário da receita
     *
     * @author    Eduardo Schitz <eduardo.schitz@cnm.org.br>
     *
     * @return string
     */
     function montaRecuperaContaCreditoTributario()
     {
         $stSql = "
            SELECT receita_credito_tributario.cod_conta
                 , receita_credito_tributario.exercicio
                 , plano_conta.nom_conta
                 , plano_conta.cod_estrutural
                 , plano_analitica.cod_plano
              FROM orcamento.receita_credito_tributario
        INNER JOIN contabilidade.plano_conta
                ON plano_conta.cod_conta = receita_credito_tributario.cod_conta
               AND plano_conta.exercicio = receita_credito_tributario.exercicio
        INNER JOIN contabilidade.plano_analitica
                ON plano_conta.cod_conta = plano_analitica.cod_conta
               AND plano_conta.exercicio = plano_analitica.exercicio
               
             WHERE ";
         if ($this->getDado('cod_receita') != '') {
             $stSql .= "receita_credito_tributario.cod_receita = ".$this->getDado('cod_receita') ." AND  ";
         }

         if ($this->getDado('exercicio') != '') {
             $stSql .= "receita_credito_tributario.exercicio = '".$this->getDado('exercicio') ."' AND  ";
         }

         return substr($stSql,0,-6);
     }

}
?>
