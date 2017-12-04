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

class TTCERNObraAcompanhamento extends Persistente
{

function TTCERNObraAcompanhamento()
{
    parent::Persistente();
    $this->setTabela('tcern.obra_acompanhamento');

    $this->setCampoCod('id');

    $this->AddCampo('id'                , 'integer', true, ''   , false, false);
    $this->AddCampo('obra_contrato_id'  , 'varchar', true, '50' , false, true);
    $this->AddCampo('dt_evento'         , 'date'   , true, ''   , false, true);
    $this->AddCampo('numcgm_responsavel', 'integer', true, ''   , false, false);
    $this->AddCampo('cod_situacao'      , 'integer', true, ''   , false, true);
    $this->AddCampo('justificativa'     , 'varchar', true, '255', false, true);
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
    $stSql .= "SELECT MAX(id) AS max_id FROM tcern.obra_acompanhamento";

    return $stSql;
}

function recuperaListaAcompanhamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaListaAcompanhamento().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaAcompanhamento()
{
    $stSql .= "    SELECT *
                          , TO_CHAR(obra_acompanhamento.dt_evento, 'dd/mm/yyyy') AS dt_evento
                          , obra_acompanhamento.id AS id
                     FROM tcern.obra_acompanhamento

               INNER JOIN tcern.obra_acompanhamento_situacao
                       ON obra_acompanhamento_situacao.cod_situacao = obra_acompanhamento.cod_situacao

               INNER JOIN tcern.obra_contrato
                       ON obra_contrato.id = obra_acompanhamento.obra_contrato_id

               INNER JOIN tcern.obra
                       ON obra.cod_entidade = obra_contrato.cod_entidade
                      AND obra.exercicio    = obra_contrato.exercicio
                      AND obra.num_obra     = obra_contrato.num_obra";

    return $stSql;
}

function recuperaSituacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaSituacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSituacao()
{
    $stSql .= "SELECT * FROM tcern.obra_acompanhamento_situacao";

    return $stSql;
}

}
