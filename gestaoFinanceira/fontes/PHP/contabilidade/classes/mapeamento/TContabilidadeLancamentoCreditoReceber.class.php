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
    * Mapeamento da tabela contabilidade.receita_credito_tributario
    * Data de Criação   : 16/04/2014

    * @author Desenvolvedor: Carlos Adriano

    $Id: TContabilidadeLancamentoCreditoReceber.class.php 64362 2016-01-26 19:45:10Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela contabilidade.receita_credito_tributario
  * Data de Criação: 03/10/2014
*/

class TContabilidadeLancamentoCreditoReceber extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TContabilidadeLancamentoCreditoReceber()
    {
        parent::Persistente();

        $this->setTabela('contabilidade.receita_credito_tributario');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_receita, exercicio');

        $this->AddCampo('cod_receita' ,'integer', true, '' , true , true);
        $this->AddCampo('exercicio'	  ,'char'	, true, '4', true , true);
        $this->AddCampo('cod_conta'   ,'integer', true, '' , false, true);
    }

    /**
     * Método que retorna a conta crédito tributário da receita
     *
     * @author    Carlos Adriano <carlos.silva@cnm.org.br>
     * @param recordset $rsRecordSet
     * @param string    $stFiltro    Filtros alternativos que podem ser passados
     * @param string    $stOrder     Ordenacao do SQL
     * @param boolean   $boTransacao Usar transacao
     *
     * @return recordset
     */
    public function executaInsereLancamento($arParam, $boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->insereLancamento($arParam);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    /**
     * Método que retorna a conta crédito tributário da receita
     *
     * @author    Carlos Adriano <carlos.silva@cnm.org.br>
     * @return string
     */
    function insereLancamento($arParam) {
       $stSql = "SELECT contabilidade.fn_insere_lancamentos(
                       '".$arParam['exercicio']."',
                       ".$arParam['cod_plano_deb'].",
                       ".$arParam['cod_plano_cred'].",
                       '',
                       '',
                       ".$arParam['valor'].",
                       ".$arParam['cod_lote'].",
                       ".$arParam['cod_entidade'].",
                       ".$arParam['cod_historico'].",
                       'M',
                       '".$arParam['exercicio']."'
                   )";
       
       return $stSql;
   }
}
