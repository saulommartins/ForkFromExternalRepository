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

/*
    * Classe de mapeamento da tabela tcmgo.nota_fiscal
    * Data de Criação   : 10/02/2009

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor André Machado

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCEPBNotaFiscal extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function TCEPBNotaFiscal()
    {
        parent::Persistente();
        $this->setTabela("tcepb.nota_fiscal");

        $this->setCampoCod('cod_nota');
        $this->setComplementoChave('');

        $this->AddCampo( 'cod_entidade'       , 'integer' , true  , ''     , true  , true   );
        $this->AddCampo( 'cod_nota'            , 'integer' , true  , ''      , true  , false  );
        $this->AddCampo( 'cod_nota_liquidacao' , 'integer' , true  , ''      , true  , false  );
        $this->AddCampo( 'nro_nota'            , 'integer' , true  , ''      , false , false  );
        $this->AddCampo( 'nro_serie'           , 'varchar' , true  , ''      , false , false  );
        $this->AddCampo( 'exercicio'           , 'varchar' , true  , ''      , false , false  );
        $this->AddCampo( 'data_emissao'        , 'date'    , true  , ''      , false , false  );

    }

    public function recuperaTodos(&$rsRecordSet, $stFiltro)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaTodos().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

        return $obErro;
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "SELECT cod_nota_liquidacao                                                 \n";
        $stSql .= "      ,nro_nota                                                            \n";
        $stSql .= "      ,tcepb.nota_fiscal.cod_nota as cod_nota                              \n";
        $stSql .= "      ,nota_liquidacao.cod_nota AS cod_nota_liquidacao                     \n";
        $stSql .= "      ,nro_serie                                                           \n";
        $stSql .= "      ,cod_empenho                                                         \n";
        $stSql .= "      ,to_char(dt_liquidacao,'dd/mm/yyyy') as dt_liquidacao                \n";
        $stSql .= "      ,to_char(data_emissao, 'dd/mm/yyyy') as data_emissao                 \n";
        $stSql .= "      ,tcepb.nota_fiscal.cod_entidade  as cod_entidade                     \n";
        $stSql .= "      ,tcepb.nota_fiscal.exercicio     as exercicio                        \n";
        $stSql .= "      ,COALESCE (empenho.fn_consultar_valor_liquidado(empenho.nota_liquidacao.exercicio,empenho.nota_liquidacao.cod_empenho,empenho.nota_liquidacao.cod_entidade),0.00) -                              \n";
        $stSql .= "       COALESCE (empenho.fn_consultar_valor_liquidado_anulado(empenho.nota_liquidacao.exercicio,empenho.nota_liquidacao.cod_empenho,empenho.nota_liquidacao.cod_entidade),0.00) as vl_associado        \n";
        $stSql .= "  FROM  tcepb.nota_fiscal                                                  \n";
        $stSql .= "INNER JOIN empenho.nota_liquidacao                                         \n";
        $stSql .= "    ON empenho.nota_liquidacao.cod_nota     = tcepb.nota_fiscal.cod_nota   \n";
        $stSql .= "   AND empenho.nota_liquidacao.exercicio    = tcepb.nota_fiscal.exercicio  \n";
        $stSql .= "  AND empenho.nota_liquidacao.cod_entidade = tcepb.nota_fiscal.cod_entidade\n";

        return $stSql;
    }

    /**
     * Método que recupera os dados completos da nota fiscal
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function getNotaFiscal(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT *
              FROM tcepb.nota_fiscal
             WHERE nota_fiscal.cod_nota            = " . $this->getDado('cod_nota'). "
               AND nota_fiscal.cod_nota_liquidacao = " . $this->getDado('cod_nota_liquidacao'). "
               AND nota_fiscal.cod_entidade        = " . $this->getDado('cod_entidade'). "
               AND nota_fiscal.exercicio           = " . $this->getDado('exercicio'). "
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}

?>
