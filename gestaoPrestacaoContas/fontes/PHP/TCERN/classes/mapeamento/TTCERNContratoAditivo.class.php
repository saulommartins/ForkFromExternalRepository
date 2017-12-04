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
    * Extensão da Classe de mapeamento
    * Data de Criação: 2/02/2012

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCERNContratoAditivo extends Persistente
{

function TTCERNContratoAditivo()
{
    parent::Persistente();
    $this->setTabela('tcern.contrato_aditivo');

    $this->setCampoCod('num_contrato_aditivo');
    $this->setComplementoChave('num_convenio, cod_entidade, exercicio, exercicio_aditivo');

    $this->AddCampo('num_contrato_aditivo', 'integer', true, ''    , false, false);
    $this->AddCampo('exercicio_aditivo'   , 'varchar', true, '4'   , false, true);
    $this->AddCampo('num_convenio'        , 'integer', true, ''    , false, true);
    $this->AddCampo('cod_entidade'        , 'integer', true, ''    , false, false);
    $this->AddCampo('exercicio'           , 'varchar', true, '4'   , false, false);
    $this->AddCampo('cod_processo'        , 'integer', true, ''    , false, true);
    $this->AddCampo('exercicio_processo'  , 'varchar', true, '4'   , false, true);
    $this->AddCampo('bimestre'            , 'varchar', true, '50'  , false, false);
    $this->AddCampo('cod_objeto'          , 'integer', true, ''    , false, false);
    $this->AddCampo('valor_aditivo'       , 'numeric', true, '14,2', false, false);
    $this->AddCampo('dt_inicio_vigencia'  , 'date'   , true, ''    , false, false);
    $this->AddCampo('dt_termino_vigencia' , 'date'   , true, ''    , false, false);
    $this->AddCampo('dt_assinatura'       , 'date'   , true, ''    , false, false);
    $this->AddCampo('dt_publicacao'       , 'date'   , true, ''    , false, false);
}

function recuperaContratoAditivo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaContratoAditivo().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratoAditivo()
{
    $stSql .= " SELECT *
                       , TO_CHAR(contrato_aditivo.dt_inicio_vigencia, 'dd/mm/yyyy') AS dt_inicio_vigencia   \n
                       , TO_CHAR(contrato_aditivo.dt_termino_vigencia, 'dd/mm/yyyy') AS dt_termino_vigencia \n
                       , TO_CHAR(contrato_aditivo.dt_assinatura, 'dd/mm/yyyy') AS dt_assinatura             \n
                       , TO_CHAR(contrato_aditivo.dt_publicacao, 'dd/mm/yyyy') AS dt_publicacao             \n
                       , contrato_aditivo.bimestre AS bimestre                                              \n
                       , contrato_aditivo.cod_processo AS cod_processo                                      \n
                       , contrato_aditivo.exercicio_processo AS exercicio_processo                          \n
                       , contrato_aditivo.cod_objeto AS cod_objeto                                          \n
                  FROM tcern.contrato_aditivo                                                               \n
            INNER JOIN tcern.convenio                                                                       \n
                    ON convenio.num_convenio = contrato_aditivo.num_convenio                                \n
                   AND convenio.cod_entidade = contrato_aditivo.cod_entidade                                \n
                   AND convenio.exercicio    = contrato_aditivo.exercicio                                   \n
                 WHERE contrato_aditivo.num_contrato_aditivo = ".$this->getDado('num_contrato_aditivo')."   \n
                   AND contrato_aditivo.exercicio_aditivo    = '".$this->getDado('exercicio_aditivo')."'    \n
                   AND contrato_aditivo.exercicio            = '".$this->getDado('exercicio')."'            \n
                   AND contrato_aditivo.cod_entidade         = ".$this->getDado('cod_entidade')."           \n
                   AND contrato_aditivo.num_convenio         = ".$this->getDado('num_convenio')."           \n
            ";

    return $stSql;
}

function recuperaAditivo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaAditivo().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAditivo()
{
    $stSql .= " SELECT *
                       , TO_CHAR(contrato_aditivo.dt_inicio_vigencia, 'dd/mm/yyyy') AS dt_inicio_vigencia   \n
                       , TO_CHAR(contrato_aditivo.dt_termino_vigencia, 'dd/mm/yyyy') AS dt_termino_vigencia \n
                       , TO_CHAR(contrato_aditivo.dt_assinatura, 'dd/mm/yyyy') AS dt_assinatura             \n
                       , TO_CHAR(contrato_aditivo.dt_publicacao, 'dd/mm/yyyy') AS dt_publicacao             \n
                       , contrato_aditivo.bimestre AS bimestre                                              \n
                       , contrato_aditivo.cod_processo AS cod_processo                                      \n
                       , contrato_aditivo.exercicio_processo AS exercicio_processo                          \n
                  FROM tcern.contrato_aditivo                                                                \n
            INNER JOIN tcern.convenio                                                                        \n
                    ON convenio.num_convenio = contrato_aditivo.num_convenio                                 \n
                   AND convenio.cod_entidade = contrato_aditivo.cod_entidade                                 \n
                   AND convenio.exercicio    = contrato_aditivo.exercicio                                    \n
            ";

    return $stSql;
}

}
