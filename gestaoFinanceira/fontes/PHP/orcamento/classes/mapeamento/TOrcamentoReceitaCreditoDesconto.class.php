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
    * Mapeamento da tabela orcamento.receita_credito_desconto
    * Data de Criação   : 08/09/2008

    * @author Desenvolvedor: Henrique Boaventura

    $Id: $

    * Casos de uso: uc-02.04.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  orcamento.receita_credito_desconto
  * Data de Criação: 08/09/2008
*/

class TOrcamentoReceitaCreditoDesconto extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TOrcamentoReceitaCreditoDesconto()
    {
        parent::Persistente();

        $this->setTabela('orcamento.receita_credito_desconto');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_especie, cod_genero, cod_natureza, cod_credito, cod_receita');

        $this->AddCampo('exercicio'	   		   ,'char'	  ,true , '4', true, true);
        $this->AddCampo('cod_especie'  		   ,'integer' ,true , '' , true, true);
        $this->AddCampo('cod_genero'   		   ,'integer' ,true , '' , true, true);
        $this->AddCampo('cod_natureza' 		   ,'integer' ,true , '' , true, true);
        $this->AddCampo('cod_credito'  		   ,'integer' ,true , '' , true, true);
        $this->AddCampo('cod_receita'  		   ,'integer' ,true , '' , true, true);
        $this->AddCampo('exercicio_dedutora'   ,'char'    ,false, '4', true, true);
        $this->AddCampo('cod_receita_dedutora' ,'integer' ,false, '' , true, true);
        $this->AddCampo('divida_ativa'         ,'boolean' ,true, '' , true, true);
    }

    /**
     * Método que retorna as receitas vinculadas a cada credito de desconto
     *
     * @author    Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param recordset $rsRecordSet
     * @param string    $stFiltro    Filtros alternativos que podem ser passados
     * @param string    $stOrder     Ordenacao do SQL
     * @param boolean   $boTransacao Usar transacao
     *
     * @return recordset
     */
    public function recuperaReceitaCreditoDesconto(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaReceitaCreditoDesconto",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
     * Método que retorna as receitas vinculadas a cada credito de acrescimo
     *
     * @author    Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return string
     */
     function montaRecuperaReceitaCreditoDesconto()
     {
         $stSql = "
            SELECT receita_credito_desconto.exercicio_dedutora
                 , receita_credito_desconto.cod_receita_dedutora
                 , conta_receita.descricao
              FROM orcamento.receita_credito_desconto
        INNER JOIN orcamento.receita
                ON receita.exercicio = receita_credito_desconto.exercicio_dedutora
               AND receita.cod_receita = receita_credito_desconto.cod_receita_dedutora
        INNER JOIN orcamento.conta_receita
                ON conta_receita.exercicio = receita.exercicio
               AND conta_receita.cod_conta = receita.cod_conta
             WHERE ";
         if ($this->getDado('cod_credito') != '') {
             $stSql .= "receita_credito_desconto.cod_credito = ".$this->getDado('cod_credito') ." AND  ";
         }
         if ($this->getDado('cod_natureza') != '') {
             $stSql .= "receita_credito_desconto.cod_natureza = ".$this->getDado('cod_natureza')." AND  ";
         }
         if ($this->getDado('cod_genero') != '') {
             $stSql .= "receita_credito_desconto.cod_genero = ".$this->getDado('cod_genero')." AND  ";
         }
         if ($this->getDado('cod_especie') != '') {
             $stSql .= "receita_credito_desconto.cod_especie = ".$this->getDado('cod_especie')." AND  ";
         }
         if ($this->getDado('exercicio') != '') {
             $stSql .= "receita_credito_desconto.exercicio = ".$this->getDado('exercicio')." AND  ";
         }
         if ($this->getDado('cod_receita') != '') {
             $stSql .= "receita_credito_desconto.cod_receita = ".$this->getDado('cod_receita')." AND  ";
         }
         if ($this->getDado('divida_ativa') != '') {
             $stSql .= "receita_credito_desconto.divida_ativa = '".$this->getDado('divida_ativa')."' AND  ";
         }

         return substr($stSql,0,-6);
     }

}
?>
