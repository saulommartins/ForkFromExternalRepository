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
    * Classe de mapeamento da tabela ORCAMENTO.RECEITA
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: eduardoschitz $
    $Date: 2008-03-11 16:01:21 -0300 (Ter, 11 Mar 2008) $

    * Casos de uso: uc-02.01.06, uc-02.04.04, uc-02.01.34, uc-02.04.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORCAMENTO.RECEITA
  * Data de Criação: 13/07/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Marcelo B. Paulino

*/
class TOrcamentoReceitaCredito extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TOrcamentoReceitaCredito()
    {
        parent::Persistente();

        $this->setTabela('orcamento.receita_credito');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_especie, cod_genero, cod_natureza, cod_credito, divida_ativa');

        $this->AddCampo('exercicio'   ,'char'   ,true,'4',true,true);
        $this->AddCampo('cod_especie' ,'integer',true,'' ,true,true);
        $this->AddCampo('cod_genero'  ,'integer',true,'' ,true,true);
        $this->AddCampo('cod_natureza','integer',true,'' ,true,true);
        $this->AddCampo('cod_credito' ,'integer',true,'' ,true,true);
        $this->AddCampo('cod_receita' ,'integer',true,'' ,false,true);
        $this->AddCampo('divida_ativa','boolean',true,'' ,true,true);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = "
                SELECT receita_credito.*
                     , conta_receita.descricao
                  FROM orcamento.receita_credito
            INNER JOIN orcamento.receita
                    ON receita.exercicio = receita_credito.exercicio
                   AND receita.cod_receita = receita_credito.cod_receita
            INNER JOIN orcamento.conta_receita
                    ON conta_receita.exercicio = receita.exercicio
                   AND conta_receita.cod_conta = receita.cod_conta
                 WHERE ";
         if ($this->getDado('cod_genero') != '') {
             $stSql .= "receita_credito.cod_genero = ".$this->getDado('cod_genero')." AND  ";
         }
         if ($this->getDado('cod_especie') != '') {
             $stSql .= "receita_credito.cod_especie = ".$this->getDado('cod_especie')." AND  ";
         }
         if ($this->getDado('cod_natureza') != '') {
             $stSql .= "receita_credito.cod_natureza = ".$this->getDado('cod_natureza')." AND  ";
         }
         if ($this->getDado('cod_credito') != '') {
             $stSql .= "receita_credito.cod_credito = ".$this->getDado('cod_credito')." AND  ";
         }
         if ($this->getDado('exercicio') != '') {
             $stSql .= "receita_credito.exercicio = ".$this->getDado('exercicio')." AND  ";
         }
         if ($this->getDado('cod_receita') != '') {
             $stSql .= "receita_credito.cod_receita = ".$this->getDado('cod_receita')." AND  ";
         }
         if ($this->getDado('divida_ativa') != '') {
             $stSql .= "receita_credito.divida_ativa = ".$this->getDado('divida_ativa')." AND  ";
         }

         return substr($stSql,0,-6);
    }

    /**
     * Valida credito/acrescimo, verificando se ja não esta vinculado a outra receita/conta
     */
    public function recuperaClassReceitasCreditosValidacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
            return $this->executaRecupera("montaRecuperaClassReceitasCreditosValidacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaClassReceitasCreditosValidacao()
    {
        $stSql = "  select tbl.cod_credito ";
        $stSql .= "   from contabilidade.plano_analitica_credito as tbl ";

        $stSql .= "	where tbl.cod_credito = " . $this->getDado('cod_credito') ."
                                    and tbl.cod_especie = " . $this->getDado('cod_especie') ."
                                    and tbl.cod_genero = " . $this->getDado('cod_genero') ."
                                    and tbl.cod_natureza = " . $this->getDado('cod_natureza') ."";

        return $stSql;
    }

    public function recuperaClassReceitasCreditosValidacaoOrcamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
            return $this->executaRecupera("montaRecuperaClassReceitasCreditosValidacaoOrcamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaClassReceitasCreditosValidacaoOrcamento()
    {
        $stSql = "
            SELECT receita_credito.cod_credito
              FROM orcamento.receita_credito
             WHERE receita_credito.cod_credito  = ".$this->getDado('cod_credito')."
               AND receita_credito.cod_especie  = ".$this->getDado('cod_especie')."
               AND receita_credito.cod_genero   = ".$this->getDado('cod_genero')."
               AND receita_credito.cod_natureza = ".$this->getDado('cod_natureza')."
               AND receita_credito.divida_ativa = '".$this->getDado('divida_ativa')."'
               AND receita_credito.cod_receita != ".$this->getDado('codigo')."
               AND receita_credito.exercicio    = ".$this->getDado('exercicio')."
        ";

        return $stSql;
    }

    public function recuperaDadosCredito(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDadosCredito",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDadosCredito()
    {
        $stSql = "
            SELECT  receita_credito.exercicio
                ,   receita_credito.cod_especie
                ,   receita_credito.cod_genero
                ,   receita_credito.cod_natureza
                ,   receita_credito.cod_credito
                ,   receita_credito.cod_receita
                ,   receita_credito.divida_ativa
                ,   receita_credito.cod_credito||'.'||receita_credito.cod_especie||'.'||receita_credito.cod_genero||'.'||receita_credito.cod_natureza as codigo
                ,   credito.descricao_credito AS descricao
              FROM  orcamento.receita_credito
        INNER JOIN  monetario.credito
                ON  credito.cod_credito  = receita_credito.cod_credito
                AND credito.cod_natureza = receita_credito.cod_natureza
                AND credito.cod_genero   = receita_credito.cod_genero
                AND credito.cod_especie  = receita_credito.cod_especie
        WHERE ";
         if ($this->getDado('cod_genero') != '') {
             $stSql .= "receita_credito.cod_genero = ".$this->getDado('cod_genero')." AND  ";
         }
         if ($this->getDado('cod_especie') != '') {
             $stSql .= "receita_credito.cod_especie = ".$this->getDado('cod_especie')." AND  ";
         }
         if ($this->getDado('cod_natureza') != '') {
             $stSql .= "receita_credito.cod_natureza = ".$this->getDado('cod_natureza')." AND  ";
         }
         if ($this->getDado('cod_credito') != '') {
             $stSql .= "receita_credito.cod_credito = ".$this->getDado('cod_credito')." AND  ";
         }
         if ($this->getDado('exercicio') != '') {
             $stSql .= "receita_credito.exercicio = ".$this->getDado('exercicio')." AND  ";
         }
         if ($this->getDado('cod_receita') != '') {
             $stSql .= "receita_credito.cod_receita = ".$this->getDado('cod_receita')." AND  ";
         }
         if ($this->getDado('divida_ativa') != '') {
             $stSql .= "receita_credito.divida_ativa = ".$this->getDado('divida_ativa')." AND  ";
         }

        return substr($stSql,0,-6);
    }

}
