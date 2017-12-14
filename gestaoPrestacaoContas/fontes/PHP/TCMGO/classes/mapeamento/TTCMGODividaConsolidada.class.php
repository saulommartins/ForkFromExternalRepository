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
    * Classe de mapeamento da tabela tcmgo.divida_consolidada
    * Data de Criação   : 30/01/2012

    * @author Analista      Carlos Adriano
    * @author Desenvolvedor Carlos Adriano

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGODividaConsolidada extends Persistente
{
    /**
      * Método Construtor
      * @access Private
      */
    public function TTCMGODividaConsolidada()
    {
        parent::Persistente();
        $this->setTabela("tcmgo.divida_consolidada");

        $this->setCampoCod('num_unidade');
        $this->setComplementoChave('num_orgao, exercicio, tipo_lancamento');

        $this->AddCampo( 'exercicio'           , 'char'    , true  , '4'    , true  , true   );
        $this->AddCampo( 'dt_inicio'           , 'date'    , true  , '4'    , true  , true   );
        $this->AddCampo( 'dt_fim'              , 'date'    , true  , '4'    , true  , true   );
        $this->AddCampo( 'num_unidade'         , 'integer' , true  , ''     , true  , true   );
        $this->AddCampo( 'num_orgao'           , 'integer' , true  , ''     , true  , true   );
        $this->AddCampo( 'numcgm'              , 'integer' , true  , ''     , false , true   );
        $this->AddCampo( 'tipo_lancamento'     , 'integer' , true  , ''     , true  , true   );
        $this->AddCampo( 'nro_lei_autorizacao' , 'char'    , true  , '7'    , false , false  );
        $this->AddCampo( 'dt_lei_autorizacao'  , 'date'    , true  , ''     , false , false  );
        $this->AddCampo( 'vl_saldo_anterior'   , 'numeric' , true  , '14,2' , false , false  );
        $this->AddCampo( 'vl_contratacao'      , 'numeric' , true  , '14,2' , false , false  );
        $this->AddCampo( 'vl_amortizacao'      , 'numeric' , true  , '14,2' , false , false  );
        $this->AddCampo( 'vl_cancelamento'     , 'numeric' , true  , '14,2' , false , false  );
        $this->AddCampo( 'vl_encampacao'        , 'numeric' , true  , '14,2' , false , false  );
        $this->AddCampo( 'vl_atualizacao'      , 'numeric' , true  , '14,2' , false , false  );
        $this->AddCampo( 'vl_saldo_atual'      , 'numeric' , true  , '14,2' , false , false  );

    }

    public function recuperaDividaPorMes(&$rsRecordSet, $stFiltro="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDividaPorMes().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

        return $obErro;
    }

    public function montaRecuperaDividaPorMes()
    {
        $stSql  = " SELECT *                                                    \n";
        $stSql .= "   FROM tcmgo.divida_consolidada                             \n";
        $stSql .= "  WHERE dt_inicio >= '".$this->getDado('dt_inicio')."'       \n";
        $stSql .= "    AND dt_fim   <= '".$this->getDado('dt_fim')."'           \n";
        $stSql .= "    AND exercicio = '".$this->getDado('exercicio')."'        \n";

        return $stSql;
    }

    public function limpaDividas()
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;

        $stSql  = " DELETE                                                      \n";
        $stSql .= "   FROM tcmgo.divida_consolidada                             \n";
        $stSql .= "  WHERE dt_inicio >= '".$this->getDado('dt_inicio')."'       \n";
        $stSql .= "    AND dt_fim    <= '".$this->getDado('dt_fim')."'          \n";
        $stSql .= "    AND exercicio = '".$this->getDado('exercicio')."'        \n";

        $this->setDebug( $stSql );
        $obErro = $obConexao->executaDML( $stSql );

        return $obErro;
    }

}
