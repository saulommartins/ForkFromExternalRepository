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
    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCERNObraContrato extends Persistente
{

function TTCERNObraContrato()
{
    parent::Persistente();
    $this->setTabela('tcern.obra_contrato');

    $this->setCampoCod('num_contrato');

    $this->AddCampo('id'                        , 'integer', true, ''    , false, false);
    $this->AddCampo('cod_entidade'              , 'integer', true, ''    , false, false);
    $this->AddCampo('exercicio'                 , 'varchar', true, '4'   , false, true);
    $this->AddCampo('num_obra'                  , 'integer', true, ''    , false, true);
    $this->AddCampo('num_contrato'              , 'varchar', true, '50'  , false, false);
    $this->AddCampo('servico'                   , 'varchar', true, '255' , false, false);
    $this->AddCampo('processo_licitacao'        , 'varchar', true, '10'  , false, true);
    $this->AddCampo('numcgm'                    , 'integer', true, ''    , false, false);
    $this->AddCampo('valor_contrato'            , 'numeric', true, '14,2', false, false);
    $this->AddCampo('valor_executado_exercicio' , 'numeric', true, '14,2', false, false);
    $this->AddCampo('valor_a_exercutar'         , 'numeric', true, '14,2', false, false);
    $this->AddCampo('dt_inicio_contrato'        , 'date'   , true, ''    , false, false);
    $this->AddCampo('dt_termino_contrato'       , 'date'   , true, ''    , false, false);
    $this->AddCampo('num_art'                   , 'integer', true, ''    , false, false);
    $this->AddCampo('valor_iss'                 , 'numeric', true, '14,2', false, false);
    $this->AddCampo('num_dcms'                  , 'integer', true, ''    , false, false);
    $this->AddCampo('valor_inss'                , 'numeric', true, '14,2', false, false);
    $this->AddCampo('numcgm_fiscal'             , 'integer', true, ''    , false, false);
}

function recuperaMaxId(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaMaxId().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMaxId()
{
    $stSql .= "SELECT MAX(id) AS max_id FROM tcern.obra_contrato";

    return $stSql;
}

function recuperaListaContrato(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaListaContrato().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaContrato()
{
    $stSql .= "    SELECT *
                     FROM tcern.obra_contrato
               INNER JOIN tcern.obra
                       ON obra.cod_entidade = obra_contrato.cod_entidade
                      AND obra.exercicio    = obra_contrato.exercicio
                      AND obra.num_obra     = obra_contrato.num_obra";

    return $stSql;
}

}
